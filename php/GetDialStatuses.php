<?php

	/** Campaigns API - Add a new Campaign */
	/**
	 * Generates action circle buttons for different pages/module
	 */

    require_once('goCRMAPISettings.php');

		$url = gourl."/goDialStatus/goAPI.php"; #URL to GoAutoDial API. (required)
		$postfields["goUser"] = goUser; #Username goes here. (required)
		$postfields["goPass"] = goPass; #Password goes here. (required)
		$postfields["goAction"] = "getAllDialStatuses"; #action performed by the [[API:Functions]]. (required)
		$postfields["responsetype"] = responsetype; #json. (required)
		$postfields["campaign_id"] = $_POST['campaign_id'];
		$postfields["hotkeys_only"] = $_POST['hotkeys_only'];

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
			// $i=0;
			foreach($output->status as $key => $val){
			// for($i=0;$i<=count($output->status);$i++) {
				$data .= '<option value="'.$val.'" data-name="'.$output->status_name[$key].'">'.$val.' - '.$output->status_name[$key].'</option>';
			}

			echo json_encode($data, true);
		}else{
			echo json_encode("empty", true);
		}


?>
