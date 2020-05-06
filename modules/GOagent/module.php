<?php
 /**
 * @file 		module.php
 * @brief 		Agent UI Module
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

require_once(CRM_MODULE_INCLUDE_DIRECTORY.'Module.php');
require_once(CRM_MODULE_INCLUDE_DIRECTORY.'CRMDefaults.php');
require_once(CRM_MODULE_INCLUDE_DIRECTORY.'LanguageHandler.php');
include(CRM_MODULE_INCLUDE_DIRECTORY.'Session.php');
require_once(CRM_MODULE_INCLUDE_DIRECTORY.'goCRMAPISettings.php');

$baseURL = (!empty($_SERVER['HTTPS'])) ? "https://".$_SERVER['SERVER_NAME'] : "http://".$_SERVER['SERVER_NAME'];
$getSlashes = preg_match_all("/\//", $_SERVER['REQUEST_URI']);
$baseDIR = (!empty($_SERVER['REQUEST_URI']) && $getSlashes > 1) ? dirname($_SERVER['REQUEST_URI'])."/" : "/";
define(__NAMESPACE__ . '\GO_MODULE_DIR', $baseURL.$baseDIR.'modules'.DIRECTORY_SEPARATOR.'GOagent'.DIRECTORY_SEPARATOR);

/**
 * This module is an example of how to write a module for Creamy.
 * It will show a message of the day (message of the day).
 */
class GOagent extends Module {
	protected $userrole;
	protected $is_logged_in;
	protected $astDB;

	// module meta-data (ModuleData interface implementation).
	static function getModuleName() { return "GOautodial Agent Dialer"; }
	
	static function getModuleVersion() { return "1.0"; }
	
	static function getModuleDescription() { return "A module for GOautodial Agent Dialer integration."; }

	// lifecycle and respond to interactions.
	public function uponInit() {
		error_log("Module \"GOautodial Agent Dialer\" initializing...");
		
		// add the translation files to our language handler.
		$customLanguageFile = $this->getModuleLanguageFileForLocale($this->lh()->getLanguageHandlerLocale());
		if (!isset($customLanguageFile)) { $customLanguageFile = $this->getModuleLanguageFileForLocale(CRM_LANGUAGE_DEFAULT_LOCALE); }
		$this->lh()->addCustomTranslationsFromFile($customLanguageFile);
		
		$this->astDB = \creamy\DatabaseConnectorFactory::getInstance()->getDatabaseConnectorOfType(CRM_DB_CONNECTOR_TYPE_MYSQL, null, DB_NAME_ASTERISK);
		$this->goDB = \creamy\DatabaseConnectorFactory::getInstance()->getDatabaseConnectorOfType(CRM_DB_CONNECTOR_TYPE_MYSQL, null, DB_NAME);

		$this->userrole = \creamy\CreamyUser::currentUser()->getUserRole();
		$this->userName = \creamy\CreamyUser::currentUser()->getUserName();

		if ($this->userrole > 1) {
			$_SESSION['is_logged_in'] = $this->checkIfLoggedOnPhone();
			
			$this->goDB->where('setting', 'GO_agent_sip_server');
			$rslt = $this->goDB->getOne('settings', 'value');
			$_SESSION['SIPserver'] = (strlen($rslt['value']) > 0) ? $rslt['value'] : 'kamailio';

			echo $this->getGOagentContent();
		} else {
			if (count($_POST) < 1) {
				echo $this->getGOadminContent();
			}
		}
	}
		
	public function uponActivation() {
		error_log("Module \"GOautodial Agent Dialer\" activating...");
	}
		
	public function uponDeactivation() {
		error_log("Module \"GOautodial Agent Dialer\" deactivating...");
	}

	public function uponUninstall() {
		error_log("Module \"GOautodial Agent Dialer\" uninstalling...");
	}
	
	// Private functions for this module.
	private function dateIsToday($date) {
		 $current = strtotime(date("Y-m-d"));
		
		 $datediff = $date - $current;
		 $differance = floor($datediff/(60*60*24));
		 if ($differance == 0) return true;
		 return false;
	}

	private function checkIfLoggedOnPhone() {
		$this->is_logged_in = (isset($_SESSION['is_logged_in'])) ? $_SESSION['is_logged_in'] : false;
		return $this->is_logged_in;
	}
	
	// views and code generation
	/** We return true here to indicate that we want access to the database */
	public function needsDatabaseFunctionality() { return false; }

	public function mainPageViewContent($args) {
		return false;
	}

	public function mainPageViewTitle() {
		return $this->lh()->translationFor("GO_title");
	}
	
	public function mainPageViewSubtitle() {
		return $this->lh()->translationFor("GO_subtitle");
	}
	
	public function mainPageViewIcon() {
		return 'phone-square';
	}

	private function getGOagentContent() {
		$custInfoTitle = $this->lh()->translationFor("customer_information");
		$selectACampaign = $this->lh()->translationFor("select_a_campaign");
		$dispositionCall = $this->lh()->translationFor("disposition_call");
		$endOfCallDispositionSelection = $this->lh()->translationFor("end_of_call_disposition_selection");
		$manualDialLead = $this->lh()->translationFor("manual_dial_lead");
		$availableCampaigns = $this->lh()->translationFor("available_campaigns");
		$inboundGroups = $this->lh()->translationFor("inbound_groups");
		$groupsNotSelected = $this->lh()->translationFor("groups_not_selected");
		$selectedGroups = $this->lh()->translationFor("selected_groups");
		$blendedCalling = $this->lh()->translationFor("blended_calling");
		$outboundActivated = $this->lh()->translationFor("outbound_activated");
		$selectAll = $this->lh()->translationFor("select_all");
		$submit = $this->lh()->translationFor("submit");
		$note = $this->lh()->translationFor("note");
		$phoneNumber = $this->lh()->translationFor("phone_number");
		$dialCode = $this->lh()->translationFor("dial_code");
		$dialCodeInfo = $this->lh()->translationFor("dial_code_info");
		$digitsOnly = $this->lh()->translationFor("digits_only");
		$searchExistingLeads = $this->lh()->translationFor("search_existing_leads");
		$searchExistingLeadsInfo = $this->lh()->translationFor("search_existing_leads_info");
		$dialOverride = $this->lh()->translationFor("dial_override");
		$dialOverrideInfo = $this->lh()->translationFor("dial_override_info");
		$digitsOnlyPlease = $this->lh()->translationFor("digits_only_please");
		$dialNow = $this->lh()->translationFor("dial_now");
		$previewCall = $this->lh()->translationFor("preview_call");
		$goBack = $this->lh()->translationFor("go_back");
		$pauseAgent = $this->lh()->translationFor("pause_agent");
		$pauseAgentXS = $this->lh()->translationFor("pause");
		$transferConference = $this->lh()->translationFor("transfer_conference_functions");
		$callbackDateSelection = $this->lh()->translationFor("callback_datepicker");
		$selectPauseCode = $this->lh()->translationFor("select_pause_code");
		$pauseCodeSelection = $this->lh()->translationFor("pause_code_selection");
		$selectGroupsToSendCalls = $this->lh()->translationFor("select_group_to_send_calls");
		$contactAdmin = $this->lh()->translationFor('contact_admin');
		$customerInformation = $this->lh()->translationFor('contact_information');
		$convertToCustomer = $this->lh()->translationFor('convert_to_customer');
		$youTurnOnMic = $this->lh()->translationFor('you_have_turn_on_mic');
		$youTurnOffMic = $this->lh()->translationFor('you_have_turn_off_mic');
		$phoneIsRegistered = $this->lh()->translationFor('phone_is_now_registered');
		$registrationFailed = $this->lh()->translationFor('registration_failed_refresh');
		$transferSelection = $this->lh()->translationFor('transfer_selection');
		$closerGroups = $this->lh()->translationFor('closer_groups');
		$localCloser = $this->lh()->translationFor('local_closer');
		$seconds = $this->lh()->translationFor('seconds');
		$channel = $this->lh()->translationFor('channel');
		$consultative = $this->lh()->translationFor('consultative');
		$numberToDial = $this->lh()->translationFor('number_to_dial');
		$send = $this->lh()->translationFor('send');
		$sendDTMF = $this->lh()->translationFor('send_dtmf');
		$hangupXferLine = $this->lh()->translationFor('hangup_xfer_line');
		$hangupBothLine = $this->lh()->translationFor('hangup_both_line');
		$leave3wayCall = $this->lh()->translationFor('leave_3way_call');
		$blindTransfer = $this->lh()->translationFor('blind_transfer');
		$dialWithCustomer = $this->lh()->translationFor('dial_with_customer');
		$parkCustomerDial = $this->lh()->translationFor('park_customer_dial');
		$blindTransferVM = $this->lh()->translationFor('blind_transfer_vmail');
		$selectedDateAndTime = $this->lh()->translationFor('selected_date_time');
		$comments = $this->lh()->translationFor('comments');
		$myCallbackOnly = $this->lh()->translationFor('my_callback_only');
		$save = $this->lh()->translationFor('save');
		$close = $this->lh()->translationFor('close');
		$maximize = $this->lh()->translationFor('maximize');
		$minimize = $this->lh()->translationFor('minimize');
		$missedCallbacks = $this->lh()->translationFor('missed_callbacks');
		$selectByDragging = preg_replace('/(\w*'. $selectAll .'\w*)/i', '<b>$1</b>', $this->lh()->translationFor("select_by_dragging"));
		$goModuleDIR = GO_MODULE_DIR;
		$userrole = $this->userrole;
		$_SESSION['module_dir'] = $goModuleDIR;
		$_SESSION['campaign_id'] = (strlen($_SESSION['campaign_id']) > 0) ? $_SESSION['campaign_id'] : '';
		
		//$webProtocol = (preg_match("/Windows/", $_SERVER['HTTP_USER_AGENT'])) ? "wss" : "ws";
		$webProtocol = (strlen($_SERVER['HTTPS']) > 0) ? "wss" : "ws";
		
		$this->goDB->where('setting', 'GO_agent_use_wss');
		$rslt = $this->goDB->getOne('settings', 'value');
		$useWebRTC = (strlen($rslt['value']) > 0) ? $rslt['value'] : 0;
		$_SESSION['use_webrtc'] = $useWebRTC;
		
		if ($useWebRTC) {
			$this->goDB->where('setting', 'GO_agent_wss');
			$rslt = $this->goDB->getOne('settings', 'value');
			$websocketURL = (strlen($rslt['value']) > 0) ? $rslt['value'] : "webrtc.goautodial.com";
			
			$this->goDB->where('setting', 'GO_agent_wss_port');
			$rslt = $this->goDB->getOne('settings', 'value');
			$websocketPORT = (strlen($rslt['value']) > 0) ? $rslt['value'] : "10443";
			
			$this->goDB->where('setting', 'GO_agent_wss_sip');
			$rslt = $this->goDB->getOne('settings', 'value');
			$websocketSIP = (strlen($rslt['value']) > 0) ? "{$rslt['value']}" : "'+server_ip";
			
			$this->goDB->where('setting', 'GO_agent_wss_sip_port');
			$rslt = $this->goDB->getOne('settings', 'value');
			$websocketSIPPort = "";
			if (!preg_match("/server_ip/", $websocketSIP)) {
				if (strlen($rslt['value']) > 0 && $rslt['value'] > 0 && $rslt['value'] != 5060) {
					$websocketSIPPort = ":{$rslt['value']}'";
				} else {
					$websocketSIPPort = "'";
				}
			}
			
			$this->goDB->where('setting', 'GO_agent_domain');
			$rslt = $this->goDB->getOne('settings', 'value');
			$domain = (strlen($rslt['value']) > 0) ? $rslt['value'] : "goautodial.com";
		}
		
		$labels = $this->getLabels()->labels;
		$disable_alter_custphone = $this->getLabels()->disable_alter_custphone;
		$labelHTML = '';
		foreach ($labels as $key => $value) {
			$key = str_replace("label_", "", $key);
			if (!preg_match("/---HIDE---/i", $value)) {
				if (strlen($value) < 1) {
					$value = ucwords(str_replace("_", " ", $key));
				}
				if ($key == "comments") {
					$labelHTML .= "<tr>\n";
					$labelHTML .= "<td align='right' valign='top' width='200' nowrap style='padding-right: 10px;'>$value:<br style='display:none;'><span id='viewcommentsdisplay' style='display:none;'><input type='button' id='ViewCommentButton' onClick=\"ViewComments('ON')\" value='-History-'/></span> </td><td><textarea rows='5' cols='50' id='formMain_$key' name='$key' class='cust_form_text' value='' style='resize:none;'></textarea></td>\n";
					$labelHTML .= "</tr>\n";
				} else if ($key == "gender") {
					$labelHTML .= "<tr>\n";
					$labelHTML .= "<td align='right' width='200' nowrap style='padding-right: 10px;'>$value:</td><td><span id='GENDERhideFORie'><select size='1' name='$key' class='cust_form' id='formMain_$key'><option value='U'>U - Undefined</option><option value='M'>M - Male</option><option value='F'>F - Female</option></select></span></td>\n";
					$labelHTML .= "</tr>\n";
				} else if ($key == "phone_number") {
					if ( preg_match('/Y|HIDE/', $disable_alter_custphone) ) {
						$labelHTML .= "<tr>\n";
						$labelHTML .= "<td align='right' width='200' nowrap style='padding-right: 10px;'>$value:</td><td><span id='phone_numberDISP' style='line-height: 30px;'> &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; </span>";
						$labelHTML .= "<input type='hidden' name='$key' id='formMain_$key' value='' /></td>\n";
						$labelHTML .= "</tr>\n";
					} else {
						$labelHTML .= "<tr>\n";
						$labelHTML .= "<td align='right' width='200' nowrap style='padding-right: 10px;'>$value:</td><td><input type='text' size='20' name='$key' id='formMain_$key' maxlength='16' class='cust_form' value='' /></td>\n";
						$labelHTML .= "</tr>\n";
					}
				} else {
					switch ($key) {
						case "title":
							$size = "4";
							$maxlength = "4";
							break;
						case "middle_initial":
							$size = "1";
							$maxlength = "1";
							break;
						case "email":
							$size = "45";
							$maxlength = "70";
							break;
						case "state":
							$size = "4";
							$maxlength = "2";
							break;
						case "postal_code":
							$size = "14";
							$maxlength = "10";
							break;
						case "vendor_lead_code":
							$size = "15";
							$maxlength = "20";
							break;
						case "phone_code":
							$size = "4";
							$maxlength = "10";
							break;
						case "alt_phone":
							$size = "20";
							$maxlength = "12";
							break;
						case "security_phrase":
							$size = "20";
							$maxlength = "100";
							break;
						case "address1":
							$size = "50";
							$maxlength = "100";
							break;
						case "address2":
							$size = "50";
							$maxlength = "100";
							break;
						case "address3":
							$size = "50";
							$maxlength = "100";
							break;
						case "city":
							$size = "20";
							$maxlength = "50";
							break;
						case "province":
							$size = "20";
							$maxlength = "50";
							break;
						default:
							$size = "20";
							$maxlength = "30";
					}
					
					$convert_dial_code = 0;
					if ($convert_dial_code && $key == "phone_code") {
						$labelHTML .= "<tr>\n";
						$labelHTML .= "<td align='right' width='200' nowrap style='padding-right: 10px;'>$value:</td><td><span id='converted_dial_code'></span><input type='hidden' size='$size' maxlength='$maxlength' id='formMain_$key' name='$key' class='cust_form' value='' /></td>\n";
						$labelHTML .= "</tr>\n";
					} else {
						$labelHTML .= "<tr>\n";
						$labelHTML .= "<td align='right' width='200' nowrap style='padding-right: 10px;'>$value:</td><td><input type='text' size='$size' maxlength='$maxlength' id='formMain_$key' name='$key' class='cust_form' value='' /></td>\n";
						$labelHTML .= "</tr>\n";
					}
				}
			} else {
				$additionalDISP = '';
				if ($key == "phone_number") { $additionalDISP = "<span id='phone_numberDISP' style='display:none;'></span>"; }
				if ($key == "phone_code") { $additionalDISP = "<span id='converted_dial_code' style='display:none;'></span>"; }
				$labelHTML .= "<tr style='display:none;' width='200' nowrap style='padding-right: 10px;'>\n";
				$labelHTML .= "<td align='right'>$value:</td><td>$additionalDISP<input type='hidden' id='formMain_$key' name='$key' value='' />";
				if ($key == "gender")
					{$labelHTML .= "<span id='GENDERhideFORie' style='display:none;'><select size='1' name='$key' class='cust_form' id='formMain_$key'><option value='U'>U - Undefined</option><option value='M'>M - Male</option><option value='F'>F - Female</option></select></span>";}
				$labelHTML .= "</td>\n";
				$labelHTML .= "</tr>\n";
			}
		}
		
		###### Removed the DTMF HotKeys
		//$(document).on('keydown', function(event) {
		//	var keys = {
		//		48: '0', 49: '1', 50: '2', 51: '3', 52: '4', 53: '5', 54: '6', 55: '7', 56: '8', 57: '9'
		//	};
		//	
		//	if (keys[event.which] === undefined) {
		//		return;
		//	}
		//	
		//	console.log('keydown: '+keys[event.which], event);
		//	var options = {
		//		'duration': 160,
		//		'eventHandlers': {
		//			'succeeded': function(originator, response) {
		//				console.log('DTMF succeeded', originator, response);
		//			},
		//			'failed': function(originator, response, cause) {
		//				console.log('DTMF failed', originator, response, cause);
		//			},
		//		}
		//	};
		//	
		//	if (live_customer_call) {
		//		session.sendDTMF(keys[event.which], options);
		//	}
		//});
		
		$str  = <<<EOF
		<link type='text/css' rel='stylesheet' href='{$goModuleDIR}css/style.css'></link>
					<script type='text/javascript' src='{$goModuleDIR}GOagentJS.php'></script>
					<script type='text/javascript' src='{$goModuleDIR}js/addons.js'></script>
					
EOF;

		if ($useWebRTC) {
			$display_name = $_SESSION['user'];
			$phone_login = $_SESSION['phone_login'];
			$socketParams = "password: phone_pass,";
			if ($_SESSION['bcrypt'] > 0) {
				$ha1_pass = $_SESSION['ha1'];
				$realm = $_SESSION['realm'];
				$socketParams = "ha1: '$ha1_pass', realm: '$realm',";
			}
			$str .= <<<EOF
<audio id="remoteStream" style="display: none;" autoplay controls></audio>
<script type="text/javascript" src="{$goModuleDIR}js/jssip-3.0.13.js"></script>
<script>
	var audioElement = document.querySelector('#remoteStream');
	var localStream;
	var remoteStream;
	var globalSession;
	var phone_login = '$phone_login';
	
	var socket = new JsSIP.WebSocketInterface('{$webProtocol}://{$websocketURL}:{$websocketPORT}/');
	var configuration = {
		sockets : [ socket ],
		uri: 'sip:'+phone_login+'@{$websocketSIP}{$websocketSIPPort},
		$socketParams
		session_timers: false,
		registrar_server: '$websocketSIP',
		use_preloaded_route: false,
		register: true
	};
	
	//init rtcninja libraries...
	
	var phone = new JsSIP.UA(configuration);
	
	phone.on('connected', function(e) {
		//console.log('connected', e);
		
		//phone.register();
	});
	
	phone.on('disconnected', function(e) {
		//console.log('disconnected', e);
	});
	
	phone.on('newRTCSession', function(e) {
		//console.log(e);
		
		var session = e.session;
		//console.log('newRTCSession: originator', e.originator, 'session', e.session, 'request', e.request);
	
		session.on('peerconnection', function (data) {
			//console.log('session::peerconnection', data);
		});
	
		session.on('iceconnectionstatechange', function (data) {
			//console.log('session::iceconnectionstatechange', data);
		});
	
		session.on('connecting', function (data) {
			//console.log('session::connecting', data);
		});
	
		session.on('sending', function (data) {
			//console.log('session::sending', data);
		});
	
		session.on('progress', function (data) {
			//console.log('session::progress', data);
		});
	
		session.on('accepted', function (data) {
			//console.log('session::accepted', data);
		});
	
		session.on('confirmed', function (data) {
			//console.log('session::confirmed', data);
		});
	
		session.on('ended', function (data) {
			//console.log('session::ended', data);
			if (data.cause !== 'Terminated') {
				alertLogout = false;
				sendLogout(true);
				swal({
					title: data.cause,
					text: "$contactAdmin",
					type: 'error'
				});
			}
		});
	
		session.on('failed', function (data) {
			//console.log('session::failed', data);
			alertLogout = false;
			sendLogout(true);
			swal({
				title: data.cause,
				text: "$contactAdmin",
				type: 'error'
			});
		});
	
		//session.on('addstream', function (data) {
		//	console.log('session::addstream', data);
		//
		//	remoteStream = data.stream;
		//	audioElement = document.querySelector('#remoteStream');
		//	audioElement.src = window.URL.createObjectURL(remoteStream);
		//	
		//	globalSession = session;
		//});
	
		session.on('removestream', function (data) {
			//console.log('session::removestream', data);
		});
	
		session.on('newDTMF', function (data) {
			//console.log('session::newDTMF', data);
		});
	
		session.on('hold', function (data) {
			//console.log('session::hold', data);
		});
	
		session.on('unhold', function (data) {
			//console.log('session::unhold', data);
		});
	
		session.on('muted', function (data) {
			//console.log('session::muted', data);
            $.snackbar({id: "mutedMic", content: "<i class='fa fa-microphone-slash fa-lg text-danger' aria-hidden='true'></i>&nbsp; $youTurnOffMic", timeout: 0, htmlAllowed: true});
		});
	
		session.on('unmuted', function (data) {
			//console.log('session::unmuted', data);
			$("#mutedMic").snackbar('hide');
            $.snackbar({content: "<i class='fa fa-microphone fa-lg text-success' aria-hidden='true'></i>&nbsp; $youTurnOnMic", timeout: 5000, htmlAllowed: true});
		});
	
		session.on('reinvite', function (data) {
			//console.log('session::reinvite', data);
		});
	
		session.on('update', function (data) {
			//console.log('session::update', data);
		});
	
		session.on('refer', function (data) {
			//console.log('session::refer', data);
		});
	
		session.on('replaces', function (data) {
			//console.log('session::replaces', data);
		});
	
		session.on('sdp', function (data) {
			//console.log('session::sdp', data);
		});
	
		session.answer({
			mediaConstraints: {
				audio: true,
				video: false
			}
		});
		
		session.connection.addEventListener('addstream', (event) => {
			//console.log("session::addstream", event);
			
			remoteStream = event.stream;
			audioElement = document.querySelector('#remoteStream');
			audioElement.srcObject = remoteStream;
			
			globalSession = session;
		});
	});
	
	phone.on('newMessage', function(e) {
		//console.log('newMessage', e);
	});
	
	phone.on('registered', function(e) {
		//console.log('registered', e);
		phoneRegistered = true;
		registrationFailed = false;
		if ( !!$.prototype.snackbar ) {
			$.snackbar({content: "<i class='fa fa-info-circle fa-lg text-success' aria-hidden='true'></i>&nbsp; $phoneIsRegistered", timeout: 5000, htmlAllowed: true});
		}
	});
	
	phone.on('unregistered', function(e) {
		//console.log('unregistered', e);
		phoneRegistered = false;
	});
	
	phone.on('registrationFailed', function(e) {
		//console.log('registrationFailed', e);
		phoneRegistered = false;
		registrationFailed = true;
		phone.stop();
		swal({
			title: "Registration Failed - " + e.cause,
			text: "$contactAdmin",
			type: 'error'
		});
		
		if ( !!$.prototype.snackbar ) {
			$.snackbar({content: "<i class='fa fa-exclamation-triangle fa-lg text-danger' aria-hidden='true'></i>&nbsp; $registrationFailed", timeout: 5000, htmlAllowed: true});
		}
	});
	
	navigator.mediaDevices.getUserMedia({
		audio: true,
		video: false
	}).then(function (stream) {
		localStream = stream;
		//console.log('getUserMedia', stream);
	
		//phone.start();
	}).catch(function (err) {
		console.error('getUserMedia failed: %s', err.toString());
		swal({
			title: "Microphone NOT Detected",
			text: "$contactAdmin",
			type: 'error'
		});
	});
</script>
EOF;
		}
		if(ECCS_BLIND_MODE === 'y'){
                                $eccsTabStopDatePicker = '<div class="col-md-3"><label for="eccs_year" style="font-size:x-large;">Year</label><input type="number" name="eccs_year" id="eccs_year" class="mda-form-control"  data-tooltip="toolip" title="Callback Year" /></div>';
                                //$eccsTabStopDatePicker .= '<div class="col-md-3"><label for="eccs_month" style="font-size:x-large;">Month</label><input type="text" name="eccs_month" id="eccs_month" class="mda-form-control"  data-tooltip="toolip" title="Callback Month" /></div>';
				$eccsTabStopDatePicker .= '<div class="col-md-3"><label for="eccs_month" style="font-size:x-large;">Month</label>';
				$eccsTabStopDatePicker .= '<select name="eccs_month" id="eccs_month" class="mda-form-control"  data-tooltip="toolip" title="Callback Month"><option value="01">January</option><option value="02">February</option><option value="03">March</option><option value="04">April</option><option value="05">May</option><option value="06">June</option><option value="07">July</option><option value="08">August</option><option value="09">September</option><option value="10">October</option><option value="11">November</option><option value="12">December</option></select></div>';
                                $eccsTabStopDatePicker .= '<div class="col-md-3"><label for="eccs_day" style="font-size:x-large;">Date</label><input type="number" name="eccs_day" id="eccs_day" class="mda-form-control"  data-tooltip="toolip" title="Callback Day"  /></div>';
                                $eccsTabStopDatePicker .= '<div class="col-md-3"><label for="eccs_time" style="font-size:x-large;">Time</label><input type="text" name="eccs_time" id="eccs_time" class="mda-form-control" data-tooltip="toolip" title="Callback Time" /></div>';
		}
	
		$str .= <<<EOF
<div id="dialog-custinfo" class="modal fade" tabindex="-1">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h4 class="modal-title">$custInfoTitle</h4>
			</div>
			<div class="modal-body">
				<form id="formMain" class="form-horizontal">
					<div class="list-group">
						<span name="callchannel" id="formMain_callchannel" style="display: none;"></span>
						<input type="hidden" name="callserverip" id="formMain_callserverip" value="" />
						<input type="hidden" name="uniqueid" id="formMain_uniqueid" value="" />
						<input type="hidden" name="lead_id" id="formMain_lead_id" value="" />
						<input type="hidden" name="list_id" id="formMain_list_id" value="" />
						<table width="100%" border=0>
							$labelHTML
						</table>
					</div>
				</form>
			</div>
			<div class="modal-footer">
				<button id="submitForm" class="btn btn-warning" data-dismiss="modal"><span class="fa fa-check-square-o" aria-hidden="true"></span> $submit</button>
			</div>
		</div>
	</div>
</div>
<div id="select-campaign" class="modal fade" tabindex="-1">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h4 class="modal-title">$selectACampaign</h4>
			</div>
			<div class="modal-body">
				<div style='text-align: center; padding: 2px 5px;'><select id='select_camp' class='mda-form-control ng-pristine ng-empty ng-invalid ng-invalid-required ng-touched select'></select><label for="select_camp" class="control-label">$availableCampaigns</label></div>
				<br />
				<div id="logSpinner" class="text-center hidden"><span style="font-size: 42px;" class="fa fa-spinner fa-pulse"></span></div>
				<div id="inboundSelection" class="clearfix hidden">
					<div class="text-center">
						<strong>$inboundGroups</strong>
					</div>
					<span style="min-width: 46%; margin: 0 5px;" class="text-center bold pull-left">$groupsNotSelected</span>
					<span style="min-width: 46%; margin: 0 5px;" class="text-center bold pull-right">$selectedGroups</span>
					<ul id="notSelectedINB" class="connectedINB pull-left"></ul>
					<ul id="selectedINB" class="connectedINB pull-right"></ul>
					<br />
				</div>
				<p class="text-center hidden" style="padding-top: 10px;">
					<span class="material-switch">
						<input type="checkbox" name="closerSelectBlended" id="closerSelectBlended" value="closer" checked />
						<label for="closerSelectBlended" class="label-primary" style="width: 0px; margin-left: 10px;"></label>
					</span>
					<span style="padding-left: 45px;">$blendedCalling ($outboundActivated)</span>
					<br />
				</p>
				<p id="selectionNote" class="small text-center hidden" style="margin-bottom: 0px;"><b>$note</b>: $selectByDragging</p>
				<div class="hidden" style="text-align: center;">Use WebRTC: <input type="checkbox" name="use_webrtc" value="1" checked disabled /></div>
			</div>
			<div class="modal-footer">
				<button id="scButton" class="btn btn-link bold hidden">$selectAll</button>
				<button id="scSubmit" class="btn btn-warning disabled"><span class="fa fa-check-square-o" aria-hidden="true"></span> $submit</button>
			</div>
		</div>
	</div>
</div>
<div id="select-disposition" class="modal fade" tabindex="-1">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="max-modal hidden" onclick="maximizeModal('select-disposition')" aria-hidden="true" title="$maximize"><i class="fa fa-plus"></i></button>
				<button type="button" class="min-modal" onclick="minimizeModal('select-disposition')" aria-hidden="true" title="$minimize"><i class="fa fa-minus"></i></button>
				<h4 class="modal-title">$dispositionCall: <span id='DispoSelectPhone'></span></h4>
			</div>
			<div class="modal-body">
				<span id="Dispo3wayMessage"></span>
				<span id="DispoManualQueueMessage"></span>
				<span id="PerCallNotesContent"><input type="hidden" name="call_notes_dispo" id="call_notes_dispo" value="" /></span>
				<div id="DispoSelectContent"> $endOfCallDispositionSelection </div>
			</div>
			<div class="modal-footer">
				<input type="hidden" name="DispoSelection" id="DispoSelection" value="" />
				<span class="pull-right">
					<button class="btn btn-default btn-raised hidden-xs" id="btn-dispo-reset-lg">Clear Form</button> 
					<button class="btn btn-default btn-raised visible-xs" id="btn-dispo-reset-xs">Clear</button> 
					<button class="btn btn-warning btn-raised" id="btn-dispo-submit">Submit</button>
				</span>
				<div class="pull-left">
					<div class="material-switch pull-right">
						<input type="checkbox" name="DispoSelectStop" id="DispoSelectStop" value="0" />
						<label for="DispoSelectStop" class="label-primary" style="width: 0px; margin-left: 10px;"></label>
					</div>
					<strong><span id="pause_agent">$pauseAgent</span><span id="pause_agent_xs" style="display: none;">$pauseAgentXS</span></strong>
				</div>
			</div>
		</div>
	</div>
</div>
<div id="select-pause-codes" class="modal fade" tabindex="-1">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title">$selectPauseCode</h4>
			</div>
			<div class="modal-body">
				<span id="PauseCodeSelectContent"> $pauseCodeSelection </span>
			</div>
			<div class="modal-footer">
				<input type="hidden" name="PauseCodeSelection" id="PauseCodeSelection" />
				<span class="pull-right">
					<button class="btn btn-default btn-raised" id="btn-pause-code-back" data-dismiss="modal">Close</button>
				</span>
			</div>
		</div>
	</div>
</div>
<div id="transfer-conference" class="modal fade" tabindex="-1">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true" onclick="btnTransferCall('OFF', 'YES');">&times;</button>
				<h4 class="modal-title">$transferConference</h4>
			</div>
			<div class="modal-body">
				<form role="form" id="xfer_form" class="formXFER form-inline">
					<input type="hidden" id="xferuniqueid" name="xferuniqueid" value="">
					<input type="hidden" id="xfername" name="xfername" value="">
					<input type="hidden" id="xfernumhidden" name="xfernumhidden" value="">
					<div class="row">
						<div class="col-md-12">
							<div class="mda-form-group label-floating">
								<select id="transfer-selection" name="transfer-selection" class="mda-form-control ng-pristine ng-empty ng-invalid ng-invalid-required ng-touched select">
									<option></option>
									<option value="CLOSER">Transfer to Agent / Closer Group</option>
									<option value="REGULAR">Regular 3-Way</option>
								</select>
								<label for="transfer-selection">$transferSelection</label>
							</div>
						</div>
					</div>
					<div id="transfer-closer" class="hidden">
						<div class="row">
							<div class="col-md-9">
								<div class="mda-form-group label-floating">
									<select id="transfer-local-closer" name="transfer-local-closer" onchange="XferAgentSelectLink();" class="mda-form-control ng-pristine ng-empty ng-invalid ng-invalid-required ng-touched select">
										<option>-- $selectGroupsToSendCalls --</option>
									</select>
									<label for="transfer-local-closer" style="text-transform: uppercase;">$closerGroups</label>
								</div>
							</div>
							<div class="col-md-3" style="padding: 10px;">
								<button id="btnLocalCloser" class="btn btn-primary" style="text-transform: uppercase;"> $localCloser </button>
							</div>
						</div>
					</div>
					<div id="transfer-regular" class="hidden">
						<div class="row">
							<div class="col-md-2">
								<div class="mda-form-group label-floating">
									<input type="text" class="mda-form-control ng-pristine ng-empty ng-invalid ng-invalid-required ng-touched input-disabled" id="xferlength" name="xferlength" disabled>
									<label for="xferlength" style="text-transform: uppercase;">$seconds</label>
								</div>
							</div>
							<div class="col-md-4">
								<div class="mda-form-group label-floating">
									<input type="text" class="mda-form-control ng-pristine ng-empty ng-invalid ng-invalid-required ng-touched input-disabled" id="xferchannel" name="xferchannel" disabled>
									<label for="xferchannel" style="text-transform: uppercase;">$channel</label>
								</div>
							</div>
							<div class="col-md-4" style="padding-top: 15px;">
								<div class="material-switch pull-right">
									<input id="consultativexfer" name="consultativexfer" value="1" type="checkbox" onchange="$('#xferoverride').prop('checked', this.checked);"/>
									<label for="consultativexfer" class="label-primary"></label>
								</div>
								<div><b style="opacity: .5; text-transform: uppercase;">$consultative</b></div>
								<input type="checkbox" name="xferoverride" id="xferoverride" value="0" class="hidden">
							</div>
							<!--<div class="col-md-2" style="text-align: center; display: none;"><button class="btn btn-default btn-sm" style="margin-bottom: 2px;" onclick="DTMF_Preset_a();">D1</button><br><button class="btn btn-default btn-sm" onclick="DTMF_Preset_b();">D2</button></div>-->
						</div>
						<div class="row">
							<div class="col-md-9">
								<div class="mda-form-group label-floating">
									<input type="text" class="mda-form-control ng-pristine ng-empty ng-invalid ng-invalid-required ng-touched" id="xfernumber" name="xfernumber">
									<label for="xfernumber" style="text-transform: uppercase;">$numberToDial</label>
								</div>
							</div>
							<div class="col-md-3">
								<div class="mda-form-group label-floating">
								  <div class="input-group" style="margin-top: -18px;">
									<input type="text" id="xferdtmf" class="mda-form-control ng-pristine ng-empty ng-invalid ng-invalid-required ng-touched" style="margin-top: 18px;">
									<label for="xferdtmf" style="text-transform: uppercase;">$sendDTMF</label>
									<span class="input-group-btn" style="padding-top: 18px;">
										<button class="btn btn-primary btn-sm" type="button" onclick="sendXFERdtmf();" style="text-transform: uppercase;">$send</button>
									</span>
								  </div>
								</div>
							</div>
						</div>
						<div class="row" style="margin-bottom: 5px;">
							<div class="col-md-3"><button id="btnHangupXferLine" class="btn btn-default btn-sm disabled" style="text-transform: uppercase;">$hangupXferLine</button></div>
							<div class="col-md-3"><button id="btnHangupBothLines" onclick="BothCallHangup();" class="btn btn-danger btn-sm disabled" style="text-transform: uppercase;">&nbsp; $hangupBothLine &nbsp;</button></div>
							<div class="col-md-3" style="padding-left: 30px;"><button id="btnLeave3WayCall" onclick="Leave3WayCall('FIRST');" class="btn btn-primary btn-sm disabled" style="text-transform: uppercase;">&nbsp; &nbsp;$leave3wayCall&nbsp; &nbsp;&nbsp;</button></div>
							<div class="col-md-3" style="text-align: center;"><button class="btn btn-default btn-sm" style="margin-right: 2px;" onclick="DTMF_Preset_a();">D1</button><button class="btn btn-default btn-sm" onclick="DTMF_Preset_b();">D2</button></div>
						</div>
						<div class="row">
							<div class="col-md-3"><button id="btnDialBlindTransfer" class="btn btn-primary btn-sm disabled" style="text-transform: uppercase;">&nbsp; $blindTransfer &nbsp;</button></div>
							<div class="col-md-3"><button id="btnDialWithCustomer" onclick="SendManualDial('YES');" class="btn btn-primary btn-sm" style="text-transform: uppercase;">$dialWithCustomer</button></div>
							<div class="col-md-3" style="padding-left: 30px;"><button id="btnParkCustomerDial" onclick="XFERParkDial();" class="btn btn-primary btn-sm" style="text-transform: uppercase;">$parkCustomerDial</button></div>
							<div class="col-md-3" style="text-align: center;"><button id="btnDialBlindVMail" class="btn btn-primary btn-sm disabled" title="$blindTransferVM"><i class="fa fa-phone-square"></i> VM</button></div>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>

<div id="callback-datepicker" class="modal fade" tabindex="-1">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<!--<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>-->
				<h4 class="modal-title">$callbackDateSelection</h4>
			</div>
			<div class="modal-body">
				<input type="hidden" id="callback-date" value="" />
				<div class="row">
					<div class="col-md-12">
						<div class="well well-sm bg-info-dark">
							<p class="m0">
								<span class="hidden-xs">$selectedDateAndTime:</span> <em id="date-selected"></em>
								<i id="show-cb-calendar" class="fa fa-calendar pull-right" style="cursor: pointer;"></i>
							</p>
						</div>
					</div>
				</div>
				<div id="cb-container" class="row" style="display: none;">
					<div class="col-md-12">
						<div id="cb-datepicker" class="well well-sm"></div>
					</div>
				</div>
				<div class="row">
					$eccsTabStopDatePicker;
					<div class="col-md-12">
						<div class="mda-form-group label-floating">
							<textarea id="callback-comments" name="callback-comments" rows="5" data-tooltip="tooltip" title="Callback Comment" class="mda-form-control ng-pristine ng-empty ng-invalid ng-invalid-required ng-touched textarea" style="resize:none; width: 100%;"></textarea>
							<label for="callback-comments">$comments</label>
						</div>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<div class="row">
					<div class="col-md-4" id="my_callback_only">
						<p class="pull-left">
							$myCallbackOnly
						</p>
						<div class="material-switch pull-right">
							<input type="checkbox" name="CallBackOnlyMe" id="CallBackOnlyMe" value="0" />
							<label for="CallBackOnlyMe" class="label-primary" style="width: 0px;"></label>
						</div>
					</div>
					<div class="col-md-8">
						<button id="submitCBDate" type="button" data-tooltip="tooltip" title="Submit Callback" class="btn btn-labeled btn-primary">
							<span class="btn-label">
								<i class="fa fa-check"></i>
							</span>
							$submit
						</button>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<div id="view-customer-info" class="modal fade" tabindex="-1">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h4 class="modal-title">$customerInformation</h4>
			</div>
			<div class="modal-body">
				<div id="customer-info-content" style="display: none;"></div>
				<div id="custom-field-content" style="display: none;"></div>
				<div class="cust-preloader" style="margin: 30px 0 10px; text-align: center;">
					<span class="dots">
						<div class="circ1"></div><div class="circ2"></div><div class="circ3"></div><div class="circ4"></div>
					</span>
				</div>
			</div>
			<div class="modal-footer">
				<button class="btn btn-default btn-raised pull-left" id="cust-info-back" data-dismiss="modal">$close</button>
				<span class="pull-right">
					<div style="display: inline-block; width: 250px; padding-right: 70px;">
						<div class="material-switch pull-right" style="margin-left: 20px;">
							<input id="convert-customer" name="convert-customer" value="0" type="checkbox"/>
							<label for="convert-customer" class="label-primary" style="width: 0px;"></label>
						</div>
						<div style="font-weight: bold;">$convertToCustomer</div>
					</div>
					<button class="btn btn-warning btn-raised" id="cust-info-submit">$save</button>
				</span>
			</div>
		</div>
	</div>
</div>
<div id="view-missed-callbacks" class="modal fade" tabindex="-1">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title">$missedCallbacks</h4>
			</div>
			<div class="modal-body">
				<div id="missed-callbacks-loading" style="text-align: center;">
					<i class="fa fa-spinner fa-pulse fa-5x fa-fw"></i>
					<span class="sr-only">Loading...</span>
				</div>
				<div id="missed-callbacks-content" style="display: none;">
					<table width="100%" border=0>
						<thead>
							<tr>
								<th>Name</th>
								<th>Phone</th>
								<th>Callback Date</th>
								<th>Last Call Date</th>
								<th>Comments</th>
							</tr>
						</thead>
						<tbody>
						</tbody>
					</table>
				</div>
			</div>
			<div class="modal-footer">
				<button class="btn btn-default btn-raised pull-right" id="missed-cb-close" data-dismiss="modal">OK</button>
			</div>
		</div>
	</div>
</div>

EOF;
		return $str;
	}

	private function getGOadminContent() {
		$goModuleDIR = GO_MODULE_DIR;
		$userrole = $this->userrole;
		$_SESSION['module_dir'] = $goModuleDIR;
		$_SESSION['campaign_id'] = (strlen($_SESSION['campaign_id']) > 0) ? $_SESSION['campaign_id'] : '';
		
		$phoneIsRegistered = $this->lh()->translationFor('phone_is_now_registered');
		$registrationFailed = $this->lh()->translationFor('registration_failed_refresh');
		
		//$webProtocol = (preg_match("/Windows/", $_SERVER['HTTP_USER_AGENT'])) ? "wss" : "ws";
		$webProtocol = (strlen($_SERVER['HTTPS']) > 0) ? "wss" : "ws";
		
		$this->goDB->where('setting', 'GO_agent_use_wss');
		$rslt = $this->goDB->getOne('settings', 'value');
		$useWebRTC = (strlen($rslt['value']) > 0) ? $rslt['value'] : 0;
		$_SESSION['use_webrtc'] = $useWebRTC;
		
		$this->goDB->where('setting', 'GO_show_phones');
		$rslt = $this->goDB->getOne('settings', 'value');
		$showPhones = (strlen($rslt['value']) > 0) ? $rslt['value'] : 0;
		$_SESSION['show_phones'] = $showPhones;
		
		if ($useWebRTC) {
			$this->goDB->where('setting', 'GO_agent_wss');
			$rslt = $this->goDB->getOne('settings', 'value');
			$websocketURL = (strlen($rslt['value']) > 0) ? $rslt['value'] : "webrtc.goautodial.com";
			
			$this->goDB->where('setting', 'GO_agent_wss_port');
			$rslt = $this->goDB->getOne('settings', 'value');
			$websocketPORT = (strlen($rslt['value']) > 0) ? $rslt['value'] : "10443";
			
			$this->goDB->where('setting', 'GO_agent_wss_sip');
			$rslt = $this->goDB->getOne('settings', 'value');
			$websocketSIP = (strlen($rslt['value']) > 0) ? "{$rslt['value']}" : "'+server_ip";
			
			$this->goDB->where('setting', 'GO_agent_wss_sip_port');
			$rslt = $this->goDB->getOne('settings', 'value');
			$websocketSIPPort = "";
			if (!preg_match("/server_ip/", $websocketSIP)) {
				if (strlen($rslt['value']) > 0 && $rslt['value'] > 0 && $rslt['value'] != 5060) {
					$websocketSIPPort = ":{$rslt['value']}'";
				} else {
					$websocketSIPPort = "'";
				}
			}
			
			$this->goDB->where('setting', 'GO_agent_domain');
			$rslt = $this->goDB->getOne('settings', 'value');
			$domain = (strlen($rslt['value']) > 0) ? $rslt['value'] : "goautodial.com";
		}
		
		
		$phone_login = $_SESSION['phone_login'];
		$phone_pass = $_SESSION['phone_pass'];
		$user_id = $_SESSION['user'];
		$this->astDB->where('user', $user_id);
		$rslt = $this->astDB->getOne('vicidial_users', 'pass,pass_hash');
		$user_pass = (strlen($rslt['pass']) > 0) ? $rslt['pass'] : $rslt['pass_hash'];
		
		if ($useWebRTC) {
			$display_name = $_SESSION['user'];
			$socketParams = "password: pass,";
			if ($_SESSION['bcrypt'] > 0) {
				$ha1_pass = $_SESSION['ha1'];
				$realm = $_SESSION['realm'];
				$socketParams = "ha1: '$ha1_pass', realm: '$realm',";
			}
			$str .= <<<EOF
<audio id="remoteStream" style="display: none;" autoplay controls></audio>
<script type="text/javascript" src="{$goModuleDIR}js/jssip-3.0.13.js"></script>
<script>
	var audioElement = document.querySelector('#remoteStream');
	var localStream;
	var remoteStream;
	var globalSession;
	var phone;
	var phone_login = '$phone_login';
	var phone_pass = '$phone_pass';
	var uName = '$user_id';
	var uPass = '$user_pass';
	var configuration;
	
	function registerPhone(phone_login, pass) {
		var socket = new JsSIP.WebSocketInterface('{$webProtocol}://{$websocketURL}:{$websocketPORT}/');
		configuration = {
			sockets : [ socket ],
			uri: 'sip:'+phone_login+'@{$websocketSIP}{$websocketSIPPort},
			$socketParams
			session_timers: false,
			registrar_server: '$websocketSIP',
			use_preloaded_route: false,
			register: true
		};
		
		phone = new JsSIP.UA(configuration);
		
		phone.on('connecting', function(e) {
			console.log('connecting', e);
		});
		
		phone.on('connected', function(e) {
			console.log('connected', e);
		});
		
		phone.on('disconnected', function(e) {
			console.log('disconnected', e);
		});
		
		phone.on('newRTCSession', function(e) {
			var session = e.session;
			console.log('newRTCSession: originator', e.originator, 'session', e.session, 'request', e.request);
		
			session.on('peerconnection', function (data) {
				console.log('session::peerconnection', data);
			});
		
			session.on('iceconnectionstatechange', function (data) {
				console.log('session::iceconnectionstatechange', data);
			});
		
			session.on('connecting', function (data) {
				console.log('session::connecting', data);
			});
		
			session.on('sending', function (data) {
				console.log('session::sending', data);
			});
		
			session.on('progress', function (data) {
				console.log('session::progress', data);
			});
		
			session.on('accepted', function (data) {
				console.log('session::accepted', data);
			});
		
			session.on('confirmed', function (data) {
				console.log('session::confirmed', data);
			});
		
			session.on('ended', function (data) {
				console.log('session::ended', data);
			});
		
			session.on('failed', function (data) {
				console.log('session::failed', data);
			});
		
			//session.on('addstream', function (data) {
			//	console.log('session::addstream', data);
			//
			//	remoteStream = data.stream;
			//	audioElement = document.querySelector('#remoteStream');
			//	audioElement.src = window.URL.createObjectURL(remoteStream);
			//	
			//	globalSession = session;
			//});
		
			session.on('removestream', function (data) {
				console.log('session::removestream', data);
			});
		
			session.on('newDTMF', function (data) {
				console.log('session::newDTMF', data);
			});
		
			session.on('hold', function (data) {
				console.log('session::hold', data);
			});
		
			session.on('unhold', function (data) {
				console.log('session::unhold', data);
			});
		
			session.on('muted', function (data) {
				console.log('session::muted', data);
			});
		
			session.on('unmuted', function (data) {
				console.log('session::unmuted', data);
			});
		
			session.on('reinvite', function (data) {
				console.log('session::reinvite', data);
			});
		
			session.on('update', function (data) {
				console.log('session::update', data);
			});
		
			session.on('refer', function (data) {
				console.log('session::refer', data);
			});
		
			session.on('replaces', function (data) {
				console.log('session::replaces', data);
			});
		
			session.on('sdp', function (data) {
				console.log('session::sdp', data);
			});
		
			session.answer({
				mediaConstraints: {
					audio: true,
					video: false
				}
			});
		
			session.connection.addEventListener('addstream', (event) => {
				console.log("session::addstream", event);
				
				remoteStream = event.stream;
				audioElement = document.querySelector('#remoteStream');
				audioElement.srcObject = remoteStream;
				
				globalSession = session;
			});
		});
		
		phone.on('newMessage', function(e) {
			console.log('newMessage', e);
		});
		
		phone.on('registered', function(e) {
			var xmlhttp = new XMLHttpRequest();
			var query = "";
			
			phoneRegistered = true;
			$("#dialer-tab").css('display', 'table-cell');
			if ( !!$.prototype.snackbar ) {
				$.snackbar({content: "<i class='fa fa-info-circle fa-lg text-success' aria-hidden='true'></i>&nbsp; $phoneIsRegistered", timeout: 5000, htmlAllowed: true});
			}
		});
		
		phone.on('unregistered', function(e) {
			console.log('unregistered', e);
			phoneRegistered = false;
		});
		
		phone.on('registrationFailed', function(e) {
			console.log('registrationFailed', e);
			if ( !!$.prototype.snackbar ) {
				$.snackbar({content: "<i class='fa fa-exclamation-triangle fa-lg text-danger' aria-hidden='true'></i>&nbsp; $registrationFailed", timeout: 5000});
			}
		});
		
		navigator.mediaDevices.getUserMedia({
			audio: true,
			video: false
		}).then(function (stream) {
			localStream = stream;
		
			//phone.start();
		}).catch(function (err) {
			console.error('getUserMedia failed: %s', err.toString());
			swal({
				title: "Microphone NOT Detected",
				text: "$contactAdmin",
				type: 'error'
			});
		});
	}
</script>
EOF;
		}
		return $str;
	}
	
	// hooks
	private function getLabels($type='system_settings', $label_id=null) {
		//set variables
		$camp = (isset($_SESSION['campaign_id'])) ? $_SESSION['campaign_id'] : null;
		$url = gourl.'/goAgent/goAPI.php';
		$fields = array(
			'goAction' => 'goGetLabels',
			'goUser' => goUser,
			'goPass' => goPass,
			'responsetype' => responsetype,
			'goTableName' => $type,
			'goLabelID' => $label_id,
			'goCampaign' => $camp
		);
		
		//url-ify the data for the POST
		foreach($fields as $key=>$value) { $fields_string .= $key.'='.$value.'&'; }
		rtrim($fields_string, '&');
		
		//open connection
		$ch = curl_init();
		
		//set the url, number of POST vars, POST data
		curl_setopt($ch,CURLOPT_URL, $url);
		curl_setopt($ch,CURLOPT_POST, count($fields));
		curl_setopt($ch,CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch,CURLOPT_POSTFIELDS, $fields_string);
		
		//execute post
		$result = json_decode(curl_exec($ch));
		
		//close connection
		curl_close($ch);
		
		return $result->data;
	}
	
	//public function taskListHoverHook($taskid) {
	//	$this->db()->where("id", $taskid);
	//	$task = $this->db()->getOne(CRM_TASKS_TABLE_NAME);
	//	if (isset($task)) { 
	//		$text = $task["description"]; 
	//		$url = $this->searchQuoteURL($text);
	//	} else { $url = "http://duckduckgo.com"; }
	//	return $this->ui()->hoverActionButton("quote_task_action", "quote-left", $url);
	//}
	
	public function topBarHookAgent() {
		$callbacks = $this->getCallbacks();
		$numberOfCallbacks = count($callbacks);
		$header = $this->getTopbarMenuHeader("phone-square", $numberOfCallbacks, CRM_UI_TOPBAR_MENU_STYLE_SIMPLE, "callbacks", $this->lh()->translationFor("callbacks_for_today"), null, "primary", false);
		
		$elements = "";
		foreach ($callbacks as $key => $cbinfo) {
			//$elements .= $this->getTopbarSimpleElement($cbinfo["quote"]." -- ".$quote["author"], "quote-right", $this->mainPageViewURL(array("author_number" => $quote["author_number"])));
			$elements .= $this->getTopbarSimpleElementWithDate($cbinfo["phone_number"], $cbinfo["callback_time"], "clock-o", "events.php", CRM_UI_STYLE_WARNING, $cbinfo["cust_name"], true);
		}
		$footer = $this->getTopbarMenuFooter($this->lh()->translationFor("see_all_callbacks"), "events.php");
		return $this->ui()->getTopbarCustomMenu($header, $elements, $footer);
	}
	
	private function getCallbacks($number = 5, $cb_type = "today_callbacks") {
		$callbacks_array = $this->getUserInfo($_SESSION['user']);
		$callbacks = [];
		foreach ($callbacks_array->$cb_type as $key => $value) {
			if ($key < $number) {
				$callbacks[$key] = array("phone_number" => $value->phone_number, "cust_name" => $value->cust_name, "callback_time" => $value->short_callback_time);
			} else {
				break;
			}
		}
		
		return $callbacks;
	}

	private function getTopbarMenuFooter($footerText, $footerLink = null) {
		$linkPrefix = isset($footerLink) ? '<a href="'.$footerLink.'">' : '';
		$linkSuffix = isset($footerLink) ? '</a>' : '';
		return '</ul></li><li class="footer">'.$linkPrefix.$footerText.$linkSuffix.'</li></ul></li>';
	}
	
	private function getTopbarSimpleElement($text, $icon, $link, $tint = "aqua") {
		$shortText = strlen($text) > 40 ? substr($text,0,40)."..." : $text;
		return '<li style="text-align: left; !important;"><a href="'.$link.'"><i class="fa fa-'.$icon.' text-'.$tint.'"></i><b>'.$shortText.'</b></a></li>';
	}
	
	private function getTopbarSimpleElementWithDate($text, $date, $icon, $link, $tint = CRM_UI_STYLE_DEFAULT, $title, $isPhone = false) {
	    $shortText = strlen($text) > 25 ? substr($text,0,25)."..." : $text;
		//$relativeTime = $this->ui()->relativeTime($date, 1);
		$relativeTime = $date;
		$showTitle = strlen($title) > 0 ? " title='$title'" : '';
		$showIcon = ($isPhone) ? '<i class="fa fa-phone"></i> ' : '';
		return '<li><a href="'.$link.'"'.$showTitle.' style="padding: 0px 10px;"><h4 style="margin-top: 9.5px;"><p class="pull-left">'.$showIcon.'<b>'.$shortText.'</b></p><small class="label label-'.$tint.' pull-right"><i class="fa fa-'.$icon.'"></i> '.$relativeTime.'</small></h4></a></li>';
	}

	private function getTopbarMenuHeader($icon, $badge, $menuStyle, $menuId = null, $headerText = null, $headerLink = null, $badgeStyle = CRM_UI_STYLE_DEFAULT, $hideForLowResolution = true) {
		// header text and link
		if (!empty($headerText)) {
			$linkPrefix = isset($headerLink) ? '<a href="'.$headerLink.'">' : '';
			$linkSuffix = isset($headerLink) ? '</a>' : '';
			$headerText = $this->lh()->translationFor('you_have')." ".$badge." ".$headerText;
			$headerCode = '<li class="header">'.$linkPrefix.$headerText.$linkSuffix.'</li>';
		} else { $headerCode = ""; }
		$hideCode = $hideForLowResolution? "hide-on-low" : "";
		$menuName = isset($menuId) ? 'id="topbar-'.$menuId.'" ' : '';
		
		// return the topbar menu header
		return '<li '.$menuName.'class="dropdown '.$menuStyle.'-menu '.$hideCode.'"><a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class="fa fa-'.$icon.'"></i><span class="label label-'.$badgeStyle.'">'.$badge.'</span></a>
			<ul class="dropdown-menu">'.$headerCode.'<li><ul class="menu">';
	}
	
	// settings
	public function moduleSettings() {
		//$options = array('', 'asterisk', 'kamailio');
		$options = array('kamailio');
		$moduleSettings = array(
			"GO_agent_use_wss_info" => CRM_SETTING_TYPE_LABEL,
			"GO_agent_use_wss" => CRM_SETTING_TYPE_BOOL,
			"GO_show_phones" => CRM_SETTING_TYPE_BOOL,
			"GO_show_phones_info" => CRM_SETTING_TYPE_LABEL,
			"GO_agent_wss" => CRM_SETTING_TYPE_STRING,
			"GO_agent_wss_info" => CRM_SETTING_TYPE_LABEL,
			"GO_agent_wss_port" => CRM_SETTING_TYPE_INT,
			"GO_agent_wss_port_info" => CRM_SETTING_TYPE_LABEL,
			"GO_agent_wss_sip" => CRM_SETTING_TYPE_STRING,
			"GO_agent_wss_sip_info" => CRM_SETTING_TYPE_LABEL,
			"GO_agent_wss_sip_port" => CRM_SETTING_TYPE_INT,
			"GO_agent_wss_sip_port_info" => CRM_SETTING_TYPE_LABEL,
			"GO_agent_sip_server" => array(
				"type" => CRM_SETTING_TYPE_SELECT,
				"options" => $options
			),
			"GO_agent_sip_server_info" => CRM_SETTING_TYPE_LABEL,
			"GO_agent_domain" => CRM_SETTING_TYPE_STRING,
			"GO_agent_domain_info" => CRM_SETTING_TYPE_LABEL
		);
		return $moduleSettings;
	}
	
	public function getUserInfo($user) {
		//set variables
		$camp = (isset($_SESSION['campaign_id'])) ? $_SESSION['campaign_id'] : null;
		$url = gourl.'/goAgent/goAPI.php';
		$fields = array(
			'goAction' => 'goGetCallbackCount',
			'goUser' => goUser,
			'goPass' => goPass,
			'responsetype' => responsetype,
			'goCampaign' => $camp,
			'goUserID' => $user
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
	}
}

?>
