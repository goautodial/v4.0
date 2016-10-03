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

$goCSVvalues = $_REQUEST['goCSVvalues'];

$defaultFields = '|lead_id|vendor_lead_code|source_id|list_id|gmt_offset_now|called_since_last_reset|phone_code|phone_number|title|first_name|middle_initial|last_name|address1|address2|address3|city|state|province|postal_code|country_code|gender|date_of_birth|alt_phone|email|security_phrase|comments|called_count|last_local_call_time|rank|owner|entry_list_id|';

$standard_SQL = array("defFields" => "vendor_lead_code, source_id, list_id, phone_code, phone_number, title, first_name, middle_initial, last_name, address1, address2, address3, city, state, province, postal_code, country_code, gender, date_of_birth, alt_phone, email, security_phrase, comments, rank, owner");

$yo = json_encode($standard_SQL);
$yo = json_decode($yo);


foreach ($goCSVvalues as $x => $line)
                {
                        foreach ($line as $n => $cell)
                        {
                                $cell_val[$x][$n] = $cell;
                        }
                }
//echo $cell_val[0][1];
//echo count($yo->defFields);
$splitVals = explode("|",$defaultFields);
$cntSplitVals = count($splitVals);
$o=0;
foreach ($splitVals as $vx ) {
echo $vx.'<br>';
$o++;
}

for($jj=0;$jj<$cntSplitVals;$jj++) {
//echo 

}
for ($i=0;$i<count($cell_val[0]);$i++)
                                {
                                        $columnHTML .= "<option value=\"$i\">".$cell_val[0][$i]."</option>\n";
                                }


/*echo "<pre>";
print_r($yo);
echo "</pre>";*/




/*                $url = gourl."/goDashboard/goAPI.php"; #URL to GoAutoDial API. (required)
                $postfields["goUser"] = goUser; #Username goes here. (required)
                $postfields["goPass"] = goPass;
                $postfields["goAction"] = "goEmergencyLogout"; #action performed by the [[API:Functions]]
                $postfields["responsetype"] = responsetype;
                $postfields["goUserAgent"] = $_POST['goUserAgent'];

                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_TIMEOUT, 100);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $postfields);
                $data = curl_exec($ch);
                curl_close($ch);

                $output = json_decode($data);

	        if ($output->result=="success") {
	           # Result was OK!
        	        echo "Agent ".$_POST['goUserAgent']." successfully logout.";
	         } else {
        	   # An error occured
                	echo $output->result;
        	}

*/


	
?>
