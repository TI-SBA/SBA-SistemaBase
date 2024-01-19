<?php
global $f;
$f->library('pdf');
class expdientes extends FPDF
{
	var $filter;
	function Filtros($filtros){
		$this->filter = $filtros;
	}
	function Header(){
		//$this->Image(IndexPath.DS.'templates/td/expd.gif',15,15,347,249);
		$this->SetFont('Arial','B',14);
		$this->SetXY(10,10);$this->MultiCell(357,5,"REPORTE DE EXPEDIENTES TRAMITADOS",'0','C');
		$this->SetFont('Arial','B',9);
		$this->SetXY(10,5);$this->MultiCell(60,5,"SOCIEDAD DE BENEFICENCIA PUBLICA DE AREQUIPA",'0','C');
		$this->SetXY(10,5);$this->MultiCell(357,5,"Página: ".$this->PageNo()."/{nb}",'0','R');
		$this->SetFont('Arial','',7);
		$this->SetXY(10,15);$this->MultiCell(60,5,"Sistema SBPA - Módulo de Trámite Documentario",'0','C');
		$this->SetFont('arial','B',10);
		$this->SetXY(15,26);$this->MultiCell(165,10,"Organización:",'1','L');//28
		$this->SetXY(180,26);$this->MultiCell(110,10,"Usuario:",'1','L');//20
		$this->SetXY(290,26);$this->MultiCell(73,10,"Desde:                          Hasta:",'1','L');
		$this->SetXY(15,36);$this->MultiCell(258,10,"Tipo de Procedimiento (TUPA):",'1','L');//55
		$this->SetXY(273,36);$this->MultiCell(55,10,"Por vencimiento de plazo:",'1','L');
		$this->SetXY(328,36);$this->MultiCell(35,10,"No Atendidos:",'1','L');
		$this->SetFont('arial','',11);
		$this->SetXY(40,26);$this->Cell(110,10,$this->filter["oficina"],0,0,'L');
		$this->SetXY(195,26);$this->Cell(110,10,$this->filter["usuario"],0,0,'L');
		$desde = explode(" ",$this->filter["desde"]);
		$desde = $desde[0];
		$hasta = explode(" ",$this->filter["hasta"]);
		$hasta = $hasta[0];
		$this->SetXY(302,26);$this->Cell(25,10,$desde,0,0,'L');
		$this->SetXY(338,26);$this->Cell(25,10,$hasta,0,0,'L');
		$this->SetXY(70,36);$this->MultiCell(200,5,$this->filter["proc"],'0','L');
		if($this->filter["venc"]=="1"){
			$this->SetXY(320,36);$this->Cell(10,10,"X",0,0,'L');
		}
		if($this->filter["noaten"]=="1"){
			$this->SetXY(355,36);$this->Cell(10,10,"X",0,0,'L');
		}
		$this->SetFont('arial','B',9);
		$this->SetXY(15,46);$this->MultiCell(10,10,"NRO.",'1','C');
		$this->SetXY(25,46);$this->MultiCell(25,10,"EXPEDIENTE",'1','C');
		$this->SetXY(50,46);$this->MultiCell(65,10,"GESTOR",'1','C');
		$this->SetXY(115,46);$this->MultiCell(110,10,"ASUNTO / PROCEDIMIENTO TUPA",'1','C');
		$this->SetXY(225,46);$this->MultiCell(40,10,"UBICACION",'1','C');
		$this->SetXY(265,46);$this->MultiCell(25,10,"REGISTRO",'1','C');
		$this->SetXY(290,46);$this->MultiCell(25,10,"CONCLUIDO",'1','C');
		$this->SetXY(315,46);$this->MultiCell(28,10,"PLAZO TUPA",'1','C');
		$this->SetXY(343,46);$this->MultiCell(30,5,"DÍAS TRANSCURRIDOS",'1','C');
	}
	function days_bt_fec($fecha,$fecha2=null)
	{
	    $fecha= strtotime($fecha); // convierte la fecha de formato mm/dd/yyyy a marca de tiempo
	    $dia=date("d",$fecha); // día del mes en número
	    $mes=date("m",$fecha); // número del mes de 01 a 12
	    $ano=date("Y",$fecha);
	    if($fecha2==null){
		    $diaactual=date("d",time());
		    $mesactual=date("m",time());
		    $anoactual=date("Y",time());
	    }else{
	  	 	$fecha2= strtotime($fecha2); // convierte la fecha de formato mm/dd/yyyy a marca de tiempo
		    $diaactual=date("d",$fecha2); // día del mes en número
		    $mesactual=date("m",$fecha2); // número del mes de 01 a 12
		    $anoactual=date("Y",$fecha2);
		}
	    $fecha1=mktime(0,0,0,$mesactual,$diaactual,$anoactual);
	    $fecha2=mktime(0,0,0,$mes,$dia,$ano);
	    $diferencia=$fecha2-$fecha1;
	    $dias=$diferencia/(60*60*24);
	    $dias=floor($dias);
	    return -$dias;
	}	
	function Publicar($items,$filter){
		$this->SetFont('arial','',10);
		$y_marg = 10;
		$y=56;
		$this->SetY($y);
		if(count($items)>0){
			foreach($items as $i=>$item){
				if($this->GetY()>255){
					$this->AddPage();
					$y=56;
				}
				$this->SetXY(15, $y);$this->MultiCell(10,5,($i+1),'0','C');
				$this->SetXY(25, $y);$this->MultiCell(24,5,$item["num"],'0','L');
				$gestor = $item["gestor"]["nomb"];
				if($item["gestor"]["tipo_enti"]=="P"){
					$gestor = $item["gestor"]["appat"]." ".$item["gestor"]["apmat"].", ".$item["gestor"]["nomb"];
				}
				//$this->SetFont('courier','',8);
				$this->SetXY(50, $y);$this->MultiCell(65,5,$gestor,'0','L');
				$y_gestor = $this->getY(); 
				//$this->SetFont('courier','',10);
				$this->SetXY(265, $y);$this->MultiCell(25,5,Date::format($item["fecreg"]->sec, 'd/m/Y'),'0','C');
				if(isset($item["feccon"]))$feccon = Date::format($item["feccon"]->sec, 'd/m/Y');
				else $feccon = "--";
				$this->SetXY(290, $y);$this->MultiCell(25,5,$feccon,'0','C');
				if(isset($item["tupa"]))$plazo = $item["tupa"]["procedimiento"]["modalidad"]["plazo"];
				else $plazo = "--";
				$this->SetXY(315, $y);$this->MultiCell(25,5,$plazo." dia(s)",'0','C');
				if($item['estado']=='C'){
					$this->SetXY(343, $y);$this->MultiCell(30,5,$this->days_bt_fec(Date::format($item["fecreg"]->sec, 'm/d/Y'),Date::format($item["feccon"]->sec, 'm/d/Y'))." día(s)",'0','C');
				}else{
					$this->SetXY(343, $y);$this->MultiCell(30,5,$this->days_bt_fec(Date::format($item["fecreg"]->sec, 'm/d/Y'))." día(s)",'0','C');
				}
				//$this->SetFont('courier','',11);
				$this->SetXY(225, $y);$this->MultiCell(40,5,$item['ubicacion']['nomb'],'0','C');
				$y_area = $this->getY();
				$this->SetXY(115, $y);$this->MultiCell(110,5,$item["concepto"],'0','L');
				$y_concepto = $this->getY();
				if($y_gestor>$y_area){
					$y=$y_gestor;
					if($y_gestor>$y_concepto){
						$y=$y_gestor;
					}else{
						$y=$y_concepto;
					}
				}else{
					$y=$y_area;
					if($y_concepto>$y_area){
						$y=$y_concepto;
					}else{
						$y=$y_area;
					}
				}
				//$y = $this->GetY();
				$this->Line(15, $y, 372, $y);//362
			}
		}
	}
	function Footer()
	{
    	//Footer de la pagina
    	$this->SetXY(220,-15);
    	$this->SetFont('Arial','I',8);
    	$this->SetTextColor(128);
    	$this->Cell(0,10,$this->PageNo(),0,0,'C');
    	$this->SetXY(29,-15);
    	$this->SetFont('Arial','I',8);
    	$this->SetTextColor(128);
    	$this->Cell(0,10,date("d-m-Y"),0,0,'L');
	} 
}
$pdf=new expdientes('P','mm',array(387,279));
$pdf->AliasNbPages();
$pdf->SetMargins(15,39.5,0); 
$pdf->SetTitle("reporte");
$pdf->SetAutoPageBreak(false,0); 
$pdf->Open();
$pdf->Filtros($filter);
$pdf->AddPage();
$pdf->Publicar($items,$filter);
$pdf->SetLeftMargin(25);
$pdf->Output();

?>