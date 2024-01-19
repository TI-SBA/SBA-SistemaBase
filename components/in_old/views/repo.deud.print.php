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
		$this->SetXY(10,5);$this->MultiCell(190,5,date("d/m/Y")." - Página: ".$this->PageNo()."/{nb}",'0','R');
		$this->SetFont('Arial','',7);
		$this->SetXY(10,15);$this->MultiCell(60,5,"Sistema SBPA - Módulo de Inmuebles",'0','C');
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
			"S"=>"S/.",
			"D"=>"$"
		);
		$uso_espacio = array(
			"TI"=>"Tiendas",
			"OF"=>"Oficina",
			"HO"=>"Hotel",
			"ST"=>"Stand",
			"CI"=>"Cine",
			'ES'=>'Espacio',
			'CO'=>'Cochera',
			'VI'=>'Casa - Habitación',
			'OT'=>'Otros'
		);
		$condicion_arrendamiento = array(
			"CT"=>"Nuevo",
			"RE"=>"Renovación",
			"CV"=>"Convenio",
			"CU"=>"Cesión en Uso",
			"CM"=>"Comodato",
			"AC"=>"Acta de Conciliación",
			"RS"=>'Por Ocupación',
			"AU"=>'Autorización',
			"PE"=>'Penalidades',
			"TR"=>'Traspaso',
			"AD"=>'Audiencias'
		);
		$tipo_arren = array(
			"P"=>"NUEVO ARRENDAMIENTO",
			"R"=>"RENOV."
		);
		$this->SetFont('arial','',10);
		$y=30;
		$y_ini = $y;
		foreach($items as $uso){
			if($y>265){
				$this->AddPage();
				$y=$y_ini;
			}
			$this->SetFont('arial','BU',12);
			$this->SetXY(10,$y);$this->MultiCell(190,5,$uso_espacio[$uso["nomb"]],'0','L');
			$y+=5;
			foreach($uso["items"] as $item){
				$usuario = $item["arrendatario"]["nomb"];
				if($item["arrendatario"]["tipo_enti"]=="P"){
					$usuario.=" ".$item["arrendatario"]["appat"]." ".$item["arrendatario"]["apmat"];
				}
				if($y>265){
					$this->AddPage();
					$y=$y_ini;
				}
				$this->SetFont('arial','B',10);
				$this->SetXY(10,$y);$this->MultiCell(190,5,$item["espacio"]["ubic"]["local"]["direc"]." ".$item["espacio"]["descr"],'0','L');
				$y+=5;
				$this->SetXY(30,$y);$this->MultiCell(190,5,"[ CONTRATO: ".$condicion_arrendamiento[$item["arrendamiento"]["condicion"]]." ] ".Date::format($item["arrendamiento"]["feccon"]->sec, "d/m/Y")." - ".Date::format($item["arrendamiento"]["fecven"]->sec, "d/m/Y"),'0','L');
				$y+=5;
				$this->SetXY(30,$y);$this->MultiCell(190,5,$usuario,'0','L');
				$y+=5;
				$this->SetFont('arial','',8);
				$num_rows = count($item["arrendamiento"]["rentas"]);
				$col = 10;
				$tot_deud = 0;
				foreach($item["arrendamiento"]["rentas"] as $r=>$rent){
					if(($r%4==0)&($r!=0)){
						$y+=5;
						$col=10;
						if($y>275){
							$this->AddPage();
							$y=$y_ini;
						}
					}
					$this->SetXY($col,$y);$this->MultiCell(25,5,Date::format($rent["fecpago"]->sec,"d/m/Y")."   ==>",'0','L');
					$this->SetXY($col+25,$y);$this->MultiCell(190,5,$monedas[$rent["moneda"]]." ".number_format($rent["importe"],2),'0','L');
					$tot_deud +=$rent["importe"];
					$col+=50;		
				}
				$this->Line(10, $y+5, 200, $y+5);
				$y+=5;
				$this->SetXY(10,$y);$this->MultiCell(190,5,"Total Deuda  ==>  ".$monedas[$item["arrendamiento"]["moneda"]]." ".number_format($tot_deud,2),'0','R');
				$y+=5;
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