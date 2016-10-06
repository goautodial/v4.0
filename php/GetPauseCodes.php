<?php

	/** Campaigns API - Add a new Campaign */
	/**
	 * Generates action circle buttons for different pages/module
	 */

    require_once('goCRMAPISettings.php');

    $url = gourl."/goPauseCodes/goAPI.php"; #URL to GoAutoDial API. (required)
    $postfields["goUser"] = goUser; #Username goes here. (required)
    $postfields["goPass"] = goPass; #Password goes here. (required)
    $postfields["goAction"] = "getAllPauseCodes"; #action performed by the [[API:Functions]]. (required)
    $postfields["responsetype"] = responsetype; #json. (required)
    $postfields["pauseCampID"] = $_POST['campaign_id'];

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_TIMEOUT, 100);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $postfields);
    $data = curl_exec($ch);
    curl_close($ch);
    $output = json_decode($data);

		if(!empty($output)){
			$data = '';
			$i=0;
			for($i=0;$i<=count($output->campaign_id);$i++) {
				if(!empty($output->pause_code[$i])){
					$data .= '<tr>';
					$data .= '<td>'.$output->pause_code[$i].'</td>';
					$data .= '<td>'.str_replace("+"," ",$output->pause_code_name[$i]).'</td>';
					$data .= '<td>'.$output->billable[$i].'</td>';
					$data .= '<td style="width: 20%;"><a style="margin-right: 5px;" href="#"" class="btn-edit-pc btn btn-primary" data-camp-id="'.$output->campaign_id[$i].'" data-code="'.$output->pause_code[$i].'" data-name="'.str_replace("+"," ",$output->pause_code_name[$i]).'" data-billable="'.$output->billable[$i].'"><span class="fa fa-pencil"></span></a><a href="#" class="btn-delete-pc btn btn-danger" data-camp-id="'.$output->campaign_id[$i].'" data-code="'.$output->pause_code[$i].'"><span class="fa fa-trash"></span></a></td>';
					$data .= '</tr>';
				}
			}

			echo json_encode($data, true);
		}else{
			echo json_encode("empty", true);
		}


?>
