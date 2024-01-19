<?php
global $f;
$f->library('pdf');

class repo extends FPDF
{
	function Header(){
		//$this->Image(IndexPath.DS.'templates/ts/comprobante.gif',10,10,190,275);	
		$y=10;
		$this->SetFont('Arial','B',9);
		$this->SetXY(10,5);$this->MultiCell(80,5,"SOCIEDAD DE BENEFICENCIA PUBLICA DE AREQUIPA",'0','C');		
		$this->SetXY(10,5);$this->MultiCell(190,5,date("d/m/Y")." Página: ".$this->PageNo()."/{nb}",'0','R');
		$this->SetFont('Arial','',9);
		$this->SetXY(10,15);$this->MultiCell(80,5,"Centro de Salud Mental",'0','C');
		$this->SetXY(10,20);$this->MultiCell(80,5,"'Moises Heresi'",'0','C');
		$this->SetFont('Arial','B',13);
		$this->SetXY(5,30);$this->MultiCell(200,5,"Reporte de Hospitalizaciones de Moises Heresi",'0','C');
		
	}	
	function Publicar($hospi){
		$x=5;
		$y=25;
		$i_ini=$y;
		$pag_b = 275;
		$y=$y+25;

		$this->SetFont('Arial','B',8);
		$p_nuevos =0;
		$p_continuadores = 0;
		$p_indigentes = 0;
		$p_nuevo_completa = 0;
		$p_nuevo_parcial = 0;
		$nuevo_completa_intermedio = 0;
		$nuevo_completa_intensivo = 0;

		$p_continuador_completa = 0;
		$p_continuador_parcial = 0;
		$continuador_completa_intermedio = 0;
		$continuador_completa_intensivo = 0;

		$p_indigente_completa = 0;
		$p_indigente_parcial = 0;
		$indigente_completa_intermedio = 0;
		$indigente_completa_intensivo = 0;
		

		
		for ($i=0; $i <count($hospi); $i++) { 
			//print_r($hospi[$i]['categoria']);
			if($hospi[$i]['categoria'] == '10'){
				$p_nuevos++;
			}
			if($hospi[$i]['categoria'] == '10' && $hospi[$i]['tipo_hosp'] == 'C'){
				$p_nuevo_completa++;
			}
			if($hospi[$i]['categoria'] == '10' && $hospi[$i]['tipo_hosp'] == 'P'){
				$p_nuevo_parcial++;
			}
			if($hospi[$i]['categoria'] == '10' && $hospi[$i]['tipo_hosp'] == 'C' && $hospi[$i]['pabe'] == 'Intensivo'){
				$nuevo_completa_intensivo++;
			}
			if($hospi[$i]['categoria'] == '10' && $hospi[$i]['tipo_hosp'] == 'C' && $hospi[$i]['pabe'] == 'Intermedio'){
				$nuevo_completa_intermedio++;
			}
			/****************************************************/
			if($hospi[$i]['categoria'] == '11'){
				$p_continuadores++;
			}
			if($hospi[$i]['categoria'] == '11' && $hospi[$i]['tipo_hosp'] == 'C'){
				$p_continuador_completa++;
			}
			if($hospi[$i]['categoria'] == '11' && $hospi[$i]['tipo_hosp'] == 'P'){
				$p_continuador_parcial++;
			}
			if($hospi[$i]['categoria'] == '11' && $hospi[$i]['tipo_hosp'] == 'C' && $hospi[$i]['pabe'] == 'Intensivo'){
				$continuador_completa_intensivo++;
			}
			if($hospi[$i]['categoria'] == '11' && $hospi[$i]['tipo_hosp'] == 'C' && $hospi[$i]['pabe'] == 'Intermedio'){
				$continuador_completa_intermedio++;
			}
			/****************************************************/
			if($hospi[$i]['categoria'] == '8'){
				$p_indigentes++;
			}
			if($hospi[$i]['categoria'] == '8' && $hospi[$i]['tipo_hosp'] == 'C'){
				$p_indigente_completa++;
			}
			if($hospi[$i]['categoria'] == '8' && $hospi[$i]['tipo_hosp'] == 'P'){
				$p_indigente_parcial++;
			}
			if($hospi[$i]['categoria'] == '8' && $hospi[$i]['tipo_hosp'] == 'C' && $hospi[$i]['pabe'] == 'Intensivo'){
				$indigente_completa_intensivo++;
			}
			if($hospi[$i]['categoria'] == '8' && $hospi[$i]['tipo_hosp'] == 'C' && $hospi[$i]['pabe'] == 'Intermedio'){
				$indigente_completa_intermedio++;
			}		
		}
		print_r($hospi[0]['importe']);	
		die();
		$total_nuevo = 0;
		$total_nuevo = $p_nuevo_completa + $p_nuevo_parcial;
		$total_nuevo_pable = 0;
		$total_nuevo_pable = $nuevo_completa_intermedio + $nuevo_completa_intensivo;
		$this->SetXY(40,$y);$this->MultiCell(80,5,"PACIENTES CATEGORIA: NUEVOS",'1','C');
		$this->SetXY(120,$y);$this->MultiCell(20,5,"CANTIDAD",'1','C');
		
		$y=$y+5;
		$this->SetXY(40,$y);$this->MultiCell(100,5,"TIPO DE HOSPITALIZACION",'1','C');
		$y=$y+5;
		$this->SetXY(40,$y);$this->MultiCell(80,5,"COMPLETA",'1','C');
		$this->SetXY(120,$y);$this->MultiCell(20,5,"".$p_nuevo_completa,'1','C');
		
		$y=$y+5;
		$this->SetXY(40,$y);$this->MultiCell(80,5,"PARCIAL",'1','C');
		$this->SetXY(120,$y);$this->MultiCell(20,5,"".$p_nuevo_parcial,'1','C');
		
		$y=$y+5;
		$this->SetXY(40,$y);$this->MultiCell(80,5,"TOTAL",'1','C');
		$this->SetXY(120,$y);$this->MultiCell(20,5,$total_nuevo,'1','C');
		
		$y=$y+5;
		$this->SetXY(40,$y);$this->MultiCell(100,5,"PABELLON",'1','C');
		$y=$y+5;
		$this->SetXY(40,$y);$this->MultiCell(80,5,"INTERMEDIO",'1','C');
		$this->SetXY(120,$y);$this->MultiCell(20,5,"".$nuevo_completa_intermedio,'1','C');
		
		$y=$y+5;
		$this->SetXY(40,$y);$this->MultiCell(80,5,"INTENSIVO",'1','C');
		$this->SetXY(120,$y);$this->MultiCell(20,5,"".$nuevo_completa_intensivo,'1','C');
		
		$y=$y+5;
		$this->SetXY(40,$y);$this->MultiCell(80,5,"TOTAL",'1','C');
		$this->SetXY(120,$y);$this->MultiCell(20,5,$total_nuevo_pable,'1','C');
		

	}

}

$pdf=new repo('P','mm','A4');
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



