usPedi = {
	states: {
		C: {
			descr: "Creado",
			color: "green",
			label: '<span class="label label-success">Creado</span>'
		},
		A: {
			descr: "Atendido",
			color: "#CCCCCC",
			label: '<span class="label label-default">Atendido</span>'
		}
	},
	init: function () {
		K.initMode({
			mode: 'us',
			action: 'usPedi',
			titleBar: {
				title: 'Pedido de Raciones'
			}
		});

		new K.Panel({
			onContentLoaded: function () {
				var $grid = new K.grid({
					cols: ['', '', {
						n: 'Periodo',
						f: 'ini'
					}, {
						n: '&Uacute;ltima Modificaci&oacute;n',
						f: 'fecmod'
					}],
					data: 'us/pedi/lista',
					params: {},
					itemdescr: 'pedido(s)',
					toolbarHTML: '<button name="btnAgregar" class="btn btn-success"><i class="fa fa-plus"></i> Agregar Semana</button>',
					onContentLoaded: function ($el) {
						$el.find('[name=btnAgregar]').click(function () {
							usPedi.windowNew();
						});
					},
					onLoading: function () {
						K.block({
							$element: $('#pageWrapperMain')
						});
					},
					onComplete: function () {
						K.unblock({
							$element: $('#pageWrapperMain')
						});
					},
					fill: function (data, $row) {
						$row.append('<td>');
						$row.append('<td>' + usPedi.states[data.estado].label + '</td>');
						$row.append('<td>' + ciHelper.date.format.bd_ymd(data.ini) + ' al ' + ciHelper.date.format.bd_ymd(data.fin) + '</td>');
						$row.append('<td>' + ciHelper.date.format.bd_ymdhi(data.fecmod) + '<br />' + mgEnti.formatName(data.trabajador) + '</td>');
						$row.data('id', data._id.$id).dblclick(function () {
							usPedi.windowDetails({
								id: $(this).data('id'),
								nomb: $(this).find('td:eq(2)').html()
							});
						}).data('estado', data.estado).contextMenu("conMenListEd", {
							onShowMenu: function ($row, menu) {
								$('#conMenListEd_des,#conMenListEd_hab', menu).remove();
								if ($row.data('estado') != 'C') $('#conMenListEd_edi', menu).remove();
								return menu;
							},
							bindings: {
								'conMenListEd_ver': function (t) {
									usPedi.windowDetails({
										id: K.tmp.data('id'),
										nomb: K.tmp.find('td:eq(2)').html()
									});
								},
								'conMenListEd_edi': function (t) {
									usPedi.windowEdit({
										id: K.tmp.data('id'),
										nomb: K.tmp.find('td:eq(2)').html()
									});
								}
							}
						});
						return $row;
					}
				});
			}
		});
	},
	windowNew: function (p) {
		if (p == null) p = {};
		new K.Panel({
			contentURL: 'us/pedi/edit',
			buttons: {
				'Guardar': {
					icon: 'fa-save',
					type: 'success',
					f: function () {
						K.clearNoti();
						var data = {
							ini: p.ini,
							fin: p.fin,
							oficina: mgOrga.dbRel(p.$w.find('[name=organizacion]').data('data')),
							desa: [],
							almu: [],
							cena: [],
							diet: []
						};
						for (var i = 0, j = 7; i < j; i++) {
							data.desa.push(p.$w.find('[name=gridRaci] tbody .item:eq(0) [name^=cant]').eq(i).val());
						}
						for (var i = 0, j = 7; i < j; i++) {
							data.almu.push(p.$w.find('[name=gridRaci] tbody .item:eq(1) [name^=cant]').eq(i).val());
						}
						for (var i = 0, j = 7; i < j; i++) {
							data.cena.push(p.$w.find('[name=gridRaci] tbody .item:eq(2) [name^=cant]').eq(i).val());
						}
						for (var i = 0, j = 7; i < j; i++) {
							data.diet.push(p.$w.find('[name=gridRaci] tbody .item:eq(3) [name^=cant]').eq(i).val());
						}
						K.sendingInfo();
						p.$w.find('#div_buttons button').attr('disabled', 'disabled');
						$.post("us/pedi/save", data, function (result) {
							K.clearNoti();
							K.notification({
								title: ciHelper.titleMessages.regiGua,
								text: "Pedido agregado!"
							});
							usPedi.init();
							K.closeWindow(p.$w.attr('id'));
						}, 'json');
					}
				},
				'Cancelar': {
					icon: 'fa-ban',
					type: 'danger',
					f: function () {
						usPedi.init();
					}
				}
			},
			onContentLoaded: function () {
				p.$w = $('#mainPanel');
				K.block({
					$element: p.$w
				});
				p.$w.find('[name=btnOrga]').click(function () {
					tdOfic.windowSelect({
						callback: function (data) {
							p.$w.find('[name=organizacion]').html(data.nomb).data('data', data);
						}
					});
				}).hide();
				K.grid({
					$el: p.$w.find('[name=gridRaci]'),
					cols: ['', 'Lunes', 'Martes', 'Mi&eacute;rcoles', 'Jueves', 'Viernes', 'S&aacute;bado', 'Domingo'],
					search: false,
					pagination: false,
					onlyHtml: true
				});
				$.post('us/pedi/get_week', function (data) {
					if (data.progra == null) {
						K.notification({
							title: ciHelper.titleMessages.infoReq,
							text: 'No hay una programaci&oacute;n para la semana que viene!'
						});
						return usPedi.init();
					}
					p.ini = data.ini;
					p.fin = data.fin;
					p.$w.find('[name=organizacion]').html(data.oficina.nomb).data('data', data.oficina);
					p.$w.find('[name=periodo]').html(data.ini + ' (Lunes)  al  ' + data.fin + ' (Domingo)');
					/* DESAYUNO */
					var $row = $('<tr class="item">');
					$row.append('<td>Desayuno</td>');
					for (var i = 0, j = 7; i < j; i++) {
						$row.append('<td><input type="number" name="cant' + i + '" value="0" />' +
							'<br />' + data.progra.desa[i].descr + '<br />(' + data.progra.desa[i].valor_nut + ' calor&iacute;as)' +
							'</td>');
					}
					p.$w.find('[name=gridRaci] tbody').append($row);
					/* ALMUERZO */
					var $row = $('<tr class="item">');
					$row.append('<td>Almuerzo</td>');
					for (var i = 0, j = 7; i < j; i++) {
						$row.append('<td><input type="number" name="cant' + i + '" value="0" />' +
							'<br />' + data.progra.almu[i].descr + '<br />(' + data.progra.almu[i].valor_nut + ' calor&iacute;as)' +
							'</td>');
					}
					p.$w.find('[name=gridRaci] tbody').append($row);
					/* CENA */
					var $row = $('<tr class="item">');
					$row.append('<td>Cena</td>');
					for (var i = 0, j = 7; i < j; i++) {
						$row.append('<td><input type="number" name="cant' + i + '" value="0" />' +
							'<br />' + data.progra.cena[i].descr + '<br />(' + data.progra.cena[i].valor_nut + ' calor&iacute;as)' +
							'</td>');
					}
					p.$w.find('[name=gridRaci] tbody').append($row);
					/* DIETA */
					var $row = $('<tr class="item">');
					$row.append('<td>Dieta</td>');
					for (var i = 0, j = 7; i < j; i++) {
						$row.append('<td><input type="number" name="cant' + i + '" value="0" />' +
							'<br />' + data.progra.diet[i].descr + '<br />(' + data.progra.diet[i].valor_nut + ' calor&iacute;as)' +
							'</td>');
					}
					p.$w.find('[name=gridRaci] tbody').append($row);
					K.unblock({
						$element: p.$w
					});
				}, 'json');
			}
		});
	},
	windowEdit: function (p) {
		if (p == null) p = {};
		new K.Panel({
			contentURL: 'us/pedi/edit',
			buttons: {
				'Guardar': {
					icon: 'fa-save',
					type: 'success',
					f: function () {
						K.clearNoti();
						var data = {
							_id: p.id,
							desa: [],
							almu: [],
							cena: [],
							diet: []
						};
						for (var i = 0, j = 7; i < j; i++) {
							data.desa.push(p.$w.find('[name=gridRaci] tbody .item:eq(0) [name^=cant]').eq(i).val());
						}
						for (var i = 0, j = 7; i < j; i++) {
							data.almu.push(p.$w.find('[name=gridRaci] tbody .item:eq(1) [name^=cant]').eq(i).val());
						}
						for (var i = 0, j = 7; i < j; i++) {
							data.cena.push(p.$w.find('[name=gridRaci] tbody .item:eq(2) [name^=cant]').eq(i).val());
						}
						for (var i = 0, j = 7; i < j; i++) {
							data.diet.push(p.$w.find('[name=gridRaci] tbody .item:eq(3) [name^=cant]').eq(i).val());
						}
						K.sendingInfo();
						p.$w.find('#div_buttons button').attr('disabled', 'disabled');
						$.post("us/pedi/save", data, function (result) {
							K.clearNoti();
							K.notification({
								title: ciHelper.titleMessages.regiGua,
								text: "Pedido actualizado!"
							});
							usPedi.init();
							K.closeWindow(p.$w.attr('id'));
						}, 'json');
					}
				},
				'Cancelar': {
					icon: 'fa-ban',
					type: 'danger',
					f: function () {
						usPedi.init();
					}
				}
			},
			onContentLoaded: function () {
				p.$w = $('#mainPanel');
				K.block({
					$element: p.$w
				});
				p.$w.find('[name=btnOrga]').hide();
				K.grid({
					$el: p.$w.find('[name=gridRaci]'),
					cols: ['', 'Lunes', 'Martes', 'Mi&eacute;rcoles', 'Jueves', 'Viernes', 'S&aacute;bado', 'Domingo'],
					search: false,
					pagination: false,
					onlyHtml: true,
				});
				$.post('us/pedi/get', {
					_id: p.id,
					prog: true
				}, function (data) {
					p.$w.find('[name=organizacion]').html(data.oficina.nomb);
					p.$w.find('[name=periodo]').html(ciHelper.date.format.bd_ymd(data.ini) + ' (Lunes)  al  ' + ciHelper.date.format.bd_ymd(data.fin) + ' (Domingo)');
					/* DESAYUNO */
					var $row = $('<tr class="item">');
					$row.append('<td>Desayuno</td>');
					for (var i = 0, j = 7; i < j; i++) {
						$row.append('<td><input type="number" name="cant' + i + '" value="' + data.desa[i] + '" />' +
							'<br />' + data.prog.desa[i].descr + '<br />(' + data.prog.desa[i].valor_nut + ' calor&iacute;as)' +
							'</td>');
					}
					p.$w.find('[name=gridRaci] tbody').append($row);
					/* ALMUERZO */
					var $row = $('<tr class="item">');
					$row.append('<td>Almuerzo</td>');
					for (var i = 0, j = 7; i < j; i++) {
						$row.append('<td><input type="number" name="cant' + i + '" value="' + data.almu[i] + '" />' +
							'<br />' + data.prog.almu[i].descr + '<br />(' + data.prog.almu[i].valor_nut + ' calor&iacute;as)' +
							'</td>');
					}
					p.$w.find('[name=gridRaci] tbody').append($row);
					/* CENA */
					var $row = $('<tr class="item">');
					$row.append('<td>Cena</td>');
					for (var i = 0, j = 7; i < j; i++) {
						$row.append('<td><input type="number" name="cant' + i + '" value="' + data.cena[i] + '" />' +
							'<br />' + data.prog.cena[i].descr + '<br />(' + data.prog.cena[i].valor_nut + ' calor&iacute;as)' +
							'</td>');
					}
					p.$w.find('[name=gridRaci] tbody').append($row);
					/* DIETA */
					var $row = $('<tr class="item">');
					$row.append('<td>Dieta</td>');
					for (var i = 0, j = 7; i < j; i++) {
						$row.append('<td><input type="number" name="cant' + i + '" value="' + data.diet[i] + '" />' +
							'<br />' + data.prog.diet[i].descr + '<br />(' + data.prog.diet[i].valor_nut + ' calor&iacute;as)' +
							'</td>');
					}
					p.$w.find('[name=gridRaci] tbody').append($row);
					K.unblock({
						$element: p.$w
					});
				}, 'json');
			}
		});
	},
	windowDetails: function (p) {
		if (p == null) p = {};
		new K.Panel({
			contentURL: 'us/pedi/edit',
			buttons: {
				'Cancelar': {
					icon: 'fa-ban',
					type: 'danger',
					f: function () {
						usPedi.init();
					}
				}
			},
			onContentLoaded: function () {
				p.$w = $('#mainPanel');
				K.block({
					$element: p.$w
				});
				p.$w.find('[name=btnOrga]').hide();
				K.grid({
					$el: p.$w.find('[name=gridRaci]'),
					cols: ['', 'Lunes', 'Martes', 'Mi&eacute;rcoles', 'Jueves', 'Viernes', 'S&aacute;bado', 'Domingo'],
					search: false,
					pagination: false,
					onlyHtml: true
				});
				$.post('us/pedi/get', {
					_id: p.id,
					prog: true
				}, function (data) {
					p.$w.find('[name=organizacion]').html(data.oficina.nomb);
					p.$w.find('[name=periodo]').html(ciHelper.date.format.bd_ymd(data.ini) + ' (Lunes)  al  ' + ciHelper.date.format.bd_ymd(data.fin) + ' (Domingo)');
					/* DESAYUNO */
					var $row = $('<tr class="item">');
					$row.append('<td>Desayuno</td>');
					for (var i = 0, j = 7; i < j; i++) {
						$row.append('<td>' + data.desa[i] +
							'<br />' + data.prog.desa[i].descr + '<br />(' + data.prog.desa[i].valor_nut + ' calor&iacute;as)' +
							'</td>');
					}
					p.$w.find('[name=gridRaci] tbody').append($row);
					/* ALMUERZO */
					var $row = $('<tr class="item">');
					$row.append('<td>Almuerzo</td>');
					for (var i = 0, j = 7; i < j; i++) {
						$row.append('<td>' + data.almu[i] +
							'<br />' + data.prog.almu[i].descr + '<br />(' + data.prog.almu[i].valor_nut + ' calor&iacute;as)' +
							'</td>');
					}
					p.$w.find('[name=gridRaci] tbody').append($row);
					/* CENA */
					var $row = $('<tr class="item">');
					$row.append('<td>Cena</td>');
					for (var i = 0, j = 7; i < j; i++) {
						$row.append('<td>' + data.cena[i] +
							'<br />' + data.prog.cena[i].descr + '<br />(' + data.prog.cena[i].valor_nut + ' calor&iacute;as)' +
							'</td>');
					}
					p.$w.find('[name=gridRaci] tbody').append($row);
					/* DIETA */
					var $row = $('<tr class="item">');
					$row.append('<td>Dieta</td>');
					for (var i = 0, j = 7; i < j; i++) {
						$row.append('<td>' + data.diet[i] +
							'<br />' + data.prog.diet[i].descr + '<br />(' + data.prog.diet[i].valor_nut + ' calor&iacute;as)' +
							'</td>');
					}
					p.$w.find('[name=gridRaci] tbody').append($row);
					K.unblock({
						$element: p.$w
					});
				}, 'json');
			}
		});
	}
};
define(
	['mg/enti', 'us/rece'],
	function (mgEnti, usRece) {
		return usPedi;
	}
);