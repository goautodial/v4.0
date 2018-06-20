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
require_once('ModuleHandler.php');
require_once('goCRMAPISettings.php');
require_once('Session.php');

// constants
define ('CRM_UI_DEFAULT_RESULT_MESSAGE_TAG', "resultmessage");
error_reporting(E_ERROR | E_PARSE);

/**
 *  UIHandler.
 *  This class is in charge of generating the dynamic HTML code for the basic functionality of the system.
 *  Every time a page view has to generate dynamic contact, it should do so by calling some of this class methods.
 *  UIHandler uses the Singleton pattern, thus gets instanciated by the UIHandler::getInstante().
 *  This class is supposed to work as a ViewController, stablishing the link between the view (PHP/HTML view pages) and the Controller (DbHandler).
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

	public function API_goGetAllUsers($user = $_SESSION['user']){
		$url = gourl."/goUsers/goAPI.php"; #URL to GoAutoDial API. (required)
		$postfields["goUser"] = goUser; #Username goes here. (required)
		$postfields["goPass"] = goPass; #Password goes here. (required)
		$postfields["goAction"] = "goGetAllUsers"; #action performed by the [[API:Functions]]. (required)
		$postfields["responsetype"] = responsetype; #json. (required)
		$postfields["session_user"] = $_SESSION['user']; #json. (required)
		
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

		 return $output;
	}

	// get user info
	public function goGetUserInfo($userid, $type, $filter, $session_user = $_SESSION['user'], $session_usergroup = $_SESSION['usergroup']){
		$url = gourl."/goUsers/goAPI.php"; #URL to GoAutoDial API. (required)
		$postfields["goUser"] = goUser; 
		$postfields["goPass"] = goPass; 
		$postfields["goAction"] = "goGetUserInfo"; 
		$postfields["responsetype"] = responsetype; 
		if ($type == "user") {
			$postfields["user"] = $userid; 
		} else {
			$postfields["user_id"] = $userid; 
		}
		if($filter == "userInfo"){
			$postfields["filter"] = $filter;
		}
		
		$postfields["log_user"] = $session_user;
		$postfields["log_group"] = $session_usergroup;
		$postfields["log_ip"] = $_SERVER['REMOTE_ADDR'];

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

		return $output;
	}
	
	public function goGetUserInfoNew($userid){
		$url = gourl."/goUsers/goAPI.php"; #URL to GoAutoDial API. (required)
		$postfields["goUser"] = goUser; #Username goes here. (required)
		$postfields["goPass"] = goPass; #Password goes here. (required)
		$postfields["goAction"] = "goGetUserInfoNew"; #action performed by the [[API:Functions]]. (required)
		$postfields["responsetype"] = responsetype; #json. (required)
		$postfields["user_id"] = $userid; #Desired User (required)		
		$postfields["log_user"] = $_SESSION['user'];
		$postfields["log_group"] = $_SESSION['usergroup'];
		$postfields["log_ip"] = $_SERVER['REMOTE_ADDR'];

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

		return $output;
	}	

	// get user list
	public function goGetAllUserList($user, $perm) {

	$output = $this->API_goGetAllUserLists($user);
		$checkbox_all = $this->getCheckAll("user", $perm);
		if($perm->user_delete !== 'N')
       	    $columns = array("     ", $checkbox_all,$this->lh->translationFor("user_id"), $this->lh->translationFor("full_name"), $this->lh->translationFor("user_group"), $this->lh->translationFor("status"), $this->lh->translationFor("action"));
	    else
		$columns = array("     ",$this->lh->translationFor("user_id"), $this->lh->translationFor("full_name"), $this->lh->translationFor("user_group"), $this->lh->translationFor("status"), $this->lh->translationFor("action"));
		   $hideOnMedium = array($this->lh->translationFor("user_group"), $this->lh->translationFor("status"));
	       $hideOnLow = array($this->lh->translationFor("agent_id"), $this->lh->translationFor("user_group"), $this->lh->translationFor("status"));
		   $result = $this->generateTableHeaderWithItems($columns, "T_users", "table-bordered table-striped", true, false, $hideOnMedium, $hideOnLow);
		
	       // iterate through all users
			for($i=0;$i<count($output->user_id);$i++){
				if($output->active[$i] == "Y"){
					$output->active[$i] = $this->lh->translationFor("active");
				}else{
					$output->active[$i] = $this->lh->translationFor("inactive");
				}
				$role = $output->user_level[$i];
					$action = $this->getUserActionMenuForT_User($output->user_id[$i], $output->user[$i], $output->user_level[$i], $output->full_name[$i], $user, $perm);
					//$sessionAvatar = "<avatar username='".$output->full_name[$i]."' :size='36'></avatar>";
					$avatar = NULL;
					if ($this->db->getUserAvatar($output->user_id[$i])) {
						$avatar = "./php/ViewImage.php?user_id=" . $output->user_id[$i];
					}
					$sessionAvatar = $this->getVueAvatar($output->full_name[$i], $avatar, 36);
					
					$preFix = "<a class='edit-T_user' data-id=".$output->user_id[$i]." data-user=".$user." data-role=".$role.">"; 
					$sufFix = "</a>";
					if ($perm->user_update === 'N') {
						$preFix = '';
						$sufFix = '';
					}
					$checkbox = '<label for="'.$output->user_id[$i].'"'.($perm->user_delete === 'N' ? ' class="hidden"' : '').'><div class="checkbox c-checkbox"><label><input name="" class="check_user" id="'.$output->user_id[$i].'" type="checkbox" value="Y"><span class="fa fa-check"></span> </label></div></label>';
					
					$result .= "<tr>
									<td style='width:5%;'>".$sessionAvatar."</a></td>";
					if($perm->user_delete !== 'N')
						$result .= "<td style='width:10%;'>".$checkbox."</td>";
						$result .= "<td class='hide-on-low'>".$preFix."<strong>".$output->user[$i]."</strong>".$sufFix."</td>
									<td>".$output->full_name[$i]."</td>
									<td class=' hide-on-low hide-on-medium'>".$output->user_group[$i]."</td>
									<td class='hide-on-low hide-on-medium'>".$output->active[$i]."</td>
									<td nowrap style='width:16%;'>".$action."</td>
								</tr>";
				}

	       // print suffix
	       //$result .= $this->generateTableFooterWithItems($columns, true, false, $hideOnMedium, $hideOnLow);

			return $result.'</table>';
	}


	public function API_goGetAllLists($user_group = ''){
		$url = gourl."/goLists/goAPI.php"; #URL to GoAutoDial API. (required)
		$postfields["goUser"] = goUser; #Username goes here. (required)
		$postfields["goPass"] = goPass; #Password goes here. (required)
		$postfields["goAction"] = "goGetAllLists"; #action performed by the [[API:Functions]]. (required)
		$postfields["responsetype"] = responsetype; #json. (required)
		$postfields["user_group"] = $user_group;

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

		return $output;
	}

	private function ActionMenuForLists($id, $name) {

	   return '<div class="btn-group">
		    <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">'.$this->lh->translationFor("choose_action").'
		    <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" style="height: 34px;">
					    <span class="caret"></span>
					    <span class="sr-only">Toggle Dropdown</span>
		    </button>
		    <ul class="dropdown-menu" role="menu">
			<li><a class="edit-list" href="#" data-id="'.$id.'" data-name="'.$name.'">'.$this->lh->translationFor("modify").'</a></li>
			<li class="divider"></li>
			<li><a class="delete-list" href="#" data-id="'.$id.'" data-name="'.$name.'">'.$this->lh->translationFor("delete").'</a></li>
		    </ul>
		</div>';
	}


	// API to get usergroups
	public function API_goGetUserGroupsList() {
		require_once('Session.php');
		$url = gourl."/goUserGroups/goAPI.php"; #URL to GoAutoDial API. (required)
        $postfields["goUser"] = goUser; #Username goes here. (required)
        $postfields["goPass"] = goPass; #Password goes here. (required)
        $postfields["goAction"] = "goGetAllUserGroups"; #action performed by the [[API:Functions]]. (required)
        $postfields["responsetype"] = "json"; #json. (required)
		$postfields["session_user"] = $_SESSION['user']; #json. (required)
		$postfields["group_id"] = $_SESSION['usergroup']; #json. (required)
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

         return $output;
        /*
        if ($output->result=="success") {
           # Result was OK!
                        for($i=0;$i<count($output->user_group);$i++){
                                echo $output->user_group[$i]."</br>";
                                echo $output->group_name[$i]."</br>";
                                echo $output->group_type[$i]."</br>";
                                echo $output->forced_timeclock_login[$i]."</br>";
                        }
         } else {
           # An error occured
                echo "The following error occured: ".$results["message"];
        }
		*/
	}
	//USERGROUPS LIST
	public function goGetUserGroupsList() {
		$output = $this->API_goGetUserGroupsList();

		if ($output->result=="success") {
		# Result was OK!

		$columns = array($this->lh->translationFor('user_group'), $this->lh->translationFor('group_name'), $this->lh->translationFor('type'), $this->lh->translationFor('force_timeclock'), $this->lh->translationFor('action'));
	    $hideOnMedium = array($this->lh->translationFor('type'), $this->lh->translationFor('force_timeclock'));
	    $hideOnLow = array($this->lh->translationFor('user_group'), $this->lh->translationFor('type'), $this->lh->translationFor('force_timeclock'));
		$result = $this->generateTableHeaderWithItems($columns, "usergroups_table", "table-bordered table-striped", true, false, $hideOnMedium, $hideOnLow);


			for($i=0;$i < count($output->user_group);$i++){

				if($output->forced_timeclock_login[$i] == "Y"){
					$output->forced_timeclock_login[$i] = $this->lh->translationFor('go_yes');
				}else{
					$output->forced_timeclock_login[$i] = $this->lh->translationFor('go_no');
				}

				$action = $this->ActionMenuForUserGroups($output->user_group[$i], $output->group_name[$i]);

				$result = $result."<tr>
	                    <td class='hide-on-low'><a class='edit-usergroup' data-id='".$output->user_group[$i]."'>".$output->user_group[$i]."</a></td>
	                    <td>".$output->group_name[$i]."</td>
	                    <td class='hide-on-medium hide-on-low'>".$output->group_type[$i]."</td>
	                    <td class='hide-on-medium hide-on-low'>".$output->forced_timeclock_login[$i]."</td>
	                    <td nowrap>".$action."</td>
	                </tr>";

			}

			return $result.'</table>';

		} else {
		# An error occured
			return $this->calloutWarningMessage($this->lh->translationFor("No Entry in Database"));
		}

	}
	private function ActionMenuForUserGroups($id, $name) {

	   return '<div class="btn-group">
		    <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">'.$this->lh->translationFor("choose_action").'
		    <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" style="height: 34px;">
					    <span class="caret"></span>
					    <span class="sr-only">Toggle Dropdown</span>
		    </button>
		    <ul class="dropdown-menu" role="menu">
			<li><a class="edit-usergroup" href="#" data-id="'.$id.'" data-name="'.$name.'">'.$this->lh->translationFor("modify").'</a></li>
			<li class="divider"></li>
			<li><a class="delete-usergroup" href="#" data-id="'.$id.'" data-name="'.$name.'">'.$this->lh->translationFor("delete").'</a></li>
		    </ul>
		</div>';
	}

// TELEPHONY INBOUND
	//ingroups
	public function API_getInGroups($user_group = '') {
		$url = gourl."/goInbound/goAPI.php"; #URL to GoAutoDial API. (required)
		$postfields["goUser"] = goUser; #Username goes here. (required)
		$postfields["goPass"] = goPass; #Password goes here. (required)
		$postfields["goAction"] = "goGetAllInboundList"; #action performed by the [[API:Functions]]. (required)
		$postfields["responsetype"] = responsetype; #json. (required)
		$postfields["user_group"] = $user_group;

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

		return $output;
	}

	// Telephony IVR
	public function API_getIVR($user_group = '') {
		$url = gourl."/goInbound/goAPI.php"; #URL to GoAutoDial API. (required)
		$postfields["goUser"] = goUser; #Username goes here. (required)
		$postfields["goPass"] = goPass; #Password goes here. (required)
		$postfields["goAction"] = "goGetIVRMenusList"; #action performed by the [[API:Functions]]. (required)
		$postfields["responsetype"] = responsetype; #json. (required)
		$postfields["user_group"] = $user_group;

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

		return $output;

	}

	//Telephony > phonenumber(DID)
	public function API_getPhoneNumber($user_group = '') {
		$url = gourl."/goInbound/goAPI.php"; #URL to GoAutoDial API. (required)
		$postfields["goUser"] = goUser; #Username goes here. (required)
		$postfields["goPass"] = goPass; #Password goes here. (required)
		$postfields["goAction"] = "goGetDIDsList"; #action performed by the [[API:Functions]]. (required)
		$postfields["responsetype"] = responsetype; #json. (required)
		$postfields["user_group"] = $user_group;

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

		return $output;

	}

	public function API_goGetAllAgentRank($user_id, $group_id) {
		$url = gourl."/goInbound/goAPI.php"; #URL to GoAutoDial API. (required)
		$postfields["goUser"] = goUser; #Username goes here. (required)
		$postfields["goPass"] = goPass; #Password goes here. (required)
		$postfields["goAction"] = "goGetAllAgentRank"; #action performed by the [[API:Functions]]. (required)
		$postfields["responsetype"] = responsetype; #json. (required)
		$postfields["user_id"] = $user_id;
		$postfields["group_id"] = $group_id;
		//$postfields["goVarLimit"] = 10;

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

		return $output;

	}

	/*
	 *
	 * SETTINGS MENU
	 *
	*/
	// Settings > Admin Logs
	public function API_goGetAdminLogsList($group, $limit){
		$url = gourl."/goAdminLogs/goAPI.php"; #URL to GoAutoDial API. (required)
		$postfields["goUser"] = goUser; #Username goes here. (required)
		$postfields["goPass"] = goPass; #Password goes here. (required)
		$postfields["goAction"] = "goGetAdminLogsList"; #action performed by the [[API:Functions]]. (required)
		$postfields["goUserGroup"] = $group;
		$postfields["limit"] = $limit;
		$postfields["responsetype"] = responsetype; #json. (required)

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
		//var_dump($output);
		return $output;
	}

	public function getAdminLogsList($group, $limit) {
		$output = $this->API_goGetAdminLogsList($group, $limit);

		if ($output->result=="success") {
		# Result was OK!

			$columns = array($this->lh->translationFor('user'), $this->lh->translationFor('ip_address'), $this->lh->translationFor('date_and_time'), $this->lh->translationFor('action'), $this->lh->translationFor('details'), $this->lh->translationFor('sql_query'));
			$result = $this->generateTableHeaderWithItems($columns, "adminlogs_table", "table-bordered table-striped", true, false);
	
			foreach ($output->data as $log) {
				$details = stripslashes($log->details);
				$db_query = stripslashes($log->db_query);
				//$details = (strlen($details) > 30) ? substr($details, 0, 30) . "..." : $details;
				//$db_query = (strlen($db_query) > 30) ? substr($db_query, 0, 30) . "..." : $db_query;
				$result = $result."<tr>
					<td><span class='hidden-xs'>".$log->name. " (".$log->user.")</span><span class='visible-xs'>".$log->user."</span></td>
					<td><a href='http://www.ip-tracker.org/locator/ip-lookup.php?ip=".$log->ip_address."' target='_new'>".$log->ip_address."</a></td>
					<td>".$log->event_date."</td>
					<td>".$log->action."</td>
					<td title=\"".stripslashes($log->details)."\">".$details."</td>
					<td title=\"".stripslashes($log->db_query)."\">".$db_query."</td></tr>";
			}

			return $result.'</table>';

		} else {
			return $output->result;
		}
	}

	// Settings > Phone
	public function API_getPhonesList(){
		$url = gourl."/goPhones/goAPI.php"; #URL to GoAutoDial API. (required)
		$postfields["goUser"] = goUser; #Username goes here. (required)
		$postfields["goPass"] = goPass; #Password goes here. (required)
		$postfields["goAction"] = "goGetAllPhones"; #action performed by the [[API:Functions]]. (required)
		$postfields["responsetype"] = responsetype; #json. (required)
		$postfields["session_user"] = $_SESSION['user']; #json. (required)
		
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

		return $output;
	}

	public function getPhonesList() {
		$output = $this->API_getPhonesList();
		
		if ($output->result=="success") {
		# Result was OK!
		$checkbox_all = $this->getCheckAll("phone");
		//if($perm->user_delete !== 'N')
			$columns = array($this->lh->translationFor("extension"), $checkbox_all,$this->lh->translationFor("protocol"),$this->lh->translationFor("server_ip"), $this->lh->translationFor("status"), $this->lh->translationFor("voicemail"), $this->lh->translationFor("action"));
	    //else
		//	$columns = array($this->lh->translationFor("extension"), $this->lh->translationFor("protocol"), $this->lh->translationFor("server_ip"), $this->lh->translationFor("status"), $this->lh->translationFor("voicemail"), $this->lh->translationFor("action"));
	    $hideOnMedium = array($this->lh->translationFor("server_ip"), $this->lh->translationFor("status"), $this->lh->translationFor("vmail"));
	    $hideOnLow = array($this->lh->translationFor("server_ip"), $this->lh->translationFor("status"), $this->lh->translationFor("vmail"));
		$result = $this->generateTableHeaderWithItems($columns, "T_phones", "table-bordered table-striped", true, false, $hideOnMedium, $hideOnLow);

			for($i=0;$i < count($output->extension);$i++){

				if($output->active[$i] == "Y"){
					$output->active[$i] = $this->lh->translationFor("active");
				}else{
					$output->active[$i] = $this->lh->translationFor("inactive");
				}

				if($output->messages[$i] == NULL){
					$output->messages[$i] = 0;
				}
				if($output->old_messages[$i] == NULL){
					$output->old_messages[$i] = 0;
				}
				$checkbox = '<label for="'.$output->extension[$i].'"><div class="checkbox c-checkbox"><label><input name="" class="check_phone" id="'.$output->extension[$i].'" type="checkbox" value="Y"><span class="fa fa-check"></span> </label></div></label>';
				$action = $this->getUserActionMenuForPhones($output->extension[$i]);
                //$sessionAvatar = "<avatar username='".$output->messages[$i]."' :size='36'></avatar><td>".$sessionAvatar."</a></td>";
				
				$result = $result."<tr>
	                    <td><a class='edit-phone' data-id='".$output->extension[$i]."'><strong>".$output->extension[$i]."</strong></a></td>";
				$result .= "<td style='width:10%;'>".$checkbox."</td>
						<td class='hide-on-medium hide-on-low'>".$output->protocol[$i]."</td>
						<td class='hide-on-medium hide-on-low'>".$output->server_ip[$i]."</td>
	                    <td class='hide-on-medium hide-on-low'>".$output->active[$i]."</td>
						<td class='hide-on-medium hide-on-low'>".$output->messages[$i]."&nbsp;<font style='padding-left: 50px;'>".$output->old_messages[$i]."</font></td>
						<td nowrap style='width:16%;'>".$action."</td>
	                </tr>";

			}

			return $result.'</table>';

		} else {
		# An error occured
			return $this->calloutErrorMessage($this->lh->translationFor("Unable to get Phone List"));
		}
	       // print suffix
	       //$result .= $this->generateTableFooterWithItems($columns, true, false, $hideOnMedium, $hideOnLow);
	}

	// VoiceMails
	public function API_goGetVoiceMails() {

		$url = gourl."/goVoicemails/goAPI.php"; #URL to GoAutoDial API. (required)
        $postfields["goUser"] = goUser; #Username goes here. (required)
        $postfields["goPass"] = goPass; #Password goes here. (required)
        $postfields["goAction"] = "goGetAllVoiceFiles"; #action performed by the [[API:Functions]]. (required)
        $postfields["responsetype"] = responsetype; #json. (required)

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

		//var_dump($output);
		return $output;

	}

	public function getVoiceMails() {
		$output = $this->API_goGetVoiceMails();

		if ($output->result=="success") {
		# Result was OK!

		$columns = array($this->lh->translationFor('voicemail_id'), $this->lh->translationFor('name'), $this->lh->translationFor('status'), $this->lh->translationFor('new_message'), $this->lh->translationFor('old_message'), $this->lh->translationFor('delete'), $this->lh->translationFor('user_group'), $this->lh->translationFor('action'));
	    $hideOnMedium = array($this->lh->translationFor('status'), $this->lh->translationFor('new_message'), $this->lh->translationFor('old_message'), $this->lh->translationFor('delete'), $this->lh->translationFor('user_group'));
	    $hideOnLow = array($this->lh->translationFor('voicemail_id'), $this->lh->translationFor('status'), $this->lh->translationFor('new_message'), $this->lh->translationFor('old_message'), $this->lh->translationFor('delete'), $this->lh->translationFor('user_group'));
		$result = $this->generateTableHeaderWithItems($columns, "voicemails_table", "table-bordered table-striped", true, false, $hideOnMedium, $hideOnLow);

			for($i=0;$i < count($output->voicemail_id);$i++){

				if($output->active[$i] == "Y"){
					$output->active[$i] = $this->lh->translationFor('active');
				}else{
					$output->active[$i] = $this->lh->translationFor('inactive');
				}

				$action = $this->ActionMenuForVoicemail($output->voicemail_id[$i], $output->fullname[$i]);

				$result = $result."<tr>
	                    <td class='hide-on-low'><a class='edit-voicemail' data-id='".$output->voicemail_id[$i]."''>".$output->voicemail_id[$i]."</a></td>
	                    <td>".$output->fullname[$i]."</a></td>
						<td class='hide-on-medium hide-on-low'>".$output->active[$i]."</td>
	                    <td class='hide-on-medium hide-on-low'>".$output->messages[$i]."</td>
	                    <td class='hide-on-medium hide-on-low'>".$output->old_messages[$i]."</td>
						<td class='hide-on-medium hide-on-low'>".$output->delete_vm_after_email[$i]."</td>
						<td class='hide-on-medium hide-on-low'>".$output->user_group[$i]."</td>
	                    <td nowrap>".$action."</td>
	                </tr>";

			}

			return $result.'</table>';

		}else{
			// if no entry in voicemails
			return $this->calloutWarningMessage($this->lh->translationFor("No Entry in Database"));
		}
	}

	private function ActionMenuForVoicemail($id, $name) {

	   return '<div class="btn-group">
		    <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">'.$this->lh->translationFor("choose_action").'
		    <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" style="height: 34px;">
					    <span class="caret"></span>
					    <span class="sr-only">Toggle Dropdown</span>
		    </button>
		    <ul class="dropdown-menu" role="menu">
			<li><a class="edit-voicemail" href="#" data-id="'.$id.'" data-name="'.$name.'">'.$this->lh->translationFor("modify").'</a></li>
			<li class="divider"></li>
			<li><a class="delete-voicemail" href="#" data-id="'.$id.'" data-name="'.$name.'">'.$this->lh->translationFor("delete").'</a></li>
		    </ul>
		</div>';
	}

	/** Getting Circle Buttons */

	/**
	 * Generates action circle buttons for different pages/module
	 * @param page name of page/current page
	 * @param icon will determine what icon to be use for the button
	 */
	public function getCircleButton($page, $icon){
	    $theme = $this->db->getSettingValueForKey(CRM_SETTING_THEME);
	    if(empty($theme)){
	    	$theme = 'blue';
	    }

	    // this will be the output html
	    $button = "";
	    $button .= '<a button-area add-'.$page.'">';
	    $button .= '<div class="circle-button skin-'.$theme.'">';
	    $button .= '<em class="fa fa-'.$icon.' button-area add-'.$page.'"></em>';
	    $button .= '</div>';
	    $button .= '</a>';
	    return $button;
	}

	/** Campaigns API - Get all list of campaign */
	/**
	 * @param goUser
	 * @param goPass
	 * @param goAction
	 * @param responsetype
	 */

	public function API_getRealtimeAgent($goUser, $goPass, $goAction, $responsetype){
	    $url = gourl."/goBarging/goAPI.php"; #URL to GoAutoDial API. (required)
	    $postfields["goUser"] = goUser; #Username goes here. (required)
	    $postfields["goPass"] = goPass; #Password goes here. (required)
	    $postfields["goAction"] = "goGetAgentsOnCall"; #action performed by the [[API:Functions]]. (required)
	    $postfields["responsetype"] = responsetype; #json. (required)

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

	#### OLD PARAMS: $goUser, $goPass, $goAction, $responsetype
	public function API_getListAllCampaigns($user_group){
	    $url = gourl."/goCampaigns/goAPI.php"; #URL to GoAutoDial API. (required)
	    $postfields["goUser"] = goUser; #Username goes here. (required)
	    $postfields["goPass"] = goPass; #Password goes here. (required)
	    $postfields["goAction"] = "getAllCampaigns"; #action performed by the [[API:Functions]]. (required)
		$postfields["user_group"] = $_SESSION['usergroup'];
		$postfields["session_user"] = $_SESSION['user'];
	    $postfields["responsetype"] = responsetype; #json. (required)

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

	public function ActionMenuForCampaigns($id, $name, $perm) {

	    return '<div class="btn-group">
		    <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">'.$this->lh->translationFor("choose_action").'
		    <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" style="height: 34px;">
					    <span class="caret"></span>
					    <span class="sr-only">Toggle Dropdown</span>
		    </button>
		    <ul class="dropdown-menu" role="menu">
			<li'.($perm->campaign->campaign_update === 'N' ? ' class="hidden"' : '').'><a class="edit-campaign" href="#" data-id="'.$id.'">'.$this->lh->translationFor("view_details").'</a></li>
      <li'.($perm->pausecodes->pausecodes_read === 'N' ? ' class="hidden"' : '').'><a class="view-pause-codes" href="#" data-id="'.$id.'">'.$this->lh->translationFor("view_pause_codes").'</a></li>
      <li'.($perm->hotkeys->hotkeys_read === 'N' ? ' class="hidden"' : '').'><a class="view-hotkeys" href="#" data-id="'.$id.'">'.$this->lh->translationFor("view_hotkeys").'</a></li>
	  <li'.($perm->list->list_read === 'N' ? ' class="hidden"' : '').'><a class="view-lists" href="#" data-id="'.$id.'">'.$this->lh->translationFor("view_lists").'</a></li>
			<li'.($perm->campaign->campaign_delete === 'N' ? ' class="hidden"' : '').'><a class="delete-campaign" href="#" data-id="'.$id.'" data-name="'.$name.'">'.$this->lh->translationFor("delete").'</a></li>
		    </ul>
		</div>';
	}

	public function API_getCampaignInfo($campid){
		$url = gourl."/goCampaigns/goAPI.php"; #URL to GoAutoDial API. (required)
		$postfields["goUser"] = goUser; #Username goes here. (required)
		$postfields["goPass"] = goPass; #Password goes here. (required)
		$postfields["goAction"] = "getCampaignInfo"; #action performed by the [[API:Functions]]. (required)
		$postfields["responsetype"] = responsetype; #json. (required)
		$postfields["campaign_id"] = $campid; #Desired campaign id. (required)
		$postfields["session_user"] = $_SESSION['user'];
		$postfields["log_user"] = $_SESSION['user'];
		$postfields["log_group"] = $_SESSION['usergroup'];
		$postfields["log_ip"] = $_SERVER['REMOTE_ADDR'];

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

		return $output;
	}

	/** Call Recordings API - Get all list of call recording */
	/**
	 * @param goUser
	 * @param goPass
	 * @param goAction
	 * @param responsetype
	 */
	public function API_getListAllRecordings($search_phone, $start_filterdate, $end_filterdate, $agent_filter){
		require_once('Session.php');
		$url = gourl."/goCallRecordings/goAPI.php"; #URL to GoAutoDial API. (required)
	    $postfields["goUser"] = goUser; #Username goes here. (required)
	    $postfields["goPass"] = goPass; #Password goes here. (required)
	    $postfields["goAction"] = "goGetCallRecordingList"; #action performed by the [[API:Functions]]. (required)
	    $postfields["responsetype"] = responsetype; #json. (required)
	    $postfields["requestDataPhone"] = $search_phone;
		$postfields["log_user"] = $_SESSION['user'];
		$postfields["session_user"] = $_SESSION['user'];
		$postfields["log_group"] = $_SESSION['usergroup'];
		$postfields["log_ip"] = $_SERVER['REMOTE_ADDR'];
		
	    if(isset($start_filterdate))
	    $postfields["start_filterdate"] = $start_filterdate;

	    $postfields["end_filterdate"] = $end_filterdate;
	    $postfields["agent_filter"] = $agent_filter;

	    $ch = curl_init();
	    curl_setopt($ch, CURLOPT_URL, $url);
	    // curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
	    curl_setopt($ch, CURLOPT_POST, 1);
	    curl_setopt($ch, CURLOPT_TIMEOUT, 100);
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	    curl_setopt($ch, CURLOPT_POSTFIELDS, $postfields);
	    $data = curl_exec($ch);
	    curl_close($ch);
	    $output = json_decode($data);

	    return $output;
	}

	public function getListAllRecordings($search_phone, $start_filterdate, $end_filterdate, $agent_filter, $session_user){
	    $output = $this->API_getListAllRecordings($search_phone, $start_filterdate, $end_filterdate, $agent_filter, $_SESSION['user']);

	    if ($output->result=="success") {

	    	$columns = array("Date", "Customer", "Phone Number", "Agent", "Duration", "Action");
	    	$hideOnMedium = array("Agent", "Duration");
	    	$hideOnLow = array("Customer", "Phone Number", "Agent", "Duration");
			$result = $this->generateTableHeaderWithItems($columns, "table_callrecordings", "table-bordered table-striped", true, false, $hideOnMedium, $hideOnLow);

			//$result .= "<tr><td colspan='6'>".$output->query."</tr>";

	    for($i=0; $i < count($output->uniqueid); $i++){
			
			$details = "<strong>Phone</strong>: <i>".$output->phone_number[$i]."</i><br/>";
			$details .= "<strong>Date</strong>: <i>".date("M.d,Y h:i A", strtotime($output->end_last_local_call_time[$i]))."</i><br/>";
			
			//$action_Call = $output->query;
			$action_Call = $this->getUserActionMenuForCallRecording($output->uniqueid[$i], $output->location[$i], $details);

			$d1 = strtotime($output->start_last_local_call_time[$i]);
			$d2 = strtotime($output->end_last_local_call_time[$i]);

			$diff = abs($d2 - $d1);
			$duration = gmdate('H:i:s', $diff);
			
			$result .= "<tr>
				<td>".date("M.d,Y h:i A", strtotime($output->end_last_local_call_time[$i]))."</td>
				<td class='hide-on-low'>".$output->full_name[$i]."</td>
				<td class='hide-on-low'>".$output->phone_number[$i]."</td>
				<td class='hide-on-medium hide-on-low'>".$output->users[$i]."</td>
				<td class='hide-on-medium hide-on-low'>".$duration."</td>
				<td>".$action_Call."</td>
				</tr>";

	    }

			return $result."</table>";

	    } else {
		# An error occured
			return $output->result;

	    }
	}

	public function getUserActionMenuForCallRecording($id, $location, $details) {
	    return "<div class='btn-group'>
		    <button type='button' class='btn btn-default dropdown-toggle' data-toggle='dropdown'>".$this->lh->translationFor('choose_action')."
		    <button type='button' class='btn btn-default dropdown-toggle' data-toggle='dropdown' style='height: 34px;'>
					    <span class='caret'></span>
					    <span class='sr-only'>Toggle Dropdown</span>
		    </button>
		    <ul class='dropdown-menu' role='menu'>
			<li><a class='play_audio' href='#' data-location='".$location."' data-details='".$details."'>".$this->lh->translationFor('play_call_recording')."</a></li>
			<li><a class='download-call-recording' href='".$location."' download>".$this->lh->translationFor('download_call_recording')."</a></li>
		    </ul>
		</div>";
	}

	/** Music On Hold API - Get all list of music on hold */
	/**
	 * @param goUser
	 * @param goPass
	 * @param goAction
	 * @param responsetype
	 */
	public function API_goGetAllMusicOnHold(){
		$url = gourl."/goMusicOnHold/goAPI.php"; #URL to GoAutoDial API. (required)
	    $postfields["goUser"] = goUser; #Username goes here. (required)
	    $postfields["goPass"] = goPass; #Password goes here. (required)
	    $postfields["goAction"] = "goGetAllMusicOnHold"; #action performed by the [[API:Functions]]. (required)
	    $postfields["responsetype"] = responsetype; #json. (required)

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

	    if ($output->result=="success") {
	    	return $output;
	    } else {
		# An error occured
			return $output->result;
	    }
	}

	public function getListAllMusicOnHold($goUser, $goPass, $goAction, $responsetype){
		require_once('Session.php');
		$perm = $this->goGetPermissions('moh', $_SESSION['usergroup']);
	    $output = $this->API_goGetAllMusicOnHold();

	    # Result was OK!
	    $columns = array($this->lh->translationFor('moh_name'), $this->lh->translationFor('status'), $this->lh->translationFor('random_order'), $this->lh->translationFor('group'), $this->lh->translationFor('action'));
	    $hideOnMedium = array("Random Order", "Group", "Status");
		$hideOnLow = array( "Random Order", "Group", "Status");
	    $result = $this->generateTableHeaderWithItems($columns, "music-on-hold_table", "table-bordered table-striped", true, false, $hideOnMedium, $hideOnLow);

	    for($i=0;$i<count($output->moh_id);$i++){
			$action = $this->getUserActionMenuForMusicOnHold($output->moh_id[$i], $output->moh_name[$i], $perm);

			if($output->active[$i] == "Y"){
				$output->active[$i] = "Active";
			}else{
				$output->active[$i] = "Inactive";
			}

			if($output->random[$i] == "Y"){
				$output->random[$i] = "YES";
			}else{
				$output->random[$i] = "NO";
			}

			if($output->user_group[$i] == "---ALL---"){
				$output->user_group[$i] = "ALL USER GROUPS";
			}

			$result .= "<tr>
				<td><a class='edit-moh' data-id='".$output->moh_id[$i]."'>".$output->moh_name[$i]."</td>
				<td class ='hide-on-medium hide-on-low'>".$output->active[$i]."</td>
				<td class ='hide-on-medium hide-on-low'>".$output->random[$i]."</td>
				<td class ='hide-on-medium hide-on-low'>".$output->user_group[$i]."</td>
				<td nowrap>".$action."</td>
				</tr>";
	    }
		return $result.'</table>';
	}

	private function getUserActionMenuForMusicOnHold($id, $name, $perm) {

	    return '<div class="btn-group">
		    <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">'.$this->lh->translationFor("choose_action").'
		    <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" style="height: 34px;">
					    <span class="caret"></span>
					    <span class="sr-only">Toggle Dropdown</span>
		    </button>
		    <ul class="dropdown-menu" role="menu">
			<li'.($perm->moh_update === 'N' ? ' class="hidden"' : '').'><a class="edit-moh" href="#" data-id="'.$id.'">'.$this->lh->translationFor("modify").'</a></li>
			<li'.($perm->moh_delete === 'N' ? ' class="hidden"' : '').'><a class="delete-moh" href="#" data-id="'.$id.'" data-name="'.$name.'">'.$this->lh->translationFor("delete").'</a></li>
		    </ul>
		</div>';
	}

	/** Voice Files API - Get all list of voice files */
	/**
	 * @param goUser
	 * @param goPass
	 * @param goAction
	 * @param responsetype
	 */
	public function API_GetVoiceFilesList($goUser, $goPass, $goAction, $responsetype){
	    $url = gourl."/goVoiceFiles/goAPI.php"; #URL to GoAutoDial API. (required)
	    $postfields["goUser"] = goUser; #Username goes here. (required)
	    $postfields["goPass"] = goPass; #Password goes here. (required)
	    $postfields["goAction"] = "goGetVoiceFilesList"; #action performed by the [[API:Functions]]. (required)
	    $postfields["responsetype"] = responsetype; #json. (required)
		$postfields["session_user"] = $_SESSION['user']; #json. (required)
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

	public function getListAllVoiceFiles(){
		require_once('Session.php');
		$perm = $this->goGetPermissions('voicefiles', $_SESSION['usergroup']);
		$output = $this->API_GetVoiceFilesList();
	    if ($output->result=="success") {
	    # Result was OK!
	    $columns = array($this->lh->translationFor('file_name'), $this->lh->translationFor('date'), $this->lh->translationFor('size'), $this->lh->translationFor('action'));
	    $hideOnMedium = array("Date");
		$hideOnLow = array( "Date");
		$result = $this->generateTableHeaderWithItems($columns, "voicefiles", "table-bordered table-striped", true, false, $hideOnMedium, $hideOnLow);
	    $server_port = getenv("SERVER_PORT");
		//$web_ip = getenv("SERVER_ADDR");
		$web_ip = $_SERVER['SERVER_NAME'];
		if (preg_match("/443/",$server_port)) {$HTTPprotocol = 'https://';}
		else {$HTTPprotocol = 'http://';}
	    for($i=0;$i<count($output->file_name);$i++){
	    $file_link = $HTTPprotocol.$web_ip."/sounds/".$output->file_name[$i];
		 if (!$this->check_url($file_link)) {
			 $web_host = getenv("SERVER_NAME");
			 $file_link = "http://".$web_host."/sounds/".$output->file_name[$i];
		 }
	    //$file_link = "http://69.46.6.35/sounds/".$output->file_name[$i];

	    $details = "<strong>Filename</strong>: <i>".$output->file_name[$i]."</i><br/>";
	    $details .= "<strong>Date</strong>: <i>".$output->file_date[$i]."</i><br/>";

		$action = $this->getUserActionMenuForVoiceFiles($output->file_name[$i], $details, $perm, $HTTPprotocol, $web_ip);

		$preFix = "<a class='play_voice_file' data-location='".$file_link."' data-details='".$details."'>";
		$sufFix = "</a>";
		if ($perm->voicefiles_play === 'N') {
			$preFix = '';
			$sufFix = '';
		}
		
		$result .= "<tr>
			<td>{$preFix}".$output->file_name[$i]."{$sufFix}</td>
			<td class ='hide-on-medium hide-on-low'>".$output->file_date[$i]."</td>
			<td class ='hide-on-medium hide-on-low'>".$output->file_size[$i]."</td>
			<td nowrap>".$action."</td>
		    </tr>";
	    }
		return $result.'</table>';
	    } else {
		# An error occured
		return $output->result;
	    }
	}

	private function getUserActionMenuForVoiceFiles($filename, $details, $perm, $protocol, $web_ip) {
	    $file_link = $protocol.$web_ip."/sounds/".$filename;
		 if (!$this->check_url($file_link)) {
			 $web_host = getenv("SERVER_NAME");
			 $file_link = "http://".$web_host."/sounds/".$filename;
		 }
	    return '<div class="btn-group">
		    <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">'.$this->lh->translationFor("choose_action").'
		    <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" style="height: 34px;">
					    <span class="caret"></span>
					    <span class="sr-only">Toggle Dropdown</span>
		    </button>
		    <ul class="dropdown-menu" role="menu">
			<li'.($perm->voicefiles_play === 'N' ? ' class="hidden"' : '').'><a class="play_voice_file" href="#" data-location="'.$file_link.'" data-details="'.$details.'">'.$this->lh->translationFor("play_voice_file").'</a></li>
		    </ul>
		</div>';
	}


	/** Scripts API - Get all list of scripts */
	/**
	 * @param goUser
	 * @param goPass
	 * @param goAction
	 * @param responsetype
	 */

	// API Scripts
	public function API_goGetAllScripts($userid){
		//goGetAllScriptsAPI
		$url = gourl."/goScripts/goAPI.php"; #URL to GoAutoDial API. (required)
        $postfields["goUser"] = goUser; #Username goes here. (required)
        $postfields["goPass"] = goPass; #Password goes here. (required)
        $postfields["goAction"] = "getAllScripts"; #action performed by the [[API:Functions]]. (required)
        $postfields["responsetype"] = responsetype; #json. (required)
        $postfields["userid"] = $userid;
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

	public function getListAllScripts($userid, $perm){
	    $output = $this->API_goGetAllScripts($userid);

	    if ($output->result=="success") {
	    # Result was OK!
	    $columns = array($this->lh->translationFor("script_id"), $this->lh->translationFor("script_name"), $this->lh->translationFor("status"), $this->lh->translationFor("type"), $this->lh->translationFor("user_group"), $this->lh->translationFor("action"));
	    $hideOnMedium = array($this->lh->translationFor("type"), $this->lh->translationFor("status"), $this->lh->translationFor("user_group"));
	    $hideOnLow = array($this->lh->translationFor("script_id"), $this->lh->translationFor("type"), $this->lh->translationFor("status"), $this->lh->translationFor("user_group"));

		$result = $this->generateTableHeaderWithItems($columns, "scripts_table", "table-bordered table-striped", true, false, $hideOnMedium, $hideOnLow);

	    for($i=0;$i<count($output->script_id);$i++){
		$action = $this->getUserActionMenuForScripts($output->script_id[$i], $output->script_name[$i], $perm);

			if($output->active[$i] == "Y"){
			    $active = $this->lh->translationFor("active");
			}else{
			    $active = $this->lh->translationFor("inactive");
			}
			
			$preFix = "<a class='edit_script' data-id='".$output->script_id[$i]."'>";
			$sufFix = "</a>";
			if ($perm->script_update === 'N') {
				$preFix = '';
				$sufFix = '';
			}

			$result .= "<tr>
				<td class='hide-on-low'>".$preFix."".$output->script_id[$i]."".$sufFix."</td>
				<td>".$output->script_name[$i]."</td>
				<td class='hide-on-medium hide-on-low'>".$active."</td>
				<td class='hide-on-medium hide-on-low'>".$output->active[$i]."</td>
				<td class='hide-on-medium hide-on-low'>".$output->user_group[$i]."</td>
				<td>".$action."</td>
			    </tr>";
		    }
			return $result.'</table>';

	    } else {
		# An error occured
		return $output->result;
	    }
	}

	private function getUserActionMenuForScripts($id, $name, $perm) {

	    return '<div class="btn-group">
		    <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">'.$this->lh->translationFor("choose_action").'
		    <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" style="height: 34px;">
					    <span class="caret"></span>
					    <span class="sr-only">Toggle Dropdown</span>
		    </button>
		    <ul class="dropdown-menu" role="menu">
			<li'.($perm->script_update === 'N' ? ' class="hidden"' : '').'><a class="edit_script" href="#" data-id="'.$id.'">'.$this->lh->translationFor("modify").'</a></li>
			<li'.($perm->script_delete === 'N' ? ' class="hidden"' : '').'><a class="delete_script" href="#" data-id="'.$id.'" data-name="'.$name.'">'.$this->lh->translationFor("delete").'</a></li>
		    </ul>
		</div>';
	}

	/** Call Times API - Get all list of call times */
	/**
	 * @param goUser
	 * @param goPass
	 * @param goAction
	 * @param responsetype
	 */

	public function getCalltimes(){
		$url = gourl."/goCalltimes/goAPI.php"; #URL to GoAutoDial API. (required)
	    $postfields["goUser"] = goUser; #Username goes here. (required)
	    $postfields["goPass"] = goPass; #Password goes here. (required)
	    $postfields["goAction"] = "getAllCalltimes"; #action performed by the [[API:Functions]]. (required)
	    $postfields["responsetype"] = responsetype; #json. (required)
		 $postfields["log_user"] = $_SESSION['user'];
		 $postfields["log_group"] = $_SESSION['usergroup'];
		 $postfields["log_ip"] = $_SERVER['REMOTE_ADDR'];

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

	public function getListAllCallTimes($goUser, $goPass, $goAction, $responsetype){
	    $output = $this->getCalltimes();
	    if ($output->result=="success") {
	    # Result was OK!
        //$columns = array($this->lh->translationFor('call_time_id'), $this->lh->translationFor('call_time_name'), $this->lh->translationFor('default_start'), $this->lh->translationFor('default_stop'), $this->lh->translationFor('user_group'), $this->lh->translationFor('action'));
        //$hideOnMedium = array($this->lh->translationFor('call_time_id'), $this->lh->translationFor('default_start'), $this->lh->translationFor('default_stop'), $this->lh->translationFor('user_group'));
		//$hideOnLow = array( $this->lh->translationFor('call_time_id'), $this->lh->translationFor('default_start'), $this->lh->translationFor('default_stop'), $this->lh->translationFor('user_group'));
		$columns = array($this->lh->translationFor('call_time_id'), $this->lh->translationFor('call_time_name'), $this->lh->translationFor('Schedules'), $this->lh->translationFor('user_group'), $this->lh->translationFor('action'));
        $hideOnMedium = array($this->lh->translationFor('call_time_id'), $this->lh->translationFor('user_group'));
		$hideOnLow = array( $this->lh->translationFor('call_time_id'), $this->lh->translationFor('Schedules'), $this->lh->translationFor('user_group'));
		
		$result = $this->generateTableHeaderWithItems($columns, "calltimes", "table-bordered table-striped", true, false, $hideOnMedium, $hideOnLow);
		
	    for($i=0;$i<count($output->call_time_id);$i++){
		    $action = $this->getUserActionMenuForCalltimes($output->call_time_id[$i], $output->call_time_name[$i]);
			$schedule = "NULL";
			if($output->ct_default_start[$i] === $output->ct_default_stop[$i]){
				$def = 'data-def="NULL"';
			}else{
				$default_start = date('h:i A', strtotime(sprintf("%04d", $output->ct_default_start[$i])));
				$default_stop = date('h:i A', strtotime(sprintf("%04d", $output->ct_default_stop[$i])));
				$def = 'data-def="'.$default_start.' - '.$default_stop.'"';
				$schedule = $default_start.' - '.$default_stop;
			}
			if($output->ct_sunday_start[$i] === $output->ct_sunday_stop[$i]){
				$sun = 'data-sun="NULL"';
			}else{
				$sun_start = date('h:i A', strtotime(sprintf("%04d", $output->ct_sunday_start[$i])));
				$sun_stop = date('h:i A', strtotime(sprintf("%04d", $output->ct_sunday_stop[$i])));
				$sun = 'data-sun="'.$sun_start.' - '.$sun_stop.'"';
				if($schedule === "NULL")
					$schedule = $sun_start.' - '.$sun_stop;
			}
			if($output->ct_monday_start[$i] === $output->ct_monday_stop[$i]){
				$mon = 'data-mon="NULL"';
			}else{
				$mon_start = date('h:i A', strtotime(sprintf("%04d", $output->ct_monday_start[$i])));
				$mon_stop = date('h:i A', strtotime(sprintf("%04d", $output->ct_monday_stop[$i])));
				$mon = 'data-mon="'.$mon_start.' - '.$mon_stop.'"';
				if($schedule === "NULL")
					$schedule = $mon_start.' - '.$mon_stop;
			}
			if($output->ct_tuesday_start[$i] === $output->ct_tuesday_stop[$i]){
				$tue = 'data-tue="NULL"';
			}else{
				$tue_start = date('h:i A', strtotime(sprintf("%04d", $output->ct_tuesday_start[$i])));
				$tue_stop = date('h:i A', strtotime(sprintf("%04d", $output->ct_tuesday_stop[$i])));
				$tue = 'data-tue="'.$tue_start.' - '.$tue_stop.'"';
				if($schedule === "NULL")
					$schedule = $tue_start.' - '.$tue_stop;
			}
			if($output->ct_wednesday_start[$i] === $output->ct_wednesday_stop[$i]){
				$wed = 'data-wed="NULL"';
			}else{
				$wed_start = date('h:i A', strtotime(sprintf("%04d", $output->ct_wednesday_start[$i])));
				$wed_stop = date('h:i A', strtotime(sprintf("%04d", $output->ct_wednesday_stop[$i])));
				$wed = 'data-wed="'.$wed_start.' - '.$wed_start.'"';
				if($schedule === "NULL")
					$schedule = $wed_start.' - '.$wed_stop;
			}
			if($output->ct_thursday_start[$i] === $output->ct_thursday_stop[$i]){
				$thu = 'data-thu="NULL"';
			}else{
				$thu_start = date('h:i A', strtotime(sprintf("%04d", $output->ct_thursday_start[$i])));
				$thu_stop = date('h:i A', strtotime(sprintf("%04d", $output->ct_thursday_stop[$i])));
				$thu = 'data-thu="'.$thu_start.' - '.$thu_stop.'"';
				if($schedule === "NULL")
					$schedule = $thu_start.' - '.$thu_stop;
			}
			if($output->ct_friday_start[$i] === $output->ct_friday_stop[$i]){
				$fri = 'data-fri="NULL"';
			}else{
				$fri_start = date('h:i A', strtotime(sprintf("%04d", $output->ct_friday_start[$i])));
				$fri_stop = date('h:i A', strtotime(sprintf("%04d", $output->ct_friday_stop[$i])));
				$fri = 'data-fri="'.$fri_start.' - '.$fri_stop.'"';
				if($schedule === "NULL")
					$schedule = $fri_start.' - '.$fri_stop;
			}
			if($output->ct_saturday_start[$i] === $output->ct_saturday_stop[$i]){
				$sat = 'data-sat="NULL"';
			}else{
				$sat_start = date('h:i A', strtotime(sprintf("%04d", $output->ct_saturday_start[$i])));
				$sat_stop = date('h:i A', strtotime(sprintf("%04d", $output->ct_saturday_stop[$i])));
				$sat = 'data-sat="'.$sat_start.' - '.$sat_stop.'"';
				if($schedule === "NULL")
					$schedule = $sat_start.' - '.$sat_stop;
			}
            if($output->user_group[$i] === "---ALL---"){
            	$output->user_group[$i] = "ALL USER GROUPS";
            }
			$scheds = $def.' '.$mon.' '.$tue.' '.$wed.' '.$thu.' '.$fri.' '.$sat.' '.$sun;
			//<td class ='hide-on-medium hide-on-low'>".$output->ct_default_start[$i]."</td>
			//<td class ='hide-on-medium hide-on-low'>".$output->ct_default_stop[$i]."</td>
			$view_modal = '<a class="view_sched" data-toggle="modal" data-target="#view-sched-modal" data-id="'.$output->call_time_id[$i].' - '.$output->call_time_name[$i].'" '.$scheds.'>'.$schedule.'</a>';
				$result .= "<tr>
					<td class ='hide-on-medium hide-on-low'><a class='edit-calltime' data-id='".$output->call_time_id[$i]."'>".$output->call_time_id[$i]."</a></td>
					<td>".$output->call_time_name[$i]."</td>
					<td class ='hide-on-medium hide-on-low'>".$view_modal."</td>
					<td class ='hide-on-medium hide-on-low'>".$output->user_group[$i]."</td>
					<td nowrap>".$action."</td>
				</tr>";
        }
		    return $result.'</table>';
	    } else {
	       # An error occured
	       return $output->result;
	    }
	}

	private function getUserActionMenuForCalltimes($id, $name) {
	    return '<div class="btn-group">
		    <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">'.$this->lh->translationFor("choose_action").'
		    <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" style="height: 34px;">
					    <span class="caret"></span>
					    <span class="sr-only">Toggle Dropdown</span>
		    </button>
		    <ul class="dropdown-menu" role="menu">
			<li><a class="edit-calltime" href="#" data-id="'.$id.'" data-name="'.$name.'">'.$this->lh->translationFor("modify").'</a></li>
			<li><a class="delete-calltime" href="#" data-id="'.$id.'" data-name="'.$name.'">'.$this->lh->translationFor("delete").'</a></li>
		    </ul>
		</div>';
	}
	
	private function getCalltimeScheds($id, $name) {
	    
	}
	
	/** Carriers API - Get all list of carriers */
	/**
	 * @param goUser
	 * @param goPass
	 * @param goAction
	 * @param responsetype
	 */

	public function getServers(){
		$url = gourl."/goServers/goAPI.php"; #URL to GoAutoDial API. (required)
	    $postfields["goUser"] = goUser; #Username goes here. (required)
	    $postfields["goPass"] = goPass; #Password goes here. (required)
	    $postfields["goAction"] = "goGetServerList"; #action performed by the [[API:Functions]]. (required)
	    $postfields["responsetype"] = responsetype; #json. (required)

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
	
	public function getServerList($perm){
		$output = $this->getServers();

	    if ($output->result=="success") {
	    # Result was OK!

        $columns = array($this->lh->translationFor('server_id'), $this->lh->translationFor('server_name'), $this->lh->translationFor('server_ip'), $this->lh->translationFor('status'), $this->lh->translationFor('asterisk'), $this->lh->translationFor('trunks'), $this->lh->translationFor('gmt'), $this->lh->translationFor('action'));
        $hideOnMedium = array($this->lh->translationFor('asterisk'),$this->lh->translationFor('trunks'), $this->lh->translationFor('gmt'));
		$hideOnLow = array($this->lh->translationFor('server_ip'), $this->lh->translationFor('server_name'), $this->lh->translationFor('status'), $this->lh->translationFor('asterisk'),$this->lh->translationFor('trunks'),$this->lh->translationFor('gmt'));

		$result = $this->generateTableHeaderWithItems($columns, "servers_table", "table-bordered table-striped", true, false, $hideOnMedium, $hideOnLow);

	        for($i=0;$i<count($output->server_id);$i++){

				$action = '';
				if ($perm->servers_update != 'N' || $perm->servers_delete != 'N') {
					$action = $this->ActionMenuForServers($output->server_id[$i], $perm);
				}

			    if($output->active[$i] == "Y"){
				    $active = $this->lh->translationFor('active');
				}else{
				    $active = $this->lh->translationFor('inactive');
				}
                    $result .= "<tr>
	                    <td class ='hide-on-low'>".($perm->servers_update !== 'N' ? "<a class='edit-server' data-id='".$output->server_id[$i]."'>" : '')."".$output->server_id[$i]."</td>
	                    <td>".$output->server_description[$i]."</td>
	                    <td class ='hide-on-medium hide-on-low'>".$output->server_ip[$i]."</td>
						<td class ='hide-on-medium hide-on-low'>".$active."</td>
						<td class ='hide-on-low'>".$output->asterisk_version[$i]."</td>
						<td class ='hide-on-low'>".$output->max_vicidial_trunks[$i]."</td>
						<td class ='hide-on-low'>".$output->local_gmt[$i]."</td>
	                    <td nowrap>".$action."</td>
	                </tr>";
            }

		    return $result.'</table>';

	    } else {
	       # An error occured
	       return $output->result;
	    }
	}
	
	public function ActionMenuForServers($id, $perm) {

	    return '<div class="btn-group">
		    <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">'.$this->lh->translationFor("choose_action").'
		    <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" style="height: 34px;">
					    <span class="caret"></span>
					    <span class="sr-only">Toggle Dropdown</span>
		    </button>
		    <ul class="dropdown-menu" role="menu">
			<li'.($perm->servers_update === 'N' ? ' class="hidden"' : '').'><a class="edit-server" href="#" data-id="'.$id.'">'.$this->lh->translationFor("modify").'</a></li>
			<li'.($perm->servers_delete === 'N' ? ' class="hidden"' : '').'><a class="delete-server" href="#" data-id="'.$id.'">'.$this->lh->translationFor("delete").'</a></li>
		    </ul>
		</div>';
	}
	
	/** Carriers API - Get all list of carriers */
	/**
	 * @param goUser
	 * @param goPass
	 * @param goAction
	 * @param responsetype
	 */

	public function getCarriers(){
		$url = gourl."/goCarriers/goAPI.php"; #URL to GoAutoDial API. (required)
	    $postfields["goUser"] = goUser; #Username goes here. (required)
	    $postfields["goPass"] = goPass; #Password goes here. (required)
	    $postfields["goAction"] = "goGetCarriersList"; #action performed by the [[API:Functions]]. (required)
	    $postfields["responsetype"] = responsetype; #json. (required)

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

	public function getListAllCarriers($perm){
		$output = $this->getCarriers();

	    if ($output->result=="success") {
	    # Result was OK!

        $columns = array($this->lh->translationFor('carrier_id'), $this->lh->translationFor('carrier_name'), $this->lh->translationFor('server_ip'), $this->lh->translationFor('protocol'), $this->lh->translationFor('status'), $this->lh->translationFor('action'));
        $hideOnMedium = array($this->lh->translationFor('server_ip'), $this->lh->translationFor('protocol'));
		$hideOnLow = array( $this->lh->translationFor('carrier_id'), $this->lh->translationFor('server_ip'), $this->lh->translationFor('protocol'), $this->lh->translationFor('status'));

		$result = $this->generateTableHeaderWithItems($columns, "carriers", "table-bordered table-striped", true, false, $hideOnMedium, $hideOnLow);

	      for($i=0;$i<count($output->carrier_id);$i++){

				$action = '';
				if ($perm->carriers_update != 'N' || $perm->carriers_delete != 'N') {
					$action = $this->getUserActionMenuForCarriers($output->carrier_id[$i], $perm);
				}

			    if($output->active[$i] == "Y"){
				    $active = $this->lh->translationFor('active');
				}else{
				    $active = $this->lh->translationFor('inactive');
				}
            $result .= "<tr>
						<td class ='hide-on-low'>".($perm->carriers_update !== 'N' ? "<a class='edit-carrier' data-id='".$output->carrier_id[$i]."'>" : '')."".$output->carrier_id[$i]."</td>
						<td>".$output->carrier_name[$i]."</td>
						<td class ='hide-on-medium hide-on-low'>".$output->server_ip[$i]."</td>
						<td class ='hide-on-medium hide-on-low'>".$output->protocol[$i]."</td>
						<td class ='hide-on-low'>".$active."</td>
						<td nowrap>".$action."</td>
	            </tr>";
         }

		    return $result.'</table>';

	    } else {
	       # An error occured
	       return $output->result;
	    }
	}

	public function getUserActionMenuForCarriers($id, $perm) {

	    return '<div class="btn-group">
		    <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">'.$this->lh->translationFor("choose_action").'
		    <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" style="height: 34px;">
					    <span class="caret"></span>
					    <span class="sr-only">Toggle Dropdown</span>
		    </button>
		    <ul class="dropdown-menu" role="menu">
			<li'.($perm->carriers_update === 'N' ? ' class="hidden"' : '').'><a class="edit-carrier" href="#" data-id="'.$id.'">'.$this->lh->translationFor("modify").'</a></li>
			<li'.($perm->carriers_delete === 'N' ? ' class="hidden"' : '').'><a class="delete-carrier" href="#" data-id="'.$id.'">'.$this->lh->translationFor("delete").'</a></li>
		    </ul>
		</div>';
	}

	/**
     * Returns a HTML representation of the wizard form of campaign
     *
     */
	public function wizardFromCampaign(){
		return '
			<div class="form-horizontal">
    			<div class="form-group">
    				<label class="control-label col-lg-4">Campaign Type:</label>
    				<div class="col-lg-8">
    					<select id="campaignType" class="form-control">
    						<option value="outbound">Outbound</option>
    						<option value="inbound">Inbound</option>
    						<option value="blended">Blended</option>
    						<option value="survey">Survey</option>
    						<option value="copy">Copy Campaign</option>
    					</select>
    				</div>
    			</div>
    			<div class="form-group campaign-id">
    				<label class="control-label col-lg-4">Campaign ID:</label>
    				<div class="col-lg-8">
    					<div class="input-group">
					      <input id="campaign-id" name="campaign_id" type="text" class="form-control" placeholder="" readonly>
					      <span class="input-group-btn">
					        <button id="campaign-id-edit-btn" class="btn btn-default" type="button"><i class="fa fa-pencil"></i></button>
					      </span>
					    </div><!-- /input-group -->
    				</div>
    			</div>
    			<div class="form-group">
    				<label class="control-label col-lg-4">Campaign Name:</label>
    				<div class="col-lg-8">
    					<input id="campaign-name" type="text" class="form-control">
    				</div>
    			</div>
    			<div class="form-group did-tfn-ext hide">
    				<label class="control-label col-lg-4">DID / TFN Extension:</label>
    				<div class="col-lg-8">
    					<input id="did-tfn" type="text" class="form-control">
    				</div>
    			</div>
    			<div class="form-group call-route hide">
    				<span class="control-label col-lg-4">Call route:</span>
    				<div class="col-lg-8">
    					<select id="call-route" class="form-control">
    						<option value="NONE"></option>
    						<option value="INGROUP">INGROUP (campaign)</option>
    						<option value="IVR">IVR (callmenu)</option>
    						<option value="AGENT">AGENT</option>
    						<option value="VOICEMAIL">VOICEMAIL</option>
    					</select>
    				</div>
    			</div>
    			<div class="form-group surver-type hide">
    				<span class="control-label col-lg-4">Survey Type:</span>
    				<div class="col-lg-8">
    					<select id="survey-type" class="form-control">
    						<option value="BROADCAST">VOICE BROADCAST</option>
    						<option value="PRESS1">SURVEY PRESS 1</option>
    					</select>
    				</div>
    			</div>
    			<div class="form-group no-channels hide">
    				<span class="control-label col-lg-4">Number of Channels:</span>
    				<div class="col-lg-8">
    					<select id="no-channels" class="form-control">
    						<option>1</option>
    						<option>5</option>
    						<option>10</option>
    						<option>15</option>
    						<option>20</option>
    						<option>30</option>
    					</select>
    				</div>
    			</div>
    			<div class="form-group copy-from hide">
    				<span class="control-label col-lg-4">Copy from:</span>
    				<div class="col-lg-8">
    					<select id="copy-from" class="form-control">
    						<option>LIST HERE</option>
    					</select>
    				</div>
    			</div>
    			<div class="form-group upload-wav hide">
    				<span class="control-label col-lg-4">Please Upload .wav file</span>
    				<div class="col-lg-8">
    					<div class="input-group">
					      <input type="text" class="form-control" placeholder="16 bit mono 8000 PCM WAV audio files only">
					      <span class="input-group-btn">
					        <button class="btn btn-primary" type="button">Browse</button>
					      </span>
					    </div><!-- /input-group -->
    				</div>
    			</div>
    			<div class="lead-section hide">
        			<div class="form-group">
        				<label class="control-label col-lg-4">Lead File:</label>
        				<div class="col-lg-8">
        					<input type="text" class="form-control">
        				</div>
        			</div>
        			<div class="form-group">
        				<label class="control-label col-lg-4">List ID:</label>
        				<div class="col-lg-8">
        					<span>Auto Generated here range</span>
        				</div>
        			</div>
        			<div class="form-group">
        				<label class="control-label col-lg-4">Country:</label>
        				<div class="col-lg-8">
        					<select class="form-control">
        						<option>LIST HERE</option>
        					</select>
        				</div>
        			</div>
        			<div class="form-group">
        				<label class="control-label col-lg-4">Check For Duplicates:</label>
        				<div class="col-lg-8">
        					<select class="form-control">
        						<option>LIST HERE</option>
        					</select>
        				</div>
        			</div>
        			<div class="form-group">
        				<label class="control-label col-lg-4">&nbsp</label>
        				<div class="col-lg-8">
        					<button type="button" class="btn btn-default">UPLOAD LEADS</button>
        				</div>
        			</div>
    			</div>
    		</div>
		';
	}

//--------- Disposition ---------

	/*
	 * Displaying Disposition
	 * [[API: Function]] - getAllDispositions
	 * 	This application is used to get list of campaign belongs to user.
	*/
	public function API_getAllDispositions($custom){
        $url = gourl."/goDispositions/goAPI.php"; #URL to GoAutoDial API. (required)
        $postfields["goUser"] = goUser; #Username goes here. (required)
        $postfields["goPass"] = goPass; #Password goes here. (required)
        $postfields["goAction"] = "getAllDispositions"; #action performed by the [[API:Functions]]. (required)
        $postfields["responsetype"] = responsetype; #json. (required)
        $postfields["custom_request"] = $custom;
		$postfields["session_user"] = $_SESSION["user"];
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

		//var_dump($output->status);
		return $output;
	}

	public function ActionMenuForDisposition($id, $name, $perm) {
		 return '<div class="btn-group">
		    <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">'.$this->lh->translationFor("choose_action").'
		    <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" style="height: 34px;">
					    <span class="caret"></span>
					    <span class="sr-only">Toggle Dropdown</span>
		    </button>
		    <ul class="dropdown-menu" role="menu">
			<li'.($perm->disposition->disposition_update === 'N' ? ' class="hidden"' : '').'><a class="edit_disposition" href="#" data-id="'.$id.'">'.$this->lh->translationFor("modify").'</a></li>
			<li'.($perm->disposition->disposition_delete === 'N' ? ' class="hidden"' : '').'><a class="delete_disposition" href="#" data-id="'.$id.'" data-name="'.$name.'">'.$this->lh->translationFor("delete").'</a></li>
		    </ul>
		</div>';
	}

	public function API_getDispositionInfo($id){
        $url = gourl."/goDispositions/goAPI.php"; #URL to GoAutoDial API. (required)
        $postfields["goUser"] = goUser; #Username goes here. (required)
        $postfields["goPass"] = goPass; #Password goes here. (required)
        $postfields["goAction"] = "getDispositionInfo"; #action performed by the [[API:Functions]]. (required)
        $postfields["responsetype"] = responsetype; #json. (required)
        $postfields["campaign_id"] = $id;
		  $postfields["log_user"] = $_SESSION['user'];
		  $postfields["log_group"] = $_SESSION['usergroup'];
		  $postfields["log_ip"] = $_SERVER['REMOTE_ADDR'];

         $ch = curl_init();
         curl_setopt($ch, CURLOPT_URL, $url);
         curl_setopt($ch, CURLOPT_POST, 1);
         curl_setopt($ch, CURLOPT_TIMEOUT, 100);
         curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
         curl_setopt($ch, CURLOPT_POSTFIELDS, $postfields);
         $data = curl_exec($ch);
         curl_close($ch);
         $output = json_decode($data);

		//var_dump($output->status);
		return $output;
	}

//--------- Lead Filter ---------

	/*
	 * Displaying Lead Filter
	 * [[API: Function]] - getAllLeadFilters
	 * 	This application is used to get list of lead filter belongs to user.
	*/
	public function API_getAllLeadFilters(){
        $url = gourl."/goLeadFilters/goAPI.php"; #URL to GoAutoDial API. (required)
        $postfields["goUser"] = goUser; #Username goes here. (required)
        $postfields["goPass"] = goPass; #Password goes here. (required)
        $postfields["goAction"] = "getAllLeadFilters"; #action performed by the [[API:Functions]]. (required)
        $postfields["responsetype"] = responsetype; #json. (required)
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
		//var_dump($data);
		return $output;
	}

	public function ActionMenuForLeadFilters($id, $name) {
		 return '<div class="btn-group">
		    <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">'.$this->lh->translationFor("choose_action").'
		    <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" style="height: 34px;">
					    <span class="caret"></span>
					    <span class="sr-only">Toggle Dropdown</span>
		    </button>
		    <ul class="dropdown-menu" role="menu">
			<li><a class="view_leadfilter" href="#" data-id="'.$id.'">'.$this->lh->translationFor("view").'</a></li>
				<li><a class="edit_leadfilter" href="#" data-id="'.$id.'">'.$this->lh->translationFor("modify").'</a></li>
				<li><a class="delete_leadfilter" href="#" data-id="'.$id.'" data-name="'.$name.'">'.$this->lh->translationFor("delete").'</a></li>
			    </ul>
			</div>';
		}

		/*
		 * <<<<==================== END OF TELEPHONY APIs =====================>>>>
		 */

		/*
		 * APIs for Dashboard
		 *
		*/
			/*
			 * Displaying Total Sales
			 * [[API: Function]] - goGetTotalSales
			 * This application is used to get total number of total sales.
			*/
		public function API_goGetTotalSales($session_user) {
			$url = gourl."/goDashboard/goAPI.php"; #URL to GoAutoDial API. (required)
			$postfields["goUser"] = goUser; #Username goes here. (required)
			$postfields["goPass"] = goPass;
			$postfields["goAction"] = "goGetTotalSales"; #action performed by the [[API:Functions]]
			$postfields["session_user"] = $session_user; #current user
			
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
			//var_dump($data);
			/*$data = explode(";",$data);
			foreach ($data AS $temp) {
			  $temp = explode("=",$temp);
			  $results[$temp[0]] = $temp[1];
			}
			if ($results["result"]=="success") {
			  # Result was OK!
			  //var_dump($results); #to see the returned arrays.
				return $results["getTotalSales"];
			} else {
			  # An error occurred
				$vars = 0;
				return $vars;
			}*/
			return $output;
		}

		/*
		 * Displaying in Sales / Hour
		 * [[API: Function]] - goGetINSalesHour
		 * This application is used to get total number of in Sales per hour
		*/
		public function API_goGetINSalesPerHour($session_user) {
			$url = gourl."/goDashboard/goAPI.php"; #URL to GoAutoDial API. (required)
			$postfields["goUser"] = goUser; #Username goes here. (required)
			$postfields["goPass"] = goPass;
			$postfields["goAction"] = "goGetINSalesPerHour"; #action performed by the [[API:Functions]]
			$postfields["session_user"] = $session_user; #current user
			
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_TIMEOUT, 100);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $postfields);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			$data = curl_exec($ch);
			curl_close($ch);

			//var_dump($data);
			$data = explode(";",$data);
			foreach ($data AS $temp) {
			  $temp = explode("=",$temp);
			  $results[$temp[0]] = $temp[1];
			}

			if ($results["result"]=="success") {
			  # Result was OK!
			  //var_dump($results); #to see the returned arrays.
				   return $results["getINSalesPerHour"];
			} else {
			  # An error occurred
				   $vars = 0;
				   return $vars;
			}

		}

		/*
		 * Displaying OUT Sales / Hour
		 * [[API: Function]] - goGetOutSalesPerHour
		 * This application is used to get OUT sales per hour.
		*/

		public function API_goGetOUTSalesPerHour($session_user){
			$url = gourl."/goDashboard/goAPI.php"; #URL to GoAutoDial API. (required)
			$postfields["goUser"] = goUser; #Username goes here. (required)
			$postfields["goPass"] = goPass;
			$postfields["goAction"] = "goGetOutSalesPerHour"; #action performed by the [[API:Functions]]
			$postfields["session_user"] = $session_user; #current user
			
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_TIMEOUT, 100);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $postfields);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			$data = curl_exec($ch);
			curl_close($ch);

			//var_dump($data);
			 $data = explode(";",$data);
			 foreach ($data AS $temp) {
			   $temp = explode("=",$temp);
			   $results[$temp[0]] = $temp[1];
			 }

			 if ($results["result"]=="success") {
			   # Result was OK!
			   //var_dump($results); #to see the returned arrays.
				return $results["getOutSalesPerHour"];
			 } else {
			   # An error occurred
				$vars = 0;
				return $vars;
			 }
		}
		
		/*
		 * Displaying inbound Sales
		 * [[API: Function]] - goGetTotalInboundSales
		 * This application is used to get total number of inbound sales
		*/
		public function API_goGetInboundSales($session_user) {
			$url = gourl."/goDashboard/goAPI.php"; #URL to GoAutoDial API. (required)
			$postfields["goUser"] = goUser; #Username goes here. (required)
			$postfields["goPass"] = goPass;
			$postfields["goAction"] = "goGetTotalInboundSales"; #action performed by the [[API:Functions]]
			$postfields["session_user"] = $session_user; #current user
			 $ch = curl_init();
			 curl_setopt($ch, CURLOPT_URL, $url);
			 curl_setopt($ch, CURLOPT_POST, 1);
			 curl_setopt($ch, CURLOPT_TIMEOUT, 100);
			 curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			 curl_setopt($ch, CURLOPT_POSTFIELDS, $postfields);
			 curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			 $data = curl_exec($ch);
			 curl_close($ch);

			//var_dump($data);
			 $data = explode(";",$data);
			 foreach ($data AS $temp) {
			   $temp = explode("=",$temp);
			   $results[$temp[0]] = $temp[1];
			 }

			 if ($results["result"]=="success") {
			   # Result was OK!
			   //var_dump($results); #to see the returned arrays.
					return $results["InboundSales"];
			 } else {
			   # An error occurred
					$vars = 0;
					return $vars;
			 }

		}
		
		/*
		 * Displaying outbound Sales
		 * [[API: Function]] - goGetTotalOutboundSales
		 * This application is used to get total number of outbound sales
		*/
		public function API_goGetOutboundSales($session_user) {
			$url = gourl."/goDashboard/goAPI.php"; #URL to GoAutoDial API. (required)
			$postfields["goUser"] = goUser; #Username goes here. (required)
			$postfields["goPass"] = goPass;
			$postfields["goAction"] = "goGetTotalOutboundSales"; #action performed by the [[API:Functions]]
			$postfields["session_user"] = $session_user; #current user
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_TIMEOUT, 100);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $postfields);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			$data = curl_exec($ch);
			curl_close($ch);
			
			$data = explode(";",$data);
			foreach ($data AS $temp) {
			  $temp = explode("=",$temp);
			  $results[$temp[0]] = $temp[1];
			}

			if ($results["result"]=="success") {
			  # Result was OK!
			  //var_dump($results); #to see the returned arrays.
				   return $results["OutboundSales"];
			} else {
			  # An error occurred
				   $vars = 0;
				   return $vars;
			}
		}

		/*
		 * Displaying Agent(s) Waiting
		 * [[API: Function]] - getTotalAgentsWaitCalls
		 * This application is used to get total of agents waiting
		*/

		public function API_goGetTotalAgentsWaitCalls() {
			$url = gourl."/goDashboard/goAPI.php"; #URL to GoAutoDial API. (required)
			$postfields["goUser"] = goUser; #Username goes here. (required)
			$postfields["goPass"] = goPass;
			$postfields["goAction"] = "goGetTotalAgentsWaitCalls"; #action performed by the [[API:Functions]]
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
            return($data);
		}

		/*
		 *Displaying Agent(s) on Paused
		 *[[API: Function]] - goGetTotalAgentsPaused
		 *This application is used to get total of agents paused
		*/

		public function API_goGetTotalAgentsPaused(){
			$url = gourl."/goDashboard/goAPI.php"; #URL to GoAutoDial API. (required)
			$postfields["goUser"] = goUser; #Username goes here. (required)
			$postfields["goPass"] = goPass;
			$postfields["goAction"] = "goGetTotalAgentsPaused"; #action performed by the [[API:Functions]]
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

		/*
		 * Displaying Agent(s) on Call
		 * [[API: Function]] - goGetTotalAgentsCall
		 * This application is used to get total of agents on call
		*/

		public function API_goGetTotalAgentsCall() {
			$url = gourl."/goDashboard/goAPI.php"; #URL to GoAutoDial API. (required)
			$postfields["goUser"] = goUser; #Username goes here. (required)
			$postfields["goPass"] = goPass;
			$postfields["goAction"] = "goGetTotalAgentsCall"; #action performed by the [[API:Functions]]
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

                        return($data);
		}

		/*
		 * Displaying Leads in hopper
		 * [[API: Function]] - goGetLeadsinHopper
		 * This application is used to get total number of leads in hopper
		*/

		public function API_GetLeadsinHopper() {

			$url = gourl."/goDashboard/goAPI.php"; #URL to GoAutoDial API. (required)
			$postfields["goUser"] = goUser; #Username goes here. (required)
			$postfields["goPass"] = goPass;
			$postfields["goAction"] = "goGetLeadsinHopper"; #action performed by the [[API:Functions]]

			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_TIMEOUT, 100);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $postfields);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			$data = curl_exec($ch);
			curl_close($ch);

			//var_dump($data);
			 $data = explode(";",$data);
			 foreach ($data AS $temp) {
			   $temp = explode("=",$temp);
			   $results[$temp[0]] = $temp[1];
			 }

			 if ($results["result"]=="success") {
			   # Result was OK!
			   //var_dump($results); #to see the returned arrays.
					echo $results["getLeadsinHopper"];
			 } else {
			   # An error occured
			   echo "0";
			 }
		}

		/*
		 * Displaying Dialable Leads
		 * [[API: Function]] - goGetTotalDialableLeads
		 * This application is used to get total number of dialable leads.
		*/

		public function API_goGetTotalDialableLeads(){
			$url = gourl."/goDashboard/goAPI.php"; #URL to GoAutoDial API. (required)
			$postfields["goUser"] = goUser; #Username goes here. (required)
			$postfields["goPass"] = goPass;
			$postfields["goAction"] = "goGetTotalDialableLeads"; #action performed by the [[API:Functions]]

			 $ch = curl_init();
			 curl_setopt($ch, CURLOPT_URL, $url);
			 curl_setopt($ch, CURLOPT_POST, 1);
			 curl_setopt($ch, CURLOPT_TIMEOUT, 100);
			 curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			 curl_setopt($ch, CURLOPT_POSTFIELDS, $postfields);
			 curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			 $data = curl_exec($ch);
			 curl_close($ch);

			//var_dump($data);
			 $data = explode(";",$data);
			 foreach ($data AS $temp) {
			   $temp = explode("=",$temp);
			   $results[$temp[0]] = $temp[1];
			 }

			 if ($results["result"]=="success") {
			   # Result was OK!
			   //var_dump($results); #to see the returned arrays.
					return $results["getTotalDialableLeads"];
			 } else {
			   # An error occurred
					$vars = 0;
					return $vars;
			 }
		}

		/*
		 * Displaying Total Active Leads
		 * [[API: Function]] - goGetTotalActiveLeads
		 * This application is used to get total number of active leads
		*/

		public function API_goGetTotalActiveLeads(){

			$url = gourl."/goDashboard/goAPI.php"; #URL to GoAutoDial API. (required)
			$postfields["goUser"] = goUser; #Username goes here. (required)
			$postfields["goPass"] = goPass;
			$postfields["goAction"] = "goGetTotalActiveLeads"; #action performed by the [[API:Functions]]
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

			//var_dump($data);
			 $data = explode(";",$data);
			 foreach ($data AS $temp) {
			   $temp = explode("=",$temp);
			   $results[$temp[0]] = $temp[1];
			 }

			 if ($results["result"]=="success") {
			   # Result was OK!
			   //var_dump($results); #to see the returned arrays.
					return $results["getTotalActiveLeads"];
			 } else {
			   # An error occurred
					$vars = 0;
					return $vars;
			 }
		}

		/*
		 * Displaying Total Active Campaigns
		 * [[API: Function]] - goGetActiveCampaignsToday
		 * This application is used to get total number of active leads
		*/

		public function API_goGetActiveCampaignsToday(){
			$url = gourl."/goDashboard/goAPI.php"; #URL to GoAutoDial API. (required)
			$postfields["goUser"] = goUser; #Username goes here. (required)
			$postfields["goPass"] = goPass;
			$postfields["goAction"] = "goGetActiveCampaignsToday"; #action performed by the [[API:Functions]]
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
            return($data);
		}
		/*
		 * Displaying Call(s) Ringing
		 * [[API: Function]] - goGetRingingCalls
		 * This application is used to get calls ringing
		*/

		public function API_goGetRingingCalls() {
			$url = gourl."/goDashboard/goAPI.php"; #URL to GoAutoDial API. (required)
			$postfields["goUser"] = goUser; #Username goes here. (required)
			$postfields["goPass"] = goPass;
			$postfields["goAction"] = "goGetRingingCalls"; #action performed by the [[API:Functions]]
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

		/*
		 * Displaying Hopper Leads Warning
		 * [[API: Function]] - goGetHopperLeadsWarning
		 * This application is used to get the list of campaigns < 100
		*/
		public function API_goGetHopperLeadsWarning() {
			$url = gourl."/goDashboard/goAPI.php"; #URL to GoAutoDial API. (required)
			$postfields["goUser"] = goUser; #Username goes here. (required)
			$postfields["goPass"] = goPass;
			$postfields["goAction"] = "goGetHopperLeadsWarning"; #action performed by the [[API:Functions]]
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

		/*
		 * Displaying Online Agents Statuses
		 * [[API: Function]] - gogoGetAgentsMonitoringSummary
		 * This application is used to get the list online agents
		*/

		public function API_goGetAgentsMonitoringSummary() {
			$url = gourl."/goDashboard/goAPI.php"; #URL to GoAutoDial API. (required)
			$postfields["goUser"] = goUser; #Username goes here. (required)
			$postfields["goPass"] = goPass;
			$postfields["goAction"] = "goGetAgentsMonitoringSummary"; #action performed by the [[API:Functions]]
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

		/*
		 * Displaying Online Agents Statuses
		 * [[API: Function]] - goGetRealtimeAgentsMonitoring
		 * This application is used to get the list online agents
		 * for realtime monitoring
		*/
		public function API_goGetRealtimeAgentsMonitoring() {
			$url = gourl."/goDashboard/goAPI.php"; #URL to GoAutoDial API. (required)
			$postfields["goUser"] = goUser; #Username goes here. (required)
			$postfields["goPass"] = goPass;
			$postfields["goAction"] = "goGetRealtimeAgentsMonitoring"; #action performed by the [[API:Functions]]
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

		public function API_goGetIncomingQueue($session_user) {
			$url = gourl."/goDashboard/goAPI.php"; #URL to GoAutoDial API. (required)
			$postfields["goUser"] = goUser; #Username goes here. (required)
			$postfields["goPass"] = goPass;
			$postfields["goAction"] = "goGetIncomingQueue"; #action performed by the [[API:Functions]]
			$postfields["responsetype"] = responsetype;
			$postfields["session_user"] = $session_user; #current user
			
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

		/*
		 * Displaying Total Calls
		 * [[API: Function]] - getTotalcalls
		 * This application is used to get total calls.
		*/

		public function API_goGetTotalCalls(){
			$url = gourl."/goDashboard/goAPI.php"; #URL to GoAutoDial API. (required)
			$postfields["goUser"] = goUser; #Username goes here. (required)
			$postfields["goPass"] = goPass;
			$postfields["goAction"] = "goGetTotalCalls"; #action performed by the [[API:Functions]]
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

		/*
		 * Displaying Total Answered Calls
		 * [[API: Function]] - goGetTotalAnsweredCalls
		 * This application is used to get total calls.
		*/

		public function API_goGetTotalAnsweredCalls(){
			$url = gourl."/goDashboard/goAPI.php"; #URL to GoAutoDial API. (required)
			$postfields["goUser"] = goUser; #Username goes here. (required)
			$postfields["goPass"] = goPass;
			$postfields["goAction"] = "goGetTotalAnsweredCalls"; #action performed by the [[API:Functions]]
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
		/*
		 * Displaying Total Dropped Calls
		 * [[API: Function]] - goGetTotalDroppedCalls
		 * This application is used to get total calls.
		*/

		public function API_goGetTotalDroppedCalls($session_use){
			$url = gourl."/goDashboard/goAPI.php"; #URL to GoAutoDial API. (required)
			$postfields["goUser"] = goUser; #Username goes here. (required)
			$postfields["goPass"] = goPass;
			$postfields["goAction"] = "goGetTotalDroppedCalls"; #action performed by the [[API:Functions]]
			$postfields["session_user"] = $session_user; #current user
			
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
		/*
		 * Displaying Live Outbound
		 * [[API: Function]] - goGetLiveOutbound
		 * This application is used to get live outbound..
		*/

		public function API_goGetLiveOutbound() {
			$url = gourl."/goDashboard/goAPI.php"; #URL to GoAutoDial API. (required)
			$postfields["goUser"] = goUser; #Username goes here. (required)
			$postfields["goPass"] = goPass;
			$postfields["goAction"] = "goGetLiveOutbound"; #action performed by the [[API:Functions]]
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

		/*
		 * Displaying Calls / Hour
		 * [[API: Function]] - getPerHourCall
		 * This application is used to get calls per hour.
		*/

		public function API_goGetCallsPerHour($session_user) {
			$url = gourl."/goDashboard/goAPI.php"; #URL to GoAutoDial API. (required)
			$postfields["goUser"] = goUser; #Username goes here. (required)
			$postfields["goPass"] = goPass;
			$postfields["goAction"] = "goGetCallsPerHour"; #action performed by the [[API:Functions]]
			$postfields["session_user"] = $session_user; #action performed by the [[API:Functions]]
			
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_TIMEOUT, 100);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $postfields);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			$data = curl_exec($ch);
			curl_close($ch);

			return $data;
		}

		/*
		 * Displaying Sales / Hour
		 * [[API: Function]] - getPerHourSales
		 * This application is used to get calls per hour.
		*/

		public function API_goGetSalesPerHour() {
			$url = gourl."/goDashboard/goAPI.php"; #URL to GoAutoDial API. (required)
			$postfields["goUser"] = goUser; #Username goes here. (required)
			$postfields["goPass"] = goPass;
			$postfields["goAction"] = "goGetSalesPerHour"; #action performed by the [[API:Functions]]
			$postfields["session_user"] = $session_user; #action performed by the [[API:Functions]]

			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_TIMEOUT, 100);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $postfields);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			$data = curl_exec($ch);
			curl_close($ch);

			return $data;
		}


		/*
		 * Display Dropped Percentage
		 * [[API: Function]] - goGetDroppedPercentage
		 * This application is used to get dropped call percentage.
		*/
		public function API_goGetDroppedPercentage($session_user) {
			$url = gourl."/goDashboard/goAPI.php"; #URL to GoAutoDial API. (required)
			$postfields["goUser"] = goUser; #Username goes here. (required)
			$postfields["goPass"] = goPass;
			$postfields["goAction"] = "goGetDroppedPercentage"; #action performed by the [[API:Functions]]
			$postfields["responsetype"] = responsetype;
			$postfields["session_user"] = $session_user; #action performed by the [[API:Functions]]

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

		/*
		 * Display SLA Percentage
		 * [[API: Function]] - goGetSLAPercentage
		 * This application is used to get dropped call percentage.
		*/
        public function API_goGetSLAPercentage() {
		$url = gourl."/goDashboard/goAPI.php"; #URL to GoAutoDial API. (required)
		$postfields["goUser"] = goUser; #Username goes here. (required)
		$postfields["goPass"] = goPass;
		$postfields["goAction"] = "goGetSLAPercentage"; #action performed by the [[API:Functions]]
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

	/*
	 * Displaying Cluster Status
	 * [[API: Function]] - goGetClusterStatus
	 * This application is used to get cluster status
	*/

	public function API_goGetClusterStatus(){
		$url = gourl."/goDashboard/goAPI.php"; #URL to GoAutoDial API. (required)
		$postfields["goUser"] = goUser; #Username goes here. (required)
		$postfields["goPass"] = goPass;
		$postfields["goAction"] = "goGetClusterStatus"; #action performed by the [[API:Functions]]
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


// <<<=================== END OF DASHBOARD APIs =============>>>

	/*
	 * Displaying Contacts
	 * [[API: Function]] - goGetLeads
	 * This application is used to get cluster status
	*/
	public function API_GetLeads($userName, $search, $disposition_filter, $list_filter, $address_filter, $city_filter, $state_filter, $limit = 0, $search_customers = 0){
	$url = gourl."/goGetLeads/goAPI.php"; #URL to GoAutoDial API. (required)
	$postfields["goUser"] = goUser; #Username goes here. (required)
	$postfields["goPass"] = goPass;
	if($limit > 0){
		$postfields["goVarLimit"] = $limit;
	}else{
		$postfields["goVarLimit"] = "500";
	}

	$postfields["user"] = $userName;
	$postfields["goAction"] = "goGetLeads"; #action performed by the [[API:Functions]]
	$postfields["responsetype"] = responsetype; #json. (required)
	$postfields["search"] = $search;
	$postfields["disposition_filter"] = $disposition_filter;
	$postfields["list_filter"] = $list_filter;
	$postfields["address_filter"] = $address_filter;
	$postfields["city_filter"] = $city_filter;
	$postfields["state_filter"] = $state_filter;
	$postfields["search_customers"] = $search_customers;

	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_TIMEOUT, 600);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $postfields);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	$data = curl_exec($ch);
	curl_close($ch);
	$output = json_decode($data);

		return $output;
	}

	// get specific contact info
	public function API_GetLeadInfo($lead_id){
		$url = gourl."/goGetLeads/goAPI.php"; #URL to GoAutoDial API. (required)
	$postfields["goUser"] = goUser; #Username goes here. (required)
	$postfields["goPass"] = goPass; #Password goes here. (required)
	$postfields["goAction"] = "goGetLeadsInfo"; #action performed by the [[API:Functions]]. (required)
	$postfields["responsetype"] = responsetype; #json. (required)
	$postfields["lead_id"] = $lead_id; #Desired exten ID. (required)
	$postfields["session_user"] = $_SESSION['user']; 
	$postfields["log_user"] = $_SESSION['user'];
	$postfields["log_group"] = $_SESSION['usergroup'];
	$postfields["log_ip"] = $_SERVER['REMOTE_ADDR'];
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

		 return $output;
		}

	// get contact list
	public function GetContacts($userid, $search, $disposition_filter, $list_filter, $address_filter, $city_filter, $state_filter, $limit = 500, $search_customers = 0) {
		//$limit = 10;
		$output = $this->API_GetLeads($userid, $search, $disposition_filter, $list_filter, $address_filter, $city_filter, $state_filter, $limit, $search_customers);
	       if($output->result=="success") {

       	   $columns = array($this->lh->translationFor('lead_id'), $this->lh->translationFor('full_name'), $this->lh->translationFor('phone_number'), $this->lh->translationFor('status'), $this->lh->translationFor('action'));
	       $hideOnMedium = array($this->lh->translationFor('lead_id'), $this->lh->translationFor('status'));
	       $hideOnLow = array( $this->lh->translationFor('lead_id'), $this->lh->translationFor('phone_number'), $this->lh->translationFor('status'));
		   $result = $this->generateTableHeaderWithItems($columns, "table_contacts", "table-bordered table-striped", true, false, $hideOnMedium, $hideOnLow);

			for($i=0;$i<=count($output->list_id);$i++){
		   	//for($i=0;$i<=500;$i++){
				if($output->phone_number[$i] != ""){

				$action = $this->ActionMenuForContacts($output->lead_id[$i]);
				$result .= '<tr>
								<td><a class="edit-contact" data-id="'.$output->lead_id[$i].'">' .$output->lead_id[$i]. '</a></td>
								<td class="hide-on-low">' .$output->first_name[$i].' '.$output->middle_initial[$i].' '.$output->last_name[$i].'</td>
								<td class="hide-on-low">' .$output->phone_number[$i].'</td>
								<td class="hide-on-low hide-on-medium">' .$output->status[$i].'</td>
								<td>' .$action.'</td>
							</tr> ';
				}
			}

			return $result.'</table>';
       }else{
       		//display nothing
       }
	}

	public function getAllowedList($user_id){
		$url = gourl."/goGetLeads/goAPI.php"; #URL to GoAutoDial API. (required)
		$postfields["goUser"] = goUser; #Username goes here. (required)
		$postfields["goPass"] = goPass;
		$postfields["goAction"] = "goGetAllowedList"; #action performed by the [[API:Functions]]
		$postfields["responsetype"] = responsetype; #json. (required)
		$postfields['user_id'] = $user_id;

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

		return $output->lists;
	}

	// get script
	public function getAgentScript($lead_id, $fullname, $first_name, $last_name, $middle_initial, $email, $phone_number, $alt_phone,
		$address1, $address2, $address3, $city, $province, $state, $postal_code, $country_code){
		$url = gourl."/goViewScripts/goAPI.php"; # URL to GoAutoDial API filem (required)
         $postfields["goUser"] = goUser; #Username goes here. (required)
         $postfields["goPass"] = goPass; #Password goes here. (required)
         $postfields["goAction"] = "goViewAgentScript"; #action performed by the [[API:Functions]] (required0
         $postfields["responsetype"] = responsetype; #response type by the [[API:Functions]] (required)

         #required fields
         $postfields["lead_id"] = $lead_id; #Agent full anme(required)
         $postfields["fullname"] = $fullname; #Agent full anme(required)
         $postfields["first_name"] = $first_name; #Lead first_name (required)
         $postfields["last_name"] = $last_name; #Lead last_name (required)
         $postfields["middle_initial"] = $middle_initial; #Lead middle_initial (required)
         $postfields["email"] = $email; #Lead email (required)
         $postfields["phone_number"] = $phone_number; #Lead phone_number (required)
         $postfields["alt_phone"] = $alt_phone; #Lead alt_phone (required)
         $postfields["address1"] = $address1; #Lead address1 (required)
         $postfields["address2"] = $address2; #Lead address2 (required)
         $postfields["address3"] = $address3; #Lead address3 (required)
         $postfields["city"] = $city; #Lead city (required)
         $postfields["province"] = $province; #Lead province (required)
         $postfields["state"] = $state; #Lead state (required)
         $postfields["postal_code"] = $postal_code; #Lead postal_code (required)
         $postfields["country_code"] = $country_code; #Lead country_code(required)

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
        // var_dump($output);
        if ($output->result=="success") {
           # Result was OK!
                return $output->gocampaignScript;
         } else {
           # An error occured
                return $output->result;
        }

	}

	public function dropdownFormInputElement($id, $name, $options = array(), $currentValue, $required = false) {
		$requiredCode = $required ? "required" : "";
		$optionList = "";
		if (count($options) > 0) {
			foreach ($options as $k => $opt) {
				$isSelected = ($currentValue == $opt) ? "selected" : "";
				$optionList .= '<option value="'.$opt.'" '.$isSelected.'>'.$opt.'</option>';
			}
		}
		return '<select name="'.$name.'" id="'.$id.'" class="form-control '.$requiredCode.'">'.$optionList.'</select></div>';
	}

	public function getCountryCodes(){
		$url = gourl."/goCountryCode/goAPI.php"; #URL to GoAutoDial API. (required)
		$postfields["goUser"] = goUser; #Username goes here. (required)
		$postfields["goPass"] = goPass;
		$postfields["goAction"] = "getAllCountryCodes"; #action performed by the [[API:Functions]]
		$postfields["responsetype"] = responsetype; #json. (required)

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

	public function API_getAllDialStatuses($campaign_id){
		$url = gourl."/goDialStatus/goAPI.php"; #URL to GoAutoDial API. (required)
	    $postfields["goUser"] = goUser; #Username goes here. (required)
	    $postfields["goPass"] = goPass; #Password goes here. (required)
	    $postfields["goAction"] = "getAllDialStatuses"; #action performed by the [[API:Functions]]. (required)
	    $postfields["responsetype"] = responsetype; #json. (required)
	    $postfields["campaign_id"] = $campaign_id;

	    $ch = curl_init();
	    curl_setopt($ch, CURLOPT_URL, $url);
	    curl_setopt($ch, CURLOPT_POST, 1);
	    curl_setopt($ch, CURLOPT_TIMEOUT, 100);
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	    curl_setopt($ch, CURLOPT_POSTFIELDS, $postfields);
	    $data = curl_exec($ch);
	    curl_close($ch);
	    $output = json_decode($data);

	    return $output;
	}

	public function API_getAllDIDs($campaign_id){
		$url = gourl."/goInbound/goAPI.php"; #URL to GoAutoDial API. (required)
	    $postfields["goUser"] = goUser; #Username goes here. (required)
	    $postfields["goPass"] = goPass; #Password goes here. (required)
	    $postfields["goAction"] = "getAllDIDs"; #action performed by the [[API:Functions]]. (required)
	    $postfields["responsetype"] = responsetype; #json. (required)
	    $postfields["campaign_id"] = $campaign_id;

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

	public function getSessionAvatar() {
		$sessionAvatar = $_SESSION['avatar'];
		return $sessionAvatar;
	}

   public function API_goGetReports($pageTitle){
		$url = gourl."/goJamesReports/goAPI.php"; #URL to GoAutoDial API. (required)
		$postfields["goUser"] = goUser; #Username goes here. (required)
		$postfields["goPass"] = goPass; #Password goes here. (required)
		$postfields["goAction"] = "getAllDIDs"; #action performed by the [[API:Functions]]. (required)
		$postfields["responsetype"] = responsetype; #json. (required)
		$postfields["pageTitle"] = $pageTitle;

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


	/**
	 * Returns the standardized theme css for all pages.
	 */
	public function standardizedThemeCSS() {
		$css = "";
		$css .= '<link href="css/bootstrap.min.css" rel="stylesheet" type="text/css" />'."\n"; // bootstrap basic css
		$css .= '<link href="css/creamycrm.css" rel="stylesheet" type="text/css" />'."\n"; // creamycrm css
		$css .= '<link href="css/circle-buttons.css" rel="stylesheet" type="text/css" />'."\n"; // circle buttons css
		$css .= '<link href="css/ionicons.min.css" rel="stylesheet" type="text/css" />'."\n"; // ionicons
		$css .= '<link href="css/bootstrap-wysihtml5/bootstrap3-wysihtml5.min.css" rel="stylesheet" type="text/css" />'."\n"; // bootstrap3 css
		$css .= '<link rel="stylesheet" href="css/fontawesome/css/font-awesome.min.css">'."\n"; // font-awesome css
		$css .= '<link rel="stylesheet" href="theme_dashboard/simple-line-icons/css/simple-line-icons.css">'; // line css
		$css .= '<link rel="stylesheet" href="theme_dashboard/animate.css/animate.min.css">'."\n"; // animate css
		$css .= '<link rel="stylesheet" href="theme_dashboard/css/bootstrap.css" id="bscss">'; // bootstrap css
		$css .= '<link rel="stylesheet" href="theme_dashboard/css/app.css" id="maincss">'."\n"; // app css
		$css .= '<link rel="stylesheet" href="adminlte/css/AdminLTE.min.css">'."\n";
		$css .= '<link href="css/bootstrap-glyphicons.css" rel="stylesheet">'."\n";
		$css .= '<link rel="stylesheet" href="css/customizedLoader.css">'."\n"; // preloader css
		$css .= '<link rel="stylesheet" href="theme_dashboard/sweetalert/dist/sweetalert.css">'."\n"; // sweetalert

		/* JS that needs to be declared first */
		$css .= '<script src="js/jquery.min.js"></script>'."\n"; // required JS
		$css .= '<script src="js/bootstrap.min.js" type="text/javascript"></script>'."\n"; // required JS
		$css .= '<script src="js/jquery-ui.min.js" type="text/javascript"></script>'."\n"; // required JS

		return $css;
	}

	/**
	 * Returns the standardized theme js for all pages.
	 */
	public function standardizedThemeJS() {
		$js = '';
		$js .= '<script src="js/jquery.validate.min.js" type="text/javascript"></script>'."\n"; // forms and action js
		$js .= '<script src="theme_dashboard/sweetalert/dist/sweetalert.min.js"></script>'."\n"; // sweetalert js
		$js .= '<script src="js/plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.all.min.js" type="text/javascript"></script>'."\n"; // bootstrap 3 js
		$js .= '<script src="adminlte/js/app.min.js" type="text/javascript"></script>'."\n"; // creamy app js
		$js .= '<script src="js/vue-avatar/vue.min.js" type="text/javascript"></script>'."\n";
		$js .= '<script src="js/vue-avatar/vue-avatar.min.js" type="text/javascript"></script>'."\n";
		$js .= "<script type='text/javascript'>

			var goOptions = {
				el: 'body',
				components: {
					'avatar': Avatar.Avatar,
					'rules': {
						props: ['items'],
						template: 'For example:' +
							'<ul id=\"example-1\">' +
							'<li v-for=\"item in items\"><b>{{ item.username }}</b> becomes <b>{{ item.initials }}</b></li>' +
							'</ul>'
					}
				},

				data: {
					items: []
				},

				methods: {
					initials: function(username, initials) {
						this.items.push({username: username, initials: initials});
					}
				}
			};
			var goAvatar = new Vue(goOptions);
		</script>\n";

		return $js;
	}

	/**
	 * Returns an Vue Avatar
	 */
	public function getVueAvatar($username, $avatar, $size, $topBar = false, $sideBar = false, $rounded = true) {
		$showAvatar = '';
		$initials = '';
		if (isset($avatar)) {
			if (preg_match("/(agent|goautodial)/i", $username) && preg_match("/defaultAvatar/i", $avatar)) {
				$showAvatar = '';
				$initials = 'initials="GO"';
			} else {
				$showAvatar = 'src="'.$avatar.'"';
				$initials = '';
			}
		}
		$topBarStyle = ($topBar) ? 'style="float: left; padding-right: 5px;"' : '';
		$sideBarStyle = ($sideBar) ? 'style="width: 100%; text-align: center;" display="inline-block"' : '';
		$roundedImg = (!$rounded) ? ':rounded="false"' : '';

		return '<avatar username="'.$username.'" '.$showAvatar.' '.$initials.' '.$topBarStyle.' '.$sideBarStyle.' '.$roundedImg.' :size="'.$size.'"></avatar>';
	}

	public function API_goGetAllCustomFields($list_id) {
		$url = gourl."/goCustomFields/goAPI.php"; #URL to GoAutoDial API. (required)
		$postfields["goUser"] = goUser; #Username goes here. (required)
		$postfields["goPass"] = goPass; #Password goes here. (required)
		$postfields["goAction"] = "getAllCustomFields"; #action performed by the [[API:Functions]]. (required)
		$postfields["responsetype"] = responsetype; #json. (required)
		$postfields["list_id"] = $list_id;

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

	public function API_EmergencyLogout($username) {
		$url = gourl."/goDashboard/goAPI.php"; #URL to GoAutoDial API. (required)
		$postfields["goUser"] = goUser; #Username goes here. (required)
		$postfields["goPass"] = goPass;
		$postfields["goAction"] = "goEmergencyLogout"; #action performed by the [[API:Functions]]
		$postfields["responsetype"] = responsetype;
		$postfields["goUserAgent"] = $username;
		$postfields["log_user"] = $_SESSION['user'];
		$postfields["log_group"] = $_SESSION['usergroup'];
		$postfields["log_ip"] = $_SERVER['REMOTE_ADDR'];

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

		if ($output->result=="success") {
		   # Result was OK!
		    $status = "success";
		 } else {
		   # An error occured
			$status = $output->result;
		}

		return $status;
	}
	
	public function API_goGetGroupPermission($group){
		$url = gourl."/goUserGroups/goAPI.php"; #URL to GoAutoDial API. (required)
		$postfields["goUser"] = goUser; #Username goes here. (required)
		$postfields["goPass"] = goPass; #Password goes here. (required)
		$postfields["goAction"] = "goGetUserGroupInfo"; #action performed by the [[API:Functions]]. (required)
		$postfields["responsetype"] = responsetype; #json. (required)
		$postfields["user_group"] = $group; #json. (required)
		$postfields["session_user"] = $_SESSION['user'];		
		
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

		 return $output;
	}
	
	public function goGetPermissions($type = 'dashboard', $group) {
		$permissions = $this->API_goGetGroupPermission($group);
		if (!is_null($permissions)) {
			$types = explode(",", $type);
			if (count($types) > 1) {
				foreach ($types as $t) {
					if (array_key_exists($t, $permissions)) {
						$return->{$t} = $permissions->{$t};
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
		} else {
			$return = null;
		}
		return $return;
	}

	public function API_ListsStatuses($list_id) {
		$url = gourl."/goLists/goAPI.php"; #URL to GoAutoDial API. (required)
		$postfields["goUser"] = goUser; #Username goes here. (required)
		$postfields["goPass"] = goPass;
		$postfields["goAction"] = "goGetStatusesWithCountCalledNCalled"; #action performed by the [[API:Functions]]
		$postfields["responsetype"] = responsetype;
		$postfields["list_id"] = $list_id;

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

	public function API_ListsTimezone($list_id) {
		$url = gourl."/goLists/goAPI.php"; #URL to GoAutoDial API. (required)
		$postfields["goUser"] = goUser; #Username goes here. (required)
		$postfields["goPass"] = goPass;
		$postfields["goAction"] = "goGetTZonesWithCountCalledNCalled"; #action performed by the [[API:Functions]]
		$postfields["responsetype"] = responsetype;
		$postfields["list_id"] = $list_id;

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
	
	public function API_getAllCampaignDialStatuses($campaign_id){
		$url = gourl."/goDialStatus/goAPI.php"; #URL to GoAutoDial API. (required)
	    $postfields["goUser"] = goUser; #Username goes here. (required)
	    $postfields["goPass"] = goPass; #Password goes here. (required)
	    $postfields["goAction"] = "getAllCampaignDialStatuses"; #action performed by the [[API:Functions]]. (required)
	    $postfields["responsetype"] = responsetype; #json. (required)
	    $postfields["campaign_id"] = $campaign_id;

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
	
	// Call Menu Options
	public function API_getIVROptions($menu_id) {

		$url = gourl."/goInbound/goAPI.php"; #URL to GoAutoDial API. (required)
        $postfields["goUser"] = goUser; #Username goes here. (required)
        $postfields["goPass"] = goPass; #Password goes here. (required)
        $postfields["goAction"] = "goGetIVROptions"; #action performed by the [[API:Functions]]. (required)
        $postfields["responsetype"] = responsetype; #json. (required)
		$postfields["menu_id"] = $menu_id;
		
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

		//var_dump($output);
		return $output;

	}
	
	private function check_url($url) {
		$headers = @get_headers( $url);
		$headers = (is_array($headers)) ? implode( "\n ", $headers) : $headers;
		
		return (bool)preg_match('#^HTTP/.*\s+[(200|301|302)]+\s#i', $headers);
	}
	
	// get dnc
	public function API_GetDNC($search){
		$url = gourl."/goLists/goAPI.php"; #URL to GoAutoDial API. (required)
		$postfields["goUser"] = goUser; #Username goes here. (required)
		$postfields["goPass"] = goPass; #Password goes here. (required)
		$postfields["goAction"] = "getAllDNC"; #action performed by the [[API:Functions]]. (required)
		$postfields["search"] = $search; #get DNC by this list_id search
		$postfields["responsetype"] = responsetype; #json. (required)
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
	
		return $output;
	}

	// get dnc table
	public function GetDNC($search) {
		//$limit = 10;
		$output = $this->API_GetDNC($search);
	       if($output->result=="success") {
			$columns = array($this->lh->translationFor("phone_number"), $this->lh->translationFor("campaign"), $this->lh->translationFor("action"));
			$hideOnMedium = array();
			$hideOnLow = array( $this->lh->translationFor("campaign") );
			$result = $this->generateTableHeaderWithItems($columns, "table_dnc", "table-bordered table-striped", true, false, $hideOnMedium, $hideOnLow);

			for($i=0;$i < count($output->phone_number);$i++){
				$result .= '<tr>
								<td>' .$output->phone_number[$i]. '</td>
								<td class="hide-on-low">' .$output->campaign[$i].'</td>
								<td><a class="delete-dnc btn btn-danger" data-id="'.$output->phone_number[$i].'" data-campaign="'.$output->campaign[$i].'" title="'.$this->lh->translationFor("delete").' DNC Number: '.$output->phone_number[$i].'"><i class="fa fa-trash"></i></a></td>
							</tr> ';
			}

			return $result.'</table>';
       }else{
       		//display nothing
       }
	}
	
	public function API_LogActions($action, $user, $ip, $event_date, $details, $user_group, $db_query = '') {
		$url = gourl."/goAdminLogs/goAPI.php"; #URL to GoAutoDial API. (required)
		$postfields["goUser"] = goUser; #Username goes here. (required)
		$postfields["goPass"] = goPass; #Password goes here. (required)
		$postfields["goAction"] = "goLogActions"; #action performed by the [[API:Functions]]. (required)
		$postfields["action"] = $action; #get DNC by this list_id search
		$postfields["user"] = $user;
		$postfields["ip_address"] = $ip;
		$postfields["details"] = $details;
		$postfields["user_group"] = $user_group;
		$postfields["db_query"] = $db_query;
		$postfields["responsetype"] = responsetype; #json. (required)
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_TIMEOUT, 100);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $postfields);
		$data = curl_exec($ch);
		curl_close($ch);
		$output = json_decode($data);
		
		return $output;
	}
		
	public function getDialStatusesforSurvey($campaign_id){
		$url = gourl."/goDialStatus/goAPI.php"; #URL to GoAutoDial API. (required)
		$postfields["goUser"] = goUser; #Username goes here. (required)
		$postfields["goPass"] = goPass; #Password goes here. (required)
		$postfields["goAction"] = "getAllDialStatuses"; #action performed by the [[API:Functions]]. (required)
		$postfields["responsetype"] = responsetype; #json. (required)
		$postfields["campaign_id"] = $campaign_id;
		$postfields["hotkeys_only"] = 1;

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
	
	public function API_getListAudioFiles(){
		$url = gourl."/goCampaigns/goAPI.php"; #URL to GoAutoDial API. (required)
		$postfields["goUser"] = goUser; #Username goes here. (required)
		$postfields["goPass"] = goPass; #Password goes here. (required)
		$postfields["goAction"] = "getAllAudioFiles"; #action performed by the [[API:Functions]]. (required)
		$postfields["responsetype"] = responsetype; #json. (required)

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
	
	public function API_getSMTPActivation(){
		$url = gourl."/goSMTP/goAPI.php"; #URL to GoAutoDial API. (required)
		$postfields["goUser"] = goUser; #Username goes here. (required)
		$postfields["goPass"] = goPass; #Password goes here. (required)
		$postfields["goAction"] = "goGetSMTPActivation"; #action performed by the [[API:Functions]]. (required)
		$postfields["responsetype"] = responsetype; #json. (required)

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
		if($output->result == "success")
			return $output->data->value;
		else
			return '0';
	}
	
	private function getActionButtonForSMTP($status) {
	   $return = '<div class="btn-group"><button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">'.$this->lh->translationFor("choose_action").'
		    <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" style="height: 34px;">
					    <span class="caret"></span>
					    <span class="sr-only">Toggle Dropdown</span>
		    </button>
		    <ul class="dropdown-menu" role="menu">';
			if($status == 1){
				$return .= '<li><a class="activate-smtp" href="#" data-id="0" >'.$this->lh->translationFor("disable").'</a></li>';
			}else{
				$return .= '<li><a class="activate-smtp" href="#" data-id="1" >'.$this->lh->translationFor("enable").'</a></li>';
			}
		$return .= '</ul>
		</div>';
		return $return;
	}
	
	public function getCheckAll($action){
		$return = '<div class="btn-group">
					<div class="checkbox c-checkbox" style="margin-right: 0; margin-left: 0;">
							<label><input class="check-all_'.$action.'" type="checkbox" value="Y"><span class="fa fa-check"></span> </label>
						</div>
						<div>
							<a type="button" class="btn dropdown-toggle" data-toggle="dropdown" style="height: 20px;">
							<center><span class="caret"></span></center>
							</a>
							<ul class="dropdown-menu" role="menu">
								<li><a class="delete-multiple-'.$action.'" href="#" >Delete Selected</a></li>
							</ul>
						</div>
					</div>';
		$return .= '
		<script>
			$(document).ready(function() {
				$(document).on("change",".check-all_'.$action.'",function() {
					var box = $(this);
					if (box.is(":checked")) {
						$(".check_'.$action.'").prop("checked", true);
					}else{
						$(".check_'.$action.'").prop("checked", false);
					}
				});
			});
		</script>';
		return $return;
	}
	
	public function API_getAgentLog($user, $session_user, $sdate, $edate) {
		$url = gourl."/goUsers/goAPI.php"; #URL to GoAutoDial API. (required)
		$postfields["goUser"] = goUser; #Username goes here. (required)
		$postfields["goPass"] = goPass; #Password goes here. (required)
		$postfields["goAction"] = "goGetAgentLog"; #action performed by the [[API:Functions]]. (required)
		$postfields["responsetype"] = responsetype; #json. (required)
		$postfields["user"] = $user; 
		$postfields["start_date"] = $sdate;
		$postfields["end_date"] = $edate; 
		$postfields["session_user"] = $session_user; #json. (required)
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
	
	public function getAgentLog($user, $session_user, $sdate, $edate) {
		$output = $this->API_getAgentLog($user, $session_user, $sdate, $edate);
		//var_dump($output);
		if($output->result=="success") {
			$columns = array($this->lh->translationFor('event_time'), $this->lh->translationFor('status'), $this->lh->translationFor('phone_number'), $this->lh->translationFor('campaign'), $this->lh->translationFor('group'), $this->lh->translationFor('list_id'), $this->lh->translationFor('lead_id'), $this->lh->translationFor('term_reason'));
			$hideOnMedium = array();
			$hideOnLow = array( );
			$outbound = $this->generateTableHeaderWithItems($columns, "table_outbound", "table-bordered table-striped", true, false, $hideOnMedium, $hideOnLow);
			
			for($i=0;$i < count($output->outbound->campaign_id);$i++){
				$outbound .= '<tr>
								<td>' .$output->outbound->event_time[$i]. '</a></td>
								<td>' .$output->outbound->status[$i].'</td>
								<td>' .$output->outbound->phone_number[$i].'</td>
								<td>' .$output->outbound->campaign_id[$i].'</td>
								<td>' .$output->outbound->user_group[$i].'</td>
								<td>' .$output->outbound->list_id[$i].'</td>
								<td>' .$output->outbound->lead_id[$i].'</td>
								<td>' .$output->outbound->term_reason[$i].'</td>
							</tr>';
			}
			$outbound .= "</table>";
			
			$columns = array($this->lh->translationFor('event_time'), $this->lh->translationFor('status'), $this->lh->translationFor('phone_number'), $this->lh->translationFor('campaign'), $this->lh->translationFor('group'), $this->lh->translationFor('list_id'), $this->lh->translationFor('lead_id'), $this->lh->translationFor('term_reason'));
			$hideOnMedium = array();
			$hideOnLow = array( );
			$inbound = $this->generateTableHeaderWithItems($columns, "table_inbound", "table-bordered table-striped", true, false, $hideOnMedium, $hideOnLow);
			
			for($i=0;$i < count($output->inbound->campaign_id);$i++){
				$inbound .= '<tr>
								<td>' .$output->inbound->call_date[$i]. '</a></td>
								<td>' .$output->inbound->queue_seconds[$i].'</td>
								<td>' .$output->inbound->status[$i].'</td>
								<td>' .$output->inbound->campaign_id[$i].'</td>
								<td>' .$output->inbound->user_group[$i].'</td>
								<td>' .$output->inbound->list_id[$i].'</td>
								<td>' .$output->inbound->lead_id[$i].'</td>
								<td>' .$output->inbound->term_reason[$i].'</td>
							</tr>';
			}
			$inbound .= "</table>";
			
			$columns = array($this->lh->translationFor('event_time'), $this->lh->translationFor('event'), $this->lh->translationFor('campaign'), $this->lh->translationFor('group'));
			$hideOnMedium = array();
			$hideOnLow = array( );
			$userlog = $this->generateTableHeaderWithItems($columns, "table_userstat", "table-bordered table-striped", true, false, $hideOnMedium, $hideOnLow);
			
			for($i=0;$i < count($output->userlog->user_log_id);$i++){
				$userlog .= '<tr>
								<td>' .$output->userlog->event_date[$i]. '</a></td>
								<td>' .$output->userlog->event[$i].'</td>
								<td>' .$output->userlog->campaign_id[$i].'</td>
								<td>' .$output->userlog->user_group[$i].'</td>
							</tr>';
			}
			$userlog .= "</table>";
			
			$result = array($outbound, $inbound, $userlog);
		}else{
			$result = "";
		}
		
		return json_encode($result);
	}

	// Getting all Standard Fields
	public function API_getAllStandardFields(){
        $url = gourl."/goScripts/goAPI.php"; #URL to GoAutoDial API. (required)
		$postfields["goUser"] = goUser; #Username goes here. (required)
		$postfields["goPass"] = goPass;
		$postfields["goAction"] = "goGetStandardFields"; #action performed by the [[API:Functions]]
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

		if($output->result == "success"){
			return $output->field_name;
		}else{
			return "EMPTY";
		}
	}

	public function API_getGOPackage(){
		$url = gourl."/goPackages/goAPI.php"; //URL to GoAutoDial API. (required)
		$postfields["goUser"] = goUser; //Username goes here. (required)
		$postfields["goPass"] = goPass; //Password goes here. (required)
		$postfields["goAction"] = "goGetPackage"; //action performed by the [[API:Functions]]. (required)
		$postfields["responsetype"] = responsetype; //json. (required)

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

	public function getAllCampaignStatuses(){
        $campaign = $this->API_getListAllCampaigns();
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
	
	public function API_getLeadRecycling($user){
		$url = gourl."/goLeadRecycling/goAPI.php"; #URL to GoAutoDial API. (required)
	    $postfields["goUser"] = goUser; #Username goes here. (required)
	    $postfields["goPass"] = goPass; #Password goes here. (required)
	    $postfields["goAction"] = "goGetAllLeadRecycling"; #action performed by the [[API:Functions]]. (required)
	    $postfields["responsetype"] = responsetype; #json. (required)
	    $postfields["session_user"] = $user; #json. (required)

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
	
	public function ActionMenuForLeadRecycling($id) {

	    return '<div class="btn-group">
		    <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">'.$this->lh->translationFor("choose_action").'
		    <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" style="height: 34px;">
					    <span class="caret"></span>
					    <span class="sr-only">Toggle Dropdown</span>
		    </button>
		    <ul class="dropdown-menu" role="menu">
			<li><a class="edit-leadrecycling" href="#" data-id="'.$id.'">'.$this->lh->translationFor("modify").'</a></li>
			<li><a class="delete-leadrecycling" href="#" data-id="'.$id.'">'.$this->lh->translationFor("delete").'</a></li>
		    </ul>
		</div>';
	}
	
	public function escapeJsonString($value) { # list from www.json.org: (\b backspace, \f formfeed)
		$escapers = array("\\", "/", "\"", "\n", "\r", "\t", "\x08", "\x0c", "	");
		$replacements = array("\\\\", "\\/", "\\\"", "\\n", "\\r", "\\t", "\\f", "\\b", " ");
		$result = str_replace($escapers, $replacements, $value);

		return $result;
	}
	
	public function getSettingsAPIKey($type) {
		switch ($type) {
			case 'google':
				$return = $this->db->getSettingValueForKey(CRM_SETTING_GOOGLE_API_KEY);
				break;
			default:
				$return = false;
		}
		
		return $return;
	}
}

?>
