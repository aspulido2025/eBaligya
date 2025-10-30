<?php
    function getWithin($time1, $time2, int $diff, string $unit): bool {
        $secondsPerUnit = [
            'year'   => 31536000, // ~365 days
            'month'  => 2592000,  // ~30 days
            'day'    => 86400,
            'hour'   => 3600,
            'minute' => 60,
            'second' => 1,
        ];

        $diffSeconds = abs(strtotime($time1) - strtotime($time2));
        return $diffSeconds < ($diff * $secondsPerUnit[$unit]);
    }

    // Example usage:
    // if (getWithin($_SESSION['datetime'], $now, 10, 'minute')) {
    //     echo "✅ Difference is less than 10 minutes";
    // }

    //
    // getWithin($t1, $t2, 2, 'hour') → checks if within 2 hours
    // getWithin($t1, $t2, 30, 'second') → checks if within 30 seconds
    // getWithin($t1, $t2, 1, 'day') → checks if within 1 day