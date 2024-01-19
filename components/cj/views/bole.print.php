<?php
global $f;
$f->library('pdf');
setlocale(LC_ALL,"esp");
class doc extends FPDF
{
	var $d;
	function head($items){
		$this->d = $items;
	}
	function Header(){
		$this->SetFont('courier','',10);
		$cliente = $this->d["cliente"]["nomb"];
		if($this->d["cliente"]["tipo_enti"]=="P"){
			$cliente .= " ".$this->d["cliente"]["appat"]." ".$this->d["cliente"]["apmat"];
		}
		$this->setXY(27,48);$this->Cell(100,5,$cliente,0,0,'L');
		$this->setXY(27,55);$this->Cell(100,5,$this->d["cliente"]["domicilios"][0]["direccion"],0,0,'L');
		if(isset($this->d["cliente"]["docident"])){		
			foreach($this->d["cliente"]["docident"] as $dident){
				if($this->d["cliente"]["tipo_enti"]=="P"){
					if($dident["tipo"]=="DNI"){
						$doc_tipo = "DNI";
						$doc_num = $dident["num"];
					}					
				}else{
					if($dident["tipo"]=="RUC"){
						$doc_tipo = "RUC";
						$doc_num = $dident["num"];
					}
				}
			}
		}
		$this->setXY(42,62);$this->Cell(100,5,$doc_tipo." - ".$doc_num,0,0,'L');
		$this->setXY(147,62);$this->Cell(8,5,Date::format($this->d["fecreg"]->sec, 'd'),0,0,'L');
		$this->setXY(160,62);$this->Cell(25,5,Date::format($this->d["fecreg"]->sec, 'm'),0,0,'L');
		$this->setXY(190,62);$this->Cell(10,5,Date::format($this->d["fecreg"]->sec, 'Y'),0,0,'L');
		$this->SetFont('courier','B',17);
		$this->setXY(145,40);$this->Cell(70,5,$this->d["serie"]." - ".$this->d["num"],0,0,'L');
	}		
	function Publicar($items){
		global $f;
		$f->library('helpers');
		$meses = array("Todos","Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Setiembre","Octubre","Noviembre","Diciembre");
		$monedas = array(
			"S"=>array("nomb"=>"NUEVOS SOLES","simb"=>"S/."),
			"D"=>array("nomb"=>"DOLARES AMERICANOS","simb"=>"US$")
		);
		$y = 76;
		$this->SetFont('courier','',10);
		foreach($items["items"] as $row){
			$after = "";
			if($row["cuenta_cobrar"]["modulo"]=="CM"){
				if(isset($row["cuenta_cobrar"]["operacion"]["concesion"])){
					if(isset($row["cuenta_cobrar"]["operacion"]["espacio"])){
						$after .= "\nESPACIO - ".$helper->replace_acc($row["cuenta_cobrar"]["operacion"]["espacio"]["nomb"]);
					}
				}
				if(isset($row["cuenta_cobrar"]["operacion"]["inhumacion"])){
					if(isset($row["cuenta_cobrar"]["operacion"]["ocupante"])){
						$after .= "\nPARA LOS RESTOS DEL QUE FUE:".$row["cuenta_cobrar"]["operacion"]["ocupante"]["appat"]." ".$row["cuenta_cobrar"]["operacion"]["ocupante"]["apmat"].", ".$row["cuenta_cobrar"]["operacion"]["ocupante"]["nomb"];
					}
					if(isset($row["cuenta_cobrar"]["operacion"]["inhumacion"]["funeraria"])){
						$after .= "\nFUNERARIA: ".$row["cuenta_cobrar"]["operacion"]["inhumacion"]["funeraria"]["nomb"];	
					}
					if(isset($row["cuenta_cobrar"]["operacion"]["programacion"])){
						$after .= "\nPROGRAMACION PARA EL: ".Date::format($row["cuenta_cobrar"]["operacion"]["programacion"]["fecprog"]->sec,"d/m/Y H:i");
					}
				}
			}elseif($row["cuenta_cobrar"]["modulo"]=="IN"){
				if(isset($row["cuenta_cobrar"]["operacion"]["arrendamiento"])){
					if(isset($row["cuenta_cobrar"]["operacion"]["arrendamiento"]["rentas"])){
						foreach($row["cuenta_cobrar"]["operacion"]["arrendamiento"]["rentas"] as $renta){
							if($row["cuenta_cobrar"]['_id']==$renta['cuenta_cobrar']){
								$after.="\n POR EL ALQUILER DEL MES DE: ".$renta['letra'];
							}
						}
					}
					$after.="\n CONTRATO VIGENTE ".$row["cuenta_cobrar"]["operacion"]["arrendamiento"]["contrato"]." DEL ".Date::format($row["cuenta_cobrar"]["operacion"]["arrendamiento"]["feccon"]->sec,"d/m/Y")." AL ".Date::format($row["cuenta_cobrar"]["operacion"]["arrendamiento"]["fecven"]->sec,"d/m/Y");
				}
			}
			//$this->setXY(14,$y);$this->Cell(163,5,$row["cuenta_cobrar"]["servicio"]["nomb"].$after,0,0,'L');
			$this->setXY(14,$y);$this->MultiCell(163,3,$row["cuenta_cobrar"]["servicio"]["nomb"].$after,'0','L');	
			$y=$this->GetY();
			if($row["cuenta_cobrar"]["modulo"]=="CM"){
				foreach($row["conceptos"] as $con){
					$this->setXY(24,$y);$this->Cell(153,5,$con["concepto"]["nomb"],0,0,'L');
					$this->setXY(175,$y);$this->Cell(25,5,number_format($con["monto"],2),0,0,'R');
					$y+=5;
				}
			}elseif($row["cuenta_cobrar"]["modulo"]=="IN"){
				$tot_me=0;
				$tot_mn=0;
				foreach($row["conceptos"] as $con){
					if($row["cuenta_cobrar"]['moneda']=='S'){
						$_me=0;
						$_tc=0;
						$_mn=$con["monto"];
					}else{
						$_me=$con["monto"];
						$_tc=$items["tc"];
						$_mn=$con["monto"]*$_tc;
					}
					$tot_me+=$_me;
					$tot_mn+=$_mn;
					//if($con["concepto"]["nomb"]=="IGV Alquier") continue;
					$this->setXY(25,$y);$this->Cell(100,5,$con["concepto"]["nomb"],0,0,'L');
					$this->setXY(125,$y);$this->Cell(25,5,number_format($_me,2),0,0,'R');
					$this->setXY(150,$y);$this->Cell(25,5,number_format($_tc,3),0,0,'R');
					$this->setXY(175,$y);$this->Cell(25,5,number_format($_mn,2),0,0,'R');
					$y+=5;
				}
				$this->setXY(125,$y);$this->Cell(25,5,number_format($tot_me,2),0,0,'R');
				$this->setXY(175,$y);$this->Cell(25,5,number_format($tot_mn,2),0,0,'R');
			}
		}
		$total = $items['total'];
		if($items['moneda']=='D'){
			$total = $total/$items['tc'];
		}
		$this->SetFont('courier','',9);
		$y=110;
		$this->setXY(25,$y);$this->Cell(163,5,Number::lit($total).' Y '.round((($total-((int)$total))*100),0).'/100 '.$monedas[$items["moneda"]]["nomb"],0,0,'L');
		$y+=5;
		$this->setXY(150,$y);$this->Cell(25,5,"RECIBIDO EN",0,0,'R');
		foreach($items['efectivos'] as $pago){
			$this->setXY(175,$y);$this->Cell(25,5,$monedas[$pago["moneda"]]["simb"].' '.number_format($pago['monto'],2),0,0,'R');
			$y+=5;	
		}
		//$this->setXY(66,120);$this->MultiCell(85,5,"Arequipa ".strftime("%d de %B del %Y",$items['fecreg']->sec)." \n RECAUDADOR:".$items["autor"]["nomb"]." ".$items["autor"]["appat"]." ".$items["autor"]["apmat"],'0','C');
		//$this->setXY(175,120);$this->Cell(25,5,number_format($items["total"],2),0,0,'R');
		//$this->Rect(15, $y, 20, 10);$this->setXY(15,$y);$this->MultiCell(20,5,"Tipo de Persona",'0','C');
	}
	function Footer()
	{

	} 
	 
}

//$pdf=new doc('P','mm',array(210,148));
$pdf=new doc('P','mm','A4');
$pdf->SetMargins(15,39.5,0); 
$pdf->SetTitle("Boleta de Venta");
$pdf->SetAutoPageBreak(false,0); 
$pdf->Open();
$pdf->head($items);
$pdf->AddPage();
$pdf->Publicar($items);
$pdf->SetLeftMargin(25);
//header('Content-Type: application/pdf; charset=utf-8');
$pdf->Output();

?>