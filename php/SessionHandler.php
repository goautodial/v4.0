<?php
namespace creamy;

// dependencies
require_once('CRMDefaults.php');
require_once('LanguageHandler.php');
require_once('DbHandler.php');

class SessionHandler {
    // ****************************************************************************
    // This class saves the PHP session data in a database table.
    // ****************************************************************************
    private $db;
    
    // ****************************************************************************
    // class constructor
    // ****************************************************************************
    function __construct () {
        $this->db = new \creamy\DbHandler();
    }
    
    // ****************************************************************************
    function open ($save_path, $session_name) {
        // do nothing
		error_log('open');
        return TRUE;
        
    }
    
    // ****************************************************************************
    function close () {
        if (!empty($this->fieldarray)) {
            // perform garbage collection
            //$result = $this->gc(CRM_SESSION_EXPIRATION);
            error_log('close');
            //return $result;
			return TRUE;
        }
        
        return FALSE; 
    }
    
    // ****************************************************************************
    function read ($session_id) {
        //$fieldarray = $this->_dml_getData("session_id='" .addslashes($session_id) ."'");
        
        $fieldarray = $this->db->onSessionRead($session_id);
        //var_dump($fieldarray[0]);
        
		error_log('read');
        if (isset($fieldarray[0]['user_data'])) {
            $this->fieldarray = $fieldarray[0];
            $this->fieldarray['user_data'] = '';
            return $fieldarray[0]['user_data'];
        } else {
            return '';  // return an empty string
        }
    }
    
    // ****************************************************************************
    function write ($session_id, $session_data) {
		error_log('write');
        if (!empty($this->fieldarray)) {
            if ($this->fieldarray['session_id'] != $session_id) {
                // user is starting a new session with previous data
                $this->fieldarray = array();
            }
        }
		
        var_dump($this->fieldarray);
		
        if (empty($this->fieldarray)) {
            // create new record
            error_log('insert');
			$postData = array(
				'session_id' => $session_id,
				'user_data' => addslashes($session_data),
				'last_activity' => time(),
				'ip_address' => $_SERVER['REMOTE_ADDR'],
				'user_agent' => $_SERVER['HTTP_USER_AGENT']
			);
            
			$result = $this->db->onSessionWrite('insert', $postData, $session_id);
        } else {
            // update existing record
            error_log('update');
            error_log($session_data);
			$postData = array(
				'user_data' => addslashes($session_data),
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
        error_log('destroy');
        //$this->db->onSessionDestroy($session_id);
        
        return TRUE;
    }
    
    // ****************************************************************************
    function gc ($max_lifetime) {
		error_log('gc');
        $count = $this->db->onSessionGC($max_lifetime);
        
        return TRUE;
    }
    
    // ****************************************************************************
    function __destruct () {
		error_log('destruct');
        @session_write_close();
    }
    
// ****************************************************************************
} // end class
// ****************************************************************************
?>