<?php
require_once('../goCRMAPISettings.php');
/*
* Displaying Total Dropped Calls
* [[API: Function]] - goGetTotalDroppedCalls
* This application is used to get total dropped calls.
*/

   $url = gourl."/goDashboard/goAPI.php"; #URL to GoAutoDial API. (required)
   $postfields["goUser"] = goUser; #Username goes here. (required)
   $postfields["goPass"] = goPass;
   $postfields["goAction"] = "goGetTotalDroppedCalls"; #action performed by the [[API:Functions]]
   
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
           echo number_format($results["getTotalDroppedCalls"]);
   } else {
      # An error occurred
           echo 0;
   }

?>
