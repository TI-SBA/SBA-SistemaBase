tdTupa = {
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
	init: function(){
		K.initMode({
			mode: 'td',
			action: 'tdTupa',
			titleBar: {
				title: 'TUPA'
			}
		});
		
		new K.Panel({
			onContentLoaded: function(){
		   		var $grid = new K.grid({
					cols: [
						'',
						'',
						{n:'Organizaci&oacute;n',f:'organizacion.nomb'},
						{n:'Cod.',f:'item'},
						{n:'Nombre de Procedimiento',f:'titulo'},
						{n:'Modalidades',f:'item'},
						{n:'Plazo',f:'item'},
						{n:'Costo',f:'item'}
					],
					data: 'td/tupa/lista',
					params: {},
					itemdescr: 'organo(s) externo(s)',
					toolbarHTML: '<select name="ano"></select>&nbsp;<button name="btnAgregar" class="btn btn-success"><i class="fa fa-plus"></i> Agregar</button>',
					onContentLoaded: function($el){
						$el.find('[name=btnAgregar]').click(function(){
							tdTupa.windowNew({tupa: $el.find('[name=ano] option:selected').val()});
						});
						$.post('td/tupa/listatupas',function(data){
							if(data==null){
								tdTupa.windowNewTupa();
								return K.notification({text: 'Debe crear primero un TUPA!',type: 'error'});
							}
							var $cbo = $el.find('[name=ano]');
							for(var i=0; i<data.length; i++){
								$cbo.append('<option value="'+data[i]._id.$id+'">'+data[i].anio+'</option>');
								if(data[i].estado=='V')
									$cbo.find('option:last').attr('selected','selected');
							}
							$cbo.change(function(){
								$grid.reinit({params: {tupa: $(this).find('option:selected').val()}});
							}).change();
						},'json');
					},
					onLoading: function(){ 
						K.block({$element: $('#pageWrapperMain')});
					},
					onComplete: function(){ 
						K.unblock({$element: $('#pageWrapperMain')});
					},
					search: false,
					stopLoad: true,
					load: function(data,$tbody){
						for(var i=0,j=data.items.length; i<j; i++){
							var items = data.items[i];
							for(var xk=0; xk<items.length; xk++){
								var $row = $('<tr class="item">'),
								item = items[xk],
								mods = item.modalidades,
								row_mod = [];
								$row.append('<td rowspan="'+mods.length+'">');
								$row.append('<td rowspan="'+mods.length+'">'+tdTupa.states[item.estado].label+'</td>');
								$row.append('<td rowspan="'+mods.length+'">'+item.organizacion.nomb+'</td>');
								$row.append('<td rowspan="'+mods.length+'">'+item.item+'</td>');
								$row.append('<td rowspan="'+mods.length+'">'+item.titulo+'</td>');
								for(var k=0; k<mods.length; k++){
									if(k!=0){
										var $rowm = $('<tr class="item">');
										$rowm.append('<td>'+mods[k].descr+'</td>');
										$rowm.append('<td>'+mods[k].aprueba.plazo+' d&iacute;as</td>');
										if(mods[k].reqs!=null){
											var tmp_tot = 0,
											tmp_uit = 0;
											for (var ir=0; ir < mods[k].reqs.length; ir++) {
												if(mods[k].reqs[ir].soles!=null)
													tmp_tot += parseFloat(mods[k].reqs[ir].soles);
												else if(mods[k].reqs[ir].uit!=null)
													tmp_uit += parseFloat(mods[k].reqs[ir].uit);
											};
											if(tmp_tot!=0)
												$rowm.append('<td>S/.'+tmp_tot+'</td>');
											else if(tmp_uit!=0)
												$rowm.append('<td>'+tmp_uit+'% UIT</td>');
											else
												$rowm.append('<td>');
										}else
											$rowm.append('<td>');
										row_mod.push($rowm);
									}else{
										$row.append('<td>'+mods[k].descr+'</td>');
										$row.append('<td>'+mods[k].aprueba.plazo+' d&iacute;as</td>');
										if(mods[k].reqs!=null){
											var tmp_tot = 0,
											tmp_uit = 0;
											for (var ir=0; ir < mods[k].reqs.length; ir++) {
												if(mods[k].reqs[ir].soles!=null)
													tmp_tot += parseFloat(mods[k].reqs[ir].soles);
												else if(mods[k].reqs[ir].uit!=null)
													tmp_uit += parseFloat(mods[k].reqs[ir].uit);
											};
											if(tmp_tot!=0)
												$row.append('<td>S/.'+tmp_tot+'</td>');
											else if(tmp_uit!=0)
												$row.append('<td>'+tmp_uit+'% UIT</td>');
											else
												$row.append('<td>');
										}else
											$row.append('<td>');
									}
								}
								$row.data('id',item._id.$id).data('data',item).dblclick(function(){
									tdTupa.windowDetails({id: $(this).data('id'),nomb: $(this).find('td').eq(2).html(),goBack: function(){ tdTupa.init(); }});
								}).data('estado',item.estado).contextMenu('conMenListEd', {
									onShowMenu: function($row, menu) {
										if($row.data('estado')=='H') $('#conMenListEd_hab',menu).remove();
										else $('#conMenListEd_edi,#conMenListEd_des',menu).remove();
										return menu;
									},
									bindings: {
										'conMenListEd_ver': function(t) {
											tdTupa.windowDetails({id: K.tmp.data('id'),nomb: K.tmp.find('td').eq(2).html(),goBack: function(){ tdTupa.init(); }});
										},
										'conMenListEd_edi': function(t) {
											tdTupa.windowEdit({
												id: K.tmp.data('id'),
												nomb: K.tmp.find('td').eq(2).html(),
												tupa: $('#mainPanel [name=ano] option:selected').val()
											});
										},
										'conMenListEd_hab': function(t) {
											ciHelper.confirm('&#191;Desea <b>Habilitar</b> el Procedimiento <b>'+K.tmp.find('td:eq(2)').html()+'</b>&#63;',
											function(){
												K.sendingInfo();
												$.post('td/tupa/estado',{_id: K.tmp.data('id'),estado: 'H',nomb: K.tmp.find('td').eq(2).html()},function(){
													K.clearNoti();
													K.notification({title: 'Procedimiento Habilitado',text: 'La habilitaci&oacute;n se realiz&oacute; con &eacute;xito!'});
													tdTupa.init();
												});
											},function(){
												$.noop();
											},'Habilitaci&oacute;n de Procedimiento');
										},
										'conMenListEd_des': function(t) {
											ciHelper.confirm('&#191;Desea <b>Deshabilitar</b> el Procedimiento <b>'+K.tmp.find('td:eq(2)').html()+'</b>&#63;',
											function(){
												K.sendingInfo();
												$.post('td/tupa/estado',{_id: K.tmp.data('id'),estado: 'D',nomb: K.tmp.find('td').eq(2).html()},function(){
													K.clearNoti();
													K.notification({title: 'Procedimiento Deshabilitado',text: 'La deshabilitaci&oacute;n se realiz&oacute; con &eacute;xito!'});
													tdTupa.init();
												});
											},function(){
												$.noop();
											},'Deshabilitaci&oacute;n de Procedimiento');
										}
									}
								});
								$tbody.append($row);
								for(var k=0; k<mods.length; k++){
									$tbody.append(row_mod[k]);
								}
							}
						}
					}
				});
			}
		});
	},
	modalBleg: function(q){
		new K.Modal({
			id: 'modBleg',
			title: 'Editar Base Legal',
			contentURL: 'td/tupa/bleg',
			buttons: {
				'Actualizar': {
					icon: 'fa-refresh',
					type: 'success',
					f: function(){
						var bleg = {
							descr: q.$w.find('[name=descr]').val(),
							url: q.$w.find('[name=url]').val()
						};
						if(bleg.descr==''){
							q.$w.find('[name=descr]').focus();
							return K.notification({
								title: ciHelper.titleMessages.infoReq,
								text: 'Debe ingresar una Base Legal!',
								type: 'error'
							});
						}
						q.callback(bleg,q.$el);
						K.closeWindow('modBleg');
					}
				},
				'Cancelar': {
					type: 'danger',
					icon: 'fa-ban',
					f: function(){
						K.closeWindow('modBleg');
					}
				}
			},
			onContentLoaded: function(){
				q.$w = $('#modBleg');
				if(q.data!=null){
					q.$w.find('[name=descr]').val(q.data.descr);
					q.$w.find('[name=url]').val(q.data.url);
				}
			}
		});
	},
	modalReqs: function(q){
		new K.Modal({
			id: 'modReqs',
			title: 'Editar Requerimiento',
			contentURL: 'td/tupa/reqs',
			width: 500,
			store: false,
			buttons: {
				'Actualizar': {
					icon: 'fa-refresh',
					type: 'success',
					f: function(){
						var reqs = {
							descr: q.$w.find('[name=descr]').val(),
							item: q.$w.find('[name=item]').val()
						};
						if(reqs.item==''){
							q.$w.find('[name=item]').focus();
							return K.notification({
								title: ciHelper.titleMessages.infoReq,
								text: 'Debe ingresar un c&oacute;digo!',
								type: 'error'
							});
						}
						if(reqs.descr==''){
							q.$w.find('[name=descr]').focus();
							return K.notification({
								title: ciHelper.titleMessages.infoReq,
								text: 'Debe ingresar una descripci&oacute;n!',
								type: 'error'
							});
						}
						if(q.$w.find('[name=soles]').val()!=''){
							reqs.soles = q.$w.find('[name=soles]').val();
						}
						if(q.$w.find('[name=uit]').val()!=''){
							reqs.uit = q.$w.find('[name=uit]').val();
						}
						if(reqs.soles==null&&reqs.uit==null)
							reqs.gratuito = true;
						q.callback(reqs,q.$el);
						K.closeWindow('modReqs');
					}
				},
				'Cancelar': {
					type: 'danger',
					icon: 'fa-ban',
					f: function(){
						K.closeWindow('modReqs');
					}
				}
			},
			onContentLoaded: function(){
				q.$w = $('#modReqs');
				q.$w.find('[name=superior]').closest('.col-sm-6').hide();
				if(q.data!=null){
					q.$w.find('[name=descr]').val(q.data.descr);
					q.$w.find('[name=item]').val(q.data.item);
					if(q.data.soles)
						q.$w.find('[name=soles]').val(q.data.soles);
					if(q.data.uit)
						q.$w.find('[name=uit]').val(q.data.uit);
				}
			}
		});
	},
	windowNew: function(p){
		if(p==null) p = {};
		$.extend(p,{
			cbBleg: function(bleg,$el){
				var $row = $('<tr class="item">');
				$row.append('<td>'+bleg.descr+'</td>');
				$row.append('<td>'+bleg.url+'</td>');
				$row.append('<td><button name="btnEdi" class="btn btn-info"><i class="fa fa-pencil"></i></button>'+
					'&nbsp;<button name="btnEli" class="btn btn-danger"><i class="fa fa-trash-o"></i></button></td>');
				$row.find('[name=btnEdi]').click(function(){
					var $row = $(this).closest('.item');
					tdTupa.modalBleg({callback: function(bleg){
						$row.find('td:eq(0)').html(bleg.descr);
						$row.find('td:eq(1)').html(bleg.url);
						$row.data('data',bleg);
					},data: $(this).closest('.item').data('data')});
				});
				$row.find('[name=btnEli]').click(function(){
					$(this).closest('.item').remove();
				});
				$row.data('data',bleg);
				$el.find('tbody').append($row);
			},
			cbReqs: function(reqs,$el){
				var $row = $('<tr class="item">');
				$row.append('<td>'+reqs.item+'</td>');
				$row.append('<td>'+reqs.descr+'</td>');
				$row.append('<td>');
				if(reqs.soles!=null)
					$row.find('td:last').append('S/.'+reqs.soles);
				if(reqs.uit!=null){
					if($row.find('td:last').html()!='')
						$row.find('td:last').append('<br />');
					$row.find('td:last').append(reqs.uit+'% UIT');
				}
				if(reqs.soles==null&&reqs.uit==null)
					$row.find('td:last').append('Gratuito');
				$row.append('<td><button name="btnEdi" class="btn btn-info"><i class="fa fa-pencil"></i></button>'+
					'&nbsp;<button name="btnEli" class="btn btn-danger"><i class="fa fa-trash-o"></i></button></td>');
				$row.find('[name=btnEdi]').click(function(){
					var $row = $(this).closest('.item');
					tdTupa.modalReqs({callback: function(reqs){
						$row.find('td:eq(0)').html(reqs.item);
						$row.find('td:eq(1)').html(reqs.descr);
						$row.find('td:eq(2)').html('');
						if(reqs.soles!=null)
							$row.find('td:eq(2)').append('S/.'+reqs.soles);
						if(reqs.uit!=null){
							if($row.find('td:eq(2)').html()!='')
								$row.find('td:eq(2)').append('<br />');
							$row.find('td:eq(2)').append(reqs.uit+'% UIT');
						}
						if(reqs.soles==null&&reqs.uit==null)
							$row.find('td:eq(2)').append('Gratuito');
						$row.data('data',reqs);
					},data: $(this).closest('.item').data('data')});
				});
				$row.find('[name=btnEli]').click(function(){
					$(this).closest('.item').remove();
				});
				$row.data('data',reqs);
				$el.find('tbody').append($row);
			}
		});
		new K.Modal({
			id: 'modalMod',
			title: 'Tipo de Procedimiento',
			contentURL: 'td/tupa/tipo',
			store: false,
			buttons: {
				'Crear': {
					type: 'success',
					icon: 'fa-save',
					f: function(){
						p.num = $('#modalMod [name=num]').val();
						if(p.num!=''&&p.num!='0'){
							K.closeWindow('modalMod');
							new K.Panel({
								contentURL: 'td/tupa/edit',
								store: false,
								buttons: {
									'Guardar': {
										type: 'success',
										icon: 'fa-save',
										f: function(){
											K.clearNoti();
											var data = {
												item: p.$w.find('[name=item]').val(),
												organizacion: {
													_id: p.$w.find('[name=organizacion] option:selected').val(),
													nomb: p.$w.find('[name=organizacion] option:selected').html(),
													ext: (p.$w.find('[name=organizacion] option:selected').attr('ext')==1)?true:false
												},
												titulo: p.$w.find('[name=titulo]').val(),
												notas: p.$w.find('[name=notas]').val(),
												modalidades:[]
											};
											if(data.item==''){
												p.$w.find('[name=item]').focus();
												return K.notification({title: ciHelper.titleMessages.infoReq,type: 'error',text: 'Debe ingresar un c&oacute;digo de Procedimiento!'});
											}
											if(data.titulo==''){
												p.$w.find('[name=titulo]').focus();
												return K.notification({title: ciHelper.titleMessages.infoReq,type: 'error',text: 'Debe ingresar un t&iacute;tulo de Procedimiento!'});
											}
											for(var i=0; i<parseInt(p.num); i++){
												if(parseInt(p.num)==1)
													var $mod = p.$w.find('[id=mod1]');
												else
													var $mod = p.$w.find('[id=mods'+i+']');
												var tmp_mod = {
													descr: $mod.find('[name=desc_pro]').val(),
													item: $mod.find('[name=item_pro]').val()
												};
												if(tmp_mod.item==''){
													$mod.find('[name=item_pro]').focus();
													K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar un c&oacute;digo de modalidad!',type: 'error'});
													return false;
												}
												if(tmp_mod.descr==''){
													$mod.find('[name=desc_pro]').focus();
													K.notification({title: ciHelper.titleMessages.infoReq,type: 'error',text: 'Debe ingresar un t&iacute;tulo de modalidad!'});
													return false;
												}
												tmp_mod.calif = $mod.find('[name=calificacion] option:selected').val();
												tmp_mod.inicia = {
													organismo: {
														_id: $mod.find('[name=inicio_tramite] option:selected').val(),
														nomb: $mod.find('[name=inicio_tramite] option:selected').html(),
														ext: ($mod.find('[name=inicio_tramite] option:selected').attr('ext')==1)?true:false
													}
												};
												tmp_mod.aprueba = {
													plazo: $mod.find('[name=plazo_apr]').val()
												};
												if(tmp_mod.aprueba.plazo==''){
													$mod.find('[name=plazo_apr]').focus();
													K.notification({title: ciHelper.titleMessages.infoReq,type: 'error',text: 'Debe ingresar un plazo de aprobaci&oacute;n!'});
													return false;
												}
												tmp_mod.aprueba.organismo = {
													_id: $mod.find('[name=aprueba_tramite] option:selected').val(),
													nomb: $mod.find('[name=aprueba_tramite] option:selected').html(),
													ext: ($mod.find('[name=aprueba_tramite] option:selected').attr('ext')==1)?true:false
												};
												if($mod.find('[name=plazo_rec_res]').val()!=''){
													tmp_mod.reconsidera = {
														plazos: {
															presentacion: $mod.find('[name=plazo_rec_pre]').val(),
															resolucion: $mod.find('[name=plazo_rec_res]').val()
														},
														organismo: {
															_id: $mod.find('[name=reclamacion_tramite] option:selected').val(),
															nomb: $mod.find('[name=reclamacion_tramite] option:selected').html(),
															ext: ($mod.find('[name=reclamacion_tramite] option:selected').attr('ext')==1)?true:false
														}
													};
													if(tmp_mod.reconsidera.plazos.resolucion==''){
														$mod.find('[name=plazo_rec_res]').focus();
														K.notification({title: ciHelper.titleMessages.infoReq,type: 'error',text: 'Debe ingresar un plazo de resoluci&oacute;n!'});
														return false;
													}					
												}
												if($mod.find('[name=plazo_ape_res]').val()!=''){
													tmp_mod.apela = {
														plazos: {
															presentacion: $mod.find('[name=plazo_ape_pre]').val(),
															resolucion: $mod.find('[name=plazo_ape_res]').val()
														}
													};
													if(tmp_mod.apela.plazos.resolucion==''){
														$mod.find('[name=plazo_ape_res]').focus();
														K.notification({title: ciHelper.titleMessages.infoReq,type: 'error',text: 'Debe ingresar un plazo de resoluci&oacute;n!'});
														return false;
													}
													tmp_mod.apela.organismo = {
														_id: $mod.find('[name=apelacion_tramite] option:selected').val(),
														nomb: $mod.find('[name=apelacion_tramite] option:selected').html(),
														ext: ($mod.find('[name=apelacion_tramite] option:selected').attr('ext')==1)?true:false
													};					
												}
												tmp_mod.url_doc = $mod.find('[name=url_pro]').val();
												tmp_mod.notas = $mod.find('[name=notas_pro]').val();
												tmp_mod.blegs = [];
												for(var ii = 0; ii<p.$w.find('[name=gridBleg] .item').length; ii++){
													tmp_mod.blegs.push($mod.find('[name=gridBleg] .item').eq(ii).data('data'));
												}
												tmp_mod.reqs = [];
												for(var ii = 0; ii<p.$w.find('[name=gridReqs] .item').length; ii++){
													tmp_mod.reqs.push($mod.find('[name=gridReqs] .item').eq(ii).data('data'));
												}
												data.modalidades.push(tmp_mod);
											}
											var datos = {
												tupa: p.tupa,
												data: data
											};
											K.sendingInfo();
											p.$w.find('#div_buttons button').attr('disabled','disabled');
											$.post('td/tupa/save',datos,function(){
												K.clearNoti();
												K.notification({title: ciHelper.titleMessages.regiGua,text: 'Procedimiento agregado!'});
												tdTupa.init();
											});
										}
									},
									'Cancelar': {
										type: 'danger',
										icon: 'fa-ban',
										f: function(){
											tdTupa.init();
										}
									}
								},
								onContentLoaded: function(){
									p.$w = $('#mainPanel');
									new K.grid({
										$el: p.$w.find('[name=gridBleg]'),
										search: false,
										pagination: false,
										cols: ['Descripci&oacute;n','URL',''],
										onlyHtml: true,
										toolbarHTML: '<button name="btnAgregar" class="btn btn-success"><i class="fa fa-plus"></i> Agregar Base Legal</button>',
										onContentLoaded: function($el){
											$el.find('[name=btnAgregar]').click(function(){
												tdTupa.modalBleg({callback: p.cbBleg,$el: $(this).closest('[name=gridBleg]')});
											});
										}
									});
									new K.grid({
										$el: p.$w.find('[name=gridReqs]'),
										search: false,
										pagination: false,
										cols: ['Item','Descripci&oacute;n','Costo',''],
										onlyHtml: true,
										toolbarHTML: '<button name="btnAgregar" class="btn btn-success"><i class="fa fa-plus"></i> Agregar Base Legal</button>',
										onContentLoaded: function($el){
											$el.find('[name=btnAgregar]').click(function(){
												tdTupa.modalReqs({callback: p.cbReqs,$el: $(this).closest('[name=gridReqs]')});
											});
										}
									});
									if(parseInt(p.num)!=1){
										p.$w.find('[name=modalidades]').selectVal('1');
										p.$w.find('[name=modalidades]')
											.attr('disabled','disabled');
										for(var i=0,j=parseInt(p.num); i<j; i++){
											var $mod = p.$w.find('[name=div_mods] #mod1').clone();
											$mod.attr('id','mods'+(i));
											var $li = p.$w.find('[name=div_mods] li:eq(0)').clone();
											$li.find('a').attr('href','#mods'+(i))
												.attr('aria-controls','mods'+(i))
												.html('Modalidad '+(i+1));
											if(i!=0){
												$li.removeClass('active');
												$mod.removeClass('active');
											}
											$mod.find('[href=#tabs-1]').attr('aria-controls','tabsm'+i+'-1')
												.attr('href','#tabsm'+i+'-1');
											$mod.find('[id=tabs-1]').attr('id','tabsm'+i+'-1');
											$mod.find('[href=#tabs-2]').attr('aria-controls','tabsm'+i+'-2')
												.attr('href','#tabsm'+i+'-2');
											$mod.find('[id=tabs-2]').attr('id','tabsm'+i+'-2');
											$mod.find('[href=#tabs-3]').attr('aria-controls','tabsm'+i+'-3')
												.attr('href','#tabsm'+i+'-3');
											$mod.find('[id=tabs-3]').attr('id','tabsm'+i+'-3');
											$mod.find('[href=#tabs-4]').attr('aria-controls','tabsm'+i+'-4')
												.attr('href','#tabsm'+i+'-4');
											$mod.find('[id=tabs-4]').attr('id','tabsm'+i+'-4');
											$mod.find('[name=btnAgregar]:eq(0)').click(function(){
												var $tmp = $(this).closest('[name=gridBleg]');
												tdTupa.modalBleg({callback: p.cbBleg,$el: $tmp});
											});
											$mod.find('[name=btnAgregar]:eq(1)').click(function(){
												var $tmp = $(this).closest('[name=gridReqs]');
												tdTupa.modalReqs({callback: p.cbReqs,$el: $tmp});
											});
											p.$w.find('[name=div_mods] .nav-tabs:eq(0)').append($li);
											p.$w.find('[name=div_mods] .tab-content:eq(0)').append($mod);
										}
										p.$w.find('[name=div_mods] #mod1').remove();
										p.$w.find('[name=div_mods] li:eq(0)').remove();
									}else{
										p.$w.find('[name=modalidades]').selectVal('0');
										p.$w.find('[name=modalidades]')
											.attr('disabled','disabled');
									}
									K.block({$element: $('#pageWrapperMain')});
									$.post('td/tupa/get_info',function(data){
										p.grupos = data.orga;
										p.organos = data.orga;
										p.ext = data.ext;
										var $select = p.$w.find('[name=organizacion]');
										for(var i=0; i<p.grupos.length; i++){
											$select.append('<option ext="0" value="'+p.grupos[i]._id.$id+'">'+p.grupos[i].nomb+'</option>');
										}
										if(p.ext!=null)
											for(var i=0; i<p.ext.length; i++){
												p.$w.find('[name=inicio_tramite]').append('<option ext="1" value="'+p.ext[i]._id.$id+'">'+p.ext[i].nomb+'</option>');
												p.$w.find('[name=aprueba_tramite]').append('<option ext="1" value="'+p.ext[i]._id.$id+'">'+p.ext[i].nomb+'</option>');
												p.$w.find('[name=reclamacion_tramite]').append('<option ext="1" value="'+p.ext[i]._id.$id+'">'+p.ext[i].nomb+'</option>');
												p.$w.find('[name=apelacion_tramite]').append('<option ext="1" value="'+p.ext[i]._id.$id+'">'+p.ext[i].nomb+'</option>');
											}
										for(var i=0; i<p.organos.length; i++){
											p.$w.find('[name=inicio_tramite]').append('<option ext="0" value="'+p.organos[i]._id.$id+'">'+p.organos[i].nomb+'</option>');
											p.$w.find('[name=aprueba_tramite]').append('<option ext="0" value="'+p.organos[i]._id.$id+'">'+p.organos[i].nomb+'</option>');
											p.$w.find('[name=reclamacion_tramite]').append('<option ext="0" value="'+p.organos[i]._id.$id+'">'+p.organos[i].nomb+'</option>');
											p.$w.find('[name=apelacion_tramite]').append('<option ext="0" value="'+p.organos[i]._id.$id+'">'+p.organos[i].nomb+'</option>');
										}
										K.unblock({$element: $('#pageWrapperMain')});
									},'json');
								}
							});
						}else{
							K.notification({
								title: ciHelper.titleMessages.infoReq,
								text: 'Debe ingresar un n&uacute;mero de modalidades!',
								type: 'error'
							});
						}
					}
				},
				'Cancelar': {
					type: 'danger',
					icon: 'fa-ban',
					f: function(){
						$.noop();
					}
				}
			},
			onContentLoaded: function(){
				$('#modalMod [name=num]').val(1).numeric();
				$('#modalMod [name=tipo]').change(function(){
					var valor = $('#modalMod [name=tipo]:checked').val();
					if(valor=='0'){
						$('#modalMod .form-group').hide();
						$('#modalMod [name=num]').val(1);
					}else
						$('#modalMod .form-group').show();
						$('#modalMod [name=num]').val(1);
				}).change();
			}
		});
	},
	windowEdit: function(p){
		$.extend(p,{
			cbBleg: function(bleg,$el){
				var $row = $('<tr class="item">');
				$row.append('<td>'+bleg.descr+'</td>');
				$row.append('<td>'+bleg.url+'</td>');
				$row.append('<td><button name="btnEdi" class="btn btn-info"><i class="fa fa-pencil"></i></button>'+
					'&nbsp;<button name="btnEli" class="btn btn-danger"><i class="fa fa-trash-o"></i></button></td>');
				$row.find('[name=btnEdi]').click(function(){
					var $row = $(this).closest('.item');
					tdTupa.modalBleg({callback: function(bleg){
						$row.find('td:eq(0)').html(bleg.descr);
						$row.find('td:eq(1)').html(bleg.url);
						$row.data('data',bleg);
					},data: $(this).closest('.item').data('data')});
				});
				$row.find('[name=btnEli]').click(function(){
					$(this).closest('.item').remove();
				});
				$row.data('data',bleg);
				$el.find('tbody').append($row);
			},
			cbReqs: function(reqs,$el){
				var $row = $('<tr class="item">');
				$row.append('<td>'+reqs.item+'</td>');
				$row.append('<td>'+reqs.descr+'</td>');
				$row.append('<td>');
				if(reqs.soles!=null)
					$row.find('td:last').append('S/.'+reqs.soles);
				if(reqs.uit!=null){
					if($row.find('td:last').html()!='')
						$row.find('td:last').append('<br />');
					$row.find('td:last').append(reqs.uit+'% UIT');
				}
				if(reqs.soles==null&&reqs.uit==null)
					$row.find('td:last').append('Gratuito');
				$row.append('<td><button name="btnEdi" class="btn btn-info"><i class="fa fa-pencil"></i></button>'+
					'&nbsp;<button name="btnEli" class="btn btn-danger"><i class="fa fa-trash-o"></i></button></td>');
				$row.find('[name=btnEdi]').click(function(){
					var $row = $(this).closest('.item');
					tdTupa.modalReqs({callback: function(reqs){
						$row.find('td:eq(0)').html(reqs.item);
						$row.find('td:eq(1)').html(reqs.descr);
						$row.find('td:eq(2)').html('');
						if(reqs.soles!=null)
							$row.find('td:eq(2)').append('S/.'+reqs.soles);
						if(reqs.uit!=null){
							if($row.find('td:eq(2)').html()!='')
								$row.find('td:eq(2)').append('<br />');
							$row.find('td:eq(2)').append(reqs.uit+'% UIT');
						}
						if(reqs.soles==null&&reqs.uit==null)
							$row.find('td:eq(2)').append('Gratuito');
						$row.data('data',reqs);
					},data: $(this).closest('.item').data('data')});
				});
				$row.find('[name=btnEli]').click(function(){
					$(this).closest('.item').remove();
				});
				$row.data('data',reqs);
				$el.find('tbody').append($row);
			}
		});
		new K.Panel({
			contentURL: 'td/tupa/edit',
			store: false,
			buttons: {
				'Guardar': {
					type: 'success',
					icon: 'fa-save',
					f: function(){
						K.clearNoti();
						var data = {
							item: p.$w.find('[name=item]').val(),
							organizacion: {
								_id: p.$w.find('[name=organizacion] option:selected').val(),
								nomb: p.$w.find('[name=organizacion] option:selected').html(),
								ext: (p.$w.find('[name=organizacion] option:selected').attr('ext')==1)?true:false
							},
							titulo: p.$w.find('[name=titulo]').val(),
							notas: p.$w.find('[name=notas]').val(),
							modalidades:[]
						};
						if(data.item==''){
							p.$w.find('[name=item]').focus();
							return K.notification({title: ciHelper.titleMessages.infoReq,type: 'error',text: 'Debe ingresar un c&oacute;digo de Procedimiento!'});
						}
						if(data.titulo==''){
							p.$w.find('[name=titulo]').focus();
							return K.notification({title: ciHelper.titleMessages.infoReq,type: 'error',text: 'Debe ingresar un t&iacute;tulo de Procedimiento!'});
						}
						for(var i=0; i<parseInt(p.num); i++){
							if(parseInt(p.num)==1)
								var $mod = p.$w.find('[id=mod1]');
							else
								var $mod = p.$w.find('[id=mods'+i+']');
							var tmp_mod = {
								descr: $mod.find('[name=desc_pro]').val(),
								item: $mod.find('[name=item_pro]').val()
							};
							if(tmp_mod.item==''){
								$mod.find('[name=item_pro]').focus();
								K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar un c&oacute;digo de modalidad!',type: 'error'});
								return false;
							}
							if(tmp_mod.descr==''){
								$mod.find('[name=desc_pro]').focus();
								K.notification({title: ciHelper.titleMessages.infoReq,type: 'error',text: 'Debe ingresar un t&iacute;tulo de modalidad!'});
								return false;
							}
							tmp_mod.calif = $mod.find('[name=calificacion] option:selected').val();
							tmp_mod.inicia = {
								organismo: {
									_id: $mod.find('[name=inicio_tramite] option:selected').val(),
									nomb: $mod.find('[name=inicio_tramite] option:selected').html(),
									ext: ($mod.find('[name=inicio_tramite] option:selected').attr('ext')==1)?true:false
								}
							};
							tmp_mod.aprueba = {
								plazo: $mod.find('[name=plazo_apr]').val()
							};
							if(tmp_mod.aprueba.plazo==''){
								$mod.find('[name=plazo_apr]').focus();
								K.notification({title: ciHelper.titleMessages.infoReq,type: 'error',text: 'Debe ingresar un plazo de aprobaci&oacute;n!'});
								return false;
							}
							tmp_mod.aprueba.organismo = {
								_id: $mod.find('[name=aprueba_tramite] option:selected').val(),
								nomb: $mod.find('[name=aprueba_tramite] option:selected').html(),
								ext: ($mod.find('[name=aprueba_tramite] option:selected').attr('ext')==1)?true:false
							};
							if($mod.find('[name=plazo_rec_res]').val()!=''){
								tmp_mod.reconsidera = {
									plazos: {
										presentacion: $mod.find('[name=plazo_rec_pre]').val(),
										resolucion: $mod.find('[name=plazo_rec_res]').val()
									},
									organismo: {
										_id: $mod.find('[name=reclamacion_tramite] option:selected').val(),
										nomb: $mod.find('[name=reclamacion_tramite] option:selected').html(),
										ext: ($mod.find('[name=reclamacion_tramite] option:selected').attr('ext')==1)?true:false
									}
								};
								if(tmp_mod.reconsidera.plazos.resolucion==''){
									$mod.find('[name=plazo_rec_res]').focus();
									K.notification({title: ciHelper.titleMessages.infoReq,type: 'error',text: 'Debe ingresar un plazo de resoluci&oacute;n!'});
									return false;
								}					
							}
							if($mod.find('[name=plazo_ape_res]').val()!=''){
								tmp_mod.apela = {
									plazos: {
										presentacion: $mod.find('[name=plazo_ape_pre]').val(),
										resolucion: $mod.find('[name=plazo_ape_res]').val()
									}
								};
								if(tmp_mod.apela.plazos.resolucion==''){
									$mod.find('[name=plazo_ape_res]').focus();
									K.notification({title: ciHelper.titleMessages.infoReq,type: 'error',text: 'Debe ingresar un plazo de resoluci&oacute;n!'});
									return false;
								}
								tmp_mod.apela.organismo = {
									_id: $mod.find('[name=apelacion_tramite] option:selected').val(),
									nomb: $mod.find('[name=apelacion_tramite] option:selected').html(),
									ext: ($mod.find('[name=apelacion_tramite] option:selected').attr('ext')==1)?true:false
								};					
							}
							tmp_mod.url_doc = $mod.find('[name=url_pro]').val();
							tmp_mod.notas = $mod.find('[name=notas_pro]').val();
							tmp_mod.blegs = [];
							for(var ii = 0; ii<p.$w.find('[name=gridBleg] .item').length; ii++){
								tmp_mod.blegs.push($mod.find('[name=gridBleg] .item').eq(ii).data('data'));
							}
							tmp_mod.reqs = [];
							for(var ii = 0; ii<p.$w.find('[name=gridReqs] .item').length; ii++){
								tmp_mod.reqs.push($mod.find('[name=gridReqs] .item').eq(ii).data('data'));
							}
							data.modalidades.push(tmp_mod);
						}
						var datos = {
							_id: p.id,
							tupa: p.tupa,
							data: data
						};
						K.sendingInfo();
						p.$w.find('#div_buttons button').attr('disabled','disabled');
						$.post('td/tupa/update',datos,function(){
							K.clearNoti();
							K.notification({title: ciHelper.titleMessages.regiGua,text: 'Procedimiento agregado!'});
							tdTupa.init();
						});
					}
				},
				'Cancelar': {
					type: 'danger',
					icon: 'fa-ban',
					f: function(){
						tdTupa.init();
					}
				}
			},
			onContentLoaded: function(){
				p.$w = $('#mainPanel');
				new K.grid({
					$el: p.$w.find('[name=gridBleg]'),
					search: false,
					pagination: false,
					cols: ['Descripci&oacute;n','URL',''],
					onlyHtml: true,
					toolbarHTML: '<button name="btnAgregar" class="btn btn-success"><i class="fa fa-plus"></i> Agregar Base Legal</button>',
					onContentLoaded: function($el){
						$el.find('[name=btnAgregar]').click(function(){
							tdTupa.modalBleg({callback: p.cbBleg,$el: $(this).closest('[name=gridBleg]')});
						});
					}
				});
				new K.grid({
					$el: p.$w.find('[name=gridReqs]'),
					search: false,
					pagination: false,
					cols: ['Item','Descripci&oacute;n','Costo',''],
					onlyHtml: true,
					toolbarHTML: '<button name="btnAgregar" class="btn btn-success"><i class="fa fa-plus"></i> Agregar Base Legal</button>',
					onContentLoaded: function($el){
						$el.find('[name=btnAgregar]').click(function(){
							tdTupa.modalReqs({callback: p.cbReqs,$el: $(this).closest('[name=gridReqs]')});
						});
					}
				});
				K.block({$element: $('#pageWrapperMain')});
				$.post('td/tupa/get_info',function(data){
					p.grupos = data.orga;
					p.organos = data.orga;
					p.ext = data.ext;
					var $select = p.$w.find('[name=organizacion]');
					for(var i=0; i<p.grupos.length; i++){
						$select.append('<option ext="0" value="'+p.grupos[i]._id.$id+'">'+p.grupos[i].nomb+'</option>');
					}
					if(p.ext!=null)
						for(var i=0; i<p.ext.length; i++){
							p.$w.find('[name=inicio_tramite]').append('<option ext="1" value="'+p.ext[i]._id.$id+'">'+p.ext[i].nomb+'</option>');
							p.$w.find('[name=aprueba_tramite]').append('<option ext="1" value="'+p.ext[i]._id.$id+'">'+p.ext[i].nomb+'</option>');
							p.$w.find('[name=reclamacion_tramite]').append('<option ext="1" value="'+p.ext[i]._id.$id+'">'+p.ext[i].nomb+'</option>');
							p.$w.find('[name=apelacion_tramite]').append('<option ext="1" value="'+p.ext[i]._id.$id+'">'+p.ext[i].nomb+'</option>');
						}
					for(var i=0; i<p.organos.length; i++){
						p.$w.find('[name=inicio_tramite]').append('<option ext="0" value="'+p.organos[i]._id.$id+'">'+p.organos[i].nomb+'</option>');
						p.$w.find('[name=aprueba_tramite]').append('<option ext="0" value="'+p.organos[i]._id.$id+'">'+p.organos[i].nomb+'</option>');
						p.$w.find('[name=reclamacion_tramite]').append('<option ext="0" value="'+p.organos[i]._id.$id+'">'+p.organos[i].nomb+'</option>');
						p.$w.find('[name=apelacion_tramite]').append('<option ext="0" value="'+p.organos[i]._id.$id+'">'+p.organos[i].nomb+'</option>');
					}
					$.post('td/tupa/get','id='+p.id,function(data){
						p.estado = data.estado;
						p.$w.find('[name=item]').val(data.item);
						p.$w.find('[name=organizacion]').selectVal(data.organizacion._id.$id);
						p.$w.find('[name=titulo]').val(data.titulo);
						p.$w.find('[name=notas]').val(data.notas);
						p.$w.find('[name=descr]').val(data.descr);
						p.$w.find('[name=notas]').val(data.notas);
						p.num = data.modalidades.length;
						if(data.modalidades.length>1){
							p.$w.find('[name=modalidades]').selectVal('1');
							p.$w.find('[name=modalidades]')
								.attr('disabled','disabled');
							for(var i=0,j=data.modalidades.length; i<j; i++){
								var $mod = p.$w.find('[name=div_mods] #mod1').clone();
								$mod.attr('id','mods'+(i));
								var $li = p.$w.find('[name=div_mods] li:eq(0)').clone();
								$li.find('a').attr('href','#mods'+(i))
									.attr('aria-controls','mods'+(i))
									.html('Modalidad '+(i+1));
								if(i!=0){
									$li.removeClass('active');
									$mod.removeClass('active');
								}
								var mod = data.modalidades[i];
								$mod.find('[name=modalidades]').selectVal('0');
								$mod.find('[name=modalidades]').attr('disabled','disabled');
								$mod.find('#mod1 .row:eq(0)').remove();
								$mod.find('[name=item_pro]').val(mod.item);
								$mod.find('[name=plazo_pro]').val(mod.plazo);
								$mod.find('[name=desc_pro]').val(mod.descr);
								$mod.find('[name=calificacion]').selectVal(mod.calif);
								$mod.find('[name=inicio_tramite]').selectVal(mod.inicia.organismo._id.$id);
								$mod.find('[name=aprueba_tramite]').selectVal(mod.aprueba.organismo._id.$id);
								$mod.find('[name=plazo_apr]').val(mod.aprueba.plazo);
								if(mod.reconsidera!=null){
									$mod.find('[name=reclamacion_tramite]').selectVal(mod.reconsidera.organismo._id.$id);
									$mod.find('[name=plazo_rec_pre]').val(mod.reconsidera.plazos.presentacion);
									$mod.find('[name=plazo_rec_res]').val(mod.reconsidera.plazos.resolucion);
								}
								if(mod.apela!=null){
									$mod.find('[name=apelacion_tramite]').selectVal(mod.apela.organismo._id.$id);
									$mod.find('[name=plazo_ape_pre]').val(mod.apela.plazos.presentacion);
									$mod.find('[name=plazo_ape_res]').val(mod.apela.plazos.resolucion);
								}
								$mod.find('[name=url_pro]').val(mod.url_doc);
								$mod.find('[name=notas_pro]').val(mod.notas);
								if(mod.blegs!=null){
									for(var id = 0; id<mod.blegs.length; id++){
										var $row = $('<tr class="item">');
										$row.append('<td>'+mod.blegs[id].descr+'</td>');
										$row.append('<td>'+mod.blegs[id].url+'</td>');
										$row.append('<td><button name="btnEdi" class="btn btn-info"><i class="fa fa-pencil"></i></button>'+
											'&nbsp;<button name="btnEli" class="btn btn-danger"><i class="fa fa-trash-o"></i></button></td>');
										$row.find('[name=btnEdi]').click(function(){
											var $row = $(this).closest('.item');
											tdTupa.modalBleg({callback: function(bleg){
												$row.find('td:eq(0)').html(bleg.descr);
												$row.find('td:eq(1)').html(bleg.url);
												$row.data('data',bleg);
											},data: $(this).closest('.item').data('data')});
										});
										$row.find('[name=btnEli]').click(function(){
											$(this).closest('.item').remove();
										});
										$row.data('data',mod.blegs[id]);
										$mod.find("[name=gridBleg] tbody").append($row);
									}
								}
								if(mod.reqs!=null){
									for(var id = 0; id<mod.reqs.length; id++){
										var $row = $('<tr class="item">');
										$row.append('<td>'+mod.reqs[id].item+'</td>');
										$row.append('<td>'+mod.reqs[id].descr+'</td>');
										$row.append('<td>');
										if(mod.reqs[id].soles!=null) $row.find('td:last').append('S/.'+mod.reqs[id].soles+'<br />');
										if(mod.reqs[id].uit!=null) $row.find('td:last').append(mod.reqs[id].uit+'% UIT<br />');
										if(mod.reqs[id].gratuito!=null) $row.find('td:last').append('Gratuito');
										$row.append('<td><button name="btnEdi" class="btn btn-info"><i class="fa fa-pencil"></i></button>'+
											'&nbsp;<button name="btnEli" class="btn btn-danger"><i class="fa fa-trash-o"></i></button></td>');
										$row.find('[name=btnEdi]').click(function(){
											var $row = $(this).closest('.item');
											tdTupa.modalReqs({callback: function(reqs){
												$row.find('td:eq(0)').html(reqs.item);
												$row.find('td:eq(1)').html(reqs.descr);
												$row.find('td:eq(2)').html('');
												if(reqs.soles!=null)
													$row.find('td:eq(2)').append('S/.'+reqs.soles);
												if(reqs.uit!=null){
													if($row.find('td:eq(2)').html()!='')
														$row.find('td:eq(2)').append('<br />');
													$row.find('td:eq(2)').append(reqs.uit+'% UIT');
												}
												if(reqs.soles==null&&reqs.uit==null)
													$row.find('td:eq(2)').append('Gratuito');
												$row.data('data',reqs);
											},data: $(this).closest('.item').data('data')});
										});
										$row.find('[name=btnEli]').click(function(){
											$(this).closest('.item').remove();
										});
										$row.data('data',mod.reqs[id]);
										$mod.find("[name=gridReqs] tbody").append($row);
									}
								}
								$mod.find('[href=#tabs-1]').attr('aria-controls','tabsm'+i+'-1')
									.attr('href','#tabsm'+i+'-1');
								$mod.find('[id=tabs-1]').attr('id','tabsm'+i+'-1');
								$mod.find('[href=#tabs-2]').attr('aria-controls','tabsm'+i+'-2')
									.attr('href','#tabsm'+i+'-2');
								$mod.find('[id=tabs-2]').attr('id','tabsm'+i+'-2');
								$mod.find('[href=#tabs-3]').attr('aria-controls','tabsm'+i+'-3')
									.attr('href','#tabsm'+i+'-3');
								$mod.find('[id=tabs-3]').attr('id','tabsm'+i+'-3');
								$mod.find('[href=#tabs-4]').attr('aria-controls','tabsm'+i+'-4')
									.attr('href','#tabsm'+i+'-4');
								$mod.find('[id=tabs-4]').attr('id','tabsm'+i+'-4');
								$mod.find('[name=btnAgregar]:eq(0)').click(function(){
									var $tmp = $(this).closest('[name=gridBleg]');
									tdTupa.modalBleg({callback: p.cbBleg,$el: $tmp});
								});
								$mod.find('[name=btnAgregar]:eq(1)').click(function(){
									var $tmp = $(this).closest('[name=gridReqs]');
									tdTupa.modalReqs({callback: p.cbReqs,$el: $tmp});
								});
								p.$w.find('[name=div_mods] .nav-tabs:eq(0)').append($li);
								p.$w.find('[name=div_mods] .tab-content:eq(0)').append($mod);
							}
							p.$w.find('[name=div_mods] #mod1').remove();
							p.$w.find('[name=div_mods] li:eq(0)').remove();
						}else{
							var mod = data.modalidades[0];
							p.$w.find('[name=modalidades]').selectVal('0');
							//p.$w.find('#mod1 .row:eq(0)').remove();
							p.$w.find('[name=item_pro]').val(mod.item);
							p.$w.find('[name=plazo_pro]').val(mod.plazo);
							p.$w.find('[name=desc_pro]').val(mod.descr);
							p.$w.find('[name=calificacion]').selectVal(mod.calif);
							p.$w.find('[name=calificacion]').attr('disabled','disabled');
							p.$w.find('[name=inicio_tramite]').selectVal(mod.inicia.organismo._id.$id);
							p.$w.find('[name=aprueba_tramite]').selectVal(mod.aprueba.organismo._id.$id);
							p.$w.find('[name=plazo_apr]').val(mod.aprueba.plazo);
							if(data.reconsidera!=null){
								p.$w.find('[name=reclamacion_tramite]').selectVal(mod.reconsidera.organismo._id.$id);
								p.$w.find('[name=plazo_rec_pre]').val(mod.reconsidera.plazos.presentacion);
								p.$w.find('[name=plazo_rec_res]').val(mod.reconsidera.plazos.resolucion);
							}
							if(data.apela!=null){
								p.$w.find('[name=apelacion_tramite]').selectVal(data.apela.organismo._id.$id);
								p.$w.find('[name=plazo_ape_pre]').val(data.apela.plazos.presentacion);
								p.$w.find('[name=plazo_ape_res]').val(data.apela.plazos.resolucion);
							}
							p.$w.find('[name=url_pro]').val(data.url_doc);
							p.$w.find('[name=notas_pro]').val(data.notas);
							if(mod.blegs!=null){
								for(var i = 0; i<mod.blegs.length; i++){
									var $row = $('<tr class="item">');
									$row.append('<td>'+mod.blegs[i].descr+'</td>');
									$row.append('<td>'+mod.blegs[i].url+'</td>');
									$row.append('<td><button name="btnEdi" class="btn btn-info"><i class="fa fa-pencil"></i></button>'+
										'&nbsp;<button name="btnEli" class="btn btn-danger"><i class="fa fa-trash-o"></i></button></td>');
									$row.find('[name=btnEdi]').click(function(){
										var $row = $(this).closest('.item');
										tdTupa.modalBleg({callback: function(bleg){
											$row.find('td:eq(0)').html(bleg.descr);
											$row.find('td:eq(1)').html(bleg.url);
											$row.data('data',bleg);
										},data: $(this).closest('.item').data('data')});
									});
									$row.find('[name=btnEli]').click(function(){
										$(this).closest('.item').remove();
									});
									$row.data('data',mod.blegs[id]);
									p.$w.find("[name=gridBleg] tbody").append($row);
								}
							}
							if(mod.reqs!=null){
								for(var i = 0; i<mod.reqs.length; i++){
									var $row = $('<tr class="item">');
									$row.append('<td>'+mod.reqs[i].item+'</td>');
									$row.append('<td>'+mod.reqs[i].descr+'</td>');
									$row.append('<td>');
									if(mod.reqs[i].soles!=null) $row.find('td:last').append('S/.'+mod.reqs[i].soles+'<br />');
									if(mod.reqs[i].uit!=null) $row.find('td:last').append(mod.reqs[i].uit+'% UIT<br />');
									if(mod.reqs[i].gratuito!=null) $row.find('td:last').append('Gratuito');
									$row.append('<td><button name="btnEdi" class="btn btn-info"><i class="fa fa-pencil"></i></button>'+
										'&nbsp;<button name="btnEli" class="btn btn-danger"><i class="fa fa-trash-o"></i></button></td>');
									$row.find('[name=btnEdi]').click(function(){
										var $row = $(this).closest('.item');
										tdTupa.modalReqs({callback: function(reqs){
											$row.find('td:eq(0)').html(reqs.item);
											$row.find('td:eq(1)').html(reqs.descr);
											$row.find('td:eq(2)').html('');
											if(reqs.soles!=null)
												$row.find('td:eq(2)').append('S/.'+reqs.soles);
											if(reqs.uit!=null){
												if($row.find('td:eq(2)').html()!='')
													$row.find('td:eq(2)').append('<br />');
												$row.find('td:eq(2)').append(reqs.uit+'% UIT');
											}
											if(reqs.soles==null&&reqs.uit==null)
												$row.find('td:eq(2)').append('Gratuito');
											$row.data('data',reqs);
										},data: $(this).closest('.item').data('data')});
									});
									$row.find('[name=btnEli]').click(function(){
										$(this).closest('.item').remove();
									});
									$row.data('data',mod.reqs[i]);
									p.$w.find("[name=gridReqs] tbody").append($row);
								}
							}
						}
						K.unblock({$element: $('#pageWrapperMain')});
					},'json');
				},'json');
			}
		});
	},
	windowDetails: function(p){
		if(p.goBack!=null) K.history.push({f: p.goBack});
		$.extend(p,{
			buttons: {}
		});
		if(K.session.tasks["td.tupa.edit"]){
			$.extend(p.buttons,{
				'Editar': {
					type: 'info',
					icon: 'fa-pencil',
					f: function(){
						tdTupa.windowEdit({id: p.id,nomb: p.nomb,data: p.data});
						K.closeWindow('winDetailPro'+p.id);
					}
				},
				'Habilitar': {
					type: 'success',
					icon: 'fa-check',
					f: function(){
						ciHelper.confirm('&#191;Desea <b>Habilitar</b> el Procedimiento <b>'+K.tmp.find('td:eq(2)').html()+'</b>&#63;',
						function(){
							K.sendingInfo();
							$.post('td/tupa/estado',{_id: p.id,estado: 'H'},function(){
								K.clearNoti();
								K.closeWindow('winDetailPro'+p.id);
								K.notification({title: 'Procedimiento Habilitado',text: 'La habilitaci&oacute;n se realiz&oacute; con &eacute;xito!'});
								K.goBack();
							});
						},function(){
							$.noop();
						},'Habilitaci&oacute;n de Procedimiento');
					}
				},
				'Deshabilitar': {
					type: 'danger',
					icon: 'fa-ban',
					f: function(){
						ciHelper.confirm('&#191;Desea <b>Deshabilitar</b> el Procedimiento <b>'+K.tmp.find('td:eq(2)').html()+'</b>&#63;',
						function(){
							K.sendingInfo();
							$.post('td/tupa/estado',{_id: p.id,estado: 'D'},function(){
								K.clearNoti();
							K.closeWindow('winDetailPro'+p.id);
								K.notification({title: 'Procedimiento Deshabilitado',text: 'La deshabilitaci&oacute;n se realiz&oacute; con &eacute;xito!'});
								K.goBack();
							});
						},function(){
							$.noop();
						},'Deshabilitaci&oacute;n de Procedimiento');
					}
				}
			});
		}
		$.extend(p.buttons,{
			'Regresar': {
				type: 'danger',
				icon: 'fa-ban',
				f: function(){
					K.goBack();
				}
			}
		});
		new K.Panel({
			contentURL: 'td/tupa/details',
			store: false,
			buttons: p.buttons,
			onContentLoaded: function(){
				p.$w = $('#mainPanel');
				new K.grid({
					$el: p.$w.find('[name=gridBleg]'),
					search: false,
					pagination: false,
					cols: ['Descripci&oacute;n','URL'],
					onlyHtml: true
				});
				new K.grid({
					$el: p.$w.find('[name=gridReqs]'),
					search: false,
					pagination: false,
					cols: ['Item','Descripci&oacute;n','Costo'],
					onlyHtml: true
				});
				K.block({$element: $('#pageWrapperMain')});
				$.post('td/tupa/get','id='+p.id,function(data){
					p.estado = data.estado;
					if(data.estado=='H')
						p.$w.find('#div_buttons button:eq(1)').remove();
					else
						p.$w.find('#div_buttons button:eq(2)').remove();
					p.$w.find('[name=item]').html(data.item);
					p.$w.find('[name=organizacion]').html(data.organizacion.nomb);
					p.$w.find('[name=titulo]').html(data.titulo);
					p.$w.find('[name=notas]').html(data.notas);
					p.$w.find('[name=descr]').html(data.descr);
					p.$w.find('[name=notas]').html(data.notas);
					if(data.modalidades.length>1){
						p.$w.find('[name=modalidades]').selectVal('1');
						p.$w.find('[name=modalidades]')
							.attr('disabled','disabled');
						for(var i=0,j=data.modalidades.length; i<j; i++){
							var $mod = p.$w.find('[name=div_mods] #mod1').clone();
							$mod.attr('id','mods'+(i+1));
							var $li = p.$w.find('[name=div_mods] li:eq(0)').clone();
							$li.find('a').attr('href','#mods'+(i+1))
								.attr('aria-controls','mods'+(i+1))
								.html('Modalidad '+(i+1));
							if(i!=0){
								$li.removeClass('active');
								$mod.removeClass('active');
							}
							var mod = data.modalidades[i];
							$mod.find('[name=modalidades]').selectVal('0');
							$mod.find('[name=modalidades]').attr('disabled','disabled');
							$mod.find('#mod1 .row:eq(0)').remove();
							$mod.find('[name=item_pro]').html(mod.item);
							$mod.find('[name=plazo_pro]').html(mod.plazo);
							$mod.find('[name=desc_pro]').html(mod.descr);
							$mod.find('[name=calificacion]').selectVal(mod.calif);
							$mod.find('[name=calificacion]').attr('disabled','disabled');
							$mod.find('[name=inicio_tramite]').html(mod.inicia.organismo.nomb);
							$mod.find('[name=aprueba_tramite]').html(mod.aprueba.organismo.nomb);
							$mod.find('[name=plazo_apr]').html(mod.aprueba.plazo);
							if(mod.reconsidera!=null){
								$mod.find('[name=reclamacion_tramite]').html(mod.reconsidera.organismo.nomb);
								$mod.find('[name=plazo_rec_pre]').html(mod.reconsidera.plazos.presentacion);
								$mod.find('[name=plazo_rec_res]').html(mod.reconsidera.plazos.resolucion);
							}else{
								$mod.find('#tabs-2 fieldset:eq(0) div').hide();
								$mod.find('#tabs-2 fieldset:eq(0)').append('<span name="spanRecon">El procedimiento no tiene un proceso de Reconsideraci&oacute;n</span>');
							}
							if(mod.apela!=null){
								$mod.find('[name=apelacion_tramite]').html(mod.apela.organismo.nomb);
								$mod.find('[name=plazo_ape_pre]').html(mod.apela.plazos.presentacion);
								$mod.find('[name=plazo_ape_res]').html(mod.apela.plazos.resolucion);
							}else{
								$mod.find('#tabs-2 fieldset:eq(1) div').hide();
								$mod.find('#tabs-2 fieldset:eq(1)').append('<span name="spanApela">El procedimiento no tiene un proceso de Apelaci&oacute;n</span>');
							}
							$mod.find('[name=url_pro]').val(mod.url_doc);
							$mod.find('[name=notas_pro]').val(mod.notas);
							if(mod.blegs!=null){
								for(var id = 0; id<mod.blegs.length; id++){
									var $row = $('<tr class="item">');
									$row.append('<td>'+mod.blegs[id].descr+'</td>');
									$row.append('<td>'+mod.blegs[id].url+'</td>');
									$mod.find("[name=gridBleg] tbody").append($row);
								}
							}
							if(mod.reqs!=null){
								for(var id = 0; id<mod.reqs.length; id++){
									var $row = $('<tr class="item">');
									$row.append('<td>'+mod.reqs[id].item+'</td>');
									$row.append('<td>'+mod.reqs[id].descr+'</td>');
									$row.append('<td>');
									if(mod.reqs[id].soles!=null) $row.find('td:last').append('S/.'+mod.reqs[id].soles+'<br />');
									if(mod.reqs[id].uit!=null) $row.find('td:last').append(mod.reqs[id].uit+'% UIT<br />');
									if(mod.reqs[id].gratuito!=null) $row.find('td:last').append('Gratuito');
									$mod.find("[name=gridReqs] tbody").append($row);
								}
							}
							$mod.find('[href=#tabs-1]').attr('aria-controls','tabsm'+i+'-1')
								.attr('href','#tabsm'+i+'-1');
							$mod.find('[id=tabs-1]').attr('id','tabsm'+i+'-1');
							$mod.find('[href=#tabs-2]').attr('aria-controls','tabsm'+i+'-2')
								.attr('href','#tabsm'+i+'-2');
							$mod.find('[id=tabs-2]').attr('id','tabsm'+i+'-2');
							$mod.find('[href=#tabs-3]').attr('aria-controls','tabsm'+i+'-3')
								.attr('href','#tabsm'+i+'-3');
							$mod.find('[id=tabs-3]').attr('id','tabsm'+i+'-3');
							$mod.find('[href=#tabs-4]').attr('aria-controls','tabsm'+i+'-4')
								.attr('href','#tabsm'+i+'-4');
							$mod.find('[id=tabs-4]').attr('id','tabsm'+i+'-4');
							p.$w.find('[name=div_mods] .nav-tabs:eq(0)').append($li);
							p.$w.find('[name=div_mods] .tab-content:eq(0)').append($mod);
						}
						p.$w.find('[name=div_mods] #mod1').remove();
						p.$w.find('[name=div_mods] li:eq(0)').remove();
					}else{
						var mod = data.modalidades[0];
						p.$w.find('[name=modalidades]').selectVal('0');
						p.$w.find('[name=modalidades]').attr('disabled','disabled');
						p.$w.find('#mod1 .row:eq(0)').remove();
						p.$w.find('[name=item_pro]').html(mod.item);
						p.$w.find('[name=plazo_pro]').html(mod.plazo);
						p.$w.find('[name=desc_pro]').html(mod.descr);
						p.$w.find('[name=calificacion]').selectVal(mod.calif);
						p.$w.find('[name=calificacion]').attr('disabled','disabled');
						p.$w.find('[name=inicio_tramite]').html(mod.inicia.organismo.nomb);
						p.$w.find('[name=aprueba_tramite]').html(mod.aprueba.organismo.nomb);
						p.$w.find('[name=plazo_apr]').html(mod.aprueba.plazo);
						if(data.reconsidera!=null){
							p.$w.find('[name=reclamacion_tramite]').html(mod.reconsidera.organismo.nomb);
							p.$w.find('[name=plazo_rec_pre]').html(mod.reconsidera.plazos.presentacion);
							p.$w.find('[name=plazo_rec_res]').html(mod.reconsidera.plazos.resolucion);
						}else{
							p.$w.find('#tabs-2 fieldset:eq(0) div').hide();
							p.$w.find('#tabs-2 fieldset:eq(0)').append('<span name="spanRecon">El procedimiento no tiene un proceso de Reconsideraci&oacute;n</span>');
						}
						if(data.apela!=null){
							p.$w.find('[name=apelacion_tramite]').html(data.apela.organismo.nomb);
							p.$w.find('[name=plazo_ape_pre]').html(data.apela.plazos.presentacion);
							p.$w.find('[name=plazo_ape_res]').html(data.apela.plazos.resolucion);
						}else{
							p.$w.find('#tabs-2 fieldset:eq(1) div').hide();
							p.$w.find('#tabs-2 fieldset:eq(1)').append('<span name="spanApela">El procedimiento no tiene un proceso de Apelaci&oacute;n</span>');
						}
						p.$w.find('[name=url_pro]').val(data.url_doc);
						p.$w.find('[name=notas_pro]').val(data.notas);
						if(mod.blegs!=null){
							for(var i = 0; i<mod.blegs.length; i++){
								var $row = $('<tr class="item">');
								$row.append('<td>'+mod.blegs[i].descr+'</td>');
								$row.append('<td>'+mod.blegs[i].url+'</td>');
								p.$w.find("[name=gridBleg] tbody").append($row);
							}
						}
						if(mod.reqs!=null){
							for(var i = 0; i<mod.reqs.length; i++){
								var $row = $('<tr class="item">');
								$row.append('<td>'+mod.reqs[i].item+'</td>');
								$row.append('<td>'+mod.reqs[i].descr+'</td>');
								$row.append('<td>');
								if(mod.reqs[i].soles!=null) $row.find('td:last').append('S/.'+mod.reqs[i].soles+'<br />');
								if(mod.reqs[i].uit!=null) $row.find('td:last').append(mod.reqs[i].uit+'% UIT<br />');
								if(mod.reqs[i].gratuito!=null) $row.find('td:last').append('Gratuito');
								p.$w.find("[name=gridReqs] tbody").append($row);
							}
						}
					}
					K.unblock({$element: $('#pageWrapperMain')});
				},'json');
			}
		});
	},
	windowEli: function(p){
		new K.Modal({
			id: 'windowDelete',
			title: 'Eliminar procedimiento '+p.nomb,
			content: '&iquest;Desea <b>eliminar</b> el procedimiento <strong>'+p.nomb+'</strong>&#63;',
			icon: 'ui-icon-info',
			type: 'modal',
			width: 350,
			height: 40,
			padding: { top: 15, right: 10, bottom: 0, left: 20 },
			buttons: {
				"Eliminar": function() {
					K.sendingInfo();
					$('#windowDelete').dialog('widget').find('.ui-dialog-buttonpane button').button('disable');
					$.post('td/tupa/delete','id='+p.id,function(){
						K.clearNoti();
						K.notification({title: ciHelper.titleMessages.regiEli,text: 'Procedimiento eliminado!'});
						K.closeWindow('windowDelete');
						if($.cookie('mode')) tdTupa.init();
					});
				},
				"Cancelar": function() { K.closeWindow('windowDelete'); }
			}
		});
	},
	windowNewTupa: function(p){
		p = new Object;
		K.Modal({
			id: 'windowNewTupa',
			title: 'Crear Nuevo TUPA',
			contentURL: 'td/tupa/new',
			icon: 'ui-icon-note',
			width: 340,
			height: 60,
			buttons: {
				"Crear TUPA": function(){
					K.clearNoti();
					var data = new Object;
					data.anio = p.$w.find('[name=ano]').val();
					data.dl = p.$w.find('[name=dl]').val();
					data.estado = 'V';
					if(data.anio==''){
						p.$w.find('[name=ano]').focus();
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar el a&ntilde;o del TUPA',type: 'error'});
					}
					if(data.dl==''){
						p.$w.find('[name=dl]').focus();
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar el Decreto Legislativo del TUPA',type: 'error'});
					}
					K.clearNoti();
					K.sendingInfo();
					p.$w.dialog('widget').find('.ui-dialog-buttonpane button').button('disable');
					$.post('td/tupa/create',data,function(){
						K.clearNoti();
						K.notification({title: ciHelper.titleMessages.regiGua,text: 'Un nuevo TUPA ha sido creado y ahora es el vigente!'});
						K.closeWindow(p.$w.attr('id'));
					});
				},
				"Cancelar": function(){
					K.closeWindow(p.$w.attr('id'));
				}
			},
			onContentLoaded: function(){
				p.$w = $('#windowNewTupa');
				var date = new Date();
				p.$w.find('[name=ano]').val(date.getFullYear()).numeric().spinner({step: 1,min: 1990,max: 2100});
				p.$w.find('.ui-button').css('height','14px');
			}
		});
	},
	windowSelect: function(p){
		new K.Modal({
			id: 'windowSelect',
			content: '<div name="tmp"></div>',
			allScreen: true,
			title: 'Seleccionar Procedimiento de TUPA',
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
							return K.msg({
								title: ciHelper.titles.infoReq,
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
				K.block();
				p.$grid = new K.grid({
					$el: p.$w.find('[name=tmp]'),
					cols: ['','Nombre','Modalidades','Plazo','Costo'],
					search: false,
					pagination: false,
					itemdescr: 'procedimiento(s)',
					onlyHtml: true,
					toolbarHTML: '',
					onContentLoaded: function(){
						$.post("td/tupa/vigente", {page: 1}, function(data){
							if ( data.items != null ) {
								var orga = p.$w.find('[name=orga]').data('orga');
								for (var i=0; i<data.items.length; i++) {
									for (j=0; j < data.items[i].length; j++) {
										result = data.items[i][j];
										if(result.estado!='D'){
											for(var k=0; k<result.modalidades.length; k++){
												var $row = $('<tr class="item">');
												$row.append('<td>');
												$row.append('<td>'+result.titulo+'</td>');
												$row.append('<td>'+result.modalidades[k].descr+'</td>');
												$row.append('<td>'+result.modalidades[k].aprueba.plazo+'</td>');
												if(result.modalidades[k].reqs!=null){
													if(result.modalidades[k].reqs[0].soles!=null)
														$row.append('<td>'+result.modalidades[k].reqs[0].soles+'</td>');
													else if(result.modalidades[k].reqs[0].uit!=null)
														$row.append('<td>'+result.modalidades[k].reqs[0].uit+'</td>');
													else
														$row.append('<td>--</td>');
												}else
													$row.append('<td>--</td>');
												$row.data('data',{
													data: result,
													index: k,
													anio: data.anio,
													_id: data._id
												}).data('index',k).dblclick(function(){
													p.$w.find('.modal-footer button:first').click();
												}).data('_id',data._id).data('anio',data.anio).contextMenu('conMenListSel', {
													bindings: {
														'conMenListSel_sel': function(t) {
															p.$w.find('.modal-footer button:first').click();
														}
													}
												});
												p.$w.find('[name=tmp] tbody').append($row);
											}
										}
									}
								}
							}
							p.$w.find('table').filterTable();
							K.unblock();
						},'json');
					}
				});
			}
		});
	}
};
define(
	['ci/details'],
	function(ciDetails){
		return tdTupa;
	}
);