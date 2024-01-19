<?php
global $f;
$f->library('pdf');
class hosp extends FPDF{
  public $initX=15;
  public $hTitle=10;
  public $hText=5;
  public $vX=0;
  public $wName=250;
  public $wHistClin=110;
  public $wNH=15;
  public $wDate=25;
  public $wNumDoc=30;
  public $wCat=35;
  public $wModulo=40;
  public $wTipo=40;
  public $wHospInit=25;
  public $wHospEnd=25;
  public $wQuantDay=25;
  public $wMontoServ=30;
  public $wPagR=20;
  public $wpagMont=20;
  public $wSaldo=30;

	function Header(){
		$this->SetFont('Arial','B',14);
		$this->SetXY(10,10);$this->MultiCell(357,5,"ESTADO DE CUENTA DE PACIENTE HOSPITALIZADO",'0','C');
		$this->SetFont('Arial','B',9);
		$this->SetXY(10,5);$this->MultiCell(60,5,"SOCIEDAD DE BENEFICENCIA DE AREQUIPA",'0','C');
		$this->SetXY(10,5);$this->MultiCell(357,5,"Página: ".$this->PageNo()."/{nb}",'0','R');
		$this->SetFont('Arial','',7);
		$this->SetXY(10,15);$this->MultiCell(60,5,"Sistema SBA - Módulo de Hospitalizaciones",'0','C');
		$this->SetFont('arial','B',10);
    $this->SetXY($this->vX+=$this->initX,26);$this->MultiCell($this->wName,$this->hTitle,"Nombres y Apellidos: ",'1','L');
    $this->SetXY($this->vX+=$this->wName,26);$this->MultiCell($this->wHistClin,$this->hTitle,"Historia clinica: ",'1','L');$this->vX=0;
    //Titles
    $this->SetXY($this->vX+=$this->initX,36);$this->MultiCell($this->wNH,$this->hTitle,"Nº",'1','C');
    $this->SetXY($this->vX+=$this->wNH,36);$this->MultiCell($this->wDate,$this->hTitle,"Fecha",'1','C');
    $this->SetXY($this->vX+=$this->wDate,36);$this->MultiCell($this->wNumDoc,$this->hTitle,"NºDoc",'1','C');
    $this->SetXY($this->vX+=$this->wNumDoc,36);$this->MultiCell($this->wCat,$this->hTitle,"Cate.",'1','C');
    $this->SetXY($this->vX+=$this->wCat,36);$this->MultiCell($this->wModulo,$this->hTitle,"Modulo",'1','C');
    $this->SetXY($this->vX+=$this->wModulo,36);$this->MultiCell($this->wTipo,$this->hTitle,"Tipo",'1','C');
    $this->SetXY($this->vX+=$this->wTipo,36);$this->MultiCell($this->wHospInit+$this->wHospEnd,$this->hTitle,"Hospitalizacion",'1','C');
    $this->SetXY($this->vX+=$this->wHospInit+$this->wHospEnd,36);$this->MultiCell($this->wQuantDay,$this->hTitle,"días/meses.",'1','C');
    $this->SetXY($this->vX+=$this->wQuantDay,36);$this->MultiCell($this->wMontoServ,$this->hTitle,"MontoServicio",'1','C');
    $this->SetXY($this->vX+=$this->wMontoServ,36);$this->MultiCell($this->wPagR+$this->wpagMont,$this->hTitle,"Pagos",'1','C');
    $this->SetXY($this->vX+=$this->wPagR+$this->wpagMont,36);$this->MultiCell($this->wSaldo,$this->hTitle,"Saldo",'1','C');
    $this->vX=0;
    $this->SetXY($this->vX+=$this->initX+$this->wNH+$this->wDate+$this->wNumDoc+$this->wCat+$this->wModulo+$this->wTipo,46);$this->MultiCell($this->wHospInit,$this->hTitle,'Del','1','C');
    $this->SetXY($this->vX+=$this->wHospInit,46);$this->MultiCell($this->wHospEnd,$this->hTitle,'Al','1','C');
    $this->SetXY($this->vX+=$this->wHospEnd+$this->wQuantDay+$this->wMontoServ,46);$this->MultiCell($this->wPagR,$this->hTitle,'Recibo','1','C');
    $this->SetXY($this->vX+=$this->wPagR,46);$this->MultiCell($this->wpagMont,$this->hTitle,'Monto','1','C');
	}
	function Publicar($data){
		$categorias= array(
			"1"=>"S/E",
			"2"=>"PP",
			"3"=>"P",
			"4"=>"A",
			"5"=>"B",
			"6"=>"C",
			"7"=>"E",
			"10"=>"Nuevo",
			"11"=>"Continuador",
      "8"=>"Indigente",
      "12"=>"Categoría B",
      "13"=>"Categoría C",
    );
    $modulos = array(
      "MH"=>"Salud mental",
      "AD"=>"Adicciones"
    );
    $tipos=array(
      "P"=>"Parcial",
      "C"=>"Completo"
    );
    $tiempo=array(
      "D"=>"Días",
      "M"=>"Meses",
    );
		$this->SetFont('arial','',10);
		$y_marg = 10;
		$y=56;
    $this->SetY($y);
    $count=1;
    $this->vX=0;
		if(count($data)>0){
      $paciente=$data[0]["paciente"]["appat"]." ".$data[0]["paciente"]["apmat"].", ".$data[0]["paciente"]["nomb"]." [".$data[0]["paciente"]["docident"][0]["num"]."] ";
      $this->SetXY($this->vX+=$this->initX+38,26);$this->MultiCell($this->wName-38,$this->hTitle,$paciente,'0','L');
      $this->SetXY($this->vX+=$this->wName-38+30,26);$this->MultiCell($this->wHistClin-30,$this->hTitle,$data[0]["hist_cli"],'0','L');$this->vX=0;
      
			foreach($data as $i=>$item){
				if($this->GetY()>255){
					$this->AddPage();
					$y=56;
        }
				$this->SetXY($this->vX+=$this->initX,$y);$this->MultiCell($this->wNH,$this->hText,$i+1,'0','C');
        $this->SetXY($this->vX+=$this->wNH,$y);$this->MultiCell($this->wDate,$this->hText,Date::format($item['fecreg']->sec, 'd/m/Y'),'0','C');
        $this->SetXY($this->vX+=$this->wDate,$y);$this->MultiCell($this->wNumDoc,$this->hText,$item['paciente']['docident'][0]['num'],'0','C');
        $this->SetXY($this->vX+=$this->wNumDoc,$y);$this->MultiCell($this->wCat,$this->hText,$categorias[$item['categoria']],'0','C');
        $this->SetXY($this->vX+=$this->wCat,$y);$this->MultiCell($this->wModulo,$this->hText,$modulos[$item['modulo']],'0','C');
        $this->SetXY($this->vX+=$this->wModulo,$y);$this->MultiCell($this->wTipo,$this->hText,$tipos[$item['tipo_hosp']],'0','C');
        $this->SetXY($this->vX+=$this->wTipo,$y);$this->MultiCell($this->wHospInit,$this->hText,Date::format($item['fecini']->sec, 'd/m/Y'),'0','C');
        $this->SetXY($this->vX+=$this->wHospInit,$y);$this->MultiCell($this->wHospEnd,$this->hText,Date::format($item['fecfin']->sec, 'd/m/Y'),'0','C');
        if(isset($item['cant']) && isset($item['modalidad'])){
          $this->SetXY($this->vX+=$this->wHospEnd,$y);$this->MultiCell($this->wQuantDay,$this->hText,$item['cant']." ".$tiempo[$item['modalidad']],'0','C');
        }
        if(isset($item['importe'])){
            $this->SetXY($this->vX+=$this->wQuantDay,$y);$this->MultiCell($this->wMontoServ,$this->hText,"s/ ".$item['importe'],'0','R');
        }
        $tempY=$y;
        $tempX=$this->vX+=$this->wMontoServ;
        $tempTotalRecibos=0;
        if(isset($item['recibos'])){
          foreach($item['recibos'] as $recibo)
          {
            if($this->GetY()>255){
              $this->AddPage();
              $y=56;
            }
            $this->SetXY($tempX,$y);$this->MultiCell($this->wPagR,$this->hText,$recibo['serie']+$recibo['num'],'0','C');
            $this->SetXY($tempX+$this->wPagR,$y);$this->MultiCell($this->wpagMont,$this->hText,"s/ ".$recibo['total'],'0','R');
            $tempTotalRecibos+=$recibo['total'];
            $this->SetY($y+=5);
          }
          $this->SetXY($this->vX+=$this->wPagR+$this->wpagMont,$tempY);$this->MultiCell($this->wSaldo,$this->hText,"s/ ".($item["importe"]-$tempTotalRecibos),'0','R');
        }
        $this->SetY($tempY);
        $this->SetY($y+=5);
        $this->vX=0;
			}
		}
	}
	function Footer(){
    	$this->SetXY(220,-15);
    	$this->SetFont('Arial','I',8);
    	$this->SetTextColor(128);
    	$this->Cell(0,10,$this->PageNo(),0,0,'C');
    	$this->SetXY(29,-15);
    	$this->SetTextColor(128);
    	$this->Cell(0,10,date("d/m/Y"),0,0,'L');
	} 
}
$pdf=new hosp('P','mm',array(387,279));
$pdf->AliasNbPages();
$pdf->SetMargins(15,39.5,0); 
$pdf->SetTitle("reporte");
$pdf->SetAutoPageBreak(false,0); 
$pdf->Open();
$pdf->AddPage();
$pdf->Publicar($data);
$pdf->SetLeftMargin(25);
$pdf->Output();
?>