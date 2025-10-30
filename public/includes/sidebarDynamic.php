<?php ?>
        <!--**********************************
            Sidebar start
        ***********************************-->
		<div class="dlabnav bg-secondary">
            <br>
            <div class="dlabnav-scroll">
				<div class="dropdown header-profile2 ">
					<a class="nav-link " href="javascript:void(0);"  role="button" data-bs-toggle="dropdown">
						<div class="header-info2 d-flex align-items-center">
                            
                                    <img src="<?php echo FOLDER_USER_PHOTOS . $_SESSION['rbac']['photo']; ?>"  onerror="this.onerror=null; this.src='<?php echo DEFAULT_AVATAR; ?>'" />
							<div class="d-flex align-items-center sidebar-info">
								<div class="nav-link">
									<span class="nav-link font-w400"><?php echo $_SESSION['rbac']['fullname']; ?></span>
									<small class="text-end font-w400"><?php echo $_SESSION['rbac']['role']; ?></small>
								</div>	
								<i class="fas fa-chevron-down"></i>
							</div>
							
						</div>
					</a>
					<div class="dropdown-menu dropdown-menu-end">
						<a href="<?php echo BASE_URL . '/forms/userProfile.php'; ?>" class="dropdown-item ai-icon ">
							<i class="fa fa-user"></i>
							<span class="ms-2">Profile </span>
						</a>
						<a href="<?php echo BASE_URL . '/auth/logout.php'; ?>" class="dropdown-item ai-icon">
							<i class="fa-solid fa-right-from-bracket"></i>
							<span class="ms-2">Logout </span>
						</a>
					</div>
				</div>
				<ul class="metismenu" id="menu">
                    <li><a href="<?php echo BASE_URL; ?>" aria-expanded="false">
							<i class="flaticon-025-dashboard"></i>
							<span class="nav-text">Dashboard</span>
						</a>
                    </li>

                    <li><a class="has-arrow " href="javascript:void(0);" aria-expanded="false">
							<i class="flaticon-022-copy"></i>
							<span class="nav-text">Shop</span>
						</a>
                        <ul aria-expanded="false">
                            <li><a href="<?php echo BASE_URL . '/tables/shop_products.php'; ?>">Products</a></li>
                        </ul>
                    </li>
                    <li><a class="has-arrow " href="javascript:void(0);" aria-expanded="false">
							<i class="flaticon-022-copy"></i>
							<span class="nav-text">Administration</span>
						</a>
                        <ul aria-expanded="false">
                            <li><a href="<?php echo BASE_URL . '/tables/sys_vendors.php'; ?>">Vendors</a></li>
                            <li><a href="<?php echo BASE_URL . '/tables/sys_user_accounts.php'; ?>">System Users</a></li>
                            <li><a href="<?php echo BASE_URL . '/rbac/tbl_role_management.php'; ?>">Role Management</a></li>
                            <li><a class="has-arrow" href="javascript:void(0);" aria-expanded="false">System Logs</a>
                                <ul aria-expanded="false">
                                    <li><a href="<?php echo BASE_URL . '/tables/log_error.php'; ?>">Error Logs</a></li>
                                    <li><a href="<?php echo BASE_URL . '/tables/log_cookie.php'; ?>">Cookie Logs</a></li>
                                    <li><a href="<?php echo BASE_URL . '/tables/log_email.php'; ?>">eMail Logs</a></li>
                                    <li><a href="<?php echo BASE_URL . '/tables/log_sms.php'; ?>">SMS Logs</a></li>
                                    <li><a href="<?php echo BASE_URL . '/tables/log_transaction.php'; ?>">Transaction Logs</a></li>
                                    <li><a href="<?php echo BASE_URL . '/tables/log_master_index.php'; ?>">Master Index</a></li>
                                </ul>
                            </li>
                        </ul>
                    </li>

                   
                </ul>
			</div>
        </div>
        <!--**********************************
            Sidebar end
        ***********************************-->