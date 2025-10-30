<?php ?>
    <div class="footer">
        <div class="copyright">
            <?php echo ("<p><b>" . PROJECT . ' ' . TITLE . "</b>&nbsp;" . VERSION . " | " . COPYRIGHT . date('Y')." | <a href=''> " . AUTHOR . "</a>, All Rights Reserved.</p>"); ?>
        </div>
    </div>

    
    <!-- Scroll to Top -->
    <button type="button" 
        class="scrollToTop btn  rounded-circle shadow d-flex align-items-center justify-content-center">
        <i class="ti ti-arrow-up fs-5"></i>
    </button>

    <!-- Scroll Progress Bar -->
    <div id="scrollProgress"></div>

    <!-- Timeout Notification -->
    <div class="modal fade" id="idle-timeout-dialog" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Session Timeout Notification</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Your session is expiring soon.
                    Redirecting in <span id="idle-timeout-counter"></span> seconds.</p>
                    <div class="progress progress-lg">
                        <div class="progress-bar progress-bar-success countdown-bar active" role="progressbar" style="min-width: 15px; width: 100%;">
                            <span>&nbsp;</span> 
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button id="session-timeout-dialog-logout" type="button" class="btn btn-default" onclick="window.location='<?php echo BASE_URL; ?>/auth/logout.php'">Logout</button>
                    <button id="idle-timeout-dialog-keepalive" type="button" class="btn btn-primary" data-bs-dismiss="modal">Stay Connected</button>
                </div>
            </div>
        </div>
    </div>