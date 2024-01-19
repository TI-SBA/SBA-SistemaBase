<?php
class Model_in_aler extends Model
{
    private $db;
    public $items;

    public function __construct()
    {
        global $f;
        $this->db = $f->datastore->in_contratos;
    }
    protected function get_Vencimientos()
    {
        global $f;
        $fec_actual = date('Y-m-d');
        $dia_actual = intval(date('d', strtotime($fec_actual)));
        $mes_actual = intval(date('m', strtotime($fec_actual)));
        $ano_actual = intval(date('Y', strtotime($fec_actual)));
        $filter = array(
            'pagos.mes'=> array(
                '$lte' => strval($mes_actual),
            ),
            'pagos.ano'=> array(
                '$lte' => strval($ano_actual),
            )   
        );
        $fields= array(
            '_id'=>1,
            'fecini'=>1,
            'fecdes'=>1,
            'inmueble.direccion'=>1,
            'inmueble.sublocal.nomb'=>1,
            'inmueble.tipo.nomb'=>1,
            'pagos.item'=>1,
            'pagos.mes'=>1,
            'pagos.ano'=>1,
            'pagos.estado'=>1,
            'pagos.total'=>1,
            'titular'=>1,
            'moneda'=>1,
            'importe'=>1,
        );
        /**
         * En caso que se nos de un limite de pagos que vencen en n dias
         * @var $this->params['dias']
         */
        if (isset($this->params['dias'])) {
            $dif_venci=(int) $this->params['dias'] ;
            $fec_anteri = date('Y-m-d', strtotime('-'.$dif_venci.' days', strtotime($fec_actual)));
            $dia_anteri = intval(date('d', strtotime($fec_anteri)));
            $mes_anteri = intval(date('m', strtotime($fec_anteri)));
            $ano_anteri = intval(date('Y', strtotime($fec_anteri)));
            $filter['pagos.mes']['$gte']=strval($mes_anteri);
            $filter['pagos.ano']['$gte']=strval($ano_anteri);
        }
        if (isset($this->params['sublocal'])) {
            $filter['inmueble.sublocal._id']=$this->params['sublocal'];
        }
        if (isset($this->params['inmueble'])) {
            $filter['inmueble._id']=$this->params['inmueble'];
        }
        $items = $f->model("in/cont")->params(array('filter'=>$filter,'fields'=>$fields))->get("all")->items;
        $total=0;
        foreach ($items as $c => $cont) {
            $cont['total']=0;
            $deudas=[];
            foreach ($cont['pagos'] as $p => $pago) {
                if (!isset($pago['estado']) || $pago['estado']==='P') {
                    $dia_pago = date('d', ($cont['fecini']->sec));
                    $mes_pago = strval(sprintf("%02d", $pago['mes']));
                    $ano_pago = strval($pago['ano']);
                    $fec_pago = $ano_pago.'-'.$mes_pago.'-'.$dia_pago;
                    $date_actual = new DateTime($fec_actual);
                    $date_pago = new DateTime($fec_pago);
                    $interval = (int)$date_actual->diff($date_pago)->format("%r%a");
                    /**
                     * En caso que se nos de un limite de pagos que vencen en n dias
                     * @var $this->params['dias']
                     */
                    if (isset($this->params['dias'])) {
                        if (0 <= $interval && $interval < $dif_venci) {
                            $deudor=array(
                              'contrato'=>$cont['_id'],
                              'titular' => $cont['titular'],
                              'moneda' => $cont['moneda'],
                              'importe' => (!isset($cont['estado'])) ? floatval($cont['importe']) : floatval($cont['importe']-$pago['total']),
                              'fecpago'=>new MongoDate(strtotime($fec_pago)),
                              'dias_rest'=>$interval,
                            );
                            $this->items[]=$deudor;
                        }
                    } else {
                        if (0 >= $interval) {
                            if (!isset($pago['estado']) || $pago['estado'] == "P") {
                                $deudor=array(
                                    'importe' => (!isset($cont['estado'])) ? floatval($cont['importe']) : floatval($cont['importe']-$pago['total']),
                                    'fecpago'=>new MongoDate(strtotime($fec_pago)),
                                    'dias_sobr'=>(-1)*$interval,
                                );
                                $deudas[]=$deudor;
                                $cont['total'] += $deudor['importe'];
                            }
                        }
                    }
                }
            }
            if (!isset($this->params['dias'])) {
                if (!empty($deudas)) {
                    $cont['pagos']=$deudas;
                    $this->items[]=$cont;
                }
            }
        }
    }
}
