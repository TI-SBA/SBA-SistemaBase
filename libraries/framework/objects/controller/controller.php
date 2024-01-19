<?php
/**
 * Controlador
 * @name Controller
 * @version 1.2
 */
abstract class Controller {
		
	/* Para utilizar la clase Controller, se debe crear una clase Controller_{algo}
	 * que contenga métodos con la forma: execute_{algo}()
	 * el Framework hará la llamada en la forma $f->controller->{algo}()
	 */
	public function __call($task, $params) {
		/* verificar si tarea existe */
		$method = 'execute_' . $task;
		if (! method_exists($this, $method) ) { throw Application::Exception('Ctr003', array(get_class($this), $method)); }
		
		/* ejercer seguridad según si sesión está en modo estricto */
		$Application = Application::getInstance();
		if (! $Application->session->valid) {
			if($task!='index')
				$Application->response->forbidden();
		}
		
		/* ejecutar tarea */
		//try {
			/* soporte para debug */
			if (tracing()) Trace::info("Ejecutando la tarea " . $method, 'Controller');
			$this->$method();
		/*} catch(Exception $e) {
			echo("Error ejecutando controlador '" . $method . "': " . $e->getMessage());
		}*/
	}
}
?>