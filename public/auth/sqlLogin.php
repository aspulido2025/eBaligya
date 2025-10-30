<?php
    session_start();
    require_once __DIR__ . '/../../config/init.php';

    use App\Classes\DB;
    use App\Classes\Toast;
    use App\Classes\Logger;
    use App\Classes\DateHelper;
    use App\Classes\RBAC;

    $db = new DB($pdo);
    $logger = new Logger($db);

    $errors = [ 'varUsername' => NULL, 'varPassword' => NULL ];   
    $sessionToken = hash("sha256", getSecureCode(SESSION_TOKEN, 'hex')) ?? NULL ;
    $selector  = getSecureCode(COOKIE_SELECTOR, 'hex');
    $validator = getSecureCode(COOKIE_VALIDATOR, 'hex');
    $hashedValidator = hash("sha256", $validator);

    // Validation Sequence
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
        $varUsername = $_POST["varUsername"] ?? "";
        $varPassword = $_POST["varPassword"] ?? "";
        $varRemember = isset($_POST["varRemember"]);

        $row = $db->fetch( "SELECT rbac_user_roles.role_id, rbac_roles.access, system_users.*
            FROM system_users 
            JOIN rbac_user_roles On rbac_user_roles.user_id = system_users.id
            JOIN rbac_roles On rbac_roles.id = rbac_user_roles.role_id
            WHERE system_users.username = ? AND
                rbac_user_roles.is_default = ?", [ $varUsername, 1 ], [ 'lower', 'numeric'], 'row' );

        if ( !$row )  {

            $errors['varUsername'] = "Username not found.";

        } else {

            if ( $row['is_banned'] == 1 ) { 

                // Log to AUTH
                $logger->log(Logger::AUTH, [
                    'user_id'       => $row['id'],
                    'turnout'       => 'LOGIN BLOCKED', 
                    'entity'        => 'system_users',
                    'entity_id'     => $row['id'],
                    'ipaddress'     => getClientIP(), 
                    'user_agent'    => getUserAgent(),
                    'master_session'    => $sessionToken,
                    'extra_context'     => [
                        'role'          => $row['access'],
                        'caller'        => __FILE__,
                        'csrf_token'    => $sessionToken
                                ]
                    ]);

                $_SESSION['authGuard'] = "ACCOUNT RESTRICTED||Please contact the System Administrator.||Login";
                $header_link = BASE_URL . '/auth/authGuard.php';

            } else {

                if (password_verify($varPassword, $row["password_hash"])) {
                    $_SESSION["user_id"] = $row["id"];
                    $_SESSION["fullname"] = $row["fullname"];
                    $_SESSION["email_address"] = $row["email_address"];

                    // Remember Me
                    if ($varRemember) {

                        // SET COOKIE
                        setcookie(COOKIE_HANDLE, $selector . ":" . $validator, strtotime(DateHelper::nowUtc())+EXPIRY_COOKIE, "/", "", false, true);

                        // Log to COOKIE
                        $logger->log(Logger::COOKIE, [
                            'user_id'           => $row['id'],
                            'selector'          => $selector, 
                            'hashed_validator'  => $hashedValidator,
                            'ipaddress'         => getClientIP(), 
                            'user_agent'        => getUserAgent(),
                            'master_session'    => $sessionToken,
                            'expires_at'        => DATE('Y-m-d H:i:s', strtotime(DateHelper::nowUtc())+EXPIRY_COOKIE)
                            ]);

                    }

                    // Log to AUTH
                    $logger->log(Logger::AUTH, [
                        'user_id'       => $row['id'],
                        'turnout'       => 'LOGIN SUCCESS', 
                        'entity'        => 'system_users',
                        'entity_id'     => $row['id'],
                        'ipaddress'     => getClientIP(), 
                        'user_agent'    => getUserAgent(),
                        'master_session'=> $sessionToken,
                        'extra_context'     => [
                            'role'          => $row['access'],
                            'caller'        => __FILE__,
                            'csrf_token'    => $sessionToken
                                    ]
                        ]);

                    // UPDATE session_token, session_expiry, is_archived (check time here UTC)
                    $db->exec("UPDATE system_users SET session_token = ?, session_expiry = ?, is_archived = ?, date_min = ?, date_max = ? WHERE id = ?", 
                             [ $sessionToken, DATE('Y-m-d H:i:s', strtotime(DateHelper::nowUtc())+SESSION_LIFETIME), "0", DATE('Y-m-01'), DATE('Y-m-t'), $row['role_id'] ], 
                             [ 'strig', '', 'numeric', '', '', 'numeric' ] );

                    // Log to TXN
                    $logger->log(Logger::TRANSACTION, [
                        'user_id'       => $row['id'],
                        'turnout'       => 'SESSION STARTED',  
                        'entity'        => 'system_users',
                        'entity_id'     => $row['id'],
                        'ipaddress'     => getClientIP(), 
                        'user_agent'    => getUserAgent(),  
                        'master_session'=> $sessionToken,
                        'extra_context' => [
                            'role'      => $row['access'], 
                            'caller'    => __FILE__, 
                            'session_token'=> $sessionToken
                            ]
                        ]);

                    // HAND OVER TO RBAC
                    $rbac = new RBAC($db, $row['id']);
                    $defaultRole = $rbac->getActiveRole();

                    $_SESSION['rbac'] = [
                        'user_id'               => $row['id'],
                        'username'              => $row['username'],
                        'fullname'              => $row['fullname'],
                        'birthday'              => $row['date_birth'], 
                        'photo'                 => $row['photo'], 
                        'role_id'               => $defaultRole,
                        'role'                  => $row['access'],
                        
                        'mobile_number'         => $row['mobile_number'],
                        'verified_mobile'       => $row['is_verified_mobile'],
                        'email_address'         => $row['email_address'],
                        'verified_email'        => $row['is_verified_email'],
                        
                        'is_banned'             => $row['is_banned'],
                        'date_min'              => $row['date_min'],
                        'date_max'              => $row['date_max'],

                        'roles'                 => $rbac->getUserRoles(),
                        'session_token'         => $sessionToken,
                        'session_expiry'        => $row['session_expiry'],
                        'permissions'           => $rbac->listPermissions()
                    ];


                    // Toast :warning :error :success
                    Toast::set('success', 'Logged-in successfully.', 'Success');
                    $header_link = BASE_URL;
                
                } else {

                    // Log to AUTH
                    $logger->log(Logger::AUTH, [
                        'user_id'           => $row['id'],
                        'turnout'           => 'WRONG PASSWORD', 
                        'entity'            => 'system_users',
                        'entity_id'         => $row['id'],
                        'ipaddress'         => getClientIP(), 
                        'user_agent'        => getUserAgent(),
                        'master_session'    => $sessionToken,
                        'extra_context'     => [
                            'role'          => $row['role'],
                            'caller'        => __FILE__,
                            'csrf_token'    => $sessionToken
                            ]
                        ]);

                    // Check Auth_logs for attempts
                    $retry = $db->fetch( "SELECT log_authentication.user_id FROM log_authentication 
                        WHERE log_authentication.user_id = ? AND 
                        DATE_FORMAT(log_authentication.created, '%Y-%m-%d') = ? AND 
                        log_authentication.turnout = ?", 
                        [ $row['id'], DATE('Y-m-d', strtotime(DateHelper::nowUtc())), 'WRONG PASSWORD' ], 
                        [], 
                        'all' );
                    
                    if (count($retry) < RETRY_ATTEMPTS) {

                        $errors['varPassword'] = "Wrong password.";                        

                    } else {

                        // Restrict Account
                        $db->exec( "UPDATE system_users SET is_banned = ? WHERE id = ?", [ 1, $row['id'] ], [] );

                        // Log TXN
                        $logger->log(Logger::TRANSACTION, [
                            'user_id'           => $row['id'],
                            'turnout'           => 'ACCOUNT RESTRICTED',
                            'entity'            => 'system_users',
                            'entity_id'         => $row['id'],
                            'ipaddress'         => getClientIP(), 
                            'user_agent'        => getUserAgent(),  
                            'before_data'       => NULL,
                            'after_data'        => NULL,
                            'master_session'    => $sessionToken,
                            'extra_context'     => [
                                'role'          => $row['role'],
                                'caller'        => __FILE__,
                                'csrf_token'    => $sessionToken
                                ]
                            ]);

                        $_SESSION['authGuard'] = "SELF-INFLICTED RESTRICTION||Please contact the System Administrator.||Login";
                        $header_link = BASE_URL . '/auth/authGuard.php';
                                
                    }

                }

            } 
        }
            
        // Check for session errors.
        if (implode('', $errors) !== '') {
            // Send back errors & old input
            $_SESSION['errors'] = $errors;
            $_SESSION['oldval'] = $_POST;
            $header_link = BASE_URL . "/auth/login.php";
        } 

    } else {
    
        Toast::set('error', 'Something went wrong. Please try again.', 'Error');
        $header_link = BASE_URL . '/index.php';

    }
    
    header("Location: ".$header_link);
    exit;