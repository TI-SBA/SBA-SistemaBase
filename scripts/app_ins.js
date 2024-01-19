requirejs.config({
    //By default load any module IDs from js/lib
    baseUrl: '/sba/scripts',
    urlArgs: 'bust=v'+(new Date()).getTime()
    //urlArgs: 'bust=v1'
    //except, if the module ID starts with "app",
    //load it from the js/app directory. paths
    //config is relative to the baseUrl, and
    //never includes a ".js" extension since
    //the paths config could be for a directory.
    /*paths: {
        app: '../app'
    }*/
});
requirejs.onError = function (err) {
    if (err.requireType=='scripterror') {
        alert('Su conexion a Internet es deficiente, se refrescara el navegador para intentar cargar todas las librerias!');
        $.cookie('action',null);
        location.reload();
    } else {
        throw err;
    }
};
// Start the main app logic.
requirejs(['ci/helper_ins','mg/enti','mg/titu','cj/ecom','navg'],
function (ciHelper,mgEnti,mgTitu,cjEcom,navg) {
    //jQuery, canvas and the app/sub module are all
    //loaded and can be used here now.
    //K.block({text: 'Calculando asistencias. Espere por favor.'});
    //$.post('pe/marc/ultimo_dia',function(){
        K.unblock();
        navg.init();
    //});
    /*$.post('ci/index/all_data',function(data){
        K.session.almacenes = data.almacenes;
        K.session.programas = data.programas;
        K.session.cajas = data.cajas;
        K.session.ctban = data.ctban;
        K.session.variables = data.variables;
        for(var i=0,j=data.variables.length; i<j; i++){
            if(data.variables[i].cod=='IGV')
                K.session.igv = (parseFloat(data.variables[i].valor)/100);
            else if(data.variables[i].cod=='MORA')
                K.session.mora = parseFloat(data.variables[i].valor);
            else if(data.variables[i].cod=='DETRACCION')
                K.session.detraccion = parseFloat(data.variables[i].valor);
        }
    },'json');*/    
});