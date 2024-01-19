/*******************************************************************************
auxiliares pasivo */
ctAuxsIngr = {
	init: function(){
		if($('#pageWrapper [child=auxs]').length<=0){
			$.post('ct/navg/auxs',function(data){
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
					$row.wrapInner('<a name="'+result.name+'" class="item" href="javascript: void(0);" child="auxs" />');
					$p.find("[name=ctAuxs]").after( $row.children() );
				}
				$p.find('[name=ctAuxs]').data('auxs',$('#pageWrapper [child=auxs]:first').data('auxs'));
				$p.find('[name=ctAuxsPasi]').click(function(){ ctAuxsPasi.init(); });
				$p.find('[name=ctAuxsActi]').click(function(){ ctAuxsActi.init(); });
				$p.find('[name=ctAuxsPatr]').click(function(){ ctAuxsPatr.init(); });
				$p.find('[name=ctAuxsIngr]').click(function(){ ctAuxsIngr.init(); }).addClass('ui-state-highlight');
				$p.find('[name=ctAuxsGast]').click(function(){ ctAuxsGast.init(); });
				$p.find('[name=ctAuxsResu]').click(function(){ ctAuxsResu.init(); });
				$p.find('[name=ctAuxsPres]').click(function(){ ctAuxsPres.init(); });
				$p.find('[name=ctAuxsDeor]').click(function(){ ctAuxsDeor.init(); });
			},'json');
		}
		K.initMode({
			mode: 'ct',
			action: 'ctAuxsIngr',
			titleBar: {
				title: 'Auxiliares Standard de las Cuentas de Ingreso'
			}
		});
		
		new K.Panel({
			id: 'mainPanel',
			contentURL: 'ct/auxs',
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
				$mainPanel.find('[name=btnAgregar]').click(function(){
					ctAuxs.windowNew({tipo: 'I'});
				}).button({icons: {primary: 'ui-icon-plusthick'}});
				$mainPanel.find('[name=btnCerrar]').click(function(){
					ctAuxs.closePeriodo({tipo: 'I'});
				}).button({icons: {primary: 'ui-icon-gear'}});
				$mainPanel.find('[name=btnGenerar]').click(function(){
					ctAuxs.windowGenerar({tipo: 'I'});
				}).button({icons: {primary: 'ui-icon-gear'}}).hide();
				$mainPanel.find('[name=periodo]').datepicker( {
					maxDate: '+1d',
			        dateFormat: 'MM yy',
			        onClose: function(dateText, inst) { 
			            var month = $("#ui-datepicker-div .ui-datepicker-month :selected").val();
			            var year = $("#ui-datepicker-div .ui-datepicker-year :selected").val();
			            $(this).data('mes',month).data('ano',year);
			            $(this).val($.datepicker.formatDate('MM yy', new Date(year, month, 1)));
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
			    	ctAuxs.loadData({url: 'ct/auxs/lista'});
			    });
				$mainPanel.find('[name=btnOrga]').click(function(){
					ciSearch.windowSearchOrga({callback: function(data){
						$mainPanel.find('[name=orga]').html(data.nomb).data('data',data);
						$mainPanel.find('[name=btnOrga]').button('option','text',false);
						$mainPanel.find('[name=periodo]').change();
					}});
				}).button({icons: {primary: 'ui-icon-search'}});
				$mainPanel.find('[name=btnCtaMayor]').click(function(){
					ctPcon.windowSelect({tipo: 'I',digit: 4,callback: function(data){
						$mainPanel.find('[name=cuenta_mayor]').html(data.cod).data('data',data);
						$mainPanel.find('[name=descr_may]').html(data.descr);
						$mainPanel.find('[name=btnCtaMayor]').button('option','text',false);
						$mainPanel.find('[name=periodo]').change();
					}});
				}).button({icons: {primary: 'ui-icon-search'}});
				$mainPanel.find('[name=btnSubCta]').click(function(){
					ctPcon.windowSelect({tipo: 'I',last: true,callback: function(data){
						$mainPanel.find('[name=subcuenta]').html(data.cod).data('data',data);
						$mainPanel.find('[name=descr_sub]').html(data.descr);
						$mainPanel.find('[name=btnSubCta]').button('option','text',false);
						$mainPanel.find('[name=periodo]').change();
					}});
				}).button({icons: {primary: 'ui-icon-search'}});
				$mainPanel.find('[name=btnDesOrg]').click(function(){
					$mainPanel.find('[name=orga]').html('').removeData('data');
					$mainPanel.find('[name=btnOrga]').button('option','text',true);
					$mainPanel.find('[name=periodo]').change();
				}).button({icons: {primary: 'ui-icon-closethick'},text: false});
				$mainPanel.find('[name=btnInmueble]').click(function(){
					inLoca.selectEsp({callback: function(data){
						$mainPanel.find('[name=inmueble]').html(data.descr).data('data',data);
						$mainPanel.find('[name=arrendatario]').html(ciHelper.enti.formatName(data.arrendatario));
						$mainPanel.find('[name=btnInmueble]').button('option','text',false);
						$mainPanel.find('[name=periodo]').change();
					},ocupado: true});
				}).button({icons: {primary: 'ui-icon-search'}});
				$mainPanel.find('[name=btnDesInm]').click(function(){
					$mainPanel.find('[name=inmueble]').html('').removeData('data');
					$mainPanel.find('[name=arrendatario]').html('');
					$mainPanel.find('[name=btnInmueble]').button('option','text',true);
					$mainPanel.find('[name=periodo]').change();
				}).button({icons: {primary: 'ui-icon-closethick'},text: false});
				$mainPanel.find('[name=periodo]').change();
				K.unblock({$element: $('#pageWrapperMain')});
			}
		});
		$('#pageWrapperMain').layout();
	}
};