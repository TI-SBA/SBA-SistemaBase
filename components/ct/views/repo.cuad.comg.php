<?php
global $f;
$f->library('pdf');

class repo extends FPDF
{
	var $mes;
	var $ano;
	function Filter($filtros){
		$this->mes = $filtros["mes"];
		$this->ano = $filtros["ano"];
	}
	function Header(){
		$meses = array("Todos","Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Setiembre","Octubre","Noviembre","Diciembre");
		//$this->Image(IndexPath.DS.'templates/pr/presupuestos.gif',15,15,180,267);	
		$this->SetFont('Arial','B',15);
		$this->setY(5);$this->Cell(0,10,"SOCIEDAD DE BENEFICENCIA PUBLICA DE AREQUIPA",0,0,'C');
		$this->SetFont('Arial','I',13);
		$this->setY(10);$this->Cell(0,10,"CUADRO DE COMPROMISOS Y GASTOS ".strtoupper($meses[$this->mes])." - ".$this->ano,0,0,'C');
		$y=20;
		$this->SetFont('Arial','B',9);
		$this->SetXY(10,$y);$this->MultiCell(50,5,"DENOMINACIÓN",'1','C');
		$this->SetXY(60,$y);$this->MultiCell(15,5,"AD",'1','C');
		$this->SetXY(75,$y);$this->MultiCell(15,5,"AL",'1','C');
		$this->SetXY(90,$y);$this->MultiCell(15,5,"GA",'1','C');
		$this->SetXY(105,$y);$this->MultiCell(15,5,"41",'1','C');
		$this->SetXY(120,$y);$this->MultiCell(15,5,"42",'1','C');
		$this->SetXY(135,$y);$this->MultiCell(15,5,"43",'1','C');
		$this->SetXY(150,$y);$this->MultiCell(15,5,"44",'1','C');
		$this->SetXY(165,$y);$this->MultiCell(15,5,"45",'1','C');
		$this->SetXY(180,$y);$this->MultiCell(15,5,"46",'1','C');
		$this->SetXY(195,$y);$this->MultiCell(15,5,"47",'1','C');
		$this->SetXY(210,$y);$this->MultiCell(15,5,"50",'1','C');
		$this->SetXY(225,$y);$this->MultiCell(15,5,"51",'1','C');
		$this->SetFont('Arial','B',7);
		$this->SetXY(240,$y);$this->MultiCell(15,5,"PEN",'1','C');
		$this->SetFont('Arial','B',6);
		$this->SetXY(255,$y);$this->MultiCell(15,5,"REH/CONTR",'1','C');
		$this->SetFont('Arial','B',9);
		$this->SetXY(270,$y);$this->MultiCell(15,5,"TOTAL",'1','C');
	}		
	function Publicar($items){
		$x=0;
		$y=25;
		$y_ini = $y;
		$page_b = 190;
		$label = array(
			0=>"COMPROMISOS",
			1=>"EJECUCION"
		);
		//compromisos
		//$this->Rect(10, $y, 275, 5);			
		//$y = $y_ini;
		foreach($items as $k=>$ite){
			if($y>$page_b){
				$this->AddPage();
				$y=$y_ini;
			}
			$this->SetFont('Arial','B',8);
			$this->SetXY(10,$y);$this->MultiCell(275,5,$label[$k],'1','L');
			$y = $y+5;
			$total_ad = 0;
			$total_al = 0;
			$total_ga = 0;
			$total_41 = 0;
			$total_42 = 0;
			$total_43 = 0;
			$total_44 = 0;
			$total_45 = 0;
			$total_46 = 0;
			$total_47 = 0;
			$total_50 = 0;
			$total_51 = 0;
			$total_pen = 0;
			$total_rc = 0;
			foreach($items[$k][0]["items"] as $i=>$row){
				if($y>$page_b){
					$this->AddPage();
					$y=$y_ini;
					$this->SetFont('Arial','B',8);
					$this->SetXY(10,$y);$this->MultiCell(275,5,$label[$k],'1','L');
					$y = $y+5;
				}
				$this->SetFont('Arial','',6);
				$this->SetXY(10,$y);$this->MultiCell(50,5,substr($row["denominacion"],0,50),'1','L');
				$this->Rect(60, $y, 15, 5);
				$this->Rect(75, $y, 15, 5);
				$this->Rect(90, $y, 15, 5);
				$this->Rect(105, $y, 15, 5);
				$this->Rect(120, $y, 15, 5);
				$this->Rect(135, $y, 15, 5);
				$this->Rect(150, $y, 15, 5);
				$this->Rect(165, $y, 15, 5);
				$this->Rect(180, $y, 15, 5);
				$this->Rect(195, $y, 15, 5);
				$this->Rect(210, $y, 15, 5);
				$this->Rect(225, $y, 15, 5);
				$this->Rect(240, $y, 15, 5);
				$this->Rect(255, $y, 15, 5);
				$this->Rect(270, $y, 15, 5);
				$total_row = 0;
				$total = array();
				foreach($items[$k] as $item){		
					switch ($item["columna"]){
						case "AD";
							$total_ad = $total_ad + $item["items"][$i]["monto"];
							$this->SetXY(60,$y);$this->MultiCell(15,5,number_format($item["items"][$i]["monto"],2,".", ","),'0','R');						
							break;
						case "AL";
							$total_al = $total_al + $item["items"][$i]["monto"];
							$this->SetXY(75,$y);$this->MultiCell(15,5,number_format($item["items"][$i]["monto"],2,".", ","),'0','R');
							break;
						case "GA";
							$total_ga = $total_ga + $item["items"][$i]["monto"];
							$this->SetXY(90,$y);$this->MultiCell(15,5,number_format($item["items"][$i]["monto"],2,".", ","),'0','R');
							break;	
						case "41";
							$total_41 = $total_41 + $item["items"][$i]["monto"];
							$this->SetXY(105,$y);$this->MultiCell(15,5,number_format($item["items"][$i]["monto"],2,".", ","),'0','R');
							break;
						case "42";
							$total_42 = $total_42 + $item["items"][$i]["monto"];
							$this->SetXY(120,$y);$this->MultiCell(15,5,number_format($item["items"][$i]["monto"],2,".", ","),'0','R');
							break;
						case "43";
							$total_43 = $total_43 + $item["items"][$i]["monto"];
							$this->SetXY(135,$y);$this->MultiCell(15,5,number_format($item["items"][$i]["monto"],2,".", ","),'0','R');
							break;
						case "44";
							$total_44 = $total_44 + $item["items"][$i]["monto"];
							$this->SetXY(150,$y);$this->MultiCell(15,5,number_format($item["items"][$i]["monto"],2,".", ","),'0','R');
							break;
						case "45";
							$total_45 = $total_45 + $item["items"][$i]["monto"];
							$this->SetXY(165,$y);$this->MultiCell(15,5,number_format($item["items"][$i]["monto"],2,".", ","),'0','R');
							break;
						case "46";
							$total_46 = $total_46 + $item["items"][$i]["monto"];
							$this->SetXY(180,$y);$this->MultiCell(15,5,number_format($item["items"][$i]["monto"],2,".", ","),'0','R');
							break;
						case "47";
							$total_47 = $total_47 + $item["items"][$i]["monto"];
							$this->SetXY(195,$y);$this->MultiCell(15,5,number_format($item["items"][$i]["monto"],2,".", ","),'0','R');
							break;
						case "50";
							$total_50 = $total_50 + $item["items"][$i]["monto"];
							$this->SetXY(210,$y);$this->MultiCell(15,5,number_format($item["items"][$i]["monto"],2,".", ","),'0','R');
							break;
						case "51";
							$total_51 = $total_51 + $item["items"][$i]["monto"];
							$this->SetXY(225,$y);$this->MultiCell(15,5,number_format($item["items"][$i]["monto"],2,".", ","),'0','R');
							break;
						case "PEN";
							$total_pen = $total_pen + $item["items"][$i]["monto"];
							$this->SetXY(240,$y);$this->MultiCell(15,5,number_format($item["items"][$i]["monto"],2,".", ","),'0','R');
							break;
						case "RC";
							$total_rc = $total_rc + $item["items"][$i]["monto"];
							$this->SetXY(255,$y);$this->MultiCell(15,5,number_format($item["items"][$i]["monto"],2,".", ","),'0','R');
							break;					
					}
					$total_row = $total_row + $item["items"][$i]["monto"];
					//$this->Rect(10, $y, 85, 15);$this->SetXY(10,$y);$this->MultiCell(85,5,$item["nomb"],'0','L');				
				}
				$this->SetXY(270,$y);$this->MultiCell(15,5,number_format($total_row,2,".", ","),'0','R');
				$y=$y+5;
			}
			//Total
			$this->SetXY(10,$y);$this->MultiCell(50,5,"TOTAL",'1','C');
			$this->Rect(60, $y, 15, 5);$this->SetXY(60,$y);$this->MultiCell(15,5,number_format($total_ad,2,".", ","),'0','R');
			$this->Rect(75, $y, 15, 5);$this->SetXY(75,$y);$this->MultiCell(15,5,number_format($total_al,2,".", ","),'0','R');
			$this->Rect(90, $y, 15, 5);$this->SetXY(90,$y);$this->MultiCell(15,5,number_format($total_ga,2,".", ","),'0','R');
			$this->Rect(105, $y, 15, 5);$this->SetXY(105,$y);$this->MultiCell(15,5,number_format($total_41,2,".", ","),'0','R');
			$this->Rect(120, $y, 15, 5);$this->SetXY(120,$y);$this->MultiCell(15,5,number_format($total_42,2,".", ","),'0','R');
			$this->Rect(135, $y, 15, 5);$this->SetXY(135,$y);$this->MultiCell(15,5,number_format($total_43,2,".", ","),'0','R');
			$this->Rect(150, $y, 15, 5);$this->SetXY(150,$y);$this->MultiCell(15,5,number_format($total_44,2,".", ","),'0','R');
			$this->Rect(165, $y, 15, 5);$this->SetXY(165,$y);$this->MultiCell(15,5,number_format($total_45,2,".", ","),'0','R');
			$this->Rect(180, $y, 15, 5);$this->SetXY(180,$y);$this->MultiCell(15,5,number_format($total_46,2,".", ","),'0','R');
			$this->Rect(195, $y, 15, 5);$this->SetXY(195,$y);$this->MultiCell(15,5,number_format($total_47,2,".", ","),'0','R');
			$this->Rect(210, $y, 15, 5);$this->SetXY(210,$y);$this->MultiCell(15,5,number_format($total_50,2,".", ","),'0','R');
			$this->Rect(225, $y, 15, 5);$this->SetXY(225,$y);$this->MultiCell(15,5,number_format($total_51,2,".", ","),'0','R');
			$this->Rect(240, $y, 15, 5);$this->SetXY(240,$y);$this->MultiCell(15,5,number_format($total_pen,2,".", ","),'0','R');
			$this->Rect(255, $y, 15, 5);$this->SetXY(255,$y);$this->MultiCell(15,5,number_format($total_rc,2,".", ","),'0','R');
			$total = $total_ad+$total_al+$total_ga+$total_41+$total_42+$total_43+$total_44+$total_45+$total_46+$total_47+$total_50+$total_51+$total_pen+$total_rc;
			$this->Rect(270, $y, 15, 5);$this->SetXY(270,$y);$this->MultiCell(15,5,number_format($total,2,".", ","),'0','R');
			$y=$y+5;
			//Donacion
			$this->SetXY(10,$y);$this->MultiCell(50,5,"D.T.",'1','C');
			$x_t = 60;
			$total_don = 0;
			for($i=0;$i<count($items[$k]);$i++){
				$this->Rect($x_t, $y, 15, 5);$this->SetXY($x_t,$y);$this->MultiCell(15,5,number_format($items[$k][$i]["donacion"],2,".", ","),'0','R');
				$x_t = $x_t + 15;
				$total_don = $total_don + $items[$k][$i]["donacion"];
			}
			$this->Rect($x_t, $y, 15, 5);$this->SetXY($x_t,$y);$this->MultiCell(15,5,number_format($total_don,2,".", ","),'0','R');
			$y=$y+5;
			//RDR
			$this->SetXY(10,$y);$this->MultiCell(50,5,"RDR",'1','C');
			$this->Rect(60, $y, 15, 5);$this->SetXY(60,$y);$this->MultiCell(15,5,number_format($total_ad-$items[$k][0]["donacion"],2,".", ","),'0','R');
			$this->Rect(75, $y, 15, 5);$this->SetXY(75,$y);$this->MultiCell(15,5,number_format($total_al-$items[$k][1]["donacion"],2,".", ","),'0','R');
			$this->Rect(90, $y, 15, 5);$this->SetXY(90,$y);$this->MultiCell(15,5,number_format($total_ga-$items[$k][2]["donacion"],2,".", ","),'0','R');
			$this->Rect(105, $y, 15, 5);$this->SetXY(105,$y);$this->MultiCell(15,5,number_format($total_41-$items[$k][3]["donacion"],2,".", ","),'0','R');
			$this->Rect(120, $y, 15, 5);$this->SetXY(120,$y);$this->MultiCell(15,5,number_format($total_42-$items[$k][4]["donacion"],2,".", ","),'0','R');
			$this->Rect(135, $y, 15, 5);$this->SetXY(135,$y);$this->MultiCell(15,5,number_format($total_43-$items[$k][5]["donacion"],2,".", ","),'0','R');
			$this->Rect(150, $y, 15, 5);$this->SetXY(150,$y);$this->MultiCell(15,5,number_format($total_44-$items[$k][6]["donacion"],2,".", ","),'0','R');
			$this->Rect(165, $y, 15, 5);$this->SetXY(165,$y);$this->MultiCell(15,5,number_format($total_45-$items[$k][7]["donacion"],2,".", ","),'0','R');
			$this->Rect(180, $y, 15, 5);$this->SetXY(180,$y);$this->MultiCell(15,5,number_format($total_46-$items[$k][8]["donacion"],2,".", ","),'0','R');
			$this->Rect(195, $y, 15, 5);$this->SetXY(195,$y);$this->MultiCell(15,5,number_format($total_47-$items[$k][9]["donacion"],2,".", ","),'0','R');
			$this->Rect(210, $y, 15, 5);$this->SetXY(210,$y);$this->MultiCell(15,5,number_format($total_50-$items[$k][10]["donacion"],2,".", ","),'0','R');
			$this->Rect(225, $y, 15, 5);$this->SetXY(225,$y);$this->MultiCell(15,5,number_format($total_51-$items[$k][11]["donacion"],2,".", ","),'0','R');
			$this->Rect(240, $y, 15, 5);$this->SetXY(240,$y);$this->MultiCell(15,5,number_format($total_pen-$items[$k][12]["donacion"],2,".", ","),'0','R');
			$this->Rect(255, $y, 15, 5);$this->SetXY(255,$y);$this->MultiCell(15,5,number_format($total_rc-$items[$k][13]["donacion"],2,".", ","),'0','R');
			$this->Rect(270, $y, 15, 5);$this->SetXY(270,$y);$this->MultiCell(15,5,number_format($total-$total_don,2,".", ","),'0','R');	
			$y = $y + 10;
		}
	}
	function Footer()
	{
    	//Footer de la pagina
	}  
}

$pdf=new repo('L','mm','A4');
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