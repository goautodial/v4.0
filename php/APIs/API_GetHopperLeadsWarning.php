<?php

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
     
//milo
for($i=0;$i < count($output->campaign_id);$i++){
    $campname = $output->campaign_name[$i];
    $leadscount = $output->mycnt[$i];
    $campid =  $output->campaign_id[$i];
    
   
   echo '<small class="text-muted pull-right ml">'.$leadscount.'</small>
    <div class="media-box-heading"><div class="media-box-heading">
    <a href="#" class="text-info m0">'.$campname.'</a></div>
    </span><br>';
}
    
           
?>
