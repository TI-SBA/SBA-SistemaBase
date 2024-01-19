<?php
/**
 * Clase cargadora de objetos del Framework
 * @name Loader
 * @version 1.3
 */
class Loader {
	
	public function datastore($params) {
		if (! isset($params["driver"]) ) { throw Application::Exception('Dst001'); }
		$filename = FrameworkPath.DS.'objects'.DS.'datastore'.DS.$params['driver'].DS.'datastore.php';
		if (! file_exists($filename)) { throw Application::Exception('Ldr001', array($filename)); }
		require_once ($filename);
		$ClassName = "Datastore_".$params["driver"];
		return new $ClassName($params);
	}
	
	public function controller($params) {
		$filename = isset($params['config-path']) ? $params['config-path'] : "controllers/$1";
		foreach ($params['vars'] as $i => $var) {
			$filename = str_replace("$".($i+1), $var, $filename);
		}
		if ( substr($filename, -4) != ".php" ) { $filename .= ".php"; }
		$filename = IndexPath.DS.$filename;
		if (! file_exists($filename)) { throw Application::Exception('Ldr001', array($filename)); }
		require_once ($filename);
		$ClassName = 'Controller_'.$params["name"];
		if (! class_exists($ClassName, false)) { throw Application::Exception('Ctr001', array($ClassName)); }
		$newObject = new $ClassName;
		if (! $newObject instanceof Controller) { throw Application::Exception('Ctr002', array($ClassName)); }
		if (tracing()) Trace::info("Controlador " . $ClassName . " cargado", 'Application');
		return $newObject;
	}
	
	public function model($params) {
		$ClassName = 'Model_'.$params["name"];
		if (! class_exists($ClassName, false)) {
			$filename = isset($params['config-path']) ? $params['config-path'] : "models/$1";
			foreach ($params['vars'] as $i => $var) {
				$filename = str_replace("$".($i+1), $var, $filename);
			}
			if ( substr($filename, -4) != ".php" ) { $filename .= ".php"; }
			$filename = IndexPath.DS.$filename;
			if (! file_exists($filename)) { throw Application::Exception('Ldr001', array($filename)); }
			require_once ($filename);
			if (! class_exists($ClassName, false)) { throw Application::Exception('Mod001', array($ClassName)); }
			
		}
		$newObject = new $ClassName;
		if (! $newObject instanceof Model) { throw Application::Exception('Mod002', array($ClassName)); }
		if (tracing()) Trace::info("Modelo " . $ClassName . " cargado", 'Application');
		return $newObject;
	}
	
	public function view($params) {
		$blocks = $params['view-parts'];
		$filename = isset($params['config-path']) ? $params['config-path'] : "views/$1";
		foreach ($blocks as $k => $block) {
			$filename = str_replace("$".($k+1), $block, $filename);
		}
		if ( substr($filename, -4) != ".php" ) { $filename .= ".php"; }
		$filename = IndexPath.DS.$filename;
		if (! file_exists($filename)) { throw Application::Exception('Ldr001', array($filename)); }
		if (is_array($params['view-data'])) {
			extract( $params['view-data'] );
		} elseif (is_object($params['view-data'])) {
			extract( get_object_vars($params['view-data']) );
		}
		include ($filename);
	}
	
	public function dataobject($params) {
		$ClassName = $params["name"];
		if (! class_exists($ClassName, false)) {
			$filename = isset($params['config-path']) ? $params['config-path'] : "models/dataobjects/$1";
			foreach ($params['vars'] as $i => $var) {
				$filename = str_replace("$".($i+1), $var, $filename);
			}
			if ( substr($filename, -4) != ".php" ) { $filename .= ".php"; }
			$filename = IndexPath.DS.$filename;
			if (! file_exists($filename)) { throw Application::Exception('Ldr001', array($filename)); }
			require_once ($filename);
			if (! class_exists($ClassName, false)) { throw Application::Exception('Dob001', array($ClassName)); }
		}
		return new $ClassName;
		$newObject = new $ClassName;
		if (! $newObject instanceof DataObject) { throw Application::Exception('Dob002', array($ClassName));}
		if (tracing()) Trace::info("Objeto " . $ClassName . " cargado", 'Application');
		return $newObject;
	}
	
	public function object($params) {
		if ($params['name'] =! "*") {
			if ( class_exists($params["name"], false) ) { return; }
		}
		$filename = isset($params['config-path']) ? $params['config-path'] : "models/dataobjects/$1";
		foreach ($params['vars'] as $i => $var) {
			$filename = str_replace("$".($i+1), $var, $filename);
		}
		if ( substr($filename, -4) != ".php" ) { $filename .= ".php"; }
		$filename = IndexPath.DS.$filename;
		if (! file_exists($filename)) { throw Application::Exception('Ldr001', array($filename)); }
		require_once ($filename);
		if (tracing()) Trace::info("Objetos del archivo " . $filename . " cargados", 'Application');
		if ($params['name'] =! "*") {
			if ( class_exists($params['name'], false) ) { throw Application::Exception('Dob003', array($params['name'])); }
		}
	}
}
?>