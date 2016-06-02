<?php

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
     
     /*
     $data = explode(";",$data);
    foreach ($data AS $temp) {
      $temp = explode("=",$temp);
      $cluster[$temp[0]] = $temp[1];
    }
    */

    if($output->result=="success"){
      for($i=0;$i < count($output->server_id);$i++){

        if(isset($_POST['api1'])){
          echo $output->server_id[$i];
        }
        
        if(isset($_POST['api2'])){
          echo $output->server_ip[$i];
        }
        
        if(isset($_POST['api3'])){
          echo $output->active[$i];
        }
        
        if(isset($_POST['api4'])){
          echo $output->sysload[$i]."%";
        }
        
        if(isset($_POST['api5'])){
          echo $output->cpu[$i];
        }
        
        if(isset($_POST['api6'])){
          echo $output->channel[$i];
        }
        
        if(isset($_POST['api7'])){
          echo $output->disk_usage[$i]."%";
        }
        
        if(isset($_POST['api8'])){
          echo $output->cpu[$i];
        }
      
       }

        if(isset($_POST['api9'])){
          echo $output->phptime;
        }
        
        if(isset($_POST['api10'])){
          echo $output->dbtime;
        }

     

    }else{
      echo " --- ";
    }

?>