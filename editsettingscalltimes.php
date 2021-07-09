<?php
/**
 * @file        editsettingscalltimes.php
 * @brief       Manage Calltimes
 * @copyright   Copyright (c) 2018 GOautodial Inc.
 * @author      Alexander Jim Abenoja
 * @author		Demian Lizandro A. Biscocho
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

	require_once('php/UIHandler.php');
	require_once('php/APIHandler.php');
	require_once('./php/CRMDefaults.php');
    require_once('./php/LanguageHandler.php');
    include('./php/Session.php');

	$ui = \creamy\UIHandler::getInstance();
	$api = \creamy\APIHandler::getInstance();
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
        <title><?php $lh->translateText('portal_title'); ?> - <?php $lh->translateText("edit_call_times"); ?></title>
        <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
        
        <?php print $ui->standardizedThemeCSS(); ?>

        <?php print $ui->creamyThemeCSS(); ?>

        <!-- datetime picker --> 
		<link rel="stylesheet" href="js/dashboard/eonasdan-bootstrap-datetimepicker/build/css/bootstrap-datetimepicker.min.css">

		<!-- Date Picker -->
        <script type="text/javascript" src="js/dashboard/eonasdan-bootstrap-datetimepicker/build/js/moment.js"></script>
		<script type="text/javascript" src="js/dashboard/eonasdan-bootstrap-datetimepicker/build/js/bootstrap-datetimepicker.min.js"></script>

        <script type="text/javascript">
			$(window).ready(function() {
				$(".preloader").fadeOut("slow");
			});
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
                        <small><?php $lh->translateText("edit_call_times"); ?></small>
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
						$output = $api->API_getCalltimeInfo($cid);
						//echo "<pre>";
						//var_dump($output);
						if ($output->result == "success") {
							
							$user_groups = $api->API_getAllUserGroups();
							$voicefiles = $api->API_getAllVoiceFiles();
							
							# Result was OK!
							if($output->ct_default_start !== NULL && $output->ct_default_stop !== NULL){
								$start_default =  date('h:i A', strtotime(sprintf("%04d", $output->ct_default_start)));
								$stop_default =  date('h:i A', strtotime(sprintf("%04d", $output->ct_default_stop)));
							}else{
								$start_default =  "NULL";
								$stop_default =  "NULL";
							}
							
							if($output->ct_sunday_start != $output->ct_sunday_stop){
								$start_sunday =  date('h:i A', strtotime(sprintf("%04d", $output->ct_sunday_start)));
								$stop_sunday =  date('h:i A', strtotime(sprintf("%04d", $output->ct_sunday_stop)));
							}else{
								$start_sunday =  "NULL";
								$stop_sunday =  "NULL";
							}
							
							if($output->ct_monday_start != $output->ct_monday_stop){
								$start_monday =  date('h:i A', strtotime(sprintf("%04d", $output->ct_monday_start)));
								$stop_monday =  date('h:i A', strtotime(sprintf("%04d", $output->ct_monday_stop)));
							}else{
								$start_monday =  "NULL";
								$stop_monday =  "NULL";
							}
							
							if($output->ct_tuesday_start != $output->ct_tuesday_stop){
								$start_tuesday =  date('h:i A', strtotime(sprintf("%04d", $output->ct_tuesday_start)));
								$stop_tuesday =  date('h:i A', strtotime(sprintf("%04d", $output->ct_tuesday_stop)));
							}else{
								$start_tuesday =  "NULL";
								$stop_tuesday =  "NULL";
							}
							
							if($output->ct_wednesday_start != $output->ct_wednesday_stop){
								$start_wednesday =  date('h:i A', strtotime(sprintf("%04d", $output->ct_wednesday_start)));
								$stop_wednesday =  date('h:i A', strtotime(sprintf("%04d", $output->ct_wednesday_stop)));
							}else{
								$start_wednesday =  "NULL";
								$stop_wednesday =  "NULL";
							}
							
							if($output->ct_thursday_start != $output->ct_thursday_stop){
								$start_thursday =  date('h:i A', strtotime(sprintf("%04d", $output->ct_thursday_start)));
								$stop_thursday =  date('h:i A', strtotime(sprintf("%04d", $output->ct_thursday_stop)));
							}else{
								$start_thursday =  "NULL";
								$stop_thursday =  "NULL";
							}
							
							if($output->ct_friday_start != $output->ct_friday_stop){
								$start_friday =  date('h:i A', strtotime(sprintf("%04d", $output->ct_friday_start)));
								$stop_friday =  date('h:i A', strtotime(sprintf("%04d", $output->ct_friday_stop)));
							}else{
								$start_friday =  "NULL";
								$stop_friday =  "NULL";
							}
							
							if($output->ct_saturday_start != $output->ct_saturday_stop){
								$start_saturday =  date('h:i A', strtotime(sprintf("%04d", $output->ct_saturday_start)));
								$stop_saturday =  date('h:i A', strtotime(sprintf("%04d", $output->ct_saturday_stop)));
							}else{
								$start_saturday =  "NULL";
								$stop_saturday =  "NULL";
							}
				?>
						<legend><?php $lh->translateText("modify_calltime_id"); ?> <u><?php echo $cid;?></u></legend>
							<form id="modifyform">
								<input type="hidden" name="modifyid" value="<?php echo $cid;?>">
								<input type="hidden" name="log_user" value="<?php echo $_SESSION['user'];?>">
								<input type="hidden" name="log_group" value="<?php echo $_SESSION['usergroup'];?>">
						<!-- Custom Tabs -->
						<div role="tabpanel">
						<!--<div class="nav-tabs-custom">-->
							<ul role="tablist" class="nav nav-tabs nav-justified">
								<li class="active"><a href="#tab_1" data-toggle="tab"> <?php $lh->translateText("basic_settings"); ?></a></li>
							</ul>
						   <!-- Tab panes-->
						   <div class="tab-content">
								<!-- BASIC SETTINGS -->
								<div id="tab_1" class="tab-pane fade in active">
								<fieldset>
									<div class="form-group mt">
										<label for="calltime_name" class="col-sm-2 control-label"><?php $lh->translateText("call_time_name"); ?></label>
										<div class="col-sm-10 mb">
											<input type="text" class="form-control" name="calltime_name" id="calltime_name" placeholder="<?php $lh->translateText("call_time_name"); ?>" value="<?php echo $output->call_time_name;?>">
										</div>
									</div>
									<div class="form-group">
										<label for="calltime_comments" class="col-sm-2 control-label"><?php $lh->translateText("call_time_comments"); ?></label>
										<div class="col-sm-10 mb">
											<input type="text" class="form-control" name="calltime_comments" id="calltime_comments" placeholder="<?php $lh->translateText("call_time_comments"); ?>" value="<?php echo $output->call_time_comments;?>">
										</div>
									</div>
									<div class="form-group">
										<label for="protocol" class="col-sm-2 control-label"><?php $lh->translateText("user_groups"); ?></label>
										<div class="col-sm-10 mb">
											<select class="form-control" id="usergroup" name="usergroup">
												<option value="---ALL---"><?php $lh->translateText("all_usergroups"); ?></option>
												<?php
													for($a=0;$a<count($user_groups->user_group);$a++){
												?>
													<option value="<?php echo $user_groups->user_group[$a];?>" <?php if($output->user_group == $user_groups->user_group[$a]){echo "selected";}?> >  
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
												<label class="col-lg-3"><?php $lh->translateText("start"); ?></label>
												<label class="col-lg-3"><?php $lh->translateText("stop"); ?></label>
												<label class="col-lg-6"><?php $lh->translateText("after_hours"); ?></label>
											</div>
										</div>
									</div>
									<div class="form-group">
										<label class="control-label col-lg-2"><?php $lh->translateText("default"); ?></label>
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
														<option value="" disabled><?php $lh->translateText("audio_chooser"); ?></option>
														<option value="" <?php if ($output->default_afterhours_filename_override == "") echo "selected"; ?>> <?php $lh->translateText("-none-"); ?></option>
														<?php
															for($a=0;$a<count($voicefiles->file_name);$a++){
																$file = substr($voicefiles->file_name[$a], 0, strrpos($voicefiles->file_name[$a], "."));
														?>
															<option value="<?php echo $file;?>" <?php if ($output->default_afterhours_filename_override === $file) echo "selected"; ?>>  <?php echo $file;?>  </option>
														<?php
															}
														?>
													</select>
												</div>
											</div>
										</div>
									</div>
									<div class="form-group">
										<label class="control-label col-lg-2"><?php $lh->translateText("sunday"); ?></label>
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
														<option value="" disabled><?php $lh->translateText("audio_chooser"); ?> </option>
														<option value="" <?php if ($output->sunday_afterhours_filename_override == "") echo "selected"; ?>><?php $lh->translateText("-none-"); ?></option>
														<?php
															for($a=0;$a<count($voicefiles->file_name);$a++){
																$file = substr($voicefiles->file_name[$a], 0, strrpos($voicefiles->file_name[$a], "."));
														?>
															<option value="<?php echo $file;?>" <?php if ($output->sunday_afterhours_filename_override === $file) echo "selected"; ?>>  <?php echo $file;?>  </option>
														<?php
															}
														?>
													</select>
												</div>
											</div>
										</div>
									</div>
									<div class="form-group">
										<label class="control-label col-lg-2"><?php $lh->translateText("monday"); ?></label>
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
														<option value="" disabled> <?php $lh->translateText("audio_chooser"); ?> </option>
														<option value="" <?php if ($output->monday_afterhours_filename_override == "") echo "selected"; ?>> <?php $lh->translateText("-none-"); ?> </option>
														<?php
															for($a=0;$a<count($voicefiles->file_name);$a++){
																$file = substr($voicefiles->file_name[$a], 0, strrpos($voicefiles->file_name[$a], "."));
														?>
															<option value="<?php echo $file;?>" <?php if ($output->monday_afterhours_filename_override === $file) echo "selected"; ?>>  <?php echo $file;?>  </option>
														<?php
															}
														?>
													</select>
												</div>
											</div>
										</div>
									</div>
									<div class="form-group">
										<label class="control-label col-lg-2"><?php $lh->translateText("tuesday"); ?></label>
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
														<option value="" disabled> <?php $lh->translateText("audio_chooser"); ?> </option>
														<option value="" <?php if ($output->tuesday_afterhours_filename_override == "") echo "selected"; ?>><?php $lh->translateText("-none-"); ?></option>
														<?php
															for($a=0;$a<count($voicefiles->file_name);$a++){
																$file = substr($voicefiles->file_name[$a], 0, strrpos($voicefiles->file_name[$a], "."));
														?>
															<option value="<?php echo $file;?>" <?php if ($output->tuesday_afterhours_filename_override === $file) echo "selected"; ?>>  <?php echo $file;?>  </option>
														<?php
															}
														?>
													</select>
												</div>
											</div>
										</div>
									</div>
									<div class="form-group">
										<label class="control-label col-lg-2"><?php $lh->translateText("wednesday"); ?></label>
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
														<option value="" disabled><?php $lh->translateText("audio_chooser"); ?></option>
														<option value="" <?php if ($output->wednesday_afterhours_filename_override == "") echo "selected"; ?>><?php $lh->translateText("-none-"); ?></option>
														<?php
															for($a=0;$a<count($voicefiles->file_name);$a++){
																$file = substr($voicefiles->file_name[$a], 0, strrpos($voicefiles->file_name[$a], "."));
														?>
															<option value="<?php echo $file;?>" <?php if ($output->wednesday_afterhours_filename_override === $file) echo "selected"; ?>>  <?php echo $file;?>  </option>
														<?php
															}
														?>
													</select>
												</div>
											</div>
										</div>
									</div>
									<div class="form-group">
										<label class="control-label col-lg-2"><?php $lh->translateText("thursday"); ?></label>
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
														<option value="" disabled> <?php $lh->translateText("audio_chooser"); ?> </option>
														<option value="" <?php if ($output->thursday_afterhours_filename_override == "") echo "selected"; ?>> <?php $lh->translateText("-none-"); ?> </option>
														<?php
															for($a=0;$a<count($voicefiles->file_name);$a++){
																$file = substr($voicefiles->file_name[$a], 0, strrpos($voicefiles->file_name[$a], "."));
														?>
															<option value="<?php echo $file;?>" <?php if ($output->thursday_afterhours_filename_override === $file) echo "selected"; ?>>  <?php echo $file;?>  </option>
														<?php
															}
														?>
													</select>
												</div>
											</div>
										</div>
									</div>
									<div class="form-group">
										<label class="control-label col-lg-2"><?php $lh->translateText("friday"); ?></label>
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
														<option value="" disabled> <?php $lh->translateText("audio_chooser"); ?> </option>
														<option value="" <?php if ($output->friday_afterhours_filename_override == "") echo "selected"; ?>> <?php $lh->translateText("-none-"); ?> </option>
														<?php
															for($a=0;$a<count($voicefiles->file_name);$a++){
																$file = substr($voicefiles->file_name[$a], 0, strrpos($voicefiles->file_name[$a], "."));
														?>
															<option value="<?php echo $file;?>" <?php if ($output->friday_afterhours_filename_override === $file) echo "selected"; ?>>  <?php echo $file;?>  </option>
														<?php
															}
														?>
													</select>
												</div>
											</div>
										</div>
									</div>
									<div class="form-group">
										<label class="control-label col-lg-2"><?php $lh->translateText("saturday"); ?></label>
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
														<option value="" disabled> <?php $lh->translateText("audio_chooser"); ?> </option>
														<option value="" <?php if ($output->saturday_afterhours_filename_override == "") echo "selected"; ?>><?php $lh->translateText("-none-"); ?> </option>
														<?php
															for($a=0;$a<count($voicefiles->file_name);$a++){
																$file = substr($voicefiles->file_name[$a], 0, strrpos($voicefiles->file_name[$a], "."));
														?>
															<option value="<?php echo $file;?>" <?php if ($output->saturday_afterhours_filename_override === $file) echo "selected"; ?>>  <?php echo $file;?>  </option>
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
										<a href="settingscalltimes.php" type="button" id="cancel" class="btn btn-danger"><i class="fa fa-close"></i> <?php $lh->translateText('cancel'); ?> </a>
										<button type="submit" class="btn btn-primary" id="modifyCalltimesOkButton" href="#"> <span id="update_button"><i class="fa fa-check"></i> <?php $lh->translateText('update'); ?></span></button>
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
					defaultDate: '',
                    format: 'LT'
                });
                $('.end_time').datetimepicker({
					defaultDate: '',
                    format: 'LT'
                });

                $(document).on('click', '#cancel', function(){
					swal("<?php $lh->translateText('cancelled'); ?>", "<?php $lh->translateText('cancel_msg'); ?>", "error");
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
                          				console.log(data);
						  	$('#update_button').html("<i class='fa fa-check'></i> Update");
						  	$('#modifyCalltimesOkButton').prop("disabled", false);
							if(data == 1){
								swal(
									{
										title: "<?php $lh->translateText("success"); ?>", 
										text: "<?php $lh->translateText("calltime_modify_success"); ?>", 
										type: "success"
									},
									function() {
										location.replace("./settingscalltimes.php");
									}
								);
							}else{
								sweetAlert("<?php $lh->translateText("oups"); ?>", "<?php $lh->translateText("something_went_wrong"); ?>", "error");
							}
                        }
                    });
				});
				
				$('#calltime_name').bind('keypress', function (event) {
				    var regex = new RegExp("^[a-zA-Z0-9 ]+$");
				    var key = String.fromCharCode(!event.charCode ? event.which : event.charCode);
				    if (!regex.test(key)) {
				       event.preventDefault();
				       return false;
				    }
				});
			});
		</script>

		<?php print $ui->getRightSidebar($user->getUserId(), $user->getUserName(), $user->getUserAvatar()); ?>
		<?php print $ui->creamyFooter(); ?>
    </body>
</html>
