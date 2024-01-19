<?php
	date_default_timezone_set('America/Lima');
	ini_set("log_errors",1);
	ini_set("error_log", "php-error.log");
	include_once("libraries/framework/core.framework.php");
	
	/* establecer ruta de archivo de configuración */
	$f->traceMode = Application::traceMode_off;
	$f->configFile = IndexPath.DS."core.framework.config";
	
	/* ejecución del framework */
	$f->run();
?>