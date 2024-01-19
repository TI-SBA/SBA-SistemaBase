/*************************************************************************
ajustes inmuebles */
acAjusInmu = {
	init: function(){
		if($('#pageWrapper [child=ajus]').length<=0){
			var $p = $('#pageWrapperLeft');
			$.post('ac/navg/ajus',function(data){
				for(var i=0; i<data.length; i++){
					var result = data[i];
					var $row = $p.find('.gridReference').clone();
					$row.find('li:eq(0)').html( '<span class="ui-icon '+result.icon+'"></span>' + result.descr )
						.css({
							"padding-left": "10px",
							"min-width": "186px",
							"max-width": "186px"
						});
					$row.wrapInner('<a name="'+result.name+'" class="item" href="javascript: void(0);" child="ajus" />');
					$p.find("[name=acAjus]").after( $row.children() );
				}
				$p.find('[name=acAjus]').data('ajus',$('#pageWrapper [child=ajus]:first').data('ajus'));
				$p.find('[name=acAjusInmu]').click(function(){ acAjusInmu.init(); }).addClass('ui-state-highlight');
			},'json');
		}
		K.initMode({
			mode: 'ac',
			action: 'acAjusInmu',
			titleBar: {
				title: 'Ajustes - Inmuebles'
			}
		});
		new K.Panel({
			id: 'mainPanel',
			contentURL: 'ac/ajus/inmu',
			onContentLoaded: function(){
				$('#No-Results').hide();
				$mainPanel = $('#mainPanel');
				$mainPanel.find('.ui-layout-west').css('padding','0px').find('a').bind('click',function(event){
					event.preventDefault();
					var $anchor = $(this);
					$('#pageWrapperMain .ui-layout-center').scrollTo( $('#'+$anchor.attr('name')), 800 );
				}).eq(0).click().find('ul').addClass("ui-state-highlight");
				$section1 = $mainPanel.find('#section1');
				$section1.find('[name=btnSelect]').click(function(){
					ciSearch.windowSearchEnti({callback: function(data){
						$section1.find('[name=enti]').html(ciHelper.enti.formatName(data)).data('data',data._id.$id);
					},filter: [
						{nomb: 'tipo_enti',value: 'P'}
					]});
				}).button({icons: {primary: 'ui-icon-search'}});
				$section1.find('[name=btnEjecutar]').click(function(){	
					ciHelper.confirm('&iquest;Est&aacute; seguro(a) de actualizar esta Entidad&#63;',function () {
						K.sendingInfo();
						$.post('ac/ajus/save_inmu_enti',{_id: $section1.find('[name=enti]').data('data')},function(){
							K.clearNoti();
							K.notification({
								title: ciHelper.titleMessages.regiAct,
								text: 'La actualizaci&oacute;n se realiz&oacute; con &eacute;xito!'
							});
						});
					},function () {
						$.noop();
					});
				}).button({icons: {primary: 'ui-icon-disk'}});
				$section2 = $mainPanel.find('#section2');
				$section2.find('[name=btnSelectEspa]').click(function(){
					inLoca.selectEspAll({callback: function(data){
						$section2.find('[name=espa]').html(data.descr+' - '+data.ubic.ref).data('data',data._id.$id);
					},desocupado: true});
				}).button({icons: {primary: 'ui-icon-search'}});
				$section2.find('[name=btnSelectArre]').click(function(){
					inArre.windowSelect({callback: function(data){
						$section2.find('[name=arren]').html(data.espacio.descr).data('data',data._id.$id);
					},oper: 'arrendamiento'});
				}).button({icons: {primary: 'ui-icon-search'}});
				$section2.find('[name=btnEjecutar]').click(function(){	
					ciHelper.confirm('&iquest;Est&aacute; seguro(a) de actualizar esta Entidad&#63;',function () {
						K.sendingInfo();
						$.post('ac/ajus/save_inmu_espa',{
							espacio: $section2.find('[name=espa]').data('data'),
							arrenda: $section2.find('[name=arren]').data('data')
						},function(){
							K.clearNoti();
							K.notification({
								title: ciHelper.titleMessages.regiAct,
								text: 'La actualizaci&oacute;n se realiz&oacute; con &eacute;xito!'
							});
						});
					},function () {
						$.noop();
					});
				}).button({icons: {primary: 'ui-icon-disk'}});
				$('#pageWrapperMain').layout();
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
		$('#pageWrapperMain').layout();
		K.unblock({$element: $('#pageWrapperMain')});
	}	
};