<?php
    // Default Meta Data
    CONST PROJECT                                   = "CrisAnn";
    CONST TITLE                                     = "Shop";
    CONST AUTHOR                                    = "ASPulido Consultancy";
    CONST COPYRIGHT                                 = "Copyright &copy; ";
    CONST META_DESCRIPTION                          = "Cris Ann Shop offers authentic Filipino delicacies, local souvenirs, and regional treats—your trusted pasalubong shop for home and travel.";
    CONST META_KEYWORDS                             = "cris ann shop, cris ann pasalubong center, pasalubong, filipino delicacies, native products, souvenirs, local treats, food gifts, regional specialties, handmade crafts, pinoy pasalubong, pasalubong shop philippines, pasalubong store, local delicacies, gift center, cris ann store";
    CONST COOKIE_HANDLE                             = "ASPulido";
    CONST TIMEZONE                                  = "Asia/Manila";

    // Database
    CONST DB_HOST                                   = "localhost";
    CONST DB_NAME                                   = "ebaligya";   // u630168728_crisannshop
    CONST DB_USER                                   = "root";       // u630168728_crisannuser
    CONST DB_PASS                                   = "";           // !4vAxRDq@n2M

    CONST ENCRYPTION_METHOD                         = "aes-256-cbc";
    CONST ENCRYPTION_KEY                            = "CBCAES256";

    
    // detect environment automatically
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
    // default to local
    $env = 'local';
    if (strpos($host, 'ebaligya.digitalassetsph.com') !== false) {
        $env = 'staging';
    } elseif (strpos($host, 'crisannshop.com') !== false) {
        $env = 'production';
    }

    // base URLs
    switch ($env) {
        case 'local':
            define('BASE_URL', 'http://localhost/ebaligya/public');
            define("BASE_PATH", dirname(__DIR__));
            define("SHOP_URL", "http://localhost/ebaligya/public/shop");
            define("SHOP_PATH", dirname(__DIR__));
            define("SHOP_IMAGES", BASE_URL . "/shop/assets/images/products");
            break;
        case 'staging':
            define('BASE_URL', 'https://ebaligya.digitalassetsph.com/public');
            define("BASE_PATH", dirname(__DIR__));
            define("SHOP_URL", "https://ebaligya.digitalassetsph.com/public/shop");
            define("SHOP_PATH", dirname(__DIR__));
            define("SHOP_IMAGES", BASE_URL . "/shop/assets/images/products");
            break;
        case 'production':
            define('BASE_URL', 'https://crisannshop.com/public');
            define("BASE_PATH", dirname(__DIR__));
            define("SHOP_URL", "https://crisannshop.com/public/shop");
            define("SHOP_PATH", dirname(__DIR__));
            define("SHOP_IMAGES", BASE_URL . "/shop/assets/images/products");
            break;
    }

           
    // DATE LIMITS
    
    define("DATE_MINIMUM",                          "2025-10-29");
    define("DATE_MAXIMUM",                          date('Y-m-t') ); // Dynamically set to end of current month.
    //
    // PATH TO FOLDER STRUCTURE                     PATH_SEPARATOR
    //

    // BROWSER-FACING 🌍
    // BASE_URL (HTML src, href, meta, etc.)
    define("FOLDER_IMAGES",                         BASE_URL . "/theme/images/");
    define("FAVICON",                               FOLDER_IMAGES . "favicon.ico");
    define("SITE_LOGO",                             FOLDER_IMAGES . "site_logo.png");
    define("SITE_TEXT_LOGO",                        FOLDER_IMAGES . "site_text_logo.png");
    define("AUTHOR_IMAGE",                          FOLDER_IMAGES . "author.png");
    define("DEFAULT_AVATAR",                        FOLDER_IMAGES . "default_avatar.png");

    define("BACKGROUND",                            BASE_URL . "/images/background/" . date("N") . ".jpg");
    define("FOLDER_PDF",                            BASE_URL . "/../storage/pdf/");
    define("FOLDER_USER_PHOTOS",                    BASE_URL . "/../storage/userPhotos/");

    // FILE SYSTEM OPERATIONS 🐘 
    // BASE_PATH (file system ops: move_uploaded_file, require, fopen), 
    define("PATH_INCLUDES",                         BASE_PATH . "/public/includes/");    
    define("UPLOAD_USER_PHOTOS",                    BASE_PATH . "/storage/userPhotos/");
    define("SIDEBARDYNAMIC",                        PATH_INCLUDES . "sidebarDynamic.php");
    define("LOGINMETA",                             PATH_INCLUDES . "loginMeta.php");
    define("LOGINJQUERY",                           PATH_INCLUDES . "loginJQuery.php");
    define("DASHBOARDMETA",                         PATH_INCLUDES . "dashboardMeta.php");
    define("DASHBOARDHEADER",                       PATH_INCLUDES . "dashboardHeader.php");
    define("DASHBOARDFOOTER",                       PATH_INCLUDES . "dashboardFooter.php");
    define("DASHBOARDSCRIPTS",                      PATH_INCLUDES . "dashboardScripts.php");
    define("PRELOADER",                             PATH_INCLUDES . "preLoader.php");
    define("SIDEBAR",                               PATH_INCLUDES . "left-sidebar.php");
    define("SIDEBAR_RIGHT",                         PATH_INCLUDES . "sidebarRight.php");
    define("SHOWUSERPROFILE",                       PATH_INCLUDES . "showProfile.php");
    define("MIDDLEWARE",                            PATH_INCLUDES . "middleWare.php");

    // SHOP SPECIFIC
    define("SHOP_INCLUDES",                         SHOP_PATH . "/public/shop/includes/");    
    define("SHOP_META",                             SHOP_INCLUDES . "shopMeta.php");
    define("SHOP_HEADER",                           SHOP_INCLUDES . "shopHeader.php");
    define("SHOP_FOOTER",                           SHOP_INCLUDES . "shopFooter.php");

    define("SHOP_PHONE",                         "(63) 991 905.7797");
    define("SHOP_EMAIL",                         "support@crissannshop.com");
    // echo '<p> env ' . $env;
    // echo '<p> urk ' . SHOP_URL;
    // echo '<p> path ' . SHOP_PATH;
    // echo '<p> incldues ' . SHOP_INCLUDES;
    // echo '<p> meta ' . SHOP_META;
    // exit;