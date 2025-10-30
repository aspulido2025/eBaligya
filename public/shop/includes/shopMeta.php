<?php 

// Detect current file name (fallback)
$currentFile = basename($_SERVER['PHP_SELF'], '.php');
$pageName    = ucfirst(str_replace(['_', '-'], ' ', $currentFile));

// If a custom $pageTitle is set by the page, use it instead
$finalTitle  = defined('PROJECT')
    ? PROJECT . (!empty($pageTitle) ? ' — ' . $pageTitle : ($currentFile !== 'index' ? ' — ' . $pageName : ''))
    : (!empty($pageTitle) ? $pageTitle : $pageName);

?>
<!DOCTYPE html>
<html class="no-js" lang="zxx">

<head>
    <meta charset="utf-8" />
    <meta http-equiv="x-ua-compatible" content="ie=edge" />
    <title><?= htmlspecialchars($finalTitle) ?></title>
    <meta name="description" content="" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link rel="shortcut icon" type="image/x-icon" href="<?= SHOP_URL . '/assets/images/favicon.svg' ?>" />

    <!-- ========================= CSS here ========================= -->
    <link rel="stylesheet" href="<?= SHOP_URL . '/assets/css/bootstrap.min.css' ?>" />
    <link rel="stylesheet" href="<?= SHOP_URL . '/assets/css/LineIcons.3.0.css' ?>" />
    <link rel="stylesheet" href="<?= SHOP_URL . '/assets/css/tiny-slider.css' ?>" />
    <link rel="stylesheet" href="<?= SHOP_URL . '/assets/css/glightbox.min.css' ?>" />
    <link rel="stylesheet" href="<?= SHOP_URL . '/assets/css/main.css' ?>" />

    <!-- PWA Setup -->
    <link rel="manifest" href="manifest.json">
    <meta name="theme-color" content="#2563eb">
    <link rel="apple-touch-icon" href="assets/images/icons/icon-192.png">

    <script>
    if ('serviceWorker' in navigator) {
        window.addEventListener('load', () => {
        navigator.serviceWorker.register('<?= SHOP_URL ?>/service-worker.js', { scope: '<?= SHOP_URL ?>/' })
            .then(() => console.log('✅ Service Worker registered'))
            .catch(err => console.log('❌ SW registration failed:', err));
        });
    }
    </script>


</head>

<body>

    <!-- Preloader -->
    <div class="preloader">
        <div class="preloader-inner">
            <div class="preloader-icon">
                <span></span>
                <span></span>
            </div>
        </div>
    </div> 
    <!-- /End Preloader