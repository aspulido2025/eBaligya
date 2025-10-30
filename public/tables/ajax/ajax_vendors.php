<?php
require __DIR__ . '/../../../config/init.php';
session_start();

$timeStart = microtime(true);

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
$orderColumnIndex = $_GET['order'][0]['column'] ?? 6; // default 'created'
$orderDir = $_GET['order'][0]['dir'] ?? 'desc';

// map columns
$columns = [
  'system_vendors.id',
  'system_vendors.fullname',
  'system_vendors.vendor_code',
  'system_vendors.area_id',
  'system_vendors.vendor_type_id',
  'system_vendors.status_id',
  'system_vendors.created'
];
$orderCol = $columns[$orderColumnIndex] ?? 'system_vendors.created';


// base query
$baseSql = "FROM system_vendors 
            LEFT JOIN system_users ON system_users.id = system_vendors.created_id";

$where  = " WHERE DATE(system_vendors.created) BETWEEN ? AND ?";
$params = [$dateMin, $dateMax];

if ($search !== '') {
    $where .= " AND ( 
                system_vendors.fullname LIKE ? 
               OR system_vendors.vendor_code LIKE ?
               OR system_vendors.area_id LIKE ?
               OR system_vendors.vendor_type_id LIKE ?
               OR system_vendors.status_id LIKE ?
            )";
    $like = "%$search%";
    array_push($params, $like, $like, $like, $like, $like);
}

// total count
$totalRecords = $db->fetch("SELECT COUNT(*) AS cnt $baseSql", [], [], 'row')['cnt'];

// filtered count
$totalFiltered = $db->fetch("SELECT COUNT(*) AS cnt $baseSql $where", $params, [], 'row')['cnt'];

// actual data
$sql = "SELECT system_vendors.*,
            system_users.fullname AS encoder
        $baseSql 
        $where 
        ORDER BY $orderCol $orderDir 
        LIMIT $start, $length";
$dataSet = $db->fetch($sql, $params, [], 'all');

file_put_contents(__DIR__ . '/debug_dt.txt', print_r($sql, true));

$rows = [];
foreach ($dataSet as $row) {

    $rows[] = [
        "id"            => $row['id'],
        "fullname"      => $row['fullname'],
        "vendor_code"   => $row['vendor_code'],
        "business_name" => $row['business_name'],
        "area"          => $lookup->getDescription("AREA ID", $row['area_id']),
        "vtype"         => $lookup->getDescription("VENDOR TYPE", $row['vendor_type_id']),
        "status"        => $lookup->getDescription("VENDOR STATE", $row['status_id']), 
        "created"       => $row['created'],
        "encoder"       => $row['encoder']
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