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
		$ya=0;
		
		for($i = 0;$i<count($diario);$i++){
			for($j = 0;$j<count($diario[$i]['paciente']);$j++){
				print_r($diario[$i]['paciente']['roles'][0]['hist_cli']);
			}
			
		}
		
		/*print_r($ya);
		//////////////////////////////////////////////////////////////////
		/*$pisqui_n_mh = 0;
		$pisqui_c_mh = 0;
		$pisqui_i_mh = 0;
		$pisco_n_mh = 0;
		$pisco_c_mh = 0;
		$pisco_i_mh = 0;
		/*------------*/
		/*$pisqui_n_ad = 0;
		$pisqui_c_ad = 0;
		$pisqui_i_ad = 0;
		$pisco_n_ad = 0;
		$pisco_c_ad = 0;
		$pisco_i_ad = 0;
		/*
		for($i = 0;$i<count($diario);$i++){
			for($j = 0;$j<count($diario[$i]['consulta']);$j++){
				if($diario[$i]['medico']['_id'] =='5824cb048e7358f805000115' && $diario[$i]['modulo'] == 'AD' && floatval($diario[$i]["consulta"][$j]['cate']) == 10 ){
					$pisco_i_ad++;

				}
				if($diario[$i]['medico']['_id'] =='57cf1d608e73586c08000095' && $diario[$i]['modulo'] == 'AD' ){
					$pisco_i_ad++;

				}
				if($diario[$i]['medico']['_id'] =='587533593e6037474b8b4568' && $diario[$i]['modulo'] == 'AD' ){
					$pisco_c_ad++;

				}
				if($diario[$i]['medico']['_id'] =='52261ed14d4a13c407000027' && $diario[$i]['modulo'] == 'AD' ){
					$pisco_n_ad++;

				}
				if($diario[$i]['medico']['_id'] =='5824cb048e7358f805000115' && $diario[$i]['modulo'] == 'AD' ){
					$pisqui_i_ad++;

				}

				/*if(floatval($diario[$i]["consulta"][$j]['cate']) == 10 && $diario[$i]['medico']['_id'] !='57cf1d608e73586c08000095' || $diario[$i]['medico']['_id'] !='587533593e6037474b8b4568' || $diario[$i]['medico']['_id'] !='5977ae6c3e603746248b4568' || $diario[$i]['medico']['_id'] !='52261ed14d4a13c407000027' && $diario[$i]['modulo'] == 'MH'){
					$pisqui_n_mh++;
				}
				if(floatval($diario[$i]["consulta"][$j]['cate']) == 11 && $diario[$i]['medico']['_id'] !='57cf1d608e73586c08000095' || $diario[$i]['medico']['_id'] !='587533593e6037474b8b4568' || $diario[$i]['medico']['_id'] !='5977ae6c3e603746248b4568' || $diario[$i]['medico']['_id'] !='52261ed14d4a13c407000027' && $diario[$i]['modulo'] == 'MH'){
					$pisqui_c_mh++;
				}/*
				if(floatval($diario[$i]["consulta"][$j]['cate']) == 8 && $diario[$i]['medico']['_id'] !='57cf1d608e73586c08000095' || $diario[$i]['medico']['_id'] !='587533593e6037474b8b4568' || $diario[$i]['medico']['_id'] !='5977ae6c3e603746248b4568' || $diario[$i]['medico']['_id'] !='52261ed14d4a13c407000027' && $diario[$i]['modulo'] == 'MH'){
					$pisqui_i_mh++;
				}*/
				/*-------------------------------------------*/
				/*if(floatval($diario[$i]["consulta"][$j]['cate']) == 10 && $diario[$i]['medico']['_id'] !='521f83304d4a13881700000d' || $diario[$i]['medico']['_id'] !='587e2e503e60376b778b4568' || $diario[$i]['medico']['_id'] !='5908b7b43e60379f5e8b4567' || $diario[$i]['medico']['_id'] !='593ec2743e6037f3588b456b' && $diario[$i]['modulo'] == 'MH'){
					$pisco_n_mh++;
				}
				if(floatval($diario[$i]["consulta"][$j]['cate']) == 11 && $diario[$i]['medico']['_id'] !='521f83304d4a13881700000d' || $diario[$i]['medico']['_id'] !='587e2e503e60376b778b4568' || $diario[$i]['medico']['_id'] !='5908b7b43e60379f5e8b4567' || $diario[$i]['medico']['_id'] !='593ec2743e6037f3588b456b' && $diario[$i]['modulo'] == 'MH'){
					$pisco_c_mh++;
				}
				if(floatval($diario[$i]["consulta"][$j]['cate']) == 8 && $diario[$i]['medico']['_id'] !='521f83304d4a13881700000d' || $diario[$i]['medico']['_id'] !='587e2e503e60376b778b4568' || $diario[$i]['medico']['_id'] !='5908b7b43e60379f5e8b4567' || $diario[$i]['medico']['_id'] !='593ec2743e6037f3588b456b' && $diario[$i]['modulo'] == 'MH'){
					$pisco_i_mh++;
				}
				/*-------------------------------------------*/
				/*if(floatval($diario[$i]["consulta"][$j]['cate']) == 10 && $diario[$i]['medico']['_id'] !='57cf1d608e73586c08000095' || $diario[$i]['medico']['_id'] !='587533593e6037474b8b4568' || $diario[$i]['medico']['_id'] !='5977ae6c3e603746248b4568' || $diario[$i]['medico']['_id'] !='52261ed14d4a13c407000027' && $diario[$i]['modulo'] == 'AD'){
					$pisqui_n_ad++;
				}
				if(floatval($diario[$i]["consulta"][$j]['cate']) == 11 && $diario[$i]['medico']['_id'] !='57cf1d608e73586c08000095' || $diario[$i]['medico']['_id'] !='587533593e6037474b8b4568' || $diario[$i]['medico']['_id'] !='5977ae6c3e603746248b4568' || $diario[$i]['medico']['_id'] !='52261ed14d4a13c407000027' && $diario[$i]['modulo'] == 'AD'){
					$pisqui_c_ad++;
				}
				if(floatval($diario[$i]["consulta"][$j]['cate']) == 8 && $diario[$i]['medico']['_id'] !='57cf1d608e73586c08000095' || $diario[$i]['medico']['_id'] !='587533593e6037474b8b4568' || $diario[$i]['medico']['_id'] !='5977ae6c3e603746248b4568' || $diario[$i]['medico']['_id'] !='52261ed14d4a13c407000027' && $diario[$i]['modulo'] == 'AD'){
					$pisqui_i_ad++;
				}
				/*-------------------------------------------*/
				/*if(floatval($diario[$i]["consulta"][$j]['cate']) == 10 && $diario[$i]['medico']['_id'] !='521f83304d4a13881700000d' || $diario[$i]['medico']['_id'] !='587e2e503e60376b778b4568' || $diario[$i]['medico']['_id'] !='5908b7b43e60379f5e8b4567' || $diario[$i]['medico']['_id'] !='593ec2743e6037f3588b456b' && $diario[$i]['modulo'] == 'AD'){
					$pisco_n_ad++;
				}
				if(floatval($diario[$i]["consulta"][$j]['cate']) == 11 && $diario[$i]['medico']['_id'] !='521f83304d4a13881700000d' || $diario[$i]['medico']['_id'] !='587e2e503e60376b778b4568' || $diario[$i]['medico']['_id'] !='5908b7b43e60379f5e8b4567' || $diario[$i]['medico']['_id'] !='593ec2743e6037f3588b456b' && $diario[$i]['modulo'] == 'AD'){
					$pisco_c_ad++;
				}
				if(floatval($diario[$i]["consulta"][$j]['cate']) == 8 && $diario[$i]['medico']['_id'] !='521f83304d4a13881700000d' || $diario[$i]['medico']['_id'] !='587e2e503e60376b778b4568' || $diario[$i]['medico']['_id'] !='5908b7b43e60379f5e8b4567' || $diario[$i]['medico']['_id'] !='593ec2743e6037f3588b456b' && $diario[$i]['modulo'] == 'AD'){
					$pisco_i_ad++;
				}*/
			/*	
			}
		}
		print_r($pisco_i_ad);
		print_r($pisco_n_ad);
		print_r($pisco_c_ad);
		print_r($pisqui_i_ad);
		*/
		//////////////////////////////////////////////////////////////////
		/*
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
		$this->SetXY(25,$y);$this->MultiCell(50,5,	"NUEVOS: 70",'0','L');
		$y=$y+5;
		$this->SetXY(25,$y);$this->MultiCell(50,5,	"CONTINUADORES: 95",'0','L');
		$y=$y+5;
		$this->SetXY(25,$y);$this->MultiCell(50,5,	"INDIGENTES: 58",'0','L');
		$y=$y+5;
		$this->SetFont('Arial','B',8);
		$this->SetXY(25,$y);$this->MultiCell(50,5,	"TOTAL: 223",'0','L');
		$y=$y+10;
		$this->SetFont('Arial','B',8);
		$this->SetXY(5,$y);$this->MultiCell(200,5,	"PACIENTES: ADICCIONES",'0','C');
		$y=$y+10;
		$this->SetFont('Arial','B',8);
		$this->SetXY(5,$y);$this->MultiCell(50,5,	"PSIQUIATRIA",'0','C');
		$y=$y+7;
		$this->SetFont('Arial','',7);
		$this->SetXY(25,$y);$this->MultiCell(50,5,	"NUEVOS: 29",'0','L');
		$y=$y+5;
		$this->SetXY(25,$y);$this->MultiCell(50,5,	"CONTINUADORES: 3",'0','L');
		$y=$y+5;
		$this->SetXY(25,$y);$this->MultiCell(50,5,	"INDIGENTES: 4",'0','L');
		$y=$y+5;
		$this->SetFont('Arial','B',8);
		$this->SetXY(25,$y);$this->MultiCell(50,5,	"TOTAL: 36",'0','L');
		$y=$y+10;
		$this->SetFont('Arial','B',8);
		$this->SetXY(5,$y);$this->MultiCell(50,5,	"PSICOLOGIA",'0','C');
		$y=$y+7;
		$this->SetFont('Arial','',7);
		$this->SetXY(25,$y);$this->MultiCell(50,5,	"NUEVOS: 0",'0','L');
		$y=$y+5;
		$this->SetXY(25,$y);$this->MultiCell(50,5,	"CONTINUADORES: 0",'0','L');
		$y=$y+5;
		$this->SetXY(25,$y);$this->MultiCell(50,5,	"INDIGENTES: 0",'0','L');
		$y=$y+5;
		$this->SetFont('Arial','B',8);
		$this->SetXY(25,$y);$this->MultiCell(50,5,	"TOTAL: 0",'0','L');
		*/
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
