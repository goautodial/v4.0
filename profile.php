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
    
    //$userid = NULL;
    $userid = "1359";
    if (isset($_POST["userid"])) {
            $userid = $_POST["userid"];
    }

    if(isset($_POST["role"])){
            $userrole = $_POST["role"];
    }

    $voicemails = $ui->API_goGetVoiceMails();
    $user_groups = $ui->API_goGetUserGroupsList();

?>

<html>
    <head>
        <meta charset="utf-8">
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
                        <small><?php $lh->translateText("users_management"); ?></small>
                    </h1>
                    <ol class="breadcrumb">
                        <li><a href="./index.php"><i class="fa fa-phone"></i> <?php $lh->translateText("home"); ?></a></li>
                        <li><?php $lh->translateText("telephony"); ?></li>
                        <li class="active"><?php $lh->translateText("users"); ?>
                    </ol>

         <!-- Page content-->
         <div class="content-wrapper" style="margin-left: -1">
<?php
            $userobj = NULL;
            $errormessage = NULL;

            $output = $ui->goGetUserInfo($userid, $userrole);
            //echo ("pre");
            //print_r($output);

            $userid = $output->data->user_id;
            $agentid = $output->data->user;
            $agentname =  $output->data->full_name;
            $email = $output->data->email;
            $user_group = $output->data->user_group;
            $status = $output->data->active;
            $outcallstoday = $output->data->outcallstoday;
            $outsalestoday = $output->data->outsalestoday;
            $incallstoday = $output->data->incallstoday;
            $insalestoday = $output->data->insalestoday;
            $totalsalestoday = ($outsalestoday + $insalestoday);
            
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
    
?>
            <div class="unwrap">
               <div style="background-image: url(img/profile-bg.jpg)" class="bg-cover">
                  <div class="p-xl text-center text-white">
                     <img src="img/avatars/demian_avatar.jpg" alt="Image" class="img-thumbnail img-circle thumb128">
                     <h3 class="m0"><?php echo $agentname; ?></h3>
                     <p><?php echo $agentid; ?></p>
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
                        <h3 class="m0">100</h3>
                        <p class="m0">Tickets</p>
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
                                 <em class="fa fa-comment"></em>
                              </div>
                              <div class="timeline-panel">
                                 <div class="popover left">
                                    <div class="arrow"></div>
                                    <div class="popover-content">
                                       <div class="table-grid table-grid-align-middle mb">
                                          <div class="col col-xs">
                                             <img src="img/user/05.jpg" alt="Image" class="media-object img-circle thumb48">
                                          </div>
                                          <div class="col">
                                             <p class="m0">
                                                <a href="#" class="text-muted">
                                                   <strong>Aiden Curtis</strong>
                                                </a>posted a comment</p>
                                          </div>
                                       </div>
                                       <p>
                                          <em>"Fusce pellentesque congue justo in rutrum. Praesent non nulla et ligula luctus mattis eget at lacus."</em>
                                       </p>
                                    </div>
                                 </div>
                              </div>
                           </li>
                           <!-- END timeline item-->
                           <!-- START timeline item-->
                           <li class="timeline-inverted">
                              <div class="timeline-badge green">
                                 <em class="fa fa-picture-o"></em>
                              </div>
                              <div class="timeline-panel">
                                 <div class="popover right">
                                    <div class="arrow"></div>
                                    <div class="popover-content">
                                       <div class="table-grid table-grid-align-middle mb">
                                          <div class="col col-xs">
                                             <img src="img/user/04.jpg" alt="Image" class="media-object img-circle thumb48">
                                          </div>
                                          <div class="col">
                                             <p class="m0">
                                                <a href="#" class="text-muted">
                                                   <strong>James Payne</strong>
                                                </a>shared a new idea</p>
                                          </div>
                                       </div>
                                       <a href="#">
                                          <img src="img/mockup.png" alt="Img" class="img-responsive">
                                       </a>
                                       <p class="text-muted mv">3 Comments</p>
                                       <div class="media bb p">
                                          <small class="pull-right text-muted">12m ago</small>
                                          <div class="pull-left">
                                             <img src="img/user/05.jpg" alt="Image" class="media-object img-circle thumb32">
                                          </div>
                                          <div class="media-body">
                                             <div class="media-heading">
                                                <p class="m0">
                                                   <a href="#">
                                                      <strong>Aiden Curtis</strong>
                                                   </a>
                                                </p>
                                                <p class="m0 text-muted">Hey looks great!</p>
                                             </div>
                                          </div>
                                       </div>
                                       <div class="media bb p">
                                          <small class="pull-right text-muted">30m ago</small>
                                          <div class="pull-left">
                                             <img src="img/user/08.jpg" alt="Image" class="media-object img-circle thumb32">
                                          </div>
                                          <div class="media-body">
                                             <div class="media-heading">
                                                <p class="m0">
                                                   <a href="#">
                                                      <strong>Samantha Murphy</strong>
                                                   </a>
                                                </p>
                                                <p class="m0 text-muted">Excellento job!</p>
                                             </div>
                                          </div>
                                       </div>
                                       <div class="media bb p">
                                          <small class="pull-right text-muted">30m ago</small>
                                          <div class="pull-left">
                                             <img src="img/user/04.jpg" alt="Image" class="media-object img-circle thumb32">
                                          </div>
                                          <div class="media-body">
                                             <div class="media-heading">
                                                <p class="m0">
                                                   <a href="#">
                                                      <strong>James Payne</strong>
                                                   </a>
                                                </p>
                                                <p class="m0 text-muted">WIP guys :)</p>
                                             </div>
                                          </div>
                                       </div>
                                       <form method="post" action="#" class="mt">
                                          <textarea placeholder="Comment..." rows="1" class="form-control no-resize"></textarea>
                                       </form>
                                    </div>
                                 </div>
                              </div>
                           </li>
                           <!-- START timeline item-->
                           <li>
                              <div class="timeline-badge info">
                                 <em class="fa fa-file-o"></em>
                              </div>
                              <div class="timeline-panel">
                                 <div class="popover left">
                                    <div class="arrow"></div>
                                    <div class="popover-content">
                                       <div class="table-grid table-grid-align-middle mb">
                                          <div class="col col-xs">
                                             <img src="img/user/08.jpg" alt="Image" class="media-object img-circle thumb48">
                                          </div>
                                          <div class="col">
                                             <p class="m0">
                                                <a href="#" class="text-muted">
                                                   <strong>Samantha Murphy</strong>
                                                </a>shared new files</p>
                                          </div>
                                       </div>
                                       <ul class="list-unstyled">
                                          <li class="pb">
                                             <em class="fa fa-file-o fa-fw mr"></em><a href="#" class="text-info">framework-docs-part1.pdf<em class="pull-right fa fa-download fa-fw"></em></a>
                                          </li>
                                          <li class="pb">
                                             <em class="fa fa-file-o fa-fw mr"></em><a href="#" class="text-info">framework-docs-part2.pdf<em class="pull-right fa fa-download fa-fw"></em></a>
                                          </li>
                                          <li class="pb">
                                             <em class="fa fa-file-o fa-fw mr"></em><a href="#" class="text-info">framework-docs-part3.pdf<em class="pull-right fa fa-download fa-fw"></em></a>
                                          </li>
                                       </ul>
                                    </div>
                                 </div>
                              </div>
                           </li>
                           <!-- END timeline item-->
                           <!-- START timeline item-->
                           <li>
                              <div class="timeline-badge purple">
                                 <em class="fa fa-map-marker"></em>
                              </div>
                              <div class="timeline-panel">
                                 <div class="popover left">
                                    <div class="arrow"></div>
                                    <div class="popover-content">
                                       <div class="table-grid table-grid-align-middle mb">
                                          <div class="col col-xs">
                                             <img src="img/user/08.jpg" alt="Image" class="media-object img-circle thumb48">
                                          </div>
                                          <div class="col">
                                             <p class="m0">
                                                <a href="#" class="text-muted">
                                                   <strong>Samantha Murphy</strong>
                                                </a>shared new location</p>
                                          </div>
                                       </div>
                                       <p>
                                          <em>"Hey guys! Please check the new location for tomorrows's meeting."</em>
                                       </p>
                                       <div data-gmap="" data-address="276 N TUSTIN ST, ORANGE, CA 92867" data-styled class="gmap"></div>
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
                              <div class="timeline-badge success">
                                 <em class="fa fa-ticket"></em>
                              </div>
                              <div class="timeline-panel">
                                 <div class="popover left">
                                    <div class="arrow"></div>
                                    <div class="popover-content">
                                       <div class="table-grid table-grid-align-middle mb">
                                          <div class="col col-xs">
                                             <img src="img/user/12.jpg" alt="Image" class="media-object img-circle thumb48">
                                          </div>
                                          <div class="col">
                                             <p class="m0">
                                                <a href="#" class="text-muted">
                                                   <strong>Dennis Green</strong>
                                                </a>closed issue <a href="#">#548795</a>
                                             </p>
                                             <p class="m0">
                                                <em>&mdash; bootstrap.js needs update</em>
                                             </p>
                                          </div>
                                       </div>
                                    </div>
                                 </div>
                              </div>
                           </li>
                           <!-- END timeline item-->
                           <li class="timeline-inverted">
                              <div class="timeline-badge warning">
                                 <em class="fa fa-ticket"></em>
                              </div>
                              <div class="timeline-panel">
                                 <div class="popover right">
                                    <div class="arrow"></div>
                                    <div class="popover-content">
                                       <div class="table-grid table-grid-align-middle mb">
                                          <div class="col col-xs">
                                             <img src="img/user/09.jpg" alt="Image" class="media-object img-circle thumb48">
                                          </div>
                                          <div class="col">
                                             <p class="m0">
                                                <a href="#" class="text-muted">
                                                   <strong><?php echo $agentname; ?></strong>
                                                </a>assigned
                                                <a href="#" class="text-muted">
                                                   <strong>Dennis Green</strong>
                                                </a>to issue <a href="#">#548795</a>
                                             </p>
                                             <p class="m0">
                                                <em>&mdash; bootstrap.js needs update</em>
                                             </p>
                                          </div>
                                       </div>
                                    </div>
                                 </div>
                              </div>
                           </li>
                           <!-- END timeline item-->
                           <!-- START timeline item-->
                           <li>
                              <div class="timeline-badge danger">
                                 <em class="fa fa-ticket"></em>
                              </div>
                              <div class="timeline-panel">
                                 <div class="popover left">
                                    <div class="arrow"></div>
                                    <div class="popover-content">
                                       <div class="table-grid table-grid-align-middle mb">
                                          <div class="col col-xs">
                                             <img src="img/user/10.jpg" alt="Image" class="media-object img-circle thumb48">
                                          </div>
                                          <div class="col">
                                             <p class="m0">
                                                <a href="#" class="text-muted">
                                                   <strong>Jon Perry</strong>
                                                </a>opened issue <a href="#">#548795</a>
                                             </p>
                                             <p class="m0">
                                                <em>&mdash; bootstrap.js needs update</em>
                                             </p>
                                          </div>
                                       </div>
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
                              <!-- END User status--><a href="#" class="media p mt0 list-group-item text-center text-muted">View all contacts</a>
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
        </div><!-- ./wrapper -->



        <?php print $ui->creamyFooter();?>
    <!-- =============== VENDOR SCRIPTS ===============-->
    <!-- MODERNIZR-->
    <script src="./vendor/modernizr/modernizr.custom.js"></script>
    <!-- MATCHMEDIA POLYFILL-->
    <script src="./vendor/matchMedia/matchMedia.js"></script>
    <!-- JQUERY-->
    <script src="./vendor/jquery/dist/jquery.js"></script>
    <!-- BOOTSTRAP-->
    <script src="./vendor/bootstrap/dist/js/bootstrap.js"></script>
    <!-- STORAGE API-->
    <script src="./vendor/jQuery-Storage-API/jquery.storageapi.js"></script>
    <!-- JQUERY EASING-->
    <script src="./vendor/jquery.easing/js/jquery.easing.js"></script>
    <!-- ANIMO-->
    <script src="./vendor/animo.js/animo.js"></script>
    <!-- SLIMSCROLL-->
    <script src="./vendor/slimScroll/jquery.slimscroll.min.js"></script>
    <!-- SCREENFULL-->
    <script src="./vendor/screenfull/dist/screenfull.js"></script>
    <!-- LOCALIZE-->
    <script src="./vendor/jquery-localize-i18n/dist/jquery.localize.js"></script>
    <!-- RTL demo-->
    <!-- <script src="js/demo/demo-rtl.js"></script> -->
    <!-- =============== PAGE VENDOR SCRIPTS ===============-->
    <!-- GOOGLE MAPS-->
    <script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=false"></script>
    <script src="./vendor/jQuery-gMap/jquery.gmap.min.js"></script>
    <!-- =============== APP SCRIPTS ===============-->
    <script src="js/app.js"></script>        
    </body>
</html>
