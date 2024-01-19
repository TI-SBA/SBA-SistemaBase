usRece = {
	states: {
		H: {
			descr: "Habilitado",
			color: "green",
			label: '<span class="label label-success">Habilitado</span>'
		},
		D: {
			descr: "Deshabilitado",
			color: "#CCCCCC",
			label: '<span class="label label-default">Deshabilitado</span>'
		}
	},
	dbRel: function (item) {
		return {
			_id: item._id.$id,
			descr: item.descr,
			valor_nut: item.valor_nut
		};
	},
	init: function () {
		K.initMode({
			mode: 'us',
			action: 'usRece',
			titleBar: {
				title: 'Recetas'
			}
		});

		new K.Panel({
			onContentLoaded: function () {
				var $grid = new K.grid({
					cols: ['', '', {
						n: 'Descripci&oacute;n',
						f: 'descr'
					}, 'Calor&iacute;as', {
						n: '&Uacute;ltima Modificaci&oacute;n',
						f: 'fecmod'
					}],
					data: 'us/rece/lista',
					params: {},
					itemdescr: 'Receta(s)',
					toolbarHTML: '<button name="btnAgregar" class="btn btn-success"><i class="fa fa-plus"></i> Agregar</button>',
					onContentLoaded: function ($el) {
						$el.find('[name=btnAgregar]').click(function () {
							usRece.windowNew();
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
						$row.append('<td>' + usRece.states[data.estado].label + '</td>');
						$row.append('<td>' + data.descr + '</td>');
						$row.append('<td>' + data.valor_nut + ' calor&iacute;as</td>');
						$row.append('<td>' + ciHelper.date.format.bd_ymdhi(data.fecmod) + '<br />' + mgEnti.formatName(data.trabajador) + '</td>');
						$row.data('id', data._id.$id).dblclick(function () {
							usRece.windowDetails({
								_id: $(this).data('id'),
								nomb: $(this).find('td:eq(2)').html()
							});
						}).data('estado', data.estado).contextMenu("conMenListEd", {
							onShowMenu: function ($row, menu) {
								$('#conMenListEd_ver', menu).remove();
								if ($row.data('estado') == 'H') $('#conMenListEd_hab', menu).remove();
								else $('#conMenListEd_edi,#conMenListEd_des', menu).remove();
								return menu;
							},
							bindings: {
								'conMenListEd_ver': function (t) {
									usRece.windowDetails({
										id: K.tmp.data('id'),
										nomb: K.tmp.find('td:eq(2)').html()
									});
								},
								'conMenListEd_edi': function (t) {
									usRece.windowEdit({
										id: K.tmp.data('id'),
										nomb: K.tmp.find('td:eq(2)').html()
									});
								},
								'conMenListEd_hab': function (t) {
									ciHelper.confirm('&#191;Desea <b>Habilitar</b> la Receta <b>' + K.tmp.find('td:eq(2)').html() + '</b>&#63;',
										function () {
											K.sendingInfo();
											$.post('us/rece/save', {
												_id: K.tmp.data('id'),
												estado: 'H'
											}, function () {
												K.clearNoti();
												K.notification({
													title: 'Receta Habilitada',
													text: 'La habilitaci&oacute;n se realiz&oacute; con &eacute;xito!'
												});
												usRece.init();
											});
										},
										function () {
											$.noop();
										}, 'Habilitaci&oacute;n de Receta');
								},
								'conMenListEd_des': function (t) {
									ciHelper.confirm('&#191;Desea <b>Deshabilitar</b> la Receta <b>' + K.tmp.find('td:eq(2)').html() + '</b>&#63;',
										function () {
											K.sendingInfo();
											$.post('us/rece/save', {
												_id: K.tmp.data('id'),
												estado: 'D'
											}, function () {
												K.clearNoti();
												K.notification({
													title: 'Receta Deshabilitada',
													text: 'La deshabilitaci&oacute;n se realiz&oacute; con &eacute;xito!'
												});
												usRece.init();
											});
										},
										function () {
											$.noop();
										}, 'Deshabilitaci&oacute;n de Receta');
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
		new K.Modal({
			id: 'windowNewTipo',
			title: 'Nuevo Receta',
			contentURL: 'us/rece/edit',
			store: false,
			width: 700,
			height: 450,
			buttons: {
				"Guardar": {
					icon: 'fa-save',
					type: 'success',
					f: function () {
						K.clearNoti();
						var data = {
							descr: p.$w.find('[name=descr]').val(),
							valor_nut: p.$w.find('[name=valor_nut]').val(),
							ingredientes: []
						};
						if (data.descr == '') {
							p.$w.find('[name=descr]').focus();
							return K.notification({
								title: ciHelper.titleMessages.infoReq,
								text: 'Debe ingresar la denominaci&oacute;n de Receta!',
								type: 'error'
							});
						}
						if (data.valor_nut == '') {
							p.$w.find('[name=valor_nut]').focus();
							return K.notification({
								title: ciHelper.titleMessages.infoReq,
								text: 'Debe ingresar el Valor Nutricional de Receta!',
								type: 'error'
							});
						}
						for (var i = 0, j = p.$w.find('[name=gridIngr] tbody').length; i < j; i++) {
							var $row = p.$w.find('[name=gridIngr] tbody tr').eq(i);
							if ($row.find('[name=cant]').val() != '' && $row.find('[name=cant]').val() > 0) {
								data.ingredientes.push({
									ingrediente: usIngr.dbRel($row.data('data')),
									cant: $row.find('[name=cant]').val()
								});
							} else {
								$row.find('[name=cant]').focus();
								return K.notification({
									title: ciHelper.titleMessages.infoReq,
									text: 'Debe ingresar una cantidad!',
									type: 'error'
								});
							}
						}
						K.sendingInfo();
						p.$w.find('#div_buttons button').attr('disabled', 'disabled');
						$.post("us/rece/save", data, function (result) {
							K.clearNoti();
							K.notification({
								title: ciHelper.titleMessages.regiGua,
								text: "Receta agregada!"
							});
							usRece.init();
							K.closeWindow(p.$w.attr('id'));
						}, 'json');
					}
				},
				"Cancelar": {
					icon: 'fa-ban',
					type: 'danger',
					f: function () {
						K.closeWindow(p.$w.attr('id'));
					}
				}
			},
			onClose: function () {
				p = null;
			},
			onContentLoaded: function () {
				p.$w = $('#windowNewTipo');
				new K.grid({
					$el: p.$w.find('[name=gridIngr]'),
					cols: ['', 'Ingredientes', 'Cantidad por porci&oacute;n', 'Unidad'],
					search: false,
					pagination: false,
					toolbarHTML: '<button name="btnAgregar" class="btn btn-success"><i class="fa fa-plus"></i> Agregar Ingrediente</button>',
					onlyHtml: true,
					onContentLoaded: function ($el) {
						$el.find('[name=btnAgregar]').click(function () {
							usIngr.windowSelect({
								callback: function (data) {
									if (p.$w.find('[name=gridIngr] [name=' + data._id.$id + ']').length == 0) {
										var $row = $('<tr class="item" name="' + data._id.$id + '">');
										$row.append('<td>' +
											'<button class="btn btn-danger"><i class="fa fa-trash-o"></i></button>' +
											'</td>');
										$row.append('<td>' + data.nomb + '</td>');
										$row.append('<td><input type="number" name="cant" value="0" /></td>');
										$row.append('<td>' + data.unidad.nomb + '</td>');
										$row.find('button').click(function () {
											var $row = $(this).closest('.item');
											$row.remove();
										});
										$row.data('data', data);
										p.$w.find('[name=gridIngr] tbody').append($row);
									} else {
										K.notification({
											title: ciHelper.titleMessages.infoReq,
											text: 'El ingrediente ya fue seleccionado!',
											type: 'error'
										});
									}
								}
							});
						});
					}
				});
			}
		});
	},
	windowEdit: function (p) {
		if (p == null) p = {};
		new K.Modal({
			id: 'windowEdit',
			title: 'Editar Receta',
			contentURL: 'us/rece/edit',
			store: false,
			width: 700,
			height: 450,
			buttons: {
				"Guardar": {
					icon: 'fa-save',
					type: 'success',
					f: function () {
						K.clearNoti();
						var data = {
							_id: p.id,
							descr: p.$w.find('[name=descr]').val(),
							valor_nut: p.$w.find('[name=valor_nut]').val(),
							ingredientes: []
						};
						if (data.descr == '') {
							p.$w.find('[name=descr]').focus();
							return K.notification({
								title: ciHelper.titleMessages.infoReq,
								text: 'Debe ingresar la denominaci&oacute;n de Receta!',
								type: 'error'
							});
						}
						if (data.valor_nut == '') {
							p.$w.find('[name=valor_nut]').focus();
							return K.notification({
								title: ciHelper.titleMessages.infoReq,
								text: 'Debe ingresar el Valor Nutricional de Receta!',
								type: 'error'
							});
						}
						for (var i = 0, j = p.$w.find('[name=gridIngr] tbody').length; i < j; i++) {
							var $row = p.$w.find('[name=gridIngr] tbody tr').eq(i);
							if ($row.find('[name=cant]').val() != '' && $row.find('[name=cant]').val() > 0) {
								data.ingredientes.push({
									ingrediente: usIngr.dbRel($row.data('data')),
									cant: $row.find('[name=cant]').val()
								});
							} else {
								$row.find('[name=cant]').focus();
								return K.notification({
									title: ciHelper.titleMessages.infoReq,
									text: 'Debe ingresar una cantidad!',
									type: 'error'
								});
							}
						}
						K.sendingInfo();
						p.$w.find('#div_buttons button').attr('disabled', 'disabled');
						$.post("us/rece/save", data, function (result) {
							K.clearNoti();
							K.notification({
								title: ciHelper.titleMessages.regiGua,
								text: "Receta agregada!"
							});
							usRece.init();
							K.closeWindow(p.$w.attr('id'));
						}, 'json');
					}
				},
				"Cancelar": {
					icon: 'fa-ban',
					type: 'danger',
					f: function () {
						K.closeWindow(p.$w.attr('id'));
					}
				}
			},
			onClose: function () {
				p = null;
			},
			onContentLoaded: function () {
				p.$w = $('#windowEdit');
				K.block({
					$element: p.$w
				});
				new K.grid({
					$el: p.$w.find('[name=gridIngr]'),
					cols: ['', 'Ingredientes', 'Cantidad por porci&oacute;n', 'Unidad'],
					search: false,
					pagination: false,
					toolbarHTML: '<button name="btnAgregar" class="btn btn-success"><i class="fa fa-plus"></i> Agregar Ingrediente</button>',
					onlyHtml: true,
					onContentLoaded: function ($el) {
						$el.find('[name=btnAgregar]').click(function () {
							usIngr.windowSelect({
								callback: function (data) {
									if (p.$w.find('[name=gridIngr] [name=' + data._id.$id + ']').length == 0) {
										var $row = $('<tr class="item" name="' + data._id.$id + '">');
										$row.append('<td>' +
											'<button class="btn btn-danger"><i class="fa fa-trash-o"></i></button>' +
											'</td>');
										$row.append('<td>' + data.nomb + '</td>');
										$row.append('<td><input type="number" name="cant" value="0" /></td>');
										$row.append('<td>' + data.unidad.nomb + '</td>');
										$row.find('button').click(function () {
											var $row = $(this).closest('.item');
											$row.remove();
										});
										$row.data('data', data);
										p.$w.find('[name=gridIngr] tbody').append($row);
									} else {
										K.notification({
											title: ciHelper.titleMessages.infoReq,
											text: 'El ingrediente ya fue seleccionado!',
											type: 'error'
										});
									}
								}
							});
						});
					}
				});
				$.post('us/rece/get', {
					_id: p.id
				}, function (data) {
					p.$w.find('[name=descr]').val(data.descr);
					p.$w.find('[name=valor_nut]').val(data.valor_nut);
					for (var i = 0, j = data.ingredientes.length; i < j; i++) {
						var $row = $('<tr class="item" name="' + data.ingredientes[i].ingrediente._id.$id + '">');
						$row.append('<td>' +
							'<button class="btn btn-danger"><i class="fa fa-trash-o"></i></button>' +
							'</td>');
						$row.append('<td>' + data.ingredientes[i].ingrediente.nomb + '</td>');
						$row.append('<td><input type="number" name="cant" value="' + data.ingredientes[i].cant + '" /></td>');
						$row.append('<td>' + data.ingredientes[i].ingrediente.unidad.nomb + '</td>');
						$row.find('button').click(function () {
							var $row = $(this).closest('.item');
							$row.remove();
						});
						$row.data('data', data.ingredientes[i].ingrediente);
						p.$w.find('[name=gridIngr] tbody').append($row);
					}
					K.unblock({
						$element: p.$w
					});
				}, 'json');
			}
		});
	},
	windowSelect: function (p) {
		new K.Modal({
			id: 'windowSelect',
			content: '<div name="tmp"></div>',
			width: 750,
			height: 400,
			title: 'Seleccionar Receta',
			buttons: {
				"Seleccionar": {
					icon: 'fa-check',
					type: 'info',
					f: function () {
						if (p.$w.find('.highlights').data('data') != null) {
							p.callback(p.$w.find('.highlights').data('data'));
							K.closeWindow(p.$w.attr('id'));
						} else {
							K.clearNoti();
							return K.notification({
								title: ciHelper.titleMessages.infoReq,
								text: 'Debe seleccionar un item!',
								type: 'error'
							});
						}
					}
				},
				"Cancelar": {
					icon: 'fa-ban',
					type: 'danger',
					f: function () {
						K.closeWindow(p.$w.attr('id'));
					}
				}
			},
			onClose: function () {
				p = null;
			},
			onContentLoaded: function () {
				p.$w = $('#windowSelect');
				p.$grid = new K.grid({
					$el: p.$w.find('[name=tmp]'),
					cols: ['', 'Descripci&oacute;n', 'Valor Nutricional'],
					data: 'us/rece/lista',
					params: {},
					itemdescr: 'receta(s)',
					onLoading: function () {
						K.block({
							$element: p.$w
						});
					},
					onComplete: function () {
						K.unblock({
							$element: p.$w
						});
					},
					fill: function (data, $row) {
						$row.append('<td><button name="btnGrid">M&aacute;s Acciones</button></td>');
						$row.append('<td>' + data.descr + '</td>');
						$row.append('<td>' + data.valor_nut + ' calor&iacute;as</td>');
						$row.data('data', data).dblclick(function () {
							p.$w.find('.modal-footer button:first').click();
						}).contextMenu('conMenListSel', {
							bindings: {
								'conMenListSel_sel': function (t) {
									p.$w.find('.modal-footer button:first').click();
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
	['mg/enti'],
	function (mgEnti) {
		return usRece;
	}
);