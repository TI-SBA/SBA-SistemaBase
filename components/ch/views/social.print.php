<?php
global $f;
$f->library('pdf');
$f->library('helpers');

class repo extends FPDF
{
	function Header(){
		
		$y=10;
		$this->SetFont('Arial','B',9);
		$this->SetXY(10,5);$this->MultiCell(60,5,"SOCIEDAD DE BENEFICENCIA PUBLICA DE AREQUIPA",'0','C');		
		$this->SetXY(10,5);$this->MultiCell(190,5,date("d/m/Y")." Página: ".$this->PageNo()."/{nb}",'0','R');
		$this->SetFont('Arial','',9);
		$this->SetXY(10,15);$this->MultiCell(60,5,"Centro de Salud Mental",'0','C');
		$this->SetXY(10,20);$this->MultiCell(60,5,"'Moises Heresi'",'0','C');
		$this->SetFont('Arial','B',16);
		$this->SetXY(80,30);$this->MultiCell(55,5,	"INFORME SOCIAL",'0','C');
		
	}
	function getAge($birthday) {
		  $birth = $birthday;
		  $now = strtotime('now');
		  $age = ($now - $birth) / 31536000;
		  return floor($age);
	}		
	function Publicar($social){
		$helper=new helper();


		$rol= array(
			"1"=>"Padre",
			"2"=>"Madre",
			"3"=>"Hijo(a)",
			"4"=>"Conyugue",
			"5"=>"Hermano(a)",
			"6"=>"Otro"

			

		);
		$dinamica= array(
			"1"=>"Armoniosa",
			"2"=>"Inestable",
			"3"=>"Confictiva",
			"4"=>"Armoniosa - Inestable",
			"5"=>"Inestable - Confictiva",
			"5"=>"Confictiva - Armoniosa",
			"6"=>"Confictiva - Armoniosa - Inestable"
			

		);
		$responsable= array(
			"1"=>"Madre",
			"2"=>"Padre",
			"3"=>"Hermano(a)",
			"4"=>"Conyugue",
			"5"=>"Hijo(a)",
			"6"=>"Otro"
			

		);
		$vivienda= array(
			"1"=>"Propia",
			"2"=>"Alquilada",
			"3"=>"Alquiler - venta",
			"4"=>"Por Invasion",
			"5"=>"Alojado",
			"6"=>"Otro"
			

		);
		$tipo= array(
			"1"=>"Nuclear Completa",
			"2"=>"Nuclear Incompleta",
			"3"=>"Extendida",
			"4"=>"Agregada"
			

		);
		$soporte= array(
			"1"=>"Material",
			"2"=>"Economico",
			"3"=>"Emocional",
			"4"=>"Emocional - Material",
			"5"=>"Emocional - Economico",
			"6"=>"Material - Economico",
			"7"=>"Material - Economico - Emocional"
			

		);
		$tratamiento= array(
			"1"=>"Terapia de Apoyo",
			"2"=>"Intervencion en crisis",
			"3"=>"Consejeria",
			"4"=>"Orientacion"
			

		);
			$grado= array(
			"1"=>"S/E",
			"2"=>"C/Primaria",
			"3"=>"C/Secundaria",
			"4"=>"C/Tecnica",
			"5"=>"C/Superior",
			"6"=>"C/Universitaria",
			"7"=>"C/Jardin",
			"8"=>"Ed Especial"

		);

			$sexos= array(
			"0"=>"Femenino",
			"1"=>"Masculino"
		);
			$material= array(
			"1"=>"Noble",
			"2"=>"Rustica"
		);
			$carga= array(
			"0"=>"SI",
			"1"=>"NO"
		);


		
		$x=5;
		$cuadro=0;
		$y=25;
		$y_ini = $y;
		$page_b = 275;
		
		
		
		//$y=$y+10;
		$this->SetFont('Arial','B',10);
		$this->SetXY(30,42);$this->MultiCell(40,5,"Historia Clinica: ",'0','L');
		$this->SetXY(100,42);$this->MultiCell(20,5,"Telefono: ",'0','L');
		$this->SetXY(160,42);$this->MultiCell(15,5,"Edad: ",'0','L');
		$this->SetFont('Arial','',10);
		$this->SetXY(70,42);$this->MultiCell(13,5,"".$social["his"],'0','L');
		$this->SetXY(120,42);$this->MultiCell(20,5,"".$social["fono"],'0','L');
		$this->SetXY(175,42);$this->MultiCell(10,5,"".$social["edad"],'0','L');
		$this->SetFont('Arial','B',10);
		$y=$y+25;
		$this->SetFont('Arial','B',10);
		$this->SetXY(30,$y);$this->MultiCell(40,5,"Apellidos y Nombres: ",'0','L');
		$this->SetFont('Arial','',10);
		$this->SetXY(70,$y);$this->MultiCell(80,5,"".$social["paciente"]["paciente"]["appat"].' '.$social["paciente"]["paciente"]["apmat"].' '.$social["paciente"]["paciente"]["nomb"],'0','L');
		$y=$y+7;
		$this->SetFont('Arial','B',10);
		$this->SetXY(30,$y);$this->MultiCell(40,5,"Sexo: ",'0','L');
		$this->SetFont('Arial','',10);
		$this->SetXY(70,$y);$this->MultiCell(80,5,"".$sexos[$social["sexo"]],'0','L');
		$y=$y+7;
		$this->SetFont('Arial','B',10);
		$this->SetXY(30,$y);$this->MultiCell(40,5,"Grado de Instruccion: ",'0','L');
		$this->SetFont('Arial','',10);
		$this->SetXY(70,$y);$this->MultiCell(80,5,"".$grado[$social["grad"]],'0','L');
		$y=$y+7;
		$this->SetFont('Arial','B',10);
		$this->SetXY(30,76);$this->MultiCell(40,5,"Antecedentes: ",'0','L');
		$this->SetFont('Arial','',8);
		$this->SetXY(70,$y);$this->MultiCell(135,7,"".$social["domi"],'0','L');
		$y=$y+12;
		
		//$this->SetXY(70,$y);$this->MultiCell(80,5,"".$grado[$social["grad"]],'0','L');
		
//CUADRICULA//
		$y=$y+10;
		$this->SetFont('Arial','B',11);
		$this->SetXY(10,$y);$this->MultiCell(187,5,"COMPOSICION FAMILIAR",'1','C');			
		$y=$y+5;
				
		$this->SetFont('Arial','B',9);
		$this->SetXY(42,$y);$this->MultiCell(32,5,"Apellido Materno",'1','L');
		$this->SetXY(74,$y);$this->MultiCell(18,5,"Nombres",'1','L'); 
		$this->SetXY(92,$y);$this->MultiCell(22,5,"Parentesco",'1','L');
		$this->SetXY(114,$y);$this->MultiCell(17,5,"Est.Civil",'1','L');		
		$this->SetXY(131,$y);$this->MultiCell(12,5,"Edad",'1','L');
		$this->SetXY(143,$y);$this->MultiCell(31,5,"Grdo.Instruccion",'1','L');
		$this->SetXY(174,$y);$this->MultiCell(23,5,"Ocupacion",'1','L');
		$this->SetXY(10,$y);$this->MultiCell(32,5,"Apellido Paterno",'1','L');
		
		
		$yini = $y;
		$y=$y+3;
		
		for($i = 0;$i<count($social["parientes"]);$i++){
		if($yini>200){
				$this->AddPage();
					$yini = $y;
			
			$this->SetFont('Arial','B',8);		
			$this->SetXY(42,$y);$this->MultiCell(32,5,"Apellido Materno",'1','L');
			$this->SetXY(74,$y);$this->MultiCell(18,5,"Nombres",'1','L'); 
			$this->SetXY(92,$y);$this->MultiCell(22,5,"Parentesco",'1','L');
			$this->SetXY(114,$y);$this->MultiCell(17,5,"Est.Civil",'1','L');		
			$this->SetXY(131,$y);$this->MultiCell(12,5,"Edad",'1','L');
			$this->SetXY(143,$y);$this->MultiCell(31,5,"Grdo.Instruccion",'1','L');
			$this->SetXY(174,$y);$this->MultiCell(23,5,"Ocupacion",'1','L');
			$this->SetXY(10,$y);$this->MultiCell(32,5,"Apellido Paterno",'1','L');
		}
		
		$y=$y+3;			

			$this->SetFont('Arial','',8);	
			
			$this->SetXY(42,$y);$this->MultiCell(32,5,$social["parientes"][$i]['amp'],'0','L');
			$this->SetXY(74,$y);$this->MultiCell(18,5,$social["parientes"][$i]['nomp'],'0','L');
			$this->SetXY(92,$y);$this->MultiCell(22,5,$social["parientes"][$i]['paren'],'0','L');
			$this->SetXY(114,$y);$this->MultiCell(17,5,$social["parientes"][$i]['civp'],'0','L');
			$this->SetXY(131,$y);$this->MultiCell(12,5,$social["parientes"][$i]['edap'],'0','L');
			$this->SetXY(143,$y);$this->MultiCell(31,5,$social["parientes"][$i]['cfami_p'],'0','L');
			$this->SetXY(174,$y);$this->MultiCell(23,5,$social["parientes"][$i]['ocup'],'0','L');
			$this->SetXY(10,$y);$this->MultiCell(32,5,$social["parientes"][$i]['app'],'0','L');
					

			$y=$this->getY();
			$this->Line(10, $y, 197,$y);
			$this->Line(10, $yini, 10,$y);
			$this->Line(42, $yini, 42,$y);
			$this->Line(74, $yini, 74,$y);
			$this->Line(92, $yini, 92,$y);
			$this->Line(114, $yini, 114,$y);
			$this->Line(131, $yini, 131,$y);
			$this->Line(143, $yini, 143,$y);
			$this->Line(174, $yini, 174,$y);
			$this->Line(197, $yini, 197,$y);
			

			}

		$y=$y+7;	
		$this->SetFont('Arial','B',10);
		$this->SetXY(30,$y);$this->MultiCell(50,5,"Rol de Paciente en Familia: ",'0','L');
		$this->SetFont('Arial','',8);
		$this->SetXY(80,$y);$this->MultiCell(30,5,"".$rol[$social["rol"]],'0','L');
		$y=$y+7;	
		$this->SetFont('Arial','B',10);
		$this->SetXY(30,$y);$this->MultiCell(50,5,"Tipos de Conformacion Familiar: ",'0','L');
		$this->SetFont('Arial','',8);
		$this->SetXY(80,$y);$this->MultiCell(30,5,"".$tipo[$social["tfam"]],'0','L');
		$y=$y+12;	
		$this->SetFont('Arial','B',10);
		$this->SetXY(30,$y);$this->MultiCell(50,5,"Persona responsable del paciente: ",'0','L');
		$this->SetFont('Arial','',8);
		$this->SetXY(80,$y);$this->MultiCell(30,5,"".$responsable[$social["pres"]],'0','L');
		$y=$y+12;	
		$this->SetFont('Arial','B',10);
		$this->SetXY(30,$y);$this->MultiCell(50,5,"Dinamica Familiar: ",'0','L');
		$this->SetFont('Arial','',8);
		$this->SetXY(80,$y);$this->MultiCell(30,5,"".$dinamica[$social["dina"]],'0','L');
		$y=$y+12;	
		/*------------------------------------CUADRO------------------------------*/
		/*
		$this->SetFont('Arial','B',8);
		$this->SetXY(30,$y);$this->MultiCell(150,5,"¿SATISFECHO CON LA AYUDA QUE RECIBE DE SU FAMILIA CUANDO USTED TIENE PROBLEMAS?: ",'0','L');
		$y=$y+10;
		$this->SetFont('Arial','',8);
		$this->SetXY(30,$y);$this->MultiCell(30,5,"".$social["p1"],'0','C');
		$y=$y+15;	
		$this->SetFont('Arial','B',8);
		$this->SetXY(30,$y);$this->MultiCell(150,5,"¿CONVERSAN ENTRE USTEDES LOS PROBLEMAS QUE TIENEN EN CASA?: ",'0','L');
		$y=$y+10;
		$this->SetFont('Arial','',8);
		$this->SetXY(30,$y);$this->MultiCell(30,5,"".$social["p2"],'0','C');
		$y=$y+15;
		$this->SetFont('Arial','B',8);
		$this->SetXY(30,$y);$this->MultiCell(150,5,"¿LAS DECISIONES IMPORTANTES SE TOMAN EN CONJUNTO EN CASA?: ",'0','L');
		$y=$y+10;
		$this->SetFont('Arial','',8);
		$this->SetXY(30,$y);$this->MultiCell(30,5,"".$social["p3"],'0','C');
		$y=$y+15;
		$this->SetFont('Arial','B',8);
		$this->SetXY(30,$y);$this->MultiCell(150,5,"¿LOS FINES DE SEMANA SON COMPARTIDOS POR TODOS LOS DE LA CASA?: ",'0','L');
		$y=$y+10;
		$this->SetFont('Arial','',8);
		$this->SetXY(30,$y);$this->MultiCell(30,5,"".$social["p4"],'0','C');
		$y=$y+15;
		$this->SetFont('Arial','B',8);
		$this->SetXY(30,$y);$this->MultiCell(150,5,"¿SIENTE QUE SU FAMILIA LO QUIERE?: ",'0','L');
		$y=$y+10;
		$this->SetFont('Arial','',8);
		$this->SetXY(30,$y);$this->MultiCell(30,5,"".$social["p5"],'0','C');
		$y=$y+15;
		*/
		/*-------------------------------------------------------------------------*/
		$cuadro = $y;
		$cuadro = $cuadro + 3;
		$this->SetXY(125,$cuadro);$this->MultiCell(30,11,"".$social["p1"],'1','C');
		$cuadro = $cuadro + 11;
		$this->SetXY(125,$cuadro);$this->MultiCell(30,8,"".$social["p2"],'1','C');
		$cuadro = $cuadro + 8;
		$this->SetXY(125,$cuadro);$this->MultiCell(30,8,"".$social["p3"],'1','C');
		$cuadro = $cuadro + 8;
		$this->SetXY(125,$cuadro);$this->MultiCell(30,11,"".$social["p4"],'1','C');
		$cuadro = $cuadro + 11;
		$this->SetXY(125,$cuadro);$this->MultiCell(30,8,"".$social["p5"],'1','C');
		$cuadro = $cuadro + 8;
		
		$this->Image(IndexPath.DS.'templates/mh/cuadro_2.jpg',25,$y,100,50);	
		
		$y=$y+53;	
		
		$this->SetFont('Arial','B',10);
		$this->SetXY(30,$y);$this->MultiCell(50,5,"Numero de miembros economicamente activos: ",'0','L');
		$this->SetFont('Arial','',8);
		$this->SetXY(80,$y);$this->MultiCell(30,5,"".$social["nmie"],'0','L');
		$y=$y+14;	
		$this->SetFont('Arial','B',10);
		$this->SetXY(30,$y);$this->MultiCell(50,5,"Carga Familiar: ",'0','L');
		$this->SetFont('Arial','',8);
		$this->SetXY(80,$y);$this->MultiCell(30,5,"".$carga[$social["cfami"]],'0','L');
		$y=$y+12;	
		$this->SetFont('Arial','B',10);
		$this->SetXY(30,$y);$this->MultiCell(50,5,"Ingreso Economico Familiar: ",'0','L');
		$this->SetFont('Arial','',8);
		$this->SetXY(80,$y);$this->MultiCell(120,5,"".$social["ingr"],'0','L');
		$y=$y+12;	
		$this->SetFont('Arial','B',10);
		$this->SetXY(30,$y);$this->MultiCell(50,5,"Vivienda: ",'0','L');
		$this->SetFont('Arial','',8);
		$this->SetXY(80,$y);$this->MultiCell(30,5,"".$vivienda[$social["vivi"]],'0','L');
		$y=$y+12;	
		$this->AddPage();
		$y=$y_ini;
		$y= 25;
		$y=$y+20;	
		
		$this->SetFont('Arial','B',10);
		$this->SetXY(30,$y);$this->MultiCell(50,5,"Material Construccion: ",'0','L');
		$this->SetFont('Arial','',8);
		$this->SetXY(80,$y);$this->MultiCell(30,5,"".$material[$social["cons"]],'0','L');
		$y=$y+12;	
		$this->SetFont('Arial','B',10);
		$this->SetXY(30,$y);$this->MultiCell(50,5,"Numero de Habitaciones: ",'0','L');
		$this->SetFont('Arial','',8);
		$this->SetXY(80,$y);$this->MultiCell(50,5,"".$social["nhab"],'0','L');
		$y=$y+12;	
		$this->SetFont('Arial','B',10);
		$this->SetXY(30,$y);$this->MultiCell(40,5,"Diagnostico Social Familiar: ",'0','L');
		$this->SetFont('Arial','',8);
		$this->SetXY(70,$y);$this->MultiCell(135,5,"".$social["dsoc"],'0','L');
		$y=$y+22;	
		$this->SetFont('Arial','B',10);
		$this->SetXY(30,$y);$this->MultiCell(50,5,"Tratamiento Social: ",'0','L');
		$this->SetFont('Arial','',8);
		$this->SetXY(70,$y);$this->MultiCell(30,5,"".$tratamiento[$social["tsoc"]],'0','L');
		$y=$y+20;	
		$this->SetFont('Arial','B',10);
		$this->SetXY(30,$y);$this->MultiCell(40,5,"Pronostico Social: ",'0','L');
		$this->SetFont('Arial','',8);
		$this->SetXY(70,$y);$this->MultiCell(135,5,"".$social["psoc"],'0','L');
		
}
	
	 
}

$pdf=new repo('P','mm','A4');
$pdf->AliasNbPages();
$pdf->SetMargins(10,10,0); 
$pdf->SetTitle("reporte");
$pdf->SetAutoPageBreak(false,0); 
$pdf->Open();
$pdf->AddPage();
$pdf->Publicar($social);
$pdf->SetLeftMargin(25);
//header('Content-Type: application/pdf; charset=utf-8');
$pdf->Output();

?>