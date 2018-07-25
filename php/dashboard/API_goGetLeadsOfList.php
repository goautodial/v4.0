<?php

####################################################
#### Name: API_EmergencyLogout.php              ####
#### Type: API for emergency logout             ####
#### Version: 0.9                               ####
#### Copyright: GOAutoDial Inc. (c) 2011-2016   ####
#### Written by: Jerico James Milo              ####
#### License: AGPLv2                            ####
####################################################

require_once('../goCRMAPISettings.php');

				$url = gourl."/goUploadLeads/goAPI.php"; #URL to GoAutoDial API. (required)
                $postfields["goUser"] = goUser; #Username goes here. (required)
                $postfields["goPass"] = goPass;
                $postfields["goAction"] = "goGetLeadsOfList"; #action performed by the [[API:Functions]]
                $postfields["responsetype"] = responsetype;
                $postfields["goListId"] = $_REQUEST['goListId'];

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
				
				
		  if (count($output->data) < 1){
			  echo "";  
		  } else {
			
			foreach ($output->data as $key => $value) {
				
				//var_dump();  
				$PhoneNumbers = $value->phone_number;
				echo $PhoneNumbers.",";
			}
			
		  }
	        /*if ($output->result=="success") {
	           # Result was OK!
	           //var_dump($output);
        	        //echo "Agent ".$_POST['goUserAgent']." successfully logout.";
	         } else {
        	   # An error occured
                	echo $output->result;
        	}*/

	
?>
