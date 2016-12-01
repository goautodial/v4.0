<?php

	/** get lists */
	/** **/

    require_once('goCRMAPISettings.php');

    $url = gourl."/goLists/goAPI.php"; #URL to GoAutoDial API. (required)
    $postfields["goUser"] = goUser; #Username goes here. (required)
    $postfields["goPass"] = goPass; #Password goes here. (required)
    $postfields["goAction"] = "getAllListsCampaign"; #action performed by the [[API:Functions]]. (required)
    $postfields["responsetype"] = responsetype; #json. (required)
    $postfields["campaign_id"] = $_POST['campaign_id'];

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_TIMEOUT, 100);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $postfields);
    $data = curl_exec($ch);
    curl_close($ch);
    $output = json_decode($data);
	//echo json_encode($output);
	//die;
		if(!empty($output)){
			$data = '';
			$i=0;
			for($i=0;$i<=count($output->campaign_id);$i++) {
				if(!empty($output->list_id[$i])){
					$info = array(
						'list_id' 						=> $output->list_id[$i],
						'list_name' 					=> $output->list_name[$i],
						'list_description' 				=> $output->list_description[$i],
						'campaign_id' 					=> $output->campaign_id[$i],
						'reset_time' 					=> $output->reset_time[$i],
						'reset_called_lead_status' 		=> $output->reset_called_lead_status[$i],
						'active' 						=> $output->active[$i],
						'agent_script_override' 		=> $output->agent_script_override[$i],
						'campaign_cid_override' 		=> $output->campaign_cid_override[$i],
						'drop_inbound_group_override' 	=> $output->drop_inbound_group_override[$i],
						'web_form_address' 				=> $output->web_form_address[$i]
					);
					$data .= '<tr>';
					$data .= '<td>'.$output->list_id[$i].'</td>';
					$data .= '<td>'.$output->list_name[$i].'</td>';
					$data .= '<td>'.$output->list_name[$i].'</td>';
					$data .= '<td>'.$output->tally[$i].'</td>';
					$data .= '<td>'.$output->active[$i].'</td>';
					$data .= '<td>'.$output->list_lastcalldate[$i].'</td>';
					$data .= "<td><a title='Modify/View List' class='edit-list' data-info='".json_encode($info)."' data-id='".$output->list_id[$i]."' data-campaign='".$output->campaign_id[$i]."'><span class='fa fa-eye'></span></a></td>";
					$data .= '</tr>';
				}
			}

			echo json_encode($data, true);
		}else{
			echo json_encode("empty", true);
		}


?>
