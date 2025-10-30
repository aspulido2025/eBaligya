<?php
    function hexToRgb($hex) {
        $hex = ltrim($hex, '#');
        if (strlen($hex) === 3) {
            $hex = $hex[0].$hex[0].$hex[1].$hex[1].$hex[2].$hex[2];
        }
        $int = hexdec($hex);
        return sprintf("%d, %d, %d",
            ($int >> 16) & 255,
            ($int >> 8) & 255,
            $int & 255
        );
    }    