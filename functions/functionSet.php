<?php

    // ========================
    /* Mobile Number Privacy */
    function mobile_number_format(string $mobile, string $type) {
        if (strlen($mobile) == '13' AND substr($mobile, 0, 4) == '+639') { 
            if ($type == 'showMobile') {           // Show Correct Mobile Number
                $result = '+63 ('.substr($mobile, 3, 3).') '.substr($mobile, 7,3).'-'.substr($mobile, -4);
            } elseif ($type == 'showMasked') {     // Show Masked
                $result = '+63 ('.substr($mobile, 3, 3).') '.' ***-****';
            } elseif ($type == 'showRandom') {     // Show Random
                $result = '+63 ('.substr($mobile, 3, 3).') '.substr(rand(123, 789) * time(), 0, 3).'-'.substr(rand(0000, 9999) * time(), 0, 4);
            }
        } else {

            $result = "Unknown";
        } 
        return $result;
    }

    
    // ========================
    /* eMail Address Privacy */
    function mask_email_address(string $email_address) {
        $result = substr(strtolower($email_address),0,4).'***@***.***';
        return $result;
    }


    // ========================
    /* eMail Address Privacy */
    function obscure_string($input) {
        // Get the length of the string
        $length = strlen($input);
        
        // If the string length is less than or equal to 2, return it as is
        if ($length <= 2) {
            return $input;
        }
        
        // Get the first and last characters
        $first_char = $input[0];
        $last_char = $input[$length - 1];
        
        // Create a string of asterisks of the appropriate length
        $middle_part = str_repeat('*', $length - 2);
        
        // Concatenate the first character, middle part, and last character
        $obscured_string = $first_char . $middle_part . $last_char;
        
        return $obscured_string;
    }

    // ========================
    /* replacement for htmlspecialchars */
    function formatValues(
        $value,
        ?string $type = null,                        // 'string', 'number', 'datetime' or auto-detect
        array $options = []                          // extra options depending on type
    ) {
        if ($value === null || $value === '') {
            return '';
        }

        // --- Auto-detect type ---
        if ($type === null) {
            if (is_numeric($value)) {
                $type = 'number';
            } elseif (strtotime((string)$value) !== false) {
                $type = 'datetime';
            } else {
                $type = 'string';
            }
        }

        switch ($type) {
            case 'number':
                $decimals = $options['decimals'] ?? 0;
                $decimal_separator = $options['decimal_separator'] ?? '.';
                $thousands_separator = $options['thousands_separator'] ?? ',';
                return number_format((float)$value, $decimals, $decimal_separator, $thousands_separator);

            case 'datetime':
                $timezone   = $options['timezone']   ?? 'Asia/Manila';
                $dateFormat = $options['dateFormat'] ?? 'Y-m-d';
                $timeFormat = $options['timeFormat'] ?? 'H:i:s';
                $separator  = $options['separator']  ?? '<br>';
                $only       = strtolower($options['only'] ?? ''); // 'date' | 'time'
                $dateTag    = $options['dateTag']    ?? null;     // e.g. ['b'] or ['span','class="date"']
                $timeTag    = $options['timeTag']    ?? null;     // e.g. ['i'] or ['small','style="color:gray"']

                try {
                    $dt = new DateTime($value, new DateTimeZone('UTC')); // stored in UTC
                    $dt->setTimezone(new DateTimeZone($timezone));

                    $date = htmlspecialchars($dt->format($dateFormat), ENT_QUOTES, 'UTF-8');
                    $time = htmlspecialchars($dt->format($timeFormat), ENT_QUOTES, 'UTF-8');

                    // Wrap with tags if provided
                    if ($dateTag) {
                        $tag = is_array($dateTag) ? $dateTag[0] : $dateTag;
                        $attrs = is_array($dateTag) && isset($dateTag[1]) ? ' ' . $dateTag[1] : '';
                        $date = "<{$tag}{$attrs}>{$date}</{$tag}>";
                    }

                    if ($timeTag) {
                        $tag = is_array($timeTag) ? $timeTag[0] : $timeTag;
                        $attrs = is_array($timeTag) && isset($timeTag[1]) ? ' ' . $timeTag[1] : '';
                        $time = "<{$tag}{$attrs}>{$time}</{$tag}>";
                    }

                    if ($only === 'date') {
                        return $date;
                    } elseif ($only === 'time') {
                        return $time;
                    }

                    return $date . $separator . $time;
                } catch (Exception $e) {
                    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
                }

            case 'string':
            default:
                return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
        }
    }



    function server_execution_time_placeholder() {
        echo "<a href='#' title='Execution Time | Memory Usage | Cache Hit' 
                class='btn waves-effect waves-light btn-outline-dark text-white' role='button'>
                <i class='ti-server text-warning'></i>&nbsp;
                <span id='execTime'>Admin block initialized.</span>
            </a>&nbsp;";
    }
        

    // ================================================================================
    /* Datatables footer buttons (Server Execution Time & Latest File Modifications) */
    function server_execution_time($sqlTimeLap) {

        echo "<a href='#' title='Server Execution Time' class='btn waves-effect waves-light btn-outline-dark text-white' role='button'>
        <i class='ti-server text-warning'></i>&nbsp;".
        number_format($sqlTimeLap,4,'.',',')."&nbsp;ms</a>&nbsp";
        return;
    }
    function latest_modification($varFile) {
        if ($_SESSION['rbac']['role_id'] == "1")  {	
            echo "<a href='#' title='Latest Modification: ($varFile)' class='btn waves-effect waves-light btn-outline-dark text-white' role='button'>
                <i class='ti-hummer text-warning'></i>&nbsp;".
                gmdate("Y-m-d h:i:s A", filemtime($varFile))."</a>"; 
            return;
        }
    }
    function hsc($value) {
        return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
    }
    function formatLocalTime($utcDateTime, $timezone = 'Asia/Manila') {
        if (!$utcDateTime) return ['', ''];

        $dt = new DateTime($utcDateTime, new DateTimeZone('UTC')); // stored in UTC
        $dt->setTimezone(new DateTimeZone($timezone));             // convert to local

        return [
            'date' => $dt->format('Y-m-d'),
            'time' => $dt->format('H:i:s')
        ];
    }
    function localToUtc($localDateTime, $tz = TIMEZONE) {
        $dt = new DateTime($localDateTime, new DateTimeZone($tz));
        $dt->setTimezone(new DateTimeZone('UTC'));
        return $dt->format('Y-m-d H:i:s');
    }


    // ============================
    /* SMS PRIME SCHOOL SENDER ID*/ 
    function sendText_prime($iuser,$ipass,$mobile_no,$msg) {
		$Uiun = urlencode($iuser);
		$Uipw = urlencode($ipass);
		$Umob = urlencode($mobile_no);
		$Umsg = urlencode($msg);
        $result = getContent_prime('https://www.isms.com.my/isms_send_all_id.php?un='.$Uiun.'&pwd='.$Uipw.'&dstno='.$Umob.'&msg='.$Umsg.'&type=1&sendid=PRIMESCHOOL&agreedterm=YES');
        return $result;
	}
	function getContent_prime($url) {
		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
		$data = curl_exec($ch);
		curl_close($ch);
		return $data;
	}

    function save_mail($mail) {
        // You can change 'Sent Mail' to any other folder or tag
        $path = '{imap.gmail.com:993/imap/ssl}[Gmail]/Sent Mail';

        //Tell your server to open an IMAP connection using the same username and password as you used for SMTP
        $imapStream = imap_open($path, $mail->Username, $mail->Password);
        $result = imap_append($imapStream, $path, $mail->getSentMIMEMessage());
        imap_close($imapStream);
        return $result;
    }

