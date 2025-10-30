<?php
require __DIR__ . '/../../../config/init.php';
session_start();



use App\Classes\DB;
use App\Classes\UniversalLookup;
$db = new DB($pdo);
$lookup = new UniversalLookup($pdo);

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

// map columns
$columns = [
  'log_cookie.id',
  'system_users.fullname',
  'log_cookie.selector',
  'log_cookie.expires_at',
  'log_cookie.user_agent',
  'log_cookie.created_at'
];
$orderCol = $columns[$orderColumnIndex] ?? 'log_cookie.created_at';


$timeStart = microtime(true);
// base query
$baseSql = "FROM log_cookie 
            LEFT JOIN system_users ON system_users.id = log_cookie.user_id";

$where  = " WHERE DATE(log_cookie.created_at) BETWEEN ? AND ?";
$params = [$dateMin, $dateMax];

if ($search !== '') {
    $where .= " AND (  
                system_users.fullname LIKE ?
               OR log_cookie.selector LIKE ?
               OR log_cookie.expires_at LIKE ?
               OR log_cookie.ipaddress LIKE ?
               OR log_cookie.user_agent LIKE ?
               OR log_cookie.created_at LIKE ?
               )";
    $like = "%$search%";
    array_push($params, $like, $like, $like, $like, $like, $like);
}

// total count
$totalRecords = $db->fetch("SELECT COUNT(*) AS cnt $baseSql", [], [], 'row')['cnt'];

// filtered count
$totalFiltered = $db->fetch("SELECT COUNT(*) AS cnt $baseSql $where", $params, [], 'row')['cnt'];

// actual data
$sql = "SELECT log_cookie.*,
            system_users.fullname 
        $baseSql 
        $where 
        ORDER BY $orderCol $orderDir 
        LIMIT $start, $length";
$dataSet = $db->fetch($sql, $params, [], 'all');

file_put_contents(__DIR__ . '/debug_dt.txt', print_r($dataSet, true));

$rows = [];
foreach ($dataSet as $row) {

    $rows[] = [
        "id"            => $row['id'],
        "fullname"      => $row['fullname'],
        "selector"      => $row['selector'],
        "expires_at"    => $row['expires_at'],
        "ipaddress"     => $row['ipaddress'],
        "user_agent"    => $row['user_agent'],
        "created_at"    => $row['created_at']
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