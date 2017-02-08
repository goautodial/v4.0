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
        <title><?php $lh->translateText("edit_script"); ?></title>
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
						<legend><?php $lh->translateText("modify_script"); ?> : <u><?php echo $script_id;?></u></legend>
                    	
							<form id="modifyform">
								<input type="hidden" name="modifyid" value="<?php echo $script_id;?>">
								<input type="hidden" name="log_user" value="<?php echo $_SESSION['user'];?>">
								<input type="hidden" name="log_group" value="<?php echo $_SESSION['usergroup'];?>">
							
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
											<label for="script_name" class="col-sm-2 control-label"><?php $lh->translateText("script_name"); ?></label>
											<div class="col-sm-10 mb">
												<input type="text" class="form-control" name="script_name" id="script_name" placeholder="Script Name (Required)" value="<?php echo $output->script_name[$i];?>">
											</div>
										</div>
										<div class="form-group mt">
											<label for="script_comments" class="col-sm-2 control-label"><?php $lh->translateText("script_comments"); ?></label>
											<div class="col-sm-10 mb">
												<input type="text" class="form-control" name="script_comments" id="script_comments" placeholder="Script Comments (Optional)" value="<?php echo $output->script_comments[$i];?>">
											</div>
										</div>
										<div class="form-group">
											<label for="status" class="col-sm-2 control-label"><?php $lh->translateText("active"); ?></label>
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
											<label for="script_text" class="col-sm-2 control-label"><?php $lh->translateText("script_text"); ?></label>
											<div class="col-sm-10 mb">
												<div class="row">
													<div class="col-sm-12 mb">
														<div class="input-group">
															<span class="input-group-btn">
																<button type="button" class="btn btn-default" onClick="addtext();">Insert!</button>
															</span>
															<select class="form-control" name="script_text_dropdown" id="script_text_dropdown">
																<option value="--A--fullname--B-- ">Agent Name</option>
																<option value="--A--vendor_lead_code--B-- ">vendor_lead_code</option>
																<option value="--A--source_id--B-- ">source_id</option>
																<option value="--A--list_id--B-- ">list_id</option>
																<option value="--A--gmt_offset_now--B-- ">gmt_offset_now</option>
																<option value="--A--called_since_last_reset--B-- ">called_since_last_reset</option>
																<option value="--A--phone_code--B-- ">phone_code</option>
																<option value="--A--phone_number--B-- ">phone_number</option>
																<option value="--A--title--B-- ">title</option>
																<option value="--A--first_name--B-- ">first_name</option>
																<option value="--A--middle_initial--B-- ">middle_initial</option>
																<option value="--A--last_name--B-- ">last_name</option>
																<option value="--A--address1--B-- ">address1</option>
																<option value="--A--address2--B-- ">address2</option>
																<option value="--A--address3--B-- ">address3</option>
																<option value="--A--city--B-- ">city</option>
																<option value="--A--state--B-- ">state</option>
																<option value="--A--province--B-- ">province</option>
																<option value="--A--postal_code--B-- ">postal_code</option>
																<option value="--A--country_code--B-- ">country_code</option>
																<option value="--A--gender--B-- ">gender</option>
																<option value="--A--date_of_birth--B-- ">date_of_birth</option>
																<option value="--A--alt_phone--B-- ">alt_phone</option>
																<option value="--A--email--B-- ">email</option>
																<option value="--A--security_phrase--B-- ">security_phrase</option>
																<option value="--A--comments--B-- ">comments</option>
																<option value="--A--lead_id--B-- ">lead_id</option>
																<option value="--A--campaign--B-- ">campaign</option>
																<option value="--A--phone_login--B-- ">phone_login</option>
																<option value="--A--group--B-- ">group</option>
																<option value="--A--channel_group--B-- ">channel_group</option>
																<option value="--A--SQLdate--B-- ">SQLdate</option>
																<option value="--A--epoch--B-- ">epoch</option>
																<option value="--A--uniqueid--B-- ">uniqueid</option>
																<option value="--A--customer_zap_channel--B-- ">customer_zap_channel</option>
																<option value="--A--server_ip--B-- ">server_ip</option>
																<option value="--A--SIPexten--B-- ">SIPexten</option>
																<option value="--A--session_id--B-- ">session_id</option>
																<option value="--A--dialed_number--B-- ">dialed_number</option>
																<option value="--A--dialed_label--B-- ">dialed_label</option>
																<option value="--A--rank--B-- ">rank</option>
																<option value="--A--owner--B-- ">owner</option>
																<option value="--A--camp_script--B-- ">camp_script</option>
																<option value="--A--in_script--B-- ">in_script</option>
																<option value="--A--script_width--B-- ">script_width</option>
																<option value="--A--script_height--B-- ">script_height</option>
																<option value="--A--recording_filename--B-- ">recording_filename</option>
																<option value="--A--recording_id--B-- ">recording_id</option>
																<option value="--A--user_custom_one--B-- ">user_custom_one</option>
																<option value="--A--user_custom_two--B-- ">user_custom_two</option>
																<option value="--A--user_custom_three--B-- ">user_custom_three</option>
																<option value="--A--user_custom_four--B-- ">user_custom_four</option>
																<option value="--A--user_custom_five--B-- ">user_custom_five</option>
																<option value="--A--preset_number_a--B-- ">preset_number_a</option>
																<option value="--A--preset_number_b--B-- ">preset_number_b</option>
																<option value="--A--preset_number_c--B-- ">preset_number_c</option>
																<option value="--A--preset_number_d--B-- ">preset_number_d</option>
																<option value="--A--preset_number_e--B-- ">preset_number_e</option>
																<option value="--A--preset_number_f--B-- ">preset_number_f</option>
																<option value="--A--preset_dtmf_a--B-- ">preset_dtmf_a</option>
																<option value="--A--preset_dtmf_b--B-- ">preset_dtmf_b</option>
																<option value="--A--did_id--B-- ">did_id</option>
																<option value="--A--did_extension--B-- ">did_extension</option>
																<option value="--A--did_pattern--B-- ">did_pattern</option>
																<option value="--A--did_description--B-- ">did_description</option>
																<option value="--A--closecallid--B-- ">closecallid</option>
																<option value="--A--xfercallid--B-- ">xfercallid</option>
																<option value="--A--agent_log_id--B-- ">agent_log_id</option>
																<option value="--A--entry_list_id--B-- ">entry_list_id</option>
															</select>
														</div>
													</div>
												</div>
											</div>
										</div>
										<div class="form-group">
											<div class="col-sm-1">&nbsp;</div>
											<div class="col-sm-11">
												<div class="panel">
													<div class="panel-body">
														<textarea rows="14" class="form-control note-editor" id="script_text" name="script_text"><?php echo $output->script_text[$i];?></textarea>
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
			<?php print $ui->getRightSidebar($user->getUserId(), $user->getUserName(), $user->getUserAvatar()); ?>
			
        </div><!-- ./wrapper -->

  		
		<?php print $ui->standardizedThemeJS();?>
		<!-- Modal Dialogs -->
		<?php include_once "./php/ModalPasswordDialogs.php" ?>
		

		<script language="javascript" type="text/javascript">
			$(document).ready(function() {
				
				$(document).on('click', '#cancel', function(){
					sweetAlert("Cancelled", "<?php $lh->translateText("been_done"); ?> :)", "error");
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
				var txtarea = document.getElementById('script_text');
				var text = document.getElementById('script_text_dropdown').value;

				if (!txtarea) { return; }

				var scrollPos = txtarea.scrollTop;
				var strPos = 0;
				var br = ((txtarea.selectionStart || txtarea.selectionStart == '0') ?
					"ff" : (document.selection ? "ie" : false ) );
				if (br == "ie") {
					txtarea.focus();
					var range = document.selection.createRange();
					range.moveStart ('character', -txtarea.value.length);
					strPos = range.text.length;
				} else if (br == "ff") {
					strPos = txtarea.selectionStart;
				}

				var front = (txtarea.value).substring(0, strPos);
				var back = (txtarea.value).substring(strPos, txtarea.value.length);
				txtarea.value = front + text + back;
				strPos = strPos + text.length;
				if (br == "ie") {
					txtarea.focus();
					var ieRange = document.selection.createRange();
					ieRange.moveStart ('character', -txtarea.value.length);
					ieRange.moveStart ('character', strPos);
					ieRange.moveEnd ('character', 0);
					ieRange.select();
				} else if (br == "ff") {
					txtarea.selectionStart = strPos;
					txtarea.selectionEnd = strPos;
					txtarea.focus();
				}

				txtarea.scrollTop = scrollPos;
			}
		</script>

		<?php print $ui->creamyFooter(); ?>
    </body>
</html>
