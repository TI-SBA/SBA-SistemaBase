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
		$this->SetFont('Arial','',9);
		$this->SetXY(10,15);$this->MultiCell(60,5,"Centro de Salud Mental",'0','C');
		$this->SetXY(10,20);$this->MultiCell(60,5,"'Moises Heresi'",'0','C');
		$this->SetFont('Arial','B',16);
		$this->SetXY(5,30);$this->MultiCell(200,5,	"FICHA DE REPORTE MENSUAL DE SERVICIOS",'0','C');
		
	}	
	function getAge($birthday) {
		  $birth = $birthday;
		  $now = strtotime('now');
		  $age = ($now - $birth) / 31536000;
		  return floor($age);
	}
 	
	function Publicar($diario){
		$a = 0;
		$b = 0;
		$c = 0;
		$d = 0;
		$e = 0;
		$p = 0;
		$pp = 0;
		$mujeres = 0;
		$varones = 0;
		$indigente = 0;
		$pagante = 0;
		$edad1M = 0;
		$edad1F = 0;
		$edad2M = 0;
		$edad2F = 0;
		$edad3M = 0;
		$edad3F = 0;
		$edad4M = 0;
		$edad4F = 0;
		$edad5M = 0;
		$edad5F = 0;
		$edad6M = 0;
		$edad6F = 0;
		$edad7M = 0;
		$edad7F = 0;
		$edad8M = 0;
		$edad8F = 0;
		$total_consultas = 0;
		$x=5;
		$y=25;
		$y_ini = $y;
		$page_b = 275;
		$y=$y+12;
		$this->SetFont('Arial','B',8);
		//CABECERAS
		$yini = $y;
			//$this->SetXY(1,$y);$this->MultiCell(5,9," ",'1','C');
			$this->SetXY(1,$y);$this->MultiCell(35,9,"Descripcion del Servicio",'1','C');
			$this->SetXY(36,46);$this->MultiCell(36,45,"",'1','C'); 
			$this->SetXY(36,$y);$this->MultiCell(36,9,"Numero de Beneficiarios",'1','C');
			$this->SetXY(72,55);$this->MultiCell(13,45,"",'1','C');
			$this->SetXY(72,$y);$this->MultiCell(28,9,"Categoria Social ",'1','C');		
			$this->SetXY(100,55);$this->MultiCell(8,45,"",'1','C');
			$this->SetXY(100,$y);$this->MultiCell(16,9,"Sexo",'1','C');	
			$this->SetXY(116,$y);$this->MultiCell(92,9,"Edades",'1','C');
			$this->SetFont('Arial','B',9);
			$this->SetXY(1,46);$this->MultiCell(5,5,"INDICADOR",'1','C');
			$this->SetFont('Arial','B',8);
			$this->SetXY(1,91);$this->MultiCell(35,9,"Consulta Externa",'1','C');
			$this->SetXY(72,46);$this->MultiCell(13,9,"Pagante",'1','C');		
			$this->SetXY(85,46);$this->MultiCell(15,9,"Indigente",'1','C');		
			$this->SetXY(100,46);$this->MultiCell(8,9,"M",'1','C');		
			$this->SetXY(108,46);$this->MultiCell(8,9,"F",'1','C');
			$this->SetXY(116,55);$this->MultiCell(92,45,"",'1','C');
			$this->SetFont('Arial','B',8);		
			$this->SetXY(116,46);$this->MultiCell(11,9,"0-5",'1','C');
			$this->SetXY(116,55);$this->MultiCell(11,36,"",'1','C');
			$this->SetXY(127,46);$this->MultiCell(11,9,"6-10",'1','C');
			$this->SetXY(127,55);$this->MultiCell(11,36,"",'1','C');
			$this->SetFont('Arial','B',7);		
			$this->SetXY(138,46);$this->MultiCell(11,9,"11-15",'1','C');
			$this->SetXY(138,55);$this->MultiCell(11,36,"",'1','C');
			
						$this->SetFont('Arial','B',8);		
			$this->SetXY(149,46);$this->MultiCell(11,9,"16-20",'1','C');
			$this->SetXY(149,55);$this->MultiCell(11,36,"",'1','C');
			$this->SetXY(160,46);$this->MultiCell(12,9,"21-30",'1','C');
			$this->SetXY(160,55);$this->MultiCell(12,36,"",'1','C');
			$this->SetXY(172,46);$this->MultiCell(12,9,"31-50",'1','C');
			$this->SetXY(172,55);$this->MultiCell(12,36,"",'1','C');
			$this->SetXY(184,46);$this->MultiCell(12,9,"51-60",'1','C');
			$this->SetXY(184,55);$this->MultiCell(12,36,"",'1','C');
			$this->SetXY(196,46);$this->MultiCell(12,9,"61-91",'1','C');
			$this->SetXY(196,55);$this->MultiCell(12,36,"",'1','C');
			//******DIVISION POR SEXO********\\\\
			$this->SetXY(116,82);$this->MultiCell(5,9,"M",'1','C');
			$this->SetXY(121,82);$this->MultiCell(6,9,"F",'1','C');
			$this->SetXY(127,82);$this->MultiCell(5,9,"M",'1','C');
			$this->SetXY(132,82);$this->MultiCell(6,9,"F",'1','C');
			$this->SetXY(138,82);$this->MultiCell(5,9,"M",'1','C');
			$this->SetXY(143,82);$this->MultiCell(6,9,"F",'1','C');
			$this->SetXY(149,82);$this->MultiCell(5,9,"M",'1','C');
			$this->SetXY(154,82);$this->MultiCell(6,9,"F",'1','C');
			$this->SetXY(160,82);$this->MultiCell(6,9,"M",'1','C');
			$this->SetXY(166,82);$this->MultiCell(6,9,"F",'1','C');
			$this->SetXY(172,82);$this->MultiCell(6,9,"M",'1','C');
			$this->SetXY(178,82);$this->MultiCell(6,9,"F",'1','C');
			$this->SetXY(184,82);$this->MultiCell(6,9,"M",'1','C');
			$this->SetXY(190,82);$this->MultiCell(6,9,"F",'1','C');
			$this->SetXY(196,82);$this->MultiCell(6,9,"M",'1','C');
			$this->SetXY(202,82);$this->MultiCell(6,9,"F",'1','C');



			$this->SetFont('Arial','',8);
			
			//$this->Line(103, 0	 , 10, 50);

		
		
		for($i = 0;$i<count($diario);$i++){
			//print_r($diario[$i]['medico']);
			
			$total_consultas+=count($diario[$i]['consulta']);
			
			for($j = 0;$j<count($diario[$i]['consulta']);$j++){

				if($diario[$i]['consulta']['esta'] == '8'){
					print_r($diario[$i]['consulta'][$j]['his_cli']);
					echo("/");
					//print_r($diario[$i]['consulta']['paciente']['paciente']['appat']);
				}

				if(isset($diario[$i]["consulta"][$j]['paciente']['fecha_na'])){
					$fechaarte = $diario[$i]['fech']->sec;

					if(isset($diario[$i]["consulta"][$j]['paciente']['fecha_na']['sec'])){
						$fecha_nacimiento = $diario[$i]["consulta"][$j]['paciente']['fecha_na']['sec'];
					}else{
						$fecha_nacimiento = $diario[$i]["consulta"][$j]['paciente']['fecha_na']->sec;	
					}
					//$fecha_nacimiento = $diario[$i]["consulta"][$j]['paciente']['fecha_na']->sec;
					$edad = $fechaarte-$fecha_nacimiento;
					$edad = floor($edad/(60*60*24*365));

				}else{
					//print_r($diario[$i]["consulta"][$j]);
					$edad = -1;
					//echo $edad;
				}
				//SEXO
				if(floatval($diario[$i]["consulta"][$j]['paciente']['sexo']) == 0 && $diario[$i]['consulta'][$j]['esta'] != '5' && $diario[$i]['medico']['_id'] !='5977ae6c3e603746248b4568'){
					$mujeres++;
				}
				if(floatval($diario[$i]["consulta"][$j]['paciente']['sexo']) == 1 && $diario[$i]['consulta'][$j]['esta'] != '5' && $diario[$i]['medico']['_id'] !='5977ae6c3e603746248b4568'){
					$varones++;
				}
				//CATEGORIA
				
				if(floatval($diario[$i]["consulta"][$j]['cate']) != 8 && $diario[$i]['consulta'][$j]['esta'] != '5' && $diario[$i]['medico']['_id'] !='5977ae6c3e603746248b4568'){
					$pagante++;
				}
				if(floatval($diario[$i]["consulta"][$j]['cate']) == 8 && $diario[$i]['consulta'][$j]['esta'] != '5' && $diario[$i]['medico']['_id'] !='5977ae6c3e603746248b4568'){
					$indigente++;
				}
				///
				if($diario[$i]['consulta'][$j]['esta'] == '5' && $diario[$i]['medico']['_id'] !='5977ae6c3e603746248b4568'){
						$inter++;
				}

				//EDAD
				if($edad<=5 && floatval($diario[$i]["consulta"][$j]['paciente']['sexo']) == 0 && $diario[$i]['consulta'][$j]['esta'] != '5' && $diario[$i]['medico']['_id'] !='5977ae6c3e603746248b4568'){

				$edad1M++;
				}
				if($edad>=0 && $edad<=5 && floatval($diario[$i]["consulta"][$j]['paciente']['sexo']) == 1 && $diario[$i]['consulta'][$j]['esta'] != '5' && $diario[$i]['medico']['_id'] !='5977ae6c3e603746248b4568'){
				$edad1F++;
				}
				if($edad>=6 && $edad<=10 && floatval($diario[$i]["consulta"][$j]['paciente']['sexo']) == 0 && $diario[$i]['consulta'][$j]['esta'] != '5' && $diario[$i]['medico']['_id'] !='5977ae6c3e603746248b4568'){
					$edad2M++;
				}
				if($edad>=6 && $edad<=10 && floatval($diario[$i]["consulta"][$j]['paciente']['sexo']) == 1 && $diario[$i]['consulta'][$j]['esta'] != '5' && $diario[$i]['medico']['_id'] !='5977ae6c3e603746248b4568'){
					$edad2F++;
				}
				if($edad>=11 && $edad<=15 && floatval($diario[$i]["consulta"][$j]['paciente']['sexo']) == 0 && $diario[$i]['consulta'][$j]['esta'] != '5' && $diario[$i]['medico']['_id'] !='5977ae6c3e603746248b4568'){
					$edad3M++;
				}
				if($edad>=11 && $edad<=15 && floatval($diario[$i]["consulta"][$j]['paciente']['sexo']) == 1 && $diario[$i]['consulta'][$j]['esta'] != '5' && $diario[$i]['medico']['_id'] !='5977ae6c3e603746248b4568'){
					$edad3F++;
				}
				if($edad>=16 && $edad<=20 && floatval($diario[$i]["consulta"][$j]['paciente']['sexo']) == 0 && $diario[$i]['consulta'][$j]['esta'] != '5' && $diario[$i]['medico']['_id'] !='5977ae6c3e603746248b4568'){
					$edad4M++;
				}
				if($edad>=16 && $edad<=20 && floatval($diario[$i]["consulta"][$j]['paciente']['sexo']) == 1 && $diario[$i]['consulta'][$j]['esta'] != '5' && $diario[$i]['medico']['_id'] !='5977ae6c3e603746248b4568'){
					$edad4F++;
				}
				if($edad>=21 && $edad<=30 && floatval($diario[$i]["consulta"][$j]['paciente']['sexo']) == 0 && $diario[$i]['consulta'][$j]['esta'] != '5' && $diario[$i]['medico']['_id'] !='5977ae6c3e603746248b4568'){
					$edad5M++;
				}
				if($edad>=21 && $edad<=30 && floatval($diario[$i]["consulta"][$j]['paciente']['sexo']) == 1 && $diario[$i]['consulta'][$j]['esta'] != '5' && $diario[$i]['medico']['_id'] !='5977ae6c3e603746248b4568'){
					$edad5F++;
				}
				if($edad>=31 && $edad<=50 && floatval($diario[$i]["consulta"][$j]['paciente']['sexo']) == 0 && $diario[$i]['consulta'][$j]['esta'] != '5' && $diario[$i]['medico']['_id'] !='5977ae6c3e603746248b4568'){
					$edad6M++;
				}
				if($edad>=31 && $edad<=50 && floatval($diario[$i]["consulta"][$j]['paciente']['sexo']) == 1 && $diario[$i]['consulta'][$j]['esta'] != '5' && $diario[$i]['medico']['_id'] !='5977ae6c3e603746248b4568'){
					$edad6F++;
				}
				if($edad>=51 && $edad<=60 && floatval($diario[$i]["consulta"][$j]['paciente']['sexo']) == 0 && $diario[$i]['consulta'][$j]['esta'] != '5' && $diario[$i]['medico']['_id'] !='5977ae6c3e603746248b4568'){
					$edad7M++;
				}
				if($edad>=51 && $edad<=60 && floatval($diario[$i]["consulta"][$j]['paciente']['sexo']) == 1 && $diario[$i]['consulta'][$j]['esta'] != '5' && $diario[$i]['medico']['_id'] !='5977ae6c3e603746248b4568'){
					$edad7F++;
				}
				if($edad>=61 && $edad<=100 && floatval($diario[$i]["consulta"][$j]['paciente']['sexo']) == 0 && $diario[$i]['consulta'][$j]['esta'] != '5' && $diario[$i]['medico']['_id'] !='5977ae6c3e603746248b4568'){
					$edad8M++;
				}
				if($edad>=61 && $edad<=100 && floatval($diario[$i]["consulta"][$j]['paciente']['sexo']) == 1 && $diario[$i]['consulta'][$j]['esta'] != '5' && $diario[$i]['medico']['_id'] !='5977ae6c3e603746248b4568'){
					$edad8F++;
				}
			}
		}
		$total = 0;
		$total = $pagante + $indigente;
		
		$this->SetXY(36,91);$this->MultiCell(36,9,$total,'1','C');
		$this->SetXY(72,91);$this->MultiCell(13,9,$pagante,'1','C');
		$this->SetXY(85,91);$this->MultiCell(15,9,$indigente,'1','C');
		$this->SetXY(100,91);$this->MultiCell(8,9,$mujeres,'1','C');
		$this->SetXY(108,91);$this->MultiCell(8,9,$varones,'1','C');
			$this->SetFont('Arial','',6.7);
		$this->SetXY(116,91);$this->MultiCell(5,9,$edad1M,'1','C');
		$this->SetXY(121,91);$this->MultiCell(6,9,$edad1F,'1','C');
		$this->SetXY(127,91);$this->MultiCell(5,9,$edad2M,'1','C');
		$this->SetXY(132,91);$this->MultiCell(6,9,$edad2F,'1','C');
		$this->SetXY(138,91);$this->MultiCell(5,9,$edad3M,'1','C');
		$this->SetXY(143,91);$this->MultiCell(6,9,$edad3F,'1','C');
		$this->SetXY(149,91);$this->MultiCell(5,9,$edad4M,'1','C');
		$this->SetXY(154,91);$this->MultiCell(6,9,$edad4F,'1','C');
		$this->SetXY(160,91);$this->MultiCell(6,9,$edad5M,'1','C');
		$this->SetXY(166,91);$this->MultiCell(6,9,$edad5F,'1','C');
		$this->SetXY(172,91);$this->MultiCell(6,9,$edad6M,'1','C');
		$this->SetXY(178,91);$this->MultiCell(6,9,$edad6F,'1','C');
		$this->SetXY(184,91);$this->MultiCell(6,9,$edad7M,'1','C');
		$this->SetXY(190,91);$this->MultiCell(6,9,$edad7F,'1','C');
		$this->SetXY(196,91);$this->MultiCell(6,9,$edad8M,'1','C');
		$this->SetXY(202,91);$this->MultiCell(6,9,$edad8F,'1','C');
		

		$y= $y+5;
		
		//$this->SetXY(190,$y);$this->MultiCell(7,9,$pp,'0','C');

		

		$this->SetFont('Arial','',11);
		//RESUMEN 
		
			

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


