<?php

    require_once('goCRMAPISettings.php');
    
    $campaign_id = NULL;
    if(isset($_POST['campaign_id'])){
        $campaign_id = $_POST['campaign_id'];
    }
    $recycleid = NULL;
    if(isset($_POST['recycleid'])){
        $recycleid = $_POST['recycleid'];
    }

    $url = gourl."/goLeadRecycling/goAPI.php"; #URL to GoAutoDial API. (required)
    
    $postfields["goUser"] = goUser; #Username goes here. (required)
    $postfields["goPass"] = goPass; #Password goes here. (required)
    $postfields["goAction"] = "goDeleteLeadRecycling"; #action performed by the [[API:Functions]]. (required)
    $postfields["responsetype"] = responsetype; #json. (required)

    $postfields["campaign_id"] = $campaign_id;
    $postfields["recycle_id"] = $recycleid;
    $postfields["session_user"] = $_POST['session_user'];
        
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
 
    echo $output->result;
?>