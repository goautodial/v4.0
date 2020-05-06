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

// dependencies.
require_once('CRMDefaults.php');
require_once('CRMUtils.php');
require_once('Config.php');
require_once('UIHandler.php');
require_once('DatabaseConnectorFactory.php');

// constants.
define('CRM_MODULE_INCLUDE_DIRECTORY', \creamy\CRMUtils::creamyBaseDirectoryPath().'php'.DIRECTORY_SEPARATOR);
define('CRM_MODULE_MAX_TABLENAME_LENGTH', 63);
define('CRM_MODULE_CUSTOM_ACTION_PAGE', 'ModuleCustomAction.php');
define('CRM_MODULE_PHP_DIRECTORY_NAME', 'php');
define('CRM_MODULE_LANGUAGE_DIRECTORY_NAME', 'lang');
define('CRM_MODULE_ASSETS_DIRECTORY_NAME', 'assets');
define('CRM_MODULE_TEMPLATE_TAG_TITLE', '{title}');
define('CRM_MODULE_TEMPLATE_TAG_SUBTITLE', '{subtitle}');
define('CRM_MODULE_TEMPLATE_TAG_ICON', '{icon}');
define('CRM_MODULE_TEMPLATE_TAG_CONTENT', '{content}');


/**
 * Module.php
 * 
 * This class represents a Creamy module. It's the base class for all modules.
 * This class is abstract and should never be instantiated. Instead, a subclass 
 * module must be created following the module creation guidelines: 
 * @author Ignacio Nieto Carvajal
 * @link URL http://creamycrm.com
 */
abstract class Module implements ModuleMetadata {
	// identification variables
	protected $moduleName = null; 		// FQDN of the module. I.E: com.creamycrm.module.HelloWorld
	protected $moduleVersion = null;	// Numeric string with the version number. I.E: "1.32".
	
	// private db connectors and data.
	private   $dbTableName;				// Name of the database table assigned to the module.
	protected $dbConnector; 			// a DB Connector to use for database methods.
	protected $dbConnectorType;			// The type of DB Connector used to access the database.
	// utility classes for modules.
	protected $uiHandler; 				// User Interface Handler object.
	protected $languageHandler;			// Language handler object.
	
	/** lifecycle. */
	
	/**
	 * The construct method of the module.
	 */
	final function __construct($dbConnectorType = CRM_DB_CONNECTOR_TYPE_MYSQL) {
		// set module name and module version.
		$moduleName = $this->getModuleName();
		$moduleVersion = $this->getModuleVersion();
		$this->moduleName = trim($moduleName);
		$this->moduleVersion = trim($moduleVersion);
		
		// database
		$this->dbConnectorType = $dbConnectorType;
		if ($this->needsDatabaseFunctionality() || $this->moduleSettings() != null) {
			$this->dbTableName = $this->databaseTableName();
			$this->dbConnector = \creamy\DatabaseConnectorFactory::getInstance()->getDatabaseConnectorOfType($dbConnectorType);
			if ($this->dbConnector == null) { throw new \Exception("Unable to initialize database connector for $moduleName. Module creation failed."); }
		}

		// Language handler
		$this->languageHandler = \creamy\LanguageHandler::getInstance();
	
		// UI
	    $this->uiHandler = \creamy\UIHandler::getInstance();

		// module custom initialization function.
		$this->uponInit();
	}
	
	/**
	 * Convenience method that returns the Database Connector to be used by module's subclasses.
	 * @return the module instance of DbConnector.
	 */
	public function db($dbConnectorType = CRM_DB_CONNECTOR_TYPE_MYSQL) {
		if (!isset($this->dbConnector)) {
			$this->dbConnector = \creamy\DatabaseConnectorFactory::getInstance()->getDatabaseConnectorOfType($dbConnectorType);
		}
		return $this->dbConnector;
	}
	
	/**
	 * Convenience method that returns the Language handler associated with the module. If it has not been
	 * created before, a new instance is created and returned.
	 * @return an instance of LanguageHandler.
	 */
	public function lh() {
		if (!isset($this->languageHandler)) { $this->languageHandler = \creamy\LanguageHandler::getInstance(); }
		return $this->languageHandler;
	}
		
	/**
	 * Convenience method that returns the Language handler associated with the module. If it has not been
	 * created before, a new instance is created and returned.
	 * @return an instance of LanguageHandler.
	 */
	public function ui() {
		if (!isset($this->uiHandler)) { $this->uiHandler = \creamy\UIHandler::getInstance(); }
		return $this->uiHandler;
	}
	
	/** Custom code for lifeycle, reacting to changes in module status */
	
	/**
	 * This method allows the module to initialize its properties and values right after the
	 * instance has been created, before the __construct method ends. A module should use this
	 * function to put any code that must be executed upon creation/initialization of the module.
	 * For more information visit the module creation guidelines: 
	 */
	public function uponInit() {
		// do nothing by default.
	}
		
	/** 
	 * This method activates the module. It will create the needed database (if appliable) and call
	 * uponActivation so modules can apply their custom code upon activation.
	 */
	public final function activateModule() {
		// create database (if appliable)
		if ($this->needsDatabaseFunctionality()) {
			$tableFields = $this->databaseTableFields();
			$tableName = $this->databaseTableName();
			if (is_array($tableFields)) {
				$this->dbConnector->createTable($tableName, $tableFields, null);
			} else { // dummy table
				$this->dbConnector->createTable($tableName, array(), null);
			}
		}

		// custom activation code
		$this->uponActivation();
	}

	/**
	 * This method gets called when the module is about to be activated, so it can initialize data structures
	 * or enable certain resources. A module should use this function to put any code that must be executed
	 * upon activation of the module.
	 * For more information visit the module creation guidelines: 
	 */
	public function uponActivation() {
		// do nothing by default.
	}
		
	/** 
	 * This method deactivates the module. It will release any needed resources and call
	 * uponDeactivation so modules can apply their custom code upon activation.
	 */
	public final function deactivateModule() {
		// custom deactivation code.
		$this->uponDeactivation();
	}
	
	
	/**
	 * This method gets called when the module is about to be deactivated, so it can release any data 
	 * structures or resources. A module should use this function to put any code that must be executed
	 * upon deactivation of the module.
	 * For more information visit the module creation guidelines: 
	 */
	public function uponDeactivation() {
		// do nothing by default.
	}
	
	
	/** 
	 * This method will uninstall the module. It will delete any created database (if appliable) and call
	 * uponUninstall so modules can apply their custom code upon activation.
	 */
	public final function uninstallModule() {
		// remove database (if appliable)
		if ($this->needsDatabaseFunctionality()) {
			$this->dbConnector->dropTable($this->databaseTableName(), false);
		}
		
		// custom uninstall code.
		$this->uponUninstall();
	}

	/**
	 * This method gets called when the module is about to be uninstalled, so it can delete and clean any
	 * data and resources created in the database. A module should use this function to put any code that 
	 * must be executed upon uninstall of the module.
	 * For more information visit the module creation guidelines: 
	 */
	public function uponUninstall() {
		// do nothing by default.
	}
	
	// module paths and directories

	/** Gets the base directory of this module */
	protected function getModuleDirectory() {
		$rc = new \ReflectionClass(get_class($this));
		return dirname($rc->getFileName());
	}
	
	/** Gets the base URL for the module */
	protected function getModuleBaseURL() {
		$baseURL = \creamy\CRMUtils::creamyBaseURL();
		return $baseURL."/".CRM_MODULES_BASEDIR."/".$this->getModuleShortName();
	}
	
	/** Gets the module short name, equivalent to the directory name */
	public function getModuleShortName() {
		return basename($this->getModuleDirectory());
	}	
	
	/** Returns the language directory, where custom localization files can be found */	
	public function getLanguageDirectory() { return $this->getModuleDirectory().DIRECTORY_SEPARATOR.CRM_MODULE_LANGUAGE_DIRECTORY_NAME; }
	
	/** 
	 * Gets the module custom language file for the given locale.
	 * @return the file containing the custom translations for the given locale if it exists, null otherwise.
	 */
	public function getModuleLanguageFileForLocale($locale) {
		$filepath = $this->getLanguageDirectory().DIRECTORY_SEPARATOR.$locale;
		if (file_exists($filepath)) { return $filepath; }
		else { return null; }
	}
	
	/** Returns the assets directory, where resources, images and other files can be found. */	
	public function getAssetsDirectory($includeFinalPathSeparator = true) { 
		$finalCode = $includeFinalPathSeparator ? DIRECTORY_SEPARATOR : "";
		return $this->getModuleDirectory().DIRECTORY_SEPARATOR.CRM_MODULE_ASSETS_DIRECTORY_NAME.$finalCode; 
	}
	
	/** Returns the assets relative URL, where resources, images and other files can be found. */	
	public function getAssetsRelativeURL($includeFinalPathSeparator = true) { 
		$finalCode = $includeFinalPathSeparator ? "/" : "";
		return $this->getModuleBaseURL()."/".CRM_MODULE_ASSETS_DIRECTORY_NAME.$finalCode; 
	}

	/** Returns the php directory, where custom php pages can be found. */
	public function getPHPDirectory($includeFinalPathSeparator = true) { 
		$finalCode = $includeFinalPathSeparator ? DIRECTORY_SEPARATOR : "";
		return $this->getModuleDirectory().DIRECTORY_SEPARATOR.CRM_MODULE_PHP_DIRECTORY_NAME.$finalCode; 
	}
	
	/** Returns the php relative URL, where custom php pages can be found. */	
	public function getPHPRelativeURL($includeFinalPathSeparator = true) { 
		$finalCode = $includeFinalPathSeparator ? "/" : "";
		return $this->getModuleBaseURL()."/".CRM_MODULE_PHP_DIRECTORY_NAME.$finalCode; 
	}

	/** 
	 * Returns the custom action page for a module. This page is used by the module to perform custom
	 * actions, like adjusting database records, CRUD operations and such. This module page will call 
	 * the module custom hook contained in the parameter hook_name, with the parameters received in
	 * $_POST. For more 
	 */	
	public function customActionModulePageURL() {
		$baseURL = \creamy\CRMUtils::creamyBaseURL();
		return $baseURL."/php/".CRM_MODULE_CUSTOM_ACTION_PAGE;
	}

	// Views
	
	/** 
	 * Returns true if the module's main content view that can be accessed by means of the sidebar, 
	 * The default value is false, modules must override this function to return true if they want to be displayed in the sidebar. 
	 * For more information visit the module creation guidelines: 
	 * @return true if the module needs to set a sidebar access for its main content view, false otherwise.
	 */
	public function needsSidebarDisplay() { return false; }
	
	/**
	 * Returns the content to be included in the main page. Every module must
	 * implement this function to add the content to be shown. In order to build
	 * the basic elements of the page, the modules can refer to function of
	 * the UIHandler shared instance (UIHandler::getInstance()). 
	 * For more information visit the module creation guidelines: 
	 * @param Array $args an associative array containing the arguments.	 
	 * @return String the code for the content of the main page.
	 */
	abstract public function mainPageViewContent($args);	

	/**
	 * Returns the title for the main page. Every module must
	 * implement this function to set the title to be shown. 
	 * For more information visit the module creation guidelines: 
	 * @return String the code for the content of the main page.
	 */
	abstract public function mainPageViewTitle();
	
	/**
	 * Returns the subtitle for the main page. Every module must
	 * implement this function to set the subtitle to be shown. 
	 * For more information visit the module creation guidelines: 
	 * @return String the code for the content of the main page.
	 */
	abstract public function mainPageViewSubtitle();
	
	/**
	 * Returns the icon name for the main page. Every module must
	 * implement this function to set the icon to be shown.
	 * This icon is the string representation of a font-awesome
	 * icon, and can be retrieved here:
	 * http://fortawesome.github.io/Font-Awesome/icons/
	 * The icon will be shown both close to the page title and in
	 * the sidebar.
	 * For more information visit the module creation guidelines: 
	 * @return String the code for the content of the main page.
	 */
	abstract public function mainPageViewIcon();
	
	/**
	 * Returns the current bandge number. This badge is meant to indicate
	 * notifications or pending issues managed by the module, if needed.
	 * The badge number for the module will be shown in the sidebar if appliable.
	 * The default value is null, indicating that no badge needs to be shown.
	 * Modules need to override this function, returning an int if they want to
	 * add a badge number to their sidebar menu access.
	 * For more information visit the module creation guidelines: 
	 */
	public function sidebarBadgeNumber() { return null; }
	
	/**
	 * Returns a link to the main module page of this module.
	 */
	public final function mainPageViewURL($args = null, $basedir = "") {
		return \creamy\ModuleHandler::pageLinkForModule($this->getModuleShortName(), $args, $basedir);
	}

	// settings
	
	/**
	 * Returns the settings for the module as an array 
	 * If the module needs to keep some settings, it must return them in an
	 * associative array of type: "setting_name" => "setting_type".
	 * Valid setting_types are "string", "int", "float" and "bool".
	 * Arrays are encouraged to be stored as imploded strings separated by comma.
	 *
	 * If this method returns a valid array, a setting page will be created
	 * automatically in the settings sidebar section where the user can
	 * modify this module's settings.
	 *
	 * If the module doesn't need any setting, you can safely ignore this method or
	 * return null.
	 * For more information visit the module creation guidelines: 
	 * @return Array associative array of "setting_name" => "setting_type" or null.
	 */
	public function moduleSettings() { return null; }
	
	/**
	 * Returns the type of a setting $setting.
	 * @return String a string containing the type of the setting $setting, either 
	 * 		   "string", "int", "float" or "bool", if $setting exists, NULL otherwise.
	 */
	public final function typeOfSetting($setting) {
		$moduleSettings = $this->moduleSettings();
		if (is_array($moduleSettings) && array_key_exists($setting, $moduleSettings)) { return $moduleSettings[$setting]; }
		else { return null; }
	}
	
	/**
	 * Gets a default value for a setting. The default implementation returns 0 for settings with
	 * type "int", "" (empty string) for settings with type "string", 0.0 for settings with type
	 * "float", and "false" for settings with type "bool".
	 * Modules can override this method to return a different default value for some or all of 
	 * its properties, i.e:
	 * public function defaultValueForSetting($setting) {
	 * 		if ($setting == "mySetting") { return "Default value for mySetting"; }
	 *		else return parent::defaultValueForSetting($setting);
	 * }
	 * @return mixed the default value for the setting $setting in database if it exists. NULL otherwise.
	 */
	public function defaultValueForSetting($setting) {
		if ($type = $this->typeOfSetting($setting)) {
			if ($type == CRM_SETTING_TYPE_STRING) return "";
			if ($type == CRM_SETTING_TYPE_INT) return 0;
			if ($type == CRM_SETTING_TYPE_FLOAT) return 0.0;
			if ($type == CRM_SETTING_TYPE_BOOL) return false;
			if ($type == CRM_SETTING_TYPE_DATE) return date("Y-m-d H:i:s");
		} 
		// unknown setting. Fallback to empty string.
		return "";
	}
	
	/**
	 * Returns the value for a module setting.
	 * @param String $setting the name of the setting to get.
	 * @return mixed the value for the setting $setting in database if it exists. NULL otherwise.
	 */
	public final function valueForModuleSetting($setting) {
		$settingContext = $this->databaseTableName();
		$this->dbConnector->where("context", $settingContext);
		$this->dbConnector->where("setting", $setting);
		$result = $this->dbConnector->getOne(CRM_SETTINGS_TABLE_NAME);
		if ((isset($result)) && (array_key_exists("value", $result))) {
			$type = $this->typeOfSetting($setting); 
			// boolean?
			if ($type == CRM_SETTING_TYPE_BOOL) {
				return filter_var($result["value"], FILTER_VALIDATE_BOOLEAN);
			}
			// other
			return $result["value"]; 
		}
		else return $this->defaultValueForSetting($setting);
	}
	
	/**
	 * Sets the value for a module setting.
	 */
	public final function setSettingValue($setting, $value) {
		// setting data
		$context = $this->databaseTableName();
		$data = array("setting" => $setting, "context" => $context, "value" => $value);
		// check if there was a previous value stored for this setting.
		$this->dbConnector->where("setting", $setting)->where("context", $context);
		if ($this->dbConnector->has(CRM_SETTINGS_TABLE_NAME)) { // Setting was stored previously. Update.
			$this->dbConnector->where("setting", $setting)->where("context", $context);
			return $this->dbConnector->update(CRM_SETTINGS_TABLE_NAME, $data);
		} else { // setting was not stored previously. Insert.
			return $this->dbConnector->insert(CRM_SETTINGS_TABLE_NAME, $data);
		}
	}
	
	// scheduled jobs
	
	/**
	 * This method allows the module to execute tasks periodically. It will be called
	 * by the job-scheduler on a regular basis (default 1/day). Methods that want to
	 * be called periodically and respond to regular scheduled jobs must implement
	 * this method. If the module wants to check the last time it was invoked, it
	 * should use a setting to store the last time it was invoked, and update it
	 * with the current time when this function is invoked.
	 * The module will receive the period of the scheduled job, one of CRM_JOB_SCHEDULING_*,
	 * and it should respond only for the periods that are of interest for the module.
	 * I.E: A module could be interested only in performing some tasks every hour and then
	 * every month, but not daily or weekly.
	 * @param Int Period for the job scheduling, one of $period CRM_JOB_SCHEDULING_*.
	 * @return nothing.
	 */
	public function scheduledJobForModule($period) {  } // do nothing by default.
	
	// hooks
	
	/**
	 * This hook allows the module to show a box in the dashboard.
	 * The module can respond to this hook by returning a box using any of the
	 * UIHandler methods boxWith...
	 * If the module doesn't want to use this hook, it should not override it
	 * or return null when doing so.
	 * @param Bool $wantsFullRow if true, a full row will be dedicated to the
	 * hook. Otherwise, the UI system will try to adjust it along other modules
	 * in half a row.
	 * @return String The module must return a row of content generated with a call to
	 * UIHandler's fullRowWithContent(). The content must be wrapped in a box by
	 * calling any of the UIHandler's boxWith...() methods. If the module doesn't 
	 * need to show any box in the dashboard, it should return null.
	 */
	public function dashboardHook($wantsFullRow = true)									{ return null; }
	
	/**
	 * This hook allows the module to filter the data shown of the customer.
	 * The module can change the data that's been shown, insert links or
	 * annotations based on its own data, or modify the way the information is shown.
	 * @param Array $fields an associative array with the fields that are going to be
	 * shown to the user, in the form "field name" => "field value".
	 * @return Array The module must return an associative array with the same fields, with
	 * the values modified/altered, or null if it doesn't want to modify anything.
	 */
	public function customerListFieldsHook($fields)										{ return null; }

	/**
	 * This hook allows the module to add an action to the customer popup action button.
	 * The module must respond with an action for a popup button by calling any of the
	 * actionForPopupButtonWith...() methods of UIHandler.
	 * @param Int $customerid the id of the customer the button will act upon.
	 * @param String $customername the full name of the customer the button will act upon.
	 * @param String $customertype the string identifier of the customer type (useful for
	 *		  accessing customer screens and calling other pages).
	 * @return String the HTML code for the action for the customer's popup button 
	 * 		   generated by calling any UIHandler actionForPopupButtonWith...() methods.
	 */
	public function customerListPopupHook($customerid, $customername, $customertype)	{ return null; }

	/**
	 * This hook allows the module to add a custom Javascript related to the actions in the
	 * customer list popup hook. This javascript will be added only once, and will allow the
	 * module to set custom javascript actions to interact with the code of the customerListPopupHook.
	 * The code returned by this function will be automatically wrapped in a $(document).ready() function.
	 * @param String $customertype the string identifier of the customer type (useful for
	 *		  accessing customer screens and calling other pages).
	 * @return String the Javascript code to execute in response to interaction of the custom list
	 * hook. This code will be automatically wrapped in a $(document).ready() function.
	 */
	public function customerListActionHook($customertype) 								{ return null; }

	/**
	 * This hook allows the module to add buttons, actions or messages in the footer of
	 * the customer table. It will receive the customer type as a paramenter, so it can
	 * be used to set some global actions like imports, bulk actions on clients of 
	 * certain types, etc...
	 * The module must respond with the code to be put inside the box-footer. If it wants
	 * to be separated from other context, it can use lists, paragraphs, or append
	 * <div class="modal-footer clearfix"> to the end of the code.
	 * @param String $customertype the string identifier of the customer type.
	 * @return String the html code to put in the customer list footer section.
	 */
	public function customerListFooterHook($customertype)								{ return null; }

	/**
	 * This hook allows to set actions and custom data for a concrete customer.
	 * It can be used to set additional data or parameters for users and store them
	 * in the module database, or apply custom actions to concrete customers.
	 * The module must respond with a box by calling any of the boxWith...() methods
	 * of UIHandler.
	 * @param Int $customerid The id of the customer being shown/edited.
	 * @param String $customertype The type of customer being edited (for interaction/edition).
	 * @return String a box with the code for this module's customer edition.
	 */
	public function customerDetailHook($customerid, $customertype)						{ return null; }

	/**
	 * This hook allows to set actions and buttons for the mailbox message list.
	 * It can be used to execute actions on selected messages. The hook will receive
	 * the identifier of the mailbox pseudofolder. The module can get the 
	 * corresponding mailbox table to access the database by means of the
	 * DbHandler function getTableNameForFolder($folder).
	 * The module must generate a button with generateMailBoxButton() method. The
	 * associated javascript can be added by defininf the messageListActionHook method.
	 * @param Int $folder mailbox pseudofolder identifier. Table name can be obtained by
	 *        invoking DbHandler getTableNameForFolder($folder).
	 * @return String the HTML code for the mailbox button, from generateMailBoxButton().
	 */	
	public function messageListFooterHook($folder)										{ return null; }
	
	/**
	 * This hook allows the module to add a custom Javascript related to the actions in the
	 * message list popup hook. This javascript will be added only once, and will allow the
	 * module to set custom javascript actions to interact with the code of the messageListHook.
	 * If you want the code to be executed at document, load, you wrap it in a $(document).ready() 
	 * function by calling the UIHandler method wrapOnDocumentReady().
	 * @param String $customertype the string identifier of the customer type (useful for
	 *		  accessing customer screens and calling other pages).
	 * @return String the Javascript code to execute in response to interaction of the custom list hook.
	 */
	public function messageListActionHook($folder)	 									{ return null; }

	/**
	 * This hook allows to set buttons with actions in the mail reading view.
	 * It can be used to modify, add, remove or process the text that's currently
	 * being edited in the compose mail view. The hook will receive the message text,
	 * and must return the modified text that will substitute the old content directly
	 * in the mail composer view.
	 * @param Int $messageid 	identifer for the message.
	 * @param Int $folder		identifier of the folder containing the message
	 * @return String the code for the footer buttons for the message.
	 */
	public function messageDetailFooterHook($messageid, $folder)						{ return null; }
	

	/**
	 * This hook allows the module to add a custom Javascript related to the actions in the
	 * message detail hook. This javascript will be added only once, and will allow the
	 * module to set custom javascript actions to interact with the code of the messageDetailFooterHook.
	 * If you want the code to be executed at document, load, you wrap it in a $(document).ready() 
	 * function by calling the UIHandler method wrapOnDocumentReady().
	 * @param Int $messageid 	identifer for the message.
	 * @param Int $folder		identifier of the folder containing the message
	 * @return String the code for the action to apply to the message.
	 */
	public function messageDetailActionHook($messageid, $folder)						{ return null; }
	

	/**
	 * This hook allows to set custom buttons at the bottom right of the mail composer view.
	 * @return String the HTML code for the buttons for the compose mail.
	 */
	public function messageComposeFooterHook()											{ return null; }

	/**
	 * This hook allows to interact with the text shown in the mail composer view.
	 * It can be used to launch specific actions on messages (like sharing in a
	 * social network, for instance). 
	 * @return String the HTML code for the action to apply to the selected messages.
	 */
	public function messageComposeActionHook()											{ return null; }

	/**
	 * This message allows you to add a hover action to the task list view. This 
	 * action will be added to every task listed in the pending task list.
	 * The module has to return a hover action generated by means of UIHandler method
	 * hoverActionButton(). The method will receive the task id in the $taskid parameter.
	 * @param Int $taskid the identifier of the task this hover action will be applied to.
	 * @return String the HTML code for the hover action of this module.
	 */
	public function taskListHoverHook($taskid)											{ return null; }

	/**
	 * This hook allows the module to add a custom Javascript related to the actions in the
	 * task list popup hook. This javascript will be added only once, and will allow the
	 * module to set custom javascript actions to interact with the code of the taskListHook.
	 * The code returned by this function will be automatically wrapped in a $(document).ready() function.
	 * @param String $customertype the string identifier of the customer type (useful for
	 *		  accessing customer screens and calling other pages).
	 * @return String the Javascript code to execute in response to interaction of the custom list
	 * hook. This code will be automatically wrapped in a $(document).ready() function.
	 */
	public function taskListActionHook() 												{ return null; }

	/**
	 * This hook allows a module to add custom notifications for the timeline TODAY section.
	 * The hook must return HTML code with one or more valid timeline items, by calling
	 * UIHandler methods timelineItemWithData, timelineItemForNotification or
	 * timelineItemWithMessage().
	 * The hook will receive a timeline temporal indicator in the parameter $period, 
	 * such as CRM_NOTIFICATION_PERIOD_PASTWEEK or CRM_NOTIFICATION_PERIOD_TODAY, to
	 * indicate the module the period that the request notification items are going to
	 * be shown.
	 * @param String $period the period for the notification items to retrieve.
	 * @return String a string containing a set of timeline notification items.
	 */
	public function notificationsHook($period)											{ return null; }

	/**
	 * This hook allows a module to add a top bar icon in the top bar to show 
	 * important elements that must be visible so the user can access them easily
	 * at any time. Please do not abuse this hook. Use only if you need some 
	 * functionality that has to be present in every screen at the top bar.
	 * @return String the HTML code for a top bar icon element for this module.
	 */
	public function topBarHook()														{ return null; }

	// database

	/**
	 * Returns the table name that will be assigned to the module. This name will be
	 * built from the module name, prefixed by the "module_" prefix.
	 * @return String the name of the database.
	 */
	public final function databaseTableName() {
		// already set?
		if (isset($this->dbTableName)) { return $this->dbTableName; }
		// build the suffix based on name.
		$suffixFromName = trim(preg_replace('#[^a-zA-Z0-9_]#', '', $this->moduleName));
		if (strlen($suffixFromName) < 1) { return null; }
		else { 
			$tableName = "module_".$suffixFromName;
			if (strlen($tableName) > CRM_MODULE_MAX_TABLENAME_LENGTH)  { 
				$tableName = substr($tableName, 0, CRM_MODULE_MAX_TABLENAME_LENGTH);  
			}
			$this->dbTableName = $tableName;
			return $tableName;
		}
	}
	
	/**
	 * This method must return an associative array with the parameters for the database
	 * creation consisting in sets of "field_name" => "SQL definition".
	 * The database will be created with those parameters, and an additional id parameter
	 * for identification of the table elements. This id field will be defind as INT(11), 
	 * AUTO_INCREMENT. Any other id field returned by this method will be ignored.
	 * For more information visit the module creation guidelines: 
	 */
	public function databaseTableFields() { return null; }

	/**
	 * This function indicates the ModuleHandler if the module needs database connection functionality.
	 * It is set by default to false for performance reasons. A module that needs to access the database
	 * needs to override this method to return true here.
	 * For more information visit the module creation guidelines: 
	 */
	public function needsDatabaseFunctionality() { return false; }
	
}

/** This interface allows any object to retrieve metadata information about a module */
interface ModuleMetadata {
	/**
	 * This function must return the full module name.
	 * Subclasses of Module MUST implement this method.
	 * For more information visit the module creation guidelines: 
	 * @return String the full name of the module as a string.
	 */
	static function getModuleName();
	
	/**
	 * This function must return the module version. The version must be a numerical version string. I.E: "1.31"
	 * Subclasses of Module MUST implement this method.
	 * For more information visit the module creation guidelines: 
	 * @return String the version string for the module.
	 */
	static function getModuleVersion();
	
	/**
	 * This function must return a brief description for the module. The description will be shown in the modules settings panel.
	 * Subclasses of Module MUST implement this method.
	 * For more information visit the module creation guidelines: 
	 * @return String the version string for the module.
	 */
	static function getModuleDescription();
} 
?>