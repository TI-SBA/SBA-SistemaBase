<?php
global $f;
$f->library('pdf');

class repo extends FPDF
{
	var $mes;
	var $ano;
	function Filter($filtros){
		$this->mes = $filtros["mes"];
		$this->ano = $filtros["ano"];
	}
	function Header(){
		$meses = array("Todos","Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Setiembre","Octubre","Noviembre","Diciembre");
		//$this->Image(IndexPath.DS.'templates/pr/presupuestos.gif',15,15,180,267);	
		$this->SetFont('Arial','B',15);
		$this->setY(15);$this->Cell(0,10,"SOCIEDAD DE BENEFICENCIA PUBLICA DE AREQUIPA",0,0,'C');
		$this->SetFont('Arial','I',13);
		$this->setY(20);$this->Cell(0,10,"NOTAS A LOS ESTADOS FINANCIEROS NÚMERICOS ".strtoupper($meses[$this->mes])." - ".$this->ano,0,0,'C');
	}		
	function Publicar($items){
		$x=0;
		$y=35;
		$y_ini = $y;
		$page_b = 235;
		foreach($items as $item){
			if(isset($item["otros"])){
				if((count($item["otros"])*5 + $y)>$page_b){
					$this->AddPage();
					$y = $y_ini;
				}
			}elseif(isset($item["activos"])){
				if((count($item["activos"])*5 + $y)>$page_b){
					$this->AddPage();
					$y = $y_ini;
				}
			}
			$this->SetFont('Arial',"BU",10);
			$this->SetXY(25,$y);$this->MultiCell(180,5,"NOTA Nro. ".$item["num"]." - ".strtoupper($item["nomb"]),'0','L');			
			$y=$y+5;			
			if(isset($item["otros"])){
				$total = 0;				
				foreach($item["otros"] as $otros){			
					$cod_length = strlen($otros["cuenta"]["cod"]);
					if($cod_length==4){
						$x_left = 170;
						$this->SetFont('Arial','B',8);
						$total = $total + $otros["monto"];
					}elseif($cod_length==7){
						$x_left = 150;
						$this->SetFont('Arial','B',8);
					}elseif($cod_length>7){
						$x_left = 130;
						$this->SetFont('Arial','',8);
					}
					$this->SetXY(25,$y);$this->MultiCell(25,5,$otros["cuenta"]["cod"],'0','L');
					$this->SetXY(50,$y);$this->MultiCell(80,5,substr($otros["cuenta"]["descr"],0,45),'0','L');
					$this->SetXY($x_left,$y);$this->MultiCell(20,5,number_format($otros["monto"],2,".", ","),'0','R');
					$y=$y+5;
				}
				$this->SetXY(170,$y);$this->MultiCell(20,5,number_format($total,2,".", ","),'1','R');
			}elseif(isset($item["activos"])){
				/** Header */
				$this->SetFont('Arial','B',8);
				$this->SetXY(25,$y);$this->MultiCell(25,10,"CODIGO",'1','C');
				$this->SetXY(50,$y);$this->MultiCell(50,10,"DENOMINACION",'1','C');
				$this->SetXY(100,$y);$this->MultiCell(30,10,"VALOR BRUTO",'1','C');
				$this->SetXY(130,$y);$this->MultiCell(30,5,"DEPRECIACION ACUMULADA",'1','C');
				$this->SetXY(160,$y);$this->MultiCell(30,10,"VALOR NETO",'1','C');
				$y=$y+10;
				/**  /Header */
				$total_bruto = 0;
				$total_depre = 0;
				$total_neto = 0;
				foreach($item["activos"] as $activos){
					$this->SetFont('Arial','',7);
					$cod_length = strlen($activos["cuenta"]["cod"]);
					if($cod_length==4){
						$x_left = 170;
						$this->SetFont('Arial','B',7);
						$total_bruto = $total_bruto + $activos["valor_bruto"];
						$total_depre = $total_depre + $activos["depreciacion"];
						$total_neto = $total_neto + $activos["valor_neto"];
					}
					$this->SetXY(25,$y);$this->MultiCell(25,5,$activos["cuenta"]["cod"],'1','L');
					$this->SetXY(50,$y);$this->MultiCell(50,5,substr($activos["cuenta"]["descr"],0,33),'1','L');
					$this->SetXY(100,$y);$this->MultiCell(30,5,number_format($activos["valor_bruto"],2,".", ","),'1','R');
					$this->SetXY(130,$y);$this->MultiCell(30,5,number_format($activos["depreciacion"],2,".", ","),'1','R');
					$this->SetXY(160,$y);$this->MultiCell(30,5,number_format($activos["valor_neto"],2,".", ","),'1','R');
					$y=$y+5;
				}
				$this->SetFont('Arial','B',7);
				$this->SetXY(100,$y);$this->MultiCell(30,5,number_format($total_bruto,2,".", ","),'1','R');
				$this->SetXY(130,$y);$this->MultiCell(30,5,number_format($total_depre,2,".", ","),'1','R');
				$this->SetXY(160,$y);$this->MultiCell(30,5,number_format($total_neto,2,".", ","),'1','R');
			}else{
				//No Aplicable
			}
			$y=$y+10;
		}
	}
	function Footer()
	{
    	//Footer de la pagina
	}  
}

$pdf=new repo('P','mm','A4');
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