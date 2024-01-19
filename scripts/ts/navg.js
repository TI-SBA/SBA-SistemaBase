tsNavg = function(){
	$.post('ts/navg',function(data){
		if(data!=null){
			var $p = $('#pageWrapperLeft');
			$p.find('label:first').html('Tesoreria');
			$p.find('.gridBody').empty();
			for(var i=0; i<data.length; i++){
				var result = data[i];
				var $row = $p.find('.gridReference').clone();
				$row.find('li:eq(0)').html( '<span class="ui-icon '+result.icon+'"></span>' + result.descr );
				$row.wrapInner('<a name="'+result.name+'" class="item" href="javascript: void(0);" />');
				$p.find(".gridBody").append( $row.children() );
			}
			$p.find('[name=tsCtpp]').click(function(){ 
				$('#pageWrapperLeft .ui-state-highlight').removeClass('ui-state-highlight');
				if($('#pageWrapper [child=ctpp]').length<=0){
					$.post('ts/navg/ctpp',function(data){
						for(var i=0; i<data.length; i++){
							var result = data[i];
							var $row = $p.find('.gridReference').clone();
							$row.find('li:eq(0)').html( '<span class="ui-icon '+result.icon+'"></span>' + result.descr )
								.css({
									"padding-left": "10px",
									"min-width": "186px",
									"max-width": "186px"
								});
							$row.wrapInner('<a name="'+result.name+'" class="item" href="javascript: void(0);" child="ctpp" />');
							$p.find("[name=tsCtpp]").after( $row.children() );
						}
						$p.find('[name=tsCtpp]').data('ctpp',$('#pageWrapper [child=ctpp]:first').data('ctpp'));
						$p.find('[name=tsCtppPen]').click(function(){ tsCtppPen.init(); }).click().addClass('ui-state-highlight');
						$p.find('[name=tsCtppAll]').click(function(){ tsCtppAll.init(); });
					},'json');
				}else{
					if($('#pageWrapper [child=ctpp]').css('display')=='none'){
						$('#pageWrapper [child=ctpp]').show();
					}else{
						$('#pageWrapper [child=ctpp]').hide();
					}
				}
			});
			/* ANTIGUA INTERFAZ
			$p.find('[name=tsComp]').click(function(){ 
				$('#pageWrapperLeft .ui-state-highlight').removeClass('ui-state-highlight');
				if($('#pageWrapper [child=comp]').length<=0){
					$.post('ts/navg/comp',function(data){
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
							$p.find("[name=tsComp]").after( $row.children() );
						}
						$p.find('[name=tsComp]').data('comp',$('#pageWrapper [child=comp]:first').data('comp'));
						$p.find('[name=tsCompNue]').click(function(){ tsCompNue.init(); }).click().addClass('ui-state-highlight');
						$p.find('[name=tsCompAll]').click(function(){ tsCompAll.init(); });
					},'json');
				}else{
					if($('#pageWrapper [child=comp]').css('display')=='none'){
						$('#pageWrapper [child=comp]').show();
					}else{
						$('#pageWrapper [child=comp]').hide();
					}
				}
			});*/
			$p.find('[name=tsComp]').click(function(){
				$.cookie('action','tsComp');
				window.location.replace('?new=1');
			});
			$p.find('[name=tsMocj]').click(function(){ 
				$('#pageWrapperLeft .ui-state-highlight').removeClass('ui-state-highlight');
				if($('#pageWrapper [child=mocj]').length<=0){
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
						$p.find('[name=tsMocjDep]').click(function(){ tsMocjDep.init(); }).click().addClass('ui-state-highlight');
						$p.find('[name=tsMocjAll]').click(function(){ tsMocjAll.init(); });
					},'json');
				}else{
					if($('#pageWrapper [child=mocj]').css('display')=='none'){
						$('#pageWrapper [child=mocj]').show();
					}else{
						$('#pageWrapper [child=mocj]').hide();
					}
				}
			});
			$p.find('[name=tsCjch]').click(function(){ tsCjch.init(); });
			$p.find('[name=tsConc]').click(function(){ tsConc.init(); });
			$p.find('[name=tsCtban]').click(function(){ tsCtban.init(); });
			$p.find('[name=tsPoli]').click(function(){ 
				$('#pageWrapperLeft .ui-state-highlight').removeClass('ui-state-highlight');
				if($('#pageWrapper [child=poli]').length<=0){
					$.post('ts/navg/poli',function(data){
						for(var i=0; i<data.length; i++){
							var result = data[i];
							var $row = $p.find('.gridReference').clone();
							$row.find('li:eq(0)').html( '<span class="ui-icon '+result.icon+'"></span>' + result.descr )
								.css({
									"padding-left": "10px",
									"min-width": "186px",
									"max-width": "186px"
								});
							$row.wrapInner('<a name="'+result.name+'" class="item" href="javascript: void(0);" child="poli" />');
							$p.find("[name=tsPoli]").after( $row.children() );
						}
						$p.find('[name=tsPoli]').data('poli',$('#pageWrapper [child=poli]:first').data('poli'));
						$p.find('[name=tsPoliPor]').click(function(){ tsPoliPor.init(); }).click().addClass('ui-state-highlight');
						$p.find('[name=tsPoliTod]').click(function(){ tsPoliTod.init(); });
					},'json');
				}else{
					if($('#pageWrapper [child=poli]').css('display')=='none'){
						$('#pageWrapper [child=poli]').show();
					}else{
						$('#pageWrapper [child=poli]').hide();
					}
				}
			});
			$p.find('[name=tsMovi]').click(function(){ 
				$('#pageWrapperLeft .ui-state-highlight').removeClass('ui-state-highlight');
				if($('#pageWrapper [child=movi]').length<=0){
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
						$p.find('[name=tsMoviEfe]').click(function(){ tsMoviEfe.init(); }).click().addClass('ui-state-highlight');
						$p.find('[name=tsMoviCue]').click(function(){ tsMoviCue.init(); });
						$p.find('[name=tsMoviBan]').click(function(){ tsMoviBan.init(); });
						$p.find('[name=tsMoviCjb]').click(function(){ tsMoviCjb.init(); });
					},'json');
				}else{
					if($('#pageWrapper [child=movi]').css('display')=='none'){
						$('#pageWrapper [child=movi]').show();
					}else{
						$('#pageWrapper [child=movi]').hide();
					}
				}
			});
			$p.find('[name=tsTipo]').click(function(){ tsTipo.init(); });
			$p.find('[name=tsSald]').click(function(){ 
				$('#pageWrapperLeft .ui-state-highlight').removeClass('ui-state-highlight');
				if($('#pageWrapper [child=sald]').length<=0){
					$.post('ts/navg/sald',function(data){
						for(var i=0; i<data.length; i++){
							var result = data[i];
							var $row = $p.find('.gridReference').clone();
							$row.find('li:eq(0)').html( '<span class="ui-icon '+result.icon+'"></span>' + result.descr )
								.css({
									"padding-left": "10px",
									"min-width": "186px",
									"max-width": "186px"
								});
							$row.wrapInner('<a name="'+result.name+'" class="item" href="javascript: void(0);" child="sald" />');
							$p.find("[name=tsSald]").after( $row.children() );
						}
						$p.find('[name=tsSald]').data('sald',$('#pageWrapper [child=sald]:first').data('sald'));
						$p.find('[name=tsSaldEfe]').click(function(){ tsSaldEfe.init(); }).click().addClass('ui-state-highlight');
						$p.find('[name=tsSaldCue]').click(function(){ tsSaldCue.init(); });
						$p.find('[name=tsSaldBan]').click(function(){ tsSaldBan.init(); });
					},'json');
				}else{
					if($('#pageWrapper [child=sald]').css('display')=='none'){
						$('#pageWrapper [child=sald]').show();
					}else{
						$('#pageWrapper [child=sald]').hide();
					}
				}
			});
			$p.find('[name=tsRein]').click(function(){ 
				$('#pageWrapperLeft .ui-state-highlight').removeClass('ui-state-highlight');
				if($('#pageWrapper [child=rein]').length<=0){
					$.post('ts/navg/rein',function(data){
						for(var i=0; i<data.length; i++){
							var result = data[i];
							var $row = $p.find('.gridReference').clone();
							$row.find('li:eq(0)').html( '<span class="ui-icon '+result.icon+'"></span>' + result.descr )
								.css({
									"padding-left": "10px",
									"min-width": "186px",
									"max-width": "186px"
								});
							$row.wrapInner('<a name="'+result.name+'" class="item" href="javascript: void(0);" child="rein" />');
							$p.find("[name=tsRein]").after( $row.children() );
						}
						$p.find('[name=tsRein]').data('rein',$('#pageWrapper [child=rein]:first').data('rein'));
						$p.find('[name=tsReinPor]').click(function(){ tsReinPor.init(); }).click().addClass('ui-state-highlight');
						$p.find('[name=tsReinTod]').click(function(){ tsReinTod.init(); });
					},'json');
				}else{
					if($('#pageWrapper [child=rein]').css('display')=='none'){
						$('#pageWrapper [child=rein]').show();
					}else{
						$('#pageWrapper [child=rein]').hide();
					}
				}
			});
			/* ANTIGUA INTERFAZ
			$p.find('[name=tsCheq]').click(function(){
				require(['ts/cheq'],function(tsCheq){
					tsCheq.init();
				});
			});*/
			$p.find('[name=tsCheq]').click(function(){
				$.cookie('action','tsCheq2');
				window.location.replace('?new=1');
			});
			/* ANTIGUA INTERFAZ
			$p.find('[name=tsRede]').click(function(){
				require(['ts/rede'],function(tsRede){
					tsRede.init();
				});
			});*/
			$p.find('[name=tsRede]').click(function(){
				$.cookie('action','tsRede2');
				window.location.replace('?new=1');
			});
			$p.resize();
			switch ($.cookie('action')) {
		    	//case 'tsComp': tsComp.init(); break;
		    	case 'tsComp': $p.find('[name=tsComp]').click(); break;
		    	case 'tsCjch': tsCjch.init(); break;
		    	case 'tsConc': tsConc.init(); break;
		    	case 'tsCtban': tsCtban.init(); break;
		    	case 'tsCtppPen': tsCtppPen.init(); break;
		    	case 'tsCtppAll': tsCtppAll.init(); break;
		    	case 'tsMocjDep': tsMocjDep.init(); break;
		    	case 'tsMocjAll': tsMocjAll.init(); break;
		    	case 'tsPoliPor': tsPoliPor.init(); break;
		    	case 'tsPoliTod': tsPoliTod.init(); break;
		    	case 'tsCompNue': tsCompNue.init(); break;
		    	case 'tsCompAll': tsCompAll.init(); break;
		    	case 'tsMoviEfe': tsMoviEfe.init(); break;
		    	case 'tsMoviCue': tsMoviCue.init(); break;
		    	case 'tsMoviBan': tsMoviBan.init(); break;
		    	case 'tsMoviCjb': tsMoviCjb.init(); break;
		    	case 'tsTipo': tsTipo.init(); break;
		    	case 'tsSaldCue':tsSaldCue.init();break;
		    	case 'tsSaldEfe':tsSaldEfe.init();break;
		    	case 'tsSaldBan':tsSaldBan.init();break;
		    	case 'tsReinPor': tsReinPor.init(); break;
		    	case 'tsReinTod': tsReinTod.init(); break;
		    	case 'tsCheq': $p.find('[name=tsCheq]').click(); break;
		    	//case 'tsRede': $p.find('[name=tsRede]').click(); break;
		    	case 'tsRede': $p.find('[name=tsRede]').click(); break;
		    	default: tsCtppPen.init();
		    }
		}else{
			K.notification({text: 'Usted no tiene permisos para este m&oacute;dulo!',type: 'error', layout: 'topLeft'});
		}
	},'json');
};