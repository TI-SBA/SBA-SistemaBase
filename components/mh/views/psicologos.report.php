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



	function Publicar($reporte){	
			

		$x=5;
		$y=25;
		$y_ini = $y;
		$page_b = 275;
		
//CABECERA

//CABECERA
		$y=$y+25;
		$this->SetFont('Arial','B',10);
		$this->SetXY(5,30);$this->MultiCell(200,5,	"REPORTE MENSUAL DE CONSULTAS DE CONSULTORIOS EXTERNOS",'0','C');
		$this->SetXY(18,50);$this->MultiCell(172,5,"CONSULTAS DE ADICCIONES",'0','L');
		$this->SetFont('Arial','B',7);
		$this->SetXY(5,35);$this->MultiCell(180,5,"DRA. MILAGROS ELIZABETH REVILLA LOAYZA",'0','C');
		$this->SetFont('Arial','B',8);
		//PRIMERA COLUMNA
		$this->SetXY(20,$y+11);$this->MultiCell(42,9,"NUMERO DE BENEFCIARIOS",'1','C');
		$this->SetXY(20,70);$this->MultiCell(42,9,"APOYO EN SALUD",'1','C');
		$this->SetXY(20,79);$this->MultiCell(42,9,"BENEFICIARIO INDIGENTE",'1','C');
		$this->SetXY(20,88);$this->MultiCell(42,9,"BENEFICIARIO PAGANTE",'1','C');
		$this->SetXY(20,97);$this->MultiCell(42,9,"TOTAL",'1','C');
		//SEGUNDA COLUMNA
		$this->SetXY(62,$y+11);$this->MultiCell(22,9,"SEGUN SEXO",'1','C');
		$this->SetXY(62,70);$this->MultiCell(11,9,"M",'1','C');
		$this->SetXY(73,70);$this->MultiCell(11,9,"F",'1','C');
		//TERCERA COLUMNA
		$this->SetXY(84,$y+11);$this->MultiCell(12,9,"TOTAL",'1','C');
		$this->SetXY(84,70);$this->MultiCell(12,9,"-------",'1','C');
		//CUARTA COLUMNA
		$this->SetXY(96,$y+11);$this->MultiCell(80,9,"SEGUN SU EDAD",'1','C');
		$this->SetXY(96,70);$this->MultiCell(10,9,"0-5",'1','C');
		$this->SetXY(106,70);$this->MultiCell(10,9,"6-10",'1','C');
		$this->SetXY(116,70);$this->MultiCell(10,9,"11-15",'1','C');
		$this->SetXY(126,70);$this->MultiCell(10,9,"16-20",'1','C');
		$this->SetXY(136,70);$this->MultiCell(10,9,"21-30",'1','C');
		$this->SetXY(146,70);$this->MultiCell(10,9,"31-50",'1','C');
		$this->SetXY(156,70);$this->MultiCell(10,9,"51-60",'1','C');
		$this->SetXY(166,70);$this->MultiCell(10,9,"61-99",'1','C');

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
		for($i = 0;$i<count($reporte);$i++){
			$fecha_parte = $reporte[$i]['fech']->sec;
			
			for($j = 0;$j<count($reporte[$i]['consulta']);$j++){
				
				
				if(isset($reporte[$i]["consulta"][$j]['paciente']['fecha_na'])){
					$fecha_nacimiento = $reporte[$i]["consulta"][$j]['paciente']['fecha_na']['sec'];
					$edad = $fecha_parte-$fecha_nacimiento;
					$edad = floor($edad/(60*60*24*365));
					
				}else{
					$edad = -1;
				}

				
				if(floatval($reporte[$i]["consulta"][$j]['paciente']['sexo']) == 0 & floatval($reporte[$i]["consulta"][$j]['cate']) != 8 && $reporte[$i]['consulta'][$j]['esta'] != '5' && $reporte[$i]['medico']['_id'] =='587533593e6037474b8b4568' && $reporte[$i]['modulo'] == 'AD'){
					$mujeres_pagante++;
				}
				if(floatval($reporte[$i]["consulta"][$j]['paciente']['sexo']) == 1 & floatval($reporte[$i]["consulta"][$j]['cate']) != 8 && $reporte[$i]['consulta'][$j]['esta'] != '5' && $reporte[$i]['medico']['_id'] =='587533593e6037474b8b4568' && $reporte[$i]['modulo'] == 'AD'){
					$varones_pagante++;
				}
				if(floatval($reporte[$i]["consulta"][$j]['paciente']['sexo']) == 0 & floatval($reporte[$i]["consulta"][$j]['cate']) == 8 && $reporte[$i]['consulta'][$j]['esta'] != '5' && $reporte[$i]['medico']['_id'] =='587533593e6037474b8b4568' && $reporte[$i]['modulo'] == 'AD'){
					$mujeres_indigente++;
				}
				if(floatval($reporte[$i]["consulta"][$j]['paciente']['sexo']) == 1 & floatval($reporte[$i]["consulta"][$j]['cate']) == 8 && $reporte[$i]['consulta'][$j]['esta'] != '5' && $reporte[$i]['medico']['_id'] =='587533593e6037474b8b4568' && $reporte[$i]['modulo'] == 'AD'){
					$varones_indigente++;
				}
				
				if($edad<6 && floatval($reporte[$i]["consulta"][$j]['cate']) != 8 && $reporte[$i]['consulta'][$j]['esta'] != '5' && $reporte[$i]['medico']['_id'] =='587533593e6037474b8b4568' && $reporte[$i]['modulo'] == 'AD'){
					$edad1_p++;
				}

				if($edad>=6 && $edad<11 && floatval($reporte[$i]["consulta"][$j]['cate']) != 8 && $reporte[$i]['consulta'][$j]['esta'] != '5' && $reporte[$i]['medico']['_id'] =='587533593e6037474b8b4568' && $reporte[$i]['modulo'] == 'AD'){
					$edad2_p++;
				}

				if($edad>=11 && $edad<16 && floatval($reporte[$i]["consulta"][$j]['cate']) != 8 && $reporte[$i]['consulta'][$j]['esta'] != '5' && $reporte[$i]['medico']['_id'] =='587533593e6037474b8b4568' && $reporte[$i]['modulo'] == 'AD'){
					$edad3_p++;
				}

				if($edad>=16 && $edad<21 && floatval($reporte[$i]["consulta"][$j]['cate']) != 8 && $reporte[$i]['consulta'][$j]['esta'] != '5' && $reporte[$i]['medico']['_id'] =='587533593e6037474b8b4568' && $reporte[$i]['modulo'] == 'AD'){
					$edad4_p++;
				}
				if($edad>=21 && $edad<31 && floatval($reporte[$i]["consulta"][$j]['cate']) != 8 && $reporte[$i]['consulta'][$j]['esta'] != '5' && $reporte[$i]['medico']['_id'] =='587533593e6037474b8b4568' && $reporte[$i]['modulo'] == 'AD'){
					$edad5_p++;
				}
				if($edad>=31 && $edad<51 && floatval($reporte[$i]["consulta"][$j]['cate']) != 8 && $reporte[$i]['consulta'][$j]['esta'] != '5' && $reporte[$i]['medico']['_id'] =='587533593e6037474b8b4568' && $reporte[$i]['modulo'] == 'AD'){
					$edad6_p++;
				}
				if($edad>=51 && $edad<61 && floatval($reporte[$i]["consulta"][$j]['cate']) != 8 && $reporte[$i]['consulta'][$j]['esta'] != '5' && $reporte[$i]['medico']['_id'] =='587533593e6037474b8b4568' && $reporte[$i]['modulo'] == 'AD'){
					$edad7_p++;
				}
				if($edad>=61 && $edad<100 && floatval($reporte[$i]["consulta"][$j]['cate']) != 8 && $reporte[$i]['consulta'][$j]['esta'] != '5' && $reporte[$i]['medico']['_id'] =='587533593e6037474b8b4568' && $reporte[$i]['modulo'] == 'AD'){
					$edad8_p++;
				}


				if($edad>=0 && $edad<6 && floatval($reporte[$i]["consulta"][$j]['cate']) == 8 && $reporte[$i]['consulta'][$j]['esta'] != '5' && $reporte[$i]['medico']['_id'] =='587533593e6037474b8b4568' && $reporte[$i]['modulo'] == 'AD'){
					$edad1_i++;
				}

				if($edad>=6 && $edad<11 && floatval($reporte[$i]["consulta"][$j]['cate']) == 8 && $reporte[$i]['consulta'][$j]['esta'] != '5' && $reporte[$i]['medico']['_id'] =='587533593e6037474b8b4568' && $reporte[$i]['modulo'] == 'AD'){
					$edad2_i++;
				}

				if($edad>=11 && $edad<16 && floatval($reporte[$i]["consulta"][$j]['cate']) == 8 && $reporte[$i]['consulta'][$j]['esta'] != '5' && $reporte[$i]['medico']['_id'] =='587533593e6037474b8b4568' && $reporte[$i]['modulo'] == 'AD'){
					$edad3_i++;
				}

				if($edad>=16 && $edad<21 && floatval($reporte[$i]["consulta"][$j]['cate']) == 8 && $reporte[$i]['consulta'][$j]['esta'] != '5' && $reporte[$i]['medico']['_id'] =='587533593e6037474b8b4568' && $reporte[$i]['modulo'] == 'AD'){
					$edad4_i++;
				}
				if($edad>=21 && $edad<31 && floatval($reporte[$i]["consulta"][$j]['cate']) == 8 && $reporte[$i]['consulta'][$j]['esta'] != '5' && $reporte[$i]['medico']['_id'] =='587533593e6037474b8b4568' && $reporte[$i]['modulo'] == 'AD'){
					$edad5_i++;
				}
				if($edad>=31 && $edad<51 && floatval($reporte[$i]["consulta"][$j]['cate']) == 8 && $reporte[$i]['consulta'][$j]['esta'] != '5' && $reporte[$i]['medico']['_id'] =='587533593e6037474b8b4568' && $reporte[$i]['modulo'] == 'AD'){
					$edad6_i++;
				}
				if($edad>=51 && $edad<61 && floatval($reporte[$i]["consulta"][$j]['cate']) == 8 && $reporte[$i]['consulta'][$j]['esta'] != '5' && $reporte[$i]['medico']['_id'] =='587533593e6037474b8b4568' && $reporte[$i]['modulo'] == 'AD'){
					$edad7_i++;
				}
				if($edad>=61 && $edad<100 && floatval($reporte[$i]["consulta"][$j]['cate']) == 8 && $reporte[$i]['consulta'][$j]['esta'] != '5' && $reporte[$i]['medico']['_id'] =='587533593e6037474b8b4568' && $reporte[$i]['modulo'] == 'AD'){
					$edad8_i++;
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
			$this->SetXY(62,79);$this->MultiCell(11,9,"".$varones_indigente,'1','C');
			$this->SetXY(62,88);$this->MultiCell(11,9,"".$varones_pagante,'1','C');
			$this->SetFont('Arial','B',9);
			$this->SetXY(62,97);$this->MultiCell(11,9,"".$total_varones,'1','C');
			$this->SetFont('Arial','',9);
			//-------------------------------------------------------------------\\
			$this->SetXY(96,79);$this->MultiCell(10,9,"".$edad1_i,'1','C');
			$this->SetXY(106,79);$this->MultiCell(10,9,"".$edad2_i,'1','C');
			$this->SetXY(116,79);$this->MultiCell(10,9,"".$edad3_i,'1','C');
			$this->SetXY(126,79);$this->MultiCell(10,9,"".$edad4_i,'1','C');
			$this->SetXY(136,79);$this->MultiCell(10,9,"".$edad5_i,'1','C');
			$this->SetXY(146,79);$this->MultiCell(10,9,"".$edad6_i,'1','C');
			$this->SetXY(156,79);$this->MultiCell(10,9,"".$edad7_i,'1','C');
			$this->SetXY(166,79);$this->MultiCell(10,9,"".$edad8_i,'1','C');
			//-------------------------------------------------------------------\\
			$this->SetXY(96,88);$this->MultiCell(10,9,"".$edad1_p,'1','C');
			$this->SetXY(106,88);$this->MultiCell(10,9,"".$edad2_p,'1','C');
			$this->SetXY(116,88);$this->MultiCell(10,9,"".$edad3_p,'1','C');
			$this->SetXY(126,88);$this->MultiCell(10,9,"".$edad4_p,'1','C');
			$this->SetXY(136,88);$this->MultiCell(10,9,"".$edad5_p,'1','C');
			$this->SetXY(146,88);$this->MultiCell(10,9,"".$edad6_p,'1','C');
			$this->SetXY(156,88);$this->MultiCell(10,9,"".$edad7_p,'1','C');
			$this->SetXY(166,88);$this->MultiCell(10,9,"".$edad8_p,'1','C');
			//-------------------------------------------------------------------\\
			$this->SetFont('Arial','B',9);
			$this->SetXY(96,97);$this->MultiCell(10,9,"".$total_edad1,'1','C');
			$this->SetXY(106,97);$this->MultiCell(10,9,"".$total_edad2,'1','C');
			$this->SetXY(116,97);$this->MultiCell(10,9,"".$total_edad3,'1','C');
			$this->SetXY(126,97);$this->MultiCell(10,9,"".$total_edad4,'1','C');
			$this->SetXY(136,97);$this->MultiCell(10,9,"".$total_edad5,'1','C');
			$this->SetXY(146,97);$this->MultiCell(10,9,"".$total_edad6,'1','C');
			$this->SetXY(156,97);$this->MultiCell(10,9,"".$total_edad7,'1','C');
			$this->SetXY(166,97);$this->MultiCell(10,9,"".$total_edad8,'1','C');
			$this->SetFont('Arial','',9);
			//-------------------------------------------------------------------\\
			$this->SetXY(73,79);$this->MultiCell(11,9,"".$mujeres_indigente,'1','C');
			$this->SetXY(73,88);$this->MultiCell(11,9,"".$mujeres_pagante,'1','C');
			$this->SetFont('Arial','B',9);
			$this->SetXY(73,97);$this->MultiCell(11,9,"".$total_mujeres,'1','C');
			//-------------------------------------------------------------------\\
			$this->SetXY(84,79);$this->MultiCell(12,9,"".$total_sexo_indigente,'1','C');
			$this->SetXY(84,88);$this->MultiCell(12,9,"".$total_sexo_pagante,'1','C');
			$this->SetXY(84,97);$this->MultiCell(12,9,"".$total,'1','C');
			//-------------------------------------------------------------------\\
		$y=$y+80;
		$this->SetFont('Arial','B',10);
		$this->SetXY(18,124);$this->MultiCell(180,5,"CONSULTAS DE SALUD MENTAL",'0','L');
		$this->SetFont('Arial','B',7);
		
		$this->SetFont('Arial','B',8);
		//PRIMERA COLUMNA
		$this->SetXY(20,$y);$this->MultiCell(42,9,"NUMERO DE BENEFCIARIOS",'1','C');
		$this->SetXY(20,148);$this->MultiCell(42,9,"APOYO EN SALUD",'1','C');
		$this->SetXY(20,157);$this->MultiCell(42,9,"BENEFICIARIO INDIGENTE",'1','C');
		$this->SetXY(20,166);$this->MultiCell(42,9,"BENEFICIARIO PAGANTE",'1','C');
		$this->SetXY(20,175);$this->MultiCell(42,9,"TOTAL",'1','C');
		//SEGUNDA COLUMNA
		$this->SetXY(62,$y);$this->MultiCell(22,9,"SEGUN SEXO",'1','C');
		$this->SetXY(62,148);$this->MultiCell(11,9,"M",'1','C');
		$this->SetXY(73,148);$this->MultiCell(11,9,"F",'1','C');
		//TERCERA COLUMNA
		$this->SetXY(84,$y);$this->MultiCell(12,9,"TOTAL",'1','C');
		$this->SetXY(84,148);$this->MultiCell(12,9,"-------",'1','C');
		//CUARTA COLUMNA
		$this->SetXY(96,$y);$this->MultiCell(80,9,"SEGUN SU EDAD",'1','C');
		$this->SetXY(96,148);$this->MultiCell(10,9,"0-5",'1','C');
		$this->SetXY(106,148);$this->MultiCell(10,9,"6-10",'1','C');
		$this->SetXY(116,148);$this->MultiCell(10,9,"11-15",'1','C');
		$this->SetXY(126,148);$this->MultiCell(10,9,"16-20",'1','C');
		$this->SetXY(136,148);$this->MultiCell(10,9,"21-30",'1','C');
		$this->SetXY(146,148);$this->MultiCell(10,9,"31-50",'1','C');
		$this->SetXY(156,148);$this->MultiCell(10,9,"51-60",'1','C');
		$this->SetXY(166,148);$this->MultiCell(10,9,"61-99",'1','C');

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
		for($i = 0;$i<count($reporte);$i++){
			$fecha_parte = $reporte[$i]['fech']->sec;
			
			for($j = 0;$j<count($reporte[$i]['consulta']);$j++){
				
				
				if(isset($reporte[$i]["consulta"][$j]['paciente']['fecha_na'])){
					$fecha_nacimiento = $reporte[$i]["consulta"][$j]['paciente']['fecha_na']['sec'];
					$edad = $fecha_parte-$fecha_nacimiento;
					$edad = floor($edad/(60*60*24*365));
					
				}else{
					$edad = -1;
				}

				
				if(floatval($reporte[$i]["consulta"][$j]['paciente']['sexo']) == 0 & floatval($reporte[$i]["consulta"][$j]['cate']) != 8 && $reporte[$i]['consulta'][$j]['esta'] != '5' && $reporte[$i]['medico']['_id'] =='587533593e6037474b8b4568' && $reporte[$i]['modulo'] == 'MH'){
					$mujeres_pagante++;
				}
				if(floatval($reporte[$i]["consulta"][$j]['paciente']['sexo']) == 1 & floatval($reporte[$i]["consulta"][$j]['cate']) != 8 && $reporte[$i]['consulta'][$j]['esta'] != '5' && $reporte[$i]['medico']['_id'] =='587533593e6037474b8b4568' && $reporte[$i]['modulo'] == 'MH'){
					$varones_pagante++;
				}
				if(floatval($reporte[$i]["consulta"][$j]['paciente']['sexo']) == 0 & floatval($reporte[$i]["consulta"][$j]['cate']) == 8 && $reporte[$i]['consulta'][$j]['esta'] != '5' && $reporte[$i]['medico']['_id'] =='587533593e6037474b8b4568' && $reporte[$i]['modulo'] == 'MH'){
					$mujeres_indigente++;
				}
				if(floatval($reporte[$i]["consulta"][$j]['paciente']['sexo']) == 1 & floatval($reporte[$i]["consulta"][$j]['cate']) == 8 && $reporte[$i]['consulta'][$j]['esta'] != '5' && $reporte[$i]['medico']['_id'] =='587533593e6037474b8b4568' && $reporte[$i]['modulo'] == 'MH'){
					$varones_indigente++;
				}
				
				if($edad<6 && floatval($reporte[$i]["consulta"][$j]['cate']) != 8 && $reporte[$i]['consulta'][$j]['esta'] != '5' && $reporte[$i]['medico']['_id'] =='587533593e6037474b8b4568' && $reporte[$i]['modulo'] == 'MH'){
					$edad1_p++;
				}

				if($edad>=6 && $edad<11 && floatval($reporte[$i]["consulta"][$j]['cate']) != 8 && $reporte[$i]['consulta'][$j]['esta'] != '5' && $reporte[$i]['medico']['_id'] =='587533593e6037474b8b4568' && $reporte[$i]['modulo'] == 'MH'){
					$edad2_p++;
				}

				if($edad>=11 && $edad<16 && floatval($reporte[$i]["consulta"][$j]['cate']) != 8 && $reporte[$i]['consulta'][$j]['esta'] != '5' && $reporte[$i]['medico']['_id'] =='587533593e6037474b8b4568' && $reporte[$i]['modulo'] == 'MH'){
					$edad3_p++;
				}

				if($edad>=16 && $edad<21 && floatval($reporte[$i]["consulta"][$j]['cate']) != 8 && $reporte[$i]['consulta'][$j]['esta'] != '5' && $reporte[$i]['medico']['_id'] =='587533593e6037474b8b4568' && $reporte[$i]['modulo'] == 'MH'){
					$edad4_p++;
				}
				if($edad>=21 && $edad<31 && floatval($reporte[$i]["consulta"][$j]['cate']) != 8 && $reporte[$i]['consulta'][$j]['esta'] != '5' && $reporte[$i]['medico']['_id'] =='587533593e6037474b8b4568' && $reporte[$i]['modulo'] == 'MH'){
					$edad5_p++;
				}
				if($edad>=31 && $edad<51 && floatval($reporte[$i]["consulta"][$j]['cate']) != 8 && $reporte[$i]['consulta'][$j]['esta'] != '5' && $reporte[$i]['medico']['_id'] =='587533593e6037474b8b4568' && $reporte[$i]['modulo'] == 'MH'){
					$edad6_p++;
				}
				if($edad>=51 && $edad<61 && floatval($reporte[$i]["consulta"][$j]['cate']) != 8 && $reporte[$i]['consulta'][$j]['esta'] != '5' && $reporte[$i]['medico']['_id'] =='587533593e6037474b8b4568' && $reporte[$i]['modulo'] == 'MH'){
					$edad7_p++;
				}
				if($edad>=61 && $edad<100 && floatval($reporte[$i]["consulta"][$j]['cate']) != 8 && $reporte[$i]['consulta'][$j]['esta'] != '5' && $reporte[$i]['medico']['_id'] =='587533593e6037474b8b4568' && $reporte[$i]['modulo'] == 'MH'){
					$edad8_p++;
				}


				if($edad>=0 && $edad<6 && floatval($reporte[$i]["consulta"][$j]['cate']) == 8 && $reporte[$i]['consulta'][$j]['esta'] != '5' && $reporte[$i]['medico']['_id'] =='587533593e6037474b8b4568' && $reporte[$i]['modulo'] == 'MH'){
					$edad1_i++;
				}

				if($edad>=6 && $edad<11 && floatval($reporte[$i]["consulta"][$j]['cate']) == 8 && $reporte[$i]['consulta'][$j]['esta'] != '5' && $reporte[$i]['medico']['_id'] =='587533593e6037474b8b4568' && $reporte[$i]['modulo'] == 'MH'){
					$edad2_i++;
				}

				if($edad>=11 && $edad<16 && floatval($reporte[$i]["consulta"][$j]['cate']) == 8 && $reporte[$i]['consulta'][$j]['esta'] != '5' && $reporte[$i]['medico']['_id'] =='587533593e6037474b8b4568' && $reporte[$i]['modulo'] == 'MH'){
					$edad3_i++;
				}

				if($edad>=16 && $edad<21 && floatval($reporte[$i]["consulta"][$j]['cate']) == 8 && $reporte[$i]['consulta'][$j]['esta'] != '5' && $reporte[$i]['medico']['_id'] =='587533593e6037474b8b4568' && $reporte[$i]['modulo'] == 'MH'){
					$edad4_i++;
				}
				if($edad>=21 && $edad<31 && floatval($reporte[$i]["consulta"][$j]['cate']) == 8 && $reporte[$i]['consulta'][$j]['esta'] != '5' && $reporte[$i]['medico']['_id'] =='587533593e6037474b8b4568' && $reporte[$i]['modulo'] == 'MH'){
					$edad5_i++;
				}
				if($edad>=31 && $edad<51 && floatval($reporte[$i]["consulta"][$j]['cate']) == 8 && $reporte[$i]['consulta'][$j]['esta'] != '5' && $reporte[$i]['medico']['_id'] =='587533593e6037474b8b4568' && $reporte[$i]['modulo'] == 'MH'){
					$edad6_i++;
				}
				if($edad>=51 && $edad<61 && floatval($reporte[$i]["consulta"][$j]['cate']) == 8 && $reporte[$i]['consulta'][$j]['esta'] != '5' && $reporte[$i]['medico']['_id'] =='587533593e6037474b8b4568' && $reporte[$i]['modulo'] == 'MH'){
					$edad7_i++;
				}
				if($edad>=61 && $edad<100 && floatval($reporte[$i]["consulta"][$j]['cate']) == 8 && $reporte[$i]['consulta'][$j]['esta'] != '5' && $reporte[$i]['medico']['_id'] =='587533593e6037474b8b4568' && $reporte[$i]['modulo'] == 'MH'){
					$edad8_i++;
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
			$this->SetXY(62,157);$this->MultiCell(11,9,"".$varones_indigente,'1','C');
			$this->SetXY(62,166);$this->MultiCell(11,9,"".$varones_pagante,'1','C');
			$this->SetFont('Arial','B',9);
			$this->SetXY(62,175);$this->MultiCell(11,9,"".$total_varones,'1','C');
			$this->SetFont('Arial','',9);
			//-------------------------------------------------------------------\\
			$this->SetXY(96,157);$this->MultiCell(10,9,"".$edad1_i,'1','C');
			$this->SetXY(106,157);$this->MultiCell(10,9,"".$edad2_i,'1','C');
			$this->SetXY(116,157);$this->MultiCell(10,9,"".$edad3_i,'1','C');
			$this->SetXY(126,157);$this->MultiCell(10,9,"".$edad4_i,'1','C');
			$this->SetXY(136,157);$this->MultiCell(10,9,"".$edad5_i,'1','C');
			$this->SetXY(146,157);$this->MultiCell(10,9,"".$edad6_i,'1','C');
			$this->SetXY(156,157);$this->MultiCell(10,9,"".$edad7_i,'1','C');
			$this->SetXY(166,157);$this->MultiCell(10,9,"".$edad8_i,'1','C');
			//-------------------------------------------------------------------\\
			$this->SetXY(96,166);$this->MultiCell(10,9,"".$edad1_p,'1','C');
			$this->SetXY(106,166);$this->MultiCell(10,9,"".$edad2_p,'1','C');
			$this->SetXY(116,166);$this->MultiCell(10,9,"".$edad3_p,'1','C');
			$this->SetXY(126,166);$this->MultiCell(10,9,"".$edad4_p,'1','C');
			$this->SetXY(136,166);$this->MultiCell(10,9,"".$edad5_p,'1','C');
			$this->SetXY(146,166);$this->MultiCell(10,9,"".$edad6_p,'1','C');
			$this->SetXY(156,166);$this->MultiCell(10,9,"".$edad7_p,'1','C');
			$this->SetXY(166,166);$this->MultiCell(10,9,"".$edad8_p,'1','C');
			//-------------------------------------------------------------------\\
			$this->SetFont('Arial','B',9);
			$this->SetXY(96,175);$this->MultiCell(10,9,"".$total_edad1,'1','C');
			$this->SetXY(106,175);$this->MultiCell(10,9,"".$total_edad2,'1','C');
			$this->SetXY(116,175);$this->MultiCell(10,9,"".$total_edad3,'1','C');
			$this->SetXY(126,175);$this->MultiCell(10,9,"".$total_edad4,'1','C');
			$this->SetXY(136,175);$this->MultiCell(10,9,"".$total_edad5,'1','C');
			$this->SetXY(146,175);$this->MultiCell(10,9,"".$total_edad6,'1','C');
			$this->SetXY(156,175);$this->MultiCell(10,9,"".$total_edad7,'1','C');
			$this->SetXY(166,175);$this->MultiCell(10,9,"".$total_edad8,'1','C');
			$this->SetFont('Arial','',9);
			//-------------------------------------------------------------------\\
			$this->SetXY(73,157);$this->MultiCell(11,9,"".$mujeres_indigente,'1','C');
			$this->SetXY(73,166);$this->MultiCell(11,9,"".$mujeres_pagante,'1','C');
			$this->SetFont('Arial','B',9);
			$this->SetXY(73,175);$this->MultiCell(11,9,"".$total_mujeres,'1','C');
			//-------------------------------------------------------------------\\
			$this->SetXY(84,157);$this->MultiCell(12,9,"".$total_sexo_indigente,'1','C');
			$this->SetXY(84,166);$this->MultiCell(12,9,"".$total_sexo_pagante,'1','C');
			$this->SetXY(84,175);$this->MultiCell(12,9,"".$total,'1','C');
			//-------------------------------------------------------------------\\
			$this->AddPage();

		$x=5;
		$y=25;
		$y_ini = $y;
		$page_b = 275;
		
//CABECERA

//CABECERA
		$y=$y+25;
		$this->SetFont('Arial','B',10);
		$this->SetXY(5,30);$this->MultiCell(200,5,	"REPORTE MENSUAL DE CONSULTAS DE CONSULTORIOS EXTERNOS",'0','C');
		$this->SetXY(18,50);$this->MultiCell(172,5,"INTER - CONSULTAS DE ADICCIONES",'0','L');
		$this->SetFont('Arial','B',7);
		$this->SetXY(5,35);$this->MultiCell(180,5,"DRA. MILAGROS ELIZABETH REVILLA LOAYZA",'0','C');
		$this->SetFont('Arial','B',8);
		//PRIMERA COLUMNA
		$this->SetXY(20,$y+11);$this->MultiCell(42,9,"NUMERO DE BENEFCIARIOS",'1','C');
		$this->SetXY(20,70);$this->MultiCell(42,9,"APOYO EN SALUD",'1','C');
		$this->SetXY(20,79);$this->MultiCell(42,9,"BENEFICIARIO INDIGENTE",'1','C');
		$this->SetXY(20,88);$this->MultiCell(42,9,"BENEFICIARIO PAGANTE",'1','C');
		$this->SetXY(20,97);$this->MultiCell(42,9,"TOTAL",'1','C');
		//SEGUNDA COLUMNA
		$this->SetXY(62,$y+11);$this->MultiCell(22,9,"SEGUN SEXO",'1','C');
		$this->SetXY(62,70);$this->MultiCell(11,9,"M",'1','C');
		$this->SetXY(73,70);$this->MultiCell(11,9,"F",'1','C');
		//TERCERA COLUMNA
		$this->SetXY(84,$y+11);$this->MultiCell(12,9,"TOTAL",'1','C');
		$this->SetXY(84,70);$this->MultiCell(12,9,"-------",'1','C');
		//CUARTA COLUMNA
		$this->SetXY(96,$y+11);$this->MultiCell(80,9,"SEGUN SU EDAD",'1','C');
		$this->SetXY(96,70);$this->MultiCell(10,9,"0-5",'1','C');
		$this->SetXY(106,70);$this->MultiCell(10,9,"6-10",'1','C');
		$this->SetXY(116,70);$this->MultiCell(10,9,"11-15",'1','C');
		$this->SetXY(126,70);$this->MultiCell(10,9,"16-20",'1','C');
		$this->SetXY(136,70);$this->MultiCell(10,9,"21-30",'1','C');
		$this->SetXY(146,70);$this->MultiCell(10,9,"31-50",'1','C');
		$this->SetXY(156,70);$this->MultiCell(10,9,"51-60",'1','C');
		$this->SetXY(166,70);$this->MultiCell(10,9,"61-99",'1','C');

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
		for($i = 0;$i<count($reporte);$i++){
			$fecha_parte = $reporte[$i]['fech']->sec;
			
			for($j = 0;$j<count($reporte[$i]['consulta']);$j++){
				
				
				if(isset($reporte[$i]["consulta"][$j]['paciente']['fecha_na'])){
					$fecha_nacimiento = $reporte[$i]["consulta"][$j]['paciente']['fecha_na']['sec'];
					$edad = $fecha_parte-$fecha_nacimiento;
					$edad = floor($edad/(60*60*24*365));
					
				}else{
					$edad = -1;
				}

				
				if(floatval($reporte[$i]["consulta"][$j]['paciente']['sexo']) == 0 & floatval($reporte[$i]["consulta"][$j]['cate']) != 8 && $reporte[$i]['consulta'][$j]['esta'] == '5' && $reporte[$i]['medico']['_id'] =='587533593e6037474b8b4568' && $reporte[$i]['modulo'] == 'AD'){
					$mujeres_pagante++;
				}
				if(floatval($reporte[$i]["consulta"][$j]['paciente']['sexo']) == 1 & floatval($reporte[$i]["consulta"][$j]['cate']) != 8 && $reporte[$i]['consulta'][$j]['esta'] == '5' && $reporte[$i]['medico']['_id'] =='587533593e6037474b8b4568' && $reporte[$i]['modulo'] == 'AD'){
					$varones_pagante++;
				}
				if(floatval($reporte[$i]["consulta"][$j]['paciente']['sexo']) == 0 & floatval($reporte[$i]["consulta"][$j]['cate']) == 8 && $reporte[$i]['consulta'][$j]['esta'] == '5' && $reporte[$i]['medico']['_id'] =='587533593e6037474b8b4568' && $reporte[$i]['modulo'] == 'AD'){
					$mujeres_indigente++;
				}
				if(floatval($reporte[$i]["consulta"][$j]['paciente']['sexo']) == 1 & floatval($reporte[$i]["consulta"][$j]['cate']) == 8 && $reporte[$i]['consulta'][$j]['esta'] == '5' && $reporte[$i]['medico']['_id'] =='587533593e6037474b8b4568' && $reporte[$i]['modulo'] == 'AD'){
					$varones_indigente++;
				}
				
				if($edad<6 && floatval($reporte[$i]["consulta"][$j]['cate']) != 8 && $reporte[$i]['consulta'][$j]['esta'] == '5' && $reporte[$i]['medico']['_id'] =='587533593e6037474b8b4568' && $reporte[$i]['modulo'] == 'AD'){
					$edad1_p++;
				}

				if($edad>=6 && $edad<11 && floatval($reporte[$i]["consulta"][$j]['cate']) != 8 && $reporte[$i]['consulta'][$j]['esta'] == '5' && $reporte[$i]['medico']['_id'] =='587533593e6037474b8b4568' && $reporte[$i]['modulo'] == 'AD'){
					$edad2_p++;
				}

				if($edad>=11 && $edad<16 && floatval($reporte[$i]["consulta"][$j]['cate']) != 8 && $reporte[$i]['consulta'][$j]['esta'] == '5' && $reporte[$i]['medico']['_id'] =='587533593e6037474b8b4568' && $reporte[$i]['modulo'] == 'AD'){
					$edad3_p++;
				}

				if($edad>=16 && $edad<21 && floatval($reporte[$i]["consulta"][$j]['cate']) != 8 && $reporte[$i]['consulta'][$j]['esta'] == '5' && $reporte[$i]['medico']['_id'] =='587533593e6037474b8b4568' && $reporte[$i]['modulo'] == 'AD'){
					$edad4_p++;
				}
				if($edad>=21 && $edad<31 && floatval($reporte[$i]["consulta"][$j]['cate']) != 8 && $reporte[$i]['consulta'][$j]['esta'] == '5' && $reporte[$i]['medico']['_id'] =='587533593e6037474b8b4568' && $reporte[$i]['modulo'] == 'AD'){
					$edad5_p++;
				}
				if($edad>=31 && $edad<51 && floatval($reporte[$i]["consulta"][$j]['cate']) != 8 && $reporte[$i]['consulta'][$j]['esta'] == '5' && $reporte[$i]['medico']['_id'] =='587533593e6037474b8b4568' && $reporte[$i]['modulo'] == 'AD'){
					$edad6_p++;
				}
				if($edad>=51 && $edad<61 && floatval($reporte[$i]["consulta"][$j]['cate']) != 8 && $reporte[$i]['consulta'][$j]['esta'] == '5' && $reporte[$i]['medico']['_id'] =='587533593e6037474b8b4568' && $reporte[$i]['modulo'] == 'AD'){
					$edad7_p++;
				}
				if($edad>=61 && $edad<100 && floatval($reporte[$i]["consulta"][$j]['cate']) != 8 && $reporte[$i]['consulta'][$j]['esta'] == '5' && $reporte[$i]['medico']['_id'] =='587533593e6037474b8b4568' && $reporte[$i]['modulo'] == 'AD'){
					$edad8_p++;
				}


				if($edad>=0 && $edad<6 && floatval($reporte[$i]["consulta"][$j]['cate']) == 8 && $reporte[$i]['consulta'][$j]['esta'] == '5' && $reporte[$i]['medico']['_id'] =='587533593e6037474b8b4568' && $reporte[$i]['modulo'] == 'AD'){
					$edad1_i++;
				}

				if($edad>=6 && $edad<11 && floatval($reporte[$i]["consulta"][$j]['cate']) == 8 && $reporte[$i]['consulta'][$j]['esta'] == '5' && $reporte[$i]['medico']['_id'] =='587533593e6037474b8b4568' && $reporte[$i]['modulo'] == 'AD'){
					$edad2_i++;
				}

				if($edad>=11 && $edad<16 && floatval($reporte[$i]["consulta"][$j]['cate']) == 8 && $reporte[$i]['consulta'][$j]['esta'] == '5' && $reporte[$i]['medico']['_id'] =='587533593e6037474b8b4568' && $reporte[$i]['modulo'] == 'AD'){
					$edad3_i++;
				}

				if($edad>=16 && $edad<21 && floatval($reporte[$i]["consulta"][$j]['cate']) == 8 && $reporte[$i]['consulta'][$j]['esta'] == '5' && $reporte[$i]['medico']['_id'] =='587533593e6037474b8b4568' && $reporte[$i]['modulo'] == 'AD'){
					$edad4_i++;
				}
				if($edad>=21 && $edad<31 && floatval($reporte[$i]["consulta"][$j]['cate']) == 8 && $reporte[$i]['consulta'][$j]['esta'] == '5' && $reporte[$i]['medico']['_id'] =='587533593e6037474b8b4568' && $reporte[$i]['modulo'] == 'AD'){
					$edad5_i++;
				}
				if($edad>=31 && $edad<51 && floatval($reporte[$i]["consulta"][$j]['cate']) == 8 && $reporte[$i]['consulta'][$j]['esta'] == '5' && $reporte[$i]['medico']['_id'] =='587533593e6037474b8b4568' && $reporte[$i]['modulo'] == 'AD'){
					$edad6_i++;
				}
				if($edad>=51 && $edad<61 && floatval($reporte[$i]["consulta"][$j]['cate']) == 8 && $reporte[$i]['consulta'][$j]['esta'] == '5' && $reporte[$i]['medico']['_id'] =='587533593e6037474b8b4568' && $reporte[$i]['modulo'] == 'AD'){
					$edad7_i++;
				}
				if($edad>=61 && $edad<100 && floatval($reporte[$i]["consulta"][$j]['cate']) == 8 && $reporte[$i]['consulta'][$j]['esta'] == '5' && $reporte[$i]['medico']['_id'] =='587533593e6037474b8b4568' && $reporte[$i]['modulo'] == 'AD'){
					$edad8_i++;
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
			$this->SetXY(62,79);$this->MultiCell(11,9,"".$varones_indigente,'1','C');
			$this->SetXY(62,88);$this->MultiCell(11,9,"".$varones_pagante,'1','C');
			$this->SetFont('Arial','B',9);
			$this->SetXY(62,97);$this->MultiCell(11,9,"".$total_varones,'1','C');
			$this->SetFont('Arial','',9);
			//-------------------------------------------------------------------\\
			$this->SetXY(96,79);$this->MultiCell(10,9,"".$edad1_i,'1','C');
			$this->SetXY(106,79);$this->MultiCell(10,9,"".$edad2_i,'1','C');
			$this->SetXY(116,79);$this->MultiCell(10,9,"".$edad3_i,'1','C');
			$this->SetXY(126,79);$this->MultiCell(10,9,"".$edad4_i,'1','C');
			$this->SetXY(136,79);$this->MultiCell(10,9,"".$edad5_i,'1','C');
			$this->SetXY(146,79);$this->MultiCell(10,9,"".$edad6_i,'1','C');
			$this->SetXY(156,79);$this->MultiCell(10,9,"".$edad7_i,'1','C');
			$this->SetXY(166,79);$this->MultiCell(10,9,"".$edad8_i,'1','C');
			//-------------------------------------------------------------------\\
			$this->SetXY(96,88);$this->MultiCell(10,9,"".$edad1_p,'1','C');
			$this->SetXY(106,88);$this->MultiCell(10,9,"".$edad2_p,'1','C');
			$this->SetXY(116,88);$this->MultiCell(10,9,"".$edad3_p,'1','C');
			$this->SetXY(126,88);$this->MultiCell(10,9,"".$edad4_p,'1','C');
			$this->SetXY(136,88);$this->MultiCell(10,9,"".$edad5_p,'1','C');
			$this->SetXY(146,88);$this->MultiCell(10,9,"".$edad6_p,'1','C');
			$this->SetXY(156,88);$this->MultiCell(10,9,"".$edad7_p,'1','C');
			$this->SetXY(166,88);$this->MultiCell(10,9,"".$edad8_p,'1','C');
			//-------------------------------------------------------------------\\
			$this->SetFont('Arial','B',9);
			$this->SetXY(96,97);$this->MultiCell(10,9,"".$total_edad1,'1','C');
			$this->SetXY(106,97);$this->MultiCell(10,9,"".$total_edad2,'1','C');
			$this->SetXY(116,97);$this->MultiCell(10,9,"".$total_edad3,'1','C');
			$this->SetXY(126,97);$this->MultiCell(10,9,"".$total_edad4,'1','C');
			$this->SetXY(136,97);$this->MultiCell(10,9,"".$total_edad5,'1','C');
			$this->SetXY(146,97);$this->MultiCell(10,9,"".$total_edad6,'1','C');
			$this->SetXY(156,97);$this->MultiCell(10,9,"".$total_edad7,'1','C');
			$this->SetXY(166,97);$this->MultiCell(10,9,"".$total_edad8,'1','C');
			$this->SetFont('Arial','',9);
			//-------------------------------------------------------------------\\
			$this->SetXY(73,79);$this->MultiCell(11,9,"".$mujeres_indigente,'1','C');
			$this->SetXY(73,88);$this->MultiCell(11,9,"".$mujeres_pagante,'1','C');
			$this->SetFont('Arial','B',9);
			$this->SetXY(73,97);$this->MultiCell(11,9,"".$total_mujeres,'1','C');
			//-------------------------------------------------------------------\\
			$this->SetXY(84,79);$this->MultiCell(12,9,"".$total_sexo_indigente,'1','C');
			$this->SetXY(84,88);$this->MultiCell(12,9,"".$total_sexo_pagante,'1','C');
			$this->SetXY(84,97);$this->MultiCell(12,9,"".$total,'1','C');
			//-------------------------------------------------------------------\\
		$y=$y+80;
		$this->SetFont('Arial','B',10);
		$this->SetXY(18,124);$this->MultiCell(180,5,"INTER - CONSULTAS DE SALUD MENTAL",'0','L');
		$this->SetFont('Arial','B',7);
		
		$this->SetFont('Arial','B',8);
		//PRIMERA COLUMNA
		$this->SetXY(20,$y);$this->MultiCell(42,9,"NUMERO DE BENEFCIARIOS",'1','C');
		$this->SetXY(20,148);$this->MultiCell(42,9,"APOYO EN SALUD",'1','C');
		$this->SetXY(20,157);$this->MultiCell(42,9,"BENEFICIARIO INDIGENTE",'1','C');
		$this->SetXY(20,166);$this->MultiCell(42,9,"BENEFICIARIO PAGANTE",'1','C');
		$this->SetXY(20,175);$this->MultiCell(42,9,"TOTAL",'1','C');
		//SEGUNDA COLUMNA
		$this->SetXY(62,$y);$this->MultiCell(22,9,"SEGUN SEXO",'1','C');
		$this->SetXY(62,148);$this->MultiCell(11,9,"M",'1','C');
		$this->SetXY(73,148);$this->MultiCell(11,9,"F",'1','C');
		//TERCERA COLUMNA
		$this->SetXY(84,$y);$this->MultiCell(12,9,"TOTAL",'1','C');
		$this->SetXY(84,148);$this->MultiCell(12,9,"-------",'1','C');
		//CUARTA COLUMNA
		$this->SetXY(96,$y);$this->MultiCell(80,9,"SEGUN SU EDAD",'1','C');
		$this->SetXY(96,148);$this->MultiCell(10,9,"0-5",'1','C');
		$this->SetXY(106,148);$this->MultiCell(10,9,"6-10",'1','C');
		$this->SetXY(116,148);$this->MultiCell(10,9,"11-15",'1','C');
		$this->SetXY(126,148);$this->MultiCell(10,9,"16-20",'1','C');
		$this->SetXY(136,148);$this->MultiCell(10,9,"21-30",'1','C');
		$this->SetXY(146,148);$this->MultiCell(10,9,"31-50",'1','C');
		$this->SetXY(156,148);$this->MultiCell(10,9,"51-60",'1','C');
		$this->SetXY(166,148);$this->MultiCell(10,9,"61-99",'1','C');

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
		for($i = 0;$i<count($reporte);$i++){
			$fecha_parte = $reporte[$i]['fech']->sec;
			
			for($j = 0;$j<count($reporte[$i]['consulta']);$j++){
				
				
				if(isset($reporte[$i]["consulta"][$j]['paciente']['fecha_na'])){
					$fecha_nacimiento = $reporte[$i]["consulta"][$j]['paciente']['fecha_na']['sec'];
					$edad = $fecha_parte-$fecha_nacimiento;
					$edad = floor($edad/(60*60*24*365));
					
				}else{
					$edad = -1;
				}

				
				if(floatval($reporte[$i]["consulta"][$j]['paciente']['sexo']) == 0 & floatval($reporte[$i]["consulta"][$j]['cate']) != 8 && $reporte[$i]['consulta'][$j]['esta'] == '5' && $reporte[$i]['medico']['_id'] =='587533593e6037474b8b4568' && $reporte[$i]['modulo'] == 'MH'){
					$mujeres_pagante++;
				}
				if(floatval($reporte[$i]["consulta"][$j]['paciente']['sexo']) == 1 & floatval($reporte[$i]["consulta"][$j]['cate']) != 8 && $reporte[$i]['consulta'][$j]['esta'] == '5' && $reporte[$i]['medico']['_id'] =='587533593e6037474b8b4568' && $reporte[$i]['modulo'] == 'MH'){
					$varones_pagante++;
				}
				if(floatval($reporte[$i]["consulta"][$j]['paciente']['sexo']) == 0 & floatval($reporte[$i]["consulta"][$j]['cate']) == 8 && $reporte[$i]['consulta'][$j]['esta'] == '5' && $reporte[$i]['medico']['_id'] =='587533593e6037474b8b4568' && $reporte[$i]['modulo'] == 'MH'){
					$mujeres_indigente++;
				}
				if(floatval($reporte[$i]["consulta"][$j]['paciente']['sexo']) == 1 & floatval($reporte[$i]["consulta"][$j]['cate']) == 8 && $reporte[$i]['consulta'][$j]['esta'] == '5' && $reporte[$i]['medico']['_id'] =='587533593e6037474b8b4568' && $reporte[$i]['modulo'] == 'MH'){
					$varones_indigente++;
				}
				
				if($edad<6 && floatval($reporte[$i]["consulta"][$j]['cate']) != 8 && $reporte[$i]['consulta'][$j]['esta'] == '5' && $reporte[$i]['medico']['_id'] =='587533593e6037474b8b4568' && $reporte[$i]['modulo'] == 'MH'){
					$edad1_p++;
				}

				if($edad>=6 && $edad<11 && floatval($reporte[$i]["consulta"][$j]['cate']) != 8 && $reporte[$i]['consulta'][$j]['esta'] == '5' && $reporte[$i]['medico']['_id'] =='587533593e6037474b8b4568' && $reporte[$i]['modulo'] == 'MH'){
					$edad2_p++;
				}

				if($edad>=11 && $edad<16 && floatval($reporte[$i]["consulta"][$j]['cate']) != 8 && $reporte[$i]['consulta'][$j]['esta'] == '5' && $reporte[$i]['medico']['_id'] =='587533593e6037474b8b4568' && $reporte[$i]['modulo'] == 'MH'){
					$edad3_p++;
				}

				if($edad>=16 && $edad<21 && floatval($reporte[$i]["consulta"][$j]['cate']) != 8 && $reporte[$i]['consulta'][$j]['esta'] == '5' && $reporte[$i]['medico']['_id'] =='587533593e6037474b8b4568' && $reporte[$i]['modulo'] == 'MH'){
					$edad4_p++;
				}
				if($edad>=21 && $edad<31 && floatval($reporte[$i]["consulta"][$j]['cate']) != 8 && $reporte[$i]['consulta'][$j]['esta'] == '5' && $reporte[$i]['medico']['_id'] =='587533593e6037474b8b4568' && $reporte[$i]['modulo'] == 'MH'){
					$edad5_p++;
				}
				if($edad>=31 && $edad<51 && floatval($reporte[$i]["consulta"][$j]['cate']) != 8 && $reporte[$i]['consulta'][$j]['esta'] == '5' && $reporte[$i]['medico']['_id'] =='587533593e6037474b8b4568' && $reporte[$i]['modulo'] == 'MH'){
					$edad6_p++;
				}
				if($edad>=51 && $edad<61 && floatval($reporte[$i]["consulta"][$j]['cate']) != 8 && $reporte[$i]['consulta'][$j]['esta'] == '5' && $reporte[$i]['medico']['_id'] =='587533593e6037474b8b4568' && $reporte[$i]['modulo'] == 'MH'){
					$edad7_p++;
				}
				if($edad>=61 && $edad<100 && floatval($reporte[$i]["consulta"][$j]['cate']) != 8 && $reporte[$i]['consulta'][$j]['esta'] == '5' && $reporte[$i]['medico']['_id'] =='587533593e6037474b8b4568' && $reporte[$i]['modulo'] == 'MH'){
					$edad8_p++;
				}


				if($edad>=0 && $edad<6 && floatval($reporte[$i]["consulta"][$j]['cate']) == 8 && $reporte[$i]['consulta'][$j]['esta'] == '5' && $reporte[$i]['medico']['_id'] =='587533593e6037474b8b4568' && $reporte[$i]['modulo'] == 'MH'){
					$edad1_i++;
				}

				if($edad>=6 && $edad<11 && floatval($reporte[$i]["consulta"][$j]['cate']) == 8 && $reporte[$i]['consulta'][$j]['esta'] == '5' && $reporte[$i]['medico']['_id'] =='587533593e6037474b8b4568' && $reporte[$i]['modulo'] == 'MH'){
					$edad2_i++;
				}

				if($edad>=11 && $edad<16 && floatval($reporte[$i]["consulta"][$j]['cate']) == 8 && $reporte[$i]['consulta'][$j]['esta'] == '5' && $reporte[$i]['medico']['_id'] =='587533593e6037474b8b4568' && $reporte[$i]['modulo'] == 'MH'){
					$edad3_i++;
				}

				if($edad>=16 && $edad<21 && floatval($reporte[$i]["consulta"][$j]['cate']) == 8 && $reporte[$i]['consulta'][$j]['esta'] == '5' && $reporte[$i]['medico']['_id'] =='587533593e6037474b8b4568' && $reporte[$i]['modulo'] == 'MH'){
					$edad4_i++;
				}
				if($edad>=21 && $edad<31 && floatval($reporte[$i]["consulta"][$j]['cate']) == 8 && $reporte[$i]['consulta'][$j]['esta'] == '5' && $reporte[$i]['medico']['_id'] =='587533593e6037474b8b4568' && $reporte[$i]['modulo'] == 'MH'){
					$edad5_i++;
				}
				if($edad>=31 && $edad<51 && floatval($reporte[$i]["consulta"][$j]['cate']) == 8 && $reporte[$i]['consulta'][$j]['esta'] == '5' && $reporte[$i]['medico']['_id'] =='587533593e6037474b8b4568' && $reporte[$i]['modulo'] == 'MH'){
					$edad6_i++;
				}
				if($edad>=51 && $edad<61 && floatval($reporte[$i]["consulta"][$j]['cate']) == 8 && $reporte[$i]['consulta'][$j]['esta'] == '5' && $reporte[$i]['medico']['_id'] =='587533593e6037474b8b4568' && $reporte[$i]['modulo'] == 'MH'){
					$edad7_i++;
				}
				if($edad>=61 && $edad<100 && floatval($reporte[$i]["consulta"][$j]['cate']) == 8 && $reporte[$i]['consulta'][$j]['esta'] == '5' && $reporte[$i]['medico']['_id'] =='587533593e6037474b8b4568' && $reporte[$i]['modulo'] == 'MH'){
					$edad8_i++;
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
			$this->SetXY(62,157);$this->MultiCell(11,9,"".$varones_indigente,'1','C');
			$this->SetXY(62,166);$this->MultiCell(11,9,"".$varones_pagante,'1','C');
			$this->SetFont('Arial','B',9);
			$this->SetXY(62,175);$this->MultiCell(11,9,"".$total_varones,'1','C');
			$this->SetFont('Arial','',9);
			//-------------------------------------------------------------------\\
			$this->SetXY(96,157);$this->MultiCell(10,9,"".$edad1_i,'1','C');
			$this->SetXY(106,157);$this->MultiCell(10,9,"".$edad2_i,'1','C');
			$this->SetXY(116,157);$this->MultiCell(10,9,"".$edad3_i,'1','C');
			$this->SetXY(126,157);$this->MultiCell(10,9,"".$edad4_i,'1','C');
			$this->SetXY(136,157);$this->MultiCell(10,9,"".$edad5_i,'1','C');
			$this->SetXY(146,157);$this->MultiCell(10,9,"".$edad6_i,'1','C');
			$this->SetXY(156,157);$this->MultiCell(10,9,"".$edad7_i,'1','C');
			$this->SetXY(166,157);$this->MultiCell(10,9,"".$edad8_i,'1','C');
			//-------------------------------------------------------------------\\
			$this->SetXY(96,166);$this->MultiCell(10,9,"".$edad1_p,'1','C');
			$this->SetXY(106,166);$this->MultiCell(10,9,"".$edad2_p,'1','C');
			$this->SetXY(116,166);$this->MultiCell(10,9,"".$edad3_p,'1','C');
			$this->SetXY(126,166);$this->MultiCell(10,9,"".$edad4_p,'1','C');
			$this->SetXY(136,166);$this->MultiCell(10,9,"".$edad5_p,'1','C');
			$this->SetXY(146,166);$this->MultiCell(10,9,"".$edad6_p,'1','C');
			$this->SetXY(156,166);$this->MultiCell(10,9,"".$edad7_p,'1','C');
			$this->SetXY(166,166);$this->MultiCell(10,9,"".$edad8_p,'1','C');
			//-------------------------------------------------------------------\\
			$this->SetFont('Arial','B',9);
			$this->SetXY(96,175);$this->MultiCell(10,9,"".$total_edad1,'1','C');
			$this->SetXY(106,175);$this->MultiCell(10,9,"".$total_edad2,'1','C');
			$this->SetXY(116,175);$this->MultiCell(10,9,"".$total_edad3,'1','C');
			$this->SetXY(126,175);$this->MultiCell(10,9,"".$total_edad4,'1','C');
			$this->SetXY(136,175);$this->MultiCell(10,9,"".$total_edad5,'1','C');
			$this->SetXY(146,175);$this->MultiCell(10,9,"".$total_edad6,'1','C');
			$this->SetXY(156,175);$this->MultiCell(10,9,"".$total_edad7,'1','C');
			$this->SetXY(166,175);$this->MultiCell(10,9,"".$total_edad8,'1','C');
			$this->SetFont('Arial','',9);
			//-------------------------------------------------------------------\\
			$this->SetXY(73,157);$this->MultiCell(11,9,"".$mujeres_indigente,'1','C');
			$this->SetXY(73,166);$this->MultiCell(11,9,"".$mujeres_pagante,'1','C');
			$this->SetFont('Arial','B',9);
			$this->SetXY(73,175);$this->MultiCell(11,9,"".$total_mujeres,'1','C');
			//-------------------------------------------------------------------\\
			$this->SetXY(84,157);$this->MultiCell(12,9,"".$total_sexo_indigente,'1','C');
			$this->SetXY(84,166);$this->MultiCell(12,9,"".$total_sexo_pagante,'1','C');
			$this->SetXY(84,175);$this->MultiCell(12,9,"".$total,'1','C');
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
$pdf->Publicar($reporte);
$pdf->SetLeftMargin(25);
//header('Content-Type: application/pdf; charset=utf-8');
$pdf->Output();

?>