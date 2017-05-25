<?php
require_once('../goCRMAPISettings.php');
require_once('../Session.php');
/*
* Displaying Total Active Leads
* [[API: Function]] - goGetTotalActiveLeads
* This application is used to get total number of active leads
*/
   
	$url = gourl."/goDashboard/goAPI.php"; #URL to GoAutoDial API. (required)
	$postfields["goUser"] = goUser; #Username goes here. (required)
	$postfields["goPass"] = goPass;
	$postfields["goAction"] = "goGetTotalActiveLeads"; #action performed by the [[API:Functions]]
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
   
   //var_dump($data);
    $data = explode(";",$data);
    foreach ($data AS $temp) {
      $temp = explode("=",$temp);
      $results[$temp[0]] = $temp[1];
    }
   
    if ($results["result"]=="success") {
      # Result was OK!
      //var_dump($results); #to see the returned arrays.
           echo number_format($results["getTotalActiveLeads"]);
    } else {
      # An error occurred
      echo "0";
    }
?>
