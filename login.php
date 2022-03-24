<?php
/**
 * @file 		login.php
 * @brief 		login application
 * @copyright 	Copyright (c) 2020 GOautodial Inc. 
 * @author     	Christopher Lomuntad 
 * @author		Demian Lizandro A. Biscocho
 * @author		Ignacio Nieto Carvajal
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
**/

	error_reporting(E_ERROR | E_PARSE);

	require_once('./php/CRMDefaults.php');
	require_once('./php/UIHandler.php');
	require_once('./php/DbHandler.php');
	require_once('./php/LanguageHandler.php');
	require_once('./php/SessionHandler.php');
	$session_class = new \creamy\SessionHandler();		
	
	// force https protocol
	if(empty($_SERVER["HTTPS"]) || $_SERVER["HTTPS"] != "on") {
		header("Location: https://" . $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"]);
		exit();
	}

	/*if (CRM_SESSION_DRIVER == 'database') {
		require_once('./php/SessionHandler.php');
		$session_class = new \creamy\SessionHandler();
	} else {
		session_start(); // Starting Session
	}*/
	
	$lh = \creamy\LanguageHandler::getInstance();
	$ui = \creamy\UIHandler::getInstance();
	$error = ''; // Variable To Store Error Message
	if (isset($_POST['submit'])) {
		if (empty($_POST['username']) || empty($_POST['password'])) {
			$error = $lh->translationFor("insert_valid_login_password");
		} else {
			$db = new \creamy\DbHandler();

			// Define $username and $password
			$username=$_POST['username'];
			$password=$_POST['password'];
			
			// To protect MySQL injection for Security purpose
			$username = stripslashes($username);
			$password = stripslashes($password);
			$username = $db->escape_string($username);
			$password = $db->escape_string($password);

			// Check password and redirect accordingly
			$result = null;
			if(filter_var($username, FILTER_VALIDATE_EMAIL)) {
		        // valid email address
				$result = $db->checkLoginByEmail($username, $password, $_SERVER['REMOTE_ADDR']);
		    }
		    else {
		        // not an email. User name?
				$result = $db->checkLoginByName($username, $password, $_SERVER['REMOTE_ADDR']);
		    }
			if ($result == NULL) { // login failed
				$error = $lh->translationFor("invalid_login_password");
			} else {
				$_SESSION["user"] = $username;
				$_SESSION["userid"] = $result["id"];
				$_SESSION["username"] = $result["name"];
				$_SESSION["userrole"] = $result["role"];
				$_SESSION["usergroup"] = $result["user_group"];
				$_SESSION["phone_login"] = $result["phone_login"];
				$_SESSION["phone_pass"] = $result["phone_pass"];
				$_SESSION["phone_this"] = $password;
                $_SESSION["ha1"] = $result["ha1"];
                $_SESSION["realm"] = $result["realm"];
                $_SESSION["bcrypt"] = $result["bcrypt"];
				$_SESSION["use_webrtc"] = $result["use_webrtc"];
				$_SESSION["password_hash"] = $result["password_hash"];
				
				if (!empty($result["avatar"])) {
					$_SESSION['avatar'] = $result["avatar"];
				} else { // random avatar.
					$_SESSION["avatar"] = CRM_DEFAULTS_USER_AVATAR;
				}

				if($_SESSION["userrole"] == CRM_DEFAULTS_USER_ROLE_ADMIN || $_SESSION["userrole"] == CRM_DEFAULTS_USER_ROLE_SUPERVISOR || $_SESSION["userrole"] == CRM_DEFAULTS_USER_ROLE_TEAMLEADER){
					header("location: index.php"); // Redirecting To Admin Dashboard
				}
				if($_SESSION["userrole"] == CRM_DEFAULTS_USER_ROLE_AGENT){
					header("location: agent.php"); // Redirecting to Agent Dashboard
				}

			}
		}
	}
	
	$uname = (isset($_GET['username'])) ? $_GET['username'] : '';
	$upass = (isset($_GET['password'])) ? $_GET['password'] : '';
	//https://github.com/goautodial/v4.0/issues/48
	//prevent xss
	$uname = htmlentities($uname);
	$upass = htmlentities($upass);
?>
<html>
    <head>
        <meta charset="UTF-8">
        <title><?php $lh->translateText("system_access"); ?> </title>
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
   	
	<?php if(ECCS_BLIND_MODE === 'y'){ ?>
	<link href="./css/customizations/eccs_admin.css" rel="stylesheet" type="text/css" />
	<?php }?>
 
	</head>
  <body class="login-page" style="overflow: hidden;">
    <div class="login-box" id="login-box">
	  <div class="margin text-center">
		<img src="<?php echo $ui->creamyHeaderLogo();?>" width="auto" height="64">
	  </div>
	<!-- ECCS CUSTOMIZATION -->
      <center>
	<div id="div1" class="login-logo" style="">
        <?php $lh->translateText("welcome_to"); ?><?php print($ui->creamyHeaderName()); ?>!
      </div><!-- /.login-logo -->
      <div class="login-box-body">
        <p id="p1" style="" class="login-box-msg"><?php $lh->translateText("sign_in"); ?></p>
        <form action="" method="post">
          <div class="form-group has-feedback">
            <input id="input1" type="text" style="" class="form-control" name="username" placeholder="<?php $lh->translateText("username_or_email"); ?>" value="<?=$uname?>"/>
            <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
          </div>
          <div class="form-group has-feedback">
            <input id="input2" type="password" style="" name="password" class="form-control" placeholder="<?php $lh->translateText("password"); ?>" value="<?=$upass?>"/>
            <span class="glyphicon glyphicon-lock form-control-feedback"></span>
          </div>
	    	<div name="error-message" style="color: red;">
	    	<?php
	    		if (isset($error)) { print ("<p>".$error."</p>"); }
	    	?>
	    	</div>
          <div class="row">
            <div class="col-xs-3"></div>
            <div class="col-xs-6">
              <button id="btn1" type="submit" style="" name="submit" class="btn btn-primary btn-block btn-flat"><?php $lh->translateText("access"); ?></button>
            </div><!-- /.col -->
            <div class="col-xs-3"></div>
          </div>
        </form>
	<!--<p class="text-center"><?php $lh->translateText("forgotten_password"); ?> <a href="lostpassword.php"><?php $lh->translateText("click_here"); ?>.</a></p>-->
      </div><!-- /.login-box-body -->
    </div><!-- /.login-box -->
	</center>
	<footer>
		<div style="text-align: center; font-weight: 600; margin-top: -40px;">Copyright &copy; <?=date("Y")?> <a href="https://goautodial.com" target="_blank">GOautodial Inc.</a> All rights reserved.</div>
	</footer>
   <!--<div class="margin text-center">
        <span><?php $lh->translateText("never_heard_of_creamy"); ?></span>
        <br/>
        <button class="btn bg-red btn-flat" onclick="window.location.href='http://creamycrm.com'"><i class="fa fa-globe"></i></button>
        <button class="btn bg-light-blue btn-flat" onclick="window.location.href='https://github.com/DigitalLeaves/Creamy'"><i class="fa fa-github"></i></button>
        <button class="btn bg-aqua btn-flat" onclick="window.location.href='https://twitter.com/creamythecrm'"><i class="fa fa-twitter"></i></button>
    </div>-->

	<?php unset($error); ?>
  </body>
</html>
