<?php
###########################################################
### Name: ViewImage.php                                 ###
### Functions: View image from database                 ###
### Copyright: GOAutoDial Ltd. (c) 2011-2016            ###
### Version: 4.0                                        ###
### Written by: Christopher P. Lomuntad                 ###
### License: AGPLv2                                     ###
###########################################################

require_once('CRMDefaults.php');
require_once('DbHandler.php');

$db = new \creamy\DbHandler();

$uid = $_REQUEST['user_id'];

$image = $db->getUserAvatar($uid);

ob_clean();

header('Content-type: '.$image['type']);
echo base64_decode($image['data']);
?>