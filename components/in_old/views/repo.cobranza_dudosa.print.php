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
		$meses = array("Todos","Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Setiembre","Octubre","Noviembre","Diciembre");
		//$this->Image(IndexPath.DS.'templates/td/expd.gif',15,15,347,249);
		$this->SetFont('Arial','B',14);
		$this->SetXY(10,10);$this->MultiCell(190,5,"COBRANZA DUDOSA AL ".date("d/m/Y"),'0','C');
		$this->SetFont('Arial','B',9);
		$this->SetXY(10,5);$this->MultiCell(60,5,"SOCIEDAD DE BENEFICENCIA PUBLICA DE AREQUIPA",'0','C');
		$this->SetXY(10,5);$this->MultiCell(190,5,"Página: ".$this->PageNo()."/{nb}",'0','R');
		$this->SetFont('Arial','',7);
		$this->SetXY(10,15);$this->MultiCell(60,5,"Sistema SBPA - Módulo de Inmuebles",'0','C');
		$this->SetFont('arial','B',10);
		
		//$this->SetXY(10,100);$this->MultiCell(40,5,"Nombre",'1','C');
	}
	function Publicar($items){
		global $f;
		$meses = array("Todos","Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Setiembre","Octubre","Noviembre","Diciembre");
		$est_arre = array(
			"A"=>"Activo",
			"F"=>"Finalizado"
		);
		$moneda = array(
			"S"=>"S/.",
			"D"=>"USD$"
		);
		$y=35;
		$y_ini = $y;
		$total_s = 0;
		$total_d = 0;
		foreach($items as $item){
			$this->SetFont('arial','',8);
			if($y>270){
				$this->AddPage();
				$y=35;
			}
			$deudor = $item["cliente"]["nomb"];
			$doc_tipo = "RUC";
			if($item["cliente"]["tipo_enti"]=="P"){
				$doc_tipo = "DNI";
				$deudor .= " ".$item["cliente"]["appat"]." ".$item["cliente"]["apmat"];
			}
			$num_doc = "--";
			foreach($item["cliente"]["docident"] as $doc){
				if($doc["tipo"]==$doc_tipo)$num_doc = $doc["num"];
			}
			$y_ind = $y;			
			$this->SetXY(170,$y);$this->MultiCell(30,5,"Fec.Pago: ".Date::format($item["fecven"]->sec, "d/m/Y"),'1','C');
			$this->SetXY(10,$y);$this->MultiCell(160,5,"Deudor: ".$deudor." - ".$doc_tipo.": ".$num_doc,'1','L');
			$y=$this->GetY();
			$this->SetXY(10,$y);$this->MultiCell(95,5,"Espacio: ".$item["operacion"]["espacio"]["descr"]." - ".$item["operacion"]["espacio"]["ubic"]["local"]["nomb"],'0','L');
			$this->SetXY(105,$y);$this->MultiCell(95,5,"Contrato ".$est_arre[$item["operacion"]["arrendamiento"]["estado"]].": ".$item["operacion"]["arrendamiento"]["contrato"]." DEL ".Date::format($item["operacion"]["arrendamiento"]["feccon"]->sec, "d/m/Y")." AL ".Date::format($item["operacion"]["arrendamiento"]["fecven"]->sec, "d/m/Y"),'0','L');
			$y=$this->GetY();
			$this->SetFont('arial','',7);
			foreach($item["conceptos"] as $conc){		
				$this->SetXY(120,$y);$this->MultiCell(30,3,number_format($conc["saldo"],2),'0','R');
				$this->SetXY(40,$y);$this->MultiCell(80,3,$conc["concepto"]["nomb"],'0','L');
				$y=$this->GetY();
			}
			if($item["moneda"]=="S"){
				$total_s+=$item["saldo"];
			}else{
				$total_d+=$item["saldo"];
			}			
			$this->SetXY(40,$y);$this->MultiCell(80,3,"Total Deuda ".$moneda[$item["moneda"]]." ===>",'0','R');
			$this->SetXY(120,$y);$this->MultiCell(30,3,number_format($item["saldo"],2),'0','R');
			$y+=5;
			$this->Line(10, $y_ind, 10, $y);
			$this->Line(200, $y_ind, 200, $y);
			$this->Line(10, $y, 200, $y);
			$y+=3;
		}
		$this->SetXY(120,$y);$this->MultiCell(50,5,"TOTAL COBRANZA DUDOSA (S/.)",'1','C');
		$this->SetXY(170,$y);$this->MultiCell(30,5,number_format($total_s,2),'1','R');
		$y+=5;
		$this->SetXY(120,$y);$this->MultiCell(50,5,"TOTAL COBRANZA DUDOSA (USD$)",'1','C');
		$this->SetXY(170,$y);$this->MultiCell(30,5,number_format($total_d,2),'1','R');
	}
	function Footer()
	{
    	//Footer de la pagina
	} 
}
$pdf=new expdientes('P','mm','A4');
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