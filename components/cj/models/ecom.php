<?php
class Model_cj_ecom extends Model
{
    private $db;
    public $items;

    public function __construct()
    {
        global $f;
        $this->db = $f->datastore->cj_ecomprobantes;
    }
    protected function get_one()
    {
        global $f;
        $filter['_id'] = $this->params['_id'];
        $this->items = $this->db->findOne($filter);
    }
    protected function get_one_custom()
    {
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
        #Filtro por serie
        if (isset($this->params['serie'])) {
            $filter['serie'] = $this->params['serie'];
        }
        #Filtro por _id
        if (isset($this->params['_id'])) {
            $filter['_id'] = $this->params['_id'];
        }
        /*if(isset($this->params["texto"])){
            $filter['$or'] = array(
                $filter,
                array('num'=>intval($this->params["texto"]))
            );
        }*/
        if (isset($this->params['texto'])) {
            if ($this->params["texto"]!='') {
                $f->library('helpers');
                $helper=new helper();
                $parametro = $this->params["texto"];
                $filter = array(
                    '$or'=>array(
                        array('numero'=>floatval($this->params['texto'])),
                        array('serie'=>$parametro),
                        array('tipo'=> $parametro),
                        array('cliente_nomb'=>new MongoRegex('/^'.$parametro.'/i')),
                        array('cliente_doc'=> $parametro),
                    )
                );
            }
        }
        if (isset($this->params['caja'])) {
            $filter['caja._id'] = $this->params['caja'];
        }
        $fields = array(
            'codigo_barras_pdf'=>false,
        );
        //$order = array('serie'=>1,'num'=>-1,'fecreg'=>-1);
        $order = array('_id'=>-1);
        $data = $this->db->find($filter, $fields)->skip($this->params['page_rows'] * ($this->params['page']-1))->sort($order)->limit($this->params['page_rows']);
        foreach ($data as $obj) {
            $this->items[] = $obj;
        }
        $this->paging($this->params["page"], $this->params["page_rows"], $data->count());
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
        //print_r(date('Y-M-d h:i:s',$filter['fecreg']['$lte']->sec));die();
        $data = $this->db->find($filter, $fields)->sort($sort);
        foreach ($data as $ob) {
            $this->items[] = $ob;
        }
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
                    'tipo_cambio'=>true,
                    'moneda'=>true,
                    'total'=>true
                );
        if (isset($this->params['fields'])) {
            $fields = $this->params['fields'];
        }
        $rpta = 0;
        $data = $this->db->find($this->params['filter'], $fields);
        foreach ($data as $obj) {
            if ($obj['moneda']=="PEN") {
                $rpta += floatval($obj['total']);
            } else {
                $rpta += floatval($obj['total'] * $obj['tipo_cambio']);
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
                'estado' =>  array(
                    '$in' => array('FI','CO','ES'),
                ),
                'serie' => array(
                  '$in'=>array('B001','F001')
                ),
                'tipo' =>  array(
                    '$in' => array('F','B'),
                ),
                'fecemi'=>array(
                    '$gte'=>$moi,
                    '$lt'=>$mq
                )
          );
        $rpta=0;
        $rpta += $f->model('cj/ecom')->params(array(
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
                'estado' =>  array(
                    '$in' => array('FI','CO','ES'),
                ),
                'serie' => array(
                  '$in'=>array('B004','F004')
                ),
                'tipo' =>  array(
                    '$in' => array('F','B'),
                ),
                'fecemi'=>array(
                    '$gte'=>$moi,
                    '$lt'=>$mq
                )
          );
        $rpta=0;
        $rpta += $f->model('cj/ecom')->params(array(
            'filter'=>$filter
          ))->get('total')->items;
        $this->items = $rpta;
    }
    protected function save_insert()
    {
        global $f;
        $this->db->insert($this->params['data']);
        $this->items = $this->params['data'];
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
    protected function delete_ecom()
    {
        global $f;
        $item = $f->model('cj/ecom')->params(array('_id'=>new MongoId($this->params['_id'])))->get('one')->items;
        $item['feceli'] = new MongoDate();
        $item['coleccion'] = 'cj_ecomprobantes';
        $item['trabajador_delete'] = $f->session->userDB;
        $f->datastore->temp_del->insert($item);
        $f->model('ac/log')->params(array(
            'modulo'=>'CJ',
            'bandeja'=>'Comprobantes',
            'descr'=>'Se elimin&oacute; el <b> Borrador de Comprobante Electr&oacute;nico </b>.'.
                ' con serie <b>'.$item['serie'].' del cliente '.$item['cliente_nomb'].'</b>'
        ))->save('insert');
        $this->items = array(
            '_id' => new MongoId($this->params['_id']),
        );
        $this->db->remove($this->items);
    }
}
