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
	
	require_once('./php/CRMDefaults.php');
	require_once('./php/DbInstaller.php');
	require_once('./php/LanguageHandler.php');
	require_once('./php/RandomStringGenerator.php');
	require_once('./php/CRMUtils.php');
	
	define('CRM_INSTALL_SKEL_CONFIG_FILE', 'skel/Config.php');

	// language handler
	$locale = Locale::acceptFromHttp($_SERVER['HTTP_ACCEPT_LANGUAGE']);
	$lh = \creamy\LanguageHandler::getInstance($locale);
	
	session_start(); // Starting Session

	$error = ""; 					// Variable To Store Error Message	
	$currentState = "step1";	// current install status variable
	
	// set initial installation step (if found).
	if (isset($_SESSION["installationStep"])) { $currentState = $_SESSION["installationStep"]; }

	if (file_exists(CRM_INSTALLED_FILE)) { // check if already installed 
		$currentState = "already_installed"; 
	} elseif (isset($_POST["submit_step1"]) && $currentState == "step1") { // first step: get credentials for database access.
		$dbhost = "localhost";
		$dbname = NULL;
		$dbuser = NULL;
		$dbpass = NULL;
		$timezone = NULL;
		$desiredLanguage = $locale;
		if (isset($_POST["dbhost"])) { $dbhost = $_POST["dbhost"]; }
		if (isset($_POST["dbname"])) { $dbname = $_POST["dbname"]; }
		if (isset($_POST["dbuser"])) { $dbuser = $_POST["dbuser"]; }
		if (isset($_POST["dbpass"])) { $dbpass = $_POST["dbpass"]; }
		if (isset($_POST["userTimeZone"])) { $timezone = $_POST["userTimeZone"]; } else { $timezone = "UTC"; }
		if (isset($_POST["desiredLanguage"])) { $desiredLanguage = $_POST["desiredLanguage"]; }
		
		// stablish connection with the database.
		$dbInstaller = new DBInstaller($dbhost, $dbname, $dbuser, $dbpass);
	
		if ($dbInstaller->getState() == CRM_INSTALL_STATE_SUCCESS) { // database access succeed. Try to set the basic tables.
			// update LanguageHandler locale
			error_log("Creamy install: Trying to set locale to $locale");
			$lh->setLanguageHandlerLocale($locale);

			// delete current database tables.
			$cleanResult = $dbInstaller->dropPreviousTables();
			if ($cleanResult == true) {
				// setup settings table.
				$randomStringGenerator = new \creamy\RandomStringGenerator();
				$crmSecurityCode = $randomStringGenerator->generate(40);
				$settingsResult = $dbInstaller->setupSettingTable($timezone, $desiredLanguage, $desiredLanguage);
	
				if ($settingsResult === true) {
					// generate a new config file for Creamy, incluying db information & timezone.
					$configContent = file_get_contents(CRM_INSTALL_SKEL_CONFIG_FILE);			
					$customConfig = "
// database configuration
define('DB_USERNAME', '$dbuser');
define('DB_PASSWORD', '$dbpass');
define('DB_HOST', '$dbhost');
define('DB_NAME', '$dbname');
define('DB_PORT', '3306');

// other configuration parameters			
".CRM_PHP_END_TAG;
		
					$configContent = str_replace(CRM_PHP_END_TAG, $customConfig, $configContent);
					file_put_contents(CRM_PHP_CONFIG_FILE, $configContent);
					
					// set session credentials and continue installation.
					$_SESSION["dbhost"] = $dbhost;
					$_SESSION["dbname"] = $dbname;
					$_SESSION["dbuser"] = $dbuser;
					$_SESSION["dbpass"] = $dbpass;
					$error = "";
					$currentState = "step2";			
					$_SESSION["installationStep"] = "step2";				
				} else {
					$error = $dbInstaller->getLastErrorMessage();
					$currentState = "step1";
					$_SESSION["installationStep"] = "step1";
				}

			} else {
				$error = $dbInstaller->getLastErrorMessage();
				$currentState = "step1";
				$_SESSION["installationStep"] = "step1";
			}
		} else {
			$error = $dbInstaller->getLastErrorMessage();
			$currentState = "step1";
			$_SESSION["installationStep"] = "step1";
		}
	} elseif (isset($_POST["submit_step2"])  && $currentState == "step2") { // second step: setup database and create admin user.
		$adminName = NULL;
		$adminPassword = NULL;
		$adminPasswordCheck = NULL;
		$adminEmail = NULL;
		if (isset($_POST["adminName"])) { $adminName = $_POST["adminName"]; }
		if (isset($_POST["adminPassword"])) { $adminPassword = $_POST["adminPassword"]; }
		if (isset($_POST["adminPasswordCheck"])) { $adminPasswordCheck = $_POST["adminPasswordCheck"]; }
		if (isset($_POST["adminEmail"])) { $adminEmail = $_POST["adminEmail"]; }
		
		if (empty($adminName) || empty($adminPassword) || empty($adminPasswordCheck) || empty($adminEmail)) { // unable get admin name or password
			$error = $lh->translationFor("unable_get_admin_credentials");
			$currentState = "step2";
			$_SESSION["installationStep"] = "step2";
		} else { // setup basic database tables.
			if ($adminPassword == $adminPasswordCheck) {
				// create the initial database structure
				$dbhost = $_SESSION["dbhost"];
				$dbname = $_SESSION["dbname"];
				$dbuser = $_SESSION["dbuser"];
				$dbpass = $_SESSION["dbpass"];
				
				if (empty($dbhost) || empty($dbname) || empty($dbuser) || empty($dbpass)) {
					$error = $lh->translationFor("unable_get_database_credentials");
					$currentState = "step2";
					$_SESSION["installationStep"] = "step2";
				} else {
					$dbInstaller = new DBInstaller($dbhost, $dbname, $dbuser, $dbpass);
					if ($dbInstaller->setupBasicDatabase($adminName, $adminPassword, $adminEmail)) {
						$error = "";
						$currentState = "step3";			
						$_SESSION["installationStep"] = "step3";
					} else {
						$errorMsg = $dbInstaller->getLastErrorMessage();
						$error = $lh->translationFor("error_setting_db_tables")." ". isset($errorMsg) ? $errorMsg : $lh->translationFor("database_not_set");
						$currentState = "step2";
						$_SESSION["installationStep"] = "step2";
					}
					
					// store the admin email.
					$configContent = file_get_contents(CRM_PHP_CONFIG_FILE);					
					$customConfig = "define('CRM_ADMIN_EMAIL', '$adminEmail');\n".CRM_PHP_END_TAG;
		
					$configContent = str_replace(CRM_PHP_END_TAG, $customConfig, $configContent);
					file_put_contents(CRM_PHP_CONFIG_FILE, $configContent);
					
				}				
			} else {
				$error = "Passwords do not match. Please try again. ";
				$currentState = "step2";
				$_SESSION["installationStep"] = "step2";
			}
			
		}
		
	} elseif (isset($_POST["submit_step3"]) && $currentState == "step3") { // third step: define customer groups and names.
		$dbhost = $_SESSION["dbhost"];
		$dbname = $_SESSION["dbname"];
		$dbuser = $_SESSION["dbuser"];
		$dbpass = $_SESSION["dbpass"];

		if (empty($dbhost) || empty($dbname) || empty($dbuser) || empty($dbpass)) {
			$error = $lh->translationFor("unable_get_database_credentials");
			$currentState = "step3";
			$_SESSION["installationStep"] = "step3";
		} else {
			$customersType = "default"; if (isset($_POST["setup_customers"])) $customersType = $_POST["setup_customers"];
			$success = FALSE;
			// build the array of customers' names
			$customerNames = array();
			array_push($customerNames, "contacts");
			if ($customersType == "default") { // default customers schema
				array_push($customerNames, "customers");
			} else if ($customersType == "custom") { // custom customers schema
				foreach ($_POST as $key => $value) {
					if (\creamy\CRMUtils::startsWith($key, "customCustomerGroup")) { array_push($customerNames, $value); }
				}
			}
			
			$dbInstaller = new DBInstaller($dbhost, $dbname, $dbuser, $dbpass);
			// setup customers' tables

			if ($dbInstaller->setupCustomerTables($customersType, $customerNames)) {
				// enable customers statistic retrieval
				if ($dbInstaller->setupCustomersStatistics($customersType, $customerNames)) {
					$success = true;
				} else { 
					$success = false;
					$error = $lh->translationFor("unable_set_statistics").": ".$dbInstaller->error;
				}
				$currentState = "final_step";
				$_SESSION["installationStep"] = "final_step";
				// create a new installed.txt file to register that we have correctly installed Creamy.
				touch("./installed.txt");
			} else {
				$success = false;
				$currentState = "step_3";
				$_SESSION["installationStep"] = "step_3";
			}
		}
	} elseif (isset($_POST["submit_final_step"]) && $currentState == "final_step") { // final step: congratulations!		
		error_log("Creamy install: finished!");
		session_unset();
		// finally go to the index.
		header("Location: ./index.php");
		die();
	} else {
		session_unset();
	}

?>
<html class="lockscreen">
    <head>
        <meta charset="UTF-8">
        <title>Creamy</title>
        <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
        <link href="css/bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="css/font-awesome.min.css" rel="stylesheet" type="text/css" />
        <!-- Creamy style -->
        <link href="css/creamycrm.css" rel="stylesheet" type="text/css" />
        <link href="css/skins/skin-blue.min.css" rel="stylesheet" type="text/css" />
        
        <!-- Javascript -->
        <script src="js/jquery.min.js"></script>
        <script src="js/bootstrap.min.js" type="text/javascript"></script>
        <script src="js/plugins/timezone/jstz-1.0.4.min.js" type="text/javascript"></script>
    </head>
    
    <body class="login-page">
        <div class="login-box install-box" id="login-box">
			<div class="margin text-center">
				<img src="img/logo.png" width="64" height="64">
			</div>
	<?php if ($currentState == "already_installed") { ?>
			<div class="login-logo"><strong>Creamy</strong> <?php $lh->translateText("is_already_installed"); ?></div>
            <div class="login-box-body">
				<h3 class="login-box-msg"><?php $lh->translateText("oups"); ?></h3>
	            <p class="login-box-msg"><?php $lh->translateText("another_installation_creamy"); ?></p>
		          <div class="row">
		            <div class="col-xs-3"></div>
		            <div class="col-xs-6">
		              <button type="submit" onclick="window.location.href='index.php';" class="btn btn-primary btn-block btn-flat"><?php $lh->translateText("back_to_creamy"); ?></button>
		            </div><!-- /.col -->
		            <div class="col-xs-3"></div>
		          </div>
			</div>
	<?php } elseif ($currentState == "step1") { ?>
			<div class="login-logo"><?php $lh->translateText("installation_step_1_title"); ?></div>
            <div class="login-box-body">
	            <h3><?php $lh->translateText("welcome"); ?></h3>
	            <p><?php $lh->translateText("installation_process_steps"); ?></p>
	            <h3><?php $lh->translateText("database"); ?></h3>
	            <p><?php $lh->translateText("your_crm_needs_a_database"); ?></p>
	            <p style="color: red; "><?php $lh->translateText("installation_warning"); ?></p>
	            <form method="post">				
					<div class="form-group has-feedback">
						<input type="text" class="form-control" name="dbhost" placeholder="<?php $lh->translateText("database_host"); ?>"/>
						<span class="glyphicon glyphicon-cloud form-control-feedback"></span>
					</div>
					<div class="form-group has-feedback">
						<input type="text" class="form-control" name="dbname" placeholder="<?php $lh->translateText("database_name"); ?>"/>
						<span class="glyphicon glyphicon-credit-card form-control-feedback"></span>
					</div>
					<div class="form-group has-feedback">
						<input type="text" class="form-control" name="dbuser" placeholder="<?php $lh->translateText("database_user"); ?>"/>
						<span class="glyphicon glyphicon-user form-control-feedback"></span>
					</div>
					<div class="form-group has-feedback">
						<input type="password" class="form-control" name="dbpass" placeholder="<?php $lh->translateText("database_password"); ?>"/>
						<span class="glyphicon glyphicon-lock form-control-feedback"></span>
					</div>
					<div class="form-group">
						<p><?php $lh->translateText("detected_timezone"); ?></p>
	                    <?php
		                    // Timezones
		                    $tzs = \creamy\CRMUtils::getTimezonesAsArray();
							print '<select name="userTimeZone" id="userTimeZone" class="form-control">';
							foreach($tzs as $key => $value) { print '<option value="'.$key.'">'.$value.'</option>'; }
							print '</select>';
	                    ?>
	                </div>
	                <div class="form-group">
						<p><?php $lh->translateText("choose_language"); ?></p>
						<select name="desiredLanguage" id="desiredLanguage" class="form-control">
							<?php
							$locales = \creamy\LanguageHandler::getAvailableLanguages();
							foreach ($locales as $key => $value) {
								$selectedByDefault = ($key == $locale) ? "selected" : "";
								print('<option value="'.$key.'" '.$selectedByDefault.'> '.$value.'</option>');
							}
							?>
						</select>
	                </div>       
	            	<div name="error-message" style="color: red;">
	            	<?php 
	                	if (isset($error)) print $error."<div class='clearfix'>"; 
	            	?>
	            	</div>
	                <div class="row">
						<div class="col-xs-4"></div>
						<div class="col-xs-4"><button type="submit" name="submit_step1" id="submit_step1" class="btn btn-primary btn-block btn-flat"><?php $lh->translateText("start"); ?></button></div>
						<div class="col-xs-4"></div>
					</div>
	            </form>
        </div>

	<?php } elseif ($currentState == "step2") { ?>
			<div class="login-logo"><?php $lh->translateText("installation_step_2_title"); ?></div>
            <div class="login-box-body">
	            <h3><?php $lh->translateText("awesome"); ?></h3>
	            <p><?php $lh->translateText("database_access_checked"); ?></p>
	            <form method="post">				
					<div class="form-group has-feedback">
						<input type="text" class="form-control" name="adminName" placeholder="<?php $lh->translateText("admin_user_name"); ?>"/>
						<span class="glyphicon glyphicon-user form-control-feedback"></span>
					</div>
					<div class="form-group has-feedback">
						<input type="text" class="form-control" name="adminEmail" placeholder="<?php $lh->translateText("admin_user_email"); ?>"/>
						<span class="glyphicon glyphicon-envelope form-control-feedback"></span>
					</div>
					<div class="form-group has-feedback">
						<input type="password" class="form-control" name="adminPassword" placeholder="<?php $lh->translateText("admin_user_password"); ?>"/>
						<span class="glyphicon glyphicon-lock form-control-feedback"></span>
					</div>
					<div class="form-group has-feedback">
						<input type="password" class="form-control" name="adminPasswordCheck" placeholder="<?php $lh->translateText("admin_user_password_again"); ?>"/>
						<span class="glyphicon glyphicon-lock form-control-feedback"></span>
					</div>        
                	<div name="error-message" style="color: red;">
                	<?php 
	                	if (isset($error)) print ($error); 
                	?>
                	</div>
	                <div class="row">
						<div class="col-xs-3"></div>
						<div class="col-xs-6"><button type="submit" name="submit_step2" id="submit_step2" class="btn btn-primary btn-block btn-flat"><?php $lh->translateText("create_user"); ?></button></div>
						<div class="col-xs-3"></div>
					</div>
	            </form>
            </div>
          
    <?php } elseif ($currentState == "step3") { ?>  
			<div class="login-logo"><?php $lh->translateText("installation_step_3_title"); ?></div>
            <div class="login-box-body">
	            <h3><?php $lh->translateText("perfect"); ?></h3>
	            <p><?php $lh->translateText("lets_define_customers"); ?></p>
	            <form method="post">				
	                <input type="hidden" name="count" value="1" />
                    <div class="form-group">
                        <input type="radio" name="setup_customers" value="default" checked/> <?php $lh->translateText("contacts_and_clients_ok"); ?>
                    </div>
                    <div class="form-group">
                        <input type="radio" name="setup_customers" value="custom" /> <?php $lh->translateText("i_want_to_define_groups"); ?>
                    </div>
	                
	                <div name="custom-customers-selection" id="custom-customers-selection" style="display: none;">
	                    <div class="form-group">
	                        <input type="text" name="customCustomerGroupContacts" class="form-control" value="<?php $lh->translateText("contacts_predefined"); ?>" disabled/>
	                    </div>
	                    <div class="form-group" id="customCustomerGroup">
	                        <input type="text" autocomplete="off" name="customCustomerGroup1" id="customCustomerGroup1" class="form-control" placeholder="<?php $lh->translateText("customer_group"); ?> 1"/><button id="b1" class="btn add-more" type="button">+</button>
	                    </div>
	                </div>
                	<div name="error-message" style="color: red;">
                	<?php 
	                	if (isset($error)) print ($error); 
                	?>
                	</div>
	                <div class="row">
						<div class="col-xs-3"></div>
						<div class="col-xs-6"><button type="submit" name="submit_step3" id="submit_step3" class="btn btn-primary btn-block btn-flat"><?php $lh->translateText("create"); ?></button></div>
						<div class="col-xs-3"></div>
					</div>
	            </form>
            </div>
    <?php } elseif ($currentState == "final_step") { ?>  
			<div class="login-logo"><?php $lh->translateText("finished"); ?></div>
            <div class="login-box-body">
	            <h3><?php $lh->translateText("everythings_ready"); ?></h3>
	            <p><?php $lh->translateText("ready_to_start_creamy"); ?></p>
	        	<div name="error-message" style="color: red;">
		        	<?php 
		            	if (isset($error)) print ($error); 
		        	?>
	        	</div>
	            <h3><?php $lh->translateText("enable_creamy_events"); ?></h3>
				<p><?php $lh->translateText("creamy_events_description"); ?></p>
				<pre><?php require_once('php/CRMUtils.php'); ?>
# Creamy event job scheduling
0 * * * * php "<?php print \creamy\CRMUtils::creamyBaseDirectoryPath(true)."job-scheduler.php"; ?>" &>/dev/null</pre>
				<p><?php $lh->translateText("dont_know_cronjob"); ?></p><br>
				<form method="post">	
	                <div class="row">
						<div class="col-xs-3"></div>
						<div class="col-xs-6"><a href="index.php" class="btn bg-light-blue btn-block"><?php $lh->translateText("start_using_creamy"); ?></a></div>
						<div class="col-xs-3"></div>
					</div>
	            </form>
            </div>
        	
	<?php } ?>
            <div class="margin text-center">
                <span><?php $lh->translateText("never_heard_of_creamy"); ?></span>
                <br/>
                <button class="btn bg-red btn-circle" onclick="window.location.href='http://creamycrm.com'"><i class="fa fa-globe"></i></button>
                <button class="btn bg-light-blue btn-circle" onclick="window.location.href='https://github.com/DigitalLeaves/Creamy'"><i class="fa fa-github"></i></button>
                <button class="btn bg-aqua btn-circle" onclick="window.location.href='https://twitter.com/creamythecrm'"><i class="fa fa-twitter"></i></button>
            </div>
        </div>    
		<script type="text/javascript">
		$(document).ready(function(){
			// detect timezone
			var tz = jstz.determine(); // Determines the time zone of the browser client
			var timezone = tz.name(); //'Asia/Kolhata' for Indian Time.
			$('#userTimeZone').val(timezone);
			
			// show custom customers selection.
		    $('input:radio[name="setup_customers"]').change(function(e){
			    if (this.value == 'custom') {
					$('#custom-customers-selection').fadeIn();
			    } else if (this.value == 'default') {
			        $('#custom-customers-selection').fadeOut();
			    }
		    });
		    
		    // add more custom clients
		    var next = 1;
		    $(".add-more").click(function(e){
		        e.preventDefault();
		        var addto = "#customCustomerGroup" + next;
		        var addRemove = "#customCustomerGroup" + (next);
		        next = next + 1;
		        var newIn = '<input autocomplete="off" type="text" name="customCustomerGroup'+next+'" id="customCustomerGroup'+next+'" class="input form-control" placeholder="<?php $lh->translateText("customer_group"); ?> '+next+'"/>';
		        var newInput = $(newIn);
		        var removeBtn = '<button id="remove' + (next - 1) + '" class="btn btn-danger remove-me" >-</button></div><div id="field">';
		        var removeButton = $(removeBtn);
		        $(addto).after(newInput);
		        $(addRemove).after(removeButton);
		        $("#customCustomerGroup" + next).attr('data-source',$(addto).attr('data-source'));
		        $("#count").val(next);  

	         $('.remove-me').click(function(e){
		        e.preventDefault();
		        var fieldNum = this.id.charAt(this.id.length-1);
		        var fieldID = "#customCustomerGroup" + fieldNum;
		        $(this).remove();
		        $(fieldID).remove();
		    });

		    });

		});
		</script>
    </body>
</html>