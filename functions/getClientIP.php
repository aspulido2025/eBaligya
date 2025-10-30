<?php 
    // Cloudflare: uses HTTP_CF_CONNECTING_IP (Cloudflare always sets this to the real client IP).
    // Reverse Proxies / Load Balancers: checks X-Forwarded-For (first valid public IP).
    // Nginx / HAProxy: checks X-Real-IP.
    // Fallbacks: uses HTTP_CLIENT_IP or REMOTE_ADDR.
    // Normalization: converts ::1 to 127.0.0.1 for consistency.

    function getClientIP(): string {
        $ip = null;

        if (isset($_SERVER['HTTP_CF_CONNECTING_IP'])) {
            $ip = $_SERVER['HTTP_CF_CONNECTING_IP'];
        } elseif (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ipList = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
            foreach ($ipList as $possibleIp) {
                $possibleIp = trim($possibleIp);
                if (filter_var(
                    $possibleIp,
                    FILTER_VALIDATE_IP,
                    FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE
                )) {
                    $ip = $possibleIp;
                    break;
                }
            }
        } elseif (isset($_SERVER['HTTP_X_REAL_IP'])) {
            $ip = $_SERVER['HTTP_X_REAL_IP'];
        } elseif (isset($_SERVER['HTTP_CLIENT_IP'])) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (isset($_SERVER['REMOTE_ADDR'])) {
            $ip = $_SERVER['REMOTE_ADDR'];
        }

        if ($ip === '::1') {
            $ip = '127.0.0.1'; // Normalize IPv6 localhost
        }

        return $ip ?? 'UNKNOWN';
    }