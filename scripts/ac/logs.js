/*******************************************************************************
Usuario */
acLogs = {
	modulos: {
		MG:"Maestros Generales",
		TD:"Tramite Documentario",
		CM:"Cementerio",
		LG:"Logistica",
		IN:"Inmuebles",
		PE:"Personal",
		PR:"Planificacion Presupuestos",
		CT:"Contabilidad",
		TS:"Tesoreria",
		CJ:"Caja",
		AL:"Asesoria Legal",
		AC:"Seguridad"
	},
	init: function(){
		K.initMode({
			mode: 'ac',
			action: 'acLogs',
			titleBar: {
				title: 'Logs'
			}
		});
		
		new K.Panel({
			onContentLoaded: function(){
		   		var $grid = new K.grid({
					cols: ['M&oacute;dulo','Bandeja','Descripci&oacute;n','Trabajador','Registrado'],
					data: 'ac/log/Lista',
					params: {},
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
	function(){
		return acLogs;
	}
);