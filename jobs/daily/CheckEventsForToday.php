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
require_once(dirname(dirname(dirname(__FILE__))).'/php/Config.php');
require_once(dirname(dirname(dirname(__FILE__))).'/php/DbHandler.php');

/** 
 * Checks the events for today and sends an email to users with pending events for
 * today. This behaviour can be enabled/disabled by means of the setting property
 * notification_email_events (1=yes, 0=no).
 */
// initialize vars.
if (!isset($db)) { $db = new \creamy\DbHandler(); }
// check if notifications for events is enabled.
$enabled = $db->getNotificationsForEventsSetting();

if (filter_var($enabled, FILTER_VALIDATE_BOOLEAN)) { // if email notifications for events is enabled...
	require_once(dirname(dirname(dirname(__FILE__))).'/php/MailHandler.php');
	if (!isset($mh)) { $mh = \creamy\MailHandler::getInstance(); }
	$events = $db->getEventsForTodayForAllUsers(true);
	// error_log("Events: ".var_export($events, true));
	if (!empty($events)) {
		foreach ($events as $event) { // for every event...
			// send a new notification
			if ($mh->sendNewEventMailToUser($event)) {
				$db->setEventAsNotified($event["id"]); // set event as notified in DDBB.
			}
			
		}
	}
}
?>