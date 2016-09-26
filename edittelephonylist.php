<?php

	###################################################
	### Name: edittelephonylist.php 				###
	### Functions: Edit List Details 		  		###
	### Copyright: GOAutoDial Ltd. (c) 2011-2016	###
	### Version: 4.0 								###
	### Written by: Alexander Jim H. Abenoja		###
	### License: AGPLv2								###
	###################################################

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

$modifyid = NULL;
if (isset($_POST["modifyid"])) {
	$modifyid = $_POST["modifyid"];
}

?>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Edit List</title>
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

        <!-- SWEETALERT-->
   		<link rel="stylesheet" href="theme_dashboard/sweetalert/dist/sweetalert.css">
		<script src="theme_dashboard/sweetalert/dist/sweetalert.min.js"></script>

        	<!-- =============== BOOTSTRAP STYLES ===============-->
			<link rel="stylesheet" href="theme_dashboard/css/bootstrap.css" id="bscss">
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
                    <h1 style="font-weight:normal;">
                        <?php $lh->translateText("lists"); ?>
                        <small><?php $lh->translateText("List Edit"); ?></small>
                    </h1>
                    <ol class="breadcrumb">
                        <li><a href="./index.php"><i class="fa fa-edit"></i> <?php $lh->translateText("home"); ?></a></li>
                        <?php
							if(isset($_POST["modifyid"])){
						?>
							<li><a href="./telephonylist.php"><?php $lh->translateText("lists"); ?></a></li>
                        <?php
							}
                        ?>
                        <li class="active"><?php $lh->translateText("modify"); ?></li>
                    </ol>
                </section>

						<!-- standard custom edition form -->
					<?php
					$errormessage = NULL;
					$campaign = $ui->API_getListAllCampaigns();

					//if(isset($extenid)) {
						$url = gourl."/goLists/goAPI.php"; #URL to GoAutoDial API. (required)
				        $postfields["goUser"] = goUser; #Username goes here. (required)
				        $postfields["goPass"] = goPass; #Password goes here. (required)
				        $postfields["goAction"] = "goGetListInfo"; #action performed by the [[API:Functions]]. (required)
				        $postfields["responsetype"] = responsetype; #json. (required)
				        $postfields["list_id"] = $modifyid; #Desired exten ID. (required)

				         $ch = curl_init();
				         curl_setopt($ch, CURLOPT_URL, $url);
				         curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
				         curl_setopt($ch, CURLOPT_POST, 1);
				         curl_setopt($ch, CURLOPT_TIMEOUT, 100);
				         curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
				         curl_setopt($ch, CURLOPT_POSTFIELDS, $postfields);
				         $data = curl_exec($ch);
				         curl_close($ch);
				         $output = json_decode($data);
				        //var_dump($output);
						if ($output->result=="success") {

						# Result was OK!
							for($i=0;$i<count($output->list_id);$i++){
					?>

            <!-- Main content -->
            <section class="content">
				<div class="panel panel-default">
                    <div class="panel-body">
						<legend>MODIFY LIST ID : <u><?php echo $modifyid;?></u></legend>

							<form id="modifylist">
								<input type="hidden" name="modifyid" value="<?php echo $modifyid;?>">

						<!-- Custom Tabs -->
						<div role="tabpanel">
						<!--<div class="nav-tabs-custom">-->
							<ul role="tablist" class="nav nav-tabs nav-justified">
								<li class="active"><a href="#tab_1" data-toggle="tab"> Basic Settings</a></li>
							</ul>
			               <!-- Tab panes-->
			               <div class="tab-content">

				               	<!-- BASIC SETTINGS -->
				                <div id="tab_1" class="tab-pane fade in active">
				                	<fieldset>
										<div class="form-group mt">
											<label for="group_name" class="col-sm-2 control-label">Name</label>
											<div class="col-sm-10 mb">
												<input type="text" class="form-control" name="name" id="name" placeholder="Name (Required)" value="<?php echo $output->list_name[$i];?>">
											</div>
										</div>
										<div class="form-group mt">
											<label for="group_name" class="col-sm-2 control-label">Description</label>
											<div class="col-sm-10 mb">
												<input type="text" class="form-control" name="desc" id="desc" placeholder="Description (Optional)" value="<?php echo $output->list_desc[$i];?>">
											</div>
										</div>
										<div class="form-group">
											<label for="campaign" class="col-sm-2 control-label">Campaign</label>
											<div class="col-sm-10 mb">
												<select class="form-control" name="campaign" id="campaign">
												<?php
													$campaign_option = NULL;

													for($a=0; $a < count($campaign->campaign_id);$a++){
														if($campaign->campaign_id[$a] == $output->campaign_id[$i]){
															echo "<option value='".$campaign->campaign_id[$a]."' selected> ".$campaign->campaign_name[$a]." </option>";
														}else{
															echo "<option value='".$campaign->campaign_id[$a]."'> ".$campaign->campaign_name[$a]." </option>";
														}
													}

													echo $campaign_option;
												?>
												</select>
											</div>
										</div>
										<div class="form-group">
											<label for="active" class="col-sm-2 control-label">Active</label>
											<div class="col-sm-10 mb">
												<select class="form-control" name="active" id="active">
												<?php
													$active = NULL;
													if($output->active[$i] == "Y"){
														$active .= '<option value="Y" selected> YES </option>';
													}else{
														$active .= '<option value="Y" > YES </option>';
													}

													if($output->active[$i] == "N" || $output->active[$i] == NULL){
														$active .= '<option value="N" selected> NO </option>';
													}else{
														$active .= '<option value="N" > NO </option>';
													}
													echo $active;
												?>
												</select>
											</div>
										</div>
									</fieldset>
								</div><!-- tab 1 -->

			                    <!-- FOOTER BUTTONS -->
			                    <fieldset class="footer-buttons">
			                        <div class="box-footer">
																<div class="row">
				                          <div class="pull-right">
																		 <div class="col-sm-12">
																		 <a href="telephonylist.php" type="button" class="btn btn-danger" id="cancel"><i class="fa fa-close"></i> Cancel </a>
				                             <button type="submit" class="btn btn-primary" id="modifyListOkButton" href=""> <span id="update_button"><i class="fa fa-check"></i> Update</span></button>
																		 <button type="button" class="btn btn-success" id="add_custom_field" data-id="<?php echo $modifyid; ?>"><i class="fa fa-plus"></i> Custom Fields </button>
																	 </div>
			                           </div>
															 </div>
			                        </div>
			                    </fieldset>

				            	</div><!-- end of tab content -->
	                    	</div><!-- tab panel -->
	                    </form>
	                </div><!-- body -->
	            </div>
            </section>
					<?php
							}
						}

					?>

				<!-- /.content -->
            </aside><!-- /.right-side -->

			<?php print $ui->creamyFooter(); ?>

        </div><!-- ./wrapper -->



		<!-- Modal Dialogs -->
		<?php include_once "./php/ModalPasswordDialogs.php" ?>

		<script type="text/javascript">
			$(document).ready(function() {

				/**
				 * Modifies a telephony list
			 	 */
				//$("#modifylist").validate({
                //	submitHandler: function() {

					$(document).on('click', '#cancel', function(){
						swal("Cancelled", "No action has been done :)", "error");
					});

					$(document).on('click','#modifyListOkButton',function() {
						//submit the form
							$('#update_button').html("<i class='fa fa-edit'></i> Updating.....");
							$('#modifyListOkButton').prop("disabled", true);

                			$.ajax({
	                            url: "./php/ModifyTelephonyList.php",
	                            type: 'POST',
	                            data: $("#modifylist").serialize(),
	                            success: function(data) {
	                              // console.log(data);
	                                  if(data == "success"){
	                                        swal("Success!", "List Successfully Updated!", "success")
	                                        window.setTimeout(function(){location.reload()},2000)
	                                        $('#update_button').html("<i class='fa fa-check'></i> Update");
	                                        $('#modifyListOkButton').prop("disabled", false);
	                                  }
	                                  else{
	                                      	sweetAlert("Oops...", "Something went wrong!", "error");
											$('#update_button').html("<i class='fa fa-check'></i> Update");
											$('#modifyListOkButton').prop("disabled", false);
	                                  }
	                            }
	                        });

					//return false; //don't let the form refresh the page...
				});

				$(document).on('click','#add_custom_field',function() {
					var url = './addcustomfield.php';
					var id = $(this).attr('data-id');
					//alert(extenid);
					var form = $('<form action="' + url + '" method="post"><input type="hidden" name="modifyid" value="'+id+'" /></form>');
					//$('body').append(form);  // This line is not necessary
					$(form).submit();
				});

			});
		</script>

    </body>
</html>
