<?php
/**
 * @file        API_getClusterStatus.php
 * @brief       Displays server status
 * @copyright   Copyright (c) 2020 GOautodial Inc.
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
	$output 									= $api->API_getClusterStatus();    
    $cluster 									= '[';
     
    foreach ($output->data as $key => $value) {
    
        $serverid 								= $value->server_id;
        $serverdesc 							= $value->server_description;
        $serverip 								= $value->server_ip;
        $status 								= $value->active;
        $load 									= $value->sysload;    
        $channels 								= $value->channels_total;
        $cpuidlepercent 						= $value->cpu_idle_percent;
        $diskusage 								= $value->disk_usage;
        $stime 									= $value->s_time;
        $utime 									= $value->u_time;
    
        $disk_ary 								= explode('|', $diskusage);
        $disk_ary_ct 							= count($disk_ary);

        if ($status == "Y") {
            $status 							= "ACTIVE";
            $statustextclass 					= "text-success";
        }
        
        if ($status == "N") {
            $status								= "INACTIVE";
            $statustextclass 					= "text-danger";
        }
        
        $k 										= 0;

        while ($k < $disk_ary_ct) {
			$disk_ary[$k] 						= preg_replace("/^\d* /","",$disk_ary[$k]);
			//print_r($disk_ary);
			if ($k<1) {
				$disk 							= "$disk_ary[$k]";
			} else {
				if ($disk_ary[$k] > $disk) {
					$disk 						= "$disk_ary[$k]";
				}
			}
			
			$k++;
        }

        if ($load < 60) {
            $loadtextclass 						= "text-info";
        }
        
        if ($load >= 60) {
            $loadtextclass 						= "text-warning";
        }
        
        if ($load >= 80) {
            $loadtextclass 						= "text-danger";
        }
        
        $diskusage 								= $disk;
        
        if ($diskusage < 60) {
            $disktextclass 						= "text-info";
        }
        
        if ($diskusage >= 60) {
            $disktextclass 						= "text-warning";
        }
        
        if ($diskusage >= 80) {
            $disktextclass 						= "text-danger";
        }
        
        $diskvalue01 							= (100 - $diskusage);
        //$diskvalue02 							= ($diskvalue01 + $diskusage);
        $radial 								= "<canvas classyloader='' data-percentage='.$diskusage.' data-speed='20' data-font-size='20px' data-diameter='30' data-line-color='#f35839' data-remaining-line-color='#edf2f6' data-line-width='40' width=''40'' height='40' class='js-is-in-view'></canvas>";
        $sessionAvatar 							= "<div class='media'><avatar username='$serverid' :size='36'></avatar></div>";
        
        $cluster 								.= '[';  
        $cluster 								.= '"'.$serverid.'",';   
        $cluster 								.= '"'.$serverip.'",';  
        $cluster 								.= '"<b class=\"'.$statustextclass.'\">'.$status.'</b>",';
        $cluster 								.= '"<b class=\"'.$loadtextclass.'\">'.$load.'%</b>",';    
        $cluster 								.= '"'.$channels.'",';
        $cluster 								.= '"<b class=\"'.$disktextclass.'\">'.$diskusage.'%</b>",';
        //$cluster 								.= '"<div data-label=\"'.$diskusage.'%\" class=\"radial-bar radial-bar-'.$diskusage.' radial-bar-xs\"></div>",';
        //$cluster 								.= '"'.$radial.'",';
        $cluster 								.= '"'.date('M. d, Y h:i A', strtotime($stime)).'"';
        $cluster 								.= '],';
    
    }

    $cluster 									= rtrim($cluster, ",");    
    $cluster 									.= ']';
    
    echo json_encode($cluster);     
    


?>
