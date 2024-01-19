tsMoviCjb = {
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
				$p.find('[name=tsMoviCue]').click(function(){ tsMoviCue.init(); });
				$p.find('[name=tsMoviEfe]').click(function(){ tsMoviEfe.init(); });
				$p.find('[name=tsMoviBan]').click(function(){ tsMoviBan.init(); });
				$p.find('[name=tsMoviCjb]').click(function(){ tsMoviCjb.init(); }).addClass('ui-state-highlight');
			},'json');
		}
		K.initMode({
			mode: 'ts',
			action: 'tsMoviCjb',
			titleBar: {
				title: 'Movimientos del Libro Caja y Bancos'
			}
		});
		
		new K.Panel({
			id: 'mainPanel',
			contentURL: 'ts/movi/cjb',
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
			    	tsMoviCjb.loadData({url: 'ts/movi/lista_cjb'});
			    }).change();
				$mainPanel.find('[name=btnExportar]').click(function(){					
					var mes = +$mainPanel.find('[name=periodo]').data('mes')+1;
					window.open("ts/repo/movi_cjb?ano="+$mainPanel.find('[name=periodo]').data('ano')+'&mes='+mes+"");
				}).button({icons: {primary: 'ui-icon-extlink'}});
				K.unblock({$element: $('#pageWrapperMain')});
			}
		});
		$('#pageWrapperMain').layout();
	},
	loadData: function(params){
		var mes = +$mainPanel.find('[name=periodo]').data('mes')+1;
		$.post('ts/movi/cjb_body',function(data){
			$mainPanel.find('#mainContent').empty();
			$mainPanel.find('#mainContent').append(data);
			$.post(params.url,{
				ano: $mainPanel.find('[name=periodo]').data('ano'),
				mes: mes+""
			},function(data){
				if(data.items.length>0){	
					/** Grid */
					$mainPanel.find('.gridHeader [name=h_cuentas_debe]').css({'min-width':data.cuentas_debe.length*100+'px'});
					$mainPanel.find('.gridHeader [name=t_cuentas_debe]').css({'min-width':data.cuentas_debe.length*100+'px'});
					for(i=0;i<data.cuentas_debe.length;i++){
						$mainPanel.find('.gridHeader [name=cuentas_debe]').append('<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="min-width:100px;max-width:100px;">'+data.cuentas_debe[i].cod+'</li>');
						$mainPanel.find('.gridReference [name=b_c_d]').before('<li id="debe_'+data.cuentas_debe[i]._id.$id+'" style="min-width:100px;max-width:100px;"></li>');
					}
					$mainPanel.find('.gridHeader [name=h_cuentas_haber]').css({'min-width':data.cuentas_haber.length*100+'px'});
					$mainPanel.find('.gridHeader [name=t_cuentas_haber]').css({'min-width':data.cuentas_haber.length*100+'px'});
					for(i=0;i<data.cuentas_haber.length;i++){
						$mainPanel.find('.gridHeader [name=cuentas_haber]').append('<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="min-width:100px;max-width:100px;">'+data.cuentas_haber[i].cod+'</li>');
						$mainPanel.find('.gridReference [name=l_c_h]').after('<li id="haber_'+data.cuentas_haber[i]._id.$id+'" style="min-width:100px;max-width:100px;"></li>');					
					}					
					/** /Grid */
					
					/** Body items */
					var debe_total = 0;
					var haber_total = 0;
					for(var i=0,k=data.items.length; i<k; i++){
						var $row = $mainPanel.find('.gridReference').clone(),
						result = data.items[i];
						for(j=0;j<result.cuentas_debe.length;j++){
							$row.find('#debe_'+result.cuentas_debe[j].cuenta._id.$id).html(K.round(result.cuentas_debe[j].monto,2));
						}
						$row.find('.fecha').html(ciHelper.dateFormatOnlyDay(result.fecreg));
						$row.find('.doc_tipo').html(result.doc);
						$row.find('.doc_num').html(result.num_doc);
						$row.find('.concepto').html(result.concepto);
						$row.find('.prog').html("falta");
						$row.find('.cuenta_num').html(result.cuenta_banco.cod);
						$row.find('.cheque').html(result.cheque);
						for(j=0;j<result.cuentas_haber.length;j++){
							$row.find('#haber_'+result.cuentas_haber[j].cuenta._id.$id).html(K.round(result.cuentas_haber[j].monto,2));
						}
						$row.find('.debe_total').html(K.round(result.debe,2));
						$row.find('.haber_total').html(K.round(result.haber,2));
						$row.wrapInner('<a class="item">');
						$mainPanel.find('.gridBody').append($row.children());
						debe_total += result.debe;
						haber_total += result.haber;
					}
					/** /Body items */
					/** Sumas */
					var $row = $mainPanel.find('.gridReference').clone();
					$row.find('.fecha,.doc_tipo,.doc_num,.concepto,.prog,.cuenta_num').remove();;
					$row.find('.cheque').html("TOTAL").addClass('ui-state-default ui-button-text-only').css({'min-width':'840px','text-align':'center'});				
					for(i=0;i<data.cuentas_debe.length;i++){
						$row.find('#debe_'+data.cuentas_debe[i]._id.$id).html(K.round(data.cuentas_debe[i].total,2));
					}
					for(i=0;i<data.cuentas_haber.length;i++){
						$row.find('#haber_'+data.cuentas_haber[i]._id.$id).html(K.round(data.cuentas_haber[i].total,2));
					}
					$row.find('.debe_total').html(K.round(debe_total,2));
					$row.find('.haber_total').html(K.round(haber_total,2));
					$row.wrapInner('<a class="total item">');
					$mainPanel.find('.gridBody').append($row.children());
					/** /Sumas */
				}else{
					K.notification({
						title: 'Periodo sin movimientos',
						text: 'El periodo seleccionado no cuenta con movimientos para Caja - Bancos!',
						type: 'info'
					});
				}
			},'json');
		});
	}
};