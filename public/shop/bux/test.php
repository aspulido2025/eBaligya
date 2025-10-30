<?php
/**
 * BUX.PH Checkout API Test (Live)
 * ------------------------------------------------------------
 * Sends a live checkout request to BUX API with callback URLs.
 * Prints JSON result and logs to bux_test.log
 */

// === 1. CONFIG ===
$BUX_API_URL = 'https://api.bux.ph/v1/api/sandbox/open/checkout/'; 
$BUX_API_KEY = 'af32734099f8ed4e58f1b0d27293942e';       

// === 2. CALLBACK URLs ===
// Replace with your actual domain
$notification_url = 'https://ebaligya.digitalassetsph.com/public/shop/bux/bux-notify.php';
$redirect_url     = 'https://ebaligya.digitalassetsph.com/public/shop/bux/bux-return.php';

// === 3. SAMPLE PAYLOAD ===
$payload = [
    "req_id" => '1',
    "client_id" => '0000018138',
    "amount" => '123.45', 
    "description" => "ASPulido Test Payment",
    "email" => "ariesoripuli@yahoo.com.ph",
    "contact" => "09919057797",
    "name" => "Ariel Pulido",
    "notification_url" => $notification_url,
    "redirect_url" => $redirect_url
];

// === 4. CURL REQUEST ===
$ch = curl_init($BUX_API_URL);
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST => true,
    CURLOPT_HTTPHEADER => [
        'Content-Type: application/json',
        'x-api-key: ' . $BUX_API_KEY
    ],
    CURLOPT_POSTFIELDS => json_encode($payload)
]);


$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$err = curl_error($ch);
curl_close($ch);


// ====================

// === 5. HANDLE RESPONSE ===
if ($err) {
    die("Curl Error: " . htmlspecialchars($err));
}

$data = json_decode($response, true);
$checkoutUrl = $data['checkout_url'] ?? ($data['response']['checkout_url'] ?? null);

// === 6. REDIRECT ON SUCCESS ===
if ($httpCode === 200 && !empty($checkoutUrl)) {
    // Optional: log to file
    file_put_contents(__DIR__ . '/bux_test.log', date('Y-m-d H:i:s') . " | Redirect to $checkoutUrl\n", FILE_APPEND);

    // Redirect browser to BUX checkout
    header("Location: " . $checkoutUrl);
    exit;
}

// === 7. FALLBACK (show raw output if error) ===
header('Content-Type: application/json');
echo json_encode([
    'status' => 'fail',
    'http_code' => $httpCode,
    'response' => $data
], JSON_PRETTY_PRINT);


// ====================

// // === 5. LOGGING ===
// $logData = date('Y-m-d H:i:s') . " | HTTP $httpCode\n" . $response . "\n\n";
// file_put_contents(__DIR__ . '/bux_test.log', $logData, FILE_APPEND);

// // === 6. OUTPUT ===
// header('Content-Type: application/json');

// if ($err) {
//     echo json_encode(['status' => 'error', 'message' => $err], JSON_PRETTY_PRINT);
//     exit;
// }

// if ($httpCode !== 200) {
//     echo json_encode([
//         'status' => 'fail',
//         'http_code' => $httpCode,
//         'response' => json_decode($response, true)
//     ], JSON_PRETTY_PRINT);
//     exit;
// }

// echo json_encode([
//     'status' => 'success',
//     'response' => json_decode($response, true)
// ], JSON_PRETTY_PRINT);
