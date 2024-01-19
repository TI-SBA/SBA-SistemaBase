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
		$this->SetXY(10,10);$this->MultiCell(190,5,"FICHA DE INMUEBLE",'0','C');
		$this->SetFont('Arial','B',9);
		$this->SetXY(10,5);$this->MultiCell(60,5,"SOCIEDAD DE BENEFICENCIA PUBLICA DE AREQUIPA",'0','C');
		$this->SetXY(10,5);$this->MultiCell(190,5,date("d/m/Y")." - Página: ".$this->PageNo()."/{nb}",'0','R');
		$this->SetFont('Arial','',7);
		$this->SetXY(10,15);$this->MultiCell(60,5,"Sistema SBPA - Módulo de Inmuebles",'0','C');
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
		$uso_espacio = array(
			"TI"=>"Tiendas",
			"OF"=>"Oficina",
			"HO"=>"Hotel",
			"ST"=>"Stand",
			"CI"=>"Cine",
			'ES'=>'Espacio',
			'CO'=>'Cochera',
			'VI'=>'Casa - Habitación',
			'OT'=>'Otros'
		);
		$estado_conservacion = array(
			"B"=>"Bueno",
			"R"=>"Regular",
			"M"=>"Malo"
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
		$this->SetXY(15,$y);$this->MultiCell(180,5,"INMUEBLE MATRIZ: ".$items["ubic"]["local"]["nomb"],'0','L');
		$y+=5;
		$this->SetXY(15,$y);$this->MultiCell(180,5,"DIRECCION: ".$items["ubic"]["local"]["direc"],'0','L');
		$y+=5;	
		$this->SetXY(15,$y);$this->MultiCell(180,5,"DESCRIPCION: ".$items["descr"],'0','L');
		$y+=5;
		$this->SetXY(15,$y);$this->MultiCell(180,5,"REFERENCIA: ".$items["ubic"]["ref"],'0','L');
		$y+=5;
		$this->SetXY(15,$y);$this->MultiCell(180,5,"REGISTRADO: ".Date::format($items["fecreg"]->sec,"d/m/Y H:i"),'0','L');
		$y+=5;
		$this->SetXY(15,$y);$this->MultiCell(180,5,"USO: ".$uso_espacio[$items["uso"]],'0','L');
		$y+=5;
		$this->SetXY(15,$y);$this->MultiCell(180,5,"CONSERVACION: ".$estado_conservacion[$items["conserv"]],'0','L');
		$y+=5;
		$this->SetXY(15,$y);$this->MultiCell(180,5,"HABILITADO: ".(($items["habilitado"]==true)?"SI":"NO"),'0','L');
		$y+=5;
		$this->SetXY(15,$y);$this->MultiCell(180,5,"ESTADO: ".(($items["ocupado"]==true)?"OCUPADO":"DESOCUPADO"),'0','L');
		$y+=5;
		$this->SetXY(15,$y);$this->MultiCell(180,5,"RENTA BASE: ".$items["valor"]["renta"],'0','L');
		$y+=5;
		$this->SetXY(15,$y);$this->MultiCell(180,5,"GARANTIA: ".$items["valor"]["garantia"],'0','L');
		$y+=5;
		$this->SetXY(15,$y);$this->MultiCell(180,5,"AREA DEL TERRENO: ".$items["area_terreno"],'0','L');
		$y+=5;
		$this->SetXY(15,$y);$this->MultiCell(180,5,"AREA CONSTRUIDA: ".$items["area_construida"],'0','L');
		$y+=5;
		$this->SetXY(15,$y);$this->MultiCell(180,5,"NUMERO DE MEDIDOR DE AGUA: ".$items["medidor_agua"],'0','L');
		$y+=5;
		$this->SetXY(15,$y);$this->MultiCell(180,5,"NUMERO DE MEDIDOR DE LUZ: ".$items["medidor_luz"],'0','L');
		$y+=5;
		$this->SetXY(15,$y);$this->MultiCell(180,5,"CODIGO DE ARBITRIOS: ".$items["cod_arbitrios"],'0','L');
		$y+=5;
		if(isset($items["arrendatario"])){
			$arren = $items["arrendatario"]["nomb"];
			if($items["arrendatario"]["tipo_enti"]=="P"){
				$arren .= " ".$items["arrendatario"]["appat"]." ".$items["arrendatario"]["apmat"];
			}
			$this->SetXY(15,$y);$this->MultiCell(37,5,"ARRENDATARIO: ".$arren,'0','L');
		}
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