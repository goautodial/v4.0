<?php

	/** Telephony Users API - Add a new Telephony User */
	/**
	 * Generates action circle buttons for different pages/module
	 */
$validate = 0;	
    if($_POST['phone_logins'] == ""){
        $validate = 1;
    }

if($validate == 0){
	$url = "http://gadcs.goautodial.com/goAPI/goUsers/goAPI.php"; # URL to GoAutoDial API file
	$postfields["goUser"] 			= "admin"; #Username goes here. (required)
	$postfields["goPass"] 			= "kam0teque1234"; #Password goes here. (required)
	$postfields["goAction"] 		= "goAddUser"; #action performed by the [[API:Functions]]
	$postfields["responsetype"] 	= "json"; #json (required)
	$postfields["hostname"] 		= $_SERVER['REMOTE_ADDR']; #Default value
	$postfields["user"] 			= $_POST['user_form']; 
	$postfields["pass"] 			= $_POST['password']; 
	$postfields["full_name"] 		= $_POST['fullname']; 
	$postfields["user_group"] 		= $_POST['user_group']; 
	$postfields["active"] 			= $_POST['status']; 
	

	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	// curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_TIMEOUT, 100);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $postfields);
	$data = curl_exec($ch);
	curl_close($ch);

	$output = json_decode($data);
	
	if ($output->result=="success") {
		# Result was OK!
		$status = 1;
		//$return['msg'] = "New User has been successfully saved.";
	} else {
		# An error occured
		//$status = 0;
		// $return['msg'] = "Something went wrong please see input data on form.";
        $status = $output->result;
	}

	echo $status;
}
?>