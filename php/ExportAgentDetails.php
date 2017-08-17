<?php
ini_set('memory_limit', '2048M');
require_once('goCRMAPISettings.php');

$pageTitle = $_POST['pageTitle'];
$session_user = $_POST['session_user'];
$fromDate = $_POST['fromDate'];
$toDate = $_POST['toDate'];
$campaignID = $_POST['campaignID'];

    $url = gourl."/goReports/goAPI.php"; //URL to GoAutoDial API. (required)
    $postfields["goUser"] = goUser; //Username goes here. (required)
    $postfields["goPass"] = goPass; //Password goes here. (required)
    $postfields["goAction"] = "goGetReports"; //action performed by the [[API:Functions]]. (required)
    $postfields["responsetype"] = responsetype; //json. (required)
    $postfields["session_user"] = $session_user; //current user
    $postfields["pageTitle"] = $pageTitle;
    $postfields["fromDate"]     = $fromDate;
    $postfields["toDate"]       = $toDate;
    $postfields["campaignID"]   = $campaignID;
    
    // if(isset($_POST["request"]))
    // $postfields["request"]      = $_POST["request"];
    
    // if(isset($_POST["userID"]))
    // $postfields["userID"]       = $_POST["userID"];
    
    // if(isset($_POST["userGroup"]))
    // $postfields["userGroup"]    = $_POST["userGroup"];
    
    // if(isset($_POST["statuses"]))
    // $postfields["statuses"]     = $_POST["statuses"];
    
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
    
   // echo $lists;
    //var_dump($output);
    
    if($output->result == "success"){
        if($pageTitle === "agent_detail"){
            $filename = "Agent_Time_Detail.".date("Y-m-d").".csv";
            $header = "Agent Time Detail     ".date("Y-m-d H:i:s")."\n";
            $header .= "Time Range: ".$fromDate." to ".$toDate."\n\n";
            $header .= "Full Name, User Name, Calls, Agent Time, WAIT, Talk, Dispo, Pause, Wrap-Up, Customer";
            if(!empty($output->getReports->sub_statusesTOP)){
                //$header .= ",";
                for($i=0; $i < count($output->getReports->sub_statusesTOP); $i++){
                    if(!empty($output->getReports->sub_statusesTOP[$i])){
                        $header .= ",".$output->getReports->sub_statusesTOP[$i];
                    }else{
                        $header .= ",";
                    }
                }
            }
        }
        
        header('Content-type: application/csv');
        header('Content-Disposition: attachment; filename='.$filename);
        
		echo $header."\n";
		
        //var_dump($output->getReports->FileExport[0]);
        $row = "";
            for($i=0; $i < count($output->getReports->FileExport); $i++){
                $name = $output->getReports->FileExport[$i]->name;
                $user = $output->getReports->FileExport[$i]->user;
                $number_of_calls = $output->getReports->FileExport[$i]->number_of_calls;
                $agent_time = $output->getReports->FileExport[$i]->agent_time;
                $wait_time = $output->getReports->FileExport[$i]->wait_time;
                $talk_time = $output->getReports->FileExport[$i]->talk_time;
                $dispo_time = $output->getReports->FileExport[$i]->dispo_time;
                $pause_time = $output->getReports->FileExport[$i]->pause_time;
                $wrap_up = $output->getReports->FileExport[$i]->wrap_up;
                $customer_time = $output->getReports->FileExport[$i]->customer_time;
                $statuses = explode(",", $output->getReports->FileExport[$i]->statuses);
                
                $row .= $name.",".$user.",".$number_of_calls.",".$agent_time.",".$wait_time.",".$talk_time.",".$dispo_time.",".$pause_time.",".$wrap_up.",".$customer_time;

                if(!empty($output->getReports->sub_statusesTOP)){
                    for($a=0; $a < count($output->getReports->sub_statusesTOP); $a++){
                        if(!empty($statuses[$a]))
                            $row .= ','.$statuses[$a];
                        else
                            $row .= ',00:00:00';
                    }
                }
                $row .= "\n";
            }
        //var_dump($row);
        echo $row;
        
    }
   
?>