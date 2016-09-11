<?php
    require_once('../goCRMAPISettings.php');
    /*
    * Displaying Call(s) Ringing
    * [[API: Function]] - goGetRingingCall
    * This application is used to get calls ringing
    */

    $url = gourl."/goDashboard/goAPI.php"; #URL to GoAutoDial API. (required)
    $postfields["goUser"] = goUser; #Username goes here. (required)
    $postfields["goPass"] = goPass;
    $postfields["goAction"] = "goGetRingingCalls"; #action performed by the [[API:Functions]]
    $postfields["responsetype"] = responsetype;
   
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_TIMEOUT, 100);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $postfields);
    $data = curl_exec($ch);
    curl_close($ch);
    
    //var_dump($data);
    $output = json_decode($data);
        
    $ringing_calls = $output->data->getRingingCalls;
        
    if($ringing_calls == NULL || $ringing_calls == 0){
        $ringing_calls = 0;
    }
        
    echo json_encode(round($ringing_calls)); 

?>
