tdGestExt = {
	init: function(){
		if($('#pageWrapper [child=gest]').length<=0){
			var $p = $('#pageWrapperLeft');
			$.post('td/navg/gest',function(data){
				for(var i=0; i<data.length; i++){
					var result = data[i];
					var $row = $p.find('.gridReference').clone();
					$row.find('li:eq(0)').html( '<span class="ui-icon '+result.icon+'"></span>' + result.descr )
						.css({
							"padding-left": "10px",
							"min-width": "186px",
							"max-width": "186px"
						});
					$row.wrapInner('<a name="'+result.name+'" class="item" href="javascript: void(0);" child="gest" />');
					$p.find("[name=tdGest]").after( $row.children() );
				}
				$p.find('[name=tdGest]').data('gest',$('#pageWrapper [child=gest]:first').data('gest'));
				$p.find('[name=tdGestInt]').click(function(){ tdGestInt.init(); });
				$p.find('[name=tdGestExt]').click(function(){ tdGestExt.init(); }).find('ul').addClass('ui-state-highlight');
			},'json');
		}
		K.initMode({
			mode: 'td',
			action: 'tdGestExt',
			titleBar: {
				title: 'Cuentas: Gestores Externos',
				toolbar: true
			}
		});
		
		new K.Panel({
			id: 'mainPanel',
			contentURL: 'td/gest',
			onContentLoaded: function(){
				$('#No-Results').hide();
				$mainPanel = $('#mainPanel');
				$mainPanel.find('[name=buscar]').attr( 'placeholder' , 'Ingrese el nombre de entidad' ).width('250');
				$mainPanel.find('[name=obj]').html( 'entidades' );
				$mainPanel.find("[name=moreresults]").css('float','right').button({icons: {primary: 'ui-icon-triangle-1-s'}});
				$mainPanel.resize(function(){
					$mainPanel.find('.grid:eq(1)').height(($mainPanel.height()-$mainPanel.find('.grid:eq(0)').height()-$('.div-bottom').outerHeight()-$('.div-bottom').height())+'px');
				}).resize();
				$mainPanel.find('.grid:eq(0)').css('overflow','hidden');
				$mainPanel.find('.grid:eq(1)').scroll(function(){
					$mainPanel.find('.grid:eq(0)').scrollLeft($(this).scrollLeft());
				});
				$mainPanel.find('[name=btnGestor]').click(function(){
					ciCreate.windowNewEntidad({callBack: tdGestExt.init,roles: {gestor: 1}});
				}).button({icons: {primary: 'ui-icon-plusthick'}});
				$mainPanel.find('[name=btnExpd]').click(function(){
					tdExpd.windowNewExp({externo: true,noTupa: true});
				}).button({icons: {primary: 'ui-icon-plusthick'}}).remove();
				$mainPanel.find('.divSearch [name=buscar]').keyup(function(e){
					if(e.keyCode == 13)
						$('.divSearch [name=btnBuscar]').click();
				});
				$mainPanel.find('.divSearch [name=btnBuscar]').click(function(){
					if($('.divSearch [name=buscar]').val().length<=0){
						$("#mainPanel .gridBody").empty();
						tdGest.loadData({page: 1,url: 'td/gest/lista'});
					}else{
						$("#mainPanel .gridBody").empty();
						tdGest.loadData({page: 1,url: 'td/gest/search_gest_ext'});
					}
				}).button({icons: {primary: 'ui-icon-search'}});
				var $li = $("#mainPanel .gridHeader li");
				$li.eq(0).attr('filter','_id');
				$li.eq(1).attr('filter','nomb');
				$("#mainPanel .gridHeader").find('li:eq(0),li:eq(1)').click( function(){
					$this = $(this);
					var order = 1;
					if($this.find('.ui-sorter').length>0){
						if($this.find('.ui-asc').length>0){
							$this.find('.ui-sorter').remove();
							$this.append('<span class="ui-sorter ui-icon ui-icon-triangle-1-s ui-desc" style="float:right"></span>');
						}else{
							$this.find('.ui-sorter').remove();
							$this.append('<span class="ui-sorter ui-icon ui-icon-triangle-1-n ui-asc" style="float:right"></span>');
							order = -1;
						}
					}else{
						$this.closest('.gridHeader').find('.ui-sorter').remove();
						$this.append('<span class="ui-sorter ui-icon ui-icon-triangle-1-s ui-desc" style="float:right"></span>');
					}
					$("#mainPanel .gridBody").empty();
					tdGest.loadData({page: 1,url: 'td/gest/lista',filter: $(this).attr("filter"),order: order});
				});
				tdGest.loadData({page: 1,url: 'td/gest/lista'});
			}
		});
		$('#pageWrapperMain').layout();
	}
};
define(
	['td/gest'],
	function(tdGest){
		return tdGestExt;
	}
);