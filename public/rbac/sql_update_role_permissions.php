<?php 
    // Initialize
    require __DIR__ . '/../../config/init.php';

    // Gateway
    include MIDDLEWARE;

    // Classes
    use App\Classes\DB;
    use App\Classes\Toast;
    use App\Classes\Logger;
    $db = new DB($pdo);
    $logger = new Logger($db);

    // Validation Sequence
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {

        // Passed/Hidden Variables
        $varRoleID = $_POST['id']; 
        $access  = trim($_POST['access'] ?? '');
        $selected = $_POST['permissions'] ?? [];

        try {
            $db->beginTransaction();

            // Clear existing permissions for this role
            $before = $db->exec("DELETE FROM rbac_role_permissions WHERE role_id = ?", [$varRoleID]);

            // Insert new ones if selected
            if (!empty($selected)) {
                $placeholders = rtrim(str_repeat("(?, ?),", count($selected)), ",");
                $values = [];
                foreach ($selected as $perm_id) {
                    $values[] = $varRoleID;
                    $values[] = (int)$perm_id;
                }
                $sql = "INSERT INTO rbac_role_permissions (role_id, permission_id) VALUES $placeholders";
                $db->exec($sql, $values);
            }

            // Log to TXN_logs
                $logger->log(Logger::TRANSACTION, [
                'user_id'               => $_SESSION['rbac']['user_id'],
                'turnout'               => 'PERMISSIONS UPDATED',
                'entity'                => 'rbac_roles',
                'entity_id'             => $_SESSION['rbac']['user_id'],
                'ipaddress'             => getClientIP(), 
                'user_agent'            => getUserAgent(),
                'before_data'           => $before,
                'after_data'            => json_encode(['permissions' => $selected]),
                'extra_context'         => [
                    'role'              => json_encode($_SESSION['rbac']['access'], JSON_UNESCAPED_UNICODE),
                    'caller'            => __FILE__,
                    'session_token'     => json_encode($_SESSION['rbac']['session_token'], JSON_UNESCAPED_UNICODE),
                    'session_expiry'    => $_SESSION['rbac']['session_expiry']
                    ]
                ]);

            $db->commit();
            Toast::set('success', 'Permissions updated successfully.', 'Success');

        } catch (Exception $e) {

            $db->rollback();
            Toast::set('error', 'Error updating permissions: '.$e->getMessage(), 'Error');

        }
        $header_link = BASE_URL . '/rbac/tbl_role_management.php';

    } else {
        
        Toast::set('error', 'Oops! Something went wrong with your request. Please try again.', 'Error');
        $header_link = BASE_URL . '/rbac/tbl_role_management.php';

    }

    header("Location: ".$header_link);
    exit;