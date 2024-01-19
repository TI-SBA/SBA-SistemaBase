ctNavg = function(){
	$.post('ct/navg',function(data){
		if(data!=null){
			var $p = $('#pageWrapperLeft');
			$p.find('label:first').html('Contabilidad');
			$p.find('.gridBody').empty();
			for(var i=0; i<data.length; i++){
				var result = data[i];
				var $row = $p.find('.gridReference').clone();
				$row.find('li:eq(0)').html( '<span class="ui-icon '+result.icon+'"></span>' + result.descr );
				$row.wrapInner('<a name="'+result.name+'" class="item" href="javascript: void(0);" />');
				$p.find(".gridBody").append( $row.children() );
			}
			$p.find('[name=ctPcon]').click(function(){ ctPcon.init(); });
			/*$p.find('[name=ctTnot]').click(function(){ ctTnot.init(); });*/
			$p.find('[name=ctPcue]').click(function(){ ctPcue.init(); });
			$p.find('[name=ctColi]').click(function(){ ctColi.init(); });
			$p.find('[name=ctRcom]').click(function(){ ctRcom.init(); });
			$p.find('[name=ctRven]').click(function(){ ctRven.init(); });
			//$p.find('[name=ctLibr]').click(function(){ ctLibr.init(); });
			$p.find('[name=ctCpat]').click(function(){ ctCpat.init(); });
			$p.find('[name=ctNotc]').click(function(){ ctNotc.init(); });
			$p.find('[name=ctNota]').click(function(){ 
				$('#pageWrapperLeft .ui-state-highlight').removeClass('ui-state-highlight');
				if($('#pageWrapper [child=nota]').length<=0){
					$.post('ct/navg/nota',function(data){
						for(var i=0; i<data.length; i++){
							var result = data[i];
							var $row = $p.find('.gridReference').clone();
							$row.find('li:eq(0)').html( '<span class="ui-icon '+result.icon+'"></span>' + result.descr )
								.css({
									"padding-left": "10px",
									"min-width": "186px",
									"max-width": "186px"
								});
							$row.wrapInner('<a name="'+result.name+'" class="item" href="javascript: void(0);" child="nota" />');
							$p.find("[name=ctNota]").after( $row.children() );
						}
						$p.find('[name=ctNota]').data('nota',$('#pageWrapper [child=nota]:first').data('nota'));
						$p.find('[name=ctNotaLit]').click(function(){ ctNotaLit.init(); }).click().addClass('ui-state-highlight');
						$p.find('[name=ctNotaNum]').click(function(){ ctNotaNum.init(); });
					},'json');
				}else{
					if($('#pageWrapper [child=nota]').css('display')=='none'){
						$('#pageWrapper [child=nota]').show();
					}else{
						$('#pageWrapper [child=nota]').hide();
					}
				}
			});
			$p.find('[name=ctCban]').click(function(){ ctCban.init(); });
			$p.find('[name=ctTico]').click(function(){ ctTico.init(); });
			$p.find('[name=ctAuxs]').click(function(){ 
				$('#pageWrapperLeft .ui-state-highlight').removeClass('ui-state-highlight');
				if($('#pageWrapper [child=auxs]').length<=0){
					$.post('ct/navg/auxs',function(data){
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
						$p.find('[name=ctAuxsActi]').click(function(){ ctAuxsActi.init(); }).click().addClass('ui-state-highlight');
						$p.find('[name=ctAuxsPasi]').click(function(){ ctAuxsPasi.init(); });
						$p.find('[name=ctAuxsPatr]').click(function(){ ctAuxsPatr.init(); });
						$p.find('[name=ctAuxsIngr]').click(function(){ ctAuxsIngr.init(); });
						$p.find('[name=ctAuxsGast]').click(function(){ ctAuxsGast.init(); });
						$p.find('[name=ctAuxsResu]').click(function(){ ctAuxsResu.init(); });
						$p.find('[name=ctAuxsPres]').click(function(){ ctAuxsPres.init(); });
						$p.find('[name=ctAuxsDeor]').click(function(){ ctAuxsDeor.init(); });
					},'json');
				}else{
					if($('#pageWrapper [child=auxs]').css('display')=='none'){
						$('#pageWrapper [child=auxs]').show();
					}else{
						$('#pageWrapper [child=auxs]').hide();
					}
				}
			});
			$p.find('[name=ctMocu]').click(function(){ 
				$('#pageWrapperLeft .ui-state-highlight').removeClass('ui-state-highlight');
				if($('#pageWrapper [child=mocu]').length<=0){
					$.post('ct/navg/mocu',function(data){
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
						$p.find('[name=ctMocuActi]').click(function(){ ctMocuActi.init(); }).click().addClass('ui-state-highlight');
						$p.find('[name=ctMocuPasi]').click(function(){ ctMocuPasi.init(); });
						$p.find('[name=ctMocuResu]').click(function(){ ctMocuResu.init(); });
						$p.find('[name=ctMocuDeor]').click(function(){ ctMocuDeor.init(); });
					},'json');
				}else{
					if($('#pageWrapper [child=mocu]').css('display')=='none'){
						$('#pageWrapper [child=mocu]').show();
					}else{
						$('#pageWrapper [child=mocu]').hide();
					}
				}
			});
			$p.find('[name=ctEpres]').click(function(){ 
				$('#pageWrapperLeft .ui-state-highlight').removeClass('ui-state-highlight');
				if($('#pageWrapper [child=epres]').length<=0){
					$.post('ct/navg/epres',function(data){
						for(var i=0; i<data.length; i++){
							var result = data[i];
							var $row = $p.find('.gridReference').clone();
							$row.find('li:eq(0)').html( '<span class="ui-icon '+result.icon+'"></span>' + result.descr )
								.css({
									"padding-left": "10px",
									"min-width": "186px",
									"max-width": "186px"
								});
							$row.wrapInner('<a name="'+result.name+'" class="item" href="javascript: void(0);" child="epres" />');
							$p.find("[name=ctEpres]").after( $row.children() );
						}
						$p.find('[name=ctEpres]').data('epres',$('#pageWrapper [child=epres]:first').data('epres'));
						$p.find('[name=ctEpresAuxI]').click(function(){ ctEpresAuxI.init(); }).click().addClass('ui-state-highlight');
						$p.find('[name=ctEpresAuxG]').click(function(){ ctEpresAuxG.init(); });
						$p.find('[name=ctEpresCuadce]').click(function(){ ctEpresCuadce.init(); });
						$p.find('[name=ctEpresPpres]').click(function(){ ctEpresPpres.init(); });
						$p.find('[name=ctEpresMovi]').click(function(){ ctEpresMovi.init(); });
					},'json');
				}else{
					if($('#pageWrapper [child=epres]').css('display')=='none'){
						$('#pageWrapper [child=epres]').show();
					}else{
						$('#pageWrapper [child=epres]').hide();
					}
				}
			});
			$p.find('[name=ctMayc]').click(function(){ 
				$('#pageWrapperLeft .ui-state-highlight').removeClass('ui-state-highlight');
				if($('#pageWrapper [child=mayc]').length<=0){
					$.post('ct/navg/mayc',function(data){
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
						$p.find('[name=ctMaycActi]').click(function(){ ctMaycActi.init(); }).click().addClass('ui-state-highlight');
						$p.find('[name=ctMaycPasi]').click(function(){ ctMaycPasi.init(); });
						$p.find('[name=ctMaycResu]').click(function(){ ctMaycResu.init(); });
						$p.find('[name=ctMaycDeor]').click(function(){ ctMaycDeor.init(); });
					},'json');
				}else{
					if($('#pageWrapper [child=mayc]').css('display')=='none'){
						$('#pageWrapper [child=mayc]').show();
					}else{
						$('#pageWrapper [child=mayc]').hide();
					}
				}
			});
			$p.find('[name=ctCoal]').click(function(){ 
				$('#pageWrapperLeft .ui-state-highlight').removeClass('ui-state-highlight');
				if($('#pageWrapper [child=coal]').length<=0){
					$.post('ct/navg/coal',function(data){
						for(var i=0; i<data.length; i++){
							var result = data[i];
							var $row = $p.find('.gridReference').clone();
							$row.find('li:eq(0)').html( '<span class="ui-icon '+result.icon+'"></span>' + result.descr )
								.css({
									"padding-left": "10px",
									"min-width": "186px",
									"max-width": "186px"
								});
							$row.wrapInner('<a name="'+result.name+'" class="item" href="javascript: void(0);" child="coal" />');
							$p.find("[name=ctCoal]").after( $row.children() );
						}
						$p.find('[name=ctCoal]').data('coal',$('#pageWrapper [child=coal]:first').data('coal'));
						$p.find('[name=ctCoalEntr]').click(function(){ ctCoalEntr.init(); }).click().addClass('ui-state-highlight');
						$p.find('[name=ctCoalSali]').click(function(){ ctCoalSali.init(); });
					},'json');
				}else{
					if($('#pageWrapper [child=coal]').css('display')=='none'){
						$('#pageWrapper [child=coal]').show();
					}else{
						$('#pageWrapper [child=coal]').hide();
					}
				}
			});
			$p.find('[name=ctLidi]').click(function(){ 
				$('#pageWrapperLeft .ui-state-highlight').removeClass('ui-state-highlight');
				if($('#pageWrapper [child=lidi]').length<=0){
					$.post('ct/navg/lidi',function(data){
						for(var i=0; i<data.length; i++){
							var result = data[i];
							var $row = $p.find('.gridReference').clone();
							$row.find('li:eq(0)').html( '<span class="ui-icon '+result.icon+'"></span>' + result.descr )
								.css({
									"padding-left": "10px",
									"min-width": "186px",
									"max-width": "186px"
								});
							$row.wrapInner('<a name="'+result.name+'" class="item" href="javascript: void(0);" child="lidi" />');
							$p.find("[name=ctLidi]").after( $row.children() );
						}
						$p.find('[name=ctLidi]').data('lidi',$('#pageWrapper [child=lidi]:first').data('lidi'));
						$p.find('[name=ctLidiBene]').click(function(){ ctLidiBene.init(); }).click().addClass('ui-state-highlight');
						$p.find('[name=ctLidiSuna]').click(function(){ ctLidiSuna.init(); });
					},'json');
				}else{
					if($('#pageWrapper [child=lidi]').css('display')=='none'){
						$('#pageWrapper [child=lidi]').show();
					}else{
						$('#pageWrapper [child=lidi]').hide();
					}
				}
			});
			$p.find('[name=ctLima]').click(function(){ 
				$('#pageWrapperLeft .ui-state-highlight').removeClass('ui-state-highlight');
				if($('#pageWrapper [child=lima]').length<=0){
					$.post('ct/navg/lima',function(data){
						for(var i=0; i<data.length; i++){
							var result = data[i];
							var $row = $p.find('.gridReference').clone();
							$row.find('li:eq(0)').html( '<span class="ui-icon '+result.icon+'"></span>' + result.descr )
								.css({
									"padding-left": "10px",
									"min-width": "186px",
									"max-width": "186px"
								});
							$row.wrapInner('<a name="'+result.name+'" class="item" href="javascript: void(0);" child="lima" />');
							$p.find("[name=ctLima]").after( $row.children() );
						}
						$p.find('[name=ctLima]').data('lima',$('#pageWrapper [child=lima]:first').data('lima'));
						$p.find('[name=ctLimaBene]').click(function(){ ctLimaBene.init(); }).click().addClass('ui-state-highlight');
						$p.find('[name=ctLimaSuna]').click(function(){ ctLimaSuna.init(); });
					},'json');
				}else{
					if($('#pageWrapper [child=lima]').css('display')=='none'){
						$('#pageWrapper [child=lima]').show();
					}else{
						$('#pageWrapper [child=lima]').hide();
					}
				}
			});
			$p.find('[name=ctOrde]').click(function(){ 
				$('#pageWrapperLeft .ui-state-highlight').removeClass('ui-state-highlight');
				if($('#pageWrapper [child=orde]').length<=0){
					$.post('ct/navg/orde',function(data){
						for(var i=0; i<data.length; i++){
							var result = data[i];
							var $row = $p.find('.gridReference').clone();
							$row.find('li:eq(0)').html( '<span class="ui-icon '+result.icon+'"></span>' + result.descr )
								.css({
									"padding-left": "10px",
									"min-width": "186px",
									"max-width": "186px"
								});
							$row.wrapInner('<a name="'+result.name+'" class="item" href="javascript: void(0);" child="orde" />');
							$p.find("[name=ctOrde]").after( $row.children() );
						}
						$p.find('[name=ctOrde]').data('orde',$('#pageWrapper [child=orde]:first').data('orde'));
						$p.find('[name=ctOrdeComp]').click(function(){ ctOrdeComp.init(); });
						$p.find('[name=ctOrdeServ]').click(function(){ ctOrdeServ.init(); }).click().addClass('ui-state-highlight');
					},'json');
				}else{
					if($('#pageWrapper [child=orde]').css('display')=='none'){
						$('#pageWrapper [child=orde]').show();
					}else{
						$('#pageWrapper [child=orde]').hide();
					}
				}
			});
			$p.find('[name=ctRepo]').click(function(){ ctRepo.init(); });
			$p.resize();
			switch ($.cookie('action')) {
		    	case 'ctPcon': ctPcon.init(); break;
		    	case 'ctTnot': ctTnot.init(); break;
		    	case 'ctPcue': ctPcue.init(); break;
		    	case 'ctColi': ctColi.init(); break;
		    	case 'ctRcom': ctRcom.init(); break;
		    	case 'ctRven': ctRven.init(); break;
		    	//case 'ctLibr': ctLibr.init(); break;
		    	case 'ctCpat': ctCpat.init(); break;
		    	case 'ctNotaLit': ctNotaLit.init(); break;
		    	case 'ctNotaNum': ctNotaNum.init(); break;
		    	case 'ctNotc': ctNotc.init(); break;
		    	case 'ctCban': ctCban.init(); break;
		    	case 'ctTico': ctTico.init(); break;
		    	case 'ctAuxsActi': ctAuxsActi.init(); break;
		    	case 'ctAuxsPasi': ctAuxsPasi.init(); break;
		    	case 'ctAuxsPatr': ctAuxsPatr.init(); break;
		    	case 'ctAuxsIngr': ctAuxsIngr.init(); break;
		    	case 'ctAuxsGast': ctAuxsGast.init(); break;
		    	case 'ctAuxsResu': ctAuxsResu.init(); break;
		    	case 'ctAuxsPres': ctAuxsPres.init(); break;
		    	case 'ctAuxsDeor': ctAuxsDeor.init(); break;
		    	case 'ctMocuActi': ctMocuActi.init(); break;
		    	case 'ctMocuPasi': ctMocuPasi.init(); break;
		    	case 'ctMocuResu': ctMocuResu.init(); break;
		    	case 'ctMocuDeor': ctMocuDeor.init(); break;
		    	case 'ctMaycActi': ctMaycActi.init(); break;
		    	case 'ctMaycPasi': ctMaycPasi.init(); break;
		    	case 'ctMaycResu': ctMaycResu.init(); break;
		    	case 'ctMaycDeor': ctMaycDeor.init(); break;
		    	case 'ctEpresAuxI': ctEpresAuxI.init(); break;
		    	case 'ctEpresAuxG': ctEpresAuxG.init(); break;
		    	case 'ctEpresCuadce': ctEpresCuadce.init(); break;
		    	case 'ctEpresPpres': ctEpresPpres.init(); break;
		    	case 'ctEpresPpresg': ctEpresPpresg.init(); break;
		    	case 'ctEpresMovi': ctEpresMovi.init(); break;
		    	case 'ctCoalEntr': ctCoalEntr.init(); break;
		    	case 'ctCoalSali': ctCoalSali.init(); break;
		    	case 'ctLidiBene': ctLidiBene.init(); break;
		    	case 'ctLidiSuna': ctLidiSuna.init(); break;
		    	case 'ctLimaBene': ctLimaBene.init(); break;
		    	case 'ctLimaSuna': ctLimaSuna.init(); break;
		    	case 'ctOrdeComp': ctOrdeComp.init(); break;
		    	case 'ctOrdeServ': ctOrdeServ.init(); break;
		    	case 'ctRepo': ctRepo.init(); break;
		    	default: $p.find('[name=ctTnot]').click();
		    }
		}else{
			K.notification({text: 'Usted no tiene permisos para este m&oacute;dulo!',type: 'error', layout: 'topLeft'});
		}
	},'json');
};