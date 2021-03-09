<?php
/**
 * @file        telephonyaudiofiles.php
 * @brief       Manage audio files
 * @copyright   Copyright (c) 2020 GOautodial Inc.
 * @author		Demian Lizandro A. Biscocho
 * @author      Alexander Jim Abenoja
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
*/

	require_once('./php/UIHandler.php');
	require_once('./php/APIHandler.php');
	require_once('./php/CRMDefaults.php');
    require_once('./php/LanguageHandler.php');
    include('./php/Session.php');

	$ui = \creamy\UIHandler::getInstance();
	$api = \creamy\APIHandler::getInstance();
	$lh = \creamy\LanguageHandler::getInstance();
	$user = \creamy\CreamyUser::currentUser();
	
	//proper user redirects
	if($user->getUserRole() != CRM_DEFAULTS_USER_ROLE_ADMIN){
		if($user->getUserRole() == CRM_DEFAULTS_USER_ROLE_AGENT){
			header("location: agent.php");
		}
	}	
	
	$perm = $api->goGetPermissions('voicefiles,moh', $_SESSION['usergroup']);
?>
<html>
    <head>
        <meta charset="UTF-8">
        <title><?php $lh->translateText('portal_title'); ?> - <?php $lh->translateText("audiofiles"); ?></title>
        <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>

        <?php 
			print $ui->standardizedThemeCSS(); 
			print $ui->creamyThemeCSS();
			print $ui->dataTablesTheme();
		?>

    	<!-- Wizard Form style -->
        <link href="css/style.css" rel="stylesheet" type="text/css" />
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
                        <small><?php $lh->translateText("audiofiles_management"); ?></small>
                    </h1>
                    <ol class="breadcrumb">
                        <li><a href="./index.php"><i class="fa fa-phone"></i> <?php $lh->translateText("home"); ?></a></li>
                        <li><?php $lh->translateText("telephony"); ?></li>
						<li class="active"><?php $lh->translateText("audiofiles"); ?>
                    </ol>
                </section>

                <!-- Main content -->
                <section class="content">
                <?php if ($perm->voicefiles->voicefiles_play !== 'N' || $perm->moh->moh_read !== 'N') { ?>

			<div class="panel panel-default">
				<div class="panel-body">
					<legend><?php $lh->translateText("audiofiles"); ?></legend>

		            <div role="tabpanel">

						<ul role="tablist" class="nav nav-tabs nav-justified">

						<?php
						$toggleVoicefiles = ' class="active"';
						$toggleMOH = '';
						$activeVoicefiles = ' active';
						$activeMOH = '';
						if ($perm->voicefiles->voicefiles_upload === 'N') {
							$toggleVoicefiles = ' class="hidden"';
							$activeVoicefiles = '';
						}
						if ($perm->moh->moh_create === 'N') { $toggleMOH = ' class="hidden"'; }
						if ($perm->moh->moh_create !== 'N' && $perm->voicefiles->voicefiles_upload === 'N') {
							$toggleMOH = ' class="active"';
							$activeMOH = ' active';
						}
						?>
						<!-- Voicefiles panel tab -->
							 <li role="presentation"<?=$toggleVoicefiles?>>
								<a href="#voicefiles_tab" aria-controls="voicefiles_tab" role="tab" data-toggle="tab" class="bb0">
								   <?php $lh->translateText("voice_files"); ?> </a>
							 </li>
						 <!-- MOH panel tabs-->
							 <li role="presentation"<?=$toggleMOH?>>
								<a href="#moh_tab" aria-controls="moh_tab" role="tab" data-toggle="tab" class="bb0">
								    <?php $lh->translateText("moh"); ?> </a>
							 </li>

						  </ul>

						<!-- Tab panes-->
						<div class="tab-content bg-white">

							<!--==== MOH ====-->
							<div id="moh_tab" role="tabpanel" class="tab-pane<?=$activeMOH?>">
								<?php print $ui->getListAllMusicOnHold($_SESSION['usergroup']); ?>
							</div>

							<!--==== Voicefiles ====-->
							<div id="voicefiles_tab" role="tabpanel" class="tab-pane<?=$activeVoicefiles?>">
								<?php 
									//$output2 = $api->API_getAllVoiceFiles();
									
									//echo "<pre>";									
									//var_dump($output2);
									print $ui->getListAllVoiceFiles($_SESSION['usergroup']); 
								?>
							</div>

						</div><!-- END tab content-->

							<!-- /fila con acciones, formularios y demás -->
							<?php
								} else {
									print $ui->calloutErrorMessage($lh->translationFor("you_dont_have_permission"));
								}
							?>

						<div class="bottom-menu skin-blue<?php if ($perm->voicefiles->voicefiles_upload == 'N' && $perm->moh->moh_create == 'N') { echo " hidden"; } ?>">
							<div class="action-button-circle" data-toggle="modal">
								<?php print $ui->getCircleButton("inbound", "plus"); ?>
							</div>
							<div class="fab-div-area" id="fab-div-area">
								<?php
								$menu = 2;
								$menuHeight = '170px';
								$hideVoicefiles = '';
								$hideMOH = '';
								if ($perm->voicefiles->voicefiles_upload === 'N') {
									$menu--;
									$hideVoicefiles = ' hidden';
								}
								if ($perm->moh->moh_create === 'N') {
									$menu--;
									$hideMOH = ' hidden';
								}
								if ($menu < 2) { $menuHeight = '110px'; }
								?>
								<ul class="fab-ul" style="height: <?=$menuHeight?>;">
									<li class="li-style<?=$hideVoicefiles?>"><a class="fa fa-volume-up fab-div-item" data-toggle="modal" data-target="#form-voicefiles-modal" title="Add a Voice File"></a></li><br/>
									<li class="li-style<?=$hideMOH?>"><a class="fa fa-music fab-div-item" data-toggle="modal" data-target="#moh-wizard" title="Add a Music On-hold"></a></li><br/>
								</ul>
							</div>
						</div>
					</div>
				</div><!-- /. body -->
			</div><!-- /. panel -->
        </section><!-- /.content -->
    </aside><!-- /.right-side -->
	<?php print $ui->getRightSidebar($user->getUserId(), $user->getUserName(), $user->getUserAvatar()); ?>
</div><!-- ./wrapper -->

<?php
 /*
  * APIs needed for form
  */
   $user_groups = $api->API_getAllUserGroups();
   $audio_files = $api->API_getAllVoiceFiles(); 
?>
<!-- MOH MODALS -->
	<!-- Modal -->
	<div id="view-moh-modal" class="modal fade" role="dialog">
	  <div class="modal-dialog">

	    <!-- Modal content-->
	    <div class="modal-content">
	      <div class="modal-header">
	        <button type="button" class="close" data-dismiss="modal">&times;</button>
	        <h4 class="modal-title"><b><?php $lh->translateText("moh_details"); ?></b></h4>
	      </div>
	      <div class="modal-body">
	      <form action="" method="POST" id="edit_moh" name="edit_moh" role="form">
		<div class="form-horizontal">
			<br />
			<div class="form-group">
				<label class="control-label col-lg-4"><?php $lh->translateText("moh_name"); ?></label>
				<div class="col-lg-7">
					<input type="text" name="moh_name" class="form-control moh_name" <?=($perm->moh->moh_update === 'N' ? 'disabled' : '')?>/>
				</div>
			</div>
			<div class="form-group">
				<label class="control-label col-lg-4"><?php $lh->translateText("status"); ?>:</label>
				<div class="col-lg-5">
					<select name ="active" class="form-control moh_status" <?=($perm->moh->moh_update === 'N' ? 'disabled' : '')?>/>
						<option value="Y">Active</option>
						<option value="N">Inactive</option>
					</select>
				</div>
			</div>
			<div class="form-group">
				<label class="control-label col-lg-4"><?php $lh->translateText("user_group"); ?>:</label>
				<div class="col-lg-7">
					<select name="user_group" class="form-control moh_user_group select2-1" style="width:100%;" <?=($perm->moh->moh_update === 'N' ? 'disabled' : '')?>/>
						<option value="---ALL---">  ALL USER GROUPS  </option>
						<?php
                            for($i=0;$i<count($user_groups->user_group);$i++){
                        ?>
                            <option value="<?php echo $user_groups->user_group[$i];?>">  <?php echo $user_groups->user_group[$i].' - '.$user_groups->group_name[$i];?>  </option>
                        <?php
                            }
                        ?>
					</select>
				</div>
			</div>
			<div class="form-group">
				<label class="control-label col-lg-4"><?php $lh->translateText("random_order"); ?>:</label>
				<div class="col-lg-5">
					<select name="random" class="form-control moh_rand_order" <?=($perm->moh->moh_update === 'N' ? 'disabled' : '')?>/>
						<option value="Y">Yes</option>
						<option value="N">No</option>
					</select>
				</div>
			</div>
			<div class="form-group">
	  			<label class="col-lg-4 control-label" for="filename"><?php $lh->translateText("audiofiles"); ?>: </label>
				<div class="col-lg-7">
					<select class="form-control moh_filename  select2-1" name="filename" style="width:100%;">
                                                <option value="conf">  conf  </option>
						<?php
						for($i=0;$i<count($audio_files->file_name);$i++){
							$file = substr($audio_files->file_name[$i], 0, strrpos($audio_files->file_name[$i], "."));
						?>
							<option value="<?php echo $file;?>">  <?php echo $file; ?>  </option>
	    		<?php
           		    }		
            		?>
				        </select>
				</div>
			</div>
		</form>
		</div>
	      </div>
          <div class="message_box"></div>
	      <div class="modal-footer">
	           <?php
			   if ($perm->moh->moh_update !== 'N') {
			   ?>
			   <button type="button" class="btn btn-primary btn-update-moh-info" data-id=""><span id="update_button"><i class="fa fa-check"></i> update</span></button>
			   <?php
			   } else {
			   ?>			   
	           <button type="button" class="btn btn-default" data-dismiss="modal">close</button>
			   <?php
			   }
			   ?>
	      </div>
	    </div>
	    <!-- End of modal content -->
	  </div>
	</div>
	<!-- End of modal -->

	<!-- ADD USER GROUP MODAL -->
    <div class="modal fade" id="moh-wizard" tabindex="-1" aria-labelledby="moh-wizard" >
        <div class="modal-dialog" role="document">
            <div class="modal-content">

            <!-- Header -->
                <div class="modal-header">
                    <h4 class="modal-title animated bounceInRight">
                    	<b><?php $lh->translateText("moh_wizard_title"); ?> » <?php $lh->translateText("moh_wizard_subtitle"); ?> </b>
                    	<button type="button" class="close" data-dismiss="modal" aria-label="close_ingroup"><span aria-hidden="true">&times;</span></button>
                    </h4>
                </div>
                <div class="modal-body wizard-content">

                <form action="" method="POST" id="create_moh" name="create_moh" role="form">
					<input type="hidden" name="log_user" value="<?=$_SESSION['user']?>" />
					<input type="hidden" name="log_group" value="<?=$_SESSION['usergroup']?>" />
                    <div class="row">
                    	<h4><?php $lh->translateText("music_on_hold"); ?> <br/>
                    	<small><?php $lh->translateText("moh_sub_header"); ?></small>
                    	</h4>
                    	<fieldset>
	                        <div class="form-group">
	                            <label class="col-sm-4 control-label" for="moh_id"><?php $lh->translateText("moh_id"); ?></label>
	                            <div class="col-sm-8 mb">
	                                <input type="text" name="moh_id" id="moh_id" class="form-control" placeholder="<?php $lh->translateText("music_onhold_id"); ?>" required />
	                            </div>
	                        </div>
	                        <div class="form-group">
	                            <label class="col-sm-4 control-label" for="moh_name"><?php $lh->translateText("moh_name"); ?></label>
	                            <div class="col-sm-8 mb">
	                                <input type="text" name="moh_name" id="moh_name" class="form-control" placeholder="<?php $lh->translateText("music_onhold_name"); ?>" required />
	                            </div>
	                        </div>
	                        <div class="form-group">
	                            <label class="col-sm-4 control-label" for="active"><?php $lh->translateText("status"); ?>: </label>
	                            <div class="col-sm-8 mb">
	                                <select name="active" id="active" class="form-control">
	                                    <option value="N" selected><?php $lh->translateText("inactive"); ?></option>
	                                    <option value="Y"><?php $lh->translateText("active"); ?></option>
	                                </select>
	                            </div>
	                        </div>
	                        <div class="form-group">
	                            <label class="col-sm-4 control-label" for="user_group"><?php $lh->translateText("user_group"); ?>: </label>
	                            <div class="col-sm-8 mb">
	                                <select id="user_group" class="form-control select2-1" name="user_group" style="width:100%;">
												<?php
												if ($_SESSION['usergroup'] === "ADMIN") {
												?>
	                                	<option value="---ALL---">  ALL USER GROUPS  </option>
	                                 <?php
												}
	                                    for($i=0;$i<count($user_groups->user_group);$i++){
	                                 ?>
	                                    <option value="<?php echo $user_groups->user_group[$i];?>">  <?php echo $user_groups->user_group[$i].' - '.$user_groups->group_name[$i];?>  </option>
	                                    <?php
	                                    }
	                                    ?>
	                                </select>
	                            </div>
	                        </div>
	                        <div class="form-group">
	                            <label class="col-sm-4 control-label" for="random"><?php $lh->translateText("random_order"); ?>: </label>
	                            <div class="col-sm-8 mb">
	                                <select name="random" id="random" class="form-control">
	                                    <option value="N" selected><?php $lh->translateText("go_no"); ?></option>
	                                    <option value="Y"><?php $lh->translateText("go_yes"); ?></option>
	                                </select>
	                            </div>
	                        </div>
<div class="form-group">
                                <label class="col-sm-4 control-label" for="filename"><?php $lh->translateText("audiofiles"); ?>: </label>
                                <div class="col-sm-8">
                                        <select class="form-control select2-1" name="filename" style="width:100%;">
                                                <option value="conf">  conf  </option>
                                                <?php
                                                for($i=0;$i<count($audio_files->file_name);$i++){
							$file = substr($audio_files->file_name[$i], 0, strrpos($audio_files->file_name[$i], "."));
                                                ?>
                                                        <option value="<?php echo $file;?>">  <?php echo $file; ?>  </option>

                        <?php
                            }
                        ?>
                                        </select>
                                </div>
                        </div>

                        </fieldset>
                    </div><!-- end of step -->

                </form>

                </div> <!-- end of modal body -->
            </div>
        </div>
    </div><!-- end of modal -->
<!-- end of MOH Modals -->

<!-- VOICE FILES MODALS -->
	<!-- Playback Modal -->
	<div id="voice-playback-modal" class="modal fade" role="dialog">
	  <div class="modal-dialog modal-sm">

	    <!-- Modal content-->
	    <div class="modal-content">
	      <div class="modal-header">
	        <button type="button" class="close" data-dismiss="modal">&times;</button>
	        <h4 class="modal-title"><b><?php $lh->translateText("voice_files_playback"); ?></b></h4>
	      </div>
	      <div class="modal-body">
	      	<center class="mt"><em class="fa fa-music fa-5x"></em></center>
	      	<div class="row mt mb">
		      	<center><span class="voice-details"></span></center>
		    </div>
		    <br/>
			<div class="voice-player"></div>
	      	<!-- <audio controls>
			<source src="http://www.w3schools.com/html/horse.ogg" type="audio/ogg" />
			<source src="http://www.w3schools.com/html/horse.mp3" type="audio/mpeg" />
			<a href="http://www.w3schools.com/html/horse.mp3">horse</a>
		</audio> -->
	      </div>
	      <div class="modal-footer">
		<a href="" class="btn btn-primary download-audio-file<?=($perm->voicefiles->voicefiles_download === 'N' ? ' hidden' : '')?>" download><?php $lh->translateText("download_file"); ?></a>
	        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
	      </div>
	    </div>
	    <!-- End of modal content -->
	  </div>
	</div>
	<!-- End of modal -->

	<!-- Upload Voice Files Modal -->
	<div id="form-voicefiles-modal" class="modal fade" role="dialog">
	  <div class="modal-dialog">

	    <!-- Modal content-->
	    <div class="modal-content">
	      	<div class="modal-header">
	       		<h4 class="modal-title animated bounceInRight">
	       			<b><?php $lh->translateText("upload_voice_file"); ?></b>
	       			<button type="button" class="close" data-dismiss="modal" aria-label="close_ingroup"><span aria-hidden="true">&times;</span></button>
	       		</h4>
	      	</div>
		    <div class="modal-body clearfix">
		        <form action="./php/AddVoiceFiles.php" method="POST" id="voicefile_form" enctype="multipart/form-data">
					<input type="hidden" name="session_user" value="<?=$_SESSION['user']?>" />
		  	      	<div class="row">
		  	      		<h4>
		  	      			<?php $lh->translateText("voice_file_wizard_header"); ?><br/>
		  	      			<small><?php $lh->translateText("voice_file_wizard_sub_header"); ?></small>
		  	      		</h4>
		  	      		<fieldset>
		    				<div class="col-lg-12">
		    					<div class="form-group mt">
		    						<div class="input-group">
		    					      	<input type="text" class="form-control voice_file_holder" placeholder="<?php $lh->translateText("choose_a_file"); ?>" required>
		    					      	<span class="input-group-btn">
		    					        	<button class="btn btn-default btn-browse-file" type="button"><?php $lh->translateText("browse"); ?></button>
		    					     	</span>
		    					    </div><!-- /input-group -->
		    					    <input type="file" name="voice_file" class="hide" id="voice_file" accept="audio/*">
		    					</div>
		    				</div>
		    				<div class="form-group">
		    					<div class="upload-loader" style="display:none;">
					    			<center>
					    				<div class="fl spinner2" style="position: absolute;">
					    					<div class="spinner-container container1">
					    						<div class="circle1"></div>
					    						<div class="circle2"></div>
					    						<div class="circle3"></div>
					    						<div class="circle4"></div>
					    					</div>
					    					<div class="spinner-container container2">
					    						<div class="circle1"></div>
					    						<div class="circle2"></div>
					    						<div class="circle3"></div>
					    						<div class="circle4"></div>
					    					</div>
					    					<div class="spinner-container container3">
					    						<div class="circle1"></div>
					    						<div class="circle2"></div>
					    						<div class="circle3"></div>
					    						<div class="circle4"></div>
					    					</div>
					    					<h4 class="upload-text"><b><?php $lh->translateText("uploading"); ?></b></h4>
					    				</div>
					    			</center>
					    		</div>
		    				</div>
		    			</fieldset>
		    		</div>
		        </form>
		    </div>
	    </div>
	    <!-- End of modal content -->
	  </div>
	</div>
	<!-- End of modal -->
<!-- End of VOICE FILE Modals -->

		<?php print $ui->standardizedThemeJS(); ?>
        <!-- JQUERY STEPS-->
  		<script src="js/dashboard/js/jquery.steps/build/jquery.steps.js"></script>

 <script type="text/javascript">
	$(document).ready(function() {

		/*******************
		** INITIALIZATIONS
		*******************/

			// loads the fixed action button
				$(".bottom-menu").on('mouseenter mouseleave', function () {
				  $(this).find(".fab-div-area").stop().slideToggle({ height: 'toggle', opacity: 'toggle' }, 'slow');
				});

			//loads datatable functions
				$('#music-on-hold_table').dataTable({
					"aoColumnDefs": [{
						"bSearchable": false,
						"aTargets": [ 4 ]
					},{
						"bSortable": false,
						"aTargets": [ 4 ]
					}]
				});
				$('#voicefiles').dataTable({
					"aoColumnDefs": [{
						"bSearchable": false,
						"aTargets": [ 2 ]
					},{
						"bSortable": false,
						"aTargets": [ 2 ]
					}]
				});

		/*******************
		** MOH EVENTS
		*******************/

			/*********
			** INIT WIZARD
			*********/

				var moh_form = $("#create_moh"); // init form wizard

			    moh_form.validate({
			        errorPlacement: function errorPlacement(error, element) { element.after(error); }
			    });
			    moh_form.children("div").steps({
			        headerTag: "h4",
			        bodyTag: "fieldset",
			        transitionEffect: "slideLeft",
			        onStepChanging: function (event, currentIndex, newIndex)
			        {
			        	// Allways allow step back to the previous step even if the current step is not valid!
				        if (currentIndex > newIndex) {
				            return true;
				        }

						// Clean up if user went backward before
					    if (currentIndex < newIndex)
					    {
					        // To remove error styles
					        $(".body:eq(" + newIndex + ") label.error", moh_form).remove();
					        $(".body:eq(" + newIndex + ") .error", moh_form).removeClass("error");
					    }

			            moh_form.validate().settings.ignore = ":disabled,:hidden";
			            return moh_form.valid();
			        },
			        onFinishing: function (event, currentIndex)
			        {
			            moh_form.validate().settings.ignore = ":disabled";
			            return moh_form.valid();
			        },
			        onFinished: function (event, currentIndex)
			        {

			        	$('#finish').text("Loading...");
			        	$('#finish').attr("disabled", true);

			        	/*********
						** ADD EVENT
						*********/
				            // Submit form via ajax
					            $.ajax({
		                            url: "./php/AddMOH.php",
		                            type: 'POST',
		                            data: $("#create_moh").serialize(),
		                            success: function(data) {
		                              // console.log(data);
		                                  if (data == 1) {
		                                        swal("<?php $lh->translateText("success"); ?>", "<?php $lh->translateText("add_moh_success"); ?>", "success");
		                                        window.setTimeout(function(){location.reload()},3000)
		                                        $('#submit_moh').val("Submit");
		                                        $('#submit_moh').attr("disabled", false);
		                                  } else {
		                                      sweetAlert("<?php $lh->translateText("oups"); ?>", "<?php $lh->translateText("something_went_wrong"); ?>"+data, "error");
		                                      $('#submit_moh').val("Submit");
		                                      $('#submit_moh').attr("disabled", false);
		                                  }
		                            }
		                        });
			        }
			    }); // end of wizard

			//------------------------

			/*********
			** EDIT MOH
			*********/

				$(document).on('click','.edit-moh',function() {
					var moh_id = $(this).attr('data-id');
					$.ajax({
						url: "./php/ViewMOH.php",
						type: 'POST',
						data: {
						      moh_id : moh_id
						},
						dataType: 'json',
						success: function(data) {
						      $('.btn-update-moh-info').attr('data-id', data.moh_id);
						      $('.moh_name').val(data.moh_name);
						      $('.moh_status option[value="' + data.active + '"]').attr('selected','selected');
						      $('.moh_user_group option[value="' + data.user_group + '"]').attr('selected','selected');
						      $('.moh_rand_order option[value="' + data.random + '"]').attr('selected','selected');
                                                      $('.moh_filename option[value="' + data.filename + '"]').attr('selected','selected');


                              $('#view-moh-modal').modal('show');
						}
					});
				});


				$('#form-voicefiles-modal').on('hidden.bs.modal', function () {
					$('.voice_file_holder').val('');
					$('#voice_file').val('');
				});

				$('.btn-update-moh-info').click(function(){
					var moh_id = $(this).attr('data-id');
					console.log(moh_id);
                    $('#update_button').html("<i class='fa fa-edit'></i> Updating...");
                    $('.btn-update-moh-info').attr("disabled", true);
                    
					$.ajax({
						url: "./php/UpdateMOH.php",
						type: 'POST',
						data: $("#edit_moh").serialize() + '&moh_id=' + moh_id,
						/*data: {
						      moh_id : $(this).attr('data-id'),
						      moh_name : $('.moh_name').val(),
						      user_group : $('.mog_user_group').val(),
						      active : $('.moh_status').val(),
						      random : $('.moh_rand_order').val()
						},*/
						dataType: 'json',
						success: function(data) {
						      if (data == 1) {
							    swal("<?php $lh->translateText("success"); ?>", "<?php $lh->translateText("moh_update_success"); ?>", "success");
                                window.setTimeout(function(){location.reload();},2000);

                                $('#update_button').html("<i class='fa fa-check'></i> Update");
                                $('.btn-update-moh-info').attr("disabled", false);
						      } else {
    							sweetAlert("<?php $lh->translateText("oups"); ?>", "<?php $lh->translateText("something_went_wrong"); ?>"+data, "error");

                                $('#update_button').html("<i class='fa fa-check'></i> Update");
                                $('.btn-update-moh-info').attr("disabled", false);
						      }


						}
					});
				});

			/*********
			** DELETE MOH
			*********/
				// delete click
					$(document).on('click','.delete-moh',function() {
					 	var id = $(this).attr('data-id');
						var log_user = '<?=$_SESSION['user']?>';
						var log_group = '<?=$_SESSION['usergroup']?>';
	                    swal({
	                        title: "<?php $lh->translateText("are_you_sure"); ?>",
	                        text: "<?php $lh->translateText("action_cannot_be_undone"); ?>",
	                        type: "warning",
	                        showCancelButton: true,
	                        confirmButtonColor: "#DD6B55",
	                        confirmButtonText: "<?php $lh->translateText("confirm_delete_moh"); ?>",
	                        cancelButtonText: "<?php $lh->translateText("cancel_please"); ?>",
	                        closeOnConfirm: false,
	                        closeOnCancel: false
	                        },
	                        function(isConfirm){
	                            if (isConfirm) {
	                                $.ajax({
	                                    url: "./php/DeleteMOH.php",
	                                    type: 'POST',
	                                    data: {
	                                        moh_id: id,
											log_user: log_user,
											log_group: log_group
	                                    },
	                                    success: function(data) {
	                                    console.log(data);
	                                        if (data == 1) {
	                                           swal("<?php $lh->translateText("success"); ?>", "<?php $lh->translateText("moh_delete_success"); ?>", "success");
	                                           window.setTimeout(function(){location.reload()},1000)
	                                        } else {
	                                            sweetAlert("<?php $lh->translateText("oups"); ?>", "<?php $lh->translateText("something_went_wrong"); ?>"+data, "error");
	                                        }
	                                    }
	                                });
	                            } else {
	                                    swal("<?php $lh->translateText("cancelled"); ?>", "<?php $lh->translateText("cancel_msg"); ?>", "error");
	                            }
	                        }
	                    );
					});

		//-------------------- end of main moh events

		/*******************
		** VOICEFILES EVENTS
		*******************/

			/********
			** INIT WIZARD
			*******/
				var voicefile_form = $("#voicefile_form"); // init form wizard

			    voicefile_form.validate({
			        errorPlacement: function errorPlacement(error, element) { element.after(error); }
			    });
			    voicefile_form.children("div").steps({
			        headerTag: "h4",
			        bodyTag: "fieldset",
			        transitionEffect: "slideLeft",
			        onStepChanging: function (event, currentIndex, newIndex)
			        {
			        	// Allways allow step back to the previous step even if the current step is not valid!
				        if (currentIndex > newIndex) {
				            return true;
				        }

						// Clean up if user went backward before
					    if (currentIndex < newIndex)
					    {
					        // To remove error styles
					        $(".body:eq(" + newIndex + ") label.error", moh_form).remove();
					        $(".body:eq(" + newIndex + ") .error", moh_form).removeClass("error");
					    }

			            voicefile_form.validate().settings.ignore = ":disabled,:hidden";
			            return voicefile_form.valid();
			        },
			        onFinishing: function (event, currentIndex)
			        {
			            voicefile_form.validate().settings.ignore = ":disabled";
			            return voicefile_form.valid();
			        },
			        onFinished: function (event, currentIndex)
			        {

			        	$('#finish').text("Loading...");
			        	$('#finish').attr("disabled", true);
			        	$('.upload-loader').show();

			        	/*********
						** ADD EVENT
						*********/
				            // submit form
				            	voicefile_form.submit();
			        }
			    }); // end of wizard

			// upload result
				<?php
					if($_GET['upload_result'] == "success") {
				?>
						swal(
							{
								title: "<?php $lh->translateText("success"); ?>",
								text: "<?php $lh->translateText("upload_voicefile_success"); ?>",
								type: "success"
							},
							function(){
								window.location.href = 'audiofiles.php';
							}
						);
				<?php
					}elseif($_GET['upload_result'] == "error"){
				?>
						swal(
							{
								title: "<?php $lh->translateText("oups"); ?>",
								text: "<?php $lh->translateText("file_upload_failed"); ?>",
								type: "error"
							},
							function(){
								window.location.href = 'audiofiles.php';
							}
						);
				<?php
					}elseif($_GET['upload_result'] == "exists"){
				?>
						swal(
							{
								title: "<?php $lh->translateText("oups"); ?>",
								text: "<?php $lh->translateText("file_already_exists"); ?>",
								type: "error"
							},
							function(){
								window.location.href = 'audiofiles.php';
							}
						);
				<?php		
					}
				?>

			// On play
				$(document).on('click','.play_voice_file',function() {
					var audioFile = $(this).attr('data-location');

					var sourceFile = '<audio class="audio_file" controls>';
					    sourceFile += '<source src="'+ audioFile +'" type="audio/mpeg" download="true"/>';
					    sourceFile += '</audio>';

					var voicedetails = $(this).attr('data-details');
					$('.voice-details').html(voicedetails);
					$('.download-audio-file').attr('href', audioFile);
					$('.voice-player').html(sourceFile);
					$('#voice-playback-modal').modal('show');

					var aud = $('.audio_file').get(0);
					aud.play();
				});

			// pause
				$('#voice-playback-modal').on('hidden.bs.modal', function () {
					var aud = $('.audio_file').get(0);
					aud.pause();
				});

			// browse
				$('.btn-browse-file').click(function(){
					$('#voice_file').click();
				});

			//voice_file
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

			//voice_file_holder
		        $('.voice_file_holder').change(function(){
		          var holderVal = $(this).val();
		          var file = $('#voice_file').val();

		          if(holderVal != file){
		            $('#voice_file').val('');
		          }
		        });

		    // clear form
		        $('#form-voicefiles-modal').on('hidden.bs.modal', function () {
		            $('#voice_file').val('');
		            $('.voice_file_holder').val();
		        });

		//-------------------- end of main voice files events

		/*******************
		** FILTERS
		*******************/

			// disable special characters on Script ID
				$('#moh_id').bind('keypress', function (event) {
				    var regex = new RegExp("^[a-zA-Z0-9]+$");
				    var key = String.fromCharCode(!event.charCode ? event.which : event.charCode);
				    if (!regex.test(key)) {
				       event.preventDefault();
				       return false;
				    }
				});

			// disable special characters on MOH Name
				$('#moh_name').bind('keypress', function (event) {
				    var regex = new RegExp("^[a-zA-Z0-9 ]+$");
				    var key = String.fromCharCode(!event.charCode ? event.which : event.charCode);
				    if (!regex.test(key)) {
				       event.preventDefault();
				       return false;
				    }
				});

			// initialize select2
			$('.select2').select2({ theme: 'bootstrap' });
			$.fn.select2.defaults.set( "theme", "bootstrap" );
	});
</script>

		<?php print $ui->creamyFooter(); ?>
    </body>
</html>
