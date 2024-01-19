<?php
global $f;
$f->library('pdf');

class repo extends FPDF
{
	var $params;
	function setParams($params){
		$this->params = $params;
	}
	function Header(){
		//$this->Image(IndexPath.DS.'templates/ts/comprobante.gif',10,10,190,275);	
		$meses = array("","Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre");
		$y=10;
		$this->SetFont('Arial','B',9);
		$this->SetXY(10,5);$this->MultiCell(60,5,"SOCIEDAD DE BENEFICENCIA PUBLICA DE AREQUIPA",'0','C');		
		$this->SetXY(10,5);$this->MultiCell(190,5,date("d/m/Y")." Página: ".$this->PageNo()."/{nb}",'0','R');
		$this->SetFont('Arial','',9);
		$this->SetXY(10,15);$this->MultiCell(60,5,"Centro de Salud Mental",'0','C');
		$this->SetXY(10,20);$this->MultiCell(60,5,"'Moises Heresi'",'0','C');
		$this->SetFont('Arial','B',13);
		$this->SetXY(5,25);$this->MultiCell(200,5,	"REPORTE ESTADISTICO MENSUAL POR DOCTOR",'0','C');
		$this->SetFont('Arial','B',10);
		$this->SetXY(10,35);$this->MultiCell(200,5,	"A: 				Medico Jefe del C:S:M:M",'0','L');
		$this->SetXY(10,40);$this->MultiCell(200,5,	"DE: 				Enfermera Consultorio Externo",'0','L');
		$this->SetXY(10,45);$this->MultiCell(200,5,	"ASUNTO: 				 Estadistica del mes de ".$meses[floatval($this->params['mes'])]." del ".$this->params['ano'],'0','L');
	}
	function getAge($birthday) {
		  $birth = $birthday;
		  $now = strtotime('now');
		  $age = ($now - $birth) / 31536000;
		  return floor($age);
	}
	function Publicar($diario, $params){
		$this->SetFont('Arial','B',10);
		$this->SetXY(10,49);$this->MultiCell(200,5,	"PABELLLON: 				Salud Mental",'0','L');
		$x=5;
		$y=25;
		$y_ini = $y;
		$page_b = 275;
		$y=$y+35;
		/*ALVARADO*/
		$mujeres_A = 0;
		$varones_A = 0;
		$nuevos_A = 0;
		$continuadores_A = 0;
		$niños_A = 0;
		$jovenes_A = 0;
		$adulto_A = 0;
		$anciano_A = 0;
		/*ALVARADO 1*/
		/*--------*/
		$mujeres_A_1 = 0;
		$varones_A_1 = 0;
		$nuevos_A_1 = 0;
		$continuadores_A_1 = 0;
		$niños_A_1 = 0;
		$jovenes_A_1 = 0;
		$adulto_A_1 = 0;
		$anciano_A_1 = 0;
		/*SANCHEZ*/
		$mujeres_SA = 0;
		$varones_SA = 0;
		$nuevos_SA = 0;
		$continuadores_SA = 0;
		$niños_SA = 0;
		$jovenes_SA = 0;
		$adulto_SA = 0;
		$anciano_SA = 0;
		/*SANCHEZ (1)*/
		$mujeres_SA_1 = 0;
		$varones_SA_1 = 0;
		$nuevos_SA_1 = 0;
		$continuadores_SA_1 = 0;
		$niños_SA_1 = 0;
		$jovenes_SA_1 = 0;
		$adulto_SA_1 = 0;
		$anciano_SA_1 = 0;
		/*GONZALES*/
		$mujeres_S = 0;
		$varones_S = 0;
		$nuevos_S = 0;
		$continuadores_S = 0;
		$niños_S = 0;
		$jovenes_S = 0;
		$adulto_S = 0;
		$anciano_S = 0;
		/*PAREDES*/
		$mujeres_P = 0;
		$varones_P = 0;
		$nuevos_P = 0;
		$continuadores_P = 0;
		$niños_P = 0;
		$jovenes_P = 0;
		$adulto_P = 0;
		$anciano_P = 0;
		/*RAMIREZ*/
		$mujeres_R = 0;
		$varones_R = 0;
		$nuevos_R = 0;
		$continuadores_R = 0;
		$niños_R = 0;
		$jovenes_R = 0;
		$adulto_R = 0;
		$anciano_R = 0;
		/*TICONA*/
		$mujeres_T = 0;
		$varones_T = 0;
		$nuevos_T = 0;
		$continuadores_T = 0;
		$niños_T = 0;
		$jovenes_T = 0;
		$adulto_T = 0;
		$anciano_T = 0;
		/*ESQUIVIAS*/
		$mujeres_E = 0;
		$varones_E = 0;
		$nuevos_E = 0;
		$continuadores_E = 0;
		$niños_E = 0;
		$jovenes_E = 0;
		$adulto_E = 0;
		$anciano_E = 0;
		/*ESQUIVIAS*/
		$mujeres_E_1 = 0;
		$varones_E_1 = 0;
		$nuevos_E_1 = 0;
		$continuadores_E_1 = 0;
		$niños_E_1 = 0;
		$jovenes_E_1 = 0;
		$adulto_E_1 = 0;
		$anciano_E_1 = 0;
		/*REVILLA*/
		$mujeres_RE = 0;
		$varones_RE = 0;
		$nuevos_RE = 0;
		$continuadores_RE = 0;
		$niños_RE = 0;
		$jovenes_RE = 0;
		$adulto_RE = 0;
		$anciano_RE = 0;

		for($i = 0;$i<count($diario);$i++){
			
			for($j = 0;$j<count($diario[$i]['consulta']);$j++){

			if(isset($diario[$i]["consulta"][$j]['paciente']['fecha_na'])){
					$fechaarte = $diario[$i]['fech']->sec;

					if(isset($diario[$i]["consulta"][$j]['paciente']['fecha_na']['sec'])){
						$fecha_nacimiento = $diario[$i]["consulta"][$j]['paciente']['fecha_na']['sec'];
					}else{
						$fecha_nacimiento = $diario[$i]["consulta"][$j]['paciente']['fecha_na']->sec;	
					}
					
					$edad = $fechaarte-$fecha_nacimiento;
					$edad = floor($edad/(60*60*24*365));

				}else{
					
					$edad = -1;
					
				}
				//ALVARADO
				if(floatval($diario[$i]["consulta"][$j]['paciente']['sexo']) == 0 && $diario[$i]['consulta'][$j]['esta'] != '5' && $diario[$i]['medico']['_id'] =='521f83304d4a13881700000d' && $diario[$i]['modulo'] != 'AD' && floatval($diario[$i]["consulta"][$j]['cate']) != 8){
					$mujeres_A++;
				}
				if(floatval($diario[$i]["consulta"][$j]['paciente']['sexo']) == 1 && $diario[$i]['consulta'][$j]['esta'] != '5' && $diario[$i]['medico']['_id'] == '521f83304d4a13881700000d' && $diario[$i]['modulo'] != 'AD' && floatval($diario[$i]["consulta"][$j]['cate']) != 8){
					$varones_A++;
				}
				if($diario[$i]["consulta"][$j]['esta'] == 2 && $diario[$i]['consulta'][$j]['esta'] != '5' &&  $diario[$i]['medico']['_id'] == '521f83304d4a13881700000d' && $diario[$i]['modulo'] != 'AD' && floatval($diario[$i]["consulta"][$j]['cate']) != 8){
					$nuevos_A++;
				}
				if($diario[$i]["consulta"][$j]['esta'] == 3  && $diario[$i]['consulta'][$j]['esta'] != '5' && $diario[$i]['medico']['_id'] == '521f83304d4a13881700000d' && $diario[$i]['modulo'] != 'AD' && floatval($diario[$i]["consulta"][$j]['cate']) != 8){
					$continuadores_A++;
				}
				if($edad<11  && $diario[$i]['medico']['_id'] == '521f83304d4a13881700000d' && $diario[$i]['modulo'] != 'AD' && $diario[$i]['consulta'][$j]['esta'] != '5' && floatval($diario[$i]["consulta"][$j]['cate']) != 8){
						$niños_A++;
				}
				if($edad>=11 && $edad<18 && $diario[$i]['medico']['_id'] == '521f83304d4a13881700000d' && $diario[$i]['modulo'] != 'AD' && $diario[$i]['consulta'][$j]['esta'] != '5' && floatval($diario[$i]["consulta"][$j]['cate']) != 8){
						$jovenes_A++;
				}
				if($edad>=18 && $edad<60 && $diario[$i]['medico']['_id'] == '521f83304d4a13881700000d' && $diario[$i]['modulo'] != 'AD' && $diario[$i]['consulta'][$j]['esta'] != '5' && floatval($diario[$i]["consulta"][$j]['cate']) != 8){
						$adulto_A++;
				}
				if($edad>=60 && $diario[$i]['medico']['_id'] == '521f83304d4a13881700000d' && $diario[$i]['modulo'] != 'AD' && $diario[$i]['consulta'][$j]['esta'] != '5' && floatval($diario[$i]["consulta"][$j]['cate']) != 8){
						$anciano_A++;
				}
				//ALVARADO (1)
				if(floatval($diario[$i]["consulta"][$j]['paciente']['sexo']) == 0 && $diario[$i]['consulta'][$j]['esta'] != '5' && $diario[$i]['medico']['_id'] =='5b6dcd9c3e6037172f8b4570' && $diario[$i]['modulo'] != 'AD' && floatval($diario[$i]["consulta"][$j]['cate']) != 8){
					$mujeres_A_1++;
				}
				if(floatval($diario[$i]["consulta"][$j]['paciente']['sexo']) == 1 && $diario[$i]['consulta'][$j]['esta'] != '5' && $diario[$i]['medico']['_id'] == '5b6dcd9c3e6037172f8b4570' && $diario[$i]['modulo'] != 'AD' && floatval($diario[$i]["consulta"][$j]['cate']) != 8){
					$varones_A_1++;
				}
				if($diario[$i]["consulta"][$j]['esta'] == 2 && $diario[$i]['consulta'][$j]['esta'] != '5' &&  $diario[$i]['medico']['_id'] == '5b6dcd9c3e6037172f8b4570' && $diario[$i]['modulo'] != 'AD' && floatval($diario[$i]["consulta"][$j]['cate']) != 8){
					$nuevos_A_1++;
				}
				if($diario[$i]["consulta"][$j]['esta'] == 3  && $diario[$i]['consulta'][$j]['esta'] != '5' && $diario[$i]['medico']['_id'] == '5b6dcd9c3e6037172f8b4570' && $diario[$i]['modulo'] != 'AD' && floatval($diario[$i]["consulta"][$j]['cate']) != 8){
					$continuadores_A_1++;
				}
				if($edad<11  && $diario[$i]['medico']['_id'] == '5b6dcd9c3e6037172f8b4570' && $diario[$i]['modulo'] != 'AD' && $diario[$i]['consulta'][$j]['esta'] != '5' && floatval($diario[$i]["consulta"][$j]['cate']) != 8){
						$niños_A_1++;
				}
				if($edad>=11 && $edad<18 && $diario[$i]['medico']['_id'] == '5b6dcd9c3e6037172f8b4570' && $diario[$i]['modulo'] != 'AD' && $diario[$i]['consulta'][$j]['esta'] != '5' && floatval($diario[$i]["consulta"][$j]['cate']) != 8){
						$jovenes_A_1++;
				}
				if($edad>=18 && $edad<60 && $diario[$i]['medico']['_id'] == '5b6dcd9c3e6037172f8b4570' && $diario[$i]['modulo'] != 'AD' && $diario[$i]['consulta'][$j]['esta'] != '5' && floatval($diario[$i]["consulta"][$j]['cate']) != 8){
						$adulto_A_1++;
				}
				if($edad>=60 && $diario[$i]['medico']['_id'] == '5b6dcd9c3e6037172f8b4570' && $diario[$i]['modulo'] != 'AD' && $diario[$i]['consulta'][$j]['esta'] != '5' && floatval($diario[$i]["consulta"][$j]['cate']) != 8){
						$anciano_A_1++;
				}
				//SANCHEZ
				if(floatval($diario[$i]["consulta"][$j]['paciente']['sexo']) == 0 && $diario[$i]['consulta'][$j]['esta'] != '5' && $diario[$i]['medico']['_id'] =='587e2e503e60376b778b4568' && $diario[$i]['modulo'] != 'AD' && floatval($diario[$i]["consulta"][$j]['cate']) != 8){
					$mujeres_SA++;
				}
				if(floatval($diario[$i]["consulta"][$j]['paciente']['sexo']) == 1 && $diario[$i]['consulta'][$j]['esta'] != '5' && $diario[$i]['medico']['_id'] == '587e2e503e60376b778b4568' && $diario[$i]['modulo'] != 'AD' && floatval($diario[$i]["consulta"][$j]['cate']) != 8){
					$varones_SA++;
				}
				if($diario[$i]["consulta"][$j]['esta'] == 2 && $diario[$i]['consulta'][$j]['esta'] != '5' &&  $diario[$i]['medico']['_id'] == '587e2e503e60376b778b4568' && $diario[$i]['modulo'] != 'AD' && floatval($diario[$i]["consulta"][$j]['cate']) != 8){
					$nuevos_SA++;
				}
				if($diario[$i]["consulta"][$j]['esta'] == 3  && $diario[$i]['consulta'][$j]['esta'] != '5' && $diario[$i]['medico']['_id'] == '587e2e503e60376b778b4568' && $diario[$i]['modulo'] != 'AD' && floatval($diario[$i]["consulta"][$j]['cate']) != 8){
					$continuadores_SA++;
				}
				if($edad<11  && $diario[$i]['medico']['_id'] == '587e2e503e60376b778b4568' && $diario[$i]['modulo'] != 'AD' && $diario[$i]['consulta'][$j]['esta'] != '5' && floatval($diario[$i]["consulta"][$j]['cate']) != 8){
						$niños_SA++;
				}
				if($edad>=11 && $edad<18 && $diario[$i]['medico']['_id'] == '587e2e503e60376b778b4568' && $diario[$i]['modulo'] != 'AD' && $diario[$i]['consulta'][$j]['esta'] != '5' && floatval($diario[$i]["consulta"][$j]['cate']) != 8){
						$jovenes_SA++;
				}
				if($edad>=18 && $edad<60 && $diario[$i]['medico']['_id'] == '587e2e503e60376b778b4568' && $diario[$i]['modulo'] != 'AD' && $diario[$i]['consulta'][$j]['esta'] != '5' && floatval($diario[$i]["consulta"][$j]['cate']) != 8){
						$adulto_SA++;
				}
				if($edad>=60 && $diario[$i]['medico']['_id'] == '587e2e503e60376b778b4568' && $diario[$i]['modulo'] != 'AD' && $diario[$i]['consulta'][$j]['esta'] != '5' && floatval($diario[$i]["consulta"][$j]['cate']) != 8){
						$anciano_SA++;
				}
				//SANCHEZ (1)
				if(floatval($diario[$i]["consulta"][$j]['paciente']['sexo']) == 0 && $diario[$i]['consulta'][$j]['esta'] != '5' && $diario[$i]['medico']['_id'] =='5b6dce713e6037562f8b456c' && $diario[$i]['modulo'] != 'AD' && floatval($diario[$i]["consulta"][$j]['cate']) != 8){
					$mujeres_SA_1++;
				}
				if(floatval($diario[$i]["consulta"][$j]['paciente']['sexo']) == 1 && $diario[$i]['consulta'][$j]['esta'] != '5' && $diario[$i]['medico']['_id'] == '5b6dce713e6037562f8b456c' && $diario[$i]['modulo'] != 'AD' && floatval($diario[$i]["consulta"][$j]['cate']) != 8){
					$varones_SA_1++;
				}
				if($diario[$i]["consulta"][$j]['esta'] == 2 && $diario[$i]['consulta'][$j]['esta'] != '5' &&  $diario[$i]['medico']['_id'] == '5b6dce713e6037562f8b456c' && $diario[$i]['modulo'] != 'AD' && floatval($diario[$i]["consulta"][$j]['cate']) != 8){
					$nuevos_SA_1++;
				}
				if($diario[$i]["consulta"][$j]['esta'] == 3  && $diario[$i]['consulta'][$j]['esta'] != '5' && $diario[$i]['medico']['_id'] == '5b6dce713e6037562f8b456c' && $diario[$i]['modulo'] != 'AD' && floatval($diario[$i]["consulta"][$j]['cate']) != 8){
					$continuadores_SA_1++;
				}
				if($edad<11  && $diario[$i]['medico']['_id'] == '5b6dce713e6037562f8b456c' && $diario[$i]['modulo'] != 'AD' && $diario[$i]['consulta'][$j]['esta'] != '5' && floatval($diario[$i]["consulta"][$j]['cate']) != 8){
						$niños_SA_1++;
				}
				if($edad>=11 && $edad<18 && $diario[$i]['medico']['_id'] == '5b6dce713e6037562f8b456c' && $diario[$i]['modulo'] != 'AD' && $diario[$i]['consulta'][$j]['esta'] != '5' && floatval($diario[$i]["consulta"][$j]['cate']) != 8){
						$jovenes_SA_1++;
				}
				if($edad>=18 && $edad<60 && $diario[$i]['medico']['_id'] == '5b6dce713e6037562f8b456c' && $diario[$i]['modulo'] != 'AD' && $diario[$i]['consulta'][$j]['esta'] != '5' && floatval($diario[$i]["consulta"][$j]['cate']) != 8){
						$adulto_SA_1++;
				}
				if($edad>=60 && $diario[$i]['medico']['_id'] == '5b6dce713e6037562f8b456c' && $diario[$i]['modulo'] != 'AD' && $diario[$i]['consulta'][$j]['esta'] != '5' && floatval($diario[$i]["consulta"][$j]['cate']) != 8){
						$anciano_SA_1++;
				}
				//GONZALES
				if(floatval($diario[$i]["consulta"][$j]['paciente']['sexo']) == 0 && $diario[$i]['consulta'][$j]['esta'] != '5' && $diario[$i]['medico']['_id'] =='5ac3b2683e6037085c8b4567' && $diario[$i]['modulo'] != 'AD' && floatval($diario[$i]["consulta"][$j]['cate']) != 8){
					$mujeres_S++;
				}
				if(floatval($diario[$i]["consulta"][$j]['paciente']['sexo']) == 1 && $diario[$i]['consulta'][$j]['esta'] != '5' && $diario[$i]['medico']['_id'] == '5ac3b2683e6037085c8b4567' && $diario[$i]['modulo'] != 'AD' && floatval($diario[$i]["consulta"][$j]['cate']) != 8){
					$varones_S++;
				}
				if($diario[$i]["consulta"][$j]['esta'] == 2 && $diario[$i]['consulta'][$j]['esta'] != '5' &&  $diario[$i]['medico']['_id'] == '5ac3b2683e6037085c8b4567' && $diario[$i]['modulo'] != 'AD' && floatval($diario[$i]["consulta"][$j]['cate']) != 8){
					$nuevos_S++;
				}
				if($diario[$i]["consulta"][$j]['esta'] == 3  && $diario[$i]['consulta'][$j]['esta'] != '5' && $diario[$i]['medico']['_id'] == '5ac3b2683e6037085c8b4567' && $diario[$i]['modulo'] != 'AD' && floatval($diario[$i]["consulta"][$j]['cate']) != 8){
					$continuadores_S++;
				}
				if($edad<11  && $diario[$i]['medico']['_id'] == '5ac3b2683e6037085c8b4567' && $diario[$i]['modulo'] != 'AD' && $diario[$i]['consulta'][$j]['esta'] != '5' && floatval($diario[$i]["consulta"][$j]['cate']) != 8){
						$niños_S++;
				}
				if($edad>=11 && $edad<18 && $diario[$i]['medico']['_id'] == '5ac3b2683e6037085c8b4567' && $diario[$i]['modulo'] != 'AD' && $diario[$i]['consulta'][$j]['esta'] != '5' && floatval($diario[$i]["consulta"][$j]['cate']) != 8){
						$jovenes_S++;
				}
				if($edad>=18 && $edad<60 && $diario[$i]['medico']['_id'] == '5ac3b2683e6037085c8b4567' && $diario[$i]['modulo'] != 'AD' && $diario[$i]['consulta'][$j]['esta'] != '5' && floatval($diario[$i]["consulta"][$j]['cate']) != 8){
						$adulto_S++;
				}
				if($edad>=60 && $diario[$i]['medico']['_id'] == '5ac3b2683e6037085c8b4567' && $diario[$i]['modulo'] != 'AD' && $diario[$i]['consulta'][$j]['esta'] != '5' && floatval($diario[$i]["consulta"][$j]['cate']) != 8){
						$anciano_S++;
				}
				//PAREDES
				if(floatval($diario[$i]["consulta"][$j]['paciente']['sexo']) == 0 && $diario[$i]['consulta'][$j]['esta'] != '5' && $diario[$i]['medico']['_id'] =='5908b7b43e60379f5e8b4567' && $diario[$i]['modulo'] != 'AD' && floatval($diario[$i]["consulta"][$j]['cate']) != 8){
					$mujeres_P++;
				}
				if(floatval($diario[$i]["consulta"][$j]['paciente']['sexo']) == 1 && $diario[$i]['consulta'][$j]['esta'] != '5' && $diario[$i]['medico']['_id'] == '5908b7b43e60379f5e8b4567' && $diario[$i]['modulo'] != 'AD' && floatval($diario[$i]["consulta"][$j]['cate']) != 8){
					$varones_P++;
				}
				if($diario[$i]["consulta"][$j]['esta'] == 2 && $diario[$i]['consulta'][$j]['esta'] != '5' &&  $diario[$i]['medico']['_id'] == '5908b7b43e60379f5e8b4567' && $diario[$i]['modulo'] != 'AD' && floatval($diario[$i]["consulta"][$j]['cate']) != 8){
					$nuevos_P++;
				}
				if($diario[$i]["consulta"][$j]['esta'] == 3  && $diario[$i]['consulta'][$j]['esta'] != '5' && $diario[$i]['medico']['_id'] == '5908b7b43e60379f5e8b4567' && $diario[$i]['modulo'] != 'AD' && floatval($diario[$i]["consulta"][$j]['cate']) != 8){
					$continuadores_P++;
				}
				if($edad<11  && $diario[$i]['medico']['_id'] == '5908b7b43e60379f5e8b4567' && $diario[$i]['modulo'] != 'AD' && $diario[$i]['consulta'][$j]['esta'] != '5' && floatval($diario[$i]["consulta"][$j]['cate']) != 8){
						$niños_P++;
				}
				if($edad>=11 && $edad<18 && $diario[$i]['medico']['_id'] == '5908b7b43e60379f5e8b4567' && $diario[$i]['modulo'] != 'AD' && $diario[$i]['consulta'][$j]['esta'] != '5' && floatval($diario[$i]["consulta"][$j]['cate']) != 8){
						$jovenes_P++;
				}
				if($edad>=18 && $edad<60 && $diario[$i]['medico']['_id'] == '5908b7b43e60379f5e8b4567' && $diario[$i]['modulo'] != 'AD' && $diario[$i]['consulta'][$j]['esta'] != '5' && floatval($diario[$i]["consulta"][$j]['cate']) != 8){
						$adulto_P++;
				}
				if($edad>=60 && $diario[$i]['medico']['_id'] == '5908b7b43e60379f5e8b4567' && $diario[$i]['modulo'] != 'AD' && $diario[$i]['consulta'][$j]['esta'] != '5' && floatval($diario[$i]["consulta"][$j]['cate']) != 8){
						$anciano_P++;
				}
				//RAMIREZ
				if(floatval($diario[$i]["consulta"][$j]['paciente']['sexo']) == 0 && $diario[$i]['consulta'][$j]['esta'] != '5' && $diario[$i]['medico']['_id'] =='593ec2743e6037f3588b456b' && $diario[$i]['modulo'] != 'AD' && floatval($diario[$i]["consulta"][$j]['cate']) != 8){
					$mujeres_R++;
				}
				if(floatval($diario[$i]["consulta"][$j]['paciente']['sexo']) == 1 && $diario[$i]['consulta'][$j]['esta'] != '5' && $diario[$i]['medico']['_id'] == '593ec2743e6037f3588b456b' && $diario[$i]['modulo'] != 'AD' && floatval($diario[$i]["consulta"][$j]['cate']) != 8){
					$varones_R++;
				}
				if($diario[$i]["consulta"][$j]['esta'] == 2 && $diario[$i]['consulta'][$j]['esta'] != '5' &&  $diario[$i]['medico']['_id'] == '593ec2743e6037f3588b456b' && $diario[$i]['modulo'] != 'AD' && floatval($diario[$i]["consulta"][$j]['cate']) != 8){
					$nuevos_R++;
				}
				if($diario[$i]["consulta"][$j]['esta'] == 3  && $diario[$i]['consulta'][$j]['esta'] != '5' && $diario[$i]['medico']['_id'] == '593ec2743e6037f3588b456b' && $diario[$i]['modulo'] != 'AD' && floatval($diario[$i]["consulta"][$j]['cate']) != 8){
					$continuadores_R++;
				}
				if($edad<11  && $diario[$i]['medico']['_id'] == '593ec2743e6037f3588b456b' && $diario[$i]['modulo'] != 'AD' && $diario[$i]['consulta'][$j]['esta'] != '5' && floatval($diario[$i]["consulta"][$j]['cate']) != 8){
						$niños_R++;
				}
				if($edad>=11 && $edad<18 && $diario[$i]['medico']['_id'] == '593ec2743e6037f3588b456b' && $diario[$i]['modulo'] != 'AD' && $diario[$i]['consulta'][$j]['esta'] != '5' && floatval($diario[$i]["consulta"][$j]['cate']) != 8){
						$jovenes_R++;
				}
				if($edad>=18 && $edad<60 && $diario[$i]['medico']['_id'] == '593ec2743e6037f3588b456b' && $diario[$i]['modulo'] != 'AD' && $diario[$i]['consulta'][$j]['esta'] != '5' && floatval($diario[$i]["consulta"][$j]['cate']) != 8){
						$adulto_R++;
				}
				if($edad>=60 && $diario[$i]['medico']['_id'] == '593ec2743e6037f3588b456b' && $diario[$i]['modulo'] != 'AD' && $diario[$i]['consulta'][$j]['esta'] != '5' && floatval($diario[$i]["consulta"][$j]['cate']) != 8){
						$anciano_R++;
				}
				//TICONA
				if(floatval($diario[$i]["consulta"][$j]['paciente']['sexo']) == 0 && $diario[$i]['consulta'][$j]['esta'] != '5' && $diario[$i]['medico']['_id'] =='5977ae6c3e603746248b4568' && $diario[$i]['modulo'] != 'AD' && floatval($diario[$i]["consulta"][$j]['cate']) != 8){
					$mujeres_T++;
				}
				if(floatval($diario[$i]["consulta"][$j]['paciente']['sexo']) == 1 && $diario[$i]['consulta'][$j]['esta'] != '5' && $diario[$i]['medico']['_id'] == '5977ae6c3e603746248b4568' && $diario[$i]['modulo'] != 'AD' && floatval($diario[$i]["consulta"][$j]['cate']) != 8){
					$varones_T++;
				}
				if($diario[$i]["consulta"][$j]['esta'] == 2 && $diario[$i]['consulta'][$j]['esta'] != '5' &&  $diario[$i]['medico']['_id'] == '5977ae6c3e603746248b4568' && $diario[$i]['modulo'] != 'AD' && floatval($diario[$i]["consulta"][$j]['cate']) != 8){
					$nuevos_T++;
				}
				if($diario[$i]["consulta"][$j]['esta'] == 3  && $diario[$i]['consulta'][$j]['esta'] != '5' && $diario[$i]['medico']['_id'] == '5977ae6c3e603746248b4568' && $diario[$i]['modulo'] != 'AD' && floatval($diario[$i]["consulta"][$j]['cate']) != 8){
					$continuadores_T++;
				}
				if($edad<11  && $diario[$i]['medico']['_id'] == '5977ae6c3e603746248b4568' && $diario[$i]['modulo'] != 'AD' && $diario[$i]['consulta'][$j]['esta'] != '5' && floatval($diario[$i]["consulta"][$j]['cate']) != 8){
						$niños_T++;
				}
				if($edad>=11 && $edad<18 && $diario[$i]['medico']['_id'] == '5977ae6c3e603746248b4568' && $diario[$i]['modulo'] != 'AD' && $diario[$i]['consulta'][$j]['esta'] != '5' && floatval($diario[$i]["consulta"][$j]['cate']) != 8){
						$jovenes_T++;
				}
				if($edad>=18 && $edad<60 && $diario[$i]['medico']['_id'] == '5977ae6c3e603746248b4568' && $diario[$i]['modulo'] != 'AD' && $diario[$i]['consulta'][$j]['esta'] != '5' && floatval($diario[$i]["consulta"][$j]['cate']) != 8){
						$adulto_T++;
				}
				if($edad>=60 && $diario[$i]['medico']['_id'] == '5977ae6c3e603746248b4568' && $diario[$i]['modulo'] != 'AD' && $diario[$i]['consulta'][$j]['esta'] != '5' && floatval($diario[$i]["consulta"][$j]['cate']) != 8){
						$anciano_T++;
				}
				//ESQUIVIAS
				if(floatval($diario[$i]["consulta"][$j]['paciente']['sexo']) == 0 && $diario[$i]['consulta'][$j]['esta'] != '5' && $diario[$i]['medico']['_id'] =='57cf1d608e73586c08000095' && $diario[$i]['modulo'] != 'AD' && floatval($diario[$i]["consulta"][$j]['cate']) != 8){
					$mujeres_E++;
				}
				if(floatval($diario[$i]["consulta"][$j]['paciente']['sexo']) == 1 && $diario[$i]['consulta'][$j]['esta'] != '5' && $diario[$i]['medico']['_id'] == '57cf1d608e73586c08000095' && $diario[$i]['modulo'] != 'AD' && floatval($diario[$i]["consulta"][$j]['cate']) != 8){
					$varones_E++;
				}
				if($diario[$i]["consulta"][$j]['esta'] == 2 && $diario[$i]['consulta'][$j]['esta'] != '5' &&  $diario[$i]['medico']['_id'] == '57cf1d608e73586c08000095' && $diario[$i]['modulo'] != 'AD' && floatval($diario[$i]["consulta"][$j]['cate']) != 8){
					$nuevos_E++;
				}
				if($diario[$i]["consulta"][$j]['esta'] == 3  && $diario[$i]['consulta'][$j]['esta'] != '5' && $diario[$i]['medico']['_id'] == '57cf1d608e73586c08000095' && $diario[$i]['modulo'] != 'AD' && floatval($diario[$i]["consulta"][$j]['cate']) != 8){
					$continuadores_E++;
				}
				if($edad<11  && $diario[$i]['medico']['_id'] == '57cf1d608e73586c08000095' && $diario[$i]['modulo'] != 'AD' && $diario[$i]['consulta'][$j]['esta'] != '5' && floatval($diario[$i]["consulta"][$j]['cate']) != 8){
						$niños_E++;
				}
				if($edad>=11 && $edad<18 && $diario[$i]['medico']['_id'] == '57cf1d608e73586c08000095' && $diario[$i]['modulo'] != 'AD' && $diario[$i]['consulta'][$j]['esta'] != '5' && floatval($diario[$i]["consulta"][$j]['cate']) != 8){
						$jovenes_E++;
				}
				if($edad>=18 && $edad<60 && $diario[$i]['medico']['_id'] == '57cf1d608e73586c08000095' && $diario[$i]['modulo'] != 'AD' && $diario[$i]['consulta'][$j]['esta'] != '5' && floatval($diario[$i]["consulta"][$j]['cate']) != 8){
						$adulto_E++;
				}
				if($edad>=60 && $diario[$i]['medico']['_id'] == '57cf1d608e73586c08000095' && $diario[$i]['modulo'] != 'AD' && $diario[$i]['consulta'][$j]['esta'] != '5' && floatval($diario[$i]["consulta"][$j]['cate']) != 8){
						$anciano_E++;
				}
				//ESQUIVIAS (1)
				if(floatval($diario[$i]["consulta"][$j]['paciente']['sexo']) == 0 && $diario[$i]['consulta'][$j]['esta'] != '5' && $diario[$i]['medico']['_id'] =='5b6dce0f3e6037532f8b456b' && $diario[$i]['modulo'] != 'AD' && floatval($diario[$i]["consulta"][$j]['cate']) != 8){
					$mujeres_E_1++;
				}
				if(floatval($diario[$i]["consulta"][$j]['paciente']['sexo']) == 1 && $diario[$i]['consulta'][$j]['esta'] != '5' && $diario[$i]['medico']['_id'] == '5b6dce0f3e6037532f8b456b' && $diario[$i]['modulo'] != 'AD' && floatval($diario[$i]["consulta"][$j]['cate']) != 8){
					$varones_E_1++;
				}
				if($diario[$i]["consulta"][$j]['esta'] == 2 && $diario[$i]['consulta'][$j]['esta'] != '5' &&  $diario[$i]['medico']['_id'] == '5b6dce0f3e6037532f8b456b' && $diario[$i]['modulo'] != 'AD' && floatval($diario[$i]["consulta"][$j]['cate']) != 8){
					$nuevos_E_1++;
				}
				if($diario[$i]["consulta"][$j]['esta'] == 3  && $diario[$i]['consulta'][$j]['esta'] != '5' && $diario[$i]['medico']['_id'] == '5b6dce0f3e6037532f8b456b' && $diario[$i]['modulo'] != 'AD' && floatval($diario[$i]["consulta"][$j]['cate']) != 8){
					$continuadores_E_1++;
				}
				if($edad<11  && $diario[$i]['medico']['_id'] == '5b6dce0f3e6037532f8b456b' && $diario[$i]['modulo'] != 'AD' && $diario[$i]['consulta'][$j]['esta'] != '5' && floatval($diario[$i]["consulta"][$j]['cate']) != 8){
						$niños_E_1++;
				}
				if($edad>=11 && $edad<18 && $diario[$i]['medico']['_id'] == '5b6dce0f3e6037532f8b456b' && $diario[$i]['modulo'] != 'AD' && $diario[$i]['consulta'][$j]['esta'] != '5' && floatval($diario[$i]["consulta"][$j]['cate']) != 8){
						$jovenes_E_1++;
				}
				if($edad>=18 && $edad<60 && $diario[$i]['medico']['_id'] == '5b6dce0f3e6037532f8b456b' && $diario[$i]['modulo'] != 'AD' && $diario[$i]['consulta'][$j]['esta'] != '5' && floatval($diario[$i]["consulta"][$j]['cate']) != 8){
						$adulto_E_1++;
				}
				if($edad>=60 && $diario[$i]['medico']['_id'] == '5b6dce0f3e6037532f8b456b' && $diario[$i]['modulo'] != 'AD' && $diario[$i]['consulta'][$j]['esta'] != '5' && floatval($diario[$i]["consulta"][$j]['cate']) != 8){
						$anciano_E_1++;
				}
				//REVILLA
				if(floatval($diario[$i]["consulta"][$j]['paciente']['sexo']) == 0 && $diario[$i]['consulta'][$j]['esta'] != '5' && $diario[$i]['medico']['_id'] =='587533593e6037474b8b4568' && $diario[$i]['modulo'] != 'AD' && floatval($diario[$i]["consulta"][$j]['cate']) != 8){
					$mujeres_RE++;
				}
				if(floatval($diario[$i]["consulta"][$j]['paciente']['sexo']) == 1 && $diario[$i]['consulta'][$j]['esta'] != '5' && $diario[$i]['medico']['_id'] == '587533593e6037474b8b4568' && $diario[$i]['modulo'] != 'AD' && floatval($diario[$i]["consulta"][$j]['cate']) != 8){
					$varones_RE++;
				}
				if($diario[$i]["consulta"][$j]['esta'] == 2 && $diario[$i]['consulta'][$j]['esta'] != '5' &&  $diario[$i]['medico']['_id'] == '587533593e6037474b8b4568' && $diario[$i]['modulo'] != 'AD' && floatval($diario[$i]["consulta"][$j]['cate']) != 8){
					$nuevos_RE++;
				}
				if($diario[$i]["consulta"][$j]['esta'] == 3  && $diario[$i]['consulta'][$j]['esta'] != '5' && $diario[$i]['medico']['_id'] == '587533593e6037474b8b4568' && $diario[$i]['modulo'] != 'AD' && floatval($diario[$i]["consulta"][$j]['cate']) != 8){
					$continuadores_RE++;
				}
				if($edad<11  && $diario[$i]['medico']['_id'] == '587533593e6037474b8b4568' && $diario[$i]['modulo'] != 'AD' && $diario[$i]['consulta'][$j]['esta'] != '5' && floatval($diario[$i]["consulta"][$j]['cate']) != 8){
						$niños_RE++;
				}
				if($edad>=11 && $edad<18 && $diario[$i]['medico']['_id'] == '587533593e6037474b8b4568' && $diario[$i]['modulo'] != 'AD' && $diario[$i]['consulta'][$j]['esta'] != '5' && floatval($diario[$i]["consulta"][$j]['cate']) != 8){
						$jovenes_RE++;
				}
				if($edad>=18 && $edad<60 && $diario[$i]['medico']['_id'] == '587533593e6037474b8b4568' && $diario[$i]['modulo'] != 'AD' && $diario[$i]['consulta'][$j]['esta'] != '5' && floatval($diario[$i]["consulta"][$j]['cate']) != 8){
						$adulto_RE++;
				}
				if($edad>=60 && $diario[$i]['medico']['_id'] == '587533593e6037474b8b4568' && $diario[$i]['modulo'] != 'AD' && $diario[$i]['consulta'][$j]['esta'] != '5' && floatval($diario[$i]["consulta"][$j]['cate']) != 8){
						$anciano_RE++;
				}
			}
		}


		$total_categoria_A = $nuevos_A+$continuadores_A;
		$total_edad_A = $anciano_A+$adulto_A+$jovenes_A+$niños_A;
		$total_sexo_A = $mujeres_A + $varones_A;
		$total_categoria_A_1 = $nuevos_A_1+$continuadores_A_1;
		$total_edad_A_1 = $anciano_A_1+$adulto_A_1+$jovenes_A_1+$niños_A_1;
		$total_sexo_A_1 = $mujeres_A_1 + $varones_A_1;
		$total_categoria_SA = $nuevos_SA+$continuadores_SA;
		$total_edad_SA = $anciano_SA+$adulto_SA+$jovenes_SA+$niños_SA;
		$total_sexo_SA = $mujeres_SA + $varones_SA;
		$total_categoria_SA_1 = $nuevos_SA_1+$continuadores_SA_1;
		$total_edad_SA_1 = $anciano_SA_1+$adulto_SA_1+$jovenes_SA_1+$niños_SA_1;
		$total_sexo_SA_1 = $mujeres_SA_1 + $varones_SA_1;
		$total_categoria_S = $nuevos_S+$continuadores_S;
		$total_edad_S = $anciano_S+$adulto_S+$jovenes_S+$niños_S;
		$total_sexo_S = $mujeres_S + $varones_S;
		$total_categoria_P = $nuevos_P+$continuadores_P;
		$total_edad_P = $anciano_P+$adulto_P+$jovenes_P+$niños_P;
		$total_sexo_P = $mujeres_P + $varones_P;
		$total_categoria_R = $nuevos_R+$continuadores_R;
		$total_edad_R = $anciano_R+$adulto_R+$jovenes_R+$niños_R;
		$total_sexo_R = $mujeres_R + $varones_R;
		$total_categoria_T = $nuevos_T+$continuadores_T;
		$total_edad_T = $anciano_T+$adulto_T+$jovenes_T+$niños_T;
		$total_sexo_T = $mujeres_T + $varones_T;
		$total_categoria_E = $nuevos_E+$continuadores_E;
		$total_edad_E = $anciano_E+$adulto_E+$jovenes_E+$niños_E;
		$total_sexo_E = $mujeres_E + $varones_E;
		$total_categoria_E_1 = $nuevos_E_1+$continuadores_E_1;
		$total_edad_E_1 = $anciano_E_1+$adulto_E_1+$jovenes_E_1+$niños_E_1;
		$total_sexo_E_1 = $mujeres_E_1 + $varones_E_1;
		$total_categoria_RE = $nuevos_RE+$continuadores_RE;
		$total_edad_RE = $anciano_RE+$adulto_RE+$jovenes_RE+$niños_RE;
		$total_sexo_RE = $mujeres_RE + $varones_RE;
		$tot_nuevos = $nuevos_A_1+$nuevos_SA+$nuevos_S+$nuevos_RE+$nuevos_A+$nuevos_E+$nuevos_P+$nuevos_E_1+$nuevos_SA_1;
		$tot_continuadores = $continuadores_A_1+$continuadores_SA+$continuadores_S+$continuadores_RE+$continuadores_A+$continuadores_E+$continuadores_P+$continuadores_E_1+$continuadores_SA_1;

		$tot_ninos = $niños_A_1+$niños_SA+$niños_S+$niños_RE+$niños_A+$niños_E+$niños_P+$niños_E_1+$niños_SA_1;
		$tot_jovenes = $jovenes_A_1+$jovenes_SA+$jovenes_S+$jovenes_RE+$jovenes_A+$jovenes_E+$jovenes_P+$jovenes_E_1+$jovenes_SA_1;
		$tot_adultos = $adulto_A_1+$adulto_SA+$adulto_S+$adulto_RE+$adulto_A+$adulto_E+$adulto_P+$adulto_E_1+$adulto_SA_1;
		$tot_ancianos = $anciano_A_1+$anciano_SA+$anciano_S+$anciano_RE+$anciano_A+$anciano_E+$anciano_P+$anciano_E_1+$anciano_SA_1;
		$tot_varones = $varones_A_1+$varones_SA+$varones_S+$varones_RE+$varones_A+$varones_E+$varones_P+$varones_E_1+$varones_SA_1;
		$tot_mujeres = $mujeres_A_1+$mujeres_SA+$mujeres_S+$mujeres_RE+$mujeres_A+$mujeres_E+$mujeres_P+$mujeres_E_1+$mujeres_SA_1;

		$this->SetFont('Arial','',8);
		$this->Line(10,$y,200,$y);
		$this->SetXY(10,$y);$this->MultiCell(40,5,'ALVARADO','0','L');
		$this->SetXY(50,$y);$this->MultiCell(25,5,'Nuevos','0','L');
		$this->SetXY(75,$y);$this->MultiCell(25,5,$nuevos_A,'0','L');
		$y+=3;
		$this->SetXY(50,$y);$this->MultiCell(25,5,'Continuadores','0','L');
		$this->SetXY(75,$y);$this->MultiCell(25,5,$continuadores_A,'0','L');
		$y+=3;
		$this->SetXY(50,$y);$this->MultiCell(25,5,'Reingresantes','0','L');
		$this->SetXY(75,$y);$this->MultiCell(25,5,'0','0','L');
		$y+=3;
		$this->SetXY(50,$y);$this->MultiCell(25,5,'TOTAL','0','L');
		
		$this->SetXY(75,$y);$this->MultiCell(25,5,$total_categoria_A,'0','L');
		$y-=10;
		$this->SetXY(100,$y);$this->MultiCell(25,5,'Niños','0','L');
		$this->SetXY(125,$y);$this->MultiCell(25,5,$niños_A,'0','L');
		$y+=3;
		$this->SetXY(100,$y);$this->MultiCell(25,5,'Jovenes','0','L');
		$this->SetXY(125,$y);$this->MultiCell(25,5,$jovenes_A,'0','L');
		$y+=3;
		$this->SetXY(100,$y);$this->MultiCell(25,5,'Adultos','0','L');
		$this->SetXY(125,$y);$this->MultiCell(25,5,$adulto_A,'0','L');
		$y+=3;
		$this->SetXY(100,$y);$this->MultiCell(25,5,'Ancianos','0','L');
		$this->SetXY(125,$y);$this->MultiCell(25,5,$anciano_A,'0','L');
		$y+=3;
		$this->SetXY(100,$y);$this->MultiCell(25,5,'TOTAL','0','L');
		
		$this->SetXY(125,$y);$this->MultiCell(25,5,$total_edad_A,'0','L');
		$y-=12;
		$this->SetXY(150,$y);$this->MultiCell(25,5,'Varones','0','L');
		$this->SetXY(175,$y);$this->MultiCell(25,5,$varones_A,'0','L');
		$y+=3;
		$this->SetXY(150,$y);$this->MultiCell(25,5,'Mujeres','0','L');
		$this->SetXY(175,$y);$this->MultiCell(25,5,$mujeres_A,'0','L');
		$y+=3;
		$this->SetXY(150,$y);$this->MultiCell(25,5,'TOTAL','0','L');
		$this->SetXY(175,$y);$this->MultiCell(25,5,$total_sexo_A,'0','L');
		$this->Line(10,$y+10,200,$y+10);
		$y=$y+10;
		$this->SetFont('Arial','',8);
		$this->Line(10,$y,200,$y);
		$this->SetXY(10,$y);$this->MultiCell(40,5,'ALVARADO (1)','0','L');
		$this->SetXY(50,$y);$this->MultiCell(25,5,'Nuevos','0','L');
		$this->SetXY(75,$y);$this->MultiCell(25,5,$nuevos_A_1,'0','L');
		$y+=3;
		$this->SetXY(50,$y);$this->MultiCell(25,5,'Continuadores','0','L');
		$this->SetXY(75,$y);$this->MultiCell(25,5,$continuadores_A_1,'0','L');
		$y+=3;
		$this->SetXY(50,$y);$this->MultiCell(25,5,'Reingresantes','0','L');
		$this->SetXY(75,$y);$this->MultiCell(25,5,'0','0','L');
		$y+=3;
		$this->SetXY(50,$y);$this->MultiCell(25,5,'TOTAL','0','L');
		
		$this->SetXY(75,$y);$this->MultiCell(25,5,$total_categoria_A_1,'0','L');
		$y-=10;
		$this->SetXY(100,$y);$this->MultiCell(25,5,'Niños','0','L');
		$this->SetXY(125,$y);$this->MultiCell(25,5,$niños_A_1,'0','L');
		$y+=3;
		$this->SetXY(100,$y);$this->MultiCell(25,5,'Jovenes','0','L');
		$this->SetXY(125,$y);$this->MultiCell(25,5,$jovenes_A_1,'0','L');
		$y+=3;
		$this->SetXY(100,$y);$this->MultiCell(25,5,'Adultos','0','L');
		$this->SetXY(125,$y);$this->MultiCell(25,5,$adulto_A_1,'0','L');
		$y+=3;
		$this->SetXY(100,$y);$this->MultiCell(25,5,'Ancianos','0','L');
		$this->SetXY(125,$y);$this->MultiCell(25,5,$anciano_A_1,'0','L');
		$y+=3;
		$this->SetXY(100,$y);$this->MultiCell(25,5,'TOTAL','0','L');
		
		$this->SetXY(125,$y);$this->MultiCell(25,5,$total_edad_A_1,'0','L');
		$y-=12;
		$this->SetXY(150,$y);$this->MultiCell(25,5,'Varones','0','L');
		$this->SetXY(175,$y);$this->MultiCell(25,5,$varones_A_1,'0','L');
		$y+=3;
		$this->SetXY(150,$y);$this->MultiCell(25,5,'Mujeres','0','L');
		$this->SetXY(175,$y);$this->MultiCell(25,5,$mujeres_A_1,'0','L');
		$y+=3;
		$this->SetXY(150,$y);$this->MultiCell(25,5,'TOTAL','0','L');
		$this->SetXY(175,$y);$this->MultiCell(25,5,$total_sexo_A_1,'0','L');
		$this->Line(10,$y+10,200,$y+10);
		$y=$y+10;
		$this->SetFont('Arial','',8);
		$this->Line(10,$y,200,$y);
		$this->SetXY(10,$y);$this->MultiCell(40,5,'SANCHEZ','0','L');
		$this->SetXY(50,$y);$this->MultiCell(25,5,'Nuevos','0','L');
		$this->SetXY(75,$y);$this->MultiCell(25,5,$nuevos_SA,'0','L');
		$y+=3;
		$this->SetXY(50,$y);$this->MultiCell(25,5,'Continuadores','0','L');
		$this->SetXY(75,$y);$this->MultiCell(25,5,$continuadores_SA,'0','L');
		$y+=3;
		$this->SetXY(50,$y);$this->MultiCell(25,5,'Reingresantes','0','L');
		$this->SetXY(75,$y);$this->MultiCell(25,5,'0','0','L');
		$y+=3;
		$this->SetXY(50,$y);$this->MultiCell(25,5,'TOTAL','0','L');
		
		$this->SetXY(75,$y);$this->MultiCell(25,5,$total_categoria_SA,'0','L');
		$y-=10;
		$this->SetXY(100,$y);$this->MultiCell(25,5,'Niños','0','L');
		$this->SetXY(125,$y);$this->MultiCell(25,5,$niños_SA,'0','L');
		$y+=3;
		$this->SetXY(100,$y);$this->MultiCell(25,5,'Jovenes','0','L');
		$this->SetXY(125,$y);$this->MultiCell(25,5,$jovenes_SA,'0','L');
		$y+=3;
		$this->SetXY(100,$y);$this->MultiCell(25,5,'Adultos','0','L');
		$this->SetXY(125,$y);$this->MultiCell(25,5,$adulto_SA,'0','L');
		$y+=3;
		$this->SetXY(100,$y);$this->MultiCell(25,5,'Ancianos','0','L');
		$this->SetXY(125,$y);$this->MultiCell(25,5,$anciano_SA,'0','L');
		$y+=3;
		$this->SetXY(100,$y);$this->MultiCell(25,5,'TOTAL','0','L');
		
		$this->SetXY(125,$y);$this->MultiCell(25,5,$total_edad_SA,'0','L');
		$y-=12;
		$this->SetXY(150,$y);$this->MultiCell(25,5,'Varones','0','L');
		$this->SetXY(175,$y);$this->MultiCell(25,5,$varones_SA,'0','L');
		$y+=3;
		$this->SetXY(150,$y);$this->MultiCell(25,5,'Mujeres','0','L');
		$this->SetXY(175,$y);$this->MultiCell(25,5,$mujeres_SA,'0','L');
		$y+=3;
		$this->SetXY(150,$y);$this->MultiCell(25,5,'TOTAL','0','L');
		$this->SetXY(175,$y);$this->MultiCell(25,5,$total_sexo_SA,'0','L');
		$this->Line(10,$y+10,200,$y+10);
		$y=$y+10;
		$this->SetFont('Arial','',8);
		$this->Line(10,$y,200,$y);
		$this->SetXY(10,$y);$this->MultiCell(40,5,'SANCHEZ (1) ','0','L');
		$this->SetXY(50,$y);$this->MultiCell(25,5,'Nuevos','0','L');
		$this->SetXY(75,$y);$this->MultiCell(25,5,$nuevos_SA_1,'0','L');
		$y+=3;
		$this->SetXY(50,$y);$this->MultiCell(25,5,'Continuadores','0','L');
		$this->SetXY(75,$y);$this->MultiCell(25,5,$continuadores_SA_1,'0','L');
		$y+=3;
		$this->SetXY(50,$y);$this->MultiCell(25,5,'Reingresantes','0','L');
		$this->SetXY(75,$y);$this->MultiCell(25,5,'0','0','L');
		$y+=3;
		$this->SetXY(50,$y);$this->MultiCell(25,5,'TOTAL','0','L');
		
		$this->SetXY(75,$y);$this->MultiCell(25,5,$total_categoria_SA_1,'0','L');
		$y-=10;
		$this->SetXY(100,$y);$this->MultiCell(25,5,'Niños','0','L');
		$this->SetXY(125,$y);$this->MultiCell(25,5,$niños_SA_1,'0','L');
		$y+=3;
		$this->SetXY(100,$y);$this->MultiCell(25,5,'Jovenes','0','L');
		$this->SetXY(125,$y);$this->MultiCell(25,5,$jovenes_SA_1,'0','L');
		$y+=3;
		$this->SetXY(100,$y);$this->MultiCell(25,5,'Adultos','0','L');
		$this->SetXY(125,$y);$this->MultiCell(25,5,$adulto_SA_1,'0','L');
		$y+=3;
		$this->SetXY(100,$y);$this->MultiCell(25,5,'Ancianos','0','L');
		$this->SetXY(125,$y);$this->MultiCell(25,5,$anciano_SA_1,'0','L');
		$y+=3;
		$this->SetXY(100,$y);$this->MultiCell(25,5,'TOTAL','0','L');
		
		$this->SetXY(125,$y);$this->MultiCell(25,5,$total_edad_SA_1,'0','L');
		$y-=12;
		$this->SetXY(150,$y);$this->MultiCell(25,5,'Varones','0','L');
		$this->SetXY(175,$y);$this->MultiCell(25,5,$varones_SA_1,'0','L');
		$y+=3;
		$this->SetXY(150,$y);$this->MultiCell(25,5,'Mujeres','0','L');
		$this->SetXY(175,$y);$this->MultiCell(25,5,$mujeres_SA_1,'0','L');
		$y+=3;
		$this->SetXY(150,$y);$this->MultiCell(25,5,'TOTAL','0','L');
		$this->SetXY(175,$y);$this->MultiCell(25,5,$total_sexo_SA_1,'0','L');
		$this->Line(10,$y+10,200,$y+10);
		$y=$y+10;
		$this->SetFont('Arial','',8);
		$this->Line(10,$y,200,$y);
		$this->SetXY(10,$y);$this->MultiCell(40,5,'GONZALES','0','L');
		$this->SetXY(50,$y);$this->MultiCell(25,5,'Nuevos','0','L');
		$this->SetXY(75,$y);$this->MultiCell(25,5,$nuevos_S,'0','L');
		$y+=3;
		$this->SetXY(50,$y);$this->MultiCell(25,5,'Continuadores','0','L');
		$this->SetXY(75,$y);$this->MultiCell(25,5,$continuadores_S,'0','L');
		$y+=3;
		$this->SetXY(50,$y);$this->MultiCell(25,5,'Reingresantes','0','L');
		$this->SetXY(75,$y);$this->MultiCell(25,5,'0','0','L');
		$y+=3;
		$this->SetXY(50,$y);$this->MultiCell(25,5,'TOTAL','0','L');
		
		$this->SetXY(75,$y);$this->MultiCell(25,5,$total_categoria_S,'0','L');
		$y-=10;
		$this->SetXY(100,$y);$this->MultiCell(25,5,'Niños','0','L');
		$this->SetXY(125,$y);$this->MultiCell(25,5,$niños_S,'0','L');
		$y+=3;
		$this->SetXY(100,$y);$this->MultiCell(25,5,'Jovenes','0','L');
		$this->SetXY(125,$y);$this->MultiCell(25,5,$jovenes_S,'0','L');
		$y+=3;
		$this->SetXY(100,$y);$this->MultiCell(25,5,'Adultos','0','L');
		$this->SetXY(125,$y);$this->MultiCell(25,5,$adulto_S,'0','L');
		$y+=3;
		$this->SetXY(100,$y);$this->MultiCell(25,5,'Ancianos','0','L');
		$this->SetXY(125,$y);$this->MultiCell(25,5,$anciano_S,'0','L');
		$y+=3;
		$this->SetXY(100,$y);$this->MultiCell(25,5,'TOTAL','0','L');
		
		$this->SetXY(125,$y);$this->MultiCell(25,5,$total_edad_S,'0','L');
		$y-=12;
		$this->SetXY(150,$y);$this->MultiCell(25,5,'Varones','0','L');
		$this->SetXY(175,$y);$this->MultiCell(25,5,$varones_S,'0','L');
		$y+=3;
		$this->SetXY(150,$y);$this->MultiCell(25,5,'Mujeres','0','L');
		$this->SetXY(175,$y);$this->MultiCell(25,5,$mujeres_S,'0','L');
		$y+=3;
		$this->SetXY(150,$y);$this->MultiCell(25,5,'TOTAL','0','L');
		$this->SetXY(175,$y);$this->MultiCell(25,5,$total_sexo_S,'0','L');
		$this->Line(10,$y+10,200,$y+10);
		$y=$y+10;
		$this->SetFont('Arial','',8);
		$this->Line(10,$y,200,$y);
		$this->SetXY(10,$y);$this->MultiCell(40,5,'PAREDES','0','L');
		$this->SetXY(50,$y);$this->MultiCell(25,5,'Nuevos','0','L');
		$this->SetXY(75,$y);$this->MultiCell(25,5,$nuevos_P,'0','L');
		$y+=3;
		$this->SetXY(50,$y);$this->MultiCell(25,5,'Continuadores','0','L');
		$this->SetXY(75,$y);$this->MultiCell(25,5,$continuadores_P,'0','L');
		$y+=3;
		$this->SetXY(50,$y);$this->MultiCell(25,5,'Reingresantes','0','L');
		$this->SetXY(75,$y);$this->MultiCell(25,5,'0','0','L');
		$y+=3;
		$this->SetXY(50,$y);$this->MultiCell(25,5,'TOTAL','0','L');
		
		$this->SetXY(75,$y);$this->MultiCell(25,5,$total_categoria_P,'0','L');
		$y-=10;
		$this->SetXY(100,$y);$this->MultiCell(25,5,'Niños','0','L');
		$this->SetXY(125,$y);$this->MultiCell(25,5,$niños_P,'0','L');
		$y+=3;
		$this->SetXY(100,$y);$this->MultiCell(25,5,'Jovenes','0','L');
		$this->SetXY(125,$y);$this->MultiCell(25,5,$jovenes_P,'0','L');
		$y+=3;
		$this->SetXY(100,$y);$this->MultiCell(25,5,'Adultos','0','L');
		$this->SetXY(125,$y);$this->MultiCell(25,5,$adulto_P,'0','L');
		$y+=3;
		$this->SetXY(100,$y);$this->MultiCell(25,5,'Ancianos','0','L');
		$this->SetXY(125,$y);$this->MultiCell(25,5,$anciano_P,'0','L');
		$y+=3;
		$this->SetXY(100,$y);$this->MultiCell(25,5,'TOTAL','0','L');
		
		$this->SetXY(125,$y);$this->MultiCell(25,5,$total_edad_P,'0','L');
		$y-=12;
		$this->SetXY(150,$y);$this->MultiCell(25,5,'Varones','0','L');
		$this->SetXY(175,$y);$this->MultiCell(25,5,$varones_P,'0','L');
		$y+=3;
		$this->SetXY(150,$y);$this->MultiCell(25,5,'Mujeres','0','L');
		$this->SetXY(175,$y);$this->MultiCell(25,5,$mujeres_P,'0','L');
		$y+=3;
		$this->SetXY(150,$y);$this->MultiCell(25,5,'TOTAL','0','L');
		$this->SetXY(175,$y);$this->MultiCell(25,5,$total_sexo_P,'0','L');
		$this->Line(10,$y+10,200,$y+10);
		$y=$y+10;
		$this->SetFont('Arial','',8);
		$this->Line(10,$y,200,$y);
		$this->SetXY(10,$y);$this->MultiCell(40,5,'RAMIREZ','0','L');
		$this->SetXY(50,$y);$this->MultiCell(25,5,'Nuevos','0','L');
		$this->SetXY(75,$y);$this->MultiCell(25,5,$nuevos_R,'0','L');
		$y+=3;
		$this->SetXY(50,$y);$this->MultiCell(25,5,'Continuadores','0','L');
		$this->SetXY(75,$y);$this->MultiCell(25,5,$continuadores_R,'0','L');
		$y+=3;
		$this->SetXY(50,$y);$this->MultiCell(25,5,'Reingresantes','0','L');
		$this->SetXY(75,$y);$this->MultiCell(25,5,'0','0','L');
		$y+=3;
		$this->SetXY(50,$y);$this->MultiCell(25,5,'TOTAL','0','L');
		
		$this->SetXY(75,$y);$this->MultiCell(25,5,$total_categoria_R,'0','L');
		$y-=10;
		$this->SetXY(100,$y);$this->MultiCell(25,5,'Niños','0','L');
		$this->SetXY(125,$y);$this->MultiCell(25,5,$niños_R,'0','L');
		$y+=3;
		$this->SetXY(100,$y);$this->MultiCell(25,5,'Jovenes','0','L');
		$this->SetXY(125,$y);$this->MultiCell(25,5,$jovenes_R,'0','L');
		$y+=3;
		$this->SetXY(100,$y);$this->MultiCell(25,5,'Adultos','0','L');
		$this->SetXY(125,$y);$this->MultiCell(25,5,$adulto_R,'0','L');
		$y+=3;
		$this->SetXY(100,$y);$this->MultiCell(25,5,'Ancianos','0','L');
		$this->SetXY(125,$y);$this->MultiCell(25,5,$anciano_R,'0','L');
		$y+=3;
		$this->SetXY(100,$y);$this->MultiCell(25,5,'TOTAL','0','L');
		
		$this->SetXY(125,$y);$this->MultiCell(25,5,$total_edad_R,'0','L');
		$y-=12;
		$this->SetXY(150,$y);$this->MultiCell(25,5,'Varones','0','L');
		$this->SetXY(175,$y);$this->MultiCell(25,5,$varones_R,'0','L');
		$y+=3;
		$this->SetXY(150,$y);$this->MultiCell(25,5,'Mujeres','0','L');
		$this->SetXY(175,$y);$this->MultiCell(25,5,$mujeres_R,'0','L');
		$y+=3;
		$this->SetXY(150,$y);$this->MultiCell(25,5,'TOTAL','0','L');
		$this->SetXY(175,$y);$this->MultiCell(25,5,$total_sexo_R,'0','L');
		$this->Line(10,$y+10,200,$y+10);
		$y=$y+10;
		$this->SetFont('Arial','',8);
		$this->Line(10,$y,200,$y);
		$this->SetXY(10,$y);$this->MultiCell(40,5,'TICONA','0','L');
		$this->SetXY(50,$y);$this->MultiCell(25,5,'Nuevos','0','L');
		$this->SetXY(75,$y);$this->MultiCell(25,5,$nuevos_T,'0','L');
		$y+=3;
		$this->SetXY(50,$y);$this->MultiCell(25,5,'Continuadores','0','L');
		$this->SetXY(75,$y);$this->MultiCell(25,5,$continuadores_T,'0','L');
		$y+=3;
		$this->SetXY(50,$y);$this->MultiCell(25,5,'Reingresantes','0','L');
		$this->SetXY(75,$y);$this->MultiCell(25,5,'0','0','L');
		$y+=3;
		$this->SetXY(50,$y);$this->MultiCell(25,5,'TOTAL','0','L');
		
		$this->SetXY(75,$y);$this->MultiCell(25,5,$total_categoria_T,'0','L');
		$y-=10;
		$this->SetXY(100,$y);$this->MultiCell(25,5,'Niños','0','L');
		$this->SetXY(125,$y);$this->MultiCell(25,5,$niños_T,'0','L');
		$y+=3;
		$this->SetXY(100,$y);$this->MultiCell(25,5,'Jovenes','0','L');
		$this->SetXY(125,$y);$this->MultiCell(25,5,$jovenes_T,'0','L');
		$y+=3;
		$this->SetXY(100,$y);$this->MultiCell(25,5,'Adultos','0','L');
		$this->SetXY(125,$y);$this->MultiCell(25,5,$adulto_T,'0','L');
		$y+=3;
		$this->SetXY(100,$y);$this->MultiCell(25,5,'Ancianos','0','L');
		$this->SetXY(125,$y);$this->MultiCell(25,5,$anciano_T,'0','L');
		$y+=3;
		$this->SetXY(100,$y);$this->MultiCell(25,5,'TOTAL','0','L');
		
		$this->SetXY(125,$y);$this->MultiCell(25,5,$total_edad_T,'0','L');
		$y-=12;
		$this->SetXY(150,$y);$this->MultiCell(25,5,'Varones','0','L');
		$this->SetXY(175,$y);$this->MultiCell(25,5,$varones_T,'0','L');
		$y+=3;
		$this->SetXY(150,$y);$this->MultiCell(25,5,'Mujeres','0','L');
		$this->SetXY(175,$y);$this->MultiCell(25,5,$mujeres_T,'0','L');
		$y+=3;
		$this->SetXY(150,$y);$this->MultiCell(25,5,'TOTAL','0','L');
		$this->SetXY(175,$y);$this->MultiCell(25,5,$total_sexo_T,'0','L');
		$this->Line(10,$y+10,200,$y+10);
		$y=$y+10;
		$this->SetFont('Arial','',8);
		$this->Line(10,$y,200,$y);
		$this->SetXY(10,$y);$this->MultiCell(40,5,'ESQUIVIAS','0','L');
		$this->SetXY(50,$y);$this->MultiCell(25,5,'Nuevos','0','L');
		$this->SetXY(75,$y);$this->MultiCell(25,5,$nuevos_E,'0','L');
		$y+=3;
		$this->SetXY(50,$y);$this->MultiCell(25,5,'Continuadores','0','L');
		$this->SetXY(75,$y);$this->MultiCell(25,5,$continuadores_E,'0','L');
		$y+=3;
		$this->SetXY(50,$y);$this->MultiCell(25,5,'Reingresantes','0','L');
		$this->SetXY(75,$y);$this->MultiCell(25,5,'0','0','L');
		$y+=3;
		$this->SetXY(50,$y);$this->MultiCell(25,5,'TOTAL','0','L');
		
		$this->SetXY(75,$y);$this->MultiCell(25,5,$total_categoria_E,'0','L');
		$y-=10;
		$this->SetXY(100,$y);$this->MultiCell(25,5,'Niños','0','L');
		$this->SetXY(125,$y);$this->MultiCell(25,5,$niños_E,'0','L');
		$y+=3;
		$this->SetXY(100,$y);$this->MultiCell(25,5,'Jovenes','0','L');
		$this->SetXY(125,$y);$this->MultiCell(25,5,$jovenes_E,'0','L');
		$y+=3;
		$this->SetXY(100,$y);$this->MultiCell(25,5,'Adultos','0','L');
		$this->SetXY(125,$y);$this->MultiCell(25,5,$adulto_E,'0','L');
		$y+=3;
		$this->SetXY(100,$y);$this->MultiCell(25,5,'Ancianos','0','L');
		$this->SetXY(125,$y);$this->MultiCell(25,5,$anciano_E,'0','L');
		$y+=3;
		$this->SetXY(100,$y);$this->MultiCell(25,5,'TOTAL','0','L');
		
		$this->SetXY(125,$y);$this->MultiCell(25,5,$total_edad_E,'0','L');
		$y-=12;
		$this->SetXY(150,$y);$this->MultiCell(25,5,'Varones','0','L');
		$this->SetXY(175,$y);$this->MultiCell(25,5,$varones_E,'0','L');
		$y+=3;
		$this->SetXY(150,$y);$this->MultiCell(25,5,'Mujeres','0','L');
		$this->SetXY(175,$y);$this->MultiCell(25,5,$mujeres_E,'0','L');
		$y+=3;
		$this->SetXY(150,$y);$this->MultiCell(25,5,'TOTAL','0','L');
		$this->SetXY(175,$y);$this->MultiCell(25,5,$total_sexo_E,'0','L');
		$this->Line(10,$y+10,200,$y+10);
		$y=$y+10;
		$this->SetFont('Arial','',8);
		$this->Line(10,$y,200,$y);
		$this->SetXY(10,$y);$this->MultiCell(40,5,'ESQUIVIAS (1)','0','L');
		$this->SetXY(50,$y);$this->MultiCell(25,5,'Nuevos','0','L');
		$this->SetXY(75,$y);$this->MultiCell(25,5,$nuevos_E_1,'0','L');
		$y+=3;
		$this->SetXY(50,$y);$this->MultiCell(25,5,'Continuadores','0','L');
		$this->SetXY(75,$y);$this->MultiCell(25,5,$continuadores_E_1,'0','L');
		$y+=3;
		$this->SetXY(50,$y);$this->MultiCell(25,5,'Reingresantes','0','L');
		$this->SetXY(75,$y);$this->MultiCell(25,5,'0','0','L');
		$y+=3;
		$this->SetXY(50,$y);$this->MultiCell(25,5,'TOTAL','0','L');
		
		$this->SetXY(75,$y);$this->MultiCell(25,5,$total_categoria_E_1,'0','L');
		$y-=10;
		$this->SetXY(100,$y);$this->MultiCell(25,5,'Niños','0','L');
		$this->SetXY(125,$y);$this->MultiCell(25,5,$niños_E_1,'0','L');
		$y+=3;
		$this->SetXY(100,$y);$this->MultiCell(25,5,'Jovenes','0','L');
		$this->SetXY(125,$y);$this->MultiCell(25,5,$jovenes_E_1,'0','L');
		$y+=3;
		$this->SetXY(100,$y);$this->MultiCell(25,5,'Adultos','0','L');
		$this->SetXY(125,$y);$this->MultiCell(25,5,$adulto_E_1,'0','L');
		$y+=3;
		$this->SetXY(100,$y);$this->MultiCell(25,5,'Ancianos','0','L');
		$this->SetXY(125,$y);$this->MultiCell(25,5,$anciano_E_1,'0','L');
		$y+=3;
		$this->SetXY(100,$y);$this->MultiCell(25,5,'TOTAL','0','L');
		
		$this->SetXY(125,$y);$this->MultiCell(25,5,$total_edad_E_1,'0','L');
		$y-=12;
		$this->SetXY(150,$y);$this->MultiCell(25,5,'Varones','0','L');
		$this->SetXY(175,$y);$this->MultiCell(25,5,$varones_E_1,'0','L');
		$y+=3;
		$this->SetXY(150,$y);$this->MultiCell(25,5,'Mujeres','0','L');
		$this->SetXY(175,$y);$this->MultiCell(25,5,$mujeres_E_1,'0','L');
		$y+=3;
		$this->SetXY(150,$y);$this->MultiCell(25,5,'TOTAL','0','L');
		$this->SetXY(175,$y);$this->MultiCell(25,5,$total_sexo_E_1,'0','L');
		$this->Line(10,$y+10,200,$y+10);
		$y=$y+10;
		$this->SetFont('Arial','',8);
		$this->Line(10,$y,200,$y);
		$this->SetXY(10,$y);$this->MultiCell(40,5,'REVILLA','0','L');
		$this->SetXY(50,$y);$this->MultiCell(25,5,'Nuevos','0','L');
		$this->SetXY(75,$y);$this->MultiCell(25,5,$nuevos_RE,'0','L');
		$y+=3;
		$this->SetXY(50,$y);$this->MultiCell(25,5,'Continuadores','0','L');
		$this->SetXY(75,$y);$this->MultiCell(25,5,$continuadores_RE,'0','L');
		$y+=3;
		$this->SetXY(50,$y);$this->MultiCell(25,5,'Reingresantes','0','L');
		$this->SetXY(75,$y);$this->MultiCell(25,5,'0','0','L');
		$y+=3;
		$this->SetXY(50,$y);$this->MultiCell(25,5,'TOTAL','0','L');
		
		$this->SetXY(75,$y);$this->MultiCell(25,5,$total_categoria_RE,'0','L');
		$y-=10;
		$this->SetXY(100,$y);$this->MultiCell(25,5,'Niños','0','L');
		$this->SetXY(125,$y);$this->MultiCell(25,5,$niños_RE,'0','L');
		$y+=3;
		$this->SetXY(100,$y);$this->MultiCell(25,5,'Jovenes','0','L');
		$this->SetXY(125,$y);$this->MultiCell(25,5,$jovenes_RE,'0','L');
		$y+=3;
		$this->SetXY(100,$y);$this->MultiCell(25,5,'Adultos','0','L');
		$this->SetXY(125,$y);$this->MultiCell(25,5,$adulto_RE,'0','L');
		$y+=3;
		$this->SetXY(100,$y);$this->MultiCell(25,5,'Ancianos','0','L');
		$this->SetXY(125,$y);$this->MultiCell(25,5,$anciano_RE,'0','L');
		$y+=3;
		$this->SetXY(100,$y);$this->MultiCell(25,5,'TOTAL','0','L');
		
		$this->SetXY(125,$y);$this->MultiCell(25,5,$total_edad_RE,'0','L');
		$y-=12;
		$this->SetXY(150,$y);$this->MultiCell(25,5,'Varones','0','L');
		$this->SetXY(175,$y);$this->MultiCell(25,5,$varones_RE,'0','L');
		$y+=3;
		$this->SetXY(150,$y);$this->MultiCell(25,5,'Mujeres','0','L');
		$this->SetXY(175,$y);$this->MultiCell(25,5,$mujeres_RE,'0','L');
		$y+=3;
		$this->SetXY(150,$y);$this->MultiCell(25,5,'TOTAL','0','L');
		$this->SetXY(175,$y);$this->MultiCell(25,5,$total_sexo_RE,'0','L');
		$this->Line(10,$y+10,200,$y+10);

		$this->Line(10,$y_ini+35,10,$y+10);
		$this->Line(50,$y_ini+35,50,$y+10);
		$this->Line(100,$y_ini+35,100,$y+10);
		$this->Line(150,$y_ini+35,150,$y+10);
		$this->Line(200,$y_ini+35,200,$y+10);	
		$this->Line(10,$y+10,200,$y+10);

		$y+=15;
		$this->SetXY(10,$y);$this->MultiCell(190,5,'TOTAL DE ATENCIONES DE TODOS LOS MEDICOS','0','L');
		$y+=5;
		$this->SetFont('Arial','',7);
		$this->SetXY(10,$y);$this->MultiCell(25,5,'Nuevos','0','L');
		$this->SetFont('Arial','B',7);
		$this->SetXY(27.5,$y);$this->MultiCell(25,5,$tot_nuevos,'0','L');
		$y+=5;
		$this->SetFont('Arial','',7);
		$this->SetXY(10,$y);$this->MultiCell(25,5,'Continuadores','0','L');
		$this->SetFont('Arial','B',7);
		$this->SetXY(27.5,$y);$this->MultiCell(25,5,$tot_continuadores,'0','L');
		$y+=5;
		$this->SetFont('Arial','',7);
		$this->SetXY(10,$y);$this->MultiCell(25,5,'Reingresantes','0','L');
		$this->SetFont('Arial','B',7);
		$this->SetXY(27.5,$y);$this->MultiCell(25,5,'0','0','L');
		$y+=5;
		$this->SetFont('Arial','',7);
		$this->SetXY(10,$y);$this->MultiCell(25,5,'TOTAL','0','L');
		$this->SetFont('Arial','B',7);
		$this->SetXY(27.5,$y);$this->MultiCell(25,5,$tot_nuevos+$tot_continuadores,'0','L');
		$y-=15;
		$this->SetFont('Arial','',7);
		$this->SetXY(40,$y);$this->MultiCell(25,5,'Niños','0','L');
		$this->SetFont('Arial','B',7);
		$this->SetXY(52.5,$y);$this->MultiCell(25,5,$tot_ninos,'0','L');
		$y+=5;
		$this->SetFont('Arial','',7);
		$this->SetXY(40,$y);$this->MultiCell(25,5,'Jovenes','0','L');
		$this->SetFont('Arial','B',7);
		$this->SetXY(52.5,$y);$this->MultiCell(25,5,$tot_jovenes,'0','L');
		$y+=5;
		$this->SetFont('Arial','',7);
		$this->SetXY(40,$y);$this->MultiCell(25,5,'Adultos','0','L');
		$this->SetFont('Arial','B',7);
		$this->SetXY(52.5,$y);$this->MultiCell(25,5,$tot_adultos,'0','L');
		$y+=5;
		$this->SetFont('Arial','',7);
		$this->SetXY(40,$y);$this->MultiCell(25,5,'Ancianos','0','L');
		$this->SetFont('Arial','B',7);
		$this->SetXY(52.5,$y);$this->MultiCell(25,5,$tot_ancianos,'0','L');
		$y+=5;
		$this->SetFont('Arial','',7);
		$this->SetXY(40,$y);$this->MultiCell(25,5,'TOTAL','0','L');
		$this->SetFont('Arial','B',7);
		$this->SetXY(52.5,$y);$this->MultiCell(25,5,$tot_ninos+$tot_jovenes+$tot_adultos+$tot_ancianos,'0','L');
		$y_final = $y;
		$y-=20;
		$this->SetFont('Arial','',7);
		$this->SetXY(65,$y);$this->MultiCell(25,5,'Varones','0','L');
		$this->SetFont('Arial','B',7);
		$this->SetXY(80,$y);$this->MultiCell(25,5,$tot_varones,'0','L');
		$y+=5;
		$this->SetFont('Arial','',7);
		$this->SetXY(65,$y);$this->MultiCell(25,5,'Mujeres','0','L');
		$this->SetFont('Arial','B',7);
		$this->SetXY(80.5,$y);$this->MultiCell(25,5,$tot_mujeres,'0','L');
		$y+=5;
		$this->SetFont('Arial','',7);
		$this->SetXY(65,$y);$this->MultiCell(25,5,'TOTAL','0','L');
		$this->SetFont('Arial','B',7);
		$this->SetXY(80,$y);$this->MultiCell(25,5,$tot_varones+$tot_mujeres,'0','L');
		$this->SetFont('Arial','',7);
		$y+=15;
		$y = $y_final;
		$y-=20;
		$this->SetXY(90,$y);$this->MultiCell(50,5,'Inyectables: '.$params['inyectables'],'0','L');
		$y+=5;
		$this->SetFont('Arial','U',10);
		$this->SetXY(90,$y);$this->MultiCell(50,5,'Medicamentos de Deposito','0','L');
		$this->SetXY(150,$y);$this->MultiCell(50,5,'Aplicaciones','0','L');
		$this->SetXY(190,$y);$this->MultiCell(50,5,'Dosis','0','L');
		$y+=5;
		$this->SetXY(90,$y);$this->MultiCell(50,5,'Haldol decanoas x 50mg','0','L');
		$this->SetXY(150,$y);$this->MultiCell(50,5,$params['haldol_aplic'],'0','L');
		$this->SetXY(190,$y);$this->MultiCell(50,5,$params['haldol_dosis'],'0','L');
		$this->AddPage();
		$this->SetFont('Arial','B',10);
		$this->SetXY(10,49);$this->MultiCell(200,5,	"PABELLLON: 				Adicciones",'0','L');
		$x=5;
		$y=25;
		$y_ini = $y;
		$page_b = 275;
		$y=$y+35;
		/*ALVARADO*/
		$mujeres_A = 0;
		$varones_A = 0;
		$nuevos_A = 0;
		$continuadores_A = 0;
		$niños_A = 0;
		$jovenes_A = 0;
		$adulto_A = 0;
		$anciano_A = 0;
		/*ALVARADO 1*/
		/*--------*/
		$mujeres_A_1 = 0;
		$varones_A_1 = 0;
		$nuevos_A_1 = 0;
		$continuadores_A_1 = 0;
		$niños_A_1 = 0;
		$jovenes_A_1 = 0;
		$adulto_A_1 = 0;
		$anciano_A_1 = 0;
		/*SANCHEZ*/
		$mujeres_SA = 0;
		$varones_SA = 0;
		$nuevos_SA = 0;
		$continuadores_SA = 0;
		$niños_SA = 0;
		$jovenes_SA = 0;
		$adulto_SA = 0;
		$anciano_SA = 0;
		/*SANCHEZ (1)*/
		$mujeres_SA_1 = 0;
		$varones_SA_1 = 0;
		$nuevos_SA_1 = 0;
		$continuadores_SA_1 = 0;
		$niños_SA_1 = 0;
		$jovenes_SA_1 = 0;
		$adulto_SA_1 = 0;
		$anciano_SA_1 = 0;
		/*GONZALES*/
		$mujeres_S = 0;
		$varones_S = 0;
		$nuevos_S = 0;
		$continuadores_S = 0;
		$niños_S = 0;
		$jovenes_S = 0;
		$adulto_S = 0;
		$anciano_S = 0;
		/*PAREDES*/
		$mujeres_P = 0;
		$varones_P = 0;
		$nuevos_P = 0;
		$continuadores_P = 0;
		$niños_P = 0;
		$jovenes_P = 0;
		$adulto_P = 0;
		$anciano_P = 0;
		/*RAMIREZ*/
		$mujeres_R = 0;
		$varones_R = 0;
		$nuevos_R = 0;
		$continuadores_R = 0;
		$niños_R = 0;
		$jovenes_R = 0;
		$adulto_R = 0;
		$anciano_R = 0;
		/*TICONA*/
		$mujeres_T = 0;
		$varones_T = 0;
		$nuevos_T = 0;
		$continuadores_T = 0;
		$niños_T = 0;
		$jovenes_T = 0;
		$adulto_T = 0;
		$anciano_T = 0;
		/*ESQUIVIAS*/
		$mujeres_E = 0;
		$varones_E = 0;
		$nuevos_E = 0;
		$continuadores_E = 0;
		$niños_E = 0;
		$jovenes_E = 0;
		$adulto_E = 0;
		$anciano_E = 0;
		/*ESQUIVIAS*/
		$mujeres_E_1 = 0;
		$varones_E_1 = 0;
		$nuevos_E_1 = 0;
		$continuadores_E_1 = 0;
		$niños_E_1 = 0;
		$jovenes_E_1 = 0;
		$adulto_E_1 = 0;
		$anciano_E_1 = 0;
		/*REVILLA*/
		$mujeres_RE = 0;
		$varones_RE = 0;
		$nuevos_RE = 0;
		$continuadores_RE = 0;
		$niños_RE = 0;
		$jovenes_RE = 0;
		$adulto_RE = 0;
		$anciano_RE = 0;

		for($i = 0;$i<count($diario);$i++){
			
			for($j = 0;$j<count($diario[$i]['consulta']);$j++){

			if(isset($diario[$i]["consulta"][$j]['paciente']['fecha_na'])){
					$fechaarte = $diario[$i]['fech']->sec;

					if(isset($diario[$i]["consulta"][$j]['paciente']['fecha_na']['sec'])){
						$fecha_nacimiento = $diario[$i]["consulta"][$j]['paciente']['fecha_na']['sec'];
					}else{
						$fecha_nacimiento = $diario[$i]["consulta"][$j]['paciente']['fecha_na']->sec;	
					}
					
					$edad = $fechaarte-$fecha_nacimiento;
					$edad = floor($edad/(60*60*24*365));

				}else{
					
					$edad = -1;
					
				}
				//ALVARADO
				if(floatval($diario[$i]["consulta"][$j]['paciente']['sexo']) == 0 && $diario[$i]['consulta'][$j]['esta'] != '5' && $diario[$i]['medico']['_id'] =='521f83304d4a13881700000d' && $diario[$i]['modulo'] != 'MH' && floatval($diario[$i]["consulta"][$j]['cate']) != 8){
					$mujeres_A++;
				}
				if(floatval($diario[$i]["consulta"][$j]['paciente']['sexo']) == 1 && $diario[$i]['consulta'][$j]['esta'] != '5' && $diario[$i]['medico']['_id'] == '521f83304d4a13881700000d' && $diario[$i]['modulo'] != 'MH' && floatval($diario[$i]["consulta"][$j]['cate']) != 8){
					$varones_A++;
				}
				if($diario[$i]["consulta"][$j]['esta'] == 2 && $diario[$i]['consulta'][$j]['esta'] != '5' &&  $diario[$i]['medico']['_id'] == '521f83304d4a13881700000d' && $diario[$i]['modulo'] != 'MH' && floatval($diario[$i]["consulta"][$j]['cate']) != 8){
					$nuevos_A++;
				}
				if($diario[$i]["consulta"][$j]['esta'] == 3  && $diario[$i]['consulta'][$j]['esta'] != '5' && $diario[$i]['medico']['_id'] == '521f83304d4a13881700000d' && $diario[$i]['modulo'] != 'MH' && floatval($diario[$i]["consulta"][$j]['cate']) != 8){
					$continuadores_A++;
				}
				if($edad<11  && $diario[$i]['medico']['_id'] == '521f83304d4a13881700000d' && $diario[$i]['modulo'] != 'MH' && $diario[$i]['consulta'][$j]['esta'] != '5' && floatval($diario[$i]["consulta"][$j]['cate']) != 8){
						$niños_A++;
				}
				if($edad>=11 && $edad<18 && $diario[$i]['medico']['_id'] == '521f83304d4a13881700000d' && $diario[$i]['modulo'] != 'MH' && $diario[$i]['consulta'][$j]['esta'] != '5' && floatval($diario[$i]["consulta"][$j]['cate']) != 8){
						$jovenes_A++;
				}
				if($edad>=18 && $edad<60 && $diario[$i]['medico']['_id'] == '521f83304d4a13881700000d' && $diario[$i]['modulo'] != 'MH' && $diario[$i]['consulta'][$j]['esta'] != '5' && floatval($diario[$i]["consulta"][$j]['cate']) != 8){
						$adulto_A++;
				}
				if($edad>=60 && $diario[$i]['medico']['_id'] == '521f83304d4a13881700000d' && $diario[$i]['modulo'] != 'MH' && $diario[$i]['consulta'][$j]['esta'] != '5' && floatval($diario[$i]["consulta"][$j]['cate']) != 8){
						$anciano_A++;
				}
				//ALVARADO (1)
				if(floatval($diario[$i]["consulta"][$j]['paciente']['sexo']) == 0 && $diario[$i]['consulta'][$j]['esta'] != '5' && $diario[$i]['medico']['_id'] =='5b6dcd9c3e6037172f8b4570' && $diario[$i]['modulo'] != 'MH' && floatval($diario[$i]["consulta"][$j]['cate']) != 8){
					$mujeres_A_1++;
				}
				if(floatval($diario[$i]["consulta"][$j]['paciente']['sexo']) == 1 && $diario[$i]['consulta'][$j]['esta'] != '5' && $diario[$i]['medico']['_id'] == '5b6dcd9c3e6037172f8b4570' && $diario[$i]['modulo'] != 'MH' && floatval($diario[$i]["consulta"][$j]['cate']) != 8){
					$varones_A_1++;
				}
				if($diario[$i]["consulta"][$j]['esta'] == 2 && $diario[$i]['consulta'][$j]['esta'] != '5' &&  $diario[$i]['medico']['_id'] == '5b6dcd9c3e6037172f8b4570' && $diario[$i]['modulo'] != 'MH' && floatval($diario[$i]["consulta"][$j]['cate']) != 8){
					$nuevos_A_1++;
				}
				if($diario[$i]["consulta"][$j]['esta'] == 3  && $diario[$i]['consulta'][$j]['esta'] != '5' && $diario[$i]['medico']['_id'] == '5b6dcd9c3e6037172f8b4570' && $diario[$i]['modulo'] != 'MH' && floatval($diario[$i]["consulta"][$j]['cate']) != 8){
					$continuadores_A_1++;
				}
				if($edad<11  && $diario[$i]['medico']['_id'] == '5b6dcd9c3e6037172f8b4570' && $diario[$i]['modulo'] != 'MH' && $diario[$i]['consulta'][$j]['esta'] != '5' && floatval($diario[$i]["consulta"][$j]['cate']) != 8){
						$niños_A_1++;
				}
				if($edad>=11 && $edad<18 && $diario[$i]['medico']['_id'] == '5b6dcd9c3e6037172f8b4570' && $diario[$i]['modulo'] != 'MH' && $diario[$i]['consulta'][$j]['esta'] != '5' && floatval($diario[$i]["consulta"][$j]['cate']) != 8){
						$jovenes_A_1++;
				}
				if($edad>=18 && $edad<60 && $diario[$i]['medico']['_id'] == '5b6dcd9c3e6037172f8b4570' && $diario[$i]['modulo'] != 'MH' && $diario[$i]['consulta'][$j]['esta'] != '5' && floatval($diario[$i]["consulta"][$j]['cate']) != 8){
						$adulto_A_1++;
				}
				if($edad>=60 && $diario[$i]['medico']['_id'] == '5b6dcd9c3e6037172f8b4570' && $diario[$i]['modulo'] != 'MH' && $diario[$i]['consulta'][$j]['esta'] != '5' && floatval($diario[$i]["consulta"][$j]['cate']) != 8){
						$anciano_A_1++;
				}
				//SANCHEZ
				if(floatval($diario[$i]["consulta"][$j]['paciente']['sexo']) == 0 && $diario[$i]['consulta'][$j]['esta'] != '5' && $diario[$i]['medico']['_id'] =='587e2e503e60376b778b4568' && $diario[$i]['modulo'] != 'MH' && floatval($diario[$i]["consulta"][$j]['cate']) != 8){
					$mujeres_SA++;
				}
				if(floatval($diario[$i]["consulta"][$j]['paciente']['sexo']) == 1 && $diario[$i]['consulta'][$j]['esta'] != '5' && $diario[$i]['medico']['_id'] == '587e2e503e60376b778b4568' && $diario[$i]['modulo'] != 'MH' && floatval($diario[$i]["consulta"][$j]['cate']) != 8){
					$varones_SA++;
				}
				if($diario[$i]["consulta"][$j]['esta'] == 2 && $diario[$i]['consulta'][$j]['esta'] != '5' &&  $diario[$i]['medico']['_id'] == '587e2e503e60376b778b4568' && $diario[$i]['modulo'] != 'MH' && floatval($diario[$i]["consulta"][$j]['cate']) != 8){
					$nuevos_SA++;
				}
				if($diario[$i]["consulta"][$j]['esta'] == 3  && $diario[$i]['consulta'][$j]['esta'] != '5' && $diario[$i]['medico']['_id'] == '587e2e503e60376b778b4568' && $diario[$i]['modulo'] != 'MH' && floatval($diario[$i]["consulta"][$j]['cate']) != 8){
					$continuadores_SA++;
				}
				if($edad<11  && $diario[$i]['medico']['_id'] == '587e2e503e60376b778b4568' && $diario[$i]['modulo'] != 'MH' && $diario[$i]['consulta'][$j]['esta'] != '5' && floatval($diario[$i]["consulta"][$j]['cate']) != 8){
						$niños_SA++;
				}
				if($edad>=11 && $edad<18 && $diario[$i]['medico']['_id'] == '587e2e503e60376b778b4568' && $diario[$i]['modulo'] != 'MH' && $diario[$i]['consulta'][$j]['esta'] != '5' && floatval($diario[$i]["consulta"][$j]['cate']) != 8){
						$jovenes_SA++;
				}
				if($edad>=18 && $edad<60 && $diario[$i]['medico']['_id'] == '587e2e503e60376b778b4568' && $diario[$i]['modulo'] != 'MH' && $diario[$i]['consulta'][$j]['esta'] != '5' && floatval($diario[$i]["consulta"][$j]['cate']) != 8){
						$adulto_SA++;
				}
				if($edad>=60 && $diario[$i]['medico']['_id'] == '587e2e503e60376b778b4568' && $diario[$i]['modulo'] != 'MH' && $diario[$i]['consulta'][$j]['esta'] != '5' && floatval($diario[$i]["consulta"][$j]['cate']) != 8){
						$anciano_SA++;
				}
				//SANCHEZ (1)
				if(floatval($diario[$i]["consulta"][$j]['paciente']['sexo']) == 0 && $diario[$i]['consulta'][$j]['esta'] != '5' && $diario[$i]['medico']['_id'] =='5b6dce713e6037562f8b456c' && $diario[$i]['modulo'] != 'MH' && floatval($diario[$i]["consulta"][$j]['cate']) != 8){
					$mujeres_SA_1++;
				}
				if(floatval($diario[$i]["consulta"][$j]['paciente']['sexo']) == 1 && $diario[$i]['consulta'][$j]['esta'] != '5' && $diario[$i]['medico']['_id'] == '5b6dce713e6037562f8b456c' && $diario[$i]['modulo'] != 'MH' && floatval($diario[$i]["consulta"][$j]['cate']) != 8){
					$varones_SA_1++;
				}
				if($diario[$i]["consulta"][$j]['esta'] == 2 && $diario[$i]['consulta'][$j]['esta'] != '5' &&  $diario[$i]['medico']['_id'] == '5b6dce713e6037562f8b456c' && $diario[$i]['modulo'] != 'MH' && floatval($diario[$i]["consulta"][$j]['cate']) != 8){
					$nuevos_SA_1++;
				}
				if($diario[$i]["consulta"][$j]['esta'] == 3  && $diario[$i]['consulta'][$j]['esta'] != '5' && $diario[$i]['medico']['_id'] == '5b6dce713e6037562f8b456c' && $diario[$i]['modulo'] != 'MH' && floatval($diario[$i]["consulta"][$j]['cate']) != 8){
					$continuadores_SA_1++;
				}
				if($edad<11  && $diario[$i]['medico']['_id'] == '5b6dce713e6037562f8b456c' && $diario[$i]['modulo'] != 'MH' && $diario[$i]['consulta'][$j]['esta'] != '5' && floatval($diario[$i]["consulta"][$j]['cate']) != 8){
						$niños_SA_1++;
				}
				if($edad>=11 && $edad<18 && $diario[$i]['medico']['_id'] == '5b6dce713e6037562f8b456c' && $diario[$i]['modulo'] != 'MH' && $diario[$i]['consulta'][$j]['esta'] != '5' && floatval($diario[$i]["consulta"][$j]['cate']) != 8){
						$jovenes_SA_1++;
				}
				if($edad>=18 && $edad<60 && $diario[$i]['medico']['_id'] == '5b6dce713e6037562f8b456c' && $diario[$i]['modulo'] != 'MH' && $diario[$i]['consulta'][$j]['esta'] != '5' && floatval($diario[$i]["consulta"][$j]['cate']) != 8){
						$adulto_SA_1++;
				}
				if($edad>=60 && $diario[$i]['medico']['_id'] == '5b6dce713e6037562f8b456c' && $diario[$i]['modulo'] != 'MH' && $diario[$i]['consulta'][$j]['esta'] != '5' && floatval($diario[$i]["consulta"][$j]['cate']) != 8){
						$anciano_SA_1++;
				}
				//GONZALES
				if(floatval($diario[$i]["consulta"][$j]['paciente']['sexo']) == 0 && $diario[$i]['consulta'][$j]['esta'] != '5' && $diario[$i]['medico']['_id'] =='5ac3b2683e6037085c8b4567' && $diario[$i]['modulo'] != 'MH' && floatval($diario[$i]["consulta"][$j]['cate']) != 8){
					$mujeres_S++;
				}
				if(floatval($diario[$i]["consulta"][$j]['paciente']['sexo']) == 1 && $diario[$i]['consulta'][$j]['esta'] != '5' && $diario[$i]['medico']['_id'] == '5ac3b2683e6037085c8b4567' && $diario[$i]['modulo'] != 'MH' && floatval($diario[$i]["consulta"][$j]['cate']) != 8){
					$varones_S++;
				}
				if($diario[$i]["consulta"][$j]['esta'] == 2 && $diario[$i]['consulta'][$j]['esta'] != '5' &&  $diario[$i]['medico']['_id'] == '5ac3b2683e6037085c8b4567' && $diario[$i]['modulo'] != 'MH' && floatval($diario[$i]["consulta"][$j]['cate']) != 8){
					$nuevos_S++;
				}
				if($diario[$i]["consulta"][$j]['esta'] == 3  && $diario[$i]['consulta'][$j]['esta'] != '5' && $diario[$i]['medico']['_id'] == '5ac3b2683e6037085c8b4567' && $diario[$i]['modulo'] != 'MH' && floatval($diario[$i]["consulta"][$j]['cate']) != 8){
					$continuadores_S++;
				}
				if($edad<11  && $diario[$i]['medico']['_id'] == '5ac3b2683e6037085c8b4567' && $diario[$i]['modulo'] != 'MH' && $diario[$i]['consulta'][$j]['esta'] != '5' && floatval($diario[$i]["consulta"][$j]['cate']) != 8){
						$niños_S++;
				}
				if($edad>=11 && $edad<18 && $diario[$i]['medico']['_id'] == '5ac3b2683e6037085c8b4567' && $diario[$i]['modulo'] != 'MH' && $diario[$i]['consulta'][$j]['esta'] != '5' && floatval($diario[$i]["consulta"][$j]['cate']) != 8){
						$jovenes_S++;
				}
				if($edad>=18 && $edad<60 && $diario[$i]['medico']['_id'] == '5ac3b2683e6037085c8b4567' && $diario[$i]['modulo'] != 'MH' && $diario[$i]['consulta'][$j]['esta'] != '5' && floatval($diario[$i]["consulta"][$j]['cate']) != 8){
						$adulto_S++;
				}
				if($edad>=60 && $diario[$i]['medico']['_id'] == '5ac3b2683e6037085c8b4567' && $diario[$i]['modulo'] != 'MH' && $diario[$i]['consulta'][$j]['esta'] != '5' && floatval($diario[$i]["consulta"][$j]['cate']) != 8){
						$anciano_S++;
				}
				//PAREDES
				if(floatval($diario[$i]["consulta"][$j]['paciente']['sexo']) == 0 && $diario[$i]['consulta'][$j]['esta'] != '5' && $diario[$i]['medico']['_id'] =='5908b7b43e60379f5e8b4567' && $diario[$i]['modulo'] != 'MH' && floatval($diario[$i]["consulta"][$j]['cate']) != 8){
					$mujeres_P++;
				}
				if(floatval($diario[$i]["consulta"][$j]['paciente']['sexo']) == 1 && $diario[$i]['consulta'][$j]['esta'] != '5' && $diario[$i]['medico']['_id'] == '5908b7b43e60379f5e8b4567' && $diario[$i]['modulo'] != 'MH' && floatval($diario[$i]["consulta"][$j]['cate']) != 8){
					$varones_P++;
				}
				if($diario[$i]["consulta"][$j]['esta'] == 2 && $diario[$i]['consulta'][$j]['esta'] != '5' &&  $diario[$i]['medico']['_id'] == '5908b7b43e60379f5e8b4567' && $diario[$i]['modulo'] != 'MH' && floatval($diario[$i]["consulta"][$j]['cate']) != 8){
					$nuevos_P++;
				}
				if($diario[$i]["consulta"][$j]['esta'] == 3  && $diario[$i]['consulta'][$j]['esta'] != '5' && $diario[$i]['medico']['_id'] == '5908b7b43e60379f5e8b4567' && $diario[$i]['modulo'] != 'MH' && floatval($diario[$i]["consulta"][$j]['cate']) != 8){
					$continuadores_P++;
				}
				if($edad<11  && $diario[$i]['medico']['_id'] == '5908b7b43e60379f5e8b4567' && $diario[$i]['modulo'] != 'MH' && $diario[$i]['consulta'][$j]['esta'] != '5' && floatval($diario[$i]["consulta"][$j]['cate']) != 8){
						$niños_P++;
				}
				if($edad>=11 && $edad<18 && $diario[$i]['medico']['_id'] == '5908b7b43e60379f5e8b4567' && $diario[$i]['modulo'] != 'MH' && $diario[$i]['consulta'][$j]['esta'] != '5' && floatval($diario[$i]["consulta"][$j]['cate']) != 8){
						$jovenes_P++;
				}
				if($edad>=18 && $edad<60 && $diario[$i]['medico']['_id'] == '5908b7b43e60379f5e8b4567' && $diario[$i]['modulo'] != 'MH' && $diario[$i]['consulta'][$j]['esta'] != '5' && floatval($diario[$i]["consulta"][$j]['cate']) != 8){
						$adulto_P++;
				}
				if($edad>=60 && $diario[$i]['medico']['_id'] == '5908b7b43e60379f5e8b4567' && $diario[$i]['modulo'] != 'MH' && $diario[$i]['consulta'][$j]['esta'] != '5' && floatval($diario[$i]["consulta"][$j]['cate']) != 8){
						$anciano_P++;
				}
				//RAMIREZ
				if(floatval($diario[$i]["consulta"][$j]['paciente']['sexo']) == 0 && $diario[$i]['consulta'][$j]['esta'] != '5' && $diario[$i]['medico']['_id'] =='593ec2743e6037f3588b456b' && $diario[$i]['modulo'] != 'MH' && floatval($diario[$i]["consulta"][$j]['cate']) != 8){
					$mujeres_R++;
				}
				if(floatval($diario[$i]["consulta"][$j]['paciente']['sexo']) == 1 && $diario[$i]['consulta'][$j]['esta'] != '5' && $diario[$i]['medico']['_id'] == '593ec2743e6037f3588b456b' && $diario[$i]['modulo'] != 'MH' && floatval($diario[$i]["consulta"][$j]['cate']) != 8){
					$varones_R++;
				}
				if($diario[$i]["consulta"][$j]['esta'] == 2 && $diario[$i]['consulta'][$j]['esta'] != '5' &&  $diario[$i]['medico']['_id'] == '593ec2743e6037f3588b456b' && $diario[$i]['modulo'] != 'MH' && floatval($diario[$i]["consulta"][$j]['cate']) != 8){
					$nuevos_R++;
				}
				if($diario[$i]["consulta"][$j]['esta'] == 3  && $diario[$i]['consulta'][$j]['esta'] != '5' && $diario[$i]['medico']['_id'] == '593ec2743e6037f3588b456b' && $diario[$i]['modulo'] != 'MH' && floatval($diario[$i]["consulta"][$j]['cate']) != 8){
					$continuadores_R++;
				}
				if($edad<11  && $diario[$i]['medico']['_id'] == '593ec2743e6037f3588b456b' && $diario[$i]['modulo'] != 'MH' && $diario[$i]['consulta'][$j]['esta'] != '5' && floatval($diario[$i]["consulta"][$j]['cate']) != 8){
						$niños_R++;
				}
				if($edad>=11 && $edad<18 && $diario[$i]['medico']['_id'] == '593ec2743e6037f3588b456b' && $diario[$i]['modulo'] != 'MH' && $diario[$i]['consulta'][$j]['esta'] != '5' && floatval($diario[$i]["consulta"][$j]['cate']) != 8){
						$jovenes_R++;
				}
				if($edad>=18 && $edad<60 && $diario[$i]['medico']['_id'] == '593ec2743e6037f3588b456b' && $diario[$i]['modulo'] != 'MH' && $diario[$i]['consulta'][$j]['esta'] != '5' && floatval($diario[$i]["consulta"][$j]['cate']) != 8){
						$adulto_R++;
				}
				if($edad>=60 && $diario[$i]['medico']['_id'] == '593ec2743e6037f3588b456b' && $diario[$i]['modulo'] != 'MH' && $diario[$i]['consulta'][$j]['esta'] != '5' && floatval($diario[$i]["consulta"][$j]['cate']) != 8){
						$anciano_R++;
				}
				//TICONA
				if(floatval($diario[$i]["consulta"][$j]['paciente']['sexo']) == 0 && $diario[$i]['consulta'][$j]['esta'] != '5' && $diario[$i]['medico']['_id'] =='5977ae6c3e603746248b4568' && $diario[$i]['modulo'] != 'MH' && floatval($diario[$i]["consulta"][$j]['cate']) != 8){
					$mujeres_T++;
				}
				if(floatval($diario[$i]["consulta"][$j]['paciente']['sexo']) == 1 && $diario[$i]['consulta'][$j]['esta'] != '5' && $diario[$i]['medico']['_id'] == '5977ae6c3e603746248b4568' && $diario[$i]['modulo'] != 'MH' && floatval($diario[$i]["consulta"][$j]['cate']) != 8){
					$varones_T++;
				}
				if($diario[$i]["consulta"][$j]['esta'] == 2 && $diario[$i]['consulta'][$j]['esta'] != '5' &&  $diario[$i]['medico']['_id'] == '5977ae6c3e603746248b4568' && $diario[$i]['modulo'] != 'MH' && floatval($diario[$i]["consulta"][$j]['cate']) != 8){
					$nuevos_T++;
				}
				if($diario[$i]["consulta"][$j]['esta'] == 3  && $diario[$i]['consulta'][$j]['esta'] != '5' && $diario[$i]['medico']['_id'] == '5977ae6c3e603746248b4568' && $diario[$i]['modulo'] != 'MH' && floatval($diario[$i]["consulta"][$j]['cate']) != 8){
					$continuadores_T++;
				}
				if($edad<11  && $diario[$i]['medico']['_id'] == '5977ae6c3e603746248b4568' && $diario[$i]['modulo'] != 'MH' && $diario[$i]['consulta'][$j]['esta'] != '5' && floatval($diario[$i]["consulta"][$j]['cate']) != 8){
						$niños_T++;
				}
				if($edad>=11 && $edad<18 && $diario[$i]['medico']['_id'] == '5977ae6c3e603746248b4568' && $diario[$i]['modulo'] != 'MH' && $diario[$i]['consulta'][$j]['esta'] != '5' && floatval($diario[$i]["consulta"][$j]['cate']) != 8){
						$jovenes_T++;
				}
				if($edad>=18 && $edad<60 && $diario[$i]['medico']['_id'] == '5977ae6c3e603746248b4568' && $diario[$i]['modulo'] != 'MH' && $diario[$i]['consulta'][$j]['esta'] != '5' && floatval($diario[$i]["consulta"][$j]['cate']) != 8){
						$adulto_T++;
				}
				if($edad>=60 && $diario[$i]['medico']['_id'] == '5977ae6c3e603746248b4568' && $diario[$i]['modulo'] != 'MH' && $diario[$i]['consulta'][$j]['esta'] != '5' && floatval($diario[$i]["consulta"][$j]['cate']) != 8){
						$anciano_T++;
				}
				//ESQUIVIAS
				if(floatval($diario[$i]["consulta"][$j]['paciente']['sexo']) == 0 && $diario[$i]['consulta'][$j]['esta'] != '5' && $diario[$i]['medico']['_id'] =='57cf1d608e73586c08000095' && $diario[$i]['modulo'] != 'MH' && floatval($diario[$i]["consulta"][$j]['cate']) != 8){
					$mujeres_E++;
				}
				if(floatval($diario[$i]["consulta"][$j]['paciente']['sexo']) == 1 && $diario[$i]['consulta'][$j]['esta'] != '5' && $diario[$i]['medico']['_id'] == '57cf1d608e73586c08000095' && $diario[$i]['modulo'] != 'MH' && floatval($diario[$i]["consulta"][$j]['cate']) != 8){
					$varones_E++;
				}
				if($diario[$i]["consulta"][$j]['esta'] == 2 && $diario[$i]['consulta'][$j]['esta'] != '5' &&  $diario[$i]['medico']['_id'] == '57cf1d608e73586c08000095' && $diario[$i]['modulo'] != 'MH' && floatval($diario[$i]["consulta"][$j]['cate']) != 8){
					$nuevos_E++;
				}
				if($diario[$i]["consulta"][$j]['esta'] == 3  && $diario[$i]['consulta'][$j]['esta'] != '5' && $diario[$i]['medico']['_id'] == '57cf1d608e73586c08000095' && $diario[$i]['modulo'] != 'MH' && floatval($diario[$i]["consulta"][$j]['cate']) != 8){
					$continuadores_E++;
				}
				if($edad<11  && $diario[$i]['medico']['_id'] == '57cf1d608e73586c08000095' && $diario[$i]['modulo'] != 'MH' && $diario[$i]['consulta'][$j]['esta'] != '5' && floatval($diario[$i]["consulta"][$j]['cate']) != 8){
						$niños_E++;
				}
				if($edad>=11 && $edad<18 && $diario[$i]['medico']['_id'] == '57cf1d608e73586c08000095' && $diario[$i]['modulo'] != 'MH' && $diario[$i]['consulta'][$j]['esta'] != '5' && floatval($diario[$i]["consulta"][$j]['cate']) != 8){
						$jovenes_E++;
				}
				if($edad>=18 && $edad<60 && $diario[$i]['medico']['_id'] == '57cf1d608e73586c08000095' && $diario[$i]['modulo'] != 'MH' && $diario[$i]['consulta'][$j]['esta'] != '5' && floatval($diario[$i]["consulta"][$j]['cate']) != 8){
						$adulto_E++;
				}
				if($edad>=60 && $diario[$i]['medico']['_id'] == '57cf1d608e73586c08000095' && $diario[$i]['modulo'] != 'MH' && $diario[$i]['consulta'][$j]['esta'] != '5' && floatval($diario[$i]["consulta"][$j]['cate']) != 8){
						$anciano_E++;
				}
				//ESQUIVIAS (1)
				if(floatval($diario[$i]["consulta"][$j]['paciente']['sexo']) == 0 && $diario[$i]['consulta'][$j]['esta'] != '5' && $diario[$i]['medico']['_id'] =='5b6dce0f3e6037532f8b456b' && $diario[$i]['modulo'] != 'MH' && floatval($diario[$i]["consulta"][$j]['cate']) != 8){
					$mujeres_E_1++;
				}
				if(floatval($diario[$i]["consulta"][$j]['paciente']['sexo']) == 1 && $diario[$i]['consulta'][$j]['esta'] != '5' && $diario[$i]['medico']['_id'] == '5b6dce0f3e6037532f8b456b' && $diario[$i]['modulo'] != 'MH' && floatval($diario[$i]["consulta"][$j]['cate']) != 8){
					$varones_E_1++;
				}
				if($diario[$i]["consulta"][$j]['esta'] == 2 && $diario[$i]['consulta'][$j]['esta'] != '5' &&  $diario[$i]['medico']['_id'] == '5b6dce0f3e6037532f8b456b' && $diario[$i]['modulo'] != 'MH' && floatval($diario[$i]["consulta"][$j]['cate']) != 8){
					$nuevos_E_1++;
				}
				if($diario[$i]["consulta"][$j]['esta'] == 3  && $diario[$i]['consulta'][$j]['esta'] != '5' && $diario[$i]['medico']['_id'] == '5b6dce0f3e6037532f8b456b' && $diario[$i]['modulo'] != 'MH' && floatval($diario[$i]["consulta"][$j]['cate']) != 8){
					$continuadores_E_1++;
				}
				if($edad<11  && $diario[$i]['medico']['_id'] == '5b6dce0f3e6037532f8b456b' && $diario[$i]['modulo'] != 'MH' && $diario[$i]['consulta'][$j]['esta'] != '5' && floatval($diario[$i]["consulta"][$j]['cate']) != 8){
						$niños_E_1++;
				}
				if($edad>=11 && $edad<18 && $diario[$i]['medico']['_id'] == '5b6dce0f3e6037532f8b456b' && $diario[$i]['modulo'] != 'MH' && $diario[$i]['consulta'][$j]['esta'] != '5' && floatval($diario[$i]["consulta"][$j]['cate']) != 8){
						$jovenes_E_1++;
				}
				if($edad>=18 && $edad<60 && $diario[$i]['medico']['_id'] == '5b6dce0f3e6037532f8b456b' && $diario[$i]['modulo'] != 'MH' && $diario[$i]['consulta'][$j]['esta'] != '5' && floatval($diario[$i]["consulta"][$j]['cate']) != 8){
						$adulto_E_1++;
				}
				if($edad>=60 && $diario[$i]['medico']['_id'] == '5b6dce0f3e6037532f8b456b' && $diario[$i]['modulo'] != 'MH' && $diario[$i]['consulta'][$j]['esta'] != '5' && floatval($diario[$i]["consulta"][$j]['cate']) != 8){
						$anciano_E_1++;
				}
				//REVILLA
				if(floatval($diario[$i]["consulta"][$j]['paciente']['sexo']) == 0 && $diario[$i]['consulta'][$j]['esta'] != '5' && $diario[$i]['medico']['_id'] =='587533593e6037474b8b4568' && $diario[$i]['modulo'] != 'MH' && floatval($diario[$i]["consulta"][$j]['cate']) != 8){
					$mujeres_RE++;
				}
				if(floatval($diario[$i]["consulta"][$j]['paciente']['sexo']) == 1 && $diario[$i]['consulta'][$j]['esta'] != '5' && $diario[$i]['medico']['_id'] == '587533593e6037474b8b4568' && $diario[$i]['modulo'] != 'MH' && floatval($diario[$i]["consulta"][$j]['cate']) != 8){
					$varones_RE++;
				}
				if($diario[$i]["consulta"][$j]['esta'] == 2 && $diario[$i]['consulta'][$j]['esta'] != '5' &&  $diario[$i]['medico']['_id'] == '587533593e6037474b8b4568' && $diario[$i]['modulo'] != 'MH' && floatval($diario[$i]["consulta"][$j]['cate']) != 8){
					$nuevos_RE++;
				}
				if($diario[$i]["consulta"][$j]['esta'] == 3  && $diario[$i]['consulta'][$j]['esta'] != '5' && $diario[$i]['medico']['_id'] == '587533593e6037474b8b4568' && $diario[$i]['modulo'] != 'MH' && floatval($diario[$i]["consulta"][$j]['cate']) != 8){
					$continuadores_RE++;
				}
				if($edad<11  && $diario[$i]['medico']['_id'] == '587533593e6037474b8b4568' && $diario[$i]['modulo'] != 'MH' && $diario[$i]['consulta'][$j]['esta'] != '5' && floatval($diario[$i]["consulta"][$j]['cate']) != 8){
						$niños_RE++;
				}
				if($edad>=11 && $edad<18 && $diario[$i]['medico']['_id'] == '587533593e6037474b8b4568' && $diario[$i]['modulo'] != 'MH' && $diario[$i]['consulta'][$j]['esta'] != '5' && floatval($diario[$i]["consulta"][$j]['cate']) != 8){
						$jovenes_RE++;
				}
				if($edad>=18 && $edad<60 && $diario[$i]['medico']['_id'] == '587533593e6037474b8b4568' && $diario[$i]['modulo'] != 'MH' && $diario[$i]['consulta'][$j]['esta'] != '5' && floatval($diario[$i]["consulta"][$j]['cate']) != 8){
						$adulto_RE++;
				}
				if($edad>=60 && $diario[$i]['medico']['_id'] == '587533593e6037474b8b4568' && $diario[$i]['modulo'] != 'MH' && $diario[$i]['consulta'][$j]['esta'] != '5' && floatval($diario[$i]["consulta"][$j]['cate']) != 8){
						$anciano_RE++;
				}
			}
		}


		$total_categoria_A = $nuevos_A+$continuadores_A;
		$total_edad_A = $anciano_A+$adulto_A+$jovenes_A+$niños_A;
		$total_sexo_A = $mujeres_A + $varones_A;
		$total_categoria_A_1 = $nuevos_A_1+$continuadores_A_1;
		$total_edad_A_1 = $anciano_A_1+$adulto_A_1+$jovenes_A_1+$niños_A_1;
		$total_sexo_A_1 = $mujeres_A_1 + $varones_A_1;
		$total_categoria_SA = $nuevos_SA+$continuadores_SA;
		$total_edad_SA = $anciano_SA+$adulto_SA+$jovenes_SA+$niños_SA;
		$total_sexo_SA = $mujeres_SA + $varones_SA;
		$total_categoria_SA_1 = $nuevos_SA_1+$continuadores_SA_1;
		$total_edad_SA_1 = $anciano_SA_1+$adulto_SA_1+$jovenes_SA_1+$niños_SA_1;
		$total_sexo_SA_1 = $mujeres_SA_1 + $varones_SA_1;
		$total_categoria_S = $nuevos_S+$continuadores_S;
		$total_edad_S = $anciano_S+$adulto_S+$jovenes_S+$niños_S;
		$total_sexo_S = $mujeres_S + $varones_S;
		$total_categoria_P = $nuevos_P+$continuadores_P;
		$total_edad_P = $anciano_P+$adulto_P+$jovenes_P+$niños_P;
		$total_sexo_P = $mujeres_P + $varones_P;
		$total_categoria_R = $nuevos_R+$continuadores_R;
		$total_edad_R = $anciano_R+$adulto_R+$jovenes_R+$niños_R;
		$total_sexo_R = $mujeres_R + $varones_R;
		$total_categoria_T = $nuevos_T+$continuadores_T;
		$total_edad_T = $anciano_T+$adulto_T+$jovenes_T+$niños_T;
		$total_sexo_T = $mujeres_T + $varones_T;
		$total_categoria_E = $nuevos_E+$continuadores_E;
		$total_edad_E = $anciano_E+$adulto_E+$jovenes_E+$niños_E;
		$total_sexo_E = $mujeres_E + $varones_E;
		$total_categoria_E_1 = $nuevos_E_1+$continuadores_E_1;
		$total_edad_E_1 = $anciano_E_1+$adulto_E_1+$jovenes_E_1+$niños_E_1;
		$total_sexo_E_1 = $mujeres_E_1 + $varones_E_1;
		$total_categoria_RE = $nuevos_RE+$continuadores_RE;
		$total_edad_RE = $anciano_RE+$adulto_RE+$jovenes_RE+$niños_RE;
		$total_sexo_RE = $mujeres_RE + $varones_RE;
		$tot_nuevos = $nuevos_A_1+$nuevos_SA+$nuevos_S+$nuevos_RE+$nuevos_A+$nuevos_E+$nuevos_P+$nuevos_E_1+$nuevos_SA_1;
		$tot_continuadores = $continuadores_A_1+$continuadores_SA+$continuadores_S+$continuadores_RE+$continuadores_A+$continuadores_E+$continuadores_P+$continuadores_E_1+$continuadores_SA_1;

		$tot_ninos = $niños_A_1+$niños_SA+$niños_S+$niños_RE+$niños_A+$niños_E+$niños_P+$niños_E_1+$niños_SA_1;
		$tot_jovenes = $jovenes_A_1+$jovenes_SA+$jovenes_S+$jovenes_RE+$jovenes_A+$jovenes_E+$jovenes_P+$jovenes_E_1+$jovenes_SA_1;
		$tot_adultos = $adulto_A_1+$adulto_SA+$adulto_S+$adulto_RE+$adulto_A+$adulto_E+$adulto_P+$adulto_E_1+$adulto_SA_1;
		$tot_ancianos = $anciano_A_1+$anciano_SA+$anciano_S+$anciano_RE+$anciano_A+$anciano_E+$anciano_P+$anciano_E_1+$anciano_SA_1;
		$tot_varones = $varones_A_1+$varones_SA+$varones_S+$varones_RE+$varones_A+$varones_E+$varones_P+$varones_E_1+$varones_SA_1;
		$tot_mujeres = $mujeres_A_1+$mujeres_SA+$mujeres_S+$mujeres_RE+$mujeres_A+$mujeres_E+$mujeres_P+$mujeres_E_1+$mujeres_SA_1;

		$this->SetFont('Arial','',8);
		$this->Line(10,$y,200,$y);
		$this->SetXY(10,$y);$this->MultiCell(40,5,'ALVARADO','0','L');
		$this->SetXY(50,$y);$this->MultiCell(25,5,'Nuevos','0','L');
		$this->SetXY(75,$y);$this->MultiCell(25,5,$nuevos_A,'0','L');
		$y+=3;
		$this->SetXY(50,$y);$this->MultiCell(25,5,'Continuadores','0','L');
		$this->SetXY(75,$y);$this->MultiCell(25,5,$continuadores_A,'0','L');
		$y+=3;
		$this->SetXY(50,$y);$this->MultiCell(25,5,'Reingresantes','0','L');
		$this->SetXY(75,$y);$this->MultiCell(25,5,'0','0','L');
		$y+=3;
		$this->SetXY(50,$y);$this->MultiCell(25,5,'TOTAL','0','L');
		
		$this->SetXY(75,$y);$this->MultiCell(25,5,$total_categoria_A,'0','L');
		$y-=10;
		$this->SetXY(100,$y);$this->MultiCell(25,5,'Niños','0','L');
		$this->SetXY(125,$y);$this->MultiCell(25,5,$niños_A,'0','L');
		$y+=3;
		$this->SetXY(100,$y);$this->MultiCell(25,5,'Jovenes','0','L');
		$this->SetXY(125,$y);$this->MultiCell(25,5,$jovenes_A,'0','L');
		$y+=3;
		$this->SetXY(100,$y);$this->MultiCell(25,5,'Adultos','0','L');
		$this->SetXY(125,$y);$this->MultiCell(25,5,$adulto_A,'0','L');
		$y+=3;
		$this->SetXY(100,$y);$this->MultiCell(25,5,'Ancianos','0','L');
		$this->SetXY(125,$y);$this->MultiCell(25,5,$anciano_A,'0','L');
		$y+=3;
		$this->SetXY(100,$y);$this->MultiCell(25,5,'TOTAL','0','L');
		
		$this->SetXY(125,$y);$this->MultiCell(25,5,$total_edad_A,'0','L');
		$y-=12;
		$this->SetXY(150,$y);$this->MultiCell(25,5,'Varones','0','L');
		$this->SetXY(175,$y);$this->MultiCell(25,5,$varones_A,'0','L');
		$y+=3;
		$this->SetXY(150,$y);$this->MultiCell(25,5,'Mujeres','0','L');
		$this->SetXY(175,$y);$this->MultiCell(25,5,$mujeres_A,'0','L');
		$y+=3;
		$this->SetXY(150,$y);$this->MultiCell(25,5,'TOTAL','0','L');
		$this->SetXY(175,$y);$this->MultiCell(25,5,$total_sexo_A,'0','L');
		$this->Line(10,$y+10,200,$y+10);
		$y=$y+10;
		$this->SetFont('Arial','',8);
		$this->Line(10,$y,200,$y);
		$this->SetXY(10,$y);$this->MultiCell(40,5,'ALVARADO (1)','0','L');
		$this->SetXY(50,$y);$this->MultiCell(25,5,'Nuevos','0','L');
		$this->SetXY(75,$y);$this->MultiCell(25,5,$nuevos_A_1,'0','L');
		$y+=3;
		$this->SetXY(50,$y);$this->MultiCell(25,5,'Continuadores','0','L');
		$this->SetXY(75,$y);$this->MultiCell(25,5,$continuadores_A_1,'0','L');
		$y+=3;
		$this->SetXY(50,$y);$this->MultiCell(25,5,'Reingresantes','0','L');
		$this->SetXY(75,$y);$this->MultiCell(25,5,'0','0','L');
		$y+=3;
		$this->SetXY(50,$y);$this->MultiCell(25,5,'TOTAL','0','L');
		
		$this->SetXY(75,$y);$this->MultiCell(25,5,$total_categoria_A_1,'0','L');
		$y-=10;
		$this->SetXY(100,$y);$this->MultiCell(25,5,'Niños','0','L');
		$this->SetXY(125,$y);$this->MultiCell(25,5,$niños_A_1,'0','L');
		$y+=3;
		$this->SetXY(100,$y);$this->MultiCell(25,5,'Jovenes','0','L');
		$this->SetXY(125,$y);$this->MultiCell(25,5,$jovenes_A_1,'0','L');
		$y+=3;
		$this->SetXY(100,$y);$this->MultiCell(25,5,'Adultos','0','L');
		$this->SetXY(125,$y);$this->MultiCell(25,5,$adulto_A_1,'0','L');
		$y+=3;
		$this->SetXY(100,$y);$this->MultiCell(25,5,'Ancianos','0','L');
		$this->SetXY(125,$y);$this->MultiCell(25,5,$anciano_A_1,'0','L');
		$y+=3;
		$this->SetXY(100,$y);$this->MultiCell(25,5,'TOTAL','0','L');
		
		$this->SetXY(125,$y);$this->MultiCell(25,5,$total_edad_A_1,'0','L');
		$y-=12;
		$this->SetXY(150,$y);$this->MultiCell(25,5,'Varones','0','L');
		$this->SetXY(175,$y);$this->MultiCell(25,5,$varones_A_1,'0','L');
		$y+=3;
		$this->SetXY(150,$y);$this->MultiCell(25,5,'Mujeres','0','L');
		$this->SetXY(175,$y);$this->MultiCell(25,5,$mujeres_A_1,'0','L');
		$y+=3;
		$this->SetXY(150,$y);$this->MultiCell(25,5,'TOTAL','0','L');
		$this->SetXY(175,$y);$this->MultiCell(25,5,$total_sexo_A_1,'0','L');
		$this->Line(10,$y+10,200,$y+10);
		$y=$y+10;
		$this->SetFont('Arial','',8);
		$this->Line(10,$y,200,$y);
		$this->SetXY(10,$y);$this->MultiCell(40,5,'SANCHEZ','0','L');
		$this->SetXY(50,$y);$this->MultiCell(25,5,'Nuevos','0','L');
		$this->SetXY(75,$y);$this->MultiCell(25,5,$nuevos_SA,'0','L');
		$y+=3;
		$this->SetXY(50,$y);$this->MultiCell(25,5,'Continuadores','0','L');
		$this->SetXY(75,$y);$this->MultiCell(25,5,$continuadores_SA,'0','L');
		$y+=3;
		$this->SetXY(50,$y);$this->MultiCell(25,5,'Reingresantes','0','L');
		$this->SetXY(75,$y);$this->MultiCell(25,5,'0','0','L');
		$y+=3;
		$this->SetXY(50,$y);$this->MultiCell(25,5,'TOTAL','0','L');
		
		$this->SetXY(75,$y);$this->MultiCell(25,5,$total_categoria_SA,'0','L');
		$y-=10;
		$this->SetXY(100,$y);$this->MultiCell(25,5,'Niños','0','L');
		$this->SetXY(125,$y);$this->MultiCell(25,5,$niños_SA,'0','L');
		$y+=3;
		$this->SetXY(100,$y);$this->MultiCell(25,5,'Jovenes','0','L');
		$this->SetXY(125,$y);$this->MultiCell(25,5,$jovenes_SA,'0','L');
		$y+=3;
		$this->SetXY(100,$y);$this->MultiCell(25,5,'Adultos','0','L');
		$this->SetXY(125,$y);$this->MultiCell(25,5,$adulto_SA,'0','L');
		$y+=3;
		$this->SetXY(100,$y);$this->MultiCell(25,5,'Ancianos','0','L');
		$this->SetXY(125,$y);$this->MultiCell(25,5,$anciano_SA,'0','L');
		$y+=3;
		$this->SetXY(100,$y);$this->MultiCell(25,5,'TOTAL','0','L');
		
		$this->SetXY(125,$y);$this->MultiCell(25,5,$total_edad_SA,'0','L');
		$y-=12;
		$this->SetXY(150,$y);$this->MultiCell(25,5,'Varones','0','L');
		$this->SetXY(175,$y);$this->MultiCell(25,5,$varones_SA,'0','L');
		$y+=3;
		$this->SetXY(150,$y);$this->MultiCell(25,5,'Mujeres','0','L');
		$this->SetXY(175,$y);$this->MultiCell(25,5,$mujeres_SA,'0','L');
		$y+=3;
		$this->SetXY(150,$y);$this->MultiCell(25,5,'TOTAL','0','L');
		$this->SetXY(175,$y);$this->MultiCell(25,5,$total_sexo_SA,'0','L');
		$this->Line(10,$y+10,200,$y+10);
		$y=$y+10;
		$this->SetFont('Arial','',8);
		$this->Line(10,$y,200,$y);
		$this->SetXY(10,$y);$this->MultiCell(40,5,'SANCHEZ (1) ','0','L');
		$this->SetXY(50,$y);$this->MultiCell(25,5,'Nuevos','0','L');
		$this->SetXY(75,$y);$this->MultiCell(25,5,$nuevos_SA_1,'0','L');
		$y+=3;
		$this->SetXY(50,$y);$this->MultiCell(25,5,'Continuadores','0','L');
		$this->SetXY(75,$y);$this->MultiCell(25,5,$continuadores_SA_1,'0','L');
		$y+=3;
		$this->SetXY(50,$y);$this->MultiCell(25,5,'Reingresantes','0','L');
		$this->SetXY(75,$y);$this->MultiCell(25,5,'0','0','L');
		$y+=3;
		$this->SetXY(50,$y);$this->MultiCell(25,5,'TOTAL','0','L');
		
		$this->SetXY(75,$y);$this->MultiCell(25,5,$total_categoria_SA_1,'0','L');
		$y-=10;
		$this->SetXY(100,$y);$this->MultiCell(25,5,'Niños','0','L');
		$this->SetXY(125,$y);$this->MultiCell(25,5,$niños_SA_1,'0','L');
		$y+=3;
		$this->SetXY(100,$y);$this->MultiCell(25,5,'Jovenes','0','L');
		$this->SetXY(125,$y);$this->MultiCell(25,5,$jovenes_SA_1,'0','L');
		$y+=3;
		$this->SetXY(100,$y);$this->MultiCell(25,5,'Adultos','0','L');
		$this->SetXY(125,$y);$this->MultiCell(25,5,$adulto_SA_1,'0','L');
		$y+=3;
		$this->SetXY(100,$y);$this->MultiCell(25,5,'Ancianos','0','L');
		$this->SetXY(125,$y);$this->MultiCell(25,5,$anciano_SA_1,'0','L');
		$y+=3;
		$this->SetXY(100,$y);$this->MultiCell(25,5,'TOTAL','0','L');
		
		$this->SetXY(125,$y);$this->MultiCell(25,5,$total_edad_SA_1,'0','L');
		$y-=12;
		$this->SetXY(150,$y);$this->MultiCell(25,5,'Varones','0','L');
		$this->SetXY(175,$y);$this->MultiCell(25,5,$varones_SA_1,'0','L');
		$y+=3;
		$this->SetXY(150,$y);$this->MultiCell(25,5,'Mujeres','0','L');
		$this->SetXY(175,$y);$this->MultiCell(25,5,$mujeres_SA_1,'0','L');
		$y+=3;
		$this->SetXY(150,$y);$this->MultiCell(25,5,'TOTAL','0','L');
		$this->SetXY(175,$y);$this->MultiCell(25,5,$total_sexo_SA_1,'0','L');
		$this->Line(10,$y+10,200,$y+10);
		$y=$y+10;
		$this->SetFont('Arial','',8);
		$this->Line(10,$y,200,$y);
		$this->SetXY(10,$y);$this->MultiCell(40,5,'GONZALES','0','L');
		$this->SetXY(50,$y);$this->MultiCell(25,5,'Nuevos','0','L');
		$this->SetXY(75,$y);$this->MultiCell(25,5,$nuevos_S,'0','L');
		$y+=3;
		$this->SetXY(50,$y);$this->MultiCell(25,5,'Continuadores','0','L');
		$this->SetXY(75,$y);$this->MultiCell(25,5,$continuadores_S,'0','L');
		$y+=3;
		$this->SetXY(50,$y);$this->MultiCell(25,5,'Reingresantes','0','L');
		$this->SetXY(75,$y);$this->MultiCell(25,5,'0','0','L');
		$y+=3;
		$this->SetXY(50,$y);$this->MultiCell(25,5,'TOTAL','0','L');
		
		$this->SetXY(75,$y);$this->MultiCell(25,5,$total_categoria_S,'0','L');
		$y-=10;
		$this->SetXY(100,$y);$this->MultiCell(25,5,'Niños','0','L');
		$this->SetXY(125,$y);$this->MultiCell(25,5,$niños_S,'0','L');
		$y+=3;
		$this->SetXY(100,$y);$this->MultiCell(25,5,'Jovenes','0','L');
		$this->SetXY(125,$y);$this->MultiCell(25,5,$jovenes_S,'0','L');
		$y+=3;
		$this->SetXY(100,$y);$this->MultiCell(25,5,'Adultos','0','L');
		$this->SetXY(125,$y);$this->MultiCell(25,5,$adulto_S,'0','L');
		$y+=3;
		$this->SetXY(100,$y);$this->MultiCell(25,5,'Ancianos','0','L');
		$this->SetXY(125,$y);$this->MultiCell(25,5,$anciano_S,'0','L');
		$y+=3;
		$this->SetXY(100,$y);$this->MultiCell(25,5,'TOTAL','0','L');
		
		$this->SetXY(125,$y);$this->MultiCell(25,5,$total_edad_S,'0','L');
		$y-=12;
		$this->SetXY(150,$y);$this->MultiCell(25,5,'Varones','0','L');
		$this->SetXY(175,$y);$this->MultiCell(25,5,$varones_S,'0','L');
		$y+=3;
		$this->SetXY(150,$y);$this->MultiCell(25,5,'Mujeres','0','L');
		$this->SetXY(175,$y);$this->MultiCell(25,5,$mujeres_S,'0','L');
		$y+=3;
		$this->SetXY(150,$y);$this->MultiCell(25,5,'TOTAL','0','L');
		$this->SetXY(175,$y);$this->MultiCell(25,5,$total_sexo_S,'0','L');
		$this->Line(10,$y+10,200,$y+10);
		$y=$y+10;
		$this->SetFont('Arial','',8);
		$this->Line(10,$y,200,$y);
		$this->SetXY(10,$y);$this->MultiCell(40,5,'PAREDES','0','L');
		$this->SetXY(50,$y);$this->MultiCell(25,5,'Nuevos','0','L');
		$this->SetXY(75,$y);$this->MultiCell(25,5,$nuevos_P,'0','L');
		$y+=3;
		$this->SetXY(50,$y);$this->MultiCell(25,5,'Continuadores','0','L');
		$this->SetXY(75,$y);$this->MultiCell(25,5,$continuadores_P,'0','L');
		$y+=3;
		$this->SetXY(50,$y);$this->MultiCell(25,5,'Reingresantes','0','L');
		$this->SetXY(75,$y);$this->MultiCell(25,5,'0','0','L');
		$y+=3;
		$this->SetXY(50,$y);$this->MultiCell(25,5,'TOTAL','0','L');
		
		$this->SetXY(75,$y);$this->MultiCell(25,5,$total_categoria_P,'0','L');
		$y-=10;
		$this->SetXY(100,$y);$this->MultiCell(25,5,'Niños','0','L');
		$this->SetXY(125,$y);$this->MultiCell(25,5,$niños_P,'0','L');
		$y+=3;
		$this->SetXY(100,$y);$this->MultiCell(25,5,'Jovenes','0','L');
		$this->SetXY(125,$y);$this->MultiCell(25,5,$jovenes_P,'0','L');
		$y+=3;
		$this->SetXY(100,$y);$this->MultiCell(25,5,'Adultos','0','L');
		$this->SetXY(125,$y);$this->MultiCell(25,5,$adulto_P,'0','L');
		$y+=3;
		$this->SetXY(100,$y);$this->MultiCell(25,5,'Ancianos','0','L');
		$this->SetXY(125,$y);$this->MultiCell(25,5,$anciano_P,'0','L');
		$y+=3;
		$this->SetXY(100,$y);$this->MultiCell(25,5,'TOTAL','0','L');
		
		$this->SetXY(125,$y);$this->MultiCell(25,5,$total_edad_P,'0','L');
		$y-=12;
		$this->SetXY(150,$y);$this->MultiCell(25,5,'Varones','0','L');
		$this->SetXY(175,$y);$this->MultiCell(25,5,$varones_P,'0','L');
		$y+=3;
		$this->SetXY(150,$y);$this->MultiCell(25,5,'Mujeres','0','L');
		$this->SetXY(175,$y);$this->MultiCell(25,5,$mujeres_P,'0','L');
		$y+=3;
		$this->SetXY(150,$y);$this->MultiCell(25,5,'TOTAL','0','L');
		$this->SetXY(175,$y);$this->MultiCell(25,5,$total_sexo_P,'0','L');
		$this->Line(10,$y+10,200,$y+10);
		$y=$y+10;
		$this->SetFont('Arial','',8);
		$this->Line(10,$y,200,$y);
		$this->SetXY(10,$y);$this->MultiCell(40,5,'RAMIREZ','0','L');
		$this->SetXY(50,$y);$this->MultiCell(25,5,'Nuevos','0','L');
		$this->SetXY(75,$y);$this->MultiCell(25,5,$nuevos_R,'0','L');
		$y+=3;
		$this->SetXY(50,$y);$this->MultiCell(25,5,'Continuadores','0','L');
		$this->SetXY(75,$y);$this->MultiCell(25,5,$continuadores_R,'0','L');
		$y+=3;
		$this->SetXY(50,$y);$this->MultiCell(25,5,'Reingresantes','0','L');
		$this->SetXY(75,$y);$this->MultiCell(25,5,'0','0','L');
		$y+=3;
		$this->SetXY(50,$y);$this->MultiCell(25,5,'TOTAL','0','L');
		
		$this->SetXY(75,$y);$this->MultiCell(25,5,$total_categoria_R,'0','L');
		$y-=10;
		$this->SetXY(100,$y);$this->MultiCell(25,5,'Niños','0','L');
		$this->SetXY(125,$y);$this->MultiCell(25,5,$niños_R,'0','L');
		$y+=3;
		$this->SetXY(100,$y);$this->MultiCell(25,5,'Jovenes','0','L');
		$this->SetXY(125,$y);$this->MultiCell(25,5,$jovenes_R,'0','L');
		$y+=3;
		$this->SetXY(100,$y);$this->MultiCell(25,5,'Adultos','0','L');
		$this->SetXY(125,$y);$this->MultiCell(25,5,$adulto_R,'0','L');
		$y+=3;
		$this->SetXY(100,$y);$this->MultiCell(25,5,'Ancianos','0','L');
		$this->SetXY(125,$y);$this->MultiCell(25,5,$anciano_R,'0','L');
		$y+=3;
		$this->SetXY(100,$y);$this->MultiCell(25,5,'TOTAL','0','L');
		
		$this->SetXY(125,$y);$this->MultiCell(25,5,$total_edad_R,'0','L');
		$y-=12;
		$this->SetXY(150,$y);$this->MultiCell(25,5,'Varones','0','L');
		$this->SetXY(175,$y);$this->MultiCell(25,5,$varones_R,'0','L');
		$y+=3;
		$this->SetXY(150,$y);$this->MultiCell(25,5,'Mujeres','0','L');
		$this->SetXY(175,$y);$this->MultiCell(25,5,$mujeres_R,'0','L');
		$y+=3;
		$this->SetXY(150,$y);$this->MultiCell(25,5,'TOTAL','0','L');
		$this->SetXY(175,$y);$this->MultiCell(25,5,$total_sexo_R,'0','L');
		$this->Line(10,$y+10,200,$y+10);
		$y=$y+10;
		$this->SetFont('Arial','',8);
		$this->Line(10,$y,200,$y);
		$this->SetXY(10,$y);$this->MultiCell(40,5,'TICONA','0','L');
		$this->SetXY(50,$y);$this->MultiCell(25,5,'Nuevos','0','L');
		$this->SetXY(75,$y);$this->MultiCell(25,5,$nuevos_T,'0','L');
		$y+=3;
		$this->SetXY(50,$y);$this->MultiCell(25,5,'Continuadores','0','L');
		$this->SetXY(75,$y);$this->MultiCell(25,5,$continuadores_T,'0','L');
		$y+=3;
		$this->SetXY(50,$y);$this->MultiCell(25,5,'Reingresantes','0','L');
		$this->SetXY(75,$y);$this->MultiCell(25,5,'0','0','L');
		$y+=3;
		$this->SetXY(50,$y);$this->MultiCell(25,5,'TOTAL','0','L');
		
		$this->SetXY(75,$y);$this->MultiCell(25,5,$total_categoria_T,'0','L');
		$y-=10;
		$this->SetXY(100,$y);$this->MultiCell(25,5,'Niños','0','L');
		$this->SetXY(125,$y);$this->MultiCell(25,5,$niños_T,'0','L');
		$y+=3;
		$this->SetXY(100,$y);$this->MultiCell(25,5,'Jovenes','0','L');
		$this->SetXY(125,$y);$this->MultiCell(25,5,$jovenes_T,'0','L');
		$y+=3;
		$this->SetXY(100,$y);$this->MultiCell(25,5,'Adultos','0','L');
		$this->SetXY(125,$y);$this->MultiCell(25,5,$adulto_T,'0','L');
		$y+=3;
		$this->SetXY(100,$y);$this->MultiCell(25,5,'Ancianos','0','L');
		$this->SetXY(125,$y);$this->MultiCell(25,5,$anciano_T,'0','L');
		$y+=3;
		$this->SetXY(100,$y);$this->MultiCell(25,5,'TOTAL','0','L');
		
		$this->SetXY(125,$y);$this->MultiCell(25,5,$total_edad_T,'0','L');
		$y-=12;
		$this->SetXY(150,$y);$this->MultiCell(25,5,'Varones','0','L');
		$this->SetXY(175,$y);$this->MultiCell(25,5,$varones_T,'0','L');
		$y+=3;
		$this->SetXY(150,$y);$this->MultiCell(25,5,'Mujeres','0','L');
		$this->SetXY(175,$y);$this->MultiCell(25,5,$mujeres_T,'0','L');
		$y+=3;
		$this->SetXY(150,$y);$this->MultiCell(25,5,'TOTAL','0','L');
		$this->SetXY(175,$y);$this->MultiCell(25,5,$total_sexo_T,'0','L');
		$this->Line(10,$y+10,200,$y+10);
		$y=$y+10;
		$this->SetFont('Arial','',8);
		$this->Line(10,$y,200,$y);
		$this->SetXY(10,$y);$this->MultiCell(40,5,'ESQUIVIAS','0','L');
		$this->SetXY(50,$y);$this->MultiCell(25,5,'Nuevos','0','L');
		$this->SetXY(75,$y);$this->MultiCell(25,5,$nuevos_E,'0','L');
		$y+=3;
		$this->SetXY(50,$y);$this->MultiCell(25,5,'Continuadores','0','L');
		$this->SetXY(75,$y);$this->MultiCell(25,5,$continuadores_E,'0','L');
		$y+=3;
		$this->SetXY(50,$y);$this->MultiCell(25,5,'Reingresantes','0','L');
		$this->SetXY(75,$y);$this->MultiCell(25,5,'0','0','L');
		$y+=3;
		$this->SetXY(50,$y);$this->MultiCell(25,5,'TOTAL','0','L');
		
		$this->SetXY(75,$y);$this->MultiCell(25,5,$total_categoria_E,'0','L');
		$y-=10;
		$this->SetXY(100,$y);$this->MultiCell(25,5,'Niños','0','L');
		$this->SetXY(125,$y);$this->MultiCell(25,5,$niños_E,'0','L');
		$y+=3;
		$this->SetXY(100,$y);$this->MultiCell(25,5,'Jovenes','0','L');
		$this->SetXY(125,$y);$this->MultiCell(25,5,$jovenes_E,'0','L');
		$y+=3;
		$this->SetXY(100,$y);$this->MultiCell(25,5,'Adultos','0','L');
		$this->SetXY(125,$y);$this->MultiCell(25,5,$adulto_E,'0','L');
		$y+=3;
		$this->SetXY(100,$y);$this->MultiCell(25,5,'Ancianos','0','L');
		$this->SetXY(125,$y);$this->MultiCell(25,5,$anciano_E,'0','L');
		$y+=3;
		$this->SetXY(100,$y);$this->MultiCell(25,5,'TOTAL','0','L');
		
		$this->SetXY(125,$y);$this->MultiCell(25,5,$total_edad_E,'0','L');
		$y-=12;
		$this->SetXY(150,$y);$this->MultiCell(25,5,'Varones','0','L');
		$this->SetXY(175,$y);$this->MultiCell(25,5,$varones_E,'0','L');
		$y+=3;
		$this->SetXY(150,$y);$this->MultiCell(25,5,'Mujeres','0','L');
		$this->SetXY(175,$y);$this->MultiCell(25,5,$mujeres_E,'0','L');
		$y+=3;
		$this->SetXY(150,$y);$this->MultiCell(25,5,'TOTAL','0','L');
		$this->SetXY(175,$y);$this->MultiCell(25,5,$total_sexo_E,'0','L');
		$this->Line(10,$y+10,200,$y+10);
		$y=$y+10;
		$this->SetFont('Arial','',8);
		$this->Line(10,$y,200,$y);
		$this->SetXY(10,$y);$this->MultiCell(40,5,'ESQUIVIAS (1)','0','L');
		$this->SetXY(50,$y);$this->MultiCell(25,5,'Nuevos','0','L');
		$this->SetXY(75,$y);$this->MultiCell(25,5,$nuevos_E_1,'0','L');
		$y+=3;
		$this->SetXY(50,$y);$this->MultiCell(25,5,'Continuadores','0','L');
		$this->SetXY(75,$y);$this->MultiCell(25,5,$continuadores_E_1,'0','L');
		$y+=3;
		$this->SetXY(50,$y);$this->MultiCell(25,5,'Reingresantes','0','L');
		$this->SetXY(75,$y);$this->MultiCell(25,5,'0','0','L');
		$y+=3;
		$this->SetXY(50,$y);$this->MultiCell(25,5,'TOTAL','0','L');
		
		$this->SetXY(75,$y);$this->MultiCell(25,5,$total_categoria_E_1,'0','L');
		$y-=10;
		$this->SetXY(100,$y);$this->MultiCell(25,5,'Niños','0','L');
		$this->SetXY(125,$y);$this->MultiCell(25,5,$niños_E_1,'0','L');
		$y+=3;
		$this->SetXY(100,$y);$this->MultiCell(25,5,'Jovenes','0','L');
		$this->SetXY(125,$y);$this->MultiCell(25,5,$jovenes_E_1,'0','L');
		$y+=3;
		$this->SetXY(100,$y);$this->MultiCell(25,5,'Adultos','0','L');
		$this->SetXY(125,$y);$this->MultiCell(25,5,$adulto_E_1,'0','L');
		$y+=3;
		$this->SetXY(100,$y);$this->MultiCell(25,5,'Ancianos','0','L');
		$this->SetXY(125,$y);$this->MultiCell(25,5,$anciano_E_1,'0','L');
		$y+=3;
		$this->SetXY(100,$y);$this->MultiCell(25,5,'TOTAL','0','L');
		
		$this->SetXY(125,$y);$this->MultiCell(25,5,$total_edad_E_1,'0','L');
		$y-=12;
		$this->SetXY(150,$y);$this->MultiCell(25,5,'Varones','0','L');
		$this->SetXY(175,$y);$this->MultiCell(25,5,$varones_E_1,'0','L');
		$y+=3;
		$this->SetXY(150,$y);$this->MultiCell(25,5,'Mujeres','0','L');
		$this->SetXY(175,$y);$this->MultiCell(25,5,$mujeres_E_1,'0','L');
		$y+=3;
		$this->SetXY(150,$y);$this->MultiCell(25,5,'TOTAL','0','L');
		$this->SetXY(175,$y);$this->MultiCell(25,5,$total_sexo_E_1,'0','L');
		$this->Line(10,$y+10,200,$y+10);
		$y=$y+10;
		$this->SetFont('Arial','',8);
		$this->Line(10,$y,200,$y);
		$this->SetXY(10,$y);$this->MultiCell(40,5,'REVILLA','0','L');
		$this->SetXY(50,$y);$this->MultiCell(25,5,'Nuevos','0','L');
		$this->SetXY(75,$y);$this->MultiCell(25,5,$nuevos_RE,'0','L');
		$y+=3;
		$this->SetXY(50,$y);$this->MultiCell(25,5,'Continuadores','0','L');
		$this->SetXY(75,$y);$this->MultiCell(25,5,$continuadores_RE,'0','L');
		$y+=3;
		$this->SetXY(50,$y);$this->MultiCell(25,5,'Reingresantes','0','L');
		$this->SetXY(75,$y);$this->MultiCell(25,5,'0','0','L');
		$y+=3;
		$this->SetXY(50,$y);$this->MultiCell(25,5,'TOTAL','0','L');
		
		$this->SetXY(75,$y);$this->MultiCell(25,5,$total_categoria_RE,'0','L');
		$y-=10;
		$this->SetXY(100,$y);$this->MultiCell(25,5,'Niños','0','L');
		$this->SetXY(125,$y);$this->MultiCell(25,5,$niños_RE,'0','L');
		$y+=3;
		$this->SetXY(100,$y);$this->MultiCell(25,5,'Jovenes','0','L');
		$this->SetXY(125,$y);$this->MultiCell(25,5,$jovenes_RE,'0','L');
		$y+=3;
		$this->SetXY(100,$y);$this->MultiCell(25,5,'Adultos','0','L');
		$this->SetXY(125,$y);$this->MultiCell(25,5,$adulto_RE,'0','L');
		$y+=3;
		$this->SetXY(100,$y);$this->MultiCell(25,5,'Ancianos','0','L');
		$this->SetXY(125,$y);$this->MultiCell(25,5,$anciano_RE,'0','L');
		$y+=3;
		$this->SetXY(100,$y);$this->MultiCell(25,5,'TOTAL','0','L');
		
		$this->SetXY(125,$y);$this->MultiCell(25,5,$total_edad_RE,'0','L');
		$y-=12;
		$this->SetXY(150,$y);$this->MultiCell(25,5,'Varones','0','L');
		$this->SetXY(175,$y);$this->MultiCell(25,5,$varones_RE,'0','L');
		$y+=3;
		$this->SetXY(150,$y);$this->MultiCell(25,5,'Mujeres','0','L');
		$this->SetXY(175,$y);$this->MultiCell(25,5,$mujeres_RE,'0','L');
		$y+=3;
		$this->SetXY(150,$y);$this->MultiCell(25,5,'TOTAL','0','L');
		$this->SetXY(175,$y);$this->MultiCell(25,5,$total_sexo_RE,'0','L');
		$this->Line(10,$y+10,200,$y+10);

		$this->Line(10,$y_ini+35,10,$y+10);
		$this->Line(50,$y_ini+35,50,$y+10);
		$this->Line(100,$y_ini+35,100,$y+10);
		$this->Line(150,$y_ini+35,150,$y+10);
		$this->Line(200,$y_ini+35,200,$y+10);	
		$this->Line(10,$y+10,200,$y+10);

		$y+=15;
		$this->SetXY(10,$y);$this->MultiCell(190,5,'TOTAL DE ATENCIONES DE TODOS LOS MEDICOS','0','L');
		$y+=5;
		$this->SetFont('Arial','',7);
		$this->SetXY(10,$y);$this->MultiCell(25,5,'Nuevos','0','L');
		$this->SetFont('Arial','B',7);
		$this->SetXY(27.5,$y);$this->MultiCell(25,5,$tot_nuevos,'0','L');
		$y+=5;
		$this->SetFont('Arial','',7);
		$this->SetXY(10,$y);$this->MultiCell(25,5,'Continuadores','0','L');
		$this->SetFont('Arial','B',7);
		$this->SetXY(27.5,$y);$this->MultiCell(25,5,$tot_continuadores,'0','L');
		$y+=5;
		$this->SetFont('Arial','',7);
		$this->SetXY(10,$y);$this->MultiCell(25,5,'Reingresantes','0','L');
		$this->SetFont('Arial','B',7);
		$this->SetXY(27.5,$y);$this->MultiCell(25,5,'0','0','L');
		$y+=5;
		$this->SetFont('Arial','',7);
		$this->SetXY(10,$y);$this->MultiCell(25,5,'TOTAL','0','L');
		$this->SetFont('Arial','B',7);
		$this->SetXY(27.5,$y);$this->MultiCell(25,5,$tot_nuevos+$tot_continuadores,'0','L');
		$y-=15;
		$this->SetFont('Arial','',7);
		$this->SetXY(40,$y);$this->MultiCell(25,5,'Niños','0','L');
		$this->SetFont('Arial','B',7);
		$this->SetXY(52.5,$y);$this->MultiCell(25,5,$tot_ninos,'0','L');
		$y+=5;
		$this->SetFont('Arial','',7);
		$this->SetXY(40,$y);$this->MultiCell(25,5,'Jovenes','0','L');
		$this->SetFont('Arial','B',7);
		$this->SetXY(52.5,$y);$this->MultiCell(25,5,$tot_jovenes,'0','L');
		$y+=5;
		$this->SetFont('Arial','',7);
		$this->SetXY(40,$y);$this->MultiCell(25,5,'Adultos','0','L');
		$this->SetFont('Arial','B',7);
		$this->SetXY(52.5,$y);$this->MultiCell(25,5,$tot_adultos,'0','L');
		$y+=5;
		$this->SetFont('Arial','',7);
		$this->SetXY(40,$y);$this->MultiCell(25,5,'Ancianos','0','L');
		$this->SetFont('Arial','B',7);
		$this->SetXY(52.5,$y);$this->MultiCell(25,5,$tot_ancianos,'0','L');
		$y+=5;
		$this->SetFont('Arial','',7);
		$this->SetXY(40,$y);$this->MultiCell(25,5,'TOTAL','0','L');
		$this->SetFont('Arial','B',7);
		$this->SetXY(52.5,$y);$this->MultiCell(25,5,$tot_ninos+$tot_jovenes+$tot_adultos+$tot_ancianos,'0','L');
		$y_final = $y;
		$y-=20;
		$this->SetFont('Arial','',7);
		$this->SetXY(65,$y);$this->MultiCell(25,5,'Varones','0','L');
		$this->SetFont('Arial','B',7);
		$this->SetXY(80,$y);$this->MultiCell(25,5,$tot_varones,'0','L');
		$y+=5;
		$this->SetFont('Arial','',7);
		$this->SetXY(65,$y);$this->MultiCell(25,5,'Mujeres','0','L');
		$this->SetFont('Arial','B',7);
		$this->SetXY(80.5,$y);$this->MultiCell(25,5,$tot_mujeres,'0','L');
		$y+=5;
		$this->SetFont('Arial','',7);
		$this->SetXY(65,$y);$this->MultiCell(25,5,'TOTAL','0','L');
		$this->SetFont('Arial','B',7);
		$this->SetXY(80,$y);$this->MultiCell(25,5,$tot_varones+$tot_mujeres,'0','L');
		$this->SetFont('Arial','',7);
		$y+=15;
		$y = $y_final;
		$y-=20;
		$this->SetXY(90,$y);$this->MultiCell(50,5,'Inyectables: '.$params['inyectables'],'0','L');
		$y+=5;
		$this->SetFont('Arial','U',10);
		$this->SetXY(90,$y);$this->MultiCell(50,5,'Medicamentos de Deposito','0','L');
		$this->SetXY(150,$y);$this->MultiCell(50,5,'Aplicaciones','0','L');
		$this->SetXY(190,$y);$this->MultiCell(50,5,'Dosis','0','L');
		$y+=5;
		$this->SetXY(90,$y);$this->MultiCell(50,5,'Haldol decanoas x 50mg','0','L');
		$this->SetXY(150,$y);$this->MultiCell(50,5,$params['haldol_aplic'],'0','L');
		$this->SetXY(190,$y);$this->MultiCell(50,5,$params['haldol_dosis'],'0','L');
	}
}

$pdf=new repo('P','mm','A4');
$pdf->setParams($params);
$pdf->AliasNbPages();
$pdf->SetMargins(10,10,0); 
$pdf->SetTitle("reporte");
$pdf->SetAutoPageBreak(false,0); 
$pdf->Open();
$pdf->AddPage();
$pdf->Publicar($diario, $params);
$pdf->SetLeftMargin(25);
//header('Content-Type: application/pdf; charset=utf-8');
$pdf->Output();

?>