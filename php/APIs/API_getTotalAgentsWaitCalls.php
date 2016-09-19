<?php
    ####################################################
    #### Name: GetTotalAgentsWaitCalls.php          ####
    #### Type: API for dashboard php encode         ####
    #### Version: 0.9                               ####
    #### Copyright: GOAutoDial Inc. (c) 2011-2016   ####
    #### Written by: Demian Lizandro Biscocho       ####
    #### License: AGPLv2                            ####
    ####################################################
    
    require_once('../goCRMAPISettings.php');
    /*
    * Displaying Agent(s) Waiting
    * [[API: Function]] - getTotalAgentsWaitCalls
    * This application is used to get total of agents waiting
    */

    $url = gourl."/goDashboard/goAPI.php"; #URL to GoAutoDial API. (required)
    $postfields["goUser"] = goUser; #Username goes here. (required)
    $postfields["goPass"] = goPass;
    $postfields["goAction"] = "goGetTotalAgentsWaitCalls"; #action performed by the [[API:Functions]]
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
        
    $total_agents_wait_calls = $output->data->getTotalAgentsWaitCalls;
        
    if($total_agents_wait_calls == NULL || $total_agents_wait_calls == 0){
        $total_agents_wait_calls = 0;
    }
        
    echo number_format($total_agents_wait_calls); 
?>
