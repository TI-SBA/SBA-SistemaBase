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
		$this->SetXY(5,30);$this->MultiCell(200,5,	"LISTA DE DOCUMENTOS RECEPCIONADOS",'0','C');
		
	}	
	
 	
	function Publicar($redo){
		
		//$total_consultas = 0;
		$x=5;
		$y=25;
		$y_ini = $y;
		$page_b = 275;
		$y=$y+12;
		$year_0 = '';
		$year_1 = '';
		$this->SetFont('Arial','B',7);
		//CABECERAS
		$yini = $y;
			//$this->SetXY(1,$y);$this->MultiCell(5,9," ",'0','C');
			$this->SetXY(1,$y);$this->MultiCell(13,9,"Nro.",'1','C');
			$this->SetXY(14,$y);$this->MultiCell(35,9,"Documento",'1','C');
			$this->SetXY(49,$y);$this->MultiCell(35,9,"Direccion",'1','C');
			$this->SetXY(84,$y);$this->MultiCell(35,9,"Tipo de Serie",'1','C');
			$this->SetXY(179,$y);$this->MultiCell(30,9,"Fecha de Registro",'1','C');
			$this->SetXY(119,$y);$this->MultiCell(35,9,"Tipo de Documento",'1','C');
			$this->SetXY(154,$y);$this->MultiCell(25,9,"Fec. Extremas",'1','C');
			
		$y=$y+9;
		for($i=0;$i<count($redo);$i++){
	
		if( isset($redo[$i]['year'])){
			$year_0 = $redo[$i]['year'][0]['fec'];

			if(isset($redo[$i]['year'][1])){
				$year_1 = $redo[$i]['year'][1]['fec'];	
			}else{
				$year_1 = '';
			}
		}else{
			$year_0 = '';
		}
			
			if($y>280){
				$this->AddPage();
				$y = $yini;
			$this->SetFont('Arial','B',7);
			$this->SetXY(1,$y);$this->MultiCell(13,9,"Nro.",'1','C');
			$this->SetXY(14,$y);$this->MultiCell(35,9,"Documento",'1','C');
			$this->SetXY(49,$y);$this->MultiCell(35,9,"Direccion",'1','C');
			$this->SetXY(84,$y);$this->MultiCell(35,9,"Tipo de Serie",'1','C');
			$this->SetXY(179,$y);$this->MultiCell(30,9,"Fecha de Registro",'1','C');
			$this->SetXY(119,$y);$this->MultiCell(35,9,"Tipo de Documento",'1','C');
			$this->SetXY(154,$y);$this->MultiCell(25,9,"Fec. Extremas",'1','C');
			
				$y+=9;
			}
			
			$this->SetFont('Arial','',7);
			$this->SetXY(1,$y);$this->MultiCell(13,9,"".$redo[$i]["nro"],'0','C');
			$y_1 = ceil($this->GetStringWidth($redo[$i]["tise"])/35);
			$this->SetXY(84,$y);$this->MultiCell(35,9,"".$redo[$i]["tise"],'0','C');
			$this->SetFont('Arial','',7);
			$y_4 = ceil($this->GetStringWidth($redo[$i]["tipo_"])/35);
			$this->SetXY(119,$y);$this->MultiCell(35,9,"".$redo[$i]["tipo_"],'0','C');
						
			$y_3 = ceil($this->GetStringWidth($redo[$i]["fecreg"])/33);
			$this->SetFont('Arial','',7);
			$this->SetXY(179,$y);$this->MultiCell(33,9,"".date('d-m-Y',$redo[$i]["fecreg"]->sec),'0','C');
			$this->SetFont('Arial','',7);
			$y_5 = ceil($this->GetStringWidth($redo[$i]["dire"])/35);
			$this->SetXY(49,$y);$this->MultiCell(35,9,"".$redo[$i]["dire"],'0','C');
			/*******************************/

			$this->SetFont('Arial','',7);
			$y_6 = ceil($this->GetStringWidth($year_0)/25);
			$this->SetXY(154,$y);$this->MultiCell(35,9,$year_0.'-'.$year_1,'0','L');

			/*******************************/
			$this->SetFont('Arial','',6);
			$y_2 = ceil($this->GetStringWidth($redo[$i]["titu"])/35);
			$this->SetXY(14,$y);$this->MultiCell(35,9,"".$redo[$i]["titu"],'0','C');
			$y+=max($y_2,$y_5, $y_3, $y_1,$y_4,$y_6)*9;

			$y=$y+9;
			//$y=$this->getY();
			$this->Line(1, $y, 214,$y);
			$this->Line(1, $y, 1,$y);
			$this->Line(14, $yini, 14,$y);
			$this->Line(49, $yini, 49,$y);
			$this->Line(84, $yini, 84,$y);
			$this->Line(119, $yini, 119,$y);
			$this->Line(154, $yini, 154,$y);
			$this->Line(179, $yini, 179,$y);
			$this->Line(214, $yini, 214,$y);

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
$pdf->Publicar($redo);
$pdf->SetLeftMargin(25);
//header('Content-Type: application/pdf; charset=utf-8');
$pdf->Output();

?>


