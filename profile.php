<?php
/**
 * @file 		profile.php
 * @brief 		Profile page
 * @copyright 	Copyright (c) 2018 GOautodial Inc. 
 * @author     	Alexander Jim H. Abenoja
 * @author		Demian Lizandro A. Biscocho
 * @author     	Noel Umandap
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
	require_once('./php/APIHandler.php');
	require_once('./php/CRMDefaults.php');
    require_once('./php/LanguageHandler.php');
    include('./php/Session.php');

	$ui = \creamy\UIHandler::getInstance();
	$api = \creamy\APIHandler::getInstance();
	$lh = \creamy\LanguageHandler::getInstance();
	$user = \creamy\CreamyUser::currentUser();
    
    $userid = $user->getUserId();
    $userrole = $user->getUserRole(); 

?>

<html>
    <head>
        <meta charset="utf-8">
        <title>User Profile</title>
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
        <meta name="description" content="Bootstrap Admin App + jQuery">
        <meta name="keywords" content="app, responsive, jquery, bootstrap, dashboard, admin">
        
        <?php 
			print $ui->standardizedThemeCSS(); 
			print $ui->creamyThemeCSS();
			print $ui->dataTablesTheme();
		?>            
            

        <!-- FONT AWESOME-->
        <link rel="stylesheet" src="js/dashboard/fontawesome/css/font-awesome.min.css">
        <!-- SIMPLE LINE ICONS-->
        <link rel="stylesheet" src="js/dashboard/simple-line-icons/css/simple-line-icons.css">
        <!-- ANIMATE.CSS-->
        <link rel="stylesheet" src="js/dashboard/animate.css/animate.min.css">
        <!-- WHIRL (spinners)-->
        <link rel="stylesheet" src="js/dashboard/whirl/dist/whirl.css">
        <!-- =============== PAGE VENDOR STYLES ===============-->
        <link rel="stylesheet" href="adminlte/css/AdminLTE.css">
        <!-- =============== BOOTSTRAP STYLES ===============-->
        <link rel="stylesheet" src="js/dashboard/css/bootstrap.css" id="bscss">
        <!-- =============== APP STYLES ===============-->
        <link rel="stylesheet" src="js/dashboard/css/app.css" id="maincss">
        <!-- <link rel="stylesheet" href="css/material/app.css" id="maincss"> -->
        <!-- preloader -->
        <link rel="stylesheet" href="css/customizedLoader.css">
        <!-- croppeie -->
        <link rel="Stylesheet" type="text/css" href="css/prism.css" />
        <link rel="Stylesheet" type="text/css" src="js/dashboard/sweetalert/dist/sweetalert.css" />        
        <link rel="Stylesheet" type="text/css" href="css/croppie.css" />
        <!-- <link rel="Stylesheet" type="text/css" href="css/demo.css" /> -->

        
        <script type="text/javascript">
            $(window).ready(function(){
                $(".preloader").fadeOut("slow");
            });
        </script>

        <style>
        .upload-demo .upload-demo-wrap,
        .upload-demo .upload-result,
        .upload-demo.ready .upload-msg {
            display: none;
        }
        .upload-demo.ready .upload-demo-wrap {
            display: block;
        }
        .upload-demo.ready .upload-result {
            display: inline-block;    
        }
        .upload-demo-wrap {
            width: 300px;
            height: 300px;
            margin: 0 auto;
        }
        .upload-msg {
            text-align: center;
            padding: 45px;
            font-size: 22px;
            color: #aaa;
            width: 200px;
            height: 200px;
            margin: 40px auto;
            border: 1px solid #aaa;
        }
        </style>
    </head>
        <section class="ng-scope">
    <?php print $ui->creamyBody(); ?>
        <div data-ui-view="" data-autoscroll="false" class="wrapper ng-scope">
            <!-- header logo: style can be found in header.less -->
                <?php print $ui->creamyHeader($user); ?>

            <!-- Left side column. contains the logo and sidebar -->
                <?php print $ui->getSidebar($user->getUserId(), $user->getUserName(), $user->getUserRole(), $user->getUserAvatar()); ?>

            <!-- Right side column. Contains the navbar and content of the page -->
            <aside class="content-wrapper">
            <!-- Page content-->        
         
<?php
            $userobj = NULL;
            $errormessage = NULL;            
            $output = $api->API_getUserInfo($userid);
            $creamyAvatar = $ui->getSessionAvatar();
            $sessionAvatar = "<div class='media'><avatar username='$agentname' src='$creamyAvatar' :size='32'></avatar></div>";
            //echo ("pre");
            //var_dump($output);

            $user_id = $output->data->user_id;
            $agentid = $output->data->user;
            $agentname =  $output->data->full_name;
            $email = $output->data->email;
            $user_group = $output->data->user_group;
            $status = $output->data->active;
            $voicemail_id = $output->data->voicemail_id;
            
            $outcallstoday = $output->data->outcallstoday;
            $outsalestoday = $output->data->outsalestoday;
            $incallstoday = $output->data->incallstoday;
            $insalestoday = $output->data->insalestoday;            
            
            if ($incallstoday == NULL){
                $incallstoday = 0;
            }
            if ($incallstoday == 0){
                $in_message_today01 = "No inbound calls yet today.";
                $in_message_today02 = "Showing latest inbound calls intead.";
                $in_calls_today = "";
            }else {
                $in_message_today = "Inbound calls today: ";
                $in_calls_today = $incallstoday;
            }         
                                        
            if ($outcallstoday == NULL){
                $outcallstoday = 0;
            } 
            if ($outcallstoday == 0){
                $out_message_today01 = "No outbound calls yet today.";
                $out_message_today02 = "Showing latest outbound calls intead.";
                $out_calls_today = "";
            }else {
                $out_message_today = "Outbound calls today: ";
                $out_calls_today = $outcallstoday;
            }            
            
            $totalcallstoday = ($outcallstoday + $incallstoday);
            $totalsalestoday = ($outsalestoday + $insalestoday);            
            
            if ($status == "Y"){
                $status = "ACTIVE";
            }
            if ($status == "N"){
                $status = "INACTIVE";
            }

    $vmArray = $api->API_getAllVoiceMails();
    //echo "<pre>";
    //print_r($vmArray);
    //die();    
    foreach($vmArray->voicemail_id as $key => $value) {
        //var_dump($key, $value);                       
    
            foreach($vmArray->messages as $key2 => $value2){
                //var_dump($key2, $value2);
                if ($value == $voicemail_id) break;
                $vmid = $value;
                $vm_message = $value2;
                
            }    
    }
    if ($vm_message == NULL){
        $vm_message = 0;
    }
?>
 
            <!-- <div class="unwrap ng-scope" style="margin-top: -30;"> -->
            <div class="unwrap ng-scope">
               <div style="background-image: url(img/profile-bg.jpg)" class="bg-cover">
                  <div class="p-xl text-center text-white">
                     <a href="#" data-toggle="modal" id="onclick-userinfo" data-target="#profile_pic_modal">
                     <span style="display:table; margin:0 auto; background-color: #ff902b; border: 3px solid #dadada; border-radius: 50%; margin-bottom: 10px; height: 128px; width: 128px;"><?=$ui->getVueAvatar($user->getUserName(), $user->getUserAvatar(), 128)?></span></a>
                     <h3 class="m0"><?php echo $user->getUserName(); ?></h3>
                     <p><?php echo $_SESSION['user']; ?></p>
                     <p>Empowering the next generation contact centers.</p>
                     <a href="#" class="btn btn-xs btn-primary pull-right" style="margin:10px;"><span class="fa fa-picture"></span> Change cover</a>
                  </div>
               </div>
               <div class="text-center bg-warning p-lg mb-xl">
                  <div class="row row-table" style="height: 47px">
                     <div class="col-xs-4 br">
                        <h3 class="m0"><?php echo $totalcallstoday; ?></h3>
                        <p class="m0">
                           <span class="hidden-xs">Calls Today</span>
                           <!-- <span>Views</span> -->
                        </p>
                     </div>                    
                     <div class="col-xs-4 br">
                        <h3 class="m0"><?php echo $totalsalestoday; ?></h3>
                        <p class="m0">Sales Today</p>
                     </div>
                     <div class="col-xs-4">
                        <h3 class="m0"><?php echo $vm_message; ?></h3>
                        <p class="m0">Voicemails</p>
                     </div>
                  </div>
               </div>
               <div class="p-lg">
                  <div class="row">
                     <div class="col-lg-9">
                        <!-- START timeline-->
                        <ul class="timeline">
                            <li data-datetime="Today" class="timeline-separator"></li>
                            <!-- START timeline item-->
                            <li>
                                <div class="timeline-badge primary">
                                    <em class="fa fa-phone" style="padding-top: 10px;"></em>
                                </div>
                                <div class="timeline-panel">
                                    <div class="popover left">
                                        <h4 class="popover-title"><a href="#" data-toggle="modal" data-target="#agent_latest_outbound_calls">Outbound Calls</a></h4>
                                        <div class="arrow"></div>
                                            <div class="popover-content">
                                                <!-- <p>Calls today: <?php echo $outcallstoday; ?></p> -->
                                                <!-- <p>No inbound calls yet today.</p> -->
                                                <p> <?php echo $out_message_today01; ?> <?php echo $out_calls_today; ?></p>
                                                <p class="text-muted mv"><?php echo $out_message_today02; ?></p>
                                            <div class="media bb p">
                                                <div class="media-body">
                                                    <!-- <a class="media p mt0 list-group-item"> -->
                                                    
                                                        <span id="refresh_agent_latest_outbound_calls_summary"></span>
                                                    
                                                    <!-- </a> -->
                                                </div>
                                            </div>
                                            </div>
                                    </div>                            
                                </div>
                            </li>
                            <!-- END timeline item-->
                        <!-- START timeline item-->
                        <li class="timeline-inverted">
                            <div class="timeline-badge warning">
                                <em class="fa fa-phone" style="padding-top: 10px;"></em>
                            </div>
                            <div class="timeline-panel">
                                <div class="popover right">
                                    <h4 class="popover-title"><a href="#" data-toggle="modal" data-target="#agent_latest_inbound_calls">Inbound Calls</a></h4>
                                        <div class="arrow"></div>
                                            <div class="popover-content">
                                                <!-- <p>Calls today: <?php echo $incallstoday; ?></p> -->
                                                <!-- <p>No inbound calls yet today.</p> -->
                                                <p> <?php echo $in_message_today01; ?> <?php echo $in_calls_today; ?></p>
                                                <p class="text-muted mv"><?php echo $in_message_today02; ?></p>
                                            <div class="media bb p">
                                                <div class="media-body">
                                                    <!-- <a class="media p mt0 list-group-item"> -->
                                                    
                                                        <span id="refresh_agent_latest_inbound_calls_summary"></span>
                                                    
                                                    <!-- </a> -->
                                                </div>
                                            </div>
                                    <!-- <div class="media bb p">
                                        <small class="pull-right text-muted">30m ago</small>
                                        <div class="pull-left">
                                            <img src="app/img/user/08.jpg" alt="Image" class="media-object img-circle thumb32" />
                                        </div>
                                        <div class="media-body">
                                            <span class="media-heading">
                                                <p class="m0">
                                                <a href="#">
                                                    <strong>Samantha Murphy</strong>
                                                </a>
                                                </p>
                                                <p class="m0 text-muted">Excellento job!</p>
                                            </span>
                                        </div>
                                    </div> -->
                                    <!-- <form method="post" action="" class="mt">
                                        <textarea placeholder="Comment..." rows="1" class="form-control no-resize"></textarea>
                                    </form> -->
                                    </div>
                                </div>
                            </div>
                        </li>
                        <!-- END timeline item-->
                        <!-- START timeline separator-->
                        <li data-datetime="Yesterday" class="timeline-separator"></li>
                        <!-- END timeline separator-->
                        <!-- START timeline item-->
                        <li>
                            <div class="timeline-badge danger">
                                <em class="fa fa-video-camera" style="padding-top: 10px;"></em>
                            </div>
                            <div class="timeline-panel">
                                <div class="popover">
                                    <h4 class="popover-title">Conference</h4>
                                    <div class="arrow"></div>
                                    <div class="popover-content">
                                    <p>Join development group</p>
                                    <small>
                                        <a href="skype:echo123?call">
                                            <em class="fa fa-phone"></em>Call the Skype Echo</a>
                                    </small>
                                    </div>
                                </div>
                            </div>
                        </li>
                        <!-- END timeline item-->
                        <!-- START timeline item-->
                        <li class="timeline-inverted">
                            <div class="timeline-panel">
                                <div class="popover right">
                                    <h4 class="popover-title">Appointment</h4>
                                    <div class="arrow"></div>
                                    <div class="popover-content">
                                    <p>Sed posuere consectetur est at lobortis. Aenean eu leo quam. Pellentesque ornare sem lacinia quam.</p>
                                    <div class="btn-group">
                                        <a href="#" data-toggle="dropdown" data-play="fadeIn" class="dropdown-toggle">
                                            <em class="fa fa-paperclip"></em>
                                        </a>
                                        <ul class="dropdown-menu text-left">
                                            <li>
                                                <a href="#">
                                                <em class="fa fa-download" style="padding-top: 10px;"></em>Download</a>
                                            </li>
                                            <li>
                                                <a href="#">
                                                <em class="fa fa-share" style="padding-top: 10px;"></em>Send to</a>
                                            </li>
                                            <li class="divider"></li>
                                            <li>
                                                <a href="#">
                                                <em class="fa fa-times" style="padding-top: 10px;"></em>Delete</a>
                                            </li>
                                        </ul>
                                    </div>
                                    </div>
                                </div>
                            </div>
                        </li>
                        <!-- END timeline item-->
                        <!-- START timeline item-->
                        <li>
                            <div class="timeline-badge info">
                                <em class="fa fa-plane" style="padding-top: 10px;"></em>
                            </div>
                            <div class="timeline-panel">
                                <div class="popover">
                                    <h4 class="popover-title">Fly</h4>
                                    <div class="arrow"></div>
                                    <div class="popover-content">
                                    <p>Sed posuere consectetur est at lobortis. Aenean eu leo quam. Pellentesque ornare sem lacinia quam venenatis vestibulum.</p>
                                    </div>
                                </div>
                            </div>
                        </li>
                        <!-- END timeline item-->
                        <!-- START timeline item-->
                        <li>
                            <div class="timeline-panel">
                                <div class="popover">
                                    <h4 class="popover-title">Appointment</h4>
                                    <div class="arrow"></div>
                                    <div class="popover-content">
                                    <p>Sed posuere consectetur est at lobortis. Aenean eu leo quam. Pellentesque ornare sem lacinia quam venenatis vestibulum.</p>
                                    </div>
                                </div>
                            </div>
                        </li>
                        <!-- END timeline item-->
                        <!-- START timeline separator-->
                        <li data-datetime="2014-05-21" class="timeline-separator"></li>
                        <!-- END timeline separator-->
                        <!-- START timeline item-->
                        <li class="timeline-inverted">
                            <div class="timeline-badge success">
                                <em class="fa fa-music" style="padding-top: 10px;"></em>
                            </div>
                            <div class="timeline-panel">
                                <div class="popover right">
                                    <h4 class="popover-title">Relax</h4>
                                    <div class="arrow"></div>
                                    <div class="popover-content">
                                    <p>Listen some music</p>
                                    </div>
                                </div>
                            </div>
                        </li>
                        <!-- END timeline item-->
                        <!-- START timeline item-->
                        <li class="timeline-end">
                            <a href="#" class="timeline-badge">
                                <em class="fa fa-plus" style="padding-top: 10px;"></em>
                            </a>
                        </li>
                        <!-- END timeline item-->
                        </ul>
                        <!-- END timeline-->
                     </div>
                     <div class="col-lg-3">
                        <div class="panel panel-default">
                           <div class="panel-body">
                              <div class="text-center">
                                 <h3 class="mt0"><?php echo $agentname; ?></h3>
                                 <p><?php echo $agentid; ?></p>
                              </div>
                              <hr>
                              <ul class="list-unstyled ph-xl">
                                 <li>
                                    <em class="fa fa-home fa-fw mr-lg"></em>Group: <?php echo $user_group; ?></li>
                                 <li>
                                    <em class="fa fa-briefcase fa-fw mr-lg"></em><a href="mailto:<?php echo $email; ?>"><?php echo $email; ?></a>
                                 </li>
                                 <li>
                                    <em class="fa fa-graduation-cap fa-fw mr-lg"></em>Status: <?php echo $status; ?></li>
                              </ul>
                           </div>
                        </div>
            <div class="panel panel-default">
               <div class="panel-heading">
                  <a href="#" class="pull-right">
                     <em class="icon-plus text-muted"></em>
                  </a>Contacts</div>
               <div class="list-group">
                  <!-- START User status-->
                  <a href="#" class="media p mt0 list-group-item">
                     <span class="pull-right">
                        <span class="circle circle-success circle-lg"></span>
                     </span>
                     <span class="pull-left">
                        <!-- Contact avatar-->
                        <img src="img/user/05.jpg" alt="Image" class="media-object img-circle thumb32" />
                     </span>
                     <!-- Contact info-->
                     <span class="media-body">
                        <span class="media-heading">
                           <strong>Juan Sims</strong>
                           <br/>
                           <small class="text-muted">Designeer</small>
                        </span>
                     </span>
                  </a>
                  <!-- END User status-->
                  <!-- START User status-->
                  <a href="#" class="media p mt0 list-group-item">
                     <span class="pull-right">
                        <span class="circle circle-success circle-lg"></span>
                     </span>
                     <span class="pull-left">
                        <!-- Contact avatar-->
                        <img src="img/user/06.jpg" alt="Image" class="media-object img-circle thumb32" />
                     </span>
                     <!-- Contact info-->
                     <span class="media-body">
                        <span class="media-heading">
                           <strong>Maureen Jenkins</strong>
                           <br/>
                           <small class="text-muted">Designeer</small>
                        </span>
                     </span>
                  </a>
                  <!-- END User status-->
                  <!-- START User status-->
                  <a href="#" class="media p mt0 list-group-item">
                     <span class="pull-right">
                        <span class="circle circle-danger circle-lg"></span>
                     </span>
                     <span class="pull-left">
                        <!-- Contact avatar-->
                        <img src="img/user/07.jpg" alt="Image" class="media-object img-circle thumb32" />
                     </span>
                     <!-- Contact info-->
                     <span class="media-body">
                        <span class="media-heading">
                           <strong>Billie Dunn</strong>
                           <br/>
                           <small class="text-muted">Designeer</small>
                        </span>
                     </span>
                  </a>
                  <!-- END User status-->
                  <!-- START User status-->
                  <a href="#" class="media p mt0 list-group-item">
                     <span class="pull-right">
                        <span class="circle circle-warning circle-lg"></span>
                     </span>
                     <span class="pull-left">
                        <!-- Contact avatar-->
                        <img src="img/user/08.jpg" alt="Image" class="media-object img-circle thumb32" />
                     </span>
                     <!-- Contact info-->
                     <span class="media-body">
                        <span class="media-heading">
                           <strong>Tomothy Roberts</strong>
                           <br/>
                           <small class="text-muted">Designer</small>
                        </span>
                     </span>
                  </a>
                  <!-- END User status--><a href="crm.php" class="media p mt0 list-group-item text-center text-muted">View all contacts</a>
               </div>
            </div>                       
                        <div class="panel panel-default">
                           <div class="list-group">
                              <a href="#" class="list-group-item">
                                 <em class="pull-right fa fa-users fa-lg fa-fw text-muted mt"></em>
                                 <h4 class="list-group-item-heading">1000</h4>
                                 <p class="list-group-item-text">Friends</p>
                              </a>
                              <a href="#" class="list-group-item">
                                 <em class="pull-right fa fa-rss fa-lg fa-fw text-muted mt"></em>
                                 <h4 class="list-group-item-heading">3000</h4>
                                 <p class="list-group-item-text">Subscriptions</p>
                              </a>
                              <a href="#" class="list-group-item">
                                 <em class="pull-right fa fa-map-marker fa-lg fa-fw text-muted mt"></em>
                                 <h4 class="list-group-item-heading">100</h4>
                                 <p class="list-group-item-text">Places</p>
                              </a>
                              <a href="#" class="list-group-item">
                                 <em class="pull-right fa fa-briefcase fa-lg fa-fw text-muted mt"></em>
                                 <h4 class="list-group-item-heading">400</h4>
                                 <p class="list-group-item-text">Projects</p>
                              </a>
                              <a href="#" class="list-group-item">
                                 <em class="pull-right fa fa-twitter fa-lg fa-fw text-muted mt"></em>
                                 <h4 class="list-group-item-heading">17300</h4>
                                 <p class="list-group-item-text">Twees</p>
                              </a>
                           </div>
                        </div>
                     </div>
                  </div>
               </div>
            </div>
         </div>
      </section><!-- /.content -->
            </aside><!-- /.right-side -->         
            <?php print $ui->getRightSidebar($user->getUserId(), $user->getUserName(), $user->getUserAvatar()); ?>       
            <?php print $ui->creamyFooter();?>

        
<!--================= MODALS =====================-->

    <!-- Upload Avatar --> 
    
                    <div class="modal fade" id="profile_pic_modal" tabindex="-1" role="dialog" aria-hidden="true"> 
                        <div class="modal-dialog"> 
                            <div class="modal-content"> 
                                <div class="modal-header"> 
                                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button> 
                                    <h4 class="modal-title">Change your profile picture</h4> 
                                </div> 
                                <div class="modal-body" style="min-height: 45%;"> 
                                    <center> 
                                        <div class="upload-demo col-1-2">
                                            <div class="upload-msg">
                                                Upload a file to start cropping
                                            </div>
                                            <div class="upload-demo-wrap">
                                                <div id="upload-demo"></div>
                                            </div>
                                        </div>
                                        <div class="col-1-2">
                                            <!--<h3 class="media-heading">Upload Example (with exif orientation compatability)</h3> -->
                                            <div class="actions">
                                                <a class="btn file-btn">
                                                    <input type="file" id="upload" value="Choose a file" accept="image/*" />
                                                </a>                                                        
                                            </div>
                                        </div>
                                    </center>
                                </div> 
                                <div class="modal-footer">                                        
                                    <div class="pull-right">
                                        <button class="btn btn-default btn-sm upload-result" disabled>Preview</button> 
                                        <button class="btn btn-warning btn-sm upload-submit" disabled>Submit</button>
                                    </div>
                                    <div class="pull-left">
                                        <button class="btn btn-default btn-sm" data-dismiss="modal">Cancel</button>
                                    </div>
                                </div> 
                            </div> 
                        </div> 
                    </div>                      

    <!-- End of Upload Avatar -->    
    <!-- Agent Latest Outbound Calls -->

                    <div class="modal fade" id="agent_latest_outbound_calls" tabindex="-1" role="dialog" aria-hidden="true">
                        <div class="modal-lg modal-dialog" style="min-width: 75%">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                                    <h4>Latest Outbound Phone Calls (100 limit)</h4>
                                </div>
                                <div class="modal-body">
                                    <div class="responsive">
                                    <!-- <div class="col-sm-12">-->
                                        <table class="table table-striped table-hover display compact" id="agent_latest_outbound_calls_table" style="width: 100%">
                                            <thead>
                                                <th style="color: white;">Pic</th>
                                                <th style="font-size: small;">Lead ID</th>
                                                <th style="font-size: small;">Customer</th>
                                                <!-- <th style="font-size: small;">List ID</th> -->
                                                <th style="font-size: small;">Campaign ID</th>                                                                
                                                <th style="font-size: small;">Phone Number</th>
                                                <th style="font-size: small;">Status</th>
                                                <!-- <th style="font-size: small;">Agent</th> -->
                                                <th style="font-size: small;">Call Date</th>
                                                <th style="font-size: small;">Duration</th>
                                            </thead>
                                            <tbody>
                                            
                                            </tbody>
                                        </table>
                                    <!--</div>-->
                                    </div>
                                </div>
                            </div>
                        </div>	
                    </div>
                     
    <!-- End of Agent Latest Outbound Calls -->     
    <!-- Agent Latest Inbound Calls -->

                    <div class="modal fade" id="agent_latest_inbound_calls" tabindex="-1" role="dialog" aria-hidden="true">
                        <div class="modal-lg modal-dialog" style="min-width: 75%">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                                    <h4>Latest Inbound Phone Calls (100 limit)</h4>
                                </div>
                                <div class="modal-body">
                                    <div class="responsive">
                                    <!-- <div class="col-sm-12">-->
                                        <table class="table table-striped table-hover display compact" id="agent_latest_inbound_calls_table" style="width: 100%">
                                            <thead>
                                                <th style="color: white;">Pic</th>
                                                <th style="font-size: small;">Lead ID</th>
                                                <th style="font-size: small;">Customer</th>
                                                <!-- <th style="font-size: small;">List ID</th> -->
                                                <th style="font-size: small;">Campaign ID</th>                                                                
                                                <th style="font-size: small;">Phone Number</th>
                                                <th style="font-size: small;">Status</th>
                                                <!-- <th style="font-size: small;">Agent</th> -->
                                                <th style="font-size: small;">Call Date</th>
                                                <th style="font-size: small;">Duration</th>
                                            </thead>
                                            <tbody>
                                            
                                            </tbody>
                                        </table>
                                    <!--</div>-->
                                    </div>
                                </div>
                            </div>
                        </div>	
                    </div>
                     
    <!-- End of Agent Latest Inbound Calls -->     
    <!-- Lead Information -->
                        
    <div class="modal fade" id="view_lead_information" tabindex="-1" role="dialog" aria-hidden="true"> 
        <div class="modal-dialog"> 
            <div class="modal-content"> 
                <div class="modal-header"> 
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button> 
                        <h4 class="modal-title">More about lead ID: <span id="modal-lead_id"></span></h4> 
                </div> 
                <div class="modal-body"> 
                    <center> 
                        <div id="modal-avatar-lead"></div>
                        <!--<img src="img/avatars/demian_avatar.jpg" name="aboutme" width="160" height="160" border="0" class="img-circle">-->
                            <h3 class="media-heading"><span id="modal-full_name"></span><small>  <span id="modal-country_code"></span></small></h3> 
                                <span>Address: </span> 
                                    <span class="label label-info" id="modal-address1"></span> 
                                    <span class="label label-info" id="modal-city"></span> 
                                    <span class="label label-warning" id="modal-state"></span>
                                    <span class="label label-success" id="modal-postal_code"></span><br/>                                
                    </center> 
                        <div class="responsive">
                            <table class="table table-striped table-hover" id="view_lead_information_table" style="width: 100%">
                                <thead>
                                    <th style="font-size: small;">Phone Number</th> 
                                    <th style="font-size: small;">List ID</th>
                                    <!-- <th style="font-size: small;">Campaign ID</th> -->                                                            
                                    <!-- <th style="font-size: small;">Phone Number</th> -->
                                    <th style="font-size: small;">Status</th>
                                    <th style="font-size: small;">Agent</th>
                                </thead>
                                <tbody>
                                    <tr>
                                    <td><span id="modal-phone_number"></span></td>
                                    <td><span id="modal-list_id"></span></td>
                                    <!-- <td><span id="modal-campaign_id"></td> -->
                                    <!-- <td><span id="modal-phone_number"></td> -->
                                    <td><span id="modal-status"></span></td>
                                    <td><span id="modal-agent"></span></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                </div> 
                <div class="modal-footer">                                        
                    <!-- <center> 
                        <button type="button" class="btn btn-default" data-dismiss="modal">I'm done</button> 
                    </center> -->
                </div> 
            </div> 
        </div> 
    </div> 

<?php
        /*
        * Modal Dialogs
        */
        include_once ("./php/ModalPasswordDialogs.php");
?>

    <!-- End of Lead Information -->    
    
<script type="text/javascript">

    function load_agent_latest_outbound_calls(userid){    
        //var userid = <?=$userid?>;         
        $.ajax({
            type: 'POST',
            url: "./php/dashboard/API_getAgentLatestOutboundCalls.php",
            cache: false,
            data: {user_id: userid},
            dataType: 'json',
            success: function(values){
                var JSONStringlatest_calls = values;
                var JSONObjectlatest_calls = JSON.parse(JSONStringlatest_calls);                
                console.log(JSONObjectlatest_calls); 
                var table = $('#agent_latest_outbound_calls_table').dataTable({
                    data:JSONObjectlatest_calls,
                    "destroy":true,
                    //"searching": false,
                    stateSave: true,
                    drawCallback: function(settings) {
                        var pagination = $(this).closest('.dataTables_wrapper').find('.dataTables_paginate');
                            pagination.toggle(this.api().page.info().pages > 1);
                    },                    
                    "columnDefs": [
                        {
                            className: "hidden-xs", 
                            "targets": [ 2, 4, 5, 7 ] 
                        }
                    ]                                    
                });
                goAvatar._init(goOptions);
            } 
        });
    }
    
    function load_agent_latest_inbound_calls(){    
        var userid = <?=$userid?>;         
        $.ajax({
            type: 'POST',
            url: "./php/dashboard/API_getAgentLatestInboundCalls.php",
            cache: false,
            data: {user_id: userid},
            dataType: 'json',
            success: function(values){
                var JSONStringlatest_calls = values;
                var JSONObjectlatest_calls = JSON.parse(JSONStringlatest_calls);                
                console.log(JSONObjectlatest_calls); 
                var table = $('#agent_latest_inbound_calls_table').dataTable({
                    data:JSONObjectlatest_calls,
                    "destroy":true,
                    //"searching": false,
                    stateSave: true,
                    drawCallback: function(settings) {
                        var pagination = $(this).closest('.dataTables_wrapper').find('.dataTables_paginate');
                            pagination.toggle(this.api().page.info().pages > 1);
                    },                    
                    "columnDefs": [
                        {
                            className: "hidden-xs", 
                            "targets": [ 2, 4, 5, 7 ] 
                        }
                    ]                                    
                });
                goAvatar._init(goOptions);
            } 
        });
    }    
    
    function load_agent_latest_outbound_calls_summary(){    
        var userid = <?=$userid?>;         
        $.ajax({
            type: 'POST',
            url: "./php/dashboard/API_getAgentLatestOutboundCallsSummary.php",
            cache: false,
            data: {user_id: userid},
            success: function(data){
                $("#refresh_agent_latest_outbound_calls_summary").html(data);
                goAvatar._init(goOptions);
            } 
        });
    }
    
    function load_agent_latest_inbound_calls_summary(){    
        var userid = <?=$userid?>;         
        $.ajax({
            type: 'POST',
            url: "./php/dashboard/API_getAgentLatestInboundCallsSummary.php",
            cache: false,
            data: {user_id: userid},
            success: function(data){
                $("#refresh_agent_latest_inbound_calls_summary").html(data);
                goAvatar._init(goOptions);
            } 
        });
    }
    
    // Clear lead information
    function clear_lead_information_form() {
        $('#modal-leadID').html("");
        $('#modal-list_id').html("");
        $('#modal-first_name').html("");
        $('#modal-last_name').html("");
        $('#modal-phone_number').html("");
        $('#modal-address1').html("");     
        $('#modal-city').html("");
        $('#modal-state').html("");
        $('#modal-postal_code').html("");
        $('#modal-country_code').html("");
        $('#modal-full_name').html("");
        $('#modal-avatar-lead').html("");  
        $('#modal-agent').html("");
        $('#modal-status').html("");        
    }

	function popupResult(result) {
		var html;
		if (result.html) {
			html = result.html;
		}
		if (result.src) {
			html = '<img src="' + result.src + '" style="border-radius: 50%; border: 3px solid #dadada;" /><br><br><h3 class="m0" style="color: #333;"><?php echo $user->getUserName(); ?></h3><p style="color: #333;"><?php echo $_SESSION['user']; ?></p>';
		}
		swal({
			title: '',
			html: true,
			text: html,
			allowOutsideClick: true
		});
	}

	function popupSubmit(result) {
        var userid = <?=$userid?>;  
        var imgArray = result.src.split(";");
        var img_type = imgArray[0].replace("data:", "");
            imgArray = imgArray[1].split(",");
        var img_data = imgArray[1];
        
        $.ajax({
            type: 'POST',
            url: "./php/SaveImage.php",
            data: {
                user_id: userid,
                type: img_type,
                image: img_data
            },
            cache: false,
            success: function(result) {
                if (result == 'success') {
                    swal({
                        title: "SUCCESS!",
                        text: "Uploaded a new image on your profile.",
                        closeOnConfirm: false
                    }, function() {
                        location.reload();
                    });
                } else {
                    swal({
                        title: "ERROR!",
                        text: "Please re-upload the image file you want to use.",
                        type: "warning"
                    });
                }
            }
        });
	}
    
    //demian
    $(document).ready(function() {
        // Clear previous lead info
        $('#view_lead_information').on('hidden.bs.modal', function () {
            clear_lead_information_form();
        }); 
        
        // Get lead information 
        $(document).on('click','#onclick-leadinfo',function(){
            var leadid = $(this).attr('data-id');
            var log_user = '<?=$_SESSION['user']?>';
            var log_group = '<?=$_SESSION['usergroup']?>';

            $.ajax({
                type: 'POST',
                url: "./php/ViewContact.php",
                data: {lead_id: leadid, log_user: log_user, log_group: log_group},
                cache: false,
                //dataType: 'json',
                success: function(data){ 
                    //console.log(data);
                    var JSONStringleadinfo = data;
                    var JSONObjectleadinfo = JSON.parse(JSONStringleadinfo);  
                    var fname = JSONObjectleadinfo.data.first_name;
                    var lname = JSONObjectleadinfo.data.last_name;
                    var full_name = fname+' '+lname;
                    var avatar = '<avatar username="'+full_name+'" :size="160"></avatar>';                        
                    //console.log(JSONObjectleadinfo);
                    $('#modal-lead_id').html(JSONObjectleadinfo.data.lead_id);
                    $('#modal-list_id').html(JSONObjectleadinfo.data.list_id);                    
                    $('#modal-first_name').html(JSONObjectleadinfo.data.first_name);
                    $('#modal-last_name').html(JSONObjectleadinfo.data.last_name);
                    $('#modal-phone_number').html(JSONObjectleadinfo.data.phone_number);
                    $('#modal-address1').html(JSONObjectleadinfo.data.address1);  
                    $('#modal-city').html(JSONObjectleadinfo.data.city);
                    $('#modal-state').html(JSONObjectleadinfo.data.state);
                    $('#modal-postal_code').html(JSONObjectleadinfo.data.postal_code); 
                    $('#modal-country_code').html(JSONObjectleadinfo.data.country_code);
                    $('#modal-agent').html(JSONObjectleadinfo.data.user);
                    $('#modal-status').html(JSONObjectleadinfo.data.status);
                    $('#modal-campaign_id').html(JSONObjectleadinfo.data.campaign_id);                                            
                    $('#modal-full_name').html(full_name);
                    $('#modal-avatar-lead').html(avatar);
                    goAvatar._init(goOptions);
                }
            });  
        });

        // Clear previous  info
        $('#profile_pic_modal').on('hidden.bs.modal', function () {
            $('#upload-demo').html("");
            $('#upload').val('');
            $('.upload-demo').removeClass('ready');
            $(".upload-result").prop('disabled', true);
            $(".upload-submit").prop('disabled', true);
            //$('#upload').html("");
            //$('#upload-result').html("");
        });
        
        
        // Get user information and post results in profile_pic_modal modal
        $(document).on('click','#onclick-userinfo', function() {
            var agentid = '<?=$agentid?>';
            var userid = '<?=$userid?>';
            var agentname = '<?=$agentname?>';
            //var creamyavatar = '<?//=$creamyAvatar?>';
            var $uploadCrop;

            function readFile(input) {
                if (input.files && input.files[0]) {
                    var reader = new FileReader();
                    
                    reader.onload = function (e) {
                        $('.upload-demo').addClass('ready');
                        $uploadCrop.croppie('bind', {
                            url: e.target.result
                        }).then(function(){
                            console.log('jQuery bind complete');
                        });
                    };
                    
                    reader.readAsDataURL(input.files[0]);
                    $(".upload-result").removeAttr('disabled');
                    $(".upload-submit").removeAttr('disabled');
                } else {
                    swal("Sorry - you're browser doesn't support the FileReader API");
                }
            }

            $uploadCrop = $('#upload-demo').croppie({
                enableExif: true,
                viewport: {
                    width: 160,
                    height: 160,
                    type: 'circle'
                },
                boundary: {
                    width: 200,
                    height: 200
                }
            });

            $('#upload').off('change');
            $('.upload-result').off('click');
            $('.upload-submit').off('click');
            $('#upload').on('change', function () { readFile(this); });
            $('.upload-result').on('click', function () {
                $uploadCrop.croppie('result', {
                    type: 'canvas',
                    size: 'viewport'
                }).then(function (resp) {
                    popupResult({
                        src: resp
                    });
                });
            });
            $('.upload-submit').on('click', function () {
                $uploadCrop.croppie('result', {
                    type: 'canvas',
                    size: 'viewport'
                }).then(function (resp) {
                    popupSubmit({
                        src: resp
                    });
                });
            });
        });
        
        // ---- loads datatable functions  
        load_agent_latest_outbound_calls();
        load_agent_latest_inbound_calls();
        load_agent_latest_outbound_calls_summary();
        load_agent_latest_inbound_calls_summary();
    });
</script>

    <!-- ANIMO-->
    <script src="js/dashboard/js/animo.js/animo.js"></script>
    <!-- SLIMSCROLL-->
    <script src="js/dashboard/js/slimScroll/jquery.slimscroll.min.js"></script>
    <!-- =============== APP SCRIPTS ===============-->
    <script src="js/prism.js"></script>
    <script src="js/croppie.js"></script> 
    <!-- <script src="js/demo.js"></script> -->
    <script src="js/exif.js"></script>
    
    <?php print $ui->standardizedThemeJS();?>
    </body>
</html>
