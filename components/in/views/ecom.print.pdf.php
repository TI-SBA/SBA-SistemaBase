<?php
global $f;
$f->library('pdf');
$f->library('helpers');

/*require_once(IndexPath.'/libraries/barcode/resources/autoload.php');
$barcode = new \Com\Tecnick\Barcode\Barcode();

// generate a barcode
$bobj = $barcode->getBarcodeObj(
	'QRCODE,H',                     // barcode type and additional comma-separated parameters
	'https://tecnick.com',          // data string to encode
	-4,                             // bar height (use absolute or negative value as multiplication factor)
	-4,                             // bar width (use absolute or negative value as multiplication factor)
	'black',                        // foreground color
	array(-2, -2, -2, -2)           // padding (use absolute or negative values as multiplication factors)
	)->setBackgroundColor('black'); // background color
$bobj->getHtmlDiv();*/

class repo extends FPDF{
	var $data;
	function Filter($data){
		$this->data = $data;
	}
	function Header(){
		$this->SetFont('Arial','B',14);
		$y=10;
		$this->setXY(10,$y);$this->Image(IndexPath.'/images/logo.jpg',5,5,35,35);
		$this->setXY(130,$y);$this->MultiCell(70,14,"R.U.C. 20120212454\nFACTURA ELECTRONICA\nF001  Nº 00047479",'1','C');
		$y+=30;
		$this->SetFont('Arial','',15);
		$this->setXY(10,$y);$this->MultiCell(120,5,"Sociedad de Beneficencia Publica de Arequipa",'0','L');
		$y+=5;
		$this->SetFont('Arial','B',7);
		$this->setXY(10,$y);$this->MultiCell(30,5,"Domicilio Fiscal",'0','L');
		$this->SetFont('Arial','',7);
		$this->setXY(40,$y);$this->MultiCell(90,5,"CALLE PIEROLA, CERCADO - Arequipa",'0','L');
		$y+=5;
		$this->SetFont('Arial','B',7);
		$this->setXY(10,$y);$this->MultiCell(30,5,"SEÑOR(ES):",'0','L');
		$this->SetFont('Arial','',7);
		$this->setXY(40,$y);$this->MultiCell(90,5,"Razon social del cliente",'0','L');
		$y+=5;
		$this->SetFont('Arial','B',7);
		$this->setXY(10,$y);$this->MultiCell(60,5,"REG. UNICO DE CONTRIBUYENTES:",'0','L');
		$this->SetFont('Arial','',7);
		$this->setXY(70,$y);$this->MultiCell(60,5,"11111111111",'0','L');
		$y+=5;
		$this->SetFont('Arial','B',7);
		$this->setXY(10,$y);$this->MultiCell(30,5,"Dirección:",'0','L');
		$this->SetFont('Arial','',7);
		$this->setXY(40,$y);$this->MultiCell(90,5,"Direccion del cliente",'0','L');
		$y+=5;
		$this->SetFont('Arial','B',7);
		$this->setXY(10,$y);$this->MultiCell(30,5,"Condición:",'0','L');
		$this->SetFont('Arial','',7);
		$this->setXY(40,$y);$this->MultiCell(90,5,"FACTURA 30 DIAS",'0','L');
		$y+=10;
		$this->SetFont('Arial','',8);
		$this->setXY(10,$y);$this->MultiCell(20,10,"CODIGO",'1','C');
		$this->setXY(30,$y);$this->MultiCell(55,10,"DESCRIPCIÓN",'1','C');
		$this->setXY(85,$y);$this->MultiCell(15,5,"Unidad Medida",'1','C');
		$this->setXY(100,$y);$this->MultiCell(15,10,"Cantidad",'1','C');
		$this->setXY(115,$y);$this->MultiCell(15,5,"Valor Unitario",'1','C');
		$this->setXY(130,$y);$this->MultiCell(15,10,"IGV",'1','C');
		$this->SetFont('Arial','',7);
		$this->setXY(145,$y);$this->MultiCell(18,5,"Precio Venta Unitario",'1','C');
		$this->setXY(163,$y);$this->MultiCell(18,5,"Valor Unitario Total",'1','C');
		$this->setXY(181,$y);$this->MultiCell(19,10,"Importe Total",'1','C');
		$y+=10;
	}
	function Publicar($data){
		$monedas = array(
			"S"=>array("nomb"=>"SOLES","simb"=>"S/."),
			"D"=>array("nomb"=>"DOLARES AMERICANOS","simb"=>"US$")
		);
		$y=85;
		$y_final_items = $y+140;
		$this->SetFont('Arial','',7);
		$this->Line(10,$y,10,$y+180);
		$this->Line(30,$y,30,$y+140);
		$this->Line(85,$y,85,$y+140);
		$this->Line(100,$y,100,$y+140);
		$this->Line(115,$y,115,$y+140);
		$this->Line(130,$y,130,$y+140);
		$this->Line(145,$y,145,$y+140);
		$this->Line(163,$y,163,$y+140);
		$this->Line(181,$y,181,$y+140);
		$this->Line(200,$y,200,$y+180);

		$this->Line(10,$y+140,200,$y+140);
		$this->Line(10,$y+180,200,$y+180);

		$total_op_gratuitas = 0;
		$total_op = 0;
		$total_op_inafecta = 0;
		$total_op_gravada = 0;
		$total_tot_dscto = 0;
		$total_isc = 0;
		$total_igv = 0;
		$total_importe_total = 0;

		if(isset($data['items'])){
			if(count($data['items'])>0){
				foreach($data['items'] as $item){
					$descripcion = "";
					$valor_unitario = 0;
					$igv = 0;
					$precio_venta = 0;
					$cantidad = 1;
					$rows = array();
					if(isset($item['conceptos'])){
						foreach($item['conceptos'] as $i=>$concepto){
							if($i==0){
								$descripcion = $concepto['concepto'];
								$valor_unitario = $concepto['monto'];
								$total_op_gravada+=$valor_unitario;
							}
							if($concepto['concepto']=="IGV (18%)"){
								$igv = $concepto['monto'];
								$total_igv+=$igv;
							}
							if(substr($concepto['concepto'],0,5)=="Moras"){
								array_push($rows,array(
									"codigo"=>"000002",
									"unidad"=>"ZZ",
									"cantidad"=>1,
									"valor_unitario"=>$concepto['monto'],
									"igv"=>0,
									"precio_venta_unitario"=>$concepto['monto'],
									"valor_unitario_total"=>$concepto['monto'],
									"importe_total"=>$concepto['monto'],
									"descripcion"=>$concepto['concepto']
								));
								$total_op_inafecta+=$concepto['monto'];
								//print_r($rows);
							}
						}
					}
					$precio_venta_unitario = $valor_unitario+$igv;
					$valor_unitario_total = $valor_unitario*$cantidad;
					$importe_total = $precio_venta_unitario*$cantidad;
					$this->setXY(10,$y);$this->MultiCell(20,3,"00001",'0','C');
					$this->setXY(85,$y);$this->MultiCell(15,3,"ZZ",'0','C');
					$this->setXY(100,$y);$this->MultiCell(15,3,$cantidad,'0','C');
					$this->setXY(115,$y);$this->MultiCell(15,3,$valor_unitario,'0','R');
					$this->setXY(130,$y);$this->MultiCell(15,3,$igv,'0','R');
					$this->setXY(145,$y);$this->MultiCell(18,3,$precio_venta_unitario,'0','R');
					$this->setXY(163,$y);$this->MultiCell(18,3,$valor_unitario_total,'0','R');
					$this->setXY(181,$y);$this->MultiCell(19,3,$importe_total,'0','R');
					$this->setXY(30,$y);$this->MultiCell(55,3,$descripcion,'0','L');
					$y=$this->getY();
					if(count($rows)>0){
						foreach($rows as $row){
							$this->setXY(10,$y);$this->MultiCell(20,3,$row['codigo'],'0','C');
							$this->setXY(85,$y);$this->MultiCell(15,3,$row['unidad'],'0','C');
							$this->setXY(100,$y);$this->MultiCell(15,3,$row['cantidad'],'0','C');
							$this->setXY(115,$y);$this->MultiCell(15,3,$row['valor_unitario'],'0','R');
							$this->setXY(130,$y);$this->MultiCell(15,3,$row['igv'],'0','R');
							$this->setXY(145,$y);$this->MultiCell(18,3,$row['precio_venta_unitario'],'0','R');
							$this->setXY(163,$y);$this->MultiCell(18,3,$row['valor_unitario_total'],'0','R');
							$this->setXY(181,$y);$this->MultiCell(19,3,$row['importe_total'],'0','R');
							$this->setXY(30,$y);$this->MultiCell(55,3,$row['descripcion'],'0','L');
							$y=$this->getY();
						}
					}
				}
			}
		}

		$y = $y_final_items;
		$total = $data['total'];
		$total_importe_total = $total;
		$decimal = round((($total-((int)$total))*100),0);
		$this->SetFont('Arial','',8);
		$this->setXY(10,$y);$this->MultiCell(130,5,"SON: ".Number::lit($total).' CON '.$decimal.'/100 '.$monedas[$data["moneda"]]["nomb"],'0','L');

		$this->SetFont('Arial','B',8);
		$this->setXY(140,$y);$this->MultiCell(30,5,"OP. GRATUITAS",'0','L');
		$this->SetFont('Arial','',8);
		$this->setXY(170,$y);$this->MultiCell(30,5,$monedas[$data["moneda"]]["simb"],'0','L');
		$this->setXY(170,$y);$this->MultiCell(30,5,"0.00",'0','R');
		$y+=5;
		$this->SetFont('Arial','B',8);
		$this->setXY(140,$y);$this->MultiCell(30,5,"OP.",'0','L');
		$this->SetFont('Arial','',8);
		$this->setXY(170,$y);$this->MultiCell(30,5,$monedas[$data["moneda"]]["simb"],'0','L');
		$this->setXY(170,$y);$this->MultiCell(30,5,"0.00",'0','R');
		$y+=5;
		$this->SetFont('Arial','B',8);
		$this->setXY(140,$y);$this->MultiCell(30,5,"OP. INAFECTA",'0','L');
		$this->SetFont('Arial','',8);
		$this->setXY(170,$y);$this->MultiCell(30,5,$monedas[$data["moneda"]]["simb"],'0','L');
		$this->setXY(170,$y);$this->MultiCell(30,5,$total_op_inafecta,'0','R');
		$y+=5;
		$this->SetFont('Arial','B',8);
		$this->setXY(140,$y);$this->MultiCell(30,5,"OP. GRAVADA",'0','L');
		$this->SetFont('Arial','',8);
		$this->setXY(170,$y);$this->MultiCell(30,5,$monedas[$data["moneda"]]["simb"],'0','L');
		$this->setXY(170,$y);$this->MultiCell(30,5,$total_op_gravada,'0','R');
		$y+=5;
		$this->SetFont('Arial','B',8);
		$this->setXY(140,$y);$this->MultiCell(30,5,"TOT. DSCTO",'0','L');
		$this->SetFont('Arial','',8);
		$this->setXY(170,$y);$this->MultiCell(30,5,$monedas[$data["moneda"]]["simb"],'0','L');
		$this->setXY(170,$y);$this->MultiCell(30,5,"0.00",'0','R');
		$y+=5;
		$this->SetFont('Arial','B',8);
		$this->setXY(140,$y);$this->MultiCell(30,5,"I.S.C.",'0','L');
		$this->SetFont('Arial','',8);
		$this->setXY(170,$y);$this->MultiCell(30,5,$monedas[$data["moneda"]]["simb"],'0','L');
		$this->setXY(170,$y);$this->MultiCell(30,5,"0.00",'0','R');
		$y+=5;
		$this->SetFont('Arial','B',8);
		$this->setXY(140,$y);$this->MultiCell(30,5,"I.G.V.",'0','L');
		$this->SetFont('Arial','',8);
		$this->setXY(170,$y);$this->MultiCell(30,5,$monedas[$data["moneda"]]["simb"],'0','L');
		$this->setXY(170,$y);$this->MultiCell(30,5,$total_igv,'0','R');
		$y+=5;
		$this->SetFont('Arial','B',8);
		$this->setXY(140,$y);$this->MultiCell(30,5,"IMPORTE TOTAL",'0','L');
		$this->SetFont('Arial','',8);
		$this->setXY(170,$y);$this->MultiCell(30,5,$monedas[$data["moneda"]]["simb"],'0','L');
		$this->setXY(170,$y);$this->MultiCell(30,5,$total_importe_total,'0','R');
		$y=$y-15;
		$this->SetFont('Arial','',8);
		$this->setXY(10,$y);$this->MultiCell(130,5,'Nº GUIA:','0','L');
		$y+=5;
		$this->setXY(10,$y);$this->MultiCell(130,5,'ORDEN DE COMPRA:','0','L');
		$y+=5;
		$this->SetFont('Arial','',6);
		//$this->setXY(10,$y);$this->MultiCell(130,5,"A partir del 01 de Junio 2003 hemos sido designados Agentes de Retención según R.S. No 101-2003/SUNAT",'0','L');
		$y+=5;
		//$this->setXY(10,$y);$this->MultiCell(130,5,"A partir del 01 de Febrero del 2014 hemos sido designado Agente de Percepción según D.S. No 293-2013/EF",'0','L');
	}
	function Footer(){
		//
	}
}
$pdf=new repo('P','mm','A4');
$pdf->AliasNbPages();
$pdf->SetMargins(15,39.5,0); 
$pdf->SetTitle("reporte");
$pdf->SetAutoPageBreak(false,0); 
$pdf->Open();
$pdf->Filter($data);
$pdf->AddPage();
$pdf->Publicar($data);
$pdf->SetLeftMargin(25);
//header('Content-Type: application/pdf; charset=utf-8');
$pdf->Output();
?>
