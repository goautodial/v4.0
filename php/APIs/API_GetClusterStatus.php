<?php
    ####################################################
    #### Name: goGetClusterStatus.php               ####
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
    
    require_once('../goCRMAPISettings.php');
    
    $url = gourl."/goDashboard/goAPI.php"; #URL to GoAutoDial API. (required)
    $postfields["goUser"] = goUser; #Username goes here. (required)
    $postfields["goPass"] = goPass;
    $postfields["goAction"] = "goGetClusterStatus"; #action performed by the [[API:Functions]]
    $postfields["responsetype"] = responsetype; 
    
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
    
    $cluster = '[';
     
    foreach ($output->data as $key => $value) {
    
        $serverid = $value->server_id;
        $serverdesc = $value->server_description;
        $serverip = $value->server_ip;
        $status =  $value->active;
        $load = $value->sysload;    
        $channels = $value->channels_total;
        $cpuidlepercent = $value->cpu_idle_percent;
        $diskusage = $value->disk_usage;
        $stime = $value->s_time;
        $utime = $value->u_time;
    
        $disk_ary = explode('|', $diskusage);
        $disk_ary_ct = count($disk_ary);

        if ($status == "Y"){
            $status = "ACTIVE";
            $statustextclass = "text-success";
        }
        
        if ($status == "N"){
            $status = "INACTIVE";
            $statustextclass = "text-danger";
        }
        $k = 0;

        while($k < $disk_ary_ct){
                $disk_ary[$k] = preg_replace("/^\d* /","",$disk_ary[$k]);
                //print_r($disk_ary);
                if($k<1) {$disk = "$disk_ary[$k]";}
                else{
        if($disk_ary[$k] > $disk) {$disk = "$disk_ary[$k]";}
                }
                $k++;
        }

        if ($load < 60){
            $loadtextclass = "text-info";
        }
        
        if ($load >= 60){
            $loadtextclass = "text-warning";
        }
        
        if ($load >= 80){
            $loadtextclass = "text-danger";
        }
        
        $diskusage = $disk;
        
        if ($diskusage < 60){
            $disktextclass = "text-info";
        }
        
        if ($diskusage >= 60){
            $disktextclass = "text-warning";
        }
        
        if ($diskusage >= 80){
            $disktextclass = "text-danger";
        }
        
        $diskvalue01 = (100 - $diskusage);
        //$diskvalue02 = ($diskvalue01 + $diskusage);
        $radial = "<canvas classyloader='' data-percentage='.$diskusage.' data-speed='20' data-font-size='20px' data-diameter='30' data-line-color='#f35839' data-remaining-line-color='#edf2f6' data-line-width='40' width=''40'' height='40' class='js-is-in-view'></canvas>";
        $sessionAvatar = "<div class='media'><avatar username='$serverid' :size='36'></avatar></div>";
        
        $cluster .='[';  
        $cluster .= '"'.$serverid.'",';   
        $cluster .= '"'.$serverip.'",';  
        $cluster .= '"<b class=\"'.$statustextclass.'\">'.$status.'</b>",';
        $cluster .= '"<b class=\"'.$loadtextclass.'\">'.$load.'%</b>",';    
        $cluster .= '"'.$channels.'",';
        $cluster .= '"<b class=\"'.$disktextclass.'\">'.$diskusage.'%</b>",';
        //$cluster .= '"<div data-label=\"'.$diskusage.'%\" class=\"radial-bar radial-bar-'.$diskusage.' radial-bar-xs\"></div>",';
        //$cluster .= '"'.$radial.'",';
        $cluster .= '"'.$stime.'"';
        $cluster .='],';
    
    }

    $cluster = rtrim($cluster, ",");    
    $cluster .= ']';
    
    echo json_encode($cluster);     
    


?>
