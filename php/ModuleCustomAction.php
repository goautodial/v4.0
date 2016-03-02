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

require_once('CRMDefaults.php');
require_once('ModuleHandler.php');
require_once('Module.php');
include('Session.php');

$mh = \creamy\ModuleHandler::getInstance();

if (isset($_POST["module_name"]) && isset($_POST["hook_name"])) {
	// get main vars
	$modulename = $_POST["module_name"];
	$hookname = $_POST["hook_name"];
	
	// apply hook if module is enabled.
	if ($mh->moduleIsEnabled($modulename)) {
		// we don't include module_name in the parameters.
		unset($_POST["module_name"]);
		// apply the custom hook on the module.
		$result = $mh->applyHookOnModule($modulename, $hookname, $_POST);
		if (isset($result)) { ob_clean(); print $result; }
		else die("Unable to run $hookname on module $modulename");
	} else { die("Unable to process module action. Module $modulename is not enabled."); }
} else { die("Unable to process module action. Module name or method not found."); } 
?>