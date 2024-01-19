<?php
class helper {
	function orderMultiDimensionalArray ($toOrderArray, $field, $inverse = false) {
	    $position = array();
	    $newRow = array();
	    foreach ($toOrderArray as $key => $row) {
	            $position[$key]  = $row[$field];
	            $newRow[$key] = $row;
	    }
	    if ($inverse) {
	        arsort($position);
	    }
	    else {
	        asort($position);
	    }
	    $returnArray = array();
	    foreach ($position as $key => $pos) {
	        $returnArray[] = $newRow[$key];
	    }
	    return $returnArray;
	}
	function paramsSearch($criterio,$campos,$operador='$or'){
		$expreg=$this->expreReg($criterio);
		$params[$operador]=array();
		foreach ($campos as $campo) {
			array_push($params[$operador], array($campo=>new MongoRegex($expreg['text'])));
		}
		return $params;
	}
	function filtrar($criterio,$data,$campos){
		$expreg=$this->expreReg($criterio);
		$text = '';//print_r($data);die();
		foreach ($campos as $campo) {
			$text.=$data[$campo].' ';
		}
		preg_match_all($expreg['text'],$text , $resp);
		if($expreg['num']==count($resp[0])){
			return TRUE;
		}
		else{
			return FALSE;
		}
	}
	function expreReg($criterio){
		$text='';
		/*$palabras = explode(' ', $criterio);
		$tot_pal=0;
		foreach ($palabras as $palabra) {
			if($palabra!=''){
				$text.=$palabra.'|';
				$tot_pal++;
			}
		}
		$text=substr($text,0,-1);*/
		$text = $criterio;
		$tot_pal = 1;
		$expreg['text']='/('.$text.')/i';
		$expreg['num']=$tot_pal;
		
		return $expreg;
	}
	function paramsSearchDetails($criterio,$campos,$operador='$or'){
		$expreg=$this->expreRegDetails($criterio);
		$params[$operador]=array();
		foreach ($campos as $campo) {
			array_push($params[$operador], array($campo=>new MongoRegex($expreg['text'])));
		}
		return $params;
	}
	function filtrarDetails($criterio,$data,$campos){
		$expreg=$this->expreRegDetails($criterio);
		$text = '';//print_r($data);die();
		foreach ($campos as $campo) {
			$text.=$data[$campo].' ';
		}
		preg_match_all($expreg['text'],$text , $resp);
		if($expreg['num']==count($resp[0])){
			return TRUE;
		}
		else{
			return FALSE;
		}
	}
	function expreRegDetails($criterio){
		$text='';
		$palabras = explode(' ', $criterio);
		$tot_pal=0;
		foreach ($palabras as $palabra) {
			if($palabra!=''){
				$text.=$palabra.'|';
				$tot_pal++;
			}
		}
		$text=substr($text,0,-1);
		$expreg['text']='/('.$text.')/i';
		$expreg['num']=$tot_pal;
		
		return $expreg;
	}
	/*
	'dataY'  = Data Vertical,
	'labelY' = Etiqueta Vertical,
	'dataX'  = Data Horizontal,
	'labelX' = Etiqueta Horizontal,
	'title'  = Título de gráfico,
	'path'   = Ruta de la imagen
	 * */
	function printGraphBar($data){
		global $f;
		$f->library("graphics");
		// We need some data
		$datay=$data['dataY'];
		$legend = $data['dataX'];
		
		// Set up the graph
		$graph = new Graph(800,400,"auto");
		$graph->img->SetMargin(60,30,30,40);
		$graph->graph_theme = null;
		$graph->SetScale("textlin");
		$graph->SetShadow();
		
		// Create the bar pot
		$bplot = new BarPlot($datay);
		$bplot->SetWidth(0.6);
		
		if(!isset($data['angle']))
			$data['angle'] = 45;
		// Set up color for gradient fill style
		$bplot->SetFillColor("orange");
		$bplot->value->Show();
		$bplot->value->SetFont(FF_ARIAL,FS_BOLD,10);
		$bplot->value->SetAngle($data['angle']);
		$bplot->value->SetFormat('%0.1f');
		$graph->Add($bplot);
		
		// Set up the title for the graph
		$graph->title->Set($data['title']);
		$graph->title->SetFont(FF_VERDANA,FS_BOLD,12);
		
		// Set up font for axis
		$graph->yaxis->SetFont(FF_VERDANA,FS_NORMAL,10);
		$graph->yaxis->title->Set($data['labelY']);
		$graph->yaxis->title->SetFont(FF_VERDANA,FS_NORMAL,10);
		
		// Set up X-axis title (color &amp; font)
		$graph->xaxis->SetFont(FF_VERDANA,FS_NORMAL,10);
		$graph->xaxis->title->Set($data['labelX']);
		$graph->xaxis->title->SetFont(FF_VERDANA,FS_NORMAL,10);
		$graph->xaxis->SetTickLabels($legend);
		//$graph->xaxis->SetLabelAlign('right','center','right');
		
		// Finally send the graph to the browser
		$graph->Stroke(_IMG_HANDLER);
		$graph->img->Stream($data['path']);
	}
	function printGraphBarOrga($data){
		global $f;
		$f->library("graphics");
		// We need some data
		$datay=$data['dataY'];
		$legend = $data['dataX'];
		
		// Set up the graph
		$graph = new Graph(800,700,"auto");
		$graph->img->SetMargin(60,30,30,450);
		$graph->graph_theme = null;
		$graph->SetScale("textlin");
		$graph->SetShadow();
		
		// Create the bar pot
		$bplot = new BarPlot($datay);
		$bplot->SetWidth(0.6);
		
		// Set up color for gradient fill style
		$bplot->SetFillColor("orange");
		$bplot->value->Show();
		$bplot->value->SetFont(FF_ARIAL,FS_BOLD,10);
		$bplot->value->SetAngle(0);
		$bplot->value->SetFormat('%0.1f');
		$graph->Add($bplot);
		
		// Set up the title for the graph
		$graph->title->Set($data['title']);
		$graph->title->SetFont(FF_VERDANA,FS_BOLD,12);
		
		// Set up font for axis
		$graph->yaxis->SetFont(FF_VERDANA,FS_NORMAL,10);
		$graph->yaxis->title->Set($data['labelY']);
		$graph->yaxis->title->SetFont(FF_VERDANA,FS_NORMAL,10);
		
		// Set up X-axis title (color &amp; font)
		$graph->xaxis->SetFont(FF_VERDANA,FS_NORMAL,6);
		/*$graph->xaxis->title->Set($data['labelX']);
		$graph->xaxis->title->SetFont(FF_VERDANA,FS_NORMAL,8);*/
		$graph->xaxis->SetTickLabels($legend);
		//$graph->xaxis->SetLabelAlign('right','center','right');
		
		$graph->xaxis->SetLabelAngle(90);
		
		// Finally send the graph to the browser
		$graph->Stroke(_IMG_HANDLER);
		$graph->img->Stream($data['path']);
	}
	function printGraphPie($data){
		global $f;
		$f->library("graphics");
		$info=$data['dataY'];
		// Create the Pie Graph.
		$graph = new PieGraph(550,450);
		$graph->graph_theme = null;
		$graph->SetShadow();
		// Set A title for the plot
		$graph->title->Set($data['title']);
		$graph->SetBox(true);
		// Create
		$p1 = new PiePlot($info);
		$graph->Add($p1);
		$p1->ShowBorder();
		$p1->SetColor('black');
		if(isset($data['color'])) $p1->SetSliceColors($data['color']);
		//$graph->legend->SetPos(0.5,0.98,'center','bottom');
		$p1->SetLegends($data['legend']);
		$graph->Stroke(_IMG_HANDLER);
		$graph->img->Stream($data['path']);
	}
	function printGraphPieNotPercent($data){
		global $f;
		$f->library("graphics");
		$info=$data['dataY'];
		// Create the Pie Graph.
		$graph = new PieGraph(550,450);
		$graph->graph_theme = null;
		$graph->SetShadow();
		// Set A title for the plot
		$graph->title->Set($data['title']);
		$graph->SetBox(true);
		// Create
		$p1 = new PiePlot($info);
		$graph->Add($p1);
		$p1->ShowBorder();
		$p1->SetColor('black');
		if(isset($data['color'])) $p1->SetSliceColors($data['color']);
		//$graph->legend->SetPos(0.5,0.98,'center','bottom');
		$p1->SetLegends($data['legend']);
		$p1->SetLabelType(PIE_VALUE_ABS);
		$p1->value->SetFormat('%d expedientes');
		$p1->value->Show(); 
		$graph->Stroke(_IMG_HANDLER);
		$graph->img->Stream($data['path']);
	}
	function getUser(){
		global $f;
		$user = array(
			'_id'=>$f->session->enti['_id'],
			'tipo_enti'=>$f->session->enti['tipo_enti'],
			'nomb'=>$f->session->enti['nomb']
		);
		if(isset($f->session->enti['appat']))
			$user['appat'] = $f->session->enti['appat'];
		if(isset($f->session->enti['apmat']))
			$user['apmat'] = $f->session->enti['apmat'];
		if(!isset($f->session->enti['roles']['trabajador']['cargo']['_id'])){
			$user['cargo'] = array(
				'funcion'=>$f->session->enti['roles']['trabajador']['cargo']['funcion'],
				'organizacion'=>$f->session->enti['roles']['trabajador']['cargo']['organizacion']
			);
		}else if(isset($f->session->enti['roles']['trabajador'])){
			$user['cargo'] = array(
				'_id'=>$f->session->enti['roles']['trabajador']['cargo']['_id'],
				'nomb'=>$f->session->enti['roles']['trabajador']['cargo']['nomb'],
				'organizacion'=>$f->session->enti['roles']['trabajador']['cargo']['organizacion']
			);
		}
		return $user;
	}
	function getUserMin(){
		global $f;
		$user = array(
			'_id'=>$f->session->enti['_id'],
			'tipo_enti'=>$f->session->enti['tipo_enti'],
			'nomb'=>$f->session->enti['nomb']
		);
		if(isset($f->session->enti['appat']))
			$user['appat'] = $f->session->enti['appat'];
		if(isset($f->session->enti['apmat']))
			$user['apmat'] = $f->session->enti['apmat'];
		return $user;
	}
	function getEntiDbRel($data){
		global $f;
		$user = array(
			'_id'=>$data['_id'],
			'tipo_enti'=>$data['tipo_enti'],
			'nomb'=>$data['nomb']
		);
		if(isset($data['appat']))
			$user['appat'] = $data['appat'];
		if(isset($data['apmat']))
			$user['apmat'] = $data['apmat'];
		if(isset($data['roles']['trabajador']['cargo']['funcion'])){
			$user['cargo'] = array(
				'funcion'=>$data['roles']['trabajador']['cargo']['funcion'],
				//'organizacion'=>$data['roles']['trabajador']['cargo']['organizacion']
			);
			if(isset($data['roles']['trabajador']['cargo']['organizacion'])){
				$user['cargo']['organizacion'] = $data['roles']['trabajador']['cargo']['organizacion'];
			}
		}else if(isset($data['roles']['trabajador'])){
			if(isset($data['roles']['trabajador']['cargo'])){
				$user['cargo'] = array(
					'_id'=>$data['roles']['trabajador']['cargo']['_id'],
					'nomb'=>$data['roles']['trabajador']['cargo']['nomb'],
					//'organizacion'=>$data['roles']['trabajador']['cargo']['organizacion']
				);
				if(isset($data['roles']['trabajador']['cargo']['organizacion'])){
					$user['cargo']['organizacion'] = $data['roles']['trabajador']['cargo']['organizacion'];
				}
			}
		}
		if(isset($data['roles']['trabajador'])){
			if(isset($data['roles']['trabajador']['programa'])){
				$user['programa'] = $data['roles']['trabajador']['programa'];
			}
		}
		return $user;
	}
	function replace_acc($str){
		$conv = array(
			"&aacute;"=>"á",
			"&Aacute;"=>"Á",
			"&eacute;"=>"é",
			"&Eacute;"=>"É",
			"&iacute;"=>"í",
			"&Iacute;"=>"Í",
			"&oacute;"=>"ó",
			"&Oacute;"=>"Ó",
			"&uacute;"=>"ú",
			"&Uacute;"=>"Ú",
			"<b>"=>"",
			"</b>"=>"",
			"&deg;"=>"º"
		);
		return strtr($str, $conv);	
	}
	function format_word($palabra){
		$search = array("S.a.","S.a","S.a.c.","S.a.c","S.A.c","S.A.c.","Sac","E.i.r.l.","E.i.r.l","Eirl","S.r.l.","S.r.l","Srl","Cci","Gsf");
		$replace = array("S.A.","S.A","S.A.C.","S.A.C.","S.A.C.","S.A.C.","SAC","E.I.R.L","E.I.R.L.","EIRL","S.R.L.","S.R.L","SRL","CCI","GSF");

		$palabra = utf8_encode(ucwords(strtolower(utf8_decode($palabra))));
		$palabra = str_replace($search, $replace, $palabra);
		$palabra = trim($palabra);
		if(substr($palabra, count($palabra)-3,1)=="-"){
			$last_part = strtoupper(substr($palabra, count($palabra)-2,1));
			$palabra = substr($palabra, 0, count($palabra)-2).$last_part;
		}
		//$palabra.="    ".substr($palabra, count($palabra)-3,1);
		return $palabra;
	}
	function get_string_between($string, $start, $end){
	    $string = ' ' . $string;
	    $ini = strpos($string, $start);
	    if ($ini == 0) return '';
	    $ini += strlen($start);
	    $len = strpos($string, $end, $ini) - $ini;
	    return substr($string, $ini, $len);
	}
}
?>