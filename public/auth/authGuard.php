<?php
    session_start();
    require_once __DIR__ . '/../../config/init.php';

    use App\Classes\DB;
    use App\Classes\Logger;
    $db = new DB($pdo);
    $logger = new Logger($db);

    $message = $_SESSION['authGuard'];
    if ($message <> NULL) {
        list($index_reason, $index_dothis, $index_locked) = explode("||", $message);

        if (isset($_SESSION['rbac']['user_id'])) { // This excludes call coming from sql_authenticate as it uses $row[':)'] & session is not started yet!
                
            // Log TXN
            $logger->log(Logger::TRANSACTION, [
                'user_id'           => $_SESSION['rbac']['user_id'],
                'turnout'           => trim(strstr($index_reason, '[', true) ?: $index_reason),
                'entity'            => '',
                'entity_id'         => $_SESSION['rbac']['user_id'],
                'ipaddress'         => getClientIP(), 
                'user_agent'        => getUserAgent(),  
                'before_data'       => NULL,
                'after_data'        => NULL,
                'extra_context'     => [
                    'role'          => $_SESSION['rbac']['role'],
                    'caller'        => __FILE__,
                    'csrf_token'    => $_SESSION['rbac']['session_token'],
                    'rbac reason'   => $index_reason
                    ]
                ]);
            }

    } else {
        // As replacement for possible header already sent error.
        $index_reason = "SESSION ENDED [CODE: H_SENT]";
        $index_dothis = "Please start a new one.";
        $index_locked = "Login";
    }
?>
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">

        <!-- Tell the browser to be responsive to screen width -->
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="author" content="<?php echo AUTHOR; ?>">
        <meta name="description" content="<?php echo META_DESCRIPTION; ?>">
        <meta name="keywords" content="<?php echo META_KEYWORDS; ?>">

        <!-- Favicon icon -->
        <link rel="icon" type="image/png" sizes="16x16" href="<?php echo FAVICON; ?>">
        <title><?php echo PROJECT; ?></title>
        
        <!-- Global CSS -->
        <link rel="stylesheet" href="<?php echo BASE_URL . '/theme/css/style.css'; ?>" class="main-css" />
        <!-- page css -->
        <link href="<?php echo BASE_URL . '/dist/css/pages/error-pages.css'; ?>" rel="stylesheet">
    </head>

<body>

    <div class="fix-wrapper">
        <div class="container">
            <div class="row justify-content-center align-items-center">
                <div class="col-md-12">
                    <div class="form-input-content text-center error-page">
                        <img src="<?php echo SITE_TEXT_LOGO; ?>" alt="TextLogo" width="500">
                         <?php 
                            echo "<p><br><h3><b>" . $index_reason . "</b></h3>" . $index_dothis; 
                            if (isset($_COOKIE[COOKIE_HANDLE])) {
                                setcookie(COOKIE_HANDLE, "", time() - 3600, "/");
                            }
                            getNuked();
                        ?>
                        <br><br>
                        <a href="<?php echo BASE_URL . "/../public/index.php"; ?>" class="mt-2 btn btn-primary  <?php echo ($index_locked == 'DisableLogin' ? 'disabled' : ''); ?> ">Login</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- All Jquery -->
    <?php include DASHBOARDSCRIPTS; ?>

</body>
</html>