<?php 

	/** LoadLeads API - load leads */
	/**
	 * Generates action circle buttons for different pages/module
	 * @param goUser 
	 * @param goPass 
	 * @param goAction 
	 * @param responsetype
	 * @param files
	 */
ini_set('memory_limit','1024M');
	ini_set('upload_max_filesize', '6000M');
	ini_set('post_max_size', '6000M');
	
	require_once('goCRMAPISettings.php');
	require_once('CRMDefaults.php');
	
	// print_r($_FILES['file_upload']);die();
	$url = gourl."/goUploadLeads/goAPI.php"; #URL to GoAutoDial API. (required)
	
	$postfields["goUser"]         = goUser; #Username goes here. (required)
	$postfields["goPass"]         = goPass; #Password goes here. (required)
	$postfields["goAction"]       = "goUploadMe"; #action performed by the [[API:Functions]]. (required)
	$postfields["responsetype"]   = responsetype; #json. (required)
	$postfields["hostname"]       = $_SERVER['REMOTE_ADDR']; #Default value
	$postfields["goFileMe"]       = curl_file_create($_FILES['file_upload']['tmp_name'], $_FILES['file_upload']['type'], $_FILES["file_upload"]["name"]);
	$postfields["goListId"]       = $_REQUEST['list_id'];
	$postfields["goDupcheck"]       = $_REQUEST['goDupcheck'];
	$postfields["log_user"]		  = $_REQUEST['log_user'];
	$postfields["log_group"]	  = $_REQUEST['log_group'];
	$home = "../telephonylist.php";
	
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_TIMEOUT, 100);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $postfields);
	curl_setopt ($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT , 0); //gg
	curl_setopt ($ch, CURLOPT_TIMEOUT  , 10000); //gg
	$data = curl_exec($ch);
	
	curl_close($ch);
	$output = json_decode($data);
	
	if ($output->result == "success") {
		
		//header("Location: ".$home."?message=success&RetMesg=".$output->message);
		$status = $output->message;
	} else {
		
		#header("Location: ".$home."?message=error&RetMesg=".$output->message);
		$status = $output->message;
		
	}
	
	echo $status;

?>