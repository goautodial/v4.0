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
    private $_db;
    
    // ****************************************************************************
    // class constructor
    // ****************************************************************************
    function __construct () {
        $this->_db = new \creamy\DbHandler();
        
        // set handler to overide SESSION
        @session_set_save_handler(
            array($this, "openSession"),
            array($this, "closeSession"),
            array($this, "readSession"),
            array($this, "writeSession"),
            array($this, "destroySession"),
            array($this, "gcSession")
        );
    }
    
    // ****************************************************************************
    public function openSession ($save_path, $session_name) {
        // if database connection exists
        if ($this->_db) {
            return true;
        }
        return false; 
    }
    
    // ****************************************************************************
    public function closeSession () {
        // if database connection is closed
        if($this->_db->close()){
            @session_write_close();
            return true;
        }
        return false; 
    }
    
    // ****************************************************************************
    public function readSession ($session_id) {
        $result = $this->_db->onSessionRead($session_id);
        //var_dump($result);
        
        return isset($result[0]) ? $result[0] : "";
    }
    
    // ****************************************************************************
    public function writeSession ($session_id, $session_data) {
        $result = $this->_db->onSessionRead($session_id);
        var_dump($result);
        if (!$result) {
            // create new record
            error_log('insert');
			$postData = array(
				'session_id' => $session_id,
				'user_data' => addslashes($session_data),
				'last_activity' => time(),
				'ip_address' => $_SERVER['REMOTE_ADDR'],
				'user_agent' => $_SERVER['HTTP_USER_AGENT']
			);
            
			$result = $this->_db->onSessionWrite('insert', $postData, $session_id);
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
			
			$result = $this->_db->onSessionWrite('update', $postData, $session_id);
        }
        
        return true;
    }
    
    // ****************************************************************************
    public function destroySession ($session_id) {
        error_log('destroy');
        $this->_db->onSessionDestroy($session_id);
        
        return TRUE;
    }
    
    // ****************************************************************************
    public function gcSession ($max_lifetime) {
        $count = $this->_db->onSessionGC($max_lifetime);
        
        return TRUE;
    }
    
// ****************************************************************************
} // end class
// ****************************************************************************
?>