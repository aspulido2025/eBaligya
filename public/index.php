<?php 
    require __DIR__ . '/../config/init.php';
    session_start();

    use App\Classes\DB;
    use App\Classes\Toast;
    use App\Classes\Logger;
    use App\Classes\DateHelper;
    use App\Classes\RBAC;

    $db = new DB($pdo);
    $logger = new Logger($db);

    if (!isset($_SESSION["user_id"])) {

        if (!empty($_COOKIE[COOKIE_HANDLE])) {
            list($selector, $validator) = explode(":", $_COOKIE[COOKIE_HANDLE]);

            $token = $db->fetch( "SELECT user_id, hashed_validator, expires_at 
                FROM log_cookie 
                WHERE selector = ?", [ $selector ], [ '' ], 'row' );

            if ($token) {

                // Master Index Session
                $sessionToken = hash("sha256", getSecureCode(SESSION_TOKEN, 'hex')) ?? NULL ;

                if ($token["expires_at"] > strtotime(DateHelper::nowUtc())) {
                    if (hash_equals($token["hashed_validator"], hash("sha256", $validator))) {
                        $_SESSION["user_id"] = $token["user_id"];

                        // Fetch for Hand-over
                        $row = $db->fetch( "SELECT rbac_user_roles.role_id, rbac_roles.access, system_users.*
                            FROM system_users 
                            JOIN rbac_user_roles On rbac_user_roles.user_id = system_users.id
                            JOIN rbac_roles On rbac_roles.id = rbac_user_roles.role_id
                            WHERE system_users.id = ? AND
                                rbac_user_roles.is_default = ?", [ $_SESSION["user_id"], 1 ], [ 'lower', 'numeric'], 'row' );

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
                            'permissions'           => $rbac->listPermissions(),
                            
                            'privacy_accepted'      => $row['is_legal_privacy']
                            ];

                        // Optionally reissue cookie (COOKIE_REFRESHED)
                        $newValidator = getSecureCode(COOKIE_VALIDATOR, 'hex');
                        $newhashedValidator = hash("sha256", $newValidator);
                        $newExpiry = DATE('Y-m-d H:i:s', strtotime(DateHelper::nowUtc())+EXPIRY_COOKIE);

                        // Update Cookie
                        $db->exec( "UPDATE log_cookie SET hashed_validator = ?, expires_at = ? WHERE selector = ?", [$newhashedValidator, $newExpiry, $selector] );

                        // SET COOKIE
                        setcookie(COOKIE_HANDLE, $selector . ":" . $newValidator, strtotime(DateHelper::nowUtc())+EXPIRY_COOKIE, "/", "", false, true);

                        // Log to AUTH
                        $logger->log(Logger::AUTH, [
                            'user_id'           => $token["user_id"],
                            'turnout'           => 'COOKIE REFRESHED', 
                            'ipaddress'         => getClientIP(), 
                            'user_agent'        => getUserAgent(),
                            'master_session'    => $sessionToken,
                            'extra_context'     => [
                                'caller'        => __FILE__, 
                                'selector'      => $selector
                                ]
                            ]);

                    } else {

                        $db->exec("DELETE FROM log_cookie WHERE selector = ?", [$selector]);
                        setcookie(COOKIE_HANDLE, "", time() - 3600, "/");
                        getNuked();

                        // Log to AUTH
                        $logger->log(Logger::AUTH, [
                            'user_id'           => $token["user_id"],
                            'turnout'           => 'COOKIE THEFT DETECTED', 
                            'ipaddress'         => getClientIP(), 
                            'user_agent'        => getUserAgent(),
                            'master_session'    => $sessionToken,
                            'extra_context'     => [
                                'caller'        => __FILE__, 
                                'selector'      => $selector
                                ]
                            ]);
                    }

                } else {

                    setcookie(COOKIE_HANDLE, "", time() - 3600, "/");
                    getNuked();
                    // Log to AUTH
                    $logger->log(Logger::AUTH, [
                        'user_id'           => $token["user_id"],
                        'turnout'           => 'INVALID COOKIE SELECTOR', 
                        'ipaddress'         => getClientIP(), 
                        'user_agent'        => getUserAgent(),
                        'master_session'    => $sessionToken,
                        'extra_context'     => [
                            'caller'        => __FILE__, 
                            'selector'      => $selector
                            ]
                        ]);
                }
            }
        } 
    }
    
    // Final check if there is still no session, REDIRECT.
    if (!isset($_SESSION["user_id"])) {
        header("Location: " . BASE_URL . "/auth/login.php");
        exit;
    } 

    include DASHBOARDMETA;
?>
</head>
<body>

    <?php include PRELOADER; ?>

    <div id="main-wrapper">

        <?php include DASHBOARDHEADER; ?>
		<?php include SIDEBARDYNAMIC; ?>

        <div class="content-body">
            <!-- row -->
			<div class="container-fluid">
                <?php 
                        $breadCrumb = " Dashboard";
                        getBreadCrumb( trim(substr($breadCrumb, strrpos($breadCrumb, ',') + 1)), $breadCrumb);
                    ?>
				<div class="row">
                    Row
				</div>	
            </div>
        </div>

		<?php include DASHBOARDFOOTER; ?>
	</div>

	<?php include DASHBOARDSCRIPTS; ?>
</body>
</html>