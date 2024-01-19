/*******************************************************************************
Nota Modificatoria */
prPresModiNota = {
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
				$p.find('[name=prPresModi_Nota]').click(function(){ prPresModiNota.init(); }).addClass('ui-state-highlight');
				$p.find('[name=prPresModi_Cred]').click(function(){ prPresModiCred.init(); });
			},'json');
		}
		K.initMode({
			mode: 'pr',
			action: 'prPresModi_Nota',
			titleBar: {
				title: 'Notas Modificatorias'
			}
		});
		
		new K.Panel({
			id: 'mainPanel',
			contentURL: 'pr/nota',
			onContentLoaded: function(){				
				$mainPanel = $('#mainPanel');
				$mainPanel.find('[name=ano]').numeric().spinner({step: 1,min: 1900,max: 2100,stop: function(){ $(this).change(); }}).change(function(){
					//loadData
			    });			
				$mainPanel.find('[name=ano]').parent().find('.ui-button').css('height','14px');
				var d = new Date();
				$mainPanel.find('[name=ano]').val(d.getFullYear()); 
				var $cbo_n = $mainPanel.find('[name=nota]');
				$mainPanel.find('[name=ano]').change(function(){
					K.clearNoti();
					K.block({$element: $('#pageWrapperMain')});
					$mainPanel.find('.gridBody').empty();
					$.post('pr/pres/get_num_nota',{periodo:$mainPanel.find('[name=ano]').val()},function(data2){
						$cbo_n.empty();
						if(data2==null){
							K.notification({title: ciHelper.titleMessages.infoReq,text: 'No se encontraron notas modificatorias para este periodo!',type: 'error'});
						}else{
							for(i=0;i<parseFloat(data2[0].num_nota);i++){
								$cbo_n.append('<option value="'+(i+1)+'">'+(i+1)+'</option>');
							}
							console.log(data2[0].num_nota);
						}
						K.unblock({$element: $('#pageWrapperMain')});
					},'json');
				});
				$.post('pr/fuen/all',function(data){
					var $cbo = $mainPanel.find('[name=fuente]');
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
					if($mainPanel.find('[name=nota] :selected').val()!=null){
						prPresModiNota.loadData();
					}else{
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe seleccionar una nota modificatoria!',type: 'error'});
					}
				}).button({icons: {primary: 'ui-icon-search'}});
				$mainPanel.find('[name=btnExportar]').click(function(){
					window.open('pr/nota/print?periodo='+$mainPanel.find('[name=ano]').val()+'&num_nota='+$mainPanel.find('[name=nota] :selected').val());
				}).button({icons: {primary: 'ui-icon-print'}});
			}
		});
		$('#pageWrapperMain').layout();
	},
	loadData: function(params){
		K.block({$element: $('#mainPanel'),message:'Buscando Nota Modificatoria, espere por favor'});
		$('#mainPanel .gridBody').empty();
		params = new Object;
		$mainPanel.find('[name=gridBody]').empty();
		params.periodo = $mainPanel.find('[name=ano]').val();
		params.num_nota = $mainPanel.find('[name=nota] :selected').val();
	    $.sum = function(arr){
	        var r = 0;
	        $.each(arr,function(i,v){
	            r += parseFloat(v);
	        });
	        return r;
	    };
	    $.post('pr/nota/lista', params, function(data){
	    	if ( data.items!=null ) { 
				for (i=0; i < data.items.length; i++) {
					result = data.items[i];
					var $row = $('.gridReference2','#mainPanel').clone();
					$li = $('li',$row);
					$li.eq(0).html(i+1);
					$li.eq(1).html(result.funcion.cod);
					$li.eq(2).html(result.programa.cod);
					$li.eq(3).html(result.subprograma.cod);
					$li.eq(4).html(result.actividad.cod);
					$li.eq(5).html(result.componente.cod);
					$li.eq(6).html(result.meta);
					$li.eq(7).html(result.descr_meta);
					$li.eq(8).html(result.fuente);
					$li.eq(9).html(result.tt);
					$li.eq(10).html(result.gen);
					$li.eq(11).html(result.sg1);
					$li.eq(12).html(result.sg2);
					$li.eq(13).html(result.e1);
					$li.eq(14).html(result.e2);
					$li.eq(15).html(result.hab);
					$li.eq(16).html(result.anu);
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