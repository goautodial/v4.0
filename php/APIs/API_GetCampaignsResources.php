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
	require_once('../Session.php');
	
    $url = gourl."/goDashboard/goAPI.php"; #URL to GoAutoDial API. (required)
    $postfields["goUser"] = goUser; #Username goes here. (required)
    $postfields["goPass"] = goPass;
    $postfields["goAction"] = "goGetCampaignsResources"; #action performed by the [[API:Functions]]
    $postfields["responsetype"] = responsetype;
	$postfields["user"] = $_SESSION['user']; #action performed by the [[API:Functions]]

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_TIMEOUT, 100);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $postfields);
    $data = curl_exec($ch);
    curl_close($ch);

    $output = json_decode($data);

    if (count($output->data) < 1){
        echo '<div class="media-box">
				<div class="pull-left">
				   <span class="fa-stack">
					  <em class="fa fa-circle fa-stack-2x text-success"></em>
					  <em class="fa fa-clock-o fa-stack-1x fa-inverse text-white"></em>
				   </span>
				</div>
				<div class="media-box-body clearfix">
				   <div class="media-box-heading"><a href="#" class="text-success m0">No Available Campaign</a>
				   </div>
				</div>
			 </div>';

    } else {
  
    $max = 0;
                
        foreach ($output->data as $key => $value) {
        
            if(++$max > 6) break; 
            
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
            
                $sessionAvatar = "<avatar username='$localcalltime' :size='32'></avatar>";
                
                    echo '<div class="media-box">
							<div class="pull-left">
								'.$sessionAvatar.'
							</div>                                                
								<div class="media-box-body clearfix">
									<small class="text-muted pull-right ml">'.$leadscount.'</small>
									<div class="media-box-heading"><strong><a id="onclick-campaigninfo" data-toggle="modal" data-target="#view_campaign_information" data-id="'.$campid.'" class="text m0">'.$campname.'</strong></a>
									</div>
										<p class="m0">
											<small><strong><a id="onclick-campaigninfo" data-toggle="modal" data-target="#view_campaign_information" data-id="'.$campid.'" class="text-black">'.$campid.'</strong></a>
											</small>
										</p>
								</div>
							</div>
						</div>';
            }
            
            if ($leadscount > 0){ 
                
                    $sessionAvatar = "<avatar username='$localcalltime' :size='32'></avatar>";
                    
                    echo '<div class="media-box">
							<div class="pull-left">
								'.$sessionAvatar.'
							</div>                                                
								<div class="media-box-body clearfix">
									<small class="text-muted pull-right ml">'.$leadscount.'</small>
									<div class="media-box-heading"><strong><a id="onclick-campaigninfo" data-toggle="modal" data-target="#view_campaign_information" data-id="'.$campid.'" class="text m0">'.$campname.'</strong></a>
									</div>
										<p class="m0">
											<small><strong><a id="onclick-campaigninfo" data-toggle="modal" data-target="#view_campaign_information" data-id="'.$campid.'" class="text-black">'.$campid.'</strong></a>
											</small>
										</p>
								</div>
							</div>
						</div>';
            }
        }
    }
    
?>
