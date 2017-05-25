<?php

	/** get lists */
	/** **/

    require_once('goCRMAPISettings.php');

    $url = gourl."/goLists/goAPI.php"; #URL to GoAutoDial API. (required)
	$postfields["goUser"] = goUser; #Username goes here. (required)
	$postfields["goPass"] = goPass;
	$postfields["goAction"] = "goGetTZonesWithCountCalledNCalled"; #action performed by the [[API:Functions]]
	$postfields["responsetype"] = responsetype;
	$postfields["list_id"] = $_POST['list_id'];

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

	$data = '';
	$t=0;
	$tcalled = array();
	$tncalled = array();
	for($t=0;$t<count($output->gmt_offset_now);$t++){
		if($output->called_since_last_reset[$t] == 'N'){
			$counttCalled = 0;
			$counttNCalled = $output->counttlist[$t];

		}else{
			$counttCalled = $output->counttlist[$t];
			$counttNCalled = 0;
		}
		array_push($tcalled, $counttCalled);
		array_push($tncalled, $counttNCalled);
		$data .= '<tr>';
		$data .= '<td>'.$output->gmt_offset_now[$t].' ('.gmdate("D M Y H:i", time() + 3600 * $output->gmt_offset_now[$t]).')</td>';
		$data .= '<td style="text-align: center; width: 15%;">'.$counttCalled.'</td>';
		$data .= '<td style="text-align: center; width: 15%;">'.$counttNCalled.'</td>';
		$data .= '</tr>';
	}
	$totalt = array_sum($tcalled) + array_sum($tncalled);

	$data .= '<tr>';
	$data .= '<td style="text-align: right;"><b>SUBTOTAL</b></td>';
	$data .= '<td style="text-align: center; width: 15%;">'.array_sum($tcalled).'</td>';
	$data .= '<td style="text-align: center; width: 15%;">'.array_sum($tncalled).'</td>';
	$data .= '</tr>';
	$data .= '<tr>';
	$data .= '<td style="text-align: right;"><b>TOTAL</b></td>';
	$data .= '<td colspan="2" style="text-align: center; width: 30%;">'.$totalt.'</td>';
	$data .= '</tr>';

	echo json_encode($data, true);

?>
