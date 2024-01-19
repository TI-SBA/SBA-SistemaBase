/*******************************************************************************
auxiliares pasivo */
ctMocuResu = {
	init: function(){
		if($('#pageWrapper [child=mocu]').length<=0){
			$.post('ct/navg/mocu',function(data){
				var $p = $('#pageWrapperLeft');
				for(var i=0; i<data.length; i++){
					var result = data[i];
					var $row = $p.find('.gridReference').clone();
					$row.find('li:eq(0)').html( '<span class="ui-icon '+result.icon+'"></span>' + result.descr )
						.css({
							"padding-left": "10px",
							"min-width": "186px",
							"max-width": "186px"
						});
					$row.wrapInner('<a name="'+result.name+'" class="item" href="javascript: void(0);" child="mocu" />');
					$p.find("[name=ctMocu]").after( $row.children() );
				}
				$p.find('[name=ctMocu]').data('mocu',$('#pageWrapper [child=mocu]:first').data('mocu'));
				$p.find('[name=ctMocuPasi]').click(function(){ ctMocuPasi.init(); });
				$p.find('[name=ctMocuResu]').click(function(){ ctMocuResu.init(); }).addClass('ui-state-highlight');
				$p.find('[name=ctMocuPasi]').click(function(){ ctMocuPasi.init(); });
				$p.find('[name=ctMocuDeor]').click(function(){ ctMocuDeor.init(); });
				$p.find('[name=ctMocuTodo]').click(function(){ ctMocuTodo.init(); });
			},'json');
		}
		K.initMode({
			mode: 'ct',
			action: 'ctMocuResu',
			titleBar: {
				title: 'Movimientos de las Cuentas de Resumen'
			}
		});
		
		new K.Panel({
			id: 'mainPanel',
			contentURL: 'ct/mocu',
			onContentLoaded: function(){
				$('#No-Results').hide();
				$mainPanel = $('#mainPanel');
				$mainPanel.find('[name=obj]').html( 'registro(s)' );
				$mainPanel.find("[name=moreresults]").css('float','right').button({icons: {primary: 'ui-icon-triangle-1-s'}});
				$mainPanel.resize(function(){
					$mainPanel.find('.grid:eq(1)').height(($mainPanel.height()-$mainPanel.find('.grid:eq(0)').outerHeight()-$mainPanel.find('div:first').outerHeight())+'px');
				}).resize();
				$mainPanel.find('.grid:eq(0)').css('overflow','hidden');
				$mainPanel.find('.grid:eq(1)').scroll(function(){
					$mainPanel.find('.grid:eq(0)').scrollLeft($(this).scrollLeft());
				});
				$mainPanel.find('[name=btnGen]').click(function(){
					ctMocuResu.windowGen();
				}).button({icons: {primary: 'ui-icon-gear'}});
				$mainPanel.find('[name=periodo]').datepicker( {
					maxDate: '+1d',
			        dateFormat: 'MM yy',
			        onClose: function(dateText, inst) { 
			            var month = $("#ui-datepicker-div .ui-datepicker-month :selected").val();
			            var year = $("#ui-datepicker-div .ui-datepicker-year :selected").val();
			            $(this).data('mes',month).data('ano',year);
			            $(this).val($.datepicker.formatDate('MM yy', new Date(year, month, 1)));
			            $(this).change();
			        },
			        onChangeMonthYear: function(year,month,inst){
			            $(this).data('mes',month-1).data('ano',year);
			            $(this).val($.datepicker.formatDate('MM yy', new Date(year, month-1, 1)));
			            $(this).change();
			        }
			    }).focus(function(){
			    	$('.ui-datepicker-calendar').css('display','none');
			    }).val(ciHelper.meses[ciHelper.date.getMonth()-1]+' '+ciHelper.date.getYear())
			    .data('mes',+ciHelper.date.getMonth()-1)
			    .data('ano',ciHelper.date.getYear()).change(function(){
			    	$mainPanel.find('.gridBody').empty();
			    	ctMocuResu.loadData({url: 'ct/mocu/lista'});
			    }).change();
				K.unblock({$element: $('#pageWrapperMain')});
			}
		});
		$('#pageWrapperMain').layout();
	},
	loadData: function(params){
		$.extend(params,{
			tipo: 'R',
			mes: +$mainPanel.find('[name=periodo]').data('mes')+1,
			ano: $mainPanel.find('[name=periodo]').data('ano')
		});
	    $.post(params.url, params, function(data){
			if ( data.sald!=null ) {
				for (var i=0; i < data.sald.length; i++) {
					var result = data.sald[i];
					if(result.estado=='C'){
						if($mainPanel.find('[name='+result.cuenta_mayor._id.$id+']').length<1){
							var $row = $('.gridReference','#mainPanel').clone();
							$li = $('li',$row);
							$li.eq(0).html( result.cuenta_mayor.cod );
							$li.eq(1).html( result.cuenta_mayor.descr );
							$li.eq(2).html( ciHelper.formatMon(result.debe_inicial) );
							$li.eq(3).html( ciHelper.formatMon(result.haber_inicial) );
							$li.eq(4).html( ciHelper.formatMon(result.debe_final-result.debe_inicial) );
							$li.eq(5).html( ciHelper.formatMon(result.haber_final-result.haber_inicial) );
							$li.eq(6).html( ciHelper.formatMon(result.debe_final) );
							$li.eq(7).html( ciHelper.formatMon(result.haber_final) );
							if(result.debe_final>result.haber_final) $li.eq(8).html( ciHelper.formatMon(result.debe_final) );
							else $li.eq(8).html( ciHelper.formatMon(0) );
							if(result.haber_final>result.debe_final) $li.eq(9).html( ciHelper.formatMon(result.haber_final) );
							else $li.eq(9).html( ciHelper.formatMon(0) );
							$row.wrapInner('<a class="item" name="'+result.cuenta_mayor._id.$id+'" href="javascript: void(0);" />');
							$row.find('a').data('id',result._id.$id).data('data',{
								debe_inicial: result.debe_inicial,
								debe_final: result.debe_final,
								haber_inicial: result.haber_inicial,
								haber_final: result.haber_final
							});
							$("#mainPanel .gridBody").append( $row.children() );
							ciHelper.gridButtons($("#mainPanel .gridBody"));
						}else{
							var $row = $mainPanel.find('[name='+result.cuenta_mayor._id.$id+']'),
							$li = $('li',$row),
							old = $row.data('data');
							$li.eq(2).html( ciHelper.formatMon(result.debe_inicial+old.debe_inicial) );
							$li.eq(3).html( ciHelper.formatMon(result.haber_inicial+old.haber_inicial) );
							$li.eq(4).html( ciHelper.formatMon((result.debe_final+old.debe_final)-(result.debe_inicial+old.debe_inicial)) );
							$li.eq(5).html( ciHelper.formatMon((result.haber_final+old.haber_final)-(result.haber_inicial+old.haber_inicial)) );
							$li.eq(6).html( ciHelper.formatMon(result.debe_final+old.debe_final) );
							$li.eq(7).html( ciHelper.formatMon(result.haber_final+old.haber_final) );
							if((result.debe_final+old.debe_final)>(result.haber_final+old.haber_final)) $li.eq(8).html( ciHelper.formatMon(result.debe_final+old.debe_final) );
							else $li.eq(8).html( ciHelper.formatMon(0) );
							if((result.haber_final+old.haber_final)>(result.debe_final+old.debe_final)) $li.eq(9).html( ciHelper.formatMon(result.haber_final+old.haber_final) );
							else $li.eq(9).html( ciHelper.formatMon(0) );
							$row.find('a').data('id',result._id.$id).data('data',{
								debe_inicial: result.debe_inicial+old.debe_inicial,
								debe_final: result.debe_final+old.debe_final,
								haber_inicial: result.haber_inicial+old.haber_inicial,
								haber_final: result.haber_final+old.haber_final
							});
						}
						var $row = $('.gridReference','#mainPanel').clone(),
						$li = $('li',$row);
						$li.eq(0).html( result.sub_cuenta.cod );
						$li.eq(1).html( result.sub_cuenta.descr );
						$li.eq(2).html( ciHelper.formatMon(result.debe_inicial) );
						$li.eq(3).html( ciHelper.formatMon(result.haber_inicial) );
						$li.eq(4).html( ciHelper.formatMon(result.debe_final-result.debe_inicial) );
						$li.eq(5).html( ciHelper.formatMon(result.haber_final-result.haber_inicial) );
						$li.eq(6).html( ciHelper.formatMon(result.debe_final) );
						$li.eq(7).html( ciHelper.formatMon(result.haber_final) );
						if(result.debe_final>result.haber_final) $li.eq(8).html( ciHelper.formatMon(result.debe_final) );
						else $li.eq(8).html( ciHelper.formatMon(0) );
						if(result.haber_final>result.debe_final) $li.eq(9).html( ciHelper.formatMon(result.haber_final) );
						else $li.eq(9).html( ciHelper.formatMon(0) );
						$row.wrapInner('<a class="item" name="'+result.sub_cuenta._id.$id+'" href="javascript: void(0);" />');
						$("#mainPanel .gridBody [name="+result.cuenta_mayor._id.$id+"]").after( $row.children() );
						ciHelper.gridButtons($("#mainPanel .gridBody"));
					}
		        }
	      }
	      $('#mainPanel').resize();
	      K.unblock({$element: $('#pageWrapperMain')});
	    }, 'json');
	}
};