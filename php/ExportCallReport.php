<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
    /*
    for($i=0; $i < count($campaigns);$i++){
        echo $campaigns[$i];echo "<br>";
    } 
    for($i=0; $i < count($inbounds);$i++){
        echo $inbounds[$i];echo "<br>";
    }
    for($i=0; $i < count($lists);$i++){
        echo $lists[$i];echo "<br>";
    }
    for($i=0; $i < count($statuses);$i++){
        echo $statuses[$i];echo "<br>";
    }*/
    
    require_once('./goCRMAPISettings.php');
    
    $campaigns = $_POST['campaigns'];
    $inbounds = $_POST['inbounds'];
    $lists = $_POST['lists'];
    $statuses = $_POST['statuses'];
    $custom_fields = $_POST['custom_fields']; 
    $per_call_notes = $_POST['per_call_notes'];
    $toDate = date('Y-m-d H:i:s', strtotime($_POST['start_filterdate']));
    $fromDate = date('Y-m-d H:i:s', strtotime($_POST['end_filterdate']));
    
    $campaigns = implode(" ", $campaigns);
    $inbounds = implode(" ", $inbounds);
    $lists = implode(" ", $lists);
    $statuses = implode(" ", $statuses);
    
    $url = gourl."/goReports/goAPI.php"; #URL to GoAutoDial API. (required)
    $postfields["goUser"] = goUser; #Username goes here. (required)
    $postfields["goPass"] = goPass; #Password goes here. (required)
    $postfields["goAction"] = "goGetReports"; #action performed by the [[API:Functions]]. (required)
    $postfields["responsetype"] = responsetype; #json. (required)
    $postfields["pageTitle"] = "call_export_report"; 
    $postfields["campaigns"] = $campaigns;
    $postfields["inbounds"] = $inbounds;
    $postfields["lists"] = $lists;
    $postfields["statuses"] = $statuses;
    $postfields["custom_fields"] = $custom_fields;
    $postfields["per_call_notes"] = $per_call_notes;
    $postfields["toDate"] = $toDate;
    $postfields["fromDate"] = $fromDate;
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_TIMEOUT, 100);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $postfields);
    $data = curl_exec($ch);
    curl_close($ch);
    $output = json_decode($data);
    
   // echo $lists;
    //var_dump($output);
    if($output->result == "success"){
        $filename = $output->getReports->filename;
        $fp = fopen($filename, 'w');
    
        header('Content-type: application/csv');
        header('Content-Disposition: attachment; filename='.$filename);
        fputcsv($fp, $output->getReports->header);
        
        for($i=0; $i < count($output->getReports->header); $i++){
            fputcsv($fp, $output->getReports->rows);
        }
        exit;
    }
   
?>