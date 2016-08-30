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
                        <div class="panel-body">
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
            <div class="modal-content">
				
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					<h4 class="modal-title animated bounceInRight" id="scripts">
						<i class="fa fa-info-circle" title="A step by step wizard that allows you to create scripts."></i> 
						<b>Script Wizard » Add New Script</b>
					</h4>
				</div>
				<div class="modal-body">
				
					<form method="POST" id="create_form" role="form">
						<div class="row">
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

							<h4>Script Details
	                           <br>
	                           <small>Fill in the needed details in the form.</small>
	                        </h4>
	                        <fieldset>
								<div class="form-group">
									<label class="col-sm-3 control-label" for="script_id">Script ID</label>
									<div class="col-sm-9 mb">
										<input type="text" class="form-control" name="script_id" id="script_id" value="<?php echo $script_id_for_form;?>" maxlength="15" disabled required />
										<input type="hidden" name="script_id" value="<?php echo $script_id_for_form;?>">
										<input type="hidden" name="script_user" value="<?php echo $user->getUserName();?>">
									</div>
								</div>
								<div class="form-group">
									<label class="col-sm-3 control-label" for="script_name">Script Name</label>
									<div class="col-sm-9 mb">
										<input type="text" class="form-control" name="script_name" id="script_name" placeholder="Script Name" maxlength="50" required />
									</div>
								</div>
								<div class="form-group">
									<label class="col-sm-3 control-label" for="script_comments">Script Comments</label>
									<div class="col-sm-9 mb">
										<input type="text" class="form-control" name="script_comments" id="script_comments" maxlength="255" placeholder="Script Comments" />
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
													<span class="input-group-btn" title="Add a Preset Text to the Script Text">
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
														<textarea rows="3" class="form-control note-editor" id="script_text" name="script_text" required></textarea>
													</div>
												</div>
											</div>
										</div>
									</div>
								</div>
							</fieldset>
						</div>

					</form>
			
				</div> <!-- end of modal body -->
			</div>
		</div>
	</div><!-- end of modal -->

		<?php print $ui->standardizedThemeJS();?>
		<!-- JQUERY STEPS-->
  		<script src="theme_dashboard/js/jquery.steps/build/jquery.steps.js"></script>
	
<script>
	$(document).ready(function(){
		
		/*******************
		** INITIALIZATIONS
		*******************/

			// init data table
				$('#scripts_table').dataTable();

		/*******************
		** INIT WIZARD & ADD EVENT
		*******************/
			var form = $("#create_form"); // init form wizard 

		    form.validate({
		        errorPlacement: function errorPlacement(error, element) { element.after(error); }
		    });
		    form.children("div").steps({
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
				        $(".body:eq(" + newIndex + ") label.error", form).remove();
				        $(".body:eq(" + newIndex + ") .error", form).removeClass("error");
				    }

		            form.validate().settings.ignore = ":disabled,:hidden";
		            return form.valid();
		        },
		        onFinishing: function (event, currentIndex)
		        {
		            form.validate().settings.ignore = ":disabled";
		            return form.valid();
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
								url: "./php/AddScript.php",
								type: 'POST',
								data: $("#create_form").serialize(),
								success: function(data) {
								  // console.log(data);
									  if(data == "success"){
									  		swal({title: "Success",text: "Script Successfully Created!",type: "success"},function(){window.location.href = 'telephonyscripts.php';});

									  		$('#finish').text("Submit");
											$('#finish').attr("disabled", false);
									  }
									  else{
										  sweetAlert("Oops...", "Something went wrong! "+data, "error");

										  $('#finish').text("Submit");
										  $('#finish').attr("disabled", false);
									  }
								}
							});
		        }
		    }); // end of wizard

		/*******************
		** EDIT SCRIPT EVENT
		*******************/
			$(document).on('click','.edit_script',function() {
				var url = './edittelephonyscript.php';
				var id = $(this).attr('data-id');
				//alert(extenid);
				var form = $('<form action="' + url + '" method="post"><input type="hidden" name="script_id" value="'+id+'" /></form>');
				//$('body').append(form);  // This line is not necessary
				$(form).submit();
			});

		/*******************
		** DELETE SCRIPT EVENT
		*******************/
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
							  			swal({title: "Deleted",text: "Script Successfully Deleted!",type: "success"},function(){window.location.href = 'telephonyscripts.php';});
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
		
		/*******************
		** FILTERS
		*******************/

			// disable special characters on Script ID
				$('#script_id').bind('keypress', function (event) {
				    var regex = new RegExp("^[a-zA-Z0-9]+$");
				    var key = String.fromCharCode(!event.charCode ? event.which : event.charCode);
				    if (!regex.test(key)) {
				       event.preventDefault();
				       return false;
				    }
				});

			// disable special characters on Script Name
				$('#script_name').bind('keypress', function (event) {
				    var regex = new RegExp("^[a-zA-Z0-9 ]+$");
				    var key = String.fromCharCode(!event.charCode ? event.which : event.charCode);
				    if (!regex.test(key)) {
				       event.preventDefault();
				       return false;
				    }
				});

			// disable special characters on Script Comments
				$('#script_comments').bind('keypress', function (event) {
				    var regex = new RegExp("^[a-zA-Z0-9 ]+$");
				    var key = String.fromCharCode(!event.charCode ? event.which : event.charCode);
				    if (!regex.test(key)) {
				       event.preventDefault();
				       return false;
				    }
				});
	}); // end of document ready
	
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
