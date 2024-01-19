<?php
global $f;
$f->library('pdf');

class ficha extends FPDF
{
	function Header(){
		if($this->PageNo()==1){
			$this->Image(IndexPath.DS.'templates/pe/fichasocial.gif',15,15,180,267);
		}elseif($this->PageNo()==2){
			$this->Image(IndexPath.DS.'templates/pe/fichasocial2.gif',15,15,180,267);
		}elseif($this->PageNo()==3){
			$this->Image(IndexPath.DS.'templates/pe/fichasocial3.gif',15,15,180,267);
		}
	}	
	function calculaedad($fechanacimiento){
    	list($ano,$mes,$dia) = explode("-",$fechanacimiento);
    	$ano_diferencia  = date("Y") - $ano;
    	$mes_diferencia = date("m") - $mes;
    	$dia_diferencia   = date("d") - $dia;
    	if ($dia_diferencia < 0 || $mes_diferencia < 0)
        	$ano_diferencia--;
    	return $ano_diferencia;
	}	
	function Publicar($enti,$ficha){
		$meses = array (1 => "Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Setiembre", "Octubre", "Noviembre", "Diciembre");
		$sino = array("No","Si");
		$estadocivil=array(
			"SO"=>"Soltero",
			"CA"=>"Casado",
			"VI"=>"Viudo",
			"DI"=>"Divorciado",
			"SE"=>"Separado",
			"CO"=>"Conviviente"
		);
		$tipo_superior=array(
			"T"=>"Tecnico",
			"U"=>"Universitario",
			"M"=>"Maestria",
			"D"=>"Doctorado"
		);
		$completa=array(
			"1"=>"X",
			"0"=>""
		);
		$this->SetFont('courier','',9);
		$x=0;
		$y=50;//41
		$y_marg = 5;
		$this->SetY($y);
		$this->ln();
		$this->SetFont('arial','',10);
		/******************************************** Pagina 1 */	
		$this->SetXY(15, 45);$this->Cell(50,$y_marg,$enti["appat"],'0',0,'L',0);
		$this->SetXY(68, 45);$this->Cell(50,$y_marg,$enti["apmat"],'0',0,'L',0);
		$this->SetXY(120, 45);$this->Cell(60,$y_marg,$enti["nomb"],'0',0,'L',0);
		if(isset($enti['fecnac'])){
			$this->SetXY(180, 45);$this->Cell(50,$y_marg,$this->calculaedad(Date::format($enti['fecnac']->sec, 'Y-m-d')),'0',0,'L',0);
		}
		$ruc = '';
		$dni = '';
		foreach($enti["docident"] as $doci){
			if($doci["tipo"]=="DNI")$dni = $doci["num"];
			if($doci["tipo"]=="RUC")$ruc = $doci["num"];
		}
		$this->SetXY(15, 60.5);$this->Cell(45,$y_marg,$dni,'0',0,'L',0);
		$this->SetXY(48, 60.5);$this->Cell(45,$y_marg,$ruc,'0',0,'L',0);
		$this->SetXY(88, 60.5);$this->Cell(45,$y_marg,$enti["roles"]["trabajador"]["essalud"],'0',0,'L',0);
		if(isset($enti["sangre"])){
			$this->SetXY(127, 60.5);$this->Cell(45,$y_marg,$enti["sangre"],'0',0,'L',0);
		}
		if(isset($enti['fecnac'])){
			$this->SetXY(163, 60.5);$this->Cell(45,$y_marg,Date::format($enti['fecnac']->sec, 'Y-m-d'),'0',0,'L',0);
		}
		//if(isset($enti["roles"]["trabajador"]["ficha"]["fec_adm_pub"]))$this->SetXY(15, 74.5);$this->Cell(45,$y_marg,$enti["roles"]["trabajador"]["ficha"]["fec_adm_pub"],'0',0,'L',0);		
		//if(isset($enti["roles"]["trabajador"]["ficha"]["fec_ing_sbpa"]))$this->SetXY(47, 74.5);$this->Cell(45,$y_marg,$enti["roles"]["trabajador"]["ficha"]["fec_ing_sbpa"],'0',0,'L',0);
		$this->SetXY(142, 72);$this->MultiCell(60,$y_marg,$enti["roles"]["trabajador"]["contrato"]["nomb"],'0','L');
		//$this->SetXY(15, 88.5);$this->Cell(45,$y_marg,$enti["roles"]["trabajador"]["organizacion"]["nomb"],'0',0,'L',0);
		$this->SetXY(15, 87);$this->MultiCell(80,$y_marg,$enti["roles"]["trabajador"]["organizacion"]["nomb"],'0','L');
		$this->SetXY(92, 88.5);$this->Cell(45,$y_marg,$enti["roles"]["trabajador"]["cargo"]["nomb"],'0',0,'L',0);
		$this->SetXY(163, 88.5);$this->Cell(45,$y_marg,"Nivel",'0',0,'L',0);
		if(isset($enti["domicilios"])){
			$this->SetXY(15, 103);$this->Cell(150,$y_marg,$enti["domicilios"][0]["direccion"],'0',0,'L',0);
		}
		if(isset($enti["telefonos"])){
			$this->SetXY(163, 103);$this->Cell(45,$y_marg,$enti["telefonos"][0]["num"],'0',0,'L',0);
		}
		if(isset($enti["roles"]["trabajador"]["pension"])){
			$this->SetXY(15, 117);$this->Cell(150,$y_marg,$enti["roles"]["trabajador"]["pension"]["nomb"],'0',0,'L',0);
		}
		if(isset($enti["roles"]["trabajador"]["cod_aportante"])){
			$this->SetXY(163, 117);$this->Cell(150,$y_marg,$enti["roles"]["trabajador"]["cod_aportante"],'0',0,'L',0);	
		}
		if($ficha["estudios"]["primaria"]==true){
			$this->SetXY(50.5, 137);$this->Cell(10,$y_marg,"X",'0',0,'L',0);
		}else{
			$this->SetXY(93, 137);$this->Cell(10,$y_marg,"X",'0',0,'L',0);
		}
		if($ficha["estudios"]["secundaria"]==true){
			$this->SetXY(50.5, 146.5);$this->Cell(10,$y_marg,"X",'0',0,'L',0);
		}else{
			$this->SetXY(93, 146.5);$this->Cell(10,$y_marg,"X",'0',0,'L',0);
		}
		/** Estudios Superiores */
		if(isset($ficha["estudios"]["superior"])){
			$this->SetFont('arial','',9);	
			for($i=0;$i<count($ficha["estudios"]["superior"]);$i++){
				
				$y2 = 168;
				$this->SetXY(15, $y2);$this->MultiCell(35,5,$tipo_superior[$ficha["estudios"]["superior"][$i]["tipo"]],'0','L');
				$this->SetXY(38, $y2);$this->MultiCell(50,5,$ficha["estudios"]["superior"][$i]["grado"],'0','L');
				$this->SetXY(86, $y2);$this->MultiCell(55,5,$ficha["estudios"]["superior"][$i]["lugar"],'0','L');
				$this->SetFont('arial','',10);	
				$this->SetXY(145.6, $y2+2.2);$this->MultiCell(55,5,$completa[$ficha["estudios"]["superior"][$i]["completa"]],'0','L');
				$this->SetFont('arial','',8);	
				$this->SetXY(157, $y2);$this->MultiCell(20,5,Date::format($ficha["estudios"]["superior"][$i]["fecini"]->sec, 'Y-m-d'),'0','L');
				$this->SetXY(177, $y2);$this->MultiCell(20,5,Date::format($ficha["estudios"]["superior"][$i]["fecfin"]->sec, 'Y-m-d'),'0','L');
				$y2=4+$y2;
			}
		}
		$this->SetFont('arial','',10);	
		if(isset($ficha["colegiatura"])){
			/** Colegiatura */
			$this->SetXY(15, 261);$this->Cell(90,$y_marg,$ficha["colegiatura"]["colegio"],'0',0,'L',0);
			$this->SetXY(112, 261);$this->Cell(40,$y_marg,$ficha["colegiatura"]["cod"],'0',0,'L',0);
			$this->SetXY(157, 261);$this->Cell(40,$y_marg,Date::format($ficha["colegiatura"]["fec"]->sec, 'Y-m-d'),'0',0,'L',0);
		}
		
		/******************************************** Pagina 2 */ 
		$this->AddPage();
		if(isset($ficha["familia"]["padre"])){
			/** Padre */
			$this->SetXY(15, 32);$this->Cell(90,$y_marg,$ficha["familia"]["padre"]["appat"]." ".$ficha["familia"]["padre"]["apmat"].", ".$ficha["familia"]["padre"]["nomb"],'0',0,'L',0);
			$this->SetXY(119, 32);$this->Cell(90,$y_marg,$sino[$ficha["familia"]["padre"]["vivo"]],'0',0,'L',0);
			$this->SetXY(140, 32);$this->Cell(40,$y_marg,Date::format($ficha["familia"]["padre"]["fecnac"]->sec, 'Y-m-d'),'0',0,'L',0);
			$this->SetXY(180, 32);$this->Cell(90,$y_marg,$this->calculaedad(Date::format($ficha["familia"]["padre"]["fecnac"]->sec, 'Y-m-d')),'0',0,'L',0);
			/** Madre */
			$this->SetXY(15, 47);$this->Cell(90,$y_marg,$ficha["familia"]["madre"]["appat"]." ".$ficha["familia"]["madre"]["apmat"].", ".$ficha["familia"]["madre"]["nomb"],'0',0,'L',0);
			$this->SetXY(119, 47);$this->Cell(15,$y_marg,$sino[$ficha["familia"]["madre"]["vivo"]],'0',0,'L',0);
			$this->SetXY(140, 47);$this->Cell(40,$y_marg,Date::format($ficha["familia"]["madre"]["fecnac"]->sec, 'Y-m-d'),'0',0,'L',0);
			$this->SetXY(180, 47);$this->Cell(90,$y_marg,$this->calculaedad(Date::format($ficha["familia"]["madre"]["fecnac"]->sec, 'Y-m-d')),'0',0,'L',0);
			/** Hermanos */
		}
		if(isset($ficha["familia"]["hermanos"])){
			$y1=62;
			if(count($ficha["familia"]["hermanos"])>7)$limite = 7;
			else $limite = count($ficha["familia"]["hermanos"]);
			for($i=0;$i<$limite;$i++){
				$this->SetXY(15, $y1);$this->Cell(90,$y_marg,$ficha["familia"]["hermanos"][$i]["appat"]." ".$ficha["familia"]["hermanos"][$i]["apmat"].", ".$ficha["familia"]["hermanos"][$i]["nomb"],'0',0,'L',0);
				$this->SetXY(119, $y1);$this->Cell(15,$y_marg,$sino[$ficha["familia"]["hermanos"][$i]["vivo"]],'0',0,'L',0);
				$this->SetXY(140, $y1);$this->Cell(45,$y_marg,Date::format($ficha["familia"]["hermanos"][$i]["fecnac"]->sec, 'Y-m-d'),'0',0,'L',0);
				$this->SetXY(180, $y1);$this->Cell(45,$y_marg,$this->calculaedad(Date::format($ficha["familia"]["hermanos"][$i]["fecnac"]->sec, 'Y-m-d')),'0',0,'L',0);
				$y1=10+$y1;
			}
		}
		/** Estado Civil */
		if(isset($enti["estado_civil"])){
			$this->SetXY(15, 149);$this->Cell(40,$y_marg,$estadocivil[$enti["estado_civil"]],'0',0,'L',0);
		}
		if(isset($ficha["familia"]["conyuge"])){
			$this->SetXY(55, 149);$this->Cell(60,$y_marg,$ficha["familia"]["conyuge"]["appat"]." ".$ficha["familia"]["conyuge"]["apmat"]." ".$ficha["familia"]["conyuge"]["nomb"],'0',0,'L',0);
			$this->SetXY(157, 149);$this->Cell(60,$y_marg,Date::format($ficha["familia"]["conyuge"]["fecnac"]->sec, 'Y-m-d'),'0',0,'L',0);
		}
		/** Hijos */
		if(isset($ficha["familia"]["hijos"])){
			$y1=174;
			if(count($ficha["familia"]["hijos"])>9)$limite = 9;
			else $limite = count($ficha["familia"]["hijos"]);
			for($i=0;$i<$limite;$i++){
				$this->SetXY(15, $y1);$this->Cell(90,$y_marg,$ficha["familia"]["hijos"][$i]["appat"]." ".$ficha["familia"]["hijos"][$i]["apmat"].", ".$ficha["familia"]["hijos"][$i]["nomb"],'0',0,'L',0);
				$this->SetXY(119, $y1);$this->Cell(15,$y_marg,$ficha["familia"]["hijos"][$i]["sexo"],'0',0,'L',0);
				$this->SetXY(140, $y1);$this->Cell(45,$y_marg,Date::format($ficha["familia"]["hijos"][$i]["fecnac"]->sec, 'Y-m-d'),'0',0,'L',0);
				$this->SetXY(180, $y1);$this->Cell(45,$y_marg,$this->calculaedad(Date::format($ficha["familia"]["hijos"][$i]["fecnac"]->sec, 'Y-m-d')),'0',0,'L',0);
				$y1=10+$y1;
			}
		}
		/******************************************** Pagina 3 */ 
		$this->AddPage();
		$this->SetFont('courier','',9);	
		/** Experiencia Laboral */
		if(isset($ficha["experiencia"])){			
			for($i=0;$i<count($ficha["experiencia"]);$i++){
				if($ficha["experiencia"][$i]["tipo"]=="PU"){
				$sec_pub[]=$ficha["experiencia"][$i];
				}
			}
			for($i=0;$i<count($ficha["experiencia"]);$i++){
				if($ficha["experiencia"][$i]["tipo"]=="PR"){
				$sec_priv[]=$ficha["experiencia"][$i];
				}
			}
			$y1=33.5;
			for($i=0;$i<count($sec_pub);$i++){
				if($i<6){
					$this->SetFont('arial','',9);	
					$this->SetXY(15, $y1);$this->MultiCell(55,$y_marg,$sec_pub[$i]["lugar"],'0','L');
					$this->SetXY(67, $y1);$this->MultiCell(55,$y_marg,$sec_pub[$i]["cargo"],'0','L');
					$this->SetXY(120, $y1);$this->MultiCell(40,$y_marg,$sec_pub[$i]["motivo"],'0','L');
					$this->SetFont('arial','',8);	
					$this->SetXY(157, $y1);$this->MultiCell(20,$y_marg,Date::format($sec_pub[$i]["fecini"]->sec, 'Y-m-d'),'0','L');
					$this->SetXY(177, $y1);$this->MultiCell(20,$y_marg,Date::format($sec_pub[$i]["fecfin"]->sec, 'Y-m-d'),'0','L');
					$y1=10+$y1;
				}
			}
			$y1=112;
			for($i=0;$i<count($sec_priv);$i++){
				if($i<6){
					$this->SetFont('arial','',9);	
					$this->SetXY(15, $y1);$this->MultiCell(55,$y_marg,$sec_priv[$i]["lugar"],'0','L');
					$this->SetXY(67, $y1);$this->MultiCell(55,$y_marg,$sec_priv[$i]["cargo"],'0','L');
					$this->SetXY(120, $y1);$this->MultiCell(40,$y_marg,$sec_priv[$i]["motivo"],'0','L');
					$this->SetFont('arial','',8);	
					$this->SetXY(157, $y1);$this->MultiCell(20,$y_marg,Date::format($sec_priv[$i]["fecini"]->sec, 'Y-m-d'),'0','L');
					$this->SetXY(177, $y1);$this->MultiCell(20,$y_marg,Date::format($sec_priv[$i]["fecfin"]->sec, 'Y-m-d'),'0','L');
					$y1=10+$y1;
				}
			}
			/** Idiomas */
			$y1=197;
			for($i=0;$i<count($ficha["idiomas"]);$i++){
				if($i<3){
					$this->SetFont('arial','',9);	
					$this->SetXY(15, $y1);$this->MultiCell(55,$y_marg,$ficha["idiomas"][$i]["idioma"],'0','L');
					$this->SetXY(60, $y1);$this->MultiCell(10,$y_marg,$completa[$ficha["idiomas"][$i]["habla"]],'0','L');
					$this->SetXY(84, $y1);$this->MultiCell(10,$y_marg,$completa[$ficha["idiomas"][$i]["lee"]],'0','L');
					$this->SetXY(108, $y1);$this->MultiCell(10,$y_marg,$completa[$ficha["idiomas"][$i]["escribe"]],'0','L');
					$this->SetXY(131, $y1);$this->MultiCell(70,$y_marg,$ficha["idiomas"][$i]["lugar"],'0','L');
					$y1=10+$y1;
				}
			}
			/** Fecha Actual */
			$dia = date ("d");
			$mese = date ("n");
			$mes = $meses[$mese];
			$ano = date ("Y"); 
			$this->SetXY(129, 233.5);$this->MultiCell(55,$y_marg,$dia." de ".$mes." del ".$ano,'0','L');
		}
		
	}
	function Footer()
	{
		//footer
	} 
	 
}

$pdf=new ficha('P','mm','A4');
$pdf->SetMargins(15,39.5,0); 
$pdf->SetTitle("boleta");
$pdf->SetAutoPageBreak(false,0); 
$pdf->Open();
$pdf->AddPage();
$pdf->Publicar($enti,$ficha);
$pdf->SetLeftMargin(25);
$pdf->Output();

?>