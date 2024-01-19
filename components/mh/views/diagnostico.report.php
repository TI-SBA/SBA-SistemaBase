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
		$this->SetFont('Arial','B',10);
		$this->SetXY(5,30);$this->MultiCell(200,5,	"ESTADISTICAS DE PSICOLOGIA Y PSIQUIATRIA - 2017",'0','C');
		$y=30;
		//////////////////////////////////////////////////////////////////
		$pisqui_n_mh = 0;
		$pisqui_c_mh = 0;
		$pisqui_i_mh = 0;
		$pisco_n_mh = 0;
		$pisco_c_mh = 0;
		$pisco_i_mh = 0;
		/*------------*/
		$pisqui_n_ad = 0;
		$pisqui_c_ad = 0;
		$pisqui_i_ad = 0;
		$pisco_n_ad = 0;
		$pisco_c_ad = 0;
		$pisco_i_ad = 0;
		/*
		for($i = 0;$i<count($diario);$i++){
			for($j = 0;$j<count($diario[$i]['consulta']);$j++){
				if($diario[$i]['modulo'] == 'AD' && floatval($diario[$i]["consulta"][$j]['cate']) == 10 ){
					$pisco_i_ad++;

				}

				
			
			}
		}
		print_r($pisco_i_ad);
		//print_r($pisco_n_ad);
		//print_r($pisco_c_ad);
		//print_r($pisqui_i_ad);
		*/
		//////////////////////////////////////////////////////////////////
		$y=$y+10;
		$this->SetFont('Arial','B',8);
		$this->SetXY(5,$y);$this->MultiCell(200,5,	"PACIENTES: SALUD MENTAL",'0','C');
		$y=$y+10;
		$this->SetFont('Arial','B',8);
		$this->SetXY(5,$y);$this->MultiCell(50,5,	"PSIQUIATRIA",'0','C');
		$y=$y+7;
		$this->SetFont('Arial','',7);
		$this->SetXY(25,$y);$this->MultiCell(50,5,	"NUEVOS: 1288",'0','L');
		$y=$y+5;
		$this->SetXY(25,$y);$this->MultiCell(50,5,	"CONTINUADORES: 4033",'0','L');
		$y=$y+5;
		$this->SetXY(25,$y);$this->MultiCell(50,5,	"INDIGENTES: 649",'0','L');
		$y=$y+5;
		$this->SetFont('Arial','B',8);
		$this->SetXY(25,$y);$this->MultiCell(50,5,	"TOTAL: 5970",'0','L');
		$y=$y+10;
		$this->SetFont('Arial','B',8);
		$this->SetXY(5,$y);$this->MultiCell(50,5,	"PSICOLOGIA",'0','C');
		$y=$y+7;
		$this->SetFont('Arial','',7);
		$this->SetXY(25,$y);$this->MultiCell(50,5,	"NUEVOS: 121",'0','L');
		$y=$y+5;
		$this->SetXY(25,$y);$this->MultiCell(50,5,	"CONTINUADORES: 135",'0','L');
		$y=$y+5;
		$this->SetXY(25,$y);$this->MultiCell(50,5,	"INDIGENTES: 32",'0','L');
		$y=$y+5;
		$this->SetFont('Arial','B',8);
		$this->SetXY(25,$y);$this->MultiCell(50,5,	"TOTAL: 288",'0','L');
		$y=$y+10;
		$this->SetFont('Arial','B',8);
		$this->SetXY(5,$y);$this->MultiCell(200,5,	"PACIENTES: ADICCIONES",'0','C');
		$y=$y+10;
		$this->SetFont('Arial','B',8);
		$this->SetXY(5,$y);$this->MultiCell(50,5,	"PSIQUIATRIA",'0','C');
		$y=$y+7;
		$this->SetFont('Arial','',7);
		$this->SetXY(25,$y);$this->MultiCell(50,5,	"NUEVOS: 32",'0','L');
		$y=$y+5;
		$this->SetXY(25,$y);$this->MultiCell(50,5,	"CONTINUADORES: 0",'0','L');
		$y=$y+5;
		$this->SetXY(25,$y);$this->MultiCell(50,5,	"INDIGENTES: 0",'0','L');
		$y=$y+5;
		$this->SetFont('Arial','B',8);
		$this->SetXY(25,$y);$this->MultiCell(50,5,	"TOTAL: 32",'0','L');
		$y=$y+10;
		$this->SetFont('Arial','B',8);
		$this->SetXY(5,$y);$this->MultiCell(50,5,	"PSICOLOGIA",'0','C');
		$y=$y+7;
		$this->SetFont('Arial','',7);
		$this->SetXY(25,$y);$this->MultiCell(50,5,	"NUEVOS: 18",'0','L');
		$y=$y+5;
		$this->SetXY(25,$y);$this->MultiCell(50,5,	"CONTINUADORES: 0",'0','L');
		$y=$y+5;
		$this->SetXY(25,$y);$this->MultiCell(50,5,	"INDIGENTES: 0",'0','L');
		$y=$y+5;
		$this->SetFont('Arial','B',8);
		$this->SetXY(25,$y);$this->MultiCell(50,5,	"TOTAL: 18",'0','L');
		/*-------------------------------------------------------------------------------------*/
		$this->SetXY(5,$y);$this->MultiCell(200,5,	"TRABAJO SOCIAL",'0','C');
		$y=$y+10;
		$this->SetFont('Arial','B',8);
		$this->SetXY(5,$y);$this->MultiCell(50,5,	"SALUD MENTAL",'0','C');
		$y=$y+7;
		$this->SetFont('Arial','',7);
		$this->SetXY(25,$y);$this->MultiCell(50,5,	"NUEVOS: 747",'0','L');
		$y=$y+5;
		$this->SetXY(25,$y);$this->MultiCell(50,5,	"CONTINUADORES: 196",'0','L');
		$y=$y+5;
		$this->SetXY(25,$y);$this->MultiCell(50,5,	"INDIGENTES: 15",'0','L');
		$y=$y+5;
		$this->SetFont('Arial','B',8);
		$this->SetXY(25,$y);$this->MultiCell(50,5,	"TOTAL: 958",'0','L');
		$y=$y+10;
		$this->SetFont('Arial','B',8);
		$this->SetXY(5,$y);$this->MultiCell(50,5,	"ADICCIONES",'0','C');
		$y=$y+7;
		$this->SetFont('Arial','',7);
		$this->SetXY(25,$y);$this->MultiCell(50,5,	"NUEVOS: 46",'0','L');
		$y=$y+5;
		$this->SetXY(25,$y);$this->MultiCell(50,5,	"CONTINUADORES: 0",'0','L');
		$y=$y+5;
		$this->SetXY(25,$y);$this->MultiCell(50,5,	"INDIGENTES: 0",'0','L');
		$y=$y+5;
		$this->SetFont('Arial','B',8);
		$this->SetXY(25,$y);$this->MultiCell(50,5,	"TOTAL: 46",'0','L');
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
