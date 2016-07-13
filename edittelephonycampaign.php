

<?php
/*ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
*/
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

$campaign = NULL;
if (isset($_POST["campaign"])) {
	$campaign = $_POST["campaign"];
}

$disposition = NULL;
if (isset($_POST["disposition"])) {
	$disposition = $_POST["disposition"];
}

$leadfilter = NULL;
if (isset($_POST["leadfilter"])) {
	$leadfilter = $_POST["leadfilter"];
}
?>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Goautodial Edit In-Group</title>
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
                        <small>Modify Campaign</small>
                    </h1>
                    <ol class="breadcrumb">
                        <li><a href="./index.php"><i class="fa fa-edit"></i> <?php $lh->translateText("home"); ?></a></li>
                        <li> <?php $lh->translateText("telephony"); ?></li>
                        <?php
							if($campaign != NULL || $disposition != NULL || $leadfilter != NULL){
						?>	
							<li><a href="./telephonycampaign.php"><?php $lh->translateText("Campaign"); ?></a></li>
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
							<h3 class="box-title">Edit Campaign</h3>
						</div>
						<!-- /.box-header -->
						<form class="form-horizontal">
						<div class="box-body">
							<?php $errormessage = NULL; ?>
					
							<!-- // IF CAMPAIGN -->
							<?php if($campaign != NULL) { ?>
							<?php 
								$url = gourl."/goCampaigns/goAPI.php"; #URL to GoAutoDial API. (required)
								$postfields["goUser"] = goUser; #Username goes here. (required)
								$postfields["goPass"] = goPass; #Password goes here. (required)
								$postfields["goAction"] = "getCampaignInfo"; #action performed by the [[API:Functions]]. (required)
								$postfields["responsetype"] = responsetype; #json. (required)
								$postfields["campaign_id"] = $campaign; #Desired list id. (required)
		            
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

								// echo "<pre>";
								// print_r($output);
							?>
								<?php if ($output->result=="success") { ?>
								<!-- # Result was OK! -->
									<?php for($i=0;$i < count($output->campaign_id);$i++){ ?>
										Under Construction
									<?php } ?>
								<?php } else { ?>
								<!-- # An error occured -->
									<?php echo $output->result; ?>
								<?php } ?>
		                        
							<?php }else { ?>
					    		<?php echo $errormessage = $lh->translationFor("some_fields_missing"); ?>
							<?php } ?>
						</div>
						<!-- /.box-body -->
						<div class="box-footer">
							<a type="button" class="btn btn-warning">Cancel</a>
							<button type="submit" class="btn btn-success pull-right">Modify</button>
						</div>
						<!-- /.box-footer -->
						</form>

					</div>			
                </section>
				<!-- /.content -->
            </aside><!-- /.right-side -->
			
            <?php print $ui->creamyFooter(); ?>
			
        </div><!-- ./wrapper -->

		<!-- Modal Dialogs -->
		<?php include_once "./php/ModalPasswordDialogs.php" ?>

		<script type="text/javascript">
			$(document).ready(function() {

			$(function(){
				$('.demo1').colorpicker();
			});
	
				/** 
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
