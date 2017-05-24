<?php
        require_once('goCRMAPISettings.php');
        
        $url = gourl."/goServers/goAPI.php"; #URL to GoAutoDial API. (required)

        $postfields["goUser"] = goUser; #Username goes here. (required)
        $postfields["goPass"] = goPass; #Password goes here. (required)
        $postfields["goAction"] = "goDeleteServer"; #action performed by the [[API:Functions]]. (required)
        $postfields["responsetype"] = responsetype; #json. (required)
        $postfields["server_id"] = $_POST['server_id']; #Desired script id. (required)
        $postfields['hostname'] = $_SERVER['REMOTE_ADDR'];
	
        $postfields['log_user'] = $_POST['log_user'];
        $postfields['log_group'] = $_POST['log_group'];
        
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
        
        if ($output->result=="success") {
            # Result was OK!
                echo $output->result;
        }else{
                echo $output->result;
        }
?>