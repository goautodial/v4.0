<?php

	###################################################
	### Name: telephonyinbound.php 					###
	### Functions: Manage Inbound, IVR & DID  		###
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
        <title>Audio Files</title>
        <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
        
        <?php print $ui->standardizedThemeCSS(); ?>
    	
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
                        <?php $lh->translateText("telephony"); ?>
                        <small><?php $lh->translateText("audiofiles_management"); ?></small>
                    </h1>
                    <ol class="breadcrumb">
                        <li><a href="./index.php"><i class="fa fa-phone"></i> <?php $lh->translateText("home"); ?></a></li>
                        <li><?php $lh->translateText("telephony"); ?></li>
						<li class="active"><?php $lh->translateText("audiofiles"); ?>
                    </ol>
                </section>

                <!-- Main content -->
                <section class="content">
                <?php if ($user->userHasAdminPermission()) { ?>

			<div class="panel panel-default">
				<div class="panel-body">
					<legend>Audio Files </legend>

		            <div role="tabpanel">
						
						<ul role="tablist" class="nav nav-tabs nav-justified">

						 <!-- MOH panel tabs-->
							 <li role="presentation" class="active">
								<a href="#moh_tab" aria-controls="moh_tab" role="tab" data-toggle="tab" class="bb0">
								    Music On-Hold</a>
							 </li>
						<!-- Voicefiles panel tab -->
							 <li role="presentation">
								<a href="#voicefiles_tab" aria-controls="voicefiles_tab" role="tab" data-toggle="tab" class="bb0">
								    Voice Files </a>
							 </li>
						  </ul>
						  
						<!-- Tab panes-->
						<div class="tab-content bg-white">

							<!--==== MOH ====-->
							<div id="moh_tab" role="tabpanel" class="tab-pane active">
								<?php print $ui->getListAllMusicOnHold(); ?>
							</div>

							<!--==== Voicefiles ====-->
							<div id="voicefiles_tab" class="tab-pane">
								<?php print $ui->getListAllVoiceFiles(); ?>
							</div>

						</div><!-- END tab content-->

							<!-- /fila con acciones, formularios y demás -->
							<?php
								} else {
									print $ui->calloutErrorMessage($lh->translationFor("you_dont_have_permission"));
								}
							?>
							
						<div class="bottom-menu skin-blue">
							<div class="action-button-circle" data-toggle="modal">
								<?php print $ui->getCircleButton("inbound", "plus"); ?>
							</div>
							<div class="fab-div-area" id="fab-div-area">
								<ul class="fab-ul" style="height: 250px;">
									<li class="li-style"><a class="fa fa-music fab-div-item" data-toggle="modal" data-target="#add_moh" title="Add a Music On-hold"></a></li><br/>
									<li class="li-style"><a class="fa fa-volume-up fab-div-item" data-toggle="modal" data-target="#add_voicefile" title="Add a Voice File"></a></li><br/>
								</ul>
							</div>
						</div>
					</div>
				</div><!-- /. body -->
			</div><!-- /. panel -->
        </section><!-- /.content -->
    </aside><!-- /.right-side -->
</div><!-- ./wrapper -->

		<?php print $ui->standardizedThemeJS(); ?>
        <!-- JQUERY STEPS-->
  		<script src="theme_dashboard/js/jquery.steps/build/jquery.steps.js"></script>

 <script type="text/javascript">
	$(document).ready(function() {

		/*******************
		** INITIALIZATIONS
		*******************/
			// loads the fixed action button
				$(".bottom-menu").on('mouseenter mouseleave', function () {
				  $(this).find(".fab-div-area").stop().slideToggle({ height: 'toggle', opacity: 'toggle' }, 'slow');
				});

			//loads datatable functions
				$('#music-on-hold_table').dataTable();
				$('#voicefiles').dataTable();
		
		/*******************
		** MOH EVENTS
		*******************/

			/*********
			** INIT WIZARD
			*********/
				var ingroup_form = $("#create_ingroup"); // init form wizard 

			    ingroup_form.validate({
			        errorPlacement: function errorPlacement(error, element) { element.after(error); }
			    });
			    ingroup_form.children("div").steps({
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
					        $(".body:eq(" + newIndex + ") label.error", ingroup_form).remove();
					        $(".body:eq(" + newIndex + ") .error", ingroup_form).removeClass("error");
					    }

			            ingroup_form.validate().settings.ignore = ":disabled,:hidden";
			            return ingroup_form.valid();
			        },
			        onFinishing: function (event, currentIndex)
			        {
			            ingroup_form.validate().settings.ignore = ":disabled";
			            return ingroup_form.valid();
			        },
			        onFinished: function (event, currentIndex)
			        {

			        	$('#finish').text("Loading...");
			        	$('#finish').attr("disabled", true);

			        	/*********
						** ADD EVENT 
						*********/
				            // Submit form via ajax
					            $.ajax({
									url: "./php/AddTelephonyIngroup.php",
									type: 'POST',
									data: $("#create_ingroup").serialize(),
									success: function(data) {
									  // console.log(data);
										  if(data == "success"){
												swal("Success!", "Ingroup Successfully Created!", "success");
										  		window.setTimeout(function(){location.reload()},1000);

										  		$('#finish').text("Submit");
												$('#finish').attr("disabled", false);
										  }
										  else{
											  sweetAlert("Oops...", "Something went wrong! "+data, "error");

											  $('#finish').text("Submit");
											  $('#finish').attr("disabled", false);
										  }
									}
								});
			        }
			    }); // end of wizard
			
			//------------------------

			/*********
			** EDIT INGROUP
			*********/

				$(".edit-ingroup").click(function(e) {
					e.preventDefault();
					var url = './edittelephonyinbound.php';
					var form = $('<form action="' + url + '" method="post"><input type="hidden" name="groupid" value="' + $(this).attr('data-id') + '" /></form>');
					//$('body').append(form);  // This line is not necessary
					$(form).submit();
				});

			/*********
			** DELETE INGROUP
			*********/
				//DELETE INGROUPS
				$(document).on('click','.delete-ingroup',function() {
				 	var id = $(this).attr('data-id');
	                swal({   
	                	title: "Are you sure?",   
	                	text: "This action cannot be undone.",   
	                	type: "warning",   
	                	showCancelButton: true,   
	                	confirmButtonColor: "#DD6B55",   
	                	confirmButtonText: "Yes, delete this inbound!",   
	                	cancelButtonText: "No, cancel please!",   
	                	closeOnConfirm: false,   
	                	closeOnCancel: false 
	                	}, 
	                	function(isConfirm){   
	                		if (isConfirm) { 
	                			$.ajax({
									url: "./php/DeleteTelephonyInbound.php",
									type: 'POST',
									data: { 
										groupid:id,
									},
									success: function(data) {
									console.log(data);
								  		if(data == 1){
								  			swal("Success!", "Inbound Successfully Deleted!", "success");
											window.setTimeout(function(){location.reload()},3000)
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
		
		//-------------------- end of main ingroup events

		/*******************
		** VOICEFILES EVENTS
		*******************/

			/*********
			** DID WIZARD
			*********/
				var did_form = $("#create_phonenumber"); // init form wizard 

			    did_form.validate({
			        errorPlacement: function errorPlacement(error, element) { element.after(error); }
			    });
			    did_form.children("div").steps({
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
					        $(".body:eq(" + newIndex + ") label.error", did_form).remove();
					        $(".body:eq(" + newIndex + ") .error", did_form).removeClass("error");
					    }

			            did_form.validate().settings.ignore = ":disabled,:hidden";
			            return did_form.valid();
			        },
			        onFinishing: function (event, currentIndex)
			        {
			            did_form.validate().settings.ignore = ":disabled";
			            return did_form.valid();
			        },
			        onFinished: function (event, currentIndex)
			        {

			        	$('#finish').text("Loading...");
			        	$('#finish').attr("disabled", true);

			        	/*********
						** ADD EVENT 
						*********/
				            $.ajax({
								url: "./php/AddTelephonyPhonenumber.php",
								type: 'POST',
								data: $("#create_phonenumber").serialize(),
								success: function(data) {
								  // console.log(data);
									  if(data == 1){
											swal("Success!", "Phone Number Successfully Created!", "success");
									  		window.setTimeout(function(){location.reload()},1000)
									  		$('#submit_did').val("Submit");
											$('#submit_did').attr("disabled", false);
									  }else{
											sweetAlert("Oops...", "Something went wrong! "+data, "error");
											$('#submit_did').val("Submit");
											$('#submit_did').attr("disabled", false);
									  }
								}
							});
							
			        }
			    }); // end of wizard
			
			//------------------------

			/*********
			** EDIT DID
			*********/

				$(document).on('click','.edit-phonenumber',function() {
					var url = './edittelephonyinbound.php';
					var form = $('<form action="' + url + '" method="post"><input type="hidden" name="did" value="' + $(this).attr('data-id') + '" /></form>');
					//$('body').append(form);  // This line is not necessary
					$(form).submit();
				});

			/*********
			** DELETE DID
			*********/

				$(document).on('click','.delete-phonenumber',function() {
				 	var id = $(this).attr('data-id');
	                swal({   
	                	title: "Are you sure?",   
	                	text: "This action cannot be undone.",   
	                	type: "warning",   
	                	showCancelButton: true,   
	                	confirmButtonColor: "#DD6B55",   
	                	confirmButtonText: "Yes, delete this phonenumber!",   
	                	cancelButtonText: "No, cancel please!",   
	                	closeOnConfirm: false,   
	                	closeOnCancel: false 
	                	}, 
	                	function(isConfirm){   
	                		if (isConfirm) { 
	                			$.ajax({
									url: "./php/DeleteTelephonyInbound.php",
									type: 'POST',
									data: { 
										modify_did:id,
									},
									
									success: function(data) {
									//console.log(modify_did);
									console.log(data);
								  		if(data == 1){
								  			swal("Success!", "Phonenumber Successfully Deleted!", "success");
											//window.setTimeout(function(){location.reload()},3000)
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
		
		//-------------------- end of main did events

		/*******************
		** OTHER TRIGGER EVENTS and FILTERS
		*******************/
			/*** INGROUP ***/
				// disable special characters on Ingroup ID
					$('#groupid').bind('keypress', function (event) {
					    var regex = new RegExp("^[a-zA-Z0-9]+$");
					    var key = String.fromCharCode(!event.charCode ? event.which : event.charCode);
					    if (!regex.test(key)) {
					       event.preventDefault();
					       return false;
					    }
					});
				// disable special characters on Ingroup Name
					$('#groupname').bind('keypress', function (event) {
					    var regex = new RegExp("^[a-zA-Z0-9 ]+$");
					    var key = String.fromCharCode(!event.charCode ? event.which : event.charCode);
					    if (!regex.test(key)) {
					       event.preventDefault();
					       return false;
					    }
					});

			/*** IVR ***/
				//add option
					$('.add-option').click(function(){
						var toClone = $('.to-clone-opt').clone();

						toClone.removeClass('to-clone-opt');
						toClone.find('label.control-label').text('');
						toClone.find('.btn-remove').append('<span class="fa fa-remove fa-2x text-red remove-row"></span>');

						$('.cloning-area').append(toClone);
					});

				//remove option
					$(document).on('click', '.remove-row', function(){
						var row = $(this).parent().parent();
						
						row.remove();
					});

			/*** DID ***/
				// disable special characters on DID Exten
					$('#did_exten').bind('keypress', function (event) {
					    var regex = new RegExp("^[a-zA-Z0-9]+$");
					    var key = String.fromCharCode(!event.charCode ? event.which : event.charCode);
					    if (!regex.test(key)) {
					       event.preventDefault();
					       return false;
					    }
					});
				// disable special characters on DID Desc
					$('#desc').bind('keypress', function (event) {
					    var regex = new RegExp("^[a-zA-Z0-9 ]+$");
					    var key = String.fromCharCode(!event.charCode ? event.which : event.charCode);
					    if (!regex.test(key)) {
					       event.preventDefault();
					       return false;
					    }
					});

				//route change
					$('#route').on('change', function() {
						if(this.value == "AGENT") {
						  $('#form_route_agent').show();
						  
						  $('#form_route_ingroup').hide();
						  $('#form_route_phone').hide();
						  $('#form_route_callmenu').hide();
						  $('#form_route_voicemail').hide();
						  $('#form_route_exten').hide();
						}if(this.value == "IN_GROUP") {
						  $('#form_route_ingroup').show();
						  
						  $('#form_route_agent').hide();
						  $('#form_route_phone').hide();
						  $('#form_route_callmenu').hide();
						  $('#form_route_voicemail').hide();
						  $('#form_route_exten').hide();
						}if(this.value == "PHONE") {
						  $('#form_route_phone').show();
						  
						  $('#form_route_agent').hide();
						  $('#form_route_ingroup').hide();
						  $('#form_route_callmenu').hide();
						  $('#form_route_voicemail').hide();
						  $('#form_route_exten').hide();
						}if(this.value == "CALLMENU") {
						  $('#form_route_callmenu').show();
						  
						  $('#form_route_agent').hide();
						  $('#form_route_ingroup').hide();
						  $('#form_route_phone').hide();
						  $('#form_route_voicemail').hide();
						  $('#form_route_exten').hide();
						}if(this.value == "VOICEMAIL") {
						  $('#form_route_voicemail').show();
						  
						  $('#form_route_agent').hide();
						  $('#form_route_ingroup').hide();
						  $('#form_route_phone').hide();
						  $('#form_route_callmenu').hide();
						  $('#form_route_exten').hide();
						}if(this.value == "EXTEN") {
						  $('#form_route_exten').show();
						  
						  $('#form_route_agent').hide();
						  $('#form_route_ingroup').hide();
						  $('#form_route_phone').hide();
						  $('#form_route_voicemail').hide();
						  $('#form_route_callmenu').hide();
						}
						
					});	 

	});
</script>

		<?php print $ui->creamyFooter(); ?>
    </body>
</html>
