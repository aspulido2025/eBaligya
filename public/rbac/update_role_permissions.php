<?php 
    // Initialize
    require __DIR__ . '/../../config/init.php';

    // Gateway
    include MIDDLEWARE;    

    // Classes
    use App\Classes\RBAC;
    use App\Classes\URLKeeper;
    $rbac = new RBAC($db, $_SESSION['rbac']['user_id']); 

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

        // Fetch all permissions
        $permissions = $db->fetch("SELECT id, module, action,  description FROM rbac_permissions ORDER BY operation ASC", [], [], 'all'); // operation = name

        // Fetch current role-permissions
        $rolePerms = $db->fetch("SELECT permission_id FROM rbac_role_permissions WHERE role_id = ?", [$role_id], [], 'all');
        $currentPerms = array_column($rolePerms, 'permission_id');
    }

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
                    $breadCrumb = "Administration, RBAC, Update Role Permissions";
                    getBreadCrumb( trim(substr($breadCrumb, strrpos($breadCrumb, ',') + 1)), $breadCrumb);
                ?>
                <div class="row">
                    <div class="col-lg-12 col-xlg-12 col-md-12">
                        <div class="card">
                            <div class="card-header bg-success d-flex align-items-center">
                                <!-- <button onClick="history.go(0);" class="btn btn-sm btn-primary me-2"><i class="ti-reload"></i> Refresh</button> -->
                                
                                <?php
                                    // CREATE ROLE PROFILE
                                    $link = BASE_URL . '/rbac/create_role_permissions.php?token=' . urlencode(URLKeeper::encode('', ENCRYPTION_PASSKEY, EXPIRY_ENCRYPTION));
                                    // if ($rbac->can('role_permission_management:create')) { ?>  
                                        <a title='Create Role Permission' href="<?php echo $link; ?>" class="btn btn-sm btn-primary me-2">
                                            <i class="fa fa-plus-circle"></i> Create New Role Permission</a>
                                <?php // } ?>

                                <a href="#bottom" class="btn btn-sm btn-primary ms-auto"><i class="ti-arrow-down"></i> Bottom</a>
                            </div>
                            <div class="card-body">
                                <h5>Manage Permissions for Role: <u><b><?= $role['access'] ?></b></u></h5>
                                <br>
                                <form method="post" action="sql_update_role_permissions.php">
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th width="5%">✔</th>
                                                <th>Module</th>
                                                <th>Action</th>
                                                <th>Description</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($permissions as $perm): ?>
                                                <tr>
                                                    <td>
                                                        <input type="checkbox" 
                                                            name="permissions[]" 
                                                            value="<?= $perm['id'] ?>"
                                                            <?= in_array($perm['id'], $currentPerms) ? 'checked' : '' ?>>
                                                    </td>
                                                    <td><?= formatValues($perm['module']) ?></td>
                                                    <td><?= formatValues($perm['action']) ?></td>
                                                    <td><?= formatValues($perm['description']) ?></td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                    <br><br>
                                    <hr>
                                    <input type="hidden" name="id" value="<?php echo $role_id; ?>" />
                                    <input type="hidden" name="access" value="<?php echo $role['access']; ?>" />
                                    <button type="submit" name='submit' class="btn btn-primary"><i class="ti-save"></i> Save</button>
                                    <a href="<?php echo BASE_URL . '/rbac/tbl_role_management.php';  ?>" class="btn btn-secondary"><i class="ti-close"></i> Cancel</a>
                                    <br><br><br>
                                </form>
                            </div>
                            
                            <div class="card-footer bg-success" id="bottom">
                                <?php 
                                    latest_modification("update_role_permissions.php");
                                ?>
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