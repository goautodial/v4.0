<?php
/**
 * @file        APIHandler.php
 * @brief       API Requests
 * @copyright   Copyright (C) GOautodial Inc.
 * @author      Alexander Jim Abenoja  <alex@goautodial.com>
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

// dependencies
require_once('CRMDefaults.php');
require_once('LanguageHandler.php');
require_once('CRMUtils.php');
require_once('goCRMAPISettings.php');
require_once('Session.php');

// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);

define("session_user", $_SESSION["user"]);
define("session_usergroup", $_SESSION["usergroup"]);
define("session_password", $_SESSION["phone_this"]);
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
     * @staticvar UIHandler $instance The UIHandler instance of this class.
     * @return UIHandler The singleton instance.
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

    public function API_getGOPackage(){
		$url = gourl."/goPackages/goAPI.php";
		$postfields["goUser"] = session_user;
		$postfields["goPass"] = session_password;
		$postfields["goAction"] = "goGetPackage";
		$postfields["responsetype"] = responsetype;

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
		
		return $output;
		
	}

    public function API_goGetGroupPermission() {
		$url = gourl."/goUserGroups/goAPI.php";
		$postfields["goUser"] = goUser;
		$postfields["goPass"] = goPass;
		$postfields["goAction"] = "goGetUserGroupInfo";
		$postfields["responsetype"] = responsetype;
		$postfields["session_user"] = session_user;		
		
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

		return $output;
	}

    public function goGetPermissions($type = 'dashboard') {
		
		$permissions = $this->API_goGetGroupPermission(session_usergroup);

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
				} else if (array_key_exists($type, $permissions)) {
					$return = $permissions->{$type};
				} else {
					$return = null;
				}
			}
		}

		return $return;
	}

	public function API_goGetAllUsers(){
		$url = gourl."/goUsers/goAPI.php"; #URL to GoAutoDial API. (required)
		$postfields["goUser"] = session_user; #Username goes here. (required)
		$postfields["goPass"] = session_password; #Password goes here. (required)
		$postfields["goAction"] = "goGetAllUsers"; #action performed by the [[API:Functions]]. (required)
		$postfields["responsetype"] = responsetype; #json. (required)
		$postfields["session_user"] = session_user; #json. (required)
		
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

		return $output;
	}

	// API to get usergroups
	public function API_getAllUserGroups() {
		$url = gourl."/goUserGroups/goAPI.php";
		$postfields = array(
			'goUser' => session_user,
			'goPass' => session_password,
			'goAction' => 'goGetAllUserGroups',		
			'responsetype' => responsetype,
			'session_user' => session_user
		);		

		// Call the API
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_TIMEOUT, 30);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
		curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postfields));
		$data = curl_exec($ch);
		curl_close($ch);
		$output = json_decode($data);

        return $output;
	}

	public function API_getAllInGroups() {
		$url = gourl."/goInbound/goAPI.php";
		$postfields = array(
			'goUser' => session_user,
			'goPass' => session_password,
			'goAction' => 'goGetAllIngroup',		
			'responsetype' => responsetype,
		);		

		// Call the API
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_TIMEOUT, 30);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
		curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postfields));
		$data = curl_exec($ch);
		curl_close($ch);
		$output = json_decode($data);

		return $output;
	}

	// Telephony IVR
	public function API_getAllIVRs() {
		$url = gourl."/goInbound/goAPI.php";
		$postfields = array(
			'goUser' => session_user,
			'goPass' => session_password,
			'goAction' => 'goGetAllIVR',		
			'responsetype' => responsetype,
			'session_user' => session_user,
			'user_group' => session_usergroup
		);		

		// Call the API
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_TIMEOUT, 30);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
		curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postfields));
		$data = curl_exec($ch);
		curl_close($ch);
		$output = json_decode($data);

		return $output;

	}

	//Telephony > phonenumber(DID)
	public function API_getPhoneNumber() {
		$url = gourl."/goInbound/goAPI.php";
		$postfields["goUser"] = session_user;
		$postfields["goPass"] = session_password;
		$postfields["goAction"] = "goGetDIDsList";
		$postfields["responsetype"] = responsetype;

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

		return $output;
	}

	public function API_getAllDIDs($campaign_id){
		$url = gourl."/goInbound/goAPI.php"; #URL to GoAutoDial API. (required)
		$postfields = array(
			'goUser' => session_user,
			'goPass' => session_password,
			'goAction' => 'goGetAllDID',		
			'responsetype' => responsetype,
			'campaign_id' => $campaign_id,
			'session_user' => session_user,
			'user_group' => session_usergroup
		);		

		// Call the API
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_TIMEOUT, 30);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
		curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postfields));
		$data = curl_exec($ch);
		curl_close($ch);
		$output = json_decode($data);

	    return $output;
	}	
	/** Voice Files API - Get all list of voice files */
	public function API_getVoiceFiles(){
	    $url = gourl."/goVoiceFiles/goAPI.php";
	    $postfields["goUser"] = session_user;
	    $postfields["goPass"] = session_password; 
	    $postfields["goAction"] = "goGetVoiceFiles";
	    $postfields["responsetype"] = responsetype;
		$postfields["session_user"] = session_user;

	    $ch = curl_init();
	    curl_setopt($ch, CURLOPT_URL, $url);
	    // curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
	    curl_setopt($ch, CURLOPT_POST, 1);
	    curl_setopt($ch, CURLOPT_TIMEOUT, 100);
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	    curl_setopt($ch, CURLOPT_POSTFIELDS, $postfields);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	    $data = curl_exec($ch);
	    curl_close($ch);
	    $output = json_decode($data);

	    return $output;
	}

	// Telephony Users -> Phone
	public function API_getAllPhones(){
		$url = gourl."/goPhones/goAPI.php";
		$postfields["goUser"] = session_user;
		$postfields["goPass"] = session_password;
		$postfields["goAction"] = "goGetAllPhones";
		$postfields["responsetype"] = responsetype;
		$postfields["session_user"] = session_user;
		
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

		return $output;
	}
	
	public function API_getAllCampaigns(){
		$url = gourl."/goCampaigns/goAPI.php"; #URL to GoAutoDial API. (required)	
		$postfields = array(
			'goUser' => session_user,
			'goPass' => session_password,
			'goAction' => 'goGetAllCampaigns',		
			'responsetype' => responsetype,			
			'session_user' => session_user,
			'user_group' => session_usergroup
		);		

		// Call the API
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_TIMEOUT, 30);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
		curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postfields));
		$data = curl_exec($ch);
		curl_close($ch);
		$output = json_decode($data);

		return $output;
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
		
	/*
	 * Displaying Disposition
	 * [[API: Function]] - getAllDispositions
	 * 	This application is used to get list of campaign belongs to user.
	*/
	public function API_getAllDispositions($custom){
        $url = gourl."/goDispositions/goAPI.php"; #URL to GoAutoDial API. (required)
		$postfields = array(
			'goUser' => session_user,
			'goPass' => session_password,
			'goAction' => 'getAllDispositions',		
			'responsetype' => responsetype,
			'custom_request' => $custom,
			'session_user' => session_user
		);		

		// Call the API
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_TIMEOUT, 30);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
		curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postfields));
		$data = curl_exec($ch);
		curl_close($ch);
		$output = json_decode($data);
	
		return $output;
	}	
	
	public function API_getDispositionInfo($campid){
        $url = gourl."/goDispositions/goAPI.php"; #URL to GoAutoDial API. (required)
		$postfields = array(
			'goUser' => session_user,
			'goPass' => session_password,
			'goAction' => 'goGetDispositionInfo',		
			'responsetype' => responsetype,
			'campaign_id' => $campid,
			'session_user' => session_user,
			'user_group' => session_usergroup
		);		

		// Call the API
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_TIMEOUT, 30);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
		curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postfields));
		$data = curl_exec($ch);
		curl_close($ch);
		$output = json_decode($data);
		
		return $output;
	}
	
	public function API_getLeadRecycling(){
		$url = gourl."/goLeadRecycling/goAPI.php"; #URL to GoAutoDial API. (required)
		$postfields = array(
			'goUser' => session_user,
			'goPass' => session_password,
			'goAction' => 'goGetAllLeadRecycling',		
			'responsetype' => responsetype,
			'session_user' => session_user
		);		

		// Call the API
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_TIMEOUT, 30);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
		curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postfields));
		$data = curl_exec($ch);
		curl_close($ch);
		$output = json_decode($data);

	    return $output;
	}	
	
	public function API_getAllDialStatuses($campaign_id){
		$url = gourl."/goDialStatus/goAPI.php"; #URL to GoAutoDial API. (required)
		$postfields = array(
			'goUser' => session_user,
			'goPass' => session_password,
			'goAction' => 'goGetAllDialStatuses',		
			'responsetype' => responsetype,
			'campaign_id' => $campaign_id,
			'session_user' => session_user
		);		

		// Call the API
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_TIMEOUT, 30);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
		curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postfields));
		$data = curl_exec($ch);
		curl_close($ch);
		$output = json_decode($data);
		
	    return $output;
	}	
	
	public function API_getAllDialStatusesSurvey($campaign_id){
		$url = gourl."/goDialStatus/goAPI.php"; #URL to GoAutoDial API. (required)
		$postfields = array(
			'goUser' => session_user,
			'goPass' => session_password,
			'goAction' => 'goGetAllDialStatuses',		
			'responsetype' => responsetype,
			'campaign_id' => $campaign_id,
			'hotkeys_only' => 1,
			'session_user' => session_user
		);		

		// Call the API
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_TIMEOUT, 30);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
		curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postfields));
		$data = curl_exec($ch);
		curl_close($ch);
		$output = json_decode($data);
		
		return $output;
	}	
	/*
	 * Displaying Lead Filter
	 * [[API: Function]] - getAllLeadFilters
	 * 	This application is used to get list of lead filter belongs to user.
	*/
	public function API_getAllLeadFilters(){
        $url = gourl."/goLeadFilters/goAPI.php"; #URL to GoAutoDial API. (required)
		$postfields = array(
			'goUser' => session_user,
			'goPass' => session_password,
			'goAction' => 'goGetAllLeadFilters',		
			'responsetype' => responsetype,
			'session_user' => session_user
		);		

		// Call the API
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_TIMEOUT, 30);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
		curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postfields));
		$data = curl_exec($ch);
		curl_close($ch);
		$output = json_decode($data);
		
		return $output;
	}	
	
	public function API_getCountryCodes(){
		$url = gourl."/goCountryCode/goAPI.php"; #URL to GoAutoDial API. (required)
		$postfields = array(
			'goUser' => session_user,
			'goPass' => session_password,
			'goAction' => 'getAllCountryCodes',		
			'responsetype' => responsetype,
			'session_user' => session_user
		);		

		// Call the API
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_TIMEOUT, 30);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
		curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postfields));
		$data = curl_exec($ch);
		curl_close($ch);
		$output = json_decode($data);

		return $output;
	}	
	
	public function API_getAllLists(){
		$url = gourl."/goLists/goAPI.php"; #URL to GoAutoDial API. (required)
		$postfields = array(
			'goUser' => session_user,
			'goPass' => session_password,
			'goAction' => 'goGetAllLists',		
			'responsetype' => responsetype,
			'session_user' => session_user,
			'user_group' => session_usergroup
		);		

		// Call the API
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_TIMEOUT, 30);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
		curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postfields));
		$data = curl_exec($ch);
		curl_close($ch);
		$output = json_decode($data);

		return $output;
	}	
	
	public function API_getAllCarriers(){
		$url = gourl."/goCarriers/goAPI.php"; #URL to GoAutoDial API. (required)
		$postfields = array(
			'goUser' => session_user,
			'goPass' => session_password,
			'goAction' => 'goGetAllCarriers',		
			'responsetype' => responsetype,
		);		

		// Call the API
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_TIMEOUT, 30);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
		curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postfields));
		$data = curl_exec($ch);
		curl_close($ch);
		$output = json_decode($data);

	    return $output;
	}	
	
	public function API_getAllCampaignDialStatuses($campaign_id){
		$url = gourl."/goDialStatus/goAPI.php"; #URL to GoAutoDial API. (required)
		$postfields = array(
			'goUser' => session_user,
			'goPass' => session_password,
			'goAction' => 'goGetAllCampaignDialStatuses',		
			'responsetype' => responsetype,
			'campaign_id' => $campaign_id
		);		

		// Call the API
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_TIMEOUT, 30);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
		curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postfields));
		$data = curl_exec($ch);
		curl_close($ch);
		$output = json_decode($data);

	    return $output;
	}	
	
	public function API_getCampaignInfo($campid){
		$url = gourl."/goCampaigns/goAPI.php"; #URL to GoAutoDial API. (required)
		$postfields = array(
			'goUser' => session_user,
			'goPass' => session_password,
			'goAction' => 'goGetCampaignInfo',		
			'responsetype' => responsetype,
			'campaign_id' => $campid,
			'session_user' => session_user,
			'user_group' => session_usergroup			
		);		

		// Call the API
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_TIMEOUT, 30);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
		curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postfields));
		$data = curl_exec($ch);
		curl_close($ch);
		$output = json_decode($data);
		
		return $output;
	}	
	
	public function API_getAllUsers(){
		$url = gourl."/goUsers/goAPI.php"; #URL to GoAutoDial API. (required)
		$postfields = array(
			'goUser' => session_user,
			'goPass' => session_password,
			'goAction' => 'goGetAllUsers',		
			'responsetype' => responsetype,
			'session_user' => session_user
		);		

		// Call the API
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_TIMEOUT, 30);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
		curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postfields));
		$data = curl_exec($ch);
		curl_close($ch);
		$output = json_decode($data);

		 return $output;
	}	
	
	public function API_getCalltimes(){
		$url = gourl."/goCalltimes/goAPI.php"; #URL to GoAutoDial API. (required)
		$postfields = array(
			'goUser' => session_user,
			'goPass' => session_password,
			'goAction' => 'goGetAllCalltimes',		
			'responsetype' => responsetype,
			'session_user' => session_user,
			'user_group' => session_usergroup			
		);		

		// Call the API
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_TIMEOUT, 30);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
		curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postfields));
		$data = curl_exec($ch);
		curl_close($ch);
		$output = json_decode($data);

	    return $output;
	}
	
	public function API_getAllScripts(){
		$url = gourl."/goScripts/goAPI.php"; #URL to GoAutoDial API. (required)
		$postfields = array(
			'goUser' => session_user,
			'goPass' => session_password,
			'goAction' => 'goGetAllScripts',		
			'responsetype' => responsetype,
			'session_user' => session_user,
			'user_group' => session_usergroup			
		);		

		// Call the API
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_TIMEOUT, 30);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
		curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postfields));
		$data = curl_exec($ch);
		curl_close($ch);
		$output = json_decode($data);        

		 return $output;
	}	
	
	public function API_getAllVoicemails() {

		$url = gourl."/goVoicemails/goAPI.php"; #URL to GoAutoDial API. (required)
		$postfields = array(
			'goUser' => session_user,
			'goPass' => session_password,
			'goAction' => 'goGetAllVoicemails',		
			'responsetype' => responsetype,
			'session_user' => session_user,
			'user_group' => session_usergroup			
		);		

		// Call the API
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_TIMEOUT, 30);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
		curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postfields));
		$data = curl_exec($ch);
		curl_close($ch);
		$output = json_decode($data);

		return $output;

	}	
	
}

?>
