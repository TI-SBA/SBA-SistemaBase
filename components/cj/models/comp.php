<?php
class Model_cj_comp extends Model
{
    private $db;
    public $items;

    public function __construct()
    {
        global $f;
        $this->db = $f->datastore->cj_comprobantes;
    }
    protected function get_one()
    {
        global $f;
        if (isset($this->params["fecini"])) {
            $filter['fecreg'] = array('$gt'=>$this->params["fecini"],'$lte'=>$this->params["fecfin"]);
        }
        if (isset($this->params["fecreg"])) {
            $filter['fecreg'] = $this->params["fecreg"];
        }
        $filter['_id'] = $this->params['_id'];
        $this->items = $this->db->findOne($filter);
    }
    function get_one_custom(){
		global $f;
		$filter = array();
		$fields = array();
		if (isset($this->params['filter'])) {
				$filter = $this->params['filter'];
		}
		if (isset($this->params['fields'])) {
				$fields = $this->params['fields'];
		}
		$this->items = $this->db->findOne($filter, $fields);
	}


    protected function get_all_by_num()
    {
        # Obtener todos los comprobantes por serie, tipo y numeros
        global $f;
        if (isset($this->params['filter'])) {
            $filter = $this->params['filter'];
        }
        if (isset($this->params['fields'])) {
            $fields = $this->params['fields'];
        } else {
            $fields = array();
        }
        if (isset($this->params['sort'])) {
            $sort = $this->params['sort'];
        } else {
            $sort = array('_id'=>-1);
        }
        $data = $this->db->find($filter, $fields)->sort($sort);
        //$this->items = $this->db->findOne($filter,$fields);
        foreach ($data as $ob) {
            $this->items[] = $ob;
        }
    }

    //	protected function get_pacifecha(){																			//Retornar solo si una sola fecha
    //		global $f;
    //		if(isset($this->params["fecreg"])){
    //			$filter['fecreg'] = $this->params["fecreg"];
    //		}
    //		$filter['cliente._id'] = $this->params['_id'];
    //		$this->items = $this->db->findOne($filter);
    //	}

    protected function get_lista()
    {
        global $f;
        $filter = array();
        if (isset($this->params['tipo'])) {
            $filter['tipo'] = $this->params['tipo'];
        }
        if (isset($this->params['estado'])) {
            $filter['estado'] = $this->params['estado'];
            if ($this->params['estado']=='P') {
                $filter = array('$or'=>array(
                    array('estado'=>'P'),
                    array('estado'=>'C')
                ));
            }
        }
        if (isset($this->params['cliente'])) {
            $filter['cliente'] = $this->params['cliente'];
        }
        if (isset($this->params['modulo'])) {
            $filter['modulo'] = $this->params['modulo'];
        }
        if (isset($this->params["texto"])) {
            $filter['$or'] = array(
                $filter,
                array('num'=>intval($this->params["texto"]))
            );
        }
        $fields = array();
        //$order = array('serie'=>1,'num'=>-1,'fecreg'=>-1);
        $order = array('num'=>-1);
        $data = $this->db->find($filter, $fields)->skip($this->params['page_rows'] * ($this->params['page']-1))->sort($order)->limit($this->params['page_rows']);
        foreach ($data as $obj) {
            $this->items[] = $obj;
        }
        $this->paging($this->params["page"], $this->params["page_rows"], $data->count());
    }
    protected function get_lista_mh()
    {
        global $f;
        $filter = array();
        if (isset($this->params['tipo'])) {
            $filter['tipo'] = $this->params['tipo'];
        }
        if (isset($this->params['estado'])) {
            $filter['estado'] = $this->params['estado'];
            if ($this->params['estado']=='P') {
                $filter = array('$or'=>array(
                    array('estado'=>'P'),
                    array('estado'=>'C')
                ));
            }
        }
        if (isset($this->params['cliente'])) {
            $filter['cliente'] = $this->params['cliente'];
        }
        if (isset($this->params['modulo'])) {
            $filter['modulo'] = $this->params['modulo'];
        }
        if (isset($this->params["texto"])) {
            $filter['$or'] = array(
                $filter,
                array('num'=>intval($this->params["texto"]))
            );
        }
        $fields = array();
        //$order = array('serie'=>1,'num'=>-1,'fecreg'=>-1);
        $order = array('fecreal'=>-1);
        $data = $this->db->find($filter, $fields)->skip($this->params['page_rows'] * ($this->params['page']-1))->sort($order)->limit($this->params['page_rows']);
        foreach ($data as $obj) {
            $this->items[] = $obj;
        }
        $this->paging($this->params["page"], $this->params["page_rows"], $data->count());
    }
    protected function get_conceptos()
    {
        global $f;
        $filter = array();
        if (isset($this->params)) {
            $filter = $this->params;
        }
        $data = $this->db->find($filter, array('modulo'=>'MH','fecreg'=>1));
        foreach ($data as $obj) {
            $this->items[] = $obj;
        }
    }
    protected function get_search()
    {
        global $f;
        if (isset($this->params["texto"])) {
            if ($this->params["texto"]!='') {
                $f->library('helpers');
                $helper=new helper();
                $parametro = $this->params["texto"];
                $criteria = $helper->paramsSearch($this->params["texto"], array(
                    'servicio.nomb',
                    'organizacion.nomb',
                    'cliente.fullname',
                    'cliente.doc'
                ));
            } else {
                $criteria = array();
            }
        } else {
            $criteria = array();
        }
        if (isset($this->params['modulo'])) {
            $criteria['modulo'] = $this->params['modulo'];
        }
        if (isset($this->params['cliente'])) {
            $criteria['cliente._id'] = $this->params['cliente'];
        }
        if (isset($this->params['estado'])) {
            $criteria['estado'] = $this->params['estado'];
        }
        if (isset($this->params['tipo'])) {
            $criteria['tipo'] = $this->params['tipo'];
            /*$criteria['$or'] = array(
                $criteria,
                array('num'=>intval($this->params["texto"]),'tipo'=>$this->params['tipo'])
            );*/
        }
        if (isset($this->params["texto"])) {
            $criteria['$or'] = array(
                $criteria,
                array('num'=>intval($this->params["texto"]))
            );
        }
        if (isset($this->params['alquileres'])) {
            $criteria['$and'] = array(
                $criteria,
                array('$or'=>array(
                    array('alquiler'=>true),
                    array('parcial'=>true),
                    array('acta'=>true)
                ))
            );
        }
        //print_r($criteria);die();
        $fields = array();
        $sort = array('fecreg'=>-1,'fecreal'=>-1);
        if (isset($this->params['sort'])) {
            $sort = $this->params['sort'];
        }
        if (isset($this->params["page_rows"])) {
            $cursor = $this->db->find($criteria, $fields)->sort($sort)->skip($this->params["page_rows"] * ($this->params["page"]-1))->limit($this->params["page_rows"]);
        } else {
            $cursor = $this->db->find($criteria, $fields)->sort($sort);
        }
        foreach ($cursor as $obj) {
            $this->items[] = $obj;
        }
        if (isset($this->params["page_rows"])) {
            $this->paging($this->params["page"], $this->params["page_rows"], $cursor->count());
        }
    }
    protected function get_all()
    {
        global $f;
        if (isset($this->params['filter'])) {
            $filter = $this->params['filter'];
        } else {
            $filter = array();
        }
        if (isset($this->params['fields'])) {
            $fields = $this->params['fields'];
        } else {
            $fields = array();
        }
        if (isset($this->params['sort'])) {
            $sort = $this->params['sort'];
        } else {
            $sort = array('_id'=>-1);
        }
        //if(isset($this->params['fecreg'])) $fecreg = $this->params['fecreg'];
        //else $fecreg = array();

        //print_r(date('Y-M-d h:i:s',$filter['fecreg']['$lte']->sec));die();
        $data = $this->db->find($filter, $fields)->sort($sort);
        foreach ($data as $ob) {
            $this->items[] = $ob;
        }
    }
    protected function get_hoy()
    {
        global $f;
        if (isset($this->params["fecreg"])) {
            $filter['fecreg'] = $this->params["fecreg"];
        }
        if (isset($this->params["cliente"])) {
            $filter['cliente'] = $this->params["cliente"];
        }
        $data = $this->db->find($filter);
        foreach ($data as $ob) {
            $this->items[] = $ob;
        }
    }

    protected function get_daot()
    {
        global $f;
        error_reporting(E_ALL);
        ini_set('display_errors', 1);
        $filter = array(
            array('$match'=>array(
                '$or'=>array(
                    array('modulo'=>$this->params['modulo']),
                    array('playa'=>array('$exists'=>true)),
                    array('combinar_alq'=>array('$exists'=>true)),
                    array('alquiler'=>array('$exists'=>true)),
                    array('acta'=>array('$exists'=>true))
                ),
                'fecreg'=>array(
                    '$gte'=>$this->params['ini'],
                    '$lt'=>$this->params['fin']
                ),
                'items'=>array('$exists'=>true)
            )),
            array('$project'=>array(
                'cliente_id'=>'$cliente._id',
                'cliente'=>1,
                'items'=>1,
                'total'=>1
            )),
            array('$unwind'=>'$items'),
            array('$project'=>array(
                'conceptos'=>'$items.conceptos',
                'cliente_id'=>'$cliente._id',
                'cliente'=>1,
                'total'=>1
            )),
            array('$unwind'=>'$conceptos'),
            array('$match'=>array(
                'conceptos.cuenta._id'=>array('$ne'=>new MongoId('536bbcecee6f96e4050000b9'))
            )),
            array('$project'=>array(
                'cliente_id'=>'$cliente._id',
                'cliente'=>1,
                'conceptos'=>'$conceptos',
                'total'=>'$conceptos.monto'
            )),
            array('$group'=>array(
                '_id'=>array('cliente_id'=>'$cliente_id'),
                'conceptos'=>array('$addToSet'=>'$conceptos'),
                'items'=>array('$first'=>'$items'),
                'cliente'=>array('$first'=>'$cliente'),
                'total'=>array('$sum'=>'$total')
            )),
            array('$match'=>array(
                'total'=>array(
                    '$gte'=>(floatval($this->params['uit'])*2)
                )
            ))
        );
        $data = $this->db->aggregate($filter);
        $this->items = $data['result'];
    }
    protected function get_num()
    {
        global $f;
        $cursor = $this->db->find(array(
            'tipo'=>$this->params['tipo'],
            'serie'=>$this->params['serie'],
            'caja._id'=>$this->params['caja']
        ), array('num'=>true))->sort(array('_id'=>-1))->limit(1);
        foreach ($cursor as $ob) {
            $this->items = $ob['num'];
        }
    }
    protected function get_num_mod()
    {
        global $f;
        $cursor = $this->db->find(array(
            'tipo'=>$this->params['tipo'],
            'serie'=>$this->params['serie'],
            'modulo'=>$this->params['modulo']
        ), array('num'=>true))->sort(array('_id'=>-1))->limit(1);
        foreach ($cursor as $ob) {
            $this->items = $ob['num'];
        }
    }
    protected function get_verify()
    {
        global $f;
        $this->items = $this->db->findOne(array(
            'tipo'=>$this->params['tipo'],
            'serie'=>$this->params['serie'],
            'num'=>$this->params['num']
        ));
    }
    protected function get_custom()
    {
        global $f;
        $fields = array();
        if (isset($this->params['fields'])) {
            $fields = $this->params['fields'];
        }
        $data = $this->db->find($this->params['filter'], $fields);
        foreach ($data as $obj) {
            $this->items[] = $obj;
        }
    }
    protected function get_total()
    {
        global $f;
        $fields = array(
          'tc'=>true,
          'moneda'=>true,
          'total'=>true
        );
        if (isset($this->params['fields'])) {
            $fields = $this->params['fields'];
        }
        $rpta = 0;
        $data = $this->db->find($this->params['filter'], $fields);
        foreach ($data as $obj) {
            if ($obj['moneda']=="S") {
                $rpta += floatval($obj['total']);
            } else {
                $rpta += floatval($obj['total'] * $obj['tc']);
            }
        }
        $this->items = $rpta;
    }
    protected function get_total_alquiler()
    {
          global $f;
          $moi = new MongoDate(time());
          //$mq = new MongoDate(strtotime('+1 day', $moi));
          if (isset($this->params['moi'])) {
              $moi = $this->params['moi'];
          }
          if (isset($this->params['mq'])) {
              $mq = $this->params['mq'];
          }
          $filter = array(
                      'modulo'=>'IN',
                      'estado'=>'R',
                      'tipo'=>array('$in'=>array('R','F','B')),
                      'fecreg'=>array(
                          '$gte'=>$moi,
                          '$lt'=>$mq,
                      ),
                      'playa'=>array('$exists'=>false),
                );
        $rpta=0;
        $rpta += $f->model('cj/comp')->params(array(
            'filter'=>$filter
          ))->get('total')->items;
        $this->items = $rpta;
    }
    protected function get_total_playas()
    {
          global $f;
          $moi = time();
          //$mq = strtotime('+1 day', $moi);
          if (isset($this->params['moi'])) {
              $moi = $this->params['moi'];
          }
          if (isset($this->params['mq'])) {
              $mq = $this->params['mq'];
          }
          $filter = array(
                      'modulo'=>'IN',
                      'estado'=>'R',
                      'tipo'=>array('$in'=>array('R','F','B')),
                      'fecreg'=>array(
                          '$gte'=>new MongoDate($moi),
                          '$lt'=>new MongoDate($mq)
                      ),
                      'playa'=>array('$exists'=>true),
                );
        $rpta=0;
        $rpta += $f->model('cj/comp')->params(array(
            'filter'=>$filter
          ))->get('total')->items;
        $this->items = $rpta;
    }
    protected function save_insert()
    {
        global $f;
        if (!isset($this->params['data']['modulo'])) {
            $this->params['data']['modulo'] = 'IN';
        }
        $this->db->insert($this->params['data']);
        $this->items = $this->params['data'];
        //$f->model('cj/comp')->params(array('data'=>$this->items))->save('sunat');
    }
    protected function save_update()
    {
        global $f;
        unset($this->params['data']['_id']);
        $this->db->update(array('_id'=>$this->params['_id']), array('$set'=>$this->params['data']));
        $this->items = $this->params['data'];
    }
    protected function save_custom()
    {
        global $f;
        $this->db->update(array( '_id' => $this->params['_id'] ), $this->params['data']);
    }
    protected function get_recibos_heresi()
    {
        global $f;
        $filter = array();
        if (isset($this->params)) {
            $filter = $this->params;
        }
        $data = $this->db->find($filter, array('modulo'=>1,'estado'=>1,'tipo'=>1,'serie'=>1,'num'=>1,'cliente'=>1,'moneda'=>1,'total'=>1,'fecreg'=>1,'items'=>1));
        foreach ($data as $obj) {
            $this->items[] = $obj;
        }
    }


    /*	protected function get_lpaciente(){																					//Obtiene el ultimo comprobante del paciente
            global $f;
            $data = $this->db->find(array('cliente._id'=>$this->params['_id']))->sort(array('fecreg'=>-1))->limit(1);		//Recibir el _id de caja para encontrar el ultimo movimiento
            foreach ($data as $obj) {
                $this->items = $obj;
            }
        }
    */
    protected function save_sunat()
    {
        global $f;
        $data = $this->params['data'];
        $data['cliente'] = $f->model('mg/entidad')->params(array('_id'=>$data['cliente']['_id']))->get('one')->items;
        $rpta = array(
            'tipo'=>$data['tipo'],
            'serie'=>$data['serie'],
            'num'=>$data['num'],
            'cliente_nomb'=>$data['cliente']['nomb'],
            'cliente_ruc'=>'',
            'cliente_dni'=>'',
            'cliente_domic'=>'',
            'cliente_domic_fis'=>'',
            'items'=>array()
        );
        if (isset($data['cliente']['docident'])) {
            foreach ($data['cliente']['docident'] as $doc) {
                if ($doc['tipo']=='DNI') {
                    $rpta['cliente_dni'] = $doc['num'];
                }
                if ($doc['tipo']=='RUC') {
                    $rpta['cliente_ruc'] = $doc['num'];
                }
            }
        }
        if (isset($data['cliente']['domicilios'])) {
            foreach ($data['cliente']['domicilios'] as $domi) {
                if ($domi['tipo']=='FISCAL') {
                    $rpta['cliente_domic_fis'] = $domi['direccion'];
                } elseif ($domi['tipo']=='PERSONAL') {
                    $rpta['cliente_domic'] = $domi['direccion'];
                } else {
                    $rpta['cliente_domic'] = $domi['direccion'];
                }
            }
        }
        if (isset($data['modulo'])) {
            switch ($data['modulo']) {
                case 'AG':
                    foreach ($data['items'] as $i=>$item) {
                        $res = array(
                            'descr'=>$item['producto']['nomb'],
                            'codigo'=>'',
                            'cod_unidad'=>'NEW',
                            'unidad'=>$item['producto']['unidad']['nomb'],
                            'cant'=>number_format(floatval($item['cant']), 2),
                            'importe_total'=>number_format(floatval($item['monto'])*floatval($item['cant']), 2),
                            'precio_unitario'=>number_format(floatval($item['monto']), 2),
                            'valor_unitario'=>0,
                            'igv'=>0,
                            'isc'=>0,
                            'otros'=>0,
                            'gravado'=>true,
                            'inafecto'=>false,
                            'gratuito'=>false
                        );
                        foreach ($item['conceptos'] as $j=>$conc) {
                            if ($conc['cuenta']['cod']=='2101.010503') {
                                $res['igv'] += floatval($conc['monto']);
                            } else {
                                $res['valor_unitario'] += floatval($conc['monto']);
                            }
                        }
                        $res['cant'] = number_format($res['cant'], 2);
                        $res['importe_total'] = number_format($res['importe_total'], 2);
                        $res['precio_unitario'] = number_format($res['precio_unitario'], 2);
                        $res['valor_unitario'] = number_format($res['valor_unitario'], 2);
                        $res['igv'] = number_format($res['igv'], 2);
                        $res['isc'] = number_format($res['isc'], 2);
                        $res['otros'] = number_format($res['otros'], 2);
                        $rpta['items'][] = $res;
                    }
                    break;
                case 'FA':
                    foreach ($data['items'] as $i=>$item) {
                        $res = array(
                            'descr'=>$item['producto']['nomb'],
                            'codigo'=>'',
                            'cod_unidad'=>'NEW',
                            'unidad'=>$item['producto']['unidad']['nomb'],
                            'cant'=>number_format(floatval($item['cant']), 2),
                            'importe_total'=>number_format(floatval($item['monto'])*floatval($item['cant']), 2),
                            'precio_unitario'=>number_format(floatval($item['monto']), 2),
                            'valor_unitario'=>0,
                            'igv'=>0,
                            'isc'=>0,
                            'otros'=>0,
                            'gravado'=>true,
                            'inafecto'=>false,
                            'gratuito'=>false
                        );
                        foreach ($item['conceptos'] as $j=>$conc) {
                            if ($conc['cuenta']['cod']=='2101.010503') {
                                $res['igv'] += floatval($conc['monto']);
                            } else {
                                $res['valor_unitario'] += floatval($conc['monto']);
                            }
                        }
                        $res['cant'] = number_format($res['cant'], 2);
                        $res['importe_total'] = number_format($res['importe_total'], 2);
                        $res['precio_unitario'] = number_format($res['precio_unitario'], 2);
                        $res['valor_unitario'] = number_format($res['valor_unitario'], 2);
                        $res['igv'] = number_format($res['igv'], 2);
                        $res['isc'] = number_format($res['isc'], 2);
                        $res['otros'] = number_format($res['otros'], 2);
                        $rpta['items'][] = $res;
                    }
                    break;
                default:
                    # code...
                    break;
            }
        }
        $this->db->update(array('_id'=>$this->params['data']['_id']), array('$set'=>array('sunat_prev'=>$rpta)));
    }
}
