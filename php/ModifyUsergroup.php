<?php
/*ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);*/

require_once('CRMDefaults.php');
require_once('goCRMAPISettings.php');

// check required fields
$reason = "Unable to Modify User Group";

$validated = 1;
if (!isset($_POST["modifyid"])) {
	$validated = 0;
}

if ($validated == 1) {
    
	// collect new user data.	
	$modifyid = $_POST["modifyid"];
    
	$group_name = NULL; if (isset($_POST["group_name"])) { 
		$group_name = $_POST["group_name"]; 
		$group_name = stripslashes($group_name);
	}
	
    $group_level = NULL; if (isset($_POST["group_level"])) { 
		$group_level = $_POST["group_level"];
		$group_level = stripslashes($group_level);
	}

    $forced_timeclock_login = NULL; if (isset($_POST["forced_timeclock_login"])) { 
		$forced_timeclock_login = $_POST["forced_timeclock_login"]; 
		$forced_timeclock_login = stripslashes($forced_timeclock_login);
	}
	
    $shift_enforcement = NULL; if (isset($_POST["shift_enforcement"])) { 
		$shift_enforcement = $_POST["shift_enforcement"]; 
		$shift_enforcement = stripslashes($shift_enforcement);
	}
	
	$allowed_campaigns = " ";
	$allowed_camp = $_REQUEST['allowed_camp'];
	if (count($allowed_camp) > 0) {
		foreach ($allowed_camp as $camp) {
			$allowed_campaigns .= "{$camp} ";
		}
	}
	$allowed_campaigns .= "-";
	
	$group_permission  = '{';
	
	$group_permission .= '"dashboard":{';
	$group_permission .= '"dashboard_display":' . (isset($_POST["dashboard_display"]) ? '"Y"' : '"N"');
	$group_permission .= '},';
	
	$group_permission .= '"user":{';
	$group_permission .= '"user_create":' . (isset($_POST["user_create"]) ? '"C"' : '"N"') . ',';
	$group_permission .= '"user_read":' . (isset($_POST["user_read"]) ? '"R"' : '"N"') . ',';
	$group_permission .= '"user_update":' . (isset($_POST["user_update"]) ? '"U"' : '"N"') . ',';
	$group_permission .= '"user_delete":' . (isset($_POST["user_delete"]) ? '"D"' : '"N"');
	$group_permission .= '},';
	
	$group_permission .= '"campaign":{';
	$group_permission .= '"campaign_create":' . (isset($_POST["campaign_create"]) ? '"C"' : '"N"') . ',';
	$group_permission .= '"campaign_read":' . (isset($_POST["campaign_read"]) ? '"R"' : '"N"') . ',';
	$group_permission .= '"campaign_update":' . (isset($_POST["campaign_update"]) ? '"U"' : '"N"') . ',';
	$group_permission .= '"campaign_delete":' . (isset($_POST["campaign_delete"]) ? '"D"' : '"N"');
	$group_permission .= '},';
	
	$group_permission .= '"disposition":{';
	$group_permission .= '"disposition_create":' . (isset($_POST["disposition_create"]) ? '"C"' : '"N"') . ',';
	$group_permission .= '"disposition_update":' . (isset($_POST["disposition_update"]) ? '"U"' : '"N"') . ',';
	$group_permission .= '"disposition_delete":' . (isset($_POST["disposition_delete"]) ? '"D"' : '"N"');
	$group_permission .= '},';
	
	$group_permission .= '"pausecodes":{';
	$group_permission .= '"pausecodes_create":' . (isset($_POST["pausecodes_create"]) ? '"C"' : '"N"') . ',';
	$group_permission .= '"pausecodes_read":' . (isset($_POST["pausecodes_read"]) ? '"R"' : '"N"') . ',';
	$group_permission .= '"pausecodes_update":' . (isset($_POST["pausecodes_update"]) ? '"U"' : '"N"') . ',';
	$group_permission .= '"pausecodes_delete":' . (isset($_POST["pausecodes_delete"]) ? '"D"' : '"N"');
	$group_permission .= '},';
	
	$group_permission .= '"hotkeys":{';
	$group_permission .= '"hotkeys_create":' . (isset($_POST["hotkeys_create"]) ? '"C"' : '"N"') . ',';
	$group_permission .= '"hotkeys_read":' . (isset($_POST["hotkeys_read"]) ? '"R"' : '"N"') . ',';
	$group_permission .= '"hotkeys_delete":' . (isset($_POST["hotkeys_delete"]) ? '"D"' : '"N"');
	$group_permission .= '},';
	
	$group_permission .= '"list":{';
	$group_permission .= '"list_create":' . (isset($_POST["list_create"]) ? '"C"' : '"N"') . ',';
	$group_permission .= '"list_read":' . (isset($_POST["list_read"]) ? '"R"' : '"N"') . ',';
	$group_permission .= '"list_update":' . (isset($_POST["list_update"]) ? '"U"' : '"N"') . ',';
	$group_permission .= '"list_delete":' . (isset($_POST["list_delete"]) ? '"D"' : '"N"') . ',';
	$group_permission .= '"list_upload":' . (isset($_POST["list_upload"]) ? '"C"' : '"N"');
	$group_permission .= '},';
	
	$group_permission .= '"customfields":{';
	$group_permission .= '"customfields_create":' . (isset($_POST["customfields_create"]) ? '"C"' : '"N"') . ',';
	$group_permission .= '"customfields_read":' . (isset($_POST["customfields_read"]) ? '"R"' : '"N"') . ',';
	$group_permission .= '"customfields_update":' . (isset($_POST["customfields_update"]) ? '"U"' : '"N"') . ',';
	$group_permission .= '"customfields_delete":' . (isset($_POST["customfields_delete"]) ? '"D"' : '"N"');
	$group_permission .= '},';
	
	$group_permission .= '"script":{';
	$group_permission .= '"script_create":' . (isset($_POST["script_create"]) ? '"C"' : '"N"') . ',';
	$group_permission .= '"script_read":' . (isset($_POST["script_read"]) ? '"R"' : '"N"') . ',';
	$group_permission .= '"script_update":' . (isset($_POST["script_update"]) ? '"U"' : '"N"') . ',';
	$group_permission .= '"script_delete":' . (isset($_POST["script_delete"]) ? '"D"' : '"N"');
	$group_permission .= '},';
	
	$group_permission .= '"inbound":{';
	$group_permission .= '"inbound_create":' . (isset($_POST["inbound_create"]) ? '"C"' : '"N"') . ',';
	$group_permission .= '"inbound_read":' . (isset($_POST["inbound_read"]) ? '"R"' : '"N"') . ',';
	$group_permission .= '"inbound_update":' . (isset($_POST["inbound_update"]) ? '"U"' : '"N"') . ',';
	$group_permission .= '"inbound_delete":' . (isset($_POST["inbound_delete"]) ? '"D"' : '"N"');
	$group_permission .= '},';
	
	$group_permission .= '"ivr":{';
	$group_permission .= '"ivr_create":' . (isset($_POST["ivr_create"]) ? '"C"' : '"N"') . ',';
	$group_permission .= '"ivr_read":' . (isset($_POST["ivr_read"]) ? '"R"' : '"N"') . ',';
	$group_permission .= '"ivr_update":' . (isset($_POST["ivr_update"]) ? '"U"' : '"N"') . ',';
	$group_permission .= '"ivr_delete":' . (isset($_POST["ivr_delete"]) ? '"D"' : '"N"');
	$group_permission .= '},';
	
	$group_permission .= '"did":{';
	$group_permission .= '"did_create":' . (isset($_POST["did_create"]) ? '"C"' : '"N"') . ',';
	$group_permission .= '"did_read":' . (isset($_POST["did_read"]) ? '"R"' : '"N"') . ',';
	$group_permission .= '"did_update":' . (isset($_POST["did_update"]) ? '"U"' : '"N"') . ',';
	$group_permission .= '"did_delete":' . (isset($_POST["did_delete"]) ? '"D"' : '"N"');
	$group_permission .= '},';
	
	$group_permission .= '"voicefiles":{';
	$group_permission .= '"voicefiles_upload":' . (isset($_POST["voicefiles_upload"]) ? '"C"' : '"N"') . ',';
	$group_permission .= '"voicefiles_play":' . (isset($_POST["voicefiles_play"]) ? '"Y"' : '"N"') . ',';
	$group_permission .= '"voicefiles_download":' . (isset($_POST["voicefiles_download"]) ? '"Y"' : '"N"');
	$group_permission .= '},';
	
	$group_permission .= '"moh":{';
	$group_permission .= '"moh_create":' . (isset($_POST["moh_create"]) ? '"C"' : '"N"') . ',';
	$group_permission .= '"moh_read":' . (isset($_POST["moh_read"]) ? '"R"' : '"N"') . ',';
	$group_permission .= '"moh_update":' . (isset($_POST["moh_update"]) ? '"U"' : '"N"') . ',';
	$group_permission .= '"moh_delete":' . (isset($_POST["moh_delete"]) ? '"D"' : '"N"');
	$group_permission .= '},';
	
	$group_permission .= '"servers":{';
	$group_permission .= '"servers_create":' . (isset($_POST["servers_create"]) ? '"C"' : '"N"') . ',';
	$group_permission .= '"servers_read":' . (isset($_POST["servers_read"]) ? '"R"' : '"N"') . ',';
	$group_permission .= '"servers_update":' . (isset($_POST["servers_update"]) ? '"U"' : '"N"') . ',';
	$group_permission .= '"servers_delete":' . (isset($_POST["servers_delete"]) ? '"D"' : '"N"');
	$group_permission .= '},';
	
	$group_permission .= '"carriers":{';
	$group_permission .= '"carriers_create":' . (isset($_POST["carriers_create"]) ? '"C"' : '"N"') . ',';
	$group_permission .= '"carriers_read":' . (isset($_POST["carriers_read"]) ? '"R"' : '"N"') . ',';
	$group_permission .= '"carriers_update":' . (isset($_POST["carriers_update"]) ? '"U"' : '"N"') . ',';
	$group_permission .= '"carriers_delete":' . (isset($_POST["carriers_delete"]) ? '"D"' : '"N"');
	$group_permission .= '},';
	
	$group_permission .= '"reportsanalytics":{';
	$group_permission .= '"reportsanalytics_statistical_display":' . (isset($_POST["reportsanalytics_statistical_display"]) ? '"Y"' : '"N"') . ',';
	$group_permission .= '"reportsanalytics_agent_time_display":' . (isset($_POST["reportsanalytics_agent_time_display"]) ? '"Y"' : '"N"') . ',';
	$group_permission .= '"reportsanalytics_agent_performance_display":' . (isset($_POST["reportsanalytics_agent_performance_display"]) ? '"Y"' : '"N"') . ',';
	$group_permission .= '"reportsanalytics_dial_status_display":' . (isset($_POST["reportsanalytics_dial_status_display"]) ? '"Y"' : '"N"') . ',';
	$group_permission .= '"reportsanalytics_agent_sales_display":' . (isset($_POST["reportsanalytics_agent_sales_display"]) ? '"Y"' : '"N"') . ',';
	$group_permission .= '"reportsanalytics_sales_tracker_display":' . (isset($_POST["reportsanalytics_sales_tracker_display"]) ? '"Y"' : '"N"') . ',';
	$group_permission .= '"reportsanalytics_inbound_call_display":' . (isset($_POST["reportsanalytics_inbound_call_display"]) ? '"Y"' : '"N"') . ',';
	$group_permission .= '"reportsanalytics_export_call_display":' . (isset($_POST["reportsanalytics_export_call_display"]) ? '"Y"' : '"N"');
	$group_permission .= '},';
	
	$group_permission .= '"recordings":{';
	$group_permission .= '"recordings_display":' . (isset($_POST["recordings_display"]) ? '"Y"' : '"N"');
	$group_permission .= '},';
	
	$group_permission .= '"support":{';
	$group_permission .= '"support_display":' . (isset($_POST["support_display"]) ? '"Y"' : '"N"');
	$group_permission .= '},';
	
	$group_permission .= '"multi-tenant":{';
	$group_permission .= '"tenant_create":' . (isset($_POST["tenant_create"]) ? '"Y"' : '"N"') . ',';
	$group_permission .= '"tenant_display":' . (isset($_POST["tenant_display"]) ? '"Y"' : '"N"') . ',';
	$group_permission .= '"tenant_update":' . (isset($_POST["tenant_update"]) ? '"Y"' : '"N"') . ',';
	$group_permission .= '"tenant_delete":' . (isset($_POST["tenant_delete"]) ? '"Y"' : '"N"') . ',';
	$group_permission .= '"tenant_logs":' . (isset($_POST["tenant_logs"]) ? '"Y"' : '"N"') . ',';
	$group_permission .= '"tenant_calltimes":' . (isset($_POST["tenant_calltimes"]) ? '"Y"' : '"N"') . ',';
	$group_permission .= '"tenant_phones":' . (isset($_POST["tenant_phones"]) ? '"Y"' : '"N"') . ',';
	$group_permission .= '"tenant_voicemails":' . (isset($_POST["tenant_voicemails"]) ? '"Y"' : '"N"');
	$group_permission .= '}';
	
	$group_permission .= '}';
    
	$url = gourl."/goUserGroups/goAPI.php"; #URL to GoAutoDial API. (required)
    $postfields["goUser"] = goUser; #Username goes here. (required)
    $postfields["goPass"] = goPass; #Password goes here. (required)
    $postfields["goAction"] = "goEditUserGroup"; #action performed by the [[API:Functions]]
    $postfields["responsetype"] = responsetype; #json (required)
    $postfields["user_group"] = $modifyid; #Desired list id. (required)
	$postfields["group_name"] = $group_name; #Desired value for user (required)
	$postfields["group_level"] = $group_level; #Desired value for user (required)
	$postfields["forced_timeclock_login"] = $forced_timeclock_login; #Desired value for user (required)
	$postfields["shift_enforcement"] = $shift_enforcement; #Desired value for user (required)
	$postfields["allowed_campaigns"] = $allowed_campaigns;
	$postfields["permissions"] = $group_permission;
	
	$postfields["log_user"] = $_POST['log_user'];
	$postfields["log_group"] = $_POST['log_group'];
	$postfields["hostname"] = $_SERVER['REMOTE_ADDR'];
	
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_TIMEOUT, 100);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $postfields);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    $data = curl_exec($ch);
    curl_close($ch);
    $output = json_decode($data);

    if ($output->result=="success") {
    # Result was OK!
        echo 1;
    } else {
    # An error occured
		echo $output->result;
        //$lh->translateText("unable_modify_list");
    }
    
} else { 
	//ob_clean(); 
	print $reason; 
}
?>