
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
        <link href="css/bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="css/font-awesome.min.css" rel="stylesheet" type="text/css" />
        <!-- Ionicons -->
        <link href="css/ionicons.min.css" rel="stylesheet" type="text/css" />
        <!-- bootstrap wysihtml5 - text editor -->
        <link href="css/bootstrap-wysihtml5/bootstrap3-wysihtml5.min.css" rel="stylesheet" type="text/css" />
        <!-- Creamy style -->
        <link href="css/creamycrm.css" rel="stylesheet" type="text/css" />
        <!-- Circle Buttons style -->
        <link href="css/circle-buttons.css" rel="stylesheet" type="text/css" />
        <!-- Wizard Form style -->
        <link href="css/wizard-form.css" rel="stylesheet" type="text/css" />
        <link href="css/style.css" rel="stylesheet" type="text/css" />
        <link rel="stylesheet" href="css/easyWizard.css">
        <!-- DATA TABLES -->
        <link href="css/datatables/dataTables.bootstrap.css" rel="stylesheet" type="text/css" />
		<!-- Bootstrap Player -->
		<link href="css/bootstrap-player.css" rel="stylesheet" type="text/css" />
        <?php print $ui->creamyThemeCSS(); ?>

        <!-- datetime picker --> 
		<link rel="stylesheet" href="theme_dashboard/eonasdan-bootstrap-datetimepicker/build/css/bootstrap-datetimepicker.min.css">

        <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
          <script src="js/html5shiv.js"></script>
          <script src="js/respond.min.js"></script>
        <![endif]-->
        <script src="js/jquery.min.js"></script>
        <script src="js/bootstrap.min.js" type="text/javascript"></script>
        <script src="js/jquery-ui.min.js" type="text/javascript"></script>
        <!-- Bootstrap WYSIHTML5 -->
        <script src="js/plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.all.min.js" type="text/javascript"></script>

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

	<!-- Bootstrap Player -->
	<script src="js/bootstrap-player.js" type="text/javascript"></script>

        <!-- Creamy App -->
        <script src="js/app.min.js" type="text/javascript"></script>

        <!-- =============== APP STYLES ===============-->
			<link rel="stylesheet" href="theme_dashboard/css/app.css" id="maincss">

        <!-- preloader -->
        <link rel="stylesheet" href="css/customizedLoader.css">

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
            <aside class="right-side content-wrapper">
                <!-- Content Header (Page header) -->
                <section class="content-header content-heading">
                    <h1>
                        <?php $lh->translateText("contacts_call_recordings"); ?>
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
                	<div class="row">
						<div class="col-lg-9">
							<div class="form-group mb-xl">
								<input type="text" placeholder="Search Phone, Agent or Last Name" id="search_general" class="form-control mb">
								<div class="clearfix">
									<button type="button" class="pull-left btn btn-default">Search</button>
									<div class="pull-right">
										<label class="checkbox-inline c-checkbox" for="search_contacts">
											<input id="search_contacts" name="table_filter" type="checkbox" checked>
											<span class="fa fa-check"></span> Contacts
										</label>
										<label class="checkbox-inline c-checkbox" for="search_recordings">
											<input id="search_recordings" name="table_filter" type="checkbox">
											<span class="fa fa-check"></span> Recordings
										</label>
										<label class="checkbox-inline c-checkbox" for="search_tickets">
											<input id="search_tickets" name="table_filter" type="checkbox">
											<span class="fa fa-check"></span> Tickets
										</label>
										<label class="checkbox-inline c-checkbox" for="search_chats">
											<input id="search_chats" name="table_filter" type="checkbox">
											<span class="fa fa-check"></span> Chats
										</label>
									</div>
								</div>
							</div>
		                	<div class="panel panel-default">
								<div class="panel-body">
									
									<!-- Call Recordings panel tab -->
									<legend>Call Recordings</legend>
									

										<!--==== Call Recordings ====-->
										<table class="table table-striped table-bordered table-hover" id="table_callrecordings">
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
			               		</div><!-- /.body -->
		               		</div><!-- /.panel -->
	               		</div><!-- /.col-lg-9 -->
<?php
$campaign = $ui->API_getListAllCampaigns();
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
		                              	<select id="add_filters" multiple="multiple" class="select2-3 form-control" style="width:100%;">
		                                    <option value="filter_campaign">Campaign </option>
		                                    <option value="filter_list">List ID</option>
		                                    <option value="filter_address">Address 	</option>
		                                    <option value="filter_city">City </option>
		                                    <option value="filter_state">State </option>
		                             	</select>
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
							    <div class="form-group">
									<label>Disposition: </label>
									<div class="mb">
										<select name="campaign_filter" class="form-control">
											<?php
											//if($disposition->campaign_id[$i] == $campaign->campaign_id[$i]){
												for($a=0; $a<count($disposition->status); $a++){
											?>
													<option value=""><?php echo $disposition->status[$a].' - '.$disposition->status_name[$a];?></option>
											<?php
												}
											?>	
										</select>
									</div>
								</div>
								<div class="form-group">
									<label>Agent: </label>
									<div class="mb">
										<select name="campaign_filter" class="form-control">
											<?php
												for($i=0; $i < count($campaign->campaign_id);$i++){
													echo "<option value='".$campaign->campaign_id[$i]."'> ".$campaign->campaign_name[$i]." </option>";
												}
											?>			
										</select>
									</div>
								</div>
							<fieldset>
							    <!-- HIDDEN FILTERS -->
							    <div class="campaign_filter_div" style="display:none;">
								    <div class="form-group">
										<label>Campaign: </label>
										<div class="mb">
											<select name="campaign_filter" class="form-control">
												<?php
													for($i=0; $i < count($campaign->campaign_id);$i++){
														echo "<option value='".$campaign->campaign_id[$i]."'> ".$campaign->campaign_name[$i]." </option>";
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
											<select name="list_filter" class="form-control">
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
										<div class="mb">
											<input type="text" class="form-control" id="address_filter" name="address_filter" placeholder="Address" />
										</div>
									</div>
								</div>
								<div class="city_filter_div" style="display:none;">
									<div class="form-group">
										<label>City: </label>
										<div class="mb">
											<input type="text" class="form-control" id="city_filter" name="city_filter" placeholder="City" />
										</div>
									</div>
								</div>
								<div class="state_filter_div" style="display:none;">
									<div class="form-group">
										<label>State: </label>
										<div class="mb">
											<input type="text" class="form-control" id="state_filter" name="state_filter" placeholder="State" />
										</div>
									</div>
								</div>
							</fieldset>

							</form>
						    <!--<button type="button" class="pull-left btn btn-default" id="search_button">Apply</button>-->
	           			</div><!-- ./filters -->
           			</div><!-- /. row -->
                </section><!-- /.content -->
            </aside><!-- /.right-side -->
        </div><!-- ./wrapper -->
	
	<!-- FIXED ACTION BUTTON -->
	<div class="action-button-circle" data-toggle="modal" data-target="#list-modal">
		<?php print $ui->getCircleButton("list_and_call_recording", "plus"); ?>
	</div>

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
		<!-- Forms and actions -->
		<script src="js/jquery.validate.min.js" type="text/javascript"></script>
		<script src="js/easyWizard.js" type="text/javascript"></script> 
		<!-- CHOSEN-->
   		<script src="theme_dashboard/chosen_v1.2.0/chosen.jquery.min.js"></script>
   		<!-- SELECT2-->
   		<script src="theme_dashboard/select2/dist/js/select2.js"></script>

		<script type="text/javascript">

			$(document).ready(function() {

				$('#table_callrecordings').dataTable();

				//initialize single selecting
				$('#select2-1').select2({
			        theme: 'bootstrap'
			    });
			    //initialize multiple selecting
				$('.select2-3').select2({
			        theme: 'bootstrap'
			    });

				// the selector will match all input controls of type :checkbox
				// and attach a click event handler 
				$("input:checkbox").on('click', function() {
				  // in the handler, 'this' refers to the box clicked on
				  var $box = $(this);
				  if ($box.is(":checked")) {
				    // the name of the box is retrieved using the .attr() method
				    // as it is assumed and expected to be immutable
				    var group = "input:checkbox[name='" + $box.attr("name") + "']";
				    // the checked state of the group/box on the other hand will change
				    // and the current value is retrieved using .prop() method
				    $(group).prop("checked", false);
				    $box.prop("checked", true);
				  } else {
				    $box.prop("checked", false);
				  }
				});

				$("#add_filters").change(function(){
			        $(this).find("option:selected").each(function(){
			            if($(this).attr("value")=="filter_campaign"){
			               /* $(".list_filter_div").hide();
			                $(".address_filter_div").hide();
			                $(".city_filter_div").hide();
			                $(".state_filter_div").hide();*/

			                $(".campaign_filter_div").show();
			            }
			            else if($(this).attr("value")=="filter_list"){
			               /* $(".campaign_filter_div").hide();
			                $(".address_filter_div").hide();
			                $(".city_filter_div").hide();
			                $(".state_filter_div").hide();*/

			                $(".list_filter_div").show();
			            }
			            else if($(this).attr("value")=="filter_address"){
			              /*  $(".campaign_filter_div").hide();
			                $(".list_filter_div").hide();
			                $(".city_filter_div").hide();
			                $(".state_filter_div").hide();*/

			                $(".address_filter_div").show();
			            }
			            else if($(this).attr("value")=="filter_city"){
			               /* $(".campaign_filter_div").hide();
			                $(".list_filter_div").hide();
			                $(".address_filter_div").hide();
			                $(".state_filter_div").hide();*/

			                $(".city_filter_div").show();
			            }
			            else if($(this).attr("value")=="filter_state"){
			              /*  $(".campaign_filter_div").hide();
			                $(".list_filter_div").hide();
			                $(".address_filter_div").hide();
			                $(".city_filter_div").hide();*/

			                $(".state_filter_div").show();
			            }
			            else{
			                $(".box").hide();
			            }
			        });
			    }).change();

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
