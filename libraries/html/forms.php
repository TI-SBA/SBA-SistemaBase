<?php
class HTML_forms {

/*	function MENU_BUTTON ($label, $id, $imgstyle='', $link="", $ancho=0)
	{
		$boton = '';
		$r = '';
		if($link == "") $link = 'javascript:void(0)';
		if($label != ""){ $boton = 'class="toolButton"';
		$r = '<a href="'.$link.'" '.$boton.' id="'.$id.'" title="'.strip_tags($label).'" >';}
		if($imgstyle != ''){
			$r .= '<span class="'.$imgstyle.'"></span>';
		}
		if($label != ""){ $boton = 'class="toolButton"';
		$r .= $label . '</a>';}
		return $r;
	}*/
	function MENU_BUTTON ($label, $id, $imgstyle='', $link="", $ancho=0)
	{
		$boton = '';
		$r = '';
		if($link == "") $link = 'javascript:void(0)';
		if($label != ""){ $boton = 'class="toolButton"';
		$r='<button id="'.$id.'" '.$boton.'  VALUE="'.strip_tags($label).'">';}
		
	//	$r = '<a href="'.$link.'" '.$boton.' id="'.$id.'" title="'.strip_tags($label).'" >';}
		if($imgstyle != ''){
			$r .= '<span class="'.$imgstyle.'"></span>';
		}
		if($label != ""){ $boton = 'class="toolButton"';
		$r .= $label . '</button>';}
		return $r;
	}
	function INPUT_HIDDEN ($value, $id, $largo=10,$options="",$params = null){
		$r = '';
		$v = '';
		if($params != null){
			if(isset($params['label'])){
				$r .= '<label class="label_hide" for="'. $id .'">'.$params['label'].'</label>';
			}
			if(isset($params['valid'])){
				$v = $params['valid'];
			}
		}
		$r .= "<input type='text' class='inputHide $v' readonly='readonly'  value='$value' id='$id' name='$id' style='width:".$largo."px;' $options />";
		return $r;
	}
	function INPUT_PASSWORD ($value, $id, $largo=10,$options="",$params = null){
		$r = '';
		$v = '';
		if($params != null){
			if(isset($params['label'])){
				$r .= '<label class="" for="'. $id .'">'.$params['label'].'</label>';
			}
			if(isset($params['valid'])){
				$v = $params['valid'];
			}
		}
		$r .= "<input type='password' class='inputHide $v' value='$value' id='$id' name='$id' style='width:".$largo."px;' $options />";
		return $r;
	}
	function LABEL($lbl,$for,$largo=20,$options=""){
		$r = "<input type='text' class='inputHide' readonly='readonly' style='width:".$largo."px' $options  value='$lbl'>";
		return $r;
	}
	function INPUT_DATEPICKER ($value, $id, $largo=10,$options=""){

		$r = "<input type='text' class='inputDate' readonly='readonly' value='$value' id='$id' name='$id' style='width:".$largo."px;' autocomplete='off' $options />";
		return $r;
	}
	function COMBO($obj, $campo, $id, $id_select="",  $lbl="", $largo=20, $options="",$general="")
	{
		$r = "";
		if($lbl != ""){
			$r .= "<label for='$id' style='width:".($largo-1)."px;' class='label'>". $lbl."</label>";
		}
		$r .= "<select id='$id' name='$id' style='width:".($largo+9)."px;' > ";
		if($general != ""){
			$r .= '<option value="todo"  selected="selected" > Todos </option>';
		}
		foreach ($obj as $c=>$v){
			
			if(is_object($v)){
				$r .= '<option value="'. $v->id . '"';
				if($v->id == $id_select) $r .= " selected='selected'";

				$campos = split(',',$campo);
				$r .= '>';
				foreach ($campos as $c_c => $c_v){
					if(is_object($v->$c_v)){
						$r .= $v->$c_v->get_html() . ' ';
					}else{
						$r .= $v->$c_v . ' ';
					}
				}
				$r .= '</option>';
			} elseif (is_array($v)) {
				$r .= '<option value="'. $v['id'] . '"';
				if($v['id'] == $id_select) $r .= " selected='selected'";

				$r .= '>';
				$r .= $v[$campo];
				$r .= '</option>';
			}else{
				$r .= '<option value="'. $c . '"';
				if($c == $id_select) $r .= " selected='selected'";
				$r .= '>' . $v . '</option>';
			}
		}
		$r .= "</select>";
		return $r;
	}
	function COMBO_UTF($obj, $campo, $id, $id_select="",  $lbl="", $largo=20, $options="",$general="")
	{
		$r = "";
		if($lbl != ""){
			$r .= "<label for='$id' style='width:".($largo-1)."px;' class='label'>". $lbl."</label>";
		}
		$r .= "<select id='$id' name='$id' style='width:".($largo+9)."px;' > ";
		if($general != ""){
			$r .= '<option value="todo"  selected="selected" > Todos </option>';
		}
		foreach ($obj as $c=>$v){
			
			if(is_object($v)){
				$r .= '<option value="'. $v->id . '"';
				if($v->id == $id_select) $r .= " selected='selected'";

				$campos = split(',',$campo);
				$r .= '>';
				foreach ($campos as $c_c => $c_v){
					if(is_object($v->$c_v)){
						$r .= $v->$c_v->get_html() . ' ';
					}else{
						$r .= utf8_encode($v->$c_v) . ' ';
					}
				}
				$r .= '</option>';
			} elseif (is_array($v)) {
				$r .= '<option value="'. $v['id'] . '"';
				if($v['id'] == $id_select) $r .= " selected='selected'";

				$r .= '>';
				$r .= utf8_encode($v[$campo]);
				$r .= '</option>';
			}else{
				$r .= '<option value="'. $c . '"';
				if($c == $id_select) $r .= " selected='selected'";
				$r .= '>' . utf8_encode($v) . '</option>';
			}
		}
		$r .= "</select>";
		return $r;
	}
	function COMBO_HIDDEN ($obj, $campo, $id, $id_select="",  $lbl="", $largo=20, $options=""){
		$r = "<label for='$id' style='width:".($largo-2)."px;' class='label'>". $lbl."</label> <select id='$id' name='$id' style='width:".($largo+9)."px; display:none;'>";
		$count=0;
		foreach ($obj as $c=>$v){
			if(is_object($v)){
				$r .= '<option value="'. $v->id . '"';//print_r($v->id);die();
				if($v->id == $id_select){ $r .= " selected='selected'"; $count=1;}

				$campos = split(',',$campo);
				$r .= '>';
				foreach ($campos as $c_c => $c_v){
					if(is_object($v->$c_v)){
						$r .= $v->$c_v->get_html() . ' ';
					}else{
						$r .= $v->$c_v . ' ';
					}
				}
				$r .= '</option>';
			}else{
				$r .= '<option value="'. $c . '"';
				if($c == $id_select) {$r .= " selected='selected'";$count=1;}
				$r .= '>' . $v . '</option>';
			}
		}
		if ($count==0){
			$r .= '<option value=null';
			$r .= " selected='selected'";
				$r .= '>No Asignado</option>';
		}
	
		$r .= "</select>";
		return $r;
	}
	function HIDDEN ($value, $id){
		$r = "<input type='hidden' value='$value' id='$id' name='$id' />";
		return $r;
	}
	function INPUT ($value, $id, $largo=10,$options=""){
		$r = "<input type='text' class='input' value='$value' id='$id' name='$id' style='width:".$largo."px;' $options/>";
		return $r;
	}
	function CHECK ($label, $id, $value,  $selected=false, $options=""){
		$sel = '';
		if($selected){
			$sel = 'checked="checked"';
		}
		$r = "<label><input type='checkbox' class='input' value='$value' name='$id' id='$id' '$sel' $options/> $label</label>";
		return $r;
	}
	function LISTA_EDITABLE($id, $data, $columnas=null, $editable=true, $path="", $options=null)
	{
		global $f;
		$num_reg = 10;
		$pk = 'id';

		if($options != null && isset($options['pk'])){
			$pk = $options['pk'];
		}

		// Generar cabeceras
		$s = "<div class='listaCabeceras'>";
		if($columnas != null){
			foreach($columnas as $c => $v){
				if(!isset($v['visible']) || $v['visible']){
					$s .= '<span style="width:' . ($v['w']) . 'px;" >' . $v['lbl'] . '</span>';
				}
			}
		}
		$s .= '</div><div class="list_block" id="'.$id.'">';
		// Generar formulario de nuevos registros
		$s .= "<span class='dummy_nuevo' style='display:none;'><span class='controles'><a href='javascript:void(0);' id='editar_item' class='tool_editar'></a><a href='javascript:void(0);' id='eliminar_item' class='tool_eliminar'></a></span><form class='campos'>";
		$s .= $this->CHECK("","check_id",0,false,"disabled='disnabled'");
			if($columnas != null){
				foreach($columnas as $c => $v){
					if(isset($v['send']) && !$v['send']) $c = "";
					if(isset($v['visible']) && !$v['visible']){
						$s .= $this->HIDDEN(0, $c) . "\n ";
					}else{
						if(isset($v['obj']) && $v['obj'] != ""){
							$s .= $this->COMBO_HIDDEN($v['lista'], $v['obj'], $c,"" ,"" , $v['w']);
							//$s .= $this->INPUT_HIDDEN('', $c ,$v['w'],"title=\"".$v['lbl'] . "\"") . "\n ";
						}else{
							if(!isset($v['valid'])){	$v['valid'] = ""; }
							if(isset($v['tipo'])){
								switch ($v['tipo']){
									case "fecha":
										$s .= $this->INPUT_DATEPICKER("", $c ,$v['w']) . "\n ";
										break;
								}
							}else{
								$s .= $this->INPUT_HIDDEN("", $c ,$v['w'],"",array("valid"=>$v['valid'])) . "\n ";

							}
						}
					}
				}
			}
		$s .= "</form>\n";
		$s .= "</span>";
		$s .= $this->HIDDEN($f->config->url->base . "/index.php/" . $path, 'path_url');
		$s .= '<ul id="lista">';

		//Recorrer array
		foreach ($data as $i => $r) {
			$s .= "<li id='item_".$r->$pk."'>";

			$s .= "<span class='controles'>";
			if($options != null && $options['quickedit']){
				$s .= "<a href='javascript:void(0);' id='largeedit_item' class='tool_editar' title='Editar'></a><a href='javascript:void(0);' id='quickedit_item' class='tool_quickedit' rel='large_edit' title='Edición rápida'></a>";
			}elseif (! isset($options['deleteonly']) ){
				$s .= "<a href='javascript:void(0);' id='editar_item' class='tool_editar' title='Editar'></a>";
			}
			if($editable){
				$s .= "<a href='javascript:void(0);' id='eliminar_item' class='tool_eliminar' title='Eliminar item'></a>";
			}
			$s .= "</span><form method='POST' action='index.php' class='campos'><input type='submit'  style='display:none' />";

			$s .= $this->CHECK("", "check_id", $r->$pk);
			if($columnas == null){
				foreach($r as $c => $v){
					if($c == "id"){
						$s .= $this->INPUT($v, $c ,20) . " \n";
					}else{
						$s .= $this->INPUT($v, $c ,100) . " \n";
					}
				}
			}else{
				foreach($columnas as $c => $v){
					if(isset($v['visible']) && !$v['visible']){
						$s .= $this->HIDDEN($r->$c, $c) . "\n ";
					}else if(!$v['edit']){
						if(isset($v['obj']) && $v['obj'] != ""){
							if($v['obj'] != 'array'){
								$s .= $this->LABEL($r->$c->$v['obj'],"",$v['w']);
							}
							else{
								$s .= $this->LABEL($r->$c->get_value(),"",$v['w']);
							}
						}else{
							
							$s .= $this->LABEL($r->$c,"",$v['w']);
						}
						
					}else{
						if(!isset($v['valid'])){	$v['valid'] = ""; }
						if(isset($v['obj']) && $v['obj'] != ""){
							if($v['obj'] != 'array'){
								if(isset($r->$c->id)){
									$s .= $this->COMBO_HIDDEN($v['lista'], $v['obj'], $c, $r->$c->id, $r->$c->$v['obj'], $v['w']);
								}else{
									$s .= $this->COMBO_HIDDEN($v['lista'], $v['obj'], $c, $r->$c, $r->$c, $v['w']);
								}
							}else{
								//echo '<b>Clave: ' . $c . " Select: " . $r->$c->get() . '</b><br>';
								$s .= $this->COMBO_HIDDEN($v['lista'], $v['obj'], $c, $r->$c->get(), $r->$c->get_value(), $v['w']);
							}
							//$s .= $this->INPUT_HIDDEN($r->$c->$v['obj'], "hide_" + $c ,$v['w'],"title=\"".$v['lbl'] . "\"") . "\n ";
						}else{
							if(isset($v['tipo'])){
								switch ($v['tipo']){
									case 'fecha':
										$s .= $this->INPUT_DATEPICKER($r->$c, $c ,$v['w']) . "\n ";
										//$s .= $this->INPUT_HIDDEN($r->$c, $c ,$v['w'],"title=\"".$v['lbl'] . "\"") . "\n ";
										break;
									case 'label':
										$s .= $this->LABEL($r->$c,"",$v['w']);
								}
							}else{
								if(!isset($v['func'])){
									$fun = "get_html";
								}else{
									$fun = $v['func'];
								}
								$s .= $this->INPUT_HIDDEN($r->$c->$fun(), $c ,$v['w'],'title="'.$v['lbl'] . '"',array("valid"=>$v['valid']));
							}
						}
					}
				}
			}
			$s .= "</form></li>";
		}

		if(count($data) == 0){
			$s .= "<li class='last'>No se encontraron registros</li>";
		}

		$s .= '</ul></div>';
		return $s;
	}
	function LISTA_EDITABLE_FOOTER($obj_page)
	{
		$pages = $obj_page;
		$r = '<div class="tile_menu">

			<p style="text-align: right; float:right;"><label for="filtro_lista">Buscar en lista:
			</label><input type="text" id="filtro_lista" class="input"></p>
			<form id="form_paginacion" method="get" action="">
				<input type="hidden" value="'.$pages->page.'" id="page" name="page">
				<ul>';
			//print_r($pages);
			if(isset($pages) && $pages->pages != ""){
				$number = $pages->page - 2;
				$cant = 10;
				$final = 5;

				if($number < 1)
					$number = 1;

				$final = $number + 4;

				$r .= '<li><a href="1"> |< </a></li>';
				for($i=$number; $i<=$final; $i++){
					if($i > $pages->pages){
						break;
					}

					if($i == $pages->page){
						$r .= '<li><a href="'. $i .'" class="item_selected"> '. $i .' </a></li>';
					}else{
						$r .= '<li><a href="'. $i .'"> '. $i .' </a></li>';
					}
				}
				$r .= '<li><a href="'. $pages->pages .'"> >| </a></li>';
			}



			$r .= '</ul>Página '.$pages->page.' de '.$pages->pages.' | Mostrar <select id="cant" name="cant">
				<option value="10" '.(($pages->page_rows==10)?'selected="selected"':'') .'>10 Registros</option>
				<option value="20" '.(($pages->page_rows==20)?'selected="selected"':'') .'>20 Registros</option>
				<option value="50" '.(($pages->page_rows==50)?'selected="selected"':'') .'>50 Registros</option>
				<option value="100" '.(($pages->page_rows==100)?'selected="selected"':'') .'>100 Registros</option>
				</select>
			</form>
		</div>';

		return $r;
	}
	function TABS_UNIDADES_PROGRAMAS($id, $obj_unidades, $obj_programas)
	{
		$tabs_c = "";
		$tabs = "";
		$alias = array();
		$alias['C'] = array('nombre'=>'C',		'programa'=>'Programas');
		$alias['I'] = array('nombre'=>'I',	'programa'=>'Carreras');
		$alias['X'] = array('nombre'=>'X',	'programa'=>'Programas');

		foreach($obj_unidades as $u){
			$tabs .= "<li><a href='#".$u->id."' title='".$u->nomb."'>" . $u->nomb->get_html() . "</a></li>";
			$tabs_c .= "<div id='". $u->id ."'>";
			$tabs_c .= '<div id="lista_programas_'. $u->id .'" class="lista_menu"><h3>'.$alias[$u->id->get()]['programa'].'</h3><ul>';
			$count = 0;
			foreach($obj_programas as $kp => $vp){
				if($vp->unid->id == $u->id){$count++; $tabs_c .= '<li><a href="#prog='.$vp->id.'" id="'. $vp->id  .'"><span style="float:left;width:20px;" class="flag_hide">'.$vp->periodos.'</span>' . $vp->nomb->get_html() . '</a></li>';}
			}
			if($count == 0)$tabs_c .= '<li><a href="../ad/programas">No hay <b>elementos</b> disponibles, haga clic aqu&iacute; para agregar <b>'.$programa_name.'</b></a></li>';
			$tabs_c .= '</ul></div>'; //Cierra lista de programas
			$tabs_c .= "</div>"; //Cierra contenedor de tab
		}

		$r = '<div id="'.$id.'"><ul>'.$tabs.'</ul>'.$tabs_c.'</div>';
		return $r;
	}
	function TABS_UNIDADES($id, $obj_unidades, $obj_lista, $titulo, $caja_si="", $mostrar=true )
	{
		$tabs_c = "";
		$tabs = "";
		$alias = array();
		$alias['C'] = array('nombre'=>'CET.',	'programa'=>'Programas');
		$alias['I'] = array('nombre'=>'Ins.',	'programa'=>'Carreras');
		$alias['X'] = array('nombre'=>'Ext.',	'programa'=>'Programas');

		foreach($obj_unidades as $u){
			$tabs .= "<li><a href='#".$u->id."' title='".$u->nomb."'>" . $u->nomb->get_html() . "</a></li>";
			$tabs_c .= "<div id='". $u->id ."'><h2>".$titulo."</h2>";
			$tabs_c .= '<div id="lista_programas_'. $u->id .'" class="lista_menu"><ul>';
			$count = 0;
			foreach($obj_lista as $kp => $vp){
				$count++;
				if(eregi($u->id,$vp['group'])){
					$tabs_c .= '<li><a href="#step'.$count.'" id="'.$vp['id'].'"><span class="flag_hide" style="width:20px;">'.$count.'</span>'.$vp['label']. '</a></li>';
				}else{
					//$tabs_c .= '<li class="disabled"><span style="float:left;width:20px;" class="flag_hide">'.$count.'</span>'.$vp['label']. '</li>';
				}
			}
			if($caja_si==""){
				if($count == 0)$tabs_c .= '<li><a href="../ad/programas">No hay <b>'.$alias[$u->id]['programa'].'</b> disponibles, haga clic aqu&iacute; para agregar <b>'.$programa_name.'</b></a></li>';
			}else{
				if($count == 0)$tabs_c .= '<li><a href="../cj/cajas">Haga clic aqu&iacute; para agregar una Caja</a></li>';
			}
			$tabs_c .= '</ul></div>'; //Cierra lista de programas
			$tabs_c .= "</div>"; //Cierra contenedor de tab
		}
		$r = '<div id="'.$id.'"><ul>'.$tabs.'</ul>'.$tabs_c.'</div>';
		return $r;
	}
	function TABS_CAJA($id, $obj_unidades, $obj_lista, $titulo, $caja_si="", $mostrar=true )
	{
		$tabs_c = "";
		$tabs = "";
		$alias = array();
		$alias['C'] = array('nombre'=>'CET.',	'programa'=>'Programas');
		$alias['I'] = array('nombre'=>'Ins.',	'programa'=>'Carreras');
		$alias['X'] = array('nombre'=>'Ext.',	'programa'=>'Programas');

		foreach($obj_unidades as $u){
			$tabs .= "<li><a href='#".$u->id."' title='".$u->nomb."'>" . $u->nomb->get_html() . "</a></li>";
			$tabs_c .= "<div id='". $u->id ."'><h2>".$titulo."</h2>";
			$tabs_c .= '<div id="lista_programas_'. $u->id .'" class="lista_menu"><ul>';
			$count = 0;
			foreach($obj_lista as $kp => $vp){
				$count++;
				if(eregi($u->id,$vp['group'])){
					$tabs_c .= '<li><a href="#step'.$count.'" id="'.$vp['id'].'"><span class="flag_hide" style="width:20px;">'.$vp['id_caja'].'</span>'.$vp['label']. '</a></li>';
				}else{
					//$tabs_c .= '<li class="disabled"><span style="float:left;width:20px;" class="flag_hide">'.$count.'</span>'.$vp['label']. '</li>';
				}
			}
			if($caja_si==""){
				if($count == 0)$tabs_c .= '<li><a href="../ad/programas">No hay <b>'.$alias[$u->id]['programa'].'</b> disponibles, haga clic aqu&iacute; para agregar <b>'.$programa_name.'</b></a></li>';
			}else{
				if($count == 0)$tabs_c .= '<li><a href="../cj/cajas">Haga clic aqu&iacute; para agregar una Caja</a></li>';
			}
			$tabs_c .= '</ul></div>'; //Cierra lista de programas
			$tabs_c .= "</div>"; //Cierra contenedor de tab
		}
		$r = '<div id="'.$id.'"><ul>'.$tabs.'</ul>'.$tabs_c.'</div>';
		return $r;
	}
	function TABS_COMPROBANTE($id, $obj_unidades, $obj_lista, $titulo, $caja_si="", $mostrar=true )
	{
		$tabs_c = "";
		$tabs = "";
		$alias = array();
		$alias['C'] = array('nombre'=>'CET.',	'programa'=>'Programas');
		$alias['I'] = array('nombre'=>'Ins.',	'programa'=>'Carreras');
		$alias['X'] = array('nombre'=>'Ext.',	'programa'=>'Programas');

		foreach($obj_unidades as $u){
			$tabs .= "<li><a href='#".$u->id."' title='".$u->nomb."'>" . $u->nomb->get_html() . "</a></li>";
			$tabs_c .= "<div id='". $u->id ."'><h2>".$titulo."</h2>";
			$tabs_c .= '<div id="lista_programas_'. $u->id .'" class="lista_menu"><ul>';
			$count = 0;
			foreach($obj_lista as $kp => $vp){
				$count++;
				if(eregi($u->id,$vp['group'])){
					$tabs_c .= '<li><a href="#step'.$count.'" id="'.$vp['id_caja'].'"><span class="flag_hide" style="width:20px;">'.$count.'</span>'.$vp['label']. '</a></li>';
				}else{
					//$tabs_c .= '<li class="disabled"><span style="float:left;width:20px;" class="flag_hide">'.$count.'</span>'.$vp['label']. '</li>';
				}
			}
			if($caja_si==""){
				if($count == 0)$tabs_c .= '<li><a href="../ad/programas">No hay <b>'.$alias[$u->id]['programa'].'</b> disponibles, haga clic aqu&iacute; para agregar <b>'.$programa_name.'</b></a></li>';
			}else{
				if($count == 0)$tabs_c .= '<li><a href="../cj/cajas">Haga clic aqu&iacute; para agregar una Caja</a></li>';
			}
			$tabs_c .= '</ul></div>'; //Cierra lista de programas
			$tabs_c .= "</div>"; //Cierra contenedor de tab
		}
		$r = '<div id="'.$id.'"><ul>'.$tabs.'</ul>'.$tabs_c.'</div>';
		return $r;
	}
	function TABS_LISTA($id, $obj_tabs, $obj_lista, $titulo, $mostrar=true)
	{
		$tabs_c = "";
		$tabs = "";
		$alias = array();
		$alias['C'] = array('nombre'=>'CET.',	'programa'=>'Programas');
		$alias['I'] = array('nombre'=>'Ins.',	'programa'=>'Carreras');
		$alias['X'] = array('nombre'=>'Ext.',	'programa'=>'Programas');

		foreach($obj_tabs as $u){
			$tabs .= "<li><a href='#".$u['id']."' title='".$u['label']."'>" . $u['label'] . "</a></li>";
			$tabs_c .= "<div id='". $u['id'] ."'><h2>".$titulo."</h2>";
			$tabs_c .= '<div id="lista_programas_'. $u['id'] .'" class="lista_menu"><ul>';
			$count = 0;
			foreach($obj_lista as $kp => $vp){
				$count++;
				if(eregi($u['id'],$vp['group'])){
					$tabs_c .= '<li><a href="#step'.$count.'" id="'.$count.'"><span style="float:left;width:20px;" class="flag_hide">'.$vp['id_caja'].'</span>'.$vp['label']. '</a></li>';
				}else{
					//$tabs_c .= '<li class="disabled"><span style="float:left;width:20px;" class="flag_hide">'.$count.'</span>'.$vp['label']. '</li>';
				}
			}
			if($count == 0)$tabs_c .= '<li><a href="../ad/programas">No hay <b>'.$alias[$u['id']]['programa'].'</b> disponibles, haga clic aqu&iacute; para agregar <b>'.$programa_name.'</b></a></li>';
			$tabs_c .= '</ul></div>'; //Cierra lista de programas
			$tabs_c .= "</div>"; //Cierra contenedor de tab
		}
		$r = '<div id="'.$id.'"><ul>'.$tabs.'</ul>'.$tabs_c.'</div>';
		return $r;
	}
	function INIT_SITE_WRAPPER($buttons, $titulo)
	{
		$r = '<div id="content_header">';
		$r .= '<h1>'.$titulo.'</h1>
			<div id="tools">'. $buttons .
			'</div></div>
			<!-- Contenido -->
			<div id="wrapper"><!--  Encabezado de vista   -->';

		return $r;
	}
	function COMBO_PROGRAMAS($unid,$obj, $campo, $id, $id_select="",  $lbl="", $largo=20, $options="")
	{
		$r = "";
		if($lbl != ""){
			$r .= "<label for='$id' style='width:".($largo-1)."px;' class='label'>". $lbl."</label>";
		}
		$r .= "<select id='$id' name='$id' style='width:".($largo+70)."px; margin-top: 5px;'>";
		$r .= '<option value="todo"  selected="selected" > Todos </option>';
		foreach ($obj as $c=>$v){
		 if ($v->unid->id==$unid){
		 	if(is_object($v)){
				$r .= '<option value="'. $v->id . '"';
				if($v->id == $id_select) $r .= " selected='selected'";

				$campos = split(',',$campo);
				$r .= '>';
				foreach ($campos as $c_c => $c_v){
					if(is_object($v->$c_v)){
						$r .= $v->$c_v->get_html() . ' ';
					}else{
						$r .= $v->$c_v . ' ';
					}
				}
				$r .= '</option>';
			}else{
				$r .= '<option value="'. $c . '"';
				if($c == $id_select) $r .= " selected='selected'";
				$r .= '>' . $v . '</option>';
			}
		 }

		}
		$r .= "</select>";
		return $r;
	}
	function LISTA_EDITABLE_FOOTER_B($obj_page)
	{
		$pages = $obj_page;
		$r = '<div class="tile_menu">
			<p style="text-align: right; float:right;"><label for="filtro_lista">Buscar en lista:
			</label><input type="text" id="filtro_lista" class="input"></p>
			<table> <tr >';
		if(isset($pages) && $pages->pages != ""){
			$number = $pages->page - 2;
			$cant = 10;
			$final = $pages->pages;
			if($number < 1)
				$number = 1;
				$final = $number + 4;
				$r .= '<th width="30"><INPUT TYPE="button" id="page0" value="|<" class="item_button" ></th>';
				for($i=$number; $i<=$final; $i++){
					if($i > $pages->pages){
						break;
					}
					if($i == $pages->page){
						$r .= '<th width="30"><INPUT TYPE="button" id="page'. $i .'" class="item_selected_button" value="'. $i .'" ></th>';
					}else{
						$r .= '<th width="30"><INPUT TYPE="button" id="page'. $i .'" value="'. $i .'" class="item_button"></th>';
				}
			}
			$r .= '<th width="30"><INPUT TYPE="button" id="page'. $pages->pages .'" value=">|" class="item_button" ></th></tr></table>';

		}



		return $r;
	}
	function LISTA_EDITABLE_NUEVA($id, $data, $columnas=null, $editable=true, $path="", $options=null)
	{
		global $f;
		$num_reg = 10;
		$pk = 'id';

		if($options != null && isset($options['pk'])){
			$pk = $options['pk'];
		}

		// Generar cabeceras
		$s = "<div class='listaCabeceras'>";
		if($columnas != null){
			foreach($columnas as $c => $v){
				if(!isset($v['visible']) || $v['visible']){
					$s .= '<span style="width:' . ($v['w']) . 'px;" >' . $v['lbl'] . '</span>';
				}
			}
		}
		$s .= '</div><div class="list_block" id="'.$id.'">';
		// Generar formulario de nuevos registros
		$s .= "<span class='dummy_nuevo' style='display:none;'><span class='controles'><a href='javascript:void(0);' id='editar_item' class='tool_editar'></a><a href='javascript:void(0);' id='eliminar_item' class='tool_eliminar'></a></span><form class='campos'>";
		$s .= $this->CHECK("","check_id",0,false,"disabled='disnabled'");
			if($columnas != null){
				foreach($columnas as $c => $v){
					if(isset($v['send']) && !$v['send']) $c = "";
					if(isset($v['visible']) && !$v['visible']){
						$s .= $this->HIDDEN(0, $c) . "\n ";
					}else{
						if(isset($v['obj']) && $v['obj'] != ""){
							$s .= $this->COMBO_HIDDEN($v['lista'], $v['obj'], $c,"" ,"" , $v['w']);
							//$s .= $this->INPUT_HIDDEN('', $c ,$v['w'],"title=\"".$v['lbl'] . "\"") . "\n ";
						}else{
							if(!isset($v['valid'])){	$v['valid'] = ""; }
							if(isset($v['tipo'])){
								switch ($v['tipo']){
									case "fecha":
										$s .= $this->INPUT_DATEPICKER("", $c ,$v['w']) . "\n ";
										break;
								}
							}else{
								$s .= $this->INPUT_HIDDEN("", $c ,$v['w'],"",array("valid"=>$v['valid'])) . "\n ";

							}
						}
					}
				}
			}
		$s .= "</form>\n";
		$s .= "</span>";
		$s .= $this->HIDDEN($f->config->url->base . "/index.php/" . $path, 'path_url');
		$s .= '<ul id="lista">';

		//Recorrer array
		foreach ($data as $i => $r) {
			$s .= "<li id='item_".$r->$pk."'>";

			$s .= "<span class='controles'>";
			if($options != null && $options['quickedit']){
				$s .= "<a href='javascript:void(0);' id='largeedit_item' class='tool_editar' title='Editar'></a><a href='javascript:void(0);' id='quickedit_item' class='tool_quickedit' rel='large_edit' title='Edici&oacute;n r&aacute;pida'></a>";
			}else{
				$s .= "<a href='javascript:void(0);' id='editar_item' class='tool_editar' title='Editar'></a>";
			}
			if($editable){
				$s .= "<a href='javascript:void(0);' id='eliminar_item' class='tool_eliminar' title='Eliminar item'></a>";
			}
			$s .= "</span><form method='POST' action='index.php' class='campos'><input type='submit'  style='display:none' />";

			$s .= $this->CHECK("", "check_id", $r->$pk);
			if($columnas == null){
				foreach($r as $c => $v){
					if($c == "id"){
						$s .= $this->INPUT($v, $c ,20) . " \n";
					}else{

						$s .= $this->INPUT($v, $c ,100) . " \n";
					}
				}
			}else{
				foreach($columnas as $c => $v){
					if(isset($v['visible']) && !$v['visible']){
						$s .= $this->HIDDEN($r->$c, $c) . "\n ";
					}else if(!$v['edit']){
						if(isset($v['obj']) && $v['obj'] != ""){
							$s .= $this->LABEL(utf8_encode($r->$c->$v['obj']),"",$v['w']);
						}else{
							$s .= $this->LABEL($r->$c,"",$v['w']);
						}
					}else{
						if(!isset($v['valid'])){	$v['valid'] = ""; }
						if(isset($v['obj']) && $v['obj'] != ""){
							if($v['obj'] != 'array'){
								if(isset($r->$c->id)){
									$s .= $this->COMBO_HIDDEN($v['lista'], utf8_encode($v['obj']), $c, $r->$c->id, $r->$c->$v['obj'], $v['w']);
								}else{
									$s .= $this->COMBO_HIDDEN($v['lista'], $v['obj'], $c, $r->$c, $r->$c, $v['w']);
								}
							}else{
								//echo '<b>Clave: ' . $c . " Select: " . $r->$c->get() . '</b><br>';
								$s .= $this->COMBO_HIDDEN($v['lista'], utf8_encode($v['obj']), $c, $r->$c->get(), $r->$c->get_value(), $v['w']);
							}
							//$s .= $this->INPUT_HIDDEN($r->$c->$v['obj'], "hide_" + $c ,$v['w'],"title=\"".$v['lbl'] . "\"") . "\n ";
						}else{
							if(isset($v['tipo'])){
								switch ($v['tipo']){
									case 'fecha':
										$s .= $this->INPUT_DATEPICKER($r->$c, $c ,$v['w']) . "\n ";
										//$s .= $this->INPUT_HIDDEN($r->$c, $c ,$v['w'],"title=\"".$v['lbl'] . "\"") . "\n ";
										break;
									case 'label':
										$s .= $this->LABEL($r->$c,"",$v['w']);
								}
							}else{
								if(!isset($v['func'])){
									$fun = "get_html";
								}else{
									$fun = $v['func'];
								}
								$s .= $this->INPUT_HIDDEN($r->$c->$fun(), $c ,$v['w'],'title="'.$v['lbl'] . '"',array("valid"=>$v['valid']));
							}
						}
					}
				}
			}
			$s .= "</form></li>";
		}

		if(count($data) == 0){
			$s .= "<li class='last'>No se encontraron registros</li>";
		}

		$s .= '</ul></div>';
		return $s;
	}
	function LISTA_EDITABLE_MOD($id, $data, $columnas=null, $editable=true, $path="", $options=null)
	{
		global $f;
		$num_reg = 10;
		$pk = 'id';

		if($options != null && isset($options['pk'])){
			$pk = $options['pk'];
		}

		// Generar cabeceras
		$s = "<div class='listaCabeceras'>";
		if($columnas != null){
			foreach($columnas as $c => $v){
				if(!isset($v['visible']) || $v['visible']){
					$s .= '<span style="width:' . ($v['w']) . 'px;" >' . $v['lbl'] . '</span>';
				}
			}
		}
		$s .= '</div><div class="list_block" id="'.$id.'">';
		// Generar formulario de nuevos registros
		$s .= "<span class='dummy_nuevo' style='display:none;'><span class='controles'><a href='javascript:void(0);' id='editar_item' class='tool_editar'></a><a href='javascript:void(0);' id='eliminar_item' class='tool_eliminar'></a></span><form class='campos'>";
		$s .= $this->CHECK("","check_id",0,false,"disabled='disnabled'");
			if($columnas != null){
				foreach($columnas as $c => $v){
					if(isset($v['send']) && !$v['send']) $c = "";
					if(isset($v['visible']) && !$v['visible']){
						$s .= $this->HIDDEN(0, $c) . "\n ";
					}else{
						if(isset($v['obj']) && $v['obj'] != ""){
							$s .= $this->COMBO_HIDDEN($v['lista'], $v['obj'], $c,"" ,"" , $v['w']);
							//$s .= $this->INPUT_HIDDEN('', $c ,$v['w'],"title=\"".$v['lbl'] . "\"") . "\n ";
						}else{
							if(!isset($v['valid'])){	$v['valid'] = ""; }
							if(isset($v['tipo'])){
								switch ($v['tipo']){
									case "fecha":
										$s .= $this->INPUT_DATEPICKER("", $c ,$v['w']) . "\n ";
										break;
								}
							}else{
								$s .= $this->INPUT_HIDDEN("", $c ,$v['w'],"",array("valid"=>$v['valid'])) . "\n ";

							}
						}
					}
				}
			}
		$s .= "</form>\n";
		$s .= "</span>";
		$s .= $this->HIDDEN($f->config->url->base . "/index.php/" . $path, 'path_url');
		$s .= '<ul id="lista">';

		//Recorrer array
		foreach ($data as $i => $r) {
		if(isset($r['id'])){
			$s .= "<li id='item_".$r['id']."'>";

			$s .= "<span class='controles'>";
			if($options != null && $options['quickedit']){
		//		$s .= "<a href='javascript:void(0);' id='largeedit_item' class='tool_editar' title='Editar'></a><a href='javascript:void(0);' id='quickedit_item' class='tool_quickedit' rel='large_edit' title='Edici&oacute;n r&aacute;pida'></a>";
			}else{
		//		$s .= "<a href='javascript:void(0);' id='editar_item' class='tool_editar' title='Editar'></a>";
			}
			if($editable){
		//		$s .= "<a href='javascript:void(0);' id='eliminar_item' class='tool_eliminar' title='Eliminar item'></a>";
			}
			$s .= "</span><form method='POST' action='index.php' class='campos'><input type='submit'  style='display:none' />";

			$s .= $this->CHECK("", "check_id", $r['id']);
			if($columnas == null){
				foreach($r as $c => $v){
					if($c == "id"){
						$s .= $this->INPUT($v, $c ,20) . " \n";
					}else{

						$s .= $this->INPUT($v, $c ,100) . " \n";
					}
				}
			}else{
				foreach($columnas as $c => $v){
					//print_r($r);die();
					if(isset($v['visible']) && !$v['visible']){
						$s .= $this->HIDDEN($r[$c], $c) . "\n ";
					}else if(!$v['edit']){
						if(isset($v['obj']) && $v['obj'] != ""){
							$s .= $this->LABEL(utf8_encode($r->$c->$v['obj']),"",$v['w']);
						}else{
							$s .= $this->LABEL(utf8_encode($r[$c]),"",$v['w']);
						}
					}else{
						if(!isset($v['valid'])){	$v['valid'] = ""; }
						if(isset($v['obj']) && $v['obj'] != ""){
							if($v['obj'] != 'array'){
								if(isset($r->$c->id)){
									$s .= $this->COMBO_HIDDEN($v['lista'], utf8_encode($v['obj']), $c, $r->$c->id, $r->$c->$v['obj'], $v['w']);
								}else{
									$s .= $this->COMBO_HIDDEN($v['lista'], $v['obj'], $c, $r->$c, $r->$c, $v['w']);
								}
							}else{
								//echo '<b>Clave: ' . $c . " Select: " . $r->$c->get() . '</b><br>';
								$s .= $this->COMBO_HIDDEN($v['lista'], utf8_encode($v['obj']), $c, $r->$c->get(), $r->$c->get_value(), $v['w']);
							}
							//$s .= $this->INPUT_HIDDEN($r->$c->$v['obj'], "hide_" + $c ,$v['w'],"title=\"".$v['lbl'] . "\"") . "\n ";
						}else{
							if(isset($v['tipo'])){
								switch ($v['tipo']){
									case 'fecha':
										$s .= $this->INPUT_DATEPICKER($r->$c, $c ,$v['w']) . "\n ";
										//$s .= $this->INPUT_HIDDEN($r->$c, $c ,$v['w'],"title=\"".$v['lbl'] . "\"") . "\n ";
										break;
									case 'label':
										$s .= $this->LABEL($r->$c,"",$v['w']);
								}
							}else{
								if(!isset($v['func'])){
									$fun = "get_html";
								}else{
									$fun = $v['func'];
								}
								$s .= $this->INPUT_HIDDEN($r[$c], $c ,$v['w'],'title="'.$v['lbl'] . '"',array("valid"=>$v['valid']));
							}
						}
					}
				}
			}
			$s .= "</form></li>";
		}
		}
		if(count($data) == 0){
			$s .= "<li class='last'>No se encontraron registros</li>";
		}

		$s .= '</ul></div>';
		return $s;
	}
	function COMBO_CURRICULA($obj, $campo, $id, $id_select="",  $lbl="", $largo=20, $options="",$general="")
	{
		$r = "";
		if($lbl != ""){
			$r .= "<label for='$id' style='width:".($largo-1)."px;' class='label'>". $lbl."</label>";
		}
		$r .= "<select id='$id' name='$id' style='width:".($largo+9)."px;'  ";
		if($general != ""){
			$r .= '<option value="todo"  selected="selected" > Todos </option>';
		}
		foreach ($obj as $c=>$v){
			if(is_object($v)){
				$r .= '<option value="'. $v->fecest . '"';
				if($v->id == $id_select) $r .= " selected='selected'";
				$r .= '>';
				if(is_object($v->fecest)){
					$r .= utf8_encode($v->fecest->get()) . ' ';
				}
				$r .= '</option>';
			} 
		}
		$r .= "</select>";
		return $r;
	}
	function TABS_GRUPO($id, $obj_tabs, $obj_lista, $titulo, $mostrar=true)
	{
		$tabs_c = "";
		$tabs = "";
		$alias = array();
		$alias['C'] = array('nombre'=>'CET.',	'programa'=>'Programas');
		$alias['I'] = array('nombre'=>'Ins.',	'programa'=>'Carreras');
		$alias['X'] = array('nombre'=>'Ext.',	'programa'=>'Programas');

		foreach($obj_tabs as $u){
			$tabs .= "<li><a href='#".$u['id']."' title='".$u['label']."'>" . $u['label'] . "</a></li>";
			$tabs_c .= "<div id='". $u['id'] ."'><h2>".$titulo."</h2>";
			$tabs_c .= '<div id="lista_programas_'. $u['id'] .'" class="lista_menu"><ul>';
			$count = 0;
			foreach($obj_lista as $kp => $vp){
				$count++;
				
				if(eregi($u['id'],$vp['group'])){
					$tabs_c .= '<li><a href="#step'.$vp['id']->get().'" id="'.$vp['id']->get().'"><span style="float:left;width:20px;" class="flag_hide">'.$vp['id']->get().'</span>'.$vp['label']. '</a></li>';
				}else{
					//$tabs_c .= '<li class="disabled"><span style="float:left;width:20px;" class="flag_hide">'.$count.'</span>'.$vp['label']. '</li>';
				}
			}
			if($count == 0)$tabs_c .= '<li><a href="../ad/programas">No hay <b>'.$alias[$u['id']]['programa'].'</b> disponibles, haga clic aqu&iacute; para agregar <b>'.$programa_name.'</b></a></li>';
			$tabs_c .= '</ul></div>'; //Cierra lista de programas
			$tabs_c .= "</div>"; //Cierra contenedor de tab
		}
		$r = '<div id="'.$id.'"><ul>'.$tabs.'</ul>'.$tabs_c.'</div>';
		return $r;
	}
}
?>