<?php
    // Initialize
    require __DIR__ . '/../../config/init.php';

    // Gateway
    include MIDDLEWARE;

    // Classes
    use App\Classes\DB;
    use App\Classes\Logger;
    use App\Classes\DateHelper;
    use App\Classes\URLKeeper;
    use App\Classes\Toast;
    $db = new DB($pdo);
    $logger = new Logger($db);

    // Session validation variables
    $errors = [ 'varRoleName' => NULL, 'varDescription' => NULL ];   
    $oldval = [ 'varRoleName' => NULL, 'varDescription' => NULL ];   

    // Validation Sequence
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {

        // Passed/Hidden Variables
        $varRoleID = $_POST['id']; 
        $varRoleName = $_POST['varRoleName'];
        $varDescription = $_POST['varDescription'];

        // Validate Role Name
        $row = $db->fetch( "SELECT access, description FROM rbac_roles WHERE rbac_roles.access = ? AND rbac_roles.id <> ?", [ $varRoleName, $varRoleID], [], 'all' ); 

        if ($row) {
            $errors['varRoleName'] = "Duplicate found.";
        } 

        // Check for session errors.
        if ( implode( '', $errors ) !== '') {

            $_SESSION['errors'] = $errors;
            $_SESSION['oldval'] = $_POST;
            $header_link =  BASE_URL . '/rbac/update_role.php?token=' . urlencode(URLKeeper::encode($varRoleID, ENCRYPTION_PASSKEY, EXPIRY_ENCRYPTION)); 

        } else { 

            try {
                $db->beginTransaction();

                // 1. Fetch and lock the row (multiple columns)
                $before = $db->fetch( "SELECT access, description FROM rbac_roles WHERE rbac_roles.id = ? FOR UPDATE", [ $varRoleID], [], 'row' ); 

                // 2. Apply update
                $sql = "UPDATE rbac_roles  SET access = ?, description = ?, updated = ?  WHERE id = ?";
                $db->exec($sql, [$varRoleName, $varDescription, DateHelper::nowUtc(), $varRoleID]);

                // 📝 Transaction log (simplified)
                $logger->log(Logger::TRANSACTION, [
                    'user_id'               => $_SESSION['rbac']['user_id'],
                    'turnout'               => 'RECORD UPDATED',  
                    'entity'                => 'rbac_roles',
                    'entity_id'             => $_SESSION['rbac']['user_id'],
                    'ipaddress'             => getClientIP(), 
                    'user_agent'            => getUserAgent(),
                    'master_session'=> $_SESSION['rbac']['session_token'],
                    'before_data'           => $before,
                    'after_data'            => [
                        'name'              => $varRoleName,
                        'description'       => $varDescription
                        ],   
                    'extra_context'         => [
                        'role'              => json_encode($_SESSION['rbac']['access'], JSON_UNESCAPED_UNICODE),
                        'caller'            => __FILE__,
                        'session_token'     => json_encode($_SESSION['rbac']['session_token'], JSON_UNESCAPED_UNICODE),
                        'session_expiry'    => $_SESSION['rbac']['session_expiry']
                        ]
                    ]);

                $db->commit();
                Toast::set('success', 'Record updated successfully.', 'Success');

            } catch (Exception $e) {

                $db->rollback();
                Toast::set('error', 'Error updating record: '.$e->getMessage(), 'Error');

            }
            $header_link = BASE_URL . '/rbac/tbl_role_management.php';

        }

    } else {

        Toast::set('error', 'Oops! Something went wrong with your request. Please try again.', 'Error');
        $header_link = BASE_URL . '/rbac/tbl_role_management.php';

    }

    header("Location: ".$header_link);
    exit;