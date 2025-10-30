<?php
    // Initialize
    require __DIR__ . '/../../config/init.php';

    // Gateway
    include MIDDLEWARE;    
    
    // Classes
    use App\Classes\URLKeeper;

    // Token
    if (isset($_GET['token'])) {
        $timeBound = URLKeeper::decode('token' ?? null, ENCRYPTION_PASSKEY, EXPIRY_ENCRYPTION);
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
                    $breadCrumb = "Administration, RBAC, Create New Role Permissions";
                    getBreadCrumb( trim(substr($breadCrumb, strrpos($breadCrumb, ',') + 1)), $breadCrumb);
                ?>
                <div class="row">
                    <div class="col-lg-6 col-xlg-6 col-md-6">
                        <!-- Batch Create Role Permissions Form -->
                        <div class="card">
                            <div class="card-header bg-success">
                                <h5 class="text-white">Create New Role Permissions</h5>
                            </div>
                            <div class="card-body">
                                <!-- Notice the action points to sql_create_role_permissions.php -->
                                <form method="POST" action="sql_create_role_permissions.php">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="module" class="form-label">Module<span class="text-danger">*</span></label>
                                                <input type="text" name="module" id="module" class="form-control" required>
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="form-label">Actions<span class="text-danger">*</span></label>
                                                <div id="actions-wrapper">
                                                    <input type="text" name="actions[]" class="form-control mb-2" placeholder="e.g. view" required>
                                                </div>
                                                <button type="button" class="btn btn-sm btn-outline-primary" onclick="addActionField()">+ Add Action</button>
                                            </div>
                                        </div>

                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label for="description" class="form-label">Description (optional)</label>
                                                <textarea name="description" id="description" rows="4" class="form-control"></textarea>
                                            </div>
                                        </div>
                                    </div>
                                    <br><br>
                                    <span class="text-danger">*</span>&nbsp;<small class="text-muted">Indicates a <code>REQUIRED</code> field.</small>
                                    <hr>
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

<script>
function addActionField() {
    let wrapper = document.getElementById('actions-wrapper');
    let input = document.createElement('input');
    input.type = 'text';
    input.name = 'actions[]';
    input.className = 'form-control mb-2';
    input.placeholder = 'e.g. edit';
    wrapper.appendChild(input);
}
</script>
