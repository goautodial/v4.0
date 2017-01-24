<?php
require_once('./php/UIHandler.php');
include('./php/Session.php');

$ui = \creamy\UIHandler::getInstance();
$user = \creamy\CreamyUser::currentUser();

if(isset($_POST['search_dnc'])){

    $output = $ui->GetDNC($_POST['search_dnc']);
    echo $output;
}



?>