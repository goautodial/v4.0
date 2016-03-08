<?php
namespace creamy;

$baseURL = (!empty($_SERVER['HTTPS'])) ? "https://".$_SERVER['SERVER_NAME'] : "http://".$_SERVER['SERVER_NAME'];
define('GO_BASE_DIRECTORY', dirname(dirname(dirname(__FILE__))));
define('GO_LANG_DIRECTORY', dirname(__FILE__) . '/lang/');
require_once(GO_BASE_DIRECTORY.'/php/CRMDefaults.php');
require_once(GO_BASE_DIRECTORY.'/php/LanguageHandler.php');
require_once(GO_BASE_DIRECTORY.'/php/DatabaseConnectorFactory.php');
include(GO_BASE_DIRECTORY.'/php/Session.php');

$lh = \creamy\LanguageHandler::getInstance();
$lh->addCustomTranslationsFromFile(GO_LANG_DIRECTORY . $lh->getLanguageHandlerLocale());

$cDB = \creamy\DatabaseConnectorFactory::getInstance()->getDatabaseConnectorOfType(CRM_DB_CONNECTOR_TYPE_MYSQL);

//get db host
$cDB->where('setting', 'GO_agent_db');
$cHost = $cDB->getOne(CRM_SETTINGS_TABLE_NAME);
//get db user
$cDB->where('setting', 'GO_agent_user');
$cUser = $cDB->getOne(CRM_SETTINGS_TABLE_NAME);
//get db pass
$cDB->where('setting', 'GO_agent_pass');
$cPass = $cDB->getOne(CRM_SETTINGS_TABLE_NAME);

$astHost = (isset($cHost["value"])) ? $cHost["value"] : DB_HOST;
$astUser = (isset($cUser["value"])) ? $cUser["value"] : DB_USERNAME;
$astPass = (isset($cPass["value"])) ? $cPass["value"] : DB_PASSWORD;
    
$US='_';
$NOW_TIME = date("Y-m-d H:i:s");
$tsNOW_TIME = date("YmdHis");
$StarTtimE = date("U");

//Connect to Asterisk DB
$astDB = \creamy\DatabaseConnectorFactory::getInstance()->getDatabaseConnectorOfType(CRM_DB_CONNECTOR_TYPE_MYSQL, $astHost, 'asterisk', $astUser, $astPass);

if ($astDB == null) {
    throw new \Exception("Unable to connect to the database 'asterisk' on the specified host. Access denied, incorrect parameters or table does not exist.");
    echo "// ERROR: Unable to connect to the database 'asterisk' on the specified host. Access denied, incorrect parameters or table does not exist.\n";
    return false;
}

if (!isset($_REQUEST['action']) && !isset($_REQUEST['module_name'])) {
    header('Content-Type: text/javascript');
?>
var baseURL = '<?=$baseURL?>';
var NOW_TIME = '<?=$NOW_TIME?>';
var SQLdate = '<?=$NOW_TIME?>';
var StarTtimE = '<?=$StarTtimE?>';
var UnixTime = '<?=$StarTtimE?>';
var UnixTimeMS = 0;
var t = new Date();
var c = new Date();
    LCAe = new Array('','','','','','');
    LCAc = new Array('','','','','','');
    LCAt = new Array('','','','','','');
    LMAe = new Array('','','','','','');
var session_id = '';
var session_name = '';
var conf_exten = '';
var vtiger_callback_id = '';
var qm_extension = '';
var nocall_dial_flag = '';
var INgroupCOUNT = 0;
var CallCID = '';
var uniqueid = '';
var xfername = '';
var xferchannel = '';
var callchannel = '';
var callserverip = '';
var lastcustchannel = '';
var lastcustserverip = '';
var custchannellive = 0;
var customer_server_ip = '';
var lead_id = 0;
var list_id = 0;
var live_customer_call = 0;
var live_call_seconds = 0;
var CheckDEADcall = 0;
var CheckDEADcallON = 0;
var xfer_in_call = 0;
var dialingINprogress = 0;
var refresh_interval = 1000;
var check_r = 0;
var WaitingForNextStep = 0;
var AgentDispoing = 0;
var inOUT = 'OUT';
var all_record = 'NO';
var all_record_count = 0;
var recording_filename = '';
var recording_id = '';
var VDRP_stage = 'PAUSED';
var VDCL_group_id = '';
var agent_log_id = 0;
var active_group_alias = '';
var active_ingroup_dial = '';
var agent_dialed_type = '';
var agent_dialed_number = '';
var cid_choice = '';
var prefix_choice = '';
var reselect_preview_dial = 0;
var waiting_on_dispo = 0;
var AutoDialReady = 0;
var AutoDialWaiting = 0;
var pause_code_counter = 0;
var lead_dial_number = '';
var MDchannel = '';
var MDuniqueid = '';
var XDuniqueid = '';
var tmp_vicidial_id = '';
var EAphone_code = '';
var EAphone_number = '';
var EAalt_phone_notes = '';
var EAalt_phone_active = '';
var EAalt_phone_count = '';
var XDnextCID = '';
var XDcheck = '';
var uniqueid_status_display = '';
var uniqueid_status_prefix = '';
var custom_call_id = '';
var API_selected_xfergroup = '';
var API_selected_callmenu = '';
var MD_channel_look = 0;
var MD_ring_seconds = 0;
var MDnextCID = '';
var LastCID = '';
var LeadPrevDispo = '';
var AgainHangupChannel = '';
var AgainHangupServer = '';
var AgainCallSeconds = '';
var AgainCallCID = '';
var cust_phone_code = '';
var cust_phone_number = '';
var cust_first_name = '';
var cust_middle_initial = '';
var cust_last_name = '';
var cust_email = '';
var called_count = '';
var previous_called_count = '';
var previous_dispo = '';
var CBentry_time = '';
var CBcallback_time = '';
var CBuser = '';
var CBcomments = '';
var dialed_number = '';
var dialed_label = '';
var source_id = '';
var call_script_ID = '';
var vendor_lead_code = '';
var script_recording_delay = '';
var Call_XC_a_Number = '';
var Call_XC_b_Number = '';
var Call_XC_c_Number = '';
var Call_XC_d_Number = '';
var Call_XC_e_Number = '';
var entry_list_id = '';
var custom_field_names = '';
var custom_field_values = '';
var custom_field_types = '';
var post_phone_time_diff_alert_message = '';
var timer_action = '';
var timer_action_message = '';
var timer_action_seconds = '';
var timer_action_destination = '';
var RedirectXFER = 0;
var conf_dialed = 0;
var open_dispo_screen = 0;
var DialALTPhone = false;
var leaving_threeway = 0;
var epoch_sec = 0;
var agentcallsstatus = 0;
var callholdstatus = 1;
var campagentstatct = 0;
var campagentstatctmax = 3;
var APIManualDialQueue = 0;
var APIManualDialQueue_last = 0;
var update_fields = 0;
var update_fields_data = '';
var conf_channels_xtra_display = 0;
var LCAcount = 0;
var LMAcount = 0;
var flag_channels = 0;
var flag_string = '';
var HideMonitorSessions = 1;
var volumecontrol_active = 1;
var customerparked = 0;
var customerparkedcounter = 0;
var agentchannel = '';
var no_blind_monitors = 0;
var blind_monitoring_now = 0;
var blind_monitoring_now_trigger = 0;
var AgentStatusStatus = '';
var AgentStatusCalls = '';
var AgentStatusDials = '';
var shift_logout_flag = 0;
var api_logout_flag = 0;
var refresh_interval = 1000;
var PauseNotifyCounter = 0;
var api_timer_action = '';
var api_timer_action_message = '';
var api_timer_action_seconds = 0;
var api_timer_action_destination = '';
var api_dtmf = '';
var api_transferconf_function = '';
var api_transferconf_group = '';
var api_transferconf_number = '';
var api_transferconf_consultative = '';
var api_transferconf_override = '';
var api_transferconf_group_alias = '';
var api_transferconf_cid_number = '';
var api_parkcustomer = '';
var nochannelinsession = 0;
var conf_dtmf = '';
var conf_silent_prefix = '5';
var dtmf_silent_prefix = '7';
var CallBackRecipient = '';
var CallBackLeadStatus = '';
var CallBackDateTime = '';
var CallBackrecipient = '';
var CallBackComments = '';
var DispoQMcsCODE = '';
var DispoSelection = '';
var DispoSelectStop = true;
var customer_3way_hangup_counter = 0;
var customer_3way_hangup_counter_trigger = 0;
var currently_in_email = 0;
var Dispo3wayMessage = '';
var DispoManualQueueMessage = '';
var manual_dial_in_progress = 0;
var QUEUEpadding = 0;
var focus_blur_enabled = 0;
var call_notes_dispo = '';
var call_notes = '';
var PerCallNotesContent = '';
var wrapup_waiting = 0;
var LIVE_default_group_alias_cid = '';
var LIVE_web_vars = '';
var default_group_alias_cid = '';
var default_web_vars = '';
var did_pattern = '';
var did_id = '';
var did_extension = '';
var did_description = '';
var closecallid = '';
var xfercallid = '';
var view_scripts = '1';
var Call_Script_ID = '';
var Call_Auto_Launch = '';
var useIE = 0;
var EMAILgroupCOUNT = 0;
var prepopulate_transfer_preset_enabled = 0;
var custom_field_names = '';
var custom_field_values = '';
var custom_field_types = '';
var web_form_varsX = '';
var vicidial_agent_disable = '';
<?php
    foreach ($_SESSION as $idx => $val) {
        if (preg_match("/^(is_logged_in|username)/", $idx)) {
            ${$idx} = $val;
            if ($idx == 'is_logged_in') {
                $val = ($val) ? 1 : 0;
                echo "var {$idx} = {$val};\n";
            } else {
                echo "var {$idx} = '{$val}';\n";
            }
        }
    }
    
    $forever_stop = 0;
    $user_abb = "{$username}{$username}{$username}{$username}";
    while ( (strlen($user_abb) > 4) and ($forever_stop < 200) )
        {$user_abb = preg_replace("/^\./i","",$user_abb);   $forever_stop++;}
?>
var user_abb = '<?=$user_abb?>';

<?php
    echo "// User Settings\n";
    $astDB->where('user', $username);
    $result = $astDB->getOne('vicidial_users', 'user,pass,phone_login,phone_pass,full_name,user_level,hotkeys_active,agent_choose_ingroups,scheduled_callbacks,agentonly_callbacks,agentcall_manual,vicidial_recording,vicidial_transfers,closer_default_blended,user_group,vicidial_recording_override,alter_custphone_override,alert_enabled,agent_shift_enforcement_override,shift_override_flag,allow_alerts,closer_campaigns,agent_choose_territories,custom_one,custom_two,custom_three,custom_four,custom_five,agent_call_log_view_override,agent_choose_blended,agent_lead_search_override,preset_contact_search');
    
    foreach ($result as $idx => $val) {
        if (preg_match("/^(vicidial_recording|vicidial_recording_override)$/", $idx)) {
            ${$idx} = $val;
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
            } else if (preg_match("/^(custom_)/g", $idx)) {
                echo "var user_{$idx} = '{$val}';\n";
            } else {
                echo "var {$idx} = '{$val}';\n";
            }
        }
    }
    //echo "// ".$result['user_group']."\n";
    
    echo "\n// Phone Settings\n";
    $astDB->where('login', $phone_login);
    $astDB->where('pass', $phone_pass);
    $astDB->where('active', 'Y');
    $result = $astDB->getOne('phones', 'extension,dialplan_number,voicemail_id,server_ip,login,pass,status,active,messages,old_messages,protocol,local_gmt,login_user,login_pass,login_campaign,park_on_extension,conf_on_extension,VICIDIAL_park_on_extension,VICIDIAL_park_on_filename,monitor_prefix,recording_exten,voicemail_exten,voicemail_dump_exten,ext_context,dtmf_send_extension,enable_fast_refresh,fast_refresh_rate,VDstop_rec_after_each_call,outbound_cid,enable_sipsak_messages,conf_secret,is_webphone,use_external_server_ip,codecs_list,webphone_dialpad,phone_ring_timeout,on_hook_agent,webphone_auto_answer');
    
    foreach ($result as $idx => $val) {
        echo "var {$idx} = '{$val}';\n";
    }
    
    echo "\n// System Settings\n";
    $result = $astDB->getOne('system_settings', 'use_non_latin,vdc_header_date_format,vdc_customer_date_format,vdc_header_phone_format,webroot_writable,timeclock_end_of_day,vtiger_url,enable_vtiger_integration,outbound_autodial_active,enable_second_webform,user_territories_active,static_agent_url,custom_fields_enabled,pllb_grouping_limit,qc_features_active,allow_emails,default_language');
    
    foreach ($result as $idx => $val) {
        if (preg_match("/^(vdc_)/", $idx)) {
            $idx_ = str_replace('vdc_', '', $idx);
            echo "var {$idx_} = '{$val}';\n";
        } else {
            if ($idx == 'allow_emails') {
                echo "var email_enabled = '{$val}';\n";
            } else {
                echo "var {$idx} = '{$val}';\n";
            }
        }
    }
    
    echo "\n// Campaign Settings\n";
    $astDB->where('campaign_id', 'TESTCAMP');
    $result = $astDB->getOne('vicidial_campaigns', 'park_ext,park_file_name,web_form_address,allow_closers,auto_dial_level,dial_timeout,dial_prefix,campaign_cid,campaign_vdad_exten,campaign_rec_exten,campaign_recording,campaign_rec_filename,campaign_script,get_call_launch,am_message_exten,xferconf_a_dtmf,xferconf_a_number,xferconf_b_dtmf,xferconf_b_number,alt_number_dialing,scheduled_callbacks,wrapup_seconds,wrapup_message,closer_campaigns,use_internal_dnc,allcalls_delay,omit_phone_code,agent_pause_codes_active,no_hopper_leads_logins,campaign_allow_inbound,manual_dial_list_id,default_xfer_group,xfer_groups,disable_alter_custphone,display_queue_count,manual_dial_filter,agent_clipboard_copy,use_campaign_dnc,three_way_call_cid,dial_method,three_way_dial_prefix,web_form_target,vtiger_screen_login,agent_allow_group_alias,default_group_alias,quick_transfer_button,prepopulate_transfer_preset,view_calls_in_queue,view_calls_in_queue_launch,call_requeue_button,pause_after_each_call,no_hopper_dialing,agent_dial_owner_only,agent_display_dialable_leads,web_form_address_two,agent_select_territories,crm_popup_login,crm_login_address,timer_action,timer_action_message,timer_action_seconds,start_call_url,dispo_call_url,xferconf_c_number,xferconf_d_number,xferconf_e_number,use_custom_cid,scheduled_callbacks_alert,scheduled_callbacks_count,manual_dial_override,blind_monitor_warning,blind_monitor_message,blind_monitor_filename,timer_action_destination,enable_xfer_presets,hide_xfer_number_to_dial,manual_dial_prefix,customer_3way_hangup_logging,customer_3way_hangup_seconds,customer_3way_hangup_action,ivr_park_call,manual_preview_dial,api_manual_dial,manual_dial_call_time_check,my_callback_option,per_call_notes,agent_lead_search,agent_lead_search_method,queuemetrics_phone_environment,auto_pause_precall,auto_pause_precall_code,auto_resume_precall,manual_dial_cid,custom_3way_button_transfer,callback_days_limit,disable_dispo_screen,disable_dispo_status,screen_labels,status_display_fields,pllb_grouping,pllb_grouping_limit,in_group_dial,in_group_dial_select,pause_after_next_call,owner_populate');
    $dial_prefix = '';
?>
var campaign = 'TESTCAMP';      // put here the selected campaign upon login
var group = 'TESTCAMP';         // same value as campaign variable
<?php
    foreach ($result as $idx => $val) {
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
            if (preg_match("/^(campaign_rec_filename|default_group_alias)$/", $idx)) {
                echo "var LIVE_{$idx} = '{$val}';\n";
            }
    
            if (!preg_match("/^(disable_dispo_screen|disable_dispo_status|campaign_recording)$/", $idx)) {
                if (preg_match("/^(web_form_address)", $idx)) {
                    echo "var VDIC_{$idx} = '{$val}';\n";
                    echo "var TEMP_VDIC_{$idx} = '{$val}';\n";
                } else {
                    echo "var {$idx} = '{$val}';\n";
                    if ($idx == 'auto_dial_level') {
                        echo "var starting_dial_level = '{$val}';\n";
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
?>

$(document).ready(function() {
    $(window).load(function() {
        var refreshId = setInterval(function() {
            if (is_logged_in) {
                //Start of checking for live calls
                if (live_customer_call == 1) {
                    live_call_seconds++;
                    //$("input[name='SecondS']").val(live_call_seconds);
                    //$("div:contains('CALL LENGTH:') > span").html(live_call_seconds);
                    //$("div:contains('SESSION ID:') > span").html(session_id);
                    toggleButton('DialHangup', 'hangup');
                    toggleButton('ResumePause', 'off');
                    
                    if (CheckDEADcall > 0) {
                        if (CheckDEADcallON < 1) {
                            toggleStatus('DEAD');
                            toggleButton('ParkCall', 'off');
                            toggleButton('TransferCall', 'off');
                            CheckDEADcallON = 1;
                            
                            if (xfer_in_call > 0 && customer_3way_hangup_logging == 'ENABLED') {
                                customer_3way_hangup_counter_trigger = 1;
                                customer_3way_hangup_counter = 1;
                            }
                        }
                    }
                } else {
                    toggleStatus('NOLIVE');
                    
                    if (dialingINprogress < 1) {
                        toggleButton('DialHangup', 'dial');
                        toggleButton('ResumePause', 'on');
                    }
                }
                //End of checking for live calls
    
                check_r++;
                WaitingForNextStep = 0;
    
                if (open_dispo_screen == 1) {
                    wrapup_counter = 0;
                    if (wrapup_seconds > 0) {
                        //showDiv('WrapupBox');
                        //$("#WrapupTimer").html(wrapup_seconds);
                        wrapup_waiting = 1;
                    }
    
                    //CustomerData_update();
                    //if (hide_gender < 1)
                    //{
                    //    $("#GENDERhideFORie").html('');
                    //    $("#GENDERhideFORieALT").html('<select size="1" name="gender_list" class="cust_form" id="gender_list"><option value="U">U - Undefined</option><option value="M">M - Male</option><option value="F">F - Female</option></select>');
                    //}
    
                    //DispoSelectBox();
                    //DispoSelectContent_create('','ReSET');
                    WaitingForNextStep = 1;
                    open_dispo_screen = 0;
                    LIVE_default_xfer_group = default_xfer_group;
                    LIVE_campaign_recording = campaign_recording;
                    LIVE_campaign_rec_filename = campaign_rec_filename;
                    if (disable_alter_custphone != 'HIDE')
                        {$("#DispoSelectPhone").html(dialed_number);}
                    else
                        {$("#DispoSelectPhone").html('');}
                    if (auto_dial_level == 0) {
                        if ($("#DialALTPhone").is(':checked') == true) {
                            reselect_alt_dial = 1;
                            toggleButton('DialHangup', 'dial');
    
                            $("#MainStatusSpan").html("Dial Next Call");
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

                if (WaitingForNextStep == 0) {
                    CheckForConfCalls(session_id, '0');

                    if (AutoDialWaiting == 1) {
                        CheckForIncoming();
                    }

                    if (MD_channel_look == 1) {
                        ManualDialCheckChannel();
                    }
                }
            }
        }, refresh_interval);
    });

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

    $("footer").append($navBar);
    var $vtFooter = jQuery(".main-footer");
    $vtFooter.css({
        'paddingBottom' : $navBar.css('border-top-width'),
    });
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
        
        $(".main-footer").stop(true, false).animate({
            paddingBottom : resized ? barHeight : 0,
        });
        
        if (($(window).scrollTop() + document.body.clientHeight) == $(document).height()) {
            $("html, body").animate({ scrollTop: $(document).height() }, 'slow');
        }
        
        $("#go_nav_tab i").attr({
            class: resized ? 'fa fa-chevron-down' : 'fa fa-chevron-up'
        });
        
        resized = !resized;
    });

    // buttons
    $("#go_nav_bar").append("<div id='go_nav_btn' class='hidden'></div>");
    $("#go_nav_btn").append("<button id='btnOtherMenu' class='btn btn-default pull-right' style='margin: 5px 0;'><i class='fa fa-navicon'></i></button>");
    $("#go_nav_btn").append("<div id='livecall' class='pull-right'><h3 class='nolivecall' title=''><?=$lh->translationFor('no_live_call')?></h3></div>");
    $("#go_nav_btn").append("<div id='go_btn_div' class='pull-left'></div>");
    $("#go_btn_div").append("<button id='btnDialHangup' title='<?=$lh->translationFor('dial_next_call')?>' class='btn btn-danger' style='margin: 5px 5px 5px 0;'><i class='fa fa-phone'></i></button>");
    $("#go_btn_div").append("<button id='btnResumePause' title='<?=$lh->translationFor('resume_dialing')?>' class='btn btn-success' style='margin: 5px 5px 5px 0;'><i class='fa fa-play'></i></button>");
    $("#go_btn_div").append("<button id='btnParkCall' title='<?=$lh->translationFor('park_call')?>' class='btn btn-warning' style='margin: 5px 5px 5px 0;'><i class='fa fa-music'></i></button>");
    $("#go_btn_div").append("<button id='btnTransferCall' title='<?=$lh->translationFor('transfer_call')?>' class='btn btn-default' style='margin: 5px 5px 5px 0;'><i class='fa fa-exchange'></i></button>");
    $("#go_btn_div").append("<button id='btnIVRParkCall' title='<?=$lh->translationFor('ivr_park_call')?>' class='btn btn-default' style='margin: 5px 5px 5px 0;'><i class='fa fa-tty'></i></button>");
    $("#go_btn_div").append("<button id='btnRequeueCall' title='<?=$lh->translationFor('requeue_call')?>' class='btn btn-default' style='margin: 5px 5px 5px 0;'><i class='fa fa-refresh'></i></button>");
    $("#go_nav_btn").append("<div id='cust-info' class='center-block' style='text-align: center; line-height: 35px;'><i class='fa fa-user'></i> <span id='cust-name' style='padding-right: 20px;'>Firstname Lastname</span> <i class='fa fa-phone-square'></i> <span id='cust-phone'>(212) 777-3456</span></div>");

    $("#go_nav_bar").append("<div id='go_nav_log'></div>");
    $("#go_nav_log").append("<button id='btnLogMeIn' class='btn btn-warning center-block' style='margin-top: 2px; padding: 5px 12px;'><i class='fa fa-sign-in'></i> <?=$lh->translationFor('login_on_phone')?></button>");

    $("button[id^='btn']").click(function() {
        var btnID = $(this).attr('id').replace('btn', '');
        console.log(btnID);
        switch (btnID) {
            case "DialHangup":
                btnDialHangup();
                break;
            case "ResumePause":
                btnResumePause();
                break;
            case "LogMeIn":
                btnLogMeIn();
        }
    });

    $("#cust-info").click(function() {
        $("#dialog-custinfo").modal({
            backdrop: 'static',
            show: true
        });
    });

    toggleButtons('RATIO');
    toggleStatus('NOLIVE');

    window.addEventListener("beforeunload", function (e) {
        if (live_customer_call) {
            var confirmationMessage = "<?=$lh->translationFor('currently_in_call')?>";
        
            (e || window.event).returnValue = confirmationMessage;     //Gecko + IE
            return confirmationMessage;                                //Webkit, Safari, Chrome etc.
        }
    });
});

function btnLogMeIn () {
    $("#select-campaign").modal({
        keyboard: false,
        backdrop: 'static',
        show: true
    });
}

function btnDialHangup () {
    //console.log(live_customer_call + ' ' + toggleButton('DialHangup'));
    if (live_customer_call == 1) {
        if (toggleButton('DialHangup')) {
            toggleButton('DialHangup', 'off');
            //AgentDispoing = 1;
            
            //Pause
            if ($("#DispoSelectStop").is(':checked')) {
                //sendToAPI('PAUSE');
                toggleButton('ResumePause', 'resume');
            } else {
                toggleButton('ResumePause', 'on');
            }
            
            //Hangup
            live_customer_call = 0;
            //DialedCallHangup();
            //delay(sendToAPI('HANGUP'), 500);
            
            //Dispose
            //DispoSelectBox();
            //delay(sendToAPI('STATUS', 'A'), 1000);
        }
    } else {
        toggleButton('DialHangup', 'hangup', false);
        toggleButton('ResumePause', 'off');
        live_customer_call = 1;
        toggleStatus('LIVE');
        
        //ManualDialNext('','','','','','0');
    }
}

function btnResumePause () {
    if (live_customer_call < 1) {
        var btnClass = $('#btnResumePause').children('i').attr('class');
        if (/pause$/.test(btnClass)) {
            toggleButton('ResumePause', 'resume');
            //AutoDial_Resume_Pause("VDADpause");
        } else {
            toggleButton('ResumePause', 'pause');
            //AutoDial_Resume_Pause("VDADready");
        }
    }
}

function toggleButton (taskname, taskaction, taskenable, toupperfirst, tolowerelse) {
    if (tolowerelse) {taskname = taskname.toLowerCase();}
    if (toupperfirst) {taskname = taskname.toUpperFirst();}
    
    var actClass = '';
    var actTitle = '';
    var isEnabled = (taskenable != null) ? taskenable : true;
    var isHidden = false;
    
    if (taskaction != null && taskaction.length > 0)
    {
        switch (taskaction.toLowerCase()) {
            case "dial":
                actClass = "fa fa-phone";
                actTitle = "<?=$lh->translationFor('dial_next_call')?>";
                break;
            case "hangup":
                actClass = "fa fa-stop";
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
            default:
                actClass = "";
        }
        
        if (actClass.length > 0) {
            if (!isEnabled) {
                $("#btn"+taskname).addClass('disabled');
            } else {
                $("#btn"+taskname).removeClass('disabled');
            }
            
            $("#btn"+taskname+" i").attr('class', actClass);
            if (actTitle != '') {
                $("#btn"+taskname).attr('title', actTitle);
            }
        } else {
            if (!isEnabled) {
                $("#btn"+taskname).addClass('disabled');
            } else {
                $("#btn"+taskname).removeClass('disabled');
            }
        }
        
        if (isHidden) {
            $("#btn"+taskname).addClass('hidden');
        }
    } else {
        var returnVal = ($("#btn"+taskname).hasClass('disabled')) ? false : true;
        return returnVal;
    }
}

function toggleButtons (taskaction, taskivr, taskrequeue) {
    if (taskaction != null && taskaction.length > 0) {
        var btnIVR = 'hide';
        if (taskivr == 'ENABLED' || taskivr == 'ENABLED_PARK_ONLY') {
            var btnIVR = 'off';
        }
        var btnRequeue = (taskrequeue == 'Y') ? 'off' : 'hide';
        
        switch (taskaction.toLowerCase()) {
            case "manual":
                console.log(taskaction);
                toggleButton('DialHangup', 'dial');
                toggleButton('ResumePause', 'hide');
                break;
            default:
                toggleButton('DialHangup', 'dial');
                toggleButton('ResumePause', 'resume');
        }
        
        //console.log("btnIVR = "+btnIVR+"; btnRequeue = "+btnRequeue);
        toggleButton('TransferCall', 'off');
        toggleButton('ParkCall', 'off');
        toggleButton('IVRParkCall', btnIVR);
        toggleButton('RequeueCall', btnRequeue);
    }
}

function updateButtons () {
    if (is_logged_in) {
        $("#go_nav_btn").removeClass('hidden');
        $("#go_nav_log").addClass('hidden');
    } else {
        $("#go_nav_btn").addClass('hidden');
        $("#go_nav_log").removeClass('hidden');
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

function CheckForConfCalls (confnum, force) {
    if (confnum.length < 1) {
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
        server_ip: server_ip,
        session_name: session_name,
        user: uName,
        pass: uPass,
        client: "vdc",
        conf_exten: confnum,
        auto_dial_level: auto_dial_level,
        campagentstdisp: campagentstdisp
    };

    $.post(baseURL+'/agent/conf_exten_check.php', postData, function(result) {
        var LMAforce = force;
        var check_ALL_array=result.split("\n");
        var check_time_array=check_ALL_array[0].split("|");
        var time_array = check_time_array[1].split("UnixTime: ");
            UnixTime = time_array[1];
            UnixTime = parseInt(UnixTime);
            UnixTimeMS = (UnixTime * 1000);
            t.setTime(UnixTimeMS);
        if ( (callholdstatus == '1') || (agentcallsstatus == '1') || (vicidial_agent_disable != 'NOT_ACTIVE') ) {
            var Alogin_array = check_time_array[2].split("Logged-in: ");
            var AGLogin = Alogin_array[1];
            var CampCalls_array = check_time_array[3].split("CampCalls: ");
            var CampCalls = CampCalls_array[1];
            var DialCalls_array = check_time_array[5].split("DiaLCalls: ");
            var DialCalls = DialCalls_array[1];
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
        
        var VLAStatus_array = check_time_array[4].split("Status: ");
        var VLAStatus = VLAStatus_array[1];
        if ( (VLAStatus == 'PAUSED') && (AutoDialWaiting == 1) ) {
            if (PauseNotifyCounter > 10) {
                alert_dialog('STATUS','Your session has been paused');
                AutoDial_Resume_Pause('VDADpause');
                PauseNotifyCounter = 0;
            } else {
                PauseNotifyCounter++;
            }
        } else {
            PauseNotifyCounter = 0;
        }
        
        var APIhangup_array = check_time_array[6].split("APIHanguP: ");
        var APIhangup = APIhangup_array[1];
        var APIstatus_array = check_time_array[7].split("APIStatuS: ");
        var APIstatus = APIstatus_array[1];
        var APIpause_array = check_time_array[8].split("APIPausE: ");
        var APIpause = APIpause_array[1];
        var APIdial_array = check_time_array[9].split("APIDiaL: ");
        var APIdial = APIdial_array[1];
        var APIManualDialQueue_array = check_time_array[24].split("APIManualDialQueue: ");
            APIManualDialQueue = APIManualDialQueue_array[1];
        var CheckDEADcall_array = check_time_array[10].split("DEADcall: ");
        var CheckDEADcall = CheckDEADcall_array[1];
        var InGroupChange_array = check_time_array[11].split("InGroupChange: ");
        var InGroupChange = InGroupChange_array[1];
        var InGroupChangeBlend = check_time_array[12];
        var InGroupChangeUser = check_time_array[13];
        var InGroupChangeName = check_time_array[14];
        var APIFields_array = check_time_array[15].split("APIFields: ");
            update_fields = APIFields_array[1];
        var APIFieldsData_array = check_time_array[16].split("APIFieldsData: ");
            update_fields_data = APIFieldsData_array[1];
        var APITimerAction_array = check_time_array[17].split("APITimerAction: ");
            api_timer_action = APITimerAction_array[1];
        var APITimerMessage_array = check_time_array[18].split("APITimerMessage: ");
            api_timer_action_message = APITimerMessage_array[1];
        var APITimerSeconds_array = check_time_array[19].split("APITimerSeconds: ");
            api_timer_action_seconds = APITimerSeconds_array[1];
        var APITimerDestination_array = check_time_array[23].split("APITimerDestination: ");
            api_timer_action_destination = APITimerDestination_array[1];
        var APIRecording_array = check_time_array[25].split("APIRecording: ");
        var api_recording = APIRecording_array[1];
        var APIdtmf_array = check_time_array[20].split("APIdtmf: ");
            api_dtmf = APIdtmf_array[1];
        var APItransfercond_array = check_time_array[21].split("APItransferconf: ");
        var api_transferconf_values_array = APItransfercond_array[1].split("---");
            api_transferconf_function = api_transferconf_values_array[0];
            api_transferconf_group = api_transferconf_values_array[1];
            api_transferconf_number = api_transferconf_values_array[2];
            api_transferconf_consultative = api_transferconf_values_array[3];
            api_transferconf_override = api_transferconf_values_array[4];
            api_transferconf_group_alias = api_transferconf_values_array[5];
            api_transferconf_cid_number = api_transferconf_values_array[6];
        var APIpark_array = check_time_array[22].split("APIpark: ");
            api_parkcustomer = APIpark_array[1];
            
        if (api_recording=='START') {
            ConfSendRecording('MonitorConf', session_id,'','1');
            //sendToAPI('recording', 'START');
        }
        if (api_recording=='STOP') {
            ConfSendRecording('StopMonitorConf', session_id, recording_filename,'1');
            //sendToAPI('recording', 'STOP');
        }
        if (api_transferconf_function.length > 0) {
            if (api_transferconf_function == 'HANGUP_XFER')
                {xfercall_send_hangup();}
            if (api_transferconf_function == 'HANGUP_BOTH')
                {bothcall_send_hangup();}
            if (api_transferconf_function == 'LEAVE_VM')
                {mainxfer_send_redirect('XfeRVMAIL',lastcustchannel,lastcustserverip);}
            if (api_transferconf_function == 'LEAVE_3WAY_CALL')
                {leave_3way_call('FIRST');}
            if (api_transferconf_function == 'BLIND_TRANSFER') {
                document.vicidial_form.xfernumber.value = api_transferconf_number;
                mainxfer_send_redirect('XfeRBLIND',lastcustchannel,lastcustserverip);
            }
            if (external_transferconf_count < 1) {
                if (api_transferconf_function == 'LOCAL_CLOSER') {
                    API_selected_xfergroup = api_transferconf_group;
                    document.vicidial_form.xfernumber.value = api_transferconf_number;
                    mainxfer_send_redirect('XfeRLOCAL',lastcustchannel,lastcustserverip);
                }
                if (api_transferconf_function == 'DIAL_WITH_CUSTOMER') {
                    if (api_transferconf_consultative=='YES')
                        {document.vicidial_form.consultativexfer.checked=true;}
                    if (api_transferconf_consultative=='NO')
                        {document.vicidial_form.consultativexfer.checked=false;}
                    if (api_transferconf_override=='YES')
                        {document.vicidial_form.xferoverride.checked=true;}
                    API_selected_xfergroup = api_transferconf_group;
                    document.vicidial_form.xfernumber.value = api_transferconf_number;
                    active_group_alias = api_transferconf_group_alias;
                    cid_choice = api_transferconf_cid_number;
                    SendManualDial('YES');
                }
                if (api_transferconf_function == 'PARK_CUSTOMER_DIAL') {
                    if (api_transferconf_consultative == 'YES')
                        {document.vicidial_form.consultativexfer.checked = true;}
                    if (api_transferconf_consultative == 'NO')
                        {document.vicidial_form.consultativexfer.checked = false;}
                    if (api_transferconf_override == 'YES')
                        {document.vicidial_form.xferoverride.checked = true;}
                    API_selected_xfergroup = api_transferconf_group;
                    document.vicidial_form.xfernumber.value = api_transferconf_number;
                    active_group_alias = api_transferconf_group_alias;
                    cid_choice = api_transferconf_cid_number;
                    xfer_park_dial();
                }
                external_transferconf_count = 3;
            }
            Clear_API_Field('external_transferconf');
        }
        if (api_parkcustomer == 'PARK_CUSTOMER')
            {mainxfer_send_redirect('ParK', lastcustchannel, lastcustserverip);}
        if (api_parkcustomer == 'GRAB_CUSTOMER')
            {mainxfer_send_redirect('FROMParK', lastcustchannel, lastcustserverip);}
        if (api_parkcustomer == 'PARK_IVR_CUSTOMER')
            {mainxfer_send_redirect('ParKivr', lastcustchannel, lastcustserverip);}
        if (api_parkcustomer == 'GRAB_IVR_CUSTOMER')
            {mainxfer_send_redirect('FROMParKivr', lastcustchannel, lastcustserverip);}
        if (api_dtmf.length > 0) {
            var REGdtmfPOUND = new RegExp("P","g");
            var REGdtmfSTAR = new RegExp("S","g");
            var REGdtmfQUIET = new RegExp("Q","g");
            api_dtmf = api_dtmf.replace(REGdtmfPOUND, '#');
            api_dtmf = api_dtmf.replace(REGdtmfSTAR, '*');
            api_dtmf = api_dtmf.replace(REGdtmfQUIET, ',');
            document.vicidial_form.conf_dtmf.value = api_dtmf;
            SendConfDTMF(session_id);
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
                    console.log("Setting agent status to RESUME");
                }
            }
        }
        
        //API catcher for Manual Dial
        if (APIdial.length > 9 && AllowManualQueueCalls == '0') {
            APIManualDialQueue++;
        }
        if (APIManualDialQueue != APIManualDialQueue_last) {
            APIManualDialQueue_last = APIManualDialQueue;
            console.info('Manual Queue: '+APIManualDialQueue);
        }
        
        if (APIdial.length > 9 && WaitingForNextStep == '0' && AllowManualQueueCalls == '1' && check_r > 2) {
            var APIdial_array_detail = APIdial.split("!");
            if (APIdial_ID != APIdial_array_detail[6]) {
                APIdial_ID = APIdial_array_detail[6];
                $('#inputphone_code').val(APIdial_array_detail[1]);
                $('#inputphone_number').val(APIdial_array_detail[0]);
                $('#inputvendor_lead_code').val(APIdial_array_detail[5]);
                prefix_choice = APIdial_array_detail[7];
                active_group_alias = APIdial_array_detail[8];
                cid_choice = APIdial_array_detail[9];
                vtiger_callback_id = APIdial_array_detail[10];
                $("input[name='lead_id']").val(APIdial_array_detail[11]);
                $("input[name='uniqueid']").val(APIdial_array_detail[12]);
                
                if (active_group_alias.length > 1)
                    {var sending_group_alias = 1;}
                
                console.log('Dialing '+APIdial_array_detail[0]+'...');
                if (APIdial_array_detail[2] == 'YES')  // lookup lead in system
                    {$("#LeadLookUP").prop('checked', true);}
                else
                    {$("#LeadLookUP").prop('checked', false);}
                if (APIdial_array_detail[4] == 'YES')  // focus on vicidial agent screen
                    {window.focus();   alert_dialog("MANUAL DIAL","Placing Call To:" + APIdial_array_detail[1] + " " + APIdial_array_detail[0]);}
                if (APIdial_array_detail[3] == 'YES')  // call preview
                    {NewManualDialCall('PREVIEW');}
                else
                    {NewManualDialCall('NOW');}
            }
        }
        
        if (InGroupChange > 0) {
            var external_blended = InGroupChangeBlend;
            var external_igb_set_user = InGroupChangeUser;
            external_igb_set_name = InGroupChangeName;
            manager_ingroups_set = 1;

            if ( (external_blended == '1') && (dial_method != 'INBOUND_MAN') )
                {closer_blended = '1';}

            if (external_blended == '0')
                {closer_blended = '0';}
        }
        
        var check_conf_array = check_ALL_array[1].split("|");
        var live_conf_calls = check_conf_array[0];
        var conf_chan_array = check_conf_array[1].split(" ~");
        if ( (conf_channels_xtra_display == 1) || (conf_channels_xtra_display == 0) ) {
            if (live_conf_calls > 0) {
                var temp_blind_monitors = 0;
                var loop_ct = 0;
                var ARY_ct = 0;
                var LMAalter = 0;
                var LMAcontent_change = 0;
                var LMAcontent_match = 0;
                agentphonelive = 0;
                var conv_start = -1;
                var live_conf_HTML = '<font face="Arial,Helvetica"><b><?=$lh->translationFor('live_calls_in_your_session')?>:</b></font><br /><table width="340px"><tr><td><font class="log_title">#</font></td><td><font class="log_title"><?=$lh->translationFor('remote_channel')?></font></td><td><font class="log_title"><?=$lh->translationFor('hangup')?></font></td></tr>';
                if ( (LMAcount > live_conf_calls)  || (LMAcount < live_conf_calls) || (LMAforce > 0)) {
                    LMAe[0]=''; LMAe[1]=''; LMAe[2]=''; LMAe[3]=''; LMAe[4]=''; LMAe[5]=''; 
                    LMAcount=0;   LMAcontent_change++;
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
                        if (blind_monitoring_now==1)
                            {blind_monitoring_now_trigger=1;}
                    } else {
                        if (channelfieldA.match(regRNnolink)) {
                            // do not show hangup or volume control links for recording channels
                            live_conf_HTML = live_conf_HTML + '<tr bgcolor="' + row_color + '"><td><font class="log_text">' + loop_ct + '</font></td><td><font class="' + chan_name_color + '">' + channelfieldA + '</font></td><td><font class="log_text"><?=$lh->translationFor('recording')?></font></td></tr>';
                        } else {
                            if (volumecontrol_active!=1) {
                                live_conf_HTML = live_conf_HTML + '<tr bgcolor="' + row_color + '"><td><font class="log_text">' + loop_ct + '</font></td><td><font class="' + chan_name_color + '">' + channelfieldA + '</font></td><td><font class="log_text"><a href="#" onclick="livehangup_send_hangup(\"' + channelfieldA + '\");return false;"><?=$lh->translationFor('hangup')?></a></font></td></tr>';
                            } else {
                                live_conf_HTML = live_conf_HTML + '<tr bgcolor="' + row_color + '"><td><font class="log_text">' + loop_ct + '</font></td><td><font class="' + chan_name_color + '">' + channelfieldA + '</font></td><td><font class="log_text"><a href="#" onclick="livehangup_send_hangup(\"' + channelfieldA + '\");return false;"><?=$lh->translationFor('hangup')?></a></font></td><td><a href="#" onclick="volume_control(\"UP\",\"' + channelfieldA + '\",\"\");return false;"><img src="./images/vdc_volume_up.gif" border="0" /></a> &nbsp; <a href="#" onclick="volume_control(\"DOWN\",\"' + channelfieldA + '\",\"\");return false;"><img src="./images/vdc_volume_down.gif" border="0" /></a> &nbsp; &nbsp; &nbsp; <a href="#" onclick="volume_control(\"MUTING\",\"' + channelfieldA + '\",\"\");return false;"><img src="./images/vdc_volume_MUTE.gif" border="0" /></a> &nbsp; <a href="#" onclick="volume_control(\"UNMUTE\",\"' + channelfieldA + '\",\"\");return false;"><img src="./images/vdc_volume_UNMUTE.gif" border="0" /></a></td></tr>';
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

                                //$("#AgentMuteSpan").html("<a href='#CHAN-" + agentchannel + "' onclick='volume_control(\"MUTING\",\"" + agentchannel + "\",\"AgenT\");return false;'><img src='./images/vdc_volume_MUTE.gif' border='0' /></a>");
                            }
                        } else {
                            if (agentchannel.length < 3) {
                                agentchannel = channelfieldA;

                                //$("#AgentMuteSpan").html("<a href='#CHAN-" + agentchannel + "' onclick='volume_control(\"MUTING\",\"" + agentchannel + "\",\"AgenT\");return false;'><img src='./images/vdc_volume_MUTE.gif' border='0' /></a>");
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

                if (agentphonelive < 1) {agentchannel='';}

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
    });
}

function CheckForIncoming () {
    all_record = 'NO';
    all_record_count=0;

    var postData = {
        server_ip: server_ip,
        session_name: session_name,
        user: uName,
        pass: uPass,
        campaign: campaign,
        ACTION: 'VDADcheckINCOMING',
        agent_log_id: agent_log_id
    };

    $.post(baseURL+'/agent/vdc_db_query.php', postData, function(result) {
        if (live_customer_call == 1) {
            //console.log(result);
            forTestingOnly = '';
        }

        var check_VDIC_array=result.split("\n");
        if (check_VDIC_array[0] == '1') {
            AutoDialWaiting = 0;
            QUEUEpadding = 0;
            
            var VDIC_data_VDAC = check_VDIC_array[1].split("|");
            VDIC_web_form_address = web_form_address
            VDIC_web_form_address_two = web_form_address_two
            var VDIC_fronter='';
            
            var VDIC_data_VDIG = check_VDIC_array[2].split("|");
            if (VDIC_data_VDIG[0].length > 5)
                {VDIC_web_form_address = VDIC_data_VDIG[0];}
            var VDCL_group_name                         = VDIC_data_VDIG[1];
            var VDCL_group_color                        = VDIC_data_VDIG[2];
            var VDCL_fronter_display                    = VDIC_data_VDIG[3];
                VDCL_group_id                           = VDIC_data_VDIG[4];
                Call_Script_id                          = VDIC_data_VDIG[5];
                Call_Auto_Launch                        = VDIC_data_VDIG[6];
                Call_XC_a_DTMF                          = VDIC_data_VDIG[7];
                Call_XC_a_Number                        = VDIC_data_VDIG[8];
                Call_XC_b_DTMF                          = VDIC_data_VDIG[9];
                Call_XC_b_Number                        = VDIC_data_VDIG[10];
            if ( (VDIC_data_VDIG[11].length > 1) && (VDIC_data_VDIG[11] != '---NONE---') )
                {LIVE_default_xfer_group = VDIC_data_VDIG[11];}
            else
                {LIVE_default_xfer_group = default_xfer_group;}

            if ( (VDIC_data_VDIG[12].length > 1) && (VDIC_data_VDIG[12]!='DISABLED') )
                {LIVE_campaign_recording = VDIC_data_VDIG[12];}
            else
                {LIVE_campaign_recording = campaign_recording;}

            if ( (VDIC_data_VDIG[13].length > 1) && (VDIC_data_VDIG[13]!='NONE') )
                {LIVE_campaign_rec_filename = VDIC_data_VDIG[13];}
            else
                {LIVE_campaign_rec_filename = campaign_rec_filename;}

            if ( (VDIC_data_VDIG[14].length > 1) && (VDIC_data_VDIG[14]!='NONE') )
                {LIVE_default_group_alias = VDIC_data_VDIG[14];}
            else
                {LIVE_default_group_alias = default_group_alias;}

            if ( (VDIC_data_VDIG[15].length > 1) && (VDIC_data_VDIG[15]!='NONE') )
                {LIVE_caller_id_number = VDIC_data_VDIG[15];}
            else
                {LIVE_caller_id_number = default_group_alias_cid;}

            if (VDIC_data_VDIG[16].length > 0)
                {LIVE_web_vars = VDIC_data_VDIG[16];}
            else
                {LIVE_web_vars = default_web_vars;}

            if (VDIC_data_VDIG[17].length > 5)
                {VDIC_web_form_address_two = VDIC_data_VDIG[17];}

            var call_timer_action                       = VDIC_data_VDIG[18];

            if ( (call_timer_action == 'NONE') || (call_timer_action.length < 2) ) {
                timer_action = campaign_timer_action;
                timer_action_message = campaign_timer_action_message;
                timer_action_seconds = campaign_timer_action_seconds;
                timer_action_destination = campaign_timer_action_destination;
            } else {
                var call_timer_action_message           = VDIC_data_VDIG[19];
                var call_timer_action_seconds           = VDIC_data_VDIG[20];
                var call_timer_action_destination       = VDIC_data_VDIG[27];
                timer_action = call_timer_action;
                timer_action_message = call_timer_action_message;
                timer_action_seconds = call_timer_action_seconds;
                timer_action_destination = call_timer_action_destination;
            }

            Call_XC_c_Number                            = VDIC_data_VDIG[21];
            Call_XC_d_Number                            = VDIC_data_VDIG[22];
            Call_XC_e_Number                            = VDIC_data_VDIG[23];
            Call_XC_e_Number                            = VDIC_data_VDIG[23];
            uniqueid_status_display                     = VDIC_data_VDIG[24];
            uniqueid_status_prefix                      = VDIC_data_VDIG[26];
            did_id                                      = VDIC_data_VDIG[28];
            did_extension                               = VDIC_data_VDIG[29];
            did_pattern                                 = VDIC_data_VDIG[30];
            did_description                             = VDIC_data_VDIG[31];
            closecallid                                 = VDIC_data_VDIG[32];
            xfercallid                                  = VDIC_data_VDIG[33];

            var VDIC_data_VDFR=check_VDIC_array[3].split("|");
            if ( (VDIC_data_VDFR[1].length > 1) && (VDCL_fronter_display == 'Y') )
                {VDIC_fronter = "  Fronter: " + VDIC_data_VDFR[0] + " - " + VDIC_data_VDFR[1];}
            
            $("#formMain input[name='lead_id']").val(VDIC_data_VDAC[0]);
            $("#formMain input[name='uniqueid']").val(VDIC_data_VDAC[1]);
            CIDcheck                                    = VDIC_data_VDAC[2];
            CallCID                                     = VDIC_data_VDAC[2];
            LastCallCID                                 = VDIC_data_VDAC[2];
            $("#callchannel").html(VDIC_data_VDAC[3]);
            lastcustchannel                             = VDIC_data_VDAC[3];
            $("#formMain input[name='callserverip']").val(VDIC_data_VDAC[4]);
            lastcustserverip = VDIC_data_VDAC[4];

            toggleStatus('LIVE');

            $("#formMain input[name='SecondS']").val(0);
            //$("SecondSDISP").html('0');

            if (uniqueid_status_display=='ENABLED')
                {custom_call_id = " Call ID " + VDIC_data_VDAC[1];}
            if (uniqueid_status_display=='ENABLED_PREFIX')
                {custom_call_id = " Call ID " + uniqueid_status_prefix + "" + VDIC_data_VDAC[1];}
            if (uniqueid_status_display=='ENABLED_PRESERVE')
                {custom_call_id = " Call ID " + VDIC_data_VDIG[25];}

            live_customer_call = 1;
            live_call_seconds = 0;

            // INSERT VICIDIAL_LOG ENTRY FOR THIS CALL PROCESS
            // DialLog("start");

            custchannellive = 1;

            LastCID                                     = check_VDIC_array[4];
            LeadPrevDispo                               = check_VDIC_array[6];
            fronter                                     = check_VDIC_array[7];
            $("#inputvendor_lead_code").val(check_VDIC_array[8]);
            $("#formMain input[name='list_id']").val(check_VDIC_array[9]);
            $("#formMain input[name='gmt_offset_now']").val(check_VDIC_array[10]);
            $("#inputphone_code").val(check_VDIC_array[11]);
            $("#inputphone_number").val(check_VDIC_array[12]);
            $("#inputtitle").val(check_VDIC_array[13]);
            $("#inputfirst_name").val(check_VDIC_array[14]);
            $("#inputmiddle_initial").val(check_VDIC_array[15]);
            $("#inputlast_name").val(check_VDIC_array[16]);
            $("#inputaddress1").val(check_VDIC_array[17]);
            $("#inputaddress2").val(check_VDIC_array[18]);
            $("#inputaddress3").val(check_VDIC_array[19]);
            $("#inputcity").val(check_VDIC_array[20]);
            $("#inputstate").val(check_VDIC_array[21]);
            $("#inputprovince").val(check_VDIC_array[22]);
            $("#inputpostal_code").val(check_VDIC_array[23]);
            $("#inputcountry_code").val(check_VDIC_array[24]);
            $("#inputgender").val(check_VDIC_array[25]);
            $("#inputdate_of_birth").val(check_VDIC_array[26]);
            $("#inputalt_phone").val(check_VDIC_array[27]);
            $("#inputemail").val(check_VDIC_array[28]);
            $("#inputsecurity_phrase").val(check_VDIC_array[29]);
            var REGcommentsNL = new RegExp("!N","g");
            check_VDIC_array[30] = check_VDIC_array[30].replace(REGcommentsNL, "\n");
            $("#inputcomments").val(check_VDIC_array[30]);
            $("#inputcalled_count").val(check_VDIC_array[31]);
            CBentry_time                                = check_VDIC_array[32];
            CBcallback_time                             = check_VDIC_array[33];
            CBuser                                      = check_VDIC_array[34];
            CBcomments                                  = check_VDIC_array[35];
            dialed_number                               = check_VDIC_array[36];
            dialed_label                                = check_VDIC_array[37];
            source_id                                   = check_VDIC_array[38];
            EAphone_code                                = check_VDIC_array[39];
            EAphone_number                              = check_VDIC_array[40];
            EAalt_phone_notes                           = check_VDIC_array[41];
            EAalt_phone_active                          = check_VDIC_array[42];
            EAalt_phone_count                           = check_VDIC_array[43];
            $("#formMain input[name='rank']").val(check_VDIC_array[44]);
            $("#formMain input[name='owner']").val(check_VDIC_array[45]);
            script_recording_delay                      = check_VDIC_array[46];
            $("#formMain input[name='entry_list_id'").val(check_VDIC_array[47]);
            custom_field_names                          = check_VDIC_array[48];
            custom_field_values                         = check_VDIC_array[49];
            custom_field_types                          = check_VDIC_array[50];
            //Added By Poundteam for Audited Comments (Manual Dial Section Only)
            //if (qc_enabled > 0)
            //{
            //    document.vicidial_form.ViewCommentButton.value                                  = check_VDIC_array[53];
            //    document.vicidial_form.audit_comments_button.value                              = check_VDIC_array[53];
            //    var REGACcomments = new RegExp("!N","g");
            //    check_VDIC_array[54] = check_VDIC_array[54].replace(REGACcomments, "\n");
            //    document.vicidial_form.audit_comments.value                                     = check_VDIC_array[54];
            //}
            //END section Added By Poundteam for Audited Comments
            // Add here for AutoDial (VDADcheckINCOMING in vdc_db_query)

            //if (hide_gender > 0)
            //{
            //    document.vicidial_form.gender_list.value	= check_VDIC_array[25];
            //} else {
            //    var gIndex = 0;
            //    if (document.vicidial_form.gender.value == 'M') {var gIndex = 1;}
            //    if (document.vicidial_form.gender.value == 'F') {var gIndex = 2;}
            //    document.getElementById("gender_list").selectedIndex = gIndex;
            //}

            lead_dial_number = $("#inputphone_number").val();
            var dispnum = $("#inputphone_number").val();
            var status_display_number = phone_number_format(dispnum);
            var callnum = dialed_number;
            var dial_display_number = phone_number_format(callnum);

            var status_display_content = '';
            if (status_display_CALLID > 0) {status_display_content = status_display_content + " UID: " + LastCID;}
            if (status_display_LEADID > 0) {status_display_content = status_display_content + " Lead: " + $("#formMain input[name='lead_id'").val();}
            if (status_display_LISTID > 0) {status_display_content = status_display_content + " List: " + $("#formMain input[name='list_id'").val();}

            $("#MainStatusSpan").html(" Incoming: " + dial_display_number + " " + custom_call_id + " " + status_display_content + " &nbsp; " + VDIC_fronter);

            toggleButton('DialHangup','hangup');
            toggleButton('ResumePause', 'off');

            //if (CBentry_time.length > 2)
            //{
            //    $("#CustInfoSpan").html(" <b> PREVIOUS CALLBACK </b>");
            //    $("#CustInfoSpan").css('background', CustCB_bgcolor);
            //    $("#CBcommentsBoxA").html("<b>Last Call: </b>" + CBentry_time);
            //    $("#CBcommentsBoxB").html("<b>CallBack: </b>" + CBcallback_time);
            //    $("#CBcommentsBoxC").html("<b>Agent: </b>" + CBuser);
            //    $("#CBcommentsBoxD").html("<b>Comments: </b><br />" + CBcomments);
            //    //showDiv('CBcommentsBox');
            //}
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

            if (VDIC_data_VDIG[1].length > 0)
            {
                inOUT = 'IN';
                if (VDIC_data_VDIG[2].length > 2)
                {
                    $("#MainStatusSpan").css('background', VDIC_data_VDIG[2]);
                }
                var dispnum = $("#inputphone_number").val();
                var status_display_number = phone_number_format(dispnum);
                var callnum = dialed_number;
                var dial_display_number = phone_number_format(callnum);

                var status_display_content='';
                if (status_display_CALLID > 0) {status_display_content = status_display_content + " UID: " + CIDcheck;}
                if (status_display_LEADID > 0) {status_display_content = status_display_content + " Lead: " + $("#formMain input[name='lead_id']").val();}
                if (status_display_LISTID > 0) {status_display_content = status_display_content + " List: " + $("#formMain input[name='list_id']").val();}

                $("#MainStatusSpan").html(" Incoming: " + dial_display_number + " " + custom_call_id + " Group- " + VDIC_data_VDIG[1] + " &nbsp; " + VDIC_fronter + " " + status_display_content); 
            }

            //document.getElementById("ParkControl").innerHTML ="<a href=\"#\" onclick=\"mainxfer_send_redirect('ParK','" + lastcustchannel + "','" + lastcustserverip + "');disableButton('XFER');return false;\"><img src=\"./images/callpark.png\" border=\"0\" title=\"Park Call\" alt=\"Park Call\" /></a>";
            //if ( (ivr_park_call=='ENABLED') || (ivr_park_call=='ENABLED_PARK_ONLY') )
            //{
            //    document.getElementById("ivrParkControl").innerHTML ="<a href=\"#\" onclick=\"mainxfer_send_redirect('ParKivr','" + lastcustchannel + "','" + lastcustserverip + "');return false;\"><img src=\"./images/ivrcallpark.png\" style=\"padding-bottom:3px;\" border=\"0\" title=\"IVR Park Call\" alt=\"IVR Park Call\" /></a>";
            //}
            //
            //document.getElementById("HangupControl").innerHTML = "<a href=\"#\" onclick=\"dialedcall_send_hangup();\"><img src=\"./images/hangup.png\" border=\"0\" title=\"Hangup Customer\" alt=\"Hangup Customer\" /></a>";
            //
            //document.getElementById("XferControl").innerHTML = "<a href=\"#\" onclick=\"ShoWTransferMain('ON');disableButton('PARK');\"><img src=\"./images/transfer.png\" border=\"0\" title=\"Transfer - Conference\" alt=\"Transfer - Conference\" /></a>";
            //
            //document.getElementById("LocalCloser").innerHTML = "<a href=\"#\" onclick=\"mainxfer_send_redirect('XfeRLOCAL','" + lastcustchannel + "','" + lastcustserverip + "');return false;\"><img src=\"./images/vdc_XB_localcloser.gif\" border=\"0\" alt=\"LOCAL CLOSER\" style=\"vertical-align:middle\" /></a>";
            //
            //document.getElementById("DialBlindTransfer").innerHTML = "<input type=\"button\" onclick=\"mainxfer_send_redirect('XfeRBLIND','" + lastcustchannel + "','" + lastcustserverip + "');return false;\" value=\" BLIND TRANSFER \" style=\"font-size:10px;width:150px;vertical-align:middle;\" />";
            //
            //document.getElementById("DialBlindVMail").innerHTML = "<a href=\"#\" onclick=\"mainxfer_send_redirect('XfeRVMAIL','" + lastcustchannel + "','" + lastcustserverip + "');return false;\"><img src=\"./images/vdc_XB_ammessage.gif\" border=\"0\" alt=\"Blind Transfer VMail Message\" style=\"vertical-align:middle\" /></a>";

            if ( (quick_transfer_button == 'IN_GROUP') || (quick_transfer_button == 'LOCKED_IN_GROUP') )
            {
                if (quick_transfer_button_locked > 0)
                    {quick_transfer_button_orig = default_xfer_group;}

                //$("#QuickXfer").html("<a href=\"#\" onclick=\"mainxfer_send_redirect('XfeRLOCAL','" + lastcustchannel + "','" + lastcustserverip + "','','','" + quick_transfer_button_locked + "');return false;\"><img src=\"./images/quicktransfer.png\" style=\"padding-bottom:3px;\" border=\"0\" title=\"Quick Transfer\" alt=\"QUICK TRANSFER\" /></a>");
            }
            if (prepopulate_transfer_preset_enabled > 0)
            {
                if ( (prepopulate_transfer_preset == 'PRESET_1') || (prepopulate_transfer_preset == 'LOCKED_PRESET_1') )
                    {$("#xfernumber").val(Call_XC_a_Number);   $("#xfername").val('D1');}
                if ( (prepopulate_transfer_preset == 'PRESET_2') || (prepopulate_transfer_preset == 'LOCKED_PRESET_2') )
                    {$("#xfernumber").val(Call_XC_b_Number);   $("#xfername").val('D2');}
                if ( (prepopulate_transfer_preset == 'PRESET_3') || (prepopulate_transfer_preset == 'LOCKED_PRESET_3') )
                    {$("#xfernumber").val(Call_XC_c_Number);   $("#xfername").val('D3');}
                if ( (prepopulate_transfer_preset == 'PRESET_4') || (prepopulate_transfer_preset == 'LOCKED_PRESET_4') )
                    {$("#xfernumber").val(Call_XC_d_Number);   $("#xfername").val('D4');}
                if ( (prepopulate_transfer_preset == 'PRESET_5') || (prepopulate_transfer_preset == 'LOCKED_PRESET_5') )
                    {$("#xfernumber").val(Call_XC_e_Number);   $("#xfername").val('D5');}
            }
            if ( (quick_transfer_button == 'PRESET_1') || (quick_transfer_button == 'PRESET_2') || (quick_transfer_button == 'PRESET_3') || (quick_transfer_button == 'PRESET_4') || (quick_transfer_button == 'PRESET_5') || (quick_transfer_button == 'LOCKED_PRESET_1') || (quick_transfer_button == 'LOCKED_PRESET_2') || (quick_transfer_button == 'LOCKED_PRESET_3') || (quick_transfer_button == 'LOCKED_PRESET_4') || (quick_transfer_button == 'LOCKED_PRESET_5') )
            {
                if ( (quick_transfer_button == 'PRESET_1') || (quick_transfer_button == 'LOCKED_PRESET_1') )
                    {$("#xfernumber").val(Call_XC_a_Number);   $("#xfername").val('D1');}
                if ( (quick_transfer_button == 'PRESET_2') || (quick_transfer_button == 'LOCKED_PRESET_2') )
                    {$("#xfernumber").val(Call_XC_b_Number);   $("#xfername").val('D2');}
                if ( (quick_transfer_button == 'PRESET_3') || (quick_transfer_button == 'LOCKED_PRESET_3') )
                    {$("#xfernumber").val(Call_XC_c_Number);   $("#xfername").val('D3');}
                if ( (quick_transfer_button == 'PRESET_4') || (quick_transfer_button == 'LOCKED_PRESET_4') )
                    {$("#xfernumber").val(Call_XC_d_Number);   $("#xfername").val('D4');}
                if ( (quick_transfer_button == 'PRESET_5') || (quick_transfer_button == 'LOCKED_PRESET_5') )
                    {$("#xfernumber").val(Call_XC_e_Number);   $("#xfername").val('D5');}
                if (quick_transfer_button_locked > 0)
                    {quick_transfer_button_orig = $("#xfernumber").val();}
                
                //$("#QuickXfer").html("<a href=\"#\" onclick=\"mainxfer_send_redirect('XfeRBLIND','" + lastcustchannel + "','" + lastcustserverip + "','','','" + quick_transfer_button_locked + "');return false;\"><img src=\"./images/quicktransfer.png\" style=\"padding-bottom:3px;\" border=\"0\" title=\"Quick Transfer\" alt=\"QUICK TRANSFER\" /></a>");
            }

            //if (custom_3way_button_transfer_enabled > 0)
            //{
            //    $("#CustomXfer").html("<a href=\"#\" onclick=\"custom_button_transfer();return false;\"><img src=\"./images/vdc_LB_customxfer.gif\" border=\"0\" alt=\"Custom Transfer\" /></a>");
            //}

            if (call_requeue_button > 0)
            {
                var CloserSelectChoices = document.vicidial_form.CloserSelectList.value;
                var regCRB = new RegExp("AGENTDIRECT","ig");
                if ( (CloserSelectChoices.match(regCRB)) || (VU_closer_campaigns.match(regCRB)) )
                {
                    //$("#ReQueueCall").html("<a href=\"#\" onclick=\"call_requeue_launch();return false;\"><img src=\"./images/requeuecall.png\" border=\"0\" title=\"Re-Queue Call\" alt=\"Re-Queue Call\" /></a>");
                } else {
                    //$("#ReQueueCall").html("<img src=\"./images/requeuecall_OFF.png\" border=\"0\" title=\"Re-Queue Call\" alt=\"Re-Queue Call\" />");
                }
            }

            // Build transfer pull-down list
            var loop_ct = 0;
            var live_Xfer_HTML = '';
            var Xfer_Select = '';
            while (loop_ct < XFgroupCOUNT)
            {
                if (VARxferGroups[loop_ct] == LIVE_default_xfer_group)
                    {Xfer_Select = 'selected ';}
                else {Xfer_Select = '';}
                live_Xfer_HTML = live_Xfer_HTML + "<option " + Xfer_Select + "value=\"" + VARxferGroups[loop_ct] + "\">" + VARxferGroups[loop_ct] + " - " + VARxferGroupsNames[loop_ct] + "</option>\n";
                loop_ct++;
            }
            //$("#XferGroupList").html("<select size='1' name='XfeRGrouP' class='cust_form' id='XferGroup' onChange='XferAgentSelectLink();return false;'>" + live_Xfer_HTML + "</select>");

            if (lastcustserverip == server_ip)
            {
                //$("#VolumeUpSpan").html("<a onclick=\"volume_control('UP','" + lastcustchannel + "','');return false;\"><img src='./images/vdc_volume_up.gif' border='0' /></a>");
                //$("#VolumeDownSpan").html("<a onclick=\"volume_control('DOWN','" + lastcustchannel + "','');return false;\"><img src='./images/vdc_volume_down.gif' border='0' /></a>");
            }

            if (dial_method == "INBOUND_MAN")
            {
                //$("#DiaLControl").html("<img src=\"./images/pause_OFF.png\" border=\"0\" title=\"Pause\" alt=\" Pause \" /><br /><img src=\"./images/resume_OFF.png\" border=\"0\" title=\"Resume\" alt=\"Resume\" /><small>&nbsp;</small><img src=\"./images/dialnext_OFF.png\" border=\"0\" title=\"Dial Next Number\" alt=\"Dial Next Number\" />");
                //toggleButton('ResumePause', 'pause', false);
            } else {
                //$("#DiaLControl").html(DiaLControl_auto_HTML_OFF);
                //toggleButton('ResumePause', 'pause', false);
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
            //    document.vicidial_form.gender.value = genderValue;
            //}

            LeadDispo = '';

            var regWFAcustom = new RegExp("^VAR","ig");
            if (VDIC_web_form_address.match(regWFAcustom))
            {
                TEMP_VDIC_web_form_address = URLDecode(VDIC_web_form_address,'YES','CUSTOM');
                TEMP_VDIC_web_form_address = TEMP_VDIC_web_form_address.replace(regWFAcustom, '');
            } else {
                TEMP_VDIC_web_form_address = URLDecode(VDIC_web_form_address,'YES','DEFAULT','1');
            }

            if (VDIC_web_form_address_two.match(regWFAcustom))
            {
                TEMP_VDIC_web_form_address_two = URLDecode(VDIC_web_form_address_two,'YES','CUSTOM');
                TEMP_VDIC_web_form_address_two = TEMP_VDIC_web_form_address_two.replace(regWFAcustom, '');
            } else {
                TEMP_VDIC_web_form_address_two = URLDecode(VDIC_web_form_address_two,'YES','DEFAULT','2');
            }


            if (TEMP_VDIC_web_form_address.length > 0)
            {
                //$("#WebFormSpan").html("<a href=\"" + TEMP_VDIC_web_form_address + "\" target=\"" + web_form_target + "\" onMouseOver=\"WebFormRefresH();\"><img src=\"./images/vdc_LB_webform.gif\" border=\"0\" alt=\"Web Form\" /></a>\n");
            }

            if (enable_second_webform > 0)
            {
                //$("#WebFormSpanTwo").html("<a href=\"" + TEMP_VDIC_web_form_address_two + "\" target=\"" + web_form_target + "\" onMouseOver=\"WebFormTwoRefresH();\"><img src=\"./images/vdc_LB_webform_two.gif\" border=\"0\" alt=\"Web Form 2\" /></a>\n");
            }

            if ( (LIVE_campaign_recording == 'ALLCALLS') || (LIVE_campaign_recording == 'ALLFORCE') )
                {all_record = 'YES';}

            if ( (view_scripts == 1) && (Call_Script_ID.length > 0) )
            {
                var SCRIPT_web_form = "http://"+hostURL+"/testing.php";
                var TEMP_SCRIPT_web_form = URLDecode(SCRIPT_web_form,'YES','DEFAULT','1');
                //$("#ScriptButtonSpan").html("<A HREF=\"#\" onClick=\"ScriptPanelToFront();\"><IMG SRC=\"./images/script_tab.png\" ALT=\"SCRIPT\" WIDTH=143 HEIGHT=27 BORDER=0></A>");

                if ( (script_recording_delay > 0) && ( (LIVE_campaign_recording == 'ALLCALLS') || (LIVE_campaign_recording == 'ALLFORCE') ) )
                {
                    delayed_script_load = 'YES';
                    //RefresHScript('CLEAR');
                } else {
                    //load_script_contents();
                }
            }

            if (custom_fields_enabled > 0)
            {
                $("#CustomFormSpan").html("<a href=\"#\" onclick=\"FormPanelToFront();\"><img src=\"./images/custom_form_tab.png\" alt=\"FORM\" width=\"143px\" height=\"27px\" border=\"0\" /></a>");
                //FormContentsLoad();
            }
            // JOEJ 082812 - new for email feature
            if (email_enabled > 0)
            {
                //EmailContentsLoad();
            }
            if (Call_Auto_Launch == 'SCRIPT')
            {
                if (delayed_script_load == 'YES')
                {
                    //load_script_contents();
                }
                //ScriptPanelToFront();
            }
            if (Call_Auto_Launch == 'FORM')
            {
                //FormPanelToFront();
            }
            if (Call_Auto_Launch == 'EMAIL')
            {
                //EmailPanelToFront();
            }

            if (Call_Auto_Launch == 'WEBFORM')
            {
                window.open(TEMP_VDIC_web_form_address, web_form_target, 'toolbar=1,scrollbars=1,location=1,statusbar=1,menubar=1,resizable=1,width=640,height=450');
            }
            if (Call_Auto_Launch == 'WEBFORMTWO')
            {
                window.open(TEMP_VDIC_web_form_address_two, web_form_target, 'toolbar=1,scrollbars=1,location=1,statusbar=1,menubar=1,resizable=1,width=640,height=450');
            }

            if (useIE > 0)
            {
                var regCTC = new RegExp("^NONE","ig");
                if (Copy_to_Clipboard.match(regCTC))
                    {var nothing=1;}
                else
                {
                    var tmp_clip = $(Copy_to_Clipboard);
                    //alert_box("Copy to clipboard SETTING: |" + useIE + "|" + Copy_to_Clipboard + "|" + tmp_clip.value + "|");
                    window.clipboardData.setData('Text', tmp_clip.value)
                    //alert_box("Copy to clipboard: |" + tmp_clip.value + "|" + Copy_to_Clipboard + "|");
                }
            }

            if (alert_enabled=='ON')
            {
                var callnum = dialed_number;
                var dial_display_number = phone_number_format(callnum);
                alert(" Incoming: " + dial_display_number + "\n Group- " + VDIC_data_VDIG[1] + " &nbsp; " + VDIC_fronter);
            }
        } else if (email_enabled>0 && EMAILgroupCOUNT>0 && AutoDialWaiting==1) {
            // JOEJ check for EMAIL
            // QUEUEpadding is needed to allow inbound calls to get through QUEUE status
            QUEUEpadding++;
            if (QUEUEpadding==5) 
            {
                QUEUEpadding=0;
                //check_for_incoming_email();
            }
        }
    });
}

function ManualDialCheckChannel () {
    var dialed_number = $("#MDphone_number").val();
    var CIDcheck = MDnextCID;
    var postData = {
        server_ip: server_ip,
        session_name: session_name,
        ACTION: "manDiaLlookCaLL",
        conf_exten: conf_exten,
        user: uName,
        pass: uPass,
        MDnextCID: CIDcheck,
        agent_log_id: agent_log_id,
        lead_id: $("input[name='lead_id']").val(),
        DiaL_SecondS: MD_ring_seconds,
        stage: ""
    };
    
    $.post(baseURL+'/agent/vdc_db_query.php', postData, function(result) {
        var MDlookResponse_array=result.split("\n");
        var MDlookCID = MDlookResponse_array[0];
        var regMDL = new RegExp("^Local","ig");
        var customer_number = phone_number_format(dialed_number);
        
        if (MDlookCID == "NO") {
            MD_ring_seconds++;
            
            var status_display_content = '';
            if (alt_dial_status_display == 0) {
                if ( /CALLID/.test(status_display_fields) ) {status_display_content += " <?=$lh->translationFor('uid')?>: " + CIDcheck;}
                if ( /LEADID/.test(status_display_fields) ) {status_display_content += " <?=$lh->translationFor('lead_id')?>: " + $("input[name='lead_id']").val();}
                if ( /LISTID/.test(status_display_fields) ) {status_display_content += " <?=$lh->translationFor('list_id')?>: " + $("input[name='list_id']").val();}
                
                $("#MainStatusSpan").html("<?=$lh->translationFor('calling')?>: " + customer_number + " " + status_display_content + " &nbsp; <?=$lh->translationFor('waiting_for_ring')?> " + MD_ring_seconds + " <?=$lh->translationFor('seconds')?>");
            }
        } else {
            MDuniqueid = MDlookResponse_array[0];
            MDchannel = MDlookResponse_array[1];
            var MDalert = MDlookResponse_array[2];
            
            if (MDalert == "ERROR") {
                var MDerrorDesc = MDlookResponse_array[3];
                var MDerrorDescSIP = MDlookResponse_array[4];
                alert("<?=$lh->translationFor('call_rejected')?>: " + MDchannel + "\n" + MDerrorDesc + "\n" + MDerrorDescSIP);
            }
            
            if ( (MDchannel.match(regMDL)) && (asterisk_version != '1.0.8') && (asterisk_version != '1.0.9') ) {
                // bad grab of Local channel, try again
                MD_ring_seconds++;
            } else {
                custchannellive = 1;
                
                $("input[name='uniqueid']").val(MDlookResponse_array[0]);
                $("input[name='callchannel']").val(MDlookResponse_array[1]);
                lastcustchannel = MDlookResponse_array[1];
                
                toggleStatus('LIVE');
                $("div:contains('CALL LENGTH:') > span").html("0");
                $("div:contains('SESSION ID:') > span").html(session_id);
                
                live_customer_call = 1;
                live_call_seconds = 0;
                MD_channel_look = 0;
                var status_display_content = '';
                if ( /CALLID/.test(status_display_fields) ) {status_display_content += " <?=$lh->translationFor('uid')?>: " + CIDcheck;}
                if ( /LEADID/.test(status_display_fields) ) {status_display_content += " <?=$lh->translationFor('lead_id')?>: " + $("input[name='lead_id']").val();}
                if ( /LISTID/.test(status_display_fields) ) {status_display_content += " <?=$lh->translationFor('list_id')?>: " + $("input[name='list_id']").val();}
                
                $("#MainStatusSpan").html("<?=$lh->translationFor('called')?>: " + customer_number + " " + status_display_content);
                
                toggleButton('DialHangup', 'hangup');
                
                lastcustserverip = '';
            }
        }
    });
    
    if ( (MD_ring_seconds > 49) && (MD_ring_seconds > dial_timeout) ) {
        MD_channel_look = 0;
        MD_ring_seconds = 0;
        
        alert("<?=$lh->translationFor('dial_timeout')?>.");
    }
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
    if (header_phone_format === undefined) {
        var header_phone_format = "US_PARN (000)000-0000";
    }

    var regUS_DASHphone = new RegExp("US_DASH","g");
    var regUS_PARNphone = new RegExp("US_PARN","g");
    var regUK_DASHphone = new RegExp("UK_DASH","g");
    var regAU_SPACphone = new RegExp("AU_SPAC","g");
    var regIT_DASHphone = new RegExp("IT_DASH","g");
    var regFR_SPACphone = new RegExp("FR_SPAC","g");
    var status_display_number = formatphone;
    var dispnum = formatphone;
    if (disable_alter_custphone == 'HIDE') {
        var status_display_number = 'XXXXXXXXXX';
        var dispnum = 'XXXXXXXXXX';
    }
    if (header_phone_format.match(regUS_DASHphone)) {
        var status_display_number = dispnum.substring(0,3) + '-' + dispnum.substring(3,6) + '-' + dispnum.substring(6,10);
    }
    if (header_phone_format.match(regUS_PARNphone)) {
        var status_display_number = '(' + dispnum.substring(0,3) + ')' + dispnum.substring(3,6) + '-' + dispnum.substring(6,10);
    }
    if (header_phone_format.match(regUK_DASHphone)) {
        var status_display_number = dispnum.substring(0,2) + ' ' + dispnum.substring(2,6) + '-' + dispnum.substring(6,10);
    }
    if (header_phone_format.match(regAU_SPACphone)) {
        var status_display_number = dispnum.substring(0,3) + ' ' + dispnum.substring(3,6) + ' ' + dispnum.substring(6,9);
    }
    if (header_phone_format.match(regIT_DASHphone)) {
        var status_display_number = dispnum.substring(0,4) + '-' + dispnum.substring(4,7) + '-' + dispnum.substring(8,10);
    }
    if (header_phone_format.match(regFR_SPACphone)) {
        var status_display_number = dispnum.substring(0,2) + ' ' + dispnum.substring(2,4) + ' ' + dispnum.substring(4,6) + ' ' + dispnum.substring(6,8) + ' ' + dispnum.substring(8,10);
    }

    return status_display_number;
};

String.prototype.toUpperFirst = function() {
    return this.charAt(0).toUpperCase() + this.slice(1);
}
<?php
} else {
    if ($_REQUEST['module_name'] == 'GOagent') {
        $campaign = $_REQUEST['campaign_id'];
        $users = \creamy\CreamyUser::currentUser();
        $agent = get_settings('user', $astDB, $users->getUserName());
        
        switch ($_REQUEST['action']) {
            case "login":
                $CIDdate = date("ymdHis");
                $month_old = mktime(11, 0, 0, date("m"), date("d")-2,  date("Y"));
                $past_month_date = date("Y-m-d H:i:s",$month_old);
                $user = $agent->user;
                $VU_user_group = $agent->user_group;
                $phone_login = (isset($_REQUEST['pl'])) ? $_REQUEST['pl'] : $agent->phone_login;
                $phone_pass = (isset($_REQUEST['pp'])) ? $_REQUEST['pp'] : $agent->phone_pass;
                
                $phone_settings = get_settings('phone', $astDB, $phone_login, $phone_pass);
                $campaign_settings = get_settings('campaign', $astDB, $campaign);
                $system_settings = get_settings('system', $astDB);
                
                $astDB->where('server_ip', $phone_settings->server_ip);
                $query = $astDB->getOne('servers', 'asterisk_version');
                $asterisk_version = $query['asterisk_version'];
                
                $extension = $phone_settings->extension;
                if ($phone_settings->protocol == 'EXTERNAL') {
                    $protocol = 'Local';
                    $extension = "{$phone_settings->dialplan_number}@{$phone_settings->ext_context}";
                }
                if (preg_match("/Zap/i",$phone_settings->protocol)) {
                    if (preg_match("/^1\.0|^1\.2|^1\.4\.1|^1\.4\.20|^1\.4\.21/i", $asterisk_version)) {
                        $do_nothing = 1;
                    } else {
                        $protocol = 'DAHDI';
                    }
                }
                
                //$this->session->set_userdata('phone_login', $login);
                //$this->session->set_userdata('phone_pass', $pass);
                //$this->session->set_userdata('protocol', $protocol);
                //$this->session->set_userdata('extension', $extension);
                //$this->session->set_userdata('server_ip', $phone_settings->server_ip);
                
                $SIP_user = "{$protocol}/{$extension}";
                $SIP_user_DiaL = "{$protocol}/{$extension}";
                $qm_extension = "$extension";
                if ( (preg_match('/8300/',$phone_settings->dialplan_number)) and (strlen($phone_settings->dialplan_number)<5) and ($protocol == 'Local') ) {
                    $SIP_user = "{$protocol}/{$extension}{$agent->phone_login}";
                    $qm_extension = "{$extension}{$agent->phone_login}";
                }
                
                $session_ext = preg_replace("/[^a-z0-9]/i", "", $extension);
                if (strlen($session_ext) > 10) {$session_ext = substr($session_ext, 0, 10);}
                $session_rand = (rand(1,9999999) + 10000000);
                $session_name = "$StarTtimE$US$session_ext$session_rand";
                //$this->session->set_userdata('session_name', $session_name);
                //$this->session->set_userdata('SIP_user', $SIP_user);
                
                $astDB->where('start_time', $past_month_date, '<');
                $astDB->where('extension', $extension);
                $astDB->where('server_ip', $phone_settings->server_ip);
                $astDB->where('program', 'vicidial');
                $query = $astDB->delete('web_client_sessions');
                
                $query = $astDB->insert('web_client_sessions', array('extension' => $extension, 'server_ip' => $phone_settings->server_ip, 'program' => 'vicidial', 'start_time' => $NOW_TIME, 'session_name' => $session_name));
                
                $campaign_leads_to_call = 1; // for testing purposes -- chris
                if ( ( ($campaign_allow_inbound == 'Y') and ($dial_method != 'MANUAL') ) || ($campaign_leads_to_call > 0) || (preg_match('/Y/',$no_hopper_leads_logins)) ) {
                    ##### check to see if the user has a conf extension already, this happens if they previously exited uncleanly
                    //$query = $db->query("SELECT conf_exten FROM vicidial_conferences WHERE extension='$SIP_user' AND server_ip = '{$phone_settings->server_ip}' LIMIT 1;");
                    $astDB->where('extension', $SIP_user);
                    $astDB->where('server_ip', $phone_settings->server_ip);
                    $query = $astDB->getOne('vicidial_conferences', 'conf_exten');
                    $prev_login_ct = $astDB->getRowCount();
                    
                    $i=0;
                    while ($i < $prev_login_ct) {
                        $session_id = $query['conf_exten'];
                        $i++;
                    }
                    
                    if ($prev_login_ct > 0) {
                        //var_dump("USING PREVIOUS MEETME ROOM - $session_id - $NOW_TIME - $SIP_user");
                    } else {
                        ##### grab the next available vicidial_conference room and reserve it
                        //$query = $astDB->query("SELECT count(*) FROM vicidial_conferences WHERE server_ip='{$phone_settings->server_ip}' AND ((extension='') OR (extension IS null));");
                        $astDB->where('server_ip', $phone_settings->server_ip);
                        $astDB->where('extension', '');
                        $astDB->orWhere('extension', null);
                        $query = $astDB->get('vicidial_conferences');
                        if ($astDB->getRowCount() > 0) {
                            $query = $astDB->rawQuery("UPDATE vicidial_conferences SET extension='$SIP_user', leave_3way='0' WHERE server_ip='{$phone_settings->server_ip}' AND ((extension='') OR (extension=null))", 1);

                        var_dump($query);
                            $astDB->where('server_ip', $phone_settings->server_ip);
                            $astDB->where('extension', $SIP_user);
                            $astDB->orWhere('extension', $user);
                            $query = $astDB->getOne('vicidial_conferences', 'conf_exten');
                            $session_id = $query['conf_exten'];
                        }
                        
                        //var_dump("USING NEW MEETME ROOM - $session_id - $NOW_TIME - $SIP_user");
                    }
                    
                    //$this->session->set_userdata('conf_exten', $session_id);
                    
                    ##### clearing records from vicidial_live_agents and vicidial_live_inbound_agents
                    $astDB->where('user', $user);
                    $query = $astDB->delete('vicidial_live_agents');
                    $astDB->where('user', $user);
                    $query = $astDB->delete('vicidial_live_inbound_agents');
                                
                    ##### insert a NEW record to the vicidial_manager table to be processed
                    $SIqueryCID = "S{$CIDdate}{$session_id}";
                    $TEMP_SIP_user_DiaL = $SIP_user_DiaL;
                    if ($phone_settings->on_hook_agent == 'Y')
                        {$TEMP_SIP_user_DiaL = 'Local/8300@default';}
                    $agent_login_data = "||$NOW_TIME|NEW|N|{$phone_settings->server_ip}||Originate|$SIqueryCID|Channel: $TEMP_SIP_user_DiaL|Context: {$phone_settings->ext_context}|Exten: $session_id|Priority: 1|Callerid: $SIqueryCID|||||";
                    $insertData = array(
                        'man_id' => '',
                        'uniqueid' => '',
                        'entry_date' => $NOW_TIME,
                        'status' => 'NEW',
                        'response' => 'N',
                        'server_ip' => $phone_settings->server_ip,
                        'channel' => '',
                        'action' => 'Originate',
                        'callerid' => $SIqueryCID,
                        'cmd_line_b' => "Channel: $TEMP_SIP_user_DiaL",
                        'cmd_line_c' => "Context: {$phone_settings->ext_context}",
                        'cmd_line_d' => "Exten: $session_id",
                        'cmd_line_e' => 'Priority: 1',
                        'cmd_line_f' => "Callerid: \"$SIqueryCID\" <{$campaign_settings->campaign_cid}>",
                        'cmd_line_g' => '',
                        'cmd_line_h' => '',
                        'cmd_line_i' => '',
                        'cmd_line_j' => '',
                        'cmd_line_k' => ''
                    );
                    $query = $astDB->insert('vicidial_manager', $insertData);
                    
                    $WebPhonEurl = '';
                    $astDB->where('user', $user);
                    $query = $astDB->delete('vicidial_session_data');
                    
                    $query = $astDB->insert('vicidial_session_data', array('session_name' => $session_name, 'user' => $user, 'campaign_id' => $campaign, 'server_ip' => $phone_settings->server_ip, 'conf_exten' => $session_id, 'extension' => $extension, 'login_time' => $NOW_TIME, 'webphone_url' => $WebPhonEurl, 'agent_login_call' => $agent_login_data));
                    
                    $astDB->where('user', $user);
                    $astDB->where('campaign_id', $campaign);
                    $query = $astDB->getOne('vicidial_campaign_agents', 'campaign_weight,calls_today,campaign_grade');
                    
                    if ($astDB->getRowCount() > 0) {
                        $campaign_weight = $query['campaign_weight'];
                        $calls_today = $query['calls_today'];
                        $campaign_grade = $query['campaign_grade'];
                    } else {
                        $campaign_weight = '0';
                        $calls_today = '0';
                        $campaign_grade = '1';
                        
                        $insertData = array(
                            'user' => $user,
                            'campaign_id' => $campaign,
                            'campaign_rank' => '0',
                            'campaign_weight' => '0',
                            'calls_today' => $calls_today,
                            'campaign_grade' => $campaign_grade
                        );
                        $query = $astDB->insert('vicidial_campaign_agents', $insertData);
                    }
                    
                    if ($campaign_settings->auto_dial_level > 0) {
                        $outbound_autodial = 'Y';
                    } else {
                        $outbound_autodial = 'N';
                    }
                    
                    $random = (rand(1000000, 9999999) + 10000000);
                    $query = $astDB->rawQuery("INSERT INTO vicidial_live_agents (user,server_ip,conf_exten,extension,status,lead_id,campaign_id,closer_campaigns,uniqueid,callerid,channel,random_id,last_call_time,last_update_time,last_call_finish,user_level,campaign_weight,calls_today,last_state_change,outbound_autodial,manager_ingroup_set,on_hook_ring_time,on_hook_agent,last_inbound_call_time,last_inbound_call_finish,campaign_grade) values('$user','{$phone_settings->server_ip}','$session_id','$SIP_user','PAUSED','','$campaign','','','','','$random','$NOW_TIME','$tsNOW_TIME','$NOW_TIME','{$user_settings->user_level}', '$campaign_weight', '$calls_today','$NOW_TIME','$outbound_autodial','N','{$phone_settings->phone_ring_timeout}','{$phone_settings->on_hook_agent}','$NOW_TIME','$NOW_TIME','$campaign_grade')");
                            
                    $query = $astDB->rawQuery("INSERT INTO vicidial_agent_log (user,server_ip,event_time,campaign_id,pause_epoch,pause_sec,wait_epoch,user_group,sub_status) values('$user','{$phone_settings->server_ip}','$NOW_TIME','$campaign','$StarTtimE','0','$StarTtimE','{$user_settings->user_group}','LOGIN')");
                    $agent_log_id = $astDB->getInsertId();
                    
                    //$query = $db->query("UPDATE vicidial_campaigns SET campaign_logindate='$NOW_TIME' WHERE campaign_id='$campaign';");
                    $astDB->where('campaign_id', $campaign);
                    $query = $astDB->update('vicidial_campaigns', array('campaign_logindate' => $NOW_TIME));
                    
                    //$query = $db->query("UPDATE vicidial_live_agents SET agent_log_id='$agent_log_id' where user='$user';");
                    $astDB->where('user', $user);
                    $query = $astDB->update('vicidial_live_agents', array('agent_log_id' => $agent_log_id));
                    
                    //$query = $db->query("UPDATE vicidial_users SET shift_override_flag='0' where user='$user' and shift_override_flag='1';");
                    $astDB->where('user', $user);
                    $astDB->where('shift_override_flag', '1');
                    $query = $astDB->update('vicidial_users', array('shift_override_flag' => '0'));
                    
                    $closer_campaigns = '';
                    //$query = $db->query("UPDATE vicidial_live_agents SET closer_campaigns='$closer_campaigns' WHERE user='$user' AND server_ip='{$phone_settings->server_ip}';");
                    $astDB->where('user', $user);
                    $astDB->where('server_ip', $phone_settings->server_ip);
                    $query = $astDB->update('vicidial_live_agents', array('closer_campaigns' => $closer_campaigns));
                }
                
                $VARCBstatusesLIST = '';
                ##### grab the statuses that can be used for dispositioning by an agent
                $astDB->where('selectable', 'Y');
                $astDB->orderBy('status');
                $query = $astDB->get('vicidial_statuses', 500, 'status,status_name,scheduled_callback');
                $statuses_ct = $astDB->getRowCount();
                foreach ($query as $row) {
                    $status = $row['status'];
                    $status_name = $row['status_name'];
                    $scheduled_callback = $row['scheduled_callback'];
                    $statuses[$status] = "{$status_name}";
                    if ($scheduled_callback == 'Y')
                        {$VARCBstatusesLIST .= " {$status}";}
                }
                
                ##### grab the campaign-specific statuses that can be used for dispositioning by an agent
                $astDB->where('selectable', 'Y');
                $astDB->where('campaign_id', $campaign);
                $astDB->orderBy('status');
                $query = $astDB->get('vicidial_campaign_statuses', 500, 'status,status_name,scheduled_callback');
                $statuses_camp_ct = $astDB->getRowCount();
                foreach ($query as $row) {
                    $status = $row['status'];
                    $status_name = $row['status_name'];
                    $scheduled_callback = $row['scheduled_callback'];
                    $statuses[$status] = "{$status_name}";
                    if ($scheduled_callback == 'Y')
                        {$VARCBstatusesLIST .= " {$status}";}
                }
                $statuses_ct = ($statuses_ct + $statuses_camp_ct);
                $VARCBstatusesLIST .= " ";
                
                $xfer_groups = preg_replace("/^ | -$/", "", $campaign_settings->xfer_groups);
                $xfer_groups = explode(" ", $xfer_groups);
                //$xfer_groups = preg_replace("/ /", "','", $xfer_groups);
                //$xfer_groups = "'$xfer_groups'";
                $XFgrpCT = 0;
                $VARxferGroups = "''";
                $VARxferGroupsNames = '';
                $default_xfer_group_name = '';
                if ($campaign_settings->allow_closers == 'Y') {
                    $VARxferGroups = '';
                    $astDB->where('active', 'Y');
                    $astDB->where('group_id', $xfer_groups, 'IN');
                    $astDB->orderBy('group_id');
                    $result = $astDB->get('vicidial_inbound_groups', 800, 'group_id,group_name');
                    //$result = $astDB->query("SELECT group_id,group_name FROM vicidial_inbound_groups WHERE active = 'Y' AND group_id IN($xfer_groups) ORDER BY group_id LIMIT 800;");
                    $xfer_ct = $astDB->getRowCount();
                    $XFgrpCT = 0;
                    while ($XFgrpCT < $xfer_ct) {
                        $row = $result[$XFgrpCT];
                        $VARxferGroups = "{$VARxferGroups}'{$row['group_id']}',";
                        $VARxferGroupsNames = "{$VARxferGroupsNames}'{$row['group_name']}',";
                        if ($row['group_id'] == "{$campaign_settings->default_xfer_group}") {$default_xfer_group_name = $row['group_name'];}
                        $XFgrpCT++;
                    }
                    $VARxferGroups = substr("$VARxferGroups", 0, -1); 
                    $VARxferGroupsNames = substr("$VARxferGroupsNames", 0, -1); 
                }
                
                $return = array(
                    'user' => $user,
                    'agent_log_id' => $agent_log_id,
                    'start_time' => $StarTtimE,
                    'now_time' => $NOW_TIME,
                    'timestamp' => $tsNOW_TIME,
                    'protocol' => $protocol,
                    'extension' => $extension,
                    'conf_exten' => $session_id,
                    'session_id' => $session_id,
                    'session_name' => $session_name,
                    'server_ip' => $phone_settings->server_ip,
                    'asterisk_version' => $asterisk_version,
                    'SIP' => $SIP_user,
                    'qm_extension' => $qm_extension,
                    'user_abb' => $user_abb,
                    'statuses_ct' => $statuses_ct,
                    'statuses' => $statuses,
                    'VARCBstatusesLIST' => $VARCBstatusesLIST,
                    'XFgroupCOUNT' => $XFgrpCT,
                    'VARxferGroups' => $VARxferGroups,
                    'VARxferGroupsNames' => $VARxferGroupsNames,
                    'Copy_to_Clipboard' => $campaign_settings->agent_clipboard_copy,
                    'Call_XC_a_DTMF' => $campaign_settings->xferconf_a_dtmf,
                    'Call_XC_a_Number' => $campaign_settings->xferconf_a_number,
                    'Call_XC_b_DTMF' => $campaign_settings->xferconf_b_dtmf,
                    'Call_XC_b_Number' => $campaign_settings->xferconf_b_number,
                    'Call_XC_c_Number' => $campaign_settings->xferconf_c_number,
                    'Call_XC_d_Number' => $campaign_settings->xferconf_d_number,
                    'Call_XC_e_Number' => $campaign_settings->xferconf_e_number,
                    'default_xfer_group_name' => $default_xfer_group_name,
                    'camp_settings' => $campaign_settings,
                );
                $return = json_encode($return);
                //echo "User: {$user}|StartTime: {$StarTtimE}|Now: {$NOW_TIME}|TimeStamp: {$tsNOW_TIME}|Protocol: {$protocol}|Extension: {$extension}|ServerIP: {$phone_settings->server_ip}|{$session_name}|SIP: {$SIP_user}|SessionID: {$session_id}";
                echo $return;
                break;

            case "logout":
                $NOW_TIME = date("Y-m-d H:i:s");
                $StarTtime = date("U");
                $user = $agent->user;
                $user_group = $agent->user_group;
                $phone_login = (isset($_REQUEST['pl'])) ? $_REQUEST['pl'] : $agent->phone_login;
                $phone_pass = (isset($_REQUEST['pp'])) ? $_REQUEST['pp'] : $agent->phone_pass;
            
                $phone_settings = get_settings('phone', $astDB, $phone_login, $phone_pass);
                $campaign_settings = get_settings('campaign', $astDB, $campaign);
                
                $astDB->where('server_ip', $phone_settings->server_ip);
                $query = $astDB->getOne('servers', 'asterisk_version');
                $asterisk_version = $query['asterisk_version'];
                
                $extension = $phone_settings->extension;
                if ($phone_settings->protocol == 'EXTERNAL')
                {
                    $protocol = 'Local';
                    $extension = "{$phone_settings->dialplan_number}@{$phone_settings->ext_context}";
                }
                if (preg_match("/Zap/i",$phone_settings->protocol))
                {
                    if (preg_match("/^1\.0|^1\.2|^1\.4\.1|^1\.4\.20|^1\.4\.21/i", $asterisk_version))
                    {
                        $do_nothing = 1;
                    } else {
                        $protocol = 'DAHDI';
                    }
                }
                
                $server_ip = $phone_settings->server_ip;
                
                $astDB->where('server_ip', $server_ip);
                $astDB->where('channel', "$protocol/$extension%", 'like');
                $astDB->orderBy('channel');
                $query = $astDB->getOne('live_sip_channels', 'channel');
                //$query = $db->query("SELECT channel FROM live_sip_channels where server_ip = '$server_ip' and channel LIKE \"$protocol/$extension%\" order by channel desc;");
                $agent_channel = '';
                if ($astDB->getRowCount() > 0) {
                    $agent_channel = $query['channel'];
                    $query = $astDB->rawQuery("INSERT INTO vicidial_manager values('','','$NOW_TIME','NEW','N','$server_ip','','Hangup','ULGH3459$StarTtime','Channel: $agent_channel','','','','','','','','','')");
                }
                
                ##### check to see if the user has a conf extension already, this happens if they previously exited uncleanly
                $SIP_user = "{$protocol}/{$extension}";
                if ( (preg_match('/8300/', $phone_settings->dialplan_number)) and (strlen($phone_settings->dialplan_number)<5) and ($protocol == 'Local') ) {
                    $SIP_user = "{$protocol}/{$extension}{$login}";
                }
                
                $astDB->where('extension', $SIP_user);
                $astDB->where('server_ip', $server_ip);
                $query = $astDB->getOne('vicidial_conferences', 'conf_exten');
                $prev_login_ct = $astDB->getRowCount();
                
                $i=0;
                while ($i < $prev_login_ct) {
                    $session_id = $query['conf_exten'];
                    $i++;
                }
                
                if (strlen($session_id) > 0) {
                    ##### insert an entry on vicidial_user_log
                    $query = $astDB->rawQuery("INSERT INTO vicidial_user_log (user,event,campaign_id,event_date,event_epoch,user_group) values('$user','LOGOUT','$campaign','$NOW_TIME','$StarTtime','$user_group')");
                            
                    sleep(1);
                    
                    $astDB->where('server_ip', $server_ip);
                    $astDB->where('user', $user);
                    $query = $astDB->delete('vicidial_live_agents');
                    
                    $astDB->where('user', $user);
                    $query = $astDB->delete('vicidial_live_inbound_agents');
                    
                    $channel = $agent_channel;
                    $local_DEF = 'Local/5555';
                    $conf_exten = $session_id;
                    $local_AMP = '@';
                    $ext_context = 'default';
                    $kick_local_channel = "$local_DEF$conf_exten$local_AMP$ext_context";
                    $queryCID = "ULGH3458$StarTtime";
                    
                    $query = $astDB->rawQuery("INSERT INTO vicidial_manager values('','','$NOW_TIME','NEW','N','$server_ip','','Originate','$queryCID','Channel: $kick_local_channel','Context: $ext_context','Exten: 8300','Priority: 1','Callerid: $queryCID','','','','$channel','$conf_exten')");
                    
                    $return = "SUCCESS: {$user} has logged out.";
                } else {
                    $return = "ERROR: {$user} not logged in.";
                }
                
                header("Cache-Control: no-store, no-cache, must-revalidate");
                header("Cache-Control: post-check=0, pre-check=0");
                header("Pragma: no-cache");
                
                echo $return;
                break;
        }
    } else {
        echo "ERROR: Module '{$_REQUEST['module_name']}' not found.";
    }
}

function get_settings($type=null, $dbase, $param1=null, $param2=null) {
    switch ($type) {
        case "user":
            //User Settings
            $dbase->where('user', $param1);
            $return = $dbase->getOne('vicidial_users');
            break;
        
        case "campaign":
            //Campaign Settings
            $dbase->where('campaign_id', $param1);
            $return = $dbase->getOne('vicidial_campaigns');
            break;
        
        case "hotkeys":
            //Campaign HotKeys
            $dbase->where('campaign_id', $param1);
            $dbase->orderBy('hotkey', 'asc');
            $return = $dbase->get('vicidial_campaign_hotkeys');
            break;
        
        case "phone":
            //Phone Settings
            $dbase->where('login', $param1);
            $dbase->where('pass', $param2);
            $dbase->where('active', 'Y');
            $return = $dbase->getOne('phones');
            break;
        
        case "usergroup":
            //User Group Settings
            $dbase->where('user_group', $param1);
            $return = $dbase->getOne('vicidial_user_groups');
            break;
        
        default:
            //System Settings
            $return = $dbase->getOne('system_settings');
    }
    
    return json_decode(json_encode($return), FALSE);
}
?>