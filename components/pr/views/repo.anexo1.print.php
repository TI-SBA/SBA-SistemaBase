<?php
global $f;
$f->library('pdf');

class presaper extends FPDF
{
	function Header(){
		//$this->Image(IndexPath.DS.'templates/pr/poi_prog.gif',15,15,267,180);		
		$this->SetFont('arial','B',7);
		$y=10;
		$this->SetXY(0,$y);$this->MultiCell(297,5,'ANEXO Nº 1','0','C');
		$y+=5;
		$this->SetXY(10,$y);$this->MultiCell(277,10,'ESTRUCTURA PROGRAMATICA Y FUNCIONAL AÑO FISCAL '.date('Y'),'1','C');
		$y+=10;
		$this->Rect(10, $y, 277, 10);$this->SetXY(10,$y);$this->MultiCell(277,5,'SECTOR                       39                    MUJER Y DESARROLLO SOCIAL','0','L');
		$y+=5;
		$this->SetXY(10,$y);$this->MultiCell(277,5,'ENTIDAD                    001                    SOCIEDAD DE BENEFICENCIA PÚBLICA DE AREQUIPA','0','L');
		$y+=5;
		$this->SetFont('arial','B',6);
		$this->SetXY(10,$y);$this->MultiCell(15,8,'CATEGORIA','1','C');
		$this->SetXY(25,$y);$this->MultiCell(15,4,'PROGRAMA PRESUP.','1','C');
		$this->SetXY(40,$y);$this->MultiCell(12,4,'PROD. / PROY.','1','C');
		$this->SetXY(52,$y);$this->MultiCell(40,4,'ACTIVIDAD ACCIÓN DE INVERSIÓN/OBRAS','1','C');
		$this->SetXY(92,$y);$this->MultiCell(23,8,'FUNCION','1','C');
		$this->SetXY(115,$y);$this->MultiCell(23,4,'DIVISIÓN FUNCIONAL','1','C');
		$this->SetXY(138,$y);$this->MultiCell(23,8,'GRUPO FUNCIONAL','1','C');
		$this->SetXY(161,$y);$this->MultiCell(40,8,'FINALIDAD','1','C');
		$this->SetXY(201,$y);$this->MultiCell(13,4,'(4)ACC.INV/OBRAS','1','C');
		$this->SetXY(214,$y);$this->MultiCell(13,8,'(5)ACTI','1','C');
		$this->SetXY(227,$y);$this->MultiCell(15,4,'UNIDAD DE MEDIDA','1','C');
		$this->SetXY(242,$y);$this->MultiCell(15,8,'CANTIDAD','1','C');
		$this->SetXY(257,$y);$this->MultiCell(15,8,'UBIGEO','1','C');
		$this->SetXY(272,$y);$this->MultiCell(15,8,'DISTRITO','1','C');
	}
	function Publicar($items){
		$this->SetFont('arial','',6);
		$x=0;
		$y=35;//41
		$y_marg = 5;
		$i_org = 1;
		$tot_func = 0;
		$tot_prog = 0;
		$tot_subp = 0;
		$tot_acti = 0;
		$tot_comp = 0;		
		$y_func = 43;
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
					$all_comp_1 = 0;
					$tot_acti=count($subp["actividades"]);
					$y_acti = $y_subp;
					foreach($subp["actividades"] as $k=>$acti){
						$all_acti++;
						$all_comp_3++;
						$tot_comp=count($acti["componentes"]);						
						$y_comp = $y_acti;
						if($acti["tipo"]=="AI"){
							$acci_x = "X";
							$acti_x = "";
						}else{
							$acti_x = "X";
							$acci_x = "";
						}
						foreach($acti["componentes"] as $l=>$comp){
							$all_comp++;
							$all_comp_2++;
							$all_comp_1++;					
							$this->Rect(161, $y_comp, 40, 8);$this->setXY(161,$y_comp);$this->MultiCell(40,2,$comp["cod"]." ".$comp["nomb"],'0','L');
							$this->Rect(10, $y_comp, 15, 8);$this->setXY(10,$y_comp);$this->MultiCell(15,2,$comp["categoria"]["cod"]." ".$comp["categoria"]["nomb"],'0','L');//categoria
							$this->Rect(25, $y_comp, 15, 8);$this->setXY(25,$y_comp);$this->MultiCell(15,2,$comp["prog_pres"]["cod"],'0','C');//programa presupuestario
							$this->Rect(40, $y_comp, 12, 8);$this->setXY(40,$y_comp);$this->MultiCell(12,2,$comp["prod_proy"]["cod"]." ".$comp["prod_proy"]["nomb"],'0','C');//proyecto
							$this->Rect(201, $y_comp, 13, 8);$this->SetXY(201,$y_comp);$this->MultiCell(13,8,$acci_x,'1','C');
							$this->Rect(214, $y_comp, 13, 8);$this->SetXY(214,$y_comp);$this->MultiCell(13,8,$acti_x,'1','C');
							$this->Rect(227, $y_comp, 15, 8);$this->SetXY(227,$y_comp);$this->MultiCell(15,4,$comp["unidad"]["nomb"],'0','C');
							$this->Rect(242, $y_comp, 15, 8);$this->SetXY(242,$y_comp);$this->MultiCell(15,8,$comp["cantidad"],'0','C');
							$this->Rect(257, $y_comp, 15, 8);$this->SetXY(257,$y_comp);$this->MultiCell(15,2,$comp["ubigeo"],'0','C');
							$this->Rect(272, $y_comp, 15, 8);$this->SetXY(272,$y_comp);$this->MultiCell(15,2,$comp["distrito"],'0','C');
							$y_comp = $y_comp + 8;
						}
						$this->Rect(52, $y_acti, 40, 8*$tot_comp);$this->setXY(52,$y_acti);$this->MultiCell(40,2,$acti["cod"]." ".$acti["nomb"],'0','L');
						$y_acti = $y_acti +8*$tot_comp;
					}				
					$this->Rect(138, $y_subp, 23, 8*$all_comp_1);$this->setXY(138,$y_subp);$this->MultiCell(23,2,$subp["cod"]." ".$subp["nomb"],'0','L');
					$y_subp = $y_subp + 8*$all_comp_1;
				}
				$this->Rect(115, $y_prog, 23, 8*$all_comp_2);$this->setXY(115,$y_prog);$this->MultiCell(23,2,$prog["cod"]." ".$prog["nomb"],'0','L');
				$y_prog = $y_prog + 8*$all_comp_2;
			}
			$this->Rect(92, $y_func, 23, 8*$all_comp);$this->setXY(92,$y_func);$this->MultiCell(23,2,$func["cod"]." ".$func["nomb"],'0','L');
			$y_func = $y_func + 8*$all_comp;
		}
	}
	function Footer()
	{
 
	} 
	 
}

$pdf=new presaper('L','mm',array(297,297));
$pdf->SetTitle("reporte-pr-anexo1");
$pdf->SetAutoPageBreak(false,0); 
$pdf->Open();
$pdf->AddPage();
$pdf->Publicar($items);
$pdf->SetLeftMargin(25);
$pdf->Output();

?>