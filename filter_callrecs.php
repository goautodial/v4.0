<?php
require_once('./php/UIHandler.php');
include('./php/Session.php');

$ui = \creamy\UIHandler::getInstance();
$user = \creamy\CreamyUser::currentUser();


if($_POST['start_filterdate'] != "" && $_POST['end_filterdate'] != "" && $_POST['agent_filter'] == ""){ // if agent filter is empty

  if($_POST['start_filterdate'] != ""){
    $start = date('Y-m-d H:i:s', strtotime($_POST['start_filterdate']));
  }else{
    $start = "";
  }

  if($_POST['start_filterdate'] != ""){
    $end = date('Y-m-d H:i:s', strtotime($_POST['start_filterdate']));
  }else{
    $end = "";
  }

  if($_POST['search_recordings'] != ""){
    $output = $ui->getListAllRecordings($_POST['search_recordings'], $start, $end);
  }else{
    $output = $ui->getListAllRecordings(NULL, $start, $end);
  }
    
  echo $output;

}else if($_POST['start_filterdate'] == "" && $_POST['end_filterdate'] != "" && $_POST['agent_filter'] != ""){ // if start date is empty

  if($_POST['search_recordings'] != ""){
    $output = $ui->getListAllRecordings($_POST['search_recordings'], $start, $end, $_POST['agent_filter']);
  }else{
    $output = $ui->getListAllRecordings(NULL, $start, $end, $_POST['agent_filter']);
  }

  echo $output;

}else if($_POST['start_filterdate'] != "" && $_POST['end_filterdate'] != "" && $_POST['agent_filter'] != ""){ // if all posts are not empty

  if($_POST['start_filterdate'] != ""){
    $start = date('Y-m-d H:i:s', strtotime($_POST['start_filterdate']));
  }else{
    $start = "";
  }

  if($_POST['start_filterdate'] != ""){
    $end = date('Y-m-d H:i:s', strtotime($_POST['start_filterdate']));
  }else{
    $end = "";
  }

    if($_POST['search_recordings'] != ""){
      $output = $ui->getListAllRecordings($_POST['search_recordings'], $start, $end, $_POST['agent_filter']);
    }else{
      $output = $ui->getListAllRecordings(NULL, $start, $end, $_POST['agent_filter']);
    }
  echo $output;  
}



?>