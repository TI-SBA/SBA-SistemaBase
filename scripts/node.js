//var socket = io('https://sistemas.sbparequipa.gob.pe:3000'),
var socket = io('http://localhost:81/sba'),
socket_con = false;
socket.on('connect', function () {
	K.unblock();
	K.clearNoti();
	K.notification({title: 'Servidor conectado!',icon: 'ui-icon-check',type: 'info'});
	socket_con = true;
	var tmp_user = {
		_id: K.session.user._id.$id,
		userid: K.session.user.userid,
		user: K.session.user,
		enti: K.session.enti,
		orga: ''
	};
	if(K.session.enti.roles!=null){
		if(K.session.enti.roles.trabajador!=null){
			tmp_user.orga = K.session.enti.roles.trabajador.oficina._id.$id;
		}
	}
	
	var user_tmp = {
		_id: K.session.user._id,
		groups: K.session.user.groups,
		online: K.session.user.online,
		owner: K.session.user.owner,
		passwd: K.session.user.passwd,
		userid: K.session.user.userid
	};
	user_tmp.oficina = {
		_id: K.session.enti.roles.trabajador.oficina._id.$id,
		nomb: K.session.enti.roles.trabajador.oficina.nomb
	};
	socket.emit('adduser', user_tmp, location.hostname,function(data){
		if(data){
			socket.on('votar',function(callback){
				callback(true);
				new K.Modal({
					id: 'modAdv',
					content: '<h4>Otro Usuario Esta ingresando desde otro lugar con esta Cuenta</h2><p>Esta Sesi&oacute;n se cerrara en <div id="counter" style="margin-left:37%;"></div> segundos</4>',
				});
				/*$('#counter').countdown({
			          image: 'images/digits.png',
			          startTime: '20',
			          timerEnd: function(){ document.location.href = "ci/index/logout"; },
			          format: 'ss'
			    });*/
				/*setTimeout(function(){
					document.location.href = "ci/index/logout";
			    }, 15000);//10segundos*/
			});
		}else{
			console.log('Ha ocurrido un problema!  Refresca la Pagina(F5).');
		}
	});
	
	$.post('ac/user/all',{online: 1},function(users){
		$('#iconChat label').html(users.items.length);
	},'json');
});
socket.on('notification', function (data) {
	K.notification({
		title: data.title,
		text: data.msg,
		type: data.type
	});
	$.ajax({
		url: "ac/noti/last",
		success: function(response){
			K.unblock();
			if(response!=null){
				$('#iconNoty label').html(response.count);
			}
		},
		dataType: 'json'
	});
});
// listener, whenever the server emits 'updatechat', this updates the chat body
socket.on('updatechat', function (username, data) {
	$('#conversation').append('<b>'+username + ':</b> ' + data + '<br>');
});

// listener, whenever the server emits 'updateusers', this updates the username list
socket.on('updateusers', function(data) {
	$('#users').empty();
	$.each(data, function(key, value) {
		$('#users').append('<div>' + key + '</div>');
	});
});











socket.on('new message', function(data){
	if($('#ui-chatbox'+data.from).length!=0){
		$('#ui-chatbox'+data.from).show();
		$('#ui-chatbox'+data.from).find('.ui-chatbox-content').show();
		
		var $w = $("#ui-chatbox"+data.from),
		from = $("#ui-chatbox"+data.from).attr('data-alias');
		$w.find('[name=user_name]').effect("highlight", {}, 300);
		$w.effect("bounce", {times: 3}, 500,function(){
			$w.find('.ui-chatbox-log').animate({scrollTop: $w.find(".ui-chatbox-log div:last").offset().top},100);
		});
		$("#ui-chatbox"+data.from+" .ui-chatbox-log").append('<div style="display: block; max-width: <?=$width?>px;" class="ui-chatbox-msg">'+
			'<b>'+$w.data('data').owner.nomb+': </b>'+
			'<span>'+data.msg+'</span>'+
		'</div>');
		ndChat.locate();
		$w.find('.ui-chatbox-log').animate({scrollTop: $w.find(".ui-chatbox-log div:last").offset().top},100);
		$w.find('[name=msg]').focus();
		K.nn($w.data('data').owner.nomb,data.msg,'images/usuario.jpg');
	}else{
		ndChat.window({
			id: data.from,
			msg: data.msg
		});
	}
});
socket.on('userlist',function(){
	$.post('ac/user/all',{online: 1},function(users){
		$('#iconChat label').html(users.items.length-1);
	},'json');
});
ndChat = {
	window: function(p){
		if($('#ui-chatbox'+p.id).length!=0){
			$('#ui-chatbox'+p.id).show();
			$('#ui-chatbox'+p.id).find('.ui-chatbox-content').show();
			$('#ui-chatbox'+p.id).find('[name=user_name]').effect("highlight", {}, 300);
			$('#ui-chatbox'+p.id).effect("bounce", {times: 3}, 500,function(){
				$('#ui-chatbox'+p.id).find('.ui-chatbox-log').animate({scrollTop: $('#ui-chatbox'+p.id).find(".ui-chatbox-log div:last").offset().top},100);
				$('#ui-chatbox'+p.id).find('[name=msg]').focus();
			});
			ndChat.locate();
		}else{
			$.extend(p,{
				url: 'ci/index/view_chat',
				preinit: function(html){
					if(p.user==null){
						$.post('ac/user/get',{_id: p.id},function(data){
							p.user = data;
							p.init(html);
						},'json');
					}else{
						p.init(html);
					}
				},
				init: function(html){
					$chat = $(html);
					$chat.attr('id','ui-chatbox'+p.id);
					$('body').append($chat);
					var name_tmp = mgEnti.formatName(p.user.owner);
					if(name_tmp.length>22)
						name_tmp = name_tmp.substr(0,22)+'...';
					$chat.find('[name=user_chat]').html(name_tmp);
					$chat.find('.ui-icon-minusthick').click(function(){
						$(this).closest('.ui-chatbox').find('.ui-chatbox-content').toggle();
					});
					$chat.find('[name=user_chat]').click(function(){
						$chat.find('.ui-icon-minusthick').click();
					});
					$chat.find('.ui-icon-closethick').click(function(){
						$(this).closest('.ui-chatbox').hide();
						ndChat.locate();
					});
					$chat.find('[name=msg]').keyup(function(e) {
						var KEYCODE_ENTER = 13,
						KEYCODE_ESC = 27;
						$chat = $(this).closest('.ui-chatbox');
					    if(e.which == KEYCODE_ENTER) {
					        var msg = $chat.find('[name=msg]').val();
							if (msg != '') {
								socket.emit('send message', msg, p.id, function(data){
									//$chat.append('<span class="error">' + data + "</span><br/>");
								});
								$chat.find('.ui-chatbox-log').append('<div style="display: block; max-width: <?=$width?>px;" class="ui-chatbox-msg">'+
									'<b>Yo: </b><span>'+msg+'</span>'+
								'</div>');
							}
							$chat.find('[name=msg]').val('').focus();
							$chat.find('.ui-chatbox-log').animate({scrollTop: $chat.find(".ui-chatbox-log div:last").offset().top},100);
					    }else if(e.which == KEYCODE_ESC){
					    	$chat.find('.ui-icon-closethick').click();
					    }
					});
					$chat.data('data',p.user);
					ndChat.locate();
					$chat.find('[name=msg]').focus();
					if(p.msg!=null){
						$chat.find('.ui-chatbox-log').append('<div style="display: block; max-width: <?=$width?>px;" class="ui-chatbox-msg">'+
							'<b>'+p.user.owner.nomb+': </b><span>'+p.msg+'</span>'+
						'</div>');
						$chat.find('[name=user_name]').effect("highlight", {}, 300);
						$chat.effect("bounce", {times: 3}, 500,function(){
							$chat.find('.ui-chatbox-log').animate({scrollTop: $chat.find(".ui-chatbox-log div:last").offset().top},100);
						});
						K.nn(p.user.owner.nomb,p.msg,'images/usuario.jpg');
					}
				}
			});
			if($('#ui-chatbox'+p.id).length!=0){
				$('#ui-chatbox'+p.id).show();
			}else{
				if($.jStorage.get(p.url, false)){
					var html = $.jStorage.get(p.url);
		        	p.preinit(html);
				}else{
				    K.sendingInfo();
					$.ajax({
				        url: p.url,
				        success: function(html){
				        	K.clearNoti();
				        	$.jStorage.set(p.url, html);
				        	p.preinit(html);
				        },
				        type: "POST"
					});
				}
			}
		}
	},
	locate: function(){
		var right = 10;
		$('.ui-chatbox:visible').each(function(){
			$(this).css('right',right+'px');
			right += 250;
		});
	}
};
/*
 * CHAT CONECTADOS
 */
$('#iconChat').click(function(){
	K.filter({
		id: 'filNotif',
		content: '<div id="filNotif"><div class="div-bottom ui-dialog-buttonpane ui-widget-content">'+
			'<label class="tahoma">Usuarios Conectados</label>'+
			'<div name="close"><span class="ui-icon ui-icon-circle-close" style="position: relative;"></span></div>'+
			'</div>'+
			'<div class="grid"><div class="gridBody"></div><div class="gridReference"><ul><li style="min-width: 378px;max-width: 378px;"></li></ul></div></div></div>',
		height: 250,
		width: 400,
		top: ($(this).offset().top+34),
		left: ($(this).offset().left-330),
		onContentLoaded: function(){
			var $f = $('#filNotif');
			$f.css({
				"box-shadow": "0px 0px 8px 8px #DDD",
				"padding": "0px",
				"height": ($('#pageWrapperMain').height()-16)+'px'
			});
			$f.find('.gridBody').height(($f.height()-60)+'px').css("overflow-x","hidden");
			$f.find('label:first').css({
				'font-size': '14px',
				'color': '#5c9ccc',
				'font-weight': 'bold'
			});
			$f.find('[name=close]').click(function(){
				$f.remove();
			}).css({
				"float": "right",
				'display': 'inline',
				'position': 'relative'/*,
				"margin-top": "5px"*/
			});
			$f.find('[name=btnMore]').button({icons: {primary: 'ui-icon-triangle-1-s'}}).button('disable');
			K.block({$element: $f.find('.gridBody')});
			$.post('ac/user/all',{online: 1},function(users){
				$('#iconChat label').html(users.items.length-1);
				if ( users.items.length > 0 ) { 
					for (i=0; i < users.items.length; i++) {
						result = users.items[i];
						if(result._id.$id!=K.session.user._id.$id){
							var $row = $f.find('.gridReference').clone();
							$li = $('li',$row);
							$li.eq(0).html( ciHelper.enti.formatName(result.owner) );
							$row.wrapInner('<a class="item" href="javascript: void(0);" name="'+result._id.$id+'" />');
							$row.find('a').click(function(){
								var data = $(this).data('data');
								ndChat.window({
									id: $(this).attr('name'),
									user: data
								});
								$f.remove();
							}).data('data',result);
							$f.find(".gridBody").append( $row.children() );
						}
			        }
				}
				K.unblock({$element: $('#filNotif .gridBody')});
			//});
			},'json');
		}
	});
});