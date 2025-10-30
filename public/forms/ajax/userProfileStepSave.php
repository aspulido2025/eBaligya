<?php
    require __DIR__ . '/../../../config/init.php';
    header('Content-Type: application/json');
    if (session_status() !== PHP_SESSION_ACTIVE) { 
        session_start(); 
    }

    use App\Classes\DB;
    use App\Classes\Logger;

    $db = new DB($pdo);
    $logger = new Logger($db);

    if (empty($_SESSION['rbac']['user_id'])) {
        http_response_code(403);                                                                            
        echo json_encode(['status'=>'error','message'=>'Not authorized']); exit;
    }

    $step = $_POST['step'] ?? '';
    $data = json_decode($_POST['data'] ?? '{}', true);
    if (!is_array($data)) $data = [];

    // STEP 1 PROFILE
    // =================================================================================================
    if ($step === 'profile') {
        $fullname = trim($data['fullname'] ?? '');
        $username = trim($data['username'] ?? '');
        $birthday = trim($data['birthday'] ?? '');

        // Required fields
        if ($fullname === '' || $username === '' || $birthday === '') {
            http_response_code(422);
            echo json_encode(['status'=>'error','message'=>'All fields are required.','Error']); exit;
        }

        // Birthday validation
        $t = strtotime($birthday);
        if ($t === false) {
            http_response_code(422);
            echo json_encode(['status'=>'error','message'=>'Invalid birthday format.','Error']); exit;
        }
        $birthday = date('Y-m-d', $t);

        // Username uniqueness check
        $userId = $_SESSION['rbac']['user_id'];
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM system_users WHERE username = ? AND id <> ?");
        $stmt->execute([$username, $userId]);
        if ($stmt->fetchColumn() > 0) {
            http_response_code(409);
            echo json_encode(['status'=>'error','message'=>'Username already taken.','Error']); exit;
        }

        // Save to session cache
        $_SESSION['wizard']['profile'] = [
            'fullname' => $fullname,
            'username' => $username,
            'birthday' => $birthday,
        ];

        echo json_encode(['status'=>'success','message'=>'Profile step saved...',"Success"]);           // overridden by userProfile if (window.toastr) toastr.success("Step: Profile, saved.", "Success"); }
        exit;
    }


    // STEP 2 MOBILE NUMBER
    // =================================================================================================
    if ($step === 'mobile') {
        $mobile = trim($data['mobile'] ?? '');
        $otp    = trim($data['otp'] ?? '');
        $userId = $_SESSION['rbac']['user_id'];

        $currentMobile = $_SESSION['rbac']['mobile_number'] ?? '';

        // If unchanged, skip OTP
        if ($mobile === $currentMobile && $otp === '') {
            $_SESSION['wizard']['mobile'] = [
                'mobile' => $currentMobile,
                'verified_mobile' => 1
            ];
            echo json_encode(['status'=>'success','message'=>'Mobile unchanged, step skipped']);
            exit;
        }

        // Require values if changed
        if ($mobile === '' || $otp === '') {
            http_response_code(422);
            echo json_encode(['status'=>'error','message'=>'Mobile Number and OTP required']); exit;
        }

        // Format check
        if (!preg_match('/^\+639\d{9}$/', $mobile)) {
            http_response_code(422);
            echo json_encode(['status'=>'error','message'=>'Invalid mobile number format, use +639XXXXXXXXX']); exit;
        }

        // OTP check
        $sessionOtp = (string)($_SESSION['wizard']['mobile']['otp'] ?? '');
        if ($otp !== $sessionOtp) {
            http_response_code(422);
            echo json_encode(['status'=>'error','message'=>'Invalid OTP']); exit;
        }

        // Uniqueness check
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM system_users WHERE mobile_number = ? AND id <> ?");
        $stmt->execute([$mobile, $userId]);
        if ($stmt->fetchColumn() > 0) {
            http_response_code(409);
            echo json_encode(['status'=>'error','message'=>'Mobile number already registered.']); exit;
        }

        $_SESSION['wizard']['mobile'] = [
            'mobile' => $mobile,
            'otp'    => $otp,
            'verified_mobile' => 1
        ];

        echo json_encode(['status'=>'success','message'=>'Mobile step saved']);
        exit;
    }


    // STEP 2: SEND OTP (dummy)
    // =================================================================================================
    if ($step === 'send_otp') {
        $mobile = trim($data['mobile'] ?? '');

        if ($mobile === '') {
            http_response_code(422);
            echo json_encode(['status'=>'error','message'=>'Mobile number required']); exit;
        }

        // Generate a random 6-digit OTP
        $otp = rand(100000, 999999);

        // Save OTP in wizard cache (for later validation)
        $_SESSION['wizard']['mobile'] = [
            'mobile' => $mobile,
            'otp'    => (string)$otp,
        ];

        // ✅ Dummy send (log to error_log)
        // error_log("📱 Dummy OTP for $mobile is $otp");

        echo json_encode(['status'=>'success','message'=>'OTP sent (dummy)','debug_otp'=>$otp]);
        exit;
    }


    // STEP 3 EMAIL ADDRESS
    // =================================================================================================
    if ($step === 'email') {
        $email = trim($data['email_address'] ?? '');
        $vcode = trim($data['verification_code'] ?? '');
        $userId = $_SESSION['rbac']['user_id'];

        $currentEmail = $_SESSION['rbac']['email_address'] ?? '';

        // If unchanged, skip code
        if ($email === $currentEmail && $vcode === '') {
            $_SESSION['wizard']['email'] = [
                'email_address'  => $currentEmail,
                'verified_email' => 1
            ];
            echo json_encode(['status'=>'success','message'=>'Email unchanged, step skipped']);
            exit;
        }

        if ($email === '' || $vcode === '') {
            http_response_code(422);
            echo json_encode(['status'=>'error','message'=>'Email and Verification Code required']); exit;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            http_response_code(422);
            echo json_encode(['status'=>'error','message'=>'Invalid email address format']); exit;
        }

        if ($vcode !== ($_SESSION['wizard']['email']['verification_code'] ?? '')) {
            http_response_code(422);
            echo json_encode(['status'=>'error','message'=>'Invalid verification code']); exit;
        }

        $stmt = $pdo->prepare("SELECT COUNT(*) FROM system_users WHERE email_address = ? AND id <> ?");
        $stmt->execute([$email, $userId]);
        if ($stmt->fetchColumn() > 0) {
            http_response_code(409);
            echo json_encode(['status'=>'error','message'=>'Email already registered']); exit;
        }

        $_SESSION['wizard']['email'] = [
            'email_address'     => $email,
            'verification_code' => $vcode,
            'verified_email'    => 1
        ];

        echo json_encode(['status'=>'success','message'=>'Email step saved']);
        exit;
    }


    // STEP 3: SEND EMAIL VERIFICATION CODE (dummy)
    // =================================================================================================
    if ($step === 'send_email_code') {
        $email = trim($data['email_address'] ?? '');

        if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            http_response_code(422);
            echo json_encode(['status'=>'error','message'=>'Invalid email']); exit;
        }

        $code = (string)rand(100000, 999999);

        $_SESSION['wizard']['email'] = [
            'email_address' => $email,
            'verification_code' => $code
        ];

        // error_log("📧 Dummy verification code for $email is $code");

        echo json_encode(['status'=>'success','message'=>'Verification code sent (dummy)','debug_code'=>$code]);
        exit;
    }


    // STEP 4 PHOTO
    // =================================================================================================
    if ($step === 'photo') {
        // If user already has a photo and no new file → skip
        if (
            ($_SESSION['rbac']['photo'] ?? 'default_avatar.png') !== 'default_avatar.png'
            && empty($_FILES['photo']['name'])
        ) {
            $_SESSION['wizard']['photo'] = [
                'name' => $_SESSION['rbac']['photo'],
                'tmp'  => null
            ];
            echo json_encode(['status'=>'success','message'=>'Photo unchanged, step skipped']);
            exit;
        }

        if (!isset($_FILES['photo'])) {
            http_response_code(422);
            echo json_encode(['status'=>'error','message'=>'No file uploaded']); exit;
        }

        $file = $_FILES['photo'];
        $maxSize = 2 * 1024 * 1024; // 2MB

        // Validation: file error
        if ($file['error'] !== UPLOAD_ERR_OK) {
            http_response_code(422);
            echo json_encode(['status'=>'error','message'=>'Upload error.','Error']); exit;
        }

        // Validation: extension
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $allowedExt = ['jpeg','jpg','png','bmp'];
        if (!in_array($ext, $allowedExt)) {
            http_response_code(422);
            echo json_encode(['status'=>'error','message'=>'Invalid file extension (allowed: jpeg, jpg, png, bmp)','Error']); exit;
        }

        // Validation: mime type (double-check)
        $mime = mime_content_type($file['tmp_name']);
        $allowedMime = ['image/jpeg','image/png','image/bmp'];
        if (!in_array($mime, $allowedMime)) {
            http_response_code(422);
            echo json_encode(['status'=>'error','message'=>'Invalid file type.','Error']); exit;
        }

        // Validation: size
        if ($file['size'] > $maxSize) {
            http_response_code(422);
            echo json_encode(['status'=>'error','message'=>'File too large, max 2MB.','Error']); exit;
        }

        // Save temporarily in session
        $_SESSION['wizard']['photo'] = [
            'name' => $file['name'],
            'type' => $mime,
            'size' => $file['size']
        ];

        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $userId = $_SESSION['rbac']['user_id'];
        $tmpName = $userId . "_tmp_" . uniqid() . "." . $ext;
        $dest = UPLOAD_USER_PHOTOS . $tmpName;

        if (move_uploaded_file($file['tmp_name'], $dest)) {
            $_SESSION['wizard']['photo'] = [
                'tmp'  => $tmpName,   // ✅ keep relative filename
                'name' => $file['name'],
                'type' => $file['type'],
                'size' => $file['size']
            ];
        }


        echo json_encode(['status'=>'success','message'=>'Photo step saved']);
        exit;
    }


    // STEP 5 PASSWORD
    // =================================================================================================
    if ($step === 'password') {
        $current = trim($data['current_password'] ?? '');
        $new     = trim($data['new_password'] ?? '');
        $confirm = trim($data['confirm_password'] ?? '');

        // If left blank → skip
        if ($current === '' && $new === '' && $confirm === '') {
            echo json_encode(['status'=>'success','message'=>'Password unchanged, step skipped']);
            exit;
        }

        // Required
        if ($current === '' || $new === '' || $confirm === '') {
            http_response_code(422);
            echo json_encode(['status'=>'error','message'=>'All fields are required.','Error']); exit;
        }

        // Match new and confirm
        if ($new !== $confirm) {
            http_response_code(422);
            echo json_encode(['status'=>'error','message'=>'Passwords do not match.','Error']); exit;
        }

        // Strength check (your accepted pattern)
        $acceptedPattern = '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[.@$!%*?&])[A-Za-z\d.@$!%*?&]{8,}$/';
        if (!preg_match($acceptedPattern, $new)) {
            http_response_code(422);
            echo json_encode(['status'=>'error',
                'message'=>'Password must be at least 8 chars, with uppercase, lowercase, number, and special character.','Error']);
            exit;
        }

        // Verify current password against DB
        $userId = $_SESSION['rbac']['user_id'];
        $stmt = $pdo->prepare("SELECT password_hash FROM system_users WHERE id = ?");
        $stmt->execute([$userId]);
        $hash = $stmt->fetchColumn();

        if (!$hash || !password_verify($current, $hash)) {
            http_response_code(403);
            echo json_encode(['status'=>'error','message'=>'Current password is incorrect.','Error']); exit;
        }

        // ✅ Cache hashed password (not plain!)
        $_SESSION['wizard']['password'] = [
            'new_password_hash' => password_hash($new, PASSWORD_BCRYPT)
        ];

        echo json_encode(['status'=>'success','message'=>'Password step saved']);
        exit;
    }


    // STEP 6 PRIVACY
    // =================================================================================================
    if ($step === 'privacy') {
        $accepted = !empty($data['privacy_accepted']) && $data['privacy_accepted'] == '1';

        if (!$accepted) {
            http_response_code(422);
            echo json_encode(['status'=>'error','message'=>'You must accept the Privacy Policy.','Errpr']); exit;
        }

        // Save to wizard cache (same key as RBAC)
        $_SESSION['wizard']['privacy'] = [
            'privacy_accepted' => 1,
            'accepted_at'      => date('Y-m-d H:i:s')
        ];

        echo json_encode(['status'=>'success','message'=>'Privacy policy accepted']);
        exit;
    }


    //  FINAL SUBMIT
    // =================================================================================================
    if ($step === 'final_submit') {
        $userId = $_SESSION['rbac']['user_id'] ?? null;
        if (!$userId) {
            http_response_code(403);
            echo json_encode(['status'=>'error','message'=>'Not authorized']); exit;
        }


        $wiz = $_SESSION['wizard'] ?? [];

        // Collect values from wizard
        $fullname   = $wiz['profile']['fullname'] ?? null;
        $username   = $wiz['profile']['username'] ?? null;
        $birthday   = $wiz['profile']['birthday'] ?? null;
        $mobile     = $wiz['mobile']['mobile'] ?? null;
        $email      = $wiz['email']['email_address'] ?? null;
        $password_h = $wiz['password']['new_password_hash'] ?? null;
        $privacy    = $wiz['privacy']['privacy_accepted'] ?? 0;

        // ✅ Always define photo upfront
        $photo = null;

        // Photo handling: move from tmp to final folder
        if (!empty($wiz['photo']['tmp'])) {
            $tmpFile = UPLOAD_USER_PHOTOS . $wiz['photo']['tmp'];
            if (file_exists($tmpFile)) {
                $ext = strtolower(pathinfo($wiz['photo']['tmp'], PATHINFO_EXTENSION));
                $safeUsername = preg_replace('/[^a-zA-Z0-9_-]/', '', $username);
                $filename = $userId . "_" . $safeUsername . "_" . time() . "." . $ext;
                $finalPath = UPLOAD_USER_PHOTOS . $filename;

                if (rename($tmpFile, $finalPath)) {
                    $photo = $filename; // store in DB
                }
            }
        }

        // Build update query
        $fields = [];
        $params = [];

        if ($fullname)  { $fields[] = "fullname=?";             $params[] = $fullname; }
        if ($username)  { $fields[] = "username=?";             $params[] = $username; }
        if ($birthday)  { $fields[] = "date_birth=?";           $params[] = $birthday; }
        if ($mobile)    { $fields[] = "mobile_number=?";        $params[] = $mobile; }
        if ($email)     { $fields[] = "email_address=?";        $params[] = $email; }
        if ($password_h){ $fields[] = "password_hash=?";        $params[] = $password_h; }
        if ($privacy)   { $fields[] = "is_legal_privacy=?";     $params[] = 1; }
        if ($photo)     { $fields[] = "photo=?";                $params[] = $photo; }

        if ($fields) {
            $fields[] = "updated_id=?";
            $params[] = $userId;

            // Verified flags (if wizard says verified)
            if (!empty($wiz['email']['verified_email'])) {
                $fields[] = "is_verified_email=?";
                $params[] = 1;
            }

            if (!empty($wiz['mobile']['verified_mobile'])) {
                $fields[] = "is_verified_mobile=?";
                $params[] = 1;
            }


            $params[] = $userId;
            $sql = "UPDATE system_users SET " . implode(", ", $fields) . ", updated=NOW() WHERE id=?";
            

            // Debug substitution
            $debugSql = $sql;
            foreach ($params as $p) {
                $pQuoted = $pdo->quote($p);
                $debugSql = preg_replace('/\?/', $pQuoted, $debugSql, 1);
            }
            error_log("Debug SQL: $debugSql");
            error_log("SQL: $sql");
            error_log("Params: " . print_r($params, true));

            // Update 
            $db->exec($sql, $params);
            // $stmt = $pdo->prepare($sql);
            // $stmt->execute($params);
            // error_log("Rows updated: " . $stmt->rowCount());

            
            // 📝 Transaction log (simplified)
            $logger->log(Logger::TRANSACTION, [
                'user_id'       => $userId,
                'turnout'       => 'USER PROFILE UPDATED',  
                'entity'        => 'system_users',
                'entity_id'     => $userId,
                'ipaddress'     => getClientIP(), 
                'user_agent'    => getUserAgent(),  
                'master_session'=> $_SESSION['rbac']['session_token']
                ]);



            // === Refresh RBAC session values ===
            if (!empty($_SESSION['rbac']['user_id'])) {
                $_SESSION['rbac']['fullname']       = $fullname ?? $_SESSION['rbac']['fullname'];
                $_SESSION['rbac']['username']       = $username ?? $_SESSION['rbac']['username'];
                $_SESSION['rbac']['birthday']       = $birthday ?? $_SESSION['rbac']['birthday'];
                
                // ✅ Only overwrite photo if a new one exists
                if (!empty($photo)) {
                    $_SESSION['rbac']['photo'] = $photo;
                }

                $_SESSION['rbac']['mobile_number']  = $mobile   ?? $_SESSION['rbac']['mobile_number'];
                $_SESSION['rbac']['email_address']  = $email    ?? $_SESSION['rbac']['email_address'];


                // ✅ If you want to reflect verification flags too
                if (!empty($wiz['email']['verified_email'])) {
                    $_SESSION['rbac']['verified_email'] = 1;
                }
                if (!empty($wiz['mobile']['verified_mobile'])) {
                    $_SESSION['rbac']['verified_mobile'] = 1;
                }

                if (!empty($wiz['privacy']['privacy_accepted'])) {
                    $_SESSION['rbac']['is_legal_privacy'] = 1;
                }
            }


            // Clear wizard cache
            unset($_SESSION['wizard']);
        }


        echo json_encode(['status'=>'success','message'=>'Profile updated']);
        exit;
    }


// Invalid step
http_response_code(400);
echo json_encode(['status'=>'error','message'=>'Invalid step.','Error']);
