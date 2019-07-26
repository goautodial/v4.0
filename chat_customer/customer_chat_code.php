<?php
# customer_chat_code.php
#
# Copyright (C) 2016  Joe Johnson, Matt Florell <vicidial@gmail.com>    LICENSE: AGPLv2
#
# Example for incorporating the customer side of the Vicidial chat into a web page.  
# Can be called as an include file, if desired.
#
# Builds:
# 151212-0829 - First Build for customer chat
# 151217-1015 - Allow for group_id variable
# 151219-1415 - Added header and language variable
# 160108-1659 - Added available_agents variable
# 160120-1944 - Added show_email variable
#

if (isset($_GET["lead_id"]))				{$lead_id=$_GET["lead_id"];}
	elseif (isset($_POST["lead_id"]))		{$lead_id=$_POST["lead_id"];}
if (isset($_GET["chat_id"]))				{$chat_id=$_GET["chat_id"];}
	elseif (isset($_POST["chat_id"]))		{$chat_id=$_POST["chat_id"];}
if (isset($_GET["group_id"]))				{$chat_group_id=$_GET["group_id"];}
	elseif (isset($_POST["group_id"]))		{$chat_group_id=$_POST["group_id"];}
if (isset($_GET["chat_group_id"]))			{$chat_group_id=$_GET["chat_group_id"];}
	elseif (isset($_POST["chat_group_id"]))	{$chat_group_id=$_POST["chat_group_id"];}
if (isset($_GET["email"]))					{$email=$_GET["email"];}
	elseif (isset($_POST["email"]))			{$email=$_POST["email"];}
if (isset($_GET["unique_userID"]))			{$unique_userID=$_GET["unique_userID"];}
	elseif (isset($_POST["unique_userID"]))	{$unique_userID=$_POST["unique_userID"];}
if (isset($_GET["language"]))				{$language=$_GET["language"];}
	elseif (isset($_POST["language"]))		{$language=$_POST["language"];}
if (isset($_GET["available_agents"]))			{$available_agents=$_GET["available_agents"];}
	elseif (isset($_POST["available_agents"]))	{$available_agents=$_POST["available_agents"];}
if (isset($_GET["show_email"]))				{$show_email=$_GET["show_email"];}
	elseif (isset($_POST["show_email"]))	{$show_email=$_POST["show_email"];}

$URL_vars="?user=".urlencode($unique_userID)."&lead_id=".$lead_id."&group_id=".urlencode($chat_group_id)."&chat_id=".$chat_id."&email=".urlencode($email)."&language=".urlencode($language)."&available_agents=".urlencode($available_agents)."&show_email=".urlencode($show_email);
header ("Content-type: text/html; charset=utf-8");
header ("Cache-Control: no-cache, must-revalidate");  // HTTP/1.1
header ("Pragma: no-cache");                          // HTTP/1.0
echo '<?xml version="1.0" encoding="UTF-8"?><html><head><title>Chat</title></head>';
?>

<iframe src="/chat_customer/vicidial_chat_customer_side.php<?php echo $URL_vars; ?>" style="width:640;height:480;background-color:transparent;" scrolling="auto" frameborder="0" allowtransparency="true" id="ViCiDiAlChAtIfRaMe" name="ViCiDiAlChAtIfRaMe"/>
</html>
