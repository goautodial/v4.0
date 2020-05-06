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

require_once("CRMDefaults.php");
require_once("DbHandler.php");
require_once("Config.php");

define ('CRM_NO_VERSION', '0.1');
define ('CRM_OLD_CONFIG_VERSION_FILE', 'Config.php');
define ('CRM_REMOTE_VERSION_URL', 'http://creamycrm.com/last_version.txt');

/**
 * The Updater class is in charge of managing the version change and updating system of creamy.
 * Updater uses the Singleton design pattern, so it must be accessed by mean of the shared instance:
 * \creamy\Updater::getInstance()
 */
class Updater {
	/** Database connector */
    private $dbConnector;
    /** The version of Creamy CRM as set in the filesystem */
    private $currentFilesystemVersion;
    private $currendDatabaseVersion;
    private $updateLog = "";

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
	 * Constructor.
	 */
    function __construct($dbConnectorType = CRM_DB_CONNECTOR_TYPE_MYSQL) {
		// database
		require_once('DatabaseConnectorFactory.php');
		$this->dbConnector = \creamy\DatabaseConnectorFactory::getInstance()->getDatabaseConnectorOfType($dbConnectorType);
    
		// system versions
		$this->currendDatabaseVersion = $this->checkCurrentDatabaseVersion();
		$this->currentFilesystemVersion = $this->checkCurrentFilesystemVersion();
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
    
    /** Check versions */
    
	private function checkCurrentFilesystemVersion() { 
		if (defined('CRM_VERSION')) { return CRM_VERSION; }
		else { return null; }	
	}

	private function checkCurrentDatabaseVersion() {
		if ($this->dbConnector->has(CRM_SETTINGS_TABLE_NAME)) {
	        // set proper timezone (if found)
	        if (defined('CRM_TIMEZONE')) { $timezone = CRM_TIMEZONE; }
	        else { $timezone = "UTC"; }
	        ini_set('date.timezone', $timezone);
			date_default_timezone_set($timezone);
			
			// check settings table (if found)
			$this->dbConnector->where("setting", "crm_version");
			$versionFromSettings = $this->dbConnector->getValue(CRM_SETTINGS_TABLE_NAME, "value");
			return $versionFromSettings;
		} 
	}
	
	private function checkRemoteVersion() {
		return file_get_contents(CRM_REMOTE_VERSION_URL);
	}
	
	public function CRMIsUpToDate() {
		return (floatval($this->getCurrentVersion()) >= floatval(CRM_INSTALL_VERSION));
	}
	
	public function getCurrentVersion() {
		// first try to get the version from database.
		if (isset($this->currendDatabaseVersion)) {
			return floatval($this->currendDatabaseVersion);
		}
		// if not found, check file system version.
		if (isset($this->currentFilesystemVersion)) {
			return floatval($this->currentFilesystemVersion);
		}
		// else return no version (0.1).
		else return CRM_NO_VERSION;
	}
	
	public function canUpdateFromVersion($version) {
		if (floatval($version) < floatval(CRM_INSTALL_VERSION)) { return true; }
		else { return false; }
	}
	
	/*************** Functions implementing the changes to database from version 0.1 to 1.0 *******************/
	
	// Changes from version < 1.0 
	public function updateCRM($fromVersion) {
		$this->updateLog = "Updating system from version $fromVersion to version ".CRM_INSTALL_VERSION."...<br/>\n";
		if ($this->canUpdateFromVersion($fromVersion)) {
			// add settings table
			$this->updateLog .= "Creating settings table... ";
			if (!$this->addSettingsTable()) {
				$this->updateLog .= "Failed! (".$this->dbConnector->getLastError().")<br/>\n";
				return false;
			} else { $this->updateLog .= "Done<br/>\n"; }
			
			// Set admin account in settings.
			$this->updateLog .= "Setting admin account... ";
			if (!$this->setAdminAccountInSettings()) {
				$this->updateLog .= "Failed! (".$this->dbConnector->getLastError().")<br/>\n";
				return false;
			} else { $this->updateLog .= "Done<br/>\n"; }
			
			// Set general parameters in settings.
			$this->updateLog .= "Setting general parameters in settings... ";
			if (!$this->setGeneralParametersInSettings()) {
				$this->updateLog .= "Failed! (".$this->dbConnector->getLastError().")<br/>\n";
				return false;
			} else { $this->updateLog .= "Done<br/>\n"; }
			
			// add company_name and website fields to customers and contacts.
			$this->updateLog .= "Adding company and website fields to customers... ";
			if (!$this->addCompanyAndWebsiteFieldsToCustomers()) {
				$this->updateLog .= "Failed! (".$this->dbConnector->getLastError().")<br/>\n";
				return false;
			} else { $this->updateLog .= "Done<br/>\n"; }
			
			// add attachments for messages
			$this->updateLog .= "Adding attachments table for message attachments...";
			if (!$this->addAttachmentsTables()) {
				$this->updateLog .= "Failed! (".$this->dbConnector->getLastError().")<br/>\n";
				return false;
			} else { $this->updateLog .= "Done<br/>\n"; }
						
			// add events
			$this->updateLog .= "Adding events table for calendar events...";
			if (!$this->addEventsTable()) {
				$this->updateLog .= "Failed! (".$this->dbConnector->getLastError().")<br/>\n";
				return false;
			} else { $this->updateLog .= "Done<br/>\n"; }
			
			// set message field in messages as longtext.
			$this->updateLog .= "Extending message fields to longtext...";
			if (!$this->extendMessageFields()) {
				$this->updateLog .= "Failed! (".$this->dbConnector->getLastError().")<br/>\n";
				return false;
			} else { $this->updateLog .= "Done<br/>\n"; }

			// set email column in users table as unique.
			$this->updateLog .= "Setting email column as unique in users table...";
			if (!$this->setEmailFieldAsUniqueInUsersTable()) {
				$this->updateLog .= "Failed! (".$this->dbConnector->getLastError().")<br/>\n";
				return false;
			} else { $this->updateLog .= "Done<br/>\n"; }

			// removing statistics mysql event
			$this->updateLog .= "Removing deprecated statistics mysql event...";
			if (!$this->removeMysqlStatisticsEvent()) {
				$this->updateLog .= "Failed! (".$this->dbConnector->getLastError().")<br/>\n";
				return false;
			} else { $this->updateLog .= "Done<br/>\n"; }

			// update the local version and remove file versions...
			$this->currendDatabaseVersion = CRM_INSTALL_VERSION;
			$this->currentFilesystemVersion = CRM_INSTALL_VERSION;
			$this->removeCRMVersionAndObsoleteFieldsFromConfigFile();
			
			return true;
		} else {
			require_once('./LanguageHandler.php');
			$lh = \creamy\LanguageHandler::getInstance();
			$this->updateLog .= $lh->translationFor("crm_update_impossible");
			return false;
		}		
	}
	
	private function removeCRMVersionAndObsoleteFieldsFromConfigFile() {
		$newContents = "";
		$filename = dirname(__FILE__).DIRECTORY_SEPARATOR.CRM_OLD_CONFIG_VERSION_FILE;
		foreach(file($filename) as $line) {
			if (strpos($line, 'CRM_VERSION') === false && strpos($line, 'CRM_TIMEZONE') === false && 
				strpos($line, 'CRM_LOCALE') === false && strpos($line, 'CRM_SECURITY_TOKEN') === false &&
				strpos($line, 'Creamy version') === false && strpos($line, 'CRM_ADMIN_EMAIL') === false) { $newContents .= $line; }
		}
		file_put_contents($filename, $newContents);
	}
	
	/** Version 1.0 adds the settings table */
	private function addSettingsTable() {
		$fields = array("setting" => "VARCHAR(255) NOT NULL", "context" => "VARCHAR(255) NOT NULL" , "value" => "LONGTEXT");
		return $this->dbConnector->createTable(CRM_SETTINGS_TABLE_NAME, $fields, ["setting", "context"]);
	}
	
	/** Searchs for the admin user (first user with admin role) and sets it as the administrator of the CRM. */
	private function setAdminAccountInSettings() {
		// check if we had the variable defined in Config.
		$adminEmailFound = false;
		if (defined('CRM_ADMIN_EMAIL')) {
			$this->dbConnector->where("email", CRM_ADMIN_EMAIL);
			$currentAdmin = $this->dbConnector->getOne("users");
			if (isset($currentAdmin)) {
				$adminIdData = array("setting" => CRM_SETTING_ADMIN_USER, "context" => CRM_SETTING_CONTEXT_CREAMY, "value" => $currentAdmin["id"]);
				$adminEmailFound = $this->dbConnector->insert(CRM_SETTINGS_TABLE_NAME, $adminIdData);
			}
		}
		// if not, assign the main admin role to the first administrator.
		if ($adminEmailFound == false) {
			$this->dbConnector->where("role", CRM_DEFAULTS_USER_ROLE_ADMIN);
			$this->dbConnector->orderBy("id","asc");
			$mainAdmin = $this->dbConnector->getOne("users");
			if (is_array($mainAdmin) && (array_key_exists("id", $mainAdmin))) {
				$adminName = array("setting" => CRM_SETTING_ADMIN_USER, "context" => CRM_SETTING_CONTEXT_CREAMY, "value" => $mainAdmin["id"]);
				$adminEmailFound = $this->dbConnector->insert(CRM_SETTINGS_TABLE_NAME, $adminName);
			}
		}
		return $adminEmailFound;
	}

	/** Version 1.0 adds the attachments table for message attachments */
	private function addAttachmentsTables() {
		$fields = array(
		  	"message_id" => "INT(11) NOT NULL",
		  	"folder_id" => "INT(11) NOT NULL",
		  	"filepath" => "VARCHAR(255) NOT NULL",
		  	"filetype" => "VARCHAR(255) NOT NULL",
		  	"filesize" => "INT(11) NOT NULL"
		);
		// inbox
		if (!$this->dbConnector->createTable(CRM_ATTACHMENTS_TABLE_NAME, $fields, null)) {
			return false;
		}
		return true;
	}

	/** Version 1.0 adds the events table for calendar events */
	private function addEventsTable() {
		$fields = array(
			"user_id" => "INT(11) NOT NULL",
			"title" => "VARCHAR(512) NOT NULL",
			"all_day" => "INT(1) NOT NULL",
			"start_date" => "DATETIME NULL",
			"end_date" => "DATETIME NULL",
			"url" => "VARCHAR(512) NULL",
			"alarm" => "VARCHAR(80) NULL",
			"notification_sent" => "INT(1) NOT NULL DEFAULT 0",
			"color" => "VARCHAR(80) NOT NULL" 
		);
		if (!$this->dbConnector->createTable(CRM_EVENTS_TABLE_NAME, $fields, null)) {
			return false;
		}
		return true;
	}

	/** Version 1.0 changes the event system, so it no longer relies on MySQL events. */
	private function removeMysqlStatisticsEvent() {
		return $this->dbConnector->dropEvent("creamy_retrieve_statistics");
	}
	
	/** Sets general settings parameters of the CRM */
	private function setGeneralParametersInSettings() {
		// crm version
		$data = array("setting" => CRM_SETTING_CRM_VERSION, "context" => CRM_SETTING_CONTEXT_CREAMY, "value" => CRM_INSTALL_VERSION);
		if (!$this->dbConnector->insert(CRM_SETTINGS_TABLE_NAME, $data)) return false;
		
		// base dir. We suppose here we have been called from updater.php or other root file.
		$currentURL = \creamy\CRMUtils::getCurrentURLPath(); // i.e: http://localhost:8080/creamy/updater.php
		$baseurl = \creamy\CRMUtils::getBasedirFromURL($currentURL); // => http://localhost:8080/creamy
		$data = array("setting" => CRM_SETTING_CRM_BASE_URL, "context" => CRM_SETTING_CONTEXT_CREAMY, "value" => $baseurl);
		if (!$this->dbConnector->insert(CRM_SETTINGS_TABLE_NAME, $data)) return false;
		
		// installation date. We try to get it from the Config.php file first
		if ($timestamp = filemtime(dirname(__FILE__) . '/Config.php')) {
			$dbDate = date("Y-m-d H:i:s", $timestamp);
		} else { $dbDate = $this->dbConnector->now(); }
		$data = array("setting" => CRM_SETTING_INSTALLATION_DATE, "context" => CRM_SETTING_CONTEXT_CREAMY, "value" => $dbDate);
		if (!$this->dbConnector->insert(CRM_SETTINGS_TABLE_NAME, $data)) return false;
		
		// module system enabled (1 by default)
		$data = array("setting" => CRM_SETTING_MODULE_SYSTEM_ENABLED, "context" => CRM_SETTING_CONTEXT_CREAMY, "value" => true);
		if (!$this->dbConnector->insert(CRM_SETTINGS_TABLE_NAME, $data)) return false;
		
		// statistics system enabled (1 by default)
		$data = array("setting" => CRM_SETTING_STATISTICS_SYSTEM_ENABLED, "context" => CRM_SETTING_CONTEXT_CREAMY, "value" => true);
		if (!$this->dbConnector->insert(CRM_SETTINGS_TABLE_NAME, $data)) return false;

		// email notifications of events.
		$data = array("setting" => CRM_SETTING_EVENTS_EMAIL, "context" => CRM_SETTING_CONTEXT_CREAMY, "value" => true);
		if (!$this->dbConnector->insert(CRM_SETTINGS_TABLE_NAME, $data)) return false;

		// job scheduling frequency
		$data = array("setting" => CRM_SETTING_JOB_SCHEDULING_MIN_FREQ, "context" => CRM_SETTING_CONTEXT_CREAMY, "value" => CRM_JOB_SCHEDULING_HOURLY);
		if (!$this->dbConnector->insert(CRM_SETTINGS_TABLE_NAME, $data)) return false;

		// active modules (empty by default)
		$data = array("setting" => CRM_SETTING_ACTIVE_MODULES, "context" => CRM_SETTING_CONTEXT_CREAMY, "value" => "");
		if (!$this->dbConnector->insert(CRM_SETTINGS_TABLE_NAME, $data)) return false;

		// customer list fields
		$data = array("setting" => CRM_SETTING_CUSTOMER_LIST_FIELDS, "context" => CRM_SETTING_CONTEXT_CREAMY, "value" => CRM_SETTING_DEFAULT_CUSTOMER_LIST_FIELDS);
		if (!$this->dbConnector->insert(CRM_SETTINGS_TABLE_NAME, $data)) return false;

		// timezone
		if (defined('CRM_TIMEZONE')) {
			$data = array("setting" => CRM_SETTING_TIMEZONE, "context" => CRM_SETTING_CONTEXT_CREAMY, "value" => CRM_TIMEZONE);
			if (!$this->dbConnector->insert(CRM_SETTINGS_TABLE_NAME, $data)) return false;
		}
		
		// locale
		if (defined('CRM_LOCALE')) {
			$data = array("setting" => CRM_SETTING_LOCALE, "context" => CRM_SETTING_CONTEXT_CREAMY, "value" => CRM_LOCALE);
			if (!$this->dbConnector->insert(CRM_SETTINGS_TABLE_NAME, $data)) return false;
		}
		
		// security token
		if (defined('CRM_SECURITY_TOKEN')) {
			$data = array("setting" => CRM_SETTING_SECURITY_TOKEN, "context" => CRM_SETTING_CONTEXT_CREAMY, "value" => CRM_SECURITY_TOKEN);
			if (!$this->dbConnector->insert(CRM_SETTINGS_TABLE_NAME, $data)) return false;
		}
		return true;
	}
	
	/** Version 1.0 adds columns "company_name" and "website" to all customers and contacts. */
	private function addCompanyAndWebsiteFieldsToCustomers() {
		$customerTypes = $this->dbConnector->get("customer_types");
		foreach ($customerTypes as $customerType) {
			// column company_name
			if (!$this->tableContainsColum($customerType["table_name"], "company_name")) {
				if (!$this->addColumnToTable($customerType["table_name"], "company_name", "VARCHAR(255)")) return false;
			} 
			// colum website
			if (!$this->tableContainsColum($customerType["table_name"], "website")) {
				if (!$this->addColumnToTable($customerType["table_name"], "website", "VARCHAR(255)")) return false;
			}
		}
		return true;
	}
	
	/** Version 1.0 allows for LONGTEXT in message field of message tables. */
	private function extendMessageFields() {
		$tableNames = array(CRM_MESSAGES_INBOX_TABLE_NAME, CRM_MESSAGES_OUTBOX_TABLE_NAME, CRM_MESSAGES_JUNK_TABLE_NAME);
		foreach ($tableNames as $tableName) {
			if (!$this->dbConnector->alterColumnFromTable($tableName, "message", "LONGTEXT")) {
				return false;
			}
		}
		return true;
	}
	
	/** Version 1.0 requires the email field of users to be unique */
	private function setEmailFieldAsUniqueInUsersTable() {
		return $this->dbConnector->setColumnAsUnique(CRM_USERS_TABLE_NAME, "email");
	}
	
	
	/** Utils */
	
	/** Returns the log */
	public function getUpdateLog() { return $this->updateLog; }	
	
	/** 
	 * Returns true if table $table contains column $column, false otherwise.	
	 */
	private function tableContainsColum($table, $column) {
		$columns = $this->dbConnector->rawQuery("SHOW COLUMNS FROM $table LIKE '$column'");
		if ($this->dbConnector->getRowCount() > 0) return true;
		else return false;
	}
	
	/**
	 * Alters table $table, adding column $column.
	 */
	private function addColumnToTable($table, $column, $type, $allowNull = true) {
		$notNull = $allowNull ? "" : "NOT NULL";
		return $this->dbConnector->addColumnToTable($table, $column, $type, $notNull);
	}
}
?>