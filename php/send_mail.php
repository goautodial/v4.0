<?php
//ini_set('display_errors', 1);
//ini_set('display_startup_errors', 1);
//error_reporting(E_ALL);
include_once('smtp_settings.php');
require_once('DbHandler.php');
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
	$touserid_getinfo = goGetUserInfo($touserid, "user_id", "userInfo");
	$touserid_email = $touserid_getinfo->data->email;
	$touserid_username = $touserid_getinfo->data->user;
	$touserid_fullname = $touserid_getinfo->data->full_name;
	
	// get user info of who the message is to be sent from
	$fromuserid_getinfo = goGetUserInfo($fromuserid, "user_id", "userInfo");
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

// get user info
	function goGetUserInfo($userid, $type, $filter){
		$url = gourl."/goUsers/goAPI.php"; #URL to GoAutoDial API. (required)
		$postfields["goUser"] = goUser; #Username goes here. (required)
		$postfields["goPass"] = goPass; #Password goes here. (required)
		$postfields["goAction"] = "goGetUserInfo"; #action performed by the [[API:Functions]]. (required)
		$postfields["responsetype"] = responsetype; #json. (required)
		if ($type == "user") {
			$postfields["user"] = $userid; #Desired User ID (required)
		} else {
			$postfields["user_id"] = $userid; #Desired User (required)
		}
		if($filter == "userInfo"){
			$postfields["filter"] = $filter;
		}
		
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_TIMEOUT, 100);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $postfields);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		$data = curl_exec($ch);
		curl_close($ch);

		$output = json_decode($data);

		return $output;
	}
?>