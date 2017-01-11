<?php

	/** Campaigns API - Add a new Campaign */
	/**
	 * Generates action circle buttons for different pages/module
	 */

    require_once('goCRMAPISettings.php');

		$url = gourl."/goCampaigns/goAPI.php"; #URL to GoAutoDial API. (required)
		$postfields["goUser"] = goUser; #Username goes here. (required)
		$postfields["goPass"] = goPass; #Password goes here. (required)
		$postfields["goAction"] = "getAllAudioFiles"; #action performed by the [[API:Functions]]. (required)
		$postfields["responsetype"] = responsetype; #json. (required)

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_TIMEOUT, 100);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $postfields);
		$data = curl_exec($ch);
		curl_close($ch);
		$output = json_decode($data);

		if($output->result=="success"){
			$data = '';
			foreach($output->data as $file){
				if(strpos($file, "go_") !== false){
					$data .= '<li><a style="color: #000;" href="#" class="file-list" data-name="'.$file.'">'.$file.'</a></li>';
				}
			}

			echo json_encode($data, true);
		}else{
			echo json_encode("empty", true);
		}
?>
