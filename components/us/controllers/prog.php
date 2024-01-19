<?php
class Controller_us_prog extends Controller
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
        $f->response->json($f->model("us/prog")->params($params)->get("lista"));
    }
    public function execute_get()
    {
        global $f;
        $items = $f->model("us/prog")->params(array("_id"=>new MongoId($f->request->data['_id'])))->get("one")->items;
        $f->response->json($items);
    }
    public function execute_get_week()
    {
        global $f;
        $week = array(
            'ini'=>0,
            'fin'=>0
        );
        $week['ini'] = date('Y-m-d', strtotime("next monday"));
        $week['fin'] = date('Y-m-d', strtotime($week['ini']." + 6 days"));
        $f->response->json($week);
    }
    public function execute_save()
    {
        global $f;
        $data = $f->request->data;
        $data['fecmod'] = new MongoDate();
        $data['trabajador'] = $f->session->userDB;
        if (isset($data['ini'])) {
            $data['ini'] = new MongoDate(strtotime($data['ini']));
        }
        if (isset($data['fin'])) {
            $data['fin'] = new MongoDate(strtotime($data['fin']));
        }
        if (isset($data['desa'])) {
            foreach ($data['desa'] as $k=>$item) {
                $data['desa'][$k]['_id'] = new MongoId($item['_id']);
                $data['desa'][$k]['valor_nut'] = floatval($item['valor_nut']);
            }
        }
        if (isset($data['almu'])) {
            foreach ($data['almu'] as $k=>$item) {
                $data['almu'][$k]['_id'] = new MongoId($item['_id']);
                $data['almu'][$k]['valor_nut'] = floatval($item['valor_nut']);
            }
        }
        if (isset($data['cena'])) {
            foreach ($data['cena'] as $k=>$item) {
                $data['cena'][$k]['_id'] = new MongoId($item['_id']);
                $data['cena'][$k]['valor_nut'] = floatval($item['valor_nut']);
            }
        }
        if (isset($data['diet'])) {
            foreach ($data['diet'] as $k=>$item) {
                $data['diet'][$k]['_id'] = new MongoId($item['_id']);
                $data['diet'][$k]['valor_nut'] = floatval($item['valor_nut']);
            }
        }
        if (!isset($f->request->data['_id'])) {
            $data['fecreg'] = new MongoDate();
            $data['autor'] = $f->session->userDB;
            $data['estado'] = 'B';
            $model = $f->model("us/prog")->params(array('data'=>$data))->save("insert")->items;
            $f->model('ac/log')->params(array(
                'modulo'=>'US',
                'bandeja'=>'Programaci&oacute;n Semanal',
                'descr'=>'Se creó la Programaci&oacute;n <b>'.date('Y-m-d', strtotime($data['ini'])).'</b>.'
            ))->save('insert');
        } else {
            $vari = $f->model("us/prog")->params(array("_id"=>new MongoId($f->request->data['_id'])))->get("one")->items;
            $f->model('ac/log')->params(array(
                'modulo'=>'US',
                'bandeja'=>'Programaci&oacute;n Semanal',
                'descr'=>'Se actualizó la Programaci&oacute;n <b>'.date('Y-m-d', strtotime($vari['ini'])).'</b>.'
            ))->save('insert');
            $model = $f->model("us/prog")->params(array('_id'=>new MongoId($f->request->data['_id']),'data'=>$data))->save("update")->items;
        }
        $f->response->json($model);
    }
    public function execute_edit()
    {
        global $f;
        $f->response->view("us/prog.edit");
    }
    public function execute_details()
    {
        global $f;
        $f->response->view("us/prog.details");
    }
}
