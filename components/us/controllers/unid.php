<?php
class Controller_us_unid extends Controller
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
        $f->response->json($f->model("lg/unid")->params($params)->get("lista"));
    }
    public function execute_get()
    {
        global $f;
        $items = $f->model("lg/unid")->params(array("_id"=>new MongoId($f->request->data['_id'])))->get("one")->items;
        $f->response->json($items);
    }
    public function execute_all()
    {
        global $f;
        $items = $f->model("lg/unid")->get("all")->items;
        $f->response->json($items);
    }
    public function execute_save()
    {
        global $f;
        $data = $f->request->data;
        $data['fecmod'] = new MongoDate();
        $data['trabajador'] = $f->session->userDB;
        if (isset($data['cuenta'])) {
            $data['cuenta']['_id'] = new MongoId($data['cuenta']['_id']);
        }
        if (!isset($f->request->data['_id'])) {
            $data['fecreg'] = new MongoDate();
            $data['autor'] = $f->session->userDB;
            $data['estado'] = 'H';
            $model = $f->model("lg/unid")->params(array('data'=>$data))->save("insert")->items;
            $f->model('ac/log')->params(array(
                'modulo'=>'US',
                'bandeja'=>'Unidades',
                'descr'=>'Se creó la Unidad <b>'.$data['nomb'].'</b>.'
            ))->save('insert');
        } else {
            $vari = $f->model("lg/unid")->params(array("_id"=>new MongoId($f->request->data['_id'])))->get("one")->items;
            $f->model('ac/log')->params(array(
                'modulo'=>'US',
                'bandeja'=>'Unidades',
                'descr'=>'Se actualizó la Unidad <b>'.$vari['nomb'].'</b>.'
            ))->save('insert');
            $model = $f->model("lg/unid")->params(array('_id'=>new MongoId($f->request->data['_id']),'data'=>$data))->save("update")->items;
        }
        $f->response->json($model);
    }
    public function execute_edit()
    {
        global $f;
        $f->response->view("us/unid.edit");
    }
    public function execute_details()
    {
        global $f;
        $f->response->view("us/unid.details");
    }
}
