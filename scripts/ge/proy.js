geProy = {
	states: {
		H: {
			descr: "Activo",
			color: "green",
			label: '<span class="label label-primary">Activo</span>'
		},
		D:{
			descr: "Deshabilitado",
			color: "#CCCCCC",
			label: '<span class="label label-default">Deshabilitado</span>'
		},
		C:{
			descr: "Completado",
			color: "#CCCCCC",
			label: '<span class="label label-success">Completado</span>'
		}
	},
	prioridad: [
		{
			cod: 'N',
			label: 'Normal',
			span: '<span class="label label-primary">Normal</span>'
		},
		{
			cod: 'U',
			label: 'Urgente',
			span: '<span class="label label-danger">Urgente</span>'
		},
		{
			cod: 'A',
			label: 'Adicional',
			span: '<span class="label label-info">Adicional</span>'
		}
	],
	get_prioridad: function(item){
		for(var i=0; i<geProy.prioridad.length; i++){
			if(item==geProy.prioridad[i].cod)
				return geProy.prioridad[i];
		}
	},
	log: {
		CRE: {
			icon: 'fa-plus',
			text: function(log){
				return 'Se a&ntilde;adi&oacute; una nueva Tarea: "<i>'+log.tarea.tarea+'</i>"';
			},
			descr: function(log){
				prioridad = geProy.get_prioridad(log.tarea.prioridad);
				return 'Descripci&oacute;n: "'+log.tarea.descr+'"<br />'+
					'Prioridad: '+prioridad.label;
			},
			color: 'blue-bg'
		},
		UPD: {
			icon: 'fa-refresh',
			text: function(log){
				return 'Se modific&oacute; una Tarea: "<i>'+log.tarea.tarea+'</i>"';
			},
			descr: function(log){
				prioridad = geProy.get_prioridad(log.tarea.prioridad);
				return 'Descripci&oacute;n: "'+log.tarea.descr+'"<br />'+
					'Prioridad: '+prioridad.label;
			},
			color: 'lazur-bg'
		},
		COM: {
			icon: 'fa-check',
			text: function(log){
				return 'Se complet&oacute; la Tarea: "<i>'+log.tarea.tarea+'</i>" el '+ciHelper.date.format.bd_ymdhi(log.tarea.fec);
			},
			descr: function(log){
				return 'Usuario: "'+log.tarea.userid+'"';
			},
			color: 'navy-bg'
		},
		DES: {
			icon: 'fa-check',
			text: function(log){
				return 'Se devolvi&oacute; la Tarea: "<i>'+log.tarea.tarea+'</i>" a su estado <b>Pendiente</b>';
			},
			descr: function(log){
				return 'Usuario: "'+log.tarea.userid+'"';
			},
			color: 'yellow-bg'
		}
	},
	dbRel: function(item){
		return {
			_id: item._id.$id,
			nomb: item.nomb
		};
	},
	init: function(){
		K.initMode({
			mode: 'ge',
			action: 'ge',
			titleBar: {
				title: 'Proyectos'
			}
		});
		$('#side-menu').find("li").not('.active').children("ul.in").collapse("hide");
		
		new K.Panel({
			onContentLoaded: function(){
		   		var $grid = new K.grid({
					cols: ['','',{n:'Nombre',f:'nomb'},'Abrev.',{n:'&Uacute;ltima Modificaci&oacute;n',f:'fecmod'}],
					data: 'ge/proy/lista',
					params: {},
					itemdescr: 'proyecto(s)',
					toolbarHTML: '<button name="btnAgregar" class="btn btn-success"><i class="fa fa-tasks"></i> Agregar Nuevo Proyecto</button>',
					onContentLoaded: function($el){
						$el.find('[name=btnAgregar]').click(function(){
							geProy.windowNew();
						});
					},
					onLoading: function(){ K.block(); },
					onComplete: function(){ K.unblock(); },
					fill: function(data,$row){
						data.porcentaje = K.round(data.porcentaje,2);
						$row.append('<td>');
						$row.append('<td>'+geProy.states[data.estado].label+'</td>');
						$row.append('<td>'+data.nomb+'</td>');
						$row.append('<td><small>Completado un: '+data.porcentaje+'%</small>'+
							'<div class="progress progress-mini">'+
								'<div style="width: '+data.porcentaje+'%;" class="progress-bar"></div>'+
							'</div></td>');
						$row.append('<td><kbd>'+ciHelper.date.format.bd_ymdhi(data.fecmod)+'</kbd><br />'+mgEnti.formatName(data.trabajador)+'</td>');
						$row.data('id',data._id.$id).data('data',data).dblclick(function(){
							//geProy.windowDetails({_id: $(this).data('id'),nomb: $(this).find('td:eq(2)').html()});
							geProy.windowGantt({id: K.tmp.data('id'),nomb: K.tmp.find('td:eq(2)').html()});
						}).data('estado',data.estado).contextMenu("conMenGeProy", {
							onShowMenu: function($row, menu) {
								$('#conMenGeProy_ver,#conMenGeProy_hab,#conMenGeProy_des',menu).remove();
								if($row.data('estado')=='H') $('#conMenGeProy_hab',menu).remove();
								else $('#conMenGeProy_edi,#conMenGeProy_des',menu).remove();
								return menu;
							},
							bindings: {
								'conMenGeProy_ver': function(t) {
									geProy.windowDetails({id: K.tmp.data('id'),nomb: K.tmp.find('td:eq(2)').html()});
								},
								'conMenGeProy_edi': function(t) {
									geProy.windowEdit({id: K.tmp.data('id'),nomb: K.tmp.find('td:eq(2)').html()});
								},
								'conMenGeProy_gnt': function(t) {
									geProy.windowGantt({id: K.tmp.data('id'),nomb: K.tmp.find('td:eq(2)').html()});
								},
								'conMenGeProy_hab': function(t) {
									ciHelper.confirm('&#191;Desea <b>Habilitar</b> el Tipo de Local <b>'+K.tmp.find('td:eq(2)').html()+'</b>&#63;',
									function(){
										K.sendingInfo();
										$.post('ge/proy/save',{_id: K.tmp.data('id'),estado: 'H'},function(){
											K.clearNoti();
											K.msg({title: 'Tipo de Local Habilitado',text: 'La habilitaci&oacute;n se realiz&oacute; con &eacute;xito!'});
											geProy.init();
										});
									},function(){
										$.noop();
									},'Habilitaci&oacute;n de Tipo de Local');
								},
								'conMenGeProy_des': function(t) {
									ciHelper.confirm('&#191;Desea <b>Deshabilitar</b> el Tipo de Local <b>'+K.tmp.find('td:eq(2)').html()+'</b>&#63;',
									function(){
										K.sendingInfo();
										$.post('ge/proy/save',{_id: K.tmp.data('id'),estado: 'D'},function(){
											K.clearNoti();
											K.msg({title: 'Tipo de Local Deshabilitado',text: 'La deshabilitaci&oacute;n se realiz&oacute; con &eacute;xito!'});
											geProy.init();
										});
									},function(){
										$.noop();
									},'Deshabilitaci&oacute;n de Tipo de Local');
								},
								'conMenGeProy_ava': function(t) {
									var permiso = false,
									data = K.tmp.data('data');
									for(var i=0; i<data.miembros.length; i++){
										if(K.session.user._id.$id==data.miembros[i]._id.$id){
											permiso = true;
											break;
										}
									}
									if(permiso==false)
										K.msg({title: 'Permisos requeridos',text: 'Usted no esta autorizado para modificar este Proyecto!',type: 'error'});
									else
										geProy.windowAvance({id: K.tmp.data('id'),nomb: K.tmp.find('td:eq(2)').html()});
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
			title: 'Nuevo Tipo',
			contentURL: 'ge/proy/edit',
			buttons: {
				"Guardar": {
					icon: 'fa-save',
					type: 'success',
					f: function(){
						K.clearNoti();
						var data = {
							nomb: p.$w.find('[name=nomb]').val(),
							descr: p.$w.find('[name=descr]').val(),
							miembros: []
						};
						if(data.nomb==''){
							p.$w.find('[name=nomb]').focus();
							return K.msg({title: ciHelper.titles.infoReq,text: 'Debe ingresar la denominaci&oacute;n de Proyecto!',type: 'error'});
						}
						if(data.descr==''){
							p.$w.find('[name=descr]').focus();
							return K.msg({title: ciHelper.titles.infoReq,text: 'Debe ingresar la descripcion de Proyecto!',type: 'error'});
						}
						for(var i=0; i<p.$w.find('[name=grid] tbody .item').length; i++){
							var cuenta = p.$w.find('[name=grid] tbody .item').eq(i).data('data');
							data.miembros.push({
								_id: cuenta._id.$id,
								userid: cuenta.userid,
								owner: mgEnti.dbRel(cuenta.owner)
							});
						}
						K.sendingInfo();
						p.$w.find('#div_buttons button').attr('disabled','disabled');
						$.post("ge/proy/save",data,function(result){
							K.clearNoti();
							K.msg({title: ciHelper.titles.regiGua,text: "Proyecto agregado!"});
							geProy.init();
						},'json');
					}
				},
				"Cancelar": {
					icon: 'fa-ban',
					type: 'danger',
					f: function(){
						geProy.init();
					}
				}
			},
			onContentLoaded: function(){
				p.$w = $('#mainPanel');
				K.block();
				new K.grid({
					$el: p.$w.find('[name=grid]'),
					search: false,
					pagination: false,
					cols: ['Usuario',''],
					onlyHtml: true,
					toolbarHTML: '<input type="text" placeholder="buscar usuario..." name="userid" class="form-control">'
				});
				$.post('ac/user/all',function(data){
					p.user = $.each(data.items,function(i,item){
						item.id = item._id.$id;
						item.busqueda = mgEnti.formatName(item.owner);
					});
					p.$w.find('[name=userid]').typeaheadmap({ 
						source: p.user,
						key: "busqueda",
						value: "id",
						displayer: function(that, item, value) {
							//return item.cod+' '+value;
							return value;
						},
						listener : function(k, v) {
							var cuenta;
							$.each(p.user,function(i,item){
								if(item.id==v){
									cuenta = item;
								}
							});
							if(p.$w.find('[name='+cuenta._id.$id+']').length==0){
								var $row = $('<tr class="item" name="'+cuenta._id.$id+'">');
								$row.append('<td><kbd>'+cuenta.userid+'</kbd> - '+mgEnti.formatName(cuenta.owner)+'</td>');
								$row.append('<td><button name="btnEli" class="btn btn-danger btn-sm"><i class="fa fa-trash-o"></i></button></td>');
								$row.find('[name=btnEli]').click(function(){
									$(this).closest('.item').remove();
								});
								$row.data('data',cuenta);
								p.$w.find('[name=grid] tbody').append($row);
								p.$w.find('[name=userid]').val('');
							}else{
								K.msg({text: 'El usuario ya ha sido agregado!',type: 'error'});
							}
						}
					});
					var $row = $('<tr class="item" name="'+K.session.user._id.$id+'">');
					$row.append('<td><kbd>'+K.session.user.userid+'</kbd> - '+mgEnti.formatName(K.session.user.owner)+'</td>');
					$row.append('<td>--</td>');
					$row.data('data',K.session.user);
					p.$w.find('[name=grid] tbody').append($row);
					K.unblock();
				},'json');
			}
		});
	},
	windowEdit: function(p){
		new K.Panel({ 
			title: 'Editar Proyecto '+p.nomb,
			contentURL: 'ge/proy/edit',
			buttons: {
				"Guardar": {
					icon: 'fa-save',
					type: 'success',
					f: function(){
						K.clearNoti();
						var data = {
							_id: p.id,
							nomb: p.$w.find('[name=nomb]').val(),
							descr: p.$w.find('[name=descr]').val(),
							razon: p.$w.find('[name=razon]').val(),
							miembros: []
						};
						if(data.nomb==''){
							p.$w.find('[name=nomb]').focus();
							return K.msg({title: ciHelper.titles.infoReq,text: 'Debe ingresar la denominaci&oacute;n de Proyecto!',type: 'error'});
						}
						if(data.descr==''){
							p.$w.find('[name=descr]').focus();
							return K.msg({title: ciHelper.titles.infoReq,text: 'Debe ingresar la descripcion de Proyecto!',type: 'error'});
						}
						if(data.razon==''){
							p.$w.find('[name=razon]').focus();
							return K.msg({title: ciHelper.titles.infoReq,text: 'Debe ingresar la raz&oacute;n por la que actualizo el Proyecto!',type: 'error'});
						}
						for(var i=0; i<p.$w.find('[name=grid] tbody .item').length; i++){
							var cuenta = p.$w.find('[name=grid] tbody .item').eq(i).data('data');
							data.miembros.push({
								_id: cuenta._id.$id,
								userid: cuenta.userid,
								owner: mgEnti.dbRel(cuenta.owner)
							});
						}
						K.sendingInfo();
						p.$w.find('#div_buttons button').attr('disabled','disabled');
						$.post("ge/proy/save",data,function(result){
							K.clearNoti();
							K.msg({title: ciHelper.titles.regiAct,text: "Proyecto actualizado!"});
							geProy.init();
						},'json');
					}
				},
				"Cancelar": {
					icon: 'fa-ban',
					type: 'danger',
					f: function(){
						geProy.init();
					}
				}
			},
			onContentLoaded: function(){
				p.$w = $('#mainPanel');
				p.$w.find('[name=general]').append('<div class="form-group"><label class="col-sm-4 control-label">Raz&oacute;n de actualizaci&oacute;n</label><div class="col-sm-8"><textarea cols="30" rows="2" class="form-control" name="razon"></textarea></div></div>');
				K.block();
				new K.grid({
					$el: p.$w.find('[name=grid]'),
					search: false,
					pagination: false,
					cols: ['Usuario',''],
					onlyHtml: true,
					toolbarHTML: '<input type="text" placeholder="buscar usuario..." name="userid" class="form-control">'
				});
				$.post('ac/user/all',function(data){
					p.user = $.each(data.items,function(i,item){
						item.id = item._id.$id;
						item.busqueda = mgEnti.formatName(item.owner);
					});
					p.$w.find('[name=userid]').typeaheadmap({ 
						source: p.user,
						key: "busqueda",
						value: "id",
						displayer: function(that, item, value) {
							//return item.cod+' '+value;
							return value;
						},
						listener : function(k, v) {
							var cuenta;
							$.each(p.user,function(i,item){
								if(item.id==v){
									cuenta = item;
								}
							});
							if(p.$w.find('[name='+cuenta._id.$id+']').length==0){
								var $row = $('<tr class="item" name="'+cuenta._id.$id+'">');
								$row.append('<td><kbd>'+cuenta.userid+'</kbd> - '+mgEnti.formatName(cuenta.owner)+'</td>');
								$row.append('<td><button name="btnEli" class="btn btn-danger btn-sm"><i class="fa fa-trash-o"></i></button></td>');
								$row.find('[name=btnEli]').click(function(){
									$(this).closest('.item').remove();
								});
								$row.data('data',cuenta);
								p.$w.find('[name=grid] tbody').append($row);
								p.$w.find('[name=userid]').val('');
							}else{
								K.msg({text: 'El usuario ya ha sido agregado!',type: 'error'});
							}
						}
					});
					$.post('ge/proy/get',{_id: p.id},function(data){
						p.$w.find('[name=nomb]').val(data.nomb);
						p.$w.find('[name=descr]').val(data.descr);
						for(var i=0; i<data.miembros.length; i++){
							var cuenta = data.miembros[i],
							$row = $('<tr class="item" name="'+cuenta._id.$id+'">');
							$row.append('<td><kbd>'+cuenta.userid+'</kbd> - '+mgEnti.formatName(cuenta.owner)+'</td>');
							$row.append('<td><button name="btnEli" class="btn btn-danger btn-sm"><i class="fa fa-trash-o"></i></button></td>');
							$row.find('[name=btnEli]').click(function(){
								$(this).closest('.item').remove();
							});
							$row.data('data',cuenta);
							p.$w.find('[name=grid] tbody').append($row);
							p.$w.find('[name=userid]').val('');
						}
						K.unblock();
					},'json');
				},'json');
			}
		});
	},
	windowGantt: function(p){
		new K.Panel({ 
			title: 'Diagrama de Tiempos (Gantt): '+p.nomb,
			contentURL: 'ge/proy/gantt',
			buttons: {
				"Regresar": {
					icon: 'fa-ban',
					type: 'danger',
					f: function(){
						geProy.init();
					}
				}
			},
			onContentLoaded: function(){
				p.$w = $('#mainPanel');
				K.block();
				$.post('ge/proy/fomato_jsgantt',{_id: p.id},function(data){
					p.$w.find('[name=nomb]').val(data.nomb);
					p.$w.find('[name=descr]').val(data.descr);
					var g = new JSGantt.GanttChart(document.getElementById('GanttChartDIV'), 'week');
					if (g.getDivId() != null) {
				  		g.setCaptionType('Complete');  // Set to Show Caption (None,Caption,Resource,Duration,Complete)
				  		g.setQuarterColWidth(36);
				  		g.setDateTaskDisplayFormat('day dd month yyyy'); // Shown in tool tip box
				  		g.setDayMajorDateDisplayFormat('mon yyyy - Week ww') // Set format to display dates in the "Major" header of the "Day" view
				  		g.setWeekMinorDateDisplayFormat('dd mon') // Set format to display dates in the "Minor" header of the "Week" view
				  		g.setShowTaskInfoLink(1); // Show link in tool tip (0/1)
				  		g.setShowEndWeekDate(0); // Show/Hide the date for the last day of the week in header for daily view (1/0)
				  		g.setUseSingleCell(10000); // Set the threshold at which we will only use one cell per table row (0 disables).  Helps with rendering performance for large charts.
				  		g.setFormatArr('Day', 'Week', 'Month', 'Quarter'); // Even with setUseSingleCell using Hour format on such a large chart can cause issues in some browsers
				  		g.addLang('es', {
				  			'format':'Formato', 
				  			'comp':'Completo'
				  		});
						for(var i=0; i<data.jsgantt.length; i++){
							var task_item = data.jsgantt[i];
							// Parametros                     (pID, 	  	  pName,    	   pStart,           pEnd,           pClass,           pLink (unused)  	pMile, 		 	 pRes,       	 pComp, 	  	  pGroup, 	    	pParent, 	       pOpen, 			pDepend, 	   	   pCaption, 	       pNotes, 		 	 pGantt)
  							g.AddTaskItem(new JSGantt.TaskItem(task_item.pid, task_item.pname, task_item.pstart, task_item.pend, task_item.pclass, task_item.plink, task_item.pmile, task_item.pres, task_item.pcomp, task_item.pgroup, task_item.pparent, task_item.popen, task_item.pdepend, task_item.pcaption, task_item.pnotes, g ));
						}

							/*
								// Parametros                     (pID, pName,                  pStart,       pEnd,        pStyle,         pLink (unused)  pMile, pRes,       pComp, pGroup, pParent, pOpen, pDepend, pCaption, pNotes, pGantt)
								g.AddTaskItem(new JSGantt.TaskItem(1,   'Define Chart API',     '',           '',          'ggroupblack',  '',                 0, 'Brian',    0,     1,      0,       1,     '',      '',      'Some Notes text', g ));
								g.AddTaskItem(new JSGantt.TaskItem(11,  'Chart Object',         '2017-02-20','2017-02-20', 'gmilestone',   '',                 1, 'Shlomy',   100,   0,      1,       1,     '',      '',      '',      g));
								g.AddTaskItem(new JSGantt.TaskItem(12,  'Task Objects',         '',           '',          'ggroupblack',  '',                 0, 'Shlomy',   40,    1,      1,       1,     '',      '',      '',      g));
								g.AddTaskItem(new JSGantt.TaskItem(121, 'Constructor Proc',     '2017-02-21','2017-03-09', 'gtaskblue',    '',                 0, 'Brian T.', 60,    0,      12,      1,     '',      '',      '',      g));
								g.AddTaskItem(new JSGantt.TaskItem(122, 'Task Variables',       '2017-03-06','2017-03-11', 'gtaskred',     '',                 0, 'Brian',    60,    0,      12,      1,     121,     '',      '',      g));
								g.AddTaskItem(new JSGantt.TaskItem(123, 'Task by Minute/Hour',  '2017-03-09','2017-03-14 12:00', 'gtaskyellow', '',            0, 'Ilan',     60,    0,      12,      1,     '',      '',      '',      g));
								g.AddTaskItem(new JSGantt.TaskItem(124, 'Task Functions',       '2017-03-09','2017-03-29', 'gtaskred',     '',                 0, 'Anyone',   60,    0,      12,      1,     '123SS', 'This is a caption', null, g));
								g.AddTaskItem(new JSGantt.TaskItem(2,   'Create HTML Shell',    '2017-03-24','2017-03-24', 'gtaskyellow',  '',                 0, 'Brian',    20,    0,      0,       1,     122,     '',      '',      g));
								g.AddTaskItem(new JSGantt.TaskItem(3,   'Code Javascript',      '',           '',          'ggroupblack',  '',                 0, 'Brian',    0,     1,      0,       1,     '',      '',      '',      g));
								g.AddTaskItem(new JSGantt.TaskItem(31,  'Define Variables',     '2017-02-25','2017-03-17', 'gtaskpurple',  '',                 0, 'Brian',    30,    0,      3,       1,     '',      'Caption 1','',   g));
								g.AddTaskItem(new JSGantt.TaskItem(32,  'Calculate Chart Size', '2017-03-15','2017-03-24', 'gtaskgreen',   '',                 0, 'Shlomy',   40,    0,      3,       1,     '',      '',      '',      g));
								g.AddTaskItem(new JSGantt.TaskItem(33,  'Draw Task Items',      '',           '',          'ggroupblack',  '',                 0, 'Someone',  40,    2,      3,       1,     '',      '',      '',      g));
								g.AddTaskItem(new JSGantt.TaskItem(332, 'Task Label Table',     '2017-03-06','2017-03-09', 'gtaskblue',    '',                 0, 'Brian',    60,    0,      33,      1,     '',      '',      '',      g));
								g.AddTaskItem(new JSGantt.TaskItem(333, 'Task Scrolling Grid',  '2017-03-11','2017-03-20', 'gtaskblue',    '',                 0, 'Brian',    0,     0,      33,      1,     '332',   '',      '',      g));
								g.AddTaskItem(new JSGantt.TaskItem(34,  'Draw Task Bars',       '',           '',          'ggroupblack',  '',                 0, 'Anybody',  60,    1,      3,       0,     '',      '',      '',      g));
								g.AddTaskItem(new JSGantt.TaskItem(341, 'Loop each Task',       '2017-03-26','2017-04-11', 'gtaskred',     '',                 0, 'Brian',    60,    0,      34,      1,     '',      '',      '',      g));
								g.AddTaskItem(new JSGantt.TaskItem(342, 'Calculate Start/Stop', '2017-04-12','2017-05-18', 'gtaskpink',    '',                 0, 'Brian',    60,    0,      34,      1,     '',      '',      '',      g));
								g.AddTaskItem(new JSGantt.TaskItem(343, 'Draw Task Div',        '2017-05-13','2017-05-17', 'gtaskred',     '',                 0, 'Brian',    60,    0,      34,      1,     '',      '',      '',      g));
								g.AddTaskItem(new JSGantt.TaskItem(344, 'Draw Completion Div',  '2017-05-17','2017-06-04', 'gtaskred',     '',                 0, 'Brian',    60,    0,      34,      1,     "342,343",'',     '',      g));
								g.AddTaskItem(new JSGantt.TaskItem(35,  'Make Updates',         '2017-07-17','2017-09-04', 'gtaskpurple',  '',                 0, 'Brian',    30,    0,      3,       1,     '333',   '',      '',      g));
							*/
				  		g.Draw();
					} else {
				  		K.msg({title: ciHelper.titles.infoReq,text: 'No se pudo generar el diagrama de Gantt!',type: 'error'});
					}
					/*
						for(var i=0; i<data.miembros.length; i++){
							var cuenta = data.miembros[i],
							$row = $('<tr class="item" name="'+cuenta._id.$id+'">');
							$row.append('<td><kbd>'+cuenta.userid+'</kbd> - '+mgEnti.formatName(cuenta.owner)+'</td>');
							$row.append('<td><button name="btnEli" class="btn btn-danger btn-sm"><i class="fa fa-trash-o"></i></button></td>');
							$row.find('[name=btnEli]').click(function(){
								$(this).closest('.item').remove();
							});
							$row.data('data',cuenta);
							p.$w.find('[name=grid] tbody').append($row);
							p.$w.find('[name=userid]').val('');
						}
					*/
					K.unblock();
				},'json');
			}
		});
	},
	windowAvance: function(p){
		$.extend(p,{
			cbMeta: function(data){
				var cont = '';
				if(data.descr.length>120) cont = '...';
				p.$w.find('.notes').append('<li>'+
					'<div>'+
						'<small>'+ciHelper.date.format.bd_ymdhi(data.fecmod)+'</small>'+
						'<h4>'+data.nomb+'</h4>'+
						'<p>'+data.descr.substring(0,120)+cont+'</p>'+
						'<button type="button" class="btn btn-primary btn-block" name="btnEdi"><i class="fa fa-tasks"></i> Editar Progreso</button>'+
						'<a href="#" name="btnDel"><i class="fa fa-trash-o "></i></a>'+
					'</div>'+
				'</li>');
				p.$w.find('[name=btnEdi]:last').click(function(){
					var $row = $(this).closest('li');
					geProy.editMeta({
						id: $row.data('id'),
						nomb: $row.find('h4').html(),
						proyecto: {
							id: p.id,
							nomb: p.nomb
						}
					});
				});
				p.$w.find('[name=btnDel]:last').click(function(){
					var $this = $(this).closest('li');
					ciHelper.confirm('&#191;Desea <b>Eliminar</b> el Meta <b>'+K.tmp.find('td:eq(2)').html()+'</b>&#63;',
					function(){
						K.sendingInfo();
						$.post('ge/proy/delete_meta',{_id: $this.data('id')},function(){
							K.clearNoti();
							K.msg({title: 'Meta Eliminada',text: 'La deshabilitaci&oacute;n se realiz&oacute; con &eacute;xito!'});
							$this.remove();
						});
					},function(){
						$.noop();
					},'Eliminaci&oacute;n de Meta');
				});
				p.$w.find('li:last').data('data',data).data('id',data._id.$id);
			}
		});
		new K.Panel({ 
			title: 'Editar Avance de Proyecto '+p.nomb,
			contentURL: 'ge/proy/avance',
			buttons: {
				"Regresar": {
					icon: 'fa-reply',
					type: 'warning',
					f: function(){
						geProy.init();
					}
				}
			},
			onContentLoaded: function(){
				p.$w = $('#mainPanel');
				K.block();
				p.$w.find('[name=btnMet]').click(function(){
					var data = {
						proyecto: p.id,
						nomb: p.$w.find('[name=meta]').val(),
						descr: p.$w.find('[name=meta_descr]').val()
					}
					if(data.nomb==''){
						p.$w.find('[name=meta]').focus();
						return K.msg({title: ciHelper.titles.infoReq,text: 'Debe ingresar la denominaci&oacute;n de la Meta!',type: 'error'});
					}
					if(data.descr==''){
						p.$w.find('[name=meta_descr]').focus();
						return K.msg({title: ciHelper.titles.infoReq,text: 'Debe ingresar la descripcion de la Meta!',type: 'error'});
					}
					K.block();
					$.post('ge/proy/save_meta',data,function(data){
						p.cbMeta(data);
						K.unblock();
					},'json');
				});
				$.post('ge/proy/get',{_id: p.id,all: true},function(data){
					p.$w.find('[name=nomb]').html(data.nomb);
					p.$w.find('[name=descr]').html(data.descr);
					if(data.metas!=null){
						for(var i=0; i<data.metas.length; i++){
							var result = data.metas[i];
							p.cbMeta(result);
						}
					}
					K.unblock();
				},'json');
			}
		});
	},
	editMeta: function(p){
		$.extend(p,{
			load: function(){
				K.block();
				p.$w.find('#sortable').empty();
				p.$w.find('#vertical-timeline').empty();
				p.$w.find('[name=detalle] legend').html('Haga click en una tarea para ver su detalle');
				p.$w.find('[name=detalle] code').html('');
				p.$w.find('[name=detalle] p').html('');
				$.post('ge/proy/get_meta',{_id: p.id},function(data){
					p.$w.find('[name=meta]').html(data.nomb);
					if(data.tareas!=null){
						for(var i=0; i<data.tareas.length; i++){
							p.fillTarea(data.tareas[i]);
						}
					}
					if(data.log!=null){
						data.log.reverse();
						for(var i=0; i<data.log.length; i++){
							p.fillLog(data.log[i]);
						}
					}
					K.unblock();
				},'json');
			},
			fillTarea: function(data){
				if(data.fec.sec!=null)
					data.fec = ciHelper.date.format.bd_ymd(data.fec);
				$item = $('<li class="list-warning">'+
					'<i class=" fa fa-ellipsis-v"></i>'+
					'<div class="task-checkbox">'+
						'<input type="checkbox" class="list-child i-checks" value="">'+
					'</div>'+
					'<div class="task-title">'+
						'<span class="task-title-sp"> '+data.tarea+'</span>'+
						'<span class="badge badge-sm label-danger">'+data.fec+'</span>'+
						'<div class="pull-right hidden-phone">'+
							'<button class="btn btn-info btn-sm fa fa-search"></button>'+
							'<button class="btn btn-primary btn-sm fa fa-pencil"></button>'+
							'<button class="btn btn-danger btn-sm fa fa-trash-o"></button>'+
						'</div>'+
					'</div>'+
				'</li>');
				$item.find('.btn-info').click(function(){
					var data = $(this).closest('.list-warning').data('data');
					p.$w.find('[name=detalle] legend').html(data.tarea);
					p.$w.find('[name=detalle] code').html(data.fec);
					p.$w.find('[name=detalle] p').html(data.descr);
				});
				$item.find('.btn-primary').click(function(){
					geProy.editTarea({
						id: p.id,
						data: $(this).closest('.list-warning').data('data'),
						index: $(this).closest('.list-warning').data('index'),
						callback: p.load
					});
				});
				$item.find('.btn-danger').click(function(){
					ciHelper.confirm('&#191;Desea <b>Eliminar</b> la Tarea <b>'+K.tmp.find('td:eq(2)').html()+'</b>&#63;',
					function(){
						K.sendingInfo();






						return 0;
						$.post('ge/proy/delete_tarea',{_id: K.tmp.data('id')},function(){
							K.clearNoti();
							K.msg({title: 'Tarea eliminada',text: 'La eliminaci&oacute;n se realiz&oacute; con &eacute;xito!'});
							p.load();
						});
					},function(){
						$.noop();
					},'Eliminaci&oacute;n de Tarea');
				});
				$item.find('.i-checks').iCheck({
					checkboxClass: 'icheckbox_square-green',
					radioClass: 'iradio_square-green'
				});
				if(data.estado=='C'){
					$item.find('.i-checks').iCheck('check');
					$item.addClass("task-done");
				}
				$item.data('data',data).data('index',p.$w.find('#sortable li').length);
				$item.find('.i-checks').on('ifToggled', function(event){
					var valor = $(this).val(),
					data = $(this).closest('.list-warning').data('data'),
					index = $(this).closest('.list-warning').data('index');
					if($(this).is(':checked')){
						$(this).parents('li').addClass("task-done");
						ciHelper.confirm('&#191;Desea <b>Culminar</b> la Tarea <b>'+data.tarea+'</b>&#63;',
						function(){
							K.sendingInfo();
							$.post('ge/proy/update_tarea',{_id: p.id,index: index,tarea: data.tarea,estado: 'C'},function(){
								K.clearNoti();
								K.msg({title: 'Tarea actualizada',text: 'La actualizaci&oacute;n se realiz&oacute; con &eacute;xito!'});
								p.load();
							});
						},function(){
							p.load();
						},'Culminaci&oacute;n de Tarea');
					}else{
						$(this).parents('li').removeClass("task-done");
						ciHelper.confirm('&#191;Desea <b>Deshacer</b> la Tarea <b>'+data.tarea+'</b>&#63;',
						function(){
							K.sendingInfo();
							$.post('ge/proy/update_tarea',{_id: p.id,index: index,tarea: data.tarea,estado: 'P'},function(){
								K.clearNoti();
								K.msg({title: 'Tarea actualizada',text: 'La actualizaci&oacute;n se realiz&oacute; con &eacute;xito!'});
								p.load();
							});
						},function(){
							p.load();
						},'Deshacer Tarea');
					}
				});
				p.$w.find('#sortable').append($item);
			},
			fillLog: function(data){
				var $item = $('<div class="vertical-timeline-block">'+
	                '<div class="vertical-timeline-icon '+geProy.log[data.tipo].color+'">'+
	                    '<i class="fa '+geProy.log[data.tipo].icon+'"></i>'+
	                '</div>'+
	                '<div class="vertical-timeline-content">'+
	                    '<h2>'+geProy.log[data.tipo].text(data)+'</h2>'+
	                    '<p>'+geProy.log[data.tipo].descr(data)+'</p>'+
	                    '<span class="vertical-date">'+
	                        '<small>'+ciHelper.date.format.bd_ymdhi(data.fec)+'</small>'+
	                    '</span>'+
	                '</div>'+
	            '</div>');
				p.$w.find('#vertical-timeline').append($item);
			}
		});
		new K.Panel({
			contentURL: 'ge/proy/edit_meta',
			store: false,
			buttons: {
				"Regresar": {
					icon: 'fa-reply',
					type: 'warning',
					f: function(){
						geProy.windowAvance({
							id: p.proyecto.id,
							nomb: p.proyecto.nomb
						});
					}
				}
			},
			onContentLoaded: function(){
				p.$w = $('#mainPanel');
				p.$w.find('[name=btnTar]').click(function(){
					geProy.editTarea({
						id: p.id,
						callback: p.load
					});
				});
				p.load();
			}
		});
	},
	editTarea: function(p){
		new K.Modal({
			id: 'windowEdit',
			title: 'Editar Tarea',
			contentURL: 'ge/proy/edit_tarea',
			height: 350,
			width: 550,
			buttons: {
				"Guardar": {
					icon: 'fa-save',
					type: 'success',
					f: function(){
						K.clearNoti();
						var data = {
							_id: p.id,
							tarea: p.$w.find('[name=tarea]').val(),
							descr: p.$w.find('[name=descr]').val(),
							fec: p.$w.find('[name=fec]').val(),
							fecini: p.$w.find('[name=fecini]').val(),
							prioridad: p.$w.find('[name=prioridad] option:selected').val()
						};
						if(data.tarea==''){
							p.$w.find('[name=tarea]').focus();
							return K.msg({title: ciHelper.titles.infoReq,text: 'Debe ingresar una tarea!',type: 'error'});
						}
						if(data.descr==''){
							p.$w.find('[name=descr]').focus();
							return K.msg({title: ciHelper.titles.infoReq,text: 'Debe ingresar una descripcion!',type: 'error'});
						}
						if(data.fecini==''){
							p.$w.find('[name=fecini]').focus();
							return K.msg({title: ciHelper.titles.infoReq,text: 'Debe ingresar una fecha tentativa de inicio!',type: 'error'});
						}
						if(data.fec==''){
							p.$w.find('[name=fec]').focus();
							return K.msg({title: ciHelper.titles.infoReq,text: 'Debe ingresar una fecha limite!',type: 'error'});
						}
						if(p.index!=null){
							data.estado = p.data.estado;
							data.index = p.index;
						}
						K.sendingInfo();
						p.$w.find('#div_buttons button').attr('disabled','disabled');
						$.post("ge/proy/save_tarea",data,function(result){
							K.clearNoti();
							K.msg({title: ciHelper.titles.regiAct,text: "Proyecto actualizado!"});
							p.callback(data);
							K.closeWindow(p.$w.attr('id'));
						},'json');
					}
				},
				"Cancelar": {
					icon: 'fa-close',
					type: 'danger',
					f: function(){
						K.closeWindow(p.$w.attr('id'));
					}
				}
			},
			onClose: function(){ p = null; },
			onContentLoaded: function(){
				p.$w = $('#windowEdit');
				K.block();
				p.$w.find('[name=fec]').datepicker();
				p.$w.find('[name=fecini]').datepicker();
				var $cbo = p.$w.find('[name=prioridad]');
				for(var i=0; i<geProy.prioridad.length; i++){
					$cbo.append('<option value="'+geProy.prioridad[i].cod+'">'+geProy.prioridad[i].label+'</option>');
				}
				if(p.index!=null){
					p.$w.find('[name=tarea]').val(p.data.tarea);
					p.$w.find('[name=descr]').val(p.data.descr);
					p.$w.find('[name=fec]').val(p.data.fec);
					p.$w.find('[name=fecini]').val(p.data.fecini);
					p.$w.find('[name=prioridad]').selectVal(p.data.prioridad);
				}
				K.unblock();
			}
		});
	}
};
define(
	['mg/enti','ct/pcon'],
	function(mgEnti,ctPcon){
		return geProy;
	}
);
