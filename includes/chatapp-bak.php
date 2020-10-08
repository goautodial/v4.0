<style>
#chatbox{
    position: fixed;
    bottom: -5px;
    z-index: 1000;
    cursor: pointer;
    line-height: 30px;
    width: 30%;
    left: 20; 
}
</style>
<div class="box box-info box-solid direct-chat direct-chat-info" id="chatbox">
  <div class="box-header with-border">
    <h3 class="box-title">Agent Chat</h3>
    <div class="box-tools pull-right">
      <span data-toggle="tooltip" title="3 New Messages" class="badge bg-red">3</span>
      <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
      <!-- In box-tools add this button if you intend to use the contacts pane -->
      <button class="btn btn-box-tool" data-toggle="tooltip" title="Contacts" data-widget="chat-pane-toggle"><i class="fa fa-comments"></i></button>
      <!--<button class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>-->
    </div>
  </div>
  <!-- /.box-header -->
  <div class="box-body">
    <!-- Conversations are loaded here -->
    <div class="direct-chat-messages">
      <!-- Message. Default to the left -->
      <div class="direct-chat-msg">
        <div class="direct-chat-info clearfix">
          <span class="direct-chat-name pull-left">Alexander Pierce</span>
          <span class="direct-chat-timestamp pull-right">23 Jan 2:00 pm</span>
        </div>
        <!-- /.direct-chat-info -->
        <img class="direct-chat-img" src="../dist/img/user1-128x128.jpg" alt="message user image">
        <!-- /.direct-chat-img -->
        <div class="direct-chat-text">
          Is this template really for free? That\'s unbelievable!
        </div>
        <!-- /.direct-chat-text -->
      </div>
      <!-- /.direct-chat-msg -->

      <!-- Message to the right -->
      <div class="direct-chat-msg right">
        <div class="direct-chat-info clearfix">
          <span class="direct-chat-name pull-right">Sarah Bullock</span>
          <span class="direct-chat-timestamp pull-left">23 Jan 2:05 pm</span>
        </div>
        <!-- /.direct-chat-info -->
        <img class="direct-chat-img" src="../dist/img/user3-128x128.jpg" alt="message user image">
        <!-- /.direct-chat-img -->
        <div class="direct-chat-text">
          You better believe it!
        </div>
        <!-- /.direct-chat-text -->
      </div>
      <!-- /.direct-chat-msg -->
    </div>
    <!--/.direct-chat-messages-->

    <!-- Contacts are loaded here -->
    <div class="direct-chat-contacts">
      <ul class="contacts-list">
        <li>
          <a href="#">
            <img class="contacts-list-img" src="../dist/img/user1-128x128.jpg" alt="Contact Avatar">
            <div class="contacts-list-info">
              <span class="contacts-list-name">
                Count Dracula
                <small class="contacts-list-date pull-right">2/28/2015</small>
                </span>
              <span class="contacts-list-msg">How have you been? I was...</span>
            </div>
            <!-- /.contacts-list-info -->
          </a>
        </li>
        <!-- End Contact Item -->
      </ul>
      <!-- /.contatcts-list -->
    </div>
    <!-- /.direct-chat-pane -->
  </div>
  <!-- /.box-body -->
  <div class="box-footer">
    <div class="input-group">
      <input type="text" name="message" placeholder="Type Message ..." class="form-control">
      <span class="input-group-btn">
                <button type="button" class="btn btn-info btn-flat">Send</button>
                </span>
    </div>
  </div>
  <!-- /.box-footer-->
</div>
<!--/.direct-chat -->
