<?php
global $f;
$f->library('pdf');

class repo extends FPDF{
    function Header(){
        $y=10;
		$this->SetFont('Arial','B',7);
		$this->SetXY(5,5);$this->MultiCell(50,5,"SOCIEDAD DE BENEFICENCIA DE "."\n". "AREQUIPA",'0','C');		
		
		
    }



    function Publicar($recibo){

        $meses= array(
            '01'=>'Enero',
            '02'=>'Febrero',
            '03'=>'Marzo',
            '04'=>'Abril',
            '05'=>'Mayo',
            '06'=>'Junio',
            '07'=>'Julio',
            '08'=>'Agosto',
            '09'=>'Setiembre',
            '10'=>'Octubre',
            '11'=>'Noviembre',
            '12'=>'Dicembre'
        );

        $this->SetFont('Arial','B',12);
        $this->SetXY(110,5);$this->MultiCell(35,8,"S/. ".sprintf('%.2f',$recibo['monto']),'1','C','0');
        $this->SetFont('Arial','B',16);
        if($recibo['tipo'] == 'D'){
            $this->SetXY(0,22);$this->MultiCell(150,5,	"RECIBO DEFINITIVO N°: ".$recibo['num'],'0','C');
        }else{
            $this->SetXY(0,22);$this->MultiCell(150,5,	"RECIBO PROVISIONAL N°: ".$recibo['num'],'0','C');
        }
        $this->SetFont('Arial','',11);
        $this->SetXY(5,34);$this->MultiCell(150,5,"He recibido de la Caja de la Sociedad de Beneficencia Arequipa la suma de: ",'0','L');
        $this->SetXY(5,42);$this->MultiCell(150,5,"S/.".sprintf('%.2f',$recibo['monto']).' Soles','0','C');
        $this->SetXY(5,47);$this->MultiCell(150,8,"Por concepto: ".$recibo['concepto'],'0','L');
        $this->SetXY(5,65);$this->MultiCell(150,5,"Con cargo a dar cuenta en el curso del día. ",'0','L');
        $this->SetXY(70,79);$this->MultiCell(70,5,"AREQUIPA, ".date('d',$recibo["fecreg"]->sec).' de '.$meses[date('m',$recibo["fecreg"]->sec)].' del '.date('Y',$recibo["fecreg"]->sec),'0','R');
        $this->SetFont('Arial','',10);
        $this->SetXY(5,94);$this->MultiCell(150,5,"____________________________",'0','L');
        $this->SetXY(70,94);$this->MultiCell(150,5,"AUTOR: ".$recibo['autor']['nomb'].' '.$recibo['autor']['appat'].' '.$recibo['autor']['apmat'],'0','L');
        $this->SetFont('Arial','B',11);
        $this->SetXY(25,99);$this->MultiCell(20,5,"V° B°",'0','C');
		
        
    }
}



///$pdf=new repo('P','mm','A4');
$pdf = new repo('P','mm','A5');
$pdf->AliasNbPages();
$pdf->SetMargins(10,10,0); 
$pdf->SetTitle("reporte");
$pdf->SetAutoPageBreak(false,0); 
$pdf->Open();
$pdf->AddPage();
$pdf->Publicar($recibo);
$pdf->SetLeftMargin(25);
//header('Content-Type: application/pdf; charset=utf-8');
$pdf->Output();
?>