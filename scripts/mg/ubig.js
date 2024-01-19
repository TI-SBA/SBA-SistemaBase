function Ubig(){
	this.list = [];
	this.loaded_data = false;
	var _this = this;
	$.get('scripts/ubigeo-peru.min.json',function(data){
		_this.list = data;
		_this.loaded_data = true;
	},'json');
}
Ubig.prototype.get_dep = function(callback) {
	var _this = this;
	if(!this.loaded_data){
		setTimeout(function(){
			_this.get_dep(callback);
		}, 1000);
	}else{
		var return_list = [];
		this.list.forEach(function(ubigeo) {
			if (ubigeo.provincia === '00' && ubigeo.distrito === '00') {
				return_list.push(ubigeo);
			}
		});
		callback(return_list)
		return true;
	}
};
Ubig.prototype.get_pro = function(departamento, callback) {
	var _this = this;
	if(!this.loaded_data){
		setTimeout(function(){
			_this.get_pro(departamento,callback);
		}, 1000);
	}else{
		var return_list = [];
		this.list.forEach(function(ubigeo) {
			if (ubigeo.departamento === departamento && ubigeo.provincia !== 0 && ubigeo.distrito === '00') {
				return_list.push(ubigeo);
			}
		});
		callback(return_list);
		return true;
	}
};
Ubig.prototype.get_dis = function(departamento, provincia, callback) {
	var _this = this;
	if(!this.loaded_data){
		setTimeout(function(){
			_this.get_dis(departamento, provincia,callback);
		}, 1000);
	}
	var return_list = [];
	this.list.forEach(function(ubigeo) {
		if (ubigeo.departamento === departamento && ubigeo.provincia === provincia && ubigeo.distrito !== 0) {
			return_list.push(ubigeo);
		}
	});
	callback(return_list)
	return true;
};
Ubig.prototype.get_one = function(departamento, provincia, distrito, callback){
	var _this = this;
	if(!this.loaded_data){
		setTimeout(function(){
			_this.get_one(departamento, provincia, distrito, callback);
		}, 1000);
	}
	var return_list = null;
	this.list.forEach(function(ubigeo) {
		if (ubigeo.departamento === departamento && ubigeo.provincia === provincia && ubigeo.distrito === distrito) {
			return_list = ubigeo;
		}
	});
	callback(return_list);
	return true;
}
define(
	function(){
		return Ubig;
	}
);