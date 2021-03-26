<?php
/**
 * @file        APIHandler.php
 * @brief       API Requests
 * @copyright   Copyright (c) 2020 GOautodial Inc.
 * @author      Alexander Jim Abenoja
 * @author		Demian Lizandro A. Biscocho 
 * @author  	Thom Bernarth D. Patacsil
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

	ini_set('memory_limit','2048M');
	ini_set('upload_max_filesize', '600M');
	ini_set('post_max_size', '600M');
	ini_set('max_execution_time', 0);

	// dependencies
	require_once('CRMDefaults.php');
	require_once('LanguageHandler.php');
	require_once('CRMUtils.php');
	require_once('goCRMAPISettings.php');
	require_once('SessionHandler.php');
	$session_class = new \creamy\SessionHandler();

	// ini_set('display_errors', 1);
	// ini_set('display_startup_errors', 1);
	// error_reporting(E_ALL);

	if(isset($_SESSION["user"])){
		define("session_user", $_SESSION["user"]);
		define("session_usergroup", $_SESSION["usergroup"]);
		define("session_password", $_SESSION["phone_this"]);
		define("log_pass", $_SESSION["password_hash"]);
		//define("responsetype", "json");
	}else{
		define("session_user", "TEST DEBUG");
                define("session_usergroup", "ADMIN");
                define("session_password", "TEST DEBUG");
                define("log_pass", "TEST");
	}

	$uri = $_SERVER['REQUEST_URI'];
	$uri = explode('/', $uri);
	$uri = explode('.php', $uri[1]);
	$uri = $uri[0];

	if ($uri != 'index') {
		if ($uri != 'login') {
			if (!isset($_SESSION['user'])){
// || $_SESSION["userrole"] == CRM_DEFAULTS_USER_ROLE_AGENT) { 
				//if ($uri == 'php') die("This file cannot be accessed directly"); 
			}
		}
	}

	/**
	*  APIHandler.
	*  This class is in charge of storing the API Connections for the basic functionality of the system.
	*/
	class APIHandler {

		// language handler
		private $lh;

		/** Creation and class lifetime management */

		/**
		* Returns the singleton instance of UIHandler.
		* @staticvar APIHandler $instance The APIHandler instance of this class.
		* @return APIHandler The singleton instance.
		*/
		public static function getInstance()
		{
			static $instance = null;
			if (null === $instance) {
				$instance = new static();
			}

			return $instance;
		}


		/**
		* Private clone method to prevent cloning of the instance of the
		* *Singleton* instance.
		*
		* @return void
		*/
		private function __clone()
		{
		}

		/**
		* Private unserialize method to prevent unserializing of the *Singleton*
		* instance.
		*
		* @return void
		*/
		private function __wakeup()
		{
		}

		/*
		* API_Request - Handles All API Requests
		* @param String $folder - Folder Name where API is located (ex. goUsers, goInbound, goVoicemails)
		* @param Array $postfields - Post Requests. API Name is required (ex. goAction => goGetUserGroupInfo, goAction => goGetAllUsers, goAction => goEditDID)
		* @param Boolean $request_data - true or false. If true, converts return data to original format without json_decode. Returns json_decoded data if false.
		* @return Array $output
		*/
		public function API_Request($folder, $postfields, $request_data = false){
			$url = gourl."/".$folder."/goAPI.php";
			$responsetype = "json";

			// Constant Data to be passed
			$default_entries = array(
				'goUser' => session_user,
				'goPass' => session_password,
				'responsetype' => $responsetype,
				'session_user' => session_user,
				'log_user' => session_user,
				'log_group' => session_usergroup,
				'log_ip' => $_SERVER['REMOTE_ADDR'],
				'log_pass' => log_pass,
				'hostname' => $_SERVER['REMOTE_ADDR']);

			$postdata = array_merge($default_entries, $postfields);

			// Call the API
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_TIMEOUT, 0);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
			curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postdata));
			$data = curl_exec($ch);
			curl_close($ch);
			$output = json_decode($data);
			
			if($request_data === true)
				return $data;
			else
				return $output;
		}

		/*
		* API_Upload - Handles All API with Upload. Examples: Upload Leads, Upload Voicefiles
		* @param String $folder - Folder Name where API is located (ex. goUsers, goInbound, goVoicemails)
		* @param Array $postfields - Post Requests. API Name is required (ex. goAction => goGetUserGroupInfo, goAction => goGetAllUsers, goAction => goEditDID)
		* 
		* @return Array $output
		*/
		public function API_Upload($folder, $postfields, $return_data = NULL){
			$url = gourl."/".$folder."/goAPI.php";
			$responsetype = "json";
			
			// Constant Data to be passed
			$default_entries = array(
				'goUser' => session_user,
				'goPass' => session_password,
				'responsetype' => $responsetype,
				'session_user' => session_user,
				'log_user' => session_user,
				'log_group' => session_usergroup,
				'log_pass' => log_pass,
				'log_ip' => $_SERVER['REMOTE_ADDR'],
				'hostname' => $_SERVER['REMOTE_ADDR']);

			$postdata = array_merge($default_entries, $postfields);

			// Call the API
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $postdata);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			//curl_setopt($ch, CURLOPT_CONNECTTIMEOUT , 0); //gg
			curl_setopt($ch, CURLOPT_TIMEOUT  , 0); //gg
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			$data = curl_exec($ch);
			curl_close($ch);
			$output = json_decode($data);
				
			if(!empty($return_data))
				return array("output" => $output, "data" => $data, "URL" => $url, "CONNECTION" => $postdata);
			else
				return $output;
		}

		public function API_StarwoodTestUpload($return_data = NULL){
			$url = gourl."/goUploadLeads/goAPI.php";
			$responsetype = "json";
			$upload_url = "https://wits.justgocloud.com/leadsdata.csv";
			
			$finfo = finfo_open('text/csv');
			$finfo = finfo_file($finfo, $upload_url);

			//$goFileMe = new CURLFile($upload_url, 'text/csv');
			//$goFileMe = curl_file_create($upload_url, 'text/csv', $upload_url);

			// Constant Data to be passed
			$default_entries = array(
				'goUser' => 'admin',
				'goPass' => '6Arlk87V7SKfZU%2Fm6LPceuERHduvFiu',
				'responsetype' => $responsetype,
				'session_user' => 'admin',
				'log_user' => 'admin',
				'log_group' => 'ADMIN',
				'log_pass' => log_pass,
				'log_ip' => $_SERVER['REMOTE_ADDR'],
				'hostname' => $_SERVER['REMOTE_ADDR'],
				'goAction' => 'goUploadMe',
				'goDupcheck' => 'DUPLIST',
				'goListId' => '5054',
				'goFileMe' => $goFileMe
			);

			$postdata = $default_entries;
			// Call the API
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $postdata);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			//curl_setopt($ch, CURLOPT_CONNECTTIMEOUT , 0); //gg
			curl_setopt($ch, CURLOPT_SAFE_UPLOAD, true);
			curl_setopt($ch, CURLOPT_TIMEOUT  , 0); //gg
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			$data = curl_exec($ch);
			curl_close($ch);
			$output = json_decode($data);
				
			/*if(!empty($return_data))
				return array("output" => $output, "data" => $data, "URL" => $url, "CONNECTION" => $postdata);
			else
				return $output;*/

			return $data;
		}

		public function API_getGOPackage(){
			$postfields = array(
				'goAction' => 'goGetPackage'
			);				

			return $this->API_Request("goPackages", $postfields);
		}

		public function API_goGetGroupPermission() {
			$postfields = array(
				'goAction' => 'goGetUserGroupInfo',
				'user_group' => session_usergroup
			);

			return $this->API_Request("goUserGroups", $postfields);
		}

		public function goGetPermissions($type = 'dashboard') {
			
			$permissions = $this->API_goGetGroupPermission();
			$decoded_permission = json_decode($permissions->data->permissions);
			
			$return = NULL;
			if (!empty($permissions)) {
				$types = explode(",", $type);
				if (count($types) > 1) {
					foreach ($types as $t) {
						if (array_key_exists($t, $decoded_permission)) {
							$return->{$t} = $decoded_permission->{$t};
						}
					}
				} else {
					if ($type == 'sidebar') {
						$return = $permissions;
					} else if (array_key_exists($type, $decoded_permission)) {
						$return = $decoded_permission->{$type};
					} else {
						$return = null;
					}
				}
			}

			return $return;
		}
		
		public function API_getLoginInfo($user) {
			$camp = (isset($_SESSION['campaign_id']) && strlen($_SESSION['campaign_id']) > 2) ? $_SESSION['campaign_id'] : '';
			$url = gourl.'/goAgent/goAPI.php';
			$fields = array(
				'goAction' => 'goGetLoginInfo',
				'goUser' => session_user,
				'goPass' => session_password,
				'responsetype' => 'json',
				'session_user' => session_user,
				'log_ip' => $_SERVER['REMOTE_ADDR'],
				'goUserID' => $user,
				'goCampaign' => $camp,
				'isPBP' => 0,
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
		}
		
		public function API_getAllPauseCodes($campaign_id) {
			$postfields = array(
				'goAction' => 'goGetAllPauseCodes',
				'campaign_id' => $campaign_id
			);	

			return $this->API_Request("goPauseCodes", $postfields);
		}
		
		public function API_modifyPauseCode($postfields) {
			return $this->API_Request("goPauseCodes", $postfields);
		}	
		
		public function API_getAllInGroups() {
			$postfields = array(
				'goAction' => 'goGetAllIngroup'
			);	

			return $this->API_Request("goInbound", $postfields);
		}

		public function API_modifyInGroups($postfields) {
			return $this->API_Request("goInbound", $postfields);
		}

		public function API_getInGroupInfo($groupid) {
			$postfields = array(
				'goAction' => 'goGetIngroupInfo',
				'group_id' => $groupid
			);				
			return $this->API_Request("goInbound", $postfields);
		}

		// Telephony IVR
		public function API_getAllIVRs() {
			$postfields = array(
				'goAction' => 'goGetAllIVR'
			);
			return $this->API_Request("goInbound", $postfields);
		}
		
		public function API_getIVRInfo($menu_id) {
			$postfields = array(
				'goAction' => 'goGetIVRInfo',
				'menu_id' => $menu_id
			);
			return $this->API_Request("goInbound", $postfields);
		}	

		public function API_getIVROptions($menu_id) {
			$postfields = array(
				'goAction' => 'goGetIVROptions',
				'menu_id' => $menu_id
			);
			return $this->API_Request("goInbound", $postfields);
		}
		
		public function API_modifyIVR($postfields) {
			return $this->API_Request("goInbound", $postfields);
		}

		public function API_modifyDID($postfields) {
			return $this->API_Request("goInbound", $postfields);
		}
		
		public function API_modifyAgentRank($postfields) {
			return $this->API_Request("goInbound", $postfields);
		}

		public function API_getAllAgentRank($group_id) {
			$postfields = array(
				'goAction' => 'goGetAllAgentRank',
				//'user_id' => $user_id,
				'group_id' => $group_id
			);				
			return $this->API_Request("goInbound", $postfields);
		}
		
		public function API_getAllDIDs() {
			$postfields = array(
				'goAction' => 'goGetAllDID'
			);				
			return $this->API_Request("goInbound", $postfields);
		}

		public function API_getDIDInfo($did_id) {
			$postfields = array(
				'goAction' => 'goGetDIDInfo',
				'did_id' =>	$did_id
			);				
			return $this->API_Request("goInbound", $postfields);
		}
		
		// Telephony Users -> Phone
		public function API_getAllPhones(){
			$postfields = array(
				'goAction' => 'goGetAllPhones'
			);				
			return $this->API_Request("goPhones", $postfields);
		}

		public function API_getPhoneInfo($extenid){
			$postfields = array(
				'goAction' => 'goGetPhoneInfo',
				'extension' => $extenid
			);				
			return $this->API_Request("goPhones", $postfields);
		}
		
		/** Call Times API - Get all list of call times */
		public function API_getAllCalltimes(){
			$postfields = array(
				'goAction' => 'goGetAllCalltimes'
			);				
			return $this->API_Request("goCalltimes", $postfields);
		}

		public function API_getCalltimeInfo($call_time_id){
			$postfields = array(
				'goAction' => 'goGetCalltimeInfo',
				'call_time_id' => $call_time_id
			);				
			return $this->API_Request("goCalltimes", $postfields);
		}
		
		// API Scripts
		public function API_getAllScripts(){
			$postfields = array(
				'goAction' => 'goGetAllScripts'
			);				
			return $this->API_Request("goScripts", $postfields);
		}

		public function API_getStandardFields(){
			$postfields = array(
				'goAction' => 'goGetStandardFields'
			);				
			return $this->API_Request("goScripts", $postfields);
		}
		
		public function API_getScriptInfo($scriptid){
			$postfields = array(
				'goAction' => 'goGetScriptInfo',
				'script_id' => $scriptid
			);				
			return $this->API_Request("goScripts", $postfields);
		}
		
		// API Filters
		public function API_getAllFilters(){
			$postfields = array(
				'goAction' => 'goGetAllFilters'
			);				
			return $this->API_Request("goFilters", $postfields);
		}
		
		public function API_getFilterInfo($filterid){
			$postfields = array(
				'goAction' => 'goGetFilterInfo',
				'filter_id' => $filterid
			);				
			return $this->API_Request("goFilters", $postfields);
		}
		
		// VoiceMails
		public function API_getAllVoiceMails() {
			$postfields = array(
				'goAction' => 'goGetAllVoicemails'
			);				
			return $this->API_Request("goVoicemails", $postfields);
		}

		public function API_getVoicemailInfo($voicemail_id) {
			$postfields = array(
				'goAction' => 'goGetVoicemailInfo',
				'voicemail_id' => $voicemail_id
			);				
			return $this->API_Request("goVoicemails", $postfields);
		}
		
		/** Voice Files API - Get all list of voice files */
		public function API_getAllVoiceFiles(){
			$postfields = array(
				'goAction' => 'goGetAllVoiceFiles'
			);				
			return $this->API_Request("goVoiceFiles", $postfields);
		}

		/** Music On Hold API - Get all list of music on hold */
		public function API_getAllMusicOnHold(){
			$postfields = array(
				'goAction' => 'goGetAllMusicOnHold'
			);
			return $this->API_Request("goMusicOnHold", $postfields);
		}
		
		public function API_getAllCampaigns(){
			$postfields = array(
				'goAction' => 'goGetAllCampaigns'
			);		
			return $this->API_Request("goCampaigns", $postfields);
		}	
		
		public function API_getAllAudioFiles(){
			$postfields = array(
				'goAction' => 'getAllAudioFiles'
			);		
			return $this->API_Request("goCampaigns", $postfields);
		}
		
		public function API_getSuggestedDIDs($keyword){
			$postfields = array(
				'goAction' => 'goGetSuggestedDIDs',
				'keyword' => $keyword			
			);
			return $this->API_Request("goCampaigns", $postfields);
		}	
		
		public function API_getDIDSettings($did){
			$postfields = array(
				'goAction' => 'goGetDIDSettings',
				'did' => $did			
			);
			return $this->API_Request("goCampaigns", $postfields);
		}
		
		public function getAllCampaignStatuses(){
			$campaign = $this->API_getAllCampaigns();
			for($i=0;$i < count($campaign->campaign_id);$i++){
				$campdialStatus = $this->API_getAllCampaignDialStatuses($campaign->campaign_id[$i]);
				for($x=0;$x<count($campdialStatus->status);$x++){
					$status[] = $campdialStatus->status[$x];
					$status_name[] = $campdialStatus->status_name[$x];
				}
				$output = array("status" => $status, "status_name" => $status_name);
			}
			return $output;
		}
			
		public function API_getRealtimeAgentsMonitoring(){
			$postfields = array(
				'goAction' => 'goGetRealtimeAgentsMonitoring'
			);		
			return $this->API_Request("goDashboard", $postfields);
		}
		
		public function API_getRealtimeCallsMonitoring(){
			$postfields = array(
				'goAction' => 'goGetRealtimeCallsMonitoring'
			);		
			return $this->API_Request("goDashboard", $postfields);
		}
			
		public function API_getRealtimeInboundMonitoring($ingroup){
			$postfields = array(
				'goAction' => 'goGetRealtimeInboundMonitoring',
				'goIngroup' => $ingroup
			);		
			return $this->API_Request("goDashboard", $postfields);
		}

		public function API_getTotalDroppedCalls(){
			$postfields = array(
				'goAction' => 'goGetTotalDroppedCalls'
			);		
			return $this->API_Request("goDashboard", $postfields);
		}

			public function API_getDroppedPercentage(){
					$postfields = array(
							'goAction' => 'goGetDroppedPercentage'
					);
					return $this->API_Request("goDashboard", $postfields);
			}
		
		public function API_getCampaignsResources(){
			$postfields = array(
				'goAction' => 'goGetCampaignsResources'
			);		
			return $this->API_Request("goDashboard", $postfields);
		}

		public function API_getCampaignsMonitoring(){
			$postfields = array(
				'goAction' => 'goGetCampaignsResources'
			);		
			return $this->API_Request("goDashboard", $postfields);
		}	
		
		public function API_getTotalAgentsPaused(){
			$postfields = array(
				'goAction' => 'goGetTotalAgentsPaused'
			);		
			return $this->API_Request("goDashboard", $postfields);
		}	
		
		public function API_getTotalAgentsWaitCalls(){
			$postfields = array(
				'goAction' => 'goGetTotalAgentsWaitCalls'
			);		
			return $this->API_Request("goDashboard", $postfields);
		}
		
		public function API_getTotalAgentsCall(){
			$postfields = array(
				'goAction' => 'goGetTotalAgentsCall'
			);		
			return $this->API_Request("goDashboard", $postfields);
		}
		
		public function API_getClusterStatus(){
			$postfields = array(
				'goAction' => 'goGetClusterStatus'
			);		
			return $this->API_Request("goDashboard", $postfields);
		}	
		
		public function API_getTotalRingingCalls(){
			$postfields = array(
				'goAction' => 'goGetRingingCalls'
			);		
			return $this->API_Request("goDashboard", $postfields);
		}
		
		public function API_getTotalCalls($type){
			$postfields = array(
				'goAction' => 'goGetTotalCalls',
				'type' => $type
			);		
			return $this->API_Request("goDashboard", $postfields);
		}
		
		public function API_getTotalSales($type){
			$postfields = array(
				'goAction' => 'goGetTotalSales',
				'type' => $type
			);		
			return $this->API_Request("goDashboard", $postfields);
		}	
		
		public function API_getTotalAnsweredCalls(){
			$postfields = array(
				'goAction' => 'goGetTotalAnsweredCalls'
			);		
			return $this->API_Request("goDashboard", $postfields);
		}
		
		public function API_getRingingCalls(){
			$postfields = array(
				'goAction' => 'goGetRingingCalls'
			);		
			return $this->API_Request("goDashboard", $postfields);
		}	
		
		public function API_getIncomingQueue(){
			$postfields = array(
				'goAction' => 'goGetIncomingQueue'
			);		
			return $this->API_Request("goDashboard", $postfields);
		}	
		
		public function API_getLiveOutbound(){
			$postfields = array(
				'goAction' => 'goGetLiveOutbound'
			);		
			return $this->API_Request("goDashboard", $postfields);
		}

		public function API_getSalesAgent(){
			$postfields = array(
							'goAction' => 'goGetSalesAgent'
					);
					return $this->API_Request("goDashboard", $postfields);
		}
		
		public function API_getAllDispositions(){
			$postfields = array(
				'goAction' => 'goGetAllDispositions'
			);		
			return $this->API_Request("goDispositions", $postfields);
		}
		
		public function API_getAllCampaignDispositions(){
					$postfields = array(
							'goAction' => 'goGetAllCampaignDispositions'
					);
					return $this->API_Request("goDispositions", $postfields);
			}
		
		public function API_getAllLeadRecycling(){
			$postfields = array(
				'goAction' => 'goGetAllLeadRecycling'
			);		
			return $this->API_Request("goLeadRecycling", $postfields);
		}	
		
		public function API_getLeadRecyclingInfo($campaign_id){
			$postfields = array(
				'goAction' => 'goGetLeadRecyclingInfo',
				'campaign_id' => $campaign_id
			);		
			return $this->API_Request("goLeadRecycling", $postfields);
		}
		
		public function API_getAllDialStatuses($campaign_id, $add_hotkey, $selectable = 0){
			$postfields = array(
				'goAction' => 'goGetAllDialStatuses',
				'campaign_id' => $campaign_id,
				'is_selectable' => $selectable,
				'add_hotkey' => $add_hotkey
			);		
			return $this->API_Request("goDialStatus", $postfields);
		}	
		
		public function API_getAllDialStatusesSurvey($campaign_id){
			$postfields = array(
				'goAction' => 'goGetAllDialStatuses',
				'campaign_id' => $campaign_id,
				'hotkeys_only' => "1"
			);		
			return $this->API_Request("goDialStatus", $postfields);
		}
		
		public function API_getAllHotkeys($campaign_id) {
			$postfields = array(
				'goAction' => 'goGetAllHotkeys',
				'campaign_id' => $campaign_id
			);	

			return $this->API_Request("goHotkeys", $postfields);
		}	
		/*
		* Displaying Lead Filter
		* [[API: Function]] - getAllLeadFilters
		* 	This application is used to get list of lead filter belongs to user.
		*/
		public function API_getAllLeadFilters(){
			$postfields = array(
				'goAction' => 'goGetAllLeadFilters'
			);		
			return $this->API_Request("goLeadFilters", $postfields);
		}	
		
		public function API_getCountryCodes(){
			$postfields = array(
				'goAction' => 'getAllCountryCodes'
			);		
			return $this->API_Request("goCountryCode", $postfields);
		}	
		
		public function API_getAllLists(){
			$postfields = array(
				'goAction' => 'goGetAllLists'
			);		
			return $this->API_Request("goLists", $postfields);
		}	
		
		public function API_getAllListsCampaign($campaign_id){
			$postfields = array(
				'goAction' => 'goGetAllListsCampaign',
				'campaign_id' => $campaign_id
			);		
			return $this->API_Request("goLists", $postfields);
		}
		
		public function API_getStatusesWithCountCalledNCalled($list_id){
			$postfields = array(
				'goAction' => 'goGetStatusesWithCountCalledNCalled',
				'list_id' => $list_id
			);		
			return $this->API_Request("goLists", $postfields);
		}
		
		public function API_getTZonesWithCountCalledNCalled($list_id){
			$postfields = array(
				'goAction' => 'goGetTZonesWithCountCalledNCalled',
				'list_id' => $list_id
			);		
			return $this->API_Request("goLists", $postfields);
		}
		
		public function API_getAllLeadsOnHopper($campaign_id){
			$postfields = array(
				'goAction' => 'goGetAllLeadsOnHopper',
				'campaign_id' => $campaign_id
			);		
			return $this->API_Request("goLists", $postfields);
		}
		
		public function API_getListInfo($list_id){
			$postfields = array(
				'goAction' => 'goGetListInfo',
				'list_id' => $list_id
			);		
			return $this->API_Request("goLists", $postfields);
		}
		
		public function API_GetDNC($search){
			$postfields = array(
				'goAction' => 'goGetAllDNC',
				'search' => $search
			);		
			return $this->API_Request("goLists", $postfields);
		}
		
		public function API_listExport($list_id){
			$postfields = array(
				'goAction' => 'goListExport',
				'list_id' => $list_id
			);		
			return $this->API_Request("goLists", $postfields);
		}
		
		public function API_getLeadsInfo($lead_id){
			$postfields = array(
				'goAction' => 'goGetLeadsInfo',
				'lead_id' => $lead_id
			);		
			return $this->API_Request("goGetLeads", $postfields);
		}
		
		public function API_getLeads($search, $disposition_filter, $list_filter, $address_filter, $city_filter, $state_filter, $limit = 0, $search_customers = 0, $start_date = null, $end_date = null) {
			if ($limit == 0) {
				$limit = 50;
			}
			
			$postfields = array(
				"goAction" => "goGetLeads",
				"search" => $search,
				"disposition_filter" => $disposition_filter,
				"list_filter" => $list_filter,
				"address_filter" => $address_filter,
				"city_filter" => $city_filter,
				"state_filter" => $state_filter,
				"search_customers" => $search_customers,
				"goVarLimit" => $limit,
				"start_date" => $start_date,
				"end_date" => $end_date
			);		
			return $this->API_Request("goGetLeads", $postfields);
		}
		
		public function API_getAllCarriers(){
			$postfields = array(
				'goAction' => 'goGetAllCarriers'
			);		
			return $this->API_Request("goCarriers", $postfields);
		}	
		
		public function API_getCarrierInfo($carrier_id){
			$postfields = array(
				'goAction' => 'goGetCarrierInfo',
				'carrier_id' => $carrier_id
			);		
			return $this->API_Request("goCarriers", $postfields);
		}	
		
		public function API_getAllServers(){
			$postfields = array(
				'goAction' => 'goGetAllServers'
			);		
			return $this->API_Request("goServers", $postfields);
		}	
		
		public function API_getServerInfo($server_id){
			$postfields = array(
				'goAction' => 'goGetServerInfo',
				'server_id' => $server_id
			);		
			return $this->API_Request("goServers", $postfields);
		}
		
		public function API_getAdminLogsList(){
			$postfields = array(
				'goAction' => 'goGetAdminLogsList'
			);		
			return $this->API_Request("goAdminLogs", $postfields);
		}	
		
		public function API_getAllCampaignDialStatuses($campaign_id){
			$postfields = array(
				'goAction' => 'goGetAllCampaignDialStatuses',
				'campaign_id' => $campaign_id
			);		
			return $this->API_Request("goDialStatus", $postfields);
		}	
		
		public function API_getCampaignInfo($campid){
			$postfields = array(
				'goAction' => 'goGetCampaignInfo',
				'campaign_id' => $campid
			);		
			return $this->API_Request("goCampaigns", $postfields);
		}	
		
		public function API_getCampaignDispositions($campaign_id){
			$postfields = array(
				'goAction' => 'goGetCampaignDispositions',
				'campaign_id' => $campaign_id
			);		
			return $this->API_Request("goCampaigns", $postfields);
		}
		
		public function API_getCampaignLeadRecycling($campaign_id){
			$postfields = array(
				'goAction' => 'goGetCampaignLeadRecycling',
				'campaign_id' => $campaign_id
			);		
			return $this->API_Request("goCampaigns", $postfields);
		}	
		public function API_getAllUsers(){
			$postfields = array(
				'goAction' => 'goGetAllUsers'			
			);
			return $this->API_Request("goUsers", $postfields);
		}

		public function API_getUserInfo($user, $filter = null, $userid = null){
			$postfields = array(
				'goAction' => 'goGetUserInfo',
				'user' => $user,
				'filter' => $filter,
				'user_id' => $userid
			);
			//return $postfields;
			return $this->API_Request("goUsers", $postfields);
		}
		
		public function API_getAgentLog($user, $sdate, $edate, $agentlog){
			$postfields = array(
				'goAction' => 'goGetAgentLog',
				'user' => $user,
				'start_date' => $sdate,
				'end_date' => $edate,
				'agentlog'	=> $agentlog
			);
			return $this->API_Request("goUsers", $postfields);
		}
		
		public function API_getAllUserGroups() {
			$postfields = array(
				'goAction' => 'goGetAllUserGroups'
			);
			return $this->API_Request("goUserGroups", $postfields);
		}
		
		public function API_getUserGroupInfo($group_id) {
			$postfields = array(
				'goAction' => 'goGetUserGroupInfo',
				'user_group' => $group_id
			);
			return $this->API_Request("goUserGroups", $postfields);
		}
		
		public function API_getCallRecordingList($search_phone, $start_filterdate, $end_filterdate, $agent_filter) {
			$postfields = array(
				'goAction' => 'goGetCallRecordingList'
			);
			if (isset($search_phone)) { 
				$postfields .= array(
					'requestDataPhone' => $search_phone
				);
			}
			if (isset($start_filterdate)) {
				$postfields .= array(
					'start_filterdate' => $start_filterdate,
					'end_filterdate' => $end_filterdate,
					'agent_filter' => $agent_filter
				);	    
			}
			return $this->API_Request("goCallRecordings", $postfields);
		}	
		
		public function API_getReports($postfields){			
			return $this->API_Request("goReports", $postfields);
		}
		
		public function API_getStatisticalReports($postfields){			
			return $this->API_Request("goReports", $postfields);
		}
		
		public function API_getAgentTimeDetails($postfields){			
			return $this->API_Request("goReports", $postfields);
		}	

		public function API_getCustomizations($postfields){
			return $this->API_Reguest("goSystemSettings", $postfields);
		}

		public function API_getSystemSettingInfo(){
			$postfields = array(
				'goAction' => 'goGetSystemSettingInfo'
			);
			return $this->API_Request("goSystemSettings", $postfields);
		}
		
		public function API_editSystemSetting($allow_voicemail_greeting){
			$postfields = array(
				'goAction' => 'goEditSystemSetting',
				'allow_voicemail_greeting' => $allow_voicemail_greeting
			);
			return $this->API_Request("goSystemSettings", $postfields);
		}	

		public function API_actionDNC($postfields) {
			return $this->API_Request("goLists", $postfields);
		}

		public function API_SMTPActivation($postfields){
			return $this->API_Request("goSMTP", $postfields);
		}

		public function API_addCalltime($postfields){
			return $this->API_Request("goCalltimes", $postfields);
		}

		public function API_editCalltime($postfields){
			return $this->API_Request("goCalltimes", $postfields);
		}
		
		public function API_addCarrier($postfields){
			return $this->API_Request("goCarriers", $postfields);
		}

		public function API_editCarrier($postfields){
			return $this->API_Request("goCarriers", $postfields);
		}
		
		public function API_getAllCustomFields($list_id) {
			$postfields = array(
				'goAction' => 'goGetAllCustomFields',
				'list_id' => $list_id
			);
			return $this->API_Request("goCustomFields", $postfields);
		}
		
		public function API_addCustomFields($postfields){
			return $this->API_Request("goCustomFields", $postfields);
		}

		public function API_addCampaign($postfields){
			return $this->API_Upload("goCampaigns", $postfields);
		}
		
		public function API_addDialStatus($postfields){
			return $this->API_Request("goCampaigns", $postfields);
		}

		public function API_getAllAreacodes(){
			$postfields = array(
				'goAction' => 'goGetAllAreacodes'
			);
			return $this->API_Request("goAreacodes", $postfields);
		}

		public function API_getAreacodeInfo($postfields){
			return $this->API_Request("goAreacodes", $postfields);
		}

		public function API_addAreacode($postfields){
					return $this->API_Request("goAreacodes", $postfields);
			}

			public function API_modifyAreacode($postfields){
					return $this->API_Request("goAreacodes", $postfields);
			}

		public function API_deleteAreacode($postfields){
					return $this->API_Request("goAreacodes", $postfields);
			}

		public function API_addDisposition($postfields){
			return $this->API_Request("goDispositions", $postfields);
		}
		
		public function API_editDisposition($postfields){
			return $this->API_Request("goDispositions", $postfields);
		}
		
		public function API_updateCampaignGoogleSheet($postfields){
			return $this->API_Request("goCampaigns", $postfields);
		}

		public function API_addHotkey($postfields){
			return $this->API_Request("goHotkeys", $postfields);
		}

		public function API_addIVR($postfields){
			return $this->API_Request("goInbound", $postfields);
		}

		public function API_addLeadFilter($postfields){
			return $this->API_Request("goLeadFilters", $postfields);
		}

		public function API_addLeadRecycling($postfields){
			return $this->API_Request("goLeadRecycling", $postfields);
		}

		public function API_editLeadRecycling($postfields){
			return $this->API_Request("goLeadRecycling", $postfields);
		}
		
		public function API_addLoadLeads($postfields, $data = NULL){
			return $this->API_Upload("goUploadLeads", $postfields, $data);
		}

		public function API_addMOH($postfields){
			return $this->API_Request("goMusicOnHold", $postfields);
		}

		public function API_editMOH($postfields){
			return $this->API_Request("goMusicOnHold", $postfields);
		}
		
		public function API_getMOHInfo($moh_id){
			$postfields = array(
				'goAction' => 'goGetMOHInfo',
				'moh_id' => $moh_id
			);	
			return $this->API_Request("goMusicOnHold", $postfields);
		}
		
		public function API_addPauseCode($postfields){
			return $this->API_Request("goPauseCodes", $postfields);
		}

		public function API_addScript($postfields){
			return $this->API_Request("goScripts", $postfields);
		}
		
		public function API_editScript($postfields){
			return $this->API_Request("goScripts", $postfields);
		}	

		public function API_addFilter($postfields){
			return $this->API_Request("goFilters", $postfields);
		}
		
		public function API_editFilter($postfields){
			return $this->API_Request("goFilters", $postfields);
		}	

		public function API_addServer($postfields){
			return $this->API_Request("goServers", $postfields);
		}

		public function API_editServer($postfields){
			return $this->API_Request("goServers", $postfields);
		}
		
		public function API_addUser($postfields){
			return $this->API_Request("goUsers", $postfields);
		}

		public function API_addPhones($postfields){
			return $this->API_Request("goPhones", $postfields);
		}

		public function API_editPhone($postfields){
			return $this->API_Request("goPhones", $postfields);
		}
		
		public function API_addIngroup($postfields){
			return $this->API_Request("goInbound", $postfields);
		}

		public function API_addDID($postfields){
			return $this->API_Request("goInbound", $postfields);
		}

		public function API_addList($postfields){
			return $this->API_Request("goLists", $postfields);
		}

		public function API_addUserGroup($postfields){
			return $this->API_Request("goUserGroups", $postfields);
		}

		public function API_editUserGroup($postfields){
			return $this->API_Request("goUserGroups", $postfields);
		}
		
		public function API_editLeads($postfields){
			return $this->API_Request("goGetLeads", $postfields);
		}
		
		public function API_addVoiceFiles($postfields){
			return $this->API_Upload("goVoiceFiles", $postfields);
		}

		public function API_addVoicemail($postfields){
			return $this->API_Request("goVoicemails", $postfields);
		}

		public function API_editVoicemail($postfields){
			return $this->API_Request("goVoicemails", $postfields);
		}
		
		public function API_checkCalltimes($postfields){
			return $this->API_Request("goCalltimes", $postfields);
		}

		public function API_checkCampaign($postfields){
			return $this->API_Request("goCampaigns", $postfields);
		}

		public function API_checkUser($postfields){
			return $this->API_Request("goUsers", $postfields);
		}

		public function API_list($postfields){
			return $this->API_Request("goLists", $postfields);
		}

		public function GetSessionUser(){
			return session_user;
		}

		public function GetSessionGroup(){
			return session_usergroup;
		}	
		
		public function CheckWebrtc($user_id = null){
			$postfields = array(
				"goAction" => "goCheckWebrtc",
				"user_id" => $user_id
			);
		$result = $this->API_Request("goSettings", $postfields);
				return $result->result;
		}
		
		public function CheckPhones(){
			$postfields = array(
				"goAction" => "goCheckPhones"
			);
			$result = $this->API_Request("goSettings", $postfields);
			return $result->result;
		}

		public function CheckChat($user_id = null){
                        $postfields = array(
                                "goAction" => "goCheckChat",
                                "user_id" => $user_id
                        );
                $result = $this->API_Request("goSettings", $postfields);
                                return $result->result;
                }

		//Agent Chat
		public function API_AgentChatActivation($postfields){
			return $this->API_Request("goAgentChat", $postfields);
		}

		public function API_getUserDetails($userid){
                        $postfields = array(
                                'goAction' => 'goGetUserInfo',
                                'userid' => $userid
                        );
                        return $this->API_Request("goAgentChat", $postfields);
                }
		
		public function API_chatUsers($userid){
			$postfields = array(
				'goAction' => 'goGetChatUsers',
				'userid' => $userid
			);	
			return $this->API_Request("goAgentChat", $postfields);
		}

		public function API_insertChat($to_user_id, $userid, $chat_message){
			$postfields = array(
				'goAction' => 'goInsertChat',
				'to_user_id' => $to_user_id,
				'userid' => $userid,
				'chat_message' => $chat_message
			);	
			return $this->API_Request("goAgentChat", $postfields);
		}

		public function API_showUserChat($userid, $to_user_id){
			$postfields = array(
				'goAction' => 'goEditUserStatus',
				'userid' => $userid,
				'to_user_id' => $to_user_id
			);	
			return $this->API_Request("goAgentChat", $postfields);
		}

		public function API_getUserChat($userid, $to_user_id, $action){
			$postfields = array(
				'goAction' => 'goGetUserChat',
				'userid' => $userid,
				'to_user_id' => $to_user_id,
				'action' => $action
			);
			return $this->API_Request("goAgentChat", $postfields);
		}

		public function API_editUserStatus($userid, $to_user_id, $chat_action){
                        $postfields = array(
                                'goAction' => 'goEditUserStatus',
                                'userid' => $userid,
                                'to_user_id' => $to_user_id,
				'chat_action' => $chat_action
                        );
                        return $this->API_Request("goAgentChat", $postfields);
                }

		public function API_getUnreadMessageCount($to_user_id, $userid){
			$postfields = array(
				'goAction' => 'goGetUnreadMessages',
				'to_user_id' => $userid,
				'userid' => $to_user_id
			);
			return $this->API_Request("goAgentChat", $postfields);
		}

		public function API_updateTypingStatus($is_type, $login_details_id){
			$postfields = array(
				'goAction' => 'goUpdateTypingStatus',
				'is_type' => $is_type,
				'login_details_id' => $login_details_id
			);
			return $this->API_Request("goAgentChat", $postfields);
		}

		public function API_fetchIsTypeStatus($userid){
			$postfields = array(
				'goAction' => 'goFetchIsTypeStatus',
				'userid' => $userid,
			);
			return $this->API_Request("goAgentChat", $postfields);
		}

		//Whatsapp
		public function API_WhatsappActivation($postfields){
			return $this->API_Request("goWhatsApp", $postfields);
		}

		public function API_getWhatsappSettings(){
			$postfields = array(
                                'goAction' => 'goGetWhatsappSettings'
                        );
                        return $this->API_Request("goWhatsApp", $postfields);
                }

		public function API_editWhatsappSetting($data){
			$postfields = array(
				'goAction' => 'goEditWhatsappSettings'
			);

			$postfields = array_merge($data, $postfields);

			return $this->API_Request("goWhatsApp", $postfields);
		}
		
		public function API_WhatsAppGetAllChat($chatId, $action, $messageId){
                        $postfields = array(
                                'goAction' => 'goGetAllChatWhatsApp',
				'chatId' => $chatId,
				'action' => $action,
				'messageId' => $messageId
                        );

                        return $this->API_Request("goWhatsApp", $postfields);
                }

		public function API_WhatsAppSend($phone, $body){
			$curl = curl_init();
			curl_setopt_array($curl, array(
			CURLOPT_URL => "https://us-central1-whatsapp-center.cloudfunctions.net/api/sendMessage?user=eu149&token=onckywrgvyoz2egw&instance=159360",
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => "",
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 0,
			CURLOPT_FOLLOWLOCATION => true,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => "POST",
			CURLOPT_POSTFIELDS =>"{\r\n  \"phone\": $phone,\r\n  \"body\": \"$body\"\r\n}",
			CURLOPT_HTTPHEADER => array(
			    "Content-Type: text/plain"
			  ),
			));
			$response = curl_exec($curl);
			curl_close($curl);
			
			return $response;
			
                }

		public function API_WhatsAppWebHookURL(){
			$getSettings = $this->API_getWhatsappSettings();
			$callbackURL = $getSettings->callback_url;
			$curl = curl_init();
			curl_setopt_array($curl, array(
			CURLOPT_URL => "https://us-central1-whatsapp-center.cloudfunctions.net/api/webhook?user=eu149&token=onckywrgvyoz2egw&instance=159360",
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => "",
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 0,
			CURLOPT_FOLLOWLOCATION => true,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => "POST",
			CURLOPT_POSTFIELDS =>"{\r\n  \"webhookUrl\": \"$callbackURL\"\r\n}",
			CURLOPT_HTTPHEADER => array(
			  "Content-Type: text/plain"
			),
			));
			$response = curl_exec($curl);
			curl_close($curl);
			
			return $response;
		}

		public function API_whatsappChatUsers($userid){
			$postfields = array(
				'goAction' => 'goGetWhatsAppContacts',
				'userid' => $userid
			);	
			return $this->API_Request("goWhatsApp", $postfields);
		}
		
		public function API_GetWhatsappDispo(){
                        $postfields = array(
                                'goAction' => 'goGetWhatsAppDispo'
                        );
                        return $this->API_Request("goWhatsApp", $postfields);
                }
		
		public function API_InsertWhatsappChatLog($chatId, $userId, $dispo, $start_time, $end_time){
                        $postfields = array(
                                'goAction' => 'goAddWhatsappChatLogs',
				'chatId' => $chatId, 
				'userId' => $userId,
				'dispo' => $dispo,
				'start_time' => $start_time,
				'end_time' => $end_time
                        );
                        return $this->API_Request("goWhatsApp", $postfields);
			//return $postfields;
                }

		public function API_whatsappInsertChat($to_user_id, $userid, $chat_message){
			$postfields = array(
				'goAction' => 'goInsertChat',
				'to_user_id' => $to_user_id,
				'userid' => $userid,
				'chat_message' => $chat_message
			);	
			return $this->API_Request("goWhatsApp", $postfields);
		}

		public function API_showWhatsappUserChat($userid, $to_user_id){
			$postfields = array(
				'goAction' => 'goEditUserStatus',
				'userid' => $userid,
				'to_user_id' => $to_user_id
			);	
			return $this->API_Request("goWhatsApp", $postfields);
		}

		public function API_getWhatsappUserChat($userid, $to_user_id, $action){
			$postfields = array(
				'goAction' => 'goGetUserChat',
				'userid' => $userid,
				'to_user_id' => $to_user_id,
				'action' => $action
			);
			return $this->API_Request("goWhatsApp", $postfields);
		}

		public function API_editWhatsappChatStatus($chatId, $chat_action){
			$postfields = array(
				'goAction' => 'goEditWhatsappChatStatus',
				'chatId' => $chatId,
				'chat_action' => $chat_action
			);
			return $this->API_Request("goWhatsApp", $postfields);
		}

		public function API_getWhatsappUnreadMessageCount($to_user_id, $userid){
			$postfields = array(
				'goAction' => 'goGetUnreadMessages',
				'to_user_id' => $userid,
				'userid' => $to_user_id
			);
			return $this->API_Request("goWhatsApp", $postfields);
		}
	
		public function API_getWhatsAppRealtimeMonitoring(){
                        $postfields = array(
                                'goAction' => 'goGetWhatsAppRealtimeMonitoring'
                        );
                        return $this->API_Request("goWhatsApp", $postfields);
                }

		public function API_getWhatsAppUsersSummary(){
                        $postfields = array(
                                'goAction' => 'goGetWhatsAppUsersSummary'
                        );
                        return $this->API_Request("goWhatsApp", $postfields);
                }

		public function API_getWhatsAppChatSummary(){
                        $postfields = array(
                                'goAction' => 'goGetWhatsAppChatSummary'
                        );
                        return $this->API_Request("goWhatsApp", $postfields);
                }

		public function API_getWhatsAppAssignedChats($userid){
                        $postfields = array(
                                'goAction' => 'goGetWhatsAppAssignedChats',
                                'userid' => $userid
                        );
                        return $this->API_Request("goWhatsApp", $postfields);
                }

		public function API_whatsAppLogin($userid){
                        $postfields = array(
                                'goAction' => 'goWhatsAppLogin',
				'userid' => $userid
                        );
                        return $this->API_Request("goWhatsApp", $postfields);
                }

		public function API_whatsAppLogout($userid){
                        $postfields = array(
                                'goAction' => 'goWhatsAppLogout',
                                'userid' => $userid
                        );
                        return $this->API_Request("goWhatsApp", $postfields);
                }
		
		public function API_whatsAppPauseResume($userid, $action){
                        $postfields = array(
                                'goAction' => 'goWhatsAppResumePause',
                                'userid' => $userid,
				'action' => $action
                        );
                        return $this->API_Request("goWhatsApp", $postfields);
                }

		public function API_whatsAppQueue(){
                        $postfields = array(
                                'goAction' => 'goAddWhatsAppQueue'
                        );
                        return $this->API_Request("goWhatsApp", $postfields);
                }

		public function API_getWhatsAppUsers(){
                        $postfields = array(
                                'goAction' => 'goGetWhatsAppUsers'
                        );
                        return $this->API_Request("goWhatsApp", $postfields);
                }

		public function API_whatsAppTransferChat($id, $userid){
                        $postfields = array(
                                'goAction' => 'goWhatsAppTransferChat',
				'id' => $id,
				'userid' => $userid
                        );
                        return $this->API_Request("goWhatsApp", $postfields);
                }

		public function API_whatsAppAssignChats(){
                        $postfields = array(
                                'goAction' => 'goWhatsAppAssignChat'
                        );
                        return $this->API_Request("goWhatsApp", $postfields);
                }

		// escape existing special characters already in the database
		function escapeJsonString($value) { # list from www.json.org: (\b backspace, \f formfeed)
			$escapers = array("\\", "/", "\"", "\n", "\r", "\t", "\x08", "\x0c", "	");
			$replacements = array("\\\\", "\\/", "\\\"", "\\n", "\\r", "\\t", "\\f", "\\b", " ");
			$result = str_replace($escapers, $replacements, $value);

			return $result;
		}
	}
	
?>
