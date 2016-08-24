<?php
    ####################################################
    #### Name: goGetClusterStatus.php               ####
    #### Type: API for dashboard php encode         ####
    #### Version: 0.9               ####
    #### Copyright: GOAutoDial Inc. (c) 2011-2016   ####
    #### Written by: Demian Lizandro Biscocho       ####
    #### License: AGPLv2            ####
    ####################################################
    
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
    $time = "";
    
        $disk_ary = explode('|', $diskusage);
        $disk_ary_ct = count($disk_ary);

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

    $diskusage = $disk;

    $cluster .='[';       
    $cluster .= '"'.$serverid.'",';   
    $cluster .= '"'.$serverip.'",';   
    //$cluster .= '"'.$status.'",';    
    $cluster .= '"'.$load.'%",';    
    $cluster .= '"'.$channels.'",';
    //$cluster .= '"<b data-label=\"'.$diskusage.'\" class=\"radial-bar radial-bar-'.$diskusage.' radial-bar-xs\"></b>",';
    $cluster .= '"'.$diskusage.'%",';
    $cluster .= '"'.$time.'"';
    $cluster .='],';
  
    
}

    $cluster = rtrim($cluster, ",");    
    $cluster .= ']';
    
    echo json_encode($cluster);     
    


?>
