<?php
/**
 * @file        DeleteCampaign.php
 * @brief       Handles Delete Campaign Requests
 * @copyright   Copyright (c) 2018 GOautodial Inc.
 * @author      Alexander Jim H. Abenoja  <alex@goautodial.com>
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
	
	$api 										= \creamy\APIHandler::getInstance();

	$postfields 								= array(
        'goAction' 									=> 'goDeleteCampaign',
        'campaign_id' 								=> $_POST['campaign_id'],
        'action'									=> $_POST['action']
    );

    $output 									= $api->API_Request( "goCampaigns", $postfields );

	if ( $output->result=="success" ) { 
		$status 								= 1; 
	} else { 
		$status 								= $output->result; 
	}

	echo json_encode( $status );
?>
