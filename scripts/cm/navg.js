cmNavg = function(){
	$.post('cm/navg',function(data){
		if(data!=null){
			var $p = $('#pageWrapperLeft');
			$p.find('label:first').html('Cementerio');
			$p.find('.gridBody').empty();
			for(var i=0; i<data.length; i++){
				var result = data[i];
				var $row = $p.find('.gridReference').clone();
				$row.find('li:eq(0)').html( '<span class="ui-icon '+result.icon+'"></span>' + result.descr );
				$row.wrapInner('<a name="'+result.name+'" class="item" href="javascript: void(0);" />');
				$p.find(".gridBody").append( $row.children() );
			}
			$p.find('[name=cmRegi]').click(function(){
				require(['cm/regi'],function(cmRegi){
					cmRegi.init();
				});
			});
			$p.find('[name=cmPabe]').click(function(){
				require(['cm/pabe'],function(cmPabe){
					cmPabe.init();
				});
			});
			$p.find('[name=cmEspa]').click(function(){
				require(['cm/espa'],function(cmEspa){
					cmEspa.init();
				});
			});
			$p.find('[name=cmMapa]').click(function(){
				require(['cm/mapa'],function(cmMapa){
					cmMapa.init();
				});
			});
			$p.find('[name=cmAcce]').click(function(){
				require(['cm/acce'],function(cmAcce){
					cmAcce.init();
				});
			});
			$p.find('[name=cmCuenPro]').click(function(){
				require(['cm/prop','cm/ocup'],function(cmProp,cmOcup){
					cmProp.init();
				});
			});
			$p.find('[name=cmCuenOcu]').click(function(){
				require(['cm/prop','cm/ocup'],function(cmProp,cmOcup){
					cmOcup.init();
				});
			});
			$p.find('[name=cmOperAll]').click(function(){
				require(['cm/oper','cm/operpro','cm/operall'],function(cmOper,cmOperPro,cmOperAll){
					cmOperAll.init();
				});
			});
			$p.find('[name=cmRehi]').click(function(){
				$.cookie('action','cmRehi');
				window.location.replace('?new=1');
			});
			$p.find('[name=cmTerr]').click(function(){
				$.cookie('action','cmTerr');
				window.location.replace('?new=1');
			});
			$p.find('[name=cmConc]').click(function(){
				require(['cm/oper','cm/concrec','cm/concall','cm/concpor','cm/concven'],function(cmOper,cmConcRec,cmConcAll,cmConcPor,cmConcVen){
					$('#pageWrapperLeft .ui-state-highlight').removeClass('ui-state-highlight');
					if($('#pageWrapper [child=conc]').length<=0){
						$.post('cm/navg/conc',function(data){
							for(var i=0; i<data.length; i++){
								var result = data[i];
								var $row = $p.find('.gridReference').clone();
								$row.find('li:eq(0)').html( '<span class="ui-icon '+result.icon+'"></span>' + result.descr )
									.css({
										"padding-left": "10px",
										"min-width": "186px",
										"max-width": "186px"
									});
								$row.wrapInner('<a name="'+result.name+'" class="item" href="javascript: void(0);" child="conc" />');
								$p.find("[name=cmConc]").after( $row.children() );
							}
							$p.find('[name=cmConc]').data('conc',$('#pageWrapper [child=conc]:first').data('conc'));
							$p.find('[name=cmConcAll]').click(function(){ cmConcAll.init(); });
							$p.find('[name=cmConcVen]').click(function(){ cmConcVen.init(); });
							$p.find('[name=cmConcPor]').click(function(){ cmConcPor.init(); });
							$p.find('[name=cmConcRec]').click(function(){ cmConcRec.init(); }).click();
						},'json');
					}else{
						if($('#pageWrapper [child=conc]').css('display')=='none'){
							$('#pageWrapper [child=conc]').show();
						}else{
							$('#pageWrapper [child=conc]').hide();
						}
					}
				});
			});
			$p.find('[name=cjCompRec]').click(function(){ cjCompRec.init(); });
			$p.find('[name=cmCuen]').click(function(){
				require(['cm/cuen'],function(cmCuen){
					cmCuen.init();
				});
			});
			$p.find('[name=cjRede]').click(function(){
				require(['cj/rede'],function(cjRede){
					cjRede.init();
				});
			});
			$p.find('[name=cjRepo]').click(function(){ cjRepo.init(); });
			$p.find('[name=cmRepo]').click(function(){ cmRepo.init(); });
			$p.find('[name=cjCompPen]').click(function(){ cjCompPen.init(); });
			$p.resize();
			switch ($.cookie('action')) {
		    	case 'cmRegi':
					$p.find('[name=cmRegi]').click(); break;
		    	case 'cmPabe':
					$p.find('[name=cmPabe]').click(); break;
		    	case 'cmEspa':
					$p.find('[name=cmEspa]').click(); break;
		    	case 'cmMapa':
					$p.find('[name=cmMapa]').click(); break;
		    	case 'cmCuenProp':
					$p.find('[name=cmCuenProp]').click(); break;
		    	case 'cmAcce':
					$p.find('[name=cmAcce]').click(); break;
		    	case 'cmCuenOcup':
					$p.find('[name=cmCuenOcup]').click(); break;
				case 'cmFune': cmFune.init(); break;
				case 'cmMuni': cmMuni.init(); break;
				/*case 'cmCompPen': cmCompPen.init(); break;
				case 'cmCompAll': cmCompAll.init(); break;*/
				case 'cmConcRec':
					require(['cm/oper','cm/concrec','cm/concall','cm/concpor','cm/concven'],function(cmOper,cmConcRec,cmConcAll,cmConcPor,cmConcVen){
						cmConcRec.init();
					});
					break;
				case 'cmConcPor':
					require(['cm/oper','cm/concrec','cm/concall','cm/concpor','cm/concven'],function(cmOper,cmConcRec,cmConcAll,cmConcPor,cmConcVen){
						cmConcPor.init();
					});
					break;
				case 'cmConcVen':
					require(['cm/oper','cm/concrec','cm/concall','cm/concpor','cm/concven'],function(cmOper,cmConcRec,cmConcAll,cmConcPor,cmConcVen){
						cmConcVen.init();
					});
					break;
				case 'cmConcAll':
					require(['cm/oper','cm/concrec','cm/concall','cm/concpor','cm/concven'],function(cmOper,cmConcRec,cmConcAll,cmConcPor,cmConcVen){
						cmConcAll.init();
					});
					break;
				case 'cmOperPro':
					require(['cm/oper','cm/operpro','cm/operall'],function(cmOper,cmOperPro,cmOperAll){
						cmOperPro.init();
					});
					break;
				case 'cmOperAll':
					require(['cm/oper','cm/operpro','cm/operall'],function(cmOper,cmOperPro,cmOperAll){
						cmOperAll.init();
					});
					break;
		    	case 'cjCompRec': cjCompRec.init(); break;
		    	case 'cjCompPen': cjCompPen.init(); break;
		    	case 'cmCuen':
		    		require(['cm/cuen'],function(cmCuen){
						cmCuen.init();
					});
					break;
		    	case 'cjRede':
		    		require(['cj/rede'],function(cjRede){
						cjRede.init();
					});
					break;
		    	case 'cjRepo': cjRepo.init(); break;
				case 'cmRepo': cmRepo.init(); break;
		    	default: $p.find('[name=cmOperAll]').click();
		    }
		}else{
			K.notification({text: 'Usted no tiene permisos para este m&oacute;dulo!',type: 'error', layout: 'topLeft'});
		}
	},'json');
};