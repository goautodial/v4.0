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

require_once(dirname(dirname(dirname(__FILE__))).'/php/CRMDefaults.php');
require_once(dirname(dirname(dirname(__FILE__))).'/php/LanguageHandler.php');
require_once(dirname(dirname(dirname(__FILE__))).'/php/Config.php');
require_once(dirname(dirname(dirname(__FILE__))).'/php/DbHandler.php');

if (!isset($db)) { $db = new \creamy\DbHandler(); }
if (!isset($lh)) { $lh = \creamy\LanguageHandler::getInstance(); }

// get current timezone.
$timezone = $db->getTimezoneSetting();
if (!isset($timezone)) {
	if (defined("CRM_TIMEZONE")) $timezone = CRM_TIMEZONE;
}
if (isset($timezone)) { 
    ini_set('date.timezone', $timezone);
	date_default_timezone_set($timezone);
}

// try to store statistics for today	
$date = date('d-m-Y');
$result = $db->generateStatisticsForToday();
if ($result == false) { // if an error happened, try to send an email to the administrator.
	$adminMail = $this->getMainAdminEmail();
	if (isset($adminMail)) { 
		mail($adminMail, $lh->translationFor("error_storing_statistics").$date, $lh->translationFor("error_storing_statistics").$date); 
	}
}
?>