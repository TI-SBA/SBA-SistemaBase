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
		$this->SetFont('Arial','B',10);
		$this->SetXY(10,5);$this->MultiCell(277,5,"CIERRE Y CONCILIACION DEL PRESUPUESTO DEL SECTOR PUBLICO AÑO FISCAL ".$this->filtros["ano"],'0','C');
		$this->SetXY(10,10);$this->MultiCell(277,5,"FORMATO C-3\nPRESUPUESTO INSTITUCIONAL MODIFICADO (PIM) POR GRUPO GENERICO DE GASTO\n(En Nuevos Soles)",'0','C');
		$this->SetFont('Arial','B',9);
		$this->SetXY(10,5);$this->MultiCell(60,5,"SOCIEDAD DE BENEFICENCIA PUBLICA DE AREQUIPA",'0','C');
		$this->SetXY(10,5);$this->MultiCell(277,5,"Página: ".$this->PageNo()."/{nb}",'0','R');
		$this->SetFont('Arial','',7);
		$this->SetXY(10,15);$this->MultiCell(60,5,"Sistema SBPA - Módulo de Planificación y Presupuesto",'0','C');
			
		$this->SetFont('Arial','B',10);
		$fuente = "TODAS LAS FUENTES DE FINANCIAMIENTO";
		if(isset($this->filtros["fuente"])){
			$fuente = $this->filtros["fuente"]["cod"]." - ".$this->filtros["fuente"]["rubro"];
		}
		$this->SetXY(10,30);$this->MultiCell(277,5,"SOCIEDAD DE BENEFICENCIA PUBLICA DE AREQUIPA",'0','L');
		$this->SetXY(10,35);$this->MultiCell(277,5,"FUENTE DE FINANCIAMIENTO: ".$fuente,'0','L');
		$this->SetXY(10,40);$this->MultiCell(60,20,"DISPOSITIVOS LEGALES",'1','C');
		$this->SetXY(70,40);$this->MultiCell(30,5,"Personal y Obligaciones Sociales\n ",'1','C');
		$this->SetXY(100,40);$this->MultiCell(30,5,"Pensiones y otras Prestaciones Sociales",'1','C');
		$this->SetXY(130,40);$this->MultiCell(30,10,"Bienes y Servicios",'1','C');
		$this->SetXY(160,40);$this->MultiCell(30,10,"Donaciones y Transferencias",'1','C');
		$this->SetXY(190,40);$this->MultiCell(30,20,"Otros Gastos",'1','C');
		$this->SetXY(220,40);$this->MultiCell(30,5,"Adquisición de Activos No Financieros\n ",'1','C');
		$this->SetXY(250,40);$this->MultiCell(37,20,"TOTAL",'1','C');
	}		
	function Publicar($items){	
		$y=60;
		$this->SetFont('Arial','B',8);
		$this->SetXY(10,$y);$this->MultiCell(60,5,"B) CREDITOS SUPLEMENTARIOS",'1','L');
		$y+=5;
		$y_cred = $y;
		$this->Line(10, $y_cred, 287, $y_cred);
		$this->SetFont('Arial','',8);
		$tot_c_1=0;
		$tot_c_2=0;
		$tot_c_3=0;
		$tot_c_4=0;
		$tot_c_5=0;
		$tot_c_6=0;
		$tot_c_7=0;
		foreach($items["cred"] as $cred){
			$this->SetXY(70,$y);$this->MultiCell(30,5,number_format($cred["importes"]["2.1"],2),'0','R');
			$this->SetXY(100,$y);$this->MultiCell(30,5,number_format($cred["importes"]["2.2"],2),'0','R');
			$this->SetXY(130,$y);$this->MultiCell(30,5,number_format($cred["importes"]["2.3"],2),'0','R');
			$this->SetXY(160,$y);$this->MultiCell(30,5,number_format($cred["importes"]["2.4"],2),'0','R');
			$this->SetXY(190,$y);$this->MultiCell(30,5,number_format($cred["importes"]["2.5"],2),'0','R');
			$this->SetXY(220,$y);$this->MultiCell(30,5,number_format($cred["importes"]["2.6"],2),'0','R');
			$total = $cred["importes"]["2.1"]+
					$cred["importes"]["2.2"]+
					$cred["importes"]["2.3"]+
					$cred["importes"]["2.4"]+
					$cred["importes"]["2.5"]+
					$cred["importes"]["2.6"];
			$this->SetXY(250,$y);$this->MultiCell(37,5,number_format($total,2),'0','R');
			$this->SetXY(10,$y);$this->MultiCell(60,5,$cred["resolucion"],'0','L');
			$y=$this->getY();
			$tot_c_1+=$cred["importes"]["2.1"];
			$tot_c_2+=$cred["importes"]["2.2"];
			$tot_c_3+=$cred["importes"]["2.3"];
			$tot_c_4+=$cred["importes"]["2.4"];
			$tot_c_5+=$cred["importes"]["2.5"];
			$tot_c_6+=$cred["importes"]["2.6"];
			$tot_c_7+=$total;
		}
		$this->SetXY(70,$y_cred-5);$this->MultiCell(30,5,number_format($tot_c_1,2),'1','R');
		$this->SetXY(100,$y_cred-5);$this->MultiCell(30,5,number_format($tot_c_2,2),'1','R');
		$this->SetXY(130,$y_cred-5);$this->MultiCell(30,5,number_format($tot_c_3,2),'1','R');
		$this->SetXY(160,$y_cred-5);$this->MultiCell(30,5,number_format($tot_c_4,2),'1','R');
		$this->SetXY(190,$y_cred-5);$this->MultiCell(30,5,number_format($tot_c_5,2),'1','R');
		$this->SetXY(220,$y_cred-5);$this->MultiCell(30,5,number_format($tot_c_6,2),'1','R');
		$this->SetXY(250,$y_cred-5);$this->MultiCell(37,5,number_format($tot_c_7,2),'1','R');
		$this->Line(10, $y, 287, $y);
		$this->Line(10, $y_cred, 10, $y);
		$this->Line(70, $y_cred, 70, $y);
		$this->Line(100, $y_cred, 100, $y);
		$this->Line(130, $y_cred, 130, $y);
		$this->Line(160, $y_cred, 160, $y);
		$this->Line(190, $y_cred, 190, $y);
		$this->Line(220, $y_cred, 220, $y);
		$this->Line(250, $y_cred, 250, $y);
		$this->Line(287, $y_cred, 287, $y);
		$this->SetFont('Arial','B',8);
		$this->SetXY(10,$y);$this->MultiCell(277,5,"C) HABILITACIONES Y TRANSF. DE PARTIDA",'1','L');
		$y+=5;
		$this->SetXY(10,$y);$this->MultiCell(60,5,"D) HABILITACIONES Y ANULACIONES",'1','L');
		$y+=5;
		$y_nota = $y;
		$this->Line(10, $y_nota, 287, $y_nota);
		$this->SetFont('Arial','',8);
		$tot_n_1=0;
		$tot_n_2=0;
		$tot_n_3=0;
		$tot_n_4=0;
		$tot_n_5=0;
		$tot_n_6=0;
		$tot_n_7=0;
		foreach($items["nota"] as $nota){	
			$this->SetXY(70,$y);$this->MultiCell(30,5,number_format($nota["importes"]["2.1"],2),'0','R');
			$this->SetXY(100,$y);$this->MultiCell(30,5,number_format($nota["importes"]["2.2"],2),'0','R');
			$this->SetXY(130,$y);$this->MultiCell(30,5,number_format($nota["importes"]["2.3"],2),'0','R');
			$this->SetXY(160,$y);$this->MultiCell(30,5,number_format($nota["importes"]["2.4"],2),'0','R');
			$this->SetXY(190,$y);$this->MultiCell(30,5,number_format($nota["importes"]["2.5"],2),'0','R');
			$this->SetXY(220,$y);$this->MultiCell(30,5,number_format($nota["importes"]["2.6"],2),'0','R');
			$total = $nota["importes"]["2.1"]+
					$nota["importes"]["2.2"]+
					$nota["importes"]["2.3"]+
					$nota["importes"]["2.4"]+
					$nota["importes"]["2.5"]+
					$nota["importes"]["2.6"];
			$this->SetXY(250,$y);$this->MultiCell(37,5,number_format($total,2),'0','R');
			$this->SetXY(10,$y);$this->MultiCell(60,5,$nota["resolucion"],'0','L');
			$y=$this->getY();
			$tot_n_1+=$nota["importes"]["2.1"];
			$tot_n_2+=$nota["importes"]["2.2"];
			$tot_n_3+=$nota["importes"]["2.3"];
			$tot_n_4+=$nota["importes"]["2.4"];
			$tot_n_5+=$nota["importes"]["2.5"];
			$tot_n_6+=$nota["importes"]["2.6"];
			$tot_n_7+=$total;
		}
		$this->SetXY(70,$y_nota-5);$this->MultiCell(30,5,number_format($tot_n_1,2),'1','R');
		$this->SetXY(100,$y_nota-5);$this->MultiCell(30,5,number_format($tot_n_2,2),'1','R');
		$this->SetXY(130,$y_nota-5);$this->MultiCell(30,5,number_format($tot_n_3,2),'1','R');
		$this->SetXY(160,$y_nota-5);$this->MultiCell(30,5,number_format($tot_n_4,2),'1','R');
		$this->SetXY(190,$y_nota-5);$this->MultiCell(30,5,number_format($tot_n_5,2),'1','R');
		$this->SetXY(220,$y_nota-5);$this->MultiCell(30,5,number_format($tot_n_6,2),'1','R');
		$this->SetXY(250,$y_nota-5);$this->MultiCell(37,5,number_format($tot_n_7,2),'1','R');
		$this->Line(10, $y, 287, $y);
		$this->Line(10, $y_nota, 10, $y);
		$this->Line(70, $y_nota, 70, $y);
		$this->Line(100, $y_nota, 100, $y);
		$this->Line(130, $y_nota, 130, $y);
		$this->Line(160, $y_nota, 160, $y);
		$this->Line(190, $y_nota, 190, $y);
		$this->Line(220, $y_nota, 220, $y);
		$this->Line(250, $y_nota, 250, $y);
		$this->Line(287, $y_nota, 287, $y);
		//$y+=5;
		$this->SetXY(10,$y);$this->MultiCell(60,5,"TOTAL",'1','C');
		$this->SetXY(70,$y);$this->MultiCell(30,5,number_format($tot_n_1+$tot_c_1,2),'1','R');
		$this->SetXY(100,$y);$this->MultiCell(30,5,number_format($tot_n_2+$tot_c_2,2),'1','R');
		$this->SetXY(130,$y);$this->MultiCell(30,5,number_format($tot_n_3+$tot_c_3,2),'1','R');
		$this->SetXY(160,$y);$this->MultiCell(30,5,number_format($tot_n_4+$tot_c_4,2),'1','R');
		$this->SetXY(190,$y);$this->MultiCell(30,5,number_format($tot_n_5+$tot_c_5,2),'1','R');
		$this->SetXY(220,$y);$this->MultiCell(30,5,number_format($tot_n_6+$tot_c_6,2),'1','R');
		$this->SetXY(250,$y);$this->MultiCell(37,5,number_format($tot_n_7+$tot_c_7,2),'1','R');
	}
	function Footer()
	{
    	//Footer de la pagina
	}  
}

$pdf=new repo('L','mm','A4');
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