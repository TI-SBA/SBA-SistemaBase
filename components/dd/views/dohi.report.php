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
		$this->SetFont('Arial','B',11);
		$this->SetXY(10,15);$this->MultiCell(60,5,"Archivo Central",'0','C');
		$this->SetFont('Arial','B',14);
		$this->SetXY(5,30);$this->MultiCell(200,5,	"DOCUMENTOS HISTORICOS REGISTRADOS",'0','C');
		
	}	
	
 		
	function Publicar($dohi){

		$documento=array(
			"0"=>"DOCUMENTOS HISTORICOS",
			"1"=>"ARCHIVO FOTOGRAFICO",
			"2"=>"PRINCIPALES ACTAS DE ASAMBLEA GENERAL",
			""=>"-----------------------",
			"3"=>"PRINCIPALES ACTAS DE DIRECTORIO"
			);
		
		//$total_consultas = 0;
		$x=5;
		$y=25;
		$y_ini = $y;
		$page_b = 275;
		$y=$y+20;
		$this->SetFont('Arial','B',8);
		$yini = $y;

			$this->SetXY(30,$y);$this->MultiCell(35,9,"Numero de Documentos",'1','C');
			$this->SetXY(65,$y);$this->MultiCell(35,9,"Documento",'1','C');
			$this->SetXY(135,$y);$this->MultiCell(33,9,"Fecha de Registro",'1','C');
			$this->SetXY(100,$y);$this->MultiCell(35,9,"Tipo de Documento",'1','C');
			
		$y=$y+9;
		for($i=0;$i<count($dohi);$i++){
			$this->SetFont('Arial','',7);
			$this->SetXY(30,$y);$this->MultiCell(35,9,"".$dohi[$i]["ndoc"],'0','C');
			$this->SetXY(65,$y);$this->MultiCell(35,9,"".$dohi[$i]["titu"],'0','C');
			$this->SetXY(135,$y);$this->MultiCell(33,9,"".date('d/m/Y',$dohi[$i]["femi"]->sec),'0','C');
			$this->SetXY(100,$y);$this->MultiCell(35,9,"".$documento[$dohi[$i]["docu"]],'0','C');
			
			$y=$y+9;
			$y=$this->getY();
			$this->Line(30, $y, 168,$y);
			$this->Line(30, $yini, 30,$y);
			$this->Line(65, $yini, 65,$y);
			$this->Line(100, $yini, 100,$y);
			$this->Line(135, $yini, 135,$y);
			$this->Line(168, $yini, 168,$y);
			
			
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
$pdf->Publicar($dohi);
$pdf->SetLeftMargin(25);
//header('Content-Type: application/pdf; charset=utf-8');
$pdf->Output();

?>


