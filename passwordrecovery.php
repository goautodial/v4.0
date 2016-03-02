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
	$lh = \creamy\LanguageHandler::getInstance();
	
	$result = NULL;
	$error = NULL;
	$success = false;
	
	if (isset($_POST["submit"])) {
		require_once('./php/DbHandler.php');
		$email = NULL;
		$code = NULL;
		$nonce = NULL;
		$date = NULL;
		$password1 = NULL;
		$password2 = NULL;
		if (isset($_POST["email"])) $email = $_POST["email"];
		if (isset($_POST["code"])) $code = $_POST["code"];
		if (isset($_POST["nonce"])) $nonce = $_POST["nonce"];
		if (isset($_POST["date"])) $date = $_POST["date"];
		if (isset($_POST["password1"])) $password1 = $_POST["password1"];
		if (isset($_POST["password2"])) $password2 = $_POST["password2"];
		
		if ($password1 == $password2 && !empty($password1) && !empty($password2)) {
			$db = new \creamy\DbHandler();
			if ($db->checkEmailSecurityCode($email, $date, $nonce, $code)) {
				if ($db->changePasswordForUserIdentifiedByEmail($email, $password1)) {
					$result = $lh->translationFor("password_reset_successfully");
					$success = true;
				} else $result = $lh->translationFor("password_reset_error");
			}
		} else {
			$error = $lh->translationFor("passwords_dont_match");
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
        <?php print $ui->creamyThemeCSS(); ?>

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
        <?php $lh->translateText("reset_password"); ?>
      </div><!-- /.login-logo -->
      <div class="login-box-body">
        <p class="login-box-msg"><?php $lh->translateText("reset_password"); ?></p>
   		<?php if ($result == NULL) { ?>
        <form action="" method="post">
          <div class="form-group has-feedback">
            <input type="password" class="form-control" name="password1" id="password1" placeholder="<?php $lh->translateText("insert_new_password"); ?>"/>
            <span class="glyphicon glyphicon-lock form-control-feedback"></span>
          </div>
          <div class="form-group has-feedback">
            <input type="password" name="password2" id="password2" class="form-control" placeholder="<?php $lh->translateText("insert_new_password_again"); ?>"/>
            <span class="glyphicon glyphicon-lock form-control-feedback"></span>
          </div>
            <input type="hidden" name="code" id="code" value="<?php echo $_GET['code']; ?>">
			<input type="hidden" name="date" id="date" value="<?php echo $_GET['date']; ?>">
			<input type="hidden" name="nonce" id="nonce" value="<?php echo $_GET['nonce']; ?>">
			<input type="hidden" name="email" id="email" value="<?php echo $_GET['email']; ?>">
	    	<div name="error-message" style="color: red;">
	    	<?php
	    		if (isset($error)) { print ("<p>".$error."</p>"); }
	    	?>
	    	</div>
          <div class="row">
            <div class="col-xs-3"></div>
            <div class="col-xs-6">
              <button type="submit" name="submit" class="btn btn-primary btn-flat"><?php $lh->translateText("reset_password"); ?></button>
            </div><!-- /.col -->
            <div class="col-xs-3"></div>
          </div>
        </form>
		<p class="text-center"><?php $lh->translateText("not_looking_for"); ?> <a href="login.php"><?php $lh->translateText("back_to_creamy"); ?>.</a></p>
		<?php } else { 
			print "<p>$result</p>\n";
			if ($success == true) {
            	print "<button class=\"btn bg-light-blue btn-block\" onclick=\"window.location.href='./login.php'\">
            	".$lh->translationFor("access_creamy")."
            	</button>";
			}
		 } 
		 ?>
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