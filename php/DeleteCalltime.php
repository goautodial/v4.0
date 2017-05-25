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
        
        $postfields["goUser"] = goUser; #Username goes here. (required)
        $postfields["goPass"] = goPass; #Password goes here. (required)
        $postfields["goAction"] = "goDeleteCalltime"; #action performed by the [[API:Functions]]. (required)
        $postfields["responsetype"] = responsetype; #json. (required)
        $postfields["call_time_id"] = $_POST['call_time_id']; #Desired uniqueid. (required)
	$postfields["log_group"] = $_POST['log_group'];
	$postfields["log_user"] = $_POST['log_user'];
	$postfields["log_ip"] = $_POST['log_ip'];
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        //curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 100);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postfields);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $data = curl_exec($ch);
        curl_close($ch);
        $output = json_decode($data);
     
        //var_dump($output);
        if ($output->result=="success") {
            # Result was OK!
                echo 1;
        }else{
                echo $output->result;
        }
?>