<?php
class Model_in_cont extends Model
{
    private $db;
    public $items;

    public function __construct()
    {
        global $f;
        $this->db = $f->datastore->in_contratos;
    }
    protected function get_one()
    {
        global $f;
        if (isset($this->params['_id'])) {
            $this->items = $this->db->findOne(array('_id'=>$this->params['_id']));
        } else {
            $this->items = $this->db->findOne($this->params['data']);
        }
    }
    protected function get_count()
    {
        global $f;
        if (isset($this->params['_id'])) {
            $this->items = $this->db->count(array('_id'=>$this->params['_id']));
        } else {
            $this->items = $this->db->count($this->params['data']);
        }
    }
    protected function get_lista()
    {
        global $f;
        if (isset($this->params['texto'])) {
            if ($this->params["texto"]!='') {
                $f->library('helpers');
                $helper=new helper();
                $parametro = $this->params["texto"];
                $criteria = $helper->paramsSearch($this->params["texto"], array('titular.nomb','titular.appat','titular.apmat','titular.fullname','titular.docident.num','inmueble.direccion'));
            }
        } else {
            $criteria = array();
        }
        $sort = array('inmueble.direccion'=>1);
        if (isset($this->params['sort'])) {
            $sort = $this->params['sort'];
        }
        $data = $this->db->find($criteria)->skip($this->params['page_rows'] * ($this->params['page']-1))->sort($sort)->limit($this->params['page_rows']);
        foreach ($data as $obj) {
            $this->items[] = $obj;
        }
        $this->paging($this->params["page"], $this->params["page_rows"], $data->count());
    }
    protected function get_all()
    {
        global $f;
        $fields = array();
        $filter = array();
        if (isset($this->params['inmueble'])) {
            $filter['inmueble._id'] = $this->params['inmueble'];
        }
        if (isset($this->params['titular'])) {
            $filter['titular._id'] = $this->params['titular'];
        }
        if (isset($this->params['garantias'])) {
            $filter['garantias'] = array('$exists'=>true);
        }
        if (isset($this->params['fec'])) {
            $filter['fecfin'] = array('$gt'=>$this->params['fec']);
        }
        if (isset($this->params['estado'])) {
            if (sizeof($filter)==0) {
                $filter = array(
                    '$or'=>array(
                        array('pagos'=>array(
                            '$elemMatch'=>array('estado'=>'P')
                        )),
                        array('pagos'=>array(
                            '$elemMatch'=>array('estado'=>array('$exists'=>false))
                        ))
                    )
                );
            } else {
                $filter = array('$and'=>array(
                    $filter,
                    array('$or'=>array(
                        array('pagos'=>array(
                            '$elemMatch'=>array('estado'=>'P')
                        )),
                        array('pagos'=>array(
                            '$elemMatch'=>array('estado'=>array('$exists'=>false))
                        ))
                    ))
                ));
            }
        }
        if (isset($this->params['filter'])) {
            $filter = $this->params['filter'];
        }
        //print_r($filter);die();
        if (isset($this->params['fields'])) {
            $fields = $this->params['fields'];
        }
        $data = $this->db->find($filter, $fields)->sort(array('fecini'=>1));
        if (isset($this->params['limit'])) {
            $data->limit($this->params['limit']);
        }
        if (isset($this->params['skip'])) {
            $data->skip($this->params['skip']);
        }
        foreach ($data as $obj) {
            $this->items[] = $obj;
        }
    }
    protected function save_insert()
    {
        global $f;
        $this->db->insert($this->params['data']);
        $this->items = $this->params['data'];
    }
    protected function save_update()
    {
        global $f;
        unset($this->params['data']['_id']);
        //print_r($this->params);
        $options = array("w" => 0);
        $status = $this->db->update(array('_id'=>$this->params['_id']), array('$set'=>$this->params['data']), $options);
        //var_dump($status);
        $this->items = $this->db->findOne(array('_id'=>$this->params['_id']));
    }
    protected function save_custom()
    {
        global $f;
        $this->db->update(array( '_id' => $this->params['_id'] ), $this->params['data']);
    }


    protected function get_mora()
    {
        global $f;
        #Se asigna el horario con referencia a la zona horaria de Lima
        date_default_timezone_set('America/Lima');
        #Se inicializan u obtienen varialbes
        $data 		= $this->params;
        $mes 		= $data['mes'];
        $ano 		= $data['ano'];
        $fechaini 	= $data['fecini'];
        $fecha_pago = date('Y-m-d');
        $mora_total = 0;
        $cont_dias  = 0;
        $dias_ini   = intval(date('d', strtotime($fechaini)));
        $dias_pago  = intval(date('d', strtotime($fecha_pago)));
        //$inicio		= date('Y-m-d',strtotime($ano.'-'.$mes.'+1 month -'.$dias_ini));
        $inicio		= date('Y-m-d', strtotime($ano.'-'.$mes.'-'.$dias_ini));
        //$temp_d1    = new DateTime(date($fechaini));
        //$temp_d1    = new DateTime(date('Y-m-d',strtotime($fechaini.'+1 month')));
        $temp_d1 	= new DateTime($inicio);
        $temp_d2    = new DateTime($fecha_pago);
        //$temp_d3    = new DateTime(date('Y-m-d',strtotime($fecha_pago.'-1 month')));
        $interval   = $temp_d2->diff($temp_d1);
        //$anos_dif   = intval($interval->format('%y'));
        #Se calcula la diferencia de meses entre la fecha del ultimo pago realizado y la fecha actual de pago
        $meses_dif  = intval($interval->format('%m'))+(intval($interval->format('%y')*12));
        $ano_pago   = date('Y', strtotime($fecha_pago));
        $dias_tot   = intval($interval->format('%d'))+1;
        $dias_tem   = intval($interval->format('%d'));
        $dias_dif   = ($dias_pago) - ($dias_ini);
        //$dias_mes   = cal_days_in_month(CAL_GREGORIAN,intval(date('m',strtotime($fecha_pago))),intval(date('y',strtotime($fecha_pago))));
        //$dias_tot 	= 0;
        #Se asigna por defecto al mes un numero de 30 dias
        //$dias_mes   = 30;
        #Calcula los dias exactos de acuerdo al mes donde inicia el conteo de dias, incluyendo quincenas.
        //$mes_exac	= cal_days_in_month(CAL_GREGORIAN,intval(date('m',strtotime($inicio))),intval(date('y',strtotime($fecha_pago))));
        $mes_exac	= cal_days_in_month(CAL_GREGORIAN, intval(date('m', strtotime($inicio))), intval(date('y', strtotime($inicio))));
        #Calcula el numero de dias desde el ultimo pago realizado y la fecha actual de pago.
        //$dias_tot	= intval($interval->format('%a'));
        //$fecha_ini_pago = date('Y-m-d',strtotime($fecha_pago.' - '.($dias_tot+1).' days '));
        //$dias_cons = ($dias_pago + $dias_ini) - 3;
        #################################################################
        //	echo "<pre>";
        $fecha_ini_pago = date('Y-m-d', strtotime($fecha_pago.' - '.($dias_tem).' days '));
        if (($dias_dif)<=0&&$dias_ini!=1) {
            //$fecha_ini_pago = date('Y-m-d',mktime(0,0,0,intval(date('m',strtotime($fecha_pago.'-1 month'))),$dias_ini,intval(date('Y',strtotime($fecha_pago)))));
            //$temp_d3    = new DateTime($fecha_ini_pago);
            //$dias_dif = intval(((strtotime($fecha_pago))-(strtotime($fecha_ini_pago))));
            //$dias_dif = $dias_dif/(60*60*24);
            $dias_tem = $dias_tem+(30-$mes_exac);
            $dias_tot = $dias_tot+(30-$mes_exac);
            $dias_tem = $dias_tem-(30-cal_days_in_month(CAL_GREGORIAN, intval(date('m', strtotime($fecha_pago.'-1 month'))), intval(date('y', strtotime($fecha_pago)))));
            //var_dump($dias_tem);
            //var_dump(cal_days_in_month(CAL_GREGORIAN,intval(date('m',strtotime($fecha_pago.'-1 month'))),intval(date('y',strtotime($fecha_pago)))));
            $fecha_ini_pago = date('Y-m-d', strtotime($fecha_pago.' - '.($dias_tem).' days '));
        }
        ##################################################################
        //var_dump($dias_mes);
        //var_dump(intval($interval->format('%d')));
        //var_dump(intval($interval->format('%d')));
        //$dias_tot = $dias_tot + 3;
        //$fecha_ini_pago = date('Y-m-d',mktime(0,0,0,intval(date('m',strtotime($fecha_pago))),$dias_ini,intval(date('y',strtotime($fecha_pago)))));
        //{
        //$temp_d3    = new DateTime(date('Y-m-d',strtotime($fecha_ini_pago.'-1 month')));
        //$interval_quin = $temp_d3->diff($temp_d2);
        //$dias_mes   = cal_days_in_month(CAL_GREGORIAN,intval(date('m',strtotime($fecha_pago.'-1 month'))),intval(date('y',strtotime($fecha_pago))));
        //var_dump($dias_mes);
        //}
        #Hace el calculo en los ultimos 31 dias

        //$dias_tot++;
        /*
        var_dump($interval);
        var_dump($dias_tot);
        var_dump($dias_tem);
        var_dump($fecha_ini_pago);
        */
        $feriados   = array(
                $ano_pago."-01-01",
                $ano_pago."-05-01",
                $ano_pago."-06-29",
                $ano_pago."-07-28",
                $ano_pago."-08-30",
                $ano_pago."-10-08",
                //$ano_pago."-11-01",
                $ano_pago."-12-08",
                $ano_pago."-12-25");
        $sem_santa = array(
                "2018-03-29",
                "2018-03-30",
                "2019-04-18",
                "2019-04-19",
                "2020-04-09",
                "2020-04-10",
                "2021-04-01",
                "2021-04-02",
                "2022-04-14",
                "2022-04-15",
            );
        #Si existen feriados en el intervalo de tiempo se restan de los dias habiles estos son feriados constantes
        foreach ($feriados as $feriado) {
            if (date('Y-m-d', strtotime($feriado))>=($fecha_ini_pago)&&(date('Y-m-d', strtotime($feriado))<=date('Y-m-d', strtotime($fecha_pago)))) {
                $cont_dias--;
            }
        }
        #Igual que el caso anterior pero con Semana Santa o feriados que no mantienen fechas constantes
        foreach ($sem_santa as $sem_santa) {
            if (strtotime($sem_santa)>=strtotime($fecha_ini_pago)&&(strtotime($sem_santa)<=strtotime($fecha_pago))) {
                $cont_dias--;
            }
        }
        #Determina si existe mora en la fecha de pago
        for ($i=0;$i<=$dias_tem;$i++) {
            //	var_dump($fecha_ini_pago);
            $fecha_ini_pago = date("Y-m-d", strtotime("+1 day", strtotime($fecha_ini_pago)));
            #Excluye a los sabados y domingos del calculo de dias que poseen moras
            if (intval(date('N', strtotime($fecha_ini_pago)))<6) {
                $cont_dias++;
            }
        }
        /*
        while (strtotime($fecha_ini_pago) <= strtotime($fecha_pago))
        {
            $fecha_ini_pago = date ("Y-m-d", strtotime("+1 day", strtotime($fecha_ini_pago)));
            var_dump($fecha_ini_pago);
            if(intval(date('N',strtotime($fecha_ini_pago)))<6)
            {
            $cont_dias++;
            }
        }
        */
        //echo "<pre>";


        #Si la iteracion no esta en los ultimos 31 dias por defecto se iguala a 30
        /*
        else
        {
            $dias_tot = 30;
        }
        */

        /*
        for($i=$dias_ini;$i<=$dias_pago;$i++)
        {
            $fecha_comp = date('Y-m-d',mktime(0,0,0,intval(date('m',strtotime($fecha_pago))),$i,intval(date('y',strtotime($fecha_pago)))));

            if(intval(date('N',strtotime($fecha_comp)))>=6)
            {
                continue;
            }
            else
            {
                $cont_dias++;
            }
        }
        */
        #Determina si la mora excede los 5 dias habiles
        /*
        if ($cont_dias<6)
        {
            $dias_tot = 0;
        }
        */
        #Condicion para evitar que se cree una fecha extra en el reporte de liquidaciones
        //$mora=(2*$meses_dif)-($mes);
        //$mora_total = round((((($dias_tot)*2)/$dias_mes) + (($meses_dif)*2)),2);
        //$dias_tot = $meses_dif*30+($dias_dif+1);

        if ($dias_pago == $mes_exac) {
            if ($dias_tem+1>20) {
                $dias_tot = 30;
            } else {
                $dias_tot = 15;
            }
        }
        if ($cont_dias<6) {
            $dias_tot = 0;
        }
        $dias_tot = ($dias_tot + $meses_dif*30);
        //$dias_tot++;

        $mora_total = round(((((($dias_tot)*2))/30)), 2);
        if (strtotime($inicio)>strtotime($fecha_pago)) {
            $mora_total = 0;
        }
        //$mora = ;
        $this->items = array(
        'mora_porc'=>2,
        'mora' => $mora_total,
        'mes'=>$data['mes'],
        'ano'=>$data['ano'],
        'fecini'=>$data['fecini']
    );
        //var_dump($mora_total);
    }
protected function get_mora_final(){
	global $f;
	$data = $this->params;
	$mes = $data['mes'];
	$ano = $data['ano'];
	$fecini = $data['fecini'];
	if(isset($data['fec'])){
		$fec = $data['fec'];
	}else{
		$fec = time();
	}
	
	$fecha_pago = DateTime::createFromFormat('Y-m-d', $fecini);
	$fecha_hoy = new DateTime();

	$diferencia = $fecha_hoy -> diff($fecha_pago);
	$meses = ($diferencia->y * 12) + $diferencia->m;
	$meses += $diferencia->d / 30;
	$subMora = ($meses * 0.02) * 100;
	$mora = number_format($subMora,2);
	$this->items = array(
		'mora_porc'=>2,
		'mora' => $mora,
		'mes' => $data['mes'],
		'ano' => $data['ano'],
		'fecini' => $data['fecini']
	);

}
    protected function get_mora_legacy()
    {
        global $f;
        $data = $this->params;
        $mes = $data['mes'];
        $ano = $data['ano'];
        $fecini = $data['fecini'];
        if (isset($data['fec'])) {
            $fec = $data['fec'];
        } else {
            $fec = time();
        }

        //$fecha_actual = strtotime(date('Y-m-d'));
        $fecha_actual = $fec;
        $mes_actual = date('m', $fec);
        $dia_actual = date('d', $fec);
        $ano_actual = date('Y', $fec);
        $mora = 0;
        $dia = date('d', strtotime($fecini));

        $params = array(
        "mes"=>''.intval($mes_actual),
        "ano"=>$ano_actual,
        "tipo"=>''.intval($dia)
    );
        $limite = $f->datastore->in_calendario_pagos->findOne($params);
        if ($limite==null) {
            $fecha_limite = strtotime($ano_actual.'-'.$mes_actual.'-'.$dia.' +4 days');
            $dia_tmp = date('d', $fecha_limite);
            switch (date('w', $fecha_limite)) {
                case 0://domingo
                    $fecha_limite = strtotime($ano_actual.'-'.$mes_actual.'-'.$dia_tmp.' +2 days');
                    break;
                case 1://lunes
                    $fecha_limite = strtotime($ano_actual.'-'.$mes_actual.'-'.$dia_tmp.' +2 days');
                    break;
                case 2://martes
                    $fecha_limite = strtotime($ano_actual.'-'.$mes_actual.'-'.$dia_tmp.' +2 days');
                    break;
                case 3://miercoles
                    $fecha_limite = strtotime($ano_actual.'-'.$mes_actual.'-'.$dia_tmp.' +2 days');
                    break;
                case 4://jueves
                    $fecha_limite = strtotime($ano_actual.'-'.$mes_actual.'-'.$dia_tmp.' +1 days');
                    break;
                case 5://viernes
                    break;
                case 6://sabado
                    $fecha_limite = strtotime($ano_actual.'-'.$mes_actual.'-'.$dia_tmp.' +2 days');
                    break;
            }
        } else {
            $fecha_limite = strtotime($limite['dia']);
        }

        //echo date('Y-m-d',$fecha_limite);die();

        $meses_diferencia = strtotime($ano_actual.'-'.$mes_actual.'-'.$dia_actual)-strtotime($ano.'-'.$mes.'-'.$dia);
        //echo ($meses_diferencia/(60*60*24*30)).'<br />';
        $meses_diferencia = floor($meses_diferencia/(60*60*24*30));
        //echo $meses_diferencia;
        //var_dump($meses_diferencia);

        $inicio=date('Y-m-d', strtotime($ano.'-'.$mes.'-'.$dia));
        $fin=date('Y-m-d', strtotime($ano_actual.'-'.$mes_actual.'-'.$dia_actual));

        $datetime1=new DateTime($inicio);
        $datetime2=new DateTime($fin);

        # obtenemos la diferencia entre las dos fechas
        $interval=$datetime2->diff($datetime1);

        # obtenemos la diferencia en meses
        $intervalMeses=$interval->format("%m");

        # obtenemos la diferencia en anos y la multiplicamos por 12 para tener los meses
        $intervalAnos = $interval->format("%y")*12;

        //echo "hay una diferencia de ".($intervalMeses+$intervalAnos)." meses";

        $meses_diferencia = $intervalMeses+$intervalAnos;

        if ($datetime1>$datetime2) {
            $meses_diferencia = 0;
        }

        $mora_porc = 2;
        if (isset($data['contrato'])) {
            $contratos_mora_1 = array("568441b68e73587807001591",
            /*"568441b18e73587807001500",
            "568441988e73587807001289",
            "5684417c8e73587807000f81",
            "5684417a8e73587807000f54",
            "5684416d8e73587807000e19",
            "5684414c8e73587807000a99",
            "5684413f8e7358780700093d",
            "568441278e73587807000774",
            "568440dd8e73587807000082",
            "568440dd8e73587807000081",
            "568440e08e735878070000b8",
            "568440dd8e73587807000078",*/
            "568441288e7358780700077f");
            if (in_array($data['contrato']->{'$id'}, $contratos_mora_1)) {
                $mora_porc = 1;
            }
            //echo $data['contrato']->{'$id'};
            /*$contrato_model = $f->model('in/cont')->params(array('_id'=>$data['contrato']))->get('one')->items;
            if($contrato_model!=null){
                $mora_porc = floatval($contrato_model['porcentaje']);
                //echo $mora_porc;
            }else{
                $mora_porc = 0;
            }*/
            //if($data['contrato']->{'$id'}=="568440dd8e73587807000078" || $data['contrato']->{'$id'}=='568440e08e735878070000b8'){
                //$mora_porc = 1;
                //print_r($mora_porc);
            //}
        }

        $mora = $mora_porc*$meses_diferencia;

        if ($dia==1) {
            //echo 'caso 0 <br />';
            if ($fecha_actual>$fecha_limite) {
                //echo date("Y-m-d",$fecha_limite);
                $dias_diferencia = $fecha_actual-strtotime($ano_actual.'-'.$mes_actual.'-'.$dia);
                $dias_diferencia = floor($dias_diferencia/(60*60*24))+1;//+1
                //echo $dias_diferencia;
                //if($dias_diferencia==7){
                //	$dias_diferencia = 0;
                //}
                /*if($dias_diferencia==8){
                    $dias_diferencia = 0;
                }*/
                /***********************************************************************************/
                /*			SOLUCION MEDIANTE un seteo de variables					   */
                /***********************************************************************************/
                #Temporal para octubre

                //$dias = $f->datastore->in_dias_habiles->findOne($mes);
                //$dias = $f->datastore->in_dias_habiles->findOne(array('mes'=> $data['mes']));
                //if(!is_null($dias)){
                //	if($dias_diferencia<=$dias['dia_cobro']){
                //		$dias_diferencia = 0;
                //	}
                //}
                $mora+=($mora_porc/30)*$dias_diferencia;
            } else {
                //echo 'caso 0.1 <br />';
                $dias_diferencia = $fecha_actual-strtotime($ano_actual.'-'.$mes_actual.'-'.$dia);
                $dias_diferencia = floor($dias_diferencia/(60*60*24))+1;//+1
                //echo $dias_diferencia;
                if ($dias_diferencia==7) {
                    $dias_diferencia = 0;
                }
                /*if($dias_diferencia==8){
                    $dias_diferencia = 0;
                }*/
                if ($dias_diferencia==5) {
                    $dias_diferencia = 0;
                }
                $dias_diferencia = 0;
                $mora+=($mora_porc/30)*$dias_diferencia;
            }
        } else {
            if ($fecha_actual>$fecha_limite) {
                //echo 'caso 1 <br />';
                $dias_diferencia = $fecha_actual-strtotime($ano_actual.'-'.$mes_actual.'-'.$dia);
                $dias_diferencia = floor($dias_diferencia/(60*60*24))+1;//+1
                if ($dias_diferencia==6) {
                    $dias_diferencia=0;
                }
                $mora+=($mora_porc/30)*$dias_diferencia;
            //echo $dias_diferencia;
            } elseif ($fecha_actual<strtotime($ano_actual.'-'.$mes_actual.'-'.$dia)) {
                //echo 'caso 2 <br />';

                $anterior_fecha = strtotime($ano_actual.'-'.$mes_actual.'-'.$dia.' -1 month');
                //echo date("d/m/Y", $anterior_fecha);
                //echo "=====================================================";

                $dias_diferencia = $fecha_actual-$anterior_fecha;

                $dias_diferencia = floor($dias_diferencia/(60*60*24))+1;

                $aumentar_dias = 0;
                $disminuir_dias = 0;
                if (date("t", $anterior_fecha)==29) {
                    $aumentar_dias = 1;
                }
                if (date("t", $anterior_fecha)==28) {
                    $aumentar_dias = 2;
                }
                if (date("t", $anterior_fecha)==31) {
                    $disminuir_dias = 1;
                }
                $dias_diferencia+= $aumentar_dias;
                $dias_diferencia-= $disminuir_dias;
                if ($fecha_actual<strtotime($inicio)) {
                    $mora=0;
                } else {
                    $mora+=($mora_porc/30)*$dias_diferencia;
                }
            } else {
                //echo 'caso 3 <br />';
            }
        }

        if ($mora<0) {
            $mora = 0;
        }
        $this->items = array(
            'mora_porc'=>$mora_porc,
            'mora'=>$mora,
            'mes'=>$data['mes'],
            'ano'=>$data['ano'],
            'fecini'=>$data['fecini']
        );
    }
}
