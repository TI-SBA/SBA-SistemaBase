lgCuen = {
	init: function(){
		K.initMode({
			mode: 'lg',
			action: 'lgCuen',
			titleBar: {
				title: 'Cuentas Contables'
			}
		});
		
		new K.Panel({
			onContentLoaded: function(){
		   		var $grid = new K.grid({
					cols: ['',{n:'Cod',f:'cod'},{n:'Descripci&oacute;n',f:'descr'},'Partida','Descripci&oacute;n de Partida'],
					data: 'ct/pcon/search',
					params: {
						logistica: true
					},
					itemdescr: 'cuenta(s)',
					toolbarHTML: '<button name="btnAgregar" class="btn btn-success"><i class="fa fa-plus"></i> Agregar</button>',
					onContentLoaded: function($el){
						$el.find('[name=btnAgregar]').click(function(){
							ctPcon.windowSelect({
								bootstrap: true,
								callback: function(data){
									K.sendingInfo();
									$.post('ct/pcon/save',{_id: data._id.$id,logistica: true},function(){
										K.clearNoti();
										lgCuen.init();
										K.notification({
											title: ciHelper.titleMessages.regiAct,
											text: 'La cuenta fue a&ntilde;adida a Log&iacute;stica!'
										});
									});
								}
							});
						});
					},
					onLoading: function(){ 
						K.block({$element: $('#pageWrapperMain')});
					},
					onComplete: function(){
						K.unblock({$element: $('#pageWrapperMain')});
					},
					fill: function(data,$row){
						$row.append('<td>');
						$row.append('<td>'+data.cod+'</td>');
						$row.append('<td>'+data.descr+'</td>');
						$row.append('<td>'+data.clasificador.cod+'</td>');
						$row.append('<td>'+data.clasificador.descr+'</td>');
						$row.data('id',data._id.$id).contextMenu("conMenList", {
							onShowMenu: function($row, menu) {
								$('#conMenList_edi,#conMenList_imp',menu).remove();
								return menu;
							},
							bindings: {
								'conMenList_eli': function(t) {
									K.incomplete();
								}
							}
						});
						return $row;
					}
				});
			}
		});
	}
};
define(
	['ct/pcon'],
	function(ctPcon){
		return lgCuen;
	}
);