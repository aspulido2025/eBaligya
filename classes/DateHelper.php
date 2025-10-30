<?php
// 🚀 A centralized DateHelper, you don't even have to touch UTC_TIMESTAMP() and gmdate() functions.
// DB always stores UTC.
// PHP always converts in/out via the class.
// Queries stay clean (WHERE created_at BETWEEN ? AND ?).
// No timezone leaks.

//  PHP
//  formatValues() in /project/functions
//  DATE('Y-m-d', );

//  QUERY



namespace App\Classes;

class DateHelper
{

    /**
     * Get "now" in UTC (safe for DB inserts if needed)
     */
    public static function nowUtc(): string
    {
        return gmdate('Y-m-d H:i:s'); // always UTC
    }

    /**
     * Get "now" in local timezone
     */
    public static function nowLocal(string $tz = TIMEZONE): string
    {
        $dt = new \DateTime('now', new \DateTimeZone($tz));
        return $dt->format('Y-m-d H:i:s');
    }

    /**
     * directly accepts two local dates (Y-m-d), expands them into full days, and converts both ends to UTC:
     * $dateMin = '2025-09-01';
     * $dateMax = '2025-09-30';

     * $range = DateHelper::localDateRangeToUtcRange($dateMin, $dateMax);

     * echo '<p>start '.$range['start']; // 2025-08-31 16:00:00 (UTC)
     * echo '<p>end '.$range['end'];     // 2025-09-30 15:59:59 (UTC)

     * // Example query
     * $sql = "WHERE created_at BETWEEN ? AND ?";
     * $params = [$range['start'], $range['end']];
     * 
     * // Query safe
     * $range = DateHelper::localDateRangeToUtcRange('2025-09-01', '2025-09-01');
     * $sql   = "WHERE created_at BETWEEN ? AND ?";
     * $params = [$range['start'], $range['end']];

     * // Display safe
     * $range = DateHelper::localDateRangeToUtcRange('2025-09-01', '2025-09-30', TIMEZONE, 'Y-m-d');
     * echo "From {$range['start']} to {$range['end']}";
     */
    public static function localDateRangeToUtcRange(
        string $localDateStart,
        string $localDateEnd,
        string $tz = TIMEZONE,
        string $format = 'Y-m-d H:i:s'
    ): array {
        try {
            $start = new \DateTime($localDateStart . ' 00:00:00', new \DateTimeZone($tz));
            $start->setTimezone(new \DateTimeZone('UTC'));

            $end = new \DateTime($localDateEnd . ' 23:59:59', new \DateTimeZone($tz));
            $end->setTimezone(new \DateTimeZone('UTC'));

            return [
                'start' => $start->format($format),
                'end'   => $end->format($format),
            ];
        } catch (\Exception $e) {
            return ['start' => null, 'end' => null];
        }
    }


    // /**
    //  * Convert UTC datetime to local for display
    //  * Returns empty string if input is null/invalid.
    //  */
    // // ===========================================================================================
    // public static function utcToLocal(?string $utcDateTime, string $tz = TIMEZONE): string
    // {
    //     if (!$utcDateTime) {
    //         return '';
    //     }

    //     try {
    //         $dt = new \DateTime($utcDateTime, new \DateTimeZone('UTC')); // stored in UTC
    //         $dt->setTimezone(new \DateTimeZone($tz));                   // convert to local
    //         return $dt->format('Y-m-d H:i:s');
    //     } catch (\Exception $e) {
    //         return '';
    //     }
    // }


    /**
     * Convert local time to UTC for queries (e.g. BETWEEN ? AND ?)
     * Returns null if input is invalid.
     *
     * Full datetime input 
     * echo DateHelper::localToUtc('2025-09-02 15:30:00');  
     * → 2025-09-02 07:30:00 (UTC)
     * 
     * Date-only input
     * echo DateHelper::localToUtc('2025-09-02');  
     * → 2025-09-01 16:00:00 (UTC)   (Asia/Manila +8)
     * 
     * Force date-only output
     * echo DateHelper::localToUtc('2025-09-02', TIMEZONE, 'Y-m-d');  
     * → 2025-09-01

     */
    // ==================================
    // public static function localToUtc(
    // ==================================
    //     string $localDateOrDateTime,
    //     string $tz = TIMEZONE,
    //     string $format = 'Y-m-d H:i:s'
    // ): ?string {
    //     try {
    //         // Detect if it's just a date (Y-m-d)
    //         if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $localDateOrDateTime)) {
    //             $dt = new \DateTime($localDateOrDateTime . ' 00:00:00', new \DateTimeZone($tz));
    //         } else {
    //             $dt = new \DateTime($localDateOrDateTime, new \DateTimeZone($tz));
    //         }

    //         $dt->setTimezone(new \DateTimeZone('UTC'));
    //         return $dt->format($format);
    //     } catch (\Exception $e) {
    //         return null;
    //     }
    // }


    /**
     * Convert UTC datetime to local for display
     * Returns empty string if input is null/invalid.
     */
    // ===========================================================================================
    // public static function formatLocalTime(?string $utcDateTime, string $tz = TIMEZONE): string
    // ===========================================================================================
    // {
    //     if (!$utcDateTime) {
    //         return '';
    //     }

    //     try {
    //         $dt = new \DateTime($utcDateTime, new \DateTimeZone('UTC')); // stored in UTC
    //         $dt->setTimezone(new \DateTimeZone($tz));                   // convert to local
    //         return $dt->format('Y-m-d H:i:s');
    //     } catch (\Exception $e) {
    //         return '';
    //     }
    // }

    

    /**
     * Convert local datetime to Unix timestamp
     * Returns 0 if input is invalid.
     */
    // ====================================================================================================
    // public static function formatLocalTimeToTimestamp(string $localDateTime, string $tz = TIMEZONE): int
    // ====================================================================================================
    // {
    //     try {
    //         $dt = new \DateTime($localDateTime, new \DateTimeZone($tz));
    //         return $dt->getTimestamp();
    //     } catch (\Exception $e) {
    //         return 0;
    //     }
    // }

    /**
     * Convert UTC datetime string to Unix timestamp
     * Returns 0 if input is invalid.
     */
    // ===============================================================
    // public static function utcToTimestamp(string $utcDateTime): int
    // ===============================================================
    // {
    //     try {
    //         $dt = new \DateTime($utcDateTime, new \DateTimeZone('UTC'));
    //         return $dt->getTimestamp();
    //     } catch (\Exception $e) {
    //         return 0;
    //     }
    // }
    
    /**
     * Get current date in UTC (Y-m-d).
     */
    // =========================================
    // public static function todayUtc(): string
    // =========================================
    // {
    //     return gmdate('Y-m-d');
    // }

    /**
     * Get current date in local timezone (Y-m-d).
     */
    // ================================================================
    // public static function todayLocal(string $tz = TIMEZONE): string
    // ================================================================
    // {
    //     $dt = new \DateTime('now', new \DateTimeZone($tz));
    //     return $dt->format('Y-m-d');
    // }

    /**
     * Normalize any datetime string (UTC or local) to date-only (Y-m-d).
     * Returns empty string if invalid.
     */
    // =================================================================================
    // public static function dateOnly(?string $dateTime, string $tz = TIMEZONE): string
    // =================================================================================
    // {
    //     if (!$dateTime) {
    //         return '';
    //     }

    //     try {
    //         $dt = new \DateTime($dateTime, new \DateTimeZone($tz));
    //         return $dt->format('Y-m-d');
    //     } catch (\Exception $e) {
    //         return '';
    //     }
    // }

    /**
     * Add seconds to nowUtc and return UTC datetime string.
     */
    // ===========================================================
    // public static function addLifetimeUtc(int $seconds): string
    // ===========================================================
    // {
    //     $dt = new \DateTime('now', new \DateTimeZone('UTC'));
    //     $dt->modify("+{$seconds} seconds");
    //     return $dt->format('Y-m-d H:i:s');
    // }

}



// require_once 'DateHelper.php';

// use App\Classes\DateHelper;
// // 1. Query with local → UTC conversion
// $utcStart = DateHelper::localToUtc('2025-08-28 00:00:00');
// $utcEnd   = DateHelper::localToUtc('2025-08-28 23:59:59');

// $sql = "
//     SELECT *
//     FROM log_authentication
//     WHERE created_at BETWEEN ? AND ?
// ";
// $stmt = $pdo->prepare($sql);
// $stmt->execute([$utcStart, $utcEnd]);

// // 2. Display results (UTC → Local)
// while ($row = $stmt->fetch()) {
//     echo DateHelper::formatLocalTime($row['created_at']);
// }

// // 3. Get timestamps directly
// echo DateHelper::nowUtc();   // 2025-08-28 06:13:22
// echo DateHelper::nowLocal(); // 2025-08-28 14:13:22 (Asia/Manila)

//===================================== REVISED 09-01-2025
// Behavior with invalid inputs
// localToUtc("nonsense") → null
// formatLocalTime("invalid") → "" (empty string, so UI won’t break)
// formatLocalTimeToTimestamp("bad input") → 0
// utcToTimestamp("bad input") → 0

// echo DateHelper::todayUtc();       // "2025-09-01"
// echo DateHelper::todayLocal();     // "2025-09-01" (local tz)
// echo DateHelper::dateOnly("2025-09-01 15:45:10"); // "2025-09-01"

// Option 1: get string for DB insert
// $expiryStr = DateHelper::addLifetimeUtc(SESSION_LIFETIME);
// "2025-09-01 08:32:10"

// Option 2: still keep timestamp if needed
// $expiryTs  = DateHelper::utcToTimestamp($expiryStr);
// 1756728730