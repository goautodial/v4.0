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
   $url = gourl."/goVoiceFiles/goAPI.php"; #URL to GoAutoDial API. (required)

   $postfields["goUser"]         = goUser; #Username goes here. (required)
   $postfields["goPass"]         = goPass; #Password goes here. (required)
   $postfields["goAction"]       = "goAddVoiceFiles"; #action performed by the [[API:Functions]]. (required)
   $postfields["responsetype"]   = responsetype; #json. (required)
   $postfields["hostname"]       = $_SERVER['REMOTE_ADDR']; #Default value
   $postfields["files"]          = curl_file_create($_FILES['voice_file']['tmp_name'], $_FILES['voice_file']['type'], $_FILES["voice_file"]["name"]);
   $postfields["stage"]          = "upload";

   $ch = curl_init();
   curl_setopt($ch, CURLOPT_URL, $url);
   curl_setopt($ch, CURLOPT_POST, 1);
   curl_setopt($ch, CURLOPT_TIMEOUT, 100);
   curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
   curl_setopt($ch, CURLOPT_POSTFIELDS, $postfields);
   $data = curl_exec($ch);
   curl_close($ch);

   $output = json_decode($data);

   header("location: ../audiofiles.php?upload_result=".$output->result);
