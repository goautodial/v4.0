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
    include('./php/Session.php');
	
    $ui = \creamy\UIHandler::getInstance();
    $lh = \creamy\LanguageHandler::getInstance();
	$user = \creamy\CreamyUser::currentUser();
    
	if (isset($_GET["folder"])) {
		$folder = $_GET["folder"];
	} else $folder = MESSAGES_GET_INBOX_MESSAGES;
	if ($folder < 0 || $folder > MESSAGES_MAX_FOLDER) { $folder = MESSAGES_GET_INBOX_MESSAGES; }

	if (isset($_GET["message"])) {
		$message = $_GET["message"];
	} else $message = NULL;

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
    <!-- DATA TABLES -->
    <link href="css/datatables/dataTables.bootstrap.css" rel="stylesheet" type="text/css" />
    <!-- iCheck for checkboxes and radio inputs -->
    <link href="css/iCheck/minimal/blue.css" rel="stylesheet" type="text/css" />
    <!-- Creamy style -->
    <link href="css/creamycrm.css" rel="stylesheet" type="text/css" />
    <!-- Circle Buttons style -->
    <link href="css/circle-buttons.css" rel="stylesheet" type="text/css" />
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
	<!-- DATA TABES SCRIPT -->
    <script src="js/plugins/datatables/jquery.dataTables.js" type="text/javascript"></script>
    <script src="js/plugins/datatables/dataTables.bootstrap.js" type="text/javascript"></script>
    <!-- Bootstrap WYSIHTML5 -->
    <script src="js/plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.all.min.js" type="text/javascript"></script>
    <!-- iCheck -->
    <script src="js/plugins/iCheck/icheck.min.js" type="text/javascript"></script>
    <!-- Slimscroll -->
    <script src="js/plugins/slimScroll/jquery.slimscroll.min.js" type="text/javascript"></script>
    <!-- FastClick -->
    <script src="js/plugins/fastclick/fastclick.min.js" type="text/javascript"></script>
    <!-- Creamy App -->
    <script src="js/app.min.js" type="text/javascript"></script>
    <!-- preloader -->
        <link rel="stylesheet" href="css/customizedLoader.css">

        <script type="text/javascript">
			$(window).ready(function() {
				$(".preloader").fadeOut("slow");
			})
		</script>
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
            <?php $lh->translateText("calls"); ?>
            <small><?php $lh->translateText("call_logs"); ?></small>
          </h1>
            <ol class="breadcrumb">
                <li><a href="./index.php"><i class="fa fa-envelope"></i> <?php $lh->translateText("home"); ?></a></li>
                <li class="active">My Call Logs</li>
            </ol>
        </section>

        <!-- Main content -->
        <section class="content">
          <div class="row">
	        <!-- left side folder list column -->
            <div class="col-md-3">
              <!--<a href="makecall.php" class="btn btn-primary btn-block margin-bottom"><?php $lh->translateText("new_call"); ?></a>-->
              <div class="box box-solid">
                <div class="box-header with-border">
                  <h3 class="box-title"><?php print $lh->translationFor("folders"); ?></h3>
                </div>
                <div class="box-body no-padding">
					<?php print $ui->getCallFoldersAsList($folder); ?>
                </div><!-- /.box-body -->
              </div><!-- /. box -->
            </div><!-- /.col -->
            
            <!-- main content right side column -->
            <div class="col-md-9">
              <div class="box box-default">
                <div class="box-header with-border">
                  <h3 class="box-title"><?php $lh->translateText("calls"); ?></h3>
                </div><!-- /.box-header -->
                <div class="box-body no-padding">
                  <div class="mailbox-controls">
			<?php print $ui->getCallButtons($folder); ?>
                  </div>
                  <div class="table-responsive mailbox-messages">
	                <?php print $ui->getCallFromFolderAsTable($user->getUserId(), $folder); ?>
                  </div><!-- /.mail-box-messages -->
                </div><!-- /.box-body -->
                <div class="box-footer no-padding">
                  <div class="mailbox-controls">
			<?php print $ui->getCallButtons($folder); ?>
			<div id="messages-message-box">
				<?php if (!empty($message)) { print $ui->calloutInfoMessage($message); } ?>
			</div>
                  </div>
                </div>
              </div><!-- /. box -->
            </div><!-- /.col -->
          </div><!-- /.row -->
        </section><!-- /.content -->
		
	<div class="bottom-menu skin-blue">
	<?php print $ui->getCircleButton("calls", "phone"); ?>
	
	    	<div class="fab-div-area" id="fab-div-area">
				<ul class="fab-ul" style="height: 250px;">
					<li class="li-style"><a class="fa fa-phone fab-div-item"> Phone Calls</a></li><br/>
					<li class="li-style"><a class="fa fa-tasks fab-div-item"> Tasks</a></li><br/>
					<li class="li-style"><a class="fa fa-plus fab-div-item"> Add Calls</a></li>
				</ul>
	    	</div>
	</div>
	<script>
		$(document).ready(function(){
			/*
			$(".circle-button-large").onhover(function(){
				$('.fab-div-area').slideToggle();
				$('.fab-div-item').fadeIn();
				$('.fab-div-item').show();
				$('.fab-div-area').show();
			});
			$('.fab-div-item').hide();
			$('.fab-div-area').hide();
			*/
			
			$(".bottom-menu").on('mouseenter mouseleave', function () {
			  $(this).find(".fab-div-area").stop().slideToggle({ height: 'toggle', opacity: 'toggle' }, 'slow');

			});
			
			
		});
		
	</script>
      </div><!-- /.content-wrapper -->
	  <?php print $ui->getRightSidebar($user->getUserId(), $user->getUserName(), $user->getUserAvatar()); ?>
	  <?php print $ui->creamyFooter(); ?>
    </div><!-- ./wrapper -->
    
        <!-- Page script -->
        <script type="text/javascript">
			var datatable = null;
			$(document).ready(function() {
                datatable = $("#messagestable").dataTable( {
					"bFilter": false,  //Disable search function
					"bJQueryUI": true, //Enable smooth theme
					"bPaging": true,
					"sDom": 't'
                } );
            });
	    </script>
        <script type="text/javascript">
			 $(document).ready(function() {
			 	var folder = <?php print $folder; ?>;
			 	var selectedAll = false;
			 	var selectedMessages = [];
			 
			     "use strict";
			
				 // ------------- Favorites -------------------
			
			    //iCheck for checkbox and radio inputs
		        $('input[type="checkbox"]').iCheck({
		          checkboxClass: 'icheckbox_minimal-blue',
		          radioClass: 'iradio_minimal-blue'
		        });			    
		        
			    // check individual message
				$('input[type=checkbox]').on("ifUnchecked", function(e) {
					var index = selectedMessages.indexOf(e.currentTarget.value);
					if (index >= 0) selectedMessages.splice(index, 1);
				});
			    
			    // uncheck individual message
				$('input[type=checkbox]').on("ifChecked", function(e) {
					if (e.currentTarget.value != 'on') selectedMessages.push(e.currentTarget.value);
				});

			    // uncheck/check all messages
				$(".checkbox-toggle").click(function() {
					if (selectedAll) { $("input[type='checkbox']", ".mailbox").iCheck("uncheck"); }
					else { $("input[type='checkbox']", ".mailbox").iCheck("check"); }
					selectedAll = !selectedAll;
				});

				// next button for table.
				$(".mailbox-next").click(function() { datatable.fnPageChange('next'); });

				// previous button for table
				$(".mailbox-prev").click(function() { datatable.fnPageChange('previous'); });

			    // de-star a starred video / star a de-stared video.
			    $(".fa-star, .fa-star-o").click(function(e) {
			        e.preventDefault();
			        
			        // Detect type: e.currentTarget.id contains the message id.
					var starred = $(this).hasClass("fa-star");
					var favorite = 1;
					var selectedItem = this;
					
					if (starred) { // unmark message as favorite
						favorite = 0;   
					} // else mark message as favorite
					
					$("#messages-message-box").hide();
					$.post("./php/MarkMessagesAsFavorite.php", 
						{ "favorite": favorite, "messageids": [e.currentTarget.id], "folder": folder } ,function(data){
						if (data == "<?php print CRM_DEFAULT_SUCCESS_RESPONSE; ?>") { 
							// toggle visual change.
				            $(selectedItem).toggleClass("fa-star");
				            $(selectedItem).toggleClass("fa-star-o");
						}
						else {
							<?php
								$msg = $ui->calloutErrorMessage($lh->translationFor("message")); 
								print $ui->fadingInMessageJS($msg, "messages-message-box");
							?>
						}
					});
			    });
			
			    <?php
			    // mark messages as favorite.
				$unableFavoriteCode = $ui->calloutErrorMessage($lh->translationFor("unable_set_favorites"));
				print $ui->mailboxAction(
					"messages-mark-as-favorite", 											// classname
					"php/MarkMessagesAsFavorite.php", 										// php to request
					$ui->reloadLocationJS(), 												// success js
					$ui->fadingInMessageJS($unableFavoriteCode, "messages-message-box"),	// failure js
					array("favorite" => 1));												// custom parameters
					
				// mark messages as read
				$unableReadCode = $ui->calloutErrorMessage($lh->translationFor("unable_set_read"));
				print $ui->mailboxAction(
					"messages-mark-as-read", 												// classname
					"php/MarkMessagesAsRead.php", 											// php to request
					$ui->reloadLocationJS(), 												// success js
					$ui->fadingInMessageJS($unableReadCode, "messages-message-box")); 		// failure js
				
				// mark messages as unread
				$unableUnreadCode = $ui->calloutErrorMessage($lh->translationFor("unable_set_unread"));
				print $ui->mailboxAction(
					"messages-mark-as-unread", 												// classname
					"php/MarkMessagesAsUnread.php", 										// php to request
					$ui->reloadLocationJS(), 												// success js
					$ui->fadingInMessageJS($unableUnreadCode, "messages-message-box")); 	// failure js
				
			    // send to junk mail
				$junkText = 'data+" '.$lh->translationFor("out_of").' "+selectedMessages.length+" '.
					$lh->translationFor("messages_sent_trash").'"';
				print $ui->mailboxAction(
					"messages-send-to-junk",					// classname
					"php/JunkMessages.php",						// php to request
					$ui->reloadWithMessageCallJS($junkText));	// result js
				
			    // restore mail from junk
				$unjunkText = 'data+" '.$lh->translationFor("out_of").' "+selectedMessages.length+" '.
					$lh->translationFor("messages_recovered_trash").'"';
				print $ui->mailboxAction(
					"messages-restore-message",					// classname
					"php/UnjunkMessages.php",					// php to request
					$ui->reloadWithMessageCallJS($unjunkText));	// result js
				
			    // delete messages.
				$unableDeleteCode = $ui->calloutErrorMessage($lh->translationFor("unable_delete_messages"));
				print $ui->mailboxAction(
					"messages-delete-permanently", 											// classname
					"php/DeleteMessages.php", 												// php to request
					$ui->reloadLocationJS(), 												// success js
					$ui->fadingInMessageJS($unableDeleteCode, "messages-message-box")); 	// failure js
								
				?>

				// Reload with message function.
				<?php print $ui->reloadWithMessageFunctionJS(); ?>
				
			});
			// Modules hook for message list footer.
			<?php print $ui->getMessagesListActionJS($folder); ?>		    
        </script>

  </body>
</html>