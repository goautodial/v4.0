<?php
    ####################################################
    #### Name: goGetCampaignsMonitoring.php         ####
    #### Type: API for dashboard php encode         ####
    #### Version: 0.9                               ####
    #### Copyright: GOAutoDial Inc. (c) 2011-2016   ####
    #### Written by: Demian Lizandro Biscocho       ####
    #### License: AGPLv2                            ####
    ####################################################

    // initialize session and DDBB handler
    include_once('../UIHandler.php');
    require_once('../LanguageHandler.php');
    require_once('../DbHandler.php');
    $ui = \creamy\UIHandler::getInstance();
    $lh = \creamy\LanguageHandler::getInstance();
    //$colors = $ui->generateStatisticsColors();

    require_once('../Session.php');    
    require_once('../goCRMAPISettings.php');
    $url = gourl."/goDashboard/goAPI.php"; #URL to GoAutoDial API. (required)
    $postfields["goUser"] = goUser; #Username goes here. (required)
    $postfields["goPass"] = goPass;
    $postfields["goAction"] = "goGetCampaignsResources"; #action performed by the [[API:Functions]]
    $postfields["responsetype"] = responsetype; 

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_TIMEOUT, 100);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $postfields);
    $data = curl_exec($ch);
    curl_close($ch);

    $output = json_decode($data);
    
    //$creamyAvatar = $ui->getSessionAvatar();     

    if (count($output->data) < 1){
        echo  "No data available";

    } else {

    //echo "<pre>";
    //print_r($output);

    $max = 0;
    
    $campaignsmonitoring = '['; 
    
        foreach ($output->data as $key => $value) {
        
        if(++$max > 50) break;

        $campname = $value->campaign_name;
        $leadscount = $value->mycnt;
        $campid =  $value->campaign_id;
        $campdesc = $value->campaign_description;
        $callrecordings = $value->campaign_recording;
        $campaigncid = $value->campaign_cid;
        $localcalltime = $value->local_call_time;
        $usergroup = $value->user_group;
        $camptype = $value->campaign_type;
        
        if ($leadscount == 0){ 
            $textclass = "text-blue";
            $sessionAvatar = "<div class='media'><avatar username='$localcalltime' :size='32'></avatar></div>";
            
        } 
        
        if ($leadscount > 0){ 
            $textclass = "text-blue";
            $sessionAvatar = "<div class='media'><avatar username='$localcalltime' :size='32'></avatar></div>";
            
        } 
        $campaignsmonitoring .='[';
        $campaignsmonitoring .= '"'.$sessionAvatar.'",';
        $campaignsmonitoring .= '"<b class=\"'.$textclass.'\">'.$campid.'</b>",';        
        $campaignsmonitoring .= '"'.$campname.'",'; 
        $campaignsmonitoring .= '"'.$leadscount.'",';      
        $campaignsmonitoring .= '"'.$localcalltime.'",';      
        $campaignsmonitoring .= '"'.$usergroup.'"';
        $campaignsmonitoring .='],';

        }

        $campaignsmonitoring = rtrim($campaignsmonitoring, ",");    
        $campaignsmonitoring .= ']';

        echo json_encode($campaignsmonitoring);
   } 
?>
