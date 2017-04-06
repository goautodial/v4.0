<?php
require_once('../goCRMAPISettings.php');
require_once('../Session.php');
/*
    * Displaying in Sales / Hour
    * [[API: Function]] - goGetINSalesHour
    * This application is used to get total number of in Sales per hour
*/
    
    $url = gourl."/goDashboard/goAPI.php"; #URL to GoAutoDial API. (required)
    $postfields["goUser"] = goUser; #Username goes here. (required)
    $postfields["goPass"] = goPass;
    $postfields["goAction"] = "goGetINSalesHour"; #action performed by the [[API:Functions]]
    $postfields["session_user"] = $_SESSION['user']; #current user
	
     $ch = curl_init();
     curl_setopt($ch, CURLOPT_URL, $url);
     curl_setopt($ch, CURLOPT_POST, 1);
     curl_setopt($ch, CURLOPT_TIMEOUT, 100);
     curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
     curl_setopt($ch, CURLOPT_POSTFIELDS, $postfields);
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
            echo $results["getInSalesPerHour"];
     } else {
       # An error occurred
       echo 0;
     }

?>