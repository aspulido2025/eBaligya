<?php
/**
 * Array Handling Exercise
 * Run: http://localhost/projectFramework/public/test/array_exercise.php
 */

// =====================
// Sample multi-dimensional array
// =====================
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

// Helper function for pretty print
function dump($label, $value) {
    echo "<h3>$label</h3><pre>" . print_r($value, true) . "</pre>";
}

// =====================
// 1. Filter transactions for user_id = 2
// =====================
$user2Tx = array_filter($transactions, fn($tx) => $tx['user_id'] === 2);
dump("1. Transactions for user_id=2", $user2Tx);

// =====================
// 2. Extract all action names
// =====================
$actions = array_column($transactions, 'action');
dump("2. Action names", $actions);

// =====================
// 3. Group transactions by user_id
// =====================
$grouped = [];
foreach ($transactions as $tx) {
    $grouped[$tx['user_id']][] = $tx;
}
dump("3. Grouped by user_id", $grouped);

// =====================
// 4. Map into simplified log lines
// =====================
$logLines = array_map(function ($tx) {
    return "#{$tx['id']} {$tx['action']} @ {$tx['created_at']}";
}, $transactions);
dump("4. Simplified log lines", $logLines);

// =====================
// 5. Find the first 'update_profile'
// =====================
$profileUpdate = current(array_filter($transactions, fn($tx) => $tx['action'] === 'update_profile'));
dump("5. First update_profile transaction", $profileUpdate);

// =====================
// 6. Flatten diffs before/after
// =====================
$changes = [];
foreach ($transactions as $tx) {
    if (!empty($tx['details']['before']) && !empty($tx['details']['after'])) {
        foreach ($tx['details']['after'] as $field => $newValue) {
            $oldValue = $tx['details']['before'][$field] ?? '(none)';
            if ($oldValue !== $newValue) {
                $changes[] = "TX#{$tx['id']} {$field}: '$oldValue' → '$newValue'";
            }
        }
    }
}
dump("6. Field-level changes", $changes);

// =====================
// 7. Sort by created_at DESC
// =====================
$sorted = $transactions;
usort($sorted, fn($a, $b) => strcmp($b['created_at'], $a['created_at']));
dump("7. Sorted DESC by created_at", $sorted);

// =====================
// 8. Destructuring in foreach
// =====================
$lines = [];
foreach ($transactions as ['id' => $id, 'action' => $action, 'details' => ['ip' => $ip]]) {
    $lines[] = "TX#$id | $action | from IP $ip";
}
dump("8. Destructuring output", $lines);

// =====================
// 9. Build SQL dynamically
// =====================
$update = ['fullname' => 'Jane D.', 'email' => 'jane@example.com'];
$fields = [];
$params = [];
foreach ($update as $col => $val) {
    $fields[] = "$col=?";
    $params[] = $val;
}
$sql = "UPDATE system_users SET " . implode(", ", $fields) . " WHERE id=?";
$params[] = 2;
dump("9. SQL Builder", ['sql' => $sql, 'params' => $params]);

// =====================
// 🎯 Challenge Section
// =====================

// Count actions per user
$counts = [];
foreach ($transactions as $tx) {
    $counts[$tx['user_id']] = ($counts[$tx['user_id']] ?? 0) + 1;
}
dump("Challenge 1: Actions per user", $counts);

// Most common action
$actionCounts = array_count_values($actions);
arsort($actionCounts);
$mostCommon = key($actionCounts);
dump("Challenge 2: Most common action", $mostCommon);

// Build CSV
$csvLines = ["id,user_id,action,ip,created_at"];
foreach ($transactions as $tx) {
    $csvLines[] = implode(",", [
        $tx['id'],
        $tx['user_id'],
        $tx['action'],
        $tx['details']['ip'],
        $tx['created_at']
    ]);
}
$csv = implode("\n", $csvLines);
dump("Challenge 3: CSV Export", $csv);
