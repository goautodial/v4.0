<link href="../modules/GoChat/css/style.css" rel="stylesheet" type="text/css" />
<!--<ul class="control-sidebar-menu" id="chat-menu">
    <li>
      <div class="chat">
        <div id="profile">
            <div class="wrap">
            <div id="profile-img" class="avatar-container online"></div>
            <p></p>
            <div id="status-options">
            <ul>
            <li id="status-online" class="active"><span class="status-circle"></span> <p>Online</p></li>
            <li id="status-away"><span class="status-circle"></span> <p>Away</p></li>
            <li id="status-busy"><span class="status-circle"></span> <p>Busy</p></li>
            <li id="status-offline"><span class="status-circle"></span> <p>Offline</p></li>
            </ul>
            </div>
            </div>
        </div>
     </div>
    </li>
    <li>
     <div class="chat">
        <div id="search">
            <label for=""><i class="fa fa-search" aria-hidden="true"></i></label>
            <input type="text" placeholder="Search contacts..." />
        </div>
     </div>
    </li>
    <li>
     <div class="chat">
        <div id="contacts">
            <ul class="custom_alex">
                <li id="" class="contact" data-touserid="" data-tousername="">
                <div class="wrap">
                <span id="status" class="contact-status"></span>
                <span></span>
                <div class="meta">
                <p class="name">TEST</span></p>
                <span id="unread" class="unread"></span></p>
                <p class="preview"><span id="isTyping_'.$userid.'" class="isTyping"></span></p>
                </div>
                </div>
                </li>
            </ul>
        </div>
     </div>
    </li>
</ul> --

	<ul class="contacts-list">
        <?php
           /* $chatUsers = $api->API_chatUsers($_SESSION['userid']);
            $i = 0;
            foreach ($chatUsers->userid as $userid) {
                $avatar = NULL;
                if ($chatUsers->avatar) {
                    $avatar = "./php/ViewImage.php?user_id=" . $userid;
                }
                $sessionAvatar = $ui->getVueAvatar($chatUsers->username, $avatar, 36, false);
        ?>
        <li>
          <a href="#" class="contact" data-touserid="<?=$userid?>">
            <!--<img class="contacts-list-img" src="../dist/img/user1-128x128.jpg" alt="Contact Avatar">-->
            <span class="contacts-list-img" alt="Contact Avatar"><?php echo $sessionAvatar;?></span>
            <div class="contacts-list-info">
              <span class="contacts-list-name">
                <?=$chatUsers->username[$i]?>
                <small id="unread_<?=$userid?>" class="contacts-list-date pull-right"></small>
                </span>
              <!--<span class="contacts-list-msg">How have you been? I was...</span>-->
            </div>
            <!-- /.contacts-list-info -->
          </a>
        </li>
        <?php
                $i++;
            }*/
        ?>
        <!-- End Contact Item -->
     <!-- </ul>-->

