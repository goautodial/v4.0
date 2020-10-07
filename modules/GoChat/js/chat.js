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
var sessionStorageUserList = sessionStorage.getItem("activeChats");
var chatDiv = "";
if(sessionStorageUserList != null){
	var sessionActiveChats = sessionStorageUserList.split(",");
} else {
	var sessionActiveChats = "";
}

if(Array.isArray(sessionActiveChats) &&  sessionActiveChats.length){
    var activeChats = sessionActiveChats;
} else {
    var activeChats = [];
}

$(document).ready(function(){
    $(document).on('click', '#cream-agent-logout', function(){
	sessionStorage.clear();
    });

    // slimscroll
    $(".contacts-list").slimScroll({
        size: '4px',
        width: '100%',
        height: '100%',
        color: '#ff4800',
        allowPageScroll: true,
        alwaysVisible: true
    });

    var userid = $('#userid').val();
    updateContact = setInterval(function(){
	updateUserList(userid);
    }, 3000);

    /*$(".direct-chat-messages").scroll(function(){
	var currentScroll = $(this).scrollTop();
	console.log(currentScroll);
	if(currentScroll < previousScroll){
	    scrolled = 1;
	    console.log("scrolled");
	}
	previousScroll = currentScroll;
    });*/

    /*$("#chatCollapse").on("click", function(){
	console.log("clicked");
	if(updateChatInt != 0){
	    if(!collapsed){
        	clearInterval(updateChatInt);
		collapsed = 1;
	    } else if(collapsed){
		to_user_id = $(this).attr("data-touserid");
		updateChatInt= setInterval(function(){
            	updateUserChat(userid, to_user_id);
        	}, 5000);
		collapsed = 0;
	    }
        }
    });*/

    console.log(activeChats);	
    setTimeout(function(){
	activeChats.forEach(function(key){
	    showChatDiv(key);  
	    showUserChat(key, userid);
	});
    }, 500);

    setInterval(function(){
	if(activeChats.length != 0){
	    activeChats.forEach(function(key){
		updateUserChat(userid, key);
	    });
	}
    }, 5000);


    $(document).on('click', '.contact', function(){
	var to_user_id = $(this).data('touserid');
	var uName = $('contacts-list-name').text();

        chatDiv = '<div class="box box-info box-solid direct-chat direct-chat-info chatbox" id="chatbox_'+ to_user_id +'">';
        chatDiv += '<div class="box-header with-border">';
        chatDiv += '<div id="chatCollapse_'+to_user_id+'" data-widget="collapse" data-touserid="' + to_user_id + '">';
        chatDiv += '<h3 class="box-title fa fa-minus"> &nbsp;<span class="chat-title">' + uName + '</span></h3>';
        chatDiv += '</div>';
        chatDiv += '<div class="box-tools pull-right">';
        chatDiv += '<button class="btn btn-box-tool remove" data-widget="remove" data-touserid="' + to_user_id + '"><i class="fa fa-times"></i></button>';
        chatDiv += '</div>';
        chatDiv += '</div>';
        chatDiv += '<div class="box-body">';
        chatDiv += '<div class="direct-chat-messages">';
	chatDiv += '<ul></ul>';
        chatDiv += '</div>';
        chatDiv += '</div>';
        chatDiv += '<div class="box-footer">';
        chatDiv += '<div class="input-group">';
        chatDiv += '<input type="text" id="chatMessage_' + to_user_id + '" data-touserid="' + to_user_id + '" name="message" placeholder="Type Message ..." class="form-control chatMessage" autocomplete="off">';
        chatDiv += '<span class="input-group-btn">';
        chatDiv += '<button type="button" id="chatButton_' + to_user_id + '" data-touserid="' + to_user_id + '" class="submit btn btn-info btn-flat chatButton">Send</button>';
        chatDiv += '</span>';
        chatDiv += '</div>';
        chatDiv += '</div>';
        chatDiv += '</div>';

	if(updateChatInt != 0){
	    clearInterval(updateChatInt);
	}

	if(!activeChats.includes(to_user_id.toString())){
	    if(activeChats.length < 3){
		activeChats.push(to_user_id.toString());
		$('#chat-div').append(chatDiv);
	    } else {
		var shifted = activeChats.shift();
		activeChats.push(to_user_id.toString());
		console.log("Shifted: " + shifted);
		$('div#chatbox_'+shifted).empty();
		$('div#chatbox_'+shifted).remove();
		$('#chat-div').append(chatDiv);
	    }
	}

	console.log(to_user_id);

	showUserChat(to_user_id, userid);
	sessionStorage.setItem("activeChats", activeChats);

	console.log(activeChats);

	updateChatInt = setInterval(function(){
            updateUserChat(userid, to_user_id);
	}, 5000);
	
    });

    $(document).on('click', 'button.remove', function(){
	var to_user_id = $(this).attr('data-touserid');
	var find = activeChats.indexOf(to_user_id.toString());

	if(activeChats.length == 1){
	    activeChats = [];
	} else {
	    activeChats.splice(find, find);
	}
	sessionStorage.setItem("activeChats", activeChats);
	console.log(activeChats);
    });

    $(document).on('keyup', '.direct-chat .chatMessage', function(event) {
	var to_user_id = $(this).attr('data-touserid');
        if (event.keyCode === 13) {
           $("#chatButton_"+to_user_id).click();
        }
    });

    $(document).on("click", 'button.chatButton', function(event) {
	scrolled = 0;
        var to_user_id = $(this).attr('data-touserid');
	var message = $('#chatMessage_'+to_user_id).val();
	
        sendMessage(to_user_id, userid, message);
	updateUserChat(userid, to_user_id);
    });

    $(document).on('focus', '.direct-chat .chatMessage', function(){
	var to_user_id = $(this).attr('data-touserid');
        $.ajax({
            url:"php/ChatAction.php",
            method:"POST",
            data:{to_user_id:to_user_id, userid:userid, action:'update_message_status'},
            success:function(){
            }
        });
    });

    /*$(document).on('blur', '.message-input', function(){
        var is_type = 'no';
        $.ajax({
            url:"php/ChatAction.php",
            method:"POST",
            data:{is_type:is_type, action:'update_typing_status'},
            success:function() {
            }
        });
    });*/

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

function showChatDiv(to_user_id){
    console.log(to_user_id);
    chatDiv = '<div class="box box-info box-solid direct-chat direct-chat-info collapsed-box chatbox" id="chatbox_'+ to_user_id +'">';
    chatDiv += '<div class="box-header with-border">';
    chatDiv += '<div id="chatCollapse_'+to_user_id+'" data-widget="collapse" data-touserid="' + to_user_id + '">';
    chatDiv += '<h3 class="box-title fa fa-minus"> &nbsp;<span class="chat-title"></span></h3>';
    chatDiv += '</div>';
    chatDiv += '<div class="box-tools pull-right">';
    chatDiv += '<button class="btn btn-box-tool remove" data-widget="remove" data-touserid="' + to_user_id + '"><i class="fa fa-times"></i></button>';
    chatDiv += '</div>';
    chatDiv += '</div>';
    chatDiv += '<div class="box-body">';
    chatDiv += '<div class="direct-chat-messages">';
    chatDiv += '<ul></ul>';
    chatDiv += '</div>';
    chatDiv += '</div>';
    chatDiv += '<div class="box-footer">';
    chatDiv += '<div class="input-group">';
    chatDiv += '<input type="text" id="chatMessage_' + to_user_id + '" data-touserid="' + to_user_id + '" name="message" placeholder="Type Message ..." class="form-control chatMessage" autocomplete="off">';
    chatDiv += '<span class="input-group-btn">';
    chatDiv += '<button type="button" id="chatButton_' + to_user_id + '" data-touserid="' + to_user_id + '" class="submit btn btn-info btn-flat chatButton">Send</button>';
    chatDiv += '</span>';
    chatDiv += '</div>';
    chatDiv += '</div>';
    chatDiv += '</div>';

    $('#chat-div').append(chatDiv);
}

function updateUserList(userid) {
    messageCount = [];
    var notiff = new Audio('./sounds/swiftly.wav');
    $.ajax({
        url:"php/ChatAction.php",
        method:"POST",
        dataType: "json",
        data:{userid: userid, action:'update_user_list'},
        success:function(response){
            var obj = response.profileHTML;
	    var contactList = "";
	    var i = 0;
	    obj.userid.forEach(function(key){
		if(!contactListArr.includes(key)){
		contactListArr.push(key);
                contactList += "<li>";
                contactList += "<a href='#' class='contact' data-touserid='" + key + "'>";
                contactList += "<span class='contacts-list-img' alt='Contact Avatar'><div><div id='avatar' style='width: 36px; height: 36px; border-radius: 50%; text-align: center; vertical-align: middle; display: block; background: url(&quot;./php/ViewImage.php?user_id=" + key + "&quot;) 0% 0% / 36px 36px no-repeat content-box;'></div></div></span>";
		//<avatar username='" + obj.username[i] + "' :rounded='false' :size='36'></avatar></span>";
                contactList += "<div class='contacts-list-info'>";
                contactList += "<span class='contacts-list-name'>";
                contactList += obj.username[i];
                contactList += "<small id='unread_" + key + "' class='contacts-list-date pull-right'></small>";
                contactList += "</span>";
                contactList += "</div>";
                contactList += "</a>";
                contactList += "</li>";
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
//    console.log('Current Count: ' + currentCount + ' Previous Count: ' + previousCount);
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

	var conversation =  "<div class='direct-chat-msg right'>";
        conversation +=  "<div class='direct-chat-info clearfix'>";
        conversation +=  "<span class='direct-chat-name pull-right'></span>";
        conversation +=  "<span class='direct-chat-timestamp pull-left'></span>";
        conversation +=  "</div>";

        conversation +=  "<div class='direct-chat-text'>";
        conversation +=  message;
        conversation +=  "</div>";
        conversation +=  "</div>";

	$('#chatbox_'+to_user_id+' .direct-chat-messages ul').append(conversation);
    $.ajax({
        url:"php/ChatAction.php",
        method:"POST",
        data:{to_user_id:to_user_id, userid:userid, chat_message:message, action:'insert_chat'},
        dataType: "json",
        success:function(response) {
            var resp = $.parseJSON(response);
            //$('#chatbox_'+to_user_id+' .direct-chat-messages').html(resp.conversation);
	    $('#chatbox_'+to_user_id+' .direct-chat-messages').ready(function(){
        	$('#chatbox_'+to_user_id+' .direct-chat-messages').scrollTop($('#chatbox_'+to_user_id+' .direct-chat-messages')[0].scrollHeight);	
	    });
        }
    });
}
function showUserChat(to_user_id, userid){
    $.ajax({
        url:"php/ChatAction.php",
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
	//console.log("Updating Chat");
        $.ajax({
            url:"php/ChatAction.php",
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
    //console.log("Updating Message Count");
    //previousCount = messageCount;
    messageCount = [];
    var notiff = new Audio('./sounds/swiftly.wav');
    contactListArr.forEach(function(key){
    //$('a.contact').each(function(){
        //if(!$(this).hasClass('active')) {
            //var to_user_id = $(this).attr('data-touserid');
            var to_user_id = key;
            $.ajax({
                url:"php/ChatAction.php",
                method:"POST",
                data:{to_user_id:to_user_id, userid:userid, action:'update_unread_message'},
                dataType: "json",
                success:function(response){
                    if(response.count) {
			$('.contacts-list #unread_'+to_user_id).addClass('badge bg-red');
                        $('.contacts-list #unread_'+to_user_id).html(response.count);
			iCount = parseInt(response.count);
			messageCount.push(iCount);
			//console.log('Message Count Lenght: ' + messageCount.length);
			if(messageCount.length > 0){
			    currentCount = messageCount.reduce(getSum,0);
			}
                    } else {
                        $('.contacts-list #unread_'+to_user_id).html('').removeClass('badge bg-red');
			//currentCount = 0;
		    }

		    /*if(currentCount > 0){
		        $('#new-messages').fadeIn(100);
			notiff.play();
		
		    } else {
		        $('#new-messages').fadeOut(100);
		    }*/
                }
            });
        //}
    });
    //console.log('Message Count Length: ' + messageCount.length);
    /*if(messageCount.length == 0){
	currentCount = 0;
    }*/
    //console.log("Current: " + currentCount + " Previous: " + previousCount);
    if(currentCount > 0){
	 if(currentCount > previousCount){
             //$('#new-messages').fadeIn(100);
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
        var to_user_id = $(this).attr('data-touserid');
        $.ajax({
            url:"php/ChatAction.php",
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
