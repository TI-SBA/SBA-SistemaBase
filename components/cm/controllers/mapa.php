<?php
class Controller_cm_mapa extends Controller {
	function execute_index() {
		global $f;
		$f->response->print('<button name="btnBuscar">Buscar Espacio</button>');
		$f->response->print('<button name="btnEditar">Editar Plano</button>');
		$f->response->print('<button name="btnHome">Regresar a Mapa General</button>');
		$f->response->print("</div>");
	}
	function execute_cuadra(){
		global $f;
		$model = $f->model('cm/espa')->get('lista_mapa');
		$f->response->json( $model->items );
	}
	function execute_lista(){
		global $f;
		$model = $f->model('cm/espa')->params(array("_id"=>$f->request->_id))->get('lista_get_mapa');
		$f->response->json( $model );
	}
	function execute_edit(){
		global $f;
		$f->response->print("<div style='height:30px;line-height:30px'>");
		$f->response->print('<button name="btnCrear">Seleccionar Espacio</button>');
		$f->response->print('<button name="btnPabe">Seleccionar Pabell&oacute;n</button>');
		$f->response->print('<button name="btnDesa">Limpiar</button>');
		$f->response->print("</div><div id='p' name='mainGrid'></div>");
	}
}
?>