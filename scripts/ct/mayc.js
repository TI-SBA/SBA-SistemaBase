ctMayc = {
	loadSald: function(cuenta){
		$.post('ct/mayc/mayor',{
			_id: cuenta,
			mes: +$mainPanel.find('[name=periodo]').data('mes')+1,
			ano: $mainPanel.find('[name=periodo]').data('ano')
		},function(data){
			var debe = 0,
			haber = 0,
			$cbo = $mainPanel.find('[name=cuenta_sub]').empty();
			$mainPanel.find('[name=periodo]').data('flag',{
				mes: +$mainPanel.find('[name=periodo]').data('mes')+1,
				ano: $mainPanel.find('[name=periodo]').data('ano')
			});
			$mainPanel.find('.gridBody').empty();
			$mainPanel.find('[name=descr_sub]').html('');
			$mainPanel.find('[name=debe]').html( ciHelper.formatMon(debe) );
			$mainPanel.find('[name=haber]').html( ciHelper.formatMon(haber) );
			if(data==null){
				return K.notification({
					title: 'Saldos no existentes',
					text: 'No hay auxiliares para el periodo y cuenta mayor correspondientes!'
				});
			}
			for(var i=0,j=data.length; i<j; i++){
				if(data[i].estado=='C'){
					$cbo.append('<option value="'+data[i]._id.$id+'" descr="'+data[i].sub_cuenta.descr+'">'+data[i].sub_cuenta.cod+'</option>');
					debe += data[i].debe_final;
					haber += data[i].haber_final;
				}
			}
			$mainPanel.find('[name=debe]').html( ciHelper.formatMon(debe) );
			$mainPanel.find('[name=haber]').html( ciHelper.formatMon(haber) );
			$cbo.change();
		},'json');
	},
	loadData: function(params){
		if($mainPanel.find('[name=cuenta]').data('data')==null){
			$mainPanel.find('[name=btnCta]').click();
			return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe seleccionar una cuenta!',type: 'error'});
		}
		if($mainPanel.find('[name=cuenta_sub] option').length==0){
			$mainPanel.find('[name=btnCta]').click();
			return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe seleccionar una cuenta mayor que tenga auxiliares para el periodo seleccionado!</br>Recuerde que solamente se tomar&aacute;n en cuenta los saldos cerrados.',type: 'error'});
		}
	    params.saldo = $mainPanel.find('[name=cuenta_sub] option:selected').val();
	    $.post(params.url, params, function(data){
			if ( data!=null ) { 
				for (i=0; i < data.length; i++) {
					result = data[i];
					var $row = $('.gridReference','#mainPanel').clone(),
					$li = $('li',$row);
					if(data.tipo=='D'){
						$li.eq(0).html( result.clase+' '+result.num );
						$li.eq(1).html( ciHelper.formatMon(result.monto) );
					}else{
						$li.eq(2).html( ciHelper.formatMon(result.monto) );
						$li.eq(3).html( result.clase+' '+result.num );
					}
					$row.wrapInner('<a class="item" href="javascript: void(0);" />');
		        	$("#mainPanel .gridBody").append( $row.children() );
					ciHelper.gridButtons($("#mainPanel .gridBody"));
		        }
	      }
	      $('#mainPanel').resize();
	      K.unblock({$element: $('#pageWrapperMain')});
	    }, 'json');
	}
};