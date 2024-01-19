<?php
class Controller_in_impo extends Controller {
  function execute_contratos(){
    global $f;
    $fp = fopen ( "impo/nuevo.csv", "r" );
    $cod = 0;
    while ($data = fgetcsv ($fp, 1000, ";")){
      if($cod!=0){
        $contrato = $f->datastore->in_contratos->findOne(array('oldid'=>$data[0]));
	print_r($contrato);
        if($contrato==null){
          if($data[4]=='  -   -') $data[4] = date('Y-m-d');
          if($data[5]=='  -   -') $data[5] = date('Y-m-d');
          if($data[13]=='  -   -') $data[13] = date('Y-m-d');
          $item = array(
            'oldid'=>$data[0],
            'autor'=>$f->session->userDB,
            'trabajador'=>$f->session->userDB,
            'fecreg'=>new MongoDate(),
            'fecmod'=>new MongoDate(),
            'fecini'=>new MongoDate(strtotime($data[4])),
            'fecfin'=>new MongoDate(strtotime($data[5])),
            'sedapar'=>$data[6],
            'seal'=>$data[7],
            'arbitrios'=>$data[8],
            'fecdes'=>new MongoDate(strtotime($data[13])),
            'importe'=>$data[15],
            'pagos'=>array()
          );
          $inmueble = $f->datastore->in_inmuebles->findOne(array('oldid'=>$data[2]));
          $item['inmueble'] = array(
            '_id'=>$inmueble['_id'],
            'direccion'=>$inmueble['direccion'],
            'sublocal'=>$inmueble['sublocal'],
            'tipo'=>$inmueble['tipo']
          );
          if($data[9]=='VERDADERO') $item['desalojo'] = '1';
          else $item['desalojo'] = '0';
          if($data[10]=='VERDADERO') $item['odsd'] = '1';
          else $item['odsd'] = '0';
          if($data[11]=='VERDADERO') $item['infocorp'] = '1';
          else $item['infocorp'] = '0';
          if($data[12]=='VERDADERO') $item['as_externa'] = '1';
          else $item['as_externa'] = '0';
          switch($data[14]){
            case '01': $item['motivo'] = array('_id'=>new MongoId('5531654cbc795ba801000033'),'nomb'=>'NUEVO'); break;
            case '02': $item['motivo'] = array('_id'=>new MongoId('5531656fbc795ba801000039'),'nomb'=>'RENOVACION'); break;
            case '03': $item['motivo'] = array('_id'=>new MongoId('5531657bbc795ba80100003d'),'nomb'=>'TRASPASO'); break;
            case '04': $item['motivo'] = array('_id'=>new MongoId('55316577bc795ba80100003b'),'nomb'=>'SIN CONTRATO'); break;
            case '05': $item['motivo'] = array('_id'=>new MongoId('55316565bc795ba801000037'),'nomb'=>'RENOV. SIN CONTRATO'); break;
            case '06': $item['motivo'] = array('_id'=>new MongoId('55316539bc795ba80100002f'),'nomb'=>'AUDIENCIA'); break;
            case '07': $item['motivo'] = array('_id'=>new MongoId('5531652fbc795ba80100002d'),'nomb'=>'ACTA DE CONCILIACIÓN'); break;
            case '08': $item['motivo'] = array('_id'=>new MongoId('55316553bc795ba801000035'),'nomb'=>'PENALIDADES'); break;
            case '09': $item['motivo'] = array('_id'=>new MongoId('55316543bc795ba801000031'),'nomb'=>'AUTORIZACION'); break;
            case '10': $item['motivo'] = array('_id'=>new MongoId('5540f3c3bc795b7801000029'),'nomb'=>'CONVENIO'); break;
            default: $item['motivo'] = array('_id'=>new MongoId('5531654cbc795ba801000033'),'nomb'=>'NUEVO'); break;
          }
          switch($data[17]){
            case '01': $item['moneda'] = 'S'; break;
            case '02': $item['moneda'] = 'D'; break;
            default: $item['moneda'] = 'S'; break;
          }
          if($data[16]=='VERDADERO') $item['contrato_dias'] = '1';
          else $item['contrato_dias'] = '0';
          switch($data[18]){
            case '01': $item['situacion'] = 'O'; break;
            case '02': $item['situacion'] = 'D'; break;
            case '03': $item['situacion'] = 'U'; break;
            case '04': $item['situacion'] = 'C'; break;
            case '05': $item['situacion'] = 'M'; break;
            default: $item['situacion'] = 'O'; break;
          }
          $inquilino = $f->datastore->mg_entidades->findOne(array('oldid'=>$data[1]));
          if($inquilino==null)
            $inquilino = $f->datastore->mg_entidades->findOne(array('oldid2'=>$data[1]));
          if($inquilino!=null){
            $item['titular'] = array(
              '_id'=>$inquilino['_id'],
              'nomb'=>$inquilino['nomb'],
              'tipo_enti'=>$inquilino['tipo_enti'],
              'docident'=>$inquilino['docident']
            );
            if($inquilino['tipo_enti']=='P'){
              $item['titular']['appat'] = $inquilino['appat'];
              $item['titular']['apmat'] = $inquilino['apmat'];
            }
          }else{
            $item['titular'] = $data[1];
          }
          // CODIGO PARA MESES
          $d1 = strtotime($data[4]);
          $d2 = strtotime($data[5]);
          $min_date = min($d1, $d2);
          $max_date = max($d1, $d2);
          $i = 0;

          $prev = $min_date;
          $max_date = strtotime("+1 MONTH", $max_date);
          while (($min_date = strtotime("+1 MONTH", $min_date)) <= $max_date) {
            $i++;
            $tmp = array(
              'item'=>$i,
              'mes'=>date('n',$prev),
              'ano'=>date('Y',$prev)
            );
            $item['pagos'][] = $tmp;
            $prev = $min_date;
          }
          $f->datastore->in_contratos->insert($item);
        }
      }
    $cod++;
    }
    fclose( $fp);
    echo "true";
  }
  function execute_alquileres(){
    global $f;
    set_time_limit(0);
    $fp = fopen ( "impo/mov_alq.csv", "r" );
    $cod = 0;
    while ($data = fgetcsv ($fp, 1000, ";")){
      if($cod!=0&&$data[5]=='01'&&$data[17]!=''&&$data[17]!='00000'){
        $contrato = $f->datastore->in_contratos->findOne(array('oldid'=>$data[1]));
        if($contrato!=null){
          $tmp = null;
          $ind = null;
          $check = null;
          $total = 0;
          foreach($contrato['pagos'] as $i=>$pago){
            if(intval($pago['mes'])==intval($data[3])&&intval($pago['ano'])==intval($data[2])){
              $total += floatval($data[6]);
              $mone = 'S';
              if($data[14]!='02') $mone = 'D';
              $tipo = 'B';
              if($data[14]=='02') $tipo = 'F';
              if($data[14]=='03') $tipo = 'F';
              if($data[14]=='04') $tipo = 'RI';
              if($data[14]=='06') $tipo = 'NC';
              $tmp = array(
                'oldid'=>$data[0],
                'moneda'=>$mone,
                'tipo'=>$tipo,
                'num'=>intval($data[17]),
                'fec'=>new MongoDate(strtotime($data[18])),
                'total'=>floatval($data[6])
              );
              $ind = $i;
              $comp = $f->datastore->cj_comprobantes->findOne(array('modulo'=>'IN','num'=>intval($data[17]),'tipo'=>$tipo));
              if($comp!=null){
                $comp_ = array(
                  '_id'=>$comp['_id'],
                  'tipo'=>$comp['tipo'],
                  'serie'=>$comp['serie'],
                  'num'=>$comp['num'],
                  'detalle'=>array(
                    'alquiler'=>0,
                    'igv'=>0,
                    'moras'=>0
                  )
                );
                if(isset($comp['alquiler']))
                  $comp_['detalle']['alquiler'] = $comp['alquiler'];
                if(isset($comp['igv']))
                  $comp_['detalle']['igv'] = $comp['igv'];
                $f->datastore->in_contratos->update(array(
                  '_id'=>$contrato['_id']
                ),array('$push'=>array(
                  'pagos.'.$ind.'.comprobantes'=>$comp_
                )));
                if(floatval($contrato['importe'])==$total){
                  $f->datastore->in_contratos->update(array(
                    '_id'=>$contrato['_id']
                  ),array('$set'=>array(
                    'pagos.'.$ind.'.estado'=>'C'
                  )));
                }else{
                  $f->datastore->in_contratos->update(array(
                    '_id'=>$contrato['_id']
                  ),array('$set'=>array(
                    'pagos.'.$ind.'.estado'=>'P'
                  )));
                }
              }else{
                if($tmp!=null){
                  $f->datastore->in_contratos->update(array(
                    '_id'=>$contrato['_id']
                  ),array('$push'=>array(
                    'pagos.'.$ind.'.historico'=>$tmp
                  )));
                }
                if(floatval($contrato['importe'])==$total){
                  $f->datastore->in_contratos->update(array(
                    '_id'=>$contrato['_id']
                  ),array('$set'=>array(
                    'pagos.'.$ind.'.estado'=>'C'
                  )));
                }else{
                  $f->datastore->in_contratos->update(array(
                    '_id'=>$contrato['_id']
                  ),array('$set'=>array(
                    'pagos.'.$ind.'.estado'=>'P'
                  )));
                }
              }
              break;
            }
          }
        }
      }
      $cod++;
    }
    fclose( $fp);
    echo "true";
  }
  function execute_homo(){
    global $f;
    $rpta = array(
      '1'=>$this->generar_comprobante(array('F','FF11','1','S',3)),
      '2'=>$this->generar_comprobante(array('F','FF11','2','S',2)),
      '3'=>$this->generar_comprobante(array('F','FF11','3','S',1)),
      '4'=>$this->generar_comprobante(array('F','FF11','4','S',5)),
      '5'=>$this->generar_comprobante(array('F','FF11','5','S',4)),
      '12'=>$this->generar_comprobante(array('F','FF12','1','S',1,'I')),
      '13'=>$this->generar_comprobante(array('F','FF12','2','S',4,'I')),
      '14'=>$this->generar_comprobante(array('F','FF12','3','S',7,'I')),
      '15'=>$this->generar_comprobante(array('F','FF12','4','S',5,'I')),
      '16'=>$this->generar_comprobante(array('F','FF12','5','S',6,'I')),
      '23'=>$this->generar_comprobante(array('F','FF13','1','S',7,'G')),
      '24'=>$this->generar_comprobante(array('F','FF13','2','S',2,'G')),
      '25'=>$this->generar_comprobante(array('F','FF13','3','S',5,'G')),
      '26'=>$this->generar_comprobante(array('F','FF13','4','S',4,'G')),
      '27'=>$this->generar_comprobante(array('F','FF13','5','S',3,'G')),
      '32'=>$this->generar_comprobante(array('F','FF14','1','S',2,'D')),
      '33'=>$this->generar_comprobante(array('F','FF14','2','S',1,'D')),
      '34'=>$this->generar_comprobante(array('F','FF14','3','S',4,'D')),
      '35'=>$this->generar_comprobante(array('F','FF14','4','S',3,'D')),
      '36'=>$this->generar_comprobante(array('F','FF14','5','S',5,'D')),
      '43'=>$this->generar_comprobante(array('F','FF30','1','S',5,'S')),
      '46'=>$this->generar_comprobante(array('F','FF40','1','S',5,'P')),
      '49'=>$this->generar_comprobante(array('F','FF50','1','D',5)),
      '52'=>$this->generar_comprobante(array('B','BB11','1','D',4)),
      '53'=>$this->generar_comprobante(array('B','BB11','2','D',7)),
      '54'=>$this->generar_comprobante(array('B','BB11','3','D',5)),
      '55'=>$this->generar_comprobante(array('B','BB11','4','D',3)),
      '56'=>$this->generar_comprobante(array('B','BB11','5','D',2)),
      '63'=>$this->generar_comprobante(array('B','BB12','1','D',2)),
      '64'=>$this->generar_comprobante(array('B','BB12','2','D',4)),
      '65'=>$this->generar_comprobante(array('B','BB12','3','D',7)),
      '66'=>$this->generar_comprobante(array('B','BB12','4','D',5)),
      '67'=>$this->generar_comprobante(array('B','BB12','5','D',1)),
      '74'=>$this->generar_comprobante(array('B','BB13','1','D',7)),
      '75'=>$this->generar_comprobante(array('B','BB13','2','D',2)),
      '76'=>$this->generar_comprobante(array('B','BB13','3','D',5)),
      '77'=>$this->generar_comprobante(array('B','BB13','4','D',4)),
      '78'=>$this->generar_comprobante(array('B','BB13','5','D',9)),
      '85'=>$this->generar_comprobante(array('B','BB14','1','D',10)),
      '86'=>$this->generar_comprobante(array('B','BB14','2','D',7)),
      '87'=>$this->generar_comprobante(array('B','BB14','3','D',6)),
      '88'=>$this->generar_comprobante(array('B','BB14','4','D',9)),
      '89'=>$this->generar_comprobante(array('B','BB14','5','D',4)),
      '96'=>$this->generar_comprobante(array('B','BB50','1','D',3))
    );
      $rpta['6'] = $this->generar_comprobante(array('NC','FF11','2','S',0,'',$rpta['2']));
      $rpta['7'] = $this->generar_comprobante(array('NC','FF11','3','S',0,'',$rpta['3']));
      $rpta['8'] = $this->generar_comprobante(array('NC','FF11','4','S',0,'',$rpta['4']));
      $rpta['9'] = $this->generar_comprobante(array('ND','FF11','2','S',0,'',$rpta['2']));
      $rpta['10'] = $this->generar_comprobante(array('ND','FF11','3','S',0,'',$rpta['3']));
      $rpta['11'] = $this->generar_comprobante(array('ND','FF11','4','S',0,'',$rpta['4']));
      $rpta['17'] = $this->generar_comprobante(array('NC','FF12','12','S',0,'',$rpta['12']));
      $rpta['18'] = $this->generar_comprobante(array('NC','FF12','14','S',0,'',$rpta['14']));
      $rpta['19'] = $this->generar_comprobante(array('NC','FF12','16','S',0,'',$rpta['16']));
      $rpta['20'] = $this->generar_comprobante(array('ND','FF12','12','S',0,'',$rpta['12']));
      $rpta['21'] = $this->generar_comprobante(array('ND','FF12','14','S',0,'',$rpta['14']));
      $rpta['22'] = $this->generar_comprobante(array('ND','FF12','16','S',0,'',$rpta['16']));
      $rpta['28'] = $this->generar_comprobante(array('NC','FF13','24','S',0,'',$rpta['24']));
      $rpta['29'] = $this->generar_comprobante(array('NC','FF13','25','S',0,'',$rpta['25']));
      $rpta['30'] = $this->generar_comprobante(array('ND','FF13','24','S',0,'',$rpta['24']));
      $rpta['31'] = $this->generar_comprobante(array('ND','FF13','25','S',0,'',$rpta['25']));
      $rpta['37'] = $this->generar_comprobante(array('NC','FF14','33','S',0,'',$rpta['33']));
      $rpta['38'] = $this->generar_comprobante(array('NC','FF14','34','S',0,'',$rpta['34']));
      $rpta['39'] = $this->generar_comprobante(array('NC','FF14','36','S',0,'',$rpta['36']));
      $rpta['40'] = $this->generar_comprobante(array('ND','FF14','33','S',0,'',$rpta['33']));
      $rpta['41'] = $this->generar_comprobante(array('ND','FF14','34','S',0,'',$rpta['34']));
      $rpta['42'] = $this->generar_comprobante(array('ND','FF14','36','S',0,'',$rpta['36']));
      $rpta['44'] = $this->generar_comprobante(array('NC','FF30','43','S',0,'',$rpta['43']));
      $rpta['45'] = $this->generar_comprobante(array('ND','FF30','43','S',0,'',$rpta['43']));
      $rpta['47'] = $this->generar_comprobante(array('NC','FF40','46','S',0,'',$rpta['46']));
      $rpta['48'] = $this->generar_comprobante(array('ND','FF40','46','S',0,'',$rpta['46']));
      $rpta['50'] = $this->generar_comprobante(array('NC','FF50','49','S',0,'',$rpta['49']));
      $rpta['51'] = $this->generar_comprobante(array('ND','FF50','49','S',0,'',$rpta['49']));
      $rpta['57'] = $this->generar_comprobante(array('NC','BB11','53','S',0,'',$rpta['53']));
      $rpta['58'] = $this->generar_comprobante(array('NC','BB11','54','S',0,'',$rpta['54']));
      $rpta['59'] = $this->generar_comprobante(array('NC','BB11','55','S',0,'',$rpta['55']));
      $rpta['60'] = $this->generar_comprobante(array('ND','BB11','53','S',0,'',$rpta['53']));
      $rpta['61'] = $this->generar_comprobante(array('ND','BB11','54','S',0,'',$rpta['54']));
      $rpta['62'] = $this->generar_comprobante(array('ND','BB11','55','S',0,'',$rpta['55']));
      $rpta['68'] = $this->generar_comprobante(array('NC','BB12','63','S',0,'',$rpta['63']));
      $rpta['69'] = $this->generar_comprobante(array('NC','BB13','66','S',0,'',$rpta['65']));
      $rpta['70'] = $this->generar_comprobante(array('NC','BB14','67','S',0,'',$rpta['67']));
      $rpta['71'] = $this->generar_comprobante(array('ND','BB15','63','S',0,'',$rpta['63']));
      $rpta['72'] = $this->generar_comprobante(array('ND','BB16','66','S',0,'',$rpta['66']));
      $rpta['73'] = $this->generar_comprobante(array('ND','BB17','67','S',0,'',$rpta['67']));
      $rpta['79'] = $this->generar_comprobante(array('NC','BB13','74','S',0,'',$rpta['74']));
      $rpta['80'] = $this->generar_comprobante(array('NC','BB13','75','S',0,'',$rpta['75']));
      $rpta['81'] = $this->generar_comprobante(array('NC','BB13','77','S',0,'',$rpta['77']));
      $rpta['82'] = $this->generar_comprobante(array('ND','BB13','74','S',0,'',$rpta['74']));
      $rpta['83'] = $this->generar_comprobante(array('ND','BB13','75','S',0,'',$rpta['75']));
      $rpta['84'] = $this->generar_comprobante(array('ND','BB13','77','S',0,'',$rpta['77']));
      $rpta['90'] = $this->generar_comprobante(array('NC','BB13','85','S',0,'',$rpta['85']));
      $rpta['91'] = $this->generar_comprobante(array('NC','BB13','86','S',0,'',$rpta['86']));
      $rpta['92'] = $this->generar_comprobante(array('NC','BB13','88','S',0,'',$rpta['88']));
      $rpta['93'] = $this->generar_comprobante(array('ND','BB13','85','S',0,'',$rpta['85']));
      $rpta['94'] = $this->generar_comprobante(array('ND','BB13','86','S',0,'',$rpta['86']));
      $rpta['95'] = $this->generar_comprobante(array('ND','BB13','88','S',0,'',$rpta['88']));
      $rpta['97'] = $this->generar_comprobante(array('NC','BB50','96','S',0,'',$rpta['96']));
      $rpta['98'] = $this->generar_comprobante(array('ND','BB50','96','S',0,'',$rpta['96']));

    print_r($rpta);
    //$f->response->json($rpta);
  }
  function generar_comprobante($data){
    global $f;
    $items_example = array(
      array(
        'codigo'=>'',
        'descr'=>'625 ml con gas',
        'cod_unidad'=>'NEW1',
        'unidad'=>'Botella de 625ml',
        'precio_unitario'=>1.2
      ),
       array(
        'codigo'=>'',
        'descr'=>'625 ml sin gas',
        'cod_unidad'=>'NEW2',
        'unidad'=>'Botella de 625ml',
        'precio_unitario'=>1.2
      ),
      array(
        'codigo'=>'',
        'descr'=>'Bidon de 7l',
        'cod_unidad'=>'NEW3',
        'unidad'=>'Bidon de 7l',
        'precio_unitario'=>7
      ),
      array(
        'codigo'=>'',
        'descr'=>'Bidon de 9l',
        'cod_unidad'=>'NEW4',
        'unidad'=>'Bidon de 9l',
        'precio_unitario'=>20
      ),
      array(
        'codigo'=>'',
        'descr'=>'Paquete de 6',
        'cod_unidad'=>'NEW5',
        'unidad'=>'Sixpack',
        'precio_unitario'=>15
      ),
      array(
        'codigo'=>'',
        'descr'=>'Paquete de 12',
        'cod_unidad'=>'NEW6',
        'unidad'=>'Twelvepack',
        'precio_unitario'=>25
      ),
      array(
        'codigo'=>'',
        'descr'=>'Paneton',
        'cod_unidad'=>'NEW7',
        'unidad'=>'Bolsa',
        'precio_unitario'=>8
      )
    );
    $rpta = array(
      '_id'=>new MongoId(),
      'fecreg'=>time(),
      'tipo'=>$data[0],
      'serie'=>$data[1],
      'num'=>$data[2],
      'moneda'=>$data[3],
      'cliente_nomb'=>'FAVIO GABRIEL NAQUIRA VARGAS',
      'cliente_domic'=>'JUAN PABLO VIZCARDO Y GUZMAN E-10, JLB Y R, AREQUIPA',
      'total_isc'=>0,
      'total_igv'=>0,
      'total_otros_imp'=>0,
      'total_ope_inafectas'=>0,
      'total_ope_gravadas'=>0,
      'total_desc'=>0,
      'total_ope_exoneradas'=>0,
      'total_ope_gratuitas'=>0,
      'total'=>0,
      'items'=>array()
    );
    if($data[0]=='B'){
      $rpta['tipo_doc'] = 'DNI';
      $rpta['cliente_doc'] = '70230773';
    }else{
      $rpta['tipo_doc'] = 'RUC';
      $rpta['cliente_doc'] = '10702307738';
    }
    if($data[0]=='NC'||$data[0]=='ND'){
      $rpta['doc_original'] = array(
        'tipo'=>$data[6]['tipo'],
        'serie'=>$data[6]['serie'],
        'num'=>$data[6]['num']
      );
      $rpta['items'] = $data[6]['items'];
      if($data[0]=='NC'){
        $rpta['items'][0]['valor_unitario'] = 2;
        $rpta['items'][0]['precio_unitario'] = 2;
        $rpta['items'][0]['importe_total'] = 2;
      }else{
        $rpta['items'][0]['valor_unitario'] = 0.8;
        $rpta['items'][0]['precio_unitario'] = 0.8;
        $rpta['items'][0]['importe_total'] = 0.8;
      }
    }
    for($i=0; $i<$data[4]; $i++){
      $temp = array(
        'codigo'=>$items_example[$i]['codigo'],
        'descr'=>$items_example[$i]['descr'],
        'cod_unidad'=>$items_example[$i]['cod_unidad'],
        'unidad'=>$items_example[$i]['unidad'],
        'precio_unitario'=>$items_example[$i]['precio_unitario'],
        'cant'=>($i+1),
        'importe_total'=>$items_example[$i]['precio_unitario'] * ($i+1),
        'inafecto'=>false,
        'exonerada'=>false,
        'gratuito'=>false,
        'valor_unitario'=>$items_example[$i]['precio_unitario'],
        'base'=>0,
        'igv'=>0,
        'isc'=>0,
        'otros_imp'=>0,
        'ope_inafectas'=>0,
        'ope_exonerada'=>0,
        'ope_gravadas'=>0,
        'ope_gratuitas'=>0,
        'desc'=>0,
      );
      $rpta['items'][] = $temp;
    }
    foreach ($rpta['items'] as $inde => $temp) {
      if(!isset($data[5])) $data[5] = '';
      switch($data[5]){
        case 'I':
          if(($a % 2) == 1){
            $temp['inafecto'] = true;
            $temp['ope_inafectas'] = $temp['importe_total'];
          }else{
            $temp['exonerada'] = true;
            $temp['ope_exonerada'] = $temp['importe_total'];
          }
          break;
        case 'G':
          $temp['gratuito'] = true;
          $temp['ope_gratuitas'] = $temp['importe_total'];
          $temp['importe_total'] = 0;
          $temp['precio_unitario'] = 0;
          $temp['valor_unitario'] = 0;
          break;
        case 'S':
          /*$temp['valor_unitario'] = $temp['precio_unitario']/1.05;
          $temp['base'] = $temp['importe_total']/1.05;
          $temp['isc'] = $temp['importe_total'] - $temp['base'];
          $temp['base'] = $temp['base']/1.18;
          $temp['igv'] = $temp['base'] - $temp['base'];
          $temp['ope_gravadas'] = $temp['base'];*/
          break;
        case 'P':
          //
          break;
        default:
          $temp['valor_unitario'] = $temp['precio_unitario']/1.18;
          $temp['base'] = $temp['importe_total']/1.18;
          $temp['igv'] = $temp['importe_total'] - $temp['base'];
          $temp['ope_gravadas'] = $temp['base'];
          break;
      }
      $rpta['total'] += floatval($temp['importe_total']);
      $rpta['total_igv'] += floatval($temp['igv']);
      $rpta['total_isc'] += floatval($temp['isc']);
      $rpta['total_otros_imp'] += floatval($temp['otros_imp']);
      $rpta['total_ope_inafectas'] += floatval($temp['ope_inafectas']);
      $rpta['total_ope_gravadas'] += floatval($temp['ope_gravadas']);
      $rpta['total_desc'] += floatval($temp['desc']);
      $rpta['total_ope_exoneradas'] += floatval($temp['ope_exonerada']);
      $rpta['total_ope_gratuitas'] += floatval($temp['ope_gratuitas']);
      $rpta['items'][$inde] = $temp;
    }
    if($data[5]=='D'){
      //
    }
    foreach ($rpta as $key=>$value) {
      if($key!='items'&&$key!='fecreg'&&$key!='num'&&$key!='cliente_doc'){
        if(is_numeric($value)){
          $rpta[$key] = number_format($value, 2, '.', '');
        }
      }else{
        if($key=='items'){
          foreach ($value as $key_it => $value_it) {
            foreach ($value_it as $key_it_ => $value_it_) {
              if(is_numeric($value_it_)){
                $rpta['items'][$key_it][$key_it_] = number_format($value_it_, 2, '.', '');
              }
            }
          }
        }
      }
    }
    return $rpta;
  }
}
?>