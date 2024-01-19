<?php
global $f;
$f->library('pdf');
$data = $items;
class repo extends FPDF
{
	var $nro;
	var $fecreg;
	function filtros($items){
		$this->fecreg = $items['responsable_antiguo']['fecreg'];
	}
	function Header(){
		//$this->Image(IndexPath.DS.'templates/ts/comprobante.gif',10,10,190,275);	
		$y=10;
		$this->SetFont('Arial','B',9);
		$this->SetXY(5,$y);$this->MultiCell(200,5,"SOCIEDAD DE BENEFICENCIA PUBLICA DE AREQUIPA",'0','C');
		$this->SetXY(5,$y+10);$this->MultiCell(200,5,"TRANSFERENCIA INTERNA DE BIENES PATRIMONIALES",'0','C');		
		$y=$y+10;
		$this->SetFont('Arial','B',9);
		$this->SetXY(160,$y-5);$this->MultiCell(30,5,"FECHA",'1','C');
		$this->SetXY(160,$y);$this->MultiCell(10,5,"DÍA",'1','C');
		$this->SetXY(170,$y);$this->MultiCell(10,5,"MES",'1','C');
		$this->SetXY(180,$y);$this->MultiCell(10,5,"AÑO",'1','C');
		$y=$y+5;
		$this->SetXY(160,$y);$this->MultiCell(10,5,Date::format($this->fecreg->sec, 'd'),'1','C');
		$this->SetXY(170,$y);$this->MultiCell(10,5,Date::format($this->fecreg->sec, 'm'),'1','C');
		$this->SetXY(180,$y);$this->MultiCell(10,5,Date::format($this->fecreg->sec, 'Y'),'1','C');
		$y=$y+5;
	}	
	
	
		function Publicar($items){
		$x=5;
		$y=25;
		$y_ini = $y;
		$page_b = 275;
		$y=$y+10;
		$this->SetXY(5,$y);$this->MultiCell(150,5,"I DATOS DEL TRABAJADOR QUE TRANSFIERE",'0','C');
		$y=$y+5;		
		$this->SetFont('Arial','B',11);
		$this->SetXY(5,$y);$this->MultiCell(60,10,"NOMBRES Y APELLIDOS",'1','C');
		$this->SetXY(65,$y);$this->MultiCell(50,10,"CARGO",'1','C');
		$this->SetXY(115,$y);$this->MultiCell(80,10,"DEPENDENCIA/OFICINA",'1','C');
		
		$y=$y+10;
		$this->SetFont('Arial',"",7);
		
		$cargo_ant=$items[responsable_antiguo][trabajador][roles][trabajador][cargo]["nomb"];
		$funcion_ant=$items[responsable_antiguo][trabajador][roles][trabajador][cargo]["funcion"];
		if($cargo_ant!=""){
			$responsable_antiguo=$cargo_ant;
		}elseif($funcion_ant!=""){
			$responsable_antiguo=$funcion_ant;
		}
				
			
			$this->Rect(5, $y, 40, $alto);$this->SetXY(5,$y);$this->MultiCell(60,5,$items[responsable_antiguo][trabajador]["nomb"]." ".$items[responsable_antiguo][trabajador]["appat"]." ".$items[responsable_antiguo][trabajador]["apmat"],'1','C');
			$this->Rect(45, $y, 100, $alto);$this->SetXY(65,$y);$this->MultiCell(50,5,$responsable_antiguo,'1','L');
			$this->Rect(145, $y, 30, $alto);$this->SetXY(115,$y);$this->MultiCell(80,5,$items[responsable_antiguo][trabajador][roles][trabajador][cargo][organizacion]["nomb"],'1','L');
			
			$y=$y+$alto;
		$this->SetFont('Arial','B',9);
		$y=$y+20;
		$this->SetXY(3,$y);$this->MultiCell(150,5,"II DATOS DEL TRABAJADOR QUE RECEPCIONA ",'0','C');
		$this->SetFont('Arial','B',11);
		$y=$y+8;
		$this->SetXY(5,$y);$this->MultiCell(60,10,"NOMBRES Y APELLIDOS",'1','C');
		$this->SetXY(65,$y);$this->MultiCell(50,10,"CARGO",'1','C');
		$this->SetXY(115,$y);$this->MultiCell(80,10,"DEPENDENCIA/OFICINA",'1','C');
		
		$cargo=$items[responsable_antiguo][destino][roles][trabajador][cargo]["nomb"];
		$funcion=$items[responsable_antiguo][destino][roles][trabajador][cargo]["funcion"];
		if($cargo!=""){
			$destino=$cargo;
		}elseif($funcion!=""){
			$destino=$funcion;
		}
		
		$this->SetFont('Arial',"",7);
		$y=$y+10;
		$this->Rect(5, $y, 40, $alto);$this->SetXY(5,$y);$this->MultiCell(60,5,$items[responsable_antiguo][destino]["nomb"]." ".$items[responsable_antiguo][destino]["appat"]." ".$items[responsable_antiguo][destino]["apmat"],'1','C');
			$this->Rect(45, $y, 100, $alto);$this->SetXY(65,$y);$this->MultiCell(50,5,$destino,'1','L');
			$this->Rect(145, $y, 30, $alto);$this->SetXY(115,$y);$this->MultiCell(80,5,$items[responsable_antiguo][destino][roles][trabajador][organizacion]["nomb"],'1','L');
			
			$y=$y+$alto;
		$i=$items["conservacion"];
		switch ($i) {
		    case $i=="B1":
		        $estado="B1 - En Uso";
		        break;
		    case  $i=="B2":
		        $estado="B2 - Sin Uso";
		        break;
		    case  $i=="B3":
		        $estado="B3 - Excedente";
		        break;
		    case  $i=="R1":
		        $estado="R1 - En Uso";
		        break;
			case  $i=="R2":
		        $estado="R2 - Sin Uso";
		        break;
			case  $i=="R3":
		        $estado="R3 - Excedente";
		        break;
			case  $i=="R4":
		        $estado="R4 - Reparable";
		        break;
			case  $i=="M1":
		        $estado="M1 - Para tr&aacute,mite de baja";
		        break;
			case  $i=="M2":
		        $estado="M2 - Baja anterio";
		        break;
			 
		}
		
		$y=$y+20;
		$this->SetFont('Arial','B',9);
		$this->SetXY(5,$y);$this->MultiCell(150,5,"III DESCRIPCION DEL BIEN",'0','C');
		$y=$y+5;	
		$this->SetFont('Arial','B',10);
		$this->SetXY(5,$y);$this->MultiCell(120,10,"DESCRIPCION DEL BIEN",'1','C');
		$this->SetXY(5,$y+10);$this->MultiCell(80,10,"NOMBRE",'1','C');
		//$this->SetXY(5,$y+10);$this->MultiCell(60,10,"DESCRIPCION DE PRODUCTO",'1','C');
		$this->SetXY(85,$y+10);$this->MultiCell(40,10,"ESTADO",'1','C');
		$this->SetXY(125,$y);$this->MultiCell(30,10,"CODIGO PATRIMONIAL",'1','C');
		$this->SetXY(155,$y);$this->MultiCell(40,20,"OBSERVACION",'1','C');
		$y=$y+20;
		$this->SetFont('Arial',"",7);
		$this->Rect(5, $y, 40, $alto);$this->SetXY(5,$y);$this->MultiCell(80,5,$items[producto]["nomb"],'1','C');
		//$this->Rect(45, $y, 100, $alto);$this->SetXY(45,$y);$this->MultiCell(60,5,$items[""],'1','L');
		$this->Rect(120, $y, 30, $alto);$this->SetXY(85,$y);$this->MultiCell(40,5,$estado,'1','L');
		$this->Rect(150, $y, 30, $alto);$this->SetXY(125,$y);$this->MultiCell(30,5,$items[producto]["cod"],'1','L');
		$this->Rect(145, $y, 30, $alto);$this->SetXY(155,$y);$this->MultiCell(40,5,$items[""],'1','C');
		//$y=$y+$alto;
		
		$y=$y+55;
		$this->SetFont('Arial','',8);
		$cargo=$items[responsable_antiguo][trabajador][roles][trabajador][cargo]["nomb"];
		$funcion=$items[responsable_antiguo][trabajador][roles][trabajador][cargo]["funcion"];
		if($cargo!=""){
			$responsable_antiguo=$cargo;
		}elseif($funcion!=""){
			$responsable_antiguo=$funcion;
		}
		
		
		
		$this->SetXY(25,$y);$this->MultiCell(60,5,"\n\n_____________________\nEntregué Conforme\n",'0','C');
		$this->SetXY(15,$y+20);$this->MultiCell(80,5,$items[responsable_antiguo][trabajador]["nomb"]." ".$items[responsable_antiguo][trabajador]["appat"]." ".$items[responsable_antiguo][trabajador]["apmat"],'0','C');
		$this->SetXY(25,$y+25);$this->MultiCell(60,5,$responsable_antiguo,'0','C');
		
		
		$cargo=$items[responsable_antiguo][destino][roles][trabajador][cargo]["nomb"];
		$funcion=$items[responsable_antiguo][destino][roles][trabajador][cargo]["funcion"];
		if($cargo!=""){
			$destino=$cargo;
		}elseif($funcion!=""){
			$destino=$funcion;
		}
		
		$this->SetXY(125,$y);$this->MultiCell(60,5,"\n\n_____________________\nRecibi Conforme\n",'0','C');
		$this->SetXY(115,$y+20);$this->MultiCell(80,5,$items[responsable_antiguo][destino]["nomb"]." ".$items[responsable]["appat"]." ".$items[responsable]["apmat"],'0','C');
		$this->SetXY(125,$y+25);$this->MultiCell(60,5,$destino,'0','C');
		
				
		}
	
	}
$pdf=new repo('P','mm','A4');
$pdf->AliasNbPages();
$pdf->SetMargins(10,10,0); 
$pdf->SetTitle("reporte");
$pdf->SetAutoPageBreak(false,0); 
$pdf->Open();
$pdf->filtros($items);
$pdf->AddPage();
$pdf->Publicar($items);
$pdf->SetLeftMargin(25);
//header('Content-Type: application/pdf; charset=utf-8');
$pdf->Output();
?>