<?php

    ###########################################################
    ### Name: edittelephonyuser.php                         ###
    ### Functions: Edit Users                               ###
    ### Copyright: GOAutoDial Ltd. (c) 2011-2016            ###
    ### Version: 4.0                                        ###
    ### Written by: Alexander Abenoja                       ###
    ###             Noel Umandap                            ###
    ###             Demian Lizandro A. Biscocho             ###
    ### License: AGPLv2                                     ###
    ###########################################################
    
    require_once('./php/UIHandler.php');
    require_once('./php/CRMDefaults.php');
    require_once('./php/LanguageHandler.php');
    include('./php/Session.php');

    $ui = \creamy\UIHandler::getInstance();
    $lh = \creamy\LanguageHandler::getInstance();
    $user = \creamy\CreamyUser::currentUser();
    
    $userid = $user->getUserId();
    $userrole = $user->getUserRole();
    //$userid = NULL;
    //$userid = "1359";
    //if (isset($_POST["userid"])) {
    //        $userid = $_POST["userid"];
    //}
    //
    //if(isset($_POST["role"])){
    //        $userrole = $_POST["role"];
    //}  

?>

<html>
    <head>
        <meta charset="utf-8">
        <title>User Profile</title>
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
        <meta name="description" content="Bootstrap Admin App + jQuery">
        <meta name="keywords" content="app, responsive, jquery, bootstrap, dashboard, admin">
        
        <?php print $ui->standardizedThemeCSS(); ?>
        <?php print $ui->creamyThemeCSS(); ?>

        <!-- FONT AWESOME-->
        <link rel="stylesheet" href="theme_dashboard/fontawesome/css/font-awesome.min.css">
        <!-- SIMPLE LINE ICONS-->
        <link rel="stylesheet" href="theme_dashboard/simple-line-icons/css/simple-line-icons.css">
        <!-- ANIMATE.CSS-->
        <link rel="stylesheet" href="theme_dashboard/animate.css/animate.min.css">
        <!-- WHIRL (spinners)-->
        <link rel="stylesheet" href="theme_dashboard/whirl/dist/whirl.css">
        <!-- =============== PAGE VENDOR STYLES ===============-->
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
        
        <link rel="stylesheet" href="theme_dashboard/sweetalert/dist/sweetalert.css">
        <script src="theme_dashboard/sweetalert/dist/sweetalert.min.js"></script>
    

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
                <!-- Content Header (Page header) -->
                <!-- <section class="content-heading" style="margin-top: 20px">
                        <?php $lh->translateText("profile"); ?>
                        <small><?php $lh->translateText("users_management"); ?></small>
                </section> -->

         <!-- Page content-->        
         
<?php
            $userobj = NULL;
            $errormessage = NULL;            
            $output = $ui->goGetUserInfo($userid, "user_id");
            //echo ("pre");
            //var_dump($output);

            $userid = $output->data->user_id;
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
            $totalsalestoday = ($outsalestoday + $insalestoday);
            
            if ($incallstoday == NULL){
                $incallstoday = "0";
            }
            
            if ($outcallstoday == NULL){
                $outcallstoday = "0";
            }
            if ($incallstoday == NULL){
                $incallstoday = "0";
            }             
            
            $totalcallstoday = ($outcallstoday + $incallstoday);
            
            if ($status == "Y"){
                $status = "ACTIVE";
            }
            if ($status == "N"){
                $status = "INACTIVE";
            }

    $vmArray = $ui->API_goGetVoiceMails();
    //echo "<pre>";
    //print_r($vmArray);
    //die();
    //$vmCnt = count($vmArray->voicemail_id);
    
    foreach($vmArray->voicemail_id as $key => $value) {
        //var_dump($key, $value);                       
    
            foreach($vmArray->messages as $key2 => $value2){
                //var_dump($key2, $value2);
                if ($value == $voicemail_id) break;
                $vmid = $value;
                $vm_message = $value2;
            }    
    }
    //var_dump($vmid);
    //var_dump($vm_message);

?>
            <div class="unwrap ng-scope">
               <div style="background-image: url(img/profile-bg.jpg)" class="bg-cover">
                  <div class="p-xl text-center text-white">
                     <span style="display:table; margin:0 auto; background-color: #dadada; border: 3px solid #dadada; border-radius: 50%; margin-bottom: 10px; height: 128px; width: 128px;"><?=$ui->getVueAvatar($user->getUserName(), $user->getUserAvatar(), 124)?></span>
                     <h3 class="m0"><?php echo $user->getUserName(); ?></h3>
                     <p><?php echo $_SESSION['user']; ?></p>
                     <p>Empowering the next generation contact centers.</p>
                  </div>
               </div>
               <div class="text-center bg-gray-dark p-lg mb-xl">
                  <div class="row row-table" style="height: 7%">
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
                     <em class="fa fa-users"></em>
                  </div>
                  <div class="timeline-panel">
                     <div class="popover">
                        <h4 class="popover-title">Outbound Calls</h4>
                        <div class="arrow"></div>
                        <div class="popover-content">
                           <p>Calls today: <?php echo $outcallstoday; ?>
                              <br>
                              <small>Click to display all outbound calls for this day.</small>
                           </p>
                        </div>
                     </div>
                  </div>
               </li>
               <!-- END timeline item-->
               <!-- START timeline item-->
               <li class="timeline-inverted">
                  <div class="timeline-badge warning">
                     <em class="fa fa-phone"></em>
                  </div>
                  <div class="timeline-panel">
                     <div class="popover right">
                        <h4 class="popover-title">Inbound Calls</h4>
                        <div class="arrow"></div>
                        <div class="popover-content">
                           <p>Michael <a href="tel:+011654524578">(+011) 6545 24578 ext. 132</a>
                              <br>
                              <small>Click to display all inbound and closer calls for this day.</small>
                           </p>
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
                     <em class="fa fa-video-camera"></em>
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
                                       <em class="fa fa-download"></em>Download</a>
                                 </li>
                                 <li>
                                    <a href="#">
                                       <em class="fa fa-share"></em>Send to</a>
                                 </li>
                                 <li class="divider"></li>
                                 <li>
                                    <a href="#">
                                       <em class="fa fa-times"></em>Delete</a>
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
                     <em class="fa fa-plane"></em>
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
                     <em class="fa fa-music"></em>
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
                     <em class="fa fa-plus"></em>
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
                                    <img src="img/user/05.jpg" alt="Image" class="media-object img-circle thumb32">
                                 </span>
                                 <!-- Contact info-->
                                 <span class="media-body">
                                    <span class="media-heading">
                                       <strong>Juan Sims</strong>
                                       <br>
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
                                    <img src="img/user/06.jpg" alt="Image" class="media-object img-circle thumb32">
                                 </span>
                                 <!-- Contact info-->
                                 <span class="media-body">
                                    <span class="media-heading">
                                       <strong>Maureen Jenkins</strong>
                                       <br>
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
                                    <img src="img/user/07.jpg" alt="Image" class="media-object img-circle thumb32">
                                 </span>
                                 <!-- Contact info-->
                                 <span class="media-body">
                                    <span class="media-heading">
                                       <strong>Billie Dunn</strong>
                                       <br>
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
                                    <img src="img/user/08.jpg" alt="Image" class="media-object img-circle thumb32">
                                 </span>
                                 <!-- Contact info-->
                                 <span class="media-body">
                                    <span class="media-heading">
                                       <strong>Tomothy Roberts</strong>
                                       <br>
                                       <small class="text-muted">Designer</small>
                                    </span>
                                 </span>
                              </a>
                              <!-- END User status--><a href="contactsandcallrecordings.php" class="media p mt0 list-group-item text-center text-muted">View all contacts</a>
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
        </div><!-- ./wrapper -->



        <?php print $ui->creamyFooter();?>
    <!-- =============== VENDOR SCRIPTS ===============-->
    <!-- MODERNIZR-->
    <!--<script src="./vendor/modernizr/modernizr.custom.js"></script>-->
    <!-- MATCHMEDIA POLYFILL-->
    <!--<script src="./vendor/matchMedia/matchMedia.js"></script>-->
    <!-- JQUERY-->
    <!--<script src="./vendor/jquery/dist/jquery.js"></script>-->
    <!-- BOOTSTRAP-->
    <!--<script src="./vendor/bootstrap/dist/js/bootstrap.js"></script>-->
    <!-- STORAGE API-->
    <!--<script src="./vendor/jQuery-Storage-API/jquery.storageapi.js"></script>-->
    <!-- JQUERY EASING-->
    <!--<script src="./vendor/jquery.easing/js/jquery.easing.js"></script>-->
    <!-- ANIMO-->
    <!--<script src="./vendor/animo.js/animo.js"></script>-->
    <!-- SLIMSCROLL-->
    <!--<script src="./vendor/slimScroll/jquery.slimscroll.min.js"></script>-->
    <!-- SCREENFULL-->
    <!--<script src="./vendor/screenfull/dist/screenfull.js"></script>-->
    <!-- LOCALIZE-->
    <!--<script src="./vendor/jquery-localize-i18n/dist/jquery.localize.js"></script>-->
    <!-- RTL demo-->
    <!-- <script src="js/demo/demo-rtl.js"></script> -->
    <!-- =============== PAGE VENDOR SCRIPTS ===============-->
    <!-- GOOGLE MAPS-->
    <!--<script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=false"></script>-->
    <!--<script src="./vendor/jQuery-gMap/jquery.gmap.min.js"></script>-->
    <!-- =============== APP SCRIPTS ===============-->
    <!--<script src="js/app.js"></script>        -->
        
    <?php print $ui->standardizedThemeJS();?>
    </body>
</html>
