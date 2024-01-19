ctCoalSali = {
	init: function(){
		if($('#pageWrapper [child=coal]').length<=0){
			var $p = $('#pageWrapperLeft');
			$.post('ct/navg/coal',function(data){
				for(var i=0; i<data.length; i++){
					var result = data[i];
					var $row = $p.find('.gridReference').clone();
					$row.find('li:eq(0)').html( '<span class="ui-icon '+result.icon+'"></span>' + result.descr )
						.css({
							"padding-left": "10px",
							"min-width": "186px",
							"max-width": "186px"
						});
					$row.wrapInner('<a name="'+result.name+'" class="item" href="javascript: void(0);" child="coal" />');
					$p.find("[name=ctCoal]").after( $row.children() );
				}
				$p.find('[name=ctCoal]').data('coal',$('#pageWrapper [child=coal]:first').data('coal'));
				$p.find('[name=ctCoalSali]').click(function(){ ctCoalSali.init(); }).addClass('ui-state-highlight');
				$p.find('[name=ctCoalEntr]').click(function(){ ctCoalEntr.init(); });
			},'json');
		}
		K.initMode({
			mode: 'ct',
			action: 'ctCoalSali',
			titleBar: {
				title: 'Contabilidad de Salidas de Almacen'
			}
		});
		
		new K.Panel({
			id: 'mainPanel',
			contentURL: 'lg/kard/index_resu',
			onContentLoaded: function(){
				$('#No-Results').hide();
				$mainPanel = $('#mainPanel');
				$mainPanel.resize(function(){
					$mainPanel.find('.grid:eq(1)').height(($mainPanel.height()-$mainPanel.find('.grid:eq(0)').height()-$mainPanel.find('div:first').outerHeight())+'px');
				}).resize();
				$mainPanel.find('.grid:eq(0)').css('overflow','hidden');
				$mainPanel.find('.grid:eq(1)').scroll(function(){
					$mainPanel.find('.grid:eq(0)').scrollLeft($(this).scrollLeft());
				});
				var tmp = new Date();
				$mainPanel.find('[name=periodo]').datepicker( {
			        showButtonPanel: true,
			        dateFormat: 'MM yy',
			        onClose: function(dateText, inst) { 
			            var month = $("#ui-datepicker-div .ui-datepicker-month :selected").val();
			            var year = $("#ui-datepicker-div .ui-datepicker-year :selected").val();
			            $(this).data('mes',month).data('ano',year);
			            $(this).val($.datepicker.formatDate('MM yy', new Date(year, month, 1)));
			            ctCoalSali.loadData();
			        }
			    }).focus(function(){
			    	$('.ui-datepicker-calendar').css('display','none');
			    }).val(ciHelper.meses[tmp.getMonth()]+' '+tmp.getFullYear())
			    .data('mes',+tmp.getMonth())
			    .data('ano',tmp.getFullYear());
				$mainPanel.find('[name=tipo]').change(function(){
					ctCoalSali.loadData();
				}).change().remove();
			}
		});
		$('#pageWrapperMain').layout();
	},
	loadData: function(){
		K.clearNoti();
		$mainPanel.find('.gridBody').empty();
		$mainPanel.find('.gridHeader ul [name]').remove();
		$mainPanel.find('.gridReference ul [name]').remove();
	    $.post('lg/kard/resu', {
	    	tipo: 'S',
	    	mes: +$mainPanel.find('[name=periodo]').data('mes')+1,
	    	ano: $mainPanel.find('[name=periodo]').data('ano')
	    }, function(data){
			if ( data.data!=null ) {
				var $header = $mainPanel.find('.gridHeader ul'),
				$ref = $mainPanel.find('.gridReference ul');
				for (i=0; i < data.cuentas.length; i++) {
					var result = data.cuentas[i];
					var $row = $('.gridReference','#mainPanel').clone();
					var $li = $('li',$row);
					$li.eq(0).html(result.cod);
					$li.eq(1).attr('name','total').data('total',0);
					$row.wrapInner('<a class="item" name="'+result._id.$id+'" />');
		        	$("#mainPanel .gridBody").append( $row.children() );
				}
				if(data.data.peco!=null){
					for (i=0; i < data.data.peco.length; i++) {
						var $items = $mainPanel.find('.item');
						var result = data.data.peco[i],
						orga = result.solicitante.cargo.organizacion;
						if($header.find('[name='+orga._id.$id+']').length<1){
							$header.append('<li name="'+orga._id.$id+'" style="min-width:150px;max-width:150px" class="ui-button ui-widget ui-state-default ui-button-text-only">'+orga.nomb+'</li>');
							$ref.append('<li name="'+orga._id.$id+'" style="min-width:150px;max-width:150px"></li>');
							$items.each(function(){
								$(this).find('ul').append('<li name="'+orga._id.$id+'-'+$(this).attr('name')+'" style="min-width:150px;max-width:150px"></li>');
							});
						}
						for(var j=0; j<result.productos.length; j++){
							var clasif = result.productos[j].producto.clasif._id.$id,
							cuenta = '';
							for(var k=0; k<data.clasif.length; k++){
								if(data.clasif[k]._id.$id==clasif){
									cuenta = data.clasif[k].cuenta._id.$id;
									k = data.clasif.length;
								}
							}
							var $cell = $mainPanel.find('.gridBody [name='+orga._id.$id+'-'+cuenta+']'),
							tot = $cell.data('total');
							if(tot==null) tot = 0;
							$cell.html('S/.'+(tot+parseFloat(result.productos[j].subtotal))).data('total',tot+parseFloat(result.productos[j].subtotal));
							var $cell = $mainPanel.find('.gridBody [name='+cuenta+'] [name=total]'),
							tot = $cell.data('total');
							if(tot==null) tot = 0;
							$cell.html('S/.'+(tot+parseFloat(result.productos[j].subtotal))).data('total',tot+parseFloat(result.productos[j].subtotal)); 
						}
					}
				}
				if(data.data.orde!=null){
					for (i=0; i < data.data.orde.length; i++) {
						var $items = $mainPanel.find('.item');
						var result = data.data.orde[i];
						for(var j=0; j<result.afectacion.length; j++){
							var orga = result.afectacion[j].organizacion;
							if($header.find('[name='+orga._id.$id+']').length<1){
								$header.append('<li name="'+orga._id.$id+'" style="min-width:150px;max-width:150px" class="ui-button ui-widget ui-state-default ui-button-text-only">'+orga.nomb+'</li>');
								$ref.append('<li name="'+orga._id.$id+'" style="min-width:150px;max-width:150px"></li>');
								$items.each(function(){
									$(this).find('ul').append('<li name="'+orga._id.$id+'-'+$(this).attr('name')+'" style="min-width:150px;max-width:150px"></li>');
								});
							}
							for(var l=0; l<result.afectacion[j].gasto.length; l++){
								var clasif = result.afectacion[j].gasto[l].clasif._id.$id,
								cuenta = '';
								for(var k=0; k<data.clasif.length; k++){
									if(data.clasif[k]._id.$id==clasif){
										cuenta = data.clasif[k].cuenta._id.$id;
										k = data.clasif.length;
									}
								}
								var $cell = $mainPanel.find('.gridBody [name='+orga._id.$id+'-'+cuenta+']'),
								tot = $cell.data('total');
								if(tot==null) tot = 0;
								
								
								
								
								console.log(result.afectacion[j].gasto[l].monto);
								
								
								
								$cell.html(ciHelper.formatMon(tot+parseFloat(result.afectacion[j].gasto[l].monto))).data('total',tot+parseFloat(result.afectacion[j].gasto[l].monto));
								var $cell = $mainPanel.find('.gridBody [name='+cuenta+'] [name=total]'),
								tot = $cell.data('total');
								if(tot==null) tot = 0;
								$cell.html(ciHelper.formatMon(tot+parseFloat(result.afectacion[j].gasto[l].monto))).data('total',tot+parseFloat(result.afectacion[j].gasto[l].monto));
							}
						}
					}
				}
				if(data.data.nota!=null){
					for (i=0; i < data.data.nota.length; i++) {
						var $items = $mainPanel.find('.item');
						var result = data.data.nota[i];
						if($header.find('[name='+result.motivo+']').length<1){
							$header.append('<li name="'+result.motivo+'" style="min-width:150px;max-width:150px" class="ui-button ui-widget ui-state-default ui-button-text-only">'+result.motivo+'</li>');
							$ref.append('<li name="'+result.motivo+'" style="min-width:150px;max-width:150px"></li>');
							$items.each(function(){
								$(this).find('ul').append('<li name="'+result.motivo+'-'+$(this).attr('name')+'" style="min-width:150px;max-width:150px"></li>');
							});
						}
						for(var j=0; j<result.productos.length; j++){
							var clasif = result.productos[j].producto.clasif._id.$id,
							cuenta = '';
							for(var k=0; k<data.clasif.length; k++){
								if(data.clasif[k]._id.$id==clasif){
									cuenta = data.clasif[k].cuenta._id.$id;
									k = data.clasif.length;
								}
							}
							var $cell = $mainPanel.find('.gridBody [name='+result.motivo+'-'+cuenta+']'),
							tot = $cell.data('total');
							if(tot==null) tot = 0;
							$cell.html('S/.'+(tot+parseFloat(result.productos[j].subtotal))).data('total',tot+parseFloat(result.productos[j].subtotal));
							var $cell = $mainPanel.find('.gridBody [name='+cuenta+'] [name=total]'),
							tot = $cell.data('total');
							if(tot==null) tot = 0;
							$cell.html('S/.'+(tot+parseFloat(result.productos[j].subtotal))).data('total',tot+parseFloat(result.productos[j].subtotal));
						}
					}
				}
				var large = $mainPanel.find('.gridBody ul:last li').length;
				$mainPanel.find('.gridBody li').each(function(){
					//if($(this).html()=='') $(this).html('S/.0.00');
				});
				var $items = $mainPanel.find('.item');
				var $row = $('.gridReference','#mainPanel').clone();
				$li = $('li',$row);
				$li.addClass('ui-state-default ui-button-text-only');
				$li.eq(0).html('Total');
				for(var i=1; i<$li.length; i++){
					var total = 0;
					for(var j=0; j<$items.length; j++){
						var tmp = $items.eq(j).find('li').eq(i).data('total');
						if(tmp!=null) tmp = parseFloat(tmp);
						else tmp = 0;
						total = total + tmp;
					}
					$li.eq(i).html('S/.'+total);
				}
				$row.wrapInner('<a class="item" />');
	        	$("#mainPanel .gridBody").append( $row.children() );
	      } else {
	    	  K.notification({title: 'Registros no encontrados',text: 'No hay movimientos relacionados al periodo seleccionado!',type: 'error'});
	      }
	      $('#mainPanel').resize();
	      K.unblock({$element: $('#pageWrapperMain')});
	    }, 'json');
	}
};