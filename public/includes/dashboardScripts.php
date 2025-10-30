<?php ?>
    <!-- Required vendors -->
    <script src="<?php echo BASE_URL . '/theme/vendor/global/global.min.js'; ?>"></script>
    <script src="<?php echo BASE_URL . '/theme/vendor/bootstrap-select/dist/js/bootstrap-select.min.js'; ?>"></script>
    <script src="<?php echo BASE_URL . '/theme/vendor/bootstrap-datepicker-master/js/bootstrap-datepicker.min.js'; ?>"></script>

    <!-- Apex Chart -->
    <!-- <script src="<?php echo BASE_URL . '/theme/vendor/apexchart/apexchart.js'; ?>"></script> -->
    <!-- <script src="<?php echo BASE_URL . '/theme/vendor/chartjs/chart.bundle.min.js'; ?>"></script> -->

    
    <!-- momment js is must -->
	<script src="<?php echo BASE_URL . '/theme/vendor/bootstrap-datepicker-master/js/bootstrap-datepicker.min.js'; ?>"></script>
    <script src="<?php echo BASE_URL . '/theme/vendor/moment/moment.min.js'; ?>"></script>
    <script src="<?php echo BASE_URL . '/theme/vendor/bootstrap-daterangepicker/daterangepicker.js'; ?>"></script>

    <!-- Daterangepicker -->
    <!-- <script src="<?php echo BASE_URL . '/theme/js/plugins-init/bs-daterange-picker-init.js'; ?>"></script> -->
    
    <!-- Datatable -->
    <script src="<?php echo BASE_URL . '/theme/vendor/datatables/js/jquery.dataTables.min.js'; ?>"></script>
    <script src="<?php echo BASE_URL . '/theme/vendor/datatables/responsive/responsive.js'; ?>"></script>
    <!-- <script src="<?php echo BASE_URL . '/theme/js/plugins-init/datatables.init.js'; ?>"></script> -->

    <!-- Datatables Buttons -->
    <script src="<?php echo BASE_URL . '/theme/vendor/datatables/js/dataTables.buttons.min.js'; ?>"></script>
    <script src="<?php echo BASE_URL . '/theme/vendor/datatables/js/buttons.html5.min.js'; ?>"></script>
    <script src="<?php echo BASE_URL . '/theme/vendor/datatables/js/buttons.print.min.js'; ?>"></script>

    <!-- PDF/Excel dependencies -->
     
    <script src="<?php echo BASE_URL . '/theme/vendor/datatables/js/jszip.min.js'; ?>"></script>
    <script src="<?php echo BASE_URL . '/theme/vendor/datatables/js/pdfmake.min.js'; ?>"></script>
    <script src="<?php echo BASE_URL . '/theme/vendor/datatables/js/pdfmake.min.js'; ?>"></script>
    <script src="<?php echo BASE_URL . '/theme/vendor/datatables/js/vfs_fonts.js'; ?>"></script>

    <!-- Chart piety plugin files -->
    <script src="<?php echo BASE_URL . '/theme/vendor/peity/jquery.peity.min.js'; ?>"></script>

    <!-- Dashboard 1 -->
    <!-- <script src="<?php echo BASE_URL . '/theme/js/dashboard/dashboard-1.js'; ?>"></script>
    <script src="<?php echo BASE_URL . '/theme/vendor/owl-carousel/owl.carousel.js'; ?>"></script> -->

    <!-- Select 2 -->
    <script src="<?= BASE_URL . '/theme/vendor/select2/js/select2.full.min.js'; ?>"></script>
    <script src="<?= BASE_URL . '/theme/js/plugins-init/select2-init.js'; ?>"></script>
    
    <!-- Toastr -->
    <script src="<?php echo BASE_URL . '/theme/vendor/toastr/js/toastr.min.js'; ?>"></script>
    <!-- <script src="https:/cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script> -->
    
    <script>
        // Override Toastr default options globally
        toastr.options.closeButton = true;
        toastr.options.debug = false;
        toastr.options.newestOnTop = true;
        toastr.options.progressBar = true;
        toastr.options.positionClass = 'toast-top-full-width'; // 👈 default placement
        toastr.options.preventDuplicates = false;
        toastr.options.onclick = null;
        toastr.options.showDuration = '300';   // fadeIn time (ms)
        toastr.options.hideDuration = '1000';  // fadeOut time (ms)
        toastr.options.timeOut = '5000';       // auto-dismiss delay (ms)
        toastr.options.extendedTimeOut = '1000'; // mouse hover delay (ms)
        toastr.options.showEasing = 'swing';
        toastr.options.hideEasing = 'linear';
        toastr.options.showMethod = 'fadeIn';
        toastr.options.hideMethod = 'fadeOut'
    </script>


    <script src="<?php echo BASE_URL . '/theme/js/custom.min.js'; ?>"></script>
    <script src="<?php echo BASE_URL . '/theme/js/dlabnav-init.js'; ?>"></script>

    
    <!-- Custom JavaScript -->
    <script type="text/javascript">
        $(function() {
            $(".preloader").fadeOut();
        });
    </script>

    <!-- Scroll to Top -->
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const scrollBtn = document.querySelector(".scrollToTop");

            // Show button when user scrolls down 200px
            window.addEventListener("scroll", () => {
            if (window.scrollY > 200) {
                scrollBtn.classList.add("show");
            } else {
                scrollBtn.classList.remove("show");
            }
            });

            // Smooth scroll to top
            scrollBtn.addEventListener("click", () => {
            window.scrollTo({
                top: 0,
                behavior: "smooth"
            });
            });
        });
    </script>

    <!-- Progress Bar -->
    <script>
        window.addEventListener("scroll", function () {
            const scrollTop = document.documentElement.scrollTop || document.body.scrollTop;
            const scrollHeight = document.documentElement.scrollHeight - document.documentElement.clientHeight;
            const progress = (scrollTop / scrollHeight) * 100;
            document.getElementById("scrollProgress").style.width = progress + "%";
        });
    </script>

    <!-- Fullscreen Toggle -->
    <script>
        document.getElementById("fullscreenBtn").addEventListener("click", function () {
            if (!document.fullscreenElement) {
                document.documentElement.requestFullscreen().catch((err) => {
                console.warn(`Error attempting fullscreen: ${err.message}`);
                });
            } else {
                if (document.exitFullscreen) {
                document.exitFullscreen();
                }
            }
            });

            const fsBtn = document.getElementById("fullscreenBtn");
            const fsIcon = fsBtn.querySelector("i");

            document.addEventListener("fullscreenchange", () => {
            if (document.fullscreenElement) {
                fsIcon.classList.remove("ti-fullscreen");
                fsIcon.classList.add("ti-arrows-corner");
            } else {
                fsIcon.classList.remove("ti-fullscreen-exit");
                fsIcon.classList.add("ti-fullscreen");
            }
        });
    </script>

    <!-- SESSION TIMEOUT -->
    <script>
        (
        function () {
            let idleSeconds = 0;
            // let idleThreshold = 5; // 60 * 10; // 10 minutes inactivity before warning
            // let countdownSeconds = 5; //;   // time before auto logout once modal shows
            let idleThreshold = window.appConfig.idleThreshold;
            let countdownSeconds = window.appConfig.countdownSeconds;
            let countdownTimer, idleTimer;

            const modal = new bootstrap.Modal(document.getElementById('idle-timeout-dialog'));
            const counterEl = document.getElementById('idle-timeout-counter');
            const progressBar = document.querySelector('#idle-timeout-dialog .progress-bar');

            // Reset idle time on activity
            function resetIdleTimer() {
                idleSeconds = 0;
            }
            ['mousemove','keypress','click','scroll'].forEach(evt => {
                document.addEventListener(evt, resetIdleTimer);
            });

            // Check idle every second
            idleTimer = setInterval(() => {
                idleSeconds++;
                if (idleSeconds >= idleThreshold) {
                    showTimeoutWarning();
                }
            }, 1000);

            function showTimeoutWarning() {
                idleSeconds = 0; // reset
                modal.show();

                let remaining = countdownSeconds;
                counterEl.textContent = remaining;
                updateProgress(remaining);

                countdownTimer = setInterval(() => {
                    remaining--;
                    counterEl.textContent = remaining;
                    updateProgress(remaining);

                    if (remaining <= 0) {
                        clearInterval(countdownTimer);
                        window.location = "<?php echo BASE_URL; ?>/auth/logout.php";
                    }
                }, 1000);
            }

            function updateProgress(secondsLeft) {
                let percent = (secondsLeft / countdownSeconds) * 100;
                progressBar.style.width = percent + "%";
            }

            // Stay Connected button
            document.getElementById('idle-timeout-dialog-keepalive').addEventListener('click', () => {
                clearInterval(countdownTimer);
                idleSeconds = 0; // reset idle
            });
        })();
    </script>


    <!-- Toastr -->
    <?php \App\Classes\Toast::render(); ?>