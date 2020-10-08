<style>
    .chatbox{
        z-index: 1000;
        cursor: pointer;
        line-height: 30px;
        width: 338px;
	display: inline-block;
	margin-left: 10px;
    } 

    #chatbox .direct-chat-messages ul{
	padding-left: 5px;
    }

    .direct-chat-text{
	word-break: break-word;
	line-height: 18px;
    }
	
    .direct-chat-messages{
	height: 360px;
    }

    #chat-div{
	position: fixed;
	bottom: -5px;
	//right: 240px;
	z-index: 1000;
	width: 100%;
    }

</style>

<div id="chat-div"></div>

