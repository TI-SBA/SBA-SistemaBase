<?php
/**
 * FRAMEWORK v. 1.2
 * @name core.framework
 * Archivo bootstrap
 * @author Daniel Salas
 * @version 1.2
 */
define( 'FrameworkName', "core.framework" );
define( 'FrameworkVersion', "1.2" );
define( 'FrameworkRevision', 7 );

header( "X-Powered-By: " . "PHP/".phpversion() . "; " . FrameworkName."/".FrameworkVersion."-".FrameworkRevision );
define( 'DS', DIRECTORY_SEPARATOR );
define( 'FrameworkPath' , dirname(__FILE__) );
define( 'IndexPath', dirname($_SERVER['SCRIPT_FILENAME']) );
define( 'IndexFile', basename($_SERVER['SCRIPT_FILENAME']) );
define( 'AccountName', isset($_SERVER['HTTP_X_FORWARDED_HOST']) ? $_SERVER['HTTP_X_FORWARDED_HOST'] : "");
//define( 'DIR_LIBRARIES' , DIR_BASE.DS.'libraries' );
//define( 'DIR_COMPONENT' , DIR_BASE.DS.'components' );

require_once( FrameworkPath.DS.'application.php' );

// registrar autocargador de clases del framework
spl_autoload_register(array('Application', 'loadClass'));
//set_error_handler(array('Application', 'handleError'));
set_exception_handler(array('Application', 'handleException'));

//ob_start(array('Response', 'outputBuffer'));

//sesiones
/* session_set_save_handler(
	array('Session', '_open'),
	array('Session', '_close'),
	array('Session', '_read'),
	array('Session', '_write'),
	array('Session', '_destroy'),
	array('Session', '_gc')); */

//cambiar directorio de trabajo para manejo de archivos
chdir(IndexPath);

//if ( function_exists("__autoload") ) { throw new Exception('se repite __autoload'); }
//FB::log(array(1,2));
//FB::info('hola');

//$f = new Application();

function tracing() { return Application::getInstance()->traceMode; }

$f = Application::getInstance();
$f->traceMode = Application::traceMode_off;
?>