<?php

	require_once(__DIR__ . '/UIHandler.php');
    require_once(__DIR__ . '/goCRMAPISettings.php');
	include(__DIR__ . '/Session.php');
	
	$ui = \creamy\UIHandler::getInstance();
	//$perm = $ui->goGetPermissions('pausecodes', $_SESSION['usergroup']);

    $url = gourl."/goLists/goAPI.php"; #URL to GoAutoDial API. (required)
    $postfields["goUser"] = goUser; #Username goes here. (required)
    $postfields["goPass"] = goPass; #Password goes here. (required)
    $postfields["goAction"] = "getAllDNC"; #action performed by the [[API:Functions]]. (required)
    $postfields["responsetype"] = responsetype; #json. (required)

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_TIMEOUT, 100);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $postfields);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    $data = curl_exec($ch);
    $output = json_decode($data);

		if(!empty($output)){
			$data = '';
			$i=0;
            $counter = count($output->phone_number);
			for($i=0;$i<=$counter;$i++) {
				if(!empty($output->phone_number[$i])){
					$data .= '<tr>';
					$data .= '<td>'.$output->phone_number[$i].'</td>';
					$data .= '<td style="width: 20%;"><a href="#" class="btn-delete-dnc btn btn-danger" data-phone="'.$output->phone_number[$i].'"><span class="fa fa-trash"></span></a></td>';
					$data .= '</tr>';
				}
			}

			echo json_encode($data, true);
		}else{
			echo json_encode("empty", true);
		}


?>
