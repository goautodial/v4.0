
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
	<!-- Bootstrap Player -->
	<link href="css/bootstrap-player.css" rel="stylesheet" type="text/css" />
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
	<!-- Bootstrap Player -->
	<script src="js/bootstrap-player.js" type="text/javascript"></script>

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
                        <?php $lh->translateText("settings"); ?>
                        <small><?php $lh->translateText("carriers"); ?></small>
                    </h1>
                    <ol class="breadcrumb">
                        <li><a href="./index.php"><i class="fa fa-phone"></i> <?php $lh->translateText("home"); ?></a></li>
                       <li><?php $lh->translateText("settings"); ?></li>
						<li class="active"><?php $lh->translateText("carriers"); ?>
                    </ol>
                </section>

                <!-- Main content -->
                <section class="content">
                <?php if ($user->userHasAdminPermission()) { ?>
                    <div class="row">
                        <div class="col-xs-12">
                            <div class="box box-default">
                                <div class="box-header">
                                    <h3 class="box-title"><?php $lh->translateText("carriers"); ?></h3>
                                </div><!-- /.box-header -->
                                <div class="box-body table" id="recording_table">
					<?php print $ui->getListAllCarriers(); ?>
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
	
	<!-- Modal -->
	<div id="view-calltime-modal" class="modal fade" role="dialog">
	  <div class="modal-dialog">

	    <!-- Modal content-->
	    <div class="modal-content">
	      <div class="modal-header">
	        <button type="button" class="close" data-dismiss="modal">&times;</button>
	        <h4 class="modal-title"><b>Call Time Details</b></h4>
	      </div>
	      <div class="modal-body">
		<div class="form-horizontal">
			<div class="message_box"></div>
			<div class="form-group">
				<label class="control-label col-lg-4">Music on Hold Name:</label>
				<div class="col-lg-8">
					<input type="text" class="form-control moh_name">
				</div>
			</div>
		</div>
	      </div>
	      <div class="modal-footer">
		<button type="button" class="btn btn-primary btn-update-calltime" data-id="">Modify</button>
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
	      	<p>Are you sure you want to delete Carrier ID: <span class="carrier-id-delete-label" data-id=""></span></p>
	      </div>
	      <div class="modal-footer">
	        <button type="button" class="btn btn-default" id="delete-carrier-btn" data-id="">Yes</button>
	        <button type="button" class="btn btn-default" data-dismiss="modal">No</button>
	      </div>
	    </div>
	    <!-- End of modal content -->
	  </div>
	</div>
	<!-- End of modal -->
		<!-- Forms and actions -->
		<script src="js/jquery.validate.min.js" type="text/javascript"></script>
		<script type="text/javascript">
			$(document).ready(function() {
				$('#carriers').dataTable();
				
				$('.edit-carrier').click(function(){
					var carrier_id = $(this).attr('data-id');
					
					$.ajax({
						url: "./php/ViewCarrier.php",
						type: 'POST',
						data: { 
						      carrier_id : carrier_id,
						},
						dataType: 'json',
						success: function(data) {
						      console.log(data);
						      //$('#view-calltime-modal').modal('show');
						}
					});
				});
				
				$('.delete-carrier').click(function(){
					var carrier_id = $(this).attr('data-id');
					$('.carrier-id-delete-label').text(carrier_id);
					$('.carrier-id-delete-label').attr( "data-id", carrier_id);
					$('#confirmation-delete-modal').modal('show');
				});
				
				$('#delete-carrier-btn').click(function(){
					var carrier_id = $('.carrier-id-delete-label').attr('data-id');
					$.ajax({
						url: "./php/DeleteCarrier.php",
						type: 'POST',
						data: { 
						      carrier_id : carrier_id,
						},
						dataType: 'json',
						success: function(data) {
							if(data == 1){
								var table = $('#carriers').DataTable({
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
    </body>
</html>
