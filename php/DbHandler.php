<?php
/**
	The MIT License (MIT)
	
	Copyright (c) 2015 Ignacio Nieto Carvajal
	
	Permission is hereby granted, free of charge, to any person obtaining a copy
	of this software and associated documentation files (the "Software"), to deal
	in the Software without restriction, including without limitation the rights
	to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
	copies of the Software, and to permit persons to whom the Software is
	furnished to do so, subject to the following conditions:
	
	The above copyright notice and this permission notice shall be included in
	all copies or substantial portions of the Software.
	
	THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
	IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
	FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
	AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
	LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
	OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
	THE SOFTWARE.
*/
namespace creamy;

// dependencies
require_once('CRMDefaults.php');
require_once('PassHash.php');
require_once('ImageHandler.php');
require_once('RandomStringGenerator.php');
require_once('LanguageHandler.php');
require_once('APIHandler.php');
require_once('DatabaseConnectorFactory.php');
require_once('goCRMAPISettings.php');

/**
 * DbHandler class.
 * Class to handle all db operations
 * This class is in charge of managing the database operations for Creamy. All DB managing should be done by means of instances of this class, i.e:
 *
 * $db = new \creamy\DbHandler();
 * $success = $db->deleteUser(123);
 *
 * @author Ignacio Nieto Carvajal
 * @link URL http://digitalleaves.com
 */
class DbHandler {
    /** Database connector */
    private $dbConnector;
	private $dbConnectorAsterisk;
	/** Language handler */
	private $lh;
	private $api;
        
	/** Creation and class lifetime management */
    
    function __construct($dbConnectorType = CRM_DB_CONNECTOR_TYPE_MYSQL) {
		// Database connector
		$this->dbConnector = \creamy\DatabaseConnectorFactory::getInstance()->getDatabaseConnectorOfType($dbConnectorType);
		$this->dbConnectorAsterisk = \creamy\DatabaseConnectorFactory::getInstance()->getDatabaseConnectorOfTypeAsterisk($dbConnectorType);

		// language handler
		$locale = $this->getLocaleSetting();
		$this->lh = \creamy\LanguageHandler::getInstance($locale, $dbConnectorType);

		// api handler
		$this->api = \creamy\APIHandler::getInstance();
    
    }
    
    function __destruct() {
	    if (isset($this->dbConnector)) { unset($this->dbConnector); }
	    if (isset($this->dbConnectorAsterisk)) { unset($this->dbConnectorAsterisk); }
	    if (isset($this->api)) { unset($this->api); }
    }    
    
    /** Administration of users */
    
    /**
     * Creating new user
     * @param String $email User login email id
     * @param String $password User login password
     */
    public function createUser($name, $password, $email, $phone, $role, $avatarURL) {
        // First check if user already existed in db
        //if ($this->userExistsIdentifiedByName($name) || $this->userExistsIdentifiedByEmail($email)) {
	if ($this->userExistsIdentifiedByName($name)) {
            // User with same email already existed in the db
            return USER_ALREADY_EXISTED;
        } else {
            // Generating password hash
            //$password_hash = \creamy\PassHash::hash($password);
            if (empty($avatarURL)) $avatarURL = CRM_DEFAULTS_USER_AVATAR;

			// check if confirmation email needs to be sent
			$needsConfirmation = $this->getSettingValueForKey(CRM_SETTING_CONFIRMATION_EMAIL);
			// start transaction
			$this->dbConnectorAsterisk->startTransaction();
            // insert query
            $data = Array(
	            // "name" => $name,
	            "user" => $name,
	            //"password_hash" => $password_hash,
	            "pass" => $password,
	            "email" => $email,
	            "phone_login" => $phone,
	            "phone_pass" => $password,
	            // "role" => $role,
	            "user_level" => $role,
	            //"avatar" => $avatarURL,
	            //"creation_date" => $this->dbConnector->now(),
	            // "status" => ($needsConfirmation ? "0" : "1")
	            "active" =>	($needsConfirmation ? "Y" : "N")
            );
            $id = $this->dbConnectorAsterisk->insert(CRM_USERS_TABLE_NAME_ASTERISK, $data);
            // Check for successful insertion
            if ($id) { // User successfully inserted
	            // send confirmation email if needed.
	           // if ($needsConfirmation) {
					//require_once('MailHandler.php');
					//$mh = \creamy\MailHandler::getInstance();
					//if ($mh->sendAccountActivationEmail($email)) {
					//	$this->dbConnectorAsterisk->commit();
	                	//return USER_CREATED_SUCCESSFULLY;
					//} else {
					//	$this->dbConnectorAsterisk->rollback();
	                	//return USER_CREATE_FAILED;
					//}            
	           // } else {
		            // return result
					//$this->dbConnectorAsterisk->commit();
	                //return USER_CREATED_SUCCESSFULLY;
	            //}
		// return result
		$this->dbConnectorAsterisk->commit();
	        return USER_CREATED_SUCCESSFULLY;
            } else { // Failed to create user
		$this->dbConnectorAsterisk->rollback();
                return USER_CREATE_FAILED;
            }
        }
    }

	/**
	 * Modifies user's data.
	 * @param Int $modifyid id of the user to be modified.
	 * @param String $name new email for the user.
	 * @param String $phone new phone for the user.
	 * @param String $role new role for the user.
	 * @param String $avatar new avatar URI for the user. Old avatar will be deleted from disk.
	 * return boolean true if user was successfully modified, false otherwise.
	 */
	public function modifyUser($modifyid, $name, $email, $phone, $role, $avatar) {
		if (!empty($avatar)) { // If we are modifying the user's avatar, make sure to delete the old one.
			// get user data and remove previous avatar.
			$userdata = $this->getDataForUser($modifyid);
			// $ih = new \creamy\ImageHandler();
			// if (!empty($userdata["avatar"]) && strpos($userdata["avatar"], CRM_DEFAULTS_USER_AVATAR_IMAGE_NAME) === false) {
			// 	$ih->removeUserAvatar($userdata["avatar"]);
			// }

			// update with new avatar
			// $data = Array("name" => $name, "phone" => $phone, "avatar" => $avatar, "role" => $role);
			$data = Array("user" => $name, "email" => $email, "phone_login" => $phone, "user_level" => $role);
		} else { // no avatar change required, just update the values.
			$data = Array("user" => $name, "email" => $email, "phone_login" => $phone, "user_level" => $role);
		}

		// prepare query depending on parameters.
		$this->dbConnectorAsterisk->where("user_id", $modifyid);
		// execute and return results
		return ( $this->dbConnectorAsterisk->update(CRM_USERS_TABLE_NAME_ASTERISK, $data) );

   	}

	/**
	 * Deletes a user from the database.
	 * @param Int $userid id of the user to be deleted.
	 * return boolean true if user was successfully deleted, false otherwise.
	 */
	 public function deleteUser($userid) {
	 	// safety checks
	 	if (empty($userid)) return false;
	 	
	 	// start transaction because we will perform several atomic operations.
	 	$this->dbConnectorAsterisk->startTransaction();
	 	 	
	 	// first check if we need to remove the avatar.
	 	// $data = $this->getDataForUser($userid);
	 	// if (isset($data["avatar"])) {
		 // 	$ih = new \creamy\ImageHandler();
		 // 	if (!$ih->removeUserAvatar($data["avatar"])) { $this->dbConnector->rollback(); return false; }
	 	// }
	 	// // delete the user notifications
	 	// $this->dbConnector->where("target_user", $userid);
	 	// if (!$this->dbConnector->delete(CRM_NOTIFICATIONS_TABLE_NAME)) { $this->dbConnector->rollback(); return false; }
	 	
	 	// // delete the user events.
	 	// $this->dbConnector->where("user_id", $userid);
	 	// if (!$this->dbConnector->delete(CRM_EVENTS_TABLE_NAME)) { $this->dbConnector->rollback(); return false; }
	 	
	 	// // deletes the user tasks
	 	// $this->dbConnector->where("user_id", $userid);
	 	// if (!$this->dbConnector->delete(CRM_TASKS_TABLE_NAME)) { $this->dbConnector->rollback(); return false; }
	 	
	 	// // delete the user messages.
	 	// // inbox
	 	// $this->dbConnector->where("user_to", $userid);
	 	// if (!$this->dbConnector->delete(CRM_MESSAGES_INBOX_TABLE_NAME)) { $this->dbConnector->rollback(); return false; }
	 	// // outbox
	 	// $this->dbConnector->where("user_from", $userid);
	 	// if (!$this->dbConnector->delete(CRM_MESSAGES_OUTBOX_TABLE_NAME)) { $this->dbConnector->rollback(); return false; }
	 	// // junk
	 	// $this->dbConnector->where("user_to", $userid)->where("origin_folder", CRM_MESSAGES_INBOX_TABLE_NAME);
	 	// if (!$this->dbConnector->delete(CRM_MESSAGES_JUNK_TABLE_NAME)) { $this->dbConnector->rollback(); return false; }
	 	// $this->dbConnector->where("user_from", $userid)->where("origin_folder", CRM_MESSAGES_OUTBOX_TABLE_NAME);
	 	// if (!$this->dbConnector->delete(CRM_MESSAGES_JUNK_TABLE_NAME)) { $this->dbConnector->rollback(); return false; }
	 	
	 	// last remove the user entry at the database
	 	$this->dbConnectorAsterisk->where("user_id", $userid);
	 	$result = $this->dbConnectorAsterisk->delete(CRM_USERS_TABLE_NAME_ASTERISK);
	 	if ($result === true) {
		 	$this->dbConnectorAsterisk->commit();
		 	return true;
	 	} else  { $this->dbConnectorAsterisk->rollback(); return false; }
	 }

    /**
     * Checking user login by name
     * @param String $name User login name
     * @param String $password User login password
     * @return object an associative array containing the user's data if credentials are valid and login succeed, NULL otherwise.
     */
    public function checkLoginByName($name, $password, $ip_address) {
        // fetching user by name and password
        //$this->dbConnector->where("name", $name);
        //$userobj = $this->dbConnector->getOne(CRM_USERS_TABLE_NAME);
		// $this->dbConnectorAsterisk->where("user", $name);
        // $userobj = $this->dbConnectorAsterisk->getOne(CRM_USERS_TABLE_NAME_ASTERISK);
        $url = gourl."/goUsers/goAPI.php"; #URL to GoAutoDial API. (required)
		//$postfields["goUser"] = goUser; #Username goes here. (required)
		//$postfields["goPass"] = goPass; #Password goes here. (required)
		//$postfields["goAction"] = "goUserLogin"; #action performed by the [[API:Functions]]. (required)
		//$postfields["responsetype"] = responsetype; #json. (required)
		//$postfields["user_name"] = $name;
		//$postfields["user_pass"] = $password;

		$postfields_string = '';
		$postfields = array(
			'goUser' => goUser,
			'goPass' => goPass,
			'responsetype' => 'json',
			'goAction' => 'goUserLogin',
			'user_name' => $name,
			'user_pass' => $password,
			'ip_address' => $ip_address
		);



		foreach($postfields as $key=>$value) { $postfields_string .= $key.'='.$value.'&'; }
		$postfields_string = rtrim($postfields_string, '&');

		$ch = curl_init();
		//curl_setopt($ch, CURLOPT_URL, $url);
		//curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
		//curl_setopt($ch, CURLOPT_POST, count($postfields));
		//curl_setopt($ch, CURLOPT_TIMEOUT, 100);
		//curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		//curl_setopt($ch, CURLOPT_POSTFIELDS, $postfields_string);
		
		curl_setopt($ch,CURLOPT_URL, $url);
		curl_setopt($ch,CURLOPT_POST, count($postfields));
		curl_setopt($ch,CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch,CURLOPT_POSTFIELDS, $postfields_string);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		$data = curl_exec($ch);
		$userobj = json_decode($data);
		curl_close($ch);

		if ($userobj->result === "success") { // first match valid?
			//$password_hash = $userobj["password_hash"];
			//$status = $userobj["status"];
			$pass_hash = '';
			$cwd = $_SERVER['DOCUMENT_ROOT'];
			$password_hash = $userobj->pass;
			$status = $userobj->active;
			$user_role = $userobj->user_level;
			$_SESSION['level'] = $userobj->user_level;
			$bcrypt = $userobj->bcrypt;
			$salt = $userobj->salt;
			$cost = $userobj->cost;
			$phone_login = $userobj->phone_login;
			$realm = $userobj->realm;
			//$ha1_pass = md5("{$phone_login}:{$realm}:{$password}");
            $ha1_pass = $userobj->ha1;
			//if ($status == 1) { // user is active

			if ($bcrypt > 0) {
				//$pass_hash = exec("{$cwd}/bin/bp.pl --pass=$password --salt=$salt --cost=$cost");
				//$pass_hash = preg_replace("/PHASH: |\n|\r|\t| /",'',$pass_hash);
                $pass_options = [
                    'cost' => $cost,
                    'salt' => base64_encode($salt)
                ];
                $pass_hash = password_hash($password, PASSWORD_BCRYPT, $pass_options);
                $pass_hash = substr($pass_hash, 29, 31);				
			} else {$pass_hash = $password;}

			if ( preg_match("/Y/i", $status) ) {
				//if (\creamy\PassHash::check_password($password_hash, $password)) {
				if ($password_hash === $pass_hash) {
	                // User password is correct. return some interesting fields...
	                $arr = array();
	                //$arr["id"] = $userobj["id"];
	                //$arr["name"] = $userobj["name"];
	                //$arr["email"] = $userobj["email"];
	                //$arr["role"] = $userobj["role"];
	                //$arr["avatar"] = $userobj["avatar"];
					switch ($user_role) {
						case 9:
							$user_role = CRM_DEFAULTS_USER_ROLE_ADMIN;
							break;
						case 8:
							$user_role = CRM_DEFAULTS_USER_ROLE_SUPERVISOR;
							break;
						case 7:
							$user_role = CRM_DEFAULTS_USER_ROLE_TEAMLEADER;
							break;
						default:
							$user_role = CRM_DEFAULTS_USER_ROLE_AGENT;
					}
			
					$arr["id"] = $userobj->user_id;
	                $arr["name"] = $userobj->full_name;
	                $arr["email"] = $userobj->email;
	                $arr["phone_login"] = $userobj->phone_login;
	                $arr["phone_pass"] = $userobj->phone_pass;
					$arr["ha1"] = $ha1_pass;
					$arr["realm"] = $realm;
					$arr["bcrypt"] = $bcrypt;
					$arr["role"] = $user_role;
					$arr["avatar"] = $userobj->avatar;
					$arr["user_group"] = $userobj->user_group;
					$arr["use_webrtc"] = $userobj->use_webrtc;
					$arr["password_hash"] = $pass_hash;
	                
	                return $arr;
	            } else {
	                // user password is incorrect
	                return NULL;
	            }
			} else return NULL;
		} else {
			return NULL;
		}
    }
    
    /**
     * Checking user login by email
     * @param String $email User email
     * @param String $password User login password
     * @return object an associative array containing the user's data if credentials are valid and login succeed, NULL otherwise.
     */
    public function checkLoginByEmail($email, $password, $ip_address) {
        // fetching user by name and password
        //$this->dbConnector->where("email", $email);
        //$userobj = $this->dbConnector->getOne(CRM_USERS_TABLE_NAME);
		// $this->dbConnectorAsterisk->where("email", $email);
  //       $userobj = $this->dbConnectorAsterisk->getOne(CRM_USERS_TABLE_NAME_ASTERISK);

    	$postfields["goUser"] = goUser; #Username goes here. (required)
		$postfields["goPass"] = goPass; #Password goes here. (required)
		$postfields["goAction"] = "goUserLogin"; #action performed by the [[API:Functions]]. (required)
		$postfields["responsetype"] = responsetype; #json. (required)
		$postfields["user_email"] = $email;
		$postfields["user_pass"] = $password;
		$postfields["ip_address"] = $ip_address;

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
		$userobj = json_decode($data);

		if ($userobj) { // first match valid?
			//$password_hash = $userobj["password_hash"];
			//$status = $userobj["status"];
			// $password_hash = $userobj["pass"];
			// $status = $userobj["user_level"];
			$pass_hash = '';
			$cwd = $_SERVER['DOCUMENT_ROOT'];
			$password_hash = $userobj->pass;
			$status = $userobj->active;
			$user_role = $userobj->user_level;
			$bcrypt = $userobj->bcrypt;
			$salt = $userobj->salt;
			$cost = $userobj->cost;
			//if ($status == 1) { // user is active

			if ($bcrypt > 0) {
				//$pass_hash = exec("{$cwd}/bin/bp.pl --pass=$password --salt=$salt --cost=$cost");
				//$pass_hash = preg_replace("/PHASH: |\n|\r|\t| /",'',$pass_hash);
                $pass_options = [
                    'cost' => $cost,
                    'salt' => base64_encode($salt)
                ];
                $pass_hash = password_hash($password, PASSWORD_BCRYPT, $pass_options);
                $pass_hash = substr($pass_hash, 29, 31);				
			} else {$pass_hash = $password;}
			
			if ($user_role == 9) {
				//if (\creamy\PassHash::check_password($password_hash, $password)) {
				if ($password_hash === $pass_hash) {
	                // User password is correct. return some interesting fields...
	                $arr = array();
	                //$arr["id"] = $userobj["id"];
	                //$arr["name"] = $userobj["name"];
	                //$arr["email"] = $userobj["email"];
	                //$arr["role"] = $userobj["role"];
	                //$arr["avatar"] = $userobj["avatar"];
					// $arr["id"] = $userobj["user_id"];

	    //             $arr["name"] = $userobj["user"];
	    //             $arr["email"] = $userobj["email"];
	    //             $arr["role"] = $userobj["user_group"];
	    //             $arr["avatar"] = "";
					switch ($user_role) {
						case 9:
							$user_role = CRM_DEFAULTS_USER_ROLE_ADMIN;
							break;
						case 8:
							$user_role = CRM_DEFAULTS_USER_ROLE_SUPERVISOR;
							break;
						case 7:
							$user_role = CRM_DEFAULTS_USER_ROLE_TEAMLEADER;
							break;
						default:
							$user_role = CRM_DEFAULTS_USER_ROLE_AGENT;
					}

	                $arr["id"] = $userobj->user_id;
	                $arr["name"] = $userobj->full_name;
	                $arr["email"] = $userobj->email;
	                $arr["phone_login"] = $userobj->phone_login;
	                $arr["phone_pass"] = $userobj->phone_pass;
					$arr["role"] = $user_role;
					$arr["avatar"] = $userobj->avatar;
					$arr["user_group"] = $userobj->user_group;
					$arr["use_webrtc"] = $userobj->use_webrtc;
	                
	                return $arr;
	            } else {
	                // user password is incorrect
	                return NULL;
	            }
			} else return NULL;
		} else {
			return NULL;
		}
    }
    
    /**
	 * Changes the user password to $password1 (= $password2) if $oldpassword matches current password.
	 * This function is supposed to be called by a user changing its own password.
	 * @param String $userid ID of the user to change the password to.
	 * @param String $oldpassword old password to change.
	 * @param String $password1 new password
	 * @param String $password2 new password (must be = to $password1).
	 * @return boolean true if password was successfully changed, false otherwise.
	 */
	public function changePassword($userid, $oldpassword, $password1, $password2) {
		// safety check
		if ($password1 != $password2) return false;
		// get old password hash to check both.
		// $this->dbConnector->where("id", $userid);
		$this->dbConnectorAsterisk->where("user_id", $userid);
		$userobj = $this->dbConnectorAsterisk->getOne(CRM_USERS_TABLE_NAME_ASTERISK);
		// check if password change is valid
		if ($userobj) {
			// $password_hash = $userobj["password_hash"];
			// $status = $userobj["status"];
			$password_hash = $userobj["pass"];
			$status = $userobj["active"];
			// if ($status == 1) { // user is active, check old password.
			if ($status == 'Y' || $status == 'y') { 
				// if (\creamy\PassHash::check_password($password_hash, $oldpassword)) {
				if ($password_hash == $oldpassword) {
	                // oldpassword is correct, change password.
	                // $newPasswordHash = \creamy\PassHash::hash($password1);
	                $newPasswordHash = $password1;
					// $this->dbConnector->where("id", $userid);
					$this->dbConnectorAsterisk->where("user_id", $userid);
					// $data = Array("password_hash" => $newPasswordHash);
					$data = Array("pass" => $newPasswordHash, "phone_pass" => $newPasswordHash);
					return $this->dbConnectorAsterisk->update(CRM_USERS_TABLE_NAME_ASTERISK, $data);
	            } else {
	                // oldpassword is incorrect
	                return false;
	            }
			} else return false;
		} else {
			return false;
		}
	}
	
    /**
	 * Changes the user password to $password, without checking for valid old password.
	 * This function is intended to be called only by superuser or a CRM administrator, with admin role.
	 * @param String $userid ID of the user to change the password to.
	 * @param String $password new password
     * @return boolean true if operation succeed.
	 */
	public function changePasswordAdmin($userid, $password) {
		// $newPasswordHash = \creamy\PassHash::hash($password);
		$newPasswordHash = $password;
		$this->dbConnectorAsterisk->where("user_id", $userid);
		$data = Array("pass" => $newPasswordHash, "phone_pass" => $newPasswordHash);
		return $this->dbConnectorAsterisk->update(CRM_USERS_TABLE_NAME_ASTERISK, $data);
	}
    
    /**
     * Gets the data of a user.
     * @param String $userid id of the user to get data from.
     * @return object an associative array containing the user's relevant data if the user id valid, NULL otherwise.
     */
    public function getDataForUser($userid) {
	    $this->dbConnectorAsterisk->where("user_id", $userid);
	    // $cols = array("id", "name", "email", "phone", "role", "avatar", "creation_date");
	    $cols = array("user_id", "user", "email", "phone_login", "user_level");
	    return $this->dbConnectorAsterisk->getOne(CRM_USERS_TABLE_NAME_ASTERISK, null, $cols);
    }
        
    /**
     * Gets the data of a user.
     * @param String $userid id of the user to get data from.
     * @return object an associative array containing the user's relevant data if the user id valid, NULL otherwise.
     */
    public function getDataForUserWithEmail($email) {
	    $this->dbConnectorAsterisk->where("email", $email);
	    // $cols = array("id", "name", "email", "phone", "role", "avatar", "creation_date");
	    $cols = array("user_id", "user", "email", "phone_login", "user_level");
	    return $this->dbConnectorAsterisk->getOne(CRM_USERS_TABLE_NAME_ASTERISK, null, $cols);
    }
    
    /**
     * Returns an array containing all enabled users (those with status=1).
     * @return Array an array of objects containing the data of all users in the system.
	 */
	public function getAllEnabledUsers() {
		$user = \creamy\CreamyUser::currentUser();
		$userGroup = $this->getUserGroup($user->getUserId());
		
		// $this->dbConnector->where("status", "1");
		$this->dbConnectorAsterisk->where("active", "Y");
		$this->dbConnectorAsterisk->where("user", array('VDAD', 'VDCL', 'goAPI'), 'not in');
		if ($user->getUserRole() == CRM_DEFAULTS_USER_ROLE_AGENT) {
			$this->dbConnectorAsterisk->where('user_level', '7', '>=');
			if ($userGroup != false) {
				$userGroup = ($userGroup == 'AGENTS') ? 'ADMIN' : $userGroup;
				$this->dbConnectorAsterisk->where('user_group', $userGroup);
			}
		}
		// $cols = array("id", "name", "email", "phone", "role", "avatar", "creation_date", "status");
		$cols = array("user_id", "user", "email", "phone_login", "user_level", "active", "full_name");
		return $this->dbConnectorAsterisk->get(CRM_USERS_TABLE_NAME_ASTERISK, null, $cols);
	}
    
    /**
     * Checking for duplicate user by name
     * @param String $name name to check in db
     * @return boolean
     */
    public function userExistsIdentifiedByName($name) {
	    $this->dbConnectorAsterisk->where("name", $name);
	    $this->dbConnectorAsterisk->get(CRM_USERS_TABLE_NAME_ASTERISK);
	    return ($this->dbConnectorAsterisk->getRowCount() > 0);
    }

    /**
     * Checking for existing email for a user in the database
     * @param String $email email to check in db
     * @return boolean true if operation succeed.
     */
    public function userExistsIdentifiedByEmail($email) {
	    $this->dbConnectorAsterisk->where("email", $email);
	    $this->dbConnectorAsterisk->get(CRM_USERS_TABLE_NAME_ASTERISK);
	    return ($this->dbConnectorAsterisk->getRowCount() > 0);
    }

    /**
     * Returns an array containing all users in the system (only relevant data).
     * @return Array an array of objects containing the data of all users in the system.
     */
   	public function getAllUsers() {
	   	// $cols = array("id", "name", "email", "phone", "creation_date", "role", "avatar", "status");
	   	$cols = array("user_id", "user", "email", "phone_login", "user_level", "active");
	   	return $this->dbConnectorAsterisk->get(CRM_USERS_TABLE_NAME_ASTERISK, null, $cols);
	}
	
	/**
	 * Changes the status for a user, from enabled (=1) to disabled (=0) or viceversa.
     * @param $userid Int the id of the user
     * @param $status Int the new status for the user
	 */
	public function setStatusOfUser($userid, $status) {
		// $this->dbConnector->where("id", $userid);
		// $data = array("status" => $status);
		$this->dbConnectorAsterisk->where("user_id", $userid);
		$data = array("active" => $status);
		return $this->dbConnectorAsterisk->update(CRM_USERS_TABLE_NAME_ASTERISK, $data);
	}
	
	/** Password recovery */

	/** Checks link validity for a password reset code */
	public function checkEmailSecurityCode($email, $date, $nonce, $code) {
		$checkCode = $this->generateEmailSecurityCode($email, $date, $nonce);
		if ($checkCode == $code) { // if codes match (not tainted data)
			$parsed = date_parse_from_format('Y-m-d-H-i-s', $date);
			$requestTimestamp = mktime(
		        $parsed['hour'], 
		        $parsed['minute'], 
		        $parsed['second'], 
		        $parsed['month'], 
		        $parsed['day'], 
		        $parsed['year']
			);
			$currentTimestamp = time();
			// check if no more than 24h have passed.
			$diff = $currentTimestamp - $requestTimestamp;
			if ($diff > 0 && $diff < (60*60*24)) { return true; }
		}
		return false;
	}

	/** Generates a password reset code, a md5($email + $date + $nonce + CRM_SECURITY_TOKEN) */
	public function generateEmailSecurityCode($email, $date, $nonce) {
		$baseString = $email.$date.$nonce.CRM_SECURITY_TOKEN;
		return md5($baseString);
	}
	
	/** 
	 * Changes the password of a user identified by an email. The user must have a valid email in the database.
	 * @param $email String the email of the user.
	 * @param $password the new password for the user.
	 */
	public function changePasswordForUserIdentifiedByEmail($email, $password) {
		if ($this->userExistsIdentifiedByEmail($email)) {
	        // Generating password hash
	        $password_hash = \creamy\PassHash::hash($password);
	        $this->dbConnector->where("email", $email);
	        $data = array("password_hash" => $password_hash);
	        return $this->dbConnector->update(CRM_USERS_TABLE_NAME, $data);
		}
		return false;
	}
	
	/** Settings */

	/** Returns the value for a setting with a given key */
	public function getSettingValueForKey($key, $context = CRM_SETTING_CONTEXT_CREAMY) {
		$this->dbConnector->where("setting", $key);
		$this->dbConnector->where("context", $context);
		if ($result = $this->dbConnector->getOne(CRM_SETTINGS_TABLE_NAME)) {
			return $result["value"];
		}else{
			if ($result = $this->dbConnectorAsterisk->getOne(CRM_SETTINGS_TABLE_NAME)) {
				return $result["value"];
			}else return NULL;	
		}
	}
	
	/** Returns the value for a setting with a given key */
	public function getSettingValueForKeyAsBooleanValue($key, $context = CRM_SETTING_CONTEXT_CREAMY) {
		$rawValue = $this->getSettingValueForKey($key, $context);
		if (!isset($rawValue)) { return false; } // default value.
		else return filter_var($rawValue, FILTER_VALIDATE_BOOLEAN); 
	}	
	
	/** 
	 * Sets the value for a setting.
	 * @param String $key		name or key for the setting
	 * @param Any $value		value for the setting
	 * @param String $context	The context indicates if it is a Creamy core setting of belongs to a module.
	 */
	public function setSettingValueForKey($key, $value, $context = CRM_SETTING_CONTEXT_CREAMY) {
		$this->dbConnector->where("setting", $key);
		$this->dbConnector->where("context", $context);
		// update or insert?
		if ($this->dbConnector->getOne(CRM_SETTINGS_TABLE_NAME)) {
			// exists previously, update.
			$this->dbConnector->where("setting", $key);
			$this->dbConnector->where("context", $context);
			$data = array("value" => $value);
			return $this->dbConnector->update(CRM_SETTINGS_TABLE_NAME, $data);
		} else { // Insert the new value
			$data = array("setting" => $key, "context" => $context, "value" => $value);
			return $this->dbConnector->insert(CRM_SETTINGS_TABLE_NAME, $data);
		}
	}
	
	public function setSettings($data) {
		$this->dbConnector->startTransaction();
		if (is_array($data) && !empty($data)) {
			foreach ($data as $key => $value) {
				// locale
				if ($key == CRM_SETTING_LOCALE) { $result = $this->setLocaleSetting($value); }
				// timezone
				else if ($key == CRM_SETTING_TIMEZONE) { $result = $this->setTimezoneSetting($value); }
				// other settings.
				else { $result = $this->setSettingValueForKey($key, $value); }
				
				// failure ?
				if ($result === false) {
					$this->dbConnector->rollback();
					return false;
				}
				
			}
		}
		$this->dbConnector->commit();
		return true;
	}
	
	public function getMainAdminUserData() {
		$adminUserId = $this->getSettingValueForKey(CRM_SETTING_ADMIN_USER);
		if (!empty($adminUserId)) {
			return $this->getDataForUser($adminUserId);
		}
	}
	
	public function getMainAdminEmail() {
		$adminUserData = $this->getMainAdminUserData();
		if (isset($adminUserData)) { return $adminUserData["email"]; }
		else { return null; }
	}
	
	// special settings that need some extra work.

	public function getNotificationsForEventsSetting() { return $this->getSettingValueForKey(CRM_SETTING_EVENTS_EMAIL); }
	
	public function getLocaleSetting() { return $this->getSettingValueForKey(CRM_SETTING_LOCALE); }

	public function getTimezoneSetting() { return $this->getSettingValueForKey(CRM_SETTING_TIMEZONE); }

	public function setLocaleSetting($newLocale) {
		if ($this->setSettingValueForKey(CRM_SETTING_LOCALE, $newLocale)) {
			// update Language handler.
			\creamy\LanguageHandler::getInstance()->setLanguageHandlerLocale($newLocale);
			return true;
		}
		return false;
	}
	
	public function setTimezoneSetting($newTimezone) {
		if ($this->setSettingValueForKey(CRM_SETTING_TIMEZONE, $newTimezone)) {
			// update timezone information.
	        ini_set('date.timezone', $newTimezone);
			date_default_timezone_set($newTimezone);
			return true;
		}
		return false;
	}

	/** Customers */
	
	/**
	 * Gets all customers of certain type.
	 * @param $customerType the type of customer to retrieve.
	 * @return Array an array containing the objects with the users' data.
	 */
	public function getAllCustomersOfType($customerType, $numRows = null, $sorting = null, $filtering = null) {
		// safety check
		if (!isset($customerType)) return array();
		
		// columns
		$cols = $this->getCustomerColumnsToBeShownInCustomerList($customerType);
		
		// sorting
		if (isset($sorting) && count($sorting) > 0) {
			foreach ($sorting as $columnToSort => $sortType) { $this->dbConnector->orderBy($columnToSort, $sortType); }
		}
		
		// filtering
		if (isset($filtering) && count($filtering) > 0) {
			$i = 0;
			foreach ($filtering as $columnToSearch => $wordToSearch) {
				if ($i == 0) { $this->dbConnector->where($columnToSearch, '%'.$wordToSearch.'%', "LIKE"); }
				else { $this->dbConnector->orWhere($columnToSearch, '%'.$wordToSearch.'%', 'LIKE'); }
				$i++;
			}
		}
		
		// perform query and execute results.
		return $this->dbConnector->get($customerType, $numRows, $cols, true);
   	}
	//edited
	public function getAllContactOfType_ASTERISK($customerType, $numRows = null, $sorting = null, $filtering = null) {
		// safety check
		if (!isset($customerType)) return array();
		
		// columns
		$cols = $this->getCustomerColumnsToBeShownInCustomerList($customerType);
		
		// sorting
		if (isset($sorting) && count($sorting) > 0) {
			foreach ($sorting as $columnToSort => $sortType) { $this->dbConnectorAsterisk->orderBy($columnToSort, $sortType); }
		}
		
		// filtering
		if (isset($filtering) && count($filtering) > 0) {
			$i = 0;
			foreach ($filtering as $columnToSearch => $wordToSearch) {
				if ($i == 0) { $this->dbConnectorAsterisk->where($columnToSearch, '%'.$wordToSearch.'%', "LIKE"); }
				else { $this->dbConnectorAsterisk->orWhere($columnToSearch, '%'.$wordToSearch.'%', 'LIKE'); }
				$i++;
			}
		}
		
		// perform query and execute results.
		return $this->dbConnectorAsterisk->get($customerType, $numRows, $cols, true);
   	}
	/**
	 * Gets the customer columns to be shown in the customer list.
	 * @param $customerType the type of customer to retrieve.
	 * @return Array an array containing the columns to be shown in the customer list.
	 */	
	public function getCustomerColumnsToBeShownInCustomerList($customerType) {
		// try to get fields from database.
		//$setting = $this->getSettingValueForKey(CRM_SETTING_CUSTOMER_LIST_FIELDS);
		$setting = "lead_id|CONCAT_WS(' ', first_name, middle_initial, last_name)|phone_number|address1";
		if (isset($setting)) { return explode("|", $setting); }
		
		// fallback to default columns
		return explode(",", CRM_SETTING_DEFAULT_CUSTOMER_LIST_FIELDS);
	}
	
	
	/**
	 * Gets the first groups of customers other than contacts. If no other customer group is found, returns null.
	 */
	public function getFirstCustomerGroupTableName() {
		//edited
		$this->dbConnector->where("table_name", "vicidial_list", "!=");
		return $this->dbConnector->getOne(CRM_CUSTOMER_TYPES_TABLE_NAME);
	}
	
	/**
	 * Creates a new customer
	 * @param $customerType String type of customer (= table where to insert the new customer).
	 * @param $name String name for the new customer
	 * @param $phone String (home) phone for the new customer
	 * @param $mobile String mobile phone for the new customer.
	 * @param $id_number String passport, dni, nif, VAT number or identifier for the customer
	 * @param $address String physical address for that customer
	 * @param $city String City of the customer
	 * @param $state String state for the customer
	 * @param $zipcode String ZIP code for the customer
	 * @param $country String Country for the customer  
	 * @param $birthdate String Birthdate of the customer, expressed in the proper locale format, with month, days and years separated by '/' or '-'.  
	 * @param $maritalstatus String Marital status of the customer (single=1, married=2, divorced=3, separated=4, widow/er=5)  
	 * @param $productType String Product type or definition of the product/service sold to the customer or in which the customer is interested in.
	 * @param $donotsendemail Int a integer/boolean to indicate whether the customer doesn't want to receive email (=1) or is just fine receiving them (=0).
	 * @param $createdByUser Int id of the user that inserted the customer in the system.  
	 * @param $gender Int gender of the customer (female=0, male=1).  
	 * @param $notes String notes for the customer (test, annotations, etc).  
	 * @param $website String Website of the customer. 
	 * @return boolean true if insert was successful, false otherwise.
	 */
	//edited
	public function createCustomer($customerType, $first_name, $middle_initial, $last_name, $email, $phone, $alt_phone, 
		$address1, $address2, $address3, $city, $state, $province, $postal_code, $country, $date_of_birth, $createdByUser, $gender, $comments) {
		// sanity checks
		if (empty($customerType)) return false;
		
		// generate correct, mysql-ready date.
		$correctDate = NULL;
		if (!empty($date_of_birth)) $correctDate = date('Y-m-d',strtotime(str_replace('/','-', $date_of_birth)));
		
		// prepare and execute query.
		$data = array(
			"first_name" => $first_name,
			"middle_initial" => $middle_initial,
			"last_name" => $last_name,
			"email" => $email,
			"phone_number" => $phone,
			"alt_phone" => $alt_phone,
			"address1" => $address1,
			"address2" => $address2,
			"address3" => $address3,
			"city" => $city,
			"state" => $state,
			"province" => $province,
			"postal_code" => $postal_code,
			"country_code" => $country,
			"date_of_birth" => $correctDate,
			"entry_date" => $this->dbConnectorAsterisk->now(),
			"user" => $createdByUser,
			"gender" => $gender,
			"comments" => $comments
		);
	//insert to asterisk
		if ($this->dbConnectorAsterisk->insert($customerType, $data)) { return true; }
		else { error_log($this->dbConnectorAsterisk->getLastError()); return false; }
	
	
	}

	/**
	 * Modifies the data of an existing customer
	 * @param $customerType String type of customer (= table where to insert the new customer).
	 * @param $customerType Int id of the customer in the database
	 * @param $name String name for the new customer
	 * @param $phone String (home) phone for the new customer
	 * @param $mobile String mobile phone for the new customer.
	 * @param $id_number String passport, dni, nif, VAT number or identifier for the customer
	 * @param $address String physical address for that customer
	 * @param $city String City of the customer
	 * @param $state String state for the customer
	 * @param $zipcode String ZIP code for the customer
	 * @param $country String Country for the customer  
	 * @param $birthdate String Birthdate of the customer, expressed in the proper locale format, with month, days and years separated by '/' or '-'.  
	 * @param $maritalstatus String Marital status of the customer (single=1, married=2, divorced=3, separated=4, widow/er=5)  
	 * @param $productType String Product type or definition of the product/service sold to the customer or in which the customer is interested in.
	 * @param $donotsendemail Int a integer/boolean to indicate whether the customer doesn't want to receive email (=1) or is just fine receiving them (=0).
	 * @param $createdByUser Int id of the user that inserted the customer in the system.  
	 * @param $gender Int gender of the customer (female=0, male=1).  
	 * @param $notes String notes for the customer 
	 * @param $website String Website of the customer. 
	 * @return boolean true if insert was successful, false otherwise.
	 */
	//edited
	public function modifyCustomer($customerType, $customerid, $fname, $mi, $lname, $email, $phone, $alt_phone, $address1, $address2, $address3, $city, $state, $province, $postal_code, $country, $date_of_birth, $createdByUser, $gender, $comments) {
		// determine customer type (target table) and sanity checks.
		$correctDate = NULL;
		if (!empty($date_of_birth)) $correctDate = date('Y-m-d',strtotime(str_replace('/','-', $date_of_birth)));
		
		// prepare and execute query
				// prepare and execute query.
		$data = array(
			"first_name" => $fname,
			"middle_initial" => $mi,
			"last_name" => $lname,
			"email" => $email,
			"phone_number" => $phone,
			"alt_phone" => $alt_phone,
			"address1" => $address1,
			"address2" => $address2,
			"address3" => $address3,
			"city" => $city,
			"state" => $state,
			"province" => $province,
			"postal_code" => $postal_code,
			"country_code" => $country,
			"date_of_birth" => $correctDate,
			"gender" => $gender,
			"comments" => $comments
		);
	//update asterisk
		$this->dbConnectorAsterisk->where("lead_id", $customerid);
		return $this->dbConnectorAsterisk->update($customerType, $data);
	}
		
	/**
     * Gets the data of a customer.
     * @param Int $userid id of the customer to get data from.
     * @param String $customerType type of the customer to get data from.
     * @return Array an array containing the customer data, or NULL if customer wasn't found.
     */
    public function getDataForCustomer($customerid, $customerType) {
	   //edited
	    $this->dbConnectorAsterisk->where("lead_id", $customerid);
	    $result = $this->dbConnectorAsterisk->getOne($customerType);
	    if (isset($result)) $result["customer_type"] = $customerType;
	    return $result;
    }
    
    /**
	 * Deletes a customer from his/her database.
	 * @param $customerid Int id of the customer to delete.
	 * @param $customerType String type (=table) of the customer.
	 */
	
	 public function deleteCustomer($customerid, $customerType) {
		 // sanity checks
	 	if (empty($customerid) || empty($customerType)) return false;
	 	// then remove the entry at the database
	 	//edited
		$this->dbConnectorAsterisk->where("lead_id", $customerid);
	 	return $this->dbConnectorAsterisk->delete($customerType);
	 }
	
	/**
	 * Deletes a customer type. This function will delete the table and all associated registers.
	 * @param Int customerType the identifier of the customer type to delete (= id in table customer_types).
	 */
	public function deleteCustomerType($customerTypeId) {
		// safety checks && get the table name for the customer type.
		if ($customerTypeId == 1) { return false; } // we don't want to delete the basic contacts table.
		$this->dbConnector->where("id", $customerTypeId);
		if ($result = $this->dbConnector->getOne(CRM_CUSTOMER_TYPES_TABLE_NAME)) { 
			$tableName = $result["table_name"]; 
		}
		if (!isset($tableName)) { return false; }
		
		// We will set a transaction to make sure we delete everything at once.
		$this->dbConnector->startTransaction();
		// try to delete the association first.
		$this->dbConnector->where("id", $customerTypeId);
		if ($this->dbConnector->delete(CRM_CUSTOMER_TYPES_TABLE_NAME)) {
			// now try to delete all the customer table.
			if ($this->dbConnector->dropTable($tableName)) { // success
				// now try to delete the statistics column.
				if ($this->dbConnector->dropColumnFromTable(CRM_STATISTICS_TABLE_NAME, $tableName)) {
					// success!
					$this->dbConnector->commit();
					return true;
				}
			}
			// TODO: Warn the modules about the deletion, in case they need to modify something.
		}
		$this->dbConnector->rollback();
		return false;
	}
	
	/**
	 * Changes a customer identified by a customerid from one customer type to the other.
	 * The function first gets all data from the customer, then deletes the customer from
	 * the old table, and then adds the customer in the new table. These actions are
	 * performed atomically thanks to the use of transactions.
	 * @param Int $customerid 			The id of the customer to change.
	 * @param String $oldCustomerType	The old table name (customer type) for the customer.
	 * @param String $newCustomerType	The new table name (customer type) for the customer.
	 * @return Bool						true if change was successful, false otherwise.
	 */
	public function changeCustomerType($customerid, $oldCustomerType, $newCustomerType) {
		// safety checks.
		if (empty($customerid) || empty($oldCustomerType) || empty($newCustomerType)) { return false; }
		if ($oldCustomerType == $newCustomerType) { return false; }
		// start transaction to ensure atomic operation.
		$this->dbConnector->startTransaction();
		
		// 1. Retrieve all data from the customer.
		$this->dbConnector->where("id", $customerid);
		$oldCustomerData = $this->dbConnector->getOne($oldCustomerType);
		if (!isset($oldCustomerData)) { $this->dbConnector->rollback(); return false; }
		
		// 2. Delete customer from the old table.
		$this->dbConnector->where("id", $customerid);
		if (!$this->dbConnector->delete($oldCustomerType)) { $this->dbConnector->rollback(); return false; }
		
		// 3. Add customer to the new table.
		unset($oldCustomerData["id"]); // generate new id in new table
		if ($this->dbConnector->insert($newCustomerType, $oldCustomerData)) { // success
			$this->dbConnector->commit();
			return true;
		} else { $this->dbConnector->rollback(); return false; }
	}
	
	private function createNewCustomersTable($tablename) {
		$fields = array(
			"company" => "int(1) NOT NULL DEFAULT 0",
			"name" => "varchar(255) NOT NULL",
			"id_number" => " varchar(255) DEFAULT NULL",
			"address" => "text",
			"city" => "varchar(255) DEFAULT NULL",
			"state" => "varchar(255) DEFAULT NULL",
			"zip_code" => "varchar(255) DEFAULT NULL",
			"country" => "varchar(255) DEFAULT NULL",
			"phone" => "text",
			"mobile" => "text",
			"email" => "varchar(255) DEFAULT NULL",
			"avatar" => "varchar(255) DEFAULT NULL",
			"type" => "text",
			"website" => "varchar(255) DEFAULT NULL",
			"company_name" => "varchar(255) DEFAULT NULL",
			"notes" => "text",
			"birthdate" => "datetime DEFAULT NULL",
			"marital_status" => "int(11) DEFAULT NULL",
			"creation_date" => "datetime DEFAULT NULL",
			"created_by" => "int(11) NOT NULL",
			"do_not_send_email" => "char(1) DEFAULT NULL",
			"gender" => "int(1) DEFAULT NULL");
		
		$unique_keys = array("name", "");
		
		return $this->dbConnector->createTable($tablename, $fields, $unique_keys);
	}
	
	/**
	 * Adds a new customer type, creating the new customer tables and updating customer_types and statistics tables.
	 */
	public function addNewCustomerType($description) {
		// we generate a random temporal name for the table.
		$rsg = new \creamy\RandomStringGenerator();
		$tempName = "temp".$rsg->generate(20);

		$this->dbConnector->startTransaction();
		// first we need to insert the customer_type register, because we don't have a table_name yet.
		$data = array("table_name" => $tempName, "description" => $description);
		$id = $this->dbConnector->insert(CRM_CUSTOMER_TYPES_TABLE_NAME, $data);
		if ($id) { // if insertion was successful, use the generated auto_increment id to set the name of the table_name.
		//edited	
			$tableName = "vicidial_list";
			$this->dbConnector->where("id", $id);
			$finalData = array("table_name" => $tableName);
			if ($this->dbConnector->update(CRM_CUSTOMER_TYPES_TABLE_NAME, $finalData)) { // success!
				// now we try to add the new customers table.
				if ($this->createNewCustomersTable($tableName)) { // success!
					// now try to add to statistics.
					if ($this->dbConnector->addColumnToTable(CRM_STATISTICS_TABLE_NAME, $tableName, "INT(11) DEFAULT 0", "0")) {
						// success!
						$this->dbConnector->commit();
						return true;
					}
				}
			}
		}
		$this->dbConnector->rollback();
		return false;
	}
	
	/**
	 * Modifies the description for a type of customer.
	 */
	public function modifyCustomerDescription($customerTypeId, $newDescription) {
		// update description
		$this->dbConnectorAsterisk->where("lead_id", $customerTypeId);
		$data = array("description" => $newDescription);
		return $this->dbConnectorAsterisk->update(CRM_CUSTOMER_TYPES_TABLE_NAME, $data);
	}
	
	/**
	 * Retrieves an array containing an array with all the customer types expressed as an associative array.
	 * @return Array the list of customer type structures.
	 */
	public function getCustomerTypes() {
		return $this->dbConnector->get(CRM_CUSTOMER_TYPES_TABLE_NAME);
	}
	
	/**
	 * Returns the number of customer types.
	 */
	public function getNumberOfCustomerTypes() {
		return $this->dbConnector->getValue(CRM_CUSTOMER_TYPES_TABLE_NAME, "count(*)");
	}
	
	/**
	 * Retrieves the customer "human friendly" description name for a customer type.
	 * @param $customerType String customer type ( = table name).
	 * @return String a human friendly description of this customer type.
	 */
	public function getNameForCustomerType($customerType) {
		$this->dbConnector->where("table_name", $customerType);
		return $this->dbConnector->getValue(CRM_CUSTOMER_TYPES_TABLE_NAME, "description");
	}
	
	/** tasks */

	/**
	 * Gets all tasks belonging to a given user.
	 * @param $userid Int id of the user.
	 * @return Array an array containing all task objects as associative arrays, or NULL if user was not found or an error occurred.
	 */
	public function getCompletedTasks($userid) {
		$this->dbConnector->where("user_id", $userid);
		$this->dbConnector->where("completed", 100);
		$this->dbConnector->orderBy("creation_date", "Desc");
		return $this->dbConnector->get(CRM_TASKS_TABLE_NAME);
	}

	
	/**
	 * Retrieves the number of unfinished tasks.
	 * @param Int $userid returns the number of unfinished tasks of the user.
	 */
	 public function getUnfinishedTasksNumber($userid) {
		$this->dbConnector->where("user_id", $userid);
		$this->dbConnector->where("completed", 100, "<");
		if ($this->dbConnector->get(CRM_TASKS_TABLE_NAME)) {
			return $this->dbConnector->count;
		} else { return 0; }
	 }
	 
	/**
	 * Retrieves the unfinished tasks of a user as an array of tasks objects.
	 * @param Int $userid returns the unfinished tasks of the user.
	 */
	 public function getUnfinishedTasks($userid) {
		$this->dbConnector->where("user_id", $userid);
		$this->dbConnector->where("completed", 100, "<");
		$this->dbConnector->orderBy("creation_date", "Desc");
		$result = $this->dbConnector->get(CRM_TASKS_TABLE_NAME);
		return isset($result) ? $result : array();
	 }
	 
	/**
	 * Creates a new task for a user.
	 * @param $userid Int id of the user creating the new task.
	 * @param $taskDescription String description of the new task.
	 * @param $taskInitialProgress Int initial completion percentage of the task that has been completed (0-100).
	 * @return boolean true if operation was successful, false otherwise.
	 */
	public function createTask($userid, $taskDescription, $taskInitialProgress = 0) {
		// sanity checks
		if (empty($userid) || empty($taskDescription)) return false;
		else if (empty($taskInitialProgress)) $taskInitialProgress = 0;
		else if ($taskInitialProgress < 0) $taskInitialProgress = 0;
		else if ($taskInitialProgress > 100) $taskInitialProgress = 100;
	
		//var_dump($taskInitialProgress);
		$data = array(
			"user_id" => $userid, 
			"description" => $taskDescription, 
			"completed" => $taskInitialProgress, 
			//"creation_date" => $this->dbConnector->now()
			"creation_date" => date('Y-m-d H:i:s')
			//"creation_date" => $current_timestamppapi;
		);
		//if ($taskInitialProgress == 100) { $data["completion_date"] = $this->dbConnector->now(); }
		if ($taskInitialProgress == 100) { $data["completion_date"] = date('Y-m-d H:i:s'); }
		if ($this->dbConnector->insert(CRM_TASKS_TABLE_NAME, $data)) { return true; }
		else { return $false; }
	}
	
	/**
	 * Deletes a task
	 * @param $taskid Int id of the task to be deleted.
	 * @return boolean true if operation was successful, false otherwise.
	 */
	public function deleteTask($taskid) {
	 	// safety check
	 	if (empty($taskid)) return false;
	 	
	 	$this->dbConnector->where("id", $taskid);
	 	return $this->dbConnector->delete(CRM_TASKS_TABLE_NAME);
	}
	
	/**
	 * Sets the completed status of a task.
	 * @param $taskid Int identifier of the task
	 * @param $progress Int new completion status for the task (0-100).
	 * @param $userid Int id of the user the task belongs to.
	 * @return boolean true if modification was successful, false otherwise.
	 */
	public function setTaskCompletionStatus($taskid, $progress, $userid) {
		if (empty($taskid) || empty($progress) || empty($userid)) return false;
		
		$this->dbConnector->where("id", $taskid);
		$this->dbConnector->where("user_id", $userid);
		$data = array("completed" => $progress);
		return $this->dbConnector->update(CRM_TASKS_TABLE_NAME, $data);
	}
	
	/**
	 * Edits the description of the task
	 * @param $taskid Int identifier of the task
	 * @param $description String new progress for the task (0-100).
	 * @param $userid Int id of the user the task belongs to.
	 * @return boolean true if modification was successful, false otherwise.
	 */
	public function editTaskDescription($taskid, $description, $userid) {
		if (empty($taskid) || empty($description) || empty($userid)) return false;
		$this->dbConnector->where("id", $taskid);
		$this->dbConnector->where("user_id", $userid);
		$data = array("description" => $description);
		return $this->dbConnector->update(CRM_TASKS_TABLE_NAME, $data);
	}
	
	
	/** Messages */
	
	/**
	 * Sends a message from one user to another.
	 * @param Int $fromuserid 				id of the user sending the message.
	 * @param Int $touserid 				id of the user to send the message to.
	 * @param String $subject 				A valid RFC 2047 subject. See http://www.faqs.org/rfcs/rfc2047
	 * @param String $message 				body of the message to send (text/rich html).
	 * @param Array $attachments 			array of $_FILES with the attachments.
	 * @param String $attachmentTag		Tag that contains the attachment files in the $_FILES array.
	 * @param Array $external_recipients 	A valid RFC 2822 recipients set. See http://www.faqs.org/rfcs/rfc2822
	 * @return boolean 						true if successful, false otherwise
	 */
	public function sendMessage($fromuserid, $touserid, $subject, $message, $attachments, $external_recipients = null, $attachmentTag) {
		// sanity checks
		if (empty($fromuserid) || empty($touserid)) return false;
		if (empty($subject)) $subject = "(".$this->lh->translationFor("no_subject").")";
		if (empty($message)) $message = "(".$this->lh->translationFor("no_message").")";
		
		// first send to external recipients (if any), because we are moving them later with the call to move_uploaded_file.
		if (!empty($external_recipients)) {
			require_once('MailHandler.php');
			$mh = \creamy\MailHandler::getInstance();
			$result = $mh->sendMailWithAttachments($external_recipients, $subject, $message, $attachments, $attachmentTag);
		}
		
		// Now store the message in our database.
		// try to store the inbox message for the target user. Start transaction because we could have attachments.
		$this->dbConnector->startTransaction();
		$date = $this->dbConnector->now();
		// message data.
		$data = array(
			"user_from" => $fromuserid,
			"user_to" => $touserid,
			"subject" => $subject,
			"message" => $message,
			"date" => $date,
			"message_read" => 0,
			"favorite" => 0
		);
		// insert the new message in the inbox of the receiving user.
		$inboxmsgid = $this->dbConnector->insert(CRM_MESSAGES_INBOX_TABLE_NAME, $data);
		if (!$inboxmsgid) { $this->dbConnector->rollback(); return false; }
		
		// insert the new message in the outbox of the sending user.
		$data["message_read"] = 1;
		$outboxmsgid = $this->dbConnector->insert(CRM_MESSAGES_OUTBOX_TABLE_NAME, $data);
		if (!$outboxmsgid) { $this->dbConnector->rollback(); return false; }
		
		// insert into timeline table                
		$dataTL = array(
                        "type_id" => $inboxmsgid,
                        "type" => "message",
			"user_id" => $touserid,
			"user_from_id" => $fromuserid,
			"title" => $subject,
			"description" => $message,
			"start_date" => $date
		);
		$msgidTL = $this->dbConnector->insert(CRM_TIMELINE_TABLE_NAME, $dataTL);
	
		// insert attachments (if any).
		if (!$this->addAttachmentsForMessage($inboxmsgid, $outboxmsgid, $fromuserid, $touserid, $attachments, $attachmentTag)) {
			$this->dbConnector->rollback();
			return false;			
		}
		// success! commit transactions.
		$this->dbConnector->commit();
		return true;
	}
	
	
	/**
	 * Sends a message from one user to another.
	 * @param Int $fromuserid 				id of the user sending the message.
	 * @param Int $touserid 				id of the user to send the message to.
	 * @param String $subject 				A valid RFC 2047 subject. See http://www.faqs.org/rfcs/rfc2047
	 * @param String $message 				body of the message to send (text/rich html).
	 * @param Array $attachments 			array of $_FILES with the attachments.
	 * @param String $attachmentTag		Tag that contains the attachment files in the $_FILES array.
	 * @param Array $external_recipients 	A valid RFC 2822 recipients set. See http://www.faqs.org/rfcs/rfc2822
	 * @return boolean 						true if successful, false otherwise
	 */
	public function SMTPsendMessage($fromuserid, $touserid, $subject, $message, $attachments, $external_recipients, $attachmentTag) {
		// sanity checks
		if (empty($fromuserid) || empty($touserid)) return false;
		if (empty($subject)) $subject = "(".$this->lh->translationFor("no_subject").")";
		if (empty($message)) $message = "(".$this->lh->translationFor("no_message").")";
		
		// first send to external recipients (if any), because we are moving them later with the call to move_uploaded_file.
		if (!empty($external_recipients)) {
			require_once('MailHandler.php');
			$mh = \creamy\MailHandler::getInstance();
			$result = $mh->sendMailWithAttachments($external_recipients, $subject, $message, $attachments, $attachmentTag);
		}
		
		// Now store the message in our database.
		// try to store the inbox message for the target user. Start transaction because we could have attachments.
		$this->dbConnector->startTransaction();		
		// message data.
		$data = array(
			"user_from" => $fromuserid,
			"user_to" => $touserid,
			"external_recepient" => $external_recepients,
			"subject" => $subject,
			"message" => $message,
			"date" => $this->dbConnector->now(),
			"message_read" => 0,
			"favorite" => 0
		);
		
		// insert the new message in the outbox of the sending user.
		$data["message_read"] = 1;
		$outboxmsgid = $this->dbConnector->insert(CRM_MESSAGES_OUTBOX_TABLE_NAME, $data);
		if (!$outboxmsgid) { $this->dbConnector->rollback(); return false; }
	
		// insert attachments (if any).
		if (!$this->addAttachmentsForMessage($inboxmsgid, $outboxmsgid, $fromuserid, $touserid, $attachments, $attachmentTag)) {
			$this->dbConnector->rollback();
			return false;			
		}
		// success! commit transactions.
		$this->dbConnector->commit();
		return true;
	}
	
	/**
	 * Adds the attachments for a given message idenfitied by messageid, from user fromuserid and to
	 * user touserid. This method will create a new file in the /uploads directory for each attachment
	 * and add a link in the attachments table to both the fromuserid (in the output folder) and the
	 * touserid (in the inbox folder).
	 * @param Int $fromuserid 				id of the user sending the message.
	 * @param Int $touserid 				id of the user to send the message to.
	 * @param String $inboxmsgid			id of the message inserted in inbox.
	 * @param String $outboxmsgid			id of the message inserted in inbox.
	 * @param Array $attachments 			array of $_FILES with the attachments.
	 * @return boolean 						true if successful, false otherwise
	 */
	protected function addAttachmentsForMessage($inboxmsgid, $outboxmsgid, $fromuserid, $touserid, $attachments, $attachmentTag) {
		// no files, empty files.
		if (!isset($attachments)) { return true; }
		if (!is_array($attachments)) { return true; }
		if (count($attachments) < 1) { return true; }

		// Assign a new hashed name for files and store them.
		require_once('CRMUtils.php');
		// iterate through all the attachments and create the inbox/outbox links.
		for ($i = 0; $i < count($attachments[$attachmentTag]["tmp_name"]); $i++) {
			if ($attachments[$attachmentTag]['error'][$i] != UPLOAD_ERR_OK) { continue; }
			$relativeURL = \creamy\CRMUtils::generateUploadRelativePath($attachments[$attachmentTag]['name'][$i], true);
			$filepath = \creamy\CRMUtils::creamyBaseDirectoryPath().$relativeURL;
			if (move_uploaded_file($attachments[$attachmentTag]['tmp_name'][$i], $filepath)) { // successfully moved upload.
				// inbox attachment.
				$data = array(
					"message_id" => $inboxmsgid, 
					"folder_id" => MESSAGES_GET_INBOX_MESSAGES,
					"filepath" => $relativeURL,
					"filetype" => $attachments[$attachmentTag]['type'][$i], // I know, I know, I shouldn't trust this, but...
					"filesize" => $attachments[$attachmentTag]['size'][$i]
				);
				if (!$this->dbConnector->insert(CRM_ATTACHMENTS_TABLE_NAME, $data)) { return false; }
				
				// outbox attachment.
				$data["message_id"] = $outboxmsgid;
				$data["folder_id"] = MESSAGES_GET_SENT_MESSAGES;
				if (!$this->dbConnector->insert(CRM_ATTACHMENTS_TABLE_NAME, $data)) { return false; }				
			} else { return false; }
		}
		return true;
	}
	
	/**
	 * Returns the table name associated with a mail folder id.
	 * @param $folder the identifier of the mail folder.
	 * @return the table name associated with a mail folder id.
	 */
	private function getTableNameForFolder($folder) {
		$tableName = NULL;
		if ($folder == MESSAGES_GET_INBOX_MESSAGES) { // all inbox messages.
			$tableName = CRM_MESSAGES_INBOX_TABLE_NAME;
		} else if ($folder == MESSAGES_GET_UNREAD_MESSAGES) { // unread messages.
			$tableName = CRM_MESSAGES_INBOX_TABLE_NAME;
		} else if ($folder == MESSAGES_GET_DELETED_MESSAGES) { // deleted messages.
			$tableName = CRM_MESSAGES_JUNK_TABLE_NAME;
		} else if ($folder == MESSAGES_GET_SENT_MESSAGES) { // sent messages.
			$tableName = CRM_MESSAGES_OUTBOX_TABLE_NAME;
		} else if ($folder == MESSAGES_GET_FAVORITE_MESSAGES) { // favorite inbox messages
			$tableName = CRM_MESSAGES_INBOX_TABLE_NAME;
		} else if ($folder == CALLS_GET_INBOUND_CALLS) { // inbound call
			$tableName = CRM_RECORDINGLOGS_TABLE_NAME;
		} else if ($folder == CALLS_GET_OUTBOUND_CALLS) { // outbound calls
			$tableName = CRM_RECORDINGLOGS_TABLE_NAME;
		}
		return $tableName;
	}
	
	/**
	 * Returns the messages of the user
	 * @param Int $userid id of the user of the messages to retrieve
	 * @param Int $type type of messages to retrieve:
	 * - MESSAGES_GET_INBOX_MESSAGES (0): inbox messages 
	 * - MESSAGES_GET_UNREAD_MESSAGES (1): unread messages 
	 * - MESSAGES_GET_DELETED_MESSAGES (2): deleted messages  
	 * - MESSAGES_GET_SENT_MESSAGES (3): sent messages 	 
	 */
	public function getMessagesOfType($userid, $type) {
		// initial sanity checks
		if (!is_numeric($userid) || !is_numeric($type)) return NULL;
		
		// determine type of messages to get.
		$tableName = $this->getTableNameForFolder($type);
		if (empty($tableName)) { return NULL; }

		if ($type == MESSAGES_GET_INBOX_MESSAGES) { // all inbox messages.
			$this->dbConnector->where("m.user_to", $userid);
			$this->dbConnector->join(CRM_USERS_TABLE_NAME." u", "m.user_from = u.id", "LEFT");
		} else if ($type == MESSAGES_GET_DELETED_MESSAGES) {
			$this->dbConnector->join(CRM_USERS_TABLE_NAME." u", "m.user_from = u.id", "LEFT");
		} else if ($type == MESSAGES_GET_UNREAD_MESSAGES) { // unread messages.
			$this->dbConnector->where("m.user_to", $userid);
			$this->dbConnector->where("m.message_read", 0);
			$this->dbConnector->join(CRM_USERS_TABLE_NAME." u", "m.user_from = u.id", "LEFT");
		} else if ($type == MESSAGES_GET_SENT_MESSAGES) { // sent messages.
			$this->dbConnector->where("m.user_from", $userid);
			$this->dbConnector->join(CRM_USERS_TABLE_NAME." u", "m.user_to = u.id", "LEFT");
		} else if ($type == MESSAGES_GET_FAVORITE_MESSAGES) { // favorite inbox messages
			$this->dbConnector->where("m.user_to", $userid);
			$this->dbConnector->where("m.favorite", 1);
			$this->dbConnector->join(CRM_USERS_TABLE_NAME." u", "m.user_from = u.id", "LEFT");
		} else { return NULL; }
		
		return $this->dbConnector->get("$tableName m", null, "m.id, m.user_from, m.user_to, m.subject, m.message, m.date, m.message_read, m.favorite, u.id as remote_id, u.name as remote_user, u.avatar as remote_avatar");
	}
	
	/**
	 * Returns the messages of the user
	 * @param Int $userid id of the user of the messages to retrieve
	 * @param Int $type type of messages to retrieve:
	 * - CALLS_GET_INBOUND_CALLS (0): inbound calls
	 * - CALLS_GET_OUTBOUND_CALLS (1): outbound calls  
	 */
	public function getCallsOfType($userid, $type) {
		// initial sanity checks
		if (!is_numeric($userid) || !is_numeric($type)) return NULL;
		
		// determine type of messages to get.
		$tableName = $this->getTableNameForFolder($type);
		if (empty($tableName)) { return NULL; }

		if ($type == CALLS_GET_INBOUND_CALLS) { // all inbox messages.
			$this->dbConnectorAsterisk->where("rl.lead_id", $userid);
			$this->dbConnectorAsterisk->join(CRM_CALLLOGS_TABLE_NAME." cl", "rl.vicidial_id = cl.uniqueid", "LEFT");
		} else if ($type == CALLS_GET_OUTBOUND_CALLS) {
			$this->dbConnectorAsterisk->where("rl.lead_id", $userid);
			$this->dbConnectorAsterisk->join(CRM_CALLLOGS_TABLE_NAME." cl", "rl.vicidial_id = cl.uniqueid", "LEFT");
		} else { return NULL; }
		
		return $this->dbConnectorAsterisk->get("$tableName rl", null, "rl.recording_id, rl.length_in_sec, rl.filename, rl.location, rl.lead_id, rl.user");
	}
	
	/**
	 * Gets a specific message from one folder, taking into account the sender and receiver of the message.
	 */
	public function getSpecificMessage($userid, $messageid, $folder) {
		// sanity checks.
		$tableName = $this->getTableNameForFolder($folder);
		if ($tableName == NULL || $userid == NULL || $messageid == NULL) { return NULL; }

		// determine to/from of the message.
		$useridfield = "user_to";
		if ($folder == MESSAGES_GET_SENT_MESSAGES) {
			 $useridfield = "user_from";
		}
		
		$this->dbConnector->where("id", $messageid);
		return $this->dbConnector->getOne($tableName);
	}
	
	/**
	 * Returns the number of unread messages for a user.
	 * @param Int $userid id of the user to get the unread messages from.
	 */
	 public function getUnreadMessagesNumber($userid) {
		 if (empty($userid)) return 0;
		 // prepare query.
		 $this->dbConnector->where("user_to", $userid);
		 $this->dbConnector->where("message_read", "0");
		 return $this->dbConnector->getValue(CRM_MESSAGES_INBOX_TABLE_NAME, "count(*)");
	 }
	 
	/**
	 * Marks a set of messages as read.
	 * @param $userid Int the id of the user the messages belong to.
	 * @param $messageids Array a set of Int values containing the ids of the messages.
	 * @param $folder Int folder id the messages belong to.
	 * @return true if operation was successful, false otherwise.
	 */
	public function markMessagesAsRead($userid, $messageids, $folder) {
		// sanity checks
		if (!is_numeric($userid)) return false;
		if (!is_numeric($folder)) return false;
		$tableName = $this->getTableNameForFolder($folder);
		if ($tableName == NULL) return false;
		if (!$this->array_contains_only_numeric_values($messageids)) return false;
		
		$useridfield = "user_to";
		if ($folder == MESSAGES_GET_SENT_MESSAGES) $useridfield = "user_from";
		
		$this->dbConnector->where($useridfield, $userid);
		$this->dbConnector->where("id IN (".implode(',',$messageids).")");
		$data = array("message_read" => "1");
		return $this->dbConnector->update($tableName, $data);
	}
		 
	/**
	 * Marks a set messages as unread.
	 * @param $userid Int the id of the user the messages belong to.
	 * @param $messageids Array a set of Int values containing the ids of the messages.
	 * @param $folder Int folder id the messages belong to.
	 * @return true if operation was successful, false otherwise.
	 */
	public function markMessagesAsUnread($userid, $messageids, $folder) {
		// sanity checks
		if (!is_numeric($userid)) return false;
		if (!is_numeric($folder)) return false;
		$tableName = $this->getTableNameForFolder($folder);
		if ($tableName == NULL) return false;
		if (!$this->array_contains_only_numeric_values($messageids)) return false;

		$useridfield = "user_to";
		if ($folder == MESSAGES_GET_SENT_MESSAGES) $useridfield = "user_from";

		$this->dbConnector->where($useridfield, $userid);
		$this->dbConnector->where("id IN (".implode(',',$messageids).")");
		$data = array("message_read" => "0");
		return $this->dbConnector->update($tableName, $data);
	}

	/**
	 * Marks a set of messages as favorites or un-favorites.
	 * @param $userid Int the id of the user the messages belong to.
	 * @param $messageids Array a set of Int values containing the ids of the messages.
	 * @param $folder Int folder id the messages belong to.
	 * @return true if operation was successful, false otherwise.
	 */
	public function markMessagesAsFavorite($userid, $messageids, $folder, $favorite) {
		// sanity check
		if (!is_numeric($userid)) return false;
		if (!is_numeric($folder)) return false;
		$tableName = $this->getTableNameForFolder($folder);
		if ($tableName == NULL) return false;
		if ($favorite < 0 || $favorite > 1) return false;
		if (!$this->array_contains_only_numeric_values($messageids)) return false;

		$useridfield = "user_to";
		if ($folder == MESSAGES_GET_SENT_MESSAGES) $useridfield = "user_from";
		
		// return result of update 
		$this->dbConnector->where($useridfield, $userid);
		$this->dbConnector->where("id IN (".implode(',',$messageids).")");
		$data = array("favorite" => $favorite);
		return $this->dbConnector->update($tableName, $data);
	}

	/**
	 * Deletes a set of messages permanently
	 * @param $userid Int the id of the user the messages belong to.
	 * @param $messageids Array a set of Int values containing the ids of the messages.
	 * @param $folder Int folder id the messages belong to.
	 * @return true if operation was successful, false otherwise.
	 */
	public function deleteMessages($userid, $messageids, $folder) {
		// sanity check
		if (!is_numeric($userid)) return false;
		if (!is_numeric($folder)) return false;
		$tableName = $this->getTableNameForFolder($folder);
		if ($tableName == NULL) return false;
		if (!$this->array_contains_only_numeric_values($messageids)) return false;
		error_log("Deleting from folder $folder message ids: ".var_export($messageids, true));
		// determine required user id (depending on folder).
		$useridfield = "user_to";
		if ($folder == MESSAGES_GET_SENT_MESSAGES) $useridfield = "user_from";

		// delete attachaments first. We must check for orfan attachment files.
		if (!$this->deleteAttachmentsAndCheckForOrphanFiles($messageids, $folder)) { return false; }
		error_log("Deleted attachements");
		// now delete the message.
		$this->dbConnector->where($useridfield, $userid);
		$this->dbConnector->where("id IN (".implode(',',$messageids).")");
		return $this->dbConnector->delete($tableName);
	}

	/**
	 * This function iterates through a series of attachments (defined by messageid and folder)
	 * and deletes that attachment from the database. If the file referenced by this attachment
	 * doesn't exist anymore (orphan file) we delete it from disk.
	 * @param $messageids Array a set of Int values containing the ids of the messages.
	 * @param $folder Int folder id the messages belong to.
	 * @return true if operation was successful, false otherwise.
	 */ 
	protected function deleteAttachmentsAndCheckForOrphanFiles($messageids, $folder) {
		$basedir = \creamy\CRMUtils::creamyBaseDirectoryPath();
		foreach ($messageids as $messageid) { // iterate through all messages.
			$this->dbConnector->where("message_id", $messageid);
			$this->dbConnector->where("folder_id", $folder);
			error_log("Deleting attachements from message $messageid in folder $folder");			
			$attachments = $this->dbConnector->get(CRM_ATTACHMENTS_TABLE_NAME);
			if ($this->dbConnector->count > 0) { // do we have any attachments
				foreach ($attachments as $attachment) { // get id and delete the attachment
					$this->dbConnector->where("id", $attachment["id"]);
					if ($this->dbConnector->delete(CRM_ATTACHMENTS_TABLE_NAME)) { // success
						// Now check for orphan file. Try to look for other attachment referencing the same file.
						$this->dbConnector->where("filepath", $attachment["filepath"]);
						if (!$this->dbConnector->getOne(CRM_ATTACHMENTS_TABLE_NAME)) { // orphan file
							error_log("Removing file ".$basedir.$attachment["filepath"]);
							unlink($basedir.$attachment["filepath"]); // remove it
						}
					} else { return false; }
				}
			}
		}
		return true;
	}

	/**
	 * Moves a set of messages to the junk folder
	 * @param $userid Int the id of the user the messages belong to.
	 * @param $messageids Array a set of Int values containing the ids of the messages.
	 * @param $folder Int folder id the messages belong to.
	 * @return true if operation was successful, false otherwise.
	 */
	public function junkMessages($userid, $messageids, $folder) {
		// sanity check
		if (!is_numeric($userid)) return false;
		if (!is_numeric($folder)) return false;
		$tableName = $this->getTableNameForFolder($folder);
		if ($tableName == NULL) return false;
		if (!$this->array_contains_only_numeric_values($messageids)) return false;

		// initial values
		$messagesToJunk = count($messageids);
		$messagesJunked = 0;
		$useridfield = "user_to";
		if ($folder == MESSAGES_GET_SENT_MESSAGES) $useridfield = "user_from";
		
		foreach ($messageids as $messageid) {
			// get the data from the old messages box first
			$this->dbConnector->where($useridfield, $userid);
			$this->dbConnector->where("id", $messageid);
			$oldData = $this->dbConnector->getOne($tableName, array("user_from", "user_to", "subject", "message", "date", "message_read", "favorite"));
			if ($oldData) {
				// start a transaction so all operations happen or not atomically.
				$this->dbConnector->startTransaction();
				// add origin folder
				$oldData["origin_folder"] = $tableName;
				// insert old data in messages_junk
				$newJunkId = $this->dbConnector->insert(CRM_MESSAGES_JUNK_TABLE_NAME, $oldData);
				if ($newJunkId) { // try to delete old message.
					$this->dbConnector->where($useridfield, $userid);
					$this->dbConnector->where("id", $messageid);
					if ($deleteOriginal = $this->dbConnector->delete($tableName)) {
						// now move the attachement references.
						if ($this->adjustAttachementsFromMessage($messageid, $folder, $newJunkId, MESSAGES_GET_DELETED_MESSAGES)) {
							// commit and increment junked messages count.
							$this->dbConnector->commit();
							$messagesJunked = $messagesJunked + 1;						
						} else { $this->dbConnector->rollback(); }
					} else { $this->dbConnector->rollback(); }
				} else { $this->dbConnector->rollback(); }
			}
		}
		
		return $messagesJunked;
	}

	/**
	 * This function moves the references from the attachements of an origin message to a
	 * new message. This is useful when you are moving a message from one folder to the junk
	 * folder or viceversa.
	 * @param Int $originMsgId			Id of the message in the origin folder
	 * @param Int $originFolder			Id of the folder the original message was in.
	 * @param Int $destinationMsgId		Id of the message in the destination folder
	 * @param Int $destinationFolder	Id of the folder for the new message
	 * @return Bool true if the attachements were successfully moved, false otherwise.
	 */
	private function adjustAttachementsFromMessage($originMsgId, $originFolder, $destinationMsgId, $destinationFolder) {
		$this->dbConnector->where("message_id", $originMsgId);
		$this->dbConnector->where("folder_id", $originFolder);
		$data = array("message_id" => $destinationMsgId, "folder_id" => $destinationFolder);
		return $this->dbConnector->update(CRM_ATTACHMENTS_TABLE_NAME, $data);
	}

	/**
	 * Gets a set of messages out of the jumk folder back to their original folder.
	 * @param $userid Int the id of the user the messages belong to.
	 * @param $messageids Array a set of Int values containing the ids of the messages.
	 * @param $folder Int folder id the messages belong to.
	 * @return true if operation was successful, false otherwise.
	 */
	public function unjunkMessages($userid, $messageids) {
		// sanity check
		if (!is_numeric($userid)) return false;
		if (!$this->array_contains_only_numeric_values($messageids)) return false;

		// initial values
		$messagesToUnjunk = count($messageids);
		$messagesUnjunked = 0;
		$useridfield = "user_to";
		
		foreach ($messageids as $messageid) {
			$this->dbConnector->where("id", $messageid);
			$junkedObj = $this->dbConnector->getOne(CRM_MESSAGES_JUNK_TABLE_NAME, array("user_from", "user_to", "subject", "message", "date", "message_read", "favorite", "origin_folder"));
			if ($junkedObj) {
				$tableName = $junkedObj["origin_folder"];
				unset($junkedObj["origin_folder"]); // origin_folder doesn't exist in $tableName to insert, so we remove it.
				if (!empty($tableName)) {
					if ($this->dbConnector->insert($tableName, $junkedObj)) { // insert into origin_folder succeed!
						// now try to delete the message from the junk folder.
						$this->dbConnector->where("id", $messageid);
						if ($this->dbConnector->delete(CRM_MESSAGES_JUNK_TABLE_NAME)) { $messagesUnjunked = $messagesUnjunked + 1; }
					}
				}
			}
		}
		
		return $messagesUnjunked;	
	}
	
	/**
	 * Retrieves the attachments for a given message.
	 * @param Int $messageid 	identifier for the message.
	 * @param Int $folderid 	identifier for the folder.
	 * @return Array an array containing the attachments data.
	 */
	public function getMessageAttachments($messageid, $folderid) {
		$this->dbConnector->where("message_id", $messageid);
		$this->dbConnector->where("folder_id", $folderid);
		return $this->dbConnector->get(CRM_ATTACHMENTS_TABLE_NAME);
	}
	
	/** Events */
	
	/**
	 * Creates a new event associated to the user and to a customer, 
	 * without being assigned a specific date, for a "all day" duration.
	 * @param Int $userid			ID of the user this event will belong to.
	 * @param String $customerid	ID of the customer this event will be associated to.
	 * @param String $customertype	Customer type
	 * @param Bool $allDay			true if this event is programmed to last for the entire day.
	 * @param String $startDate		Start date expressed in mysql format.
	 * @param String $endDate		End date expressed in mysql format.
	 * @param String $alarm			Custom alarm for the event.
	 * @return Int The id of the newly created event, 0 if there was an error.
	 */
	public function createContactEventForCustomer($userid, $customerid, $customertype, $allDay = true, $startDate = null, $endDate = null, $alarm = null) {
		// Grab customer data.
		$customerData = $this->getDataForCustomer($customerid, $customertype);
		if (!isset($customerData)) { return false; }
		// build event parameters: title, url...
		$title = $this->lh->translationFor("contact_reminder_for")." ".$customerData["name"];
		if (!empty($customerData["mobile"])) { $title .= " (".$customerData["mobile"].")"; } // mobile preferred over phone.
		else if (!empty($customerData["phone"])) { $title .= " (".$customerData["phone"].")"; } // phone preferred over email.
		else if (!empty($customerData["email"])) { $title .= " (".$customerData["email"].")"; } // phone preferred over email.
		$url = "editcustomer.php?customerid=$customerid&customer_type=$customertype";
		$color = CRM_UI_COLOR_DEFAULT_HEX;
		return $this->createEvent($userid, $title, $color, $allDay, $startDate, $endDate, $url, $alarm);
	}
	
	/**
	 * Creates a new event associated to the user, without being assigned a specific date, for a "all day" duration.
	 * @param Int $userid		ID of the user this event will belong to.
	 * @param String $title		title of the event.
	 * @param String $color		Color for the event in the format #RRGGBB
	 * @param Bool $allDay		true if this event is programmed to last for the entire day.
	 * @param String $startDate	Start date expressed in mysql format.
	 * @param String $endDate	End date expressed in mysql format.
	 * @param String $url		URL associated with this event.
	 * @param String $alarm		Custom alarm for the event.
	 * @return Int The id of the newly created event, 0 if there was an error.
	 */
	public function createEvent($userid, $title, $color, $allDay = true, $startDate = null, $endDate = null, $url = null, $alarm = null) {
		$data = array(
			"user_id" => $userid,
			"title" => $title,
			"color" => $color,
			"all_day" => $allDay,
			"start_date" => $startDate,
			"end_date" => $endDate,
			"alarm" => $alarm,
			"url" => $url
		);
		error_log("Creando evento con datos: ".var_export($data, true));
		$id = $this->dbConnector->insert(CRM_EVENTS_TABLE_NAME, $data);
		if ($id) { return $id; } else return 0;
	}

	public function deleteEvent($eventid) {
		$this->dbConnector->where("id", $eventid);
		return $this->dbConnector->delete(CRM_EVENTS_TABLE_NAME);
	}
	//edit event
	public function editEventTitle($eventsid, $title, $userid) {
		if (empty($eventsid) || empty($title) || empty($userid)) return false;
		$this->dbConnector->where("id", $eventsid);
		$this->dbConnector->where("user_id", $userid);
		$data = array("title" => $title);
		return $this->dbConnector->update(CRM_EVENTS_TABLE_NAME, $data);
	}
	
	public function getUnassignedEventsForUser($userid) {
		$this->dbConnector->where("user_id", $userid);
		$this->dbConnector->where("start_date IS NULL");
		return $this->dbConnector->get(CRM_EVENTS_TABLE_NAME);
	}	
	
	//added for edit events
	public function editAssignedEventsForUser($userid) {
		$this->dbConnector->where("user_id", $userid);
		$this->dbConnector->where("start_date IS NOT NULL");
		return $this->dbConnector->get(CRM_EVENTS_TABLE_NAME);
	}
	
	public function getAssignedEventsForUser($userid) {
		$this->dbConnector->where("user_id", $userid);
		$this->dbConnector->where("start_date IS NOT NULL");
		return $this->dbConnector->get(CRM_EVENTS_TABLE_NAME);
	}
	
	public function modifyEvent($userid, $eventid, $startDate, $endDate, $allDay) {
		// calculate new values.
		$newStartDate = date("Y-m-d H:i:s", $startDate);
		$newEndDate = date("Y-m-d H:i:s", $endDate);
		$newAllDay = $allDay ? "1" : "0";
		$data = array("start_date" => $newStartDate, "end_date" => $newEndDate, "all_day" => $newAllDay);
		// perform update in ddbb.
		$this->dbConnector->where("user_id", $userid)->where("id", $eventid);
		return $this->dbConnector->update(CRM_EVENTS_TABLE_NAME, $data);
	}
	
	public function getEventsForToday($userid, $onlyUnnotifiedEvents = false) {
		// prepare query
		if (empty($userid)) return array();
		$whereClause = "((DATE(start_date) = CURDATE()) OR (DATE(end_date) = CURDATE())) AND (user_id = ?)";
		if ($onlyUnnotifiedEvents) $whereClause .= " AND (notification_sent = 0)";
		$this->dbConnector->where($whereClause, array($userid));
		$events = $this->dbConnector->get(CRM_EVENTS_TABLE_NAME);
		return isset($events) ? $events : array();
	}
	
	public function getEventsForTodayForAllUsers($onlyUnnotifiedEvents = false) {
		// prepare query
		$whereClause = "((DATE(start_date) = CURDATE()) OR (DATE(end_date) = CURDATE()))";
		if ($onlyUnnotifiedEvents) $whereClause .= " AND (notification_sent = 0)";
		$this->dbConnector->where($whereClause);
		$events = $this->dbConnector->get(CRM_EVENTS_TABLE_NAME);
		return isset($events) ? $events : array();
	}

	public function getNumberOfTodayEvents($userid) {
		// prepare query
		if (empty($userid)) return NULL;
		$this->dbConnector->where("((DATE(start_date) = CURDATE()) OR (DATE(end_date) = CURDATE())) AND (user_id = ?)", array($userid));
		return $this->dbConnector->getValue(CRM_EVENTS_TABLE_NAME, "count(*)");
	}

	public function setEventAsNotified($eventid) {
		if (!isset($eventid)) { return false; }
		$data = array("notification_sent" => 1);
		$this->dbConnector->where("id", $eventid);
		return $this->dbConnector->update(CRM_EVENTS_TABLE_NAME, $data);
	}

	/** Notifications */
	
	/**
	 * Gets the number of notifications for today for the user.
	 * @param $userid Int the identifier for the user.
	 * @return Int the number of notifications. 
	 */
	public function getNumberOfTodayNotifications($userid) {
		$this->dbConnector->where("DATE(date) = CURDATE() AND (target_user = 0 OR target_user = ?)", array($userid));
		return $this->dbConnector->getValue(CRM_NOTIFICATIONS_TABLE_NAME, "count(*)");
	}
	
	/**
	 * Gets the notifications for today for the user as an array.
	 * @param $userid Int the identifier for the user.
	 * @return Array the notifications as an associative array. 
	 */
	public function getTodayNotifications($userid) {
		// prepare query
		if (empty($userid)) return NULL;
		$this->dbConnector->where("DATE(date) = CURDATE() AND (target_user = 0 OR target_user = ?)", array($userid));
		return $this->dbConnector->get(CRM_NOTIFICATIONS_TABLE_NAME);
	}
	
	/**
	 * Get notifications for past week for the user.
	 * @param $userid Int the identifier for the user.
	 * @return Array the notifications as an associative array. 
	 */
	public function getNotificationsForPastWeek($userid) {
		// prepare query
		if (empty($userid)) return NULL;
		$this->dbConnector->where("(DATE(date) BETWEEN CURDATE() - INTERVAL 7 DAY AND CURDATE() - INTERVAL 1 DAY) AND (target_user = 0 OR target_user = ?)", array($userid));
		return $this->dbConnector->get(CRM_NOTIFICATIONS_TABLE_NAME);
	}

	/** Statistics */
	
	/**
	 * Inserts a new entry in the statistics table with the current number of customers in every table.
	 * @return boolean true if the operation was successful, false otherwise.
	 */
	
	public function generateStatisticsForToday() {
		// get customer tables
		$customerType = "vicidial_list";
		if (empty($customerTypes)) return true;

		// build the query by adding customer types
		//alex
		$currentTimestamp = time();
		$currentNow = date("Y-m-d H:i:s", $currentTimestamp);
		$data = array("timestamp" => $this->dbConnector->now());
		//foreach ($customerTypes as $customerType) {
			$numCustomers = $this->getNumberOfClientsFromTable($customerType["lead_id"]);
			$customerKey = $customerType["lead_id"];
			$data[$customerKey] = $numCustomers;
		//}
		
		
		return $this->dbConnector->insert(CRM_STATISTICS_TABLE_NAME, $data);
	}

	/**
	 * Gets the number of customers of a given customerType (= tablename).
	 * @param $tableName String the table of customers to get the count from.
	 * @return the number (count(*)) of entries in the given customer table.
	 */
	public function getNumberOfClientsFromTable($tableName) {
		if (empty($tableName)) return 0;
		$tableName = $this->escape_string($tableName);
		return $this->dbConnectorAsterisk->getValue($tableName, "count(*)");
	}

	/**
	 * Gets the number of new contacts (last week).
	 * @return the number of contact entries that were created in the last week.
	 */
	public function getNumberOfNewContacts() {
		$this->dbConnectorAsterisk->where("DATE(entry_date) BETWEEN CURDATE() - INTERVAL 7 DAY AND CURDATE()");
		return $this->dbConnectorAsterisk->getValue(CRM_CONTACTS_TABLE_NAME, "count(*)");
	}
	
	/**
	 * This function returns true if and only if we have some valid statistics to show to the user, i.e: we
	 * have a statistics line whose customer numbers are not set to zero (valid information about customers).
	 */
	public function weHaveSomeValidStatistics() {
		$stats = $this->dbConnector->get(CRM_STATISTICS_TABLE_NAME);
		if (empty($stats)) { return false; }
		$customerTypes = $this->getCustomerTypes();
		if (empty($customerTypes)) { return false; }
		foreach ($stats as $stat) {
			foreach ($customerTypes as $customerType) {
				error_log("Number of customers of type ".$customerType["table_name"].": ".intval($stat[$customerType["table_name"]]));
				if (intval($stat[$customerType["table_name"]]) > 0) { return true; }
			}
		}
		return false;
	}
	
	/**
	 * Gets the total number of customers in all customers tables.
	 */
	public function weHaveAtLeastOneCustomerOrContact() {
		$customerTypes = $this->getCustomerTypes();
		if (empty($customerTypes)) { return false; }
		foreach ($customerTypes as $customerType) {
			if ($this->getNumberOfClientsFromTable($customerType["table_name"]) > 0) { return true; }
		}		
		return false;
	}
	
	/**
	 * Gets the number of new customers (last week), not including contacts.
	 * @return the number of customer entries that were created in the last week from all customer tables but not including contacts.
	 */
	public function getNumberOfNewCustomers() {
		$customerTypes = $this->getCustomerTypes();
		if (empty($customerTypes)) return 0;
		
		$numClients = 0;
		foreach ($customerTypes as $customerType) {
			if ($customerType["table_name"] == CRM_CONTACTS_TABLE_NAME) continue;
			$this->dbConnectorAsterisk->where("DATE(entry_date) BETWEEN CURDATE() - INTERVAL 7 DAY AND CURDATE()");
			$numClients += $this->dbConnector->getValue($customerType["table_name"], "count(*)");
		}
		return $numClients;
	}
	
	/**
	 * Gets the last $limit (default 10) customer statistics.
	 * @param $limit Int (default = 10) the number of statistics to retrieve, in descending order, ordered by timestamp.
	 * 
	 */	
	public function getLastCustomerStatistics($limit = 6) {
        $this->dbConnector->orderBy("entry_date", "desc");
        $result = $this->dbConnector->get(CRM_STATISTICS_TABLE_NAME, $limit);
        if (isset($result)) { return array_reverse($result); }
        else return array();
   	}
	
	/** Modules */
	
	/**
	 * Retrieves the list of active modules.
	 * @return Array an array with the list of active modules.
	 */
	public function getActiveModules() {
		$this->dbConnector->where("setting", CRM_SETTING_ACTIVE_MODULES);
		$modulesRow = $this->dbConnector->getOne(CRM_SETTINGS_TABLE_NAME);
		if (is_string($modulesRow["value"]) && !empty($modulesRow["value"])) { return explode(",", $modulesRow["value"]); } 
		return array();
	}
	
	/**
	 * Sets the list of active modules.
	 * @param Array $modules an array containing the short names of the modules to enable.
	 * @return Bool true if successful, false otherwise.
	 */
	public function setActiveModules($modules) {
		if (is_array($modules)) {
			// generate module string.
			$modulesString = implode(",", $modules);
			$moduleData = array("value" => $modulesString);
			// update settings.
			$this->dbConnector->where("setting", CRM_SETTING_ACTIVE_MODULES);
			return $this->dbConnector->update(CRM_SETTINGS_TABLE_NAME, $moduleData);
		} else { return false; }
	}

	/**
	 * Modify the status (enabled/disabled) of a module.
	 * @param String $moduleName the name of the module to enable/disable.
	 * @param String/Bool $status 1/true if module should be enabled, 0/false otherwise.
	 * @return Bool true if active modules changed, false otherwise.
	 */
	public function changeModuleStatus($moduleName, $status) {
		$modules = $this->getActiveModules();
		$modulesChanged = false;
		// check status
		if ($status == "1" || $status == true) {
			if (!in_array($moduleName, $modules, true)) { $modules[] = $moduleName; $modulesChanged = true; }
		} else if ($status == "0" || $status == false) {
			if ( ($key = array_search($moduleName, $modules)) !== false) { unset($modules[$key]); $modulesChanged = true; } 
		}
		
		// change status and return success.
		if ($modulesChanged) {
			return $this->setActiveModules($modules);
		}
		return false;
	}
	
	/** Utility functions */
	
	/**
	 * Escapes a string for a safer inclusion in a MySQL statement. Please note that this method alone is not enough for preventing SQL injections.
	 * @param $string String the string to be escaped.
	 * @return String the string escaped with a call to mysqli::real_escape_string();
	 */
	public function escape_string($string) {
		return $this->dbConnector->escape($string);
	}
	
	/**
	 * Returns the number of affected/selected rows from the last query.
	 */
	public function rowCount() { return $this->dbConnector->getRowCount(); }
	
	/**
	 * Returns the number of rows that would have been returned from the last query if there was no limit clause.
	 * This number is useful for datatable pagination.
	 * This number is only set if the variable $countFilteredResults is set to true in get/getOne/rawQuery.
	 */
	public function unlimitedRowCount() { return $this->dbConnector->getUnlimitedRowCount(); }
	
	/**
	 * Checks if a given array only contains numeric values.
	 * @param $array ? (supposed to be an array) input parameter, to check if its an array with only numeric values.
	 * @return boolean true if and only if $array is an array which contains only numeric values (those whose call to is_numeric returns true).
	 */
	private function array_contains_only_numeric_values($array) {
		if (!is_array($array)) return false;
		foreach ($array as $element) {
			if (!is_numeric($element)) return false;
		}
		return true;
	}
    
    /**
     * Returns the user_group of the specified user_id
     * @param Number $user_id id to check in db
     * @return user_group value of specified user_id, false otherwise
     */
    public function getUserGroup($user_id) {
	    $this->dbConnectorAsterisk->where("user_id", $user_id);
	    $result = $this->dbConnectorAsterisk->getOne(CRM_USERS_TABLE_NAME_ASTERISK, 'user_group');
		$return = ($this->dbConnectorAsterisk->getRowCount() > 0) ? $result['user_group'] : false;
	    return $return;
    }
	
	/**
	 * Saves the data of the image of the specified user_id
	 * @param Number $user_id to save as image id
	 * @param String $type to save as image type
	 * @param Base64 $data to save as image data
	 * @return boolean true if success, false otherwise
	 */
	public function saveUserAvatar($user_id, $type, $data) {
		$this->dbConnector->where("user_id", $user_id);
		$this->dbConnector->getOne("go_avatars");
		$row_cnt = $this->dbConnector->getRowCount();
		if ($row_cnt > 0) {
			$this->dbConnector->where('user_id', $user_id);
			$result = $this->dbConnector->update('go_avatars', array('type' => $type, 'data' => $data));
		} else {
			$result = $this->dbConnector->insert('go_avatars', array('user_id' => $user_id, 'type' => $type, 'data' => $data));
		}
		$return = ($this->dbConnector->getRowCount() > 0) ? true : false;
		return $return;
	}
	
	/**
	 * Returns the data of the image of the specified user_id
	 * @param Number $user_id id to check in db
	 * @return type and data value of specified user_id, false otherwise
	 */
	public function getUserAvatar($user_id) {
		$this->dbConnector->where("user_id", $user_id);
		$result = $this->dbConnector->getOne("go_avatars", 'type,data');
		$return = ($this->dbConnector->getRowCount() > 0) ? $result : false;
		return $return;
	}
}

?>
