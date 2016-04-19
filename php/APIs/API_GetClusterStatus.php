<?php
require_once('../goCRMAPISettings.php');
    $url = gourl."/goDashboard/goAPI.php"; #URL to GoAutoDial API. (required)
    $postfields["goUser"] = goUser; #Username goes here. (required)
    $postfields["goPass"] = goPass;
    $postfields["goAction"] = "goGetClusterStatus"; #action performed by the [[API:Functions]]

     $ch = curl_init();
     curl_setopt($ch, CURLOPT_URL, $url);
     curl_setopt($ch, CURLOPT_POST, 1);
     curl_setopt($ch, CURLOPT_TIMEOUT, 100);
     curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
     curl_setopt($ch, CURLOPT_POSTFIELDS, $postfields);
     $data = curl_exec($ch);
     curl_close($ch);
     
     $data = explode(";",$data);
    foreach ($data AS $temp) {
      $temp = explode("=",$temp);
      $cluster[$temp[0]] = $temp[1];
    }
    
    if($cluster['result']=="success"){
      if(isset($_POST['api1'])){
        echo $cluster['server_id'];
      }
      
      if(isset($_POST['api2'])){
        echo $cluster['server_ip'];
      }
      
      if(isset($_POST['api3'])){
        echo $cluster['active'];
      }
      
      if(isset($_POST['api4'])){
        echo $cluster['sysload']."%";
      }
      
      if(isset($_POST['api5'])){
        echo $cluster['cpu'];
      }
      
      if(isset($_POST['api6'])){
        echo $cluster['channels_total'];
      }
      
      if(isset($_POST['api7'])){
        echo $cluster['disk_usage']."%";
      }
      
      if(isset($_POST['api8'])){
        echo $cluster['s_time'];
      }
      
      if(isset($_POST['api9'])){
        echo $cluster['php_time'];
      }
      
      if(isset($_POST['api10'])){
        echo $cluster['db_time'];
      }
      
    }else{
      echo " --- ";
    }

?>