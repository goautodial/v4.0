<?php
/**
 * @file        module.php
 * @brief       osTicket Module
 * @copyright   Copyright (c) 2018 GOautodial Inc.
 * @author      Christopher Lomuntad
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

class OsTicket extends Module {

	// module meta-data (ModuleData interface implementation).
	
	static function getModuleName() { return "osTicket "; }
	
	static function getModuleVersion() { return "1.0"; }
	
	static function getModuleDescription() { return "A simple module that enables osTicket integration."; }

	// lifecycle and respond to interactions.

	public function uponInit() {
		error_log("Module \"osTicket\" initializing...");
		
		// add the Sippy Softswitch translation files to our language handler.
		$customLanguageFile = $this->getModuleLanguageFileForLocale($this->lh()->getLanguageHandlerLocale());
		if (!isset($customLanguageFile)) { $customLanguageFile = $this->getModuleLanguageFileForLocale(CRM_LANGUAGE_DEFAULT_LOCALE); }
		$this->lh()->addCustomTranslationsFromFile($customLanguageFile);
	}
		
	public function uponActivation() {
		error_log("Module \"osTicket\" activating...");
	}
		
	public function uponDeactivation() {
		error_log("Module \"osTicket\" deactivating...");
	}

	public function uponUninstall() {
		error_log("Module \"osTicket\" uninstalling...");
	}
	
	// Private functions for this module.
	
	private function sectionWithRandomQuotes($number) {
		$content = "";
		
		$sippy_api_url = $this->valueForModuleSetting("sippy_api_url");
		$sippy_username = $this->valueForModuleSetting("sippy_username");
		
		$postfields = array(
			'username' => $sippy_username,
		);
		
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $sippy_api_url);
		//curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_TIMEOUT, 3);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postfields));
		
		$output = curl_exec($ch);
		$output = round($output, 2);
		
		$sippy_balance = json_encode($output);		
		
		$quoteBox = $this->ui()->boxWithQuote($this->lh()->translationFor("justgovoip_balance"), ($this->lh()->translationFor("remaining_balance")).": $".number_format($sippy_balance), '');
		$content .= $this->ui()->fullRowWithContent($quoteBox);		
		
		return $content;
	}
	
	// views and code generation

	/** We return true here to indicate that we want access to the database */
	public function needsDatabaseFunctionality() { return true; }

	public function databaseTableFields() {
		return array(
			"osticket_api_url" => "VARCHAR(255) NOT NULL",
			"osticket_api_key" => "TEXT NULL"
		);
	}

	public function needsSidebarDisplay() { return false; }

	public function mainPageViewContent($args) {
		return $this->sectionWithRandomQuotes($number);
	}

	public function mainPageViewTitle() {
		return $this->lh()->translationFor("osticket");
	}
	
	public function mainPageViewSubtitle() { 
		return $this->lh()->translationFor("a_simple_module"); 
	}
	
	public function mainPageViewIcon() {
		return "ticket-alt";
	}
	
	// hooks
	
	public function dashboardHook($wantsFullRow = true) {
		return $this->sectionWithRandomQuotes(1);
	}
	
	// settings
	
	public function moduleSettings() {
		return array("osticket_api_url" => CRM_SETTING_TYPE_STRING, "osticket_api_key" => CRM_SETTING_TYPE_STRING); 
	}
	
}

?>
