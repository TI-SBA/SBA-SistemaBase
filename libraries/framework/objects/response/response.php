<?php
/**
 * Controla la respuesta
 * @name Response
 * @version 1.2
 */
class Response {
	//private $buffer;
	
	public function __construct() {
		ob_clean();
		ob_start(array($this, "outputBuffer"));
	}
	
	public function outputBuffer($buffer) {
		return $buffer;
	}
	
	public function view( $view_name, $data = null ) {
		if (tracing()) Trace::info("Cargando vista " . $view_name, 'Response');
		$application = Application::getInstance();
		$view_parts = explode("/", trim($view_name, "/"));
		$application->load->view( array(
			"config-path" => $application->config("application", "views-path"),
			"view-parts" => $view_parts,
			"view-data" => $data
		));
		//$view =& ob_get_contents();
	}
	
	public function json( $output ) {
		//$output = $this->encode($output);
		header('Content-Type: text/plain; charset=UTF-8');
		print json_encode($output);
	}
	
	public function file( $type, $data ) {
		header('Content-Type: ' . $type);
		print $data;
	}
	
	public function cookie($name, $value = null, $expire = 0) {
		switch (true) {
			case (is_null($value)):
				setcookie($name, null, time() - 1000);
				break;
			case ($expire != 0):
				setcookie($name, $value, time() + $expire);
				break;
			default:
				setcookie($name, $value);
		}
	}
	
	public function download( $filename, $type, $data ) {
		header('Content-Disposition: attachment; filename="' . $filename . '"');
		header('Content-Type: ' . $type);
		print $data;
	}
	
	public function redirect( $location ) {
		header("Location: " . $location);
	}
	
	public function forbidden( $message='' ) {
		header("HTTP/1.1 403 Forbidden");
		//header("Location: " . Application::getInstance()->request->root);
		Application::getInstance()->response->redirect(Application::getInstance()->request->root);
		print $message;
	}
	
	public function error_internal( $message ) {
		header("HTTP/1.1 500 Internal Server Error");
		print $message;
	}
	
	public function error_user( $message ) {
		header("HTTP/1.1 501 Not Implemented");
		print $message;
	}
	
	// para errores definidos por el usuario
	public function _throw( $message, $parts = null ) {
		if (is_array($parts)) {
			$message = call_user_func_array('sprintf', array_merge($message, $parts));
		}
		throw Application::Exception('Usr000', array($message));
		//header("HTTP/1.1 501 Not Implemented");
		//print $message;
		//die();
	}
	
	public function header($name, $content) {
		header($name . ': ' . $content);
	}
	
	private function _print( $output ) {
		print $output;
	}
	
	private function encode($subject) {
		switch (true) {
		case (is_string($subject)):
			return utf8_encode($subject);
			break;
		case (is_array($subject)):
			foreach($subject as $k => $v) {
				$subject[$k] = $this->encode($v);
			}
			return $subject;
			break;
		case (is_object($subject)):
			foreach(get_object_vars($subject) as $k => $v) {
				$subject->$k = $this->encode($v);
			}
			return $subject;
			break;
		default:
			return $subject;
		}
	}
	
	function __call($method, $args) {
		switch ($method) {
			case "print":
				if ( count($args) > 1 ) { return false; }
				$this->_print($args[0]);
				break;
			case "throw":
				if ( count($args) > 1 ) { return false; }
				$this->_throw($args[0]);
				break;
		}
	}
}
?>