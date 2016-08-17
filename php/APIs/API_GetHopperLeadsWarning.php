<?php

####################################################
#### Name: goGetHopperLeadsWarning.php          ####
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
$postfields["goAction"] = "goGetHopperLeadsWarning"; #action performed by the [[API:Functions]]
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
    echo '<small class="text-muted pull-right ml" style="padding-right:20px;"></small>
                <div class="media-box-heading"><div class="media-box-heading"><strong>
                <a href="#" class="text-danger m0">There is no available data.</a></strong>
                </div>
                <p class="m0">
                <small ><strong><a href="#" class="text"></a></strong>
                </small>
                </p>';
}

//echo "<pre>";
//print_r($output);
foreach ($output->data as $key => $value) {
    
    $campname = $value->campaign_name;
    //var_dump ($campname);
    $leadscount = $value->mycnt;
    $campid =  $value->campaign_id;
  
   if ($leadscount == 0){   
        echo '<div class="media-box-heading"><div class="media-box-heading"><strong>
                <a id="onclick-campaigninfo" data-toggle="modal" data-target="#view-campaign-modal" data-id="'.$campid.'" class="text-danger m0">'.$campname.'</a></strong>
                </div>
                <p class="m0">
                <small ><strong><a id="onclick-campaigninfo" data-toggle="modal" data-target="#view-campaign-modal" data-id="'.$campid.'" class="text">'.$campid.'</a></strong>
                </small>
                <small class="text-muted pull-right ml" style="padding-right:20px;">'.$leadscount.'</small>
                </p>';
    }
    if ($leadscount > 0){ 
        echo '<div class="media-box-heading"><div class="media-box-heading"><strong>
                <a id="onclick-campaigninfo" data-toggle="modal" data-target="#view-campaign-modal" data-id="'.$campid.'" class="text-warning m0">'.$campname.'</a></strong>
                </div>
                <p class="m0">
                <small><strong><a id="onclick-campaigninfo" data-toggle="modal" data-target="#view-campaign-modal" data-id="'.$campid.'" class="text">'.$campid.'</a></strong>
                </small>
                <small class="text-muted pull-right ml" style="padding-right:20px;">'.$leadscount.'</small>
                </p>';
    }

    
}           
?>
