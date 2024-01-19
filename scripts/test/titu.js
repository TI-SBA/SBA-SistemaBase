/*******************************************************************************
Informacion del titular */
mgTitu = {
	init: function(data){
		if(data==null) data = {};
		K.loadMode({
			mode: 'mg',
			action: 'mgTitu',
			titleBar: {
				title: 'Informaci&oacute;n del Titular'
			},
			store: false,
			url: K.base+'test/titu',
			onContentLoaded: function($p){
				$p = $('#mainPanel');
				$p.find('.ui-layout-west').css('padding','0px').find('a').bind('click',function(event){
					event.preventDefault();
					var $anchor = $(this);
					$('#pageWrapperMain .ui-layout-center').scrollTo( $('#'+$anchor.attr('href')), 800 );
				}).eq(0).click();
				$p.find('.ui-layout-west').find('[name=btnGuardar]').click(function(){
					K.clearNoti();
					var data = new Object();
					$section1 = $p.find('#section1');
					data = new Object();
					data._id = $p.find('#section1').data('id');
					data.imagen = $section1.find('[name=foto]').data('id');
					data.nomb = $('[name=nomb]',$section1).val();
					data.docident = new Array();
					data.docident[0] = new Object;
					data.docident[0].tipo = 'RUC';
					data.docident[0].num = $('[name=ruc]',$section1).val();
					if(data.docident[0].num==''){
						$('[name=ruc]',$section1).focus();
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar un RUC!',type: 'error'});
					}
					if(data.nomb==''){
						$('[name=nomb]',$section1).focus();
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar un nombre!',type: 'error'});
					}
					$section2 = $p.find('#section2');
					data.domicilios = new Array();
					for(var i=1; i<$('table',$section2).length; i++){
						var locales = new Object();
						locales._id = $('table',$section2).eq(i).find('[name=direc]').data('_id');
						locales.direccion = $('table',$section2).eq(i).find('[name=direc]').val();
						locales.ubig =  $('table',$section2).eq(i).find('[name=ubigeo]').val();
						locales.descr =  $('table',$section2).eq(i).find('[name=descr]').val();
						if(locales.direccion!=''){
              				data.domicilios.push(locales);
						}
					}
					$section3 = $p.find('#section3');
					data.telefonos = new Array();
					for(var i=1; i<$('table',$section3).length; i++){
						var telefonos = new Object();
						telefonos.num = $('table',$section3).eq(i).find('[name=val]').val();
						telefonos.descr = $('table',$section3).eq(i).find('input[name=descr]:last').val();
						if(telefonos.num!='') data.telefonos.push(telefonos);
					}
					$section4 = $p.find('#section4');
					data.emails = new Array();
					for(var i=1; i<$('table',$section4).length; i++){
						var contactoInternet = new Object();
						contactoInternet.direc = $('table',$section4).eq(i).find('[name=val]').val();
						if(!($('table',$section4).eq(i).find('[name=val]').email())&&$('table',$section4).eq(i).find('[name=val]').val()!=''){
							return K.notification({title: ciHelper.titleMessages.infoReq,text: 'E-mail no v&aacute;lido.',type: 'error'});
						}
						contactoInternet.tipo = 'email';
						contactoInternet.descr = $('table',$section4).eq(i).find('input[name=descr]:last').val();
						if(contactoInternet.direc!='') data.emails.push(contactoInternet);
					}
					$section5 = $p.find('#section5');
					data.urls = new Array();
					for(var i=1; i<$('table',$section5).length; i++){
						var contactoInternet = new Object();
						contactoInternet.direc = $('table',$section5).eq(i).find('[name=val]').val();
						if(!($('table',$section5).eq(i).find('[name=val]').url())&&$('table',$section5).eq(i).find('[name=val]').val()!=''){
							return K.notification({title: ciHelper.titleMessages.infoReq,text: 'URL no v&aacute;lida.',type: 'error'});
						}
						contactoInternet.tipo = 'url';
						contactoInternet.descr = $('table',$section5).eq(i).find('input[name=descr]:last').val();
						if(contactoInternet.direc!='') data.urls.push(contactoInternet);
					}
					K.clearNoti();
					K.sendingInfo();
					$p.find('#btnGuardar').button('disable');
					$.post(K.base+'test/titu/save',data,function(rpta){
						K.clearNoti();
						mgTitu.init(rpta.$set);
						K.notification({title: ciHelper.titleMessages.regiGua,text: 'Registro actualizado!'});
					},'json');
				}).button({icons: {primary: "ui-icon-disk"}});
				/* Section1 */
				$section1 = $('#section1');
				$section1.find('#ruc').numeric();
				$section1.find('#buttonUpload').attr('id','btnUploadInf');
				var uploader = new qq.FileUploader({
					element: document.getElementById('btnUploadInf'),
					action: 'ci/files/upload',
					debug: true,
					sizeLimit: 2097152,
					allowedExtensions: ['jpg','gif','png'],
					fieldFile: 'foto',
					onSubmit: function(){
						$section1.find('.img-picture').fadeTo("slow", 0.33);
					},
					onComplete: function(id, fileName, responseJSON){
						$section1.find('[name=foto]').val(responseJSON.file).data('id',responseJSON.id.$id);
						$section1.find('.img-picture').fadeTo("slow", 1).attr('src','ci/files/get?id='+responseJSON.id.$id);
					}
		        });
		        $section1.find('.picture-box').hover(function(){
		        	$section1.find('.changepicture').show();
		        },function(){
		        	$section1.find('.changepicture').hide();
		        }).click(function(){
		        	$section1.find('[name=file]').click();
		        });
		        /* Section2 */
				$('#section2').undelegate('[name=btnAgregar]').delegate('[name=btnAgregar]','click',function(){
					var $row = $('[name=row]','#section2').clone();
					$row.attr('name','');
					if($(this).parents('table').attr('name')!='row')
						$(this).remove();
					$row.show();
					$("fieldset","#section2").append( $row );
					$('[name=btnAgregar]',$row).button({icons: {primary: "ui-icon-plusthick"}});
					$('[name=btnEliminar]',$row).button({icons: {primary: "ui-icon-closethick"}});
				}).find('[name=btnAgregar]').click();
				$('#section2').undelegate('[name=btnEliminar]').delegate('[name=btnEliminar]','click',function(){
					var $table = $(this).closest('table');
					if($table.find('[name=direc]').data('_id')) return K.notification({text: 'La direcci&oacute;n seleccionada no puede ser eliminada!',type: 'error',layout: 'topLeft'});
					$table.remove();
					if($('table','#section2').length<2)
						$('table:last','#section2').find('[name=btnAgregar]').click();
					if($('[name=btnAgregar]','#section2').length<=1){
						$('table:last','#section2').find('[name=btnEliminar]').before("<button name='btnAgregar'>Agregar</button>&nbsp;");
						$('table:last','#section2').find('[name=btnAgregar]').button({icons: {primary: "ui-icon-plusthick"}});
					}
				});
				/* telefonos */
				$('#section3').undelegate('[name=btnAgregar]').delegate('[name=btnAgregar]','click',function(){
					var $row = $('[name=row]','#section3').clone();
					$row.attr('name','');
					if($(this).parents('table').attr('name')!='row')
						$(this).remove();
					$row.show();
					$row.find('.editableSelect').editableSelect();
					$('input[name=descr]:last',$row).data('id',$('select[name=descr]:last',$row).val());
					$("fieldset","#section3").append( $row );
					$('[name=btnAgregar]',$row).button({icons: {primary: "ui-icon-plusthick"}});
					$('[name=btnEliminar]',$row).button({icons: {primary: "ui-icon-closethick"}});
				}).find('[name=btnAgregar]').click();
				$('#section3').undelegate('[name=btnEliminar]').delegate('[name=btnEliminar]','click',function(){
					var $table = $(this).closest('table');
					$table.remove();
					if($('table','#section3').length<2)
						$('table:last','#section3').find('[name=btnAgregar]').click();
					if($('[name=btnAgregar]','#section3').length<=1){
						$('table:last','#section3').find('[name=tdBtn]').append("<button name='btnAgregar'>Agregar</button>");
						$('table:last','#section3').find('[name=btnAgregar]').button({icons: {primary: "ui-icon-plusthick"}});
					}
				});
				/* Correos */
				$('#section4').undelegate('[name=btnAgregar]').delegate('[name=btnAgregar]','click',function(){
					var $row = $('[name=row]','#section4').clone();
					$row.attr('name','');
					if($(this).parents('table').attr('name')!='row')
						$(this).remove();
					$row.show();
					$row.find('.editableSelect').editableSelect();
					$('input[name=descr]:last',$row).data('id',$('select[name=descr]:last',$row).val());
					$("fieldset","#section4").append( $row );
					$('[name=btnAgregar]',$row).button({icons: {primary: "ui-icon-plusthick"}});
					$('[name=btnEliminar]',$row).button({icons: {primary: "ui-icon-closethick"}});
				}).find('[name=btnAgregar]').click();
				$('#section4').undelegate('[name=btnEliminar]').delegate('[name=btnEliminar]','click',function(){
					var $table = $(this).closest('table');
					$table.remove();
					if($('table','#section4').length<2)
						$('table:last','#section4').find('[name=btnAgregar]').click();
					if($('[name=btnAgregar]','#section4').length<=1){
						$('table:last','#section4').find('[name=tdBtn]').append("<button name='btnAgregar'>Agregar</button>");
						$('table:last','#section4').find('[name=btnAgregar]').button({icons: {primary: "ui-icon-plusthick"}});
					}
				});
				/* Sitios */
				$('#section5').undelegate('[name=btnAgregar]').delegate('[name=btnAgregar]','click',function(){
					var $row = $('[name=row]','#section5').clone();
					$row.attr('name','');
					if($(this).parents('table').attr('name')!='row')
						$(this).remove();
					$row.show();
					$row.find('.editableSelect').editableSelect();
					$('input[name=descr]:last',$row).data('id',$('select[name=descr]:last',$row).val());
					$("fieldset","#section5").append( $row );
					$('[name=btnAgregar]',$row).button({icons: {primary: "ui-icon-plusthick"}});
					$('[name=btnEliminar]',$row).button({icons: {primary: "ui-icon-closethick"}});
				}).find('[name=btnAgregar]').click();
				$('#section5').undelegate('[name=btnEliminar]').delegate('[name=btnEliminar]','click',function(){
					var $table = $(this).closest('table');
					$table.remove();
					if($('table','#section5').length<2)
						$('table:last','#section5').find('[name=btnAgregar]').click();
					if($('[name=btnAgregar]','#section5').length<=1){
						$('table:last','#section5').find('[name=tdBtn]').append("<button name='btnAgregar'>Agregar</button>");
						$('table:last','#section5').find('[name=btnAgregar]').button({icons: {primary: "ui-icon-plusthick"}});
					}
				});
				/****       CARGAR DATA      ****/
				if(data.nomb!=null){
					if(mgTitu.loadData(data)){
						$('#pageWrapperMain').layout();
						$('#mainPanel').layout({
							west__size:			170,
							west__closable:		false,
							west__resizable:	false,
							west__slidable:		false
						});
						K.unblock({$element: $('#pageWrapperMain')});
					}
				}else{
					$.post(K.base+'test/titu/titular',function(data){
						if(mgTitu.loadData(data)){
							$('#pageWrapperMain').layout();
							$('#mainPanel').layout({
								west__size:			170,
								west__closable:		false,
								west__resizable:	false,
								west__slidable:		false
							});
							K.unblock({$element: $('#pageWrapperMain')});
						}
					},'json');
				}
			}
		});
		
		/*$(window).resize(function(){
			$('#mainPanel').height($('#pageWrapperMain')+'px');
		}).resize();
		
		$('#pageWrapperMain').layout();*/
	},
	loadData: function(data){
		if(data._id!=null) $('#section1').data('id',data._id.$id);
		$p.find("[name=ruc]").val(data.docident[0].num);
		$p.find("[name=nomb]").val(data.nomb);
		if(data.imagen!=null){
			if(data.imagen.$id!=null){
				$p.find('#section1 .img-picture').attr('src','ci/files/get?id='+data.imagen.$id);
				$p.find('#section1 [name=foto]').data('id',data.imagen.$id);
			}else{
				$p.find('#section1 .img-picture').attr('src','ci/files/get?id='+data.imagen);
				$p.find('#section1 [name=foto]').data('id',data.imagen);
			}
		}
		if(data.domicilios!=null)
		for(var i=0; i<data.domicilios.length; i++){
			if(i>0){ $('[name=btnAgregar]:last','#section2').click();}
			$('#section2 [name=direc]:last').val(data.domicilios[i].direccion);
			if(data.domicilios[i]._id!=null)
				$('#section2 [name=direc]:last').data('_id',data.domicilios[i]._id.$id);
			$('#section2 [name=descr]:last').val(data.domicilios[i].descr);
			$('#section2 [name=ubigeo]:last').val(data.domicilios[i].ubigeo);
		}
		if(data.telefonos!=null)
		for(var i=0; i<data.telefonos.length; i++){
			if(i>0){ $('[name=btnAgregar]:last','#section3').click();}
			$('#section3 [name=val]:last').val(data.telefonos[i].num);
			$('#section3 input[name=descr]:last').val(data.telefonos[i].descr).data('id','telf');
			$('#section3 select[name=descr]:last option').each(function(){
				if($(this).text()==data.telefonos[i].descr){
					$('#section3 input[name=descr]:last').val($(this).text());
					$('#section3 input[name=descr]:last').data('id',$(this).val());
					return 0;
				}
			});
		}
		if(data.emails!=null)
		for(var i=0; i<data.emails.length; i++){
			if(i>0){ $('[name=btnAgregar]:last','#section4').click();}
			$('#section4 [name=val]:last').val(data.emails[i].direc);
			$('#section4 input[name=descr]:last').val(data.emails[i].descr);
			$('#section4 select[name=descr]:last option').each(function(){
				if($(this).text()==data.emails[i].descr){
					$('#section4 input[name=descr]:last').val($(this).text());
					$('#section4 input[name=descr]:last').data('id',$(this).val());
					return 0;
				}
			});
		}
		if(data.urls!=null)
		for(var i=0; i<data.urls.length; i++){
			if(i>0){ $('[name=btnAgregar]:last','#section5').click();}
			$('#section5 [name=val]:last').val(data.urls[i].direc);
			$('#section5 input[name=descr]:last').val(data.urls[i].descr);
			$('#section5 select[name=descr]:last option').each(function(){
				if($(this).text()==data.urls[i].descr){
					$('#section5 input[name=descr]:last').val($(this).text());
					$('#section5 input[name=descr]:last').data('id',$(this).val());
					return 0;
				}
			});
		}
		return true;
	}
};