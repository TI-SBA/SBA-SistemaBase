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

		
		
	}	




	function Publicar($diario){
		
		$x=5;
		$y=25;
		$y_ini = $y;
		$page_b = 275;
		
//CABECERA
		$y=$y+25;
		$this->SetFont('Arial','B',10);
		$this->SetXY(5,30);$this->MultiCell(200,5,	"FICHA DE REPORTE MENSUAL DE SERVICIOS - SOCIEDAD DE BENEFICIENCIA DE AREQUIPA - REPORTE QUINCENAL DE SERVICIOS - SALUD MENTAL",'0','C');
		$this->SetFont('Arial','B',8);
		//PRIMERA COLUMNA
		$this->SetXY(20,$y);$this->MultiCell(42,9,"NUMERO DE BENEFCIARIOS",'1','C');
		$this->SetXY(20,59);$this->MultiCell(42,9,"APOYO EN SALUD",'1','C');
		$this->SetXY(20,68);$this->MultiCell(42,9,"BENEFICIARIO INDIGENTE",'1','C');
		$this->SetXY(20,77);$this->MultiCell(42,9,"BENEFICIARIO PAGANTE",'1','C');
		$this->SetXY(20,86);$this->MultiCell(42,9,"TOTAL",'1','C');
		//SEGUNDA COLUMNA
		$this->SetXY(62,$y);$this->MultiCell(22,9,"SEGUN SEXO",'1','C');
		$this->SetXY(62,59);$this->MultiCell(11,9,"M",'1','C');
		$this->SetXY(73,59);$this->MultiCell(11,9,"F",'1','C');
		//TERCERA COLUMNA
		$this->SetXY(84,$y);$this->MultiCell(12,9,"TOTAL",'1','C');
		$this->SetXY(84,59);$this->MultiCell(12,9,"-------",'1','C');
		//CUARTA COLUMNA
		$this->SetXY(96,$y);$this->MultiCell(80,9,"SEGUN SU EDAD",'1','C');
		$this->SetXY(96,59);$this->MultiCell(10,9,"0-5",'1','C');
		$this->SetXY(106,59);$this->MultiCell(10,9,"6-10",'1','C');
		$this->SetXY(116,59);$this->MultiCell(10,9,"11-15",'1','C');
		$this->SetXY(126,59);$this->MultiCell(10,9,"16-20",'1','C');
		$this->SetXY(136,59);$this->MultiCell(10,9,"21-30",'1','C');
		$this->SetXY(146,59);$this->MultiCell(10,9,"31-50",'1','C');
		$this->SetXY(156,59);$this->MultiCell(10,9,"51-60",'1','C');
		$this->SetXY(166,59);$this->MultiCell(10,9,"61-99",'1','C');


		$this->SetFont('Arial','',9);
		$prueba = 0;
		for($i = 0;$i<count($diario);$i++){
			if($diario[$i]['modulo'] == 'AD' && $diario[$i]['categoria'] == '8' ){
				$prueba++;
			}
				
		}
		print_r($prueba);
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