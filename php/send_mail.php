<?php
//ini_set('display_errors', 1);
//ini_set('display_startup_errors', 1);
//error_reporting(E_ALL);
include_once('smtp_settings.php');
require_once('DbHandler.php');
require_once('APIHandler.php');

// check required fields
$validated = 1;
if (!isset($_POST["touserid"])) {
	$validated = 0;
}
if (!isset($_POST["fromuserid"])) {
	$validated = 0;
	
}
if (!isset($_POST["message"])) {
	$validated = 0;
}
if (!isset($_POST["subject"])) {
	$validated = 0;
}

if ($validated == 1) {
	$db = new \creamy\DbHandler();
	$api = \creamy\APIHandler::getInstance();

	// message parameters	
	$touserid = $_POST["touserid"];
	$fromuserid = $_POST["fromuserid"];
	$subject = $_POST["subject"];
	$message = $_POST["message"];
	if (isset($_POST["external_recipients"])) {
		$external_recipients = $_POST["external_recipients"];
		$external_recipients = ltrim($external_recipients, " [");
		$external_recipients = rtrim($external_recipients, " ]");
	} else { $external_recipients = null; }
	
	// get user info of who the message is to be sent to
	$touserid_getinfo = $api->API_getUserInfo(NULL, "userInfo",$touserid);
	$touserid_email = $touserid_getinfo->data->email;
	$touserid_username = $touserid_getinfo->data->user;
	$touserid_fullname = $touserid_getinfo->data->full_name;
	
	// get user info of who the message is to be sent from
	$fromuserid_getinfo = $api->API_getUserInfo(NULL, "userInfo", $fromuserid);
	$fromuserid_email = $fromuserid_getinfo->data->email;
	$fromuserid_username = $fromuserid_getinfo->data->user;
	$fromuserid_fullname = $fromuserid_getinfo->data->full_name;
	
	// if from email doesn't exist
	if($fromuserid_email == NULL || $fromuserid_email == ""){
		$fromuserid_email = $fromuserid_username.'-default@goautodial.com';
	}
	
	//Set who the message is to be sent from
	$mail->setFrom($fromuserid_email, $fromuserid_fullname);
	
	//Set an alternative reply-to address
	//$mail->addReplyTo('alex@goautodial.com', 'Alex Gwapo');
	if($external_recipients != NULL){
		$external_recipients_filter = str_replace('"','',$external_recipients);
		$external_recipients = explode(",", $external_recipients_filter);
		
		for($i=0;$i < count($external_recipients);$i++){
			$mail->addAddress($external_recipients[$i], $external_recipients[$i]);
			$result = $db->SMTPsendMessage($fromuserid, '0', $subject, $message, $_FILES, $external_recipients, "attachment");
		}
	}else{
		$result = $db->SMTPsendMessage($fromuserid, $touserid, $subject, $message, $_FILES, $external_recipients, "attachment");
	}
	
	//Set who the message is to be sent to
	if($touserid_email != NULL)
	$mail->addAddress($touserid_email, $touserid_fullname);
	
	//Set the subject line
	$mail->Subject = $subject;
	
	//Read an HTML message body from an external file, convert referenced images to embedded,
	//convert HTML into a basic plain-text alternative body
	//$mail->msgHTML(file_get_contents('contents.html'), dirname(__FILE__));
	
	$mail->Body = $message;
	//Replace the plain text body with one created manually
	$mail->AltBody = 'This is a message from: '.$fromuserid;
	
	//OVERRIDE CONNECTION FAILURE
	$mail->SMTPOptions = array(
	   	'ssl' => array(
        	'verify_peer' => false,
	        'verify_peer_name' => false,
        	'allow_self_signed' => true
    		)
	);
	
	//Attach an image file
	//$mail->addAttachment('../../phpmailer/examples/images/phpmailer_mini.png');
	
	//send the message, check for errors
	if (!$mail->send()) {
		if($touserid_email == "" || $touserid_email == NULL || $external_recipients_filter == NULL || $external_recipients_filter == "")
			echo "no email account";
		else
			echo $mail->ErrorInfo;
	} else {
		echo "success";
	}
	
}
	
?>
