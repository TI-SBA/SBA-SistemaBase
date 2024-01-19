<?php
global $f;
$f->library('pdf');

class repo extends FPDF
{
	var $params;
	function setParams($params){
		$this->params = $params;
	}
	function Header(){
		//$this->Image(IndexPath.DS.'templates/ts/comprobante.gif',10,10,190,275);	
		$meses = array("","Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre");
		$y=10;
		$this->SetFont('Arial','B',9);
		$this->SetXY(10,5);$this->MultiCell(60,5,"SOCIEDAD DE BENEFICENCIA PUBLICA DE AREQUIPA",'0','C');		
		$this->SetXY(10,5);$this->MultiCell(190,5,date("d/m/Y")." Página: ".$this->PageNo()."/{nb}",'0','R');
		$this->SetFont('Arial','',9);
		$this->SetXY(10,15);$this->MultiCell(60,5,"Centro de Salud Mental",'0','C');
		$this->SetXY(10,20);$this->MultiCell(60,5,"'Moises Heresi'",'0','C');
		$this->SetFont('Arial','B',13);
		$this->SetXY(5,25);$this->MultiCell(200,5,	"REPORTE ESTADISTICO MENSUAL POR DOCTOR",'0','C');
		$this->SetFont('Arial','B',10);
		$this->SetXY(10,35);$this->MultiCell(200,5,	"A: 				Medico Jefe del C:S:M:M",'0','L');
		$this->SetXY(10,40);$this->MultiCell(200,5,	"DE: 				Enfermera Consultorio Externo",'0','L');
		$this->SetXY(10,45);$this->MultiCell(200,5,	"ASUNTO: 				 Estadistica del mes de ".$meses[floatval($this->params['mes'])]." del ".$this->params['ano'],'0','L');
	}
	function Publicar($diario, $params){
		$y=55;
		$y_ini = $y;
		$tot_nuevos = 0;
		$tot_contin = 0;
		$tot_reingr = 0;

		$tot_ninos = 0;
		$tot_jovenes = 0;
		$tot_adultos = 0;
		$tot_ancianos = 0;

		$tot_varones = 0;
		$tot_mujeres = 0;

		if($diario!=null){
			$this->SetFont('Arial','',10);
			$this->Line(10,$y,200,$y);
			foreach($diario as $item){
				$tot_nuevos+=$item['nuevos'];
				$tot_contin+=$item['contin'];
				$tot_reingr+=$item['reingr'];

				$tot_ninos+=$item['ninos'];
				$tot_jovenes+=$item['jovenes'];
				$tot_adultos+=$item['adultos'];
				$tot_ancianos+=$item['ancianos'];

				$tot_varones+=$item['varones'];
				$tot_mujeres+=$item['mujeres'];
				$this->SetXY(10,$y);$this->MultiCell(40,5,$item['medico']['appat'],'0','L');
				$this->SetXY(50,$y);$this->MultiCell(25,5,'Nuevos','0','L');
				$this->SetXY(75,$y);$this->MultiCell(25,5,$item['nuevos'],'0','L');
				$y+=5;
				$this->SetXY(50,$y);$this->MultiCell(25,5,'Continuadores','0','L');
				$this->SetXY(75,$y);$this->MultiCell(25,5,$item['contin'],'0','L');
				$y+=5;
				$this->SetXY(50,$y);$this->MultiCell(25,5,'Reingresantes','0','L');
				$this->SetXY(75,$y);$this->MultiCell(25,5,$item['reingr'],'0','L');
				$y+=5;
				$this->SetXY(50,$y);$this->MultiCell(25,5,'TOTAL','0','L');
				$this->SetXY(75,$y);$this->MultiCell(25,5,$item['nuevos']+$item['contin']+$item['reingr'],'0','L');
				$this->Line(10,$y+10,200,$y+10);
				$y-=15;

				$this->SetXY(100,$y);$this->MultiCell(25,5,'Niños','0','L');
				$this->SetXY(125,$y);$this->MultiCell(25,5,$item['ninos'],'0','L');
				$y+=5;
				$this->SetXY(100,$y);$this->MultiCell(25,5,'Jovenes','0','L');
				$this->SetXY(125,$y);$this->MultiCell(25,5,$item['jovenes'],'0','L');
				$y+=5;
				$this->SetXY(100,$y);$this->MultiCell(25,5,'Adultos','0','L');
				$this->SetXY(125,$y);$this->MultiCell(25,5,$item['adultos'],'0','L');
				$y+=5;
				$this->SetXY(100,$y);$this->MultiCell(25,5,'Ancianos','0','L');
				$this->SetXY(125,$y);$this->MultiCell(25,5,$item['ancianos'],'0','L');
				$y+=5;
				$this->SetXY(100,$y);$this->MultiCell(25,5,'TOTAL','0','L');
				$this->SetXY(125,$y);$this->MultiCell(25,5,$item['ninos']+$item['jovenes']+$item['adultos']+$item['ancianos'],'0','L');
				$y-=20;

				$this->SetXY(150,$y);$this->MultiCell(25,5,'Varones','0','L');
				$this->SetXY(175,$y);$this->MultiCell(25,5,$item['varones'],'0','L');
				$y+=5;
				$this->SetXY(150,$y);$this->MultiCell(25,5,'Mujeres','0','L');
				$this->SetXY(175,$y);$this->MultiCell(25,5,$item['mujeres'],'0','L');
				$y+=5;
				$this->SetXY(150,$y);$this->MultiCell(25,5,'TOTAL','0','L');
				$this->SetXY(175,$y);$this->MultiCell(25,5,$item['varones']+$item['mujeres'],'0','L');
				$y+=15;
			}
		}
		$this->Line(10,$y_ini,10,$y);
		$this->Line(50,$y_ini,50,$y);
		$this->Line(100,$y_ini,100,$y);
		$this->Line(150,$y_ini,150,$y);
		$this->Line(200,$y_ini,200,$y);
		$y+=5;
		$this->SetXY(10,$y);$this->MultiCell(190,5,'TOTAL DE ATENCIONES DE TODOS LOS MEDICOS','0','L');
		$y+=5;
		$this->SetXY(10,$y);$this->MultiCell(25,5,'Nuevos','0','L');
		$this->SetXY(35,$y);$this->MultiCell(25,5,$tot_nuevos,'0','L');
		$y+=5;
		$this->SetXY(10,$y);$this->MultiCell(25,5,'Continuadores','0','L');
		$this->SetXY(35,$y);$this->MultiCell(25,5,$tot_contin,'0','L');
		$y+=5;
		$this->SetXY(10,$y);$this->MultiCell(25,5,'Reingresantes','0','L');
		$this->SetXY(35,$y);$this->MultiCell(25,5,$tot_reingr,'0','L');
		$y+=5;
		$this->SetXY(10,$y);$this->MultiCell(25,5,'TOTAL','0','L');
		$this->SetXY(35,$y);$this->MultiCell(25,5,$tot_nuevos+$tot_contin+$tot_reingr,'0','L');
		$y-=15;

		$this->SetXY(60,$y);$this->MultiCell(25,5,'Niños','0','L');
		$this->SetXY(85,$y);$this->MultiCell(25,5,$tot_ninos,'0','L');
		$y+=5;
		$this->SetXY(60,$y);$this->MultiCell(25,5,'Jovenes','0','L');
		$this->SetXY(85,$y);$this->MultiCell(25,5,$tot_jovenes,'0','L');
		$y+=5;
		$this->SetXY(60,$y);$this->MultiCell(25,5,'Adultos','0','L');
		$this->SetXY(85,$y);$this->MultiCell(25,5,$tot_adultos,'0','L');
		$y+=5;
		$this->SetXY(60,$y);$this->MultiCell(25,5,'Ancianos','0','L');
		$this->SetXY(85,$y);$this->MultiCell(25,5,$tot_ancianos,'0','L');
		$y+=5;
		$this->SetXY(60,$y);$this->MultiCell(25,5,'TOTAL','0','L');
		$this->SetXY(85,$y);$this->MultiCell(25,5,$tot_ninos+$tot_jovenes+$tot_adultos+$tot_ancianos,'0','L');
		$y_final = $y;
		$y-=20;

		$this->SetXY(110,$y);$this->MultiCell(25,5,'Varones','0','L');
		$this->SetXY(135,$y);$this->MultiCell(25,5,$tot_varones,'0','L');
		$y+=5;
		$this->SetXY(110,$y);$this->MultiCell(25,5,'Mujeres','0','L');
		$this->SetXY(135,$y);$this->MultiCell(25,5,$tot_mujeres,'0','L');
		$y+=5;
		$this->SetXY(110,$y);$this->MultiCell(25,5,'TOTAL','0','L');
		$this->SetXY(135,$y);$this->MultiCell(25,5,$tot_varones+$tot_mujeres,'0','L');
		$y+=15;
		$y = $y_final;
		$y+=10;
		$this->SetXY(10,$y);$this->MultiCell(50,5,'Inyectables: '.$params['inyectables'],'0','L');
		$y+=10;
		$this->SetFont('Arial','U',10);
		$this->SetXY(10,$y);$this->MultiCell(50,5,'Medicamentos de Deposito','0','L');
		$this->SetXY(60,$y);$this->MultiCell(50,5,'Aplicaciones','0','L');
		$this->SetXY(110,$y);$this->MultiCell(50,5,'Dosis','0','L');
		$y+=5;
		$this->SetXY(10,$y);$this->MultiCell(50,5,'Haldol decanoas x 50mg','0','L');
		$this->SetXY(60,$y);$this->MultiCell(50,5,$params['haldol_aplic'],'0','L');
		$this->SetXY(110,$y);$this->MultiCell(50,5,$params['haldol_dosis'],'0','L');
	}
}

$pdf=new repo('P','mm','A4');
$pdf->setParams($params);
$pdf->AliasNbPages();
$pdf->SetMargins(10,10,0); 
$pdf->SetTitle("reporte");
$pdf->SetAutoPageBreak(false,0); 
$pdf->Open();
$pdf->AddPage();
$pdf->Publicar($diario, $params);
$pdf->SetLeftMargin(25);
//header('Content-Type: application/pdf; charset=utf-8');
$pdf->Output();

?>