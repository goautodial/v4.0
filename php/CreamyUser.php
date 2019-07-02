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

class CreamyUser {
	protected $username;
	protected $userid;
	protected $userrole;
	protected $avatar;
	public function __construct() {
		// username
		if (isset($_SESSION["username"])) { $this->username = $_SESSION["username"]; }
		else { throw new \Exception("Unable to create current user. Session not set."); }
		// userid
		if (isset($_SESSION["userid"])) { $this->userid = $_SESSION["userid"]; }
		else { throw new \Exception("Unable to create current user. Session not set."); }
		// userrole
		if (isset($_SESSION["userrole"])) { $this->userrole = $_SESSION["userrole"]; }
		else { $this->userrole = CRM_DEFAULTS_USER_ROLE_GUEST; }
		// avatar
		if (isset($_SESSION["avatar"])) { $this->avatar = $_SESSION["avatar"]; }
		else { $this->avatar = CRM_DEFAULTS_USER_AVATAR; }
	}
	
	// Lifecycle
    public static function currentUser()
    {
        static $instance = null;
        if (null === $instance) {
            $instance = new static();
        }

        return $instance;
    }
		
	// user access functions
	
	public function getUserName() { return $this->username; }
	public function getUserId() { return $this->userid; }
	public function getUserRole() { return $this->userrole; }
	public function getUserAvatar() { return $this->avatar; }
	public function setUserRole($role) { $this->userrole = $role; $_SESSION["userrole"] = $role; }
	public function setUserName($name) { $this->username = $name; $_SESSION["username"] = $name; }
	public function setUserAvatar($avatar) { $this->avatar = $avatar; $_SESSION["avatar"] = $avatar; }

	public function userHasAdminPermission() {
		if ($this->userrole === CRM_DEFAULTS_USER_ROLE_ADMIN) return true;
		return false;
	}
	
	public function userHasManagerPermission() {
		if (($this->userrole === CRM_DEFAULTS_USER_ROLE_ADMIN) || ($this->userrole === CRM_DEFAULTS_USER_ROLE_MANAGER)) return true;
		return false;
	}
	
	public function userHasWritePermission() {
		if (($this->userrole === CRM_DEFAULTS_USER_ROLE_ADMIN) || ($this->userrole === CRM_DEFAULTS_USER_ROLE_MANAGER) || ($this->userrole === CRM_DEFAULTS_USER_ROLE_WRITER)) return true;
		return false;
	}
	
	public function userHasBasicPermission() {
		if (($this->userrole === CRM_DEFAULTS_USER_ROLE_ADMIN) || ($this->userrole === CRM_DEFAULTS_USER_ROLE_MANAGER) || ($this->userrole === CRM_DEFAULTS_USER_ROLE_WRITER) || ($this->userrole === CRM_DEFAULTS_USER_ROLE_READER)) return true;
		return false;
	}
}
?>