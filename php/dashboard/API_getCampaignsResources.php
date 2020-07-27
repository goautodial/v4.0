<?php
/**
 * @file        API_getCampaignsResources.php
 * @brief       Displays campaigns with < 100 leads in hopper
 * @copyright   Copyright (c) 2020 GOautodial Inc.
 * @author		Demian Lizandro A. Biscocho 
 *
 * @par <b>License</b>:
 *  This program is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU Affero General Public License as published by
 *  the Free Software Foundation, either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU Affero General Public License for more details.
 *
 *  You should have received a copy of the GNU Affero General Public License
 *  along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/

	require_once('APIHandler.php');
	
	$api 										= \creamy\APIHandler::getInstance();
	$output 									= $api->API_getCampaignsResources();
	
    if ( empty($output->data) || is_null($output->data) ) {
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
		$max 									= 0;
                
        foreach ($output->data as $key => $value) {        
            if(++$max > 6) break; 
            
			$campname 							= $api->escapeJsonString($value->campaign_name);
			$leadscount 						= $api->escapeJsonString($value->mycnt);
			$campid 							= $api->escapeJsonString($value->campaign_id);
			//$campdesc	 						= $api->escapeJsonString($value->campaign_description);
			//$callrecordings 					= $api->escapeJsonString($value->campaign_recording);
			//$campaigncid 						= $api->escapeJsonString($value->campaign_cid);
			$localcalltime 						= $api->escapeJsonString($value->local_call_time);
			//$usergroup 							= $api->escapeJsonString($value->user_group);
			//$camptype 							= $api->escapeJsonString($value->campaign_type);
    
            if ($leadscount == 0){               
                $sessionAvatar 					= "<avatar username='$localcalltime' :size='32'></avatar>";
                
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
				$sessionAvatar 					= "<avatar username='$localcalltime' :size='32'></avatar>";
				
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
