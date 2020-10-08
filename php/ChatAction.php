<?php
/**
 * @file        ChatAction.php
 * @brief       Handles WhatsApp Chat Actions
 * @copyright   Copyright (C) 2020 GOautodial Inc.
 * @author      Thom Bernarth Patacsil
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
require_once('APIHandler.php');
$api = \creamy\APIHandler::getInstance();

$action = '';
if(isset($_POST['action'])){
    $action = $_POST['action'];
}

$chat_message = '';
if(isset($_POST['chat_message'])){
    $chat_message = $_POST['chat_message'];
}

$to_user_id = '';
if(isset($_POST['to_user_id'])){
    $to_user_id = $_POST['to_user_id'];
}

$userid = '';
if(isset($_POST['userid'])){
    $userid = $_POST['userid'];
}

if($action == 'update_user_list') {
    $chatUsers = $api->API_chatUsers($userid);
    $data = array(
        "profileHTML" => $chatUsers,
    );
    echo json_encode($data);
}
if($action == 'insert_chat') {
    $api->API_insertChat($to_user_id, $userid, $chat_message);
}

if($action == 'show_chat') {
    //$api->API_showUserChat($userid, $to_user_id);
    $user = $api->API_getUserDetails($to_user_id);
    //$userSection = '<img src="userpics/'.$user->avatar.'" alt="" />'.
    $userSection = $user->username;

    $userChat = $api->API_getUserChat($userid, $to_user_id, $action);

    // Change Unread Messages to Read
    $api->API_editUserStatus($userid, $to_user_id, $action);
    $data = array(
	'userSection' => $userSection,
	'conversation' => $userChat->conversation
    );

    echo json_encode($data);
}

if($action == 'update_user_chat') {
    $userChat = $api->API_getUserChat($userid, $to_user_id, $action);

    // Change Loaded Value for Single Display of updates
    $api->API_editUserStatus($userid, $to_user_id, $action);
    $data = array(
    	"conversation" => $userChat->conversation
    );

    echo json_encode($data);
}

if($action == 'update_unread_message') {
    $count = $api->API_getUnreadMessageCount($to_user_id, $userid);
     // Change Unread Messages to Read
    $api->API_editUserStatus($userid, $to_user_id);
    $data = array(
    "count" => $count->countdata
    );
    echo json_encode($data);
}

if($action == 'update_message_status') {
    // Change Unread Messages to Read
    $api->API_editUserStatus($userid, $to_user_id);
}

if($action == 'update_typing_status') {
    $status = $api->API_updateTypingStatus($_POST["is_type"], $_SESSION["login_details_id"]);
    $data = array(
    "status" => $status
    );
    echo json_encode($data);
}

if($action == 'show_typing_status') {
    $message = $api->API_fetchIsTypeStatus($_POST['to_user_id']);
    $data = array(
    "message" => $message
    );
    echo json_encode($data);
}

?>

