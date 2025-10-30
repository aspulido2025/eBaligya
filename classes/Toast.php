<?php
    namespace App\Classes;

    // :warning :error :success :info

    class Toast
    {
        public static function set(string $type, string $message, string $title = ''): void
        {
            $_SESSION['toast'] = [
                'type'    => $type,
                'message' => $message,
                'title'   => $title
            ];
        }

        public static function render(): void
        {
            if (!isset($_SESSION['toast'])) {
                return;
            }

            $toast = $_SESSION['toast'];
            unset($_SESSION['toast']); // clear so it shows only once

            echo "<script>
                toastr.options = {
                    closeButton: true,
                    debug: false,
                    newestOnTop: true,
                    progressBar: true,
                    positionClass: 'toast-top-full-width', // 👈 default placement
                    preventDuplicates: false,
                    onclick: null,
                    showDuration: '300',   // fadeIn time (ms)
                    hideDuration: '1000',  // fadeOut time (ms)
                    timeOut: '1000',       // auto-dismiss delay (ms)
                    extendedTimeOut: '1000', // mouse hover delay (ms)
                    showEasing: 'swing',
                    hideEasing: 'linear',
                    showMethod: 'fadeIn',
                    hideMethod: 'fadeOut'
                };
                toastr.{$toast['type']}(" . json_encode($toast['message']) . ", " . json_encode($toast['title']) . ");
                
            </script>";
        }
    }

// original options
// toastr.options = {
//                     closeButton: true,
//                     progressBar: true,
//                     timeOut: '5000',
//                     extendedTimeOut: '1000',
//                     positionClass: 'toast-top-center'

                    
                    
//                 };

// toastr.options = {
//   "closeButton": false,
//   "debug": false,
//   "newestOnTop": false,
//   "progressBar": false,
//   "positionClass": "toast-top-right", // 👈 default placement
//   "preventDuplicates": false,
//   "onclick": null,
//   "showDuration": "300",   // fadeIn time (ms)
//   "hideDuration": "1000",  // fadeOut time (ms)
//   "timeOut": "5000",       // auto-dismiss delay (ms)
//   "extendedTimeOut": "1000", // mouse hover delay (ms)
//   "showEasing": "swing",
//   "hideEasing": "linear",
//   "showMethod": "fadeIn",
//   "hideMethod": "fadeOut"
// };

    // namespace App\Classes;

    // class Toast
    // {
    //     public static function set(string $type, string $message, string $title = ''): void
    //     {
    //         $_SESSION['toast'] = [
    //             'type'    => $type,
    //             'message' => $message,
    //             'title'   => $title
    //         ];
    //     }

    //     public static function render(): void
    //     {
    //         if (!isset($_SESSION['toast'])) {
    //             return;
    //         }

    //         $toast = $_SESSION['toast'];
    //         unset($_SESSION['toast']); // show once

    //         // Gradient + solid fallback per type
    //         $gradients = [
    //             'success' => 'linear-gradient(to right, #00b09b, #96c93d)',
    //             'error'   => 'linear-gradient(to right, #e53935, #e35d5b)',
    //             'warning' => 'linear-gradient(to right, #fbc02d, #ffeb3b)',
    //             'info'    => 'linear-gradient(to right, #2196f3, #21cbf3)',
    //         ];
    //         $fallback = [
    //             'success' => '#00b09b',
    //             'error'   => '#e53935',
    //             'warning' => '#fbc02d',
    //             'info'    => '#2196f3',
    //         ];

    //         $type = $toast['type'];
    //         $g    = $gradients[$type] ?? 'linear-gradient(to right, #444, #666)';
    //         $bg   = $fallback[$type]  ?? '#444';
    //         $text = ($type === 'warning') ? '#000' : '#fff';

    //         // CSS overrides:
    //         // - use #toast-container > .toast-* for specificity
    //         // - kill default sprite (background-image) that can make it look transparent
    //         // - force full opacity so gradients aren’t faded by theme CSS
    //         // - bump z-index so it isn’t hidden behind headers
    //         echo "<style>
    //             #toast-container { z-index: 999999 !important; }
    //             #toast-container > .toast {
    //                 background-image: none !important;   /* remove Toastr's icon sprite if interfering */
    //                 opacity: 1 !important;               /* override theme translucency */
    //                 border: 0 !important;
    //             }
    //             #toast-container > .toast-success {
    //                 background-color: {$bg} !important;
    //                 background-image: {$g} !important;
    //                 color: #fff !important;
    //             }
    //             #toast-container > .toast-error {
    //                 background-color: {$bg} !important;
    //                 background-image: {$g} !important;
    //                 color: #fff !important;
    //             }
    //             #toast-container > .toast-warning {
    //                 background-color: {$bg} !important;
    //                 background-image: {$g} !important;
    //                 color: {$text} !important;           /* black text for yellow background */
    //             }
    //             #toast-container > .toast-info {
    //                 background-color: {$bg} !important;
    //                 background-image: {$g} !important;
    //                 color: #fff !important;
    //             }
    //         </style>";

    //         echo "<script>
    //             toastr.options = {
    //                 closeButton: true,
    //                 progressBar: true,
    //                 timeOut: '10000',
    //                 extendedTimeOut: '2000',
    //                 positionClass: 'toast-top-right'
    //             };

    //             toastr.{$type}(" . json_encode($toast['message']) . ", " . json_encode($toast['title']) . ");
    //         </script>";

    //     }
    // }
