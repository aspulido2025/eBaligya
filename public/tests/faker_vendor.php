<?php
require __DIR__ . '/vendor/autoload.php';

use Faker\Factory;

// Connect to your DB
$pdo = new PDO("mysql:host=127.0.0.1;dbname=projectframework;charset=utf8mb4", "root", "");

$faker = Factory::create();
$secret = 'dev-secret-key'; // your QR HMAC secret

for ($i = 0; $i < 10000; $i++) {

    // 1️⃣ Generate readable grouped vendor code
    $chars = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';
    $code = '';
    for ($x = 0; $x < 16; $x++) {
        $code .= $chars[random_int(0, strlen($chars) - 1)];
    }
    $vendor_code = implode('-', str_split($code, 4));

    // 2️⃣ Build vendor names and business names
    $fullname = $faker->name();
    $business_name = $faker->company();

    // 3️⃣ Create timestamp + HMAC hash
    $timestamp = time();
    $payload = "{$vendor_code}|{$timestamp}";
    $hash = hash_hmac('sha256', $payload, $secret);

    // 4️⃣ Assemble encoded QR token (JSON → base64)
    $qr_data = base64_encode(json_encode([
        'code' => $vendor_code,
        'ts'   => $timestamp,
        'sig'  => $hash
    ]));

    // 5️⃣ Status and area randomization
    $statusOptions = [1, 0, 9];
    $status = $faker->randomElement($statusOptions);
    $area_id = $faker->numberBetween(1, 100);

    // 6️⃣ Final timestamps
    $created_at = $faker->dateTimeBetween('-1 years', 'now')->format('Y-m-d H:i:s');
    $updated_at = $faker->dateTimeBetween($created_at, 'now')->format('Y-m-d H:i:s');

    // 7️⃣ Push into batch array
    $values[] = [
        $vendor_code,       // vendor_code
        $fullname,          // fullname
        $business_name,     // business_name
        $area_id,           // area_id
        $status,            // status
        $qr_data,           // qr_token
        $hash,              // qr_hash
        $created_at,        // created_at
        $updated_at         // updated_at
    ];

 
}


