<?php
/**
 * @file        ExportAgentDetails.php
 * @brief       Handles Exporting of Agent Details Report Requests
 * @copyright   Copyright (c) 2018 GOautodial Inc.
 * @author      Alexander Jim H. Abenoja
 *
 * @par <b>License</b>:
 *  This program is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU Affero General Public License as published by
 *  the Free Software Foundation, either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU Affero General Public License for more details.
 *
 *  You should have received a copy of the GNU Affero General Public License
 *  along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/

require_once('APIHandler.php');
require_once('CRMDefaults.php');

$api = \creamy\APIHandler::getInstance();

ini_set('memory_limit', '2048M');

$pageTitle = $_POST['pageTitle'];
$session_user = $_POST['session_user'];
$fromDate = $_POST['fromDate'];
$toDate = $_POST['toDate'];
$campaignID = $_POST['campaignID'];

/*
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
*/
   
    $postfields = array(
        //'goAction' => 'goGetReports',
	'goAction' => 'goExportAgentDetails',
        'pageTitle' => $pageTitle,
        'fromDate' => $fromDate,
        'toDate' => $toDate,
        'campaignID' => $campaignID
    );

    $output = $api->API_Request('goReports', $postfields);
	
    //var_dump($output);
    if($output->result == "success"){
        if($pageTitle === "agent_detail"){
            $filename = "Agent_Time_Detail.".date("Y-m-d").".csv";
            $header = "Agent Time Detail     ".date("Y-m-d H:i:s")."\n";
            $header .= "Time Range: ".$fromDate." to ".$toDate."\n\n";
            $header .= "Full Name, User Name, Calls, Agent Time, WAIT, Talk, Dispo, Pause, Wrap-Up, Customer";
            /*if(!empty($output->getReports->sub_statusesTOP)){
                //$header .= ",";
                for($i=0; $i < count($output->getReports->sub_statusesTOP); $i++){
                    if(!empty($output->getReports->sub_statusesTOP[$i])){
                        $header .= ",".$output->getReports->sub_statusesTOP[$i];
                    }else{
                        $header .= ",";
                    }
                }
            }*/
        }
        
        header('Content-type: application/csv');
        header('Content-Disposition: attachment; filename='.$filename);
        
		echo $header."\n";
		
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

                /*if(!empty($output->getReports->sub_statusesTOP)){
                    for($a=0; $a < count($output->getReports->sub_statusesTOP); $a++){
                        if(!empty($statuses[$a]))
                            $row .= ','.$statuses[$a];
                        else
                            $row .= ',00:00:00';
                    }
                }*/
                $row .= "\n";
            }
        echo $row;
        
    }
   
?>
