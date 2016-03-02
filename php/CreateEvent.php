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

require_once('DbHandler.php');
require_once('CRMDefaults.php');
require_once('LanguageHandler.php');
require_once('Session.php');

$lh = \creamy\LanguageHandler::getInstance();
$user = \creamy\CreamyUser::currentUser();

// check required fields
$validated = 1;
if (!isset($_POST["title"])) { // do we have a title?
	// if not, we do need at least some customer data...
	if (!isset($_POST["customerid"])) $validated = 0;
	else if (!isset($_POST["customer_type"])) $validated = 0;
} 

if ($validated == 1) {
	$db = new \creamy\DbHandler();

	// check if this is a new customer type of event.
	if (isset($_POST["customerid"]) && isset($_POST["customer_type"])) {
		$customerid = $_POST["customerid"];
		$customertype = $_POST["customer_type"];
		$result = $db->createContactEventForCustomer($user->getUserId(), $customerid, $customertype, true, null, null, null);
	} else { // normal event
		$title = $_POST["title"];
		$color = (isset($_POST["color"])) ? $_POST["color"] : CRM_UI_COLOR_DEFAULT_HEX;
		error_log("Creando evento con color $color");
		$result = $db->createEvent($user->getUserId(), $title, $color, true, null, null, null);
	}
	// return result	
	ob_clean();
	print "$result"; 
} else { ob_clean(); print "0"; }
?>