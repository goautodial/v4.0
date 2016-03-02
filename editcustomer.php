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

require_once('./php/CRMDefaults.php');
require_once('./php/UIHandler.php');
require_once('./php/DbHandler.php');
require_once('./php/LanguageHandler.php');
require('./php/Session.php');

// initialize structures
$ui = \creamy\UIHandler::getInstance();
$lh = \creamy\LanguageHandler::getInstance();
$user = \creamy\CreamyUser::currentUser();

$customerType = NULL;
$customerid = NULL;
if (isset($_GET["customer_type"])) { $customerType = $_GET["customer_type"]; }
if (isset($_GET["customerid"])) { $customerid = $_GET["customerid"]; }
?>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Creamy</title>
        <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
        <link href="css/bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="css/font-awesome.min.css" rel="stylesheet" type="text/css" />
        <!-- Ionicons -->
        <link href="css/ionicons.min.css" rel="stylesheet" type="text/css" />
        <!-- bootstrap wysihtml5 - text editor -->
        <link href="css/bootstrap-wysihtml5/bootstrap3-wysihtml5.min.css" rel="stylesheet" type="text/css" />
        <!-- Creamy style -->
        <link href="css/creamycrm.css" rel="stylesheet" type="text/css" />
        <?php print $ui->creamyThemeCSS(); ?>

        <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
          <script src="js/html5shiv.js"></script>
          <script src="js/respond.min.js"></script>
        <![endif]-->
        <script src="js/jquery.min.js"></script>
        <script src="js/bootstrap.min.js" type="text/javascript"></script>
        <script src="js/jquery-ui.min.js" type="text/javascript"></script>
		<!-- Forms and actions -->
		<script src="js/jquery.validate.min.js" type="text/javascript"></script>
        <!-- Creamy App -->
        <script src="js/app.min.js" type="text/javascript"></script>

    </head>
    <?php print $ui->creamyBody(); ?>
        <div class="wrapper">
        <!-- header logo: style can be found in header.less -->
		<?php print $ui->creamyHeader($user); ?>
            <!-- Left side column. contains the logo and sidebar -->
			<?php print $ui->getSidebar($user->getUserId(), $user->getUserName(), $user->getUserRole(), $user->getUserAvatar()); ?>

            <!-- Right side column. Contains the navbar and content of the page -->
            <aside class="right-side">
                <!-- Content Header (Page header) -->
                <section class="content-header">
                    <h1>
                        <?php $lh->translateText("customers_and_contacts_management"); ?>
                        <small><?php $lh->translateText("personal_data_edition"); ?></small>
                    </h1>
                    <ol class="breadcrumb">
                        <li><a href="./index.php"><i class="fa fa-edit"></i> <?php $lh->translateText("home"); ?></a></li>
                        <?php 
	                        if (isset($customerType)) {
	                        	print ('<li><a href="customerslist.php?customer_type='.$customerType.'"> '.$lh->translationFor("customer_list").'</a></li>');
	                        }
                        ?>
                        <li class="active"><?php $lh->translateText("modify"); ?></li>
                    </ol>
                </section>

                <!-- Main content -->
                <section class="content">
					<!-- standard custom edition form -->
					<?php
					$customerobj = NULL;
					$errormessage = NULL;
					
					if (isset($customerid) && isset($customerType)) {
						$db = new \creamy\DbHandler();
						$customerobj = $db->getDataForCustomer($customerid, $customerType);
					} else {
			    		$errormessage = $lh->translationFor("some_fields_missing");
					}
					
					if (!empty($customerobj)) {

						// buttons at bottom (only for writing+ permissions)
						$buttons = "";
						if ($user->userHasWritePermission()) {
							$buttons = $ui->buttonWithLink("modifyCustomerDeleteButton", $customerid, $lh->translationFor("delete"), "button", "times", CRM_UI_STYLE_DANGER);
							$buttons .= $ui->buttonWithLink("modifyCustomerOkButton", "", $lh->translationFor("save"), "submit", "check", CRM_UI_STYLE_PRIMARY, "pull-right");
							$buttons = $ui->singleFormGroupWrapper($buttons);
						}
						// form fields
						$formcontent = $ui->customerFieldsForForm($customerobj, $customerType, $customerid);

						// generate form: header
						$form = $ui->formWithCustomFooterButtons("modifycustomerform", $formcontent, $buttons, "modifycustomerresult");
						
						// generate box
						$boxTitle = $lh->translationFor("insert_new_data");
						$formBox = $ui->boxWithContent($boxTitle, $form);
						// print our modifying customer box.
						print $formBox;
					} else {
						print $ui->boxWithMessage($lh->translationFor("error"), $lh->translationFor("some_fields_missing"));
					}
					?>
					<!-- modules addons via hooks -->
					<?php print $ui->customerDetailModuleHooks($customerid, $customerType); ?>
                </section><!-- /.content -->
            </aside><!-- /.right-side -->
            <?php print $ui->creamyFooter(); ?>
        </div><!-- ./wrapper -->

		<!-- Modal Dialogs -->
		<?php include_once "./php/ModalPasswordDialogs.php" ?>

		<script type="text/javascript">
			$(document).ready(function() {				
				/** 
				 * Modifies a customer
			 	 */
				$("#modifycustomerform").validate({
					submitHandler: function() {
						//submit the form
							$("#resultmessage").html();
							$("#resultmessage").fadeOut();
							$.post("./php/ModifyCustomer.php", //post
							$("#modifycustomerform").serialize(), 
								function(data){
									//if message is sent
									if (data == '<?php print CRM_DEFAULT_SUCCESS_RESPONSE; ?>') {
									<?php 
									$errorMsg = $ui->dismissableAlertWithMessage($lh->translationFor("data_successfully_modified"), true, false);
									print $ui->fadingInMessageJS($errorMsg, "modifycustomerresult"); 
									?>				
									} else {
									<?php 
									$errorMsg = $ui->dismissableAlertWithMessage($lh->translationFor("error_modifying_data"), false, true);
									print $ui->fadingInMessageJS($errorMsg, "modifycustomerresult"); 
									?>
									}
									//
								});
						return false; //don't let the form refresh the page...
					}					
				});
				
				/**
				 * Deletes a customer
				 */
				 $("#modifyCustomerDeleteButton").click(function (e) {
					var r = confirm("<?php $lh->translateText("are_you_sure"); ?>");
					e.preventDefault();
					if (r == true) {
						var customerid = $(this).attr('href');
						$.post("./php/DeleteCustomer.php", $("#modifycustomerform").serialize() ,function(data){
							if (data == "<?php print CRM_DEFAULT_SUCCESS_RESPONSE; ?>") { 
								alert("<?php $lh->translateText("customer_successfully_deleted"); ?>");
								window.location = "index.php";
							}
							else { alert ("<?php $lh->translateText("unable_delete_customer"); ?>: "+data); }
						});
					}
				 });
				 
			});
		</script>

    </body>
</html>
