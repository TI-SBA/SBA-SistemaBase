/*
 * IMPORTACION DE PLAYAS
 */
inImpl = {
	init: function(p){
		if(p==null) p = {};
		K.initMode({
			mode: 'in',
			action: 'inImpl',
			titleBar: {
				title: 'Importaci&oacute;n de Data de Playas'
			}
		});
		
		new K.Panel({
			contentURL: 'in/impl',
			onContentLoaded: function(){
		   		p.$w = $('#mainPanel');
				$("#file_upload").fileinput({
					language: "es",
					uploadUrl: "ci/upload/in_playas",
					fileType: "any",
					previewFileIcon: "<i class='fa fa-king'></i>",
					uploadExtraData: function() {
						return {
							operacion: 'IN_PLAYAS'
						};
					},
					allowedFileExtensions: ["xls","xlsx"]
				});
				$('#file_upload').on('fileuploaded', function(event, params) {
					K.clearNoti();
					K.block();
					K.sendingInfo();
					$.post('in/impl/import',{file: params.files[0].name},function(){
						K.clearNoti();
						K.unblock();
						K.notification({
							title: ciHelper.titleMessages.regiGua,
							text: 'Registros importados con &eacute;xito!'
						});
						inComp.init();
					});
				});
				$.post('cj/cuen/get_config_inmu',function(data){
					p.$w.find('[name=btnPlan]').click(function(){
						K.windowExcel({url: $(this).data('data')});
					}).data('data','https://storage.googleapis.com/archivo-central-storage/documentos/plantilla_playa.xlsx');
		   			K.unblock({$element: $('#pageWrapperMain')});
		   		},'json');
			}
		});
	}
};
define(
	['mg/mult','in/comp'],
	function(mgMult,inComp){
		return inImpl;
	}
);