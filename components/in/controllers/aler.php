<?php
  class Controller_in_aler extends Controller
  {
      public function execute_index()
      {
          global $f;
          $f->response->view("in/aler.main");
      }
      public function execute_VencimientoPagoDias()
      {
          global $f;
          $dif_venci=(int)(isset($f->request->data['dias'])) ? $f->request->data['dias'] : 5 ;
          $items = $f->model("in/aler")->params(array('dias'=>$dif_venci))->get("Vencimientos")->items;
          $response = array(
            'contratos' => $items,
            'actas' => null,
          );

          $f->response->json($response);
      }
      public function execute_VencimientoPago()
      {
          global $f;
          //$items = $f->model("in/aler")->params(array())->get("Vencimientos")->items;
          $items = $f->model("in/aler")->params(array(
            'sublocal'=>new MongoId($f->request->data['sublocal']),
            'inmueble'=>new MongoId($f->request->data['inmueble'])
          ))->get("Vencimientos")->items;
          $response = array(
            'contratos' => $items,
            'actas' => null,
          );
          if (isset($f->request->data['excel'])) {
            $report = [];
            foreach ($response['contratos'] as $c => $cont) {
              foreach ($cont['pagos'] as $p => $pago) {
                $to_report = array(
                  'direccion' => $cont['inmueble']['direccion'], 
                  'sublocal' => $cont['inmueble']['sublocal']['nomb'], 
                  'tipo' => $cont['inmueble']['tipo']['nomb'], 
                  'importe' => $pago['importe'], 
                  'fecpago' => date('m-d',$pago['fecpago']->sec), 
                  'titular' =>  ($cont['titular']['tipo_enti']=='P') ? $cont['titular']['nomb']." ".$cont['titular']['appat']." ".$cont['titular']['apmat']  : $cont['titular']['nomb'],
                );
                $report[date('Y',$pago['fecpago']->sec)][] = $to_report;
              }
            }
            $f->response->view("in/repo.alerta_pagos.xls",array(
              'data'=>$report,'params'=>$f->request->data
            ));
          } else {
            $f->response->json($response);
          }
          
      }
      public function execute_VencimientoPagosTotal()
      {
          global $f;
          $items = $f->model("in/aler")->params(array())->get("Vencimientos")->items;
          $response = array(
            'contratos' => $items,
            'actas' => null,
          );
          if (isset($f->request->data['excel'])) {
            $report = [];
            foreach ($response['contratos'] as $c => $cont) {
              foreach ($cont['pagos'] as $p => $pago) {
                $to_report = array(
                  'direccion' => $cont['inmueble']['direccion'], 
                  'sublocal' => $cont['inmueble']['sublocal']['nomb'], 
                  'tipo' => $cont['inmueble']['tipo']['nomb'], 
                  'importe' => $pago['importe'], 
                  'fecpago' => date('m-d',$pago['fecpago']->sec), 
                  'titular' =>  ($cont['titular']['tipo_enti']=='P') ? $cont['titular']['nomb']." ".$cont['titular']['appat']." ".$cont['titular']['apmat']  : $cont['titular']['nomb'],
                );
                $report[date('Y',$pago['fecpago']->sec)][] = $to_report;
              }
            }
            $f->response->view("in/repo.alerta_pagos.xls",array(
              'data'=>$report,'params'=>$f->request->data
            ));
          } else {
            $f->response->json($response);
          }
          
      }
  }
