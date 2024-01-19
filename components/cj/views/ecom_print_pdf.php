<?php
global $f;
$f->library('pdf');
Include_once IndexPath.DS."components/cj/controllers/ecom.php";
$ecom = new Controller_cj_ecom();
$config = $ecom;
class repo extends FPDF{
	var $data;
	var $ecom;
	function Filter($data, $config){
		$this->data = $data;
		$this->ecom = $config;
	}
	function Header(){
		$monedas = array(
			"PEN"=>array(
				"cod"=>"S/",
				"nomb"=>"NUEVOS SOLES"
			),
			"USD"=>array(
				"cod"=>"USD$",
				"nomb"=>"DOLARES AMERICANOS"
			)
		);
		$tipos_doc = array(
            '0'=>'SIN DOC. DE IDENTIDAD',
            'DNI'=>'DOC. NACIONAL DE IDENTIDAD',
            'CAE'=>'CARNET DE EXTRANJERIA',
            'RUC'=>'REG. NICO DE CONTRIBUYENTES',
            'PAS'=>'PASAPORTE',
            'CED'=>'CEDULA DIPLOMATICA'
        );
        $tipo = array(
        	"F"=>"FACTURA ELECTRONICA",
        	"B"=>"BOLETA ELECTRONICA",
        	"NC"=>"NOTA DE CREDITO",
        	"ND"=>"NOTA DE DEBITO"
        );
		$this->SetFont('Arial','B',14);
		$y=10;
		$this->setXY(10,$y);$this->Image('logo.jpg',5,5,60,20);
		$this->setXY(130,$y);$this->MultiCell(70,14,"R.U.C. ".$this->ecom->config('conflux-ruc')."\n".$tipo[$this->data['tipo']]."\n".$this->data['serie']."  Nº ".$this->data['num'],'1','C');
		$y+=30;
		$this->SetFont('Arial','',15);
		$this->setXY(10,$y);$this->MultiCell(120,5,$this->ecom->config('conflux-razon_social'),'0','L');
		$y+=5;
		$this->SetFont('Arial','B',7);
		$this->setXY(10,$y);$this->MultiCell(30,5,"Domicilio Fiscal",'0','L');
		$this->SetFont('Arial','',7);
		$this->setXY(40,$y);$this->MultiCell(90,5,$this->ecom->config('conflux-direccion'),'0','L');
		$y+=5;
		$this->SetFont('Arial','B',7);
		$this->setXY(10,$y);$this->MultiCell(30,5,"SEÑOR(ES):",'0','L');
		$this->SetFont('Arial','',7);
		$this->setXY(40,$y);$this->MultiCell(90,5,$this->data['cliente_nomb'],'0','L');
		$y+=5;
		$this->SetFont('Arial','B',7);
		$this->setXY(10,$y);$this->MultiCell(60,5,$tipos_doc[$this->data['tipo_doc']],'0','L');
		$this->SetFont('Arial','',7);
		$this->setXY(70,$y);$this->MultiCell(60,5,$this->data['cliente_doc'],'0','L');
		$y+=5;
		$this->SetFont('Arial','B',7);
		$this->setXY(10,$y);$this->MultiCell(30,5,"Dirección:",'0','L');
		$this->SetFont('Arial','',7);
		$this->setXY(40,$y);$this->MultiCell(90,5,$this->data['cliente_domic'],'0','L');
		/*$y+=5;
		$this->SetFont('Arial','B',7);
		$this->setXY(10,$y);$this->MultiCell(30,5,"Condición:",'0','L');
		$this->SetFont('Arial','',7);
		$this->setXY(40,$y);$this->MultiCell(90,5,"FACTURA 30 DIAS",'0','L');*/
		$y+=10;
		$this->SetFont('Arial','',8);
		$this->setXY(10,$y);$this->MultiCell(20,10,"CODIGO",'1','C');
		$this->setXY(30,$y);$this->MultiCell(60,10,"DESCRIPCIÓN",'1','C');
		$this->setXY(90,$y);$this->MultiCell(15,5,"Unidad Medida",'1','C');
		$this->setXY(105,$y);$this->MultiCell(15,10,"Cantidad",'1','C');
		$this->setXY(120,$y);$this->MultiCell(20,10,"Valor Unitario",'1','C');
		$this->setXY(140,$y);$this->MultiCell(20,10,"IGV",'1','C');
		$this->SetFont('Arial','',7);
		$this->setXY(160,$y);$this->MultiCell(20,5,"Precio Venta Unitario",'1','C');
		//$this->setXY(163,$y);$this->MultiCell(18,5,"Valor Unitario Total",'1','C');
		$this->setXY(180,$y);$this->MultiCell(20,10,"Importe Total",'1','C');
		$y+=10;
	}
	function Publicar($data){
		global $f;
		$f->library('helpers');
		$monedas = array(
			"PEN"=>array("nomb"=>"SOLES","simb"=>"S/."),
			"USD"=>array("nomb"=>"DOLARES AMERICANOS","simb"=>"US$")
		);
		$y=80;
		$y_final_items = $y+140;
		$this->SetFont('Arial','',7);
		$this->Line(10,$y,10,$y+180);
		$this->Line(30,$y,30,$y+140);
		$this->Line(90,$y,90,$y+140);
		$this->Line(105,$y,105,$y+140);
		$this->Line(120,$y,120,$y+140);
		$this->Line(140,$y,140,$y+140);
		$this->Line(160,$y,160,$y+140);
		//$this->Line(163,$y,163,$y+140);
		$this->Line(180,$y,180,$y+140);
		$this->Line(200,$y,200,$y+180);

		$this->Line(10,$y+140,200,$y+140);
		$this->Line(10,$y+180,200,$y+180);

		if(isset($data['items'])){
			if(count($data['items'])>0){
				foreach($data['items'] as $row){
					$precio_venta_unitario = $row['valor_unitario']+round($row['igv']/$row['cant'],2)+round($row['isc']/$row['cant'],2)+round($row['otros_tributos']/$row['cant'],2);
					$valor_venta = $row['valor_unitario']*$row['cant'];
					$this->setXY(10,$y);$this->MultiCell(20,3,$row['codigo'],'0','C');
					
					$this->setXY(90,$y);$this->MultiCell(15,3,$row['cod_unidad'],'0','C');
					$this->setXY(105,$y);$this->MultiCell(15,3,$row['cant'],'0','C');
					$this->setXY(120,$y);$this->MultiCell(20,3,$row['valor_unitario']-$row['descuento'],'0','R');
					$this->setXY(140,$y);$this->MultiCell(20,3,$row['igv'],'0','R');
					$this->setXY(160,$y);$this->MultiCell(20,3,$precio_venta_unitario,'0','R');
					$this->setXY(180,$y);$this->MultiCell(20,3,$precio_venta_unitario,'0','R');
					$this->setXY(30,$y);$this->MultiCell(60,3,$row['descr'],'0','L');
					$y=$this->getY();

				}
			}
		}

		$y = $y_final_items;
		$total = $data['total'];
		$total_importe_total = $total;
		//$enletras = new Enletras();
		$this->setXY(10,$y);$this->MultiCell(130,5,"SON: ".strtoupper(Number::lit($data['total'], $monedas[$data['moneda']]['nomb'])),'0','L');

		$this->SetFont('Arial','B',8);
		$this->setXY(140,$y);$this->MultiCell(30,5,"OP. GRATUITAS",'0','L');
		$this->SetFont('Arial','',8);
		$this->setXY(170,$y);$this->MultiCell(30,5,$monedas[$data["moneda"]]["simb"],'0','L');
		$this->setXY(170,$y);$this->MultiCell(30,5,$data['total_ope_gratuitas'],'0','R');
		$y+=5;
		$this->SetFont('Arial','B',8);
		$this->setXY(140,$y);$this->MultiCell(30,5,"OP. EXONERADA",'0','L');
		$this->SetFont('Arial','',8);
		$this->setXY(170,$y);$this->MultiCell(30,5,$monedas[$data["moneda"]]["simb"],'0','L');
		$this->setXY(170,$y);$this->MultiCell(30,5,$data['total_ope_exoneradas'],'0','R');
		$y+=5;
		$this->SetFont('Arial','B',8);
		$this->setXY(140,$y);$this->MultiCell(30,5,"OP. INAFECTA",'0','L');
		$this->SetFont('Arial','',8);
		$this->setXY(170,$y);$this->MultiCell(30,5,$monedas[$data["moneda"]]["simb"],'0','L');
		$this->setXY(170,$y);$this->MultiCell(30,5,$data['total_ope_inafectas'],'0','R');
		$y+=5;
		$this->SetFont('Arial','B',8);
		$this->setXY(140,$y);$this->MultiCell(30,5,"OP. GRAVADA",'0','L');
		$this->SetFont('Arial','',8);
		$this->setXY(170,$y);$this->MultiCell(30,5,$monedas[$data["moneda"]]["simb"],'0','L');
		$this->setXY(170,$y);$this->MultiCell(30,5,$data['total_ope_gravadas'],'0','R');
		$y+=5;
		$this->SetFont('Arial','B',8);
		$this->setXY(140,$y);$this->MultiCell(30,5,"TOT. DSCTO",'0','L');
		$this->SetFont('Arial','',8);
		$this->setXY(170,$y);$this->MultiCell(30,5,$monedas[$data["moneda"]]["simb"],'0','L');
		$this->setXY(170,$y);$this->MultiCell(30,5,$data['total_desc'],'0','R');
		$y+=5;
		$this->SetFont('Arial','B',8);
		$this->setXY(140,$y);$this->MultiCell(30,5,"I.S.C.",'0','L');
		$this->SetFont('Arial','',8);
		$this->setXY(170,$y);$this->MultiCell(30,5,$monedas[$data["moneda"]]["simb"],'0','L');
		$this->setXY(170,$y);$this->MultiCell(30,5,$data['total_isc'],'0','R');
		$y+=5;
		$this->SetFont('Arial','B',8);
		$this->setXY(140,$y);$this->MultiCell(30,5,"I.G.V.",'0','L');
		$this->SetFont('Arial','',8);
		$this->setXY(170,$y);$this->MultiCell(30,5,$monedas[$data["moneda"]]["simb"],'0','L');
		$this->setXY(170,$y);$this->MultiCell(30,5,$data['total_igv'],'0','R');
		$y+=5;
		$this->SetFont('Arial','B',8);
		$this->setXY(140,$y);$this->MultiCell(30,5,"IMPORTE TOTAL",'0','L');
		$this->SetFont('Arial','',8);
		$this->setXY(170,$y);$this->MultiCell(30,5,$monedas[$data["moneda"]]["simb"],'0','L');
		$this->setXY(170,$y);$this->MultiCell(30,5,$data['total'],'0','R');
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
$pdf->Filter($data,$config);
$pdf->AddPage();
$pdf->Publicar($data);
$pdf->SetLeftMargin(25);
//header('Content-Type: application/pdf; charset=utf-8');
$pdf->Output($ruta_pdf,'F');
?>