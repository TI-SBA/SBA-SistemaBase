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
// Start the main app logic.
requirejs(['ci/details','ci/helper','ci/search','ci/create','ci/edit','ac/noti','mg/enti','mg/orga','mg/titu','mg/serv','ct/pcon','pr/clas','cj/talo','ac/user'],
function(ciDetails,ciHelper,ciSearch,ciCreate,ciEdit,acNoti,mgEnti,mgOrga,mgTitu,mgServ,ctPcon,prClas,cjTalo,acUser){
    //jQuery, canvas and the app/sub module are all
    //loaded and can be used here now.
	switch ($.cookie('mode')) {
		case 'mg': mgNavg(); break;
		case 'td': tdNavg(); break;
		case 'cm': cmNavg(); break;
		case 'in': $("#in").click(); break;
		case 'lg': lgNavg(); break;
		case 'pr': prNavg(); break;
		case 'pe': peNavg(); break;
		case 'al': alNavg(); break;
		case 'cj': cjNavg(); break;
		case 'ct': ctNavg(); break;
		//case 'ac': acNavg(); break;
		case 'ts': tsNavg(); break;
		case 'ho': $("#ho").click(); break;
		case 'ch': $("#ch").click(); break;
		case 'ti': $("#ti").click(); break;
		default: $.noop();
	}
});