<?php
/**
 * @file        AddCalltime.php
 * @brief       Add new calltime
 * @copyright   Copyright (C) GOautodial Inc.
 * @author      Alexander Jim Abenoja 
 *
 * @par <b>License</b>:
 *  This program is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU Affero General Public License as published by
 *  the Free Software Foundation, either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU Affero General Public License for more details.
 *
 *  You should have received a copy of the GNU Affero General Public License
 *  along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/
require_once(__DIR__ . '/APIHandler.php');
$api = \creamy\APIHandler::getInstance();

$start_default =	((string) $_POST['start_default'] !== '') ? date('Hi', strtotime($_POST['start_default'])) : "0";
$stop_default =		((string) $_POST['stop_default'] !== '') ? date('Hi', strtotime($_POST['stop_default'])) : "0";

$start_sunday =		((string) $_POST['start_sunday'] !== '') ? date('Hi', strtotime($_POST['start_sunday'])) : "0";
$stop_sunday =		((string) $_POST['stop_sunday'] !== '') ? date('Hi', strtotime($_POST['stop_sunday'])) : "0";

$start_monday =		((string) $_POST['start_monday'] !== '') ? date('Hi', strtotime($_POST['start_monday'])) : "0";
$stop_monday =		((string) $_POST['stop_monday'] !== '') ? date('Hi', strtotime($_POST['stop_monday'])) : "0";

$start_tuesday =	((string) $_POST['start_tuesday'] !== '') ? date('Hi', strtotime($_POST['start_tuesday'])) : "0";
$stop_tuesday =		((string) $_POST['stop_tuesday'] !== '') ? date('Hi', strtotime($_POST['stop_tuesday'])) : "0";

$start_wednesday =	((string) $_POST['start_wednesday'] !== '') ? date('Hi', strtotime($_POST['start_wednesday'])) : "0";
$stop_wednesday =	((string) $_POST['stop_wednesday'] !== '') ? date('Hi', strtotime($_POST['stop_wednesday'])) : "0";

$start_thursday =	((string) $_POST['start_thursday'] !== '') ? date('Hi', strtotime($_POST['start_thursday'])) : "0";
$stop_thursday =	((string) $_POST['stop_thursday'] !== '') ? date('Hi', strtotime($_POST['stop_thursday'])) : "0";

$start_friday =		((string) $_POST['start_friday'] !== '') ? date('Hi', strtotime($_POST['start_friday'])) : "0";
$stop_friday =		((string) $_POST['stop_friday'] !== '') ? date('Hi', strtotime($_POST['stop_friday'])) : "0";

$start_saturday =	((string) $_POST['start_saturday'] !== '') ? date('Hi', strtotime($_POST['start_saturday'])) : "0";
$stop_saturday =	((string) $_POST['stop_saturday'] !== '') ? date('Hi', strtotime($_POST['stop_saturday'])) : "0";

$postfields = [
        'goAction' => "goAddCalltime",
        'call_time_id' => $_POST['call_time_id'],
        'call_time_name' => $_POST['call_time_name'],
        'call_time_comments' => $_POST['call_time_comments'],
        'user_group' => $_POST['call_time_user_group'],
        'ct_default_start' => $start_default,
        'ct_default_stop' => $stop_default,
        'ct_sunday_start' => $start_sunday,
        'ct_sunday_stop' => $stop_sunday,
        'ct_monday_start' => $start_monday,
        'ct_monday_stop' => $stop_monday,
        'ct_tuesday_start' => $start_tuesday,
        'ct_tuesday_stop' => $stop_tuesday,
        'ct_wednesday_start' => $start_wednesday,
        'ct_wednesday_stop' => $stop_wednesday,
        'ct_thursday_start' => $start_thursday,
        'ct_thursday_stop' => $stop_thursday,
        'ct_friday_start' => $start_friday,
        'ct_friday_stop' => $stop_friday,
        'ct_saturday_start' => $start_saturday,
        'ct_saturday_stop' => $stop_saturday,
        'default_audio' => $_POST["audio_default"],
        'sunday_audio' => $_POST["audio_sunday"],
        'monday_audio' => $_POST["audio_monday"],
        'tuesday_audio' => $_POST["audio_tuesday"],
        'wednesday_audio' => $_POST["audio_wednesday"],
        'thursday_audio' => $_POST["audio_thursday"],
        'friday_audio' => $_POST["audio_friday"],
        'saturday_audio' => $_POST["audio_saturday"]
];

    $output = $api->API_addCalltime($postfields);

    if ($output->result=="success") {
           $status = 1;
           //$return['msg'] = "New User has been successfully saved.";
    } else {
           //$status = 0;
           // $return['msg'] = "Something went wrong please see input data on form.";
           $status = $output->result;
    }

    echo $status;
?>
