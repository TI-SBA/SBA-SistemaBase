usRepe = {
	init: function (p) {
		if (p == null) p = {};
		K.initMode({
			mode: 'us',
			action: 'usRepe',
			titleBar: {
				title: 'Recepci&oacute;n de Pedidos'
			}
		});

		new K.Panel({
			contentURL: 'us/pedi/repe',
			buttons: {
				'Cerrar Pedidos de la Semana': {
					icon: 'fa-save',
					type: 'success',
					f: function () {
						ciHelper.confirm('&#191;Desea Cerrar el Envio de Pedido de Raciones para esta semana</b>&#63;',
							function () {
								K.sendingInfo();
								var data = {
									ini: p.ini,
									fin: p.fin,
									desa: p.desa,
									almu: p.almu,
									cena: p.cena,
									diet: p.diet
								};
								$.post('us/pedi/cerrar', data, function () {
									K.clearNoti();
									K.notification({
										title: 'Pedidos Aceptados',
										text: 'La habilitaci&oacute;n se realiz&oacute; con &eacute;xito!'
									});
									window.open('us/repo/print_repe?ini=' + p.ini);
									usRepo.init();
								});
							},
							function () {
								$.noop();
							}, 'Aceptaci&oacute;n de Envios');
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
					$el: p.$w.find('[name=gridResu]'),
					cols: ['', 'Lunes', 'Martes', 'Mi&eacute;rcoles', 'Jueves', 'Viernes', 'S&aacute;bado', 'Domingo'],
					search: false,
					pagination: false,
					onlyHtml: true
				});
				K.grid({
					$el: p.$w.find('[name=gridRaci]'),
					cols: ['', 'Lunes', 'Martes', 'Mi&eacute;rcoles', 'Jueves', 'Viernes', 'S&aacute;bado', 'Domingo'],
					search: false,
					pagination: false,
					onlyHtml: true
				});
				$.post('us/pedi/get_repe', function (data) {
					if (data != null) {
						$.extend(p, {
							ini: data.ini,
							fin: data.fin,
							desa: [0, 0, 0, 0, 0, 0, 0],
							almu: [0, 0, 0, 0, 0, 0, 0],
							cena: [0, 0, 0, 0, 0, 0, 0],
							diet: [0, 0, 0, 0, 0, 0, 0]
						});
						p.$w.find('[name=periodo]').html(data.ini + ' (Lunes)  al  ' + data.fin + ' (Domingo)');
						if (data.pedidos != null) {
							for (var i = 0, j = data.pedidos.length; i < j; i++) {
								var $row = $('<tr class="item">');
								$row.append('<td style="text-align: center;" colspan="8"><b>' + data.pedidos[i].oficina.nomb.toUpperCase() + '</b></td>');
								p.$w.find('[name=gridRaci] tbody').append($row);
								/* DESAYUNO */
								var $row = $('<tr class="item">');
								$row.append('<td>Desayuno</td>');
								for (var ii = 0, jj = 7; ii < jj; ii++) {
									$row.append('<td>' + data.pedidos[i].desa[ii] + '</td>');
									p.desa[ii] += parseInt(data.pedidos[i].desa[ii]);
								}
								p.$w.find('[name=gridRaci] tbody').append($row);
								/* ALMUERZO */
								var $row = $('<tr class="item">');
								$row.append('<td>Almuerzo</td>');
								for (var ii = 0, jj = 7; ii < jj; ii++) {
									$row.append('<td>' + data.pedidos[i].almu[ii] + '</td>');
									p.almu[ii] += parseInt(data.pedidos[i].almu[ii]);
								}
								p.$w.find('[name=gridRaci] tbody').append($row);
								/* CENA */
								var $row = $('<tr class="item">');
								$row.append('<td>Cena</td>');
								for (var ii = 0, jj = 7; ii < jj; ii++) {
									$row.append('<td>' + data.pedidos[i].cena[ii] + '</td>');
									p.cena[ii] += parseInt(data.pedidos[i].cena[ii]);
								}
								p.$w.find('[name=gridRaci] tbody').append($row);
								/* DIETA */
								var $row = $('<tr class="item">');
								$row.append('<td>Dieta</td>');
								for (var ii = 0, jj = 7; ii < jj; ii++) {
									$row.append('<td>' + data.pedidos[i].diet[ii] + '</td>');
									p.diet[ii] += parseInt(data.pedidos[i].diet[ii]);
								}
								p.$w.find('[name=gridRaci] tbody').append($row);
							}
							var $row = $('<tr class="item">');
							$row.append('<td>Desayuno</td>');
							for (var ii = 0, jj = 7; ii < jj; ii++) {
								$row.append('<td>' + p.desa[ii] + '</td>');
							}
							p.$w.find('[name=gridResu] tbody').append($row);
							/* ALMUERZO */
							var $row = $('<tr class="item">');
							$row.append('<td>Almuerzo</td>');
							for (var ii = 0, jj = 7; ii < jj; ii++) {
								$row.append('<td>' + p.almu[ii] + '</td>');
							}
							p.$w.find('[name=gridResu] tbody').append($row);
							/* CENA */
							var $row = $('<tr class="item">');
							$row.append('<td>Cena</td>');
							for (var ii = 0, jj = 7; ii < jj; ii++) {
								$row.append('<td>' + p.cena[ii] + '</td>');
							}
							p.$w.find('[name=gridResu] tbody').append($row);
							/* DIETA */
							var $row = $('<tr class="item">');
							$row.append('<td>Dieta</td>');
							for (var ii = 0, jj = 7; ii < jj; ii++) {
								$row.append('<td>' + p.diet[ii] + '</td>');
							}
							p.$w.find('[name=gridResu] tbody').append($row);
						}
					}
					K.unblock({
						$element: p.$w
					});
				}, 'json');
			}
		});
	}
};
define(
	['mg/enti', 'us/rece', 'us/pedi', 'us/repo'],
	function (mgEnti, usRece, usPedi, usRepo) {
		return usRepe;
	}
);