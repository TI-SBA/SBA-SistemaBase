<?php
global $f;
$f->library('pdf');

class pmen extends FPDF
{
	var $filtros;
	var $meses;
	var $title;
	function Filter($filtros,$meses,$title){
		$this->filtros = $filtros;
		$this->meses = $meses;
		$this->title = $title;
	}
	function Header(){
		$meses = array("Todos","Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Setiembre","Octubre","Noviembre","Diciembre");
		$tipos = array("I"=>"INGRESOS","G"=>"GASTOS");
		//$this->Image(IndexPath.DS.'templates/pr/presupuestos.gif',15,15,180,267);	
		$this->SetFont('Arial','B',9);
		$this->SetXY(5,5);$this->MultiCell(60,5,"SOCIEDAD DE BENEFICENCIA PUBLICA DE AREQUIPA",'0','C');
		$this->SetXY(10,5);$this->MultiCell(347,5,"Página: ".$this->PageNo()."/{nb}",'0','R');
		$this->SetFont('Arial','',7);
		$this->SetXY(5,15);$this->MultiCell(60,5,"Sistema SBPA - Módulo Integrado de Contabilidad y Presupuestos",'0','C');	
		/*$this->SetFont('Arial','BU',11);
		if($this->filtros["tipo"]=="I"){
			$anexo = "2";
		}elseif($this->filtros["tipo"]=="G"){
			$anexo = "3";
		}
		$this->setXY(10,5);$this->MultiCell(277,5,"ANEXO Nº ".$anexo,'0','C');*/
		$this->SetFont('Arial','B',11);
		$this->setY(10,10);$this->MultiCell(347,5,"PRESUPUESTO AUTORIZADO Y EJECUCION DE INGRESOS DEL AÑO ".$this->filtros["ano"]." ".(isset($this->filtros["organizacion"])?"POR DEPENDENCIAS":"CONSOLIDAD"),'0','C');
		$this->setXY(10,20);$this->MultiCell(347,5,$this->title,'0','C');
		$this->SetFont('arial','B',8);
		if(isset($this->filtros["organizacion"])){
			$this->setXY(5,28);$this->MultiCell(250,5,"DEPENDENCIA: ".$this->filtros["organizacion"]["nomb"],'0','L');
		}
		$this->setXY(5,32);$this->MultiCell(113,5,"PROVINCIA: AREQUIPA",'0','L');
		$this->SetFont('arial','',6);
		$this->setXY(5,37);$this->MultiCell(65,3.3,"TIPO DE TRANSACCION \n GENERICA \n   SUB GENERICA - NIVEL 1 \n     SUB GENERICA - NIVEL 2 \n      ESPECIFICA - NIVEL 1 \n       ESPECIFICA - NIVEL 2",'1','L');//85
		$this->Rect(70, 37, 40, 5);
		$this->setXY(110,37);$this->MultiCell(240,5,"EJECUCION",'1','C');
		$this->SetFont('arial','B',6);
		$this->Rect(70, 42, 20, 15);$this->setXY(70,42);$this->MultiCell(20,5,"PRESUPUESTO AUTORIZADO (PIA) ".$this->filtros["ano"],'0','C');
		$this->Rect(90, 42, 20, 15);$this->setXY(90,42);$this->MultiCell(20,5,"PRESUPUESTO AUTORIZADO (PIM) ".$this->filtros["ano"],'0','C');
		$mes_ini = $this->meses[0];
		$mes_fin = $this->meses[count($this->meses)-1];
		$x_m = 110;
		for($n=$mes_ini;$n<=$mes_fin;$n++){
			$this->setXY($x_m,42);$this->MultiCell(30,15,strtoupper($meses[$n]),'1','C');
			$x_m+=30;
		}
		$this->Rect(290, 42, 20, 15);$this->setXY(290,42);$this->MultiCell(20,5,"TOTAL\nINGRESO ACUMULADO",'0','C');
		$this->Rect(310, 42, 20, 15);$this->setXY(310,42);$this->MultiCell(20,15,"SALDO",'0','C');
		$this->Rect(330, 42, 20, 15);$this->setXY(330,42);$this->MultiCell(20,5,"INDICADOR\nDE\nEFICACIA %",'0','C');
		//$this->setXY(161,47);$this->MultiCell(34,15,"TOTAL",'1','C');
	}
	function Publicar($items,$meses){
		$x=0;
		$y=57;
		$mes_ini = $meses[0];
		$mes_fin = $meses[count($meses)-1];
		
		$all_fuen_pia = 0;	
		$all_acti_pia = 0;
		$all_proy_pia = 0;
		$all_fuen_pim = 0;	
		$all_acti_pim = 0;
		$all_proy_pim = 0;
		for($i=1;$i<=12;$i++){
			$all_fuen[$i] = 0;
			$all_acti[$i] = 0;
			$all_proy[$i] = 0;
		}	
		foreach($items as $fuente){
			$total_fuen_pia = 0;	
			$total_acti_pia = 0;
			$total_proy_pia = 0;
			$total_fuen_pim = 0;	
			$total_acti_pim = 0;
			$total_proy_pim = 0;
			for($i=$mes_ini;$i<=$mes_fin;$i++){
				$total_fuen[$i] = 0;
				$total_acti[$i] = 0;
				$total_proy[$i] = 0;
			}
			$this->SetFont('arial','B',8);
			$this->SetXY(5,$y);$this->MultiCell(345,5,"FUENTE: ".$fuente["fuente"]["cod"]." ".strtoupper($fuente["fuente"]["nomb"]),'1','L');	
			$y+=5;
			foreach($fuente["acti"] as $item){
				if ($y>=180){//180
					$this->SetFont('arial','B',8);
					$this->AddPage();$y=57;				
					$x=0;
					$this->SetXY(5,$y);$this->MultiCell(345,5,"FUENTE: ".$fuente["fuente"]["cod"]." ".strtoupper($fuente["fuente"]["nomb"]),'1','L');	
					$y=$y+5;
					$this->setY($y);
				}
				$this->SetFont('arial','',6);
				$this->Rect(5, $y, 20, 5);$this->SetXY(5,$y);$this->MultiCell(20,5,$item["clasificador"]["cod"],'0','L');	
				$this->Rect(25, $y, 45, 5);$this->SetXY(25,$y);$this->MultiCell(45,5,substr($item["clasificador"]["nomb"],0,30),'0','L');//65	
				$x_i = 110;
				$total_acum = 0;
				for($n=$mes_ini;$n<=$mes_fin;$n++){
					$suma = $item["eje"][$n];
					$total_acum+=$suma;
					//$total_class+=$suma;
					if(strlen($item["clasificador"]["cod"])==1){
						$total_fuen[$n]+=$suma;
						$total_acti[$n]+=$suma;
					}
					if($suma!=0){
						$suma = number_format($suma,2);
						//$total += array_sum($row["importes"][$i]);
					}else{
						$suma = "";
					}
					$this->SetXY($x_i,$y);$this->MultiCell(30,5,$suma,'1','R');										
					$x_i += 30;
				}
				if(strlen($item["clasificador"]["cod"])==1){
					$total_fuen_pim+=$item["pim"];
					$total_acti_pim+=$item["pim"];
					$total_fuen_pia+=$item["pia"];
					$total_acti_pia+=$item["pia"];
				}
				$this->SetXY(70,$y);$this->MultiCell(20,5,number_format($item["pia"],2),'1','R');
				$this->SetXY(90,$y);$this->MultiCell(20,5,number_format($item["pim"],2),'1','R');
				$this->setXY(290,$y);$this->MultiCell(20,5,number_format($total_acum,2),'1','R');
				$this->setXY(310,$y);$this->MultiCell(20,5,number_format($item["pim"]-$total_acum,2),'1','R');
				if($item["pim"]>0)$indi = number_format($total_acum*100/$item["pim"],2);
				else $indi = "--";
				$this->setXY(330,$y);$this->MultiCell(20,5,$indi,'1','R');
				$y+=5;
			}
			foreach($fuente["proy"] as $meta){
				$this->SetFont('arial','BU',8);
				$this->SetXY(5,$y);$this->MultiCell(285,5,"META: ".$meta["meta"]["cod"]." ".strtoupper($meta["meta"]["nomb"]),'1','L');	
				$y+=5;
				foreach($meta["items"] as $item){
					if ($y>=180){//180
						$this->SetFont('arial','B',8);
						$this->AddPage();$y=57;				
						$x=0;
						$this->SetXY(5,$y);$this->MultiCell(345,5,"FUENTE: ".$fuente["fuente"]["cod"]." ".strtoupper($fuente["fuente"]["nomb"]),'1','L');	
						$y+=5;
						//$this->setY($y);
						$this->SetXY(5,$y);$this->MultiCell(345,5,"META: ".$meta["meta"]["cod"]." ".strtoupper($meta["meta"]["nomb"]),'1','L');	
						$y+=5;
					}
					$this->SetFont('arial','',6);
					$this->Rect(5, $y, 20, 5);$this->SetXY(5,$y);$this->MultiCell(20,5,$item["clasificador"]["cod"],'0','L');	
					$this->Rect(25, $y, 45, 5);$this->SetXY(25,$y);$this->MultiCell(45,5,substr($item["clasificador"]["nomb"],0,30),'0','L');//65	
					$x_i = 110;
					$total_acum = 0;
					for($n=$mes_ini;$n<=$mes_fin;$n++){
						$suma = $item["importes"][$n];
						$total_acum+=$suma;
						//$total_class+=$suma;
						if(strlen($item["clasificador"]["cod"])==1){
							$total_fuen[$n]+=$suma;
							$total_proy[$n]+=$suma;
						}
						if($suma!=0){
							$suma = number_format($suma,2);
							//$total += array_sum($row["importes"][$i]);
						}else{
							$suma = "";
						}
						$this->SetXY($x_i,$y);$this->MultiCell(30,5,$suma,'1','R');										
						$x_i += 30;
					}
					if(strlen($item["clasificador"]["cod"])==1){
						$total_fuen_pim+=$item["pim"];
						$total_proy_pim+=$item["pim"];
						$total_fuen_pia+=$item["pia"];
						$total_proy_pia+=$item["pia"];
					}
					$this->SetXY(70,$y);$this->MultiCell(20,5,number_format($item["pia"],2),'1','R');
					$this->SetXY(90,$y);$this->MultiCell(20,5,number_format($item["pim"],2),'1','R');
					$this->setXY(290,$y);$this->MultiCell(20,5,number_format($total_acum,2),'1','R');
					$this->setXY(310,$y);$this->MultiCell(20,5,number_format($item["pim"]-$total_acum,2),'1','R');
					if($item["pim"]>0)$indi = number_format($total_acum*100/$item["pim"],2);
					else $indi = "--";
					$this->setXY(330,$y);$this->MultiCell(20,5,$indi,'1','R');
					$y+=5;
				}
			}
			$all_acti_pia+=$total_acti_pia;
			$all_proy_pia+=$total_proy_pia;
			$all_fuen_pia+=$total_fuen_pia;
			$all_acti_pim+=$total_acti_pim;
			$all_proy_pim+=$total_proy_pim;
			$all_fuen_pim+=$total_fuen_pim;		
			$this->SetFont('arial','B',6);
			$this->SetXY(5,$y);$this->MultiCell(65,5,"SUB-TOTAL ACTIVIDADES",'1','C');
			$this->SetXY(70,$y);$this->MultiCell(20,5,number_format($total_acti_pia,2),'1','R');
			$this->SetXY(90,$y);$this->MultiCell(20,5,number_format($total_acti_pim,2),'1','R');
			$x_i = 110;
			$total_acum_acti = 0;
			for($n=$mes_ini;$n<=$mes_fin;$n++){
				$all_acti[$n]+=$total_acti[$n];
				$this->SetXY($x_i,$y);$this->MultiCell(30,5,number_format($total_acti[$n],2),'1','R');										
				$x_i += 30;
				$total_acum_acti+=$total_acti[$n];
			}
			$this->setXY(290,$y);$this->MultiCell(20,5,number_format($total_acum_acti,2),'1','R');
			$this->setXY(310,$y);$this->MultiCell(20,5,number_format($total_acti_pim-$total_acum_acti,2),'1','R');
			if($total_acti_pim>0)$indi=number_format($total_acum_acti*100/$total_acti_pim,2);
			else $indi = "--";
			$this->setXY(330,$y);$this->MultiCell(20,5,$indi,'1','R');
			$y+=5;
			$this->SetXY(5,$y);$this->MultiCell(65,5,"SUB-TOTAL PROYECTOS",'1','C');
			$this->SetXY(70,$y);$this->MultiCell(20,5,number_format($total_proy_pia,2),'1','R');
			$this->SetXY(90,$y);$this->MultiCell(20,5,number_format($total_proy_pim,2),'1','R');
			$x_i = 110;
			$total_acum_proy = 0;
			for($n=$mes_ini;$n<=$mes_fin;$n++){
				$all_proy[$n]+=$total_proy[$n];
				$this->SetXY($x_i,$y);$this->MultiCell(30,5,number_format($total_proy[$n],2),'1','R');										
				$x_i += 30;
				$total_acum_proy+=$total_proy[$n];
			}
			$this->setXY(290,$y);$this->MultiCell(20,5,number_format($total_acum_proy,2),'1','R');
			$this->setXY(310,$y);$this->MultiCell(20,5,number_format($total_proy_pim-$total_acum_proy,2),'1','R');
			if($total_proy_pim>0)$indi=number_format($total_acum_proy*100/$total_proy_pim,2);
			else $indi = "--";
			$this->setXY(330,$y);$this->MultiCell(20,5,$indi,'1','R');
			$y+=5;
			$this->SetXY(5,$y);$this->MultiCell(65,5,"SUB-TOTAL FUENTE DE FINANCIAMIENTO - ".$fuente["fuente"]["cod"],'1','C');
			$this->SetXY(70,$y);$this->MultiCell(20,5,number_format($total_fuen_pia,2),'1','R');
			$this->SetXY(90,$y);$this->MultiCell(20,5,number_format($total_fuen_pim,2),'1','R');
			$x_i = 110;
			$total_acum_fuen = 0;
			for($n=$mes_ini;$n<=$mes_fin;$n++){
				$all_fuen[$n]+=$total_fuen[$n];
				$this->SetXY($x_i,$y);$this->MultiCell(30,5,number_format($total_fuen[$n],2),'1','R');										
				$x_i += 30;
				$total_acum_fuen+=$total_fuen[$n];
			}
			$this->setXY(290,$y);$this->MultiCell(20,5,number_format($total_acum_fuen,2),'1','R');
			$this->setXY(310,$y);$this->MultiCell(20,5,number_format($total_fuen_pim-$total_acum_fuen,2),'1','R');
			if($total_fuen_pim>0)$indi=number_format($total_acum_fuen*100/$total_fuen_pim,2);
			else $indi = "--";
			$this->setXY(330,$y);$this->MultiCell(20,5,$indi,'1','R');
			$y+=5;
		}		
		$this->SetFont('arial','B',6);
		$this->SetXY(5,$y);$this->MultiCell(65,5,"TOTAL ACTIVIDADES",'1','C');
		$this->SetXY(70,$y);$this->MultiCell(20,5,number_format($all_acti_pia,2),'1','R');
		$this->SetXY(90,$y);$this->MultiCell(20,5,number_format($all_acti_pim,2),'1','R');
		$x_i = 110;
		$all_acum_acti = 0;
		for($n=$mes_ini;$n<=$mes_fin;$n++){
			$this->SetXY($x_i,$y);$this->MultiCell(30,5,number_format($all_acti[$n],2),'1','R');										
			$x_i += 30;
			$all_acum_acti+=$all_acti[$n];
		}
		$this->setXY(290,$y);$this->MultiCell(20,5,number_format($all_acum_acti,2),'1','R');
		$this->setXY(310,$y);$this->MultiCell(20,5,number_format($all_acti_pim-$all_acum_acti,2),'1','R');
		if($all_acti_pim>0)$indi=number_format($all_acum_acti*100/$all_acti_pim,2);
		else $indi = "--";
		$this->setXY(330,$y);$this->MultiCell(20,5,$indi,'1','R');
		$y+=5;
		$this->SetXY(5,$y);$this->MultiCell(65,5,"TOTAL PROYECTOS",'1','C');
		$this->SetXY(70,$y);$this->MultiCell(20,5,number_format($all_proy_pia,2),'1','R');
		$this->SetXY(90,$y);$this->MultiCell(20,5,number_format($all_proy_pim,2),'1','R');
		$x_i = 110;
		$all_acum_proy = 0;
		for($n=$mes_ini;$n<=$mes_fin;$n++){
			$this->SetXY($x_i,$y);$this->MultiCell(30,5,number_format($all_proy[$n],2),'1','R');										
			$x_i += 30;
			$all_acum_proy+=$all_proy[$n];
		}
		$this->setXY(290,$y);$this->MultiCell(20,5,number_format($all_acum_proy,2),'1','R');
		$this->setXY(310,$y);$this->MultiCell(20,5,number_format($all_proy_pim-$all_acum_proy,2),'1','R');
		if($all_proy_pim>0)$indi=number_format($all_acum_proy*100/$all_proy_pim,2);
		else $indi = "--";
		$this->setXY(330,$y);$this->MultiCell(20,5,$indi,'1','R');
		$y+=5;
		$this->SetXY(5,$y);$this->MultiCell(65,5,"TOTAL TODAS LAS FUENTES DE FINANCIAMIENTO",'1','C');
		$this->SetXY(70,$y);$this->MultiCell(20,5,number_format($all_fuen_pia,2),'1','R');
		$this->SetXY(90,$y);$this->MultiCell(20,5,number_format($all_fuen_pim,2),'1','R');
		$x_i = 110;
		$all_acum_fuen = 0;
		for($n=$mes_ini;$n<=$mes_fin;$n++){
			$this->SetXY($x_i,$y);$this->MultiCell(30,5,number_format($all_fuen[$n],2),'1','R');										
			$x_i += 30;
			$all_acum_fuen+=$all_fuen[$n];
		}
		$this->setXY(290,$y);$this->MultiCell(20,5,number_format($all_acum_fuen,2),'1','R');
		$this->setXY(310,$y);$this->MultiCell(20,5,number_format($all_fuen_pim-$all_acum_fuen,2),'1','R');
		if($all_fuen_pim>0)$indi=number_format($all_acum_fuen*100/$all_fuen_pim,2);
		else $indi = "--";
		$this->setXY(330,$y);$this->MultiCell(20,5,$indi,'1','R');
		$y+=5;
	}
	function Footer()
	{
    	
	} 
	 
}

$pdf=new pmen('L','mm',array(210,357));
$pdf->AliasNbPages();
$pdf->SetMargins(15,39.5,0); 
$pdf->SetTitle("reporte");
$pdf->SetAutoPageBreak(false,0); 
$pdf->Open();
$pdf->Filter($filtros,$meses,$title);
$pdf->AddPage();
$pdf->Publicar($items,$meses);
$pdf->SetLeftMargin(25);
//header('Content-Type: application/pdf; charset=utf-8');
$pdf->Output();

?>