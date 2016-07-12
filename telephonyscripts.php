
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
                        <?php $lh->translateText("telephony"); ?>
                        <small><?php $lh->translateText("scripts"); ?></small>
                    </h1>
                    <ol class="breadcrumb">
                        <li><a href="./index.php"><i class="fa fa-phone"></i> <?php $lh->translateText("home"); ?></a></li>
                        <li><?php $lh->translateText("telephony"); ?></li>
			<li class="active"><?php $lh->translateText("scripts"); ?>
                    </ol>
                </section>

                <!-- Main content -->
                <section class="content">
                <?php if ($user->userHasAdminPermission()) { ?>
                    <div class="row">
                        <div class="col-xs-12">
                            <div class="box box-default">
                                <div class="box-header">
                                    <h3 class="box-title"><?php $lh->translateText("scripts"); ?></h3>
                                </div><!-- /.box-header -->
                                <div class="box-body table" id="campaign_table">
					<?php print $ui->getListAllScripts(); ?>
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
	<div id="script-form-modal" class="modal fade" role="dialog">
	  <div class="modal-dialog">

	    <!-- Modal content-->
	    <div class="modal-content">
	      <div class="modal-header">
	        <button type="button" class="close" data-dismiss="modal">&times;</button>
	        <h4 class="modal-title"><b>Script Information</b></h4>
	      </div>
	      <div class="modal-body">
		<div class="form-horizontal">
			<div class="form-group">
				<label class="control-label col-lg-3">Script ID:</label>
				<div class="col-lg-9">
					<input type="text" class="script_id form-control" disabled>
				</div>
			</div>
			<div class="form-group">
				<label class="control-label col-lg-3">Script Name:</label>
				<div class="col-lg-9">
					<input type="text" class="form-control script_name">
				</div>
			</div>
			<div class="form-group">
				<label class="control-label col-lg-3">Script Comments:</label>
				<div class="col-lg-9">
					<input type="text" class="form-control script_comments">
				</div>
			</div>
			<div class="form-group">
				<label class="control-label col-lg-3">Active:</label>
				<div class="col-lg-9">
					<select class="form-control script_status"></select>
				</div>
			</div>
			<div class="form-group">
				<label class="control-label col-lg-3">Script Text:</label>
				<div class="col-lg-9">
					<input type="text" class="form-control script_text">
				</div>
			</div>
			<div class="form-group">
				<label class="control-label col-lg-3">&nbsp;</label>
				<div class="col-lg-9">
					<textarea class="form-control"></textarea>
				</div>
			</div>
		</div>
	      </div>
	      <div class="modal-footer">
		<button type="button" class="btn btn-default btn-update-script">Update</button>
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
		<div class="message-box hide">
			<div class="alert alert-success hide">
				<strong>Success!</strong> Script has been succsfully deleted.
			</div>
			
			<div class="alert alert-danger hide">
				<strong>Error!</strong> Something went wrong while deleting record.
			</div>
		</div>
	      	<p>Are you sure you want to delete Script ID: <span class="script-id-delete-label" data-id=""></span></p>
	      </div>
	      <div class="modal-footer">
	        <button type="button" class="btn btn-default" id="delete-script-btn" data-id="">Yes</button>
	        <button type="button" class="btn btn-default" data-dismiss="modal">No</button>
	      </div>
	    </div>
	    <!-- End of modal content -->
	  </div>
	</div>
	<!-- End of modal -->
	
        <script>
        	// load data.
            $(".textarea").wysihtml5();
	</script>
	
	<script>
		$(document).ready(function(){
			$('#scripts').dataTable();
			//$('#script-form-modal').modal('show');
			
			$('.edit_script').click(function(){
				var id = $(this).attr('data-id');
				$.ajax({
				  url: "./php/ViewScript.php",
				  type: 'POST',
				  data: { 
				  	script_id : id
				  },
				  dataType: 'json',
				  success: function(data) {
				  	console.log(data);

					$('.script_id').val(data.script_id);
					$('.script_name').val(data.script_name);
					$('.script_comments').val();
					
					if (data.active == "Y"){
						var option = '<option value="Y" selected>Yes</option>';
						    option += '<option value="N">No</option>';
					}else{
						var option = '<option value="Y">Yes</option>';
						    option += '<option value="N" selected>No</option>';
					}
					$('.script_status').append(option);
					
					$('.script_text').val();
					
					$('#script-form-modal').modal('show');
				  }
				});
			});
			
			$('.btn-update-script').click(function(){
				$.ajax({
				  url: "./php/UpdateScript.php",
				  type: 'POST',
				  data: { 
				  	script_id : $('.script_id').val(),
					script_name : $('.script_name').val(),
					script_comments : $('.script_comments').val(),
					script_text : $('.script_text').val(),
					active : $('.script_status').val(),
				  },
				  //dataType: 'json',
				  success: function(data) {
				  	console.log(data);
				  }
				});
			});
			
			$('.delete_script').click(function(){
				var id = $(this).attr('data-id');
				$('.script-id-delete-label').text(id);
				$('.script-id-delete-label').attr( "data-id", id );
				$('#confirmation-delete-modal').modal('show');
			});
			
			$('#delete-script-btn').click(function(){
				var id = $('.script-id-delete-label').attr( "data-id");

				$.ajax({
				  url: "./php/DeleteScript.php",
				  type: 'POST',
				  data: { 
				  	script_id : id
				  },
				  dataType: 'json',
				  success: function(data) {
				  	//console.log(data);
					
					if (data.result == "success") {
						$('.message-box, .alert-success').removeClass('hide');
						$('#delete-script-btn').addClass('hide');
					}else{
						$('.message-box, .alert-danger').removeClass('hide');
					}
				  }
				});
			});
		});
	</script>
    </body>
</html>
