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
		$this->SetXY(10,10);$this->MultiCell(190,5,"RECORD DE PAGOS (ACTAS)",'0','C');
		$this->SetFont('Arial','',11);
		$this->SetXY(10,15);$this->MultiCell(190,5,"Por acta",'0','C');
		$this->SetFont('Arial','B',9);
		$this->SetXY(10,5);$this->MultiCell(60,5,"SOCIEDAD DE BENEFICENCIA PUBLICA DE AREQUIPA",'0','C');
		$this->SetXY(10,5);$this->MultiCell(277,5,date("d/m/Y")." - Página: ".$this->PageNo()."/{nb}",'0','R');
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
		$docs = array(
			"B"=>"B.V.",
			"F"=>"FACT."
		);
		$meses = array("--","ENERO","FEBRERO","MARZO","ABRIL","MAYO","JUNIO","JULIO","AGOSTO","SETIEMBRE","OCTUBRE","NOVIEMBRE","DICIEMBRE","S/.","US$");
		$meses_2 = array("--","ENE-FEB","FEB-MAR","MAR-ABR","ABR-MAY","MAY-JUN","JUN-JUL","JUL-AGO","AGO-SET","SET-OCT","OCT-NOV","NOV-DIC","DIC-ENE","S/.","US$");
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
		$y=30;
		$y_ini = $y;
		$this->SetFont('arial','B',10);
		$this->SetXY(15,$y);$this->MultiCell(40,5,"TIPO",'1','C');
		$this->SetXY(55,$y);$this->MultiCell(40,5,"SUBLOCAL",'1','C');
		$this->SetXY(95,$y);$this->MultiCell(100,5,"DIRECCION",'1','C');
		$y+=5;
		$this->SetFont('arial','',10);
		$this->SetXY(15,$y);$this->MultiCell(40,5,$items['inmueble']['tipo']['nomb'],'1','L');
		$this->SetXY(55,$y);$this->MultiCell(40,5,$items['inmueble']['sublocal']['nomb'],'1','L');
		$this->SetXY(95,$y);$this->MultiCell(100,5,$items['inmueble']['direccion'],'1','L');
		$y+=10;
		$this->SetFont('arial','B',10);
		$this->SetXY(15,$y);$this->MultiCell(85,5,"VIGENCIA",'1','C');
		$this->SetXY(100,$y);$this->MultiCell(95,5,"NRO. ACTA",'1','C');
		$y+=5;
		$this->SetFont('arial','',10);
		$this->SetXY(15,$y);$this->MultiCell(85,5,date("d/m/Y",$items['fecini']->sec).'               '.date("d/m/Y",$items['fecfin']->sec),'1','C');
		$this->SetXY(100,$y);$this->MultiCell(95,5,$items['num'],'1','L');
		$y+=10;
		$docu = '['.$items['arrendatario']['docident'][0]['tipo'].' '.$items['arrendatario']['docident'][0]['num'].']';
		$titular = $items['arrendatario']['nomb'];
		if($items['arrendatario']['tipo_enti']=='P'){
			$titular.=' '.$items['arrendatario']['appat'].' '.$items['arrendatario']['apmat'];
		}
		$this->SetXY(15,$y);$this->MultiCell(180,5,$docu.' '.$titular,'','L');
		$y+=5;
		/*$this->SetXY(15,$y);$this->MultiCell(20,5,'AÑO','1','C');
		$this->SetXY(35,$y);$this->MultiCell(40,5,'MES','1','C');*/
		$this->SetXY(15,$y);$this->MultiCell(45,5,'VENCE','1','C');
		$this->SetXY(60,$y);$this->MultiCell(55,5,'TOTAL '.$monedas["S"],'1','C');
		$this->SetXY(115,$y);$this->MultiCell(40,5,'FECHA PAGO','1','C');
		$this->SetXY(155,$y);$this->MultiCell(40,5,'TIPO     NRO.','1','C');
		$y+=5;
		if(isset($items['items'])){
			foreach($items['items'] as $pago){
				
				
				if(isset($pago['comprobante'])){
					$this->SetXY(15,$y);$this->MultiCell(45,5,date('d/m/Y',$pago['fecven']->sec),'0','L');
					$this->SetXY(75,$y);$this->MultiCell(40,5,number_format($pago['total'],2),'0','R');
					$this->SetXY(115,$y);$this->MultiCell(40,5,date('d/m/Y',$pago['comprobante']['fecreg']->sec),'0','R');
					$this->SetXY(155,$y);$this->MultiCell(40,5,$docs[$pago['comprobante']['tipo']],'0','L');
					$this->SetXY(155,$y);$this->MultiCell(40,5,$pago['comprobante']['num'],'0','R');
					$y+=5;
				}
			}
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
//$pdf->Filtros($filter);
$pdf->AddPage();
$pdf->Publicar($items);
$pdf->SetLeftMargin(25);
$pdf->Output();

?>