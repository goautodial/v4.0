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

        $url = gourl."/goCalltimes/goAPI.php"; #URL to GoAutoDial API. (required)
        
        $start_default =	(strlen($_POST['start_default']) > 0) ? date('Hi', strtotime($_POST['start_default'])) : "0000";
        $stop_default =		(strlen($_POST['stop_default']) > 0) ? date('Hi', strtotime($_POST['stop_default'])) : "0000";

        $start_sunday =		(strlen($_POST['start_sunday']) > 0) ? date('Hi', strtotime($_POST['start_sunday'])) : $start_default;
        $stop_sunday =		(strlen($_POST['stop_sunday']) > 0) ? date('Hi', strtotime($_POST['stop_sunday'])) : $stop_default;

        $start_monday =		(strlen($_POST['start_monday']) > 0) ? date('Hi', strtotime($_POST['start_monday'])) : $start_default;
        $stop_monday =		(strlen($_POST['stop_monday']) > 0) ? date('Hi', strtotime($_POST['stop_monday'])) : $stop_default;

        $start_tuesday =	(strlen($_POST['start_tuesday']) > 0) ? date('Hi', strtotime($_POST['start_tuesday'])) : $start_default;
        $stop_tuesday =		(strlen($_POST['stop_tuesday']) > 0) ? date('Hi', strtotime($_POST['stop_tuesday'])) : $stop_default;

        $start_wednesday =	(strlen($_POST['start_wednesday']) > 0) ? date('Hi', strtotime($_POST['start_wednesday'])) : $start_default;
        $stop_wednesday =	(strlen($_POST['stop_wednesday']) > 0) ? date('Hi', strtotime($_POST['stop_wednesday'])) : $stop_default;

        $start_thursday =	(strlen($_POST['start_thursday']) > 0) ? date('Hi', strtotime($_POST['start_thursday'])) : $start_default;
        $stop_thursday =	(strlen($_POST['stop_thursday']) > 0) ? date('Hi', strtotime($_POST['stop_thursday'])) : $stop_default;

        $start_friday =		(strlen($_POST['start_friday']) > 0) ? date('Hi', strtotime($_POST['start_friday'])) : $start_default;
        $stop_friday =		(strlen($_POST['stop_friday']) > 0) ? date('Hi', strtotime($_POST['stop_friday'])) : $stop_default;

        $start_saturday =	(strlen($_POST['start_saturday']) > 0) ? date('Hi', strtotime($_POST['start_saturday'])) : $start_default;
        $stop_saturday =	(strlen($_POST['stop_saturday']) > 0) ? date('Hi', strtotime($_POST['stop_saturday'])) : $stop_default;

        $postfields["goUser"]             = goUser; #Username goes here. (required)
        $postfields["goPass"]             = goPass; #Password goes here. (required)
        $postfields["goAction"]           = "goAddCalltime"; #action performed by the [[API:Functions]]. (required)
        $postfields["responsetype"]       = responsetype; #json. (required)
        $postfields["hostname"]           = $_SERVER['REMOTE_ADDR']; #Default value
        $postfields["call_time_id"]       = $_POST['call_time_id']; #Desired uniqueid. (required)
        $postfields["call_time_name"]     = $_POST['call_time_name'];
        $postfields["call_time_comments"] = $_POST['call_time_comments'];
        $postfields["user_group"]         = $_POST['call_time_user_group'];

        $postfields["ct_default_start"]   = $start_default;
        $postfields["ct_default_stop"]    = $stop_default;

        $postfields["ct_sunday_start"]    = $start_sunday;
        $postfields["ct_sunday_stop"]     = $stop_sunday;

        $postfields["ct_monday_start"]    = $start_monday;
        $postfields["ct_monday_stop"]     = $stop_monday;

        $postfields["ct_tuesday_start"]   = $start_tuesday;
        $postfields["ct_tuesday_stop"]    = $stop_tuesday;

        $postfields["ct_wednesday_start"] = $start_wednesday;
        $postfields["ct_wednesday_stop"]  = $stop_wednesday;

        $postfields["ct_thursday_start"]  = $start_thursday;
        $postfields["ct_thursday_stop"]   = $stop_thursday;

        $postfields["ct_friday_start"]    = $start_friday;
        $postfields["ct_friday_stop"]     = $stop_friday;

        $postfields["ct_saturday_start"]  = $start_saturday;
        $postfields["ct_saturday_stop"]   = $stop_saturday;
	
	$postfields["default_audio"] = $_POST["audio_default"];
	$postfields["sunday_audio"] = $_POST["audio_sunday"];
	$postfields["monday_audio"] = $_POST["audio_monday"];
	$postfields["tuesday_audio"] = $_POST["audio_tuesday"];
	$postfields["wednesday_audio"] = $_POST["audio_wednesday"];
	$postfields["thursday_audio"] = $_POST["audio_thursday"];
	$postfields["friday_audio"] = $_POST["audio_friday"];
	$postfields["saturday_audio"] = $_POST["audio_saturday"];
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        //curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
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

        echo  $status;
?>