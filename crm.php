<?php
/**
 * @file 		crm.php
 * @brief 		Manage leads and contacts
 * @copyright 	Copyright (c) 202 GOautodial Inc. 
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
	
	//proper user redirects
	if($user->getUserRole() != CRM_DEFAULTS_USER_ROLE_ADMIN){
		if($user->getUserRole() == CRM_DEFAULTS_USER_ROLE_AGENT){
			header("location: agent.php");
		}
	}
	
?>
<html>
    <head>
        <meta charset="UTF-8">
        <title><?php $lh->translateText('portal_title'); ?> - <?php $lh->translateText("crm"); ?></title>
        <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>

        <?php 
			print $ui->standardizedThemeCSS(); 
			print $ui->creamyThemeCSS();
			print $ui->dataTablesTheme();
		?>
		
        <!-- Datetime picker -->
		<link rel="stylesheet" href="js/dashboard/eonasdan-bootstrap-datetimepicker/build/css/bootstrap-datetimepicker.min.css">

        <!-- Date Picker -->	
        <script type="text/javascript" src="js/dashboard/eonasdan-bootstrap-datetimepicker/build/js/moment.js"></script>
		<script type="text/javascript" src="js/dashboard/eonasdan-bootstrap-datetimepicker/build/js/bootstrap-datetimepicker.min.js"></script>

		<!-- CHOSEN-->
   		<link rel="stylesheet" src="js/dashboard/chosen_v1.2.0/chosen.min.css">
		<style>
		/*
		* CUSTOM CSS for disable function
		*/
			.c-checkbox input[type=checkbox]:disabled + span,
			.c-radio input[type=checkbox]:disabled + span,
			.c-checkbox input[type=radio]:disabled + span,
			.c-radio input[type=radio]:disabled + span {
				border-color: none !important;
    			background-color: none !important;
			}
			.c-checkbox input[type=checkbox]:checked + span,
			.c-radio input[type=checkbox]:checked + span,
			.c-checkbox input[type=radio]:checked + span,
			.c-radio input[type=radio]:checked + span
			 {
				border-color: #3f51b5 !important;
    			background-color: #3f51b5 !important;
			}
		</style>
    </head>
    <?php print $ui->creamyBody(); ?>
        <div class="wrapper">
        <!-- header logo: style can be found in header.less -->
		<?php print $ui->creamyHeader($user); ?>
            <!-- Left side column. contains the logo and sidebar -->
			<?php print $ui->getSidebar($user->getUserId(), $user->getUserName(), $user->getUserRole(), $user->getUserAvatar(), $_SESSION['usergroup']); ?>

            <!-- Right side column. Contains the navbar and content of the page -->
            <aside class="right-side content-wrapper">
                <!-- Content Header (Page header) -->
                <section class="content-header content-heading">
                    <h1>
                        <?php $lh->translateText("crm_title"); ?>
                        <small><?php $lh->translateText("crm"); ?></small>
                    </h1>
                    <ol class="breadcrumb">
                        <li><a href="./index.php"><i class="fa fa-phone"></i> <?php $lh->translateText("home"); ?></a></li>
						<li class="active"><?php $lh->translateText("crm_title"); ?>
                    </ol>
                </section>
			<?php
				$leads = $api->API_getLeads('', '', '', '', '', '');
			?>
                <!-- Main content -->
                <section class="content">
                <div class="row">
				<div class="col-lg-9">
				<form id="search_form">
					<div class="form-group mb-xl">
						<div class="has-clear">
							<input type="text" placeholder="<?php $lh->translateText("search_crm"); ?>" id="search" name="search" class="form-control mb">
							<span class="form-control-clear fa fa-close form-control-feedback"></span>
						</div>
						<div class="clearfix">
							<button type="button" class="pull-left btn btn-default" id="search_button"> <?php $lh->translateText("search"); ?></button>
							<div class="pull-right">
								<label class="checkbox-inline c-checkbox" for="search_customers">
									<input id="search_customers" name="search_customers" type="checkbox" value="0">
									<span class="fa fa-check"></span><?php $lh->translateText("customers"); ?> 
								</label>
								<label class="checkbox-inline c-checkbox" for="search_contacts">
									<input id="search_contacts" name="search_contacts" type="checkbox" checked value="1">
									<span class="fa fa-check"></span> <?php $lh->translateText("contacts"); ?>
								</label>
							</div>
						</div>
					</div>
					<div class="panel panel-default">
						<div class="panel-body">
							<div class="contacts_div">
							<!-- Contacts panel tab -->
							<legend><?php $lh->translateText("contacts"); ?></legend>
								<!--==== Contacts ====-->
								<table class="display responsive no-wrap table table-striped table-bordered" width="100%" id="table_contacts">
								   <thead>
									  <tr>
										 <th><?php $lh->translateText("lead_id"); ?></th>
										 <th><?php $lh->translateText("full_name"); ?></th>
										 <th><?php $lh->translateText("phone_number"); ?></th>
										 <th><?php $lh->translateText("status"); ?></th>
										 <th><?php $lh->translateText("action"); ?></th>
									  </tr>
								   </thead>
								   <tbody>
									<?php
										for($i=0;$i<=count($leads->list_id);$i++){
											if($leads->phone_number[$i] != ""){
											$action_lead = $ui->ActionMenuForContacts($leads->lead_id[$i]);
									?>
										<tr>
											<td><a class="edit-contact" data-id="<?php echo $leads->lead_id[$i];?>"><?php echo $leads->lead_id[$i];?></a></td>
											<td><?php echo $leads->first_name[$i].' '.$leads->middle_initial[$i].' '.$leads->last_name[$i];?></td>
											<td><?php echo $leads->phone_number[$i];?></td>
											<td><?php echo $leads->status[$i];?></td>
											<td><?php echo $action_lead;?></td>
										</tr>
									<?php
											}
										}
									?>
								   </tbody>
								</table>
							</div>
						</div><!-- /.body -->
					</div><!-- /.panel -->
				</div><!-- /.col-lg-9 -->
			<?php
				$lists = $api->API_getAllLists();
				$disposition = $api->API_getAllDispositions();
				$dialStatus = $api->API_getAllDialStatuses("ALL", 1);
				//echo "<pre>";
				//var_dump($dialStatus);
			?>
			<div class="col-lg-3">
				<h3 class="m0 pb-lg"><?php $lh->translateText("filters"); ?></h3>
				<!-- <form id="search_form"> -->
					<div class="form-group">
					   <label><?php $lh->translateText("add_filters"); ?></label>
					   <div class="mb">
						<div class="add_contact_filters">
							<select multiple="multiple" class="select2-3 form-control add_filters1" style="width:100%;">
								<option value="filter_disposition" class="contacts_filters"><?php $lh->translateText("filter_disposition"); ?></option>
								<option value="filter_list" class="contacts_filters"><?php $lh->translateText("filter_list_id"); ?></option>
								<option value="filter_address" class="contacts_filters"><?php $lh->translateText("filter_address"); ?> </option>
								<option value="filter_city" class="contacts_filters"><?php $lh->translateText("filter_city"); ?></option>
								<option value="filter_state" class="contacts_filters"><?php $lh->translateText("filter_state"); ?></option>
							</select>
						</div>
					   </div>
					</div>
					
					<!-- CONTACT FILTERS -->
					<div class="all_contact_filters">
					<div class="disposition_filter_div" style="display:none;">
						<div class="form-group">
							<label><?php $lh->translateText("disposition"); ?> </label>
							<div class="mb">
								<select name="disposition_filter" id="disposition_filter" class="form-control select2-3" style="width:100%;">
										<option value="" selected>- - - <?php $lh->translateText("-none-"); ?> - - -</option>
										<optgroup label="System Statuses">
										<?php 
											for($i=0;$i<=count($dialStatus->status->system);$i++) { 
												if (!empty($dialStatus->status->system[$i]) && !in_array($dialStatus->status->system[$i], $dial_statuses)) { 
										?>
												<option value="<?php echo $dialStatus->status->system[$i]?>">
													<?php echo $dialStatus->status->system[$i]." - ".$dialStatus->status_name->system[$i]?>
												</option>
											<?php 
												}
											} 
										?>
										</optgroup>										
										<?php 
											if (count($disposition) > 0) { 
										?>
											<optgroup label="Campaign Statuses">
											<?php 
												for($i=0;$i<count($disposition->status);$i++) { 
											?>
												<option value="<?php echo $disposition->status[$i];?>">
													<?php echo $disposition->status[$i]." - ".$disposition->status_name[$i]?>
												</option>
											<?php 
												} 
											?>
											</optgroup>
										<?php 
											} 
										?>
								</select>
							</div>
						</div>
					</div>
					<div class="list_filter_div" style="display:none;">
					<div class="form-group">
						<label><?php $lh->translateText("list_id"); ?>:</label>
						<div class="mb">
							<select name="list_filter" id="list_filter" class="form-control">
									<option value="">- - - <?php $lh->translateText("-none-"); ?> - - -</option>
								<?php
									for($i=0; $i < count($lists->list_id);$i++){
										echo "<option value='".$lists->list_id[$i]."'> ".$lists->list_name[$i]." </option>";
									}
								?>
							</select>
						</div>
					</div>
				</div>
				<div class="address_filter_div" style="display:none;">
					<div class="form-group">
						<label><?php $lh->translateText("address"); ?>:</label>
						<div class="mb has-clear">
							<input type="text" class="form-control" id="address_filter" name="address_filter" placeholder="<?php $lh->translateText("address"); ?>" />
							<span class="form-control-clear fa fa-close form-control-feedback"></span>
						</div>
					</div>
				</div>
				<div class="city_filter_div" style="display:none;">
					<div class="form-group has-clear">
						<label><?php $lh->translateText("city"); ?>: </label>
						<div class="mb has-clear">
							<input type="text" class="form-control" id="city_filter" name="city_filter" placeholder="<?php $lh->translateText("city"); ?>" />
							<span class="form-control-clear fa fa-close form-control-feedback"></span>
						</div>
					</div>
				</div>
				<div class="state_filter_div" style="display:none;">
					<div class="form-group has-clear">
						<label><?php $lh->translateText("state"); ?>: </label>
						<div class="mb has-clear">
							<input type="text" class="form-control" id="state_filter" name="state_filter" placeholder="<?php $lh->translateText("state"); ?>" />
							<span class="form-control-clear fa fa-close form-control-feedback"></span>
						</div>
					</div>
				</div>
				<div class="form-group">
					<label><?php $lh->translateText("start_date"); ?>:</label>
					<div class="form-group">
						<div class='input-group date' id='datetimepicker1'>
							<input type='text' class="form-control" id="start_contact_filterdate" name="start_contact_filterdate"  placeholder="<?php echo date("m/d/Y H:i:s ");?>"/>
							<span class="input-group-addon">
								<!-- <span class="glyphicon glyphicon-calendar"></span>-->
								<span class="fa fa-calendar"></span>
							</span>
						</div>
					</div>
				</div>
				<div class="form-group">
					<label><?php $lh->translateText("end_date"); ?>:</label>
					<div class="form-group">
						<div class='input-group date' id='datetimepicker2'>
							<input type='text' class="form-control" id="end_contact_filterdate" name="end_contact_filterdate" placeholder="<?php echo date("m/d/Y H:i:s");?>" value="<?php echo date("m/d/Y H:i:s");?>"/>
							<span class="input-group-addon">
								<!-- <span class="glyphicon glyphicon-calendar"></span>-->
								<span class="fa fa-calendar"></span>
							</span>
						</div>
					</div>
				</div>
				</div>
				</form>
				<!--<button type="button" class="pull-left btn btn-default" id="search_button">Apply</button>-->
			</div><!-- ./filters -->
		</div><!-- /. row -->
                </section><!-- /.content -->
            </aside><!-- /.right-side -->
			<?php print $ui->getRightSidebar($user->getUserId(), $user->getUserName(), $user->getUserAvatar()); ?>
        </div><!-- ./wrapper -->

		<?php print $ui->standardizedThemeJS();?>

	<!-- CHOSEN-->
	<script src="js/dashboard/chosen_v1.2.0/chosen.jquery.min.js"></script>
	<script type="text/javascript">
		$(document).ready(function() {				
			$('body').on('keypress', '#search', function(args) {
				if (args.keyCode == 13) {
					$("#search_button").click();
					return false;
				}
			});
			
			// Datatables initialization
			$('#table_contacts').DataTable({
				destroy:true,    
				responsive:true,
				stateSave:true,
				lengthMenu: [[10, 25, 50, 500], [10, 25, 50, 500]],
				iDisplayLength: 10,
				drawCallback:function(settings) {
					var pagination = $(this).closest('.dataTables_wrapper').find('.dataTables_paginate');
					pagination.toggle(this.api().page.info().pages > 1);
				},
				columnDefs:[
					{ width: "20%", targets: 4 },
					{ width: "8%", targets: 0 },
					{ searchable: false, targets: 4 },
					{ sortable: false, targets: 4 },
					{ responsivePriority: 1, targets: 4 },
					{ responsivePriority: 2, targets: 1 },
					{ targets: -1, className: "dt-body-right" }
				]
			});

			$('.select2-3').select2({ theme: 'bootstrap' });
			$.fn.select2.defaults.set( "theme", "bootstrap" );
			
			// limits checkboxes to single selecting
			$("input:checkbox").on('click', function() {
				var $box = $(this);
				if ($box.is(":checked")) {
				var group = "input:checkbox[name='" + $box.attr("name") + "']";
				$(group).prop("checked", false);
				$box.prop("checked", true);
				} else {
				$box.prop("checked", false);
				}
			});

			// shows contacts datatable if Contact tickbox is checked
			$(document).on('change','#search_contacts, #search_customers',function() {
				if ($(this).prop('id') == 'search_contacts') {
					if ($(this).is(":checked")) {
						$("#search_customers").prop('checked', false);
						$("#search_contacts").attr('value', 1);
						$("#search_customers").attr('value', 0);						
					}
				}
				
				if ($(this).prop('id') == 'search_customers') {
					if ($(this).is(":checked")) {
						$("#search_contacts").prop('checked', false);
						$("#search_contacts").attr('value', 0);
						$("#search_customers").attr('value', 1);
					}
				}
			});

			// add filters
			$(".add_filters1").change(function(){
				$(".campaign_filter_div").fadeIn("slow")[ $.inArray('filter_campaign', $(this).val()) >= 0 ? 'show' : 'hide' ]();
				$(".list_filter_div").fadeIn("slow")[ $.inArray('filter_list', $(this).val()) >= 0 ? 'show' : 'hide' ]();
				$(".disposition_filter_div").fadeIn("slow")[ $.inArray('filter_disposition', $(this).val()) >= 0 ? 'show' : 'hide' ]();
				$(".address_filter_div").fadeIn("slow")[ $.inArray('filter_address', $(this).val()) >= 0 ? 'show' : 'hide' ]();
				$(".city_filter_div").fadeIn("slow")[ $.inArray('filter_city', $(this).val()) >= 0 ? 'show' : 'hide' ]();
				$(".state_filter_div").fadeIn("slow")[ $.inArray('filter_state', $(this).val()) >= 0 ? 'show' : 'hide' ]();
			}).change();
				
			// search filters
			$(document).on('change', '#disposition_filter, #list_filter, #address_filter, #city_filter, #state_filter',function() {
				//console.log($(this).prop('id'));				
				if ($(this).prop('id') == 'disposition_filter') {
					var dial_status = $(this).val();
					var campaign_id = "ALL";
					$.ajax({
						url: "./php/GetDialStatuses.php",
						type: 'POST',
						data: {
							campaign_id : campaign_id
						},
						dataType: 'json',
						success: function(data) {
							//console.log(data);
							var optNone = '<option value="">- - - <?php $lh->translateText("-none-"); ?> - - -</option>';
							$('#disposition_filter').html(optNone + data);
							$('#disposition_filter').val(dial_status);
							console.log($('#disposition_filter').val());
							//$('#disposition_filter').val("").trigger("change");
						}
					});	
				}			
				
				$('#address_filter, #city_filter, #state_filter').keyup(function() {
					clearTimeout($.data(this, 'timer'));
					var wait = setTimeout(searchCRM, 3000);
					$(this).data('timer', wait);
				});
				
			});				

			// ---- DATETIME PICKER INITIALIZATION
			$('#datetimepicker1').datetimepicker({ //start date contacts
			icons: {
					time: 'fa fa-clock-o',
					date: 'fa fa-calendar',
					up: 'fa fa-chevron-up',
					down: 'fa fa-chevron-down',
					previous: 'fa fa-chevron-left',
					next: 'fa fa-chevron-right',
					today: 'fa fa-crosshairs',
					clear: 'fa fa-trash'
				}
			});

			$('#datetimepicker2').datetimepicker({ //end date contacts
			icons: {
					time: 'fa fa-clock-o',
					date: 'fa fa-calendar',
					up: 'fa fa-chevron-up',
					down: 'fa fa-chevron-down',
					previous: 'fa fa-chevron-left',
					next: 'fa fa-chevron-right',
					today: 'fa fa-crosshairs',
					clear: 'fa fa-trash'
				}
			});

		           
			// search function
			$(document).on('click','#search_button',function() {
				if ($('#search').val() === "") {
					$('#search_button').attr("disabled", false); 
					$('#search_button').text("<?php print $lh->translationFor("searching"); ?>");
				} else {
					$('#search_button').text("<?php print $lh->translationFor("searching"); ?>");
					$('#search_button').attr("disabled", true);
				}

				// if contacts is checked
				searchCRM();
			});

			// edit contact
			$(document).on('click','.edit-contact',function() {
				var url = './editcontacts.php';
				var id = $(this).attr('data-id');
				//alert(extenid);
				var form = $('<form action="' + url + '" method="post"><input type="hidden" name="modifyid" value="'+id+'" /></form>');
				$('body').append(form);  // This line is not necessary
				$(form).submit();
			});

			// delete contact
			$(document).on('click','.delete-contact', function() {
				var id = $(this).attr('data-id');
				swal({
					title: "<?php $lh->translateText("are_you_sure"); ?>",
					text: "<?php $lh->translateText("action_cannot_be_undone"); ?>",
					type: "warning",
					showCancelButton: true,
					confirmButtonColor: "#DD6B55",
					confirmButtonText: "<?php $lh->translateText("confirm_delete_contact"); ?>",
					cancelButtonText: "<?php $lh->translateText("cancel_please"); ?>",
					closeOnConfirm: false,
					closeOnCancel: false
					},
					function(isConfirm){
						if (isConfirm) {
							$.ajax({
								url: "./php/crm/DeleteContact.php",
									type: 'POST',
									data: {
										leadid: id
									},
									success: function(data) {
									console.log(data);
										if (data == 1) {
											swal({
												title: "<?php $lh->translateText("success"); ?>",
												text: "<?php $lh->translateText("contact_delete_success"); ?>",
												type: "success"
												},
												function(){
													window.location.href = 'crm.php';
												}
											);
										} else {
											sweetAlert("<?php $lh->translateText("oups"); ?>", "<?php $lh->translateText("something_went_wrong"); ?>"+data, "error");
											window.setTimeout(function(){$('#delete_notification_modal').modal('hide');}, 3000);
										}
									}
							});
						} else {
								swal("<?php $lh->translateText("cancelled"); ?>", "<?php $lh->translateText("cancel_message"); ?>", "error");
						}
					}
				);
			});
			
		});
			
		function searchCRM() {
			// need to have at least 1 checked
			if ($("#search_customers").is(":checked") || $('#search_contacts').is(":checked")) {														
				$.ajax({
					url: "./php/crm/API_getLeads.php",
					type: 'POST',
					data: $('#search_form').serialize(),
					dataType: 'json',
					success: function(data) {
						$('#search_button').text("<?php print $lh->translationFor("search"); ?>");
						$('#search_button').attr("disabled", false);
						if (data !== "") {
							var JSONString = data;
							var JSONObject = JSON.parse(JSONString);
							console.log(JSONObject);
							var tableContacts = $('#table_contacts').DataTable({
								destroy: true,
								data: JSONObject,
								responsive: true,
								stateSave: true,
								lengthMenu: [[10, 25, 50, 500], [10, 25, 50, 500]],
				                                iDisplayLength: 10,
								drawCallback: function(settings) {
									var pagination = $(this).closest('.dataTables_wrapper').find('.dataTables_paginate');
									pagination.toggle(this.api().page.info().pages > 1);
								},
								columnDefs: [
									{ width: "20%", targets: 4 },
									{ width: "8%", targets: 0 },
									{ searchable: false, targets: 4 },
									{ sortable: false, targets: 4 },
									{ responsivePriority: 1, targets: 4 },
									{ responsivePriority: 2, targets: 1 },
									{ targets: -1, className: "dt-body-right" }
								]
							});
						} else {
							tableContacts.clear().draw();
						}
					}
				});
			} else {
				swal({
					title: 'ERROR',
					text: '<?php print $lh->translationFor("check_customer_or_contacts"); ?>',
					type: 'error',
					html: true
				});
				//$("#search_contacts").prop("checked", true);
				$('#search_button').text("<?php print $lh->translationFor("search"); ?>");
				$('#search_button').attr("disabled", false);								
			}
		}
			
		</script>
		<?php print $ui->creamyFooter();?>
    </body>
</html>
