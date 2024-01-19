<?php
global $f;
$f->library('pdf');

class repo extends FPDF
{
	
	function Header(){
		//$this->Image(IndexPath.DS.'templates/ts/comprobante.gif',10,10,190,275);	
		$y=10;
		$this->SetFont('Arial','B',9);
		$this->SetXY(10,5);$this->MultiCell(60,5,"SOCIEDAD DE BENEFICENCIA PUBLICA DE AREQUIPA",'0','C');		
		$this->SetXY(10,5);$this->MultiCell(190,5,date("d/m/Y")." Página: ".$this->PageNo()."/{nb}",'0','R');
		$this->SetFont('Arial','',9);
		$this->SetXY(10,15);$this->MultiCell(60,5,"Centro de Salud Mental",'0','C');
		$this->SetXY(10,20);$this->MultiCell(60,5,"'Moises Heresi'",'0','C');
		$this->SetFont('Arial','B',16);
		$this->SetXY(5,25);$this->MultiCell(200,5,"INFORME FRONTAL",'0','C');
		$y=$y+10;
		
	
		$y=$y+5;
		
		$y=$y+5;
	}

	



	function Publicar($paciente){
			$idioma= array(
			"1"=>"S/E",
			"2"=>"Castellano",
			"3"=>"Quechua",
			"4"=>"Ingles",
			"5"=>"AYMARA",
			"6"=>"QUECHUA-CASTELLANO",
			"7"=>"Portugues",
			"8"=>"otros"

		);
			$religion= array(
			"1"=>"S/E",
			"2"=>"Cristiana",
			"3"=>"Catolica",
			"4"=>"Mormona",
			"5"=>"Adventista",
			"6"=>"Testigos Jehova",
			"7"=>"Ateo",
			"8"=>"EVANGELISTA"

		);
			$generos= array(
			"0"=>"Femenino",
			"1"=>"Masculino"
		);
			$civil= array(
			"1"=>"S/E",
			"2"=>"Soltero(a)",
			"3"=>"Casado(a)",
			"4"=>"Viudo(a)",
			"5"=>"Divorciado(a)",
			"6"=>"Conviviente",
			"7"=>"Separado(a)"
		);
			$ocupacion= array(
			"1"=>"S/E",
			"2"=>"DESOCUPADO",
			"3"=>"EMPLEADO",
			"4"=>"OBRERO",
			"5"=>"ESTUDIANTE",
			"6"=>"PROFESIONAL",
			"7"=>"TECNICO",
			"8"=>"SU CASA",
			"9"=>"INDEPENDIENTE"
		);
			$grado = array(
				"1"=>"S/E",
				"2"=>"C/Primaria",
				"3"=>"C/Secundaria",
				"4"=>"C/Tecnica",
				"5"=>"C/Superior",
				"6"=>"C/Universitaria",
				"7"=>"C/Jardin",
				"8"=>"Ed Especial"
		);

		$x=5;
		$y=25;
		$y_ini = $y;
		$page_b = 275;
		$this->SetFont('Arial','B',10);
		$this->SetXY(145,43);$this->MultiCell(60,8,"Historia Clinica: ",'0','L');
		$this->SetFont('Arial','',10);
		$this->SetXY(178,43);$this->MultiCell(60,8,"".$paciente["his_cli"],'0','L');
		$y=$y+5;
		$this->SetFont('Arial','B',10);
		$this->SetXY(145,37);$this->MultiCell(60,8,"Fecha de Registro: ",'0','L');
		$this->SetFont('Arial','',10);
		$this->SetXY(178,37);$this->MultiCell(60,8,"".date('d-m-Y',$paciente["fe_regi"]->sec),'0','L');
		$y=$y+5;
		$this->SetFont('Arial','B',10);
		$this->SetXY(20,$y);$this->MultiCell(200,15,"Apellidos: ",'0','L');
		$this->SetFont('Arial','',10);
		$this->SetXY(40,$y);$this->MultiCell(200,15,"".$paciente['paciente']["appat"] . ' ' . $paciente['paciente']["apmat"],'0','L');
		$y=$y+5;
		$this->SetFont('Arial','B',10);
		$this->SetXY(20,$y);$this->MultiCell(200,15,"Nombre(s): ",'0','L');
		$this->SetFont('Arial','',10);
		$this->SetXY(40,$y);$this->MultiCell(200,15,"".$paciente['paciente']["nomb"],'0','L');
		$y=$y+20;
		$this->SetFont('Arial','B',13);
		$this->SetXY(5,$y);$this->MultiCell(200,5,"DATOS GENERALES DEL PACIENTE",'0','C');
		$y=$y+10;
		$this->SetFont('Arial','B',10);
		$this->SetXY(50,$y);$this->MultiCell(46,9,"Fecha de Nacimiento: ",'0','L');
		$this->SetFont('Arial','',10);
		$this->SetXY(105,$y);$this->MultiCell(25,9,"".date('d-m-Y',$paciente["fecha_na"]->sec),'0','L');
		$y=$y+8;
		$this->SetFont('Arial','B',10);
		$this->SetXY(46,$y);$this->MultiCell(46,9,"Distrito de Procedencia: ",'0','L');
		$this->SetFont('Arial','',10);
		$this->SetXY(105,$y);$this->MultiCell(60,9,"".$paciente['procedencia']["distrito"],'0','L');
		$y=$y+8;
		$this->SetFont('Arial','B',10);
		$this->SetXY(43,$y);$this->MultiCell(50,9,"Provincia de Procedencia: ",'0','L');
		$this->SetFont('Arial','',10);
		$this->SetXY(105,$y);$this->MultiCell(60,9,"".$paciente['procedencia']["provincia"],'0','L');

		$y=$y+8;
		$this->SetFont('Arial','B',10);
		$this->SetXY(35,$y);$this->MultiCell(63,9,"Departamento de Procedencia: ",'0','L');
		$this->SetFont('Arial','',10);
		$this->SetXY(105,$y);$this->MultiCell(200,9,"".$paciente['procedencia']["departamento"],'0','L');
		$y=$y+8;		
		$this->SetFont('Arial','B',10);
		$this->SetXY(45,$y);$this->MultiCell(60,9,"Domicilio de Residencia: ",'0','L');
		$this->SetFont('Arial','',10);
		$this->SetXY(105,$y);$this->MultiCell(110,9,"".$paciente["paciente"]['domicilios'][0]['direccion'],'0','L');
		$y=$y+16;
		$this->SetFont('Arial','B',10);
		$this->SetXY(72,$y);$this->MultiCell(50,9,"Religion: ",'0','L');
		$this->SetFont('Arial','',10);
		$this->SetXY(105,$y);$this->MultiCell(50,9,"".$religion[$paciente["reli"]],'0','L');
		$y=$y+8;		
		$this->SetFont('Arial','B',10);
		$this->SetXY(66,$y);$this->MultiCell(50,9,"Estado Civil: ",'0','L');
		$this->SetFont('Arial','',10);
		$this->SetXY(105,$y);$this->MultiCell(50,9,"".$civil[$paciente["es_civil"]],'0','L');
		$y=$y+8;
		$this->SetFont('Arial','B',10);
		$this->SetXY(67,$y);$this->MultiCell(50,9,"Ocupacion: ",'0','L');
		$this->SetFont('Arial','',10);
		$this->SetXY(105,$y);$this->MultiCell(50,9,"".$ocupacion[$paciente["ocupa"]],'0','L');
		$y=$y+8;		
		$this->SetFont('Arial','B',10);
		$this->SetXY(48,$y);$this->MultiCell(50,9,"Tiempo Desocupacion: ",'0','L');
		$this->SetFont('Arial','',10);
		$this->SetXY(105,$y);$this->MultiCell(70,9,"".$paciente["t_deso"].' meses','0','L');
		$y=$y+8;		
		$this->SetFont('Arial','B',10);
		$this->SetXY(33	,$y);$this->MultiCell(60,9,"Tiempo Residencia en Arequipa: ",'0','L');
		$this->SetFont('Arial','',10);
		$this->SetXY(105,$y);$this->MultiCell(50,9,"".$paciente["m_resi"].' meses','0','L');
		$y=$y+8;
		$this->SetFont('Arial','B',10);
		$this->SetXY(52,$y);$this->MultiCell(50,9,"Grado de Instruccion:  ",'0','L');
		$this->SetFont('Arial','',10);
		$this->SetXY(105,$y);$this->MultiCell(50,9,"".$grado[$paciente["instr"]],'0','L');
		$y=$y+8;		
		$this->SetFont('Arial','B',10);
		$this->SetXY(67,$y);$this->MultiCell(50,9,"Documento: ",'0','L');
		$this->SetFont('Arial','',10);
		$this->SetXY(105,$y);$this->MultiCell(50,9,"".$paciente["paciente"]["docident"][0]['num'],'0','L');
		$y=$y+8;
		$this->SetFont('Arial','B',10);
		$this->SetXY(58,$y);$this->MultiCell(50,9,"Idioma o Dialecto: ",'0','L');
		$this->SetFont('Arial','',10);
		$this->SetXY(105,$y);$this->MultiCell(50,9,"".$idioma[$paciente["idio"]],'0','L');
		$y=$y+8;
		$this->SetFont('Arial','B',10);
		$this->SetXY(66,$y);$this->MultiCell(50,9,"Referido Por: ",'0','L');
		$this->SetFont('Arial','',10);
		$this->SetXY(105,$y);$this->MultiCell(50,9,"".$paciente["refe"],'0','L');
		$y=$y+8;
		$this->SetFont('Arial','B',10);
		$this->SetXY(66,$y);$this->MultiCell(50,9,"Apoderado(a): ",'0','L');
		$this->SetFont('Arial','',10);
		$this->SetXY(105,$y);$this->MultiCell(150,9,"".$paciente["apoderado"]["appat"].' '.$paciente["apoderado"]["apmat"].' '.$paciente["apoderado"]["nomb"],'0','L');
		
		$y=$y+15;
		$this->SetXY(5,$y);$this->MultiCell(200,9,"Sintomas: ".$paciente["m_consu"],'1','L');
	
	}
}
$pdf=new repo('P','mm','A4');
$pdf->AliasNbPages();
$pdf->SetMargins(10,10,0); 
$pdf->SetTitle("reporte");
$pdf->SetAutoPageBreak(false,0); 
$pdf->Open();
$pdf->AddPage();
$pdf->Publicar($paciente);
$pdf->SetLeftMargin(25);
//header('Content-Type: application/pdf; charset=utf-8');
$pdf->Output();
?>