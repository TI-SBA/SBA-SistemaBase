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
		$this->SetFont('Arial','B',13);
		$this->SetXY(10,5);$this->MultiCell(190,5,"CONTROL DE LA DEUDA",'0','C');
		$this->SetFont('Arial','B',9);
		$this->SetXY(10,5);$this->MultiCell(60,5,"SOCIEDAD DE BENEFICENCIA PUBLICA DE AREQUIPA",'0','C');
		$this->SetXY(10,5);$this->MultiCell(190,5,"Página: ".$this->PageNo()."/{nb}",'0','R');
		$this->SetFont('Arial','',7);
		$this->SetXY(10,15);$this->MultiCell(60,5,"Sistema SBPA - Módulo de Caja",'0','C');
			
		$this->SetFont('Arial','B',10);
		$this->SetXY(10,30);$this->MultiCell(190,5,"DIRECCION: ".$this->filtros["espa"]["descr"]." - ".$this->filtros["espa"]["ubic"]["ref"],'0','L');
		$arre = $this->filtros["arre"]["nomb"];
		if($this->filtros["arre"]["tipo_enti"]=="P"){
			$arre .= " ".$this->filtros["arre"]["appat"]." ".$this->filtros["arre"]["apmat"];
		}
		$this->SetXY(10,35);$this->MultiCell(190,5,"NOMBRE DEL ARRENDATARIO: ".$arre,'0','L');
		$this->SetXY(10,40);$this->MultiCell(190,5,"MERCED CONDUCTIVA:",'0','L');
		$this->SetFont('Arial','B',9);
		$this->SetXY(10,45);$this->MultiCell(30,5,"FECHA",'1','C');
		$this->SetXY(40,45);$this->MultiCell(70,5,"CONCEPTO",'1','C');
		$this->SetXY(110,45);$this->MultiCell(30,5,"Nº COMP",'1','C');
		$this->SetXY(140,45);$this->MultiCell(20,5,"DEBE",'1','C');
		$this->SetXY(160,45);$this->MultiCell(20,5,"HABER",'1','C');
		$this->SetXY(180,45);$this->MultiCell(20,5,"SALDO",'1','C');
	}		
	function Publicar($items){
		$this->SetFont('Arial','',9);
		$y=50;
		//$this->SetXY(245,$y);$this->MultiCell(40,5,"",'1','C');
		$saldo = 0;
		foreach($items as $oper){
			if(isset($oper["arrendamiento"])){
				if($y>277){
					$this->AddPage();
					$y=50;
				}
				$saldo+=$oper["debe"];
				$this->SetXY(10,$y);$this->MultiCell(30,5,Date::format($oper["arrendamiento"]["feccon"]->sec,"d/m/Y"),'1','C');
				$this->SetXY(40,$y);$this->MultiCell(70,5,"Contrato ".$oper["arrendamiento"]["contrado"],'1','C');
				$this->SetXY(110,$y);$this->MultiCell(30,5,"--",'1','C');
				$this->SetXY(140,$y);$this->MultiCell(20,5,number_format($oper["debe"],2),'1','C');
				$this->SetXY(160,$y);$this->MultiCell(20,5,"",'1','C');
				$this->SetXY(180,$y);$this->MultiCell(20,5,number_format($saldo,2),'1','C');
				$y+=5;
				if(count($oper["items"])>0){
					foreach($oper["items"] as $pago){
						if($y>277){
							$this->AddPage();
							$y=50;
						}
						$saldo-=$pago["importe"];
						$comprobante = "";
						if(!isset($pago["cuenta_cobrar"]["comprobantes"])){
							foreach($pago["cuenta_cobrar"]["comprobantes"] as $comp){
								$comprobante.=" ".$comp["tipo"]." ".$comp["serie"]." - ".$comp["num"];
							}
						}
						$this->SetXY(10,$y);$this->MultiCell(30,5,Date::format($pago["cuenta_cobrar"]["fecpago"]->sec,"d/m/Y"),'1','C');
						$this->SetXY(40,$y);$this->MultiCell(70,5,"Pago ".Date::format($pago["fecpago"]->sec,"m-Y"),'1','L');
						$this->SetXY(110,$y);$this->MultiCell(30,5,$comprobante,'1','C');
						$this->SetXY(140,$y);$this->MultiCell(20,5,"",'1','C');
						$this->SetXY(160,$y);$this->MultiCell(20,5,number_format($pago["importe"],2),'1','C');
						$this->SetXY(180,$y);$this->MultiCell(20,5,number_format($saldo,2),'1','C');
						$y+=5;
					}
				}
			}
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
$pdf->filtros($filtros);
$pdf->Open();
$pdf->AddPage();
$pdf->Publicar($items);
$pdf->SetLeftMargin(25);
//header('Content-Type: application/pdf; charset=utf-8');
$pdf->Output();
?>