<?php
require_once('./php/UIHandler.php');
include('./php/Session.php');

$ui = \creamy\UIHandler::getInstance();
$user = \creamy\CreamyUser::currentUser();

// if search is not empty
  if($_POST['search_contacts'] != ""){
    $output = $ui->GetContacts($_SESSION['user'], $_POST['search_contacts'], $_POST['disposition'], $_POST['list'], $_POST['address'], $_POST['city'], $_POST['state']);
  }else{
    $output = $ui->GetContacts($_SESSION['user'], NULL, $_POST['disposition'], $_POST['list'], $_POST['address'], $_POST['city'], $_POST['state']);
  }


echo $output;

?>