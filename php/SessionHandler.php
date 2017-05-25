<?php
namespace creamy;

// dependencies
require_once('CRMDefaults.php');
require_once('LanguageHandler.php');
require_once('DatabaseConnectorFactory.php');

class SessionHandler {
	/** Class version 
	 * @var float 
	 */
	public $version = '1.0';
	
	/** Session lifetime 
	 * @var integer 
	 */
	public $lifeTime = 1440;
	
	/** Session name 
	 * @var string 
	 */
	public $name = 'PHP_MYSQL_SESSION';
	
	/** Session storage table name 
	 * @var string 
	 */
	public $table = 'go_sessions';
	
	/** Encrypt session data 
	 * @var bool 
	 */
	private $encrypt = TRUE;
	
	/** Key with which the data will be encrypted 
	 * @var string 
	 */
	private $key = '6d2b9c9b61e761b8c786ba1134c76b9c';
	
	/** Database object
	 * @var object
	 */
	private $db;
	
	/** Reserved words for array to ( insert / update )
	 * @var array
	 */
	private $reserved = array('null', 'now()', 'curtime()', 'localtime()', 'localtime', 'utc_date()', 'utc_time()', 'utc_timestamp()');
	
	/** Constructor
	 * @param string 	$server 	- MySQL Host name or ( host:port )
	 * @param string 	$username 	- MySQL User
	 * @param string 	$password 	- MySQL Password
	 * @param string 	$db		 	- MySQL Database
	 * @param string 	$table 		- MySQL Table
	 * @param integer 	$lifeTime 	- Session lifetime
	 * @param bool	 	$encrypt 	- Encrypt session data 
	 */
	function __construct ($table = NULL, $lifeTime = 0, $encrypt = TRUE) {
		$this->db = \creamy\DatabaseConnectorFactory::getInstance()->getDatabaseConnectorOfType(CRM_DB_CONNECTOR_TYPE_MYSQL);
		
		$this->lifeTime = ($lifeTime === 0) ? CRM_SESSION_EXPIRATION : $lifeTime;
		
		// Session storage table
		$this->table = ($table == NULL) ? $this->table : $table;
		
		// Encrypt session data
		$this->encrypt = $encrypt;
		
		// Hook up handler
		session_set_save_handler(
			array(&$this, '_Open'),
			array(&$this, '_Close'),
			array(&$this, '_Read'),
			array(&$this, '_Write'),
			array(&$this, '_Destroy'),
			array(&$this, '_GC')
		);
		
		// Start session
		session_start();
	}
	
	function __destruct () {
		@session_write_close();
	}
	
	/** Create table for session storage
	 * @param void
	 */	
	//function createStorageTable() {
	//	return $this->db->query("CREATE TABLE IF NOT EXISTS `{$this->table}` ( `session_id` varchar(50) NOT NULL, `name` varchar(50) NOT NULL, `expires` int(10) unsigned NOT NULL DEFAULT '0', `data` text, `fingerprint` varchar(32) NOT NULL, PRIMARY KEY (`session_id`, `name`) ) ENGINE=InnoDB;");
	//}
	
	/** Initialize session
	 * @param string 	$save_path 	- Session save path (not in use!)
	 * @param string 	$name 		- Session name
	 */
	function _Open($save_path = NULL, $name) {
		// Session name
		$this->name = $name;
		//error_log('_Open');
		// Is connection OK
		return TRUE;
	}
	
	/** Close the session
	 * @param void
	 * @return bool
	 */
	function _Close() {
		//error_log('_Close');
		// Run the garbage collector in 15% of f. calls
		if (rand(1, 100) <= 15) $this->_GC();
		// Run the garbage collector for expired sessions
		$this->db->where('session_id', md5($session_id));
		$this->db->where('last_activity', time(), '<');
		$result = $this->db->get($this->table);
		//error_log($this->db->getRowCount());
		if ($this->db->getRowCount() > 0) {
			$this->_GC();
		}
		return TRUE;
	}
	
	/** Read session data
	 * @param string	$session_id - Session identifier]
	 * @return string
	 */
	function _Read($session_id) {
		// Read entry
		//error_log('_Read');
		$this->db->where('session_id', md5($session_id));
		//$this->db->where('user_agent', $this->getUserAgent());
		$this->db->where('ip_address', $this->getUserIP());
		$this->db->where('last_activity', time(), '>');
		$this->db->orderBy('last_activity', 'DESC');
		$result = $this->db->getOne($this->table, 'user_data');
		
		// Return data or null
		return ($this->db->getRowCount() > 0 && ($row = $result)) ? $this->encrypt ?  $this->decrypt($row['user_data']) : $row['user_data'] : NULL;
	}
	
	/** Initialize session
	 * @param string 	$session_id	- Session identifier
	 * @param string 	$data 		- Session data
	 */
	function _Write($session_id, $data) {
		//error_log('_Write');
		if (!empty($data)) {
			$insertData = array(
				'session_id' => md5($session_id),
				'user_agent' => $this->getUserAgent(),
				'last_activity' => time() + $this->lifeTime,
				'user_data' => $this->encrypt ? $this->encrypt($data) : $data,
				'ip_address' => $this->getUserIP()
			);
			
			$this->db->insert($this->table, $insertData);
			$err = $this->db->getLastError();
			
			if (preg_match("/^Duplicate entry/", $err)) {
				// Update Entry
				$updateData = array(
					'user_agent' => $this->getUserAgent(),
					'last_activity' => time() + $this->lifeTime,
					'user_data' => $this->encrypt ? $this->encrypt($data) : $data,
					'ip_address' => $this->getUserIP()
				);
				
				$this->db->where('session_id', md5($session_id));
				$this->db->update($this->table, $updateData);
			}
		}
		return TRUE;
	}

	/** Destroy session
	 * @param 	string 	$session_id	- Session identifier
	 * @return 	bool
	 */
	function _Destroy($session_id) {
		//error_log('_Destroy: '.$session_id);
		// Remove $session_id session
		$this->db->where('session_id', md5($session_id));
		$result = $this->db->delete($this->table);
		return ($result) ? TRUE : FALSE;
	}
	
	/** Garbage collector
	 * @param 	string 	$maxlifetime - Session max lifetime
	 * @return 	integer	- Affected rows
	 */
	function _GC($maxlifetime = 0) {
		//error_log('_GC');
		// Remove expired sessions 
		$this->db->where('last_activity', time(), '<');
		$result = $this->db->delete($this->table);
		return ($result) ? TRUE : FALSE;
	}
	
	/** Encrypt session data
     * @param 	string 	$data 	- Data to encrypt
     * @return 	string 	- Encrypted data
     */
    function encrypt($data) {
        return rtrim(base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_256, $this->key, $data, MCRYPT_MODE_ECB, mcrypt_create_iv(mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB), MCRYPT_RAND))), "\0");
    }
	
	/** Decrypt session data
     * @param 	string 	$data 	- Data to decrypt
     * @return 	string 	- Decrypted data
     */
    function decrypt($data) {
        return rtrim(mcrypt_decrypt(MCRYPT_RIJNDAEL_256, $this->key, base64_decode($data), MCRYPT_MODE_ECB, mcrypt_create_iv(mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB), MCRYPT_RAND)), "\0");
    }
	
	/** Returns "digital fingerprint" of user
     * @param 	void
     * @return 	string 	- MD5 hashed data
     */
	function fingerprint() {
		return md5(implode('|', array($_SERVER['REMOTE_ADDR'], $_SERVER['HTTP_USER_AGENT'], $_SERVER['HTTP_ACCEPT'], $_SERVER['HTTP_ACCEPT_ENCODING'], $_SERVER['HTTP_ACCEPT_LANGUAGE'])));
	}
	
	function getUserIP() {
		return $_SERVER['REMOTE_ADDR'];
	}
	
	function getUserAgent() {
		return $_SERVER['HTTP_USER_AGENT'];
	}
	
// ****************************************************************************
} // end class
// ****************************************************************************
?>