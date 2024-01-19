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
		$this->SetFont('Arial','BU',14);
		$this->SetXY(65,30);$this->MultiCell(75,5,	"INFORME SOCIAL NRO:    ",'0','C');
		
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
		$cate= array(
			"10"=>"Nuevo",
			"11"=>"Continuador",
			"8"=>"Indigente",
			"9"=>"Privado",
			"14"=>"Categoria A",
			"12"=>"Categoria B",
			"13"=>"Categoria C"
		);


		
		$x=5;
		$cuadro=0;
		$y=25;
		$y_ini = $y;
		$page_b = 275;
		$cuadro = 0;
		
		
		
		//$y=$y+10;
		$this->SetFont('Arial','B',9);
		$this->SetXY(30,42);$this->MultiCell(40,5,"OBJETIVO: ",'0','L');
		$this->SetXY(150,42);$this->MultiCell(40,5,"".$cate[$social['categoria']],'0','L');
		$this->SetFont('Arial','',9);
		$this->SetXY(70,42);$this->MultiCell(72,5,"INFORME SOCIOECONOMICO DEL PACIENTE",'0','R');
		$this->SetFont('Arial','B',10);
		$this->SetXY(150,50);$this->MultiCell(40,5,"Historia Clinica: ".$social['his'],'0','L');
		$this->SetFont('Arial','B',9);
		$this->SetXY(30,55);$this->MultiCell(165,5,"----------------------------------------------------------------------------------------------------------------------------------------------------------",'0','C');
		$this->SetFont('Arial','BU',10);
		$this->SetXY(30,64);$this->MultiCell(40,5,"DATOS GENERALES: ",'0','L');
		$y=$y+45;
		$this->SetFont('Arial','B',10);
		$this->SetXY(30,$y);$this->MultiCell(40,5,"Apellidos y Nombres: ",'0','L');
		$this->SetXY(140,$y);$this->MultiCell(13,5,"Edad: ",'0','L');
		$this->SetXY(163,$y);$this->MultiCell(13,5,"DNI: ",'0','L');
		$this->SetFont('Arial','',10);
		$this->SetXY(153,$y);$this->MultiCell(10,5,"".$social["edad"],'0','L');
		$this->SetXY(176,$y);$this->MultiCell(18,5,"".$social["paciente"]["paciente"]['docident'][0]['num'],'0','L');
		$this->SetXY(70,$y);$this->MultiCell(80,5,"".$social["paciente"]["paciente"]["appat"].' '.$social["paciente"]["paciente"]["apmat"].' '.$social["paciente"]["paciente"]["nomb"],'0','L');
		$y=$y+7;
		$this->SetFont('Arial','B',10);
		$this->SetXY(140,$y);$this->MultiCell(40,5,"Sexo: ",'0','L');
		$this->SetFont('Arial','',10);
		$this->SetXY(153,$y);$this->MultiCell(80,5,"".$sexos[$social["sexo"]],'0','L');
		if(isset($social['telefono'])){
			$this->SetFont('Arial','B',10);
			$this->SetXY(30,$y);$this->MultiCell(40,5,"Telefono: ",'0','L');
			$this->SetFont('Arial','',10);
			$this->SetXY(70,$y);$this->MultiCell(80,5,"".$social["telefono"],'0','L');
			$y=$y+7;
		}
		$this->SetFont('Arial','B',10);
		$this->SetXY(30,$y);$this->MultiCell(40,5,"Grado de Instruccion: ",'0','L');
		$this->SetFont('Arial','',10);
		$this->SetXY(70,$y);$this->MultiCell(80,5,"".$grado[$social["grad"]],'0','L');
		$y=$y+7;
		$this->SetFont('Arial','B',10);
		$this->SetXY(30,$y);$this->MultiCell(40,5,"Direccion Actual: ",'0','L');
		$this->SetFont('Arial','',10);
		$this->SetXY(70,$y);$this->MultiCell(80,5,"".$social["paciente"]["paciente"]['domicilios'][0]['direccion'],'0','L');
		$y=$y+12;
		
		$y=$y+5;
		
		$this->SetFont('Arial','BU',11);
		$this->SetXY(30,$y);$this->MultiCell(187,5,"ORGANIZACION FAMILIAR",'0','L');			
		$y=$y+10;
				
		$this->SetFont('Arial','B',9);
		$this->SetXY(42,$y);$this->MultiCell(32,5,"Apellido Materno",'1','L');
		$this->SetXY(74,$y);$this->MultiCell(18,5,"Nombres",'1','L'); 
		$this->SetXY(92,$y);$this->MultiCell(22,5,"Parentesco",'1','L');
		$this->SetXY(114,$y);$this->MultiCell(17,5,"Est.Civil",'1','L');		
		$this->SetXY(131,$y);$this->MultiCell(12,5,"Edad",'1','L');
		$this->SetXY(143,$y);$this->MultiCell(31,5,"Grdo.Instruccion",'1','L');
		$this->SetXY(174,$y);$this->MultiCell(23,5,"Ocupacion",'1','L');
		$this->SetXY(10,$y);$this->MultiCell(32,5,"Apellido Paterno",'1','L');
		
		
		$y=$y+3;
		if(isset($social["parientes"])){
			for($i = 0;$i<count($social["parientes"]);$i++){
				if($yini>200){
						$this->AddPage();
					$yini = $y;
					
					$this->SetFont('Arial','B',8);		
					$this->SetXY(42,$y);$this->MultiCell(32,5,"Apellido Materno",'1','C');
					$this->SetXY(74,$y);$this->MultiCell(18,5,"Nombres",'1','C'); 
					$this->SetXY(92,$y);$this->MultiCell(22,5,"Parentesco",'1','C');
					$this->SetXY(114,$y);$this->MultiCell(17,5,"Est.Civil",'1','C');		
					$this->SetXY(131,$y);$this->MultiCell(12,5,"Edad",'1','C');
					$this->SetXY(143,$y);$this->MultiCell(31,5,"Grdo.Instruccion",'1','C');
					$this->SetXY(174,$y);$this->MultiCell(23,5,"Ocupacion",'1','C');
					$this->SetXY(10,$y);$this->MultiCell(32,5,"Apellido Paterno",'1','C');
				}
			
				$y=$y+2;			
				$yini = $y;
				$this->SetFont('Arial','',8);	
				
				$this->SetXY(10,$y);$this->MultiCell(32,5,$social["parientes"][$i]['app'],'0','C');
				$this->SetXY(42,$y);$this->MultiCell(32,5,$social["parientes"][$i]['amp'],'0','C');
				$this->SetXY(74,$y);$this->MultiCell(18,5,$social["parientes"][$i]['nomp'],'0','C');
				$this->SetXY(92,$y);$this->MultiCell(22,5,$social["parientes"][$i]['paren'],'0','C');
				$this->SetXY(114,$y);$this->MultiCell(17,5,$social["parientes"][$i]['civp'],'0','C');
				$this->SetXY(131,$y);$this->MultiCell(12,5,$social["parientes"][$i]['edap'],'0','C');
				$this->SetXY(143,$y);$this->MultiCell(31,5,$social["parientes"][$i]['cfami_p'],'0','C');
				$this->SetXY(174,$y);$this->MultiCell(23,5,$social["parientes"][$i]['ocup'],'0','C');
				
				$y=$this->getY();
				$this->Line(10, $y, 197,$y);
				$this->Line(10, $yini, 10,$y);
				$this->Line(42, $yini, 42,$y);
				$this->Line(74, $yini, 74,$y);
				$this->Line(92, 112, 92,$y);
				$this->Line(114, $yini, 114,$y);
				$this->Line(131, $yini, 131,$y);
				$this->Line(143, $yini, 143,$y);
				$this->Line(174, $yini, 174,$y);
				$this->Line(197, $yini, 197,$y);
				
			}
		}
		$y=$y+7;	
		$this->SetFont('Arial','B',10);
		$this->SetXY(30,$y);$this->MultiCell(52,5,"Motivo actual de la consulta: ",'0','L');
		$this->SetFont('Arial','',8);
		if(isset($social['moti'])){
			$this->SetXY(82,$y);$this->MultiCell(125,5,"".$social['moti'],'0','L');
		}
		$y=$y+20;	
		$this->SetFont('Arial','B',10);
		$this->SetXY(30,$y);$this->MultiCell(50,5,"Soluciones Intentadas: ",'0','L');
		$this->SetFont('Arial','',8);
		if(isset($social['solu'])){
			$this->SetXY(82,$y);$this->MultiCell(125,5,"".$social["solu"],'0','L');
		}
		$y=$y+20;	
		$this->SetFont('Arial','B',10);
		$this->SetXY(30,$y);$this->MultiCell(50,5,"Estresores Psicosociales: ",'0','L');
		$this->SetFont('Arial','',8);
		if(isset($social['estr'])){
			$this->SetXY(82,$y);$this->MultiCell(125,5,"".$social["estr"],'0','L');
		}
		$y=$y+20;	
		$this->SetFont('Arial','B',10);
		$this->SetXY(30,$y);$this->MultiCell(50,5,"Sitacion Economica: ",'0','L');
		$this->SetFont('Arial','',8);
		if(isset($social['site'])){
			$this->SetXY(82,$y);$this->MultiCell(125,5,"".$social["site"],'0','L');
		}
		$y=$y+20;	
		$this->SetFont('Arial','B',10);
		$this->SetXY(30,$y);$this->MultiCell(50,5,"Sitacion de Salud: ",'0','L');
		$this->SetFont('Arial','',8);
		if(isset($social['sits'])){
			$this->SetXY(82,$y);$this->MultiCell(125,5,"".$social["sits"],'0','L');
		}
		$y=$y+20;	
		$yini = $y;
		if($yini>220){
			$this->AddPage();
				$y = 50;
		$this->SetFont('Arial','B',10);
		$this->SetXY(30,$y);$this->MultiCell(50,5,"Vivienda: ",'0','L');
		$this->SetFont('Arial','',8);
		$this->SetXY(82,$y);$this->MultiCell(125,5,"".$social["vivi"],'0','L');
		$y=$y+20;
		$this->SetFont('Arial','B',10);
		$this->SetXY(30,$y);$this->MultiCell(50,5,"Problematica del Paciente: ",'0','L');
		$this->SetFont('Arial','',8);
		$this->SetXY(82,$y);$this->MultiCell(125,5,"".$social["prob"],'0','L');
		$y=$y+20;
	}else{
		$this->SetFont('Arial','B',10);
		$this->SetXY(30,$y);$this->MultiCell(50,5,"Vivienda: ",'0','L');
		$this->SetFont('Arial','',8);
		$this->SetXY(82,$y);$this->MultiCell(125,5,"".$social["vivi"],'0','L');
		$y=$y+20;
		$this->SetFont('Arial','B',10);
		$this->SetXY(30,$y);$this->MultiCell(50,5,"Problematica del Paciente: ",'0','L');
		$this->SetFont('Arial','',8);
		$this->SetXY(82,$y);$this->MultiCell(125,5,"".$social["prob"],'0','L');
		$y=$y+20;
	}
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