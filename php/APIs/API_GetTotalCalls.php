<?php
    require_once('../goCRMAPISettings.php');
    /*
    * Displaying Total Calls
    * [[API: Function]] - getTotalcalls
    * This application is used to get total calls.
    */

    $url = gourl."/goDashboard/goAPI.php"; #URL to GoAutoDial API. (required)
    $postfields["goUser"] = goUser; #Username goes here. (required)
    $postfields["goPass"] = goPass;
    $postfields["goAction"] = "goGetTotalCalls"; #action performed by the [[API:Functions]]

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_TIMEOUT, 100);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $postfields);
    $data = curl_exec($ch);
    curl_close($ch);

    $output = json_decode($data);
    
    $total_calls = $output->data->getTotalCalls;
    
    if ($total_calls == NULL){
            $total_calls = "0";
    }
        
    echo round($total_calls); 
?>
