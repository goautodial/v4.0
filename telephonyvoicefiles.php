<?php

	###################################################
	### Name: telephonyvoicefiles.php 				###
	### Functions: Manage Voicefiles 		 		###
	### Copyright: GOAutoDial Ltd. (c) 2011-2016	###
	### Version: 4.0 								###
	### Written by: Noel Umandap 					###
	### License: AGPLv2								###
	###################################################

	require_once('./php/UIHandler.php');
	require_once('./php/CRMDefaults.php');
    require_once('./php/LanguageHandler.php');
    include('./php/Session.php');

	$ui = \creamy\UIHandler::getInstance();
	$lh = \creamy\LanguageHandler::getInstance();
	$user = \creamy\CreamyUser::currentUser();
?>
<html>
    <head>
        <meta charset="UTF-8">
        <title><?php $lh->translateText("voicefiles"); ?></title>
        <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>

        <?php print $ui->standardizedThemeCSS(); ?>

        <!-- Wizard Form style -->
        <link href="css/wizard-form.css" rel="stylesheet" type="text/css" />
        <link href="css/style.css" rel="stylesheet" type="text/css" />
        <!-- DATA TABLES -->
        <link href="css/datatables/dataTables.bootstrap.css" rel="stylesheet" type="text/css" />
		<!-- Bootstrap Player -->
		<link href="css/bootstrap-player.css" rel="stylesheet" type="text/css" />

        <?php print $ui->creamyThemeCSS(); ?>

        <!-- Data Tables -->
        <script src="js/plugins/datatables/jquery.dataTables.js" type="text/javascript"></script>
        <script src="js/plugins/datatables/dataTables.bootstrap.js" type="text/javascript"></script>
		<!-- Bootstrap Player -->
		<script src="js/bootstrap-player.js" type="text/javascript"></script>

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
			<?php print $ui->getSidebar($user->getUserId(), $user->getUserName(), $user->getUserRole(), $user->getUserAvatar()); ?>

            <!-- Right side column. Contains the navbar and content of the page -->
            <aside class="right-side">
                <!-- Content Header (Page header) -->
                <section class="content-header">
                    <h1>
                        <?php $lh->translateText("telephony"); ?>
                        <small><?php $lh->translateText("voice_files_management"); ?></small>
                    </h1>
                    <ol class="breadcrumb">
                        <li><a href="./index.php"><i class="fa fa-phone"></i> <?php $lh->translateText("home"); ?></a></li>
			<li><?php $lh->translateText("telephony"); ?></li>
			<li class="active"><?php $lh->translateText("voice_files"); ?>
                    </ol>
                </section>

                <!-- Main content -->
                <section class="content">
                <?php if ($user->userHasAdminPermission()) { ?>
                    <div class="panel panel-default">
                        <div class="panel-body table" id="recording_table">
                            <legend><?php $lh->translateText("voice_files"); ?></legend>
							<?php print $ui->getListAllVoiceFiles(); ?>
                        </div>
                    </div>
				<!-- /fila con acciones, formularios y demás -->
				<?php
					} else {
						print $ui->calloutErrorMessage($lh->translationFor("you_dont_have_permission"));
					}
				?>
                </section><!-- /.content -->
            </aside><!-- /.right-side -->
            <div class="bottom-menu skin-blue">
				<div class="action-button-circle" data-toggle="modal">
					<?php print $ui->getCircleButton("voicefiles", "plus"); ?>
				</div>
				<div class="fab-div-area" id="fab-div-area">
					<ul class="fab-ul" style="">
						<li class="li-style"><a class="fa fa-volume-up fab-div-item" id="add_voicefiles" title="Add Voicefile"></a></li><br/>
					</ul>
				</div>
			</div>
        </div><!-- ./wrapper -->

	<!-- Modal -->
	<div id="voice-playback-modal" class="modal fade" role="dialog">
	  <div class="modal-dialog">

	    <!-- Modal content-->
	    <div class="modal-content">
	      <div class="modal-header">
	        <button type="button" class="close" data-dismiss="modal">&times;</button>
	        <h4 class="modal-title"><b><?php $lh->translateText("voice_playback"); ?></b></h4>
	      </div>
	      <div class="modal-body">
		<div class="voice-player"></div>
	      	<!-- <audio controls>
			<source src="http://www.w3schools.com/html/horse.ogg" type="audio/ogg" />
			<source src="http://www.w3schools.com/html/horse.mp3" type="audio/mpeg" />
			<a href="http://www.w3schools.com/html/horse.mp3">horse</a>
		</audio> -->
	      </div>
	      <div class="modal-footer">
		<a href="" class="btn btn-primary download-audio-file" download><?php $lh->translateText("download_file"); ?></a>
	        <button type="button" class="btn btn-default" data-dismiss="modal"><?php $lh->translateText("close"); ?></button>
	      </div>
	    </div>
	    <!-- End of modal content -->
	  </div>
	</div>
	<!-- End of modal -->

	<!-- Modal -->
	<div id="form-voicefiles-modal" class="modal fade" role="dialog">
	  <div class="modal-dialog">

	    <!-- Modal content-->
	    <div class="modal-content">
	      <div class="modal-header">
	        <button type="button" class="close" data-dismiss="modal">&times;</button>
	        <h4 class="modal-title"><b><?php $lh->translateText("upload_voice"); ?></b></h4>
	      </div>
        <form action="./php/AddVoiceFiles.php" method="POST" enctype="multipart/form-data">
			<input type="hidden" name="log_user" value="<?=$_SESSION['user']?>" />
			<input type="hidden" name="log_group" value="<?=$_SESSION['usergroup']?>" />
  	      <div class="modal-body" style="min-height: 15%;">
    				<div class="form-horizontal col-lg-12">
    					<div class="form-group" style="margin-bottom: 0px; margin-top: 10px;">
    						<div class="input-group">
    							<input type="file" name="voice_file" class="hide" id="voice_file" accept="audio/*">
    					      	<input type="text" class="form-control voice_file_holder" placeholder="Choose a file">
    					      	<span class="input-group-btn">
    					        	<button class="btn btn-default btn-browse-file" type="button">Browse...</button>
    					     	</span>
    					    </div><!-- /input-group -->
    					</div>
    				</div>
  	      </div>
  	      <div class="modal-footer">
  	      	<button type="submit" class="btn btn-primary btn-save-voicefiles"><?php $lh->translateText("save"); ?></button>
  	        <button type="button" class="btn btn-warning" data-dismiss="modal"><?php $lh->translateText("close"); ?></button>
  	      </div>
        </form>
	    </div>
	    <!-- End of modal content -->
	  </div>
	</div>
	<!-- End of modal -->




		<!-- Forms and actions -->
		<?php print $ui->standardizedThemeJS(); ?>

		<script type="text/javascript">
			$(document).ready(function() {
				$('#voicefiles').dataTable();

				$('.play_voice_file').click(function(){
					var audioFile = $(this).attr('data-location');

					var sourceFile = '<audio class="audio_file" controls>';
					    sourceFile += '<source src="'+ audioFile +'" type="audio/mpeg" download="true"/>';
					    sourceFile += '</audio>';

					$('.download-audio-file').attr('href', audioFile);
					$('.voice-player').html(sourceFile);
					$('#voice-playback-modal').modal('show');

					var aud = $('.audio_file').get(0);
					aud.play();
				});

				$('#voice-playback-modal').on('hidden.bs.modal', function () {
					var aud = $('.audio_file').get(0);
					aud.pause();
				});

				// FAB HOVER
				$(".bottom-menu").on('mouseenter mouseleave', function () {
				  $(this).find(".fab-div-area").stop().slideToggle({ height: 'toggle', opacity: 'toggle' }, 'slow');
				});

				$('#add_voicefiles').click(function(){
					$('#form-voicefiles-modal').modal('show');
				});

				$('.btn-browse-file').click(function(){
					$('#voice_file').click();
				});

				$('#voice_file').change(function(){
					var myFile = $(this).prop('files');
					var Filename = myFile[0].name;
          var filesize = myFile[0].size  / 1024;
              filesize = (Math.round(filesize * 100) / 100)

          if(filesize > 16000){
            alert("The voice file you are trying to upload exceeds the required file size. Maximum file size is up to 16MB only.");
            $('#voice_file').val('');
            $('.voice_file_holder').val();
          }else{
            $('.voice_file_holder').val(Filename);
          }

				});

        $('.voice_file_holder').change(function(){
          var holderVal = $(this).val();
          var file = $('#voice_file').val();

          if(holderVal != file){
            $('#voice_file').val('');
          }
        });

        $('#form-voicefiles-modal').on('hidden.bs.modal', function () {
            $('#voice_file').val('');
            $('.voice_file_holder').val();
        });
			});
		</script>

		<?php print $ui->getRightSidebar($user->getUserId(), $user->getUserName(), $user->getUserAvatar()); ?>
		<?php print $ui->creamyFooter();?>
    </body>
</html>
