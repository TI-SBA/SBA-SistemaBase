/*
 * IMPORTACION DE ASISTENCIAS (reloj antiguo)
 */
peImas = {
	init: function(p){
		if(p==null) p = {};
		K.initMode({
			mode: 'pe',
			action: 'peImas',
			titleBar: {
				title: 'Importaci&oacute;n de Asistencias (Reloj Antigüo)'
			}
		});
		
		new K.Panel({
			contentURL: 'pe/imas',
			store: false,
			onContentLoaded: function(){
		   		p.$w = $('#mainPanel');
		   		p.$w.find('[name=periodo]').datepicker({
			        format: "mm-yyyy",
					viewMode: "months", 
					minViewMode: "months"
			        /*onClose: function(dateText, inst) { 
			            var month = $("#ui-datepicker-div .ui-datepicker-month :selected").val();
			            var year = $("#ui-datepicker-div .ui-datepicker-year :selected").val();
			            $(this).data('mes',month).data('ano',year);
			            $(this).val($.datepicker.formatDate('MM yy', new Date(year, month, 1)));
			        }*/
			    });
				$("#file_upload").fileinput({
					language: "es",
					uploadUrl: "ci/upload/pe_asistencia",
					fileType: "any",
					previewFileIcon: "<i class='fa fa-king'></i>",
					uploadExtraData: function() {
						return {
							operacion: 'PE_ASISTENCIA'
						};
					},
					allowedFileExtensions: ["TXT"]
				});
				$('#file_upload').on('fileuploaded', function(event, params) {
					K.clearNoti();
					K.block();
					K.sendingInfo();
					$.post('pe/imas/import',{equipo: peEqui.dbRel(p.$w.find('[name=equipo]').data('data')),file: params.files[0].name},function(){
						K.clearNoti();
						K.unblock();
						K.notification({
							title: ciHelper.titleMessages.regiGua,
							text: 'Registros importados con &eacute;xito!'
						});
						//peImas.init();
					});
				});
				$("#file_upload2").fileinput({
					language: "es",
					uploadUrl: "ci/upload/pe_rol_turnos",
					fileType: "any",
					previewFileIcon: "<i class='fa fa-king'></i>",
					uploadExtraData: function() {
						return {
							operacion: 'PE_ASISTENCIA'
						};
					},
					allowedFileExtensions: ["XLS","XLSX"]
				});
				$('#file_upload2').on('fileuploaded', function(event, params) {
					K.clearNoti();
					K.block();
					K.sendingInfo();
					$.post('pe/imas/import_rol',{equipo: peEqui.dbRel(p.$w.find('[name=equipo]').data('data')),file: params.files[0].name},function(){
						K.clearNoti();
						K.unblock();
						K.notification({
							title: ciHelper.titleMessages.regiGua,
							text: 'Registros importados con &eacute;xito!'
						});
						//peImas.init();
					});
				});
				p.$w.find('[name=btnPlan]').click(function(){
					var periodo = p.$w.find('[name=periodo]').val();
					periodo = periodo.split('-');
					if(periodo.length==2){
						var mes = periodo[0];
						var ano = periodo[1];
						var params = {
							ano: ano,
							mes: mes,
							programa: p.$w.find('[name=programa] :selected').val()
						}
						console.log(params);
						window.open('pe/imas/download_format?'+$.param(params));
					}else{
						return false;
					}
				});
				$.post('mg/prog/all',function(prog){
					var $cbo = p.$w.find('[name=programa]');
					if(prog!=null){
						for(var i in prog){
							$cbo.append('<option value="'+prog[i]._id.$id+'">'+prog[i].nomb+'</option>');
							$cbo.find('option:last').data('data',prog[i]);
						}
					}
					K.unblock({$element: p.$w});
				},'json');
				$.post('cj/cuen/get_config_pers',function(data){
					p.$w.find('[name=equipo]').html(data.EQUIPO.nomb).data('data',data.EQUIPO);
		   			K.unblock();
		   		},'json');
			}
		});
	}
};
define(
	['mg/mult','pe/equi'],
	function(mgMult,peEqui){
		return peImas;
	}
);