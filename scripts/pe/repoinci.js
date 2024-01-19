/*******************************************************************************
reportes incidencias */
peRepoInci = {
	tipo: {
		VA: "Vacaciones",
		LI: "Licencia",
		PE: "Permiso",
		TO: "Tolerancia",
		TA: "Tardanza",
		IN: "Inasistencia",
		CO: "Compensaci&oacute;nn",
		TE: "Tiempo Extra"
	},
	init: function(){
		if($('#pageWrapper [child=repo]').length<=0){
			var $p = $('#pageWrapperLeft');
			$.post('pe/navg/repo',function(data){
				for(var i=0; i<data.length; i++){
					var result = data[i];
					var $row = $p.find('.gridReference').clone();
					$row.find('li:eq(0)').html( '<span class="ui-icon '+result.icon+'"></span>' + result.descr )
						.css({
							"padding-left": "10px",
							"min-width": "186px",
							"max-width": "186px"
						});
					$row.wrapInner('<a name="'+result.name+'" class="item" href="javascript: void(0);" child="repo" />');
					$p.find("[name=peRepo]").after( $row.children() );
				}
				$p.find('[name=peRepo]').data('repo',$('#pageWrapper [child=repo]:first').data('repo'));
				$p.find('[name=peRepoInci]').click(function(){ peRepoInci.init(); }).addClass('ui-state-highlight');
				$p.find('[name=peRepoGene]').click(function(){ peRepo.init(); });
			},'json');
		}
		K.initMode({
			mode: 'pe',
			action: 'peRepoInci',
			titleBar: {
				title: 'Reportes: Incidencias'
			}
		});
		
		new K.Panel({
			id: 'mainPanel',
			contentURL: 'pe/repo/inci',
			onContentLoaded: function(){
				$('#No-Results').hide();
				$mainPanel = $('#mainPanel');
				$mainPanel.find('[name=buscar]').attr( 'placeholder' , 'Ingrese el nombre de trabajador' ).width('250');
				$mainPanel.resize(function(){
					$mainPanel.find('.grid:eq(1)').height(($mainPanel.height()-$mainPanel.find('.grid:eq(0)').height()-30)+'px');
				}).resize();
				$mainPanel.find('.grid:eq(0)').css('overflow','hidden');
				$mainPanel.find('.grid:eq(1)').scroll(function(){
					$mainPanel.find('.grid:eq(0)').scrollLeft($(this).scrollLeft());
				});
				$mainPanel.find('.divSearch [name=buscar]').keyup(function(e){
					if(e.keyCode == 13) $('.divSearch [name=btnBuscar]').click();
				});
				$mainPanel.find('.divSearch [name=btnBuscar]').click(function(){
					$("#mainPanel .gridBody").empty();
					peRepoInci.loadData({page: 1,url: 'pe/repo/lista_inci'});
				}).button({icons: {primary: 'ui-icon-search'}});
				$mainPanel.find('[name=periodo]').datepicker( {
			        dateFormat: 'MM yy',
			        onClose: function(dateText, inst) { 
			            var month = $("#ui-datepicker-div .ui-datepicker-month :selected").val();
			            var year = $("#ui-datepicker-div .ui-datepicker-year :selected").val();
			            $(this).data('mes',month).data('ano',year);
			            $(this).val($.datepicker.formatDate('MM yy', new Date(year, month, 1)));
			            $(this).change();
			        }
			    }).focus(function(){
			    	$('.ui-datepicker-calendar').css('display','none');
			    }).val(ciHelper.meses[ciHelper.date.getMonth()-1]+' '+ciHelper.date.getYear())
			    .data('mes',+ciHelper.date.getMonth()-1)
			    .data('ano',ciHelper.date.getYear()).change(function(){
			    	$mainPanel.find('.gridBody').empty();
			    	peRepoInci.loadData({page: 1,url: 'pe/repo/lista_inci'});
			    }).change();
				$.post('pe/cont/all',function(data){
					if(data==null){
						K.clearNoti();
						return K.notification({title: 'Tipos de Contrato inv&aacute;lidos',text: 'Debe registrar primero tipos de contrato!',type: 'info'});
					}else{
						var $cbo = $mainPanel.find('[name=tipo_contrato]');
						for(var i=0,j=data.length; i<j; i++){
							$cbo.append('<option value="'+data[i].cod+'">'+data[i].nomb+'</option>');
							$cbo.find('option:last').data('data',data[i]);
						}
					}
					$mainPanel.find('[name=tipo_contrato]').change(function(){
						$("#mainPanel .gridBody").empty();
						peRepoInci.loadData({page: 1,url: 'pe/repo/lista_inci'});
					}).change();
					$mainPanel.find('[name=btnExportar]').click(function(){	
						
						window.open('pe/repo/asistencia_contratados?ano='+$mainPanel.find('[name=periodo]').data('ano')+'&mes='+$mainPanel.find('[name=periodo]').data('mes'));
					}).button({icons: {primary: 'ui-icon-print'}});	
				},'json');
			}
		});
		$('#pageWrapperMain').layout();
	},
	loadData: function(params){
		$.extend(params,{
			texto: $('.divSearch [name=buscar]').val(),
			tipo: $mainPanel.find('[name=tipo_contrato] option:selected').val(),
    		mes: +$mainPanel.find('[name=periodo]').data('mes')+1,
    		ano: $mainPanel.find('[name=periodo]').data('ano')
		});
	    $.post(params.url, params, function(data){
			if ( data!=null ) {
				for (i=0; i < data.length; i++) {
					var result = data[i];
					var $row = $('.gridReference','#mainPanel').clone();
					var $li = $('li',$row);
					$li.html(0);
					$li.eq(0).html('<button name="btnGrid">M&aacute;s Acciones</button>');
					$li.eq(1).html( ciHelper.enti.formatName(result) );
					if(result.inci!=null){
						for(var j=0,k=result.inci.length; j<k; j++){
							switch(result.inci[j].tipo.tipo){
								case "VA": 
									$li.eq(2).html( parseInt($li.eq(2).html())+1 );
									break;
								case "LI": 
									$li.eq(3).html( parseInt($li.eq(3).html())+1 );
									break;
								case "PE": 
									$li.eq(4).html( parseInt($li.eq(4).html())+1 );
									break;
								case "TO": 
									$li.eq(5).html( parseInt($li.eq(5).html())+1 );
									break;
								case "TA": 
									$li.eq(6).html( parseInt($li.eq(6).html())+1 );
									break;
								case "IN": 
									$li.eq(7).html( parseInt($li.eq(7).html())+1 );
									break;
								case "CO": 
									$li.eq(8).html( parseInt($li.eq(8).html())+1 );
									break;
								case "TE": 
									$li.eq(9).html( parseInt($li.eq(9).html())+1 );
									break;
							}
						}
					}
					$row.wrapInner('<a class="item" href="javascript: void(0);" />');
					$row.find('a').data('id',result._id.$id).contextMenu("conMenPeInci", {
						onShowMenu: function(e, menu) {
							$(e.target).closest('.gridBody').find('ul').removeClass('ui-state-highlight');
							$(e.target).closest('.item').find('ul').addClass('ui-state-highlight');
							$(e.target).closest('.item').click();
							K.tmp = $(e.target).closest('.item');
							return menu;
						},
						bindings: {
							'conMenPeInci_vac': function(t) {
								peRepoInci.windowDetails({
									id: K.tmp.data('id'),
									nomb: K.tmp.find('li:eq(1)').html(),
									mes: +$mainPanel.find('[name=periodo]').data('mes')+1,
						    		ano: $mainPanel.find('[name=periodo]').data('ano'),
						    		tipo: 'VA',
									inci: K.tmp.data('inci')
						    	});
							},
							'conMenPeInci_lic': function(t) {
								peRepoInci.windowDetails({
									id: K.tmp.data('id'),
									nomb: K.tmp.find('li:eq(1)').html(),
									mes: +$mainPanel.find('[name=periodo]').data('mes')+1,
						    		ano: $mainPanel.find('[name=periodo]').data('ano'),
						    		tipo: 'LI',
									inci: K.tmp.data('inci')
						    	});
							},
							'conMenPeInci_per': function(t) {
								peRepoInci.windowDetails({
									id: K.tmp.data('id'),
									nomb: K.tmp.find('li:eq(1)').html(),
									mes: +$mainPanel.find('[name=periodo]').data('mes')+1,
						    		ano: $mainPanel.find('[name=periodo]').data('ano'),
						    		tipo: 'PE',
									inci: K.tmp.data('inci')
						    	});
							},
							'conMenPeInci_tol': function(t) {
								peRepoInci.windowDetails({
									id: K.tmp.data('id'),
									nomb: K.tmp.find('li:eq(1)').html(),
									mes: +$mainPanel.find('[name=periodo]').data('mes')+1,
						    		ano: $mainPanel.find('[name=periodo]').data('ano'),
						    		tipo: 'TO',
									inci: K.tmp.data('inci')
						    	});
							},
							'conMenPeInci_tar': function(t) {
								peRepoInci.windowDetails({
									id: K.tmp.data('id'),
									nomb: K.tmp.find('li:eq(1)').html(),
									mes: +$mainPanel.find('[name=periodo]').data('mes')+1,
						    		ano: $mainPanel.find('[name=periodo]').data('ano'),
						    		tipo: 'TA',
									inci: K.tmp.data('inci')
						    	});
							},
							'conMenPeInci_ina': function(t) {
								peRepoInci.windowDetails({
									id: K.tmp.data('id'),
									nomb: K.tmp.find('li:eq(1)').html(),
									mes: +$mainPanel.find('[name=periodo]').data('mes')+1,
						    		ano: $mainPanel.find('[name=periodo]').data('ano'),
						    		tipo: 'IN',
									inci: K.tmp.data('inci')
						    	});
							},
							'conMenPeInci_com': function(t) {
								peRepoInci.windowDetails({
									id: K.tmp.data('id'),
									nomb: K.tmp.find('li:eq(1)').html(),
									mes: +$mainPanel.find('[name=periodo]').data('mes')+1,
						    		ano: $mainPanel.find('[name=periodo]').data('ano'),
						    		tipo: 'CO',
									inci: K.tmp.data('inci')
						    	});
							},
							'conMenPeInci_tie': function(t) {
								peRepoInci.windowDetails({
									id: K.tmp.data('id'),
									nomb: K.tmp.find('li:eq(1)').html(),
									mes: +$mainPanel.find('[name=periodo]').data('mes')+1,
						    		ano: $mainPanel.find('[name=periodo]').data('ano'),
						    		tipo: 'TE',
									inci: K.tmp.data('inci')
						    	});
							}
						}
					}).data('inci',result.inci);
		        	$("#mainPanel .gridBody").append( $row.children() );
					ciHelper.gridButtons($("#mainPanel .gridBody"));
		        }
	      }else K.notification({text: 'No se encontraron trabajadores relacionados al criterio seleccionado!',type: 'info'});
	      $('#mainPanel').resize();
	      K.unblock({$element: $('#pageWrapperMain')});
	    }, 'json');
	},
	windowDetails: function(p){
		if(p.inci==null){
			return K.notification({title: 'Incidencias no encontradas',text: 'El trabajador <b>'+p.nomb+'</b> no tiene incidencias clasificadas para el periodo seleccionado!',type: 'error'});
		}
		new K.Modal({
			id: 'windowDetailsRepoInci'+p.id,
			title: 'Ver Incidencias de '+p.nomb,
			contentURL: 'pe/repo/details_inci',
			icon: 'ui-icon-signal',
			width: 750,
			height: 350,
			buttons: {
				"Cerrar": function(){
					K.closeWindow(p.$w.attr('id'));
				}
			},
			onClose: function(){ p = null; },
			onContentLoaded: function(){
				p.$w = $('#windowDetailsRepoInci'+p.id);
				p.$w.find('fieldset').css('padding','0px');
				p.$w.find('legend').html(peRepoInci.tipo[p.tipo]);
				p.$w.find('.grid').eq(1).bind('scroll',function(){
					p.$w.find('.grid').eq(0).scrollLeft(p.$w.find('.grid').eq(1).scrollLeft());
				});
				var tot = 0;
				for(var i=0,j=p.inci.length; i<j; i++){
					if(p.inci[i].tipo.tipo==p.tipo){
						var result = p.inci[i];
						var $row = p.$w.find('.gridReference').clone();
						var $li = $('li',$row);
						$li.eq(0).html( result.tipo.nomb );
						$li.eq(1).html( ciHelper.dateFormat(result.fecini) );
						$li.eq(2).html( ciHelper.dateFormat(result.fecfin) );
						var fecini = new Date(result.fecini.sec*1000);
						var fecfin = new Date(result.fecfin.sec*1000);
						if(ciHelper.dateFormatBDNotHour(result.fecini)==ciHelper.dateFormatBDNotHour(result.fecfin)){
							$li.eq(3).html( ((fecfin.getTime()-fecini.getTime())/60/1000)+' minutos' );
						}else{
							$li.eq(3).html( ((fecfin.getTime()-fecini.getTime())/24/60/60/1000).toFixed()+' d&iacute;a(s)' );
						}
						$li.eq(4).html( result.observ );
						$row.wrapInner('<a class="item" />');
			        	p.$w.find(".gridBody").append( $row.children() );
			        	tot++;
					}
				}
				if(tot==0){
					K.notification({title: 'Incidencias no encontradas',text: 'El trabajador <b>'+p.nomb+'</b> no tiene incidencias clasificadas del tipo <b>'+peRepoInci.tipo[p.tipo]+'</b> para el periodo seleccionado!',type: 'error'});
					return K.closeWindow(p.$w.attr('id'));
				}
			}
		});
	}
};