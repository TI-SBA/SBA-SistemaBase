<?php
class Controller_cj_talo extends Controller
{
    public function execute_index()
    {
        global $f;
        $f->response->print("<div>");
        $f->response->view("ci/ci.search");
        $f->response->print('<button name="btnAgregar">Nuevo Talonario</button>');
        $f->response->print("</div>");
        $header_grid = array("cols"=>array(
            array( "nomb"=>"&nbsp;","w"=>50 ),
            array( "nomb"=>"Tipo","w"=>200 ),
            array( "nomb"=>"Serie","w"=>200 ),
            array( "nomb"=>"Numeraci&oacute;n","w"=>250 ),
            array( "nomb"=>"Caja","w"=>200 ),
            array( "nomb"=>"Registrado","w"=>150 )
        ));
        $f->response->view("ci/ci.grid", $header_grid);
        $f->response->print("<div class='div-bottom ui-dialog-buttonpane ui-widget-content'>");
        $f->response->view("ci/ci.grid.total");
        $f->response->view("ci/ci.grid.foot");
        $f->response->print("</div>");
    }
    public function execute_lista()
    {
        global $f;
        $model = $f->model("cj/talo")->params(array("page"=>$f->request->page,"page_rows"=>$f->request->page_rows))->get("lista");
        $f->response->json($model);
    }
    public function execute_search()
    {
        global $f;
        $model = $f->model("cj/talo")->params(array(
            "page"=>$f->request->page,
            "page_rows"=>$f->request->page_rows,
            "texto"=>$f->request->texto
        ))->get("search");
        $f->response->json($model);
    }
    public function execute_all()
    {
        global $f;
        $params = array();
        if (isset($f->request->data['tipo'])) {
            $params['tipo'] = $f->request->data['tipo'];
        }
        if (isset($f->request->data['caja'])) {
            $params['caja._id'] = new MongoId($f->request->data['caja']);
        }
        $model = $f->model('cj/talo')->params($params)->get('all');
        $f->response->json($model->items);
    }
    public function execute_get()
    {
        global $f;
        $model = $f->model("cj/talo")->params(array("_id"=>new MongoId($f->request->id)))->get("one");
        if (!isset($f->request->data['edit'])) {
            $cod = $f->model("cj/comp")->params(array('tipo'=>$model->items['tipo'],'serie'=>$model->items['serie'],'caja'=>$model->items['caja']['_id']))->get("num");
            if ($cod->items==null) {
                $cod->items=1;
            } else {
                $cod->items = intval($cod->items);
            }
            $model->items['actual'] = $cod->items;
        }
        $f->response->json($model->items);
    }
    public function execute_get_caja()
    {
        global $f;
        $model = $f->model("cj/talo")->params(array("caja"=>new MongoId($f->request->caja)))->get("by_caja");
        if (!is_null($model->items)) {
            foreach ($model->items as $i=>$item) {
                /*$cod = $f->model("cj/comp")->params(array(
                    'tipo'=>$item['tipo'],
                    'serie'=>$item['serie'],
                    'caja'=>$item['caja']['_id']
                ))->get("num");
                if($cod->items==null) $cod->items=$model->items[$i]['actual'];
                else $cod->items = intval($cod->items);
                $model->items[$i]['actual'] = $cod->items;
                */
            }
        }
        $f->response->json($model->items);
    }
    /*function execute_get_talos_2018(){
        global $f;
        $response = array(
            'status'=>'error',
            'message'=>'',
            'data'=>array()
        );
        $curl_handle = curl_init();
        //curl_setopt($curl_handle, CURLOPT_URL, 'https://127.0.0.1/conflux_see_server/index.php/api/config/talonario/format/json/?tipo='.$f->request->data['tipo']);
        curl_setopt($curl_handle, CURLOPT_URL, 'http://35.193.115.148/index.php/api/config/talonario/format/json/?tipo='.$f->request->data['tipo']);
        curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, 1);
        //curl_setopt($curl_handle, CURLOPT_POST, 1);
        curl_setopt($curl_handle, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl_handle, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl_handle, CURLOPT_HTTPHEADER, array(
            'X-CONFLUX-API-KEY: 1f3870be274f6c49b3e31a0c6728957f'
        ));
        $buffer = curl_exec($curl_handle);
        if($buffer===false){
            curl_close($curl_handle);
            $response['message'] = 'El servidor respondio un contenido vacio';
        }else{
            curl_close($curl_handle);
            $result = json_decode($buffer);
            $tmp_response = array();
            $tmp_response['status']=$result->status;
            $tmp_response['message']=$result->message;
            foreach ($result->rpta as $i => $talo_unic) {
                # CAJA ALQUILER INMUEBLES
                if($f->request->data['caja']=="51a752e14d4a132807000023"){
                    if($talo_unic->serie=="B001" || $talo_unic->serie=="F001"){
                        $tmp_response['rpta'][]=$talo_unic;
                    }
                }
                # CAJA ALQUILER FARMACIA
                elseif($f->request->data['caja']=="56cdef248e7358000700004d"){
                    if($talo_unic->serie=="B002" || $talo_unic->serie=="F002"){
                        $tmp_response['rpta'][]=$talo_unic;
                    }
                }
                # CAJA AGUA CHAPI
                elseif($f->request->data['caja']=="572b53b70121120409000033"){
                    if($talo_unic->serie=="B003" || $talo_unic->serie=="F003"){
                        $tmp_response['rpta'][]=$talo_unic;
                    }
                }
                # CAJA PLAYAS
                elseif($f->request->data['caja']=="5a4d1cb73e6037532b8b4567"){
                    if($talo_unic->serie=="B004" || $talo_unic->serie=="F004"){
                        $tmp_response['rpta'][]=$talo_unic;
                    }
                }
                elseif($talo_unic->serie!="B005") //unset($result->rpta[$i]);
                {
                    $tmp_response['rpta'][]=$talo_unic;
                }
                if($f->session->userDB['_id']->{'$id'}=="597f2a463e603743328b4569"){
                    if($talo_unic->serie=="B005" || $talo_unic->serie=="F005"){
                        $tmp_response['rpta'][]=$talo_unic;
                    }
                }
            }
            //print_r($f->request->data);
            //die();
            //print_r($result);

            //$response = $result;
            //$response = json_encode($tmp_response);
            $response = ($tmp_response);
            //echo $response
        }
        $f->response->json($response);
    }*/
    public function execute_get_talos()
    {
        global $f;
        $response = array(
            'status'=>'error',
            'message'=>'',
            'data'=>array()
        );
        switch ($f->request->data['tipo']) {
            case 'F':
                $toTipo = '01';
                break;
            case "B":
                $toTipo = '03';
                break;
            case 'NC':
                $toTipo = '07';
                break;
            default:
                $toTipo = '07';
                break;
        }
        $curl_handle = curl_init();
        //curl_setopt($curl_handle, CURLOPT_URL, 'http://35.193.115.148/index.php/api/config/talonario/format/json/?tipo='.$f->request->data['tipo']);
        curl_setopt($curl_handle, CURLOPT_URL, 'http://einvoice.conflux.pe/api/v/1/account_einvoice/sequence/?code='.$toTipo);
        curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl_handle, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl_handle, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl_handle, CURLOPT_HTTPHEADER, array(
            'Authorization: Token xfNxKIW6BjaCTS2CbeEhltuK2X6iIhWL'
        ));
        $buffer = curl_exec($curl_handle);
        if ($buffer===false) {
            curl_close($curl_handle);
            $response['message'] = 'El servidor de facturaci&oacute;n respondio un contenido vacio, no se pudo obtener los talonarios';
        } else {
            curl_close($curl_handle);
            $result = json_decode($buffer);
            $tmp_response = array('rpta'=>array());
            if ($result->kind=="base#sequences") {
                $tmp_response['status']="success";
                $tmp_response['message']="";
            }
            $caja = $f->model("cj/talo")->params(array("caja"=>new MongoId($f->request->data['caja'])))->get("by_caja")->items;
            foreach ($result->items as $i => $talo_unic) {
                if (!is_null($caja)) {
                    foreach ($caja as $t => $talo) {
                        if (isset($talo['serie'])) {
                            if ($talo['serie'] != '' && $talo_unic->prefix == ($talo['serie']).'-' ) {
                                switch ($talo_unic->code) {
                                    case '01':
                                        $toRptaTipo = 'F';
                                        break;
                                    case '03':
                                        $toRptaTipo = 'B';
                                        break;
                                    case '07':
                                        $toRptaTipo = 'NC';
                                        break;
                                    default:
                                        $toRptaTipo = 'NC';
                                        break;
                                }
                                $toRpta = array(
                                    'actual' => $talo_unic->number_next,
                                    'tipo' => $toRptaTipo,
                                    'serie' => $talo['serie'],
                                    '_id' => $talo_unic->id,
                                    'estado' => intval($talo_unic->active),
                                    'blocked' => '',
                                );
                                //$tmp_response['rpta'][]=$talo_unic;
                                $tmp_response['rpta'][]=$toRpta;
                            }
                        }
                    }
                }
            }
            
            //$response = $result;
            //$response = json_encode($tmp_response);
            $response = ($tmp_response);
        }
        $f->response->json($response);
    }
    public function execute_save()
    {
        global $f;
        $data = $f->request->data;
        if (isset($data['caja']['_id'])) {
            $data['caja']['_id'] = new MongoId($data['caja']['_id']);
        }
        if (isset($data['caja']['local']['_id'])) {
            $data['caja']['local']['_id'] = new MongoId($data['caja']['local']['_id']);
        }
        if (!isset($f->request->data['_id'])) {
            $data['fecreg'] = new MongoDate();
            $f->model("cj/talo")->params(array('data'=>$data))->save("insert");
            switch ($data['tipo']) {
                case 'F': $tipo = 'Factura'; break;
                case 'R': $tipo = 'Recibo de Caja'; break;
                case 'B': $tipo = 'Boleta de Venta'; break;
            }
            $f->model('ac/log')->params(array(
                'modulo'=>'CJ',
                'bandeja'=>'Talonarios',
                'descr'=>'Se cre&oacute; el talonario de <b>'.$tipo.'</b> con la serie <b>'.$data['serie'].'</b> para la caja <b>'.$data['caja']['nomb'].'</b> ubicada en <b>'.$data['caja']['local']['direccion'].'</b>'
            ))->save('insert');
        } else {
            $f->model("cj/talo")->params(array('_id'=>new MongoId($f->request->data['_id']),'data'=>$data))->save("update");
            $data = $f->model("cj/talo")->params(array("_id"=>new MongoId($f->request->data['_id'])))->get("one")->items;
            switch ($data['tipo']) {
                case 'F': $tipo = 'Factura'; break;
                case 'R': $tipo = 'Recibo de Caja'; break;
                case 'B': $tipo = 'Boleta de Venta'; break;
            }
            $f->model('ac/log')->params(array(
                'modulo'=>'CJ',
                'bandeja'=>'Talonarios',
                'descr'=>'Se actualiz&oacute; el talonario de <b>'.$tipo.'</b> con la serie <b>'.$data['serie'].'</b> para la caja <b>'.$data['caja']['nomb'].'</b> ubicada en <b>'.$data['caja']['local']['direccion'].'</b>'
            ))->save('insert');
        }
        $f->response->print("true");
    }
    public function execute_edit()
    {
        global $f;
        $f->response->view("cj/talo.edit");
    }
    public function execute_select()
    {
        global $f;
        $f->response->view("cj/talo.select");
    }
    public function execute_details()
    {
        global $f;
        $f->response->view("cj/talo.details");
    }
}
