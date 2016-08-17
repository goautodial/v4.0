<?php

	###################################################
	### Name: edittelephonyscript.php 				###
	### Functions: Edit Scripts 					###
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

$script_id = NULL;
if (isset($_POST["script_id"])) {
	$script_id = $_POST["script_id"];
}

?>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Edit Script</title>
        <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
        
        <!-- Call for standardized css -->
        <?php print $ui->standardizedThemeCSS();?>

        <?php print $ui->creamyThemeCSS(); ?>

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
                        <small><?php $lh->translateText("Script Edit"); ?></small>
                    </h1>
                    <ol class="breadcrumb">
                        <li><a href="./index.php"><i class="fa fa-edit"></i> <?php $lh->translateText("home"); ?></a></li>
                        <li> <?php $lh->translateText("settings"); ?></li>
                        <?php
							if(isset($_POST["script_id"])){
						?>	
							<li><a href="./telephonyscripts.php"><?php $lh->translateText("scripts"); ?></a></li>
                        <?php
							}
                        ?>	                    
                        <li class="active"><?php $lh->translateText("modify"); ?></li>
                    </ol>
                </section>

						<!-- standard custom edition form -->
					<?php
					$errormessage = NULL;

					//if(isset($extenid)) {
						$url = gourl."/goScripts/goAPI.php"; #URL to GoAutoDial API. (required)
				        $postfields["goUser"] = goUser; #Username goes here. (required)
				        $postfields["goPass"] = goPass; #Password goes here. (required)
				        $postfields["goAction"] = "getScriptInfo"; #action performed by the [[API:Functions]]. (required)
				        $postfields["responsetype"] = responsetype; #json. (required)
				        $postfields["script_id"] = $script_id; #Desired exten ID. (required)

				         $ch = curl_init();
				         curl_setopt($ch, CURLOPT_URL, $url);
				         curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
				         curl_setopt($ch, CURLOPT_POST, 1);
				         curl_setopt($ch, CURLOPT_TIMEOUT, 100);
				         curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
				         curl_setopt($ch, CURLOPT_POSTFIELDS, $postfields);
				         $data = curl_exec($ch);
				         curl_close($ch);
				         $output = json_decode($data);
				         
				        // var_dump($output);

						if ($output->result=="success") {
							
						# Result was OK!
							for($i=0;$i<count($output->script_id);$i++){
					?>

            <!-- Main content -->
            <section class="content">
				<div class="panel panel-default">
                    <div class="panel-body">
						<legend>MODIFY SCRIPT ID : <u><?php echo $script_id;?></u></legend>
                    	
							<form id="modifyform">
								<input type="hidden" name="modifyid" value="<?php echo $script_id;?>">
							
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
											<label for="script_name" class="col-sm-2 control-label">Script Name</label>
											<div class="col-sm-10 mb">
												<input type="text" class="form-control" name="script_name" id="script_name" placeholder="Script Name (Required)" value="<?php echo $output->script_name[$i];?>">
											</div>
										</div>
										<div class="form-group mt">
											<label for="script_comments" class="col-sm-2 control-label">Script Comments</label>
											<div class="col-sm-10 mb">
												<input type="text" class="form-control" name="script_comments" id="script_comments" placeholder="Script Comments (Optional)" value="<?php echo $output->script_comments[$i];?>">
											</div>
										</div>
										<div class="form-group">
											<label for="status" class="col-sm-2 control-label">Active</label>
											<div class="col-sm-10 mb">
												<select class="form-control" name="active" id="active">
												<?php
													$active = NULL;
													if($output->active[$i] == "Y"){
														$active .= '<option value="Y" selected> YES </option>';
													}else{
														$active .= '<option value="Y" > YES </option>';
													}
													
													if($output->active[$i] == "N" || $output->active[$i] == NULL){
														$active .= '<option value="N" selected> NO </option>';
													}else{
														$active .= '<option value="N" > NO </option>';
													}
													echo $active;
												?>
												</select>
											</div>
										</div>
										<div class="form-group">
											<label for="script_text" class="col-sm-2 control-label">Script Text</label>
											<div class="col-sm-10 mb">
												<div class="row">
													<div class="col-sm-12 mb">
														<div class="input-group">
															<span class="input-group-btn">
																<button type="button" class="btn btn-default" onClick="addtext();">Insert!</button>
															</span>
															<select class="form-control" name="script_text_dropdown" id="script_text_dropdown">
																<option value="fullname">Agent Name</option>
																<option value="vendor_lead_code">vendor_lead_code</option>
																<option value="source_id">source_id</option>
																<option value="list_id">list_id</option>
																<option value="gmt_offset_now">gmt_offset_now</option>
																<option value="called_since_last_reset">called_since_last_reset</option>
																<option value="phone_code">phone_code</option>
																<option value="phone_number">phone_number</option>
																<option value="title">title</option>
																<option value="first_name">first_name</option>
																<option value="middle_initial">middle_initial</option>
																<option value="last_name">last_name</option>
																<option value="address1">address1</option>
																<option value="address2">address2</option>
																<option value="address3">address3</option>
																<option value="city">city</option>
																<option value="state">state</option>
																<option value="province">province</option>
																<option value="postal_code">postal_code</option>
																<option value="country_code">country_code</option>
																<option value="gender">gender</option>
																<option value="date_of_birth">date_of_birth</option>
																<option value="alt_phone">alt_phone</option>
																<option value="email">email</option>
																<option value="security_phrase">security_phrase</option>
																<option value="comments">comments</option>
																<option value="lead_id">lead_id</option>
																<option value="campaign">campaign</option>
																<option value="phone_login">phone_login</option>
																<option value="group">group</option>
																<option value="channel_group">channel_group</option>
																<option value="SQLdate">SQLdate</option>
																<option value="epoch">epoch</option>
																<option value="uniqueid">uniqueid</option>
																<option value="customer_zap_channel">customer_zap_channel</option>
																<option value="server_ip">server_ip</option>
																<option value="SIPexten">SIPexten</option>
																<option value="session_id">session_id</option>
																<option value="dialed_number">dialed_number</option>
																<option value="dialed_label">dialed_label</option>
																<option value="rank">rank</option>
																<option value="owner">owner</option>
																<option value="camp_script">camp_script</option>
																<option value="in_script">in_script</option>
																<option value="script_width">script_width</option>
																<option value="script_height">script_height</option>
																<option value="recording_filename">recording_filename</option>
																<option value="recording_id">recording_id</option>
																<option value="user_custom_one">user_custom_one</option>
																<option value="user_custom_two">user_custom_two</option>
																<option value="user_custom_three">user_custom_three</option>
																<option value="user_custom_four">user_custom_four</option>
																<option value="user_custom_five">user_custom_five</option>
																<option value="preset_number_a">preset_number_a</option>
																<option value="preset_number_b">preset_number_b</option>
																<option value="preset_number_c">preset_number_c</option>
																<option value="preset_number_d">preset_number_d</option>
																<option value="preset_number_e">preset_number_e</option>
																<option value="preset_number_f">preset_number_f</option>
																<option value="preset_dtmf_a">preset_dtmf_a</option>
																<option value="preset_dtmf_b">preset_dtmf_b</option>
																<option value="did_id">did_id</option>
																<option value="did_extension">did_extension</option>
																<option value="did_pattern">did_pattern</option>
																<option value="did_description">did_description</option>
																<option value="closecallid">closecallid</option>
																<option value="xfercallid">xfercallid</option>
																<option value="agent_log_id">agent_log_id</option>
																<option value="entry_list_id">entry_list_id</option>
															</select>
														</div>
													</div>
													<div class="col-sm-12">
														<div class="panel">
															<div class="panel-body">
																<textarea rows="3" class="form-control note-editor" id="script_text" name="script_text"><?php echo $output->script_text[$i];?></textarea>
															</div>
														</div>
														
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
												<a href="telephonyscripts.php" id="cancel" type="button" class="btn btn-danger"><i class="fa fa-close"></i> Cancel </a>
			                           	
			                                	<button type="submit" class="btn btn-primary" id="modifyOkButton" href=""> <span id="update_button"><i class="fa fa-check"></i> Update</span></button>
											
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

  		
		<?php print $ui->standardizedThemeJS();?>
		<!-- Modal Dialogs -->
		<?php include_once "./php/ModalPasswordDialogs.php" ?>
		

		<script language="javascript" type="text/javascript">
			$(document).ready(function() {
				
				$(document).on('click', '#cancel', function(){
					swal("Cancelled", "No action has been done :)", "error");
				});

				/** 
				 * Modifies a telephony script
			 	 */
			 	$('#modifyOkButton').click(function(){
			 		$('#update_button').html("<i class='fa fa-edit'></i> Updating.....");
					$('#modifyOkButton').prop("disabled", true);

					$.ajax({
                        url: "./php/ModifyScript.php",
                        type: 'POST',
                        data: $("#modifyform").serialize(),
                        success: function(data) {
	                        if (data == "success") {
								swal("Updated!", "Script has been successfully updated.", "success");   
	                            window.setTimeout(function(){location.reload()},2000)
	                            $('#update_button').html("<i class='fa fa-check'></i> Update");
	                            $('#modifyOkButton').prop("disabled", false);
							} else {
								sweetAlert("Oops...","Something went wrong! " + data, "error");
								$('#update_button').html("<i class='fa fa-check'></i> Update");
								$('#modifyOkButton').prop("disabled", false);
							}
                        }
                    });		
				});
				
			});

			function addtext() {
				var script_text = document.getElementById('script_text');
    			var script_text_dropdown = document.getElementById('script_text_dropdown');
    			var addtext = "";

    			addtext = "--A--"+script_text_dropdown.value+"--B--";

				script_text.value = script_text.value  + addtext;
			}
		</script>

		<?php print $ui->creamyFooter(); ?>
    </body>
</html>
