<?php
    require_once __DIR__ . '/config.php';
    // include __DIR__ . '/hexToRgb.php';

    //  INDEX:
    //  ========================================
    //  1. PSR-4 Autoloader
    //  2. DB Connection
    //  3. Functions Loader
    //  ========================================
    //  ASPulido Consultancy
    //  2025 September 18



    //  3. Functions Loader
    //  FUNCTIONS autoload section, except the LOADER() Function itself.
    //  Static functions will be converted into CLASSES,
    //     
    require_once BASE_PATH . '/functions/loadFunctions.php';

    //  1. PSR-4 Autoloader
    //  This PSR describes a specification for autoloading classes from file paths. 
    //  It is fully interoperable, and can be used in addition to any other autoloading specification, including PSR-0. 
    //  This PSR also describes where to place files that will be autoloaded according to the specification.
    //  PSR-4 Autoloader 
        spl_autoload_register(function ($class) {
            $prefixes = [
                'App\\Classes\\' => __DIR__ . '/../classes/',           // e.g. App\Classes\DB => classes/DB.php
                                                                        // add more prefixes here later if you create other roots
            ];

            foreach ($prefixes as $prefix => $baseDir) {
                $len = strlen($prefix);

                if (strncmp($class, $prefix, $len) !== 0) {
                    continue;
                }

                $relative = substr($class, $len);
                $file = $baseDir . str_replace('\\', '/', $relative) . '.php';

                if (is_file($file)) {
                    require $file;
                }
            }
        });


    //  2. DB Connection
    //  Defines the core behavior of database connections and provides a base class for database-specific connections.
    //  This DB Connection always writes in UTC +00:00
    try {

        $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4";
        $pdo = new PDO($dsn, DB_USER, DB_PASS, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ]);

        $pdo->exec("SET NAMES utf8mb4");
        $pdo->exec("SET time_zone = '+00:00'");


        // Get System Settings Override
        $stmt = $pdo->query("SELECT name, value FROM system_configuration");
        $dbSettings = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);

        // Apply overrides with type normalization
        if ($dbSettings) {
            foreach ($dbSettings as $name => $value) {

                // Normalize value type OR include in sql_validation of form entries/ store resulting values instead
                if (is_numeric($value)) {
                    // If numeric, cast to int or float
                    $value = (strpos($value, '.') !== false) ? (float)$value : (int)$value;
                } elseif (strtolower($value) === 'true') {
                    $value = true;
                } elseif (strtolower($value) === 'false') {
                    $value = false;
                } elseif ($value === 'NULL' || $value === null) {
                    $value = null;
                } // otherwise, leave as string

                // Define only if not already hardcoded
                if (!defined($name)) {
                    define($name, $value);
                }

                // ✅ If this is a HEX color, also define an *_RGB constant
                if (is_string($value) && preg_match('/^#([A-Fa-f0-9]{3}|[A-Fa-f0-9]{6})$/', $value)) {
                    $rgbName = $name . '_RGB';
                    if (!defined($rgbName)) {
                        define($rgbName, hexToRgb($value));
                    }
                }

                // echo '<p> ' . $name. ' = ' . $value;
                // echo '<p> ' . $rgbName. ' = ' . hexToRgb($value);
            }
            // exit;
            

            if (!defined('SYSTEM_LOCKED')) {
                define('SYSTEM_LOCKED', false);
            }
        }
        // exit;

    } catch (PDOException $e) {

        header("Location: " . BASE_URL . "/auth/connectionFailed.php");
        exit;
        die("DB Connection failed: " . $e->getMessage());

    }

    
    
    if (SYSTEM_LOCKED === true) { 
        header("Location: " . BASE_URL . "/auth/systemUnavailable.php");
        exit;
    }