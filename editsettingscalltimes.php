<?php
	
	###################################################
	### Name: settingscalltimes.php 				###
	### Functions: Manage Calltimes 				###
	### Copyright: GOAutoDial Ltd. (c) 2011-2016	###
	### Version: 4.0 								###
	### Written by: Alexander Jim H. Abenoja		###
	### License: AGPLv2								###
	###################################################

	require_once('./php/CRMDefaults.php');
	require_once('./php/UIHandler.php');
	//require_once('./php/DbHandler.php');
	require_once('./php/LanguageHandler.php');
	require('./php/Session.php');
	require_once('./php/goCRMAPISettings.php');

	// initialize structures
	$ui = \creamy\UIHandler::getInstance();
	$lh = \creamy\LanguageHandler::getInstance();
	$user = \creamy\CreamyUser::currentUser();

$cid = NULL;
if (isset($_POST["cid"])) {
	$cid = $_POST["cid"];
}

?>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Edit Call Times</title>
        <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
        
        <?php print $ui->standardizedThemeCSS(); ?>

        <?php print $ui->creamyThemeCSS(); ?>

        <!-- datetime picker --> 
		<link rel="stylesheet" href="theme_dashboard/eonasdan-bootstrap-datetimepicker/build/css/bootstrap-datetimepicker.min.css">

		<!-- Date Picker -->
        <script type="text/javascript" src="theme_dashboard/eonasdan-bootstrap-datetimepicker/build/js/moment.js"></script>
		<script type="text/javascript" src="theme_dashboard/eonasdan-bootstrap-datetimepicker/build/js/bootstrap-datetimepicker.min.js"></script>

        <script type="text/javascript">
			$(window).ready(function() {
				$(".preloader").fadeOut("slow");
			})
		</script>
    </head>
    <style>
    	select{
    		font-weight: normal;
    	}
    </style>
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
                    <h1 style="font-weight:normal;">
                        <?php $lh->translateText("settings"); ?>
                        <small><?php $lh->translateText("Call Times Edit"); ?></small>
                    </h1>
                    <ol class="breadcrumb">
                        <li><a href="./index.php"><i class="fa fa-edit"></i> <?php $lh->translateText("home"); ?></a></li>
                        <li> <?php $lh->translateText("settings"); ?></li>
                        <?php
							if(isset($_POST["cid"])){
						?>	
							<li><a href="./settingscalltimes.php"><?php $lh->translateText("call_times"); ?></a></li>
                        <?php
							}
                        ?>	                    
                        <li class="active"><?php $lh->translateText("modify"); ?></li>
                    </ol>
                </section>
                <!-- Main content -->
	            <section class="content">
					<div class="panel panel-default">
	                    <div class="panel-body">
						<!-- standard custom edition form -->

						<?php
						$errormessage = NULL;
						
							$url = gourl."/goCalltimes/goAPI.php"; #URL to GoAutoDial API. (required)
        
					        $postfields["goUser"] = goUser; #Username goes here. (required)
					        $postfields["goPass"] = goPass; #Password goes here. (required)
					        $postfields["goAction"] = "getCalltimesInfo"; #action performed by the [[API:Functions]]. (required)
					        $postfields["responsetype"] = responsetype; #json. (required)
					        $postfields["call_time_id"] = $_POST['cid']; #Desired uniqueid. (required)
					        
					        $ch = curl_init();
					        curl_setopt($ch, CURLOPT_URL, $url);
					        //curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
					        curl_setopt($ch, CURLOPT_POST, 1);
					        curl_setopt($ch, CURLOPT_TIMEOUT, 100);
					        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
					        curl_setopt($ch, CURLOPT_POSTFIELDS, $postfields);
					        $data = curl_exec($ch);
					        curl_close($ch);
					        $output = json_decode($data);

					        //var_dump($output);

							if ($output->result=="success") {
							
							$user_groups = $ui->API_goGetUserGroupsList();
							$voicefiles = $ui->API_GetVoiceFilesList();

							# Result was OK!
								for($i=0;$i<count($output->call_time_id);$i++){

								if(isset($output->ct_default_start[$i])){
									$start_default =  date('h:i A', strtotime($output->ct_default_start[$i]));
						        	$stop_default =  date('h:i A', strtotime($output->ct_default_stop[$i]));
								}else{
									$start_default =  "12:00 AM";
						        	$stop_default =  "12:00 AM";
								}
								
								if(isset($output->ct_sunday_start[$i])){
									$start_sunday =  date('h:i A', strtotime($output->ct_sunday_start[$i]));
						        	$stop_sunday =  date('h:i A', strtotime($output->ct_sunday_stop[$i]));
								}else{
									$start_sunday =  "12:00 AM";
						        	$stop_sunday =  "12:00 AM";
								}
						        
								if(isset($output->ct_monday_start[$i])){
									$start_monday =  date('h:i A', strtotime($output->ct_sunday_start[$i]));
						        	$stop_monday =  date('h:i A', strtotime($output->ct_sunday_stop[$i]));
								}else{
									$start_monday =  "12:00 AM";
						        	$stop_monday =  "12:00 AM";
								}

								if(isset($output->ct_tuesday_start[$i])){
									$start_tuesday =  date('h:i A', strtotime($output->ct_tuesday_start[$i]));
						        	$stop_tuesday =  date('h:i A', strtotime($output->ct_tuesday_stop[$i]));
								}else{
									$start_tuesday =  "12:00 AM";
						        	$stop_tuesday =  "12:00 AM";
								}

								if(isset($output->ct_wednesday_start[$i])){
									$start_wednesday =  date('h:i A', strtotime($output->ct_wednesday_start[$i]));
						        	$stop_wednesday =  date('h:i A', strtotime($output->ct_wednesday_stop[$i]));
								}else{
									$start_wednesday =  "12:00 AM";
						        	$stop_wednesday =  "12:00 AM";
								}

								if(isset($output->ct_thursday_start[$i])){
									$start_thursday =  date('h:i A', strtotime($output->ct_thursday_start[$i]));
						        	$stop_thursday =  date('h:i A', strtotime($output->ct_thursday_stop[$i]));
								}else{
									$start_thursday =  "12:00 AM";
						        	$stop_thursday =  "12:00 AM";
								}

								if(isset($output->ct_friday_start[$i])){
									$start_friday =  date('h:i A', strtotime($output->ct_friday_start[$i]));
						        	$stop_friday =  date('h:i A', strtotime($output->ct_friday_stop[$i]));
								}else{
									$start_friday =  "12:00 AM";
						        	$stop_friday =  "12:00 AM";
								}

								if(isset($output->ct_saturday_start[$i])){
									$start_saturday =  date('h:i A', strtotime($output->ct_saturday_start[$i]));
						        	$stop_saturday =  date('h:i A', strtotime($output->ct_saturday_stop[$i]));
								}else{
									$start_saturday =  "12:00 AM";
						        	$stop_saturday =  "12:00 AM";
								}
							
						?>

						<legend>MODIFY CALL TIME ID : <u><?php echo $cid;?></u></legend>
                    	
							<form id="modifyform">
								<input type="hidden" name="modifyid" value="<?php echo $cid;?>">
							
						<!-- Custom Tabs -->
						<div role="tabpanel">
						<!--<div class="nav-tabs-custom">-->
							<ul role="tablist" class="nav nav-tabs nav-justified">
								<li class="active"><a href="#tab_1" data-toggle="tab"> Basic Settings</a></li>
							</ul>
			               <!-- Tab panes-->
			               <div class="tab-content">

				               	<!-- BASIC SETTINGS -->
				                <div id="tab_1" class="tab-pane fade in active">
				                	<fieldset>
										<div class="form-group mt">
											<label for="calltime_name" class="col-sm-2 control-label">Call Time Name</label>
											<div class="col-sm-10 mb">
												<input type="text" class="form-control" name="calltime_name" id="calltime_name" placeholder="Call Time Name" value="<?php echo $output->call_time_name[$i];?>">
											</div>
										</div>
										<div class="form-group">
											<label for="calltime_comments" class="col-sm-2 control-label">Call Time Comments</label>
											<div class="col-sm-10 mb">
												<input type="text" class="form-control" name="calltime_comments" id="calltime_comments" placeholder="Call Time Comments" value="<?php echo $output->call_time_comments[$i];?>">
											</div>
										</div>
										<div class="form-group">
											<label for="protocol" class="col-sm-2 control-label">User Group</label>
											<div class="col-sm-10 mb">
												<select class="form-control" id="usergroup" name="usergroup">
													<?php
														for($a=0;$a<count($user_groups->user_group);$a++){
													?>
														<option value="<?php echo $user_groups->user_group[$a];?>" <?php if($output->user_group[$i] == $user_groups->user_group[$a]){echo "selected";}?> >  
															<?php echo $user_groups->user_group[$a].' - '.$user_groups->group_name[$a];?>  
														</option>
													<?php
														}
													?>
												</select>
											</div>
										</div>
									</fieldset>
									<fieldset>
										<div class="form-group">
												<label class="col-lg-2">&nbsp;</label>
											<div class="col-lg-10">
												<div class="row">
													<label class="col-lg-3">Start</label>
													<label class="col-lg-3">Stop</label>
													<label class="col-lg-6">After Hours Audio</label>
												</div>
											</div>
										</div>
										<div class="form-group">
											<label class="control-label col-lg-2">Dafault:</label>
											<div class="col-lg-10 mb">
												<div class="row">
													<div class="col-lg-3">
														<input type="text" class="form-control start_time" name="start_default" value="<?php echo $start_default;?>">
													</div>
													<div class="col-lg-3">
														<input type="text" class="form-control end_time" name="stop_default" value="<?php echo $stop_default;?>">
													</div>
													<div class="col-lg-6">
														<select class="form-control" name="audio_default">
															<option value="" selected disabled> - - - Audio Chooser - - - </option>
															<?php
																for($a=0;$a<count($voicefiles->file_name);$a++){
															?>
																<option value="<?php echo $voicefiles->file_name[$a];?>">  <?php echo $voicefiles->file_name[$a];?>  </option>
															<?php
																}
															?>
														</select>
													</div>
												</div>
											</div>
										</div>
										<div class="form-group">
											<label class="control-label col-lg-2">Sunday:</label>
											<div class="col-lg-10 mb">
												<div class="row">
													<div class="col-lg-3">
														<input type="text" class="form-control start_time" name="start_sunday" value="<?php echo $start_sunday;?>">
													</div>
													<div class="col-lg-3">
														<input type="text" class="form-control end_time" name="stop_sunday" value="<?php echo $stop_sunday;?>">
													</div>
													<div class="col-lg-6">
														<select class="form-control" name="audio_sunday">
															<option value="" selected disabled> - - - Audio Chooser - - - </option>
															<?php
																for($a=0;$a<count($voicefiles->file_name);$a++){
															?>
																<option value="<?php echo $voicefiles->file_name[$a];?>">  <?php echo $voicefiles->file_name[$a];?>  </option>
															<?php
																}
															?>
														</select>
													</div>
												</div>
											</div>
										</div>
										<div class="form-group">
											<label class="control-label col-lg-2">Monday:</label>
											<div class="col-lg-10 mb">
												<div class="row">
													<div class="col-lg-3">
														<input type="text" class="form-control start_time" name="start_monday" value="<?php echo $start_monday;?>">
													</div>
													<div class="col-lg-3">
														<input type="text" class="form-control end_time" name="stop_monday" value="<?php echo $stop_monday;?>">
													</div>
													<div class="col-lg-6">
														<select class="form-control" name="audio_monday">
															<option value="" selected disabled> - - - Audio Chooser - - - </option>
															<?php
																for($a=0;$a<count($voicefiles->file_name);$a++){
															?>
																<option value="<?php echo $voicefiles->file_name[$a];?>">  <?php echo $voicefiles->file_name[$a];?>  </option>
															<?php
																}
															?>
														</select>
													</div>
												</div>
											</div>
										</div>
										<div class="form-group">
											<label class="control-label col-lg-2">Tuesday:</label>
											<div class="col-lg-10 mb">
												<div class="row">
													<div class="col-lg-3">
														<input type="text" class="form-control start_time" name="start_tuesday" value="<?php echo $start_tuesday;?>">
													</div>
													<div class="col-lg-3">
														<input type="text" class="form-control end_time" name="stop_tuesday" value="<?php echo $stop_tuesday;?>">
													</div>
													<div class="col-lg-6">
														<select class="form-control" name="audio_tuesday">
															<option value="" selected disabled> - - - Audio Chooser - - - </option>
															<?php
																for($a=0;$a<count($voicefiles->file_name);$a++){
															?>
																<option value="<?php echo $voicefiles->file_name[$a];?>">  <?php echo $voicefiles->file_name[$a];?>  </option>
															<?php
																}
															?>
														</select>
													</div>
												</div>
											</div>
										</div>
										<div class="form-group">
											<label class="control-label col-lg-2">Wednesday:</label>
											<div class="col-lg-10 mb">
												<div class="row">
													<div class="col-lg-3">
														<input type="text" class="form-control start_time" name="start_wednesday" value="<?php echo $start_wednesday;?>">
													</div>
													<div class="col-lg-3">
														<input type="text" class="form-control end_time" name="stop_wednesday" value="<?php echo $stop_wednesday;?>">
													</div>
													<div class="col-lg-6">
														<select class="form-control" name="audio_wednesday">
															<option value="" selected disabled> - - - Audio Chooser - - - </option>
															<?php
																for($a=0;$a<count($voicefiles->file_name);$a++){
															?>
																<option value="<?php echo $voicefiles->file_name[$a];?>">  <?php echo $voicefiles->file_name[$a];?>  </option>
															<?php
																}
															?>
														</select>
													</div>
												</div>
											</div>
										</div>
										<div class="form-group">
											<label class="control-label col-lg-2">Thursday:</label>
											<div class="col-lg-10 mb">
												<div class="row">
													<div class="col-lg-3">
														<input type="text" class="form-control start_time" name="start_thursday" value="<?php echo $start_thursday;?>">
													</div>
													<div class="col-lg-3">
														<input type="text" class="form-control end_time" name="stop_thursday" value="<?php echo $stop_thursday;?>">
													</div>
													<div class="col-lg-6">
														<select class="form-control" name="audio_thursday">
															<option value="" selected disabled> - - - Audio Chooser - - - </option>
															<?php
																for($a=0;$a<count($voicefiles->file_name);$a++){
															?>
																<option value="<?php echo $voicefiles->file_name[$a];?>">  <?php echo $voicefiles->file_name[$a];?>  </option>
															<?php
																}
															?>
														</select>
													</div>
												</div>
											</div>
										</div>
										<div class="form-group">
											<label class="control-label col-lg-2">Friday:</label>
											<div class="col-lg-10 mb">
												<div class="row">
													<div class="col-lg-3">
														<input type="text" class="form-control start_time" name="start_friday" value="<?php echo $start_friday;?>">
													</div>
													<div class="col-lg-3">
														<input type="text" class="form-control end_time" name="stop_friday" value="<?php echo $stop_friday;?>">
													</div>
													<div class="col-lg-6">
														<select class="form-control" name="audio_friday">
															<option value="" selected disabled> - - - Audio Chooser - - - </option>
															<?php
																for($a=0;$a<count($voicefiles->file_name);$a++){
															?>
																<option value="<?php echo $voicefiles->file_name[$a];?>">  <?php echo $voicefiles->file_name[$a];?>  </option>
															<?php
																}
															?>
														</select>
													</div>
												</div>
											</div>
										</div>
										<div class="form-group">
											<label class="control-label col-lg-2">Saturday:</label>
											<div class="col-lg-10 mb">
												<div class="row">
													<div class="col-lg-3">
														<input type="text" class="form-control start_time" name="start_saturday" value="<?php echo $start_saturday;?>">
													</div>
													<div class="col-lg-3">
														<input type="text" class="form-control end_time" name="stop_saturday" value="<?php echo $stop_saturday;?>">
													</div>
													<div class="col-lg-6">
														<select class="form-control" name="audio_saturday">
															<option value="" selected disabled> - - - Audio Chooser - - - </option>
															<?php
																for($a=0;$a<count($voicefiles->file_name);$a++){
															?>
																<option value="<?php echo $voicefiles->file_name[$a];?>">  <?php echo $voicefiles->file_name[$a];?>  </option>
															<?php
																}
															?>
														</select>
													</div>
												</div>
											</div>
										</div>
									</fieldset>
								</div><!-- tab 1 -->

			                    <!-- FOOTER BUTTONS -->
			                    <fieldset class="footer-buttons">
			                        <div class="box-footer">
			                           <div class="col-sm-3 pull-right">
												<a href="settingscalltimes.php" type="button" id="cancel" class="btn btn-danger"><i class="fa fa-close"></i> Cancel </a>
			                           	
			                                	<button type="submit" class="btn btn-primary" id="modifyCalltimesOkButton" href=""> <span id="update_button"><i class="fa fa-check"></i> Update</span></button>
											
			                           </div>
			                        </div>
			                    </fieldset>

				            	</div><!-- end of tab content -->
	                    	</div><!-- tab panel -->
	                    </form>
	                </div><!-- body -->
	            </div>
            </section>
					<?php
							}
						}	
                        
					?>
					
				<!-- /.content -->
            </aside><!-- /.right-side -->
        </div><!-- ./wrapper -->

		<!-- Modal Dialogs -->
		<?php print $ui->standardizedThemeJS(); ?>
		<?php include_once "./php/ModalPasswordDialogs.php" ?>

		<script type="text/javascript">
			$(document).ready(function() {

			    //initialize timepicker
				$('.start_time').datetimepicker({
                    format: 'LT'
                });
                $('.end_time').datetimepicker({
                    format: 'LT'
                });

                $(document).on('click', '#cancel', function(){
					swal("Cancelled", "No action has been done :)", "error");
				});
				/** 
				 * Modifies a telephony list
			 	 */
			 	$(document).on('click','#modifyCalltimesOkButton',function() {
					//submit the form
					$('#update_button').html("<i class='fa fa-edit'></i> Updating.....");
					$('#modifyCalltimesOkButton').prop("disabled", true);

					$.ajax({
                        url: "./php/ModifyCalltimes.php",
                        type: 'POST',
                        data: $("#modifyform").serialize(),
                        success: function(data) {
                          // console.log(data);
                              if(data == 1){
                                    swal("Success!", "Call Times Successfully Updated!", "success")
                                    window.setTimeout(function(){location.reload()},2000)
                                    $('#update_button').html("<i class='fa fa-check'></i> Update");
                                    $('#modifyCalltimesOkButton').prop("disabled", false);
                              }
                              else{
                                  	sweetAlert("Oops...", "Something went wrong!", "error");
									$('#update_button').html("<i class='fa fa-check'></i> Update");
									$('#modifyCalltimesOkButton').prop("disabled", false);
                              }
                        }
                    });				
				});
				
			});
		</script>

		<?php print $ui->creamyFooter(); ?>
    </body>
</html>
