<?php
/** Telephony Users API - Add a new Telephony User */
/**
 * Generates action circle buttons for different pages/module
 */
require_once('goCRMAPISettings.php');	

	$url = gourl."/goUserGroups/goAPI.php"; # URL to GoAutoDial API file
	
	$postfields = array(
		'goUser' => goUser,
		'goPass' => goPass,
		'goAction' => 'goAddUserGroup',		
		'responsetype' => responsetype,
		'user_group' => $_POST['usergroup_id'],
		'group_name' => $_POST['groupname'],
		'group_level' => $_POST['grouplevel'],
		'session_user' => $_POST['log_user'],
		'log_group' => $_POST['log_group'],
		'hostname' => $_SERVER['REMOTE_ADDR']
	);		

	// Call the API
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_TIMEOUT, 30);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
	curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postfields));
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

?>
