<?php
global $f;
require_once APPPATH.'/libraries/Enletras.php';
$f->library('pdf');
setlocale(LC_ALL,"esp");
$ConfluxConfig = new ConfluxConfig();
$config = $ConfluxConfig;
$altura_ticket = 180;
if(count($data['items'])){
	foreach($data['items'] as $item){
		$num_char = strlen($item['descr']);
		$altura_ticket+=ceil($num_char/25)*4;
		if(isset($item['subitems'])){
			foreach ($item['subitems'] as $subitem) {
				$num_char = strlen($subitem['descr']);
				$altura_ticket+=ceil($num_char/25)*4;
			}
		}
	}
}
if(!class_exists('invoiceRepresenttationA4')){
	class invoiceRepresenttationA4 extends FPDF{
		var $data;
		var $config;
		var $y_items;
		var $y_totales;
		function Filter($data, $config){
			$this->data = $data;
			$this->config = $config;
		}
		function Rotate($angle,$x=-1,$y=-1){
			if($x==-1)
			$x=$this->x;
			if($y==-1)
			$y=$this->y;
			if($this->angle!=0)
			$this->_out('Q');
			$this->angle=$angle;
			if($angle!=0)
			{
				$angle*=M_PI/180;
				$c=cos($angle);
				$s=sin($angle);
				$cx=$x*$this->k;
				$cy=($this->h-$y)*$this->k;
				$this->_out(sprintf('q %.5f %.5f %.5f %.5f %.2f %.2f cm 1 0 0 1 %.2f %.2f cm',$c,$s,-$s,$c,$cx,$cy,-$cx,-$cy));
			}
		}
		function Header(){
			$monedas = array(
				"PEN"=>array(
					"cod"=>"S/",
					"nomb"=>"SOLES"
				),
				"USD"=>array(
					"cod"=>"USD$",
					"nomb"=>"DOLARES AMERICANOS"
				)
			);
			$tipos_doc = array(
	            '0'=>'SIN DOC. DE IDENTIDAD',
	            'DNI'=>'D.N.I.',
	            'CAE'=>'CARNET DE EXTRANJERIA',
	            'RUC'=>'R.U.C.',
	            'PAS'=>'PASAPORTE',
	            'CED'=>'CEDULA DIPLOMATICA'
	        );
	        $tipo = array(
	        	"F"=>"FACTURA ELECTRÓNICA",
	        	"B"=>"BOLETA DE VENTA ELECTRÓNICA",
	        	"NC"=>"NOTA DE CREDITO ELECTRÓNICA",
	        	"ND"=>"NOTA DE DEBITO ELECTRÓNICA"
	        );
			
			$y=0;
			//$this->setXY(10,$y);$this->Image('logo.jpg',10,10,20,20);
			$this->SetFont('Arial','B',12);
			$this->setXY(0,$y);$this->MultiCell(75,6,"R.U.C. ".$this->config->item('conflux-ruc')."\n".$tipo[$this->data['tipo']]."\n".$this->data['serie']."  Nº ".$this->data['numero'],'','C');
			$y=$this->getY()+5;
			$this->SetFont('Arial','B',8);
			$this->setXY(0,$y);$this->MultiCell(75,5,$this->config->item('conflux-razon_social'),'0','C');
			$y=$this->getY()+5;
			$this->SetFont('Arial','B',7);
			$this->setXY(0,$y);$this->MultiCell(30,3,"DOM. FISCAL",'0','L');
			$this->SetFont('Arial','',7);

			$FechaEcomp = new DateTime(date('d-m-Y',$this->data['fecemi']->sec));
			$FechaCambi1 = new DateTime("30-05-2018");
			$DirecCambi1 = "CALLE PIEROLA 201";
			$FechaCambi2 = new DateTime("06-11-2018");
			$DirecCambi2 = "CALLE GOYONECHE 339";
			$FechaCambi3 = new DateTime("27-11-2018");
			$DirecCambi3 = "CALLE GOYENECHE 339";



			if( $FechaEcomp < $FechaCambi1 ){
				$this->setXY(30,$y);$this->MultiCell(45,3,$DirecCambi1,'0','L'); 
			} else if( $FechaEcomp < $FechaCambi2 ){
				$this->setXY(30,$y);$this->MultiCell(45,3,$DirecCambi2,'0','L'); 
			} else if( $FechaEcomp < $FechaCambi3 ){
				$this->setXY(30,$y);$this->MultiCell(45,3,$DirecCambi3,'0','L'); 
			} else {
				$this->setXY(30,$y);$this->MultiCell(45,3,$this->config->item('conflux-direccion'),'0','L');
			}  

			$y=$this->getY();
			$this->SetFont('Arial','B',7);
			$this->setXY(0,$y);$this->MultiCell(30,3,"SEÑOR(ES):",'0','L');
			$this->SetFont('Arial','',7);
			$this->setXY(30,$y);$this->MultiCell(45,3,$this->data['cliente_nomb'],'0','L');
			$y=$this->getY();
			$this->SetFont('Arial','B',7);
			$this->setXY(0,$y);$this->MultiCell(35,3,$tipos_doc[$this->data['tipo_doc']],'0','L');
			$this->SetFont('Arial','',7);
			$this->setXY(35,$y);$this->MultiCell(40,3,$this->data['cliente_doc'],'0','L');
			$y=$this->getY();
			$this->SetFont('Arial','B',7);
			$this->setXY(0,$y);$this->MultiCell(30,3,'F. EMISIÓN','0','L');
			$this->SetFont('Arial','',7);
			$this->setXY(30,$y);$this->MultiCell(45,3,date('d/m/Y',$this->data['fecemi']->sec),'0','L');
			$y=$this->getY();
			$this->SetFont('Arial','B',7);
			$this->setXY(0,$y);$this->MultiCell(30,3,'F. VENCIMIENTO','0','L');
			$this->SetFont('Arial','',7);
			$this->setXY(30,$y);$this->MultiCell(45,3,date('d/m/Y',$this->data['fecven']->sec),'0','L');
			$y=$this->getY();
			$this->SetFont('Arial','B',7);
			$this->setXY(0,$y);$this->MultiCell(30,3,"DIRECCIÓN:",'0','L');
			$this->SetFont('Arial','',7);
			$this->setXY(30,$y);$this->MultiCell(45,3,$this->data['cliente_domic'],'0','L');
			$y=$this->getY();
			/*$y+=5;
			$this->SetFont('Arial','B',7);
			$this->setXY(10,$y);$this->MultiCell(30,5,"Condición:",'0','L');
			$this->SetFont('Arial','',7);
			$this->setXY(40,$y);$this->MultiCell(90,5,"FACTURA 30 DIAS",'0','L');*/
			$y+=5;
			$this->SetFont('Arial','B',8);
			$this->setXY(0,$y);$this->MultiCell(10,10,"CANT",'0','L');
			$this->setXY(10,$y);$this->MultiCell(45,10,"DESCRIPCION",'0','L');
			$this->setXY(55,$y);$this->MultiCell(20,10,"P. TOTAL",'0','R');
			$this->Line(0,$y,75,$y);
			$y=$this->getY();
			$this->Line(0,$y,75,$y);
			$this->y_items = $y;
		}
		function Publicar($data){
			$monedas = array(
				"PEN"=>array("nomb"=>"SOLES","simb"=>"S/"),
				"USD"=>array("nomb"=>"DOLARES AMERICANOS","simb"=>"US$")
			);
			$monedas_2 = array(
				"S"=>array("nomb"=>"SOLES","simb"=>"S/"),
				"D"=>array("nomb"=>"DOLARES AMERICANOS","simb"=>"US$")
			);
			$y=$this->y_items+3;
			$y_final_items = 210;
			$this->SetFont('Arial','',7);

			if(isset($data['items'])){
				if(count($data['items'])>0){
					foreach($data['items'] as $row){
						$valor_unitario = $row['valor_unitario'];
						$precio_venta_unitario = $row['precio_venta_unitario'];
						if($row['gratuito']==true){
							//$valor_unitario = $row['valor_gratuito'];
							$precio_venta_unitario = $row['valor_gratuito'];
						}
						$importe_total = round($precio_venta_unitario*$row['cant'],2);
						$this->setXY(0,$y);$this->MultiCell(10,3,$row['cant'],'0','C');
						$this->setXY(55,$y);$this->MultiCell(20,3,number_format($importe_total,2),'0','R');
						$this->setXY(10,$y);$this->MultiCell(45,3,$row['descr'],'0','L');
						$y=$this->getY();
						if(isset($row['subitems'])){
							if(count($row['subitems'])>0){
								foreach ($row['subitems'] as $subitem) {
									$valor_unitario = $subitem['valor_unitario'];
									$precio_venta_unitario = $subitem['precio_venta_unitario'];
									if($subitem['gratuito']==true){
										//$valor_unitario = $row['valor_gratuito'];
										$precio_venta_unitario = $subitem['valor_gratuito'];
									}
									$importe_total = round($precio_venta_unitario*$subitem['cant'],2);
									$this->setXY(0,$y);$this->MultiCell(10,3,$subitem['cant'],'0','C');
									$this->setXY(55,$y);$this->MultiCell(20,3,number_format($importe_total,2),'0','R');
									$this->setXY(10,$y);$this->MultiCell(45,3,$subitem['descr'],'0','L');
									$y=$this->getY();
								}
							}
						}
					}
				}
			}
			if(isset($data['observ'])){
				if($data['observ']!=''){
					$this->setXY(0,$y);$this->MultiCell(75,4,$data['observ'],'0','L');
					$y=$this->getY();
				}
			}
			$this->SetFont('Arial','B',8);
			$this->setXY(0,$y);$this->MultiCell(30,5,"OP. GRATUITAS",'0','L');
			$this->SetFont('Arial','',8);
			$this->setXY(45,$y);$this->MultiCell(30,5,$monedas[$data["moneda"]]["simb"],'0','L');
			$this->setXY(55,$y);$this->MultiCell(20,5,number_format($data['total_ope_gratuitas'],2),'0','R');
			$y+=5;
			$this->SetFont('Arial','B',8);
			$this->setXY(0,$y);$this->MultiCell(30,5,"OP. EXONERADA",'0','L');
			$this->SetFont('Arial','',8);
			$this->setXY(45,$y);$this->MultiCell(30,5,$monedas[$data["moneda"]]["simb"],'0','L');
			$this->setXY(55,$y);$this->MultiCell(20,5,number_format($data['total_ope_exoneradas'],2),'0','R');
			$y+=5;
			$this->SetFont('Arial','B',8);
			$this->setXY(0,$y);$this->MultiCell(30,5,"OP. INAFECTA",'0','L');
			$this->SetFont('Arial','',8);
			$this->setXY(45,$y);$this->MultiCell(30,5,$monedas[$data["moneda"]]["simb"],'0','L');
			$this->setXY(55,$y);$this->MultiCell(20,5,number_format($data['total_ope_inafectas'],2),'0','R');
			$y+=5;
			$this->SetFont('Arial','B',8);
			$this->setXY(0,$y);$this->MultiCell(30,5,"OP. GRAVADA",'0','L');
			$this->SetFont('Arial','',8);
			$this->setXY(45,$y);$this->MultiCell(30,5,$monedas[$data["moneda"]]["simb"],'0','L');
			$this->setXY(55,$y);$this->MultiCell(20,5,number_format($data['total_ope_gravadas'],2),'0','R');
			$y+=5;
			$this->SetFont('Arial','B',8);
			$this->setXY(0,$y);$this->MultiCell(30,5,"TOT. DSCTO",'0','L');
			$this->SetFont('Arial','',8);
			$this->setXY(45,$y);$this->MultiCell(30,5,$monedas[$data["moneda"]]["simb"],'0','L');
			$this->setXY(55,$y);$this->MultiCell(20,5,number_format($data['total_desc'],2),'0','R');
			$y+=5;
			$this->SetFont('Arial','B',8);
			$this->setXY(0,$y);$this->MultiCell(30,5,"I.S.C.",'0','L');
			$this->SetFont('Arial','',8);
			$this->setXY(45,$y);$this->MultiCell(30,5,$monedas[$data["moneda"]]["simb"],'0','L');
			$this->setXY(55,$y);$this->MultiCell(20,5,number_format($data['total_isc'],2),'0','R');
			$y+=5;
			$this->SetFont('Arial','B',8);
			$this->setXY(0,$y);$this->MultiCell(30,5,"I.G.V.",'0','L');
			$this->SetFont('Arial','',8);
			$this->setXY(45,$y);$this->MultiCell(30,5,$monedas[$data["moneda"]]["simb"],'0','L');
			$this->setXY(55,$y);$this->MultiCell(20,5,number_format($data['total_igv'],2),'0','R');
			$y+=5;
			$this->SetFont('Arial','B',8);
			$this->setXY(0,$y);$this->MultiCell(30,5,"IMPORTE TOTAL",'0','L');
			$this->SetFont('Arial','',8);
			$this->setXY(45,$y);$this->MultiCell(30,5,$monedas[$data["moneda"]]["simb"],'0','L');
			$this->setXY(55,$y);$this->MultiCell(20,5,number_format($data['total'],2),'0','R');
			$y+=5;
			$this->Line(0,$y,75,$y);
			$y+=5;
			if(isset($data['efectivos'])){
				if(floatval($data['efectivos'][0]['monto'])!=0){
					$this->SetFont('Arial','B',8);
					$this->setXY(0,$y);$this->MultiCell(30,5,"EFECTIVO",'0','L');
					$this->SetFont('Arial','',8);
					//$this->setXY(45,$y);$this->MultiCell(30,5,$monedas_2[$data['efectivos'][0]["moneda"]]["simb"],'0','L');
					$this->setXY(45,$y);$this->MultiCell(30,5,$monedas_2[$data['efectivos'][0]["moneda"]]["simb"],'0','L');
					$this->setXY(55,$y);$this->MultiCell(20,5,number_format($data['efectivos'][0]['monto'],2),'0','R');
					$y+=5;
				}
				if(floatval($data['efectivos'][1]['monto'])!=0){
					$this->SetFont('Arial','B',8);
					$this->setXY(0,$y);$this->MultiCell(30,5,"EFECTIVO",'0','L');
					$this->SetFont('Arial','',8);
					$this->setXY(45,$y);$this->MultiCell(30,5,$monedas_2[$data['efectivos'][1]["moneda"]]["simb"],'0','L');
					$this->setXY(55,$y);$this->MultiCell(20,5,number_format($data['efectivos'][1]['monto'],2),'0','R');
					$y+=5;
				}
			}
			if(isset($data['total_detraccion'])){
				if(floatval($data['total_detraccion'])>0){
					$this->SetFont('Arial','B',8);
					$this->setXY(0,$y);$this->MultiCell(30,5,"DETRACCION",'0','L');
					$this->SetFont('Arial','',8);
					$this->setXY(45,$y);$this->MultiCell(30,5,$monedas["PEN"]["simb"],'0','L');
					$this->setXY(55,$y);$this->MultiCell(20,5,number_format($data['total_detraccion'],2),'0','R');
					$y+=5;	
				}
			}
			$total = $data['total'];
			$total_importe_total = $total;
			$enletras = new Enletras();
			$this->SetFont('Arial','',7);
			$this->setXY(0,$y);$this->MultiCell(75,5,"SON: ".strtoupper($enletras->ValorEnLetras($data['total'], $monedas[$data['moneda']]['nomb'])),'0','L');
			$y = $this->getY();
			$this->SetFont('Arial','',8);
			if(isset($data['porcentaje_detraccion'])){
				if($data['porcentaje_detraccion']!=''){
					$detraccion = '';
					switch ($data['porcentaje_detraccion']) {
						case '12':
							$detraccion = 'Operación Sujeta a Detracción Del 12% Decreto legislativo 940. BANCO DE LA NACION CTA. NRO. 101-089983';
							break;
						case '10':
							$detraccion = 'Operación Sujeta a Detracción Del 10% Decreto legislativo 940. BANCO DE LA NACION CTA. NRO. 101-089983';
							break;
						case '4':
							$detraccion = 'Operación Sujeta a Detracción Del 4% Decreto legislativo 940. BANCO DE LA NACION CTA. NRO. 101-089983';
							break;
					}
					$this->setXY(0,$y);$this->MultiCell(75,4,$detraccion,'0','L');
					$y=$this->getY();
				}
			}
			$this->y_totales = $y;
			//$this->setXY(10,$y);$this->MultiCell(130,5,"A partir del 01 de Febrero del 2014 hemos sido designado Agente de Percepción según D.S. No 293-2013/EF",'0','L');
		}
		function Footer(){
			$tipo = array(
		        	"F"=>"FACTURA ELECTRÓNICA",
		        	"B"=>"BOLETA DE VENTA ELECTRÓNICA",
		        	"NC"=>"NOTA DE CREDITO ELECTRÓNICA",
		        	"ND"=>"NOTA DE DEBITO ELECTRÓNICA"
		        );
			$y=$this->y_totales+5;
			$this->SetFont('Arial','',8);
			//$this->setXY(0,$y);$this->MultiCell(75,4,"Representación impresa de la ".$tipo[$this->data['tipo']].", para consultar el documento visita ".$this->config->item('conflux-pagina_web'),'0','C');
			$this->setXY(0,$y);$this->MultiCell(75,4,"Representación impresa de la ".$tipo[$this->data['tipo']].", para consultar el documento visita https://facturacion.sbparequipa.gob.pe/",'0','C');
			$y=$this->getY();
			$this->setXY(0,$y);$this->MultiCell(75,4,"Autorizado mediante Resolución de Intendencia No ".$this->config->item('conflux-resolucion_intendencia'),'0','C');
			$y=$this->getY();
			$this->setXY(0,$y);$this->MultiCell(75,4,"Resumen: ".$this->data['digest_value'],'0','C');
			$y=$this->getY();
			if($this->data['codigo_barras']!=''){
				//echo 'asdsad';
				//print_r(site_url('barcode/generar/?data='.utf8_encode($this->data['codigo_barras'])));
				//echo base64_encode($this->data['codigo_barras']);
				//$this->Image('https://127.0.0.1/ayuda/barcode.php?data='.base64_encode($this->data['codigo_barras']).'&ext=.png',2,$y,65,20);
			}else{
				/*$this->Rotate(40);
				$this->SetTextColor(194,8,8);//194,8,8
				$this->SetFont('Arial','',60);
				$this->setXY(100,200);$this->Write(50,"SIN VALOR LEGAL");*/
			}
		}
	}
}
$pdf=new invoiceRepresenttationA4('P','mm',array(75,$altura_ticket));
$pdf->AliasNbPages();
$pdf->SetMargins(15,39.5,0); 
$pdf->SetTitle("comprobante-electronico");
$pdf->SetAutoPageBreak(false,0);
$pdf->Open();
$pdf->Filter($data,$config);
$pdf->AddPage();
$pdf->Publicar($data);
$pdf->SetLeftMargin(25);
//header('Content-Type: application/pdf; charset=utf-8');
if($ruta_pdf!=null){
	$pdf->Output($ruta_pdf,'F');
}else{
	$pdf->Output();
}
?>
