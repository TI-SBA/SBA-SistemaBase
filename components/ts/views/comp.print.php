<?php
global $f;
$f->library('pdf');

class repo extends FPDF
{
	function Rotate($angle,$x=-1,$y=-1) {

        if($x==-1)
            $x=$this->x;
        if($y==-1)
            $y=$this->y;
        if(isset($this->angle)){
        	if($this->angle!=0)
            	$this->_out('Q');
        }
        $this->angle=$angle;
        if($angle!=0)

        {
            $angle*=M_PI/180;
            $c=cos($angle);
            $s=sin($angle);
            $cx=$x*$this->k;
            $cy=($this->h-$y)*$this->k;
            
            $this->_out(sprintf('q %.5f %.5f %.5f %.5f %.2f %.2f cm 1 0 0 1 %.2f %.2f cm',$c,$s,-$s,$c,$cx,$cy,-$cx,-$cy));
        }
    } 
	function Header(){
		//$this->Image(IndexPath.DS.'templates/ts/comprobante-pago.jpg',0,0,210,297);	
	}
	function fillTableReference($data,$y){
		switch ($data["tipo"]) {
			case "TEXTO_PLANO":
				if(isset($data["texto_plano"])){
					//$this->setXY(15,$y);$this->MultiCell(175,5,$data["texto_plano"],'1','L');
					$this->setXY(12,$y);$this->MultiCell(175,3,$data["texto_plano"],'0','L');
				}
				break;
			case "COMPRA":
				if(isset($data["table_ref"])){
					$this->setXY(12,$y);$this->MultiCell(35,5,"REFERENCIA",'1','C');
					//$this->setXY(50,$y);$this->MultiCell(35,5,"ACTIVIDAD",'1','C');
					//$this->setXY(85,$y);$this->MultiCell(35,5,"FACTURA",'1','C');
					//$this->setXY(120,$y);$this->MultiCell(35,5,"MONTO",'1','C');
					//this->setXY(155,$y);$this->MultiCell(35,5,"REQUERIMIENTO",'1','C');
					$this->setXY(47,$y);$this->MultiCell(17,5,"FACTURA",'1','C');
					$this->setXY(64,$y);$this->MultiCell(35,5,"MONTO",'1','C');
					$this->setXY(99,$y);$this->MultiCell(35,5,"",'1','C');
					$y+=5;
					$total = 0;
					foreach ($data["table_ref"] as $item) {
						$this->setXY(12,$y);$this->MultiCell(35,5,$item["referencia"],'1','L');
						//$this->setXY(50,$y);$this->MultiCell(35,5,$item["actividad"],'1','L');
						//$this->setXY(85,$y);$this->MultiCell(35,5,$item["factura"],'1','L');
						//$this->setXY(120,$y);$this->MultiCell(35,5,$item["monto"],'1','R');
						//$this->setXY(155,$y);$this->MultiCell(35,5,$item["requerimiento"],'1','L');
						$this->setXY(47,$y);$this->MultiCell(17,5,$item["factura"],'1','L');
						$this->setXY(64,$y);$this->MultiCell(35,5,$item["monto"],'1','C');
						$this->setXY(99,$y);$this->MultiCell(35,5,$item["requerimiento"],'1','L');
						$total+=$item["monto"];
						$y+=5;
					}
					$this->setXY(64,$y);$this->MultiCell(35,5,number_format($total,2),'1','L');
					$y+=5;
					if(isset($data['certificacion'])) $this->setXY(12,$y);$this->MultiCell(60,5,$data['certificacion'],'0','L');
					$y+=5;
					if(isset($data['proveido'])) $this->setXY(12,$y);$this->MultiCell(60,5,$data['proveido'],'0','L');
					$y+=5;
					if(isset($data['adicionales'])) $this->setXY(80,$y);$this->MultiCell(60,5,$data['adicionales'],'0','L');
				}
				break;
			case "LOCACION":
				if(isset($data["table_ref"])){
					$this->setXY(12,$y);$this->MultiCell(35,5,"REFERENCIA",'0','C');
					$this->setXY(47,$y);$this->MultiCell(13,5,"R. X H.",'0','C');
					$this->setXY(60,$y);$this->MultiCell(17,5,"MONTO",'0','C');
					$this->setXY(77,$y);$this->MultiCell(60,5,"NOMBRES",'0','C');
					$this->setXY(137,$y);$this->MultiCell(36,5,"OFICIO",'0','C');
					$this->setXY(173,$y);$this->MultiCell(20,5,"RUC",'0','C');
					$y+=5;
					$total = 0;
					foreach ($data["table_ref"] as $item) {
						$this->setXY(12,$y);$this->MultiCell(35,5,$item["referencia"],'0','L');
						$this->setXY(47,$y);$this->MultiCell(13,5,$item["rxh"],'0','C');
						$this->setXY(60,$y);$this->MultiCell(17,5,$item["monto"],'0','C');
						$this->setXY(77,$y);$this->MultiCell(60,5,$item["nombres"],'0','L');
						$this->setXY(137,$y);$this->MultiCell(36,5,$item["oficio"],'0','L');
						$this->setXY(173,$y);$this->MultiCell(20,5,$item["ruc"],'0','L');
						$total+=$item["monto"];
						$y+=5;
					}
					$this->setXY(60,$y);$this->MultiCell(20,5,number_format($total,2),'0','C');
					$y+=5;
					$this->setXY(12,$y);$this->MultiCell(60,5,$data['aprobacion'],'0','L');
					$this->setXY(80,$y);$this->MultiCell(60,5,$data['contratos'],'0','L');

				}
				break;
			default:
				$this->setXY(12,$y);$this->MultiCell(36,5,"Ha ocurrido un error",'1','L');
				break;
		}
	}
	function Publicar($items){
		$monedas = array(
			"S"=>array("simb"=>"S/.","nomb"=>"NUEVO SOL","plu"=>"NUEVOS SOLES"),
			"D"=>array("simb"=>"USSD $.","nomb"=>"DOLAR","plu"=>"DOLARES")
		);
		$x=0;
		$y=30;
		$alto = 5;
		$this->SetFont('Arial','B',4);
		//$this->SetXY(138,18.5);$this->MultiCell(50,5,str_pad($items["cod"], 6, "0", STR_PAD_LEFT),'0','L');
		$this->SetXY(138,16.5);$this->MultiCell(40,5,str_pad($items["cod"], 6, "0", STR_PAD_LEFT),'0','L');
		$this->SetFont('Arial','',9);
		//$this->SetXY(30,$y+1);$this->MultiCell(110,5,$items["nomb"],'0','L');
		$this->SetXY(25,$y+1);$this->MultiCell(110,5,$items["nomb"],'0','L');
		$y=$y+30;
		//$this->SetXY(155,35);$this->MultiCell(15,5,Date::format($items["fecreg"]->sec, "d"),'0','C');//150
		//$this->SetXY(165,35);$this->MultiCell(15,5,Date::format($items["fecreg"]->sec, "m"),'0','C');//165
		//$this->SetXY(180,35);$this->MultiCell(20,5,Date::format($items["fecreg"]->sec, "Y"),'0','C');//180
		$this->SetXY(150,35);$this->MultiCell(15,5,Date::format($items["fecreg"]->sec, "d"),'0','C');//150
		$this->SetXY(160,35);$this->MultiCell(15,5,Date::format($items["fecreg"]->sec, "m"),'0','C');//165
		$this->SetXY(175,35);$this->MultiCell(20,5,Date::format($items["fecreg"]->sec, "Y"),'0','C');//180
		$this->SetFont('Arial','',8.2);
		$this->SetXY(12,$y);$this->MultiCell(190,3,$items["descr"],'0','L');
		$y=$this->GetY();
		$y=$y+$alto;
		//$this->SetXY(15,$y);$this->MultiCell(180,3,"REFERENCIA.- ".$items["ref"],'0','L');
		//$this->SetXY(15,$y);$this->MultiCell(180,3,"REFERENCIA.- ".$items["ref"],'0','L');
		if(isset($items["referencia"])) $this->fillTableReference($items["referencia"],$y);
		$y=$this->GetY();	
		$this->SetFont('Arial','',10);
		if(isset($items["items"])){
			/*$this->SetXY(15,$y);$this->MultiCell(80,5,"CUENTA POR PAGAR",'0','C');
			$this->SetXY(95,$y);$this->MultiCell(55,5,"ACTIVIDAD/COMPONENTE",'0','C');
			$this->SetXY(150,$y);$this->MultiCell(40,5,"MONTO",'0','C');
			$y=$y+5;
			foreach($items["items"] as $item){
				$this->SetXY(15,$y);$this->MultiCell(80,5,$item["motivo"],'0','L');							
				if(isset($item["afectacion"])){
					$this->SetXY(95,$y);$this->MultiCell(55,5,$item["afectacion"][0]["organizacion"]["actividad"]["cod"]."/".$item["afectacion"][0]["organizacion"]["componente"]["cod"],'0','L');
					$this->SetXY(150,$y);$this->MultiCell(40,5,number_format($item["afectacion"][0]["monto"],2,".", ","),'0','R');
					$y=$y+5;
					$total_det = $item["afectacion"][0]["monto"];
					if(count($item["afectacion"])>1){
						for($i=1;$i<count($item["afectacion"]);$i++){
							$this->SetXY(95,$y);$this->MultiCell(55,5,$item["afectacion"][$i]["organizacion"]["actividad"]["cod"]."/".$item["afectacion"][$i]["organizacion"]["componente"]["cod"],'0','L');
							$this->SetXY(150,$y);$this->MultiCell(40,5,number_format($item["afectacion"][$i]["monto"],2,".", ","),'0','R');
							$total_det = $total_det + $item["afectacion"][$i]["monto"];
							$y=$y+5;
						}
					}
					$this->SetXY(95,$y);$this->MultiCell(55,5,"TOTAL==>",'0','R');
					$this->SetXY(150,$y);$this->MultiCell(40,5,number_format($total_det,2,".", ","),'0','R');
				}else{
					$y=$y+5;
				}
			}*/
			/** Codificacion programatica */
			$y = 120;
			if(isset($items["cod_programatica"])){
				$this->SetFont('Arial','',6.9);
				//$this->SetXY(10,$y);$this->MultiCell(20,5,$items["cod_programatica"]["pliego"],'1','L');
				//$this->SetXY(23,$y);$this->MultiCell(20,5,$items["cod_programatica"]["programa"],'1','L');
				//$this->SetXY(40,$y);$this->MultiCell(20,5,$items["cod_programatica"]["subprograma"],'1','L');
				//$this->SetXY(57,$y);$this->MultiCell(25,5,$items["cod_programatica"]["proyecto"],'1','L');
				//$this->SetXY(75,$y);$this->MultiCell(25,5,$items["cod_programatica"]["obra"],'1','L');
				//$this->SetXY(95,$y);$this->MultiCell(15,5,$items["fuente"]["cod"],'1','L');
				$this->SetXY(9,$y);$this->MultiCell(12.5,5,$items["cod_programatica"]["sector"],'0','C');
				$this->SetXY(21.5,$y);$this->MultiCell(10.5,5,$items["cod_programatica"]["pliego"],'0','C');
				$this->SetXY(32,$y);$this->MultiCell(12,5,$items["cod_programatica"]["programa"],'0','C');
				$this->SetXY(44,$y);$this->MultiCell(12,5,$items["cod_programatica"]["subprograma"],'0','C');
				$this->SetXY(56,$y);$this->MultiCell(11.5,5,$items["cod_programatica"]["proyecto"],'0','C');
				$this->SetXY(67.5,$y);$this->MultiCell(11,5,$items["cod_programatica"]["obra"],'0','C');
				$this->SetXY(78,$y);$this->MultiCell(13,5,$items["fuente"]["cod"],'0','C');
			}
			/** Contabilidad Presupuestal */
			$y = 146;
			$y_d = $y;
			$y_h = $y;
			if(isset($items["cont_presupuestal"])){
				foreach($items["cont_presupuestal"] as $cpres){
					if($cpres["tipo"]=="D"){
						//$this->SetXY(15,$y_d);$this->MultiCell(35,5,$cpres["cuenta"]["cod"],'0','L');
						//$this->SetXY(40,$y_d);$this->MultiCell(20,5,number_format($cpres["monto"],2,".", ","),'0','R');
						$this->SetXY(9,$y_d);$this->MultiCell(25,5,$cpres["cuenta"]["cod"],'0','L');
						$this->SetXY(28,$y_d);$this->MultiCell(28,5,number_format($cpres["monto"],2,".", ","),'0','R');
						$y_d=$y_d+5;
					}elseif($cpres["tipo"]=="H"){
						//$this->SetXY(60,$y_h);$this->MultiCell(35,5,$cpres["cuenta"]["cod"],'0','L');
						//$this->SetXY(85,$y_h);$this->MultiCell(20,5,number_format($cpres["monto"],2,".", ","),'0','R');
						$this->SetXY(56,$y_h);$this->MultiCell(25,5,$cpres["cuenta"]["cod"],'0','L');
						$this->SetXY(72,$y_h);$this->MultiCell(29,5,number_format($cpres["monto"],2,".", ","),'0','R');
						$y_h=$y_h+5;
					}
				}
			}
			$y = 168;
			$y_d = $y;
			$y_h = $y;
			/** Contabilidad Patrimonial */
			if(isset($items["cont_patrimonial"])){
				foreach($items["cont_patrimonial"] as $cpat){
					if($cpat["tipo"]=="D"){
						//$this->SetXY(15,$y_d);$this->MultiCell(35,5,$cpat["cuenta"]["cod"],'0','L');
						//$this->SetXY(40,$y_d);$this->MultiCell(20,5,number_format($cpat["monto"],2,".", ","),'0','R');
						$this->SetXY(9,$y_d);$this->MultiCell(25,5,$cpat["cuenta"]["cod"],'0','L');
						$this->SetXY(28,$y_d);$this->MultiCell(28,5,number_format($cpat["monto"],2,".", ","),'0','R');
						$y_d=$y_d+5;
					}elseif($cpat["tipo"]=="H"){
						//$this->SetXY(60,$y_h);$this->MultiCell(35,5,$cpat["cuenta"]["cod"],'0','L');
						//$this->SetXY(85,$y_h);$this->MultiCell(20,5,number_format($cpat["monto"],2,".", ","),'0','R');
						$this->SetXY(56,$y_h);$this->MultiCell(25,5,$cpat["cuenta"]["cod"],'0','L');
						$this->SetXY(72,$y_h);$this->MultiCell(29,5,number_format($cpat["monto"],2,".", ","),'0','R');
						$y_h=$y_h+5;
					}
				}
			}
			$y = 116;
			/** Estadistica objeto del gasto */
			$tot_est = 0;
			if(isset($items["objeto_gasto"])){
				foreach($items["objeto_gasto"] as $eobj){
					$this->SetXY(107,$y);$this->MultiCell(25,5,$eobj["clasificador"]["cod"],'0','L');//110
					$this->SetXY(157,$y);$this->MultiCell(37,5,number_format($eobj["monto"],2,".", ","),'0','R');//175
					//$this->SetXY(140,$y);$this->MultiCell(40,5,$eobj["clasificador"]["nomb"],'0','L');//135
					$tot_est = $tot_est + $eobj["monto"];
					//$y=$y+5;
					$y=$this->GetY();
				}			
			}
			/** Retenciones y Deducciones */
			$y = 218;
			$total_pag = 0;
			$total_des = 0;
			foreach($items["items"] as $it){
				if(count($it["conceptos"])>0){
					foreach($it["conceptos"] as $conc){
						if($conc["tipo"]=="D"){
							//$this->SetXY(120,$y);$this->MultiCell(25,5,$conc["concepto"]["cuenta"]["cod"],'0','L');
							//$this->SetXY(145,$y);$this->MultiCell(40,5,$conc["concepto"]["nomb"],'0','L');
							//$this->SetXY(175,$y);$this->MultiCell(25,5,number_format($conc["monto"],2,".", ","),'0','R');
							$y=$y+5;
							$total_des=$total_des+$conc["monto"];
						}elseif($conc["tipo"]=="P"){
							$total_pag=$total_pag+$conc["monto"];
						}
					}
				}	
			}
			$y = 200;
			if(isset($items["retenciones"])){
				foreach($items["retenciones"] as $rete){
					$this->SetXY(107,$y);$this->MultiCell(20,5,$rete["cuenta"]["cod"],'0','L');
					$this->SetXY(169,$y);$this->MultiCell(23,5,number_format($rete["monto"],2,".", ","),'0','R');
					$this->SetXY(127,$y);$this->MultiCell(42,5,$rete["cuenta"]["descr"],'0','L');
					$y = $this->GetY();
				}
			}
			$this->SetFont('Arial','',10);
			/** Total a pagar */
			$y = 178;
			//$this->SetXY(175,$y);$this->MultiCell(30,5,number_format($total_pag,2,".", ","),'1','R');
			$this->SetXY(156,$y);$this->MultiCell(38,5,number_format($total_pag,2,".", ","),'0','R');
			$y=$y+5;
			//$this->SetXY(175,$y);$this->MultiCell(30,5,number_format($total_des,2,".", ","),'1','R');
			$this->SetXY(156,$y);$this->MultiCell(38,5,number_format($total_des,2,".", ","),'0','R');
			$y=$y+5;
			//$this->SetXY(175,$y);$this->MultiCell(30,5,number_format($total_pag-$total_des,2,".", ","),'1','R');
			$this->SetXY(156,$y);$this->MultiCell(38,5,number_format($total_pag-$total_des,2,".", ","),'0','R');
			$this->SetXY(20,37);$this->MultiCell(110,5,Number::lit($total_pag-$total_des)." Y ".round(((($total_pag-$total_des)-((int)($total_pag-$total_des)))*100),0)."/00 ".$monedas[$items["moneda"]]["plu"],'0','L');
			/** Forma de Pago */
			$y = 283;
			if($items["tipo_pago"]=="C"){
				if(isset($items["beneficiarios"])){
					$strlen = strlen($items["cuenta_banco"]["cod"]);
					$inic_cuen = substr($items["cuenta_banco"]["cod"],0,3);
					$tot_bene = count($items["beneficiarios"])-1;
					$par = 0;
					$separator = "";
					foreach($items["beneficiarios"] as $bene){
						if($par!=0 || $par!=$tot_bene)$separator = "/";
						$inic_cuen = $inic_cuen.$bene["cheque"].$separator;
						$par++;
					}
					//$this->SetXY(125,$y);$this->MultiCell(70,5,$inic_cuen."-".substr($items["cuenta_banco"]["cod"],$strlen-4,$strlen),'0','L');
					$this->SetXY(122,$y);$this->MultiCell(70,5,$inic_cuen."-".substr($items["cuenta_banco"]["cod"],$strlen-4,$strlen),'0','L');
				}
			}
			/** para uso del tesorero */
			$this->SetFont('Arial','',9);
			//$this->SetXY(10,223);$this->MultiCell(25,3,Date::format($items["fecreg"]->sec, "d/m/Y"),'0','C');
			//$this->SetXY(32,223);$this->MultiCell(40,3,strtoupper($items["autor"]["nomb"][0])." ".strtoupper($items["autor"]["appat"][0])." ".strtoupper($items["autor"]["apmat"][0]),'0','C');
			$this->SetXY(9,230);$this->MultiCell(19,3,Date::format($items["fecreg"]->sec, "d/m/Y"),'0','C');
			$this->SetXY(28,230);$this->MultiCell(36,3,strtoupper($items["autor"]["nomb"][0])." ".strtoupper($items["autor"]["appat"][0])." ".strtoupper($items["autor"]["apmat"][0]),'0','C');
			/** ./para uso del tesorero */
			if($items["estado"]=="X"){
				$this->Rotate(40);
				$this->SetTextColor(194,8,8);//194,8,8
				$this->SetFont('Arial','',60);
				$this->setXY(100,200);$this->Write(50,"ANULADO");	
			}				
		}
	}
	function Footer()
	{
		
	} 
	 
}

$pdf=new repo('P','mm','A4');
$pdf->AliasNbPages();
//$pdf->SetMargins(10,10,0); 
$pdf->SetTitle("reporte");
$pdf->SetAutoPageBreak(false,0); 
$pdf->Open();
$pdf->AddPage();
$pdf->Publicar($items);
$pdf->SetLeftMargin(25);
//header('Content-Type: application/pdf; charset=utf-8');
$pdf->Output();

?>