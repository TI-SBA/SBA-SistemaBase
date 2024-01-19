<?php
global $f;
$f->library('pdf');

class repo extends FPDF
{
	var $ano;
	var $mes;
	function  filtros($filtros){
		$this->ano = $filtros["ano"];
		$this->mes = $filtros["mes"];
	}
	function Header(){
		$meses = array("Todos","Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Setiembre","Octubre","Noviembre","Diciembre");
		$this->SetFont('Arial','B',14);
		$this->SetXY(10,5);$this->MultiCell(615,5,"BALANCE CONSTRUCTIVO",'0','C');
		$this->SetFont('Arial','',9);
		$this->SetXY(10,10);$this->MultiCell(615,5,"Al Mes de ".strtoupper($meses[$this->mes])." - ".$this->ano,'0','C');
		$this->SetFont('Arial','B',9);
		$this->SetXY(10,5);$this->MultiCell(60,5,"SOCIEDAD DE BENEFICENCIA PUBLICA DE AREQUIPA",'0','C');		
		$this->SetXY(10,5);$this->MultiCell(615,5,"Página: ".$this->PageNo()."/{nb}",'0','R');
		$this->SetFont('Arial','',7);
		$this->SetXY(10,15);$this->MultiCell(60,5,"Sistema SBPA - Módulo de Contabilidad",'0','C');
		$y = 20;
		$this->SetFont('Arial','B',7);
		$this->SetXY(10,$y);$this->MultiCell(25,12,"COD",'1','C');
		$this->SetXY(35,$y);$this->MultiCell(60,12,"CUENTAS DEL MAYOR",'1','C');
		$this->SetXY(95,$y);$this->MultiCell(60,8,"ASIENTO PREFERENCIAL DE APERTURA",'1','C');
		$this->SetXY(95,$y+8);$this->MultiCell(30,4,"DEBE",'1','C');
		$this->SetXY(125,$y+8);$this->MultiCell(30,4,"HABER",'1','C');
		if($this->periodo==date("Y")){
			$header_date = "MOVIMIENTOS ACUMULADOS AL ".date("d/m/Y");
		}else{
			$header_date = "MOVIMIENTOS ACUMULADOS AL 31/12/".$this->periodo;
		}
		$this->SetXY(155,$y);$this->MultiCell(60,8,$header_date,'1','C');
		$this->SetXY(155,$y+8);$this->MultiCell(30,4,"DEBE",'1','C');
		$this->SetXY(185,$y+8);$this->MultiCell(30,4,"HABER",'1','C');
		$this->SetXY(215,$y);$this->MultiCell(60,8,"SALDOS",'1','C');
		$this->SetXY(215,$y+8);$this->MultiCell(30,4,"DEUDOR",'1','C');
		$this->SetXY(245,$y+8);$this->MultiCell(30,4,"ACREEDOR",'1','C');
		$this->SetXY(275,$y);$this->MultiCell(60,8,"AJUSTE Y REGULARIZACION",'1','C');
		$this->SetXY(275,$y+8);$this->MultiCell(30,4,"DEBE",'1','C');
		$this->SetXY(305,$y+8);$this->MultiCell(30,4,"HABER",'1','C');
		$this->SetXY(335,$y);$this->MultiCell(60,8,"SALDOS",'1','C');
		$this->SetXY(335,$y+8);$this->MultiCell(30,4,"DEUDOR",'1','C');
		$this->SetXY(365,$y+8);$this->MultiCell(30,4,"ACREEDOR",'1','C');
		$this->SetXY(395,$y);$this->MultiCell(240,4,"DISTRIBUCION DE SALDOS Y DE LAS CUENTAS",'1','C');
		$this->SetXY(395,$y+4);$this->MultiCell(60,4,"PATRIMONIALES",'1','C');
		$this->SetXY(395,$y+8);$this->MultiCell(30,4,"ACTIVO",'1','C');
		$this->SetXY(425,$y+8);$this->MultiCell(30,4,"PASIVO",'1','C');
		$this->SetXY(455,$y+4);$this->MultiCell(60,4,"GESTION Y RESULTADOS",'1','C');
		$this->SetXY(455,$y+8);$this->MultiCell(30,4,"GASTOS",'1','C');
		$this->SetXY(485,$y+8);$this->MultiCell(30,4,"INGRESOS",'1','C');
		$this->SetXY(515,$y+4);$this->MultiCell(60,4,"PRESUPUESTARIOS",'1','C');
		$this->SetXY(515,$y+8);$this->MultiCell(30,4,"DEUDOR",'1','C');
		$this->SetXY(545,$y+8);$this->MultiCell(30,4,"ACREEDOR",'1','C');
		$this->SetXY(575,$y+4);$this->MultiCell(60,4,"CUENTAS DE ORDEN",'1','C');
		$this->SetXY(575,$y+8);$this->MultiCell(30,4,"DEUDOR",'1','C');
		$this->SetXY(605,$y+8);$this->MultiCell(30,4,"ACREEDOR",'1','C');
		
		$this->Line(10, 32, 10, 284);
		$this->Line(95, 32, 95, 284);
		$this->Line(125, 32, 125, 284);
		$this->Line(155, 32, 155, 284);
		$this->Line(185, 32, 185, 284);
		$this->Line(215, 32, 215, 284);
		$this->Line(245, 32, 245, 284);
		$this->Line(275, 32, 275, 284);
		$this->Line(305, 32, 305, 284);
		$this->Line(335, 32, 335, 284);
		$this->Line(365, 32, 365, 284);
		$this->Line(395, 32, 395, 284);
		$this->Line(425, 32, 425, 284);
		$this->Line(455, 32, 455, 284);
		$this->Line(485, 32, 485, 284);
		$this->Line(515, 32, 515, 284);
		$this->Line(545, 32, 545, 284);
		$this->Line(575, 32, 575, 284);
		$this->Line(605, 32, 605, 284);
		$this->Line(635, 32, 635, 284);
		
		$this->Line(10, 280, 635, 280);
		$this->Line(10, 284, 635, 284);
	}		
	function Publicar($items){
		$x=0;
		$y=32;
		$this->SetFont('Arial','',7);
		$tot_1_d = 0;
		$tot_1_h = 0;
		$tot_2_d = 0;
		$tot_2_h = 0;
		$tot_3_d = 0;
		$tot_3_a = 0;
		foreach($items as $item){
			if($y>276){
				$this->SetXY(10,280);$this->MultiCell(85,4,"VAN",'0','C');
				$this->SetXY(95,$y);$this->MultiCell(30,4,number_format($tot_1_d,2),'0','R');
				$this->SetXY(125,$y);$this->MultiCell(30,4,number_format($tot_1_h,2),'0','R');
				$this->SetXY(155,$y);$this->MultiCell(30,4,number_format($tot_2_d,2),'0','R');
				$this->SetXY(185,$y);$this->MultiCell(30,4,number_format($tot_2_h,2),'0','R');
				$this->SetXY(215,$y);$this->MultiCell(30,4,number_format($tot_3_d,2),'0','R');
				$this->SetXY(245,$y);$this->MultiCell(30,4,number_format($tot_3_a,2),'0','R');
				$this->SetXY(275,$y);$this->MultiCell(30,4,"",'0','R');
				$this->SetXY(305,$y);$this->MultiCell(30,4,"",'0','R');	
				$this->SetXY(335,$y);$this->MultiCell(30,4,number_format($tot_5_d,2),'0','R');
				$this->SetXY(365,$y);$this->MultiCell(30,4,number_format($tot_5_a,2),'0','R');			
				$this->SetXY(395,$y);$this->MultiCell(30,4,number_format($tot_6_a,2),'0','R');
				$this->SetXY(425,$y);$this->MultiCell(30,4,number_format($tot_6_p,2),'0','R');
				$this->SetXY(455,$y);$this->MultiCell(30,4,number_format($tot_7_g,2),'0','R');
				$this->SetXY(485,$y);$this->MultiCell(30,4,number_format($tot_7_i,2),'0','R');
				$this->SetXY(515,$y);$this->MultiCell(30,4,number_format($tot_8_d,2),'0','R');
				$this->SetXY(545,$y);$this->MultiCell(30,4,number_format($tot_8_a,2),'0','R');
				$this->SetXY(575,$y);$this->MultiCell(30,4,number_format($tot_9_d,2),'0','R');
				$this->SetXY(605,$y);$this->MultiCell(30,4,number_format($tot_9_a,2),'0','R');
				$this->AddPage();
				$y=32;
				$this->SetXY(10,$y);$this->MultiCell(85,4,"VIENEN",'0','C');
				$this->SetXY(95,$y);$this->MultiCell(30,4,number_format($tot_1_d,2),'0','R');
				$this->SetXY(125,$y);$this->MultiCell(30,4,number_format($tot_1_h,2),'0','R');
				$this->SetXY(155,$y);$this->MultiCell(30,4,number_format($tot_2_d,2),'0','R');
				$this->SetXY(185,$y);$this->MultiCell(30,4,number_format($tot_2_h,2),'0','R');
				$this->SetXY(215,$y);$this->MultiCell(30,4,number_format($tot_3_d,2),'0','R');
				$this->SetXY(245,$y);$this->MultiCell(30,4,number_format($tot_3_a,2),'0','R');
				$this->SetXY(275,$y);$this->MultiCell(30,4,"",'0','R');
				$this->SetXY(305,$y);$this->MultiCell(30,4,"",'0','R');	
				$this->SetXY(335,$y);$this->MultiCell(30,4,number_format($tot_5_d,2),'0','R');
				$this->SetXY(365,$y);$this->MultiCell(30,4,number_format($tot_5_a,2),'0','R');			
				$this->SetXY(395,$y);$this->MultiCell(30,4,number_format($tot_6_a,2),'0','R');
				$this->SetXY(425,$y);$this->MultiCell(30,4,number_format($tot_6_p,2),'0','R');
				$this->SetXY(455,$y);$this->MultiCell(30,4,number_format($tot_7_g,2),'0','R');
				$this->SetXY(485,$y);$this->MultiCell(30,4,number_format($tot_7_i,2),'0','R');
				$this->SetXY(515,$y);$this->MultiCell(30,4,number_format($tot_8_d,2),'0','R');
				$this->SetXY(545,$y);$this->MultiCell(30,4,number_format($tot_8_a,2),'0','R');
				$this->SetXY(575,$y);$this->MultiCell(30,4,number_format($tot_9_d,2),'0','R');
				$this->SetXY(605,$y);$this->MultiCell(30,4,number_format($tot_9_a,2),'0','R');
				$this->Line(10, 36, 635, 36);
				$y+=4;
			}
			$this->SetXY(10,$y);$this->MultiCell(25,4,$item["cod"],'0','L');
			$this->SetXY(35,$y);$this->MultiCell(60,4,substr($item["descr"], 0,35),'0','L');
			$sum_last_d = $item["aper_d"];
			$sum_last_h = $item["aper_h"];
			$last_d = ($sum_last_d<>0)?number_format($sum_last_d,2):"";
			$last_h = ($sum_last_h<>0)?number_format($sum_last_h,2):"";
			$this->SetXY(95,$y);$this->MultiCell(30,4,$last_d,'0','R');
			$this->SetXY(125,$y);$this->MultiCell(30,4,$last_h,'0','R');
			$sum_this_d = $item["acum_d"];
			$sum_this_h = $item["acum_h"];
			$this_d = ($sum_this_d<>0)?number_format($sum_this_d,2):"";
			$this_h = ($sum_this_h<>0)?number_format($sum_this_h,2):"";
			if($sum_this_d>$sum_this_h){
				$deudor1 = number_format($sum_this_d-$sum_this_h,2);
				$sum_deudor = $sum_this_d-$sum_this_h;
				$sum_acreedor = 0;
				$acreedor1 = "";
			}else{
				$deudor1 = "";
				$acreedor1 = number_format($sum_this_h-$sum_this_d,2);
				$sum_deudor = 0;
				$sum_acreedor = $sum_this_h-$sum_this_d;
			}
			$this->SetXY(155,$y);$this->MultiCell(30,4,$this_d,'0','R');
			$this->SetXY(185,$y);$this->MultiCell(30,4,$this_h,'0','R');
			
			$this->SetXY(215,$y);$this->MultiCell(30,4,$deudor1,'0','R');
			$this->SetXY(245,$y);$this->MultiCell(30,4,$acreedor1,'0','R');
			
			$this->SetXY(335,$y);$this->MultiCell(30,4,$deudor1,'0','R');
			$this->SetXY(365,$y);$this->MultiCell(30,4,$acreedor1,'0','R');
			
			if(substr($item["cod"],0,1)=="1"||substr($item["cod"],0,1)=="2"||substr($item["cod"],0,1)=="3"){
				$this->SetXY(395,$y);$this->MultiCell(30,4,$deudor1,'0','R');
				$this->SetXY(425,$y);$this->MultiCell(30,4,$acreedor1,'0','R');
				if(strlen($item["cod"])==4){
					$tot_6_a += $sum_deudor;
					$tot_6_p += $sum_acreedor;
				}
			}elseif(substr($item["cod"],0,1)=="4"||substr($item["cod"],0,1)=="5"){
				$this->SetXY(455,$y);$this->MultiCell(30,4,$deudor1,'0','R');
				$this->SetXY(485,$y);$this->MultiCell(30,4,$acreedor1,'0','R');
				if(strlen($item["cod"])==4){
					$tot_7_g += $sum_deudor;
					$tot_7_i += $sum_acreedor;
				}
			}elseif(substr($item["cod"],0,1)=="8"){
				$this->SetXY(515,$y);$this->MultiCell(30,4,$deudor1,'0','R');
				$this->SetXY(545,$y);$this->MultiCell(30,4,$acreedor1,'0','R');
				if(strlen($item["cod"])==4){
					$tot_8_d += $sum_deudor;
					$tot_8_a += $sum_acreedor;
				}
			}elseif(substr($item["cod"],0,1)=="9"){
				$this->SetXY(575,$y);$this->MultiCell(30,4,$deudor1,'0','R');
				$this->SetXY(605,$y);$this->MultiCell(30,4,$acreedor1,'0','R');
				if(strlen($item["cod"])==4){
					$tot_9_d += $sum_deudor;
					$tot_9_a += $sum_acreedor;
				}
			}
			if(strlen($item["cod"])==4){
				$tot_1_d += $sum_last_d;
				$tot_1_h += $sum_last_h;
				$tot_2_d += $sum_this_d;
				$tot_2_h += $sum_this_h;
				$tot_3_d += floatval($sum_deudor);
				$tot_3_a += floatval($sum_acreedor);
				$tot_5_d += floatval($sum_deudor);
				$tot_5_a += floatval($sum_acreedor);
			}
			$y+=4;
		}
		$y=280;
		$this->SetXY(10,$y);$this->MultiCell(85,4,"TOTAL",'0','C');
		$this->SetXY(95,$y);$this->MultiCell(30,4,number_format($tot_1_d,2),'0','R');
		$this->SetXY(125,$y);$this->MultiCell(30,4,number_format($tot_1_h,2),'0','R');
		$this->SetXY(155,$y);$this->MultiCell(30,4,number_format($tot_2_d,2),'0','R');
		$this->SetXY(185,$y);$this->MultiCell(30,4,number_format($tot_2_h,2),'0','R');
		$this->SetXY(215,$y);$this->MultiCell(30,4,number_format($tot_3_d,2),'0','R');
		$this->SetXY(245,$y);$this->MultiCell(30,4,number_format($tot_3_a,2),'0','R');
		$this->SetXY(275,$y);$this->MultiCell(30,4,"",'0','R');
		$this->SetXY(305,$y);$this->MultiCell(30,4,"",'0','R');	
		$this->SetXY(335,$y);$this->MultiCell(30,4,number_format($tot_5_d,2),'0','R');
		$this->SetXY(365,$y);$this->MultiCell(30,4,number_format($tot_5_a,2),'0','R');			
		$this->SetXY(395,$y);$this->MultiCell(30,4,number_format($tot_6_a,2),'0','R');
		$this->SetXY(425,$y);$this->MultiCell(30,4,number_format($tot_6_p,2),'0','R');
		$this->SetXY(455,$y);$this->MultiCell(30,4,number_format($tot_7_g,2),'0','R');
		$this->SetXY(485,$y);$this->MultiCell(30,4,number_format($tot_7_i,2),'0','R');
		$this->SetXY(515,$y);$this->MultiCell(30,4,number_format($tot_8_d,2),'0','R');
		$this->SetXY(545,$y);$this->MultiCell(30,4,number_format($tot_8_a,2),'0','R');
		$this->SetXY(575,$y);$this->MultiCell(30,4,number_format($tot_9_d,2),'0','R');
		$this->SetXY(605,$y);$this->MultiCell(30,4,number_format($tot_9_a,2),'0','R');
	}
	function Footer()
	{
    	//Footer de la pagina
	}  
}

$pdf=new repo('P','mm',array(645,297));
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