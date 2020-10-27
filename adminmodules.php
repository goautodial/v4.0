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
	
	require_once('./php/UIHandler.php');
	require_once('./php/CRMDefaults.php');
    require_once('./php/LanguageHandler.php');
    include('./php/Session.php');

	$ui = \creamy\UIHandler::getInstance();
	$lh = \creamy\LanguageHandler::getInstance();
	$user = \creamy\CreamyUser::currentUser();

	//proper user redirects
	if($user->getUserRole() != CRM_DEFAULTS_USER_ROLE_ADMIN){
		if($user->getUserRole() == CRM_DEFAULTS_USER_ROLE_AGENT){
			header("location: agent.php");
		}
	}
	
?>
<html>
    <head>
        <meta charset="UTF-8">
        <title><?php $lh->translateText('portal_title'); ?> - <?php $lh->translateText("module_management"); ?></title>
        <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
        <?php print $ui->standardizedThemeCSS(); ?>
        <?php print $ui->creamyThemeCSS(); ?>

        <!-- Bootstrap WYSIHTML5 -->
        <script src="js/plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.all.min.js" type="text/javascript"></script>

    </head>
    <?php print $ui->creamyBody(); ?>
        <!-- header logo: style can be found in header.less -->
		<?php print $ui->creamyHeader($user); ?>
        <div class="wrapper">
            <!-- Left side column. contains the logo and sidebar -->
			<?php print $ui->getSidebar($user->getUserId(), $user->getUserName(), $user->getUserRole(), $user->getUserAvatar()); ?>

            <!-- Right side column. Contains the navbar and content of the page -->
            <aside class="right-side">
                <!-- Content Header (Page header) -->
                <section class="content-header">
                    <h1>
                        <?php $lh->translateText("administration"); ?>
                        <small><?php $lh->translateText("module_management"); ?></small>
                    </h1>
                    <ol class="breadcrumb">
                        <li><a href="./index.php"><i class="fa fa-dashboard"></i> <?php $lh->translateText("home"); ?></a></li>
                        <li class="active"><?php $lh->translateText("administration"); ?></li>
                    </ol>
                </section>

                <!-- Main content -->
                <section class="content">
                <?php if ($user->userHasAdminPermission()) { ?>
                	<!-- tabla muestra los usuarios -->
                    <div class="row">
                        <div class="col-xs-12">
                            <div class="box box-default">
                                <div class="box-header">
                                    <h3 class="box-title"><?php $lh->translateText("modules"); ?></h3>
                                </div><!-- /.box-header -->
                                <div class="box-body table" id="users_table">
									<?php print $ui->getModulesAsList(); ?>
                                </div><!-- /.box-body -->
                            </div><!-- /.box -->
                        </div>
	                    <!-- Log -->
			            <div class="col-xs-12">
							<div class="box box-default collapsed-box">
								<div class="box-header with-border">
									<h3 class="box-title"><?php $lh->translateText("log"); ?></h3>
									<div class="box-tools pull-right"><button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-plus"></i></button></div>
								</div>
								<div class="box-body"><pre><?php print $ui->getModuleHandlerLog(); ?></pre></div>
							</div>
			            </div>
                    </div>
				<?php
					} else {
						print $ui->calloutErrorMessage($lh->translationFor("you_dont_have_permission"));
					}
				?>

                </section><!-- /.content -->
            </aside><!-- /.right-side -->
			<?php print $ui->getRightSidebar($user->getUserId(), $user->getUserName(), $user->getUserAvatar()); ?>
        </div><!-- ./wrapper -->

		<!-- Modal Dialogs -->
		<?php include_once "./php/ModalPasswordDialogs.php" ?>
		<?php print $ui->standardizedThemeJS(); ?>
		<script type="text/javascript">
			$(document).on('click','.activate-smtp',function() {
				var url = './php/ActivateSMTP.php';
				var id = $(this).attr('data-id');
				$.ajax({
					url: url,
					type: 'POST',
					data: {action_id: id},
					success: function(data) {
					   //console.log(data);
						if(data == "success")
							location.reload();
						else
							console.log(data);
					   /*
						if(data == "success"){
							swal({title: "<?php //$lh->translateText("success"); ?>",text: "<?php //$lh->translateText("add_server_success"); ?>",type: "success"},function(){window.location.href = 'settingsservers.php';});
						}
						else{
							sweetAlert("<?php //$lh->translateText("oups"); ?>", "<?php //$lh->translateText("something_went_wrong"); ?>"+data, "error");
						}*/
					}
				});
			});

		</script>
		<?php print $ui->creamyFooter(); ?>
    </body>
</html>
