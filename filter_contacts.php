<?php
require_once('./php/UIHandler.php');
include('./php/Session.php');

$ui = \creamy\UIHandler::getInstance();
$user = \creamy\CreamyUser::currentUser();


if($_POST['disposition'] != "" && $_POST['list'] != "" && $_POST['address'] != "" && $_POST['city'] != "" && $_POST['state'] != ""){ // if all fields are not empty

  // if search is not empty
    if($_POST['search_contacts'] != ""){
      $output = $ui->GetContacts($user->getUserName(), $_POST['search_contacts'], $_POST['disposition'], $_POST['list'], $_POST['address'], $_POST['city'], $_POST['state']);
    }else{
      $output = $ui->GetContacts($user->getUserName(), NULL, $_POST['disposition'], $_POST['list'], $_POST['address'], $_POST['city'], $_POST['state']);
    }

}else if($_POST['disposition'] == "" && $_POST['list'] != "" && $_POST['address'] != "" && $_POST['city'] != "" && $_POST['state'] != ""){ // if disposition is empty

  // if search is not empty
    if($_POST['search_contacts'] != ""){
      $output = $ui->GetContacts($user->getUserName(), $_POST['search_contacts'], NULL, $_POST['list'], $_POST['address'], $_POST['city'], $_POST['state']);
    }else{
      $output = $ui->GetContacts($user->getUserName(), NULL, NULL, $_POST['list'], $_POST['address'], $_POST['city'], $_POST['state']);
    }

}else if($_POST['disposition'] != "" && $_POST['list'] == "" && $_POST['address'] != "" && $_POST['city'] != "" && $_POST['state'] != ""){ // if list is empty

  // if search is not empty
    if($_POST['search_contacts'] != ""){
      $output = $ui->GetContacts($user->getUserName(), $_POST['search_contacts'], $_POST['disposition'], NULL, $_POST['address'], $_POST['city'], $_POST['state']);
    }else{
      $output = $ui->GetContacts($user->getUserName(), NULL, $_POST['disposition'], NULL, $_POST['address'], $_POST['city'], $_POST['state']);
    }
}else if($_POST['disposition'] != "" && $_POST['list'] != "" && $_POST['address'] == "" && $_POST['city'] != "" && $_POST['state'] != ""){ // if address is empty

  // if search is not empty
    if($_POST['search_contacts'] != ""){
      $output = $ui->GetContacts($user->getUserName(), $_POST['search_contacts'], $_POST['disposition'], $_POST['list'], NULL, $_POST['city'], $_POST['state']);
    }else{
      $output = $ui->GetContacts($user->getUserName(), NULL, $_POST['disposition'], $_POST['list'], NULL, $_POST['city'], $_POST['state']);
    }

}else if($_POST['disposition'] != "" && $_POST['list'] != "" && $_POST['address'] != "" && $_POST['city'] == "" && $_POST['state'] != ""){ // if city is empty

  // if search is not empty
    if($_POST['search_contacts'] != ""){
      $output = $ui->GetContacts($user->getUserName(), $_POST['search_contacts'], $_POST['disposition'], $_POST['list'], $_POST['address'], NULL, $_POST['state']);
    }else{
      $output = $ui->GetContacts($user->getUserName(), NULL, $_POST['disposition'], $_POST['list'], $_POST['address'], NULL, $_POST['state']);
    }
}else if($_POST['disposition'] != "" && $_POST['list'] != "" && $_POST['address'] != "" && $_POST['city'] != "" && $_POST['state'] == ""){ // if state is empty

  // if search is not empty
    if($_POST['search_contacts'] != ""){
      $output = $ui->GetContacts($user->getUserName(), $_POST['search_contacts'], $_POST['disposition'], $_POST['list'], $_POST['address'], $_POST['city'], NULL);
    }else{
      $output = $ui->GetContacts($user->getUserName(), NULL, $_POST['disposition'], $_POST['list'], $_POST['address'], $_POST['city'], NULL);
    }
}else{
  // if search is not empty
    if($_POST['search_contacts'] != ""){
      $output = $ui->GetContacts($user->getUserName(), $_POST['search_contacts'], $_POST['disposition'], $_POST['list'], $_POST['address'], $_POST['city'], $_POST['state']);
    }else{
      $output = $ui->GetContacts($user->getUserName(), NULL, $_POST['disposition'], $_POST['list'], $_POST['address'], $_POST['city'], $_POST['state']);
    }
}

echo $output;

?>