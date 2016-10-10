
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

	$db = new \creamy\DbHandler();
	$ui = \creamy\UIHandler::getInstance();    
    $lh = \creamy\LanguageHandler::getInstance();
	$user = \creamy\CreamyUser::currentUser();
     
    if (isset($_POST["user_id"])) { $userid = $_POST["user_id"]; }
    else { $userid = $user->getUserId(); }
    
    
?>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Creamy</title>
        <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
        
		<?php print $ui->standardizedThemeCSS(); ?>
		
        <?php print $ui->creamyThemeCSS(); ?>

        <script type="text/javascript">
			$(window).ready(function() {
				$(".preloader").fadeOut("slow");
			});
		</script>
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
                        <?php $lh->translateText("users_management"); ?>
                        <small><?php $lh->translateText("edit_user_data"); ?></small>
                    </h1>
                    <ol class="breadcrumb">
                        <li><a href="./index.php"><i class="fa fa-edit"></i> <?php $lh->translateText("home"); ?></a></li>
                        <li class="active"><?php $lh->translateText("edit_user"); ?></li>
                    </ol>
                </section>

                <!-- Main content -->
                <section class="content">
	                <?php // build edit user form
		               	$userobj = NULL;
						$errormessage = NULL;
						
						if (!empty($userid)) {
							if (($user->getUserId() == $userid) || ($user->userHasAdminPermission())) { 
				    			// if it's the same user or we have admin privileges.
				    			$userobj = $ui->goGetUserInfo($userid);
							} else {
				    			$errormessage = $lh->translationFor("not_permission_edit_user_information");
							}
						} else {
				    		$errormessage = $lh->translationFor("unknown_error");
						}
						
						if (!empty($userobj) && $userobj->result == 'success') {
							$userdata = $userobj->data;
							// build fields
							// modify id (hidden).
							$hidden_f = $ui->hiddenFormField("modifyid", $userid);
							// name
							// $name_f = $ui->singleFormGroupWithInputGroup($ui->singleFormInputElement("name", "name", "text", $lh->translationFor("name"), $userobj["name"], "user", true));
							$name_f = $ui->singleFormGroupWithInputGroup($ui->singleFormInputElement("name", "name", "text", $lh->translationFor("name"), $userdata->user, "user", true, true));
							// email
							$email_f = $ui->singleFormGroupWithInputGroup($ui->singleFormInputElement("email", "email", "email", $lh->translationFor("email") . " (" . $lh->translationFor("optional") . ")", $userdata->email, "envelope-square", false));
							// old password 
							$old_f = $ui->singleFormGroupWithInputGroup($ui->singleFormInputElement("old_password", "old_password", "password", $lh->translationFor("insert_old_password"), null, "lock", true));
							// new password 
							$new1_f = $ui->singleFormGroupWithInputGroup($ui->singleFormInputElement("new_password_1", "new_password_1", "password", $lh->translationFor("insert_new_password"), null, "lock", true));
							// new password (again)
							$new2_f = $ui->singleFormGroupWithInputGroup($ui->singleFormInputElement("new_password_2", "new_password_2", "password", $lh->translationFor("insert_new_password_again"), null, "lock", true));
							// phone 
							//$phone_text = $lh->translationFor("phone").' ('.$lh->translationFor("optional").')';
							// $phone_f = $ui->singleFormGroupWithInputGroup($ui->singleFormInputElement("phone", "phone", "text", $phone_text, $userobj["phone"], "phone"));
							//$phone_f = $ui->singleFormGroupWithInputGroup($ui->singleFormInputElement("phone", "phone", "text", $phone_text, $userdata->phone_login, "phone"));
							// avatar (optional)
							// $currentUserAvatar = empty($userobj["avatar"]) ? "" : $ui->imageWithData($userobj["avatar"], "img-circle", array("width" => 100, "height" => 100), "User image");
							// $avatar_l = $lh->translationFor("user_avatar").' ('.$lh->translationFor("optional").')';
							// $avatar_b = $lh->translationFor("choose_image");
							// $avatar_f = $ui->singleFormGroupWithFileUpload("avatar", "avatar", $currentUserAvatar, $avatar_l, $avatar_b);
							// if requesting user is admin, we can change the user role
							//$setUserRoleCode = "";
							//if ($user->userHasAdminPermission()) {
							//	// $userRolesAsFormSelect = $ui->getUserRolesAsFormSelect($userobj["role"]);
							//	$userRolesAsFormSelect = $ui->getUserRolesAsFormSelect($userobj["user_level"]);
							//	$setUserRoleCode = $ui->singleFormGroupWrapper($userRolesAsFormSelect, $lh->translationFor("user_role"));
							//}	

							// generate the form
							$fields = $hidden_f.$name_f.$email_f.$old_f.$new1_f.$new2_f;
							// generate and show the box
							$box = $ui->boxWithForm("modifyuser", $lh->translationFor("insert_new_data"), $fields, $lh->translationFor("edit_user"));
							print $box;
						} else {
							print $ui->calloutErrorMessage($errormessage);
						}
		            ?>
                </section><!-- /.content -->
            </aside><!-- /.right-side -->
			<?php print $ui->getRightSidebar($user->getUserId(), $user->getUserName(), $user->getUserAvatar()); ?>
            <?php print $ui->creamyFooter(); ?>
        </div><!-- ./wrapper -->
		
		<?php print $ui->standardizedThemeJS();?>

		<!-- Modal Dialogs -->
		<?php include_once "./php/ModalPasswordDialogs.php" ?>

		<script type="text/javascript">
		$(document).ready(function() {
			/** 
			 * modifies a user.
		 	 */
			$("#modifyuser").validate({
				submitHandler: function(e) {
					//submit the form
						$("#resultmessage").html();
						$("#resultmessage").hide();
						var formData = new FormData(e);
		
						$.ajax({
						  url: "./php/ModifyUser.php",
						  data: formData,
						  processData: false,
						  contentType: false,
						  type: 'POST',
						  success: function(data) {
								if (data == '<?php print CRM_DEFAULT_SUCCESS_RESPONSE; ?>') {
								<?php 
								$errorMsg = $ui->dismissableAlertWithMessage($lh->translationFor("data_successfully_modified"), true, false);
								print $ui->fadingInMessageJS($errorMsg, "resultmessage"); 
								?>
								} else {
								<?php 
								$errorMsg = $ui->dismissableAlertWithMessage($lh->translationFor("error_modifying_data"), false, true);
								print $ui->fadingInMessageJS($errorMsg, "resultmessage"); 
								?>
								}
						    }
						});
					return false; //don't let the form refresh the page...
				}					
			});
			 
		});
		</script>

    </body>
</html>
