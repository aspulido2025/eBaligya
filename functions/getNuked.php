<?php
    function getNuked() {
        // Make sure session is started before trying to destroy it
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        // Unset all session variables
        $_SESSION = [];

        // Delete PHPSESSID (or whatever session_name() is)
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"],
                $params["domain"],
                $params["secure"],
                $params["httponly"]
            );

        // If a session cookie exists, delete it
        
            $params = session_get_cookie_params();
            setcookie(COOKIE_HANDLE, '', time() - 3600,
                $params["path"], 
                $params["domain"],
                $params["secure"], 
                $params["httponly"]
            );
        }
        
        // Destroy the session data on the server
        session_destroy();
    }