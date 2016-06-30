<?php
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

for($i=0;$i < count($output->list_id);$i++){
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
}
$fullname = $first_name.' '.$middle_initial.' '.$last_name;
//var_dump($output);
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
		
        <!-- Creamy App -->
        <script src="js/app.min.js" type="text/javascript"></script>

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

        <!-- preloader -->
        <link rel="stylesheet" href="css/customizedLoader.css">

        <script type="text/javascript">
			$(window).ready(function() {
				$(".preloader").fadeOut("slow");
			})
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
			input[type=text] {
			    border: none;
			    border-bottom: .5px solid;
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
		</style>
    </head>
    <?php print $ui->creamyBody(); ?>
    <div data-ui-view="" data-autoscroll="false" class="wrapper ng-scope">
        <!-- header logo: style can be found in header.less -->
		<?php print $ui->creamyHeader($user); ?>
            <!-- Left side column. contains the logo and sidebar -->
			<?php print $ui->getSidebar($user->getUserId(), $user->getUserName(), $user->getUserRole(), $user->getUserAvatar()); ?>

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
					                	<img src="theme_dashboard/img/user/05.jpg" alt="Image" class="media-object img-circle thumb96 pull-left">
					                </div>
					                <div class="col-md-10">
						                <h4><?php echo $fullname;?></h4>
						                <p class="ng-binding animated fadeInUpShort">Lead director</p>
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
											<div style="padding-top:20px;padding-left:20px;">
												<h4>Personal Details</h4>
											
											<form role="form" class="form-inline" >
												<div class="form-group">
							                        <p style="padding-right:10px;padding-top: 20px;">Name:</p> 
							                    </div>
												<div class="form-group">
							                        <input id="first_name" type="text" placeholder="First Name" value="<?php echo $first_name;?>" class="form-control" disabled>
							                    </div>
							                    <div class="form-group">
							                        <input id="middle_initial" type="text" placeholder="Middle Initial" value="<?php echo $middle_initial;?>" class="form-control" disabled>
							                        
							                    </div>
							                    <div class="form-group">
							                        <input id="last_name" type="text" placeholder="Last Name" value="<?php echo $last_name;?>" class="form-control" disabled>
							                        
							                    </div>
											</form>
											</div>
											<div style="padding-top:10px;padding-left:20px;">
												<h4>Contact Details</h4>
												<div class="form-group">
													<label><p><em class="fa fa-mobile-phone fa-fw"></em> Phone Number:</p> 
							                        	<input id="address1" type="text" width="auto" placeholder="Phone Number" value="<?php echo $address1;?>" class="form-control" disabled>
							                       	</label>
							                    </div>
							                    <div class="form-group">
													<label><p style="width:50%;padding-right:10px;"><em class="fa fa-phone fa-fw"></em> Alternative Phone Number:</p>
							                        	<input id="address1" type="text" width="100" placeholder="Alternative Phone Number" value="<?php echo $address1;?>" class="form-control" disabled>
							                       	</label>
							                    </div>
												<div class="form-group">
													<label><p><em class="fa fa-home fa-fw"></em> Address Street 1:</p> 
							                        	<input id="address1" type="text" width="auto" placeholder="Address Street 1" value="<?php echo $address1;?>" class="form-control" disabled>
							                       	</label>
							                    </div>
							                    <div class="form-group">
							                    	<label><p><em class="fa fa-home fa-fw"></em> Address Street  2:</p>
							                        	<input id="address2" type="text" placeholder="Address Street 2" value="<?php echo $address2;?>" class="form-control" disabled>
							                    	</label>
							                    </div>
							                    <div class="form-group">
							                    	<label><p><em class="fa fa-home fa-fw"></em>  Address Street 3: </p>
							                        	<input id="address3" type="text" placeholder="Address Street 3" value="<?php echo $address3;?>" class="form-control" disabled>
							                    	</label>
							                    </div>
							                    <div class="form-group">
							                    	<label><p style="width:11%;padding-right:10px;"><em class="fa fa-building fa-fw"></em> City:</p>
							                        	<input id="city" type="text" placeholder="Last Name" value="<?php echo $city;?>" class="form-control" disabled>
							                    	</label>
							                    </div>
							                    <div class="form-group">
							                        <label><p style="width:17%;padding-right:10px;"><em class="fa fa-building fa-fw"></em>  Province:</p>
							                    	    <input id="province" type="text" placeholder="Last Name" value="<?php echo $province;?>" class="form-control" disabled>
							                    	</label>
							                    </div>
							                    <div class="form-group">
							                        <label><p style="width:12%;padding-right:10px;"><em class="fa fa-building-o fa-fw"></em> State:</p>
							                        	<input id="state" type="text" placeholder="Last Name" value="<?php echo $state;?>" class="form-control" disabled>
							                    	</label>
							                    </div>
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
				                  			<img src="theme_dashboard/img/user/05.jpg" alt="Image" class="media-object img-circle thumb48">
				                  		</center>
				                  		<br/>
				                  	</div>
				                    <h4><?php echo $fullname;?></h4>
				                    <p>
				                    	Lead director<br>
				                    	<?php echo $email;?>
				                    </p>
				                    
				                  </div>
				                  <hr>
				                  <ul class="list-unstyled ph-xl">
				                    <li>
				                        <em class="fa fa-home fa-fw mr-lg"></em>
				                    	<?php
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
						                		echo ", ".$city;
						                	}
						                	if($province != NULL){
						                		echo ", ".$province;
						                	}
						                	if($state != NULL){
						                		echo ", ".$state;
						                	}
				                    	?>
				                    </li>
				                     <li>
				                        <em class="fa fa-briefcase fa-fw mr-lg"></em><a href="#">Themicon.co</a>
				                     </li>
				                     <li>
				                        <em class="fa fa-graduation-cap fa-fw mr-lg"></em>Master Designer</li>
				                  </ul>
				                  <hr/>
				                  <!-- 
									*
									*
								  -->
				                  	<small> --- INSERT PHONE HERE --- </small> 
								  <!--
									*
									*
				                  -->

				                </div>
							</div>

						</div>
					</div>
                </section><!-- /.content -->
            </aside><!-- /.right-side -->
            <?php //print $ui->creamyFooter(); ?>
        </div><!-- ./wrapper -->

		<!-- Modal Dialogs -->
		<?php include_once "./php/ModalPasswordDialogs.php" ?>

		<script type="text/javascript">
			$(document).ready(function() {				
				/** 
				 * Modifies a customer
			 	 */
				$("#modifycustomerform").validate({
					submitHandler: function() {
						//submit the form
							$("#resultmessage").html();
							$("#resultmessage").fadeOut();
							$.post("./php/ModifyCustomer.php", //post
							$("#modifycustomerform").serialize(), 
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
