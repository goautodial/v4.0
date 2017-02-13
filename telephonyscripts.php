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
	
	$perm = $ui->goGetPermissions('script', $_SESSION['usergroup']);
?>
<html>
    <head>
        <meta charset="UTF-8">
        <title><?php $lh->translateText('portal_title'); ?> - <?php $lh->translateText("scripts"); ?></title>
        <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
        
        <!-- Call for standardized css -->
        <?php print $ui->standardizedThemeCSS();?>

        <?php print $ui->creamyThemeCSS(); ?>

        <!-- DATA TABLES -->
        <link href="css/datatables/dataTables.bootstrap.css" rel="stylesheet" type="text/css" />

		<!-- Data Tables -->
        <script src="js/plugins/datatables/jquery.dataTables.js" type="text/javascript"></script>
        <script src="js/plugins/datatables/dataTables.bootstrap.js" type="text/javascript"></script>

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
                <?php if ($perm->script_read !== 'N') { ?>
                    <div class="panel panel-default">
                        <div class="panel-body">
                            <legend><?php $lh->translateText("scripts"); ?></legend>
							<?php print $ui->getListAllScripts($user->getUserName(), $perm); ?>
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
			<?php print $ui->getRightSidebar($user->getUserId(), $user->getUserName(), $user->getUserAvatar()); ?>
		
	</div><!-- ./wrapper -->

	<!-- FIXED ACTION BUTTON -->
	<div class="action-button-circle<?=($perm->script_create === 'N' ? ' hidden' : '')?>" data-toggle="modal" data-target="#scripts-modal">
		<?php print $ui->getCircleButton("scripts", "plus"); ?>
	</div>
<?php
	/*
	* APIs for add form
	*/
	$scripts = $ui->API_goGetAllScripts($user->getUserName());

?>
	<div class="modal fade" id="scripts-modal" tabindex="-1"aria-labelledby="scripts">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
				<div class="modal-header">
					<h4 class="modal-title animated bounceInRight" id="scripts">
						<i class="fa fa-info-circle" title="<?php $lh->translateText("script_wizard_description"); ?>"></i> 
						<b><?php $lh->translateText("script_wizard"); ?> » <?php $lh->translateText("new_script"); ?></b>
						<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					</h4>
				</div>
				<div class="modal-body">
				
					<form id="create_form" role="form">
						<input type="hidden" name="log_user" value="<?php echo $_SESSION['user']; ?>" />
						<input type="hidden" name="log_group" value="<?php echo $_SESSION['usergroup']; ?>" />
						<div class="row">
							<h4><?php $lh->translateText("script_details"); ?>
	                           <br>
	                           <small><?php $lh->translateText("fill_form"); ?>.</small>
	                        </h4>
	                        <fieldset>
								<div class="form-group">
									<label class="col-sm-3 control-label" for="script_id"><?php $lh->translateText("script_id"); ?></label>
									<div class="col-sm-8 mb">
										<input type="text" class="form-control" name="script_id" id="script_id" value="<?php print_r($scripts->script_count);?>" maxlength="15" disabled required />
										<input type="hidden" name="script_id" value="<?php print_r($scripts->script_count);?>">
										<input type="hidden" name="script_user" value="<?php echo $user->getUserName();?>">
									</div>
									<div class="col-sm-1">&nbsp;</div>
								</div>
								<div class="form-group">
									<label class="col-sm-3 control-label" for="script_name"><?php $lh->translateText("script_name"); ?></label>
									<div class="col-sm-8 mb">
										<input type="text" class="form-control" name="script_name" id="script_name" placeholder="<?php $lh->translateText("script_name"); ?>" maxlength="50" required />
									</div>
									<div class="col-sm-1">&nbsp;</div>
								</div>
								<div class="form-group">
									<label class="col-sm-3 control-label" for="script_comments"><?php $lh->translateText("script_comment"); ?></label>
									<div class="col-sm-8 mb">
										<input type="text" class="form-control" name="script_comments" id="script_comments" maxlength="255" placeholder="<?php $lh->translateText("script_comment"); ?>" />
									</div>
									<div class="col-sm-1">&nbsp;</div>
								</div>
								<div class="form-group">
									<label class="col-sm-3 control-label" for="active"><?php $lh->translateText("active"); ?>: </label>
									<div class="col-sm-8 mb">
										<select name="active" class="form-control">
											<option value="Y" selected><?php $lh->translateText("go_yes"); ?></option>
											<option value="N" ><?php $lh->translateText("go_no"); ?></option>
										</select>
									</div>
									<div class="col-sm-1">&nbsp;</div>
								</div>
								<div class="form-group">
									<label for="script_text" class="col-sm-3 control-label"><?php $lh->translateText("script_text"); ?></label>
									<div class="col-sm-8 mb">
										<div class="row">
											<div class="col-sm-12 mb">
												<div class="input-group">
													<span class="input-group-btn" title="<?php $lh->translateText("script_text_insert_description"); ?>">
														<button type="button" class="btn btn-default" onClick="addtext();"><?php $lh->translateText("insert"); ?></button>
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
									<div class="col-sm-1">&nbsp;</div>
								</div>
								<div class="form-group">
									<div class="col-sm-2">&nbsp;</div>
									<div class="col-sm-9">
										<div class="panel">
											<div class="panel-body">
												<textarea rows="5" class="form-control note-editor" id="script_text" name="script_text" required></textarea>
											</div>
										</div>
									</div>
									<div class="col-sm-1">&nbsp;</div>
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
				$('#scripts_table').dataTable({
					"aoColumnDefs": [{
						"bSearchable": false,
						"aTargets": [ 5 ]
					},{
						"bSortable": false,
						"aTargets": [ 5 ]
					}]
				});

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
					
					$('#finish').text("<?php $lh->translateText("loading"); ?>");
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
								$('#finish').text("<?php $lh->translateText("submit"); ?>");
								$('#finish').attr("disabled", false);
								if(data == "success"){
									swal({title: "<?php $lh->translateText("add_script_success"); ?>",text: "<?php $lh->translateText("add_script_success_msg"); ?>",type: "success"},function(){window.location.href = 'telephonyscripts.php';});
								}else{
									sweetAlert("<?php $lh->translateText("oups"); ?>", "<?php $lh->translateText("something_went_wrong"); ?> "+data, "error");
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
				$('body').append(form);  // This line is not necessary
				$(form).submit();
			});

		/*******************
		** DELETE SCRIPT EVENT
		*******************/
			$(document).on('click','.delete_script',function() {
				var id = $(this).attr('data-id');
				swal({
					title: "<?php $lh->translateText("are_you_sure"); ?>?",
					text: "<?php $lh->translateText("cannot_undo"); ?>.",
					type: "warning",
					showCancelButton: true,
					confirmButtonColor: "#DD6B55",
					confirmButtonText: "<?php $lh->translateText("confirm_delete_script"); ?>!",
					cancelButtonText: "<?php $lh->translateText("cancel_please"); ?>!",
					closeOnConfirm: false,
					closeOnCancel: false
					},
					function(isConfirm){
						if (isConfirm) {
							$.ajax({
								url: "./php/DeleteScript.php",
								type: 'POST',
								data: {
									script_id: id, 
									log_user: '<?=$_SESSION['user']?>', 
									log_group: '<?=$_SESSION['usergroup']?>' 
								},
								success: function(data) {
								//console.log(data);
									if(data == 1){
										swal({title: "<?php $lh->translateText("delete_script_success"); ?>",text: "<?php $lh->translateText("delete_script_success_msg"); ?>",type: "success"},function(){window.location.href = 'telephonyscripts.php';});
									}else{
										sweetAlert("<?php $lh->translateText("oups"); ?>", data, "error");
									}
								}
							});
						} else {
							swal("<?php $lh->translateText("cancelled"); ?>", "<?php $lh->translateText("cancel_msg"); ?>", "error");
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
		
		<?php print $ui->creamyFooter();?>
    </body>
</html>
