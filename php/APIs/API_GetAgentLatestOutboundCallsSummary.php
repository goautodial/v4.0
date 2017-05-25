<?php
    ####################################################
    #### Name: GetAgentInformation.php              ####
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

    $url = gourl."/goUsers/goAPI.php"; #URL to GoAutoDial API. (required)
    $postfields["goUser"] = goUser; #Username goes here. (required)
    $postfields["goPass"] = goPass; #Password goes here. (required)
    $postfields["goAction"] = "goGetUserInfo"; #action performed by the [[API:Functions]]. (required)
    $postfields["responsetype"] = responsetype; #json. (required)
    $postfields["user_id"] = $_REQUEST['user_id']; #User ID (required)
	$postfields["session_user"] = $_SESSION['user']; #current user

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_TIMEOUT, 100);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $postfields);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    $data = curl_exec($ch);
    curl_close($ch);

    $output = json_decode($data);
    //echo "<pre>";
    //print_r($output);
    //die();

    $creamyAvatar = $ui->getSessionAvatar();
    
    //function latest_agentoutcalls_summary (){
    
        if (count($output->agentoutcalls) < 1){
            
            $lead_id = date("Y");
            $phone_number = time();
            $fullname = "Outbound John Doe";
            $sessionAvatar = "<avatar username='$fullname' :size='36'></avatar>";
            
                echo    '<span class="media-box">
                            <span class="pull-left">
                                '.$sessionAvatar.'
                            </span>
                            <span class="media-box-body clearfix">
                                <small class="text-muted pull-right ml">'.$lead_id.'</small>             
                                <span class="media-box-heading"><strong><a class="text m0">'.$fullname.'</strong></a>
                                <p class="m0">
                                    <small class="text-black">'.$phone_number.'</small>                                
                                </p><br/>
                            </span>              
                        </span>';
            
        } else {
        
            $max = 0;
            
            foreach($output->agentoutcalls as $key => $value){
            
                if(++$max > 6) break;

                $first_name = $value->first_name;
                $last_name = $value->last_name;
                $phone_number = $value->phone_number;
                $lead_id = $value->lead_id;
                $list_id = $value->list_id;
                $campaign_id = $value->campaign_id;
                $call_date = $value->call_date;
                $length_in_sec = $value->length_in_sec;
                $status = $value->status;
                                
                if ($first_name == NULL && $last_name == NULL){
                    $first_name = "John";
                    $last_name = "Doe";
                }
        
                $fullname = "$first_name $last_name";
                $sessionAvatar = "<avatar username='$fullname' :size='36'></avatar>";
                        
                echo    '<span class="media-box">
                            <span class="pull-left">
                                '.$sessionAvatar.'
                            </span>
                            <span class="media-box-body clearfix">
                                <small class="text-muted pull-right ml"><a id="onclick-leadinfo" data-toggle="modal" data-target="#view_lead_information" data-id="'.$lead_id.'">'.$lead_id.'</a></small>             
                                <span class="media-box-heading"><strong><a id="onclick-leadinfo" data-toggle="modal" data-target="#view_lead_information" data-id="'.$lead_id.'" class="text m0">'.$fullname.'</strong></a>
                                </span>
                                <p class="m0">
                                    <small><a id="onclick-leadinfo" data-toggle="modal" data-target="#view_lead_information" data-id="'.$lead_id.'" class="text-black">'.$phone_number.'</a>
                                    </small>
                                </p><br/>
                            </span>              
                        </span>';                            

            }
        }
    //}        
    
    //function latest_agentincalls_summary (){    

/*        if (count($output->agentincalls) < 1){
                
                $lead_id = date("Y");
                $phone_number = time();
                $fullname = "Inbound John Doe";
                $sessionAvatar = "<avatar username='$fullname' :size='36'></avatar>";
                
                    echo    '<span class="media-box">
                                <span class="pull-left">
                                    '.$sessionAvatar.'
                                </span>
                                <span class="media-box-body clearfix">
                                    <small class="text-muted pull-right ml">'.$lead_id.'</small>             
                                    <span class="media-box-heading"><strong><a class="text m0">'.$fullname.'</strong></a>
                                    <p class="m0">
                                        <small class="text-black">'.$phone_number.'</small>                                
                                    </p><br/>
                                </span>              
                            </span>';
                
            } else {
            
                $max = 0;
                
                foreach($output->agentincalls as $key => $value){
                
                    if(++$max > 6) break;

                    $first_name = $value->first_name;
                    $last_name = $value->last_name;
                    $phone_number = $value->phone_number;
                    $lead_id = $value->lead_id;
                    $list_id = $value->list_id;
                    $campaign_id = $value->campaign_id;
                    $call_date = $value->call_date;
                    $length_in_sec = $value->length_in_sec;
                    $status = $value->status;
                    $fullname = "$first_name $last_name";
                    
                    if ($first_name == NULL && $last_name == NULL){
                        $first_name = "Unknown";
                        $last_name = "Caller";
                    }
            
                    $fullname = "$first_name $last_name";
                    $sessionAvatar = "<avatar username='$fullname' :size='36'></avatar>";
                            
                    echo    '<span class="media-box">
                                <span class="pull-left">
                                    '.$sessionAvatar.'
                                </span>
                                <span class="media-box-body clearfix">
                                    <small class="text-muted pull-right ml"><a id="onclick-leadinfo" data-toggle="modal" data-target="#view_lead_information" data-id="'.$lead_id.'">'.$lead_id.'</a></small>             
                                    <span class="media-box-heading"><strong><a id="onclick-leadinfo" data-toggle="modal" data-target="#view_lead_information" data-id="'.$lead_id.'" class="text m0">'.$fullname.'</strong></a>
                                    </span>
                                    <p class="m0">
                                        <small><a id="onclick-leadinfo" data-toggle="modal" data-target="#view_lead_information" data-id="'.$lead_id.'" class="text-black">'.$phone_number.'</a>
                                        </small>
                                    </p><br/>
                                </span>              
                            </span>';                            

                }
        }    
    //}
    
    //latest_agentoutcalls_summary();
    //latest_agentincalls_summary();
*/

?>
