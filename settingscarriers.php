<?php	

    ###########################################################
    ### Name: telephonymusiconhold.php                      ###
    ### Functions: Manage MOH                               ###
    ### Copyright: GOAutoDial Ltd. (c) 2011-2016            ###
    ### Version: 4.0                                        ###
    ### Written by: Alexander Abenoja & Noel Umandap        ###
    ### License: AGPLv2                                     ###
    ###########################################################

	require_once('./php/UIHandler.php');
	require_once('./php/CRMDefaults.php');
    require_once('./php/LanguageHandler.php');
    include('./php/Session.php');
    include('./php/goCRMAPISettings.php'); 

	$ui = \creamy\UIHandler::getInstance();
	$lh = \creamy\LanguageHandler::getInstance();
	$user = \creamy\CreamyUser::currentUser();
?>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Carriers</title>
        <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
       
        <?php print $ui->standardizedThemeCSS(); ?>

        <!-- Wizard Form style -->
        <link href="css/wizard-form.css" rel="stylesheet" type="text/css" />
        <link href="css/style.css" rel="stylesheet" type="text/css" />
        <!-- Wizard Form style -->
    	<link rel="stylesheet" href="css/easyWizard.css">

        <!-- DATA TABLES -->
        <link href="css/datatables/dataTables.bootstrap.css" rel="stylesheet" type="text/css" />

        <?php print $ui->creamyThemeCSS(); ?>

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
                        <?php $lh->translateText("settings"); ?>
                        <small><?php $lh->translateText("carriers_management"); ?></small>
                    </h1>
                    <ol class="breadcrumb">
                        <li><a href="./index.php"><i class="fa fa-phone"></i> <?php $lh->translateText("home"); ?></a></li>
                       <li><?php $lh->translateText("settings"); ?></li>
						<li class="active"><?php $lh->translateText("carriers"); ?>
                    </ol>
                </section>

                <!-- Main content -->
                <section class="content">
                <?php if ($user->userHasAdminPermission()) { ?>
                    <div class="panel panel-default">
                        <div class="panel-body table" id="recording_table">
                            <legend><?php $lh->translateText("carriers"); ?></legend>
							<?php print $ui->getListAllCarriers(); ?>
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
		
		<div class="action-button-circle" data-toggle="modal" data-target="#wizard-modal">
			<?php print $ui->getCircleButton("carriers", "plus"); ?>
		</div>
<?php
	/*
	* MODAL
	*/
	$user_groups = $ui->API_goGetUserGroupsList();
	$carriers = $ui->API_getListAllCarriers();
?>
	<!-- ADD WIZARD MODAL -->
	<div class="modal fade" id="wizard-modal" tabindex="-1">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
				
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					<h4 class="modal-title animate-header"><b>Carrier Wizard » Add New Carrier</b></h4>
				</div>
				<div class="modal-body wizard-content" style="min-height: 50%; overflow-y:auto; overflow-x:hidden;">
				
				<form id="create_form" role="form">
				<!-- STEP 1 -->
					<div class="wizard-step">
						<div class="form-group mt">
							<label class="col-sm-3 control-label" for="carrier_type">Carrier Type:</label>
							<div class="col-sm-9">
								<select id="carrier_type" class="form-control" name="carrier_type">
									<option value="justgo">  GoAutodial - JustGoVoIP </option>
									<option value="manual">  Manual </option>
									<option value="copy">  Copy Carrier </option>
								</select>
							</div>
						</div>
					</div>
				
				<!-- STEP 2 -->
					<div class="wizard-step">
						<!-- IF JustGoVoIP -->
						<div class="justGo_div" style="display:none;">

						</div>
						<!-- IF MANUAL / COPY -->
						<div class="manual_copy_div" style="display:none;">
							<div class="form-group mt">
											<label for="carrier_id" class="col-sm-4 control-label">Carrier ID</label>
											<div class="col-sm-8 mb">
												<input type="text" class="form-control" name="carrier_id" id="carrier_id" placeholder="Carrier ID">
											</div>
										</div>
							<div class="form-group">
								<label for="carrier_name" class="col-sm-4 control-label">Carrier Name</label>
								<div class="col-sm-8 mb">
									<input type="text" class="form-control" name="carrier_name" id="carrier_name" placeholder="Carrier Name">
								</div>
							</div>
						</div>
						<!-- IF MANUAL -->
						<div class="manual_div" style="display:none;">
							<div class="form-group">
								<label for="carrier_description" class="col-sm-4 control-label">Carrier Description</label>
								<div class="col-sm-8 mb">
									<input type="text" class="form-control" name="carrier_description" id="carrier_description" placeholder="Carrier Description">
								</div>
							</div>
							<div class="form-group">
								<label for="carrier_description" class="col-sm-4 control-label">User Group</label>
								<div class="col-sm-8 mb">
									<select id="user_group" class="form-control" name="user_group">
											<option value="ALL">  ALL USERGROUPS  </option>
										<?php
											for($i=0;$i<count($user_groups->user_group);$i++){
										?>
											<option value="<?php echo $user_groups->user_group[$i];?>">  <?php echo $user_groups->user_group[$i].' - '.$user_groups->group_name[$i];?>  </option>
										<?php
											}
										?>
									</select>
								</div>
							</div>
							<div class="form-group">
								<label for="carrier_desc" class="col-sm-4 control-label">Authentication</label>
								<div class="col-sm-8 mb">
									<div class="row mt">
										<label class="col-sm-1">
											&nbsp;
										</label>
										<label class="col-sm-4 radio-inline c-radio" for="auth_ip">
											<input id="auth_ip" type="radio" name="authentication" value="auth_ip" checked>
											<span class="fa fa-circle"></span> IP Based
										</label>
										<label class="col-sm-4 radio-inline c-radio" for="auth_reg">
											<input id="auth_reg" type="radio" name="authentication" value="auth_reg">
											<span class="fa fa-circle"></span> Registration
										</label>
									</div>
								</div>
							</div>
							<div class="form-group registration_div" style="display:none;">
								<label for="username" class="col-sm-4 control-label">Username</label>
								<div class="col-sm-8 mb">
									<input type="text" class="form-control" name="username" id="username" placeholder="Username">
								</div>
							</div>
							<div class="form-group registration_div" style="display:none;">
								<label for="password" class="col-sm-4 control-label">Password</label>
								<div class="col-sm-8 mb">
									<input type="text" class="form-control" name="password" id="password" placeholder="Password">
								</div>
							</div>
							<div class="form-group">
								<label for="server_ip" class="col-sm-4 control-label">SIP Server</label>
								<div class="col-sm-8 mb">
									<input type="text" class="form-control" name="server_ip" id="server_ip" placeholder="Server IP/Host">
								</div>
							</div>
							<div class="form-group">
								<label for="carrier_desc" class="col-sm-4 control-label">Codecs</label>
								<div class="col-sm-8 mb">
									<div class="row mt">
										<label class="col-sm-1">
											&nbsp;
										</label>
										<label class="col-sm-2 checkbox-inline c-checkbox" for="gsm">
											<input type="checkbox" id="gsm" name="codecs" value="GSM" checked>
											<span class="fa fa-check"></span> GSM
										</label>
										<label class="col-sm-2 checkbox-inline c-checkbox" for="ulaw">
											<input type="checkbox" id="ulaw" name="codecs" value="ULAW" checked>
											<span class="fa fa-check"></span> ULAW
										</label>
										<label class="col-sm-2 checkbox-inline c-checkbox" for="alaw">
											<input type="checkbox" id="alaw" name="codecs" value="ALAW">
											<span class="fa fa-check"></span> ALAW
										</label>
										<label class="col-sm-2 checkbox-inline c-checkbox" for="g729">
											<input type="checkbox" id="g729" name="codecs" value="G729">
											<span class="fa fa-check"></span> G729
										</label>
									</div>
								</div>
							</div>
							<div class="form-group">
								<label for="carrier_desc" class="col-sm-4 control-label">DTMF Mode</label>
								<div class="col-sm-8 mb">
									<div class="row mt">
										<label class="col-sm-1">
											&nbsp;
										</label>
										<label class="col-sm-3 radio-inline c-radio" for="dtmf_1">
											<input id="dtmf_1" type="radio" name="dtmf" value="RFC2833" checked>
											<span class="fa fa-circle"></span> RFC2833   
										</label>
										<label class="col-sm-3 radio-inline c-radio" for="dtmf_2">
											<input id="dtmf_2" type="radio" name="dtmf" value="inband">
											<span class="fa fa-circle"></span> Inband   
										</label>
										<label class="col-sm-3 radio-inline c-radio" for="dtmf_3">
											<input id="dtmf_3" type="radio" name="dtmf" value="custom">
											<span class="fa fa-circle"></span> Custom      
										</label>
									</div>
								</div>
							</div>
							<div class="form-group" id="input_custom_dtmf" style="display:none;">
								<label for="custom_dtmf" class="col-sm-4 control-label"></label>
								<div class="col-sm-8 mb">
									<input type="text" class="form-control" id="custom_dtmf" name="custom_dtmf" placeholder="Enter Custom DTMF" >
								</div>
							</div>
							<div class="form-group">
								<label for="protocol" class="col-sm-4 control-label">Protocol</label>
								<div class="col-sm-8 mb">
									<select class="form-control" name="protocol" id="protocol">
									<?php
										$protocol = NULL;
											$protocol .= '<option value="SIP" > SIP </option>';
											$protocol .= '<option value="IAX2" > IAX2 </option>';
											$protocol .= '<option value="CUSTOM" > CUSTOM </option>';
										echo $protocol;
									?>
									</select>
								</div>
							</div>
							<div class="form-group">
								<label for="server_ip" class="col-sm-4 control-label">Server IP</label>
								<div class="col-sm-8 mb">
									<div class="row mt">
										<label class="col-sm-1">
											&nbsp;
										</label>
										<label class="col-sm-2 checkbox-inline c-checkbox" for="server_ip">
											<input type="checkbox" id="server_ip" name="server_ip" value="<?php echo gourl;?>" checked>
											<span class="fa fa-check"></span>
										</label>
									</div>
								</div>
							</div>
						</div>
						<!-- /.manual -->

						<!-- IF COPY -->
						<div class="copy_div" style="display:none;">
							<div class="form-group mt">
								<label for="server_ip" class="col-sm-4 control-label">Server IP</label>
								<div class="col-sm-8 mb">
									<select class="form-control">
										<?php
											for($i=0;$i<1;$i++){
										?>
											<option><?php echo $carriers->server_ip[$i];?> - GOautodial Meetme Server</option>
										<?php
											}
										?>
									</select>
								</div>
							</div>
							<div class="form-group">
								<label for="carrier_name" class="col-sm-4 control-label">Source Carrier</label>
								<div class="col-sm-8 mb">
									<select class="form-control">
										<?php
											for($i=0;$i<count($carriers->carrier_id);$i++){
										?>
											<option><?php echo $carriers->carrier_id[$i].' - '.$carriers->carrier_name[$i].' - '.$carriers->server_ip[$i];?></option>
										<?php
											}
										?>
									</select>
								</div>
							</div>
						</div>
						<!-- /.copy -->

						<!-- IF MANUAL & COPY -->
						<div class="manual_copy_div" style="display:none;">
							<div class="form-group">
								<label for="status" class="col-sm-4 control-label">Active</label>
								<div class="col-sm-8 mb">
									<select class="form-control" name="active" id="active">
									<?php
										$active = NULL;
											$active .= '<option value="Y" > YES </option>';
											$active .= '<option value="N" > NO </option>';
										echo $active;
									?>
									</select>
								</div>
							</div>
						</div>
						<!-- /.copy_manual -->

						<!-- IF JUST GO VOIP -->
						<div class="justgo_div" style="display:none;">
							<style type="text/css">
								.welcome-header{width:100%;text-align:center;}
								.sales-email{float:right;text-align:left;font-size:12px;margin-top:5px; margin-right: 30px}
							</style>
							<div class="welcome-header">
	                          <span>Welcome to</span><br class="clear"><br class="clear">
	                          <span><a href="https://webrtc.goautodial.com/justgocloud/" target="_new"><img src="https://webrtc.goautodial.com/img/goautodial_logo.png"></a></span><br class="clear"><br class="clear">
	                          <span>GoAutoDial Cloud Call Center Cloud Call Center</span><br>
	                          <br>
	                          <span align="center" style="padding-left: 100px;">

							<p style="width: 90%; padding-left: 40px; line-height: 17px;" align="justify">	GoAutoDial Cloud Call Center is an easy to set up and easy to use, do it yourself (DIY) cloud based telephony solution for any type of organization in wherever country you conduct your sales, marketing, service and support activites. Designed for large enterprise-grade call center companies but priced to fit the budget of the Small Business Owner, GoAutoDial Cloud Call Center uses intuitive graphical user interfaces so that deployment is quick and hassle-free, among its dozens of hot features. </p><br>
							<p style="width: 90%; padding-left: 40px; line-height: 17px;" align="justify">Using secure cloud infrastructures certified by international standards, GoAutoDial Cloud Call Center is a "Use Anywhere, Anytime" web app so that you can create more customers for life – in the office, at home or at the beach. </p>
	                          </span>
	                          <br>
	                          <br>
	                          <span class="sales-email">  **Email <a href="mailto:sales@goautodial.com">sales@goautodial.com</a> to get 120 free minutes (US, UK and Canada calls only).</span><br>
	                       </div>
						</div>
						<!-- ./justgo -->
					</div><!--end of step2 -->
					
					<!-- STEP 3 -->
					<div class="wizard-step" style="display:none;">

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

				<div class="modal-footer wizard-buttons">
					<!-- The wizard button will be inserted here. -->
				</div>
			</div>
		</div>
	</div><!-- end of modal -->

	<!-- Modal -->
	<div id="view-calltime-modal" class="modal fade" role="dialog">
	  <div class="modal-dialog">

	    <!-- Modal content-->
	    <div class="modal-content">
	      <div class="modal-header">
	        <button type="button" class="close" data-dismiss="modal">&times;</button>
	        <h4 class="modal-title"><b>Call Time Details</b></h4>
	      </div>
	      <div class="modal-body">
		<div class="form-horizontal">
			<div class="message_box"></div>
			<div class="form-group">
				<label class="control-label col-lg-4">Music on Hold Name:</label>
				<div class="col-lg-8">
					<input type="text" class="form-control moh_name">
				</div>
			</div>
		</div>
	      </div>
	      <div class="modal-footer">
		<button type="button" class="btn btn-primary btn-update-calltime" data-id="">Modify</button>
	        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
	      </div>
	    </div>
	    <!-- End of modal content -->
	  </div>
	</div>
	<!-- End of modal -->
		
		<!-- Forms and actions -->
		<?php print $ui->standardizedThemeJS(); ?>
		<script src="js/easyWizard.js" type="text/javascript"></script> 

		<script type="text/javascript">
			$(document).ready(function() {
				$('#carriers').dataTable();
				
				/* on authorization change */
				$('input[type=radio][name=authentication]').on('change', function() {
				//  alert( this.value ); // or $(this).val()
					if(this.value == "auth_reg") {
					  $('.registration_div').show();
					}
					if(this.value == "auth_ip") {
					  $('.registration_div').hide();
					}
				});

				 /* on custom dtmf select */
				$('input[type=radio][name=dtmf]').on('change', function() {
				//  alert( this.value ); // or $(this).val()
					if(this.value == "custom") {
						$('#input_custom_dtmf').show();
					}else{
						$('#input_custom_dtmf').hide();
					}
				});

				$("#wizard-modal").wizard({

					onnext:function(){		

						var carrier_type = document.getElementById('carrier_type').value;

						if(carrier_type == "manual" || carrier_type == "copy"){
							$('.manual_copy_div').show();
						}else{
							$('.manual_copy_div').hide();
						}

						if(carrier_type == "manual"){
							$('.manual_div').show();
						}else{
							$('.manual_div').hide();
						}

						if(carrier_type == "copy"){
							$('.copy_div').show();
						}else{
							$('.copy_div').hide();
						}

						if(carrier_type == "justgo"){
							$('.justgo_div').show();
							document.getElementById("container-element").className = "wizard-step";
						}else{
							$('.justgo_div').hide();
							document.getElementById("container-element").className = "";
						}
						

					},
	                onfinish:function(){
	                
					var generate_phone_logins = document.getElementById('generate_phone_logins').value;
					var phone_logins = document.getElementById('phone_logins').value;
					var phone_pass = document.getElementById('phone_pass').value;

					var user_form = document.getElementById('user_form').value;
					var fullname = document.getElementById('fullname').value;
					var password = document.getElementById('password').value;
					var conf_password = document.getElementById('conf_password').value;

						if(generate_phone_logins == "Y"){
							if(phone_logins == ""){
								validate_wizard = 1;
							}
							if(phone_pass == ""){
								validate_wizard = 1;
							}
						}

						if(user_form == ""){
							validate_wizard = 1;
						}

						if(fullname == ""){
							validate_wizard = 1;
						}
						
						if(password != conf_password || password == ""){
							validate_wizard = 1;
						}

						if(validate_wizard == 0){
							//alert("User Created!");
							$.ajax({
								//url: "./php/CreateTelephonyUser.php",
								type: 'POST',
								data: $("#create_form").serialize(),
								success: function(data) {
								  // console.log(data);
									  if(data == 1){
									  	  swal("Success!", "Carrier Successfully Added!", "success");
										  window.setTimeout(function(){location.reload()},1000)
										  $('#add_button').val("Loading...");
									  }else{
									  	  $('#add_button').val("Submit");
	        							  $('#add_button').attr("disabled", false);
										  sweetAlert("Oops...", "Something went wrong! "+data, "error");
									  }
								}
							});
						}else{
							sweetAlert("Oops...", "Something went wrong! ", "error");
							validate_wizard = 0;
						}

	                }
					
	            });

				/**
				  * Edit user details
				 */
				$(document).on('click','.edit-carrier',function() {
					var url = 'editsettingscarrier.php';
					var cid = $(this).attr('data-id');
					var role = $(this).attr('data-role');
					//alert(userid);
					var form = $('<form action="' + url + '" method="post"><input type="hidden" name="cid" value="'+cid+'" /><input type="hidden" name="role" value="'+role+'"></form>');
					//$('body').append(form);  // This line is not necessary
					$(form).submit();
				 });

				$('.delete-carrier').click(function(){
					var id = $(this).attr('data-id');
                    swal({   
                        title: "Are you sure?",   
                        text: "This action cannot be undone.",   
                        type: "warning",   
                        showCancelButton: true,   
                        confirmButtonColor: "#DD6B55",   
                        confirmButtonText: "Yes, delete this carrier!",   
                        cancelButtonText: "No, cancel please!",   
                        closeOnConfirm: false,   
                        closeOnCancel: false 
                        }, 
                        function(isConfirm){   
                            if (isConfirm) { 
                            	$.ajax({
									url: "./php/DeleteCarrier.php",
									type: 'POST',
									data: { 
									      carrier_id : id,
									},
									dataType: 'json',
									success: function(data) {
										if(data == 1){
											swal("Success!", "Music On Hold Successfully Deleted!", "success");
                                            window.setTimeout(function(){location.reload()},1000)
										}else{
											sweetAlert("Oops...", "Something went wrong! "+data, "error");
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
    </body>
</html>
