/*******************************************************************************
Propinas */
peProp = {
	init: function(){
		K.initMode({
			mode: 'pe',
			action: 'peProp',
			titleBar: {
				title: 'Propinas'
			}
		});
		
		new K.Panel({
			id: 'mainPanel',
			contentURL: 'pe/prop',
			onContentLoaded: function(){
				$('#No-Results').hide();
				$mainPanel = $('#mainPanel');
				//$mainPanel.find('[name=buscar]').attr( 'placeholder' , 'Ingrese el nombre de sistema de pensi&oacute;n' ).width('250');
				$mainPanel.find('[name=obj]').html( 'practicante(s)' );
				$mainPanel.find("[name=moreresults]").css('float','right').button({icons: {primary: 'ui-icon-triangle-1-s'}});
				$mainPanel.resize(function(){
					$mainPanel.find('.grid:eq(1)').height(($mainPanel.height()-$mainPanel.find('.grid:eq(0)').height()-$('.div-bottom').outerHeight()-$('.div-bottom').height())+'px');
				}).resize();
				$mainPanel.find('.grid:eq(0)').css('overflow','hidden');
				$mainPanel.find('[name=periodo]').numeric().spinner({step: 1,min: 1900,max: 2100});;
				$mainPanel.find('[name=periodo]').parent().find('.ui-button').css('height','14px');
				var d = new Date();
				$mainPanel.find('[name=periodo]').val(d.getFullYear()); 
				$mainPanel.find('.ui-spinner-button').click(function() { 
					$('#mainPanel .gridBody').empty();
					peProp.loadData({
						page: 1,
						url: 'pe/prop/lista',
						bandeja:'B'
					}); 
				});
				$mainPanel.find('[name=mes]').change(function(){
					$('#mainPanel .gridBody').empty();
					peProp.loadData({
						page: 1,
						url: 'pe/prop/lista',
						bandeja:'B'
					}); 
				});
				$mainPanel.find('[name=organizacion]').val(K.session.enti.roles.trabajador.organizacion._id.$id);
				$mainPanel.find('[name=organomb]').html(K.session.enti.roles.trabajador.organizacion.nomb);
				if(1==1){
					$mainPanel.find('[name=FilOrga]').buttonset();
					$mainPanel.find('#rbtnOrgaSelect').click(function(){
						ciSearch.windowSearchOrga({callback: function(data){
							$mainPanel.find('[name=organizacion]').val(data._id.$id);
							$mainPanel.find('[name=organomb]').html(data.nomb);
							$('#mainPanel .gridBody').empty();
							peProp.loadData({
								page: 1,
								url: 'pe/prop/lista',
								bandeja:'B'
							});
						}});
					});
					$mainPanel.find('#rbtnOrgaX').click(function(){
							$mainPanel.find('[name=organizacion]').val(K.session.enti.roles.trabajador.organizacion._id.$id);
							$mainPanel.find('[name=organomb]').html(K.session.enti.roles.trabajador.organizacion.nomb);
							$('#mainPanel .gridBody').empty();
								peProp.loadData({
									page: 1,
									url: 'pe/prop/lista',
									bandeja:'B'
								});
					});
				}else{
					$mainPanel.find('[name=orgainput]').empty();
					$mainPanel.find('[name=organizacion]').val(K.session.enti.roles.trabajador.organizacion._id.$id);
					$mainPanel.find('[name=organomb]').html(K.session.enti.roles.trabajador.organizacion.nomb);
				}
				$mainPanel.find('.grid:eq(1)').scroll(function(){
					$mainPanel.find('.grid:eq(0)').scrollLeft($(this).scrollLeft());
				});
				$mainPanel.find('[name=btnEditar]').click(function(){
					$('#mainPanel .gridBody').empty();
					peProp.loadData({page: 1,url: 'pe/prop/lista',bandeja:'E'});
					$mainPanel.find('[name=btnGuardar]').show();
					$mainPanel.find('[name=btnEditar]').button( "option", "disabled", true );
					$mainPanel.find('[name=mes]').attr('disabled', 'disabled');
					$mainPanel.find('[name=periodo]').spinner("option", "disabled", true);
					$mainPanel.find('[name=FilOrga]').buttonset({disabled: true});
				}).button({icons: {primary: 'ui-icon-plusthick'}});
				$mainPanel.find('[name=btnGuardar]').click(function(){
					if($mainPanel.find('.gridBody .item').length>0){
						for(i=0;i<$mainPanel.find('.gridBody .item').length;i++){
							var data = new Object;
							if($mainPanel.find('.gridBody .item').eq(i).find('li').eq(1).data('update')==null){
								data.practicante = new Object;
								data.practicante = $mainPanel.find('.gridBody .item').eq(i).data('enti');
								data.periodo = new Object;
								data.periodo.ano = $('#mainPanel').find('[name=periodo]').val();
								data.periodo.mes = $('#mainPanel').find('[name=mes] :selected').val();
								data.organizacion = $mainPanel.find('.gridBody .item').eq(i).data('orga');
								/*data.trabajador = new Object;
								data.trabajador.tipo_enti = K.session.enti.tipo_enti;
								if(K.session.enti.tipo_enti=="E"){
									data.trabajador.nomb = K.session.enti.nomb;
								}else{
									data.trabajador.appat = K.session.enti.appat;
									data.trabajador.apmat = K.session.enti.apmat;
								}
								data.trabajador.cargo = new Object;
								data.trabajador.cargo._id = K.session.enti.roles.trabajador.cargo_clasif._id.$id;
								data.trabajador.cargo.nomb = K.session.enti.roles.trabajador.cargo_clasif.nomb;
								data.trabajador.cargo.organizacion = new Object;
								data.trabajador.cargo.organizacion._id = K.session.enti.roles.trabajador.organizacion._id.$id;
								data.trabajador.cargo.organizacion.nomb = K.session.enti.roles.trabajador.organizacion.nomb;*/
							}else{
								data._id = $mainPanel.find('.gridBody .item').eq(i).find('li').eq(1).data('update');
							}
							data.propina = $mainPanel.find('.gridBody .item input').eq(i).val();
							$.post('pe/prop/save',data,function(){
								
							});
						}
						$('#mainPanel .gridBody').empty();
						peProp.loadData({page: 1,url: 'pe/prop/lista',bandeja:'B'});
						$mainPanel.find('[name=btnEditar]').button( "option", "disabled", false );					
						$mainPanel.find('[name=btnGuardar]').hide();
						$mainPanel.find('[name=mes]').removeAttr('disabled');
						$mainPanel.find('[name=periodo]').spinner("option", "disabled", false);
						$mainPanel.find('[name=FilOrga]').buttonset({disabled: false});
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Propinas guardadas correctamente!'});
					}else{
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'No hay elementos para editar en la bandeja!',type: 'error'});
					}							
				}).button({icons: {primary: 'ui-icon-plusthick'}});
				$mainPanel.find('[name=btnImprimir]').click(function(){
					if($mainPanel.find('.gridBody .item').length>0){	
						var params = new Object;
						params.texto = $('.divSearch [name=buscar]').val();
						params.organizacion = $('#mainPanel').find('[name=organizacion]').val();
						params.organomb = $('#mainPanel').find('[name=organomb]').html();
						params.page_rows = 20;
					    params.page = (params.page) ? params.page : 1;
					    params.periodo = $('#mainPanel').find('[name=periodo]').val();
					    params.mes = $('#mainPanel').find('[name=mes] :selected').val();
						var url = "pe/prop/print?"+$.param(params);
						K.windowPrint({
							id:"windowPrintPeProp",
							title:"Reporte Practicantes",
							url:url
						});
					}else{
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'No se encontraron practicantes para generar el reporte!',type: 'error'});
					}
				}).button({icons: {primary: 'ui-icon-plusthick'}});
				peProp.loadData({page: 1,url: 'pe/prop/lista',bandeja:'B'});
			}
		});
		$('#pageWrapperMain').layout();
	},
	loadData: function(params){
		params.texto = $('.divSearch [name=buscar]').val();
		params.organizacion = $('#mainPanel').find('[name=organizacion]').val();
		params.page_rows = 20;
	    params.page = (params.page) ? params.page : 1;
	    params.periodo = $('#mainPanel').find('[name=periodo]').val();
	    params.mes = $('#mainPanel').find('[name=mes] :selected').val();
	    $.post(params.url, params, function(data){
			if ( data.paging.total_page_items > 0 ) { 
				for (i=0; i < data.paging.total_page_items; i++) {
					result = data.items[i];
					var $row = $('.gridReference','#mainPanel').clone();
					$li = $('li',$row);
					$li.eq(0).html( ciHelper.enti.formatName(result) );
					if(params.bandeja=='E'){
						$li.eq(1).html('<input type="text" name="prop" value="" size="8">' );
					}else{
						$li.eq(1).html("");
					}
					if(result.practicas!=null){
						if(params.bandeja=='E'){
							$li.eq(1).html('S./ <input type="text" name="prop" value="'+result.practicas.propina+'" size="7">').data('update',result.practicas._id.$id);
							$li.eq(2).html( ciHelper.enti.formatName(result.practicas.trabajador) );
							$li.eq(3).html( ciHelper.dateFormat(result.practicas.fecreg) );
						}else{
							$li.eq(1).html(result.practicas.propina);
							$li.eq(2).html( ciHelper.enti.formatName(result.practicas.trabajador) );
							$li.eq(3).html( ciHelper.dateFormat(result.practicas.fecreg) );
						}
					}else{
						if(params.bandeja=='E'){
							$li.eq(1).html('S./ <input type="text" name="prop" value="'+result.roles.practicante.propina+'" size="7">').data('update',null);
						}else{
							$li.eq(1).html("---");
						}
					}
					$row.wrapInner('<a class="item" href="javascript: void(0);" />');
					var entitosave={
							_id:result._id.$id,
							tipo_enti:result.tipo_enti,
							nomb:result.nomb,
							appat:result.appat,
							apmat:result.apmat
					};
					var orgatosave={
							_id:result.roles.practicante.organizacion._id.id,
							nomb:result.roles.practicante.organizacion.nomb
					};
					$row.find('a').data('id',result._id.$id).data('enti',entitosave).data('orga',orgatosave)
					.contextMenu("conMenPeSist", {
						onShowMenu: function(e, menu) {
							$(e.target).closest('.gridBody').find('ul').removeClass('ui-state-highlight');
							$(e.target).closest('.item').find('ul').addClass('ui-state-highlight');
							$(e.target).closest('.item').click();
							K.tmp = $(e.target).closest('.item');
							//if(K.tmp.data('estado')=='H') $('#conMenPeSist_hab',menu).remove();
							//else $('#conMenPeSist_edi,#conMenPeSist_des,#conMenPeSist_act',menu).remove();
							return menu;
						},
						bindings: {
							'conMenPeSist_ver': function(t) {
								peSist.windowDetails({id: K.tmp.data('id'),nomb: K.tmp.find('li:eq(2)').html()});
							}
						}
					});
		        	$("#mainPanel .gridBody").append( $row.children() );
					ciHelper.gridButtons($("#mainPanel .gridBody"));
		        }
		        count = $("#mainPanel .gridBody .item").length;
		        $('#No-Results').hide();
		        $('#Results [name=showing]').html( count );
		        $('#Results [name=founded]').html( data.paging.total_items );
		        $('#Results').show();
		        
		        $moreresults = $("[name=moreresults]").unbind();
		        if (parseFloat(data.paging.page) < parseFloat(data.paging.total_pages)) {
					$("#mainPanel .gridFoot").show();
					$moreresults.click( function(){
						$('#mainPanel .grid').scrollTo( $("#mainPanel .gridBody a:last"), 800 );
						params.page = parseFloat(data.paging.page) + 1;
						peProp.loadData(params);
						$(this).button( "option", "disabled", true );
					});
					$( "[name=moreresults]",'#mainPanel').button( "option", "disabled", false );
		        }else{
					$("#mainPanel .gridFoot").hide();
					$( "[name=moreresults]",'#mainPanel').button( "option", "disabled", true );
		        }
	      } else {
	        $('#No-Results').show();
	        $('#Results').hide();
	        $( "[name=moreresults]",'#mainPanel').button( "option", "disabled", true );
	      }
	      $('#mainPanel').resize();
	      K.unblock({$element: $('#pageWrapperMain')});
	    }, 'json');
	}
};