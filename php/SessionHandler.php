<?php
namespace creamy;

// dependencies
require_once('CRMDefaults.php');
require_once('LanguageHandler.php');
require_once('DbHandler.php');

//require_once('std.table.class.inc');
class SessionHandler {
    // ****************************************************************************
    // This class saves the PHP session data in a database table.
    // ****************************************************************************
    private $db;
    private $fieldarray;
    
    // ****************************************************************************
    // class constructor
    // ****************************************************************************
    function __construct () {
        $this->db = new \creamy\DbHandler();
    }
    
    // ****************************************************************************
    function open ($save_path, $session_name) {
        // do nothing
        return TRUE;
        
    }
    
    // ****************************************************************************
    function close () {
        if (!empty($this->fieldarray)) {
            // perform garbage collection
            $result = $this->gc(CRM_SESSION_EXPIRATION);
            return TRUE;
        }
        
        return FALSE; 
    }
    
    // ****************************************************************************
    function read ($session_id) {
        //$fieldarray = $this->_dml_getData("session_id='" .addslashes($session_id) ."'");
        
        $fieldarray = $this->db->onSessionRead($session_id);
        
        if (isset($fieldarray['user_data'])) {
            $this->fieldarray = $fieldarray;
            $this->fieldarray['user_data'] = '';
            return $fieldarray['user_data'];
        } else {
            return '';  // return an empty string
        }
    }
    
    // ****************************************************************************
    function write ($session_id, $session_data) {
        if (!empty($this->fieldarray)) {
            if ($this->fieldarray['session_id'] != $session_id) {
                // user is starting a new session with previous data
                $this->fieldarray = array();
            }
        }
        
        if (empty($this->fieldarray)) {
            // create new record
			$postData = array(
				'session_id' => $this->db->escape_string($session_id),
				'user_data' => $this->db->escape_string($session_data),
				'last_activity' => time(),
				'ip_address' => $_SERVER['REMOTE_ADDR'],
				'user_agent' => $_SERVER['HTTP_USER_AGENT']
			);
			
			$result = $this->db->onSessionWrite('insert', $postData);
        } else {
            // update existing record
			$postData = array(
				'user_data' => $this->db->escape_string($session_data),
				'last_activity' => time(),
				'ip_address' => $_SERVER['REMOTE_ADDR'],
				'user_agent' => $_SERVER['HTTP_USER_AGENT']
			);
			
			$result = $this->db->onSessionWrite('update', $postData, $session_id);
        }
        
        return TRUE;
    }
    
    // ****************************************************************************
    function destroy ($session_id) {
        $this->db->onSessionDestroy($session_id);
        
        return TRUE;
    }
    
    // ****************************************************************************
    function gc ($max_lifetime) {
        $count = $this->db->onSessionGC($max_lifetime);
        
        return TRUE;
    }
    
    // ****************************************************************************
    function __destruct () {
        //@session_write_close();
    }
    
// ****************************************************************************
} // end class
// ****************************************************************************
?>