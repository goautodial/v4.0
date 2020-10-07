<?php
/**
 * @file        module.php
 * @brief       WhatsApp Module
 * @copyright   Copyright (c) 2020 GOautodial Inc.
 * @author		Thom Bernarth Patacsil 
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

class WhatsApp extends Module {

	// module meta-data (ModuleData interface implementation).
	
	static function getModuleName() { return "WhatsApp "; }
	
	static function getModuleVersion() { return "1.0"; }
	
	static function getModuleDescription() { return "A simple module for chatting using WhatsApp."; }

	// lifecycle and respond to interactions.

	public function uponInit() {
		error_log("Module \"WhatsApp\" initializing...");
		
		// add the WhatsApp translation files to our language handler.
		$customLanguageFile = $this->getModuleLanguageFileForLocale($this->lh()->getLanguageHandlerLocale());
		if (!isset($customLanguageFile)) { $customLanguageFile = $this->getModuleLanguageFileForLocale(CRM_LANGUAGE_DEFAULT_LOCALE); }
		$this->lh()->addCustomTranslationsFromFile($customLanguageFile);
	}
		
	public function uponActivation() {
		error_log("Module \"WhatsApp\" activating...");
	}
		
	public function uponDeactivation() {
		error_log("Module \"WhatsApp\" deactivating...");
	}

	public function uponUninstall() {
		error_log("Module \"WhatsApp\" uninstalling...");
	}
	
	// Private functions for this module.
		
	// views and code generation

	/** We return true here to indicate that we want access to the database */
	public function needsDatabaseFunctionality() { return true; }

	public function databaseTableFields() {
		return array(
			"GO_whatsapp_user" => "VARCHAR(255) NOT NULL",
			"GO_whatsapp_token" => "VARCHAR(25) NOT NULL",
			"GO_whatsapp_instance" => "VARCHAR(25) NOT NULL",
			"GO_whatsapp_host" => "VARCHAR(25) NOT NULL",
			"GO_whatsapp_callback_url" => "VARCHAR(25) NOT NULL"
		);
	}

	public function needsSidebarDisplay() { return false; }

	public function mainPageViewContent($args) {
		return $this->sectionWithRandomQuotes($number);
	}

	public function mainPageViewTitle() {
		return $this->lh()->translationFor("whatsapp");
	}
	
	public function mainPageViewSubtitle() { 
		return $this->lh()->translationFor("a_simple_creamy_module"); 
	}
	
	public function mainPageViewIcon() {
		return "quote-left";
	}
	
	// hooks
	
	/*public function dashboardHook($wantsFullRow = true) {
		return $this->sectionWithRandomQuotes(1);
	}*/

	public function setDataForWhatsApp() {
		$success = false;
		
		$whatsapp_user = $this->valueForModuleSetting("GO_whatsapp_user");
		$whatsapp_token = $this->valueForModuleSetting("GO_whatsapp_token");
		$whatsapp_instance = $this->valueForModuleSetting("GO_whatsapp_instance");
		$whatsapp_host = $this->valueForModuleSetting("GO_whatsapp_host");
		$whatsapp_callback_url = $this->valueForModuleSetting("GO_whatsapp_callback_url");
		
		if (isset($whatsapp_user) && isset($whatsapp_token) && isset($whatsapp_instance) && isset($whatsapp_host)) {
			// try to update current value
			$data = array("GO_whatsapp_user" => $whatsapp_user, "GO_whatsapp_token" => $whatsapp_token, "GO_whatsapp_instance" => $whatsapp_instance, "GO_whatsapp_callback_url" => $whatsapp_callback_url );
			$success = $this->db()->update($this->databaseTableName(), $data);
		} else {
			$data = array("GO_whatsapp_user" => $whatsapp_user, "GO_whatsapp_token" => $whatsapp_token, "GO_whatsapp_instance" => $whatsapp_instance, "GO_whatsapp_callback_url" => $whatsapp_callback_url );
			$success = $this->db()->insert($this->databaseTableName(), $data);
		}		
		
		return $success ? CRM_DEFAULT_SUCCESS_RESPONSE : $this->lh()->translationFor("unable_set_quote");
	}	
	
	// settings
	
	public function moduleSettings() {
		return array(
			"GO_whatsapp_user" => CRM_SETTING_TYPE_STRING, 
			"GO_whatsapp_token" => CRM_SETTING_TYPE_STRING,
			"GO_whatsapp_instance" => CRM_SETTING_TYPE_STRING,
			"GO_whatsapp_host" => CRM_SETTING_TYPE_STRING,
			"GO_whatsapp_callback_url" => CRM_SETTING_TYPE_STRING
		); 
	}
	
}

?>

