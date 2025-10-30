<?php 
    // Initialize
    require __DIR__ . '/../../config/init.php';
    
    // Session Gateway
    include MIDDLEWARE;
    
    // RBAC Matrix Layer (Will replace the Passed Token below)
    // blackBox('role_management', 'view', $rbac, $db); 

    // RBAC
    use App\Classes\DB;
    $db = new DB($pdo);

    // Simple escaper
    if (!function_exists('e')) {
        function e($v) {
            return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8');
        }
    }

    // Value Resolver
    $wizProfile = $_SESSION['wizard']['profile'] ?? [];
    $rbac       = $_SESSION['rbac'] ?? [];

    // Explicit function instead of closure
    function val($key, $wizProfile, $rbac) {
        return $wizProfile[$key] ?? ($rbac[$key] ?? '');
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
                        $breadCrumb = "Account, User Profile";
                        getBreadCrumb( trim(substr($breadCrumb, strrpos($breadCrumb, ',') + 1)), $breadCrumb);
                    ?>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-body text-center p-4">
									<div class="mx-auto d-inline-block position-relative mb-3 mt-3">
                                        <img src="<?php echo FOLDER_USER_PHOTOS . $_SESSION['rbac']['photo']; ?>"  class="rounded-circle avatar avatar-lg" width="120" onerror="this.onerror=null; this.src='<?php echo DEFAULT_AVATAR; ?>'" />
										<span class="fa fa-circle bored border-light text-success position-absolute bottom-0 end-0 mb-0 me-1 fs-12"></span>
									</div>
									<div class="media-body mb-4">
										<h4 class="mb-0">
											<a href="javascript:void(0);" class="text-black"><?php echo $_SESSION['rbac']['fullname']; ?></a>
										</h4>
										<p class="mb-0 fs-15"><?php echo $_SESSION['rbac']['role']; ?></p>
									</div>
                                    <hr><br>
									<div class="d-flex justify-content-center px-3">
										<div class="bg-light rounded px-3 py-2 text-start mx-2 flex-grow-1">
											<h6 class="fs-15 mb-0"><?php echo mobile_number_format($_SESSION['rbac']['mobile_number'],'showMobile'); ?></i></h6>
											<span class="fs-14">Mobile Number</span><br>
                                            <small>
                                                <?php echo ($_SESSION['rbac']['verified_mobile'] == 1 ? 
                                                    "<i class='ti-check text-primary'></i> Verified"  :  "<i class='ti-close text-danger'></i> Not Verified" ); ?> 
                                            </small>
										</div>
										<div class="bg-light rounded px-3 py-2 text-start mx-2 flex-grow-1">
											<h6 class="fs-15 mb-0"><?php echo $_SESSION['rbac']['email_address']; ?></h6>
											<span class="fs-14">eMail Address</span><br>
                                            <small>
                                                <?php echo ($_SESSION['rbac']['verified_email'] == 1 ? 
                                                    "<i class='ti-check text-primary'></i> Verified"  :  "<i class='ti-close text-danger'></i> Not Verified" ); ?> 
                                            </small>
										</div>
									</div>
								</div>
                            </div>  
                        </div>
                        <div class="col-md-8">
                            <div class="card">
                                <div class="card-body">
                                    <h3 class="mb-4">User Profile</h3>

                                    <!-- Progress Bar -->
                                    <div class="progress mb-3" style="height: 6px;">
                                        <div id="wizardProgress" class="progress-bar bg-primary" role="progressbar" style="width: 0%;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>


                                    <!-- Tab headers -->
                                    <ul class="nav nav-tabs" id="wizardTabs" role="tablist">
                                        <li class="nav-item" role="presentation">
                                            <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#step1" type="button">Profile</button>
                                        </li>
                                        <li class="nav-item" role="presentation">
                                            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#step2" type="button">Mobile Number</button>
                                        </li>
                                        <li class="nav-item" role="presentation">
                                            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#step3" type="button">Email Address</button>
                                        </li>
                                        <li class="nav-item" role="presentation">
                                            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#step4" type="button">Photo</button>
                                        </li>
                                        <li class="nav-item" role="presentation">
                                            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#step5" type="button">Password</button>
                                        </li>
                                        <li class="nav-item" role="presentation">
                                            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#step6" type="button">Privacy Policy</button>
                                        </li>
                                    </ul>

                                    <!-- Tab content -->
                                    <form id="wizardForm" class="tab-content border border-top-0 p-4">
                                        
                                        <!-- Step 1 -->
                                        <div class="tab-pane fade show active" id="step1">
                                            <h5 class='text-primary'>Profile</h5><br>
                                            <div class="mb-3"><label class="form-label" for="fullname">Fullname</label><span class="text-danger">*</span>
                                                <input type="text" class="form-control" id="fullname" name="fullname" value="<?= e(val('fullname', $wizProfile, $rbac)) ?>" autofocus required>
                                            </div>
                                            <div class="mb-3"><label class="form-label" for="username">Username</label><span class="text-danger">*</span>
                                                <input type="text" class="form-control" id="username" name="username" autocomplete="username" value="<?= e(val('username', $wizProfile, $rbac)) ?>" required>
                                            </div>
                                            <div class="mb-3"><label class="form-label" for="birthday">Date of Birth</label>
                                                <input type="date" class="form-control" id="birthday" name="birthday" value="<?= e(val('birthday', $wizProfile, $rbac)) ?>">
                                            </div>
                                            <br><br>
                                            <span class="text-danger">*</span>&nbsp;<small class="text-muted">Indicates a <code>REQUIRED</code> field.</small>
                                            <hr>
                                        </div>

                                        <!-- Step 2 -->
                                        <div class="tab-pane fade" id="step2">
                                            <h5 class='text-primary'>Mobile Number</h5><br>
                                            <div class="mb-3"><label class="form-label" for="mobile_number">Mobile Number</label><span class="text-danger">*</span>
                                                <input type="text" class="form-control" 
                                                    id="mobile_number" name="mobile_number" 
                                                    value="<?= e(val('mobile_number', $wizProfile, $rbac)) ?>" 
                                                    data-original="<?= e($rbac['mobile_number'] ?? '') ?>">
                                            </div>
                                            <button type="button" class="btn btn-secondary btn-sm mb-3">Send OTP</button>
                                            <div class="mb-3"><label class="form-label" for="otp">OTP</label><span class="text-danger">*</span>
                                                <input type="text" class="form-control" id="otp" name="otp">
                                            </div>
                                            <br><br>
                                            <span class="text-danger">*</span>&nbsp;<small class="text-muted">Indicates a <code>REQUIRED</code> field.</small>
                                            <hr>
                                        </div>

                                        <!-- Step 3 -->
                                        <div class="tab-pane fade" id="step3">
                                            <h5 class='text-primary'>Email Address</h5><br>
                                            <div class="mb-3"><label class="form-label" for="email_address">Email Address</label><span class="text-danger">*</span>
                                                <input type="email" class="form-control" 
                                                    id="email_address" name="email_address" 
                                                    value="<?= e(val('email_address', $wizProfile, $rbac)) ?>"
                                                    data-original="<?= e($rbac['email_address'] ?? '') ?>">
                                            </div>
                                            <button type="button" class="btn btn-secondary btn-sm mb-3">Send Verification Code</button>
                                            <div class="mb-3"><label class="form-label" for="verification_code">Verification Code</label><span class="text-danger">*</span>
                                                <input type="text" class="form-control" id="verification_code" name="verification_code">
                                            </div>
                                            <br><br>
                                            <span class="text-danger">*</span>&nbsp;<small class="text-muted">Indicates a <code>REQUIRED</code> field.</small>
                                            <hr>
                                        </div>

                                        <!-- Step 4 -->
                                        <div class="tab-pane fade" id="step4">
                                            <h5 class='text-primary'>Upload Photo</h5><br>
                                            <div class="mb-3">
                                                <input type="file" class="form-control" 
                                                    name="photo"
                                                    accept=".jpg,.jpeg,.png,.bmp"
                                                    data-original="<?= e($rbac['photo'] ?? 'default_avatar.png') ?>">
                                            </div>
                                            <br><br>
                                            <hr>
                                        </div>

                                        <!-- Step 5 -->
                                        <div class="tab-pane fade" id="step5">
                                            <h5 class='text-primary'>Change Password</h5><br>
                                            <div class="mb-3"><label class="form-label" for="current_password">Current Password</label><span class="text-danger">*</span>
                                                <div class="input-group">
                                                    <input type="password" class="form-control" id="current_password" name="current_password">
                                                    <button class="btn btn-outline-primary toggle-password" type="button">
                                                        <i class="ti fa fa-eye"></i>
                                                    </button>
                                                </div>
                                            </div>
                                            <div class="mb-3"><label class="form-label" for="new_password">New Password</label><span class="text-danger">*</span>
                                                <div class="input-group">
                                                    <input type="password" class="form-control" id="new_password" name="new_password">
                                                    <button class="btn btn-outline-primary toggle-password" type="button">
                                                        <i class="ti fa fa-eye"></i>
                                                    </button>
                                                </div>
                                            </div>
                                            <div class="mb-3"><label class="form-label" for="confirm_password">Confirm Password</label><span class="text-danger">*</span>
                                                <div class="input-group">
                                                    <input type="password" class="form-control" id="confirm_password" name="confirm_password">
                                                    <button class="btn btn-outline-primary toggle-password" type="button">
                                                        <i class="ti fa fa-eye"></i>
                                                    </button>
                                                </div>
                                            </div>
                                            <br><br>
                                            <span class="text-danger">*</span>&nbsp;<small class="text-muted">Indicates a <code>REQUIRED</code> field.</small>
                                            <hr>
                                        </div>

                                        <!-- Step 6 -->
                                        <div class="tab-pane fade" id="step6">
                                            <h5 class='text-primary'>Privacy Policy</h5><br>
                                            <?php 
                                                include(DOC_PRIVACYPROLICY);
                                             ?>
                                            <br><br><br>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" id="privacyCheck" name="privacy_accepted"
                                                        value="1"
                                                        <?= (
                                                            // 1. Wizard cache if available
                                                            (isset($_SESSION['wizard']['privacy']['privacy_accepted']) && $_SESSION['wizard']['privacy']['privacy_accepted'] == 1)
                                                            // 2. Else RBAC session value (which reflects DB `is_legal_privacy`)
                                                            || (!isset($_SESSION['wizard']['privacy']['privacy_accepted']) && ($rbac['privacy_accepted'] ?? 1) == 1)
                                                            ) ? 'checked' : '' ?>>
                                                <label class="form-check-label" for="privacyCheck">I accept the Privacy Policy</label>
                                            </div>

                                            <br><br>
                                            <hr>
                                        </div>

                                        <!-- Nav buttons -->
                                        <div class="d-flex justify-content-between mt-2">
                                            <!-- <button type="button" id="prevBtn" class="btn btn-outline-secondary">Prev</button> -->
                                            <button type="button" id="prevBtn" class="btn btn-primary">Prev</button>
                                            <button type="button" id="nextBtn" class="btn btn-primary">Next</button>
                                        </div>
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

        <script>
            const tabElList = [].slice.call(document.querySelectorAll('#wizardTabs button'));
            let currentTab = 0;

            function updateProgress() {
                const progress = ((currentTab + 1) / tabElList.length) * 100;
                const progressBar = document.getElementById('wizardProgress');
                progressBar.style.width = progress + "%";
                progressBar.setAttribute('aria-valuenow', progress);
            }

            function showTab(index) {
                new bootstrap.Tab(tabElList[index]).show();
                document.getElementById('prevBtn').style.display = index === 0 ? 'none' : 'inline-block';
                document.getElementById('nextBtn').textContent = index === tabElList.length - 1 ? 'Submit' : 'Next';
                updateProgress();
            }

            // ✅ Step validation (basic required fields)
            function validateCurrentStep() {                                                                               
                const stepPane = document.querySelector(tabElList[currentTab].dataset.bsTarget);
                const inputs = stepPane.querySelectorAll('input, select, textarea');
                let ok = true, firstBad = null;

                inputs.forEach(inp => {
                    inp.classList.remove('is-invalid');
                    if (inp.hasAttribute('required') && !String(inp.value || '').trim()) {
                        inp.classList.add('is-invalid');
                        ok = false;
                        if (!firstBad) firstBad = inp;
                    }
                });

                if (!ok) {
                    if (firstBad) firstBad.focus();
                    if (window.toastr) toastr.error("Please fill all required fields.","Error");                                   
                }
                return ok;
            }

                // Step 1: Profile AJAX save
                async function saveProfileStep() {
                    const pane = document.querySelector('#step1');
                    const payload = {
                        fullname: pane.querySelector('[name="fullname"]').value,
                        username: pane.querySelector('[name="username"]').value,
                        birthday: pane.querySelector('[name="birthday"]').value
                    };

                    const res = await fetch('ajax/userProfileStepSave.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                        credentials: 'same-origin',   // 👈 ensures PHP session cookie is sent
                        body: new URLSearchParams({
                            step: 'profile',
                            data: JSON.stringify(payload)
                        })
                    });
                    const json = await res.json().catch(() => ({}));
                    if (!res.ok || json.status !== 'success') {
                        throw new Error(json.message || 'Failed to save Step: Profile.');                                         
                    }
                    if (window.toastr) toastr.success("Step: Profile, saved.", "Success"); 
                }

                // Step 2: Mobile Number AJAX save
                async function saveMobileStep() {
                    const pane = document.querySelector('#step2');
                    const payload = {
                        mobile: pane.querySelector('[name="mobile_number"]').value,
                        otp: pane.querySelector('[name="otp"]').value
                    };
                    // console.log("Mobile payload:", payload);
                    const res = await fetch('ajax/userProfileStepSave.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                        credentials: 'same-origin',
                        body: new URLSearchParams({
                            step: 'mobile',
                            data: JSON.stringify(payload)
                        })
                    });
                    const json = await res.json().catch(() => ({}));
                    if (!res.ok || json.status !== 'success') {
                        throw new Error(json.message || 'Failed to save Step: Mobile Number.');
                    }
                    if (window.toastr) toastr.success("Step: Mobile Number, saved.", "Success"); 
                }

                // Step 3: eMail Address AJAX save
                async function saveEmailStep() {
                    const pane = document.querySelector('#step3');
                    const payload = {
                        email_address: pane.querySelector('[name="email_address"]').value,
                        verification_code: pane.querySelector('[name="verification_code"]').value
                    };
                    const res = await fetch('ajax/userProfileStepSave.php', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                            credentials: 'same-origin',
                            body: new URLSearchParams({
                            step: 'email',
                            data: JSON.stringify(payload)
                        })
                    });
                    const json = await res.json().catch(() => ({}));
                    if (!res.ok || json.status !== 'success') {
                        throw new Error(json.message || 'Failed to save Step: Email Address.');
                    }
                    if (window.toastr) toastr.success("Step: Email Address, saved.", "Success");
                }

                // Step 4: Photo
                async function savePhotoStep() {
                    const pane = document.querySelector('#step4');
                    const fileInput = pane.querySelector('[name="photo"]');

                    // if (!fileInput.files.length) {
                    //     throw new Error("Please choose a photo before continuing."); 
                    // }

                    const formData = new FormData();
                    formData.append('step', 'photo');
                    formData.append('photo', fileInput.files[0]);

                    const res = await fetch('ajax/userProfileStepSave.php', {
                        method: 'POST',
                        credentials: 'same-origin',
                        body: formData
                    });

                    const json = await res.json().catch(() => ({}));
                    if (!res.ok || json.status !== 'success') {
                        throw new Error(json.message || 'Failed to save Step: Photo.');
                    }
                    if (window.toastr) toastr.success("Step: Photo, saved.", "Success");
                }

                // Step 5: Password
                async function savePasswordStep() {
                    const pane = document.querySelector('#step5');
                    const payload = {
                        current_password: pane.querySelector('[name="current_password"]').value,
                        new_password:     pane.querySelector('[name="new_password"]').value,
                        confirm_password: pane.querySelector('[name="confirm_password"]').value
                    };

                    const res = await fetch('ajax/userProfileStepSave.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                        credentials: 'same-origin',
                        body: new URLSearchParams({
                        step: 'password',
                        data: JSON.stringify(payload)
                        })
                    });

                    const json = await res.json().catch(() => ({}));
                    if (!res.ok || json.status !== 'success') {
                        throw new Error(json.message || 'Failed to save Step: Password.');
                    }
                    if (window.toastr) toastr.success("Step: Password, saved.", "Success"); 
                }

                // Step 6: Privacy
                async function savePrivacyStep() {
                    const pane = document.querySelector('#step6');
                    const checkbox = pane.querySelector('[name="privacy_accepted"]');

                    const payload = { privacy_accepted: checkbox.checked ? '1' : '0' }; // 👈 match PHP + HTML

                    const res = await fetch('ajax/userProfileStepSave.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                        credentials: 'same-origin',
                        body: new URLSearchParams({
                            step: 'privacy',
                            data: JSON.stringify(payload)
                        })
                    });

                    const json = await res.json().catch(() => ({}));
                    if (!res.ok || json.status !== 'success') {
                        throw new Error(json.message || 'Failed to save Step: Privacy Policy.');
                    }
                    if (window.toastr) toastr.success("Step: Privacy Policy, accepted.", "Success");
                }


            // Send OTP
            document.querySelector('#step2 button').addEventListener('click', async () => {
                const mobile = document.querySelector('[name="mobile_number"]').value;

                const res = await fetch('ajax/userProfileStepSave.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    credentials: 'same-origin',
                    body: new URLSearchParams({
                        step: 'send_otp',
                        data: JSON.stringify({ mobile })
                    })
                });

                const json = await res.json().catch(() => ({}));
                if (!res.ok || json.status !== 'success') {
                    if (window.toastr) toastr.error(json.message || 'Failed to send OTP', 'Error');
                } else {
                    if (window.toastr) toastr.success(json.message, 'Success');
                }
            });


            // Send Verification Code
            document.querySelector('#step3 button').addEventListener('click', async () => {
                const email = document.querySelector('[name="email_address"]').value;

                const res = await fetch('ajax/userProfileStepSave.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    credentials: 'same-origin',
                    body: new URLSearchParams({
                        step: 'send_email_code',
                        data: JSON.stringify({ email_address: email })
                    })
                });

                const json = await res.json().catch(() => ({}));
                if (!res.ok || json.status !== 'success') {
                    if (window.toastr) toastr.error(json.message || 'Failed to send code', 'Error');
                } else {
                    if (window.toastr) toastr.success(json.message, 'Success');
                }
            });


            // Prev
            document.getElementById('prevBtn').addEventListener('click', () => {
                if (currentTab > 0) {
                    currentTab--;
                    showTab(currentTab);
                }
            });

            
            // Next / Submit
            document.getElementById('nextBtn').addEventListener('click', async () => {
                if (!validateCurrentStep()) return;

                try {
                    if (currentTab === 0) {         //      Step 1: Profile
                        await saveProfileStep();
                    } else if (currentTab === 1) {  //      Step 2: Mobile
                        await saveMobileStep();
                    } else if (currentTab === 2) {  //      Step 3: Email
                        await saveEmailStep();
                    } else if (currentTab === 3) {  //      Step 4: Photo
                        await savePhotoStep();
                    } else if (currentTab === 4) {  //      Step 5: Password
                        await savePasswordStep();
                    } else if (currentTab === 5) { // Step 6: Privacy
                        await savePrivacyStep();
                    } 
                    
                    if (currentTab < tabElList.length - 1) {
                            currentTab++;
                            showTab(currentTab);
                        } else {
                            // Final submit ONLY here
                            const res = await fetch('ajax/userProfileStepSave.php', {
                                method: 'POST',
                                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                                credentials: 'same-origin',
                                body: new URLSearchParams({ step: 'final_submit' })
                            });

                            const json = await res.json().catch(() => ({}));
                            if (!res.ok || json.status !== 'success') {
                                throw new Error(json.message || 'Final save failed');
                            }
                            if (window.toastr) {
                                toastr.success("Profile updated successfully!", "Success");
                                setTimeout(function () {
                                    window.location.href ="<?= BASE_URL ?>/index.php";
                                }, 500); // small delay so user sees the toast
                            }
                        }
                } catch (err) {
                    if (window.toastr) toastr.error(err.message,'Error');                                                               
                        
                }
            });

            //
            //
            // SEND OTP & NEXT BUTTON LOGIC
                // document.addEventListener("DOMContentLoaded", () => {
                //     const mobileInput = document.querySelector('#step2 [name="mobile_number"]');
                //     const otpInput = document.querySelector('#step2 [name="otp"]');
                //     const sendOtpBtn = document.querySelector('#step2 button'); // Send OTP button
                //     const nextBtn = document.getElementById('nextBtn');

                //     // Helper: validate mobile format
                //     function isValidMobile(val) {
                //         return /^\+639\d{9}$/.test(val);
                //     }

                //     // Validate mobile number input
                //     if (mobileInput && sendOtpBtn) {
                //         mobileInput.addEventListener('input', () => {
                //             const valid = isValidMobile(mobileInput.value.trim());
                //             sendOtpBtn.disabled = !valid;
                //         });
                //     }

                //     // Validate OTP input (enable Next only if 6 digits)
                //     if (otpInput) {
                //         otpInput.addEventListener('input', () => {
                //             if (currentTab === 1) { // Step 2
                //                 nextBtn.disabled = !/^\d{6}$/.test(otpInput.value.trim());
                //             }
                //         });
                //     }

                //     // Tab switching: adjust button states
                //     document.getElementById('wizardTabs').addEventListener('shown.bs.tab', (event) => {
                //         if (currentTab === 1) {
                //             // On Mobile step
                //             const valid = isValidMobile(mobileInput.value.trim());
                //             sendOtpBtn.disabled = !valid;   // enable immediately if valid
                //             nextBtn.disabled = true;        // still lock Next until OTP entered
                //         } else {
                //             // On all other steps
                //             if (sendOtpBtn) sendOtpBtn.disabled = true; // prevent accidental clicks outside step2
                //             nextBtn.disabled = false;
                //         }
                //     });
                // });

            
            // SEND VERIFICATION CODE & NEXT BUTTON LOGIC
                // document.addEventListener("DOMContentLoaded", () => {
                //     const emailInput   = document.querySelector('#step3 [name="email_address"]');
                //     const codeInput    = document.querySelector('#step3 [name="verification_code"]');
                //     const sendCodeBtn  = document.querySelector('#step3 button'); // Send Verification Code button
                //     const nextBtn      = document.getElementById('nextBtn');

                //     // Helper: validate email format
                //     function isValidEmail(val) {
                //         return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(val);
                //     }

                //     // Disable send code by default
                //     if (sendCodeBtn) sendCodeBtn.disabled = true;

                //     // Validate email input
                //     if (emailInput) {
                //         emailInput.addEventListener('input', () => {
                //             const valid = isValidEmail(emailInput.value.trim());
                //             sendCodeBtn.disabled = !valid;
                //         });
                //     }

                //     // Validate code input (6-digit numeric)
                //     if (codeInput) {
                //         codeInput.addEventListener('input', () => {
                //             if (currentTab === 2) { // Step 3 index
                //                 nextBtn.disabled = !/^\d{6}$/.test(codeInput.value.trim());
                //             }
                //         });
                //     }

                //     // Tab switching: adjust button states
                //     document.getElementById('wizardTabs').addEventListener('shown.bs.tab', (event) => {
                //         if (currentTab === 2) {
                //             // On Email step
                //             const valid = isValidEmail(emailInput.value.trim());
                //             sendCodeBtn.disabled = !valid;  // enable immediately if valid
                //             nextBtn.disabled = true;        // lock until code typed
                //         } else {
                //             if (sendCodeBtn) sendCodeBtn.disabled = true; // disable outside step3
                //             nextBtn.disabled = false;
                //         }
                //     });
                // });


            //    
            //
            
            // SEND OTP & NEXT BUTTON LOGIC
            document.addEventListener("DOMContentLoaded", () => {
                const mobileInput = document.querySelector('#step2 [name="mobile_number"]');
                const otpInput    = document.querySelector('#step2 [name="otp"]');
                const sendOtpBtn  = document.querySelector('#step2 button'); 
                const nextBtn     = document.getElementById('nextBtn');

                const original = mobileInput.dataset.original;

                function isValidMobile(val) {
                    return /^\+639\d{9}$/.test(val);
                }

                function refreshMobileControls() {
                    const current = mobileInput.value.trim();
                    const changed = current !== original;

                    if (!changed) {
                        // No change → skip OTP
                        sendOtpBtn.disabled = true;
                        nextBtn.disabled = false;
                    } else {
                        // Changed → require OTP
                        const validMobile = isValidMobile(current);
                        sendOtpBtn.disabled = !validMobile;
                        nextBtn.disabled = !/^\d{6}$/.test(otpInput.value.trim());
                    }
                }

                if (mobileInput) mobileInput.addEventListener('input', refreshMobileControls);
                if (otpInput) otpInput.addEventListener('input', refreshMobileControls);

                document.getElementById('wizardTabs').addEventListener('shown.bs.tab', () => {
                    if (currentTab === 1) refreshMobileControls();
                });
            });

            
            // SEND VERIFICATION CODE & NEXT BUTTON LOGIC
            document.addEventListener("DOMContentLoaded", () => {
                const emailInput  = document.querySelector('#step3 [name="email_address"]');
                const codeInput   = document.querySelector('#step3 [name="verification_code"]');
                const sendCodeBtn = document.querySelector('#step3 button');
                const nextBtn     = document.getElementById('nextBtn');

                const original = emailInput.dataset.original;

                function isValidEmail(val) {
                    return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(val);
                }

                function refreshEmailControls() {
                    const current = emailInput.value.trim();
                    const changed = current !== original;

                    if (!changed) {
                        // No change → skip VCODE
                        sendCodeBtn.disabled = true;
                        nextBtn.disabled = false;
                    } else {
                        // Changed → require VCODE
                        const validEmail = isValidEmail(current);
                        sendCodeBtn.disabled = !validEmail;
                        nextBtn.disabled = !/^\d{6}$/.test(codeInput.value.trim());
                    }
                }

                if (emailInput) emailInput.addEventListener('input', refreshEmailControls);
                if (codeInput) codeInput.addEventListener('input', refreshEmailControls);

                document.getElementById('wizardTabs').addEventListener('shown.bs.tab', () => {
                    if (currentTab === 2) refreshEmailControls();
                });
            });


            // PHOTO & NEXT BUTTON LOGIC
            document.addEventListener("DOMContentLoaded", () => {
                const photoInput = document.querySelector('#step4 [name="photo"]');
                const nextBtn    = document.getElementById('nextBtn');
                const original   = photoInput.dataset.original;

                function refreshPhotoControls() {
                    // If original is not default AND no new file → skip requirement
                    if (original && original !== 'default_avatar.png' && !photoInput.files.length) {
                        nextBtn.disabled = false;
                    } else {
                        // If default avatar OR new file required
                        nextBtn.disabled = !photoInput.files.length;
                    }
                }

                if (photoInput) photoInput.addEventListener('change', refreshPhotoControls);

                document.getElementById('wizardTabs').addEventListener('shown.bs.tab', () => {
                    if (currentTab === 3) refreshPhotoControls();
                });
            });


            // PASSWORD & NEXT BUTTON LOGIC
            document.addEventListener("DOMContentLoaded", () => {
                const pane    = document.querySelector('#step5');
                const nextBtn = document.getElementById('nextBtn');

                const current = pane.querySelector('[name="current_password"]');
                const newer   = pane.querySelector('[name="new_password"]');
                const confirm = pane.querySelector('[name="confirm_password"]');

                function refreshPasswordControls() {
                    const hasAny = current.value.trim() || newer.value.trim() || confirm.value.trim();

                    if (!hasAny) {
                        // Nothing typed → skip requirement
                        nextBtn.disabled = false;
                    } else {
                        // Require all fields and matching
                        const valid = current.value.trim() && newer.value.trim() && confirm.value.trim()
                                    && newer.value === confirm.value;
                        nextBtn.disabled = !valid;
                    }
                }

                [current, newer, confirm].forEach(inp => inp.addEventListener('input', refreshPasswordControls));

                document.getElementById('wizardTabs').addEventListener('shown.bs.tab', () => {
                    if (currentTab === 4) refreshPasswordControls();
                });
            });

            
            // PW Toggle Switch
            document.addEventListener("DOMContentLoaded", () => {
                document.querySelectorAll(".toggle-password").forEach(btn => {
                    btn.addEventListener("click", () => {
                    const input = btn.closest(".input-group").querySelector("input");
                    const icon  = btn.querySelector("i");

                    if (input.type === "password") {
                        input.type = "text";
                        icon.classList.remove("fa-eye");
                        icon.classList.add("fa-eye-slash");
                    } else {
                        input.type = "password";
                        icon.classList.remove("fa-eye-slash");
                        icon.classList.add("fa-eye");
                    }
                    });
                });
            });




            // Keep tab sync when clicking directly
            document.getElementById('wizardTabs').addEventListener('shown.bs.tab', (event) => {
                currentTab = tabElList.indexOf(event.target);
                showTab(currentTab);
            });

            showTab(currentTab);
        </script>

        <pre><?php print_r($_SESSION['wizard']); ?></pre>


    </body>
</html>
