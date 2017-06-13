<?php
require_once('./php/UIHandler.php');
include('./php/Session.php');

$ui = \creamy\UIHandler::getInstance();
$user = \creamy\CreamyUser::currentUser();

if(isset($_POST['user'])){
	if(isset($_POST['start_date'])){
		$output = $ui->getAgentLog($_POST['user'], $_SESSION['user'], $_POST['start_date'], $_POST['end_date']);
	}else{
		$output = $ui->getAgentLog($_POST['user'], $_SESSION['user']);
	}
	
    echo $output;
}



?>