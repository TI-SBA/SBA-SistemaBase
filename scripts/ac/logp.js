/*******************************************************************************
log de usuario */
acLogp = {
	init: function(){
		K.initMode({
			mode: 'ac',
			action: 'acLogp',
			titleBar: {
				title: 'Log Personal de Usuario'
			}
		});
		
		new K.Panel({
			onContentLoaded: function(){
		   		var $grid = new K.grid({
					cols: ['M&oacute;dulo','Bandeja','Descripci&oacute;n','Trabajador','Registrado'],
					data: 'ac/log/Lista',
					params: {usuario: K.session.enti._id.$id},
					itemdescr: 'registro(s)',
					onLoading: function(){ 
						K.block({$element: $('#pageWrapperMain')});
					},
					onComplete: function(){ 
						K.unblock({$element: $('#pageWrapperMain')});
					},
					fill: function(data,$row){
						$row.append('<td>'+acLogs.modulos[data.modulo]+'</td>');
						$row.append('<td>'+data.bandeja+'</td>');
						$row.append('<td>'+data.descr+'</td>');
						$row.append('<td>'+mgEnti.formatName(data.entidad)+'</td>');
						$row.append('<td>'+ciHelper.date.format.bd_ymdhi(data.fecreg)+'</td>');
						return $row;
					}
				});
			}
		});
	}
};
define(
	['ac/logs'],
	function(acLogs){
		return acLogp;
	}
);