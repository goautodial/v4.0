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
require_once('./php/LanguageHandler.php');
require_once('./php/DbHandler.php');
require_once('./php/CRMUtils.php');
include('./php/Session.php');

$ui = \creamy\UIHandler::getInstance();
$lh = \creamy\LanguageHandler::getInstance();
$db = new \creamy\DbHandler();
$user = \creamy\CreamyUser::currentUser();

// get parameters
if (isset($_GET["reply_text"])) {
	$reply_text = $_GET["reply_text"];
} else $reply_text = "";

if (isset($_GET["reply_user"])) {
	$reply_user = $_GET["reply_user"];
} else $reply_user = null;

if (isset($_GET["reply_subject"])) {
	$reply_subject = $_GET["reply_subject"];
} else $reply_subject = "";

$folder = MESSAGES_GET_INBOX_MESSAGES;
$smtp_status = $ui->API_getSMTPActivation();
?>
<html>
  <head>
    <meta charset="UTF-8">
    <title><?php print $lh->translationFor("compose_message"); ?> </title>
    <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
	
	<!-- multiple emails plugin -->
    <link href="css/multiple-emails/multiple-emails.css" rel="stylesheet" type="text/css" />
	
	<?php print $ui->standardizedThemeCSS(); ?>
    <?php print $ui->creamyThemeCSS(); ?>
	
    <!-- Bootstrap WYSIHTML5 -->
    <script src="js/plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.all.min.js" type="text/javascript"></script>
    <!-- Multi file upload -->
    <script src="js/plugins/multifile/jQuery.MultiFile.min.js" type="text/javascript"></script>
    <!-- Multiple emails -->
    <script src="js/plugins/multiple-emails/multiple-emails.js" type="text/javascript"></script>
	
	<!-- SELECT2-->
   		<link rel="stylesheet" src="js/dashboard/select2/dist/css/select2.css">
   		<link rel="stylesheet" src="js/dashboard/select2-bootstrap-theme/dist/select2-bootstrap.css">
   		<!-- SELECT2-->
   		<script src="js/dashboard/select2/dist/js/select2.js"></script>
  </head>
  <?php print $ui->creamyBody(); ?>
    <div class="wrapper">
      <!-- header logo: style can be found in header.less -->
	  <?php print $ui->creamyHeader($user); ?>
      <!-- Left side column. contains the logo and sidebar -->
        <!-- Left side column. contains the logo and sidebar -->
		<?php print $ui->getSidebar($user->getUserId(), $user->getUserName(), $user->getUserRole(), $user->getUserAvatar()); ?>

      <!-- Content Wrapper. Contains page content -->
      <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
          <h1>
            <?php $lh->translateText("messages"); ?>
            <small><?php $lh->translateText("messaging_system"); ?></small>
          </h1>
            <ol class="breadcrumb">
                <li><a href="./index.php"><i class="fa fa-envelope"></i> <?php $lh->translateText("home"); ?></a></li>
                <li><a href="./messages.php?folder=<?php print $folder; ?>"><?php $lh->translateText("messages"); ?></a></li>
                <li class="active"><?php $lh->translateText("message"); ?></li>
            </ol>
        </section>

        <!-- Main content -->
        <section class="content">
	        <div class="row">
	        <!-- left side folder list column -->
            <div class="col-md-3">
              <a href="messages.php" class="btn btn-primary btn-block margin-bottom"><?php $lh->translateText("back_to_inbox"); ?></a>
              <div class="box box-solid">
                <div class="box-header with-border">
                  <h3 class="box-title"><?php $lh->translateText("folders"); ?></h3>
                </div>
                <div class="box-body no-padding">
					<?php print $ui->getMessageFoldersAsList($folder); ?>
                </div><!-- /.box-body -->
              </div><!-- /. box -->
            </div><!-- /.col -->

            <!-- main content right side column -->
            <div class="col-md-9">
              <div class="box box-default">
	            <form method="POST" id="send-message-form" enctype="multipart/form-data">
                <div class="box-header with-border">
                  <h3 class="box-title"><?php $lh->translateText("compose_new_message"); ?></h3>
                </div><!-- /.box-header -->
                <div class="box-body">
                  <input type="hidden" id="fromuserid" name="fromuserid" value="<?php print $user->getUserId(); ?>">
				  <div class="form-group">
				    <?php print $ui->generateSendToUserSelect($user->getUserId(), false, null, $reply_user); ?>
				  </div>
				  <?php if($smtp_status == 1){?>
                  <div class="form-group">
                    <input id="external_recipients" name="external_recipients" class="form-control" placeholder="<?php $lh->translateText("external_message_recipients"); ?>"/>
                  </div>
				  <?php } ?>
                  <div class="form-group">
                    <input id="subject" name="subject" class="form-control required" placeholder="<?php $lh->translateText("subject"); ?>:" value="<?php print $reply_subject; ?>"/>
                  </div>
                  <div class="form-group">
                    <textarea id="compose-textarea" name="message" class="form-control required" style="height: 300px" placeholder="<?php $lh->translateText("write_your_message_here"); ?>">
                    <?php print $reply_text; ?>
                    </textarea>
                  </div>
                  <div class="form-group">
                    <div class="btn btn-default btn-file">
                      <i class="fa fa-paperclip"></i> <?php $lh->translateText("attachment"); ?>
                      <input type="file" class="attachment" name="attachment[]"/>
                    </div>
                    <p class="help-block"><?php print $lh->translationFor("max")." ".CRM_MAX_ATTACHMENT_FILESIZE; ?>MB</p>
                  </div>
                </div><!-- /.box-body -->
                <div class="box-footer" id="attachment-list">
	            <label><?php $lh->translateText("attachments"); ?>: </label>
                </div>
                <div class="box-footer" id="compose-mail-results">
                </div>
                <div class="box-footer">
                  <div class="pull-right">
                    <button type="submit" class="btn btn-primary" id="send_button"><i class="fa fa-envelope-o"></i> <?php $lh->translateText("send"); ?></button>
                  </div>
                  <button class="btn btn-default" id="compose-mail-discard"><i class="fa fa-times"></i> <?php $lh->translateText("discard"); ?></button>
                  <!-- Module hook footer -->
                  <?php print $ui->getComposeMessageFooter(); ?>
                </div><!-- /.box-footer -->
	            </form> <!-- /.form -->
              </div><!-- /. box -->
            </div><!-- /.col -->
          </div><!-- /.row -->
        </section><!-- /.content -->
      </div><!-- /.content-wrapper -->
	  <?php print $ui->getRightSidebar($user->getUserId(), $user->getUserName(), $user->getUserAvatar()); ?>
    </div><!-- ./wrapper -->
    
    <!-- WYSIHTML5 edition -->
    <script type="text/javascript"> $("#compose-textarea").wysihtml5(); </script>
	
	<?php print $ui->standardizedThemeJS(); ?>
	<script type="text/javascript">
		$(document).ready(function() {
			$('#send_button').prop("disabled", true);
			
			/* initialize select2 */
			$('.select2').select2({
				theme: 'bootstrap'
			});
			
			// external recipients
			$('#external_recipients').multiple_emails();

			// attachments
			$('.attachment').MultiFile({
			    max: 5,
			    //accept: 'jpg|jpeg|png|gif|pdf|doc|pages|numbers|xls|docx|xlsx|mp4|mpg|mpeg|avi|m4v|txt|rdf|mp3|ogg|zip|html',
				list: '#attachment-list',
				STRING: {
					remove: '<i class="fa fa-times"></i>'
				}
			});
			
			 /* if an agent is selected */
			$('#touserid').on('change', function() {
				var external_email = $('#external_recipients').val();
				if(this.value !== "0") {
					$('#send_button').attr("disabled", false);
				}else if(external_email !== ""){
					$('#send_button').attr("disabled", false);
				}else{
					$('#send_button').attr("disabled", true);
				}
			});
			
			 /* if external email is given */
			$('#external_recipients').on('change', function() {
				var touserid = $('#touserid').val();
				if(this.value !== "[]") {
					$('#send_button').attr("disabled", false);
				}else if(touserid !== "0"){
					$('#send_button').attr("disabled", false);
				}else{
					$('#send_button').attr("disabled", true);
				}
			});
			
			 /* if external email is given */
			 $(document).on("click",".multiple_emails-close",function(e) {
				var external_email = $('#external_recipients').val();
				var touserid = $('#touserid').val();
				
				if(external_email !== "[]") {
					$('#send_button').attr("disabled", false);
				}else if(touserid !== "0"){
					$('#send_button').attr("disabled", false);
				}else{
					$('#send_button').attr("disabled", true);
				}
			});
			
			
			// send a message
			$("#send-message-form").validate({
				rules: {
                    mimeType: "multipart/form-data",
					subject: "required",
					message: "required"
				},
			    messages: {
			        touserid: "<?php $lh->translateText("you_must_choose_user"); ?>",
				},
				submitHandler: function() {
					// file uploads only allowed on modern browsers (sorry IE < 10).
				    var form = $("#send-message-form");
				    var formdata = false;
					if (window.FormData){
						formdata = new FormData(form[0]);
					}
					<?php
						$okMsg = $ui->dismissableAlertWithMessage($lh->translationFor("message_successfully_sent"), true, false);
						$koMsg = $ui->dismissableAlertWithMessage($lh->translationFor("unable_send_message"), false, true);
					?>
					//submit the form
					
					$("#compose-mail-results").html();
					$("#compose-mail-results").hide();
					$('#send_button').html('<i class="fa fa-envelope-o"></i> <?php print $lh->translationFor("sending"); ?>');
					$('#send_button').prop("disabled", true);
					
					$.ajax({
				        url         : 'php/SendMessage.php',
				        data        : formdata ? formdata : form.serialize(),
				        cache       : false,
				        contentType : false,
				        processData : false,
				        type        : 'POST',
				        success     : function(data, textStatus, jqXHR){
						
						$('#send_button').html('<i class="fa fa-envelope-o"></i> <?php print $lh->translationFor("send"); ?>');
						$('#send_button').attr("disabled", false);
							
							if (data == 'success') {
								$("#compose-mail-results").html('<?php print $okMsg; ?>');
								$("#compose-mail-results").fadeIn(); //show confirmation message
								$("#send-message-form")[0].reset();
								$("#select2-touserid-container").text('<?php print $lh->translationFor("send_this_message_to"); ?>');
								
							} else { // failure
								<?php if($smtp_status != 1){?>
								$("#compose-mail-results").html('<?php print $koMsg; ?>');
								$("#compose-mail-results").fadeIn(); //show confirmation message
								<?php }?>
							}
				        }, error: function(jqXHR, textStatus, errorThrown) {
							<?php if($smtp_status != 1){?>
							$("#compose-mail-results").html('<?php print $koMsg; ?>');
							$("#compose-mail-results").fadeIn(); //show confirmation message
							<?php }?>
				        }
				    });
					<?php if($smtp_status == 1){?>
					//send to actual email account
					$.ajax({
				        url         : 'php/send_mail.php',
				        data        : formdata ? formdata : form.serialize(),
				        cache       : false,
				        contentType : false,
				        processData : false,
				        type        : 'POST',
				        success     : function(data, textStatus, jqXHR){
						
						$('#send_button').html('<i class="fa fa-envelope-o"></i> <?php print $lh->translationFor("send"); ?>');
						$('#send_button').attr("disabled", false);
						
							if (data == 'success') {
								sweetAlert('<?php print $lh->translationFor("message_sent"); ?>', '<?php print $lh->translationFor("message_sent_msg"); ?>', 'success');
								$("#send-message-form")[0].reset();
							}
							else if (data == 'no email account'){
								sweetAlert("<?php print $lh->translationFor("no_email_account"); ?>","<?php print $lh->translationFor("no_email_account_msg"); ?>", "warning");
							}
							else { // failure
								sweetAlert('<?php print $lh->translationFor("message_error"); ?>' + data, 'error');
							}
				        }, error: function(jqXHR, textStatus, errorThrown) {
							sweetAlert('<?php print $lh->translationFor("system_error"); ?>', 'error');
				        }
				    });
					<?php } ?>
					return false; //don't let the form refresh the page...
				}
			});
			
			$('#send-message-form').on('keyup keypress', function(e) {
				var keyCode = e.keyCode || e.which;
				if (keyCode === 13) {
				  e.preventDefault();
				  return false;
				}
			});
			
			// discard message
			$('#compose-mail-discard').click(function(e) { history.back(); });
			
		});
		// hooks
		<?php print $ui->getComposeMessageActionJS(); ?>		    
	</script>
	<?php print $ui->creamyFooter(); ?>
  </body>
</html>