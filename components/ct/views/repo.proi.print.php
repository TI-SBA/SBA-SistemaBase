<?php
global $f;
$f->library('pdf');

class repo extends FPDF
{
	var $organomb;
	var $mes;
	var $ano;
	function Filter($filtros){
		$this->organomb = $filtros["organomb"];
		$this->mes = $filtros["mes"];
		$this->ano = $filtros["ano"];
	}
	function Header(){
		$meses = array("Todos","Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Setiembre","Octubre","Noviembre","Diciembre");
		//$this->Image(IndexPath.DS.'templates/pr/presupuestos.gif',15,15,180,267);	
		$this->SetFont('Arial','B',12);
		$this->setY(15);$this->MultiCell(180,5,"PROCESO PRESUPUESTARIO DE LAS SOCIEDADES DE BENEFICENCIA\n PRESUPUESTO DE  INGRESOS AÑO FISCAL - ".strtoupper($meses[$this->mes])." - ".$this->ano,'0','C');
		$this->SetFont('arial','',8);
		$this->setY(25);$this->MultiCell(180,5,"(EN NUEVOS SOLES)",'0','C');	
		$this->SetFont('arial','B',8);
		$this->setXY(5,27);$this->MultiCell(200,5,"SOCIEDAD DE BENEFICENCIA DE AREQUIPA",'0','L');
		$this->setXY(5,32);$this->MultiCell(200,5,"DEPARTAMENTO: AREQUIPA",'0','L');
		$this->setXY(5,37);$this->MultiCell(200,5,"PRIVINCIA:    AREQUIPA",'0','L');
		$this->setXY(5,42);$this->MultiCell(200,5,"DEPENDENCIA:  ".strtoupper($this->organomb),'0','L');
		$this->setXY(5,47);$this->MultiCell(100,5,"TIPO DE TRANSACCION \n SUB GENERICA - NIVEL 1 \n  SUB GENERICA - NIVEL 2 \n   ESPECIFICA - NIVEL 1 \n      ESPECIFICAL - NIVEL 2",'1','L');
		$this->Rect(105, 47, 75, 5);
		$this->setXY(105,52);$this->MultiCell(25,5,"PPTO.\nAUTORIZADO\nPIM\n2013",'1','C');
		$this->setXY(130,52);$this->MultiCell(25,10,"INGRESOS DEL MES",'1','C');
		$this->setXY(155,52);$this->MultiCell(25,20,"ACUMULADO",'1','C');
		$this->setXY(180,47);$this->MultiCell(25,5,"\nSALDO\nDE\nPRESUPUESTO\n ",'1','C');
	}		
	function Publicar($items){
		$x=0;
		$y=72;
		$y_marg = 4.8;
		$alto = 5;
		$this->SetY($y);
		$this->SetFont('arial','',7);
		$total_ppto = 0;
		$total_ingr = 0;
		$total_acum = 0;
		$total_sald = 0;
		foreach($items as $item){
			$this->SetFont('arial','BU',7);
			$this->Rect(5, $y, 100, $alto);$this->SetXY(5,$y);$this->MultiCell(100,5,"FUENTE: ".$item["cod"]." ".strtoupper($item["nomb"]),'0','L');	
			$this->Rect(105, $y, 25, $alto);$this->SetXY(105,$y);$this->MultiCell(25,5,number_format(array_sum($item["total_pim"]),2,".", ","),'0','R');	
			$this->Rect(130, $y, 25, $alto);$this->SetXY(130,$y);$this->MultiCell(25,5,number_format(array_sum($item["total_eje"]),2,".", ","),'0','R');	
			$this->Rect(155, $y, 25, $alto);$this->SetXY(155,$y);$this->MultiCell(25,5,number_format(array_sum($item["total_acu"]),2,".", ","),'0','R');
			$this->Rect(180, $y, 25, $alto);$this->SetXY(180,$y);$this->MultiCell(25,5,number_format(array_sum($item["total_pim"])-array_sum($item["total_acu"]),2,".", ","),'0','R');
			$y=$y+5;
			foreach($item["items"] as $row){
				if ($y>=265){//265
					$this->AddPage();$y=72;				
				}
				$pim = array_sum($row["pim"]);
				$eje = array_sum($row["ejecucion"]);
				$acu = array_sum($row["acumulado"]);
				$sal = $pim-$acu; 
				if(strlen($row["clasificador"]["cod"])==3){
					$total_ppto += $pim;
					$total_ingr += $eje;
					$total_acum += $acu;
					$total_sald += $sal;
				}
				if($eje==0){
					$eje = "";
				}else{
					$eje = number_format($eje,2);
				}
				if($acu==0){
					$acu = "";
				}else{
					$acu = number_format($acu,2);
				}
				if(strlen($row["clasificador"]["cod"])==1||strlen($row["clasificador"]["cod"])==3||strlen($row["clasificador"]["cod"])==5){
					$this->SetFont('arial','B',7);
				}else{
					$this->SetFont('arial','',7);
				}
				$this->Rect(5, $y, 25, $alto);$this->SetXY(5,$y);$this->MultiCell(25,5,$row["clasificador"]["cod"],'0','L');	
				$this->Rect(30, $y, 75, $alto);$this->SetXY(30,$y);$this->MultiCell(75,5,substr($row["clasificador"]["nomb"],0,48),'0','L');	
				$this->Rect(105, $y, 25, $alto);$this->SetXY(105,$y);$this->MultiCell(25,5,number_format($pim,2),'0','R');	
				$this->Rect(130, $y, 25, $alto);$this->SetXY(130,$y);$this->MultiCell(25,5,$eje,'0','R');	
				$this->Rect(155, $y, 25, $alto);$this->SetXY(155,$y);$this->MultiCell(25,5,$acu,'0','R');
				$this->Rect(180, $y, 25, $alto);$this->SetXY(180,$y);$this->MultiCell(25,5,number_format($sal,2),'0','R');
				$y=$y+5;
			}
			foreach($item["metas"] as $meta){
				$this->Rect(5, $y, 100, $alto);$this->SetXY(5,$y);$this->MultiCell(100,5,"META: ".$meta["meta"]["cod"]." ".strtoupper($meta["meta"]["nomb"]),'0','L');
				$y+=5;
				foreach($meta["items"] as $row){
					if ($y>=265){//265
						$this->AddPage();$y=72;				
					}
					$pim = array_sum($row["pim"]);
					$eje = array_sum($row["ejecucion"]);
					$acu = array_sum($row["acumulado"]);
					$sal = $pim-$acu; 
					/*if(strlen($row["clasificador"]["cod"])==3){
						$total_ppto += $pim;
						$total_ingr += $eje;
						$total_acum += $acu;
						$total_sald += $sal;
					}*/
					if($eje==0){
						$eje = "";
					}else{
						$eje = number_format($eje,2);
					}
					if($acu==0){
						$acu = "";
					}else{
						$acu = number_format($acu,2);
					}
					if(strlen($row["clasificador"]["cod"])==1||strlen($row["clasificador"]["cod"])==3||strlen($row["clasificador"]["cod"])==5){
						$this->SetFont('arial','B',7);
					}else{
						$this->SetFont('arial','',7);
					}
					$this->Rect(5, $y, 25, $alto);$this->SetXY(5,$y);$this->MultiCell(25,5,$row["clasificador"]["cod"],'0','L');	
					$this->Rect(30, $y, 75, $alto);$this->SetXY(30,$y);$this->MultiCell(75,5,substr($row["clasificador"]["nomb"],0,48),'0','L');	
					$this->Rect(105, $y, 25, $alto);$this->SetXY(105,$y);$this->MultiCell(25,5,number_format($pim,2),'0','R');	
					$this->Rect(130, $y, 25, $alto);$this->SetXY(130,$y);$this->MultiCell(25,5,$eje,'0','R');	
					$this->Rect(155, $y, 25, $alto);$this->SetXY(155,$y);$this->MultiCell(25,5,$acu,'0','R');
					$this->Rect(180, $y, 25, $alto);$this->SetXY(180,$y);$this->MultiCell(25,5,number_format($sal,2),'0','R');
					$y=$y+5;
				}
			}		
		}
		$this->SetFont('arial','B',7);
		$this->Rect(5, $y, 100, $alto);$this->SetXY(5,$y);$this->MultiCell(100,5,"TOTALES",'0','C');	
		$this->Rect(105, $y, 25, $alto);$this->SetXY(105,$y);$this->MultiCell(25,5,number_format($total_ppto,2,".", ","),'0','R');	
		$this->Rect(130, $y, 25, $alto);$this->SetXY(130,$y);$this->MultiCell(25,5,number_format($total_ingr,2,".", ","),'0','R');	
		$this->Rect(155, $y, 25, $alto);$this->SetXY(155,$y);$this->MultiCell(25,5,number_format($total_acum,2,".", ","),'0','R');
		$this->Rect(180, $y, 25, $alto);$this->SetXY(180,$y);$this->MultiCell(25,5,number_format($total_sald,2,".", ","),'0','R');
	}
	function Footer()
	{
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