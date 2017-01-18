<?php

	/** Campaigns API - Add a new Campaign */
	/**
	 * Generates action circle buttons for different pages/module
	 * @param goUser
	 * @param goPass
	 * @param goAction
	 * @param responsetype
	 * @param hostname
	 * @param did_pattern
	 * @param group_color
	 * @param call_route
	 * @param survey_type
	 * @param number_channels
	 * @param campaign_type
	 * @param campaign_id
	 * @param campaign_name
	 */

    require_once('goCRMAPISettings.php');

	$url = gourl."/goCampaigns/goAPI.php"; # URL to GoAutoDial API file
	$postfields["goUser"] 						= goUser; #Username goes here. (required)
	$postfields["goPass"] 						= goPass; #Password goes here. (required)
	$postfields["goAction"] 					= "goAddCampaign"; #action performed by the [[API:Functions]]
	$postfields["responsetype"] 				= responsetype; #json (required)
	$postfields["hostname"] 					= $_SERVER['REMOTE_ADDR']; #Default value

	$postfields['campaign_type'] 				= $_POST['campaign_type'];
	$postfields['campaign_id']  				= $_POST['campaign_id'];
	$postfields['campaign_name'] 				= $_POST['campaign_name'];
	$postfields['did_tfn_extension']  			= $_POST['did_tfn_extension'];
	$postfields['call_route'] 					= $_POST['call_route'];
    switch ($_POST['call_route']){
        case "INGROUP":
            $postfields['call_route_text'] 		= $_POST['ingroup_text'];
        break;

        case "IVR":
            $postfields['call_route_text'] 		= $_POST['ivr_text'];
        break;

        case "AGENT":
            $postfields['call_route_text'] 		= $_POST['agent_text'];
        break;

        case "VOICEMAIL":
            $postfields['call_route_text'] 		= $_POST['voicemail_text'];
        break;
    }
	$postfields['group_color'] 					= $_POST['group_color'];
	$postfields['survey_type'] 					= $_POST['survey_type'];
	$postfields['no_channels']  				= $_POST['no_channels'];
	$postfields['copy_from_campaign'] 			= $_POST['copy_from_campaign'];
	$postfields['list_id'] 						= $_POST['list_id'];
	$postfields['country'] 						= $_POST['country'];
	$postfields['check_for_duplicates'] 		= $_POST['check_for_duplicates'];
	$postfields['dial_method'] 					= $_POST['dial_method'];
	$postfields['auto_dial_level'] 				= (!isset($_POST['auto_dial_level']))? 'OFF':$_POST['auto_dial_level'];
    $postfields["auto_dial_level_adv"] 			= $_POST["auto_dial_level_adv"];
	$postfields['dial_prefix']					= (!isset($_POST['dial_prefix']))? 9:$_POST['dial_prefix'];
	$postfields['custom_dial_prefix']			= ($postfields['dial_prefix'] == "CUSTOM")? $_POST['custom_prefix']:0;
	$postfields['description'] 					= $_POST['description'];
	$postfields['status'] 						= $_POST['status'];
	$postfields['script'] 						= $_POST['script'];
	$postfields['answering_machine_detection'] 	= $_POST['answering_machine_detection'];
	$postfields['caller_id']  					= $_POST['caller_id'];
	$postfields['force_reset_hopper'] 			= $_POST['force_reset_hopper'];
	$postfields['campaign_recording'] 			= $_POST['campaign_recording'];
	$postfields['inbound_man'] 					= $_POST['inbound_man'];
	$postfields['phone_numbers'] 				= $_POST['phone_numbers'];
	if(!empty($_FILES["lead_file"]["name"])) $postfields['lead_file'] = curl_file_create($_FILES['lead_file']['tmp_name'], $_FILES['lead_file']['type'], $_FILES["lead_file"]["name"]);
	if(!empty($_FILES["leads"]["name"])) $postfields['leads'] = curl_file_create($_FILES['leads']['tmp_name'], $_FILES['leads']['type'], $_FILES["leads"]["name"]);
    if(!empty($_FILES["uploaded_wav"]["name"])) $postfields['uploaded_wav'] = curl_file_create($_FILES['uploaded_wav']['tmp_name'], $_FILES['uploaded_wav']['type'], $_FILES["uploaded_wav"]["name"]);

	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	// curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_TIMEOUT, 100);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $postfields);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
	$data = curl_exec($ch);

	curl_close($ch);

	$output = json_decode($data);
    //echo "<pre>";
    //print_r($postfields);die;
	$home = $_SERVER['HTTP_REFERER'];
	if ($output->result=="success") {
		# Result was OK!
		// $status = 1;
		// $return['msg'] = "New Campaign has been successfully saved.";
		$url = str_replace("?message=Success", "", $home);
		header("Location: ".$url."?message=Success");
	} else {
		# An error occured
		// $status = 0;
		// $return['msg'] = "Something went wrong please see input data on form.";
		$url = str_replace("?message=Error", "", $home);
		header("Location: ".$url."?message=Error");
	}

?>
