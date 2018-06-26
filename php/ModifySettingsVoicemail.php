<?php
/**
 * @file        ModifySettingsVoicemail.php
 * @brief       
 * @copyright   Copyright (c) 2018 GOautodial Inc.
 * @author		Demian Lizandro A, Biscocho
 * @author      Alexander Jim H. Abenoja
 * @author		Jerico James F. Milo
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

require_once('CRMDefaults.php');
require_once('goCRMAPISettings.php');

// check required fields
$reason = "Unable to Modify Voicemail";

$validated = 1;
if (!isset($_POST["modifyid"])) {
	$validated = 0;
}

if ($validated == 1) {
    
	// collect new user data.	
	$modifyid = $_POST["modifyid"];
    
	$pass = NULL; if (isset($_POST["password"])) { 
		$pass = $_POST["password"]; 
		$pass = stripslashes($pass);
	}
	
    $fullname = NULL; if (isset($_POST["fullname"])) { 
		$fullname = $_POST["fullname"];
		$fullname = stripslashes($fullname);
	}

    $email = NULL; if (isset($_POST["email"])) { 
		$email = $_POST["email"]; 
		$email = stripslashes($email);
	}
	
    $active = NULL; if (isset($_POST["active"])) { 
		$active = $_POST["active"]; 
		$active = stripslashes($active);
	}
	
    $delete_vm_after_email = NULL; if (isset($_POST["delete_vm_after_email"])) { 
		$delete_vm_after_email = $_POST["delete_vm_after_email"]; 
		$delete_vm_after_email = stripslashes($delete_vm_after_email);
	}  
    
	$url = gourl."/goVoicemails/goAPI.php"; #URL to GoAutoDial API. (required)
	$postfields = array(
		'goUser' => goUser,
		'goPass' => goPass,
		'goAction' => 'goEditVoicemail',		
		'responsetype' => responsetype,
		'voicemail_id' => $modifyid,
		'pass' => $pass,
		'fullname' => $fullname,
		'email' => $email,
		'active' => $active,
		'delete_vm_after_email' => $delete_vm_after_email,
		'session_user' => $_POST['log_user'],
		'log_ip' => $_SERVER['REMOTE_ADDR']
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

    if ($output->result=="success") {
    # Result was OK!
        echo 1;
    } else {
    # An error occured
		echo $output->result;
        //$lh->translateText("unable_modify_list");
    }
    
} else { ob_clean(); print $reason; }
?>
