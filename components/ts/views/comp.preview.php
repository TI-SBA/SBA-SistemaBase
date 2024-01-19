<?php
global $f;
$f->library('pdf');
//setlocale(LC_ALL,"esp");
setlocale(LC_ALL,"esp");
class doc extends FPDF{	
	function Header(){
		$this->Image(IndexPath.DS.'templates/ts/comprobante-pago.jpg',0,0,210,297);
	}
	function Publicar($data){
		$this->SetFont('arial','B',12);
		if(isset($data['nomb'])){
			$this->setXY(10,26);$this->MultiCell(130,10,'            '.$data['nomb'],'0','L');
		}
		$this->SetFont('arial','B',16);
		if(isset($data['fec'])){
			$this->setXY(135,35);$this->Cell(25,5,date('d', $data['fec']->sec),0,0,'R');
			$this->setXY(147,35);$this->Cell(25,5,date('m', $data['fec']->sec),0,0,'R');
			$this->setXY(164,35);$this->Cell(25,5,date('Y', $data['fec']->sec),0,0,'R');
		}
		
		
		
		
		
		
		$this->SetFont('Arial','',10);
		$y = 115;
		if(isset($data["cod_programatica"])){
			$pliegos = array();
			$programas = array();
			$subprogramas = array();
			$proyectos = array();
			$obras = array();
			foreach($data["cod_programatica"] as $cod_prog){
				array_push($pliegos,$cod_prog["funcion"]["cod"]);
				array_push($programas,$cod_prog["programa"]["cod"]);
				array_push($subprogramas,$cod_prog["subprograma"]["cod"]);
				array_push($proyectos,$cod_prog["actividad"]["cod"]);
				array_push($obras,$cod_prog["componente"]["cod"]);
			}
			if(count($pliegos)>0){
				$res_pliegos = array_unique($pliegos);
				$this->SetXY(10,$y);
				if(count($res_pliegos)>1){
					$this->MultiCell(20,5,"V",'0','L');
				}else{
					$this->MultiCell(20,5,$res_pliegos[0],'0','L');
				}		
			}
			if(count($programas)>0){
				$res_prog = array_unique($programas);
				$this->SetXY(23,$y);
				if(count($res_prog)>1){
					$this->MultiCell(20,5,"V",'0','L');
				}else{
					$this->MultiCell(20,5,$res_prog[0],'0','L');
				}		
			}
			if(count($subprogramas)>0){
				$res_sprog = array_unique($subprogramas);
				$this->SetXY(40,$y);
				if(count($res_sprog)>1){
					$this->MultiCell(20,5,"V",'0','L');
				}else{
					$this->MultiCell(20,5,$res_sprog[0],'0','L');
				}		
			}
			if(count($proyectos)>0){
				$res_proy = array_unique($proyectos);
				$this->SetXY(57,$y);
				if(count($res_proy)>1){
					$this->MultiCell(25,5,"V",'0','L');
				}else{
					$this->MultiCell(25,5,$res_proy[0],'0','L');
				}		
			}
			if(count($obras)>0){
				$res_obr = array_unique($obras);
				$this->SetXY(75,$y);
				if(count($res_obr)>1){
					$this->MultiCell(25,5,"V",'0','L');
				}else{
					$this->MultiCell(25,5,$res_obr[0],'0','L');
				}		
			}
			$this->SetXY(95,$y);$this->MultiCell(15,5,$data["fuente"]["cod"],'0','L');
		}
		
		
		
		
		
		
		
		
		
		
		
		
		/** Contabilidad Presupuestal */
		$y = 140;
		$y_d = $y;
		$y_h = $y;
		if(isset($data["cont_presupuestal"])){
			foreach($data["cont_presupuestal"] as $cpres){
				if($cpres["tipo"]=="D"){
					$this->SetXY(10,$y_d);$this->MultiCell(35,5,$cpres["cuenta"]["cod"],'0','L');
					$this->SetXY(25,$y_d);$this->MultiCell(20,5,number_format($cpres["monto"],2,".", ","),'0','R');
					$y_d=$y_d+5;
				}elseif($cpres["tipo"]=="H"){
					$this->SetXY(55,$y_h);$this->MultiCell(35,5,$cpres["cuenta"]["cod"],'0','L');
					$this->SetXY(70,$y_h);$this->MultiCell(20,5,number_format($cpres["monto"],2,".", ","),'0','R');
					$y_h=$y_h+5;
				}
			}
		}
		$y = 167;
		$y_d = $y;
		$y_h = $y;
		/** Contabilidad Patrimonial */
		if(isset($data["cont_patrimonial"])){
			foreach($data["cont_patrimonial"] as $cpat){
				if($cpat["tipo"]=="D"){
					$this->SetXY(10,$y_d);$this->MultiCell(35,5,$cpat["cuenta"]["cod"],'0','L');
					$this->SetXY(25,$y_d);$this->MultiCell(20,5,number_format($cpat["monto"],2,".", ","),'0','R');
					$y_d=$y_d+5;
				}elseif($cpat["tipo"]=="H"){
					$this->SetXY(55,$y_h);$this->MultiCell(35,5,$cpat["cuenta"]["cod"],'0','L');
					$this->SetXY(70,$y_h);$this->MultiCell(20,5,number_format($cpat["monto"],2,".", ","),'0','R');
					$y_h=$y_h+5;
				}
			}
		}
		/** Retenciones y Deducciones */
		$y = 197;
		if(isset($data["retenciones"])){
			foreach($data["retenciones"] as $rete){
				$this->SetXY(105,$y);$this->MultiCell(25,5,$rete["cuenta"]["cod"],'0','L');
				$this->SetXY(165,$y);$this->MultiCell(25,5,number_format($rete["monto"],2,".", ","),'0','R');
				$this->SetXY(125,$y);$this->MultiCell(40,5,$rete["cuenta"]["descr"],'0','L');
				$y = $this->GetY();
			}
		}
		
		
		
		
		
		
	}
}
$pdf=new doc('P','mm','A4');
$pdf->SetTitle("Comprobante de Pago");
$pdf->Open();
$pdf->AddPage();
$pdf->Publicar($data);
$pdf->Output();
?>