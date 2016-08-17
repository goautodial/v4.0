<?php	
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);	
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

		<!-- Bootstrap Player -->
		<link href="css/bootstrap-player.css" rel="stylesheet" type="text/css" />

        <?php print $ui->creamyThemeCSS(); ?>

        <!-- Datetime picker CSS --> 
		<link rel="stylesheet" href="theme_dashboard/eonasdan-bootstrap-datetimepicker/build/css/bootstrap-datetimepicker.min.css">

        <!-- Data Tables JS -->
        <script src="js/plugins/datatables/jquery.dataTables.js" type="text/javascript"></script>
        <script src="js/plugins/datatables/dataTables.bootstrap.js" type="text/javascript"></script>

        <!-- Date Picker JS -->
        <script type="text/javascript" src="theme_dashboard/eonasdan-bootstrap-datetimepicker/build/js/moment.js"></script>
		<script type="text/javascript" src="theme_dashboard/eonasdan-bootstrap-datetimepicker/build/js/bootstrap-datetimepicker.min.js"></script>
		
		<!-- SELECT2 CSS -->
   		<link rel="stylesheet" href="theme_dashboard/select2/dist/css/select2.css">
   		<link rel="stylesheet" href="theme_dashboard/select2-bootstrap-theme/dist/select2-bootstrap.css">

		<!-- Bootstrap Player JS -->
		<script src="js/bootstrap-player.js" type="text/javascript"></script>

		

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
                        <small><?php $lh->translateText("list_management"); ?></small>
                    </h1>
                    <ol class="breadcrumb">
                        <li><a href="./index.php"><i class="fa fa-home"></i> <?php $lh->translateText("home"); ?></a></li>
                       <li><?php $lh->translateText("telephony"); ?></li>
						<li class="active"><?php $lh->translateText("lists"); ?>
                    </ol>
                </section>

		<?php
			/****
			** API to get data of tables
			****/
			$lists = $ui->API_goGetAllLists();
		?>
                <!-- Main content -->
                <section class="content">
                	<div class="row">
						<div class="col-lg-9">
		                	<div class="panel panel-default">
								<div class="panel-body">
									<legend>Lists</legend>

									<table class="table table-striped table-bordered table-hover" id="table_lists">
									   <thead>
										  <tr>
											 <th class='hide-on-medium hide-on-low'>List ID</th>
											 <th>Name</th>
											 <th class='hide-on-medium hide-on-low'>Status</th>
											 <th class='hide-on-medium hide-on-low'>Leads Count</th>
											 <th class='hide-on-medium hide-on-low'>Campaign</th>
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
												
												$action_list = $ui->getUserActionMenuForLists($lists->list_id[$i], $lists->list_name[$i]);
											?>
												<tr>
								                    <td class='hide-on-low'><a class='edit-list' data-id='<?php echo $lists->list_id[$i];?>'><?php echo $lists->list_id[$i];?></td>
								                    <td><?php echo $lists->list_name[$i];?></td>
													<td class='hide-on-medium hide-on-low'><?php echo $lists->active[$i];?></td>
								                    <td class='hide-on-medium hide-on-low'><?php echo $lists->tally[$i];?></td>
													<td class='hide-on-medium hide-on-low'><?php echo $lists->campaign_id[$i];?></td>
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
			            <div class="col-lg-3">
	           				<h3 class="m0 pb-lg">Upload/Import Leads</h3>
	           				<form action="./php/AddLoadLeads.php" method="POST" enctype="multipart/form-data">
								
								<div class="form-group">
									<label>List ID:</label>
									<div class="form-group">
										<select id="select2-1" class="form-control" name="list_id">
										<option value="" selected disabled>-- Select List ID --</option>
											<?php 
												for($i=0;$i<count($lists->list_id);$i++){
		                                			echo '<option value="'.$lists->list_id[$i].'">'.$lists->list_id[$i].' - '.$lists->list_name[$i].'</option>';
		                                		}
											?>
										</select>
									</div>
								</div>
								<div class="form-group">
									<label>CSV File:</label>
									<div class="form-group">
										<div class="input-group">
									      <input type="text" class="form-control file-name" name="file_name" placeholder="CSV File">
									      <span class="input-group-btn">
									        <button type="button" class="btn btn-default browse-btn" type="button">Browse</button>
									      </span>
									    </div><!-- /input-group -->
									    <input type="file" class="file-box hide" name="file_upload">
									</div>
								</div>
								<div class="form-group">
									<button type="submit" class="btn btn-primary">Submit</button>
								</div>
							</form>
							<?php 
                        		if(isset($_GET['message'])){
                        			echo '<div class="col-lg-12" style="margin-top: 10px;">';
                        			if($_GET['message'] == "Success"){
                        				echo '<div class="alert alert-success">
										  <strong>Success!</strong> Upload of leads was successful.
										</div>';
                        			}else{
                        				echo '<div class="alert alert-danger">
										  <strong>Error!</strong> Failed to upload file.
										</div>';
                        			}
                        			echo '</div>';
                        		}
                        	?>
	           			</div><!-- ./upload leads -->
                	</div>
                </section><!-- /.content -->
            </aside><!-- /.right-side -->
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
								<input type="text" class="form-control" name="add_list_id" id="add_list_id" placeholder="List ID" value="<?php echo $next_list;?>" disabled />
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-3 control-label" for="list_name">List Name:</label>
							<div class="col-sm-9 mb">
								<input type="text" class="form-control" name="list_name" id="list_name" placeholder="List Name" value="<?php echo $next_listname;?>" />
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-3 control-label" for="list_desc">List Description:</label>
							<div class="col-sm-9 mb">
								<input type="text" class="form-control" name="list_desc" id="list_desc" placeholder="List Description"  value="<?php echo $next_listdesc;?>"/>
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
	<!-- End of modal -->

		<?php print $ui->standardizedThemeJS();?>

		<script src="js/easyWizard.js" type="text/javascript"></script> 
		<!-- SELECT2-->
   		<script src="theme_dashboard/select2/dist/js/select2.js"></script>
   		
		<script type="text/javascript">

			$(document).ready(function() {
				
				/*****
				** Functions for List
				*****/

					// initialize datatable
					$('#table_lists').DataTable( {
			            deferRender:    true,
				    	select: true,
				    	stateSave: true
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

	                if(list_id == ""){
	                    validate = 1;
	                }

	                if(list_name == ""){
	                    validate = 1;
	                }

	                if(list_desc == ""){
	                    validate = 1;
	                }

	                    if(validate == 0){
	                    //alert("Validated !");
	                    
	                        $.ajax({
	                            url: "./php/AddTelephonyList.php",
	                            type: 'POST',
	                            data: serialized,
	                            success: function(data) {
	                              // console.log(data);
	                                  if(data == 1){
	                                        swal("Success!", "List Successfully Created!", "success")
	                                        window.setTimeout(function(){location.reload()},3000)
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
