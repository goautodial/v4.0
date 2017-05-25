<?php

	/** get lists */
	/** **/

    require_once('goCRMAPISettings.php');

    $url = gourl."/goLists/goAPI.php"; #URL to GoAutoDial API. (required)
    $postfields["goUser"] = goUser; #Username goes here. (required)
    $postfields["goPass"] = goPass; #Password goes here. (required)
    $postfields["goAction"] = "goGetAllLeadsOnHopper"; #action performed by the [[API:Functions]]. (required)
    $postfields["responsetype"] = responsetype; #json. (required)
    $postfields["campaign_id"] = $_POST['campaign_id'];

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
		$count = 0;
		$dial_status = explode(" ", $output->camp_dial_status[0]);
		$statuses = array();
		foreach($dial_status as $status){
			if(!empty($status)){
				array_push($statuses, $status);
			}
		}
		$availableStats = array();
		for($i=0;$i<=count($output->lead_id);$i++) {
			array_push($availableStats, $output->status[$i]);
			if(!empty($output->hopper_id[$i]) && in_array($output->status[$i], $statuses)){
				$count = $count + 1;
				$data .= '<tr>';
				$data .= '<td>'.$output->hopper_id[$i].'</td>';
				$data .= '<td>'.$output->priority[$i].'</td>';
				$data .= '<td>'.$output->lead_id[$i].'</td>';
				$data .= '<td>'.$output->list_id[$i].'</td>';
				$data .= '<td>'.$output->phone_number[$i].'</td>';
				$data .= '<td>'.$output->state[$i].'</td>';
				$data .= '<td>'.$output->status[$i].'</td>';
				$data .= '<td>'.$output->called_count[$i].'</td>';
				$data .= '<td>'.$output->gmt_offset_now[$i].'</td>';
				$data .= '<td>'.$output->alt_dial[$i].'</td>';
				$data .= '<td>'.$output->source[$i].'</td>';
				$data .= '</tr>';
			}
		}
		
		$details['count'] = $count;
		$details['data'] = $data;
		$details['stats'] = $statuses;
		$details['data_stats'] = $availableStats;
		echo json_encode($details, true);
	}else{
		echo json_encode("empty", true);
	}

?>
