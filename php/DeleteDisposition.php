<?php
require_once('CRMDefaults.php');
require_once('goCRMAPISettings.php');

// check required fields
$validated = 1;
if (!isset($_POST["disposition_id"])) {
	$validated = 0;
}

if ($validated == 1) {
	$disposition_id = $_POST["disposition_id"];

	if(isset($_POST['status'])){
        $status = $_POST["status"];

        $url = gourl."/goDispositions/goAPI.php"; #URL to GoAutoDial API. (required)
        $postfields["goUser"] = goUser; #Username goes here. (required)
        $postfields["goPass"] = goPass; #Password goes here. (required)
        $postfields["goAction"] = "goDeleteDisposition"; #action performed by the [[API:Functions]]. (required)
        $postfields["responsetype"] = responsetype; #json. (required)
        $postfields["campaign_id"] = $disposition_id; #Desired User ID. (required)
        $postfields["statuses"] = $status;
        $postfields["hostname"] = $_SERVER['REMOTE_ADDR']; #Default value
		$postfields["log_user"] = $_POST['log_user'];
		$postfields["log_group"] = $_POST['log_group'];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        // curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 100);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postfields);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $data = curl_exec($ch);
        curl_close($ch);
        $output = json_decode($data);

        if ($output->result=="success") {
        # Result was OK!
            echo 1;
        }else{
            echo $output->result;
        }
    }else{
        $url = gourl."/goDispositions/goAPI.php"; #URL to GoAutoDial API. (required)
        $postfields["goUser"] = goUser; #Username goes here. (required)
        $postfields["goPass"] = goPass; #Password goes here. (required)
        $postfields["goAction"] = "goDeleteDisposition"; #action performed by the [[API:Functions]]. (required)
        $postfields["responsetype"] = responsetype; #json. (required)
        $postfields["campaign_id"] = $disposition_id; #Desired User ID. (required)
        $postfields["hostname"] = $_SERVER['REMOTE_ADDR']; #Default value

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        // curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 100);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postfields);
        $data = curl_exec($ch);
        curl_close($ch);
        $output = json_decode($data);

        if ($output->result=="success") {
        # Result was OK!
    		echo 1;
        }else{
            echo $output->result;
        }
    }
}


?>
