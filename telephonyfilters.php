<?php	
/**
 * @file 		telephonyfilters.php
 * @brief 		Manage filters
 * @copyright 	Copyright (c) 2020 GOautodial Inc. 
 * @author		Christopher P. Lomuntad
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
	
	//proper user redirects
	if($user->getUserRole() != CRM_DEFAULTS_USER_ROLE_ADMIN){
		if($user->getUserRole() == CRM_DEFAULTS_USER_ROLE_AGENT){
			header("location: agent.php");
		}
	}	
	
	$perm = $api->goGetPermissions('filters');	
	$user_groups = $api->API_getAllUserGroups();
?>
<html>
    <head>
        <meta charset="UTF-8">
        <title><?php $lh->translateText('portal_title'); ?> - <?php $lh->translateText("filters"); ?></title>
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
                        <small><?php $lh->translateText("filters_management"); ?></small>
                    </h1>
                    <ol class="breadcrumb">
                        <li><a href="./index.php"><i class="fa fa-phone"></i> <?php $lh->translateText("home"); ?></a></li>
                        <li><?php $lh->translateText("telephony"); ?></li>
			<li class="active"><?php $lh->translateText("filters"); ?>
                    </ol>
                </section>

                <!-- Main content -->
                <section class="content">
                <?php if ($perm->filters_read !== 'N') { ?>
                    <div class="panel panel-default">
                        <div class="panel-body">
                            <legend><?php $lh->translateText("filters"); ?></legend>
							<?php print $ui->getListAllFilters($_SESSION['user'], $perm, $_SESSION['usergroup']); ?>
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
	<div class="action-button-circle<?=($perm->filters_create === 'N' ? ' hidden' : '')?>" data-toggle="modal" data-target="#filters-modal">
		<?php print $ui->getCircleButton("filters", "plus"); ?>
	</div>
<?php
	/*
	* APIs for add form
	*/
	$filters = $api->API_getAllFilters();

?>
	<div class="modal fade" id="filters-modal" tabindex="-1" aria-labelledby="filters">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
				<div class="modal-header">
					<h4 class="modal-title animated bounceInRight" id="filters">
						<i class="fa fa-info-circle" title="<?php $lh->translateText("filter_wizard_description"); ?>"></i> 
						<b><?php $lh->translateText("filter_wizard"); ?> » <?php $lh->translateText("new_filter"); ?></b>
						<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					</h4>
				</div>
				<div class="modal-body">
				
					<form id="create_form" role="form">
						<input type="hidden" name="log_user" value="<?php echo $_SESSION['user']; ?>" />
						<input type="hidden" name="log_group" value="<?php echo $_SESSION['usergroup']; ?>" />
						<div class="row">
							<h4><?php $lh->translateText("filter_details"); ?>
	                           <br>
	                           <small><?php $lh->translateText("fill_form"); ?>.</small>
	                        </h4>
	                        <fieldset>
								<div class="form-group">
									<label class="col-sm-3 control-label" for="filter_id"><?php $lh->translateText("filter_id"); ?></label>
									<div class="col-sm-8 mb">
										<input type="text" class="form-control" name="filter_id" id="filter_id" value="<?php echo ($filters->filter_count);?>" maxlength="15" disabled required />
										<input type="hidden" name="filter_id" value="<?php echo ($filters->filter_count);?>">
										<input type="hidden" name="filter_user" value="<?php echo $user->getUserName();?>">
									</div>
									<div class="col-sm-1">&nbsp;</div>
								</div>
								<div class="form-group">
									<label class="col-sm-3 control-label" for="filter_name"><?php $lh->translateText("filter_name"); ?></label>
									<div class="col-sm-8 mb">
										<input type="text" class="form-control" name="filter_name" id="filter_name" placeholder="<?php $lh->translateText("filter_name"); ?>" maxlength="50" required />
									</div>
									<div class="col-sm-1">&nbsp;</div>
								</div>
								<div class="form-group">
									<label class="col-sm-3 control-label" for="filter_comments"><?php $lh->translateText("filter_comments"); ?></label>
									<div class="col-sm-8 mb">
										<input type="text" class="form-control" name="filter_comments" id="filter_comments" maxlength="255" placeholder="<?php $lh->translateText("filter_comments"); ?>" />
									</div>
									<div class="col-sm-1">&nbsp;</div>
								</div>
								<div class="form-group<?=($_SESSION['usergroup'] !== 'ADMIN' ? ' hidden' : '')?>">
									<label class="col-sm-3 control-label" for="filter_user_group"><?php $lh->translateText("user_group"); ?>: </label>
									<div class="col-sm-8 mb">
										<select name="filter_user_group" class="form-control">
											<option value="" disabled selected> - - - <?php $lh->translateText('Select User Group'); ?> - - -</option>
											<option value="---ALL---" selected> - - - ALL - - -</option>
											<?php
											if ($user_groups->result == 'success') {
												foreach ($user_groups->user_group as $i => $group) {
													$isSelected = '';
													if ($group == $_SESSION['usergroup']) {
														$isSelected = ' selected';
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
									<label class="col-sm-3 control-label" for="filter_user_group"><?php $lh->translateText("filter_sql"); ?>: </label>
									<div class="col-sm-8 mb">
									    <textarea rows="5" class="form-control" id="filter_sql" name="filter_sql"></textarea>
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
	$(document).ready(function(){
		/*******************
		** INITIALIZATIONS
		*******************/

		$('#filters_table').DataTable({
			destroy:true, 
			responsive:true,
			stateSave:true,
			drawCallback:function(settings) {
				var pagination = $(this).closest('.dataTables_wrapper').find('.dataTables_paginate');
				pagination.toggle(this.api().page.info().pages > 1);
			},
			columnDefs:[
				{ width: "12%", targets: 4 },
				{ searchable: false, targets: 4 },
				{ sortable: false, targets: 4 },
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
							url: "./php/AddFilter.php",
							type: 'POST',
							data: $("#create_form").serialize(),
							success: function(data) {
								console.log(data);
								$('#finish').text("<?php $lh->translateText("submit"); ?>");
								$('#finish').attr("disabled", false);
								if(data == 1){
									swal({title: "<?php $lh->translateText("add_filter_success"); ?>",text: "<?php $lh->translateText("add_filter_success_msg"); ?>",type: "success"},function(){window.location.href = 'telephonyfilters.php';});
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
			$(document).on('click','.edit_filter',function() {
				var url = './edittelephonyfilter.php';
				var id = $(this).attr('data-id');
				//alert(extenid);
				var form = $('<form action="' + url + '" method="post"><input type="hidden" name="filter_id" value="'+id+'" /></form>');
				$('body').append(form);  // This line is not necessary
				$(form).submit();
			});

		/*******************
		** DELETE SCRIPT EVENT
		*******************/
			$(document).on('click','.delete_filter',function() {
				var id = $(this).attr('data-id');
				swal({
					title: "<?php $lh->translateText("are_you_sure"); ?>?",
					text: "<?php $lh->translateText("action_cannot_be_undone"); ?>.",
					type: "warning",
					showCancelButton: true,
					confirmButtonColor: "#DD6B55",
					confirmButtonText: "<?php $lh->translateText("confirm_delete_filter"); ?>!",
					cancelButtonText: "<?php $lh->translateText("cancel_please"); ?>!",
					closeOnConfirm: false,
					closeOnCancel: false
					},
					function(isConfirm){
						if (isConfirm) {
							$.ajax({
								url: "./php/DeleteFilter.php",
								type: 'POST',
								data: {
									filter_id: id, 
									log_user: '<?=$_SESSION['user']?>', 
									log_group: '<?=$_SESSION['usergroup']?>' 
								},
								success: function(data) {
								//console.log(data);
									if(data == 1){
										swal({title: "<?php $lh->translateText("delete_filter_success"); ?>",text: "<?php $lh->translateText("delete_filter_success_msg"); ?>",type: "success"},function(){window.location.href = 'telephonyfilters.php';});
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
				$('#filter_id').bind('keypress', function (event) {
				    var regex = new RegExp("^[a-zA-Z0-9]+$");
				    var key = String.fromCharCode(!event.charCode ? event.which : event.charCode);
				    if (!regex.test(key)) {
				       event.preventDefault();
				       return false;
				    }
				});

			// disable special characters on Script Name
				$('#filter_name').bind('keypress', function (event) {
				    var regex = new RegExp("^[a-zA-Z0-9 ]+$");
				    var key = String.fromCharCode(!event.charCode ? event.which : event.charCode);
				    if (!regex.test(key)) {
				       event.preventDefault();
				       return false;
				    }
				});

			// disable special characters on Script Comments
				$('#filter_comments').bind('keypress', function (event) {
				    var regex = new RegExp("^[a-zA-Z0-9 ]+$");
				    var key = String.fromCharCode(!event.charCode ? event.which : event.charCode);
				    if (!regex.test(key)) {
				       event.preventDefault();
				       return false;
				    }
				});
	}); // end of document ready
</script>
		
		<?php print $ui->creamyFooter();?>
    </body>
</html>
