<?php
    // ?? '' → Handles case where header doesn’t exist.
    // preg_replace('/[^\P{C}\n]+/u', '', $ua) → Strips weird control characters.
    // substr(..., 0, 255) → Protects DB columns & logs from overflows.
    // Fallback string → No empty value stored, useful for analytics/logging.

    function getUserAgent(): string {
        $ua = $_SERVER['HTTP_USER_AGENT'] ?? '';

        // Remove weird control chars and limit length
        $ua = trim(preg_replace('/[^\P{C}\n]+/u', '', $ua));
        $ua = substr($ua, 0, 255);

        return $ua !== '' ? $ua : 'Unknown User Agent';
    }