<?php
global $f;
$f->library('pdf');

class repo extends FPDF
{
	var $mes;
	var $ano;
	function Filtros($filtros){
		$this->mes = $filtros["mes"];
		$this->ano = $filtros["ano"];
	}
	function Header(){
		$meses = array("Todos","ENE","FEB","MAR","ABR","MAY","JUN","JUL","AGO","SET","OCT","NOV","DIC");
		$x=5;
		$y=5;
		$this->SetFont('Arial','B',12);
		$this->setXY(10,$y);$this->MultiCell(390,5,"INVENTARIO FISICO DE ACTIVOS ".$meses[$this->mes]."-".$this->ano,'0','C');
		$y=$y+5;
		$this->SetFont('Arial','B',9);
		$this->Rect(5, $y, 15, 15);$this->setXY(5,$y);$this->MultiCell(15,7.5,"Nº de Orden",'0','C');
		$this->Rect(20, $y, 250, 5);$this->setXY(20,$y);$this->MultiCell(250,5,"ARTICULOS",'0','C');//
		$this->Rect(20, $y+5, 20, 10);$this->setXY(20,$y+5);$this->MultiCell(20,5,"CODIGO PATRIM.",'0','C');
		$this->Rect(40, $y+5, 70, 10);$this->setXY(40,$y+5);$this->MultiCell(70,10,"DESCRIPCION DEL BIEN",'0','C');
		$this->Rect(110, $y+5, 20, 10);$this->setXY(110,$y+5);$this->MultiCell(20,5,"Ubicación Fisica",'0','C');
		$this->Rect(130, $y+5, 15, 10);$this->setXY(130,$y+5);$this->MultiCell(15,5,"Unidad Medida",'0','C');
		$this->Rect(145, $y+5, 15, 10);$this->setXY(145,$y+5);$this->MultiCell(15,10,"Cant.",'0','C');
		$this->Rect(160, $y+5, 20, 5);$this->setXY(160,$y+5);$this->MultiCell(20,5,"Fec. de Adq",'0','C');//
		$this->Rect(160, $y+10, 10, 5);$this->setXY(160,$y+10);$this->MultiCell(10,5,"mes",'0','C');
		$this->Rect(170, $y+10, 10, 5);$this->setXY(170,$y+10);$this->MultiCell(10,5,"año",'0','C');
		$this->Rect(180, $y+5, 20, 10);$this->setXY(180,$y+5);$this->MultiCell(20,5,"Estado Conservac.",'0','C');
		$this->Rect(200, $y+5, 20, 10);$this->setXY(200,$y+5);$this->MultiCell(20,5,"Valor en Libros",'0','C');
		$this->Rect(220, $y+5, 15, 10);$this->setXY(220,$y+5);$this->MultiCell(15,5,"Factor Ajuste",'0','C');
		$this->Rect(235, $y+5, 15, 10);$this->setXY(235,$y+5);$this->MultiCell(15,5,"Valor Ajustad",'0','C');
		$this->Rect(250, $y+5, 20, 10);$this->setXY(250,$y+5);$this->MultiCell(20,5,"Nuevo Val_lib",'0','C');
		$this->Rect(270, $y, 125, 5);
		$this->Rect(270, $y+5, 20, 10);$this->setXY(270,$y+5);$this->MultiCell(20,5,"Deprec. En Libros",'0','C');
		$this->Rect(290, $y+5, 20, 10);$this->setXY(290,$y+5);$this->MultiCell(20,5,"Deprec. Periodo",'0','C');
		$this->Rect(310, $y+5, 20, 10);$this->setXY(310,$y+5);$this->MultiCell(20,5,"Deprec. Acumulada",'0','C');
		$this->Rect(330, $y+5, 15, 10);$this->setXY(330,$y+5);$this->MultiCell(15,5,"Deprec. Ajustad",'0','C');
		$this->Rect(345, $y+5, 15, 10);$this->setXY(345,$y+5);$this->MultiCell(15,5,"Dife. Ajuste",'0','C');
		$this->Rect(360, $y+5, 15, 10);$this->setXY(360,$y+5);$this->MultiCell(15,5,"Dprec_perl Ajus.",'0','C');
		$this->Rect(375, $y+5, 20, 10);$this->setXY(375,$y+5);$this->MultiCell(20,10,"Valor Neto",'0','C');
	}		
	function Publicar($items){
		$y=30;
		$page_b = 270;
		$y_ini = $y;
		$this->SetFont('Arial','',8);
		$t_1 = 0;
		$t_2 = 0;
		$t_3 = 0;
		$t_4 = 0;
		$t_5 = 0;
		$t_6 = 0;
		$t_7 = 0;
		$t_8 = 0;
		$t_9 = 0;
		$t_10 = 0;
		$t_11 = 0;
		$index = 0;
		foreach($items as $item){
			$dep_lib = "";
			$dep_per = "";
			$dep_acu = "";
			foreach($item["depreciacion"] as $dep){
				if((floatval(Date::format($dep["periodo"]->sec,"m"))==floatval($this->mes))&&(floatval(Date::format($dep["periodo"]->sec,"Y"))==floatval($this->ano))){
					$dep_lib = $dep["total"];
					$dep_per = $dep["porc"];
					$dep_acu = $dep["acumulado"];
				}
			}
			$index++;
			$this->setXY(5,$y);$this->MultiCell(15,5,$index,'0','C');
			$this->setXY(20,$y);$this->MultiCell(20,5,$item["cod"],'0','C');
			$this->setXY(40,$y);$this->MultiCell(70,5,$item["producto"]["nomb"],'0','C');
			$this->setXY(110,$y);$this->MultiCell(20,5,$item["ubicacion"],'0','C');
			$this->setXY(130,$y);$this->MultiCell(15,5,$item["producto"]["unidad"]["nomb"],'0','C');
			$this->setXY(145,$y);$this->MultiCell(15,5,"Cant.",'0','C');
			$this->setXY(160,$y);$this->MultiCell(10,5,Date::format($item["entrada"]["fec"]->sec,"m"),'0','C');
			$this->setXY(170,$y);$this->MultiCell(10,5,Date::format($item["entrada"]["fec"]->sec,"Y"),'0','C');
			$this->setXY(180,$y);$this->MultiCell(20,5,$item["conservacion"],'0','C');
			$this->setXY(200,$y);$this->MultiCell(20,5,number_format($item["valor_inicial"],2),'0','C');
			$this->setXY(220,$y);$this->MultiCell(15,5,"0.00",'0','C');
			$this->setXY(235,$y);$this->MultiCell(15,5,"0.00",'0','C');
			$this->setXY(250,$y);$this->MultiCell(20,5,"falta",'0','C');
			$this->setXY(270,$y);$this->MultiCell(20,5,$dep_lib,'0','C');
			$this->setXY(290,$y);$this->MultiCell(20,5,$dep_per,'0','C');
			$this->setXY(310,$y);$this->MultiCell(20,5,$dep_acu,'0','C');
			$this->setXY(330,$y);$this->MultiCell(15,5,"0.00",'0','C');
			$this->setXY(345,$y);$this->MultiCell(15,5,"0.00",'0','C');
			$this->setXY(360,$y);$this->MultiCell(15,5,"0.00",'0','C');
			$this->setXY(375,$y);$this->MultiCell(20,5,number_format($item["valor_actual"],2),'0','C');
			$y=$y+5;
		}
		$this->setXY(5,$y);$this->MultiCell(195,5,"TOTAL S/.",'1','C');
		$this->setXY(200,$y);$this->MultiCell(20,5,number_format($t_1,2),'0','C');
		$this->setXY(220,$y);$this->MultiCell(15,5,number_format($t_2,2),'0','C');
		$this->setXY(235,$y);$this->MultiCell(15,5,number_format($t_3,2),'0','C');
		$this->setXY(250,$y);$this->MultiCell(20,5,number_format($t_4,2),'0','C');
		$this->setXY(270,$y);$this->MultiCell(20,5,number_format($t_5,2),'0','C');
		$this->setXY(290,$y);$this->MultiCell(20,5,number_format($t_6,2),'0','C');
		$this->setXY(310,$y);$this->MultiCell(20,5,number_format($t_7,2),'0','C');
		$this->setXY(330,$y);$this->MultiCell(15,5,number_format($t_8,2),'0','C');
		$this->setXY(345,$y);$this->MultiCell(15,5,number_format($t_9,2),'0','C');
		$this->setXY(360,$y);$this->MultiCell(15,5,number_format($t_10,2),'0','C');
		$this->setXY(375,$y);$this->MultiCell(20,5,number_format($t_11,2),'0','C');
		$y=$y+30;
		$this->Line(30, $y, 100, $y);$this->setXY(30,$y);$this->MultiCell(70,5,"Director General de Administracion",'0','C');
		$this->Line(160, $y, 230, $y);$this->setXY(160,$y);$this->MultiCell(70,5,"Jefe de Control Patrimonial",'0','C');
		$this->Line(290, $y, 360, $y);$this->setXY(290,$y);$this->MultiCell(70,5,"Jefe de la unidad de Inventarios",'0','C');
		//$this->Rect(10, $y, 15, 5);$this->setXY(10,$y);$this->MultiCell(15,5,Date::format($item["fecreg"]->sec, 'd'),'0','L');
	}
	function Footer()
	{
		$this->SetXY(360,-10);
    	$this->SetFont('Arial','I',8);
    	$this->SetTextColor(128);
    	$this->Cell(30,10,'Página: '.$this->PageNo().'/{nb}',0,0,'L');
    	
    	$this->SetXY(15,-10);
    	$this->SetFont('Arial','I',8);
    	$this->SetTextColor(128);
    	$this->Cell(0,10,'Fecha de Impresión: '.date("d-m-Y"),0,0,'L');
	}  
}
$pdf=new repo('L','mm',array(280,400));
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
