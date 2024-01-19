cjNavg = function(){
	$.post('cj/navg',function(data){
		if(data!=null){
			var $p = $('#pageWrapperLeft');
			$p.find('label:first').html('Caja');
			$p.find('.gridBody').empty();
			for(var i=0; i<data.length; i++){
				var result = data[i];
				var $row = $p.find('.gridReference').clone();
				$row.find('li:eq(0)').html( '<span class="ui-icon '+result.icon+'"></span>' + result.descr );
				$row.wrapInner('<a name="'+result.name+'" class="item" href="javascript: void(0);" />');
				$p.find(".gridBody").append( $row.children() );
			}
			$p.find('[name=cjCaja]').click(function(){ cjCaja.init(); });
			//$p.find('[name=cjTalo]').click(function(){ cjTalo.init(); });
			$p.find('[name=cjTalo]').click(function(){
				$.cookie('action','cjTalo');
				window.location.replace('?new=1');
			});
			$p.find('[name=cjConc]').click(function(){ cjConc.init(); });
			$p.find('[name=cjCuen]').click(function(){
				$('#pageWrapperLeft .ui-state-highlight').removeClass('ui-state-highlight');
				if($('#pageWrapper [child=cuen]').length<=0){
					$.post('cj/navg/cuen',function(data){
						for(var i=0; i<data.length; i++){
							var result = data[i];
							var $row = $p.find('.gridReference').clone();
							$row.find('li:eq(0)').html( '<span class="ui-icon '+result.icon+'"></span>' + result.descr )
								.css({
									"padding-left": "10px",
									"min-width": "186px",
									"max-width": "186px"
								});
							$row.wrapInner('<a name="'+result.name+'" class="item" href="javascript: void(0);" child="cuen" />');
							$p.find("[name=cjCuen]").after( $row.children() );
						}
						$p.find('[name=cjCuen]').data('cuen',$('#pageWrapper [child=cuen]:first').data('cuen'));
						$p.find('[name=cjCuenPor]').click(function(){ cjCuenPor.init(); }).addClass('ui-state-highlight').click();
						$p.find('[name=cjCuenTod]').click(function(){ cjCuenTod.init(); });
					},'json');
				}else{
					if($('#pageWrapper [child=cuen]').css('display')=='none'){
						$('#pageWrapper [child=cuen]').show();
					}else{
						$('#pageWrapper [child=cuen]').hide();
					}
				}
			});
			$p.find('[name=cjEnti]').click(function(){
				$('#pageWrapperLeft .ui-state-highlight').removeClass('ui-state-highlight');
				if($('#pageWrapper [child=enti]').length<=0){
					$.post('cj/navg/enti',function(data){
						for(var i=0; i<data.length; i++){
							var result = data[i];
							var $row = $p.find('.gridReference').clone();
							$row.find('li:eq(0)').html( '<span class="ui-icon '+result.icon+'"></span>' + result.descr )
								.css({
									"padding-left": "10px",
									"min-width": "186px",
									"max-width": "186px"
								});
							$row.wrapInner('<a name="'+result.name+'" class="item" href="javascript: void(0);" child="enti" />');
							$p.find("[name=cjEnti]").after( $row.children() );
						}
						$p.find('[name=cjEnti]').data('enti',$('#pageWrapper [child=enti]:first').data('enti'));
						$p.find('[name=cjEntiClie]').click(function(){ cjEntiClie.init(); }).addClass('ui-state-highlight').click();
						$p.find('[name=cjEntiCaje]').click(function(){ cjEntiCaje.init(); })
					},'json');
				}else{
					if($('#pageWrapper [child=enti]').css('display')=='none'){
						$('#pageWrapper [child=enti]').show();
					}else{
						$('#pageWrapper [child=enti]').hide();
					}
				}
			});
			$p.find('[name=cjComp]').click(function(){
				$('#pageWrapperLeft .ui-state-highlight').removeClass('ui-state-highlight');
				if($('#pageWrapper [child=comp]').length<=0){
					$.post('cj/navg/comp',function(data){
						for(var i=0; i<data.length; i++){
							var result = data[i];
							var $row = $p.find('.gridReference').clone();
							$row.find('li:eq(0)').html( '<span class="ui-icon '+result.icon+'"></span>' + result.descr )
								.css({
									"padding-left": "10px",
									"min-width": "186px",
									"max-width": "186px"
								});
							$row.wrapInner('<a name="'+result.name+'" class="item" href="javascript: void(0);" child="comp" />');
							$p.find("[name=cjComp]").after( $row.children() );
						}
						$p.find('[name=cjComp]').data('comp',$('#pageWrapper [child=comp]:first').data('comp'));
						$p.find('[name=cjCompFac]').click(function(){ cjCompFac.init(); }).addClass('ui-state-highlight').click();
						$p.find('[name=cjCompBol]').click(function(){ cjCompBol.init(); });
						$p.find('[name=cjCompRec]').click(function(){ cjCompRec.init(); });
						$p.find('[name=cjCompPen]').click(function(){ cjCompPen.init(); });
					},'json');
				}else{
					if($('#pageWrapper [child=comp]').css('display')=='none'){
						$('#pageWrapper [child=comp]').show();
					}else{
						$('#pageWrapper [child=comp]').hide();
					}
				}
			});
			$p.find('[name=cjCompFac]').click(function(){ cjCompFac.init(); });
			$p.find('[name=cjCompBol]').click(function(){ cjCompBol.init(); });
			$p.find('[name=cjCompRec]').click(function(){ cjCompRec.init(); });
			$p.find('[name=cjCompPen]').click(function(){ cjCompPen.init(); });
			$p.find('[name=cjInmu]').click(function(){
				require(['cj/inmu'],function(cjInmu){
					cjInmu.init();
				});
			});
			$p.find('[name=cjEcom]').click(function(){
				$.cookie('action','cjEcom');
				window.location.replace('?new=1');
			});
			$p.find('[name=cjCeme]').click(function(){
				require(['cj/ceme'],function(cjCeme){
					cjCeme.init();
				});
			});
			$p.find('[name=cjRede]').click(function(){
				require(['cj/rede'],function(cjRede){
					cjRede.init();
				});
			});
			$p.find('[name=cjRepo]').click(function(){ cjRepo.init(); });
			$p.resize();
			switch ($.cookie('action')) {
		    	case 'cjCaja': cjCaja.init(); break;
		    	//case 'cjTalo': cjTalo.init(); break;
		    	case 'cjTalo': $p.find('[name=cjTalo]').click(); break;
		    	case 'cjConc': cjConc.init(); break;
		    	case 'cjCuenPor': cjCuenPor.init(); break;
		    	case 'cjCuenTod': cjCuenTod.init(); break;
		    	case 'cjEntiClie': cjEntiClie.init(); break;
		    	case 'cjEntiCaje': cjEntiCaje.init(); break;
		    	case 'cjEcom': $p.find('[name=cjEcom]').click(); break;
		    	case 'cjCompFac': cjCompFac.init(); break;
		    	case 'cjCompBol': cjCompBol.init(); break;
		    	case 'cjCompRec': cjCompRec.init(); break;
		    	case 'cjCompPen': cjCompPen.init(); break;
		    	case 'cjInmu':
		    		require(['cj/inmu'],function(cjInmu){
						cjInmu.init();
					});
					break;
		    	case 'cjCeme':
		    		require(['cj/ceme'],function(cjCeme){
						cjCeme.init();
					});
					break;
		    	case 'cjRede':
		    		require(['cj/rede'],function(cjRede){
						cjRede.init();
					});
					break;
		    	case 'cjRepo': cjRepo.init(); break;
		    	default: cjCaja.init();
		    }
		}else{
			K.notification({text: 'Usted no tiene permisos para este m&oacute;dulo!',type: 'error'});
		}
	},'json');
};
cjcmNavg = function(){
	$.post('cj/navg/ceme',function(data){
		if(data!=null){
			var $p = $('#pageWrapperLeft');
			$p.find('label:first').html('Caja Cementerio');
			$p.find('.gridBody').empty();
			for(var i=0; i<data.length; i++){
				var result = data[i];
				var $row = $p.find('.gridReference').clone();
				$row.find('li:eq(0)').html( '<span class="ui-icon '+result.icon+'"></span>' + result.descr );
				$row.wrapInner('<a name="'+result.name+'" class="item" href="javascript: void(0);" />');
				$p.find(".gridBody").append( $row.children() );
			}
			$p.find('[name=cjCompRec]').click(function(){ cjCompRec.init(); });
			$p.find('[name=cjCeme]').click(function(){
				require(['cj/ceme'],function(cjCeme){
					cjCeme.init();
				});
			});
			$p.find('[name=cjRepo]').click(function(){ cjRepo.init(); });
			$p.resize();
			switch ($.cookie('action')) {
		    	case 'cjCompRec': cjCompRec.init(); break;
		    	case 'cjCeme':
		    		require(['cj/ceme'],function(cjCeme){
						cjCeme.init();
					});
					break;
		    	case 'cjRepo': cjRepo.init(); break;
		    	default:
		    		require(['cj/ceme'],function(cjCeme){
						cjCeme.init();
					});
					break;
		    }
		}else{
			K.notification({text: 'Usted no tiene permisos para este m&oacute;dulo!',type: 'error'});
		}
	},'json');
};