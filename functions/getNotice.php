<?php
    // MAIN INDEX NOTICES FUNCTION
    /* parameters are sent via config.php */
    function getNotice($what, $note, $when) {

        isset($note) ? $note : NULL;
        isset($when) ? $when : NULL;
        switch ($what) {
            case 'welcome'  : echo ("<div class='text-primary text-sm'><code>".getWelcome(1,13)."</code></div>"); break;
            case 'schedule' : echo ("<div class='text-info text-sm'><b>System Maintenance Schedule</b></div><small class='text-info text-xs'>".$note."<br>".$when."</small>"); break;
            case 'ongoing'  : echo ("<div class='text-danger text-sm'><b>Temporary Service Disruption</b></div><small class='text-danger text-xs'>".$note."<br>".$when."</small>"); break;
            return;
        }
    }