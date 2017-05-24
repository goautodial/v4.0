<?php

        require_once('goCRMAPISettings.php');
         
        $url = gourl."/goDispositions/goAPI.php"; #URL to GoAutoDial API. (required)

        $postfields["goUser"] = goUser;
        $postfields["goPass"] = goPass;
        $postfields["goAction"] = "getDispositionInfo";
        $postfields["responsetype"] = responsetype; 
        $postfields["status"] = $_POST['status']; 
        $postfields["campaign_id"] = $_POST['campaign_id'];
        $postfields["log_user"] = $_POST['log_user'];
        $postfields["log_group"] = $_POST['log_group'];
        $postfields["log_ip"] = $_SERVER['REMOTE_ADDR'];
        
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
 
        echo $data;
?>