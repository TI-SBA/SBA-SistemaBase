tiComp = {
	states: {
		H: {
			descr: "Habilitado",
			color: "green",
			label: '<span class="label label-success">Habilitado</span>'
		},
		D:{
			descr: "Deshabilitado",
			color: "#CCCCCC",
			label: '<span class="label label-default">Deshabilitado</span>'
		}
	},
	dbRel: function(item){
		return {
			_id: item._id.$id,
			nomb: item.nomb
		};
	},
	windows: [
		'Windows 95',
		'Windows 98',
		'Windows 2000',
		'Windows Millenium',
		'Windows XP',
		'Windows 2003',
		'Windows Server 2003',
		'Windows 2008',
		'Windows Server 2008',
		'Windows 8',
		'Windows 8.1',
		'Windows 10'
	],
	office: [
		'Office 97',
		'Office 2000',
		'Office 2003',
		'Office 2007',
		'Office 2010',
		'Office 2013',
		'Office 2015'
	],
	proxy: {
		R: 'Restricci&oacute;n',
		P: 'Permitido'
	},
	init: function(){
		K.initMode({
			mode: 'ti',
			action: 'tiComp',
			titleBar: {
				title: 'Listado de Computadoras'
			}
		});
		
		new K.Panel({
			onContentLoaded: function(){
		   		var $grid = new K.grid({
					cols: ['','',
						{n:'Local',f:'local.descr'},
						{n:'Nombre',f:'nomb'},
						{n:'IP',f:'order'},
						{n:'Oficina',f:'oficina'},
						{n:'Encargado',f:'encargado'},
						{n:'Filtro Proxy',f:'proxy'}
					],
					data: 'ti/comp/lista',
					params: {},
					itemdescr: 'computadora(s)',
					toolbarHTML: '<button name="btnAgregar" class="btn btn-success"><i class="fa fa-plus"></i> Agregar</button>',
					onContentLoaded: function($el){
						$el.find('[name=btnAgregar]').click(function(){
							tiComp.windowNew();
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
						$row.append('<td>'+tiComp.states[data.estado].label+'</td>');
						$row.append('<td>'+data.local.descr+'</td>');
						$row.append('<td>'+data.nomb+'</td>');
						$row.append('<td>'+data.ip+'</td>');
						$row.append('<td>'+data.oficina+'</td>');
						$row.append('<td>'+data.encargado+'</td>');
						$row.append('<td>'+tiComp.proxy[data.proxy]+'</td>');
						$row.data('id',data._id.$id).dblclick(function(){
							tiComp.windowDetails({_id: $(this).data('id'),nomb: $(this).find('td:eq(2)').html()});
						}).data('estado',data.estado).contextMenu("conMenListEd", {
							onShowMenu: function($row, menu) {
								$('#conMenListEd_ver',menu).remove();
								if($row.data('estado')=='H') $('#conMenListEd_hab',menu).remove();
								else $('#conMenListEd_edi,#conMenListEd_des',menu).remove();
								return menu;
							},
							bindings: {
								'conMenListEd_ver': function(t) {
									tiComp.windowDetails({id: K.tmp.data('id'),nomb: K.tmp.find('td:eq(2)').html()});
								},
								'conMenListEd_edi': function(t) {
									tiComp.windowEdit({id: K.tmp.data('id'),nomb: K.tmp.find('td:eq(2)').html()});
								},
								'conMenListEd_hab': function(t) {
									ciHelper.confirm('&#191;Desea <b>Habilitar</b> el Computadora <b>'+K.tmp.find('td:eq(2)').html()+'</b>&#63;',
									function(){
										K.sendingInfo();
										$.post('ti/comp/save',{_id: K.tmp.data('id'),estado: 'H'},function(){
											K.clearNoti();
											K.notification({title: 'Computadora Habilitado',text: 'La habilitaci&oacute;n se realiz&oacute; con &eacute;xito!'});
											tiComp.init();
										});
									},function(){
										$.noop();
									},'Habilitaci&oacute;n de Computadora');
								},
								'conMenListEd_des': function(t) {
									ciHelper.confirm('&#191;Desea <b>Deshabilitar</b> el Computadora <b>'+K.tmp.find('td:eq(2)').html()+'</b>&#63;',
									function(){
										K.sendingInfo();
										$.post('ti/comp/save',{_id: K.tmp.data('id'),estado: 'D'},function(){
											K.clearNoti();
											K.notification({title: 'Computadora Deshabilitado',text: 'La deshabilitaci&oacute;n se realiz&oacute; con &eacute;xito!'});
											tiComp.init();
										});
									},function(){
										$.noop();
									},'Deshabilitaci&oacute;n de Computadora');
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
		new K.Panel({
			title: 'Nueva Computadora',
			contentURL: 'ti/comp/edit',
			store: false,
			buttons: {
				"Guardar": {
					icon: 'fa-save',
					type: 'success',
					f: function(){
						K.clearNoti();
						var data = {
							local: p.$w.find('[name=local]').data('data'),
							tipo: p.$w.find('[name=tipo] option:selected').val(),
							proxy: p.$w.find('[name=proxy] option:selected').val(),
							oficina: p.$w.find('[name=oficina]').val(),
							encargado: p.$w.find('[name=encargado]').val(),
							nomb: p.$w.find('[name=nomb]').val(),
							mac: p.$w.find('[name=mac]').val(),
							ip: p.$w.find('[name=ip]').val(),
							windows: p.$w.find('[name=windows]').val(),
							windows_lic: p.$w.find('[name=windows_lic]').val(),
							office: p.$w.find('[name=office]').val(),
							office_lic: p.$w.find('[name=office_lic]').val(),
							team_id: p.$w.find('[name=team_id]').val(),
							team_pass: p.$w.find('[name=team_pass]').val(),
							fec: p.$w.find('[name=fec]').val(),
							observ: p.$w.find('[name=observ]').val()
						};
						if(data.local==null){
							p.$w.find('[name=btnLocal]').click();
							return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe seleccionar el Local de la Computadora!',type: 'error'});
						}else{
							data.local._id = data.local._id.$id;
						}
						if(data.ip==''){
							p.$w.find('[name=ip]').focus();
							return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar la IP de la Computadora!',type: 'error'});
						}
						if(data.oficina==''){
							p.$w.find('[name=oficina]').focus();
							return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar la Oficina de la Computadora!',type: 'error'});
						}
						if(data.encargado==''){
							p.$w.find('[name=encargado]').focus();
							return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar la persona encargada de la Computadora!',type: 'error'});
						}
						K.sendingInfo();
						p.$w.find('#div_buttons button').attr('disabled','disabled');
						$.post("ti/comp/save",data,function(result){
							K.clearNoti();
							K.notification({title: ciHelper.titleMessages.regiGua,text: "Computadora agregada!"});
							tiComp.init();
						},'json');
					}
				},
				"Cancelar": {
					icon: 'fa-ban',
					type: 'danger',
					f: function(){
						tiComp.init();
					}
				}
			},
			onContentLoaded: function(){
				p.$w = $('#mainPanel');
				p.$w.find('[name=btnLocal]').click(function(){
					mgTitu.windowSelectLocal({callback: function(data){
						p.$w.find('[name=local]').html(data.descr).data('data',data);
					}});
				});
				p.$w.find('[name=local]').html("Administración Central").data('data',{
					_id: {$id:"519d35d29c7684f0050000c2"},
					direccion: "Av. Piérola 201 - Arequipa, Arequipa",
					descr: "Administración Central"
				});
				p.$w.find('[name=fec]').datepicker();
				p.oficinas = [];
				$.post('mg/ofic/all',function(data){
					for(var i=0,j=data.length; i<j; i++){
						p.oficinas.push(data[i].nomb);
					}
					p.$w.find('[name=oficina]').typeahead({
						hint: true,
						highlight: true,
						minLength: 1
					},
					{
						name: 'oficinas',
						source: substringMatcher(p.oficinas)
					});
					p.$w.find('[name=windows]').typeahead({
						hint: true,
						highlight: true,
						minLength: 1
					},
					{
						name: 'windows',
						source: substringMatcher(tiComp.windows)
					});
					p.$w.find('[name=office]').typeahead({
						hint: true,
						highlight: true,
						minLength: 1
					},
					{
						name: 'office',
						source: substringMatcher(tiComp.office)
					});
					p.$w.find('[name=oficina]').parent().css('width','100%');
					p.$w.find('[name=windows]').parent().css('width','100%');
					p.$w.find('[name=office]').parent().css('width','100%');
				},'json');
			}
		});
	},
	windowEdit: function(p){
		if(p==null) p = {};
		new K.Panel({
			title: 'Nueva Computadora',
			contentURL: 'ti/comp/edit',
			store: false,
			buttons: {
				"Guardar": {
					icon: 'fa-save',
					type: 'success',
					f: function(){
						K.clearNoti();
						var data = {
							_id: p.id,
							tipo: p.$w.find('[name=tipo] option:selected').val(),
							proxy: p.$w.find('[name=proxy] option:selected').val(),
							local: p.$w.find('[name=local]').data('data'),
							oficina: p.$w.find('[name=oficina]').val(),
							encargado: p.$w.find('[name=encargado]').val(),
							nomb: p.$w.find('[name=nomb]').val(),
							mac: p.$w.find('[name=mac]').val(),
							ip: p.$w.find('[name=ip]').val(),
							windows: p.$w.find('[name=windows]').val(),
							windows_lic: p.$w.find('[name=windows_lic]').val(),
							office: p.$w.find('[name=office]').val(),
							office_lic: p.$w.find('[name=office_lic]').val(),
							team_id: p.$w.find('[name=team_id]').val(),
							team_pass: p.$w.find('[name=team_pass]').val(),
							fec: p.$w.find('[name=fec]').val(),
							observ: p.$w.find('[name=observ]').val()
						};
						if(data.local==null){
							p.$w.find('[name=btnLocal]').click();
							return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe seleccionar el Local de la Computadora!',type: 'error'});
						}else{
							data.local._id = data.local._id.$id;
						}
						if(data.ip==''){
							p.$w.find('[name=ip]').focus();
							return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar la IP de la Computadora!',type: 'error'});
						}
						if(data.oficina==''){
							p.$w.find('[name=oficina]').focus();
							return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar la Oficina de la Computadora!',type: 'error'});
						}
						if(data.encargado==''){
							p.$w.find('[name=encargado]').focus();
							return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar la persona encargada de la Computadora!',type: 'error'});
						}
						K.sendingInfo();
						p.$w.find('#div_buttons button').attr('disabled','disabled');
						$.post("ti/comp/save",data,function(result){
							K.clearNoti();
							K.notification({title: ciHelper.titleMessages.regiAct,text: "Computadora actualizada!"});
							tiComp.init();
						},'json');
					}
				},
				"Cancelar": {
					icon: 'fa-ban',
					type: 'danger',
					f: function(){
						tiComp.init();
					}
				}
			},
			onContentLoaded: function(){
				p.$w = $('#mainPanel');
				p.$w.find('[name=btnLocal]').click(function(){
					mgTitu.windowSelectLocal({callback: function(data){
						p.$w.find('[name=local]').html(data.descr).data('data',data);
					}});
				});
				p.$w.find('[name=local]').html("Administración Central").data('data',{
					_id: {$id:"519d35d29c7684f0050000c2"},
					direccion: "Av. Piérola 201 - Arequipa, Arequipa",
					descr: "Administración Central"
				});
				p.$w.find('[name=fec]').datepicker();
				p.oficinas = [];
				$.post('mg/ofic/all',function(data){
					for(var i=0,j=data.length; i<j; i++){
						p.oficinas.push(data[i].nomb);
					}
					p.$w.find('[name=oficina]').typeahead({
						hint: true,
						highlight: true,
						minLength: 1
					},
					{
						name: 'oficinas',
						source: substringMatcher(p.oficinas)
					});
					p.$w.find('[name=windows]').typeahead({
						hint: true,
						highlight: true,
						minLength: 1
					},
					{
						name: 'windows',
						source: substringMatcher(tiComp.windows)
					});
					p.$w.find('[name=office]').typeahead({
						hint: true,
						highlight: true,
						minLength: 1
					},
					{
						name: 'office',
						source: substringMatcher(tiComp.office)
					});
					p.$w.find('[name=oficina]').parent().css('width','100%');
					p.$w.find('[name=windows]').parent().css('width','100%');
					p.$w.find('[name=office]').parent().css('width','100%');
					$.post('ti/comp/get',{_id: p.id},function(data){
						p.$w.find('[name=local]').html(data.local.descr).data('data',data.local);
						p.$w.find('[name=proxy]').selectVal(data.proxy);
						p.$w.find('[name=tipo]').selectVal(data.tipo);
						p.$w.find('[name=oficina]').val(data.oficina);
						p.$w.find('[name=encargado]').val(data.encargado);
						p.$w.find('[name=nomb]').val(data.nomb);
						p.$w.find('[name=mac]').val(data.mac);
						p.$w.find('[name=ip]').val(data.ip);
						p.$w.find('[name=windows]').val(data.windows);
						p.$w.find('[name=windows_lic]').val(data.windows_lic);
						p.$w.find('[name=office]').val(data.office);
						p.$w.find('[name=office_lic]').val(data.office_lic);
						p.$w.find('[name=team_id]').val(data.team_id);
						p.$w.find('[name=team_pass]').val(data.team_pass);
						p.$w.find('[name=fec]').val(data.fec);
						p.$w.find('[name=observ]').val(data.observ);
					},'json');
				},'json');
			}
		});
	},
	windowSelect: function(p){
		new K.Modal({
			id: 'windowSelect',
			content: '<div name="tmp"></div>',
			width: 750,
			height: 400,
			title: 'Seleccionar Computadora',
			buttons: {
				"Seleccionar": {
					icon: 'fa-check',
					type: 'info',
					f: function(){
						if(p.$w.find('.highlights').data('data')!=null){
							p.callback(p.$w.find('.highlights').data('data'));
							K.closeWindow(p.$w.attr('id'));
						}else{
							K.clearNoti();
							return K.notification({
								title: ciHelper.titleMessages.infoReq,
								text: 'Debe seleccionar un item!',
								type: 'error'
							});
						}
					}
				},
				"Cancelar": {
					icon: 'fa-ban',
					type: 'danger',
					f: function(){
						K.closeWindow(p.$w.attr('id'));
					}
				}
			},
			onClose: function(){ p = null; },
			onContentLoaded: function(){
				p.$w = $('#windowSelect');
				p.$grid = new K.grid({
					$el: p.$w.find('[name=tmp]'),
					cols: ['','Nombre'],
					data: 'ti/comp/lista',
					params: {},
					itemdescr: 'computadora(s)',
					onLoading: function(){ 
						K.block({$element: p.$w});
					},
					onComplete: function(){ 
						K.unblock({$element: p.$w});
					},
					fill: function(data,$row){
						$row.append('<td>');
						$row.append('<td>'+data.nomb+'</td>');
						$row.data('data',data).dblclick(function(){
							p.$w.find('.modal-footer button:first').click();
						}).contextMenu('conMenListSel', {
							bindings: {
								'conMenListSel_sel': function(t) {
									p.$w.find('.modal-footer button:first').click();
								}
							}
						});
						return $row;
					}
				});
			}
		});
	}
};
define(
	['mg/enti','ct/pcon','mg/titu'],
	function(mgEnti,ctPcon,mgTitu){
		return tiComp;
	}
);