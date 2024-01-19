peNavg = function(){
	$.post('pe/navg',function(data){
		if(data!=null){
			var $p = $('#pageWrapperLeft');
			$p.find('label:first').html('Personal');
			$p.find('.gridBody').empty();
			for(var i=0; i<data.length; i++){
				var result = data[i];
				var $row = $p.find('.gridReference').clone();
				$row.find('li:eq(0)').html( '<span class="ui-icon '+result.icon+'"></span>' + result.descr );
				$row.wrapInner('<a name="'+result.name+'" class="item" />');
				$p.find(".gridBody").append( $row.children() );
			}
			$p.find('[name=peGrup]').click(function(){
				$.cookie('action','peGrup');
				window.location.replace('?new=1');
			});
			$p.find('[name=peClas]').click(function(){
				$.cookie('action','peClas');
				window.location.replace('?new=1');
			});
			$p.find('[name=peCarg]').click(function(){
				$.cookie('action','peCarg');
				window.location.replace('?new=1');
			});
			$p.find('[name=peNive]').click(function(){
				$.cookie('action','peNive');
				window.location.replace('?new=1');
			});
			$p.find('[name=peTipo]').click(function(){
				$.cookie('action','peTipo');
				window.location.replace('?new=1');
			});
			$p.find('[name=peSist]').click(function(){
				$.cookie('action','peSist');
				window.location.replace('?new=1');
			});
			$p.find('[name=peCont]').click(function(){
				$.cookie('action','peCont');
				window.location.replace('?new=1');
			});
			$p.find('[name=peConc]').click(function(){
				$.cookie('action','peConc');
				window.location.replace('?new=1');
			});
			$p.find('[name=peEqui]').click(function(){
				$.cookie('action','peEqui');
				window.location.replace('?new=1');
			});
			$p.find('[name=peFeri]').click(function(){
				$.cookie('action','peFeri');
				window.location.replace('?new=1');
			});
			$p.find('[name=peTurn]').click(function(){
				$.cookie('action','peTurn');
				window.location.replace('?new=1');
			});
			$p.find('[name=pePerm]').click(function(){ pePerm.init(); });
			$p.find('[name=peProp]').click(function(){ peProp.init(); });
			$p.find('[name=peEnti]').click(function(){
				$('#pageWrapperLeft .ui-state-highlight').removeClass('ui-state-highlight');
				if($('#pageWrapper [child=enti]').length<=0){
					$.post('pe/navg/enti',function(data){
						for(var i=0; i<data.length; i++){
							var result = data[i];
							var $row = $p.find('.gridReference').clone();
							$row.find('li:eq(0)').html( '<span class="ui-icon '+result.icon+'"></span>' + result.descr )
								.css({
									"padding-left": "10px",
									"min-width": "186px",
									"max-width": "186px"
								});
							$row.wrapInner('<a name="'+result.name+'" class="item" child="enti" />');
							$p.find("[name=peEnti]").after( $row.children() );
						}
						$p.find('[name=peEnti]').data('enti',$('#pageWrapper [child=enti]:first').data('enti'));
						$p.find('[name=peEntiPra]').click(function(){ peEntiPra.init(); });
						$p.find('[name^=peEntiTrab]').click(function(){
							$.cookie('tipo_contrato',$(this).attr('name').substring(10));
							peEntiTrab.init();
						});
						$p.find('[name^=peEntiTrab]:first').addClass('ui-state-highlight').click();
					},'json');
				}else{
					if($('#pageWrapper [child=enti]').css('display')=='none'){
						$('#pageWrapper [child=enti]').show();
					}else{
						$('#pageWrapper [child=enti]').hide();
					}
				}
			});
			$p.find('[name=peCoas]').click(function(){
				$('#pageWrapperLeft .ui-state-highlight').removeClass('ui-state-highlight');
				if($('#pageWrapper [child=coas]').length<=0){
					$.post('pe/navg/coas',function(data){
						for(var i=0; i<data.length; i++){
							var result = data[i];
							var $row = $p.find('.gridReference').clone();
							$row.find('li:eq(0)').html( '<span class="ui-icon '+result.icon+'"></span>' + result.descr )
								.css({
									"padding-left": "10px",
									"min-width": "186px",
									"max-width": "186px"
								});
							$row.wrapInner('<a name="'+result.name+'" class="item" child="coas" />');
							$p.find("[name=peCoas]").after( $row.children() );
						}
						$p.find('[name=peCoas]').data('coas',$('#pageWrapper [child=coas]:first').data('coas'));
						$p.find('[name=peCoasHora]').click(function(){ peCoasHora.init(); }).addClass('ui-state-highlight').click();
						$p.find('[name=peCoasAsis]').click(function(){ peCoasAsis.init(); });
						$p.find('[name=peCoasInci]').click(function(){ peCoasInci.init(); });
						$p.find('[name=peCoasProg]').click(function(){ peCoasProg.init(); });
					},'json');
				}else{
					if($('#pageWrapper [child=coas]').css('display')=='none'){
						$('#pageWrapper [child=coas]').show();
					}else{
						$('#pageWrapper [child=coas]').hide();
					}
				}
			});
			$p.find('[name=pePlan]').click(function(){
				$('#pageWrapperLeft .ui-state-highlight').removeClass('ui-state-highlight');
				if($('#pageWrapper [child=plan]').length<=0){
					$.post('pe/navg/plan',function(data){
						for(var i=0; i<data.length; i++){
							var result = data[i];
							var $row = $p.find('.gridReference').clone();
							$row.find('li:eq(0)').html( '<span class="ui-icon '+result.icon+'"></span>' + result.descr )
								.css({
									"padding-left": "10px",
									"min-width": "186px",
									"max-width": "186px"
								});
							$row.wrapInner('<a name="'+result.name+'" class="item" child="plan" />');
							$p.find("[name=pePlan]").after( $row.children() );
						}
						$p.find('[name=pePlan]').data('plan',$('#pageWrapper [child=plan]:first').data('plan'));
						/*$p.find('[name=pePlanPer]').click(function(){ pePlan276.init(); }).addClass('ui-state-highlight').click();
						$p.find('[name=pePlanCas]').click(function(){ pePlanCas.init(); });*/
						$p.find('[name=pePlanFal]').click(function(){ pePlanFal.init(); });
						$p.find('[name=pePlanBon]').click(function(){ pePlanBon.init(); });
						$p.find('[name=pePlanEnf]').click(function(){ pePlanEnf.init(); });
						$p.find('[name=pePlanSep]').click(function(){ pePlanSep.init(); });
						$p.find('[name=pePlanVac]').click(function(){ pePlanVac.init(); });
						$p.find('[name=pePlanVei]').click(function(){ pePlanVei.init(); });
						$p.find('[name=pePlanTre]').click(function(){ pePlanTre.init(); });
						$p.find('[name=pePlanMat]').click(function(){ pePlanMat.init(); });
						$p.find('[name=pePlanQui]').click(function(){ pePlanQui.init(); });
						$p.find('[name^=pePlanBole]').click(function(){
							$.cookie('tipo_contrato',$(this).attr('name').substring(10));
							pePlanBole.init();
						});
						$p.find('[name^=pePlanBole]:first').addClass('ui-state-highlight').click();
					},'json');
				}else{
					if($('#pageWrapper [child=plan]').css('display')=='none'){
						$('#pageWrapper [child=plan]').show();
					}else{
						$('#pageWrapper [child=plan]').hide();
					}
				}
			});
			$p.find('[name=pePres]').click(function(){ pePres.init(); });
			$p.find('[name=peCuad]').click(function(){ peCuad.init(); });
			$p.find('[name=peVaca]').click(function(){ peVaca.init(); });
			$p.find('[name=peLice]').click(function(){ peLice.init(); });
			$p.find('[name=peRepo]').click(function(){
				$('#pageWrapperLeft .ui-state-highlight').removeClass('ui-state-highlight');
				if($('#pageWrapper [child=repo]').length<=0){
					$.post('pe/navg/repo',function(data){
						for(var i=0; i<data.length; i++){
							var result = data[i];
							var $row = $p.find('.gridReference').clone();
							$row.find('li:eq(0)').html( '<span class="ui-icon '+result.icon+'"></span>' + result.descr )
								.css({
									"padding-left": "10px",
									"min-width": "186px",
									"max-width": "186px"
								});
							$row.wrapInner('<a name="'+result.name+'" class="item" child="repo" />');
							$p.find("[name=peRepo]").after( $row.children() );
						}
						$p.find('[name=peRepo]').data('repo',$('#pageWrapper [child=repo]:first').data('repo'));
						$p.find('[name=peRepoInci]').click(function(){ peRepoInci.init(); }).addClass('ui-state-highlight').click();
						$p.find('[name=peRepoGene]').click(function(){ peRepo.init(); });
					},'json');
				}else{
					if($('#pageWrapper [child=repo]').css('display')=='none'){
						$('#pageWrapper [child=repo]').show();
					}else{
						$('#pageWrapper [child=repo]').hide();
					}
				}
			});
			$p.resize();
			switch ($.cookie('action')) {
		    	case 'peNive': $p.find('[name=peNive]').click(); break;
		    	case 'peCarg': $p.find('[name=peCarg]').click(); break;
		    	case 'peEqui': $p.find('[name=peEqui]').click(); break;
		    	case 'pePerm': pePerm.init(); break;
		    	case 'peTurn': peTurn.init(); break;
		    	case 'peCont': $p.find('[name=peCont]').click(); break;
		    	case 'peSist': $p.find('[name=peSist]').click(); break;
		    	/*case 'peEnti276': peEnti276.init(); break;
		    	case 'peEntiCas': peEntiCas.init(); break;*/
		    	case 'peEntiTrab': peEntiTrab.init(); break;
		    	case 'peEntiPra': peEntiPra.init(); break;
		    	case 'peFeri': peFeri.init(); break;
		    	case 'peClas': $p.find('[name=peClas]').click(); break;
		    	case 'peGrup': $p.find('[name=peGrup]').click(); break;
		    	case 'peTipo': $p.find('[name=peTipo]').click(); break;
		    	case 'peCoasHora': peCoasHora.init(); break;
		    	case 'peCoasAsis': peCoasAsis.init(); break;
		    	case 'peCoasInci': peCoasInci.init(); break;
		    	case 'peCoasProg': peCoasProg.init(); break;
		    	case 'peConc': $p.find('[name=peConc]').click(); break;
		    	case 'pePlanBole': pePlanBole.init(); break;
		    	case 'pePlanVei': pePlanVei.init(); break;
		    	case 'pePlanTre': pePlanTre.init(); break;
		    	case 'pePlanVac': pePlanVac.init(); break;
		    	case 'pePlanSep': pePlanSep.init(); break;
		    	case 'pePlanEnf': pePlanEnf.init(); break;
		    	case 'pePlanBon': pePlanBon.init(); break;
		    	case 'pePlanFal': pePlanFal.init(); break;
		    	case 'pePlanMat': pePlanMat.init(); break;
		    	case 'pePlanQui': pePlanQui.init(); break;
		    	case 'pePres': pePres.init(); break;
		    	case 'peCuad': peCuad.init(); break;
		    	case 'peVaca': peVaca.init(); break;
		    	case 'peLice': peLice.init(); break;
		    	case 'peProp': peProp.init(); break;
		    	case 'peRepoInci': peRepoInci.init(); break;
		    	case 'peRepoGene': peRepo.init(); break;
		    	default: peRepo.init();
		    }
		}else{
			K.notification({text: 'Usted no tiene permisos para este m&oacute;dulo!',type: 'error'});
		}
	},'json');
};