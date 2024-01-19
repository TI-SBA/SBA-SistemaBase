/*************************************************************************
Tramite Documentario: Reportes */
tdRepo = {
	init: function(){
		K.initMode({
			mode: 'td',
			action: 'tdRepo',
			titleBar: {
				title: 'Reportes'
			}
		});	
		new K.Panel({
			id: 'mainPanel',
			contentURL: 'td/repo',
			onContentLoaded: function(){
				$('#No-Results').hide();
				$mainPanel = $('#mainPanel');
				$mainPanel.find('.ui-layout-west').css('padding','0px').find('a').bind('click',function(event){
					event.preventDefault();
					var $anchor = $(this);
					$('#pageWrapperMain .ui-layout-center').scrollTo( $('#'+$anchor.attr('name')), 800 );
				}).eq(0).click().find('ul').addClass("ui-state-highlight");
				$section1 = $mainPanel.find('#section1');
				$section1.find('[name=fec]').html(ciHelper.dateFormatNowLong);
				$section1.find('[name=resp]').html(mgEnti.formatName(K.session.enti));
				if(K.session.enti.roles.trabajador.organizacion!=null)
					$section1.find('[name=area]').html(K.session.enti.roles.trabajador.organizacion.nomb);
				var tmp = new Date();
				$section1.find('[name=inicio]').datepicker( {
			        dateFormat: 'MM yy',
			        onClose: function(dateText, inst) { 
			            var month = $("#ui-datepicker-div .ui-datepicker-month :selected").val();
			            var year = $("#ui-datepicker-div .ui-datepicker-year :selected").val();
			            $(this).data('mes',month).data('ano',year);
			            $(this).val($.datepicker.formatDate('MM yy', new Date(year, month, 1)));
			        }
			    }).focus(function(){
			    	$('.ui-datepicker-calendar').css('display','none');
			    }).val(ciHelper.meses[tmp.getMonth()]+' '+tmp.getFullYear())
			    .data('mes',+tmp.getMonth())
			    .data('ano',tmp.getFullYear());
				$section1.find('[name=btnImprimir]').click(function(){	
					K.windowPrint({
						id: 'winPrintRepTD',
						title: 'Imprimir Reporte de Tr&aacute;mite Documentario',
						url: 'td/repo/print?a='+$section1.find('[name=inicio]').data('ano')+'&m='+$section1.find('[name=inicio]').data('mes')+'&c='+$section1.find('[name=periodo] option:selected').val()
					});
				}).button({icons: {primary: 'ui-icon-print'}});
				$section2 = $mainPanel.find('#section2');
				var tmp = new Date();
				$section2.find('[name=desde]').datepicker( {
			        dateFormat: 'yy-mm-dd'   
			    });
				$section2.find('[name=hasta]').datepicker( {
			        dateFormat: 'yy-mm-dd'   
			    });
				$section2.find('[name=rbtnVenc]').buttonset();	
				$section2.find('[name=rbtnNoaten]').buttonset();
				$section2.find('[name=btnOfi]').click(function(){
					ciSearch.windowSearchOrga_tramite({callback: function(data){
						$section2.find('[name=oficina]').html(data.nomb).data('data',data._id.$id);
					}});
				}).button({icons: {primary: 'ui-icon-search'},text:false});
				$section2.find('[name=btnUsu]').click(function(){
					ciSearch.windowSearchEnti({callback: function(data){
						$section2.find('[name=usuario]').html(mgEnti.formatName(data)).data('data',data._id.$id);
					}});
				}).button({icons: {primary: 'ui-icon-search'},text:false});
				$section2.find('[name=btnProc]').click(function(){
					ciSearch.windowSearchTupa({$window: $section2,callback: function(data){
						$section2.find('[name=proc]').html(data.data.modalidades[data.index].descr).data('data',data.data._id.$id);
					},textSearch:""});
				}).button({icons: {primary: 'ui-icon-search'},text:false});
				$section2.find('[name=btnImprimir]').click(function(){
					var usuario = $section2.find('[name=usuario]').data('data');
					if(usuario==null)usuario="";
					var oficina = $section2.find('[name=oficina]').data('data');
					if(oficina==null)oficina="";
					var proc = $section2.find('[name=proc]').data('data');
					if(proc==null)proc = "";
					var venc = $section2.find('[name=venc]:checked').val();
					var noaten = $section2.find('[name=noaten]:checked').val();
					K.windowPrint({
						id: 'winPrintRepTDAll',
						title: 'Imprimir Reporte de Tr&aacute;mite Documentario',
						url: 'td/repo/print_all?desde='+$section2.find('[name=desde]').val()+'&hasta='+$section2.find('[name=hasta]').val()+'&usuario='+usuario+'&oficina='+oficina+'&proc='+proc+'&venc='+venc+'&noaten='+noaten+'&usuario_label='+$section2.find('[name=usuario]').html()+'&oficina_label='+$section2.find('[name=oficina]').html()+'&proc_label='+$section2.find('[name=proc]').html()+'&tipo='+$section2.find('[name=tipo] :selected').val()+'&estado='+$section2.find('[name=estado] :selected').val()
					});
				}).button({icons: {primary: 'ui-icon-print'}});
				$section3 = $mainPanel.find('#section3');
				var tmp = new Date();
				$section3.find('[name=inicio]').datepicker( {
			        dateFormat: 'MM yy',
			        onClose: function(dateText, inst) { 
			            var month = $("#ui-datepicker-div .ui-datepicker-month :selected").val();
			            var year = $("#ui-datepicker-div .ui-datepicker-year :selected").val();
			            $(this).data('mes',month).data('ano',year);
			            $(this).val($.datepicker.formatDate('MM yy', new Date(year, month, 1)));
			        }
			    }).focus(function(){
			    	$('.ui-datepicker-calendar').css('display','none');
			    }).val(ciHelper.meses[tmp.getMonth()]+' '+tmp.getFullYear())
			    .data('mes',+tmp.getMonth())
			    .data('ano',tmp.getFullYear());
				$section3.find('[name=btnImprimir]').click(function(){
					/*K.windowPrint({
						id: 'winPrintRepTD',
						title: 'Imprimir Reporte de Tr&aacute;mite Documentario',
						url: 'td/repo/print_all_doc?'+
							'a='+$section3.find('[name=inicio]').data('ano')+
							'&m='+$section3.find('[name=inicio]').data('mes')+
							'&c='+$section3.find('[name=periodo] option:selected').val()
					});*/
					window.open('td/repo/print_all_doc?'+
							'a='+$section3.find('[name=inicio]').data('ano')+
							'&m='+$section3.find('[name=inicio]').data('mes')+
							'&c='+$section3.find('[name=periodo] option:selected').val());
				}).button({icons: {primary: 'ui-icon-print'}});
				$('#pageWrapperMain').layout();
				$mainPanel.layout({
					resizeWithWindow:	false,
					west__size:			200,
					west__closable:		false,
					west__resizable:	false,
					west__slidable:		false
				});
				$mainPanel.find('fieldset').height($mainPanel.height());
				/*---------------------Section 4 ----------------------*/
				$section4 = $mainPanel.find('#section4');
				var tmp = new Date();
				$section4.find('[name=desde]').datepicker( {
			        dateFormat: 'yy-mm-dd'   
			    });
				$section4.find('[name=hasta]').datepicker( {
			        dateFormat: 'yy-mm-dd'   
			    });
				$section4.find('[name=btnImprimir]').click(function(){
					K.windowPrint({
						id: 'winPrintRepTDAll',
						title: 'Imprimir Reporte de Tr&aacute;mite Documentario',
						url: 'td/repo/print_all_archivado?desde='+$section4.find('[name=desde]').val()+'&hasta='+$section4.find('[name=hasta]').val()+'&archivado='+$section4.find('[name=archivado]').val()
					});
				}).button({icons: {primary: 'ui-icon-print'}});
				
				
			}
		});
		
		/*$(window).resize(function(){
			$('#mainPanel').height($('#pageWrapperMain')+'px');
		}).resize();*/
		$('#pageWrapperMain').layout();
		K.unblock({$element: $('#pageWrapperMain')});
	}	
	};