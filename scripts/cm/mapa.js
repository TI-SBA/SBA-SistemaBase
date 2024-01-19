cmMapa = {
	init: function(){
		K.initMode({
			mode: 'cm',
			action: 'cmMapa',
			titleBar: {
				title: 'Mapa de Cementerio'
			}
		});
		
		new K.Panel({
			id: 'mainPanel',
			contentURL: 'cm/mapa',
			onContentLoaded: function(){
				$mainPanel = $('#mainPanel');
				$mainPanel.find('[name=buscar]').attr( 'placeholder' , 'Ingrese el nombre de ubicacion' ).width('250');
				$mainPanel.resize(function(){
					$mainPanel.find('.grid:eq(1)').height(($mainPanel.height()-$mainPanel.find('.grid:eq(0)').height()-$('.div-bottom').outerHeight()-$('.div-bottom').height())+'px');
				}).resize();
				$mainPanel.find('[name=btnEditar]').click(function(){
					cmMapa.edit($(this).data('data'));
				}).button({icons: {primary: 'ui-icon-pencil'}}).hide();
				$mainPanel.find('[name=btnHome]').click(function(){
					cmMapa.init();
				}).button({icons: {primary: 'ui-icon-pencil'}}).hide();
				$mainPanel.find('[name=btnBuscar]').click(function(){
					cmEspa.windowSelect({
						callback: function(data){
							if(data.imagen!=null){
								if(data.nicho==null){
									var cuadrantes = $mainPanel.data('data'),
									tmp = {};
									for(var i=0,j=cuadrantes.length; i<j; i++){
										if(data.imagen.$id==cuadrantes[i]._id.$id)
											tmp = cuadrantes[i];
									}
									tmp.espa_select = data._id.$id;
									cmMapa.loadCuad(tmp);
								}else{
									var cuadrantes = $mainPanel.data('data'),
									tmp = {};
									for(var i=0,j=cuadrantes.length; i<j; i++){
										if(data.sector==cuadrantes[i].cm_nomb)
											tmp = cuadrantes[i];
									}
									tmp.espa_select = data.nicho.pabellon._id.$id;
									cmMapa.loadCuad(tmp);
									$.post('cm/pabe/get',{_id: data.nicho.pabellon._id.$id},function(pabe){
										pabe.espa_select = data._id.$id;
										cmMapa.loadPabe(pabe);
									},'json');
								}
							}else{
								if(data.nicho!=null){
									cmEspa.windowDetailsNicho({_id: data._id.$id,nomb: data.nomb,data: data});
								}else if(data.mausoleo!=null){
									cmEspa.windowDetailsMauso({_id: data._id.$id,nomb: data.nomb,data: data});
								}else if(data.tumba!=null){
									cmEspa.windowDetailsTumba({_id: data._id.$id,nomb: data.nomb,data: data});
								}
							}
						}
					});
				}).button({icons: {primary: 'ui-icon-search'}});
				$mainPanel.find('[name=lienzo]').remove();
				$.post('cm/mapa/cuadra',function(data){
					$mainPanel.data('data',data.cuadra);
					$mainPanel.append('<div name="lienzo">');
					var $lienzo = $mainPanel.find('[name=lienzo]');
					$('#mainPanel [name=lienzo]').css({'background-image':'url("ci/files/get?id='+data.total._id.$id+'")','width': data.total.metadata.width+'px','height': data.total.metadata.height+'px'});
					$lienzo.css({
						'background-image':'url("ci/files/get?id='+data.total._id.$id+'")',
						'width': data.total.metadata.width+'px',
						'height': data.total.metadata.height+'px'
					});
					$lienzo.svg();
					var svg = $lienzo.svg('get');
					for(var i=0,j=data.cuadra.length; i<j; i++){
						switch(data.cuadra[i].cm_nomb){
							case 'A':
								svg.polygon([[567,15],[751,15],[751,171],[567,171]], {'fill-opacity': '0',stroke: 'blue', strokeWidth: 2, id:'lienzo-'+i});
								break;
							case 'B':
								svg.polygon([[567,172],[751,172],[751,339],[567,339]], {'fill-opacity': '0',stroke: 'blue', strokeWidth: 2, id:'lienzo-'+i});
								break;
							case 'C':
								svg.polygon([[394,172],[566,172],[566,339],[394,339]], {'fill-opacity': '0',stroke: 'blue', strokeWidth: 2, id:'lienzo-'+i});
								break;
							case 'D':
								svg.polygon([[394,15],[566,15],[566,171],[394,171]], {'fill-opacity': '0',stroke: 'blue', strokeWidth: 2, id:'lienzo-'+i});
								break;
							case 'E':
								svg.polygon([[0,15],[393,15],[393,129],[43,129],[43,93],[0,93]], {'fill-opacity': '0',stroke: 'blue', strokeWidth: 2, id:'lienzo-'+i});
								break;
							case 'F':
								svg.polygon([[0,93],[43,93],[43,129],[393,129],[393,224],[110,224],[110,242],[0,242]], {'fill-opacity': '0',stroke: 'blue', strokeWidth: 2, id:'lienzo-'+i});
								break;	
							case 'G':
								svg.polygon([[0,242],[110,242],[110,224],[393,224],[393,339],[0,339]], {'fill-opacity': '0',stroke: 'blue', strokeWidth: 2, id:'lienzo-'+i});
								break;
						}
						var g = svg.group({'font-weight': 'bold'});
					    svg.text(g, 630, 100, 'Cuadrante A');
					    svg.text(g, 630, 260, 'Cuadrante B');
					    svg.text(g, 445, 260, 'Cuadrante C');
					    svg.text(g, 445, 100, 'Cuadrante D');
					    svg.text(g, 160, 80, 'Cuadrante E');
					    svg.text(g, 160, 180, 'Cuadrante F');
					    svg.text(g, 160, 290, 'Cuadrante G');
						$lienzo.find('#lienzo-'+i).attr('class','polygon').click(function(){
							cmMapa.loadCuad($(this).data('data'));
						}).data('data',data.cuadra[i]).bind({
							mouseenter: function(e){
								K.tmp = $(this).data('data');
								this.top = (e.pageY + yOffset);
								this.left = (e.pageX + xOffset);
								cmMapa.showInfo({data: K.tmp});
								$('#vtipCem').css("top", this.top+"px").css("left", this.left+"px").fadeIn("fast");
							},
							mouseleave: function(){
								$("#vtipCem").fadeOut("slow").remove();
							},
							mousemove: function(e){
						        if($("#vtipCem").length==0)
						        	cmMapa.showInfo({data: K.tmp});
								this.top = (e.pageY + yOffset);
								this.left = (e.pageX + xOffset);
						        var left = this.left;
						        var width = $('#vtipCem').width() + 14;
						        if((left+width)>K.docWidth)
						            left = this.left - width;
						        var top = this.top;
						        var height = $('#vtipCem').height() + 8;
						        if((top +height )>K.docHeight)
						            top = this.top - height*2;
								$("#vtipCem").css("top", top+"px").css("left", left+"px");
							}
						});
					}
					K.unblock({$element: $('#pageWrapperMain')});
				},'json');
			}
		});
		$('#pageWrapperMain').layout();
	},
	showInfo: function(p){
		var content = "";
		if(p.data.pabellon!=null){
			content = "<h3>Pabell&oacute;n</h3><br /><span>"+p.data.nomb+' '+p.data.num+"</span>";
		}else{
			if(p.data.nicho!=null){
				content = "<h3>Nicho "+p.data.nicho.tipo+"</h3><br /><span>Fila "+p.data.nicho.fila+" - N&uacute;mero "+p.data.nicho.num+"</span>";
			}else if(p.data.mausoleo!=null){
				content = "<h3>Mausoleo</h3><br /><span>"+p.data.mausoleo.denominacion+"</span>";
			}else if(p.data.tumba!=null){
				content = "<h3>Tumba "+p.data.tumba.denominacion+"</h3>";
			}else if(p.data.osario!=null){
				content = "<h3>Osario</h3>";
			}else{
				content = '<h3>Cuadrante '+p.data.cm_nomb+'</h3>';
			}
		}
		$('body').append( '<p id="vtipCem" class="ui-widget ui-widget-content ui-corner-all" style="line-height: 12px;"></p>' );
		$('#vtipCem').append( content );
	},
	loadCuad: function(p){
		$('#vtipCem').remove();
		$mainPanel.find('[name=btnEditar],[name=btnHome]').show();
		$mainPanel.find('[name=lienzo]').remove();
		$mainPanel.find('[name=btnEditar]').data('data',p);
		$.post('cm/mapa/lista','_id='+p._id.$id,function(data){
			$mainPanel.append('<div name="lienzo">');
			var $lienzo = $mainPanel.find('[name=lienzo]');
			$('#mainPanel [name=lienzo]').css({'background-image':'url("ci/files/get?id='+p._id.$id+'")','width': p.metadata.width+'px','height': p.metadata.height+'px'});
			$lienzo.css({
				'background-image':'url("ci/files/get?id='+p._id.$id+'")',
				'width': p.metadata.width+'px',
				'height': p.metadata.height+'px'
			});
			$lienzo.svg();
			var svg = $lienzo.svg('get');
			if(data.items!=null){
				xOffset = -10;
				yOffset = 10;
				for(var i=0; i<data.items.length; i++){
					var color = 'white';
					var params_poly = {stroke: 'blue', strokeWidth: 2, id:'lienzo-'+i};
					if(p.espa_select){
						if(p.espa_select==data.items[i]._id.$id){
							params_poly.fill = 'black';
						}else params_poly['fill-opacity'] = '0';
					}else params_poly['fill-opacity'] = '0';
					svg.polygon(data.items[i].coordenadas, params_poly);
					$lienzo.find('#lienzo-'+i).attr('class','polygon').click(function(){
						var data = $(this).data('data');
						if(data.pabellon!=null){
							cmMapa.loadPabe(data);
						}else{
							if(data.nicho!=null){
								cmEspa.windowDetailsNicho({_id: data._id.$id,nomb: data.nomb,data: data});
							}else if(data.mausoleo!=null){
								cmEspa.windowDetailsMauso({_id: data._id.$id,nomb: data.nomb,data: data});
							}else if(data.tumba!=null){
								cmEspa.windowDetailsTumba({_id: data._id.$id,nomb: data.nomb,data: data});
							}
						}
					}).data('data',data.items[i]).bind({
						mouseenter: function(e){
							this.top = (e.pageY + yOffset);
							this.left = (e.pageX + xOffset);
							cmMapa.showInfo({data: $(this).data('data')});
							$('#vtipCem').css("top", this.top+"px").css("left", this.left+"px").fadeIn("fast");
						},
						mouseleave: function(){
							$("#vtipCem").fadeOut("slow").remove();
						},
						mousemove: function(e){
							this.top = (e.pageY + yOffset);
							this.left = (e.pageX + xOffset);
					        var left = this.left;
					        var width = $('#vtipCem').width() + 14;
					        if((left+width)>K.docWidth)
					            left = this.left - width;
					        var top = this.top;
					        var height = $('#vtipCem').height() + 8;
					        if((top +height )>K.docHeight)
					            top = this.top - height*2;
							$("#vtipCem").css("top", top+"px").css("left", left+"px");
						}
					});
				}
			}
			K.unblock({$element: $('#pageWrapperMain')});
		},'json');
	},
	edit: function(p){
		new K.Window({
			id: 'windowMapaEdit',
			title: 'Editar Mapa',
			contentURL: 'cm/mapa/edit',
			icon: 'ui-icon-image',
			width: 600,
			height: 500,
			minimizable: false,
			onClose: function(){
				cmMapa.loadCuad(p);
			},
			onContentLoaded: function(){
				p.$w = $('#windowMapaEdit');
				K.block({$element: p.$w});
				K.maximizeWindow(p.$w.attr('id'));
				var imagen = p._id.$id;
				p.$w.find('#p').css({
					'height': p.metadata.height+'px',
					'width': p.metadata.width+'px'
				});
				p.$w.find('#p').css('background-image',"url('ci/files/get?id="+imagen+"')");
				p.$w.find('#p').css('background-repeat','no-repeat');
				var puntos = [],
				i = 0;
				p.$w.find('#p').svg();
				var svg = p.$w.find('#p').svg('get');
				function clickarray(){
					p.$w.find("#p").unbind('click').click(function (e) {
						drawingpix = $('<span class="tempDraw">').css({
				        	"background-color": "#FF00FF",
					        "width": "3px",
					        "height": "3px",
					        "position": "absolute",
					        "top": "-50px",
					        "left": "-50px",
					        "z-index": "99999"
				        }).hide();
				        $(document.body).append(drawingpix);
				        drawingpix.css({
				            top: e.pageY,
				            left: e.pageX 
				        }).show();
				    	var offset = p.$w.find('#p').offset();
				    	var ptos = [];
				    	p.$w.find('#status2').html(e.pageX +', '+ e.pageY);
				        ptos[0] = e.pageX - offset.left;
				        ptos[1] = e.pageY - offset.top; 
				        puntos.push(ptos);
				        clickarray();
				     });
				}
				clickarray();
				var deshacer = function deshacer(){
					i--;
					var nodo =  document.getElementById(i);
					if(nodo!=null){
						svg.remove(nodo);
					}
					else i = 0;
				};
				p.$w.find('[name=btnPabe]').click(function(){
					if(puntos.length > 0){
						if(puntos.length > 2){
							svg.polygon(puntos, {fill: 'white',stroke: 'blue', strokeWidth: 2, id:i});
							p.$w.find("#"+i).attr('class','polygon').bind({
								mouseenter: function(e){
									this.top = (e.pageY + yOffset);
									this.left = (e.pageX + xOffset);
									cmEspa.showInfo({data: $(this).data('data')});
									$('#vtipCem').css("top", this.top+"px").css("left", this.left+"px").fadeIn("fast");
								},
								mouseleave: function(){
									$("#vtipCem").fadeOut("slow").remove();
								},
								mousemove: function(e){
									this.top = (e.pageY + yOffset);
									this.left = (e.pageX + xOffset);
									var left = this.left;
									var width = $('#vtipCem').width() + 14;
									if((left+width)>K.docWidth)
									left = this.left - width;
									var top = this.top;
									var height = $('#vtipCem').height() + 8;
									if((top +height )>K.docHeight)
									top = this.top - height*2;
									$("#vtipCem").css("top", top+"px").css("left", left+"px");
								}
							});
							var tmp_puntos = puntos;
							cmPabe.windowSelect({
								filter: [
								    {nomb: 'imagen',value: {$exists: false}},
									{nomb: 'sector',value: p.cm_nomb}
								],
								cancel: deshacer,
								callback: function(espa){
									K.clearNoti();
									var data = {
										_id: espa._id.$id,
										coordenadas: tmp_puntos,
										imagen: imagen
									};
									K.sendingInfo();
									$.post('cm/pabe/save',data,function(pabe){
										K.clearNoti();
										K.notification({
											title: ciHelper.titleMessages.regiAct,
											text: 'El espacio fue actualizado y localizado en el mapa!'
										});
									},'json');
								}
							});
							i++;
							puntos = [];
					    	$(document.body).find('.tempDraw').remove();
					    	clickarray();
						}
						else{
							K.notification({title: ciHelper.titleMessages.infoReq,text: "Debe ingresar como m&iacute;nimo tres coordenadas",type:"error"});
						}
			    	}
					else{
						K.notification({title: ciHelper.titleMessages.infoReq,text: "Diagrame el Espacio!",type:"error"});
						puntos = new Array;
				    	$(document.body).find('.tempDraw').remove();
				    	clickarray();
					}
				}).button({icons: {primary: 'ui-icon-tag'}});
				p.$w.find('[name=btnCrear]').click(function(){
					if(puntos.length > 0){
						if(puntos.length > 2){
							svg.polygon(puntos, {fill: 'white',stroke: 'blue', strokeWidth: 2, id:i});
							p.$w.find("#"+i).attr('class','polygon').bind({
								mouseenter: function(e){
									this.top = (e.pageY + yOffset);
									this.left = (e.pageX + xOffset);
									//$('body').append( '<p id="vtipCem" class="ui-widget ui-widget-content ui-corner-all">aaaaaaa<br />basdabsdñas<br/>asdasdasdasdsad</p>' );
									cmEspa.showInfo({data: $(this).data('data')});
									$('#vtipCem').css("top", this.top+"px").css("left", this.left+"px").fadeIn("fast");
								},
								mouseleave: function(){
									$("#vtipCem").fadeOut("slow").remove();
								},
								mousemove: function(e){
									this.top = (e.pageY + yOffset);
									this.left = (e.pageX + xOffset);
									//Left
									var left = this.left;
									var width = $('#vtipCem').width() + 14;
									if((left+width)>K.docWidth)
									left = this.left - width;
									//Top
									var top = this.top;
									var height = $('#vtipCem').height() + 8;
									if((top +height )>K.docHeight)
									top = this.top - height*2;
									$("#vtipCem").css("top", top+"px").css("left", left+"px");
								}
							});
							var tmp_puntos = puntos;
							cmEspa.windowSelect({
								filter: [
								    {nomb: 'imagen',value: {$exists: false}},
								    {nomb: 'nicho',value: {$exists: false}},
								    {nomb: 'sector',value: p.cm_nomb}
								],
								cancel: deshacer,
								callback: function(espa){
									K.clearNoti();
									var data = {
										_id: espa._id.$id,
										coordenadas: tmp_puntos,
										imagen: imagen
									};
									K.sendingInfo();
									$.post('cm/espa/save',data,function(){
										K.clearNoti();
										K.notification({
											title: ciHelper.titleMessages.regiAct,
											text: 'El espacio fue actualizado y localizado en el mapa!'
										});
									});
								}
							});
							i++;
							puntos = new Array;
					    	$(document.body).find('.tempDraw').remove();
					    	clickarray();
						}
						else{
							K.notification({title: ciHelper.titleMessages.infoReq,text: "Debe ingresar como m&iacute;nimo tres coordenadas!",type:"error"});
						}
			    	}
					else{
						K.notification({title: ciHelper.titleMessages.infoReq,text: "Diagrame el Espacio!",type:"error"});
						puntos = new Array;
				    	$(document.body).find('.tempDraw').remove();
				    	clickarray();
					}
				}).button({icons: {primary: 'ui-icon-tag'}});
				p.$w.find('[name=btnDesa]').click(function(){
					$(document.body).find('.tempDraw').remove();
					puntos = [];
				}).button({icons: {primary: 'ui-icon-arrowreturnthick-1-w'}});
				$.post('cm/mapa/lista',{_id: imagen},function(data){
					if(data.items!=null){
						xOffset = -10;
						yOffset = 10;
						for(var i=0; i<data.items.length; i++){
							svg.polygon(data.items[i].coordenadas, {'fill-opacity': '0',stroke: 'blue', strokeWidth: 2, id:'d-'+i});
							p.$w.find('#d-'+i).attr('class','polygon').data('data',data.items[i]).bind({
								mouseenter: function(e){
									this.top = (e.pageY + yOffset);
									this.left = (e.pageX + xOffset);
									cmMapa.showInfo({data: $(this).data('data')});
									$('p#vtipCem').css("top", this.top+"px").css("left", this.left+"px").fadeIn("fast");
								},
								mouseleave: function(){
									$("p#vtipCem").fadeOut("slow").remove();
								},
								mousemove: function(e){
									this.top = (e.pageY + yOffset);
									this.left = (e.pageX + xOffset);
							        var left = this.left;
							        var width = $('#vtipCem').width() + 14;
							        if((left+width)>K.docWidth)
							            left = this.left - width;
							        var top = this.top;
							        var height = $('#vtipCem').height() + 8;
							        if((top +height )>K.docHeight)
							            top = this.top - height*2;
									$("p#vtipCem").css("top", top+"px").css("left", left+"px");
								}
							}).data('index',i);
							if(data.items[i].pabellon==true){
								p.$w.find('#d-'+i).click(function(){
									cmMapa.windowEditPabe({$parent: p.$w,data: $(this).data('data'),index: $(this).data('index')});
									setTimeout("$('#windowMapaEdit [name=btnDesa]').click()",100);
								});
							}else{
								p.$w.find('#d-'+i).click(function(){
									K.notification({title: 'Espacio no permitido',text: 'No puede invadir el terreno de otro espacio asignado!',type: 'error'});
									setTimeout("$('#windowMapaEdit [name=btnDesa]').click()",100);
								});
							}
						}
					}
					K.unblock({$element: p.$w});
				},'json');
			}
		});
	},
	windowEditPabe: function(p){
		if(p.data.pabellon_imagen!=null){
			new K.Modal({
				id: 'windowEditPabellon',
				title: 'Editar Pabell&oacute;n '+p.data.nomb,
				icon: 'ui-icon-bookmark',
				minimizable: false,
				contentURL: 'cm/pabe/edit',
				onClose: function(){
					$(document.body).find('.tempDraw').remove();
					K.notification({title: 'Pabell&oacute;n actualizado',text: 'Edici&oacute;n de pabell&oacute;n realizada con &eacute;xito!'});
				},
				onContentLoaded: function(){
					p.$w = $('#windowEditPabellon');
					K.block({$element: p.$w});
					K.maximizeWindow(p.$w.attr('id'));
					$.post('ci/files/metadata','id='+p.data.pabellon_imagen.$id,function(metadata){
						p.$w.find('#pabe').css({
							'height': metadata.height+'px',
							'width': metadata.width+'px'
						});
						p.$w.find('#pabe').css('background-image',"url('ci/files/get?id="+p.data.pabellon_imagen.$id+"')");
						p.$w.find('#pabe').css('background-repeat','no-repeat');
						var puntos = new Array;
						var i = 0;
						p.$w.find('#pabe').svg();
						var svg = p.$w.find('#pabe').svg('get');
						p.$w.find('svg').css({
							'height': metadata.height+'px',
							'width': metadata.width+'px'
						});
						function clickarray(){
							p.$w.find("#pabe").unbind('click').click(function (e) {
								drawingpix = $('<span class="tempDraw">').css({
						        	"background-color": "#FF00FF",
							        "width": "3px",
							        "height": "3px",
							        "position": "absolute",
							        "top": "-50px",
							        "left": "-50px",
							        "z-index": "99999"
						        }).hide();
						        $(document.body).append(drawingpix);
						        drawingpix.css({
						            top: e.pageY,
						            left: e.pageX 
						        }).show();
						    	var offset = p.$w.find('#pabe').offset();
						    	var ptos = new Array;
						    	p.$w.find('#status2').html(e.pageX +', '+ e.pageY);
						        ptos[0] = e.pageX - offset.left;
						        ptos[1] = e.pageY - offset.top; 
						        puntos.push(ptos);
						        clickarray();
						     });
						}
						clickarray();
						var deshacer = function deshacer(){
							i--;
							var nodo =  document.getElementById('pabNic'+i);
							if(nodo!=null){
								svg.remove(nodo);
							}
							else i = 0;
						};
						p.$w.find('[name=btnCrear]').click(function(){
							if(puntos.length > 0){
								if(puntos.length > 2){
									svg.polygon(puntos, {'fill-opacity': '0',stroke: 'blue', strokeWidth: 2, id:'pabNic'+i});
									p.$w.find("#pabNic"+i).attr('class','polygon').bind({
										mouseenter: function(e){
											this.top = (e.pageY + yOffset);
											this.left = (e.pageX + xOffset);
											cmMapa.showInfo({data: $(this).data('data')});
											$('#vtipCem').css("top", this.top+"px").css("left", this.left+"px").fadeIn("fast");
										},
										mouseleave: function(){
											$("#vtipCem").fadeOut("slow").remove();
										},
										mousemove: function(e){
											this.top = (e.pageY + yOffset);
											this.left = (e.pageX + xOffset);
											//Left
											var left = this.left;
											var width = $('#vtipCem').width() + 14;
											if((left+width)>K.docWidth)
											left = this.left - width;
											//Top
											var top = this.top;
											var height = $('#vtipCem').height() + 8;
											if((top +height )>K.docHeight)
											top = this.top - height*2;
											$("#vtipCem").css("top", top+"px").css("left", left+"px");
										}
									});
									p.$w.find('#pabNic'+i).click(function(){
										K.notification({title: 'Espacio no permitido',text: 'No puede invadir el terreno de otro espacio asignado!',type: 'error'});
										setTimeout("$('#windowMapaEdit [name=btnDesa]').click()",100);
									});
									var tmp_puntos = puntos,
									tmp_i = i;
									cmEspa.windowSelect({
										filter: [
										    {nomb: 'nicho',value: {$exists: true}},
										    {nomb: 'imagen',value: {$exists: false}},
										    {nomb: 'nicho.pabellon._id',value: p.data._id.$id}
										],
										cancel: deshacer,
										callback: function(espa){
											K.clearNoti();
											var data = {
												_id: espa._id.$id,
												coordenadas: tmp_puntos,
												imagen: p.data.pabellon_imagen.$id
											};
											K.sendingInfo();
											$.post('cm/espa/save',data,function(rpta){
												K.clearNoti();
												K.notification({
													title: ciHelper.titleMessages.regiAct,
													text: 'El espacio fue actualizado y localizado en el mapa!'
												});
												p.$w.find("#pabNic"+tmp_i).data('data',rpta);
											},'json');
										}
									});
									i++;
									puntos = new Array;
							    	$(document.body).find('.tempDraw').remove();
							    	clickarray();
								}else{
									K.notification({title: ciHelper.titleMessages.infoReq,text: "Debe ingresar como m&iacute;nimo tres coordenadas!",type:"error"});
								}
					    	}else{
								K.notification({title: ciHelper.titleMessages.infoReq,text: "Diagrame el Espacio!",type:"error"});
								puntos = new Array;
						    	$(document.body).find('.tempDraw').remove();
						    	clickarray();
							}
						}).button({icons: {primary: 'ui-icon-tag'}});
						p.$w.find('[name=btnDesa]').click(function(){
							$(document.body).find('.tempDraw').remove();
							puntos = new Array;
						}).button({icons: {primary: 'ui-icon-arrowreturnthick-1-w'}});
						$.post('cm/mapa/lista','_id='+p.data.pabellon_imagen.$id,function(data){
							if(data.items!=null){
								xOffset = -10;
								yOffset = 10;
								for(var i=0; i<data.items.length; i++){
									svg.polygon(data.items[i].coordenadas, {'fill-opacity': '0',stroke: 'blue', strokeWidth: 2, id:'dP-'+i});
									p.$w.find('#dP-'+i).attr('class','polygon').data('data',data.items[i]).bind({
										mouseenter: function(e){
											this.top = (e.pageY + yOffset);
											this.left = (e.pageX + xOffset);
											cmMapa.showInfo({data: $(this).data('data')});
											$('p#vtipCem').css("top", this.top+"px").css("left", this.left+"px").fadeIn("fast");
										},
										mouseleave: function(){
											$("p#vtipCem").fadeOut("slow").remove();
										},
										mousemove: function(e){
											this.top = (e.pageY + yOffset);
											this.left = (e.pageX + xOffset);
											//Left
									        var left = this.left;
									        var width = $('#vtipCem').width() + 14;
									        if((left+width)>K.docWidth)
									            left = this.left - width;
									        //Top
									        var top = this.top;
									        var height = $('#vtipCem').height() + 8;
									        if((top +height )>K.docHeight)
									            top = this.top - height*2;
											$("p#vtipCem").css("top", top+"px").css("left", left+"px");
										}
									});
									p.$w.find('#dP-'+i).click(function(){
										K.notification({title: 'Espacio no permitido',text: 'No puede invadir el terreno de otro espacio asignado!',type: 'error'});
										setTimeout("$('#windowMapaEdit [name=btnDesa]').click()",100);
									});
								}
							}
							K.unblock({$element: p.$w});
						},'json');
					},'json');
				}
			});
		}else{
			new K.Modal({
				id: 'windowCmUploadPabe',
				title: 'Subir imagen de pabell&oacute;n '+p.data.nomb,
				icon: 'ui-icon-folder',
				contentURL: 'cm/pabe/upload',
				width: 470,
				height: 220,
				buttons: {
					'Actualizar Mapa': function(){
						K.clearNoti();
						var data = {
							_id: p.data._id.$id,
							pabellon_imagen: p.$w.find('[name=foto]').data('id')
						};
						if(data.pabellon_imagen==null){
							return K.notification({
								title: ciHelper.titleMessages.infoReq,
								text: 'Debe seleccionar una imagen para el pabell&oacute;n!',
								type: 'error'
							});
						}
						K.sendingInfo();
						$.post('cm/pabe/save',data,function(data){
							K.notification({
								title: ciHelper.titleMessages.regiAct,
								text: 'El pabell&oacute;n fue actualizado con &eacute;xito!'
							});
							data.pabellon = true;
							p.$parent.find('#d-'+p.index).data('data',data);
							K.closeWindow(p.$w.attr('id'));
						},'json');
					},
					'Cancelar': function(){
						K.closeWindow(p.$w.attr('id'));
					}
				},
				onClose: function(){ p = null; },
				onContentLoaded: function(){
					p.$w = $('#windowCmUploadPabe');
					var uploader = new qq.FileUploader({
						element: document.getElementById('buttonUpload'),
						action: 'ci/files/upload',
						debug: true,
						sizeLimit: 2097152,
						allowedExtensions: ['jpg','gif','png'],
						fieldFile: 'foto',
						onSubmit: function(){
					    	p.$w.find('.img-picture').fadeTo("slow", 0.33);
						},
						onComplete: function(id, fileName, responseJSON){
							p.$w.find('[name=foto]').val(responseJSON.file).data('id',responseJSON.id.$id);
							p.$w.find('.img-picture').fadeTo("slow", 1).attr('src','ci/files/get?id='+responseJSON.id.$id);
						}
					});
					p.$w.find('.picture-box').hover(function(){
						p.$w.find('.changepicture').show();
					},function(){
						p.$w.find('.changepicture').hide();
					}).click(function(){
						p.$w.find('[name=file]').click();
					});
				}
			});
		}
	},
	loadPabe: function(p){
		new K.Modal({
			id: 'windowDetailsPabellon',
			title: 'Pabell&oacute;n '+p.nomb,
			icon: 'ui-icon-bookmark',
			minimizable: false,
			contentURL: 'cm/pabe/details',
			onClose: function(){ p = null; },
			onContentLoaded: function(){
				p.$w = $('#windowDetailsPabellon');
				K.block({$element: p.$w});
				K.maximizeWindow(p.$w.attr('id'));
				$.post('ci/files/metadata','id='+p.pabellon_imagen.$id,function(metadata){
					p.$w.find('#viewPabe').css({
						'height': metadata.height+'px',
						'width': metadata.width+'px'
					});
					p.$w.find('#viewPabe').css('background-image',"url('ci/files/get?id="+p.pabellon_imagen.$id+"')");
					p.$w.find('#viewPabe').css('background-repeat','no-repeat');
					var puntos = new Array;
					p.$w.find('#viewPabe').svg();
					var svg = p.$w.find('#viewPabe').svg('get');
					p.$w.find('svg').css({
						'height': metadata.height+'px',
						'width': metadata.width+'px'
					});
					$.post('cm/mapa/lista','_id='+p.pabellon_imagen.$id,function(data){
						if(data.items!=null){
							xOffset = -10;
							yOffset = 10;
							for(var i=0; i<data.items.length; i++){
								var color = 'white';
								var params_poly = {stroke: 'blue', strokeWidth: 2, id:'dP-'+i};
								if(p.espa_select){
									if(p.espa_select==data.items[i]._id.$id){
										params_poly.fill = 'black';
									}else params_poly['fill-opacity'] = '0';
								}else params_poly['fill-opacity'] = '0';
								svg.polygon(data.items[i].coordenadas, params_poly);
								p.$w.find('#dP-'+i).attr('class','polygon').data('data',data.items[i]).bind({
									mouseenter: function(e){
										this.top = (e.pageY + yOffset);
										this.left = (e.pageX + xOffset);
										cmMapa.showInfo({data: $(this).data('data')});
										$('p#vtipCem').css("top", this.top+"px").css("left", this.left+"px").fadeIn("fast");
									},
									mouseleave: function(){
										$("p#vtipCem").fadeOut("slow").remove();
									},
									mousemove: function(e){
										this.top = (e.pageY + yOffset);
										this.left = (e.pageX + xOffset);
								        var left = this.left;
								        var width = $('#vtipCem').width() + 14;
								        if((left+width)>K.docWidth)
								            left = this.left - width;
								        var top = this.top;
								        var height = $('#vtipCem').height() + 8;
								        if((top +height )>K.docHeight)
								            top = this.top - height*2;
										$("p#vtipCem").css("top", top+"px").css("left", left+"px");
									}
								});
								p.$w.find('#dP-'+i).click(function(){
									var data = $(this).data('data');
									cmEspa.windowDetailsNicho({_id: data._id.$id,nomb: data.nomb,data: data});
								});
							}
						}
						K.unblock({$element: p.$w});
					},'json');
				},'json');
			}
		});
	}
};
define(
	['cm/pabe','cm/espa','cm/ocup','cm/prop','cm/oper'],
	function(cmPabe,cmEspa,cmOcup,cmProp,cmOper){
		return cmMapa;
	}
);