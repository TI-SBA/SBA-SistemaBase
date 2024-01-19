<?php
global $f;
$f->library('pdf');

class estr extends FPDF
{
	function Header(){
		//$this->Image(IndexPath.DS.'templates/pr/poi_prog.gif',15,15,267,180);
		$this->SetFont('Arial','B',15);
		$this->setY(15);$this->Cell(0,10,"SOCIEDAD DE BENEFICENCIA PUBLICA DE AREQUIPA",0,0,'C');
		$this->SetFont('Arial','I',13);
		$this->setY(20);$this->Cell(0,10,"ESTRUCTURA FUNCIONAL PROGRAMATICA AÑO FISCAL ".date("Y"),0,0,'C');
		$this->SetFont('Arial','B',8);
		$this->setXY(5,30);$this->MultiCell(40,5,"COD.FUNCIONAL",'1','C');
		$this->setXY(45,30);$this->MultiCell(40,5,"COD.PROGRAMATICA",'1','C');
		$this->setXY(85,30);$this->MultiCell(40,5,"COD.SUB-PROGRAMATICA",'1','C');
		$this->setXY(125,30);$this->MultiCell(40,5,"COD.ACTIVIDAD/PROYECT",'1','C');
		$this->setXY(165,30);$this->MultiCell(40,5,"COD.COMPONENTE",'1','C');
	}
	function Publicar($items){
		$x=0;
		$y=49;//41
		$y_marg = 5;
		$this->SetFont('arial','',8);
		$i_org = 1;
		$tot_func = 0;
		$tot_prog = 0;
		$tot_subp = 0;
		$tot_acti = 0;
		$tot_comp = 0;		
		$y_func = 35;
		foreach($items as $func){
			$all_prog = 0;
			$all_subp = 0;
			$all_acti = 0;
			$all_comp = 0;
			$tot_prog=count($func["programas"]);	
			$y_prog = $y_func;									
			foreach($func["programas"] as $i=>$prog){
				$all_comp_2 = 0;
				$tot_subp = count($prog["subprogramas"]);
				$y_subp = $y_prog;
				foreach($prog["subprogramas"] as $j=>$subp){
					$all_comp_3 = 0;
					$tot_acti=count($subp["actividades"]);
					$y_acti = $y_subp;
					foreach($subp["actividades"] as $k=>$acti){
						$all_acti++;
						$all_comp_3++;
						$tot_comp=count($acti["componentes"]);						
						$y_comp = $y_acti;
						foreach($acti["componentes"] as $l=>$comp){
							$all_comp++;
							$all_comp_2++;
							$this->SetFont('arial','',6);
							$this->Rect(165, $y_comp, 40, 15);$this->setXY(165,$y_comp);$this->MultiCell(40,5,$comp["cod"]." ".$comp["nomb"],'0','C');
							$this->SetFont('arial','',8);
							$y_comp = $y_comp + 15;
						}
						$this->Rect(125, $y_acti, 40, 15*$tot_comp);$this->setXY(125,$y_acti);$this->MultiCell(40,5,$acti["cod"]." ".$acti["nomb"],'0','C');
						$y_acti = $y_acti +15*$tot_comp;
					}
					$this->Rect(85, $y_subp, 40, 15*$all_comp_3);$this->setXY(85,$y_subp);$this->MultiCell(40,5,$subp["cod"]." ".$subp["nomb"],'0','C');
					$y_subp = $y_subp + 15*$all_comp_3;
				}
				$this->Rect(45, $y_prog, 40, 15*$all_comp_2);$this->setXY(45,$y_prog);$this->MultiCell(40,5,$prog["cod"]." ".$prog["nomb"],'0','C');
				$y_prog = $y_prog + 15*$all_comp_2;
			}
			$this->Rect(5, $y_func, 40, 15*$all_comp);$this->setXY(5,$y_func);$this->MultiCell(40,5,$func["cod"]." ".$func["nomb"],'0','C');
			$y_func = $y_func + 15*$all_comp;
		}
	}
	function Footer()
	{
    	//Footer de la pagina
    	$this->SetXY(220,-21.5);
    	$this->SetFont('Arial','I',8);
    	$this->SetTextColor(128);
    	$this->Cell(0,10,"Página: ".$this->PageNo(),0,0,'C');
    	
    	$this->SetXY(29,-21.5);
    	$this->SetFont('Arial','I',8);
    	$this->SetTextColor(128);
    	$this->Cell(0,10,"Fecha de Impresión: ".date("d-m-Y"),0,0,'L');
	} 
	 
}

$pdf=new estr('P','mm','A4');
$pdf->SetMargins(15,39.5,0); 
$pdf->SetTitle("reporte");
$pdf->SetAutoPageBreak(false,0); 
$pdf->Open();
$pdf->AddPage();
$pdf->Publicar($items);
$pdf->SetLeftMargin(25);
$pdf->Output();

?>