(function( $ ){
	jQuery.fn.email=function(){
		if(/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/.test($(this).val())){
			return true;
		}else{
			$(this).focus();
			return false;
		}
	};
	jQuery.fn.url=function(){
		var regex=/^(ht|f)tps?:\/\/\w+([\.\-\w]+)?\.([a-z]{2,4}|travel)(:\d{2,5})?(\/.*)?$/i;
		if(regex.test($(this).val())){
			return true;
		}else{
			$(this).focus();
			return false;
		}
	};
})( jQuery );