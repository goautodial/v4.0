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
if (isset($_GET["folder"])) {
	$folder = $_GET["folder"];
} else $folder = MESSAGES_GET_INBOX_MESSAGES;
if ($folder < 0 || $folder > MESSAGES_MAX_FOLDER) { $folder = MESSAGES_GET_INBOX_MESSAGES; }

if (isset($_GET["message_id"])) {
	$messageid = $_GET["message_id"];
} else $messageid = NULL;

// get the message
$message = null;
if (isset($folder) && isset($messageid)) {
	// retrieve data about the message and sending user.
	$message = $db->getSpecificMessage($user->getUserId(), $messageid, $folder);
	$fromUser = $db->getDataForUser($message["user_from"]);
	// mark the message as read
	$db->markMessagesAsRead($user->getUserId(), array($messageid), $folder);	
}
?>
<html>
  <head>
    <meta charset="UTF-8">
    <title>Read Message</title>
    <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
    <link href="./css/bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="./css/font-awesome.min.css" rel="stylesheet" type="text/css" />
    <!-- Creamy style -->
    <link href="./css/creamycrm.css" rel="stylesheet" type="text/css" />
	
	<?php print $ui->standardizedThemeCSS(); ?>
    <?php print $ui->creamyThemeCSS(); ?>

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
      <script src="js/html5shiv.js"></script>
      <script src="js/respond.min.js"></script>
    <![endif]-->

	<!-- Javascript -->
    <script src="js/jquery.min.js"></script>
    <script src="js/bootstrap.min.js" type="text/javascript"></script>
    <script src="js/jquery-ui.min.js" type="text/javascript"></script>
    <!-- Print page -->
    <script src="js/plugins/printThis/printThis.js" type="text/javascript"></script>
    <!-- Creamy App -->
    <script src="js/app.min.js" type="text/javascript"></script>

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
              <a href="composemail.php" class="btn btn-primary btn-block margin-bottom"><?php $lh->translateText("new_message"); ?></a>
              <div class="box box-solid">
                <div class="box-header with-border">
                  <h3 class="box-title"><?php print $lh->translationFor("folders"); ?></h3>
                </div>
                <div class="box-body no-padding">
					<?php print $ui->getMessageFoldersAsList($folder); ?>
                </div><!-- /.box-body -->
              </div><!-- /. box -->
            </div><!-- /.col -->

            <!-- main content right side column -->
            <div class="col-md-9">
              <div class="box box-default" id="message-full-box">
                <div class="box-header with-border non-printable">
                  <h3 class="box-title"><?php print $lh->translationFor("read_message"); ?></h3>
                </div><!-- /.box-header -->
                <div class="box-body no-padding">
                  <div class="mailbox-read-info">
                    <h3><?php print $message["subject"]; ?></h3>
                    <h5><?php print $lh->translationFor("from")." ".(isset($fromUser["user"]) ? $fromUser["user"] : $lh->translationFor("unknown")); ?> 
                    <span class="mailbox-read-time pull-right"><?php print $ui->relativeTime($message["date"]) ?></span></h5>
                  </div><!-- /.mailbox-read-info -->
                  <div class="mailbox-controls with-border text-center non-printable">
                    <div class="btn-group">
                      <button class="btn btn-default btn-sm mail-delete" data-toggle="tooltip" title="Delete"><i class="fa fa-trash-o"></i></button>
					  <?php if (isset($fromUser)) { ?>
                      <button class="btn btn-default btn-sm mail-reply" data-toggle="tooltip" title="Reply"><i class="fa fa-reply"></i></button>
                      <button class="btn btn-default btn-sm mail-forward" data-toggle="tooltip" title="Forward"><i class="fa fa-share"></i></button>
                      <?php } ?>
                    </div><!-- /.btn-group -->
                    <button class="btn btn-default btn-sm mail-print" data-toggle="tooltip" title="Print"><i class="fa fa-print"></i></button>
                  </div><!-- /.mailbox-controls -->
                  <div class="mailbox-read-message" id="mailbox-message-text">
	                <?php print $message["message"]; ?>
                  </div><!-- /.mailbox-read-message -->
                </div><!-- /.box-body -->
                <!-- Attachments (if any) -->
				<?php print $ui->attachmentsSectionForMessage($messageid, $folder); ?>
                <div class="box-footer">
	              <?php if (isset($fromUser)) { ?>
                  <div class="pull-right">
                    <button class="btn btn-default mail-reply"><i class="fa fa-reply"></i> Reply</button>
                    <button class="btn btn-default mail-forward"><i class="fa fa-share"></i> Forward</button>
                  </div>
                  <?php } ?>
                  <button class="btn btn-default mail-delete"><i class="fa fa-trash-o"></i> Delete</button>
                  <button class="btn btn-default mail-print"><i class="fa fa-print"></i> Print</button>
				  <!-- Module hook footer -->
                  <?php print $ui->getMessageDetailFooter($messageid, $folder); ?>
                </div><!-- /.box-footer -->
              </div><!-- /. box -->
            </div><!-- /.col -->
          </div><!-- /.row -->
        </section><!-- /.content -->
      </div><!-- /.content-wrapper -->
    </div><!-- ./wrapper -->
	<script type="text/javascript">
		$(document).ready(function() {
			// this message variables
			var selectedMessages = [<?php print $messageid; ?>];
			var folder = <?php print $folder; ?>;
			
			// print.
			$('.mail-print').click(function(e) {
				$('#message-full-box').printThis({ 
					loadCSS: [
						"<?php print \creamy\CRMUtils::creamyBaseURL(); ?>/css/creamycrm.css",
						"<?php print \creamy\CRMUtils::creamyBaseURL(); ?>/css/printpage.css"
					], 
					pageTitle: "<?php print $message["subject"]; ?>",
					header: '<div class="print-logo"><img src="http://creamycrm.com/img/logo.png" width="32" height="32"> Creamy</div>'
					});
			});

			<?php 
			    // delete
			    $successURL = "messages.php?folder=$folder&message=".urlencode($lh->translationFor("message_successfully_deleted"));
				print $ui->mailboxAction(
				    "mail-delete", 																		// class name
				    "php/DeleteMessages.php", 															// POST Request URL
				    $ui->newLocationJS($successURL), 													// Success JS				
				    $ui->showCustomErrorMessageAlertJS($lh->translationFor("unable_delete_messages")),  // Failure JS
				    null,																				// custom params 
				    true,																				// confirmation ?
				    true);																				// check selected messages?
			
			?>
			
			// reply
			$('.mail-reply').click(function (e) {
				var text = $('#mailbox-message-text').html();
				<?php $replySubject = urlencode("Re: ".$message["subject"]); ?>
				window.location.href = "composemail.php?reply_text="+responseEncodedMessageText(text, "<?php print $fromUser["name"]; ?>")+"&reply_subject=<?php print $replySubject; ?>&reply_user=<?php print $fromUser["id"]; ?>";
			});
			
			// forward
			$('.mail-forward').click(function (e) {
				var text = $('#mailbox-message-text').html();
				<?php $fwdSubject = urlencode("Fwd: ".$message["subject"]); ?>
				window.location.href = "composemail.php?reply_subject=<?php print $fwdSubject; ?>&reply_text="+responseEncodedMessageText(text, "<?php print $fromUser["name"]; ?>");
			});
			
			// generates the reply-to or forward message text. This text will be suitable for placing in the reply-to/forward content
			// of a message. It will be:
			// 1. stripped of all html entities
			// 2. Added --- Original message from "replyUser" --- 
			// 3. cut down to 512 characters (added ...)
			// 4. wrapped in <pre>...</pre>
			// 5. encoded to be passed as URI
			function responseEncodedMessageText(text, replyUser) {
				result = text.trim().substr(0, 512);
				result = "-------- <?php $lh->translateText("original_message_from"); ?> "+replyUser+" --------\n"+result;
				result = "<br/><br/><pre>"+result+"</pre>";
				result = encodeURI(result);
				return result;				
			}
			
			// hook actions
			<?php print $ui->getMessageDetailActionJS($messageid, $folder); ?>

		});
	</script>
	<?php print $ui->creamyFooter(); ?>
  </body>
</html>