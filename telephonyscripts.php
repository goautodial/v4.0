<?php	

	###################################################
	### Name: telephonyscripts.php 					###
	### Functions: Manage Scripts 				 	###
	### Copyright: GOAutoDial Ltd. (c) 2011-2016	###
	### Version: 4.0 								###
	### Written by: Alexander Jim H. Abenoja		###
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
        <title>Scripts</title>
        <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
        
        <!-- Call for standardized css -->
        <?php print $ui->standardizedThemeCSS();?>

    	<!-- Wizard Form style -->
		<link href="css/style.css" rel="stylesheet" type="text/css" />
    	<link rel="stylesheet" href="css/easyWizard.css">

        <?php print $ui->creamyThemeCSS(); ?>

        <!-- DATA TABLES -->
        <link href="css/datatables/dataTables.bootstrap.css" rel="stylesheet" type="text/css" />

		<!-- Data Tables -->
        <script src="js/plugins/datatables/jquery.dataTables.js" type="text/javascript"></script>
        <script src="js/plugins/datatables/dataTables.bootstrap.js" type="text/javascript"></script>

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
                        <small><?php $lh->translateText("scripts_management"); ?></small>
                    </h1>
                    <ol class="breadcrumb">
                        <li><a href="./index.php"><i class="fa fa-phone"></i> <?php $lh->translateText("home"); ?></a></li>
                        <li><?php $lh->translateText("telephony"); ?></li>
			<li class="active"><?php $lh->translateText("scripts"); ?>
                    </ol>
                </section>

                <!-- Main content -->
                <section class="content">
                <?php if ($user->userHasAdminPermission()) { ?>
                    <div class="panel panel-default">
                        <div class="panel-body table" id="campaign_table">
                            <legend><?php $lh->translateText("scripts"); ?></legend>
							<?php print $ui->getListAllScripts(); ?>
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
		
	</div><!-- ./wrapper -->

	<!-- FIXED ACTION BUTTON -->
	<div class="action-button-circle" data-toggle="modal" data-target="#scripts-modal">
		<?php print $ui->getCircleButton("scripts", "plus"); ?>
	</div>
<?php
	/*
	* APIs for add form
	*/
	$scripts = $ui->API_goGetAllScripts();
?>
	<div class="modal fade" id="scripts-modal" tabindex="-1"aria-labelledby="scripts" >
        <div class="modal-dialog" role="document">
            <div class="modal-content" style="border-radius:5px;">
				
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					<h4 class="modal-title animate-header" id="scripts"><b>Script Wizard » Add New Script</b></h4>
				</div>
				<div class="modal-body wizard-content" style="min-height: 50%; overflow-y:auto; overflow-x:hidden;">
				
				<form method="POST" id="create_form" class="form-horizontal " role="form">
				<!-- STEP 1 -->
					<div class="wizard-step">
					<?php
						$max = count($scripts->script_id);
						$x = 0;
						for($i=0; $i < $max; $i++){
							//echo $max-$x;
							$agent = substr($scripts->script_id[$max-$x], 0, 6);
							if($agent == "script"){
								$get_last = substr($scripts->script_id[$max-$x], -2);
							}else{
								$x = $x+1;
							}
						}
						$script_num = $get_last + 1;

						$num_padded = sprintf("%03d", $script_num);
						
						//$fullname = "Agent ".$num_padded;
						$script_id_for_form = "script".$num_padded;
					?>
						<div class="form-group">
							<label class="col-sm-3 control-label" for="script_id">Script ID:</label>
							<div class="col-sm-9 mb">
								<input type="text" class="form-control" name="script_id" id="script_id" value="<?php echo $script_id_for_form;?>" disabled />
								<input type="hidden" name="script_user" value="<?php echo $user->getUserName();?>">
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-3 control-label" for="script_name">Script Name:</label>
							<div class="col-sm-9 mb">
								<input type="text" class="form-control" name="script_name" id="script_name" placeholder="Script Name" />
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-3 control-label" for="script_comments">Script Comments:</label>
							<div class="col-sm-9 mb">
								<input type="text" class="form-control" name="script_comments" id="script_comments" placeholder="Script Comments" />
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-3 control-label" for="status">Active: </label>
							<div class="col-sm-9 mb">
								<select name="status" class="form-control">
									<option value="Y" selected>Yes</option>
									<option value="N" >No</option>						
								</select>
							</div>
						</div>
						<div class="form-group">
							<label for="script_text" class="col-sm-3 control-label">Script Text</label>
							<div class="col-sm-9 mb">
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
												<textarea rows="3" class="form-control note-editor" id="script_text" name="script_text"></textarea>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>

					</form>
			
					</div> <!-- end of modal body -->

				<div class="modal-footer">
					<!-- The wizard button will be inserted here. -->
					<button type="button" class="btn btn-default wizard-button-exit" data-dismiss="modal" style="display: inline-block;">Cancel</button>
                    <input type="submit" class="btn btn-primary" id="submit_script" value="Submit" style="display: inline-block;">
				</div>
			</div>
		</div>
	</div><!-- end of modal -->

		<?php print $ui->standardizedThemeJS();?>
		<script src="js/easyWizard.js" type="text/javascript"></script> 
	
	<script>
		$(document).ready(function(){
			$('#scripts_table').dataTable();

			$("#scripts-modal").wizard({

			});
			//$('#script-form-modal').modal('show');

			
			$(document).on('click','#submit_script',function() {
				$('#submit_script').val("Loading.....");
				$('#submit_script').prop("disabled", true);
				$('#script_id').prop("disabled", false);
				$.ajax({
				  url: "./php/AddScript.php",
				  type: 'POST',
				  data: $("#create_form").serialize(),
				  //dataType: 'json',
				  success: function(data) {
				  	if (data == "success") {
						swal("Success!", "Script has been successfully added.", "success");   
                        window.setTimeout(function(){location.reload()},2000)
                        $('#submit_script').val("Submit");
                        $('#submit_script').prop("disabled", false);
					} else {
						sweetAlert("Oops...", "Something went wrong! "+data, "error");
						$('#submit_script').val("Submit");
						$('#submit_script').prop("disabled", false);
						$('#script_id').prop("disabled", true);
					}
				  	
				  }
				});
			});
			/**
			  * Edit script details
			 */
			$(document).on('click','.edit_script',function() {
				var url = './edittelephonyscript.php';
				var id = $(this).attr('data-id');
				//alert(extenid);
				var form = $('<form action="' + url + '" method="post"><input type="hidden" name="script_id" value="'+id+'" /></form>');
				//$('body').append(form);  // This line is not necessary
				$(form).submit();
			});

			/**
			 * Delete validation modal
			 */
			 $(document).on('click','.delete_script',function() {
			 	var id = $(this).attr('data-id');
			 	swal({   
	            	title: "Are you sure?",   
	            	text: "This action cannot be undone.",   
	            	type: "warning",   
	            	showCancelButton: true,   
	            	confirmButtonColor: "#DD6B55",   
	            	confirmButtonText: "Yes, delete this script!",   
	            	cancelButtonText: "No, cancel please!",   
	            	closeOnConfirm: false,   
	            	closeOnCancel: false 
	            	}, 
	            	function(isConfirm){   
	            		if (isConfirm) { 
	            			$.ajax({
								url: "./php/DeleteScript.php",
								type: 'POST',
								data: { 
									script_id:id,
								},
								success: function(data) {
								console.log(data);
							  		if(data == 1){
							  			swal("Deleted!", "Script has been successfully deleted.", "success");   
		                                window.setTimeout(function(){location.reload()},1000)
									}else{
										sweetAlert("Oops...", data, "error");
									}
								}
							});
						} else {     
		                	swal("Cancelled", "No action has been done :)", "error");   
		                } 
                	}
                );
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

		<?php print $ui->creamyFooter();?>
    </body>
</html>
