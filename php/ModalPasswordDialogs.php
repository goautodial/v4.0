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
require_once('CRMDefaults.php');
require_once('LanguageHandler.php');
if (!isset($lh)) { $lh = \creamy\LanguageHandler::getInstance(); }
require_once('UIHandler.php');
if (!isset($ui)) { $ui = \creamy\UIHandler::getInstance(); }
require_once('Session.php');
$user = \creamy\CreamyUser::currentUser();
?>
<script src="js/jquery.validate.min.js" type="text/javascript"></script>
<script>
$(document).ready(function() {
	jQuery.validator.addMethod("notEqual", function(value, element, param) {
		return this.optional(element) || value != param;
	}, "Please specify a value");
	
	/**
	 * Changes user password
	 */
	$("#passwordform").validate({
	 	rules: {
			userid: "required",
			old_password: {
				required: true,
				notEqual: "<?=$lh->translationFor("insert_old_password")?>"
			},
			new_password_1: {
				required: true,
				notEqual: "<?=$lh->translationFor("insert_new_password")?>"
			},
			new_password_2: {
				minlength: 6,
				equalTo: "#new_password_1",
				notEqual: "<?=$lh->translationFor("insert_new_password_again")?>"
			}
   		},
		submitHandler: function() {
			//submit the form
			$("#changepasswordresult").html();
			$("#changepasswordresult").fadeOut();
			$.post("./php/ChangePassword.php", //post
			$("#passwordform").serialize(), 
			function(data) {
				//if message is sent
				if (data == '<?php print CRM_DEFAULT_SUCCESS_RESPONSE; ?>') {
					<?php 
					// show ok message
					$errorMsg = $ui->dismissableAlertWithMessage($lh->translationFor("password_successfully_changed"), true, false);
					print $ui->fadingInMessageJS($errorMsg, "changepasswordresult"); 
					?>
					$("#changepassCancelButton").html("<i class=\"fa fa-check-circle\"></i> <?php $lh->translateText("exit"); ?>");
					$("#changepassOkButton").fadeOut();
				} else {
					<?php 
					$errorMsg = $ui->dismissableAlertWithMessage($lh->translationFor("error_changing_password"), false, true);
					print $ui->fadingInMessageJS($errorMsg, "changepasswordresult"); 
					?>
				}
				//
			});
			return false; //don't let the form refresh the page...
		}					
	});
	/** Reset form fields when clicked */
	$('#change-password-toggle').click(function() {
		$('#passwordform')[0].reset();
		$("#changepasswordresult").hide();
		$("#changepassOkButton").fadeIn();
		$("#changepassCancelButton").html("<i class=\"fa fa-times\"></i> <?php $lh->translateText("cancel"); ?>");
	});
});
</script>
<!-- MODAL FORM -->
<?php
// form fields
// old password 
$old_f = $ui->singleFormGroupWithInputGroup($ui->singleFormInputElement("old_password", "old_password", "password", $lh->translationFor("insert_old_password"), null, "lock", true));
// new password 
$new1_f = $ui->singleFormGroupWithInputGroup($ui->singleFormInputElement("new_password_1", "new_password_1", "password", $lh->translationFor("insert_new_password"), null, "lock", true));
// new password (again)
$new2_f = $ui->singleFormGroupWithInputGroup($ui->singleFormInputElement("new_password_2", "new_password_2", "password", $lh->translationFor("insert_new_password_again"), null, "lock", true));
// hidden user id
$hidden_f = $ui->hiddenFormField("userid", $user->getUserId());
// fields
$fields = $old_f.$new1_f.$new2_f.$hidden_f;
// buttons
$okButton = $ui->buttonWithLink("changepassOkButton", "", $lh->translationFor("change_password"), "submit", "check-circle", CRM_UI_STYLE_DEFAULT, "pull-right");
$koButton = $ui->modalDismissButton("changepassCancelButton", $lh->translationFor("cancel"), "left", true);
$buttons = $okButton.$koButton;

// form
$form = $ui->modalFormStructure("change-password-dialog-modal", "passwordform", $lh->translationFor("change_password"), null, $fields, $buttons, "lock", "changepasswordresult", '');
print $form;
?>
<script>
$(function() {
	$(this).find(".modal-body").css('margin', '15px');
});
</script>
