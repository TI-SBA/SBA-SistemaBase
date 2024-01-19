KChat = {
	init:function(){
		this.windowFocus = true;
		this.username;
		this.chatHeartbeatCount = 0;
		this.minChatHeartbeat = 1000;
		this.maxChatHeartbeat = 33000;
		this.chatHeartbeatTime = this.minChatHeartbeat;
		this.originalTitle = document.title;
		this.blinkOrder = 0;

		this.chatboxFocus = new Array();
		this.newMessages = new Array();
		this.newMessagesWin = new Array();
		this.chatBoxes = new Array();

		KChat.originalTitle = document.title;
		//startChatSession();
		$([window, document]).blur(function(){
			KChat.windowFocus = false;
		}).focus(function(){
			KChat.windowFocus = true;
			document.title = KChat.originalTitle;
		});
		socket.on('new message', function(data){	
			KChat.chatWith(data.from);
			var from = $("#chatbox_"+data.from).attr('data-alias');
			$("#chatbox_"+data.from+" .chatboxcontent").append('<div class="chatboxmessage"><span class="chatboxmessagefrom">'+from+':&nbsp;&nbsp;</span><span class="chatboxmessagecontent">'+data.msg+'</span></div>');
			$("#chatbox_"+data.from+" .chatboxcontent").scrollTop($("#chatbox_"+data.from+" .chatboxcontent")[0].scrollHeight);
		});
		KChat.userList();
		socket.on('userlist',function(){
			KChat.userList();
		});
	},
	userList:function(){
		$.post('ac/user/all',{online: 1},function(data){
			if(data.items!=null){
				data = data.items;
				var $cnt = $('[name=userList]');
				$cnt = $('[name=userList]').empty();
				var user_online = 0;
				for(i=0;i<data.length;i++){
					if(K.session.user._id.$id!=data[i]._id.$id){
						var online = "0";
						var icon = '<i class="offline fa fa-circle"></i>';
						if(data[i].online==true){
							user_online++;
							online="1";
							icon = '<i class="online fa fa-circle"></i>';
						}
						$cnt.append('<li><a data-user-id="'+data[i]._id.$id+'" data-user-alias="'+data[i].owner.nomb+'" data-user-online="'+online+'" href="javascript:void(0);">'+icon+'<span>'+mgEnti.formatName(data[i].owner)+'</span></a></li>');
					}
				}
				$cnt.on('click','[data-user-online=1]',function(){					
					KChat.chatWith($(this).attr('data-user-id'));
				});
				$('#ChatList').find('span').text(user_online);
			}
		},'json');
	},
	restructureChatBoxes:function(){
		align = 0;
		for (x in this.chatBoxes) {
			chatboxtitle = this.chatBoxes[x];

			if ($("#chatbox_"+chatboxtitle).css('display') != 'none') {
				if (align == 0) {
					$("#chatbox_"+chatboxtitle).css('right', '20px');
				} else {
					width = (align)*(225+7)+20;
					$("#chatbox_"+chatboxtitle).css('right', width+'px');
				}
				align++;
			}
		}
	},
	chatWith:function(chatuser){
		this.createChatBox(chatuser);
		$("#chatbox_"+chatuser+" .chatboxtextarea").focus();
	},
	createChatBox:function(chatboxtitle,minimizeChatBox){
		if ($("#chatbox_"+chatboxtitle).length > 0) {
			if ($("#chatbox_"+chatboxtitle).css('display') == 'none') {
				$("#chatbox_"+chatboxtitle).css('display','block');
				this.restructureChatBoxes();
			}
			$("#chatbox_"+chatboxtitle+" .chatboxtextarea").focus();
			return;
		}
		var $enti = $('[name=userList]').find('[data-user-id='+chatboxtitle+'] span').html();
		var $alias = $('[name=userList]').find('[data-user-id='+chatboxtitle+']').attr('data-user-alias');
		$("<div />" ).attr("id","chatbox_"+chatboxtitle).attr('data-alias',$alias)
		.addClass("chatbox")
		.html('<div class="chatboxhead"><div class="chatboxtitle">'+$enti+'</div><div class="chatboxoptions"><a href="javascript:void(0)" onclick="javascript:KChat.toggleChatBoxGrowth(\''+chatboxtitle+'\')"><i class="fa fa-minus-circle"></i></a> <a href="javascript:void(0)" onclick="javascript:KChat.closeChatBox(\''+chatboxtitle+'\')"><i class="fa fa-times-circle"></i></a></div><br clear="all"/></div><div class="chatboxcontent"></div><div class="chatboxinput"><small class="text-muted center">Pulsa Intro para enviar el mensaje.</small><textarea class="chatboxtextarea" onkeydown="javascript:return KChat.checkChatBoxInputKey(event,this,\''+chatboxtitle+'\');"></textarea></div>')
		.appendTo($( "body" ));		   
		$("#chatbox_"+chatboxtitle).css('bottom', '0px');	
		this.chatBoxeslength = 0;
		for (x in this.chatBoxes) {
			if ($("#chatbox_"+this.chatBoxes[x]).css('display') != 'none') {
				this.chatBoxeslength++;
			}
		}
		if (this.chatBoxeslength == 0) {
			$("#chatbox_"+chatboxtitle).css('right', '20px');
		} else {
			width = (this.chatBoxeslength)*(225+7)+20;
			$("#chatbox_"+chatboxtitle).css('right', width+'px');
		}		
		this.chatBoxes.push(chatboxtitle);
		if (minimizeChatBox == 1) {
			minimizedChatBoxes = new Array();
			if ($.cookie('chatbox_minimized')) {
				minimizedChatBoxes = $.cookie('chatbox_minimized').split(/\|/);
			}
			minimize = 0;
			for (j=0;j<minimizedChatBoxes.length;j++) {
				if (minimizedChatBoxes[j] == chatboxtitle) {
					minimize = 1;
				}
			}
			if (minimize == 1) {
				$('#chatbox_'+chatboxtitle+' .chatboxcontent').css('display','none');
				$('#chatbox_'+chatboxtitle+' .chatboxinput').css('display','none');
			}
		}
		this.chatboxFocus[chatboxtitle] = false;
		$("#chatbox_"+chatboxtitle+" .chatboxtextarea").blur(function(){
			KChat.chatboxFocus[chatboxtitle] = false;
			$("#chatbox_"+chatboxtitle+" .chatboxtextarea").removeClass('chatboxtextareaselected');
		}).focus(function(){
			KChat.chatboxFocus[chatboxtitle] = true;
			KChat.newMessages[chatboxtitle] = false;
			$('#chatbox_'+chatboxtitle+' .chatboxhead').removeClass('chatboxblink');
			$("#chatbox_"+chatboxtitle+" .chatboxtextarea").addClass('chatboxtextareaselected');
		});
		$("#chatbox_"+chatboxtitle).click(function() {
			if ($('#chatbox_'+chatboxtitle+' .chatboxcontent').css('display') != 'none') {
				$("#chatbox_"+chatboxtitle+" .chatboxtextarea").focus();
			}
		});
		$("#chatbox_"+chatboxtitle).show();
	},
	closeChatBox:function(chatboxtitle){
		$('#chatbox_'+chatboxtitle).css('display','none');
		this.restructureChatBoxes();
	},
	toggleChatBoxGrowth:function(chatboxtitle){
		if ($('#chatbox_'+chatboxtitle+' .chatboxcontent').css('display') == 'none') {
			$('#chatbox_'+chatboxtitle+' .chatboxoptions').find('.fa-plus-circle').removeClass('fa-plus-circle').addClass('fa-minus-circle');
			var minimizedChatBoxes = new Array();
			if ($.cookie('chatbox_minimized')) {
				minimizedChatBoxes = $.cookie('chatbox_minimized').split(/\|/);
			}
			var newCookie = '';
			for (i=0;i<minimizedChatBoxes.length;i++) {
				if (minimizedChatBoxes[i] != chatboxtitle) {
					newCookie += chatboxtitle+'|';
				}
			}
			newCookie = newCookie.slice(0, -1);
			$.cookie('chatbox_minimized', newCookie);
			$('#chatbox_'+chatboxtitle+' .chatboxcontent').css('display','block');
			$('#chatbox_'+chatboxtitle+' .chatboxinput').css('display','block');
			$("#chatbox_"+chatboxtitle+" .chatboxcontent").scrollTop($("#chatbox_"+chatboxtitle+" .chatboxcontent")[0].scrollHeight);
		} else {
			$('#chatbox_'+chatboxtitle+' .chatboxoptions').find('.fa-minus-circle').removeClass('fa-minus-circle').addClass('fa-plus-circle');
			var newCookie = chatboxtitle;

			if ($.cookie('chatbox_minimized')) {
				newCookie += '|'+$.cookie('chatbox_minimized');
			}
			$.cookie('chatbox_minimized',newCookie);
			$('#chatbox_'+chatboxtitle+' .chatboxcontent').css('display','none');
			$('#chatbox_'+chatboxtitle+' .chatboxinput').css('display','none');
		}
	},
	checkChatBoxInputKey:function(event,chatboxtextarea,chatboxtitle){
		 
		if(event.keyCode == 13 && event.shiftKey == 0)  {
			message = $(chatboxtextarea).val();
			message = message.replace(/^\s+|\s+$/g,"");

			$(chatboxtextarea).val('');
			$(chatboxtextarea).focus();
			$(chatboxtextarea).css('height','44px');
			if (message != '') {
				//console.log("para "+chatboxtitle+": "+message);
				$("#chatbox_"+chatboxtitle+" .chatboxcontent").append('<div class="chatboxmessage"><span class="chatboxmessagefrom">Yo:&nbsp;&nbsp;</span><span class="chatboxmessagecontent">'+message+'</span></div>');
				socket.emit('send message', message, chatboxtitle, function(data){
					//$chat.append('<span class="error">' + data + "</span><br/>");
				});
				$("#chatbox_"+chatboxtitle+" .chatboxcontent").scrollTop($("#chatbox_"+chatboxtitle+" .chatboxcontent")[0].scrollHeight);
			}
			return false;
		}else if(event.keyCode == 27){
			KChat.closeChatBox(chatboxtitle);
		}
		var adjustedHeight = chatboxtextarea.clientHeight;
		var maxHeight = 94;
		if (maxHeight > adjustedHeight) {
			adjustedHeight = Math.max(chatboxtextarea.scrollHeight, adjustedHeight);
			if (maxHeight)
				adjustedHeight = Math.min(maxHeight, adjustedHeight);
			if (adjustedHeight > chatboxtextarea.clientHeight)
				$(chatboxtextarea).css('height',adjustedHeight+8 +'px');
		} else {
			$(chatboxtextarea).css('overflow','auto');
		}
	}
};
