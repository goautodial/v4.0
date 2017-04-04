<?php
    ####################################################
    #### Name: GetTotalAgentsPaused.php             ####
    #### Type: API for dashboard php encode         ####
    #### Version: 0.9                               ####
    #### Copyright: GOAutoDial Inc. (c) 2011-2016   ####
    #### Written by: Demian Lizandro Biscocho       ####
    #### License: AGPLv2                            ####
    ####################################################
    
    require_once('../goCRMAPISettings.php');
	require_once('../Session.php');
    /*
    *Displaying Agent(s) on Paused
    *[[API: Function]] - goGetTotalAgentsPaused
    *This application is used to get total of agents paused
    */

    $url = gourl."/goDashboard/goAPI.php"; #URL to GoAutoDial API. (required)
    $postfields["goUser"] = goUser; #Username goes here. (required)
    $postfields["goPass"] = goPass;
    $postfields["goAction"] = "goGetTotalAgentsPaused"; #action performed by the [[API:Functions]]
    $postfields["responsetype"] = responsetype;
    $postfields["session_user"] = $_SESSION['user']; #current user
	
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
        
    $total_agents_paused = $output->data->getTotalAgentsPaused;
        
    if($total_agents_paused == NULL || $total_agents_paused == 0){
        $total_agents_paused = 0;
    }
        
    echo number_format($total_agents_paused); 

?>
