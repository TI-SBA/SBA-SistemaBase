tsMoviEfe = {
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
				$p.find('[name=tsMoviEfe]').click(function(){ tsMoviEfe.init(); }).addClass('ui-state-highlight');
				$p.find('[name=tsMoviCue]').click(function(){ tsMoviCue.init(); });
				$p.find('[name=tsMoviBan]').click(function(){ tsMoviBan.init(); });
				$p.find('[name=tsMoviCjb]').click(function(){ tsMoviCjb.init(); });
			},'json');
		}
		K.initMode({
			mode: 'ts',
			action: 'tsMoviEfe',
			titleBar: {
				title: 'Movimientos: Efectivo'
			}
		});
		
		new K.Panel({
			id: 'mainPanel',
			contentURL: 'ts/movi',
			store: false,
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
			    	tsMoviEfe.loadData({url: 'ts/movi/lista_efe'});
			    }).change();
				$mainPanel.find('[name=btnExportar]').click(function(){
					var mes = +$mainPanel.find('[name=periodo]').data('mes')+1;
					window.open("ts/repo/movi_efe?periodo="+$mainPanel.find('[name=periodo]').data('ano')+(mes<10?'0'+mes:mes)+'00');
				}).button({icons: {primary: 'ui-icon-extlink'}});
				$mainPanel.find('[name=nomb]').html(K.session.titular.nomb);
				$mainPanel.find('[name=ruc]').html(K.session.titular.docident[0].num);
				K.unblock({$element: $('#pageWrapperMain')});
			}
		});
		$('#pageWrapperMain').layout();
	},
	loadData: function(params){
		var mes = +$mainPanel.find('[name=periodo]').data('mes')+1;
		$.post(params.url,{
			periodo: $mainPanel.find('[name=periodo]').data('ano')+(mes<10?'0'+mes:mes)+'00'
		},function(data){
			if(data!=null){
				for(var i=0,j=data.length; i<j; i++){
					var $row = $mainPanel.find('.gridReference').clone(),
					result = data[i];
					$row.find('li:eq(0)').html(result.tipo_doc+' '+result.num_doc);
					$row.find('li:eq(1)').html(ciHelper.dateFormatOnlyDay(result.fecreg));
					$row.find('li:eq(2)').html(result.descr);
					$row.find('li:eq(3)').html(result.cuenta.cod);
					$row.find('li:eq(4)').html(result.cuenta.descr);
					if(result.tipo=='D') $row.find('li:eq(5)').html(ciHelper.formatMon(result.monto));
					else $row.find('li:eq(6)').html(ciHelper.formatMon(result.monto));
					$row.find('li:eq(7)').html(result.estado_sunat);
					$row.wrapInner('<a class="item">');
					$mainPanel.find('.gridBody').append($row.children());
				}
			}else{
				K.notification({
					title: 'Periodo sin movimientos',
					text: 'El periodo seleccionado no cuenta con movimientos para el efectivo!',
					type: 'info'
				});
			}
		},'json');
	}
};