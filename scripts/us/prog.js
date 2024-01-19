usProg = {
	states: {
		P: {
			descr: "Publicado",
			color: "green",
			label: '<span class="label label-success">Publicado</span>'
		},
		B: {
			descr: "Borrador",
			color: "#CCCCCC",
			label: '<span class="label label-default">Borrador</span>'
		}
	},
	init: function () {
		K.initMode({
			mode: 'us',
			action: 'usProg',
			titleBar: {
				title: 'Programaci&oacute;n Semanal de Raciones'
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
					data: 'us/prog/lista',
					params: {},
					itemdescr: 'semana(s)',
					toolbarHTML: '<button name="btnAgregar" class="btn btn-success"><i class="fa fa-plus"></i> Agregar Semana</button>',
					onContentLoaded: function ($el) {
						$el.find('[name=btnAgregar]').click(function () {
							usProg.windowNew();
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
						$row.append('<td>' + usProg.states[data.estado].label + '</td>');
						$row.append('<td>' + ciHelper.date.format.bd_ymd(data.ini) + ' al ' + ciHelper.date.format.bd_ymd(data.fin) + '</td>');
						$row.append('<td>' + ciHelper.date.format.bd_ymdhi(data.fecmod) + '<br />' + mgEnti.formatName(data.trabajador) + '</td>');
						$row.data('id', data._id.$id).dblclick(function () {
							usProg.windowDetails({
								id: $(this).data('id'),
								nomb: $(this).find('td:eq(2)').html()
							});
						}).data('estado', data.estado).contextMenu("conMenUsProg", {
							onShowMenu: function ($row, menu) {
								$('#conMenListEd_des,#conMenListEd_hab', menu).remove();
								if ($row.data('estado') == 'P')
									$('#conMenUsProg_edi,#conMenUsProg_apr', menu).remove();
								else
									$('#conMenUsProg_imp', menu).remove();
								return menu;
							},
							bindings: {
								'conMenUsProg_ver': function (t) {
									usProg.windowDetails({
										id: K.tmp.data('id'),
										nomb: K.tmp.find('td:eq(2)').html()
									});
								},
								'conMenUsProg_edi': function (t) {
									usProg.windowEdit({
										id: K.tmp.data('id'),
										nomb: K.tmp.find('td:eq(2)').html()
									});
								},
								'conMenUsProg_imp': function (t) {
									K.incomplete();
								},
								'conMenUsProg_apr': function (t) {
									ciHelper.confirm('&#191;Desea <b>Publicar</b> la Programaci&oacute;n para el Periodo <b>' + K.tmp.find('td:eq(2)').html() + '</b>&#63;',
										function () {
											K.sendingInfo();
											$.post('us/prog/save', {
												_id: K.tmp.data('id'),
												estado: 'P'
											}, function () {
												K.clearNoti();
												K.notification({
													title: 'Programaci&oacute;n Publicada',
													text: 'La publicaci&oacute;n se realiz&oacute; con &eacute;xito!'
												});
												usProg.init();
											});
										},
										function () {
											$.noop();
										}, 'Programaci&oacute;n Semanal');
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
			contentURL: 'us/prog/edit',
			buttons: {
				'Guardar': {
					icon: 'fa-save',
					type: 'success',
					f: function () {
						K.clearNoti();
						var data = {
							ini: p.ini,
							fin: p.fin,
							desa: [],
							almu: [],
							cena: [],
							diet: []
						};
						for (var i = 0, j = 7; i < j; i++) {
							if (p.$w.find('[name=gridRaci] tbody .item:eq(0) [name^=receta]').eq(i).data('data') == null) {
								p.$w.find('[name=gridRaci] tbody .item:eq(0) button').eq(i).click();
								return K.notification({
									title: ciHelper.titleMessages.infoReq,
									text: 'Debe seleccionar una Receta!',
									type: 'error'
								});
							}
							data.desa.push(usRece.dbRel(p.$w.find('[name=gridRaci] tbody .item:eq(0) [name^=receta]').eq(i).data('data')));
						}
						for (var i = 0, j = 7; i < j; i++) {
							if (p.$w.find('[name=gridRaci] tbody .item:eq(1) [name^=receta]').eq(i).data('data') == null) {
								p.$w.find('[name=gridRaci] tbody .item:eq(1) button').eq(i).click();
								return K.notification({
									title: ciHelper.titleMessages.infoReq,
									text: 'Debe seleccionar una Receta!',
									type: 'error'
								});
							}
							data.almu.push(usRece.dbRel(p.$w.find('[name=gridRaci] tbody .item:eq(1) [name^=receta]').eq(i).data('data')));
						}
						for (var i = 0, j = 7; i < j; i++) {
							if (p.$w.find('[name=gridRaci] tbody .item:eq(2) [name^=receta]').eq(i).data('data') == null) {
								p.$w.find('[name=gridRaci] tbody .item:eq(2) button').eq(i).click();
								return K.notification({
									title: ciHelper.titleMessages.infoReq,
									text: 'Debe seleccionar una Receta!',
									type: 'error'
								});
							}
							data.cena.push(usRece.dbRel(p.$w.find('[name=gridRaci] tbody .item:eq(2) [name^=receta]').eq(i).data('data')));
						}
						for (var i = 0, j = 7; i < j; i++) {
							if (p.$w.find('[name=gridRaci] tbody .item:eq(3) [name^=receta]').eq(i).data('data') == null) {
								p.$w.find('[name=gridRaci] tbody .item:eq(3) button').eq(i).click();
								return K.notification({
									title: ciHelper.titleMessages.infoReq,
									text: 'Debe seleccionar una Receta!',
									type: 'error'
								});
							}
							data.diet.push(usRece.dbRel(p.$w.find('[name=gridRaci] tbody .item:eq(3) [name^=receta]').eq(i).data('data')));
						}
						K.sendingInfo();
						p.$w.find('#div_buttons button').attr('disabled', 'disabled');
						$.post("us/prog/save", data, function (result) {
							K.clearNoti();
							K.notification({
								title: ciHelper.titleMessages.regiGua,
								text: "Pedido agregado!"
							});
							usProg.init();
							K.closeWindow(p.$w.attr('id'));
						}, 'json');
					}
				},
				'Cancelar': {
					icon: 'fa-ban',
					type: 'danger',
					f: function () {
						usProg.init();
					}
				}
			},
			onContentLoaded: function () {
				p.$w = $('#mainPanel');
				K.block({
					$element: p.$w
				});
				K.grid({
					$el: p.$w.find('[name=gridRaci]'),
					cols: ['', 'Lunes', 'Martes', 'Mi&eacute;rcoles', 'Jueves', 'Viernes', 'S&aacute;bado', 'Domingo'],
					search: false,
					pagination: false,
					onlyHtml: true
				});
				$.post('us/prog/get_week', function (data) {
					p.ini = data.ini;
					p.fin = data.fin;
					p.$w.find('[name=periodo]').html(data.ini + ' (Lunes)  al  ' + data.fin + ' (Domingo)');
					/* DESAYUNO */
					var $row = $('<tr class="item">');
					$row.append('<td>Desayuno</td>');
					for (var i = 0, j = 7; i < j; i++) {
						$row.append('<td><span name="receta' + i + '"></span>&nbsp;<button class="btn btn-info"><i class="fa fa-search"></i></button></td>');
					}
					p.$w.find('[name=gridRaci] tbody').append($row);
					/* ALMUERZO */
					var $row = $('<tr class="item">');
					$row.append('<td>Almuerzo</td>');
					for (var i = 0, j = 7; i < j; i++) {
						$row.append('<td><span name="receta' + i + '"></span>&nbsp;<button class="btn btn-info"><i class="fa fa-search"></i></button></td>');
					}
					p.$w.find('[name=gridRaci] tbody').append($row);
					/* CENA */
					var $row = $('<tr class="item">');
					$row.append('<td>Cena</td>');
					for (var i = 0, j = 7; i < j; i++) {
						$row.append('<td><span name="receta' + i + '"></span>&nbsp;<button class="btn btn-info"><i class="fa fa-search"></i></button></td>');
					}
					p.$w.find('[name=gridRaci] tbody').append($row);
					/* DIETA */
					var $row = $('<tr class="item">');
					$row.append('<td>Dieta</td>');
					for (var i = 0, j = 7; i < j; i++) {
						$row.append('<td><span name="receta' + i + '"></span>&nbsp;<button class="btn btn-info"><i class="fa fa-search"></i></button></td>');
					}
					p.$w.find('[name=gridRaci] tbody').append($row);
					p.$w.find('[name=gridRaci] tbody button').click(function () {
						var $this = $(this);
						usRece.windowSelect({
							callback: function (data) {
								$this.closest('td').find('span').html(data.descr + ' <br />(' + data.valor_nut + ' calor&iacute;as)').data('data', data);
							}
						});
					});
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
			contentURL: 'us/prog/edit',
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
							if (p.$w.find('[name=gridRaci] tbody .item:eq(0) [name^=receta]').eq(i).data('data') == null) {
								p.$w.find('[name=gridRaci] tbody .item:eq(0) button').eq(i).click();
								return K.notification({
									title: ciHelper.titleMessages.infoReq,
									text: 'Debe seleccionar una Receta!',
									type: 'error'
								});
							}
							data.desa.push(usRece.dbRel(p.$w.find('[name=gridRaci] tbody .item:eq(0) [name^=receta]').eq(i).data('data')));
						}
						for (var i = 0, j = 7; i < j; i++) {
							if (p.$w.find('[name=gridRaci] tbody .item:eq(1) [name^=receta]').eq(i).data('data') == null) {
								p.$w.find('[name=gridRaci] tbody .item:eq(1) button').eq(i).click();
								return K.notification({
									title: ciHelper.titleMessages.infoReq,
									text: 'Debe seleccionar una Receta!',
									type: 'error'
								});
							}
							data.almu.push(usRece.dbRel(p.$w.find('[name=gridRaci] tbody .item:eq(1) [name^=receta]').eq(i).data('data')));
						}
						for (var i = 0, j = 7; i < j; i++) {
							if (p.$w.find('[name=gridRaci] tbody .item:eq(2) [name^=receta]').eq(i).data('data') == null) {
								p.$w.find('[name=gridRaci] tbody .item:eq(2) button').eq(i).click();
								return K.notification({
									title: ciHelper.titleMessages.infoReq,
									text: 'Debe seleccionar una Receta!',
									type: 'error'
								});
							}
							data.cena.push(usRece.dbRel(p.$w.find('[name=gridRaci] tbody .item:eq(2) [name^=receta]').eq(i).data('data')));
						}
						for (var i = 0, j = 7; i < j; i++) {
							if (p.$w.find('[name=gridRaci] tbody .item:eq(3) [name^=receta]').eq(i).data('data') == null) {
								p.$w.find('[name=gridRaci] tbody .item:eq(3) button').eq(i).click();
								return K.notification({
									title: ciHelper.titleMessages.infoReq,
									text: 'Debe seleccionar una Receta!',
									type: 'error'
								});
							}
							data.diet.push(usRece.dbRel(p.$w.find('[name=gridRaci] tbody .item:eq(3) [name^=receta]').eq(i).data('data')));
						}
						K.sendingInfo();
						p.$w.find('#div_buttons button').attr('disabled', 'disabled');
						$.post("us/prog/save", data, function (result) {
							K.clearNoti();
							K.notification({
								title: ciHelper.titleMessages.regiGua,
								text: "Pedido actualizado!"
							});
							usProg.init();
							K.closeWindow(p.$w.attr('id'));
						}, 'json');
					}
				},
				'Cancelar': {
					icon: 'fa-ban',
					type: 'danger',
					f: function () {
						usProg.init();
					}
				}
			},
			onContentLoaded: function () {
				p.$w = $('#mainPanel');
				K.block({
					$element: p.$w
				});
				K.grid({
					$el: p.$w.find('[name=gridRaci]'),
					cols: ['', 'Lunes', 'Martes', 'Mi&eacute;rcoles', 'Jueves', 'Viernes', 'S&aacute;bado', 'Domingo'],
					search: false,
					pagination: false,
					onlyHtml: true
				});
				$.post('us/prog/get', {
					_id: p.id
				}, function (data) {
					p.$w.find('[name=periodo]').html(ciHelper.date.format.bd_ymd(data.ini) + ' (Lunes)  al  ' + ciHelper.date.format.bd_ymd(data.fin) + ' (Domingo)');
					/* DESAYUNO */
					var $row = $('<tr class="item">');
					$row.append('<td>Desayuno</td>');
					for (var i = 0, j = 7; i < j; i++) {
						$row.append('<td><span name="receta' + i + '"></span>&nbsp;<button class="btn btn-info"><i class="fa fa-search"></i></button></td>');
						$row.find('td:last').find('span').html(data.desa[i].descr + ' <br />(' + data.desa[i].valor_nut + ' calor&iacute;as)').data('data', data.desa[i]);
					}
					p.$w.find('[name=gridRaci] tbody').append($row);
					/* ALMUERZO */
					var $row = $('<tr class="item">');
					$row.append('<td>Almuerzo</td>');
					for (var i = 0, j = 7; i < j; i++) {
						$row.append('<td><span name="receta' + i + '"></span>&nbsp;<button class="btn btn-info"><i class="fa fa-search"></i></button></td>');
						$row.find('td:last').find('span').html(data.almu[i].descr + ' <br />(' + data.almu[i].valor_nut + ' calor&iacute;as)').data('data', data.almu[i]);
					}
					p.$w.find('[name=gridRaci] tbody').append($row);
					/* CENA */
					var $row = $('<tr class="item">');
					$row.append('<td>Cena</td>');
					for (var i = 0, j = 7; i < j; i++) {
						$row.append('<td><span name="receta' + i + '"></span>&nbsp;<button class="btn btn-info"><i class="fa fa-search"></i></button></td>');
						$row.find('td:last').find('span').html(data.cena[i].descr + ' <br />(' + data.cena[i].valor_nut + ' calor&iacute;as)').data('data', data.cena[i]);
					}
					p.$w.find('[name=gridRaci] tbody').append($row);
					/* DIETA */
					var $row = $('<tr class="item">');
					$row.append('<td>Dieta</td>');
					for (var i = 0, j = 7; i < j; i++) {
						$row.append('<td><span name="receta' + i + '"></span>&nbsp;<button class="btn btn-info"><i class="fa fa-search"></i></button></td>');
						$row.find('td:last').find('span').html(data.diet[i].descr + ' <br />(' + data.diet[i].valor_nut + ' calor&iacute;as)').data('data', data.diet[i]);
					}
					p.$w.find('[name=gridRaci] tbody').append($row);
					p.$w.find('[name=gridRaci] tbody button').click(function () {
						var $this = $(this);
						usRece.windowSelect({
							callback: function (data) {
								$this.closest('td').find('span').html(data.descr + ' <br />(' + data.valor_nut + ' calor&iacute;as)').data('data', data);
							}
						});
					});
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
			contentURL: 'us/prog/edit',
			buttons: {
				'Cancelar': {
					icon: 'fa-ban',
					type: 'danger',
					f: function () {
						usProg.init();
					}
				}
			},
			onContentLoaded: function () {
				p.$w = $('#mainPanel');
				K.block({
					$element: p.$w
				});
				K.grid({
					$el: p.$w.find('[name=gridRaci]'),
					cols: ['', 'Lunes', 'Martes', 'Mi&eacute;rcoles', 'Jueves', 'Viernes', 'S&aacute;bado', 'Domingo'],
					search: false,
					pagination: false,
					onlyHtml: true
				});
				$.post('us/prog/get', {
					_id: p.id
				}, function (data) {
					p.$w.find('[name=periodo]').html(ciHelper.date.format.bd_ymd(data.ini) + ' (Lunes)  al  ' + ciHelper.date.format.bd_ymd(data.fin) + ' (Domingo)');
					/* DESAYUNO */
					var $row = $('<tr class="item">');
					$row.append('<td>Desayuno</td>');
					for (var i = 0, j = 7; i < j; i++) {
						$row.append('<td><span name="receta' + i + '"></span></td>');
						$row.find('td:last').find('span').html(data.desa[i].descr + ' <br />(' + data.desa[i].valor_nut + ' calor&iacute;as)');
					}
					p.$w.find('[name=gridRaci] tbody').append($row);
					/* ALMUERZO */
					var $row = $('<tr class="item">');
					$row.append('<td>Almuerzo</td>');
					for (var i = 0, j = 7; i < j; i++) {
						$row.append('<td><span name="receta' + i + '"></span></td>');
						$row.find('td:last').find('span').html(data.almu[i].descr + ' <br />(' + data.almu[i].valor_nut + ' calor&iacute;as)');
					}
					p.$w.find('[name=gridRaci] tbody').append($row);
					/* CENA */
					var $row = $('<tr class="item">');
					$row.append('<td>Cena</td>');
					for (var i = 0, j = 7; i < j; i++) {
						$row.append('<td><span name="receta' + i + '"></span></td>');
						$row.find('td:last').find('span').html(data.cena[i].descr + ' <br />(' + data.cena[i].valor_nut + ' calor&iacute;as)');
					}
					p.$w.find('[name=gridRaci] tbody').append($row);
					/* DIETA */
					var $row = $('<tr class="item">');
					$row.append('<td>Dieta</td>');
					for (var i = 0, j = 7; i < j; i++) {
						$row.append('<td><span name="receta' + i + '"></span></td>');
						$row.find('td:last').find('span').html(data.diet[i].descr + ' <br />(' + data.diet[i].valor_nut + ' calor&iacute;as)');
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
		return usProg;
	}
);