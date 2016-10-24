<?php
###########################################################
### Name: SaveImage.php                                 ###
### Functions: Save image to the database               ###
### Copyright: GOAutoDial Ltd. (c) 2011-2016            ###
### Version: 4.0                                        ###
### Written by: Christopher P. Lomuntad                 ###
### License: AGPLv2                                     ###
###########################################################

require_once('CRMDefaults.php');
require_once('DbHandler.php');

$db = new \creamy\DbHandler();

$uid  = $_REQUEST['user_id'];
$type = $_REQUEST['type'];
$image = $_REQUEST['image'];

$uploaded = $db->saveUserAvatar($uid, $type, $image);

if ($uploaded) {
    echo "success";
} else {
    echo "error";
}
?>