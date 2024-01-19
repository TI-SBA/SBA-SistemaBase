tdNavg = function(){
	$.post('td/navg',function(data){
		if(data!=null){
			var $p = $('#pageWrapperLeft');
			$p.find('label:first').html('Tr&aacute;mite Documentario');
			$p.find('.gridBody').empty();
			for(var i=0; i<data.length; i++){
				var result = data[i];
				var $row = $p.find('.gridReference').clone();
				$row.find('li:eq(0)').html( '<span class="ui-icon '+result.icon+'"></span>' + result.descr );
				$row.wrapInner('<a name="'+result.name+'" class="item" href="javascript: void(0);" />');
				$p.find(".gridBody").append( $row.children() );
			}
			$p.find('[name=tdExp]').click(function(){
				require(['td/expdcopi','td/expdvenc','td/expdarch','td/expdpor','td/expdreci'],function(tdExpdCopi,tdExpdVenc,tdExpdArch,tdExpdPor,tdExpdReci){
					if($('#pageWrapper [child=exps]').length<=0){
						$.post('td/navg/exps',function(data){
							for(var i=0; i<data.length; i++){
								var result = data[i];
								var $row = $p.find('.gridReference').clone();
								$row.find('li:eq(0)').html( '<span class="ui-icon '+result.icon+'"></span>' + result.descr )
									.css({
										"padding-left": "10px",
										"min-width": "186px",
										"max-width": "186px"
									});
								$row.wrapInner('<a name="'+result.name+'" class="item" href="javascript: void(0);" child="exps" />');
								$p.find("[name=tdExp]").after( $row.children() );
							}
							$p.find('[name=tdExp]').data('exps',$('#pageWrapper [child=exps]:first').data('exps'));
							$p.find('[name=tdExpdReci]').click(function(){ tdExpdReci.init(); }).click();
							$p.find('[name=tdExpdVenc]').click(function(){ tdExpdVenc.init(); });
							$p.find('[name=tdExpdArch]').click(function(){ tdExpdArch.init(); });
							$p.find('[name=tdExpdPor]').click(function(){ tdExpdPor.init(); });
							$p.find('[name=tdExpdCopi]').click(function(){ tdExpdCopi.init(); });
						},'json');
					}else{
						if($('#pageWrapper [child=exps]').css('display')=='none'){
							$('#pageWrapper [child=exps]').show();
						}else{
							$('#pageWrapper [child=exps]').hide();
						}
					}
				});
			});
			$p.find('[name=tdHis]').click(function(){
				require(['td/expdhistall','td/expdhistrebi','td/expdhistenvi','td/expdhistvenc'],function(tdGest,tdGestInt,tdGestExt){
					if($('#pageWrapper [child=his]').length<=0){
						$.post('td/navg/his',function(data){
							for(var i=0; i<data.length; i++){
								var result = data[i];
								var $row = $p.find('.gridReference').clone();
								$row.find('li:eq(0)').html( '<span class="ui-icon '+result.icon+'"></span>' + result.descr )
									.css({
										"padding-left": "10px",
										"min-width": "186px",
										"max-width": "186px"
									});
								$row.wrapInner('<a name="'+result.name+'" class="item" href="javascript: void(0);" child="his" />');
								$row.find('a').data('his',result.name).click(function(){
									//Llama a una funcion para el panel derecho
								});
								$p.find("[name=tdHis]").after( $row.children() );
							}
							$p.find('[name=tdHis]').data('his',$('#pageWrapper [child=his]:first').data('his'));
							$p.find('[name=tdExpdHistAll]').click(function(){ tdExpdHistAll.init(); });
							$p.find('[name=tdExpdHistRebi]').click(function(){ tdExpdHistRebi.init(); }).click();
							$p.find('[name=tdExpdHistEnvi]').click(function(){ tdExpdHistEnvi.init(); });
							$p.find('[name=tdExpdHistVenc]').click(function(){ tdExpdHistVenc.init(); });
						},'json');
					}else{
						if($('#pageWrapper [child=his]').css('display')=='none'){
							$('#pageWrapper [child=his]').show();
						}else{
							$('#pageWrapper [child=his]').hide();
						}
					}
				});
			});
			//$p.find('[name=tdGest]').click(function(){ tdGest.init(); });
			$p.find('[name=tdGest]').click(function(){
				require(['td/gest','td/gestint','td/gestext'],function(tdGest,tdGestInt,tdGestExt){
					if($('#pageWrapper [child=gest]').length<=0){
						$.post('td/navg/gest',function(data){
							for(var i=0; i<data.length; i++){
								var result = data[i];
								var $row = $p.find('.gridReference').clone();
								$row.find('li:eq(0)').html( '<span class="ui-icon '+result.icon+'"></span>' + result.descr )
									.css({
										"padding-left": "10px",
										"min-width": "186px",
										"max-width": "186px"
									});
								$row.wrapInner('<a name="'+result.name+'" class="item" href="javascript: void(0);" child="gest" />');
								$p.find("[name=tdGest]").after( $row.children() );
							}
							$p.find('[name=tdGest]').data('gest',$('#pageWrapper [child=gest]:first').data('gest'));
							$p.find('[name=tdGestInt]').click(function(){ tdGestInt.init(); }).click();
							$p.find('[name=tdGestExt]').click(function(){ tdGestExt.init(); });
						},'json');
					}else{
						if($('#pageWrapper [child=gest]').css('display')=='none'){
							$('#pageWrapper [child=gest]').show();
						}else{
							$('#pageWrapper [child=gest]').hide();
						}
					}
				});
			});
			$p.find('[name=tdTdocs]').click(function(){
				$.cookie('action','tdTdocs');
				window.location.replace('?new=1');
			});
			$p.find('[name=tdOrga]').click(function(){
				$.cookie('action','tdOrga');
				window.location.replace('?new=1');
			});
			$p.find('[name=tdComi]').click(function(){
				$.cookie('action','tdComi');
				window.location.replace('?new=1');
			});
			$p.find('[name=tdTupa]').click(function(){
				$.cookie('action','tdTupa');
				window.location.replace('?new=1');
			});
			$p.find('[name=tdRepo]').click(function(){ tdRepo.init(); });
			$p.resize();
			switch ($.cookie('action')) {
				case 'tdTupa':
					$p.find('[name=tdTupa]').click(); break;
		    	case 'tdExpdReci':
					require(['td/expdcopi','td/expdvenc','td/expdarch','td/expdpor','td/expdreci'],function(tdExpdCopi,tdExpdVenc,tdExpdArch,tdExpdPor,tdExpdReci){
						tdExpdReci.init();
					});
					break;
		    	case 'tdExpdPor':
					require(['td/expdcopi','td/expdvenc','td/expdarch','td/expdpor','td/expdreci'],function(tdExpdCopi,tdExpdVenc,tdExpdArch,tdExpdPor,tdExpdReci){
						tdExpdPor.init();
					});
					break;
		    	case 'tdExpdArch':
					require(['td/expdcopi','td/expdvenc','td/expdarch','td/expdpor','td/expdreci'],function(tdExpdCopi,tdExpdVenc,tdExpdArch,tdExpdPor,tdExpdReci){
						tdExpdArch.init();
					});
					break;
		    	case 'tdExpdVenc':
					require(['td/expdcopi','td/expdvenc','td/expdarch','td/expdpor','td/expdreci'],function(tdExpdCopi,tdExpdVenc,tdExpdArch,tdExpdPor,tdExpdReci){
						tdExpdVenc.init();
					});
					break;
		    	case 'tdExpdCopi':
					require(['td/expdcopi','td/expdvenc','td/expdarch','td/expdpor','td/expdreci'],function(tdExpdCopi,tdExpdVenc,tdExpdArch,tdExpdPor,tdExpdReci){
						tdExpdCopi.init();
					});
					break;
		    	case 'tdExpdHistAll':
					require(['td/expdhistall','td/expdhistrebi','td/expdhistenvi','td/expdhistvenc'],function(tdGest,tdGestInt,tdGestExt){
						tdExpdHistAll.init();
					});
					break;
		    	case 'tdExpdHistEnvi':
					require(['td/expdhistall','td/expdhistrebi','td/expdhistenvi','td/expdhistvenc'],function(tdGest,tdGestInt,tdGestExt){
						tdExpdHistEnvi.init();
					});
					break;
		    	case 'tdExpdHistRebi':
					require(['td/expdhistall','td/expdhistrebi','td/expdhistenvi','td/expdhistvenc'],function(tdGest,tdGestInt,tdGestExt){
						tdExpdHistRebi.init();
					});
					break;
		    	case 'tdExpdHistVenc':
					require(['td/expdhistall','td/expdhistrebi','td/expdhistenvi','td/expdhistvenc'],function(tdGest,tdGestInt,tdGestExt){
						tdExpdHistVenc.init();
					});
					break;
		    	case 'tdGestInt':
					require(['td/gestint','td/gestext'],function(tdGestInt,tdGestExt){
						tdGestInt.init();
					});
					break;
		    	case 'tdGestExt':
		    		require(['td/gestint','td/gestext'],function(tdGestInt,tdGestExt){
						tdGestExt.init();
					});
					break;
		    	case 'tdTdocs':
					$p.find('[name=tdTdocs]').click(); break;
		    	case 'tdOrga':
					$p.find('[name=tdOrga]').click(); break;
		    	case 'tdComi':
					$p.find('[name=tdComi]').click(); break;
		    	case 'tdRepo':
		    		tdRepo.init(); break;
		    	default: $p.find('[name=tdExp]').click();
		    }
		}else{
			K.notification({text: 'Usted no tiene permisos para este m&oacute;dulo!',type: 'error'});
		}
	},'json');
};