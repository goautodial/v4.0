<?php

	/** Campaigns API - Add a new Campaign */
	/**
	 * Generates action circle buttons for different pages/module
	 */

	require_once('UIHandler.php');
    require_once('goCRMAPISettings.php');
	include('Session.php');
	
	$ui = \creamy\UIHandler::getInstance();
	$perm = $ui->goGetPermissions('hotkeys', $_SESSION['usergroup']);

    $url = gourl."/goHotkeys/goAPI.php"; #URL to GoAutoDial API. (required)
    $postfields["goUser"] = goUser; #Username goes here. (required)
    $postfields["goPass"] = goPass; #Password goes here. (required)
    $postfields["goAction"] = "getAllHotkeys"; #action performed by the [[API:Functions]]. (required)
    $postfields["responsetype"] = responsetype; #json. (required)
    $postfields["hotkeyCampID"] = $_POST['campaign_id'];

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_TIMEOUT, 100);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $postfields);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    $data = curl_exec($ch);
    curl_close($ch);
    $output = json_decode($data);

		if(!empty($output)){
			$data = '';
			$i=0;
			for($i=0;$i<=count($output->campaign_id);$i++) {
				if(!empty($output->hotkey[$i])){
					$data .= '<tr>';
					$data .= '<td>'.$output->hotkey[$i].'</td>';
					$data .= '<td>'.$output->status[$i].'</td>';
					$data .= '<td>'.str_replace("+"," ",$output->status_name[$i]).'</td>';
					$data .= '<td style="width: 20%;"><a href="#" class="btn-delete-hk btn btn-danger'.($perm->hotkeys_delete === 'N' ? ' hidden' : '').'" data-camp-id="'.$output->campaign_id[$i].'" data-hotkey="'.$output->hotkey[$i].'"><span class="fa fa-trash"></span></a></td>';
					$data .= '</tr>';
				}
			}

			echo json_encode($data, true);
		}else{
			echo json_encode("empty", true);
		}


?>
