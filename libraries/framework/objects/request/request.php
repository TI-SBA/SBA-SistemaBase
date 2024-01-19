<?php
/**
 * Controla la solicitud
 * @name Request
 * @version 1.2
 */
class Request {
	private $structure;
	
	public function __construct() {
		$this->structure = array(
			'method' => $this->getMethod(),
			'host' => $this->getHost(),
			'port' => $this->getPort(),
			'root' => $this->getRoot(),
			'call' => $this->getCall(),
			'data' => $this->getData()
		);
		if (tracing()) Trace::table(array(array_keys($this->structure), $this->structure), 'Request');
	}
	
	public function get($var_name, $value = null) {
		if (! isset($_GET[$var_name]) ) { throw new Exception(); }
		if (! is_null($value) ) { $_GET[$var_name] = $value; }
		return $_GET[$var_name];
	}
	
	public function post($var_name, $value = null) {
		if (! isset($_POST[$var_name]) ) { throw new Exception(); }
		if (! is_null($value) ) { $_POST[$var_name] = $value; }
		return $_POST[$var_name];
	}
	
	public function cookie($name) {
		return isset($_COOKIE[$name]) ? $_COOKIE[$name] : null;
	}
	
	private function getHost() {
		return isset($_SERVER['HTTP_X_FORWARDED_HOST']) ? $_SERVER['HTTP_X_FORWARDED_HOST'] : $_SERVER['HTTP_HOST'];
	}
	
	private function getPort() {
		return $_SERVER['SERVER_PORT'];
	}
	
	private function getMethod() {
		return $_SERVER['REQUEST_METHOD'];
	}
	
	private function getRoot() {
		return str_replace(IndexFile, "", $_SERVER['PHP_SELF']);
	}
	
	private function getCall() {
		// quitar la parte del query string de la URL: ?var=...
		$call = trim(str_replace($_SERVER['QUERY_STRING'], "", $_SERVER['REQUEST_URI']), "?");
		if ($this->getRoot() == "/") {
			// si la aplicación se encuentra en la raiz del sitio, quitar el primer /
			$call = ltrim($call, "/");
		} else  {
			// si la raíz de la aplicación es una subcarpeta, quitarla
			$call = str_replace($this->getRoot(), "", $call);
		}
		return $call;
	}
	
	private function getData() {
		return array_merge($_GET, $_POST);
	}
	
	public function __get($property) {
		if ( array_key_exists($property, $this->structure) ) { return $this->structure[$property]; }
		elseif ( array_key_exists($property, $this->structure['data']) ) { return $this->structure['data'][$property]; }
		else { throw Application::Exception('Req001', array($property)); }
	}
	
	public function __set($property, $value) {
		if (! array_key_exists($property, $this->structure['data']) ) {
			throw Application::Exception('Req001', array($property));
		} else {
			$this->structure['data'][$property] = $value;
		}
	}
}
?>