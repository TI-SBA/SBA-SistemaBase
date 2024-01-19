<?php
global $f;
$f->library('pdf');

class repo extends FPDF
{
	var $nro;
	var $mes;
	var $ano;
	function Filter($filtros){
		$this->nro = $filtros["nro"];
		$this->mes = $filtros["mes"];
		$this->ano = $filtros["ano"];
	}
	function Header(){
		$meses = array("Todos","Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Setiembre","Octubre","Noviembre","Diciembre");
		//$this->Image(IndexPath.DS.'templates/pr/presupuestos.gif',15,15,180,267);	
		$this->Rect(15, 15, 180, 30);
		$this->SetFont('Arial','B',9);
		$this->setY(15);$this->MultiCell(150,5,"SOCIEDAD DE BENEFICENCIA\nPÚBLICA DE AREQUIPA",'0','C');
		$this->setY(35);$this->MultiCell(150,5,"NOTA DE CONTABILIDAD",'0','C');
		$this->SetXY(135,30);$this->MultiCell(20,5,"NRO.",'1','C');	
		$this->SetXY(135,35);$this->MultiCell(20,5,$this->nro,'1','C');		
		$this->SetXY(155,30);$this->MultiCell(20,5,"MES",'1','C');	
		$this->SetXY(155,35);$this->MultiCell(20,5,strtoupper($meses[$this->mes]),'1','C');	
		$this->SetXY(175,30);$this->MultiCell(20,5,"AÑO",'1','C');	
		$this->SetXY(175,35);$this->MultiCell(20,5,$this->ano,'1','C');	

		$this->SetXY(15,45);$this->MultiCell(35,5,"CODIGO",'1','C');
		$this->SetXY(15,50);$this->MultiCell(10,5,"CTA.",'1','C');	
		$this->SetXY(25,50);$this->MultiCell(25,5,"DIV",'1','C');
		$this->SetXY(50,45);$this->MultiCell(85,5,"CUENTAS DEL MAYOR Y DIVISIONARIAS",'1','C');
		$this->SetXY(50,50);$this->MultiCell(85,5,"DENOMINACION",'1','C');
		$this->SetXY(135,45);$this->MultiCell(20,10,"",'1','C');
		$this->SetXY(155,45);$this->MultiCell(20,10,"DEBE",'1','C');
		$this->SetXY(175,45);$this->MultiCell(20,10,"HABER",'1','C');
	}		
	function Publicar($items){
		$x=0;
		$y=55;
		$alto = 5;
		$this->SetFont('Arial','',7);
		$total_debe = 0;
		$total_haber = 0;
		foreach($items["cuentas"] as $item){
			$str_cod = strlen($item["cuenta"]["cod"]);
			$this->Rect(15, $y, 10, 5);
			$this->Rect(25, $y, 25, 5);
			$this->Rect(50, $y, 65, 5);
			$this->Rect(115, $y, 20, 5);
			$this->Rect(135, $y, 20, 5);
			$this->Rect(155, $y, 20, 5);
			$this->Rect(175, $y, 20, 5);
			if($y>230){//230
				$this->SetXY(50,$y);$this->MultiCell(65,5,"VAN...",'0','C');
				$this->SetXY(155,$y);$this->MultiCell(20,5,number_format($total_debe,2,".", ","),'0','R');
				$this->SetXY(175,$y);$this->MultiCell(20,5,number_format($total_haber,2,".", ","),'0','R');
				$this->AddPage();
				$y=55;
				$this->Rect(15, $y, 10, 5);
				$this->Rect(25, $y, 25, 5);
				$this->Rect(50, $y, 65, 5);
				$this->Rect(115, $y, 20, 5);
				$this->Rect(135, $y, 20, 5);
				$this->Rect(155, $y, 20, 5);
				$this->Rect(175, $y, 20, 5);
				$this->SetXY(50,$y);$this->MultiCell(65,5,"VIENEN...",'0','C');
				$this->SetXY(155,$y);$this->MultiCell(20,5,number_format($total_debe,2,".", ","),'0','R');
				$this->SetXY(175,$y);$this->MultiCell(20,5,number_format($total_haber,2,".", ","),'0','R');
				$y=$y+5;
				$this->Rect(15, $y, 10, 5);
				$this->Rect(25, $y, 25, 5);
				$this->Rect(50, $y, 65, 5);
				$this->Rect(115, $y, 20, 5);
				$this->Rect(135, $y, 20, 5);
				$this->Rect(155, $y, 20, 5);
				$this->Rect(175, $y, 20, 5);
			}
			if($str_cod==4){
				$this->SetFont('Arial','B',7);
				$this->SetXY(15,$y);$this->MultiCell(10,5,$item["cuenta"]["cod"],'0','L');
				if($item["tipo"]=="D"){
					$total_debe = $total_debe + $item["monto"];
					$this->SetXY(155,$y);$this->MultiCell(20,5,number_format($item["monto"],2,".", ","),'0','R');
				}elseif($item["tipo"]=="H"){
					$total_haber = $total_haber + $item["monto"];
					$this->SetXY(175,$y);$this->MultiCell(20,5,number_format($item["monto"],2,".", ","),'0','R');
				}
			}
			if($str_cod>4){
				$this->SetFont('Arial','',7);
				$this->SetXY(25,$y);$this->MultiCell(25,5,$item["cuenta"]["cod"],'0','L');
			}
			if($str_cod>7){
				$this->SetXY(135,$y);$this->MultiCell(20,5,number_format($item["monto"],2,".", ","),'0','R');
			}			
			$this->SetXY(50,$y);$this->MultiCell(65,5,substr($item["cuenta"]["descr"],0,43),'0','L');	
			if($str_cod==7){
				$this->SetFont('Arial','B',7);
				$this->SetXY(115,$y);$this->MultiCell(20,5,number_format($item["monto"],2,".", ","),'0','R');
			}	
			$y = $y + 5;
		}
		$this->SetFont('Arial','',7);
		$glosa_t = strlen($items["concepto"]);
		$alto = ceil($glosa_t/120);
		$this->SetXY(25,$y);$this->MultiCell(150,5,$items["concepto"],'0','L');
		for($i=0;$i<$alto;$i++){
			$this->Rect(15, $y, 10, 5);
			$this->Rect(25, $y, 25, 5);
			$this->Rect(50, $y, 65, 5);
			$this->Rect(115, $y, 20, 5);
			$this->Rect(135, $y, 20, 5);
			$this->Rect(155, $y, 20, 5);
			$this->Rect(175, $y, 20, 5);
			$y+=5;
		}
		$this->Rect(15, $y, 10, 5);
		$this->Rect(25, $y, 25, 5);$this->SetXY(25,$y);$this->MultiCell(25,5,$items["post_glosa_head"]["col1"],'0','L');
		$this->Rect(50, $y, 65, 5);$this->SetXY(50,$y);$this->MultiCell(65,5,$items["post_glosa_head"]["col2"],'0','L');
		$this->Rect(115, $y, 20, 5);$this->SetXY(115,$y);$this->MultiCell(20,5,$items["post_glosa_head"]["col3"],'0','L');
		$this->Rect(135, $y, 20, 5);$this->SetXY(135,$y);$this->MultiCell(20,5,$items["post_glosa_head"]["col4"],'0','L');
		$this->Rect(155, $y, 20, 5);$this->SetXY(155,$y);$this->MultiCell(20,5,$items["post_glosa_head"]["col5"],'0','L');
		$this->Rect(175, $y, 20, 5);$this->SetXY(175,$y);$this->MultiCell(20,5,$items["post_glosa_head"]["col6"],'0','L');
		$y+=5;
		for($i=0;$i<count($items["post_glosa_body"]);$i++){
			$this->Rect(15, $y, 10, 5);
			$this->Rect(25, $y, 25, 5);$this->SetXY(25,$y);$this->MultiCell(25,5,$items["post_glosa_body"][$i]["col1"],'0','L');
			$this->Rect(50, $y, 65, 5);$this->SetXY(50,$y);$this->MultiCell(65,5,$items["post_glosa_body"][$i]["col2"],'0','L');
			$this->Rect(115, $y, 20, 5);$this->SetXY(115,$y);$this->MultiCell(20,5,$items["post_glosa_body"][$i]["col3"],'0','L');
			$this->Rect(135, $y, 20, 5);$this->SetXY(135,$y);$this->MultiCell(20,5,$items["post_glosa_body"][$i]["col4"],'0','L');
			$this->Rect(155, $y, 20, 5);$this->SetXY(155,$y);$this->MultiCell(20,5,$items["post_glosa_body"][$i]["col5"],'0','L');
			$this->Rect(175, $y, 20, 5);$this->SetXY(175,$y);$this->MultiCell(20,5,$items["post_glosa_body"][$i]["col6"],'0','L');
			$y+=5;
		}
		$this->Rect(15, $y, 10, 5);
		$this->Rect(25, $y, 25, 5);
		$this->Rect(50, $y, 65, 5);
		$this->Rect(115, $y, 20, 5);
		$this->Rect(135, $y, 20, 5);
		$this->Rect(155, $y, 20, 5);
		$this->Rect(175, $y, 20, 5);
		$this->SetFont('Arial','B',7);
		$this->SetXY(155,$y);$this->MultiCell(20,5,number_format($total_debe,2,".", ","),'0','R');
		$this->SetXY(175,$y);$this->MultiCell(20,5,number_format($total_haber,2,".", ","),'0','R');
	}
	function Footer()
	{
		$this->SetFont('Arial','B',8);
		$this->SetXY(30,240);$this->MultiCell(35,5,"........................................",'0','C');
		$this->SetXY(30,245);$this->MultiCell(35,5,"PREPARADO POR",'0','C');
		$this->SetXY(140,240);$this->MultiCell(35,5,"........................................",'0','C');
		$this->SetXY(140,245);$this->MultiCell(35,5,"CONTADOR",'0','C');
    	//Footer de la pagina
    	$this->SetXY(170,-21);
    	$this->SetFont('Arial','I',8);
    	$this->SetTextColor(128);
    	$this->Cell(0,10,"Página: ".$this->PageNo()."/{nb}",0,0,'C');
    	
    	$this->SetXY(15,-21);
    	$this->SetFont('Arial','I',8);
    	$this->SetTextColor(128);
    	$this->Cell(0,10,"Fecha de Impresión: ".date("d-m-Y"),0,0,'L');
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