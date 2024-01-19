<?php
global $f;
$f->library('pdf');

class repo extends FPDF
{
	var $his_Cli;
	var $fe_regi;
	
	function Header(){
		//$this->Image(IndexPath.DS.'templates/ts/comprobante.gif',5,5,190,275);	
		$y=5;
		$this->SetFont('Arial','B',9);
		$this->SetXY(5,5);$this->MultiCell(60,5,"SOCIEDAD DE BENEFICENCIA PUBLICA DE AREQUIPA",'0','C');		
		$this->SetXY(5,5);$this->MultiCell(190,5,date("d/m/Y")." Página: ".$this->PageNo()."/{nb}",'0','R');
		$this->SetFont('Arial','',9);
		$this->SetXY(5,15);$this->MultiCell(60,5,"Centro de Salud Mental",'0','C');
		$this->SetXY(5,20);$this->MultiCell(60,5,"'Moises Heresi'",'0','C');
		$this->SetFont('Arial','B',16);
		/*
		$y=$y+5;	
		$y=$y+5;	
		*/
	}	

	function Publicar($charla){
		$mes= array(
			"01"=>"ENERO",
            "02"=>"FEBRERO",
            "03"=>"MARZO",
            "04"=>"ABRIL",
            "05"=>"MAYO",
            "06"=>"JUNIO",
            "07"=>"JULIO",
            "08"=>"AGOSTO",
            "09"=>"SETIEMBRE",
            "10"=>"OCTUBRE",
            "11"=>"NOVIEMBRE",
            "12"=>"DICIEMBRE"

		);


		$x=5;
		$y=25;
		$y_ini = $y;
		$page_b = 275;

		//$y=$y+5;	
		$this->SetFont('Arial','B',11);
		//$this->SetXY(0,$y);$this->MultiCell(200,5,"".$charla["tit"].' '.$charla["mes"].'-'.$charla["ano"],'0','C');
		$this->SetXY(5,$y);$this->MultiCell(200,5,	"INFORME DE CHARLAS Y ATENCION ",'0','C');
		$y=$y+5;	
		$this->SetXY(5,$y);$this->MultiCell(200,5,	"DE PACIENTES EN CONSULTORIO EXTERNO ",'0','C');
		$y=$y+5;	
		$this->SetXY(5,$y);$this->MultiCell(200,5,	"".$mes[$charla['mes']].' - '.$charla['año'],'0','C');
		
//----------------------------------------------------------------------
		$servicio = 0;
		$servicio = $charla["seso"];
		$psicología = 0;
		$psicología = $charla["psic"];
		$enfermería = 0;
		$enfermería = $charla["enfe"];
		$psiquiatría = 0;
		$psiquiatría = $charla["psiq"];
		$tocha = 0;
		$tocha = $servicio + $psicología + $enfermería + $psiquiatría;
//----------------------------------------------------------------------
//----------------------------------------------------------------------
		$vchi = 0;
		$vchi = $charla["vchi"];
		$mchi = 0;
		$mchi = $charla["mchi"];
		$tac = 0;
		$tac = $vchi + $mchi;
//----------------------------------------------------------------------
//----------------------------------------------------------------------
		$vjes = 0;
		$vjes = $charla["vjes"];
		$mjes = 0;
		$mjes = $charla["mjes"];	
		$tje = 0;
		$tje = $vjes + $mjes;
//----------------------------------------------------------------------
//----------------------------------------------------------------------
		$vcam = 0;
		$vcam = $charla["vcam"];
		$mcam = 0;
		$mcam = $charla["mcam"];	
		$ttc = 0;
		$ttc = $vcam + $mcam;
//----------------------------------------------------------------------
//----------------------------------------------------------------------
		$valf = 0;
		$valf = $charla["valf"];
		$malf = 0;
		$malf = $charla["malf"];	
		$tau = 0;
		$tau = $valf + $malf;
//---------------------------------------------------------------------
		$vgon = 0;
		$vgon = $charla["vgon"];
		$mgon = 0;
		$mgon = $charla["mgon"];	
		$tgon = 0;
		$tgon = $vgon + $mgon;
//---------------------------------------------------------------------
		$vjos = 0;
		$vjos = $charla["vjos"];
		$mjos = 0;
		$mjos = $charla["mjos"];	
		$tjos = 0;
		$tjos = $vjos + $mjos;
//---------------------------------------------------------------------
		$vmar = 0;
		$vmar = $charla["vmar"];
		$mmar = 0;
		$mmar = $charla["mmar"];	
		$tmar = 0;
		$tmar = $vmar + $mmar;
//---------------------------------------------------------------------
		$vcar = 0;
		$vcar = $charla["vcar"];
		$mcar = 0;
		$mcar = $charla["mcar"];	
		$tcar = 0;
		$tcar = $vcar + $mcar;
//---------------------------------------------------------------------
		$vasi = 0;
		$vasi = $charla["vasi"];
		$masi = 0;
		$masi = $charla["masi"];	
		$tasi = 0;
		$tasi = $vasi + $masi;
//---------------------------------------------------------------------
		$vfam = 0;
		$vfam = $charla["vfam"];
		$mfam = 0;
		$mfam = $charla["mfam"];	
		$tfam = 0;
		$tfam = $vfam + $mfam;
//---------------------------------------------------------------------
		$vfis = 0;
		$vfis = $charla["vfis"];
		$mfis = 0;
		$mfis = $charla["mfis"];	
		$tfis = 0;
		$tfis = $vfis + $mfis;
//---------------------------------------------------------------------
		$vmoq = 0;
		$vmoq = $charla["vmoq"];
		$mmoq = 0;
		$mmoq = $charla["mmoq"];	
		$tcam = 0;
		$tcam = $vmoq + $mmoq;
//---------------------------------------------------------------------
		$vpau = 0;
		$vpau = $charla["vpau"];
		$mpau = 0;
		$mpau = $charla["mpau"];	
		$tpau = 0;
		$tpau = $vpau + $mpau;
//---------------------------------------------------------------------
		$vros = 0;
		$vros = $charla["vros"];
		$mros = 0;
		$mros = $charla["mros"];	
		$trosa = 0;
		$trosa = $vros + $mros;
//---------------------------------------------------------------------

		$vsoa = 0;
		$vsoa = $charla["vsoa"];
		$msoa = 0;
		$msoa = $charla["msoa"];	
		$ttsoa = 0;
		$ttsoa = $vsoa + $msoa;
//---------------------------------------------------------------------
		$vcef = 0;
		$vcef = $charla["vcef"];
		$mcef = 0;
		$mcef = $charla["mcef"];	
		$ttcef = 0;
		$ttcef = $vcef + $mcef;


		$y=$y+5;	
		$this->SetFont('Arial','B',9);
		$this->SetXY(20,$y);$this->MultiCell(150,5,"CHARLAS INTRAMURALES: ",'0','L');
		$this->SetFont('Arial','',8);
		$y=$y+5;	
		$this->SetXY(30,$y);$this->MultiCell(150,5,"Servicio Social: ".$charla["seso"],'0','L');
		$y=$y+5;	
		$this->SetXY(30,$y);$this->MultiCell(150,5,"Psicología: ".$charla["psic"],'0','L');
		$y=$y+5;	
		$this->SetFont('Arial','',8);
		$this->SetXY(30,$y);$this->MultiCell(150,5,"Enfermería: ".$charla["enfe"],'0','L');
		$y=$y+5;	
		$this->SetFont('Arial','',8);
		$this->SetXY(30,$y);$this->MultiCell(150,5,"Psiquiatría: ".$charla["psiq"],'0','L');
		$y=$y+5;	
		$this->SetFont('Arial','B',9);
		$this->SetXY(30,$y);$this->MultiCell(150,5,"Total Charlas: ".$tocha,'0','R');
		$this->SetFont('Arial','',8);
//------------------------------------------------------------------------
		$y=$y+10;
		$this->SetFont('Arial','B',11);	
		$this->SetXY(5,$y);$this->MultiCell(200,5,"ATENCIÓN DE PACIENTES INDIGENTES EN EL CENTRO DE SALUD MENTAL M.H. A DIFERENTES INSTITUCIONES Y A PACIENTES  INDEPENDIENTES ",'0','C');
		$y=$y+15;
		//ALFONSO UGARTE
		$this->SetFont('Arial','B',9);
		$this->SetXY(20,$y);$this->MultiCell(150,5,"CENTRO DE REHABILITACION ALFONSO UGARTE:",'0','L');
		$this->SetFont('Arial','',8);
		$y=$y+5;	
		$this->SetXY(30,$y);$this->MultiCell(150,5,"Atención Varones: ".$charla["valf"],'0','L');
		$y=$y+5;	
		$this->SetXY(30,$y);$this->MultiCell(150,5,"Atención Mujeres: ".$charla["malf"],'0','L');
		$y=$y+5;	
		$this->SetFont('Arial','B',9);
		$this->SetXY(30,$y);$this->MultiCell(150,5,"Total: ".$tau,'0','R');
		//GONZAGA
		$this->SetFont('Arial','B',9);
		$this->SetXY(20,$y);$this->MultiCell(150,5,"ALBERGUE SAN LUIS GONZAGA:",'0','L');
		$this->SetFont('Arial','',8);
		$y=$y+5;	
		$this->SetXY(30,$y);$this->MultiCell(150,5,"Atención Varones: ".$charla["vgon"],'0','L');
		$y=$y+5;	
		$this->SetXY(30,$y);$this->MultiCell(150,5,"Atención Mujeres: ".$charla["mgon"],'0','L');
		$y=$y+5;	
		$this->SetFont('Arial','B',9);
		$this->SetXY(30,$y);$this->MultiCell(150,5,"Total: ".$tgon,'0','R');
		
		$y=$y+5;	
		//SAN JOSE
		$this->SetFont('Arial','B',9);
		$this->SetXY(20,$y);$this->MultiCell(150,5,"ALBERGUE SAN JOSE:",'0','L');
		$this->SetFont('Arial','',8);
		$y=$y+5;	
		$this->SetXY(30,$y);$this->MultiCell(150,5,"Atención Varones: ".$charla["vjos"],'0','L');
		$y=$y+5;	
		$this->SetXY(30,$y);$this->MultiCell(150,5,"Atención Mujeres: ".$charla["mjos"],'0','L');
		$y=$y+5;	
		$this->SetFont('Arial','B',9);
		$this->SetXY(30,$y);$this->MultiCell(150,5,"Total: ".$tjos,'0','R');
		
		$y=$y+5;	
		//HOGAR DE MARIA
		$this->SetFont('Arial','B',9);
		$this->SetXY(20,$y);$this->MultiCell(150,5,"HOGAR DE MARIA:",'0','L');
		$this->SetFont('Arial','',8);
		$y=$y+5;	
		$this->SetXY(30,$y);$this->MultiCell(150,5,"Atención Varones: ".$charla["vmar"],'0','L');
		$y=$y+5;	
		$this->SetXY(30,$y);$this->MultiCell(150,5,"Atención Mujeres: ".$charla["mmar"],'0','L');
		$y=$y+5;	
		$this->SetFont('Arial','B',9);
		$this->SetXY(30,$y);$this->MultiCell(150,5,"Total: ".$tmar,'0','R');
		
		$y=$y+5;	
		//C.A.R.
		$this->SetFont('Arial','B',9);
		$this->SetXY(20,$y);$this->MultiCell(150,5,"C.A.R. BUEN JESUS:",'0','L');
		$this->SetFont('Arial','',8);
		$y=$y+5;
		$this->SetXY(30,$y);$this->MultiCell(150,5,"Atención Varones: ".$charla["vcar"],'0','L');
		$y=$y+5;
		$this->SetXY(30,$y);$this->MultiCell(150,5,"Atención Mujeres: ".$charla["mcar"],'0','L');
		$y=$y+5;
		$this->SetFont('Arial','B',9);
		$this->SetXY(30,$y);$this->MultiCell(150,5,"Total: ".$tcar,'0','R');
		
		$y=$y+5;
		//ASIS
		$this->SetFont('Arial','B',9);
		$this->SetXY(20,$y);$this->MultiCell(150,5,"SAN FRANCISCO DE ASIS:",'0','L');
		$this->SetFont('Arial','',8);
		$y=$y+5;
		$this->SetXY(30,$y);$this->MultiCell(150,5,"Atención Varones: ".$charla["vasi"],'0','L');
		$y=$y+5;
		$this->SetXY(30,$y);$this->MultiCell(150,5,"Atención Mujeres: ".$charla["masi"],'0','L');
		$y=$y+5;
		$this->SetFont('Arial','B',9);
		$this->SetXY(30,$y);$this->MultiCell(150,5,"Total: ".$tasi,'0','R');
		
		$y=$y+5;	
		//SAGRADA FAMILIA
		$this->SetFont('Arial','B',9);
		$this->SetXY(20,$y);$this->MultiCell(150,5,"SAGRADA FAMILIA:",'0','L');
		$this->SetFont('Arial','',8);
		$y=$y+5;
		$this->SetXY(30,$y);$this->MultiCell(150,5,"Atención Varones: ".$charla["vfam"],'0','L');
		$y=$y+5;
		$this->SetXY(30,$y);$this->MultiCell(150,5,"Atención Mujeres: ".$charla["mfam"],'0','L');
		$y=$y+5;
		$this->SetFont('Arial','B',9);
		$this->SetXY(30,$y);$this->MultiCell(150,5,"Total: ".$tfam,'0','R');
		
		$y=$y+5;
		//FISCALIA
		$this->SetFont('Arial','B',9);
		$this->SetXY(20,$y);$this->MultiCell(150,5,"FISCALIA:",'0','L');
		$this->SetFont('Arial','',8);
		$y=$y+5;
		$this->SetXY(30,$y);$this->MultiCell(150,5,"Atención Varones: ".$charla["vfis"],'0','L');
		$y=$y+5;
		$this->SetXY(30,$y);$this->MultiCell(150,5,"Atención Mujeres: ".$charla["mfis"],'0','L');
		$y=$y+5;
		$this->SetFont('Arial','B',9);
		$this->SetXY(30,$y);$this->MultiCell(150,5,"Total: ".$tfis,'0','R');
		$y=$y+5;
		//MOQUEGUA
		$this->SetFont('Arial','B',9);
		$this->SetXY(20,$y);$this->MultiCell(150,5,"MOQUEGUA SBPA:",'0','L');
		$this->SetFont('Arial','',8);
		$y=$y+5;
		$this->SetXY(30,$y);$this->MultiCell(150,5,"Atención Varones: ".$charla["vmoq"],'0','L');
		$y=$y+5;
		$this->SetXY(30,$y);$this->MultiCell(150,5,"Atención Mujeres: ".$charla["mmoq"],'0','L');
		$y=$y+5;
		$this->SetFont('Arial','B',9);
		$this->SetXY(30,$y);$this->MultiCell(150,5,"Total: ".$tcam,'0','R');
		$y=$y+5;	
				
		
		//
		
		//
		
		//
		
		$this->AddPage();

		$y_ini = 10;
		$y = $y_ini;
		$y= $y + 25;
		
						
		//CHILPINILLA
		$y=$y+5;	
		$this->SetFont('Arial','B',9);
		$this->SetXY(20,$y);$this->MultiCell(150,5,"ASILO SAN VICENTE DE PAUL",'0','L');
		$this->SetFont('Arial','',8);
		$y=$y+5;	
		$this->SetXY(30,$y);$this->MultiCell(150,5,"Atención Varones: ".$charla["vpau"],'0','L');
		$y=$y+5;	
		$this->SetXY(30,$y);$this->MultiCell(150,5,"Atención Mujeres: ".$charla["mpau"],'0','L');
		$y=$y+5;	
		$this->SetFont('Arial','B',9);
		$this->SetXY(30,$y);$this->MultiCell(150,5,"Total: ".$tpau,'0','R');
//------------------------------------------------------------------------
		$y=$y+5;	

		$this->SetFont('Arial','B',9);
		$this->SetXY(20,$y);$this->MultiCell(150,5,"ALBERGUE CHAVEZ DE LA ROSA",'0','L');
		$this->SetFont('Arial','',8);
		$y=$y+5;	
		$this->SetXY(30,$y);$this->MultiCell(150,5,"Atención Varones: ".$charla["vros"],'0','L');
		$y=$y+5;	
		$this->SetXY(30,$y);$this->MultiCell(150,5,"Atención Mujeres: ".$charla["mros"],'0','L');
		$y=$y+5;	
		$this->SetFont('Arial','B',9);
		$this->SetXY(30,$y);$this->MultiCell(150,5,"Total: ".$trosa,'0','R');
		$this->SetFont('Arial','',8);
		$y=$y+5;	
//------------------------------------------------------------------------
		$this->SetFont('Arial','B',9);
		$this->SetXY(20,$y);$this->MultiCell(150,5,"PACIENTES INDEPENDIENTES",'0','L');
		$y=$y+5;	
		$this->SetFont('Arial','B',9);
		//$y=$y+5;	
		$this->SetXY(30,$y);$this->MultiCell(150,5,"Total: ".$charla["toin"],'0','R');
//------------------------------------------------------------------------
		$y=$y+10;
		$this->SetFont('Arial','B',11);	
		$this->SetXY(5,$y);$this->MultiCell(200,5,"ATENCIÓN DE PACIENTES INDIGENTES FUERA DEL CENTRO DE SALUD MENTAL MOISES HERESI  ",'0','C');
		$y=$y+15;
		$this->SetFont('Arial','B',9);
		$this->SetXY(20,$y);$this->MultiCell(150,5,"ATENCION CHILPINILLA",'0','L');
		$this->SetFont('Arial','',8);
		$y=$y+5;	
		$this->SetXY(30,$y);$this->MultiCell(150,5,"Atención Varones: ".$charla["vchi"],'0','L');
		$y=$y+5;	
		$this->SetXY(30,$y);$this->MultiCell(150,5,"Atención Mujeres: ".$charla["mchi"],'0','L');
		$y=$y+5;	
		$this->SetFont('Arial','B',9);
		$this->SetXY(30,$y);$this->MultiCell(150,5,"Total: ".$tac,'0','R');
		$this->SetFont('Arial','B',9);
		$y=$y+5;	
		$this->SetXY(20,$y);$this->MultiCell(150,5,"ATENCION CAMANA",'0','L');
		$this->SetFont('Arial','',8);
		$y=$y+5;	
		$this->SetXY(30,$y);$this->MultiCell(150,5,"Atención Varones: ".$charla["vcam"],'0','L');
		$y=$y+5;	
		$this->SetXY(30,$y);$this->MultiCell(150,5,"Atención Mujeres: ".$charla["mcam"],'0','L');
		$y=$y+5;	
		$this->SetFont('Arial','B',9);
		$this->SetXY(30,$y);$this->MultiCell(150,5,"Total: ".$ttc,'0','R');
		$y=$y+10;
		$this->SetFont('Arial','B',9);	

		//------------------------------------------------------------------------------------------


		$this->SetXY(20,$y);$this->MultiCell(150,5,"ATENCION ALBERGUE SOA",'0','L');
		$this->SetFont('Arial','',8);
		$y=$y+5;	
		$this->SetXY(30,$y);$this->MultiCell(150,5,"Atención Varones: ".$charla["vsoa"],'0','L');
		$y=$y+5;	
		$this->SetXY(30,$y);$this->MultiCell(150,5,"Atención Mujeres: ".$charla["msoa"],'0','L');
		$y=$y+5;	
		$this->SetFont('Arial','B',9);
		$this->SetXY(30,$y);$this->MultiCell(150,5,"Total: ".$ttsoa,'0','R');
		$y=$y+10;
		$this->SetFont('Arial','B',9);	
		//-------------------------------------------------------------------------------------------


		$this->SetXY(20,$y);$this->MultiCell(150,5,"ATENCION CEFAC",'0','L');
		$this->SetFont('Arial','',8);
		$y=$y+5;	
		$this->SetXY(30,$y);$this->MultiCell(150,5,"Atención Varones: ".$charla["vcef"],'0','L');
		$y=$y+5;	
		$this->SetXY(30,$y);$this->MultiCell(150,5,"Atención Mujeres: ".$charla["mcef"],'0','L');
		$y=$y+5;	
		$this->SetFont('Arial','B',9);
		$this->SetXY(30,$y);$this->MultiCell(150,5,"Total: ".$ttcef,'0','R');
		$y=$y+10;
		$this->SetFont('Arial','B',9);	
		//----------------------------------------------------------------------------------------
		$this->SetXY(11,$y);$this->MultiCell(150,5,"RESUMEN DE ATENCION  ",'0','L');
		$y=$y+10;
		$this->SetFont('Arial','',9);
		$this->SetXY(30,$y);$this->MultiCell(150,5,"Total Pacientes Pagantes: ".$charla["tppd"],'0','L');
		$y=$y+5;	
		$this->SetXY(30,$y);$this->MultiCell(150,5,"Total Pacientes Indigentes: ".$charla["tpid"],'0','L');
		$y=$y+5;
		
		$this->SetFont('Arial','B',9);	
		$this->SetXY(30,$y);$this->MultiCell(150,5,"Total Pacientes: ".$charla["tpif"],'0','L');
		$y=$y+5;
		$y=$y+10;
		$this->SetFont('Arial','B',9);	
		$this->SetXY(11,$y);$this->MultiCell(150,5,"OBSERVACIONES",'0','L');
		$y=$y+10;
		$this->SetFont('Arial','',9);	
		$this->SetXY(30,$y);$this->MultiCell(150,5,"".$charla["obse"],'0','L');
		$y=$y+15;
		$this->SetFont('Arial','B',11);	
		$this->SetXY(30,$y);$this->MultiCell(150,5,"----------------------------------------",'0','C');
		$y=$y+5;
		$this->SetXY(30,$y);$this->MultiCell(150,5,"FIRMA",'0','C');


		
		$this->AddPage();

		$y_ini = 10;
		$y = $y_ini;
		$y= $y + 25;
		
						
		//CAMANA
		//MUJERES POR RANGO DE EDAD
		$mran1 = 0;
		$mran1 = $charla["mran1"];
		$mran2 = 0;
		$mran2 = $charla["mran2"];
		$mran3 = 0;
		$mran3 = $charla["mran3"];
		$mran4 = 0;
		$mran4 = $charla["mran4"];
		$mran5 = 0;
		$mran5 = $charla["mran5"];
		$mran6 = 0;
		$mran6 = $charla["mran6"];
		$mran7 = 0;
		$mran7 = $charla["mran7"];
		$mran8 = 0;
		$mran8 = $charla["mran8"];
		
		//HOMBRES POR RANGO DE EDAD
		$vran1 = 0;
		$vran1 = $charla["vran1"];
		$vran2 = 0;
		$vran2 = $charla["vran2"];
		$vran3 = 0;
		$vran3 = $charla["vran3"];
		$vran4 = 0;
		$vran4 = $charla["vran4"];
		$vran5 = 0;
		$vran5 = $charla["vran5"];
		$vran6 = 0;
		$vran6 = $charla["vran6"];
		$vran7 = 0;
		$vran7 = $charla["vran7"];
		$vran8 = 0;
		$vran8 = $charla["vran8"];
		// SUMA DE RANGOS DE SEXO
		$trva = 0;
		$trva = $vran1 + $vran2 + $vran3 + $vran4 + $vran5 + $vran6 + $vran7 + $vran8;
		$trmu = 0;
		$trmu = $mran1 + $mran2 + $mran3 + $mran4 + $mran5 + $mran6 + $mran7 + $mran8;

			$this->SetFont('Arial','B',9);	
			$this->SetXY(11,30);$this->MultiCell(150,5,"CUADRO DETALLADO ALBERGUE CAMANA",'0','L');
			
			$this->SetFont('Arial','B',8);
			$this->SetXY(1,$y);$this->MultiCell(35,9,"Descripcion del Servicio",'1','C');
			$this->SetXY(36,44);$this->MultiCell(36,45,"",'1','C'); 
			$this->SetXY(36,$y);$this->MultiCell(36,9,"Numero de Beneficiarios",'1','C');
			$this->SetXY(72,44);$this->MultiCell(13,45,"",'1','C');
			$this->SetXY(72,$y);$this->MultiCell(28,9,"Categoria Social ",'1','C');		
			$this->SetXY(100,44);$this->MultiCell(8,45,"",'1','C');
			$this->SetXY(100,$y);$this->MultiCell(16,9,"Sexo",'1','C');	
			$this->SetXY(116,$y);$this->MultiCell(92,9,"Edades",'1','C');
			$this->SetFont('Arial','B',9);
			$this->SetXY(1,44);$this->MultiCell(5,5,"INDICADOR",'1','C');
			$this->SetFont('Arial','B',8);
			$this->SetXY(1,89);$this->MultiCell(35,9,"Consulta Camana",'1','C');
			$this->SetXY(72,44);$this->MultiCell(13,9,"Pagante",'1','C');		
			$this->SetXY(85,44);$this->MultiCell(15,9,"Indigente",'1','C');		
			$this->SetXY(100,44);$this->MultiCell(8,9,"M",'1','C');		
			$this->SetXY(108,44);$this->MultiCell(8,9,"F",'1','C');
			$this->SetXY(116,53);$this->MultiCell(92,45,"",'1','C');
			$this->SetFont('Arial','B',8);		
			$this->SetXY(116,44);$this->MultiCell(11,9,"0-5",'1','C');
			$this->SetXY(116,53);$this->MultiCell(11,36,"",'1','C');
			$this->SetXY(127,44);$this->MultiCell(11,9,"6-10",'1','C');
			$this->SetXY(127,53);$this->MultiCell(11,36,"",'1','C');
			$this->SetFont('Arial','B',7);		
			$this->SetXY(138,44);$this->MultiCell(11,9,"11-15",'1','C');
			$this->SetXY(138,53);$this->MultiCell(11,36,"",'1','C');
			
						$this->SetFont('Arial','B',8);		
			$this->SetXY(149,44);$this->MultiCell(11,9,"16-20",'1','C');
			$this->SetXY(149,53);$this->MultiCell(11,36,"",'1','C');
			$this->SetXY(160,44);$this->MultiCell(12,9,"21-30",'1','C');
			$this->SetXY(160,53);$this->MultiCell(12,36,"",'1','C');
			$this->SetXY(172,44);$this->MultiCell(12,9,"31-50",'1','C');
			$this->SetXY(172,53);$this->MultiCell(12,36,"",'1','C');
			$this->SetXY(184,44);$this->MultiCell(12,9,"51-60",'1','C');
			$this->SetXY(184,53);$this->MultiCell(12,36,"",'1','C');
			$this->SetXY(196,44);$this->MultiCell(12,9,"61-89",'1','C');
			$this->SetXY(196,53);$this->MultiCell(12,36,"",'1','C');
			//******DIVISION POR SEXO********\\\\
			$this->SetXY(116,80);$this->MultiCell(5,9,"M",'1','C');
			$this->SetXY(121,80);$this->MultiCell(6,9,"F",'1','C');
			$this->SetXY(127,80);$this->MultiCell(5,9,"M",'1','C');
			$this->SetXY(132,80);$this->MultiCell(6,9,"F",'1','C');
			$this->SetXY(138,80);$this->MultiCell(5,9,"M",'1','C');
			$this->SetXY(143,80);$this->MultiCell(6,9,"F",'1','C');
			$this->SetXY(149,80);$this->MultiCell(5,9,"M",'1','C');
			$this->SetXY(154,80);$this->MultiCell(6,9,"F",'1','C');
			$this->SetXY(160,80);$this->MultiCell(6,9,"M",'1','C');
			$this->SetXY(166,80);$this->MultiCell(6,9,"F",'1','C');
			$this->SetXY(172,80);$this->MultiCell(6,9,"M",'1','C');
			$this->SetXY(178,80);$this->MultiCell(6,9,"F",'1','C');
			$this->SetXY(184,80);$this->MultiCell(6,9,"M",'1','C');
			$this->SetXY(190,80);$this->MultiCell(6,9,"F",'1','C');
			$this->SetXY(196,80);$this->MultiCell(6,9,"M",'1','C');
			$this->SetXY(202,80);$this->MultiCell(6,9,"F",'1','C');
			//RESULTADOS
			$this->SetFont('Arial','B',7);		
			$this->SetXY(116,89);$this->MultiCell(5,9,$vran1,'1','C');
			$this->SetXY(121,89);$this->MultiCell(6,9,$mran1,'1','C');
			$this->SetXY(127,89);$this->MultiCell(5,9,$vran2,'1','C');
			$this->SetXY(132,89);$this->MultiCell(6,9,$mran2,'1','C');
			$this->SetXY(138,89);$this->MultiCell(5,9,$vran3,'1','C');
			$this->SetXY(143,89);$this->MultiCell(6,9,$mran3,'1','C');
			$this->SetXY(149,89);$this->MultiCell(5,9,$vran4,'1','C');
			$this->SetXY(154,89);$this->MultiCell(6,9,$mran4,'1','C');
			$this->SetXY(160,89);$this->MultiCell(6,9,$vran5,'1','C');
			$this->SetXY(166,89);$this->MultiCell(6,9,$mran5,'1','C');
			$this->SetXY(172,89);$this->MultiCell(6,9,$vran6,'1','C');
			$this->SetXY(178,89);$this->MultiCell(6,9,$mran6,'1','C');
			$this->SetXY(184,89);$this->MultiCell(6,9,$vran7,'1','C');
			$this->SetXY(190,89);$this->MultiCell(6,9,$mran7,'1','C');
			$this->SetXY(196,89);$this->MultiCell(6,9,$vran8,'1','C');
			$this->SetXY(202,89);$this->MultiCell(6,9,$mran8,'1','C');
			$this->SetXY(36,89);$this->MultiCell(36,9,$ttc,'1','C');
			$this->SetXY(72,89);$this->MultiCell(13,9,"0",'1','C');
			$this->SetXY(85,89);$this->MultiCell(15,9,$ttc,'1','C');
			$this->SetXY(100,89);$this->MultiCell(8,9,$trva,'1','C');
			$this->SetXY(108,89);$this->MultiCell(8,9,$trmu,'1','C');

			$mchi1 = 0;
		$mchi1 = $charla["mchi1"];
		$mchi2 = 0;
		$mchi2 = $charla["mchi2"];
		$mchi3 = 0;
		$mchi3 = $charla["mchi3"];
		$mchi4 = 0;
		$mchi4 = $charla["mchi4"];
		$mchi5 = 0;
		$mchi5 = $charla["mchi5"];
		$mchi6 = 0;
		$mchi6 = $charla["mchi6"];
		$mchi7 = 0;
		$mchi7 = $charla["mchi7"];
		$mchi8 = 0;
		$mchi8 = $charla["mchi8"];
		
		//HOMBRES POR chiGO DE EDAD
		$vchi1 = 0;
		$vchi1 = $charla["vchi1"];
		$vchi2 = 0;
		$vchi2 = $charla["vchi2"];
		$vchi3 = 0;
		$vchi3 = $charla["vchi3"];
		$vchi4 = 0;
		$vchi4 = $charla["vchi4"];
		$vchi5 = 0;
		$vchi5 = $charla["vchi5"];
		$vchi6 = 0;
		$vchi6 = $charla["vchi6"];
		$vchi7 = 0;
		$vchi7 = $charla["vchi7"];
		$vchi8 = 0;
		$vchi8 = $charla["vchi8"];
		// SUMA DE chiGOS DE SEXO
		$tcva = 0;
		$tcva = $vchi1 + $vchi2 + $vchi3 + $vchi4 + $vchi5 + $vchi6 + $vchi7 + $vchi8;
		$tcmu = 0;
		$tcmu = $mchi1 + $mchi2 + $mchi3 + $mchi4 + $mchi5 + $mchi6 + $mchi7 + $mchi8;

			$this->SetFont('Arial','B',9);	
			$this->SetXY(11,110);$this->MultiCell(150,5,"CUADRO DETALLADO ALBERGUE CHILPINILLA",'0','L');
			
			$this->SetFont('Arial','B',8);
			$this->SetXY(1,125);$this->MultiCell(35,9,"Descripcion del Servicio",'1','C');
			$this->SetXY(36,134);$this->MultiCell(36,45,"",'1','C'); 
			$this->SetXY(36,125);$this->MultiCell(36,9,"Numero de Beneficiarios",'1','C');
			$this->SetXY(72,134);$this->MultiCell(13,45,"",'1','C');
			$this->SetXY(72,125);$this->MultiCell(28,9,"Categoria Social ",'1','C');		
			$this->SetXY(100,134);$this->MultiCell(8,45,"",'1','C');
			$this->SetXY(100,125);$this->MultiCell(16,9,"Sexo",'1','C');	
			$this->SetXY(116,125);$this->MultiCell(92,9,"Edades",'1','C');
			$this->SetFont('Arial','B',9);
			$this->SetXY(1,134);$this->MultiCell(5,5,"INDICADOR",'1','C');
			$this->SetFont('Arial','B',8);
			$this->SetXY(1,179);$this->MultiCell(35,9,"Consulta Camana",'1','C');
			$this->SetXY(72,134);$this->MultiCell(13,9,"Pagante",'1','C');		
			$this->SetXY(85,134);$this->MultiCell(15,9,"Indigente",'1','C');		
			$this->SetXY(100,134);$this->MultiCell(8,9,"M",'1','C');		
			$this->SetXY(108,134);$this->MultiCell(8,9,"F",'1','C');
			$this->SetXY(116,143);$this->MultiCell(92,45,"",'1','C');
			$this->SetFont('Arial','B',8);		
			$this->SetXY(116,134);$this->MultiCell(11,9,"0-5",'1','C');
			$this->SetXY(116,143);$this->MultiCell(11,36,"",'1','C');
			$this->SetXY(127,134);$this->MultiCell(11,9,"6-10",'1','C');
			$this->SetXY(127,143);$this->MultiCell(11,36,"",'1','C');
			$this->SetFont('Arial','B',7);		
			$this->SetXY(138,134);$this->MultiCell(11,9,"11-15",'1','C');
			$this->SetXY(138,143);$this->MultiCell(11,36,"",'1','C');
			
						$this->SetFont('Arial','B',8);		
			$this->SetXY(149,134);$this->MultiCell(11,9,"16-20",'1','C');
			$this->SetXY(149,143);$this->MultiCell(11,36,"",'1','C');
			$this->SetXY(160,134);$this->MultiCell(12,9,"21-30",'1','C');
			$this->SetXY(160,143);$this->MultiCell(12,36,"",'1','C');
			$this->SetXY(172,134);$this->MultiCell(12,9,"31-50",'1','C');
			$this->SetXY(172,143);$this->MultiCell(12,36,"",'1','C');
			$this->SetXY(184,134);$this->MultiCell(12,9,"51-60",'1','C');
			$this->SetXY(184,143);$this->MultiCell(12,36,"",'1','C');
			$this->SetXY(196,134);$this->MultiCell(12,9,"61-179",'1','C');
			$this->SetXY(196,143);$this->MultiCell(12,36,"",'1','C');
			//******DIVISION POR SEXO********\\\\
			$this->SetXY(116,170);$this->MultiCell(5,9,"M",'1','C');
			$this->SetXY(121,170);$this->MultiCell(6,9,"F",'1','C');
			$this->SetXY(127,170);$this->MultiCell(5,9,"M",'1','C');
			$this->SetXY(132,170);$this->MultiCell(6,9,"F",'1','C');
			$this->SetXY(138,170);$this->MultiCell(5,9,"M",'1','C');
			$this->SetXY(143,170);$this->MultiCell(6,9,"F",'1','C');
			$this->SetXY(149,170);$this->MultiCell(5,9,"M",'1','C');
			$this->SetXY(154,170);$this->MultiCell(6,9,"F",'1','C');
			$this->SetXY(160,170);$this->MultiCell(6,9,"M",'1','C');
			$this->SetXY(166,170);$this->MultiCell(6,9,"F",'1','C');
			$this->SetXY(172,170);$this->MultiCell(6,9,"M",'1','C');
			$this->SetXY(178,170);$this->MultiCell(6,9,"F",'1','C');
			$this->SetXY(184,170);$this->MultiCell(6,9,"M",'1','C');
			$this->SetXY(190,170);$this->MultiCell(6,9,"F",'1','C');
			$this->SetXY(196,170);$this->MultiCell(6,9,"M",'1','C');
			$this->SetXY(202,170);$this->MultiCell(6,9,"F",'1','C');
			//RESULTADOS
			$this->SetFont('Arial','B',7);		
			$this->SetXY(116,179);$this->MultiCell(5,9,$vchi1,'1','C');
			$this->SetXY(121,179);$this->MultiCell(6,9,$mchi1,'1','C');
			$this->SetXY(127,179);$this->MultiCell(5,9,$vchi2,'1','C');
			$this->SetXY(132,179);$this->MultiCell(6,9,$mchi2,'1','C');
			$this->SetXY(138,179);$this->MultiCell(5,9,$vchi3,'1','C');
			$this->SetXY(143,179);$this->MultiCell(6,9,$mchi3,'1','C');
			$this->SetXY(149,179);$this->MultiCell(5,9,$vchi4,'1','C');
			$this->SetXY(154,179);$this->MultiCell(6,9,$mchi4,'1','C');
			$this->SetXY(160,179);$this->MultiCell(6,9,$vchi5,'1','C');
			$this->SetXY(166,179);$this->MultiCell(6,9,$mchi5,'1','C');
			$this->SetXY(172,179);$this->MultiCell(6,9,$vchi6,'1','C');
			$this->SetXY(178,179);$this->MultiCell(6,9,$mchi6,'1','C');
			$this->SetXY(184,179);$this->MultiCell(6,9,$vchi7,'1','C');
			$this->SetXY(190,179);$this->MultiCell(6,9,$mchi7,'1','C');
			$this->SetXY(196,179);$this->MultiCell(6,9,$vchi8,'1','C');
			$this->SetXY(202,179);$this->MultiCell(6,9,$mchi8,'1','C');
			$this->SetXY(36,179);$this->MultiCell(36,9,$tac,'1','C');
			$this->SetXY(72,179);$this->MultiCell(13,9,"0",'1','C');
			$this->SetXY(85,179);$this->MultiCell(15,9,$tac,'1','C');
			$this->SetXY(100,179);$this->MultiCell(8,9,$tcva,'1','C');
			$this->SetXY(108,179);$this->MultiCell(8,9,$tcmu,'1','C');





	}
	
	 
}

$pdf=new repo('P','mm','A4');
$pdf->AliasNbPages();
$pdf->SetMargins(5,5,0); 
$pdf->SetTitle("reporte");
$pdf->SetAutoPageBreak(false,0); 
$pdf->Open();
$pdf->AddPage();
$pdf->Publicar($charla);
$pdf->SetLeftMargin(25);
$pdf->Output();

?>