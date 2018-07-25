<?php

require_once('../goCRMAPISettings.php');
require_once('../Session.php');
$url = gourl."/goDashboard/goAPI.php"; #URL to GoAutoDial API. (required)
$postfields["goUser"] = goUser; #Username goes here. (required)
$postfields["goPass"] = goPass;
$postfields["goAction"] = "goGetActiveCampaignsToday"; #action performed by the [[API:Functions]]
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
     
     /*
     $data = explode(";",$data);
    foreach ($data AS $temp) {
      $temp = explode("=",$temp);
      $cluster[$temp[0]] = $temp[1];
    }
    */

    if($output->result=="success"){
        for($i=0;$i < count($output->campaign_id);$i++){
                if(isset($_POST['api1'])){
                        echo $output->campaign_id[$i];
                }        
                if(isset($_POST['api2'])){
                        echo $output->calls_today[$i];
                }
        }
    }else{
                echo " --- ";
    }        
?>
