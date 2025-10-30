<?php
    // Initialize
    require __DIR__ . '/../../config/init.php';
    session_start();

    // RBAC
    use App\Classes\DB;
    use App\Classes\Logger;
    $db = new DB($pdo);
    $logger = new Logger($db);

    // Log to AUTH
    $logger->log(Logger::AUTH, [
        'user_id'           => $_SESSION['rbac']['user_id'], 
        'turnout'           => 'LOGOUT SUCCESS',         
        'entity'            => 'system_users',
        'entity_id'         => $_SESSION['rbac']['user_id'],
        'ipaddress'         => getClientIP(), 
        'user_agent'        => getUserAgent(), 
        'master_session'    => $_SESSION['rbac']['session_token'],
        'before_data'       => NULL,
        'after_data'        => NULL,
        'extra_context'     => [
            'role'          => $_SESSION['rbac']['role'], 
            'caller'        => __FILE__,
            'csrf_token'    => $_SESSION['rbac']['session_token']
            ]
        ]);

    // Log to TXN_logs
    $logger->log(Logger::TRANSACTION, [
        'user_id'           => $_SESSION['rbac']['user_id'],
        'turnout'           => 'SESSION ENDED',
        'entity'            => 'system_users',
        'entity_id'         => $_SESSION['rbac']['user_id'],
        'ipaddress'         => getClientIP(), 
        'user_agent'        => getUserAgent(), 
        'master_session'    => $_SESSION['rbac']['session_token'],
        'before_data'       => NULL,
        'after_data'        => NULL,
        'extra_context'     => [
            'role'          => $_SESSION['rbac']['role'], 
            'caller'        => __FILE__,
            'csrf_token'    => $_SESSION['rbac']['session_token']
            ]   
        ]);

    // Dump Cookie and logs_cookie_token BY user_id
    if (isset($_COOKIE[COOKIE_HANDLE])) {

        // Delete all cookie based on user_id
        $stmt = $pdo->prepare("DELETE FROM log_cookie WHERE user_id = ?");
        $stmt->execute([ $_SESSION['rbac']['user_id'] ]);
        setcookie(COOKIE_HANDLE, "", time() - 3600, "/");
        
    }

	getNuked();
	header("Location: ". BASE_URL); 
    exit;
?>