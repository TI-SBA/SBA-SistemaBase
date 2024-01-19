/*******************************************************************************
Credito Suplementario */
prPresModiCred = {
	init: function(){
		if($('#pageWrapper [child=pres]').length<=0){
			var $p = $('#pageWrapperLeft');
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
				$p.find('[name=prPresAper]').click(function(){ prPresAper.init(); });
				$p.find('[name=prPresModi]').click(function(){ prPresModi.init(); });
				$p.find('[name=prPresModi_Nota]').click(function(){ prPresModiNota.init(); });
				$p.find('[name=prPresModi_Cred]').click(function(){ prPresModiCred.init(); }).addClass('ui-state-highlight');
			},'json');
		}
		K.initMode({
			mode: 'pr',
			action: 'prPresModi_Cred',
			titleBar: {
				title: 'Creditos Suplementarios'
			}
		});
		
		new K.Panel({
			id: 'mainPanel',
			contentURL: 'pr/cred',
			onContentLoaded: function(){				
				$mainPanel = $('#mainPanel');
				$mainPanel.find('[name=ano]').numeric().spinner({step: 1,min: 1900,max: 2100,stop: function(){ $(this).change(); }}).change(function(){
					//loadData
			    });			
				$mainPanel.find('[name=ano]').parent().find('.ui-button').css('height','14px');
				var d = new Date();
				$mainPanel.find('[name=ano]').val(d.getFullYear()); 
				var $cbo_n = $mainPanel.find('[name=cred]');
				$mainPanel.find('[name=ano]').change(function(){
					K.block({$element: $('#pageWrapperMain')});
					$.post('pr/pres/get_num_credito',{periodo:$mainPanel.find('[name=ano]').val()},function(data2){
						$cbo_n.empty();
						if(data2==null){
							//no hay notas modificatorias
						}else{
							for(i=0;i<parseFloat(data2[0].num_credito);i++){
								$cbo_n.append('<option value="'+(i+1)+'">'+(i+1)+'</option>');
							}
						}
						K.unblock({$element: $('#pageWrapperMain')});
					},'json');
				});
				$.post('pr/fuen/all',function(data){
					var $cbo = $mainPanel.find('[name=fuente]');
					//$cbo.append('<option value="">Todas</option>');
					if(data!=null){
						for(var i=0; i<data.length; i++){
							var rubro = data[i].rubro;
							var cod = data[i].cod;
							var id = data[i]._id.$id;
							$cbo.append('<option value="'+id+'" >'+rubro+'</option>');
						}	
						$mainPanel.find('[name=ano]').change();
					}else{
						
					}			
				},'json');	
				$mainPanel.find('[name=btnGenerar]').click(function(){
					if($mainPanel.find('[name=cred] :selected').val()!=null){
						prPresModiCred.loadData();
					}else{
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe seleccionar un credito suplementario!',type: 'error'});
					}
				}).button({icons: {primary: 'ui-icon-search'}});
				/*$mainPanel.find('[name=btnExportar]').click(function(){
					window.open('pr/cred/print?periodo='+$mainPanel.find('[name=ano]').val()+'&num_credito='+$mainPanel.find('[name=cred] :selected').val()+'&fuente='+$mainPanel.find('[name=fuente] :selected').val());
				}).button({icons: {primary: 'ui-icon-print'}});*/
				$mainPanel.find('[name=btnImprimir1]').click(function(){
					var params = {
						tipo: "I",
						periodo: $mainPanel.find('[name=ano]').val(),
						num_credito: $mainPanel.find('[name=cred] :selected').val(),
					}, url = 'pr/cred/print?'+$.param(params);
					K.windowPrint({
						id:'windowPrCredAPrint',
						title: "Credito suplementario (ANEXO A)",
						url: url
					});
				}).button({icons: {primary: 'ui-icon-print'}});
				$mainPanel.find('[name=btnImprimir2]').click(function(){
					var params = {
						tipo: "G",
						periodo: $mainPanel.find('[name=ano]').val(),
						num_credito: $mainPanel.find('[name=cred] :selected').val(),
					}, url = 'pr/cred/print?'+$.param(params);
					K.windowPrint({
						id:'windowPrCredAPrint',
						title: "Credito suplementario (ANEXO B)",
						url: url
					});
				}).button({icons: {primary: 'ui-icon-print'}});
			}
		});
		$('#pageWrapperMain').layout();
	},
	loadData: function(params){
		K.block({$element: $('#mainPanel'),message:'Buscando Credito Suplementario, espere por favor'});
		$('#mainPanel .gridBody').empty();
		params = new Object;
		$mainPanel.find('[name=gridBody]').empty();
		params.periodo = $mainPanel.find('[name=ano]').val();
		params.num_credito = $mainPanel.find('[name=cred] :selected').val();
	    $.sum = function(arr){
	        var r = 0;
	        $.each(arr,function(i,v){
	            r += parseFloat(v);
	        });
	        return r;
	    };
	    $.post('pr/cred/lista', params, function(data){
	    	if ( data.items!=null){
				for (i=0; i < data.items.length; i++) {
					result = data.items[i];
					var $row = $('.gridReference2','#mainPanel').clone();
					$li = $('li',$row);
					$li.eq(0).html(result.cod);
					$li.eq(1).html(result.nomb);
					$li.eq(2).html(K.round($.sum(result.importes),2)).css({'text-align':'right'});
					$row.wrapInner('<a class="item" href="javascript: void(0);" />');
		        	$("#mainPanel .gridBody").append( $row.children() );
					ciHelper.gridButtons($("#mainPanel .gridBody"));
				}		     
	    	}else{	      
	    		$mainPanel.find('[name=btnAgregarAct]').hide();
	      	  	$mainPanel.find('[name=btnImprimir]').hide();
	    	}
	      $('#mainPanel').resize();
	      K.unblock({$element: $('#mainPanel')});
	    }, 'json');
	}
};