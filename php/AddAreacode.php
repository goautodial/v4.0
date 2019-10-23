<?php
/**
 * @file        AddAreacode.php
 * @brief       Handles Add AC CID Request
 * @copyright   Copyright (c) 2019 GOautodial Inc.
 * @author      Thom Bernarth Patacsil
 *
 * @par <b>License</b>:
 *  This program is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU Affero General Public License as published by
 *  the Free Software Foundation, either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU Affero General Public License for more details.
 *
 *  You should have received a copy of the GNU Affero General Public License
 *  along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/

	require_once('APIHandler.php');
	$api 								= \creamy\APIHandler::getInstance();

	$campaign_id = NULL;
	if(isset($_POST['areacode_campaign'])){
		$campaign_id 			= $_POST['areacode_campaign'];
	}
	
	$areacode = NULL;
	if(isset($_POST['areacode'])){
                $areacode                  	= $_POST['areacode'];
        }
	
	$outbound_cid = NULL;
	if(isset($_POST['areacode_outbound_cid'])){
                $outbound_cid                   = $_POST['areacode_outbound_cid'];
        }

	$cid_description = NULL;
	if(isset($_POST['areacode_description'])){
                $cid_description                    = $_POST['areacode_description'];
        }


	$postfields 						= array(
		'goAction' 							=> 'goAddAreacode',
		'campaign_id' 							=> $campaign_id,
		'areacode'							=> $areacode,
		'outbound_cid'							=> $outbound_cid,
		'cid_description'							=> $cid_description
	);

	$output 							= $api->API_addAreacode($postfields);
	
	if ($output->result=="success") { 
		$status 						= 1; 
	} else { 
		$status 						= $output->result; 
	}
	
	echo json_encode($status);

?>
