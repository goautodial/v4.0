<?php
//ini_set('display_errors', 1);
//ini_set('display_startup_errors', 1);
//error_reporting(E_ALL);
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

require_once('./php/CRMDefaults.php');
require_once('./php/UIHandler.php');
require_once('./php/DbHandler.php');
require_once('./php/LanguageHandler.php');
require('./php/Session.php');

// initialize structures
$ui = \creamy\UIHandler::getInstance();
$lh = \creamy\LanguageHandler::getInstance();
$user = \creamy\CreamyUser::currentUser();


$lead_id = $_GET['lead_id'];
$output = $ui->API_GetLeadInfo($lead_id);
$list_id_ct = count($output->list_id);

if ($list_id_ct > 0) {
	for($i=0;$i < $list_id_ct;$i++){
		$first_name 	= $output->first_name[$i];
		$middle_initial = $output->middle_initial[$i];
		$last_name 		= $output->last_name[$i];
		
		$email 			= $output->email[$i];
		$phone_number 	= $output->phone_number[$i];
		$alt_phone 		= $output->alt_phone[$i];
		$address1 		= $output->address1[$i];
		$address2 		= $output->address2[$i];
		$address3 		= $output->address3[$i];
		$city 			= $output->city[$i];
		$state 			= $output->state[$i];
		$country 		= $output->country[$i];
		$gender 		= $output->gender[$i];
		$date_of_birth 	= $output->date_of_birth[$i];
		$comments 		= $output->comments[$i];
		$title 			= $output->title[$i];
		$call_count 	= $output->call_count[$i];
		$last_local_call_time = $output->last_local_call_time[$i];
	}
}
$fullname = $title.' '.$first_name.' '.$middle_initial.' '.$last_name;
$date_of_birth = date('Y-m-d', strtotime($date_of_birth));
//var_dump($output);
 $output_script = $ui->getAgentScript($lead_id, $fullname, $first_name, $last_name, $middle_initial, $email, 
 									  $phone_number, $alt_phone, $address1, $address2, $address3, $city, $province, $state, $postal_code, $country);

?>

<html>
    <head>
        <meta charset="UTF-8">
        <title>Contact Profile</title>
        <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
        <link href="css/bootstrap.min.css" rel="stylesheet" type="text/css" />
        <!-- Creamy style -->
        <link href="css/creamycrm.css" rel="stylesheet" type="text/css" />
        <!-- Customized Style -->
        <link href="css/creamycrm_test.css" rel="stylesheet" type="text/css" />
        <?php print $ui->creamyThemeCSS(); ?>

		<!-- javascript -->
        <script src="js/jquery.min.js"></script>
        <script src="js/bootstrap.min.js" type="text/javascript"></script>
        <script src="js/jquery-ui.min.js" type="text/javascript"></script>
        <script src="js/jquery.validate.min.js" type="text/javascript"></script>
		
        <!-- Creamy App 
        <script src="js/app.min.js" type="text/javascript"></script>
		-->
         <!-- theme_dashboard folder -->
					<!-- FONT AWESOME-->
			<link rel="stylesheet" href="theme_dashboard/fontawesome/css/font-awesome.min.css">
					<!-- SIMPLE LINE ICONS-->
			<link rel="stylesheet" href="theme_dashboard/simple-line-icons/css/simple-line-icons.css">
					<!-- ANIMATE.CSS-->
			<link rel="stylesheet" href="theme_dashboard/animate.css/animate.min.css">
					<!-- WHIRL (spinners)-->
			<link rel="stylesheet" href="theme_dashboard/whirl/dist/whirl.css">
				<!-- =============== PAGE VENDOR STYLES ===============-->
					<!-- WEATHER ICONS-->
			<link rel="stylesheet" href="theme_dashboard/weather-icons/css/weather-icons.min.css">
				<!-- =============== BOOTSTRAP STYLES ===============-->
			<link rel="stylesheet" href="theme_dashboard/css/bootstrap.css" id="bscss">
				<!-- =============== APP STYLES ===============-->
			<link rel="stylesheet" href="theme_dashboard/css/app.css" id="maincss">
			<!-- SLIMSCROLL-->
  			<script src="theme_dashboard/slimScroll/jquery.slimscroll.min.js"></script>

  		<!-- Theme style -->
  		<link rel="stylesheet" href="adminlte/css/AdminLTE.min.css">

        <!-- preloader -->
        <link rel="stylesheet" href="css/customizedLoader.css">

        <script type="text/javascript">
			$(window).ready(function() {
				$(".preloader").fadeOut("slow");
			});
		</script>
		<style>
			.nav-tabs > li > a{
				font-weight: normal;
				border:0px;
				border-radius: 3px 3px 0px 0px;
			}
			.custom-tabpanel{
				padding-top: 20px;
				margin-right: 10px;
			}
			h3{
				font-weight: normal;
			}
			.custom-row{
				padding: 0px 50px;
				padding-bottom: 50px;
			}
			.panel{
				margin-bottom:0;
			}
			.required_div{
				background: rgba(158,158,158,0.30);
			}
			input[type=text] {
			    border: none;
			    border-bottom: .5px solid #656565;
			}
			input[type=number] {
			    border: none;
			    border-bottom: .5px solid #656565;
			}
			input[type=date] {
			    border: none;
			    border-bottom: .5px solid #656565;
			}
			.select{
				border: none;
    			border-bottom: .5px solid #656565;
			}
			.textarea{
				border: none;
				border-bottom: .5px solid #656565;
				width: 100%;
				-webkit-box-sizing: border-box;
				   -moz-box-sizing: border-box;
						box-sizing: border-box;
			}
			.form-control[disabled], fieldset[disabled] .form-control{
				cursor: text;
				background-color: white;
			}
			label{
				font-weight: normal;
				display: inline-flex;
				width:100%;
				padding-right: 40px;
			}
			label > p {
				padding-top:10px;
				width:25%;
			}
			.edit-profile-button{
				font-size:14px; 
				font-weight:normal; 
				margin-right:30px;
			}
			.hide_div{
				display: none;
			}
			.btn.btn-raised {
				box-shadow: 0 2px 2px 0 rgba(0,0,0,.14),0 3px 1px -2px rgba(0,0,0,.2),0 1px 5px 0 rgba(0,0,0,.12);
			}
		</style>
    </head>
    <?php print $ui->creamyAgentBody(); ?>
    <div class="wrapper">
        <!-- header logo: style can be found in header.less -->
		<?php print $ui->creamyAgentHeader($user); ?>
            
            <!-- Right side column. Contains the navbar and content of the page -->
            <aside class="content-wrapper">

                <!-- Content Header (Page header) -->
                <section class="content-heading">
					<!-- Page title -->
                    <?php $lh->translateText("Contact Profile"); ?>
                    <small class="ng-binding animated fadeInUpShort"><?php echo $fullname;?></small>
                </section>

                <!-- Main content -->
                <section class="content">
					<!-- standard custom edition form -->
					<div class="panel ng-scope">
						<div class="panel-body">
							
							<div class="col-lg-9 col-lg-9 br pv custom-row">
								<div class="row">
									<div class="col-md-2 text-center visible-md visible-lg">
					                	<img src="<?php echo $user->getUserAvatar();?>" alt="Image" class="media-object img-circle thumb96 pull-left">
					                </div>
					                <div class="col-md-10">
						                <h4><?php echo $user->getUserName();?></h4>
						                <p class="ng-binding animated fadeInUpShort">Agent</p>
						                <address>
						                <?php
						                /* 
						                	echo $address1; 
						             		if($address2 != NULL){
						             			echo ", ";
						             			echo $address2; 
						             		}
						             		if($address3 != NULL){
						             			echo ", ";
						             			echo $address3;
						             		}
						                	if($city != NULL){
						                		echo "<br>".$city;
						                	}
						                	if($province != NULL){
						                		echo ", ".$province;
						                	}
						                	if($state != NULL){
						                		echo ", ".$state;
						                	}
						                	*/
						                ?>
						                </address>
						            </div>
						        </div>
						        <div class="row custom-tabpanel">
				                	<div role="tabpanel" class="panel panel-transparent">
									  <ul role="tablist" class="nav nav-tabs">
									  <!-- Nav task panel tabs-->
										 <li role="presentation" class="active">
											<a href="#profile" aria-controls="home" role="tab" data-toggle="tab" class="bb0">
											   <em class="fa fa-user fa-fw"></em>Profile</a>
										 </li>
										 <li role="presentation">
											<a href="#comments" aria-controls="home" role="tab" data-toggle="tab" class="bb0">
											   <em class="fa fa-comments-o fa-fw"></em>Comments</a>
										 </li>
										 <li role="presentation">
											<a href="#activity" aria-controls="home" role="tab" data-toggle="tab" class="bb0">
											   <em class="fa fa-bullhorn fa-fw"></em>Activity</a>
										 </li>
									  </ul>
									</div>
									<!-- Tab panes-->
									<div class="tab-content p0 bg-white">
										<div id="activity" role="tabpanel" class="tab-pane">
											<table class="table table-striped">
							                    <tr>
							                    	<td>
							                    		<center>
							                    		<em class="fa fa-user fa-fw"></em>
							                    		</center>
							                    	</td>
							                        <td>
							                           <div>1238: Outbound call to + 1650233332342</div>
							                           <div class="text-muted">
							                           		<small>March 10, 2015</small>
							                           </div>
							                        </td>
							                        <td class="text-muted text-center hidden-xs hidden-sm">
							                           <strong>254</strong>
							                        </td>
							                        <td class="hidden-xs hidden-sm"><a href="">Arnold Gray</a>
							                           <br>
							                           <small>March 10, 2015</small>
							                        </td>
							                    </tr>
							                    <tr>
							                    	<td>
							                    		<center>
							                    		<em class="fa fa-user fa-fw"></em>
							                    		</center>
							                    	</td>
							                        <td>
							                           <div>1238: Call missed from + 1273934031</div>
							                           <div class="text-muted">
							                           		<small>March 10, 2015</small>
							                           </div>
							                        </td>
							                        <td class="text-muted text-center hidden-xs hidden-sm">
							                           <strong>28</strong>
							                        </td>
							                        <td class="hidden-xs hidden-sm"><a href="">Erika Mckinney</a>
							                           <br>
							                           <small>March 10, 2015</small>
							                        </td>
							                    </tr>
							                    <tr>
							                    	<td>
							                    		<center>
							                    		<em class="fa fa-user fa-fw"></em>
							                    		</center>
							                    	</td>
							                        <td>
							                           <div>Latest udpates and news about this forum</div>
							                           <div class="text-muted">
							                           		<small>March 10, 2015</small>
							                           </div>
							                        </td>
							                        <td class="text-muted text-center hidden-xs hidden-sm">
							                           <strong>561</strong>
							                        </td>
							                        <td class="hidden-xs hidden-sm"><a href="">Annette Ruiz</a>
							                           <br>
							                           <small>March 10, 2015</small>
							                        </td>
							                    </tr>
								            </table>
										</div>
									
										<div id="profile" role="tabpanel" class="tab-pane active">

											<div style="padding-top:20px;padding-left:20px;padding-right:30px;">
												<h4>Personal Details
													<a href="#" data-role="button" class="pull-right edit-profile-button hidden" id="edit-profile">Edit Information</a>
												</h4>
											<form role="form" id="name_form" class="formMain form-inline" >
												
												<!--LEAD ID-->
												<input type="hidden" value="<?php echo $lead_id;?>" name="lead_id">
												<!--LIST ID-->
												<input type="hidden" value="<?php echo $list_id;?>" name="list_id">
												<!--ENTRY LIST ID-->
												<input type="hidden" value="<?php echo $entry_list_id;?>" name="entry_list_id">
												<!--VENDOR ID-->
												<input type="hidden" value="<?php echo $vendor_lead_code;?>" name="vendor_lead_code">
												<!--GMT OFFSET-->
												<input type="hidden" value="<?php echo $gmt_offset_now;?>" name="gmt_offset_now">
												<!--SECURITY PHRASE-->
												<input type="hidden" value="<?php echo $security_phrase;?>" name="security_phrase">
												<!--RANK-->
												<input type="hidden" value="<?php echo $rank;?>" name="rank">
												<!--CALLED COUNT-->
												<input type="hidden" value="<?php echo $call_count;?>" name="called_count">
												<!--UNIQUEID-->
												<input type="hidden" value="<?php echo $uniqueid;?>" name="uniqueid">
												<!--SECONDS-->
												<input type="hidden" value="" name="seconds">

												<div class="form-group">
							                        <p style="padding-right:10px;padding-top: 20px;">Name:</p> 
							                    </div>
												<div class="form-group">
							                        <input id="first_name" name="first_name" type="text" placeholder="First Name" value="<?php echo $first_name;?>" class="form-control input-disabled" disabled required>
							                    </div>
							                    <div class="form-group">
							                        <input id="middle_initial" name="middle_initial" type="text" placeholder="Middle Initial" value="<?php echo $middle_initial;?>" class="form-control input-disabled" disabled>
							                        
							                    </div>
							                    <div class="form-group">
							                        <input id="last_name" name="last_name" type="text" placeholder="Last Name" value="<?php echo $last_name;?>" class="form-control input-disabled" disabled required>
							                        
							                    </div>
											</form>
											<form role="form" id="gender_form" class="formMain form-inline" >
												<div class="form-group">
							                        <p style="padding-right:0px;padding-top: 20px;">Title:</p> 
							                    </div>
												<div class="form-group">
							                        <input id="title" name="title" type="text" placeholder="Title" value="<?php echo $title;?>" style="width:100px;" class="form-control input-disabled" disabled>
							                    </div>
												<div class="form-group">
							                        <p style="padding-right:0px;padding-top: 20px;">Gender:</p> 
							                    </div>
												<div class="form-group" style="padding-right:10px;">
							                        <select id="gender" name="gender" placeholder="Gender" value="<?php echo $gender;?>" class="form-control select input-disabled" disabled>
							                        	<?php 
							                        		if($gender == "M"){
							                        	?>
							                        		<option selected value="M">Male</option>
							                        		<option value="F">Female</option>
							                        	<?php
							                        		}else
							                        		if($gender == "F"){
							                        	?>
							                        		<option selected value="F">Female</option>
							                        		<option value="M">Male</option>
							                        	<?php
							                        		}else{
							                        	?>
							                        		<option selected disabled value=""> - - - </option>
							                        		<option value="M">Male</option>
							                        		<option value="F">Female</option>
							                        	<?php
							                        		}
							                        	?>
							                        </select>
							                    </div>		

							                    <div class="form-group">
							                        <p style="padding-right:5px;padding-top: 20px;">Date Of Birth:</p> 
							                    </div>
												<div class="form-group">
													<?php //echo $date_of_birth;?>
													<input type="date" id="date_of_birth" value="<?php echo $date_of_birth;?>" name="date_of_birth" class="form-control input-disabled" disabled>
							                    </div>						                   
											</form>
											</div>
											<div style="padding-top:10px;padding-left:20px;">
												<h4>Contact Details</h4>
											<form id="contact_details_form" class="formMain">
												<div class="form-group">
													<label><p><em class="fa fa-at fa-fw"></em> E-mail Address:</p> 
							                        	<input id="email" name="email" type="text" width="auto" placeholder="E-Mail Address" value="<?php echo $email;?>" class="form-control input-disabled" disabled>
							                       	</label>
							                    </div>
												<div class="form-group">
													<label><p><em class="fa fa-mobile-phone fa-fw"></em> Phone Number:</p>
														<span id="phone_numberDISP" class="hidden"></span>
														<input id="phone_code" name="phone_code" type="hidden" value="<?php echo $phone_code;?>">
							                        	<input id="phone_number" name="phone_number" type="number" width="auto" placeholder="Phone Number" value="<?php echo $phone_number;?>" class="form-control input-disabled" disabled required>
							                       	</label>
							                    </div>
							                    <div class="form-group">
													<label><p style="width:47%;padding-right:10px;"><em class="fa fa-phone fa-fw"></em> Alternative Phone Number:</p>
							                        	<input id="alt_phone" name="alt_phone" type="number" width="100" placeholder="Alternative Phone Number" value="<?php echo $alt_phone;?>" class="form-control input-disabled" disabled>
							                       	</label>
							                    </div>
												<div class="form-group">
													<label><p><em class="fa fa-home fa-fw"></em> Address Street 1:</p> 
							                        	<input id="address1" name="address1" type="text" width="auto" placeholder="Address Street 1" value="<?php echo $address1;?>" class="form-control input-disabled" disabled>
							                       	</label>
							                    </div>
							                    <div class="form-group">
							                    	<label><p><em class="fa fa-home fa-fw"></em> Address Street  2:</p>
							                        	<input id="address2" name="address2" type="text" placeholder="Address Street 2" value="<?php echo $address2;?>" class="form-control input-disabled" disabled>
							                    	</label>
							                    </div>
							                    <div class="form-group">
							                    	<label><p><em class="fa fa-home fa-fw"></em>  Address Street 3: </p>
							                        	<input id="address3" name="address3" type="text" placeholder="Address Street 3" value="<?php echo $address3;?>" class="form-control input-disabled" disabled>
							                    	</label>
							                    </div>
							                    <div class="form-group">
							                    	<label><p style="width:11%;padding-right:10px;"><em class="fa fa-building fa-fw"></em> City:</p>
							                        	<input id="city" name="city" type="text" placeholder="City" value="<?php echo $city;?>" class="form-control input-disabled" disabled>
							                    	</label>
							                    </div>
							                    <div class="form-group">
							                        <label><p style="width:17%;padding-right:10px;"><em class="fa fa-building fa-fw"></em>  Province:</p>
							                    	    <input id="province" name="province" type="text" placeholder="Province" value="<?php echo $province;?>" class="form-control input-disabled" disabled>
							                    	</label>
							                    </div>
							                    <div class="form-group">
							                        <label><p style="width:12%;padding-right:10px;"><em class="fa fa-building-o fa-fw"></em> State:</p>
							                        	<input id="state" name="state" type="text" placeholder="State" value="<?php echo $state;?>" class="form-control input-disabled" disabled>
							                    	</label>
							                    </div>
							                    <div class="form-group">
							                        <label><p style="width:21%;padding-right:10px;"><em class="fa fa-send fa-fw"></em> Postal Code:</p>
							                        	<input id="postal_code" name="postal_code" type="text" placeholder="Postal Code" value="<?php echo $postal_code;?>" class="form-control input-disabled" disabled>
							                    	</label>
							                    </div>
							                    <div class="form-group">
							                        <label><p style="width:16%;padding-right:10px;"><em class="fa fa-globe fa-fw"></em> Country:</p>
							                        	<input id="country" name="country" type="text" placeholder="Country" value="<?php echo $country;?>" class="form-control input-disabled" disabled>
							                    	</label>
							                    </div>
							                </form> 
							                </div>
							                <br/>
							                <!-- NOTIFICATIONS -->
											<div id="notifications">
												<div class="output-message-success" style="display:none;">
													<div class="alert alert-success alert-dismissible" role="alert">
													  <strong>Success!</strong> Successfuly updated contact.
													</div>
												</div>
												<div class="output-message-error" style="display:none;">
													<div class="alert alert-danger alert-dismissible" role="alert">
													  <strong>Error!</strong> Something went wrong please see input data on form or if agent already exists.
													</div>
												</div>
												<div class="output-message-incomplete" style="display:none;">
													<div class="alert alert-danger alert-dismissible" role="alert">
													  Please fill-up all the fields correctly and do not leave any highlighted fields blank.
													</div>
												</div>
											</div>

							                <div class="hide_div">
							                	<button type="submit" name="submit" id="submit_edit_form" class="btn btn-primary btn-block btn-flat">Submit</button>
							                </div>
							               
										</div><!--End of Profile-->
										
										<div id="comments" role="tabpanel" class="tab-pane">
											<div style="padding-top:20px;padding-left:20px;padding-right:30px;">
												<h4>Comments
													<a href="#" data-role="button" class="pull-right edit-profile-button hidden" id="edit-profile">Edit Information</a>
												</h4>
												<form role="form" id="comment_form" class="formMain form-inline" >
													<div class="form-group hidden">
														<p style="padding-right:0px;padding-top: 20px;">Comments:</p> 
														<button id="ViewCommentButton" onClick="ViewComments('ON');" value="-History-" class="hidden"></button>
													</div>
													<div class="form-group" style="float: left; width:100%;">
														<textarea rows="5" id="comments" name="comments" placeholder="Comments" class="form-control textarea input-disabled" style="resize:none; width: 100%;" disabled><?=$comments?></textarea>
													</div>
													<div style="clear:both;"></div>
													<br>
												</form>
											</div>
										</div>
									</div>
								</div>
							</div>
				               

							<div class="col-lg-3 col-lg-3 pv">
								<div class="panel-body">
				                  <div class="text-center">
				                  	<div class="col-md-12 text-center">
				                  		<center>
				                  			<img src="<?php echo $user->getUserAvatar();?>" alt="Image" class="media-object img-circle thumb48">
				                  		</center>
				                  		<br/>
				                  	</div>
				                    <h4><?php echo $user->getUserName();?></h4>
				                    <p>
				                    	Agent<br>
				                    	<?php echo $email;?>
				                    </p>
				                    
				                  </div>
				                  <hr>
				                  <ul class="list-unstyled ph-xl">
				                    <li>
				                        <em class="fa fa-comment fa-fw mr-lg"></em><a href="#" data-toggle="modal" data-target="#script" role="button">Script</a>
				                    </li>
				                     <li>
				                        <em class="fa fa-globe fa-fw mr-lg"></em><a href="#" data-toggle="modal" data-target="#webform" role="button">Webform</a>
				                     </li>
				                  </ul>
				                  <hr/>
				                  <!-- 
									*
									*
								  -->
								  <!--
									*
									*
				                  -->

				                </div>
							</div>

					<!-- SCRIPT MODAL -->
							<div class="modal fade" id="script" name="script" tabindex="-1" role="dialog" aria-hidden="true">
						        <div class="modal-dialog">
						            <div class="modal-content">
									
						                <div class="modal-header">
						                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
						                    <h4 class="modal-title"><i class="fa fa-edit"></i> <b><?php $lh->translateText("Script"); ?></b></h4>
						                </div>

						                    <div class="modal-body">
						                        <?php echo $output_script;?>
											</div>
										
						            </div><!-- /.modal-content -->
						        </div><!-- /.modal-dialog -->
						    </div><!-- /.modal -->

					<!-- WEBFORM MODAL -->
							<div class="modal fade" id="webform" name="webform" tabindex="-1" role="dialog" aria-hidden="true">
						        <div class="modal-dialog">
						            <div class="modal-content">
										<br/>
										<center><h2>Ok!</h2></center>
										<br/>
						            </div><!-- /.modal-content -->
						        </div><!-- /.modal-dialog -->
						    </div><!-- /.modal -->

						</div>
					</div>
                </section><!-- /.content -->
            </aside><!-- /.right-side -->

            <?php //print $ui->creamyFooter(); ?>

            <!-- Control Sidebar -->
  <aside class="control-sidebar control-sidebar-dark">
    <!-- Create the tabs -->
    <ul class="nav nav-tabs nav-justified control-sidebar-tabs">
      <li class="active"><a href="#control-sidebar-dialer-tab" data-toggle="tab"><i class="fa fa-phone"></i></a></li>
      <li><a href="#control-sidebar-agents-tab" data-toggle="tab"><i class="fa fa-user"></i></a></li>
    </ul>
    <!-- Tab panes -->
    <div class="tab-content" style="border-width:0;">
      <!-- Home tab content -->
      <div class="tab-pane active" id="control-sidebar-dialer-tab">
        <ul class="control-sidebar-menu" id="go_agent_dialer">
			
        </ul>
        <!-- /.control-sidebar-menu -->

        <ul class="control-sidebar-menu" id="go_agent_status" style="margin-top: 15px;padding: 0 15px;">
			
        </ul>
		
        <h3 class="control-sidebar-heading"><?php $lh->translateText("Manual Dial"); ?>:</h3>
        <ul class="control-sidebar-menu" id="go_agent_manualdial" style="margin-top: -10px;padding: 0 15px;">
			
        </ul>

        <ul class="control-sidebar-menu" id="go_agent_dialpad" style="margin-top: 15px;padding: 0 15px;">
			
        </ul>
		
        <ul class="control-sidebar-menu" id="go_agent_login" style="bottom: 0px; position: absolute; width: 100%; margin: 25px -15px 15px; text-align: center;">
			
        </ul>
		
        <ul class="control-sidebar-menu hidden" id="go_agent_logout" style="bottom: 0px; position: absolute; width: 100%; margin: 25px -15px 15px; text-align: center;">
			<span>
				<p><?=$lh->translateText("Call Duration")?>: <span id="SecondsDISP">0</span> <?=$lh->translationFor('second')?></p>
				<span id="session_id" class="hidden"></span>
				<span id="callchannel" class="hidden"></span>
				<input type="hidden" id="callserverip" value="" />
				<input type="checkbox" id="LeadPreview" value="0" class="hidden" />
				<span id="custdatetime" class="hidden"></span>
			</span>
			
        </ul>
        <!-- /.control-sidebar-menu -->

      </div>
      <!-- /.tab-pane -->
      <!-- Stats tab content -->
      <div class="tab-pane" id="control-sidebar-stats-tab">Stats Tab Content</div>
      <!-- /.tab-pane -->
      <!-- Settings tab content -->
      <div class="tab-pane" id="control-sidebar-agents-tab">
		<ul class="control-sidebar-menu" id="go_agent_profile">
			<li>
				<div class="center-block" style="text-align: center; background: #181f23 none repeat scroll 0 0; margin: 0 10px; padding-bottom: 1px;">
					<a href="#" class="dropdown-toggle" data-toggle="dropdown">
						<img src="<?=$user->getUserAvatar()?>" width="120" height="auto" style="border-color:transparent;" alt="User Image" />
						<p><?=$user->getUserName()?><br><small><?=$lh->translationFor("nice_to_see_you_again")?></small></p>
					</a>
				</div>
			</li>
			<?php
			if ($user->userHasBasicPermission()) {
				echo '<li>
					<div class="text-center"><a href="" data-toggle="modal" id="change-password-toggle" data-target="#change-password-dialog-modal">'.$lh->translationFor("change_password").'</a></div>
					<div class="text-center"><a href="./messages.php">'.$lh->translationFor("messages").'</a></div>
					<div class="text-center"><a href="./notificationes.php">'.$lh->translationFor("notifications").'</a></div>
					<div class="text-center"><a href="./tasks.php">'.$lh->translationFor("tasks").'</a></div>
				</li>';
			}
			?>
		</ul>
		
        <ul class="control-sidebar-menu" style="bottom: 0px; position: absolute; width: 100%; margin: 25px -15px 15px;">
			<li>
				<div class="center-block" style="text-align: center">
					<a href="./edituser.php" class="btn btn-warning"><i class='fa fa-user'></i> <?=$lh->translationFor("my_profile")?></a>
					 &nbsp; 
					<a href="./logout.php" id="cream-agent-logout" class="btn btn-warning"><i class='fa fa-sign-out'></i> <?=$lh->translationFor("exit")?></a>
				</div>
			</li>
        </ul>
      </div>
      <!-- /.tab-pane -->
    </div>
  </aside>
  <!-- /.control-sidebar -->
  <!-- Add the sidebar's background. This div must be placed
       immediately after the control sidebar -->
  <div class="control-sidebar-bg" style="    position: fixed;
    height: auto;"></div>

        </div><!-- ./wrapper -->

		<!-- Modal Dialogs -->
		<?php include_once "./php/ModalPasswordDialogs.php" ?>

		<!-- AdminLTE App -->
		<script src="adminlte/js/app.min.js"></script>

		<script type="text/javascript">
			$(document).ready(function() {
				$("#edit-profile").click(function(){
				    $('.input-disabled').prop('disabled', false);
				    $('.hide_div').show();
				    $("input:required, select:required").addClass("required_div");
				    $('#edit-profile').hide();
				    
				    var txtBox=document.getElementById("first_name" );
					txtBox.focus();
				    //$("#submit_div").focus(function() { $(this).select(); } );
				    //$('input[name="first_name"]').focus();
				});

				/** 
				 * Modifies a customer
			 	
				$("#modifycustomerform").validate({
					submitHandler: function() {
						//submit the form
							$("#resultmessage").html();
							$("#resultmessage").fadeOut();
							$.post("./php/ModifyCustomer.php", //post
							$("#name_form, #gender_form, #contact_details_form").serialize(), 
								function(data){
									//if message is sent
									if (data == '<?php print CRM_DEFAULT_SUCCESS_RESPONSE; ?>') {
									<?php 
									$errorMsg = $ui->dismissableAlertWithMessage($lh->translationFor("data_successfully_modified"), true, false);
									print $ui->fadingInMessageJS($errorMsg, "modifycustomerresult"); 
									?>				
									} else {
									<?php 
									$errorMsg = $ui->dismissableAlertWithMessage($lh->translationFor("error_modifying_data"), false, true);
									print $ui->fadingInMessageJS($errorMsg, "modifycustomerresult"); 
									?>
									}
									//
								});
						return false; //don't let the form refresh the page...
					}					
				});
				 */
				$("#submit_edit_form").click(function(){
				//alert("User Created!");
				
				var validate = 0;

					if($('#name_form')[0].checkValidity()) {
					    if($('#gender_form')[0].checkValidity()) {
					    	if($('#contact_details_form')[0].checkValidity()) {
								
								//alert("Form Submitted!");
								$.ajax({
									url: "./php/ModifyCustomer.php",
									type: 'POST',
									data: $("#name_form, #gender_form, #contact_details_form, #comment_form").serialize(),
									success: function(data) {
									  // console.log(data);
										  if(data == 1){
										  	  $('.output-message-success').show().focus().delay(2000).fadeOut().queue(function(n){$(this).hide(); n();});
											  window.setTimeout(function(){location.reload()},2000);
										  }else{
											  $('.output-message-error').show().focus().delay(5000).fadeOut().queue(function(n){$(this).hide(); n();});
										  }
									}
								});

							}else{
								validate = 1;
							}
						}else{
							validate = 1;
						}
					}else{
						validate = 1;
					}

					if(validate == 1){
						$('.output-message-incomplete').show().focus().delay(5000).fadeOut().queue(function(n){$(this).hide(); n();});
						validate = 0;
					}
				
				});
				/**
				 * Deletes a customer
				 */
				 $("#modifyCustomerDeleteButton").click(function (e) {
					var r = confirm("<?php $lh->translateText("are_you_sure"); ?>");
					e.preventDefault();
					if (r == true) {
						var customerid = $(this).attr('href');
						$.post("./php/DeleteContact.php", $("#modifycustomerform").serialize() ,function(data){
							if (data == "<?php print CRM_DEFAULT_SUCCESS_RESPONSE; ?>") { 
								alert("<?php $lh->translateText("Contact Successfully Deleted"); ?>");
								window.location = "index.php";
							}
							else { alert ("<?php $lh->translateText("Unable to Delete Contact"); ?>: "+data); }
						});
					}
				 });
				 
			});
		</script>

    </body>
</html>
