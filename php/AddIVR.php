<?php

	/** Telephony Callmenu API - Add a new Telephony Callmenu */
	/**
	 * Generates action circle buttons for different pages/module
	 */
require_once('goCRMAPISettings.php');

	$url = gourl."/goInbound/goAPI.php"; # URL to GoAutoDial API file
	$postfields["goUser"] 			= goUser; #Username goes here. (required)
	$postfields["goPass"] 			= goPass; #Password goes here. (required)
	$postfields["goAction"] 		= "goAddIVRmenu"; #action performed by the [[API:Functions]]
	$postfields["responsetype"] 	= responsetype; #json (required)
	$postfields["hostname"] 		= $_SERVER['REMOTE_ADDR']; #Default value

    $postfields['menu_id'] = $_POST['menu_id'];
	$postfields['menu_name'] = $_POST['menu_name'];
	$postfields['user_group'] = $_POST['user_groups'];
	$postfields['menu_prompt'] = $_POST['menu_prompt'];
	$postfields['menu_timeout'] = $_POST['menu_timeout'];
	$postfields['menu_timeout_prompt'] = $_POST['menu_timeout_prompt'];
	$postfields['menu_invalid_prompt'] = $_POST['menu_invalid_prompt'];
	$postfields['menu_repeat'] = $_POST['menu_repeat'];
	$postfields['postfields'] = $_POST['menu_time_check'];
	$postfields['call_time_id'] = $_POST['call_time_id'];
	$postfields['track_in_vdac'] = $_POST['track_in_vdac'];
	$postfields['custom_dialplan_entry'] = $_POST['custom_dialplan_entry'];
	$postfields['tracking_group'] = $_POST['tracking_group'];
    
	$route_option = $_POST['option'];
	$route_option = array_diff($route_option, array( '' ));
	
	$route_desc = $_POST['route_desc'];
	$route_desc = array_diff($route_desc, array( '' ));
	
	$route_menu = $_POST['route_menu'];
	$route_menu = array_diff($route_menu, array( '' ));
	
	$option_route_value = $_POST['option_route_value'];
	$option_route_value = array_diff($option_route_value, array( '' ));
	
	$option_route_context = $_POST['option_route_value_context'];
	$option_route_context = array_diff($option_route_context, array( '' ));
	
	for($i=0;$i < count($route_option);$i++){
		$array[] = array("option" => $route_option[$i], "desc" => $route_desc[$i], "menu" => $route_menu[$i], "option_value" => $option_route_value, "option_context" => $option_route_context[$i]);
	}
	$filtered_array = array_diff($array, array( '' ));
	
	$imploded_option = implode("+", array_column($filtered_array, 'option'));
	$imploded_desc = implode("+", array_column($filtered_array, 'desc'));
	$imploded_menu = implode("+", array_column($filtered_array, 'menu'));
	$imploded_value = implode("+", $option_route_value);
	$imploded_context = implode("+", $option_route_context);
	
	$postfields['option'] = $imploded_option;
	$postfields['option_desc'] = $imploded_desc;
	$postfields['option_menu'] = $imploded_menu;
	$postfields['option_value'] = $imploded_value;
	$postfields['option_context'] = $imploded_context;
	
	//echo "<pre>";
	//var_dump($imploded_value);
	//echo "</pre>";
    
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
		$status = "success";
		//$return['msg'] = "New User has been successfully saved.";
	} else {
		# An error occured
		//$status = 0;
		// $return['msg'] = "Something went wrong please see input data on form.";
        $status = $output->result;
	}

	echo $status;

?>