alNavg = function(){
	$.post('al/navg',function(data){
		if(data!=null){
			var $p = $('#pageWrapperLeft');
			$p.find('label:first').html('Asesor&iacute;a Legal');
			$p.find('.gridBody').empty();
			for(var i=0; i<data.length; i++){
				var result = data[i];
				var $row = $p.find('.gridReference').clone();
				$row.find('li:eq(0)').html( '<span class="ui-icon '+result.icon+'"></span>' + result.descr );
				$row.wrapInner('<a name="'+result.name+'" class="item" href="javascript: void(0);" />');
				$p.find(".gridBody").append( $row.children() );
			}
			//$p.find('[name=alExpdTipos]').click(function(){ alExpdTipos.init(); });
			$p.find('[name=alExpd]').click(function(){ 
				$('#pageWrapperLeft .ui-state-highlight').removeClass('ui-state-highlight');
				if($('#pageWrapper [child=alexpd]').length<=0){
					$.post('al/navg/expd',function(data){
						for(var i=0; i<data.length; i++){
							var result = data[i];
							var $row = $p.find('.gridReference').clone();
							$row.find('li:eq(0)').html( '<span class="ui-icon '+result.icon+'"></span>' + result.descr )
								.css({
									"padding-left": "10px",
									"min-width": "186px",
									"max-width": "186px"
								});
							$row.wrapInner('<a name="'+result.name+'" class="item" href="javascript: void(0);" child="alexpd" />');
							$p.find("[name=alExpd]").after( $row.children() );
						}
						$p.find('[name=alExpd]').data('alexpd',$('#pageWrapper [child=alexpd]:first').data('alexpd'));
						$p.find('[name=alExpdActi]').click(function(){ alExpdActi.init(); }).click().addClass('ui-state-highlight');
						$p.find('[name=alExpdArch]').click(function(){ alExpdArch.init(); });
					},'json');
				}else{
					if($('#pageWrapper [child=alexpd]').css('display')=='none'){
						$('#pageWrapper [child=alexpd]').show();
					}else{
						$('#pageWrapper [child=alexpd]').hide();
					}
				}
			});
			$p.find('[name=alCont]').click(function(){ 
				$('#pageWrapperLeft .ui-state-highlight').removeClass('ui-state-highlight');
				if($('#pageWrapper [child=alcont]').length<=0){
					$.post('al/navg/cont',function(data){
						for(var i=0; i<data.length; i++){
							var result = data[i];
							var $row = $p.find('.gridReference').clone();
							$row.find('li:eq(0)').html( '<span class="ui-icon '+result.icon+'"></span>' + result.descr )
								.css({
									"padding-left": "10px",
									"min-width": "186px",
									"max-width": "186px"
								});
							$row.wrapInner('<a name="'+result.name+'" class="item" href="javascript: void(0);" child="alcont" />');
							$p.find("[name=alCont]").after( $row.children() );
						}
						$p.find('[name=alCont]').data('alcont',$('#pageWrapper [child=alcont]:first').data('alcont'));
						$p.find('[name=alContFav]').click(function(){ alContFav.init(); }).click().addClass('ui-state-highlight');
						$p.find('[name=alContCont]').click(function(){ alContCont.init(); });
					},'json');
				}else{
					if($('#pageWrapper [child=alcont]').css('display')=='none'){
						$('#pageWrapper [child=alcont]').show();
					}else{
						$('#pageWrapper [child=alcont]').hide();
					}
				}
			});
			$p.find('[name=alDili]').click(function(){ 
				$('#pageWrapperLeft .ui-state-highlight').removeClass('ui-state-highlight');
				if($('#pageWrapper [child=aldili]').length<=0){
					$.post('al/navg/dili',function(data){
						for(var i=0; i<data.length; i++){
							var result = data[i];
							var $row = $p.find('.gridReference').clone();
							$row.find('li:eq(0)').html( '<span class="ui-icon '+result.icon+'"></span>' + result.descr )
								.css({
									"padding-left": "10px",
									"min-width": "186px",
									"max-width": "186px"
								});
							$row.wrapInner('<a name="'+result.name+'" class="item" href="javascript: void(0);" child="aldili" />');
							$p.find("[name=alDili]").after( $row.children() );
						}
						$p.find('[name=alDili]').data('aldili',$('#pageWrapper [child=aldili]:first').data('aldili'));
						$p.find('[name=alDiliProg]').click(function(){ alDiliProg.init(); }).click().addClass('ui-state-highlight');
						$p.find('[name=alDiliEjec]').click(function(){ alDiliEjec.init(); });
						$p.find('[name=alDiliSusp]').click(function(){ alDiliSusp.init(); });
					},'json');
				}else{
					if($('#pageWrapper [child=aldili]').css('display')=='none'){
						$('#pageWrapper [child=aldili]').show();
					}else{
						$('#pageWrapper [child=aldili]').hide();
					}
				}
			});
			$p.find('[name=alConv]').click(function(){
				$.cookie('action','alConv');
				window.location.replace('?new=1');
			});
			$p.find('[name=alRepo]').click(function(){ alRepo.init(); });
			$p.resize();
			switch ($.cookie('action')) {
		    	case 'alExpdTipos': alExpdTipos.init(); break;	
		    	case 'alExpdActi': alExpdActi.init(); break;
		    	case 'alExpdArch': alExpdArch.init(); break;
		    	case 'alContFav': alContFav.init(); break;
		    	case 'alContCont': alContCont.init(); break;
		    	case 'alDiliProg': alDiliProg.init(); break;
		    	case 'alDiliEjec': alDiliEjec.init(); break;
		    	case 'alDiliSusp': alDiliSusp.init(); break;
		    	case 'alConv': $p.find('[name=alConv]').click(); break;
		    	case 'alConv': alRepo.init(); break;
		    	default: $p.find('[name=alConv]').click();
		    }
		}else{
			K.notification({text: 'Usted no tiene permisos para este m&oacute;dulo!',type: 'error', layout: 'topLeft'});
		}
	},'json');
};