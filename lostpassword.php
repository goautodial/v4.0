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
	
	error_reporting(E_ERROR | E_PARSE);

	require_once('./php/LanguageHandler.php');
	require_once('./php/DbHandler.php');
	require_once('./php/CRMUtils.php');
	
	$error=''; // Variable To Store Error Message
	$lh = \creamy\LanguageHandler::getInstance();

	if (isset($_POST['submit'])) {
		if (empty($_POST['email'])) {
			$error = $lh->translationFor("insert_valid_address");
		} else {
			$db = new \creamy\DbHandler();

			// Define $username and $password
			$username=$_POST['email'];
			
			// To protect MySQL injection for Security purpose
			$email = stripslashes($_POST["email"]);
			
			// Check password and redirect accordingly
			$result = false;
			
			require_once('./php/MailHandler.php');
			$mh = \creamy\MailHandler::getInstance();
			$result = $mh->sendPasswordRecoveryEmail($email);
			if ($result === false) { // login failed
				$error = $lh->translationFor("error_sending_recovery_email");
			} else {
				$error = $lh->translationFor("recovery_email_sent")." $email. ".$lh->translationFor("please_check_email");
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
        <!-- Theme style -->
        <link href="./css/creamycrm.css" rel="stylesheet" type="text/css" />

        <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
          <script src="js/html5shiv.js"></script>
          <script src="js/respond.min.js"></script>
        <![endif]-->
    <script src="js/jquery.min.js"></script>
    <script src="js/bootstrap.min.js" type="text/javascript"></script>
    </head>

  <body class="login-page">
    <div class="login-box" id="login-box">
	  <div class="margin text-center">
		<img src="img/logo.png" width="auto" height="64">
	  </div>
      <div class="login-logo"><?php $lh->translateText("reset_password"); ?></div>
      <div class="login-box-body">
        <p class="login-box-msg"><?php $lh->translateText("have_you_lost_your_password"); ?></p>
        <form action="" method="post">
          <div class="form-group has-feedback">
            <input type="text" class="form-control" name="email" id="email" placeholder="<?php $lh->translateText("email"); ?>"/>
            <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
          </div>
	    	<div class="text-center" name="error-message" style="color: red;">
	    	<?php
	    		if (isset($error)) { print ("<p>".$error."</p>"); }
	    	?>
	    	</div>
          <div class="row">
            <div class="col-xs-4"></div>
            <div class="col-xs-4">
              <button type="submit" name="submit" class="btn btn-primary btn-block btn-flat"><?php $lh->translateText("send"); ?></button>
            </div><!-- /.col -->
            <div class="col-xs-4"></div>
          </div>
        </form>
		<p class="text-center"><?php $lh->translateText("want_to_try_again"); ?> <a href="login.php"><?php $lh->translateText("back_to_creamy"); ?>.</a></p>
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
    </script>
  </body>
</html>
