<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once('php/goCRMAPISettings.php');

$pageTitle = $_POST['pageTitle'];

$url = gourl."/goJamesReports/goAPI.php"; #URL to GoAutoDial API. (required)
$postfields["goUser"] = goUser; #Username goes here. (required)
$postfields["goPass"] = goPass; #Password goes here. (required)
$postfields["goAction"] = "goGetReports"; #action performed by the [[API:Functions]]. (required)
$postfields["responsetype"] = responsetype; #json. (required)
$postfields["pageTitle"] = $pageTitle;

if(isset($_POST["fromDate"]))
$postfields["fromDate"] 	= $_POST["fromDate"];

if(isset($_POST["toDate"]))
$postfields["toDate"] 		= $_POST["toDate"];

if(isset($_POST["campaignID"]))
$postfields["campaignID"] 	= $_POST["campaignID"];

if(isset($_POST["request"]))
$postfields["request"] 		= $_POST["request"];

if(isset($_POST["userID"]))
$postfields["userID"] 		= $_POST["userID"];

if(isset($_POST["userGroup"]))
$postfields["userGroup"] 	= $_POST["userGroup"];

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_TIMEOUT, 100);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, $postfields);
$data = curl_exec($ch);
curl_close($ch);
$output = json_decode($data);

print_r($output);
?>
