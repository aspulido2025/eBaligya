<?php 
    require_once __DIR__ . '/../../config/defaultSettings.php';
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
?>
<!DOCTYPE html>
<html lang="en">

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
                                echo "<p><br><h3><b>SYSTEM IS TEMPORARILY UNAVAILABLE!</b></h3>Please try again later.<br> If the issue persist, please contact the System Administrator." ; 
                                
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