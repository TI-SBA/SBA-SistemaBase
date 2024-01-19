<?php
global $f;
$f->library('pdf');

class repo extends FPDF
{
	var $mes;
	var $ano;
	function Filter($filtros){
		$this->mes = $filtros["mes"];
		$this->ano = $filtros["ano"];
	}
	function Header(){
		/*$meses = array("Todos","Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Setiembre","Octubre","Noviembre","Diciembre");
		//$this->Image(IndexPath.DS.'templates/pr/presupuestos.gif',15,15,180,267);	
		$this->SetFont('Arial','B',15);
		$this->setY(15);$this->Cell(0,10,"SOCIEDAD DE BENEFICENCIA PUBLICA DE AREQUIPA",0,0,'C');
		$this->SetFont('Arial','I',13);
		$this->setY(20);$this->Cell(0,10,"NOTAS A LOS ESTADOS FINANCIEROS NÚMERICOS ".strtoupper($meses[$this->mes])." - ".$this->ano,0,0,'C');*/
	}		
	function Publicar($items){
		$meses = array("Todos","Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Setiembre","Octubre","Noviembre","Diciembre");
		$monedas = array(
				"S"=>array("nomb"=>"Soles","simb"=>"S/."),
				"D"=>array("nomb"=>"Dolares","simb"=>"USSD $.")
		);
		$x=0;
		$y=20;
		$y_ini = $y;
		$page_b = 250;
		//$this->Rect(60, $y, 15, 5);$this->SetXY(100,$y);$this->MultiCell(30,5,number_format($activos["valor_bruto"],2,".", ","),'1','R');
		$index = 0;
		foreach($items as $item){
			if($index>0){
				$this->AddPage();
				$y = $y_ini;
			}
			$this->SetFont('Arial','B',12);
			$this->SetXY(15,$y);$this->MultiCell(180,5,"REPORTE DE CONCILIACIÓN BANCARIA",'1','C');
			$y=$y+5;
			$this->Rect(15, $y, 180, 20);
			$this->SetXY(15,$y);$this->MultiCell(40,5,"BANCO",'0','L');
			$this->SetFont('Arial','',12);
			$this->SetXY(55,$y);$this->MultiCell(140,5,$item["cuenta_banco"]["banco"]["nomb"],'0','L');
			$y=$y+5;
			$this->SetFont('Arial','B',12);
			$this->SetXY(15,$y);$this->MultiCell(40,5,"Nº CTA. CTE",'0','L');
			$this->SetFont('Arial','',12);
			$this->SetXY(55,$y);$this->MultiCell(140,5,$item["cuenta_banco"]["cod"],'0','L');
			$y=$y+5;
			$this->SetFont('Arial','B',12);
			$this->SetXY(15,$y);$this->MultiCell(40,5,"MONEDA",'0','L');
			$this->SetFont('Arial','',12);
			$this->SetXY(55,$y);$this->MultiCell(140,5,$monedas[$item["cuenta_banco"]["moneda"]]["nomb"],'0','L');
			$y=$y+5;
			$this->SetFont('Arial','B',12);
			$this->SetXY(15,$y);$this->MultiCell(40,5,"MES",'0','L');
			$this->SetFont('Arial','',12);
			$this->SetXY(55,$y);$this->MultiCell(40,5,strtoupper($meses[$this->mes])." ".$this->ano,'0','L');
			$y=$y+5;
			$this->SetFont('Arial','B',12);
			$this->SetXY(15,$y);$this->MultiCell(180,5,"A. SALDO SEGÚN LIBRO DE BANCOS",'0','L');
			$this->SetFont('Arial','',12);
			$this->SetXY(15,$y);$this->MultiCell(180,5,$item["saldo_libro_bancos"],'0','R');
			$y=$y+5;
			/** Cheques Pendientes */
			$this->SetFont('Arial','B',12);
			$this->SetXY(15,$y);$this->MultiCell(180,5,"B. CHEQUES PENDIENTES DE PAGO EN BANCOS",'0','L');
			$this->SetFont('Arial','',12);
			$this->SetXY(15,$y);$this->MultiCell(180,5,number_format($item["total_cheques"],2,".", ","),'0','R');
			$y=$y+5;
			$this->SetXY(15,$y);$this->MultiCell(35,5,"FECHA",'1','C');
			$this->SetXY(50,$y);$this->MultiCell(30,5,"CHEQUE Nº",'1','C');
			$this->SetXY(80,$y);$this->MultiCell(80,5,"DETALLE",'1','C');
			$this->SetXY(160,$y);$this->MultiCell(35,5,"MONTO",'1','C');			
			$y=$y+5;
			$total_cheques = 0;
			foreach($item["cheques"] as $cheque){
				if($y>$page_b){
					$this->SetXY(15,$y);$this->MultiCell(35,5,"",'1','C');
					$this->SetXY(50,$y);$this->MultiCell(30,5,"",'1','C');
					$this->SetXY(80,$y);$this->MultiCell(80,5,"VAN",'1','C');
					$this->SetXY(160,$y);$this->MultiCell(35,5,number_format($total_cheques,2,".", ","),'1','C');
					$this->AddPage();
					$y=$y_ini;
					$this->SetXY(15,$y);$this->MultiCell(35,5,"FECHA",'1','C');
					$this->SetXY(50,$y);$this->MultiCell(30,5,"CHEQUE Nº",'1','C');
					$this->SetXY(80,$y);$this->MultiCell(80,5,"DETALLE",'1','C');
					$this->SetXY(160,$y);$this->MultiCell(35,5,"MONTO",'1','C');
					$y=$y+5;
					$this->SetXY(15,$y);$this->MultiCell(35,5,"",'1','C');
					$this->SetXY(50,$y);$this->MultiCell(30,5,"",'1','C');
					$this->SetXY(80,$y);$this->MultiCell(80,5,"VIENEN",'1','C');
					$this->SetXY(160,$y);$this->MultiCell(35,5,number_format($total_cheques,2,".", ","),'1','C');
					$y=$y+5;
				}
				$this->Rect(15, $y, 35, 5);$this->SetXY(15,$y);$this->MultiCell(35,5,Date::format($cheque["fecha"]->sec, 'd/m/Y'),'0','C');
				$this->Rect(50, $y, 30, 5);$this->SetXY(50,$y);$this->MultiCell(30,5,$cheque["cheque"],'0','C');
				$nomb = $cheque["detalle"]["nomb"];
				if($cheque["detalle"]["tipo_enti"]=="P")$nomb = $cheque["detalle"]["nomb"]." ".$cheque["detalle"]["appat"]." ".$cheque["detalle"]["apmat"];
				$this->Rect(80, $y, 80, 5);$this->SetXY(80,$y);$this->MultiCell(80,5,$nomb,'0','L');
				$this->Rect(160, $y, 35, 5);$this->SetXY(160,$y);$this->MultiCell(35,5,number_format($cheque["monto"],2,".", ","),'0','R');		
				$total_cheques = $total_cheques + $cheque["monto"];
				$y=$y+5;
			}
			$y=$y+5;
			/** Depositos no Registrados */
			$this->SetFont('Arial','B',12);
			$this->SetXY(15,$y);$this->MultiCell(180,5,"C. DEPÓSITOS NO REGISTRADOS EN LIBROS",'0','L');
			$this->SetFont('Arial','',12);
			$this->SetXY(15,$y);$this->MultiCell(180,5,number_format($item["total_depositos"],2,".", ","),'0','R');
			$y=$y+5;
			$this->SetXY(15,$y);$this->MultiCell(35,5,"FECHA",'1','C');
			$this->SetXY(50,$y);$this->MultiCell(80,5,"DEPOSITO Nº",'1','C');
			$this->SetXY(130,$y);$this->MultiCell(30,5,"OFICINA",'1','C');
			$this->SetXY(160,$y);$this->MultiCell(35,5,"MONTO",'1','C');
			$y=$y+5;
			$total_depositos = 0;
			foreach($item["depositos"] as $deposito){
				if($y>$page_b){
					$this->SetXY(15,$y);$this->MultiCell(35,5,"",'1','C');
					$this->SetXY(50,$y);$this->MultiCell(80,5,"",'1','C');
					$this->SetXY(130,$y);$this->MultiCell(30,5,"VAN",'1','C');
					$this->SetXY(160,$y);$this->MultiCell(35,5,number_format($total_depositos,2,".", ","),'1','R');
					$this->AddPage();
					$y=$y_ini;
					$this->SetXY(15,$y);$this->MultiCell(35,5,"FECHA",'1','C');
					$this->SetXY(50,$y);$this->MultiCell(80,5,"DEPOSITO Nº",'1','C');
					$this->SetXY(130,$y);$this->MultiCell(30,5,"OFICINA",'1','C');
					$this->SetXY(160,$y);$this->MultiCell(35,5,"MONTO",'1','C');
					$y=$y+5;
					$this->SetXY(15,$y);$this->MultiCell(35,5,"",'1','C');
					$this->SetXY(50,$y);$this->MultiCell(80,5,"",'1','C');
					$this->SetXY(130,$y);$this->MultiCell(30,5,"VIENEN",'1','C');
					$this->SetXY(160,$y);$this->MultiCell(35,5,number_format($total_depositos,2,".", ","),'1','R');
					$y=$y+5;
				}
				$this->Rect(15, $y, 35, 5);$this->SetXY(15,$y);$this->MultiCell(35,5,Date::format($deposito["fecha"]->sec, 'd/m/Y'),'0','C');
				$this->Rect(50, $y, 80, 5);$this->SetXY(50,$y);$this->MultiCell(80,5,$deposito["deposito"],'0','L');
				$this->Rect(130, $y, 30, 5);$this->SetXY(130,$y);$this->MultiCell(30,5,$deposito["agencia"],'0','L');
				$this->Rect(160, $y, 35, 5);$this->SetXY(160,$y);$this->MultiCell(35,5,number_format($deposito["monto"],2,".", ","),'0','R');	
				$total_depositos = $total_depositos + $deposito["monto"];
				$y=$y+5;
			}
			$y=$y+5;
			/** Gastos no registrados */
			$this->SetFont('Arial','B',12);
			$this->SetXY(15,$y);$this->MultiCell(180,5,"D. GASTOS NO REGISTRADOS EN LIBROS",'0','L');
			$this->SetFont('Arial','',12);
			$this->SetXY(15,$y);$this->MultiCell(180,5,number_format($item["total_gastos"],2,".", ","),'0','R');
			$y=$y+5;
			$this->SetXY(15,$y);$this->MultiCell(35,5,"FECHA",'1','C');
			$this->SetXY(50,$y);$this->MultiCell(110,5,"DESCRIPCIÓN",'1','C');
			$this->SetXY(160,$y);$this->MultiCell(35,5,"MONTO",'1','C');
			$y=$y+5;
			$total_gastos = 0;
			foreach($item["gastos"] as $gasto){
				if($y>$page_b){
					$this->SetXY(15,$y);$this->MultiCell(35,5,"",'1','C');
					$this->SetXY(50,$y);$this->MultiCell(110,5,"VAN",'1','C');
					$this->SetXY(160,$y);$this->MultiCell(35,5,number_format($total_gastos,2,".", ","),'1','R');
					$this->AddPage();
					$y=$y_ini;
					$this->SetXY(15,$y);$this->MultiCell(35,5,"FECHA",'1','C');
					$this->SetXY(50,$y);$this->MultiCell(110,5,"DESCRIPCIÓN",'1','C');
					$this->SetXY(160,$y);$this->MultiCell(35,5,"MONTO",'1','C');
					$y=$y+5;
					$this->SetXY(15,$y);$this->MultiCell(35,5,"",'1','C');
					$this->SetXY(50,$y);$this->MultiCell(110,5,"VIENEN",'1','C');
					$this->SetXY(160,$y);$this->MultiCell(35,5,number_format($total_gastos,2,".", ","),'1','R');
					$y=$y+5;
				}
				$this->Rect(15, $y, 35, 5);$this->SetXY(15,$y);$this->MultiCell(35,5,Date::format($gasto["fecha"]->sec, 'd/m/Y'),'1','C');
				$this->Rect(50, $y, 110, 5);$this->SetXY(50,$y);$this->MultiCell(110,5,$gasto["descr"],'1','L');
				$this->Rect(160, $y, 35, 5);$this->SetXY(160,$y);$this->MultiCell(35,5,number_format($gasto["monto"],2,".", ","),'1','R');
				$total_gastos = $total_gastos + $gasto["monto"];
				$y=$y+5;
			}
			$y=$y+5;
			$this->SetFont('Arial','B',12);
			$this->SetXY(15,$y);$this->MultiCell(145,5,"E. SALDO EN BANCOS = A+B+C-D",'1','L');
			$this->Rect(160, $y, 35, 5);$this->SetXY(160,$y);$this->MultiCell(35,5,number_format($item["saldo_bancos"],2,".", ","),'1','R');
			$y=$y+10;
			$this->SetFont('Arial','UB',12);
			$this->SetXY(15,$y);$this->MultiCell(180,5,"CONCILIACIÓN",'0','C');
			$y=$y+5;
			$this->SetFont('Arial','',11);
			$this->SetXY(15,$y);$this->MultiCell(180,5,"Saldo según extracto bancario ".$monedas[$item["cuenta_banco"]["moneda"]]["simb"],'0','L');
			$this->SetXY(15,$y);$this->MultiCell(180,5,number_format($item["saldo_extracto"],2,".", ","),'0','R');
			$y=$y+5;
			$this->SetXY(15,$y);$this->MultiCell(180,5,"Saldo según libro bancos ".$monedas[$item["cuenta_banco"]["moneda"]]["simb"],'0','L');
			$this->SetXY(15,$y);$this->MultiCell(180,5,number_format($item["saldo_bancos"],2,".", ","),'0','R');
			$y=$y+5;
			$this->SetXY(15,$y);$this->MultiCell(180,5,"Diferencia ".$monedas[$item["cuenta_banco"]["moneda"]]["simb"],'0','L');
			$this->SetXY(15,$y);$this->MultiCell(180,5,number_format($item["diferencia"],2,".", ","),'0','R');
			$y = $y+10;
			$this->SetFont('Arial','B',12);
			$this->SetXY(15,$y);$this->MultiCell(180,5,"OBSERVACIONES:",'0','L');
			$y=$y+5;
			$this->SetFont('Arial','',12);
			$this->SetXY(15,$y);$this->MultiCell(180,5,$item["observ"],'0','L');
			$y=$y+15;
			$this->SetXY(15,$y);$this->MultiCell(40,5,"CHEQUE Nº",'1','C');
			$this->SetXY(55,$y);$this->MultiCell(50,5,"MONTO",'1','C');
			$this->SetXY(105,$y);$this->MultiCell(45,5,"CH/. EXTRACTO",'1','C');
			$this->SetXY(150,$y);$this->MultiCell(45,5,"DIFERENCIA",'1','C');
			$y=$y+5;
			/** Recitifaciones */
			$total_recti_monto = 0;
			$total_recti_estr = 0;
			$total_recti_dif = 0;
			foreach($item["rectificaciones"] as $recti){
				if($y>$page_b){
					$this->SetXY(15,$y);$this->MultiCell(40,5,"VAN",'1','C');
					$this->SetXY(55,$y);$this->MultiCell(50,5,number_format($total_recti_monto,2,".", ","),'1','R');
					$this->SetXY(105,$y);$this->MultiCell(45,5,number_format($total_recti_estr,2,".", ","),'1','R');
					$this->SetXY(150,$y);$this->MultiCell(45,5,number_format($total_recti_dif,2,".", ","),'1','R');
					$this->AddPage();
					$y=$y_ini;
					$this->SetXY(15,$y);$this->MultiCell(40,5,"CHEQUE Nº",'1','C');
					$this->SetXY(55,$y);$this->MultiCell(50,5,"MONTO",'1','C');
					$this->SetXY(105,$y);$this->MultiCell(45,5,"CH/. EXTRACTO",'1','C');
					$this->SetXY(150,$y);$this->MultiCell(45,5,"DIFERENCIA",'1','C');
					$y = $y+5;
					$this->SetXY(15,$y);$this->MultiCell(40,5,"VIENEN",'1','C');
					$this->SetXY(55,$y);$this->MultiCell(50,5,number_format($total_recti_monto,2,".", ","),'1','R');
					$this->SetXY(105,$y);$this->MultiCell(45,5,number_format($total_recti_estr,2,".", ","),'1','R');
					$this->SetXY(150,$y);$this->MultiCell(45,5,number_format($total_recti_dif,2,".", ","),'1','R');
					$y=$y+5;
				}
				$this->SetXY(15,$y);$this->MultiCell(40,5,$recti["cheque"],'1','C');
				$this->SetXY(55,$y);$this->MultiCell(50,5,number_format($recti["monto"],2,".", ","),'1','R');
				$this->SetXY(105,$y);$this->MultiCell(45,5,number_format($recti["estracto"],2,".", ","),'1','R');
				$this->SetXY(150,$y);$this->MultiCell(45,5,number_format($recti["diferencia"],2,".", ","),'1','R');
				$total_recti_monto = $total_recti_monto + $recti["monto"];
				$total_recti_estr = $total_recti_estr + $recti["estracto"];
				$total_recti_dif = $total_recti_dif + $recti["diferencia"];
				$y=$y+5;
			}
			$y=$y+10;
			$this->SetXY(15,$y);$this->MultiCell(180,5,"...............................................",'0','C');
			$y=$y+5;
			$this->SetXY(15,$y);$this->MultiCell(180,5,$item["activo"]["autor"]["nomb"]." ".$item["activo"]["autor"]["appat"]." ".$item["activo"]["autor"]["apmat"],'0','C');
			$y = $y+10;
			$index++;
		}
	}
	function Footer()
	{
    	//Footer de la pagina
	}  
}

$pdf=new repo('P','mm','A4');
$pdf->AliasNbPages();
$pdf->SetMargins(15,39.5,0); 
$pdf->SetTitle("reporte");
$pdf->SetAutoPageBreak(false,0); 
$pdf->Open();
$pdf->Filter($filtros);
$pdf->AddPage();
$pdf->Publicar($items);
$pdf->SetLeftMargin(25);
//header('Content-Type: application/pdf; charset=utf-8');
$pdf->Output();

?>