<?php
class Controller_us_pedi extends Controller
{
    public function execute_lista()
    {
        global $f;
        $params = array("page"=>$f->request->data['page'],"page_rows"=>$f->request->data['page_rows']);
        if (isset($f->request->data['texto'])) {
            if ($f->request->data['texto']!='') {
                $params['texto'] = $f->request->data['texto'];
            }
        }
        if (isset($f->request->data['sort'])) {
            $params['sort'] = array($f->request->data['sort']=>floatval($f->request->data['sort_i']));
        }
        $f->response->json($f->model("us/pedi")->params($params)->get("lista"));
    }
    public function execute_get()
    {
        global $f;
        $items = $f->model("us/pedi")->params(array("_id"=>new MongoId($f->request->data['_id'])))->get("one")->items;
        if (isset($f->request->data['prog'])) {
            $items['prog'] = $f->model('us/prog')->params(array('ini'=>$items['ini']))->get('ini')->items;
        }
        $f->response->json($items);
    }
    public function execute_get_week()
    {
        global $f;
        $oficina = $f->session->enti['roles']['trabajador']['oficina'];
        $last = $f->model('us/pedi')->params(array('oficina'=>$oficina['_id']))->get('last')->items;
        $week = array(
            'oficina'=>$oficina,
            'last'=>$last,
            'progra'=>array(),
            'ini'=>0,
            'fin'=>0
        );
        if ($last==null) {
            $week['ini'] = date('Y-m-d', strtotime("next monday"));
            $week['fin'] = date('Y-m-d', strtotime($week['ini']." + 6 days"));
        } else {
            $week['ini'] = date(strtotime($last['ini']));
            $week['fin'] = date(strtotime($last['fin']));
        }
        $week['progra'] = $f->model('us/prog')->params(array('ini'=>new MongoDate(strtotime($week['ini']))))->get('ini_aprob')->items;
        $f->response->json($week);
    }
    public function execute_get_repe()
    {
        global $f;
        $rpta = array(
            'ini'=>0,
            'fin'=>0,
            'pedidos'=>array()
        );
        $rpta['ini'] = date('Y-m-d', strtotime("next monday"));
        $rpta['fin'] = date('Y-m-d', strtotime($rpta['ini']." + 6 days"));
        $rpta['pedidos'] = $f->model('us/pedi')->params(array('ini'=>new MongoDate(strtotime($rpta['ini']))))->get('semana')->items;
        $f->response->json($rpta);
    }
    public function execute_save()
    {
        global $f;
        $data = $f->request->data;
        $data['fecmod'] = new MongoDate();
        $data['trabajador'] = $f->session->userDB;
        if (isset($data['oficina'])) {
            $data['oficina']['_id'] = new MongoId($data['oficina']['_id']);
        }
        if (isset($data['ini'])) {
            $data['ini'] = new MongoDate(strtotime($data['ini']));
        }
        if (isset($data['fin'])) {
            $data['fin'] = new MongoDate(strtotime($data['fin']));
        }
        if ($data['desa']) {
            foreach ($data['desa'] as $k=>$item) {
                $data['desa'][$k] = intval($item);
            }
        }
        if ($data['almu']) {
            foreach ($data['almu'] as $k=>$item) {
                $data['almu'][$k] = intval($item);
            }
        }
        if ($data['cena']) {
            foreach ($data['cena'] as $k=>$item) {
                $data['cena'][$k] = intval($item);
            }
        }
        if ($data['diet']) {
            foreach ($data['diet'] as $k=>$item) {
                $data['diet'][$k] = intval($item);
            }
        }

        if (!isset($f->request->data['_id'])) {
            $data['fecreg'] = new MongoDate();
            $data['autor'] = $f->session->userDB;
            $data['estado'] = 'C';
            $model = $f->model("us/pedi")->params(array('data'=>$data))->save("insert")->items;
            $f->model('ac/log')->params(array(
                'modulo'=>'US',
                'bandeja'=>'Pedidos',
                'descr'=>'Se creó el Pedido de <b>'.$data['oficina']['nomb'].'</b>.'
            ))->save('insert');
        } else {
            $vari = $f->model("us/pedi")->params(array("_id"=>new MongoId($f->request->data['_id'])))->get("one")->items;
            $f->model('ac/log')->params(array(
                'modulo'=>'US',
                'bandeja'=>'Pedidos',
                'descr'=>'Se actualizó el Pedido de <b>'.$vari['oficina']['nomb'].'</b>.'
            ))->save('insert');
            $model = $f->model("us/pedi")->params(array('_id'=>new MongoId($f->request->data['_id']),'data'=>$data))->save("update")->items;
        }
        $f->response->json($model);
    }
    public function execute_cerrar()
    {
        global $f;
        $data = $f->request->data;
        if (isset($data['ini'])) {
            $data['ini'] = new MongoDate(strtotime($data['ini']));
        }
        if (isset($data['fin'])) {
            $data['fin'] = new MongoDate(strtotime($data['fin']));
        }
        if ($data['desa']) {
            foreach ($data['desa'] as $k=>$item) {
                $data['desa'][$k] = intval($item);
            }
        }
        if ($data['almu']) {
            foreach ($data['almu'] as $k=>$item) {
                $data['almu'][$k] = intval($item);
            }
        }
        if ($data['cena']) {
            foreach ($data['cena'] as $k=>$item) {
                $data['cena'][$k] = intval($item);
            }
        }
        if ($data['diet']) {
            foreach ($data['diet'] as $k=>$item) {
                $data['diet'][$k] = intval($item);
            }
        }
        $data['fecreg'] = new MongoDate();
        $data['autor'] = $f->session->userDB;
        $data['estado'] = 'C';
        $model = $f->model("us/repe")->params(array('data'=>$data))->save("insert")->items;
        $f->model('ac/log')->params(array(
            'modulo'=>'US',
            'bandeja'=>'Recepci&oacute;n de Pedidos',
            'descr'=>'Se cerr&oacute; la Recepci&oacute;n de Pedidos para <b>'.date('Y-m-d', $data['ini']->sec).'</b>.'
        ))->save('insert');
        $f->model("us/pedi")->params(array('ini'=>$data['ini']))->save("atender");
        $f->response->json(true);
    }
    public function execute_edit()
    {
        global $f;
        $f->response->view("us/pedi.edit");
    }
    public function execute_details()
    {
        global $f;
        $f->response->view("us/pedi.details");
    }
    public function execute_repe()
    {
        global $f;
        $f->response->view("us/pedi.recepcion");
    }
}
