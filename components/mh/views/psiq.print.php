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
		$y=$y+10;
		
		$y=$y+10;
		$y=$y+5;
		$y=$y+20;
	}		
	function Publicar($psiquiatrica){
		
		$y=25;
		$this->SetFont('Arial','B',12);
		$this->SetXY(5,$y);$this->MultiCell(200,5,"FICHA PSIQUIATRICA",'0','C');
		$x=5;
		$y=25;
		$y_ini = $y;
		$page_b = 275;
		$y=$y+15;
		$this->SetFont('Arial','B',11);
		$this->SetXY(20,$y);$this->MultiCell(50,5,"Apellidos y Nombres:",'1','L');
		$this->SetFont('Arial','',11);
		$this->SetXY(70,$y);$this->MultiCell(150,5," ".$psiquiatrica["paciente"]["paciente"]["appat"].' '.$psiquiatrica["paciente"]["paciente"]["apmat"].','.$psiquiatrica["paciente"]["paciente"]["nomb"],'1','L');
		$y=$y+5;
		$this->SetFont('Arial','B',11);
		$this->SetXY(20,$y);$this->MultiCell(35,5,"INFORMANTES:",'1','L');
		$this->SetFont('Arial','',11);
		$this->SetXY(55,$y);$this->MultiCell(150,25,"".$psiquiatrica["infor"],'1','L');
		$y=$y+25;
		$this->SetFont('Arial','B',11);
		$this->SetXY(20,$y);$this->MultiCell(53,5,"SINTOMAS PRINCIPALES: ",'1','L');
		$this->SetFont('Arial','',11);
		$this->SetXY(73,$y);$this->MultiCell(150,5,"".$psiquiatrica["sin"],'1','L');
		$y=$y+35;
		/*
		$this->SetFont('Arial','B',13);
		$this->SetXY(20,$y);$this->MultiCell(150,5,"Historia DE LA ENFERMEDAD: ",'0','L');
		$y=$y+5;
		$this->SetFont('Arial','',11);
		$this->SetXY(30,$y);$this->MultiCell(150,5,"".$psiquiatrica["hist"],'0','L');
		//HISTORIA PERSONAL
		$this->AddPage();
		$y=20;
		$this->SetFont('Arial','B',16);
		$this->SetXY(5,$y);$this->MultiCell(200,5,	"HISTORIA PERSONAL",'0','C');
		$y=$y+13;
		$this->SetFont('Arial','B',12);
		$this->SetXY(20,$y);$this->MultiCell(150,5,"Desarrollo Inicial:",'0','L');
		$this->SetFont('Arial','',11);
		$y=$y+5;
		$this->SetXY(30,$y);$this->MultiCell(150,5,"".$psiquiatrica["desa"],'0','L');
		$this->SetFont('Arial','B',12);
		$y=$y+13;
		$this->SetXY(20,$y);$this->MultiCell(150,5,"Educacion: ",'0','L');
		$this->SetFont('Arial','',11);
		$y=$y+5;
		$this->SetXY(30,$y);$this->MultiCell(150,5,"".$psiquiatrica["educ"],'0','L');
		$this->SetFont('Arial','B',12);
		$y=$y+13;
		$this->SetXY(20,$y);$this->MultiCell(150,5,"Ocupacion: ",'0','L');
		$this->SetFont('Arial','',11);
		$y=$y+5;
		$this->SetXY(30,$y);$this->MultiCell(150,5,"".$psiquiatrica["ocup"],'0','L');
		$y=$y+13;
		$this->SetFont('Arial','B',12);
		$this->SetXY(20,$y);$this->MultiCell(150,5,"Historia Psicosexual: ",'0','L');
		$y=$y+5;
		$this->SetFont('Arial','',11);
		$this->SetXY(30,$y);$this->MultiCell(150,5,"".$psiquiatrica["pisco"],'0','L');
		$y=$y+13;
		$this->SetFont('Arial','B',12);				
		$this->SetXY(20,$y);$this->MultiCell(150,5,"Historia Marital: ",'0','L');
		$y=$y+5;
		$this->SetFont('Arial','',11);
		$this->SetXY(30,$y);$this->MultiCell(150,5,"".$psiquiatrica["mari"],'0','L');
		$y=$y+13;
		$this->SetFont('Arial','B',12);
		$this->SetXY(20,$y);$this->MultiCell(150,5,"Recreacion y Vida Social: ",'0','L');
		$this->SetFont('Arial','',11);
		$y=$y+5;
		$this->SetXY(30,$y);$this->MultiCell(150,5,"".$psiquiatrica["recre"],'0','L');
		$y=$y+13;
		$this->SetFont('Arial','B',12);
		$this->SetXY(20,$y);$this->MultiCell(150,5,"Habitos: ",'0','L');
		$y=$y+5;
		$this->SetFont('Arial','',11);
		$this->SetXY(30,$y);$this->MultiCell(150,5,"".$psiquiatrica["habi"],'0','L');
		$y=$y+13;
		$this->SetFont('Arial','B',12);		
		$this->SetXY(20,$y);$this->MultiCell(150,5,"Religion: ",'0','L');
		$y=$y+5;
		$this->SetFont('Arial','',11);
		$this->SetXY(30,$y);$this->MultiCell(150,5,"".$psiquiatrica["reli"],'0','L');
		$y=$y+13;
		$this->SetFont('Arial','B',12);		
		$this->SetXY(20,$y);$this->MultiCell(150,5,"Servicio Militar: ",'0','L');
		$this->SetFont('Arial','',11);
		$y=$y+5;
		$this->SetXY(30,$y);$this->MultiCell(150,5,"".$psiquiatrica["mili"],'0','L');
		$y=$y+13;
		$this->SetFont('Arial','B',12);		
		$this->SetXY(20,$y);$this->MultiCell(150,5,"Movilidad e Instalacion: ",'0','L');
		$y=$y+5;
		$this->SetFont('Arial','',11);
		$this->SetXY(30,$y);$this->MultiCell(150,5,"".$psiquiatrica["movi"],'0','L');
		$y=$y+13;
		$this->SetFont('Arial','B',12);		
		$this->SetXY(20,$y);$this->MultiCell(150,5,"Delincuencia, record policial y judicial: ",'0','L');
		$y=$y+5;
		$this->SetFont('Arial','',11);
		$this->SetXY(30,$y);$this->MultiCell(150,5,"".$psiquiatrica["deli"],'0','L');
		$y=$y+13;
		$this->SetFont('Arial','B',12);		
		$this->SetXY(20,$y);$this->MultiCell(150,5,"Enfermedades: ",'0','L');
		$y=$y+5;
		$this->SetFont('Arial','',11);
		$this->SetXY(30,$y);$this->MultiCell(150,5,"".$psiquiatrica["enfe"],'0','L');
		$y=$y+13;
		$this->SetFont('Arial','B',12);		
		$this->SetXY(20,$y);$this->MultiCell(150,5,"Personalidad: ",'0','L');
		$y=$y+5;
		$this->SetFont('Arial','',11);
		$this->SetXY(30,$y);$this->MultiCell(150,5,"".$psiquiatrica["perso"],'0','L');
		$this->SetFont('Arial','B',12);	

		//	HISTORIA FAMILIAR

		$this->AddPage();
		$y=20;
		$this->SetFont('Arial','B',16);
		$this->SetXY(5,$y);$this->MultiCell(200,5,	"HISTORIA FAMILIAR",'0','C');
		$y=$y+13;
		$this->SetFont('Arial','B',12);
		$this->SetXY(20,$y);$this->MultiCell(150,5,"Atecedentes Generales:",'0','L');
		$this->SetFont('Arial','',11);
		$y=$y+5;
		$this->SetXY(30,$y);$this->MultiCell(150,5,"".$psiquiatrica["ante"],'0','L');
		$this->SetFont('Arial','B',12);
		$y=$y+13;
		$this->SetXY(20,$y);$this->MultiCell(150,5,"Parientes Paternos: ",'0','L');
		$this->SetFont('Arial','',11);
		$y=$y+5;
		$this->SetXY(30,$y);$this->MultiCell(150,5,"".$psiquiatrica["parip"],'0','L');
		$this->SetFont('Arial','B',12);
		$y=$y+13;
		$this->SetXY(20,$y);$this->MultiCell(150,5,"Parientes Maternos: ",'0','L');
		$this->SetFont('Arial','',11);
		$y=$y+5;
		$this->SetXY(30,$y);$this->MultiCell(150,5,"".$psiquiatrica["parim"],'0','L');
		$y=$y+13;
		$this->SetFont('Arial','B',12);
		$this->SetXY(20,$y);$this->MultiCell(150,5,"Padres: ",'0','L');
		$y=$y+5;
		$this->SetFont('Arial','',11);
		$this->SetXY(30,$y);$this->MultiCell(150,5,"".$psiquiatrica["padr"],'0','L');
		$y=$y+13;
		$this->SetFont('Arial','B',12);				
		$this->SetXY(20,$y);$this->MultiCell(150,5,"Hermanos y Hermanas: ",'0','L');
		$y=$y+5;
		$this->SetFont('Arial','',11);
		$this->SetXY(30,$y);$this->MultiCell(150,5,"".$psiquiatrica["herm"],'0','L');
		$y=$y+13;
		$this->SetFont('Arial','B',12);
		$this->SetXY(20,$y);$this->MultiCell(150,5,"Historia del hogar: ",'0','L');
		$this->SetFont('Arial','',11);
		$y=$y+5;
		$this->SetXY(30,$y);$this->MultiCell(150,5,"".$psiquiatrica["hish"],'0','L');

		//	EXAMEN MENTAL

		$this->AddPage();
		$y=20;
		$this->SetFont('Arial','B',16);
		$this->SetXY(5,$y);$this->MultiCell(200,5,	"EXAMEN MENTAL",'0','C');
		$y=$y+13;
		$this->SetFont('Arial','B',12);
		$this->SetXY(20,$y);$this->MultiCell(150,5,"Apariencia General:",'0','L');
		$this->SetFont('Arial','',11);
		$y=$y+5;
		$this->SetXY(30,$y);$this->MultiCell(150,5,"".$psiquiatrica["apar"],'0','L');
		$this->SetFont('Arial','B',12);
		$y=$y+13;
		$this->SetXY(20,$y);$this->MultiCell(150,5,"Atencion, estado, conciencia y orientacion: ",'0','L');
		$this->SetFont('Arial','',11);
		$y=$y+5;
		$this->SetXY(30,$y);$this->MultiCell(150,5,"".$psiquiatrica["aten"],'0','L');
		$this->SetFont('Arial','B',12);
		$y=$y+13;
		$this->SetXY(20,$y);$this->MultiCell(150,5,"Curso de Lenguaje: ",'0','L');
		$this->SetFont('Arial','',11);
		$y=$y+5;
		$this->SetXY(30,$y);$this->MultiCell(150,5,"".$psiquiatrica["cur"],'0','L');
		$y=$y+13;
		$this->SetFont('Arial','B',12);
		$this->SetXY(20,$y);$this->MultiCell(150,5,"Estado Efectivo:Estados de ánimo,emociones, actitudes emocionales: ",'0','L');
		$y=$y+5;
		$this->SetFont('Arial','',11);
		$this->SetXY(30,$y);$this->MultiCell(150,5,"".$psiquiatrica["efec"],'0','L');
		$y=$y+13;
		$this->SetFont('Arial','B',12);				
		$this->SetXY(20,$y);$this->MultiCell(150,5,"Contenido: Temas de preocupacion,tendencias y actitudes dominantes",'0','L');
		$y=$y+5;
		$this->SetFont('Arial','',11);
		$this->SetXY(30,$y);$this->MultiCell(150,5,"".$psiquiatrica["cont"],'0','L');
		$y=$y+13;
		$this->SetFont('Arial','B',12);
		$this->SetXY(20,$y);$this->MultiCell(150,5,"Memoria y capacidad Intelectual: ",'0','L');
		$this->SetFont('Arial','',11);
		$y=$y+5;
		$this->SetXY(30,$y);$this->MultiCell(150,5,"".$psiquiatrica["memo"],'0','L');
		$y=$y+13;
		$this->SetFont('Arial','B',12);
		$this->SetXY(20,$y);$this->MultiCell(150,5,"Comprensión de la enfermedad o problemas grado de capacidad",'0','L');
		$this->SetFont('Arial','',11);
		$y=$y+5;
		$this->SetXY(30,$y);$this->MultiCell(150,5,"".$psiquiatrica["compre"],'0','L');
		$y=$y+13;
		$this->SetFont('Arial','B',12);
		$this->SetXY(20,$y);$this->MultiCell(150,5,"Diagnostico",'0','L');
		$this->SetFont('Arial','',11);
		$y=$y+5;
		$this->SetXY(30,$y);$this->MultiCell(150,5,"".$psiquiatrica["diagn"],'0','L');
		$y=$y+13;
		$this->SetFont('Arial','B',12);
		$this->SetXY(20,$y);$this->MultiCell(150,5,"Doctor(a):",'0','L');
		$this->SetFont('Arial','',11);
		$y=$y+5;
		$this->SetXY(30,$y);$this->MultiCell(150,5,"".$psiquiatrica["doc"],'0','L');

*/
				
	}
	
	 
}

$pdf=new repo('P','mm','A4');
$pdf->AliasNbPages();
$pdf->SetMargins(10,10,0); 
$pdf->SetTitle("reporte");
$pdf->SetAutoPageBreak(false,0); 
$pdf->Open();
$pdf->AddPage();
$pdf->Publicar($psiquiatrica);
$pdf->SetLeftMargin(25);
//header('Content-Type: application/pdf; charset=utf-8');
$pdf->Output();

?>