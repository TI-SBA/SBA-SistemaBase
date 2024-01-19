/*************************************************************************
  Planificacion y Presupuestos: Reportes */
prRepo = {
	init: function(){
		K.initMode({
			mode: 'pr',
			action: 'prRepo',
			titleBar: {
				title: 'Reportes'
			}
		});	
		new K.Panel({
			id: 'mainPanel',
			contentURL: 'pr/repo',
			onContentLoaded: function(){
				$('#No-Results').hide();
				$mainPanel = $('#mainPanel');
				$mainPanel.find('.ui-layout-west').css('padding','0px').find('a').bind('click',function(event){
					event.preventDefault();
					var $anchor = $(this);
					$('#pageWrapperMain .ui-layout-center').scrollTo( $('#'+$anchor.attr('name')), 800 );
				}).eq(0).click().find('ul').addClass("ui-state-highlight");
				$section1 = $mainPanel.find('#section1');
				$mainPanel.find('[name=ano]').numeric().spinner().parent().find('.ui-button').css('height','14px');
				var d = new Date();
				$mainPanel.find('[name=ano]').val(d.getFullYear()); 
				$section1.find('[name=btnImprimir]').click(function(){	
					params = new Object;
					params.periodo = $section1.find('[name=ano]').val();
					//params.organizacion = "";
					params.programa = "";
					params.organomb= "";
					params.estadonomb= "";
					params.etapa = "P";
					params.page_rows = 99999;
				    params.page = (params.page) ? params.page : 1;
					//var url = 'pr/repo/acti?'+$.param(params);
					var url = 'pr/repo/acti_v1?'+$.param(params);
					K.windowPrint({
						id:'windowPlanProgPrintRepo',
						title: "Reporte / Informe",
						url: url
					});
				}).button({icons: {primary: 'ui-icon-print'}});			
				$section2 = $mainPanel.find('#section2');
				$section2.find('[name=orga]').html("Consolidado");
				$section2.find('[name=btnSelectOrga]').click(function(){
					ciSearch.windowSearchOrga({callback: function(data){
						$section2.find('[name=orga]').html(data.cod+' '+data.nomb).data('data',data._id.$id);
					}});
				}).button({icons: {primary: 'ui-icon-search'},text:false});
				$section2.find('[name=btnLimpiar]').click(function(){
					$section2.find('[name=orga]').html("Consolidado").removeData('data');
				}).button({icons: {primary: 'ui-icon-closethick'},text:false});
				$.post('pr/fuen/all',function(data){
					var $cbo = $section2.find('[name=fuente]');
					if(data!=null){
						$cbo.append('<option value="">Todos</option>');
						for(var i=0; i<data.length; i++){
							var rubro = data[i].rubro;
							var cod = data[i].cod;
							var id = data[i]._id.$id;
							$cbo.append('<option value="'+id+'" >'+rubro+'</option>');
						}
					}
				},'json');
				$section2.find('[name=btnImprimir]:eq(0)').click(function(){	
					var params = {
							tipo: $section2.find('[name=tipo] :selected').val(),
							ano: $section2.find('[name=ano]').val(),
							fuente:$section2.find('[name=fuente] :selected').val()
					};
					if($section2.find('[name=orga]').data('data')!=null)params.organizacion=$section2.find('[name=orga]').data('data');
					var url = 'pr/repo/pmen_pia?'+$.param(params);
					K.windowPrint({
						id:'windowPIAProgPrintRepo',
						title: "Reportes: PIA",
						url: url
					});
				}).button({icons: {primary: 'ui-icon-print'}});
				$section2.find('[name=btnImprimir]:eq(1)').click(function(){	
					var params = {
							tipo: $section2.find('[name=tipo] :selected').val(),
							ano: $section2.find('[name=ano]').val(),
							fuente:$section2.find('[name=fuente] :selected').val()
					};
					if($section2.find('[name=orga]').data('data')!=null)params.organizacion=$section2.find('[name=orga]').data('data');
					var url = 'pr/repo/pmen_pim?'+$.param(params);
					K.windowPrint({
						id:'windowPIMProgPrintRepo',
						title: "Reportes: PIM",
						url: url
					});
				}).button({icons: {primary: 'ui-icon-print'}});
				$section3 = $mainPanel.find('#section3');
				$section3.find('[name=btnImprimir]').click(function(){	
					var url = 'pr/repo/anexo1?';
					K.windowPrint({
						id:'windowEstrFuncProg',
						title: "Reporte / Estructura Programatica y Funcional",
						url: url
					});
				}).button({icons: {primary: 'ui-icon-print'}});
				$section4 = $mainPanel.find('#section4');
				$section4.find('[name=btnImprimir]').click(function(){	
					var params = {
							tipo: "I",
							periodo: $section4.find('[name=ano]').val(),
							mes: "0",
							organizacion: "",
							clasificador: "",
							organomb: "",
							clasnomb: "",
							page_rows: 99999,
							page: 1
					}, url = 'pr/repo/pmen2?'+$.param(params);
					K.windowPrint({
						id:'windowPlanProgPrintRepo',
						title: "Reportes: Programaci&oacute;n mensualizada de Ingresos",
						url: url
					});
				}).button({icons: {primary: 'ui-icon-print'}});
				$section5 = $mainPanel.find('#section5');
				$section5.find('[name=btnExportar]').click(function(){	
					//var url = 'pr/repo/poe?periodo='+$section5.find('[name=ano]').val()+'&filtro='+$section5.find('[name=filtro] :selected').val();
					var url = 'pr/repo/poe_v1?periodo='+$section5.find('[name=ano]').val()+'&filtro='+$section5.find('[name=filtro] :selected').val();
					window.open(url);
				}).button({icons: {primary: 'ui-icon-print'}});
				$section6 = $mainPanel.find('#section6');
				$.post('pr/fuen/all',function(data){
					var $cbo6 = $section6.find('[name=fuente]');
					if(data!=null){
						$cbo6.append('<option value="">Todos</option>');
						for(var i=0; i<data.length; i++){
							var rubro = data[i].rubro;
							var cod = data[i].cod;
							var id = data[i]._id.$id;
							$cbo6.append('<option value="'+id+'" >'+rubro+'</option>');
						}
					}
				},'json');
				$section6.find('[name=btnImprimir]').click(function(){	
					var params = {
						ano:$section6.find('[name=ano]').val(),
						fuente:$section6.find('[name=fuente] :selected').val()
					};
					var url = 'pr/repo/c1?'+$.param(params);
					K.windowPrint({
						id:'windowPrC1Repo',
						title: "Reportes",
						url: url
					});
				}).button({icons: {primary: 'ui-icon-print'}});
				$section7 = $mainPanel.find('#section7');
				$.post('pr/fuen/all',function(data){
					var $cbo7 = $section7.find('[name=fuente]');
					if(data!=null){
						$cbo7.append('<option value="">Todos</option>');
						for(var i=0; i<data.length; i++){
							var rubro = data[i].rubro;
							var cod = data[i].cod;
							var id = data[i]._id.$id;
							$cbo7.append('<option value="'+id+'" >'+rubro+'</option>');
						}
					}
				},'json');
				$section7.find('[name=btnImprimir]').click(function(){	
					var params = {
						ano:$section7.find('[name=ano]').val(),
						fuente:$section7.find('[name=fuente] :selected').val()
					};
					var url = 'pr/repo/c3?'+$.param(params);
					K.windowPrint({
						id:'windowPrC3Repo',
						title: "Reportes",
						url: url
					});
				}).button({icons: {primary: 'ui-icon-print'}});
				$section8 = $mainPanel.find('#section8');
				$.post('pr/fuen/all',function(data){
					var $cbo8 = $section8.find('[name=fuente]');
					if(data!=null){
						$cbo8.append('<option value="">Todos</option>');
						for(var i=0; i<data.length; i++){
							var rubro = data[i].rubro;
							var cod = data[i].cod;
							var id = data[i]._id.$id;
							$cbo8.append('<option value="'+id+'" >'+rubro+'</option>');
						}
					}
				},'json');
				$section8.find('[name=btnImprimir]').click(function(){	
					var params = {
						ano:$section8.find('[name=ano]').val(),
						fuente:$section8.find('[name=fuente] :selected').val()
					};
					var url = 'pr/repo/c4?'+$.param(params);
					K.windowPrint({
						id:'windowPrC4Repo',
						title: "Reportes",
						url: url
					});
				}).button({icons: {primary: 'ui-icon-print'}});
				$section9 = $mainPanel.find('#section9');
				$section9.find('[name=btnImprimir]').click(function(){	
					var params = {
						ano:$section9.find('[name=ano]').val()
					};
					var url = 'pr/repo/c5?'+$.param(params);
					K.windowPrint({
						id:'windowPrC5Repo',
						title: "Reportes",
						url: url
					});
				}).button({icons: {primary: 'ui-icon-print'}});
				$section10 = $mainPanel.find('#section10');
				$section10.find('[name=btnExportar]:eq(0)').click(function(){	
					var params = {
						periodo:$section10.find('[name=ano]:eq(0)').val(),
						etapa:$section10.find('[name=etapa] :selected').val()
					};
					var url = 'pr/repo/mefi?'+$.param(params);
					window.open(url);
				}).button({icons: {primary: 'ui-icon-print'}});
				$section10.find('[name=btnExportar]:eq(1)').click(function(){	
					var params = {
						periodo:$section10.find('[name=ano]:eq(1)').val(),
						filtro:$section10.find('[name=filtro] :selected').val()
					};
					var url = 'pr/repo/mefi2?'+$.param(params);
					window.open(url);
				}).button({icons: {primary: 'ui-icon-print'}});
				$('#pageWrapperMain').layout();
				$section11 = $mainPanel.find('#section11');
				$section11.find('[name=btnImprimir]').click(function(){
					prRepo.windowRepo1({ano:$section11.find('[name=ano]').val(),tipo:$section11.find('[name=tipo] :selected').val()});
				}).button({icons: {primary: 'ui-icon-print'}});
				$section11.find('[name=btnImprimir2]').click(function(){
					prRepo.windowRepo4({ano:$section11.find('[name=ano]').val(),tipo:$section11.find('[name=tipo] :selected').val()});
				}).button({icons: {primary: 'ui-icon-print'}});
				$section12 = $mainPanel.find('#section12');
				$section12.find('[name=btnImprimir]').click(function(){
					prRepo.windowRepo2({
						ano:$section12.find('[name=ano]').val(),
						tipo:$section12.find('[name=tipo] :selected').val(),
						etapa:$section12.find('[name=etapa] :selected').val()
					});
				}).button({icons: {primary: 'ui-icon-print'}});
				$section13 = $mainPanel.find('#section13');
				$section13.find('[name=btnImprimir]').click(function(){
					prRepo.windowRepo3({
						ano:$section13.find('[name=ano]').val(),
						tipo:$section13.find('[name=tipo] :selected').val(),
						etapa:$section13.find('[name=etapa] :selected').val()
					});
				}).button({icons: {primary: 'ui-icon-print'}});
				$mainPanel.layout({
					resizeWithWindow:	false,
					west__size:			200,
					west__closable:		false,
					west__resizable:	false,
					west__slidable:		false
				});
				$mainPanel.find('fieldset').height($mainPanel.height());
			}
		});
		
		/*$(window).resize(function(){
			$('#mainPanel').height($('#pageWrapperMain')+'px');
		}).resize();*/
		$('#pageWrapperMain').layout();
		K.unblock({$element: $('#pageWrapperMain')});
	},
	windowRepo1: function(p){
		if(p==null) p = new Object;
		new K.Window({
			id: 'windowPrRepo1',
			title: 'Editar Meta',
			content: '<div id="cuadro1"></div>',
			icon: 'ui-icon-plusthick',
			width: 800,
			height: 500,
			buttons: {
			},
			onContentLoaded: function(){
				p.$w = $('#windowPrRepo1');
				$(window).resize(function(){
					var $this = $('#windowPrRepo1');
					$this.dialog( "option", "height", $(window).height() )
					.dialog( "option", "width", $(window).width() )
					.dialog( "option", "position", [ 0 , 0 ] )
					.dialog( "option", "draggable", false )
					.dialog( "option", "resizable", false );
					$this.height(($this.height()-0)+'px');
				}).resize();
				K.block({$element: p.$w});
				//var c = new Object;
				p.cate = new Array;
				p.data = [
					{name:"Ingreso Anual PIA",data:[]},
					{name:"Ingreso Anual PIM",data:[]},
					{name:"Ejecución de Ingresos",data:[]}
				];
				p.data[0].dataLabels = {
                    enabled: true,
                    rotation: 45,
                    color: '#000000',
                    align: 'right',
                    x: 4,
                    y: -10,
                    style: {
                        fontSize: '10px',
                        fontFamily: 'Verdana, sans-serif',
                        textShadow: '0 0 3px white'
                    }
                };
                p.data[1].dataLabels = {
                    enabled: true,
                    rotation: 45,
                    color: '#000000',
                    align: 'right',
                    x: 4,
                    y: -10,
                    style: {
                        fontSize: '10px',
                        fontFamily: 'Verdana, sans-serif',
                        textShadow: '0 0 3px white'
                    }
                };
                p.data[2].dataLabels = {
                    enabled: true,
                    rotation: 45,
                    color: '#000000',
                    align: 'right',
                    x: 4,
                    y: -10,
                    style: {
                        fontSize: '10px',
                        fontFamily: 'Verdana, sans-serif',
                        textShadow: '0 0 3px white'
                    }
                };
				$.post('pr/repo/cuadro1?ano='+p.ano+'&tipo='+p.tipo,function(data){
					if(data!=null){
						for(i=0;i<data.length;i++){
							p.cate.push(data[i].actividad.nomb);
							/*c.data.push({
				                name: data[i].actividad.nomb,
				                data: [data[i].pim, data[i].pia, data[i].eje]
				            });*/
							p.data[0].data.push(data[i].pia);
							p.data[1].data.push(data[i].pim);
							p.data[2].data.push(data[i].eje);
						}
						
					}
					p.$w.find('#cuadro1').highcharts({
			            chart: {
			                type: 'column'
			            },
			            title: {
			                text: 'PRESUPUESTO INSTITUCIONAL DE '+((p.tipo=="I")?"INGRESOS":"GASTOS")+' POR DEPENDENIA AÑO '+p.ano
			            },
			            xAxis: {
			                categories: p.cate
			            },
			            yAxis: {
			                min: 0,
			                title: {
			                    text: 'Nuevos Soles (S/.)'
			                }
			            },
			            tooltip: {
			                headerFormat: '<span style="font-size:10px">{point.key}</span><table>',
			                pointFormat: '<tr><td style="color:{series.color};padding:0">{series.name}: </td>' +
			                    '<td style="padding:0"><b>S/. {point.y:.2f}</b></td></tr>',
			                footerFormat: '</table>',
			                shared: true,
			                useHTML: true
			            },
			            plotOptions: {
			                column: {
			                    pointPadding: 0.2,
			                    borderWidth: 0
			                }
			            },
			            series: p.data,
			            credits: {
			                enabled: false
			            }
			        });
					K.unblock({$element: p.$w});
				},'json');
			}
		});
	},
	windowRepo2: function(p){
		if(p==null) p = new Object;
		new K.Window({
			id: 'windowPrRepo2',
			title: 'Editar Meta',
			content: '<div id="cuadro"></div>',
			icon: 'ui-icon-plusthick',
			width: 800,
			height: 500,
			buttons: {
			},
			onContentLoaded: function(){
				p.$w = $('#windowPrRepo2');
				$(window).resize(function(){
					var $this = $('#windowPrRepo2');
					$this.dialog( "option", "height", $(window).height() )
					.dialog( "option", "width", $(window).width() )
					.dialog( "option", "position", [ 0 , 0 ] )
					.dialog( "option", "draggable", false )
					.dialog( "option", "resizable", false );
					$this.height(($this.height()-0)+'px');
				}).resize();
				K.block({$element: p.$w});
				p.data = [];
				p.title = "";
				p._tipo = "INGRESOS";
				if(p.tipo=="G") p._tipo = "GASTOS";
				switch(p.etapa){
					case "PIA":
						p.title = "PRESUPUESTO DE APERTURA (PIA) DE "+p._tipo+" POR DEPENDENCIAS DEL AÑO "+p.ano;
						break;
					case "PIM":
						p.title = "PRESUPUESTO DE MODIFICADO (PIM) DE "+p._tipo+" POR DEPENDENCIAS DEL AÑO "+p.ano;
						break;
					case "EJE":
						p.title = "EJECUCION DEL PRESUPUESTO DE "+p._tipo+" POR DEPENDENCIAS DEL AÑO "+p.ano;
						break;
				}
				$.post('pr/repo/cuadro1?ano='+p.ano+'&tipo='+p.tipo,function(data){
					if(data!=null){
						for(i=0;i<data.length;i++){
							switch(p.etapa){
								case "PIA":
									p.data.push([data[i].actividad.nomb,data[i].pia]);
									break;
								case "PIM":
									p.data.push([data[i].actividad.nomb,data[i].pim]);
									break;
								case "EJE":
									p.data.push([data[i].actividad.nomb,data[i].eje]);
									break;
							}
						}
					}
					p.$w.find('#cuadro').highcharts({
				        chart: {
				            plotBackgroundColor: null,
				            plotBorderWidth: null,
				            plotShadow: false
				        },
				        title: {
				            text: p.title
				        },
				        tooltip: {
				    	    pointFormat: '{series.name}: <b>S/. {point.y}</b>'
				        },
				        plotOptions: {
				            pie: {
				                allowPointSelect: true,
				                cursor: 'pointer',
				                dataLabels: {
				                    enabled: true,
				                    color: '#000000',
				                    connectorColor: '#000000',
				                    format: '<b>{point.name}</b>: {point.percentage:.1f} %'
				                }
				            }
				        },
				        series: [{
				            type: 'pie',
				            name: 'Nuevos Soles',
				            data: p.data
				        }],
				        credits: {
			                enabled: false
			            }
				    });
					K.unblock({$element: p.$w});
				},'json');
			}
		});
	},
	windowRepo3: function(p){
		if(p==null) p = new Object;
		new K.Window({
			id: 'windowPrRepo3',
			title: 'Editar Meta',
			content: '<div id="cuadro"></div>',
			icon: 'ui-icon-plusthick',
			width: 800,
			height: 500,
			buttons: {
			},
			onContentLoaded: function(){
				p.$w = $('#windowPrRepo3');
				$(window).resize(function(){
					var $this = $('#windowPrRepo3');
					$this.dialog( "option", "height", $(window).height() )
					.dialog( "option", "width", $(window).width() )
					.dialog( "option", "position", [ 0 , 0 ] )
					.dialog( "option", "draggable", false )
					.dialog( "option", "resizable", false );
					$this.height(($this.height()-0)+'px');
				}).resize();
				K.block({$element: p.$w});
				p.data = [];
				p.title = "";
				p._tipo = "INGRESOS";
				if(p.tipo=="G") p._tipo = "GASTOS";
				switch(p.etapa){
					case "PIA":
						p.title = "PRESUPUESTO DE APERTURA MENSUAL (PIA) DE "+p._tipo+" DEL AÑO "+p.ano;
						break;
					case "PIM":
						p.title = "PRESUPUESTO DE MODIFICADO MENSUAL (PIM) DE "+p._tipo+" DEL AÑO "+p.ano;
						break;
					case "EJE":
						p.title = "EJECUCION MENSUAL DEL PRESUPUESTO DE "+p._tipo+" DEL AÑO "+p.ano;
						break;
				}
				$.post('pr/repo/cuadro2?ano='+p.ano+'&tipo='+p.tipo,function(data){
					if(data!=null){
						for(i=0;i<data.length;i++){
							switch(p.etapa){
								case "PIA":
									var pia = [
										data[i].pia[1],
										data[i].pia[2],
										data[i].pia[3],
										data[i].pia[4],
										data[i].pia[5],
										data[i].pia[6],
										data[i].pia[7],
										data[i].pia[8],
										data[i].pia[9],
										data[i].pia[10],
										data[i].pia[11],
										data[i].pia[12]
									];
									p.data.push({name:data[i].clasificador.cod+' '+data[i].clasificador.nomb,data:pia});
									break;
								case "PIM":
									var pim = [
										data[i].pim[1],
										data[i].pim[2],
										data[i].pim[3],
										data[i].pim[4],
										data[i].pim[5],
										data[i].pim[6],
										data[i].pim[7],
										data[i].pim[8],
										data[i].pim[9],
										data[i].pim[10],
										data[i].pim[11],
										data[i].pim[12]
									];
									p.data.push({name:data[i].clasificador.cod+' '+data[i].clasificador.nomb,data:pim});
									break;
								case "EJE":
									var eje = [
										data[i].eje[1],
										data[i].eje[2],
										data[i].eje[3],
										data[i].eje[4],
										data[i].eje[5],
										data[i].eje[6],
										data[i].eje[7],
										data[i].eje[8],
										data[i].eje[9],
										data[i].eje[10],
										data[i].eje[11],
										data[i].eje[12]
									];
									p.data.push({name:data[i].clasificador.cod+' '+data[i].clasificador.nomb,data:eje});
									break;
							}
						}
					}
					p.$w.find('#cuadro').highcharts({
			            chart: {
			                type: 'line'
			            },
			            title: {
			                text: p.title
			            },
			            xAxis: {
			                categories: ['ENERO', 'FEBRERO', 'MARZO', 'ABRIL', 'MAYO', 'JUNIO', 'JULIO', 'AGOSTO', 'SETIEMBRE', 'OCTUBRE', 'NOVIEMBRE', 'DDICIEMBRE']
			            },
			            yAxis: {
			                title: {
			                    text: 'Nuevos Soles (S/.)'
			                }
			            },
			            tooltip: {
			                enabled: true,
			                formatter: function() {
			                    return '<b>'+ this.series.name +'</b><br/>'+
			                        this.x +': S/.'+ this.y ;
			                }
			            },
			            plotOptions: {
			                line: {
			                    dataLabels: {
			                        enabled: false,
			                        style: {
			                            textShadow: '0 0 3px white, 0 0 3px white'
			                        }
			                    }
			                }
			            },
			            /*legend: {
			                layout: 'vertical',
			                align: 'right',
			                verticalAlign: 'middle',
			                borderWidth: 0
			            },*/
			            series: p.data,
				        credits: {
			                enabled: false
			            },
			            exporting: {
			            	sourceWidth: 1100,
			                sourceHeight: 600,
			            }
			        });
					K.unblock({$element: p.$w});
				},'json');
			}
		});
	},
	windowRepo4: function(p){
		if(p==null) p = new Object;
		new K.Window({
			id: 'windowPrRepo4',
			title: 'Editar Meta',
			content: '<div id="cuadro1"></div>',
			icon: 'ui-icon-plusthick',
			width: 800,
			height: 500,
			buttons: {
			},
			onContentLoaded: function(){
				p.$w = $('#windowPrRepo4');
				$(window).resize(function(){
					var $this = $('#windowPrRepo1');
					$this.dialog( "option", "height", $(window).height() )
					.dialog( "option", "width", $(window).width() )
					.dialog( "option", "position", [ 0 , 0 ] )
					.dialog( "option", "draggable", false )
					.dialog( "option", "resizable", false );
					$this.height(($this.height()-0)+'px');
				}).resize();
				K.block({$element: p.$w});
				//var c = new Object;
				p.cate = new Array;
				p.data = [
					{name:"Ingreso Anual PIA",data:[]},
					{name:"Ingreso Anual PIM",data:[]},
					{name:"Ejecución de Ingresos",data:[]}
				];
				p.data[0].dataLabels = {
                    enabled: true,
                    rotation: 45,
                    color: '#000000',
                    align: 'right',
                    x: 4,
                    y: -10,
                    style: {
                        fontSize: '10px',
                        fontFamily: 'Verdana, sans-serif',
                        textShadow: '0 0 3px white'
                    }
                };
                p.data[1].dataLabels = {
                    enabled: true,
                    rotation: 45,
                    color: '#000000',
                    align: 'right',
                    x: 4,
                    y: -10,
                    style: {
                        fontSize: '10px',
                        fontFamily: 'Verdana, sans-serif',
                        textShadow: '0 0 3px white'
                    }
                };
                p.data[2].dataLabels = {
                    enabled: true,
                    rotation: 45,
                    color: '#000000',
                    align: 'right',
                    x: 4,
                    y: -10,
                    style: {
                        fontSize: '10px',
                        fontFamily: 'Verdana, sans-serif',
                        textShadow: '0 0 3px white'
                    }
                };
				$.post('pr/repo/cuadro2?ano='+p.ano+'&tipo='+p.tipo,function(data){
					if(data!=null){
						for(i=0;i<data.length;i++){
							p.cate.push(data[i].clasificador.cod+' '+data[i].clasificador.nomb);
							p.data[0].data.push(data[i].tot_pia);
							p.data[1].data.push(data[i].tot_pim);
							p.data[2].data.push(data[i].tot_eje);
						}	
					}
					p.$w.find('#cuadro1').highcharts({
			            chart: {
			                type: 'column'
			            },
			            title: {
			                text: 'PRESUPUESTO INSTITUCIONAL DE '+((p.tipo=="I")?"INGRESOS":"GASTOS")+' POR GENERICAS CONSOLIDADO AÑO '+p.ano
			            },
			            xAxis: {
			                categories: p.cate
			            },
			            yAxis: {
			                min: 0,
			                title: {
			                    text: 'Nuevos Soles (S/.)'
			                }
			            },
			            tooltip: {
			                headerFormat: '<span style="font-size:10px">{point.key}</span><table>',
			                pointFormat: '<tr><td style="color:{series.color};padding:0">{series.name}: </td>' +
			                    '<td style="padding:0"><b>S/. {point.y:.2f}</b></td></tr>',
			                footerFormat: '</table>',
			                shared: true,
			                useHTML: true
			            },
			            plotOptions: {
			                column: {
			                    pointPadding: 0.2,
			                    borderWidth: 0
			                }
			            },
			            series: p.data,
			            credits: {
			                enabled: false
			            }
			        });
					K.unblock({$element: p.$w});
				},'json');
			}
		});
	}
};