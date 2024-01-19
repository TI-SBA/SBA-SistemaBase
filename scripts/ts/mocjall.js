/*******************************************************************************
Cajas chicas */
tsMocjAll = {
	types: {
		F: 'Factura',
		R: 'Recibo de Caja',
		B: 'Boleta de Venta'
	},
	init: function(){
		if($('#pageWrapper [child=mocj]').length<=0){
			var $p = $('#pageWrapperLeft');
			$.post('ts/navg/mocj',function(data){
				for(var i=0; i<data.length; i++){
					var result = data[i];
					var $row = $p.find('.gridReference').clone();
					$row.find('li:eq(0)').html( '<span class="ui-icon '+result.icon+'"></span>' + result.descr )
						.css({
							"padding-left": "10px",
							"min-width": "186px",
							"max-width": "186px"
						});
					$row.wrapInner('<a name="'+result.name+'" class="item" href="javascript: void(0);" child="mocj" />');
					$p.find("[name=tsMocj]").after( $row.children() );
				}
				$p.find('[name=tsMocj]').data('mocj',$('#pageWrapper [child=mocj]:first').data('mocj'));
				$p.find('[name=tsMocjAll]').click(function(){ tsMocjAll.init(); }).addClass('ui-state-highlight');
				$p.find('[name=tsMocjDep]').click(function(){ tsMocjDep.init(); });
			},'json');
		}
		K.initMode({
			mode: 'ts',
			action: 'tsMocjAll',
			titleBar: {
				title: 'Movimientos de Caja Chica por dependencia'
			}
		});
		
		new K.Panel({
			id: 'mainPanel',
			contentURL: 'ts/mocj',
			onContentLoaded: function(){
				$('#No-Results').hide();
				$mainPanel = $('#mainPanel');
				$mainPanel.find('[name=obj]').html( 'movimiento(s)' );
				$mainPanel.find("[name=moreresults]").css('float','right').button({icons: {primary: 'ui-icon-triangle-1-s'}});
				$mainPanel.resize(function(){
					$mainPanel.find('.grid:eq(1)').height(($mainPanel.height()-$mainPanel.find('.grid:eq(0)').height()-$mainPanel.find('div:first').outerHeight()-$('.div-bottom').height())+'px');
				}).resize();
				$mainPanel.find('.grid:eq(0)').css('overflow','hidden');
				$mainPanel.find('.grid:eq(1)').scroll(function(){
					$mainPanel.find('.grid:eq(0)').scrollLeft($(this).scrollLeft());
				});
				$mainPanel.find('[name=btnAgregar]').remove();
				$mainPanel.find('[name=btnGenerar]').remove();
				$mainPanel.find('[name=btnOrga]').hide();
				$mainPanel.find('[name=btnRendicion]').hide();
				$mainPanel.find('[name=btnRendicion]').click(function(){
					tsMocjDep.windowVer();
				}).button({icons: {primary: 'ui-icon-document'}});
				$mainPanel.find('[name=numero]').change(function(){
					$mainPanel.find('.gridBody').empty();
					if($(this).find('option:selected').attr('estado')=='A'){
						$mainPanel.find('[name=btnRendicion]').hide();
					}else{
						$mainPanel.find('[name=btnRendicion]').show();
					}
					tsMocjAll.loadData({page: 1,url: 'ts/mocj/lista'});
				});
				$mainPanel.find('div:first').append('<button name="btnAuxs">Imprimir Auxiliar Standar</button>');
				$mainPanel.find('[name=btnAuxs]').click(function(){
					window.open('ts/repo/auxs_caja?id='+$mainPanel.find('[name=numero] option:selected').val());
				}).button({icons: {primary: 'ui-icon-print'}});
				K.unblock({$element: $('#pageWrapperMain')});
				$.post('ts/cjch/all',function(data){
					var $select = $mainPanel.find('[name=caja]').change(function(){
						var $this = $(this),
						$select = $mainPanel.find('[name=numero]').empty();
						$mainPanel.find('[name=organomb]').html($this.find('option:selected').data('data').organizacion.nomb);
						K.block({$element: $('#pageWrapperMain')});
						$mainPanel.find('.gridBody').empty();
						$.post('ts/mocj/get_saldos','id='+$this.find('option:selected').val(),function(data){
							if(data!=null){
								for(var i=0,j=data.length; i<j; i++){
									$select.append('<option estado="'+data[i].estado+'" value="'+data[i]._id.$id+'" num="'+data[i].cod+'">'+data[i].cod+' ('+ciHelper.dateFormatOnlyDay(data[i].fecreg)+')'+'</option>');
									if(data[i].estado=='A'){
										$select.find('option:last').html(data[i].cod+' (Actual)');
									}
								}
							}
							$mainPanel.find('[name=numero]').change();
						},'json');
					});
					if(data==null){
						var error = 'No hay cajas chicas creadas!';
						K.block({$element: $('#pageWrapperMain'),message: error});
						return K.notification({title: 'Caja Chicas no encontradas',text: error,type: 'info'});
					}
					for(var i=0,j=data.length; i<j; i++){
						$select.append('<option value="'+data[i]._id.$id+'" actual="'+data[i].cod+'">'+data[i].nomb+'</option>');
						$select.find('option:last').data('data',data[i]);
					}
					$select.change();
				},'json');
			}
		});
		$('#pageWrapperMain').layout();
	},
	loadData: function(params){
		$.extend(params,{
			caja: $mainPanel.find('[name=caja] option:selected').val(),
			num: $mainPanel.find('[name=numero] option:selected').val(),
			page_rows: 20,
		    page: (params.page) ? params.page : 1
		});
	    $.post(params.url, params, function(data){
			if ( data.paging.total_page_items > 0 ) { 
				for (i=0; i < data.paging.total_page_items; i++) {
					result = data.items[i];
					var $row = $('.gridReference','#mainPanel').clone();
					$li = $('li',$row);
					$li.eq(0).html( result.item );
					$li.eq(1).html( ciHelper.dateFormat(result.fecreg) );
					$li.eq(2).html( tsMocjAll.types[result.documento] );
					$li.eq(3).html( result.num_doc );
					$li.eq(4).html( ciHelper.enti.formatName(result.beneficiario) );
					$li.eq(5).html( result.concepto );
					$li.eq(6).html( result.organizacion.nomb );
					$li.eq(7).html( ciHelper.formatMon(result.monto) );
					$li.eq(8).html( result.clasificador.cod );
					$row.wrapInner('<a class="item" href="javascript: void(0);" />');
					$row.find('a').data('id',result._id.$id).data('data',result);
		        	$("#mainPanel .gridBody").append( $row.children() );
		        }
		        count = $("#mainPanel .gridBody .item").length;
		        $('#No-Results').hide();
		        $('#Results [name=showing]').html( count );
		        $('#Results [name=founded]').html( data.paging.total_items );
		        $('#Results').show();
		        
		        $moreresults = $("[name=moreresults]").unbind();
		        if (parseFloat(data.paging.page) < parseFloat(data.paging.total_pages)) {
					$("#mainPanel .gridFoot").show();
					$moreresults.click( function(){
						$('#mainPanel .grid').scrollTo( $("#mainPanel .gridBody a:last"), 800 );
						params.page = parseFloat(data.paging.page) + 1;
						tsMocjAll.loadData(params);
						$(this).button( "option", "disabled", true );
					});
					$( "[name=moreresults]",'#mainPanel').button( "option", "disabled", false );
		        }else{
					$("#mainPanel .gridFoot").hide();
					$( "[name=moreresults]",'#mainPanel').button( "option", "disabled", true );
		        }
	      } else {
	        $('#No-Results').show();
	        $('#Results').hide();
	        $( "[name=moreresults]",'#mainPanel').button( "option", "disabled", true );
	      }
	      $('#mainPanel').resize();
	      K.unblock({$element: $('#pageWrapperMain')});
	    }, 'json');
	}
};