<?php

    /** View Lead Info API - View a specific lead   */
    /**
    * Generates action circle buttons for different pages/module
    * @param goUser 
    * @param goPass 
    * @param goAction 
    * @param responsetype
    * @param campaign_id
    */
    require_once('goCRMAPISettings.php');

    $url = gourl."/goGetLeads/goAPI.php"; #URL to GoAutoDial API. (required)
    $postfields["goUser"] = goUser; #Username goes here. (required)
    $postfields["goPass"] = goPass; #Password goes here. (required)
    $postfields["goAction"] = "goGetLeadsInfo"; #action performed by the [[API:Functions]]. (required)
    $postfields["responsetype"] = responsetype; #json. (required)
    $postfields["lead_id"] = $_POST['lead_id'];; #Desired exten ID. (required)

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    //curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_TIMEOUT, 100);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $postfields);
    $data = curl_exec($ch);
    curl_close($ch);

    //var_dump($data);

    echo $data;
?>
