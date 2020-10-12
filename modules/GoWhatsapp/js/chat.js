var scrolled = 0;
var previousScroll = 0;
var updateChatInt = 0;
var updateUnread = 0;
var updateContact = 0;
var collapsed = 0;
var messageCount = [];
var previousCount = 0;
var currentCount = 0;
var contactListArr = [];
var currentCount = 0;
var chatDiv = "";

$(document).ready(function(){
    // slimscroll
    $(".contacts-list").slimScroll({
        size: '4px',
        width: '100%',
        height: '100%',
        color: '#ff4800',
        allowPageScroll: true,
        alwaysVisible: true
    });

    var userid = $('#wauserid').val();
    updateContact = setInterval(function(){
	    updateUserList(userid);
    }, 3000);

    setInterval(function(){
		updateUserChat(userid, key);
    }, 5000);


    $(document).on('click', '.wa-contact', function(){
        var to_user_id = $(this).attr('data-watouserid');
        
        if(updateChatInt != 0){
            clearInterval(updateChatInt);
        }

        showUserChat(to_user_id, userid);

        updateChatInt = setInterval(function(){
            updateUserChat(userid, to_user_id);
        }, 5000);
    });

    $(document).on('keyup', '.direct-chat .chatMessage', function(event) {
	var to_user_id = $(this).attr('data-watouserid');
        if (event.keyCode === 13) {
           $("#chatButton_"+to_user_id).click();
        }
    });

    $(document).on("click", 'button.chatButton', function(event) {
    	scrolled = 0;
        var to_user_id = $(this).attr('data-watouserid');
	    var message = $('#chatMessage_'+to_user_id).val();
	
        sendMessage(to_user_id, userid, message);
	    updateUserChat(userid, to_user_id);
    });

    $(document).on('focus', '.direct-chat .chatMessage', function(){
	var to_user_id = $(this).attr('data-watouserid');
        $.ajax({
            url:"php/WhatsappChatAction.php",
            method:"POST",
            data:{to_user_id:to_user_id, userid:userid, action:'update_message_status'},
            success:function(){
            }
        });
    });

    $('.chatbox').ready(function(){
	    $(".direct-chat-messages").scroll(function(){
            var currentScroll = $(this).scrollTop();
            console.log(currentScroll);
            if(currentScroll < previousScroll){
                scrolled = 1;
                console.log("scrolled");
            }
            previousScroll = currentScroll;
        });
    });
});

function updateUserList(userid) {
    messageCount = [];
    var notiff = new Audio('./sounds/swiftly.wav');
    $.ajax({
        url:"php/WhatsappChatAction.php",
        method:"POST",
        dataType: "json",
        data:{userid: userid, action:'update_user_list'},
        success:function(response){
            var obj = response.profileHTML;
            var contactList = "";
            var i = 0;
	        obj.userid.forEach(function(key){
                if(!contactListArr.includes(key)){
                    contactList += "<div class='row sideBar-body'>";
                    contactList += "<a href='#' class='wa-contact' data-watouserid='" + key + "'>";
                    contactList += "<div class='col-sm-3 col-xs-3 sideBar-avatar'>";
                    contactList += "<div class='avatar-icon'>";
                    contactList += "<span class='contacts-list-img' alt='Contact Avatar'><div><div id='avatar' style='width: 36px; height: 36px; border-radius: 50%; text-align: center; vertical-align: middle; display: block; background: url(&quot;./php/ViewImage.php?user_id=" + key + "&quot;) 0% 0% / 36px 36px no-repeat content-box;'></div></div></span>";
                    contactList += "</div>";
                    contactList += "</div>";
                    contactList += "<div class='col-sm-9 col-xs-9 sideBar-main'>";
                    contactList += "<div class='row'>";
                    contactList += "<div class='col-sm-8 col-xs-8 sideBar-name'>";
                    contactList += "<span class='name-meta'>" + obj.username[i];
                    contactList += "</span>";
                    contactList += "</div>";
                    contactList += "<div class='col-sm-4 col-xs-4 pull-right sideBar-time'>";
                    contactList += "<span class='time-meta pull-right'>18:18";
                    contactList += "</span>";
                    contactList += "</div>";
                    contactList += "</div>";
                    contactList += "</div>";
                    contactList += "</a>";
                    contactList += "</div>";
                }
                i++;
	        });

            $(".contacts-list").append(contactList);

            var n = 0;
            var count = 0;
            obj.userid.forEach(function(key){
                count = obj.count[n];
                if(count != 0 ){
                    $('.contacts-list #unread_'+key).addClass('badge bg-red');
                    $('.contacts-list #unread_'+key).html(count);
                } else {
                    $('.contacts-list #unread_'+key).html('').removeClass('badge bg-red');
                }
                    iCount = parseInt(count);
                    messageCount.push(iCount);
                    n++;
            });
            currentCount = messageCount.reduce(getSum,0);
        }

    });
    
    if(currentCount > 0){
        if(currentCount > previousCount){
            $('#new-messages').fadeIn(100);
            notiff.play();
        }
        previousCount = currentCount;
    } else {
        $('#new-messages').fadeOut(100);
        previousCount = 0;
    }
	
}

function sendMessage(to_user_id, userid, message) {
    $('#chatMessage_'+to_user_id).val('');
    if($.trim(message) == '') {
        return false;
    }

	var conversation = "<div class='row message-body'>";
    conversation += "<div class='col-sm-12 message-main-sender'>";
    conversation += "<div class='sender'>";
    conversation += "<div class='message-text'>";
    conversation += message;
    conversation += "</div>";
    conversation += "<span class='message-time pull-right'>";
    conversation += "";
    conversation += "</span>";
    conversation += "</div>";
    conversation += "</div>";
    conversation += "</div>";

    $('#chatbox_'+to_user_id+' .direct-chat-messages ul').append(conversation);
    
    $.ajax({
        url:"php/WhatsappChatAction.php",
        method:"POST",
        data:{to_user_id:to_user_id, userid:userid, chat_message:message, action:'insert_chat'},
        dataType: "json",
        success:function(response) {
            var resp = $.parseJSON(response);
            $('#chatbox_'+to_user_id+' .direct-chat-messages').ready(function(){
                $('#chatbox_'+to_user_id+' .direct-chat-messages').scrollTop($('#chatbox_'+to_user_id+' .direct-chat-messages')[0].scrollHeight);	
            });
        }
    });
}
function showUserChat(to_user_id, userid){
    $.ajax({
        url:"php/WhatsappChatAction.php",
        method:"POST",
        data:{to_user_id:to_user_id, userid:userid, action:'show_chat'},
        dataType: "json",
        success:function(response){
            $('#chatbox_'+to_user_id+' .chat-title').html(response.userSection);
            $('#chatbox_'+to_user_id+' .direct-chat-messages ul').html(response.conversation);
            $('.contacts-list #unread_'+to_user_id).html('').removeClass('badge bg-red');
        }
    });
    scrolled = 0;
    $('#chatbox_'+to_user_id+' .direct-chat-messages').ready(function(){
	$('#chatbox_'+to_user_id+' .direct-chat-messages').scrollTop($('#chatbox_'+to_user_id+' .direct-chat-messages')[0].scrollHeight);
    });

}
function updateUserChat(userid, to_user_id) {
        $.ajax({
            url:"php/WhatsappChatAction.php",
            method:"POST",
            data:{to_user_id:to_user_id, userid:userid, action:'update_user_chat'},
            dataType: "json",
            success:function(response){
                $('#chatbox_'+to_user_id+' .direct-chat-messages ul').append(response.conversation);
		//console.log("success");
            },
	    error:function(e){
		console.log(e);
	    }
        });
	$('#chatbox_'+to_user_id+' .direct-chat-messages').ready(function(){
	    if(scrolled == 0){
		$('#chatbox_'+to_user_id+' .direct-chat-messages').scrollTop($('#chatbox_'+to_user_id+' .direct-chat-messages')[0].scrollHeight);
	    }
	});
}
function updateUnreadMessageCount(userid) {
    messageCount = [];
    var notiff = new Audio('./sounds/swiftly.wav');
    contactListArr.forEach(function(key){
        var to_user_id = key;
        $.ajax({
            url:"php/WhatsappChatAction.php",
            method:"POST",
            data:{to_user_id:to_user_id, userid:userid, action:'update_unread_message'},
            dataType: "json",
            success:function(response){
                if(response.count) {
                    $('.contacts-list #unread_'+to_user_id).addClass('badge bg-red');
                    $('.contacts-list #unread_'+to_user_id).html(response.count);
                    iCount = parseInt(response.count);
                    messageCount.push(iCount);
                    if(messageCount.length > 0){
                        currentCount = messageCount.reduce(getSum,0);
                    }
                } else {
                    $('.contacts-list #unread_'+to_user_id).html('').removeClass('badge bg-red');
                }
            }
        });
    });
    if(currentCount > 0){
        if(currentCount > previousCount){
            notiff.play();
        }
        previousCount = currentCount;
    } else {
        $('#new-messages').fadeOut(100);
        previousCount = 0;
    }
}

function showTypingStatus() {
    $('li.contact.active').each(function(){
        var to_user_id = $(this).attr('data-watouserid');
        $.ajax({
            url:"php/WhatsappChatAction.php",
            method:"POST",
            data:{to_user_id:to_user_id, action:'show_typing_status'},
            dataType: "json",
            success:function(response){
                    $('#isTyping_'+to_user_id).html(response.message);
            }
        });
    });
}

function getSum(total, num) {
  return total + Math.round(num);
}
