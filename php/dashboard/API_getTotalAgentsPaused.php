<?php
/**
 * @file        API_getTotalAgentsPaused.php
 * @brief       Displays the total number of paused agents
 * @copyright   Copyright (c) 2018 GOautocial Inc.
 * @author		Demian Lizandro A. Biscocho 
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
	$output 									= $api->API_getTotalAgentsPaused();        
    $agent 										= $output->data;
        
    if (empty($agent) || is_null($agent)){
        $agent									= 0;
    }
        
    echo json_encode($agent); 

?>
