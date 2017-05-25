<?php

	/** Campaigns API - Add a new Campaign */
	/**
	 * Generates action circle buttons for different pages/module
	 * @param goUser
	 * @param goPass
	 * @param goAction
	 * @param responsetype
	 * @param hostname
	 * @param campaign_id
	 */
  require_once('goCRMAPISettings.php');

  $url = gourl."/goCustomFields/goAPI.php"; #URL to GoAutoDial API. (required)
  $postfields["goUser"] = goUser; #Username goes here. (required)
  $postfields["goPass"] = goPass; #Password goes here. (required)
  $postfields["goAction"] = "goModifyCustomField"; #action performed by the [[API:Functions]]. (required)
  $postfields["responsetype"] = responsetype; #json. (required)
	$postfields["hostname"] = $_SERVER['REMOTE_ADDR'];
	$postfields["list_id"] 								= $_POST['list_id'];
	$postfields["field_id"] 							= $_POST['field_id'];
	$postfields["field_name"] 						= $_POST['field_name'];
	$postfields["field_rank"] 						= $_POST['field_rank'];
	$postfields["field_order"] 						= $_POST['field_order'];
	$postfields["field_label"] 						= $_POST['field_label'];
	$postfields["field_label_old"] 				= $_POST['field_label_old'];
	$postfields["name_position"] 					= $_POST['field_position'];
	$postfields["field_description"] 			= $_POST['field_description'];
	$postfields["field_type"] 						= $_POST['field_type'];
	$postfields["field_options"] 					= $_POST['field_options'];
	$postfields["multi_position"] 				= $_POST['field_option_position'];
	$postfields["field_size"] 						= $_POST['field_size'];
	$postfields["field_max"] 							= $_POST['field_max'];
	$postfields["field_default"] 					= $_POST['field_default'];
	$postfields["field_required"] 				= $_POST['field_required'];
	
	$postfields["log_user"]								= $_POST['log_user'];
	$postfields["log_group"]							= $_POST['log_group'];

  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $url);
  curl_setopt($ch, CURLOPT_POST, 1);
  curl_setopt($ch, CURLOPT_TIMEOUT, 100);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_POSTFIELDS, $postfields);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  $data = curl_exec($ch);
  curl_close($ch);
  $output = json_decode($data);

	if ($output->result=="success") {
		# Result was OK!
		$status = "success";
	} else {
		# An error occured
		$status = "error";
	}

	echo $status;
#var_dump($output);
?>
