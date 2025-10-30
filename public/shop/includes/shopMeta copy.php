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


<!-- <meta name="description" content="Cris Ann Pasalubong Center – now at CrisAnnShop.com! Discover authentic Filipino delicacies, regional treats, and local souvenirs — the perfect pasalubong for your loved ones.">
<meta name="keywords" content="crisannshop, cris ann pasalubong, filipino delicacies, native treats, pasalubong shop, souvenirs, local products, food gifts, regional specialties, pasalubong philippines, pasalubong center, cris ann store, cris ann pasalubong center">
<meta name="author" content="Cris Ann Pasalubong Center">
<meta name="robots" content="index, follow">
<meta property="og:title" content="<?= $title ?>">
<meta property="og:description" content="Cris Ann Pasalubong Center – authentic Filipino delicacies and local pasalubong favorites. Visit us at CrisAnnShop.com!">
<meta property="og:type" content="website">
<meta property="og:url" content="https://crisannshop.com/">
<meta property="og:image" content="https://crisannshop.com/assets/images/social-preview.jpg">
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="<?= $title ?>">
<meta name="twitter:description" content="Authentic Filipino pasalubong delicacies and souvenirs — Cris Ann Pasalubong Center.">
<meta name="theme-color" content="#e91e63"> -->


    <link rel="shortcut icon" type="image/x-icon" href="<?= SHOP_URL . '/assets/images/favicon.svg' ?>" />

    <!-- ========================= CSS here ========================= -->
    <link rel="stylesheet" href="<?= SHOP_URL . '/assets/css/bootstrap.min.css' ?>" />
    <link rel="stylesheet" href="<?= SHOP_URL . '/assets/css/LineIcons.3.0.css' ?>" />
    <link rel="stylesheet" href="<?= SHOP_URL . '/assets/css/tiny-slider.css' ?>" />
    <link rel="stylesheet" href="<?= SHOP_URL . '/assets/css/glightbox.min.css' ?>" />
    <link rel="stylesheet" href="<?= SHOP_URL . '/assets/css/main.css' ?>" />


    <!-- <link rel="manifest" href="manifest.json"> -->
<!-- <meta name="theme-color" content="#e91e63">
<link rel="apple-touch-icon" href="assets/images/logo/logo.png"> -->

</head>

<body>

    <!-- Preloader -->
    <!-- <div class="preloader">
        <div class="preloader-inner">
            <div class="preloader-icon">
                <span></span>
                <span></span>
            </div>
        </div>
    </div> -->
    <!-- /End Preloader -->