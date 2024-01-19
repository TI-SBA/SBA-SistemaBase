<?php
class Controller_re_repo extends Controller
{
    public function execute_index2()
    {
        global $f;
        $f->response->view("re/repo.view");
    }
    public function execute_dashboard()
    {
        global $f;
        $f->response->view("re/dashboard");
    }
    public function execute_temp_febrero()
    {
        global $f;
        $data = [];
        $data['recaudacion']['alquileres'][2019][1][1] = $this->get_month_recau('IN', new MongoDate(strtotime('2019-01-01')), new MongoDate(strtotime('2019-01-02')), 'A');
        $data['recaudacion']['alquileres'][2019][1][2] = $this->get_month_recau('IN', new MongoDate(strtotime('2019-01-02')), new MongoDate(strtotime('2019-01-03')), 'A');
        $data['recaudacion']['alquileres'][2019][1][3] = $this->get_month_recau('IN', new MongoDate(strtotime('2019-01-03')), new MongoDate(strtotime('2019-01-04')), 'A');
        $data['recaudacion']['alquileres'][2019][1][4] = $this->get_month_recau('IN', new MongoDate(strtotime('2019-01-04')), new MongoDate(strtotime('2019-01-05')), 'A');
        $data['recaudacion']['alquileres'][2019][1][5] = $this->get_month_recau('IN', new MongoDate(strtotime('2019-01-05')), new MongoDate(strtotime('2019-01-06')), 'A');
        $data['recaudacion']['alquileres'][2019][1][6] = $this->get_month_recau('IN', new MongoDate(strtotime('2019-01-06')), new MongoDate(strtotime('2019-01-07')), 'A');
        $data['recaudacion']['alquileres'][2019][1][7] = $this->get_month_recau('IN', new MongoDate(strtotime('2019-01-07')), new MongoDate(strtotime('2019-01-08')), 'A');
        $data['recaudacion']['alquileres'][2019][1][8] = $this->get_month_recau('IN', new MongoDate(strtotime('2019-01-08')), new MongoDate(strtotime('2019-01-09')), 'A');
        $data['recaudacion']['alquileres'][2019][1][9] = $this->get_month_recau('IN', new MongoDate(strtotime('2019-01-09')), new MongoDate(strtotime('2019-01-10')), 'A');
        $data['recaudacion']['alquileres'][2019][1][10] = $this->get_month_recau('IN', new MongoDate(strtotime('2019-01-10')), new MongoDate(strtotime('2019-01-11')), 'A');
        $data['recaudacion']['alquileres'][2019][1][11] = $this->get_month_recau('IN', new MongoDate(strtotime('2019-01-11')), new MongoDate(strtotime('2019-01-12')), 'A');
        $data['recaudacion']['alquileres'][2019][1][12] = $this->get_month_recau('IN', new MongoDate(strtotime('2019-01-12')), new MongoDate(strtotime('2019-01-13')), 'A');
        $data['recaudacion']['alquileres'][2019][1][13] = $this->get_month_recau('IN', new MongoDate(strtotime('2019-01-13')), new MongoDate(strtotime('2019-01-14')), 'A');
        $data['recaudacion']['alquileres'][2019][1][14] = $this->get_month_recau('IN', new MongoDate(strtotime('2019-01-14')), new MongoDate(strtotime('2019-01-15')), 'A');
        $data['recaudacion']['alquileres'][2019][1][15] = $this->get_month_recau('IN', new MongoDate(strtotime('2019-01-15')), new MongoDate(strtotime('2019-01-16')), 'A');
        $data['recaudacion']['alquileres'][2019][1][16] = $this->get_month_recau('IN', new MongoDate(strtotime('2019-01-16')), new MongoDate(strtotime('2019-01-17')), 'A');
        $data['recaudacion']['alquileres'][2019][1][17] = $this->get_month_recau('IN', new MongoDate(strtotime('2019-01-17')), new MongoDate(strtotime('2019-01-18')), 'A');
        $data['recaudacion']['alquileres'][2019][1][18] = $this->get_month_recau('IN', new MongoDate(strtotime('2019-01-18')), new MongoDate(strtotime('2019-01-19')), 'A');
        $data['recaudacion']['alquileres'][2019][1][19] = $this->get_month_recau('IN', new MongoDate(strtotime('2019-01-19')), new MongoDate(strtotime('2019-01-20')), 'A');
        $data['recaudacion']['alquileres'][2019][1][20] = $this->get_month_recau('IN', new MongoDate(strtotime('2019-01-20')), new MongoDate(strtotime('2019-01-21')), 'A');
        $data['recaudacion']['alquileres'][2019][1][21] = $this->get_month_recau('IN', new MongoDate(strtotime('2019-01-21')), new MongoDate(strtotime('2019-01-22')), 'A');
        $data['recaudacion']['alquileres'][2019][1][22] = $this->get_month_recau('IN', new MongoDate(strtotime('2019-01-22')), new MongoDate(strtotime('2019-01-23')), 'A');
        $data['recaudacion']['alquileres'][2019][1][23] = $this->get_month_recau('IN', new MongoDate(strtotime('2019-01-23')), new MongoDate(strtotime('2019-01-24')), 'A');
        $data['recaudacion']['alquileres'][2019][1][24] = $this->get_month_recau('IN', new MongoDate(strtotime('2019-01-24')), new MongoDate(strtotime('2019-01-25')), 'A');
        $data['recaudacion']['alquileres'][2019][1][25] = $this->get_month_recau('IN', new MongoDate(strtotime('2019-01-25')), new MongoDate(strtotime('2019-01-26')), 'A');
        $data['recaudacion']['alquileres'][2019][1][26] = $this->get_month_recau('IN', new MongoDate(strtotime('2019-01-26')), new MongoDate(strtotime('2019-01-27')), 'A');
        $data['recaudacion']['alquileres'][2019][1][27] = $this->get_month_recau('IN', new MongoDate(strtotime('2019-01-27')), new MongoDate(strtotime('2019-01-28')), 'A');
        $data['recaudacion']['alquileres'][2019][1][28] = $this->get_month_recau('IN', new MongoDate(strtotime('2019-01-28')), new MongoDate(strtotime('2019-01-29')), 'A');
        $data['recaudacion']['alquileres'][2019][1][29] = $this->get_month_recau('IN', new MongoDate(strtotime('2019-01-29')), new MongoDate(strtotime('2019-01-30')), 'A');
        $data['recaudacion']['alquileres'][2019][1][30] = $this->get_month_recau('IN', new MongoDate(strtotime('2019-01-30')), new MongoDate(strtotime('2019-01-31')), 'A');
        $data['recaudacion']['alquileres'][2019][1][31] = $this->get_month_recau('IN', new MongoDate(strtotime('2019-01-31')), new MongoDate(strtotime('2019-02-01')), 'A');

        $data['recaudacion']['alquileres'][2019][1]['total']=array_sum($data['recaudacion']['alquileres'][2019][1]);

        $data['recaudacion']['alquileres'][2019][2][1] = $this->get_month_recau('IN', new MongoDate(strtotime('2019-02-01')), new MongoDate(strtotime('2019-02-02')), 'A');
        $data['recaudacion']['alquileres'][2019][2][2] = $this->get_month_recau('IN', new MongoDate(strtotime('2019-02-02')), new MongoDate(strtotime('2019-02-03')), 'A');
        $data['recaudacion']['alquileres'][2019][2][3] = $this->get_month_recau('IN', new MongoDate(strtotime('2019-02-03')), new MongoDate(strtotime('2019-02-04')), 'A');
        $data['recaudacion']['alquileres'][2019][2][4] = $this->get_month_recau('IN', new MongoDate(strtotime('2019-02-04')), new MongoDate(strtotime('2019-02-05')), 'A');
        $data['recaudacion']['alquileres'][2019][2][5] = $this->get_month_recau('IN', new MongoDate(strtotime('2019-02-05')), new MongoDate(strtotime('2019-02-06')), 'A');
        $data['recaudacion']['alquileres'][2019][2][6] = $this->get_month_recau('IN', new MongoDate(strtotime('2019-02-06')), new MongoDate(strtotime('2019-02-07')), 'A');
        $data['recaudacion']['alquileres'][2019][2][7] = $this->get_month_recau('IN', new MongoDate(strtotime('2019-02-07')), new MongoDate(strtotime('2019-02-08')), 'A');
        $data['recaudacion']['alquileres'][2019][2][8] = $this->get_month_recau('IN', new MongoDate(strtotime('2019-02-08')), new MongoDate(strtotime('2019-02-09')), 'A');
        $data['recaudacion']['alquileres'][2019][2][9] = $this->get_month_recau('IN', new MongoDate(strtotime('2019-02-09')), new MongoDate(strtotime('2019-02-10')), 'A');
        $data['recaudacion']['alquileres'][2019][2][10] = $this->get_month_recau('IN', new MongoDate(strtotime('2019-02-10')), new MongoDate(strtotime('2019-02-11')), 'A');
        $data['recaudacion']['alquileres'][2019][2][11] = $this->get_month_recau('IN', new MongoDate(strtotime('2019-02-11')), new MongoDate(strtotime('2019-02-12')), 'A');
        $data['recaudacion']['alquileres'][2019][2][12] = $this->get_month_recau('IN', new MongoDate(strtotime('2019-02-12')), new MongoDate(strtotime('2019-02-13')), 'A');
        $data['recaudacion']['alquileres'][2019][2][13] = $this->get_month_recau('IN', new MongoDate(strtotime('2019-02-13')), new MongoDate(strtotime('2019-02-14')), 'A');
        $data['recaudacion']['alquileres'][2019][2][14] = $this->get_month_recau('IN', new MongoDate(strtotime('2019-02-14')), new MongoDate(strtotime('2019-02-15')), 'A');
        $data['recaudacion']['alquileres'][2019][2][15] = $this->get_month_recau('IN', new MongoDate(strtotime('2019-02-15')), new MongoDate(strtotime('2019-02-16')), 'A');
        $data['recaudacion']['alquileres'][2019][2][16] = $this->get_month_recau('IN', new MongoDate(strtotime('2019-02-16')), new MongoDate(strtotime('2019-02-17')), 'A');
        $data['recaudacion']['alquileres'][2019][2][17] = $this->get_month_recau('IN', new MongoDate(strtotime('2019-02-17')), new MongoDate(strtotime('2019-02-18')), 'A');
        $data['recaudacion']['alquileres'][2019][2][18] = $this->get_month_recau('IN', new MongoDate(strtotime('2019-02-18')), new MongoDate(strtotime('2019-02-19')), 'A');
        $data['recaudacion']['alquileres'][2019][2][19] = $this->get_month_recau('IN', new MongoDate(strtotime('2019-02-19')), new MongoDate(strtotime('2019-02-20')), 'A');
        $data['recaudacion']['alquileres'][2019][2][20] = $this->get_month_recau('IN', new MongoDate(strtotime('2019-02-20')), new MongoDate(strtotime('2019-02-21')), 'A');
        $data['recaudacion']['alquileres'][2019][2][21] = $this->get_month_recau('IN', new MongoDate(strtotime('2019-02-21')), new MongoDate(strtotime('2019-02-22')), 'A');
        $data['recaudacion']['alquileres'][2019][2][22] = $this->get_month_recau('IN', new MongoDate(strtotime('2019-02-22')), new MongoDate(strtotime('2019-02-23')), 'A');
        $data['recaudacion']['alquileres'][2019][2][23] = $this->get_month_recau('IN', new MongoDate(strtotime('2019-02-23')), new MongoDate(strtotime('2019-02-24')), 'A');
        $data['recaudacion']['alquileres'][2019][2][24] = $this->get_month_recau('IN', new MongoDate(strtotime('2019-02-24')), new MongoDate(strtotime('2019-02-25')), 'A');
        $data['recaudacion']['alquileres'][2019][2][25] = $this->get_month_recau('IN', new MongoDate(strtotime('2019-02-25')), new MongoDate(strtotime('2019-02-26')), 'A');
        $data['recaudacion']['alquileres'][2019][2][26] = $this->get_month_recau('IN', new MongoDate(strtotime('2019-02-26')), new MongoDate(strtotime('2019-02-27')), 'A');
        $data['recaudacion']['alquileres'][2019][2][27] = $this->get_month_recau('IN', new MongoDate(strtotime('2019-02-27')), new MongoDate(strtotime('2019-02-28')), 'A');
        $data['recaudacion']['alquileres'][2019][2][28] = $this->get_month_recau('IN', new MongoDate(strtotime('2019-02-28')), new MongoDate(strtotime('2019-03-01')), 'A');

        $data['recaudacion']['alquileres'][2019][2]['total']=array_sum($data['recaudacion']['alquileres'][2019][2]);

        header("Content-type:application/json");
        echo json_encode($data);
    }
    public function get_month_recau($modulo, $ini, $fin, $tipo_inm=null)
    {
        global $f;
        $rpta = 0;
        if ($tipo_inm!=null) {
            if ($tipo_inm=='P') {
                $rpta += $f->model('cj/comp')->params(array(
                  'moi'=>$ini,
                  'mq'=>$fin,
                ))->get('total_playas')->items;
            } else {
                $rpta += $f->model('cj/comp')->params(array(
                  'moi'=>$ini,
                  'mq'=>$fin,
                ))->get('total_alquiler')->items;
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
    public function execute_ingresos_inmuebles()
    {
        global $f;
        $hoy = strtotime('now');
        $hoyDate = date('Y-m-d', $hoy);
        $data= array(
          'anomes' => array(
            'legend' => [],
            'data' => [],
          ),
          'mesdia' => array(
            'legend' => [],
            'data' => [],
          ),
        );

        /**
        * Ingresos Mensuales en 2 años
        */
        $TWA = date('Y')-2;
        $twoYearsAgo = strtotime("first day of January ".$TWA);
        $TYADate = date('Y-m-d', $twoYearsAgo);
        $tempTYADate = $TYADate;
        $anomes = $twoYearsAgo;
        while ($anomes < $hoy) {
            $tempTYADate = date('Y-m-d', $anomes);
            $te = explode('-', $tempTYADate);
            $data['anomes']['legend'][intval($te[1]-1)] = $te[1];
            $data['anomes']['data'][(string)$te[0]][intval($te[1]-1)] = 0;
            $data['anomes']['data'][(string)$te[0]][intval($te[1]-1)] += round($f->model('cj/ecom')->params(array(
              'moi'=> new MongoDate($anomes),
              'mq'=> new MongoDate(strtotime($tempTYADate . 'first day of next month')),
            ))->get('total_alquiler')->items, 2);
            $data['anomes']['data'][(string)$te[0]][intval($te[1]-1)] += round($f->model('cj/comp')->params(array(
              'moi'=> new MongoDate($anomes),
              'mq'=> new MongoDate(strtotime($tempTYADate . 'first day of next month')),
            ))->get('total_alquiler')->items, 2);
            $anomes = strtotime($tempTYADate . 'first day of next month');
        }
        foreach ($data['anomes']['data'] as $ano => $values) {
            for ($i=0; $i < 12 ; $i++) {
                if (!isset($data['anomes']['data'][$ano][intval($i)])) {
                    $data['anomes']['data'][$ano][intval($i)] = 0;
                }
            }
        }


        /**
        * Ingresos Diarios en 2 Meses
        */
        $twoMonthsAgo = strtotime("-2 months");
        $TMADate = date('Y-m-d', $twoMonthsAgo);
        $tempTMADate = $TMADate;
        $mesdia = $twoMonthsAgo;
        while ($mesdia < $hoy) {
            $tempTMADate = date('Y-m-d', $mesdia);
            $te = explode('-', $tempTMADate);
            $data['mesdia']['legend'][intval($te[2]-1)] = $te[2];
            $data['mesdia']['data'][(string)$te[1]][intval($te[2]-1)] = 0;
            $data['mesdia']['data'][(string)$te[1]][intval($te[2]-1)] += round($f->model('cj/ecom')->params(array(
              'moi'=> new MongoDate($mesdia),
              'mq'=> new MongoDate(strtotime($tempTMADate . 'next day')),
            ))->get('total_alquiler')->items, 2);
            $data['mesdia']['data'][(string)$te[1]][intval($te[2]-1)] += round($f->model('cj/comp')->params(array(
              'moi'=> new MongoDate($mesdia),
              'mq'=> new MongoDate(strtotime($tempTMADate . 'next day')),
            ))->get('total_alquiler')->items, 2);
            $mesdia = strtotime($tempTMADate . 'next day');
        }


        header("Content-type:application/json");
        echo json_encode($data);
    }
}
