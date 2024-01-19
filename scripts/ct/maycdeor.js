/*******************************************************************************
mayorizacion de cuentas orden */
ctMaycDeor = {
	init: function(){
		if($('#pageWrapper [child=mayc]').length<=0){
			$.post('ct/navg/mayc',function(data){
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
					$row.wrapInner('<a name="'+result.name+'" class="item" href="javascript: void(0);" child="mayc" />');
					$p.find("[name=ctMayc]").after( $row.children() );
				}
				$p.find('[name=ctMayc]').data('mayc',$('#pageWrapper [child=mayc]:first').data('mayc'));
				$p.find('[name=ctMaycPasi]').click(function(){ ctMaycPasi.init(); });
				$p.find('[name=ctMaycDeor]').click(function(){ ctMaycDeor.init(); }).addClass('ui-state-highlight');
				$p.find('[name=ctMaycResu]').click(function(){ ctMaycResu.init(); });
				$p.find('[name=ctMaycActi]').click(function(){ ctMaycActi.init(); });
			},'json');
		}
		K.initMode({
			mode: 'ct',
			action: 'ctMaycDeor',
			titleBar: {
				title: 'Mayorizaci&oacute;n de las Cuentas de Orden'
			}
		});
		
		new K.Panel({
			id: 'mainPanel',
			contentURL: 'ct/mayc',
			onContentLoaded: function(){
				$('#No-Results').hide();
				$mainPanel = $('#mainPanel');
				$mainPanel.find('[name=obj]').html( 'registro(s)' );
				$mainPanel.find("[name=moreresults]").css('float','right').button({icons: {primary: 'ui-icon-triangle-1-s'}});
				$mainPanel.resize(function(){
					$mainPanel.find('.grid:eq(1)').height(($mainPanel.height()-$mainPanel.find('.grid:eq(0)').outerHeight()-$mainPanel.find('div:first').outerHeight()-$('.div-bottom').outerHeight())+'px');
				}).resize();
				$mainPanel.find('.grid:eq(0)').css('overflow','hidden');
				$mainPanel.find('.grid:eq(1)').scroll(function(){
					$mainPanel.find('.grid:eq(0)').scrollLeft($(this).scrollLeft());
				});
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
			    	var flag = $mainPanel.find('[name=periodo]').data('flag');
				    if(flag!=null){
				    	if(flag.ano!=$mainPanel.find('[name=periodo]').data('ano')||flag.mes!=(+$mainPanel.find('[name=periodo]').data('mes')+1)){
							ctMayc.loadSald($mainPanel.find('[name=cuenta]').data('data')._id.$id);
				    	}else
				    		ctMayc.loadData({url: 'ct/mayc/auxs'});
				    }else
				    	ctMayc.loadData({url: 'ct/mayc/auxs'});
			    });
				$mainPanel.find('[name=btnCta]').click(function(){
					ctPcon.windowSelect({tipo: 'O',digit: 4,callback: function(data){
						$mainPanel.find('[name=cuenta]').html(data.cod).data('data',data);
						$mainPanel.find('[name=descr]').html(data.descr);
						$mainPanel.find('[name=btnCta]').button('option','text',false);
						ctMayc.loadSald(data._id.$id);
					}});
				}).button({icons: {primary: 'ui-icon-search'}});
				$mainPanel.find('[name=cuenta_sub]').change(function(){
					$mainPanel.find('[name=descr_sub]').html($(this).find('option:selected').attr('descr'));
					$mainPanel.find('[name=periodo]').change();
				});
				$mainPanel.find('[name=periodo]').change();
				K.unblock({$element: $('#pageWrapperMain')});
			}
		});
		$('#pageWrapperMain').layout();
	}
};