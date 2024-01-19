<?php
global $f;
$f->library('pdf');
class expdientes extends FPDF
{
	var $filter;
	function Filtros($filtros){
		$this->filter = $filtros;
	}
	function Header(){
		//$this->Image(IndexPath.DS.'templates/td/expd.gif',15,15,347,249);
		$this->SetFont('Arial','B',13);
		$this->SetXY(10,10);$this->MultiCell(190,5,"EXPEDIENTE Nº ".$this->filter["num"],'0','C');
		$this->SetFont('Arial','B',9);
		$this->SetXY(10,5);$this->MultiCell(60,5,"SOCIEDAD DE BENEFICENCIA PUBLICA DE AREQUIPA",'0','C');
		$this->SetXY(10,5);$this->MultiCell(190,5,"Página: ".$this->PageNo()."/{nb}",'0','R');
		$this->SetFont('Arial','',7);
		$this->SetXY(10,15);$this->MultiCell(60,5,"Sistema SBPA - Módulo de Trámite Documentario",'0','C');
	}
	function Publicar($items){
		$states = array(
			"C"=>"Concluido",
			"P"=>"Pendiente",
			"A"=> "Aceptado",
			"R"=> "Rechazado",
			"F"=> "Enviado a Entidad Externa"
		);
		$this->SetFont('arial','',9);
		$y=30;
		$y_ini = $y;
		$this->SetXY(15,$y);$this->MultiCell(180,5,"REGISTRADO: ".Date::format($items["fecreg"]->sec,"d/m/Y H:i"),'0','L');
		$this->SetFont('arial','B',9);
		$instancia = "";
		if(isset($items["flujos"]["apelacion"])) $instancia = 'Apelación';
		else if(isset($items["flujos"]["reconsideracion"])) $instancia = "Reconsideración";
		else $instancia = "Inicio";
		$this->SetXY(15,$y);$this->MultiCell(180,5,"INSTANCIA / ESTADO: ".$instancia." / ".$states[$items["estado"]],'0','R');
		$this->SetFont('arial','',9);
		$y+=5;
		if(isset($items["fecven"])){
			$this->SetXY(15,$y);$this->MultiCell(180,5,"VENCIMIENTO: ".Date::format($items["fecven"]->sec,"d/m/Y H:i"),'0','L');
		}
		$y+=5;
		$gestor = $items["gestor"]["nomb"];
		if($items["gestor"]["tipo_enti"]=="P"){
			$gestor .=" ".$items["gestor"]["appat"]." ".$items["gestor"]["apmat"];
		}
		$this->SetXY(15,$y);$this->MultiCell(180,5,"GESTOR: ".$gestor,'0','L');
		$y+=5;
		$this->SetXY(15,$y);$this->MultiCell(180,5,"ASUNTO: ".$items["concepto"],'0','L');
		$y=$this->GetY();
		$this->SetXY(15,$y);$this->MultiCell(180,5,"UBICACIÓN ACTUAL: ".$items["ubicacion"]["nomb"],'0','L');
		$y+=10;
		$this->SetFont('arial','B',10);
		$this->SetXY(15,$y);$this->MultiCell(180,5,"RESOLUCIONES",'0','C');
		$this->SetFont('arial','',9);
		$y+=10;
		if(isset($items["flujos"])){
			if(isset($items["flujos"]["iniciacion"])){
				$this->SetXY(15,$y);$this->MultiCell(40,5,"Inicio",'0','L');
				$this->SetXY(135,$y);$this->MultiCell(60,5,"Iniciado ".Date::format($items["flujos"]["iniciacion"]["fecini"]->sec,"d/m/Y H:i"),'0','L');
				if(isset($items["flujos"]["iniciacion"]["fecfin"])){
					$y+=5;
					$this->SetXY(15,$y);$this->MultiCell(40,5,"Resolución",'0','L');
					$this->SetXY(55,$y);$this->MultiCell(60,5,$items["flujos"]["iniciacion"]["evaluacion"]." - ".$items["flujos"]["iniciacion"]["respuesta"],'0','L');
					$this->SetXY(135,$y);$this->MultiCell(60,5,"Concluido ".Date::format($items["flujos"]["iniciacion"]["fecfin"]->sec,"d/m/Y H:i"),'0','L');
				}
				$y+=10;
			}
			if(isset($items["flujos"]["reconsideracion"])){
				$this->SetXY(15,$y);$this->MultiCell(40,5,"Reconsideración",'0','L');
				$this->SetXY(135,$y);$this->MultiCell(60,5,"Iniciado ".Date::format($items["flujos"]["reconsideracion"]["fecini"]->sec,"d/m/Y H:i"),'0','L');
				if($items["flujos"]["reconsideracion"]["fecfin"]!=null){
					$y+=5;
					$this->SetXY(15,$y);$this->MultiCell(40,5,"Resolución",'0','L');
					$this->SetXY(55,$y);$this->MultiCell(60,5,$items["flujos"]["reconsideracion"]["evaluacion"]." - ".$items["flujos"]["reconsideracion"]["respuesta"],'0','L');
					$this->SetXY(135,$y);$this->MultiCell(60,5,"Concluido ".Date::format($items["flujos"]["reconsideracion"]["fecfin"]->sec,"d/m/Y H:i"),'0','L');
				}
				$y+=10;
			}
			if(isset($items["flujos"]["apelacion"])){
				$this->SetXY(15,$y);$this->MultiCell(40,5,"Apelacuón",'0','L');
				$this->SetXY(135,$y);$this->MultiCell(60,5,"Iniciado ".Date::format($items["flujos"]["apelacion"]["fecini"]->sec,"d/m/Y H:i"),'0','L');
				if($items["flujos"]["apelacion"]["fecfin"]!=null){
					$y+=5;
					$this->SetXY(15,$y);$this->MultiCell(40,5,"Resolución",'0','L');
					$this->SetXY(55,$y);$this->MultiCell(60,5,$items["flujos"]["apelacion"]["evaluacion"]." - ".$items["flujos"]["apelacion"]["respuesta"],'0','L');
					$this->SetXY(135,$y);$this->MultiCell(60,5,"Concluido ".Date::format($items["flujos"]["apelacion"]["fecfin"]->sec,"d/m/Y H:i"),'0','L');
				}
				$y+=10;
			}
			if(isset($items["documentos"])){
				$this->SetFont('arial','B',10);
				$this->SetXY(15,$y);$this->MultiCell(180,5,"DOCUMENTOS",'0','C');
				$y+=10;
				$this->SetFont('arial','',9);
				foreach($items["documentos"] as $item){
					if($y>275){
						$this->AddPage();
						$y=$y_ini;
					}
					$this->SetXY(15,$y);$this->MultiCell(120,5,$item['tipo_documento']['nomb']." - ".$item["num"],'1','L');
					$this->SetXY(135,$y);$this->MultiCell(60,5,"Fecha: ".Date::format($item["fecreg"]->sec,'d/m/Y H:i'),'1','L');
					$y+=5;
					$this->SetXY(15,$y);$this->MultiCell(120,5,"Origen: ".$item["organizacion"]["nomb"],'1','L');
					$this->SetXY(135,$y);$this->MultiCell(60,5,"Nº de Folios: ".$item["folios"],'1','L');
					$y+=5;
					$this->SetXY(15,$y);$this->MultiCell(180,5,"Asunto: ".$item["asunto"],'1','L');
					$y=$this->GetY()+5;
				}
				$y+=10;
			}
			if(isset($items["traslados"])){
				$this->SetFont('arial','B',10);
				$this->SetXY(15,$y);$this->MultiCell(180,5,"TRASLADOS",'0','C');
				$y+=10;
				$this->SetFont('arial','',9);
				foreach($items["traslados"] as $item){
					if($y>275){
						$this->AddPage();
						$y=$y_ini;
					}
					$orga = $item["origen"]["organizacion"]["nomb"];
					$entidad = $item["origen"]["entidad"]["nomb"]." ".$item["origen"]["entidad"]["appat"]." ".$item["origen"]["entidad"]["apmat"];
					if(isset($item["origen"]["entidad_ext"])){
						$orga = $item["origen"]["entidad_ext"]["nomb"];
						if($item["origen"]["entidad_ext"]["tipo_enti"]=="P"){
							$orga .= " ".$item["origen"]["entidad_ext"]["appat"]." ".$item["origen"]["entidad_ext"]["apmat"];
						}
						$entidad = $orga;
					}
					$this->SetXY(15,$y);$this->MultiCell(120,5,$orga,'0','L');
					$this->SetXY(135,$y);$this->MultiCell(60,5,"Recibido: ".Date::format($item["origen"]["fecreg"]->sec,'d/m/Y H:i'),'0','L');
					$y+=5;
					$this->SetXY(15,$y);$this->MultiCell(120,5,"Recibido por: ".$entidad,'0','L');
					if(isset($item["destino"]["fecenv"])){
						$this->SetXY(135,$y);$this->MultiCell(60,5,"Enviado: ".Date::format($item["destino"]["fecenv"]->sec,'d/m/Y H:i'),'0','L');
						$y+=5;
						if(isset($item["destino"]["organizacion"])){
							$env_org = $item["destino"]["organizacion"]["nomb"];
						}else{
							$env_org = $item["destino"]["entidad"]["nomb"];
							if($item["destino"]["entidad"]["tipo_enti"]=="P"){
								$env_org .= $item["destino"]["entidad"]["appat"]." ".$item["destino"]["entidad"]["apmat"];
							}
						}
						$copias = "";
						if(isset($item["copias"])){
							if(count($item["copias"])>0){
								$copias.="\nCon copia a: \n";
								foreach($item["copias"] as $copia){
									$copias.="                     ".$copia["organizacion"]["nomb"]."\n";
								}
							}
						}
						$this->SetXY(15,$y);$this->MultiCell(120,5,"Enviado a: ".$env_org.$copias,'0','L');
					}
					$y=$this->GetY();
					$this->Line(15, $y, 195, $y);
					$y+=5;
				}
				$y+=10;
			}
		}
	}
	function Footer()
	{
		
	} 
}
$pdf=new expdientes('P','mm','A4');
$pdf->AliasNbPages();
$pdf->SetMargins(15,39.5,0); 
$pdf->SetTitle("reporte");
$pdf->SetAutoPageBreak(false,0); 
$pdf->Open();
$pdf->Filtros($items);
$pdf->AddPage();
$pdf->Publicar($items);
$pdf->SetLeftMargin(25);
$pdf->Output();

?>