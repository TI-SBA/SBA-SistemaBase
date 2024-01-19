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
		$this->SetXY(5,5);$this->MultiCell(200,5,date("d/m/Y")." Página: ".$this->PageNo()."/{nb}",'0','R');
		$this->SetFont('Arial','',9);
		$this->SetXY(10,15);$this->MultiCell(60,5,"Centro de Salud Mental",'0','C');
		$this->SetXY(10,20);$this->MultiCell(60,5,"'Moises Heresi'",'0','C');
		$this->SetFont('Arial','B',16);
		$this->SetXY(0,30);$this->MultiCell(210,5,	"PARTE DIARIO DE CONSULTA EXTERNA",'0','C');
		
	}	
	function getAge($birthday) {
		  $birth = $birthday;
		  $now = strtotime('now');
		  $age = ($now - $birth) / 31536000;
		  return floor($age);
	}
 	
	function Publicar($diario){
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
			$generos= array(
			"0"=>"Femenino",
			"1"=>"Masculino"
			

		);


		$x=5;
		$y=25;
		$y_ini = $y;
		$page_b = 275;
		$this->SetFont('Arial','B',10);
		
		$this->SetXY(5,38);$this->MultiCell(80,8,"Numero de Parte Diario: ",'0','L');
		$this->SetFont('Arial','',10);
		$this->SetXY(48,38);$this->MultiCell(10,8,"".$diario["num"],'0','L');
		$this->SetFont('Arial','B',10);
		$this->SetXY(5,40);$this->MultiCell(23,15,"DOCTOR: ",'0','L');
		$this->SetFont('Arial','',10);
		$this->SetXY(28,40);$this->MultiCell(200,15,"".$diario["medico"]["appat"]. ' '.$diario["medico"]["apmat"]. ','.$diario["medico"]["nomb"],'0s','L');
		$this->SetFont('Arial','B',10);
		$this->SetXY(5,45);$this->MultiCell(23,15,"Fecha: ",'0','L');
		$this->SetFont('Arial','',10);
		$this->SetXY(28,45);$this->MultiCell(60,15,"".date('d-m-Y',$diario["fech"]->sec),'0','L');
		$this->SetFont('Arial','',10);	
		$y=$y+40;

		$this->SetFont('Arial','B',8);
		//CABECERAS
		$yini = $y;
		$this->SetXY(5,$y);$this->MultiCell(15,5,"H.C",'1','C');
		$this->SetXY(20,$y);$this->MultiCell(50,5,"Paciente",'1','C'); 
		$this->SetXY(70,$y);$this->MultiCell(20,5,"Sexo ",'1','C');
		$this->SetXY(90,$y);$this->MultiCell(10,5,"Edad",'1','C');
		$this->SetXY(100,$y);$this->MultiCell(20,5,"Estado",'1','C');
		$this->SetXY(120,$y);$this->MultiCell(40,5,"Diagnostico",'1','C');
		$this->SetXY(160,$y);$this->MultiCell(15,5,"Categ.",'1','C');
		$this->SetXY(175,$y);$this->MultiCell(30,5,"Procedencia",'1','C');

		$y=$y+9;
		

		//RESUMEN 
		$mujeres = 0;
		$varones = 0;
		$primero = 0;
		$segundo = 0;
		$tercero = 0;
		$cuarto = 0;
		$quinto = 0;
		$sexto = 0;
		$setimo = 0;
		$octavo = 0;

		$nuevo = 0;
		$continuador = 0;
		$reingresante = 0;
		$se = 0;
		$a = 0;
		$b = 0;
		$c = 0;
		$d = 0;
		$e = 0;
		$f = 0;
		$g = 0;
		//RELLENAR GRILLA
		
		for($i = 0;$i<count($diario["consulta"]);$i++){
			if($y>200){
				$this->AddPage();
				$y = $yini;
				$this->SetXY(5,$y);$this->MultiCell(15,5,"H.C",'1','C');
				$this->SetXY(20,$y);$this->MultiCell(50,5,"Paciente",'1','C'); 
				$this->SetXY(70,$y);$this->MultiCell(20,5,"Sexo ",'1','C');
				$this->SetXY(90,$y);$this->MultiCell(10,5,"Edad",'1','C');
				$this->SetXY(100,$y);$this->MultiCell(20,5,"Estado",'1','C');
				$this->SetXY(120,$y);$this->MultiCell(40,5,"Diagnostico",'1','C');
				$this->SetXY(160,$y);$this->MultiCell(15,5,"Categ.",'1','C');
				$this->SetXY(175,$y);$this->MultiCell(30,5,"Procedencia",'1','C');
				$y+=9;
			}
			$this->SetFont('Arial','',8);
			//$y=$y+5;
			$edad_actual =$this->getAge($diario["consulta"][$i]["paciente"]['fecha_na']->sec);
			$this->SetXY(5,$y);$this->MultiCell(15,5,$diario["consulta"][$i]["paciente"]['his_cli'],'0','C');
			$this->SetXY(70,$y);$this->MultiCell(20,5,$generos[$diario["consulta"][$i]["paciente"]['sexo']],'0','C');
			//$this->SetXY(142,$y);$this->MultiCell(12,9,$this->getAge($diario["consulta"][$i]["paciente"]['fecha_na']->sec),'0','C');
			$this->SetXY(90,$y);$this->MultiCell(10,5,$edad_actual,'0','C');	
			$this->SetXY(100,$y);$this->MultiCell(20,5,$estados[$diario["consulta"][$i]['esta']],'0','C');
			$this->SetXY(160,$y);$this->MultiCell(15,5,$categorias[$diario["consulta"][$i]['cate']],'0','C');
			$this->SetFont('Arial','',7);
			$y_3 = ceil($this->GetStringWidth($diario["consulta"][$i]['paciente']['procedencia']['distrito'])/30);
			$this->SetXY(175,$y);$this->MultiCell(30,5,$diario["consulta"][$i]['paciente']['procedencia']['distrito'],'0','C');
			$this->SetFont('Arial','',8);
			$y_1 = ceil($this->GetStringWidth($diario["consulta"][$i]["paciente"]["paciente"]['appat']. ' '.$diario["consulta"][$i]["paciente"]["paciente"]['apmat'].','.$diario["consulta"][$i]["paciente"]["paciente"]['nomb'])/50);
			$this->SetXY(20,$y);$this->MultiCell(50,5,$diario["consulta"][$i]["paciente"]["paciente"]['appat']. ' '.$diario["consulta"][$i]["paciente"]["paciente"]['apmat'].','.$diario["consulta"][$i]["paciente"]["paciente"]['nomb'],'0','C');
			$y_2 = ceil($this->GetStringWidth($diario["consulta"][$i]['cie10'])/30);
			$this->SetXY(120,$y);$this->MultiCell(40,5,$diario["consulta"][$i]['cie10'],'0','C');

			$y+=max($y_1, $y_2, $y_3)*5;

			//$y=$this->getY();
			$this->Line(5, $y, 205,$y);
			$this->Line(5, $yini, 5,$y);
			$this->Line(20, $yini, 20,$y);
			$this->Line(70, $yini, 70,$y);
			$this->Line(90, $yini, 90,$y);
			$this->Line(100, $yini, 100,$y);
			$this->Line(120, $yini, 120,$y);
			$this->Line(160, $yini, 160,$y);
			$this->Line(175, $yini, 175,$y);
			$this->Line(205, $yini, 205,$y);
			if($diario["consulta"][$i]['paciente']['sexo']==0){
				$mujeres++;
			}
			if($diario["consulta"][$i]['paciente']['sexo']==1){
				$varones++;
			}
			//ESTADO
			if($diario["consulta"][$i]['esta'] == 2){
				$nuevo++;
			}
			if($diario["consulta"][$i]['esta'] == 3){
				$continuador++;
			}
			if($diario["consulta"][$i]['esta'] == 4){
				$reingresante++;
			}
			if($diario["consulta"][$i]['esta'] == 1){
				$se++;
			}
			//CATEGORIA
			if($diario["consulta"][$i]['cate'] == 2){
				$a++;
			}
			if($diario["consulta"][$i]['cate'] == 3){
				$b++;
			}
			if($diario["consulta"][$i]['cate'] == 4){
				$c++;
			}
			if($diario["consulta"][$i]['cate'] == 5){
				$d++;
			}
			if($diario["consulta"][$i]['cate'] == 6){
				$e++;
			}
			if($diario["consulta"][$i]['cate'] == 7){
				$f++;
			}
			if($diario["consulta"][$i]['cate'] == 8){
				$g++;
			}
			if($diario["consulta"][$i]['cate'] == 1){
				$se++;
			}
			if($edad_actual>=0 && $edad_actual<6){
				$primero++;
			}

			if($edad_actual>=6 && $edad_actual<11){
				$segundo++;
			}

			if($edad_actual>=11 && $edad_actual<16){
				$tercero++;
			}

			if($edad_actual>=16 && $edad_actual<21){
				$cuarto++;
			}
			if($edad_actual>=21 && $edad_actual<31){
				$quinto++;
			}
			if($edad_actual>=31 && $edad_actual<51){
				$sexto++;
			}
			if($edad_actual>=51 && $edad_actual<61){
				$setimo++;
			}
			if($edad_actual>=61 && $edad_actual<100){
				$octavo++;
			}
		}
							
		$this->AddPage();
		$y= 40;
		$this->SetFont('Arial','B',10);
		$this->SetXY(1,$y);$this->MultiCell(200,5,"Detalles por grupos de Diagnósticos",'0','L');
		$this->SetFont('Arial','',10);
		$y=$y+5;
		$this->SetXY(5,$y);$this->MultiCell(200,5,"F00-F09:Transtornos mentales orgánicos, incluidos los sintomáticos.",'0','L');	
		$y=$y+5;
		$this->SetXY(5,$y);$this->MultiCell(200,5,"F10-F19:Transtornos mentales y del comportamiento debidos al consumo de sustancias psicotropas..",'0','L');
		$y=$y+5;
		$this->SetXY(5,$y);$this->MultiCell(200,5,"F20-F29:Esquisofrenia, transtorno esquizotípico y transtorno de ideas delirantes.",'0','L');
		$y=$y+5;
		$this->SetXY(5,$y);$this->MultiCell(200,5,"F30-F39:Transtornos de humor(afectivos).",'0','L');
		$y=$y+5;
		$this->SetXY(5,$y);$this->MultiCell(200,5,"F40-F49:Transtornos neuróticos secundarios a situaciones estresantes y somatomorfos.",'0','L');
		$y=$y+5;
		$this->SetXY(5,$y);$this->MultiCell(200,5,"F50-F59:Transtornos de comportamiento asociados a disfunciones fisiológicas y a factores somáticos.",'0','L');
		$y=$y+5;
		$this->SetXY(5,$y);$this->MultiCell(200,5,"F60-F69:Transtornos de la personalidad y del comportamiento del adulto.",'0','L');
		$y=$y+5;
		$this->SetXY(5,$y);$this->MultiCell(200,5,"F70-F79:Retraso Mental.",'0','L');
		$y=$y+5;
		$this->SetXY(5,$y);$this->MultiCell(200,5,"F80-F89:Transtornos de desarrollo psicológico.",'0','L');
		$y=$y+5;
		$this->SetXY(5,$y);$this->MultiCell(200,5,"F90-F98:Transtornos del comportamiento y de las emociones de comienzo habitual en la infancia y adolescencia.",'0','L');
		$y=$y+5;
		$this->SetXY(5,$y);$this->MultiCell(200,5,"F99:Transtornos mental sin especificación.",'0','L');
		$y=$y+10;
		$this->SetFont('Arial','B',10);
		$this->SetXY(1,$y);$this->MultiCell(200,5,"RESUMEN",'0','L');

		$this->SetXY(22,110);$this->MultiCell(200,5,"SEXO",'0','L');
		$this->SetXY(62,110);$this->MultiCell(200,5,"ESTADO",'0','L');
		$this->SetXY(102,110);$this->MultiCell(200,5,"CATEGORIA",'0','L');
		$this->SetXY(142,110);$this->MultiCell(200,5,"EDAD",'0','L');
		$y=$y+5;
		$this->SetXY(20,$y);$this->MultiCell(200,15,"Mujeres: ".$mujeres,'0','L');
		$y=$y+5;
		$this->SetXY(20,$y);$this->MultiCell(200,15,"Varones: ".$varones,'0','L');
		$y=$y+5;
		$this->SetXY(60,110);$this->MultiCell(200,15,"S/E: ".$se,'0','L');
		$y=$y+5;
		$this->SetXY(60,115);$this->MultiCell(200,15,"Nuevos: ".$nuevo,'0','L');
		$y=$y+5;
		$this->SetXY(60,120);$this->MultiCell(200,15,"Continuadores: ".$continuador,'0','L');
		$y=$y+5;
		$this->SetXY(60,125);$this->MultiCell(200,15,"Reingresante: ".$reingresante,'0','L');
		$y=$y+5;
		
		$this->SetXY(100,110);$this->MultiCell(200,15,"Categoria PP: ".$a,'0','L');
		$y=$y+5;
		$this->SetXY(100,115);$this->MultiCell(200,15,"Categoria P: ".$b,'0','L');
		$y=$y+5;
		$this->SetXY(100,120);$this->MultiCell(200,15,"Categoria A: ".$c,'0','L');
		$y=$y+5;
		$this->SetXY(100,125);$this->MultiCell(200,15,"Categoria B: ".$d,'0','L');
		$y=$y+5;
		$this->SetXY(100,130);$this->MultiCell(200,15,"Categoria C: ".$e,'0','L');
		$y=$y+5;
		$this->SetXY(100,135);$this->MultiCell(200,15,"Categoria D: ".$g,'0','L');
		$y=$y+5;
		$this->SetXY(100,140);$this->MultiCell(200,15,"Categoria E: ".$f,'0','L');
		$y=$y+5;
		$this->SetXY(100,145);$this->MultiCell(200,15,"Categoria S/E ".$se,'0','L');
		$y=$y+5;
		//------------------------------------------------------------------------
		$this->SetXY(140,110);$this->MultiCell(200,15,"00 - 5 Años: ".$primero,'0','L');
		$y=$y+5;
		$this->SetXY(140,115);$this->MultiCell(200,15,"6 - 10 Años: ".$segundo,'0','L');
		$y=$y+5;
		$this->SetXY(140,120);$this->MultiCell(200,15,"11 - 15 Años: ".$tercero,'0','L');
		$y=$y+5;
		$this->SetXY(140,125);$this->MultiCell(200,15,"16 - 20 Años: ".$cuarto,'0','L');
		$y=$y+5;
		$this->SetXY(140,130);$this->MultiCell(200,15,"21 - 30 Años: ".$quinto,'0','L');
		$y=$y+5;
		$this->SetXY(140,135);$this->MultiCell(200,15,"31 - 50 Años: ".$sexto,'0','L');
		$y=$y+5;
		$this->SetXY(140,140);$this->MultiCell(200,15,"51 - 60 Años: ".$setimo,'0','L');
		$y=$y+5;
		$this->SetXY(140,145);$this->MultiCell(200,15,"61 - 91 Años: ".$octavo,'0','L');

	} 
}

$pdf=new repo('P','mm','A4');
$pdf->AliasNbPages();
$pdf->SetMargins(10,10,0); 
$pdf->SetTitle("reporte");
$pdf->SetAutoPageBreak(false,0); 
$pdf->Open();
$pdf->AddPage();
$pdf->Publicar($diario);
$pdf->SetLeftMargin(25);
//header('Content-Type: application/pdf; charset=utf-8');
$pdf->Output();

?>
