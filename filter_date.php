<?php
require_once('./php/UIHandler.php');
include('./php/Session.php');

$ui = \creamy\UIHandler::getInstance();
$user = \creamy\CreamyUser::currentUser();

if($_POST['start_filterdate'] != "" && $_POST['end_filterdate'] != ""){

$start =  date('Y-m-d H:i:s', strtotime($_POST['start_filterdate']));
$end = date('Y-m-d H:i:s', strtotime($_POST['end_filterdate']));

  if($_POST['search_recordings'] != ""){
    $output = $ui->getListAllRecordings($_POST['search_recordings'], $start, $end);
  }else{
    $output = $ui->getListAllRecordings(NULL, $start, $end);
  }
  /*
    echo $start;
    echo "<br>";
    echo $end;
    echo "<br>";
    var_dump($output);
*/
    echo $output;
}


?>