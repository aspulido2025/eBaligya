<?php
    // Initialize
    require __DIR__ . '/../../config/init.php';

    // Gateway
    include MIDDLEWARE;    

    // Classes 
    use App\Classes\URLKeeper;

    // Token
    if (isset($_GET['token'])) {
        $token = URLKeeper::decode('token' ?? null, ENCRYPTION_PASSKEY, EXPIRY_ENCRYPTION);
    }

    // Get/Initiate variables.
    if ( $_SERVER['REQUEST_METHOD'] === 'GET' ) {

        $role_id = (int)$token;

        // Fetch role
        $role = $db->fetch("SELECT * FROM rbac_roles WHERE id = ?", [$role_id], [], 'row');
        if (!$role) {
            die("Role not found.");
        }
    
        $varRoleName = $role['access'];
        $varDescription = $role['description'];

    }

    // Session validation variables
    $errors = $_SESSION['errors'] ?? [];
    $oldval = $_SESSION['oldval'] ?? [];
    unset($_SESSION['errors'], $_SESSION['oldval']);

    // Theme files
    include DASHBOARDMETA;
?>
</head>
    <body>
    <?php include PRELOADER; ?>
    <div id="main-wrapper">
        <?php include DASHBOARDHEADER; ?>
        <?php include SIDEBARDYNAMIC; ?>

        <div class="content-body">
            <div class="container-fluid">
                <?php 
                    $breadCrumb = "Administration, RBAC, Update Role";
                    getBreadCrumb( trim(substr($breadCrumb, strrpos($breadCrumb, ',') + 1)), $breadCrumb);
                ?>
                <div class="row">
                    <div class="col-lg-6 col-xlg-6 col-md-6">
                        <div class="card">
                            <div class="card-header bg-success">
                                <h5 class="text-white">Update Role Profile: <u><?= $varRoleName; ?></u></h5>
                            </div>
                            <div class="card-body">
                                <form method="post" action="sql_update_role.php">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label class="form-label" for="varRoleName">Role Name<span class="text-danger">*</span></label>
                                                <input type="text" class="form-control" name="varRoleName" id="varRoleName" value="<?php echo formatValues($oldval['varRoleName'] ?? $varRoleName); ?>" required autofocus /> 
                                                <small class="form-text text-warning"><?php echo (isset($errors['varRoleName']) ? formatValues($errors['varRoleName']) : '')  ; ?></small>
                                            </div>
                                        </div>
                                        <div class="col-md-9">
                                            <div class="form-group">
                                                <label class="form-label" for="varDescription">Description<span class="text-danger">*</span></label>
                                                <textarea name="varDescription" rows="4" class="form-control"><?= $varDescription ?></textarea>
                                            </div>
                                        </div>
                                    </div>
                                    <br><br>
                                    <span class="text-danger">*</span>&nbsp;<small class="text-muted">Indicates a <code>REQUIRED</code> field.</small>
                                    <hr>

                                    <input type="hidden" name="id" value="<?php echo $role_id; ?>" />
                                    <button type="submit" name='submit' class="btn btn-primary"><i class="ti-save"></i> Save</button>
                                    <a href="<?php echo BASE_URL . '/rbac/tbl_role_management.php';  ?>" class="btn btn-secondary"><i class="ti-close"></i> Cancel</a>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php include DASHBOARDFOOTER; ?>
    </div>        
    <?php include DASHBOARDSCRIPTS; ?>
    </body>
</html>