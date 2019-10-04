<?php
 /**
 * @file 		GOagentJS.php
 * @brief 		Agent UI Script
 * @copyright 	Copyright (C) GOautodial Inc.
 * @author     	Chris Lomuntad  <chris@goautodial.com>
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

namespace creamy;

define('GO_AGENT_DIRECTORY', str_replace($_SERVER['DOCUMENT_ROOT'], "", dirname(__FILE__)));
define('GO_BASE_DIRECTORY', dirname(dirname(dirname(__FILE__))));
define('GO_LANG_DIRECTORY', dirname(__FILE__) . '/lang/');

$isAgentUI = $_SERVER['PHP_SELF'];
require_once(GO_BASE_DIRECTORY.'/php/APIHandler.php');
require_once(GO_BASE_DIRECTORY.'/php/CRMDefaults.php');
require_once(GO_BASE_DIRECTORY.'/php/UIHandler.php');
require_once(GO_BASE_DIRECTORY.'/php/LanguageHandler.php');
require_once(GO_BASE_DIRECTORY.'/php/DbHandler.php');
include(GO_BASE_DIRECTORY.'/php/Session.php');
require_once(GO_BASE_DIRECTORY.'/php/goCRMAPISettings.php');
$goAPI = (empty($_SERVER['HTTPS'])) ? str_replace('https:', 'http:', gourl) : str_replace('http:', 'https:', gourl);

$api = \creamy\APIHandler::getInstance();
$ui = \creamy\UIHandler::getInstance();
$lh = \creamy\LanguageHandler::getInstance();
$lh->addCustomTranslationsFromFile(GO_LANG_DIRECTORY . $lh->getLanguageHandlerLocale());

$US = '_';
$NOW_TIME = date("Y-m-d H:i:s");
$tsNOW_TIME = date("YmdHis");
$StarTtimE = date("U");
$FILE_TIME = date("Ymd-His");

$module_dir = (!empty($module_dir)) ? $module_dir : '/modules/GOagent/';

//ini_set('display_errors', 'on');
//error_reporting(E_ALL);

if (!isset($_REQUEST['action']) && !isset($_REQUEST['module_name'])) {
    //$result = get_user_info($_SESSION['user']);
    $result = $api->API_getLoginInfo($_SESSION['user']);
    $default_settings = $result->default_settings;
    $agent = $result->user_info;
    $phone = $result->phone_info;
    $system = $result->system_info;
    $country_codes = $result->country_codes;
    if (isset($result->camp_info)) {
        $camp_info = $result->camp_info;
    }
    
    $_SESSION['is_logged_in'] = $result->is_logged_in;
    
    header('Content-Type: text/javascript');

    echo "// Session Variables\n";
    $sess_vars = "|";
    foreach ($_SESSION as $idx => $val) {
        if (!preg_match("/^(userrole|avatar)/", $idx)) {
            if ($idx == 'is_logged_in')
                $val = ($val) ? 1 : 0;
            ${$idx} = $val;
            $sess_vars .= "{$idx}|";
        }
    }
    echo "// {$sess_vars}\n";
?>

// Settings
var phone;
var phoneRegistered = false;
var check_if_logged_out = 1;
var check_last_call = 0;
var isMobile = false; //initiate as false
var is_logged_in = <?=$is_logged_in?>;
var logging_in = false;
var logoutWarn = true;
var use_webrtc = <?=($use_webrtc ? $use_webrtc : 0)?>;
var NOW_TIME = '<?=$NOW_TIME?>';
var SQLdate = '<?=$NOW_TIME?>';
var filedate = '<?=$FILE_TIME?>';
var tinydate = '';
var StarTtimE = '<?=$StarTtimE?>';
var UnixTime = '<?=$StarTtimE?>';
var UnixTimeMS = 0;
var t = new Date();
var c = new Date();
var refresh_interval = 1000;
var SIPserver = '<?=$SIPserver?>';
var check_s;
var getFields = false;
var hangup_all_non_reserved= 1;	//set to 1 to force hangup all non-reserved channels upon Hangup Customer
var blind_transfer = 0;
var MDlogEPOCH = 0;
var recLIST = '';
var filename = '';
var last_filename = '';
var alertLogout = true;
var registrationFailed = false;
var minimizedDispo = false;
var check_login = false;
var window_focus = true;
var callback_alert = false;
var callback_alerts = {};
var reschedule_cb = false;
var reschedule_cb_id = 0;
var just_logged_in = false;
var editProfileEnabled = false;
var ECCS_BLIND_MODE = '<?=ECCS_BLIND_MODE?>';
var ECCS_DIAL_TIMEOUT = 2;
var has_inbound_call = 0;
var check_inbound_call = true;
var dialInterval;

<?php if( ECCS_BLIND_MODE === 'y' ) { ?>
var enable_eccs_shortcuts = 1;
<?php } ?>

<?php
foreach ($default_settings as $idx => $val) {
if (is_numeric($val) && !preg_match("/^(conf_exten|session_id)$/", $idx)) {
    if ($idx == 'xfer_group_count') {
	echo "var XFgroupCOUNT = {$val};\n";
    } else if ($idx == 'inbound_group_count') {
	echo "var INgroupCOUNT = {$val};\n";
    } else if ($idx == 'email_group_count') {
	echo "var EMAILgroupCOUNT = {$val};\n";
    } else if ($idx == 'phone_group_count') {
	echo "var PHONEgroupCOUNT = {$val};\n";
    } else if ($idx == 'alt_phone_dialing') {
	echo "var starting_alt_phone_dialing = {$val};\n";
    }
    echo "var {$idx} = {$val};\n";
} else if (is_array($val)) {
    if (preg_match("/^(xfer_groups|inbound_groups|xfer_group_names|inbound_group_handlers)$/", $idx)) {
	$valName = $idx;
	if ($idx == 'xfer_groups') {
	    $valName = 'VARxferGroups';
	} else if ($idx == 'xfer_group_names') {
	    $valName = 'VARxferGroupsNames';
	} else if ($idx == 'inbound_groups') {
	    $valName = 'VARingroups';
	} else if ($idx == 'inbound_group_handlers') {
	    $valName = 'VARingroup_handlers';
	} else if ($idx == 'email_groups') {
	    $valName = 'VARemailgroups';
	} else if ($idx == 'phone_groups') {
	    $valName = 'VARphonegroups';
	}
	echo "var {$valName} = new Array();\n";
    } else {
	echo "    {$idx} = new Array('','','','','','');\n";
    }
} else if (is_object($val)) {
    $valList  = "";
    $valList2 = "";
    $valName  = $idx;
    foreach ($val as $idz => $valz) {
	$valList  .= "'{$idz}',";
	$valList2 .= "'{$valz}',";
    }
    $valList  = preg_replace("/,$/", "", $valList);
    $valList2 = preg_replace("/,$/", "", $valList2);
    
    if ($idx == 'xfer_groups') {
	$valName = 'VARxferGroups';
    } else if ($idx == 'xfer_group_names') {
	$valName = 'VARxferGroupsNames';
    } else if ($idx == 'inbound_groups') {
	$valName = 'VARingroups';
    } else if ($idx == 'inbound_group_handlers') {
	$valName = 'VARingroup_handlers';
    } else if ($idx == 'email_groups') {
	$valName = 'VARemailgroups';
    } else if ($idx == 'phone_groups') {
	$valName = 'VARphonegroups';
    }
    
    echo "var {$valName} = new Array({$valList});\n";
    if ($idx == 'statuses') {
	echo "var statuses_names = new Array({$valList2});\n";
    } else if ($idx == 'pause_codes') {
	echo "var pause_codes_names = new Array({$valList2});\n";
    }
} else if (preg_match("/^(timezone)$/", $idx)) {
    ${$idx} = $val;
    echo "var {$idx} = '{$val}';\n";
} else {
    echo "var {$idx} = '{$val}';\n";
    if ($idx == 'callback_statuses_list') {
	echo "var VARCBstatusesLIST = '{$val}';\n";
    }
}
}
echo "\n";

echo "// User Settings\n";    
foreach ($agent as $idx => $val) {
if (preg_match("/^(vicidial_recording|vicidial_recording_override)$/", $idx)) {
    ${$idx} = $val;
    echo "var {$idx} = '{$val}';\n";
} else {
    if ($idx == 'user') {
	echo "var {$idx} = '{$val}';\n";
	echo "var uName = '{$val}';\n";
    } else if ($idx == 'pass') {
	echo "var {$idx} = '{$val}';\n";
	echo "var uPass = '{$val}';\n";
    } else if ($idx == 'phone_login') {
	${$idx} = $val;
	echo "var {$idx} = '{$val}';\n";
	echo "var pExten = '{$val}';\n";
	echo "var original_{$idx} = '{$val}';\n";
    } else if ($idx == 'phone_pass') {
	${$idx} = $val;
	echo "var {$idx} = '{$val}';\n";
	echo "var pPass = '{$val}';\n";
    } else if ($idx == 'full_name') {
	echo "var {$idx} = '{$val}';\n";
	echo "var fName = '{$val}';\n";
	echo "var LOGfullname = '{$val}';\n";
    } else if (preg_match("/^(custom_)/", $idx)) {
	echo "var user_{$idx} = '{$val}';\n";
    } else {
	echo "var {$idx} = '{$val}';\n";
    }
}
}
//echo "// ".$result['user_group']."\n";

$phone_login = (isset($_SESSION['phone_login'])) ? $_SESSION['phone_login'] : $phone_login;
$phone_pass = (isset($_SESSION['phone_pass'])) ? $_SESSION['phone_pass'] : $phone_pass;
echo "\n// Phone Settings\n";

foreach ($phone as $idx => $val) {
echo "var {$idx} = '{$val}';\n";
}

echo "\n// System Settings\n";

foreach ($system as $idx => $val) {
if (preg_match("/^(vdc_)/", $idx)) {
    $idx_ = str_replace('vdc_', '', $idx);
    echo "var {$idx_} = '{$val}';\n";
} else {
    if ($idx == 'allow_emails') {
	echo "var email_enabled = '{$val}';\n";
    } else if ($idx == 'qc_features_active') {
	echo "var qc_enabled = '{$val}';\n";
    } else if ($idx == 'default_local_gmt') {
	echo "var {$idx} = '{$val}';\n";
	${$idx} = $val;
    } else {
	echo "var {$idx} = '{$val}';\n";
    }
}
}

//$tz = ini_get('date.timezone');
$tz = $timezone;
if (strlen($tz) < 1) {
$tz = timezone_name_from_abbr(null, $default_local_gmt * 3600, -1);
if($tz === false) $tz = timezone_name_from_abbr(null, $default_local_gmt * 3600, 1);
}
date_default_timezone_set($tz);
?>

var currentTZ = '<?=$tz?>';
var currenttime = '<?=date("F d, Y H:i:s", time())?>' //PHP method of getting server date
var todayarray=new Array("<?=$lh->translationFor('sunday')?>","<?=$lh->translationFor('monday')?>","<?=$lh->translationFor('tuesday')?>","<?=$lh->translationFor('wednesday')?>","<?=$lh->translationFor('thursday')?>","<?=$lh->translationFor('friday')?>","<?=$lh->translationFor('saturday')?>");
var montharray=new Array("<?=$lh->translationFor('january')?>","<?=$lh->translationFor('february')?>","<?=$lh->translationFor('march')?>","<?=$lh->translationFor('april')?>","<?=$lh->translationFor('may')?>","<?=$lh->translationFor('june')?>","<?=$lh->translationFor('july')?>","<?=$lh->translationFor('august')?>","<?=$lh->translationFor('september')?>","<?=$lh->translationFor('october')?>","<?=$lh->translationFor('november')?>","<?=$lh->translationFor('december')?>");
var serverdate=new Date(currenttime);

<?php  
if (isset($camp_info->campaign_id)) {
echo "\n// Campaign Settings\n";
$dial_prefix = '';
?>
var campaign = '<?=$camp_info->campaign_id?>';      // put here the selected campaign upon login
var group = '<?=$camp_info->campaign_id?>';         // same value as campaign variable
<?php
foreach ($camp_info as $idx => $val) {
    if (preg_match("/^(timer_action)/", $idx)) {
	echo "var campaign_{$idx} = '{$val}';\n";
    } else {
	if ($idx == 'dial_prefix')
	    {$dial_prefix = $val;}
	if ($idx == 'manual_dial_prefix')
	    {$val = (strlen($val) < 1) ? $dial_prefix : $val;}
	if ($idx == 'pause_after_each_call') {
	    $idx = 'dispo_check_all_pause';
	    $val = ($val == 'Y') ? 1 : 0;
	}
	if (preg_match("/^(campaign_rec_filename|default_group_alias|default_xfer_group)$/", $idx)) {
	    echo "var LIVE_{$idx} = '{$val}';\n";
	}

	if (!preg_match("/^(disable_dispo_screen|disable_dispo_status|campaign_recording)$/", $idx)) {
	    if (preg_match("/^(web_form_address)/", $idx)) {
		echo "var {$idx} = '{$val}';\n";
		echo "var VDIC_{$idx} = '{$val}';\n";
		echo "var TEMP_VDIC_{$idx} = '{$val}';\n";
	    } else {
		if (is_object($val)) {
		    if (preg_match("/^hotkeys/", $idx)) {
			$hkList = "";
			foreach ($val as $k => $v) {
			    $hkList .= "'{$k}': '{$v}', ";
			}
			$hkList  = preg_replace("/, $/", "", $hkList);
			echo "var {$idx} = {".$hkList."};\n";
		    }
		} else if (is_numeric($val) && $idx == 'call_requeue_button') {
		    echo "var {$idx} = $val;\n";
		} else if (is_numeric($val) && preg_match("/^(enable_callback_alert|cb_noexpire|cb_sendemail)$/", $idx)) {
		    echo "var {$idx} = $val;\n";
		} else {
		    echo "var {$idx} = '{$val}';\n";
		    if ($idx == 'auto_dial_level') {
			echo "var starting_dial_level = '{$val}';\n";
		    }
		    if ($idx == 'api_manual_dial') {
			$AllowManualQueueCalls = 1;
			$AllowManualQueueCallsChoice = 0;
			if ($val == 'QUEUE') {
			    $AllowManualQueueCalls = 0;
			    $AllowManualQueueCallsChoice = 1;
			}
			echo "var AllowManualQueueCalls = '{$AllowManualQueueCalls}';\n";
			echo "var AllowManualQueueCallsChoice = '{$AllowManualQueueCallsChoice}';\n";
		    }
		    if ($idx == 'manual_preview_dial') {
			$manual_dial_preview = 1;
			if ($val == 'DISABLED')
			    {$manual_dial_preview = 0;}
			echo "var manual_dial_preview = '{$manual_dial_preview}';\n";
		    }
		    if ($idx == 'manual_dial_override') {
			if ($val == 'ALLOW_ALL')
			    {echo "    agentcall_manual = '1';\n";}
			if ($val == 'DISABLE_ALL')
			    {echo "    agentcall_manual = '0';\n";}
		    }
		    if ($idx == 'agent_clipboard_copy') {
			echo "var Copy_to_Clipboard = '{$val}';\n";
		    }
		    if (preg_match("/^(xferconf_)/", $idx)) {
			echo "var ".preg_replace(array('/xferconf/', '/number/', '/dtmf/'), array('Call_XC', 'Number', 'DTMF'), $idx)." = '{$val}';\n";
		    }
		    if ($idx == 'view_calls_in_queue_launch') {
			echo "var view_calls_in_queue_active = '{$val}';\n";
		    }
		}
	    }
	} else {
	    ${$idx} = $val;
	}
    }
}

if (($disable_dispo_screen == 'DISPO_ENABLED') || ($disable_dispo_screen == 'DISPO_SELECT_DISABLED') || (strlen($disable_dispo_status) < 1)) {
    if ($disable_dispo_screen == 'DISPO_SELECT_DISABLED') {
	echo "var hide_dispo_list = '1';\n";
    } else {
	echo "var hide_dispo_list = '0';\n";
    }
    echo "var disable_dispo_screen = '0';\n";
    echo "var disable_dispo_status = '';\n";
}
if (($disable_dispo_screen == 'DISPO_DISABLED') && (strlen($disable_dispo_status) > 0)) {
    echo "var hide_dispo_list = '0';\n";
    echo "var disable_dispo_screen = '1';\n";
    echo "var disable_dispo_status = '{$disable_dispo_status}';\n";
}

if ((!preg_match('/DISABLED/', $vicidial_recording_override)) && ($vicidial_recording > 0))
    {$campaign_recording = $vicidial_recording_override;}
if ($vicidial_recording == '0')
    {$campaign_recording = 'NEVER';}
echo "var campaign_recording = '{$campaign_recording}';\n";
echo "var LIVE_campaign_recording = '{$campaign_recording}';\n";
}

$country_code_list = '{}';
if (isset($country_codes)) {
$country_code_list = stripslashes(json_encode($country_codes));
}
echo "var country_codes = $country_code_list;\n";
?>
var defaultFields = "vendor_lead_code,source_id,list_id,gmt_offset_now,called_since_last_reset,phone_code,phone_number,title,first_name,middle_initial,last_name,address1,address2,address3,city,state,province,postal_code,country_code,gender,date_of_birth,alt_phone,email,security_phrase,comments,called_count,last_local_call_time,rank,owner";

$(document).ready(function() {
// Load current server time
setInterval("displaytime()", 1000);
checkLogin = 0;

$(window).focus(function() {
window_focus = true;
}).blur(function() {
window_focus = false;
}).trigger('focus');

$(window).load(function() {
var refreshId = setInterval(function() {
    if (is_logged_in && ((use_webrtc && phoneRegistered) || !use_webrtc)) {
	//Start of checking for live calls
	//if (live_customer_call == 1) {
	//    live_call_seconds++;
	//    //$("input[name='SecondS']").val(live_call_seconds);
	//    //$("div:contains('CALL LENGTH:') > span").html(live_call_seconds);
	//    //$("div:contains('SESSION ID:') > span").html(session_id);
	//    toggleButton('DialHangup', 'hangup');
	//    toggleButton('ResumePause', 'off');
	//    
	//    if (CheckDEADcall > 0) {
	//        if (CheckDEADcallON < 1) {
	//            toggleStatus('DEAD');
	//            toggleButton('ParkCall', 'off');
	//            toggleButton('TransferCall', 'off');
	//            CheckDEADcallON = 1;
	//            
	//            if (xfer_in_call > 0 && customer_3way_hangup_logging == 'ENABLED') {
	//                customer_3way_hangup_counter_trigger = 1;
	//                customer_3way_hangup_counter = 1;
	//            }
	//        }
	//    }
	//}
	
	if (live_customer_call < 1) {
	    editProfileEnabled = false;
	    $("#for_dtmf").addClass('hidden');
	    $('#edit-profile').addClass('hidden');
	    $("#reload-script").addClass('hidden');
	    $("#dialer-pad-ast, #dialer-pad-hash").addClass('hidden');
	    $("#dialer-pad-clear, #dialer-pad-undo").removeClass('hidden');
	    $("#btnLogMeOut").removeClass("disabled");
	    //toggleStatus('NOLIVE');
	    
	    if (dialingINprogress < 1) {
		//toggleButton('DialHangup', 'dial');
		//toggleButton('ResumePause', 'on');
	    }
	    
	    if (per_call_notes == 'ENABLED') {
		$("#call_notes_content").removeClass('hidden');
	    } else {
		$("#call_notes_content").addClass('hidden');
	    }
	}
	//End of checking for live calls

	$("#LeadLookUP").prop('checked', true);
	    
	if (check_r < 1) {
	    toggleButtons(dial_method, ivr_park_call, call_requeue_button);
	    if (agent_status_view > 0) {
		$("#agents-tab").removeClass('hidden');
	    } else {
		$("#agents-tab").addClass('hidden');
	    }
	}

	epoch_sec++;
	check_r++;
	even++;
	if (even > 1) {
	    even = 0;
	}
	
	WaitingForNextStep = 0;
	if ( (CloserSelecting == 1) || (TerritorySelecting == 1) )	{WaitingForNextStep = 1;}
    
    if (disable_alter_custphone != 'HIDE') {
        $("#phone_number").removeClass('hidden');
        $("#phone_number_DISP").addClass('hidden');
    } else {
        $("#phone_number").addClass('hidden');
        $("#phone_number_DISP").removeClass('hidden');
    }
	
	if (open_dispo_screen == 1) {
	    wrapup_counter = 0;
	    if (wrapup_seconds > 0) {
		//showDiv('WrapupBox');
		//$("#WrapupTimer").html(wrapup_seconds);
		wrapup_waiting = 1;
	    }

	    CustomerData_update();
	    //if (hide_gender < 1)
	    //{
	    //    $("#GENDERhideFORie").html('');
	    //    $("#GENDERhideFORieALT").html('<select size="1" name="gender_list" class="cust_form" id="gender_list"><option value="U">U - <?=$lh->translationFor('undefined')?></option><option value="M">M - <?=$lh->translationFor('male')?></option><option value="F">F - <?=$lh->translationFor('female')?></option></select>');
	    //}

	    DispoSelectBox();
	    //DispoSelectContent_create('','ReSET');
	    WaitingForNextStep = 1;
	    open_dispo_screen = 0;
	    LIVE_default_xfer_group = default_xfer_group;
	    LIVE_campaign_recording = campaign_recording;
	    LIVE_campaign_rec_filename = campaign_rec_filename;
	    if (disable_alter_custphone != 'HIDE') {
            $("#DispoSelectPhone").html(dialed_number);
        } else {
            $("#DispoSelectPhone").html('');
        }
	    if (auto_dial_level == 0) {
		if ($("#DialALTPhone").is(':checked') == true) {
		    reselect_alt_dial = 1;
		    toggleButton('DialHangup', 'dial');

		    $("#MainStatusSpan").html("<b><?=$lh->translationFor('dial_next_call')?></b>");
		} else {
		    reselect_alt_dial = 0;
		}
	    }

	    // Submit custom form if it is custom_fields_enabled
	    if (custom_fields_enabled > 0) {
		//alert("IFRAME submitting!");
		//vcFormIFrame.document.form_custom_fields.submit();
	    }
	}
	
	if (AgentDispoing > 0) {
	    WaitingForNextStep = 1;
	    CheckForConfCalls(session_id, '0');
	    AgentDispoing++;
	}
	
	if (agent_choose_ingroups_skip_count > 0) {
	    agent_choose_ingroups_skip_count--;
	    if (agent_choose_ingroups_skip_count == 0)
		{CloserSelectSubmit();}
	}
	if (agent_select_territories_skip_count > 0) {
	    agent_select_territories_skip_count--;
	    if (agent_select_territories_skip_count == 0)
		{TerritorySelectSubmit();}
	}
	if (logout_stop_timeouts == 1)	{WaitingForNextStep = 1;}
	if ( (custchannellive < -30) && (lastcustchannel.length > 3) && (no_empty_session_warnings < 1) ) {CustomerChannelGone();}
	if ( (custchannellive < -10) && (lastcustchannel.length > 3) ) {ReCheckCustomerChan();}
	if ( (nochannelinsession > 16) && (check_r > 15) && (no_empty_session_warnings < 1) ) {NoneInSession();}
	if (external_transferconf_count > 0) {external_transferconf_count = (external_transferconf_count - 1);}
	if (manual_auto_hotkey == 1) {
	    manual_auto_hotkey = 0;
	    ManualDialNext('','','','','','0');
	}
	if (manual_auto_hotkey > 1) {manual_auto_hotkey = (manual_auto_hotkey - 1);}
	
	if (agent_lead_search_override == 'NOT_ACTIVE') {
	    if (agent_lead_search == 'ENABLED') {
		$("#agent-lead-search").removeClass('hidden');
	    } else {
		$("#agent-lead-search").addClass('hidden');
	    }
	}

	if (WaitingForNextStep == 0) {
	    if (trigger_ready > 0) {
		trigger_ready = 0;
		if (auto_resume_precall == 'Y')
		    {AutoDial_Resume_Pause("VDADready");}
	    }
	    
	    // check for live channels in conference room and get current datetime
	    CheckForConfCalls(session_id, '0');
	    
	    // refresh agent status view
	    if ($("#agents-tab").hasClass('active')) {
		agent_status_view_active = 1;
	    } else {
		agent_status_view_active = 0;
	    }
	    if (agent_status_view_active > 0) {
		RefreshAgentsView('AgentViewStatus', agent_status_view);
	    }
	    if (view_calls_in_queue_active > 0) {
		RefreshCallsInQueue(view_calls_in_queue);
	    }
	    if (xfer_select_agents_active > 0) {
		RefreshAgentsView('AgentXferViewSelect', agent_status_view);
	    }
	    if (agentonly_callbacks == '1')
		{CB_count_check++;}

	    if (AutoDialWaiting == 1) {
		CheckForIncoming();
	    }

	    if (MD_channel_look == 1) {
		ManualDialCheckChannel(XDcheck);
	    }
	    
	    if ( (CB_count_check > 19) && (agentonly_callbacks == '1') ) {
		CallBacksCountCheck();
		CB_count_check = 0;
	    }
	    if ( (even > 0) && (agent_display_dialable_leads > 0) ) {
		//DiaLableLeaDsCounT();
	    }
	    if (live_customer_call == 1) {
		live_call_seconds++;
		$(".formMain input[name='seconds']").val(live_call_seconds);
		$("#SecondsDISP").html(live_call_seconds);
		$("#for_dtmf").removeClass('hidden');
		if (!editProfileEnabled)
		    $('#edit-profile').removeClass('hidden');
		$("#reload-script").removeClass('hidden');
		$("#dialer-pad-ast, #dialer-pad-hash").removeClass('hidden');
		$("#dialer-pad-clear, #dialer-pad-undo").addClass('hidden');
		$("#btnLogMeOut").addClass("disabled");
	    }
	    if (XD_live_customer_call == 1) {
		XD_live_call_seconds++;
		$("#xferlength").val(XD_live_call_seconds);
		if (!editProfileEnabled)
		    $('#edit-profile').removeClass('hidden');
		$("#reload-script").removeClass('hidden');
		$("#btnLogMeOut").addClass("disabled");
	    }
	    if (customerparked == 1) {
		customerparkedcounter++;
		var parked_mm = Math.floor(customerparkedcounter/60);  // The minutes
		var parked_ss = customerparkedcounter % 60;            // The balance of seconds
		if (parked_ss < 10)
		    {parked_ss = "0" + parked_ss;}
		var parked_mmss = parked_mm + ":" + parked_ss;
		$("#ParkCounterSpan").html("<?=$lh->translationFor('time_on_park')?>: " + parked_mmss);
				}
	    if (customer_3way_hangup_counter_trigger > 0) {
		if (customer_3way_hangup_counter > customer_3way_hangup_seconds) {
		    var customer_3way_timer_seconds = (XD_live_call_seconds - customer_3way_hangup_counter);
		    //customer_3way_hangup_process('DURING_CALL',customer_3way_timer_seconds);

		    customer_3way_hangup_counter = 0;
		    customer_3way_hangup_counter_trigger = 0;

		    if (customer_3way_hangup_action == 'DISPO') {
			customer_3way_hangup_dispo_message = '<?=$lh->translationFor('customer_hangup_3way')?>';
			BothCallHangup();
		    }
					} else {
		    customer_3way_hangup_counter++;
		    //document.getElementById("debugbottomspan").innerHTML = "<?=$lang['customer_3way_hangup']?> " + customer_3way_hangup_counter;
					}
				}
	    if ( (update_fields > 0) && (update_fields_data.length > 2) ) {
		UpdateFieldsData();
				}
	    if ( (timer_action != 'NONE') && (timer_action.length > 3) && (timer_action_seconds <= live_call_seconds) && (timer_action_seconds >= 0) ) {
		TimerActionRun('', '');
	    }
	    if (HKdispo_display > 0) {
		if ( (HKdispo_display == 3) && (HKfinish == 1) ) {
		    HKfinish = 0;
		    DispoSelectSubmit();
		    //AutoDialWaiting = 1;
		    //AutoDial_Resume_Pause("VDADready");
		}
		if (HKdispo_display == 1) {
		    //if (hot_keys_active == 1)
		    //	{showDiv('HotKeyEntriesBox');}
		    //hideDiv('HotKeyActionBox');
		}
		HKdispo_display--;
	    }
	    if (all_record == 'YES') {
		if (all_record_count < allcalls_delay)
		    {all_record_count++;}
		else {
		    ConfSendRecording('MonitorConf', session_id , '');
		    all_record = 'NO';
		    all_record_count = 0;
		}
	    }


	    if (active_display == 1) {
		check_s = check_r.toString();
		if ( (check_s.match(/00$/)) || (check_r < 2) )  {
		    CheckForConfCalls(session_id, '0');
		}
	    }
	    if (check_r < 2) {
		// nothing to see here... move along...
	    } else {
		//check_for_live_calls();
		check_s = check_r.toString();
	    }
	    if ( (blind_monitoring_now > 0) && ( (blind_monitor_warning == 'ALERT') || (blind_monitor_warning == 'NOTICE') ||  (blind_monitor_warning == 'AUDIO') || (blind_monitor_warning == 'ALERT_NOTICE') || (blind_monitor_warning == 'ALERT_AUDIO') || (blind_monitor_warning == 'NOTICE_AUDIO') || (blind_monitor_warning == 'ALL') ) ) {
		if ( (blind_monitor_warning == 'NOTICE') || (blind_monitor_warning == 'ALERT_NOTICE') || (blind_monitor_warning == 'NOTICE_AUDIO') || (blind_monitor_warning == 'ALL') ) {
		    //document.getElementById("blind_monitor_notice_span_contents").innerHTML = blind_monitor_message + "<br />";
		    //showDiv('blind_monitor_notice_span');
		}
		if (blind_monitoring_now_trigger > 0) {
		    if ( (blind_monitor_warning == 'ALERT') || (blind_monitor_warning == 'ALERT_NOTICE') || (blind_monitor_warning == 'ALERT_AUDIO') || (blind_monitor_warning == 'ALL') ) {
			//document.getElementById("blind_monitor_alert_span_contents").innerHTML = blind_monitor_message;
			//showDiv('blind_monitor_alert_span');
		    }
		    if ( (blind_monitor_filename.length > 0) && ( (blind_monitor_warning == 'AUDIO') || (blind_monitor_warning == 'ALERT_AUDIO')|| (blind_monitor_warning == 'NOTICE_AUDIO') || (blind_monitor_warning == 'ALL') ) ) {
			BasicOriginateCall(blind_monitor_filename, 'NO', 'YES', session_id, 'YES', '', '1', '0', '1');
		    }
		    blind_monitoring_now_trigger = 0;
		}
	    } else {
		//hideDiv('blind_monitor_notice_span');
		//document.getElementById("blind_monitor_notice_span_contents").innerHTML = '';
		//hideDiv('blind_monitor_alert_span');
	    }
	    if (wrapup_seconds > 0) {
		//document.getElementById("WrapupTimer").innerHTML = (wrapup_seconds - wrapup_counter);
		wrapup_counter++;
		if ( (wrapup_counter > wrapup_seconds) && ($("#WrapupBox").is(':visible')) ) {
		    wrapup_waiting = 0;
		    //hideDiv('WrapupBox');
		    if ($("#DispoSelectStop").is(':checked')) {
			if (auto_dial_level != '0') {
			    AutoDialWaiting = 0;
			    //alert('wrapup pause');
			    AutoDial_Resume_Pause("VDADpause");
			    //document.getElementById("DiaLControl").innerHTML = DiaLControl_auto_HTML;
			}
			pause_calling = 1;
			if (dispo_check_all_pause != '1') {
			    DispoSelectStop = false;
			    $("#DispoSelectStop").prop('checked', false);
			    //alert("unchecking PAUSE");
			}
		    } else {
			if (auto_dial_level != '0') {
			    AutoDialWaiting = 1;
			    //alert('wrapup ready');
			    AutoDial_Resume_Pause("VDADready", "NEW_ID", "WRAPUP");
			    //document.getElementById("DiaLControl").innerHTML = DiaLControl_auto_HTML_ready;
			}
		    }
		}
	    }
	    if (consult_custom_wait > 0) {
		//if (consult_custom_wait == '1')
		//    {vcFormIFrame.document.form_custom_fields.submit();}
		if (consult_custom_wait >= consult_custom_delay)
		    {SendManualDial('YES');}
		else
		    {consult_custom_wait++;}
	    }
	}
	
	//Check for Callbacks
	if (enable_callback_alert) {
	    checkForCallbacks();
	}
	
	//Check if Agent is still logged in
	checkIfStillLoggedIn(check_if_logged_out, check_last_call);
    } else {
	updateButtons();
	
	if (DefaultALTDial == 1) {
	    $("#DialALTPhone").prop('checked', true);
	}
	
	$("#LeadLookUP").prop('checked', true);
	
	if ( (agent_pause_codes_active == 'Y') || (agent_pause_codes_active == 'FORCE') ) {
	    $("#pause_code_link").removeClass('hidden');
	}
	if (allow_closers != 'Y') {
	    toggleButton('LocalCloser', 'hide');
			}
	
	if (agent_lead_search_override !== 'DISABLED') {
	    $("#agent-lead-search").removeClass('hidden');
	} else {
	    $("#agent-lead-search").addClass('hidden');
	}
	
	$("#sessionIDspan").html(session_id);
	if ( (LIVE_campaign_recording == 'NEVER') || (LIVE_campaign_recording == 'ALLFORCE') ) {
	    //$("#RecordControl").html("<img src=\"./images/vdc_LB_startrecording_OFF.gif\" border=\"0\" alt=\"<?=$lh->translationFor('start_recording')?>\" />");
	    $("#RecordControl").addClass('hidden');
			} else if ( LIVE_campaign_recording == 'ONDEMAND' ) {
	    $("#RecordControl").removeClass('hidden');
	}
	
	if (per_call_notes == 'ENABLED') {
	    $("#call_notes_content").removeClass('hidden');
	} else {
	    $("#call_notes_content").addClass('hidden');
	}
	
	if (INgroupCOUNT > 0) {
	    //if (closer_default_blended == 1)
	    //    {$("#closerSelectBlended").prop('checked', true);}
	    //CloserSelectContent_create();
	    //showDiv('CloserSelectBox');
	    //CloserSelecting = 1;
	    //CloserSelectContent_create();
	    if (agent_choose_ingroups_DV == "MGRLOCK")
		{agent_choose_ingroups_skip_count = 4;}
	} else {
	    //hideDiv('CloserSelectBox');
	    //MainPanelToFront();
	    //CloserSelecting = 0;
	    if (dial_method == "INBOUND_MAN") {
		dial_method = "MANUAL";
		auto_dial_level = 0;
		starting_dial_level = 0;
		toggleButton('DialHangup', 'dial');
	    }
	}
	
	if (agentonly_callbacks == '1')
	    {CB_count_check++;}
	
	if ( (CB_count_check > 19) && (agentonly_callbacks == '1') ) {
	    CallBacksCountCheck();
	    CB_count_check = 0;
	}
	
	//Check if Agent is still logged in
	checkLogin++;
	if (checkLogin > 2) {
	    checkLogin = 0;
	    checkIfStillLoggedIn(false);
	}
    }
    
    //console.log(window_focus);
}, refresh_interval);

if (is_logged_in && ((use_webrtc && phoneRegistered) || !use_webrtc)) {
    $("aside.control-sidebar").addClass('control-sidebar-open');
}

$("aside.control-sidebar").css({
    'overflow': 'hidden',
    'min-height': $("body").innerHeight()
});

var origHeight = $("body").innerHeight();
$(window).resize(function() {
    $("aside.control-sidebar").css('height', '100%');
    
    if (parseInt($("body").innerWidth()) < 768) {
	isMobile = true;
	paddingHB = 50;
    } else {
	isMobile = false;
	paddingHB = 100;
    }
    
    var navConBar = $("header.main-header").innerHeight();
    var minusThis = (parseInt(navConBar) + parseInt(paddingHB));
    var newHeight = parseInt($("body").innerHeight()) - minusThis;
    $("aside.control-sidebar div.tab-content").css({
	'height': newHeight
    });
    
    var newPos = 0;
    if (origHeight > $("body").innerHeight()) {
	newPos = origHeight - parseInt($("body").innerHeight());
    }
    $("#go_agent_logout").css('bottom', newPos);
});

var d = new Date();
//var currDate = new Date(d.getFullYear(), d.getMonth(), d.getDate(), d.getHours(), d.getMinutes() + 15);
var currDate = new Date(serverdate.getFullYear(), serverdate.getMonth(), serverdate.getDate(), serverdate.getHours(), serverdate.getMinutes() + 15);
$("#cb-datepicker").datetimepicker({
    inline: true,
    sideBySide: true,
    icons: {
	time: 'fa fa-clock-o',
	date: 'fa fa-calendar'
    },
    minDate: currDate
});

var selectedDate = moment(currDate).format('YYYY-MM-DD HH:mm:00');
$("#date-selected").html(moment(currDate).format('dddd, MMMM Do YYYY, h:mm a'));
$("#callback-date").val(selectedDate);
$("#cb-datepicker").on("dp.change", function (e) {
    selectedDate = moment(e.date).format('YYYY-MM-DD HH:mm:00');
    $("#date-selected").html(moment(e.date).format('dddd, MMMM Do YYYY, h:mm a'));
    $("#callback-date").val(selectedDate);
});
<?php if(ECCS_BLIND_MODE === 'y') { ?>

$('#callback-datepicker').on('shown.bs.modal', function(){

	enable_eccs_shortcuts = 0;
	$('#CallBackOnlyMe').prop('checked', true);

	var eccs_currYear = moment(currDate).format('YYYY');
        var eccs_currMonth = moment(currDate).format('MM');
        var eccs_currDay = moment(currDate).format('DD');

        $("#eccs_year").val(eccs_currYear);
        $("#eccs_year").attr("min", eccs_currYear);

        $("#eccs_month option[value='"+eccs_currMonth+"']").attr("selected", "selected");

        $("#eccs_day").val(eccs_currDay);
        $("#eccs_day").attr("min", "1");
        $("#eccs_day").attr("max", "31");

         $("#eccs_time").datetimepicker({
            format: "hh:mm a",
            icons: {
                time: 'fa fa-clock-o',
                date: 'fa fa-calendar'
            },
            defaultDate: currDate
         });

         $("#eccs_time").on("dp.change", function (e) {
	    selectedTime = moment(e.date).format('HH:mm:00');
            selectedYear = $("#eccs_year").val();
            selectedMonth = $("#eccs_month").val();
            selectedDay = $("#eccs_day").val();
		
	    var monthContainer = new Array("", "January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December");

            selectedDate = selectedYear + "-" + parseInt(selectedMonth) + "-" + selectedDay + " " + selectedTime;
            $("#date-selected").html(selectedYear + "-" + monthContainer[parseInt(selectedMonth)] + "-" + selectedDay + " " + moment(e.date).format('h:mm a'));
            $("#callback-date").val(selectedDate);
         });

         $("#eccs_year").on("change", function () {
            eccsChangeSelectedDate();
	 });

         $("#eccs_month").on("change", function () {
            eccsChangeSelectedDate();
         });

         $("#eccs_day").on("change", function () {
            eccsChangeSelectedDate();
         });
      });
	

	function eccsChangeSelectedDate(){
	    eccstimepicker = $("#eccs_time").data('DateTimePicker').date();
	    selectedTime = moment(eccstimepicker).format('HH:mm:00');
            selectedYear = $("#eccs_year").val();
            selectedMonth = $("#eccs_month").val();
            selectedDay = $("#eccs_day").val();
	
	    monthContainer = new Array("", "January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December");

            selectedDate = selectedYear + "-" + parseInt(selectedMonth) + "-" + selectedDay + " " + selectedTime;
            $("#date-selected").html(selectedYear + "-" + monthContainer[parseInt(selectedMonth)] + "-" + selectedDay + " " + moment(eccstimepicker).format('h:mm a'));
            $("#callback-date").val(selectedDate);
	}

<?php } ?>
        
        $("#show-cb-calendar").click(function() {
            $("#cb-container").slideToggle('slow');
        });
        
        if (!$("aside.control-sidebar").hasClass("control-sidebar-open")) {
            if (!is_logged_in && ((use_webrtc && !phoneRegistered) || !use_webrtc)) {
                checkSidebarIfOpen(true);
                //$.AdminLTE.controlSidebar.open($("aside.control-sidebar"), true);
            }
        }
    });

    var logoutRegX = new RegExp("logout\.php", "ig");
    $("#cream-agent-logout").click(function(event) {
        var hRef = $(this).attr('href');
        var loggedOut = 0;
        if (hRef.match(logoutRegX) && !minimizedDispo) {
            event.preventDefault();
            refresh_interval = 730000;
            swal({
                title: "<?=$lh->translationFor('sure_to_logout')?>",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "<?=$lh->translationFor('log_me_out')?>",
                closeOnConfirm: false
            }, function(sureToLogout){
                if (sureToLogout) {
                    swal.close();
                    if (is_logged_in && ((use_webrtc && phoneRegistered) || !use_webrtc)) {
                        logoutWarn = false;
                        btnLogMeOut();
                        loggedOut++;
                    }
                    if (use_webrtc && phoneRegistered) {
                        if (phone.isConnected()) {
                            phone.stop();
                            loggedOut++;
                        }
                    }
                    if (loggedOut > 0 || (loggedOut < 1 && !is_logged_in)) {
                        console.log('<?=$lh->translationFor('logging_out_phones')?>...');
                        $("div.preloader center").append('<br><br><span style="font-size: 24px; color: white;"><?=$lh->translationFor('logging_out_phones')?>...</span>');
                        $("div.preloader").fadeIn("slow");
                        setTimeout(
                            function() {
                                window.location.href = hRef;
                            },
                            2500
                        );
                    }
                } else {
                    refresh_interval = 1000;
                }
            });
        }
    });
   
   /*$(document).bind('keydown', '16', function(){
	 $("#cream-agent-logout").click();
   });*/
    <?php
    $GOmodule = false;
    if ($GOmodule) {
    ?>
    var $navBar = $("<div id='go_nav_bar'></div>");
    $navBar.css({
        'left' : '0px',
        'bottom' : '0px',
        'width' : '100%',
        'height' : '0px',
        'padding' : '0 5px',
        'position' : 'fixed',
        'zIndex' : '1001',
        'backgroundColor' : 'white',
        'border-top' : 'solid 1px black'
    });
    
    if ($("footer").length < 1) {
        $("body").append('<footer></footer>');
    }

    $("footer").append($navBar);
    var $vtFooter = jQuery("footer");
    $vtFooter.css({
        'border-top' : '0',
        'margin-left' : '230px',
        'zIndex' : '995'
    });
    var circleButton = jQuery(".circle-button").css('bottom');
    var favDivButton = jQuery("#fab-div-area").css('margin-bottom');
    
    $("footer").prepend($('<div id="go_nav_tab" title="<?=$lh->translationFor('open_tab')?>"> <i class="fa fa-chevron-up"></i> </div>'));
    $("#go_nav_tab").css({
        'left' : '3px',
        'bottom' : '0px',
        'padding' : '0 5px',
        'backgroundColor' : 'white',
        'border-style' : 'solid',
        'border-width' : '1px 1px 0',
        'border-color' : 'black',
        'cursor' : 'pointer',
        'position' : 'fixed',
        'zIndex' : '1002',
        'height' : '15px',
    });

    var resized = true;
    $("#go_nav_tab").click(function() {
        var barHeight = 37;
        $("#go_nav_tab").stop(true, false).animate({
            bottom: resized ? (barHeight - 1) : -1
        }, function() {
            if (resized) {
                $(this).attr('title', '<?=$lh->translationFor('open_tab')?>');
            } else {
                $(this).attr('title', '<?=$lh->translationFor('close_tab')?>');
            }
        });
        
        $("#go_nav_bar").stop(true, false).animate({
            height: resized ? barHeight : 0
        });
        
        $("footer").stop(true, false).animate({
            paddingBottom : resized ? barHeight : 15,
        });
        
        $(".circle-button").stop(true, false).animate({
            bottom : resized ? (barHeight + parseInt(circleButton)) : circleButton,
        });
        
        $("#fab-div-area").stop(true, false).animate({
            marginBottom : resized ? (barHeight + parseInt(favDivButton)) : favDivButton,
        });
        
        if (($(window).scrollTop() + document.body.clientHeight) == $(document).height()) {
            $("html, body").animate({ scrollTop: $(document).height() }, 'slow');
        }
        
        $("#go_nav_tab i").attr({
            class: resized ? 'fa fa-chevron-down' : 'fa fa-chevron-up'
        });
        
        resized = !resized;
    });
    <?php
    }
    ?>

    // buttons
    $("#go_agent_dialer").append("<li id='go_nav_btn' style='margin-top: 10px;'></li>");
    //$("#go_nav_btn").append("<div id='go_btn_group' class='btn-group dropup pull-right' style='margin: 0 1px;'>");
    //$("#go_btn_group").append("<button id='dropdownMenuAgent' type='button' data-toggle='dropdown' class='btn btn-default dropdown-toggle' style='margin: 5px 0;'><i class='fa fa-navicon'></i></button>");
    //$("#go_btn_group").append("<ul id='go_dropdown' class='dropdown-menu'>");
    //$("#go_dropdown").append("<li id='manual-dial'><a><?=$lh->translationFor('manual_dial')?> <span class='fa fa-phone pull-right'></span></a></li>");
    //$("#go_dropdown").append("<li><a><?=$lh->translationFor('available_hot_keys')?> <span class='fa fa-keyboard-o pull-right'></span></a></li>");
    //$("#go_dropdown").append("<li><a><?=$lh->translationFor('active_callbacks')?> <span class='badge pull-right bg-green'>0</span></a></li>");
    //$("#go_dropdown").append("<li><a><?=$lh->translationFor('callbacks_for_today')?> <span class='badge pull-right bg-red'>0</span></a></li>");
    //$("#go_dropdown").append("<li><a><?=$lh->translationFor('enter_pause_code')?> <span class='fa fa-pause-circle-o pull-right'></span></a></li>");
    //$("#go_dropdown").append("<li><a><?=$lh->translationFor('lead_search')?> <span class='fa fa-search pull-right'></span></a></li>");
    //$("#go_dropdown").append("<li id='btnLogMeOut'><a><?=$lh->translationFor('logout_from_phone')?> <span class='fa fa-sign-out pull-right'></span></a></li>");
    //$("#go_btn_group").append("</ul>");
    //$("#go_nav_btn").append("</div>");
    $("#go_nav_btn").append("<div id='livecall' class='center-block'><h3 class='nolivecall' title=''><?=$lh->translationFor('no_live_call')?></h3></div>");
    $("#go_nav_btn").append("<div id='go_btn_div' class='center-block' style='text-align: center;'></div>");
    $("#go_btn_div").append("<button id='btnDialHangup' title='<?=$lh->translationFor('dial_next_call')?>' class='btn btn-primary btn-lg' style='margin: 0 5px 5px 0; font-size: 16px;'><i class='fa fa-phone'></i></button>");
    $("#go_btn_div").append("<button id='btnResumePause' title='<?=$lh->translationFor('resume_dialing')?>' class='btn btn-success btn-lg' style='margin: 0 5px 5px 0; font-size: 16px;'><i class='fa fa-play'></i></button>");
    $("#go_btn_div").append("<button id='btnParkCall' title='<?=$lh->translationFor('park_call')?>' class='btn btn-warning btn-lg' style='margin: 0 5px 5px 0; font-size: 15px; padding-bottom: 11px;'><i class='fa fa-music'></i></button>");
    $("#go_btn_div").append("<button id='btnTransferCall' title='<?=$lh->translationFor('transfer_call')?>' class='btn btn-default btn-lg' style='margin: 0 5px 5px 0; padding: 10px 19px; font-size: 15px;'><i class='fa fa-random'></i></button>");
    $("#go_btn_div").append("<button id='btnIVRParkCall' title='<?=$lh->translationFor('ivr_park_call')?>' class='btn btn-default btn-lg' style='margin: 0 5px 5px 0; padding: 10px 18px 10px 19px; font-size: 15px;'><i class='fa fa-tty'></i></button>");
    $("#go_btn_div").append("<button id='btnReQueueCall' title='<?=$lh->translationFor('requeue_call')?>' class='btn btn-default btn-lg' style='margin: 0 5px 5px 0; padding: 10px 20px 10px 21px; font-size: 15px;'><i class='fa fa-refresh'></i></button>");
    $("#go_btn_div").append("<button id='btnQuickTransfer' title='<?=$lh->translationFor('quick_transfer')?>' class='btn btn-default btn-lg' style='margin: 0 5px 5px 0; padding: 10px 20px 10px 21px; font-size: 15px;'><i class='fa fa-exchange'></i></button>");
    //$("#go_nav_btn").append("<div id='cust-info' class='center-block' style='text-align: center; line-height: 35px;'><i class='fa fa-user'></i> <span id='cust-name' style='padding-right: 20px;'></span> <i class='fa fa-phone-square'></i> <span id='cust-phone'></span><input type='hidden' id='cust-phone-number' /></div>");
    $("#go_agent_status").append("<li><div id='MainStatusSpan' class='center-block hidden-xs'>&nbsp;</div></li>");
    $("#go_agent_status").append("<li><div id='RecordControl' class='center-block hidden-xs' style='text-align: center;'><button id='btnRecordCall' onclick='btnRecordCall();' title='<?=$lh->translationFor('start_recording')?>' class='btn btn-danger btn-sm' style='margin: 0 5px 5px 0; font-size: 16px;'><?=$lh->translationFor('start_recording')?></button></div></li>");
    $("#go_agent_dialpad").append("<li><div id='AgentDialPad' class='center-block' style='text-align: center; min-width: 200px;'></div></li>");
    $("#AgentDialPad").append("<button type='button' id='dialer-pad-1' class='btn btn-default btn-lg btn-raised' style='padding: 10px 25px; margin: 0 5px 5px 0; font-size: 16px; font-family: monospace;'> 1 </button>");
    $("#AgentDialPad").append("<button type='button' id='dialer-pad-2' class='btn btn-default btn-lg btn-raised' style='padding: 10px 25px; margin: 0 5px 5px 0; font-size: 16px; font-family: monospace;'> 2 </button>");
    $("#AgentDialPad").append("<button type='button' id='dialer-pad-3' class='btn btn-default btn-lg btn-raised' style='padding: 10px 25px; margin: 0 0 5px 0; font-size: 16px; font-family: monospace;'> 3 </button>");
    $("#AgentDialPad").append("<button type='button' id='dialer-pad-4' class='btn btn-default btn-lg btn-raised' style='padding: 10px 25px; margin: 0 5px 5px 0; font-size: 16px; font-family: monospace;'> 4 </button>");
    $("#AgentDialPad").append("<button type='button' id='dialer-pad-5' class='btn btn-default btn-lg btn-raised' style='padding: 10px 25px; margin: 0 5px 5px 0; font-size: 16px; font-family: monospace;'> 5 </button>");
    $("#AgentDialPad").append("<button type='button' id='dialer-pad-6' class='btn btn-default btn-lg btn-raised' style='padding: 10px 25px; margin: 0 0 5px 0; font-size: 16px; font-family: monospace;'> 6 </button>");
    $("#AgentDialPad").append("<button type='button' id='dialer-pad-7' class='btn btn-default btn-lg btn-raised' style='padding: 10px 25px; margin: 0 5px 5px 0; font-size: 16px; font-family: monospace;'> 7 </button>");
    $("#AgentDialPad").append("<button type='button' id='dialer-pad-8' class='btn btn-default btn-lg btn-raised' style='padding: 10px 25px; margin: 0 5px 5px 0; font-size: 16px; font-family: monospace;'> 8 </button>");
    $("#AgentDialPad").append("<button type='button' id='dialer-pad-9' class='btn btn-default btn-lg btn-raised' style='padding: 10px 25px; margin: 0 0 5px 0; font-size: 16px; font-family: monospace;'> 9 </button>");
    $("#AgentDialPad").append("<button type='button' id='dialer-pad-ast' class='btn btn-default btn-lg btn-raised hidden' style='padding: 10px 25px; margin: 0 5px 5px 0; font-size: 16px; font-family: monospace;'> * </button>");
    $("#AgentDialPad").append("<button type='button' id='dialer-pad-clear' class='btn btn-default btn-lg btn-raised' style='padding: 12.5px 23px; margin: 0 5px 5px 0; font-size: 16px; font-family: monospace;' title='<?=$lh->translationFor('clear')?>'> <i class='fa fa-times'></i> </button>");
    $("#AgentDialPad").append("<button type='button' id='dialer-pad-0' class='btn btn-default btn-lg btn-raised' style='padding: 10px 25px; margin: 0 5px 5px 0; font-size: 16px; font-family: monospace;'> 0 </button>");
    $("#AgentDialPad").append("<button type='button' id='dialer-pad-hash' class='btn btn-default btn-lg btn-raised hidden' style='padding: 10px 25px; margin: 0 0 5px 0; font-size: 16px; font-family: monospace;'> # </button>");
    $("#AgentDialPad").append("<button type='button' id='dialer-pad-undo' class='btn btn-default btn-lg btn-raised' style='padding: 12.5px 22.6px; margin: 0 0 5px 0; font-size: 16px; font-family: monospace;' title='<?=$lh->translationFor('undo')?>'> <i class='fa fa-undo'></i> </button>");
    $("#AgentDialPad").append("<span id='for_dtmf' style='display: block;' class='hidden'><small>(<?=$lh->translationFor('for_dtmf')?>)</small></span>");
    
    $("#go_agent_manualdial").append("<li><div class='input-group' style='padding: 0 3px;'><input type='text' maxlength='18' name='MDPhonENumbeR' id='MDPhonENumbeR' class='form-control phonenumbers-only' value='' placeholder='<?=$lh->translationFor('enter_phone_number')?>' onkeyup='activateLinks();' onchange='activateLinks();' onkeydown='enableDialOnEnter(event);' style='padding: 3px 2px; color: #222; height: 30px;' aria-label='...' /><div class='input-group-btn' role='group'><button type='button' class='btn btn-success btn-raised' id='manual-dial-now' style='padding: 6px 10px; height: 30px;'><i class='fa fa-phone'></i></button><button type='button' class='btn btn-success dropdown-toggle' style='padding: 0 6px; height: 30px;' data-toggle='dropdown' id='manual-dial-dropdown'>&nbsp;<span id='code_flag' class='flag flag-us'></span><span class='sr-only'>Toggle Dropdown</span>&nbsp;</button><ul id='country_codes' class='dropdown-menu dropdown-menu-right scrollable-menu' role='menu'></ul></div></div><input type='hidden' name='MDDiaLCodE' id='MDDiaLCodE' class='digits-only' value='1' /><input type='hidden' name='MDPhonENumbeRHiddeN' id='MDPhonENumbeRHiddeN' value='' /><input type='hidden' name='MDLeadID' id='MDLeadID' value='' /><input type='hidden' name='MDType' id='MDType' value='' /><input type='checkbox' name='LeadLookUP' id='LeadLookUP' size='1' value='0' class='hidden' disabled /><input type='hidden' size='24' maxlength='20' name='MDDiaLOverridE' id='MDDiaLOverridE' class='cust_form' value='' /></li>");

    $("#go_agent_login").append("<li><button id='btnLogMeIn' class='btn btn-warning btn-lg center-block' style='margin-top: 2px;'><i class='fa fa-sign-in'></i> <?=$lh->translationFor('login_on_phone')?></button></li>");
    $("#go_agent_logout").append("<li><button id='btnLogMeOut' class='btn btn-warning center-block' style='margin-top: 2px; padding: 5px 12px;'><i class='fa fa-sign-out'></i> <?=$lh->translationFor('logout_from_phone')?></button></li>");
    
    $("div.navbar-custom-menu").prepend("<span id='server_date' class='hidden-xs hidden-sm no-selection pull-left' style='color: #fff; line-height: 21px; height: 50px; padding: 14px 20px;'></span>");
    
    var paddingHB = 100;
    var navConBar = $("header.main-header").innerHeight();
    var minusThis = (parseInt(navConBar) + parseInt(paddingHB));
    var newHeight = parseInt($("body").innerHeight()) - minusThis;
    $("aside.control-sidebar div.tab-content").css({
        'height': newHeight
    });

    $("button[id^='btn']").click(function() {
        var btnID = $(this).attr('id').replace('btn', '');
        switch (btnID) {
            case "DialHangup":
                btnDialHangup();
                break;
            case "ResumePause":
                btnResumePause();
                break;
            case "LogMeIn":
                btnLogMeIn();
                break;
            case "LogMeOut":
                btnLogMeOut();
                break;
            case "ReQueueCall":
                btnReQueueCall();
                break;
        }
    });
    
    $("li[id^='btn']").click(function() {
        var btnID = $(this).attr('id').replace('btn', '');
        switch (btnID) {
            case "LogMeOut":
                btnLogMeOut();
                break;
        }
    });

    var showInfo = false;
    $("#cust-info").click(function() {
        if (!showInfo) {
            $("#dialog-custinfo").modal({
                backdrop: 'static',
                show: true
            });
            showInfo = true;
        } else {
            $("#dialog-custinfo").modal('hide');
            showInfo = false;
        }
    });

    $("#manual-dial").click(function() {
        $("#manual-dial-box").modal({
            backdrop: 'static',
            show: true
        });
    });
    
    $("#dialog-custinfo div.modal-dialog").css({'width': '750px'});
    
    $("a[id^='manual-dial-'], button[id^='manual-dial-']").click(function() {
        //$('#manual-dial-box').modal('hide');
        var btnID = $(this).attr('id').replace('manual-dial-', '');
        if (btnID != 'dropdown' && ! $(this).hasClass('disabled') && agentcall_manual > 0) {
            NewManualDialCall(btnID.toUpperCase());
            activateLinks();
        }
    });
    <?php
    //ECCS Customization
	if( ECCS_BLIND_MODE === 'y'){
    ?>
	    // Keyboard Shortcuts
	    $(document).keydown(function(e){
		if( enable_eccs_shortcuts == 0 ){
			return;
		}
            if (AgentDispoing < 1) {
                // User Exit
                if(e.shiftKey && e.key == "End"){
                      if (is_logged_in && (live_customer_call > 0 || XD_live_customer_call > 0)) {
                          swal({
                              title: '<?=$lh->translationFor('error')?>',
                              text: '<?=$lh->translationFor('currently_in_call')?>',
                              type: 'error'
                          });
                      } else {
                          btnLogMeOut();
                      }
      
                // Phone Log In
                } else if(e.shiftKey && e.key == "Home") {
                      if (is_logged_in && ((use_webrtc && phoneRegistered) || !use_webrtc)) {
                          swal({
                              title: '<?=$lh->translationFor('error')?>',
                              text: '<?=$lh->translationFor('phone_already_logged_in')?>',
                              type: 'error'
                          });
                      } else {
                          btnLogMeIn();
                      }
      
                    // Dial or Hangup
                    } else if(e.shiftKey && e.key == "!") {
                      console.log('Shift: ' + e.shiftKey, 'Key: ' + e.key);
                      btnDialHangup();
                        
                    // Resume or Pause
                    } else if(e.shiftKey && e.key == "@") {
                      if (live_customer_call < 1) {
                          btnResumePause();
                    }
      
                // Open Web form
                    } else if(e.shiftKey && e.key == "#") {
                      $("#openWebForm").click();
      
                // Toggle Lead Preview
                    } else if(e.shiftKey && e.key == "$") {
                      $("#LeadPreview").click();
      
                // Callbacklist
                    } else if(e.shiftKey && e.key == "%") {
                      if ($("#loaded-contents").is(':visible')) {
                          MainPanelToFront();
                      } else {
                          $("a[href='#callbackslist']").click();
                      }
                    }
            }
        });
   
     <?php
	}	
    // /. ECCS Customization
    ?>

    setInterval(function() {
        if (!$('#go_dropdown').is(':visible')) {
            $('.circle-button').show();
        } else {
            $('.circle-button').hide();
        }
    }, 500);
    
    $('#dropdownMenuAgent').click(function() {
        $('.circle-button').hide();
    });

    updateButtons();
    toggleButtons(dial_method, ivr_park_call, call_requeue_button);
    toggleStatus('NOLIVE');
    activateLinks();
    
    // device detection
    if (parseInt($("body").innerWidth()) < 768) {
        isMobile = true;
    }

    window.addEventListener("beforeunload", function (e) {
        if (is_logged_in && ((use_webrtc && phoneRegistered) || !use_webrtc)) {
            var confirmationMessage = "<?=$lh->translationFor('currently_in_call')?>";
        
            (e || window.event).returnValue = confirmationMessage;     //Gecko + IE
            return confirmationMessage;                                //Webkit, Safari, Chrome etc.
        } else {
            //GOagentWebRTC.close();
        }
    });

    $("#notSelectedINB, #selectedINB").sortable({
        connectWith: ".connectedINB",
        placeholder: "ui-state-highlight",
        receive: function(event, ui) {
            if ($(this).attr('id') == 'notSelectedINB' && $(this).text() != "") {
                $("#scButton").html('<?=$lh->translationFor('select_all')?>');
            }
        },
        remove: function(event, ui) {
            if ($(this).attr('id') == 'notSelectedINB' && $(this).text() == "") {
                $("#scButton").html('<?=$lh->translationFor('remove_all')?>');
            } else if ($(this).attr('id') == 'selectedINB' && $(this).text() == "") {
                $("#scButton").html('<?=$lh->translationFor('select_all')?>');
            }
        }
    }).disableSelection();
    
    $("#select_camp").change(function() {
        var camp = $(this).val();
        $("#inboundSelection, #scButton, #selectionNote").addClass('hidden');
        $("#closerSelectBlended").closest('p').addClass('hidden');
        if (camp.length > 0) {
            $("#scSubmit").removeClass('disabled');
            if (agent_choose_ingroups == '1') {
                $("#logSpinner").removeClass('hidden');
                $("#scButton").html('<?=$lh->translationFor('select_all')?>');
                var postData = {
                    goAction: 'goGetInboundGroups',
                    goUser: uName,
                    goPass: uPass,
                    goCampaign: $(this).val(),
                    responsetype: 'json'
                };
            
                $.ajax({
                    type: 'POST',
                    url: '<?=$goAPI?>/goAgent/goAPI.php',
                    processData: true,
                    data: postData,
                    dataType: "json",
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    }
                })
                .done(function (data) {
                    var result = data.result;
                    $("#logSpinner").addClass('hidden');
                    if (result != 'error') {
                        var inb_list = '';
                        $.each(data.data.inbound_groups, function(idx, inbg) {
                            inb_list += "<li class='ui-state-default'><abbr title='"+inbg+"'>"+idx+"</abbr></li>";
                        });
                        $("#selectedINB").empty();
                        $("#notSelectedINB").empty().append(inb_list);
                        $("#inboundSelection, #scButton, #selectionNote").removeClass('hidden');
                        $("#closerSelectBlended").closest('p').removeClass('hidden');
                    } else {
                        //alert(data.message);
                        $("#inboundSelection, #scButton, #selectionNote").addClass('hidden');
                        $("#closerSelectBlended").closest('p').addClass('hidden');
                    }
                });
            } else {
                var inb_list = $.trim(user_closer_campaigns.slice(0,-1)),
                user_inb_list = '';
                $.each(inb_list.split(" "), function(idx, inbg) {
                    user_inb_list += "<li class='ui-state-default'><abbr title='"+inbg+"'>"+inbg+"</abbr></li>";
                });
                $("#selectedINB").empty().append(user_inb_list);
            }
        } else {
            $("#scSubmit").addClass('disabled');
        }
    });
    
    $("#transfer-selection").change(function() {
        var transfer_selected = $(this).val();
        $("#transfer-closer, #transfer-regular").addClass('hidden');
        //$("#closerSelectBlended").closest('p').addClass('hidden');
        if (transfer_selected.length > 0) {
            var thisSelected = transfer_selected.toLowerCase();
            //$("#scSubmit").removeClass('disabled');

            $("#transfer-"+thisSelected).removeClass('hidden');
            //$("#closerSelectBlended").closest('p').removeClass('hidden');
        } else {
            //$("#scSubmit").addClass('disabled');
        }
    });

    $("#scButton").click(function() {
        var content = $(this).text();
        if (content == '<?=$lh->translationFor('select_all')?>') {
            content = '<?=$lh->translationFor('remove_all')?>';
            var divContent = $("#notSelectedINB").html();
            $("#notSelectedINB").empty();
            $("#selectedINB").append(divContent);
        } else {
            content = '<?=$lh->translationFor('select_all')?>';
            var divContent = $("#selectedINB").html();
            $("#selectedINB").empty();
            $("#notSelectedINB").append(divContent);
        }
        $(this).text(content);
    });
    
    $("#scSubmit").click(function(e) {
        e.preventDefault();
        var inbArray = '';
        var CloserSelectList = '';
        var origPreloader = $(".preloader center").html();
        $(".preloader center").append('<br><br><span style="font-size: 24px; color: white;"><?=$lh->translationFor('logging_in_phones')?>...</span>');
        $(".preloader").fadeIn('slow');
        $("#scSubmit").addClass('disabled');
        $("#selectedINB").find('abbr').each(function(index) {
            inbArray += $(this).text() + "|";
            CloserSelectList += $(this).text() + " ";
        });
        $("#CloserSelectList").val(CloserSelectList.slice(0, -1));
        
        if (use_webrtc && !phone.isConnected()) {
            phone.start();
        }
        
        var loggingInUser = setInterval(function() {
            if ((use_webrtc && !registrationFailed && phoneRegistered) || !use_webrtc) {
                clearInterval(loggingInUser);
                
                var postData = {
                    goAction: 'goLoginUser',
                    goUser: uName,
                    goPass: uPass,
                    goCampaign: $("#select_camp").val(),
                    goIngroups: inbArray.slice(0, -1),
                    responsetype: 'json',
                    goCloserBlended: ($("#closerSelectBlended").is(':checked') ? 1 : 0),
                    goUseWebRTC: use_webrtc
                };
        
                $.ajax({
                    type: 'POST',
                    url: '<?=$goAPI?>/goAgent/goAPI.php',
                    processData: true,
                    data: postData,
                    dataType: "json",
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    }
                })
                .done(function (result) {
                    $(".preloader").fadeOut('slow');
                    $(".preloader center").html(origPreloader);
                    if (result.result != 'error') {
                        $("#select-campaign").modal('hide');
                        MainPanelToFront();
                        
                        updateButtons();
                        refresh_interval = 1000;
                        is_logged_in = 1;
                        check_if_logged_out = 1;
                        logout_stop_timeouts = 0;
                        just_logged_in = true;
                        
                        $.each(result.data, function(key, value) {
                            if (key == 'campaign_settings') {
                                $.each(value, function(cKey, cValue) {
                                    var patt = /^timer_action/g;
                                    if (patt.test(cKey)) {
                                        $.globalEval("campaign_"+cKey+" = '"+cValue+"';");
                                    } else {
                                        if (typeof cValue == 'undefined' || typeof cValue == 'null') {
                                            cValue = "";
                                        }
                                        
                                        if (cKey == 'campaign_id') {
                                            $.post("<?=$module_dir?>GOagentJS.php", {'module_name': 'GOagent', 'action': 'SessioN', 'campaign_id': cValue, 'is_logged_in': is_logged_in});
                                        }
                                        
                                        if (cKey == 'dial_prefix') {
                                            dial_prefix = cValue;
                                            $.globalEval(cKey+" = '"+cValue+"';");
                                        }
                                    
                                        if (cKey == 'manual_dial_prefix') {
                                            cValue = (cValue.length < 1) ? dial_prefix : cValue;
                                            $.globalEval(cKey+" = '"+cValue+"';");
                                        }
                                        
                                        if (cKey == 'pause_after_each_call') {
                                            cKey = 'dispo_check_all_pause';
                                            cValue = (cValue == 'Y') ? 1 : 0;
                                        }
                                        
                                        if (cKey == 'call_requeue_button') {
                                            cValue = (cValue == 'Y') ? 1 : 0;
                                        }
                                        
                                        if (cKey == 'scheduled_callbacks') {
                                            cKey = 'camp_scheduled_callbacks';
                                        }
                                        
                                        var rec_patt = /^(campaign_rec_filename|default_group_alias|default_xfer_group)$/g;
                                        if (rec_patt.test(cKey)) {
                                            $.globalEval("LIVE_"+cKey+" = '"+cValue+"';");
                                        }
                                        
                                        if (cKey == 'campaign_recording') {
                                            $.globalEval("LIVE_"+cKey+" = '"+cValue+"';");
                                        }
                                        
                                        var dispo_patt = /^(disable_dispo_screen|disable_dispo_status)$/g;
                                        if (!dispo_patt.test(cKey)) {
                                            if (cKey == 'web_form_address' || cKey == 'web_form_address_two') {
                                                $.globalEval(cKey+" = '"+cValue+"';");
                                                $.globalEval("VDIC_"+cKey+" = '"+cValue+"';");
                                                $.globalEval("TEMP_VDIC_"+cKey+" = '"+cValue+"';");
                                            } else {
                                                $.globalEval(cKey+" = '"+cValue+"';");
                                                if (cKey == 'auto_dial_level') {
                                                    $.globalEval("starting_dial_level = '"+cValue+"';");
                                                }
                                                if (cKey == 'campaign_id') {
                                                    $.globalEval("campaign = '"+cValue+"';");
                                                    $.globalEval("group = '"+cValue+"';");
                                                }
                                                if (cKey == 'api_manual_dial') {
                                                    var amqc = 1;
                                                    var amqcc = 0;
                                                    if (cValue == 'QUEUE') {
                                                        amqc = 0;
                                                        amqcc = 1;
                                                    }
                                                    $.globalEval("AllowManualQueueCalls = '"+amqc+"';");
                                                    $.globalEval("AllowManualQueueCallsChoice = '"+amqcc+"';");
                                                }
                                                if (cKey == 'manual_preview_dial') {
                                                    var mdp = 1;
                                                    if (cValue == 'DISABLED')
                                                        {mdp = 0;}
                                                    $.globalEval("manual_dial_preview = '"+mdp+"';");
                                                }
                                                if (cKey == 'manual_dial_override') {
                                                    if (cValue == 'ALLOW_ALL')
                                                        {agentcall_manual = '1';}
                                                    if (cValue == 'DISABLE_ALL')
                                                        {agentcall_manual = '0';}
                                                }
                                            }
                                        } else {
                                            $.globalEval(cKey+" = '"+cValue+"';");
                                        }
                                    }
                                });
                            } else {
                                var patt = /^(user|pass|statuses|statuses_count|pause_codes|pause_codes_count)$/g;
                                //console.log("var "+key+" = '"+value+"';");
                                if (!patt.test(key)) {
                                    if (key == 'now_time') {
                                        $.globalEval("NOW_TIME = '"+value+"';");
                                        $.globalEval("SQLdate = '"+value+"';");
                                    } else if (key == 'start_time') {
                                        $.globalEval("StarTtimE = '"+value+"';");
                                        $.globalEval("UnixTime = '"+value+"';");
                                    } else if (key == 'VARxferGroups' || key == 'VARxferGroupsNames' || key == 'VARingroups' || key == 'VARingroup_handlers') {
                                        $.globalEval(key+" = new Array("+value+");");
                                    } else if (key == 'hotkeys' || key == 'hotkeys_content') {
                                        $.globalEval(key+" = {"+value+"};");
                                    } else if (key == 'session_name') {
                                        $.globalEval(key+" = '"+value+"';");
                                        $.globalEval("webform_session = '&"+key+"="+value+"';");
                                    } else if (key == 'alt_phone_dialing') {
                                        $.globalEval(key+" = "+value+";");
                                        $.globalEval("starting_"+key+" = "+value+";");
                                    } else if (key == 'closer_blended') {
                                        $.globalEval(key+" = "+value+";");
                                    } else if (key == 'callback_statuses_list') {
                                        $.globalEval(key+" = '"+value+"';");
                                        $.globalEval("VARCBstatusesLIST = '"+value+"';");
                                    } else if (key == 'custom_fields_launch') {
                                        $.globalEval(key+" = '"+value+"';");
                                    } else if (key == 'custom_fields_list_id') {
                                        $.globalEval(key+" = '"+value+"';");
                                    } else if (key == 'enable_callback_alert') {
                                        $.globalEval(key+" = "+value+";");
                                    } else if (key == 'cb_noexpire') {
                                        $.globalEval(key+" = "+value+";");
                                    } else if (key == 'cb_sendemail') {
                                        $.globalEval(key+" = "+value+";");
                                    } else if (key == 'manual_dial_min_digits') {
                                        var defaultValue = (typeof value !== 'undefined' && value > 0) ? value : 6;
                                        $.globalEval(key+" = "+defaultValue+";");
                                    } else {
                                        $.globalEval(key+" = '"+value+"';");
                                    }
                                } else {
                                    if (key != 'user') {
                                        if (typeof value == 'object') {
                                            var idxArr = '';
                                            var valArr = '';
                                            $.each(value, function(idx, val) {
                                                idxArr += "'"+idx+"',";
                                                valArr += "'"+val+"',";
                                            });
                                            $.globalEval(key+" = new Array("+idxArr.slice(0,-1)+");");
                                            $.globalEval(key+"_names = new Array("+valArr.slice(0,-1)+");");
                                        } else if (typeof value == 'number') {
                                            $.globalEval(key+" = "+value+";");
                                        } else {
                                            $.globalEval(key+" = '"+value+"';");
                                        }
                                    }
                                }
                            }
                        });
            
                        if ((disable_dispo_screen == 'DISPO_ENABLED') || (disable_dispo_screen == 'DISPO_SELECT_DISABLED') || (disable_dispo_status.length < 1)) {
                            if (disable_dispo_screen == 'DISPO_SELECT_DISABLED') {
                                $.globalEval("hide_dispo_list = '1';");
                            } else {
                                $.globalEval("hide_dispo_list = '0';");
                            }
                            $.globalEval("disable_dispo_screen = '0';");
                            $.globalEval("disable_dispo_status = '';");
                        }
                        if ((disable_dispo_screen == 'DISPO_DISABLED') && (disable_dispo_status.length > 0)) {
                            $.globalEval("hide_dispo_list = '0';");
                            $.globalEval("disable_dispo_screen = '1';");
                            $.globalEval("disable_dispo_status = '"+disable_dispo_status+"';");
                        }
                        
                        if (alt_phone_dialing == 1) {
                            $("#DialALTPhoneMenu").show();
                        } else {
                            $("#DialALTPhoneMenu").hide();
                        }
                        
                        var vro_patt = /DISABLED/;
                        var camp_rec = campaign_recording;
                        if ((!vro_patt.test(vicidial_recording_override)) && (vicidial_recording > 0))
                            {camp_rec = vicidial_recording_override;}
                        if (vicidial_recording == '0')
                            {camp_rec = 'NEVER';}
                        campaign_recording = camp_rec;
                        LIVE_campaign_recording = camp_rec;
                        
                        if (campaign_recording == 'ONDEMAND') {
                            $("#RecordControl").removeClass('hidden');
                        } else {
                            $("#RecordControl").addClass('hidden');
                        }
                        
                        updateHotKeys();
		        // ECCS Customization
		        <?php if (ECCS_BLIND_MODE === "y"){ ?>
		        $('.clickhotkey').click(function() {
                    if (!minimizedDispo) {
                        console.log($(this).attr('data-id'));
                        var clicked_hotkey = $(this).attr('data-id');
    
                        var clicked_numkey_equivalent = [48, 49, 50, 51, 52, 53, 54, 55, 56, 57];
                        var clicked_numkey = clicked_numkey_equivalent;
                        var hotkeyId = { key: null };
    
                        for( var a=1; a < clicked_numkey.length; a++ ){
                            if( clicked_hotkey == clicked_numkey[a] ){
                            hotkeyId['key'] = a;				
                            } 
                        }
                        console.log("Hotkey ID: " + hotkeyId);
                        //triggerHotkey();
                        hotKeysAvailable(hotkeyId);
                    }
		        });
		        <?php } ?>
		        // /.ECCS Customization

                        updateButtons();
                        toggleButtons(dial_method, ivr_park_call, call_requeue_button, quick_transfer_button_enabled);
                        CallBacksCountCheck();
                        ShowURLTabs();
                        if (custom_fields_launch == 'LOGIN') {
                            GetCustomFields(custom_fields_list_id, true, true);
                        }
                    } else {
                        refresh_interval = 730000;
                        is_logged_in = 0;
                        swal({
                            title: '<?=$lh->translationFor('error')?>',
                            text: result.message,
                            type: 'error'
                        });
                        $("#scSubmit").removeClass('disabled');
                    }
                })
                .fail(function() {
                    $(".preloader").fadeOut('slow');
                    $(".preloader center").html(origPreloader);
                    refresh_interval = 730000;
                    is_logged_in = 0;
                    $("#scSubmit").removeClass('disabled');
                });
            } else {
                $("#select-campaign").modal('hide');
                if ((use_webrtc && registrationFailed && !phoneRegistered) || !use_webrtc) {
                    $(".preloader").fadeOut('slow');
                    $(".preloader center").html(origPreloader);
                }
            }
        }, 3000);
    });
    
    $("input.digits-only, input.phonenumbers-only").keypress(function (e) {
        var thisOne = $(this);
        //if the letter is not digit then display error and don't type anything
        if (thisOne.hasClass('digits-only') && e.which != 8 && e.which != 0 && (e.which < 48 || e.which > 57)) {
            //display error message
            return false;
        } else if (thisOne.hasClass('phonenumbers-only') && e.which != 8 && e.which != 0 && (e.which != 40 && e.which != 41 && e.which != 43 && e.which != 45) && (e.which < 48 || e.which > 57)) {
            //display error message
            return false;
        }
    });
    
    $('#manual-dial-box').on('hidden.bs.modal', function () {
        $("#MDPhonENumbeR").val('');
        $("#MDPhonENumbeRHiddeN").val('');
        $("#MDLeadID").val('');
        $("#MDType").val('');
    });
    
    $("#btn-dispo-submit").click(function() {
        if (minimizedDispo) {
            minimizedDispo = false;
            $.AdminLTE.options.controlSidebarOptions.minimizedDispo = false;
            $("body").css('overflow-y', 'auto');
            CustomerData_update();
        }
        
        DispoSelectSubmit();
    });
    
    $("[id^='btn-dispo-reset-']").click(function() {
        DispoSelectContent_create('', 'ReSET');
    });
    
    $("button[id^='dialer-pad-']").click(function() {
        var btnID = $(this).attr('id').replace('dialer-pad-', '');
        if (btnID == 'ast') {
            btnID = '*';
        }
        if (btnID == 'hash') {
            btnID = '#';
        }
        if (live_customer_call > 0) {
            // For DTMF
            console.log('DTMF: '+btnID);
            if (btnID != 'clear' && btnID != 'undo') {
                var options = {
                    'duration': 160,
                    'eventHandlers': {
                        'succeeded': function(originator, response) {
                            console.log('DTMF succeeded', originator, response);
                        },
                        'failed': function(originator, response, cause) {
                            console.log('DTMF failed', originator, response, cause);
                        },
                    }
                };
                
                globalSession.sendDTMF(btnID, options);
            }
        } else {
            if (!minimizedDispo) {
                // For Manual Dialing
                var currentValue = $("#MDPhonENumbeR").val();
                if (btnID == 'undo') {
                    currentValue = currentValue.slice(0, -1);
                } else if (btnID == 'clear') {
                    currentValue = '';
                } else {
                    currentValue += btnID;
                }
                $("#MDPhonENumbeR").val(currentValue);
                activateLinks();
            }
        }
    });
    
    $("#agents-tab").click(function() {
        if (!$(this).hasClass('active')) {
            RefreshAgentsView('AgentViewStatus', agent_status_view);
        }
    });
    
    $("#enableHotKeys").on('change', function() {
        if ($(this).is(':checked')) {
			$(document).on('keydown', 'body', hotKeysAvailable);
            $("#popup-hotkeys").fadeIn("fast");
        } else {
            $(document).off('keydown', 'body', hotKeysAvailable);
            $("#popup-hotkeys").fadeOut("fast");
        }
    });
    
    $("#muteMicrophone").on('change', function() {
        if ($(this).is(':checked')) {
            globalSession.unmute();
        } else {
            globalSession.mute();
        }
    });
    
    $("input").on('focus', function() {
	if ($("#enableHotKeys").is(':checked')) {
	    $(document).off('keydown', 'body', hotKeysAvailable);
	}
    });
    
    $("input").on('focusout', function() {
	if ($("#enableHotKeys").is(':checked')) {
            $(document).on('keydown', 'body', hotKeysAvailable);
	}
    });

    <?php if(ECCS_BLIND_MODE !== 'y'){?> 
    $("#popup-hotkeys").drags();
    <?php } ?>

    $("[data-toggle='control-sidebar']").on('click', function() {
        if (!minimizedDispo) {
            checkSidebarIfOpen();
        }
    });
    
    $("#reload-script").click(function() {
        LoadScriptContents();
    });
    
    // Hijack links on left menu
    $("a:regex(href, index|agent|edituser|profile|customerslist|events|messages|notifications|tasks|callbackslist|composemail|readmail)").on('click', hijackThisLink);
    
    $("#submitCBDate").click(function() {
	<?php if( ECCS_BLIND_MODE === 'y') { ?>
	var currDate = new Date(serverdate.getFullYear(), serverdate.getMonth(), serverdate.getDate(), serverdate.getHours(), serverdate.getMinutes());
	var cbDateVal = $('#callback-date').val();
	var resCbDateVal = cbDateVal.split(" ");
	var resDateCbDateVal = resCbDateVal[0].split("-");
	var resTimeCbDateVal = resCbDateVal[1].split(":");
	var eccs_callback_date = new Date(resDateCbDateVal[0], parseInt(resDateCbDateVal[1])-1, resDateCbDateVal[2], resTimeCbDateVal[0], resTimeCbDateVal[1]);

	if( (eccs_callback_date.getTime() < currDate.getTime() ) || eccs_callback_date == 'Invalid Date' ){
	   swal({
            title: "<?=$lh->translationFor('Invalid Call Back Schedule')?>",
	    text: "Enter a Valid Schedule",
            type: "warning",
            confirmButtonColor: "#DD6B55",
            confirmButtonText: "<?=$lh->translationFor('Confirm')?>"
           });
	   return false;
	}
	<?php } ?>
        if (!reschedule_cb && reschedule_cb_id < 1) {
            CallBackDateSubmit();
        } else {
            var cbOnly = $("#CallBackOnlyMe").prop('checked');
            var cbDate = $("#callback-date").val();
            var cbComment = $("#callback-comments").val();
            ReschedCallback(reschedule_cb_id, cbDate, cbComment, cbOnly);
        }
    });
    
    $("#openWebForm").click(function() {
        var webFormOptions = (ECCS_BLIND_MODE == 'n') ? 'toolbar=1,scrollbars=1,location=1,statusbar=1,menubar=1,resizable=1,width=640,height=450' : '';
        window.open(TEMP_VDIC_web_form_address, web_form_target, webFormOptions);
    });
    
    $("#openWebFormTwo").click(function() {
        var webFormOptions = (ECCS_BLIND_MODE == 'n') ? 'toolbar=1,scrollbars=1,location=1,statusbar=1,menubar=1,resizable=1,width=640,height=450' : '';
        window.open(TEMP_VDIC_web_form_address_two, web_form_target, webFormOptions);
    });
    
    $("form.formXFER").submit(function(e){
        e.preventDefault();
    });
    
    $("#cust-info-submit").click(function() {
        var submitCFData;
        var submitData = $("[id^='viewCust_']").serializeArray();
        var saveAsCustomer = $("#convert-customer").prop('checked');
        
        if ($("#custom-field-content").is(':visible')) {
            submitCFData = $("[id^='viewCustom_']").serializeArray();
        }
        
        swal({
            title: "<?=$lh->translationFor('saving_customer_info')?>",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#DD6B55",
            confirmButtonText: "<?=$lh->translationFor('submit')?>"
        }, function(){
            var postData = {
                goAction: 'goUpdateCustomer',
                goUser: uName,
                goPass: uPass,
                goLeadInfo: submitData,
                goCustomInfo: submitCFData,
                goSaveAsCustomer: saveAsCustomer,
                responsetype: 'json'
            };
        
            $.ajax({
                type: 'POST',
                url: '<?=$goAPI?>/goAgent/goAPI.php',
                processData: true,
                data: postData,
                dataType: "json",
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                }
            })
            .done(function (data) {
                if (data.result == 'success') {
                    swal({
                        title: '<?=$lh->translationFor('success')?>',
                        text: data.message,
                        type: 'success',
                        html: true
                    });
                    getContactList();
                    $("#view-customer-info").modal('hide');
                } else {
                    swal({
                        title: '<?=$lh->translationFor('error')?>',
                        text: data.message+".<br><br><?=$lh->translationFor('contact_admin')?>",
                        type: 'error',
                        html: true
                    });
                }
            });
        });
    });
    
    var country_cnt = Object.keys(country_codes).length;
    var country_list = '';
    if (country_cnt > 0) {
        country_list += '<li data-code="1" data-tld="us"><i class="flag flag-us"></i> United States of America</li>';
        country_list += '<li data-code="1" data-tld="ca"><i class="flag flag-ca"></i> Canada</li>';
        country_list += '<li data-code="44" data-tld="uk"><i class="flag flag-uk"></i> United Kingdom of Great Britain and Northern Ireland</li>';
        country_list += '<li data-code="63" data-tld="ph"><i class="flag flag-ph"></i> Philippines</li>';
        for (var key in country_codes) {
            // skip loop if the property is from prototype
            if (!country_codes.hasOwnProperty(key)) continue;
        
            var obj = country_codes[key];
            
            if (key !== 'USA_1' && key !== 'CAN_1' && key !== 'PHL_63' && key !== 'GBR_44') {
                country_list += '<li data-code="'+obj['code']+'" data-tld="'+obj['tld']+'"><i class="flag flag-'+obj['tld']+'"></i> '+ obj['name'] +'</li>';
            }
        }
        
        $("#country_codes").html(country_list);
    }
    
    $("#country_codes li").on('click', function() {
        var thisCode = $(this).data('code');
        var thisFlag = $(this).data('tld');
        $("#MDDiaLCodE").val(thisCode);
        $("#code_flag").attr('class', 'flag flag-'+thisFlag);
    });
    
    $("#manual-dial-dropdown").on('click', function() {
        var listVisible = $("#country_codes").is(':visible');
        if (!listVisible) {
            setTimeout(function() {
                $("#country_codes").scrollTop(0);
            }, 50);
        }
    });
    
    $('#view-missed-callbacks').on('hidden.bs.modal', function () {
        callback_alert = false;
        $("#missed-callbacks-content table tbody").html('');
        $("#missed-callbacks-content").hide();
        $("#missed-callbacks-loading").show();
    });

    <?php if(ECCS_BLIND_MODE ==='y'){ ?>
/*	var sidebar_counter = 1;
	$("aside.control-sidebar").addClass('control-sidebar-open');
	$("ul.nav.navbar-nav li:nth-of-type(3) a.hidden-xs").on('click', function(){
             //console.log('toggle clicked' + sidebar_counter);
             if(!(sidebar_counter == 0 || !!(sidebar_counter && !(sidebar_counter%2)))){
                 sidebar_counter++;
                 $.snackbar({content: "<i class='fa fa-info-circle fa-lg text-success' aria-hidden='true'></i>&nbsp; Sidebar Hidden", timeout: 2000, htmlAllowed: true});
             } else {
                 sidebar_counter++;
             }
        }); */
    <?php } ?>
    
    // WebSocket Keep-alive
    var checkWebsocketConn = setInterval(function() {
        if (is_logged_in && (use_webrtc && phoneRegistered) && typeof socket !== 'undefined' && (live_customer_call < 1 && XD_live_customer_call < 1)) {
            socket.send('PING');
        }
    }, 60000);
    
    $("ul.nav.navbar-nav li, ul.nav.nav-tabs li").on('click', function(e) {
        if (minimizedDispo) {
            e.preventDefault();
            return false;
        }
    });
});

function checkSidebarIfOpen(startUp) {
    var $isOpen = $("aside.control-sidebar").hasClass("control-sidebar-open");
    var options = {};
    var rightBar = 0;
    var sideBar = '230px';
    if (isMobile) {
        sideBar = '100%';
    }
    
    if ($isOpen && !startUp) {
        options = {
            'margin-right': '0'
        };
        if (parseInt($("body").innerWidth()) < 768) {
            options['margin-left'] = '0';
        }
        rightBar = '-' + sideBar;

	<?php if(ECCS_BLIND_MODE ==='y'){ ?>
        $.snackbar({content: "<i class='fa fa-info-circle fa-lg text-success' aria-hidden='true'></i>&nbsp; Login Tab Hidden", timeout: 2000, htmlAllowed: true});
	$("[data-toggle='control-sidebar']").attr("title", "Enter to Show Login Tab");
        <?php } ?>

    } else {
        options = {
            'margin-right': '230px'
        };
        if (parseInt($("body").innerWidth()) < 768) {
            options['margin-left'] = '-230px';
        }
        
        if (startUp) {
            $("aside.control-sidebar").addClass('control-sidebar-open');
        }
	<?php if(ECCS_BLIND_MODE ==='y'){ ?>
	    $("[data-toggle='control-sidebar']").attr("title", "Enter to Hide Login Tab");
        <?php } ?>
    }
    $("aside.content-wrapper").css(options);
    $("aside.control-sidebar").css({
        'width': sideBar,
        'right': rightBar
    });
}

function hijackThisLink(e) {
    e.preventDefault();
    if (!minimizedDispo) {
        var thisLink = $(this).attr('href');
        var hash = '';
        var origHash = window.location.hash.replace("#","");
        var breadCrumb = '<li><a href="agent.php"><i class="fa fa-home"></i> <?=$lh->translationFor('home')?></a></li>';
        if (/customerslist/g.test(thisLink)) {
            $(".content-heading span").html("<?=$lh->translationFor('contacts')?>");
            breadCrumb += '<li class="active"><?=$lh->translationFor('contacts')?></li>';
            hash = 'contacts';
        } else if (/agent|index/g.test(thisLink)) {
            $(".content-heading span").html("<?=$lh->translationFor('contact_information')?>");
            breadCrumb = '<li class="active"><i class="fa fa-home"></i> <?=$lh->translationFor('home')?></li>';
        } else if (/edituser/g.test(thisLink)) {
            $(".content-heading span").html("<?=$lh->translationFor('my_profile')?>");
            breadCrumb += '<li class="active"><?=$lh->translationFor('profile')?></li>';
            hash = 'profile';
        } else if (/profile/g.test(thisLink)) {
            $(".content-heading span").html("<?=$lh->translationFor('my_profile')?>");
            breadCrumb += '<li class="active"><?=$lh->translationFor('profile')?></li>';
            hash = 'profile';
        } else if (/events|callbackslist/g.test(thisLink)) {
            $(".content-heading span").html("<?=$lh->translationFor('list_of_callbacks')?>");
            breadCrumb += '<li class="active"><?=$lh->translationFor('callbacks')?></li>';
            hash = 'callbacks';
        } else if (/messages|readmail|composemail/g.test(thisLink)) {
            $(".content-heading span").html("<?=$lh->translationFor('messages')?>");
            breadCrumb += '<li class="active"><?=$lh->translationFor('messages')?></li>';
            
            $.each($("div[id^='mail-']"), function(idx, elem) {
                var thisID = $(elem).attr('id').replace('mail-', '');
                var searchID = new RegExp(thisID);
                if (searchID.test(thisLink)) {
                    $(elem).show();
                } else {
                    $(elem).hide();
                }
            });
            hash = 'messages';
        } else if (/notifications/g.test(thisLink)) {
            $(".content-heading span").html("<?=$lh->translationFor('notifications')?>");
            breadCrumb += '<li class="active"><?=$lh->translationFor('notifications')?></li>';
            hash = 'notifications';
        } else if (/tasks/g.test(thisLink)) {
            $(".content-heading span").html("<?=$lh->translationFor('tasks')?>");
            breadCrumb += '<li class="active"><?=$lh->translationFor('tasks')?></li>';
            hash = 'tasks';
        }
        
        if (origHash !== hash) {
            $(".preloader").fadeIn('fast');
            if (hash == 'contacts') {
                getContactList();
            }
        }
        
        if (hash.length > 0) {
            window.location.hash = hash;
            
            var thisContents = $("#loaded-contents div[id^='contents-']");
            $.each(thisContents, function() {
                var contentID = $(this).prop('id').replace('contents-', '');
                if (contentID == hash) {
                    $(this).show();
                } else {
                    $(this).hide();
                }
            });
            
            $("#cust_info").hide();
            $("#loaded-contents").show();
        } else {
            MainPanelToFront();
        }
        
        $(".content-heading ol").empty();
        $(".content-heading ol").html(breadCrumb);
        $("a:regex(href, index|agent|edituser|profile|customerslist|events|messages|notifications|tasks|callbackslist|composemail|readmail)").off('click', hijackThisLink).on('click', hijackThisLink);
        
        history.pushState('', document.title, window.location.pathname);
        
        if (origHash !== hash && hash != 'contacts') {
            $(".preloader").fadeOut('slow');
        }
    }
}

function btnLogMeIn () {
    logging_in = true;
    alertLogout = true;
    registrationFailed = false;
    if (is_logged_in && ((use_webrtc && !phoneRegistered) || !use_webrtc)) {
        swal({
            title: '<?=$lh->translationFor('error')?>',
            text: "<?=$lh->translationFor('phone_already_logged_in')?>",
            type: 'error',
            html: true,
            closeOnConfirm: false
        }, function() {
            logging_in = false;
            swal.close();
        });
        
        return;
    }
    
    if (typeof phone_login !== 'undefined' && phone_login.length > 0) {
        var postData = {
            goAction: 'goGetAllowedCampaigns',
            goUser: uName,
            goPass: uPass,
            responsetype: 'json'
        };
    
        $.ajax({
            type: 'POST',
            url: '<?=$goAPI?>/goAgent/goAPI.php',
            processData: true,
            data: postData,
            dataType: "json",
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            }
        })
        .done(function (result) {
            if (result.result == 'success') {
                var camp_list = result.data.allowed_campaigns;
                var camp_options = "<option value=''><?=$lh->translationFor('select_a_campaign')?></option>";
                $.each(camp_list, function(idx, camp) {
                    camp_options += "<option value='"+idx+"'>"+camp+"</option>";
                });
                $("#select-campaign select#select_camp").html(camp_options);
                $("#inboundSelection, #scButton, #selectionNote").addClass('hidden');
                $("#closerSelectBlended").closest('p').addClass('hidden');
                $("#select-campaign").modal({
                    keyboard: false,
                    backdrop: 'static',
                    show: true
                });
                
                $("#select-campaign").on('hidden.bs.modal', function() {
                    logging_in = false;
                    //console.log('hide', logging_in);
                });
            } else {
                swal({
                    title: '<?=$lh->translationFor('error')?>',
                    text: result.message+".<br><?=$lh->translationFor('contact_admin')?>",
                    type: 'error',
                    html: true
                });
            }
        });
    } else {
        swal({
            title: '<?=$lh->translationFor('phone_not_configured')?>',
            text: "<?=$lh->translationFor('contact_admin')?>",
            type: 'error',
            html: true
        });
    }
}

function btnLogMeOut () {
    if (!minimizedDispo) {
        refresh_interval = 730000;
        check_if_logged_out = 0;
        if (logoutWarn) {
            swal({
                title: "<?=$lh->translationFor('sure_to_logout')?>",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "<?=$lh->translationFor('log_me_out')?>",
                closeOnConfirm: false
            }, function(isConfirm){
                swal.close();
                sendLogout(isConfirm);
            });
        } else {
            sendLogout(true);
        }
    }
}
    
function sendLogout (logMeOut) {
    if (logMeOut) {
        var postData = {
            goAction: 'goLogoutUser',
            goUser: uName,
            goPass: uPass,
            goSIPserver: SIPserver,
            goNoDeleteSession: no_delete_sessions,
            goLogoutKickAll: LogoutKickAll,
            goServerIP: server_ip,
            goSessionName: session_name,
            goExtContext: ext_context,
            goAgentLogID: agent_log_id,
            responsetype: 'json',
            goUseWebRTC: use_webrtc
        };
    
        $.ajax({
            type: 'POST',
            url: '<?=$goAPI?>/goAgent/goAPI.php',
            processData: true,
            data: postData,
            dataType: "json",
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            }
        })
        .done(function (result) {
            if (result.result == 'success') {
                MainPanelToFront();
                is_logged_in = 0;
               	<?php
			//ECCS Customization
			if( ECCS_BLIND_MODE === 'y'){
	        ?>            
			$("#popup-hotkeys div.panel-body").html("<?=$lh->translationFor('no_available_hotkeys')?>");
		<?php
			}else{
		?> 
                if ($("#enableHotKeys").is(':checked')) {
                    $("#enableHotKeys").prop('checked', false);
                    $(document).off('keydown', 'body', hotKeysAvailable);
                    $("#popup-hotkeys").fadeOut("fast");
                }
		<?php 
			}
			// /.ECCS Customization
		?>
                
                $("#ScriptContents").html('');
                $("#reload-script").hide();
                CallBacksCountCheck();
                
                //alert('SUCCESS: You have been logged out of the dialer.');
                if (!!$.prototype.functionName) {
                    $.snackbar({content: "<i class='fa fa-info-circle fa-lg text-success' aria-hidden='true'></i>&nbsp; You have logged out from the dialer.", timeout: 5000, htmlAllowed: true});
                }
                
                setTimeout(function() {
                    if (use_webrtc) {
                        if (phone.isConnected()) {
                            phone.unregister(configuration);
                            
                            phone.stop();
                        }
                    }
                    
                    phoneRegistered = false;
                }, 3000);
                
                removeTabs();
                if (custom_fields_launch == 'LOGIN') {
                    GetCustomFields(null, false);
                }
                
                $("#btnLogMeIn").addClass('disabled');
                setTimeout(function() {
                    $("#btnLogMeIn").removeClass('disabled');
                }, 5000);
            } else {
                refresh_interval = 1000;
                var thisMessage = result.message;
                swal({
                    title: '<?=$lh->translationFor('error')?>',
                    text: thisMessage,
                    type: 'error'
                });
                
                if (thisMessage.indexOf('NOT connected') > -1) {
                    refresh_interval = 730000;
                    is_logged_in = 0;
                }
            }
        });
        logoutWarn = true;
        logout_stop_timeouts = 1;
        $("#MainStatusSpan").html('&nbsp;');
    } else {
        refresh_interval = 1000;
    }
}

function btnDialHangup () {
    //console.log(live_customer_call + ' ' + toggleButton('DialHangup'));
    if (live_customer_call == 1 || dialingINprogress == 1) {
        if (toggleButton('DialHangup')) {
            dialingINprogress = 0;
            has_inbound_call = 0;
            check_inbound_call = true;
            toggleButton('DialHangup', 'hangup', false);
            DialedCallHangup();
        }
    } else {
        toggleButton('DialHangup', 'hangup', false);
        if (ECCS_BLIND_MODE == 'y') {
            if (AutoDialReady > 0) {
                var dialCount = 0;
                dialInterval = setInterval(function() {
                    if (!check_inbound_call) {
                        console.log('Has Inbound Call', has_inbound_call);
                        if (has_inbound_call > 0) {
                            console.log('Already had a call...');
                            //toggleButton('DialHangup', 'dial');
                        } else {
                            console.log('Manual Dialing...');
                            toggleButton('ResumePause', 'off');
                            AutoDialReady = 0;
                            AutoDialWaiting = 0;
                            AutoDial_Resume_Pause("VDADpause");
                            
                            console.log('Live Customer Call', live_customer_call);
                            if (has_inbound_call < 1 && live_customer_call < 1) {
                                ManualDialNext('','','','','','0');
                            }
                        }
                        
                        clearInterval(dialInterval);
                        dialInterval = undefined;
                    }
                    
                    if (dialCount >= ECCS_DIAL_TIMEOUT) {
                        check_inbound_call = false;
                    }
                    
                    dialCount++;
                }, 1000);
            } else {
                toggleButton('ResumePause', 'off');
                ManualDialNext('','','','','','0');
            }
        } else {
            toggleButton('ResumePause', 'off');
            //live_customer_call = 1;
            //toggleStatus('LIVE');
            
            ManualDialNext('','','','','','0');
        }
    }
}

function btnResumePause () {
    if (live_customer_call < 1) {
        var btnClass = $('#btnResumePause').children('i').attr('class');
        if (/pause$/.test(btnClass)) {
            //toggleButton('ResumePause', 'resume');
            AutoDial_Resume_Pause("VDADpause");
            has_inbound_call = 0;
        } else {
            //toggleButton('ResumePause', 'pause');
            AutoDial_Resume_Pause("VDADready");
        }
    }
}

function btnRecordCall (action, norecord) {
    if (campaign_recording == 'ONDEMAND') {
        if (typeof norecord === 'undefined') {
            norecord = false;
        }
        if (action == 'STOP') {
            toggleButton('RecordCall', 'stop');
            $("#btnRecordCall").html('<?=$lh->translationFor('start_recording')?>');
            $("#btnRecordCall").removeClass('glowing_button');
            if (!norecord) {
                ConfSendRecording('StopMonitorConf', session_id, filename);
                
                swal({
                    title: "Recording Filename",
                    text: filename,
                    type: 'info'
                });
            }
        } else {
            toggleButton('RecordCall', 'start');
            $("#btnRecordCall").html('<?=$lh->translationFor('recording')?>');
            $("#btnRecordCall").addClass('glowing_button');
            if (!norecord) {
                ConfSendRecording('MonitorConf', session_id, '');
            }
        }
    }
}

function enableDialOnEnter(e) {
    if (e.keyCode != 13) {
        return;
    }
    
    if (live_customer_call < 1 && !minimizedDispo) {
        var phoneNumber = $('#MDPhonENumbeR').val();
    
        if (phoneNumber.length >= manual_dial_min_digits && agentcall_manual > 0) {
            NewManualDialCall('NOW');
            activateLinks();
        }
    }
}

function activateLinks() {
    if (AutoDialReady > 0 || live_customer_call > 0) {
        $('#MDPhonENumbeR').val('');
        $('#MDPhonENumbeR').prop('readonly', true);
    } else {
        $('#MDPhonENumbeR').prop('readonly', false);
    }
    var phoneNumber = $('#MDPhonENumbeR').val();

    if (phoneNumber.length >= manual_dial_min_digits && agentcall_manual > 0) {
        $("a[id^='manual-dial-'], button[id^='manual-dial-']").removeClass('disabled');
    } else {
        $("a[id^='manual-dial-'], button[id^='manual-dial-']").addClass('disabled');
    }
}

function updateHotKeys() {
    if ( (HK_statuses_camp > 0) && (user_level >= HKuser_level) && (hotkeys_active > 0) ) {
        $("#toggleHotkeys").show();
    } else {
        $("#toggleHotkeys").hide();
    }
    var hotkeysContent = "<dl class='dl-horizontal'>";
    var numkey_equivalent = [48, 49, 50, 51, 52, 53, 54, 55, 56, 57]; 
    for (var key in hotkeys) {
        var thisKey = hotkeys[key];
	var numKey = numkey_equivalent[key];
	
	hotkeysContent += "<a data-id='"+numKey+"' class='clickhotkey' <?php if( ECCS_BLIND_MODE === 'y'){ ?> title='"+hotkeys_content[thisKey]+"' <?php } ?> >";
        hotkeysContent += "<dt class='text-primary'>"+key+") "+thisKey+"</dt>";
        hotkeysContent += "<dd>- "+hotkeys_content[thisKey]+"</dd>";
	hotkeysContent += "</a>";
    }
    hotkeysContent += "</dl>";
    
    $("#popup-hotkeys .panel-body").html(hotkeysContent);

}

//ECCS Customization
<?php if ( ECCS_BLIND_MODE === 'y'){ ?>
function triggerHotkey(hotKeyId){
    console.log('Clicked hotkey: ' + hotKeyId);

}
<?php } ?>
// /.ECCS Customization

function hotKeysAvailable(e) {
    if (hotkeys[e.key] === undefined) {
        return;
    }
    
    if (minimizedDispo) {
        return;
    }
    
    //console.log('keydown: '+ hotkeys[e.key], event);
    if (live_customer_call || MD_ring_seconds > 4) {
        var HKdispo = hotkeys[e.key];
        var HKstatus = hotkeys_content[HKdispo];
        if (HKdispo) {
            CustomerData_update();
	
            if ( (HKdispo == 'ALTPH2') || (HKdispo == 'ADDR3') ) {
                if ($("#DiaLALTPhone").prop('checked')) {
                    DialedCallHangup('NO', 'YES', HKdispo);
                }
            } else {
                // transfer call to answering maching message with hotkey
                if ( (HKdispo == 'LTMG') || (HKdispo == 'XFTAMM') ) {
                    mainxfer_send_redirect('XfeRVMAIL', lastcustchannel, lastcustserverip);
                } else {
                    HKdispo_display = 4;
                    HKfinish = 1;
                    $("#HotKeyDispo").html(HKdispo + " - " + HKstatus);
                    //showDiv('HotKeyActionBox');
                    //hideDiv('HotKeyEntriesBox');
                    $("#DispoSelection").val(HKdispo);
                    alt_phone_dialing = starting_alt_phone_dialing;
                    alt_dial_active = 0;
                    alt_dial_status_display = 0;
                    DialedCallHangup('NO', 'YES', HKdispo);
                
                    if (custom_fields_enabled > 0) {
                        //vcFormIFrame.document.form_custom_fields.submit();
                    }
                }
            }
        //DispoSelect_submit();
        //AutoDialWaiting = 1;
        //AutoDial_ReSume_PauSe("VDADready");
        //alert(HKdispo + " - " + HKdispo_ary[0] + " - " + HKdispo_ary[1]);
        }
    }
}

function toggleButton (taskname, taskaction, taskenable, taskhide, toupperfirst, tolowerelse) {
    if (tolowerelse) {taskname = taskname.toLowerCase();}
    if (toupperfirst) {taskname = taskname.toUpperFirst();}
    
    var actClass = '';
    var actColor = '';
    var actTitle = '';
    var onClick = '';
    var isEnabled = (typeof taskenable !== 'undefined') ? taskenable : true;
    var isHidden = (typeof taskhide !== 'undefined') ? taskhide : false;
    
    if (typeof taskaction !== 'undefined' && taskaction.length > 0) {
        switch (taskaction.toLowerCase()) {
            case "dial":
                actClass = "fa fa-phone";
                actColor = "btn btn-primary btn-lg";
                actTitle = "<?=$lh->translationFor('dial_next_call')?>";
                break;
            case "hangup":
                actClass = "fa fa-stop";
                actColor = "btn btn-danger btn-lg";
                actTitle = "<?=$lh->translationFor('hangup_call')?>";
                break;
            case "resume":
                actClass = "fa fa-play";
                actTitle = "<?=$lh->translationFor('resume_dialing')?>";
                break;
            case "pause":
                actClass = "fa fa-pause";
                actTitle = "<?=$lh->translationFor('pause_dialing')?>";
                break;
            case "park":
                actColor = "btn btn-warning btn-lg";
                actTitle = "<?=$lh->translationFor('park_call')?>";
                break;
            case "grab":
                actColor = "btn btn-danger btn-lg";
                actTitle = "<?=$lh->translationFor('grab_park_call')?>";
                break;
            case "parkivr":
                actColor = "btn btn-warning btn-lg";
                actTitle = "<?=$lh->translationFor('ivr_park_call')?>";
                break;
            case "grabivr":
                actColor = "btn btn-danger btn-lg";
                actTitle = "<?=$lh->translationFor('grab_ivr_park_call')?>";
                break;
            case "xferon":
                actClass = "";
                onClick = "btn"+taskname+"('ON');";
                break;
            case "xferoff":
                actClass = "";
                onClick = "btn"+taskname+"('OFF', 'YES');";
                break;
            case "start":
                actClass = "";
                onClick = "btn"+taskname+"('STOP');";
                actTitle = "<?=$lh->translationFor('stop_recording')?>";
                break;
            case "stop":
                actClass = "";
                onClick = "btn"+taskname+"('START');";
                actTitle = "<?=$lh->translationFor('start_recording')?>";
                break;
            case "on":
                actClass = "";
                isEnabled = true;
                break;
            case "off":
                actClass = "";
                isEnabled = false;
                break;
            case "hide":
                actClass = "";
                isHidden = true;
                break;
            case "show":
                actClass = "";
                isHidden = false;
                break;
            default:
                actClass = "";
        }
        
        if (actColor.length > 0) {
            $("#btn"+taskname).prop('class', actColor);
        }
        
        if (actClass.length > 0) {
            if (!isEnabled) {
                $("#btn"+taskname).addClass('disabled');
            } else {
                $("#btn"+taskname).removeClass('disabled');
            }
            
            $("#btn"+taskname+" i").attr('class', actClass);
        } else {
            if (!isEnabled) {
                $("#btn"+taskname).addClass('disabled');
            } else {
                $("#btn"+taskname).removeClass('disabled');
            }
            
            if (onClick.length > 0) {
                $("#btn"+taskname).attr('onclick', onClick);
            }
        }
        
        if (actTitle !== '') {
            $("#btn"+taskname).attr('title', actTitle);
        }
        
        if (isHidden) {
            $("#btn"+taskname).addClass('hidden');
        } else {
            $("#btn"+taskname).removeClass('hidden');
        }

	<?php
		//ECCS Customization
		if( ECCS_BLIND_MODE === "y" ){	
	?>
	if(taskname === "DialHangup" ){
		if(taskaction.toLowerCase() ==  "dial"){
                	$("#hash-dial-hangup").html('<span class="sr-only">Dial Next</span>#DN');
		}else{
        	        $("#hash-dial-hangup").html('<span class="sr-only">Hang Up</span>#HU');
	        }
	}
	<?php
		}
		// /. ECCS Customization
	?>
    } else {
        var returnVal = ($("#btn"+taskname).hasClass('disabled')) ? false : true;
        return returnVal;
    }
}

function toggleButtons (taskaction, taskivr, taskrequeue, taskquickxfer) {
    if (typeof taskaction !== 'undefined' && taskaction.length > 0) {
        var btnIVR = 'hide';
        if (taskivr == 'ENABLED' || taskivr == 'ENABLED_PARK_ONLY') {
            btnIVR = 'off';
        }
        var btnRequeue = (taskrequeue > 0) ? 'off' : 'hide';
        var btnQuickXFER = (taskquickxfer > 0) ? 'off' : 'hide';
        
        switch (taskaction.toLowerCase()) {
            case "manual":
                toggleButton('DialHangup', 'dial');
                toggleButton('ResumePause', 'hide');
                break;
            case "inbound_man":
                toggleButton('DialHangup', 'dial');
                toggleButton('ResumePause', 'on');
                break;
            default:
                toggleButton('DialHangup', 'dial', false);
                toggleButton('ResumePause', 'on');
        }
        
        //console.log("btnIVR = "+btnIVR+"; btnRequeue = "+btnRequeue);
        toggleButton('TransferCall', 'off');
        toggleButton('ParkCall', 'off');
        toggleButton('IVRParkCall', btnIVR);
        toggleButton('ReQueueCall', btnRequeue);
        toggleButton('QuickTransfer', btnQuickXFER);
        
        if (btnIVR == 'hide' && btnRequeue == 'hide') {
            $("#btn_spacer").addClass('hidden');
        } else {
            $("#btn_spacer").removeClass('hidden');
        }
    }
}

function updateButtons () {
    if (is_logged_in && ((use_webrtc && phoneRegistered) || !use_webrtc)) {
        $("#go_nav_btn").removeClass('hidden');
        $("#go_agent_login").addClass('hidden');
        $("#go_agent_logout").removeClass('hidden');
        $("#go_agent_status").removeClass('hidden');
        $("#go_agent_manualdial").removeClass('hidden');
        $("#AgentDialPad").removeClass('hidden');
        $("#go_agent_other_buttons").removeClass('hidden');
        if (agent_status_view > 0) {
            $("#agents-tab").removeClass('hidden');
        } else {
            $("#agents-tab").addClass('hidden');
        }
    } else {
        $("#go_nav_btn").addClass('hidden');
        $("#go_agent_login").removeClass('hidden');
        $("#go_agent_logout").addClass('hidden');
        $("#go_agent_status").addClass('hidden');
        $("#go_agent_manualdial").addClass('hidden');
        $("#AgentDialPad").addClass('hidden');
        $("#go_agent_other_buttons").addClass('hidden');
        $("#agents-tab").addClass('hidden');
    }
}

function toggleStatus (status) {
    var statusClass = '';
    var statusLabel = '';
    switch (status) {
        case "DEAD":
            statusClass = 'deadcall';
            statusLabel = '<?=$lh->translationFor('dead_call')?>';
            break;
        case "LIVE":
            statusClass = 'livecall';
            statusLabel = '<?=$lh->translationFor('live_call')?>';
            break;
        case "HANGUP":
            statusClass = 'callhangup';
            statusLabel = '<?=$lh->translationFor('call_hangup')?>';
            break;
        default:
            statusClass = 'nolivecall';
            statusLabel = '<?=$lh->translationFor('no_live_call')?>';
    }

    $("#livecall h3").attr({'class': statusClass, 'title': statusLabel}).html(statusLabel);
}

function checkIfStillLoggedIn(logged_out, last_call) {
    if (logged_out && ((use_webrtc && phoneRegistered) || !use_webrtc)) {
        var checkLastCall = (typeof last_call !== 'undefined') ? last_call : 0;
        check_last_call = 0;
        
        var postData = {
            goAction: 'goCheckIfLoggedIn',
            goUser: uName,
            goPass: uPass,
            goPhone: phone_login,
            goPhonePass: phone_pass,
            goCheckLastCall: checkLastCall,
            responsetype: 'json'
        };
    
        $.ajax({
            type: 'POST',
            url: '<?=$goAPI?>/goAgent/goAPI.php',
            processData: true,
            data: postData,
            dataType: "json",
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            }
        })
        .done(function (result) {
            if (result.result == 'success') {
                if (!result.logged_in) {
                    if (alertLogout) {
                        sendLogout(true);
                        swal({
                            title: "<?=$lh->translationFor('logged_out')?>",
                            text: result.message,
                            type: 'warning'
                        });
                    }
                }
                return;
            } else {
                swal(result.message);
            }
        });
    } else {
        if (!logging_in) {
            var update_login = ((use_webrtc && phoneRegistered) || (!use_webrtc && is_logged_in)) ? 1 : 0;
            if (check_login && window_focus) {
                check_login = false;
                $.post("<?=$module_dir?>GOagentJS.php", {'module_name': 'GOagent', 'action': 'ChecKLogiN', 'is_logged_in': update_login}, function(result) {
                    is_logged_in = parseInt(result);
                });
            } else {
                check_login = true;
            }
        }
    }
}

function CheckForConfCalls (confnum, force) {
    if (confnum == '') {
        confnum = conf_exten;
    }
    
    custchannellive--;
    if ( (agentcallsstatus == '1') || (callholdstatus == '1') ) {
        campagentstatct++;
        if (campagentstatct > campagentstatctmax) {
            campagentstatct = 0;
            var campagentstdisp = 'YES';
        } else {
            var campagentstdisp = 'NO';
        }
    } else {
        var campagentstdisp = 'NO';
    }

    var postData = {
        goAction: 'goCheckConference',
        goUser: uName,
        goPass: uPass,
        goSessionName: session_name,
        goClient: "vdc",
        goConfExten: confnum,
        goAutoDialLevel: auto_dial_level,
        goCampAgentDisp: campagentstdisp,
        responsetype: 'json'
    };

    $.ajax({
        type: 'POST',
        url: '<?=$goAPI?>/goAgent/goAPI.php',
        processData: true,
        data: postData,
        dataType: "json",
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
        }
    })
    .done(function (result) {
        if (result.result == 'success') {
            var LMAforce = force;
            var confArray = result.data.conf_output;
                UnixTime = confArray.unixtime;
                UnixTime = parseInt(UnixTime);
                UnixTimeMS = (UnixTime * 1000);
                t.setTime(UnixTimeMS);
            if ( (callholdstatus == '1') || (agentcallsstatus == '1') || (vicidial_agent_disable != 'NOT_ACTIVE') ) {
                var AGLogin = confArray.logged_in;
                var CampCalls = confArray.camp_calls;
                var DialCalls = confArray.dial_calls;
                if (AGLogin != 'N') {
                    $("#AgentStatusStatus").html(AGLogin);
                }
                if (CampCalls != 'N') {
                    $("#AgentStatusCalls").html(CampCalls);
                }
                if (DialCalls != 'N') {
                    $("#AgentStatusDials").html(DialCalls);
                }
                if ( (AGLogin == 'DEAD_VLA') && ( (vicidial_agent_disable == 'LIVE_AGENT') || (vicidial_agent_disable == 'ALL') ) ) {
                    //showDiv('AgenTDisablEBoX');
                    refresh_interval = 7300000;
                }
                if ( (AGLogin == 'DEAD_EXTERNAL') && ( (vicidial_agent_disable == 'EXTERNAL') || (vicidial_agent_disable == 'ALL') ) ) {
                    //showDiv('AgenTDisablEBoX');
                    refresh_interval = 7300000;
                }
                if ( (AGLogin == 'TIME_SYNC') && (vicidial_agent_disable == 'ALL') ) {
                    //showDiv('SysteMDisablEBoX');
                }
                if (AGLogin == 'SHIFT_LOGOUT') {
                    shift_logout_flag = 1;
                }
                if (AGLogin == 'API_LOGOUT') {
                    api_logout_flag = 1;
                    //if ( (MD_channel_look < 1) && (live_customer_call < 1) && (alt_dial_status_display < 1) )
                    //    {LogouT('API');}
                }
            }
            
            var VLAStatus = confArray.status;
            if ( (VLAStatus == 'PAUSED') && (AutoDialWaiting == 1) ) {
                if (PauseNotifyCounter > 10) {
                    swal('<?=$lh->translationFor('session_paused')?>');
                    AutoDial_Resume_Pause('VDADpause');
                    PauseNotifyCounter = 0;
                } else {
                    PauseNotifyCounter++;
                }
            } else {
                PauseNotifyCounter = 0;
            }
            
            var APIhangup = confArray.api_hangup;
            var APIstatus = confArray.api_status;
            var APIpause = confArray.api_pause;
            var APIdial = confArray.api_dial;
                APIManualDialQueue = confArray.api_manual_dial_queue;
            var CheckDEADcall = confArray.dead_call;
            var InGroupChange_array = confArray.ingroup_change.split("|");
            var InGroupChange = InGroupChange_array[0];
            var InGroupChangeBlend = InGroupChange_array[1];
            var InGroupChangeUser = InGroupChange_array[2];
            var InGroupChangeName = InGroupChange_array[3];
                update_fields = confArray.api_fields;
                update_fields_data = confArray.api_fields_data;
                api_timer_action = confArray.api_timer_action;
                api_timer_action_message = confArray.api_timer_message;
                api_timer_action_seconds = confArray.api_timer_seconds;
                api_timer_action_destination = confArray.api_timer_destination;
            var api_recording = confArray.api_recording;
                api_dtmf = confArray.api_dtmf;
            if (confArray.api_transferconf.length > 0) {
                var api_transferconf_values_array = confArray.api_transferconf.split("---");
                    api_transferconf_function = api_transferconf_values_array[0];
                    api_transferconf_group = api_transferconf_values_array[1];
                    api_transferconf_number = api_transferconf_values_array[2];
                    api_transferconf_consultative = api_transferconf_values_array[3];
                    api_transferconf_override = api_transferconf_values_array[4];
                    api_transferconf_group_alias = api_transferconf_values_array[5];
                    api_transferconf_cid_number = api_transferconf_values_array[6];
            }
                api_parkcustomer = confArray.api_park;
                
            if (api_recording=='START') {
                ConfSendRecording('MonitorConf', session_id,'','1');
                //sendToAPI('recording', 'START');
            }
            if (api_recording=='STOP') {
                ConfSendRecording('StopMonitorConf', session_id, recording_filename, '1');
                //sendToAPI('recording', 'STOP');
            }
            if (api_transferconf_function.length > 0) {
                if (api_transferconf_function == 'HANGUP_XFER')
                    {XFerCallHangup();}
                if (api_transferconf_function == 'HANGUP_BOTH')
                    {BothCallHangup();}
                if (api_transferconf_function == 'LEAVE_VM')
                    {mainxfer_send_redirect('XfeRVMAIL', lastcustchannel, lastcustserverip);}
                if (api_transferconf_function == 'LEAVE_3WAY_CALL')
                    {leave_3way_call('FIRST');}
                if (api_transferconf_function == 'BLIND_TRANSFER') {
                    $(".formXFER input[name='xfernumber']").val(api_transferconf_number);
                    mainxfer_send_redirect('XfeRBLIND', lastcustchannel, lastcustserverip);
                }
                if (external_transferconf_count < 1) {
                    if (api_transferconf_function == 'LOCAL_CLOSER') {
                        API_selected_xfergroup = api_transferconf_group;
                        $(".formXFER input[name='xfernumber']").val(api_transferconf_number);
                        mainxfer_send_redirect('XfeRLOCAL', lastcustchannel, lastcustserverip);
                    }
                    if (api_transferconf_function == 'DIAL_WITH_CUSTOMER') {
                        if (api_transferconf_consultative == 'YES')
                            {$(".formXFER input[name='consultativexfer']").prop('checked', true);}
                        if (api_transferconf_consultative == 'NO')
                            {$(".formXFER input[name='consultativexfer']").prop('checked', false);}
                        if (api_transferconf_override == 'YES')
                            {$(".formXFER input[name='xferoverride']").prop('checked', true);}
                        API_selected_xfergroup = api_transferconf_group;
                        $(".formXFER input[name='xfernumber']").val(api_transferconf_number);
                        active_group_alias = api_transferconf_group_alias;
                        cid_choice = api_transferconf_cid_number;
                        SendManualDial('YES');
                    }
                    if (api_transferconf_function == 'PARK_CUSTOMER_DIAL') {
                        if (api_transferconf_consultative == 'YES')
                            {$(".formXFER input[name='consultativexfer']").prop('checked', true);}
                        if (api_transferconf_consultative == 'NO')
                            {$(".formXFER input[name='consultativexfer']").prop('checked', false);}
                        if (api_transferconf_override == 'YES')
                            {$(".formXFER input[name='xferoverride']").prop('checked', true);}
                        API_selected_xfergroup = api_transferconf_group;
                        $(".formXFER input[name='xfernumber']").val(api_transferconf_number);
                        active_group_alias = api_transferconf_group_alias;
                        cid_choice = api_transferconf_cid_number;
                        xfer_park_dial();
                    }
                    external_transferconf_count = 3;
                }
                Clear_API_Field('external_transferconf');
            }
            if (api_parkcustomer == 'PARK_CUSTOMER') {
                toggleButton('ParkCall', 'park');
                mainxfer_send_redirect('ParK', lastcustchannel, lastcustserverip);
            }
            if (api_parkcustomer == 'GRAB_CUSTOMER') {
                toggleButton('ParkCall', 'grab');
                mainxfer_send_redirect('FROMParK', lastcustchannel, lastcustserverip);
            }
            if (api_parkcustomer == 'PARK_IVR_CUSTOMER') {
                toggleButton('IVRParkCall', 'parkivr');
                mainxfer_send_redirect('ParKivr', lastcustchannel, lastcustserverip);
            }
            if (api_parkcustomer == 'GRAB_IVR_CUSTOMER') {
                toggleButton('IVRParkCall', 'grabivr');
                mainxfer_send_redirect('FROMParKivr', lastcustchannel, lastcustserverip);
            }
            if (api_dtmf.length > 0) {
                var REGdtmfPOUND = new RegExp("P","g");
                var REGdtmfSTAR = new RegExp("S","g");
                var REGdtmfQUIET = new RegExp("Q","g");
                api_dtmf = api_dtmf.replace(REGdtmfPOUND, '#');
                api_dtmf = api_dtmf.replace(REGdtmfSTAR, '*');
                api_dtmf = api_dtmf.replace(REGdtmfQUIET, ',');
                $("#conf_dtmf").val(api_dtmf);
                //SendConfDTMF(session_id);
            }
    
            if (api_timer_action.length > 2) {
                timer_action = api_timer_action;
                timer_action_message = api_timer_action_message;
                timer_action_seconds = api_timer_action_seconds;
                timer_action_destination = api_timer_action_destination;
                //alert("TIMER_API:" + timer_action + '|' + timer_action_message + '|' + timer_action_seconds + '|' + timer_action_destination + '|');
            }
            
            //API catcher for hanging up calls
            if (APIhangup == 1 && (live_customer_call == 1 || MD_channel_look == 1)) {
                WaitingForNextStep = 0;
                custchannellive = 0;
                
                DialedCallHangup();
            }
            
            //API catcher for Call Dispositions
            if ( (APIstatus.length < 1000) && (APIstatus.length > 0) && (AgentDispoing > 1) && (APIstatus != '::::::::::') ) {
                var regCBmatch = new RegExp('!',"g");
                if (APIstatus.match(regCBmatch)) {
                    var APIcbSTATUS_array = APIstatus.split("!");
                    var APIcbSTATUS =	APIcbSTATUS_array[0];
                    var APIcbDATETIME =	APIcbSTATUS_array[1];
                    var APIcbTYPE =		APIcbSTATUS_array[2];
                    var APIcbCOMMENTS =	APIcbSTATUS_array[3];
                    var APIqmCScode =	APIcbSTATUS_array[4];
    
                    if ( (APIcbDATETIME.length > 10) && (APIcbTYPE.length > 5) ) {
                        CallBackDateTime =   APIcbDATETIME;
                        CallBackRecipient =  APIcbTYPE;
                        CallBackLeadStatus = APIcbSTATUS;
                        CallBackComments =   APIcbCOMMENTS;
                        $("#DispoSelection").val('CBHOLD');
                    } else {
                        $("#DispoSelection").val(APIcbSTATUS);
                    }
                    
                    if (APIqmCScode.length > 0) {
                        DispoQMcsCODE = APIqmCScode;
                    }
    
                    DispoSelectSubmit();
                } else {
                    $("#DispoSelection").val(APIstatus);
                    DispoSelectSubmit();
                }
            }
    
            if (APIpause.length > 4) {
                var APIpause_array = APIpause.split("!");
                if (APIpause_ID != APIpause_array[1]) {
                    APIpause_ID = APIpause_array[1];
                    if (APIpause_array[0] == 'PAUSE') {
                        if (live_customer_call == 1) {
                            // set to pause on next dispo
                            $("#DispoSelectStop").prop('checked', true);
                            DispoSelectStop = true;
                            //console.log("Setting agent status to PAUSE on next dispo");
                        } else {
                            if (AutoDialReady == 1) {
                                if (auto_dial_level != '0') {
                                    AutoDialWaiting = 0;
                                    AutoDial_Resume_Pause("VDADpause");
                                }
                                pause_calling = 1;
                            }
                        }
                    }
                    
                    if ( (APIpause_array[0] == 'RESUME') && (AutoDialReady < 1) && (auto_dial_level > 0) ) {
                        AutoDialWaiting = 1;
                        AutoDial_Resume_Pause("VDADready");
                        //console.log("Setting agent status to RESUME");
                    }
                }
            }
            
            //API catcher for Manual Dial
            if (APIdial.length > 9 && AllowManualQueueCalls == '0') {
                APIManualDialQueue++;
            }
            if (APIManualDialQueue != APIManualDialQueue_last) {
                APIManualDialQueue_last = APIManualDialQueue;
                //console.info('Manual Queue: '+APIManualDialQueue);
            }
            
            if (APIdial.length > 9 && WaitingForNextStep == '0' && AllowManualQueueCalls == '1' && check_r > 2) {
                var APIdial_array_detail = APIdial.split("!");
                if (APIdial_ID != APIdial_array_detail[6]) {
                    APIdial_ID = APIdial_array_detail[6];
                    $('#inputphone_code').val(APIdial_array_detail[1]);
                    $('#cust-phone-number').val(APIdial_array_detail[0]);
                    $('#inputvendor_lead_code').val(APIdial_array_detail[5]);
                    prefix_choice = APIdial_array_detail[7];
                    active_group_alias = APIdial_array_detail[8];
                    cid_choice = APIdial_array_detail[9];
                    vtiger_callback_id = APIdial_array_detail[10];
                    $("input[name='lead_id']").val(APIdial_array_detail[11]);
                    $("input[name='uniqueid']").val(APIdial_array_detail[12]);
                    
                    if (active_group_alias.length > 1)
                        {var sending_group_alias = 1;}
                    
                    //console.log('Dialing '+APIdial_array_detail[0]+'...');
                    if (APIdial_array_detail[2] == 'YES')  // lookup lead in system
                        {$("#LeadLookUP").prop('checked', true);}
                    else
                        {$("#LeadLookUP").prop('checked', false);}
                    if (APIdial_array_detail[4] == 'YES') { // focus on agent screen
                        window.focus();
                        swal({
                            title: "<?=$lh->translationFor('placing_call_to')?>",
                            text: APIdial_array_detail[1] + " " + APIdial_array_detail[0]
                        });
                    }
                    if (APIdial_array_detail[3] == 'YES')  // call preview
                        {NewManualDialCall('PREVIEW');}
                    else
                        {NewManualDialCall('NOW');}
                }
            }

            if ( (CheckDEADcall > 0) && (live_customer_call == 1) ) {
                if (CheckDEADcallON < 1) {
                    toggleStatus('DEAD');
                    toggleButton('ParkCall', 'grab', false);
                    toggleButton('TransferCall', 'off');
                    CheckDEADcallON = 1;

                    if ( (xfer_in_call > 0) && (customer_3way_hangup_logging=='ENABLED') ) {
                        customer_3way_hangup_counter_trigger = 1;
                        customer_3way_hangup_counter = 1;
                    }
                }
            }
            
            if (InGroupChange > 0) {
                var external_blended = InGroupChangeBlend;
                var external_igb_set_user = InGroupChangeUser;
                external_igb_set_name = InGroupChangeName;
                manager_ingroups_set = 1;
    
                if ( (external_blended == '1') && (dial_method != 'INBOUND_MAN') )
                    {closer_blended = 1;}
    
                if (external_blended == '0')
                    {closer_blended = 0;}
            }
            
            var live_conf_calls = result.data.channels_list;
            var conf_chan_array = result.data.count_echo.split(" ~");
            if ( (conf_channels_xtra_display == 1) || (conf_channels_xtra_display == 0) ) {
                if (live_conf_calls > 0) {
                    loop_ct = 0;
                    var temp_blind_monitors = 0;
                    var ARY_ct = 0;
                    var LMAalter = 0;
                    var LMAcontent_change = 0;
                    var LMAcontent_match = 0;
                    agentphonelive = 0;
                    var conv_start = -1;
                    var live_conf_HTML = '<font face="Arial,Helvetica"><b><?=$lh->translationFor('live_calls_in_your_session')?>:</b></font><br /><table width="340px"><tr><td><font class="log_title">#</font></td><td><font class="log_title"><?=$lh->translationFor('remote_channel')?></font></td><td><font class="log_title"><?=$lh->translationFor('hangup')?></font></td></tr>';
                    if ( (LMAcount > live_conf_calls)  || (LMAcount < live_conf_calls) || (LMAforce > 0)) {
                        LMAe[0] = '';
                        LMAe[1] = '';
                        LMAe[2] = '';
                        LMAe[3] = '';
                        LMAe[4] = '';
                        LMAe[5] = ''; 
                        LMAcount = 0;
                        LMAcontent_change++;
                    }
    
                    while (loop_ct < live_conf_calls) {
                        loop_ct++;
                        loop_s = loop_ct.toString();
                        if (loop_s.match(/1$|3$|5$|7$|9$/)) 
                            {var row_color = '#DDDDFF';}
                        else
                            {var row_color = '#CCCCFF';}
                        var conv_ct = (loop_ct + conv_start);
                        var channelfieldA = conf_chan_array[conv_ct];
                        var regXFcred = new RegExp(flag_string,"g");
                        var regRNnolink = new RegExp('Local/5' + confnum,"g")
                        if ( (channelfieldA.match(regXFcred)) && (flag_channels>0) ) {
                            var chan_name_color = 'log_text_red';
                        } else {
                            var chan_name_color = 'log_text';
                        }
                        if ( (HideMonitorSessions==1) && (channelfieldA.match(/ASTblind/)) ) {
                            var hide_channel=1;
                            blind_monitoring_now++;
                            temp_blind_monitors++;
                            if (blind_monitoring_now == 1)
                                {blind_monitoring_now_trigger = 1;}
                        } else {
                            if (channelfieldA.match(regRNnolink)) {
                                // do not show hangup or volume control links for recording channels
                                live_conf_HTML = live_conf_HTML + '<tr bgcolor="' + row_color + '"><td><font class="log_text">' + loop_ct + '</font></td><td><font class="' + chan_name_color + '">' + channelfieldA + '</font></td><td><font class="log_text"><?=$lh->translationFor('recording')?></font></td></tr>';
                            } else {
                                if (volumecontrol_active!=1) {
                                    live_conf_HTML = live_conf_HTML + '<tr bgcolor="' + row_color + '"><td><font class="log_text">' + loop_ct + '</font></td><td><font class="' + chan_name_color + '">' + channelfieldA + '</font></td><td><font class="log_text"><a href="#" onclick="livehangup_send_hangup(\"' + channelfieldA + '\");return false;"><?=$lh->translationFor('hangup')?></a></font></td></tr>';
                                } else {
                                    live_conf_HTML = live_conf_HTML + '<tr bgcolor="' + row_color + '"><td><font class="log_text">' + loop_ct + '</font></td><td><font class="' + chan_name_color + '">' + channelfieldA + '</font></td><td><font class="log_text"><a href="#" onclick="livehangup_send_hangup(\"' + channelfieldA + '\");return false;"><?=$lh->translationFor('hangup')?></a></font></td><td><a href="#" onclick="VolumeControl(\"UP\",\"' + channelfieldA + '\",\"\");return false;"><img src="./images/vdc_volume_up.gif" border="0" /></a> &nbsp; <a href="#" onclick="VolumeControl(\"DOWN\",\"' + channelfieldA + '\",\"\");return false;"><img src="./images/vdc_volume_down.gif" border="0" /></a> &nbsp; &nbsp; &nbsp; <a href="#" onclick="VolumeControl(\"MUTING\",\"' + channelfieldA + '\",\"\");return false;"><img src="./images/vdc_volume_MUTE.gif" border="0" /></a> &nbsp; <a href="#" onclick="VolumeControl(\"UNMUTE\",\"' + channelfieldA + '\",\"\");return false;"><img src="./images/vdc_volume_UNMUTE.gif" border="0" /></a></td></tr>';
                                }
                            }
                        }
                        //var debugspan = document.getElementById("debugbottomspan").innerHTML;
    
                        if (channelfieldA == lastcustchannel) {custchannellive++;}
                        else {
                            if(customerparked == 1)
                                {custchannellive++;}
                            // allow for no customer hungup errors if call from another server
                            if(server_ip == lastcustserverip)
                                {var nothing='';}
                            else
                                {custchannellive++;}
                        }
    
                        if (volumecontrol_active > 0) {
                            if ( (protocol != 'EXTERNAL') && (protocol != 'Local') ) {
                                var regAGNTchan = new RegExp(protocol + '/' + extension,"g");
                                if  ( (channelfieldA.match(regAGNTchan)) && (agentchannel != channelfieldA) ) {
                                    agentchannel = channelfieldA;
    
                                    //$("#AgentMuteSpan").html("<a href='#CHAN-" + agentchannel + "' onclick='VolumeControl(\"MUTING\",\"" + agentchannel + "\",\"AgenT\");return false;'><img src='./images/vdc_volume_MUTE.gif' border='0' /></a>");
                                }
                            } else {
                                if (agentchannel.length < 3) {
                                    agentchannel = channelfieldA;
    
                                    //$("#AgentMuteSpan").html("<a href='#CHAN-" + agentchannel + "' onclick='VolumeControl(\"MUTING\",\"" + agentchannel + "\",\"AgenT\");return false;'><img src='./images/vdc_volume_MUTE.gif' border='0' /></a>");
                                }
                            }
                            //document.getElementById("agentchannelSPAN").innerHTML = agentchannel;
                        }
    
                        //document.getElementById("debugbottomspan").innerHTML = debugspan + '<br />' + channelfieldA + '|' + lastcustchannel + '|' + custchannellive + '|' + LMAcontent_change + '|' + LMAalter;
    
                        if (!LMAe[ARY_ct]) {
                            LMAe[ARY_ct] = channelfieldA;
                            LMAcontent_change++;
                            LMAalter++;
                        } else {
                            if (LMAe[ARY_ct].length < 1) {
                                LMAe[ARY_ct] = channelfieldA;
                                LMAcontent_change++;
                                LMAalter++;
                            } else {
                                if (LMAe[ARY_ct] == channelfieldA) {LMAcontent_match++;}
                                else {
                                    LMAcontent_change++;
                                    LMAe[ARY_ct] = channelfieldA;
                                }
                            }
                        }
                        if (LMAalter > 0) {LMAcount++;}
                            
                        if (agentchannel == channelfieldA) {agentphonelive++;}
    
                        ARY_ct++;
                    }
                    //var debug_LMA = LMAcontent_match+"|"+LMAcontent_change+"|"+LMAcount+"|"+live_conf_calls+"|"+LMAe[0]+LMAe[1]+LMAe[2]+LMAe[3]+LMAe[4]+LMAe[5];
                    //$("#confdebug").html(debug_LMA + "<br />");
    
                    if (agentphonelive < 1) {agentchannel = '';}
    
                    live_conf_HTML = live_conf_HTML + "</table>";
    
                    if (LMAcontent_change > 0) {
                        if (conf_channels_xtra_display == 1)
                            {$("#outboundcallsspan").html(live_conf_HTML);}
                    }
                    nochannelinsession = 0;
                    if (temp_blind_monitors < 1) {
                        no_blind_monitors++;
                        if (no_blind_monitors > 2)
                            {blind_monitoring_now = 0;}
                    }
                } else {
                    LMAe[0]='';
                    LMAe[1]='';
                    LMAe[2]='';
                    LMAe[3]='';
                    LMAe[4]='';
                    LMAe[5]='';
                    LMAcount=0;
                    if (conf_channels_xtra_display == 1) {
                        if ($("#outboundcallsspan").html().length > 2) {
                            $("#outboundcallsspan").html('');
                        }
                    }
                    custchannellive = -99;
                    nochannelinsession++;
    
                    no_blind_monitors++;
                    if (no_blind_monitors > 2)
                        {blind_monitoring_now = 0;}
                }
            }
            
            //$('#debug').html('<b>DEBUG:</b> ' + result);
        } else {
            swal({
                title: '<?=$lh->translationFor('error')?>',
                text: result.message,
                type: 'error'
            });
        }
    });
}

function CheckForIncoming () {
    all_record = 'NO';
    all_record_count = 0;

    var postData = {
        goAction: 'goVDADCheckIncoming',
        goServerIP: server_ip,
        goSessionName: session_name,
        goUser: uName,
        goPass: uPass,
        goCampaign: campaign,
        goAgentLogID: agent_log_id,
        responsetype: 'json'
    };

    $.ajax({
        type: 'POST',
        url: '<?=$goAPI?>/goAgent/goAPI.php',
        processData: true,
        data: postData,
        dataType: "json",
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
        }
    })
    .done(function (result) {
        if (live_customer_call == 1) {
            //console.log(result);
            forTestingOnly = '';
        }

        var this_VDIC_data = result.data;
        has_inbound_call = this_VDIC_data.has_call;
        if (this_VDIC_data.has_call == '1') {
            AutoDialWaiting = 0;
            QUEUEpadding = 0;
            
            VDIC_web_form_address = web_form_address
            VDIC_web_form_address_two = web_form_address_two
            var VDIC_fronter = '';
            
            if (this_VDIC_data.group_web.length > 5)
                {VDIC_web_form_address = this_VDIC_data.group_web;}
            var VDCL_group_name                         = this_VDIC_data.group_name;
            var VDCL_group_color                        = this_VDIC_data.group_color;
            var VDCL_fronter_display                    = this_VDIC_data.fronter_display;
                VDCL_group_id                           = this_VDIC_data.channel_group;
                Call_Script_ID                          = this_VDIC_data.ingroup_script;
                Call_Auto_Launch                        = this_VDIC_data.get_call_launch;
                Call_XC_a_DTMF                          = this_VDIC_data.xferconf_a_dtmf;
                Call_XC_a_Number                        = this_VDIC_data.xferconf_a_number;
                Call_XC_b_DTMF                          = this_VDIC_data.xferconf_b_dtmf;
                Call_XC_b_Number                        = this_VDIC_data.xferconf_b_number;
            if ( (this_VDIC_data.default_xfer_group.length > 1) && (this_VDIC_data.default_xfer_group != '---NONE---') )
                {LIVE_default_xfer_group = this_VDIC_data.default_xfer_group;}
            else
                {LIVE_default_xfer_group = default_xfer_group;}

            if ( (this_VDIC_data.ingroup_recording_override.length > 1) && (this_VDIC_data.ingroup_recording_override != 'DISABLED') )
                {LIVE_campaign_recording = this_VDIC_data.ingroup_recording_override;}
            else
                {LIVE_campaign_recording = campaign_recording;}

            if ( (this_VDIC_data.ingroup_rec_filename.length > 1) && (this_VDIC_data.ingroup_rec_filename != 'NONE') )
                {LIVE_campaign_rec_filename = this_VDIC_data.ingroup_rec_filename;}
            else
                {LIVE_campaign_rec_filename = campaign_rec_filename;}

            if ( (this_VDIC_data.default_group_alias.length > 1) && (this_VDIC_data.default_group_alias != 'NONE') )
                {LIVE_default_group_alias = this_VDIC_data.default_group_alias;}
            else
                {LIVE_default_group_alias = default_group_alias;}

            if ( (this_VDIC_data.caller_id_number.length > 1) && (this_VDIC_data.caller_id_number != 'NONE') )
                {LIVE_caller_id_number = this_VDIC_data.caller_id_number;}
            else
                {LIVE_caller_id_number = default_group_alias_cid;}

            if (this_VDIC_data.group_web_vars.length > 0)
                {LIVE_web_vars = this_VDIC_data.group_web_vars;}
            else
                {LIVE_web_vars = default_web_vars;}

            if (this_VDIC_data.group_web_two != null) {
                if (this_VDIC_data.group_web_two.length > 5)
                    {VDIC_web_form_address_two = this_VDIC_data.group_web_two;}
            }

            var call_timer_action                       = this_VDIC_data.timer_action;

            if ( (call_timer_action == 'NONE') || (call_timer_action.length < 2) ) {
                timer_action = campaign_timer_action;
                timer_action_message = campaign_timer_action_message;
                timer_action_seconds = campaign_timer_action_seconds;
                timer_action_destination = campaign_timer_action_destination;
            } else {
                var call_timer_action_message           = this_VDIC_data.timer_action_message;
                var call_timer_action_seconds           = this_VDIC_data.timer_action_seconds;
                var call_timer_action_destination       = this_VDIC_data.timer_action_destination;
                timer_action = call_timer_action;
                timer_action_message = call_timer_action_message;
                timer_action_seconds = call_timer_action_seconds;
                timer_action_destination = call_timer_action_destination;
            }

            Call_XC_c_Number                            = this_VDIC_data.xferconf_c_number;
            Call_XC_d_Number                            = this_VDIC_data.xferconf_d_number;
            Call_XC_e_Number                            = this_VDIC_data.xferconf_e_number;
            uniqueid_status_display                     = this_VDIC_data.uniqueid_status_display;
            uniqueid_status_prefix                      = this_VDIC_data.uniqueid_status_prefix;
            did_id                                      = this_VDIC_data.did_id;
            did_extension                               = this_VDIC_data.did_extension;
            did_pattern                                 = this_VDIC_data.did_pattern;
            did_description                             = this_VDIC_data.did_description;
            closecallid                                 = this_VDIC_data.closecallid;
            xfercallid                                  = this_VDIC_data.xfercallid;

            if (this_VDIC_data.fronter_full_name != null) {
                if ( (this_VDIC_data.fronter_full_name.length > 1) && (VDCL_fronter_display == 'Y') )
                    {VDIC_fronter = "  Fronter: " + this_VDIC_data.fronter_full_name + " - " + this_VDIC_data.tsr;}
            }
            
            $(".formMain input[name='lead_id']").val(this_VDIC_data.lead_id);
            $(".formMain input[name='uniqueid']").val(this_VDIC_data.uniqueid);
            CIDcheck                                    = this_VDIC_data.callerid;
            CallCID                                     = this_VDIC_data.callerid;
            LastCallCID                                 = this_VDIC_data.callerid;
            $("#callchannel").html(this_VDIC_data.channel);
            lastcustchannel                             = this_VDIC_data.channel;
            $("#callserverip").val(this_VDIC_data.call_server_ip);
            lastcustserverip                            = this_VDIC_data.call_server_ip;

            toggleStatus('LIVE');

            $(".formMain input[name='seconds']").val(0);
            $("#SecondsDISP").html('0');

            if (uniqueid_status_display=='ENABLED')
                {custom_call_id = " Call ID " + this_VDIC_data.uniqueid;}
            if (uniqueid_status_display=='ENABLED_PREFIX')
                {custom_call_id = " Call ID " + uniqueid_status_prefix + "" + this_VDIC_data.uniqueid;}
            if (uniqueid_status_display=='ENABLED_PRESERVE')
                {custom_call_id = " Call ID " + this_VDIC_data.custom_call_id;}

            live_customer_call = 1;
            live_call_seconds = 0;
            
            activateLinks();

            // INSERT VICIDIAL_LOG ENTRY FOR THIS CALL PROCESS
            //DialLog("start");

            custchannellive = 1;

            LastCID                                     = this_VDIC_data.MqueryCID;
            LeadPrevDispo                               = this_VDIC_data.dispo;
            fronter                                     = this_VDIC_data.tsr;
            $(".formMain input[name='vendor_lead_code']").val(this_VDIC_data.vendor_id);
            $(".formMain input[name='list_id']").val(this_VDIC_data.list_id);
            $(".formMain input[name='gmt_offset_now']").val(this_VDIC_data.gmt_offset_now);
            cust_phone_code                         = this_VDIC_data.phone_code;
            $(".formMain input[name='phone_code']").val(cust_phone_code);
            cust_phone_number                       = this_VDIC_data.phone_number;
            $(".formMain input[name='phone_number']").val(cust_phone_number).trigger('change');
            $(".formMain input[name='title']").val(this_VDIC_data.title).trigger('change');
            if (this_VDIC_data.first_name !== '') {
                $("#cust_full_name a[id='first_name']").editable('setValue', this_VDIC_data.first_name, true);
            }
            if (this_VDIC_data.middle_initial !== '') {
                $("#cust_full_name a[id='middle_initial']").editable('setValue', this_VDIC_data.middle_initial, true);
            }
            if (this_VDIC_data.last_name !== '') {
                $("#cust_full_name a[id='last_name']").editable('setValue', this_VDIC_data.last_name, true);
            }

            $(".formMain input[name='address1']").val(this_VDIC_data.address1).trigger('change');
            $(".formMain input[name='address2']").val(this_VDIC_data.address2).trigger('change');
            $(".formMain input[name='address3']").val(this_VDIC_data.address3).trigger('change');
            $(".formMain input[name='city']").val(this_VDIC_data.city).trigger('change');
            $(".formMain input[name='state']").val(this_VDIC_data.state).trigger('change');
            $(".formMain input[name='province']").val(this_VDIC_data.province).trigger('change');
            $(".formMain input[name='postal_code']").val(this_VDIC_data.postal_code).trigger('change');
            $(".formMain select[name='country_code']").val(this_VDIC_data.country_code).trigger('change');
            $(".formMain select[name='gender']").val(this_VDIC_data.gender).trigger('change');
            var dateOfBirth = this_VDIC_data.date_of_birth;
            $(".formMain input[name='date_of_birth']").val(dateOfBirth);
            $(".formMain input[name='alt_phone']").val(this_VDIC_data.alt_phone).trigger('change');
            $(".formMain input[name='email']").val(this_VDIC_data.email).trigger('change');
            $(".formMain input[name='security_phrase']").val(this_VDIC_data.security);
            var REGcommentsNL = new RegExp("!N!","g");
            var thisComments = this_VDIC_data.comments;
            if (typeof thisComments !== 'undefined') {
                thisComments = thisComments.replace(REGcommentsNL, "\n");
            }
            $(".formMain textarea[name='comments']").val(thisComments).trigger('change');
            $(".formMain input[name='called_count']").val(this_VDIC_data.called_count);
            CBentry_time                                = this_VDIC_data.CBentry_time;
            CBcallback_time                             = this_VDIC_data.CBcallback_time;
            CBuser                                      = this_VDIC_data.CBuser;
            CBcomments                                  = this_VDIC_data.CBcomments;
            dialed_number                               = this_VDIC_data.dialed_number;
            dialed_label                                = this_VDIC_data.dialed_label;
            source_id                                   = this_VDIC_data.source_id;
            EAphone_code                                = this_VDIC_data.alt_phone_code;
            EAphone_number                              = this_VDIC_data.alt_phone_number;
            EAalt_phone_notes                           = this_VDIC_data.alt_phone_note;
            EAalt_phone_active                          = this_VDIC_data.alt_phone_active;
            EAalt_phone_count                           = this_VDIC_data.alt_phone_count;
            $(".formMain input[name='rank']").val(this_VDIC_data.rank);
            $(".formMain input[name='owner']").val(this_VDIC_data.owner);
            $(".formMain textarea[name='call_notes']").val(this_VDIC_data.call_notes);
            script_recording_delay                      = this_VDIC_data.script_recording_delay;
            $(".formMain input[name='entry_list_id']").val(this_VDIC_data.entry_list_id);
            custom_field_names                          = this_VDIC_data.custom_field_names;
            custom_field_values                         = this_VDIC_data.custom_field_values;
            custom_field_types                          = this_VDIC_data.custom_field_types;
            //Added By Poundteam for Audited Comments (Manual Dial Section Only)
            //if (qc_enabled > 0)
            //{
            //    $(".formMain input[name='ViewCommentButton']").val(check_VDIC_array[53]);
            //    $(".formMain input[name='audit_comments_button']").val(check_VDIC_array[53]);
            //    var REGACcomments = new RegExp("!N","g");
            //    check_VDIC_array[54] = check_VDIC_array[54].replace(REGACcomments, "\n");
            //    $(".formMain input[name='audit_comments']").val(check_VDIC_array[54]);
            //}
            //END section Added By Poundteam for Audited Comments
            // Add here for AutoDial (VDADcheckINCOMING in vdc_db_query)

            //if (hide_gender > 0)
            //{
            //    $(".formMain input[name='gender_list']").val(check_VDIC_array[25]);
            //} else {
            //    var gIndex = 0;
            //    if ($(".formMain input[name='gender']").val() == 'M') {var gIndex = 1;}
            //    if ($(".formMain input[name='gender']").val() == 'F') {var gIndex = 2;}
            //    document.getElementById("gender_list").selectedIndex = gIndex;
            //}
            
            if (custom_field_names.length > 1) {
                if (custom_fields_launch == 'ONCALL') {
                    GetCustomFields(this_VDIC_data.list_id, false, true);
                }
                
                var custom_names_array = custom_field_names.split("|");
                var custom_values_array = custom_field_values.split("----------");
                var custom_types_array = custom_field_types.split("|");
                var field_name = ".formMain #custom_fields";
                
                var fieldsPopulated = setInterval(function() {
                    if (getFields) {
                        clearInterval(fieldsPopulated);
                        
                        $.each(custom_names_array, function(idx, field) {
                            if (field.length < 1) return true;
                            
                            switch (custom_types_array[idx]) {
                                case "TEXT":
                                case "AREA":
                                case "HIDDEN":
                                case "DATE":
                                case "TIME":
                                    $(field_name + " [id='custom_" + field + "']").val(custom_values_array[idx]);
                                    break;
                                case "CHECKBOX":
                                case "RADIO":
                                    var checkThis = custom_values_array[idx].split(',');
                                    $.each($(field_name + " [id^='custom_" + field + "']"), function() {
                                        if (checkThis.indexOf($(this).val()) > -1) {
                                            $(this).prop('checked', true);
                                        } else {
                                            $(this).prop('checked', false);
                                        }
                                    });
                                    break;
                                case "SELECT":
                                case "MULTI":
                                    var selectThis = custom_values_array[idx].split(',');
                                    $.each($(field_name  + " [id='custom_" + field + "'] option"), function() {
                                        if (selectThis.indexOf($(this).val()) > -1) {
                                            $(this).prop('selected', true);
                                        } else {
                                            $(this).prop('selected', false);
                                        }
                                    });
                                    break;
                                default:
                                    $(field_name + " [id='custom_" + field + "']").html(custom_values_array[idx]);
                            }
                        });
                        
                        replaceCustomFields();
                        if (custom_fields_launch == 'ONCALL') {
                            GetCustomFields(null, true);
                        }
                    }
                }, 1000);
            }

            lead_dial_number = $(".formMain input[name='phone_number']").val();
            dispnum = $(".formMain input[name='phone_number']").val();
            var status_display_number = phone_number_format(dispnum);
            var callnum = dialed_number;
            var dial_display_number = phone_number_format(callnum);
            
            //$("#cust_full_name").html(this_VDIC_data.first_name+" "+this_VDIC_data.middle_initial+" "+this_VDIC_data.last_name);
            $("#cust_full_name").removeClass('hidden');
            $("#cust_number").html(phone_number_format(dispnum));
    	    <?php if(ECCS_BLIND_MODE === 'y'){ ?> $("span#span-cust-number").removeClass("hidden");  $("#cust_number").val(cust_phone_number); <?php } ?>
            $("#cust_avatar").html(goGetAvatar(this_VDIC_data.first_name+" "+this_VDIC_data.last_name));
            //goAvatar._init(goOptions);

            var status_display_content = '';
            if (status_display_CALLID > 0) {status_display_content = status_display_content + "<br><b><?=$lh->translationFor('uid')?>:</b> " + LastCID;}
            if (status_display_LEADID > 0) {status_display_content = status_display_content + "<br><b><?=$lh->translationFor('lead_id')?>:</b> " + $("#formMain input[name='lead_id'").val();}
            if (status_display_LISTID > 0) {status_display_content = status_display_content + "<br><b><?=$lh->translationFor('list_id')?>:</b> " + $("#formMain input[name='list_id'").val();}

            $("#MainStatusSpan").html("<b><?=$lh->translationFor('incoming_call')?>:</b> " + dial_display_number + " " + custom_call_id + " " + status_display_content + "<br>" + VDIC_fronter);
	
            // ECCS Customization
            <?php
            if(ECCS_BLIND_MODE === 'y'){
            ?>
            $("#cust_campaign_name").html("["+ campaign_name + "] - ");
            $("#cust_call_type").html(" - <span style='background-color: blue;'>OUTBOUNDzz CALL</span>");
            <?php } ?>

            if (CBentry_time.length > 2) {
            //    $("#CustInfoSpan").html(" <b> PREVIOUS CALLBACK </b>");
            //    $("#CustInfoSpan").css('background', CustCB_bgcolor);
            //    $("#CBcommentsBoxA").html("<b>Last Call: </b>" + CBentry_time);
            //    $("#CBcommentsBoxB").html("<b>CallBack: </b>" + CBcallback_time);
            //    $("#CBcommentsBoxC").html("<b>Agent: </b>" + CBuser);
            //    $("#CBcommentsBoxD").html("<b>Comments: </b><br />" + CBcomments);
            //    //showDiv('CBcommentsBox');
                    
                // ECCS Customization
                <?php
                if(ECCS_BLIND_MODE === 'y'){
                ?>
                $("#cust_campaign_name").html("["+ campaign_name + "] - ");
                $("#cust_call_type").html(" - <span style='background-color: purple;'>CALLBACK - Last call by " + CBuser + "</span>");
                 <?php } ?>
                
                swal({
                    title: "<?=$lh->translationFor('previous_callback')?>",
                    text: "<div class='swal-previous-callback' style='text-align: left; padding: 0 30px;'><div style='padding-bottom: 10px;'><b><?=$lh->translationFor('last_call')?>:</b> " + CBentry_time + "</div><div style='padding-bottom: 10px;'><b><?=$lh->translationFor('callback')?>:</b> " + CBcallback_time + "</div><div style='padding-bottom: 10px;'><b><?=$lh->translationFor('agent')?>:</b> " + CBuser + "</div><div style='padding-bottom: 10px;'><b><?=$lh->translationFor('comments')?>:</b><br />" + CBcomments + "</div></div>",
                    type: 'info',
                    html: true
                });

		<?php if(ECCS_BLIND_MODE === 'y'){ ?>
                $("div.swal-previous-callback").attr("title", "<?=$lh->translationFor('previous_callback')?>");
                <?php } ?>

            }
            
            //if (dialed_label == 'ALT')
            //    {$("#CustInfoSpan").html(" <b> ALT DIAL NUMBER: ALT </b>");}
            //if (dialed_label == 'ADDR3')
            //    {$("#CustInfoSpan").html(" <b> ALT DIAL NUMBER: ADDRESS3 </b>");}
            //var REGalt_dial = new RegExp("X","g");
            //if (dialed_label.match(REGalt_dial))
            //{
            //    $("#CustInfoSpan").html(" <b> ALT DIAL NUMBER: " + dialed_label + "</b>");
            //    $("#EAcommentsBoxA").html("<b>Phone Code and Number: </b>" + EAphone_code + " " + EAphone_number);
            //
            //    var EAactive_link = '';
            //    if (EAalt_phone_active == 'Y') 
            //        {EAactive_link = "<a href=\"#\" onclick=\"alt_phone_change('" + EAphone_number + "','" + EAalt_phone_count + "','" + $("#formMain input[name='lead_id']").val() + "','N');\">Change this phone number to INACTIVE</a>";}
            //    else
            //        {EAactive_link = "<a href=\"#\" onclick=\"alt_phone_change('" + EAphone_number + "','" + EAalt_phone_count + "','" + $("#formMain input[name='lead_id']").val() + "','Y');\">Change this phone number to ACTIVE</a>";}
            //
            //    $("#EAcommentsBoxB").html("<b>Active: </b>" + EAalt_phone_active + "<br />" + EAactive_link);
            //    $("#EAcommentsBoxC").html("<b>Alt Count: </b>" + EAalt_phone_count);
            //    $("#EAcommentsBoxD").html("<b>Notes: </b>" + EAalt_phone_notes);
            //    //showDiv('EAcommentsBox');
            //}

            console.log('Group Name', this_VDIC_data.group_name);
            if (this_VDIC_data.group_name.length > 0) {
                inOUT = 'IN';
                if (this_VDIC_data.group_color.length > 2) {
                    $("#MainStatusSpan").css('background', this_VDIC_data.group_color);
                }
                dispnum = $(".formMain input[name='phone_number']").val();
                var status_display_number = phone_number_format(dispnum);
                var callnum = dialed_number;
                var dial_display_number = phone_number_format(callnum);

                var status_display_content = '';
                if (status_display_CALLID > 0) {status_display_content = status_display_content + "<br><b><?=$lh->translationFor('uid')?>:</b> " + CIDcheck;}
                if (status_display_LEADID > 0) {status_display_content = status_display_content + "<br><b><?=$lh->translationFor('lead_id')?>:</b> " + $("#formMain input[name='lead_id']").val();}
                if (status_display_LISTID > 0) {status_display_content = status_display_content + "<br><b><?=$lh->translationFor('list_id')?>:</b> " + $("#formMain input[name='list_id']").val();}

                $("#MainStatusSpan").html("<b><?=$lh->translationFor('incoming_call')?>:</b> " + dial_display_number + " " + custom_call_id + " <?=$lh->translationFor('group')?>- " + this_VDIC_data.group_name + " &nbsp; " + VDIC_fronter + " " + status_display_content); 
	
                // ECCS Customization
                <?php
                if(ECCS_BLIND_MODE === 'y'){
                ?>
                $("#cust_campaign_name").html("["+ this_VDIC_data.group_name + "] - ");
                $("#cust_call_type").html(" - <span style='background-color: red;'>INBOUND CALL</span>");
                <?php } ?>
            }

            toggleButton('DialHangup','hangup');
            
            toggleButton('ParkCall', 'park');
            $("#btnParkCall").attr('onclick', "mainxfer_send_redirect('ParK','" + lastcustchannel + "','" + lastcustserverip + "'); toggleButton('TransferCall', 'off');");
            if ( (ivr_park_call == 'ENABLED') || (ivr_park_call == 'ENABLED_PARK_ONLY') ) {
                toggleButton('IVRParkCall', 'parkivr');
                $("#btnIVRParkCall").attr('onclick', "mainxfer_send_redirect('ParKivr','" + lastcustchannel + "','" + lastcustserverip + "');");
            }
            
            toggleButton('TransferCall', 'XFERON');
            
            toggleButton('LocalCloser', 'on');
            $("#btnLocalCloser").attr('onclick', "mainxfer_send_redirect('XfeRLOCAL','" + lastcustchannel + "','" + lastcustserverip + "');");
            
            toggleButton('DialBlindTransfer', 'on');
            $("#btnDialBlindTransfer").attr('onclick', "mainxfer_send_redirect('XfeRBLIND','" + lastcustchannel + "','" + lastcustserverip + "');");
            
            toggleButton('DialBlindVMail', 'on');
            $("#btnDialBlindVMail").attr('onclick', "mainxfer_send_redirect('XfeRVMAIL','" + lastcustchannel + "','" + lastcustserverip + "');");

            if ( (quick_transfer_button == 'IN_GROUP') || (quick_transfer_button == 'LOCKED_IN_GROUP') ) {
                if (quick_transfer_button_locked > 0)
                    {quick_transfer_button_orig = default_xfer_group;}

                toggleButton('QuickTransfer', 'on');
                $("#btnQuickTransfer").attr('onclick', "mainxfer_send_redirect('XfeRLOCAL','" + lastcustchannel + "','" + lastcustserverip + "','','','" + quick_transfer_button_locked + "');return false;");
            }
            if (prepopulate_transfer_preset_enabled > 0) {
                if ( (prepopulate_transfer_preset == 'PRESET_1') || (prepopulate_transfer_preset == 'LOCKED_PRESET_1') ) {
                    $(".formXFER input[name='xfernumber']").val(Call_XC_a_Number);
                    $(".formXFER input[name='xfername']").val('D1');
                }
                if ( (prepopulate_transfer_preset == 'PRESET_2') || (prepopulate_transfer_preset == 'LOCKED_PRESET_2') ) {
                    $(".formXFER input[name='xfernumber']").val(Call_XC_b_Number);
                    $(".formXFER input[name='xfername']").val('D2');
                }
                if ( (prepopulate_transfer_preset == 'PRESET_3') || (prepopulate_transfer_preset == 'LOCKED_PRESET_3') ) {
                    $(".formXFER input[name='xfernumber']").val(Call_XC_c_Number);
                    $(".formXFER input[name='xfername']").val('D3');
                }
                if ( (prepopulate_transfer_preset == 'PRESET_4') || (prepopulate_transfer_preset == 'LOCKED_PRESET_4') ) {
                    $(".formXFER input[name='xfernumber']").val(Call_XC_d_Number);
                    $(".formXFER input[name='xfername']").val('D4');
                }
                if ( (prepopulate_transfer_preset == 'PRESET_5') || (prepopulate_transfer_preset == 'LOCKED_PRESET_5') ) {
                    $(".formXFER input[name='xfernumber']").val(Call_XC_e_Number);
                    $(".formXFER input[name='xfername']").val('D5');
                }
            }
            if ( (quick_transfer_button == 'PRESET_1') || (quick_transfer_button == 'PRESET_2') || (quick_transfer_button == 'PRESET_3') || (quick_transfer_button == 'PRESET_4') || (quick_transfer_button == 'PRESET_5') || (quick_transfer_button == 'LOCKED_PRESET_1') || (quick_transfer_button == 'LOCKED_PRESET_2') || (quick_transfer_button == 'LOCKED_PRESET_3') || (quick_transfer_button == 'LOCKED_PRESET_4') || (quick_transfer_button == 'LOCKED_PRESET_5') ) {
                if ( (quick_transfer_button == 'PRESET_1') || (quick_transfer_button == 'LOCKED_PRESET_1') ) {
                    $(".formXFER input[name='xfernumber']").val(Call_XC_a_Number);
                    $(".formXFER input[name='xfername']").val('D1');
                }
                if ( (quick_transfer_button == 'PRESET_2') || (quick_transfer_button == 'LOCKED_PRESET_2') ) {
                    $(".formXFER input[name='xfernumber']").val(Call_XC_b_Number);
                    $(".formXFER input[name='xfername']").val('D2');
                }
                if ( (quick_transfer_button == 'PRESET_3') || (quick_transfer_button == 'LOCKED_PRESET_3') ) {
                    $(".formXFER input[name='xfernumber']").val(Call_XC_c_Number);
                    $(".formXFER input[name='xfername']").val('D3');
                }
                if ( (quick_transfer_button == 'PRESET_4') || (quick_transfer_button == 'LOCKED_PRESET_4') ) {
                    $(".formXFER input[name='xfernumber']").val(Call_XC_d_Number);
                    $(".formXFER input[name='xfername']").val('D4');
                }
                if ( (quick_transfer_button == 'PRESET_5') || (quick_transfer_button == 'LOCKED_PRESET_5') ) {
                    $(".formXFER input[name='xfernumber']").val(Call_XC_e_Number);
                    $(".formXFER input[name='xfername']").val('D5');
                }
                if (quick_transfer_button_locked > 0) {
                    quick_transfer_button_orig = $(".formXFER input[name='xfernumber']").val();
                }
                
                toggleButton('QuickTransfer', 'on');
                $("#btnQuickTransfer").attr('onclick', "mainxfer_send_redirect('XfeRBLIND','" + lastcustchannel + "','" + lastcustserverip + "','','','" + quick_transfer_button_locked + "');return false;");
            }

            if (custom_3way_button_transfer_enabled > 0) {
                //$("#CustomXfer").html("<a href=\"#\" onclick=\"custom_button_transfer();return false;\"><img src=\"./images/vdc_LB_customxfer.gif\" border=\"0\" alt=\"Custom Transfer\" /></a>");
                //toggleButton('CustomTransfer', 'on');
            }

            if (call_requeue_button > 0) {
                var CloserSelectChoices = $("#CloserSelectList").val();
                var regCRB = new RegExp("AGENTDIRECT","ig");
                if ( (CloserSelectChoices.match(regCRB)) || (closer_campaigns.match(regCRB)) ) {
                    toggleButton('ReQueueCall', 'on');
                } else {
                    toggleButton('ReQueueCall', 'off');
                }
            }

            // Build transfer pull-down list
            loop_ct = 0;
            live_Xfer_HTML = '';
            Xfer_Select = '';
            while (loop_ct < XFgroupCOUNT) {
                if (VARxferGroups[loop_ct] == LIVE_default_xfer_group)
                    {Xfer_Select = 'selected ';}
                else {Xfer_Select = '';}
                live_Xfer_HTML = live_Xfer_HTML + "<option " + Xfer_Select + "value=\"" + VARxferGroups[loop_ct] + "\">" + VARxferGroups[loop_ct] + " - " + VARxferGroupsNames[loop_ct] + "</option>\n";
                loop_ct++;
            }
            $("#transfer-local-closer").html(live_Xfer_HTML);

            if (lastcustserverip == server_ip) {
                //$("#VolumeUpSpan").html("<a onclick=\"VolumeControl('UP','" + lastcustchannel + "','');return false;\"><img src='./images/vdc_volume_up.gif' border='0' /></a>");
                //$("#VolumeDownSpan").html("<a onclick=\"VolumeControl('DOWN','" + lastcustchannel + "','');return false;\"><img src='./images/vdc_volume_down.gif' border='0' /></a>");
            }

            if (dial_method == "INBOUND_MAN") {
                //$("#DiaLControl").html("<img src=\"./images/pause_OFF.png\" border=\"0\" title=\"Pause\" alt=\" Pause \" /><br /><img src=\"./images/resume_OFF.png\" border=\"0\" title=\"Resume\" alt=\"Resume\" /><small>&nbsp;</small><img src=\"./images/dialnext_OFF.png\" border=\"0\" title=\"Dial Next Number\" alt=\"Dial Next Number\" />");
                toggleButton('ResumePause', 'pause', false);
            } else {
                //$("#DiaLControl").html(DiaLControl_auto_HTML_OFF);
                toggleButton('ResumePause', 'pause', false);
            }

            if (VDCL_group_id.length > 1)
                {var group = VDCL_group_id;}
            else
                {var group = campaign;}
            if ( (dialed_label.length < 2) || (dialed_label=='NONE') ) {dialed_label='MAIN';}

            //if (hide_gender < 1)
            //{
            //    var genderIndex = document.getElementById("gender_list").selectedIndex;
            //    var genderValue =  document.getElementById('gender_list').options[genderIndex].value;
            //    $(".formMain input[name='gender']").val(genderValue);
            //}

            LeadDispo = '';

            var regWFAcustom = new RegExp("^VAR","ig");
            if (VDIC_web_form_address.match(regWFAcustom)) {
                TEMP_VDIC_web_form_address = URLDecode(VDIC_web_form_address,'YES','CUSTOM');
                TEMP_VDIC_web_form_address = TEMP_VDIC_web_form_address.replace(regWFAcustom, '');
            } else {
                TEMP_VDIC_web_form_address = URLDecode(VDIC_web_form_address,'YES','DEFAULT','1');
            }

            if (VDIC_web_form_address_two.match(regWFAcustom)) {
                TEMP_VDIC_web_form_address_two = URLDecode(VDIC_web_form_address_two,'YES','CUSTOM');
                TEMP_VDIC_web_form_address_two = TEMP_VDIC_web_form_address_two.replace(regWFAcustom, '');
            } else {
                TEMP_VDIC_web_form_address_two = URLDecode(VDIC_web_form_address_two,'YES','DEFAULT','2');
            }


            if (VDIC_web_form_address.length > 0) {
                $("#openWebForm").removeClass('disabled');
                //$("#WebFormSpan").html("<a href=\"" + TEMP_VDIC_web_form_address + "\" target=\"" + web_form_target + "\" onMouseOver=\"WebFormRefresH();\"><img src=\"./images/vdc_LB_webform.gif\" border=\"0\" alt=\"Web Form\" /></a>\n");
            }

            if (enable_second_webform > 0 && VDIC_web_form_address_two.length > 0) {
                $("#openWebFormTwo").removeClass('disabled');
                //$("#WebFormSpanTwo").html("<a href=\"" + TEMP_VDIC_web_form_address_two + "\" target=\"" + web_form_target + "\" onMouseOver=\"WebFormTwoRefresH();\"><img src=\"./images/vdc_LB_webform_two.gif\" border=\"0\" alt=\"Web Form 2\" /></a>\n");
            }

            if ( (LIVE_campaign_recording == 'ALLCALLS') || (LIVE_campaign_recording == 'ALLFORCE') )
                {all_record = 'YES';}

            if (typeof Call_Script_ID === 'undefined') {
                Call_Script_ID = '';
            }
            if ( (view_scripts == 1) && (Call_Script_ID.length > 0 || campaign_script.length > 0) ) {
                var SCRIPT_web_form = "http://127.0.0.1/testing.php";
                var TEMP_SCRIPT_web_form = URLDecode(SCRIPT_web_form,'YES','DEFAULT','1');
                //$("#ScriptButtonSpan").html("<A HREF=\"#\" onClick=\"ScriptPanelToFront();\"><IMG SRC=\"./images/script_tab.png\" ALT=\"SCRIPT\" WIDTH=143 HEIGHT=27 BORDER=0></A>");

                if ( (script_recording_delay > 0) && ( (LIVE_campaign_recording == 'ALLCALLS') || (LIVE_campaign_recording == 'ALLFORCE') ) ) {
                    delayed_script_load = 'YES';
                    //RefresHScript('CLEAR');
                    ClearScript();
                } else {
                    LoadScriptContents();
                }
            }

            if (custom_fields_enabled > 0) {
                $("#CustomFormSpan").html("<a href=\"#\" onclick=\"FormPanelToFront();\"><img src=\"./images/custom_form_tab.png\" alt=\"FORM\" width=\"143px\" height=\"27px\" border=\"0\" /></a>");
                //FormContentsLoad();
            }
            // JOEJ 082812 - new for email feature
            if (email_enabled > 0) {
                //EmailContentsLoad();
            }
            if (get_call_launch == 'SCRIPT') {
                if (delayed_script_load == 'YES') {
                    LoadScriptContents();
                }
                //ScriptPanelToFront();
                $('#agent_tablist a[href="#scripts"]').tab('show');
            }
            if (get_call_launch == 'FORM') {
                //FormPanelToFront();
            }
            if (get_call_launch == 'EMAIL') {
                //EmailPanelToFront();
            }

            if (get_call_launch == 'WEBFORM') {
                window.open(TEMP_VDIC_web_form_address, web_form_target, 'toolbar=1,scrollbars=1,location=1,statusbar=1,menubar=1,resizable=1,width=640,height=450');
            }
            if (get_call_launch == 'WEBFORMTWO') {
                window.open(TEMP_VDIC_web_form_address_two, web_form_target, 'toolbar=1,scrollbars=1,location=1,statusbar=1,menubar=1,resizable=1,width=640,height=450');
            }

            if (useIE > 0) {
                var regCTC = new RegExp("^NONE","ig");
                if (Copy_to_Clipboard.match(regCTC))
                    {var nothing = 1;}
                else {
                    var tmp_clip = $(Copy_to_Clipboard);
                    //alert_box("Copy to clipboard SETTING: |" + useIE + "|" + Copy_to_Clipboard + "|" + tmp_clip.value + "|");
                    window.clipboardData.setData('Text', tmp_clip.value)
                    //alert_box("Copy to clipboard: |" + tmp_clip.value + "|" + Copy_to_Clipboard + "|");
                }
            }

            if (alert_enabled == 'ON') {
                var callnum = dialed_number;
                var dial_display_number = phone_number_format(callnum);
                swal({
                    title: "<?=$lh->translationFor('incoming')?>",
                    text: dial_display_number + "<br> <?=$lh->translationFor('group')?>- " + VDIC_data_VDIG[1] + " &nbsp; " + VDIC_fronter,
                    type: 'warning',
                    html: true
                });
            }
        } else if (email_enabled > 0 && EMAILgroupCOUNT > 0 && AutoDialWaiting == 1) {
            // JOEJ check for EMAIL
            // QUEUEpadding is needed to allow inbound calls to get through QUEUE status
            QUEUEpadding++;
            if (QUEUEpadding == 5)  {
                QUEUEpadding = 0;
                //check_for_incoming_email();
            }
        }

	// ECCS Customization
         <?php
           /* if(ECCS_BLIND_MODE === 'y'){
         ?>
               if(inOUT === "OUT"){
                  $("#cust_call_type").html(" - OUTBOUND CALL");
               }
               if(inOUT === "IN"){
                  $("#cust_call_type").html(" - INBOUND CALL");
               }
         <?php }*/ ?>
    });
}

// ################################################################################
// RefresH the agents view sidebar or xfer frame
function RefreshAgentsView(RAlocation, RAcount) {
    if (RAcount > 0) {
        if (even > 0) {
            var postData = {
                goAction: 'goGetAgentsLoggedIn',
                goServerIP: server_ip,
                goSessionName: session_name,
                goUser: uName,
                goPass: uPass,
                goUserGroup: user_group,
                goCampaign: campaign,
                goConfExten: session_id,
                goExtension: extension,
                goProtocol: protocol,
                goStage: agent_status_view_time,
                goComments: RAlocation,
                responsetype: 'json'
            };
        
            $.ajax({
                type: 'POST',
                url: '<?=$goAPI?>/goAgent/goAPI.php',
                processData: true,
                data: postData,
                dataType: "json",
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                }
            })
            .done(function (result) {
                var newRAlocationHTML = result.result;
                if (RAlocation == 'AgentXferViewSelect') {
                    //document.getElementById(RAlocation).innerHTML = newRAlocationHTML + "\n<br /><br /><a href=\"#\" onclick=\"AgentsXferSelect('0','AgentXferViewSelect');return false;\"><?=$lang['close_window']?></a>&nbsp;";
                } else {
                    //document.getElementById(RAlocation).innerHTML = newRAlocationHTML + "\n";
                    var agent_list = '';
                    if (result.result == 'success') {
                        var agent_data = result.data;
                        $.each(agent_data, function(key, agents) {
                            var agent_status_time = '';
                            $.each(agents.logged_in, function(agentID, agentStatus) {
                                if (agent_status_view_time > 0) {
                                    agent_status_time = ' - '+agentStatus.call_time;
                                }
                                var agentAvatar = goGetAvatar(agentID, '32');
                                agent_list += '<li title="'+agentID+' - '+agentStatus.status+'" style="cursor: default; color: '+agentStatus.textcolor+'; background-color: '+agentStatus.statcolor+'; line-height: 32px; padding: 5px 0;"><div>'+agentStatus.full_name+''+agent_status_time+'<span style="float: left; padding: 0 15px;">'+agentAvatar+'</span></div></li>';
                            })
                        });
                        agent_list += '<li style="bottom: 25; position: absolute;"><div class="text-center"><span><i class="fa fa-square" style="color: #ADD8E6;"></i> <?=$lh->translationFor('ready')?></span> &nbsp; <span><i class="fa fa-square" style="color: #D8BFD8;"></i> <?=$lh->translationFor('incall')?></span> &nbsp; <span><i class="fa fa-square" style="color: #F0E68C;"></i> <?=$lh->translationFor('paused')?></span></div></li>';
                        $("#go_agent_view_list").html(agent_list);
                        //goAvatar._init(goOptions);
                    }
                }
            });
        }
    }
}

function CustomerChannelGone() {
    
}

// ################################################################################
// Check to see if there is a call being sent from the auto-dialer to agent conf
function ReCheckCustomerChan() {
    var postData = {
        goAction: 'goVDADCheckIncoming',
        goServerIP: server_ip,
        goSessionName: session_name,
        goUser: uName,
        goPass: uPass,
        goCampaign: campaign,
        goAgentLogID: agent_log_id,
        goLeadID: $(".formMain input[name='lead_id']").val(),
        responsetype: 'json'
    };

    //$.ajax({
    //    type: 'POST',
    //    url: '<?=$goAPI?>/goAgent/goAPI.php',
    //    processData: true,
    //    data: postData,
    //    dataType: "json",
    //    headers: {
    //        'Content-Type': 'application/x-www-form-urlencoded'
    //    }
    //})
    //.done(function (result) {
        //var recheck_incoming = null;
        //recheck_incoming = xmlhttp.responseText;
    //	alert(xmlhttp.responseText);
        //var recheck_VDIC_array=recheck_incoming.split("\n");
        //if (recheck_VDIC_array[0] == '1') {
        //    var reVDIC_data_VDAC=recheck_VDIC_array[1].split("|");
        //    if (reVDIC_data_VDAC[3] == lastcustchannel)
        //        {
            // do nothing
        //        }
        //    else
        //        {
    //	alert("Channel has changed from:\n" + lastcustchannel + '|' + lastcustserverip + "\nto:\n" + reVDIC_data_VDAC[3] + '|' + reVDIC_data_VDAC[4]);
        //        document.getElementById("callchannel").innerHTML	= reVDIC_data_VDAC[3];
        //        lastcustchannel = reVDIC_data_VDAC[3];
        //        document.vicidial_form.callserverip.value	= reVDIC_data_VDAC[4];
        //        lastcustserverip = reVDIC_data_VDAC[4];
        //        custchannellive = 1;
        //        }
        //}
    //});
}


// ################################################################################
// Populate the dtmf and xfer number for each preset link in xfer-conf frame
function DTMF_Preset_a() {
    $("#conf_dtmf").val(Call_XC_a_DTMF);
    $(".formXFER input[name='xfernumber']").val(Call_XC_a_Number);
    $(".formXFER input[name='xfername']").val('D1');
}
function DTMF_Preset_b() {
    $("#conf_dtmf").val(Call_XC_b_DTMF);
    $(".formXFER input[name='xfernumber']").val(Call_XC_b_Number);
    $(".formXFER input[name='xfername']").val('D2');
}
function DTMF_Preset_c() {
    $(".formXFER input[name='xfernumber']").val(Call_XC_c_Number);
    $(".formXFER input[name='xfername']").val('D3');
}
function DTMF_Preset_d() {
    $(".formXFER input[name='xfernumber']").val(Call_XC_d_Number);
    $(".formXFER input[name='xfername']").val('D4');
}
function DTMF_Preset_e() {
    $(".formXFER input[name='xfernumber']").val(Call_XC_e_Number);
    $(".formXFER input[name='xfername']").val('D5');
}

function DTMF_Preset_a_Dial(taskquiet) {
    $("#conf_dtmf").val(Call_XC_a_DTMF);
    $(".formXFER input[name='xfernumber']").val(Call_XC_a_Number);
    var session_id_dial = session_id;
    if (taskquiet == 'YES')
        {session_id_dial = '7' + session_id};
    BasicOriginateCall(Call_XC_a_Number,'NO','YES',session_id_dial,'YES','','1','0');
}
function DTMF_Preset_b_Dial(taskquiet) {
    $("#conf_dtmf").val(Call_XC_b_DTMF);
    $(".formXFER input[name='xfernumber']").val(Call_XC_b_Number);
    var session_id_dial = session_id;
    if (taskquiet == 'YES')
        {session_id_dial = '7' + session_id};
    BasicOriginateCall(Call_XC_b_Number,'NO','YES',session_id_dial,'YES','','1','0');
}
function DtMf_Preset_c_Dial(taskquiet) {
    $(".formXFER input[name='xfernumber']").val(Call_XC_c_Number);
    var session_id_dial = session_id;
    if (taskquiet == 'YES')
        {session_id_dial = '7' + session_id};
    BasicOriginateCall(Call_XC_c_Number,'NO','YES',session_id_dial,'YES','','1','0');
}
function DTMF_Preset_d_Dial(taskquiet) {
    $(".formXFER input[name='xfernumber']").val(Call_XC_d_Number);
    var session_id_dial = session_id;
    if (taskquiet == 'YES')
        {session_id_dial = '7' + session_id};
    BasicOriginateCall(Call_XC_d_Number,'NO','YES',session_id_dial,'YES','','1','0');
}
function DTMF_Preset_e_Dial(taskquiet) {
    $(".formXFER input[name='xfernumber']").val(Call_XC_e_Number);
    var session_id_dial = session_id;
    if (taskquiet == 'YES')
        {session_id_dial = '7' + session_id};
    BasicOriginateCall(Call_XC_e_Number,'NO','YES',session_id_dial,'YES','','1','0');
}
function hangup_timer_xfer() {
    //hideDiv('CustomerGoneBox');
    WaitingForNextStep = 0;
    custchannellive = 0;

    DialedCallHangup();
}
function extension_timer_xfer() {
    $(".formXFER input[name='xfernumber']").val(timer_action_destination);
    mainxfer_send_redirect('XfeRBLIND',lastcustchannel,lastcustserverip);
}
function callmenu_timer_xfer() {
    API_selected_callmenu = timer_action_destination;
    $(".formXFER input[name='xfernumber']").val(timer_action_destination);
    mainxfer_send_redirect('XfeRBLIND',lastcustchannel,lastcustserverip);
}
function ingroup_timer_xfer() {
    API_selected_xfergroup = timer_action_destination;
    $(".formXFER input[name='xfernumber']").val(timer_action_destination);
    mainxfer_send_redirect('XfeRLOCAL',lastcustchannel,lastcustserverip);
}

// ################################################################################
// Insert or update the vicidial_log entry for a customer call
function DialLog(taskMDstage, nodeletevdac) {
    var alt_num_status = 0;
    if (taskMDstage == "start") {
        MDlogEPOCH = 0;
        var UID_test = $(".formMain input[name='uniqueid']").val();
        if (UID_test.length < 4) {
            UID_test = epoch_sec + '.' + random;
            $(".formMain input[name='uniqueid']").val(UID_test);
        }
    } else {
        if (alt_phone_dialing == 1) {
            if ($("#DialALTPhone").is(':checked')) {
                var status_display_content = '';
                if (status_display_LEADID > 0) {status_display_content = status_display_content + " <?=$lh->translationFor('lead')?>: " + $(".formMain input[name='lead_id']").val();}
                if (status_display_LISTID > 0) {status_display_content = status_display_content + " <?=$lh->translationFor('list')?>: " + $(".formMain input[name='list_id']").val();}

                alt_num_status = 1;
                reselect_alt_dial = 1;
                alt_dial_active = 1;
                alt_dial_status_display = 1;
                var man_status = "<?=$lh->translationFor('dial_alt_phone_number')?>: <a href=\"#\" onclick=\"ManualDialOnly('MainPhone')\"><font class=\"preview_text\">&nbsp;<?=$lh->translationFor('main_phone')?>&nbsp;</font></a> or <a href=\"#\" onclick=\"ManualDialOnly('ALTPhone')\"><font class=\"preview_text\">&nbsp;<?=$lh->translationFor('alt_phone')?>&nbsp;</font></a> or <a href=\"#\" onclick=\"ManualDialOnly('Address3')\"><font class=\"preview_text\">&nbsp;<?=$lh->translationFor('address3')?>&nbsp;</font></a> or <a href=\"#\" onclick=\"ManualDialAltDone()\"><font class=\"preview_text_red\">&nbsp;<?=$lh->translationFor('finish_lead')?>&nbsp;</font></a>" + status_display_content;
                $("#MainStatusSpan").html(man_status);
            }
        }
    }
    
    var postData = {
        goAction: 'goManualDialLogCall',
        goServerIP: server_ip,
        goSessionName: session_name,
        goUser: uName,
        goPass: uPass,
        goStage: taskMDstage,
        goCampaign: campaign,
        goAgentLogID: agent_log_id,
        goUniqueID: $(".formMain input[name='uniqueid']").val(),
        goLeadID: $(".formMain input[name='lead_id']").val(),
        goListID: $(".formMain input[name='list_id']").val(),
        goLengthInSec: 0,
        goPhoneCode: $(".formMain input[name='phone_code']").val(),
        goPhoneNumber: lead_dial_number,
        goExten: extension,
        goExtension: extension,
        goChannel: lastcustchannel,
        goStartEpoch: MDlogEPOCH,
        goAutoDialLevel: auto_dial_level,
        goStopRecAfterEachCall: VDstop_rec_after_each_call,
        goConfSilentPrefix: conf_silent_prefix,
        goProtocol: protocol,
        goExtContext: ext_context,
        goConfExten: session_id,
        goUserABB: user_abb,
        goMDnextCID: LastCID,
        goInOut: inOUT,
        goALTDial: dialed_label,
        goAgentChannel: agentchannel,
        goConfDialed: conf_dialed,
        goLeavingThreeway: leaving_threeway,
        goHangupAllNonReserved: hangup_all_non_reserved,
        goBlindTransfer: blind_transfer,
        goDialMethod: dial_method,
        goNoDeleteVDAC: nodeletevdac,
        goALTNumStatus: alt_num_status,
        goQMExtension: qm_extension,
        responsetype: 'json'
    };

    $.ajax({
        type: 'POST',
        url: '<?=$goAPI?>/goAgent/goAPI.php',
        processData: true,
        data: postData,
        dataType: "json",
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
        }
    })
    .done(function (result) {
        var MDlogResponse = result.message;
        var MDlogResponse_array = MDlogResponse.split('\n');
        if ( (MDlogResponse_array[0].indexOf("LOG NOT ENTERED") > -1) && (VDstop_rec_after_each_call != 1) ) {
    //		alert("error: log not entered\n");
            console.log('ERROR: Log NOT Entered');
        } else {
            MDlogEPOCH = MDlogResponse_array[1];
            if ( (taskMDstage != "start") && (VDstop_rec_after_each_call == 1) ) {
                //var conf_rec_start_html = "<a href=\"#\" onclick=\"ConfSendRecording('MonitorConf','" + session_id + "','');return false;\"><img src=\"./images/vdc_LB_startrecording.gif\" border=\"0\" alt=\"<?=$lh->translationFor('start_recording')?>\" /></a>";
                if ( (LIVE_campaign_recording == 'NEVER') || (LIVE_campaign_recording == 'ALLFORCE') ) {
                    //$("#RecorDControl").html("<img src=\"./images/vdc_LB_startrecording_OFF.gif\" border=\"0\" alt=\"<?=$lh->translationFor('start_recording')?>\" />");
                } else {
                    //$("#RecorDControl").html(conf_rec_start_html);
                    btnRecordCall('STOP', true);
                }
                
                MDlogRecordings = MDlogResponse_array[3];
                if (window.MDlogRecorDings) {
                    var MDlogRecordings_array = MDlogRecordings.split("|");
            //		recording_filename = MDlogRecordings_array[2];
            //		recording_id = MDlogRecordings_array[3];

                    var RecDispName = MDlogRecorDings_array[2];
                    if (RecDispName.length > 25) {
                        RecDispName = RecDispName.substr(0,22);
                        RecDispName = RecDispName + '...';
                    }
                    $("#RecorDingFilename").html(RecDispName);
                    $("#RecordID").html(MDlogRecordings_array[3]);
                }
            }
        }
        RedirectXFER = 0;
        conf_dialed = 0;
    });
}

// ################################################################################
// Send MonitorConf/StopMonitorConf command for recording of conferences
function ConfSendRecording(taskconfrectype, taskconfrec, taskconffile, taskfromapi) {
    console.log('Start recording of this call');
    
    if (inOUT == 'OUT') {
        tmp_vicidial_id = $(".formMain input[name='uniqueid']").val();
    } else {
        tmp_vicidial_id = 'IN';
    }
    if (taskconfrectype == 'MonitorConf') {
        var REGrecCLEANvlc = new RegExp(" ","g");
        var recVendorLeadCode = $(".formMain input[name='vendor_lead_code']").val();
        recVendorLeadCode = recVendorLeadCode.replace(REGrecCLEANvlc, '');
        var recLeadID = $(".formMain input[name='lead_id']").val();

        //	CAMPAIGN CUSTPHONE FULLDATE TINYDATE EPOCH AGENT VENDORLEADCODE LEADID
        var REGrecCAMPAIGN = new RegExp("CAMPAIGN","g");
        var REGrecINGROUP = new RegExp("INGROUP","g");
        var REGrecCUSTPHONE = new RegExp("CUSTPHONE","g");
        var REGrecFULLDATE = new RegExp("FULLDATE","g");
        var REGrecTINYDATE = new RegExp("TINYDATE","g");
        var REGrecEPOCH = new RegExp("EPOCH","g");
        var REGrecAGENT = new RegExp("AGENT","g");
        var REGrecVENDORLEADCODE = new RegExp("VENDORLEADCODE","g");
        var REGrecLEADID = new RegExp("LEADID","g");
        filename = LIVE_campaign_rec_filename;
        filename = filename.replace(REGrecCAMPAIGN, campaign);
        filename = filename.replace(REGrecINGROUP, VDCL_group_id);
        filename = filename.replace(REGrecCUSTPHONE, lead_dial_number);
        filename = filename.replace(REGrecFULLDATE, filedate);
        filename = filename.replace(REGrecTINYDATE, tinydate);
        filename = filename.replace(REGrecEPOCH, epoch_sec);
        filename = filename.replace(REGrecAGENT, user);
        filename = filename.replace(REGrecVENDORLEADCODE, recVendorLeadCode);
        filename = filename.replace(REGrecLEADID, recLeadID);
    	//filename = filedate + "_" + user_abb;
        var query_recording_exten = recording_exten;
        var channelrec = "Local/" + conf_silent_prefix + '' + taskconfrec + "@" + ext_context;
        //var conf_rec_start_html = "<a href=\"#\" onclick=\"conf_send_recording('StopMonitorConf','" + taskconfrec + "','" + filename + "');return false;\"><img src=\"./images/vdc_LB_stoprecording.gif\" border=\"0\" alt=\"<?=$lang['stop_recording']?>\" /></a>";

        if (LIVE_campaign_recording == 'ALLFORCE') {
            //$("#RecorDControl").html("<img src=\"./images/vdc_LB_startrecording_OFF.gif\" border=\"0\" alt=\"<?=$lh->translationFor('start_recording')?>\" />");
        } else {
            //$("#RecorDControl").html(conf_rec_start_html);
            btnRecordCall('START', true);
        }
    }
    if (taskconfrectype == 'StopMonitorConf') {
        filename = taskconffile;
        var query_recording_exten = session_id;
        var channelrec = "Local/" + conf_silent_prefix + '' + taskconfrec + "@" + ext_context;
        //var conf_rec_start_html = "<a href=\"#\" onclick=\"conf_send_recording('MonitorConf','" + taskconfrec + "','');return false;\"><img src=\"./images/vdc_LB_startrecording.gif\" border=\"0\" alt=\"<?=$lang['start_recording']?>\" /></a>";
        if (LIVE_campaign_recording == 'ALLFORCE') {
            //$("#RecorDControl").html("<img src=\"./images/vdc_LB_startrecording_OFF.gif\" border=\"0\" alt=\"<?=$lh->translationFor('start_recording')?>\" />");
        } else {
            //$("#RecorDControl").html(conf_rec_start_html);
            btnRecordCall('STOP', true);
        }
    }
    
    var postData = {
        goAction: 'goMonitorCall',
        goServerIP: server_ip,
        goSessionName: session_name,
        goUser: uName,
        goPass: uPass,
        goCampaign: campaign,
        goTask: taskconfrectype,
        goChannel: channelrec,
        goFilename: filename,
        goExten: query_recording_exten,
        goExtContext: ext_context,
        goLeadID: $(".formMain input[name='lead_id']").val(),
        goExtPriority: 1,
        goFromVDC: 'YES',
        goFormat: 'text',
        goUniqueID: tmp_vicidial_id,
        goFromAPI: taskfromapi,
        responsetype: 'json'
    };

    $.ajax({
        type: 'POST',
        url: '<?=$goAPI?>/goAgent/goAPI.php',
        processData: true,
        data: postData,
        dataType: "json",
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
        }
    })
    .done(function (result) {
        var RClookResponse = result;
        if (RClookResponse.result == 'success') {
            var RClookFILE = RClookResponse.filename;
            var RClookID = RClookResponse.recording_id;
            if (RClookID.length > 0) {
                recording_filename = RClookFILE;
                recording_id = RClookID;
    
                if (delayed_script_load == 'YES') {
                    //RefresHScript();
                    delayed_script_load = 'NO';
                }
    
                var RecDispNamE = RClookFILE;
                if (RecDispNamE.length > 25) {
                    RecDispNamE = RecDispNamE.substr(0,22);
                    RecDispNamE = RecDispNamE + '...';
                }
                $("#RecorDingFilename").html(RecDispNamE);
                $("#RecorDID").html(RClookID);
            }
        }
    });
}

// ################################################################################
// Finish the alternate dialing and move on to disposition the call
function ManualDialAltDone() {
    alt_phone_dialing = starting_alt_phone_dialing;
    alt_dial_active = 0;
    alt_dial_status_display = 0;
    open_dispo_screen = 1;
    $("#MainStatusSpan").html("<?=$lh->translationFor('dial_next')?>");
}

// ################################################################################
// RefresH the calls in queue bottombar
function RefreshCallsInQueue(CQcount) {
    if (CQcount > 0) {
        if (even > 0) {
            var postData = {
                goAction: 'goGetCallsInQueue',
                goServerIP: server_ip,
                goSessionName: session_name,
                goUser: uName,
                goPass: uPass,
                goCampaign: campaign,
                goConfExten: session_id,
                goExtension: extension,
                goProtocol: protocol,
                responsetype: 'json'
            };
        
            $.ajax({
                type: 'POST',
                url: '<?=$goAPI?>/goAgent/goAPI.php',
                processData: true,
                data: postData,
                dataType: "json",
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                }
            })
            .done(function (result) {
                if (result.result == 'success') {
                    $('#callsinqueuelist').html('');
                } else {
                    if (result.result == 'error' && (!!$.prototype.snackbar)) {
                        $.snackbar({content: "<i class='fa fa-exclamation-circle fa-lg text-warning' aria-hidden='true'></i>&nbsp; " + result.message, timeout: 3000, htmlAllowed: true});
                    }
                }
            });
        }
    }
}


// ################################################################################
// Request number of USERONLY callbacks for this agent
function CallBacksCountCheck() {
    var postData = {
        goAction: 'goGetCallbackCount',
        goUser: uName,
        goPass: uPass,
        goCampaign: campaign,
        goNoExpire: cb_noexpire,
        responsetype: 'json'
    };

    $.ajax({
        type: 'POST',
        url: '<?=$goAPI?>/goAgent/goAPI.php',
        processData: true,
        data: postData,
        dataType: "json",
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
        }
    })
    .done(function (result) {
        var CBpre = '';
        var CBpost = '';
        var CBalert = '';
        var Defer = 0;

        if (result.result == 'success') {
            var CBdata = result.data;
            var CBcount = CBdata.callback_count;
            if (scheduled_callbacks_count == 'LIVE')
                {CBcount = CBdata.callback_live;}
            var CBcountToday = CBdata.callback_today;
            if (CBcount == 0) {
                var CBprint = "NO";
            } else {
                var CBprint = CBcount;
                if ( (LastCallbackCount < CBcount) || (LastCallbackCount > CBcount) ) {
                    LastCallbackCount = CBcount;
                    LastCallbackViewed = 0;
                }
    
                if ( (scheduled_callbacks_alert == 'RED_DEFER') || (scheduled_callbacks_alert == 'BLINK_DEFER') || (scheduled_callbacks_alert == 'BLINK_RED_DEFER') )
                    {Defer=1;}
    
                if ( (LastCallbackViewed > 0) && (Defer > 0) ) {
                    var do_nothing = 1;
                } else {
                    if ( (scheduled_callbacks_alert == 'BLINK') || (scheduled_callbacks_alert == 'BLINK_DEFER') ) {
                        CBpre = '<blink>';
                        CBpost = '</blink>';
                    }
                    if ( (scheduled_callbacks_alert == 'RED') || (scheduled_callbacks_alert == 'RED_DEFER') ) {
                        CBpre = '<b><font color="red">';
                        CBpost = '</font></b>';
                    }
                    if ( (scheduled_callbacks_alert == 'BLINK_RED') || (scheduled_callbacks_alert == 'BLINK_RED_DEFER') ) {
                        CBpre = '<b><font color="red"><blink>';
                        CBpost = '</blink></font></b>';
                    }
                    if (CBcountToday > 0) {
                        if (CBcountToday > 1) {
                            //CBalert = "<span class=\"body_text\"> / </span><a href=\"#\" onclick=\"CalLBacKsLisTCheck();return false;\" class=\"red_link\">" + CBpre + '' + CBcountToday + " <?=$lang['callback_alerts_today']?>" + CBpost + "</a>";
                        } else {
                            //CBalert = "<span class=\"body_text\"> / </span><a href=\"#\" onclick=\"CalLBacKsLisTCheck();return false;\" class=\"red_link\">" + CBpre + '' + CBcountToday + " <?=$lang['callback_alert_today']?>" + CBpost + "</a>";
                        }
                    }
                }
            }
            if (CBprint < 2) {
                //CBlinkCONTENT ="<a href=\"#\" onclick=\"CalLBacKsLisTCheck();return false;\">" + CBprint + '' + " <?=$lang['active_callback']?></a>" + CBalert;
            } else {
                //CBlinkCONTENT ="<a href=\"#\" onclick=\"CalLBacKsLisTCheck();return false;\">" + CBprint + '' + " <?=$lang['active_callbacks']?></a>" + CBalert;
            }
            //document.getElementById("CBstatusSpan").innerHTML = CBlinkCONTENT;
            $("#callbacks-active").html(CBcount);
            $("#callbacks-today").html(CBcountToday);
            
            $("a[href='#callbackslist'] small.badge").html(CBcount);
            $("#topbar-callbacks a span.label").html(CBcountToday);
            $("#topbar-callbacks ul li.header").html('<?=$lh->translationFor("you_have")?> '+CBcountToday+' <?=$lh->translationFor("callbacks_for_today")?>');
            
            var CBallToday = CBdata.today_callbacks;
            var maxCBtoday = 4;
            var cntCB = 0;
            $("#topbar-callbacks ul li div.slimScrollDiv ul.menu").empty();
            $.each(CBallToday, function(key, value) {
                if (cntCB < maxCBtoday) {
                    var appendThis = '<li><a href="events.php" title="'+value.cust_name+'" style="padding: 0px 10px;"><h4 style="margin-top: 5.5px;"><p class="pull-left" style="margin-bottom: 5.5px;"><i class="fa fa-phone"></i> <b>'+phone_number_format(value.phone_number)+'</b><br><small style="margin-left:20px; font-size: 12px; line-height: 18px;"><em>Campaign: '+value.campaign_name+'</em></small></p><small class="label label-<?=CRM_UI_STYLE_WARNING?> pull-right" title="'+value.long_callback_time+'"><i class="fa fa-clock-o"></i> '+value.short_callback_time+'</small></h4></a></li>';
                    $("#topbar-callbacks ul li div.slimScrollDiv ul.menu").append(appendThis);
                }
                cntCB++;
            });
            
            if (!$("#contents-callbacks").is(':visible')) {
                var CBallList = CBdata.all_callbacks;
                $("#callback-list").dataTable().fnDestroy();
                $("#callback-list tbody").empty();
                $.each(CBallList, function(key, value) {
                    var thisComments = value.comments;
                    var commentTitle = '';
                    if (typeof callback_alerts[value.callback_id] === 'undefined') {
                        callback_alerts[value.callback_id] = new Array();
                    }
                    if (thisComments.length > 20) {
                        commentTitle = ' title="'+thisComments+'"';
                        thisComments = thisComments.substring(0, 20) + "...";
                    }

		<?php if( ECCS_BLIND_MODE === 'y'){?>
                    var appendThis = '<tr data-id="'+value.callback_id+'"><td title="'+value.cust_name+'" style="cursor: pointer;">'+value.cust_name+'</td><td title="'+value.phone_number.split('').join(' ')+'" style="cursor: pointer;">'+value.phone_number+'</td><td title="'+value.entry_time+'" style="cursor: pointer;"><i class="fa fa-clock-o"></i> '+value.short_entry_time+'</td><td title="'+value.callback_time+'" style="cursor: pointer;"><i class="fa fa-clock-o"></i> '+value.short_callback_time+'</td><td title="'+value.campaign_name+'" style="cursor: pointer;">'+value.campaign_name+'</td><td'+commentTitle+'>'+thisComments+'</td><td class="text-center" style="white-space: nowrap;"><button id="dial-cb-'+value.callback_id+'" title="Dial Callback" data-cbid="'+value.callback_id+'" data-leadid="'+value.lead_id+'" onclick="NewCallbackCall('+value.callback_id+', '+value.lead_id+');" class="btn btn-primary btn-sm dial-callback"><i class="fa fa-phone"></i></button> <button id="remove-cb-'+value.callback_id+'" class="btn btn-danger btn-sm hidden"><i class="fa fa-trash-o"></i></button></td></tr>';
		<?php } else { ?>
                    var appendThis = '<tr data-id="'+value.callback_id+'"><td>'+value.cust_name+'</td><td>'+value.phone_number+'</td><td title="'+value.entry_time+'" style="cursor: pointer;"><i class="fa fa-clock-o"></i> '+value.short_entry_time+'</td><td title="'+value.callback_time+'" style="cursor: pointer;"><i class="fa fa-clock-o"></i> '+value.short_callback_time+'</td><td>'+value.campaign_name+'</td><td'+commentTitle+'>'+thisComments+'</td><td class="text-center" style="white-space: nowrap;"><button id="dial-cb-'+value.callback_id+'" data-cbid="'+value.callback_id+'" data-leadid="'+value.lead_id+'" onclick="NewCallbackCall('+value.callback_id+', '+value.lead_id+');" class="btn btn-primary btn-sm dial-callback"><i class="fa fa-phone"></i></button> <button id="remove-cb-'+value.callback_id+'" class="btn btn-danger btn-sm hidden"><i class="fa fa-trash-o"></i></button></td></tr>';
		<?php } ?>

                    $("#callback-list tbody").append(appendThis);
                    
                    if (enable_callback_alert) {
                        callback_alerts[value.callback_id]['lead_id'] = value.lead_id;
                        callback_alerts[value.callback_id]['cust_name'] = value.cust_name;
                        callback_alerts[value.callback_id]['phone_number'] = value.phone_number;
                        callback_alerts[value.callback_id]['entry_time'] = value.entry_time;
                        callback_alerts[value.callback_id]['callback_time'] = value.callback_time;
                        callback_alerts[value.callback_id]['campaign_id'] = value.campaign_id;
                        callback_alerts[value.callback_id]['comments'] = value.comments;
                        callback_alerts[value.callback_id]['seen'] = value.seen;
                    }
                });
                $("#callback-list").css('width', '100%');
                $("#callback-list").DataTable({
                <?php if( ECCS_BLIND_MODE === 'y'){ ?>
                    "drawCallback": function(){
                        var paginateLength =  $('#callback-list_paginate ul').children().length;
                        for(var a = 1; a <= paginateLength; a++){
                                $('#callback-list_paginate li.fg-button.ui-button a[data-dt-idx="'+ a +'"]').attr("title", a);
                        }

                        $('li#callback-list_previous').attr('title', 'Previous');
                        $('li#callback-list_next a').attr('title', 'Next');
                    },
		    "scrollX": true,
                <?php } ?>
                    "bDestroy": true,
                    "aoColumnDefs": [{
                        "bSortable": false,
                        "aTargets": [ 6 ],
                    }, {
                        "bSearchable": false,
                        "aTargets": [ 2, 3, 6 ]
                    }, 
		    <?php if ( ECCS_BLIND_MODE === 'y' ) { ?>
		    {
                        "sClass": "visible-lg",
                        "aTargets": [ 0, 2, 4 ]
                    }
		    <?php } else { ?>
		    {
                        "sClass": "hidden-xs",
                        "aTargets": [ 0 ]
                    }, {
                        "sClass": "hidden-xs hidden-sm",
                        "aTargets": [ 3 ]
                    }, {
                        "sClass": "visible-md visible-lg",
                        "aTargets": [ 4 ]
                    }, {
                        "sClass": "visible-lg",
                        "aTargets": [ 2, 5 ]
		    }
                   <?php  } ?>
		    ]
                });
                $("#callback-list_filter").parent('div').attr('class', 'col-sm-6 hidden-xs');
                $("#callback-list_length").parent('div').attr('class', 'col-xs-12 col-sm-6');
                $("#contents-callbacks").find("div.dataTables_info").parent('div').attr('class', 'col-xs-12 col-sm-6');
                $("#contents-callbacks").find("div.dataTables_paginate").parent('div').attr('class', 'col-xs-12 col-sm-6');
                if (!is_logged_in || (is_logged_in && (use_webrtc && !phoneRegistered))) {
                    $("button[id^='dial-cb-']").addClass('disabled');
                }
                
                $('#callback-list').on('draw.dt', function() {
                    if (!is_logged_in || (is_logged_in && (use_webrtc && !phoneRegistered))) {
                        $("button[id^='dial-cb-']").addClass('disabled');
                    } else {
                        $("button[id^='dial-cb-']").removeClass('disabled');
                    }
                });
            } else {
                if (!is_logged_in || (is_logged_in && (use_webrtc && !phoneRegistered))) {
                    $("button[id^='dial-cb-']").addClass('disabled');
                } else {
                    $("button[id^='dial-cb-']").removeClass('disabled');
                }
            }
            
            $("a:regex(href, index|agent|edituser|profile|customerslist|events|messages|notifications|tasks|callbackslist|composemail|readmail)").off('click', hijackThisLink).on('click', hijackThisLink);
        }
    });
}


// ################################################################################
// Open up a callback customer record as manual dial preview mode
function NewCallbackCall(taskCBid, taskLEADid, taskCBalt) {
    var move_on = 1;
    if (typeof taskCBalt == 'undefined' || taskCBalt == '') {
        taskCBalt = 'MAIN';
    }
    
    if ($(".sweet-alert.visible").length > 0) {
        swal.close();
    }
    
    if (($("#view-missed-callbacks").data('bs.modal') || {}).isShown) {
        $("#view-missed-callbacks").modal('hide');
    }
    
    if (callback_alert) {
        callback_alerts[taskCBid].seen = true;
        
        var postData = {
            goAction: 'goGetCallbackCount',
            goUser: uName,
            goPass: uPass,
            goSeen: true,
            goCampaign: campaign,
            goCallbackID: taskCBid,
            goNoExpire: cb_noexpire,
            responsetype: 'json'
        };
    
        $.ajax({
            type: 'POST',
            url: '<?=$goAPI?>/goAgent/goAPI.php',
            processData: true,
            data: postData,
            dataType: "json",
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            }
        })
        .done(function (result) {
            console.log(result);
            callback_alert = false;
        });
    }
    
    if ( (AutoDialWaiting == 1) || (live_customer_call == 1) || (alt_dial_active == 1) || (MD_channel_look == 1) || (in_lead_preview_state == 1) ) {
        if ( (auto_pause_precall == 'Y') && ( (agent_pause_codes_active == 'Y') || (agent_pause_codes_active == 'FORCE') ) && (AutoDialWaiting == 1) && (live_customer_call != 1) && (alt_dial_active != 1) && (MD_channel_look != 1) && (in_lead_preview_state != 1) ) {
            agent_log_id = AutoDial_ReSume_PauSe("VDADpause", '', '', '', '', '1', auto_pause_precall_code);
        } else {
            move_on = 0;
            swal("<?=$lh->translationFor('must_be_paused_to_check_callbacks')?>");
        }
    }
    if (move_on == 1) {
        if (waiting_on_dispo > 0) {
            swal({
                title: "<?=$lh->translationFor('system_delay_try_again')?>",
                text: "<?=$lh->translationFor('code')?>: " + agent_log_id + " - " + waiting_on_dispo,
                type: 'error'
            });
        } else {
            //alt_phone_dialing = 1;
            LastCallbackViewed = 1;
            LastCallbackCount = (LastCallbackCount - 1);
            auto_dial_level = 0;
            manual_dial_in_progress = 1;
            MainPanelToFront();
            if (alt_phone_dialing == 1) {
                $("#DialALTPhoneMenu").show();
            }
            $("#LeadPreview").prop('checked', false);
            //$("#DialALTPhone").prop('checked', true);
            ManualDialNext(taskCBid,taskLEADid,'','','','0','',taskCBalt);
        }
    }
}


// ################################################################################
// Update Agent screen with values from vicidial_list record
function UpdateFieldsData() {
    var fields_list = update_fields_data + ',';
    update_fields = 0;
    update_fields_data = '';
    
    var postData = {
        goAction: 'goUpdateFields',
        goUser: uName,
        goPass: uPass,
        goSessionName: session_name,
        goServerIP: server_ip,
        goConfExten: session_id,
        goStage: update_fields_data,
        responsetype: 'json'
    };

    $.ajax({
        type: 'POST',
        url: '<?=$goAPI?>/goAgent/goAPI.php',
        processData: true,
        data: postData,
        dataType: "json",
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
        }
    })
    .done(function (result) {
        var UDfieldsResponse = null;
        UDfieldsData = result.data;
        
        if (UDfieldsData.status == 'GOOD') {
            var regUDvendor_lead_code = new RegExp("vendor_lead_code,","ig");
            if (fields_list.match(regUDvendor_lead_code))
                {$(".formMain input[name='vendor_lead_code']").val(UDfieldsData.vendor_id);}
            var regUDsource_id = new RegExp("source_id,","ig");
            if (fields_list.match(regUDsource_id))
                {source_id = UDfieldsData.source_id;}
            var regUDgmt_offset_now = new RegExp("gmt_offset_now,","ig");
            if (fields_list.match(regUDgmt_offset_now))
                {$(".formMain input[name='gmt_offset_now']").val(UDfieldsData.gmt_offset);}
            var regUDphone_code = new RegExp("phone_code,","ig");
            if (fields_list.match(regUDphone_code))
                {$(".formMain input[name='phone_code']").val(UDfieldsData.phone_code);}
            var regUDphone_number = new RegExp("phone_number,","ig");
            if (fields_list.match(regUDphone_number)) {
                if ( (disable_alter_custphone == 'Y') || (disable_alter_custphone == 'HIDE') ) {
                    var tmp_pn = $("#phone_numberDISP");
                    if (disable_alter_custphone == 'Y') {
                        tmp_pn.html(UDfieldsData.phone_number);
                        $("#phone_number_DISP").val(UDfieldsData.phone_number);
                    }
                }
                $(".formMain input[name='phone_number']").val(UDfieldsData.phone_number);
            }
            var regUDtitle = new RegExp("title,","ig");
            if (fields_list.match(regUDtitle))
                {$(".formMain input[name='title']").val(UDfieldsData.title);}
            var regUDfirst_name = new RegExp("first_name,","ig");
            if (fields_list.match(regUDfirst_name))
                {$("#cust_full_name a[id='first_name']").editable('setValue', UDfieldsData.first_name, true);}
            var regUDmiddle_initial = new RegExp("middle_initial,","ig");
            if (fields_list.match(regUDmiddle_initial))
                {$("#cust_full_name a[id='middle_initial']").editable('setValue', UDfieldsData.middle_initial, true);}
            var regUDlast_name = new RegExp("last_name,","ig");
            if (fields_list.match(regUDlast_name))
                {$("#cust_full_name a[id='last_name']").editable('setValue', UDfieldsData.last_name, true);}
            var regUDaddress1 = new RegExp("address1,","ig");
            if (fields_list.match(regUDaddress1))
                {$(".formMain input[name='address1']").val(UDfieldsData.address1);}
            var regUDaddress2 = new RegExp("address2,","ig");
            if (fields_list.match(regUDaddress2))
                {$(".formMain input[name='address2']").val(UDfieldsData.address2);}
            var regUDaddress3 = new RegExp("address3,","ig");
            if (fields_list.match(regUDaddress3))
                {$(".formMain input[name='address3']").val(UDfieldsData.address3);}
            var regUDcity = new RegExp("city,","ig");
            if (fields_list.match(regUDcity))
                {$(".formMain input[name='city']").val(UDfieldsData.city);}
            var regUDstate = new RegExp("state,","ig");
            if (fields_list.match(regUDstate))
                {$(".formMain input[name='state']").val(UDfieldsData.state);}
            var regUDprovince = new RegExp("province,","ig");
            if (fields_list.match(regUDprovince))
                {$(".formMain input[name='province']").val(UDfieldsData.province);}
            var regUDpostal_code = new RegExp("postal_code,","ig");
            if (fields_list.match(regUDpostal_code))
                {$(".formMain input[name='postal_code']").val(UDfieldsData.postal_code);}
            var regUDcountry_code = new RegExp("country_code,","ig");
            if (fields_list.match(regUDcountry_code))
                {$(".formMain select[name='country_code']").val(UDfieldsData.country_code);}
            var regUDgender = new RegExp("gender,","ig");
            if (fields_list.match(regUDgender)) {
                $(".formMain select[name='gender']").val(UDfieldsData.gender);
                if (hide_gender > 0) {
                    //document.vicidial_form.gender_list.value		= UDfieldsResponse_array[18];
                } else {
                    var gIndex = 0;
                    //if (document.vicidial_form.gender.value == 'M') {var gIndex = 1;}
                    //if (document.vicidial_form.gender.value == 'F') {var gIndex = 2;}
                    //document.getElementById("gender_list").selectedIndex = gIndex;
                    //var genderIndex = document.getElementById("gender_list").selectedIndex;
                    //var genderValue =  document.getElementById('gender_list').options[genderIndex].value;
                    //document.vicidial_form.gender.value = genderValue;
                }
            }
            var regUDdate_of_birth = new RegExp("date_of_birth,","ig");
            if (fields_list.match(regUDdate_of_birth)) {
                var dateOfBirth = UDfieldsData.date_of_birth;
                $(".formMain input[name='date_of_birth']").val(dateOfBirth);
            }
            var regUDalt_phone = new RegExp("alt_phone,","ig");
            if (fields_list.match(regUDalt_phone))
                {$(".formMain input[name='alt_phone']").val(UDfieldsData.alt_phone);}
            var regUDemail = new RegExp("email,","ig");
            if (fields_list.match(regUDemail))
                {$(".formMain input[name='email']").val(UDfieldsData.email);}
            var regUDsecurity_phrase = new RegExp("security_phrase,","ig");
            if (fields_list.match(regUDsecurity_phrase))
                {$(".formMain input[name='security_phrase']").val(UDfieldsData.security);}
            var regUDcomments = new RegExp("comments,","ig");
            if (fields_list.match(regUDcomments)) {
                var REGcommentsNL = new RegExp("!N!","g");
                var UDfieldComments = UDfieldsData.comments;
                if (typeof UDfieldComments !== 'undefined') {
                    UDfieldComments = UDfieldComments.replace(REGcommentsNL, "\n");
                }
                $(".formMain textarea[name='comments']").val(UDfieldComments);
            }
            var regUDrank = new RegExp("rank,","ig");
            if (fields_list.match(regUDrank))
                {$(".formMain input[name='rank']").val(UDfieldsData.rank);}
            var regUDowner = new RegExp("owner,","ig");
            if (fields_list.match(regUDowner))
                {$(".formMain input[name='owner']").val(UDfieldsData.owner);}
            var regUDformreload = new RegExp("formreload,","ig");
            if (fields_list.match(regUDformreload))
                {FormContentsLoad();}

            // JOEJ 082812 - new for email feature
            //var regUDemailreload = new RegExp("emailreload,","ig");
            //if (fields_list.match(regUDemailreload))
            //    {EmailContentsLoad();}

            var VDIC_web_form_address = web_form_address;
            var VDIC_web_form_address_two = web_form_address_two;
            var regWFAcustom = new RegExp("^VAR","ig");
            if (VDIC_web_form_address.match(regWFAcustom)) {
                TEMP_VDIC_web_form_address = URLDecode(VDIC_web_form_address, 'YES', 'CUSTOM');
                TEMP_VDIC_web_form_address = TEMP_VDIC_web_form_address.replace(regWFAcustom, '');
            } else {
                TEMP_VDIC_web_form_address = URLDecode(VDIC_web_form_address, 'YES', 'DEFAULT', '1');
            }

            if (VDIC_web_form_address_two.match(regWFAcustom)) {
                TEMP_VDIC_web_form_address_two = URLDecode(VDIC_web_form_address_two, 'YES', 'CUSTOM');
                TEMP_VDIC_web_form_address_two = TEMP_VDIC_web_form_address_two.replace(regWFAcustom, '');
            } else {
                TEMP_VDIC_web_form_address_two = URLDecode(VDIC_web_form_address_two, 'YES', 'DEFAULT', '2');
            }

            if (VDIC_web_form_address.length > 0) {
                $("#openWebForm").removeClass('disabled');
                //document.getElementById("WebFormSpan").innerHTML = "<a href=\"" + TEMP_VDIC_web_form_address + "\" target=\"" + web_form_target + "\" onMouseOver=\"WebFormRefresH();\" style=\"font-size:13px;color:white;text-decoration:none;\"><?=$lang['web_form']?></a>";
            }
                                            
            if (enable_second_webform > 0 && VDIC_web_form_address_two.length > 0) {
                $("#openWebFormTwo").removeClass('disabled');
                //document.getElementById("WebFormSpanTwo").innerHTML = "<a href=\"" + TEMP_VDIC_web_form_address_two + "\" target=\"" + web_form_target + "\" onMouseOver=\"WebFormTwoRefresH();\" style=\"font-size:13px;color:white;text-decoration:none;\"><?=$lang['web_form_two']?></a>";
            }
        } else {
            swal({
                title: "<?=$lh->translationFor('update_fields_error')?>",
                text: result.message,
                type: 'error'
            });
        }
    });
}


// ################################################################################
// Refresh the FORM content
function FormContentsLoad() {
    var form_list_id = $(".formMain input[name='list_id']").val();
    var form_entry_list_id = $(".formMain input[name='entry_list_id']").val();
    if (form_entry_list_id.length > 2)
        {form_list_id = form_entry_list_id}
    //document.getElementById('vcFormIFrame').src='./vdc_form_display.php?lead_id=' + document.vicidial_form.lead_id.value + '&list_id=' + form_list_id + '&user=' + user + '&pass=' + pass + '&campaign=' + campaign + '&server_ip=' + server_ip + '&session_id=' + '&uniqueid=' + document.vicidial_form.uniqueid.value + '&stage=DISPLAY' + "&campaign=" + campaign + "&phone_login=" + phone_login + "&original_phone_login=" + original_phone_login +"&phone_pass=" + phone_pass + "&fronter=" + fronter + "&closer=" + user + "&group=" + group + "&channel_group=" + group + "&SQLdate=" + SQLdate + "&epoch=" + UnixTime + "&uniqueid=" + document.vicidial_form.uniqueid.value + "&customer_zap_channel=" + lastcustchannel + "&customer_server_ip=" + lastcustserverip +"&server_ip=" + server_ip + "&SIPexten=" + extension + "&session_id=" + session_id + "&phone=" + document.vicidial_form.phone_number.value + "&parked_by=" + document.vicidial_form.lead_id.value +"&dispo=" + LeaDDispO + '' +"&dialed_number=" + dialed_number + '' +"&dialed_label=" + dialed_label + '' +"&camp_script=" + campaign_script + '' +"&in_script=" + CalL_ScripT_id + '' +"&script_width=" + script_width + '' +"&script_height=" + script_height + '' +"&fullname=" + LOGfullname + '' +"&recording_filename=" + recording_filename + '' +"&recording_id=" + recording_id + '' +"&user_custom_one=" + VU_custom_one + '' +"&user_custom_two=" + VU_custom_two + '' +"&user_custom_three=" + VU_custom_three + '' +"&user_custom_four=" + VU_custom_four + '' +"&user_custom_five=" + VU_custom_five + '' +"&preset_number_a=" + CalL_XC_a_NuMber + '' +"&preset_number_b=" + CalL_XC_b_NuMber + '' +"&preset_number_c=" + CalL_XC_c_NuMber + '' +"&preset_number_d=" + CalL_XC_d_NuMber + '' +"&preset_number_e=" + CalL_XC_e_NuMber + '' +"&preset_dtmf_a=" + CalL_XC_a_Dtmf + '' +"&preset_dtmf_b=" + CalL_XC_b_Dtmf + '' +"&did_id=" + did_id + '' +"&did_extension=" + did_extension + '' +"&did_pattern=" + did_pattern + '' +"&did_description=" + did_description + '' +"&closecallid=" + closecallid + '' +"&xfercallid=" + xfercallid + '' + "&agent_log_id=" + agent_log_id + "&call_id=" + LastCID + "&user_group=" + VU_user_group + '' +"&web_vars=" + LIVE_web_vars + '';
    form_list_id = '';
    form_entry_list_id = '';
}


// clear api field
function Clear_API_Field(temp_field) {
    var postData = {
        goServerIP: server_ip,
        goSessionName: session_name,
        goAction: "goClearAPIField",
        goComments: temp_field,
        goUser: uName,
        goPass: uPass,
        responsetype: 'json'
    };
        
    $.ajax({
        type: 'POST',
        url: '<?=$goAPI?>/goAgent/goAPI.php',
        processData: true,
        data: postData,
        dataType: "json",
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
        }
    })
    .done(function (result) {
		//alert(result.result);
	});
}

function ManualDialCheckChannel(taskCheckOR) {
    var CIDcheck = MDnextCID;
    if (taskCheckOR == 'YES') {
        var CIDcheck = XDnextCID;
    } else {
        var CIDcheck = MDnextCID;
    }
    var postData = {
        goServerIP: server_ip,
        goSessionName: session_name,
        goAction: "goManualDialLookCall",
        goConfExten: session_id,
        goUser: uName,
        goPass: uPass,
        goMDnextCID: CIDcheck,
        goAgentLogID: agent_log_id,
        goLeadID: $(".formMain input[name='lead_id']").val(),
        goDialSeconds: MD_ring_seconds,
        goStage: taskCheckOR,
        responsetype: 'json'
    };
        
    $.ajax({
        type: 'POST',
        url: '<?=$goAPI?>/goAgent/goAPI.php',
        processData: true,
        data: postData,
        dataType: "json",
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
        }
    })
    .done(function (result) {
        var this_MD_data = result.data;
        var MDlookCID = result.lookCID;
        var regMDL = new RegExp("^Local","ig");
        
        if (MDlookCID == "NO") {
            MD_ring_seconds++;
            dispnum = lead_dial_number;
            var status_display_number = phone_number_format(dispnum);
            
            var status_display_content = '';
            if (alt_dial_status_display == 0) {
                if (status_display_CALLID > 0) {status_display_content += "<br><b><?=$lh->translationFor('uid')?>:</b> " + CIDcheck;}
                if (status_display_LEADID > 0) {status_display_content += "<br><b><?=$lh->translationFor('lead_id')?>:</b> " + $(".formMain input[name='lead_id']").val();}
                if (status_display_LISTID > 0) {status_display_content += "<br><b><?=$lh->translationFor('list_id')?>:</b> " + $(".formMain input[name='list_id']").val();}
                
                $("#MainStatusSpan").html("<b><?=$lh->translationFor('calling')?>:</b> " + status_display_number + " " + status_display_content + " <br><?=$lh->translationFor('waiting_for_ring')?>... " + MD_ring_seconds + " <?=$lh->translationFor('seconds')?>");
            }
        } else {
            if (taskCheckOR == 'YES') {
                XDuniqueid = this_MD_data.uniqueid;
                XDchannel = this_MD_data.channel;
                var XDalert = this_MD_data.MDalert;
                
                if (XDalert == 'ERROR') {
                    var XDerrorDesc = this_MD_data.MDerrorDesc;
                    var XDerrorDescSIP = this_MD_data.MDerrorDescSIP;
                    swal({
                        title: "<?=$lh->translationFor('call_rejected')?>",
                        text: XDchannel + "<br>" + XDerrorDescSIP + "<br>" + XDerrorDescSIP,
                        type: 'warning',
                        html: true
                    });
                }
                if ( (XDchannel.match(regMDL)) && (asterisk_version != '1.0.8') && (asterisk_version != '1.0.9') && (MD_ring_seconds < 10) ) {
                    // bad grab of Local channel, try again
                    MD_ring_seconds++;
                } else {
                    $(".formXFER input[name='xferuniqueid']").val(this_MD_data.uniqueid);
                    $(".formXFER input[name='xferchannel']").val(this_MD_data.channel);
                    lastxferchannel = this_MD_data.channel;
                    $(".formXFER input[name='xferlength']").val(0);

                    XD_live_customer_call = 1;
                    XD_live_call_seconds = 0;
                    MD_channel_look = 0;

                    var called3rdparty = $(".formXFER input[name='xfernumber']").val();
                    if (hide_xfer_number_to_dial == 'ENABLED')
                        {called3rdparty = ' ';}
                    var status_display_content = '';
                    if (status_display_CALLID > 0) {status_display_content = status_display_content + " <?=$lh->translationFor('uid')?>: " + CIDcheck;}
                    if (status_display_LEADID > 0) {status_display_content = status_display_content + " <?=$lh->translationFor('lead')?>: " + $(".formMain input[name='lead_id']").val();}
                    if (status_display_LISTID > 0) {status_display_content = status_display_content + " <?=$lh->translationFor('list')?>: " + $(".formMain input[name='list_id']").val();}

                    $("#MainStatuSSpan").html("<?=$lh->translationFor('called_3rd_party')?>: " + called3rdparty + " " + status_display_content);

                    toggleButton('Leave3WayCall', 'on');

                    toggleButton('DialWithCustomer', 'off');

                    toggleButton('ParkCustomerDial', 'off');

                    toggleButton('HangupXferLine', 'on');
                    $("#btnHangupXferLine").attr('onclick', "XFerCallHangup(); return false;");

                    toggleButton('HangupBothLines', 'on');

                    xferchannellive = 1;
                    XDcheck = '';
                }
            } else {
                MDuniqueid = this_MD_data.uniqueid;
                MDchannel = this_MD_data.channel;
                var MDalert = this_MD_data.MDalert;
                
                if (MDalert == "ERROR") {
                    var MDerrorDesc = this_MD_data.MDerrorDesc;
                    var MDerrorDescSIP = this_MD_data.MDerrorDescSIP;
                    swal({
                        title: "<?=$lh->translationFor('call_rejected')?>",
                        text: MDchannel + "<br>" + MDerrorDesc + "<br>" + MDerrorDescSIP,
                        type: 'warning',
                        html: true
                    });
                }
                
                if ( (MDchannel.match(regMDL)) && (asterisk_version != '1.0.8') && (asterisk_version != '1.0.9') ) {
                    // bad grab of Local channel, try again
                    MD_ring_seconds++;
                } else {
                    dialingINprogress = 0;
                    custchannellive = 1;
                    
                    $(".formMain input[name='uniqueid']").val(this_MD_data.uniqueid);
                    $("#callchannel").html(this_MD_data.channel);
                    lastcustchannel = this_MD_data.channel;
                    
                    toggleStatus('LIVE');
                    $("#call_length").html("0");
                    $("#session_id").html(session_id);
                    
                    dispnum = lead_dial_number;
                    var status_display_number = phone_number_format(dispnum);
                    
                    live_customer_call = 1;
                    live_call_seconds = 0;
                    MD_channel_look = 0;
                    var status_display_content = '';
                    if (status_display_CALLID > 0) {status_display_content += "<br><b><?=$lh->translationFor('uid')?>:</b> " + CIDcheck;}
                    if (status_display_LEADID > 0) {status_display_content += "<br><b><?=$lh->translationFor('lead_id')?>:</b> " + $(".formMain input[name='lead_id']").val();}
                    if (status_display_LISTID > 0) {status_display_content += "<br><b><?=$lh->translationFor('list_id')?>:</b> " + $(".formMain input[name='list_id']").val();}
                    
                    $("#MainStatusSpan").html("<b><?=$lh->translationFor('called')?>:</b> " + status_display_number + " " + status_display_content);
                    
                    
                    toggleButton('ParkCall', 'park');
                    $("#btnParkCall").attr('onclick', "mainxfer_send_redirect('ParK','" + lastcustchannel + "','" + lastcustserverip + "'); toggleButton('TransferCall', 'off');");
                    if ( (ivr_park_call == 'ENABLED') || (ivr_park_call == 'ENABLED_PARK_ONLY') ) {
                        toggleButton('IVRParkCall', 'parkivr');
                        $("#btnIVRParkCall").attr('onclick', "mainxfer_send_redirect('ParKivr','" + lastcustchannel + "','" + lastcustserverip + "');");
                    }

                    toggleButton('DialHangup', 'hangup');

                    toggleButton('TransferCall', 'XFERON');

                    toggleButton('LocalCloser', 'on');
                    $("#btnLocalCloser").attr('onclick', "mainxfer_send_redirect('XfeRLOCAL','" + lastcustchannel + "','" + lastcustserverip + "');");

                    toggleButton('DialBlindTransfer', 'on');
                    $("#btnDialBlindTransfer").attr('onclick', "mainxfer_send_redirect('XfeRBLIND','" + lastcustchannel + "','" + lastcustserverip + "');");

                    toggleButton('DialBlindVMail', 'on');
                    $("#btnDialBlindVMail").attr('onclick', "mainxfer_send_redirect('XfeRVMAIL','" + lastcustchannel + "','" + lastcustserverip + "');");

                    //VolumeControl('UP','" + MDchannel + "','');return false;
                    //VolumeControl('DOWN','" + MDchannel + "','');return false;

                    if ( (quick_transfer_button == 'IN_GROUP') || (quick_transfer_button == 'LOCKED_IN_GROUP') ) {
                        quick_transfer_button_orig = '';
                        if (quick_transfer_button_locked > 0)
                            {quick_transfer_button_orig = default_xfer_group;}

                        toggleButton('QuickTransfer', 'on');
                        $("#btnQuickTransfer").attr('onclick', "mainxfer_send_redirect('XfeRLOCAL','" + lastcustchannel + "','" + lastcustserverip + "','','','" + quick_transfer_button_locked + "');return false;");
                    }
                    if (prepopulate_transfer_preset_enabled > 0) {
                        if ( (prepopulate_transfer_preset == 'PRESET_1') || (prepopulate_transfer_preset == 'LOCKED_PRESET_1') ) {
                            $(".formXFER input[name='xfernumber']").val(Call_XC_a_Number);
                            $(".formXFER input[name='xfername']").val('D1');
                        }
                        if ( (prepopulate_transfer_preset == 'PRESET_2') || (prepopulate_transfer_preset == 'LOCKED_PRESET_2') ) {
                            $(".formXFER input[name='xfernumber']").val(Call_XC_b_Number);
                            $(".formXFER input[name='xfername']").val('D2');
                        }
                        if ( (prepopulate_transfer_preset == 'PRESET_3') || (prepopulate_transfer_preset == 'LOCKED_PRESET_3') ) {
                            $(".formXFER input[name='xfernumber']").val(Call_XC_c_Number);
                            $(".formXFER input[name='xfername']").val('D3');
                        }
                        if ( (prepopulate_transfer_preset == 'PRESET_4') || (prepopulate_transfer_preset == 'LOCKED_PRESET_4') ) {
                            $(".formXFER input[name='xfernumber']").val(Call_XC_d_Number);
                            $(".formXFER input[name='xfername']").val('D4');
                        }
                        if ( (prepopulate_transfer_preset == 'PRESET_5') || (prepopulate_transfer_preset == 'LOCKED_PRESET_5') ) {
                            $(".formXFER input[name='xfernumber']").val(Call_XC_e_Number);
                            $(".formXFER input[name='xfername']").val('D5');
                        }
                    }
                    if ( (quick_transfer_button == 'PRESET_1') || (quick_transfer_button == 'PRESET_2') || (quick_transfer_button == 'PRESET_3') || (quick_transfer_button == 'PRESET_4') || (quick_transfer_button == 'PRESET_5') || (quick_transfer_button == 'LOCKED_PRESET_1') || (quick_transfer_button == 'LOCKED_PRESET_2') || (quick_transfer_button == 'LOCKED_PRESET_3') || (quick_transfer_button == 'LOCKED_PRESET_4') || (quick_transfer_button == 'LOCKED_PRESET_5') ) {
                        if ( (quick_transfer_button == 'PRESET_1') || (quick_transfer_button == 'LOCKED_PRESET_1') ) {
                            $(".formXFER input[name='xfernumber']").val(Call_XC_a_Number);
                            $(".formXFER input[name='xfername']").val('D1');
                        }
                        if ( (quick_transfer_button == 'PRESET_2') || (quick_transfer_button == 'LOCKED_PRESET_2') ) {
                            $(".formXFER input[name='xfernumber']").val(Call_XC_b_Number);
                            $(".formXFER input[name='xfername']").val('D2');
                        }
                        if ( (quick_transfer_button == 'PRESET_3') || (quick_transfer_button == 'LOCKED_PRESET_3') ) {
                            $(".formXFER input[name='xfernumber']").val(Call_XC_c_Number);
                            $(".formXFER input[name='xfername']").val('D3');
                        }
                        if ( (quick_transfer_button == 'PRESET_4') || (quick_transfer_button == 'LOCKED_PRESET_4') ) {
                            $(".formXFER input[name='xfernumber']").val(Call_XC_d_Number);
                            $(".formXFER input[name='xfername']").val('D4');
                        }
                        if ( (quick_transfer_button == 'PRESET_5') || (quick_transfer_button == 'LOCKED_PRESET_5') ) {
                            $(".formXFER input[name='xfernumber']").val(Call_XC_e_Number);
                            $(".formXFER input[name='xfername']").val('D5');
                        }
                        quick_transfer_button_orig = '';
                        if (quick_transfer_button_locked > 0)
                            {quick_transfer_button_orig = $(".formXFER input[name='xfernumber']").val();}

                        toggleButton('QuickTransfer', 'on');
                        $("#btnQuickTransfer").attr('onclick', "mainxfer_send_redirect('XfeRBLIND','" + lastcustchannel + "','" + lastcustserverip + "','','','" + quick_transfer_button_locked + "');return false;");
                    }

                    if (custom_3way_button_transfer_enabled > 0) {
                        //custom_button_transfer();return false;
                        //Custom Transfer Button
                    }

                    if (call_requeue_button > 0) {
                        var CloserSelectChoices = $("#CloserSelectList").val();
                        var regCRB = new RegExp("AGENTDIRECT","ig");
                        if ( (CloserSelectChoices.match(regCRB)) || (closer_campaigns.match(regCRB)) ) {
                            toggleButton('ReQueueCall', 'on');
                            //call_requeue_launch();return false;
                        } else {
                            toggleButton('ReQueueCall', 'off');
                        }
                    }

                    // Build transfer pull-down list
                    var loop_ct = 0;
                    var live_Xfer_HTML = '';
                    var Xfer_Select = '';
                    while (loop_ct < XFgroupCOUNT) {
                        if (VARxferGroups[loop_ct] == LIVE_default_xfer_group)
                            {Xfer_Select = 'selected ';}
                        else {Xfer_Select = '';}
                        live_Xfer_HTML += "<option " + Xfer_Select + "value=\"" + VARxferGroups[loop_ct] + "\">" + VARxferGroups[loop_ct] + " - " + VARxferGroupsNames[loop_ct] + "</option>\n";
                        loop_ct++;
                    }
                    $("#transfer-local-closer").html(live_Xfer_HTML);
                    
                    activateLinks();
                    
                    // INSERT VICIDIAL_LOG ENTRY FOR THIS CALL PROCESS
                    DialLog("start");
                    lastcustserverip = '';
                }
            }
        }
    });
    
    if ( (MD_ring_seconds > 49) && (MD_ring_seconds > dial_timeout) ) {
        MD_channel_look = 0;
        MD_ring_seconds = 0;
        
        $("#MainStatusSpan").html('&nbsp;');
        swal("<?=$lh->translationFor('dial_timeout')?>.");

        if (taskCheckOR == 'YES') {
            toggleButton('DialWithCustomer', 'on');
            toggleButton('ParkCustomerDial', 'on');
        }
    }
}

// ################################################################################
// Insert the new manual dial as a lead and go to manual dial screen
function NewManualDialCall(tempDiaLnow) {
    if (waiting_on_dispo > 0) {
        swal({
            title: '<?=$lh->translationFor('error')?>',
            text: "<?=$lh->translationFor('system_delay_try_again')?><br><?=$lh->translationFor('code')?>:" + agent_log_id + " - " + waiting_on_dispo,
            type: 'error',
            html: true
        });
    } else {
        //hideDiv('NeWManuaLDiaLBox');

        var sending_group_alias = 0;
        var MDDiaLCodEform = $("#MDDiaLCodE").val();
        var MDPhonENumbeRform = $("#MDPhonENumbeR").val();
        var MDLeadIDform = $("#MDLeadID").val();
        var MDTypeform = $("#MDType").val();
        var MDDiaLOverridEform = $("#MDDiaLOverridE").val();
        var MDVendorLeadCode = $(".formMain input[name='vendor_lead_code']").val();
        var MDLookuPLeaD = 'new';
        if ($("#LeadLookUP").is(':checked'))
            {MDLookuPLeaD = 'lookup';}

        if (MDPhonENumbeRform == 'XXXXXXXXXX')
            {MDPhonENumbeRform = $("#MDPhonENumbeRHiddeN").val();}

        if (MDDiaLCodEform.length < 1)
            {MDDiaLCodEform = $(".formMain input[name='phone_code']").val();}

        if ( (MDDiaLOverridEform.length > 0) && (active_ingroup_dial.length < 1) ) {
            agent_dialed_number = 1;
            agent_dialed_type = 'MANUAL_OVERRIDE';
            BasicOriginateCall(session_id,'NO','YES',MDDiaLOverridEform,'YES','','1','0');
        } else {
            if (active_ingroup_dial.length < 1) {
                auto_dial_level = 0;
                manual_dial_in_progress = 1;
                agent_dialed_number = 1;
            }
            MainPanelToFront();

            if ( ($("#LeadPreview").prop('checked') || tempDiaLnow == 'PREVIEW') && (active_ingroup_dial.length < 1) ) {
                //alt_phone_dialing = 1;
                agent_dialed_type='MANUAL_PREVIEW';
                //buildDiv('DiaLLeaDPrevieW');
                if (alt_phone_dialing == 1) {
                    $("#DialALTPhoneMenu").show();
                }
                $("#LeadPreview").prop('checked', true);
                if (DefaultALTDial == 1) {
                    $("#DialALTPhone").prop('checked', true);
                }
            } else {
                agent_dialed_type = 'MANUAL_DIALNOW';
                $("#LeadPreview").prop('checked', false);
                $("#DialALTPhone").prop('checked', false);
            }
            if (active_group_alias.length > 1)
                {var sending_group_alias = 1;}

            ManualDialNext("",MDLeadIDform,MDDiaLCodEform,MDPhonENumbeRform,MDLookuPLeaD,MDVendorLeadCode,sending_group_alias,MDTypeform);
        }

        $("#MDPhonENumbeR").val('');
        $("#MDDiaLOverridE").val('');
        $("#MDLeadID").val('');
        $("#MDType").val('');
        $("#MDPhonENumbeRHiddeN").val('');
    }
}

// ################################################################################
// Fast version of manual dial
function NewManualDialCallFast() {
    var MDDiaLCodEform = $(".formMain input[name='phone_code']").val();
    var MDPhonENumbeRform = $(".formMain input[name='phone_number']").val();
    var MDVendorLeadCode = $(".formMain input[name='vendor_lead_code']").val();

    if ( (MDDiaLCodEform.length < 1) || (MDPhonENumbeRform.length < 5) ) {
        swal("<?=$lh->translationFor('must_enter_number_to_fdial')?>");
    } else {
        if (waiting_on_dispo > 0) {
            swal({
                title: '<?=$lh->translationFor('error')?>',
                text: "<?=$lh->translationFor('system_delay_try_again')?><br><?=$lh->translationFor('code')?>:" + agent_log_id + " - " + waiting_on_dispo,
                type: 'error',
                html: true
            });
        } else {
            var MDLookuPLeaD = 'new';
            if ($("#LeadLookUP").is(':checked'))
                {MDLookuPLeaD = 'lookup';}
        
            agent_dialed_number = 1;
            agent_dialed_type = 'MANUAL_DIALFAST';
            //alt_phone_dialing = 1;
            auto_dial_level = 0;
            manual_dial_in_progress = 1;
            MainPanelToFront();
            //buildDiv('DiaLLeaDPrevieW');
            if (alt_phone_dialing == 1) {
                $("#DialALTPhoneMenu").show();
            }
            $("#LeadPreview").prop('checked', false);
            //$("#DialALTPhone").prop('checked', true);
            ManualDialNext("","",MDDiaLCodEform,MDPhonENumbeRform,MDLookuPLeaD,MDVendorLeadCode,'0');
        }
    }
}

// ################################################################################
// Finish Callback and go back to original screen
function ManualDialFinished() {
    alt_phone_dialing = starting_alt_phone_dialing;
    auto_dial_level = starting_dial_level;
    MainPanelToFront();
    CallBacksCountCheck();
    manual_dial_in_progress = 0;
}


// ################################################################################
// place 3way and customer into other conference and fake-hangup the lines
function Leave3WayCall(tempvarattempt) {
    threeway_end = 0;
    leaving_threeway = 1;

    if (customerparked > 0) {
        toggleButton('ParkCall', 'grab');
        mainxfer_send_redirect('FROMParK',lastcustchannel,lastcustserverip);
    }

    mainxfer_send_redirect('3WAY','','',tempvarattempt);

//	if (threeway_end == '0') {
//		document.vicidial_form.xferchannel.value = '';
//		xfercall_send_hangup();
//
//		document.vicidial_form.callchannel.value = '';
//		document.vicidial_form.callserverip.value = '';
//		dialedcall_send_hangup();
//	}

    toggleStatus('NOLIVE');
}


// ################################################################################
// filter manual dialstring and pass on to originate call
function SendManualDial(taskFromConf) {
    conf_dialed = 1;
    var sending_group_alias = 0;
    // Dial With Customer button
    if (taskFromConf == 'YES') {
        xfer_in_call = 1;
        agent_dialed_number = '1';
        agent_dialed_type = 'XFER_3WAY';

        toggleButton('DialWithCustomer', 'off');

        toggleButton('ParkCustomerDial', 'off');
        
        toggleButton('Leave3WayCall', 'on');

        toggleButton('HangupBothLines', 'on');

        var manual_number = $(".formXFER input[name='xfernumber']").val();
        var manual_number_hidden = $(".formXFER input[name='xfernumhidden']").val();
        if ( (manual_number.length < 1) && (manual_number_hidden.length > 0) )
            {manual_number = manual_number_hidden;}
        var manual_string = manual_number.toString();
        var dial_conf_exten = session_id;
        threeway_cid = '';
        if (three_way_call_cid == 'CAMPAIGN')
            {threeway_cid = campaign_cid;}
        if (three_way_call_cid == 'AGENT_PHONE')
            {threeway_cid = outbound_cid;}
        if (three_way_call_cid == 'CUSTOMER')
            {threeway_cid = $(".formMain input[name='phone_number']").val();}
        if (three_way_call_cid == 'CUSTOM_CID')
            {threeway_cid = $(".formMain input[name='security_phrase']").val();}
        if (three_way_call_cid == 'AGENT_CHOOSE') {
            threeway_cid = cid_choice;
            if (active_group_alias.length > 1)
                {var sending_group_alias = 1;}
        }
    } else {
        var manual_number = $(".formXFER input[name='xfernumber']").val();
        var manual_string = manual_number.toString();
        var threeway_cid = '1';
        if (manual_dial_cid == 'AGENT_PHONE')
            {threeway_cid = outbound_cid;}
    }
    var regXFvars = new RegExp("XFER","g");
    if (manual_string.match(regXFvars)) {
        var donothing = 1;
    } else {
        if ($("#xferoverride").prop('checked') == false) {
            if (three_way_dial_prefix == 'X') {var temp_dial_prefix = '';}
            else {var temp_dial_prefix = three_way_dial_prefix;}
            if (omit_phone_code == 'Y') {var temp_phone_code = '';}
            else {var temp_phone_code = $(".formMain input[name='phone_code']").val();}
            
            if (temp_dial_prefix === '' && dial_prefix !== '') {
                temp_dial_prefix = dial_prefix;
            }

            if (manual_string.length > 7)
                {manual_string = temp_dial_prefix + "" + temp_phone_code + "" + manual_string;}
        } else {
            agent_dialed_type = 'XFER_OVERRIDE';
        }
        // due to a bug in Asterisk, these call variables do not actually work
        call_variables = '__vendor_lead_code=' + $(".formMain input[name='vendor_lead_code']").val() + ',__lead_id=' + $(".formMain input[name='lead_id']").val();
    }
    var sending_preset_name = $(".formXFER input[name='xfername']").val();
    if (taskFromConf == 'YES') {
        // give extra time for custom fields to commit before consultative transfers
        if ( ($("#consultativexfer").prop('checked') == true) && (custom_fields_enabled > 0) && (consult_custom_delay > 0) ) {
            if (consult_custom_wait >= consult_custom_delay) {
                consult_custom_go = 1;
                consult_custom_wait = 0;
            } else {
                CustomerData_update();
                consult_custom_wait++;
                consult_custom_sent++;
            }
        } else {
            consult_custom_go = 1;
            consult_custom_wait = 0;
        }

        if (consult_custom_go > 0) {
            BasicOriginateCall(manual_string,'NO','YES',dial_conf_exten,'NO',taskFromConf,threeway_cid,sending_group_alias,'',sending_preset_name,call_variables);
        }
    } else {
        BasicOriginateCall(manual_string,'NO','NO','','','',threeway_cid,sending_group_alias,sending_preset_name,call_variables);
    }

    MD_ring_seconds = 0;
}


// ################################################################################
// park customer and place 3way call
function XFERParkDial() {
    conf_dialed = 1;

    mainxfer_send_redirect('ParK', lastcustchannel, lastcustserverip);

    SendManualDial('YES');
}


// Hangup Calls
function DialedCallHangup(dispowindow, hotkeysused, altdispo, nodeletevdac) {
    if (VDCL_group_id.length > 1)
        {var group = VDCL_group_id;}
    else
        {var group = campaign;}
    var form_cust_channel = $("#callchannel").html();
    var form_cust_serverip = $("#callserverip").val();
    var customer_channel = lastcustchannel;
    var customer_server_ip = lastcustserverip;
    AgainHangupChannel = lastcustchannel;
    AgainHangupServer = lastcustserverip;
    AgainCallSeconds = live_call_seconds;
    AgainCallCID = CallCID;
    var process_post_hangup = 0;
    if ( (RedirectXFER < 1) && ( (MD_channel_look == 1) || (auto_dial_level == 0) ) ) {
        MD_channel_look = 0;
        //DialTimeHangup('MAIN');
    }
    if (form_cust_channel.length > 3) {
        var queryCID = "HLvdcW" + epoch_sec + user_abb;
        var hangupvalue = customer_channel;
        var postData = {
            goServerIP: server_ip,
            goSessionName: session_name,
            goAction: 'goHangupCall',
            goChannel: hangupvalue,
            goUser: uName,
            goPass: uPass,
            goCallServerIP: customer_server_ip,
            goAutoDialLevel: auto_dial_level,
            goQueryCID: queryCID,
            goCallCID: CallCID,
            goSeconds: live_call_seconds,
            goExten: session_id,
            goCampaign: group,
            goNoDeleteVDAC: nodeletevdac,
            goLogCampaign: campaign,
            goQMExtension: qm_extension,
            responsetype: 'json'
        };
        
        $.ajax({
            type: 'POST',
            url: '<?=$goAPI?>/goAgent/goAPI.php',
            processData: true,
            data: postData,
            dataType: "json",
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            }
        })
        .done(function (result) {
            NActiveExt = null;
            NActiveExt = result;
        });
        process_post_hangup = 1;
    } else {process_post_hangup = 1;}
    
    if (process_post_hangup == 1) {
        live_customer_call = 0;
        live_call_seconds = 0;
        MD_ring_seconds = 0;
        CallCID = '';
        MDnextCID = '';

        //UPDATE VICIDIAL_LOG ENTRY FOR THIS CALL PROCESS
        DialLog("end",nodeletevdac);
        conf_dialed = 0;
        if (dispowindow == 'NO') {
            open_dispo_screen = 0;
        } else {
            if (auto_dial_level == 0) {
                if ($("#DialALTPhone").is(':checked')) {
                    reselect_alt_dial = 1;
                    open_dispo_screen = 0;
                } else {
                    reselect_alt_dial = 0;
                    open_dispo_screen = 1;
                }
            } else {
                if ($("#DialALTPhone").is(':checked')) {
                    reselect_alt_dial = 1;
                    open_dispo_screen = 0;
                    auto_dial_level = 0;
                    manual_dial_in_progress = 1;
                    auto_dial_alt_dial = 1;
                } else {
                    reselect_alt_dial = 0;
                    open_dispo_screen = 1;
                }
            }
        }

        //DEACTIVATE CHANNEL-DEPENDANT BUTTONS AND VARIABLES
        $("#callchannel").html('');
        $("#callserverip").val('');
        lastcustchannel = '';
        lastcustserverip = '';
        MDchannel = '';
        if (post_phone_time_diff_alert_message.length > 10) {
            $("#post_phone_time_diff_span_contents").html("");
            //hideDiv('post_phone_time_diff_span');
            post_phone_time_diff_alert_message = '';
        }

        toggleStatus('NOLIVE');
        
        $("#openWebForm").addClass('disabled');
        //document.getElementById("WebFormSpan").innerHTML = "<a href=\"#\" style=\"font-size:13px;color:grey;text-decoration:none;\" /><?=$lang['web_form']?></a>";
        if (enable_second_webform > 0) {
            $("#openWebFormTwo").addClass('disabled');
            //document.getElementById("WebFormSpanTwo").innerHTML = "<a href=\"#\" style=\"font-size:13px;color:grey;text-decoration:none;\" /><?=$lang['web_form_two']?></a>";
        }
        //document.getElementById("ScriptButtonSpan").innerHTML = "<a href=\"#\" id=\"ScriptButtonSpan\" style=\"font-size:13px;color:grey;text-decoration:none;\"><?=ucwords($lang['script'])?></a>";
        //document.getElementById("CustomFormSpan").innerHTML = "<a href=\"#\" id=\"CustomFormSpan\" style=\"font-size:13px;color:grey;text-decoration:none;\" /><?=ucwords($lang['custom_form'])?></a>";
        
        toggleButton('ParkCall', 'park', false);
        //$("#btnParkCall").removeAttr('onclick');
        if ( (ivr_park_call == 'ENABLED') || (ivr_park_call == 'ENABLED_PARK_ONLY') ) {
            toggleButton('IVRParkCall', 'parkivr', false);
            //$("#btnIVRParkCall").removeAttr('onclick');
        }
        
        toggleButton('DialHangup', 'dial');
        toggleButton('TransferCall', 'off');
    
        toggleButton('LocalCloser', 'off');
        toggleButton('DialBlindTransfer', 'off');
        toggleButton('DialBlindVMail', 'off');
        //document.getElementById("VolumeUpSpan").innerHTML = "<img src=\"./images/vdc_volume_up_off.gif\" border=\"0\" />";
        //document.getElementById("VolumeDownSpan").innerHTML = "<img src=\"./images/vdc_volume_down_off.gif\" border=\"0\" />";
        
        if ($("#DialALTPhone").is(':checked')) {
            $("#MainStatusSpan").html("&nbsp;");
        }

        if (quick_transfer_button_enabled > 0) {
            //document.getElementById("QuickXfer").innerHTML = "<img src=\"./images/quicktransfer_OFF.png\" style=\"padding-bottom:3px;\" border=\"0\" alt=\"<?=$lang['quick_transfer']?>\" />";
        }

        if (custom_3way_button_transfer_enabled > 0) {
            //document.getElementById("CustomXfer").innerHTML = "<img src=\"./images/vdc_LB_customxfer_OFF.gif\" border=\"0\" alt=\"<?=$lang['custom_transfer']?>\" />";
        }

        if (call_requeue_button > 0) {
            toggleButton('ReQueueCall', 'off');
        }

        $("#custdatetime").html(' &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; ');

        if ( (auto_dial_level == 0) && (dial_method != 'INBOUND_MAN') ) {
            if ($("#DialALTPhone").is(':checked')) {
                reselect_alt_dial = 1;
                if (altdispo == 'ALTPH2') {
                    ManualDialOnly('ALTPhone');
                } else {
                    if (altdispo == 'ADDR3') {
                        ManualDialOnly('Address3');
                    } else {
                        if (hotkeysused == 'YES') {
                            alt_dial_active = 0;
                            alt_dial_status_display = 0;
                            reselect_alt_dial = 0;
	           <?php if( ECCS_BLIND_MODE === 'y'){ ?>
        	            if(altdispo == 'CALLBK'){
				manual_auto_hotkey = 0;
			    } else {
				manual_auto_hotkey = 2;
			    }
	           <?php } else { ?>
        	            manual_auto_hotkey = 2;
	           <?php } ?>
                        }
                    }
                }
            } else {
                if (hotkeysused == 'YES') {
                    alt_dial_active = 0;
                    alt_dial_status_display = 0;
	        <?php if( ECCS_BLIND_MODE === 'y'){ ?>
 		    if(altdispo == 'CALLBK'){
                	 manual_auto_hotkey = 0;
                    } else {
                         manual_auto_hotkey = 2;
                    }
           <?php } else { ?>
                    manual_auto_hotkey = 2;
           <?php } ?>
                } else {
                    toggleButton('DialHangup', 'dial');
                    //document.getElementById("DiaLControl").innerHTML = "<a href=\"#\" onclick=\"ManualDialNext('','','','','','0');\"><img src=\"./images/dialnext.png\" border=\"0\" title=\"<?=$lang['dial_next']?>\" alt=\"<?=$lang['dial_next']?>\" /></a>";
                }
                reselect_alt_dial = 0;
            }
        } else {
            if ($("#DialALTPhone").is(':checked')) {
                reselect_alt_dial = 1;
                if (altdispo == 'ALTPH2') {
                    ManualDialOnly('ALTPhone');
                } else {
                    if (altdispo == 'ADDR3') {
                        ManualDialOnly('Address3');
                    } else {
                        if (hotkeysused == 'YES') {
                            manual_auto_hotkey = 2;
                            alt_dial_active = 0;
                            alt_dial_status_display = 0;

                            //$("#MainStatusSpan").style.background = panel_bgcolor;
                            $("#MainStatusSpan").html('&nbsp;');
                            if (dial_method == "INBOUND_MAN") {
                                toggleButton('ResumePause', 'resume', false);
                                //document.getElementById("DiaLControl").innerHTML = "<img src=\"./images/pause_OFF.png\" border=\"0\" title=\"<?=$lang['pause']?>\" alt=\" <?=$lang['pause']?> \" /><br /><img src=\"./images/resume_OFF.png\" border=\"0\" title=\"<?=$lang['resume']?>\" alt=\"<?=$lang['resume']?>\" /><small>&nbsp;</small><img src=\"./images/dialnext_OFF.png\" border=\"0\" title=\"<?=$lang['dial_next']?>\" alt=\"<?=$lang['dial_next']?>\" />";
                            } else {
                                toggleButton('ResumePause', 'resume', false);
                                //document.getElementById("DiaLControl").innerHTML = DiaLControl_auto_HTML_OFF;
                            }
                            reselect_alt_dial = 0;
                        }
                    }
                }
            } else {
                //$("#MainStatusSpan").style.background = panel_bgcolor;
                if (dial_method == "INBOUND_MAN") {
                    toggleButton('ResumePause', 'resume', false);
                    //document.getElementById("DiaLControl").innerHTML = "<img src=\"./images/pause_OFF.png\" border=\"0\" title=\"<?=$lang['pause']?>\" alt=\" <?=$lang['pause']?> \" /><br /><img src=\"./images/resume_OFF.png\" border=\"0\" title=\"<?=$lang['resume']?>\" alt=\"<?=$lang['resume']?>\" /><small>&nbsp;</small><img src=\"./images/dialnext_OFF.png\" border=\"0\" title=\"<?=$lang['dial_next']?>\" alt=\"<?=$lang['dial_next']?>\" />";
                } else {
                    toggleButton('ResumePause', 'resume', false);
                    //document.getElementById("DiaLControl").innerHTML = DiaLControl_auto_HTML_OFF;
                }
                reselect_alt_dial = 0;
            }
        }

        btnTransferCall('OFF');
        if (custom_fields_launch == 'ONCALL') {
            GetCustomFields(null, false);
        }
        activateLinks();
    }
}


function DispoSelectBox() {
    $("#select-disposition").modal({
        keyboard: false,
        backdrop: 'static'
    });
    DispoSelectContent_create('','ReSET');
}

function DispoSelectContent_create(taskDSgrp,taskDSstage) {
    if (disable_dispo_screen > 0) {
        $("#DispoSelection").val(disable_dispo_status);
        DispoSelectSubmit();
    } else {
        if (customer_3way_hangup_dispo_message.length > 1) {
            $("#Dispo3wayMessage").html("<b>" + customer_3way_hangup_dispo_message + "</b>");
        }
        if (APIManualDialQueue > 0) {
            $("#DispoManualQueueMessage").html("<b><?=$lh->translationFor('manual_dial_queue_calls_waiting')?>: " + APIManualDialQueue + "</b>");
        }
        if (per_call_notes == 'ENABLED') {
            var test_notes = $("[name='call_notes_dispo']").val();
            if (test_notes.length > 0 && test_notes !== '')
                {$(".formMain textarea[name='call_notes']").val(test_notes);}
            $("#PerCallNotesContent").html("<b><font size='3'><?=$lh->translationFor('call_notes')?>: </font></b><br /><textarea name='call_notes_dispo' id='call_notes_dispo' rows='2' class='form-control ng-pristine ng-empty ng-invalid ng-invalid-required ng-touched textarea note-editor note-editor-margin'>" + $(".formMain textarea[name='call_notes']").val() + "</textarea><br>");
        } else {
            $("#PerCallNotesContent").html("<input type='hidden' name='call_notes_dispo' id='call_notes_dispo' value='' />");
        }

        AgentDispoing = 1;
        var CBflag = '';
        var statuses_ct_half = parseInt(statuses_count / 2);
        var dispo_HTML = "<script>";
            dispo_HTML = dispo_HTML + "$(function() {";
            dispo_HTML = dispo_HTML + "    $('[id^=dispo-add-]').click(function() {";
            dispo_HTML = dispo_HTML + "        var dispoID = $(this).attr('id');";
            dispo_HTML = dispo_HTML + "        DispoSelectContent_create(dispoID.replace('dispo-add-', ''), 'ADD');";
            //dispo_HTML = dispo_HTML + "alert($('#DispoSelection').val());";
            dispo_HTML = dispo_HTML + "    });";
            dispo_HTML = dispo_HTML + "    $('[id^=dispo-sel-]').click(function() {";
            dispo_HTML = dispo_HTML + "        if (minimizedDispo) {";
            dispo_HTML = dispo_HTML + "            minimizedDispo = false;";
            dispo_HTML = dispo_HTML + "            $.AdminLTE.options.controlSidebarOptions.minimizedDispo = false;";
            dispo_HTML = dispo_HTML + "            $('body').css('overflow-y', 'auto');";
            dispo_HTML = dispo_HTML + "            CustomerData_update();";
            dispo_HTML = dispo_HTML + "        }";
            dispo_HTML = dispo_HTML + "        DispoSelectSubmit();";
            dispo_HTML = dispo_HTML + "    });";
            dispo_HTML = dispo_HTML + "});";
            dispo_HTML = dispo_HTML + "</script>";
            dispo_HTML = dispo_HTML + "<table cellpadding='5' cellspacing='5' width='100%' style='-webkit-touch-callout: none; -webkit-user-select: none; -khtml-user-select: none; -moz-user-select: none; -ms-user-select: none; user-select: none; margin: 0 auto;'><tr><td colspan='2'>&nbsp; <b><?=$lh->translationFor('call_dispositions')?></b><br><br></td></tr><tr><td bgcolor='#FFFFFF' height='300px' width='auto' valign='top' class='DispoSelectA' style='white-space: nowrap;'>";
        var loop_ct = 0;
        if (hide_dispo_list < 1) {
            while (loop_ct < statuses_count) {
                var regCBstatus = new RegExp(' ' + statuses[loop_ct] + ' ', "ig");
                if (VARCBstatusesLIST.match(regCBstatus))
                    {CBflag = '*';}
                else
                    {CBflag = '';}
                //console.log(statuses[loop_ct], taskDSgrp);
                if (taskDSgrp == statuses[loop_ct]) {
                    dispo_HTML = dispo_HTML + "<span id='dispo-sel-"+statuses[loop_ct]+"'";
			<?php if(ECCS_BLIND_MODE === 'y'){ ?>
				dispo_HTML = dispo_HTML + " class='btn' style='background-color:#000;font-size:larger;font-weight:bolder;cursor:pointer;color:white;'";
			<?php }else{ ?>
		    		dispo_HTML = dispo_HTML + " style='background-color:#99FF99;cursor:pointer;color:#77a30a;'";
		    	<?php } ?>
		    dispo_HTML = dispo_HTML + ">&nbsp; <span class='hidden-xs'>" + statuses[loop_ct] + " - " + statuses_names[loop_ct] + "</span><span class='hidden-sm hidden-md hidden-lg'>" + statuses_names[loop_ct] + "</span> " + CBflag + " &nbsp;</span><br /><br />";
                } else {
                    dispo_HTML = dispo_HTML + "<span style='cursor:pointer;color:#77a30a;font-size:large;' ";
		    //ECCS Customization
			<?php if(ECCS_BLIND_MODE === 'y'){ ?>
			     dispo_HTML = dispo_HTML + " class='btn dispo-focus' data-tooltip='tooltip' title='"+statuses_names[loop_ct]+"' tabindex='0'";
		        <?php } ?>
//style='cursor:pointer;color:#77a30a;'
		    dispo_HTML = dispo_HTML + "id='dispo-add-"+statuses[loop_ct]+"' >&nbsp; <span class='hidden-xs'>" + statuses[loop_ct] + " - " + statuses_names[loop_ct] + "</span><span class='hidden-sm hidden-md hidden-lg'>" + statuses_names[loop_ct] + "</span></span> " + CBflag + " &nbsp;<br /><br />";
			//ECCS Customization
			<?php if(ECCS_BLIND_MODE === 'y'){ ?>
			    dispo_HTML = dispo_HTML + "<style> #dispo-add-"+statuses[loop_ct]+":focus{ background:yellow;color:#000!important;font-weight:bold; } </style>";
        		<?php } ?>
	        }
                if (loop_ct == statuses_ct_half && !isMobile) {
                    $("#pause_agent").show();
                    $("#pause_agent_xs").hide();
                    dispo_HTML = dispo_HTML + "</td><td bgcolor='#FFFFFF' height='300px' width='auto' valign='top' class='DispoSelectB' style='white-space: nowrap;'>";
                } else {
                    $("#pause_agent").hide();
                    $("#pause_agent_xs").show();
                }
                loop_ct++;
            }
		//ECCS Customization
		<?php if(ECCS_BLIND_MODE === 'y'){ ?>
		dispo_HTML = dispo_HTML + "<script> $('.dispo-focus').keypress(function (e) { var key = e.which; var focused_elem = $(':focus'); if(key == 13){ $(focused_elem).click(); $('#btn-dispo-submit').focus(); } }); </script>";
		<?php } ?>
        } else {
            dispo_HTML = dispo_HTML + "<?=$lh->translationFor('dispo_status_list_hidden')?><br /><br />";
        }
        dispo_HTML = dispo_HTML + "</td></tr></table>";

        if (taskDSstage == 'ReSET') {$("#DispoSelection").val('');}
        else {$("#DispoSelection").val(taskDSgrp);}

        $("#DispoSelectContent").html(dispo_HTML);
        if (focus_blur_enabled == 1) {
            //document.inert_form.inert_button.focus();
            //document.inert_form.inert_button.blur();
        }
        if (my_callback_option == 'CHECKED')
            {$("#CallBackOnlyMe").prop('checked', true);}
    }
}


function DispoSelectSubmit() {
    console.log('Disposing call...');
    if (VDCL_group_id.length > 1) {var group = VDCL_group_id;}
    else {var group = campaign;}

    leaving_threeway = 0;
    blind_transfer = 0;
    CheckDEADcallON = 0;
    currently_in_email = 0;
    customer_3way_hangup_counter = 0;
    customer_3way_hangup_counter_trigger = 0;
    waiting_on_dispo = 1;
    callchannel = '';
    callserverip = '';
    xferchannel = '';
    var dispo_error = 0;

    toggleButton('DialWithCustomer', 'on');
    toggleButton('ParkCustomerDial', 'on');
    toggleButton('HangupBothLines', 'on');

    var DispoChoice = $("#DispoSelection").val().toString();

    if (DispoChoice.length < 1) {
     	swal("<?=$lh->translationFor('must_select_disposition')?>.");
        //console.log("Dispo Choice: Must select disposition.");
    } else {
        if ($("#DialALTPhone").is(':checked') == true) {
            var man_status = "";
            alt_dial_status_display = 0;
        }
    
        var regCBstatus = new RegExp(' ' + DispoChoice + ' ',"ig");
        //console.log((VARCBstatusesLIST.match(regCBstatus)), (DispoChoice.length > 0), scheduled_callbacks, (DispoChoice != 'CBHOLD'));
        if ((VARCBstatusesLIST.match(regCBstatus)) && (DispoChoice.length > 0) && (scheduled_callbacks > 0) && (DispoChoice != 'CBHOLD')) {
            // Change Calendar date
            var d = new Date();
            var currDate = new Date(serverdate.getFullYear(), serverdate.getMonth(), serverdate.getDate(), serverdate.getHours(), serverdate.getMinutes() + 15);
            var selectedDate = moment(currDate).format('YYYY-MM-DD HH:mm:00');
            $("#date-selected").html(moment(currDate).format('dddd, MMMM Do YYYY, h:mm a'));
            $("#callback-date").val(selectedDate);
            if (agentonly_callbacks > 0) {
                $("#my_callback_only p, #my_callback_only div").show();
            } else {
                $("#my_callback_only p, #my_callback_only div").hide();
            }
            $("#select-disposition").modal('hide');
            $("#callback-datepicker").modal({
                keyboard: false,
                backdrop: 'static',
                show: true
            });
        } else {
            var postData = {
                goServerIP: server_ip,
                goSessionName: session_name,
                goAction: 'goUpdateDispo',
                goUser: uName,
                goPass: uPass,
                goDispoChoice: DispoChoice,
                goLeadID: $(".formMain input[name='lead_id']").val(),
                goCampaign: campaign,
                goAutoDialLevel: auto_dial_level,
                goAgentLogID: agent_log_id,
                goCallBackDateTime: CallBackDateTime,
                goListID: $(".formMain input[name='list_id']").val(),
                goRecipient: CallBackRecipient,
                goUseInternalDNC: use_internal_dnc,
                goUseCampaignDNC: use_campaign_dnc,
                goMDnextCID: LastCID,
                goStage: group,
                goPhoneNumber: cust_phone_number,
                goPhoneCode: cust_phone_code,
                goDialMethod: dial_method,
                goUniqueid: $(".formMain input[name='uniqueid']").val(),
                goCallBackLeadStatus: CallBackLeadStatus,
                goComments: encodeURIComponent(CallBackComments),
                goCustomFieldNames: custom_field_names,
                goCallNotes: encodeURIComponent($("[name='call_notes_dispo']").val()),
                goQMDispoCode: DispoQMcsCODE,
                goEmailEnabled: email_enabled,
                goSendEmail: cb_sendemail,
                responsetype: 'json'
            };
    
            $.ajax({
                type: 'POST',
                url: '<?=$goAPI?>/goAgent/goAPI.php',
                processData: true,
                data: postData,
                dataType: "json",
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                }
            })
            .done(function (result) {
                if (auto_dial_level < 1) {
                    if (result.result == 'success') {
                        agent_log_id = result.data.agent_log_id;
                    } else {
                        dispo_error++;
                        swal('<?=$lh->translationFor('dispo_leadid_not_valid')?>');
                    }
                }
                
                if (!!$.prototype.snackbar) {
                    if (result.result == 'success') {
                        $.snackbar({content: "<i class='fa fa-info-circle fa-lg text-success' aria-hidden='true'></i>&nbsp; " + result.message, timeout: 3000, htmlAllowed: true});
                    } else {
                        $.snackbar({content: "<i class='fa fa-exclamation-circle fa-lg text-warning' aria-hidden='true'></i>&nbsp; " + result.message, timeout: 3000, htmlAllowed: true});
                    }
                }
    
                waiting_on_dispo = 0;
            });
            
            //CLEAR ALL FORM VARIABLES
            $(".formMain input[name='lead_id']").val('');
            $(".formMain input[name='vendor_lead_code']").val('');
            $(".formMain input[name='list_id']").val('');
            $(".formMain input[name='entry_list_id']").val('');
            $(".formMain input[name='gmt_offset_now']").val('');
            $(".formMain input[name='phone_code']").val('');
            if ( (disable_alter_custphone == 'Y') || (disable_alter_custphone == 'HIDE') ) {
                var tmp_pn = $("#phone_numberDISP");
                tmp_pn.html(' &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; ');
                $("#phone_number_DISP").val('');
            }
            $(".formMain input[name='phone_number']").val('').trigger('change');
            $(".formMain input[name='title']").val('').trigger('change');
            $("#cust_full_name a[id='first_name']").editable('setValue', '   ', true);
            $("#cust_full_name a[id='middle_initial']").editable('setValue', '   ', true);
            $("#cust_full_name a[id='last_name']").editable('setValue', '   ', true);
	    <?php
                 if(ECCS_BLIND_MODE === 'y'){
            ?>
            $("#cust_campaign_name").html('');
	    $("#cust_call_type").html('');
	    <?php } ?>
            $(".formMain input[name='address1']").val('').trigger('change');
            $(".formMain input[name='address2']").val('').trigger('change');
            $(".formMain input[name='address3']").val('').trigger('change');
            $(".formMain input[name='city']").val('').trigger('change');
            $(".formMain input[name='state']").val('').trigger('change');
            $(".formMain input[name='province']").val('').trigger('change');
            $(".formMain input[name='postal_code']").val('').trigger('change');
            $(".formMain select[name='country_code']").val('').trigger('change');
            $(".formMain select[name='gender']").val('').trigger('change');
            $(".formMain input[name='date_of_birth']").val('');
            $(".formMain input[name='alt_phone']").val('').trigger('change');
            $(".formMain input[name='email']").val('').trigger('change');
            $(".formMain input[name='security_phrase']").val('');
            $(".formMain textarea[name='comments']").val('').trigger('change');
            $(".formMain input[name='audit_comments']").val('');
            if (qc_enabled > 0) {
                $(".formMain input[name='ViewCommentButton']").val('');
                $(".formMain input[name='audit_comments_button']").val('');
            }
            $(".formMain input[name='called_count']").val('');
            $(".formMain textarea[name='call_notes']").val('');
            $("[name='call_notes_dispo']").val('');
            $("#MainStatusSpan").html('&nbsp;');
            VDCL_group_id = '';
            fronter = '';
            inOUT = 'OUT';
            vtiger_callback_id = '0';
            recording_filename = '';
            recording_id = '';
            MDuniqueid = '';
            XDuniqueid = '';
            tmp_vicidial_id = '';
            EAphone_code = '';
            EAphone_number = '';
            EAalt_phone_notes = '';
            EAalt_phone_active = '';
            EAalt_phone_count = '';
            XDnextCID = '';
            XDcheck = '';
            MDnextCID = '';
            XD_live_customer_call = 0;
            XD_live_call_seconds = 0;
            xfer_in_call = 0;
            MD_channel_look = 0;
            MD_ring_seconds = 0;
            uniqueid_status_display = '';
            uniqueid_status_prefix = '';
            custom_call_id = '';
            API_selected_xfergroup = '';
            API_selected_callmenu = '';
            timer_action = '';
            timer_action_seconds = '';
            timer_action_mesage = '';
            timer_action_destination = '';
            did_pattern = '';
            did_id = '';
            did_extension = '';
            did_description = '';
            closecallid = '';
            xfercallid = '';
            custom_field_names = '';
            custom_field_values = '';
            custom_field_types = '';
            customerparked = 0;
            customerparkedcounter = 0;
            consult_custom_wait = 0;
            consult_custom_go = 0;
            consult_custom_sent = 0;
            $(".formXFER input[name='xfername']").val('');
            $(".formXFER input[name='xfernumhidden']").val('');
            //$("#debugbottomspan").html('');
            customer_3way_hangup_dispo_message = '';
            Dispo3wayMessage = '';
            DispoManualQueueMessage = '';
            //$("#ManualQueueNotice").html('');
            APIManualDialQueue_last = 0;
            $(".formMain input[name='FORM_LOADED']").val('0');
            CallBackLeadStatus = '';
            CallBackDateTime = '';
            CallBackRecipient = '';
            CallBackComments = '';
            DispoQMcsCODE = '';
            active_ingroup_dial = '';
            nocall_dial_flag = 'DISABLED';
            
            $("#SecondsDISP").html('0');
            
            //$("#cust_full_name").html('');
            $("#cust_full_name").addClass('hidden');
            $("#cust_number").empty();
	    <?php if(ECCS_BLIND_MODE === 'y'){ ?> $("span#span-cust-number").addClass("hidden");  $("#cust_number").val(''); <?php } ?>
            $("#cust_avatar").html(goGetAvatar());
            //goAvatar._init(goOptions);
            //console.log(goGetAvatar());
    
            //CLEAR ALL SUB FORM VARIABLES
            //$("#subForm").find(':input').each(function()
            //{
            //    var pattID = /(callchannel|xferchannel|LeadLookUP)/;
            //    var testInput = pattID.test($(this).attr('id'));
            //    if ( ! testInput ) {
            //        $(this).val('');
            //    }
            //});
            inbound_lead_search = 0;
    
            if (post_phone_time_diff_alert_message.length > 10) {
                $("#post_phone_time_diff_span_contents").html("");
                //hideDiv('post_phone_time_diff_span');
                post_phone_time_diff_alert_message = '';
            }
    
            if (manual_dial_in_progress == 1) {
                ManualDialFinished();
            }
    
            $("#select-disposition").modal('hide');
            AgentDispoing = 0;
    
            if ( (shift_logout_flag < 1) && (api_logout_flag < 1) ) {
                if (wrapup_waiting == 0) {
                    if ($("#DispoSelectStop").is(':checked')) {
                        if (auto_dial_level != '0') {
                            AutoDialWaiting = 0;
                            QUEUEpadding = 0;
                            AutoDial_Resume_Pause("VDADpause");
                        }
                        pause_calling = 1;
                        if (dispo_check_all_pause != '1') {
                            DispoSelectStop = false;
                            $("#DispoSelectStop").prop('checked', false);
                        }
                    } else {
                        if (auto_dial_level != '0') {
                            AutoDialWaiting = 1;
                            agent_log_id = AutoDial_Resume_Pause("VDADready","NEW_ID");
                        } else {
                            // trigger HotKeys manual dial automatically go to next lead
                            if (manual_auto_hotkey > 0) {
                                manual_auto_hotkey = 0;
                                ManualDialNext('','','','','','0');
                            }
                        }
                    }
                }
            } else {
                //if (shift_logout_flag > 0)
                //    {LogMeOut('SHIFT');}
                //else
                //    {LogMeOut('API');}
            }
            if (focus_blur_enabled == 1) {
                //$("#inert_button").focus();
                //$("#inert_button").blur();
            }
        }
    }
}

function ManualDialSkip() {
    if (manual_dial_in_progress == 1) {
        swal({
            title: "<?=$lh->translationFor('error')?>",
            text: "<?=$lh->translationFor('cannot_skip_call')?>",
            type: 'error',
        });
    } else {
        in_lead_preview_state = 0;
        if (dial_method == "INBOUND_MAN") {
            auto_dial_level = starting_dial_level;

            toggleButton('ResumePause', 'off');
            toggleButton('DialHangup', 'off');
        } else {
            toggleButton('DialHangup', 'off');
        }

        var postData = {
            goServerIP: server_ip,
            goSessionName: session_name,
            goAction: 'goManualDialSkip',
            goUser: uName,
            goPass: uPass,
            goLeadID: $(".formMain input[name='lead_id']").val(),
            goCampaign: campaign,
            goConfExten: session_id,
            goStage: previous_dispo,
            goCalledCount: previous_called_count,
            responsetype: 'json'
        };
        
        $.ajax({
            type: 'POST',
            url: '<?=$goAPI?>/goAgent/goAPI.php',
            processData: true,
            data: postData,
            dataType: "json",
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            }
        })
        .done(function (result) {
            if (result.result == 'success') {
                if (result.message == "LEAD NOT REVERTED") {
                    swal({
                        title: "<?=$lh->translationFor('error')?>",
                        text: "<?=$lh->translationFor('lead_not_reverted')?>",
                        type: 'error',
                    });
                } else {
                    $(".formMain input[name='lead_id']").val('');
                    $(".formMain input[name='vendor_lead_code']").val('');
                    $(".formMain input[name='list_id']").val('');
                    $(".formMain input[name='entry_list_id']").val('');
                    $(".formMain input[name='gmt_offset_now']").val('');
                    $(".formMain input[name='phone_code']").val('');
                    if ( (disable_alter_custphone == 'Y') || (disable_alter_custphone == 'HIDE') ) {
                        var tmp_pn = $("#phone_numberDISP");
                        tmp_pn.html(' &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; ');
                        $("#phone_number_DISP").val('');
                    }
                    $(".formMain input[name='phone_number']").val('');
                    $(".formMain input[name='title']").val('');
                    $("#cust_full_name a[id='first_name']").editable('setValue', '   ', true);
                    $("#cust_full_name a[id='middle_initial']").editable('setValue', '   ', true);
                    $("#cust_full_name a[id='last_name']").editable('setValue', '   ', true);
                    //$(".formMain input[name='first_name.value		='';
                    //$(".formMain input[name='middle_initial.value	='';
                    //$(".formMain input[name='last_name.value		='';
                    $(".formMain input[name='address1']").val('');
                    $(".formMain input[name='address2']").val('');
                    $(".formMain input[name='address3']").val('');
                    $(".formMain input[name='city']").val('');
                    $(".formMain input[name='state']").val('');
                    $(".formMain input[name='province']").val('');
                    $(".formMain input[name='postal_code']").val('');
                    $(".formMain select[name='country_code']").val('');
                    $(".formMain select[name='gender']").val('');
                    $(".formMain input[name='date_of_birth']").val('');
                    $(".formMain input[name='alt_phone']").val('');
                    $(".formMain input[name='email']").val('');
                    $(".formMain input[name='security_phrase']").val('');
                    $(".formMain input[name='comments']").val('');
                    $(".formMain input[name='audit_comments']").val('');
                    if (qc_enabled > 0) {
                        $(".formMain input[name='ViewCommentButton']").val('');
                        $(".formMain input[name='audit_comments_button']").val('');
                    }
                    $(".formMain input[name='called_count']").val('');
                    $(".formMain input[name='rank']").val('');
                    $(".formMain input[name='owner']").val('');
                    VDCL_group_id = '';
                    fronter = '';
                    previous_called_count = '';
                    previous_dispo = '';
                    custchannellive = 1;
                    
                    $("#cust_full_name").addClass('hidden');
                    $("#cust_number").empty();
		    <?php if(ECCS_BLIND_MODE === 'y'){ ?> $("span#span-cust-number").addClass("hidden");  $("#cust_number").val(''); <?php } ?>
                    $("#cust_avatar").html(goGetAvatar());
                    //goAvatar._init(goOptions);

                    if (post_phone_time_diff_alert_message.length > 10) {
                        $("#post_phone_time_diff_span_contents").html();
                        //hideDiv('post_phone_time_diff_span');
                        //post_phone_time_diff_alert_message = '';
                    }

                    $("#MainStatusSpan").html("<center><?=$lh->translationFor('lead_skipped')?></center>");

                    if (dial_method == "INBOUND_MAN") {
                        toggleButton('ResumePause', 'resume');
                        toggleButton('DialHangup', 'dial');
                    } else {
                        toggleButton('DialHangup', 'dial');
                    }
                    
                    ManualDialNext('','','','','','0');
                }
            }
            
            active_group_alias = '';
            cid_choice = '';
            prefix_choice = '';
            agent_dialed_number = '';
            agent_dialed_type = '';
            Call_Script_ID = '';
            //RefresHScript('CLEAR');
            ClearScript();
        });
    }
}


// ################################################################################
// Update vicidial_list lead record with all altered values from form
function CustomerData_update() {
    var REGcommentsAMP = new RegExp('&',"g");
    var REGcommentsQUES = new RegExp("\\?","g");
    var REGcommentsPOUND = new RegExp("\\#","g");
    var REGcommentsNEWLINE = new RegExp("\n","g");
    var REGcommentsRESULT = $(".formMain textarea[name='comments']").val();
        REGcommentsRESULT = REGcommentsRESULT.replace(REGcommentsAMP, "--AMP--");
        REGcommentsRESULT = REGcommentsRESULT.replace(REGcommentsQUES, "--QUES--");
        REGcommentsRESULT = REGcommentsRESULT.replace(REGcommentsPOUND, "--POUND--");

    var postData = {
        goAction: 'goUpdateLead',
        goUser: uName,
        goPass: uPass,
        goCampaign: campaign,
        goServerIP: server_ip,
        goSessionName: session_name,
        goComments: REGcommentsRESULT,
        goVendorLeadCode: $(".formMain input[name='vendor_lead_code']").val(),
        goPhoneNumber: $(".formMain input[name='phone_number']").val(),
        goTitle: $(".formMain input[name='title']").val(),
        goFirstName: $("#cust_full_name a[id='first_name']").editable('getValue', true),
        goMiddleInitial: $("#cust_full_name a[id='middle_initial']").editable('getValue', true),
        goLastName: $("#cust_full_name a[id='last_name']").editable('getValue', true),
        goAddress1: $(".formMain input[name='address1']").val(),
        goAddress2: $(".formMain input[name='address2']").val(),
        goAddress3: $(".formMain input[name='address3']").val(),
        goCity: $(".formMain input[name='city']").val(),
        goState: $(".formMain input[name='state']").val(),
        goProvince: $(".formMain input[name='province']").val(),
        goPostalCode: $(".formMain input[name='postal_code']").val(),
        goCountryCode: $(".formMain select[name='country_code']").val(),
        goGender: $(".formMain select[name='gender']").val(),
        goDateOfBirth: $(".formMain input[name='date_of_birth']").val(),
        goALTPhone: $(".formMain input[name='alt_phone']").val(),
        goEmail: $(".formMain input[name='email']").val(),
        goSecurity: $(".formMain input[name='security_phrase']").val(),
        goLeadID: $(".formMain input[name='lead_id']").val(),
        goCustomFields: '',
        responsetype: 'json'
    };
    
    if (custom_fields_enabled > 0) {
        var defaultFieldsArray = defaultFields.split(',');
        var custom_fields = '';
        $.each($(".formMain #custom_fields [id^='custom_']"), function() {
            var thisID = $(this).prop('id').replace(/^custom_/, '');
            var fieldType = $(this).data('type');
            var thisVal = $(this).val();
            if (defaultFieldsArray.indexOf(thisID) > -1) return true;
            if (/script|display|readonly/i.test(fieldType)) return true;
            
            switch (fieldType) {
                case "checkbox":
                case "radio":
                    thisID = thisID.replace('[]', '');
                    if ($(this).prop('checked')) {
                        if (typeof postData[thisID] === 'undefined') {
                            postData[thisID] = thisVal;
                        } else {
                            postData[thisID] += ',' + thisVal;
                        }
                    }
                    break;
                case "multi":
                    if (thisVal !== null) {
                        postData[thisID] = thisVal.join(',');
                    }
                    break;
                case "area":
                    thisVal = thisVal.replace(REGcommentsAMP, "--AMP--");
                    thisVal = thisVal.replace(REGcommentsQUES, "--QUES--");
                    thisVal = thisVal.replace(REGcommentsPOUND, "--POUND--");
                    postData[thisID] = thisVal;
                    break;
                default:
                    postData[thisID] = thisVal;
            }
            
            if (custom_fields.indexOf(thisID) < 0) {
                custom_fields += thisID + ',';
            }
        });
        postData['goCustomFields'] = custom_fields.slice(0,-1);
    }

    $.ajax({
        type: 'POST',
        url: '<?=$goAPI?>/goAgent/goAPI.php',
        processData: true,
        data: postData,
        dataType: "json",
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
        }
    })
    .done(function (result) {
        console.log('Customer data updated...');
        
        $(".formMain #custom_fields [id^='custom_']").val('');
        $(".formMain #custom_fields [id^='custom_']").prop('checked', false);
        $('.input-disabled').prop('disabled', true);
        $('.input-phone-disabled').prop('disabled', true);
        $('#cust_full_name .editable').editable('disable');
        $('.hide_div').hide();
        $("input:required, select:required").removeClass("required_div");
    });
}


// ################################################################################
// Call ReQueue call back to AGENTDIRECT queue launch
function btnReQueueCall() {
    $(".formXFER input[name='xfernumber']").val(user);

    // Build transfer pull-down list
    var loop_ct = 0;
    var live_Xfer_HTML = '';
    var Xfer_Select = '';
    while (loop_ct < XFgroupCOUNT) {
        if (VARxferGroups[loop_ct] == 'AGENTDIRECT')
            {Xfer_Select = 'selected ';}
        else {Xfer_Select = '';}
        live_Xfer_HTML += "<option " + Xfer_Select + "value=\"" + VARxferGroups[loop_ct] + "\">" + VARxferGroups[loop_ct] + " - " + VARxferGroupsNames[loop_ct] + "</option>\n";
        loop_ct++;
    }
    $("#transfer-local-closer").html(live_Xfer_HTML);

    mainxfer_send_redirect('XfeRLOCAL', lastcustchannel, lastcustserverip, '', 'NO');

    $("#DispoSelection").val('RQXFER');
    DispoSelectSubmit();

    AutoDial_Resume_Pause("VDADpause",'','','',"REQUEUE",'1','RQUEUE');

    //PauseCodeSelect_submit("RQUEUE");
}


// ################################################################################
// Send the Manual Dial Only - dial the previewed lead
function ManualDialOnly(taskaltnum) {
    in_lead_preview_state = 0;
    inOUT = 'OUT';
    alt_dial_status_display = 0;
    all_record = 'NO';
    all_record_count = 0;
    var usegroupalias = 0;
    if (taskaltnum == 'ALTPhone') {
        var manDialOnly_num = $(".formMain input[name='alt_phone']").val();
        lead_dial_number = $(".formMain input[name='alt_phone']").val();
        dialed_number = lead_dial_number;
        dialed_label = 'ALT';
        //WebFormRefresH('');
    } else {
        if (taskaltnum == 'Address3') {
            var manDialOnly_num = $(".formMain input[name='address3']").val();
            lead_dial_number = $(".formMain input[name='address3']").val();
            dialed_number = lead_dial_number;
            dialed_label = 'ADDR3';
            //WebFormRefresH('');
        } else {
            var manDialOnly_num = $(".formMain input[name='phone_number']").val();
            lead_dial_number = $(".formMain input[name='phone_number']").val();
            dialed_number = lead_dial_number;
            dialed_label = 'MAIN';
            //WebFormRefresH('');
        }
    }
    if (dialed_label == 'ALT')
        {$("#CustInfoSpan").html(" <b> <?=$lh->translationFor('alt_dial_number')?>: <?=$lh->translationFor('alt')?> </b>");}
    if (dialed_label == 'ADDR3')
        {$("#CustInfoSpan").html(" <b> <?=$lh->translationFor('alt_dial_number')?>: <?=$lh->translationFor('address3')?> </b>");}
    var REGalt_dial = new RegExp("X","g");
    if (dialed_label.match(REGalt_dial)) {
        $("#CustInfoSpan").html(" <b> <?=$lh->translationFor('alt_dial_number')?>: " + dialed_label + "</b>");
        $("#EAcommentsBoxA").html("<b><?=$lh->translationFor('phone_code_number')?>: </b>" + EAphone_code + " " + EAphone_number);

        var EAactive_link = '';
        if (EAalt_phone_active == 'Y') 
            {EAactive_link = "<a href=\"#\" onclick=\"alt_phone_change('" + EAphone_number + "','" + EAalt_phone_count + "','" + $(".formMain input[name='lead_id']").val() + "','N');\"><?=$lh->translationFor('change_number_to_inactive')?></a>";}
        else
            {EAactive_link = "<a href=\"#\" onclick=\"alt_phone_change('" + EAphone_number + "','" + EAalt_phone_count + "','" + $(".formMain input[name='lead_id']").val() + "','Y');\"><?=$lh->translationFor('change_number_to_active')?></a>";}

        $("#EAcommentsBoxB").html("<b><?=$lh->translationFor('active')?>: </b>" + EAalt_phone_active + "<br />" + EAactive_link);
        $("#EAcommentsBoxC").html("<b><?=$lh->translationFor('alt_count')?>: </b>" + EAalt_phone_count);
        $("#EAcommentsBoxD").html("<b><?=$lh->translationFor('notes')?>: </b><br />" + EAalt_phone_notes);
        //showDiv('EAcommentsBox');
    }

    if (cid_choice.length > 3) {
        var call_cid = cid_choice;
        usegroupalias = 1;
    } else {
        var call_cid = campaign_cid;
        if (manual_dial_cid == 'AGENT_PHONE')
            {call_cid = outbound_cid;}
    }
    if (prefix_choice.length > 0)
        {var call_prefix = prefix_choice;}
    else
        {var call_prefix = manual_dial_prefix;}

    var postData = {
        goAction: "goManualDialOnly",
        goUser: uName,
        goPass: uPass,
        goServerIP: server_ip,
        goSessionName: session_name,
        goConfExten: session_id,
        goLeadID: $(".formMain input[name='lead_id']").val(),
        goPhoneNumber: manDialOnly_num,
        goPhoneCode: $(".formMain input[name='phone_code']").val(),
        goCampaign: campaign,
        goExtContext: ext_context,
        goDialTimeout: dial_timeout,
        goDialPrefix: call_prefix,
        goCampaignCID: call_cid,
        goOmitPhoneCode: omit_phone_code,
        goUseGroupAlias: usegroupalias,
        goAccount: active_group_alias,
        goAgentDialedNumber: agent_dialed_number,
        goAgentDialedType: agent_dialed_type,
        goDialMethod: dial_method,
        goAgentLogID: agent_log_id,
        goSecurity: $(".formMain input[name='security_phrase']").val(),
        goQMExtension: qm_extension,
        goOldCID: LastCallCID,
        responsetype: 'json'
    };

    $.ajax({
        type: 'POST',
        url: '<?=$goAPI?>/goAgent/goAPI.php',
        processData: true,
        data: postData,
        dataType: "json",
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
        }
    })
    .done(function (result) {
        if (result.result == 'error') {
            if (!!$.prototype.snackbar) {
                $.snackbar({content: "<i class='fa fa-exclamation-circle fa-lg text-warning' aria-hidden='true'></i>&nbsp; "+result.message+".", timeout: 5000, htmlAllowed: true});
            }
        } else {
            MDnextCID =		result.data.callerid;
            LastCallCID =	result.data.callerid;
            agent_log_id =	result.data.agent_log_id;
            LastCID =	    result.data.callerid;
            MD_channel_look = 1;
            custchannellive = 1;

            var dispnum = manDialOnly_num;
            var status_display_number = phone_number_format(dispnum);

            if (alt_dial_status_display == '0') {
                var status_display_content='';
                if (status_display_CALLID > 0) {status_display_content = status_display_content + " <?=$lh->translationFor('uid')?>: " + MDnextCID;}
                if (status_display_LEADID > 0) {status_display_content = status_display_content + " <?=$lh->translationFor('lead_id')?>: " + $(".formMain input[name='lead_id']").val();}
                if (status_display_LISTID > 0) {status_display_content = status_display_content + " <?=$lh->translationFor('list_id')?>: " + $(".formMain input[name='list_id']").val();}

                $("#MainStatusSpan").html("<strong><?=$lh->translationFor('calling')?></strong>: " + status_display_number + " " + status_display_content + " &nbsp; <?=$lh->translationFor('waiting_for_ring')?>");
                
                //document.getElementById("HangupControl").innerHTML = "<a href=\"#\" onclick=\"dialedcall_send_hangup();\"><img src=\"./images/hangup.png\" border=\"0\" title=\"<?=$lang['hangup_customer']?>\" alt=\"<?=$lang['hangup_customer']?>\" /></a>";
                toggleButton('DialHangup', 'hangup');
            }
            if ( (LIVE_campaign_recording == 'ALLCALLS') || (LIVE_campaign_recording == 'ALLFORCE') )
                {all_record = 'YES';}

            if ( (view_scripts == 1) && (campaign_script.length > 0) ) {
                var SCRIPT_web_form = 'http://127.0.0.1/testing.php';
                var TEMP_SCRIPT_web_form = URLDecode(SCRIPT_web_form,'YES','DEFAULT','1');
                //$("#ScriptButtonSpan").html("<a href='#' id='ScriptButtonSpan' onClick='ScriptPanelToFront();' style='font-size:13px;color:white;text-decoration:none;'><?=ucwords($lh->translationFor('script'))?></a><!--<A HREF=\"#\" onClick=\"ScriptPanelToFront();\"><IMG SRC=\"./images/script_tab.png\" ALT=\"<?=$lh->translationFor('script')?>\" WIDTH=143 HEIGHT=27 BORDER=0></A>-->");

                if ( (script_recording_delay > 0) && ( (LIVE_campaign_recording == 'ALLCALLS') || (LIVE_campaign_recording == 'ALLFORCE') ) ) {
                    delayed_script_load = 'YES';
                    //RefresHScript('CLEAR');
                    ClearScript();
                } else {
                    LoadScriptContents();
                }
            }

            if (custom_fields_enabled > 0) {
                $("CustomFormSpan").html(" <a href='#' id='CustomFormSpan' onclick='FormPanelToFront();'  style='font-size:13px;color:white;text-decoration:none;' /><?=ucwords($lh->translationFor('script'))?></a>"); 
                //<!-- <a href=\"#\" onclick=\"FormPanelToFront();\"><img src=\"./images/custom_form_tab.png\" alt=\"FORM\" width=\"143px\" height=\"27px\" border=\"0\" /></a>"; jin3rd-->
                //FormContentsLoad();
            }

            if (email_enabled > 0) {
                //EmailContentsLoad();
            }
            if (get_call_launch == 'SCRIPT') {
                if (delayed_script_load == 'YES') {
                    LoadScriptContents();
                }
                //ScriptPanelToFront();
                $('#agent_tablist a[href="#scripts"]').tab('show');
            }
            if (get_call_launch == 'FORM') {
                //FormPanelToFront();
            }
            if (get_call_launch == 'EMAIL') {
                //EmailPanelToFront();
            }
            if (get_call_launch == 'WEBFORM') {
                window.open(TEMP_VDIC_web_form_address, web_form_target, 'toolbar=1,scrollbars=1,location=1,statusbar=1,menubar=1,resizable=1,width=640,height=450');
            }
            if (get_call_launch == 'WEBFORMTWO') {
                window.open(TEMP_VDIC_web_form_address_two, web_form_target, 'toolbar=1,scrollbars=1,location=1,statusbar=1,menubar=1,resizable=1,width=640,height=450');
            }
        }
    });
}


// ################################################################################
// Send Originate command to manager to place a phone call
function BasicOriginateCall(tasknum, taskprefix, taskreverse, taskdialvalue, tasknowait, taskconfxfer, taskcid, taskusegroupalias, taskalert, taskpresetname, taskvariables) {
    if (taskalert == '1') {
        var TAqueryCID = tasknum;
        tasknum = '83047777777777';
        taskdialvalue = '7' + taskdialvalue;
        var alertCID = 1;
    } else {
        var alertCID = 0;
    }
    var usegroupalias = 0;
    var consultativexfer_checked = 0;
    if ($("#consultativexfer").is(':checked'))
        {consultativexfer_checked = 1;}
    var regCXFvars = new RegExp("CXFER","g");
    var tasknum_string = tasknum.toString();
    if ( (tasknum_string.match(regCXFvars)) || (consultativexfer_checked > 0) ) {
        if (tasknum_string.match(regCXFvars)) {
            var Ctasknum = tasknum_string.replace(regCXFvars, '');
            if (Ctasknum.length < 2)
                {Ctasknum = '90009';}
            var agentdirect = '';
        } else {
            Ctasknum = '90009';
            var agentdirect = tasknum_string;
        }
        var XFERSelect = $("#transfer-local-closer");
        var XFER_Group = XFERSelect.val();
        if (API_selected_xfergroup.length > 1)
            {var XFER_Group = API_selected_xfergroup;}
        tasknum = Ctasknum + "*" + XFER_Group + '*CXFER*' + $(".formMain input[name='lead_id']").val() + '**' + dialed_number + '*' + user + '*' + agentdirect + '*' + live_call_seconds + '*';

        if (consult_custom_sent < 1)
            {CustomerData_update();}
    }
    var regAXFvars = new RegExp("AXFER","g");
    if (tasknum_string.match(regAXFvars)) {
        var Ctasknum = tasknum_string.replace(regAXFvars, '');
        if (Ctasknum.length < 2)
            {Ctasknum = '83009';}
        var closerxfercamptail = '_L';
        if (closerxfercamptail.length < 3)
            {closerxfercamptail = 'IVR';}
        tasknum = Ctasknum + '*' + $(".formMain input[name='phone_number']").val() + '*' + $(".formMain input[name='lead_id']").val() + '*' + campaign + '*' + closerxfercamptail + '*' + user + '**' + live_call_seconds + '*';

        if (consult_custom_sent < 1)
            {CustomerData_update();}
    }

    if (taskprefix == 'NO') {var call_prefix = '';}
    else {var call_prefix = agc_dial_prefix;}

    if (prefix_choice.length > 0)
        {var call_prefix = prefix_choice;}

    if (taskreverse == 'YES') {
        if (taskdialvalue.length < 2)
            {var dialnum = dialplan_number;}
        else
            {var dialnum = taskdialvalue;}
        var call_prefix = '';
        var originatevalue = "Local/" + tasknum + "@" + ext_context;
    } else {
        var dialnum = tasknum;
        if ( (protocol == 'EXTERNAL') || (protocol == 'Local') ) {
            var protodial = 'Local';
            var extendial = extension;
            //var extendial = extension + "@" + ext_context;
        } else {
            var protodial = protocol;
            var extendial = extension;
        }
        var originatevalue = protodial + "/" + extendial;
    }

    var leadCID = $(".formMain input[name='lead_id']").val();
    var epochCID = epoch_sec;
    if (leadCID.length < 1)
        {leadCID = user_abb;}
    leadCID = set_length(leadCID,'10', 'left');
    epochCID = set_length(epochCID,'6', 'right');
    if (taskconfxfer == 'YES')
        {var queryCID = "DC" + epochCID + 'W' + leadCID + 'W';}
    else
        {var queryCID = "DV" + epochCID + 'W' + leadCID + 'W';}

    //if (taskconfxfer == 'YES')
    //	{var queryCID = "DCagcW" + epoch_sec + user_abb;}
    //else
    //	{var queryCID = "DVagcW" + epoch_sec + user_abb;}

    if (taskalert == '1') {
        queryCID = TAqueryCID;
    }

    if (cid_choice.length > 3) {
        var call_cid = cid_choice;
        usegroupalias = 1;
    } else {
        if (taskcid.length > 3) 
            {var call_cid = taskcid;}
        else 
            {var call_cid = campaign_cid;}
    }
    
    var postData = {
        goAction: 'goOriginate',
        goUser: uName,
        goPass: uPass,
        goCampaign: campaign,
        goServerIP: server_ip,
        goSessionName: session_name,
        goChannel: originatevalue,
        goQueryCID: queryCID,
        goOutboundCID: call_cid,
        goExten: call_prefix + "" + dialnum,
        goExtContext: ext_context,
        goExtPriority: 1,
        goUseGroupAlias: usegroupalias,
        goPresetName: taskpresetname,
        goAccount: active_group_alias,
        goAgentDialedNumber: agent_dialed_number,
        goAgentDialedType: agent_dialed_type,
        goLeadID: $(".formMain input[name='lead_id']").val(),
        goStage: CheckDEADcallON,
        goAlertCID: alertCID,
        goCallVariables: taskvariables,
        responsetype: 'json'
    };

    $.ajax({
        type: 'POST',
        url: '<?=$goAPI?>/goAgent/goAPI.php',
        processData: true,
        data: postData,
        dataType: "json",
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
        }
    })
    .done(function (result) {
        var BOresponse = result;
        if (BOresponse.result == 'error') {
            swal({
                title: '<?=$lh->translationFor('error')?>',
                text: BOresponse.message,
                type: 'error',
            });
        }

        if ((taskdialvalue.length > 0) && (tasknowait != 'YES')) {
            XDnextCID = queryCID;
            MD_channel_look = 1;
            XDcheck = 'YES';

            //document.getElementById("HangupXferLine").innerHTML ="<a href=\"#\" onclick=\"xfercall_send_hangup();return false;\"><img src=\"./images/vdc_XB_hangupxferline.gif\" border=\"0\" alt=\"Hangup Xfer Line\" /></a>";
            toggleButton('HangupXferLine', 'on');
            $("#btnHangupXferLine").attr('onclick', "XFerCallHangup(); return false;");
        }
        
        active_group_alias = '';
        cid_choice = '';
        prefix_choice = '';
        agent_dialed_number = '';
        agent_dialed_type = '';
        Call_Script_ID = '';
        call_variables = '';
    });
}


function ManualDialNext(mdnCBid, mdnBDleadid, mdnDiaLCodE, mdnPhonENumbeR, mdnStagE, mdVendorid, mdgroupalias, mdtype) {
    dialingINprogress = 1;
    if (waiting_on_dispo > 0) {
        dialingINprogress = 0;
        alt_phone_dialing = starting_alt_phone_dialing;
        auto_dial_level = starting_dial_level;
        swal({
            title: '<?=$lh->translationFor('error')?>',
            text: "<?=$lh->translationFor('system_delay_try_again')?><br><?=$lh->translationFor('code')?>: " + agent_log_id + " - " + waiting_on_dispo,
            type: 'error',
            html: true
        });
    } else {
        inOUT = 'OUT';
        all_record = 'NO';
        all_record_count = 0;
        if (dial_method == "INBOUND_MAN") {
            auto_dial_level = 0;

            if (VDRP_stage != 'PAUSED') {
                agent_log_id = AutoDial_Resume_Pause("VDADpause",'','','',"DIALNEXT",'1','NXDIAL');
            } else {
                auto_dial_level = starting_dial_level;
            }
            toggleButton('ResumePause', 'off');
            toggleButton('DialHangup', 'off');
        } else {
            if (active_ingroup_dial.length < 1) {
                toggleButton('DialHangup', 'off');
                toggleButton('ResumePause', 'hide');
            }
        }

        var manual_dial_only_type_flag = '';
        if ( (mdtype == 'ALT') || (mdtype == 'ADDR3') ) {
            agent_dialed_type = mdtype;
            agent_dialed_number = mdnPhonENumbeR;
            if (mdtype == 'ALT')
                {manual_dial_only_type_flag = 'ALTPhone';}
            if (mdtype == 'ADDR3')
                {manual_dial_only_type_flag = 'Address3';}
        }

        if ( ($("#LeadPreview").prop('checked')) && (active_ingroup_dial.length < 1) ) {
            reselect_preview_dial = 1;
            in_lead_preview_state = 1;
            var man_preview = 'YES';
       	<?php if( ECCS_BLIND_MODE === 'y' ){ ?> 
            var man_status = "<a href=\"#\" onclick=\"ManualDialOnly('" + manual_dial_only_type_flag + "')\" title=\"<?=$lh->translationFor('dial_lead')?>\">&nbsp;<blink><?=$lh->translationFor('dial_lead')?> [#DL]</blink>&nbsp;</a> or <a href=\"#\" onclick=\"ManualDialSkip()\" title=\"<?=$lh->translationFor('skip_lead')?>\">&nbsp;<blink><?=$lh->translationFor('skip_lead')?> [#SL] </blink>&nbsp;</a>";
	<?php } else { ?>
            var man_status = "<a href=\"#\" onclick=\"ManualDialOnly('" + manual_dial_only_type_flag + "')\">&nbsp;<blink><?=$lh->translationFor('dial_lead')?></blink>&nbsp;</a> or <a href=\"#\" onclick=\"ManualDialSkip()\">&nbsp;<blink><?=$lh->translationFor('skip_lead')?></blink>&nbsp;</a>";
	<?php } ?>

            if (manual_preview_dial=='PREVIEW_ONLY') {
                var man_status = "<a href=\"#\" onclick=\"ManualDialOnly('" + manual_dial_only_type_flag + "')\">&nbsp;<blink><?=$lh->translationFor('dial_lead')?></blink>&nbsp;</a>";
            }
        } else {
            reselect_preview_dial = 0;
            var man_preview = 'NO';
            var man_status = "<?=$lh->translationFor('waiting_for_ring')?>...";
        }

        if (cid_choice.length > 0)
            {var call_cid = cid_choice;}
        else
            {var call_cid = campaign_cid;}

        if (prefix_choice.length > 0)
            {var call_prefix = prefix_choice;}
        else
            {var call_prefix = manual_dial_prefix;}
        
        var postData = {
            goAction: 'goManualDialNext',
            goUser: uName,
            goPass: uPass,
            goServerIP: server_ip,
            goSessionName: session_name,
            goExtContext: ext_context,
            goConfExten: session_id,
            goCampaign: campaign,
            goPreview: man_preview,
            goCallbackID: mdnCBid,
            goLeadID: mdnBDleadid,
            goPhoneCode: mdnDiaLCodE,
            goPhoneNumber: mdnPhonENumbeR,
            goListID: manual_dial_list_id,
            goStage: mdnStagE,
            goVendorLeadCode: mdVendorid,
            goUseGroupAlias: mdgroupalias,
            goAccount: active_group_alias,
            goAgentDialedNumber: mdnPhonENumbeR,
            goAgentDialedType: mdtype,
            goQMExtension: qm_extension,
            goDialIngroup: active_ingroup_dial,
            goNoCallDialFlag: nocall_dial_flag,
            goSIPserver: SIPserver,
            goVTCallbackID: vtiger_callback_id,
            goDialTimeout: dial_timeout,
            goDialPrefix: call_prefix,
            goCampaignCID: call_cid,
            goAgentLogID: agent_log_id,
            goUseInternalDNC: use_internal_dnc,
            goUseCampaignDNC: use_campaign_dnc,
            goOmitPhoneCode: omit_phone_code,
            goManualDialCallTimeCheck: manual_dial_call_time_check,
            goManualDialFilter: manual_dial_filter,
            goUseGroupAlias: mdgroupalias,
            responsetype: 'json'
        };
        

        $.ajax({
            type: 'POST',
            url: '<?=$goAPI?>/goAgent/goAPI.php',
            processData: true,
            data: postData,
            dataType: "json",
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            }
        })
        .done(function (result) {
            //dialingINprogress = 0;

            if (active_ingroup_dial.length > 0) {
                AutoDial_Resume_Pause("VDADready",'','','NO_STATUS_CHANGE');
                AutoDialWaiting = 1;
            } else {
                var ERR_MSG = "";
                if (result.result == 'error') {
                    ERR_MSG = result.message;
                }
                //$('#dialerOutput').html('<b>DIALER:</b> ' + result);

                var regMNCvar = new RegExp("HOPPER EMPTY","ig");
                var regMDFvarDNC = new RegExp("DNC","ig");
                var regMDFvarCAMP = new RegExp("CAMPLISTS","ig");
                var regMDFvarTIME = new RegExp("OUTSIDE","ig");
                if ( (ERR_MSG.match(regMNCvar)) || (ERR_MSG.match(regMDFvarDNC)) || (ERR_MSG.match(regMDFvarCAMP)) || (ERR_MSG.match(regMDFvarTIME)) ) {
                    var alert_displayed = 0;
                    trigger_ready = 1;
                    live_customer_call = 0;
                    MD_channel_look = 0;
                    dialingINprogress = 0;
                    alt_phone_dialing = starting_alt_phone_dialing;
                    auto_dial_level = starting_dial_level;

                    if (ERR_MSG.match(regMNCvar)) {
                        swal("<?=$lh->translationFor('no_leads_on_hopper')?>.");
                        alert_displayed = 1;
                    }
                    if (ERR_MSG.match(regMDFvarDNC)) {
                        swal("<?=$lh->translationFor('phone_number_on_dnc')?>.");
                        alert_displayed = 1;
                    }
                    if (ERR_MSG.match(regMDFvarCAMP)) {
                        swal("<?=$lh->translationFor('phone_number_not_on_list')?>.");
                        alert_displayed = 1;
                    }
                    if (ERR_MSG.match(regMDFvarTIME)) {
                        swal("<?=$lh->translationFor('phone_number_outside_time')?>.");
                        alert_displayed = 1;
                    }
                    if (alert_displayed == 0) {
                        swal("<?=$lh->translationFor('unspecified_error')?>:\n" + mdnPhonENumbeR + " | " + MDnextCID);
                        alert_displayed = 1;
                    }

                    if (starting_dial_level > 0) {
                        if (dial_method == "INBOUND_MAN") {
                            auto_dial_level = starting_dial_level;

                            toggleButton('DialHangup', 'dial');
                            toggleButton('ResumePause', 'resume');
                        } else {
                            toggleButton('DialHangup', 'dial');
                            toggleButton('ResumePause', 'resume');
                        }
                    } else {
                        toggleButton('DialHangup', 'dial');
                    }
                } else {
                    var thisVdata = result.data;
                    
                    MDnextCID = thisVdata.MqueryCID;
                    
                    fronter                                 = uName;
                    LastCID                                 = thisVdata.MqueryCID;
                    lead_id                                 = thisVdata.lead_id;
                    $(".formMain input[name='lead_id']").val(lead_id);
                    LeadPrevDispo                           = thisVdata.status;
                    $(".formMain input[name='vendor_lead_code']").val(thisVdata.vendor_lead_code);
                    list_id                                 = thisVdata.list_id;
                    $(".formMain input[name='list_id']").val(list_id);
                    $(".formMain input[name='gmt_offset_now']").val(thisVdata.gmt_offset_now);
                    cust_phone_code                         = thisVdata.phone_code;
                    $(".formMain input[name='phone_code']").val(cust_phone_code).trigger('change');
                    cust_phone_number                       = thisVdata.phone_number;
                    $(".formMain input[name='phone_number']").val(cust_phone_number).trigger('change');
                    $(".formMain input[name='title']").val(thisVdata.title);
                    cust_first_name                         = thisVdata.first_name;
                    if (cust_first_name !== '') {
                        $("#cust_full_name a[id='first_name']").editable('setValue', cust_first_name, true);
                    }
                    cust_middle_initial                     = thisVdata.middle_initial;
                    if (cust_middle_initial != '') {
                        $("#cust_full_name a[id='middle_initial']").editable('setValue', cust_middle_initial, true);
                    }
                    cust_last_name                          = thisVdata.last_name;
                    if (cust_last_name !== '') {
                        $("#cust_full_name a[id='last_name']").editable('setValue', cust_last_name, true);
                    }
                    
                    // ECCS Customization
                    <?php
                    if(ECCS_BLIND_MODE === 'y'){
                    ?>
                    $("#cust_campaign_name").html("["+ campaign_name + "] - ");
                    $("#cust_call_type").html(" - <span style='background-color: blue;'>OUTBOUND CALL</span>");
                    <?php } ?>

                    $(".formMain input[name='address1']").val(thisVdata.address1).trigger('change');
                    $(".formMain input[name='address2']").val(thisVdata.address2).trigger('change');
                    $(".formMain input[name='address3']").val(thisVdata.address3).trigger('change');
                    $(".formMain input[name='city']").val(thisVdata.city).trigger('change');
                    $(".formMain input[name='state']").val(thisVdata.state).trigger('change');
                    $(".formMain input[name='province']").val(thisVdata.province).trigger('change');
                    $(".formMain input[name='postal_code']").val(thisVdata.postal_code).trigger('change');
                    $(".formMain select[name='country_code']").val(thisVdata.country_code).trigger('change');
                    $(".formMain select[name='gender']").val(thisVdata.gender).trigger('change');
                    var dateOfBirth = thisVdata.date_of_birth;
                    $(".formMain input[name='date_of_birth']").val(dateOfBirth);
                    $(".formMain input[name='alt_phone']").val(thisVdata.alt_phone).trigger('change');
                    cust_email                              = thisVdata.email;
                    $(".formMain input[name='email']").val(cust_email).trigger('change');
                    $(".formMain input[name='security_phrase']").val(thisVdata.security_phrase);
                    var REGcommentsNL = new RegExp("!N!","g");
                    if (typeof thisVdata.comments !== 'undefined') {
                        thisVdata.comments = thisVdata.comments.replace(REGcommentsNL, "\n");
                    }
                    $(".formMain textarea[name='comments']").val(thisVdata.comments).trigger('change');
                    called_count                            = thisVdata.called_count;
                    $(".formMain input[name='called_count']").val(called_count);
                    previous_called_count                   = thisVdata.called_count;
                    previous_dispo                          = thisVdata.status;
                    CBentry_time                            = thisVdata.CBentry_time;
                    CBcallback_time                         = thisVdata.CBcallback_time;
                    CBuser                                  = thisVdata.CBuser;
                    CBcomments                              = thisVdata.CBcomments;
                    dialed_number                           = thisVdata.agent_dialed_number;
                    dialed_label                            = thisVdata.agent_dialed_type;
                    source_id                               = thisVdata.source_id;
                    $(".formMain input[name='rank']").val(thisVdata.rank);
                    $(".formMain input[name='owner']").val(thisVdata.owner);
                    $(".formMain textarea[name='call_notes']").val(thisVdata.call_notes).trigger('change');
                    Call_Script_ID                          = thisVdata.Call_Script_ID;
                    script_recording_delay                  = thisVdata.script_recording_delay;
                    Call_XC_a_Number                        = thisVdata.xferconf_a_number;
                    Call_XC_b_Number                        = thisVdata.xferconf_b_number;
                    Call_XC_c_Number                        = thisVdata.xferconf_c_number;
                    Call_XC_d_Number                        = thisVdata.xferconf_d_number;
                    Call_XC_e_Number                        = thisVdata.xferconf_e_number;
                    entry_list_id                           = thisVdata.entry_list_id;
                    $(".formMain input[name='entry_list_id']").val(entry_list_id);
                    custom_field_names                      = thisVdata.custom_field_names;
                    custom_field_values                     = thisVdata.custom_field_values;
                    custom_field_types                      = thisVdata.custom_field_types;
                    list_webform                            = thisVdata.web_form_address;
                    list_webform_two                        = thisVdata.web_form_address_two;
                    post_phone_time_diff_alert_message      = thisVdata.post_phone_time_diff_alert_message;

                    timer_action = campaign_timer_action;
                    timer_action_message = campaign_timer_action_message;
                    timer_action_seconds = campaign_timer_action_seconds;
                    timer_action_destination = campaign_timer_action_destination;
                    
                    lead_dial_number = dialed_number;
                    dispnum = dialed_number;
                    var status_display_number = phone_number_format(dispnum);
                    var status_display_content = '';
                    if (status_display_CALLID > 0) {status_display_content = status_display_content + "<br><b><?=$lh->translationFor('uid')?>:</b> " + MDnextCID;}
                    if (status_display_LEADID > 0) {status_display_content = status_display_content + "<br><b><?=$lh->translationFor('lead_id')?>:</b> " + $(".formMain input[name='lead_id']").val();}
                    if (status_display_LISTID > 0) {status_display_content = status_display_content + "<br><b><?=$lh->translationFor('list_id')?>:</b> " + $(".formMain input[name='list_id']").val();}
                    
                    $("#MainStatusSpan").html("<b><?=$lh->translationFor('calling')?>:</b> " + status_display_number + " " + status_display_content + "<br>" + man_status);
                    
                    if (custom_field_names.length > 1) {
                        if (custom_fields_launch == 'ONCALL') {
                            GetCustomFields(list_id, false, true);
                        }
                        
                        var custom_names_array = custom_field_names.split("|");
                        var custom_values_array = custom_field_values.split("----------");
                        var custom_types_array = custom_field_types.split("|");
                        var field_name = ".formMain #custom_fields";
                        
                        var fieldsPopulated = setInterval(function() {
                            if (getFields) {
                                clearInterval(fieldsPopulated);
                                
                                $.each(custom_names_array, function(idx, field) {
                                    if (field.length < 1) return true;
                                    
                                    switch (custom_types_array[idx]) {
                                        case "TEXT":
                                        case "AREA":
                                        case "HIDDEN":
                                        case "DATE":
                                        case "TIME":
                                            $(field_name + " [id='custom_" + field + "']").val(custom_values_array[idx]);
                                            break;
                                        case "CHECKBOX":
                                        case "RADIO":
                                            var checkThis = custom_values_array[idx].split(',');
                                            $.each($(field_name + " [id^='custom_" + field + "']"), function() {
                                                if (checkThis.indexOf($(this).val()) > -1) {
                                                    $(this).prop('checked', true);
                                                } else {
                                                    $(this).prop('checked', false);
                                                }
                                            });
                                            break;
                                        case "SELECT":
                                        case "MULTI":
                                            var selectThis = custom_values_array[idx].split(',');
                                            $.each($(field_name  + " [id='custom_" + field + "'] option"), function() {
                                                if (selectThis.indexOf($(this).val()) > -1) {
                                                    $(this).prop('selected', true);
                                                } else {
                                                    $(this).prop('selected', false);
                                                }
                                            });
                                            break;
                                        default:
                                            $(field_name + " [id='custom_" + field + "']").html(custom_values_array[idx]);
                                    }
                                });
                            
                                replaceCustomFields();
                                if (custom_fields_launch == 'ONCALL') {
                                    GetCustomFields(null, true);
                                }
                            }
                        }, 1000);
                    }
            
                    //$("#cust_full_name").html(cust_first_name+" "+cust_middle_initial+" "+cust_last_name);
                    $("#cust_full_name").removeClass('hidden');
                    $("#cust_number").html(phone_number_format(dispnum));
	            <?php if(ECCS_BLIND_MODE === 'y'){ ?> $("span#span-cust-number").removeClass("hidden"); $("#cust_number").val(cust_phone_number); <?php } ?>
                    $("#cust_avatar").html(goGetAvatar(cust_first_name+" "+cust_last_name));
                    //goAvatar._init(goOptions);
                    //console.log(goGetAvatar(dispnum));
                    
                    LeadDispo = '';
                    
                    VDIC_web_form_address = web_form_address
                    VDIC_web_form_address_two = web_form_address_two
                    if (list_webform.length > 5) {VDIC_web_form_address = list_webform;}
                    if (list_webform_two.length > 5) {VDIC_web_form_address_two = list_webform_two;}

                    var regWFAcustom = new RegExp("^VAR","ig");
                    if (VDIC_web_form_address.match(regWFAcustom)) {
                        TEMP_VDIC_web_form_address = URLDecode(VDIC_web_form_address,'YES','CUSTOM');
                        TEMP_VDIC_web_form_address = TEMP_VDIC_web_form_address.replace(regWFAcustom, '');
                    } else {
                        TEMP_VDIC_web_form_address = URLDecode(VDIC_web_form_address,'YES','DEFAULT','1');
                    }

                    if (VDIC_web_form_address_two.match(regWFAcustom)) {
                        TEMP_VDIC_web_form_address_two = URLDecode(VDIC_web_form_address_two,'YES','CUSTOM');
                        TEMP_VDIC_web_form_address_two = TEMP_VDIC_web_form_address_two.replace(regWFAcustom, '');
                    } else {
                        TEMP_VDIC_web_form_address_two = URLDecode(VDIC_web_form_address_two,'YES','DEFAULT','2');
                    }
                    
                    if (VDIC_web_form_address.length > 0) {
                        $("#openWebForm").removeClass('disabled');
                        //document.getElementById("WebFormSpan").innerHTML = "<a href=\"" + TEMP_VDIC_web_form_address + "\" target=\"" + web_form_target + "\" onMouseOver=\"WebFormRefresH();\" style=\"font-size:13px;color:white;text-decoration:none;\"><?=$lang['web_form']?></a>";
                    }
                    
                    if (enable_second_webform > 0 && VDIC_web_form_address_two.length > 0) {
                        $("#openWebFormTwo").removeClass('disabled');
                        //document.getElementById("WebFormSpanTwo").innerHTML = "<a href=\"" + TEMP_VDIC_web_form_address_two + "\" target=\"" + web_form_target + "\" onMouseOver=\"WebFormTwoRefresH();\" style=\"font-size:13px;color:white;text-decoration:none;\" /><?=$lang['web_form_two']?></a>";
                    }

                    if (CBentry_time.length > 2) {
                        //document.getElementById("CusTInfOSpaN").innerHTML = " <b> <?=$lang['previous_callback']?> </b>";
                        //document.getElementById("CusTInfOSpaN").style.background = CusTCB_bgcolor;
                        //document.getElementById("CBcommentsBoxA").innerHTML = "<b><?=$lang['last_call']?>: </b>" + CBentry_time;
                        //document.getElementById("CBcommentsBoxB").innerHTML = "<b><?=$lang['callback']?>: </b>" + CBcallback_time;
                        //document.getElementById("CBcommentsBoxC").innerHTML = "<b><?=$lang['agent']?>: </b>" + CBuser;
                        //document.getElementById("CBcommentsBoxD").innerHTML = "<b><?=$lang['comments']?>: </b><br />" + CBcomments;
                        //showDiv('CBcommentsBox');
                        // ECCS Customization
                        <?php
                        if(ECCS_BLIND_MODE === 'y'){
                        ?>
                        $("#cust_campaign_name").html("["+ campaign_name + "] - ");
                        $("#cust_call_type").html(" - <span style='background-color: purple;'>CALLBACK - Last call by " + CBuser + "</span>");
                        <?php } ?>
                        
                        swal({
                            title: "<?=$lh->translationFor('previous_callback')?>",
                            text: "<div class='swal-previous-callback' style='text-align: left; padding: 0 30px;'><div style='padding-bottom: 10px;'><b><?=$lh->translationFor('last_call')?>:</b> " + CBentry_time + "</div><div style='padding-bottom: 10px;'><b><?=$lh->translationFor('callback')?>:</b> " + CBcallback_time + "</div><div style='padding-bottom: 10px;'><b><?=$lh->translationFor('agent')?>:</b> " + CBuser + "</div><div style='padding-bottom: 10px;'><b><?=$lh->translationFor('comments')?>:</b><br />" + CBcomments + "</div></div>",
                            type: 'info',
                            html: true
                        });

			<?php if(ECCS_BLIND_MODE === 'y'){ ?>
        	        $("div.swal-previous-callback").attr("title", "<?=$lh->translationFor('previous_callback')?>");
	                <?php } ?>

                    }

                    if (post_phone_time_diff_alert_message.length > 10) {
                        //document.getElementById("post_phone_time_diff_span_contents").innerHTML = " &nbsp; &nbsp; " + post_phone_time_diff_alert_message + "<br />";
                        //showDiv('post_phone_time_diff_span');
                    }

                    if ($("#LeadPreview").prop('checked') == false) {
                        reselect_preview_dial = 0;
                        MD_channel_look = 1;
                        custchannellive = 1;

                        toggleButton('DialHangup', 'hangup');

                        if ( (LIVE_campaign_recording == 'ALLCALLS') || (LIVE_campaign_recording == 'ALLFORCE') ) {
                            all_record = 'YES';
                        }

                        if ( (view_scripts == 1) && (campaign_script.length > 0) ) {
                            var SCRIPT_web_form = 'http://127.0.0.1/testing.php';
                            var TEMP_SCRIPT_web_form = URLDecode(SCRIPT_web_form,'YES','DEFAULT','1');
                            //$("#ScriptButtonSpan").html("<a href=\"#\" id=\"ScriptButtonSpan\" onClick=\"ScriptPanelToFront();\" style=\"font-size:13px;color:white;text-decoration:none;\"><?=ucwords($lh->translationFor('script'))?></a> <!-- <A HREF=\"#\" onClick=\"ScriptPanelToFront();\"><IMG SRC=\"./images/script_tab.png\" ALT=\"<?=$lh->translationFor('script')?>\" WIDTH=143 HEIGHT=27 BORDER=0></A>-->");

                            if ( (script_recording_delay > 0) && ( (LIVE_campaign_recording == 'ALLCALLS') || (LIVE_campaign_recording == 'ALLFORCE') ) ) {
                                delayed_script_load = 'YES';
                                //RefresHScript('CLEAR');
                                ClearScript();
                            } else {
                                LoadScriptContents();
                            }
                        }

                        if (custom_fields_enabled > 0) {
                            $("#CustomFormSpan").html(" <a href=\"#\" id=\"CustomFormSpan\" onclick=\"FormPanelToFront();\"  style=\"font-size:13px;color:white;text-decoration:none;\" /><?=ucwords($lh->translationFor('custom_form'))?></a>");  
                            //FormContentsLoad();
                        }

                        if (email_enabled > 0 && EMAILgroupCOUNT > 0) {
                            //EmailContentsLoad();
                        }
                        if (get_call_launch == 'SCRIPT') {
                            if (delayed_script_load == 'YES') {
                                LoadScriptContents();
                            }
                            //ScriptPanelToFront();
                            $('#agent_tablist a[href="#scripts"]').tab('show');
                        }

                        if (get_call_launch == 'FORM') {
                            //FormPanelToFront();
                        }

                        if (get_call_launch == 'EMAIL') {
                            //EmailPanelToFront();
                        }

                        if (get_call_launch == 'WEBFORM') {
                            window.open(TEMP_VDIC_web_form_address, web_form_target, 'toolbar=1,scrollbars=1,location=1,statusbar=1,menubar=1,resizable=1,width=640,height=450');
                        }
                        if (get_call_launch == 'WEBFORMTWO') {
                            window.open(TEMP_VDIC_web_form_address_two, web_form_target, 'toolbar=1,scrollbars=1,location=1,statusbar=1,menubar=1,resizable=1,width=640,height=450');
                        }
                    } else {
                        if (custom_fields_enabled > 0) {
                            $("#CustomFormSpan").html(" <a href=\"#\" id=\"CustomFormSpan\" onclick=\"FormPanelToFront();\"  style=\"font-size:13px;color:white;text-decoration:none;\" /><?=ucwords($lh->translationFor('custom_form'))?></a>");
                            //FormContentsLoad();
                        }
                        if ( (view_scripts == 1) && (campaign_script.length > 0) ) {
                            var SCRIPT_web_form = 'http://127.0.0.1/testing.php';
                            var TEMP_SCRIPT_web_form = URLDecode(SCRIPT_web_form,'YES','DEFAULT','1');
                            //RefresHScript();
                        }
                        reselect_preview_dial = 1;
                    }
                    
                    if (ECCS_BLIND_MODE === 'n') {
                        setTimeout(function() {
                            if (live_customer_call > 0 || XD_live_customer_call > 0) {
                                check_last_call = 1;
                            }
                        }, 15000);
                    }
                }
            }
        });
    }
}

function AutoDial_Resume_Pause(taskaction, taskagentlog, taskwrapup, taskstatuschange, temp_reason, temp_auto, temp_auto_code) {
    var add_pause_code = '';
    if (taskaction == 'VDADready') {
        VDRP_stage = 'READY';
        var APIaction = 'RESUME';
        if (INgroupCOUNT > 0) {
            if (closer_blended == 0)
                {VDRP_stage = 'CLOSER';}
            else
                {VDRP_stage = 'READY';}
        }
        AutoDialReady = 1;
        AutoDialWaiting = 1;
        if (dial_method == "INBOUND_MAN") {
            auto_dial_level = starting_dial_level;

            toggleButton('ResumePause', 'pause');
            toggleButton('DialHangup', 'dial');
        } else {
            toggleButton('ResumePause', 'pause');
            toggleButton('DialHangup', 'dial', false);
        }
        
        if ($("#agentPaused").is(':visible')) {
            $("#agentPaused").snackbar('hide');
        }
    } else {
        VDRP_stage = 'PAUSED';
        var APIaction = 'PAUSE';
        AutoDialReady = 0;
        AutoDialWaiting = 0;
        pause_code_counter = 0;
        if (dial_method == "INBOUND_MAN") {
            auto_dial_level = starting_dial_level;

            toggleButton('ResumePause', 'resume');
            toggleButton('DialHangup', 'dial');
        } else {
            toggleButton('ResumePause', 'resume');
            toggleButton('DialHangup', 'dial', false);
        }

        if ( (agent_pause_codes_active == 'FORCE') && (temp_reason != 'LOGOUT') && (temp_reason != 'REQUEUE') && (temp_reason != 'DIALNEXT') && (temp_auto != '1') ) {
            PauseCodeSelectBox();
            //PauseCodeSelectContent_create();
        }

        if (temp_auto == '1') {
            add_pause_code = temp_auto_code;
        }
    }
    
    activateLinks();
    
    var postData = {
        goAction: 'goAutodialResumePause',
        goUser: uName,
        goPass: uPass,
        goCampaign: campaign,
        goAgentLogID: agent_log_id,
        goServerIP: server_ip,
        goSessionName: session_name,
        goTask: taskaction,
        goStage: VDRP_stage,
        goAgentLog: taskagentlog,
        goWrapUp: taskwrapup,
        goDialMethod: dial_method,
        goComments: taskstatuschange,
        goSubStatus: add_pause_code,
        goQMExtension: qm_extension,
        responsetype: 'json'
    };
        
    $.ajax({
        type: 'POST',
        url: '<?=$goAPI?>/goAgent/goAPI.php',
        processData: true,
        data: postData,
        dataType: "json",
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
        }
    })
    .done(function (result) {
        if (result.result == 'error') {
            return 0;
        } else {
            agent_log_id = result.data.agent_log_id;
        }
    });

    waiting_on_dispo = 0;
    return agent_log_id;
}

function ManualDialCall(TVfast, TVphone_code, TVphone_number, TVlead_id, TVtype) {
    var move_on = 1;
    if ( (AutoDialWaiting == 1) || (live_customer_call == 1) || (alt_dial_active == 1) || (MD_channel_look == 1) || (in_lead_preview_state == 1) ) {
        if ((auto_pause_precall == 'Y') && ( (agent_pause_codes_active == 'Y') || (agent_pause_codes_active == 'FORCE') ) && (AutoDialWaiting == 1) && (live_customer_call != 1) && (alt_dial_active != 1) && (MD_channel_look != 1) && (in_lead_preview_state != 1) ) {
            agent_log_id = AutoDial_Resume_Pause("VDADpause", '', '', '', '', '1', auto_pause_precall_code);
        } else {
            move_on = 0;
            swal({
                title: "<?=$lh->translationFor('error')?>",
                text: "<?=$lh->translationFor('must_be_paused_to_dial_manually')?>.",
                type: 'error'
            });
        }
    }
    if (move_on == 1) {
        if (TVfast == 'FAST') {
            NewManualDialCallFast();
        } else {
            if (TVfast == 'CALLLOG') {
                //hideDiv('CalLLoGDisplaYBox');
                //hideDiv('SearcHForMDisplaYBox');
                //hideDiv('SearcHResultSDisplaYBox');
                //hideDiv('LeaDInfOBox');
                $("#MDDiaLCodE").val(TVphone_code);
                $("#MDPhonENumbeR").val(TVphone_number);
                $("#MDPhonENumbeRHiddeN").val(TVphone_number);
                $("#MDLeadID").val(TVlead_id);
                $("#MDType").val(TVtype);
                if (disable_alter_custphone == 'HIDE')
                    {$("#MDPhonENumbeR").val('XXXXXXXXXX');}
            }
            if (TVfast == 'LEADSEARCH') {
                //hideDiv('SearcHForMDisplaYBox');
                //hideDiv('SearcHResultSDisplaYBox');
                //hideDiv('LeaDInfOBox');
                $("#MDDiaLCodE").val(TVphone_code);
                $("#MDPhonENumbeR").val(TVphone_number);
                $("#MDLeadID").val(TVlead_id);
                $("#MDType").val(TVtype);
            }
            if (agent_allow_group_alias == 'Y') {
                $("#ManuaLDiaLGrouPSelecteD").html('Group Alias: ' + active_group_alias);
                $("#ManuaLDiaLGrouP").html('<a href="#" onclick="GroupAliasSelectContent_create(0);"><?=$lh->translationFor('choose_group_alias')?></a>');
            }
            if (in_group_dial_display > 0) {
                $("#ManuaLDiaLInGrouPSelecteD").html('Dial Ingroup: ' + active_ingroup_dial);
                $("#ManuaLDiaLInGrouP").html('<a href="#" onclick="ManuaLDiaLInGrouPSelectContent_create(0);"><?=$lh->translationFor('choose_dial_ingroup')?></a>');
            }
            if ( (in_group_dial == 'BOTH') || (in_group_dial == 'NO_DIAL') ) {
                nocall_dial_flag = 'DISABLED';
                $("#NoDiaLSelecteD").html('<?=$lh->translationFor('no_call_dial')?>: ' + nocall_dial_flag + ' &nbsp; &nbsp; <a href="#" onclick="NoDiaLSwitcH();"><?=$lh->translationFor('click_to_activate')?></a>');
            }
            //showDiv('NeWManuaLDiaLBox');

            $("#search_phone_number").val('');
            $("#search_lead_id").val('');
            $("#search_vendor_lead_code").val('');
            $("#search_first_name").val('');
            $("#search_last_name").val('');
            $("#search_city").val('');
            $("#search_state").val('');
            $("#search_postal_code").val('');
        }
    }
}


// ################################################################################
// Send Hangup command for 3rd party call connected to the conference now to Manager
function XFerCallHangup() {
    var xferchannel = $(".formXFER input[name='xferchannel']").val();
    var xfer_channel = lastxferchannel;
    var process_post_hangup = 0;
    xfer_in_call = 0;
    if ( (MD_channel_look == 1) && (leaving_threeway < 1) ) {
        MD_channel_look=0;
        DialTimeHangup('XFER');
    }
    if (xferchannel.length > 3) {
        var queryCID = "HXvdcW" + epoch_sec + user_abb;
        var hangupvalue = xfer_channel;
 
        var postData = {
            goAction: 'goHangupCall',
            goUser: uName,
            goPass: uPass,
            goCampaign: campaign,
            goLogCampaign: campaign,
            goChannel: hangupvalue,
            goServerIP: server_ip,
            goSessionName: session_name,
            goQueryCID: queryCID,
            goQMExtension: qm_extension,
            responsetype: 'json'
        };
            
        $.ajax({
            type: 'POST',
            url: '<?=$goAPI?>/goAgent/goAPI.php',
            processData: true,
            data: postData,
            dataType: "json",
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            }
        })
        .done(function (result) {
                //alert(result.message);
        });
        process_post_hangup = 1;
    } else {
        process_post_hangup = 1;
    }
    
    if (process_post_hangup == 1) {
        XD_live_customer_call = 0;
        XD_live_call_seconds = 0;
        MD_ring_seconds = 0;
        MD_channel_look = 0;
        XDnextCID = '';
        XDcheck = '';
        xferchannellive = 0;
        consult_custom_wait = 0;
        consult_custom_go = 0;
        consult_custom_sent = 0;


    //  DEACTIVATE CHANNEL-DEPENDANT BUTTONS AND VARIABLES
        $(".formXFER input[name='xferchannel']").val('');
        lastxferchannel = '';

        toggleButton('Leave3WayCall', 'off');

        toggleButton('DialWithCustomer', 'on');

        toggleButton('ParkCustomerDial', 'on');

        toggleButton('HangupXferLine', 'off');

        toggleButton('HangupBothLines', 'on');
        
        activateLinks();
    }
}


// ################################################################################
// Send Hangup command for any Local call that is not in the quiet(7) entry - used to stop manual dials even if no connect
function DialTimeHangup(tasktypecall) {
    if ( (RedirectXFER < 1) && (leaving_threeway < 1) ) {
        //alert("RedirecTxFEr|" + RedirecTxFEr);
        var queryCID = "HTvdcW" + epoch_sec + user_abb;
        var postData = {
            goAction: 'goHangupConfDial',
            goUser: uName,
            goPass: uPass,
            goCampaign: campaign,
            goExten: session_id,
            goServerIP: server_ip,
            goSessionName: session_name,
            goExtContext: ext_context,
            goQueryCID: queryCID,
            goQMExtension: qm_extension,
            responsetype: 'json'
        };
            
        $.ajax({
            type: 'POST',
            url: '<?=$goAPI?>/goAgent/goAPI.php',
            processData: true,
            data: postData,
            dataType: "json",
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            }
        })
        .done(function (result) {
            //alert(result.message + "\n" + tasktypecall + "\n" + leaving_threeway);
        });
    }
}


function btnTransferCall(showxfervar, showoffvar) {
    if (vicidial_transfers == '1') {
        //XferAgentSelectLink();

        if (showxfervar == 'ON') {
            toggleButton('ParkCall', 'off');
            HKbutton_allowed = 0;
            
            $("#transfer-conference").modal({
                keyboard: false,
                backdrop: false,
                show: true
            });
            
            toggleButton('TransferCall', 'XFEROFF');
            if ( (quick_transfer_button_enabled > 0) && (quick_transfer_button_locked < 1) ) {
                toggleButton('QuickTransfer', 'off');
            }
            if (custom_3way_button_transfer_enabled > 0) {
                //toggleButton('CustomTransfer', 'off');
            }
        } else {
            HKbutton_allowed = 1;
            $("#transfer-conference").modal('hide');
            $("#agentdirectlink").hide();
            if (showoffvar == 'YES') {
                toggleButton('TransferCall', 'XFERON');

                if ( (quick_transfer_button == 'IN_GROUP') || (quick_transfer_button == 'LOCKED_IN_GROUP') ) {
                    toggleButton('QuickTransfer', 'on');
                    $("#btnQuickTransfer").attr('onclick', "mainxfer_send_redirect('XfeRLOCAL','" + lastcustchannel + "','" + lastcustserverip + "','','','" + quick_transfer_button_locked + "');return false;");
                }
                if ( (quick_transfer_button == 'PRESET_1') || (quick_transfer_button == 'PRESET_2') || (quick_transfer_button == 'PRESET_3') || (quick_transfer_button == 'PRESET_4') || (quick_transfer_button == 'PRESET_5') || (quick_transfer_button == 'LOCKED_PRESET_1') || (quick_transfer_button == 'LOCKED_PRESET_2') || (quick_transfer_button == 'LOCKED_PRESET_3') || (quick_transfer_button == 'LOCKED_PRESET_4') || (quick_transfer_button == 'LOCKED_PRESET_5') ) {
                    toggleButton('QuickTransfer', 'on');
                    $("#btnQuickTransfer").attr('onclick', "mainxfer_send_redirect('XfeRBLIND','" + lastcustchannel + "','" + lastcustserverip + "','','','" + quick_transfer_button_locked + "');return false;");
                }
                if (custom_3way_button_transfer_enabled > 0) {
                    //custom_button_transfer();return false;
                    //toggleButton('CustomTransfer', 'on');
                }
                
                toggleButton('ParkCall', 'on');
            }
        }
        if (three_way_call_cid == 'AGENT_CHOOSE') {
            if ( (active_group_alias.length < 1) && (LIVE_default_group_alias.length > 1) && (LIVE_caller_id_number.length > 3) ) {
                active_group_alias = LIVE_default_group_alias;
                cid_choice = LIVE_caller_id_number;
            }
            $("#transfer-dial-group-selected").html("<?=$lh->translationFor('group_alias')?>: " + active_group_alias);
            $("#transfer-cid").html("<a href=\"#\" onclick=\"GroupAliasSelectContent_create('1');\"><?=$lh->translationFor('choose_group_alias')?></a>");
        } else {
            $("#transfer-cid").html('');
            $("#transfer-dial-group-selected").html('');
        }
    } else {
        if (showxfervar != 'OFF') {
            swal("<?=$lh->translationFor('no_perm_to_transfer_calls')?>");
        }
    }
}


// ################################################################################
// OnChange function for transfer group select list
function XferAgentSelectLink() {
    var XFERSelect = $("#transfer-local-closer");
    var XScheck = XFERSelect.val();
    if (typeof XScheck != null) {
        if (XScheck.match(/AGENTDIRECT/i)) {
            $("#agentdirectlink").show();
        } else {
            $("#agentdirectlink").hide();
        }
    }
}


// ################################################################################
// Start Hangup Functions for both 
function BothCallHangup() {
    if (lastcustchannel.length > 3)
        {DialedCallHangup();}
    if (lastxferchannel.length > 3)
        {XFerCallHangup();}
}


// ################################################################################
// Send Redirect command for live call to Manager sends phone name where call is going to
// Covers the following types: XFER, VMAIL, ENTRY, CONF, PARK, FROMPARK, XfeRLOCAL, XfeRINTERNAL, XfeRBLIND, VfeRVMAIL
function mainxfer_send_redirect(taskvar, taskxferconf, taskserverip, taskdebugnote, taskdispowindow, tasklockedquick) {
    var XFERSelect = $("#transfer-local-closer");
    var XFER_Group = XFERSelect.val();
    var ADvalue = $(".formXFER input[name='xfernumber']").val();
    if ( ( (taskvar == 'XfeRLOCAL') || (taskvar == 'XfeRINTERNAL') ) && (XFER_Group.match(/AGENTDIRECT/i)) && (ADvalue.length < 2) ) {
        swal("<?=$lh->translationFor('must_select_agent_to_transfer')?>");
    } else {
        blind_transfer = 1;
        var consultativexfer_checked = 0;
        if ($("#consultativexfer").is(':checked'))
            {consultativexfer_checked = 1;}
        if (taskvar == 'XfeRLOCAL')
            {consultativexfer_checked = 0;}

        if (taskxferconf == 'EMAIL') {
            // If it's an EMAIL you're transferring, it will work differently from a call, BIG TIME.  So a new function was made.
            var email_row_id = taskserverip; // Change variable name to what it actually is; too confusing otherwise
            //transfer_email(taskvar, document.vicidial_form.lead_id.value, document.vicidial_form.uniqueid.value, email_row_id);
        } else {
        //	conf_dialed = 1;
            if (auto_dial_level == 0) {RedirectXFER = 1;}
            var redirectvalue = MDchannel;
            var redirectserverip = lastcustserverip;
            var postData = {
                goAction: 'goXFERSendRedirect',
                goServerIP: server_ip,
                goSessionName: session_name,
                goUser: uName,
                goPass: uPass,
                responsetype: 'json'
            };
            if (redirectvalue.length < 2)
                {redirectvalue = lastcustchannel}
            if ( (taskvar == 'XfeRBLIND') || (taskvar == 'XfeRVMAIL') ) {
                if (tasklockedquick > 0)
                    {$(".formXFER input[name='xfernumber']").val(quick_transfer_button_orig);}
                var queryCID = "XBvdcW" + epoch_sec + user_abb;
                var blindxferdialstring = $(".formXFER input[name='xfernumber']").val();
                var blindxferhiddendialstring = $(".formXFER input[name='xfernumhidden']").val();
                if ( (blindxferdialstring.length < 1) && (blindxferhiddendialstring.length > 0) )
                    {blindxferdialstring = blindxferhiddendialstring;}
                var regXFvars = new RegExp("XFER","g");
                if (blindxferdialstring.match(regXFvars)) {
                    var regAXFvars = new RegExp("AXFER","g");
                    if (blindxferdialstring.match(regAXFvars)) {
                        var Ctasknum = blindxferdialstring.replace(regAXFvars, '');
                        if (Ctasknum.length < 2)
                            {Ctasknum = '83009';}
                        var closerxfercamptail = '_L';
                        if (closerxfercamptail.length < 3)
                            {closerxfercamptail = 'IVR';}
                        blindxferdialstring = Ctasknum + '*' + $(".formMain input[name='phone_number']").val() + '*' + $(".formMain input[name='lead_id']").val() + '*' + campaign + '*' + closerxfercamptail + '*' + user + '**' + live_call_seconds + '*';
                    }
                } else {
                    if ($(".formXFER input[name='xferoverride']").is(':checked') == false) {
                        if (three_way_dial_prefix == 'X') {var temp_dial_prefix = '';}
                        else {var temp_dial_prefix = three_way_dial_prefix;}
                        if (omit_phone_code == 'Y') {var temp_phone_code = '';}
                        else {var temp_phone_code = $(".formMain input[name='phone_code']").val();}

                        if (blindxferdialstring.length > 7)
                            {blindxferdialstring = temp_dial_prefix + "" + temp_phone_code + "" + blindxferdialstring;}
                    }
                }
                if (API_selected_callmenu.length > 0) {
                    var blindxferdialstring = 's';
                    var blindxfercontext = $(".formXFER input[name='xfernumber']").val();
                } else {
                    var blindxfercontext = ext_context;
                }
                no_delete_VDAC = 0;
                if (taskvar == 'XfeRVMAIL') {
                    var blindxferdialstring = campaign_am_message_exten + '*' + campaign + '*' + $(".formMain input[name='phone_code']").val() + '*' + $(".formMain input[name='phone_number']").val() + '*' + $(".formMain input[name='lead_id']").val();
                    no_delete_VDAC = 1;
                }
                if (blindxferdialstring.length < '1') {
                    postData = {};
                    taskvar = 'NOTHING';
                    swal("<?=$lh->translationFor('transfer_num_at_least_1_digit')?>:" + blindxferdialstring);
                } else {
                    postData['goTask'] = 'RedirectVD';
                    postData['goChannel'] = redirectvalue;
                    postData['goCallServerIP'] = redirectserverip;
                    postData['goQueryCID'] = queryCID;
                    postData['goExten'] = blindxferdialstring;
                    postData['goExtContext'] = blindxfercontext;
                    postData['goExtPriority'] = 1;
                    postData['goAutoDialLevel'] = auto_dial_level;
                    postData['goCampaign'] = campaign;
                    postData['goUniqueID'] = $(".formMain input[name='uniqueid']").val();
                    postData['goLeadID'] = $(".formMain input[name='lead_id']").val();
                    postData['goSeconds'] = live_call_seconds;
                    postData['goSessionID'] = session_id;
                    postData['goNoDeleteVDAC'] = no_delete_VDAC;
                    postData['goPresetName'] = $(".formXFER input[name='xfername']").val();
                }
            }
            if (taskvar == 'XfeRINTERNAL') {
                var closerxferinternal = '';
                taskvar = 'XfeRLOCAL';
            } else {
                var closerxferinternal = '9';
            }
            if (taskvar == 'XfeRLOCAL') {
                if (consult_custom_sent < 1)
                    {CustomerData_update();}

                $(".formXFER input[name='xfername']").val('');
                XFERSelect = $("#transfer-local-closer");
                XFER_Group = XFERSelect.val();
                if (API_selected_xfergroup.length > 1)
                    {XFER_Group = API_selected_xfergroup;}
                if (tasklockedquick > 0)
                    {XFER_Group = quick_transfer_button_orig;}
                var queryCID = "XLvdcW" + epoch_sec + user_abb;
                // 		 "90009*$group**$lead_id**$phone_number*$user*$agent_only*";
                var redirectdestination = closerxferinternal + '90009*' + XFER_Group + '**' + $(".formMain input[name='lead_id']").val() + '**' + dialed_number + '*' + user + '*' + $("#xfernumber").val() + '*';
                
                postData['goTask'] = 'RedirectVD';
                postData['goChannel'] = redirectvalue;
                postData['goCallServerIP'] = redirectserverip;
                postData['goQueryCID'] = queryCID;
                postData['goExten'] = redirectdestination;
                postData['goExtContext'] = ext_context;
                postData['goExtPriority'] = 1;
                postData['goAutoDialLevel'] = auto_dial_level;
                postData['goCampaign'] = campaign;
                postData['goUniqueID'] = $(".formMain input[name='uniqueid']").val();
                postData['goLeadID'] = $(".formMain input[name='lead_id']").val();
                postData['goSeconds'] = live_call_seconds;
                postData['goSessionID'] = session_id;
            }
            if (taskvar == 'XfeR') {
                var queryCID = "LRvdcW" + epoch_sec + user_abb;
                var redirectdestination = $(".formXFER input[name='extension_xfer']").val();
                
                postData['goTask'] = 'RedirectName';
                postData['goChannel'] = redirectvalue;
                postData['goCallServerIP'] = redirectserverip;
                postData['goQueryCID'] = queryCID;
                postData['goExtenName'] = redirectdestination;
                postData['goExtContext'] = ext_context;
                postData['goExtPriority'] = 1;
                postData['goSessionID'] = session_id;
            }
            if (taskvar == 'VMAIL') {
                var queryCID = "LVvdcW" + epoch_sec + user_abb;
                var redirectdestination = $(".formXFER input[name='extension_xfer']").val();
                
                postData['goTask'] = 'RedirectNameVmail';
                postData['goChannel'] = redirectvalue;
                postData['goCallServerIP'] = redirectserverip;
                postData['goQueryCID'] = queryCID;
                postData['goExten'] = voicemail_dump_exten;
                postData['goExtenName'] = redirectdestination;
                postData['goExtContext'] = ext_context;
                postData['goExtPriority'] = 1;
                postData['goSessionID'] = session_id;
            }
            if (taskvar == 'ENTRY') {
                var queryCID = "LEvdcW" + epoch_sec + user_abb;
                var redirectdestination = $(".formXFER input[name='extension_xfer_entry']").val();
                
                postData['goTask'] = 'Redirect';
                postData['goChannel'] = redirectvalue;
                postData['goCallServerIP'] = redirectserverip;
                postData['goQueryCID'] = queryCID;
                postData['goExten'] = redirectdestination;
                postData['goExtContext'] = ext_context;
                postData['goExtPriority'] = 1;
                postData['goSessionID'] = session_id;
            }
            if (taskvar == '3WAY') {
                var queryCID = "VXvdcW" + epoch_sec + user_abb;
                var redirectdestination = "NEXTAVAILABLE";
                var redirectXTRAvalue = XDchannel;
                var redirecttype_test = $(".formXFER input[name='xfernumber']").val();
                var XFERSelect = $("#transfer-local-closer");
                var XFER_Group = XFERSelect.val();
                if (API_selected_xfergroup.length > 1)
                    {var XFER_Group = API_selected_xfergroup;}
                var regRXFvars = new RegExp("CXFER","g");
                if ( ( (redirecttype_test.match(regRXFvars)) || (consultativexfer_checked > 0) ) && (local_consult_xfers > 0) )
                    {var redirecttype = 'RedirectXtraCXNeW';}
                else
                    {var redirecttype = 'RedirectXtraNeW';}
                Dispo3wayChannel = redirectvalue;
                Dispo3wayXTRAChannel = redirectXTRAvalue;
                Dispo3wayCallServerIP = redirectserverip;
                Dispo3wayCallXFERNumber = $(".formXFER input[name='xfernumber']").val();
                Dispo3wayCallCampTail = '';
                
                postData['goTask'] = redirecttype;
                postData['goChannel'] = redirectvalue;
                postData['goCallServerIP'] = redirectserverip;
                postData['goQueryCID'] = queryCID;
                postData['goExten'] = redirectdestination;
                postData['goExtContext'] = ext_context;
                postData['goExtPriority'] = 1;
                postData['goExtraChannel'] = redirectXTRAvalue;
                postData['goLeadID'] = $(".formMain input[name='lead_id']").val();
                postData['goPhoneCode'] = $(".formMain input[name='phone_code']").val();
                postData['goPhoneNumber'] = $(".formMain input[name='phone_number']").val();
                postData['goAutoDialLevel'] = auto_dial_level;
                postData['goCampaign'] = XFER_Group;
                postData['goFilename'] = taskdebugnote;
                postData['goAgentChannel'] = agentchannel;
                postData['goSessionID'] = session_id;
                postData['goProtocol'] = protocol;
                postData['goExtension'] = extension;

                if (taskdebugnote == 'FIRST') {
                    $("#DispoSelectHAspan").html("<a href='#' onclick='DispoLeavE3wayAgaiN()'><?=$lh->translationFor('leave_3way_again')?></a>");
                }
            }
            if (taskvar == 'ParK') {
                if (CallCID.length < 1) {
                    CallCID = MDnextCID;
                }
                blind_transfer = 0;
                var queryCID = "LPvdcW" + epoch_sec + user_abb;
                var redirectdestination = taskxferconf;
                var redirectdestserverip = taskserverip;
                var parkedby = protocol + "/" + extension;
                
                postData['goTask'] = 'RedirectToPark';
                postData['goChannel'] = redirectdestination;
                postData['goCallServerIP'] = redirectdestserverip;
                postData['goQueryCID'] = queryCID;
                postData['goExten'] = park_on_extension;
                postData['goExtContext'] = ext_context;
                postData['goExtPriority'] = 1;
                postData['goExtenName'] = 'park';
                postData['goParkedBy'] = parkedby;
                postData['goSessionID'] = session_id;
                postData['goCallCID'] = CallCID;
                postData['goUniqueID'] = $(".formMain input[name='uniqueid']").val();
                postData['goLeadID'] = $(".formMain input[name='lead_id']").val();
                postData['goCampaign'] = campaign;
                
                toggleButton('ParkCall', 'grab');
                $("#btnParkCall").attr('onclick', "mainxfer_send_redirect('FROMParK','" + redirectdestination + "','" + redirectdestserverip + "'); toggleButton('TransferCall', 'XFERON');");
                if ( (ivr_park_call == 'ENABLED') || (ivr_park_call == 'ENABLED_PARK_ONLY') ) {
                    //document.getElementById("ivrParkControl").innerHTML ="<img src='./images/ivrcallpark_OFF.png' style='padding-bottom:3px;' border='0' title='<?=$lh->translationFor('ivr_park_call')?>' alt='<?=$lh->translationFor('ivr_park_call')?>' />";
                    toggleButton('IVRParkCall', 'parkivr', false);
                }
                customerparked = 1;
                customerparkedcounter = 0;
            }
            if (taskvar == 'FROMParK') {
                blind_transfer = 0;
                var queryCID = "FPvdcW" + epoch_sec + user_abb;
                var redirectdestination = taskxferconf;
                var redirectdestserverip = taskserverip;

                if( (server_ip == taskserverip) && (taskserverip.length > 6) )
                    {var dest_dialstring = session_id;}
                else {
                    if(taskserverip.length > 6)
                        {var dest_dialstring = server_ip_dialstring + "" + session_id;}
                    else
                        {var dest_dialstring = session_id;}
                }
                
                postData['goTask'] = 'RedirectFromPark';
                postData['goChannel'] = redirectdestination;
                postData['goCallServerIP'] = redirectdestserverip;
                postData['goQueryCID'] = queryCID;
                postData['goExten'] = dest_dialstring;
                postData['goExtContext'] = ext_context;
                postData['goExtPriority'] = 1;
                postData['goCallCID'] = CallCID;
                postData['goCampaign'] = campaign;
                postData['goUniqueID'] = $(".formMain input[name='uniqueid']").val();
                postData['goLeadID'] = $(".formMain input[name='lead_id']").val();
                postData['goSessionID'] = session_id;
                
                toggleButton('ParkCall', 'park')
                $("#btnParkCall").attr('onclick', "mainxfer_send_redirect('ParK','" + redirectdestination + "','" + redirectdestserverip + "'); toggleButton('TransferCall', 'off');");
                if ( (ivr_park_call == 'ENABLED') || (ivr_park_call == 'ENABLED_PARK_ONLY') ) {
                    toggleButton('IVRParkCall', 'parkivr');
                    $("#btnIVRParkCall").attr('onclick', "mainxfer_send_redirect('ParKivr','" + redirectdestination + "','" + redirectdestserverip + "');");
                }
                customerparked = 0;
                customerparkedcounter = 0;
            }
            if (taskvar == 'ParKivr') {
                if (CallCID.length < 1) {
                    CallCID = MDnextCID;
                }
                blind_transfer = 0;
                var queryCID = "LPvdcW" + epoch_sec + user_abb;
                var redirectdestination = taskxferconf;
                var redirectdestserverip = taskserverip;
                var parkedby = protocol + "/" + extension;
                
                postData['goTask'] = 'RedirectToParkIVR';
                postData['goChannel'] = redirectdestination;
                postData['goCallServerIP'] = redirectdestserverip;
                postData['goQueryCID'] = queryCID;
                postData['goExten'] = park_on_extension;
                postData['goExtContext'] = ext_context;
                postData['goExtPriority'] = 1;
                postData['goExtenName'] = 'park';
                postData['goCampaign'] = campaign;
                postData['goUniqueID'] = $(".formMain input[name='uniqueid']").val();
                postData['goLeadID'] = $(".formMain input[name='lead_id']").val();
                postData['goParkedBy'] = parkedby;
                postData['goSessionID'] = session_id;
                postData['goCallCID'] = CallCID;
                
                toggleButton('ParkCall', 'grab', false);
                if (ivr_park_call == 'ENABLED_PARK_ONLY') {
                    toggleButton('IVRParkCall', 'grabivr', false);
                }
                if (ivr_park_call == 'ENABLED') {
                    toggleButton('IVRParkCall', 'grabivr', true);
                    $("#btnIVRParkCall").attr('onclick', "mainxfer_send_redirect('FROMParKivr','" + redirectdestination + "','" + redirectdestserverip + "');");
                }
                customerparked = 1;
                customerparkedcounter = 0;
            }
            if (taskvar == 'FROMParKivr') {
                blind_transfer = 0;
                var queryCID = "FPvdcW" + epoch_sec + user_abb;
                var redirectdestination = taskxferconf;
                var redirectdestserverip = taskserverip;

                if( (server_ip == taskserverip) && (taskserverip.length > 6) )
                    {var dest_dialstring = session_id;}
                else {
                    if(taskserverip.length > 6)
                        {var dest_dialstring = server_ip_dialstring + "" + session_id;}
                    else
                        {var dest_dialstring = session_id;}
                }
                
                postData['goTask'] = 'RedirectFromParkIVR';
                postData['goChannel'] = redirectdestination;
                postData['goCallServerIP'] = redirectdestserverip;
                postData['goQueryCID'] = queryCID;
                postData['goExten'] = dest_dialstring;
                postData['goExtContext'] = ext_context;
                postData['goExtPriority'] = 1;
                postData['goCampaign'] = campaign;
                postData['goUniqueID'] = $(".formMain input[name='uniqueid']").val();
                postData['goLeadID'] = $(".formMain input[name='lead_id']").val();
                postData['goSessionID'] = session_id;
                postData['goCallCID'] = CallCID;
                
                toggleButton('ParkCall', 'park');
                $("#btnParkCall").attr('onclick', "mainxfer_send_redirect('ParK','" + redirectdestination + "','" + redirectdestserverip + "'); toggleButton('TransferCall', 'off');");
                if ( (ivr_park_call=='ENABLED') || (ivr_park_call=='ENABLED_PARK_ONLY') ) {
                    toggleButton('IVRParkCall', 'parkivr');
                    $("#btnIVRParkCall").attr('onclick', "mainxfer_send_redirect('ParKivr','" + redirectdestination + "','" + redirectdestserverip + "');");
                }
                customerparked = 0;
                customerparkedcounter = 0;
            }

            var XFRDop = '';
            $.ajax({
                type: 'POST',
                url: '<?=$goAPI?>/goAgent/goAPI.php',
                processData: true,
                data: postData,
                dataType: "json",
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                }
            })
            .done(function (result) {
                if (result.result == 'success') {
                    if (typeof result.new_session != 'undefined') {
                        threeway_end = 1;
                        $("#callchannel").html('');
                        $("#callserverip").val('');
                        DialedCallHangup();
    
                        $(".formXFER input[name='xferchannel']").val('');
                        XFerCallHangup();
    
                        session_id = result.new_session;
                        $("#sessionIDspan").html(session_id);
    
                		console.log("session_id changed to: " + session_id);
                    }
                }
            //	alert(xferredirect_query + "\n" + xmlhttpXF.responseText);
            //	document.getElementById("debugbottomspan").innerHTML = xferredirect_query + "\n" + xmlhttpXF.responseText;
            });

            // used to send second Redirect for manual dial calls
            if ( (auto_dial_level == 0) && (taskvar != '3WAY') ) {
                RedirectXFER = 1;
                postData['goStage'] = '2NDXfeR';
                
                $.ajax({
                    type: 'POST',
                    url: '<?=$goAPI?>/goAgent/goAPI.php',
                    processData: true,
                    data: postData,
                    dataType: "json",
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    }
                })
                .done(function (result) {
                //	alert(RedirecTxFEr + "|" + result.message);
                });
    
                if ( (taskvar == 'XfeRLOCAL') || (taskvar == 'XfeRBLIND') || (taskvar == 'XfeRVMAIL') ) {
                    if (auto_dial_level == 0) {RedirecTxFEr = 1;}
                    $("#callchannel").html('');
                    $("#callserverip").val('');
                    //if( document.images ) { document.images['livecall'].src = image_livecall_OFF.src;}
                //	alert(RedirecTxFEr + "|" + auto_dial_level);
                    DialedCallHangup(taskdispowindow, '', '', no_delete_VDAC);
                }
            } // END ELSE FOR EMAIL CHECK
        }
    }
}

function sendXFERdtmf() {
    // For DTMF
    var xferDTMF = $("#xferdtmf").val();
    console.log('DTMF: '+xferDTMF);
    var dtmferror = 1;
    
    if (live_customer_call > 0 || XD_live_customer_call > 0) {
        // $.isNumeric(xferDTMF) -- Old condition
        if (xferDTMF.length > 0 && xferDTMF !== '') {
            //xferDTMF = parseInt(xferDTMF);
            // xferDTMF.between(0, 9, true) -- Old condition
            var regDTMF = /(^[0-9\#\*]+$)/ig;
            if (regDTMF.test(xferDTMF)) {
                var options = {
                    'duration': 160,
                    'eventHandlers': {
                        'succeeded': function(originator, response) {
                            console.log('DTMF succeeded', originator, response);
                        },
                        'failed': function(originator, response, cause) {
                            console.log('DTMF failed', originator, response, cause);
                        },
                    }
                };
                
                globalSession.sendDTMF(xferDTMF, options);
                dtmferror = 0;
            }
        }
        
        if (dtmferror > 0) {
            swal({
                title: '<?=$lh->translationFor('error')?>',
                text: '<?=$lh->translationFor('please_enter_a_valid_dtmf_number')?>',
                type: 'error'
            });
        }
    }
}

function GetCustomFields(listid, show, getData, viewFields) {
    if (typeof show === 'undefined') {
        show = false;
    }
    
    if (typeof getData === 'undefined') {
        getData = false;
    }
    
    if (!show) {
        if (typeof viewFields === 'undefined') {
            $("#custom_fields_content, #custom_br").slideUp();
        } else {
            $("#custom-field-content").slideUp();
        }
    }
    
    if (typeof listid === 'undefined' || listid === null || listid.length < 1) {
        if (!show) {
            if (typeof viewFields === 'undefined') {
                $("#custom_fields_content, #custom_br").slideUp();
            } else {
                $("#custom-field-content").slideUp();
            }
        } else {
            if (typeof viewFields === 'undefined') {
                $("#custom_fields_content, #custom_br").slideDown();
            } else {
                $("#custom-field-content").slideDown();
            }
        }
        getFields = false;
    }
    
    if (getData) {
        var postData = {
            module_name: 'GOagent',
            action: 'CustoMFielD',
            list_id: listid
        };
        $.ajax({
            type: 'POST',
            url: '<?=$module_dir?>GOagentJS.php',
            processData: true,
            data: postData,
            dataType: "json",
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            }
        })
        .done(function (result) {
            if (result !== null) {
                if (result.result == 'success') {
                    var customHTML = '';
                    if (typeof viewFields !== 'undefined' && viewFields) {
                        customHTML = '<div class="row"><div class="col-sm-12"><h4 style="border-bottom: 1px solid #f4f4f4; margin: 10.5px -15px; padding: 0 15px 15px;"><?=$lh->translationFor('custom_fields')?></h4></div></div>';
                    }
                    var fields = [];
                    var skipMe = false;
                    $.each(result.data, function(idx, val) {
                        var thisRank = val['field_rank'];
                        var thisOrder = val['field_order'];
                        
                        if (typeof fields[thisRank] === 'undefined') {
                           fields[thisRank] = [];
                        }
                        fields[thisRank][thisOrder] = val;
                    });
                    
                    var field_prefix = 'custom_';
                    if (typeof viewFields !== 'undefined') {
                        field_prefix = 'viewCustom_';
                    }
                    
                    var defaultFieldsArray = defaultFields.split(',');
                    $.each(fields, function(rank, data) {
                        if (typeof data === 'undefined') return true;
                        var order = 0;
                        var field_cnt = (data.length - 1);
                        customHTML += '<div class="row">';
                        while (order < field_cnt) {
                            order++;
                            var thisField = data[order];
                            
                            if (typeof thisField !== 'undefined') {
                                if (typeof viewFields !== 'undefined' && (defaultFieldsArray.indexOf(thisField.field_label) > -1 || thisField.field_label == 'lead_id')) {
                                    skipMe = true;
                                }
                                
                                if (!skipMe) {
                                    var isDisabled = (defaultFieldsArray.indexOf(thisField.field_label) > -1) ? ' disabled' : '';
                                    var column = (field_cnt > 1) ? (12 / field_cnt) : 12;
                                    var field_type = (thisField.field_type.length > 0) ? thisField.field_type : 'DISPLAY';
                                    customHTML += '<div class="col-sm-' + column + (field_type == 'HIDDEN' ? ' hidden' : '') + '">';
                                    if (field_type == 'TEXT' || field_type == 'HIDDEN') {
                                        var default_value = (thisField.field_default != 'NULL') ? thisField.field_default : '';
                                        customHTML += '<div class="mda-form-group">';
                                        customHTML += '<input id="' + field_prefix + thisField.field_label + '" data-type="' + field_type.toLowerCase() + '" name="' + field_prefix + thisField.field_label + '" type="'+ field_type.toLowerCase() +'" size="' + thisField.field_size + '" maxlength="' + thisField.field_max + '" value="' + default_value + '" class="mda-form-control ng-pristine ng-empty ng-invalid ng-invalid-required ng-touched"' + isDisabled + '>';
                                        if (field_type != 'HIDDEN') {
                                            customHTML += '<label for="' + field_prefix + thisField.field_label + '">' + thisField.field_name + '</label>';
                                        }
                                        customHTML += '</div>';
                                    } else if (field_type == 'AREA') {
                                        var default_value = (thisField.field_default != 'NULL') ? thisField.field_default : '';
                                        customHTML += '<div class="mda-form-group">';
                                        customHTML += '<textarea id="' + field_prefix + thisField.field_label + '" data-type="' + field_type.toLowerCase() + '" name="' + field_prefix + thisField.field_label + '" rows="' + thisField.field_max + '" cols="' + thisField.field_size + '" class="form-control ng-pristine ng-empty ng-invalid ng-invalid-required ng-touched textarea input-disabled note-editor note-editor-margin"' + isDisabled + '>' + default_value + '</textarea>';
                                        customHTML += '<label for="' + field_prefix + thisField.field_label + '">' + thisField.field_name + '</label>';
                                        customHTML += '</div>';
                                    } else if (field_type == 'DATE' || field_type == 'TIME') {
                                        var default_value = thisField.field_default;
                                        if (default_value == null || default_value.length < 1 || default_value == 'NULL') {
                                            var curr_date = new Date();
                                            var mon = (curr_date.getMonth() + 1);
                                            var day = curr_date.getDate();
                                            var year = (curr_date.getYear() + 1900);
                                            var hour = curr_date.getHours();
                                            var min = curr_date.getMinutes();
                                            var sec = curr_date.getSeconds();
                                            if (mon < 10) mon = "0" + mon;
                                            if (day < 10) day = "0" + day;
                                            if (hour < 10) hour = "0" + hour;
                                            if (min < 10) min = "0" + min;
                                            if (sec < 10) sec = "0" + sec;
                                            
                                            default_value = (field_type == 'DATE') ? year + "-" + mon + "-" + day : hour + ":" + min;
                                        }
                                        customHTML += '<div class="mda-form-group">';
                                        customHTML += '<input id="' + field_prefix + thisField.field_label + '" data-type="' + field_type.toLowerCase() + '" name="' + field_prefix + thisField.field_label + '" type="'+ field_type.toLowerCase() +'" value="' + default_value + '" class="mda-form-control ng-pristine ng-empty ng-invalid ng-invalid-required ng-touched"' + isDisabled + '>';
                                        customHTML += '<label for="' + field_prefix + thisField.field_label + '">' + thisField.field_name + '</label>';
                                        customHTML += '</div>';
                                    } else if (field_type == 'CHECKBOX' || field_type == 'RADIO') {
                                        var checkBox = thisField.field_options.split("\n");
                                        var default_check = thisField.field_default.split(",");
                                        customHTML += '<div class="mda-form-group">';
                                        if (thisField.multi_position == 'HORIZONTAL') {
                                            customHTML += '<div class="' + field_type.toLowerCase() + '">';
                                        }
                                        for (i = 0; i < checkBox.length; i++) {
                                            var checkBoxValue = checkBox[i].split(",");
                                            var isChecked = (default_check.indexOf(checkBoxValue[0]) > -1) ? 'checked' : '';
                                            if (thisField.multi_position == 'VERTICAL') {
                                                customHTML += '<div class="' + field_type.toLowerCase() + '">';
                                            }
                                            customHTML += '<label style="margin-right: 15px;">';
                                            customHTML += '<input data-type="' + field_type.toLowerCase() + '" type="' + field_type.toLowerCase() + '" name="' + field_prefix + thisField.field_label + '[]" id="' + field_prefix + thisField.field_label + '[]" value="' + checkBoxValue[0] + '"' + isDisabled + ' ' + isChecked + '>';
                                            customHTML += checkBoxValue[1];
                                            customHTML += '</label>';
                                            if (thisField.multi_position == 'VERTICAL') {
                                                customHTML += '</div>';
                                            }
                                        }
                                        if (thisField.multi_position == 'HORIZONTAL') {
                                            customHTML += '</div>';
                                        }
                                        customHTML += '<div class="customform-label">' + thisField.field_name + '</div>';
                                        customHTML += '</div>';
                                    } else if (field_type == 'SELECT' || field_type == 'MULTI') {
                                        var selectOptions = thisField.field_options.split("\n");
                                        var default_selected = thisField.field_default.split(",");
                                        var isMulti = (field_type == 'MULTI') ? 'multiple size="' + thisField.field_size + '"' : '';
                                        customHTML += '<div class="mda-form-group">';
                                        customHTML += '<select ' + isMulti + ' id="' + field_prefix + thisField.field_label + '" name="' + field_prefix + thisField.field_label + '" data-type="' + field_type.toLowerCase() + '" class="mda-form-control ng-pristine ng-empty ng-invalid ng-invalid-required ng-touched select"' + isDisabled + '>';
                                        for (i = 0; i < selectOptions.length; i++) {
                                            var selectOption = selectOptions[i].split(",");
                                            var isSelected = (default_selected.indexOf(selectOption[0]) > -1) ? 'selected' : '';
                                            customHTML += '<option value="' + selectOption[0] + '" ' + isSelected + '>' + selectOption[1] + '</option>';
                                        }
                                        customHTML += '</select>';
                                        customHTML += '<label for="' + field_prefix + thisField.field_label + '">' + thisField.field_name + '</label>';
                                        customHTML += '</div>';
                                    } else {
                                        var patt = /^\s/g;
                                        var display_content = (field_type != 'SCRIPT') ? thisField.field_default : thisField.field_options;
                                        if ((patt.test(display_content) && display_content.length < 2) || display_content.length < 1 || display_content == 'NULL') {
                                            display_content = "&nbsp;";
                                        }
                                        
                                        customHTML += '<div class="mda-form-group">';
                                        customHTML += '<span id="' + field_prefix + thisField.field_label + '" data-type="' + field_type.toLowerCase() + '" class="' + field_prefix + field_type.toLowerCase() + '" style="padding-left: 5px;">' + display_content + '</span>';
                                        if (field_type != 'SCRIPT') {
                                            customHTML += '<div class="customform-label">' + thisField.field_name + '</div>';
                                        }
                                        customHTML += '</div>';
                                    }
                                    customHTML += '</div>';
                                }
                                skipMe = false;
                            }
                        }
                        customHTML += '</div>';
                    });
                    
                    if (typeof viewFields === 'undefined') {
                        $("#custom_fields").html(customHTML);
                        if (show) {
                            $("#custom_fields_content, #custom_br").slideDown();
                        }
                    } else {
                        $("#custom-field-content").html(customHTML);
                        if (show) {
                            $("#custom-field-content").slideDown();
                        }
                    }
                    
                    getFields = true;
                }
            } else {
                unloadPreloader = true;
            }
        });
    }
}

function checkForCallbacks() {
    if (Object.keys(callback_alerts).length > 0 && (live_customer_call < 1 && XD_live_customer_call < 1 && AgentDispoing < 1) && !callback_alert) {
        var missedCB = false;
        var swalContent = '';
        $.each(callback_alerts, function(key, value) {
            var nowDate = serverdate;
            var dateParts = value.callback_time.match(/(\d+)-(\d+)-(\d+) (\d+):(\d+):(\d+)/);
            var cbDate = new Date(dateParts[1], parseInt(dateParts[2], 10) - 1, dateParts[3], dateParts[4], dateParts[5], dateParts[6]);
            var minsBetween = minutesBetween(nowDate, cbDate);
            if (!value.seen && (minsBetween <= 5 && minsBetween >= 0)) {
                callback_alert = true;
                AutoDial_Resume_Pause("VDADpause");
                swalContent  = '';
                <?php if( ECCS_BLIND_MODE === 'y' ){ ?>
		swalContent += '<div class="swal-callback" title="Call Back">';
                <?php } ?>
                swalContent += '<div style="padding: 0 30px; text-align: left; line-height: 24px;"><strong>Name:</strong> '+value.cust_name+'</div>';
                swalContent += '<div style="padding: 0 30px; text-align: left; line-height: 24px;"><strong>Phone:</strong> '+phone_number_format(value.phone_number)+' <span style="float:right;"><a class="btn btn-sm btn-success" onclick="NewCallbackCall('+key+', '+value.lead_id+');"><i class="fa fa-phone"></i></a> &nbsp; <a class="btn btn-sm btn-primary" onclick=\'ShowCBDatePicker('+key+', "'+value.callback_time+'", "'+value.comments+'");\'><i class="fa fa-calendar"></i></a></span></div>';
                swalContent += '<div style="padding: 0 30px; text-align: left; line-height: 24px;"><strong>Callback Date:</strong> '+value.callback_time+'</div>';
                swalContent += '<div style="padding: 0 30px; text-align: left; line-height: 24px;"><strong>Last Call Date:</strong> '+value.entry_time+'</div>';
                swalContent += '<div style="padding: 0 30px; text-align: left; line-height: 24px;"><strong>Comments:</strong> '+value.comments.replace(/\n/, "<br>")+'</div>';
                <?php if( ECCS_BLIND_MODE === 'y' ){ ?>
                swalContent += '</div>';
                <?php } ?>

                swal({
                    title: "<?=$lh->translateText('Call Back')?>",
                    text: swalContent,
                    type: "info",
                    html: true
                }, function(){
                    callback_alerts[key].seen = true;
                    
                    var postData = {
                        goAction: 'goGetCallbackCount',
                        goUser: uName,
                        goPass: uPass,
                        goSeen: true,
                        goCampaign: campaign,
                        goCallbackID: key,
                        goNoExpire: cb_noexpire,
                        responsetype: 'json'
                    };
                
                    $.ajax({
                        type: 'POST',
                        url: '<?=$goAPI?>/goAgent/goAPI.php',
                        processData: true,
                        data: postData,
                        dataType: "json",
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded'
                        }
                    })
                    .done(function (result) {
                        console.log(result);
                        callback_alert = false;
                    });
                });
            } else if (!value.seen && minsBetween < 0 && just_logged_in) {
                callback_alert = true;
                missedCB = true;
               <?php if(ECCS_BLIND_MODE === 'y') { ?>
                swalContent += '<tr>';
                swalContent += '<td title="'+value.cust_name+'">'+value.cust_name+'</td>';
                swalContent += '<td title="'+phone_number_format(value.phone_number)+'">'+phone_number_format(value.phone_number)+' <span style="float:right;"><a class="btn btn-sm btn-success" onclick="NewCallbackCall('+key+', '+value.lead_id+');"><i class="fa fa-phone"></i></a> &nbsp; <a class="btn btn-sm btn-primary" onclick=\'ShowCBDatePicker('+key+', "'+value.callback_time+'", "'+value.comments+'");\'><i class="fa fa-calendar"></i></a></span></td>';
                swalContent += '<td title="'+value.callback_time+'">'+value.callback_time+'</td>';
                swalContent += '<td title="'+value.entry_time+'">'+value.entry_time+'</td>';
                swalContent += '<td title="'+value.comments+'">'+value.comments+'</td>';
                swalContent += '</tr>';
		<?php } else { ?>
                swalContent += '<tr>';
                swalContent += '<td>'+value.cust_name+'</td>';
                swalContent += '<td>'+phone_number_format(value.phone_number)+' <span style="float:right;"><a class="btn btn-sm btn-success" onclick="NewCallbackCall('+key+', '+value.lead_id+');"><i class="fa fa-phone"></i></a> &nbsp; <a class="btn btn-sm btn-primary" onclick=\'ShowCBDatePicker('+key+', "'+value.callback_time+'", "'+value.comments+'");\'><i class="fa fa-calendar"></i></a></span></td>';
                swalContent += '<td>'+value.callback_time+'</td>';
                swalContent += '<td>'+value.entry_time+'</td>';
                swalContent += '<td>'+value.comments+'</td>';
                swalContent += '</tr>';
		<?php } ?>
            } else if (!value.seen && minsBetween < 0) {
                var recurringDate = new Date(serverdate.getFullYear(), serverdate.getMonth() + 1, serverdate.getDate(), dateParts[4], dateParts[5], dateParts[6]);
                var newMinsBetween = minutesBetween(nowDate, recurringDate);
                if (newMinsBetween <= 5 && newMinsBetween >= 0) {
                    callback_alert = true;
                    AutoDial_Resume_Pause("VDADpause");
                    swalContent  = '';
                    swalContent += '<div style="padding: 0 30px; text-align: left; line-height: 24px;"><strong>Name:</strong> '+value.cust_name+'</div>';
                    swalContent += '<div style="padding: 0 30px; text-align: left; line-height: 24px;"><strong>Phone:</strong> '+phone_number_format(value.phone_number)+' <span style="float:right;"><a class="btn btn-sm btn-success" onclick="NewCallbackCall('+key+', '+value.lead_id+');"><i class="fa fa-phone"></i></a> &nbsp; <a class="btn btn-sm btn-primary" onclick=\'ShowCBDatePicker('+key+', "'+value.callback_time+'", "'+value.comments+'");\'><i class="fa fa-calendar"></i></a></span></div>';
                    swalContent += '<div style="padding: 0 30px; text-align: left; line-height: 24px;"><strong>Callback Date:</strong> '+value.callback_time+'</div>';
                    swalContent += '<div style="padding: 0 30px; text-align: left; line-height: 24px;"><strong>Last Call Date:</strong> '+value.entry_time+'</div>';
                    swalContent += '<div style="padding: 0 30px; text-align: left; line-height: 24px;"><strong>Comments:</strong> '+value.comments.replace(/\n/, "<br>")+'</div>';
                    
                    swal({
                        title: "<?=$lh->translateText('Call Back')?>",
                        text: swalContent,
                        type: "info",
                        html: true
                    }, function(){
                        callback_alerts[key].seen = true;
                        
                        var postData = {
                            goAction: 'goGetCallbackCount',
                            goUser: uName,
                            goPass: uPass,
                            goSeen: true,
                            goCampaign: campaign,
                            goCallbackID: key,
                            goNoExpire: cb_noexpire,
                            responsetype: 'json'
                        };
                    
                        $.ajax({
                            type: 'POST',
                            url: '<?=$goAPI?>/goAgent/goAPI.php',
                            processData: true,
                            data: postData,
                            dataType: "json",
                            headers: {
                                'Content-Type': 'application/x-www-form-urlencoded'
                            }
                        })
                        .done(function (result) {
                            console.log(result);
                            callback_alert = false;
                        });
                    });
                }
            }
        });
        
        if (just_logged_in && missedCB && swalContent !== '') {
            just_logged_in = false;
            
            $("#missed-callbacks-content table tbody").html(swalContent);
            $("#missed-callbacks-loading").hide();
            $("#missed-callbacks-content").show();
            $("#view-missed-callbacks").modal({
                keyboard: false,
                backdrop: 'static',
                show: true
            });
        }
    }
}

function replaceCustomFields(view) {
    var defaultFieldsArray = defaultFields.split(',');
    var getCFields = $(".formMain #custom_fields [id^='custom_']");
    if (typeof view !== 'undefined') {
        getCFields = $("[id^='viewCustom_']");
    }
    $.each(getCFields, function() {
        var fieldType = $(this).data('type');
        if (/checkbox|radio|multi|select/.test(fieldType)) return true;
        var pattern = /--A--\w+--B--/g;
        var replaceThis = (/script|display|readonly/i.test(fieldType)) ? $(this).html() : $(this).val();
        var output = replaceThis.match(pattern);
        if (output != null) {
            for (i=0; i < output.length; i++) {
                output[i] = output[i].replace(/--[AB]--/g, '');
                var newValue = '';
                if (typeof view === 'undefined') {
                    if (/first_name|middle_initial|last_name/.test(output[i])) {
                        if (typeof view === 'undefined') {
                            newValue = $("#cust_full_name a[id='" + output[i] + "']").editable('getValue', true);
                        }
                    } else {
                        var tagName = $(".formMain [name='" + output[i] + "']").prop('tagName');
                        newValue = $(".formMain " + tagName.toLowerCase() + "[name='" + output[i] + "']").val();
                    }
                } else {
                    var tagName = $("[name='viewCust_" + output[i] + "']").prop('tagName');
                    newValue = $(tagName.toLowerCase() + "[name='viewCust_" + output[i] + "']").val();
                }
                replaceThis = replaceThis.replace("--A--" + output[i] + "--B--", newValue);
            }
            
            if (/script|display|readonly/i.test(fieldType)) {
                $(this).html(replaceThis);
            } else {
                $(this).val(replaceThis);
            }
        } else {
            var fieldID = $(this).attr('id').replace(/^custom_|viewCust_/, '');
            if (defaultFieldsArray.indexOf(fieldID) > -1 && typeof view === 'undefined') {
                var newValue = '';
                
                if (/first_name|middle_initial|last_name/.test(fieldID)) {
                    newValue = $("#cust_full_name a[id='" + fieldID + "']").editable('getValue', true);
                } else {
                    var tagName = $(".formMain [name='" + fieldID + "']").prop('tagName');
                    newValue = $(".formMain " + tagName.toLowerCase() + "[name='" + fieldID + "']").val();
                }
                
                if (/script|display|readonly/i.test(fieldType)) {
                    $(this).html(newValue);
                } else {
                    $(this).val(newValue);
                }
            }
        }
    });
    
    $(".formMain input[name='FORM_LOADED']").val('1');
}

function ViewCustInfo(leadid) {
    $(".cust-preloader").show();
    $("#customer-info-content").hide();
    $("#custom-field-content").hide();
    $("#convert-customer").prop('checked', false);
    $("#cust-info-submit").prop('disabled', true);
    $("#view-customer-info").modal({
        backdrop: 'static',
        show: true
    });
    
    var postData = {
        goAction: 'goGetCustomerInfo',
        goUser: uName,
        goPass: uPass,
        goLeadID: leadid,
        responsetype: 'json'
    };
    $.ajax({
        type: 'POST',
        url: '<?=$goAPI?>/goAgent/goAPI.php',
        processData: true,
        data: postData,
        dataType: "json",
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
        }
    })
    .done(function (result) {
        if (result.result == 'success') {
            var lead_info = result.lead_info;
            var custom_info = result.custom_info;
            var infoHtml = '';
            var infoTitle = '';
            var colNum = 12;
            var maxLength = 20;
            $.each(lead_info, function(key, val) {
                if (key == 'address3') {
                    //do nothing
                } else if (/lead_id|list_id/.test(key)) {
                    infoHtml += '<input type="hidden" id="viewCust_'+key+'" name="viewCust_'+key+'" value="'+val+'" />';
                } else if (/title|first_name|middle_initial|last_name/.test(key)) {
                    if (key == 'title') {
                        infoHtml += '<div class="row">';
                    }
                    if (/title|middle_initial/.test(key)) {
                        colNum = 2;
                        maxLength = (key == 'title') ? 4 : 1;
                    } else {
                        colNum = 4;
                        maxLength = 30;
                    }
                    infoTitle = key.replace(/_/g, ' ').toUpperFirstLetters();
                    infoHtml += '<div class="col-sm-'+colNum+'">\
                            <div class="mda-form-group label-floating">\
                                <input id="viewCust_'+key+'" name="viewCust_'+key+'" type="text" maxlength="'+maxLength+'" value="'+val+'" class="mda-form-control ng-pristine ng-empty ng-invalid ng-invalid-required ng-touched">\
                                <label for="viewCust_'+key+'">'+infoTitle+'</label>\
                            </div>\
                        </div>';
                    if (key == 'last_name') {
                        infoHtml += '</div>';
                    }
                } else if (/phone_code|phone_number|alt_phone/.test(key)) {
                    if (key == 'phone_number') {
                        infoHtml += '<div class="row">';
                    }
                    
                    colNum = 6;
                    maxLength = (key == 'phone_number') ? 18 : 12;
                    var disableThis = '';
                    infoTitle = key.replace(/_/g, ' ').toUpperFirstLetters();
                    if (key == 'phone_number' && disable_alter_custphone == 'Y') {
                        disableThis = 'disabled';
                    }
                    infoHtml += '<div class="col-sm-'+colNum+'">\
                            <div class="mda-form-group label-floating">\
                                <input id="viewCust_'+key+'" name="viewCust_'+key+'" type="text" maxlength="'+maxLength+'" value="'+val+'" class="mda-form-control ng-pristine ng-empty ng-invalid ng-invalid-required ng-touched" '+disableThis+'>\
                                <label for="viewCust_'+key+'">'+infoTitle+'</label>\
                            </div>\
                        </div>';
                    if (key == 'alt_phone') {
                        infoHtml += '</div>';
                    }
                } else if (/address|email/.test(key)) {
                    maxLength = (key == 'email') ? 70 : 100;
                    infoTitle = key.replace(/_/g, ' ').toUpperFirstLetters();
                    infoHtml += '<div class="row">\
                        <div class="col-sm-12">\
                            <div class="mda-form-group label-floating">\
                                <input id="viewCust_'+key+'" name="viewCust_'+key+'" type="text" maxlength="'+maxLength+'" value="'+val+'" class="mda-form-control ng-pristine ng-empty ng-invalid ng-invalid-required ng-touched">\
                                <label for="viewCust_'+key+'">'+infoTitle+'</label>\
                            </div>\
                        </div>\
                    </div>';
                } else if (/city|state|postal_code|country_code/.test(key)) {
                    if (key == 'city') {
                        infoHtml += '<div class="row">';
                    }
                    
                    colNum = 2;
                    maxLength = 2;
                    maxLength = (key == 'city') ? 50 : maxLength;
                    maxLength = (key == 'postal_code') ? 10 : maxLength;
                    maxLength = (key == 'country_code') ? 3 : maxLength;
                    colNum = (key == 'city') ? 5 : colNum;
                    colNum = (key == 'postal_code') ? 3 : colNum;
                    infoTitle = key.replace(/_/g, ' ').toUpperFirstLetters();
                    infoHtml += '<div class="col-sm-'+colNum+'">\
                            <div class="mda-form-group label-floating">\
                                <input id="viewCust_'+key+'" name="viewCust_'+key+'" type="text" maxlength="'+maxLength+'" value="'+val+'" class="mda-form-control ng-pristine ng-empty ng-invalid ng-invalid-required ng-touched">\
                                <label for="viewCust_'+key+'">'+infoTitle+'</label>\
                            </div>\
                        </div>';
                    if (key == 'country_code') {
                        infoHtml += '</div>';
                    }
                } else if (/gender|date_of_birth/.test(key)) {
                    infoTitle = key.replace(/_/g, ' ').toUpperFirstLetters();
                    if (key == 'gender') {
                        var selectNone = (val === '') ? 'selected' : '';
                        var selectMale = (val == 'M') ? 'selected' : '';
                        var selectFemale = (val == 'F') ? 'selected' : '';
                        infoHtml += '<div class="row">\
                            <div class="col-sm-6">\
                                <div class="mda-form-group label-floating">\
                                    <select id="viewCust_'+key+'" name="viewCust_'+key+'" value="'+val+'" class="mda-form-control ng-pristine ng-empty ng-invalid ng-invalid-required ng-touched select">\
                                        <option '+selectNone+' disabled value=""></option>\
                                        <option '+selectMale+' value="M">Male</option>\
                                        <option '+selectFemale+' value="F">Female</option>\
                                    </select>\
                                    <label for="viewCust_'+key+'">'+infoTitle+'</label>\
                                </div>\
                            </div>';
                    }
                    if (key == 'date_of_birth') {
                        infoHtml += '<div class="col-sm-6">\
                                <div class="mda-form-group label-floating">\
                                    <input type="date" id="viewCust_'+key+'" value="'+val+'" name="viewCust_'+key+'" class="mda-form-control ng-pristine ng-empty ng-invalid ng-invalid-required ng-touched">\
                                    <label for="viewCust_'+key+'">'+infoTitle+'</label>\
                                </div>\
                            </div>\
                        </div>';
                    }
                } else {
                    //do nothing right now
                }
            });
            
            var unloadPreloader = true;
            if (custom_fields_enabled > 0) {
                unloadPreloader = false;
                GetCustomFields(lead_info.list_id, false, true, true);
                var fieldsPopulated = setInterval(function() {
                    if (getFields) {
                        clearInterval(fieldsPopulated);
                        
                        if (custom_info !== null) {
                            $.each(custom_info, function(key, val) {
                                if (val == null) return true;
                                var custom_type = $("[id='viewCustom_"+key+"']").prop("tagName");
                                
                                switch (custom_type) {
                                    case "INPUT":
                                    case "TEXTAREA":
                                        $("#custom-field-content [id='viewCustom_" + key + "']").val(val);
                                        break;
                                    case "SPAN":
                                    case "DIV":
                                        $("#custom-field-content [id='viewCustom_" + key + "']").html(val);
                                        break;
                                    case "SELECT":
                                        var selectThis = val.split(',');
                                        $.each($("#custom-field-content [id='viewCustom_" + key + "'] option"), function() {
                                            if (selectThis.indexOf($(this).val()) > -1) {
                                                $(this).prop('selected', true);
                                            } else {
                                                $(this).prop('selected', false);
                                            }
                                        });
                                        break;
                                    default:
                                        var checkThis = val.split(',');
                                        $.each($("#custom-field-content [id^='viewCustom_" + key + "']"), function() {
                                            if (checkThis.indexOf($(this).val()) > -1) {
                                                $(this).prop('checked', true);
                                            } else {
                                                $(this).prop('checked', false);
                                            }
                                        });
                                }
                            });
                        }
                        
                        replaceCustomFields(true);
                        $(".cust-preloader").hide();
                        GetCustomFields(null, true, false, true);
                    } else {
                        unloadPreloader = true;
                        $(".cust-preloader").hide();
                    }
                }, 3000);
            }
            
            setTimeout(function() {
                if (unloadPreloader) {
                    $(".cust-preloader").hide();
                }
                $("#customer-info-content").html(infoHtml).slideDown();
                $("#cust-info-submit").prop('disabled', false);
                if (result.is_customer > 0) {
                    $("#convert-customer").prop('checked', true);
                    $("#convert-customer").prop('disabled', true);
                }
            }, 2000);
        } else {
            swal({
                title: '<?=$lh->translationFor('error')?>',
                text: result.message,
                type: 'error'
            });
        }
    });
}

function ShowURLTabs() {
    if (url_tab_first_url.length < 6 && url_tab_second_url.length < 6) return;
    
    if (url_tab_first_url.length > 5) {
        var first_title = (url_tab_first_title.length > 0) ? url_tab_first_title : '<?=$lh->translationFor('url_tab_one')?>';
        var first_tab = '<li id="url_tab_one" role="presentation">\
            <a href="#url_content_one" aria-controls="home" role="tab" data-toggle="tab" class="bb0">\
                <span class="fa fa-bookmark hidden"></span>\
                '+first_title+'\
            </a>\
        </li>';
        var first_content = '<div id="url_content_one" role="tabpanel" class="tab-pane">\
            <div class="row">\
                <div class="col-sm-12">\
                    <fieldset style="padding-bottom: 5px; margin-bottom: 5px;">\
                        <h4>\
                            <button type="button" class="btn btn-default btn-sm pull-right" onclick="reloadTab(\'ONE\');" style="margin-bottom: 2px;"><i class="fa fa-refresh"></i> <?=$lh->translationFor('refresh')?></button>\
                        </h4>\
                        <iframe id="url_tab_iframe_one" src="'+url_tab_first_url+'" style="width: 100%; height: 650px; border: dashed 1px #c0c0c0;">\
                            <?=$lh->translationFor('broser_not_support_iframes')?>\
                        </iframe>\
                    </fieldset>\
                </div>\
            </div>\
        </div>';
        
        $(first_tab).insertAfter($("#agent_tablist li").last());
        $(first_content).insertAfter($("#agent_tabs div[class^='tab-pane']").last());
    }
    
    if (url_tab_second_url.length > 5) {
        var second_title = (url_tab_second_title.length > 0) ? url_tab_second_title : '<?=$lh->translationFor('url_tab_two')?>';
        var second_tab = '<li id="url_tab_two" role="presentation">\
            <a href="#url_content_two" aria-controls="home" role="tab" data-toggle="tab" class="bb0">\
                <span class="fa fa-bookmark hidden"></span>\
                '+second_title+'\
            </a>\
        </li>';
        var second_content = '<div id="url_content_two" role="tabpanel" class="tab-pane">\
            <div class="row">\
                <div class="col-sm-12">\
                    <fieldset style="padding-bottom: 5px; margin-bottom: 5px;">\
                        <h4>\
                            <button type="button" class="btn btn-default btn-sm pull-right" onclick="reloadTab(\'TWO\');" style="margin-bottom: 2px;"><i class="fa fa-refresh"></i> <?=$lh->translationFor('refresh')?></button>\
                        </h4>\
                        <iframe id="url_tab_iframe_two" src="'+url_tab_second_url+'" style="width: 100%; height: 650px; border: dashed 1px #c0c0c0;">\
                            <?=$lh->translationFor('broser_not_support_iframes')?>\
                        </iframe>\
                    </fieldset>\
                </div>\
            </div>\
        </div>';
        
        $(second_tab).insertAfter($("#agent_tablist li").last());
        $(second_content).insertAfter($("#agent_tabs div[class^='tab-pane']").last());
    }
}

function reloadTab(what) {
    if (what == 'TWO') {
        $('#url_tab_iframe_two').attr( 'src', url_tab_second_url);
    } else {
        $('#url_tab_iframe_one').attr( 'src', url_tab_first_url);
    }
}

function removeTabs() {
    $("#url_tab_one").remove();
    $("#url_content_one").remove();
    $("#url_tab_two").remove();
    $("#url_content_two").remove();
    $("#agent_tablist li").first().addClass('active');
    $("#agent_tabs div[id='contact_info']").first().addClass('active');
}

function PauseCodeSelectBox() {
    $("#select-pause-codes").modal({
        keyboard: false,
        backdrop: 'static'
    });
    PauseCodeSelectContent_create();
}

// ################################################################################
// Generate the Pause Code Chooser panel
function PauseCodeSelectContent_create() {
    var move_on = 1;
    if ( (AutoDialWaiting == 1) || (live_customer_call == 1) || (alt_dial_active == 1) || (MD_channel_look == 1) || (in_lead_preview_state == 1) ) {
        if ((auto_pause_precall == 'Y') && ( (agent_pause_codes_active == 'Y') || (agent_pause_codes_active == 'FORCE') ) && (AutoDialWaiting == 1) && (live_customer_call != 1) && (alt_dial_active != 1) && (MD_channel_look != 1) && (in_lead_preview_state != 1) ) {
            agent_log_id = AutoDial_Resume_Pause("VDADpause",'','','','','1','');
        } else {
            move_on = 0;
            swal("<?=$lh->translationFor('must_be_pause_to_enter_code')?>");
        }
    }
    if (move_on == 1) {
        if (APIManualDialQueue > 0) {
            PauseCodeSelectSubmit('NXDIAL');
        } else {
            WaitingForNextStep = 1;
            PauseCode_HTML = '';
            $("input[name='PauseCodeSelection']").val('');		
            var pause_codes_count_half = parseInt(pause_codes_count / 2);
            PauseCode_HTML = "<table cellpadding='5' cellspacing='5' width='100%' style='-webkit-touch-callout: none; -webkit-user-select: none; -khtml-user-select: none; -moz-user-select: none; -ms-user-select: none; user-select: none; margin: 0 auto;'><tr><td colspan='2'>&nbsp; <b><?=$lh->translationFor('pause_code')?></b><br><br></td></tr><tr><td bgcolor='#FFFFFF' height='300px' width='auto' valign='top' class='PauseCodeSelectA' style='white-space: nowrap;'>";
            var loop_ct = 0;
            while (loop_ct < pause_codes_count) {
                PauseCode_HTML = PauseCode_HTML + "<span id='pause-code-"+pause_codes[loop_ct]+"' style='cursor:pointer;color:#77a30a;' onclick=\"PauseCodeSelectSubmit('" + pause_codes[loop_ct] + "');return false;\">&nbsp; <span class='hidden-xs'>" + pause_codes[loop_ct] + " - " + pause_codes_names[loop_ct] + "</span><span class='hidden-sm hidden-md hidden-lg'>" + pause_codes_names[loop_ct] + "</span></span> &nbsp;<br /><br />";
                loop_ct++;
                if (loop_ct == pause_codes_count_half && !isMobile) {
                    PauseCode_HTML = PauseCode_HTML + "</td><td bgcolor='#FFFFFF' height='300px' width='auto' valign='top' class='PauseCodeSelectB' style='white-space: nowrap;'>";
                }
            }

            if (agent_pause_codes_active == 'FORCE' && is_logged_in) {
                $("#btn-pause-code-back").hide();
            } else {
                $("#btn-pause-code-back").show();
            }
            
            PauseCode_HTML = PauseCode_HTML + "</td></tr></table>";
            $("#PauseCodeSelectContent").html(PauseCode_HTML);
        }
    }
    if (focus_blur_enabled == 1) {
        //document.inert_form.inert_button.focus();
        //document.inert_form.inert_button.blur();
    }
}

// ################################################################################
// Submit the Pause Code 
function PauseCodeSelectSubmit(newpausecode) {
    WaitingForNextStep = 0;

    var postData = {
        goAction: 'goPauseCodeSubmit',
        goUser: uName,
        goPass: uPass,
        goCampaign: campaign,
        goExtension: extension,
        goServerIP: server_ip,
        goSessionName: session_name,
        goStatus: newpausecode,
        goAgentLogID: agent_log_id,
        goProtocol: protocol,
        goPhoneIP: phone_ip,
        goEnableSIPSAKMessages: enable_sipsak_messages,
        goStage: pause_code_counter,
        goCampaignCID: LastCallCID,
        goAutoDialLevel: starting_dial_level,
        responsetype: 'json'
    };
    
    pause_code_counter++;
    
    $.ajax({
        type: 'POST',
        url: '<?=$goAPI?>/goAgent/goAPI.php',
        processData: true,
        data: postData,
        dataType: "json",
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
        }
    })
    .done(function (result) {
        if (result.result == 'success') {
            $("#select-pause-codes").modal('hide');
            agent_log_id = result.agent_log_id;
        }
        
        if (!!$.prototype.snackbar) {
            if (result.result == 'success') {
                $.snackbar({id: "agentPaused", content: "<i class='fa fa-info-circle fa-lg text-success' aria-hidden='true'></i>&nbsp; " + result.message, timeout: 10000, htmlAllowed: true});
            } else {
                $.snackbar({content: "<i class='fa fa-exclamation-circle fa-lg text-warning' aria-hidden='true'></i>&nbsp; " + result.message, timeout: 3000, htmlAllowed: true});
            }
        }
    });
    
    //return agent_log_id;
    LastCallCID = '';
    scroll(0,0);
}

// ################################################################################
// pull the script contents sending the webform variables to the script display script
function LoadScriptContents() {
    var new_script_content = null;
    var postData = {
        goAction: 'goGetScriptContents',
        goServerIP: server_ip,
        goSessionName: session_name,
        goUser: uName,
        goPass: uPass,
        goScrollDIV: 1,
        responsetype: 'json'
    };
    
    var new_vars = {};
    var new_web_vars = web_form_vars.replace(/^\?/g, '');
    var web_vars_arr = new_web_vars.split('&');
    $.each(web_vars_arr, function(idx, val) {
        if (val.length > 0) {
            var vars_arr = val.split('=');
            new_vars[vars_arr[0]] = vars_arr[1];
        }
    });
    
    postData = $.extend(postData, new_vars);
    $.ajax({
        type: 'POST',
        url: '<?=$goAPI?>/goAgent/goAPI.php',
        processData: true,
        data: postData,
        dataType: "json",
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
        }
    })
    .done(function (result) {
        if (result.result == 'success') {
            new_script_content = result.content;
            new_script_content = new_script_content.replace(" + ", "!PLUS!");
            new_script_content = new_script_content.replace(/\+/g, " ");
            new_script_content = new_script_content.replace("!PLUS!", " + ");
            $("#ScriptContents").html(new_script_content);
        }
    });
}

function ClearScript() {
    $("#ScriptContents").html('');
}

// ################################################################################
// Submitting the callback date and time to the system
function CallBackDateSubmit() {
    CallBackLeadStatus = $("#DispoSelection").val();
    CallBackDateTime = $("#callback-date").val();
    CallBackComments = $("#callback-comments").val();

    if ($("#CallBackOnlyMe").prop('checked')) {
        CallBackRecipient = 'USERONLY';
    } else {
        CallBackRecipient = 'ANYONE';
    }
    
    $("#CallBackOnlyMe").prop('checked', false);
    if (my_callback_option == 'CHECKED')
        {$("#CallBackOnlyMe").prop('checked', true);}
    $("#callback-date").val('');
    $("#callback-comments").val('');
    
    $("#DispoSelection").val('CBHOLD');
    $("#callback-datepicker").modal('hide');
    DispoSelectSubmit();
    <?php if( ECCS_BLIND_MODE === 'y' ) { ?>
	enable_eccs_shortcuts = 1;
    <?php } ?>
}


function VolumeControl(taskdirection, taskvolchannel, taskagentmute) {
    if (taskagentmute=='GOagent') {
        taskvolchannel = agentchannel;
    }
    
    var queryCID = "VCagcW" + epoch_sec + user_abb;
    var volchanvalue = taskvolchannel;
    
    var postData = {
        goAction: 'goVolumeControl',
        goServerIP: server_ip,
        goSessionName: session_name,
        goUser: uName,
        goPass: uPass,
        goChannel: volchanvalue,
        goStage: taskdirection,
        goExten: session_id,
        goExtContext: ext_context,
        goQueryCID: queryCID,
        responsetype: 'json'
    };
    
    $.ajax({
        type: 'POST',
        url: '<?=$goAPI?>/goAgent/goAPI.php',
        processData: true,
        data: postData,
        dataType: "json",
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
        }
    })
    .done(function (result) {
        //nothing to do here...
    });
    
    if (taskagentmute=='GOagent') {
        if (taskdirection=='MUTING') {
            $.notifyClose('bottom-left');
            notifyMe("<?=$lh->translationFor('you_have_turned_off_mic')?>", 'warning', 'mic_off', 0);
        } else {
            notifyMe("<?=$lh->translationFor('you_have_turned_on_mic')?>", 'success', 'mic');
        }
    }
}


// ################################################################################
// Finish the wrapup timer early
function TimerActionRun(taskaction, taskdialalert) {
    var next_action = 0;
    if (taskaction == 'DialAlert') {
        //document.getElementById("TimerContentSpan").innerHTML = "<b><?=$lh->translationFor('dial_alert')?>:<br /><br />" + taskdialalert.replace("\n","<br />") + "</b>";

        //showDiv('TimerSpan');
    } else {
        if ( (timer_action_message.length > 0) || (timer_action == 'MESSAGE_ONLY') ) {
            //document.getElementById("TimerContentSpan").innerHTML = "<b><?=$lh->translationFor('timer_notification')?>: " + timer_action_seconds + " <?=$lang['seconds']?><br /><br />" + timer_action_message + "</b>";

            //showDiv('TimerSpan');
        }

        if (timer_action == 'WEBFORM') {
            //WebFormRefresH('NO','YES');
            window.open(TEMP_VDIC_web_form_address, web_form_target, 'toolbar=1,scrollbars=1,location=1,statusbar=1,menubar=1,resizable=1,width=640,height=450');
        }
        if (timer_action == 'WEBFORM2') {
            //WebFormTwoRefresH('NO','YES');
            window.open(TEMP_VDIC_web_form_address_two, web_form_target, 'toolbar=1,scrollbars=1,location=1,statusbar=1,menubar=1,resizable=1,width=640,height=450');
        }
        if (timer_action == 'D1_DIAL') {
            //DtMf_PreSet_a_DiaL();
        }
        if (timer_action == 'D2_DIAL') {
            //DtMf_PreSet_b_DiaL();
        }
        if (timer_action == 'D3_DIAL') {
            //DtMf_PreSet_c_DiaL();
        }
        if (timer_action == 'D4_DIAL') {
            //DtMf_PreSet_d_DiaL();
        }
        if (timer_action == 'D5_DIAL') {
            //DtMf_PreSet_e_DiaL();
        }
        if (timer_action == 'D1_DIAL_QUIET') {
            //DtMf_PreSet_a_DiaL('YES');
        }
        if (timer_action == 'D2_DIAL_QUIET') {
            //DtMf_PreSet_b_DiaL('YES');
        }
        if (timer_action == 'D3_DIAL_QUIET') {
            //DtMf_PreSet_c_DiaL('YES');
        }
        if (timer_action == 'D4_DIAL_QUIET') {
            //DtMf_PreSet_d_DiaL('YES');
        }
        if (timer_action == 'D5_DIAL_QUIET') {
            //DtMf_PreSet_e_DiaL('YES');
        }
        if ( (timer_action == 'HANGUP') && (live_customer_call == 1) ) {
            //hangup_timer_xfer();
        }
        if ( (timer_action == 'EXTENSION') && (live_customer_call == 1) && (timer_action_destination.length > 0) ) {
            //extension_timer_xfer();
        }
        if ( (timer_action == 'CALLMENU') && (live_customer_call == 1) && (timer_action_destination.length > 0) ) {
            //callmenu_timer_xfer();
        }
        if ( (timer_action == 'IN_GROUP') && (live_customer_call == 1) && (timer_action_destination.length > 0) ) {
            //ingroup_timer_xfer();
        }
        if (timer_action_destination.length > 0) {
            var regNS = new RegExp("nextstep---","ig");
            if (timer_action_destination.match(regNS)) {
                next_action = 1;
                timer_action = 'NONE';
                var next_action_array = timer_action_destination.split("nextstep---");
                var next_action_details_array = next_action_array[1].split("--");
                timer_action = next_action_details_array[0];
                timer_action_seconds = parseInt(next_action_details_array[1]);
                timer_action_seconds = (timer_action_seconds + live_call_seconds);
                timer_action_destination = next_action_details_array[2];
                timer_action_message = next_action_details_array[3];
                //alert("NEXT: " + timer_action + '|' + timer_action_message + '|' + timer_action_seconds + '|' + timer_action_destination + '|');
            }
        }
    }

    if (next_action < 1)
        {timer_action = 'NONE';}	
}

function getContactList() {
    $("#contacts-list").dataTable().fnDestroy();
    $("#contacts-list").css('width', '100%');
    $("#contacts-list tbody").empty();
    
    var postData = {
        goAction: 'goGetContactList',
        goUser: uName,
        goPass: uPass,
        //goLimit: 50, sabi ni sir chi itaas daw limit
	goLimit: 1000,
        goCampaign: campaign,
        goLeadSearchMethod: agent_lead_search_method,
        goIsLoggedIn: is_logged_in,
        responsetype: 'json'
    };
    
    $.ajax({
        type: 'POST',
        url: '<?=$goAPI?>/goAgent/goAPI.php',
        processData: true,
        data: postData,
        dataType: "json",
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
        }
    })
    .done(function (result) {
        if (result.result == 'success') {
            var leadsList = result.leads;
            $.each(leadsList, function(key, value) {
                var thisComments = value.comments;
                var commentTitle = '';
                if (thisComments !== null) {
                    if (thisComments.length > 20) {
                        commentTitle = ' title="'+thisComments+'"';
                        thisComments = thisComments.substring(0, 20) + "...";
                    }
                }
                
                var customer_name = (value.first_name || '') + ' ' + (value.middle_initial || '') + ' ' + (value.last_name || '');
                var last_call_time = (value.last_local_call_time || '0000-00-00 00:00:00');
                var appendThis = '<tr data-id="'+value.lead_id+'"><td>'+value.lead_id+'</td><td>'+customer_name+'</td><td>'+value.phone_number+'</td><td>'+last_call_time+'</td><td>'+value.campaign_id+'</td><td>'+value.status+'</td><td'+commentTitle+'>'+thisComments+'</td><td class="text-center" style="white-space: nowrap;"><button id="lead-info-'+value.lead_id+'" data-leadid="'+value.lead_id+'" onclick="ViewCustInfo('+value.lead_id+');" class="btn btn-info btn-sm" style="margin: 2px;" title="<?=$lh->translationFor('view_contact_info')?>"><i class="fa fa-file-text-o"></i></button><button id="dial-lead-'+value.lead_id+'" data-leadid="'+value.lead_id+'" onclick="ManualDialNext(\'\','+value.lead_id+','+value.phone_code+','+value.phone_number+',\'\',\'0\');" class="btn btn-primary btn-sm disabled" style="margin: 2px;" title="<?=$lh->translationFor('call_contact_number')?>"><i class="fa fa-phone"></i></button></td></tr>';
                $("#contacts-list tbody").append(appendThis);
            });
            $("#contacts-list").css('width', '100%');
            $("#contacts-list").DataTable({
                "bDestroy": true,
                "aoColumnDefs": [{
                    "bSortable": false,
                    "aTargets": [ 7 ],
                }, {
                    "bSearchable": false,
                    "aTargets": [ 3, 5, 7 ]
                }, {
                    "sClass": "hidden-xs",
                    "aTargets": [ 0 ]
                }, {
                    "sClass": "hidden-xs hidden-sm",
                    "aTargets": [ 1 ]
                }, {
                    "sClass": "visible-md visible-lg",
                    "aTargets": [ 4, 5 ]
                }, {
                    "sClass": "visible-lg",
                    "aTargets": [ 3, 6 ]
                }],
                "fnInitComplete": function() {
                    $(".preloader").fadeOut('slow');
                }
            });
            $("#contacts-list_filter").parent('div').attr('class', 'col-sm-6 hidden-xs');
            $("#contacts-list_length").parent('div').attr('class', 'col-xs-12 col-sm-6');
            $("#contents-contacts").find("div.dataTables_info").parent('div').attr('class', 'col-xs-12 col-sm-6');
            $("#contents-contacts").find("div.dataTables_paginate").parent('div').attr('class', 'col-xs-12 col-sm-6');
            if (!is_logged_in || (is_logged_in && (use_webrtc && !phoneRegistered))) {
                $("button[id^='dial-lead-']").addClass('disabled');
            } else {
                $("button[id^='dial-lead-']").removeClass('disabled');
            }
            
            $('#contacts-list').on('draw.dt', function() {
                if (!is_logged_in || (is_logged_in && (use_webrtc && !phoneRegistered))) {
                    $("button[id^='dial-lead-']").addClass('disabled');
                } else {
                    $("button[id^='dial-lead-']").removeClass('disabled');
                }
            });
        } else {
            $(".preloader").fadeOut('slow');
            $("#contacts-list").DataTable();
            
            swal({
                title: '<?=$lh->translationFor('error')?>',
                text: result.message,
                type: 'error',
                html: true
            });
        }
    });
}

function NoneInSession() {
    //still on development
}

function ShowCBDatePicker(cbId, cbDate, cbComment) {
    reschedule_cb = true;
    reschedule_cb_id = cbId;
    
    if ($(".sweet-alert.visible").length > 0) {
        swal.close();
    }
    
    if (($("#view-missed-callbacks").data('bs.modal') || {}).isShown) {
        $("#view-missed-callbacks").modal('hide');
    }
    
    if (callback_alert) {
        callback_alerts[cbId].seen = true;
        
        var postData = {
            goAction: 'goGetCallbackCount',
            goUser: uName,
            goPass: uPass,
            goSeen: true,
            goCampaign: campaign,
            goCallbackID: cbId,
            goNoExpire: cb_noexpire,
            responsetype: 'json'
        };
    
        $.ajax({
            type: 'POST',
            url: '<?=$goAPI?>/goAgent/goAPI.php',
            processData: true,
            data: postData,
            dataType: "json",
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            }
        })
        .done(function (result) {
            console.log(result);
            callback_alert = false;
        });
    }
    
    // Change Calendar date
    var d = new Date();
    var currDate = new Date(serverdate.getFullYear(), serverdate.getMonth(), serverdate.getDate(), serverdate.getHours(), serverdate.getMinutes() + 15);
    var selectedDate = moment(currDate).format('YYYY-MM-DD HH:mm:00');
    $("#date-selected").html(moment(currDate).format('dddd, MMMM Do YYYY, h:mm a'));
    $("#callback-date").val(selectedDate);
    
    if (agentonly_callbacks > 0) {
        $("#my_callback_only p, #my_callback_only div").show();
    } else {
        $("#my_callback_only p, #my_callback_only div").hide();
    }
    $("#callback-datepicker").modal({
        keyboard: false,
        backdrop: 'static',
        show: true
    });
    
    $("#CallBackOnlyMe").prop('checked', false);
    if (my_callback_option == 'CHECKED')
        {$("#CallBackOnlyMe").prop('checked', true);}
    
    //var newDate = moment(cbDate).format('YYYY-MM-DD HH:mm:00');
    //$("#callback-date").val(newDate);
    $("#callback-comments").val(cbComment);
}

function ReschedCallback(cbId, cbDate, cbComment, cbOnly) {
    var postData = {
        goAction: 'goCallbackResched',
        goUser: uName,
        goPass: uPass,
        goCampaign: campaign,
        goCallbackID: cbId,
        goCallbackDate: cbDate,
        goCallbackComment: cbComment,
        goCallbackOnly: cbOnly,
        responsetype: 'json'
    };

    $.ajax({
        type: 'POST',
        url: '<?=$goAPI?>/goAgent/goAPI.php',
        processData: true,
        data: postData,
        dataType: "json",
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
        }
    })
    .done(function (data) {
        var thisStatus = (data.result == 'success') ? data.result : 'danger';
        if ( !!$.prototype.snackbar ) {
            $.snackbar({content: "<i class='fa fa-calendar fa-lg text-"+data.result+"' aria-hidden='true'></i>&nbsp; "+data.message, timeout: 3000, htmlAllowed: true});
        }
        CallBacksCountCheck();
        
        reschedule_cb = false;
        reschedule_cb_id = 0;
        callback_alert = false;
        $("#CallBackOnlyMe").prop('checked', false);
        if (my_callback_option == 'CHECKED')
            {$("#CallBackOnlyMe").prop('checked', true);}
        $("#callback-date").val('');
        $("#callback-comments").val('');
        $("#callback-datepicker").modal('hide');
    });
}

function goGetAvatar(account, size) {
    var defaultAvatar = '';
    var avatarInitials = 'initials';
    size = (typeof size === 'undefined') ? '64' : size;
    if (account === undefined || account == '') {
        var account = 'Dialed Client';
        defaultAvatar = 'src="<?php echo CRM_DEFAULTS_USER_AVATAR;?>"';
        avatarInitials = 'username';
    }
    var avatar = "<avatar username='"+account+"' "+defaultAvatar+" :size='"+size+"'></avatar>";
    
    return avatar;
}

function MainPanelToFront() {
    //history.pushState('', document.title, window.location.pathname);
    
    $("#cust_info").show();
    $("#loaded-contents").hide();
    $(".content-heading ol").html('<li class="active"><i class="fa fa-home"></i> <?=$lh->translationFor('home')?></li>');
}

function padlength(what){
    var output=(what.toString().length==1)? "0"+what : what
    return output
}

function minutesBetween( date1, date2 ) {
  //Get 1 minute in milliseconds
  var one_min=1000*60;

  // Convert both dates to milliseconds
  var date1_ms = date1.getTime();
  var date2_ms = date2.getTime();

  // Calculate the difference in milliseconds
  var difference_ms = date2_ms - date1_ms;
    
  // Convert back to minutes and return
  return Math.floor(difference_ms/one_min); 
}

function displaytime(){
    serverdate.setSeconds(serverdate.getSeconds()+1)
    var todaystring = todayarray[serverdate.getDay()];
    var datestring = montharray[serverdate.getMonth()]+" "+padlength(serverdate.getDate())+", "+serverdate.getFullYear();
    var AmPm = 'AM';
    var dispHour = serverdate.getHours();
    if (dispHour > 11) {AmPm = 'PM';}
    if (dispHour > 12) {
        dispHour = dispHour - 12;
    } else if (dispHour < 1) {
        dispHour = 12;
    }
    
    var year= serverdate.getYear()
    var month= serverdate.getMonth()
        month++;
    var daym= serverdate.getDate()
    var hours = serverdate.getHours();
    var min = serverdate.getMinutes();
    var sec = serverdate.getSeconds();
    var dayz= serverdate.getDay();
        dayz++;
    if (year < 1000) {year+=1900}
    if (month< 10) {month= "0" + month}
    if (daym< 10) {daym= "0" + daym}
    if (hours < 10) {hours = "0" + hours;}
    if (min < 10) {min = "0" + min;}
    if (sec < 10) {sec = "0" + sec;}
    var Tyear = (year-2000);
    
    filedate = year + "" + month + "" + daym + "-" + hours + "" + min + "" + sec;
    tinydate = Tyear + "" + month + "" + daym + "" + hours + "" + min + "" + sec;
    SQLdate = year + "-" + month + "-" + daym + " " + hours + ":" + min + ":" + sec;
    
    var timestring = padlength(dispHour)+":"+padlength(serverdate.getMinutes())+":"+padlength(serverdate.getSeconds())+" "+AmPm;
    $("#server_date").html(todaystring+", "+datestring+" "+timestring);
}


// ################################################################################
// zero-pad numbers or chop them to get to the desired length
function set_length(SLnumber, SLlength_goal, SLdirection) {
	var SLnumber = SLnumber + '';
	var begin_point = 0;
	var number_length = SLnumber.length;
	if (number_length > SLlength_goal) {
		if (SLdirection == 'right') {
			begin_point = (number_length - SLlength_goal);
			SLnumber = SLnumber.substr(begin_point, SLlength_goal);
		} else {
			SLnumber = SLnumber.substr(0,SLlength_goal);
        }
    }
    //alert(SLnumber + '|' + SLlength_goal + '|' + begin_point + '|' + SLdirection + '|' + SLnumber.length + '|' + number_length);
	var result = SLnumber + '';
	while(result.length < SLlength_goal) {
		result = "0" + result;
	}
	return result;
}


// decode the scripttext and scriptname so that it can be displayed
function URLDecode(encodedvar, scriptformat, urlschema, webformnumber) {
    // Replace %ZZ with equivalent character
    // Put [ERR] in output if %ZZ is invalid.
	var HEXCHAR = '0123456789ABCDEFabcdef'; 
	var encoded = encodedvar;
	var decoded = '';
	var web_form_varsX = '';
	var i = 0;
	var RGnl = new RegExp("[\\r]\\n","g");
	var RGtab = new RegExp("\t","g");
	var RGplus = new RegExp(" |\\t|\\n","g");
	var RGiframe = new RegExp("iframe","gi");
	var regWF = new RegExp("\\`|\\~|\\:|\\;|\\#|\\'|\\\"|\\{|\\}|\\(|\\)|\\*|\\^|\\%|\\$|\\!|\\%|\\r|\\t|\\n","ig");

	var xtest;
	xtest = unescape(encoded);
	encoded = utf8_decode(xtest);

	if (urlschema == 'DEFAULT') {
        var getFirstName = $("#cust_full_name a[id='first_name']").editable('getValue', true);
            getFirstName = (getFirstName !== ' ') ? getFirstName : '';
        var getMiddleName = $("#cust_full_name a[id='middle_initial']").editable('getValue', true);
            getMiddleName = (getMiddleName !== ' ') ? getMiddleName : '';
        var getLastName = $("#cust_full_name a[id='last_name']").editable('getValue', true);
            getLastName = (getLastName !== ' ') ? getLastName : '';
		web_form_varsX = 
		"&lead_id=" + $(".formMain input[name='lead_id']").val() + 
		"&vendor_id=" + $(".formMain input[name='vendor_lead_code']").val() + 
		"&list_id=" + $(".formMain input[name='list_id']").val() + 
		"&gmt_offset_now=" + $(".formMain input[name='gmt_offset_now']").val() + 
		"&phone_code=" + $(".formMain input[name='phone_code']").val() + 
		"&phone_number=" + $(".formMain input[name='phone_number']").val() + 
		"&title=" + $(".formMain input[name='title']").val() + 
		"&first_name=" + getFirstName + 
		"&middle_initial=" + getMiddleName + 
		"&last_name=" + getLastName + 
		"&address1=" + $(".formMain input[name='address1']").val() + 
		"&address2=" + $(".formMain input[name='address2']").val() + 
		"&address3=" + $(".formMain input[name='address3']").val() + 
		"&city=" + $(".formMain input[name='city']").val() + 
		"&state=" + $(".formMain input[name='state']").val() + 
		"&province=" + $(".formMain input[name='province']").val() + 
		"&postal_code=" + $(".formMain input[name='postal_code']").val() + 
		"&country_code=" + $(".formMain select[name='country_code']").val() + 
		"&gender=" + $(".formMain select[name='gender']").val() + 
		"&date_of_birth=" + $(".formMain input[name='date_of_birth']").val() + 
		"&alt_phone=" + $(".formMain input[name='alt_phone']").val() + 
		"&email=" + $(".formMain input[name='email']").val() + 
		"&security_phrase=" + $(".formMain input[name='security_phrase']").val() + 
		"&comments=" + $(".formMain textarea[name='comments']").val() + 
		"&user=" + uName + 
		"&pass=" + uPass + 
		"&campaign=" + campaign + 
		"&phone_login=" + phone_login + 
		"&original_phone_login=" + original_phone_login +
		"&phone_pass=" + phone_pass + 
		"&fronter=" + fronter + 
		"&closer=" + user + 
		"&group=" + group + 
		"&channel_group=" + group + 
		"&SQLdate=" + SQLdate + 
		"&epoch=" + UnixTime + 
		"&uniqueid=" + $(".formMain input[name='uniqueid']").val() + 
		"&customer_zap_channel=" + lastcustchannel + 
		"&customer_server_ip=" + lastcustserverip +
		"&server_ip=" + server_ip + 
		"&SIPexten=" + extension + 
		"&session_id=" + session_id + 
		"&phone=" + $(".formMain input[name='phone_number']").val() + 
		"&parked_by=" + $(".formMain input[name='lead_id']").val() +
		"&dispo=" + LeadDispo + '' +
		"&dialed_number=" + dialed_number + '' +
		"&dialed_label=" + dialed_label + '' +
		"&source_id=" + source_id + '' +
		"&rank=" + $(".formMain input[name='rank']").val() + '' +
		"&owner=" + $(".formMain input[name='owner']").val() + '' +
		"&camp_script=" + campaign_script + '' +
		"&in_script=" + Call_Script_ID + '' +
		"&fullname=" + LOGfullname + '' +
		"&recording_filename=" + recording_filename + '' +
		"&recording_id=" + recording_id + '' +
		"&user_custom_one=" + user_custom_one + '' +
		"&user_custom_two=" + user_custom_two + '' +
		"&user_custom_three=" + user_custom_three + '' +
		"&user_custom_four=" + user_custom_four + '' +
		"&user_custom_five=" + user_custom_five + '' +
		"&preset_number_a=" + Call_XC_a_Number + '' +
		"&preset_number_b=" + Call_XC_b_Number + '' +
		"&preset_number_c=" + Call_XC_c_Number + '' +
		"&preset_number_d=" + Call_XC_d_Number + '' +
		"&preset_number_e=" + Call_XC_e_Number + '' +
		"&preset_dtmf_a=" + Call_XC_a_DTMF + '' +
		"&preset_dtmf_b=" + Call_XC_b_DTMF + '' +
		"&did_id=" + did_id + '' +
		"&did_extension=" + did_extension + '' +
		"&did_pattern=" + did_pattern + '' +
		"&did_description=" + did_description + '' +
		"&closecallid=" + closecallid + '' +
		"&xfercallid=" + xfercallid + '' +
		"&agent_log_id=" + agent_log_id + '' +
		"&entry_list_id=" + $(".formMain input[name='entry_list_id']").val() + '' +
		"&call_id=" + LastCID + '' +
		"&user_group=" + user_group + '' +
		"&web_vars=" + LIVE_web_vars + '' +
		webform_session;
		
		if (custom_field_names.length > 2) {
			var url_custom_field = '';
			var CFN_array = custom_field_names.split('|');
			var CFN_count = CFN_array.length;
			var CFN_tick = 0;
			while (CFN_tick < CFN_count) {
				var CFN_field = CFN_array[CFN_tick];
				if (CFN_field.length > 0) {
					url_custom_field = url_custom_field + "&" + CFN_field + "=--A--" + CFN_field + "--B--";
				}
				CFN_tick++;
			}
			if (url_custom_field.length > 10) {
				url_custom_field = '&CF_uses_custom_fields=Y' + url_custom_field;
			}
			web_form_varsX = web_form_varsX + '' + url_custom_field;
			scriptformat = 'YES';
		}

		web_form_varsX = web_form_varsX.replace(RGplus, '+');
		web_form_varsX = web_form_varsX.replace(RGnl, '+');
		web_form_varsX = web_form_varsX.replace(regWF, '');

		var regWFAvars = new RegExp("\\?","ig");
		if (encoded.match(regWFAvars))
			{web_form_varsX = '&' + web_form_varsX}
		else
			{web_form_varsX = '?' + web_form_varsX}

		var TEMPX_VDIC_web_form_address = encoded + "" + web_form_varsX;

		var regWFAqavars = new RegExp("\\?&","ig");
		var regWFAaavars = new RegExp("&&","ig");
		TEMPX_VDIC_web_form_address = TEMPX_VDIC_web_form_address.replace(regWFAqavars, '?');
		TEMPX_VDIC_web_form_address = TEMPX_VDIC_web_form_address.replace(regWFAaavars, '&');
		encoded = TEMPX_VDIC_web_form_address;
	}
	if (scriptformat == 'YES') {
		// custom fields populate if lead information is sent with custom field names
		if (custom_field_names.length > 2) {
			var CFN_array = custom_field_names.split('|');
			var CFV_array = custom_field_values.split('----------');
			var CFT_array = custom_field_types.split('|');
			var CFN_count = CFN_array.length;
			var CFN_tick = 0;
			var CFN_debug = '';
			var CF_loaded = $(".formMain input[name='FORM_LOADED']").val();
		//	alert(custom_field_names + "\n" + custom_field_values + "\n" + CFN_count + "\n" + CF_loaded);
			while (CFN_tick < CFN_count) {
				var CFN_field = CFN_array[CFN_tick];
				var RG_CFN_field = new RegExp("--A--" + CFN_field + "--B--","g");
				if ( (CFN_field.length > 0) && (encoded.match(RG_CFN_field)) ) {
					if (CF_loaded == '1') {
						var CFN_value = '';
						var field_parsed=0;
						if ( (CFT_array[CFN_tick]=='TIME') && (field_parsed < 1) ) {
							var CFN_field_hour = 'HOUR_' + CFN_field;
							var cIndex_hour = vcFormIFrame.document.form_custom_fields[CFN_field_hour].selectedIndex;
							var CFN_value_hour =  vcFormIFrame.document.form_custom_fields[CFN_field_hour].options[cIndex_hour].value;
							var CFN_field_minute = 'MINUTE_' + CFN_field;
							var cIndex_minute = vcFormIFrame.document.form_custom_fields[CFN_field_minute].selectedIndex;
							var CFN_value_minute =  vcFormIFrame.document.form_custom_fields[CFN_field_minute].options[cIndex_minute].value;
							var CFN_value = CFN_value_hour + ':' + CFN_value_minute + ':00'
							field_parsed=1;
						}
						if ( (CFT_array[CFN_tick]=='SELECT') && (field_parsed < 1) ) {
							var cIndex = vcFormIFrame.document.form_custom_fields[CFN_field].selectedIndex;
							var CFN_value =  vcFormIFrame.document.form_custom_fields[CFN_field].options[cIndex].value;
							field_parsed=1;
						}
						if ( (CFT_array[CFN_tick]=='MULTI') && (field_parsed < 1) ) {
							var chosen = '';
							var CFN_field = CFN_field + '[]';
							for (i=0; i < vcFormIFrame.document.form_custom_fields[CFN_field].options.length; i++) {
								if (vcFormIFrame.document.form_custom_fields[CFN_field].options[i].selected) {
									chosen = chosen + '' + vcFormIFrame.document.form_custom_fields[CFN_field].options[i].value + ',';
                                }
                            }
							var CFN_value = chosen;
							if (CFN_value.length > 0) {CFN_value = CFN_value.slice(0,-1);}
							field_parsed=1;
							}
						if ( ( (CFT_array[CFN_tick]=='RADIO') || (CFT_array[CFN_tick]=='CHECKBOX') ) && (field_parsed < 1) )
							{
							var chosen = '';
							var CFN_field = CFN_field + '[]';
							var len = vcFormIFrame.document.form_custom_fields[CFN_field].length;
							for (i = 0; i < len; i++) {
								if (vcFormIFrame.document.form_custom_fields[CFN_field][i].checked) {
									chosen = chosen + '' + vcFormIFrame.document.form_custom_fields[CFN_field][i].value + ',';
                                }
                            }
							var CFN_value = chosen;
							if (CFN_value.length > 0) {CFN_value = CFN_value.slice(0,-1);}
							field_parsed = 1;
						}
						if (field_parsed < 1) {
							var CFN_value = vcFormIFrame.document.form_custom_fields[CFN_field].value;
							field_parsed=1;
                        }
                    } else {
						var CFN_value = CFV_array[CFN_tick];
					}
					CFN_value = CFN_value.replace(RGnl,'+');
					CFN_value = CFN_value.replace(RGtab,'+');
					CFN_value = CFN_value.replace(RGplus,'+');
					encoded = encoded.replace(RG_CFN_field, CFN_value);
					web_form_varsX = web_form_varsX.replace(RG_CFN_field, CFN_value);
					CFN_debug = CFN_debug + '|' + CFN_field + '-' + CFN_value;
				}
				CFN_tick++;
			}
//			document.getElementById("debugbottomspan").innerHTML = CFN_debug;
		}

		if (webformnumber == '1')
			{web_form_vars = web_form_varsX;}
		if (webformnumber == '2')
			{web_form_vars_two = web_form_varsX;}
        var getFirstName = $("#cust_full_name a[id='first_name']").editable('getValue', true);
            getFirstName = (getFirstName !== ' ') ? getFirstName : '';
        var getMiddleName = $("#cust_full_name a[id='middle_initial']").editable('getValue', true);
            getMiddleName = (getMiddleName !== ' ') ? getMiddleName : '';
        var getLastName = $("#cust_full_name a[id='last_name']").editable('getValue', true);
            getLastName = (getLastName !== ' ') ? getLastName : '';

		var SCvendor_lead_code = $(".formMain input[name='vendor_lead_code']").val();
		var SCsource_id = source_id;
		var SClist_id = $(".formMain input[name='list_id']").val();
		var SCgmt_offset_now = $(".formMain input[name='gmt_offset_now']").val();
		var SCcalled_since_last_reset = "";
		var SCphone_code = $(".formMain input[name='phone_code']").val();
		var SCphone_number = $(".formMain input[name='phone_number']").val();
		var SCtitle = $(".formMain input[name='title']").val();
		var SCfirst_name = getFirstName;
		var SCmiddle_initial = getMiddleName;
		var SClast_name = getLastName;
		var SCaddress1 = $(".formMain input[name='address1']").val();
		var SCaddress2 = $(".formMain input[name='address2']").val();
		var SCaddress3 = $(".formMain input[name='address3']").val();
		var SCcity = $(".formMain input[name='city']").val();
		var SCstate = $(".formMain input[name='state']").val();
		var SCprovince = $(".formMain input[name='province']").val();
		var SCpostal_code = $(".formMain input[name='postal_code']").val();
		var SCcountry_code = $(".formMain select[name='country_code']").val();
		var SCgender = $(".formMain select[name='gender']").val();
		var SCdate_of_birth = $(".formMain input[name='date_of_birth']").val();
		var SCalt_phone = $(".formMain input[name='alt_phone']").val();
		var SCemail = $(".formMain input[name='email']").val();
		var SCsecurity_phrase = $(".formMain input[name='security_phrase']").val();
		var SCcomments = $(".formMain textarea[name='comments']").val();
		var SCfullname = LOGfullname;
		var SCfronter = fronter;
		var SCuser = uName;
		var SCpass = uPass;
		var SClead_id = $(".formMain input[name='lead_id']").val();
		var SCcampaign = campaign;
		var SCphone_login = phone_login;
		var SCoriginal_phone_login = original_phone_login;
		var SCgroup = group;
		var SCchannel_group = group;
		var SCSQLdate = SQLdate;
		var SCepoch = UnixTime;
		var SCuniqueid = $(".formMain input[name='uniqueid']").val();
		var SCcustomer_zap_channel = lastcustchannel;
		var SCserver_ip = server_ip;
		var SCSIPexten = extension;
		var SCsession_id = session_id;
		var SCdispo = LeadDispo;
		var SCdialed_number = dialed_number;
		var SCdialed_label = dialed_label;
		var SCrank = $(".formMain input[name='rank']").val();
		var SCowner = $(".formMain input[name='owner']").val();
		var SCcamp_script = campaign_script;
		var SCin_script = Call_Script_ID;
		var SCrecording_filename = recording_filename;
		var SCrecording_id = recording_id;
		var SCuser_custom_one = user_custom_one;
		var SCuser_custom_two = user_custom_two;
		var SCuser_custom_three = user_custom_three;
		var SCuser_custom_four = user_custom_four;
		var SCuser_custom_five = user_custom_five;
		var SCpreset_number_a = Call_XC_a_Number;
		var SCpreset_number_b = Call_XC_b_Number;
		var SCpreset_number_c = Call_XC_c_Number;
		var SCpreset_number_d = Call_XC_d_Number;
		var SCpreset_number_e = Call_XC_e_Number;
		var SCpreset_dtmf_a = Call_XC_a_DTMF;
		var SCpreset_dtmf_b = Call_XC_b_DTMF;
		var SCdid_id = did_id;
		var SCdid_extension = did_extension;
		var SCdid_pattern = did_pattern;
		var SCdid_description = did_description;
		var SCclosecallid = closecallid;
		var SCxfercallid = xfercallid;
		var SCcall_id = LastCID;
		var SCuser_group = user_group;
		var SCagent_log_id = agent_log_id;
		var SCweb_vars = LIVE_web_vars;

		if (encoded.match(RGiframe)) {
			SCvendor_lead_code = SCvendor_lead_code.replace(RGplus,'+');
			SCsource_id = SCsource_id.replace(RGplus,'+');
			SClist_id = SClist_id.replace(RGplus,'+');
			SCgmt_offset_now = SCgmt_offset_now.replace(RGplus,'+');
			SCcalled_since_last_reset = SCcalled_since_last_reset.replace(RGplus,'+');
			SCphone_code = SCphone_code.replace(RGplus,'+');
			SCphone_number = SCphone_number.replace(RGplus,'+');
			SCtitle = SCtitle.replace(RGplus,'+');
			SCfirst_name = SCfirst_name.replace(RGplus,'+');
			SCmiddle_initial = SCmiddle_initial.replace(RGplus,'+');
			SClast_name = SClast_name.replace(RGplus,'+');
			SCaddress1 = SCaddress1.replace(RGplus,'+');
			SCaddress2 = SCaddress2.replace(RGplus,'+');
			SCaddress3 = SCaddress3.replace(RGplus,'+');
			SCcity = SCcity.replace(RGplus,'+');
			SCstate = SCstate.replace(RGplus,'+');
			SCprovince = SCprovince.replace(RGplus,'+');
			SCpostal_code = SCpostal_code.replace(RGplus,'+');
			SCcountry_code = SCcountry_code.replace(RGplus,'+');
			SCgender = SCgender.replace(RGplus,'+');
			SCdate_of_birth = SCdate_of_birth.replace(RGplus,'+');
			SCalt_phone = SCalt_phone.replace(RGplus,'+');
			SCemail = SCemail.replace(RGplus,'+');
			SCsecurity_phrase = SCsecurity_phrase.replace(RGplus,'+');
			SCcomments = SCcomments.replace(RGplus,'+');
			SCfullname = SCfullname.replace(RGplus,'+');
			SCfronter = SCfronter.replace(RGplus,'+');
			SCuser = SCuser.replace(RGplus,'+');
			SCpass = SCpass.replace(RGplus,'+');
			SClead_id = SClead_id.replace(RGplus,'+');
			SCcampaign = SCcampaign.replace(RGplus,'+');
			SCphone_login = SCphone_login.replace(RGplus,'+');
			SCoriginal_phone_login = SCoriginal_phone_login.replace(RGplus,'+');
			SCgroup = SCgroup.replace(RGplus,'+');
			SCchannel_group = SCchannel_group.replace(RGplus,'+');
			SCSQLdate = SCSQLdate.replace(RGplus,'+');
			SCuniqueid = SCuniqueid.replace(RGplus,'+');
			SCcustomer_zap_channel = SCcustomer_zap_channel.replace(RGplus,'+');
			SCserver_ip = SCserver_ip.replace(RGplus,'+');
			SCSIPexten = SCSIPexten.replace(RGplus,'+');
			SCdispo = SCdispo.replace(RGplus,'+');
			SCdialed_number = SCdialed_number.replace(RGplus,'+');
			SCdialed_label = SCdialed_label.replace(RGplus,'+');
			SCrank = SCrank.replace(RGplus,'+');
			SCowner = SCowner.replace(RGplus,'+');
			SCcamp_script = SCcamp_script.replace(RGplus,'+');
			SCin_script = SCin_script.replace(RGplus,'+');
			SCrecording_filename = SCrecording_filename.replace(RGplus,'+');
			SCrecording_id = SCrecording_id.replace(RGplus,'+');
			SCuser_custom_one = SCuser_custom_one.replace(RGplus,'+');
			SCuser_custom_two = SCuser_custom_two.replace(RGplus,'+');
			SCuser_custom_three = SCuser_custom_three.replace(RGplus,'+');
			SCuser_custom_four = SCuser_custom_four.replace(RGplus,'+');
			SCuser_custom_five = SCuser_custom_five.replace(RGplus,'+');
			SCpreset_number_a = SCpreset_number_a.replace(RGplus,'+');
			SCpreset_number_b = SCpreset_number_b.replace(RGplus,'+');
			SCpreset_number_c = SCpreset_number_c.replace(RGplus,'+');
			SCpreset_number_d = SCpreset_number_d.replace(RGplus,'+');
			SCpreset_number_e = SCpreset_number_e.replace(RGplus,'+');
			SCpreset_dtmf_a = SCpreset_dtmf_a.replace(RGplus,'+');
			SCpreset_dtmf_b = SCpreset_dtmf_b.replace(RGplus,'+');
			SCdid_id = SCdid_id.replace(RGplus,'+');
			SCdid_extension = SCdid_extension.replace(RGplus,'+');
			SCdid_pattern = SCdid_pattern.replace(RGplus,'+');
			SCdid_description = SCdid_description.replace(RGplus,'+');
			SCcall_id = SCcall_id.replace(RGplus,'+');
			SCuser_group = SCuser_group.replace(RGplus,'+');
			SCweb_vars = SCweb_vars.replace(RGplus,'+');
		}

		var RGvendor_lead_code = new RegExp("--A--vendor_lead_code--B--","g");
		var RGsource_id = new RegExp("--A--source_id--B--","g");
		var RGlist_id = new RegExp("--A--list_id--B--","g");
		var RGgmt_offset_now = new RegExp("--A--gmt_offset_now--B--","g");
		var RGcalled_since_last_reset = new RegExp("--A--called_since_last_reset--B--","g");
		var RGphone_code = new RegExp("--A--phone_code--B--","g");
		var RGphone_number = new RegExp("--A--phone_number--B--","g");
		var RGtitle = new RegExp("--A--title--B--","g");
		var RGfirst_name = new RegExp("--A--first_name--B--","g");
		var RGmiddle_initial = new RegExp("--A--middle_initial--B--","g");
		var RGlast_name = new RegExp("--A--last_name--B--","g");
		var RGaddress1 = new RegExp("--A--address1--B--","g");
		var RGaddress2 = new RegExp("--A--address2--B--","g");
		var RGaddress3 = new RegExp("--A--address3--B--","g");
		var RGcity = new RegExp("--A--city--B--","g");
		var RGstate = new RegExp("--A--state--B--","g");
		var RGprovince = new RegExp("--A--province--B--","g");
		var RGpostal_code = new RegExp("--A--postal_code--B--","g");
		var RGcountry_code = new RegExp("--A--country_code--B--","g");
		var RGgender = new RegExp("--A--gender--B--","g");
		var RGdate_of_birth = new RegExp("--A--date_of_birth--B--","g");
		var RGalt_phone = new RegExp("--A--alt_phone--B--","g");
		var RGemail = new RegExp("--A--email--B--","g");
		var RGsecurity_phrase = new RegExp("--A--security_phrase--B--","g");
		var RGcomments = new RegExp("--A--comments--B--","g");
		var RGfullname = new RegExp("--A--fullname--B--","g");
		var RGfronter = new RegExp("--A--fronter--B--","g");
		var RGuser = new RegExp("--A--user--B--","g");
		var RGpass = new RegExp("--A--pass--B--","g");
		var RGlead_id = new RegExp("--A--lead_id--B--","g");
		var RGcampaign = new RegExp("--A--campaign--B--","g");
		var RGphone_login = new RegExp("--A--phone_login--B--","g");
		var RGoriginal_phone_login = new RegExp("--A--original_phone_login--B--","g");
		var RGgroup = new RegExp("--A--group--B--","g");
		var RGchannel_group = new RegExp("--A--channel_group--B--","g");
		var RGSQLdate = new RegExp("--A--SQLdate--B--","g");
		var RGepoch = new RegExp("--A--epoch--B--","g");
		var RGuniqueid = new RegExp("--A--uniqueid--B--","g");
		var RGcustomer_zap_channel = new RegExp("--A--customer_zap_channel--B--","g");
		var RGserver_ip = new RegExp("--A--server_ip--B--","g");
		var RGSIPexten = new RegExp("--A--SIPexten--B--","g");
		var RGsession_id = new RegExp("--A--session_id--B--","g");
		var RGdispo = new RegExp("--A--dispo--B--","g");
		var RGdialed_number = new RegExp("--A--dialed_number--B--","g");
		var RGdialed_label = new RegExp("--A--dialed_label--B--","g");
		var RGrank = new RegExp("--A--rank--B--","g");
		var RGowner = new RegExp("--A--owner--B--","g");
		var RGcamp_script = new RegExp("--A--camp_script--B--","g");
		var RGin_script = new RegExp("--A--in_script--B--","g");
		var RGrecording_filename = new RegExp("--A--recording_filename--B--","g");
		var RGrecording_id = new RegExp("--A--recording_id--B--","g");
		var RGuser_custom_one = new RegExp("--A--user_custom_one--B--","g");
		var RGuser_custom_two = new RegExp("--A--user_custom_two--B--","g");
		var RGuser_custom_three = new RegExp("--A--user_custom_three--B--","g");
		var RGuser_custom_four = new RegExp("--A--user_custom_four--B--","g");
		var RGuser_custom_five = new RegExp("--A--user_custom_five--B--","g");
		var RGpreset_number_a = new RegExp("--A--preset_number_a--B--","g");
		var RGpreset_number_b = new RegExp("--A--preset_number_b--B--","g");
		var RGpreset_number_c = new RegExp("--A--preset_number_c--B--","g");
		var RGpreset_number_d = new RegExp("--A--preset_number_d--B--","g");
		var RGpreset_number_e = new RegExp("--A--preset_number_e--B--","g");
		var RGpreset_dtmf_a = new RegExp("--A--preset_dtmf_a--B--","g");
		var RGpreset_dtmf_b = new RegExp("--A--preset_dtmf_b--B--","g");
		var RGdid_id = new RegExp("--A--did_id--B--","g");
		var RGdid_extension = new RegExp("--A--did_extension--B--","g");
		var RGdid_pattern = new RegExp("--A--did_pattern--B--","g");
		var RGdid_description = new RegExp("--A--did_description--B--","g");
		var RGclosecallid = new RegExp("--A--closecallid--B--","g");
		var RGxfercallid = new RegExp("--A--xfercallid--B--","g");
		var RGagent_log_id = new RegExp("--A--agent_log_id--B--","g");
		var RGcall_id = new RegExp("--A--call_id--B--","g");
		var RGuser_group = new RegExp("--A--user_group--B--","g");
		var RGweb_vars = new RegExp("--A--web_vars--B--","g");

		encoded = encoded.replace(RGvendor_lead_code, SCvendor_lead_code);
		encoded = encoded.replace(RGsource_id, SCsource_id);
		encoded = encoded.replace(RGlist_id, SClist_id);
		encoded = encoded.replace(RGgmt_offset_now, SCgmt_offset_now);
		encoded = encoded.replace(RGcalled_since_last_reset, SCcalled_since_last_reset);
		encoded = encoded.replace(RGphone_code, SCphone_code);
		encoded = encoded.replace(RGphone_number, SCphone_number);
		encoded = encoded.replace(RGtitle, SCtitle);
		encoded = encoded.replace(RGfirst_name, SCfirst_name);
		encoded = encoded.replace(RGmiddle_initial, SCmiddle_initial);
		encoded = encoded.replace(RGlast_name, SClast_name);
		encoded = encoded.replace(RGaddress1, SCaddress1);
		encoded = encoded.replace(RGaddress2, SCaddress2);
		encoded = encoded.replace(RGaddress3, SCaddress3);
		encoded = encoded.replace(RGcity, SCcity);
		encoded = encoded.replace(RGstate, SCstate);
		encoded = encoded.replace(RGprovince, SCprovince);
		encoded = encoded.replace(RGpostal_code, SCpostal_code);
		encoded = encoded.replace(RGcountry_code, SCcountry_code);
		encoded = encoded.replace(RGgender, SCgender);
		encoded = encoded.replace(RGdate_of_birth, SCdate_of_birth);
		encoded = encoded.replace(RGalt_phone, SCalt_phone);
		encoded = encoded.replace(RGemail, SCemail);
		encoded = encoded.replace(RGsecurity_phrase, SCsecurity_phrase);
		encoded = encoded.replace(RGcomments, SCcomments);
		encoded = encoded.replace(RGfullname, SCfullname);
		encoded = encoded.replace(RGfronter, SCfronter);
		encoded = encoded.replace(RGuser, SCuser);
		encoded = encoded.replace(RGpass, SCpass);
		encoded = encoded.replace(RGlead_id, SClead_id);
		encoded = encoded.replace(RGcampaign, SCcampaign);
		encoded = encoded.replace(RGphone_login, SCphone_login);
		encoded = encoded.replace(RGoriginal_phone_login, SCoriginal_phone_login);
		encoded = encoded.replace(RGgroup, SCgroup);
		encoded = encoded.replace(RGchannel_group, SCchannel_group);
		encoded = encoded.replace(RGSQLdate, SCSQLdate);
		encoded = encoded.replace(RGepoch, SCepoch);
		encoded = encoded.replace(RGuniqueid, SCuniqueid);
		encoded = encoded.replace(RGcustomer_zap_channel, SCcustomer_zap_channel);
		encoded = encoded.replace(RGserver_ip, SCserver_ip);
		encoded = encoded.replace(RGSIPexten, SCSIPexten);
		encoded = encoded.replace(RGsession_id, SCsession_id);
		encoded = encoded.replace(RGdispo, SCdispo);
		encoded = encoded.replace(RGdialed_number, SCdialed_number);
		encoded = encoded.replace(RGdialed_label, SCdialed_label);
		encoded = encoded.replace(RGrank, SCrank);
		encoded = encoded.replace(RGowner, SCowner);
		encoded = encoded.replace(RGcamp_script, SCcamp_script);
		encoded = encoded.replace(RGin_script, SCin_script);
		encoded = encoded.replace(RGrecording_filename, SCrecording_filename);
		encoded = encoded.replace(RGrecording_id, SCrecording_id);
		encoded = encoded.replace(RGuser_custom_one, SCuser_custom_one);
		encoded = encoded.replace(RGuser_custom_two, SCuser_custom_two);
		encoded = encoded.replace(RGuser_custom_three, SCuser_custom_three);
		encoded = encoded.replace(RGuser_custom_four, SCuser_custom_four);
		encoded = encoded.replace(RGuser_custom_five, SCuser_custom_five);
		encoded = encoded.replace(RGpreset_number_a, SCpreset_number_a);
		encoded = encoded.replace(RGpreset_number_b, SCpreset_number_b);
		encoded = encoded.replace(RGpreset_number_c, SCpreset_number_c);
		encoded = encoded.replace(RGpreset_number_d, SCpreset_number_d);
		encoded = encoded.replace(RGpreset_number_e, SCpreset_number_e);
		encoded = encoded.replace(RGpreset_dtmf_a, SCpreset_dtmf_a);
		encoded = encoded.replace(RGpreset_dtmf_b, SCpreset_dtmf_b);
		encoded = encoded.replace(RGdid_id, SCdid_id);
		encoded = encoded.replace(RGdid_extension, SCdid_extension);
		encoded = encoded.replace(RGdid_pattern, SCdid_pattern);
		encoded = encoded.replace(RGdid_description, SCdid_description);
		encoded = encoded.replace(RGclosecallid, SCclosecallid);
		encoded = encoded.replace(RGxfercallid, SCxfercallid);
		encoded = encoded.replace(RGagent_log_id, SCagent_log_id);
		encoded = encoded.replace(RGcall_id, SCcall_id);
		encoded = encoded.replace(RGuser_group, SCuser_group);
		encoded = encoded.replace(RGweb_vars, SCweb_vars);
	}
	decoded = encoded; // simple no ?
	decoded = decoded.replace(RGnl, '+');
	decoded = decoded.replace(RGplus,'+');
	decoded = decoded.replace(RGtab,'+');

	//	   while (i < encoded.length) {
	//		   var ch = encoded.charAt(i);
	//		   if (ch == "%") {
	//				if (i < (encoded.length-2) 
	//						&& HEXCHAR.indexOf(encoded.charAt(i+1)) != -1 
	//						&& HEXCHAR.indexOf(encoded.charAt(i+2)) != -1 ) {
	//					decoded += unescape( encoded.substr(i,3) );
	//					i += 3;
	//				} else {
	//					alert( 'Bad escape combo near ...' + encoded.substr(i) );
	//					decoded += "%[ERR]";
	//					i++;
	//				}
	//			} else {
	//			   decoded += ch;
	//			   i++;
	//			}
	//		} // while
    //      decoded = decoded.replace(RGnl, "<br />");
	//
	return decoded;
}

function utf8_decode(utftext) {
    var string = "";
    var i = 0;
    var c = c1 = c2 = 0;

    while ( i < utftext.length ) {

        c = utftext.charCodeAt(i);

        if (c < 128) {
            string += String.fromCharCode(c);
            i++;
        }
        else if((c > 191) && (c < 224)) {
            c2 = utftext.charCodeAt(i+1);
            string += String.fromCharCode(((c & 31) << 6) | (c2 & 63));
            i += 2;
        }
        else {
            c2 = utftext.charCodeAt(i+1);
            c3 = utftext.charCodeAt(i+2);
            string += String.fromCharCode(((c & 15) << 12) | ((c2 & 63) << 6) | (c3 & 63));
            i += 3;
        }

    }

    return string;
}


// ################################################################################
// phone number format
function phone_number_format(formatphone) {
    // customer_local_time, status date display 9999999999
    //	header_phone_format
    //  US_DASH 000-000-0000 - USA dash separated phone number
    //  US_PARN (000)000-0000 - USA dash separated number with area code in parenthesis
    //  UK_DASH 00 0000-0000 - UK dash separated phone number with space after city code
    //  AU_SPAC 000 000 000 - Australia space separated phone number
    //  IT_DASH 0000-000-000 - Italy dash separated phone number
    //  FR_SPAC 00 00 00 00 00 - France space separated phone number

    var regUS_DASHphone = new RegExp("US_DASH","g");
    var regUS_PARNphone = new RegExp("US_PARN","g");
    var regUK_DASHphone = new RegExp("UK_DASH","g");
    var regAU_SPACphone = new RegExp("AU_SPAC","g");
    var regIT_DASHphone = new RegExp("IT_DASH","g");
    var regFR_SPACphone = new RegExp("FR_SPAC","g");
    var status_display_number = formatphone;
    var dispnumber = formatphone;
    if (disable_alter_custphone == 'HIDE') {
        var status_display_number = 'XXXXXXXXXX';
        dispnumber = 'XXXXXXXXXX';
    }
    if (header_phone_format.match(regUS_DASHphone)) {
        var status_display_number = dispnumber.substring(0,3) + '-' + dispnumber.substring(3,6) + '-' + dispnumber.substring(6,10);
    }
    if (header_phone_format.match(regUS_PARNphone)) {
        var status_display_number = '(' + dispnumber.substring(0,3) + ')' + dispnumber.substring(3,6) + '-' + dispnumber.substring(6,10);
    }
    if (header_phone_format.match(regUK_DASHphone)) {
        var status_display_number = dispnumber.substring(0,2) + ' ' + dispnumber.substring(2,6) + '-' + dispnumber.substring(6,10);
    }
    if (header_phone_format.match(regAU_SPACphone)) {
        var status_display_number = dispnumber.substring(0,3) + ' ' + dispnumber.substring(3,6) + ' ' + dispnumber.substring(6,9);
    }
    if (header_phone_format.match(regIT_DASHphone)) {
        var status_display_number = dispnumber.substring(0,4) + '-' + dispnumber.substring(4,7) + '-' + dispnumber.substring(8,10);
    }
    if (header_phone_format.match(regFR_SPACphone)) {
        var status_display_number = dispnumber.substring(0,2) + ' ' + dispnumber.substring(2,4) + ' ' + dispnumber.substring(4,6) + ' ' + dispnumber.substring(6,8) + ' ' + dispnumber.substring(8,10);
    }

    return status_display_number;
};

function minimizeModal(modal_id) {
    minimizedDispo = true;
    $.AdminLTE.options.controlSidebarOptions.minimizedDispo = true;
    $("#"+modal_id).css('overflow', 'hidden');
    $("#"+modal_id+" div.modal-dialog").animate({ 'margin-top': '5px' }, 500);
    $("#"+modal_id).animate({ 'top': '94%' }, 500, function() {
        $("body").css('overflow-y', 'auto');
        $(".max-modal").removeClass('hidden');
        $(".min-modal").addClass('hidden');
    });
    
    toggleButton('DialHangup', 'off');
    if (dial_method.toLowerCase() !== 'manual')
        toggleButton('ResumePause', 'off');
    $('#MDPhonENumbeR').prop('readonly', true);
    
    $(document).off('focusin.modal');
    
    $('.input-disabled').prop('disabled', false);
    if (disable_alter_custphone == 'N') {
        $('.input-phone-disabled').prop('disabled', false);
    }
    $('#cust_full_name .editable').editable('enable');
    $("input:required, select:required").addClass("required_div");
    
    var txtBox=document.getElementById("first_name" );
    txtBox.focus();
}

function maximizeModal(modal_id) {
    $("#"+modal_id+" div.modal-dialog").animate({ 'margin-top': '30px' }, 500);
    $("#"+modal_id).animate({ 'top': '0' }, 500, function() {
        $("#"+modal_id).css('overflow', 'auto');
        $("body").css('overflow', 'hidden');
        $(".max-modal").addClass('hidden');
        $(".min-modal").removeClass('hidden');
    });

    toggleButton('DialHangup', 'dial');
    if (dial_method.toLowerCase() !== 'manual') {
        var btnIsPaused = (VDRP_stage == 'PAUSED') ? 'resume' : 'pause';
        toggleButton('ResumePause', btnIsPaused);
    }
    $('#MDPhonENumbeR').prop('readonly', false);
}

String.prototype.toUpperFirst = function() {
    return this.charAt(0).toUpperCase() + this.slice(1);
}

String.prototype.toUpperFirstLetters = function() {
    var str = this.toLowerCase().replace(/^[\u00C0-\u1FFF\u2C00-\uD7FF\w]|\s[\u00C0-\u1FFF\u2C00-\uD7FF\w]/g, function(letter) {
        return letter.toUpperCase();
    });
    
    return str;
}

Number.prototype.between = function (a, b, inclusive) {
    var min = Math.min(a, b),
        max = Math.max(a, b);

    return inclusive ? this >= min && this <= max : this > min && this < max;
}

<?php
} else {
    if ($_REQUEST['module_name'] == 'GOagent') {
        switch ($_REQUEST['action']) {
            case "SessioN":
                $campaign = $_REQUEST['campaign_id'];
                $is_logged_in = $_REQUEST['is_logged_in'];
                $_SESSION['campaign_id'] = (strlen($campaign) > 0) ? $campaign : $_SESSION['campaign_id'];
                $_SESSION['is_logged_in'] = (strlen($is_logged_in) > 0) ? $is_logged_in : $_SESSION['is_logged_in'];
                $result = $_SESSION['is_logged_in'];
                break;
            case "ChecKLogiN":
                $is_logged_in = $_REQUEST['is_logged_in'];
                $sess_logged_in = (strlen($_SESSION['is_logged_in']) > 0) ? $_SESSION['is_logged_in'] : 0;
                $_SESSION['is_logged_in'] = (strlen($is_logged_in) > 0) ? $is_logged_in : $sess_logged_in;
                $result = $_SESSION['is_logged_in'];
                break;
            case "CustoMFielD":
                $list_id = $_REQUEST['list_id'];
                $result = $ui->API_goGetAllCustomFields($list_id);
                $result = json_encode($result);
                break;
            case "UpdateMessages":
                $user = \creamy\CreamyUser::currentUser();
                $folder = $_REQUEST['folder'];
                $user_id = $_REQUEST['user_id'];
                $updates = array(
                    'result' => 'success',
                    'folders' => $ui->getMessageFoldersAsList($folder),
                    'controls' => $ui->getMailboxButtons($folder, true, false),
                    'messages' => $ui->getMessagesFromFolderAsTable($user_id, $folder),
                    'topbar' => $ui->getTopbarMessagesMenu($user)
                );
                $result = json_encode($updates, JSON_UNESCAPED_SLASHES);
                break;
            case "ReadMessage":
                $db = new \creamy\DbHandler();
                $user = \creamy\CreamyUser::currentUser();
                $folder = $_REQUEST['folder'];
                $user_id = $_REQUEST['user_id'];
                $messageid = $_REQUEST['messageid'];
                
                // retrieve data about the message and sending user.
                $message = $db->getSpecificMessage($user_id, $messageid, $folder);
                $message["date"] = $ui->relativeTime($message["date"]);
                
                $from = $db->getDataForUser($message["user_from"]);
                $fromUser["id"] = $from["user_id"];
                $fromUser["user"] = (isset($from["user"]) ? $from["user"] : $lh->translationFor("unknown"));
                $fromUser["name"] = $from["full_name"];
                // mark the message as read
                $db->markMessagesAsRead($user_id, array($messageid), $folder);
                $readmail = array(
                    'result' => 'success',
                    'message' => $message,
                    'from' => $fromUser,
                    'attachments' => $ui->attachmentsSectionForMessage($messageid, $folder, true),
                    'test' => "{$module_dir}/../../uploads/2016/10/favorite-1png"
                );
                $result = json_encode($readmail, JSON_UNESCAPED_SLASHES);
                break;
        }
        print($result);
    } else {
        echo "ERROR: Module '{$_REQUEST['module_name']}' not found.";
    }
}

/*function get_user_info($user) {
    //set variables
    //$camp = (isset($_SESSION['campaign_id']) && strlen($_SESSION['campaign_id']) > 2) ? $_SESSION['campaign_id'] : '';
    
    //$output = $api->API_getLoginInfo($user); 
    
    /*$url = gourl.'/goAgent/goAPI.php';
    $fields = array(
        'goAction' => 'goGetLoginInfo',
        'goUser' => goUser,
        'goPass' => goPass,
        'responsetype' => 'json',
        'goUserID' => $user,
        'goCampaign' => $camp,
        'bcrypt' => 0
    );
    
    //url-ify the data for the POST
    $fields_string = "";
    foreach($fields as $key=>$value) { $fields_string .= $key.'='.$value.'&'; }
    rtrim($fields_string, '&');

    //open connection
    $ch = curl_init();
    
    //set the url, number of POST vars, POST data
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, count($fields));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    
    //execute post
    $data = curl_exec($ch);
    $result = json_decode($data);
    
    //close connection
    curl_close($ch);
    
    return $result->data;
    return $output;
}*/
?>
