<?php
global $f;
$f->library('pdf');

class repo extends FPDF
{
	var $orga;
	var $cuenmay;
	var $subcuen;
	var $cuenmay_cod;
	var $subcuen_cod;
	var $arren;
	var $inmu;
	function filtros($filtros){
		$this->orga = $filtros["organizacion"]["nomb"];
		$this->cuenmay = $filtros["cuenta_mayor"]["descr"];
		$this->cuenmay_cod = $filtros["cuenta_mayor"]["cod"];
		$this->subcuen = $filtros["sub_cuenta"]["descr"];
		$this->subcuen_cod = $filtros["sub_cuenta"]["cod"];
		$this->arren = $filtros["arrendatario"];
		$this->inmu = $filtros["inmueble"]["nomb"];
	}
	function Header(){
		//$this->Image(IndexPath.DS.'templates/ts/comprobante.gif',10,10,190,275);	
		$y=10;
		$this->SetFont('Arial','B',17);
		$this->SetXY(60,$y);$this->MultiCell(100,5,"AUXILIAR ESTANDAR",'0','C');	
		$y=$y+10;
		$this->SetFont('Arial','B',12);
		if($this->orga!=null){
			$this->SetXY(70,$y);$this->MultiCell(100,5,"PROGRAMA: ".$this->orga,'0','L');
		}
		$this->SetFont('Arial','B',10);
		$this->SetXY(230,$y);$this->MultiCell(60,5,"CÓDIGO: ".$this->cuenmay_cod,'1','L');
		$this->SetXY(230,$y+5);$this->MultiCell(60,5,"CÓDIGO: ".$this->subcuen_cod,'1','L');
		$y=$y+5;
		$this->SetFont('Arial','B',10);
		$this->SetXY(70,$y);$this->MultiCell(150,5,"CUENTA DE MAYOR: ".$this->cuenmay,'0','L');
		$y=$y+5;
		$this->SetXY(70,$y);$this->MultiCell(150,5,"SUB CUENTA: ".$this->subcuen,'0','L');
		if($this->arren!=null){
			$arren = $this->arren["nomb"];
			if($this->arren["tipo_enti"]=="P"){
				$arren = $this->arren["nomb"]." ".$this->arren["appat"]." ".$this->arren["apmat"];
			}
			$this->SetXY(70,$y+5);$this->MultiCell(150,5,$arren."      ".$this->inmu,'0','L');
		}
		$y=$y+10;
		$this->SetXY(5,$y);$this->MultiCell(35,5,"FECHA",'1','C');
		$this->SetXY(5,$y+5);$this->MultiCell(25,5,"MES",'1','C');
		$this->SetXY(30,$y+5);$this->MultiCell(10,5,"DIA",'1','C');
		$this->SetXY(40,$y);$this->MultiCell(40,5,"COMPROBANTE",'1','C');
		$this->SetXY(40,$y+5);$this->MultiCell(20,5,"CLASE",'1','C');
		$this->SetXY(60,$y+5);$this->MultiCell(20,5,"Nº",'1','C');
		$this->SetXY(80,$y);$this->MultiCell(135,10,"DETALLE",'1','C');
		$this->SetXY(215,$y);$this->MultiCell(75,5,"MOVIMIENTO",'1','C');
		$this->SetXY(215,$y+5);$this->MultiCell(25,5,"DEBE",'1','C');
		$this->SetXY(240,$y+5);$this->MultiCell(25,5,"HBAER",'1','C');
		$this->SetXY(265,$y+5);$this->MultiCell(25,5,"SALDO",'1','C');
	}		
	function Publicar($saldos,$items){
		$monedas = array(
			"S"=>array("simb"=>"S/.","nomb"=>"NUEVO SOL","plu"=>"NUEVOS SOLES"),
			"D"=>array("simb"=>"USSD $.","nomb"=>"DOLAR","plu"=>"DOLARES")
		);
		$meses = array("Todos","Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Setiembre","Octubre","Noviembre","Diciembre");
		$clase = array(
			"CP"=>"C. P.",
			"NC"=>"N. C.",
			"OS"=>"O. S.",
			"OC"=>"O. C.",
			"RI"=>"R. I.",
			"PC"=>"P. C."
		);
		$x=5;
		$y=50;
		$y_ini = $y;
		$page_b = 275;
		$this->SetFont('Arial','',9);
		$debe = $saldos["debe_inicial"];
		$haber = $saldos["haber_inicial"];
		$saldo = $debe - $haber;
		
		$this->Rect(5, $y, 25, 5);
		$this->Rect(30, $y, 10, 5);
		$this->Rect(40, $y, 20, 5);
		$this->Rect(60, $y, 20, 5);
		$this->Rect(80, $y, 135, 5);
		$this->Rect(215, $y, 25, 5);$this->SetXY(215,$y);$this->MultiCell(25,5,number_format($debe,2,".", ","),'0','R');
		$this->Rect(240, $y, 25, 5);$this->SetXY(240,$y);$this->MultiCell(25,5,number_format($haber,2,".", ","),'0','R');
		$this->Rect(265, $y, 25, 5);$this->SetXY(265,$y);$this->MultiCell(25,5,number_format($saldo,2,".", ","),'0','R');	
		$y=$y+5;
		foreach($items as $item){
			if($y>185){
				$this->Rect(5, $y, 25, 5);
				$this->Rect(30, $y, 10, 5);
				$this->Rect(40, $y, 20, 5);
				$this->Rect(60, $y, 20, 5);
				$this->Rect(80, $y, 135, 5);$this->SetXY(80,$y);$this->MultiCell(135,5,"VAN",'0','L');
				$this->Rect(215, $y, 25, 5);$this->SetXY(215,$y);$this->MultiCell(25,5,number_format($debe,2,".", ","),'0','R');
				$this->Rect(240, $y, 25, 5);$this->SetXY(240,$y);$this->MultiCell(25,5,number_format($haber,2,".", ","),'0','R');
				$this->Rect(265, $y, 25, 5);$this->SetXY(265,$y);$this->MultiCell(25,5,number_format($saldo,2,".", ","),'0','R');			
				$this->AddPage();
				$y=$y_ini;
				$this->Rect(5, $y, 25, 5);
				$this->Rect(30, $y, 10, 5);
				$this->Rect(40, $y, 20, 5);
				$this->Rect(60, $y, 20, 5);
				$this->Rect(80, $y, 135, 5);$this->SetXY(80,$y);$this->MultiCell(135,5,"VIENEN",'0','L');
				$this->Rect(215, $y, 25, 5);$this->SetXY(215,$y);$this->MultiCell(25,5,number_format($debe,2,".", ","),'0','R');
				$this->Rect(240, $y, 25, 5);$this->SetXY(240,$y);$this->MultiCell(25,5,number_format($haber,2,".", ","),'0','R');
				$this->Rect(265, $y, 25, 5);$this->SetXY(265,$y);$this->MultiCell(25,5,number_format($saldo,2,".", ","),'0','R');	
				$y=$y+5;
			}
			$this->Rect(5, $y, 25, 5);$this->SetXY(5,$y);$this->MultiCell(25,5,$meses[floatval(Date::format($item["fec"]->sec, 'm'))],'0','L');
			$this->Rect(30, $y, 10, 5);$this->SetXY(30,$y);$this->MultiCell(10,5,Date::format($item["fec"]->sec, 'd'),'0','C');
			$this->Rect(40, $y, 20, 5);$this->SetXY(40,$y);$this->MultiCell(20,5,$clase[$item["clase"]],'0','C');
			$this->Rect(60, $y, 20, 5);$this->SetXY(60,$y);$this->MultiCell(20,5,$item["num"],'0','C');
			$this->Rect(80, $y, 135, 5);$this->SetXY(80,$y);$this->MultiCell(135,5,substr($item["detalle"],0,120),'0','L');
			$this->Rect(215, $y, 25, 5);
			$this->Rect(240, $y, 25, 5);
			if($item["tipo"]=="D"){
				$debe = $debe + $item["monto"];
				$saldo = $saldo + $item["monto"];
				$this->SetXY(215,$y);$this->MultiCell(25,5,number_format($item["monto"],2,".", ","),'0','R');
			}else{
				$haber = $haber + $item["monto"];
				$saldo = $saldo - $item["monto"];
				$this->SetXY(240,$y);$this->MultiCell(25,5,number_format($item["monto"],2,".", ","),'0','R');
			}
			$this->Rect(265, $y, 25, 5);$this->SetXY(265,$y);$this->MultiCell(25,5,number_format($saldo,2,".", ","),'0','R');	
			$y=$y+5;
		}
		$this->Rect(5, $y, 25, 5);
		$this->Rect(30, $y, 10, 5);
		$this->Rect(40, $y, 20, 5);
		$this->Rect(60, $y, 20, 5);
		$this->Rect(80, $y, 135, 5);
		$this->Rect(215, $y, 25, 5);$this->SetXY(215,$y);$this->MultiCell(25,5,number_format($debe,2,".", ","),'0','R');
		$this->Rect(240, $y, 25, 5);$this->SetXY(240,$y);$this->MultiCell(25,5,number_format($haber,2,".", ","),'0','R');
		$this->Rect(265, $y, 25, 5);$this->SetXY(265,$y);$this->MultiCell(25,5,number_format($saldo,2,".", ","),'0','R');
	}
	function Footer()
	{
		
	} 
	 
}

$pdf=new repo('L','mm','A4');
$pdf->AliasNbPages();
$pdf->SetMargins(10,10,0); 
$pdf->SetTitle("reporte");
$pdf->SetAutoPageBreak(false,0); 
$pdf->Open();
$pdf->filtros($saldo);
$pdf->AddPage();
$pdf->Publicar($saldo,$items);
$pdf->SetLeftMargin(25);
//header('Content-Type: application/pdf; charset=utf-8');
$pdf->Output();

?>