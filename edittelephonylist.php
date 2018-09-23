<?php
/**
 * @file 		edittelephonylist.php
 * @brief 		Edit list details
 * @copyright 	Copyright (c) 2018 GOautodial Inc. 
 * @author		Demian Lizandro A. Biscocho
 * @author     	Alexander Jim H. Abenoja
 *
 * @par <b>License</b>:
 *  This program is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU Affero General Public License as published by
 *  the Free Software Foundation, either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU Affero General Public License for more details.
 *
 *  You should have received a copy of the GNU Affero General Public License
 *  along with this program.  If not, see <http://www.gnu.org/licenses/>.
**/

	require_once('./php/UIHandler.php');
	require_once('./php/APIHandler.php');
	require_once('./php/CRMDefaults.php');
    require_once('./php/LanguageHandler.php');
    include('./php/Session.php');

	$ui = \creamy\UIHandler::getInstance();
	$api = \creamy\APIHandler::getInstance();
	$lh = \creamy\LanguageHandler::getInstance();
	$user = \creamy\CreamyUser::currentUser();

	$modifyid = NULL;
	if (isset($_POST["modifyid"])) {
		$modifyid = $_POST["modifyid"];
	}else{
		header("location: telephonylist.php");
	}
	$statuses = $api->API_getStatusesWithCountCalledNCalled($modifyid);
	$timezones = $api->API_getTZonesWithCountCalledNCalled($modifyid);
	$scripts = $api->API_getAllScripts($_SESSION['user']);
	$perm = $api->goGetPermissions('customfields');
?>
<html>
    <head>
        <meta charset="UTF-8">
        <title><?php $lh->translateText('portal_title'); ?> - <?php $lh->translateText("edit_list"); ?></title>
        <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
        <?php 
			print $ui->standardizedThemeCSS(); 
			print $ui->creamyThemeCSS();
			print $ui->dataTablesTheme();
		?>
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
			//$campaign = $ui->API_getListAllCampaigns($_SESSION['usergroup']);
			$campaign = $api->API_getAllCampaigns();
		    $output = $api->API_getListInfo($modifyid);
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
											<input type="text" pattern=".{2,20}" class="form-control" name="name" value="<?php echo $output->list_name[0];?>" maxlength="30">
										</div>
									</div>
									<div class="form-group clearfix">
										<label class="control-label col-lg-3" style="text-align: left;"><?php $lh->translateText("description"); ?>:</label>
										<div class="col-lg-9">
											<input type="text" class="form-control" name="desc" value="<?php echo $output->list_description[0];?>" maxlength="255">
										</div>
									</div>
									<div class="form-group clearfix">
										<label class="control-label col-lg-3" style="text-align: left;"><?php $lh->translateText("campaign"); ?>:</label>
										<div class="col-lg-9">
											<select class="form-control select2" name="campaign" id="campaign">
											<?php
												$campaign_option = NULL;
												$campaign_option .= '<option value="">'.$lh->translationFor("-none-").'</option>';
												for($a=0; $a < count($campaign->campaign_id);$a++){
													if($campaign->campaign_id[$a] == $output->campaign_id[0]){
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
											<input type="text" class="form-control" name="reset_time" value="<?php echo $output->reset_time[0];?>">
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
												<option value="N"  <?php if($output->active[0] == 'N') echo 'selected';?>><?php $lh->translateText("go_no"); ?></option>
												<option value="Y"  <?php if($output->active[0] == 'Y') echo 'selected';?>><?php $lh->translateText("go_yes"); ?></option>
											</select>
										</div>
									</div>
									<div class="form-group clearfix">
										<label class="control-label col-lg-3" style="text-align: left;"><?php $lh->translateText("agent_script"); ?>:</label>
										<div class="col-lg-9">
											<select name="agent_script_override" class="form-control select2">
												<option value="" selected="selected">NONE - INACTIVE</option>
												<?php
												if ($scripts->result == 'success') {
													foreach($scripts->script_id as $x => $script) {
														$isSelected = '';
														if ($script == $output->agent_script_override[0]) {
															$isSelected = ' selected';
														}
														echo '<option value="'.$script.'"'.$isSelected.'>'.$scripts->script_name[$x].'</option>';
													}
												}
												?>
											</select>
										</div>
									</div>
									<div class="form-group clearfix">
										<label class="control-label col-lg-3" style="text-align: left;"><?php $lh->translateText("campaign_override"); ?>:</label>
										<div class="col-lg-9">
											<input type="text" class="form-control" name="campaign_cid_override" value="<?php echo $output->campaign_cid_override[0];?>">
										</div>
									</div>
									<div class="form-group clearfix">
										<label class="control-label col-lg-3" style="text-align: left;"><?php $lh->translateText("drop_inbound_group_override"); ?></label>
										<div class="col-lg-9">
											<select name="drop_inbound_group_override" class="form-control select2">
												<option value="NONE">NONE</option>
											</select>
										</div>
									</div>
									<div class="form-group clearfix">
										<label class="control-label col-lg-3" style="text-align: left;"><?php $lh->translateText("web"); ?>:</label>
										<div class="col-lg-9">
											<input type="text" class="form-control" name="web_form" placeholder="https://goautodial.org" value="<?php echo $output->web_form_address[0];?>">
										</div>
									</div>
									<div class="form-group clearfix">
										<label class="control-label col-lg-3" style="text-align: left;"><?php $lh->translateText("transfer"); ?>:</label>
										<div class="col-lg-4">
											<input type="text" class="form-control" name="xferconf_a_number" placeholder="<?php $lh->translateText("xferconf_a_number"); ?>">
										</div>
										<div class="col-lg-4">
											<input type="text" class="form-control" name="xferconf_b_number" placeholder="<?php $lh->translateText("xferconf_b_number"); ?>">
										</div>
									</div>
									<div class="form-group clearfix">
										<label class="control-label col-lg-3" style="text-align: left;"></label>
										<div class="col-lg-4">
											<input type="text" class="form-control" name="xferconf_c_number" placeholder="<?php $lh->translateText("xferconf_c_number"); ?>">
										</div>
										<div class="col-lg-4">
											<input type="text" class="form-control" name="xferconf_d_number" placeholder="<?php $lh->translateText("xferconf_d_number"); ?>">
										</div>
									</div>
									<div class="form-group clearfix">
										<label class="control-label col-lg-3" style="text-align: left;"></label>
										<div class="col-lg-4">
											<input type="text" class="form-control" name="xferconf_e_number" placeholder="<?php $lh->translateText("xferconf_e_number"); ?>">
										</div>
									</div>
								
								</div><!-- tab 1 -->
								<div id="tab_2" class="tab-pane">
									<div id="example1_wrapper" class="dataTables_wrapper form-inline dt-bootstrap" style="margin-top: 10px;">
										<div class="table-responsive">
											<table id="lists_statuses" class="responsive display no-wrap" style="width: 100%;">
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
															// if($statuses->called_since_last_reset[$s] == 'N'){
															// 	$countCalled = 0;
															// 	$countNCalled = $statuses->countvlists[$s];

															// }else{
															// 	$countCalled = $statuses->countvlists[$s];
															// 	$countNCalled = 0;
															// }
															array_push($called, $statuses->is_called[$s]);
															array_push($ncalled, $statuses->not_called[$s]);
														?>
														<tr>
															<td><?php echo $statuses->stats[$s]; ?></td>
															<td><?php echo $statuses->status_name[$s]; ?></td>
															<td style="text-align: center; width: 15%;"><?php echo $statuses->is_called[$s]; ?></td>
															<td style="text-align: center; width: 15%;"><?php echo $statuses->not_called[$s]; ?></td>
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
											<table id="lists_statuses" class="responsive display no-wrap" style="width: 100%;">
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
						/*
							}
						}*/
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
				var list_read 	= <?php echo ($perm->list->list_create !== "N" ? 1 : 0 ) ?>;
				var list_update = <?php echo ($perm->list->list_create !== "N" ? 1 : 0 ) ?>;
				
				$(document).on('click', '#cancel', function(){
					swal({title: "<?php $lh->translateText("cancelled"); ?>", text: "<?php $lh->translateText("cancel_msg"); ?>", type: "error"},function(){window.location.href = 'telephonylist.php';});
				});
				
				if (list_read == 1 && list_update == 1) {
					$('#modifyListOkButton').attr('disabled', false);
					$('#add_custom_field').attr('disabled', false);
					
					$(document).on('click','#modifyListOkButton',function() {
						//submit the form
						$('#update_button').html("<i class='fa fa-edit'></i> <?php $lh->translateText("updating"); ?>");
						$('#modifyListOkButton').prop("disabled", true);
						$.ajax({
							url: "./php/ModifyTelephonyList.php",
							type: 'POST',
							data: $("#modifylist").serialize(),
							success: function(data) {
								//console.log(data);
								//console.log($("#modifylist").serialize());
								$('#update_button').html("<i class='fa fa-check'></i> <?php $lh->translateText("update"); ?>");
								$('#modifyListOkButton').prop("disabled", false);
								if (data == 1) {
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
				} else {
					$('#modifyListOkButton').attr('disabled', true);
					$('#add_custom_field').attr('disabled', false);
				}
	
			});
		</script>
		
		<?php print $ui->creamyFooter(); ?>
    </body>
</html>
