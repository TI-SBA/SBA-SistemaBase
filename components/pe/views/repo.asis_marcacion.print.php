<?php
global $f;
$f->library('pdf');

class repo extends FPDF
{
	var $filtros;
	function  filtros($filtros){
		$this->filtros = $filtros;
	}
	function Header(){
		$meses = array("TODOS","ENERO","FEBRERO","MARZO","ABRIL","MAYO","JUNIO","JULIO","AGOSTO","SETIEMBRE","OCTUBRE","NOVIEMBRE","DICIEMBRE");
		$this->SetFont('Arial','B',13);
		$this->SetXY(10,20);$this->MultiCell(190,5,"ASISTENCIA: MARCACIONES\n".$this->filtros['fecini']." - ".$this->filtros['fecfin']." ",'0','C');
		$this->SetFont('Arial','B',9);
		$this->SetXY(10,5);$this->MultiCell(60,5,"SOCIEDAD DE BENEFICENCIA PUBLICA DE AREQUIPA",'0','C');
		$this->SetXY(10,5);$this->MultiCell(190,5,"Página: ".$this->PageNo()."/{nb}",'0','R');
		$this->SetFont('Arial','',7);
		$this->SetXY(10,15);$this->MultiCell(60,5,"Sistema SBPA - Módulo de Personal",'0','C');
			
		$this->SetFont('Arial','B',9);
		$this->SetXY(15,35);$this->MultiCell(90,10,"PROGRAMAS",'1','C');
		$this->SetXY(105,35);$this->MultiCell(30,10,"ENTRADA",'1','C');
		$this->SetXY(135,35);$this->MultiCell(30,10,"SALIDA",'1','C');
		$this->SetXY(165,35);$this->MultiCell(30,10,"TIEMPO (hh:mm)",'1','C');
	}		
	function Publicar($items){
		$y=45;
		$t_1=0;
		$t_2=0;
		$t_3=0;
		foreach($items as $orga){
			$t_par = 0;
			$this->SetFont('Arial','BU',9);	
			$this->SetXY(15,$y);$this->MultiCell(180,5,"Programa: ".$orga["programa"]["nomb"],'0','L');
			$y=$this->getY();
			$this->SetFont('Arial','',9);
			$tot_1 = 0;	
			$tot_2 = 0;
			$tot_3 = 0;
			foreach($orga["trabajadores"] as $trab){
				if($y>277){
					$this->AddPage();
					$y=45;
				}
				$this->SetXY(15,$y);$this->MultiCell(150,5,$trab["trabajador"]["appat"]." ".$trab["trabajador"]["apmat"]." ".$trab["trabajador"]["nomb"],'0','L');
				if(isset($trab['bloques'])){
					if(count($trab['bloques'])>0){
						foreach($trab['bloques'] as $blo){
							$entrada = 0;
							$salida = 0;
							$this->SetFont('Arial','',8);
							if(isset($blo['entrada'])){
								$entrada = $blo['entrada']['fecreg']->sec;
								$this->SetXY(105,$y);$this->MultiCell(40,5,date('d/m/Y H:i:s',$blo['entrada']['fecreg']->sec),'0','L');	
							}
							if(isset($blo['salida'])){
								$salida = $blo['salida']['fecreg']->sec;
								$this->SetXY(135,$y);$this->MultiCell(40,5,date('d/m/Y H:i:s',$blo['salida']['fecreg']->sec),'0','L');
							}
							$this->SetFont('Arial','',9);
							if($entrada>0 && $salida > 0){
								$horas = $salida-$entrada;
								$horas_ent = (int)($horas/(60*60));
								$horas_fra = ($horas/(60*60));
								//$minutos = ceil(($horas_fra-$horas_ent)/(60));
								$minutos = ceil(($horas_fra-$horas_ent)*60);
							}else{
								$horas_ent = "--";
								$minutos = "--";
							}
							if(intval($minutos)<10){
								$minutos = '0'.$minutos;
							}
							$this->SetXY(165,$y);$this->MultiCell(30,5,$horas_ent.":".$minutos,'0','R');
							$y=$this->GetY();
						}
					}
				}
				$this->Line(15, $y, 195, $y);	
				$y=$this->GetY();
			}
			/*$this->Line(15, $y, 195, $y);
			$this->SetXY(15,$y);$this->MultiCell(150,5,"SUB-TOTAL",'0','C');
			$this->SetXY(105,$y);$this->MultiCell(30,5,number_format($tot_1,2),'0','R');
			$this->SetXY(135,$y);$this->MultiCell(30,5,number_format($tot_2,2),'0','R');
			$this->SetXY(165,$y);$this->MultiCell(30,5,number_format($tot_3,2),'0','R');*/
			$y=$this->GetY();
		}
		$this->Line(15, $y, 195, $y);
		/*$this->SetXY(15,$y);$this->MultiCell(150,5,"TOTAL",'0','C');
		$this->SetXY(105,$y);$this->MultiCell(30,5,number_format($t_1,2),'0','R');
		$this->SetXY(135,$y);$this->MultiCell(30,5,number_format($t_2,2),'0','R');
		$this->SetXY(165,$y);$this->MultiCell(30,5,number_format($t_3,2),'0','R');*/
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
$pdf->filtros($filtros);
$pdf->Open();
$pdf->AddPage();
$pdf->Publicar($items);
$pdf->SetLeftMargin(25);
//header('Content-Type: application/pdf; charset=utf-8');
$pdf->Output();
?>