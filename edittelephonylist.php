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
}else{
	header("location: telephonylist.php");
}
$statuses = $ui->API_ListsStatuses($modifyid);
$timezones = $ui->API_ListsTimezone($modifyid);

$perm = $ui->goGetPermissions('customfields', $_SESSION['usergroup']);
?>
<html>
    <head>
        <meta charset="UTF-8">
        <title><?php $lh->translateText('portal_title'); ?> - <?php $lh->translateText("edit_list"); ?></title>
        <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
        <?php print $ui->standardizedThemeCSS();?>
        <?php print $ui->creamyThemeCSS(); ?>
    </head>
    <style>
    	select{
    		font-weight: normal;
    	}
 
    	.table-bordered>tbody>tr>td,
    	.table-bordered>thead>tr>th{
    		border: 1px solid #f4f4f4;
    		font-size: small;
    	}
    	.panel .table {
		    margin-bottom: 0;
		    border: 1px solid #f4f4f4;
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
                        <small><?php $lh->translateText("edit_list"); ?></small>
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
						$postfields["log_user"] = $_SESSION['user'];
						$postfields["log_group"] = $_SESSION['usergroup'];
						$postfields["log_ip"] = $_SERVER['REMOTE_ADDR'];

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
						<legend><?php $lh->translateText("modify_list_id"); ?> :<u><?php echo $modifyid;?></u></legend>

							<form id="modifylist">
								<input type="hidden" name="modifyid" value="<?php echo $modifyid;?>">
								<input type="hidden" name="log_user" value="<?php echo $_SESSION['user']; ?>" />
								<input type="hidden" name="log_group" value="<?php echo $_SESSION['usergroup']; ?>" />

						<!-- Custom Tabs -->
						<div role="tabpanel">
						<!--<div class="nav-tabs-custom">-->
							<ul role="tablist" class="nav nav-tabs nav-justified">
								<li class="active"><a href="#tab_1" data-toggle="tab"><?php $lh->translateText("basic_settings"); ?></a></li>
								<li><a href="#tab_2" data-toggle="tab"><?php $lh->translateText("statuses"); ?> </a></li>
								<li><a href="#tab_3" data-toggle="tab"> <?php $lh->translateText("timezones"); ?></a></li>
							</ul>
			               <!-- Tab panes-->
			               <div class="tab-content">

				               	<!-- BASIC SETTINGS -->
				                <div id="tab_1" class="tab-pane fade in active">
									<div class="form-group clearfix">
										<label class="control-label col-lg-3" style="text-align: left;"><?php $lh->translateText("name"); ?>:</label>
										<div class="col-lg-9">
											<input type="text" pattern=".{2,20}" class="form-control" name="name" value="<?php echo $output->list_name[$i];?>" maxlength="30">
										</div>
									</div>
									<div class="form-group clearfix">
										<label class="control-label col-lg-3" style="text-align: left;"><?php $lh->translateText("description"); ?>:</label>
										<div class="col-lg-9">
											<input type="text" class="form-control" name="desc" value="<?php echo $output->list_description[$i];?>" maxlength="255">
										</div>
									</div>
									<div class="form-group clearfix">
										<label class="control-label col-lg-3" style="text-align: left;"><?php $lh->translateText("campaign"); ?>:</label>
										<div class="col-lg-9">
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
											</select>
										</div>
									</div>
									<div class="form-group clearfix">
										<label class="control-label col-lg-3" style="text-align: left;"><?php $lh->translateText("reset_time"); ?>:</label>
										<div class="col-lg-9">
											<input type="text" class="form-control" name="reset_time" value="<?php echo $output->reset_time[$i];?>">
										</div>
									</div>
									<div class="form-group clearfix">
										<label class="control-label col-lg-3" style="text-align: left;"><?php $lh->translateText("reset_lead"); ?>:</label>
										<div class="col-lg-4">
											<select name="reset_list" class="form-control select2">
												<option value="N"><?php $lh->translateText("go_no"); ?></option>
												<option value="Y"><?php $lh->translateText("go_yes"); ?></option>
											</select>
										</div>
										<label class="control-label col-lg-2" style="text-align: left;"><?php $lh->translateText("active"); ?>:</label>
										<div class="col-lg-3">
											<select name="active" class="form-control select2">
												<option value="N"  <?php if($output->active[$i] == 'N') echo 'selected';?>><?php $lh->translateText("go_no"); ?></option>
												<option value="Y"  <?php if($output->active[$i] == 'Y') echo 'selected';?>><?php $lh->translateText("go_yes"); ?></option>
											</select>
										</div>
									</div>
									<div class="form-group clearfix">
										<label class="control-label col-lg-3" style="text-align: left;"><?php $lh->translateText("agent_script"); ?>:</label>
										<div class="col-lg-9">
											<select name="agent_script_override" class="form-control">
												<option value="" selected="selected">NONE - INACTIVE</option>
											</select>
										</div>
									</div>
									<div class="form-group clearfix">
										<label class="control-label col-lg-3" style="text-align: left;"><?php $lh->translateText("campaign_override"); ?>:</label>
										<div class="col-lg-9">
											<input type="text" class="form-control" name="campaign_cid_override" value="<?php echo $output->campaign_cid_override[$i];?>">
										</div>
									</div>
									<div class="form-group clearfix">
										<label class="control-label col-lg-3" style="text-align: left;"></label>
										<div class="col-lg-9">
											<select name="drop_inbound_group_override" class="form-control">
												<option value="NONE">NONE</option>
											</select>
										</div>
									</div>
									<div class="form-group clearfix">
										<label class="control-label col-lg-3" style="text-align: left;"><?php $lh->translateText("web"); ?>:</label>
										<div class="col-lg-9">
											<input type="text" class="form-control" name="web_form">
										</div>
									</div>
									<div class="form-group clearfix">
										<label class="control-label col-lg-3" style="text-align: left;"><?php $lh->translateText("transfer"); ?>:</label>
										<div class="col-lg-4">
											<input type="text" class="form-control" name="xferconf_a_number">
										</div>
										<div class="col-lg-4">
											<input type="text" class="form-control" name="xferconf_b_number">
										</div>
									</div>
									<div class="form-group clearfix">
										<label class="control-label col-lg-3" style="text-align: left;"></label>
										<div class="col-lg-4">
											<input type="text" class="form-control" name="xferconf_c_number">
										</div>
										<div class="col-lg-4">
											<input type="text" class="form-control" name="xferconf_d_number">
										</div>
									</div>
									<div class="form-group clearfix">
										<label class="control-label col-lg-3" style="text-align: left;"></label>
										<div class="col-lg-4">
											<input type="text" class="form-control" name="xferconf_e_number">
										</div>
									</div>
								
								</div><!-- tab 1 -->
								<div id="tab_2" class="tab-pane">
									<div id="example1_wrapper" class="dataTables_wrapper form-inline dt-bootstrap" style="margin-top: 10px;">
										<div class="table-responsive">
											<table id="lists_statuses" class="table table-bordered" style="width: 100%;">
												<thead>
													<tr>
														<th><?php $lh->translateText("status"); ?></th>
														<th><?php $lh->translateText("description"); ?></th>
														<th><?php $lh->translateText("called"); ?></th>
														<th><?php $lh->translateText("not_called"); ?></th>
													</tr>
												</thead>
												<tbody>
													<?php 
														$called = array();
														$ncalled = array();
													?>
													<?php for($s=0;$s<count($statuses->stats);$s++){ ?>
														<?php 
															if($statuses->called_since_last_reset[$s] == 'N'){
																$countCalled = 0;
																$countNCalled = $statuses->countvlists[$s];

															}else{
																$countCalled = $statuses->countvlists[$s];
																$countNCalled = 0;
															}
															array_push($called, $countCalled);
															array_push($ncalled, $countNCalled);
														?>
														<tr>
															<td><?php echo $statuses->stats[$s]; ?></td>
															<td><?php echo $statuses->status_name[$s]; ?></td>
															<td style="text-align: center; width: 15%;"><?php echo $countCalled; ?></td>
															<td style="text-align: center; width: 15%;"><?php echo $countNCalled; ?></td>
														</tr>
													<?php } ?>
													<tr>
														<td colspan="2" style="text-align: right;"><b><?php $lh->translateText("SUB_TOTAL"); ?></b></td>
														<td style="text-align: center; width: 15%;"><?php echo array_sum($called); ?></td>
														<td style="text-align: center; width: 15%;"><?php echo array_sum($ncalled); ?></td>
													</tr>
													<tr>
														<td colspan="2" style="text-align: right;"><b><?php $lh->translateText("TOTAL"); ?></b></td>
														<td colspan="2" style="text-align: center; width: 30%;">
															<?php 
																$total = array_sum($called) + array_sum($ncalled);
																echo $total;
															?>
														</td>
													</tr>
												</tbody>
											</table>
										</div>
									</div>
								</div>

								<div id="tab_3" class="tab-pane">
									<div id="example1_wrapper" class="dataTables_wrapper form-inline dt-bootstrap" style="margin-top: 10px;">
										<div class="table-responsive">
											<table id="lists_statuses" class="table table-bordered" style="width: 100%;">
												<thead>
													<tr>
														<th><?php $lh->translateText("local_time"); ?></th>
														<th><?php $lh->translateText("called"); ?></th>
														<th><?php $lh->translateText("not_called"); ?></th>
													</tr>
												</thead>
												<tbody>
													<?php 
														$tcalled = array();
														$tncalled = array();
													?>
													<?php for($t=0;$t<count($timezones->gmt_offset_now);$t++){ ?>
														<?php 
															if($timezones->called_since_last_reset[$t] == 'N'){
																$counttCalled = 0;
																$counttNCalled = $timezones->counttlist[$t];

															}else{
																$counttCalled = $timezones->counttlist[$t];
																$counttNCalled = 0;
															}
															array_push($tcalled, $counttCalled);
															array_push($tncalled, $counttNCalled);
														?>
														<tr>
															<td><?php echo $timezones->gmt_offset_now[$t]." (".gmdate("D M Y H:i", time() + 3600 * $timezones->gmt_offset_now[$t]).")"; ?></td>
															<td style="text-align: center; width: 15%;"><?php echo $counttCalled; ?></td>
															<td style="text-align: center; width: 15%;"><?php echo $counttNCalled; ?></td>
														</tr>
													<?php } ?>
													<tr>
														<td style="text-align: right;"><b><?php $lh->translateText("SUB_TOTAL"); ?></b></td>
														<td style="text-align: center; width: 15%;"><?php echo array_sum($tcalled); ?></td>
														<td style="text-align: center; width: 15%;"><?php echo array_sum($tncalled); ?></td>
													</tr>
													<tr>
														<td style="text-align: right;"><b><?php $lh->translateText("TOTAL"); ?></b></td>
														<td colspan="2" style="text-align: center; width: 30%;">
															<?php 
																$totalt = array_sum($tcalled) + array_sum($tncalled);
																echo $totalt;
															?>
														</td>
													</tr>
												</tbody>
											</table>
										</div>
									</div>
								</div>

			                    <!-- FOOTER BUTTONS -->
			                    <fieldset class="footer-buttons">
			                        <div class="box-footer">
										<div class="row">
				                          <div class="pull-right">
											<div class="col-sm-12">
												<a href="telephonylist.php" type="button" class="btn btn-danger" id="cancel"><i class="fa fa-close"></i> <?php $lh->translateText("cancel"); ?> </a>
												<button type="submit" class="btn btn-primary" id="modifyListOkButton" href=""> <span id="update_button"><i class="fa fa-check"></i> <?php $lh->translateText("update"); ?></span></button>
												<button type="button" class="btn btn-success<?php if ($perm->customfields_create === 'N' && $perm->customfields_read === 'N' && $perm->customfields_update === 'N' && $perm->customfields_delete === 'N') { echo ' hidden'; } ?>" id="add_custom_field" data-id="<?php echo $modifyid; ?>"><i class="fa fa-th-list"></i> Custom Fields </button>
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
			<?php print $ui->getRightSidebar($user->getUserId(), $user->getUserName(), $user->getUserAvatar()); ?>

        </div><!-- ./wrapper -->
		
        <?php print $ui->standardizedThemeJS();?>
		
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
					swal({title: "<?php $lh->translateText("cancelled"); ?>", text: "<?php $lh->translateText("cancel_msg"); ?>", type: "error"},function(){window.location.href = 'telephonylist.php';});
				});
				
				$(document).on('click','#modifyListOkButton',function() {
					//submit the form
					$('#update_button').html("<i class='fa fa-edit'></i> <?php $lh->translateText("updating"); ?>");
					$('#modifyListOkButton').prop("disabled", true);
					$.ajax({
					url: "./php/ModifyTelephonyList.php",
					type: 'POST',
					data: $("#modifylist").serialize(),
					success: function(data) {
						// console.log(data);
						$('#update_button').html("<i class='fa fa-check'></i> <?php $lh->translateText("update"); ?>");
						$('#modifyListOkButton').prop("disabled", false);
						if(data == "success"){
							swal({title: "<?php $lh->translateText("success"); ?>", text: "<?php $lh->translateText("list_update_success"); ?>", type: "success"}, function(){window.location.href = 'telephonylist.php';});
							window.setTimeout(function(){location.reload();},2000);
						} else {
							sweetAlert("<?php $lh->translateText("oups"); ?>", "<?php $lh->translateText("something_went_wrong"); ?>" + data, "error");
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
					$('body').append(form);  // This line is not necessary
					$(form).submit();
				});
	
			});
		</script>
		
		<?php print $ui->creamyFooter(); ?>
    </body>
</html>
