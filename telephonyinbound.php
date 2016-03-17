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
        <?php print $ui->creamyThemeCSS(); ?>
		<!-- Circle Buttons style -->
    	<link href="css/circle-buttons.css" rel="stylesheet" type="text/css" />
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

        <!-- Creamy App -->
        <script src="js/app.min.js" type="text/javascript"></script>
		<!-- Data Tables -->
        <script src="js/plugins/datatables/jquery.dataTables.js" type="text/javascript"></script>
        <script src="js/plugins/datatables/dataTables.bootstrap.js" type="text/javascript"></script>
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
                        <small><?php $lh->translateText("inbound"); ?></small>
                    </h1>
                    <ol class="breadcrumb">
                        <li><a href="./index.php"><i class="fa fa-phone"></i> <?php $lh->translateText("home"); ?></a></li>
                        <li><?php $lh->translateText("telephony"); ?></li>
						<li class="active"><?php $lh->translateText("inbound"); ?>
                    </ol>
                </section>

                <!-- Main content -->
                <section class="content">
                <?php if ($user->userHasAdminPermission()) { ?>
				
				<!-- TAB CONTENT CONTROLLER -->
				<ul class="nav nav-tabs" role="tablist"> 
					<li role="presentation" class="active"><a data-toggle="tab" aria-controls="T_ingroup" role="tab" href="#T_ingroup">In-Groups</a></li>
					<li role="presentation"><a data-toggle="tab" aria-controls="ivr" role="tab" href="#ivr">Interactive Voice Response (IVR) Menus</a></li>
					<li role="presentation"><a data-toggle="tab" aria-controls="T_phonenumber" role="tab" href="#T_phonenumber">Phone Numbers (DIDs/TFNs)</a></li>
				</ul><!-- END OF CONTROLLER -->
				
                <div class="row">
					<div class="col-xs-12">
						<div class="box box-default">
							<div class="box-body">
					<!-- TAB CONTENT -->
						<div class="tab-content">
							
								<?php echo $ui->getInGroups(); ?>
							
							<div role="tabpanel" id="ivr" class="tab-pane table fade">
								<?php //print $ui->getIVR(); ?>
							</div>
							
								<?php echo $ui->getPhoneNumber(); ?>
							
						</div><!-- end of tab content -->
							</div>
						</div>
					</div>
				</div>

				<!-- /fila con acciones, formularios y demás -->
				<?php
					} else {
						print $ui->calloutErrorMessage($lh->translationFor("you_dont_have_permission"));
					}
				?>
				
				<div class="bottom-menu skin-blue">
					<div class="action-button-circle" data-toggle="modal" data-target="#wizard-modal">
						<?php print $ui->getCircleButton("calls", "plus"); ?>
					</div>
					<div class="fab-div-area" id="fab-div-area">
							<ul class="fab-ul" style="height: 250px;">
								<li class="li-style"><a class="fa fa-users fab-div-item" data-toggle="modal" data-target="#add_ingroups"></a></li><br/>
								<li class="li-style"><a class="fa fa-volume-up fab-div-item" data-toggle="modal" data-target="#add_ivr"></a></li><br/>
								<li class="li-style"><a class="fa fa-phone-square fab-div-item" data-toggle="modal" data-target="#add_phonenumbers"> </a></li>
							</ul>
						</div>
				</div>
					
                </section><!-- /.content -->
            </aside><!-- /.right-side -->
        </div><!-- ./wrapper -->
		
		<script>
			$(document).ready(function(){
				$(".bottom-menu").on('mouseenter mouseleave', function () {
				  $(this).find(".fab-div-area").stop().slideToggle({ height: 'toggle', opacity: 'toggle' }, 'slow');
				});
			});
		</script>
		
		<script type="text/javascript">
			$(document).ready(function() {
				/*$("#T_ingroup").dataTable({
	                "bPaginate": true,
					"aoColumnDefs": [ { "bSortable": false, "bVisible": true, "aTargets": [ -1 ] } ]
                });
				$("#T_phonenumber").dataTable({
	                "bPaginate": true,
					"aoColumnDefs": [ { "bSortable": false, "bVisible": true, "aTargets": [ -1 ] } ]
                });*/
				
				//$('#T_ingroup').dataTable();
				//$('#T_phonenumber').dataTable();
				
				/**
				  * Edit details
				 */
				
				//INGROUP
				 $(".edit-ingroup").click(function(e) {
					e.preventDefault();
					var url = './edittelephonyinbound.php';
					var form = $('<form action="' + url + '" method="post"><input type="hidden" name="groupid" value="' + $(this).attr('href') + '" /></form>');
					//$('body').append(form);  // This line is not necessary
					$(form).submit();
				 });
				 //PHONENUMBER/DID
				 $(".edit-phonenumber").click(function(e) {
					e.preventDefault();
					var url = './edittelephonyinbound.php';
					var form = $('<form action="' + url + '" method="post"><input type="hidden" name="did" value="' + $(this).attr('href') + '" /></form>');
					//$('body').append(form);  // This line is not necessary
					$(form).submit();
				 });
				
				/**
				 * Delete user.
				 */
				//INGROUPS
				 $(".delete-ingroup").click(function(e) {
					var r = confirm("<?php $lh->translateText("are_you_sure"); ?>");
					e.preventDefault();
					if (r == true) {
						var list_id = $(this).attr('href');
						$.post("./php/DeleteTelephonyList.php", { userid: list_id } ,function(data){
							if (data == "<?php print CRM_DEFAULT_SUCCESS_RESPONSE; ?>") { location.reload(); }
							else { alert ("<?php $lh->translateText("unable_delete_list"); ?>"); }
						});
					}
				 });
				 //PHONENUMBER/DID
				 $(".delete-phonenumber").click(function(e) {
					var r = confirm("<?php $lh->translateText("are_you_sure"); ?>");
					e.preventDefault();
					if (r == true) {
						var did = $(this).attr('href');
						$.post("./php/DeleteTelephonyList.php", { userid: did } ,function(data){
							if (data == "<?php print CRM_DEFAULT_SUCCESS_RESPONSE; ?>") { location.reload(); }
							else { alert ("<?php $lh->translateText("unable_delete_phonenumber"); ?>"); }
						});
					}
				 });
				
			});
		</script>
    </body>
</html>
