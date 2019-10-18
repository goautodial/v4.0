<?php

	require_once('goCRMAPISettings.php');
	require_once('../phpmailer/PHPMailerAutoload.php');
	require_once('../phpmailer/class.phpmailer.php');
	require_once('../phpmailer/class.smtp.php');
	require_once('APIHandler.php');
//      include_once('../phpmailer/info.php');
        $api = \creamy\APIHandler::getInstance();

        // API to get SMTP Credentials
                $postfields["goAction"] = "goGetSMTPSettings"; #action performed by the [[API:Functions]]. (required)

                $output = $api->API_Request("goSMTP", $postfields);	
		
		if($output->result == "success"){
			date_default_timezone_set($output->data->timezone);
			
			//Create a new PHPMailer instance
			$mail = new PHPMailer;
			
			//Tell PHPMailer to use SMTP
			$mail->isSMTP();
			
			//Enable SMTP debugging
			// 0 = off (for production use)
			// 1 = client messages
			// 2 = client and server messages
			$mail->SMTPDebug = $output->data->debug;
			
			//Ask for HTML-friendly debug output
			$mail->Debugoutput = 'html';
			
			if($output->data->ipv6_support == 1)
				$mail->Host = $output->data->host; //Set the hostname of the mail server
			else
				$mail->Host = gethostbyname($output->data->host); // if your network does not support SMTP over IPv6
			
			//Set the SMTP port number - 587 for authenticated TLS, a.k.a. RFC4409 SMTP submission
			$mail->Port = $output->data->port;
			
			//Set the encryption system to use - ssl (deprecated) or tls
			$mail->SMTPSecure = $output->data->smtp_security;
			
			//Whether to use SMTP authentication
			$mail->SMTPAuth = $output->data->smtp_auth;
			
			//Username to use for SMTP authentication - use full email address for gmail
			$mail->Username = $output->data->username;
			
			$password_enc = $output->data->password;
			$password = encrypt_decrypt('decrypt', $password_enc);
			
			//Password to use for SMTP authentication
			$mail->Password = $password;
		}else{
			$status = "error";
		}
		
	function encrypt_decrypt($action, $string) {
	        //$output = false;

        	$encrypt_method = "AES-256-CBC";
	        $secret_key = 'This is my secret key';
        	$secret_iv = 'This is my secret iv';

        	// hash
	        $key = hash('sha256', $secret_key);

        	// iv - encrypt method AES-256-CBC expects 16 bytes - else you will get a warning
	        $iv = substr(hash('sha256', $secret_iv), 0, 16);

        	if( $action == 'encrypt' ) {
	            $output = openssl_encrypt($string, $encrypt_method, $key, 0, $iv);
        	    $output = base64_encode($output);
	        }
        	else if( $action == 'decrypt' ){
	            $output = openssl_decrypt(base64_decode($string), $encrypt_method, $key, 0, $iv);
        	}
        	return $output;
    	}
?>
