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
		$this->SetXY(170,5);$this->MultiCell(44,5,date("d/m/Y")." Página: ".$this->PageNo()."/{nb}",'0','C');
		$this->SetFont('Arial','',6);
		$this->SetXY(10,8);$this->MultiCell(60,5,"Centro de Salud Mental",'0','C');
		$this->SetXY(10,11);$this->MultiCell(60,5,"'Moises Heresi'",'0','C');
		$this->SetFont('Times','B',13);
		$this->SetXY(80,19);$this->MultiCell(58,5,"CARNET DE PACIENTE",'0','C');
		$y=$y+10;
		
	
		$y=$y+5;
		
		$y=$y+5;
	}
	function Publicar($paciente){
			

		$x=5;
		$y=25;
		$y_ini = $y;
		$page_b = 275;

		$this->SetFont('Arial','B',10);
		$this->SetXY(40,35);$this->MultiCell(45,8,"APELLIDO PATERNO ",'1','C'); 
		$this->SetXY(85,35);$this->MultiCell(45,8,"APELLIDO MATERNO ",'1','C'); 
		$this->SetXY(130,35);$this->MultiCell(45,8,"NOMBRE ",'1','C'); 
		$this->SetFont('Arial','',10);
		$this->SetXY(40,43);$this->MultiCell(45,8,"".$paciente["paciente"]["appat"],'1','C'); 
		$this->SetXY(85,43);$this->MultiCell(45,8,"".$paciente["paciente"]["apmat"],'1','C'); 
		$this->SetXY(130,43);$this->MultiCell(45,8,"".$paciente["paciente"]["nomb"],'1','C'); 
		$this->SetFont('Arial','B',10);
		$this->SetXY(40,51);$this->MultiCell(45,8,"HISTORIA CLINICA ",'1','C'); 
		$this->SetXY(85,51);$this->MultiCell(45,8,"MEDICO TRATANTE",'1','C'); 
		$this->SetXY(130,51);$this->MultiCell(45,8,"DIA DE CONSULTA",'1','C'); 
		$this->SetFont('Arial','',10);
		$this->SetXY(40,59);$this->MultiCell(45,8,"".$paciente["his_cli"],'1','C'); 
		$this->SetXY(85,59);$this->MultiCell(45,8,"",'1','C'); 
		$this->SetXY(130,59);$this->MultiCell(45,8,"",'1','C'); 

		$this->SetFont('Arial','B',10);
		$this->SetXY(20,70);$this->MultiCell(45,8,"FECHA DE ADMISION: ",'0','L'); 
		$this->SetFont('Arial','',10);
		$this->SetXY(65,70);$this->MultiCell(45,8,"".date('d-m-Y',$paciente["fecreg"]->sec),'0','L');

		$this->SetFont('Arial','B',10);
		$this->SetXY(20,86);$this->MultiCell(45,8,"DOMICILIO: ",'0','L'); 
		$this->SetFont('Arial','',10);
		$this->SetXY(45,86);$this->MultiCell(120,8,"".$paciente["paciente"]['domicilios'][0]['direccion'],'1','L');
		$this->SetFont('Arial','B',10);
		$this->SetXY(170,86);$this->MultiCell(20,8,"FECHA: ",'0','L'); 
		$this->SetFont('Arial','',10);
		$this->SetXY(190,86);$this->MultiCell(20,8,"".date("d/m/Y"),'0','L');
		$this->SetFont('Arial','B',10);
		$this->SetXY(20,105);$this->MultiCell(45,8,"DOMICILIO: ",'0','L'); 
		$this->SetFont('Arial','',10);
		$this->SetXY(45,105);$this->MultiCell(120,8,"",'1','L');
		$this->SetFont('Arial','B',10);
		$this->SetXY(170,105);$this->MultiCell(20,8,"FECHA: ",'0','L'); 
		$this->SetFont('Arial','',10);
		$this->SetXY(190,105);$this->MultiCell(20,8,"..................",'0','L');
		$this->SetFont('Arial','B',10);
		$this->SetXY(20,118);$this->MultiCell(45,8,"DOMICILIO: ",'0','L'); 
		$this->SetFont('Arial','',10);
		$this->SetXY(45,118);$this->MultiCell(120,8,"",'1','L');
		$this->SetFont('Arial','B',10);
		$this->SetXY(170,118);$this->MultiCell(20,8,"FECHA: ",'0','L'); 
		$this->SetFont('Arial','',10);
		$this->SetXY(190,118);$this->MultiCell(20,8,"..................",'0','L');


		$this->AddPage();
		$y= 5;
		
		$this->SetFont('Arial','B',6);
		$this->SetXY(15,33);$this->MultiCell(21,12,"FECHA CITA",'1','C'); 
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
		$this->SetXY(15,117);$this->MultiCell(21,6,"",'1','C'); 
		$this->SetXY(15,123);$this->MultiCell(21,6,"",'1','C'); 
		$this->SetXY(15,129);$this->MultiCell(21,6,"",'1','C'); 
		$this->SetXY(15,135);$this->MultiCell(21,6,"",'1','C'); 

		$this->SetXY(36,33);$this->MultiCell(21,12,"HORA",'1','C'); 
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
		$this->SetXY(36,117);$this->MultiCell(21,6,"",'1','C'); 
		$this->SetXY(36,123);$this->MultiCell(21,6,"",'1','C'); 
		$this->SetXY(36,129);$this->MultiCell(21,6,"",'1','C'); 
		$this->SetXY(36,135);$this->MultiCell(21,6,"",'1','C'); 
		$this->SetFont('Arial','B',5);
		
		$this->SetXY(57,33);$this->MultiCell(40,6,"CONSULTA",'1','C');
		$this->SetXY(57,39);$this->MultiCell(10,6,"1",'1','C');
		$this->SetXY(67,39);$this->MultiCell(10,6,"2",'1','C');
		$this->SetXY(77,39);$this->MultiCell(10,6,"3",'1','C');
		$this->SetXY(87,39);$this->MultiCell(10,6,"4",'1','C');
		$this->SetXY(57,45);$this->MultiCell(10,96,"",'1','C');
		$this->SetXY(67,45);$this->MultiCell(10,96,"",'1','C');
		$this->SetXY(77,45);$this->MultiCell(10,96,"",'1','C');
		$this->SetXY(87,45);$this->MultiCell(10,96,"",'1','C');
		$this->SetXY(57,45);$this->MultiCell(40,6,"",'1','C'); 
		$this->SetXY(57,51);$this->MultiCell(40,6,"",'1','C'); 
		$this->SetXY(57,57);$this->MultiCell(40,6,"",'1','C'); 
		$this->SetXY(57,63);$this->MultiCell(40,6,"",'1','C'); 
		$this->SetXY(57,69);$this->MultiCell(40,6,"",'1','C'); 
		$this->SetXY(57,75);$this->MultiCell(40,6,"",'1','C'); 
		$this->SetXY(57,81);$this->MultiCell(40,6,"",'1','C'); 
		$this->SetXY(57,87);$this->MultiCell(40,6,"",'1','C'); 
		$this->SetXY(57,93);$this->MultiCell(40,6,"",'1','C'); 
		$this->SetXY(57,99);$this->MultiCell(40,6,"",'1','C'); 
		$this->SetXY(57,105);$this->MultiCell(40,6,"",'1','C'); 
		$this->SetXY(57,111);$this->MultiCell(40,6,"",'1','C'); 
		$this->SetXY(57,117);$this->MultiCell(40,6,"",'1','C'); 
		$this->SetXY(57,123);$this->MultiCell(40,6,"",'1','C'); 
		$this->SetXY(57,129);$this->MultiCell(40,6,"",'1','C'); 
		$this->SetXY(57,135);$this->MultiCell(40,6,"",'1','C'); 


		$this->SetFont('Arial','B',6);
		$this->SetXY(97,33);$this->MultiCell(21,12,"FECHA CITA",'1','C'); 

		//$this->SetXY(97,39);$this->MultiCell(21,6,"",'1','C'); 
		$this->SetXY(97,45);$this->MultiCell(21,6,"",'1','C'); 
		$this->SetXY(97,51);$this->MultiCell(21,6,"",'1','C'); 
		$this->SetXY(97,57);$this->MultiCell(21,6,"",'1','C'); 
		$this->SetXY(97,63);$this->MultiCell(21,6,"",'1','C'); 
		$this->SetXY(97,69);$this->MultiCell(21,6,"",'1','C'); 
		$this->SetXY(97,75);$this->MultiCell(21,6,"",'1','C'); 
		$this->SetXY(97,81);$this->MultiCell(21,6,"",'1','C'); 
		$this->SetXY(97,87);$this->MultiCell(21,6,"",'1','C'); 
		$this->SetXY(97,93);$this->MultiCell(21,6,"",'1','C'); 
		$this->SetXY(97,99);$this->MultiCell(21,6,"",'1','C'); 
		$this->SetXY(97,105);$this->MultiCell(21,6,"",'1','C'); 
		$this->SetXY(97,111);$this->MultiCell(21,6,"",'1','C'); 
		$this->SetXY(97,117);$this->MultiCell(21,6,"",'1','C'); 
		$this->SetXY(97,123);$this->MultiCell(21,6,"",'1','C'); 
		$this->SetXY(97,129);$this->MultiCell(21,6,"",'1','C'); 
		$this->SetXY(97,135);$this->MultiCell(21,6,"",'1','C'); 

		$this->SetXY(118,33);$this->MultiCell(21,12,"HORA",'1','C'); 
		$this->SetXY(118,45);$this->MultiCell(21,6,"",'1','C'); 
		$this->SetXY(118,51);$this->MultiCell(21,6,"",'1','C'); 
		$this->SetXY(118,57);$this->MultiCell(21,6,"",'1','C'); 
		$this->SetXY(118,63);$this->MultiCell(21,6,"",'1','C'); 
		$this->SetXY(118,69);$this->MultiCell(21,6,"",'1','C'); 
		$this->SetXY(118,75);$this->MultiCell(21,6,"",'1','C'); 
		$this->SetXY(118,81);$this->MultiCell(21,6,"",'1','C'); 
		$this->SetXY(118,87);$this->MultiCell(21,6,"",'1','C'); 
		$this->SetXY(118,93);$this->MultiCell(21,6,"",'1','C'); 
		$this->SetXY(118,99);$this->MultiCell(21,6,"",'1','C'); 
		$this->SetXY(118,105);$this->MultiCell(21,6,"",'1','C'); 
		$this->SetXY(118,111);$this->MultiCell(21,6,"",'1','C'); 
		$this->SetXY(118,117);$this->MultiCell(21,6,"",'1','C'); 
		$this->SetXY(118,123);$this->MultiCell(21,6,"",'1','C'); 
		$this->SetXY(118,129);$this->MultiCell(21,6,"",'1','C'); 
		$this->SetXY(118,135);$this->MultiCell(21,6,"",'1','C'); 
		$this->SetFont('Arial','B',5);
		$this->SetXY(139,33);$this->MultiCell(40,6,"CONSULTA",'1','C');
		$this->SetXY(139,39);$this->MultiCell(10,6,"1",'1','C');
		$this->SetXY(149,39);$this->MultiCell(10,6,"2",'1','C');
		$this->SetXY(159,39);$this->MultiCell(10,6,"3",'1','C');
		$this->SetXY(169,39);$this->MultiCell(10,6,"4",'1','C');

		$this->SetXY(139,45);$this->MultiCell(10,96,"",'1','C');
		$this->SetXY(149,45);$this->MultiCell(10,96,"",'1','C');
		$this->SetXY(159,45);$this->MultiCell(10,96,"",'1','C');
		$this->SetXY(169,45);$this->MultiCell(10,96,"",'1','C');

		$this->SetXY(139,45);$this->MultiCell(40,6,"",'1','C'); 
		$this->SetXY(139,51);$this->MultiCell(40,6,"",'1','C'); 
		$this->SetXY(139,57);$this->MultiCell(40,6,"",'1','C'); 
		$this->SetXY(139,63);$this->MultiCell(40,6,"",'1','C'); 
		$this->SetXY(139,69);$this->MultiCell(40,6,"",'1','C'); 
		$this->SetXY(139,75);$this->MultiCell(40,6,"",'1','C'); 
		$this->SetXY(139,81);$this->MultiCell(40,6,"",'1','C'); 
		$this->SetXY(139,87);$this->MultiCell(40,6,"",'1','C'); 
		$this->SetXY(139,93);$this->MultiCell(40,6,"",'1','C'); 
		$this->SetXY(139,99);$this->MultiCell(40,6,"",'1','C'); 
		$this->SetXY(139,105);$this->MultiCell(40,6,"",'1','C'); 
		$this->SetXY(139,111);$this->MultiCell(40,6,"",'1','C'); 
		$this->SetXY(139,117);$this->MultiCell(40,6,"",'1','C'); 
		$this->SetXY(139,123);$this->MultiCell(40,6,"",'1','C'); 
		$this->SetXY(139,129);$this->MultiCell(40,6,"",'1','C'); 
		$this->SetXY(139,135);$this->MultiCell(40,6,"",'1','C');

		$this->SetXY(179,33);$this->MultiCell(21,12,"OBSERVACION",'1','C'); 
		$this->SetXY(179,45);$this->MultiCell(21,6,"",'1','C'); 
		$this->SetXY(179,51);$this->MultiCell(21,6,"",'1','C'); 
		$this->SetXY(179,57);$this->MultiCell(21,6,"",'1','C'); 
		$this->SetXY(179,63);$this->MultiCell(21,6,"",'1','C'); 
		$this->SetXY(179,69);$this->MultiCell(21,6,"",'1','C'); 
		$this->SetXY(179,75);$this->MultiCell(21,6,"",'1','C'); 
		$this->SetXY(179,81);$this->MultiCell(21,6,"",'1','C'); 
		$this->SetXY(179,87);$this->MultiCell(21,6,"",'1','C'); 
		$this->SetXY(179,93);$this->MultiCell(21,6,"",'1','C'); 
		$this->SetXY(179,99);$this->MultiCell(21,6,"",'1','C'); 
		$this->SetXY(179,105);$this->MultiCell(21,6,"",'1','C'); 
		$this->SetXY(179,111);$this->MultiCell(21,6,"",'1','C'); 
		$this->SetXY(179,117);$this->MultiCell(21,6,"",'1','C'); 
		$this->SetXY(179,123);$this->MultiCell(21,6,"",'1','C'); 
		$this->SetXY(179,129);$this->MultiCell(21,6,"",'1','C'); 
		$this->SetXY(179,135);$this->MultiCell(21,6,"",'1','C'); 

	
	}
}
//$pdf=new repo('L','mm','A4');
$pdf = new repo('L','mm',array(155,220));
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