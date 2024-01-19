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
		$this->SetXY(5,30);$this->MultiCell(200,5,	"LISTA DE DOCUMENTOS REGISTRADOS",'0','C');
		
	}	
	
 	
	function Publicar($regi){
		
		//$total_consultas = 0;
		$x=5;
		$y=25;
		$y_ini = $y;
		$page_b = 275;
		$y=$y+12;
		$this->SetFont('Arial','B',8);
		//CABECERAS
		$yini = $y;
			//$this->SetXY(1,$y);$this->MultiCell(5,9," ",'1','C');
			$this->SetXY(1,$y);$this->MultiCell(15,9,"N.Doc",'1','C');
			$this->SetXY(16,$y);$this->MultiCell(25,9,"Documento",'1','C');
			$this->SetXY(41,$y);$this->MultiCell(55,9,"Direccion",'1','C');
			$this->SetXY(96,$y);$this->MultiCell(45,9,"Oficina",'1','C');
			$this->SetXY(141,$y);$this->MultiCell(49,9,"Tipo de Documento",'1','C');
			$this->SetXY(190,$y);$this->MultiCell(15,9,"Fecha",'1','C');
			$y=$y+9;
			
		for($i=0;$i<count($regi);$i++){
			if($y>280){
				$this->AddPage();
				$y = $yini;
				$this->SetXY(1,$y);$this->MultiCell(15,9,"N.Doc",'1','C');
				$this->SetXY(16,$y);$this->MultiCell(25,9,"Documento",'1','C');
				$this->SetXY(41,$y);$this->MultiCell(55,9,"Direccion",'1','C');
				$this->SetXY(96,$y);$this->MultiCell(45,9,"Oficina",'1','C');
				$this->SetXY(141,$y);$this->MultiCell(49,9,"Tipo de Documento",'1','C');
				$this->SetXY(190,$y);$this->MultiCell(15,9,"Fecha",'1','C');
				$y+=9;
			}

			$this->SetFont('Arial','',7);
			$this->SetXY(1,$y);$this->MultiCell(15,9,"".$regi[$i]["ndoc"],'0','C');
			$y_2 = ceil($this->GetStringWidth($regi[$i]["titu"])/25);
			$this->SetXY(16,$y);$this->MultiCell(25,9,"".$regi[$i]["titu"],'0','C');
			$this->SetFont('Arial','',6);
			$y_3 = ceil($this->GetStringWidth($regi[$i]["dire"])/55);
			$this->SetXY(41,$y);$this->MultiCell(55,9,"".$regi[$i]["dire"],'0','C');
			$this->SetFont('Arial','',6);
			$this->SetXY(141,$y);$this->MultiCell(49,9,"".$regi[$i]["docu"],'0','C');
			$this->SetFont('Arial','',7);
			$this->SetXY(190,$y);$this->MultiCell(15,9,"".date('d-m-Y',$regi[$i]["femi"]->sec),'0','C');
			$this->SetFont('Arial','',6);
			$y_1 = ceil($this->GetStringWidth($regi[$i]["ofic"])/45);
			$this->SetXY(96,$y);$this->MultiCell(45,9,"".$regi[$i]["ofic"],'0','C');
			$y+=max($y_1, $y_2, $y_3)*9;

//			$yini=$this->getY();
			$this->Line(1, $y, 205,$y);
			$this->Line(16, $yini, 16,$y);
			$this->Line(41, $yini, 41,$y);
			$this->Line(96, $yini, 96,$y);
			$this->Line(141, $yini, 141,$y);
			$this->Line(190, $yini, 190,$y);
			$this->Line(205, $yini, 205,$y);
			$y=$y+9;
		}
			//$y=$this->getY();
			

		



			

	}
	
	 
}

$pdf=new repo('P','mm','A4');
$pdf->AliasNbPages();
$pdf->SetMargins(10,10,0); 
$pdf->SetTitle("reporte");
$pdf->SetAutoPageBreak(false,0); 
$pdf->Open();
$pdf->AddPage();
$pdf->Publicar($regi);
$pdf->SetLeftMargin(25);
//header('Content-Type: application/pdf; charset=utf-8');
$pdf->Output();

?>


