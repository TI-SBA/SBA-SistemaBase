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
		$this->SetXY(60,25);$this->MultiCell(100,5,"INFORME DE HOSPITALIZACION",'0','C');
		
	}		
	function Publicar($hospitalizacion){
		$categorias= array(
			"1"=>"S/E",
			"2"=>"PP",
			"3"=>"P",
			"4"=>"A",		
			"5"=>"B",
			"6"=>"C",
			"7"=>"E",
			"8"=>"D"

		);
			$estados= array(
			"1"=>"S/E",
			"2"=>"Nuevo",
			"3"=>"Continuador",
			"4"=>"Reingresante"

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
			$civil= array(
			"1"=>"S/E",
			"2"=>"Soltero(a)",
			"3"=>"Casado(a)",
			"4"=>"Viudo(a)",
			"5"=>"Divorciado(a)",
			"6"=>"Conviviente",
			"7"=>"Separado(a)"
		);
			$ocupacion = array(
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
			$instruccion = array(
				"1"=>"S/E",
				"2"=>"C/Primaria",
				"3"=>"C/Secundaria",
				"4"=>"C/Tecnica",
				"5"=>"C/Superior",
				"6"=>"C/Universitaria",
				"7"=>"C/Jardin",
				"8"=>"Ed.Especial"
		);
			$idioma = array(
				"1"=>"S/E",
				"2"=>"Castellano",
				"3"=>"Quechua",
				"4"=>"Ingles",
				"5"=>"AYMARA",
				"6"=>"QUECHUA-CASTELLANO",
				"7"=>"Portugues",
				"8"=>"otros"
		);
			$tipoh = array(
				"C"=>"Completa",
				"P"=>"Parcial"
		);


		
		$x=5;
		$y=25;
		$y_ini = $y;
		$page_b = 275;
		$this->SetFont('Arial','B',11);
		$this->SetXY(120,41);$this->MultiCell(60,8,"Historia Clinica: ",'0','L');
		$this->SetFont('Arial','',11);
		$this->SetXY(150,41);$this->MultiCell(60,8,"".$hospitalizacion["hist_cli"],'0','L');
		$y=$y+5;
		$this->SetFont('Arial','B',11);
		$this->SetXY(120,35);$this->MultiCell(60,8,"Fecha de Registro: ",'0','L');
		$this->SetFont('Arial','',11);
		$this->SetXY(155,35);$this->MultiCell(60,8,"".date('d-m-Y',$hospitalizacion["fecreg"]->sec),'0','L');
		$y=$y+5;
		$this->SetFont('Arial','B',11);
		$this->SetXY(120,47);$this->MultiCell(60,8,"Numero de Ingreso: ",'0','L');
		$this->SetFont('Arial','',11);
		$this->SetXY(160,47);$this->MultiCell(60,8,"".$hospitalizacion["ning"],'0','L');
		$y=$y+5;
		$this->SetFont('Arial','B',11);
		$this->SetXY(20,$y);$this->MultiCell(50,8,"Apellidos: ",'0','L');
		$this->SetFont('Arial','',11);
		$this->SetXY(40,$y);$this->MultiCell(50,8,"".$hospitalizacion['paciente']["appat"]. ' '.$hospitalizacion['paciente']['apmat'],'0','L');
		$y=$y+5;
		$this->SetFont('Arial','B',11);
		$this->SetXY(20,$y);$this->MultiCell(50,8,"Nombres: ",'0','L');
		$this->SetFont('Arial','',11);
		$this->SetXY(40,$y);$this->MultiCell(50,8,"".$hospitalizacion['paciente']["nomb"],'0','L');
		$y=$y+20;
		$this->SetFont('Arial','B',13);
		/*-------------------------------------------------------------------------------------------*/
		$this->SetXY(60,$y);$this->MultiCell(100,5,"DATOS GENERALES DEL PACIENTE",'0','C');
		$y=$y+10;
		$this->SetFont('Arial','',11);
		$this->SetXY(55,$y);$this->MultiCell(42,8,"Fecha de Nacimiento: ",'0','L');
		$this->SetXY(105,$y);$this->MultiCell(25,8,"".$hospitalizacion["fena"],'0','L');
		$y=$y+8;
		$this->SetXY(55,$y);$this->MultiCell(42,8,"Domicilio:  ",'0','R');
		$this->SetXY(105,$y);$this->MultiCell(150,8,"".$hospitalizacion["domi"],'0','L');
		$y=$y+8;
		$this->SetXY(55,$y);$this->MultiCell(42,8,"Religion:  ",'0','R');
		$this->SetXY(105,$y);$this->MultiCell(50,8,"".$religion[$hospitalizacion["reli"]],'0','L');
		$y=$y+8;		
		$this->SetXY(55,$y);$this->MultiCell(42,8,"Estado Civil:  ",'0','R');
		$this->SetXY(105,$y);$this->MultiCell(50,8,"".$civil[$hospitalizacion["es_civil"]],'0','L');
		$y=$y+8;
		$this->SetXY(55,$y);$this->MultiCell(42,8,"Ocupacion:  ",'0','R');
		$this->SetXY(105,$y);$this->MultiCell(50,8,"".$ocupacion[$hospitalizacion["ocup"]],'0','L');
		//$y=$y+8;
		//$this->SetXY(55,$y);$this->MultiCell(100,8,"Grado de Instruccion:  ".$instruccion[$hospitalizacion["instr"]],'0','L');
		$y=$y+8;		
		$this->SetXY(55,$y);$this->MultiCell(42,8,"Documento:  ",'0','R');
		$this->SetXY(105,$y);$this->MultiCell(50,8,"".$hospitalizacion['paciente']['docident'][0]["num"],'0','L');
		$y=$y+8;
		$this->SetXY(55,$y);$this->MultiCell(42,8,"Idioma o Dialecto:  ",'0','R');
		$this->SetXY(105,$y);$this->MultiCell(50,8,"".$idioma[$hospitalizacion["idio"]],'0','L');
		$y=$y+8;
		$this->SetXY(55,$y);$this->MultiCell(42,8,"Referido Por:  ",'0','R');
		$this->SetXY(105,$y);$this->MultiCell(100,8,"".$hospitalizacion["refe"],'0','L');
		$y=$y+8;
		$this->SetXY(55,$y);$this->MultiCell(42,8,"Apoderado(a):  ",'0','R');
		$this->SetXY(105,$y);$this->MultiCell(100,8,"".$hospitalizacion['apoderado']["appat"].' '.$hospitalizacion['apoderado']["apmat"].','.$hospitalizacion['apoderado']["nomb"] ,'0','L');
		$y=$y+8;
		$this->SetXY(55,$y);$this->MultiCell(42,8,"Direccion Apoderado:  ",'0','R');
		$this->SetXY(105,$y);$this->MultiCell(50,8,"".$hospitalizacion['apoderado']['domicilios'][0]["direccion"],'0','L');
		$y=$y+15;
		$this->SetXY(10,$y);$this->MultiCell(45,8,"Sintomas: ",'0','L');
		$this->SetXY(50,$y);$this->MultiCell(150,8,"".$hospitalizacion["moti"],'0','L');
		$y=$y+20;
		$this->SetXY(10,$y);$this->MultiCell(45,8,"Fecha de Ingreso: ",'0','L');
		//$this->SetXY(50,$y);$this->MultiCell(30,8,"".$hospitalizacion["fecini"],'0','L');
		$this->SetXY(50,$y);$this->MultiCell(30,8,"".date('d-m-Y',$hospitalizacion["fecini"]->sec),'0','L');
		$y=$y+12;
		$this->SetXY(10,$y);$this->MultiCell(45,8,"Fecha de Egreso: ",'0','L');
		$this->SetXY(50,$y);$this->MultiCell(30,8,"".$hospitalizacion["fegr"],'0','L');
		$y=$y+8;
		$this->SetXY(10,$y);$this->MultiCell(45,8,"Pabellon: ",'0','L');
		$this->SetXY(50,$y);$this->MultiCell(30,8,"".$hospitalizacion["pabe"],'0','L');
		$y=$y+8;
		$this->SetXY(10,$y);$this->MultiCell(45,8,"Tipo Hospitalizacion: ",'0','L');
		$this->SetXY(50,$y);$this->MultiCell(30,8,"".$tipoh[$hospitalizacion["tipo_hosp"]],'0','L');
		$y=$y+8;



}
	
	 
}

$pdf=new repo('P','mm','A4');
$pdf->AliasNbPages();
$pdf->SetMargins(10,10,0); 
$pdf->SetTitle("reporte");
$pdf->SetAutoPageBreak(false,0); 
$pdf->Open();
$pdf->AddPage();
$pdf->Publicar($hospitalizacion);
$pdf->SetLeftMargin(25);
//header('Content-Type: application/pdf; charset=utf-8');
$pdf->Output();

?>