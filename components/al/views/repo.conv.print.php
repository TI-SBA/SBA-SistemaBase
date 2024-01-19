<?php
global $f;
$f->library('pdf');

class repo extends FPDF
{
	function mayorValor( $array ){
		$a = array_unique( $array );
		$s = 0;
		if( is_array( $a ) )
			foreach( $a as $v )
				$s = intval( $v ) > $s ? $v : $s;
		return $s;
	}
	function Header(){
		//$this->Image(IndexPath.DS.'templates/pr/presupuestos.gif',15,15,180,267);	
		$this->SetFont('Arial','B',15);
		$this->setY(15);$this->Cell(0,10,"SOCIEDAD DE BENEFICENCIA PUBLICA DE AREQUIPA",0,0,'C');
		$this->SetFont('Arial','I',13);
		$this->setY(20);$this->Cell(0,10,"CONVENIOS",0,0,'C');
		$this->SetFont('Arial','B',8);
		$this->setXY(5,35);$this->MultiCell(35,10,"Entidad",'1','C');
		$this->setXY(40,35);$this->MultiCell(25,10,"Fecha Inicio",'1','C');
		$this->setXY(65,35);$this->MultiCell(25,10,"Fecha Fin",'1','C');
		$this->setXY(90,35);$this->MultiCell(50,10,"Aportes SBPA",'1','C');
		$this->setXY(140,35);$this->MultiCell(50,10,"Aportes Entidad",'1','C');		
		$this->setXY(190,35);$this->MultiCell(50,10,"Comision",'1','C');	
		$this->setXY(240,35);$this->MultiCell(50,10,"Adenda",'1','C');	
	}		
	function Publicar($items){
		$this->SetFont('courier','B',8);
		$x=0;
		$y=45;
		$y_marg = 4.8;
		$index=0;
		$this->SetY($y);
		$this->SetFont('courier','',8);
		$this->Line(5,$y,5,190);
		$this->Line(40,$y,40,190);
		$this->Line(65,$y,65,190);
		$this->Line(90,$y,90,190);
		$this->Line(140,$y,140,190);
		$this->Line(190,$y,190,190);
		$this->Line(240,$y,240,190);
		$this->Line(290,$y,290,190);
		$this->Line(5,190,290,190);
		foreach($items as $row){
			if ($y>=180){//180
				$this->AddPage();$y=45;
				$this->Line(5,$y,5,190);
				$this->Line(40,$y,40,190);
				$this->Line(65,$y,65,190);
				$this->Line(90,$y,90,190);
				$this->Line(140,$y,140,190);
				$this->Line(190,$y,190,190);
				$this->Line(240,$y,240,190);
				$this->Line(290,$y,290,190);
				$this->Line(5,190,290,190);
			}
			$entidad = $row["entidad"]["nomb"];
			if($row["entidad"]["tipo_enti"]=="P"){
				$entidad = $row["entidad"]["appat"]." ".$row["entidad"]["apmat"].", ".$row["entidad"]["nomb"];
			}
			$this->SetFont('arial','',8);
			$this->SetXY(5,$y);$this->MultiCell(35,5,$entidad,'0','L');
			$y_entidad = $this->getY();
			$this->SetXY(40,$y);$this->MultiCell(25,5,Date::format($row["fecini"]->sec, 'd/m/Y'),'0','C');
			$this->SetXY(65,$y);$this->MultiCell(25,5,Date::format($row["fecfin"]->sec, 'd/m/Y'),'0','C');
			$this->SetXY(90,$y);$this->MultiCell(50,5,substr($row["aportes"]["sbpa"], 0,120).'...','0','L');
			$y_aportes_dbpa = $this->getY();
			$this->SetXY(140,$y);$this->MultiCell(50,5,substr($row["aportes"]["entidad"], 0, 120).'...','0','L');
			$y_aportes_entidad = $this->getY();
			$this->SetXY(190,$y);$this->MultiCell(50,5,substr($row["comision"], 0, 120).'...','0','L');
			$y_comision = $this->getY();
			$this->SetXY(240,$y);$this->MultiCell(50,5,substr($row["adenda"], 0, 120).'...','0','L');
			$y_adenda = $this->getY();
			$values = array($y_entidad,$y_aportes_dbpa,$y_aportes_entidad,$y_comision,$y_adenda);
			$y_mayor = $this->mayorValor($values);
			$index++;
			$y=$y_mayor;
			$this->Line(5,$y,290,$y);
		}
	}
	function Footer()
	{
    	//Footer de la pagina
    	$this->SetXY(170,-21);
    	$this->SetFont('Arial','I',8);
    	$this->SetTextColor(128);
    	$this->Cell(0,10,"Página: ".$this->PageNo(),0,0,'C');
    	
    	$this->SetXY(28,-21);
    	$this->SetFont('Arial','I',8);
    	$this->SetTextColor(128);
    	$this->Cell(0,10,"Fecha de Impresión: ".date("d-m-Y"),0,0,'L');
	} 
	 
}

$pdf=new repo('L','mm','A4');
$pdf->SetMargins(15,39.5,0); 
$pdf->SetTitle("reporte");
$pdf->SetAutoPageBreak(false,0); 
$pdf->Open();
$pdf->AddPage();
$pdf->Publicar($items);
$pdf->SetLeftMargin(25);
//header('Content-Type: application/pdf; charset=utf-8');
$pdf->Output();

?>