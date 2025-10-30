<?php
    /**
     * Function Loader
     * Loads all PHP function files in /functions directory,
     * except this loadAll.php itself.
     */

    $functionsDir = __DIR__;

    foreach (glob($functionsDir . '/*.php') as $file) {
        if (basename($file) === 'loadFunctions.php' ) continue;
        require_once realpath($file);
        // echo '<p>' . $file;
    }
    // exit;