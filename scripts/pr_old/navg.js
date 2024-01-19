prNavg = function(){
	$.post('pr/navg',function(data){
		if(data!=null){
			var $p = $('#pageWrapperLeft');
			$p.find('label:first').html('Planificaci&oacute;n y Presupuesto');
			$p.find('.gridBody').empty();
			for(var i=0; i<data.length; i++){
				var result = data[i];
				var $row = $p.find('.gridReference').clone();
				$row.find('li:eq(0)').html( '<span class="ui-icon '+result.icon+'"></span>' + result.descr );
				$row.wrapInner('<a name="'+result.name+'" class="item" href="javascript: void(0);" />');
				$p.find(".gridBody").append( $row.children() );
			}
			$p.find('[name=prClas]').click(function(){ prClas.init(); });
			$p.find('[name=prEstr]').click(function(){ prEstr.init(); });
			$p.find('[name=prEprog]').click(function(){ prEprog.init(); });
			$p.find('[name=prActi]').click(function(){ prActi.init(); });
			$p.find('[name=prFuen]').click(function(){ prFuen.init(); });
			$p.find('[name=prUnid]').click(function(){ prUnid.init(); });
			$p.find('[name=prMeta]').click(function(){ prMeta.init(); });
			$p.find('[name=prMefi]').click(function(){ prMefi.init(); });
			$p.find('[name=prRese]').click(function(){ prRese.init(); });
			$p.find('[name=prSald]').click(function(){ prSald.init(); });
			$p.find('[name=prPres]').click(function(){ 
				$('#pageWrapperLeft .ui-state-highlight').removeClass('ui-state-highlight');
				if($('#pageWrapper [child=pres]').length<=0){
					$.post('pr/navg/pres',function(data){
						for(var i=0; i<data.length; i++){
							var result = data[i];
							var $row = $p.find('.gridReference').clone();
							$row.find('li:eq(0)').html( '<span class="ui-icon '+result.icon+'"></span>' + result.descr )
								.css({
									"padding-left": "10px",
									"min-width": "186px",
									"max-width": "186px"
								});
							$row.wrapInner('<a name="'+result.name+'" class="item" href="javascript: void(0);" child="pres" />');
							$p.find("[name=prPres]").after( $row.children() );
						}
						$p.find('[name=prPres]').data('pres',$('#pageWrapper [child=pres]:first').data('pres'));
						$p.find('[name=prPresAper]').click(function(){ prPresAper.init(); }).click().addClass('ui-state-highlight');
						$p.find('[name=prPresModi]').click(function(){ prPresModi.init(); });
						$p.find('[name=prPresModi_Nota]').click(function(){ prPresModiNota.init(); });
						$p.find('[name=prPresModi_Cred]').click(function(){ prPresModiCred.init(); });
					},'json');
				}else{
					if($('#pageWrapper [child=pres]').css('display')=='none'){
						$('#pageWrapper [child=pres]').show();
					}else{
						$('#pageWrapper [child=pres]').hide();
					}
				}
			});
			$p.find('[name=prPlan]').click(function(){ 
				$('#pageWrapperLeft .ui-state-highlight').removeClass('ui-state-highlight');
				if($('#pageWrapper [child=plan]').length<=0){
					$.post('pr/navg/plan',function(data){
						for(var i=0; i<data.length; i++){
							var result = data[i];
							var $row = $p.find('.gridReference').clone();
							$row.find('li:eq(0)').html( '<span class="ui-icon '+result.icon+'"></span>' + result.descr )
								.css({
									"padding-left": "10px",
									"min-width": "186px",
									"max-width": "186px"
								});
							$row.wrapInner('<a name="'+result.name+'" class="item" href="javascript: void(0);" child="plan" />');
							$p.find("[name=prPlan]").after( $row.children() );
						}
						$p.find('[name=prPlan]').data('plan',$('#pageWrapper [child=plan]:first').data('plan'));
						$p.find('[name=prPlanProgDep]').click(function(){ prPlanProgDep.init(); });
						$p.find('[name=prPlanEjecDep]').click(function(){ prPlanEjecDep.init(); });
						$p.find('[name=prPlanProg]').click(function(){ prPlanProg.init(); }).click().addClass('ui-state-highlight');
						$p.find('[name=prPlanEjec]').click(function(){ prPlanEjec.init(); });

					},'json');
				}else{
					if($('#pageWrapper [child=plan]').css('display')=='none'){
						$('#pageWrapper [child=plan]').show();
					}else{
						$('#pageWrapper [child=plan]').hide();
					}
				}
			});
			$p.find('[name=prRepo]').click(function(){ prRepo.init(); });
			$p.resize();
			switch ($.cookie('action')) {
		    	case 'prClas': prClas.init(); break;
		    	case 'prEstr': prEstr.init(); break;
		    	case 'prEprog': prEprog.init(); break;
		    	case 'prActi': prActi.init(); break;
		    	case 'prFuen': prFuen.init(); break;
		    	case 'prUnid': prUnid.init(); break;
		    	case 'prPlanEjec': prPlanEjec.init(); break; 
		    	case 'prPlanProg': prPlanProg.init(); break; 
		    	case 'prPresAper': prPresAper.init(); break; 
		    	case 'prPresModi': prPresModi.init(); break; 
		    	case 'prPresModi_Nota': prPresModiNota.init(); break; 	
		    	case 'prPresModi_Cred': prPresModiCred.init(); break; 	
		    	case 'prMeta': prMeta.init(); break;
		    	case 'prMefi': prMefi.init(); break;
		    	case 'prRese': prRese.init(); break;
		    	case 'prSald': prSald.init(); break;
		    	case 'prRepo': prRepo.init(); break; 		    	
		    	default: $p.find('[name=prUnid]').click();
		    }
		}else{
			K.notification({text: 'Usted no tiene permisos para este m&oacute;dulo!',type: 'error', layout: 'topLeft'});
		}
	},'json');
};