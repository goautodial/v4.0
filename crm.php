<?php

	###################################################
	### Name: crm.php 								###
	### Functions: Display, Search and Filter  		###
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
        <title><?php $lh->translateText("crm"); ?></title>
        <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>

   		<!-- Call for standardized css -->
   		<?php print $ui->standardizedThemeCSS();?>

        <!-- DATA TABLES -->
        <link href="css/datatables/dataTables.bootstrap.css" rel="stylesheet" type="text/css" />
        <?php print $ui->creamyThemeCSS(); ?>

        <!-- Datetime picker -->
		<link rel="stylesheet" href="theme_dashboard/eonasdan-bootstrap-datetimepicker/build/css/bootstrap-datetimepicker.min.css">

        <!-- Data Tables -->
        <script src="js/plugins/datatables/jquery.dataTables.js" type="text/javascript"></script>
        <script src="js/plugins/datatables/dataTables.bootstrap.js" type="text/javascript"></script>

        <!-- Date Picker -->	
        <script type="text/javascript" src="theme_dashboard/eonasdan-bootstrap-datetimepicker/build/js/moment.js"></script>
		<script type="text/javascript" src="theme_dashboard/eonasdan-bootstrap-datetimepicker/build/js/bootstrap-datetimepicker.min.js"></script>

		<!-- CHOSEN-->
   		<link rel="stylesheet" href="theme_dashboard/chosen_v1.2.0/chosen.min.css">
   		<!-- SELECT2-->
   		<link rel="stylesheet" href="theme_dashboard/select2/dist/css/select2.css">
   		<link rel="stylesheet" href="theme_dashboard/select2-bootstrap-theme/dist/select2-bootstrap.css">

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
			<?php print $ui->getSidebar($user->getUserId(), $user->getUserName(), $user->getUserRole(), $user->getUserAvatar()); ?>

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
$lists = $ui->API_goGetAllLists();
$leads = $ui->API_GetLeads($_SESSION['user']);
?>
                <!-- Main content -->
                <section class="content">
                	<div class="row">
						<div class="col-lg-9">
							<div class="form-group mb-xl">
								<div class="has-clear">
									<input type="text" placeholder="Search Phone Number, First or Last Name" id="search" class="form-control mb">
									<span class="form-control-clear fa fa-close form-control-feedback"></span>
								</div>
								<div class="clearfix">
									<button type="button" class="pull-left btn btn-default" id="search_button"> Search</button>
									<div class="pull-right">
										<label class="checkbox-inline c-checkbox" for="search_customers">
											<input id="search_customers" name="search_customers" type="checkbox">
											<span class="fa fa-check"></span> Customers
										</label>
										<label class="checkbox-inline c-checkbox" for="search_contacts">
											<input id="search_contacts" name="table_filter" type="checkbox" checked>
											<span class="fa fa-check"></span> Contacts
										</label>
									</div>
								</div>
							</div>
		                	<div class="panel panel-default">
								<div class="panel-body">
									<div class="contacts_div">
									<!-- Contacts panel tab -->
									<legend>Contacts</legend>

										<!--==== Contacts ====-->
										<table class="table table-striped table-bordered table-hover" id="table_contacts">
										   <thead>
											  <tr>
												 <th nowrap>Lead ID</th>
												 <th nowrap class='hide-on-low'>Full Name</th>
												 <th nowrap class='hide-on-low'>Phone Number</th>
												 <th nowrap class='hide-on-medium hide-on-low'>Status</th>
												 <th nowrap>Action</th>
											  </tr>
										   </thead>
										   <tbody>
											   	<?php
											   		for($i=0;$i<=count($leads->list_id);$i++){

												   		if($leads->phone_number[$i] != ""){

														$action_lead = $ui->ActionMenuForContacts($leads->lead_id[$i]);

											   	?>
														<tr>
															<td nowrap><a class="edit-contact" data-id="<?php echo $leads->lead_id[$i];?>"><?php echo $leads->lead_id[$i];?></a></td>
															<td nowrap class='hide-on-low'><?php echo $leads->first_name[$i].' '.$leads->middle_initial[$i].' '.$leads->last_name[$i];?></td>
															<td nowrap class='hide-on-low'><?php echo $leads->phone_number[$i];?></td>
															<td nowrap class='hide-on-medium hide-on-low'><?php echo $leads->status[$i];?></td>
															<td nowrap><?php echo $action_lead;?></td>
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
$lists = $ui->API_goGetAllLists();
$agents = $ui->API_goGetAllUserLists();
$disposition = $ui->API_getAllDispositions();
?>
	               		<div class="col-lg-3">
	           				<h3 class="m0 pb-lg">Filters</h3>
	           				<form id="search_form">

		                        <div class="form-group">
		                           <label>Add Filters:</label>
		                           <div class="mb">
		                           		<div class="add_contact_filters">
			                              	<select multiple="multiple" class="select2-3 form-control add_filters1" style="width:100%;">
			                                    <option value="filter_disposition" class="contacts_filters">Disposition</option>
			                                    <option value="filter_list" class="contacts_filters">List ID</option>
			                                    <option value="filter_address" class="contacts_filters">Address </option>
			                                    <option value="filter_city" class="contacts_filters">City </option>
			                                    <option value="filter_state" class="contacts_filters">State </option>
			                             	</select>
			                            </div>
		                           </div>
		                        </div>


		                    <!-- CONTACT FILTERS -->
		                    <div class="all_contact_filters">
		                    	<div class="disposition_filter_div" style="display:none;">
								    <div class="form-group">
										<label>Disposition: </label>
										<div class="mb">
											<select name="disposition_filter" id="disposition_filter" class="form-control select2-3" style="width:100%;">
													<option value="">- - - NO DISPOSITION SELECTED - - -</option>
												<?php
												//if($disposition->campaign_id[$i] == $campaign->campaign_id[$i]){
													for($a=0; $a<count($disposition->status); $a++){
												?>
														<option value="<?php echo $disposition->status[$a];?>"><?php echo $disposition->status[$a].' - '.$disposition->status_name[$a];?></option>
												<?php
													}
												?>
											</select>
										</div>
									</div>
								</div>
		                        <div class="list_filter_div" style="display:none;">
									<div class="form-group">
										<label>List ID: </label>
										<div class="mb">
											<select name="list_filter" id="list_filter" class="form-control">
													<option value="">- - - NO LIST SELECTED - - -</option>
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
										<label>Address: </label>
										<div class="mb has-clear">
											<input type="text" class="form-control" id="address_filter" name="address_filter" placeholder="Address" />
											<span class="form-control-clear fa fa-close form-control-feedback"></span>
										</div>
									</div>
								</div>
								<div class="city_filter_div" style="display:none;">
									<div class="form-group has-clear">
										<label>City: </label>
										<div class="mb has-clear">
											<input type="text" class="form-control" id="city_filter" name="city_filter" placeholder="City" />
											<span class="form-control-clear fa fa-close form-control-feedback"></span>
										</div>
									</div>
								</div>
								<div class="state_filter_div" style="display:none;">
									<div class="form-group has-clear">
										<label>State: </label>
										<div class="mb has-clear">
											<input type="text" class="form-control" id="state_filter" name="state_filter" placeholder="State" />
											<span class="form-control-clear fa fa-close form-control-feedback"></span>
										</div>
									</div>
								</div>

	               				<div class="form-group">
		               				<label>Start Date:</label>
						            <div class="form-group">
						                <div class='input-group date' id='datetimepicker1'>
						                    <input type='text' class="form-control" id="start_contact_filterdate" placeholder="<?php echo date("m/d/Y H:i:s ");?>"/>
						                    <span class="input-group-addon">
						                        <!-- <span class="glyphicon glyphicon-calendar"></span>-->
												<span class="fa fa-calendar"></span>
						                    </span>
						                </div>
						            </div>
						        </div>
						        <div class="form-group">
						            <label>End Date:</label>
						            <div class="form-group">
						                <div class='input-group date' id='datetimepicker2'>
						                    <input type='text' class="form-control" id="end_contact_filterdate" placeholder="<?php echo date("m/d/Y H:i:s");?>" value="<?php echo date("m/d/Y H:i:s");?>"/>
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
   		<script src="theme_dashboard/chosen_v1.2.0/chosen.jquery.min.js"></script>
   		<!-- SELECT2-->
   		<script src="theme_dashboard/select2/dist/js/select2.js"></script>

		<script type="text/javascript">

			$(document).ready(function() {

				$('body').on('keypress', '#search', function(args) {
				    if (args.keyCode == 13) {
				        $("#search_button").click();
				        return false;
				    }
				});

				// initialization of datatables
				var init_contacts_table = $('#table_contacts').DataTable({
                	"bDestroy" : true
                });

				// initialize single selecting
				$('.select2-1').select2({
			        theme: 'bootstrap'
			    });
			    // initialize multiple selecting
				$('.select2-3').select2({
			        theme: 'bootstrap'
			    });

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

				/****
				** Change between Contacts and Recordings
				****/
					// shows contacts datatable if Contact tickbox is checked
					$(document).on('change','#search_contacts, #search_customers',function() {
						//$("#search_contacts").prop("disabled", true);
		            	//$("#search_customers").prop("disabled", false);
						if ($(this).prop('id') == 'search_contacts') {
							if ($(this).is(":checked")) {
								$("#search_customers").prop('checked', false);
							}
						}
						
						if ($(this).prop('id') == 'search_customers') {
							if ($(this).is(":checked")) {
								$("#search_contacts").prop('checked', false);
							}
						}

		//				if($('#search_contacts').is(":checked")){
		//					$(".contacts_div").show(); // show contact table
		//					//$(".callrecordings_div").hide(); // hide table
		//
		//					$(".all_contact_filters").show(); // show filters
		//					$(".add_contact_filters").show(); // disable add filter
		//					//$(".all_callrecording_filters").hide(); // hide filters
		//					//$(".add_callrecording_filters").hide(); // disable add filter
		//
		//            	}else{
		//            		$(".contacts_div").hide();
		//            		$(".all_contact_filters").hide();
		//            		$(".add_contact_filters").hide(); // disable add filter
		//            	}
					});

				/***
				** Add Filters
				***/
					// add filters
					$(".add_filters1").change(function(){

						$(".campaign_filter_div").fadeIn("slow")[ $.inArray('filter_campaign', $(this).val()) >= 0 ? 'show' : 'hide' ]();

						$(".list_filter_div").fadeIn("slow")[ $.inArray('filter_list', $(this).val()) >= 0 ? 'show' : 'hide' ]();

						$(".disposition_filter_div").fadeIn("slow")[ $.inArray('filter_disposition', $(this).val()) >= 0 ? 'show' : 'hide' ]();

						$(".address_filter_div").fadeIn("slow")[ $.inArray('filter_address', $(this).val()) >= 0 ? 'show' : 'hide' ]();

						$(".city_filter_div").fadeIn("slow")[ $.inArray('filter_city', $(this).val()) >= 0 ? 'show' : 'hide' ]();

						$(".state_filter_div").fadeIn("slow")[ $.inArray('filter_state', $(this).val()) >= 0 ? 'show' : 'hide' ]();

				    }).change();

				/****
				** Contact filters
				****/

					// ----- Disposition
						$('#disposition_filter').on('change', function() {
		            		var disposition_filter_val = $('#disposition_filter').val();
		            		var list_filter_val = $('#list_filter').val();
		            		var address_filter_val = $("#address_filter").val();
		            		var city_filter_val = $("#city_filter").val();
		            		var state_filter_val = $("#state_filter").val();

		            		$.ajax({
							    url: "filter_contacts.php",
							    type: 'POST',
							    data: {
							    	search_contacts : $('#search').val(),
							    	disposition : disposition_filter_val,
							    	list : list_filter_val,
							    	address : address_filter_val,
							    	city : city_filter_val,
							    	state : state_filter_val
							    },
								success: function(data) {
									$('#search_button').text("Search");
	                				$('#search_button').attr("disabled", false);
									console.log(data);

									if(data !== ""){
										$('#table_contacts').html(data);
										$('#table_contacts').DataTable({
						                	"bDestroy" : true
						                });
									}else{
										init_contacts_table.fnClearTable();
									}
								}
							});

						});

					// ----- List ID
						$('#list_filter').on('change', function() {
		            		var disposition_filter_val = $('#disposition_filter').val();
		            		var list_filter_val = $('#list_filter').val();
		            		var address_filter_val = $("#address_filter").val();
		            		var city_filter_val = $("#city_filter").val();
		            		var state_filter_val = $("#state_filter").val();

		            		$.ajax({
							    url: "filter_contacts.php",
							    type: 'POST',
							    data: {
							    	search_contacts : $('#search').val(),
							    	disposition : disposition_filter_val,
							    	list : list_filter_val,
							    	address : address_filter_val,
							    	city : city_filter_val,
							    	state : state_filter_val
							    },
								success: function(data) {
									$('#search_button').text("Search");
	                				$('#search_button').attr("disabled", false);
									console.log(data);

									if(data !== ""){

										$('#table_contacts').html(data);
										$('#table_contacts').DataTable({
						                	"bDestroy" : true
						                });
									}else{
										init_contacts_table.fnClearTable();
									}
								}
							});

						});

					// ----- Address
						$("#address_filter").keyup(function() {
							clearTimeout($.data(this, 'timer'));
							var wait = setTimeout(address_filter_ajax, 3000);
							$(this).data('timer', wait);
						});

						function address_filter_ajax() {
						    var disposition_filter_val = $('#disposition_filter').val();
		            		var list_filter_val = $('#list_filter').val();
		            		var address_filter_val = $("#address_filter").val();
		            		var city_filter_val = $("#city_filter").val();
		            		var state_filter_val = $("#state_filter").val();

						    $.ajax({
							    url: "filter_contacts.php",
							    type: 'POST',
							    data: {
							    	search_contacts : $('#search').val(),
							    	disposition : disposition_filter_val,
							    	list : list_filter_val,
							    	address : address_filter_val,
							    	city : city_filter_val,
							    	state : state_filter_val
							    },
								success: function(data) {
									$('#search_button').text("Search");
	                				$('#search_button').attr("disabled", false);
									console.log(data);

									if(data !== ""){
										$('#table_contacts').html(data);
										$('#table_contacts').DataTable({
						                	"bDestroy" : true
						                });
									}else{
										init_contacts_table.fnClearTable();
									}
								}
							});
						}

					// ----- City
						$("#city_filter").keyup(function() {
							clearTimeout($.data(this, 'timer'));
							var wait = setTimeout(city_filter_ajax, 3000);
							$(this).data('timer', wait);
						});

						function city_filter_ajax() {
						    var disposition_filter_val = $('#disposition_filter').val();
		            		var list_filter_val = $('#list_filter').val();
		            		var address_filter_val = $("#address_filter").val();
		            		var city_filter_val = $("#city_filter").val();
		            		var state_filter_val = $("#state_filter").val();

						    $.ajax({
							    url: "filter_contacts.php",
							    type: 'POST',
							    data: {
							    	search_contacts : $('#search').val(),
							    	disposition : disposition_filter_val,
							    	list : list_filter_val,
							    	address : address_filter_val,
							    	city : city_filter_val,
							    	state : state_filter_val
							    },
								success: function(data) {
									$('#search_button').text("Search");
	                				$('#search_button').attr("disabled", false);
									console.log(data);

									if(data !== ""){
										$('#table_contacts').html(data);
										$('#table_contacts').DataTable({
						                	"bDestroy" : true
						                });
									}else{
										init_contacts_table.fnClearTable();
									}
								}
							});
						}

					// ----- State
						$("#state_filter").keyup(function() {
							clearTimeout($.data(this, 'timer'));
							var wait = setTimeout(state_filter_ajax, 3000);
							$(this).data('timer', wait);
						});

						function state_filter_ajax() {
						    var disposition_filter_val = $('#disposition_filter').val();
		            		var list_filter_val = $('#list_filter').val();
		            		var address_filter_val = $("#address_filter").val();
		            		var city_filter_val = $("#city_filter").val();
		            		var state_filter_val = $("#state_filter").val();

						    $.ajax({
							    url: "filter_contacts.php",
							    type: 'POST',
							    data: {
							    	search_contacts : $('#search').val(),
							    	disposition : disposition_filter_val,
							    	list : list_filter_val,
							    	address : address_filter_val,
							    	city : city_filter_val,
							    	state : state_filter_val
							    },
								success: function(data) {
									$('#search_button').text("Search");
	                				$('#search_button').attr("disabled", false);
									console.log(data);

									if(data !== ""){
										$('#table_contacts').html(data);
										$('#table_contacts').DataTable({
						                	"bDestroy" : true
						                });
									}else{
										init_contacts_table.fnClearTable();
									}
								}
							});
						}

				/****
				** Call Recording filters
				****/

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

		           
	                /****
	                ** Search function
	                ****/
		                $(document).on('click','#search_button',function() {
		                //init_contacts_table.destroy();

		                	if($('#search').val() === ""){
		                		$('#search_button').attr("disabled", false); 
		                		$('#search_button').text("Searching...");
		                	}else{
			                	$('#search_button').text("Searching...");
			                	$('#search_button').attr("disabled", true);
		                	}

		                	// if contacts is checked
		                	if($("#search_customers").is(":checked") || $('#search_contacts').is(":checked")){
		                		var disposition_filter_val = $('#disposition_filter').val();
			            		var list_filter_val = $('#list_filter').val();
			            		var address_filter_val = $("#address_filter").val();
			            		var city_filter_val = $("#city_filter").val();
			            		var state_filter_val = $("#state_filter").val();
								var search_customers = ($("#search_customers").is(":checked")) ? 1 : 0;

								$.ajax({
								    url: "search.php",
								    type: 'POST',
								    data: {
								    	search_contacts : $('#search').val(),
								    	disposition : disposition_filter_val,
								    	list : list_filter_val,
								    	address : address_filter_val,
								    	city : city_filter_val,
								    	state : state_filter_val,
										search_customers : search_customers
								    },
									success: function(data) {
										$('#search_button').text("Search");
		                				$('#search_button').attr("disabled", false);
										//console.log(data);
										if(data !== ""){

											$('#table_contacts').html(data);
											$('#table_contacts').DataTable({
							                	"bDestroy" : true
							                });
										}else{
											init_contacts_table.fnClearTable();
										}
									}
								});
			            	} else {
								swal({
									title: 'ERROR',
									text: 'Please check either <b>Customer</b> or <b>Contacts</b> checkbox<br>before clicking the <b>Search</b> button.',
									type: 'error',
									html: true
								});
								//$("#search_contacts").prop("checked", true);
								$('#search_button').text("Search");
								$('#search_button').attr("disabled", false);
							}

						});

				/*****
				*** Edit functions
				*****/
				$(document).on('click','.edit-contact',function() {
					var url = './editcontacts.php';
					var id = $(this).attr('data-id');
					//alert(extenid);
					var form = $('<form action="' + url + '" method="post"><input type="hidden" name="modifyid" value="'+id+'" /></form>');
					//$('body').append(form);  // This line is not necessary
					$(form).submit();
				});

				/*****
				*** Delete functions
				*****/
				$(document).on('click','.delete-contact', function() {
						var id = $(this).attr('data-id');
						swal({
							title: "Are you sure?",
							text: "This action cannot be undone.",
							type: "warning",
							showCancelButton: true,
							confirmButtonColor: "#DD6B55",
							confirmButtonText: "Yes, delete this contact!",
							cancelButtonText: "No, cancel please!",
							closeOnConfirm: false,
							closeOnCancel: false
							},
							function(isConfirm){
								if (isConfirm) {
										$.ajax({
											url: "./php/DeleteContact.php",
												type: 'POST',
												data: {
														leadid:id,
												},
												success: function(data) {
												console.log(data);
													if(data == "success"){
														swal({
																title: "Success",
																text: "Contact Successfully Deleted!",
																type: "success"
															},
															function(){
																window.location.href = 'contactsandcallrecordings.php';
															}
														);
													}else{
															sweetAlert("Oops...", "Something went wrong! "+data, "error");
															window.setTimeout(function(){$('#delete_notification_modal').modal('hide');}, 3000);
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
		</script>

		<?php print $ui->creamyFooter();?>
    </body>
</html>
