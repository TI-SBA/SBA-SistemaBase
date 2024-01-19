<?php
global $f;
$f->library('pdf');

class repo extends FPDF
{
	function Header(){
		
		
		$this->SetFont('Arial','B',7);
		$this->SetXY(8,5);$this->MultiCell(50,5,"SOCIEDAD DE BENEFICENCIA PUBLICA DE AREQUIPA",'0','C');
		$this->SetXY(10,5);$this->MultiCell(200,5,"Página: ".$this->PageNo()."/{nb}",'0','R');
		$this->SetFont('Arial','',5);
		$this->SetXY(8,15);$this->MultiCell(50,5,"Sistema SBPA - Módulo de Hospitalizaciones",'0','C');
		
		
	}
	function Publicar($paciente){

		$this->SetFont('Arial','B',12);
		$this->SetXY(10,25);$this->MultiCell(195,5,"REPORTE DE PACIENTES HOSPITALIZADOS - SALUD MENTAL",'0','C');
		$this->SetFont('Arial','B',8);
		$this->SetXY(10,35);$this->MultiCell(175,5,"CATEGORIA: NUEVOS",'0','C');
		
		$tipo= array(
			"C"=>"COMPLETA",
			"P"=>"PARCIAL"
			
		);
		$modalidad= array(
			"D"=>"Dias",
			"M"=>"Meses"
			
		);
		$categoria= array(
			"10"=>"Nuevo",
			"11"=>"Continuador",
			"8"=>"Indigente",
			"9"=>"Privado"
			
		);

		$x=5;
		$y=30;
		$y_ini = $y;
		$page_b = 290;
				
		$intermedio = 0;
		$intensivo = 0;
		$parcial = 0;
		///
		$intermedio_c = 0;
		$intensivo_c = 0;
		$parcial_c = 0;
		///
		$intermedio_p = 0;
		$intensivo_p = 0;
		$parcial_p = 0;
		//
		$intermedio_c_v = 0;
		$intensivo_c_v = 0;
		$parcial_c_v = 0;
		//
		$intermedio_c_m = 0;
		$intensivo_c_m = 0;
		$parcial_c_m = 0;
		//
		$intermedio_p_v = 0;
		$intensivo_p_v = 0;
		$parcial_p_v = 0;
		//
		$intermedio_p_m = 0;
		$intensivo_p_m = 0;
		$parcial_p_m = 0;
		//
		$intermedio_c_v_n = 0;
		$intermedio_c_v_c = 0;
		/*----------------------------------*/
		$c_intermedio = 0;
		$c_intensivo = 0;
		$c_parcial = 0;
		///
		$c_intermedio_c = 0;
		$c_intensivo_c = 0;
		$c_parcial_c = 0;
		///
		$c_intermedio_p = 0;
		$c_intensivo_p = 0;
		$c_parcial_p = 0;
		//
		$c_intermedio_c_v = 0;
		$c_intensivo_c_v = 0;
		$c_parcial_c_v = 0;
		//
		$c_intermedio_c_m = 0;
		$c_intensivo_c_m = 0;
		$c_parcial_c_m = 0;
		//
		$c_intermedio_p_v = 0;
		$c_intensivo_p_v = 0;
		$c_parcial_p_v = 0;
		//
		$c_intermedio_p_m = 0;
		$c_intensivo_p_m = 0;
		$c_parcial_p_m = 0;
		//
		$c_intermedio_c_v_n = 0;
		$c_intermedio_c_v_c = 0;
		/*----------------------------------*/
		/*----------------------------------*/
		$i_intermedio = 0;
		$i_intensivo = 0;
		$i_parcial = 0;
		///
		$i_intermedio_c = 0;
		$i_intensivo_c = 0;
		$i_parcial_c = 0;
		///
		$i_intermedio_p = 0;
		$i_intensivo_p = 0;
		$i_parcial_p = 0;
		//
		$i_intermedio_c_v = 0;
		$i_intensivo_c_v = 0;
		$i_parcial_c_v = 0;
		//
		$i_intermedio_c_m = 0;
		$i_intensivo_c_m = 0;
		$i_parcial_c_m = 0;
		//
		$i_intermedio_p_v = 0;
		$i_intensivo_p_v = 0;
		$i_parcial_p_v = 0;
		//
		$i_intermedio_p_m = 0;
		$i_intensivo_p_m = 0;
		$i_parcial_p_m = 0;
		//
		$i_intermedio_c_v_n = 0;
		$i_intermedio_c_v_c = 0;
		/*----------------------------------*/
		$y=$y+10;
		$this->SetFont('Arial','B',8);
		$yini = $y;

			 
			for($i = 0;$i<count($paciente);$i++){				

				if($paciente[$i]['pabellon'] == 'Intermedio' && $paciente[$i]['modulo']=='MH' && $paciente[$i]['estado'] == 'H' && $paciente[$i]['cate'] == '10'){
					$intermedio ++;
				}
				if($paciente[$i]['pabellon'] == 'Intensivo' && $paciente[$i]['modulo']=='MH' && $paciente[$i]['estado'] == 'H' && $paciente[$i]['cate'] == '10'){
					$intensivo ++;
				}
				if($paciente[$i]['pabellon'] == 'Parcial' && $paciente[$i]['modulo']=='MH' && $paciente[$i]['estado'] == 'H' && $paciente[$i]['cate'] == '10'){
					$parcial ++;
				}
				///
				if($paciente[$i]['pabellon'] == 'Intermedio' && $paciente[$i]['modulo']=='MH' && $paciente[$i]['tipo_hosp'] == 'C' && $paciente[$i]['estado'] == 'H' && $paciente[$i]['cate'] == '10'){
					$intermedio_c ++;
				}
				if($paciente[$i]['pabellon'] == 'Intensivo' && $paciente[$i]['modulo']=='MH' && $paciente[$i]['tipo_hosp'] == 'C' && $paciente[$i]['estado'] == 'H' && $paciente[$i]['cate'] == '10'){
					$intensivo_c ++;
				}
				if($paciente[$i]['pabellon'] == 'Parcial' && $paciente[$i]['modulo']=='MH' && $paciente[$i]['tipo_hosp'] == 'C' && $paciente[$i]['estado'] == 'H' && $paciente[$i]['cate'] == '10'){
					$parcial_c ++;
				}
				///
				if($paciente[$i]['pabellon'] == 'Intermedio' && $paciente[$i]['modulo']=='MH' && $paciente[$i]['tipo_hosp'] == 'P' && $paciente[$i]['estado'] == 'H' && $paciente[$i]['cate'] == '10'){
					$intermedio_p ++;
				}
				if($paciente[$i]['pabellon'] == 'Intensivo' && $paciente[$i]['modulo']=='MH' && $paciente[$i]['tipo_hosp'] == 'P' && $paciente[$i]['estado'] == 'H' && $paciente[$i]['cate'] == '10'){
					$intensivo_p ++;
				}
				if($paciente[$i]['pabellon'] == 'Parcial' && $paciente[$i]['modulo']=='MH' && $paciente[$i]['tipo_hosp'] == 'P' && $paciente[$i]['estado'] == 'H' && $paciente[$i]['cate'] == '10'){
					$parcial_p ++;
				}
				//
				if($paciente[$i]['pabellon'] == 'Intermedio' && $paciente[$i]['modulo']=='MH' && $paciente[$i]['tipo_hosp'] == 'C' && $paciente[$i]['estado'] == 'H' && $paciente[$i]['sala'] == 'V' && $paciente[$i]['cate'] == '10'){
					$intermedio_c_v++;
				}
				if($paciente[$i]['pabellon'] == 'Intermedio' && $paciente[$i]['modulo']=='MH' && $paciente[$i]['tipo_hosp'] == 'C' && $paciente[$i]['estado'] == 'H' && $paciente[$i]['sala'] == 'M' && $paciente[$i]['cate'] == '10'){
					$intermedio_c_m++;
				}
				//
				if($paciente[$i]['pabellon'] == 'Intensivo' && $paciente[$i]['modulo']=='MH' && $paciente[$i]['tipo_hosp'] == 'C' && $paciente[$i]['estado'] == 'H' && $paciente[$i]['sala'] == 'V' && $paciente[$i]['cate'] == '10'){
					$intensivo_c_v++;
				}
				if($paciente[$i]['pabellon'] == 'Intensivo' && $paciente[$i]['modulo']=='MH' && $paciente[$i]['tipo_hosp'] == 'C' && $paciente[$i]['estado'] == 'H' && $paciente[$i]['sala'] == 'M' && $paciente[$i]['cate'] == '10'){
					$intensivo_c_m++;
				}
				//
				if($paciente[$i]['pabellon'] == 'Parcial' && $paciente[$i]['modulo']=='MH' && $paciente[$i]['tipo_hosp'] == 'P' && $paciente[$i]['estado'] == 'H' && $paciente[$i]['sala'] == 'V' && $paciente[$i]['cate'] == '10'){
					$parcial_p_v++;
				}
				if($paciente[$i]['pabellon'] == 'Parcial' && $paciente[$i]['modulo']=='MH' && $paciente[$i]['tipo_hosp'] == 'P' && $paciente[$i]['estado'] == 'H' && $paciente[$i]['sala'] == 'M' && $paciente[$i]['cate'] == '10'){
					$parcial_p_m++;
				}
				
			}

			$this->SetXY(20,$y);$this->MultiCell(150,9,"PABELLON",'1','C');
			$this->SetXY(170,$y);$this->MultiCell(20,54,"TOTAL",'1','C');
			$y=$y+9;
			$this->SetXY(20,$y);$this->MultiCell(50,9,"INTERMEDIO",'1','C');
			$this->SetXY(70,$y);$this->MultiCell(50,9,"INTENSIVO",'1','C');
			$this->SetXY(120,$y);$this->MultiCell(50,9,"PARCIAL",'1','C');
			$y=$y+9;
			$this->SetFont('Arial','',8);
			$this->SetXY(20,$y);$this->MultiCell(50,9,$intermedio,'1','C');
			$this->SetXY(70,$y);$this->MultiCell(50,9,$intensivo,'1','C');
			$this->SetXY(120,$y);$this->MultiCell(50,9,$parcial,'1','C');
			$y=$y+9;
			$this->SetFont('Arial','B',8);
			$this->SetXY(70,$y);$this->MultiCell(25,9,"COMPLETA",'1','C');
			$this->SetXY(95,$y);$this->MultiCell(25,9,"PARCIAL",'1','C');
			//
			$this->SetXY(120,$y);$this->MultiCell(25,9,"COMPLETA",'1','C');
			$this->SetXY(145,$y);$this->MultiCell(25,9,"PARCIAL",'1','C');
			//
			$this->SetXY(20,$y);$this->MultiCell(25,9,"COMPLETA",'1','C');
			$this->SetXY(45,$y);$this->MultiCell(25,9,"PARCIAL",'1','C');
			$y=$y+9;
			$this->SetFont('Arial','',8);
			$this->SetXY(20,$y);$this->MultiCell(25,9,$intermedio_c,'1','C');
			$this->SetXY(45,$y);$this->MultiCell(25,9,$intermedio_p,'1','C');
			//
			$this->SetXY(70,$y);$this->MultiCell(25,9,$intensivo_c,'1','C');
			$this->SetXY(95,$y);$this->MultiCell(25,9,$intensivo_p,'1','C');
			//
			$this->SetXY(120,$y);$this->MultiCell(25,9,$parcial_c,'1','C');
			$this->SetXY(145,$y);$this->MultiCell(25,9,$parcial_p,'1','C');
			$y=$y+9;
			$this->SetFont('Arial','B',8);
			$this->SetXY(20,$y);$this->MultiCell(12.5,9,"V",'1','C');
			$this->SetXY(32.5,$y);$this->MultiCell(12.5,9,"M",'1','C');
			//
			$this->SetXY(45,$y);$this->MultiCell(12.5,9,"V",'1','C');
			$this->SetXY(57.5,$y);$this->MultiCell(12.5,9,"M",'1','C');
			//
			$this->SetXY(70,$y);$this->MultiCell(12.5,9,"V",'1','C');
			$this->SetXY(82.5,$y);$this->MultiCell(12.5,9,"M",'1','C');
			//
			$this->SetXY(95,$y);$this->MultiCell(12.5,9,"V",'1','C');
			$this->SetXY(107.5,$y);$this->MultiCell(12.5,9,"M",'1','C');
			//
			$this->SetXY(120,$y);$this->MultiCell(12.5,9,"V",'1','C');
			$this->SetXY(132.5,$y);$this->MultiCell(12.5,9,"M",'1','C');
			//
			$this->SetXY(145,$y);$this->MultiCell(12.5,9,"V",'1','C');
			$this->SetXY(157.5,$y);$this->MultiCell(12.5,9,"M",'1','C');
			$y=$y+9;
			$this->SetFont('Arial','',8);
			$this->SetXY(20,$y);$this->MultiCell(12.5,9,$intermedio_c_v,'1','C');
			$this->SetXY(32.5,$y);$this->MultiCell(12.5,9,$intermedio_c_m,'1','C');
			//
			$this->SetXY(45,$y);$this->MultiCell(12.5,9,"0",'1','C');
			$this->SetXY(57.5,$y);$this->MultiCell(12.5,9,"0",'1','C');
			//
			$this->SetXY(70,$y);$this->MultiCell(12.5,9,$intensivo_c_v,'1','C');
			$this->SetXY(82.5,$y);$this->MultiCell(12.5,9,$intensivo_c_m,'1','C');
			//
			$this->SetXY(95,$y);$this->MultiCell(12.5,9,"0",'1','C');
			$this->SetXY(107.5,$y);$this->MultiCell(12.5,9,"0",'1','C');
			//
			$this->SetXY(120,$y);$this->MultiCell(12.5,9,"0",'1','C');
			$this->SetXY(132.5,$y);$this->MultiCell(12.5,9,"0",'1','C');
			//
			$this->SetXY(145,$y);$this->MultiCell(12.5,9,$parcial_p_v,'1','C');
			$this->SetXY(157.5,$y);$this->MultiCell(12.5,9,$parcial_p_m,'1','C');
			$this->SetFont('Arial','B',8);
			$this->SetXY(170,$y);$this->MultiCell(20,9,$intermedio+$intensivo+$parcial,'1','C');
			
		$this->SetFont('Arial','B',8);
		$this->SetXY(10,110);$this->MultiCell(165,5,"CATEGORIA: CONTINUADORES",'0','C');
		$y=110;
		$y=$y+5;
		$this->SetFont('Arial','B',8);
		$yini = $y;

			 
			for($i = 0;$i<count($paciente);$i++){				

				if($paciente[$i]['pabellon'] == 'Intermedio' && $paciente[$i]['modulo']=='MH' && $paciente[$i]['estado'] == 'H' && $paciente[$i]['cate'] == '11'){
					$c_intermedio ++;
				}
				if($paciente[$i]['pabellon'] == 'Intensivo' && $paciente[$i]['modulo']=='MH' && $paciente[$i]['estado'] == 'H' && $paciente[$i]['cate'] == '11'){
					$c_intensivo ++;
				}
				if($paciente[$i]['pabellon'] == 'Parcial' && $paciente[$i]['modulo']=='MH' && $paciente[$i]['estado'] == 'H' && $paciente[$i]['cate'] == '11'){
					$c_parcial ++;
				}
				///
				if($paciente[$i]['pabellon'] == 'Intermedio' && $paciente[$i]['modulo']=='MH' && $paciente[$i]['tipo_hosp'] == 'C' && $paciente[$i]['estado'] == 'H' && $paciente[$i]['cate'] == '11'){
					$c_intermedio_c ++;
				}
				if($paciente[$i]['pabellon'] == 'Intensivo' && $paciente[$i]['modulo']=='MH' && $paciente[$i]['tipo_hosp'] == 'C' && $paciente[$i]['estado'] == 'H' && $paciente[$i]['cate'] == '11'){
					$c_intensivo_c ++;
				}
				if($paciente[$i]['pabellon'] == 'Parcial' && $paciente[$i]['modulo']=='MH' && $paciente[$i]['tipo_hosp'] == 'C' && $paciente[$i]['estado'] == 'H' && $paciente[$i]['cate'] == '11'){
					$c_parcial_c ++;
				}
				///
				if($paciente[$i]['pabellon'] == 'Intermedio' && $paciente[$i]['modulo']=='MH' && $paciente[$i]['tipo_hosp'] == 'P' && $paciente[$i]['estado'] == 'H' && $paciente[$i]['cate'] == '11'){
					$c_intermedio_p ++;
				}
				if($paciente[$i]['pabellon'] == 'Intensivo' && $paciente[$i]['modulo']=='MH' && $paciente[$i]['tipo_hosp'] == 'P' && $paciente[$i]['estado'] == 'H' && $paciente[$i]['cate'] == '11'){
					$c_intensivo_p ++;
				}
				if($paciente[$i]['pabellon'] == 'Parcial' && $paciente[$i]['modulo']=='MH' && $paciente[$i]['tipo_hosp'] == 'P' && $paciente[$i]['estado'] == 'H' && $paciente[$i]['cate'] == '11'){
					$c_parcial_p ++;
				}
				//
				if($paciente[$i]['pabellon'] == 'Intermedio' && $paciente[$i]['modulo']=='MH' && $paciente[$i]['tipo_hosp'] == 'C' && $paciente[$i]['estado'] == 'H' && $paciente[$i]['sala'] == 'V' && $paciente[$i]['cate'] == '11'){
					$c_intermedio_c_v++;
				}
				if($paciente[$i]['pabellon'] == 'Intermedio' && $paciente[$i]['modulo']=='MH' && $paciente[$i]['tipo_hosp'] == 'C' && $paciente[$i]['estado'] == 'H' && $paciente[$i]['sala'] == 'M' && $paciente[$i]['cate'] == '11'){
					$c_intermedio_c_m++;
				}
				//
				if($paciente[$i]['pabellon'] == 'Intensivo' && $paciente[$i]['modulo']=='MH' && $paciente[$i]['tipo_hosp'] == 'C' && $paciente[$i]['estado'] == 'H' && $paciente[$i]['sala'] == 'V' && $paciente[$i]['cate'] == '11'){
					$c_intensivo_c_v++;
				}
				if($paciente[$i]['pabellon'] == 'Intensivo' && $paciente[$i]['modulo']=='MH' && $paciente[$i]['tipo_hosp'] == 'C' && $paciente[$i]['estado'] == 'H' && $paciente[$i]['sala'] == 'M' && $paciente[$i]['cate'] == '11'){
					$c_intensivo_c_m++;
				}
				//
				if($paciente[$i]['pabellon'] == 'Parcial' && $paciente[$i]['modulo']=='MH' && $paciente[$i]['tipo_hosp'] == 'P' && $paciente[$i]['estado'] == 'H' && $paciente[$i]['sala'] == 'V' && $paciente[$i]['cate'] == '11'){
					$c_parcial_p_v++;
				}
				if($paciente[$i]['pabellon'] == 'Parcial' && $paciente[$i]['modulo']=='MH' && $paciente[$i]['tipo_hosp'] == 'P' && $paciente[$i]['estado'] == 'H' && $paciente[$i]['sala'] == 'M' && $paciente[$i]['cate'] == '11'){
					$c_parcial_p_m++;
				}
				
			}

			$this->SetXY(20,$y);$this->MultiCell(150,9,"PABELLON",'1','C');
			$this->SetXY(170,$y);$this->MultiCell(20,54,"TOTAL",'1','C');
			$y=$y+9;
			$this->SetXY(20,$y);$this->MultiCell(50,9,"INTERMEDIO",'1','C');
			$this->SetXY(70,$y);$this->MultiCell(50,9,"INTENSIVO",'1','C');
			$this->SetXY(120,$y);$this->MultiCell(50,9,"PARCIAL",'1','C');
			$y=$y+9;
			$this->SetFont('Arial','',8);
			$this->SetXY(20,$y);$this->MultiCell(50,9,$c_intermedio,'1','C');
			$this->SetXY(70,$y);$this->MultiCell(50,9,$c_intensivo,'1','C');
			$this->SetXY(120,$y);$this->MultiCell(50,9,$c_parcial,'1','C');
			$y=$y+9;
			$this->SetFont('Arial','B',8);
			$this->SetXY(70,$y);$this->MultiCell(25,9,"COMPLETA",'1','C');
			$this->SetXY(95,$y);$this->MultiCell(25,9,"PARCIAL",'1','C');
			//
			$this->SetXY(120,$y);$this->MultiCell(25,9,"COMPLETA",'1','C');
			$this->SetXY(145,$y);$this->MultiCell(25,9,"PARCIAL",'1','C');
			//
			$this->SetXY(20,$y);$this->MultiCell(25,9,"COMPLETA",'1','C');
			$this->SetXY(45,$y);$this->MultiCell(25,9,"PARCIAL",'1','C');
			$y=$y+9;
			$this->SetFont('Arial','',8);
			$this->SetXY(20,$y);$this->MultiCell(25,9,$c_intermedio_c,'1','C');
			$this->SetXY(45,$y);$this->MultiCell(25,9,$c_intermedio_p,'1','C');
			//
			$this->SetXY(70,$y);$this->MultiCell(25,9,$c_intensivo_c,'1','C');
			$this->SetXY(95,$y);$this->MultiCell(25,9,$c_intensivo_p,'1','C');
			//
			$this->SetXY(120,$y);$this->MultiCell(25,9,$c_parcial_c,'1','C');
			$this->SetXY(145,$y);$this->MultiCell(25,9,$c_parcial_p,'1','C');
			$y=$y+9;
			$this->SetFont('Arial','B',8);
			$this->SetXY(20,$y);$this->MultiCell(12.5,9,"V",'1','C');
			$this->SetXY(32.5,$y);$this->MultiCell(12.5,9,"M",'1','C');
			//
			$this->SetXY(45,$y);$this->MultiCell(12.5,9,"V",'1','C');
			$this->SetXY(57.5,$y);$this->MultiCell(12.5,9,"M",'1','C');
			//
			$this->SetXY(70,$y);$this->MultiCell(12.5,9,"V",'1','C');
			$this->SetXY(82.5,$y);$this->MultiCell(12.5,9,"M",'1','C');
			//
			$this->SetXY(95,$y);$this->MultiCell(12.5,9,"V",'1','C');
			$this->SetXY(107.5,$y);$this->MultiCell(12.5,9,"M",'1','C');
			//
			$this->SetXY(120,$y);$this->MultiCell(12.5,9,"V",'1','C');
			$this->SetXY(132.5,$y);$this->MultiCell(12.5,9,"M",'1','C');
			//
			$this->SetXY(145,$y);$this->MultiCell(12.5,9,"V",'1','C');
			$this->SetXY(157.5,$y);$this->MultiCell(12.5,9,"M",'1','C');
			$y=$y+9;
			$this->SetFont('Arial','',8);
			$this->SetXY(20,$y);$this->MultiCell(12.5,9,$c_intermedio_c_v,'1','C');
			$this->SetXY(32.5,$y);$this->MultiCell(12.5,9,$c_intermedio_c_m,'1','C');
			//
			$this->SetXY(45,$y);$this->MultiCell(12.5,9,"0",'1','C');
			$this->SetXY(57.5,$y);$this->MultiCell(12.5,9,"0",'1','C');
			//
			$this->SetXY(70,$y);$this->MultiCell(12.5,9,$c_intensivo_c_v,'1','C');
			$this->SetXY(82.5,$y);$this->MultiCell(12.5,9,$c_intensivo_c_m,'1','C');
			//
			$this->SetXY(95,$y);$this->MultiCell(12.5,9,"0",'1','C');
			$this->SetXY(107.5,$y);$this->MultiCell(12.5,9,"0",'1','C');
			//
			$this->SetXY(120,$y);$this->MultiCell(12.5,9,"0",'1','C');
			$this->SetXY(132.5,$y);$this->MultiCell(12.5,9,"0",'1','C');
			//
			$this->SetXY(145,$y);$this->MultiCell(12.5,9,$c_parcial_p_v,'1','C');
			$this->SetXY(157.5,$y);$this->MultiCell(12.5,9,$c_parcial_p_m,'1','C');
			$this->SetFont('Arial','B',8);
			$this->SetXY(170,$y);$this->MultiCell(20,9,$c_intermedio+$c_intensivo+$c_parcial,'1','C');

		$this->SetFont('Arial','B',8);
		$this->SetXY(10,185);$this->MultiCell(175,5,"CATEGORIA: INDIGENTES",'0','C');
		$y=185;
		$y=$y+5;
		$this->SetFont('Arial','B',8);
		$yini = $y;

		for($i = 0;$i<count($paciente);$i++){				

				if($paciente[$i]['pabellon'] == 'Intermedio' && $paciente[$i]['modulo']=='MH' && $paciente[$i]['estado'] == 'H' && $paciente[$i]['cate'] == '8'){
					$i_intermedio ++;
				}
				if($paciente[$i]['pabellon'] == 'Intensivo' && $paciente[$i]['modulo']=='MH' && $paciente[$i]['estado'] == 'H' && $paciente[$i]['cate'] == '8'){
					$i_intensivo ++;
				}
				if($paciente[$i]['pabellon'] == 'Parcial' && $paciente[$i]['modulo']=='MH' && $paciente[$i]['estado'] == 'H' && $paciente[$i]['cate'] == '8'){
					$i_parcial ++;
				}
				///
				if($paciente[$i]['pabellon'] == 'Intermedio' && $paciente[$i]['modulo']=='MH' && $paciente[$i]['tipo_hosp'] == 'C' && $paciente[$i]['estado'] == 'H' && $paciente[$i]['cate'] == '8'){
					$i_intermedio_c ++;
				}
				if($paciente[$i]['pabellon'] == 'Intensivo' && $paciente[$i]['modulo']=='MH' && $paciente[$i]['tipo_hosp'] == 'C' && $paciente[$i]['estado'] == 'H' && $paciente[$i]['cate'] == '8'){
					$i_intensivo_c ++;
				}
				if($paciente[$i]['pabellon'] == 'Parcial' && $paciente[$i]['modulo']=='MH' && $paciente[$i]['tipo_hosp'] == 'C' && $paciente[$i]['estado'] == 'H' && $paciente[$i]['cate'] == '8'){
					$i_parcial_c ++;
				}
				///
				if($paciente[$i]['pabellon'] == 'Intermedio' && $paciente[$i]['modulo']=='MH' && $paciente[$i]['tipo_hosp'] == 'P' && $paciente[$i]['estado'] == 'H' && $paciente[$i]['cate'] == '8'){
					$i_intermedio_p ++;
				}
				if($paciente[$i]['pabellon'] == 'Intensivo' && $paciente[$i]['modulo']=='MH' && $paciente[$i]['tipo_hosp'] == 'P' && $paciente[$i]['estado'] == 'H' && $paciente[$i]['cate'] == '8'){
					$i_intensivo_p ++;
				}
				if($paciente[$i]['pabellon'] == 'Parcial' && $paciente[$i]['modulo']=='MH' && $paciente[$i]['tipo_hosp'] == 'P' && $paciente[$i]['estado'] == 'H' && $paciente[$i]['cate'] == '8'){
					$i_parcial_p ++;
				}
				//
				if($paciente[$i]['pabellon'] == 'Intermedio' && $paciente[$i]['modulo']=='MH' && $paciente[$i]['tipo_hosp'] == 'C' && $paciente[$i]['estado'] == 'H' && $paciente[$i]['sala'] == 'V' && $paciente[$i]['cate'] == '8'){
					$i_intermedio_c_v++;
				}
				if($paciente[$i]['pabellon'] == 'Intermedio' && $paciente[$i]['modulo']=='MH' && $paciente[$i]['tipo_hosp'] == 'C' && $paciente[$i]['estado'] == 'H' && $paciente[$i]['sala'] == 'M' && $paciente[$i]['cate'] == '8'){
					$i_intermedio_c_m++;
				}
				//
				if($paciente[$i]['pabellon'] == 'Intensivo' && $paciente[$i]['modulo']=='MH' && $paciente[$i]['tipo_hosp'] == 'C' && $paciente[$i]['estado'] == 'H' && $paciente[$i]['sala'] == 'V' && $paciente[$i]['cate'] == '8'){
					$i_intensivo_c_v++;
				}
				if($paciente[$i]['pabellon'] == 'Intensivo' && $paciente[$i]['modulo']=='MH' && $paciente[$i]['tipo_hosp'] == 'C' && $paciente[$i]['estado'] == 'H' && $paciente[$i]['sala'] == 'M' && $paciente[$i]['cate'] == '8'){
					$i_intensivo_c_m++;
				}
				//
				if($paciente[$i]['pabellon'] == 'Parcial' && $paciente[$i]['modulo']=='MH' && $paciente[$i]['tipo_hosp'] == 'P' && $paciente[$i]['estado'] == 'H' && $paciente[$i]['sala'] == 'V' && $paciente[$i]['cate'] == '8'){
					$i_parcial_p_v++;
				}
				if($paciente[$i]['pabellon'] == 'Parcial' && $paciente[$i]['modulo']=='MH' && $paciente[$i]['tipo_hosp'] == 'P' && $paciente[$i]['estado'] == 'H' && $paciente[$i]['sala'] == 'M' && $paciente[$i]['cate'] == '8'){
					$i_parcial_p_m++;
				}
				
			}

			$this->SetXY(20,$y);$this->MultiCell(150,9,"PABELLON",'1','C');
			$this->SetXY(170,$y);$this->MultiCell(20,54,"TOTAL",'1','C');
			$y=$y+9;
			$this->SetXY(20,$y);$this->MultiCell(50,9,"INTERMEDIO",'1','C');
			$this->SetXY(70,$y);$this->MultiCell(50,9,"INTENSIVO",'1','C');
			$this->SetXY(120,$y);$this->MultiCell(50,9,"PARCIAL",'1','C');

			$y=$y+9;
			$this->SetFont('Arial','',8);
			$this->SetXY(20,$y);$this->MultiCell(50,9,$i_intermedio,'1','C');
			$this->SetXY(70,$y);$this->MultiCell(50,9,$i_intensivo,'1','C');
			$this->SetXY(120,$y);$this->MultiCell(50,9,$i_parcial,'1','C');
			$y=$y+9;
			$this->SetFont('Arial','B',8);
			$this->SetXY(70,$y);$this->MultiCell(25,9,"COMPLETA",'1','C');
			$this->SetXY(95,$y);$this->MultiCell(25,9,"PARCIAL",'1','C');
			//
			$this->SetXY(120,$y);$this->MultiCell(25,9,"COMPLETA",'1','C');
			$this->SetXY(145,$y);$this->MultiCell(25,9,"PARCIAL",'1','C');
			//
			$this->SetXY(20,$y);$this->MultiCell(25,9,"COMPLETA",'1','C');
			$this->SetXY(45,$y);$this->MultiCell(25,9,"PARCIAL",'1','C');
			$y=$y+9;
			$this->SetFont('Arial','',8);
			$this->SetXY(20,$y);$this->MultiCell(25,9,$i_intermedio_c,'1','C');
			$this->SetXY(45,$y);$this->MultiCell(25,9,$i_intermedio_p,'1','C');
			//
			$this->SetXY(70,$y);$this->MultiCell(25,9,$i_intensivo_c,'1','C');
			$this->SetXY(95,$y);$this->MultiCell(25,9,$i_intensivo_p,'1','C');
			//
			$this->SetXY(120,$y);$this->MultiCell(25,9,$i_parcial_c,'1','C');
			$this->SetXY(145,$y);$this->MultiCell(25,9,$i_parcial_p,'1','C');
			$y=$y+9;
			$this->SetFont('Arial','B',8);
			$this->SetXY(20,$y);$this->MultiCell(12.5,9,"V",'1','C');
			$this->SetXY(32.5,$y);$this->MultiCell(12.5,9,"M",'1','C');
			//
			$this->SetXY(45,$y);$this->MultiCell(12.5,9,"V",'1','C');
			$this->SetXY(57.5,$y);$this->MultiCell(12.5,9,"M",'1','C');
			//
			$this->SetXY(70,$y);$this->MultiCell(12.5,9,"V",'1','C');
			$this->SetXY(82.5,$y);$this->MultiCell(12.5,9,"M",'1','C');
			//
			$this->SetXY(95,$y);$this->MultiCell(12.5,9,"V",'1','C');
			$this->SetXY(107.5,$y);$this->MultiCell(12.5,9,"M",'1','C');
			//
			$this->SetXY(120,$y);$this->MultiCell(12.5,9,"V",'1','C');
			$this->SetXY(132.5,$y);$this->MultiCell(12.5,9,"M",'1','C');
			//
			$this->SetXY(145,$y);$this->MultiCell(12.5,9,"V",'1','C');
			$this->SetXY(157.5,$y);$this->MultiCell(12.5,9,"M",'1','C');
			$y=$y+9;
			$this->SetFont('Arial','',8);
			$this->SetXY(20,$y);$this->MultiCell(12.5,9,$i_intermedio_c_v,'1','C');
			$this->SetXY(32.5,$y);$this->MultiCell(12.5,9,$i_intermedio_c_m,'1','C');
			//
			$this->SetXY(45,$y);$this->MultiCell(12.5,9,"0",'1','C');
			$this->SetXY(57.5,$y);$this->MultiCell(12.5,9,"0",'1','C');
			//
			$this->SetXY(70,$y);$this->MultiCell(12.5,9,$i_intensivo_c_v,'1','C');
			$this->SetXY(82.5,$y);$this->MultiCell(12.5,9,$i_intensivo_c_m,'1','C');
			//
			$this->SetXY(95,$y);$this->MultiCell(12.5,9,"0",'1','C');
			$this->SetXY(107.5,$y);$this->MultiCell(12.5,9,"0",'1','C');
			//
			$this->SetXY(120,$y);$this->MultiCell(12.5,9,"0",'1','C');
			$this->SetXY(132.5,$y);$this->MultiCell(12.5,9,"0",'1','C');
			//
			$this->SetXY(145,$y);$this->MultiCell(12.5,9,$i_parcial_p_v,'1','C');
			$this->SetXY(157.5,$y);$this->MultiCell(12.5,9,$i_parcial_p_m,'1','C');
			$this->SetFont('Arial','B',8);
			$this->SetXY(170,$y);$this->MultiCell(20,9,$i_intermedio+$i_intensivo+$i_parcial,'1','C');

		$y=$y+15;
		$this->SetFont('Arial','B',12);
		$this->SetXY(19,$y);$this->MultiCell(50,5,"RESUMEN:",'0','L');
		$y=$y+5;
		$this->SetFont('Arial','',10);
		$this->SetXY(19,$y);$this->MultiCell(60,5,"Total de Pacientes Nuevos: ",'0','L');
		$this->SetXY(79,$y);$this->MultiCell(50,5,$intermedio+$intensivo+$parcial,'0','L');
		$y=$y+5;
		$this->SetFont('Arial','',10);
		$this->SetXY(19,$y);$this->MultiCell(60,5,"Total de Pacientes Continuadores: ",'0','L');
		$this->SetXY(79,$y);$this->MultiCell(50,5,$c_intermedio+$c_intensivo+$c_parcial,'0','L');
		$y=$y+5;
		$this->SetFont('Arial','',10);
		$this->SetXY(19,$y);$this->MultiCell(60,5,"Total de Pacientes Indigentes: ",'0','L');
		$this->SetXY(79,$y);$this->MultiCell(50,5,$i_intermedio+$i_intensivo+$i_parcial,'0','L');
		$y=$y+5;
		$this->SetFont('Arial','B',10);
		$this->SetXY(19,$y);$this->MultiCell(60,5,"Total: ",'0','L');
		$this->SetXY(79,$y);$this->MultiCell(50,5,$intermedio+$intensivo+$parcial+$c_intermedio+$c_intensivo+$c_parcial+$i_intermedio+$i_intensivo+$i_parcial,'0','L');



		$this->AddPage();

		$this->SetFont('Arial','B',12);
		$this->SetXY(10,25);$this->MultiCell(195,5,"REPORTE DE PACIENTES HOSPITALIZADOS - ADICCIONES",'0','C');
		$this->SetFont('Arial','B',8);
		$this->SetXY(10,35);$this->MultiCell(175,5,"CATEGORIA: NUEVOS",'0','C');
		
		$tipo= array(
			"C"=>"COMPLETA",
			"P"=>"PARCIAL"
			
		);
		$modalidad= array(
			"D"=>"Dias",
			"M"=>"Meses"
			
		);
		$categoria= array(
			"10"=>"Nuevo",
			"11"=>"Continuador",
			"8"=>"Indigente",
			"9"=>"Privado"
			
		);

		$x=5;
		$y=36;
		$y_ini = $y;
		$page_b = 275;
				
		$intermedio = 0;
		$intensivo = 0;
		$parcial = 0;
		///
		$intermedio_c = 0;
		$intensivo_c = 0;
		$parcial_c = 0;
		///
		$intermedio_p = 0;
		$intensivo_p = 0;
		$parcial_p = 0;
		//
		$intermedio_c_v = 0;
		$intensivo_c_v = 0;
		$parcial_c_v = 0;
		//
		$intermedio_c_m = 0;
		$intensivo_c_m = 0;
		$parcial_c_m = 0;
		//
		$intermedio_p_v = 0;
		$intensivo_p_v = 0;
		$parcial_p_v = 0;
		//
		$intermedio_p_m = 0;
		$intensivo_p_m = 0;
		$parcial_p_m = 0;
		//
		$intermedio_c_v_n = 0;
		$intermedio_c_v_c = 0;
		/*----------------------------------*/
		$c_intermedio = 0;
		$c_intensivo = 0;
		$c_parcial = 0;
		///
		$c_intermedio_c = 0;
		$c_intensivo_c = 0;
		$c_parcial_c = 0;
		///
		$c_intermedio_p = 0;
		$c_intensivo_p = 0;
		$c_parcial_p = 0;
		//
		$c_intermedio_c_v = 0;
		$c_intensivo_c_v = 0;
		$c_parcial_c_v = 0;
		//
		$c_intermedio_c_m = 0;
		$c_intensivo_c_m = 0;
		$c_parcial_c_m = 0;
		//
		$c_intermedio_p_v = 0;
		$c_intensivo_p_v = 0;
		$c_parcial_p_v = 0;
		//
		$c_intermedio_p_m = 0;
		$c_intensivo_p_m = 0;
		$c_parcial_p_m = 0;
		//
		$c_intermedio_c_v_n = 0;
		$c_intermedio_c_v_c = 0;
		/*----------------------------------*/
		/*----------------------------------*/
		$i_intermedio = 0;
		$i_intensivo = 0;
		$i_parcial = 0;
		///
		$i_intermedio_c = 0;
		$i_intensivo_c = 0;
		$i_parcial_c = 0;
		///
		$i_intermedio_p = 0;
		$i_intensivo_p = 0;
		$i_parcial_p = 0;
		//
		$i_intermedio_c_v = 0;
		$i_intensivo_c_v = 0;
		$i_parcial_c_v = 0;
		//
		$i_intermedio_c_m = 0;
		$i_intensivo_c_m = 0;
		$i_parcial_c_m = 0;
		//
		$i_intermedio_p_v = 0;
		$i_intensivo_p_v = 0;
		$i_parcial_p_v = 0;
		//
		$i_intermedio_p_m = 0;
		$i_intensivo_p_m = 0;
		$i_parcial_p_m = 0;
		//
		$i_intermedio_c_v_n = 0;
		$i_intermedio_c_v_c = 0;
		/*----------------------------------*/
		$y=$y+5;
		$this->SetFont('Arial','B',8);
		$yini = $y;

			 
			for($i = 0;$i<count($paciente);$i++){				

				if($paciente[$i]['pabellon'] == 'Intermedio' && $paciente[$i]['modulo']=='AD' && $paciente[$i]['estado'] == 'H' && $paciente[$i]['cate'] == '10'){
					$intermedio ++;
				}
				if($paciente[$i]['pabellon'] == 'Intensivo' && $paciente[$i]['modulo']=='AD' && $paciente[$i]['estado'] == 'H' && $paciente[$i]['cate'] == '10'){
					$intensivo ++;
				}
				if($paciente[$i]['pabellon'] == 'Parcial' && $paciente[$i]['modulo']=='AD' && $paciente[$i]['estado'] == 'H' && $paciente[$i]['cate'] == '10'){
					$parcial ++;
				}
				///
				if($paciente[$i]['pabellon'] == 'Intermedio' && $paciente[$i]['modulo']=='AD' && $paciente[$i]['tipo_hosp'] == 'C' && $paciente[$i]['estado'] == 'H' && $paciente[$i]['cate'] == '10'){
					$intermedio_c ++;
				}
				if($paciente[$i]['pabellon'] == 'Intensivo' && $paciente[$i]['modulo']=='AD' && $paciente[$i]['tipo_hosp'] == 'C' && $paciente[$i]['estado'] == 'H' && $paciente[$i]['cate'] == '10'){
					$intensivo_c ++;
				}
				if($paciente[$i]['pabellon'] == 'Parcial' && $paciente[$i]['modulo']=='AD' && $paciente[$i]['tipo_hosp'] == 'C' && $paciente[$i]['estado'] == 'H' && $paciente[$i]['cate'] == '10'){
					$parcial_c ++;
				}
				///
				if($paciente[$i]['pabellon'] == 'Intermedio' && $paciente[$i]['modulo']=='AD' && $paciente[$i]['tipo_hosp'] == 'P' && $paciente[$i]['estado'] == 'H' && $paciente[$i]['cate'] == '10'){
					$intermedio_p ++;
				}
				if($paciente[$i]['pabellon'] == 'Intensivo' && $paciente[$i]['modulo']=='AD' && $paciente[$i]['tipo_hosp'] == 'P' && $paciente[$i]['estado'] == 'H' && $paciente[$i]['cate'] == '10'){
					$intensivo_p ++;
				}
				if($paciente[$i]['pabellon'] == 'Parcial' && $paciente[$i]['modulo']=='AD' && $paciente[$i]['tipo_hosp'] == 'P' && $paciente[$i]['estado'] == 'H' && $paciente[$i]['cate'] == '10'){
					$parcial_p ++;
				}
				//
				if($paciente[$i]['pabellon'] == 'Intermedio' && $paciente[$i]['modulo']=='AD' && $paciente[$i]['tipo_hosp'] == 'C' && $paciente[$i]['estado'] == 'H' && $paciente[$i]['sala'] == 'V' && $paciente[$i]['cate'] == '10'){
					$intermedio_c_v++;
				}
				if($paciente[$i]['pabellon'] == 'Intermedio' && $paciente[$i]['modulo']=='AD' && $paciente[$i]['tipo_hosp'] == 'C' && $paciente[$i]['estado'] == 'H' && $paciente[$i]['sala'] == 'M' && $paciente[$i]['cate'] == '10'){
					$intermedio_c_m++;
				}
				//
				if($paciente[$i]['pabellon'] == 'Intensivo' && $paciente[$i]['modulo']=='AD' && $paciente[$i]['tipo_hosp'] == 'C' && $paciente[$i]['estado'] == 'H' && $paciente[$i]['sala'] == 'V' && $paciente[$i]['cate'] == '10'){
					$intensivo_c_v++;
				}
				if($paciente[$i]['pabellon'] == 'Intensivo' && $paciente[$i]['modulo']=='AD' && $paciente[$i]['tipo_hosp'] == 'C' && $paciente[$i]['estado'] == 'H' && $paciente[$i]['sala'] == 'M' && $paciente[$i]['cate'] == '10'){
					$intensivo_c_m++;
				}
				//
				if($paciente[$i]['pabellon'] == 'Parcial' && $paciente[$i]['modulo']=='AD' && $paciente[$i]['tipo_hosp'] == 'P' && $paciente[$i]['estado'] == 'H' && $paciente[$i]['sala'] == 'V' && $paciente[$i]['cate'] == '10'){
					$parcial_p_v++;
				}
				if($paciente[$i]['pabellon'] == 'Parcial' && $paciente[$i]['modulo']=='AD' && $paciente[$i]['tipo_hosp'] == 'P' && $paciente[$i]['estado'] == 'H' && $paciente[$i]['sala'] == 'M' && $paciente[$i]['cate'] == '10'){
					$parcial_p_m++;
				}
				
			}

			$this->SetXY(20,$y);$this->MultiCell(150,9,"PABELLON",'1','C');
			$this->SetXY(170,$y);$this->MultiCell(20,54,"TOTAL",'1','C');
			$y=$y+9;
			$this->SetXY(20,$y);$this->MultiCell(50,9,"INTERMEDIO",'1','C');
			$this->SetXY(70,$y);$this->MultiCell(50,9,"INTENSIVO",'1','C');
			$this->SetXY(120,$y);$this->MultiCell(50,9,"PARCIAL",'1','C');
			$y=$y+9;
			$this->SetFont('Arial','',8);
			$this->SetXY(20,$y);$this->MultiCell(50,9,$intermedio,'1','C');
			$this->SetXY(70,$y);$this->MultiCell(50,9,$intensivo,'1','C');
			$this->SetXY(120,$y);$this->MultiCell(50,9,$parcial,'1','C');
			$y=$y+9;
			$this->SetFont('Arial','B',8);
			$this->SetXY(70,$y);$this->MultiCell(25,9,"COMPLETA",'1','C');
			$this->SetXY(95,$y);$this->MultiCell(25,9,"PARCIAL",'1','C');
			//
			$this->SetXY(120,$y);$this->MultiCell(25,9,"COMPLETA",'1','C');
			$this->SetXY(145,$y);$this->MultiCell(25,9,"PARCIAL",'1','C');
			//
			$this->SetXY(20,$y);$this->MultiCell(25,9,"COMPLETA",'1','C');
			$this->SetXY(45,$y);$this->MultiCell(25,9,"PARCIAL",'1','C');
			$y=$y+9;
			$this->SetFont('Arial','',8);
			$this->SetXY(20,$y);$this->MultiCell(25,9,$intermedio_c,'1','C');
			$this->SetXY(45,$y);$this->MultiCell(25,9,$intermedio_p,'1','C');
			//
			$this->SetXY(70,$y);$this->MultiCell(25,9,$intensivo_c,'1','C');
			$this->SetXY(95,$y);$this->MultiCell(25,9,$intensivo_p,'1','C');
			//
			$this->SetXY(120,$y);$this->MultiCell(25,9,$parcial_c,'1','C');
			$this->SetXY(145,$y);$this->MultiCell(25,9,$parcial_p,'1','C');
			$y=$y+9;
			$this->SetFont('Arial','B',8);
			$this->SetXY(20,$y);$this->MultiCell(12.5,9,"V",'1','C');
			$this->SetXY(32.5,$y);$this->MultiCell(12.5,9,"M",'1','C');
			//
			$this->SetXY(45,$y);$this->MultiCell(12.5,9,"V",'1','C');
			$this->SetXY(57.5,$y);$this->MultiCell(12.5,9,"M",'1','C');
			//
			$this->SetXY(70,$y);$this->MultiCell(12.5,9,"V",'1','C');
			$this->SetXY(82.5,$y);$this->MultiCell(12.5,9,"M",'1','C');
			//
			$this->SetXY(95,$y);$this->MultiCell(12.5,9,"V",'1','C');
			$this->SetXY(107.5,$y);$this->MultiCell(12.5,9,"M",'1','C');
			//
			$this->SetXY(120,$y);$this->MultiCell(12.5,9,"V",'1','C');
			$this->SetXY(132.5,$y);$this->MultiCell(12.5,9,"M",'1','C');
			//
			$this->SetXY(145,$y);$this->MultiCell(12.5,9,"V",'1','C');
			$this->SetXY(157.5,$y);$this->MultiCell(12.5,9,"M",'1','C');
			$y=$y+9;
			$this->SetFont('Arial','',8);
			$this->SetXY(20,$y);$this->MultiCell(12.5,9,$intermedio_c_v,'1','C');
			$this->SetXY(32.5,$y);$this->MultiCell(12.5,9,$intermedio_c_m,'1','C');
			//
			$this->SetXY(45,$y);$this->MultiCell(12.5,9,"0",'1','C');
			$this->SetXY(57.5,$y);$this->MultiCell(12.5,9,"0",'1','C');
			//
			$this->SetXY(70,$y);$this->MultiCell(12.5,9,$intensivo_c_v,'1','C');
			$this->SetXY(82.5,$y);$this->MultiCell(12.5,9,$intensivo_c_m,'1','C');
			//
			$this->SetXY(95,$y);$this->MultiCell(12.5,9,"0",'1','C');
			$this->SetXY(107.5,$y);$this->MultiCell(12.5,9,"0",'1','C');
			//
			$this->SetXY(120,$y);$this->MultiCell(12.5,9,"0",'1','C');
			$this->SetXY(132.5,$y);$this->MultiCell(12.5,9,"0",'1','C');
			//
			$this->SetXY(145,$y);$this->MultiCell(12.5,9,$parcial_p_v,'1','C');
			$this->SetXY(157.5,$y);$this->MultiCell(12.5,9,$parcial_p_m,'1','C');
			$this->SetFont('Arial','B',8);
			$this->SetXY(170,$y);$this->MultiCell(20,9,$intermedio+$intensivo+$parcial,'1','C');
			
		$this->SetFont('Arial','B',8);
		$this->SetXY(10,110);$this->MultiCell(165,5,"CATEGORIA: CONTINUADORES",'0','C');
		$y=110;
		$y=$y+5;
		$this->SetFont('Arial','B',8);
		$yini = $y;

			 
			for($i = 0;$i<count($paciente);$i++){				

				if($paciente[$i]['pabellon'] == 'Intermedio' && $paciente[$i]['modulo']=='AD' && $paciente[$i]['estado'] == 'H' && $paciente[$i]['cate'] == '11'){
					$c_intermedio ++;
				}
				if($paciente[$i]['pabellon'] == 'Intensivo' && $paciente[$i]['modulo']=='AD' && $paciente[$i]['estado'] == 'H' && $paciente[$i]['cate'] == '11'){
					$c_intensivo ++;
				}
				if($paciente[$i]['pabellon'] == 'Parcial' && $paciente[$i]['modulo']=='AD' && $paciente[$i]['estado'] == 'H' && $paciente[$i]['cate'] == '11'){
					$c_parcial ++;
				}
				///
				if($paciente[$i]['pabellon'] == 'Intermedio' && $paciente[$i]['modulo']=='AD' && $paciente[$i]['tipo_hosp'] == 'C' && $paciente[$i]['estado'] == 'H' && $paciente[$i]['cate'] == '11'){
					$c_intermedio_c ++;
				}
				if($paciente[$i]['pabellon'] == 'Intensivo' && $paciente[$i]['modulo']=='AD' && $paciente[$i]['tipo_hosp'] == 'C' && $paciente[$i]['estado'] == 'H' && $paciente[$i]['cate'] == '11'){
					$c_intensivo_c ++;
				}
				if($paciente[$i]['pabellon'] == 'Parcial' && $paciente[$i]['modulo']=='AD' && $paciente[$i]['tipo_hosp'] == 'C' && $paciente[$i]['estado'] == 'H' && $paciente[$i]['cate'] == '11'){
					$c_parcial_c ++;
				}
				///
				if($paciente[$i]['pabellon'] == 'Intermedio' && $paciente[$i]['modulo']=='AD' && $paciente[$i]['tipo_hosp'] == 'P' && $paciente[$i]['estado'] == 'H' && $paciente[$i]['cate'] == '11'){
					$c_intermedio_p ++;
				}
				if($paciente[$i]['pabellon'] == 'Intensivo' && $paciente[$i]['modulo']=='AD' && $paciente[$i]['tipo_hosp'] == 'P' && $paciente[$i]['estado'] == 'H' && $paciente[$i]['cate'] == '11'){
					$c_intensivo_p ++;
				}
				if($paciente[$i]['pabellon'] == 'Parcial' && $paciente[$i]['modulo']=='AD' && $paciente[$i]['tipo_hosp'] == 'P' && $paciente[$i]['estado'] == 'H' && $paciente[$i]['cate'] == '11'){
					$c_parcial_p ++;
				}
				//
				if($paciente[$i]['pabellon'] == 'Intermedio' && $paciente[$i]['modulo']=='AD' && $paciente[$i]['tipo_hosp'] == 'C' && $paciente[$i]['estado'] == 'H' && $paciente[$i]['sala'] == 'V' && $paciente[$i]['cate'] == '11'){
					$c_intermedio_c_v++;
				}
				if($paciente[$i]['pabellon'] == 'Intermedio' && $paciente[$i]['modulo']=='AD' && $paciente[$i]['tipo_hosp'] == 'C' && $paciente[$i]['estado'] == 'H' && $paciente[$i]['sala'] == 'M' && $paciente[$i]['cate'] == '11'){
					$c_intermedio_c_m++;
				}
				//
				if($paciente[$i]['pabellon'] == 'Intensivo' && $paciente[$i]['modulo']=='AD' && $paciente[$i]['tipo_hosp'] == 'C' && $paciente[$i]['estado'] == 'H' && $paciente[$i]['sala'] == 'V' && $paciente[$i]['cate'] == '11'){
					$c_intensivo_c_v++;
				}
				if($paciente[$i]['pabellon'] == 'Intensivo' && $paciente[$i]['modulo']=='AD' && $paciente[$i]['tipo_hosp'] == 'C' && $paciente[$i]['estado'] == 'H' && $paciente[$i]['sala'] == 'M' && $paciente[$i]['cate'] == '11'){
					$c_intensivo_c_m++;
				}
				//
				if($paciente[$i]['pabellon'] == 'Parcial' && $paciente[$i]['modulo']=='AD' && $paciente[$i]['tipo_hosp'] == 'P' && $paciente[$i]['estado'] == 'H' && $paciente[$i]['sala'] == 'V' && $paciente[$i]['cate'] == '11'){
					$c_parcial_p_v++;
				}
				if($paciente[$i]['pabellon'] == 'Parcial' && $paciente[$i]['modulo']=='AD' && $paciente[$i]['tipo_hosp'] == 'P' && $paciente[$i]['estado'] == 'H' && $paciente[$i]['sala'] == 'M' && $paciente[$i]['cate'] == '11'){
					$c_parcial_p_m++;
				}
				
			}

			$this->SetXY(20,$y);$this->MultiCell(150,9,"PABELLON",'1','C');
			$this->SetXY(170,$y);$this->MultiCell(20,54,"TOTAL",'1','C');
			$y=$y+9;
			$this->SetXY(20,$y);$this->MultiCell(50,9,"INTERMEDIO",'1','C');
			$this->SetXY(70,$y);$this->MultiCell(50,9,"INTENSIVO",'1','C');
			$this->SetXY(120,$y);$this->MultiCell(50,9,"PARCIAL",'1','C');
			$y=$y+9;
			$this->SetFont('Arial','',8);
			$this->SetXY(20,$y);$this->MultiCell(50,9,$c_intermedio,'1','C');
			$this->SetXY(70,$y);$this->MultiCell(50,9,$c_intensivo,'1','C');
			$this->SetXY(120,$y);$this->MultiCell(50,9,$c_parcial,'1','C');
			$y=$y+9;
			$this->SetFont('Arial','B',8);
			$this->SetXY(70,$y);$this->MultiCell(25,9,"COMPLETA",'1','C');
			$this->SetXY(95,$y);$this->MultiCell(25,9,"PARCIAL",'1','C');
			//
			$this->SetXY(120,$y);$this->MultiCell(25,9,"COMPLETA",'1','C');
			$this->SetXY(145,$y);$this->MultiCell(25,9,"PARCIAL",'1','C');
			//
			$this->SetXY(20,$y);$this->MultiCell(25,9,"COMPLETA",'1','C');
			$this->SetXY(45,$y);$this->MultiCell(25,9,"PARCIAL",'1','C');
			$y=$y+9;
			$this->SetFont('Arial','',8);
			$this->SetXY(20,$y);$this->MultiCell(25,9,$c_intermedio_c,'1','C');
			$this->SetXY(45,$y);$this->MultiCell(25,9,$c_intermedio_p,'1','C');
			//
			$this->SetXY(70,$y);$this->MultiCell(25,9,$c_intensivo_c,'1','C');
			$this->SetXY(95,$y);$this->MultiCell(25,9,$c_intensivo_p,'1','C');
			//
			$this->SetXY(120,$y);$this->MultiCell(25,9,$c_parcial_c,'1','C');
			$this->SetXY(145,$y);$this->MultiCell(25,9,$c_parcial_p,'1','C');
			$y=$y+9;
			$this->SetFont('Arial','B',8);
			$this->SetXY(20,$y);$this->MultiCell(12.5,9,"V",'1','C');
			$this->SetXY(32.5,$y);$this->MultiCell(12.5,9,"M",'1','C');
			//
			$this->SetXY(45,$y);$this->MultiCell(12.5,9,"V",'1','C');
			$this->SetXY(57.5,$y);$this->MultiCell(12.5,9,"M",'1','C');
			//
			$this->SetXY(70,$y);$this->MultiCell(12.5,9,"V",'1','C');
			$this->SetXY(82.5,$y);$this->MultiCell(12.5,9,"M",'1','C');
			//
			$this->SetXY(95,$y);$this->MultiCell(12.5,9,"V",'1','C');
			$this->SetXY(107.5,$y);$this->MultiCell(12.5,9,"M",'1','C');
			//
			$this->SetXY(120,$y);$this->MultiCell(12.5,9,"V",'1','C');
			$this->SetXY(132.5,$y);$this->MultiCell(12.5,9,"M",'1','C');
			//
			$this->SetXY(145,$y);$this->MultiCell(12.5,9,"V",'1','C');
			$this->SetXY(157.5,$y);$this->MultiCell(12.5,9,"M",'1','C');
			$y=$y+9;
			$this->SetFont('Arial','',8);
			$this->SetXY(20,$y);$this->MultiCell(12.5,9,$c_intermedio_c_v,'1','C');
			$this->SetXY(32.5,$y);$this->MultiCell(12.5,9,$c_intermedio_c_m,'1','C');
			//
			$this->SetXY(45,$y);$this->MultiCell(12.5,9,"0",'1','C');
			$this->SetXY(57.5,$y);$this->MultiCell(12.5,9,"0",'1','C');
			//
			$this->SetXY(70,$y);$this->MultiCell(12.5,9,$c_intensivo_c_v,'1','C');
			$this->SetXY(82.5,$y);$this->MultiCell(12.5,9,$c_intensivo_c_m,'1','C');
			//
			$this->SetXY(95,$y);$this->MultiCell(12.5,9,"0",'1','C');
			$this->SetXY(107.5,$y);$this->MultiCell(12.5,9,"0",'1','C');
			//
			$this->SetXY(120,$y);$this->MultiCell(12.5,9,"0",'1','C');
			$this->SetXY(132.5,$y);$this->MultiCell(12.5,9,"0",'1','C');
			//
			$this->SetXY(145,$y);$this->MultiCell(12.5,9,$c_parcial_p_v,'1','C');
			$this->SetXY(157.5,$y);$this->MultiCell(12.5,9,$c_parcial_p_m,'1','C');
			$this->SetFont('Arial','B',8);
			$this->SetXY(170,$y);$this->MultiCell(20,9,$c_intermedio+$c_intensivo+$c_parcial,'1','C');

		$this->SetFont('Arial','B',8);
		$this->SetXY(10,185);$this->MultiCell(175,5,"CATEGORIA: INDIGENTES",'0','C');
		$y=185;
		$y=$y+5;
		$this->SetFont('Arial','B',8);
		$yini = $y;

		for($i = 0;$i<count($paciente);$i++){				

				if($paciente[$i]['pabellon'] == 'Intermedio' && $paciente[$i]['modulo']=='AD' && $paciente[$i]['estado'] == 'H' && $paciente[$i]['cate'] == '8'){
					$i_intermedio ++;
				}
				if($paciente[$i]['pabellon'] == 'Intensivo' && $paciente[$i]['modulo']=='AD' && $paciente[$i]['estado'] == 'H' && $paciente[$i]['cate'] == '8'){
					$i_intensivo ++;
				}
				if($paciente[$i]['pabellon'] == 'Parcial' && $paciente[$i]['modulo']=='AD' && $paciente[$i]['estado'] == 'H' && $paciente[$i]['cate'] == '8'){
					$i_parcial ++;
				}
				///
				if($paciente[$i]['pabellon'] == 'Intermedio' && $paciente[$i]['modulo']=='AD' && $paciente[$i]['tipo_hosp'] == 'C' && $paciente[$i]['estado'] == 'H' && $paciente[$i]['cate'] == '8'){
					$i_intermedio_c ++;
				}
				if($paciente[$i]['pabellon'] == 'Intensivo' && $paciente[$i]['modulo']=='AD' && $paciente[$i]['tipo_hosp'] == 'C' && $paciente[$i]['estado'] == 'H' && $paciente[$i]['cate'] == '8'){
					$i_intensivo_c ++;
				}
				if($paciente[$i]['pabellon'] == 'Parcial' && $paciente[$i]['modulo']=='AD' && $paciente[$i]['tipo_hosp'] == 'C' && $paciente[$i]['estado'] == 'H' && $paciente[$i]['cate'] == '8'){
					$i_parcial_c ++;
				}
				///
				if($paciente[$i]['pabellon'] == 'Intermedio' && $paciente[$i]['modulo']=='AD' && $paciente[$i]['tipo_hosp'] == 'P' && $paciente[$i]['estado'] == 'H' && $paciente[$i]['cate'] == '8'){
					$i_intermedio_p ++;
				}
				if($paciente[$i]['pabellon'] == 'Intensivo' && $paciente[$i]['modulo']=='AD' && $paciente[$i]['tipo_hosp'] == 'P' && $paciente[$i]['estado'] == 'H' && $paciente[$i]['cate'] == '8'){
					$i_intensivo_p ++;
				}
				if($paciente[$i]['pabellon'] == 'Parcial' && $paciente[$i]['modulo']=='AD' && $paciente[$i]['tipo_hosp'] == 'P' && $paciente[$i]['estado'] == 'H' && $paciente[$i]['cate'] == '8'){
					$i_parcial_p ++;
				}
				//
				if($paciente[$i]['pabellon'] == 'Intermedio' && $paciente[$i]['modulo']=='AD' && $paciente[$i]['tipo_hosp'] == 'C' && $paciente[$i]['estado'] == 'H' && $paciente[$i]['sala'] == 'V' && $paciente[$i]['cate'] == '8'){
					$i_intermedio_c_v++;
				}
				if($paciente[$i]['pabellon'] == 'Intermedio' && $paciente[$i]['modulo']=='AD' && $paciente[$i]['tipo_hosp'] == 'C' && $paciente[$i]['estado'] == 'H' && $paciente[$i]['sala'] == 'M' && $paciente[$i]['cate'] == '8'){
					$i_intermedio_c_m++;
				}
				//
				if($paciente[$i]['pabellon'] == 'Intensivo' && $paciente[$i]['modulo']=='AD' && $paciente[$i]['tipo_hosp'] == 'C' && $paciente[$i]['estado'] == 'H' && $paciente[$i]['sala'] == 'V' && $paciente[$i]['cate'] == '8'){
					$i_intensivo_c_v++;
				}
				if($paciente[$i]['pabellon'] == 'Intensivo' && $paciente[$i]['modulo']=='AD' && $paciente[$i]['tipo_hosp'] == 'C' && $paciente[$i]['estado'] == 'H' && $paciente[$i]['sala'] == 'M' && $paciente[$i]['cate'] == '8'){
					$i_intensivo_c_m++;
				}
				//
				if($paciente[$i]['pabellon'] == 'Parcial' && $paciente[$i]['modulo']=='AD' && $paciente[$i]['tipo_hosp'] == 'P' && $paciente[$i]['estado'] == 'H' && $paciente[$i]['sala'] == 'V' && $paciente[$i]['cate'] == '8'){
					$i_parcial_p_v++;
				}
				if($paciente[$i]['pabellon'] == 'Parcial' && $paciente[$i]['modulo']=='AD' && $paciente[$i]['tipo_hosp'] == 'P' && $paciente[$i]['estado'] == 'H' && $paciente[$i]['sala'] == 'M' && $paciente[$i]['cate'] == '8'){
					$i_parcial_p_m++;
				}
				
			}

			$this->SetXY(20,$y);$this->MultiCell(150,9,"PABELLON",'1','C');
			$this->SetXY(170,$y);$this->MultiCell(20,54,"TOTAL",'1','C');
			$y=$y+9;
			$this->SetXY(20,$y);$this->MultiCell(50,9,"INTERMEDIO",'1','C');
			$this->SetXY(70,$y);$this->MultiCell(50,9,"INTENSIVO",'1','C');
			$this->SetXY(120,$y);$this->MultiCell(50,9,"PARCIAL",'1','C');

			$y=$y+9;
			$this->SetFont('Arial','',8);
			$this->SetXY(20,$y);$this->MultiCell(50,9,$i_intermedio,'1','C');
			$this->SetXY(70,$y);$this->MultiCell(50,9,$i_intensivo,'1','C');
			$this->SetXY(120,$y);$this->MultiCell(50,9,$i_parcial,'1','C');
			$y=$y+9;
			$this->SetFont('Arial','B',8);
			$this->SetXY(70,$y);$this->MultiCell(25,9,"COMPLETA",'1','C');
			$this->SetXY(95,$y);$this->MultiCell(25,9,"PARCIAL",'1','C');
			//
			$this->SetXY(120,$y);$this->MultiCell(25,9,"COMPLETA",'1','C');
			$this->SetXY(145,$y);$this->MultiCell(25,9,"PARCIAL",'1','C');
			//
			$this->SetXY(20,$y);$this->MultiCell(25,9,"COMPLETA",'1','C');
			$this->SetXY(45,$y);$this->MultiCell(25,9,"PARCIAL",'1','C');
			$y=$y+9;
			$this->SetFont('Arial','',8);
			$this->SetXY(20,$y);$this->MultiCell(25,9,$i_intermedio_c,'1','C');
			$this->SetXY(45,$y);$this->MultiCell(25,9,$i_intermedio_p,'1','C');
			//
			$this->SetXY(70,$y);$this->MultiCell(25,9,$i_intensivo_c,'1','C');
			$this->SetXY(95,$y);$this->MultiCell(25,9,$i_intensivo_p,'1','C');
			//
			$this->SetXY(120,$y);$this->MultiCell(25,9,$i_parcial_c,'1','C');
			$this->SetXY(145,$y);$this->MultiCell(25,9,$i_parcial_p,'1','C');
			$y=$y+9;
			$this->SetFont('Arial','B',8);
			$this->SetXY(20,$y);$this->MultiCell(12.5,9,"V",'1','C');
			$this->SetXY(32.5,$y);$this->MultiCell(12.5,9,"M",'1','C');
			//
			$this->SetXY(45,$y);$this->MultiCell(12.5,9,"V",'1','C');
			$this->SetXY(57.5,$y);$this->MultiCell(12.5,9,"M",'1','C');
			//
			$this->SetXY(70,$y);$this->MultiCell(12.5,9,"V",'1','C');
			$this->SetXY(82.5,$y);$this->MultiCell(12.5,9,"M",'1','C');
			//
			$this->SetXY(95,$y);$this->MultiCell(12.5,9,"V",'1','C');
			$this->SetXY(107.5,$y);$this->MultiCell(12.5,9,"M",'1','C');
			//
			$this->SetXY(120,$y);$this->MultiCell(12.5,9,"V",'1','C');
			$this->SetXY(132.5,$y);$this->MultiCell(12.5,9,"M",'1','C');
			//
			$this->SetXY(145,$y);$this->MultiCell(12.5,9,"V",'1','C');
			$this->SetXY(157.5,$y);$this->MultiCell(12.5,9,"M",'1','C');
			$y=$y+9;
			$this->SetFont('Arial','',8);
			$this->SetXY(20,$y);$this->MultiCell(12.5,9,$i_intermedio_c_v,'1','C');
			$this->SetXY(32.5,$y);$this->MultiCell(12.5,9,$i_intermedio_c_m,'1','C');
			//
			$this->SetXY(45,$y);$this->MultiCell(12.5,9,"0",'1','C');
			$this->SetXY(57.5,$y);$this->MultiCell(12.5,9,"0",'1','C');
			//
			$this->SetXY(70,$y);$this->MultiCell(12.5,9,$i_intensivo_c_v,'1','C');
			$this->SetXY(82.5,$y);$this->MultiCell(12.5,9,$i_intensivo_c_m,'1','C');
			//
			$this->SetXY(95,$y);$this->MultiCell(12.5,9,"0",'1','C');
			$this->SetXY(107.5,$y);$this->MultiCell(12.5,9,"0",'1','C');
			//
			$this->SetXY(120,$y);$this->MultiCell(12.5,9,"0",'1','C');
			$this->SetXY(132.5,$y);$this->MultiCell(12.5,9,"0",'1','C');
			//
			$this->SetXY(145,$y);$this->MultiCell(12.5,9,$i_parcial_p_v,'1','C');
			$this->SetXY(157.5,$y);$this->MultiCell(12.5,9,$i_parcial_p_m,'1','C');
			$this->SetFont('Arial','B',8);
			$this->SetXY(170,$y);$this->MultiCell(20,9,$i_intermedio+$i_intensivo+$i_parcial,'1','C');
			$y=$y+15;
		$this->SetFont('Arial','B',12);
		$this->SetXY(19,$y);$this->MultiCell(50,5,"RESUMEN:",'0','L');
		$y=$y+5;
		$this->SetFont('Arial','',10);
		$this->SetXY(19,$y);$this->MultiCell(60,5,"Total de Pacientes Nuevos: ",'0','L');
		$this->SetXY(79,$y);$this->MultiCell(50,5,$intermedio+$intensivo+$parcial,'0','L');
		$y=$y+5;
		$this->SetFont('Arial','',10);
		$this->SetXY(19,$y);$this->MultiCell(60,5,"Total de Pacientes Continuadores: ",'0','L');
		$this->SetXY(79,$y);$this->MultiCell(50,5,$c_intermedio+$c_intensivo+$c_parcial,'0','L');
		$y=$y+5;
		$this->SetFont('Arial','',10);
		$this->SetXY(19,$y);$this->MultiCell(60,5,"Total de Pacientes Indigentes: ",'0','L');
		$this->SetXY(79,$y);$this->MultiCell(50,5,$i_intermedio+$i_intensivo+$i_parcial,'0','L');
		$y=$y+5;
		$this->SetFont('Arial','B',10);
		$this->SetXY(19,$y);$this->MultiCell(60,5,"Total: ",'0','L');
		$this->SetXY(79,$y);$this->MultiCell(50,5,$intermedio+$intensivo+$parcial+$c_intermedio+$c_intensivo+$c_parcial+$i_intermedio+$i_intensivo+$i_parcial,'0','L');

		
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