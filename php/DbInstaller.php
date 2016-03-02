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

require_once('CRMDefaults.php');
require_once('PassHash.php');
require_once('LanguageHandler.php');
require_once('DatabaseConnectorFactory.php');

/**
 * Class to handle DB Installation
 *
 * @author Ignacio Nieto Carvajal
 * @link URL http://digitalleaves.com
 */
class DBInstaller {

    private $dbConnector;
    private $state;
    public $error;
    private $lh;
    
    /* ---------------- Initializers -------------------- */
    
    public function __construct($dbhost, $dbname, $dbuser, $dbpass, $dbport = CRM_DEFAULT_DB_PORT, $dbConnectorType = CRM_DB_CONNECTOR_TYPE_MYSQL) {
		$this->lh = \creamy\LanguageHandler::getInstance();
        try {
	        $dbConnectorFactory = \creamy\DatabaseConnectorFactory::getInstance();
	        $this->dbConnector = $dbConnectorFactory->getDatabaseConnectorOfType($dbConnectorType, $dbhost, $dbname, $dbuser, $dbpass, $dbport);
	        $this->state = CRM_INSTALL_STATE_SUCCESS;
        } catch (\Exception $e) {
            $this->state = CRM_INSTALL_STATE_ERROR;
            $this->error = CRM_INSTALL_STATE_DATABASE_ERROR . ". Unable to instantiate database connector of type $dbConnectorType. Incorrect credentials or access denied.";
        }
    }
    
    public function __destruct() {
    }
    
    public function getState() {
	    return $this->state;
    }
    
    public function getLastErrorMessage() {
	    return $this->error;
    }

    /* ---------------- Setup of database -------------------------- */
    
    /**
	 * Setups the database without the client models, just the standard tables.
	 * It also creates the default admin user.
	 * @param $adminUserName String the name of the admin user.
	 * @param $adminUserPassword String 
	 */
    public function setupBasicDatabase($adminUserName, $adminUserPassword, $adminUserEmail) {
	    // create the basic tables
	    if ($this->setupUsersTable($adminUserName, $adminUserPassword, $adminUserEmail) == false) { return false; }
	    error_log("Creamy install: users table setup OK");
	    if ($this->setupTasksTable() == false) { return false; }
	    error_log("Creamy install: task table setup OK");
	    if ($this->setupNotificationsTable() == false) { return false; }
	    error_log("Creamy install: Notifications table setup OK");
	    if ($this->setupMaritalStatusTable() == false) { return false; }
	    error_log("Creamy install: Marital status table setup OK");
	    if ($this->setupMessagesTables() == false) { return false; }
	    error_log("Creamy install: Messages table setup OK");
	    if ($this->setupEventsTable() == false) { return false; }
	    error_log("Creamy install: Events table setup OK");
	    if ($this->setupAttachmentsTables() == false) { return false; }
	    error_log("Creamy install: Attachments table setup OK");
	    
	    return true;
    }
    
	/* ----------------------- Table creation, deletion and population -------------------------- */

	public function dropPreviousTables() {
		$tablesToDrop = array(CRM_CUSTOMER_TYPES_TABLE_NAME, CRM_MARITAL_STATUS_TABLE_NAME, 
			CRM_MESSAGES_INBOX_TABLE_NAME, CRM_MESSAGES_OUTBOX_TABLE_NAME, CRM_MESSAGES_JUNK_TABLE_NAME, 
			CRM_NOTIFICATIONS_TABLE_NAME, CRM_SETTINGS_TABLE_NAME, CRM_STATISTICS_TABLE_NAME, 
			CRM_TASKS_TABLE_NAME, CRM_USERS_TABLE_NAME, CRM_ATTACHMENTS_TABLE_NAME);
		
		foreach ($tablesToDrop as $tablename) {
			error_log("Creamy install: Dropping table $tablename");
			if (!$this->dbConnector->dropTable($tablename, true)) { 
				$this->error = "Creamy install: Failed to drop table $tablename"; 
				return false; 
			}
		}
	    error_log("Creamy install: Cleaned previous database");
		return true;
	}

	private function setupUsersTable($initialUser, $initialPass, $initialEmail) {
		// users data table.
		$fields = array(
			"name" => "VARCHAR(255) NOT NULL",
			"password_hash" => "VARCHAR(255) NOT NULL",
			"phone" => "VARCHAR(255) DEFAULT NULL",
			"email" => "VARCHAR(255) DEFAULT NULL",
			"avatar"  => "VARCHAR(255) DEFAULT NULL",
			"creation_date" => "DATETIME NOT NULL",
			"role" => "INT(4) NOT NULL",
			"status" => "INT(1) NOT NULL", // 1=enabled, 0=disabled
		);
		if (!$this->dbConnector->createTable(CRM_USERS_TABLE_NAME, $fields, ["name", "email"])) {
			$this->error = "Creamy install: Failed to create table ".CRM_USERS_TABLE_NAME."."; 
			return false;
		}

		// create main admin users.		
		$password_hash = \creamy\PassHash::hash($initialPass);
		$data = array(
			"name" => $initialUser, "password_hash" => $password_hash, "email" => $initialEmail, 
			"avatar" => CRM_DEFAULTS_USER_AVATAR, "creation_date" => "now()", "role" => CRM_DEFAULTS_USER_ROLE_ADMIN,
			"status" => CRM_DEFAULTS_USER_ENABLED
		);
		if (!$this->dbConnector->insert(CRM_USERS_TABLE_NAME, $data)) {
			$this->error = "Creamy install: Failed to insert the initial admin user."; 
			return false;
		}
		return true;
	}
	
	public function setupSettingTable($timezone, $locale, $securityToken) {
		// create setting data table.
		$fields = array("setting" => "VARCHAR(255) NOT NULL", "context" => "VARCHAR(255) NOT NULL", "value" => "LONGTEXT");
		if (!$this->dbConnector->createTable(CRM_SETTINGS_TABLE_NAME, $fields, ["setting", "context"])) { return false; }
		
		// fill the settings table.

		// base dir. We suppose here we have been called from updater.php or other root file.
		$currentURL = \creamy\CRMUtils::getCurrentURLPath(); // i.e: http://localhost:8080/creamy/updater.php
		$baseurl = \creamy\CRMUtils::getBasedirFromURL($currentURL); // => http://localhost:8080/creamy
		$data = array("setting" => CRM_SETTING_CRM_BASE_URL, "context" => CRM_SETTING_CONTEXT_CREAMY, "value" => $baseurl);
		if (!$this->dbConnector->insert(CRM_SETTINGS_TABLE_NAME, $data)) return false;
		
		// admin account
		$adminEmail = array("setting" => CRM_SETTING_ADMIN_USER, "context" => CRM_SETTING_CONTEXT_CREAMY, "value" => 1);
		$adminEmailFound = $this->dbConnector->insert(CRM_SETTINGS_TABLE_NAME, $adminEmail);

		// crm version
		$data = array("setting" => CRM_SETTING_CRM_VERSION, "context" => CRM_SETTING_CONTEXT_CREAMY, "value" => CRM_INSTALL_VERSION);
		if (!$this->dbConnector->insert(CRM_SETTINGS_TABLE_NAME, $data)) return false;
		
		// installation date. We try to get it from the Config.php file first
		if ($timestamp = filemtime(dirname(__FILE__) . '/Config.php')) {
			$dbDate = date("Y-m-d H:i:s", $timestamp);
		} else { $dbDate = $this->dbConnector->now(); }
		$data = array("setting" => CRM_SETTING_INSTALLATION_DATE, "context" => CRM_SETTING_CONTEXT_CREAMY, "value" => $dbDate);
		if (!$this->dbConnector->insert(CRM_SETTINGS_TABLE_NAME, $data)) return false;
		
		// plugin system enabled (1 by default)
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

		// active plugins (empty by default)
		$data = array("setting" => CRM_SETTING_ACTIVE_MODULES, "context" => CRM_SETTING_CONTEXT_CREAMY, "value" => "");
		if (!$this->dbConnector->insert(CRM_SETTINGS_TABLE_NAME, $data)) return false;

		// customer list fields
		$data = array("setting" => CRM_SETTING_CUSTOMER_LIST_FIELDS, "context" => CRM_SETTING_CONTEXT_CREAMY, "value" => CRM_SETTING_DEFAULT_CUSTOMER_LIST_FIELDS);
		if (!$this->dbConnector->insert(CRM_SETTINGS_TABLE_NAME, $data)) return false;

		// timezone
		$data = array("setting" => CRM_SETTING_TIMEZONE, "context" => CRM_SETTING_CONTEXT_CREAMY, "value" => $timezone);
		if (!$this->dbConnector->insert(CRM_SETTINGS_TABLE_NAME, $data)) return false;
		
		// locale
		$data = array("setting" => CRM_SETTING_LOCALE, "context" => CRM_SETTING_CONTEXT_CREAMY, "value" => $locale);
		if (!$this->dbConnector->insert(CRM_SETTINGS_TABLE_NAME, $data)) return false;
		
		// security token
		$data = array("setting" => CRM_SETTING_SECURITY_TOKEN, "context" => CRM_SETTING_CONTEXT_CREAMY, "value" => $securityToken);
		if (!$this->dbConnector->insert(CRM_SETTINGS_TABLE_NAME, $data)) return false;

		error_log("Creamy install: Settings database set.");
		return true;
	}
	
	private function setupTasksTable() {
		$fields = array(
			"description" => "VARCHAR(512) NOT NULL",
			"user_id" => "INT(11) NOT NULL",
			"target_customer_id" => "INT(11)",
			"creation_date" => "DATETIME NOT NULL",
			"completion_date" => "DATETIME DEFAULT NULL",
			"completed" => "INT(3) NOT NULL" // from 0 to 100, 0=not started, 100=completed
		);
		if (!$this->dbConnector->createTable(CRM_TASKS_TABLE_NAME, $fields, null)) {
			$this->error = "Creamy install: Failed to create table ".CRM_TASKS_TABLE_NAME."."; 
			return false;
		}
		return true;
	}

	private function setupEventsTable() {
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
			$this->error = "Creamy install: Failed to create table ".CRM_EVENTS_TABLE_NAME."."; 
			return false;
		}
		return true;
	}

	private function setupNotificationsTable() {
		$fields = array(
			"target_user" => "INT(11) DEFAULT NULL", // '0=all users, otherwise, the user id of the target user.',
			"text" => "VARCHAR(512) NOT NULL",
			"date" => "DATETIME NOT NULL",
			"action" => "VARCHAR(255) DEFAULT NULL", // 'if not null, a link to the target of the action',
			"type" => "VARCHAR(255) NOT NULL"
		);
		if (!$this->dbConnector->createTable(CRM_NOTIFICATIONS_TABLE_NAME, $fields, null)) {
			$this->error = "Creamy install: Failed to create table ".CRM_NOTIFICATIONS_TABLE_NAME."."; 
			return false;
		}
		return true;
	}

	private function setupMaritalStatusTable() {
		$fields = array("name" => "VARCHAR(255) NOT NULL");
		if (!$this->dbConnector->createTable(CRM_MARITAL_STATUS_TABLE_NAME, $fields, null)) {
			$this->error = "Creamy install: Failed to create table ".CRM_MARITAL_STATUS_TABLE_NAME."."; 
			return false;
		}
		
		$marital_statuses = array("single", "married", "divorced", "separated", "widow/er");
		$i = 1;
		foreach ($marital_statuses as $ms) {
			$data = array("id" => $i, "name" => $ms);
			if (!$this->dbConnector->insert(CRM_MARITAL_STATUS_TABLE_NAME, $data)) {
				$this->error = "Creamy install: Failed to initialize ".CRM_MARITAL_STATUS_TABLE_NAME."."; 
				return false;
			}
			$i++;
		}
		return true;
	}

	private function setupMessagesTables() {
		$fields = array(
		  	"user_from" => "INT(11) NOT NULL",
		  	"user_to" => "INT(11) NOT NULL",
		  	"subject" => "VARCHAR(255) NOT NULL",
		  	"message" => "LONGTEXT DEFAULT NULL",
		  	"date" => "DATETIME NOT NULL",
		  	"message_read" => "INT(1) NOT NULL",
		  	"favorite" => "INT(1) NOT NULL DEFAULT 0" // '0=not-favorite, 1=favorite',
		);
		// inbox
		if (!$this->dbConnector->createTable(CRM_MESSAGES_INBOX_TABLE_NAME, $fields, null)) {
			$this->error = "Creamy install: Failed to create table ".CRM_MESSAGES_INBOX_TABLE_NAME."."; 
			return false;
		}
		// outbox
		if (!$this->dbConnector->createTable(CRM_MESSAGES_OUTBOX_TABLE_NAME, $fields, null)) {
			$this->error = "Creamy install: Failed to create table ".CRM_MESSAGES_OUTBOX_TABLE_NAME."."; 
			return false;
		}
		
		// junk
		$fields["origin_folder"] = "varchar(255) NOT NULL";
		if (!$this->dbConnector->createTable(CRM_MESSAGES_JUNK_TABLE_NAME, $fields, null)) {
			$this->error = "Creamy install: Failed to create table ".CRM_MESSAGES_JUNK_TABLE_NAME."."; 
			return false;
		}
		return true;		
	}
	
	private function setupAttachmentsTables() {
		$fields = array(
		  	"message_id" => "INT(11) NOT NULL",
		  	"folder_id" => "INT(11) NOT NULL",
		  	"filepath" => "VARCHAR(255) NOT NULL",
		  	"filetype" => "VARCHAR(255) NOT NULL",
		  	"filesize" => "INT(11) NOT NULL"
		);
		// inbox
		if (!$this->dbConnector->createTable(CRM_ATTACHMENTS_TABLE_NAME, $fields, null)) {
			$this->error = "Creamy install: Failed to create table ".CRM_ATTACHMENTS_TABLE_NAME."."; 
			return false;
		}
		return true;
	}

	private function generateIdentifiersForCustomers($schema, $customCustomers) {
		$customerIdentifiers = array();
		
		if ($schema == CRM_DEFAULTS_CUSTOMERS_SCHEMA_DEFAULT) { // default schema: clients_1 (contacts) and clients_2 (normal clients).
			array_push($customerIdentifiers, "clients_1");
			array_push($customerIdentifiers, "clients_2");			
		} else if ($schema == CRM_DEFAULTS_CUSTOMERS_SCHEMA_CUSTOM) {
			$index = 1;
			foreach ($customCustomers as $description) {
				array_push($customerIdentifiers, "clients_$index");
				$index += 1;
			}
		}
		return $customerIdentifiers;
	}

	public function setupCustomerTables($schema, $customCustomers) {
		// first create the types table
		if (!$this->createCustomerTypesTable()) {
			$this->error = "Creamy install: Unable to setup the customer types table: ".$this->conn->error;
			return false; 
		}
		$customerIdentifiers = $this->generateIdentifiersForCustomers($schema, $customCustomers);
		
		if ($schema == CRM_DEFAULTS_CUSTOMERS_SCHEMA_DEFAULT) { // default schema: clients_1 (contacts) and clients_2 (normal clients).
			if (!$this->createCustomersTableWithNameAndDescription("clients_1", $this->lh->translationFor("contacts"))) { 
				$this->error = "Creamy install: Unable to create the contacts table";
				return false;
			}
			if (!$this->createCustomersTableWithNameAndDescription("clients_2", $this->lh->translationFor("customers"))) { 
				$this->error = "Creamy install: Unable to create the contacts table";
				return false;
			}
		} else if ($schema == CRM_DEFAULTS_CUSTOMERS_SCHEMA_CUSTOM) {
			$index = 1;
			foreach ($customCustomers as $description) {
				if ($index == 1) { $description = $this->lh->translationFor("contacts"); if (empty($description)) $description = "Contacts"; }
				if (!$this->createCustomersTableWithNameAndDescription("clients_$index", $description)) {
					$this->error = "Creamy install: Unable to create the customer table named $description";
					return false;
				}
				$index++;
			}
		}
		
		// if all operations succeed, return true
		return true;
	}
	
	public function setupCustomersStatistics($schema, $customCustomers) {
	    // create the statistics table for tracking evolution in number of customers.
		$customerIdentifiers = $this->generateIdentifiersForCustomers($schema, $customCustomers);
		$fields = array("timestamp" => "DATETIME NOT NULL");
		foreach ($customerIdentifiers as $customerId) {
			$fields[$customerId] = "INT(11) NOT NULL DEFAULT 0";
		}
		if (!$this->dbConnector->createTable(CRM_STATISTICS_TABLE_NAME, $fields, null)) {
			$this->error = "Creamy install: Unable to create the table ".CRM_STATISTICS_TABLE_NAME.".";
			return false;
		}
		// if all operations succeed, return true
		return true;
	}

	private function createCustomersTableWithNameAndDescription($name, $description) {
		$fields = array(
		  "company" => "INT(1) NOT NULL DEFAULT 0",
		  "name" => "VARCHAR(255) NOT NULL",
		  "id_number" => "VARCHAR(255) DEFAULT NULL", // 'passport, dni, nif or identifier of the person',
		  "address" => "TEXT",
		  "city" => "VARCHAR(255) DEFAULT NULL",
		  "state" => "VARCHAR(255) DEFAULT NULL",
		  "zip_code" => "VARCHAR(255) DEFAULT NULL",
		  "country" => "VARCHAR(255) DEFAULT NULL",
		  "phone" => "TEXT",
		  "mobile" => "TEXT",
		  "email" => "VARCHAR(255) DEFAULT NULL",
		  "avatar" => "VARCHAR(255) DEFAULT NULL",
		  "type" => "TEXT",
		  "website" => "VARCHAR(255) DEFAULT NULL",
		  "company_name" => "VARCHAR(255) DEFAULT NULL",
		  "notes" => "TEXT",
		  "birthdate" => "DATETIME DEFAULT NULL",
		  "marital_status" => "INT(11) DEFAULT NULL",
		  "creation_date" => "DATETIME DEFAULT NULL",
		  "created_by" => "INT(11) NOT NULL", // 'id of the user that created the contact or client',
		  "do_not_send_email" => "CHAR(1) DEFAULT NULL",
		  "gender" => "INT(1) DEFAULT NULL" // '0=female, 1=male',
		);
		if ($this->dbConnector->createTable($name, $fields, ["name", "id_number", "email"])) {
			$data = array("table_name" => $name, "description" => $description);
			return ($this->dbConnector->insert(CRM_CUSTOMER_TYPES_TABLE_NAME, $data));
		} else { return true; }
		
		return $success;
	}
	
	private function createCustomerTypesTable() {
		$fields = array(
		  "table_name" => "VARCHAR(255) NOT NULL",
		  "description" => "VARCHAR(255) NOT NULL",
		);
		return $this->dbConnector->createTable(CRM_CUSTOMER_TYPES_TABLE_NAME, $fields, null);
	}
}
?>