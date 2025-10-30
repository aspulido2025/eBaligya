<?php
    // CI/CD Pipeline for Version Control
    require_once __DIR__ . '/version.php';
    require_once __DIR__ . '/defaultSettings.php';

    // Maintenanance Flag 
    define('SYSTEM_LOCKED',                         false);
    // Default only if DB does not provide
    if (!defined('SYSTEM_LOCKED')) {
        define('SYSTEM_LOCKED', false);
    }


    
    // if (SYSTEM_LOCKED === true) { 
    //     header("Location: " . BASE_URL . "/auth/systemUnavailable.php");
    //     exit;
    // }

    // Default PHP Timezone 
    date_default_timezone_set(TIMEZONE);            // Apply globally
    define("DTZ", new DateTimeZone(TIMEZONE));      // Optional: make DateTimeZone instance reusable

    

    
    
    // Encryption
    // define("RETRY_ATTEMPTS",                        3);                     // maximum allowed attempt to enter USER PASSWORD and/or MOBILE NUMBER OTP AND EMAIL ADDRESS VERIFICATION CODE
    // define("SESSION_TOKEN",                         8); 
    // define("SMS_OTP_LENGTH",                        6);                     // bin2hex(getSecureCode(SECURE_CODE_LENGTH)) : Standard Randomization = bin2hex(random_bytes(32));
    // define("EMAIL_VERIFICATION_LENGTH",             6);                     // 
    // define("COOKIE_SELECTOR",                       12);                    // final string length = 12 hex chars
    // define("COOKIE_VALIDATOR",                      64);                    // final string length = 64 hex chars
    // define("AVATAR_MAX_SIZE",                       5 * 1024 * 1024);       // 5MB
    // define("SESSION_LIFETIME",                      30 * 60);               // "30 minutes");
    // define("EXPIRY_OTP",                            5 * 60);                // "5 minutes");
    // define("EXPIRY_VERIFICATION_CODE",              5 * 60);                // "5 minutes");
    // define("EXPIRY_ENCRYPTION",                     5 * 60);                // "5 minutes");
    // define("EXPIRY_COOKIE",                         30 * 24 * 60 * 60);     // "30 days");
    
    
    // define("ENCRYPTION_PASSKEY",                    "1234568790abcDEF");    // Encrypt + HMAC + Auto-detect, strict mode (Check config_functions as they are declared literally.)
    // define("INITIALIZATION_VECTOR",                 "ABCdef0134256798");    // Initialization Vector bin2hex(date('Ymd'));



    // API - SMS
    // define("ALLOW_SMS_SENDING",                     1);                     // $cfg_send_sms = 0;
    // define("SMS_API_USER",                          "dabigccoop");          // $gIuser
    // define("SMS_API_PASSWORD",                      "isms79801388");        // $gIpass
    // define("SMS_REPLY_PATH",                        "09919057797");         // $varReplyPath 


    // API - EMAIL
    // define("ALLOW_EMAIL_SENDING",                   0);                     // via PHPMailer Class


    // PDF HEADERS 
    // define("PDF_HEADER1",                           TITLE);
    // define("PDF_HEADER2",                           "HEAD OFFICE");
    // define("PDF_HEADER3",                           "#3 Sampaguita Street, Barangay Crossing Bayabas, Toril, Davao City, 8000");
    // define("PDF_HEADER4",                           "Contact Number: (0991) 905-7797 | eMail Address: aspulidoconsultancy@gmail.com");


    // Legal Documents      
    define("DOC_PRIVACYPROLICY",                    BASE_PATH . "/public/includes/TextPrivacyPolicy.php");
    define("DOC_PRIVACYPROLICY_UPDATE",             "2025 Sept 18th");
    // Terms and Conditions                         // Get from VYZOR
    // Cookie Policy



   
    /* Login Notice */
    $cfg_dashboard_notice                        = "welcome||&nbsp;||&nbsp;";
    // $cfg_dashboard_notice                        = "schedule||Please LOGOUT before maintenance period begins.||September 22, 2025 1800PST UFN"; 
    //$cfg_dashboard_notice                         = "ongoing||Uploading essential updates & security measures.||We apologize for any inconvenience.";

    