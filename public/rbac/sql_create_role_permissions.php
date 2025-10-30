<?php
    // Initialize
    require __DIR__ . '/../../config/init.php';
    
    // Gateway
    include MIDDLEWARE;

    // RBAC
    use App\Classes\DB;
    use App\Classes\Toast;
    use App\Classes\Logger;
    $db = new DB($pdo);
    $logger = new Logger($db);

    // Validation Sequence
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {

        // Passed/Hidden Variables
        $module = trim($_POST['module'] ?? '');
        $actions = $_POST['actions'] ?? [];
        $description = trim($_POST['description'] ?? '');

        try {
            if ($module === '') {
                throw new Exception("Module is required.");
            }
            if (empty($actions)) {
                throw new Exception("At least one action is required.");
            }

            $db->beginTransaction();

            foreach ($actions as $action) {
                $action = trim($action);
                if ($action === '') continue;

                $db->exec("INSERT INTO rbac_permissions (module, action, operation, description, created_id) 
                            VALUES (?, ?, ?, ?, ?)", [
                    $module,
                    $action,
                    $module . ':' . $action,
                    $description,
                    $_SESSION['rbac']['user_id'] ?? 0
                ]);
            }

            // Log to TXN_logs
            $logger->log(Logger::TRANSACTION, [
                'user_id'               => $_SESSION['rbac']['user_id'],
                'turnout'               => 'RECORD CREATED',  
                'entity'                => 'rbac_roles',
                'entity_id'             => $_SESSION['rbac']['user_id'],
                'ipaddress'             => getClientIP(), 
                'user_agent'            => getUserAgent(),
                'before_data'           => '',
                'after_data'            => [
                    'module'            => $module,
                    'actions'           => implode(', ', $actions)
                    ],   
                'extra_context'         => [
                    'role'              => json_encode($_SESSION['rbac']['access'], JSON_UNESCAPED_UNICODE),
                    'caller'            => __FILE__,
                    'session_token'     => json_encode($_SESSION['rbac']['session_token'], JSON_UNESCAPED_UNICODE),
                    'session_expiry'    => $_SESSION['rbac']['session_expiry']
                    ]   
                ]);

            $db->commit();
            Toast::set('success', 'Permissions created successfully for module $module.', 'Success');

        } catch (Exception $e) {

            $db->rollBack();
            Toast::set('error', 'Error creating permissions: '.$e->getMessage(), 'Error');
        }
        $header_link = BASE_URL . '/rbac/tbl_role_management.php';

    } else {

        Toast::set('error', 'Oops! Something went wrong with your request. Please try again.', 'Error');
        $header_link = BASE_URL . '/rbac/tbl_role_management.php';

    }
    
    header("Location: ".$header_link);
    exit;
