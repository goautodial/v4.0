<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);	

require_once('CRMDefaults.php');
require_once('goCRMAPISettings.php');

// check required fields
$reason = "Unable to Modify List";
$validated = 1;
if (!isset($_POST["modifyid"])) {
	$validated = 0;
}

if ($validated == 1) {

	// collect new user data.	
	$modifyid = $_POST["modifyid"];
    
	$name = NULL; if (isset($_POST["name"])) { 
		$name = $_POST["name"]; 
		$name = stripslashes($name);
	}
	
	$desc = NULL; if (isset($_POST["desc"])) { 
		$desc = $_POST["desc"]; 
		$desc = stripslashes($desc);
	}
	
    $campaign = NULL; if (isset($_POST["campaign"])) { 
		$campaign = $_POST["campaign"]; 
		$campaign = stripslashes($campaign);
	}
	
    $status = NULL; if (isset($_POST["active"])) { 
		$status = $_POST["active"]; 
		$status = stripslashes($status);
	}
	$reset_list = NULL; if (isset($_POST["reset_list"])) { 
		$reset_list = $_POST["reset_list"]; 
		$reset_list = stripslashes($reset_list);
	}

	$reset_time = NULL; if (isset($_POST["reset_time"])) { 
		$reset_time = $_POST["reset_time"]; 
		$reset_time = stripslashes($reset_time);
	}

	$xferconf_a_number = NULL; if (isset($_POST["xferconf_a_number"])) { 
		$xferconf_a_number = $_POST["xferconf_a_number"]; 
		$xferconf_a_number = stripslashes($xferconf_a_number);
	}

	$xferconf_b_number = NULL; if (isset($_POST["xferconf_b_number"])) { 
		$xferconf_b_number = $_POST["xferconf_b_number"]; 
		$xferconf_b_number = stripslashes($xferconf_b_number);
	}

	$xferconf_c_number = NULL; if (isset($_POST["xferconf_c_number"])) { 
		$xferconf_c_number = $_POST["xferconf_c_number"]; 
		$xferconf_c_number = stripslashes($xferconf_c_number);
	}

	$xferconf_d_number = NULL; if (isset($_POST["xferconf_d_number"])) { 
		$xferconf_d_number = $_POST["xferconf_d_number"]; 
		$xferconf_d_number = stripslashes($xferconf_d_number);
	}

	$xferconf_e_number = NULL; if (isset($_POST["xferconf_e_number"])) { 
		$xferconf_e_number = $_POST["xferconf_e_number"]; 
		$xferconf_e_number = stripslashes($xferconf_e_number);
	}

	$agent_script_override = NULL; if (isset($_POST["agent_script_override"])) { 
		$agent_script_override = $_POST["agent_script_override"]; 
		$agent_script_override = stripslashes($agent_script_override);
	}

	$drop_inbound_group_override = NULL; if (isset($_POST["drop_inbound_group_override"])) { 
		$drop_inbound_group_override = $_POST["drop_inbound_group_override"]; 
		$drop_inbound_group_override = stripslashes($drop_inbound_group_override);
	}

	$campaign_cid_override = NULL; if (isset($_POST["campaign_cid_override"])) { 
		$campaign_cid_override = $_POST["campaign_cid_override"]; 
		$campaign_cid_override = stripslashes($campaign_cid_override);
	}

	$web_form = NULL; if (isset($_POST["web_form"])) { 
		$web_form = $_POST["web_form"]; 
		$web_form = stripslashes($web_form);
	}

	$url = gourl."/goLists/goAPI.php"; # URL to GoAutoDial API file
    $postfields["goUser"] = goUser; #Username goes here. (required)
    $postfields["goPass"] = goPass; #Password goes here. (required)
    $postfields["goAction"] = "goEditList"; #action performed by the [[API:Functions]]
    $postfields["responsetype"] = responsetype; #json (required)
    $postfields["list_id"] = $modifyid; #Desired list id. (required)
	$postfields["list_name"] = $name; #Desired value for user (required)
	$postfields["list_description"] = $desc; #Desired value for user (required)
	$postfields["campaign_id"] = $campaign; #Desired value for user (required)
	$postfields["active"] = $status; #Desired value for user (required)
	$postfields["reset_list"] = $reset_list;
	$postfields["reset_time"] = $reset_time;
	$postfields["xferconf_a_number"] = $xferconf_a_number;
	$postfields["xferconf_b_number"] = $xferconf_b_number;
	$postfields["xferconf_c_number"] = $xferconf_c_number;
	$postfields["xferconf_d_number"] = $xferconf_d_number;
	$postfields["xferconf_e_number"] = $xferconf_e_number;
	$postfields["agent_script_override"] = $agent_script_override;
	$postfields["drop_inbound_group_override"] = $drop_inbound_group_override;
	$postfields["campaign_cid_override"] = $campaign_cid_override;
	$postfields["web_form_address"] = $web_form;
    $postfields["hostname"] = $_SERVER['REMOTE_ADDR']; #Default value
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    //curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_TIMEOUT, 100);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $postfields);
    $data = curl_exec($ch);
    curl_close($ch);
    $output = json_decode($data);
    //print_r($output);die;
    if ($output->result=="success") {
    # Result was OK!
		echo "success";
    } else {
    # An error occured
		echo $output->result;
        //$lh->translateText("unable_modify_list");
    }
    
} else { print $reason; }
?>
