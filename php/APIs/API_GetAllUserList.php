<?php
    ####################################################
    #### Name: goGetAllUserLists.php                ####
    #### Type: API for dashboard php encode         ####
    #### Version: 0.9                               ####
    #### Copyright: GOAutoDial Inc. (c) 2011-2016   ####
    #### Written by: Demian Lizandro Biscocho       ####
    #### License: AGPLv2                            ####
    ####################################################

    //initialize session and DDBB handler
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
    $postfields["goPass"] = goPass;
    $postfields["goAction"] = "goGetAllUserLists"; #action performed by the [[API:Functions]]
    $postfields["responsetype"] = responsetype;
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
    //die("dd");
    
    $barracks = '[';

    foreach ($output->data as $key => $value) {

        $userid = $value->user_id;
        $user = $value->user;       
        $fullname = $value->full_name;
        $usergroup = $value->user_group;
        $status = $value->active;        
        $userlevel = $value->user_level;
        $action_user = "";
        //$action_user = $ui->getUserActionMenuForT_User($userid, $userlevel, $fullname); 
        
        $textclass = "text-info";        
        $sessionAvatar = "<div class='media'><avatar username='$fullname' :size='36'></avatar></div>";   
        //$sessionAvatar = "";
        
        $barracks .='[';       
        $barracks .= '"'.$sessionAvatar.'",';
        $barracks .= '"'.$user.'",'; 
        $barracks .= '"'.$fullname.'",';    
        $barracks .= '"'.$usergroup.'",';                   
        $barracks .= '"'.$status.'",';
        $barracks .= '"'.$action_user.'"';
        $barracks .='],';
 
    }

    $barracks = rtrim($barracks, ",");    
    $barracks .= ']';
    
    echo json_encode($barracks);

?>
