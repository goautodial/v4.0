<?php
require_once('./php/UIHandler.php');
include('./php/Session.php');

$ui = \creamy\UIHandler::getInstance();
$user = \creamy\CreamyUser::currentUser();

if($_POST['search_contacts'] != ""){

    $output = $ui->GetContacts($user->getUserName(), $_POST['search_contacts'], $_POST['disposition'], $_POST['list'], $_POST['address'], $_POST['city'], $_POST['state']);
    echo $output;
}else if($_POST['search_contacts'] == ""){

  $output = $ui->GetContacts($user->getUserName(), NULL, $_POST['disposition'], $_POST['list'], $_POST['address'], $_POST['city'], $_POST['state']);
  echo $output;

}

if($_POST['search_recordings'] != ""){

  if($_POST['start_filterdate'] != "" && $_POST['end_filterdate'] != ""){
    $start =  date('Y-m-d H:i:s', strtotime($_POST['start_filterdate']));
    $end = date('Y-m-d H:i:s', strtotime($_POST['end_filterdate']));
    $output = $ui->getListAllRecordings($_POST['search_recordings'], $start, $end);
  }else{
    $output = $ui->getListAllRecordings($_POST['search_recordings']);
  }

  echo $output;

}else if($_POST['search_recordings'] == "" && $_POST['start_filterdate'] != "" && $_POST['end_filterdate'] != ""){

  if($_POST['start_filterdate'] != "" && $_POST['end_filterdate'] != ""){
    $start =  date('Y-m-d H:i:s', strtotime($_POST['start_filterdate']));
    $end = date('Y-m-d H:i:s', strtotime($_POST['end_filterdate']));
    $output = $ui->getListAllRecordings(NULL, $start, $end);
  }
  echo $output;

}


?>