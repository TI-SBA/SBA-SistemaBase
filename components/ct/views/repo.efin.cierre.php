<?php
global $f;
$f->library('pdf');

class repo extends FPDF
{
	var $ano1;
	var $ano2;
	function Filter($filtros){
		$this->ano1 = $filtros["ano1"];
		$this->ano2 = $filtros["ano2"];
	}
	function Header(){
		$this->SetFont('Arial','B',15);
		$this->setY(15);$this->Cell(0,10,"SOCIEDAD DE BENEFICENCIA PUBLICA DE AREQUIPA",0,0,'C');
		$this->SetFont('Arial','I',13);
		$this->setY(20);$this->Cell(0,10,"Notas a los Estados financieros - Cierre de Balance - ".$this->ano1,0,0,'C');
	}		
	function Publicar($items){
		$x=0;
		$y=35;
		$y_ini = $y;
		$page_b = 260;
		foreach($items as $item){
			$this->SetFont('Arial',"B",10);
			$this->SetXY(15,$y);$this->MultiCell(180,5,"NOTA Nro. ".$item["num"]."\n".strtoupper($item["nomb"]),'0','C');			
			$y=$y+10;
			$this->SetFont('Arial',"B",9);
			$this->SetXY(15,$y);$this->MultiCell(20,5,"CODIGO",'0','C');
			$this->SetXY(35,$y);$this->MultiCell(85,5,"DENOMINACION",'0','C');
			$this->SetXY(120,$y);$this->MultiCell(25,5,$this->ano1,'0','C');
			$this->SetXY(145,$y);$this->MultiCell(25,5,$this->ano2,'0','C');
			$this->SetXY(170,$y);$this->MultiCell(30,5,"VARIACION",'0','C');
			//$this->SetXY($x_left,$y);$this->MultiCell(20,5,number_format($otros["monto"],2,".", ","),'0','R');
			$y=$y+5;
			$this->SetFont('Arial',"",7);
			$total_1 = 0;
			$total_2 = 0;
			foreach($item["cuentas"] as $row){	
				if($y>$page_b){
					$this->AddPage();
					$y = $y_ini;
				}		
				$this->SetXY(15,$y);$this->MultiCell(20,5,$row["cuenta"]["cod"],'0','L');
				$this->SetXY(35,$y);$this->MultiCell(85,5,substr($row["cuenta"]["descr"],0,50),'0','L');
				$ano1 = array_sum($row["ano1"]);
				$this->SetXY(120,$y);$this->MultiCell(25,5,number_format($ano1,2,".", ","),'0','R');
				$ano2 = array_sum($row["ano2"]);
				$this->SetXY(145,$y);$this->MultiCell(25,5,number_format($ano2,2,".", ","),'0','R');
				$vari = $ano1-$ano2;
				if($vari<0)$vari = "(".number_format(-$vari,2,".", ",").")";
				else $vari = number_format($vari,2,".", ",");
				$this->SetXY(170,$y);$this->MultiCell(30,5,$vari,'0','R');
				if(strlen($row["cuenta"]["cod"])==4){
					$total_1 = $total_1+$ano1;
					$total_2 = $total_2+$ano2;
				}
				$y=$y+5;
			}
			$this->SetFont('Arial',"B",7);
			$this->SetXY(35,$y);$this->MultiCell(85,5,"TOTAL==>",'0','R');
			$this->SetXY(120,$y);$this->MultiCell(25,5,number_format($total_1,2,".", ","),'0','R');
			$this->SetXY(145,$y);$this->MultiCell(25,5,number_format($total_2,2,".", ","),'0','R');
			$total_var = $total_1-$total_2;
			if($total_var<0)$total_var = "(".number_format(-$total_var,2,".", ",").")";
			else $total_var = number_format($total_var,2,".", ",");
			$this->SetXY(170,$y);$this->MultiCell(30,5,$total_var,'0','R');		
			$y=$y+15;
		}
	}
	function Footer()
	{
    	//Footer de la pagina
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