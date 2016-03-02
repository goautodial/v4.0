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

/**
 * The job scheduler takes care of executing periodic tasks in four categories:
 * - hourly: tasks that should be executed every hour, in dir hourly.
 * - hourly: tasks that should be executed every hour, in dir hourly.
 * - hourly: tasks that should be executed every hour, in dir hourly.
 * - hourly: tasks that should be executed every hour, in dir hourly.
 * The job scheduler must be invoked from a cronjob or any other scheduling application, every hour. 
 */

chdir(dirname(__FILE__));
require_once(dirname(__FILE__).'/php/CRMDefaults.php');
require_once(dirname(__FILE__).'/php/DbHandler.php');
require_once(dirname(__FILE__).'/php/ModuleHandler.php');

define ('CRM_JOB_SCHEDULER_BASE_DIR', 'jobs');
define ('CRM_JOB_SCHEDULER_HOURLY_DIR', 'hourly');
define ('CRM_JOB_SCHEDULER_DAILY_DIR', 'daily');
define ('CRM_JOB_SCHEDULER_WEEKLY_DIR', 'weekly');
define ('CRM_JOB_SCHEDULER_MONTHLY_DIR', 'monthly');

/**
 * This function will analyze the given directory and execute any tasks
 * residing there. Valid tasks are those php file not starting with ".".
 */
function scheduleJobsInDirectory($dir) {
	$basedir = dirname(__FILE__).DIRECTORY_SEPARATOR.CRM_JOB_SCHEDULER_BASE_DIR.DIRECTORY_SEPARATOR.$dir;
	$files = scandir($basedir);
	foreach ($files as $filename) { // iterate throuhg files/directories.
		$realpath = $basedir.DIRECTORY_SEPARATOR.$filename;
		// If it's a directory (except for "." & "..")
		if (is_file($realpath) && (substr($filename, 0, 1 ) !== '.' )) { // possible script.
			include($realpath);
		}
	}
}


/** Schedule tasks for all frequencies */
// DDBB handler.
$db = new \creamy\DbHandler();
// Module handler
$mh = \creamy\ModuleHandler::getInstance();

// 1. Run the system scheduled jobs.
$scheduleMinFreq = $db->getSettingValueForKey(CRM_SETTING_JOB_SCHEDULING_MIN_FREQ);
if (empty($scheduleMinFreq)) { $scheduleMinFreq = CRM_JOB_SCHEDULING_HOURLY; }

// 1.a Hourly tasks
if ($scheduleMinFreq <= CRM_JOB_SCHEDULING_HOURLY) {
	scheduleJobsInDirectory(CRM_JOB_SCHEDULER_HOURLY_DIR);
	// invoke the job scheduling in modules.
	$mh->scheduleJobsOnActiveModules(CRM_JOB_SCHEDULING_HOURLY);
}

// 1.b Daily tasks
if ($scheduleMinFreq <= CRM_JOB_SCHEDULING_DAILY) {
	$lastDailySchedule = $db->getSettingValueForKey(CRM_SETTING_JOB_LAST_DAY);
	$scheduleNow = false;
	if (empty($lastDailySchedule)) { $scheduleNow = true; } // if we have no previous last day schedule date, schedule daily tasks now.
	else {
		$timestamp = strtotime($lastDailySchedule);
		$nextPeriod = strtotime("+1 day", $timestamp);
		if (time() >= $nextPeriod) { $scheduleNow = true;}
	}
	// schedule now?
	if ($scheduleNow) {
		// save new scheduling date.
		$db->setSettingValueForKey(CRM_SETTING_JOB_LAST_DAY, date("Y-m-d H:i:s"));
		// schedule tasks
		scheduleJobsInDirectory(CRM_JOB_SCHEDULER_DAILY_DIR);
		// invoke the job scheduling in modules.
		$mh->scheduleJobsOnActiveModules(CRM_JOB_SCHEDULING_DAILY);
	}
}

// 1.c Weekly tasks
if ($scheduleMinFreq <= CRM_JOB_SCHEDULING_WEEKLY) {
	$lastWeeklySchedule = $db->getSettingValueForKey(CRM_SETTING_JOB_LAST_WEEK);
	$scheduleNow = false;
	if (empty($lastWeeklySchedule)) { $scheduleNow = true; } // if we have no previous last day schedule date, schedule daily tasks now.
	else {
		$timestamp = strtotime($lastWeeklySchedule);
		$nextPeriod = strtotime("+1 week", $timestamp);
		if (time() >= $nextPeriod) { $scheduleNow = true;}
	}
	// schedule now?
	if ($scheduleNow) {
		// save new scheduling date.
		$db->setSettingValueForKey(CRM_SETTING_JOB_LAST_WEEK, date("Y-m-d H:i:s"));
		// schedule tasks
		scheduleJobsInDirectory(CRM_JOB_SCHEDULER_WEEKLY_DIR);
		// invoke the job scheduling in modules.
		$mh->scheduleJobsOnActiveModules(CRM_JOB_SCHEDULING_WEEKLY);
	}
}

// 1.d Monthly tasks
if ($scheduleMinFreq <= CRM_JOB_SCHEDULING_MONTHLY) {
	$lastMonthlySchedule = $db->getSettingValueForKey(CRM_SETTING_JOB_LAST_MONTH);
	$scheduleNow = false;
	if (empty($lastMonthlySchedule)) { $scheduleNow = true; } // if we have no previous last day schedule date, schedule daily tasks now.
	else {
		$timestamp = strtotime($lastMonthlySchedule);
		$nextPeriod = strtotime("+1 month", $timestamp);
		if (time() >= $nextPeriod) { $scheduleNow = true;}
	}
	// schedule now?
	if ($scheduleNow) {
		// save new scheduling date.
		$db->setSettingValueForKey(CRM_SETTING_JOB_LAST_MONTH, date("Y-m-d H:i:s"));
		// schedule tasks
		scheduleJobsInDirectory(CRM_JOB_SCHEDULER_MONTHLY_DIR);
		// invoke the job scheduling in modules.
		$mh->scheduleJobsOnActiveModules(CRM_JOB_SCHEDULING_MONTHLY);
	}
}

?>
