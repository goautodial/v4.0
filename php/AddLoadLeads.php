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
    $postfields["goListId"]       = $_POST['list_id'];

    // print_r($postfields);die;
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    //curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_TIMEOUT, 100);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $postfields);
    $data = curl_exec($ch);
    // print_r(curl_getinfo($ch));die;
    curl_close($ch);
    
    $output = json_decode($data);

    $home = "../telephonylist.php";
   
    if ($output->result == "success") {
    	// echo "Success";
    	header("Location: ".$home."?message=Success");
    } else {
    	// echo "The following error occured: ".$output->result;
    	header("Location: ".$home."?message=Error");
    }

	print_r($data);
	die;

?>