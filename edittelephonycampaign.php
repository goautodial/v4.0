<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once('./php/CRMDefaults.php');
require_once('./php/UIHandler.php');
//require_once('./php/DbHandler.php');
require_once('./php/LanguageHandler.php');
require('./php/Session.php');
require_once('./php/goCRMAPISettings.php');

// initialize structures
$ui = \creamy\UIHandler::getInstance();
$lh = \creamy\LanguageHandler::getInstance();
$user = \creamy\CreamyUser::currentUser();

$campaign_id = NULL;
if (isset($_POST["campaign"])) {
	$campaign_id = $_POST["campaign"];
}

$did = NULL;
if (isset($_POST["disposition_id"])) {
	$did = $_POST["disposition_id"];
}

$lf_id = NULL;
if (isset($_POST["leadfilter"])) {
	$lf_id = $_POST["leadfilter"];
}

/*
 * APIs for forms
 */ 
$campaign = $ui->API_getCampaignInfo($campaign_id);
$disposition = $ui->API_getDispositionInfo($did);

?>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Goautodial Edit 
        	<?php 
        		if($campaign_id != NULL){echo "Campaign";}
        		if($did != NULL){echo "Disposition";}
        		if($lf_id != NULL){echo "Lead Filter";}
        	?>
        </title>
        <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
        <link href="css/bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="css/font-awesome.min.css" rel="stylesheet" type="text/css" />
        <!-- Ionicons -->
        <link href="css/ionicons.min.css" rel="stylesheet" type="text/css" />
        <!-- bootstrap wysihtml5 - text editor -->
        <link href="css/bootstrap-wysihtml5/bootstrap3-wysihtml5.min.css" rel="stylesheet" type="text/css" />
        <!-- Creamy style -->
        <link href="css/creamycrm.css" rel="stylesheet" type="text/css" />
        <?php print $ui->creamyThemeCSS(); ?>
        <!-- iCheck for checkboxes and radio inputs -->
  		<link rel="stylesheet" href="css/iCheck/all.css">
        <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
          <script src="js/html5shiv.js"></script>
          <script src="js/respond.min.js"></script>
        <![endif]-->
        <script src="js/jquery.min.js"></script>
        <script src="js/bootstrap.min.js" type="text/javascript"></script>
        <script src="js/jquery-ui.min.js" type="text/javascript"></script>
		<!-- Forms and actions -->
		<script src="js/jquery.validate.min.js" type="text/javascript"></script>
        <!-- Creamy App -->
        <script src="js/app.min.js" type="text/javascript"></script>

        <!-- preloader -->
        <link rel="stylesheet" href="css/customizedLoader.css">

        <script type="text/javascript">
			$(window).ready(function() {
				$(".preloader").fadeOut("slow");
			})
		</script>
    </head>
    <style>
    	select{
    		font-weight: normal;
    	}
    </style>
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
                        <small>Edit 
                        	<?php 
				        		if($campaign_id != NULL){echo "Campaign";}
				        		if($did != NULL){echo "Disposition";}
				        		if($leadfilter != NULL){echo "Lead Filter";}
					        ?>
					    </small>
                    </h1>
                    <ol class="breadcrumb">
                        <li><a href="./index.php"><i class="fa fa-edit"></i> <?php $lh->translateText("home"); ?></a></li>
                        <li> <?php $lh->translateText("telephony"); ?></li>
                        <?php
							if($campaign_id != NULL || $did != NULL || $lf_id != NULL){
						?>	
							<li><a href="./telephonycampaigns.php"><?php $lh->translateText("Campaign"); ?></a></li>
                        <?php
							}
                        ?>	                    
                        <li class="active"><?php $lh->translateText("modify"); ?></li>
                    </ol>
                </section>

                <!-- Main content -->
                <section class="content">
					<div class="box box-info">
						<div class="box-header with-border">
							<h3 class="box-title">
								<?php 
					        		if($campaign_id != NULL){echo "Campaign";}
					        		if($did != NULL){
					        	?>
					        		CUSTOM STATUSES WITHIN THIS CAMPAIGN: <?php echo $did;?>
					        	<?php
					        		}
					        		if($lf_id != NULL){echo "Lead Filter";}
					        	?>
							</h3>
						</div>
						<!-- /.box-header -->
						<form class="form-horizontal">
							<?php 
							
							$errormessage = NULL; 

					// ---- IF CAMPAIGN
							if($campaign_id != NULL) {
								if ($campaign->result=="success") {
									for($i=0;$i < count($campaign->campaign_id);$i++){
										echo $campaign->campaign_id[$i];
									} 
								} else { 
								 	echo $campaign->result; 
								}
							}
							
					// ---- IF DID
							if($did != NULL){
								//var_dump($disposition->result);
								
								//var_dump($did);
								if ($disposition->result == "success") {
							?>
					          
					            <!-- /.box-header -->
					            <div class="box-body table-responsive no-padding">
					              <table class="table table-hover">
					              	<thead>
					              		<style>
					              		.custom_statuses{
					              			font-size: 14px;
										    text-align: center;
										    padding: 0px 20px;
										}
										td {
											text-align: center;
										}
					              		</style>
					                <tr>
					                	<th> STATUS </th>
										<th> STATUS NAME </th>
										<th class="custom_statuses"> Selectable </th>
										<th class="custom_statuses"> Human Answered </th>
										<th class="custom_statuses"> Sale </th>
										<th class="custom_statuses"> DNC </th>
										<th class="custom_statuses"> Customer Contact </th>
										<th class="custom_statuses"> Not Interested </th>
										<th class="custom_statuses"> Unworkable </th>
										<th class="custom_statuses"> Scheduled Callback </th>
					                </tr>
					            	</thead>
					                <tbody>
								<?php
										for($i=0;$i < count($disposition->campaign_id);$i++){
								?>
									<tr>
										<td>
											<input type="text" name="status" id="status" maxlength="6" class="" value="<?php echo $disposition->status[$i];?>" placeholder="Status">
										</td>
										<td>
											<input type="text" name="status_name" id="status_name" class="" value="<?php echo $disposition->status_name[$i];?>" placeholder="Status Name">
										</td>
										<td>
											<input type="checkbox" name="selectable" id="selectable" class="flat-red" <?php if($disposition->selectable[$i] == "Y"){echo checked;}?> />
										</td>
										<td>
											<input type="checkbox" name="human_answered" id="human_answered" class="flat-red" <?php if($disposition->human_answered[$i] == "Y"){echo checked;}?> />
										</td>
										<td>
											<input type="checkbox" name="sale" id="sale" class="flat-red" <?php if($disposition->sale[$i] == "Y"){echo checked;}?> />
										</td>
										<td>
											<input type="checkbox" name="dnc" id="dnc" class="flat-red" <?php if($disposition->dnc[$i] == "Y"){echo checked;}?> />
										</td>
										<td>
											<input type="checkbox" name="customer_contact" id="customer_contact" class="flat-red" <?php if($disposition->customer_contact[$i] == "Y"){echo checked;}?> />
										</td>
										<td>
											<input type="checkbox" name="not_interested" id="not_interested" class="flat-red" <?php if($disposition->not_interested[$i] == "Y"){echo checked;}?> />
										</td>
										<td>
											<input type="checkbox" name="unworkable" id="unworkable" class="flat-red" <?php if($disposition->unworkable[$i] == "Y"){echo checked;}?> />
										</td>
										<td>
											<input type="checkbox" name="scheduled_callback" id="scheduled_callback" class="flat-red" <?php if($disposition->scheduled_callback[$i] == "Y"){echo checked;}?> />
										</td>
									</tr>
								<?php
										}
								?>
								<!-- ADD A NEW STATUS -->
									<tr><td colspan="10"><a type="button" id="add_new_status" data-id="<?php echo $did;?>" class="btn btn-primary">Add New Status</a></td></tr>
									<tr style="border-top: 1px solid #f4f4f4; background-color: #f9f9f9;">
										<td>
											<input type="text" name="add_status" id="add_status" class="" placeholder="Status">
										</td>
										<td>
											<input type="text" name="add_status_name" id="add_status_name" class="" placeholder="Status Name">
										</td>
										<td>
											<input type="checkbox" name="add_selectable" id="add_selectable" class="flat-red" value="Y" checked />
										</td>
										<td>
											<input type="checkbox" name="add_human_answered" id="add_human_answered" class="flat-red" value="Y" />
										</td>
										<td>
											<input type="checkbox" name="add_sale" id="add_sale" class="flat-red" value="Y" />
										</td>
										<td>
											<input type="checkbox" name="add_dnc" id="add_dnc" class="flat-red" value="Y" />
										</td>
										<td>
											<input type="checkbox" name="add_customer_contact" id="add_customer_contact" class="flat-red" value="Y" />
										</td>
										<td>
											<input type="checkbox" name="add_not_interested" id="add_not_interested" class="flat-red" value="Y" />
										</td>
										<td>
											<input type="checkbox" name="add_unworkable" id="add_unworkable" class="flat-red" value="Y" />
										</td>
										<td>
											<input type="checkbox" name="add_scheduled_callback" id="add_scheduled_callback" class="flat-red" value="Y" />
										</td>
									</tr>
								<!------>

									</tbody>
					              </table>
					            </div>

					            <!-- NOTIFICATIONS -->
				                <div id="notifications">
				                    <div class="output-message-success" style="display:none;">
				                        <div class="alert alert-success alert-dismissible" role="alert">
				                          <strong>Success!</strong> New Disposition added !
				                        </div>
				                    </div>
				                    <div class="output-message-error" style="display:none;">
				                        <div class="alert alert-danger alert-dismissible" role="alert">
				                          <span id="disposition_result"></span>
				                        </div>
				                    </div>
				                    <div class="output-message-incomplete" style="display:none;">
				                        <div class="alert alert-danger alert-dismissible" role="alert">
				                          Please do not leave <u>status</u> and <u>status name blank</u>.
				                        </div>
				                    </div>
				                </div>


					            <div class="box-footer">
									<a type="button" id="" class="btn">Cancel</a>
									<button type="submit" class="btn btn-warning pull-right">Modify</button>
								</div>
								<!-- /.box-footer -->
							<?php
								} else { 
								echo $disposition->result; 
								}
								
							}
							/*
					// ---- IF LEADFILTER
							if($lf_id != NULL){
								echo "Under Construction";
							}else { 
									echo $errormessage = $lh->translationFor("some_fields_missing");
							} 
								*/
							?>
						
						
						</form>

					</div>			
                </section>
				<!-- /.content -->
            </aside><!-- /.right-side -->
			
            <?php print $ui->creamyFooter(); ?>
			
        </div><!-- ./wrapper -->

		<!-- Modal Dialogs -->
		<?php include_once "./php/ModalPasswordDialogs.php" ?>

		<!-- SLIMSCROLL-->
    	<script src="theme_dashboard/js/slimScroll/jquery.slimscroll.min.js"></script>
    	<!-- iCheck 1.0.1 -->
		<script src="js/plugins/iCheck/icheck.min.js"></script>

		<script type="text/javascript">
			$(document).ready(function() {
				//Flat red color scheme for iCheck
			    $('input[type="checkbox"].flat-red, input[type="radio"].flat-red').iCheck({
			      checkboxClass: 'icheckbox_flat-green',
			      radioClass: 'iradio_flat-green'
			    });

			   //Add Status
		        $('#add_new_status').click(function(){
		        
		        var validate = 0;
		        var status = $("#add_status").val();
		        var status_name = $("#add_status_name").val();
		        
		        if(status == ""){
		            validate = 1;
		        }

		        if(status_name == ""){
		            validate = 1;
		        }
		       
		            if(validate == 0){
		            		var selectable = "Y";
		            	if(!$('#add_selectable').is(":checked")){
		            		selectable = "N"
		            	}
		            		var human_answered = "Y";
		            	if(!$('#add_human_answered').is(":checked")){
		            		human_answered = "N";
		            	}
		            		var sale = "Y";
		            	if(!$('#add_sale').is(":checked")){
		            		sale = "N";
		            	}
		            		var dnc = "Y";
		            	if(!$('#add_dnc').is(":checked")){
		            		dnc = "N";
		            	}
		            		var scheduled_callback = "Y";
		            	if(!$('#add_scheduled_callback').is(":checked")){
		            		scheduled_callback = "N";
		            	}
		            		var customer_contact = "Y";
		            	if(!$('#add_customer_contact').is(":checked")){
		            		customer_contact = "N";
		            	}
		            		var not_interested = "Y";
		            	if(!$('#add_not_interested').is(":checked")){
		            		not_interested = "N";
		            	}
		            		var unworkable = "Y"
		            	if(!$('#add_unworkable').is(":checked")){
		            		unworkable = "N";
		            	}
		                $.ajax({
		                    url: "./php/AddDisposition.php",
		                    type: 'POST',
		                    data: {
		                    	campaign : $(this).attr('data-id'),
		                    	status : $('#add_status').val(),
					    		status_name : $('#add_status_name').val(),
					   			selectable : selectable,
					    		human_answered : human_answered,
					    		sale : sale,
					    		dnc : dnc,
					    		scheduled_callback : scheduled_callback,
					    		customer_contact : customer_contact,
					    		not_interested : not_interested,
					    		unworkable : unworkable,
		                    },
		                    success: function(data) {
		                      // console.log(data);
		                          if(data == 1){
		                                $('.output-message-success').show().focus().delay(5000).fadeOut().queue(function(n){$(this).hide(); n();});
		                                window.setTimeout(function(){location.reload()},1000)
		                          }
		                          else{
		                              $('.output-message-error').show().focus().delay(5000).fadeOut().queue(function(n){$(this).hide(); n();});
		                              $("#disposition_result").html(data); 
		                          }
		                    }
		                });
		            	
		            }else{
		                $('.output-message-incomplete').show().focus().delay(5000).fadeOut().queue(function(n){$(this).hide(); n();});
		                validate = 0;
		            }
		        });

				/* 
				 * Modifies 
			 	 */
				//CAMPAIGN
				$("#modifycampaign").validate({
                	submitHandler: function() {
						//submit the form
							$("#resultmessage").html();
							$("#resultmessage").fadeOut();
							$.post("./php/ModifyTelephonyCampaign.php", //post
							$("#modifycampaign").serialize(), 
								function(data){
									//if message is sent
									if (data == '<?php print CRM_DEFAULT_SUCCESS_RESPONSE; ?>') {
									<?php 
										$errorMsg = $ui->dismissableAlertWithMessage($lh->translationFor("data_successfully_modified"), true, false);
										print $ui->fadingInMessageJS($errorMsg, "modifyCAMPAIGNresult"); 
									?>				
									} else {
									<?php 
										$errorMsg = $ui->dismissableAlertWithMessage($lh->translationFor("error_modifying_data<br/>"), false, true);
										print $ui->fadingInMessageJS($errorMsg, "modifyCAMPAIGNresult");
									?>
									}
									//
								});
						return false; //don't let the form refresh the page...
					}					
				});
				//LEADFILTER
				$("#modifyleadfilter").validate({
                	submitHandler: function() {
						//submit the form
							$("#resultmessage").html();
							$("#resultmessage").fadeOut();
							$.post("./php/ModifyTelephonyCampaign.php", //post
							$("#modifyleadfilter").serialize(), 
								function(data){
									//if message is sent
									if (data == '<?php print CRM_DEFAULT_SUCCESS_RESPONSE; ?>') {
									<?php 
										$errorMsg = $ui->dismissableAlertWithMessage($lh->translationFor("data_successfully_modified"), true, false);
										print $ui->fadingInMessageJS($errorMsg, "modifyLEADFILTERresult"); 
									?>				
									} else {
									<?php 
										$errorMsg = $ui->dismissableAlertWithMessage($lh->translationFor("error_modifying_data<br/>"), false, true);
										print $ui->fadingInMessageJS($errorMsg, "modifyLEADFILTERresult");
									?>
									}
									//
								});
						return false; //don't let the form refresh the page...
					}					
				});
				
				/**
				 * Delete
				 */

				//delete_campaign
				$("#modifyCAMPAIGNDeleteButton").click(function (e) {
					var r = confirm("<?php $lh->translateText("are_you_sure"); ?>");
					e.preventDefault();
					if (r == true) {
						var campaign = $(this).attr('href');
						$.post("./php/DeleteTelephonyCampaign.php", { campaign: campaign } ,function(data){
							if (data == "<?php print CRM_DEFAULT_SUCCESS_RESPONSE; ?>") { 
								alert("<?php $lh->translateText("campaign_successfully_deleted"); ?>");
								window.location = "index.php";
							}
							else { alert ("<?php $lh->translateText("unable_delete_campaign"); ?>: "+data); }
						});
					}
				 });

				//delete_leadfilter
				  $("#modifyLEADFILTERDeleteButton").click(function (e) {
					var r = confirm("<?php $lh->translateText("are_you_sure"); ?>");
					e.preventDefault();
					if (r == true) {
						var leadfilter = $(this).attr('href');
						$.post("./php/DeleteTelephonyCampaign.php", { leadfilter: leadfilter } ,function(data){
							if (data == "<?php print CRM_DEFAULT_SUCCESS_RESPONSE; ?>") { 
								alert("<?php $lh->translateText("leadfilter_successfully_deleted"); ?>");
								window.location = "index.php";
							}
							else { alert ("<?php $lh->translateText("unable_delete_leadfilter"); ?>: "+data); }
						});
					}
				 });

			});
		</script>


    </body>
</html>

?>