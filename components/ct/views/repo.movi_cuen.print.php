<?php
global $f;
$f->library('pdf');

class repo extends FPDF
{
	var $ano;
	var $mes;
	var $tipo;
	function  filtros($filtros){
		$this->ano = $filtros["ano"];
		$this->mes = $filtros["mes"];
		$this->tipo = $filtros["tipo"];
	}
	function Header(){
		$tipos = array(
			"A"=>"Activo",
			"P"=>"Pasivo",
			"PT"=>"Patrimonio",
			"I"=>"Ingresos",
			"G"=>"Gastos",
			"R"=>"Resultados",
			"PR"=>"Presupuestos",
			"O"=>"Orden"
		);
		$meses = array("Todos","Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Setiembre","Octubre","Noviembre","Diciembre");
		$this->SetFont('Arial','B',14);
		$this->SetXY(10,5);$this->MultiCell(277,5,"MOVIMIENTO DE CUENTAS DE TIPO ".strtoupper($tipos[$this->tipo]),'0','C');
		$this->SetFont('Arial','',9);
		$this->SetXY(10,10);$this->MultiCell(277,5,"Al Mes de ".strtoupper($meses[$this->mes])." - ".$this->ano,'0','C');
		$this->SetFont('Arial','B',9);
		$this->SetXY(10,5);$this->MultiCell(60,5,"SOCIEDAD DE BENEFICENCIA PUBLICA DE AREQUIPA",'0','C');
		$this->SetXY(10,5);$this->MultiCell(277,5,"Página: ".$this->PageNo()."/{nb}",'0','R');
		$this->SetFont('Arial','',7);
		$this->SetXY(10,15);$this->MultiCell(60,5,"Sistema SBPA - Módulo de Contabilidad",'0','C');
		$this->SetFont('Arial','B',7);
		$y = 20;
		$this->SetXY(5,$y);$this->MultiCell(25,12,"COD",'1','C');
		$this->SetXY(30,$y);$this->MultiCell(60,12,"CUENTAS DEL MAYOR",'1','C');
		$this->SetXY(90,$y);$this->MultiCell(50,8,"MOVIMIENTO ACUMULADO ANTERIOR",'1','C');
		$this->SetXY(90,$y+8);$this->MultiCell(25,4,"DEBE",'1','C');
		$this->SetXY(115,$y+8);$this->MultiCell(25,4,"HABER",'1','C');
		$this->SetXY(140,$y);$this->MultiCell(50,8,"MOVIMIENTOS DEL MES",'1','C');
		$this->SetXY(140,$y+8);$this->MultiCell(25,4,"DEBE",'1','C');
		$this->SetXY(165,$y+8);$this->MultiCell(25,4,"HABER",'1','C');
		$this->SetXY(190,$y);$this->MultiCell(50,8,"MOVIMIENTOS ACUMULADOS",'1','C');
		$this->SetXY(190,$y+8);$this->MultiCell(25,4,"DEBE",'1','C');
		$this->SetXY(215,$y+8);$this->MultiCell(25,4,"HABER",'1','C');
		$this->SetXY(240,$y);$this->MultiCell(50,8,"SALDOS ACUMULADOS",'1','C');
		$this->SetXY(240,$y+8);$this->MultiCell(25,4,"DEUDOR",'1','C');
		$this->SetXY(265,$y+8);$this->MultiCell(25,4,"ACREEDOR",'1','C');
		$this->Line(5, 32, 5, 200);
		$this->Line(90, 32, 90, 200);
		$this->Line(115, 32, 115, 200);
		$this->Line(140, 32, 140, 200);
		$this->Line(165, 32, 165, 200);
		$this->Line(190, 32, 190, 200);
		$this->Line(215, 32, 215, 200);
		$this->Line(240, 32, 240, 200);
		$this->Line(265, 32, 265, 200);
		$this->Line(290, 32, 290, 200);
		$this->Line(5, 200, 290, 200);
	}		
	function Publicar($items){
		$y=32;
		$this->SetFont('Arial','',7);
		$tot_last_d = 0;
		$tot_last_h = 0;
		$tot_this_d = 0;
		$tot_this_h = 0;
		$tot_acum_d = 0;
		$tot_acum_h = 0;
		$tot_sald_d = 0;
		$tot_sald_a = 0;
		foreach($items as $item){
			if($y>197){
				$this->addPage();
				$y = 32;
			}
			$this->SetXY(5,$y);$this->MultiCell(25,3,$item["cod"],'0','L');
			$this->SetXY(30,$y);$this->MultiCell(60,3,substr($item["descr"], 0,35),'0','L');
			$last_d = ($item["last_d"]<>0)?number_format($item["last_d"],2):"";
			$last_h = ($item["last_h"]<>0)?number_format($item["last_h"],2):"";
			$this_d = ($item["this_d"]<>0)?number_format($item["this_d"],2):"";
			$this_h = ($item["this_h"]<>0)?number_format($item["this_h"],2):"";
			$sum_acum_d = $item["last_d"]+$item["this_d"];
			$sum_acum_h = $item["last_h"]+$item["this_h"];
			$acum_d = ($sum_acum_d<>0)?number_format($sum_acum_d,2):"";
			$acum_h = ($sum_acum_h<>0)?number_format($sum_acum_h,2):"";
			$sum_sald = $sum_acum_d-$sum_acum_h;
			if($sum_sald==0){
				$sald_d = "";
				$sald_a = "";
			}elseif($sum_sald>0){
				$sald_d = number_format($sum_sald,2);
				$sald_a = "";
				if(strlen($item["cod"])==4){
					$tot_sald_d +=$sum_sald;
				}
			}else{
				$sald_d = "";
				$sald_a = number_format(abs($sum_sald),2);
				if(strlen($item["cod"])==4){
					$tot_sald_a +=abs($sum_sald);
				}
			}
			$this->SetXY(90,$y);$this->MultiCell(25,3,$last_d,'0','R');
			$this->SetXY(115,$y);$this->MultiCell(25,3,$last_h,'0','R');
			$this->SetXY(140,$y);$this->MultiCell(25,3,$this_d,'0','R');
			$this->SetXY(165,$y);$this->MultiCell(25,3,$this_h,'0','R');
			$this->SetXY(190,$y);$this->MultiCell(25,3,$acum_d,'0','R');
			$this->SetXY(215,$y);$this->MultiCell(25,3,$acum_h,'0','R');
			$this->SetXY(240,$y);$this->MultiCell(25,3,$sald_d,'0','R');
			$this->SetXY(265,$y);$this->MultiCell(25,3,$sald_a,'0','R');
			if(strlen($item["cod"])==4){
				$tot_last_d +=$item["last_d"];
				$tot_last_h +=$item["last_h"];
				$tot_this_d +=$item["this_d"];
				$tot_this_h +=$item["this_h"];
				$tot_acum_d +=$sum_acum_d;
				$tot_acum_h +=$sum_acum_h;			
			}
			$y+=3;
		}
		$this->SetXY(5,200);$this->MultiCell(85,3,"TOTAL",'1','L');
		$this->SetXY(90,200);$this->MultiCell(25,3,number_format($tot_last_d,2),'1','R');
		$this->SetXY(115,200);$this->MultiCell(25,3,number_format($tot_last_h,2),'1','R');
		$this->SetXY(140,200);$this->MultiCell(25,3,number_format($tot_this_d,2),'1','R');
		$this->SetXY(165,200);$this->MultiCell(25,3,number_format($tot_this_h,2),'1','R');
		$this->SetXY(190,200);$this->MultiCell(25,3,number_format($tot_acum_d,2),'1','R');
		$this->SetXY(215,200);$this->MultiCell(25,3,number_format($tot_acum_h,2),'1','R');
		$this->SetXY(240,200);$this->MultiCell(25,3,number_format($tot_sald_d,2),'1','R');
		$this->SetXY(265,200);$this->MultiCell(25,3,number_format($tot_sald_a,2),'1','R');
	}
	function Footer()
	{
    	//Footer de la pagina
	}  
}

$pdf=new repo('L','mm','A4');
$pdf->AliasNbPages();
$pdf->SetMargins(15,39.5,0); 
$pdf->SetTitle("reporte");
$pdf->SetAutoPageBreak(false,0); 
$pdf->filtros($filtros);
$pdf->Open();
$pdf->AddPage();
$pdf->Publicar($items);
$pdf->SetLeftMargin(25);
//header('Content-Type: application/pdf; charset=utf-8');
$pdf->Output();
?>