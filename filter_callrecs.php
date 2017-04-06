<?php
require_once('./php/UIHandler.php');
include('./php/Session.php');

$ui = \creamy\UIHandler::getInstance();
$user = \creamy\CreamyUser::currentUser();

  if(isset($_POST['start_filterdate']) && $_POST['start_filterdate'] != ""){
    $start = date('Y-m-d H:i:s', strtotime($_POST['start_filterdate']));
  }else{
    $start = "";
  }

  if(isset($_POST['end_filterdate']) && $_POST['end_filterdate'] != ""){
    $end = date('Y-m-d H:i:s', strtotime($_POST['end_filterdate']));
  }else{
    $end = "";
  }

  if($_POST['search_recordings'] != ""){
    $search = $_POST['search_recordings'];
  }else{
    $search = "";
  }
  
  if($_POST['agent_filter'] != ""){
    $agent = $_POST['agent_filter'];
  }else{
    $agent = "";
  }
  
    $output = $ui->getListAllRecordings($search, $start, $end, $agent, $_SESSION['user']);
  
  echo $output;  

?>