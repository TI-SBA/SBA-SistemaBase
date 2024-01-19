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
		$this->SetXY(10,10);$this->MultiCell(190,5,"REPORTE DE DEUDORES",'0','C');
		$this->SetFont('Arial','B',9);
		$this->SetXY(10,5);$this->MultiCell(60,5,"SOCIEDAD DE BENEFICENCIA PUBLICA DE AREQUIPA",'0','C');
		$this->SetXY(10,5);$this->MultiCell(190,5,"Página: ".$this->PageNo()."/{nb}",'0','R');
		$this->SetFont('Arial','',7);
		$this->SetXY(10,15);$this->MultiCell(60,5,"Sistema SBPA - Módulo de Caja",'0','C');
	}
	function days_bt_fec($fecha)
	{
	    $fecha= strtotime($fecha); // convierte la fecha de formato mm/dd/yyyy a marca de tiempo
	    $dia=date("d",$fecha); // día del mes en número
	    $mes=date("m",$fecha); // número del mes de 01 a 12
	    $ano=date("Y",$fecha);
	   
	    $diaactual=date("d",time());
	    $mesactual=date("m",time());
	    $anoactual=date("Y",time());
	    $fecha1=mktime(0,0,0,$mesactual,$diaactual,$anoactual);
	    $fecha2=mktime(0,0,0,$mes,$dia,$ano);
	 
	    $diferencia=$fecha2-$fecha1;
	    $dias=$diferencia/(60*60*24);
	    $dias=floor($dias);
	   
	    return -$dias;
	}	
	function Publicar($items){
		global $f;
		$f->library('helpers');
		$helper=new helper();
		$monedas = array(
			"S"=>"Soles S/.",
			"D"=>"Dolares USD $"
		);
		$this->SetFont('arial','',10);
		$y=30;
		$y_ini = $y;
		foreach($items as $clie){
			if($y>275){
				$this->AddPage();
				$y=$y_ini;
			}
			$cliente = $clie["nomb"];
			if($clie["tipo_enti"]=="P"){
				$cliente .=" ".$clie["appat"]." ".$clie["apmat"];
			}
			$this->SetFont('arial','B',10);
			$this->SetXY(10,$y);$this->MultiCell(190,5,$cliente,'1','L');
			$y+=5;
			$this->SetFont('arial','',10);
			foreach($clie["operaciones"] as $oper){
				if($y>275){
					$this->AddPage();
					$y=$y_ini;
				}
				$this->SetXY(10,$y);$this->MultiCell(190,5,$oper["espacio"]["ubic"]["local"]["direc"],'1','L');
				$y+=5;
				foreach($oper["items"] as $item){
					if($y>275){
						$this->AddPage();
						$y=$y_ini;
					}
					/*$this->SetXY(10,$y);$this->MultiCell(190,5,$helper->replace_acc($item["observ"]),'1','L');
					$y=$this->GetY();*/
					$this->SetXY(10,$y);$this->MultiCell(40,5,$monedas[$item["moneda"]],'1','L');
					$this->SetXY(50,$y);$this->MultiCell(40,5,number_format($item["total"],2),'1','L');
					$this->SetXY(100,$y);$this->MultiCell(60,5,Date::format($item["fecven"]->sec,"d/m/Y"),'1','L');
					$y+=5;
				}
			}
		}	
	}
	function Footer()
	{
    	//Footer de la pagina
	} 
}
$pdf=new expdientes('P','mm','A4');
$pdf->AliasNbPages();
$pdf->SetMargins(15,39.5,0); 
$pdf->SetTitle("reporte");
$pdf->SetAutoPageBreak(false,0); 
$pdf->Open();
//$pdf->Filtros($filter);
$pdf->AddPage();
$pdf->Publicar($items);
$pdf->SetLeftMargin(25);
$pdf->Output();

?>