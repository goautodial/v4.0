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
require_once(CRM_MODULE_INCLUDE_DIRECTORY.'goCRMAPISettings.php');

class osTicket extends Module {
	protected $userrole;

	// module meta-data (ModuleData interface implementation).
	
	static function getModuleName() { return "osTicket"; }
	
	static function getModuleVersion() { return "1.0"; }
	
	static function getModuleDescription() { return "A simple module that enables osTicket integration."; }

	// lifecycle and respond to interactions.

	public function uponInit() {
		error_log("Module \"osTicket\" initializing...");
		
		// add the osTicket translation files to our language handler.
		$customLanguageFile = $this->getModuleLanguageFileForLocale($this->lh()->getLanguageHandlerLocale());
		if (!isset($customLanguageFile)) { $customLanguageFile = $this->getModuleLanguageFileForLocale(CRM_LANGUAGE_DEFAULT_LOCALE); }
		$this->lh()->addCustomTranslationsFromFile($customLanguageFile);
		
		$this->userrole = \creamy\CreamyUser::currentUser()->getUserRole();
		
		if (isset($_SESSION['phone_this']) && $this->userrole > 1) {
			echo $this->goLoginToOsTicket($_SESSION['user'], $_SESSION['phone_this'], $this->userrole);
		}
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
	
	private function goLoginToOsTicket($user, $pass, $role) {
		$content = "";
		$osticket_url = $this->valueForModuleSetting("osticket_url");
		$token = $this->valueForModuleSetting("osticket_api_key");
		$server_ip = $_SERVER['SERVER_ADDR'];
		
		if ($role > 1) {
			$content  = <<<EOF
		<script>
					//Logging in on osTicket
					$(function() {
						$("#osTicketContent").attr('src', '{$osticket_url}gologin.php?username={$user}&passwd={$pass}&token={$token}&from_ip={$server_ip}');
						
						$("#osticket_tab").on('click', function() {
							$("#osTicketContent").attr('src', '{$osticket_url}');
						});
						
						$("#btnLogMeOut").on('click', function() {
							$("#agent_tablist li").first().addClass('active');
							$("#agent_tabs div[id='contact_info']").first().addClass('active');
							$("#osticket_tab").removeClass('active');
							$("#osticket").removeClass('active');
						});
					});
					</script>
					
EOF;
		} else {
			$content  = <<<EOF
		<script>
					//Logging in on osTicket
					$(function() {
						window.open("{$osticket_url}gologin.php?username={$user}&passwd={$pass}&token={$token}", "osTicket");
					});
					</script>
					
EOF;
		}
		
		return $content;
	}
	
	// views and code generation

	/** We return true here to indicate that we want access to the database */
	public function needsDatabaseFunctionality() { return true; }

	public function databaseTableFields() {
		return array(
			"osticket_url" => "VARCHAR(255) NOT NULL",
			"osticket_api_key" => "TEXT NULL"
		);
	}

	public function needsSidebarDisplay() { return false; }

	public function mainPageViewContent($args) {
		return false;
	}

	public function mainPageViewTitle() {
		return $this->lh()->translationFor("osticket");
	}
	
	public function mainPageViewSubtitle() { 
		return $this->lh()->translationFor("a_simple_module"); 
	}
	
	public function mainPageViewIcon() {
		return "ticket";
	}
	
	public function logoutFromOsTicket() {
		$content = "";
		$osticket_url = $this->valueForModuleSetting("osticket_url");
		
		$content  = <<<EOF
					//Logging in on osTicket
					$.get('{$osticket_url}gologout.php');
					
EOF;
		return $content;
	}
	
	// hooks
	
	public function dashboardHook($wantsFullRow = true) {
		$content = '';
		if (isset($_SESSION['phone_this']) && $this->userrole < 2) {
			$content = $this->goLoginToOsTicket($_SESSION['user'], $_SESSION['phone_this'], $this->userrole);
		}
		return $content;
	}
	
	// settings
	
	public function moduleSettings() {
		return array("osticket_url" => CRM_SETTING_TYPE_STRING, "osticket_api_key" => CRM_SETTING_TYPE_STRING); 
	}
	
}

?>
