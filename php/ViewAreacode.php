<?php
/**
 * @file        ViewAreacode.php
 * @brief       Displays Areacode information
 * @copyright   Copyright (c) 2019 GOautodial Inc.
 * @author	Thom Bernarth Patacsil 
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

	$campaign_id = $_POST['campaign_id'];
	$areacode = $_POST['areacode'];

	$api 										= \creamy\APIHandler::getInstance();

	$postfields = array(
		'goAction' => 'goGetAreacodeInfo',
		'campaign_id' => $campaign_id,
		'areacode' => $areacode
	);

	$output 									= $api->API_getAreacodeInfo($postfields);

    $areacode									= $output->data;
    
    if ( empty($areacode) || is_null($areacode) ) {
        $areacode 									= 0;
    }
        
    echo json_encode($output);

?>
