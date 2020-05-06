<?php	
/**
 * @file 		telephonyscripts.php
 * @brief 		Manage scripts
 * @copyright 	Copyright (c) 2018 GOautodial Inc. 
 * @author		Demian Lizandro A. Biscocho
 * @author     	Alexander Jim H. Abenoja
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
**/

	require_once('./php/UIHandler.php');
	require_once('./php/APIHandler.php');
	require_once('./php/CRMDefaults.php');
    require_once('./php/LanguageHandler.php');
    include('./php/Session.php');

	$ui = \creamy\UIHandler::getInstance();
	$api = \creamy\APIHandler::getInstance();
	$lh = \creamy\LanguageHandler::getInstance();
	$user = \creamy\CreamyUser::currentUser();
	
	$perm = $api->goGetPermissions('script');	
	$user_groups = $api->API_getAllUserGroups();
?>
<html>
    <head>
        <meta charset="UTF-8">
        <title><?php $lh->translateText('portal_title'); ?> - <?php $lh->translateText("scripts"); ?></title>
        <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
        
        <?php 
			print $ui->standardizedThemeCSS(); 
			print $ui->creamyThemeCSS();
			print $ui->dataTablesTheme();
		?>
		
        <script src="js/plugins/ckeditor/ckeditor.js" type="text/javascript"></script>
        <script src="js/plugins/ckeditor/styles.js" type="text/javascript"></script>
		
    </head>

     <?php print $ui->creamyBody(); ?>
     <?php 
     	$standard_fields = $api->API_getStandardFields();
     ?>
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
							<?php print $ui->getListAllScripts($_SESSION['user'], $perm); ?>
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
	$scripts = $api->API_getAllScripts();

?>
	<div class="modal fade" id="scripts-modal" tabindex="-1" aria-labelledby="scripts">
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
										<input type="text" class="form-control" name="script_id" id="script_id" value="<?php echo ($scripts->script_count);?>" maxlength="15" disabled required />
										<input type="hidden" name="script_id" value="<?php echo ($scripts->script_count);?>">
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
								<div class="form-group<?=($_SESSION['usergroup'] !== 'ADMIN' ? ' hidden' : '')?>">
									<label class="col-sm-3 control-label" for="script_user_group"><?php $lh->translateText("user_group"); ?>: </label>
									<div class="col-sm-8 mb">
										<select name="script_user_group" class="form-control">
											<option value="" disabled selected> - - - <?php $lh->translateText('Select User Group'); ?> - - -</option>
											<?php
											if (strtoupper($_SESSION['usergroup']) === 'ADMIN') {
											?>
											<option value="---ALL---" selected> - - - ALL - - -</option>
											<?php
											}
											
											if ($user_groups->result == 'success') {
												foreach ($user_groups->user_group as $i => $group) {
													$isSelected = '';
													if ($group == $_SESSION['usergroup']) {
														$isSelected = ' selected';
													}
													if (strtoupper($_SESSION['usergroup']) !== 'ADMIN' && strtoupper($group) !== strtoupper($_SESSION['usergroup'])) {
														continue;
													}
													$group_name = (strlen($user_groups->group_name[$i]) > 0) ? $user_groups->group_name[$i] : $group;
													echo '<option value="'.$group.'"'.$isSelected.'>'.$group_name.'</option>';
												}
											}
											?>
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
														dwadwad<button type="button" class="btn btn-default" onClick="addtext();"><?php $lh->translateText("insert"); ?></button>
													</span>
													<select class="form-control" name="script_text_dropdown" id="script_text_dropdown">
														<option value="--A--fullname--B--">Agent Name (fullname)</option>
														<?php foreach($standard_fields->field_name as $sf) { ?>
															<option value="--A--<?php echo $sf; ?>--B-- "><?php echo $sf; ?></option>
														<?php } ?>
													</select>
												</div>
											</div>
										</div>
									</div>
									<div class="col-sm-1">&nbsp;</div>
								</div>
								<div class="form-group">
									<!-- <div class="col-sm-2">&nbsp;</div> -->
									<div class="col-sm-12">
										<div class="panel">
											<div class="panel-body">
												<!-- <textarea rows="5" class="form-control textarea" id="script_text" name="script_text" required></textarea> -->
												<div class="box-body pad">
									                <textarea rows="5" class="form-control" id="script_text" name="script_text" required></textarea>
									            </div>
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
  		<script src="js/dashboard/js/jquery.steps/build/jquery.steps.js"></script>
	
<script>
	// function wysihtml5(){
	// 	$(".textarea").wysihtml5();
	// }
	$(document).ready(function(){
		$('#scripts-modal').on('shown.bs.modal', function(){
	        // $('.textarea').wysihtml5();
	        CKEDITOR.replace('script_text', 
	        	{
	                toolbar: [
	                    // { name: 'document', items: [ 'Source', '-', 'Save', 'NewPage', 'Preview', 'Print', '-', 'Templates' ] },
						{ name: 'clipboard', items: [ 'Source', 'Cut', 'Copy', 'Paste', 'PasteText', 'PasteFromWord', '-', 'Undo', 'Redo' ] },
						{ name: 'editing', items: [ 'Find', 'Replace', '-', 'SelectAll', '-', 'Scayt' ] },
						{ name: 'forms', items: [ 'Form', 'Checkbox', 'Radio', 'TextField', 'Textarea', 'Select', 'Button', 'ImageButton', 'HiddenField' ] },
						'/',
						{ name: 'basicstyles', items: [ 'Bold', 'Italic', 'Underline', 'Strike', 'Subscript', 'Superscript', '-', 'CopyFormatting', 'RemoveFormat' ] },
						{ name: 'paragraph', items: [ 'NumberedList', 'BulletedList', '-', 'Outdent', 'Indent', '-', 'Blockquote', 'CreateDiv', '-', 'JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock', '-', 'BidiLtr', 'BidiRtl', 'Language' ] },
						{ name: 'links', items: [ 'Link', 'Unlink', 'Anchor' ] },
						{ name: 'insert', items: [ 'Image', 'Flash', 'Table', 'HorizontalRule', 'Smiley', 'SpecialChar', 'PageBreak', 'Iframe' ] },
						'/',
						{ name: 'styles', items: [ 'Styles', 'Format', 'Font', 'FontSize' ] },
						{ name: 'colors', items: [ 'TextColor', 'BGColor' ] },
						{ name: 'tools', items: [ 'Maximize', 'ShowBlocks' ] },
						{ name: 'about', items: [ 'About' ] }
	                ],
	                allowedContent: {
		                script: true,
		                div: true,
		                $1: {
		                    // This will set the default set of elements
		                    elements: CKEDITOR.dtd,
		                    attributes: true,
		                    styles: true,
		                    classes: true
		                }
		            }
            	}
	        );
	    });
		/*******************
		** INITIALIZATIONS
		*******************/

		$('#scripts_table').DataTable({
			destroy:true, 
			responsive:true,
			stateSave:true,
			drawCallback:function(settings) {
				var pagination = $(this).closest('.dataTables_wrapper').find('.dataTables_paginate');
				pagination.toggle(this.api().page.info().pages > 1);
			},
			columnDefs:[
				{ width: "12%", targets: 5 },
				{ searchable: false, targets: 5 },
				{ sortable: false, targets: 5 },
				{ targets: -1, className: "dt-body-right" }
			]
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
							data: $("#create_form").serialize() + '&script_text_value=' + encodeURIComponent(CKEDITOR.instances['script_text'].getData()),
							success: function(data) {
								console.log(data);
								//console.log($("#create_form").serialize() + '&script_text_value=' + encodeURIComponent(CKEDITOR.instances['script_text'].getData()));
								$('#finish').text("<?php $lh->translateText("submit"); ?>");
								$('#finish').attr("disabled", false);
								if(data == 1){
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
					text: "<?php $lh->translateText("action_cannot_be_undone"); ?>.",
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
	CKEDITOR.instances.script_text.insertText( text );
}
</script>
		
		<?php print $ui->creamyFooter();?>
    </body>
</html>
