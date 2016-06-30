<?php
ini_set("display_errors", 'on');
error_reporting(E_ALL);
$VARDBgo_server = "69.46.6.35";
$VARDBgo_user = "justgocloud";
$VARDBgo_pass = "justgocloud1234";
$VARDBgo_database = "asterisk";
$VARDBgo_port = 3306;
$linkgo=mysqli_connect("$VARDBgo_server", "$VARDBgo_user", "$VARDBgo_pass", "$VARDBgo_database", "$VARDBgo_port");

if (!$linkgo) {
	echo mysqli_connect_errno();
}
?>
