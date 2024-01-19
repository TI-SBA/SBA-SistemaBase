<?php
global $f;
$f->library('pdf');
class hosp extends FPDF{
	function Header(){
		//$this->Image(IndexPath.DS.'templates/td/expd.gif',15,15,347,249);
		$this->SetFont('Arial','B',14);
		$this->SetXY(10,10);$this->MultiCell(357,5,"ESTADO HOSPITALIZACIÓN",'0','C');
		$this->SetFont('Arial','B',9);
		$this->SetXY(10,5);$this->MultiCell(60,5,"SOCIEDAD DE BENEFICENCIA PUBLICA DE AREQUIPA",'0','C');
		$this->SetXY(10,5);$this->MultiCell(357,5,"Página: ".$this->PageNo()."/{nb}",'0','R');
		$this->SetFont('Arial','',7);
		$this->SetXY(10,15);$this->MultiCell(60,5,"Sistema SBPA - Módulo de Hospitalizaciones",'0','C');
		$this->SetFont('arial','B',10);
		$this->SetXY(15,26);$this->MultiCell(360,10,"CAT.    N°H.C.               Documento                  Fecha                                               Apellidos y Nombres",'1','L');//28
		$this->SetXY(15,36);$this->MultiCell(110,20,"",'1','L');//55
		$this->SetXY(125,36);$this->Cell(125,10,'Hospitalización categoleta',1,0,'C');
		$this->SetXY(250,36);$this->Cell(125,10,'Hospitalización Parcial',1,0,'C');
		$this->SetXY(125,46);$this->Cell(125,10,'           Del                 Al                        días/meses                          Fec.Alta',1,0,'L');
		$this->SetXY(250,46);$this->Cell(125,10,'           Del                 Al                        días/meses                          Fec.Alta',1,0,'L');
	}
	function Publicar($catego){
		$categorias= array(
			"1"=>"S/E",
			"2"=>"PP",
			"3"=>"P",
			"4"=>"A",
			"5"=>"B",
			"6"=>"C",
			"7"=>"E",
			"10"=>"Nue.",
			"11"=>"Cont.",
			"8"=>"D"

		);
		$this->SetFont('arial','',10);
		$y_marg = 10;
		$y=56;
		$this->SetY($y);
		if(count($catego)>0){
            foreach($catego as $i=>$item){
                if(isset($item['hist_cli'])){
                    foreach($item['efectivos'] as $ii=>$efectivo){
                        if(isset($efectivo['monto_old'])){
                            print_r($item['hospitalizacion']['categoria']);
							print_r('|');
							print_r($item['hospitalizacion']['cant']);
                            print_r('|');
                            print_r($item['hist_cli']);
                            print_r('|');
                            print_r($efectivo['monto']);
							print_r('|');
							print_r($efectivo['monto_old']."\n");
                            
                        }
                        
                    }
                }
                
            }
		}
	}
	/*function Footer(){
    	$this->SetXY(220,-15);
    	$this->SetFont('Arial','I',8);
    	$this->SetTextColor(128);
    	$this->Cell(0,10,$this->PageNo(),0,0,'C');
    	$this->SetXY(29,-15);
    	$this->SetTextColor(128);
    	$this->Cell(0,10,date("d/m/Y"),0,0,'L');
	} */
}
$pdf=new hosp('P','mm',array(387,279));
$pdf->AliasNbPages();
$pdf->SetMargins(15,39.5,0); 
$pdf->SetTitle("reporte");
$pdf->SetAutoPageBreak(false,0); 
$pdf->Open();
$pdf->AddPage();
$pdf->Publicar($catego);
$pdf->SetLeftMargin(25);
$pdf->Output();
?>