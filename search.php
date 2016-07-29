<?php
require_once('./php/UIHandler.php');

$ui = \creamy\UIHandler::getInstance();


if($_POST['search_phone'] != ""){
   // $output = $ui->API_getListAllRecordings($_POST['search_phone']);

   // if($output->result == "success"){
    	echo $_POST['search_phone'];
   // }
}

if($_POST['search_agent'] != ""){
   // $output = $ui->API_getListAllRecordings($_POST['search_agent']);

  //  if($output->result == "success"){
    	echo $_POST['search_agent'];
   // }
}

if($_POST['search_customer'] != ""){
  //  $output = $ui->API_getListAllRecordings($_POST['search_customer']);

  //  if($output->result == "success"){
    	echo $_POST['search_customer'];
   // }
}

if($_POST['search'] != ""){
  //  $output = $ui->API_getListAllRecordings();

  //  if($output->result == "success"){
    	echo "";
  //  }
}

?>