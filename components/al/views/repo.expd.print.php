<?php
global $f;
$f->library('pdf');

class repo extends FPDF
{
	function Header(){
		//$this->Image(IndexPath.DS.'templates/pr/presupuestos.gif',15,15,180,267);	
		$this->SetFont('Arial','B',15);
		$this->setY(15);$this->Cell(0,10,"SOCIEDAD DE BENEFICENCIA PUBLICA DE AREQUIPA",0,0,'C');		
	}		
	function Publicar($row){
		$tipos = array(
			"C"=>"Civil",
			"P"=>"Penal",
			"A"=>"Administrativo",
			"L"=>"Laboral",
			"T"=>"Contensioso Administrativo"
		);
		$enc = array(
			"B"=>"Sociedad de Beneficencia Publica de Arequipa",
			"C"=>"Contraloria",
			"M"=>"Mimdes"
		);
		$x=0;
		$y=30;
		$y_marg = 4.8;
		$index=0;
		$this->SetY($y);
		$this->SetFont('Arial','I',13);
		$this->setY(20);$this->Cell(0,10,"EXPEDIENTE Nº ".$row["numero"],0,0,'C');
		$this->SetFont('Arial','',10);
		$abogado = $row["trabajador_autor"]["appat"]." ".$row["trabajador_autor"]["apmat"].", ".$row["trabajador_autor"]["nomb"];
		$autor = strlen($abogado);
		$juzgado = strlen($row["juzgado"]);
		$this->SetXY(15,$y);$this->MultiCell(180,5,"Tipo: ".$tipos[$row["tipo"]],'0','L');	
		$y+=5;
		$this->SetXY(15,$y);$this->MultiCell(180,5,"Demandante: ".$row["demandante"],'0','L');	
		$y+=5;
		$this->SetXY(15,$y);$this->MultiCell(180,5,"Demandado: ".$row["demandado"],'0','L');
		$y+=5;
		$this->SetXY(15,$y);$this->MultiCell(180,5,"Encargado: ".$enc[$row["encargado"]],'0','L');
		if(isset($row["inmueble"])){
			$y+=5;
			$this->SetXY(15,$y);$this->MultiCell(180,5,"Inmueble: ".$row["inmueble"]["descr"]." - ".$row["inmueble"]["local"]["direc"],'0','L');
		}
		$y+=10;
		$this->SetXY(15,$y);$this->MultiCell(180,5,"HISTORICO",'0','C');
		$y+=10;
		$row['historico'] = array($row['historico'][0]);
		foreach($row["historico"] as $hist){
			if($y>260){
				$this->AddPage();
				$y=30;
			}
			$this->SetXY(15,$y);$this->MultiCell(180,5,"Registrado por: ".$hist["trabajador"]["nomb"]." ".$hist["trabajador"]["appat"]." ".$hist["trabajador"]["apmat"]." El ".Date::format($hist["fecactualizacion"]->sec, "d/m/Y h:m"),'0','L');	
			$y+=5;
			if($y>260){
				$this->AddPage();
				$y=30;
			}
			$this->SetXY(15,$y);$this->MultiCell(180,5,"Materia: ".$hist["materia"],'0','L');
			$y+=5;
			if($y>260){
				$this->AddPage();
				$y=30;
			}
			$this->SetXY(15,$y);$this->MultiCell(180,5,"Juzgado: ".$hist["juzgado"],'0','L');
			$y+=5;
			if($y>260){
				$this->AddPage();
				$y=30;
			}
			$this->SetXY(15,$y);$this->MultiCell(180,5,"Estado: ".$hist["estado"],'0','L');
			$y=$this->GetY();
			$y+=5;
			if($y>260){
				$this->AddPage();
				$y=30;
			}
			$this->SetXY(15,$y);$this->MultiCell(180,5,"Ubicación: ".$hist["ubicacion"],'0','L');
			$y+=5;
			if($y>260){
				$this->AddPage();
				$y=30;
			}
			$this->SetXY(15,$y);$this->MultiCell(180,5,"Observaciones: ".$hist["observ"],'0','L');
			$y+=10;
		}
	}
	function Footer()
	{
    	//Footer de la pagina
    	$this->SetXY(170,-21);
    	$this->SetFont('Arial','I',8);
    	$this->SetTextColor(128);
    	$this->Cell(0,10,"Página: ".$this->PageNo(),0,0,'C');
    	
    	$this->SetXY(28,-21);
    	$this->SetFont('Arial','I',8);
    	$this->SetTextColor(128);
    	$this->Cell(0,10,"Fecha de Impresión: ".date("d-m-Y"),0,0,'L');
	} 
	 
}

$pdf=new repo('P','mm','A4');
$pdf->SetMargins(15,39.5,0); 
$pdf->SetTitle("reporte");
$pdf->SetAutoPageBreak(false,0); 
$pdf->Open();
$pdf->AddPage();
$pdf->Publicar($items);
$pdf->SetLeftMargin(25);
//header('Content-Type: application/pdf; charset=utf-8');
$pdf->Output();

?>