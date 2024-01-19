mgNavg = function(){
	$.post('mg/navg',function(data){
		if(data!=null){
			var $p = $('#pageWrapperLeft');
			$p.find('label:first').html('Maestros Generales');
			$p.find('.gridBody').empty();
			for(var i=0; i<data.length; i++){
				var result = data[i];
				var $row = $p.find('.gridReference').clone();
				$row.find('li:eq(0)').html( '<span class="ui-icon '+result.icon+'"></span>' + result.descr );
				$row.wrapInner('<a name="'+result.name+'" class="item" href="javascript: void(0);" />');
				$p.find(".gridBody").append( $row.children() );
			}
			
			$p.find('[name=mgTitu]').click(function(){ mgTitu.init(); });
			$p.find('[name=mgTides]').click(function(){ mgTides.init(); });
			$p.find('[name=mgOrga]').click(function(){ mgOrga.init(); });
			$p.find('[name=mgVari]').click(function(){
				$.cookie('action','mgVari');
				window.location.replace('?new=1');
			});
			$p.find('[name=mgServ]').click(function(){
				$.cookie('action','mgServ');
				window.location.replace('?new=1');
			});
			$p.find('[name=mgOfic]').click(function(){
				require(['mg/ofic'],function(mgOfic){
					mgOfic.init();
				});
			});
			$p.find('[name=mgEnti]').click(function(){ mgEnti.init(); });
			$p.resize();
			switch ($.cookie('action')) {
		    	case 'mgTitu': mgTitu.init(); break;
		    	case 'mgTides': mgTides.init(); break;
		    	case 'mgOrga': mgOrga.init(); break;
		    	case 'mgVari': mgVari.init(); break;
		    	case 'mgServ': $p.find('[name=mgServ]').click(); break;
		    	case 'mgOfic': $p.find('[name=mgOfic]').click(); break;
		    	case 'mgEnti': mgEnti.init(); break;
		    	default: mgTitu.init();
		    }
		}else{
			K.notification({text: 'Usted no tiene permisos para este m&oacute;dulo!',type: 'error', layout: 'topLeft'});
		}
	},'json');
};