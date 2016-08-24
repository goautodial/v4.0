<?php
    ####################################################
    #### Name: goGetCampaignsResources.php          ####
    #### Type: API for dashboard php encode         ####
    #### Version: 0.9                               ####
    #### Copyright: GOAutoDial Inc. (c) 2011-2016   ####
    #### Written by: Demian Lizandro Biscocho       ####
    #### License: AGPLv2                            ####
    ####################################################

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

    if ($output == NULL){
        echo   		                    '<div class="media-box">
		                              <div class="pull-left">
		                                 <span class="fa-stack">
		                                    <em class="fa fa-circle fa-stack-2x text-success"></em>
		                                    <em class="fa fa-clock-o fa-stack-1x fa-inverse text-white"></em>
		                                 </span>
		                              </div>
		                              <div class="media-box-body clearfix">
		                                 <small class="text-muted pull-right ml">150</small>
		                                 <div class="media-box-heading"><a href="#" class="text-success m0">Sample Campaign</a>
		                                 </div>
		                                 <p class="m0">
		                                    <small>On
		                                       <em>10/12/2015 09:00 am</em>
		                                    </small>
		                                 </p>
		                              </div>
		                           </div>';
    }

    //echo "<pre>";
    //print_r($output);
    foreach ($output->data as $key => $value) {
        
        $campname = $value->campaign_name;
        $leadscount = $value->mycnt;
        $campid =  $value->campaign_id;
        $campdesc = $value->campaign_description;
        $callrecordings = $value->campaign_recording;
        $campaigncid = $value->campaign_cid;
        $localcalltime = $value->local_call_time;
        $camptype = $value->campaign_type;      
        
    if ($leadscount == 0){   
    
            echo                            '<div class="media-box">
                                                <div class="pull-left">
		                                 <span class="fa-stack">
		                                    <em class="fa fa-circle fa-stack-2x text-danger"></em>
		                                    <em class="fa fa-exclamation fa-stack-1x fa-inverse text-white"></em>
		                                 </span>
		                              </div>
		                              <div class="media-box-body clearfix">
		                                 <small class="text-muted pull-right ml">'.$leadscount.'</small>
		                                 <div class="media-box-heading"><strong><a id="onclick-campaigninfo" data-toggle="modal" data-target="#view_campaign_information" data-id="'.$campid.'" class="text-danger m0">'.$campname.'</strong></a>
		                                 </div>
		                                 <p class="m0">
		                                    <small><strong><a id="onclick-campaigninfo" data-toggle="modal" data-target="#view_campaign_information" data-id="'.$campid.'" class="text">'.$campid.'</strong></a>
		                                    </small>
		                                 </p>
		                              </div>
                                            </div>';
                                            
    }
        if ($leadscount > 0){ 

             echo                           '<div class="media-box">
                                                <div class="pull-left">
		                                 <span class="fa-stack">
		                                    <em class="fa fa-circle fa-stack-2x text-info"></em>
		                                    <em class="fa fa-file-text-o fa-stack-1x fa-inverse text-white"></em>
		                                 </span>
		                              </div>
		                              <div class="media-box-body clearfix">
		                                 <small class="text-muted pull-right ml">'.$leadscount.'</small>
		                                 <div class="media-box-heading"><strong><a id="onclick-campaigninfo" data-toggle="modal" data-target="#view_campaign_information" data-id="'.$campid.'" class="text-warning m0">'.$campname.'</strong></a>
		                                 </div>
		                                 <p class="m0">
		                                    <small><strong><a id="onclick-campaigninfo" data-toggle="modal" data-target="#view_campaign_information" data-id="'.$campid.'" class="text">'.$campid.'</strong></a>
		                                    </small>
		                                 </p>
		                              </div>
                                            </div>';
        }

        
    }  
    
    
?>
