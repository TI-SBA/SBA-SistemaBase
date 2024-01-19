/*******************************************************************************
Cuadro Resumen del compromiso y ejecución */
ctEpresCuadce = {
	states : {
		"A":{descr:"Aperturado"},
		"C":{descr:"Cerrado"}
	},
	calcSum : function(){
		var tot_ad = 0;
		var tot_al = 0;
		var tot_ga = 0;
		var tot_41 = 0;
		var tot_42 = 0;
		var tot_43 = 0;
		var tot_44 = 0;
		var tot_45 = 0;
		var tot_46 = 0;
		var tot_47 = 0;
		var tot_50 = 0;
		var tot_51 = 0;
		var tot_pen = 0;
		var tot_rc = 0;
		var tot_total = 0;
		for(i=0;i<($mainPanel.find('.gridBody .item').length-3);i++){
			/** AD */
			var grid_ad = $mainPanel.find('.gridBody .item').eq(i).find('input').eq(1).val();
			if(grid_ad=="")grid_ad = "0";
			tot_ad = parseFloat(grid_ad) + tot_ad;
			/** AL */
			var grid_al = $mainPanel.find('.gridBody .item').eq(i).find('input').eq(2).val();
			if(grid_al=="")grid_al = "0";
			tot_al = parseFloat(grid_al) + tot_al;
			/** GA */
			var grid_ga = $mainPanel.find('.gridBody .item').eq(i).find('input').eq(3).val();
			if(grid_ga=="")grid_ga = "0";
			tot_ga = parseFloat(grid_ga) + tot_ga;
			/** 41 */
			var grid_41 = $mainPanel.find('.gridBody .item').eq(i).find('input').eq(4).val();
			if(grid_41=="")grid_41 = "0";
			tot_41 = parseFloat(grid_41) + tot_41;
			/** 42 */
			var grid_42 = $mainPanel.find('.gridBody .item').eq(i).find('input').eq(5).val();
			if(grid_42=="")grid_42 = "0";
			tot_42 = parseFloat(grid_42) + tot_42;
			/** 43 */
			var grid_43 = $mainPanel.find('.gridBody .item').eq(i).find('input').eq(6).val();
			if(grid_43=="")grid_43 = "0";
			tot_43 = parseFloat(grid_43) + tot_43;
			/** 44 */
			var grid_44 = $mainPanel.find('.gridBody .item').eq(i).find('input').eq(7).val();
			if(grid_44=="")grid_44 = "0";
			tot_44 = parseFloat(grid_44) + tot_44;
			/** 45 */
			var grid_45 = $mainPanel.find('.gridBody .item').eq(i).find('input').eq(8).val();
			if(grid_45=="")grid_45 = "0";
			tot_45 = parseFloat(grid_45) + tot_45;
			/** 46 */
			var grid_46 = $mainPanel.find('.gridBody .item').eq(i).find('input').eq(9).val();
			if(grid_46=="")grid_46 = "0";
			tot_46 = parseFloat(grid_46) + tot_46;
			/** 47 */
			var grid_47 = $mainPanel.find('.gridBody .item').eq(i).find('input').eq(10).val();
			if(grid_47=="")grid_47 = "0";
			tot_47 = parseFloat(grid_47) + tot_47;
			/** 50 */
			var grid_50 = $mainPanel.find('.gridBody .item').eq(i).find('input').eq(11).val();
			if(grid_50=="")grid_50 = "0";
			tot_50 = parseFloat(grid_50) + tot_50;
			/** 51 */
			var grid_51 = $mainPanel.find('.gridBody .item').eq(i).find('input').eq(12).val();
			if(grid_51=="")grid_51 = "0";
			tot_51 = parseFloat(grid_51) + tot_51;
			/** PEN */
			var grid_pen = $mainPanel.find('.gridBody .item').eq(i).find('input').eq(13).val();
			if(grid_pen=="")grid_pen = "0";
			tot_pen = parseFloat(grid_pen) + tot_pen;
			/** PEN */
			var grid_rc = $mainPanel.find('.gridBody .item').eq(i).find('input').eq(14).val();
			if(grid_rc=="")grid_rc = "0";
			tot_rc = parseFloat(grid_rc) + tot_rc;
			/** Total */
			var grid_total = $mainPanel.find('.gridBody .item').eq(i).find('li').eq(15).html();
			if(grid_total=="")grid_total = "0";
			tot_total = parseFloat(grid_total) + tot_total;
		}
		$mainPanel.find('.gridBody .total').eq(0).find('li').eq(1).html(K.round(tot_ad,2));
		$mainPanel.find('.gridBody .total').eq(0).find('li').eq(2).html(K.round(tot_al,2));
		$mainPanel.find('.gridBody .total').eq(0).find('li').eq(3).html(K.round(tot_ga,2));
		$mainPanel.find('.gridBody .total').eq(0).find('li').eq(4).html(K.round(tot_41,2));
		$mainPanel.find('.gridBody .total').eq(0).find('li').eq(5).html(K.round(tot_42,2));
		$mainPanel.find('.gridBody .total').eq(0).find('li').eq(6).html(K.round(tot_43,2));
		$mainPanel.find('.gridBody .total').eq(0).find('li').eq(7).html(K.round(tot_44,2));
		$mainPanel.find('.gridBody .total').eq(0).find('li').eq(8).html(K.round(tot_45,2));
		$mainPanel.find('.gridBody .total').eq(0).find('li').eq(9).html(K.round(tot_46,2));
		$mainPanel.find('.gridBody .total').eq(0).find('li').eq(10).html(K.round(tot_47,2));
		$mainPanel.find('.gridBody .total').eq(0).find('li').eq(11).html(K.round(tot_50,2));
		$mainPanel.find('.gridBody .total').eq(0).find('li').eq(12).html(K.round(tot_51,2));
		$mainPanel.find('.gridBody .total').eq(0).find('li').eq(13).html(K.round(tot_pen,2));
		$mainPanel.find('.gridBody .total').eq(0).find('li').eq(14).html(K.round(tot_rc,2));
		$mainPanel.find('.gridBody .total').eq(0).find('li').eq(15).html(K.round(tot_total,2));
	},
	sumDT : function(){
		var tot_ad = $mainPanel.find('.gridBody .total').eq(0).find('li').eq(1).html();
		var tot_al = $mainPanel.find('.gridBody .total').eq(0).find('li').eq(2).html();
		var tot_ga = $mainPanel.find('.gridBody .total').eq(0).find('li').eq(3).html();
		var tot_41 = $mainPanel.find('.gridBody .total').eq(0).find('li').eq(4).html();
		var tot_42 = $mainPanel.find('.gridBody .total').eq(0).find('li').eq(5).html();
		var tot_43 = $mainPanel.find('.gridBody .total').eq(0).find('li').eq(6).html();
		var tot_44 = $mainPanel.find('.gridBody .total').eq(0).find('li').eq(7).html();
		var tot_45 = $mainPanel.find('.gridBody .total').eq(0).find('li').eq(8).html();
		var tot_46 = $mainPanel.find('.gridBody .total').eq(0).find('li').eq(9).html();
		var tot_47 = $mainPanel.find('.gridBody .total').eq(0).find('li').eq(10).html();
		var tot_50 = $mainPanel.find('.gridBody .total').eq(0).find('li').eq(11).html();
		var tot_51 = $mainPanel.find('.gridBody .total').eq(0).find('li').eq(12).html();
		var tot_pen = $mainPanel.find('.gridBody .total').eq(0).find('li').eq(13).html();
		var tot_rc = $mainPanel.find('.gridBody .total').eq(0).find('li').eq(14).html();
		var tot_total = $mainPanel.find('.gridBody .total').eq(0).find('li').eq(15).html();
		
		/** AD */
		var grid_ad = $mainPanel.find('.gridBody .donaciones').eq(0).find('input').eq(0).val();
		if(grid_ad=="")grid_ad = "0";
		/** AL */
		var grid_al = $mainPanel.find('.gridBody .donaciones').eq(0).find('input').eq(1).val();
		if(grid_al=="")grid_al = "0";
		/** GA */
		var grid_ga = $mainPanel.find('.gridBody .donaciones').eq(0).find('input').eq(2).val();
		if(grid_ga=="")grid_ga = "0";
		/** 41 */
		var grid_41 = $mainPanel.find('.gridBody .donaciones').eq(0).find('input').eq(3).val();
		if(grid_41=="")grid_41 = "0";
		/** 42 */
		var grid_42 = $mainPanel.find('.gridBody .donaciones').eq(0).find('input').eq(4).val();
		if(grid_42=="")grid_42 = "0";
		/** 43 */
		var grid_43 = $mainPanel.find('.gridBody .donaciones').eq(0).find('input').eq(5).val();
		if(grid_43=="")grid_43 = "0";
		/** 44 */
		var grid_44 = $mainPanel.find('.gridBody .donaciones').eq(0).find('input').eq(6).val();
		if(grid_44=="")grid_44 = "0";
		/** 45 */
		var grid_45 = $mainPanel.find('.gridBody .donaciones').eq(0).find('input').eq(7).val();
		if(grid_45=="")grid_45 = "0";
		/** 46 */
		var grid_46 = $mainPanel.find('.gridBody .donaciones').eq(0).find('input').eq(8).val();
		if(grid_46=="")grid_46 = "0";
		/** 47 */
		var grid_47 = $mainPanel.find('.gridBody .donaciones').eq(0).find('input').eq(9).val();
		if(grid_47=="")grid_47 = "0";
		/** 50 */
		var grid_50 = $mainPanel.find('.gridBody .donaciones').eq(0).find('input').eq(10).val();
		if(grid_50=="")grid_50 = "0";
		/** 51 */
		var grid_51 = $mainPanel.find('.gridBody .donaciones').eq(0).find('input').eq(11).val();
		if(grid_51=="")grid_51 = "0";
		/** PEN */
		var grid_pen = $mainPanel.find('.gridBody .donaciones').eq(0).find('input').eq(12).val();
		if(grid_pen=="")grid_pen = "0";
		/** PEN */
		var grid_rc = $mainPanel.find('.gridBody .donaciones').eq(0).find('input').eq(13).val();
		if(grid_rc=="")grid_rc = "0";
		/** Total */
		var grid_total = $mainPanel.find('.gridBody .donaciones').eq(0).find('li').eq(15).html();
		if(grid_total=="")grid_total = "0";
		$mainPanel.find('.gridBody .rdr').eq(0).find('li').eq(1).html(K.round(parseFloat(tot_ad)-parseFloat(grid_ad),2));
		$mainPanel.find('.gridBody .rdr').eq(0).find('li').eq(2).html(K.round(parseFloat(tot_al)-parseFloat(grid_al),2));
		$mainPanel.find('.gridBody .rdr').eq(0).find('li').eq(3).html(K.round(parseFloat(tot_ga)-parseFloat(grid_ga),2));
		$mainPanel.find('.gridBody .rdr').eq(0).find('li').eq(4).html(K.round(parseFloat(tot_41)-parseFloat(grid_41),2));
		$mainPanel.find('.gridBody .rdr').eq(0).find('li').eq(5).html(K.round(parseFloat(tot_42)-parseFloat(grid_42),2));
		$mainPanel.find('.gridBody .rdr').eq(0).find('li').eq(6).html(K.round(parseFloat(tot_43)-parseFloat(grid_43),2));
		$mainPanel.find('.gridBody .rdr').eq(0).find('li').eq(7).html(K.round(parseFloat(tot_44)-parseFloat(grid_44),2));
		$mainPanel.find('.gridBody .rdr').eq(0).find('li').eq(8).html(K.round(parseFloat(tot_45)-parseFloat(grid_45),2));
		$mainPanel.find('.gridBody .rdr').eq(0).find('li').eq(9).html(K.round(parseFloat(tot_46)-parseFloat(grid_46),2));
		$mainPanel.find('.gridBody .rdr').eq(0).find('li').eq(10).html(K.round(parseFloat(tot_47)-parseFloat(grid_47),2));
		$mainPanel.find('.gridBody .rdr').eq(0).find('li').eq(11).html(K.round(parseFloat(tot_50)-parseFloat(grid_50),2));
		$mainPanel.find('.gridBody .rdr').eq(0).find('li').eq(12).html(K.round(parseFloat(tot_51)-parseFloat(grid_51),2));
		$mainPanel.find('.gridBody .rdr').eq(0).find('li').eq(13).html(K.round(parseFloat(tot_pen)-parseFloat(grid_pen),2));
		$mainPanel.find('.gridBody .rdr').eq(0).find('li').eq(14).html(K.round(parseFloat(tot_rc)-parseFloat(grid_rc),2));
		$mainPanel.find('.gridBody .rdr').eq(0).find('li').eq(15).html(K.round(parseFloat(tot_total)-parseFloat(grid_total),2));
	},
	init: function(){
		if($('#pageWrapper [child=epres]').length<=0){
			$.post('ct/navg/epres',function(data){
				var $p = $('#pageWrapperLeft');
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
				$p.find('[name=ctEpresAuxI]').click(function(){ ctEpresAuxI.init(); });
				$p.find('[name=ctEpresAuxG]').click(function(){ ctEpresAuxG.init(); });
				$p.find('[name=ctEpresCuadce]').click(function(){ ctEpresCuadce.init(); }).addClass('ui-state-highlight');
				$p.find('[name=ctEpresPpres]').click(function(){ ctEpresPpres.init(); });
				$p.find('[name=ctEpresMovi]').click(function(){ ctEpresMovi.init(); });
			},'json');
		}
		K.initMode({
			mode: 'ct',
			action: 'ctEpresCuadce',
			titleBar: {
				title: 'Cuadro Resumen del Compromiso y Ejecuci&oacute;n'
			}
		});
		new K.Panel({
			id: 'mainPanel',
			contentURL: 'ct/comej',
			onContentLoaded: function(){
				$('#No-Results').hide();
				$mainPanel = $('#mainPanel');
				$mainPanel.resize(function(){
					$mainPanel.find('.grid:eq(1)').height(($mainPanel.height()-$mainPanel.find('.grid:eq(0)').height()-$('.div-bottom').outerHeight()-$('.div-bottom').height()-$mainPanel.find('table').height())+'px');
				}).resize();
				$mainPanel.find('.grid:eq(0)').css('overflow','hidden');
				$mainPanel.find('.grid:eq(1)').scroll(function(){
					$mainPanel.find('.grid:eq(0)').scrollLeft($(this).scrollLeft());
				});
				$mainPanel.find('[name=ano]').numeric().spinner({step: 1,min: 1900,max: 2100}).change(function(){
					$('#mainPanel .gridBody').empty();
			    });			
				$mainPanel.find('[name=ano]').parent().find('.ui-button').css('height','14px');
				var d = new Date();
				$mainPanel.find('[name=ano]').val(d.getFullYear()); 
				$mainPanel.find('.ui-spinner-button').click(function() { 
					$('#mainPanel .gridBody').empty();
					ctEpresCuadce.loadData({url: 'ct/comej/lista'});
				});
				$mainPanel.find('[name=ano]').keyup(function(){
					$('#mainPanel .gridBody').empty();
					ctEpresCuadce.loadData({url: 'ct/comej/lista'});
				});
				$mainPanel.find('[name=mes]').change(function(){
					$('#mainPanel .gridBody').empty();
					ctEpresCuadce.loadData({url: 'ct/comej/lista'});
			    });
				$mainPanel.find('[name=tipo]').change(function(){
					$('#mainPanel .gridBody').empty();
					ctEpresCuadce.loadData({url: 'ct/comej/lista'});
			    });
				$mainPanel.find('[name=btnGuardar]').click(function(){
					var data = new Object;
					data.periodo = new Object;
					data.periodo.ano = $mainPanel.find('[name=ano]').val();
					data.periodo.mes = $mainPanel.find('[name=mes] :selected').val();
					data.autor = ciHelper.enti.dbTrabRel(K.session.enti);
					data.tipo = $mainPanel.find('[name=tipo] :selected').val();
					data.cols = new Array;
					var cols_ad = {};
					cols_ad.columna = "AD";
					cols_ad.donacion = $mainPanel.find('.gridBody .donaciones').eq(0).find('input').eq(0).val();
					cols_ad.items = new Array;
					if(cols_ad.donacion=="")cols_ad.donacion = "0";
					var cols_al = {};
					cols_al.columna = "AL";
					cols_al.donacion = $mainPanel.find('.gridBody .donaciones').eq(0).find('input').eq(1).val();
					cols_al.items = new Array;
					if(cols_al.donacion=="")cols_al.donacion = "0";
					var cols_ga = {};
					cols_ga.columna = "GA";
					cols_ga.donacion = $mainPanel.find('.gridBody .donaciones').eq(0).find('input').eq(2).val();
					cols_ga.items = new Array;
					if(cols_ga.donacion=="")cols_ga.donacion = "0";
					var cols_41 = {};
					cols_41.columna = "41";
					cols_41.donacion = $mainPanel.find('.gridBody .donaciones').eq(0).find('input').eq(3).val();
					cols_41.items = new Array;
					if(cols_41.donacion=="")cols_41.donacion = "0";
					var cols_42 = {};
					cols_42.columna = "42";
					cols_42.donacion = $mainPanel.find('.gridBody .donaciones').eq(0).find('input').eq(4).val();
					cols_42.items = new Array;
					if(cols_42.donacion=="")cols_42.donacion = "0";
					var cols_43 = {};
					cols_43.columna = "43";
					cols_43.donacion = $mainPanel.find('.gridBody .donaciones').eq(0).find('input').eq(5).val();
					cols_43.items = new Array;
					if(cols_43.donacion=="")cols_43.donacion = "0";
					var cols_44 = {};
					cols_44.columna = "44";
					cols_44.donacion = $mainPanel.find('.gridBody .donaciones').eq(0).find('input').eq(6).val();
					cols_44.items = new Array;
					if(cols_44.donacion=="")cols_44.donacion = "0";
					var cols_45 = {};
					cols_45.columna = "45";
					cols_45.donacion = $mainPanel.find('.gridBody .donaciones').eq(0).find('input').eq(7).val();
					cols_45.items = new Array;
					if(cols_45.donacion=="")cols_45.donacion = "0";
					var cols_46 = {};
					cols_46.columna = "46";
					cols_46.donacion = $mainPanel.find('.gridBody .donaciones').eq(0).find('input').eq(8).val();
					cols_46.items = new Array;
					if(cols_46.donacion=="")cols_46.donacion = "0";
					var cols_47 = {};
					cols_47.columna = "47";
					cols_47.donacion = $mainPanel.find('.gridBody .donaciones').eq(0).find('input').eq(9).val();
					cols_47.items = new Array;
					if(cols_47.donacion=="")cols_47.donacion = "0";
					var cols_50 = {};
					cols_50.columna = "50";
					cols_50.donacion = $mainPanel.find('.gridBody .donaciones').eq(0).find('input').eq(10).val();
					cols_50.items = new Array;
					if(cols_50.donacion=="")cols_50.donacion = "0";
					var cols_51 = {};
					cols_51.columna = "51";
					cols_51.donacion = $mainPanel.find('.gridBody .donaciones').eq(0).find('input').eq(11).val();
					cols_51.items = new Array;
					if(cols_51.donacion=="")cols_51.donacion = "0";
					var cols_pen = {};
					cols_pen.columna = "PEN";
					cols_pen.donacion = $mainPanel.find('.gridBody .donaciones').eq(0).find('input').eq(12).val();
					cols_pen.items = new Array;
					if(cols_pen.donacion=="")cols_pen.donacion = "0";
					var cols_rc = {};
					cols_rc.columna = "RC";
					cols_rc.donacion = $mainPanel.find('.gridBody .donaciones').eq(0).find('input').eq(13).val();
					cols_rc.items = new Array;
					if(cols_rc.donacion=="")cols_rc.donacion = "0";
					for(i=0;i<($mainPanel.find('.gridBody .item').length-3);i++){
						if($mainPanel.find('.gridBody .item').eq(i).find('input').eq(0).val()!=""){
							var item_ad = {};
							item_ad.denominacion = $mainPanel.find('.gridBody .item').eq(i).find('input').eq(0).val();
							item_ad.monto = $mainPanel.find('.gridBody .item').eq(i).find('input').eq(1).val();
							if(item_ad.monto=="")item_ad.monto="0";
							cols_ad.items.push(item_ad);
							var item_al = {};
							item_al.denominacion = $mainPanel.find('.gridBody .item').eq(i).find('input').eq(0).val();
							item_al.monto = $mainPanel.find('.gridBody .item').eq(i).find('input').eq(2).val();
							if(item_al.monto=="")item_al.monto="0";
							cols_al.items.push(item_al);
							var item_ga = {};
							item_ga.denominacion = $mainPanel.find('.gridBody .item').eq(i).find('input').eq(0).val();
							item_ga.monto = $mainPanel.find('.gridBody .item').eq(i).find('input').eq(3).val();
							if(item_ga.monto=="")item_ga.monto="0";
							cols_ga.items.push(item_ga);
							var item_41 = {};
							item_41.denominacion = $mainPanel.find('.gridBody .item').eq(i).find('input').eq(0).val();
							item_41.monto = $mainPanel.find('.gridBody .item').eq(i).find('input').eq(4).val();
							if(item_41.monto=="")item_41.monto="0";
							cols_41.items.push(item_41);
							var item_42 = {};
							item_42.denominacion = $mainPanel.find('.gridBody .item').eq(i).find('input').eq(0).val();
							item_42.monto = $mainPanel.find('.gridBody .item').eq(i).find('input').eq(5).val();
							if(item_42.monto=="")item_42.monto="0";
							cols_42.items.push(item_42);
							var item_43 = {};
							item_43.denominacion = $mainPanel.find('.gridBody .item').eq(i).find('input').eq(0).val();
							item_43.monto = $mainPanel.find('.gridBody .item').eq(i).find('input').eq(6).val();
							if(item_43.monto=="")item_43.monto="0";
							cols_43.items.push(item_43);
							var item_44 = {};
							item_44.denominacion = $mainPanel.find('.gridBody .item').eq(i).find('input').eq(0).val();
							item_44.monto = $mainPanel.find('.gridBody .item').eq(i).find('input').eq(7).val();
							if(item_44.monto=="")item_44.monto="0";
							cols_44.items.push(item_44);
							var item_45 = {};
							item_45.denominacion = $mainPanel.find('.gridBody .item').eq(i).find('input').eq(0).val();
							item_45.monto = $mainPanel.find('.gridBody .item').eq(i).find('input').eq(8).val();
							if(item_45.monto=="")item_45.monto="0";
							cols_45.items.push(item_45);
							var item_46 = {};
							item_46.denominacion = $mainPanel.find('.gridBody .item').eq(i).find('input').eq(0).val();
							item_46.monto = $mainPanel.find('.gridBody .item').eq(i).find('input').eq(9).val();
							if(item_46.monto=="")item_46.monto="0";
							cols_46.items.push(item_46);
							var item_47 = {};
							item_47.denominacion = $mainPanel.find('.gridBody .item').eq(i).find('input').eq(0).val();
							item_47.monto = $mainPanel.find('.gridBody .item').eq(i).find('input').eq(10).val();
							if(item_47.monto=="")item_47.monto="0";
							cols_47.items.push(item_47);
							var item_50 = {};
							item_50.denominacion = $mainPanel.find('.gridBody .item').eq(i).find('input').eq(0).val();
							item_50.monto = $mainPanel.find('.gridBody .item').eq(i).find('input').eq(11).val();
							if(item_50.monto=="")item_50.monto="0";
							cols_50.items.push(item_50);
							var item_51 = {};
							item_51.denominacion = $mainPanel.find('.gridBody .item').eq(i).find('input').eq(0).val();
							item_51.monto = $mainPanel.find('.gridBody .item').eq(i).find('input').eq(12).val();
							if(item_51.monto=="")item_51.monto="0";
							cols_51.items.push(item_51);
							var item_pen = {};
							item_pen.denominacion = $mainPanel.find('.gridBody .item').eq(i).find('input').eq(0).val();
							item_pen.monto = $mainPanel.find('.gridBody .item').eq(i).find('input').eq(13).val();
							if(item_pen.monto=="")item_pen.monto="0";
							cols_pen.items.push(item_pen);
							var item_rc = {};
							item_rc.denominacion = $mainPanel.find('.gridBody .item').eq(i).find('input').eq(0).val();
							item_rc.monto = $mainPanel.find('.gridBody .item').eq(i).find('input').eq(14).val();
							if(item_rc.monto=="")item_rc.monto="0";
							cols_rc.items.push(item_rc);
						}else{
							return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar una denominaci&oacute;n por cada fila!',type: 'error'});
						}
					}
					data.cols.push(cols_ad);
					data.cols.push(cols_al);
					data.cols.push(cols_ga);
					data.cols.push(cols_41);
					data.cols.push(cols_42);
					data.cols.push(cols_43);
					data.cols.push(cols_44);
					data.cols.push(cols_45);
					data.cols.push(cols_46);
					data.cols.push(cols_47);
					data.cols.push(cols_50);
					data.cols.push(cols_51);
					data.cols.push(cols_pen);
					data.cols.push(cols_rc);
					$.post('ct/comej/save',data,function(){
						K.notification({title: ciHelper.titleMessages.infoReq,text: 'los Registros para este periodo fuer&oacute;n guardados correctamente!'});
						$('#pageWrapperLeft .ui-state-highlight').click();
					});
				}).button({icons: {primary: 'ui-icon-plusthick'}});	
				$mainPanel.find('[name=btnCerrarperiodo]').click(function(){
					ciHelper.confirm(
						'Esta seguro(a) de cerrar este periodo?',
						function () {
							var data = {
								mes:$mainPanel.find('[name=mes] :selected').val(),
								ano:$mainPanel.find('[name=ano]').val(),
								tipo:$mainPanel.find('[name=tipo] :selected').val()
							};
							$.post('ct/comej/cerrar',data,function(){
								K.notification({title: ciHelper.titleMessages.regiGua,text: 'El periodo fue cerrado con &eacute;xito!'});
								$mainPanel.find('[name=btnCerrarperiodo]').button( "option", "disabled", true );
								$('#pageWrapperLeft .ui-state-highlight').click();
							});	
						},
						function () {
							//nothing
						}										
					);			
				}).button({icons: {primary: 'ui-icon-check'}});
				$mainPanel.find('[name=btnAdd]').live('click',function(){
					var $row4 = $mainPanel.find('.gridReference').clone();
					$li4 = $('li',$row4);
					$row4.find('[name=btnEli]').button({icons: {primary: 'ui-icon-trash'},text: false});
					$row4.find('#item').numeric();
					$row4.wrapInner('<a class="item" />');
					$mainPanel.find(".gridBody .total").before( $row4.children() );
					$mainPanel.find('.gridBody [name=btnAdd]').remove();
					$mainPanel.find('.gridBody .item').eq($mainPanel.find('.gridBody .item').length-4).find('li:last').append('<button name="btnAdd">Agregar</button>');
					$mainPanel.find('.gridBody [name=btnAdd]').button({icons: {primary: 'ui-icon-plusthick'},text: false});
				}).button({icons: {primary: 'ui-icon-plusthick'}});
				$mainPanel.find('[name=btnEli]').live('click',function(){
					if($mainPanel.find('.gridBody .item').length>4){
						$(this).closest('.item').remove();			
						$mainPanel.find('.gridBody [name=btnAdd]').remove();
						$mainPanel.find('.gridBody .item').eq($mainPanel.find('.gridBody .item').length-4).find('li:last').append('<button name="btnAdd">Agregar</button>');
						$mainPanel.find('.gridBody [name=btnAdd]').button({icons: {primary: 'ui-icon-plusthick'},text: false});
						ctEpresCuadce.calcSum();
						ctEpresCuadce.sumDT();
					}
				});
				$mainPanel.find('.gridBody #item,.gridBody #item_dt').live('keyup',function(){
					var $this = $(this);
					var sum = 0;
					for(i=0;i<$this.closest('.item').find('#item,#item_dt').length;i++){
						var get_val = $this.closest('.item').find('#item,#item_dt').eq(i).val();
						if(get_val=="")get_val = "0";
						sum = parseFloat(get_val) + sum;						
					}
					$this.closest('.item').find('li').eq(15).html(K.round(sum,2));
					ctEpresCuadce.calcSum();
					ctEpresCuadce.sumDT();
				});
				$mainPanel.find('[name=btnGuardar]').button( "option", "disabled", true );
				$mainPanel.find('[name=btnCerrarperiodo]').button( "option", "disabled", true );			
				ctEpresCuadce.loadData({url: 'ct/comej/lista'});
			}
		});
		$('#pageWrapperMain').layout();
	},
	loadData: function(params){
		params.ano = $mainPanel.find('[name=ano]').val();
		params.mes = $mainPanel.find('[name=mes] :selected').val();
		params.tipo = $mainPanel.find('[name=tipo] :selected').val();
		/* Total */
		var $row1 = $('.gridReference','#mainPanel').clone();
		$li1 = $('li',$row1);
		$li1.eq(0).html("Total").addClass('ui-state-default ui-button-text-only');
		$li1.eq(1).html("");
		$li1.eq(2).html("");
		$li1.eq(3).html("");
		$li1.eq(4).html("");
		$li1.eq(5).html("");
		$li1.eq(6).html("");
		$li1.eq(7).html("");
		$li1.eq(8).html("");
		$li1.eq(9).html("");
		$li1.eq(10).html("");
		$li1.eq(11).html("");
		$li1.eq(12).html("");
		$li1.eq(13).html("");
		$li1.eq(14).html("");
		$li1.eq(15).html("");
		$li1.eq(16).html("");
		$row1.wrapInner('<a class="item total" href="javascript: void(0);" />');
		$mainPanel.find(".gridBody").append( $row1.children() );	
		/* /Total */
		/* DT */
		var $row1 = $('.gridReference','#mainPanel').clone();
		$li1 = $('li',$row1);		
		$li1.eq(0).html("DT").addClass('ui-state-default ui-button-text-only');
		$li1.eq(1).find('input').attr('id','item_dt');
		$li1.eq(2).find('input').attr('id','item_dt');
		$li1.eq(3).find('input').attr('id','item_dt');
		$li1.eq(4).find('input').attr('id','item_dt');
		$li1.eq(5).find('input').attr('id','item_dt');
		$li1.eq(6).find('input').attr('id','item_dt');
		$li1.eq(7).find('input').attr('id','item_dt');
		$li1.eq(8).find('input').attr('id','item_dt');
		$li1.eq(9).find('input').attr('id','item_dt');
		$li1.eq(10).find('input').attr('id','item_dt');
		$li1.eq(11).find('input').attr('id','item_dt');
		$li1.eq(12).find('input').attr('id','item_dt');
		$li1.eq(13).find('input').attr('id','item_dt');
		$li1.eq(14).find('input').attr('id','item_dt');
		$li1.eq(16).html("");
		$row1.find('#item_dt').numeric();
		$row1.wrapInner('<a class="item donaciones" href="javascript: void(0);" />');
		$mainPanel.find(".gridBody").append( $row1.children() );	
		/* /DT */
		/* RDR */
		var $row1 = $('.gridReference','#mainPanel').clone();
		$li1 = $('li',$row1);
		$li1.eq(0).html("RDR").addClass('ui-state-default ui-button-text-only');
		$li1.eq(1).html("");
		$li1.eq(2).html("");
		$li1.eq(3).html("");
		$li1.eq(4).html("");
		$li1.eq(5).html("");
		$li1.eq(6).html("");
		$li1.eq(7).html("");
		$li1.eq(8).html("");
		$li1.eq(9).html("");
		$li1.eq(10).html("");
		$li1.eq(11).html("");
		$li1.eq(12).html("");
		$li1.eq(13).html("");
		$li1.eq(14).html("");
		$li1.eq(15).html("");
		$li1.eq(16).html("");
		$row1.wrapInner('<a class="item rdr" href="javascript: void(0);" />');
		$mainPanel.find(".gridBody").append( $row1.children() );	
		/* /RDR */
	    $.post(params.url, params, function(data){
			if ( data.items!=null ) {					
				for (i=0; i < data.items[0].items.length; i++) {					
					var $row = $('.gridReference','#mainPanel').clone();
					$li = $('li',$row);		
					for(j=0;j<data.items.length;j++){					
						result = data.items[j];
						if(result.columna=="AD"){
							$li.eq(1).find('input').val(result.items[i].monto);
							$mainPanel.find('.gridBody .donaciones').eq(0).find('input').eq(0).val(result.donacion);
						}else if(result.columna=="AL"){
							$li.eq(2).find('input').val(result.items[i].monto);
							$mainPanel.find('.gridBody .donaciones').eq(0).find('input').eq(1).val(result.donacion);
						}else if(result.columna=="GA"){
							$li.eq(3).find('input').val(result.items[i].monto);
							$mainPanel.find('.gridBody .donaciones').eq(0).find('input').eq(2).val(result.donacion);
						}else if(result.columna=="41"){
							$li.eq(4).find('input').val(result.items[i].monto);
							$mainPanel.find('.gridBody .donaciones').eq(0).find('input').eq(3).val(result.donacion);
						}else if(result.columna=="42"){
							$li.eq(5).find('input').val(result.items[i].monto);
							$mainPanel.find('.gridBody .donaciones').eq(0).find('input').eq(4).val(result.donacion);
						}else if(result.columna=="43"){
							$li.eq(6).find('input').val(result.items[i].monto);
							$mainPanel.find('.gridBody .donaciones').eq(0).find('input').eq(5).val(result.donacion);
						}else if(result.columna=="44"){
							$li.eq(7).find('input').val(result.items[i].monto);
							$mainPanel.find('.gridBody .donaciones').eq(0).find('input').eq(6).val(result.donacion);
						}else if(result.columna=="45"){
							$li.eq(8).find('input').val(result.items[i].monto);
							$mainPanel.find('.gridBody .donaciones').eq(0).find('input').eq(7).val(result.donacion);
						}else if(result.columna=="46"){
							$li.eq(9).find('input').val(result.items[i].monto);
							$mainPanel.find('.gridBody .donaciones').eq(0).find('input').eq(8).val(result.donacion);
						}else if(result.columna=="47"){
							$li.eq(10).find('input').val(result.items[i].monto);
							$mainPanel.find('.gridBody .donaciones').eq(0).find('input').eq(9).val(result.donacion);
						}else if(result.columna=="50"){
							$li.eq(11).find('input').val(result.items[i].monto);
							$mainPanel.find('.gridBody .donaciones').eq(0).find('input').eq(10).val(result.donacion);
						}else if(result.columna=="51"){
							$li.eq(12).find('input').val(result.items[i].monto);
							$mainPanel.find('.gridBody .donaciones').eq(0).find('input').eq(11).val(result.donacion);
						}else if(result.columna=="PEN"){
							$li.eq(13).find('input').val(result.items[i].monto);
							$mainPanel.find('.gridBody .donaciones').eq(0).find('input').eq(12).val(result.donacion);
						}else if(result.columna=="RC"){
							$li.eq(14).find('input').val(result.items[i].monto);
							$mainPanel.find('.gridBody .donaciones').eq(0).find('input').eq(13).val(result.donacion);
						}
					}
					$row.find('#item').numeric();
					$row.find('input').eq(0).val(data.items[0].items[i].denominacion);
					$row.find('[name=btnEli]').button({icons: {primary: 'ui-icon-trash'},text: false});
					$row.wrapInner('<a class="item" href="javascript: void(0);" />');
					$row.find('a').data('id',result._id.$id).data('estado',result.estado)
					.contextMenu("conMenList", {
							onShowMenu: function(e, menu) {
							    var excep = '';	
								$(e.target).closest('.gridBody').find('ul').removeClass('ui-state-highlight');
								$(e.target).closest('.item').find('ul').addClass('ui-state-highlight');
								$(e.target).closest('.item').click();
								K.tmp = $(e.target).closest('.item');
								if(K.tmp.data('estado')=="C") excep+='#conMenList_edi';
								$(excep+',#conMenList_about,#conMenList_imp,#conMenList_eli',menu).remove();
								return menu;
							},
							bindings: {
								'conMenList_edi': function(t) {
									ctEpresAuxI.windowEdit({id: K.tmp.data('id')});
								}
							}
						});
		        	$("#mainPanel .gridBody .total").before( $row.children() );
		        	$mainPanel.find('.gridBody [name=btnAdd]').remove();
					$mainPanel.find('.gridBody .item').eq($mainPanel.find('.gridBody .item').length-4).find('li:last').append('<button name="btnAdd">Agregar</button>');
					$mainPanel.find('.gridBody [name=btnAdd]').button({icons: {primary: 'ui-icon-plusthick'},text: false});
					if(data.items[0].estado=="A"){
						$mainPanel.find('[name=btnGuardar]').button( "option", "disabled", false );
						$mainPanel.find('[name=btnCerrarperiodo]').button( "option", "disabled", false );
					}else if(data.items[0].estado=="C"){
						$mainPanel.find('[name=btnGuardar]').button( "option", "disabled", true );
						$mainPanel.find('[name=btnCerrarperiodo]').button( "option", "disabled", true );
						$mainPanel.find('.gridBody [name=btnAdd]').remove();
						$mainPanel.find('.gridBody [name=btnEli]').remove();
					}
					$mainPanel.find('[name=estado]').html(ctEpresCuadce.states[data.items[0].estado].descr);
					ciHelper.gridButtons($("#mainPanel .gridBody"));
		        }						
				for(k=0;k<$mainPanel.find('.item').length;k++){
					$mainPanel.find('.item').eq(k).find('input').eq(1).keyup();
				}
		        count = $("#mainPanel .gridBody .item").length;
		        $('#No-Results').hide();
		        $('#Results').hide();
	      } else { 	  
	    	  var $row2 = $('.gridReference','#mainPanel').clone();
	    	  $li2 = $('li',$row2);
	    	  $row2.find('#item').numeric();
			  $row2.wrapInner('<a class="item" href="javascript: void(0);" />');
			  $row2.find('[name=btnAdd]').button({icons: {primary: 'ui-icon-plusthick'},text: false});
			  $row2.find('[name=btnEli]').button({icons: {primary: 'ui-icon-trash'},text: false});
			  $mainPanel.find(".gridBody .total").before( $row2.children() );	
			  $mainPanel.find('[name=btnGuardar]').button( "option", "disabled", false );
			  $mainPanel.find('[name=btnCerrarperiodo]').button( "option", "disabled", true );
			  $mainPanel.find('[name=estado]').html("No Aperturado");
	      }
	      $('#mainPanel').resize();
	      K.unblock({$element: $('#pageWrapperMain')});
	    }, 'json');
	}
};