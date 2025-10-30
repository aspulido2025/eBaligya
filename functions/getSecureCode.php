<?php
    /**
     * Generate a secure random code.
     *
     * @param int $length    Number of characters to return (final string length).
     * @param string|null $charset  Custom character set (default = digits + uppercase letters).
     *                              Pass "hex" to use bin2hex(random_bytes()).
     *                              Pass "numeric" for digits only.
     *
     * @return string
     */
    function getSecureCode(int $length = 8, ?string $charset = null): string {
        // Special case: HEX (safe for tokens, validation links)
        if ($charset === 'hex') {
            // bin2hex doubles the length, so request half the bytes
            return bin2hex(random_bytes((int) ceil($length / 2)));
        }

        // Default charsets
        switch ($charset) {
            case 'numeric':
                $chars = '0123456789';
                break;
            case null:
            default:
                $chars = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        }

        $charLen = strlen($chars);
        $code    = '';

        for ($i = 0; $i < $length; $i++) {
            $randomIndex = random_int(0, $charLen - 1);
            $code .= $chars[$randomIndex];
        }

        return $code;
    }


    // 1. OTP (6 digits)
    // $otp = getSecureCode(6, 'numeric'); 
    // e.g. "482193"

    // 2. Alphanumeric session code (12 chars)
    // $sessionCode = getSecureCode(12); 
    // e.g. "7H2X9QK3LM5N"

    // 3. Email validation token (64 hex chars → 32 random bytes)
    // $token = getSecureCode(64, 'hex');
    // e.g. "a3f7c2d9e6..."

    // 4. Custom alphabet (only lowercase, just as an example)
    // $custom = getSecureCode(10, 'abcdef');
    // e.g. "cfedabdeac"
