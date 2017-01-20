<?php

	###########################################################
	### Name: telephonylist.php                             ###
	### Functions: Manage List and Upload Leads             ###
	### Copyright: GOAutoDial Ltd. (c) 2011-2016            ###
	### Version: 4.0                                        ###
	### Written by: Alexander Abenoja & Noel Umandap        ###
	### License: AGPLv2                                     ###
	###########################################################

	require_once('./php/UIHandler.php');
	require_once('./php/CRMDefaults.php');
    require_once('./php/LanguageHandler.php');
    include('./php/Session.php');

	$ui = \creamy\UIHandler::getInstance();
	$lh = \creamy\LanguageHandler::getInstance();
	$user = \creamy\CreamyUser::currentUser();
	$perm = $ui->goGetPermissions('list,customfields', $_SESSION['usergroup']);
?>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Lists</title>
        <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>

        <!-- Call for standardized css -->
        <?php print $ui->standardizedThemeCSS();?>

        <!-- Wizard Form style -->
        <link href="css/wizard-form.css" rel="stylesheet" type="text/css" />
        <link href="css/style.css" rel="stylesheet" type="text/css" />
        <link rel="stylesheet" href="css/easyWizard.css">

        <!-- DATA TABLES CSS -->
        <link href="css/datatables/dataTables.bootstrap.css" rel="stylesheet" type="text/css" />

        <?php print $ui->creamyThemeCSS(); ?>

        <!-- Datetime picker CSS -->
		<link rel="stylesheet" href="theme_dashboard/eonasdan-bootstrap-datetimepicker/build/css/bootstrap-datetimepicker.min.css">

        <!-- Data Tables JS -->
        <script src="js/plugins/datatables/jquery.dataTables.js" type="text/javascript"></script>
        <script src="js/plugins/datatables/dataTables.bootstrap.js" type="text/javascript"></script>

        <!-- Date Picker JS -->
        <script type="text/javascript" src="theme_dashboard/eonasdan-bootstrap-datetimepicker/build/js/moment.js"></script>
		<script type="text/javascript" src="theme_dashboard/eonasdan-bootstrap-datetimepicker/build/js/bootstrap-datetimepicker.min.js"></script>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-csv/0.71/jquery.csv-0.71.min.js"></script>
		<!-- SELECT2 CSS -->
   		<link rel="stylesheet" href="theme_dashboard/select2/dist/css/select2.css">
   		<link rel="stylesheet" href="theme_dashboard/select2-bootstrap-theme/dist/select2-bootstrap.css">
		<style type="text/css">
			#progress-wrp {
				border: 1px solid #0099CC;
				border-radius: 3px;
				position: relative;
				width: 100%;
				height: 30px;
				background-color: #367fa9;
			}
			
			#progress-wrp .progress-bar {
				border-radius: 3px;
				position: absolute;
				width: 1%;
				height: 100%;
				background-color: #00a65a;
			  /* background-color: #4CAF50; */
			}
			
			#progress-wrp .status {
				top:3px;
				left:50%;
				position:absolute;
				display:inline-block;
				color: white;
				font-style: bold;
				/* color: #000000; */
			}
		</style>

		<style type="text/css">
			.select2-container{
				width: 100% !important;
			}
		</style>
		
        <script type="text/javascript">
			$(window).ready(function() {
				$(".preloader").fadeOut("slow");
			});
			
			// Progress bar function
			function goProgressBar() {
				
				var formData = new FormData($('#upload_form')[0]);
				var progress_bar_id 		= '#progress-wrp'; //ID of an element for response output
				var percent = 0;
				
				var result_output 			= '#output'; //ID of an element for response output
				var my_form_id 				= '#upload_form'; //ID of an element for response output
				var submit_btn  = $(this).find("input[type=button]"); //btnUpload
	
				  
				formData.append('tax_file', $('input[type=file]')[0].files);
				
				$.ajax({
					url : "./php/AddLoadLeads.php",
					type: "POST",
					data : formData,
					contentType: false,
					cache: false,
					processData:false,
					maxChunkSize: 1000000000,
					maxRetries: 100000000,
					retryTimeout: 5000000000,
					xhr: function(){
						//upload Progress
						var xhr = $.ajaxSettings.xhr();
						if (xhr.upload) {
							xhr.upload.addEventListener('progress', function(event) {
								
								var position = event.loaded || event.position;
								var total = event.total;
								if (event.lengthComputable) {
									percent = Math.ceil(position / total * 100);
								}
								
								//update progressbar
								$(progress_bar_id +" .progress-bar").css("width", + percent +"%");
								$(progress_bar_id + " .status").text(percent +"%");
								//$(progress_bar_id + " .status").innerHTML = percent + '%';
								
								if(percent === 100) {
									
									//$('#dStatus').css("display", "block");
									//$('#dStatus').css("color", "#4CAF50");
									//$('#qstatus').text("File Uploaded Successfully. Please wait for the TOTAL of leads uploaded.(Do not refresh the page)");
									//$('#qstatus').text("Data Processing. Please Wait.");
									//sweetAlert("Oops...", "Something went wrong!", "error");
									
									//var uploadMsgTotal = "Total Leads Uploaded: "+res;
					
									swal({
										title: "CSV file upload complete.",
										text: "Data Now Processing. Please Wait. DO NOT refresh the page.",
										type: "info",
										showCancelButton: false,
										closeOnConfirm: false
									  });
									
								}
								
							}, true);
							
						}
						return xhr;
					},
					mimeType:"multipart/form-data"
				}).done(function(res){ //
					
					//$(result_output).html(res); //output response from server
					//submit_btn.val("Upload").prop( "disabled", false); //enable submit button once ajax is done
					//$(my_form_id)[0].reset(); //reset form
					//$('#dStatus').css("display", "block");
					//$('#dStatus').css("color", "#4CAF50");
					//$('#qstatus').text("Total leads uploaded: "+res);
					
					var uploadMsgTotal = "Total Leads Uploaded: "+res;
					
					swal({
							title: "Data Processing Complete!",
							text: uploadMsgTotal,
							type: "success"
						},
						function(){
							location.reload();
							$(".preloader").fadeIn();
						}
					);
					
				});
								
			}
			// End Progress bar function
			
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
                        <small><?php $lh->translateText("list_management"); ?></small>
                    </h1>
                    <ol class="breadcrumb">
                        <li><a href="./index.php"><i class="fa fa-home"></i> <?php $lh->translateText("home"); ?></a></li>
                       <li><?php $lh->translateText("telephony"); ?></li>
						<li class="active"><?php $lh->translateText("lists"); ?>
                    </ol>
                </section>

                <!-- Main content -->
                <section class="content">
		<?php
			if ($perm->list->list_read !== 'N') {
				/****
				** API to get data of tables
				****/
				$lists = $ui->API_goGetAllLists();
		?>
                	<div class="row">
                        <div class="col-lg-<?=($perm->list->list_upload === 'N' ? '12' : '9')?>">
		                	<div class="panel panel-default">
								<div class="panel-body">
									<legend>Lists</legend>
									<button type="button" class="btn btn-primary view-dnc">View DNC</button>
									<table class="table table-striped table-bordered table-hover" id="table_lists">
									   <thead>
										  <tr>
                                                                                         <th style="color: white;">Pic</th>
											 <th class='hide-on-medium hide-on-low'>List ID</th>
											 <th>Name</th>
											 <th class='hide-on-medium hide-on-low'>Status</th>
											 <th class='hide-on-medium hide-on-low'>Leads Count</th>
											 <th class='hide-on-medium hide-on-low'>Campaign</th>
											 <th class='hide-on-medium hide-on-low'>Fields</th>
											 <th class='hide-on-medium hide-on-low'>Action</th>
										  </tr>
									   </thead>
									   <tbody>
										   	<?php
										   		for($i=0;$i < count($lists->list_id);$i++){
												// if no entry in user list

												if($lists->active[$i] == "Y"){
													$lists->active[$i] = "Active";
												}else{
													$lists->active[$i] = "Inactive";
												}

												$action_list = $ui->getUserActionMenuForLists($lists->list_id[$i], $lists->list_name[$i], $perm);
											?>
												<tr>
                                                                                    <td><avatar username='<?php echo $lists->list_name[$i];?>' :size='36'></avatar></td>
								                    <td class='hide-on-low'><strong><a class='edit-list' data-id='<?php echo $lists->list_id[$i];?>'><?php echo $lists->list_id[$i];?></strong></td>
								                    <td><?php echo $lists->list_name[$i];?></td>
													<td class='hide-on-medium hide-on-low'><?php echo $lists->active[$i];?></td>
								                    <td class='hide-on-medium hide-on-low'><?php echo $lists->tally[$i];?></td>
													<td class='hide-on-medium hide-on-low'><?php echo $lists->campaign_id[$i];?></td>
													<td class='hide-on-medium hide-on-low'><?php echo $lists->cf_count[$i];?></td>
								                    <td><?php echo $action_list;?></td>
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
						if ($perm->list->list_upload !== 'N') {
						?>
			            <div class="col-lg-3">
	           				<h3 class="m0 pb-lg">Upload/Import Leads</h3>
	           				<form action="./php/AddLoadLeads.php" method="POST" enctype="multipart/form-data" id="upload_form" name="upload_form">
								<div class="form-group">
									<label>List ID:</label>
									<div class="form-group">
										<!-- <select id="select2-1" class="form-control" name="list_id"> -->
										<select id="list_id" class="form-control select2" name="list_id" required>
										<option value="" selected disabled></option>
											<?php
												for($i=0;$i<count($lists->list_id);$i++){
		                                			echo '<option value="'.$lists->list_id[$i].'">'.$lists->list_id[$i].' - '.$lists->list_name[$i].'</option>';
		                                		}
											?>
										</select>
									</div>
									
									<div class="form-group">
									<label>Duplicate Check:</label>
										<SELECT size="1" NAME="goDupcheck" ID="goDupcheck" TITLE="Duplicate Check - Will check phone numbers on the lead file and cross reference it with all phone numbers on a specific campaign or in all List ID." class="form-control select2">
											<OPTION value="NONE">NO DUPLICATE CHECK</OPTION>
											<OPTION value="DUPLIST">CHECK PHONES IN LIST ID</OPTION>
											<OPTION value="DUPCAMP">CHECK PHONES IN CAMPAIGN-LISTS</OPTION>
										</SELECT>
									</div>
									
								</div>
								<div class="form-group">

									<label>CSV File:</label>
									<div class="form-group" id="dvImportSegments">
										<div class="input-group">
									      <input type="text" class="form-control file-name" name="file_name" placeholder="CSV File" required>
									      <span class="input-group-btn">
									        <button type="button" class="btn browse-btn  btn-primary" type="button">Browse</button>
									      </span>
									    </div>
									    <input type="file" class="file-box hide" name="file_upload" id="txtFileUpload" accept=".csv">

									</div>
									
									<div id="goMappingContainer"></div>
									<div id="goValuesContainer"></div> 

								</div>
								
								<!-- Progress bar -->
								<div class="form-group">
									<div id="progress-wrp">
										<div class="progress-bar"></div >
										<div class="status">0%</div>
									</div>
									<div id="output"><!-- error or success results --></div>
									<br />
									<div>
										<div class="alert alert-success" style="display:none;" id="dStatus"> 
											<div id="qstatus">  </div>
										</div>
									</div>
								</div>
								<!-- End Progress bar -->
						
								<div class="form-group">
										<input type="button" id="btnUpload" name="btnUpload" value="Upload" class="btn btn-primary" onClick="goProgressBar();">
<!--										<div class="col-lg-12" style="margin-top: 10px;">
											<div class="alert alert-success" style="display:none;" id="dStatus"> 
												<div id="qstatus">  </div>
											</div>
										</div>-->
								</div>
								
								<div id="jMapFieldsdiv">
									<span id="jMapFieldsSpan"></span>
								</div>
							</form> 
							<?php
                        		if(isset($_GET['message'])){
                        			echo '<div class="col-lg-12" style="margin-top: 10px;">';
                        			if($_GET['message'] == "success"){
                        				echo '<div class="alert alert-success"> <strong>Succes: </strong>'.$_GET['RetMesg']." leads uploaded</div>";
                        			}else{
                        				echo '<div class="alert alert-success"> <strong>Error: </strong>'.$_GET['RetMesg']."</div>";
                        			}
                        			echo '</div>';
                        		}
								#var_dump($_GET);
                        	?>
							
								
							
	           			</div><!-- ./upload leads -->
						<?php
						}
						?>

                	</div>
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
					<h4 class="modal-title animate-header" id="scripts"><b>List Wizard » Add New List</b></h4>
				</div>
				<div class="modal-body wizard-content" style="min-height: 50%; overflow-y:auto; overflow-x:hidden;">

				<form method="POST" id="create_form" class="form-horizontal " role="form">
				<!-- STEP 1 -->
					<div class="wizard-step">
						<div class="form-group mt">
							<label class="col-sm-3 control-label" for="auto_generate">Auto-generated:</label>
							<div class="col-sm-9 mb">
								<div class="row">
									<label class="col-sm-3 checkbox-inline c-checkbox" for="auto_generate">
										<input type="checkbox" id="auto_generate" checked>
										<span class="fa fa-check"></span>
									</label>
								</div>
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-3 control-label" for="add_list_id">List ID:</label>
							<div class="col-sm-9 mb">
								<input type="number" pattern=".{2,8}" class="form-control" name="add_list_id" id="add_list_id" placeholder="List ID" value="<?php echo $next_list;?>" maxlength="14" disabled />
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-3 control-label" for="list_name">List Name:</label>
							<div class="col-sm-9 mb">
								<input type="text" pattern=".{2,20}" class="form-control" name="list_name" id="list_name" placeholder="List Name" value="<?php echo $next_listname;?>" maxlength="30" />
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-3 control-label" for="list_desc">List Description:</label>
							<div class="col-sm-9 mb">
								<input type="text" class="form-control" name="list_desc" id="list_desc" placeholder="List Description"  value="<?php echo $next_listdesc;?>" maxlength="255" />
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-3 control-label" for="campaign_select">Campaign: </label>
							<div class="col-sm-9 mb">
								<select name="campaign_select" class="form-control">
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
							<div class="col-sm-9 mb">
								<select name="status" class="form-control">
									<option value="Y" selected>Yes</option>
									<option value="N" >No</option>
								</select>
							</div>
						</div>

					</div>
				</form>

				</div> <!-- end of modal body -->

				<div class="modal-footer">
                <!-- The wizard button will be inserted here. -->
                    <button type="button" class="btn btn-default wizard-button-exit" data-dismiss="modal" style="display: inline-block;">Cancel</button>
                    <input type="submit" class="btn btn-primary" id="submit_list" value="Submit" style="display: inline-block;">
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


	<div id="modal_custom_field_copy" class="modal fade" role="dialog">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					<h4 class="modal-title">Copy Custom Fields Wizard</h4>
				</div>
				<div class="modal-body">
					<form id="copy_cf_form" class="form-horizontal" style="margin-top: 10px;">
						<div class="form-group">
							<label class="control-label col-lg-4">List ID to copy Fields from:</label>
							<div class="col-lg-8">
								<input type="hidden" class="form-control list-from" value="" name="list_from">
								<input type="text" class="form-control list-from-label" value="" readonly>
							</div>
						</div>
						<div class="form-group">
							<label class="control-label col-lg-4">Copy fields to another list :</label>
							<div class="col-lg-8">
								<select class="form-control select2" name="list_to">
									<?php for($i=0;$i < count($lists->list_id);$i++){ ?>
										<option value="<?php echo $lists->list_id[$i]; ?>"><?php echo $lists->list_id[$i].' - '.$lists->list_name[$i];?></option>
									<?php } ?>
								</select>
							</div>
						</div>
						<div class="form-group">
							<label class="control-label col-lg-4">Copy Option:</label>
							<div class="col-lg-8">
								<select class="form-control select2" name="copy_option">
									<!-- <option value="APPEND">APPEND</option> -->
									<option value="UPDATE">UPDATE</option>
									<option value="REPLACE">REPLACE</option>
								</select>
							</div>
						</div>
					</form>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
					<button type="button" class="btn btn-success btn-copy-cf" data-dismiss="modal">Copy</button>
				</div>
			</div><!-- /.modal-content -->
		</div><!-- /.modal-dialog -->
	</div><!-- /.modal -->
	<!-- End of modal -->
	
	<!-- Modal -->
	<div id="modal_view_list_dnc" class="modal fade" role="dialog">
	  <div class="modal-dialog">

	    <!-- Modal content-->
	    <div class="modal-content">
	      <div class="modal-header">
	        <button type="button" class="close" data-dismiss="modal">&times;</button>
	        <h4 class="modal-title"><b>DNC List</b></h4>
	      </div>
	      <div class="modal-body">
			<div id="example1_wrapper" class="dataTables_wrapper form-inline dt-bootstrap" style="margin-top: 10px;">
				<div class="table-responsive">
					<table id="dnc_list" class="table table-bordered" style="width: 100%;">
						<thead>
							<tr>
								<th>Phone Number</th>
								<th>Delete</th>
							</tr>
						</thead>
						<tbody id="dnc_container">
							<!-- Data Here -->
						</tbody>
					</table>
				</div>
			</div>
	      </div>
	      <div class="modal-footer">
			<button type="button" class="btn btn-primary add-dnc">Add DNC</button>
	        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
	      </div>
	    </div>
	    <!-- End of modal content -->
	  </div>
	</div>

		<?php print $ui->standardizedThemeJS();?>

		<script src="js/easyWizard.js" type="text/javascript"></script>
		<!-- SELECT2-->
   		<script src="theme_dashboard/select2/dist/js/select2.js"></script>

		<script type="text/javascript">
			function get_list_dnc(){
				$.ajax({
					url: "./php/GetListDNC.php",
					type: 'POST',
					data: {
						
					},
					dataType: 'json',
					success: function(response) {
							// var values = JSON.parse(response.result);
							// console.log(response);

							$('#modal_view_list_dnc').modal('show');
							var table = $('#dnc_list').DataTable();
							table.fnClearTable();
							table.fnDestroy();
							$('#dnc_container').html(response);
							$('#dnc_list').DataTable({
								"searching": true,
								bFilter: true,
								"aoColumnDefs": [{
									"bSearchable": false,
									"aTargets": [ 3 ]
								},{
									"bSortable": false,
									"aTargets": [ 3 ]
								}]
							});
							$("#dnc_list").css("width","100%");
						}
				});
			}

			$(document).ready(function() {
				$(document).on('click', '.view-dnc', function(){
					get_list_dnc();
				});
				/*****
				** Functions for List
				*****/

					// initialize datatable
					$('#table_lists').DataTable( {
			            deferRender:    true,
				    	select: true,
				    	stateSave: true,
						"aaSorting": [[ 1, "asc" ]],
						"aoColumnDefs": [{
							"bSearchable": false,
							"aTargets": [ 0, 7 ]
						},{
							"bSortable": false,
							"aTargets": [ 0, 7 ]
						}]
					});

					/**
					* Add list
					**/
					$('#submit_list').click(function(){

	                $('#submit_list').val("Saving, Please Wait.....");
	                $('#submit_list').prop("disabled", true);

        			var validate = 0;
	                var list_id = $("#add_list_id").val();
	                var list_name = $("#list_name").val();
	                var list_desc = $("#list_desc").val();

	               	var form = $("#create_form");
	               	// Find disabled inputs, and remove the "disabled" attribute
					var disabled = form.find(':input:disabled').removeAttr('disabled');
					var serialized = form.serialize();

	                if(list_id === ""){
	                    validate = 1;
	                }

	                if(list_name === ""){
	                    validate = 1;
	                }

	                //if(list_desc == ""){
	                //    validate = 1;
	                //}

	                    if(validate < 1){
	                    //alert("Validated !");

	                        $.ajax({
	                            url: "./php/AddTelephonyList.php",
	                            type: 'POST',
	                            data: serialized,
	                            success: function(data) {
	                              // console.log(data);
	                                  if(data == 1){
	                                        swal("Success!", "List Successfully Created!", "success");
	                                        window.setTimeout(function(){location.reload();},3000);
	                                        $('#submit_list').val("Loading");
	                                  }
	                                  else{
	                                      sweetAlert("Oops...", "Something went wrong!", "error");
	                                      $('#submit_list').val("Submit");
	                                      $('#submit_list').prop("disabled", false);
	                                  }
	                            }
	                        });

	                    }else{
	                        sweetAlert("Oops...", "Something went wrong!", "error");
	                        validate = 0;
	                        $('#submit_list').val("Submit");
	                        $('#submit_list').prop("disabled", false);
	                    }
					});

					/**
					  * Edit user details
					 */
					$(document).on('click','.edit-list',function() {
						var url = './edittelephonylist.php';
						var id = $(this).attr('data-id');
						//alert(extenid);
						var form = $('<form action="' + url + '" method="post"><input type="hidden" name="modifyid" value="'+id+'" /></form>');
						//$('body').append(form);  // This line is not necessary
						$(form).submit();
					});
					
					/**
					  * Edit user details
					 */
					$(document).on('click','.download-list',function() {
						var url = 'php/ExportList.php';
						var id = $(this).attr('data-id');
						//alert(extenid);
						var form = $('<form action="' + url + '" method="post"><input type="hidden" name="listid" value="'+id+'" /></form>');
						//$('body').append(form);  // This line is not necessary
						$(form).submit();
					});
					

					/***
					** Delete
					***/

		             $(document).on('click','.delete-list',function() {
		             	var id = $(this).attr('data-id');
		                swal({
		                	title: "Are you sure?",
		                	text: "This action cannot be undone.",
		                	type: "warning",
		                	showCancelButton: true,
		                	confirmButtonColor: "#DD6B55",
		                	confirmButtonText: "Yes, delete this list!",
		                	cancelButtonText: "No, cancel please!",
		                	closeOnConfirm: false,
		                	closeOnCancel: false
		                	},
		                	function(isConfirm){
		                		if (isConfirm) {

		                			$.ajax({
				                        url: "./php/DeleteTelephonyList.php",
				                        type: 'POST',
				                        data: {
				                            listid:id,
				                        },
				                        success: function(data) {
				                        console.log(data);
				                            if(data == 1){
				                                swal("Deleted!", "List has been successfully deleted.", "success");
				                                window.setTimeout(function(){location.reload()},1000)
				                            }else{
				                               sweetAlert("Oops...", "Something went wrong!", "error");
				                            }
				                        }
				                    });

		                		} else {
		                			swal("Cancelled", "No action has been done :)", "error");
		                		}
		                	}
		                );



		             });

					$(document).on('click', '.copy-custom-fields', function(){
						var list_id = $(this).data('id');
						var list_name = $(this).data('name');

						$('.list-from').val(list_id);
						$('.list-from-label').val(list_id + ' - ' + list_name);
						$('#modal_custom_field_copy').modal('show');
					});

					$(document).on('click', '.btn-copy-cf', function(){
						var form_data = new FormData($("#copy_cf_form")[0]);

						swal({
							title: "Are you sure?",
							text: "This action cannot be undone.",
							type: "warning",
							showCancelButton: true,
							confirmButtonColor: "#DD6B55",
							confirmButtonText: "Yes, Copy Custom Fields.",
							cancelButtonText: "No, cancel please!",
							closeOnConfirm: false,
							closeOnCancel: false
							},
							function(isConfirm){
								if (isConfirm) {
									$.ajax({
													url: "./php/CopyCustomFields.php",
													type: 'POST',
													data: form_data,
													// dataType: 'json',
													cache: false,
	                        contentType: false,
	                        processData: false,
													success: function(data) {
															// console.log(data);
															if(data == "success"){
																swal({
																		title: "Success",
																		text: "Custom Fields Successfully Copied",
																		type: "success"
																	},
																	function(){
																		location.reload();
																		$(".preloader").fadeIn();
																	}
																);
															}else{
																	sweetAlert("Oops...", "Something went wrong! "+ data, "error");
															}
													}
										});
									} else {
											swal("Cancelled", "No action has been done :)", "error");
									}
							}
						);
					});

					$('#list-modal').wizard();
					// $('#call-playback-modal').modal('show');

					$('#auto_generate').on('change', function() {
					//  alert( this.value ); // or $(this).val()
						if($('#auto_generate').is(":checked")){
		            		$('#add_list_id').val("<?php echo $next_list;?>");
		            		$('#list_name').val("<?php echo $next_listname;?>");
		            		$('#list_desc').val("<?php echo $next_listdesc;?>");
		            		$('#add_list_id').prop("disabled", true);
		            	}
		            	if(!$('#auto_generate').is(":checked")){
		            		$('#add_list_id').val("");
		            		$('#list_name').val("");
		            		$('#list_desc').val("");
		            		$('#add_list_id').prop("disabled", false);
		            	}
					});

				/****
				** Functions for upload leads
				*****/
					//initialize single selecting
					$('#select2-1').select2({
				        theme: 'bootstrap'
				    });

						$('.select2').select2({
									theme: 'bootstrap'
						});

					$('.browse-btn').click(function(){
						$('.file-box').click();
					});

					$('.file-box').change(function(){
						var myFile = $(this).prop('files');
						var Filename = myFile[0].name;

						$('.file-name').val(Filename);
						console.log($(this).val());
					});

				//-- end
			});
		</script>

		<?php print $ui->creamyFooter();?>
    </body>
</html>
