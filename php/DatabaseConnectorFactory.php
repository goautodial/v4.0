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

// dependencies
namespace creamy;
@include_once("Config.php");


// Database Connectors
define ('CRM_DB_CONNECTOR_TYPE_MYSQL', "MySQL");
define ('CRM_DB_CONNECTOR_TYPE_PGSQL', "PgSQL");

/** The class DatabaseConnectorFactory is in charge of retrieving a proper database connector for the given database. */
class DatabaseConnectorFactory {
	/** Creation and class lifetime management */

	/**
     * Returns the singleton instance of DatabaseConnectorFactory.
     * @staticvar DatabaseConnectorFactory $instance The DatabaseConnectorFactory instance of this class.
     * @return DatabaseConnectorFactory The singleton instance.
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
     * Protected constructor to prevent creating a new instance of the
     * *Singleton* via the `new` operator from outside of this class.
     */
    protected function __construct()
    {
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
    
    /** Returns the given database connector for a given database connector type */
    public function getDatabaseConnectorOfType($type, $dbhost = null, $dbname = null, $dbuser = null, $dbpass = null, $dbport = null) {
	    
	    if (empty($dbhost) && defined('DB_HOST')) { $dbhost = DB_HOST; }	    
	    if (empty($dbname) && defined('DB_NAME')) { $dbname = DB_NAME; }	    
	    if (empty($dbuser) && defined('DB_USERNAME')) { $dbuser = DB_USERNAME; }	    
	    if (empty($dbpass) && defined('DB_PASSWORD')) { $dbpass = DB_PASSWORD; }	    
	    if (empty($dbport) && defined('DB_PORT')) { $dbport = DB_PORT; }	    

	    if ($type == CRM_DB_CONNECTOR_TYPE_MYSQL) { // MySQL Database connector
		    require_once("db_connectors/MysqliDb.php");
		    try {
			    @$mysqldb = new \MysqliDb($dbhost, $dbuser, $dbpass, $dbname, $dbport);
			    if (empty($mysqldb)) { throw new \Exception("Database access failed. Incorrect credentials or missing parameters."); return null; }
			    // try to set the timezone (for dates).
				$mysqldb->where("setting", CRM_SETTING_TIMEZONE);
				$mysqldb->where("context", CRM_SETTING_CONTEXT_CREAMY);
				if ($result = $mysqldb->getOne(CRM_SETTINGS_TABLE_NAME)) {
					$timezone = $result["value"];
					if (isset($timezone)) { date_default_timezone_set($timezone); } 
				} else { // fallback.
					if (defined('CRM_TIMEZONE')) { $timezone = CRM_TIMEZONE; }
					if (defined('CRM_LOCALE')) { date_default_timezone_set($timezone); }			
				}
			    // return MySQL database connector
			    return $mysqldb;
		    } catch (\Exception $e) {
		    	throw new \Exception("Incorrect credentials. Access denied or incorrect parameters.");
		    	return null;
		    }
		    
	    } else {
		    throw new \Exception("Database connector $type not supported yet!");
	    }
    }
    
    /** Database connection for asterisk db */
    public function getDatabaseConnectorOfTypeAsterisk($type, $dbhost = null, $dbname = null, $dbuser = null, $dbpass = null, $dbport = null) {
	    
	    if (empty($dbhost) && defined('DB_HOST')) { $dbhost = DB_HOST; }	    
	    if (empty($dbname) && defined('DB_NAME_ASTERISK')) { $dbname = DB_NAME_ASTERISK; }	    
	    if (empty($dbuser) && defined('DB_USERNAME')) { $dbuser = DB_USERNAME; }	    
	    if (empty($dbpass) && defined('DB_PASSWORD')) { $dbpass = DB_PASSWORD; }	    
	    if (empty($dbport) && defined('DB_PORT')) { $dbport = DB_PORT; }	    

	    if ($type == CRM_DB_CONNECTOR_TYPE_MYSQL) { // MySQL Database connector
		    require_once("db_connectors/MysqliDb.php");
		    try {
			    @$mysqldb = new \MysqliDb($dbhost, $dbuser, $dbpass, $dbname, $dbport);
			    if (empty($mysqldb)) { throw new \Exception("Database access failed. Incorrect credentials or missing parameters."); return null; }
			    // try to set the timezone (for dates).
				//$mysqldb->where("setting", CRM_SETTING_TIMEZONE);
				//$mysqldb->where("context", CRM_SETTING_CONTEXT_CREAMY);
				//if ($result = $mysqldb->getOne(CRM_SETTINGS_TABLE_NAME)) {
				//	$timezone = $result["value"];
				//	if (isset($timezone)) { date_default_timezone_set($timezone); } 
				//} else { // fallback.
				//	if (defined('CRM_TIMEZONE')) { $timezone = CRM_TIMEZONE; }
				//	if (defined('CRM_LOCALE')) { date_default_timezone_set($timezone); }			
				//}
			    // return MySQL database connector
			    return $mysqldb;
		    } catch (\Exception $e) {
		    	throw new \Exception("Incorrect credentials. Access denied or incorrect parameters.");
		    	return null;
		    }
		    
	    } else {
		    throw new \Exception("Database connector $type not supported yet!");
	    }
    }
    
	/**
	 * This function checks if we can stablish a connection to the database using a connector of type $type.
	 * If true, we can safely assume that we can instantiate and use a DbConnector of this type by using getDatabaseConnectorOfType.
	 * @param $type type of connector to check 
	 * @return true if we have all the requisites & credentials needed for instantiating a DbConnector of type $type, false otherwise.
	 */
    public static function instantiationAvailableForConnectorOfType($type) {
	    if ($type == CRM_DB_CONNECTOR_TYPE_MYSQL) { // for mysql, we need the credentials defined in Config.php
		    if (defined("DB_HOST") && defined("DB_USERNAME") && defined("DB_PASSWORD") && defined("DB_NAME") && defined("DB_PORT")) return true;
		    else return false;
	    } else { // not supported.
		    return false;
	    }
    }
}	
?>