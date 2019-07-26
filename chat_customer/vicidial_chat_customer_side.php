<?php
# vicidial_chat_customer_side.php
#
# Copyright (C) 2016  Joe Johnson, Matt Florell <vicidial@gmail.com>    LICENSE: AGPLv2
#
# The main page of the customer chat interface.  This will display a form for the customer
# to fill out to attempt to initiate a chat with an available agent in the in-group 
# ("department") of their choice.  
#
# Builds:
# 150902-2100 - First build
# 151212-0830 - First Build for customer chat
# 151213-1105 - Added variable filtering
# 151217-1017 - Allow for pre-populating of group_id
# 151219-0850 - Added translation code
# 151220-0959 - Only search for phone number if greater than 4 digits
# 160108-1700 - Added available_agents, status_link button options and validation of active in-group
# 160120-1925 - Fixed missing list_id on vicidial_list inserts, Issue #915. Added show_email option
# 160203-1052 - Added display of chat message after ending it
# 160805-2315 - Added coding to show logos in customer display
#

require("dbconnect_mysqli.php");
require("functions.php");

if (isset($_GET["first_name"]))				{$first_name=$_GET["first_name"];}
	elseif (isset($_POST["first_name"]))	{$first_name=$_POST["first_name"];}
if (isset($_GET["last_name"]))				{$last_name=$_GET["last_name"];}
	elseif (isset($_POST["last_name"]))		{$last_name=$_POST["last_name"];}
if (isset($_GET["group_id"]))				{$group_id=$_GET["group_id"];}
	elseif (isset($_POST["group_id"]))		{$group_id=$_POST["group_id"];}
if (isset($_GET["phone_number"]))			{$phone_number=$_GET["phone_number"];}
	elseif (isset($_POST["phone_number"]))	{$phone_number=$_POST["phone_number"];}
if (isset($_GET["send_request"]))			{$send_request=$_GET["send_request"];}
	elseif (isset($_POST["send_request"]))	{$send_request=$_POST["send_request"];}
if (isset($_GET["email"]))					{$email=$_GET["email"];}
	elseif (isset($_POST["email"]))			{$email=$_POST["email"];}
if (isset($_GET["join_chat"]))				{$join_chat=$_GET["join_chat"];}
	elseif (isset($_POST["join_chat"]))		{$join_chat=$_POST["join_chat"];}
if (isset($_GET["chat_id"]))				{$chat_id=$_GET["chat_id"];}
	elseif (isset($_POST["chat_id"]))		{$chat_id=$_POST["chat_id"];}
if (isset($_GET["lead_id"]))				{$lead_id=$_GET["lead_id"];}
	elseif (isset($_POST["lead_id"]))		{$lead_id=$_POST["lead_id"];}
if (isset($_GET["user"]))					{$user=$_GET["user"];}
	elseif (isset($_POST["user"]))			{$user=$_POST["user"];}
if (isset($_GET["language"]))				{$language=$_GET["language"];}
	elseif (isset($_POST["language"]))		{$language=$_POST["language"];}
if (isset($_GET["stage"]))					{$stage=$_GET["stage"];}
	elseif (isset($_POST["stage"]))			{$stage=$_POST["stage"];}
if (isset($_GET["available_agents"]))			{$available_agents=$_GET["available_agents"];}
	elseif (isset($_POST["available_agents"]))	{$available_agents=$_POST["available_agents"];}
if (isset($_GET["status_link"]))			{$status_link=$_GET["status_link"];}
	elseif (isset($_POST["status_link"]))	{$status_link=$_POST["status_link"];}
if (isset($_GET["show_email"]))				{$show_email=$_GET["show_email"];}
	elseif (isset($_POST["show_email"]))	{$show_email=$_POST["show_email"];}
$PHP_SELF=$_SERVER["PHP_SELF"];

$lead_id = preg_replace("/[^0-9]/","",$lead_id);
$chat_id = preg_replace('/[^- \_\.0-9a-zA-Z]/','',$chat_id);
$group_id = preg_replace('/[^- \_0-9a-zA-Z]/','',$group_id);
$language = preg_replace('/[^-\_0-9a-zA-Z]/','',$language);
$available_agents = preg_replace('/[^-\_0-9a-zA-Z]/','',$available_agents);
$status_link = preg_replace('/[^-\_0-9a-zA-Z]/','',$status_link);
$show_email = preg_replace('/[^-\_0-9a-zA-Z]/','',$show_email);

if ($non_latin < 1)
	{
	$user = preg_replace('/[^- \'\+\_\.0-9a-zA-Z]/','',$user);
	$first_name = preg_replace('/[^- \'\+\_\.0-9a-zA-Z]/','',$first_name);
	$first_name = preg_replace('/\+/',' ',$first_name);
	$last_name = preg_replace('/[^- \'\+\_\.0-9a-zA-Z]/','',$last_name);
	$last_name = preg_replace('/\+/',' ',$last_name);
	$email = preg_replace('/[^- \'\+\.\:\/\@\%\_0-9a-zA-Z]/','',$email);
	$email = preg_replace('/\+/',' ',$email);
	$phone_number = preg_replace("/[^0-9]/","",$phone_number);
	}
else
	{
	$user = preg_replace("/\'|\"|\\\\|;/","",$user);
	$first_name = preg_replace("/\"|\\\\|;/","",$first_name);
	$last_name = preg_replace("/\"|\\\\|;/","",$last_name);
	$email = preg_replace("/\'|\"|\\\\|;/","",$email);
	$phone_number = preg_replace('/[^- \'\+\.\:\/\@\%\_0-9a-zA-Z]/','',$email);
	}

#############################################
##### START SYSTEM_SETTINGS LOOKUP #####
$VUselected_language='';
$stmt = "SELECT use_non_latin,enable_languages,language_method,default_language,allow_chats,chat_url FROM system_settings;";
$rslt=mysql_to_mysqli($stmt, $link);
        if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'00XXX',$user,$server_ip,$session_name,$one_mysql_log);}
if ($DB) {echo "$stmt\n";}
$qm_conf_ct = mysqli_num_rows($rslt);
if ($qm_conf_ct > 0)
	{
	$row=mysqli_fetch_row($rslt);
	$non_latin =			$row[0];
	$SSenable_languages =	$row[1];
	$SSlanguage_method =	$row[2];
	$SSdefault_language =	$row[3];
	$SSallow_chats =		$row[4];
	$SSchat_url =			$row[5];
	}
$VUselected_language = $SSdefault_language;
##### END SETTINGS LOOKUP #####
###########################################

if (strlen($language) > 1)
	{
	$stmt = "SELECT language_code,language_description FROM vicidial_languages where language_id='$language' and active='Y';";
	$rslt=mysql_to_mysqli($stmt, $link);
			if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'00XXX',$user,$server_ip,$session_name,$one_mysql_log);}
	if ($DB) {echo "$stmt\n";}
	$lang_good_ct = mysqli_num_rows($rslt);
	if ($lang_good_ct > 0)
		{
		$row=mysqli_fetch_row($rslt);
		$language_code =		$row[0];
		$language_description =	$row[1];
		$VUselected_language = $language;
		}
	}
if ($SSallow_chats < 1)
	{
	header ("Content-type: text/html; charset=utf-8");
	if ($status_link == 'Y')
		{echo "<img src=\"./images/"._QXZ("chat_status_button_OFF.gif")."\" width=150 height=37 alt=\""._QXZ("no chat agents available")."\">";}
	else
		{echo _QXZ("Error, chat disabled on this system");}
	exit;
	}
if (strlen($group_id) > 1)
	{
	$group_active='N';
	$group_name='';
	$stmt = "SELECT active,group_name FROM vicidial_inbound_groups where group_id='$group_id';";
	$rslt=mysql_to_mysqli($stmt, $link);
			if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'00XXX',$user,$server_ip,$session_name,$one_mysql_log);}
	if ($DB) {echo "$stmt\n";}
	$group_good_ct = mysqli_num_rows($rslt);
	if ($group_good_ct > 0)
		{
		$row=mysqli_fetch_row($rslt);
		$group_active =		$row[0];
		$group_name =		$row[1];
		}

	if ($group_active == 'N')
		{
		header ("Content-type: text/html; charset=utf-8");
		if ($status_link == 'Y')
			{echo "<img src=\"./images/"._QXZ("chat_status_button_OFF.gif")."\" width=150 height=37 alt=\""._QXZ("no chat agents available")."\">";}
		else
			{echo _QXZ("Error, group is not active").": $group_id $group_name";}
		exit;
		}

	if ($available_agents == 'WAITING_ONLY')
		{
		$waiting_agents=0;
		$stmt = "SELECT count(*) FROM vicidial_live_agents where closer_campaigns LIKE \"% $group_id %\" and status IN('READY','CLOSER');";
		$rslt=mysql_to_mysqli($stmt, $link);
				if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'00XXX',$user,$server_ip,$session_name,$one_mysql_log);}
		if ($DB) {echo "$stmt\n";}
		$group_good_ct = mysqli_num_rows($rslt);
		if ($group_good_ct > 0)
			{
			$row=mysqli_fetch_row($rslt);
			$waiting_agents = $row[0];
			}
		if ($waiting_agents < 1)
			{
			header ("Content-type: text/html; charset=utf-8");
			if ($status_link == 'Y')
				{echo "<img src=\"./images/"._QXZ("chat_status_button_OFF.gif")."\" width=150 height=37 alt=\""._QXZ("no chat agents available")."\">";}
			else
				{echo _QXZ("We are sorry, there are no waiting agents at this time. Please try again later.").": $group_id $group_name";}
			exit;
			}
		}
	if ($available_agents == 'LOGGED_IN')
		{
		$loggedin_agents=0;
		$stmt = "SELECT count(*) FROM vicidial_live_agents where closer_campaigns LIKE \"% $group_id %\";";
		$rslt=mysql_to_mysqli($stmt, $link);
				if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'00XXX',$user,$server_ip,$session_name,$one_mysql_log);}
		if ($DB) {echo "$stmt\n";}
		$group_good_ct = mysqli_num_rows($rslt);
		if ($group_good_ct > 0)
			{
			$row=mysqli_fetch_row($rslt);
			$loggedin_agents = $row[0];
			}
		if ($loggedin_agents < 1)
			{
			header ("Content-type: text/html; charset=utf-8");
			if ($status_link == 'Y')
				{echo "<img src=\"./images/"._QXZ("chat_status_button_OFF.gif")."\" width=150 height=37 alt=\""._QXZ("no chat agents available")."\">";}
			else
				{echo _QXZ("We are sorry, there are no logged in agents at this time. Please try again later.").": $group_id $group_name";}
			exit;
			}
		}

	if ($status_link == 'Y')
		{
		echo "<a href=\"".$PHP_SELF."?group_id=$group_id&language=$language&available_agents=$available_agents&show_email=$show_email\"><img src=\"./images/"._QXZ("chat_status_button_ON.gif")."\" width=150 height=37 border=0 alt=\""._QXZ("Agents Available, click to chat now")."\"></a>";
		exit;
		}
	}



# http://192.168.1.2/chat_customer/vicidial_chat_customer_side.php?user=1440723435.60926&lead_id=1079350&group_id=CHAT_TEST_GROUP&chat_id=254&email=joej%40test.com
$phone_number=preg_replace('/[^0-9]/', '', $phone_number);

if ($stage == "join_chat") { # For people invited to an existing chat from an agent.
	$error_msg="";

	if (!$first_name || !$last_name) {
		$error_msg=_QXZ("Please enter your first and last name");
	} else { 
		$chat_member_name="$first_name $last_name";
		$ip_address=$_SERVER['REMOTE_ADDR'];
		$user=time().".".rand(10000,99999);

		# SEARCH FOR CUSTOMER IN DATABASE - IF NOT FOUND BY PHONE OR IP ADDRESS MAKE A NEW USER
		if ($lead_id) { # CAN OCCUR VIA INVITE
			$upd_stmt="UPDATE vicidial_list set first_name=\"$first_name\", last_name=\"$last_name\", status='WCHAT' where lead_id='$lead_id' limit 1";
			# update email and security_phrase?
			$upd_rslt=mysql_to_mysqli($upd_stmt, $link);

			# Private message
			$alert_stmt="select chat_creator from vicidial_live_chats where chat_id='$chat_id' and lead_id='$lead_id'";
			$alert_rslt=mysql_to_mysqli($alert_stmt, $link);
			if (mysqli_num_rows($alert_rslt)>0) {
				$alert_row=mysqli_fetch_row($alert_rslt);
				$chat_creator=$alert_row[0];
				$ins_alert_stmt="INSERT INTO vicidial_chat_log(poster, chat_member_name, message_time, message, chat_id, chat_level) select '" . mysqli_real_escape_string($link, $chat_creator) . "', full_name, now(), '" . mysqli_real_escape_string($link, $chat_member_name) . " has joined chat', '" . mysqli_real_escape_string($link, $chat_id) . "', '1' from vicidial_users where user='" . mysqli_real_escape_string($link, $chat_creator) . "'";
				$ins_alert_rslt=mysql_to_mysqli($ins_alert_stmt, $link);
			}
		} else {
			if (strlen($phone_number) > 4) {
				$stmt="SELECT lead_id from vicidial_list where phone_number='$phone_number' order by entry_date desc limit 1;";
				$rslt=mysql_to_mysqli($stmt, $link);
			} else if ($email) {
				$stmt="SELECT lead_id from vicidial_list where email='$email' order by entry_date desc limit 1;";
				$rslt=mysql_to_mysqli($stmt, $link);
			} else {
				$stmt="SELECT lead_id from vicidial_list where email like \"%".$ip_address."%\" order by entry_date desc limit 1;";
				$rslt=mysql_to_mysqli($stmt, $link);
			}

			if (mysqli_num_rows($rslt)>0) {
				$row=mysqli_fetch_row($rslt);
				$lead_id=$row[0];

				# Update to reflect chat request - should I do this?  And use WCHAT status for "waiting for chat"?
				$upd_stmt="UPDATE vicidial_list set first_name=\"$first_name\", last_name=\"$last_name\", status='WCHAT' where lead_id='$lead_id' limit 1";
				# update email and security_phrase?
				$upd_rslt=mysql_to_mysqli($upd_stmt, $link);
			} else if (!$error_msg) {
				$stmtA = "SELECT hold_time_option_callback_list_id FROM vicidial_inbound_groups where group_id='" . mysqli_real_escape_string($link, $group_id) . "';";
				$rsltA=mysql_to_mysqli($stmtA, $link);
				if ($DB) {echo "$stmtA\n";}
				$list_ct = mysqli_num_rows($rsltA);
				if ($list_ct > 0)
					{
					$row=mysqli_fetch_row($rsltA);
					$default_list_id =	$row[0];
					}
				# Create lead in vicidial_list table (make special system status for waiting for chat)
				$ins_stmt="INSERT INTO vicidial_list(status, first_name, last_name, email, list_id, phone_number, security_phrase, entry_date) VALUES('WCHAT', '" . mysqli_real_escape_string($link, $first_name) . "', '" . mysqli_real_escape_string($link, $last_name) . "', '" . mysqli_real_escape_string($link, $email) . "', '" . mysqli_real_escape_string($link, $default_list_id) . "', '" . mysqli_real_escape_string($link, $phone_number) . "', '" . mysqli_real_escape_string($link, $group_id) . "',NOW())";
				$ins_rslt=mysql_to_mysqli($ins_stmt, $link);
				$lead_id=mysqli_insert_id($link);
			}
		}

		if (!$lead_id || $lead_id==0) {
			$error_msg.="<BR>\n"._QXZ("Could not find or create dialer entry");
		} else {
			$chat_upd_stmt="UPDATE vicidial_live_chats set lead_id='$lead_id' where chat_id='$chat_id'";
			$chat_upd_rslt=mysql_to_mysqli($chat_upd_stmt, $link);

		}
	}

	if (!$error_msg) {
		if ($chat_id>0) {

			$ins_stmt="INSERT INTO vicidial_chat_participants(chat_id, chat_member, chat_member_name, ping_date, vd_agent) VALUES('" . mysqli_real_escape_string($link, $chat_id) . "', '" . mysqli_real_escape_string($link, $user) . "', '" . mysqli_real_escape_string($link, $chat_member_name) . "', now(), 'N')";
			$ins_rslt=mysql_to_mysqli($ins_stmt, $link);
			if (mysqli_affected_rows($link)==0) {
				$del_stmt="DELETE from vicidial_live_chats where chat_id='$chat_id'";
				$del_rslt=mysql_to_mysqli($del_stmt, $link);
				$error_msg=_QXZ("Chat started, failure to join"); 
				unset($chat_id);
			} else {
				# echo "$chat_id|$lead_id|$ins_stmt";
			}
		} else {
			$error_msg=_QXZ("Chat not started")." - $ins_stmt";
		}
	}
}

if ($stage == 'send_request') { # For people requesting a chat with an agent; consider using a special user variable name for this

	$error_msg="";

	if (!$first_name || !$last_name) {$error_msg=_QXZ("Please enter your first and last name");}

	$chat_member_name="$first_name $last_name";
	$ip_address=$_SERVER['REMOTE_ADDR'];
	$user=time().".".rand(10000,99999);

	# SEARCH FOR CUSTOMER IN DATABASE - IF NOT FOUND BY PHONE OR IP ADDRESS MAKE A NEW USER
	if (strlen($phone_number) > 4) {
		$stmt="SELECT lead_id from vicidial_list where phone_number='$phone_number' order by entry_date desc limit 1;";
		$rslt=mysql_to_mysqli($stmt, $link);
	} else if ($email) {
		$stmt="SELECT lead_id from vicidial_list where email='$email' order by entry_date desc limit 1;";
		$rslt=mysql_to_mysqli($stmt, $link);
	} else {
		$stmt="SELECT lead_id from vicidial_list where email like \"%".$ip_address."%\" order by entry_date desc limit 1;";
		$rslt=mysql_to_mysqli($stmt, $link);
	}
	if (strlen($email)<1)
		{$email = $ip_address;}

	if (mysqli_num_rows($rslt)>0) {
		$row=mysqli_fetch_row($rslt);
		$lead_id=$row[0];

		# Update to reflect chat request - should I do this?  And use WCHAT status for "waiting for chat"?
		$upd_stmt="UPDATE vicidial_list set first_name='" . mysqli_real_escape_string($link, $first_name) . "', last_name='" . mysqli_real_escape_string($link, $last_name) . "', status='WCHAT' where lead_id='$lead_id' limit 1";
		# update email and security_phrase?
		$upd_rslt=mysql_to_mysqli($upd_stmt, $link);
	} else if (!$error_msg) {
		$stmtA = "SELECT hold_time_option_callback_list_id FROM vicidial_inbound_groups where group_id='" . mysqli_real_escape_string($link, $group_id) . "';";
		$rsltA=mysql_to_mysqli($stmtA, $link);
		if ($DB) {echo "$stmtA\n";}
		$list_ct = mysqli_num_rows($rsltA);
		if ($list_ct > 0)
			{
			$row=mysqli_fetch_row($rsltA);
			$default_list_id =	$row[0];
			}
		# Create lead in vicidial_list table (make special system status for waiting for chat)
		$ins_stmt="INSERT INTO vicidial_list(status, first_name, last_name, email, list_id, phone_number, security_phrase, entry_date) VALUES('WCHAT', '" . mysqli_real_escape_string($link, $first_name) . "', '" . mysqli_real_escape_string($link, $last_name) . "', '" . mysqli_real_escape_string($link, $email) . "', '" . mysqli_real_escape_string($link, $default_list_id) . "', '" . mysqli_real_escape_string($link, $phone_number) . "', '" . mysqli_real_escape_string($link, $group_id) . "',NOW())";
		$ins_rslt=mysql_to_mysqli($ins_stmt, $link);
		$lead_id=mysqli_insert_id($link);
	}

	if (!$lead_id || $lead_id==0) {
		$error_msg.="<BR>\n"._QXZ("Could not find or create dialer entry");
	}

	if (!$error_msg) {
		$ins_stmt="INSERT INTO vicidial_live_chats(status, chat_creator, group_id, lead_id, chat_start_time) VALUES('WAITING', 'NONE', '" . mysqli_real_escape_string($link, $group_id) . "', '" . mysqli_real_escape_string($link, $lead_id) . "', now())";
		$ins_rslt=mysql_to_mysqli($ins_stmt, $link);
		$chat_id=mysqli_insert_id($link);	

		# WEB LOGO
		$chat_color_stmt="select web_logo from vicidial_inbound_groups vig, vicidial_screen_colors v where vig.group_id='$group_id' and vig.customer_chat_screen_colors=v.colors_id limit 1;";
		$color_rslt=mysql_to_mysqli($chat_color_stmt, $link);
		$web_logo=""; $filepath="vicidial/images";
		if(mysqli_num_rows($color_rslt)>0) {
			$color_row=mysqli_fetch_array($color_rslt);
			switch ($color_row["web_logo"]) {
				case "default_new":
					$color_row["web_logo"]=".png";
					break;
				case "default_old";
					$color_row["web_logo"]=".gif";
					$filepath="vicidial";
					break;			
			}
			$web_logo=$color_row["web_logo"];
		}
		if (!preg_match("/\.(jpg|gif|png|bmp)$/", $web_logo)) {$web_logo.=".png";}

		
		if ($chat_id>0) {

			$survey_stmt="select customer_chat_survey_link, customer_chat_survey_text from vicidial_inbound_groups where group_id='$group_id'";
			$survey_rslt=mysql_to_mysqli($survey_stmt, $link);
			$survey_row=mysqli_fetch_array($survey_rslt);
			if (strlen($survey_row["customer_chat_survey_link"])>0) {
				$survey_str="<BR/><BR/><font class='chat_title'><a href='".$survey_row["customer_chat_survey_link"]."' target='_parent'>";
				if (strlen($survey_row["customer_chat_survey_text"])>0) {
					$survey_str.=$survey_row["customer_chat_survey_text"];
				} else {
					$survey_str.=_QXZ("PLEASE TAKE OUR SURVEY");
				}
				$survey_str.="</a>";
			}


			$ins_stmt="INSERT INTO vicidial_chat_participants(chat_id, chat_member, chat_member_name, ping_date, vd_agent) VALUES('$chat_id', '" . mysqli_real_escape_string($link, $user) . "', '" . mysqli_real_escape_string($link, $chat_member_name) . "', now(), 'N')";
			$ins_rslt=mysql_to_mysqli($ins_stmt, $link);
			if (mysqli_affected_rows($link)==0) {
				$del_stmt="DELETE from vicidial_live_chats where chat_id='$chat_id'";
				$del_rslt=mysql_to_mysqli($del_stmt, $link);
				$error_msg=_QXZ("Chat started, failure to join"); 
				unset($chat_id);
			} else {
				# echo "$chat_id|$lead_id|$ins_stmt";
			}
		} else {
			$error_msg=_QXZ("Chat not started")." - $ins_stmt";
		}
	}
}

header ("Content-type: text/html; charset=utf-8");
header ("Cache-Control: no-cache, must-revalidate");  // HTTP/1.1
header ("Pragma: no-cache");                          // HTTP/1.0
echo '<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
';
?>
<html>
<title><?php echo $error_msg; ?></title>
<head>
<link rel="stylesheet" type="text/css" href="css/style.css" />
<link rel="stylesheet" type="text/css" href="css/custom.css" />
<link rel="stylesheet" type="text/css" href="css/simpletree.css" />

<script language="Javascript">
var language='<?php echo $language ?>';
var chat_url='<?php echo $SSchat_url ?>';
var group_id='<?php echo $group_id ?>';
var available_agents='<?php echo $available_agents ?>';
var show_email='<?php echo $show_email ?>';

function PleaseWait() {
	document.getElementById('chat_request_span').style.display="none";
	document.getElementById('please_wait_span').style.display="block";
	RequestChat();
}
function ResetScreen() {
	document.getElementById('chat_request_span').style.display="block";
	document.getElementById('please_wait_span').style.display="none";
}
function LeaveChat(chat_id, user, chat_member_name) {
	if (!chat_id)
		{
		var chat_id=document.getElementById('chat_id').value;
		}
	if (!user)
		{
		var user=document.getElementById('user').value;
		}
	if (!chat_member_name)
		{
		var chat_member_name=document.getElementById('chat_member_name').value;
		}
	
	var xmlhttp=false;
	if (!xmlhttp && typeof XMLHttpRequest!='undefined')
		{
		xmlhttp = new XMLHttpRequest();
		}
	if (xmlhttp) 
		{ 
		chat_query = "&action=leave_chat&chat_id="+chat_id+"&group_id="+group_id+"&user="+user+"&chat_member_name="+chat_member_name+"&language="+language+"&available_agents="+available_agents+"&show_email="+show_email;
		// alert(chat_query);
		xmlhttp.open('POST', 'customer_chat_functions.php'); 
		xmlhttp.setRequestHeader('Content-Type','application/x-www-form-urlencoded; charset=UTF-8');
		xmlhttp.send(chat_query); 
		xmlhttp.onreadystatechange = function() 
			{ 
			if (xmlhttp.readyState == 4 && xmlhttp.status == 200) 
				{
				document.getElementById('chat_message_console').innerHTML="<BR/><BR/><font class='chat_title'><a href='"+chat_url+"?group_id="+group_id+"&language="+language+"&available_agents="+available_agents+"&show_email="+show_email+"'><?php echo _QXZ("GO BACK TO CHAT FORM") ?></a></font>\n<?php echo $survey_str; ?>";
				//if (chat_creator==user) {EndChat();}
				}
			}
		delete xmlhttp;
		}
}

function StartRefresh() {
	rInt=window.setInterval(function() {UpdateChatWindow()}, 1000);
}
function UpdateChatWindow() {
	var chat_id=document.getElementById('chat_id').value;
//	var chat_creator=document.getElementById('chat_creator').value;
	var user=document.getElementById('user').value;
	var current_message_field = document.getElementById('current_message_count');
	if (current_message_field == null) {var current_message_count=0;} else {var current_message_count=current_message_field.value;}

	if (chat_id)
		{
		var xmlhttp=false;
		if (!xmlhttp && typeof XMLHttpRequest!='undefined')
			{
			xmlhttp = new XMLHttpRequest();
			}
		if (xmlhttp) 
			{ 
			chat_query = "&chat_id="+chat_id+"&group_id="+group_id+"&user="+user+"&current_message_count="+current_message_count+"&language="+language+"&available_agents="+available_agents+"&show_email="+show_email+"&action=update_chat_window&keepalive=1";
			xmlhttp.open('POST', 'customer_chat_functions.php'); 
			xmlhttp.setRequestHeader('Content-Type','application/x-www-form-urlencoded; charset=UTF-8');
			xmlhttp.send(chat_query); 
			xmlhttp.onreadystatechange = function() 
				{ 
				if (xmlhttp.readyState == 4 && xmlhttp.status == 200) 
					{
					var chat_log_response = xmlhttp.responseText;
					if (chat_log_response.match(/^Error/))
						{
						document.getElementById('chat_message_console').innerHTML="<font class='chat_title alert'><?php echo _QXZ("Chat does not exist or has been closed") ?></font><BR/><BR/><font class='chat_title'><a href='"+chat_url+"?group_id="+group_id+"&language="+language+"&available_agents="+available_agents+"&show_email="+show_email+"'><?php echo _QXZ("GO BACK TO CHAT FORM") ?></a></font>\n<?php echo $survey_str; ?>";
						}

					var chatlogresponse=chat_log_response.replace(/Error\|/, '');
					var chatlogresponse_ary=chatlogresponse.split("|");

					var current_chat_status=chatlogresponse_ary[0];
					var fullchatlog=chatlogresponse_ary[1];

					document.getElementById('ChatActiveStatus').innerHTML=current_chat_status;
					// var fullchatlog=chat_log_response.replace(/Error\|/, '');
					document.getElementById('ChatDisplay').innerHTML=fullchatlog;
					var current_message_field_update = document.getElementById('current_message_count');
					if (current_message_field_update != null) {var current_message_count_update=current_message_field_update.value;}
					if (current_message_count_update>current_message_count) 
						{
						var myDiv = document.getElementById('ChatDisplay');
						document.getElementById('ChatDisplay').scrollTop = document.getElementById('ChatDisplay').scrollHeight;
						if (!document.getElementById("MuteCustomerChatAlert").checked) {document.getElementById("CustomerChatAudioAlertFile").play();}
						}
					}
				}
			delete xmlhttp;
			}
		}
}
function CustomerSendMessage(chat_id, user, message, chat_member_name) {
	var chat_id=document.getElementById('chat_id').value;
	var user=document.getElementById('user').value;
	var chat_message=encodeURIComponent(document.getElementById('chat_message').value.trim());
	var chat_member_name=encodeURIComponent(document.getElementById('chat_member_name').value.trim());

	if (!chat_message || !user) {return false;}
	if (!chat_member_name) {alert("Please enter a name to chat as.");}
	if (!chat_id) {alert("You have not joined a chat yet.");}
	document.getElementById('chat_message').value='';

	var xmlhttp=false;
	if (!xmlhttp && typeof XMLHttpRequest!='undefined')
		{
		xmlhttp = new XMLHttpRequest();
		}
	if (xmlhttp) 
		{ 
		chat_query = "&chat_message="+chat_message+"&chat_id="+chat_id+"&group_id="+group_id+"&chat_member_name="+chat_member_name+"&user="+user+"&language="+language+"&available_agents="+available_agents+"&show_email="+show_email+"&chat_level=0&action=send_message";
		xmlhttp.open('POST', 'customer_chat_functions.php'); 
		xmlhttp.setRequestHeader('Content-Type','application/x-www-form-urlencoded; charset=UTF-8');
		xmlhttp.send(chat_query); 
		xmlhttp.onreadystatechange = function() 
			{ 
			if (xmlhttp.readyState == 4 && xmlhttp.status == 200) 
				{
				var posting_response = xmlhttp.responseText;
				if (posting_response) 
					{
					// alert(posting_response);
					}
				else
					{
					UpdateChatWindow();
					}
				}
			}
		delete xmlhttp;
		}
}

</script>
</head>
<?php
if (!$chat_id) {
$chat_title= _QXZ("Request chat with agent"); # This can be modified for customization later
?>
	<body>
	<form action="<?php echo $PHP_SELF; ?>" method="post">
	<input type=hidden name=stage value="send_request">
	<input type=hidden name=language value="<?php echo $language; ?>">
	<input type=hidden name=available_agents value="<?php echo $available_agents; ?>">
	<input type=hidden name=show_email value="<?php echo $show_email ?>">
	<span id="chat_request_span">
		<table width="500" border=0 cellpadding=1 cellspacing=1 height="300">
			<tr>
				<th colspan='2' class='body_small_bold'><?php echo $chat_title; ?></th>
			</tr>
			<tr>
				<td align='right' class='body_small'><?php echo _QXZ("Please enter your name"); ?>:</td>
				<td align='left'>
				<input type='text' class="cust_form" name='first_name' id='first_name' size='10' maxlength='30'>&nbsp;<input class="cust_form" type='text' name='last_name' id='last_name' size='15' maxlength='30'>
				</td>
			</tr>
			<?php
			if ($group_id=='')
				{
			?>
			<tr>
				<td align='right' class='body_small'><?php echo _QXZ("Select the department you wish to chat with"); ?>:</td>
				<td align='left'>
				<select name='group_id' id='group_id' class="cust_form">
				<?php
				$group_stmt="select group_id, group_name from vicidial_inbound_groups where active='Y' and group_handling='CHAT' order by group_name asc";
				$group_rslt=mysql_to_mysqli($group_stmt, $link);
				while ($row=mysqli_fetch_row($group_rslt)) {
					echo "<option value='$row[0]'>$row[1]</option>\n";
				}
				?>
				</select>
				</td>
			</tr>
			<?php
				}
			else
				{
				echo "<tr><td><input type=hidden name=group_id value=\"$group_id\"></td></tr>\n";
				}
			if ( ($show_email=='') or ($show_email=='N') or ($show_email=='Y_WITH_PHONE') )
				{
			?>
			<tr>
				<td align='right' class='body_small'><?php echo _QXZ("Phone number (optional)"); ?>:</td>
				<td align='left'>
				<input type='text' class="cust_form" name='phone_number' id='phone_number' size='10' maxlength='20'>
				</td>
			</tr>
			<?php
				}
			if ( ($show_email=='ONLY') or ($show_email=='Y_WITH_PHONE') )
				{
			?>
			<tr>
				<td align='right' class='body_small'><?php echo _QXZ("Email (optional)"); ?>:</td>
				<td align='left'>
				<input type='text' class="cust_form" name='email' id='email' size='50' maxlength='100'>
				</td>
			</tr>
			<?php
				}
			?>
			<tr>
				<th colspan='2'><input type='submit' class='blue_btn' value='<?php echo _QXZ("SEND REQUEST"); ?>' name="send_request"><BR></th>
			</tr>
			<?php
			if ($error_msg) {
					echo "<tr><th colspan='2' class='queue_text_red'>$error_msg<BR></th></tr>";
			}
			?>
		</table>
	</span>
	</form>
	</body>
<?
} else if ($chat_id && $group_id && (!$first_name || !$last_name)) {
?>
	<body>
	<form action="<?php echo $PHP_SELF; ?>" method="post" name="chat_form" id="chat_form">
	<input type=hidden name=stage value="join_chat">
	<span id="chat_request_span">
		<table width="500" border=0 cellpadding=3 cellspacing=3 height="300">
			<tr>
				<th colspan='2' class='body_small_bold'><?php echo $chat_title; ?></th>
			</tr>
			<tr>
				<td align='right' class='body_small'><?php echo _QXZ("Please enter your name"); ?>:</td>
				<td align='left'>
				<input type='text' class="cust_form" name='first_name' id='first_name' size='10' maxlength='30'>&nbsp;<input class="cust_form" type='text' name='last_name' id='last_name' size='15' maxlength='30'>
				</td>
			</tr>
			<tr>
				<td align='right' class='body_small'><?php echo _QXZ("Phone number (optional)"); ?>:</td>
				<td align='left'>
				<input type='text' class="cust_form" name='phone_number' id='phone_number' size='10' maxlength='20'>
				</td>
			</tr>
			<tr>
				<th colspan='2'><input type='submit' class='blue_btn' value='<?php echo _QXZ("JOIN CHAT"); ?>' name="join_chat"><BR></th>
			</tr>
			<?php
			if ($error_msg) {
					echo "<tr><th colspan='2' class='queue_text_red'>$error_msg<BR></th></tr>";
			}
			?>
		</table>
	</span>
	<input type="hidden" id="chat_id" name="chat_id" value="<?php echo $chat_id; ?>">
	<input type="hidden" id="group_id" name="group_id" value="<?php echo $group_id; ?>">
	<input type="hidden" id="lead_id" name="lead_id" value="<?php echo $lead_id; ?>">
	<input type="hidden" id="language" name="language" value="<?php echo $language; ?>">
	<input type="hidden" id="available_agents" name="available_agents" value="<?php echo $available_agents; ?>">
	<input type="hidden" id="show_email" name="show_email" value="<?php echo $show_email ?>">
	</form>
	</body>

<?
} else {
?>
	<body onLoad="StartRefresh();" onUnload="javascript:clearInterval(rInt); LeaveChat();">
	<form action='<?php echo $PHP_SELF; ?>' name="chat_form" id="chat_form">
	<table width='100%' border='0'>
	<tr>
		<td class="chat_window" height='250' width='100%'>
		
		<table border='0' width='100%'>
			<tr height='35'>
				<td align='left' width='50%' valign='top'>
					<font class='chat_title bold'><?php echo _QXZ("Current chat"); ?>: <span id='ChatActiveStatus'><font color='#990'>WAITING</font></span></font>
				</td>
				<td align='right' width='50%' valign='top'>
				<?php
				if (file_exists("../$filepath/vicidial_admin_web_logo$web_logo")) 
					{
					echo "<img class='small_logo' src='/$filepath/vicidial_admin_web_logo$web_logo'>\n";
					}
				else
					{
					if (file_exists("./images/vicidial_admin_web_logo$web_logo")) 
						{
						echo "<img class='small_logo' src='images/vicidial_admin_web_logo$web_logo'>\n";
						}
					}
				?>
				</td>
			</tr>
		</table>

		<span id='ChatDisplay' name='ChatDisplay' style="position:relative;display:block;width:100%;height:215px;overflow-y:auto;overflow-x:none;z-index:0">
		</span>
<!--
		<span style="position:fixed;display:block;top:230px;right:25px;z-index:1"><img border="0" src="images/VICIchat_powered_logo.gif" width="123" height="30"></span> 
		<span style="display:inline-block;float:right;z-index:1"><img border="0" src="images/VICIchat_powered_logo.png" width="123" height="30"></span> 
//-->
		</td>
	</tr>
	<tr>
		<td align='center'>
		<table width='400' align='center' border='0' cellpadding='0' cellspacing='0'>
			<tr>
				<td align='center' colspan='2'>
					<span id='chat_message_console' name='chat_message_console'>
					<textarea border='1' name='chat_message' id='chat_message' class='chat_window' cols='86' rows='4' onkeypress="if (event.keyCode==13 && !event.shiftKey) {CustomerSendMessage(this.form.chat_id.value, this.form.user.value, this.form.chat_message.value); return false;}"></textarea>
					</span>
				</td>
			</tr>
			<tr>
				<td align='left' class='chat_message' valign='top'><input class='blue_btn' type='button' style="width:100px" value="<?php echo _QXZ("SEND MESSAGE"); ?>" onClick="CustomerSendMessage(this.form.chat_id.value, this.form.user.value, this.form.chat_message.value)"></td>
				<td align='right' valign='top'><input class='blue_btn' type='button' style="width:100px" value="<?php echo _QXZ("CLEAR"); ?>" onClick="document.getElementById('chat_message').value=''"></td>
			</tr>
			<tr>
				<td class='chat_message' valign='top' align='left'><BR>
					<input type='checkbox' id='MuteCustomerChatAlert' name='MuteCustomerChatAlert'><?php echo _QXZ("Mute alert sound"); ?>
				</td>
				<td valign='top' align='right'><BR>
					<input class='red_btn' type='button' style="width:100px" value="<?php echo _QXZ("LEAVE CHAT"); ?>" onClick="LeaveChat(document.getElementById('chat_id').value, document.getElementById('user').value, document.getElementById('chat_member_name').value);">
				</td>
			</tr>
			<tr>
				<td align='right' colspan='2'><img border="0" style="padding-top: 5px;" src="images/VICIchat_powered_logo.png" width="123" height="30"></td>
			</tr>
		</table>
		</td>
	</tr>
	<?php
	if ($error_msg) 
		{
		echo "<tr><th class='queue_text_red'>$error_msg<BR></th></tr>";
		}
	?>
	</table>
	<input type="hidden" id="user" name="user" value="<?php echo $user; ?>">
	<input type="hidden" id="chat_member_name" name="chat_member_name" value="<?php echo $chat_member_name; ?>">
	<input type="hidden" id="chat_id" name="chat_id" value="<?php echo $chat_id; ?>">
	<input type="hidden" id="chat_creator" name="chat_creator" value="<?php echo $chat_creator; ?>">
	<input type="hidden" id="lead_id" name="lead_id" value="<?php echo $lead_id; ?>">
	<input type="hidden" id="group_id" name="group_id" value="<?php echo $group_id; ?>">
	<input type="hidden" id="language" name="language" value="<?php echo $language; ?>">
	<input type="hidden" id="available_agents" name="available_agents" value="<?php echo $available_agents; ?>">
	<input type="hidden" id="show_email" name="show_email" value="<?php echo $show_email ?>">
	<audio id='CustomerChatAudioAlertFile'><source src="sounds/chat_alert.mp3" type="audio/mpeg"></audio>
	</form>
	</body>
<?
}
?>

</html>
