<?php
/*ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);*/
	/**
		The MIT License (MIT)
		
		Copyright (c) 2015 Ignacio Nieto Carvajal
		
		Permission is hereby granted, free of charge, to any person obtaining a copy
		of this software and associated documentation files (the "Software"), to deal
		in the Software without restriction, including without limitation the rights
		to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
		copies of the Software, and to permit persons to whom the Software is
		furnished to do so, subject to the following conditions:
		
		The above copyright notice and this permission notice shall be included in
		all copies or substantial portions of the Software.
		
		THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
		IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
		FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
		AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
		LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
		OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
		THE SOFTWARE.
	*/
	
	require_once ('./php/CRMDefaults.php');
	require_once ('./php/UIHandler.php');
	require_once ('./php/LanguageHandler.php');
    include ('./php/Session.php');

    $ui = \creamy\UIHandler::getInstance();
	$lh = \creamy\LanguageHandler::getInstance();
	$user = \creamy\CreamyUser::currentUser();

?>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Creamy</title>
        <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
        <link href="css/bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="css/font-awesome.min.css" rel="stylesheet" type="text/css" />
        <!-- Ionicons -->
        <link href="css/ionicons.min.css" rel="stylesheet" type="text/css" />
	    <!-- iCheck for checkboxes and radio inputs -->
	    <link href="css/iCheck/minimal/blue.css" rel="stylesheet" type="text/css" />
        <!-- DATA TABLES -->
        <link href="css/datatables/dataTables.bootstrap.css" rel="stylesheet" type="text/css" />
        <!-- Creamy style -->
        <link href="css/creamycrm.css" rel="stylesheet" type="text/css" />
        <?php print $ui->creamyThemeCSS(); ?>

        <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
          <script src="js/html5shiv.js"></script>
          <script src="js/respond.min.js"></script>
        <![endif]-->
        <script src="js/jquery.min.js"></script>
        <script src="js/bootstrap.min.js" type="text/javascript"></script>
        <!-- DATA TABES SCRIPT -->
        <script src="./js/plugins/datatables/jquery.dataTables.js" type="text/javascript"></script>
        <script src="./js/plugins/datatables/dataTables.bootstrap.js" type="text/javascript"></script>
	    <!-- iCheck -->
	    <script src="js/plugins/iCheck/icheck.min.js" type="text/javascript"></script>
        <!-- JQuery Validate -->
        <script src ="js/jquery.validate.min.js" type="text/javascript"></script>
		<!-- Input mask for date textfields -->
		<script src="js/plugins/input-mask/jquery.inputmask.js" type="text/javascript"></script>
		<script src="js/plugins/input-mask/jquery.inputmask.date.extensions.js" type="text/javascript"></script>
		<script src="js/plugins/input-mask/jquery.inputmask.extensions.js" type="text/javascript"></script>
        <!-- Creamy App -->
        <script src="js/app.min.js" type="text/javascript"></script>
		<!-- Data Tables -->
        <script src="js/plugins/datatables/jquery.dataTables.js" type="text/javascript"></script>
        <script src="js/plugins/datatables/dataTables.bootstrap.js" type="text/javascript"></script>

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
        	<div class="fakeloader"></div>
        <!-- header logo: style can be found in header.less -->
		<?php print $ui->creamyHeader($user); ?>
			<?php print $ui->getSidebar($user->getUserId(), $user->getUserName(), $user->getUserRole(), $user->getUserAvatar()); ?>

            <!-- Right side column. Contains the navbar and content of the page -->
            <aside class="right-side" style="min-height: 100%;">
                <!-- Content Header (Page header) -->
                <section class="content-header">
                    <h1>
                        Contacts
                        <small><?php $lh->translateText("Contacts"); ?></small>
                    </h1>
                    <ol class="breadcrumb">
                        <li><a href="./index.php"><i class="fa fa-users"></i> <?php $lh->translateText("home"); ?></a></li>
                        <li class="active">Contacts</li>
                    </ol>
                </section>

                <!-- Main content -->
                <section class="content">
	                <!-- check permissions -->

	                <?php 
                    	$list = $ui->getAllowedList($user->getUserId());
                    	// print_r($list);
                    ?>
	                <?php if ($user->userHasBasicPermission()) { ?>
                        <div class="box box-primary">
                            <div class="box-header">
								
                            </div><!-- /.box-header -->
							
                           <div class="box-body">	
                           		<form class="form-horizontal clearfix" method="POST" action="searchleads.php">
                           			<input type="hidden" name="lists" value="<?php echo $list; ?>">
                           			<div class="fom-group">
                           				<label class="control-label col-lg-2">Last Name:</label>
                           				<div class="col-lg-3">
                           					<input type="text" class="form-control last-name" name="last_name" required>
                           				</div>
                           				<label class="control-label col-lg-2">Phone Number:</label>
                           				<div class="col-lg-3">
                           					<input type="text" class="form-control phone-number" name="phone_number">
                           				</div>
                           				<div class="col-lg-2">
                           					<button type="submit" class="btn btn-primary">Search</button>
                           				</div>
                           			</div>
                           		</form>
                           </div><!-- /.box-body -->
                        </div><!-- /.box -->
                        <style>
                        	.letter div{
                        		padding: 20px;
                        		margin: 2px;
                        		border-radius: 8px;
                        		font-size: 15px;
                        	}
                        </style>
                        <div class="box">
                           <div class="box-body">	
                           		<?php $alphabet = range('A', 'Z'); ?>
                           		<div class="row">
	                           		<?php foreach($alphabet as $letter){ ?>
	                           		<a href="#">
	                           			<div class="col-lg-2 text-center letter">
		                           			<div class="btn btn-success letter-select" data-letter="<?php echo $letter; ?>" style="width: 100%"><b><?php echo $letter; ?></b></div>
	                           			</div>
	                           		</a>
	                           		<?php } ?>
                           		</div>
                           </div><!-- /.box-body -->
                        </div><!-- /.box -->
                    <?php } ?>

	                <?php //if ($user->userHasBasicPermission()) { ?>
                    <div class="row">
                        <div class="col-xs-12">
                            <div class="box box-default">
                                <div class="box-header">
									
                                </div><!-- /.box-header -->
								
                               <div class="box-body table">	
									
									<?php

									//var_dump($user->getUserName());

									$output = $ui->API_GetLeads();
									//var_dump($output);

										if ($output->result=="success") {
										# Result was OK!
											echo $ui->GetContacts($_SESSION['user']);
										} else {
										   # An error occured
											echo $output->result;
										}
									?>
                                </div><!-- /.box-body -->
					<?php //} ?>
                            
                            </div><!-- /.box -->
                        </div>
                    </div>

                    <!-- user not authorized -->
					<?php 
					//if ($user->userHasWritePermission()) { ?>
					<?php //} else { print $ui->getUnauthotizedAccessMessage(); } ?>
                </section><!-- /.content -->
            </aside><!-- /.right-side -->
			<?php print $ui->getRightSidebar($user->getUserId(), $user->getUserName(), $user->getUserAvatar()); ?>
        </div><!-- ./wrapper -->

<!-- View Modal -->
	<div id="view-contact-modal" class="modal fade" role="dialog">
	  <div class="modal-dialog">

	    <!-- Modal content-->
	    <div class="modal-content">
	      <div class="modal-header">
	        <button type="button" class="close" data-dismiss="modal">&times;</button>
	        <h4 class="modal-title"><b>Contact/Lead Information</b>&nbsp;<span class="badge label-info"><span class="fa fa-info"></span></span></h4>
	      </div>
	      <div class="modal-body">
	      	<div class="output-message-no-result hide">
		      	<div class="alert alert-warning alert-dismissible" role="alert">
				  <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				  <strong>Notice!</strong> There was an error retrieving " style="font-weight: normal;padding-left: 20px;". Either error or no result.
				</div>
			</div>
	        <div id="content" class="view-form hide">
			    <div class="form-horizontal">
			    	<div class="row">
			    		<label class="control-label col-lg-8">List ID:
			    			<span class="info-list-id"></span>
			    		</label>
			    	</div>
			    	<div class="row">
			    		<label class="control-label col-lg-8">Full Name:
			    			<span class="info-fullname"></span>
			    		</label>
			    	</div>
			    	<div class="row">
			    		<label class="control-label col-lg-8">Phone Number:
			    			<span class="info-phonenumber"></span>
			    		</label>
			    	</div>
			    	<div class="row">
			    		<label class="control-label col-lg-8">Alternative Phone Number:
			    			<span class="info-altphonenumber"></span>
			    		</label>
			    	</div>
			    	<div class="row">
			    		<label class="control-label col-lg-8">Email:
			    			<span class="info-email"></span>
			    		</label>
			    	</div>
			    	<div class="row">
			    		<label class="control-label col-lg-8">Address 1:
			    			<span class="info-address1"></span>
			    		</label>
			    	</div>
			    	<div class="row">
			    		<label class="control-label col-lg-8">Address 2:
			    			<span class="info-address2"></span>
			    		</label>
			    	</div>
			    	<div class="row">
			    		<label class="control-label col-lg-8">Address 3:
			    			<span class="info-address3"></span>
			    		</label>
			    	</div>
			    	<div class="row">
			    		<label class="control-label col-lg-8">City:
			    			<span class="info-city"></span>
			    		</label>
			    	</div>
			    	<div class="row">
			    		<label class="control-label col-lg-8">Province:
			    			<span class="info-province"></span>
			    		</label>
			    	</div>
			    	<div class="row">
			    		<label class="control-label col-lg-8">State:
			    			<span class="info-state"></span>
			    		</label>
			    	</div>
			    	<div class="row">
			    		<label class="control-label col-lg-8">Postal Code:
			    			<span class="info-postalcode"></span>
			    		</label>
			    	</div>
			    	<div class="row">
			    		<label class="control-label col-lg-8">Gender:
			    			<span class="info-gender"></span>
			    		</label>
			    	</div>
			    </div>
			</div>
	      </div>
	      <div class="modal-footer">
	        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
	      </div>
	    </div>
	    <!-- End of modal content -->
	  </div>
	</div>
	<!-- End of modal -->

        <!-- Modal Dialogs -->
		<?php include_once "./php/ModalPasswordDialogs.php" ?>
		<?php 
			// Create new customer form as a modal dialog
			$fields = $ui->customerFieldsForForm(null, null, null);
			// buttons
			$okButton = $ui->buttonWithLink("createCustomerOkButton", "", $lh->translationFor("create"), "submit", "times", CRM_UI_STYLE_DEFAULT, "pull-right");
			$koButton = $ui->modalDismissButton("createCustomerCancelButton", $lh->translationFor("cancel"), "left", true);
			$buttons = $okButton.$koButton;
			
			// form
			$form = $ui->modalFormStructure("create-client-dialog-modal", "newclientform", $lh->translationFor("create_new"), null, $fields, $buttons, "user", "createcustomerresult");
			print $form;
		?>

        <!-- page script -->
        <script type="text/javascript">
			"use strict";
	        var clientCreated = false;

	        // load datatable of customer.
            $(document).ready(function() {
            	$('.letter-select').click(function(e){
            		e.preventDefault();
            		var letter = $(this).attr('data-letter');
            		console.log(letter);
            	});

			    // uncheck individual customer
				$('input[type=checkbox]').on("ifChecked", function(e) {
					if (e.currentTarget.value != 'on') selectedCustomers.push(e.currentTarget.value);
					alert("customers: "+selectedCustomers);
				});
				
				$('#contacts').dataTable();
               /* 
				$("#contacts").dataTable({
	                "bProcessing": true,
	                "bPaginate": true,
					"bServerSide": true,
					"aoColumnDefs": [ { "bSortable": false, "bVisible": true, "aTargets": [ -1 ] } ]
                });
                */
                //Datemask dd/mm/yyyy
			    $("#birthdate").inputmask("dd/mm/yyyy", {"placeholder": "dd/mm/yyyy"});
			
				/**
				 * Create a new customer/contact
				 */
				 $("#newclientform").validate({
				 	rules: {
						name: "required",
			   		},
					submitHandler: function() {
						//submit the form
							$("#createcustomerresult").html();
							$("#createcustomerresult").hide();
							$.post("./php/CreateCustomer.php", //post
							$("#newclientform").serialize(), 
								function(data){
									//if message is sent
									if (data == '<?php print CRM_DEFAULT_SUCCESS_RESPONSE; ?>') {
									<?php 
									$errorMsg = $ui->dismissableAlertWithMessage($lh->translationFor("user_successfully_created"), true, false);
									print $ui->fadingInMessageJS($errorMsg, "createcustomerresult"); 
									?>
									$('#newclientform')[0].reset(); // reset form (except for hidden fields).
									clientCreated = true;
									} else {
									<?php 
									$errorMsg = $ui->dismissableAlertWithMessage($lh->translationFor("error_creating_user"), false, true);
									print $ui->fadingInMessageJS($errorMsg, "createcustomerresult"); 
									?>
									}
									//
								});
						return false; //don't let the form refresh the page...
					}					
				});
				
				/**
				 * Set the elements of the newly created customers.
				 */
				$("#create-customer-trigger-button").click(function (e) {
					e.preventDefault();
					clientCreated = false;
					var customerType = $(this).attr('customer_type');
					$("#new-customer-header-text").html('<i class="fa fa-user"></i> <?php $lh->translateText("create_new"); ?></h4>');
					$("#customer_type").val(customerType);
				});
				
				/**
				 * Reload page when exiting users creation dialog.
				 */
				$("#createCustomerCancelButton").click(function(e) { location.reload(); });
				$('#create-client-dialog-modal').on('hidden.bs.modal', function () { if (clientCreated) { location.reload(); } });


				//VIEW CONTACT INFO
				$('.view-contact').click(function(){
					var cont_id = $(this).attr('data-id');
					var log_user = '<?=$_SESSION['user']?>';
					var log_group = '<?=$_SESSION['usergroup']?>';
					// alert(camp_id);
					$.ajax({
					  url: "./php/ViewContact.php",
					  type: 'POST',
					  data: { 
					  	contact_id :cont_id,
						log_user: log_user,
						log_group: log_group
					  },
					  dataType: 'json',
					  success: function(data) {
					  		// console.log(data);
					  		if(data){
					  			
								$('.output-message-no-result').addClass('hide');
								$('.view-form').removeClass('hide');

								// set info here
								var fullname = data.first_name + " " + data.middle_initial + " " + data.last_name;
								var blank_data = " - - - - - ";

								$('.info-list-id').text(data.list_id);
								$('.info-fullname').text(fullname);
								$('.info-phonenumber').text(data.phone_number);

								if(data.alt_phone == ""){
									$('.info-altphonenumber').text(blank_data);
								}else{
									$('.info-altphonenumber').text(data.alt_phone);
								}

								if(data.email == ""){
									$('.info-email').text(blank_data);
								}else{
									$('.info-email').text(data.email);
								}

								if(data.address1 == ""){
									$('.info-address1').text(blank_data);
								}else{
									$('.info-address1').text(data.address1);
								}

								if(data.address2 == ""){
									$('.info-address2').text(blank_data);
								}else{
									$('.info-address2').text(data.address2);
								}

								if(data.address3 == ""){
									$('.info-address3').text(blank_data);
								}else{
									$('.info-address3').text(data.address3);
								}

								if(data.city == ""){
									$('.info-city').text(blank_data);
								}else{
									$('.info-city').text(data.city);
								}

								if(data.province == ""){
									$('.info-province').text(blank_data);
								}else{
									$('.info-province').text(data.province);
								}

								if(data.state == ""){
									$('.info-state').text(blank_data);
								}else{
									$('.info-state').text(data.state);
								}
								
								if(data.postal_code == ""){
									$('.info-postalcode').text(blank_data);
								}else{
									$('.info-postalcode').text(data.postal_code);
								}

								if(data.gender == ""){
									$('.info-gender').text(blank_data);
								}else{
									$('.info-gender').text(data.gender);
								}

								$('.output-message-no-result').addClass('hide');
								$('.view-form').removeClass('hide');

								$('#view-contact-modal').modal('show');

					  		}else{
								$('.output-message-no-result').removeClass('hide');
								$('.view-form').addClass('hide');
					  		}
					    }
					});
				});

				//EDIT CONTACT
				 $(".edit-contact").click(function(e) {
					e.preventDefault();
					var url = './editcustomer.php';
					var form = $('<form action="' + url + '" method="post"><input type="hidden" name="lead_id" value="' + $(this).attr('href') + '" /></form>');
					$('body').append(form);  // This line is not necessary
					$(form).submit();
				 });

				//DELETE CONTACT
				 $(".delete-contact").click(function(e) {
					var r = confirm("<?php $lh->translateText("are_you_sure"); ?>");
					var log_user = '<?=$_SESSION['user']?>';
					var log_group = '<?=$_SESSION['usergroup']?>';
					e.preventDefault();
					if (r === true) {
						var leadid = $(this).attr('href');
						$.post("./php/DeleteContact.php", { leadid: leadid, log_user: log_user, log_group: log_group } ,function(data){
							if (data == "<?php print CRM_DEFAULT_SUCCESS_RESPONSE; ?>") { location.reload(); }
							else { alert ("<?php $lh->translateText("unable_delete_list"); ?>"); }
						});
					}
				 });


			});
			
            // function to create an event associated with a customer.
            function createEventForCustomer(customerId, customerType) {
				$.post("./php/CreateEvent.php", { "customerid": customerId, "customer_type": customerType }, function(data){
					if (data != '0') { window.location.href = "events.php" }
					else { alert(data); }
				});
            }
            // function to change a customer type
            function changeCustomerType(customerId, oldCustomerType, newCustomerType) {
				$.post("./php/ChangeCustomerType.php", 
				{ "customerid": customerId, "old_customer_type": oldCustomerType, "new_customer_type": newCustomerType }, 
				function(data){
					if (data == '<?php print CRM_DEFAULT_SUCCESS_RESPONSE; ?>') { location.reload(); }
					else { alert(data); }
				});
            }
        </script>

    </body>
</html>
