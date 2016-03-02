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

// dependencies
require_once('./php/CRMDefaults.php');
require_once('./php/LanguageHandler.php');
require_once('./php/DbHandler.php');

// initial values
$lh = \creamy\LanguageHandler::getInstance();
$db = new \creamy\DbHandler();
$result = $lh->translationFor("activation_account_failed");
$title = $lh->translationFor("oups");

// received parameters
$email = NULL;
$code = NULL;
$nonce = NULL;
$date = NULL;
if (isset($_GET["email"])) $email = $_GET["email"];
if (isset($_GET["code"])) $code = $_GET["code"];
if (isset($_GET["nonce"])) $nonce = $_GET["nonce"];
if (isset($_GET["date"])) $date = $_GET["date"];

if (isset($email) && isset($code) && isset($nonce) && isset($date)) {
	// check security code validity
	if ($db->checkEmailSecurityCode($email, $date, $nonce, $code)) {
		// check if this email corresponds to a valid user.
		$userData = $db->getDataForUserWithEmail($email);
		if (isset($userData)) {
			// now enable the user.
			if ($db->setStatusOfUser($userData["id"], CRM_DEFAULTS_USER_ENABLED)) {
				$result = $lh->translationFor("activation_account_succeed");
				$title = $lh->translationFor("congratulations");
			} 
		}
	}
}
?>
<html class="lockscreen">
    <head>
        <meta charset="UTF-8">
        <title><?php $lh->translateText("reset_password"); ?></title>
        <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
        <link href="css/bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="css/font-awesome.min.css" rel="stylesheet" type="text/css" />
        <!-- Creamy style -->
        <link href="css/creamycrm.css" rel="stylesheet" type="text/css" />
        <link href="css/skins/skin-blue.min.css" rel="stylesheet" type="text/css" />
        

        <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
          <script src="js/html5shiv.js"></script>
          <script src="js/respond.min.js"></script>
        <![endif]-->
    </head>
    
    <body class="login-page">
    <div class="login-box" id="login-box">
	  <div class="margin text-center">
		<img src="img/logo.png" width="64" height="64">
	  </div>
      <div class="login-logo">
        <?php print $title; ?>
      </div><!-- /.login-logo -->
      <div class="login-box-body">
        <p class="login-box-msg"><?php print $result; ?></p>
        <div class="row">
			<div class="col-xs-3"></div>
			<div class="col-xs-6"><a href="./login.php" class="btn btn-primary btn-block btn-flat"><?php $lh->translateText("back_to_creamy"); ?></a></div>
			<div class="col-xs-3"></div>
		</div>
      </div><!-- /.login-box-body -->
    </div><!-- /.login-box -->
    <div class="margin text-center">
        <span><?php $lh->translateText("never_heard_of_creamy"); ?></span>
        <br/>
        <button class="btn bg-red btn-flat" onclick="window.location.href='http://creamycrm.com'"><i class="fa fa-globe"></i></button>
        <button class="btn bg-light-blue btn-flat" onclick="window.location.href='https://github.com/DigitalLeaves/Creamy'"><i class="fa fa-github"></i></button>
        <button class="btn bg-aqua btn-flat" onclick="window.location.href='https://twitter.com/creamythecrm'"><i class="fa fa-twitter"></i></button>
    </div>
	<?php unset($error); ?>
  </body>
</html>