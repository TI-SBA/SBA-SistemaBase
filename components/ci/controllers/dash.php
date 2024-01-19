<?php
class Controller_ci_dash extends Controller
{
    public function execute_index()
    {
        global $f;
        $f->response->view("ci/dashboard");
    }
    public function execute_get()
    {
        global $f;
        $meses = array("","Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Setiembre","Octubre","Noviembre","Diciembre");
        $ini_mes = strtotime(date('Y-m-01'));
        $ini_mes_ = intval(date('m'));
        $data = array(
            'recaudacion'=>array(
                'legend'=>array(),
                'cementerio'=>array(0,0,0,0),
                'alquileres'=>array(0,0,0,0),
                'playas'=>array(0,0,0,0)
            ),
            'expedientes'=>array(0,0),
            'inhumaciones'=>array()
        );
        for ($i=8; $i>=0; $i--) {
            $tmp = $ini_mes_-$i;
            $tmp_a = intval(date('y'));
            if ($tmp<1) {
                $tmp = 12+$tmp;
                $tmp_a--;
            }
            $data['recaudacion']['legend'][] = $meses[$tmp].' \''.$tmp_a;
        }
        /******************************************************************************************
        * RECAUDACION CEMENTERIO
        ******************************************************************************************/
        $data['recaudacion']['cementerio'][0] = $this->get_month_recau('CM', new MongoDate(strtotime('-8 month', $ini_mes)), new MongoDate(strtotime('-7 month'), $ini_mes));
        $data['recaudacion']['cementerio'][0] += $this->get_month_rede(new MongoDate(strtotime('-8 month', $ini_mes)), new MongoDate(strtotime('-7 month'), $ini_mes));
        $data['recaudacion']['cementerio'][1] = $this->get_month_recau('CM', new MongoDate(strtotime('-7 month', $ini_mes)), new MongoDate(strtotime('-6 month'), $ini_mes));
        $data['recaudacion']['cementerio'][1] += $this->get_month_rede(new MongoDate(strtotime('-7 month', $ini_mes)), new MongoDate(strtotime('-6 month'), $ini_mes));
        $data['recaudacion']['cementerio'][2] = $this->get_month_recau('CM', new MongoDate(strtotime('-6 month', $ini_mes)), new MongoDate(strtotime('-5 month'), $ini_mes));
        $data['recaudacion']['cementerio'][2] += $this->get_month_rede(new MongoDate(strtotime('-6 month', $ini_mes)), new MongoDate(strtotime('-5 month'), $ini_mes));
        $data['recaudacion']['cementerio'][3] = $this->get_month_recau('CM', new MongoDate(strtotime('-5 month', $ini_mes)), new MongoDate(strtotime('-4 month'), $ini_mes));
        $data['recaudacion']['cementerio'][3] += $this->get_month_rede(new MongoDate(strtotime('-5 month', $ini_mes)), new MongoDate(strtotime('-4 month'), $ini_mes));
        $data['recaudacion']['cementerio'][4] = $this->get_month_recau('CM', new MongoDate(strtotime('-4 month', $ini_mes)), new MongoDate(strtotime('-3 month'), $ini_mes));
        $data['recaudacion']['cementerio'][4] += $this->get_month_rede(new MongoDate(strtotime('-4 month', $ini_mes)), new MongoDate(strtotime('-3 month'), $ini_mes));
        $data['recaudacion']['cementerio'][5] = $this->get_month_recau('CM', new MongoDate(strtotime('-3 month', $ini_mes)), new MongoDate(strtotime('-2 month'), $ini_mes));
        $data['recaudacion']['cementerio'][5] += $this->get_month_rede(new MongoDate(strtotime('-3 month', $ini_mes)), new MongoDate(strtotime('-2 month'), $ini_mes));
        $data['recaudacion']['cementerio'][6] = $this->get_month_recau('CM', new MongoDate(strtotime('-2 month', $ini_mes)), new MongoDate(strtotime('-1 month'), $ini_mes));
        $data['recaudacion']['cementerio'][6] += $this->get_month_rede(new MongoDate(strtotime('-2 month', $ini_mes)), new MongoDate(strtotime('-1 month'), $ini_mes));
        $data['recaudacion']['cementerio'][7] = $this->get_month_recau('CM', new MongoDate(strtotime('-1 month', $ini_mes)), new MongoDate($ini_mes));
        $data['recaudacion']['cementerio'][7] += $this->get_month_rede(new MongoDate(strtotime('-1 month', $ini_mes)), new MongoDate($ini_mes));
        $data['recaudacion']['cementerio'][8] = $this->get_month_recau('CM', new MongoDate($ini_mes), new MongoDate());
        $data['recaudacion']['cementerio'][8] += $this->get_month_rede(new MongoDate($ini_mes), new MongoDate());
        /******************************************************************************************
        * RECAUDACION INMUEBLES ALQUILERES
        ******************************************************************************************/
        $data['recaudacion']['alquileres'][0] = $this->get_month_recau('IN', new MongoDate(strtotime('-8 month', $ini_mes)), new MongoDate(strtotime('-7 month'), $ini_mes), 'A');
        $data['recaudacion']['alquileres'][1] = $this->get_month_recau('IN', new MongoDate(strtotime('-7 month', $ini_mes)), new MongoDate(strtotime('-6 month'), $ini_mes), 'A');
        $data['recaudacion']['alquileres'][2] = $this->get_month_recau('IN', new MongoDate(strtotime('-6 month', $ini_mes)), new MongoDate(strtotime('-5 month'), $ini_mes), 'A');
        $data['recaudacion']['alquileres'][3] = $this->get_month_recau('IN', new MongoDate(strtotime('-5 month', $ini_mes)), new MongoDate(strtotime('-4 month'), $ini_mes), 'A');
        $data['recaudacion']['alquileres'][4] = $this->get_month_recau('IN', new MongoDate(strtotime('-4 month', $ini_mes)), new MongoDate(strtotime('-3 month'), $ini_mes), 'A');
        $data['recaudacion']['alquileres'][5] = $this->get_month_recau('IN', new MongoDate(strtotime('-3 month', $ini_mes)), new MongoDate(strtotime('-2 month'), $ini_mes), 'A');
        $data['recaudacion']['alquileres'][6] = $this->get_month_recau('IN', new MongoDate(strtotime('-2 month', $ini_mes)), new MongoDate(strtotime('-1 month'), $ini_mes), 'A');
        $data['recaudacion']['alquileres'][7] = $this->get_month_recau('IN', new MongoDate(strtotime('-1 month', $ini_mes)), new MongoDate($ini_mes), 'A');
        $data['recaudacion']['alquileres'][8] = $this->get_month_recau('IN', new MongoDate($ini_mes), new MongoDate(), 'A');
        /******************************************************************************************
        * RECAUDACION INMUEBLES PLAYAS
        ******************************************************************************************/
        $data['recaudacion']['playas'][0] = $this->get_month_recau('IN', new MongoDate(strtotime('-8 month', $ini_mes)), new MongoDate(strtotime('-7 month'), $ini_mes), 'P');
        $data['recaudacion']['playas'][1] = $this->get_month_recau('IN', new MongoDate(strtotime('-7 month', $ini_mes)), new MongoDate(strtotime('-6 month'), $ini_mes), 'P');
        $data['recaudacion']['playas'][2] = $this->get_month_recau('IN', new MongoDate(strtotime('-6 month', $ini_mes)), new MongoDate(strtotime('-5 month'), $ini_mes), 'P');
        $data['recaudacion']['playas'][3] = $this->get_month_recau('IN', new MongoDate(strtotime('-5 month', $ini_mes)), new MongoDate(strtotime('-4 month'), $ini_mes), 'P');
        $data['recaudacion']['playas'][4] = $this->get_month_recau('IN', new MongoDate(strtotime('-4 month', $ini_mes)), new MongoDate(strtotime('-3 month'), $ini_mes), 'P');
        $data['recaudacion']['playas'][5] = $this->get_month_recau('IN', new MongoDate(strtotime('-3 month', $ini_mes)), new MongoDate(strtotime('-2 month'), $ini_mes), 'P');
        $data['recaudacion']['playas'][6] = $this->get_month_recau('IN', new MongoDate(strtotime('-2 month', $ini_mes)), new MongoDate(strtotime('-1 month'), $ini_mes), 'P');
        $data['recaudacion']['playas'][7] = $this->get_month_recau('IN', new MongoDate(strtotime('-1 month', $ini_mes)), new MongoDate($ini_mes), 'P');
        $data['recaudacion']['playas'][8] = $this->get_month_recau('IN', new MongoDate($ini_mes), new MongoDate(), 'P');
        /******************************************************************************************
        * EXPEDIENTES
        ******************************************************************************************/
        $expds = $f->model('td/expd')->params(array(
            'filter'=>array(
                'tupa'=>array('$exists'=>true),
                'fecreg'=>array(
                    '$gte'=>new MongoDate(strtotime('-3 month', $ini_mes)),
                    '$lte'=>new MongoDate()
                )
            ),
            'fields'=>array('estado'=>true)
        ))->get('custom')->items;
        if ($expds!=null) {
            foreach ($expds as $expd) {
                if ($expd['estado']=='C') {
                    $data['expedientes'][0]++;
                } else {
                    $data['expedientes'][1]++;
                }
            }
        }
        /******************************************************************************************
        * INHUMACIONES DEL DIA
        ******************************************************************************************/
        $inhumaciones = $f->model('cm/oper')->params(array(
            'filter'=>array(
                'inhumacion'=>array('$exists'=>true),
                'programacion.fecprog'=>array(
                    '$gte'=>new MongoDate(strtotime(date('Y-m-d'))),
                    '$lt'=>new MongoDate(strtotime(date('Y-m-d').' +3 day'))
                )
            ),
            'fields'=>array('ocupante'=>true,'programacion.fecprog'=>true,'inhumacion.funeraria'=>true,'inhumacion.puerta'=>true),
            'sort'=>array('programacion.fecprog'=>1)
        ))->get('custom')->items;
        if ($inhumaciones!=null) {
            foreach ($inhumaciones as $inhu) {
                $data['inhumaciones'][] = $inhu;
            }
        }
        $f->response->json($data);
    }
    public function get_month_recau($modulo, $ini, $fin, $tipo_inm=null)
    {
        global $f;
        $rpta = 0;
        $filter = array(
            'modulo'=>$modulo,
            'estado'=>'R',
            'fecreg'=>array(
                '$gte'=>$ini,
                '$lt'=>$fin
            )
        );
        if ($tipo_inm!=null) {
            if ($tipo_inm=='P') {
                $filter['playa'] = array('$exists'=>true);
            } else {
                $filter['playa'] = array('$exists'=>false);
            }
        }
        $comps = $f->model('cj/comp')->params(array(
            'filter'=>$filter,
            'fields'=>array('total'=>true)
        ))->get('custom')->items;
        if ($comps!=null) {
            foreach ($comps as $k=>$comp) {
                $rpta += floatval($comp['total']);
            }
        }
        if ($tipo_inm!=null) {
            if ($tipo_inm=='P') {
                $rpta += $f->model('cj/ecom')->params(array(
              'moi'=>$ini,
              'mq'=>$fin,
            ))->get('total_playas')->items;
            } else {
                $rpta += $f->model('cj/ecom')->params(array(
              'moi'=>$ini,
              'mq'=>$fin,
            ))->get('total_alquiler')->items;
            }
        }
        return $rpta;
    }
    public function get_month_rede($ini, $fin)
    {
        global $f;
        $rpta = 0;
        $comps = $f->model('cj/rede')->params(array(
            'filter'=>array(
                'fec_db'=>array(
                    '$gte'=>$ini,
                    '$lt'=>$fin
                )
            ),
            'fields'=>array('total'=>true)
        ))->get('custom')->items;
        if ($comps!=null) {
            foreach ($comps as $k=>$comp) {
                $rpta += floatval($comp['total']);
            }
        }
        return $rpta;
    }
}
