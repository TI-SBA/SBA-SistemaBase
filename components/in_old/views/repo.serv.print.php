<?php
global $f;
$f->library('pdf');
class expdientes extends FPDF
{
	var $filter;
	function Filtros($filtros){
		$this->filter = $filtros;
	}
	function Header(){
		//$this->Image(IndexPath.DS.'templates/td/expd.gif',15,15,347,249);
		$this->SetFont('Arial','B',14);
		$this->SetXY(10,10);$this->MultiCell(277,5,"PAGO DE SERVICIOS: ".$this->filter["serv"]["nomb"],'0','C');
		$this->SetFont('Arial','B',9);
		$this->SetXY(10,5);$this->MultiCell(60,5,"SOCIEDAD DE BENEFICENCIA PUBLICA DE AREQUIPA",'0','C');
		$this->SetXY(10,5);$this->MultiCell(277,5,date("d/m/Y")." - Página: ".$this->PageNo()."/{nb}",'0','R');
		$this->SetFont('Arial','',7);
		$this->SetXY(10,15);$this->MultiCell(60,5,"Sistema SBPA - Módulo de Inmuebles",'0','C');
		
		$this->SetFont('Arial','B',10);
		$clie = $this->filter["arren"]["nomb"];
		if($this->filter["arren"]["tipo_enti"]=="P"){
			$clie .= " ".$this->filter["arren"]["appat"]." ".$this->filter["arren"]["apmat"];
		}
		$this->SetXY(10,20);$this->MultiCell(277,5,"RAZON SOCIAL / NOMBRES Y APELLIDOS: ".$clie,'1','L');
		$this->SetXY(10,25);$this->MultiCell(77,5,"LOCAL  /  SUBLOCAL",'1','L');
		$this->SetXY(87,25);$this->MultiCell(200,5,"DIRECCION",'1','L');
		
		$this->SetXY(10,30);$this->MultiCell(120,5,"CONTRATO",'1','C');
		$this->SetXY(130,30);$this->MultiCell(40,5,"DOCUMENTO",'1','C');
		$this->SetXY(170,30);$this->MultiCell(40,5,"IMPORTE",'1','C');
		$this->SetXY(210,30);$this->MultiCell(40,5,"FECHA",'1','C');
		$this->SetXY(250,30);$this->MultiCell(37,5,"ESTADO",'1','C');
	}
	function Publicar($items){
		global $f;
		$f->library('helpers');
		$helper=new helper();
		$monedas = array(
			"S"=>"S/.",
			"D"=>"$"
		);
		$tipo_local = array(
			"CH"=>"COMP. HABIT.",
			"ED"=>"EDIFICIO",
			"PG"=>"PROGRAMA",
			"OT"=>"OTROS"
		);
		$condicion_arrendamiento = array(
			"CT"=>"Nuevo",
			"RE"=>"Renovación",
			"CV"=>"Convenio",
			"CU"=>"Cesión en Uso",
			"CM"=>"Comodato",
			"AC"=>"Acta de Conciliación",
			"RS"=>'Por Ocupación',
			"AU"=>'Autorización',
			"PE"=>'Penalidades',
			"TR"=>'Traspaso',
			"AD"=>'Audiencias'
		);
		$tipo_arrendamiento = array(
			"P"=>"NUEVO",
			"R"=>"RENOVACION"
		);
		$estado = array(
			"P"=>"Pendiente",
			"C"=>"Cancelado",
			"X"=>"Anulado"
		);
		$tipo_comp = array(
			"R"=>"R.C.",
			"B"=>"B.V.",
			"F"=>"FACT."
		);
		$this->SetFont('arial','',10);
		$y=35;
		$y_ini = $y;
		foreach($items as $i=>$item){
			if($y>190){
				$this->AddPage();
				$y=$y_ini;
			}
			$this->SetXY(20,$y);$this->MultiCell(80,5,$item["espacio"]["ubic"]["local"]["nomb"]."    ".$item["espacio"]["descr"],'0','L');
			$y+=5;
			$this->SetXY(10,$y);$this->MultiCell(120,5,$tipo_arrendamiento[$item["arrendamiento"]["tipo"]]." ".Date::format($item["arrendamiento"]["feccon"]->sec, "d/m/Y")." - ".Date::format($item["arrendamiento"]["fecven"]->sec, "d/m/Y"),'0','C');
			$y+=5;
			foreach($item["cuentas"] as $cuenta){
				$comprobante = "";
				if(!isset($cuenta["comprobantes"])){
					foreach($cuenta["comprobantes"] as $k=>$comp){
						$comprobante .= $tipo_comp[$comp["tipo"]]."  ".$comp["serie"]." - ".$comp["num"];
						if($k>0)$comprobante.="|";
					}
				}
				$this->SetXY(130,$y);$this->MultiCell(40,5,$comprobante,'0','L');
				$this->SetXY(170,$y);$this->MultiCell(40,5,number_format($cuenta["total"],2),'0','R');
				$this->SetXY(210,$y);$this->MultiCell(40,5,Date::format($cuenta["fecven"]->sec,"d/m/Y"),'0','C');
				$this->SetXY(250,$y);$this->MultiCell(37,5,$estado[$cuenta["estado"]],'0','L');
				$y+=5;
			}
		}	
	}
	function Footer()
	{
    	//Footer de la pagina
	} 
}
$pdf=new expdientes('L','mm','A4');
$pdf->AliasNbPages();
$pdf->SetMargins(15,39.5,0); 
$pdf->SetTitle("reporte");
$pdf->SetAutoPageBreak(false,0); 
$pdf->Open();
$pdf->Filtros($filtros);
$pdf->AddPage();
$pdf->Publicar($items);
$pdf->SetLeftMargin(25);
$pdf->Output();

?>