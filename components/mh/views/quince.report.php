<?php
global $f;
$f->library('pdf');

class repo extends FPDF
{
	function Header(){
		//$this->Image(IndexPath.DS.'templates/ts/comprobante.gif',10,10,190,275);
		
		$y=10;
		$this->SetFont('Arial','B',9);
		$this->SetXY(10,5);$this->MultiCell(60,5,"SOCIEDAD DE BENEFICENCIA PUBLICA DE AREQUIPA",'0','C');		
		$this->SetXY(10,5);$this->MultiCell(190,5,date("d/m/Y")." Página: ".$this->PageNo()."/{nb}",'0','R');
		//$this->SetXY(10,5);$this->MultiCell(190,5,$fecha." Página: ".$this->PageNo()."/{nb}",'0','R');
		$this->SetFont('Arial','',9);
		$this->SetXY(10,15);$this->MultiCell(60,5,"Centro de Salud Mental",'0','C');
		$this->SetXY(10,20);$this->MultiCell(60,5,"'Moises Heresi'",'0','C');	
		
		
	}



	function Publicar($diario){	
			

		$x=5;
		$y=25;
		$y_ini = $y;
		$page_b = 275;
		
//CABECERA
		$y=$y+25;
		$this->SetFont('Arial','B',10);
		$this->SetXY(5,30);$this->MultiCell(200,5,	"FICHA DE REPORTE MENSUAL DE SERVICIOS - SOCIEDAD DE BENEFICIENCIA DE AREQUIPA - REPORTE QUINCENAL DE SERVICIOS - SALUD MENTAL",'0','C');
		$this->SetFont('Arial','B',8);
		//PRIMERA COLUMNA
		$this->SetXY(20,$y);$this->MultiCell(42,9,"NUMERO DE BENEFCIARIOS",'1','C');
		$this->SetXY(20,59);$this->MultiCell(42,9,"APOYO EN SALUD",'1','C');
		$this->SetXY(20,68);$this->MultiCell(42,9,"BENEFICIARIO INDIGENTE",'1','C');
		$this->SetXY(20,77);$this->MultiCell(42,9,"BENEFICIARIO PAGANTE",'1','C');
		$this->SetXY(20,86);$this->MultiCell(42,9,"TOTAL",'1','C');
		//SEGUNDA COLUMNA
		$this->SetXY(62,$y);$this->MultiCell(22,9,"SEGUN SEXO",'1','C');
		$this->SetXY(62,59);$this->MultiCell(11,9,"M",'1','C');
		$this->SetXY(73,59);$this->MultiCell(11,9,"F",'1','C');
		//TERCERA COLUMNA
		$this->SetXY(84,$y);$this->MultiCell(12,9,"TOTAL",'1','C');
		$this->SetXY(84,59);$this->MultiCell(12,9,"-------",'1','C');
		//CUARTA COLUMNA
		$this->SetXY(96,$y);$this->MultiCell(80,9,"SEGUN SU EDAD",'1','C');
		$this->SetXY(96,59);$this->MultiCell(10,9,"0-5",'1','C');
		$this->SetXY(106,59);$this->MultiCell(10,9,"6-10",'1','C');
		$this->SetXY(116,59);$this->MultiCell(10,9,"11-15",'1','C');
		$this->SetXY(126,59);$this->MultiCell(10,9,"16-20",'1','C');
		$this->SetXY(136,59);$this->MultiCell(10,9,"21-30",'1','C');
		$this->SetXY(146,59);$this->MultiCell(10,9,"31-50",'1','C');
		$this->SetXY(156,59);$this->MultiCell(10,9,"51-60",'1','C');
		$this->SetXY(166,59);$this->MultiCell(10,9,"61-99",'1','C');

		//CUERPO
		$mujeres_pagante = 0;
		$varones_pagante = 0;
		$mujeres_indigente = 0;
		$varones_indigente = 0;
		$total_mujeres = 0;
		$total_varones = 0;
		$total = 0;
		$edad1_i=0;
		$edad2_i=0;
		$edad3_i=0;
		$edad4_i=0;
		$edad5_i=0;
		$edad6_i=0;
		$edad7_i=0;
		$edad8_i=0;

		$edad1_p=0;
		$edad2_p=0;
		$edad3_p=0;
		$edad4_p=0;
		$edad5_p=0;
		$edad6_p=0;
		$edad7_p=0;
		$edad8_p=0;

		$total_edad1=0;
		$total_edad2=0;
		$total_edad3=0;
		$total_edad4=0;
		$total_edad5=0;
		$total_edad6=0;
		$total_edad7=0;
		$total_edad8=0;
		//$diagnostico=0;
		$this->SetFont('Arial','',9);
		$y=$y+9;
		$yini= $y;
		for($i = 0;$i<count($diario);$i++){
			$fecha_parte = $diario[$i]['fech']->sec;
			if(isset($diario[$i]['consulta'])){
				for($j = 0;$j<count($diario[$i]['consulta']);$j++){
					if(isset($diario[$i]["consulta"][$j]['paciente']['fecha_na']['sec'])){
						$fecha_nacimiento = $diario[$i]["consulta"][$j]['paciente']['fecha_na']['sec'];
						$edad = $fecha_parte-$fecha_nacimiento;
						$edad = floor($edad/(60*60*24*365));
					}else{
						$edad = -1;
					}
	
					if(($diario[$i]["consulta"][$j] != null)){
						
						if(floatval($diario[$i]["consulta"][$j]['paciente']['sexo']) == 0 && floatval($diario[$i]["consulta"][$j]['cate']) != 8 && $diario[$i]['consulta'][$j]['esta'] != '5'  && $diario[$i]['modulo'] != 'AD'){
							$mujeres_pagante++;
						}
						if(floatval($diario[$i]["consulta"][$j]['paciente']['sexo']) == 1 && floatval($diario[$i]["consulta"][$j]['cate']) != 8 && $diario[$i]['consulta'][$j]['esta'] != '5'  && $diario[$i]['modulo'] != 'AD'){
							$varones_pagante++;
						}
						if(floatval($diario[$i]["consulta"][$j]['paciente']['sexo']) == 0 && floatval($diario[$i]["consulta"][$j]['cate']) == 8 && $diario[$i]['consulta'][$j]['esta'] != '5'  && $diario[$i]['modulo'] != 'AD'){
							$mujeres_indigente++;
						}
						if(floatval($diario[$i]["consulta"][$j]['paciente']['sexo']) == 1 && floatval($diario[$i]["consulta"][$j]['cate']) == 8 && $diario[$i]['consulta'][$j]['esta'] != '5'  && $diario[$i]['modulo'] != 'AD'){
							$varones_indigente++;
						}
						
						if($edad<6 && floatval($diario[$i]["consulta"][$j]['cate']) != 8 && $diario[$i]['consulta'][$j]['esta'] != '5'  && $diario[$i]['modulo'] != 'AD'){
							$edad1_p++;
						}
		
						if($edad>=6 && $edad<11 && floatval($diario[$i]["consulta"][$j]['cate']) != 8 && $diario[$i]['consulta'][$j]['esta'] != '5'  && $diario[$i]['modulo'] != 'AD'){
							$edad2_p++;
						}
		
						if($edad>=11 && $edad<16 && floatval($diario[$i]["consulta"][$j]['cate']) != 8 && $diario[$i]['consulta'][$j]['esta'] != '5'  && $diario[$i]['modulo'] != 'AD'){
							$edad3_p++;
						}
		
						if($edad>=16 && $edad<21 && floatval($diario[$i]["consulta"][$j]['cate']) != 8 && $diario[$i]['consulta'][$j]['esta'] != '5'  && $diario[$i]['modulo'] != 'AD'){
							$edad4_p++;
						}
						if($edad>=21 && $edad<31 && floatval($diario[$i]["consulta"][$j]['cate']) != 8 && $diario[$i]['consulta'][$j]['esta'] != '5'  && $diario[$i]['modulo'] != 'AD'){
							$edad5_p++;
						}
						if($edad>=31 && $edad<51 && floatval($diario[$i]["consulta"][$j]['cate']) != 8 && $diario[$i]['consulta'][$j]['esta'] != '5'  && $diario[$i]['modulo'] != 'AD'){
							$edad6_p++;
						}
						if($edad>=51 && $edad<61 && floatval($diario[$i]["consulta"][$j]['cate']) != 8 && $diario[$i]['consulta'][$j]['esta'] != '5'  && $diario[$i]['modulo'] != 'AD'){
							$edad7_p++;
						}
						if($edad>=61 && $edad<100 && floatval($diario[$i]["consulta"][$j]['cate']) != 8 && $diario[$i]['consulta'][$j]['esta'] != '5'  && $diario[$i]['modulo'] != 'AD'){
							$edad8_p++;
						}
		
		
						if($edad>=0 && $edad<6 && floatval($diario[$i]["consulta"][$j]['cate']) == 8 && $diario[$i]['consulta'][$j]['esta'] != '5'  && $diario[$i]['modulo'] != 'AD'){
							$edad1_i++;
						}
		
						if($edad>=6 && $edad<11 && floatval($diario[$i]["consulta"][$j]['cate']) == 8 && $diario[$i]['consulta'][$j]['esta'] != '5'  && $diario[$i]['modulo'] != 'AD'){
							$edad2_i++;
						}
		
						if($edad>=11 && $edad<16 && floatval($diario[$i]["consulta"][$j]['cate']) == 8 && $diario[$i]['consulta'][$j]['esta'] != '5'  && $diario[$i]['modulo'] != 'AD'){
							$edad3_i++;
						}
		
						if($edad>=16 && $edad<21 && floatval($diario[$i]["consulta"][$j]['cate']) == 8 && $diario[$i]['consulta'][$j]['esta'] != '5'  && $diario[$i]['modulo'] != 'AD'){
							$edad4_i++;
						}
						if($edad>=21 && $edad<31 && floatval($diario[$i]["consulta"][$j]['cate']) == 8 && $diario[$i]['consulta'][$j]['esta'] != '5'  && $diario[$i]['modulo'] != 'AD'){
							$edad5_i++;
						}
						if($edad>=31 && $edad<51 && floatval($diario[$i]["consulta"][$j]['cate']) == 8 && $diario[$i]['consulta'][$j]['esta'] != '5'  && $diario[$i]['modulo'] != 'AD'){
							$edad6_i++;
						}
						if($edad>=51 && $edad<61 && floatval($diario[$i]["consulta"][$j]['cate']) == 8 && $diario[$i]['consulta'][$j]['esta'] != '5'  && $diario[$i]['modulo'] != 'AD'){
							$edad7_i++;
						}
						if($edad>=61 && $edad<100 && floatval($diario[$i]["consulta"][$j]['cate']) == 8 && $diario[$i]['consulta'][$j]['esta'] != '5'  && $diario[$i]['modulo'] != 'AD'){
							$edad8_i++;
						}
					}
					
	
	
					/*if($diario[$i]['consulta'][$j]['cie10'] != 'F20' && $diario[$i]['consulta'][$j]['esta'] != '5'){
						$diagnostico++;
					}*/
	
	
				}
			}
			
		}


			$total_sexo_pagante = $mujeres_pagante + $varones_pagante;
			$total_sexo_indigente = $mujeres_indigente + $varones_indigente;
			$total_varones = $varones_indigente+$varones_pagante;
			$total_mujeres = $mujeres_indigente + $mujeres_pagante;
			$total = $total_mujeres + $total_varones;
			$total_edad1 = $edad1_p + $edad1_i;
			$total_edad2 = $edad2_p + $edad2_i;
			$total_edad3 = $edad3_p + $edad3_i;
			$total_edad4 = $edad4_p + $edad4_i;
			$total_edad5 = $edad5_p + $edad5_i;
			$total_edad6 = $edad6_p + $edad6_i;
			$total_edad7 = $edad7_p + $edad7_i;
			$total_edad8 = $edad8_p + $edad8_i;
			//print_r($diagnostico);
			
			//---------------------------SEGUN SEXO VARONES----------------------------------------\\
			$this->SetXY(62,68);$this->MultiCell(11,9,"".$varones_indigente,'1','C');
			$this->SetXY(62,77);$this->MultiCell(11,9,"".$varones_pagante,'1','C');
			$this->SetFont('Arial','B',9);
			$this->SetXY(62,86);$this->MultiCell(11,9,"".$total_varones,'1','C');
			$this->SetFont('Arial','',9);
			//-------------------------------------------------------------------\\
			$this->SetXY(96,68);$this->MultiCell(10,9,"".$edad1_i,'1','C');
			$this->SetXY(106,68);$this->MultiCell(10,9,"".$edad2_i,'1','C');
			$this->SetXY(116,68);$this->MultiCell(10,9,"".$edad3_i,'1','C');
			$this->SetXY(126,68);$this->MultiCell(10,9,"".$edad4_i,'1','C');
			$this->SetXY(136,68);$this->MultiCell(10,9,"".$edad5_i,'1','C');
			$this->SetXY(146,68);$this->MultiCell(10,9,"".$edad6_i,'1','C');
			$this->SetXY(156,68);$this->MultiCell(10,9,"".$edad7_i,'1','C');
			$this->SetXY(166,68);$this->MultiCell(10,9,"".$edad8_i,'1','C');
			//-------------------------------------------------------------------\\
			$this->SetXY(96,77);$this->MultiCell(10,9,"".$edad1_p,'1','C');
			$this->SetXY(106,77);$this->MultiCell(10,9,"".$edad2_p,'1','C');
			$this->SetXY(116,77);$this->MultiCell(10,9,"".$edad3_p,'1','C');
			$this->SetXY(126,77);$this->MultiCell(10,9,"".$edad4_p,'1','C');
			$this->SetXY(136,77);$this->MultiCell(10,9,"".$edad5_p,'1','C');
			$this->SetXY(146,77);$this->MultiCell(10,9,"".$edad6_p,'1','C');
			$this->SetXY(156,77);$this->MultiCell(10,9,"".$edad7_p,'1','C');
			$this->SetXY(166,77);$this->MultiCell(10,9,"".$edad8_p,'1','C');
			//-------------------------------------------------------------------\\
			$this->SetFont('Arial','B',9);
			$this->SetXY(96,86);$this->MultiCell(10,9,"".$total_edad1,'1','C');
			$this->SetXY(106,86);$this->MultiCell(10,9,"".$total_edad2,'1','C');
			$this->SetXY(116,86);$this->MultiCell(10,9,"".$total_edad3,'1','C');
			$this->SetXY(126,86);$this->MultiCell(10,9,"".$total_edad4,'1','C');
			$this->SetXY(136,86);$this->MultiCell(10,9,"".$total_edad5,'1','C');
			$this->SetXY(146,86);$this->MultiCell(10,9,"".$total_edad6,'1','C');
			$this->SetXY(156,86);$this->MultiCell(10,9,"".$total_edad7,'1','C');
			$this->SetXY(166,86);$this->MultiCell(10,9,"".$total_edad8,'1','C');
			$this->SetFont('Arial','',9);
			//-------------------------------------------------------------------\\
			$this->SetXY(73,68);$this->MultiCell(11,9,"".$mujeres_indigente,'1','C');
			$this->SetXY(73,77);$this->MultiCell(11,9,"".$mujeres_pagante,'1','C');
			$this->SetFont('Arial','B',9);
			$this->SetXY(73,86);$this->MultiCell(11,9,"".$total_mujeres,'1','C');
			//-------------------------------------------------------------------\\
			$this->SetXY(84,68);$this->MultiCell(12,9,"".$total_sexo_indigente,'1','C');
			$this->SetXY(84,77);$this->MultiCell(12,9,"".$total_sexo_pagante,'1','C');
			$this->SetXY(84,86);$this->MultiCell(12,9,"".$total,'1','C');
			//-------------------------------------------------------------------\\
		$this->AddPage();
			$x=5;
		$y=25;
		$y_ini = $y;
		$page_b = 275;
		
//CABECERA
		$y=$y+25;
		$this->SetFont('Arial','B',10);
		$this->SetXY(5,30);$this->MultiCell(200,5,	"FICHA DE REPORTE MENSUAL DE SERVICIOS - SOCIEDAD DE BENEFICIENCIA DE AREQUIPA - REPORTE QUINCENAL DE SERVICIOS - ADICCIONES",'0','C');
		$this->SetFont('Arial','B',8);
		//PRIMERA COLUMNA
		$this->SetXY(20,$y);$this->MultiCell(42,9,"NUMERO DE BENEFCIARIOS",'1','C');
		$this->SetXY(20,59);$this->MultiCell(42,9,"APOYO EN SALUD",'1','C');
		$this->SetXY(20,68);$this->MultiCell(42,9,"BENEFICIARIO INDIGENTE",'1','C');
		$this->SetXY(20,77);$this->MultiCell(42,9,"BENEFICIARIO PAGANTE",'1','C');
		$this->SetXY(20,86);$this->MultiCell(42,9,"TOTAL",'1','C');
		//SEGUNDA COLUMNA
		$this->SetXY(62,$y);$this->MultiCell(22,9,"SEGUN SEXO",'1','C');
		$this->SetXY(62,59);$this->MultiCell(11,9,"M",'1','C');
		$this->SetXY(73,59);$this->MultiCell(11,9,"F",'1','C');
		//TERCERA COLUMNA
		$this->SetXY(84,$y);$this->MultiCell(12,9,"TOTAL",'1','C');
		$this->SetXY(84,59);$this->MultiCell(12,9,"-------",'1','C');
		//CUARTA COLUMNA
		$this->SetXY(96,$y);$this->MultiCell(80,9,"SEGUN SU EDAD",'1','C');
		$this->SetXY(96,59);$this->MultiCell(10,9,"0-5",'1','C');
		$this->SetXY(106,59);$this->MultiCell(10,9,"6-10",'1','C');
		$this->SetXY(116,59);$this->MultiCell(10,9,"11-15",'1','C');
		$this->SetXY(126,59);$this->MultiCell(10,9,"16-20",'1','C');
		$this->SetXY(136,59);$this->MultiCell(10,9,"21-30",'1','C');
		$this->SetXY(146,59);$this->MultiCell(10,9,"31-50",'1','C');
		$this->SetXY(156,59);$this->MultiCell(10,9,"51-60",'1','C');
		$this->SetXY(166,59);$this->MultiCell(10,9,"61-99",'1','C');

		//CUERPO
		$mujeres_pagante = 0;
		$varones_pagante = 0;
		$mujeres_indigente = 0;
		$varones_indigente = 0;
		$total_mujeres = 0;
		$total_varones = 0;
		$total = 0;
		$edad1_i=0;
		$edad2_i=0;
		$edad3_i=0;
		$edad4_i=0;
		$edad5_i=0;
		$edad6_i=0;
		$edad7_i=0;
		$edad8_i=0;

		$edad1_p=0;
		$edad2_p=0;
		$edad3_p=0;
		$edad4_p=0;
		$edad5_p=0;
		$edad6_p=0;
		$edad7_p=0;
		$edad8_p=0;

		$total_edad1=0;
		$total_edad2=0;
		$total_edad3=0;
		$total_edad4=0;
		$total_edad5=0;
		$total_edad6=0;
		$total_edad7=0;
		$total_edad8=0;

		$this->SetFont('Arial','',9);
		$y=$y+9;
		$yini= $y;
		for($i = 0;$i<count($diario);$i++){
			$fecha_parte = $diario[$i]['fech']->sec;
			if(isset($diario[$i]['consulta'])){
				for($j = 0;$j<count($diario[$i]['consulta']);$j++){
					if(isset($diario[$i]["consulta"][$j]['paciente']['fecha_na']['sec'])){
						$fecha_nacimiento = $diario[$i]["consulta"][$j]['paciente']['fecha_na']['sec'];
						$edad = $fecha_parte-$fecha_nacimiento;
						$edad = floor($edad/(60*60*24*365));
					}else{
						$edad = -1;
					}
	
				if(($diario[$i]["consulta"][$j] != null)){
						if(floatval($diario[$i]["consulta"][$j]['paciente']['sexo']) == 0 && floatval($diario[$i]["consulta"][$j]['cate']) != 8 && $diario[$i]['consulta'][$j]['esta'] != '5'  && $diario[$i]['modulo'] != 'MH'){
							$mujeres_pagante++;
						}
						if(floatval($diario[$i]["consulta"][$j]['paciente']['sexo']) == 1 && floatval($diario[$i]["consulta"][$j]['cate']) != 8 && $diario[$i]['consulta'][$j]['esta'] != '5'  && $diario[$i]['modulo'] != 'MH'){
							$varones_pagante++;
						}
						if(floatval($diario[$i]["consulta"][$j]['paciente']['sexo']) == 0 && floatval($diario[$i]["consulta"][$j]['cate']) == 8 && $diario[$i]['consulta'][$j]['esta'] != '5'  && $diario[$i]['modulo'] != 'MH'){
							$mujeres_indigente++;
						}
						if(floatval($diario[$i]["consulta"][$j]['paciente']['sexo']) == 1 && floatval($diario[$i]["consulta"][$j]['cate']) == 8 && $diario[$i]['consulta'][$j]['esta'] != '5'  && $diario[$i]['modulo'] != 'MH'){
							$varones_indigente++;
						}
						
						if($edad<6 && floatval($diario[$i]["consulta"][$j]['cate']) != 8 && $diario[$i]['consulta'][$j]['esta'] != '5'  && $diario[$i]['modulo'] != 'MH'){
							$edad1_p++;
						}

						if($edad>=6 && $edad<11 && floatval($diario[$i]["consulta"][$j]['cate']) != 8 && $diario[$i]['consulta'][$j]['esta'] != '5'  && $diario[$i]['modulo'] != 'MH'){
							$edad2_p++;
						}

						if($edad>=11 && $edad<16 && floatval($diario[$i]["consulta"][$j]['cate']) != 8 && $diario[$i]['consulta'][$j]['esta'] != '5'  && $diario[$i]['modulo'] != 'MH'){
							$edad3_p++;
							
						}

						if($edad>=16 && $edad<21 && floatval($diario[$i]["consulta"][$j]['cate']) != 8 && $diario[$i]['consulta'][$j]['esta'] != '5'  && $diario[$i]['modulo'] != 'MH'){
							$edad4_p++;
						}
						if($edad>=21 && $edad<31 && floatval($diario[$i]["consulta"][$j]['cate']) != 8 && $diario[$i]['consulta'][$j]['esta'] != '5'  && $diario[$i]['modulo'] != 'MH'){
							$edad5_p++;
						}
						if($edad>=31 && $edad<51 && floatval($diario[$i]["consulta"][$j]['cate']) != 8 && $diario[$i]['consulta'][$j]['esta'] != '5'  && $diario[$i]['modulo'] != 'MH'){
							$edad6_p++;
						}
						if($edad>=51 && $edad<61 && floatval($diario[$i]["consulta"][$j]['cate']) != 8 && $diario[$i]['consulta'][$j]['esta'] != '5'  && $diario[$i]['modulo'] != 'MH'){
							$edad7_p++;
						}
						if($edad>=61 && $edad<100 && floatval($diario[$i]["consulta"][$j]['cate']) != 8 && $diario[$i]['consulta'][$j]['esta'] != '5'  && $diario[$i]['modulo'] != 'MH'){
							$edad8_p++;
						}


						if($edad>=0 && $edad<6 && floatval($diario[$i]["consulta"][$j]['cate']) == 8 && $diario[$i]['consulta'][$j]['esta'] != '5'  && $diario[$i]['modulo'] != 'MH'){
							$edad1_i++;
						}

						if($edad>=6 && $edad<11 && floatval($diario[$i]["consulta"][$j]['cate']) == 8 && $diario[$i]['consulta'][$j]['esta'] != '5'  && $diario[$i]['modulo'] != 'MH'){
							$edad2_i++;
						}

						if($edad>=11 && $edad<16 && floatval($diario[$i]["consulta"][$j]['cate']) == 8 && $diario[$i]['consulta'][$j]['esta'] != '5'  && $diario[$i]['modulo'] != 'MH'){
							$edad3_i++;
						}

						if($edad>=16 && $edad<21 && floatval($diario[$i]["consulta"][$j]['cate']) == 8 && $diario[$i]['consulta'][$j]['esta'] != '5'  && $diario[$i]['modulo'] != 'MH'){
							$edad4_i++;
						}
						if($edad>=21 && $edad<31 && floatval($diario[$i]["consulta"][$j]['cate']) == 8 && $diario[$i]['consulta'][$j]['esta'] != '5'  && $diario[$i]['modulo'] != 'MH'){
							$edad5_i++;
						}
						if($edad>=31 && $edad<51 && floatval($diario[$i]["consulta"][$j]['cate']) == 8 && $diario[$i]['consulta'][$j]['esta'] != '5'  && $diario[$i]['modulo'] != 'MH'){
							$edad6_i++;
						}
						if($edad>=51 && $edad<61 && floatval($diario[$i]["consulta"][$j]['cate']) == 8 && $diario[$i]['consulta'][$j]['esta'] != '5'  && $diario[$i]['modulo'] != 'MH'){
							$edad7_i++;
						}
						if($edad>=61 && $edad<100 && floatval($diario[$i]["consulta"][$j]['cate']) == 8 && $diario[$i]['consulta'][$j]['esta'] != '5'  && $diario[$i]['modulo'] != 'MH'){
							$edad8_i++;
						}
					}
				}
			}
		}


			$total_sexo_pagante = $mujeres_pagante + $varones_pagante;
			$total_sexo_indigente = $mujeres_indigente + $varones_indigente;
			$total_varones = $varones_indigente+$varones_pagante;
			$total_mujeres = $mujeres_indigente + $mujeres_pagante;
			$total = $total_mujeres + $total_varones;
			$total_edad1 = $edad1_p + $edad1_i;
			$total_edad2 = $edad2_p + $edad2_i;
			$total_edad3 = $edad3_p + $edad3_i;
			$total_edad4 = $edad4_p + $edad4_i;
			$total_edad5 = $edad5_p + $edad5_i;
			$total_edad6 = $edad6_p + $edad6_i;
			$total_edad7 = $edad7_p + $edad7_i;
			$total_edad8 = $edad8_p + $edad8_i;
			
			//---------------------------SEGUN SEXO VARONES----------------------------------------\\
			$this->SetXY(62,68);$this->MultiCell(11,9,"".$varones_indigente,'1','C');
			$this->SetXY(62,77);$this->MultiCell(11,9,"".$varones_pagante,'1','C');
			$this->SetFont('Arial','B',9);
			$this->SetXY(62,86);$this->MultiCell(11,9,"".$total_varones,'1','C');
			$this->SetFont('Arial','',9);
			//-------------------------------------------------------------------\\
			$this->SetXY(96,68);$this->MultiCell(10,9,"".$edad1_i,'1','C');
			$this->SetXY(106,68);$this->MultiCell(10,9,"".$edad2_i,'1','C');
			$this->SetXY(116,68);$this->MultiCell(10,9,"".$edad3_i,'1','C');
			$this->SetXY(126,68);$this->MultiCell(10,9,"".$edad4_i,'1','C');
			$this->SetXY(136,68);$this->MultiCell(10,9,"".$edad5_i,'1','C');
			$this->SetXY(146,68);$this->MultiCell(10,9,"".$edad6_i,'1','C');
			$this->SetXY(156,68);$this->MultiCell(10,9,"".$edad7_i,'1','C');
			$this->SetXY(166,68);$this->MultiCell(10,9,"".$edad8_i,'1','C');
			//-------------------------------------------------------------------\\
			$this->SetXY(96,77);$this->MultiCell(10,9,"".$edad1_p,'1','C');
			$this->SetXY(106,77);$this->MultiCell(10,9,"".$edad2_p,'1','C');
			$this->SetXY(116,77);$this->MultiCell(10,9,"".$edad3_p,'1','C');
			$this->SetXY(126,77);$this->MultiCell(10,9,"".$edad4_p,'1','C');
			$this->SetXY(136,77);$this->MultiCell(10,9,"".$edad5_p,'1','C');
			$this->SetXY(146,77);$this->MultiCell(10,9,"".$edad6_p,'1','C');
			$this->SetXY(156,77);$this->MultiCell(10,9,"".$edad7_p,'1','C');
			$this->SetXY(166,77);$this->MultiCell(10,9,"".$edad8_p,'1','C');
			//-------------------------------------------------------------------\\
			$this->SetFont('Arial','B',9);
			$this->SetXY(96,86);$this->MultiCell(10,9,"".$total_edad1,'1','C');
			$this->SetXY(106,86);$this->MultiCell(10,9,"".$total_edad2,'1','C');
			$this->SetXY(116,86);$this->MultiCell(10,9,"".$total_edad3,'1','C');
			$this->SetXY(126,86);$this->MultiCell(10,9,"".$total_edad4,'1','C');
			$this->SetXY(136,86);$this->MultiCell(10,9,"".$total_edad5,'1','C');
			$this->SetXY(146,86);$this->MultiCell(10,9,"".$total_edad6,'1','C');
			$this->SetXY(156,86);$this->MultiCell(10,9,"".$total_edad7,'1','C');
			$this->SetXY(166,86);$this->MultiCell(10,9,"".$total_edad8,'1','C');
			$this->SetFont('Arial','',9);
			//-------------------------------------------------------------------\\
			$this->SetXY(73,68);$this->MultiCell(11,9,"".$mujeres_indigente,'1','C');
			$this->SetXY(73,77);$this->MultiCell(11,9,"".$mujeres_pagante,'1','C');
			$this->SetFont('Arial','B',9);
			$this->SetXY(73,86);$this->MultiCell(11,9,"".$total_mujeres,'1','C');
			//-------------------------------------------------------------------\\
			$this->SetXY(84,68);$this->MultiCell(12,9,"".$total_sexo_indigente,'1','C');
			$this->SetXY(84,77);$this->MultiCell(12,9,"".$total_sexo_pagante,'1','C');
			$this->SetXY(84,86);$this->MultiCell(12,9,"".$total,'1','C');
			//-------------------------------------------------------------------\\
	/*-------------------------------------------------------------------------*/
	$this->AddPage();
$x=5;
$y=25;
$y_ini = $y;
$page_b = 275;
		
//CABECERA
		$y=$y+25;
		$this->SetFont('Arial','B',10);
		$this->SetXY(5,30);$this->MultiCell(200,5,	"FICHA DE REPORTE MENSUAL DE SERVICIOS - SOCIEDAD DE BENEFICIENCIA DE AREQUIPA - REPORTE QUINCENAL DE SERVICIOS - INTERCONSULTAS: SALUD MENTAL",'0','C');
		$this->SetFont('Arial','B',8);
		//PRIMERA COLUMNA
		$this->SetXY(20,$y);$this->MultiCell(42,9,"NUMERO DE BENEFCIARIOS",'1','C');
		$this->SetXY(20,59);$this->MultiCell(42,9,"APOYO EN SALUD",'1','C');
		$this->SetXY(20,68);$this->MultiCell(42,9,"BENEFICIARIO INDIGENTE",'1','C');
		$this->SetXY(20,77);$this->MultiCell(42,9,"BENEFICIARIO PAGANTE",'1','C');
		$this->SetXY(20,86);$this->MultiCell(42,9,"TOTAL",'1','C');
		//SEGUNDA COLUMNA
		$this->SetXY(62,$y);$this->MultiCell(22,9,"SEGUN SEXO",'1','C');
		$this->SetXY(62,59);$this->MultiCell(11,9,"M",'1','C');
		$this->SetXY(73,59);$this->MultiCell(11,9,"F",'1','C');
		//TERCERA COLUMNA
		$this->SetXY(84,$y);$this->MultiCell(12,9,"TOTAL",'1','C');
		$this->SetXY(84,59);$this->MultiCell(12,9,"-------",'1','C');
		//CUARTA COLUMNA
		$this->SetXY(96,$y);$this->MultiCell(80,9,"SEGUN SU EDAD",'1','C');
		$this->SetXY(96,59);$this->MultiCell(10,9,"0-5",'1','C');
		$this->SetXY(106,59);$this->MultiCell(10,9,"6-10",'1','C');
		$this->SetXY(116,59);$this->MultiCell(10,9,"11-15",'1','C');
		$this->SetXY(126,59);$this->MultiCell(10,9,"16-20",'1','C');
		$this->SetXY(136,59);$this->MultiCell(10,9,"21-30",'1','C');
		$this->SetXY(146,59);$this->MultiCell(10,9,"31-50",'1','C');
		$this->SetXY(156,59);$this->MultiCell(10,9,"51-60",'1','C');
		$this->SetXY(166,59);$this->MultiCell(10,9,"61-99",'1','C');

		//CUERPO
		$mujeres_pagante = 0;
		$varones_pagante = 0;
		$mujeres_indigente = 0;
		$varones_indigente = 0;
		$total_mujeres = 0;
		$total_varones = 0;
		$total = 0;
		$edad1_i=0;
		$edad2_i=0;
		$edad3_i=0;
		$edad4_i=0;
		$edad5_i=0;
		$edad6_i=0;
		$edad7_i=0;
		$edad8_i=0;

		$edad1_p=0;
		$edad2_p=0;
		$edad3_p=0;
		$edad4_p=0;
		$edad5_p=0;
		$edad6_p=0;
		$edad7_p=0;
		$edad8_p=0;

		$total_edad1=0;
		$total_edad2=0;
		$total_edad3=0;
		$total_edad4=0;
		$total_edad5=0;
		$total_edad6=0;
		$total_edad7=0;
		$total_edad8=0;

		$this->SetFont('Arial','',9);
		$y=$y+9;
		$yini= $y;
		for($i = 0;$i<count($diario);$i++){
			$fecha_parte = $diario[$i]['fech']->sec;
			if(isset($diario[$i]['consulta'])){
				for($j = 0;$j<count($diario[$i]['consulta']);$j++){
					if(isset($diario[$i]["consulta"][$j]['paciente']['fecha_na']['sec'])){
						$fecha_nacimiento = $diario[$i]["consulta"][$j]['paciente']['fecha_na']['sec'];
						$edad = $fecha_parte-$fecha_nacimiento;
						$edad = floor($edad/(60*60*24*365));
					}else{
						$edad = -1;
					}
	
				if(($diario[$i]["consulta"][$j] != null)){
						if(floatval($diario[$i]["consulta"][$j]['paciente']['sexo']) == 0 && floatval($diario[$i]["consulta"][$j]['cate']) != 8 && $diario[$i]['consulta'][$j]['esta'] == '5'  && $diario[$i]['modulo'] == 'MH'){
							$mujeres_pagante++;
						}
						if(floatval($diario[$i]["consulta"][$j]['paciente']['sexo']) == 1 && floatval($diario[$i]["consulta"][$j]['cate']) != 8 && $diario[$i]['consulta'][$j]['esta'] == '5'  && $diario[$i]['modulo'] == 'MH'){
							$varones_pagante++;
						}
						if(floatval($diario[$i]["consulta"][$j]['paciente']['sexo']) == 0 && floatval($diario[$i]["consulta"][$j]['cate']) == 8 && $diario[$i]['consulta'][$j]['esta'] == '5'  && $diario[$i]['modulo'] == 'MH'){
							$mujeres_indigente++;
						}
						if(floatval($diario[$i]["consulta"][$j]['paciente']['sexo']) == 1 && floatval($diario[$i]["consulta"][$j]['cate']) == 8 && $diario[$i]['consulta'][$j]['esta'] == '5'  && $diario[$i]['modulo'] == 'MH'){
							$varones_indigente++;
						}
						
						if($edad<6 && floatval($diario[$i]["consulta"][$j]['cate']) != 8 && $diario[$i]['consulta'][$j]['esta'] == '5'  && $diario[$i]['modulo'] == 'MH'){
							$edad1_p++;
						}

						if($edad>=6 && $edad<11 && floatval($diario[$i]["consulta"][$j]['cate']) != 8 && $diario[$i]['consulta'][$j]['esta'] == '5'  && $diario[$i]['modulo'] == 'MH'){
							$edad2_p++;
						}

						if($edad>=11 && $edad<16 && floatval($diario[$i]["consulta"][$j]['cate']) != 8 && $diario[$i]['consulta'][$j]['esta'] == '5'  && $diario[$i]['modulo'] == 'MH'){
							$edad3_p++;
							
						}

						if($edad>=16 && $edad<21 && floatval($diario[$i]["consulta"][$j]['cate']) != 8 && $diario[$i]['consulta'][$j]['esta'] == '5'  && $diario[$i]['modulo'] == 'MH'){
							$edad4_p++;
						}
						if($edad>=21 && $edad<31 && floatval($diario[$i]["consulta"][$j]['cate']) != 8 && $diario[$i]['consulta'][$j]['esta'] == '5'  && $diario[$i]['modulo'] == 'MH'){
							$edad5_p++;
						}
						if($edad>=31 && $edad<51 && floatval($diario[$i]["consulta"][$j]['cate']) != 8 && $diario[$i]['consulta'][$j]['esta'] == '5'  && $diario[$i]['modulo'] == 'MH'){
							$edad6_p++;
						}
						if($edad>=51 && $edad<61 && floatval($diario[$i]["consulta"][$j]['cate']) != 8 && $diario[$i]['consulta'][$j]['esta'] == '5'  && $diario[$i]['modulo'] == 'MH'){
							$edad7_p++;
						}
						if($edad>=61 && $edad<100 && floatval($diario[$i]["consulta"][$j]['cate']) != 8 && $diario[$i]['consulta'][$j]['esta'] == '5'  && $diario[$i]['modulo'] == 'MH'){
							$edad8_p++;
						}


						if($edad>=0 && $edad<6 && floatval($diario[$i]["consulta"][$j]['cate']) == 8 && $diario[$i]['consulta'][$j]['esta'] == '5'  && $diario[$i]['modulo'] == 'MH'){
							$edad1_i++;
						}

						if($edad>=6 && $edad<11 && floatval($diario[$i]["consulta"][$j]['cate']) == 8 && $diario[$i]['consulta'][$j]['esta'] == '5'  && $diario[$i]['modulo'] == 'MH'){
							$edad2_i++;
						}

						if($edad>=11 && $edad<16 && floatval($diario[$i]["consulta"][$j]['cate']) == 8 && $diario[$i]['consulta'][$j]['esta'] == '5'  && $diario[$i]['modulo'] == 'MH'){
							$edad3_i++;
						}

						if($edad>=16 && $edad<21 && floatval($diario[$i]["consulta"][$j]['cate']) == 8 && $diario[$i]['consulta'][$j]['esta'] == '5'  && $diario[$i]['modulo'] == 'MH'){
							$edad4_i++;
						}
						if($edad>=21 && $edad<31 && floatval($diario[$i]["consulta"][$j]['cate']) == 8 && $diario[$i]['consulta'][$j]['esta'] == '5'  && $diario[$i]['modulo'] == 'MH'){
							$edad5_i++;
						}
						if($edad>=31 && $edad<51 && floatval($diario[$i]["consulta"][$j]['cate']) == 8 && $diario[$i]['consulta'][$j]['esta'] == '5'  && $diario[$i]['modulo'] == 'MH'){
							$edad6_i++;
						}
						if($edad>=51 && $edad<61 && floatval($diario[$i]["consulta"][$j]['cate']) == 8 && $diario[$i]['consulta'][$j]['esta'] == '5'  && $diario[$i]['modulo'] == 'MH'){
							$edad7_i++;
						}
						if($edad>=61 && $edad<100 && floatval($diario[$i]["consulta"][$j]['cate']) == 8 && $diario[$i]['consulta'][$j]['esta'] == '5'  && $diario[$i]['modulo'] == 'MH'){
							$edad8_i++;
						}
					}
				}
			}
		}


			$total_sexo_pagante = $mujeres_pagante + $varones_pagante;
			$total_sexo_indigente = $mujeres_indigente + $varones_indigente;
			$total_varones = $varones_indigente+$varones_pagante;
			$total_mujeres = $mujeres_indigente + $mujeres_pagante;
			$total = $total_mujeres + $total_varones;
			$total_edad1 = $edad1_p + $edad1_i;
			$total_edad2 = $edad2_p + $edad2_i;
			$total_edad3 = $edad3_p + $edad3_i;
			$total_edad4 = $edad4_p + $edad4_i;
			$total_edad5 = $edad5_p + $edad5_i;
			$total_edad6 = $edad6_p + $edad6_i;
			$total_edad7 = $edad7_p + $edad7_i;
			$total_edad8 = $edad8_p + $edad8_i;
			
			//---------------------------SEGUN SEXO VARONES----------------------------------------\\
			$this->SetXY(62,68);$this->MultiCell(11,9,"".$varones_indigente,'1','C');
			$this->SetXY(62,77);$this->MultiCell(11,9,"".$varones_pagante,'1','C');
			$this->SetFont('Arial','B',9);
			$this->SetXY(62,86);$this->MultiCell(11,9,"".$total_varones,'1','C');
			$this->SetFont('Arial','',9);
			//-------------------------------------------------------------------\\
			$this->SetXY(96,68);$this->MultiCell(10,9,"".$edad1_i,'1','C');
			$this->SetXY(106,68);$this->MultiCell(10,9,"".$edad2_i,'1','C');
			$this->SetXY(116,68);$this->MultiCell(10,9,"".$edad3_i,'1','C');
			$this->SetXY(126,68);$this->MultiCell(10,9,"".$edad4_i,'1','C');
			$this->SetXY(136,68);$this->MultiCell(10,9,"".$edad5_i,'1','C');
			$this->SetXY(146,68);$this->MultiCell(10,9,"".$edad6_i,'1','C');
			$this->SetXY(156,68);$this->MultiCell(10,9,"".$edad7_i,'1','C');
			$this->SetXY(166,68);$this->MultiCell(10,9,"".$edad8_i,'1','C');
			//-------------------------------------------------------------------\\
			$this->SetXY(96,77);$this->MultiCell(10,9,"".$edad1_p,'1','C');
			$this->SetXY(106,77);$this->MultiCell(10,9,"".$edad2_p,'1','C');
			$this->SetXY(116,77);$this->MultiCell(10,9,"".$edad3_p,'1','C');
			$this->SetXY(126,77);$this->MultiCell(10,9,"".$edad4_p,'1','C');
			$this->SetXY(136,77);$this->MultiCell(10,9,"".$edad5_p,'1','C');
			$this->SetXY(146,77);$this->MultiCell(10,9,"".$edad6_p,'1','C');
			$this->SetXY(156,77);$this->MultiCell(10,9,"".$edad7_p,'1','C');
			$this->SetXY(166,77);$this->MultiCell(10,9,"".$edad8_p,'1','C');
			//-------------------------------------------------------------------\\
			$this->SetFont('Arial','B',9);
			$this->SetXY(96,86);$this->MultiCell(10,9,"".$total_edad1,'1','C');
			$this->SetXY(106,86);$this->MultiCell(10,9,"".$total_edad2,'1','C');
			$this->SetXY(116,86);$this->MultiCell(10,9,"".$total_edad3,'1','C');
			$this->SetXY(126,86);$this->MultiCell(10,9,"".$total_edad4,'1','C');
			$this->SetXY(136,86);$this->MultiCell(10,9,"".$total_edad5,'1','C');
			$this->SetXY(146,86);$this->MultiCell(10,9,"".$total_edad6,'1','C');
			$this->SetXY(156,86);$this->MultiCell(10,9,"".$total_edad7,'1','C');
			$this->SetXY(166,86);$this->MultiCell(10,9,"".$total_edad8,'1','C');
			$this->SetFont('Arial','',9);
			//-------------------------------------------------------------------\\
			$this->SetXY(73,68);$this->MultiCell(11,9,"".$mujeres_indigente,'1','C');
			$this->SetXY(73,77);$this->MultiCell(11,9,"".$mujeres_pagante,'1','C');
			$this->SetFont('Arial','B',9);
			$this->SetXY(73,86);$this->MultiCell(11,9,"".$total_mujeres,'1','C');
			//-------------------------------------------------------------------\\
			$this->SetXY(84,68);$this->MultiCell(12,9,"".$total_sexo_indigente,'1','C');
			$this->SetXY(84,77);$this->MultiCell(12,9,"".$total_sexo_pagante,'1','C');
			$this->SetXY(84,86);$this->MultiCell(12,9,"".$total,'1','C');
			//-------------------------------------------------------------------\\
	$this->AddPage();
$x=5;
$y=25;
$y_ini = $y;
$page_b = 275;
		
//CABECERA
		$y=$y+25;
		$this->SetFont('Arial','B',10);
		$this->SetXY(5,30);$this->MultiCell(200,5,	"FICHA DE REPORTE MENSUAL DE SERVICIOS - SOCIEDAD DE BENEFICIENCIA DE AREQUIPA - REPORTE QUINCENAL DE SERVICIOS - INTERCONSULTAS: ADICCIONES",'0','C');
		$this->SetFont('Arial','B',8);
		//PRIMERA COLUMNA
		$this->SetXY(20,$y);$this->MultiCell(42,9,"NUMERO DE BENEFCIARIOS",'1','C');
		$this->SetXY(20,59);$this->MultiCell(42,9,"APOYO EN SALUD",'1','C');
		$this->SetXY(20,68);$this->MultiCell(42,9,"BENEFICIARIO INDIGENTE",'1','C');
		$this->SetXY(20,77);$this->MultiCell(42,9,"BENEFICIARIO PAGANTE",'1','C');
		$this->SetXY(20,86);$this->MultiCell(42,9,"TOTAL",'1','C');
		//SEGUNDA COLUMNA
		$this->SetXY(62,$y);$this->MultiCell(22,9,"SEGUN SEXO",'1','C');
		$this->SetXY(62,59);$this->MultiCell(11,9,"M",'1','C');
		$this->SetXY(73,59);$this->MultiCell(11,9,"F",'1','C');
		//TERCERA COLUMNA
		$this->SetXY(84,$y);$this->MultiCell(12,9,"TOTAL",'1','C');
		$this->SetXY(84,59);$this->MultiCell(12,9,"-------",'1','C');
		//CUARTA COLUMNA
		$this->SetXY(96,$y);$this->MultiCell(80,9,"SEGUN SU EDAD",'1','C');
		$this->SetXY(96,59);$this->MultiCell(10,9,"0-5",'1','C');
		$this->SetXY(106,59);$this->MultiCell(10,9,"6-10",'1','C');
		$this->SetXY(116,59);$this->MultiCell(10,9,"11-15",'1','C');
		$this->SetXY(126,59);$this->MultiCell(10,9,"16-20",'1','C');
		$this->SetXY(136,59);$this->MultiCell(10,9,"21-30",'1','C');
		$this->SetXY(146,59);$this->MultiCell(10,9,"31-50",'1','C');
		$this->SetXY(156,59);$this->MultiCell(10,9,"51-60",'1','C');
		$this->SetXY(166,59);$this->MultiCell(10,9,"61-99",'1','C');

		//CUERPO
		$mujeres_pagante = 0;
		$varones_pagante = 0;
		$mujeres_indigente = 0;
		$varones_indigente = 0;
		$total_mujeres = 0;
		$total_varones = 0;
		$total = 0;
		$edad1_i=0;
		$edad2_i=0;
		$edad3_i=0;
		$edad4_i=0;
		$edad5_i=0;
		$edad6_i=0;
		$edad7_i=0;
		$edad8_i=0;

		$edad1_p=0;
		$edad2_p=0;
		$edad3_p=0;
		$edad4_p=0;
		$edad5_p=0;
		$edad6_p=0;
		$edad7_p=0;
		$edad8_p=0;

		$total_edad1=0;
		$total_edad2=0;
		$total_edad3=0;
		$total_edad4=0;
		$total_edad5=0;
		$total_edad6=0;
		$total_edad7=0;
		$total_edad8=0;

		$this->SetFont('Arial','',9);
		$y=$y+9;
		$yini= $y;
		for($i = 0;$i<count($diario);$i++){
			$fecha_parte = $diario[$i]['fech']->sec;
			if(isset($diario[$i]['consulta'])){
				for($j = 0;$j<count($diario[$i]['consulta']);$j++){
					if(isset($diario[$i]["consulta"][$j]['paciente']['fecha_na']['sec'])){
						$fecha_nacimiento = $diario[$i]["consulta"][$j]['paciente']['fecha_na']['sec'];
						$edad = $fecha_parte-$fecha_nacimiento;
						$edad = floor($edad/(60*60*24*365));
					}else{
						$edad = -1;
					}
	
				if(($diario[$i]["consulta"][$j] != null)){
						if(floatval($diario[$i]["consulta"][$j]['paciente']['sexo']) == 0 && floatval($diario[$i]["consulta"][$j]['cate']) != 8 && $diario[$i]['consulta'][$j]['esta'] == '5'  && $diario[$i]['modulo'] == 'AD'){
							$mujeres_pagante++;
						}
						if(floatval($diario[$i]["consulta"][$j]['paciente']['sexo']) == 1 && floatval($diario[$i]["consulta"][$j]['cate']) != 8 && $diario[$i]['consulta'][$j]['esta'] == '5'  && $diario[$i]['modulo'] == 'AD'){
							$varones_pagante++;
						}
						if(floatval($diario[$i]["consulta"][$j]['paciente']['sexo']) == 0 && floatval($diario[$i]["consulta"][$j]['cate']) == 8 && $diario[$i]['consulta'][$j]['esta'] == '5'  && $diario[$i]['modulo'] == 'AD'){
							$mujeres_indigente++;
						}
						if(floatval($diario[$i]["consulta"][$j]['paciente']['sexo']) == 1 && floatval($diario[$i]["consulta"][$j]['cate']) == 8 && $diario[$i]['consulta'][$j]['esta'] == '5'  && $diario[$i]['modulo'] == 'AD'){
							$varones_indigente++;
						}
						
						if($edad<6 && floatval($diario[$i]["consulta"][$j]['cate']) != 8 && $diario[$i]['consulta'][$j]['esta'] == '5'  && $diario[$i]['modulo'] == 'AD'){
							$edad1_p++;
						}

						if($edad>=6 && $edad<11 && floatval($diario[$i]["consulta"][$j]['cate']) != 8 && $diario[$i]['consulta'][$j]['esta'] == '5'  && $diario[$i]['modulo'] == 'AD'){
							$edad2_p++;
						}

						if($edad>=11 && $edad<16 && floatval($diario[$i]["consulta"][$j]['cate']) != 8 && $diario[$i]['consulta'][$j]['esta'] == '5'  && $diario[$i]['modulo'] == 'AD'){
							$edad3_p++;
							
						}

						if($edad>=16 && $edad<21 && floatval($diario[$i]["consulta"][$j]['cate']) != 8 && $diario[$i]['consulta'][$j]['esta'] == '5'  && $diario[$i]['modulo'] == 'AD'){
							$edad4_p++;
						}
						if($edad>=21 && $edad<31 && floatval($diario[$i]["consulta"][$j]['cate']) != 8 && $diario[$i]['consulta'][$j]['esta'] == '5'  && $diario[$i]['modulo'] == 'AD'){
							$edad5_p++;
						}
						if($edad>=31 && $edad<51 && floatval($diario[$i]["consulta"][$j]['cate']) != 8 && $diario[$i]['consulta'][$j]['esta'] == '5'  && $diario[$i]['modulo'] == 'AD'){
							$edad6_p++;
						}
						if($edad>=51 && $edad<61 && floatval($diario[$i]["consulta"][$j]['cate']) != 8 && $diario[$i]['consulta'][$j]['esta'] == '5'  && $diario[$i]['modulo'] == 'AD'){
							$edad7_p++;
						}
						if($edad>=61 && $edad<100 && floatval($diario[$i]["consulta"][$j]['cate']) != 8 && $diario[$i]['consulta'][$j]['esta'] == '5'  && $diario[$i]['modulo'] == 'AD'){
							$edad8_p++;
						}


						if($edad>=0 && $edad<6 && floatval($diario[$i]["consulta"][$j]['cate']) == 8 && $diario[$i]['consulta'][$j]['esta'] == '5'  && $diario[$i]['modulo'] == 'AD'){
							$edad1_i++;
						}

						if($edad>=6 && $edad<11 && floatval($diario[$i]["consulta"][$j]['cate']) == 8 && $diario[$i]['consulta'][$j]['esta'] == '5'  && $diario[$i]['modulo'] == 'AD'){
							$edad2_i++;
						}

						if($edad>=11 && $edad<16 && floatval($diario[$i]["consulta"][$j]['cate']) == 8 && $diario[$i]['consulta'][$j]['esta'] == '5'  && $diario[$i]['modulo'] == 'AD'){
							$edad3_i++;
						}

						if($edad>=16 && $edad<21 && floatval($diario[$i]["consulta"][$j]['cate']) == 8 && $diario[$i]['consulta'][$j]['esta'] == '5'  && $diario[$i]['modulo'] == 'AD'){
							$edad4_i++;
						}
						if($edad>=21 && $edad<31 && floatval($diario[$i]["consulta"][$j]['cate']) == 8 && $diario[$i]['consulta'][$j]['esta'] == '5'  && $diario[$i]['modulo'] == 'AD'){
							$edad5_i++;
						}
						if($edad>=31 && $edad<51 && floatval($diario[$i]["consulta"][$j]['cate']) == 8 && $diario[$i]['consulta'][$j]['esta'] == '5'  && $diario[$i]['modulo'] == 'AD'){
							$edad6_i++;
						}
						if($edad>=51 && $edad<61 && floatval($diario[$i]["consulta"][$j]['cate']) == 8 && $diario[$i]['consulta'][$j]['esta'] == '5'  && $diario[$i]['modulo'] == 'AD'){
							$edad7_i++;
						}
						if($edad>=61 && $edad<100 && floatval($diario[$i]["consulta"][$j]['cate']) == 8 && $diario[$i]['consulta'][$j]['esta'] == '5'  && $diario[$i]['modulo'] == 'AD'){
							$edad8_i++;
						}
					}
				}
			}
		}


			$total_sexo_pagante = $mujeres_pagante + $varones_pagante;
			$total_sexo_indigente = $mujeres_indigente + $varones_indigente;
			$total_varones = $varones_indigente+$varones_pagante;
			$total_mujeres = $mujeres_indigente + $mujeres_pagante;
			$total = $total_mujeres + $total_varones;
			$total_edad1 = $edad1_p + $edad1_i;
			$total_edad2 = $edad2_p + $edad2_i;
			$total_edad3 = $edad3_p + $edad3_i;
			$total_edad4 = $edad4_p + $edad4_i;
			$total_edad5 = $edad5_p + $edad5_i;
			$total_edad6 = $edad6_p + $edad6_i;
			$total_edad7 = $edad7_p + $edad7_i;
			$total_edad8 = $edad8_p + $edad8_i;
			
			//---------------------------SEGUN SEXO VARONES----------------------------------------\\
			$this->SetXY(62,68);$this->MultiCell(11,9,"".$varones_indigente,'1','C');
			$this->SetXY(62,77);$this->MultiCell(11,9,"".$varones_pagante,'1','C');
			$this->SetFont('Arial','B',9);
			$this->SetXY(62,86);$this->MultiCell(11,9,"".$total_varones,'1','C');
			$this->SetFont('Arial','',9);
			//-------------------------------------------------------------------\\
			$this->SetXY(96,68);$this->MultiCell(10,9,"".$edad1_i,'1','C');
			$this->SetXY(106,68);$this->MultiCell(10,9,"".$edad2_i,'1','C');
			$this->SetXY(116,68);$this->MultiCell(10,9,"".$edad3_i,'1','C');
			$this->SetXY(126,68);$this->MultiCell(10,9,"".$edad4_i,'1','C');
			$this->SetXY(136,68);$this->MultiCell(10,9,"".$edad5_i,'1','C');
			$this->SetXY(146,68);$this->MultiCell(10,9,"".$edad6_i,'1','C');
			$this->SetXY(156,68);$this->MultiCell(10,9,"".$edad7_i,'1','C');
			$this->SetXY(166,68);$this->MultiCell(10,9,"".$edad8_i,'1','C');
			//-------------------------------------------------------------------\\
			$this->SetXY(96,77);$this->MultiCell(10,9,"".$edad1_p,'1','C');
			$this->SetXY(106,77);$this->MultiCell(10,9,"".$edad2_p,'1','C');
			$this->SetXY(116,77);$this->MultiCell(10,9,"".$edad3_p,'1','C');
			$this->SetXY(126,77);$this->MultiCell(10,9,"".$edad4_p,'1','C');
			$this->SetXY(136,77);$this->MultiCell(10,9,"".$edad5_p,'1','C');
			$this->SetXY(146,77);$this->MultiCell(10,9,"".$edad6_p,'1','C');
			$this->SetXY(156,77);$this->MultiCell(10,9,"".$edad7_p,'1','C');
			$this->SetXY(166,77);$this->MultiCell(10,9,"".$edad8_p,'1','C');
			//-------------------------------------------------------------------\\
			$this->SetFont('Arial','B',9);
			$this->SetXY(96,86);$this->MultiCell(10,9,"".$total_edad1,'1','C');
			$this->SetXY(106,86);$this->MultiCell(10,9,"".$total_edad2,'1','C');
			$this->SetXY(116,86);$this->MultiCell(10,9,"".$total_edad3,'1','C');
			$this->SetXY(126,86);$this->MultiCell(10,9,"".$total_edad4,'1','C');
			$this->SetXY(136,86);$this->MultiCell(10,9,"".$total_edad5,'1','C');
			$this->SetXY(146,86);$this->MultiCell(10,9,"".$total_edad6,'1','C');
			$this->SetXY(156,86);$this->MultiCell(10,9,"".$total_edad7,'1','C');
			$this->SetXY(166,86);$this->MultiCell(10,9,"".$total_edad8,'1','C');
			$this->SetFont('Arial','',9);
			//-------------------------------------------------------------------\\
			$this->SetXY(73,68);$this->MultiCell(11,9,"".$mujeres_indigente,'1','C');
			$this->SetXY(73,77);$this->MultiCell(11,9,"".$mujeres_pagante,'1','C');
			$this->SetFont('Arial','B',9);
			$this->SetXY(73,86);$this->MultiCell(11,9,"".$total_mujeres,'1','C');
			//-------------------------------------------------------------------\\
			$this->SetXY(84,68);$this->MultiCell(12,9,"".$total_sexo_indigente,'1','C');
			$this->SetXY(84,77);$this->MultiCell(12,9,"".$total_sexo_pagante,'1','C');
			$this->SetXY(84,86);$this->MultiCell(12,9,"".$total,'1','C');
			//-------------------------------------------------------------------\\

			


}

	
	 
}

$pdf=new repo('P','mm','A4');
$pdf->AliasNbPages();
$pdf->SetMargins(10,10,0); 
$pdf->SetTitle("reporte");
$pdf->SetAutoPageBreak(false,0); 
$pdf->Open();
$pdf->AddPage();
$pdf->Publicar($diario);
$pdf->SetLeftMargin(25);
//header('Content-Type: application/pdf; charset=utf-8');
$pdf->Output();

?>