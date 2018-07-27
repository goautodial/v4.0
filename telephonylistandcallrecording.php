
<?php	
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
        <title>Goautodial Contacts & Call Recordings</title>
        <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
        
        <!-- Call for standardized css -->
        <?php print $ui->standardizedThemeCSS();?>

        <!-- DATA TABLES -->
        <link href="css/datatables/dataTables.bootstrap.css" rel="stylesheet" type="text/css" />
		
		<!-- Bootstrap Player -->
		<link href="css/bootstrap-player.css" rel="stylesheet" type="text/css" />
        <?php print $ui->creamyThemeCSS(); ?>

        <!-- datetime picker --> 
		<link rel="stylesheet" src="js/dashboard/eonasdan-bootstrap-datetimepicker/build/css/bootstrap-datetimepicker.min.css">

        <!-- Data Tables -->
        <script src="js/plugins/datatables/jquery.dataTables.js" type="text/javascript"></script>
        <script src="js/plugins/datatables/dataTables.bootstrap.js" type="text/javascript"></script>

        <!-- Date Picker -->
        <script type="text/javascript" src="js/dashboard/eonasdan-bootstrap-datetimepicker/build/js/moment.js"></script>
		<script type="text/javascript" src="js/dashboard/eonasdan-bootstrap-datetimepicker/build/js/bootstrap-datetimepicker.min.js"></script>

		<!-- Bootstrap Player -->
		<script src="js/bootstrap-player.js" type="text/javascript"></script>
		
		<!-- SELECT2-->
   		<link rel="stylesheet" src="js/dashboard/select2/dist/css/select2.css">
   		<link rel="stylesheet" src="js/dashboard/select2-bootstrap-theme/dist/select2-bootstrap.css">			
		<style type="text/css">
			.select2-container{
				width: 100% !important;
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
            <aside class="right-side">
                <!-- Content Header (Page header) -->
                <section class="content-header">
                    <h1>
                        <?php $lh->translateText("telephony"); ?>
                        <small><?php $lh->translateText("contacts_call_recordings_management"); ?></small>
                    </h1>
                    <ol class="breadcrumb">
                        <li><a href="./index.php"><i class="fa fa-phone"></i> <?php $lh->translateText("home"); ?></a></li>
                       <li><?php $lh->translateText("telephony"); ?></li>
						<li class="active"><?php $lh->translateText("contacts_call_recordings"); ?>
                    </ol>
                </section>
<?php
$lists = $ui->API_goGetAllLists();

$callrecs = $ui->API_getListAllRecordings();

?>
                <!-- Main content -->
                <section class="content">
                	<div class="panel panel-default">
						<div class="panel-body">
							<legend>List & Call Recordings</legend>

							<div class="col-lg-9">
								

					            <div role="tabpanel">
									
									<ul role="tablist" class="nav nav-tabs nav-justified">

									 <!-- Lists panel tabs-->
									 	
										 <li role="presentation" class="ul_list_tab <?php if(!isset($_GET['search'])){echo 'active';}?>">
										
											<a href="#list_tab" aria-controls="list_tab" role="tab" data-toggle="tab" class="bb0">
											   <sup><span class="fa fa-users"></span></sup> Lists</a>
										 </li>
									<!-- Call Recordings panel tab -->
										 <li role="presentation" class="ul_callrecordings_tab <?php if(isset($_GET['search'])){echo 'active';}?>">
											<a href="#callrecordings_tab" aria-controls="callrecordings_tab" role="tab" data-toggle="tab" class="bb0">
											   <sup><span class="fa fa-phone-square"></span></sup> Call Recordings </a>
										 </li>
									  </ul>
									  
									<!-- Tab panes-->
									<div class="tab-content bg-white">

										<!--==== Lists ====-->
										<div id="list_tab" role="tabpanel" class="tab-pane ul_list_tab <?php if(!isset($_GET['search'])){echo 'active';}?>">
											<table class="table table-striped table-bordered table-hover" id="table_lists">
											   <thead>
												  <tr>
													 <th>List ID</th>
													 <th class='hide-on-low hide-on-medium'>Name</th>
													 <th class='hide-on-low hide-on-medium'>Status</th>
													 <th class='hide-on-low'>Last Call Date</th>
													 <th class='hide-on-low hide-on-medium'>Leads Count</th>
													 <th class='hide-on-low hide-on-medium'>Campaign</th>
													 <th>Action</th>
												  </tr>
											   </thead>
											   <tbody>
												   	<?php
												   		for($i=0;$i < count($lists->list_id);$i++){
										
															if($lists->active[$i] == "Y"){
																$lists->active[$i] = "Active";
															}else{
																$lists->active[$i] = "Inactive";
															}

														$action_LISTS = $ui->getUserActionMenuForLists($output->list_id[$i], $output->list_name[$i]);

												   	?>	
														<tr>
															<td><a class='edit-ingroup' data-id="<?php echo $ingroup->group_id[$i];?>"><?php echo $lists->list_id[$i];?></a></td>
															<td class='hide-on-low hide-on-medium'><?php echo $lists->list_name[$i];?></td>
															<td class='hide-on-low hide-on-medium'><?php echo $lists->active[$i];?></td>
															<td class='hide-on-low'><?php echo $lists->list_lastcalldate[$i];?></td>
															<td class='hide-on-low hide-on-medium'><?php echo $lists->tally[$i];?></td>
															<td class='hide-on-low hide-on-medium'><?php echo $lists->campaign_id[$i];?></td>
															<td><?php echo $action_LISTS;?></td>
														</tr>
													<?php
														}
													?>
											   </tbody>
											</table>
										</div>

										<!--==== Call Recordings ====-->
										<div id="callrecordings_tab" class="tab-pane ul_callrecordings_tab <?php if(isset($_GET['search'])){echo 'active';}?>">
											<table class="table table-striped table-bordered table-hover" id="table_callrecs">
											   <thead>
												  <tr>
													 <th>Date</th>
													 <th class='hide-on-medium hide-on-low'>Customer</th>
													 <th class='hide-on-medium hide-on-low'>Phone Number</th>
													 <th class='hide-on-medium hide-on-low'>Agent</th>
													 <th class='hide-on-medium hide-on-low'>Duration</th>
													 <th>Action</th>
												  </tr>
											   </thead>
											   <tbody>
												   	<?php
												   		for($i=0;$i < count($callrecs->list_id);$i++){

													   		$d1 = strtotime($callrecs->start_last_local_call_time[$i]);
															$d2 = strtotime($callrecs->end_last_local_call_time[$i]);

															$diff = abs($d2 - $d1);

															$action_Call = $ui->getUserActionMenuForCallRecording($callrecs->uniqueid[$i], $callrecs->location[$i]);

												   	?>	
															<tr>
																<td><?php echo $callrecs->end_last_local_call_time[$i];?></a></td>
																<td class='hide-on-medium hide-on-low'><?php echo $callrecs->full_name[$i];?></td>
																<td class='hide-on-medium hide-on-low'><?php echo $callrecs->phone_number[$i];?></td>
																<td class='hide-on-medium hide-on-low'><?php echo $callrecs->users[$i];?></td>
																<td><?php echo gmdate('H:i:s', $diff); ?></td>
																<td><?php echo $action_Call;?></td>
															</tr>
													<?php
														}
													?>
											   </tbody>
											</table>
										</div>
	               					</div>
	               				</div>
	               			</div>
	               			<div class="col-lg-3">
	               				<h3 class="m0 pb-lg">Filters</h3>
	               				<form id="search_form">
		               				<div class="form-group mb-xl">
										<input type="text" placeholder="Search Phone, Agent or Customer Last Name" id="search_general" class="form-control mb">
										<div class="clearfix">
											<div class="pull-right">
												<label class="checkbox-inline c-checkbox" for="search_phone">
													<input id="search_phone" type="checkbox" checked>
													<span class="fa fa-check"></span> Phone
												</label>
												<label class="checkbox-inline c-checkbox" for="search_agent">
													<input id="search_agent" type="checkbox" checked>
													<span class="fa fa-check"></span> Agent</label>
												<label class="checkbox-inline c-checkbox" for="search_lastname">
													<input id="search_lastname" type="checkbox" checked>
													<span class="fa fa-check"></span> Customer Lastname
												</label>
											</div>
										</div>
									</div>
		               				<div class="form-group">
			               				<label>Start Date:</label>
							            <div class="form-group">
							                <div class='input-group date' id='datetimepicker1'>
							                    <input type='text' class="form-control" />
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
							                    <input type='text' class="form-control" />
							                    <span class="input-group-addon">
							                        <!-- <span class="glyphicon glyphicon-calendar"></span>-->
							                        <span class="fa fa-calendar"></span>
							                    </span>
							                </div>
							            </div>
								    </div>
								</form>
							    <button type="button" class="pull-left btn btn-default" id="search_button">Search</button>
	               			</div><!-- ./filters -->
               			</div><!-- /.body -->
               		</div><!-- /.panel -->
                </section><!-- /.content -->
            </aside><!-- /.right-side -->
			<?php print $ui->getRightSidebar($user->getUserId(), $user->getUserName(), $user->getUserAvatar()); ?>
        </div><!-- ./wrapper -->
	
	<!-- FIXED ACTION BUTTON -->
	<div class="action-button-circle" data-toggle="modal" data-target="#list-modal">
		<?php print $ui->getCircleButton("list_and_call_recording", "plus"); ?>
	</div>
<?php
	/*
	* APIs for add form
	*/
	$campaign = $ui->API_getListAllCampaigns();
	$next_list = max($lists->list_id);
	$next_list = $next_list + 1;
	$next_listname = "ListID ".$next_list;
	$datenow = date("j-n-Y");
	$next_listdesc = "Auto-generated - ListID - ".$datenow;
?>
	<div class="modal fade" id="list-modal" tabindex="-1"aria-labelledby="list-modal" >
        <div class="modal-dialog" role="document">
            <div class="modal-content" style="border-radius:5px;">
				
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					<h4 class="modal-title animate-header" id="scripts"><b>Call Recordings Wizard >> Add New Call Recordings</b></h4>
				</div>
				<div class="modal-body wizard-content" style="min-height: 50%; overflow-y:auto; overflow-x:hidden;">
				
				<form action="CreateTelephonyUser.php" method="POST" id="create_form" class="form-horizontal " role="form">
					<input type="hidden" name="log_user" value="<?=$_SESSION['user']?>" />
					<input type="hidden" name="log_group" value="<?=$_SESSION['usergroup']?>" />
				<!-- STEP 1 -->
					<div class="wizard-step">
						<div class="form-group">
							<label class="col-sm-3 control-label" for="auto_generate">Auto-generated:</label>
							<div class="col-sm-9">
								<label class="col-sm-2 checkbox-inline c-checkbox" for="auto_generate">
									<input type="checkbox" id="auto_generate" checked>
									<span class="fa fa-check"></span>
								</label>
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-3 control-label" for="list_id">List ID:</label>
							<div class="col-sm-9">
								<input type="text" class="form-control" name="list_id" id="list_id" placeholder="List ID" value="<?php echo $next_list;?>" disabled />
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-3 control-label" for="list_name">List Name:</label>
							<div class="col-sm-9">
								<input type="text" class="form-control" name="list_name" id="list_name" placeholder="List Name" value="<?php echo $next_listname;?>" />
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-3 control-label" for="list_desc">List Description:</label>
							<div class="col-sm-9">
								<input type="text" class="form-control" name="list_desc" id="list_desc" placeholder="List Description"  value="<?php echo $next_listdesc;?>"/>
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-3 control-label" for="status">Campaign: </label>
							<div class="col-sm-9">
								<select name="status" class="form-control select2">
									<?php
										for($i=0; $i < count($campaign->campaign_id);$i++){
											echo "<option value='".$campaign->campaign_id[$i]."'> ".$campaign->campaign_name[$i]." </option>";
										}
									?>			
								</select>
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-3 control-label" for="status">Active: </label>
							<div class="col-sm-9">
								<select name="status" class="form-control select2">
									<option value="Y" selected>Yes</option>
									<option value="N" >No</option>						
								</select>
							</div>
						</div>
	
					</div>
				</form>
		
				</div> <!-- end of modal body -->
				
				<!-- NOTIFICATIONS -->
				<div id="notifications">
					<div class="output-message-success" style="display:none;">
						<div class="alert alert-success alert-dismissible" role="alert">
						  <strong>Success!</strong> New Agent added.
						</div>
					</div>
					<div class="output-message-error" style="display:none;">
						<div class="alert alert-danger alert-dismissible" role="alert">
						  <strong>Error!</strong> Something went wrong please see input data on form or if agent already exists.
						</div>
					</div>
					<div class="output-message-incomplete" style="display:none;">
						<div class="alert alert-danger alert-dismissible" role="alert">
						  Please fill-up all the fields correctly and do not leave any fields with (<strong> * </strong>) blank.
						</div>
					</div>
				</div>

				<div class="modal-footer">
                <!-- The wizard button will be inserted here. -->
                    <button type="button" class="btn btn-default wizard-button-exit" data-dismiss="modal" style="display: inline-block;">Cancel</button>
                    <input type="submit" class="btn btn-primary" id="submit_usergroup" value="Submit" style="display: inline-block;">
                </div>
			</div>
		</div>
	</div><!-- end of modal -->

	<!-- Modal -->
	<div id="call-playback-modal" class="modal fade" role="dialog">
	  <div class="modal-dialog">

	    <!-- Modal content-->
	    <div class="modal-content">
	      <div class="modal-header">
	        <button type="button" class="close" data-dismiss="modal">&times;</button>
	        <h4 class="modal-title"><b>Call Recording Playback</b></h4>
	      </div>
	      <div class="modal-body">
		<div class="audio-player"></div>
	      	<!-- <audio controls>
			<source src="http://www.w3schools.com/html/horse.ogg" type="audio/ogg" />
			<source src="http://www.w3schools.com/html/horse.mp3" type="audio/mpeg" />
			<a href="http://www.w3schools.com/html/horse.mp3">horse</a>
		</audio> -->
	      </div>
	      <div class="modal-footer">
		<a href="" class="btn btn-primary download-audio-file" download>Download File</a>
	        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
	      </div>
	    </div>
	    <!-- End of modal content -->
	  </div>
	</div>
	<!-- End of modal -->

	<?php print $ui->standardizedThemeJS();?>
	<!-- SELECT2-->
	<script src="js/dashboard/select2/dist/js/select2.js"></script>   		
		<script type="text/javascript">

			$(document).ready(function() {
				$('.select2-1').select2({ theme: 'bootstrap' });
				
				$('#table_lists').dataTable();

				$('#table_callrecs').DataTable( {
		            deferRender:    true,
			    	select: true,
			    	stateSave: true
				});

				$('#list-modal').wizard();
				// $('#call-playback-modal').modal('show');

				$('#auto_generate').on('change', function() {
				//  alert( this.value ); // or $(this).val()
					if($('#auto_generate').is(":checked")){
	            		$('#list_id').val("<?php echo $next_list;?>");
	            		$('#list_name').val("<?php echo $next_listname;?>");
	            		$('#list_desc').val("<?php echo $next_listdesc;?>");
	            		$('#list_id').prop("disabled", true);
	            	}
	            	if(!$('#auto_generate').is(":checked")){
	            		$('#list_id').val("");
	            		$('#list_name').val("");
	            		$('#list_desc').val("");
	            		$('#list_id').prop("disabled", false);
	            	}
				});

				$('#datetimepicker1').datetimepicker({
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

                $('#datetimepicker2').datetimepicker({
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

                $(document).on('click','#search_button',function() {
                //$('#search').click(function(){
                $('#search_button').prop("disabled", true);
                	var Asearch_phone = "";
                	var Asearch_agent = "";
                	var Asearch_customer = "";

            		if($('#search_phone').is(":checked")){
	            		Asearch_phone = $('#search_general').val();
	            	}
	            	if($('#search_agent').is(":checked")){
	            		Asearch_agent = $('#search_general').val();

	            	}
	            	if($('#search_lastname').is(":checked")){
	            		Asearch_customer = $('#search_general').val();
	            	}
                	
                	//$('#search_form').serialize(),
				    $.ajax({
					    url: "search.php",
					    type: 'POST',
					    data: {
					    	search : $('#search_general').val(),
					    	search_phone : Asearch_phone,
					    	search_agent : Asearch_agent,
					    	search_customer : Asearch_customer,
					    },
						success: function(data) {
							$('#search_button').prop("disabled", false);
							console.log(data);
							if(data != ""){
								//window.location.replace("telephonylistandcallrecording.php?search="+data);
							}else{
								//window.location.replace("telephonylistandcallrecording.php");
							}
						}
					});
					
				});

				$('.play_audio').click(function(){
					var audioFile = $(this).attr('data-location');
					
					var sourceFile = '<audio class="audio_file" controls>';
					    sourceFile += '<source src="'+ audioFile +'" type="audio/mpeg" download="true"/>';
					    sourceFile += '</audio>';
					    
					$('.download-audio-file').attr('href', audioFile);
					$('.audio-player').html(sourceFile);
					$('#call-playback-modal').modal('show');
					
					var aud = $('.audio_file').get(0);
					aud.play();
				});
				
				$('#call-playback-modal').on('hidden.bs.modal', function () {
					var aud = $('.audio_file').get(0);
					aud.pause();
				});
				
			});
		</script>
    </body>
</html>
