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
		$this->SetXY(220,5);$this->MultiCell(60,5,date("d/m/Y")." Página: ".$this->PageNo()."/{nb}",'0','R');
		$this->SetFont('Arial','',9);
		$this->SetXY(10,15);$this->MultiCell(60,5,"Centro de Salud Mental",'0','C');
		$this->SetXY(10,20);$this->MultiCell(60,5,"'Moises Heresi'",'0','C');
		$this->SetFont('Arial','B',10);
		$this->SetXY(100,35);$this->MultiCell(100,5,"RESUMEN DE PACIENTES BENEFICIARIOS",'0','C');
		
	}	
	function Publicar($hospi){
		
		$categorias= array(
			"1"=>"S/E",
			"2"=>"PP",
			"3"=>"P",
			"4"=>"A",
			"5"=>"B",
			"6"=>"C",
			"7"=>"E",
			"8"=>"D"

		);
		//$this->SetXY(135,35);$this->MultiCell(22,5,"".date('d-m-Y',$hospi[0]["fecpag"]->sec),'0','C');
		$x=5;
		$y=25;
		$y_ini = $y;
		$y= $y+30;
		$total = 0;
		$suma = 0;
		$page_b = 275;
		//$y=$y+40;
		$yini = $y;

		
		$this->SetFont('Arial','B',8);
		$this->SetXY(20,$y);$this->MultiCell(20,9,"AÑO\MESES",'1','C');

		$this->SetXY(40,$y);$this->MultiCell(20,9,"ENERO",'1','C');
		$this->SetXY(60,$y);$this->MultiCell(20,9,"FEBRERO",'1','C');
		$this->SetXY(80,$y);$this->MultiCell(20,9,"MARZO",'1','C');
		$this->SetXY(100,$y);$this->MultiCell(20,9,"ABRIL",'1','C');
		$this->SetXY(120,$y);$this->MultiCell(20,9,"MAYO",'1','C');
		$this->SetXY(140,$y);$this->MultiCell(20,9,"JUNIO",'1','C');
		$this->SetXY(160,$y);$this->MultiCell(20,9,"JULIO",'1','C');
		$this->SetXY(180,$y);$this->MultiCell(20,9,"AGOSTO",'1','C');
		$this->SetXY(200,$y);$this->MultiCell(20,9,"SETIEMBRE",'1','C');
		$this->SetXY(220,$y);$this->MultiCell(20,9,"OCTUBRE",'1','C');
		$this->SetXY(240,$y);$this->MultiCell(20,9,"NOVIEMBRE",'1','C');
		$this->SetXY(260,$y);$this->MultiCell(20,9,"DICIEMBRE",'1','C');

		$y= $y+9;
				$this->SetFont('Arial','B',8);

		$this->SetXY(20,$y);$this->MultiCell(20,9,"2012",'1','C');
				$this->SetFont('Arial','',8);

		$this->SetXY(40,$y);$this->MultiCell(20,9,"0",'1','C');
		$this->SetXY(60,$y);$this->MultiCell(20,9,"0",'1','C');
		$this->SetXY(80,$y);$this->MultiCell(20,9,"0",'1','C');
		$this->SetXY(100,$y);$this->MultiCell(20,9,"0",'1','C');
		$this->SetXY(120,$y);$this->MultiCell(20,9,"0",'1','C');
		$this->SetXY(140,$y);$this->MultiCell(20,9,"0",'1','C');
		$this->SetXY(160,$y);$this->MultiCell(20,9,"0",'1','C');
		$this->SetXY(180,$y);$this->MultiCell(20,9,"0",'1','C');
		$this->SetXY(200,$y);$this->MultiCell(20,9,"0",'1','C');
		$this->SetXY(220,$y);$this->MultiCell(20,9,"0",'1','C');
		$this->SetXY(240,$y);$this->MultiCell(20,9,"0",'1','C');
		$this->SetXY(260,$y);$this->MultiCell(20,9,"0",'1','C');
		$this->SetFont('Arial','B',8);
		$this->SetXY(280,$y);$this->MultiCell(15,9,"0",'1','C');
		$y= $y+9;
				$this->SetFont('Arial','B',8);

		$this->SetXY(20,$y);$this->MultiCell(20,9,"2013",'1','C');
				$this->SetFont('Arial','',8);

		$this->SetXY(40,$y);$this->MultiCell(20,9,"0",'1','C');
		$this->SetXY(60,$y);$this->MultiCell(20,9,"0",'1','C');
		$this->SetXY(80,$y);$this->MultiCell(20,9,"0",'1','C');
		$this->SetXY(100,$y);$this->MultiCell(20,9,"0",'1','C');
		$this->SetXY(120,$y);$this->MultiCell(20,9,"0",'1','C');
		$this->SetXY(140,$y);$this->MultiCell(20,9,"0",'1','C');
		$this->SetXY(160,$y);$this->MultiCell(20,9,"0",'1','C');
		$this->SetXY(180,$y);$this->MultiCell(20,9,"0",'1','C');
		$this->SetXY(200,$y);$this->MultiCell(20,9,"0",'1','C');
		$this->SetXY(220,$y);$this->MultiCell(20,9,"0",'1','C');
		$this->SetXY(240,$y);$this->MultiCell(20,9,"0",'1','C');
		$this->SetXY(260,$y);$this->MultiCell(20,9,"1",'1','C');
		$this->SetFont('Arial','B',8);
		$this->SetXY(280,$y);$this->MultiCell(15,9,"1",'1','C');
		$y= $y+9;
				$this->SetFont('Arial','B',8);

		$this->SetXY(20,$y);$this->MultiCell(20,9,"2014",'1','C');
				$this->SetFont('Arial','',8);

		$this->SetXY(40,$y);$this->MultiCell(20,9,"3",'1','C');
		$this->SetXY(60,$y);$this->MultiCell(20,9,"0",'1','C');
		$this->SetXY(80,$y);$this->MultiCell(20,9,"6",'1','C');
		$this->SetXY(100,$y);$this->MultiCell(20,9,"26",'1','C');
		$this->SetXY(120,$y);$this->MultiCell(20,9,"24",'1','C');
		$this->SetXY(140,$y);$this->MultiCell(20,9,"23",'1','C');
		$this->SetXY(160,$y);$this->MultiCell(20,9,"32",'1','C');
		$this->SetXY(180,$y);$this->MultiCell(20,9,"27",'1','C');
		$this->SetXY(200,$y);$this->MultiCell(20,9,"33",'1','C');
		$this->SetXY(220,$y);$this->MultiCell(20,9,"24",'1','C');
		$this->SetXY(240,$y);$this->MultiCell(20,9,"26",'1','C');
		$this->SetXY(260,$y);$this->MultiCell(20,9,"21",'1','C');
		$this->SetFont('Arial','B',8);
		$this->SetXY(280,$y);$this->MultiCell(15,9,"245",'1','C');
		$y= $y+9;
				$this->SetFont('Arial','B',8);

		$this->SetXY(20,$y);$this->MultiCell(20,9,"2015",'1','C');
				$this->SetFont('Arial','',8);

		$this->SetXY(40,$y);$this->MultiCell(20,9,"29",'1','C');
		$this->SetXY(60,$y);$this->MultiCell(20,9,"17",'1','C');
		$this->SetXY(80,$y);$this->MultiCell(20,9,"24",'1','C');
		$this->SetXY(100,$y);$this->MultiCell(20,9,"31",'1','C');
		$this->SetXY(120,$y);$this->MultiCell(20,9,"40",'1','C');
		$this->SetXY(140,$y);$this->MultiCell(20,9,"44",'1','C');
		$this->SetXY(160,$y);$this->MultiCell(20,9,"29",'1','C');
		$this->SetXY(180,$y);$this->MultiCell(20,9,"35",'1','C');
		$this->SetXY(200,$y);$this->MultiCell(20,9,"28",'1','C');
		$this->SetXY(220,$y);$this->MultiCell(20,9,"37",'1','C');
		$this->SetXY(240,$y);$this->MultiCell(20,9,"28",'1','C');
		$this->SetXY(260,$y);$this->MultiCell(20,9,"24",'1','C');
		$this->SetFont('Arial','B',8);
		$this->SetXY(280,$y);$this->MultiCell(15,9,"366",'1','C');
		$y= $y+9;
				$this->SetFont('Arial','B',8);

		$this->SetXY(20,$y);$this->MultiCell(20,9,"2016",'1','C');
				$this->SetFont('Arial','',8);

		$this->SetXY(40,$y);$this->MultiCell(20,9,"27",'1','C');
		$this->SetXY(60,$y);$this->MultiCell(20,9,"27",'1','C');
		$this->SetXY(80,$y);$this->MultiCell(20,9,"54",'1','C');
		$this->SetXY(100,$y);$this->MultiCell(20,9,"55",'1','C');
		$this->SetXY(120,$y);$this->MultiCell(20,9,"44",'1','C');
		$this->SetXY(140,$y);$this->MultiCell(20,9,"16",'1','C');
		$this->SetXY(160,$y);$this->MultiCell(20,9,"7",'1','C');
		$this->SetXY(180,$y);$this->MultiCell(20,9,"9501",'1','C');
		$this->SetXY(200,$y);$this->MultiCell(20,9,"29",'1','C');
		$this->SetXY(220,$y);$this->MultiCell(20,9,"12",'1','C');
		$this->SetXY(240,$y);$this->MultiCell(20,9,"19",'1','C');
		$this->SetXY(260,$y);$this->MultiCell(20,9,"22",'1','C');
		$this->SetFont('Arial','B',8);
		$this->SetXY(280,$y);$this->MultiCell(15,9,"9813",'1','C');
		$y= $y+9;	
		$this->SetXY(280,$y);$this->MultiCell(15,9,"10425",'1','C');
		$y= $y+10;			
		
		//$y= $y+18;			
		$this->SetXY(40,$y);$this->MultiCell(66,9,"Diagnostico Medico Frecuentes",'1','C');
		$y= $y+9;	
		$this->SetXY(40,$y);$this->MultiCell(22,9,"1",'1','C');
		$this->SetXY(62,$y);$this->MultiCell(22,9,"F20",'1','C');
		$this->SetXY(84,$y);$this->MultiCell(22,9,"Esquizofrenia",'1','C');
		$y= $y+9;	
		$this->SetXY(40,$y);$this->MultiCell(22,9,"2",'1','C');
		$this->SetXY(62,$y);$this->MultiCell(22,9,"F32",'1','C');
		$this->SetXY(84,$y);$this->MultiCell(22,9,"Depresion",'1','C');
		$y= $y+9;	
		$this->SetXY(40,$y);$this->MultiCell(22,9,"3",'1','C');
		$this->SetXY(62,$y);$this->MultiCell(22,9,"F41",'1','C');
		$this->SetXY(84,$y);$this->MultiCell(22,9,"Ansiedad",'1','C');
		$y= $y+18;	
		$this->SetXY(20,$y);$this->MultiCell(120,9,"*Listado de pacientes hospitalizados durante el los años 2012-2013-2014-2015-2016",'0','C');
		
		
		for($i = 0; $i<count($hospi);$i++){
			/*
			if($y>240){
						$this->AddPage();
						$y = $yini;
						
						//$this->SetXY(90,$y);$this->MultiCell(20,9,"categoria",'1','C');
						$y= $y+9;	
						
					}
				//$this->SetXY(75,$y);$this->MultiCell(60,9,"".$hospi[$i]["paciente"]["appat"]. ' '.$hospi[$i]["paciente"]["apmat"].' '.$hospi[$i]["paciente"]["nomb"],'1','C');
				
				//$this->SetXY(135,$y);$this->MultiCell(60,9,"".$hospi[$i]["domi"],'1','C');
				$y= $y+9;
			
		*/							
		}


		







}

	
	 
}

$pdf=new repo('L','mm','A4');
$pdf->AliasNbPages();
$pdf->SetMargins(10,10,0); 
$pdf->SetTitle("reporte");
$pdf->SetAutoPageBreak(false,0); 
$pdf->Open();
$pdf->AddPage();
$pdf->Publicar($hospi);
$pdf->SetLeftMargin(25);
//header('Content-Type: application/pdf; charset=utf-8');
$pdf->Output();

?>
