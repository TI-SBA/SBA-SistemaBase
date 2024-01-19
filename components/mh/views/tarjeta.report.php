<?php
global $f;
$f->library('pdf');

class repo extends FPDF
{
	
	function Header(){
		//$this->Image(IndexPath.DS.'templates/ts/comprobante.gif',10,10,190,275);	
		$y=10;
		$this->SetFont('Arial','B',6);
		$this->SetXY(10,5);$this->MultiCell(60,5,"SOCIEDAD DE BENEFICENCIA PUBLICA DE AREQUIPA",'0','C');		
		$this->SetXY(133,5);$this->MultiCell(44,5,date("d/m/Y")." Página: ".$this->PageNo()."/{nb}",'0','C');
		$this->SetFont('Arial','',6);
		$this->SetXY(10,8);$this->MultiCell(60,5,"Centro de Salud Mental",'0','C');
		$this->SetXY(10,11);$this->MultiCell(60,5,"'Moises Heresi'",'0','C');
		$this->SetFont('Times','B',13);
		$this->SetXY(60,19);$this->MultiCell(58,5,"TARJETA DE ADMISION",'0','C');
		$y=$y+10;
		
	
		$y=$y+5;
		
		$y=$y+5;
	}
	function Publicar($paciente){
			

		$x=5;
		$y=25;
		$y_ini = $y;
		$page_b = 275;

		$this->SetFont('Arial','B',7);
		$this->SetXY(10,35);$this->MultiCell(14,8,"NOMBRE: ",'0','L'); 
		$this->SetFont('Arial','',7);
		$this->SetXY(24,35);$this->MultiCell(95,8,"".$paciente["paciente"]["appat"].' '.$paciente["paciente"]["apmat"].' '.$paciente["paciente"]["nomb"],'0','L'); 
		$this->SetFont('Arial','B',7);
		$this->SetXY(10,45);$this->MultiCell(14,8,"HC: ",'0','L'); 
		$this->SetFont('Arial','',7);
		$this->SetXY(24,45);$this->MultiCell(95,8,"".$paciente["his_cli"],'0','L'); 
		$this->SetFont('Arial','B',7);
		$this->SetXY(10,55);$this->MultiCell(29,8,"Fecha de Inscripcion: ",'0','L'); 
		$this->SetFont('Arial','',7);
		$this->SetXY(39,55);$this->MultiCell(15,8,"".date('d-m-Y',$paciente["fecreg"]->sec),'0','L');
		$this->SetFont('Arial','B',7);
		$this->SetXY(10,65);$this->MultiCell(15,8,"Direccion: ",'0','L'); 
		$this->SetFont('Arial','',7);
		$this->SetXY(25,65);$this->MultiCell(95,8,"".$paciente["paciente"]['domicilios'][0]['direccion'],'0','L');
		$this->AddPage();
		$y= 5;
		$this->SetFont('Arial','B',8);
		$this->SetXY(15,27);$this->MultiCell(72,6,"HOSPITAL COMPLETO",'1','C'); 
		$this->SetXY(87,27);$this->MultiCell(72,6,"HOSPITAL DEL DIA",'1','C'); 
		$this->SetFont('Arial','B',6);
		$this->SetXY(15,33);$this->MultiCell(21,6,"FECHA INGRESO",'1','C'); 
		$this->SetXY(15,39);$this->MultiCell(21,6,"",'1','C'); 
		$this->SetXY(15,45);$this->MultiCell(21,6,"",'1','C'); 
		$this->SetXY(15,51);$this->MultiCell(21,6,"",'1','C'); 
		$this->SetXY(15,57);$this->MultiCell(21,6,"",'1','C'); 
		$this->SetXY(15,63);$this->MultiCell(21,6,"",'1','C'); 
		$this->SetXY(15,69);$this->MultiCell(21,6,"",'1','C'); 
		$this->SetXY(15,75);$this->MultiCell(21,6,"",'1','C'); 
		$this->SetXY(15,81);$this->MultiCell(21,6,"",'1','C'); 
		$this->SetXY(15,87);$this->MultiCell(21,6,"",'1','C'); 
		$this->SetXY(15,93);$this->MultiCell(21,6,"",'1','C'); 
		$this->SetXY(15,99);$this->MultiCell(21,6,"",'1','C'); 
		$this->SetXY(15,105);$this->MultiCell(21,6,"",'1','C'); 
		$this->SetXY(15,111);$this->MultiCell(21,6,"",'1','C'); 

		$this->SetXY(36,33);$this->MultiCell(21,6,"FECHA EGRESO",'1','C'); 
		$this->SetXY(36,39);$this->MultiCell(21,6,"",'1','C'); 
		$this->SetXY(36,45);$this->MultiCell(21,6,"",'1','C'); 
		$this->SetXY(36,51);$this->MultiCell(21,6,"",'1','C'); 
		$this->SetXY(36,57);$this->MultiCell(21,6,"",'1','C'); 
		$this->SetXY(36,63);$this->MultiCell(21,6,"",'1','C'); 
		$this->SetXY(36,69);$this->MultiCell(21,6,"",'1','C'); 
		$this->SetXY(36,75);$this->MultiCell(21,6,"",'1','C'); 
		$this->SetXY(36,81);$this->MultiCell(21,6,"",'1','C'); 
		$this->SetXY(36,87);$this->MultiCell(21,6,"",'1','C'); 
		$this->SetXY(36,93);$this->MultiCell(21,6,"",'1','C'); 
		$this->SetXY(36,99);$this->MultiCell(21,6,"",'1','C'); 
		$this->SetXY(36,105);$this->MultiCell(21,6,"",'1','C'); 
		$this->SetXY(36,111);$this->MultiCell(21,6,"",'1','C'); 
		$this->SetFont('Arial','B',5);
		$this->SetXY(57,33);$this->MultiCell(30,6,"DIAS DE HOSPITALIZACION",'1','C');
		$this->SetXY(57,39);$this->MultiCell(30,6,"",'1','C'); 
		$this->SetXY(57,45);$this->MultiCell(30,6,"",'1','C'); 
		$this->SetXY(57,51);$this->MultiCell(30,6,"",'1','C'); 
		$this->SetXY(57,57);$this->MultiCell(30,6,"",'1','C'); 
		$this->SetXY(57,63);$this->MultiCell(30,6,"",'1','C'); 
		$this->SetXY(57,69);$this->MultiCell(30,6,"",'1','C'); 
		$this->SetXY(57,75);$this->MultiCell(30,6,"",'1','C'); 
		$this->SetXY(57,81);$this->MultiCell(30,6,"",'1','C'); 
		$this->SetXY(57,87);$this->MultiCell(30,6,"",'1','C'); 
		$this->SetXY(57,93);$this->MultiCell(30,6,"",'1','C'); 
		$this->SetXY(57,99);$this->MultiCell(30,6,"",'1','C'); 
		$this->SetXY(57,105);$this->MultiCell(30,6,"",'1','C'); 
		$this->SetXY(57,111);$this->MultiCell(30,6,"",'1','C'); 

			$this->SetFont('Arial','B',6);
		$this->SetXY(87,33);$this->MultiCell(21,6,"FECHA INGRESO",'1','C'); 
		$this->SetXY(87,39);$this->MultiCell(21,6,"",'1','C'); 
		$this->SetXY(87,45);$this->MultiCell(21,6,"",'1','C'); 
		$this->SetXY(87,51);$this->MultiCell(21,6,"",'1','C'); 
		$this->SetXY(87,57);$this->MultiCell(21,6,"",'1','C'); 
		$this->SetXY(87,63);$this->MultiCell(21,6,"",'1','C'); 
		$this->SetXY(87,69);$this->MultiCell(21,6,"",'1','C'); 
		$this->SetXY(87,75);$this->MultiCell(21,6,"",'1','C'); 
		$this->SetXY(87,81);$this->MultiCell(21,6,"",'1','C'); 
		$this->SetXY(87,87);$this->MultiCell(21,6,"",'1','C'); 
		$this->SetXY(87,93);$this->MultiCell(21,6,"",'1','C'); 
		$this->SetXY(87,99);$this->MultiCell(21,6,"",'1','C'); 
		$this->SetXY(87,105);$this->MultiCell(21,6,"",'1','C'); 
		$this->SetXY(87,111);$this->MultiCell(21,6,"",'1','C'); 

		$this->SetXY(108,33);$this->MultiCell(21,6,"FECHA EGRESO",'1','C'); 
		$this->SetXY(108,39);$this->MultiCell(21,6,"",'1','C'); 
		$this->SetXY(108,45);$this->MultiCell(21,6,"",'1','C'); 
		$this->SetXY(108,51);$this->MultiCell(21,6,"",'1','C'); 
		$this->SetXY(108,57);$this->MultiCell(21,6,"",'1','C'); 
		$this->SetXY(108,63);$this->MultiCell(21,6,"",'1','C'); 
		$this->SetXY(108,69);$this->MultiCell(21,6,"",'1','C'); 
		$this->SetXY(108,75);$this->MultiCell(21,6,"",'1','C'); 
		$this->SetXY(108,81);$this->MultiCell(21,6,"",'1','C'); 
		$this->SetXY(108,87);$this->MultiCell(21,6,"",'1','C'); 
		$this->SetXY(108,93);$this->MultiCell(21,6,"",'1','C'); 
		$this->SetXY(108,99);$this->MultiCell(21,6,"",'1','C'); 
		$this->SetXY(108,105);$this->MultiCell(21,6,"",'1','C'); 
		$this->SetXY(108,111);$this->MultiCell(21,6,"",'1','C'); 
		$this->SetFont('Arial','B',5);
		$this->SetXY(129,33);$this->MultiCell(30,6,"DIAS DE HOSPITALIZACION",'1','C');
		$this->SetXY(129,39);$this->MultiCell(30,6,"",'1','C'); 
		$this->SetXY(129,45);$this->MultiCell(30,6,"",'1','C'); 
		$this->SetXY(129,51);$this->MultiCell(30,6,"",'1','C'); 
		$this->SetXY(129,57);$this->MultiCell(30,6,"",'1','C'); 
		$this->SetXY(129,63);$this->MultiCell(30,6,"",'1','C'); 
		$this->SetXY(129,69);$this->MultiCell(30,6,"",'1','C'); 
		$this->SetXY(129,75);$this->MultiCell(30,6,"",'1','C'); 
		$this->SetXY(129,81);$this->MultiCell(30,6,"",'1','C'); 
		$this->SetXY(129,87);$this->MultiCell(30,6,"",'1','C'); 
		$this->SetXY(129,93);$this->MultiCell(30,6,"",'1','C'); 
		$this->SetXY(129,99);$this->MultiCell(30,6,"",'1','C'); 
		$this->SetXY(129,105);$this->MultiCell(30,6,"",'1','C'); 
		$this->SetXY(129,111);$this->MultiCell(30,6,"",'1','C'); 


		
		
		
	
	}
}
//$pdf=new repo('L','mm','A4');
$pdf = new repo('L','mm',array(120,170));
$pdf->AliasNbPages();
$pdf->SetMargins(10,10,0); 
$pdf->SetTitle("reporte");
$pdf->SetAutoPageBreak(false,0); 
$pdf->Open();
$pdf->AddPage();
$pdf->Publicar($paciente);
$pdf->SetLeftMargin(25);
//header('Content-Type: application/pdf; charset=utf-8');
$pdf->Output();
?>