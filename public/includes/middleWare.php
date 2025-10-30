<?php
    // Initialize
    require __DIR__ . '/../../config/init.php';
    session_start();

    // RBAC
    use App\Classes\DB;
    use App\Classes\DateHelper;
    use App\Classes\Logger;

    $db = new DB($pdo);
    $logger = new Logger($db);

    // The MiddleWare - Check for RBAC Session
    if(!isset($_SESSION['rbac']['user_id'])) {
        header("Location: ". BASE_URL);
        exit;
    }

    // Check if RBAC Session is equal to stored session and NOT expired.
    $row = $db->fetch( "SELECT system_users.* FROM system_users WHERE system_users.id = ?", [ $_SESSION['rbac']['user_id'] ], [], 'row' );

    // Master Index Session
    $sessionToken = hash("sha256", getSecureCode(SESSION_TOKEN, 'hex')) ?? NULL;
    
    if ( $_SESSION['rbac']['session_token'] == $row['session_token'] ) {

        if ( strtotime(($row['session_expiry'])) > strtotime(DateHelper::nowUtc())) { 

            // Session is not yet expired
            // Update Session Expiry only when remaining time getWithin() 
            if (getWithin($row['session_expiry'], DateHelper::nowUtc(), 15, 'minute')) {

                $db->exec("UPDATE system_users SET session_expiry = ? WHERE id = ?", 
                    [ DATE('Y-m-d H:i:s', strtotime( DateHelper::nowUtc() ) + SESSION_LIFETIME  ), $_SESSION['rbac']['user_id'] ], 
                    [ '', 'numeric'] );

                // Log to TXN
                $logger->log(Logger::TRANSACTION, [
                    'user_id'               => $_SESSION['rbac']['user_id'],
                    'turnout'               => 'SESSION REFRESHED',  
                    'entity'                => 'system_users',
                    'entity_id'             => $_SESSION['rbac']['user_id'],
                    'ipaddress'             => getClientIP(), 
                    'user_agent'            => getUserAgent(),  
                    'master_session'        => $sessionToken,
                    'before_data'           => [
                        'role'              => $_SESSION['rbac']['role'],
                        'caller'            => __FILE__, 
                        'session_expiry'    => $_SESSION['rbac']['session_expiry']
                        ],
                    'after_data'            => [
                        'role'              => $_SESSION['rbac']['role'], 
                        'caller'            => __FILE__, 
                        'session_expiry'    => DATE('Y-m-d H:i:s', strtotime( DateHelper::nowUtc() ) + SESSION_LIFETIME  )
                        ],
                    'extra_context'         => [
                        'role'              => $_SESSION['rbac']['role'], 
                        'caller'            => __FILE__, 
                        'session_token'     => $sessionToken
                        ]
                    ]);

                // HAND OVER TO RBAC
                $_SESSION['rbac']['session_expiry'] = DATE('Y-m-d H:i:s', strtotime( DateHelper::nowUtc() ) + SESSION_LIFETIME  );

            }

        } else { // ***** $row['session_expiry'] > gmdate("Y-m-d H:i:s", time())

            // Log to TXN_logs
            $logger->log(Logger::TRANSACTION, [
                'user_id'               => $_SESSION['rbac']['user_id'],
                'turnout'               => 'SESSION EXPIRED', 
                'entity'                => 'system_users',
                'entity_id'             => $_SESSION['rbac']['user_id'],
                'ipaddress'             => getClientIP(), 
                'user_agent'            => getUserAgent(),
                'master_session'        => $sessionToken,
                'before_data'           => NULL,
                'after_data'            => NULL,
                'extra_context'         => [
                    'role'              => $_SESSION['rbac']['role'], 
                    'caller'            => __FILE__, 
                    'session_token'     => $_SESSION['rbac']['session_token']
                    ]   
                ]);

            $_SESSION['authGuard'] = "SESSION EXPIRED||Please log in again.||Login";
            header("Location: " . BASE_URL . "/auth/authGuard.php");

        }

    } else { // ***** ( $row['session_token'] == $_SESSION['rbac']['session_token'] )

        // Session token is updated via Login from another device.

        // Log to TXN_logs
        $logger->log(Logger::TRANSACTION, [
            'user_id'               => $_SESSION['rbac']['user_id'],
            'turnout'               => 'SESSION TERMINATED',  
            'entity'                => 'system_users',
            'entity_id'             => $_SESSION['rbac']['user_id'],
            'ipaddress'             => getClientIP(), 
            'user_agent'            => getUserAgent(),
            'master_session'        => $sessionToken,
            'before_data'           => NULL,
            'after_data'            => NULL,
            'extra_context'         => [
                'role'              => $_SESSION['rbac']['role'], 
                'caller'            => __FILE__, 
                'session_token'     => $_SESSION['rbac']['session_token']
                ]   
            ]);

        $_SESSION['authGuard'] = "NEW DEVICE DETECTED||Please log in again.||Login";
        header("Location: " . BASE_URL . "/auth/authGuard.php");   
        exit;
    }
?>