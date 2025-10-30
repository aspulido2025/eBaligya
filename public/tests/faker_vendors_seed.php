<?php
/**
 * vendors_table_faker_seed.php
 * ---------------------------------------------
 * Generates fake vendor data for development and testing.
 * Matches latest "vendors" table structure.
 */

ini_set('max_execution_time', 0);
ini_set('memory_limit', '512M');



    require_once __DIR__ . '/../../config/init.php';
    
    // require_once __DIR__ . '/../../config/init.php';
    // use App\Classes\DB;
    // $db = new DB($pdo);
use PDO;

$pdo = new PDO('mysql:host=localhost;dbname=projectframework;charset=utf8mb4', 'root', '');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$secret = 'dev-secret-key'; // use env in production
$total = 50; // how many vendors to generate

function generateVendorCode(): string {
    $chars = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789'; // exclude O,0,I,1
    $code = '';
    for ($i = 0; $i < 16; $i++) {
        $code .= $chars[random_int(0, strlen($chars) - 1)];
    }
    return implode('-', str_split($code, 4)); // e.g. X2KJ-L9T8-4FQM-H3ZN
}

function randomName(): string {
    $first = ['Juan', 'Maria', 'Jose', 'Ana', 'Pedro', 'Luisa', 'Carlo', 'Veronica', 'Miguel', 'Isabel'];
    $last  = ['Dela Cruz', 'Reyes', 'Santos', 'Garcia', 'Lopez', 'Domingo', 'Ramos', 'Aquino', 'Bautista', 'Fernandez'];
    return $first[array_rand($first)] . ' ' . $last[array_rand($last)];
}

function randomTikTok($name): string {
    $handle = strtolower(str_replace(' ', '', $name));
    return '@' . $handle . rand(10, 99);
}

for ($i = 0; $i < $total; $i++) {
    $vendor_code = generateVendorCode();
    $fullname = randomName();
    $tiktok_username = randomTikTok($fullname);
    $area_id = rand(1, 5);
    $status = ['active', 'inactive', 'revoked'][rand(0, 8) > 1 ? 0 : rand(1, 2)]; // 80% active

    $timestamp = time();
    $payload = "{$vendor_code}|{$timestamp}";
    $hash = hash_hmac('sha256', $payload, $secret);
    $qr_data = base64_encode(json_encode([
        'code' => $vendor_code,
        'ts'   => $timestamp,
        'sig'  => $hash
    ]));

    $created = date('Y-m-d H:i:s', strtotime('-' . rand(0, 30) . ' days'));
    $updated = date('Y-m-d H:i:s', strtotime($created . ' + ' . rand(0, 10) . ' days'));

    $stmt = $pdo->prepare("
        INSERT INTO vendors 
        (vendor_code, fullname, tiktok_username, area_id, status, qr_token, qr_hash, created_at, updated_at)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");
    $stmt->execute([
        $vendor_code,
        $fullname,
        $tiktok_username,
        $area_id,
        $status,
        $qr_data,
        $hash,
        $created,
        $updated
    ]);

    echo "✅ Inserted vendor: {$fullname} ({$vendor_code})\n";
}

echo "\nDone! Seeded {$total} vendors.\n";
