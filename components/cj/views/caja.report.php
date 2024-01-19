<?php
global $f;
$f->library('pdf');

class repo extends FPDF
{
	function Header(){
		//$this->Image(IndexPath.DS.'templates/ts/comprobante.gif',10,10,190,275);	
		$y=10;
		$this->SetFont('Arial','B',9);
		$this->SetXY(10,5);$this->MultiCell(80,5,"SOCIEDAD DE BENEFICENCIA PUBLICA DE AREQUIPA",'0','C');		
		$this->SetXY(10,5);$this->MultiCell(190,5,date("d/m/Y")." Página: ".$this->PageNo()."/{nb}",'0','R');
		$this->SetFont('Arial','',9);
		$this->SetXY(10,15);$this->MultiCell(80,5,"Centro de Salud Mental",'0','C');
		$this->SetXY(10,20);$this->MultiCell(80,5,"'Moises Heresi'",'0','C');
		$this->SetFont('Arial','B',13);
		$this->SetXY(5,30);$this->MultiCell(200,5,"Reporte de Conceptos de Caja Moises Heresi",'0','C');
		
	}	
	function Publicar($conceptos){
		$x=5;
		$y=25;
		$i_ini=$y;
		$pag_b = 275;
		$y=$y+12;
		$this->SetFont('Arial','B',8);
		$cancha =0;
		$busqueda =0;
		$carnet = 0;
		$certificado = 0;
		$constancia = 0;
		$con_es_psic_cont = 0;
		$con_es_psic_ind = 0;
		$con_es_psic_nuev = 0;
		$con_es_psiq_cont = 0;
		$con_es_psiq_ind = 0;
		$con_es_psiq_nuev = 0;
		$con_es_tera_cont = 0;
		$con_es_tera_ind = 0;
		$con_es_tera_nuev = 0;
		$cons_medi_nuevo = 0;
		$cons_medi_conti = 0;
		$cons_medi_indi = 0;
		$cop_cer_hoja = 0;
		$hist_clinica = 0;
		$infor_medico = 0;
		$manual = 0;
		$ambulancia = 0;
		$humus = 0;
		$ganado = 0;




		//*/
		
		
		
		//

		for ($i=0; $i <count($conceptos); $i++) { 
			for($j = 0;$j<count($conceptos[$i]['items']);$j++){
				if($conceptos[$i]['items'][$j]['servicio']['nomb'] == 'Alquiler Cancha Deportiva por Hora'){
							$cancha++;								
				}
				if($conceptos[$i]['items'][$j]['servicio']['nomb'] == 'Búsqueda Historia'){
							$busqueda++;								
				}
				if($conceptos[$i]['items'][$j]['servicio']['nomb'] == 'Carnet de Atención'){
							$carnet++;								
				}
				if($conceptos[$i]['items'][$j]['servicio']['nomb'] == 'Certificado Médico'){
							$certificado++;								
				}
				if($conceptos[$i]['items'][$j]['servicio']['nomb'] == 'Constancia Médica'){
							$constancia++;								
				}
				if($conceptos[$i]['items'][$j]['servicio']['nomb'] == 'Consulta Especializada - Psicología - Continuador'){
							$con_es_psic_cont++;								
				}
				if($conceptos[$i]['items'][$j]['servicio']['nomb'] == 'Consulta Especializada - Psicología - Indigente'){
							$con_es_psic_ind++;								
				}
				if($conceptos[$i]['items'][$j]['servicio']['nomb'] == 'Consulta Especializada - Psicología - Nuevo'){
							$con_es_psic_nuev++;								
				}
				if($conceptos[$i]['items'][$j]['servicio']['nomb'] == 'Consulta Especializada - Psiquiatría - Continuadores'){
							$con_es_psiq_cont++;								
				}
				if($conceptos[$i]['items'][$j]['servicio']['nomb'] == 'Consulta Especializada - Psiquiatría - Indigentes'){
							$con_es_psiq_ind++;								
				}
				if($conceptos[$i]['items'][$j]['servicio']['nomb'] == 'Consulta Especializada - Psiquiatría - Nuevo'){
							$con_es_psiq_nuev++;								
				}
				if($conceptos[$i]['items'][$j]['servicio']['nomb'] == 'Consulta Especializada - Terapeuta - Continuador'){
							$con_es_tera_cont++;								
				}
				if($conceptos[$i]['items'][$j]['servicio']['nomb'] == 'Consulta Especializada - Terapeuta - Indigente'){
							$con_es_tera_ind++;								
				}
				if($conceptos[$i]['items'][$j]['servicio']['nomb'] == 'Consulta Especializada - Terapeuta - Nuevo'){
							$con_es_tera_nuev++;								
				}
				if($conceptos[$i]['items'][$j]['servicio']['nomb'] == 'Consulta Médica - Nuevo'){
							$cons_medi_nuevo++;								
				}
				if($conceptos[$i]['items'][$j]['servicio']['nomb'] == 'Consulta Médica Continuador'){
							$cons_medi_conti++;								
				}
				if($conceptos[$i]['items'][$j]['servicio']['nomb'] == 'Consulta Médica Indigente'){
							$cons_medi_indi++;								
				}
				if($conceptos[$i]['items'][$j]['servicio']['nomb'] == 'Copias Certificadas por Hoja'){
							$cop_cer_hoja++;								
				}
				if($conceptos[$i]['items'][$j]['servicio']['nomb'] == 'Historia Clínica'){
							$hist_clinica++;								
				}
				if($conceptos[$i]['items'][$j]['servicio']['nomb'] == 'Informe Médico'){
							$infor_medico++;								
				}
				if($conceptos[$i]['items'][$j]['servicio']['nomb'] == 'Manualidades'){
							$manual++;								
				}
				if($conceptos[$i]['items'][$j]['servicio']['nomb'] == 'Servicio de Ambulancia'){
							$ambulancia++;								
				}
				if($conceptos[$i]['items'][$j]['servicio']['nomb'] == 'Venta de Humus y otros ( papas, maiz, alfalfa,etc.)'){
							$humus++;								
				}
				
					
			}
		
		}


		$this->SetXY(40,$y);$this->MultiCell(80,5,"CONCEPTO",'1','L');
		$this->SetXY(120,$y);$this->MultiCell(20,5,"CANTIDAD",'1','L');
		$this->SetXY(140,$y);$this->MultiCell(20,5,"MONTO",'1','L');
		$y = $y+5;
		$this->SetFont('Arial','',8);
		$this->SetXY(40,$y);$this->MultiCell(80,5,"Alquiler Cancha Deportiva por Hora: ",'1','L');
		$this->SetXY(120,$y);$this->MultiCell(20,5,"".$cancha,'1','L');
		$t_1 = 0;
		$t_1 = $cancha * 25;
		$this->SetXY(140,$y);$this->MultiCell(20,5,$t_1,'1','L');
		$y = $y+5;
		$this->SetXY(40,$y);$this->MultiCell(80,5,"Búsqueda Historia ",'1','L');
		$this->SetXY(120,$y);$this->MultiCell(20,5,"".$busqueda,'1','L');
		$t_2 = 0;
		$t_2 = $busqueda * 3;
		$this->SetXY(140,$y);$this->MultiCell(20,5,$t_2,'1','L');
		$y = $y+5;
		$this->SetXY(40,$y);$this->MultiCell(80,5,"Carnet de Atención ",'1','L');
		$this->SetXY(120,$y);$this->MultiCell(20,5,"".$carnet,'1','L');
		$t_3 = 0;
		$t_3 = $carnet * 1;
		$this->SetXY(140,$y);$this->MultiCell(20,5,$t_3,'1','L');
		$y = $y+5;
		$this->SetXY(40,$y);$this->MultiCell(80,5,"Certificado Médico ",'1','L');
		$this->SetXY(120,$y);$this->MultiCell(20,5,"".$certificado,'1','L');
		$t_4 = 0;
		$t_4 = $certificado * 20.6;
		$this->SetXY(140,$y);$this->MultiCell(20,5,$t_4,'1','L');
		$y = $y+5;
		$this->SetXY(40,$y);$this->MultiCell(80,5,"Constancia Médica ",'1','L');
		$this->SetXY(120,$y);$this->MultiCell(20,5,"".$constancia,'1','L');
		$t_5 = 0;
		$t_5 = $constancia * 20.6;
		$this->SetXY(140,$y);$this->MultiCell(20,5,$t_5,'1','L');
		$y = $y+5;
		$this->SetXY(40,$y);$this->MultiCell(80,5,"Consulta Especializada - Psicología - Nuevo ",'1','L');
		$this->SetXY(120,$y);$this->MultiCell(20,5,"".$con_es_psic_nuev,'1','L');
		$t_6 = 0;
		$t_6 = $con_es_psic_nuev * 40;
		$this->SetXY(140,$y);$this->MultiCell(20,5,$t_6,'1','L');
		$y = $y+5;
		$this->SetXY(40,$y);$this->MultiCell(80,5,"Consulta Especializada - Terapeuta - Continuador ",'1','L');
		$this->SetXY(120,$y);$this->MultiCell(20,5,"".$con_es_tera_cont,'1','L');
		$t_7 = 0;
		$t_7 = $con_es_tera_cont * 30;
		$this->SetXY(140,$y);$this->MultiCell(20,5,$t_7,'1','L');
		$y = $y+5;
		$this->SetXY(40,$y);$this->MultiCell(80,5,"Consulta Especializada - Terapeuta - Indigente ",'1','L');
		$this->SetXY(120,$y);$this->MultiCell(20,5,"".$con_es_tera_ind,'1','L');
		$t_8 = 0;
		$t_8 = $con_es_tera_cont * 0;
		$this->SetXY(140,$y);$this->MultiCell(20,5,$t_8,'1','L');
		$y = $y+5;
		$this->SetXY(40,$y);$this->MultiCell(80,5,"Consulta Especializada - Terapeuta - Nuevo ",'1','L');
		$this->SetXY(120,$y);$this->MultiCell(20,5,"".$con_es_tera_nuev,'1','L');
		$t_9 = 0;
		$t_9 = $con_es_tera_nuev * 30;
		$this->SetXY(140,$y);$this->MultiCell(20,5,$t_9,'1','L');
		$y = $y+5;
		$this->SetXY(40,$y);$this->MultiCell(80,5,"Consulta Médica - Nuevo",'1','L');
		$this->SetXY(120,$y);$this->MultiCell(20,5,"".$cons_medi_nuevo,'1','L');
		$t_10 = 0;
		$t_10 = $cons_medi_nuevo * 60;
		$this->SetXY(140,$y);$this->MultiCell(20,5,$t_10,'1','L');
		$y = $y+5;
		$this->SetXY(40,$y);$this->MultiCell(80,5,"Consulta Médica Continuador ",'1','L');
		$this->SetXY(120,$y);$this->MultiCell(20,5,"".$cons_medi_conti,'1','L');
		$t_11 = 0;
		$t_11 = $cons_medi_conti * 20;
		$this->SetXY(140,$y);$this->MultiCell(20,5,$t_11,'1','L');
		$y = $y+5;
		$this->SetXY(40,$y);$this->MultiCell(80,5,"Consulta Médica Indigente ",'1','L');
		$this->SetXY(120,$y);$this->MultiCell(20,5,"".$cons_medi_indi,'1','L');
		$t_12 = 0;
		$t_12 = $cons_medi_indi * 0;
		$this->SetXY(140,$y);$this->MultiCell(20,5,$t_12,'1','L');
		$y = $y+5;
		$this->SetXY(40,$y);$this->MultiCell(80,5,"Copias Certificadas por Hoja ",'1','L');
		$this->SetXY(120,$y);$this->MultiCell(20,5,"".$cop_cer_hoja,'1','L');
		$t_13 = 0;
		$t_13 = $cop_cer_hoja * 1.5;
		$this->SetXY(140,$y);$this->MultiCell(20,5,$t_13,'1','L');
		$y = $y+5;
		$this->SetXY(40,$y);$this->MultiCell(80,5,"Informe Médico ",'1','L');
		$this->SetXY(120,$y);$this->MultiCell(20,5,"".$infor_medico,'1','L');
		$t_14 = 0;
		$t_14 = $infor_medico * 20.6;
		$this->SetXY(140,$y);$this->MultiCell(20,5,$t_14,'1','L');
		$y = $y+5;
		$this->SetXY(40,$y);$this->MultiCell(80,5,"Manualidades",'1','L');
		$this->SetXY(120,$y);$this->MultiCell(20,5,"".$manual,'1','L');
		$t_15 = 0;
		$t_15 = $manual * 1;
		$this->SetXY(140,$y);$this->MultiCell(20,5,$t_15,'1','L');
		$y = $y+5;
		$this->SetXY(40,$y);$this->MultiCell(80,5,"Servicio de Ambulancia ",'1','L');
		$this->SetXY(120,$y);$this->MultiCell(20,5,"".$ambulancia,'1','L');
		$t_16 = 0;
		$t_16 = $ambulancia * 120;
		$this->SetXY(140,$y);$this->MultiCell(20,5,$t_16,'1','L');
		$y = $y+5;
		$this->SetXY(40,$y);$this->MultiCell(80,5,"Venta de Productos Agricolas(frutales y otos)",'1','L');
		$this->SetXY(120,$y);$this->MultiCell(20,5,"".$humus,'1','L');
		$t_17 = 0;
		$t_17 = $humus * 5;
		$this->SetXY(140,$y);$this->MultiCell(20,5,$t_17,'1','L');
		$y = $y+5;
		$total = 0;
		$total = $t_1+$t_2+$t_3+$t_4+$t_5+$t_6+$t_7+$t_8+$t_9+$t_10+$t_11+$t_12+$t_13+$t_14+$t_15+$t_15+$t_16+$t_17+$t_18;
		$this->SetXY(40,$y);$this->MultiCell(100,5,'TOTAL','1','C');
		$this->SetXY(140,$y);$this->MultiCell(20,5,$total,'1','L');
		
		
	}

}

$pdf=new repo('P','mm','A4');
$pdf->AliasNbPages();
$pdf->SetMargins(10,10,0); 
$pdf->SetTitle("reporte");
$pdf->SetAutoPageBreak(false,0); 
$pdf->Open();
$pdf->AddPage();
$pdf->Publicar($conceptos);
$pdf->SetLeftMargin(25);
//header('Content-Type: application/pdf; charset=utf-8');
$pdf->Output();

?>


