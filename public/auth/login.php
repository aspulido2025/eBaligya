<?php
    session_start();
    require_once __DIR__ . '/../../config/init.php';

    $errors  = $_SESSION['errors'] ?? [];
    $old = $_SESSION['oldval'] ?? [];
    unset($_SESSION['errors'], $_SESSION['old'], $_SESSION['varAttempt']);
    
    list($what, $note, $when) = explode('||', $cfg_dashboard_notice);

    include DASHBOARDMETA;
?>

</head>
 
<body>
    <?php include PRELOADER; ?>

    <div class="fix-wrapper">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-5 col-md-6">
                    <div class="card mb-0 h-auto">
                        <div class="card-body">
                            <div class="text-center mb-5">
                                <a href="javascript:void(0)" class="text-center db"><img src="<?php echo SITE_TEXT_LOGO; ?>" alt="Home" height="75"/>
                                <hr/>
                                <?php getNotice($what, $note, $when); ?>
                            </div>
                            <h4 class="text-center mb-4">Sign in your account</h4>
                            <form method="post" action="sqlLogin.php">
                                <div class="form-group mb-4 text-start">
                                    <input class="form-control" type="text" id="varUsername" placeholder="Username" name="varUsername" value="<?php echo formatValues($old['varUsername'] ?? ''); ?>" required autofocus <?php echo ($what == 'ongoing'  ? 'disabled' : ''); ?> />
                                    <small class="text-warning"><?php echo (isset($errors['varUsername']) ? formatValues($errors['varUsername']) : ''); ?></small>
                                </div>
                                <div class="mb-sm-4 mb-3 position-relative text-start">
                                    <input class="form-control" type="password" id="dlab-password" name="varPassword" placeholder="Password" required <?php echo ($what == 'ongoing'  ? 'disabled' : ''); ?> />
                                    <span class="show-pass eye">
                                        <i class="fa fa-eye-slash"></i>
                                        <i class="fa fa-eye"></i>
                                    </span>
                                    <small class="form-text text-warning"><?php echo (isset($errors['varPassword']) ? formatValues($errors['varPassword']) : ''); ?></small>
                                </div>
                                <div class="form-row d-flex flex-wrap justify-content-between mb-2">
                                    <div class="form-group mb-sm-4 mb-1">
                                        <div class="form-check custom-checkbox ms-1">
                                            <input type="checkbox" class="form-check-input" id="basic_checkbox_1" name="varRemember" value="" checked <?php echo ($what == 'ongoing'  ? 'disabled' : ''); ?>>
                                            <label class="form-check-label" for="basic_checkbox_1">Remember Me</label>
                                        </div>
                                    </div>
                                    <div class="form-group ms-2">
                                        <a class="text-hover" href="page-forgot-password.html">Forgot Password?</a>
                                    </div>
                                </div>
                                <div class="text-center">
                                    <button type="submit" class="btn btn-primary btn-block">Sign In</button>
                                </div>
                            </form>
                            <div class="new-account mt-3">


                                <br>
                    <span><?php echo "<b>" . PROJECT . ' ' . TITLE . "</b>"; ?></span>
                    <span><?php echo trim(strstr(VERSION, '+', true) ?: VERSION)."<br>"; ?></span>
                    <span><?php echo COPYRIGHT.date('Y'); ?></span>
                    <span><a href="javascript:void(0);"><?php echo AUTHOR; ?></a><br>All Rights Reserved.</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    


    
	<?php include DASHBOARDSCRIPTS; ?>
    
    
</body>