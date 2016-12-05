<?php
/*
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
    }*/
    
    require_once('./goCRMAPISettings.php');
	
    $url = gourl."/goLists/goAPI.php"; #URL to GoAutoDial API. (required)
    $postfields["goUser"] = goUser; #Username goes here. (required)
    $postfields["goPass"] = goPass; #Password goes here. (required)
    $postfields["goAction"] = "goListExport"; #action performed by the [[API:Functions]]. (required)
    $postfields["responsetype"] = responsetype; #json. (required)
    $postfields["list_id"] = $_POST["listid"];
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_TIMEOUT, 1000);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $postfields);
    $data = curl_exec($ch);
    curl_close($ch);
    $output = json_decode($data);
//    echo "<pre>";
//	print_r($output);
//	echo "</pre>";
	
    if($output->result == "success"){
        //$filename = $output->getReports->filename;
        
        $header = implode(",",$output->header);
        
        $filename = "LIST_.".$_POST["listid"]."_".date("Ymd")."_".date("His").".csv";
        //$fp = fopen($filename, 'w');
        
        header('Content-type: application/csv');
        header('Content-Disposition: attachment; filename='.$filename);
        
        echo $header."\n";
        
        for($i=0; $i <= count($output->header); $i++){
            echo $output->row[$i]."\n";
        }
        
        
    }
   
?>