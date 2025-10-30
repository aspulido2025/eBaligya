<?php
require __DIR__ . '/../../../config/init.php';
session_start();

$timeStart = microtime(true);

use App\Classes\DB;
$db = new DB($pdo);

// --- Date range from RBAC session ---
$dateMin = $_SESSION['rbac']['date_min'] ?? date('Y-m-01');
$dateMax = $_SESSION['rbac']['date_max'] ?? date('Y-m-d');

// DataTables request params
$draw   = $_GET['draw'] ?? 1;
$start  = (int)($_GET['start'] ?? 0);
$length = (int)($_GET['length'] ?? 25);
$search = $_GET['search']['value'] ?? '';
$orderColumnIndex = $_GET['order'][0]['column'] ?? 5; // default 'created'
$orderDir = $_GET['order'][0]['dir'] ?? 'desc';

// file_put_contents(__DIR__ . '/debug_dt.txt', print_r($_POST, true));
// file_put_contents(__DIR__ . '/debug_dt.txt', print_r($_GET, true));

// map columns
$columns = [
  'system_log_index.id',
  'system_log_index.log_type',
  'system_log_index.ref_id',
  'system_users.fullname',
  'system_log_index.ipaddress',
  'system_log_index.created'
];
$orderCol = $columns[$orderColumnIndex] ?? 'system_log_index.created';

// base query
$baseSql = "FROM system_log_index 
            LEFT JOIN system_users ON system_users.id = system_log_index.user_id";

$where  = " WHERE DATE(system_log_index.created) BETWEEN ? AND ?";
$params = [$dateMin, $dateMax];

if ($search !== '') {
    $where .= " AND ( 
        system_log_index.log_type   LIKE ? 
        OR system_log_index.ipaddress LIKE ?
        OR system_users.fullname    LIKE ?
        OR system_log_index.user_agent LIKE ?
        OR system_log_index.ref_id  LIKE ?
    )";

    $like = "%$search%";
    array_push($params, $like, $like, $like, $like, $like);
}



// total count
$totalRecords = $db->fetch("SELECT COUNT(*) AS cnt $baseSql", [], [], 'row')['cnt'];

// filtered count
$totalFiltered = $db->fetch("SELECT COUNT(*) AS cnt $baseSql $where", $params, [], 'row')['cnt'];

// actual data
$sql = "SELECT system_log_index.*,
            system_users.fullname AS username
        $baseSql 
        $where 
        ORDER BY $orderCol $orderDir 
        LIMIT $start, $length";
$dataSet = $db->fetch($sql, $params, [], 'all');

// file_put_contents(__DIR__ . '/debug_dt.txt', print_r($sql, true));

$rows = [];
foreach ($dataSet as $row) {

    $rows[] = [
        "id"            => $row['id'],
        "log_type"      => $row['log_type'],
        "ref_id"        => $row['ref_id'],
        "user_id"       => $row['user_id'],
        "session_id"    => $row['session_id'],
        "ipaddress"     => $row['ipaddress'],
        "user_agent"    => $row['user_agent'] ,
        "created"       => $row['created'],
        "username"      => $row['username']
    ];
}

$timeEnd = microtime(true);
$sqlTime = round(($timeEnd - $timeStart) * 1000, 2); // ms

// ---- Performance metrics ----
$memoryUsage = round(memory_get_usage(true) / 1048576, 2); // MB

$opcache = function_exists('opcache_get_status') ? opcache_get_status(false) : null;
$opcacheHits = $opcache['opcache_statistics']['hits'] ?? 0;
$opcacheMisses = $opcache['opcache_statistics']['misses'] ?? 0;
$totalOps = max(1, $opcacheHits + $opcacheMisses);
$opcacheHitRatio = round(($opcacheHits / $totalOps) * 100, 2);

// return JSON
$response = [
    "draw" => (int)$draw,
    "recordsTotal" => (int)$totalRecords,
    "recordsFiltered" => (int)$totalFiltered,
    "data" => $rows,
    "debug" => [
        "execution_ms" => $sqlTime,
        "memory_mb"      => $memoryUsage,
        "opcache_hit%"   => $opcacheHitRatio,
        "returned_rows" => count($dataSet),
        "dataset" => $rows,
        "query" => $sql,
        "params" => $params]
];

header('Content-Type: application/json; charset=utf-8');
echo json_encode($response, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);