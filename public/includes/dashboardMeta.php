<?php ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">

    <!-- Tell the browser to be responsive to screen width -->
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="author" content="<?php echo AUTHOR; ?>">
    <meta name="description" content="<?php echo META_DESCRIPTION; ?>">
    <meta name="keywords" content="<?php echo META_KEYWORDS; ?>">

    <!-- Page Title -->
    <title><?php echo PROJECT; ?></title>

    <!-- Favicon icon -->
    <link rel="icon" type="image/png" sizes="16x16" href="<?php echo FAVICON; ?>">

    
    <!-- Daterange picker -->
    <link href="<?php echo BASE_URL . '/theme/vendor/bootstrap-daterangepicker/daterangepicker.css'; ?>" rel="stylesheet">

    <!-- Datatable -->
    <link rel="stylesheet" href="<?php echo BASE_URL . '/theme/vendor/datatables/css/jquery.dataTables.min.css'; ?>" >
    <link rel="stylesheet" href="<?php echo BASE_URL . '/theme/vendor/datatables/responsive/responsive.css'; ?>">

    <!-- Datatables Buttons (CDN since theme doesn’t have them) -->
    <link rel="stylesheet" href="<?php echo BASE_URL . '/theme/vendor/datatables/css/buttons.dataTables.min'; ?>" >

	<!-- All StyleSheet -->
     
    <link rel="stylesheet" href="<?php echo BASE_URL . '/theme/vendor/select2/css/select2.min.css'; ?>">
	<link rel="stylesheet" href="<?php echo BASE_URL . '/theme/vendor/bootstrap-select/dist/css/bootstrap-select.min.css'; ?>" />
	<link rel="stylesheet" href="<?php echo BASE_URL . '/theme/vendor/bootstrap-datepicker-master/css/bootstrap-datepicker.min.css'; ?>" />


    <link rel="stylesheet" href="<?php echo BASE_URL . '/theme/vendor/toastr/css/toastr.min.css'; ?>">
    <!-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css"> -->

	
	<!-- Global CSS -->
    <link rel="stylesheet" href="<?php echo BASE_URL . '/theme/css/style.css'; ?>" class="main-css" />
    <!-- <link rel="stylesheet" href="<?php echo BASE_URL . '/theme/css/custom.css'; ?>">  -->

    <!-- DB-driven Palette -->
    <!-- <style>
    :root {
        <?php
            foreach (get_defined_constants(true)['user'] as $const => $val) {
                if (strpos($const, 'COLOR_') === 0) {
                    if (substr($const, -4) === '_RGB') {
                        $cssVar = strtolower(str_replace(['COLOR_', '_RGB'], ['--bs-', '-rgb'], $const));
                        echo "  {$cssVar}: {$val};\n";
                    } else {
                        $cssVar = strtolower(str_replace('COLOR_', '--bs-', $const));
                        echo "  {$cssVar}: {$val};\n";
                    }
                }
            }
        ?>
    }
        /* Text Colors */
    .text-primary    { color: var(--bs-primary) !important; }
    .text-secondary  { color: var(--bs-secondary) !important; }
    .text-success    { color: var(--bs-success) !important; }
    .text-info       { color: var(--bs-info) !important; }
    .text-warning    { color: var(--bs-warning) !important; }
    .text-danger     { color: var(--bs-danger) !important; }
    .text-light      { color: var(--bs-light-text, #212529) !important; } /* contrast-safe */
    .text-dark       { color: var(--bs-dark-text, #fff) !important; }

    /* Backgrounds */
    .bg-primary    { background-color: var(--bs-primary) !important; }
    .bg-secondary  { background-color: var(--bs-secondary) !important; }
    .bg-success    { background-color: var(--bs-success) !important; }
    .bg-info       { background-color: var(--bs-info) !important; }
    .bg-warning    { background-color: var(--bs-warning) !important; }
    .bg-danger     { background-color: var(--bs-danger) !important; }
    .bg-light      { background-color: var(--bs-light) !important; }
    .bg-dark       { background-color: var(--bs-dark) !important; }

    /* Cards */
    .card {
        background-color: var(--bs-light) !important;
        color: var(--bs-dark-text, #000) !important;
    }
    .card.bg-dark {
        background-color: var(--bs-dark) !important;
        color: var(--bs-light-text, #fff) !important;
    }
/* Default (light mode) */
body,
body p,
body span,
body div,
body li,
body td,
body th {
  color: var(--bs-light-text, #212529) !important;
}

/* Dark mode (replace .dark-mode with your theme’s dark selector) */
body.dark-mode,
body.dark-mode p,
body.dark-mode span,
body.dark-mode div,
body.dark-mode li,
body.dark-mode td,
body.dark-mode th {
  color: var(--bs-dark-text, #e9ecef) !important;
}

/* Force nav text to follow the palette */
.nav-text {
  color: var(--bs-dark-text, #e9ecef) !important; /* default (light mode) */
}
.nav-link{
  color: var(--bs-dark-text, #e9ecef) !important; /* default (light mode) */
}

body.dark-mode .nav-text {
  color: var(--bs-dark-text, #e9ecef) !important; /* dark mode */
} 
    
    </style>-->


    <!-- Global config from PHP -->
    <script>
        window.appConfig = {
            idleThreshold: <?= IDLE_THRESHOLD ?>,
            countdownSeconds: <?= COUNTDOWN_SECONDS ?>
        };
    </script>