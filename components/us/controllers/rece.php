<?php
class Controller_us_rece extends Controller
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
        $f->response->json($f->model("us/rece")->params($params)->get("lista"));
    }
    public function execute_get()
    {
        global $f;
        $items = $f->model("us/rece")->params(array("_id"=>new MongoId($f->request->data['_id'])))->get("one")->items;
        $f->response->json($items);
    }
    public function execute_save()
    {
        global $f;
        $data = $f->request->data;
        $data['fecmod'] = new MongoDate();
        $data['trabajador'] = $f->session->userDB;
        if (isset($data['valor_nut'])) {
            $data['valor_nut'] = floatval($data['valor_nut']);
        }
        if (isset($data['ingredientes'])) {
            foreach ($data['ingredientes'] as $k => $equiv) {
                $data['ingredientes'][$k]['ingrediente']['unidad']['_id'] = new MongoId($equiv['ingrediente']['unidad']['_id']);
                $data['ingredientes'][$k]['ingrediente']['_id'] = new MongoId($equiv['ingrediente']['_id']);
                $data['ingredientes'][$k]['cant'] = floatval($equiv['cant']);
            }
        }
        if (!isset($f->request->data['_id'])) {
            $data['fecreg'] = new MongoDate();
            $data['autor'] = $f->session->userDB;
            $data['estado'] = 'H';
            $model = $f->model("us/rece")->params(array('data'=>$data))->save("insert")->items;
            $f->model('ac/log')->params(array(
                'modulo'=>'US',
                'bandeja'=>'Recetas',
                'descr'=>'Se creó la Receta <b>'.$data['descr'].'</b>.'
            ))->save('insert');
        } else {
            $vari = $f->model("us/rece")->params(array("_id"=>new MongoId($f->request->data['_id'])))->get("one")->items;
            $f->model('ac/log')->params(array(
                'modulo'=>'US',
                'bandeja'=>'Recetas',
                'descr'=>'Se actualizó la Receta <b>'.$vari['descr'].'</b>.'
            ))->save('insert');
            $model = $f->model("us/rece")->params(array('_id'=>new MongoId($f->request->data['_id']),'data'=>$data))->save("update")->items;
        }
        $f->response->json($model);
    }
    public function execute_edit()
    {
        global $f;
        $f->response->view("us/rece.edit");
    }
    public function execute_details()
    {
        global $f;
        $f->response->view("us/rece.details");
    }
}
