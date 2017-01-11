<?php
    require_once('goCRMAPISettings.php');
    
    $url = gourl."/goCampaigns/goAPI.php"; # URL to GoAutoDial API file
	$postfields["goUser"]           = goUser; #Username goes here. (required)
	$postfields["goPass"]           = goPass; #Password goes here. (required)
	$postfields["goAction"]         = "goGetSuggestedDIDs"; #action performed by the [[API:Functions]]
	$postfields["responsetype"]     = responsetype; #json (required)
	$postfields["hostname"]         = $_SERVER['REMOTE_ADDR']; #Default value

	$postfields['keyword']          = trim(strip_tags($_POST['term']));
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_TIMEOUT, 100);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $postfields);
    $data = curl_exec($ch);
    curl_close($ch);
    $output = json_decode($data);
    
    if ($output->result=="success") {
        echo json_encode($output->data, true);
    } else {
        echo json_encode("");
    }
?>