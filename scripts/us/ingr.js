usIngr = {
	states: {
		H: {
			descr: 'Habilitado',
			color: 'green',
			label: '<span class="label label-success">Habilitado</span>'
		},
		D: {
			descr: 'Deshabilitado',
			color: '#CCCCCC',
			label: '<span class="label label-default">Deshabilitado</span>'
		}
	},
	dbRel: function (item) {
		return {
			_id: item._id.$id,
			nomb: item.nomb,
			unidad: {
				_id: item.unidad._id.$id,
				nomb: item.unidad.nomb
			}
		};
	},
	init: function () {
		K.initMode({
			mode: 'us',
			action: 'usIngr',
			titleBar: {
				title: 'Ingredientes'
			}
		});

		new K.Panel({
			onContentLoaded: function () {
				var $grid = new K.grid({
					cols: ['', '', {
						n: 'Nombre',
						f: 'nomb'
					}, 'Stock', {
						n: '&Uacute;ltima Modificaci&oacute;n',
						f: 'fecmod'
					}, {
						n: 'Modificado por',
						f: 'trabajador.fullname'
					}],
					data: 'us/ingr/lista',
					params: {},
					itemdescr: 'tipo(s)',
					toolbarHTML: '<button name="btnAgregar" class="btn btn-success"><i class="fa fa-plus"></i> Agregar</button>',
					onContentLoaded: function ($el) {
						$el.find('[name=btnAgregar]').click(function () {
							usIngr.windowNew();
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
						$row.append('<td>' + usIngr.states[data.estado].label + '</td>');
						$row.append('<td>' + data.nomb + '</td>');
						$row.append('<td>' + data.cant + ' ' + data.unidad.nomb + '</td>');
						$row.append('<td>' + ciHelper.date.format.bd_ymdhi(data.fecmod) + '</td>');
						$row.append('<td>' + mgEnti.formatName(data.trabajador) + '</td>');
						$row.data('id', data._id.$id).dblclick(function () {
							usIngr.windowEdit({
								id: $(this).data('id'),
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
									usIngr.windowDetails({
										id: K.tmp.data('id'),
										nomb: K.tmp.find('td:eq(2)').html()
									});
								},
								'conMenListEd_edi': function (t) {
									usIngr.windowEdit({
										id: K.tmp.data('id'),
										nomb: K.tmp.find('td:eq(2)').html()
									});
								},
								'conMenListEd_hab': function (t) {
									ciHelper.confirm('&#191;Desea <b>Habilitar</b> el Tipo de Local <b>' + K.tmp.find('td:eq(2)').html() + '</b>&#63;',
										function () {
											K.sendingInfo();
											$.post('us/ingr/save', {
												_id: K.tmp.data('id'),
												estado: 'H'
											}, function () {
												K.clearNoti();
												K.notification({
													title: 'Tipo de Local Habilitado',
													text: 'La habilitaci&oacute;n se realiz&oacute; con &eacute;xito!'
												});
												usIngr.init();
											});
										},
										function () {
											$.noop();
										}, 'Habilitaci&oacute;n de Tipo de Local');
								},
								'conMenListEd_des': function (t) {
									ciHelper.confirm('&#191;Desea <b>Deshabilitar</b> el Tipo de Local <b>' + K.tmp.find('td:eq(2)').html() + '</b>&#63;',
										function () {
											K.sendingInfo();
											$.post('us/ingr/save', {
												_id: K.tmp.data('id'),
												estado: 'D'
											}, function () {
												K.clearNoti();
												K.notification({
													title: 'Tipo de Local Deshabilitado',
													text: 'La deshabilitaci&oacute;n se realiz&oacute; con &eacute;xito!'
												});
												usIngr.init();
											});
										},
										function () {
											$.noop();
										}, 'Deshabilitaci&oacute;n de Tipo de Local');
								}
							}
						});
						return $row;
					}
				});
			}
		});
	},
	windowEquiv: function (p) {
		new K.Modal({
			id: 'windowEquiv',
			title: 'Nueva Equivalencia',
			contentURL: 'us/ingr/equiv',
			width: 380,
			height: 180,
			buttons: {
				'Actualizar Equivalencia': {
					icon: 'fa-refresh',
					type: 'success',
					f: function () {
						var data = {
							cant: p.$w.find('[name=cant]').val(),
							unidad: p.$w.find('[name=unid] option:selected').data('data')
						};
						if (data.cant == '') {
							p.$w.find('[name=cant]').focus();
							return K.notification({
								title: ciHelper.titleMessages.infoReq,
								text: 'Debe ingresar una cantidad!',
								type: 'error'
							});
						}
						p.callback(data);
						K.closeWindow(p.$w.attr('id'));
					}
				},
				'Cancelar': {
					icon: 'fa-close',
					type: 'danger',
					f: function () {
						K.closeWindow(p.$w.attr('id'));
					}
				}
			},
			onContentLoaded: function () {
				p.$w = $('#windowEquiv');
				var $cbo = p.$w.find('[name=unid]');
				for (var i = 0, j = p.all.length; i < j; i++) {
					if (p.all[i]._id.$id != p.unid._id.$id) {
						$cbo.append('<option value="' + p.all[i]._id.$id + '">' + p.all[i].nomb + '</option>');
						$cbo.find('option:last').data('data', p.all[i]);
					}
				}
				if (p.cant != null)
					p.$w.find('[name=cant]').val(p.cant);
				if (p.un != null)
					p.$w.find('[name=unid]').selectVal(p.un);
				p.$w.find('[name=cant]').focus();
			}
		});
	},
	windowNew: function (p) {
		if (p == null) p = {};
		new K.Modal({
			id: 'windowNewTipo',
			title: 'Nuevo Ingrediente',
			contentURL: 'us/ingr/edit',
			store: false,
			width: 450,
			height: 400,
			buttons: {
				"Guardar": {
					icon: 'fa-save',
					type: 'success',
					f: function () {
						K.clearNoti();
						var data = {
							nomb: p.$w.find('[name=nomb]').val(),
							cant: p.$w.find('[name=cant]').val(),
							unidad: usUnid.dbRel(p.$w.find('[name=unid] option:selected').data('data')),
							equiv: []
						};
						if (data.nomb == '') {
							p.$w.find('[name=nomb]').focus();
							return K.notification({
								title: ciHelper.titleMessages.infoReq,
								text: 'Debe ingresar el nombre del Ingrediente!',
								type: 'error'
							});
						}
						if (data.cant == '') {
							p.$w.find('[name=cant]').focus();
							return K.notification({
								title: ciHelper.titleMessages.infoReq,
								text: 'Debe ingresar la cantidad del Ingrediente!',
								type: 'error'
							});
						}
						for (var i = 0, j = p.$w.find('[name=gridConv] tbody tr').length; i < j; i++) {
							var tmp = p.$w.find('[name=gridConv] tbody tr').eq(i).data('data');
							data.equiv.push({
								unidad: usUnid.dbRel(tmp.unidad),
								cant: tmp.cant
							});
						}
						K.sendingInfo();
						p.$w.find('#div_buttons button').attr('disabled', 'disabled');
						$.post("us/ingr/save", data, function (result) {
							K.clearNoti();
							K.notification({
								title: ciHelper.titleMessages.regiGua,
								text: "Ingrediente agregado!"
							});
							usIngr.init();
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
					$el: p.$w.find('[name=gridConv]'),
					cols: ['', 'Unidad', 'Equivalente'],
					search: false,
					pagination: false,
					toolbarHTML: '<button name="btnAgregar" class="btn btn-success"><i class="fa fa-plus"></i> Agregar</button>',
					onlyHtml: true,
					onContentLoaded: function ($el) {
						$el.find('[name=btnAgregar]').click(function () {
							usIngr.windowEquiv({
								all: p.unid,
								unid: p.$w.find('[name=unid] option:selected').data('data'),
								callback: function (data) {
									if (p.$w.find('[name=gridConv] [name=' + data.unidad._id.$id + ']').length === 0) {
										var $row = $('<tr class="item" name="' + data.unidad._id.$id + '">');
										$row.append('<td>' +
											'<button class="btn btn-info"><i class="fa fa-pencil"></i></button>' +
											'<button class="btn btn-danger"><i class="fa fa-trash-o"></i></button>' +
											'</td>');
										$row.append('<td>' + data.unidad.nomb + '</td>');
										$row.append('<td>Equivale a ' + data.cant + ' Unidades</td>');
										$row.find('button:eq(0)').click(function () {
											var $row = $(this).closest('.item');
											usIngr.windowEquiv({
												all: p.unid,
												cant: $row.data('data').cant,
												un: $row.data('data').unidad._id.$id,
												unid: p.$w.find('[name=unid] option:selected').data('data'),
												callback: function (data) {
													$row.find('td:eq(1)').html(data.unidad.nomb);
													$row.find('td:eq(2)').html('Equivale a ' + data.cant + ' Unidades');
													$row.data('data', data);
												}
											});
										});
										$row.find('button:eq(1)').click(function () {
											var $row = $(this).closest('.item');
											$row.remove();
										});
										$row.data('data', data);
										p.$w.find('[name=gridConv] tbody').append($row);
									} else {
										K.notification({
											title: ciHelper.titleMessages.infoReq,
											text: 'La unidad ya fue seleccionada!',
											type: 'error'
										});
									}
								}
							});
						});
					}
				});
				K.block({
					$element: p.$w
				});
				$.post('us/unid/all', function (data) {
					p.unid = data;
					var $cbo = p.$w.find('[name=unid]');
					for (var i = 0, j = p.unid.length; i < j; i++) {
						$cbo.append('<option value="' + p.unid[i]._id.$id + '">' + p.unid[i].nomb + '</option>');
						$cbo.find('option:last').data('data', p.unid[i]);
					}
					K.unblock({
						$element: p.$w
					});
				}, 'json');
			}
		});
	},
	windowEdit: function (p) {
		new K.Modal({
			id: 'windowEdit',
			title: 'Editar Ingrediente ' + p.nomb,
			contentURL: 'us/ingr/edit',
			store: false,
			width: 450,
			height: 400,
			buttons: {
				"Guardar": {
					icon: 'fa-save',
					type: 'success',
					f: function () {
						K.clearNoti();
						var data = {
							_id: p.id,
							nomb: p.$w.find('[name=nomb]').val(),
							cant: p.$w.find('[name=cant]').val(),
							unidad: usUnid.dbRel(p.$w.find('[name=unid] option:selected').data('data')),
							equiv: []
						};
						if (data.nomb == '') {
							p.$w.find('[name=nomb]').focus();
							return K.notification({
								title: ciHelper.titleMessages.infoReq,
								text: 'Debe ingresar el nombre del Ingrediente!',
								type: 'error'
							});
						}
						if (data.cant == '') {
							p.$w.find('[name=cant]').focus();
							return K.notification({
								title: ciHelper.titleMessages.infoReq,
								text: 'Debe ingresar la cantidad del Ingrediente!',
								type: 'error'
							});
						}
						for (var i = 0, j = p.$w.find('[name=gridConv] tbody tr').length; i < j; i++) {
							var tmp = p.$w.find('[name=gridConv] tbody tr').eq(i).data('data');
							data.equiv.push({
								unidad: usUnid.dbRel(tmp.unidad),
								cant: tmp.cant
							});
						}
						K.sendingInfo();
						p.$w.find('#div_buttons button').attr('disabled', 'disabled');
						$.post("us/ingr/save", data, function (result) {
							K.clearNoti();
							K.notification({
								title: ciHelper.titleMessages.regiAct,
								text: "Ingrediente actualizado!"
							});
							usIngr.init();
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
					$el: p.$w.find('[name=gridConv]'),
					cols: ['', 'Unidad', 'Equivalente'],
					search: false,
					pagination: false,
					toolbarHTML: '<button name="btnAgregar" class="btn btn-success"><i class="fa fa-plus"></i> Agregar</button>',
					onlyHtml: true,
					onContentLoaded: function ($el) {
						$el.find('[name=btnAgregar]').click(function () {
							usIngr.windowEquiv({
								all: p.unid,
								unid: p.$w.find('[name=unid] option:selected').data('data'),
								callback: function (data) {
									if (p.$w.find('[name=gridConv] [name=' + data.unidad._id.$id + ']').length == 0) {
										var $row = $('<tr class="item" name="' + data.unidad._id.$id + '">');
										$row.append('<td>' +
											'<button class="btn btn-info"><i class="fa fa-pencil"></i></button>' +
											'<button class="btn btn-danger"><i class="fa fa-trash-o"></i></button>' +
											'</td>');
										$row.append('<td>' + data.unidad.nomb + '</td>');
										$row.append('<td>Equivale a ' + data.cant + ' Unidades</td>');
										$row.find('button:eq(0)').click(function () {
											var $row = $(this).closest('.item');
											usIngr.windowEquiv({
												all: p.unid,
												un: $row.data('data').unidad._id.$id,
												cant: $row.data('data').cant,
												unid: p.$w.find('[name=unid] option:selected').data('data'),
												callback: function (data) {
													$row.find('td:eq(1)').html(data.unidad.nomb);
													$row.find('td:eq(2)').html('Equivale a ' + data.cant + ' Unidades');
													$row.data('data', data);
												}
											});
										});
										$row.find('button:eq(1)').click(function () {
											var $row = $(this).closest('.item');
											$row.remove();
										});
										$row.data('data', data);
										p.$w.find('[name=gridConv] tbody').append($row);
									} else {
										K.notification({
											title: ciHelper.titleMessages.infoReq,
											text: 'La unidad ya fue seleccionada!',
											type: 'error'
										});
									}
								}
							});
						});
					}
				});
				$.post('us/unid/all', function (data) {
					p.unid = data;
					var $cbo = p.$w.find('[name=unid]');
					for (var i = 0, j = p.unid.length; i < j; i++) {
						$cbo.append('<option value="' + p.unid[i]._id.$id + '">' + p.unid[i].nomb + '</option>');
						$cbo.find('option:last').data('data', p.unid[i]);
					}
					$.post('us/ingr/get', {
						_id: p.id
					}, function (data) {
						p.$w.find('[name=nomb]').val(data.nomb);
						p.$w.find('[name=cant]').val(data.cant);
						p.$w.find('[name=unid]').selectVal(data.unidad._id.$id);
						for (var i = 0, j = data.equiv.length; i < j; i++) {
							var $row = $('<tr class="item" name="' + data.equiv[i].unidad._id.$id + '">');
							$row.append('<td>' +
								'<button class="btn btn-info"><i class="fa fa-pencil"></i></button>' +
								'<button class="btn btn-danger"><i class="fa fa-trash-o"></i></button>' +
								'</td>');
							$row.append('<td>' + data.equiv[i].unidad.nomb + '</td>');
							$row.append('<td>Equivale a ' + data.equiv[i].cant + ' Unidades</td>');
							$row.find('button:eq(0)').click(function () {
								var $row = $(this).closest('.item');
								usIngr.windowEquiv({
									all: p.unid,
									un: $row.data('data').unidad._id.$id,
									cant: $row.data('data').cant,
									unid: p.$w.find('[name=unid] option:selected').data('data'),
									callback: function (data) {
										$row.find('td:eq(1)').html(data.unidad.nomb);
										$row.find('td:eq(2)').html('Equivale a ' + data.cant + ' Unidades');
										$row.data('data', data);
									}
								});
							});
							$row.find('button:eq(1)').click(function () {
								var $row = $(this).closest('.item');
								$row.remove();
							});
							$row.data('data', data.equiv[i]);
							p.$w.find('[name=gridConv] tbody').append($row);
						}
						K.unblock({
							$element: p.$w
						});
					}, 'json');
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
			title: 'Seleccionar Ingrediente',
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
					cols: ['', 'Nombre'],
					data: 'us/ingr/lista',
					params: {},
					itemdescr: 'ingrediente(s)',
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
						$row.append('<td>' + data.nomb + '</td>');
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
  ['mg/enti', 'us/unid'],
	function (mgEnti, usUnid) {
		return usIngr;
	}
);