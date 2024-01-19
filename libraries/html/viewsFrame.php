<?php
class viewsFrame {
		
	//Botones
	function BUTTON_LINK ($label, $id, $imgstyle='', $link="", $ancho=0){
		$boton = 'class="toolButton"';$r = '';
		if($link == "") $link = 'javascript:void(0)';
		if($label != ""){$r = '<a href="'.$link.'" '.$boton.' id="'.$id.'" title="'.strip_tags($label).'" >';}
		if($imgstyle != ''){$r .= '<span class="'.$imgstyle.'"></span>';}
		if($label != ""){ $r .= $label . '</a>';}
		return $r;
	}
	function MENU_BUTTON ($label, $id, $imgstyle='', $link="", $ancho=0)
	{
		$boton = '';
		$r = '';
		if($link == "") $link = 'javascript:void(0)';
		if($label != ""){ $boton = 'class="toolButton"';
		$r='<button id="'.$id.'" '.$boton.'  VALUE="'.strip_tags($label).'">';}
		if($imgstyle != ''){
			$r .= '<span class="'.$imgstyle.'"></span>';
		}
		if($label != ""){ $boton = 'class="toolButton"';
		$r .= $label . '</button>';}
		return $r;
	}
	function BUTTON ($label, $id, $imgstyle='', $link="",$clase="toolButton_s", $ancho=10){
		$boton = 'class="'.$clase.'"';
		$r = '';
		if($link == "") $link = 'javascript:void(0)';
		if($label != ""){$r='<button id="'.$id.'" '.$boton.'   value="'.strip_tags($label).'" style="width:'.$ancho.'px;">';}
		if($imgstyle != ''){$r .= '<span class="'.$imgstyle.'"></span>';}
		if($label != ""){$r .= utf8_decode($label) . '</button>';}
		return $r;
	}
	//campos de ingreso de datos 
	function INPUT($value, $id,$type='T', $largo=10,$options="",$class='input'){
		if ($type=='T')$tipo='text';elseif ($type=='P')$tipo='password';elseif ($type=='H')$tipo='hidden';
		$r = "<input type='$tipo' class='$class' value='".utf8_encode($value)."' id='$id' name='$id' style='width:".$largo."px;'$options'/>";
		return $r;
	}
	function INPUT_HIDDEN ($value, $id, $largo=10,$clase="label_hide",$type='T',$options="",$params = null,$habil=''){
		$r = '';$v = '';
		if ($type=='T')$tipo='text';elseif ($type=='P')$tipo='password';
		if($params != null){
			if(isset($params['label'])){$r .= '<label class="'.$clase.'" for="'. $id .'">'.$params['label'].'</label>';}
			if(isset($params['valid'])){$v = $params['valid'];}
		}
		$r .= "<input type='$tipo' class='inputHide $v' readonly='readonly'".$habil." value='".utf8_encode($value)."' id='$id' name='$id' style='width:".$largo."px;' $options />";
		return $r;
	}
	function INPUT_HIDDEN_ID ($value, $id, $largo=10,$type='T',$options="",$params = null,$habil=''){
		$r = '';$v = '';
		if ($type=='T')$tipo='text';elseif ($type=='P')$tipo='password';
		if($params != null){
			if(isset($params['label'])){$r .= '<label class="label_hide" for="'. $id .'">'.$params['label'].'</label>';}
			if(isset($params['valid'])){$v = $params['valid'];}
		}
		$r .= "<input type='$tipo' class='inputHide_id' readonly='readonly'".$habil." value='$value' id='$id' name='$id' style='width:".$largo."px;' $options />";
		return $r;
	}
	//etiquetas
	function LABEL($lbl,$id,$largo=10,$options=""){
		if (is_numeric($largo))$largo=$largo.'px';
		$r = "<input type='text' class='inputHide' id='$id' name='$id' readonly='readonly' style='width:".$largo."' $options  value='".utf8_encode($lbl)."'>";
		return $r;
	}
	//lista desplegable COMBOBOX
	function COMBO($obj, $campo, $id, $id_select="", $lbl="", $largo=29,$general="", $options=""){
	$r = "";
		if (is_numeric($largo))$largo=$largo.'px';
		$lbl_largo=strlen($lbl);
		
		if($lbl != ""){$r .= "<label for='$id' style='width:".$lbl_largo."px;' class='label'>". $lbl."</label>";}
		$r .= "<select id='$id' name='$id' style='width:".$largo.";height:24px;' > ";
		if($general != ""){
			if ($general=='T')$r .= '<option value="todo"  selected="selected" >Todos</option>';
			else $r .= '<option value="vacio"  selected="selected" ></option>';
		}
		if ($obj!=''){
			foreach ($obj as $c=>$v){
				
				if(is_object($v)){
					$r .= '<option value="'. $v->id->get() . '"';
					if($v->id == $id_select) $r .= " selected='selected'";
					$campos = split(',',$campo);
					$r .= '>';
					foreach ($campos as $c_c => $c_v){
						if(is_object($v->$c_v)){
							if(is_numeric($v->$c_v->get())){ $r .= $v->$c_v->get(). ' ';}
							else{$r .= $v->$c_v->get_html() . ' ';}
						}
						else{$r .= $v->$c_v . ' ';}
					}
					$r .= '</option>';
				}
				elseif (is_array($v)) {
					$r .= '<option value="'. $v['id'] . '"';
					if($v['id'] == $id_select) $r .= " selected='selected'";
					$r .= '>';$r .= utf8_encode($v[$campo]);$r .= '</option>';
				}
				else{
					$r .= '<option value="'. $c . '"';
					if($c == $id_select) $r .= " selected='selected'";
					$r .= '>' .utf8_encode($v) . '</option>';
				}
			}
		}
		else{
			$r .= '<option value="vacio" selected="selected" ></option>';
		}
		$r .= "</select>";
		return $r;
	}
	function COMBO_HIDDEN ($obj, $campo, $id, $id_select="",  $lbl="", $largo=29, $options=""){
		$r = "<label for='$id' style='width:".($largo-2)."px;' class='label'>". utf8_encode($lbl)."</label> <select id='$id' name='$id' style='width:".($largo)."px; display:none;'>";
		$count=0;
		foreach ($obj as $c=>$v){
			if(is_object($v)){
				$r .= '<option value="'. $v->id . '"';//print_r($v->id);die();
				if($v->id == $id_select){ $r .= " selected='selected'"; $count=1;}
				$campos = split(',',$campo);
				$r .= '>';
				foreach ($campos as $c_c => $c_v){
					if(is_object($v->$c_v)){$r .= $v->$c_v->get_html() . ' ';}
					else{$r .= $v->$c_v . ' ';}
				}
				$r .= '</option>';
			}
			elseif (is_array($v)) {
				$r .= '<option value="'. $v['id'] . '"';
				if($v['id'] == $id_select) $r .= " selected='selected'";$count=1;;
				$r .= '>';$r .= utf8_encode($v[$campo]);$r .= '</option>';
			}
			else{
				$r .= '<option value="'. $c . '"';
				if($c == $id_select) {$r .= " selected='selected'";$count=1;}
				$r .= '>' . utf8_encode($v) . '</option>';
			}
		}
		if ($count==0){
			$r .= '<option value="vacio"';
			$r .= " selected='selected'";
			$r .= '>No Asignado</option>';
		}
	
		$r .= "</select>";
		return $r;
	}
	//CHECKBOX
	function CHECK ($label, $id, $value,  $selected=false, $options=""){
		$sel = '';
		if($selected){$sel = 'checked="checked"';}
		$r = "<label name='$id' id='$id'><input type='checkbox' class='input' value='$value' name='$id' id='$id' '$sel' $options/> $label</label>";
		return $r;
	}
	//TABS
	function TABS_MENU($id, $obj_titulo, $obj_lista,$vinculo_titulo='id',$vinculo_lista='group',$mostrar=true)	{
		$tabs_c = "";
		$tabs = "";
		
		foreach($obj_titulo as $i=> $u){
			$tabs .= "<li><a href='#lista_".$u['id']."' title='". $u['label']."'>" . $u['label']. "</a></li>";
			if(isset($u['titulo']) and $u['titulo']!='')$tabs_c .= "<div id='lista_". $u['id'] ."'><h2>".$u['titulo']."</h2>";
				else $tabs_c .= "<div id='lista_". $u['id'] ."'>";
			$tabs_c .= '<div id="'. $u['id'] .'" class="lista_menu"><ul>';
			$count = 0;
			
			foreach($obj_lista as $kp => $vp){
				
				if($vp['group'] == $u['id']){
					$tabs_c .= '<li><a href="#step'.$vp['id'].'" id="'.$vp['id'].'"><span style="float:left;width:20px;" class="flag_hide">'.$vp['id'].'</span>'.$vp['label']. '</a></li>';
					$count++;
				}
			}
			if($count == 0){$tabs_c .= "<li><a href='#'>No hay elementos disponibles </a></li>";}
			$tabs_c .= "</ul></div>"; //Cierra lista
			$tabs_c .= "</div>"; //Cierra contenedor de tab
		}

		$r = '<div id="'.$id.'"><ul>'.$tabs.'</ul>'.$tabs_c.'</div>';
		return $r;
	}
	//lista editable
	function LISTA_EDITABLE($id, $data, $columnas=null, $editable=true, $path="", $options=null)	{
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
						$s .= $this->INPUT(0, $c,'H') . "\n ";
					}else{
						if(isset($v['obj']) && $v['obj'] != ""){
							$s .= $this->COMBO_HIDDEN($v['lista'], $v['obj'], $c,"" ,"" , $v['w']);
							//$s .= $this->INPUT_HIDDEN('', $c ,$v['w'],"title=\"".$v['lbl'] . "\"") . "\n ";
						}else{
							if(!isset($v['valid'])){	$v['valid'] = ""; }
							if(isset($v['tipo'])){
								switch ($v['tipo']){
									case "fecha":
										$s .= $this->INPUT("", $c ,$v['w']) . "\n ";
										break;
								}
							}else{
								$s .= $this->INPUT("", $c ,$v['w'],'H',array("valid"=>$v['valid'])) . "\n ";

							}
						}
					}
				}
			}
		$s .= "</form>\n";
		$s .= "</span>";
		$s .= $this->INPUT($path, 'path_url','H');
		$s .= '<ul id="lista">';

		//Recorrer array
		foreach ($data as $i => $r) {
			
			$s .= "<li id='item_".$r[$pk]."'>";

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
						$s .= $this->INPUT($r->$c, $c,'H') . "\n ";
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
										$s .= $this->INPUT($r->$c, $c ,'T',$v['w']) . "\n ";
										//$s .= $this->INPUT_HIDDEN($r->$c, $c ,$v['w'],"title=\"".$v['lbl'] . "\"") . "\n ";
										break;
									case 'label':
										$s .= $this->LABEL($r->$c,"",$v['w']);
								}
							}else{
								if(!isset($v['func'])){
									$fun = $v['func'];
								}
								$s .= $this->INPUT_HIDDEN(utf8_encode($r[$c]), $c ,$v['w'],'title="'.$v['lbl'] . '"',array("valid"=>$v['valid']));
								
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
	function INIT_SITE_WRAPPER($buttons, $titulo)	{
		$r = '<div id="content_header">';
		$r .= '<h1>'.utf8_encode($titulo).'</h1>	<div id="tools">'.$buttons.'</div></div>
		<!-- Contenido -->
			<div id="wrapper"><!--  Encabezado de vista   -->';
		return $r;
	}
	function LISTA_DATOS($id, $data, $columnas=null, $editable=true, $path="", $options=null)	{
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
					$s .= '<span style="width:' . ($v['w']) . 'px;" >' . utf8_encode($v['lbl']) . '</span>';
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
			$s .= "<li id='item_".$r->$pk."' class='item_click'>";

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
								if ($c=='id'){
									$s .= $this->INPUT_HIDDEN_ID($r->$c->$fun(), $c ,$v['w'],'title="'.$v['lbl'] . '"',array("valid"=>$v['valid']));
								}else{
									$s .= $this->INPUT_HIDDEN($r->$c->$fun(), $c ,$v['w'],'title="'.$v['lbl'] . '"',array("valid"=>$v['valid']));
								}
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
	function HIDDEN ($value, $id){
		$r = "<input type='hidden' value='$value' id='$id' name='$id' />";
		return $r;
	}
	
}
?>