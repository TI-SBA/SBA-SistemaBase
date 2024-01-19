<?php
global $f;
$f->library('pdf');

class repo extends FPDF
{
	var $trimestre;
	var $ano;
	function Filter($filtros){
		$this->trimestre = $filtros["trimestre"];
		$this->ano = $filtros["ano"];
	}
	function Header(){
		$trimestres = array(
				"1"=>"I Trimestre",
				"2"=>"II Trimestre",
				"3"=>"III Trimestre",
				"4"=>"IV Trimestre"
		);
		if($this->trimestre=="1"){
			$mes1 = "Enero";
			$mes2 = "Febrero";
			$mes3 = "Marzo";
		}elseif($this->trimestre=="2"){
			$mes1 = "Abril";
			$mes2 = "Mayo";
			$mes3 = "Junio";
		}elseif($this->trimestre=="3"){
			$mes1 = "Julio";
			$mes2 = "Agosto";
			$mes3 = "Setiembre";
		}elseif($this->trimestre=="4"){
			$mes1 = "Octubre";
			$mes2 = "Noviembre";
			$mes3 = "Diciembre";
		}
		//$this->Image(IndexPath.DS.'templates/pr/presupuestos.gif',15,15,180,267);	
		$this->SetFont('Arial','B',12);
		$this->setY(5);$this->MultiCell(260,5,"PROCESO PRESUPUESTARIO DE LAS SOCIEDADES DE BENEFICENCIA\n PRESUPUESTO AUTORIZADO DE GASTOS ".strtoupper($trimestres[$this->trimestre])." - ".$this->ano,'0','C');
		$this->SetFont('Arial','B',6);
		$this->setXY(5,17);$this->MultiCell(85,4,"TIPO DE TRANSACCION \n SUB GENERICA - NIVEL 1 \n  SUB GENERICA - NIVEL 2 \n   ESPECIFICA - NIVEL 1 \n      ESPECIFICAL - NIVEL 2",'1','L');
		//$this->Rect(105, 27, 75, 5);
		$this->setXY(90,17);$this->MultiCell(20,5,"PPTO.\nAUTORIZADO\nPIM\n".$this->ano,'1','C');
		$this->setXY(110,17);$this->MultiCell(20,10,"COMPROMISO MES ".strtoupper($mes1),'1','C');
		$this->setXY(130,17);$this->MultiCell(20,10,"COMPROMISO MES ".strtoupper($mes2),'1','C');
		$this->setXY(150,17);$this->MultiCell(20,10,"COMPROMISO MES ".strtoupper($mes3),'1','C');
		$this->setXY(170,17);$this->MultiCell(20,20,"ACUMULADO",'1','C');
		$this->setXY(190,17);$this->MultiCell(20,10,"EJECUCION MES ".strtoupper($mes1),'1','C');
		$this->setXY(210,17);$this->MultiCell(20,10,"EJECUCION MES ".strtoupper($mes2),'1','C');
		$this->setXY(230,17);$this->MultiCell(20,10,"EJECUCION MES ".strtoupper($mes3),'1','C');
		$this->setXY(250,17);$this->MultiCell(20,20,"ACUMULADO",'1','C');
		$this->setXY(270,17);$this->MultiCell(20,10,"SALDO DEL PPTO",'1','C');
	}		
	function Publicar($items){
		$x=0;
		$y=37;
		$y_marg = 4.8;
		$alto = 5;
		$this->SetY($y);
		$this->SetFont('Arial','',6);
		foreach($items as $item){
			//organizaciones
			$this->Rect(5, $y, 285, 5);$this->SetXY(5,$y);$this->MultiCell(285,5,$item["funcion"]["cod"]." ".$item["programa"]["cod"]." ".$item["subprograma"]["cod"]." - ".strtoupper($item["nomb"]),'0','L');	
			$y = $y + 5;
			$o_tot_ppto = 0;
			$o_tot_comp1 = 0;
			$o_tot_comp2 = 0;
			$o_tot_comp3 = 0;
			$o_tot_compa = 0;
			$o_tot_ejec1 = 0;
			$o_tot_ejec2 = 0;
			$o_tot_ejec3 = 0;
			$o_tot_ejeca = 0;
			$o_tot_sald = 0;
			foreach($item["fuentes"] as $fuente){
				//fuentes
				$this->Rect(5, $y, 285, 5);$this->SetXY(5,$y);$this->MultiCell(285,5,"FUENTE: ".$fuente["cod"]." ".strtoupper($fuente["rubro"]),'0','L');	
				$y = $y + 5;
				$f_tot_ppto = 0;
				$f_tot_comp1 = 0;
				$f_tot_comp2 = 0;
				$f_tot_comp3 = 0;
				$f_tot_compa = 0;
				$f_tot_ejec1 = 0;
				$f_tot_ejec2 = 0;
				$f_tot_ejec3 = 0;
				$f_tot_ejeca = 0;
				$f_tot_sald = 0;
				foreach($fuente["items"] as $row){
					if ($y>=190){//190
						$this->AddPage();$y=37;				
					}
					$this->SetFont('Arial','',6);
					$this->Rect(5, $y, 20, 4);$this->SetXY(5,$y);$this->MultiCell(20,4,$row["cod"],'0','L');	
					$this->Rect(25, $y, 65, 4);$this->SetXY(25,$y);$this->MultiCell(65,4,substr($row["nomb"],0,48),'0','L');
					$ppto = array_sum($row["ppto"]);
					$this->Rect(90, $y, 20, 4);$this->SetXY(90,$y);$this->MultiCell(20,4,number_format($ppto,2,".", ","),'0','R');
					$c_mes1 = array_sum($row["comp_mes1"]); 
					$c_mes2 = array_sum($row["comp_mes2"]); 
					$c_mes3 = array_sum($row["comp_mes3"]); 
					$this->Rect(110, $y, 20, 4);$this->SetXY(110,$y);$this->MultiCell(20,4,number_format($c_mes1,2,".", ","),'0','R');	
					$this->Rect(130, $y, 20, 4);$this->SetXY(130,$y);$this->MultiCell(20,4,number_format($c_mes2,2,".", ","),'0','R');	
					$this->Rect(150, $y, 20, 4);$this->SetXY(150,$y);$this->MultiCell(20,4,number_format($c_mes3,2,".", ","),'0','R');
					$this->Rect(170, $y, 20, 4);$this->SetXY(170,$y);$this->MultiCell(20,4,number_format($c_mes1+$c_mes2+$c_mes3,2,".", ","),'0','R');
					$e_mes1 = array_sum($row["ejec_mes1"]);
					$e_mes2 = array_sum($row["ejec_mes2"]);
					$e_mes3 = array_sum($row["ejec_mes3"]);
					$this->Rect(190, $y, 20, 4);$this->SetXY(190,$y);$this->MultiCell(20,4,number_format($e_mes1,2,".", ","),'0','R');
					$this->Rect(210, $y, 20, 4);$this->SetXY(210,$y);$this->MultiCell(20,4,number_format($e_mes2,2,".", ","),'0','R');
					$this->Rect(230, $y, 20, 4);$this->SetXY(230,$y);$this->MultiCell(20,4,number_format($e_mes3,2,".", ","),'0','R');
					$this->Rect(250, $y, 20, 4);$this->SetXY(250,$y);$this->MultiCell(20,4,number_format($e_mes1+$e_mes2+$e_mes3,2,".", ","),'0','R');
					$this->Rect(270, $y, 20, 4);$this->SetXY(270,$y);$this->MultiCell(20,4,number_format($ppto-($e_mes1+$e_mes2+$e_mes3),2,".", ","),'0','R');
					$y = $y + 4;
					//sumas
					if(!isset($row["clasificadores"]["hijos"])){
						$f_tot_ppto = $f_tot_ppto+$ppto;
						$f_tot_comp1 = $f_tot_comp1+$c_mes1;
						$f_tot_comp2 = $f_tot_comp2+$c_mes2;
						$f_tot_comp3 = $f_tot_comp3+$c_mes3;
						$f_tot_compa = $f_tot_compa+$c_mes1+$c_mes2+$c_mes3;
						$f_tot_ejec1 = $f_tot_ejec1+$e_mes1;
						$f_tot_ejec2 = $f_tot_ejec2+$e_mes2;
						$f_tot_ejec3 = $f_tot_ejec3+$e_mes3;
						$f_tot_ejeca = $f_tot_ejeca+$e_mes1+$e_mes2+$e_mes3;
						$f_tot_sald = $f_tot_sald+$ppto-($e_mes1+$e_mes2+$e_mes3);
					}
				}
				$this->SetFont('Arial','B',6);
				$this->Rect(5, $y, 85, 4);$this->SetXY(5,$y);$this->MultiCell(85,4,"SUBTOTAL",'0','C');	
				$this->SetFont('Arial','',6);
				$this->Rect(90, $y, 20, 4);$this->SetXY(90,$y);$this->MultiCell(20,4,number_format($f_tot_ppto,2,".", ","),'0','R');
				$this->Rect(110, $y, 20, 4);$this->SetXY(110,$y);$this->MultiCell(20,4,number_format($f_tot_comp1,2,".", ","),'0','R');	
				$this->Rect(130, $y, 20, 4);$this->SetXY(130,$y);$this->MultiCell(20,4,number_format($f_tot_comp2,2,".", ","),'0','R');	
				$this->Rect(150, $y, 20, 4);$this->SetXY(150,$y);$this->MultiCell(20,4,number_format($f_tot_comp3,2,".", ","),'0','R');
				$this->Rect(170, $y, 20, 4);$this->SetXY(170,$y);$this->MultiCell(20,4,number_format($f_tot_compa,2,".", ","),'0','R');
				$this->Rect(190, $y, 20, 4);$this->SetXY(190,$y);$this->MultiCell(20,4,number_format($f_tot_ejec1,2,".", ","),'0','R');
				$this->Rect(210, $y, 20, 4);$this->SetXY(210,$y);$this->MultiCell(20,4,number_format($f_tot_ejec2,2,".", ","),'0','R');
				$this->Rect(230, $y, 20, 4);$this->SetXY(230,$y);$this->MultiCell(20,4,number_format($f_tot_ejec3,2,".", ","),'0','R');
				$this->Rect(250, $y, 20, 4);$this->SetXY(250,$y);$this->MultiCell(20,4,number_format($f_tot_ejeca,2,".", ","),'0','R');
				$this->Rect(270, $y, 20, 4);$this->SetXY(270,$y);$this->MultiCell(20,4,number_format($f_tot_sald,2,".", ","),'0','R');
				$y = $y + 4;
				//sumas
				$o_tot_ppto = $o_tot_ppto+$f_tot_ppto;
				$o_tot_comp1 = $o_tot_comp1+$f_tot_comp1;
				$o_tot_comp2 = $o_tot_comp2+$f_tot_comp2;
				$o_tot_comp3 = $o_tot_comp3+$f_tot_comp3;
				$o_tot_compa = $o_tot_compa+$f_tot_comp1+$f_tot_comp2+$f_tot_comp3;
				$o_tot_ejec1 = $o_tot_ejec1+$f_tot_ejec1;
				$o_tot_ejec2 = $o_tot_ejec2+$f_tot_ejec2;
				$o_tot_ejec3 = $o_tot_ejec3+$f_tot_ejec3;
				$o_tot_ejeca = $o_tot_ejeca+$f_tot_ejec1+$f_tot_ejec2+$f_tot_ejec3;
				$o_tot_sald = $o_tot_sald+$f_tot_ppto-($f_tot_ejec1+$f_tot_ejec2+$f_tot_ejec3);
			}
			$this->SetFont('Arial','B',6);
			$this->Rect(5, $y, 85, 4);$this->SetXY(5,$y);$this->MultiCell(85,4,substr("TOTAL ".strtoupper($item["nomb"]),0,60),'0','C');
			$this->SetFont('Arial','',6);
			$this->Rect(90, $y, 20, 4);$this->SetXY(90,$y);$this->MultiCell(20,4,number_format($o_tot_ppto,2,".", ","),'0','R');
			$this->Rect(110, $y, 20, 4);$this->SetXY(110,$y);$this->MultiCell(20,4,number_format($o_tot_comp1,2,".", ","),'0','R');	
			$this->Rect(130, $y, 20, 4);$this->SetXY(130,$y);$this->MultiCell(20,4,number_format($o_tot_comp2,2,".", ","),'0','R');	
			$this->Rect(150, $y, 20, 4);$this->SetXY(150,$y);$this->MultiCell(20,4,number_format($o_tot_comp3,2,".", ","),'0','R');
			$this->Rect(170, $y, 20, 4);$this->SetXY(170,$y);$this->MultiCell(20,4,number_format($o_tot_compa,2,".", ","),'0','R');
			$this->Rect(190, $y, 20, 4);$this->SetXY(190,$y);$this->MultiCell(20,4,number_format($o_tot_ejec1,2,".", ","),'0','R');
			$this->Rect(210, $y, 20, 4);$this->SetXY(210,$y);$this->MultiCell(20,4,number_format($o_tot_ejec2,2,".", ","),'0','R');
			$this->Rect(230, $y, 20, 4);$this->SetXY(230,$y);$this->MultiCell(20,4,number_format($o_tot_ejec3,2,".", ","),'0','R');
			$this->Rect(250, $y, 20, 4);$this->SetXY(250,$y);$this->MultiCell(20,4,number_format($o_tot_ejeca,2,".", ","),'0','R');
			$this->Rect(270, $y, 20, 4);$this->SetXY(270,$y);$this->MultiCell(20,4,number_format($o_tot_sald,2,".", ","),'0','R');
			$y = $y + 4;
		}		
	}
	function Footer()
	{
    	//Footer de la pagina
    	$this->SetXY(260,-21);
    	$this->SetFont('Arial','I',8);
    	$this->SetTextColor(128);
    	$this->Cell(30,10,'Página: '.$this->PageNo().'/{nb}',0,0,'R');
    	
    	$this->SetXY(15,-21);
    	$this->SetFont('Arial','I',8);
    	$this->SetTextColor(128);
    	$this->Cell(0,10,'Fecha de Impresión: '.date("d-m-Y"),0,0,'L');
	} 
}

$pdf=new repo('L','mm','A4');
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