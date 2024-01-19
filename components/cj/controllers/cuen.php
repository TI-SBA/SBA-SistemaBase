<?php
class Controller_cj_cuen extends Controller
{
    public function execute_index()
    {
        global $f;
        $f->response->print("<div>");
        $f->response->view("ci/ci.search");
        $f->response->print('<label>Organizaci&oacute;n: </label><b><span name="orga"></span></b>&nbsp;');
        $f->response->print('<label>Estado: </label><select name="estado"><option value="">--</option><option value="P">Pendiente</option><option value="C">Cancelada</option><option value="V">Vencida</option><option value="X">Anulada</option></select>&nbsp;');
        $f->response->print('<button name="btnAgregar">Nueva Cuenta por Cobrar</button>');
        $f->response->print("</div>");
        $header_grid = array("cols"=>array(
            array( "nomb"=>"&nbsp;","w"=>10 ),
            array( "nomb"=>"&nbsp;","w"=>50 ),
            array( "nomb"=>"Servicio","w"=>250 ),
            array( "nomb"=>"Organizaci&oacute;n","w"=>250 ),
            array( "nomb"=>"Cliente","w"=>250 ),
            array( "nomb"=>"Total","w"=>150 ),
            array( "nomb"=>"Registrado por","w"=>250 ),
            array( "nomb"=>"Registrado","w"=>150 ),
            array( "nomb"=>"Vencimiento","w"=>150 )
        ));
        $f->response->view("ci/ci.grid", $header_grid);
        $f->response->print("<div class='div-bottom ui-dialog-buttonpane ui-widget-content'>");
        $f->response->view("ci/ci.grid.total");
        $f->response->view("ci/ci.grid.foot");
        $f->response->print("</div>");
    }
    public function execute_toda()
    {
        global $f;
        $f->response->print("<div>");
        $f->response->view("ci/ci.search");
        $f->response->print('<b><span name="orga">Organizaci&oacute;n</span></b>&nbsp;<div style="display:inline;" name="divOrga">
			<input type="radio" name="rbtnOrga" id="rbtnOrgaSelect" value="S"><label for="rbtnOrgaSelect">Seleccionar</label>
			<input type="radio" name="rbtnOrga" id="rbtnOrgaX" value="X" checked="checked"><label for="rbtnOrgaX">X</label>
		</div>&nbsp;');
        $f->response->print('<label>Estado: </label><select name="estado"><option value="">--</option><option value="P">Pendiente</option><option value="C">Cancelada</option><option value="V">Vencida</option><option value="X">Anulada</option></select>&nbsp;');
        $f->response->print('<button name="btnEmitir">Emitir Comprobante</button>');
        $f->response->print("</div>");
        $header_grid = array("cols"=>array(
            array( "nomb"=>"&nbsp;","w"=>10 ),
            array( "nomb"=>"&nbsp;","w"=>50 ),
            array( "nomb"=>"Servicio","w"=>250 ),
            array( "nomb"=>"Organizaci&oacute;n","w"=>250 ),
            array( "nomb"=>"Cliente","w"=>250 ),
            array( "nomb"=>"Total","w"=>150 ),
            array( "nomb"=>"Saldo","w"=>150 ),
            array( "nomb"=>"Registrado por","w"=>250 ),
            array( "nomb"=>"Registrado","w"=>150 ),
            array( "nomb"=>"Vencimiento","w"=>150 )
        ));
        $f->response->view("ci/ci.grid", $header_grid);
        $f->response->print("<div class='div-bottom ui-dialog-buttonpane ui-widget-content'>");
        $f->response->view("ci/ci.grid.total");
        $f->response->view("ci/ci.grid.foot");
        $f->response->print("</div>");
    }
    public function execute_inmu_old()
    {
        global $f;
        $f->response->print("<div>");
        $f->response->view("ci/ci.search");
        $f->response->print('<label>Estado: </label><select name="estado"><option value="">--</option><option value="P" selected>Pendiente</option><option value="C">Cancelada</option><option value="V">Vencida</option><option value="X">Anulada</option></select>&nbsp;');
        $f->response->print('<button name="btnEmitir">Emitir Comprobante</button>');
        $f->response->print('<button name="btnConfig">Configurar</button>');
        $f->response->print("</div>");
        $header_grid = array("cols"=>array(
            array( "nomb"=>"&nbsp;","w"=>10 ),
            array( "nomb"=>"&nbsp;","w"=>50 ),
            array( "nomb"=>"Arrendatario","w"=>170 ),
            array( "nomb"=>"Cuota","w"=>90 ),
            array( "nomb"=>"Detalle","w"=>250 ),
            array( "nomb"=>"Total","w"=>150 ),
            array( "nomb"=>"Registrado por","w"=>170 ),
            array( "nomb"=>"Registrado","w"=>150 ),
            array( "nomb"=>"Vencimiento","w"=>150 )
        ));
        $f->response->view("ci/ci.grid", $header_grid);
        $f->response->print("<div class='div-bottom ui-dialog-buttonpane ui-widget-content'>");
        $f->response->view("ci/ci.grid.total");
        $f->response->view("ci/ci.grid.foot");
        $f->response->print("</div>");
    }
    public function execute_inmu()
    {
        global $f;
        $f->response->view("cj/cuen.inmu");
    }
    public function execute_ceme()
    {
        global $f;
        $f->response->view("cj/cuen.ceme");
    }
    public function execute_lista()
    {
        global $f;
        $params = array(
            "page"=>$f->request->page,
            "page_rows"=>$f->request->page_rows
        );
        if (isset($f->request->data['estado'])) {
            if ($f->request->data['estado']!='') {
                $params['estado'] = $f->request->data['estado'];
            }
        }
        if (isset($f->request->data['orga'])) {
            $params['orga'] = new MongoId($f->request->data['orga']);
        }
        $model = $f->model("cj/cuen")->params($params)->get("lista");
        $f->response->json($model);
    }
    public function execute_lista_all()
    {
        global $f;
        $params = array(
            "page"=>$f->request->data['page'],
            "page_rows"=>$f->request->data['page_rows']
        );
        if (isset($f->request->data['texto'])) {
            if ($f->request->data['texto']!='') {
                $params['texto'] = $f->request->data['texto'];
            }
        }
        if (isset($f->request->data['sort'])) {
            $params['sort'] = array($f->request->data['sort']=>floatval($f->request->data['sort_i']));
        }
        if (isset($f->request->data['estado'])) {
            if ($f->request->data['estado']!='') {
                $params['estado'] = $f->request->data['estado'];
            }
        }
        if (isset($f->request->data['orga'])) {
            if ($f->request->data['orga']!='') {
                $params['orga'] = new MongoId($f->request->data['orga']);
            }
        }
        if (isset($f->request->data['texto'])) {
            if ($f->request->data['texto']!='') {
                $params['texto'] = $f->request->data['texto'];
            }
        }
        if (isset($f->request->data['inmueble'])) {
            if ($f->request->data['inmueble']!='') {
                $params['inmueble'] = new MongoId($f->request->data['inmueble']);
            }
        }
        if (isset($f->request->data['cliente'])) {
            if ($f->request->data['cliente']!='') {
                $params['cliente'] = new MongoId($f->request->data['cliente']);
            }
        }
        if (isset($f->request->data['espacio'])) {
            if ($f->request->data['espacio']!='') {
                $params['espacio'] = new MongoId($f->request->data['espacio']);
            }
        }
        $model = $f->model("cj/cuen")->params($params)->get("lista");
        foreach ($model->items as $k=>$cuenta) {
            $model->items[$k]['cliente'] = $f->model("mg/entidad")->params(array("_id"=>$cuenta['cliente']['_id']))->get("one")->items;
        }
        $f->response->json($model);
    }
    public function execute_search()
    {
        global $f;
        $params = array(
            "texto"=>$f->request->texto,
            "page"=>$f->request->page,
            "page_rows"=>$f->request->page_rows
        );
        if (isset($f->request->data['estado'])) {
            if ($f->request->data['estado']!='') {
                $params['estado'] = $f->request->data['estado'];
            }
        }
        if (isset($f->request->data['orga'])) {
            if ($f->request->data['orga']!='') {
                $params['orga'] = new MongoId($f->request->data['orga']);
            }
        }
        $model = $f->model("cj/cuen")->params($params)->get("search");
        $f->response->json($model);
    }
    public function execute_all()
    {
        global $f;
        $model = $f->model('cj/cuen')->get('all');
        $f->response->json($model->items);
    }
    public function execute_get()
    {
        global $f;
        $model = $f->model("cj/cuen")->params(array("_id"=>new MongoId($f->request->id)))->get("one")->items;
        $model['cliente'] = $f->model('mg/entidad')->params(array(
            '_id'=>$model['cliente']['_id'],
            'fields'=>array(
                'nomb'=>true,
                'appat'=>true,
                'apmat'=>true,
                'tipo_enti'=>true,
                'docident'=>true,
                'domicilios'=>true
            )
        ))->get('one')->items;
        $f->response->json($model);
    }
    public function execute_get_info_comp()
    {
        global $f;
        $cliente = $f->model("mg/entidad")->params(array("_id"=>$f->request->data['cliente']))->get("one")->items;
        $cuentas = array();
        foreach ($f->request->data['cuentas'] as $cuenta) {
            $cuentas[] = $f->model("cj/cuen")->params(array("_id"=>new MongoId($cuenta)))->get("one")->items;
        }
        foreach ($cuentas as $i=>$cuenta) {
            foreach ($cuenta['conceptos'] as $j=>$conc) {
                if (strpos($conc['concepto']['formula'], 'FECVEN')!==false) {
                    $cuentas[$i]['calcular'] = true;
                    $cuentas[$i]['conceptos'][$j]['calcular'] = true;
                }
            }
            if (isset($cuenta['operacion'])) {
                if ($cuenta['modulo']=='CM') {
                    $cuentas[$i]['operacion'] = $f->model('cm/oper')->params(array('_id'=>$cuenta['operacion']))->get('one2')->items;
                }
                if ($cuenta['modulo']=='IN') {
                    $cuentas[$i]['operacion'] = $f->model('in/oper')->params(array('_id'=>$cuenta['operacion']))->get('one')->items;
                }
            }
        }
        $cajas = array();
        if (isset($f->session->enti['roles']['cajero'])) {
            foreach ($f->session->enti['roles']['cajero']['cajas'] as $caja) {
                $cajas[] = $f->model("cj/caja")->params(array("_id"=>new MongoId($caja)))->get("one")->items;
            }
        }
        $vars = array();
        $varss = $f->model("mg/vari")->params(array("fields"=>array(
            'cod'=>true,
            'nomb'=>true,
            'valor'=>true
        )))->get("all");
        foreach ($varss->items as $item) {
            $vars[] = array('cod'=>$item['cod'],'valor'=>floatval($item['valor']));
        }
        $tasa = $f->model('mg/vari')->params(array('cod'=>'TC'))->get('by_cod')->items;
        $ctban = $f->model("ts/ctban")->get("all")->items;
        $f->response->json(array(
            'cliente'=>$cliente,
            'cuentas'=>$cuentas,
            'cajas'=>$cajas,
            'tasa'=>$tasa,
            'ctban'=>$ctban,
            'vars'=>$vars
        ));
    }
    public function execute_get_config_ceme()
    {
        global $f;
        $conf = $f->model('cj/conf')->params(array('cod'=>'CM'))->get('cod')->items;
        $f->response->json($conf);
    }
    public function execute_get_config_inmu()
    {
        global $f;
        $conf = $f->model('cj/conf')->params(array('cod'=>'IN'))->get('cod')->items;
        $conf['FILE_PLAYA'] = $f->model('ci/archivos')->params(array('_id'=>$conf['FILE_PLAYA']['_id']))->get('one')->items;
        $f->response->json($conf);
    }
    public function execute_get_config_hosp()
    {
        global $f;
        $conf = $f->model('cj/conf')->params(array('cod'=>'HO'))->get('cod')->items;
        $f->response->json($conf);
    }
    public function execute_get_config_pers()
    {
        global $f;
        $conf = $f->model('cj/conf')->params(array('cod'=>'PE'))->get('cod')->items;
        $f->response->json($conf);
    }
    public function execute_save_config_inmu()
    {
        global $f;
        $data = $f->request->data;
        $data['fecmod'] = new MongoDate();
        $data['trabajador'] = $f->session->userDB;
        $conf = $f->model('cj/conf')->params(array('cod'=>'IN'))->get('cod')->items;
        if (isset($data['MOR'])) {
            $data['MOR']['_id'] = new MongoId($data['MOR']['_id']);
        }
        if (isset($data['IGV'])) {
            $data['IGV']['_id'] = new MongoId($data['IGV']['_id']);
        }
        if (isset($data['FILE_PLAYA'])) {
            $data['FILE_PLAYA']['_id'] = new MongoId($data['FILE_PLAYA']['_id']);
        }
        if (isset($data['COBRANZA'])) {
            $data['COBRANZA']['_id'] = new MongoId($data['COBRANZA']['_id']);
        }
        if (!isset($conf)) {
            $f->model("cj/conf")->params(array('data'=>$data))->save("insert");
        } else {
            $f->model("cj/conf")->params(array('_id'=>$conf['_id'],'data'=>$data))->save("update");
        }
        $f->model('ac/log')->params(array(
            'modulo'=>'CJ',
            'bandeja'=>'Configuracion',
            'descr'=>'Se modifico la <b>Configuracion de Caja Inmuebles</b> (Concepto de Alquiler, Moras e IGV)'
        ))->save('insert');
    }
    public function execute_save_config_hosp()
    {
        global $f;
        $data = $f->request->data;
        $data['fecmod'] = new MongoDate();
        $data['trabajador'] = $f->session->userDB;
        $conf = $f->model('cj/conf')->params(array('cod'=>'HO'))->get('cod')->items;
        if (isset($data['HOSP'])) {
            $data['HOSP']['_id'] = new MongoId($data['HOSP']['_id']);
        }
        if (isset($data['AGRI'])) {
            $data['AGRI']['_id'] = new MongoId($data['AGRI']['_id']);
        }
        if (isset($data['GANA'])) {
            $data['GANA']['_id'] = new MongoId($data['GANA']['_id']);
        }
        if (isset($data['rehab'])) {
            foreach ($data['rehab'] as $key => $item) {
                $data['rehab'][$key]['_id'] = new MongoId($item['_id']);
            }
        }
        if (!isset($conf)) {
            $f->model("cj/conf")->params(array('data'=>$data))->save("insert");
        } else {
            $f->model("cj/conf")->params(array('_id'=>$conf['_id'],'data'=>$data))->save("update");
        }
        $f->model('ac/log')->params(array(
            'modulo'=>'CJ',
            'bandeja'=>'Configuracion',
            'descr'=>'Se modifico la <b>Configuracion de Caja Hospitalizaci&oacute;n</b> (Cobros Mois&eacute;s Heresi)'
        ))->save('insert');
        $f->response->print('true');
    }
    public function execute_get_config_agua()
    {
        global $f;
        $conf = $f->model('cj/conf')->params(array('cod'=>'AG'))->get('cod')->items;
        $alma = $f->model('lg/alma')->params(array('filter'=>array('aplicacion'=>'AG')))->get('all')->items;
        $conf['almacenes'] = $alma;
        $f->response->json($conf);
    }
    public function execute_get_config_farm()
    {
        global $f;
        $conf = $f->model('cj/conf')->params(array('cod'=>'FA'))->get('cod')->items;
        $f->response->json($conf);
    }
    public function execute_get_config_logi()
    {
        global $f;
        $conf = $f->model('cj/conf')->params(array('cod'=>'LG'))->get('cod')->items;
        $f->response->json($conf);
    }
    public function execute_save_config_logi()
    {
        global $f;
        $data = $f->request->data;
        $data['fecmod'] = new MongoDate();
        $data['trabajador'] = $f->session->userDB;
        $conf = $f->model('cj/conf')->params(array('cod'=>'LG'))->get('cod')->items;
        if (isset($data['AGUA'])) {
            $data['AGUA']['_id'] = new MongoId($data['AGUA']['_id']);
        }
        if (isset($data['FARM'])) {
            $data['FARM']['_id'] = new MongoId($data['FARM']['_id']);
        }
        if (!isset($conf)) {
            $f->model("cj/conf")->params(array('data'=>$data))->save("insert");
        } else {
            $f->model("cj/conf")->params(array('_id'=>$conf['_id'],'data'=>$data))->save("update");
        }
        $f->model('ac/log')->params(array(
            'modulo'=>'CJ',
            'bandeja'=>'Configuracion',
            'descr'=>'Se modifico la <b>Configuracion de Logistica</b> (Almacenes)'
        ))->save('insert');
    }
    public function execute_get_misa(){
        global $f;
        $misas = $f->model("cj/cuen")->params(array('tipo' => 'MISA'))->get("custom_misa")->items;
        $f->response->json( $misas );
    }
    public function execute_save()
    {
        global $f;
        $data = $f->request->data;
        if (isset($data['operacion'])) {
            $data['operacion'] = new MongoId($data['operacion']);
        }
        if (isset($data['cliente']['_id'])) {
            $data['cliente']['_id'] = new MongoId($data['cliente']['_id']);
        }
        if (isset($data['servicio']['_id'])) {
            $data['servicio']['_id'] = new MongoId($data['servicio']['_id']);
        }
        if (isset($data['servicio']['organizacion']['_id'])) {
            $data['servicio']['organizacion']['_id'] = new MongoId($data['servicio']['organizacion']['_id']);
        }
        if (isset($data['fecven'])) {
            $data['fecven'] = new MongoDate(strtotime($data['fecven']));
        }
        if (isset($data['evento'])) {
            $data['evento'] = new MongoDate(strtotime($data['evento']));
        }
        if (isset($data['saldo'])) {
            $data['saldo'] = floatval($data['saldo']);
        }
        if (isset($data['total'])) {
            $data['total'] = floatval($data['total']);
        }
        if (isset($data['espacio']['_id'])) {
            $data['espacio']['_id'] = new MongoId($data['espacio']['_id']);
        }
        if (isset($data['contrato'])) {
            $data['contrato'] = new MongoId($data['contrato']);
        }
        if (isset($data['inmueble']['_id'])) {
            $data['inmueble']['_id'] = new MongoId($data['inmueble']['_id']);
        }
        if (isset($data['inmueble']['tipo']['_id'])) {
            $data['inmueble']['tipo']['_id'] = new MongoId($data['inmueble']['tipo']['_id']);
        }
        if (isset($data['inmueble']['sublocal']['_id'])) {
            $data['inmueble']['sublocal']['_id'] = new MongoId($data['inmueble']['sublocal']['_id']);
        }
        if (isset($data['inmueble']['ubic']['local']['_id'])) {
            $data['inmueble']['ubic']['local']['_id'] = new MongoId($data['inmueble']['ubic']['local']['_id']);
        }
        foreach ($data['conceptos'] as $j=>$con) {
            if (isset($con['concepto']['_id'])) {
                $data['conceptos'][$j]['concepto']['_id'] = new MongoId($con['concepto']['_id']);
            }
            if (isset($con['concepto']['_id'])) {
                $data['conceptos'][$j]['concepto']['_id'] = new MongoId($con['concepto']['_id']);
            }
            if (isset($con['concepto']['clasificador']['_id'])) {
                $data['conceptos'][$j]['concepto']['clasificador']['_id'] = new MongoId($con['concepto']['clasificador']['_id']);
            }
            if (isset($con['concepto']['clasificador']['cuenta']['_id'])) {
                $data['conceptos'][$j]['concepto']['clasificador']['cuenta']['_id'] = new MongoId($con['concepto']['clasificador']['cuenta']['_id']);
            }
            if (isset($con['concepto']['cuenta']['_id'])) {
                $data['conceptos'][$j]['concepto']['cuenta']['_id'] = new MongoId($con['concepto']['cuenta']['_id']);
            }
            if (isset($con['saldo'])) {
                $data['conceptos'][$j]['saldo'] = floatval($con['saldo']);
            }
            if (isset($con['total'])) {
                $data['conceptos'][$j]['total'] = floatval($con['total']);
            }
        }
        if (!isset($f->request->data['_id'])) {
            $data['fecreg'] = new MongoDate();
            $data['estado'] = 'P';
            $data['autor'] = $f->session->userDB;
            $f->model("cj/cuen")->params(array('data'=>$data))->save("insert");
            $enti = $data['cliente']['nomb'];
            if ($data['cliente']['tipo_enti']=='P') {
                $enti .= ' '.$data['cliente']['appat'].' '.$data['cliente']['apmat'];
            }
            if ($data['moneda']=='S') {
                $total = 'S/.'.$data['total'];
            } else {
                $total = '$'.$data['total'];
            }
            $f->model('ac/log')->params(array(
                'modulo'=>'CJ',
                'bandeja'=>'Cuentas por Cobrar',
                'descr'=>'Se cre&oacute; la <b>Cuenta por Cobrar</b> a nombre de <b>'.$enti.'</b>.'.
                    ' por el servicio de <b>'.$data['servicio']['nomb'].'</b>'.
                    ' de la organizaci&oacute;n <b>'.$data['servicio']['organizacion']['nomb'].'</b>'.
                    ' por un total de <b>'.$total.'</b>'
            ))->save('insert');
        } else {
            $f->model("cj/cuen")->params(array('_id'=>new MongoId($f->request->data['_id']),'data'=>$data))->save("update");
            $vari = $f->model("cj/cuen")->params(array("_id"=>new MongoId($f->request->vari['_id'])))->get("one")->items;
            $enti = $vari['cliente']['nomb'];
            if ($vari['cliente']['tipo_enti']=='P') {
                $enti .= ' '.$vari['cliente']['appat'].' '.$vari['cliente']['apmat'];
            }
            if ($vari['moneda']=='S') {
                $total = 'S/.'.$vari['total'];
            } else {
                $total = '$'.$vari['total'];
            }
            if (isset($data['estado'])) {
                if ($data['estado']=='X') {
                    $word = 'anul&oacute;';
                }
                $f->model('ac/log')->params(array(
                    'modulo'=>'CJ',
                    'bandeja'=>'Cuentas por Cobrar',
                    'descr'=>'Se '.$word.' la <b>Cuenta por Cobrar</b> a nombre de <b>'.$enti.'</b>.'.
                        ' por el servicio de <b>'.$vari['servicio']['nomb'].'</b>'.
                        ' de la organizaci&oacute;n <b>'.$vari['servicio']['organizacion']['nomb'].'</b>'.
                        ' por un total de <b>'.$total.'</b>'
                ))->save('insert');
            } else {
                $f->model('ac/log')->params(array(
                    'modulo'=>'CJ',
                    'bandeja'=>'Cuentas por Cobrar',
                    'descr'=>'Se actualiz&oacute; la <b>Cuenta por Cobrar</b> a nombre de <b>'.$enti.'</b>.'.
                        ' por el servicio de <b>'.$vari['servicio']['nomb'].'</b>'.
                        ' de la organizaci&oacute;n <b>'.$vari['servicio']['organizacion']['nomb'].'</b>'.
                        ' por un total de <b>'.$total.'</b>'
                ))->save('insert');
            }
        }
        $f->response->print("true");
    }
    public function execute_save_comp()
    {
        global $f;
        $data = $f->request->data;
        $trabajador = $f->session->userDB;
        if ($trabajador['_id']==new MongoId('57f3e8eb8e7358b007000042')) {
            $trabajador = array(
                '_id'=>new MongoId('56fd3c148e73584c07000062'),
                 "tipo_enti"=>"P",
                 "nomb"=>"PEDRO PERCY",
                 "apmat"=>"REVILLA",
                 "appat"=>"AMESQUITA",
                 "cargo"=>array(
                   "funcion"=>"APOYO ADMINISTRATIVO",
                   "organizacion"=>array(
                     "_id"=>new MongoId("51a50f0f4d4a13c409000013"),
                     "nomb"=>"Unidad de Cementerio y Servicios Funerarios",
                     "componente"=>array(
                       "_id"=>new MongoId("51e99d7a4d4a13c404000016"),
                       "nomb"=>"SERVICIOS FUNERARIOS Y DE CEMENTERIO",
                       "cod"=>"001"
                    ),
                     "actividad"=>array(
                       "_id"=>new MongoId("51e996044d4a13440a00000e"),
                       "nomb"=>"SERVICIOS FUNERARIOS Y DE CEMENTERIO",
                       "cod"=>"5001194"
                    )
                  )
                )
            );
        }
        $cliente = $data['cliente'];
        $cliente['_id'] = new MongoId($cliente['_id']);
        $caja = $data['caja'];
        $caja['_id'] = new MongoId($caja['_id']);
        $caja['local']['_id'] = new MongoId($caja['local']['_id']);
        $items = $data['items'];
        foreach ($items as $i=>$item) {
            unset($items[$i]['total']);
            if (isset($item['cuenta_cobrar'])) {
                $items[$i]['cuenta_cobrar']['_id'] = new MongoId($item['cuenta_cobrar']['_id']);
                $items[$i]['cuenta_cobrar']['servicio']['_id'] = new MongoId($item['cuenta_cobrar']['servicio']['_id']);
                $items[$i]['cuenta_cobrar']['servicio']['organizacion']['_id'] = new MongoId($item['cuenta_cobrar']['servicio']['organizacion']['_id']);
            }
            if (isset($item['conceptos'])) {
                foreach ($item['conceptos'] as $j=>$conc) {
                    if (isset($conc['concepto'])) {
                        if (gettype($conc['concepto'])=='array') {
                            $items[$i]['conceptos'][$j]['concepto']['_id'] = new MongoId($conc['concepto']['_id']);
                        }
                    }
                    if (isset($conc['cuenta'])) {
                        if (isset($conc['cuenta']['_id'])) {
                            $items[$i]['conceptos'][$j]['cuenta']['_id'] = new MongoId($conc['cuenta']['_id']);
                        }
                    }
                    if (isset($conc['monto'])) {
                        $items[$i]['conceptos'][$j]['monto'] = floatval($conc['monto']);
                    }
                }
            }
        }
        $efec = $data['efectivos'];
        foreach ($efec as $i=>$ef) {
            $efec[$i]['monto'] = floatval($ef['monto']);
        }
        if (isset($data['vouchers'])) {
            $vouchers = $data['vouchers'];
            foreach ($vouchers as $i=>$vou) {
                $vouchers[$i]['monto'] = floatval($vou['monto']);
                $vouchers[$i]['cuenta_banco']['_id'] = new MongoId($vou['cuenta_banco']['_id']);
            }
        }
        if (!isset($data['fecreg'])) {
            if (isset($data['fecemi'])) {
                $data['fecreg'] = $data['fecemi'];
            }
            if (isset($data['fec'])) {
                $data['fecreg'] = $data['fec'];
            } else {
                $data['fecreg'] = new MongoDate();
            }
        }
        $comp = array(
            'modulo'=>'CM',
            'fecreg'=>new MongoDate(strtotime($data['fecreg'])),
            'fecreal'=>new MongoDate(),
            'estado'=>'R',
            'periodo'=>date('ym00'),
            'autor'=>$trabajador,
            'cliente'=>$cliente,
            'caja'=>$caja,
            'tipo'=>$data['tipo'],
            'serie'=>$data['serie'],
            'num'=>floatval($data['num']),
            'moneda'=>$data['moneda'],
            'observ'=>$data['observ'],
            'items'=>$items,
            'total'=>floatval($data['total']),
            'tc'=>floatval($data['tc']),
            'efectivos'=>$efec
        );
        if (isset($data['modulo'])) {
            $comp['modulo'] = $data['modulo'];
        }
        if ($data['moneda']=='D') {
            $comp['total_soles'] = floatval($data['total_soles']);
        }
        if (isset($vouchers)) {
            $comp['vouchers'] = $vouchers;
        }
        if (isset($data['contrato'])) {
            $comp['contrato'] = new MongoId($data['contrato']);
        }
        if (isset($data['acta_conciliacion'])) {
            $comp['acta_conciliacion'] = new MongoId($data['acta_conciliacion']);
        }
        if (isset($data['inmueble'])) {
            $comp['inmueble'] = $data['inmueble'];
            $comp['inmueble']['_id'] = new MongoId($comp['inmueble']['_id']);
        }
        if (isset($data['alquiler'])) {
            $comp['alquiler'] = true;
        }
        if (isset($data['compensacion'])) {
            $comp['compensacion'] = true;
        }
        if (isset($data['acta'])) {
            $comp['acta'] = true;
        }
        if (isset($data['parcial'])) {
            $comp['parcial'] = true;
        }
        if (isset($data['combinacion_alq'])) {
            $comp['combinacion_alq'] = true;
        }
        if (isset($data['valor_igv'])) {
            $comp['valor_igv'] = floatval($data['valor_igv']);
        }
        if (isset($data['igv'])) {
            $comp['igv'] = floatval($data['igv']);
        }
        if (isset($data['subtotal'])) {
            $comp['subtotal'] = floatval($data['subtotal']);
        }
        if (isset($data['fecemi'])) {
            $data['fecreg'] = new MongoDate(strtotime($data['fecemi']));
            $comp['fecreg'] = new MongoDate(strtotime($data['fecemi']));
        }
        $compro = $f->model('cj/comp')->params(array('data'=>$comp))->save('insert')->items;
        $f->model('cj/talo')->params(array(
            'tipo'=>$data['tipo'],
            'serie'=>$data['serie'],
            'num'=>floatval($data['num']),
            'caja'=>$caja['_id']
        ))->save('num');
        if (isset($comp['alquiler'])) {
            $contrato = $f->model('in/cont')->params(array('_id'=>new MongoId($data['contrato'])))->get('one')->items;
        }
        if (isset($comp['acta'])) {
            $acta = $f->model('in/acta')->params(array('_id'=>new MongoId($data['acta_conciliacion'])))->get('one')->items;
        }
        foreach ($items as $i=>$item) {
            if (isset($item['cuenta_cobrar'])) {
                $total = 0;
                foreach ($item['conceptos'] as $w=>$conc) {
                    $upd['conceptos.'.$w.'.saldo'] = -floatval($conc['monto']);
                    $total = $total + (float)$conc['monto'];
                }
                $upd['saldo'] = -floatval($total);
                $f->model('cj/cuen')->params(array(
                    '_id'=>$item['cuenta_cobrar']['_id'],
                    'data'=>array('$inc'=>$upd)
                ))->save('custom');
                $f->model('cj/cuen')->params(array(
                    '_id'=>$item['cuenta_cobrar']['_id'],
                    'data'=>array('$push'=>array('comprobantes'=>$compro['_id']))
                ))->save('custom');
                $cuenta = $f->model('cj/cuen')->params(array('_id'=>$item['cuenta_cobrar']['_id']))->get('one')->items;
                if (floatval($cuenta['saldo'])<=0) {
                    $f->model('cj/cuen')->params(array(
                        '_id'=>$item['cuenta_cobrar']['_id'],
                        'data'=>array('$set'=>array(
                            'estado'=>'C',
                            'saldo'=>0,
                            'total'=>floatval($cuenta['total'])+abs($cuenta['saldo'])
                        ))
                    ))->save('custom');
                }
            }
            /*************************************************************************************************
            * EN CASO SEA UN PAGO DE ALQUILER DE INMUEBLES
            *************************************************************************************************/
            if (isset($comp['alquiler'])) {
                $tmp_pay = array(
                    'alquiler'=>$item['conceptos'][0]['monto'],
                    'igv'=>$item['conceptos'][1]['monto']
                );
                if (isset($item['conceptos'][2])) {
                    $tmp_pay['moras'] = $item['conceptos'][2]['monto'];
                }
                foreach ($contrato['pagos'] as $kp=>$pago) {
                    if ($pago['mes']==$item['pago']['mes']&&$pago['ano']==$item['pago']['ano']) {
                        if (!isset($data['parcial'])) {
                            $f->model('in/cont')->params(array(
                                '_id'=>$contrato['_id'],
                                'data'=>array(
                                    'pagos.'.$kp.'.estado'=>'C',
                                    'pagos.'.$kp.'.comprobante'=>array(
                                        '_id'=>$compro['_id'],
                                        'tipo'=>$compro['tipo'],
                                        'serie'=>$compro['serie'],
                                        'num'=>$compro['num']
                                    ),
                                    'pagos.'.$kp.'.item_c'=>$i,
                                    'pagos.'.$kp.'.detalle'=>$tmp_pay
                                )
                            ))->save('update');
                        } elseif (isset($data['parcial'])) {
                            $estado_tmp = 'P';
                            $total_tmp = floatval($item['conceptos'][0]['monto']);
                            if (isset($pago['total'])) {
                                $total_tmp += floatval($pago['total']);
                            }
                            if ($total_tmp==floatval($contrato['importe'])) {
                                $estado_tmp = 'C';
                            }
                            $f->model('in/cont')->params(array(
                                '_id'=>$contrato['_id'],
                                'data'=>array(
                                    'pagos.'.$kp.'.estado'=>$estado_tmp,
                                    'pagos.'.$kp.'.total'=>$total_tmp,
                                    'pagos.'.$kp.'.item_c'=>$i
                                )
                            ))->save('update');
                            $f->model('in/cont')->params(array(
                                '_id'=>$contrato['_id'],
                                'data'=>array('$push'=>
                                    array(
                                        'pagos.'.$kp.'.comprobantes'=>array(
                                            '_id'=>$compro['_id'],
                                            'tipo'=>$compro['tipo'],
                                            'serie'=>$compro['serie'],
                                            'num'=>$compro['num'],
                                            'detalle'=>$tmp_pay
                                        )
                                    )
                                )
                            ))->save('custom');
                        }
                    }
                }
            }
            /*************************************************************************************************
            * EN CASO SEA UN PAGO DE ACTA DE CONCILIACION DE INMUEBLES
            *************************************************************************************************/
            if (isset($comp['acta'])) {
                foreach ($acta['items'] as $kp=>$pago) {
                    if ($pago['num']==$item['pago']['num']) {
                        $f->model('in/acta')->params(array(
                            '_id'=>$acta['_id'],
                            'data'=>array(
                                'items.'.$kp.'.estado'=>'C',
                                'items.'.$kp.'.comprobante'=>array(
                                    '_id'=>$compro['_id'],
                                    'tipo'=>$compro['tipo'],
                                    'serie'=>$compro['serie'],
                                    'num'=>$compro['num']
                                )
                            )
                        ))->save('update');
                    }
                }
            }
            if (isset($cuenta['modulo'])) {
                if ($cuenta['modulo']=='CM') {
                    if(isset($cuenta['operacion'])){
                        $f->model('cm/oper')->params(array(
                            '_id'=>$cuenta['operacion'],
                            'data'=>array('$push'=>array(
                                'recibos'=>array(
                                    '_id'=>$compro['_id'],
                                    'serie'=>$compro['serie'],
                                    'num'=>$compro['num'],
                                    'total'=>$compro['total'],
                                    'cliente'=>$compro['cliente']
                                )
                            ))
                        ))->save('update');
                    }
                }
                if ($cuenta['modulo']=='IN') {
                    /*$oper_in = $f->model('in/oper')->params(array('_id'=>$cuenta['operacion']))->get('one')->items;
                    $upd_rent = array();
                    foreach ($oper_in['arrendamiento']['rentas'] as $i=>$rent){
                        if($rent['cuenta_cobrar']==$cuenta['_id']) $upd_rent['arrendamiento.rentas.'.$i.'.estado'] = 'PG';
                    }
                    $f->model('in/oper')->params(array(
                        'filter'=>array('_id'=>$cuenta['operacion']),
                        'data'=>array('$set'=>$upd)
                    ))->save('upd_custom');*/
                }
            }
        }
        $enti = $compro['cliente']['nomb'];
        if ($compro['cliente']['tipo_enti']=='P') {
            $enti .= ' '.$compro['cliente']['appat'].' '.$compro['cliente']['apmat'];
        }
        if ($compro['moneda']=='S') {
            $total = 'S/.'.$compro['total'];
        } else {
            $total = '$'.$compro['total'];
        }
        switch ($compro['tipo']) {
            case 'B': $word = 'Boleta de Venta'; break;
            case 'R': $word = 'Recibo de Caja'; break;
            case 'F': $word = 'Factura'; break;
        }
        $f->model('ac/log')->params(array(
            'modulo'=>'CJ',
            'bandeja'=>'Cuentas por Cobrar',
            'descr'=>'Se cre&oacute; un comprobante <b>'.$word.'</b> a nombre de <b>'.$enti.'</b>.'.
                ' con serie y n&uacute;mero <b>'.$compro['serie'].'-'.$compro['num'].'</b>'.
                ' por un total de <b>'.$total.'</b>'
        ))->save('insert');
        $f->response->json($compro);
    }
    public function execute_edit()
    {
        global $f;
        $f->response->view("cj/cuen.edit");
    }
    public function execute_select()
    {
        global $f;
        $f->response->view("cj/cuen.select");
    }
    public function execute_details()
    {
        global $f;
        $f->response->view("cj/cuen.details");
    }
    public function execute_new_comp()
    {
        global $f;
        $f->response->view("cj/comp.new");
    }
    public function execute_new_comp_inmu()
    {
        global $f;
        $f->response->view("cj/comp.new.inmu");
    }
    public function execute_conf_ceme()
    {
        global $f;
        $f->response->view("cj/conf.ceme");
    }
    public function execute_conf_inmu()
    {
        global $f;
        $f->response->view("cj/conf.inmu");
    }
    public function execute_conf_logi()
    {
        global $f;
        $f->response->view("cj/conf.logi");
    }
    public function execute_conf_hosp()
    {
        global $f;
        $f->response->view("cj/conf.hosp");
    }
}
