<?php
namespace creamy;

require_once(CRM_MODULE_INCLUDE_DIRECTORY.'Module.php');
require_once(CRM_MODULE_INCLUDE_DIRECTORY.'CRMDefaults.php');
require_once(CRM_MODULE_INCLUDE_DIRECTORY.'LanguageHandler.php');
include(CRM_MODULE_INCLUDE_DIRECTORY.'Session.php');

$creamURL = \creamy\CRMUtils::creamyBaseURL();
$creamURL = parse_url($creamURL);
$baseURL = (!empty($_SERVER['HTTPS'])) ? "https://".$_SERVER['SERVER_NAME'] : "http://".$_SERVER['SERVER_NAME'];
define(__NAMESPACE__ . '\GO_MODULE_DIR', $baseURL.'/'.$creamURL['path'].'/modules'.DIRECTORY_SEPARATOR.'GOagent'.DIRECTORY_SEPARATOR);

/**
 * This module is an example of how to write a module for Creamy.
 * It will show a message of the day (message of the day).
 */
class GOagent extends Module {
	protected $userrole;
	protected $is_logged_in;

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

		$this->userrole = \creamy\CreamyUser::currentUser()->getUserRole();

		if ($this->userrole > 1) {
			$_SESSION['is_logged_in'] = $this->checkIfLoggedOnPhone();

			echo $this->getGOagentContent();
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
		$selectAll = $this->lh()->translationFor("select_all");
		$submit = $this->lh()->translationFor("submit");
		$labels = $this->getLabels();
		$goModuleDIR = GO_MODULE_DIR;
		$userrole = $this->userrole;
		$str = <<<EOF
		<link type='text/css' rel='stylesheet' href='{$goModuleDIR}css/style.css'></link>
					<script type='text/javascript' src='{$goModuleDIR}GOagentJS.php'></script>
					<div id="dialog-custinfo" class="modal fade" tabindex="-1">
						<div class="modal-dialog">
							<div class="modal-content">
								<div class="modal-header">
									<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
									<h4>$custInfoTitle</h4>
								</div>
								<div class="modal-body">
									<form id="formMain" class="form-horizontal">
										<div class="list-group">
										</div>
									</form>
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
									&nbsp;
								</div>
								<div class="modal-footer">
									<button id="scButton" class="btn btn-link bold hidden">$selectAll</button>
									<button id="scSubmit" class="btn btn-warning disabled"><span class="fa fa-check-square-o" aria-hidden="true"></span> $submit</button>
								</div>
							</div>
						</div>
					</div>
					
EOF;
		return $str;
	}
	
	// hooks
	private function getLabels() {
		$result = $this->db()->getOne('system_settings', 'label_title,label_first_name,label_middle_initial,label_last_name,label_address1,label_address2,label_address3,label_city,label_state,label_province,label_postal_code,label_vendor_lead_code,label_gender,label_phone_number,label_phone_code,label_alt_phone,label_security_phrase,label_email,label_comments');
		return $result;
	}
	
	// settings
	public function moduleSettings() {
		return array("GO_agent_url" => CRM_SETTING_TYPE_STRING, "GO_agent_url_info" => CRM_SETTING_TYPE_LABEL, "GO_agent_db" => CRM_SETTING_TYPE_STRING, "GO_agent_user" => CRM_SETTING_TYPE_STRING, "GO_agent_pass" => CRM_SETTING_TYPE_PASS, "GO_agent_db_info" => CRM_SETTING_TYPE_LABEL);
	}
}

?>