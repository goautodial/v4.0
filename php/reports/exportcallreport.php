<?php
/**
 * @file        exportcallreport.php
 * @brief       Handles report requests
 * @copyright   Copyright (c) 2018 GOautodial Inc.
 * @author      Alexander Jim H. Abenoja
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
*/

	require_once('APIHandler.php');
	
	$api = \creamy\APIHandler::getInstance();
	
	$session_user = $api->GetSessionUser();
	$session_group = $api->GetSessionGroup();	

	// GET CAMPAIGNS
		$campaigns = $api->API_getAllCampaigns();
		
	// GET INBOUND GROUPS
		$inbound = $api->API_getAllInGroups();
		
	// GET LISTS
		$list = $api->API_getAllLists();
			
	// GET STATUSES
		$disposition = $api->API_getAllDispositions();
		
		$display = '';
		$display .= '
				<form action="../ExportCallReport.php" id="export_callreport_form" method="POST">
					<input type="hidden" name="log_user" value="'.$session_user.'" />
					<input type="hidden" name="log_group" value="'.$session_group.'" />
					<div class="row">
						<div class="col-lg-6">
							<div class="form-group">
								<label>Campaigns:</label>
								<div class="mb">
									<div class="">
										<select multiple="multiple" class="select2-3 form-control" id="selected_campaigns" name="campaigns[]" style="width:100%;">';
											for($i=0; $i < count($campaigns->campaign_id);$i++) {
												$display .= '<option value="'.$campaigns->campaign_id[$i].'">'.$campaigns->campaign_id[$i].' - '.$campaigns->campaign_name[$i].'</option>';
											}
			$display .= '				 </select>
									</div>
								</div>
							</div>
						</div>
						<div class="col-lg-6">
							<div class="form-group">
								<label>Inbound Groups:</label>
								<div class="mb">
									<div class="">
										<select multiple="multiple" class="select2-3 form-control" id="selected_inbounds" name="inbounds[]" style="width:100%;">';
											for($i=0; $i < count($inbound->group_id);$i++) {
												$display .= '<option value="'.$inbound->group_id[$i].'">'.$inbound->group_id[$i].' - '.$inbound->group_name[$i].'</option>';
											}
			$display .= '				 </select>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-lg-6">
							<div class="form-group">
								<label>Lists:</label>
								<div class="mb">
									<div class="">
										<select multiple="multiple" class="select2-3 form-control" id="selected_lists" name="lists[]" style="width:100%;">';
										$display .= '<option value="ALL" selected>--- ALL ---</option>';
											for($i=0; $i < count($list->list_id);$i++) {
												$display .= '<option value="'.$list->list_id[$i].'">'.$list->list_id[$i].' - '.$list->list_name[$i].'</option>';
											}
			$display .= '				 </select>
									</div>
								</div>
							</div>
						</div>
						<div class="col-lg-6">
							<div class="form-group">
								<label>Statuses:</label>
								<div class="mb">
									<div class="">
										<select multiple="multiple" class="select2-3 form-control" id="selected_statuses" name="statuses[]" style="width:100%;">';
										$display .= '<option value="ALL" selected>--- ALL ---</option>';
											for($i=0; $i < count($disposition->status);$i++) {
												$display .= '<option value="'.$disposition->status[$i].'">'.$disposition->status[$i].' - '.$disposition->status_name[$i].'</option>';
											}
			$display .= '				 </select>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-lg-6">
							<div class="form-group">
								<label>Custom Fields:</label>
								<div class="mb">
									<div class="">
										<select class="form-control" id="selected_custom_fields" name="custom_fields">
											<option value="N">NO</option>
											<option value="Y">YES</option>
										</select>
									</div>
								</div>
							</div>
						</div>
						<div class="col-lg-6">
							<div class="form-group">
								<label>Per Call Notes:</label>
								<div class="mb">
									<div class="">
										<select class="form-control" id="selected_per_call_notes" name="per_call_notes">
											<option value="N">NO</option>
											<option value="Y">YES</option>
										</select>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-lg-6">
							<div class="form-group">
								<label>Recording Location:</label>
								<div class="mb">
									<div class="">
										<select class="form-control" id="selected_rec_location" name="rec_location">
											<option value="N">NO</option>
											<option value="Y">YES</option>
										</select>
									</div>
								</div>
							</div>
						</div>
					</div>
				</form>
					<div class="row">
						<center><button class="btn btn-info" name="submit_export" id="submit_export"><li class="fa fa-download"> Submit & Download</button>
					</div>
					<span id="result_export"></span>
		';
		echo $display;
	?>
		<script>
			// initialize multiple selecting
			$('.select2-3').select2({ theme: 'bootstrap' });	
			$.fn.select2.defaults.set( "theme", "bootstrap" );
			
			$(document).on('click','#submit_export',function() {
				$('#submit_export').html("Downloading.....");
				$('#submit_export').attr("disabled", true);
				
				toDateVal = $('#start_filterdate').val();
				$('#export_callreport_form').append("<input type='hidden' name='fromDate' value='"+
									toDateVal+"' />");
				fromDateVal = $('#end_filterdate').val();
				$('#export_callreport_form').append("<input type='hidden' name='toDate' value='"+
									fromDateVal+"' />");
				
				//alert($("#toDate").val());
				
				$( "#export_callreport_form" ).submit();
				
				$('#submit_export').html('<li class="fa fa-download"> Submit & Download');
				$('#submit_export').attr("disabled", false);
			});
		</script>

