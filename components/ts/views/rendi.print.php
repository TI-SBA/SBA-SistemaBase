<?php
global $f;
$f->library('pdf');

class repo extends FPDF
{
	var $prog;
	var $num;
	var $fecha;
	function filtros($filtros){
		$this->prog=$filtros["prog"];
		$this->num=$filtros["num"];
		$this->fecha=$filtros["fecha"];
	}
	function Header(){
		//$this->Image(IndexPath.DS.'templates/ts/comprobante.gif',10,10,190,275);	
		$y=15;
		$this->SetFont('Arial','B',15);
		$this->SetXY(10,$y);$this->MultiCell(180,5,"RENDICION DEL FONDO PARA PAGOS EN EFECTIVO",'0','C');	
		$y=$y+10;
		$this->SetFont('Arial','',10);
		$this->SetXY(10,$y);$this->MultiCell(35,5,"Programa Nº",'1','C');
		$this->SetXY(45,$y);$this->MultiCell(15,5,"",'1','C');
		$this->SetXY(10,$y+5);$this->MultiCell(35,5,"Sub programa Nº",'1','C');
		$this->SetXY(45,$y+5);$this->MultiCell(15,5,"",'1','C');
		
		$this->SetXY(135,$y);$this->MultiCell(20,5,"Nº",'1','C');
		$this->SetXY(155,$y);$this->MultiCell(15,5,"Día",'1','C');
		$this->SetXY(170,$y);$this->MultiCell(15,5,"Mes",'1','C');
		$this->SetXY(185,$y);$this->MultiCell(15,5,"Año",'1','C');
		$this->SetXY(135,$y+5);$this->MultiCell(20,5,$this->num,'1','C');
		$this->SetXY(155,$y+5);$this->MultiCell(15,5,Date::format($this->fecha->sec, 'd'),'1','C');
		$this->SetXY(170,$y+5);$this->MultiCell(15,5,Date::format($this->fecha->sec, 'm'),'1','C');
		$this->SetXY(185,$y+5);$this->MultiCell(15,5,Date::format($this->fecha->sec, 'Y'),'1','C');
	}		
	function Publicar($saldo,$movs,$ultimo,$afectacion){
		$monedas = array(
			"S"=>array("simb"=>"S/.","nomb"=>"NUEVO SOL","plu"=>"NUEVOS SOLES"),
			"D"=>array("simb"=>"USSD $.","nomb"=>"DOLAR","plu"=>"DOLARES")
		);
		$types=array(
			"F"=>'Factura',
			"R"=>'Recibo',
			"B"=>'B.Vta.'
		);
		$x=10;
		$y=40;
		$y_ini = $y;
		$page_b = 275;
		$this->SetFont('Arial','',9);
		$this->SetXY(10,$y);$this->MultiCell(10,10,"Nº",'1','C');
		$this->SetXY(20,$y);$this->MultiCell(50,5,"DOCUMENTO",'1','C');
		$this->SetXY(20,$y+5);$this->MultiCell(20,5,"FECHA",'1','C');
		$this->SetXY(40,$y+5);$this->MultiCell(15,5,"CLASE",'1','C');
		$this->SetXY(55,$y+5);$this->MultiCell(15,5,"Nº",'1','C');
		$this->SetXY(70,$y);$this->MultiCell(80,10,"DETALLE DEL GASTO",'1','C');
		$this->SetXY(150,$y);$this->MultiCell(25,10,"Importe",'1','C');
		$this->SetXY(175,$y);$this->MultiCell(25,5,"Partida Especifica",'1','C');
		$y=$y+10;
		$total_movs = 0;
		foreach($movs as $mov){
			if($y>$page_b){
				$this->Rect(10, $y, 10, 5);
				$this->Rect(20, $y, 20, 5);
				$this->Rect(40, $y, 15, 5);
				$this->Rect(55, $y, 15, 5);
				$this->Rect(70, $y, 80, 5);$this->SetXY(70,$y);$this->MultiCell(100,5,"VAN",'0','C');
				$this->Rect(150, $y, 25, 5);$this->SetXY(150,$y);$this->MultiCell(25,5,number_format($total_movs,2,".", ","),'0','R');
				$this->Rect(175, $y, 25, 5);
				$this->AddPage();
				$y=$y_ini;
				$this->SetXY(10,$y);$this->MultiCell(10,10,"Nº",'1','C');
				$this->SetXY(20,$y);$this->MultiCell(50,5,"DOCUMENTO",'1','C');
				$this->SetXY(20,$y+5);$this->MultiCell(20,5,"FECHA",'1','C');
				$this->SetXY(40,$y+5);$this->MultiCell(15,5,"CLASE",'1','C');
				$this->SetXY(55,$y+5);$this->MultiCell(15,5,"Nº",'1','C');
				$this->SetXY(70,$y);$this->MultiCell(80,10,"DETALLE DEL GASTO",'1','C');
				$this->SetXY(150,$y);$this->MultiCell(25,10,"Importe",'1','C');
				$this->SetXY(175,$y);$this->MultiCell(25,5,"Partida Especifica",'1','C');
				$y=$y+10;
				$this->Rect(10, $y, 10, 5);
				$this->Rect(20, $y, 20, 5);
				$this->Rect(40, $y, 15, 5);
				$this->Rect(55, $y, 15, 5);
				$this->Rect(70, $y, 80, 5);$this->SetXY(70,$y);$this->MultiCell(100,5,"VIENEN",'0','C');
				$this->Rect(150, $y, 25, 5);$this->SetXY(150,$y);$this->MultiCell(25,5,number_format($total_movs,2,".", ","),'0','R');
				$this->Rect(175, $y, 25, 5);
				$y=$y+5;
			}
			$this->Rect(10, $y, 10, 5);$this->SetXY(10,$y);$this->MultiCell(10,5,$mov["item"],'0','C');
			$this->Rect(20, $y, 20, 5);$this->SetXY(20,$y);$this->MultiCell(20,5,Date::format($mov["fecreg"]->sec, 'd-m-Y'),'0','C');
			$this->Rect(40, $y, 15, 5);$this->SetXY(40,$y);$this->MultiCell(15,5,$types[$mov["documento"]],'0','L');
			$this->Rect(55, $y, 15, 5);$this->SetXY(55,$y);$this->MultiCell(15,5,$mov["num_doc"],'0','R');
			$beneficiario = $mov["beneficiario"]["nomb"];
			if($mov["beneficiario"]["tipo_enti"]=="P")$beneficiario=$mov["beneficiario"]["nomb"]." ".$mov["beneficiario"]["appat"]." ".$mov["beneficiario"]["apmat"];
			$this->Rect(70, $y, 80, 5);$this->SetXY(70,$y);$this->MultiCell(100,5,substr($beneficiario.": ".$mov["concepto"],0,50),'0','L');
			$this->Rect(150, $y, 25, 5);$this->SetXY(150,$y);$this->MultiCell(25,5,number_format($mov["monto"],2,".", ","),'0','R');
			$this->Rect(175, $y, 25, 5);$this->SetXY(175,$y);$this->MultiCell(25,5,$mov["clasificador"]["cod"],'0','R');
			$total_movs=$total_movs+$mov["monto"];
			$y=$y+5;
		}
		$this->Rect(10, $y, 10, 5);
		$this->Rect(20, $y, 20, 5);
		$this->Rect(40, $y, 15, 5);
		$this->Rect(55, $y, 15, 5);
		$this->Rect(70, $y, 80, 5);$this->SetXY(70,$y);$this->MultiCell(100,5,"TOTAL",'0','C');
		$this->Rect(150, $y, 25, 5);$this->SetXY(150,$y);$this->MultiCell(25,5,number_format($total_movs,2,".", ","),'0','R');
		$this->Rect(175, $y, 25, 5);
		$y=$y+10;
		$this->SetXY(10,$y);$this->MultiCell(75,5,"Informado en el Formato:\nEjecución Presupuestal (T-6) Nº\nLa Columan Cinco (5) Partidas Especificas\nSerá llenado por DIGA ú Oficina que haga su veces.\n\n\n",'1','L');
		$this->Rect(100, $y, 100, 35);
		$this->SetXY(100,$y);$this->MultiCell(100,5,"MOVIMIENTO DEL MES",'0','C');
		$y=$y+5;
		$this->SetXY(100,$y);$this->MultiCell(75,5,"Saldo Anterior",'0','L');
		$saldo_ant = 0;
		$incre_fond = 0;
		if($ultimo!=null){
			if(count($ultimo)>0){
				$sald_ant = $ultimo[0]["saldo"];
				$incre_fond = $ultimo[0]["gasto"];
			}
		}
		$this->SetXY(175,$y);$this->MultiCell(25,5,number_format($sald_ant,2,".", ","),'0','R');
		$y=$y+5;
		$this->SetXY(100,$y);$this->MultiCell(75,5,"Incremento de Fondo",'0','L');
		$this->SetXY(175,$y);$this->MultiCell(25,5,number_format($incre_fond,2,".", ","),'0','R');
		$y=$y+5;
		$this->SetXY(100,$y);$this->MultiCell(75,5,"TOTAL",'0','C');
		$this->SetXY(175,$y);$this->MultiCell(25,5,number_format($sald_ant+$incre_fond,2,".", ","),'0','R');
		$y=$y+5;
		$this->SetXY(100,$y);$this->MultiCell(75,5,"Menos importe de la Presente",'0','L');
		$y=$y+5;
		$this->SetXY(100,$y);$this->MultiCell(75,5,"Rendición",'0','L');
		$this->SetXY(175,$y);$this->MultiCell(25,5,number_format($saldo["saldo"],2,".", ","),'0','R');
		$y=$y+5;
		$this->SetXY(100,$y);$this->MultiCell(75,5,"Saldo Actual",'0','C');
		$this->SetXY(175,$y);$this->MultiCell(25,5,number_format($sald_ant+$incre_fond-$saldo["saldo"],2,".", ","),'0','R');
		$y=$y+10;
		/** estadistica del Objetod del Gasto */
		if($y+30>$page_b){
			$this->AddPage();
			$y=$y_ini;
		}
		$this->SetXY(10,$y);$this->MultiCell(190,5,"ESTADISTICA DEL OBJETO DEL GASTO",'1','C');
		$y=$y+5;
		$this->SetXY(10,$y);$this->MultiCell(40,5,"CODIGO DE LA PARTIDA",'1','C');
		$x_i = 50;
		foreach($afectacion[0]["importe"] as $orga){
			$this->SetXY($x_i,$y);$this->MultiCell(30,5,$orga["actividad"]["cod"],'1','C');
			$x_i+=30;
		}
		$y+=5;
		foreach($afectacion as $item){
			$this->SetXY(10,$y);$this->MultiCell(40,5,$item["clasificador"]["cod"],'1','L');
			$x_i = 50;
			foreach($item["importe"] as $imp){
				$this->SetXY($x_i,$y);$this->MultiCell(30,5,number_format($imp["monto"],2),'1','R');
				$x_i+=30;
			}
			$y+=5;
		}
		$y+=5;
		/*$this->SetXY(10,$y);$this->MultiCell(190,5,"ESTADISTICA DEL OBJETO DEL GASTO",'1','C');
		$y=$y+5;
		$this->SetXY(10,$y);$this->MultiCell(40,5,"CODIGO DE LA PARTIDA",'1','C');
		$this->SetXY(50,$y);$this->MultiCell(100,5,"IMPORTE",'1','C');
		$this->SetXY(150,$y);$this->MultiCell(50,5,"",'1','C');
		$y=$y+5;
		$tot_afec=0;
		foreach($saldo["afectacion"] as $afec){
			$this->Rect(10, $y, 190, 5);$this->SetXY(10,$y);$this->MultiCell(190,5,$afec["organizacion"]["nomb"],'0','L');
			$y=$y+5;
			if(isset($afec["gasto"])){
				foreach($afec["gasto"] as $result){
					if($y>$page_b){	
						$this->AddPage();		
						$y=$y_ini;
						$this->Rect(10, $y, 190, 5);$this->SetXY(10,$y);$this->MultiCell(190,5,$afec["organizacion"]["nomb"],'0','L');
						$y=$y+5;
					}
					$this->Rect(10, $y, 40, 5);$this->SetXY(10,$y);$this->MultiCell(40,5,$result["clasificador"]["cod"],'0','L');
					$this->SetXY(50,$y);$this->MultiCell(100,5,number_format($result["monto"],2,".", ","),'1','R');
					$this->Rect(150, $y, 50, 5);
					$tot_afec=$tot_afec+$result["monto"];
					$y=$y+5;
				}
				$this->SetXY(10,$y);$this->MultiCell(40,5,"",'1','C');
				$this->SetXY(50,$y);$this->MultiCell(100,5,"SUBTOTAL=>",'1','R');
				$this->SetXY(150,$y);$this->MultiCell(50,5,number_format($afec["monto"],2,".", ","),'1','R');
			}
			$y=$y+5;
		}
		$this->SetXY(10,$y);$this->MultiCell(40,5,"",'1','C');
		$this->SetXY(50,$y);$this->MultiCell(100,5,"TOTAL=>",'1','R');
		$this->SetXY(150,$y);$this->MultiCell(50,5,number_format($tot_afec,2,".", ","),'1','R');
		$y=$y+10;*/
		/** Contabilidad patrimonial */
		if($y+30>$page_b){
			$this->AddPage();
			$y=$y_ini;
		}
		$this->SetXY(10,$y);$this->MultiCell(190,5,"CONTABILIDAD DEL PROCESO FINANCIERO O PATRIMONIAL",'1','C');
		$y=$y+5;
		$this->SetXY(10,$y);$this->MultiCell(80,5,"CODIGO",'1','C');
		$this->SetXY(90,$y);$this->MultiCell(110,5,"IMPORTE",'1','C');
		$y=$y+5;
		$this->SetXY(10,$y);$this->MultiCell(35,5,"CUENTA MAYOR",'1','C');
		$this->SetXY(45,$y);$this->MultiCell(45,5,"SUB CUENTA",'1','C');
		$this->SetXY(90,$y);$this->MultiCell(55,5,"DEBE",'1','C');
		$this->SetXY(145,$y);$this->MultiCell(55,5,"HABER",'1','C');
		$y=$y+5;
		foreach($saldo["cont_patrimonial"] as $result){
			if($y>$page_b){
				$this->AddPage();
				$y=$y_ini;
			}
			$monto_d = "";
			$monto_h = "";
			if($result["tipo"]=="D")$monto_d=number_format($result["monto"],2,".", ",");
			if($result["tipo"]=="H")$monto_h=number_format($result["monto"],2,".", ",");
			$this->SetXY(10,$y);$this->MultiCell(35,5,substr($result["cuenta"]["cod"],0,4),'1','C');
			$this->SetXY(45,$y);$this->MultiCell(45,5,$result["cuenta"]["cod"],'1','C');
			$this->SetXY(90,$y);$this->MultiCell(55,5,$monto_d,'1','C');
			$this->SetXY(145,$y);$this->MultiCell(55,5,$monto_h,'1','C');
			$y=$y+5;
		}
		$y=$y+5;
		/** Contabilidad presupuestaria */
		$this->SetXY(20,$y);$this->MultiCell(170,5,"CONTABILIDAD DEL PROCESO PRESUPUESTARIO",'1','C');
		$y=$y+5;
		$this->SetXY(20,$y);$this->MultiCell(40,5,"CODIGO",'1','C');
		$this->SetXY(60,$y);$this->MultiCell(130,5,"IMPORTE",'1','C');
		$y=$y+5;
		$this->SetXY(20,$y);$this->MultiCell(40,5,"CUENTA MAYOR",'1','C');
		$this->SetXY(60,$y);$this->MultiCell(65,5,"DEBE",'1','C');
		$this->SetXY(125,$y);$this->MultiCell(65,5,"HABER",'1','C');
		$y=$y+5;
		foreach($saldo["cont_presupuestal"] as $result){
			if($y>$page_b){
				$this->AddPage();
				$y=$y_ini;
			}
			$monto_d = "";
			$monto_h = "";
			if($result["tipo"]=="D")$monto_d=number_format($result["monto"],2,".", ",");
			if($result["tipo"]=="H")$monto_h=number_format($result["monto"],2,".", ",");
			$this->SetXY(20,$y);$this->MultiCell(40,5,$result["cuenta"]["cod"],'1','C');
			$this->SetXY(60,$y);$this->MultiCell(65,5,$monto_d,'1','C');
			$this->SetXY(125,$y);$this->MultiCell(65,5,$monto_h,'1','C');
			$y=$y+5;
		}
		//$this->Rect(190, $y, 20, 4);		
		//$this->SetXY(170,$y);$this->MultiCell(30,5,number_format($total_pag-$total_des,2,".", ","),'0','R');
	}
	function Footer()
	{
		
	} 
	 
}

$pdf=new repo('P','mm','A4');
$pdf->AliasNbPages();
$pdf->SetMargins(10,10,0); 
$pdf->SetTitle("reporte");
$pdf->SetAutoPageBreak(false,0); 
$pdf->Open();
$pdf->filtros($filtros);
$pdf->AddPage();
$pdf->Publicar($saldo,$movs,$ultimo,$afectacion);
$pdf->SetLeftMargin(25);
//header('Content-Type: application/pdf; charset=utf-8');
$pdf->Output();

?>