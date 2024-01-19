/*******************************************************************************
Grupos */
acGrup = {
	init: function(){
		K.initMode({
			mode: 'ac',
			action: 'acGrup',
			titleBar: {
				title: 'Grupos'
			}
		});
		
		new K.Panel({
			onContentLoaded: function(){
		   		var $grid = new K.grid({
					cols: ['','Cuenta','Nombre'],
					data: 'ac/grup/lista',
					params: {},
					itemdescr: 'grupo(s)',
					toolbarHTML: '<button name="btnAgregar" class="btn btn-success"><i class="fa fa-plus"></i> Agregar</button>',
					onContentLoaded: function($el){
						$el.find('[name=btnAgregar]').click(function(){
							acGrup.windowNew();
						});
					},
					onLoading: function(){ 
						K.block({$element: $('#pageWrapperMain')});
					},
					onComplete: function(){ 
						K.unblock({$element: $('#pageWrapperMain')});
					},
					fill: function(data,$row){
						$row.append('<td>');
						$row.append('<td>'+data.groupid+'</td>');
						$row.append('<td>'+data.descr+'</td>');
						$row.data('id',data._id.$id).dblclick(function(){
							acGrup.windowEdit({_id: $(this).data('id'),nomb: $(this).find('td:eq(2)').html()});
						}).contextMenu("conMenListEd", {
							onShowMenu: function($row, menu) {
								$('#conMenListEd_ver,#conMenListEd_hab,#conMenListEd_des',menu).remove();
								return menu;
							},
							bindings: {
								'conMenListEd_edi': function(t) {
									acGrup.windowEdit({id: K.tmp.data('id'),nomb: K.tmp.find('td:eq(2)').html()});
								}
							}
						});
						return $row;
					}
				});
			}
		});
	},
	windowNew: function(p){
		if(p==null) p = {};
		$.extend(p,{
			check: function(padre){
				p.$w.find('[name="'+padre+'"]').iCheck('check');
				if(p.$w.find('[name="'+padre+'"]').attr('padre')){
					var p_parent = p.$w.find('[name="'+padre+'"]').attr('padre');
					//p.$w.find('[name='+p_parent+']').attr('checked','checked');
					p.check(p_parent);
				}
			},
			uncheck: function(padre){
				$('[name^="'+padre+'."]').iCheck('uncheck');
			}
		});
		new K.Panel({
			title: 'Nuevo Grupo',
			contentURL: 'ac/grup/edit',
			buttons: {
				'Guardar': {
					icon: 'fa-save',
					type: 'success',
					f: function(){
						K.clearNoti();
						var data = {
							groupid: p.$w.find('[name=cuenta]').val(),
							descr: p.$w.find('[name=nomb]').val(),
							allowed: []
						};
						if(!p.$w.find('[name=cuenta]').data('dispo')){
							p.$w.find('[name=cuenta]').focus();
							return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Ingrese un nombre de cuenta v&aacute;lido!',type: 'error'});
						}
						if(data.descr==''){
							p.$w.find('[name=nomb]').focus();
							return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Ingrese un nombre de grupo!',type: 'error'});
						}
						for(var i=0; i<(p.$w.find('input:checked').length); i++){
							data.allowed.push({
								taskid: p.$w.find('input:checked').eq(i).attr('name')
							});
						}
						K.sendingInfo();
						p.$w.find('#div_buttons button').attr('disabled','disabled');
						$.post("ac/grup/save",data,function(result){
							K.clearNoti();
							K.notification({title: ciHelper.titleMessages.regiGua,text: "Grupo agregado!"});
							acGrup.init();
						},'json');
					}
				},
				'Cancelar': {
					icon: 'fa-ban',
					type: 'danger',
					f: function(){
						acGrup.init();
					}
				}
			},
			onContentLoaded: function(){
				p.$w = $('#mainPanel');
				K.block();
				p.$w.find('[name=cuenta]').blur(function(){
					var texto = $(this).val();
					if(texto==''){
						p.$w.find('[name=btnCue]').removeClass('btn-primary').addClass('btn-danger');
						p.$w.find('[name=btnCue] i').removeClass('fa-check').addClass('fa-close');
						K.notification({
							text: 'El nombre de grupo no se encuentra disponible.<br/>Elija otro nombre de usuario!',
							type: 'error'
						});
						return p.$w.find('[name=cuenta]').data('dispo',false);
					}
					K.block();
					$.post('ac/grup/validar',{groupid: texto},function(data){
						if(data.msj){
							p.$w.find('[name=btnCue]').removeClass('btn-danger').addClass('btn-primary');
							p.$w.find('[name=btnCue] i').removeClass('fa-close').addClass('fa-check');
							K.notification('El nombre de grupo est&aacute; disponible!');
							p.$w.find('[name=cuenta]').data('dispo',true);
						}else{
							p.$w.find('[name=btnCue]').removeClass('btn-primary').addClass('btn-danger');
							p.$w.find('[name=btnCue] i').removeClass('fa-check').addClass('fa-close');
							K.notification({
								text: 'El nombre de grupo no se encuentra disponible.<br/>Elija otro nombre de usuario!',
								type: 'error'
							});
							p.$w.find('[name=cuenta]').data('dispo',false);
						}
						K.unblock();
					},'json');
				}).data('dispo',false);
				K.grid({
					$el: p.$w.find('[name^=grid]'),
					cols: ['','Descripci&oacute;n'],
					search: false,
					onlyHtml: true
				});
				$.post('ac/perm',function(data){
					if(data.items==null)
						return K.notification({text: 'No hay permisos creados!',type: 'error'});
					for(var i=0; i<data.items.length; i++){
						var result = data.items[i],
						$row = $('<tr class="item">'),
						idperm = result.id,
						splitid = idperm.split("."),
						sangria = "";
						if(result.sangria=="1"){
							var sangria = "|—";	
						}else if(result.sangria=="2"){
							var sangria = "|—|—";								
						}else if(result.sangria=="3"){
							var sangria = "|—|—|—";								
						}
						var padre = '';
						if(result.padre){
							padre = ' padre="'+result.padre+'"';
						}
						$row.append('<td><input type="checkbox" id="rb_'+result.id+'_s" name="'+result.id+'"'+padre+'/></td>');
						$row.append('<td>'+sangria+' '+result.descr+'</td>');
						$row.find('input').iCheck({
							checkboxClass: 'icheckbox_square-green',
							radioClass: 'iradio_square-green'
						});
						$row.find('input').on('ifToggled', function(event){
							var deno = $(this).attr('name');
							if($(this).is(':checked')){
								if($(this).attr('padre'))
									p.check($(this).attr('padre'));
							}else{
								p.uncheck(deno);
							}
						});
						p.$w.find('[name=grid'+splitid[0]+'] tbody').append( $row );
					}
					K.unblock();
				},'json');
			}
		});
	},
	windowEdit: function(p){
		if(p==null) p = {};
		$.extend(p,{
			check: function(padre){
				p.$w.find('[name="'+padre+'"]').iCheck('check');
				if(p.$w.find('[name="'+padre+'"]').attr('padre')){
					var p_parent = p.$w.find('[name="'+padre+'"]').attr('padre');
					//p.$w.find('[name='+p_parent+']').attr('checked','checked');
					p.check(p_parent);
				}
			},
			uncheck: function(padre){
				$('[name^="'+padre+'."]').iCheck('uncheck');
			}
		});
		new K.Panel({
			title: 'Editar Grupo '+p.nomb,
			contentURL: 'ac/grup/edit',
			buttons: {
				'Guardar': {
					icon: 'fa-save',
					type: 'success',
					f: function(){
						K.clearNoti();
						var data = {
							_id: p.id,
							allowed: []
						};
						for(var i=0; i<(p.$w.find('input:checked').length); i++){
							data.allowed.push({
								taskid: p.$w.find('input:checked').eq(i).attr('name')
							});
						}
						K.sendingInfo();
						p.$w.find('#div_buttons button').attr('disabled','disabled');
						$.post("ac/grup/save",data,function(result){
							K.clearNoti();
							K.notification({title: ciHelper.titleMessages.regiGua,text: "Grupo agregado!"});
							acGrup.init();
						},'json');
					}
				},
				'Cancelar': {
					icon: 'fa-ban',
					type: 'danger',
					f: function(){
						acGrup.init();
					}
				}
			},
			onContentLoaded: function(){
				p.$w = $('#mainPanel');
				K.block();
				K.grid({
					$el: p.$w.find('[name^=grid]'),
					cols: ['','Descripci&oacute;n'],
					search: false,
					onlyHtml: true
				});
				$.post('ac/perm',function(data){
					if(data.items==null)
						return K.notification({text: 'No hay permisos creados!',type: 'error'});
					for(var i=0; i<data.items.length; i++){
						var result = data.items[i],
						$row = $('<tr class="item">'),
						idperm = result.id,
						splitid = idperm.split("."),
						sangria = "";
						if(result.sangria=="1"){
							var sangria = "|—";	
						}else if(result.sangria=="2"){
							var sangria = "|—|—";								
						}else if(result.sangria=="3"){
							var sangria = "|—|—|—";								
						}
						var padre = '';
						if(result.padre){
							padre = ' padre="'+result.padre+'"';
						}
						$row.append('<td><input type="checkbox" id="rb_'+result.id+'_s" name="'+result.id+'"'+padre+'/></td>');
						$row.append('<td>'+sangria+' '+result.descr+'</td>');
						$row.find('input').iCheck({
							checkboxClass: 'icheckbox_square-green',
							radioClass: 'iradio_square-green'
						});
						p.$w.find('[name=grid'+splitid[0]+'] tbody').append( $row );
					}
					$.post('ac/grup/get','_id='+p.id,function(data){
						p.$w.find('[name=cuenta]').val(data.groupid).attr('disabled','disabled');
						p.$w.find('[name=nomb]').val(data.descr).attr('disabled','disabled');
						if(data.allowed!=null){
							for(var i=0; i<data.allowed.length; i++){
								var $radio = $('[name="'+data.allowed[i].taskid+'"]:eq(0)');
								//$('[name="'+data.allowed[i].taskid+'"]:eq(1)').removeAttr('checked');
								$radio.iCheck('check');
								//$radio.closest('li').buttonset().find('span').css('margin-top','0px');
							}
						}
						p.$w.find('tbody input').on('ifToggled', function(event){
							var deno = $(this).attr('name');
							if($(this).is(':checked')){
								if($(this).attr('padre'))
									p.check($(this).attr('padre'));
							}else{
								p.uncheck(deno);
							}
						});
						K.unblock();
					},'json');
				},'json');
			}
		});
	}
};
define(
	function(){
		return acGrup;
	}
);