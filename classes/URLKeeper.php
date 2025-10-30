<?php
namespace App\Classes;

class URLKeeper {
    // Encode (string or array) with HMAC, AES, TTL
    public static function encode(string|array $data, string $key, int $ttl_seconds = 0): string {
        $iv = random_bytes(16);
        $timestamp = time();

        $payload = [
            '_ts'   => $ttl_seconds > 0 ? $timestamp : null,
            '_data' => $data
        ];

        $ciphertext = openssl_encrypt(json_encode($payload), 'AES-256-CBC', $key, OPENSSL_RAW_DATA, $iv);
        if ($ciphertext === false) {
            die("Encryption failed");
        }

        $hmac = hash_hmac('sha256', $iv . $ciphertext, $key, true);
        return base64_encode($iv . $hmac . $ciphertext);
    }

    // Decode a param from GET/POST
    public static function decode(string $param_name, string $key, int $ttl_seconds = 0): string|array {
        $encoded = $_POST[$param_name] ?? $_GET[$param_name] ?? null;
        if ($encoded === null) {
            self::spoofDetected('MISSING PARAMETERS', $key);
        }

        $raw = base64_decode($encoded, true);
        if ($raw === false || strlen($raw) < 48) {
            self::spoofDetected('INVALID FORMAT', $key);
        }

        $iv         = substr($raw, 0, 16);
        $hmac       = substr($raw, 16, 32);
        $ciphertext = substr($raw, 48);

        $calc_hmac = hash_hmac('sha256', $iv . $ciphertext, $key, true);
        if (!hash_equals($hmac, $calc_hmac)) {
            self::spoofDetected('HMAC MISMATCH', $key);
        }

        $decrypted = openssl_decrypt($ciphertext, 'AES-256-CBC', $key, OPENSSL_RAW_DATA, $iv);
        if ($decrypted === false) {
            self::spoofDetected('FAILED DECRYPTION', $key);
        }

        $payload = json_decode($decrypted, true);
        if (!is_array($payload) || !array_key_exists('_data', $payload)) {
            self::spoofDetected('INVALID PAYLOAD', $key);
        }

        if ($ttl_seconds > 0) {
            if (!isset($payload['_ts']) || (time() - (int)$payload['_ts']) > $ttl_seconds) {
                // self::expiredLink();
                self::spoofDetected('EXPIRED LINK', $key);
            }
        }

        return $payload['_data'];
    }

    private static function spoofDetected(string $source, string $key): never {
        if (!ob_get_level()) ob_start();
        while (ob_get_level()) ob_end_clean();
        
        //
        // THIS IS WHERE WE LOG THE TRANSACTION => actually moved to auth_guard.php
        //

        
        $_SESSION['auth_guard'] = $source."||Please log in again.||Login";
        $header_link = BASE_URL . '/auth/auth_guard.php';
        header("Location: " . $header_link);
        exit;
                        
    }

    // private static function expiredLink(): never {
    //     if (!ob_get_level()) ob_start();
    //     while (ob_get_level()) ob_end_clean();

    //     header($_SERVER['SERVER_PROTOCOL'] . ' 302 Found');
    //     header("Location: ../system_authentication/index_session_expiredlink.php");
    //     exit;
    // }
}


//===================================================================================================
// Single Value (e.g., pass a code in a link)
// // Encode
// $token = Bouncer::encode('c', $cfg_encryption_passkey, $cfg_encryption_expiry);

// // Use in a link (with urlencode!)
// echo '<a href="page.php?x=' . urlencode($token) . '">Click</a>';
// Then in page.php:
// $value = Bouncer::decode('x', $cfg_encryption_passkey, $cfg_encryption_expiry);
// // $value will be 'c'

// Multiple Values (e.g., pass structured data securely)
// // Prepare an array
// $params = [
//     'user_id'       => $row['user_id'],
//     'generatedCode' => $varGeneratedCode,
//     'ip_address'    => getUserIP()
// ];

// // Encode
// $token = Bouncer::encode($params, $cfg_encryption_passkey, $cfg_encryption_expiry);

// // Use in URL
// echo '<a href="verify.php?t=' . urlencode($token) . '">Verify</a>';
// Then in verify.php:
// $data = Bouncer::decode('t', $cfg_encryption_passkey, $cfg_encryption_expiry);

// // You now have back your original array
// $user_id       = $data['user_id'];
// $generatedCode = $data['generatedCode'];
// $ip_address    = $data['ip_address'];
    