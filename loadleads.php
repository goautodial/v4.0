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
		<!-- DATA TABLES -->
        <link href="css/datatables/dataTables.bootstrap.css" rel="stylesheet" type="text/css" />
    	<!-- Wizard Form style -->
	<link href="css/style.css" rel="stylesheet" type="text/css" />
    	<link rel="stylesheet" href="css/easyWizard.css">

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
                        <?php $lh->translateText("load_leads"); ?>
                        <small>(Upload/Import Leads)</small>
                    </h1>
                    <ol class="breadcrumb">
                        <li><a href="./index.php"><i class="fa fa-phone"></i> <?php $lh->translateText("home"); ?></a></li>
                        <li><?php $lh->translateText("load_leads"); ?></li>
						<li class="active">Upload/Import Leads</li>
                    </ol>
                </section>
		
                <!-- Main content -->
                <section class="content">
                <?php if ($user->userHasAdminPermission()) { ?>
                    <div class="clearfix">
                        <div class="col-xs 12">
                            <div class="box box-default">
                            	<?php 
                            		if(isset($_GET['message'])){
                            			echo '<div class="col-lg-12" style="margin-top: 10px;">';
                            			if($_GET['message'] == "Success"){
                            				echo '<div class="alert alert-success">
											  <strong>Success!</strong> Upload of leads was successful.
											</div>';
                            			}else{
                            				echo '<div class="alert alert-danger">
											  <strong>Error!</strong> Something went wrong please contact administrator.
											</div>';
                            			}
                            			echo '</div>';
                            		}
                            	?>
                                <div class="box-header">
                                    <h3 class="box-title"><?php $lh->translateText("load_leads"); ?></h3>
                                </div><!-- /.box-header -->
                                <div class="box-body clearfix" id="load_leads">
                                	<div class="col-lg-12">
                                	<?php 
                                		$lists = $ui->API_goGetAllLists();
                                		// print_r($lists);
                                		
                                	?>
										<form class="form-horizontal" action="./php/AddLoadLeads.php" method="POST" enctype="multipart/form-data">
											<div class="form-group">
												<label class="">List ID:</label>
												<div>
													<select class="form-control" name="list_id">
													<option value="">-- Select List ID --</option>
														<?php 
															for($i=0;$i<count($lists->list_id);$i++){
					                                			echo '<option value="'.$lists->list_id[$i].'">'.$lists->list_id[$i].'</option>';
					                                		}
														?>
													</select>
												</div>
											</div>
											<div class="form-group">
												<label class="">CSV File:</label>
												<div>
													<div class="input-group">
												      <input type="text" class="form-control file-name" name="file_name" placeholder="CSV File">
												      <span class="input-group-btn">
												        <button type="button" class="btn btn-default browse-btn" type="button">Browse</button>
												      </span>
												    </div><!-- /input-group -->
												    <input type="file" class="file-box hide" name="file_upload">
												</div>
											</div>
											<div class="form-group">
												<div class="pull-right">
													<button type="submit" class="btn btn-primary">Submit</button>
												</div>
											</div>
										</form>
									</div>
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
					</script>
                </section><!-- /.content -->
            </aside><!-- /.right-side -->
			<?php print $ui->getRightSidebar($user->getUserId(), $user->getUserName(), $user->getUserAvatar()); ?>
        </div><!-- ./wrapper -->
	
		<!-- Forms and actions -->
		<script src="js/jquery.validate.min.js" type="text/javascript"></script>
		<script src="js/easyWizard.js" type="text/javascript"></script> 
		<script type="text/javascript">
			$(document).ready(function() {
				$('#T_users').dataTable();

				$('.browse-btn').click(function(){
					$('.file-box').click();
				});

				$('.file-box').change(function(){
					var myFile = $(this).prop('files');
					var Filename = myFile[0].name;

					$('.file-name').val(Filename);
					console.log($(this).val());
				});
		
		// for easy wizard -
			$("#wizard-modal").wizard({
				onnext:function(){
					//alert("Nexted!");
					var phone_logins = document.getElementById('phone_logins').value;
					document.getElementById("phone_login1").value = phone_logins;
					
					if(phone_logins == null || phone_logins == ""){
					  alert("Please Fill All Required Field");
					  return false;
						
						
					}
				},
                onfinish:function(){
					$.ajax({
						/*url: ".\php\AddCampaign.php",*/
						url: "./php/CreateTelephonyUser.php",
						type: 'POST',
						data: $("#create_form").serialize(),
						success: function(data) {
						  // console.log(data);
							  if(data == 1){
								  $('.output-message-success').removeClass('hide');
								  $('.output-message-error').addClass('hide');
								  window.location = window.location.href;
							  }else{
								  $('.output-message-error').removeClass('hide');
								  $('.output-message-success').addClass('hide');
							  }
						}
					});
                }
				
            });
		
		/* additional number custom*/
			$('#seats').on('change', function() {
			//  alert( this.value ); // or $(this).val()
				if(this.value == "custom_seats") {
				  $('#custom_seats').show();
				}
				if(this.value != "custom_seats") {
				  $('#custom_seats').hide();
				}
			});

		/* generate phone logins*/
			$('#generate_phone_logins').on('change', function() {
			//  alert( this.value ); // or $(this).val()
				if(this.value == "Y") {
				  $('#phone_logins_form').show();
				}
				if(this.value == "N") {
				  $('#phone_logins_form').hide();
				}
			});

				/**
				  * Edit user details
				 */
				 $(".edit-T_user").click(function(e) {
					e.preventDefault();
					var url = 'edittelephonyuser.php';
					var form = $('<form action="' + url + '" method="post"><input type="hidden" name="userid" value="' + $(this).attr('href') + '" /></form>');
					$('body').append(form);  // This line is not necessary
					$(form).submit();
				 });
				
				/**
				 * Delete user.
				 */
				 $(".delete-T_user").click(function(e) {
					var r = confirm("<?php $lh->translateText("are_you_sure"); ?>");
					e.preventDefault();
					if (r == true) {
						var user_id = $(this).attr('href');
						$.post("./php/DeleteTelephonyUser.php", { userid: user_id } ,function(data){
							if (data == "<?php print CRM_DEFAULT_SUCCESS_RESPONSE; ?>") { location.reload(); }
							else { alert ("<?php $lh->translateText("unable_delete_user"); ?>"); }
						});
					}
				 });
				
				
				
			});
			
		</script>
    </body>
</html>
