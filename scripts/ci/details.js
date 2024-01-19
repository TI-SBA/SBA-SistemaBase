ciDetails = {
	windowDetailsEnti: function(p){
		var params = {
			id: 'windowDetailEnti'+p.id,
			contentURL: 'td/gest/details',
			title: p.nomb!=null?'Entidad: '+p.nomb:'',
			maximizable: false,
			resizable: false,
			width: 600,
			height: 300,
			buttons: p.buttons,
			icon: 'ui-icon-person',
			onContentLoaded: function(){
				$modal = $('#windowDetailEnti'+p.id);
				K.block({$element: $modal});
				$.post('mg/enti/get','_id='+p.id,function(data){
					p.data = data;
					if(data.tipo_enti=='P') $modal.find('[name=nomb]').text(data.nomb+' '+data.appat+' '+data.apmat);
					else $modal.find('[name=nomb]').text(data.nomb);
					$modal.find('[name=iden]').text(data.docident[0].tipo);
					$modal.find('[name=numiden]').text(data.docident[0].num);
					if(data.imagen!=null) $modal.find('img').attr('src','ci/files/get?id='+data.imagen.$id).show();
					if(data.domicilios!=null)
					for(var i=0; i<data.domicilios.length; i++){
						$row = $modal.find('[name=local] tr').eq(0).clone();
						$row.find('[name=descr]').text(data.domicilios[i].descr);
						$row.find('[name=direc]').text(data.domicilios[i].direccion);
						$modal.find('[name=local]').append($row);
					}
					if(data.telefonos!=null)
					for(var i=0; i<data.telefonos.length; i++){
						$row = $modal.find('[name=telef] tr').eq(0).clone();
						$row.find('[name=descr]').text(data.telefonos[i].num);
						$row.find('[name=val]').text(data.telefonos[i].val);
						$modal.find('[name=telef]').append($row);
					}
					if(data.emails!=null)
						for(var i=0; i<data.emails.length; i++){
							$row = $modal.find('[name=cint] tr').eq(0).clone();
							$row.find('[name=descr]').text(data.emails[i].descr);
							$row.find('[name=val]').text(data.emails[i].direc);
							$modal.find('[name=cint]').append($row);
						}
					if(data.urls!=null)
						for(var i=0; i<data.urls.length; i++){
							$row = $modal.find('[name=cint] tr').eq(0).clone();
							$row.find('[name=descr]').text(data.urls[i].descr);
							$row.find('[name=val]').text(data.urls[i].direc);
							$modal.find('[name=cint]').append($row);
						}
					K.unblock({$element: $modal});
				},'json');
			}
		};
		if(p.modal!=null){
			params.header = false;
			new K.Modal(params);
		}else new K.Window(params);
	}
};