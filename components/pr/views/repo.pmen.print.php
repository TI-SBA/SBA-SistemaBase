<?php
global $f;
$f->library('pdf');

class pmen extends FPDF
{
	var $organizacion;
	var $organomb;
	var $periodo;
	var $mes;
	var $tipo;
	var $fuente;
	var $etapa;
	function Filter($filtros){
		$this->organizacion = $filtros["organizacion"];
		$this->organomb = $filtros["organomb"];
		$this->periodo = $filtros["periodo"];	
		$this->mes = $filtros["mes"];
		$this->tipo = $filtros["tipo"];
		$this->fuente = $filtros["fuente"];
		$this->etapa = $filtros["etapa"];
	}
	function Header(){
		$meses = array("Todos","Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Setiembre","Octubre","Noviembre","Diciembre");
		$tipos = array("I"=>"INGRESOS","G"=>"GASTOS");
		$etapas = array(
			""=>"PIM",
			"A"=>"PIA"
		);
		//$this->Image(IndexPath.DS.'templates/pr/presupuestos.gif',15,15,180,267);	
		$this->SetFont('Arial','B',9);
		$this->SetXY(5,5);$this->MultiCell(60,5,"SOCIEDAD DE BENEFICENCIA PUBLICA DE AREQUIPA",'0','C');
		$this->SetXY(10,5);$this->MultiCell(277,5,"Página: ".$this->PageNo()."/{nb}",'0','R');
		$this->SetFont('Arial','',7);
		$this->SetXY(5,15);$this->MultiCell(60,5,"Sistema SBPA - Módulo de Planificacion y Presupuestos",'0','C');	
		$this->SetFont('Arial','BU',11);
		$this->setXY(10,5);$this->MultiCell(277,5,"ANEXO Nº 2",'0','C');
		$this->SetFont('Arial','B',11);
		$this->setY(10,10);$this->MultiCell(277,5,"PROCESO PRESUPUESTARIO DE LAS SOCIEDADES DE BENEFICENCIA \n PROGRAMACION MENSUALIZADA DE ".$tipos[$this->tipo]." DEL AÑO ".$this->periodo." - ".(($this->fuente=="")?"CONSOLIDADO":"POR FUENTE DE FINANCIAMIENTO"),'0','C');
		$this->setXY(10,20);$this->MultiCell(277,5,$etapas[$this->etapa],'0','C');
		$this->SetFont('arial','B',8);
		if($this->organizacion!=""){
			$this->setXY(5,28);$this->MultiCell(250,5,"ORGANIZACIÓN: ".$this->organomb,'0','L');
		}
		$this->setXY(5,32);$this->MultiCell(113,5,"PROVINCIA: AREQUIPA",'0','L');
		$this->SetFont('arial','',6);
		$this->setXY(5,37);$this->MultiCell(65,3.3,"TIPO DE TRANSACCION \n GENERICA \n   SUB GENERICA - NIVEL 1 \n     SUB GENERICA - NIVEL 2 \n      ESPECIFICA - NIVEL 1 \n       ESPECIFICA - NIVEL 2",'1','L');//85
		$this->Rect(70, 37, 40, 5);
		$this->setXY(110,37);$this->MultiCell(180,5,"",'1','C');
		$this->SetFont('arial','B',6);
		$this->Rect(70, 42, 20, 15);$this->setXY(70,42);$this->MultiCell(20,5,"PRESUPUESTO AUTORIZADO (PIA) ".$this->periodo,'0','C');
		$this->Rect(90, 42, 20, 15);$this->setXY(90,42);$this->MultiCell(20,15,"TOTAL ".$this->periodo,'0','C');
		$this->Rect(110, 42, 15, 15);$this->setXY(110,42);$this->MultiCell(15,15,"ENERO",'0','C');
		$this->Rect(125, 42, 15, 15);$this->setXY(125,42);$this->MultiCell(15,15,"FEBRERO",'0','C');
		$this->Rect(140, 42, 15, 15);$this->setXY(140,42);$this->MultiCell(15,15,"MARZO",'0','C');
		$this->Rect(155, 42, 15, 15);$this->setXY(155,42);$this->MultiCell(15,15,"ABRIL",'0','C');
		$this->Rect(170, 42, 15, 15);$this->setXY(170,42);$this->MultiCell(15,15,"MAYO",'0','C');
		$this->Rect(185, 42, 15, 15);$this->setXY(185,42);$this->MultiCell(15,15,"JUNIO",'0','C');
		$this->Rect(200, 42, 15, 15);$this->setXY(200,42);$this->MultiCell(15,15,"JULIO",'0','C');
		$this->Rect(215, 42, 15, 15);$this->setXY(215,42);$this->MultiCell(15,15,"AGOSTO",'0','C');
		$this->Rect(230, 42, 15, 15);$this->setXY(230,42);$this->MultiCell(15,15,"SETIEMBRE",'0','C');
		$this->Rect(245, 42, 15, 15);$this->setXY(245,42);$this->MultiCell(15,15,"OCTUBRE",'0','C');
		$this->Rect(260, 42, 15, 15);$this->setXY(260,42);$this->MultiCell(15,15,"NOVIEMBRE",'0','C');
		$this->Rect(275, 42, 15, 15);$this->setXY(275,42);$this->MultiCell(15,15,"DICIEMBRE",'0','C');
		//$this->setXY(161,47);$this->MultiCell(34,15,"TOTAL",'1','C');
	}
	function Publicar($items){
		$x=0;
		$y=57;
		$y_marg = 4.8;
		$alto = 5;
		$pia = 0;
		$total_metas = 0;
		$total_proy = 0;
		$total_[0] = 0;
		$total_[1] = 0;
		$total_[2] = 0;
		$total_[3] = 0;
		$total_[4] = 0;
		$total_[5] = 0;
		$total_[6] = 0;
		$total_[7] = 0;
		$total_[8] = 0;
		$total_[9] = 0;
		$total_[10] = 0;
		$total_[11] = 0;
		$total_[12] = 0;
		foreach($items as $fuente){
			$this->SetFont('arial','B',8);
			$this->SetXY(5,$y);$this->MultiCell(285,5,"FUENTE: ".$fuente["fuente"]["cod"]." ".strtoupper($fuente["fuente"]["nomb"]),'1','L');	
			$y=$y+5;
			foreach($fuente["items"] as $row){
				if ($y>=180){//180
					$this->SetFont('arial','B',8);
					$this->AddPage();$y=57;				
					$x=0;
					$this->SetXY(5,$y);$this->MultiCell(285,5,"FUENTE: ".$fuente["fuente"]["cod"]." ".strtoupper($fuente["fuente"]["nomb"]),'1','L');	
					$y=$y+5;
					$this->setY($y);
				}
				$this->SetFont('arial','',6);	
				$this->Rect(5, $y, 20, $alto);$this->SetXY(5,$y);$this->MultiCell(20,5,$row["clasificador"]["cod"],'0','L');	
				$this->Rect(25, $y, 45, $alto);$this->SetXY(25,$y);$this->MultiCell(45,5,substr($row["clasificador"]["nomb"],0,30),'0','L');//65		
				$x_i = 110;
				$total = 0;		
				for($i=1;$i<=12;$i++){					
					$suma = array_sum($row["importes"][$i]);
					if(strlen($row["clasificador"]["cod"])==1){
						$total_[$i] +=$suma;
						$total_proy +=$suma;
					}
					if($suma!=0){
						$suma = number_format($suma,2,".", ",");
						$total += array_sum($row["importes"][$i]);
					}else{
						$suma = "";
					}
					$this->SetXY($x_i,$y);$this->MultiCell(15,5,$suma,'1','R');										
					$x_i = $x_i + 15;
				}
				if(strlen($row["clasificador"]["cod"])==1){
					$total_[0] +=$total; 
					$pia +=array_sum($row["pia"]); 
				}
				$this->SetXY(70,$y);$this->MultiCell(20,5,number_format(array_sum($row["pia"]),2,".", ","),'1','R');
				$this->SetXY(90,$y);$this->MultiCell(20,5,number_format($total,2,".", ","),'1','R');
				$y=$y+5;
			}
			foreach($fuente["metas"] as $meta){
				$this->SetFont('arial','B',6);
				$this->SetXY(5,$y);$this->MultiCell(285,5,"META: ".$meta["meta"]["cod"]." ".strtoupper($meta["meta"]["nomb"]),'1','L');
				$y+=5;
				foreach($meta["items"] as $row){
					if ($y>=180){//180
						$this->SetFont('arial','B',6);
						$this->AddPage();$y=57;				
						$x=0;
						$this->SetXY(5,$y);$this->MultiCell(285,5,"META: ".$meta["meta"]["cod"]." ".strtoupper($meta["meta"]["nomb"]),'1','L');
						$y=$y+5;
						$this->setY($y);
					}
					$this->SetFont('arial','',6);
					$this->Rect(5, $y, 20, $alto);$this->SetXY(5,$y);$this->MultiCell(20,5,$row["clasificador"]["cod"],'0','L');	
					$this->Rect(25, $y, 65, $alto);$this->SetXY(25,$y);$this->MultiCell(65,5,substr($row["clasificador"]["nomb"],0,42),'0','L');
					
					$x_i = 110;
					$total = 0;		
					for($i=1;$i<=12;$i++){					
						$suma = array_sum($row["importes"][$i]);
						if(strlen($row["clasificador"]["cod"])==1){
							$total_[$i] +=$suma;
							$total_metas +=$suma;
						}
						if($suma!=0){
							$suma = number_format($suma,2,".", ",");
							$total += array_sum($row["importes"][$i]);
						}else{
							$suma = "";
						}
						$this->SetXY($x_i,$y);$this->MultiCell(15,5,$suma,'1','R');										
						$x_i = $x_i + 15;
					}
					if(strlen($row["clasificador"]["cod"])==1){
						$total_[0] +=$total; 
						$pia +=array_sum($row["pia"]); 
					}
					$this->SetXY(70,$y);$this->MultiCell(20,5,number_format(array_sum($row["pia"]),2,".", ","),'1','R');
					$this->SetXY(90,$y);$this->MultiCell(20,5,number_format($total,2,".", ","),'1','R');		
					$y+=5;
				}	
			}
		}
		$x_t_i = 110;
		$this->Rect(5, $y, 65, $alto);$this->SetXY(5,$y);$this->MultiCell(65,5,"TOTAL",'0','C');
		$this->SetXY(70,$y);$this->MultiCell(20,5,number_format($pia,2,".", ","),'1','R');
		$this->SetXY(90,$y);$this->MultiCell(20,5,number_format($total_[0],2,".", ","),'1','R');
		for($i=1;$i<=12;$i++){
			$this->SetXY($x_t_i,$y);$this->MultiCell(15,5,number_format($total_[$i],2,".", ","),'1','R');
			$x_t_i = $x_t_i + 15;
		}
		$y+=10;
		$this->SetXY(50,$y);$this->MultiCell(50,5,"TOTAL METAS",'0','C');
		$this->SetXY(100,$y);$this->MultiCell(20,5,number_format($total_metas,2,".", ","),'1','R');
		$y+=5;
		$this->SetXY(50,$y);$this->MultiCell(50,5,"TOTAL PROYECTOS",'0','C');
		$this->SetXY(100,$y);$this->MultiCell(20,5,number_format($total_proy,2,".", ","),'1','R');
		
	}
	function Footer()
	{
    	
	} 
	 
}

$pdf=new pmen('L','mm','A4');
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