tsMoviBan = {
	init: function(){
		if($('#pageWrapper [child=movi]').length<=0){
			var $p = $('#pageWrapperLeft');
			$.post('ts/navg/movi',function(data){
				for(var i=0; i<data.length; i++){
					var result = data[i];
					var $row = $p.find('.gridReference').clone();
					$row.find('li:eq(0)').html( '<span class="ui-icon '+result.icon+'"></span>' + result.descr )
						.css({
							"padding-left": "10px",
							"min-width": "186px",
							"max-width": "186px"
						});
					$row.wrapInner('<a name="'+result.name+'" class="item" href="javascript: void(0);" child="movi" />');
					$p.find("[name=tsMovi]").after( $row.children() );
				}
				$p.find('[name=tsMovi]').data('movi',$('#pageWrapper [child=movi]:first').data('movi'));
				$p.find('[name=tsMoviBan]').click(function(){ tsMoviBan.init(); }).addClass('ui-state-highlight');
				$p.find('[name=tsMoviEfe]').click(function(){ tsMoviEfe.init(); });
				$p.find('[name=tsMoviCue]').click(function(){ tsMoviCue.init(); });
				$p.find('[name=tsMoviCjb]').click(function(){ tsMoviCjb.init(); });
			},'json');
		}
		K.initMode({
			mode: 'ts',
			action: 'tsMoviBan',
			titleBar: {
				title: 'Movimientos: Banco'
			}
		});
		
		new K.Panel({
			id: 'mainPanel',
			contentURL: 'ts/movi/ban',
			onContentLoaded: function(){
				$('#No-Results').hide();
				$mainPanel = $('#mainPanel');
				$mainPanel.find('[name=btnAgregar]').click(function(){
					K.incomplete();
				}).button({icons: {primary: 'ui-icon-plusthick'}}).remove();
				$mainPanel.resize(function(){
					$mainPanel.find('.grid:eq(1)').height(($mainPanel.height()-$mainPanel.find('.grid:eq(0)').height()-$mainPanel.find('table:first').height())+'px');
				}).resize();
				$mainPanel.find('.grid:eq(0)').css('overflow','hidden');
				$mainPanel.find('.grid').eq(1).bind('scroll',function(){
					$mainPanel.find('.grid').eq(0).scrollLeft($mainPanel.find('.grid').eq(1).scrollLeft());
				});
				var date = new Date();
				$mainPanel.find('[name=periodo]').datepicker( {
			        dateFormat: 'MM yy',
			        onClose: function(dateText, inst) { 
			            var month = $("#ui-datepicker-div .ui-datepicker-month :selected").val();
			            var year = $("#ui-datepicker-div .ui-datepicker-year :selected").val();
			            $(this).data('mes',month).data('ano',year);
			            $(this).val($.datepicker.formatDate('MM yy', new Date(year, month, 1)));
			            $(this).change();
			        }
			    }).focus(function(){
			    	$('.ui-datepicker-calendar').css('display','none');
			    }).val(ciHelper.meses[date.getMonth()]+' '+date.getFullYear())
			    .data('mes',+date.getMonth())
			    .data('ano',date.getFullYear()).change(function(){
			    	$mainPanel.find('.gridBody').empty();
			    	tsMoviBan.loadData({url: 'ts/movi/lista_ban'});
			    });
				$.post('ts/ctban/all',function(data){
					var $cbo = $mainPanel.find('[name=ctban]');
					if(data!=null){
						for(var i=0,j=data.length; i<j; i++){
							$cbo.append('<option value="'+data[i]._id.$id+'">'+data[i].cod+'</option>');
						}
					}
					$cbo.change(function(){
						$mainPanel.find('[name=periodo]').change();
					}).change();
					K.unblock({$element: $('#pageWrapperMain')});
				},'json');
				$mainPanel.find('[name=btnExportar]').click(function(){
					var mes = +$mainPanel.find('[name=periodo]').data('mes')+1;
					window.open("ts/repo/movi_ban?periodo="+$mainPanel.find('[name=periodo]').data('ano')+(mes<10?'0'+mes:mes)+'00&ctban='+$mainPanel.find('[name=ctban] option:selected').val());
				}).button({icons: {primary: 'ui-icon-extlink'}});
			}
		});
		$('#pageWrapperMain').layout();
	},
	loadData: function(params){
		var mes = +$mainPanel.find('[name=periodo]').data('mes')+1;
		if($mainPanel.find('[name=ctban] option').length==0){
			return K.notification({
				title: ciHelper.titleMessages.infoReq,
				text: 'Debe ingresar cuentas bancarias para iniciar los movimientos!',
				type: 'error'
			});
		}
		$.post(params.url,{
			ctban: $mainPanel.find('[name=ctban] option:selected').val(),
			periodo: $mainPanel.find('[name=periodo]').data('ano')+(mes<10?'0'+mes:mes)+'00'
		},function(data){
			if(data!=null){
				var saldo = 0;
				for(var i=0,j=data.length; i<j; i++){
					var $row = $mainPanel.find('.gridReference').clone(),
					result = data[i];
					$row.find('li:eq(0)').html(ciHelper.dateFormatOnlyDay(result.fec));
					$row.find('li:eq(1)').html(result.tipo_doc+' '+result.num_doc);
					$row.find('li:eq(2)').html(result.tipo_origen+' '+result.num_origen);
					if(result.moneda=='S') $row.find('li:eq(3)').html(result.detalle);
					else $row.find('li:eq(3)').html(result.detalle+' - '.ciHelper.formatMon(result.monto,'D')+' - TC '+ciHelper.formatMon(result.tc));
					if(result.tipo=='D'){
						saldo += result.monto;
						$row.find('li:eq(4)').html(ciHelper.formatMon(result.monto));
					}else{
						saldo -= result.monto;
						$row.find('li:eq(5)').html(ciHelper.formatMon(result.monto));
					}
					$row.find('li:eq(6)').html(ciHelper.formatMon(saldo));
					$row.wrapInner('<a class="item">');
					$mainPanel.find('.gridBody').append($row.children());
				}
			}else{
				K.notification({
					title: 'Periodo sin movimientos',
					text: 'El periodo seleccionado no cuenta con movimientos para bancos!',
					type: 'info'
				});
			}
		},'json');
	}
};