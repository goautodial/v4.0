<?php
/**
 * @file        ModifyWhatsappSetting.php
 * @brief       Modify Whatsapp Setting
 * @copyright   Copyright (c) 2018 GOautodial Inc.
 * @author      Alexander Jim Abenoja
 * @author		Demian Lizandro A, Biscocho 
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

require_once('APIHandler.php');

$api = \creamy\APIHandler::getInstance();

	$GO_whatsapp_user = NULL; if (isset($_POST["GO_whatsapp_user"])) { 
		$GO_whatsapp_user = $_POST["GO_whatsapp_user"]; 
		$GO_whatsapp_user = stripslashes($GO_whatsapp_user);
	}

	$GO_whatsapp_instance = NULL; if (isset($_POST["GO_whatsapp_instance"])) { 
		$GO_whatsapp_instance = $_POST["GO_whatsapp_instance"]; 
		$GO_whatsapp_instance = stripslashes($GO_whatsapp_instance);
	}

	$GO_whatsapp_token = NULL; if (isset($_POST["GO_whatsapp_token"])) { 
		$GO_whatsapp_token = $_POST["GO_whatsapp_token"]; 
		$GO_whatsapp_token = stripslashes($GO_whatsapp_token);
	}

	$GO_whatsapp_host = NULL; if (isset($_POST["GO_whatsapp_host"])) { 
		$GO_whatsapp_host = $_POST["GO_whatsapp_host"]; 
		$GO_whatsapp_host = stripslashes($GO_whatsapp_host);
	}

	$GO_whatsapp_callback_url = NULL; if (isset($_POST["GO_whatsapp_callback_url"])) { 
		$GO_whatsapp_callback_url = $_POST["GO_whatsapp_callback_url"]; 
		$GO_whatsapp_callback_url = stripslashes($GO_whatsapp_callback_url);
	}
	
	$postfields = array(
		'GO_whatsapp_user' => $GO_whatsapp_user,
		'GO_whatsapp_token' => $GO_whatsapp_token,
		'GO_whatsapp_host' => $GO_whatsapp_host,
		'GO_whatsapp_instance' => $GO_whatsapp_instance,
		'GO_whatsapp_callback_url' => $GO_whatsapp_callback_url
	);
	
	$output = $api->API_editWhatsappSetting($postfields);

	if ($output->result=="success") {
	   # Result was OK!
		echo $output->result;
	 } else {
	   # An error occured
		echo $output->result;
	}
?>
