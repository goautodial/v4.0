<?php

	/** Calltimes API - View calltime */
	/**
	 * Generates action circle buttons for different pages/module
	 * @param goUser 
	 * @param goPass 
	 * @param goAction 
	 * @param responsetype
	 * @param call_time_id
	 */
        require_once('goCRMAPISettings.php');

        $form_data = $_POST['form_data'];
        parse_str($form_data, $data_array);

        $url = gourl."/goCalltimes/goAPI.php"; #URL to GoAutoDial API. (required)
        
        $postfields["goUser"]             = goUser; #Username goes here. (required)
        $postfields["goPass"]             = goPass; #Password goes here. (required)
        $postfields["goAction"]           = "goAddCalltime"; #action performed by the [[API:Functions]]. (required)
        $postfields["responsetype"]       = responsetype; #json. (required)
        $postfields["hostname"]           = $_SERVER['REMOTE_ADDR']; #Default value
        $postfields["call_time_id"]       = $data_array['call_time_id']; #Desired uniqueid. (required)
        $postfields["call_time_name"]     = $data_array['call_time_name'];
        $postfields["call_time_comments"] = $data_array['call_time_comments'];
        $postfields["user_group"]         = $data_array['call_time_user_group'];
        $postfields["ct_default_start"]   = $data_array['start_default'];
        $postfields["ct_default_stop"]    = $data_array['stop_default'];
        $postfields["ct_sunday_start"]    = $data_array['start_sunday'];
        $postfields["ct_sunday_stop"]     = $data_array['stop_sunday'];
        $postfields["ct_monday_start"]    = $data_array['start_monday'];
        $postfields["ct_monday_stop"]     = $data_array['stop_monday'];
        $postfields["ct_tuesday_start"]   = $data_array['start_tuesday'];
        $postfields["ct_tuesday_stop"]    = $data_array['stop_tuesday'];
        $postfields["ct_wednesday_start"] = $data_array['start_wednesday'];
        $postfields["ct_wednesday_stop"]  = $data_array['stop_wednesday'];
        $postfields["ct_thursday_start"]  = $data_array['start_thursday'];
        $postfields["ct_thursday_stop"]   = $data_array['stop_thursday'];
        $postfields["ct_friday_start"]    = $data_array['start_friday'];
        $postfields["ct_friday_stop"]     = $data_array['stop_friday'];
        $postfields["ct_saturday_start"]  = $data_array['start_saturday'];
        $postfields["ct_saturday_stop"]   = $data_array['stop_saturday'];
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        //curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 100);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postfields);
        $data = curl_exec($ch);
        curl_close($ch);
        
        echo $data;
?>