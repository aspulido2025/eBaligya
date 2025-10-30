<?php
/**
 * Array Handling Exercise 2
 * Procedural vs Array Functions
 * Run: http://localhost/projectFramework/public/test/array_exercise2.php
 */

$transactions = [
    [
        'id' => 1,
        'user_id' => 2,
        'action' => 'update_profile',
        'details' => [
            'before' => ['fullname' => 'John Doe', 'email' => 'john@example.com'],
            'after'  => ['fullname' => 'John D.', 'email' => 'john.d@example.com'],
            'ip'     => '192.168.1.10'
        ],
        'created_at' => '2025-09-29 10:00:00'
    ],
    [
        'id' => 2,
        'user_id' => 2,
        'action' => 'change_password',
        'details' => [
            'before' => [],
            'after'  => [],
            'ip'     => '192.168.1.10'
        ],
        'created_at' => '2025-09-29 11:00:00'
    ],
    [
        'id' => 3,
        'user_id' => 3,
        'action' => 'login',
        'details' => [
            'before' => [],
            'after'  => [],
            'ip'     => '192.168.1.50'
        ],
        'created_at' => '2025-09-29 11:30:00'
    ],
];

// Helper
function dump($label, $value) {
    echo "<h3>$label</h3><pre>" . print_r($value, true) . "</pre>";
}

/* ============================================================
   1. Filter transactions for user_id=2
   ============================================================ */
// Procedural
$user2Tx_loop = [];
foreach ($transactions as $tx) {
    if ($tx['user_id'] === 2) {
        $user2Tx_loop[] = $tx;
    }
}

// Array function
$user2Tx_func = array_filter($transactions, fn($tx) => $tx['user_id'] === 2);

dump("1. Procedural filter", $user2Tx_loop);
dump("1. Array function filter", $user2Tx_func);

/* ============================================================
   2. Extract all action names
   ============================================================ */
// Procedural
$actions_loop = [];
foreach ($transactions as $tx) {
    $actions_loop[] = $tx['action'];
}

// Array function
$actions_func = array_column($transactions, 'action');

dump("2. Procedural action list", $actions_loop);
dump("2. Array function action list", $actions_func);

/* ============================================================
   3. Group by user_id
   ============================================================ */
// Procedural
$grouped_loop = [];
foreach ($transactions as $tx) {
    $grouped_loop[$tx['user_id']][] = $tx;
}

// Array function way (using reduce)
$grouped_func = array_reduce($transactions, function($carry, $tx) {
    $carry[$tx['user_id']][] = $tx;
    return $carry;
}, []);

dump("3. Procedural grouped", $grouped_loop);
dump("3. Array function grouped", $grouped_func);

/* ============================================================
   4. Map into simplified log lines
   ============================================================ */
// Procedural
$log_loop = [];
foreach ($transactions as $tx) {
    $log_loop[] = "#{$tx['id']} {$tx['action']} @ {$tx['created_at']}";
}

// Array function
$log_func = array_map(fn($tx) => "#{$tx['id']} {$tx['action']} @ {$tx['created_at']}", $transactions);

dump("4. Procedural log lines", $log_loop);
dump("4. Array function log lines", $log_func);

/* ============================================================
   5. Sort by created_at DESC
   ============================================================ */
// Procedural
$sorted_loop = $transactions;
usort($sorted_loop, function ($a, $b) {
    if ($a['created_at'] == $b['created_at']) return 0;
    return ($a['created_at'] > $b['created_at']) ? -1 : 1;
});

// Array function (shorter)
$sorted_func = $transactions;
usort($sorted_func, fn($a, $b) => strcmp($b['created_at'], $a['created_at']));

dump("5. Procedural sort", $sorted_loop);
dump("5. Array function sort", $sorted_func);

/* ============================================================
   6. Count actions per user
   ============================================================ */
// Procedural
$counts_loop = [];
foreach ($transactions as $tx) {
    $uid = $tx['user_id'];
    if (!isset($counts_loop[$uid])) {
        $counts_loop[$uid] = 0;
    }
    $counts_loop[$uid]++;
}

// Array function
$counts_func = array_count_values(array_column($transactions, 'user_id'));

dump("6. Procedural counts", $counts_loop);
dump("6. Array function counts", $counts_func);

/* ============================================================
   7. Build CSV
   ============================================================ */
// Procedural
$csv_loop = "id,user_id,action,ip,created_at\n";
foreach ($transactions as $tx) {
    $csv_loop .= "{$tx['id']},{$tx['user_id']},{$tx['action']},{$tx['details']['ip']},{$tx['created_at']}\n";
}

// Array function
$csv_func = implode("\n", array_merge(
    ["id,user_id,action,ip,created_at"],
    array_map(fn($tx) => implode(",", [
        $tx['id'], $tx['user_id'], $tx['action'], $tx['details']['ip'], $tx['created_at']
    ]), $transactions)
));

dump("7. Procedural CSV", $csv_loop);
dump("7. Array function CSV", $csv_func);


/* ============================================================
   8. Build UPDATE query
   ============================================================ */

$updateData = [
    'fullname' => 'Jane D.',
    'email'    => 'jane@example.com',
    'birthday' => '1970-11-08'
];
$userId = 2;

// ------------------------------------------------------------
// Procedural way
// ------------------------------------------------------------
$fields_loop = [];
$params_loop = [];

foreach ($updateData as $col => $val) {
    $fields_loop[] = "$col=?";
    $params_loop[] = $val;
}
$sql_loop = "UPDATE system_users SET " . implode(", ", $fields_loop) . " WHERE id=?";
$params_loop[] = $userId;

dump("8. Procedural SQL", ['sql' => $sql_loop, 'params' => $params_loop]);

// ------------------------------------------------------------
// Array-function way
// ------------------------------------------------------------
$sql_func = "UPDATE system_users SET " .
    implode(", ", array_map(fn($col) => "$col=?", array_keys($updateData))) .
    " WHERE id=?";
$params_func = array_values($updateData);
$params_func[] = $userId;

dump("8. Array function SQL", ['sql' => $sql_func, 'params' => $params_func]);
