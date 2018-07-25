<?php
/**
 * @file        checkCalltime.php
 * @brief       API helper for add user wizard
 * @copyright   Copyright (c) 2018 GOautodial Inc.
 * @author      Demian Lizandro A. Biscocho
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
	$api = \creamy\APIHandler::getInstance();

	$output = $api->API_getAllUsers();
	$phones = $api->API_getAllPhones();	
    $max = max($phones->extension);
    $phone_login = $max + 1;	
    $agent_num = $output->last_count;
    $num_padded = sprintf("%03d", $agent_num);                                        
    $full_name = "Agent ".$num_padded;
    $user_id = "agent".$num_padded;  

    $result = '[{"user_id":"'.$user_id.'","full_name":"'.$full_name.'","phone_login":"'.$phone_login.'"}]';
    
    echo json_encode($result);

?>
