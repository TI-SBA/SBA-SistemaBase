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
		$this->SetXY(5,30);$this->MultiCell(200,5,	"LISTA DE DOCUMENTOS DEPURADOS",'0','C');
		
	}	
	
 	
	function Publicar($depu){
		
		//$total_consultas = 0;
		$x=5;
		$y=25;
		$y_ini = $y;
		$page_b = 275;
		$y=$y+12;
		$this->SetFont('Arial','B',8);
		//CABECERAS
		$yini = $y;
		
			//$this->SetXY(1,$y);$this->MultiCell(5,9," ",'0','C');
			$this->SetXY(1,$y);$this->MultiCell(30,9,"Documento",'1','C');
			$this->SetXY(31,$y);$this->MultiCell(35,9,"Direccion",'1','C');
			$this->SetXY(66,$y);$this->MultiCell(35,9,"Oficina",'1','C');
			$this->SetXY(101,$y);$this->MultiCell(30,9,"Tipo Documento",'1','C');
			$this->SetXY(131,$y);$this->MultiCell(23,9,"Metraje",'1','C');
			$this->SetXY(154,$y);$this->MultiCell(25,9,"Cajas",'1','C');
			$this->SetXY(179,$y);$this->MultiCell(30,9,"Fecha Depuracion",'1','C');
		$y=$y+9;
		
		for($i=0;$i<count($depu);$i++){

			if($y>280){
				$this->AddPage();
				$y = $yini;
					//$this->SetXY(1,$y);$this->MultiCell(5,9," ",'0','C');
				$this->SetXY(1,$y);$this->MultiCell(30,9,"Documento",'1','C');
				$this->SetXY(31,$y);$this->MultiCell(35,9,"Direccion",'1','C');
				$this->SetXY(66,$y);$this->MultiCell(35,9,"Oficina",'1','C');
				$this->SetXY(101,$y);$this->MultiCell(30,9,"Tipo Documento",'1','C');
				$this->SetXY(131,$y);$this->MultiCell(23,9,"Metraje",'1','C');
				$this->SetXY(154,$y);$this->MultiCell(25,9,"Cajas",'1','C');
				$this->SetXY(179,$y);$this->MultiCell(30,9,"Fecha Depuracion",'1','C');
				$y+=9;
			}


			$this->SetFont('Arial','',6.8);
			$y_1 = ceil($this->GetStringWidth($depu[$i]["nomb"])/30);
			$this->SetXY(1,$y);$this->MultiCell(30,9,"".$depu[$i]["nomb"],'0','C');
			$y_2 = ceil($this->GetStringWidth($depu[$i]["dire"])/35);
			$this->SetXY(31,$y);$this->MultiCell(35,9,"".$depu[$i]["dire"],'0','C');
			$y_3 = ceil($this->GetStringWidth($depu[$i]["ofic"])/35);
			$this->SetXY(66,$y);$this->MultiCell(35,9,"".$depu[$i]["ofic"],'0','C');
			$this->SetXY(101,$y);$this->MultiCell(30,9,"".$depu[$i]["docu"],'0','C');
			$this->SetXY(131,$y);$this->MultiCell(23,9,"".$depu[$i]["metr"],'0','C');
			$this->SetXY(154,$y);$this->MultiCell(25,9,"".$depu[$i]["casa"],'0','C');
			$this->SetXY(179,$y);$this->MultiCell(30,9,"".date('d-m-Y',$depu[$i]["femi"]->sec),'0','C');
			
			$y+=max($y_1, $y_2, $y_3)*8;
			$this->Line(1, $y, 209,$y);
			$this->Line(1, $y, 1,$y);
			$this->Line(31, $yini, 31,$y);
			$this->Line(66, $yini, 66,$y);
			$this->Line(101, $yini, 101,$y);
			$this->Line(131, $yini, 131,$y);
			$this->Line(154, $yini, 154,$y);
			$this->Line(179, $yini, 179,$y);
			$this->Line(209, $yini, 209,$y);

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
$pdf->Publicar($depu);
$pdf->SetLeftMargin(25);
//header('Content-Type: application/pdf; charset=utf-8');
$pdf->Output();

?>


