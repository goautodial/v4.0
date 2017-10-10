<?php

	/** get lists */
	/** **/

    require_once('goCRMAPISettings.php');

    $url = gourl."/goLists/goAPI.php"; #URL to GoAutoDial API. (required)
	$postfields["goUser"] = goUser; #Username goes here. (required)
	$postfields["goPass"] = goPass;
	$postfields["goAction"] = "goGetStatusesWithCountCalledNCalled"; #action performed by the [[API:Functions]]
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
	$s=0;
	$called = array();
	$ncalled = array();
	for($s=0;$s<count($output->stats);$s++){
		// if($output->called_since_last_reset[$s] == 'N'){
		// 	$countCalled = 0;
		// 	$countNCalled = $output->countvlists[$s];

		// }else{
		// 	$countCalled = $output->countvlists[$s];
		// 	$countNCalled = 0;
		// }
		array_push($called, $output->is_called[$s]);
		array_push($ncalled, $output->not_called[$s]);
		$data .= '<tr>';
		$data .= '<td>'.$output->stats[$s].'</td>';
		$data .= '<td>'.$output->status_name[$s].'</td>';
		$data .= '<td style="text-align: center; width: 15%;">'.$output->is_called[$s].'</td>';
		$data .= '<td style="text-align: center; width: 15%;">'.$output->not_called[$s].'</td>';
		$data .= '</tr>';
	}
	$total = array_sum($called) + array_sum($ncalled);

	$data .= '<tr>';
	$data .= '<td colspan="2" style="text-align: right;"><b>SUBTOTAL</b></td>';
	$data .= '<td style="text-align: center; width: 15%;">'.array_sum($called).'</td>';
	$data .= '<td style="text-align: center; width: 15%;">'.array_sum($ncalled).'</td>';
	$data .= '</tr>';
	$data .= '<tr>';
	$data .= '<td colspan="2" style="text-align: right;"><b>TOTAL</b></td>';
	$data .= '<td colspan="2" style="text-align: center; width: 30%;">'.$total.'</td>';
	$data .= '</tr>';

	echo json_encode($data, true);
?>
