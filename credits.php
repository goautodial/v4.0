<?php
/**
 * @file 		crm.php
 * @brief 		Manage leads and contacts
 * @copyright 	Copyright (c) 202 GOautodial Inc. 
 * @author		Demian Lizandro A. Biscocho
 * @author     	Christopher P. Lomuntad
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
	require_once('./php/CRMDefaults.php');
	require_once('./php/LanguageHandler.php');
    require('./php/Session.php');

	// DDBB, User & Language vars
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
        <title><?php $lh->translateText('portal_title'); ?> - <?php $lh->translateText("credits"); ?></title>
        <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
        <link href="css/bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="css/font-awesome.min.css" rel="stylesheet" type="text/css" />
        <!-- iCheck -->
        <link href="css/iCheck/minimal/blue.css" rel="stylesheet" type="text/css" />
        <!-- Ionicons -->
        <link href="css/ionicons.min.css" rel="stylesheet" type="text/css" />
        <!-- Creamy style -->
        <link href="css/creamycrm.css" rel="stylesheet" type="text/css" />
        <?php print $ui->creamyThemeCSS(); ?>

        <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
          <script src="js/html5shiv.js"></script>
          <script src="js/respond.min.js"></script>
        <![endif]-->

		<!-- Javascript -->
        <script src="js/jquery.min.js"></script>
        <script src="js/bootstrap.min.js" type="text/javascript"></script>
        <!-- iCheck -->
        <script src="js/plugins/iCheck/icheck.min.js" type="text/javascript"></script>
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
                        <?php $lh->translateText("credits"); ?>
                        <small><?php $lh->translateText("GOautodial_team"); ?></small>
                    </h1>
                    <ol class="breadcrumb">
                        <li><a href="#"><i class="fa fa-list-alt"></i> <?php $lh->translateText("home"); ?></a></li>
                        <li class="active"><?php $lh->translateText("credits"); ?></li>
                    </ol>
                </section>

                <!-- Main content -->
                <section class="content">
					<div class="panel panel-default">
						<div class="panel-body">
							<div class="corner-all team-thanks" style="width: 90%; margin-left: auto; margin-right: auto;" >
								<!-- <span>The GOautodial open source CE project is hosted and funded by GOautodial Inc. We are not affiliated with any organizations.</span> -->
								<br />
								<br />                            
								<span>The GOautodial open source CE project team and contributors:</span>
								<br />
								<br />
								<b>Architect and Creator:</b> Demian Lizandro A. Biscocho<br />
								<br />
								<span><b>Core Team:</b></span>
								<br/>
								<div class="contributors">
                                    Demian Lizandro A. Biscocho<br />
                                    Christopher P. Lomuntad<br />
                                    Alexander Jim Abenoja<br />                                   
                                </div>
								<br />
								<span><b>QA Team:</b></span>
								<br/>
								<div class="contributors">
                                    Levy Ryan D. Nolasco<br />
                                    Jackie J. Alfonso<br />
                                </div>
								<br />
								<b>Applications Security:</b> 
								<br />Chris McCurley<br />
								<br />								
								<b>Graphics Designer:</b> 
								<br />Om Narayan A. Velasco<br />
								<br />								
								<span><b>Contributors:</b></span>
								<br/>
								<div class="contributors">
									Augusto Monge<br />									
                                    Rafael R. Pekson II<br />
                                    Willard Ty H. Manansala<br />
                                    Jerico James F. Milo<br />
                                    Tristan Kendrick A. Biscocho<br />
                                    Kristian T. Antiligando<br />
									Noel D.L. Umandap<br />
									Jeremiah Sebastian V. Samatra<br />
									Warren I. Briones<br />
                                    Jefferson C. Varias<br />
                                    Regie V. Irupang<br />
                                    Erwin C. De Luna<br />
                                    Jin Kevin Dionisio<br />
                                    Huaguan Gao (高华冠) - Chinese translation<br />
                                </div>
								<br />
								<br />
								<span><b>Project Links:</b></span>
								<br/>
								<div class="contributors">
									<table width="100%" >
										<tr>
											<td align="center"><a href="http://ui.ajax.org/" target="_blank">             <img width="110px" src="./img/ajax-logo.png" title="AJAX - Asynchronous Javascript And XML"/></a></td>
											<td align="center"><a href="http://jssip.net" target="_blank">               <img width="110px" src="./img/jssip-logo.png" title="Apache HTTP Server"/></a></td>
											<td align="center"><a href="http://asterisk.org" target="_blank">             <img width="110px" src="./img/asterisk-logo.png" title="Asterisk Telephony"/></a></td>
											<td align="center"><a href="http://webrtc.org" target="_blank">               <img width="180px" src="./img/webrtc-logo.png" title="CentOS Linux"/></a></td>
											<td align="center"><a href="https://iknowitworks.github.io/Creamy" target="_blank"> <img width="80px" src="./img/creamy-logo.png" title="Creamy CRM"/></a></td>
											<td align="center"><a href="http://javascript.org" target="_blank">           <img width="80px" src="./img/javascript-logo.png" title="Javascript"/></a></td>
										</tr>  
										<tr>   
											<td align="center"><a href="http://json.org" target="_blank">                 <img width="110px" src="./img/json-logo.png" title="JSON"/></a></td>
											<td align="center"><a href="https://jquery.org" target="_blank" >             <img width="110px" src="./img/jquery-logo.png" title="jQuery"/></a></td>
											<td align="center"><a href="http://mariadb.org" target="_blank">              <img width="140px" src="./img/mariadb-logo.png" title="MySQL"/></a></td>
											<td align="center"><a href="http://perl.org" target="_blank">                 <img width="110px" src="./img/perl-logo.png" title="Perl"/></a></td>
											<td align="center"><a href="http://php.net" target="_blank">                  <img width="100px" src="./img/php-logo.png" title="PHP"/></a></td>
											<td align="center"><a href="http://vicidial.org" target="_blank">             <img width="100px" src="./img/vicidial-logo.png" title="Vicidial"/></a></td>
										</tr>
									</table>
                                </div>
                                <br />
                                <br />
                                <br />                          
                                <span><b>Open source community:</b>
                                <br />URL: <u><a href="https://goautodial.org" target="_blank">https://goautodial.org</a></u>
                                <br />Email: <a href="mailto:community@goautodial.com"><u>community@goautodial.com</u></a></span>
                                <br />
                                <br />
                                <span><b>Commercial support:</b>
                                <br />URL: <u><a href="https://goautodial.com" target="_blank">https://goautodial.com</a></u>
                                <br />Email: <a href="mailto:sales@goautodial.com"><u>sales@goautodial.com</u></a></span>
                                <br />
                                <br />
                                <br />
                                <span><b>Trademark:</b></span>
                                <br />
                                <span>GOautodial &reg;, GOautodial CE &reg;, GOAdmin &reg;,  GOAgent &reg;,  GOReports &reg; are registered <u><a href="http://goautodial.org/projects/goautodialce/wiki/License" target="_blank">trademarks</a></u> of GOautodial Inc. All other trademarks are the property of their respective owners.</span>
                                <br />  
							</div>
						</div>
					</div>

                </section><!-- /.content -->
           
            </aside><!-- /.right-side -->
            <?php print $ui->creamyFooter(); ?>
        </div><!-- ./wrapper -->

	<!-- CHANGE TASK MODAL -->

    <div class="modal fade" id="edit-task-modal" name="edit-task-modal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title"><i class="fa fa-edit"></i> <?php $lh->translateText("modify_task_description"); ?></h4>
                    <p><?php $lh->translateText("insert_new_description"); ?></p>
                </div>
                <form action="" method="post" name="edit-task-form" id="edit-task-form">
                    <div class="modal-body">
                        <div class="form-group">
                            	<label for="edit-task-description"><?php $lh->translateText("new_description"); ?></label>
                            	<input type="text required" class="form-control" id="edit-task-description" name="edit-task-description" placeholder="<?php $lh->translateText("new_description"); ?>">
                    	    	<!--<br/>
				<center>
				<label for="task-note">Add a note</label>	
                            	<textarea name="task-note" id="task-note" rows="4" cols="80" placeholder="Add Notes/Comments in your task..."></textarea>
		</center>-->	</div>
						<input type="hidden" id="edit-task-taskid" name="edit-task-taskid" value="">
						<div id="changetaskresult" name="changetaskresult"></div>
                    </div>
                    <div class="modal-footer clearfix">
                        <button type="button" class="btn btn-danger pull-left" data-dismiss="modal" id="changetaskCancelButton"><i class="fa fa-times"></i> <?php $lh->translateText("cancel"); ?></button>
                        <button type="submit" class="btn btn-primary pull-right" id="changetaskOkButton"><i class="fa fa-check"></i> <?php $lh->translateText("modify_task"); ?></button>
                    </div>
                </form>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->		

	<!-- /CHANGE TASK MODAL -->
	
	<!-- TASK DIALOGS -->

	<!-- END TASK DIALOGS -->

		<script type="text/javascript">
		$(document).ready(function() {
			/** 
			 * Creates a new task.
		 	 */
			$("#createtask").validate({
				rules: {
					taskDescription: "required",
				},
				submitHandler: function() {
					//submit the form
						$("#resultmessage").html();
						$("#resultmessage").fadeOut();
						$.post("./php/CreateTask.php", //post
						$("#createtask").serialize(), 
							function(data){
								//if message is sent
								if (data == '<?php print CRM_DEFAULT_SUCCESS_RESPONSE; ?>') {
									location.reload();
								} else {
									$("#resultmessage").html('<div class="alert alert-danger alert-dismissable"><i class="fa fa-ban"></i><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button><b><?php $lh->translateText("oups"); ?></b> <?php $lh->translateText("unable_create_task"); ?>: '+ data);
									$("#resultmessage").fadeIn(); //show confirmation message
								}
								//
							});
					return false; //don't let the form refresh the page...
				}					
			});
		
			/**
			 * Delete a task
			 */
			 $(".delete-task-action").click(function(e) {
				var r = confirm("<?php $lh->translateText("are_you_sure"); ?>");
				e.preventDefault();
				if (r == true) {
					var taskid = $(this).attr('href');
					$.post("./php/DeleteTask.php", { "taskid": taskid } ,function(data){
						if (data == "<?php print CRM_DEFAULT_SUCCESS_RESPONSE; ?>") { location.reload(); }
						else { alert ("<?php $lh->translateText("unable_delete_task"); ?>"); }
					});
				}
			 });
			 
			/**
			 * Show the edit task dialog, filling the edit fields properly.
			 */
			$(".edit-task-action").click(function(e) {
				// Set ID of the task to edit
				
				e.preventDefault();
				$('#edit-task-modal').modal();
                                var ele = $(this).parents("li").first();
				var task_id = ele.attr("id"); // task ID is contained in the ID element of the li object.
				$('#edit-task-taskid').val(task_id);
				
				// set the previous description of task.
				var current_text = $('.text', ele);
				$('#edit-task-description').val(current_text.text());
			});
		
			/**
			 * Edit the description of a task
			 */
			$("#edit-task-form").validate({
				submitHandler: function() {
					//submit the form
						$("#resultmessage").html();
						$("#resultmessage").fadeOut();
						$.post("./php/ModifyTask.php", //post
						$("#edit-task-form").serialize(), 
							function(data){
								//if message is sent
								if (data == '<?php print CRM_DEFAULT_SUCCESS_RESPONSE; ?>') {
									location.reload();
								} else {
									$("#resultmessage").html('<div class="alert alert-danger alert-dismissable"><i class="fa fa-ban"></i><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button><b><?php $lh->translateText("oups"); ?></b> <?php $lh->translateText("unable_modify_task"); ?>: '+ data);
									$("#resultmessage").fadeIn(); //show confirmation message
								}
								//
							});
					return false; //don't let the form refresh the page...
				}					
			});
		
		
			/**
			 * React to checking and unchecking of boxes -- Mark tasks as completed.
			 */
		    $('input', this).on('ifChecked', function(event) {
		        var ele = $(this).parents("li").first();
				// task ID is contained in the ID element of the li object.
				var task_id = ele[0].id;
				
				// clear current result field
				$("#changetaskresult").html();
				$("#changetaskresult").fadeOut();
				
				// mark item as "done" and call ModifyTask. 
		        ele.toggleClass("done");
				$.post("./php/CompleteTask.php", {"complete-task-taskid": task_id, "complete-task-progress": "100" }, 
				function(data){
					if (data == "<?php print CRM_DEFAULT_SUCCESS_RESPONSE; ?>") { location.reload(); }
					else {
						$("#changetaskresult").html(data);
						$("#changetaskresult").fadeIn();
					}
				});
		    });

		    $('input').iCheck({
		    	checkboxClass: 'icheckbox_minimal-blue',
				radioClass: 'iradio_minimal-blue'
		    });
		
		});
		
		</script>
		<!-- Modal Dialogs -->
		<?php include_once "./php/ModalPasswordDialogs.php" ?>


    </body>
</html>
