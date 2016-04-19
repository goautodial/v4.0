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
        <title>Goautodial</title>
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
        <?php print $ui->creamyThemeCSS(); ?>

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

        <!-- Creamy App -->
        <script src="js/app.min.js" type="text/javascript"></script>
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
                        <small><?php $lh->translateText("campaign_management"); ?></small>
                    </h1>
                    <ol class="breadcrumb">
                        <li><a href="./index.php"><i class="fa fa-phone"></i> <?php $lh->translateText("home"); ?></a></li>
                        <li><?php $lh->translateText("telephony"); ?></li>
						<li class="active"><?php $lh->translateText("campaign_management"); ?>
                    </ol>
                </section>

                <!-- Main content -->
                <section class="content">
                <?php if ($user->userHasAdminPermission()) { ?>
				
				<!-- TAB CONTENT CONTROLLER -->
				<ul class="nav nav-tabs" role="tablist"> 
					<li role="presentation" class="active"><a data-toggle="tab" aria-controls="T_campaigns" role="tab" href="#T_campaigns">Campaigns</a></li>
					<li role="presentation"><a data-toggle="tab" aria-controls="T_disposition" role="tab" href="#T_disposition">Dispositions</a></li>
					<li role="presentation"><a data-toggle="tab" aria-controls="T_LeadFilters" role="tab" href="#T_LeadFilters">Lead Filters</a></li>
				</ul><!-- END OF CONTROLLER -->
				
                    <div class="row">
                        <div class="col-xs-12">
                            <div class="box box-default">
                                <div class="box-body">
							
							<!-- TAB CONTENT -->
								<div class="tab-content">
										
										<?php echo $ui->getListAllCampaigns(); ?>
										
										<?php echo $ui->getAllDispositions(); ?>
										
										<?php echo $ui->getLeadFilters(); ?>

								</div><!-- end of tab content -->
									
                                </div><!-- /.box-body -->
                            </div><!-- /.box -->
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
		<?php print $ui->getCircleButton("calls", "plus"); ?>
	</div>
	<!-- Modal -->
	<div id="wizard-modal" class="modal fade" role="dialog">
	  <div class="modal-dialog">

	    <!-- Modal content-->
	    <div class="modal-content">
	      <div class="modal-header">
	        <button type="button" class="close" data-dismiss="modal">&times;</button>
	        <h4 class="modal-title"><b>Campaign Wizard >> <span class="wizard-type">Outbound</span></b></h4>
	      </div>
	      <div class="modal-body">
	      	<div class="output-message-success hide">
		      	<div class="alert alert-success alert-dismissible" role="alert">
				  <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				  <strong>Success!</strong> New Campaign saved.
				</div>
			</div>
			<div class="output-message-error hide">
				<div class="alert alert-danger alert-dismissible" role="alert">
				  <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				  <strong>Error!</strong> Something went wrong please see input data on form or campaign already exist.
				</div>
			</div>
	        <div id="content" class="wizard-form">
			    <?php print $ui->wizardFromCampaign(); ?>
			</div>
	      </div>
	      <div class="modal-footer">
	        <button type="button" class="btn btn-default" id="add-campaign-btn">Save</button>
	        <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
	      </div>
	    </div>
	    <!-- End of modal content -->
	  </div>
	</div>
	<!-- End of modal -->

	<!-- Modal -->
	<div id="view-campaign-modal" class="modal fade" role="dialog">
	  <div class="modal-dialog">

	    <!-- Modal content-->
	    <div class="modal-content">
	      <div class="modal-header">
	        <button type="button" class="close" data-dismiss="modal">&times;</button>
	        <h4 class="modal-title"><b>Campaign Information</b>&nbsp;<span class="badge label-info"><span class="fa fa-info"></span></span></h4>
	      </div>
	      <div class="modal-body">
	      	<div class="output-message-no-result hide">
		      	<div class="alert alert-warning alert-dismissible" role="alert">
				  <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				  <strong>Notice!</strong> There was an error retrieving details. Either error or no result.
				</div>
			</div>
	        <div id="content" class="view-form hide">
			    <div class="form-horizontal">
			    	<div class="form-group">
			    		<label class="control-label col-lg-5">Campaign ID:</label>
			    		<span class="info-camp-id control-label align-left col-lg-7"></span>
			    	</div>
			    	<div class="form-group">
			    		<label class="control-label col-lg-5">Campaign Name:</label>
			    		<span class="info-camp-name control-label align-left col-lg-7"></span>
			    	</div>
			    	<div class="form-group">
			    		<label class="control-label col-lg-5">Campaign Description:</label>
			    		<span class="info-camp-desc control-label align-left col-lg-7"></span>
			    	</div>
			    	<div class="form-group">
			    		<label class="control-label col-lg-5">Allowed Inbound and Blended:</label>
			    		<span class="info-allowed control-label align-left col-lg-7"></span>
			    	</div>
			    	<div class="form-group">
			    		<label class="control-label col-lg-5">Dial Method:</label>
			    		<span class="info-dial-method control-label align-left col-lg-7"></span>
			    	</div>
			    	<div class="form-group">
			    		<label class="control-label col-lg-5">AutoDial Level:</label>
			    		<span class="info-autodial-level control-label align-left col-lg-7"></span>
			    	</div>
			    	<div class="form-group">
			    		<label class="control-label col-lg-5">Answering Machine Detection:</label>
			    		<span class="info-ans-mach control-label align-left col-lg-7"></span>
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

	<!-- Modal -->
	<div id="confirmation-delete-modal" class="modal fade" role="dialog">
	  <div class="modal-dialog">

	    <!-- Modal content-->
	    <div class="modal-content">
	      <div class="modal-header">
	        <button type="button" class="close" data-dismiss="modal">&times;</button>
	        <h4 class="modal-title"><b>Confirmation Box</b></h4>
	      </div>
	      <div class="modal-body">
	      	<p>Are you sure you want to delete Campaign ID: <span class="camp-id-delete-label" data-id=""></span></p>
	      </div>
	      <div class="modal-footer">
	        <button type="button" class="btn btn-default" id="delete-campaign-btn" data-id="">Yes</button>
	        <button type="button" class="btn btn-default" data-dismiss="modal">No</button>
	      </div>
	    </div>
	    <!-- End of modal content -->
	  </div>
	</div>
	<!-- End of modal -->

	<!-- Script for wizard -->
	<script type="text/javascript">
		$(document).ready(function(){
			//$('#campaigns').dataTable();
			// $('#wizard-modal').modal('show');
			// $('#view-campaign-modal').modal('show');
		
		 
		 
			//$('#T_disposition').DataTable();

			
			
			$('#campaign-id-edit-btn').click(function(){
				$('.campaign-id').find('input[name="campaign_id"]').prop('readonly',function(i,r){
			        return !r;
			    });
			});

			$('.lead-section').removeClass('hide');
			$('#campaignType').change(function(){
				var selectedTypeText = $(this).find("option:selected").text();
				var selectedTypeVal = $(this).find("option:selected").val();
				$('.wizard-type').text(selectedTypeText);

				if(selectedTypeVal == 'inbound' || selectedTypeVal == 'blended'){
					$('.did-tfn-ext').removeClass('hide');
					$('.call-route').removeClass('hide');
					$('.surver-type').addClass('hide');
					$('.no-channels').addClass('hide');
					$('.copy-from').addClass('hide');
					$('.upload-wav').addClass('hide');

					if(selectedTypeVal == 'inbound'){
						$('.lead-section').addClass('hide');
					}else{	
						$('.lead-section').removeClass('hide');
					}

				}else if(selectedTypeVal == 'survey'){
					$('.did-tfn-ext').addClass('hide');
					$('.call-route').addClass('hide');
					$('.surver-type').removeClass('hide');
					$('.no-channels').removeClass('hide');
					$('.copy-from').addClass('hide');
					$('.upload-wav').removeClass('hide');
					$('.lead-section').removeClass('hide');
				}else if(selectedTypeVal == 'copy'){
					$('.did-tfn-ext').addClass('hide');
					$('.call-route').addClass('hide');
					$('.surver-type').addClass('hide');
					$('.no-channels').addClass('hide');
					$('.copy-from').removeClass('hide');
					$('.upload-wav').addClass('hide');
					$('.lead-section').addClass('hide');
				}else{
					// default
					$('.did-tfn-ext').addClass('hide');
					$('.call-route').addClass('hide');
					$('.surver-type').addClass('hide');
					$('.no-channels').addClass('hide');
					$('.copy-from').addClass('hide');
					$('.upload-wav').addClass('hide');
					$('.lead-section').removeClass('hide');
				}
			});

			$('#add-campaign-btn').click(function(){
				$.ajax({
				  /*url: ".\php\AddCampaign.php",*/
				  url: "./php/AddCampaign.php",
				  type: 'POST',
				  data: { 
				  	campaign_type : $('#campaignType').val(),
				  	campaign_id : $('#campaign-id').val(),
				  	campaign_name : $('#campaign-name').val(),
				  	did_tfn : $('#did-tfn').val(),
				  	call_route : $('#call-route').val(),
				  	survey_type : $('#survey-type').val(),
				  	no_channels : $('#no-channels').val(),
				  	copy_from : $('#copy-from').val(),
				  },
				  success: function(data) {
				  	// console.log(data);
						if(data == 1){
							$('.output-message-success').removeClass('hide');
							$('.output-message-error').addClass('hide');
						}else{
							$('.output-message-error').removeClass('hide');
							$('.output-message-success').addClass('hide');
						}
				    }
				});
			});

			$('.view-campaign').click(function(){
				var camp_id = $(this).attr('data-id');
				// alert(camp_id);
				$.ajax({
				  /*url: ".\php\ViewCampaign.php",*/
				  url: "./php/ViewCampaign.php",
				  type: 'POST',
				  data: { 
				  	campaign_id :camp_id,
				  },
				  dataType: 'json',
				  success: function(data) {
				  		// console.log(data);
				  		if(data){
				  			// info-camp-id
							// info-camp-name
							// info-camp-desc
							// info-allowed
							// info-dial-method
							// info-autodial-level
							// info-ans-mach
							$('.output-message-no-result').addClass('hide');
							$('.view-form').removeClass('hide');

							// set info here
							$('.info-camp-id').text(data.campaign_id);
							$('.info-camp-name').text(data.campaign_name);
							$('.info-dial-method').text(data.dial_method);

							$('#view-campaign-modal').modal('show');

				  		}else{
							$('.output-message-no-result').removeClass('hide');
							$('.view-form').addClass('hide');
				  		}
				    }
				});
			});

			$('.delete-campaign').click(function(){
				var camp_id = $(this).attr('data-id');
				$('.camp-id-delete-label').text(camp_id);
				$('.camp-id-delete-label').attr( "data-id", camp_id);
				$('#confirmation-delete-modal').modal('show');
			});

			$('#delete-campaign-btn').click(function(){
				var camp_id = $('.camp-id-delete-label').attr('data-id');
				// console.log(camp_id);
				$.ajax({
				  /*url: ".\php\DeleteCampaign.php",*/
				  url: "./php/DeleteCampaign.php",
				  type: 'POST',
				  data: { 
				  	campaign_id :camp_id,
				  },
				  success: function(data) {
				  		// console.log(data);
				  		if(data == 1){
				  			var table = $('#campaigns').DataTable({
				  				"sAjaxSource": ""
				  			});
							alert('Success');
							$('#confirmation-delete-modal').modal('hide');
							table.fnDraw();
						}else{
							alert('Error');
						}
				    }
				});
			});
		});
	</script>
	<!-- End of script -->

        <script>
        	// load data.
            $(".textarea").wysihtml5();
	</script>

    </body>
</html>
