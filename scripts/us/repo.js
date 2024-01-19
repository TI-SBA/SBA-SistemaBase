usRepo = {
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
			nomb: item.nomb
		};
	},
	init: function () {
		K.initMode({
			mode: 'us',
			action: 'usRepo',
			titleBar: {
				title: 'Reportes'
			}
		});

		/*	new K.Panel({
				onContentLoaded: function () {
					var $grid = new K.grid({
						cols: ['', '', {
							n: 'Nombre',
							f: 'nomb'
						}, {
							n: '&Uacute;ltima Modificaci&oacute;n',
							f: 'fecmod'
						}],
						data: 'us/coci/lista',
						params: {},
						itemdescr: 'cocina(s)',
						toolbarHTML: '<button name="btnAgregar" class="btn btn-success"><i class="fa fa-plus"></i> Agregar</button>',
						onContentLoaded: function ($el) {
							$el.find('[name=btnAgregar]').click(function () {
								usRepo.windowNew();
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
							$row.append('<td>' + usRepo.states[data.estado].label + '</td>');
							$row.append('<td>' + data.nomb + '</td>');
							$row.append('<td>' + ciHelper.date.format.bd_ymdhi(data.fecmod) + '<br />' + mgEnti.formatName(data.trabajador) + '</td>');
							$row.data('id', data._id.$id).dblclick(function () {
								usRepo.windowDetails({
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
										usRepo.windowDetails({
											id: K.tmp.data('id'),
											nomb: K.tmp.find('td:eq(2)').html()
										});
									},
									'conMenListEd_edi': function (t) {
										usRepo.windowEdit({
											id: K.tmp.data('id'),
											nomb: K.tmp.find('td:eq(2)').html()
										});
									},
									'conMenListEd_hab': function (t) {
										ciHelper.confirm('&#191;Desea <b>Habilitar</b> la Cocina <b>' + K.tmp.find('td:eq(2)').html() + '</b>&#63;',
											function () {
												K.sendingInfo();
												$.post('us/coci/save', {
													_id: K.tmp.data('id'),
													estado: 'H'
												}, function () {
													K.clearNoti();
													K.notification({
														title: 'Cocina Habilitada',
														text: 'La habilitaci&oacute;n se realiz&oacute; con &eacute;xito!'
													});
													usRepo.init();
												});
											},
											function () {
												$.noop();
											}, 'Habilitaci&oacute;n de Cocina');
									},
									'conMenListEd_des': function (t) {
										ciHelper.confirm('&#191;Desea <b>Deshabilitar</b> la Cocina <b>' + K.tmp.find('td:eq(2)').html() + '</b>&#63;',
											function () {
												K.sendingInfo();
												$.post('us/coci/save', {
													_id: K.tmp.data('id'),
													estado: 'D'
												}, function () {
													K.clearNoti();
													K.notification({
														title: 'Cocina Deshabilitada',
														text: 'La deshabilitaci&oacute;n se realiz&oacute; con &eacute;xito!'
													});
													usRepo.init();
												});
											},
											function () {
												$.noop();
											}, 'Deshabilitaci&oacute;n de Cocina');
									}
								}
							});
							return $row;
						}
					});
				}
			});*/
	},
	/*
	windowNew: function (p) {
		if (p == null) p = {};
		new K.Modal({
			id: 'windowNewTipo',
			title: 'Nuevo Cocina',
			contentURL: 'us/coci/edit',
			store: false,
			width: 380,
			height: 130,
			buttons: {
				"Guardar": {
					icon: 'fa-save',
					type: 'success',
					f: function () {
						K.clearNoti();
						var data = {
							nomb: p.$w.find('[name=nomb]').val()
						};
						if (data.nomb == '') {
							p.$w.find('[name=nomb]').focus();
							return K.notification({
								title: ciHelper.titleMessages.infoReq,
								text: 'Debe ingresar la denominaci&oacute;n de Tipo!',
								type: 'error'
							});
						}
						K.sendingInfo();
						p.$w.find('#div_buttons button').attr('disabled', 'disabled');
						$.post("us/coci/save", data, function (result) {
							K.clearNoti();
							K.notification({
								title: ciHelper.titleMessages.regiGua,
								text: "Tipo agregado!"
							});
							usRepo.init();
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
				p.$w.find('[name=btnCta]').click(function () {
					ctPcon.windowSelect({
						callback: function (data) {
							p.$w.find('[name=cuenta]').html(data.cod + ' - ' + data.descr).data('data', data);
						},
						bootstrap: true
					});
				}).button({
					icons: {
						primary: 'ui-icon-search'
					}
				});
			}
		});
	},
	windowEdit: function (p) {
		new K.Modal({
			id: 'windowEditTipo',
			title: 'Editar Tipo ' + p.nomb,
			contentURL: 'us/coci/edit',
			store: false,
			width: 380,
			height: 130,
			buttons: {
				"Guardar": {
					icon: 'fa-save',
					type: 'success',
					f: function () {
						K.clearNoti();
						var data = {
							_id: p.id,
							nomb: p.$w.find('[name=nomb]').val()
						};
						if (data.nomb == '') {
							p.$w.find('[name=nomb]').focus();
							return K.notification({
								title: ciHelper.titleMessages.infoReq,
								text: 'Debe ingresar la denominaci&oacute;n de Tipo!',
								type: 'error'
							});
						}
						K.sendingInfo();
						p.$w.find('#div_buttons button').attr('disabled', 'disabled');
						$.post("us/coci/save", data, function (result) {
							K.clearNoti();
							K.notification({
								title: ciHelper.titleMessages.regiAct,
								text: "Tipo actualizado!"
							});
							usRepo.init();
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
				p.$w = $('#windowEditTipo');
				K.block({
					$element: p.$w
				});
				$.post('us/coci/get', {
					_id: p.id
				}, function (data) {
					p.$w.find('[name=nomb]').val(data.nomb);
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
			title: 'Seleccionar Cocina',
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
					data: 'us/coci/lista',
					params: {},
					itemdescr: 'unidad(es)',
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
	*/
};
define(
	['mg/enti'],
	function (mgEnti) {
		return usRepo;
	}
);