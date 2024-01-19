<?php
class Controller_lg_impo extends Controller {
	function execute_movimientos(){
		global $f;
		print_r('<pre>');
		$fp = fopen ( "kardex/1301080201_17.dbf.csv","r");
	    $cod = 0;
	    $tmp_cod = 0;
	    /*Almacenar los productos en una variable temporal*/
	    $noimp=0;
	    $fp_p = fopen ("kardex/productos.csv","r");
		$cod_p = 0;
		while ($data_p = fgetcsv ($fp_p, 1000, ";")){
			if($cod_p!=0){
				//Si el foxito importa el codigo sin cero
				$expl=explode(".", $data_p[0]);
				if (strlen($expl[0])==1)
					$data_p[0]='0'.$data_p[0];
				$data_p=array_merge($data_p,array($cod_p));
				$producto[$data_p[0]]=$data_p;
			}
			$cod_p++;
		}
		fclose($fp_p);
		/*Almacenar los clasificadores en una variable temporal*/
		$fc = fopen ( "kardex/clas.csv", "r" );
		$cod_c = 0;
		while ($data_c = fgetcsv ($fc,1000,";")){
			if($cod_c!=0){
				$clasif[$data_c[3]]=$data_c;
			}
			$cod_c++;
		}

		fclose( $fc);
		//Contadores
		$err_prod=array();
		$err_cuenta=array();
		$err_clasif=array();
		$err_unidad=array();
		$no_prod=array();
		$no_movi=array();

		$almacenes = $f->datastore->lg_almacenes->findOne(array('_id'=>new MongoId("51a79e6a4d4a13280700003e")));
		$organizaciones= $f->datastore->mg_organizaciones->findOne(array('_id'=>new MongoId("51a666aa4d4a13540a00000f")));


		while ($data = fgetcsv ($fp, 1000, ";")){
			if($cod!=0){
				if($data[1]=='0'){
					if($data[2]=='INV'){
						$mov = array(
							'autor'=>$f->session->userDB,
							'trabajador'=>$f->session->userDB,
							'fecreg'=>new MongoDate(strtotime('2017-'.$data[1].'-'.$data[3])),
							'fecmod'=>new MongoDate(),
							'saldo'=>str_replace(',', '.',$data[9]),
							'saldo_imp'=>str_replace(',', '.',$data[14]),
							'tipo'=>'I',
							'almacen'=>array(
								'_id' => $almacenes['_id'],
							    "nomb" => $almacenes['nomb'],
							),
							'cant'=>str_replace(',', '.',$data[7]),
							'precio_unitario'=>str_replace(',', '.',$data[10]),
							'total'=>str_replace(',', '.',$data[12]),
							'organizacion'=>array(
								'_id'=>$organizaciones['_id'],
							    'nomb'=>$organizaciones['nomb'],
							)
						);
						/*Verificación de Producto*/
						$prod = $f->datastore->lg_productos->findOne(array('oldid'=>$data[0]));
						if(is_null($prod)){
								if(array_key_exists($data[0],$producto)){
									if($producto[$data[0]][0]==strval($data[0])){
											/*Se encontro nuevo producto*/
											$prod = array(
												'oldid'=>$producto[$data[0]][0],
												'autor'=>$f->session->userDB,
												'trabajador'=>$f->session->userDB,
												'fecreg'=>new MongoDate(),
												'fecmod'=>new MongoDate(),
												'cod'=>$producto[$data[0]][10],
												'nomb'=>utf8_encode($producto[$data[0]][1]),
												'descr'=>utf8_encode($producto[$data[0]][1]),
												'estado'=>'H',
												'precio'=>0,
												'cant'=>0,
												'valor_total'=>0,
												'precio_promedio'=>0,
												'tipo_producto'=>'P'
											);
											$unid=$f->datastore->lg_unidades->findOne(array('nomb'=>utf8_encode($producto[$data[0]][4])));

											/*Agregar unidad si no existe*/
											if(is_null($unid)){
												array_push($err_unidad, $unid);
												$unid = array(
													'nomb'=>utf8_encode($producto[$data[0]][4]),
													'abrev'=>utf8_encode($producto[$data[0]][4]),
													'fecreg'=>new MongoDate(),
													'estado'=>'H',
													'autor'=>$f->session->userDB,
												);
												$f->datastore->lg_unidades->insert($unid);
											}
											$prod['unidad'] = array (
												'_id'=>$unid['_id'],
												'nomb'=>$unid['nomb']
											);

											//Buscar cuenta contable
											$cta = $f->datastore->ct_cuenta->findOne(array('cod'=>$producto[$data[0]][5]));
											if(is_null($cta))
													array_push($err_cuenta, $producto[$data[0]][5]);
											else{
												$prod['cuenta'] = array (
													'_id'=>$cta['_id' ],
												    'cod'=>$cta['cod'],
													'descr'=>$cta['descr']
												);
											}

											/*Verificar si hay clasificador*/
											$cla = $f->datastore->pr_clasificadores->findOne(array('cod'=>$data[16]));
											if(is_null($cla)){
												if(array_key_exists($producto[$data[0]][5],$clasif)){
													if($producto[$data[0]][5]==$clasif[$producto[$data[0]][5]][3]){
														$cla = $f->datastore->pr_clasificadores->findOne(array('cod'=>$clasif[$producto[$data[0]][5]][0]));
													}
													else
														print_r("No se encontro el clasificador de cuenta ".$producto[$data[0]][5]." ...</br>");
												}
												else
													array_push($err_clasif, $clasif[$producto[$data[0]][5]][0]);
											}
											if(is_null($cla))
												array_push($err_clasif, $clasif[$producto[$data[0]][5]][3]);
											else
												$prod[ 'clasif'] = array (
													'_id'=>$cla['_id' ],
													'cod'=>$cla['cod'],
													'descr'=>$cla['descr']
												);

											if(!is_null($cta) && !is_null($cla)){
												//if (empty($err_clasif) || empty($err_cuenta) || empty($err_prod)) {
													$prod=$f->datastore->lg_productos->insert($prod);
												//}
												//else print_r("Incongruencia ");
											}
											else
												array_push($no_prod, $data[0]);
										}
										else
											array_push($err_prod, $data[0]);
								}
								else
									array_push($err_prod, $data[0]);

							}
 						if(isset($prod['cuenta']) && isset($prod['clasif'])){
							$mov['producto'] = array(
								'_id'=>$prod['_id'],
								'cod'=>$prod['cod'],
								'nomb'=>$prod['nomb']
							);
							$mov['cuenta'] = array(
								'_id'=>$prod['cuenta']['_id'],
								'cod'=>$prod['cuenta']['cod'],
								'descr'=>$prod['cuenta']['descr']
							);
							$mov['clasif'] = array(
							    '_id'=>$prod['clasif']['_id'],
							    'cod'=>$prod['clasif']['cod'],
							    'descr'=>$prod['clasif']['descr']
							);
						}
						/*Verificacion de cuentas contables*/
						$prod = $f->datastore->lg_productos->findOne(array('oldid'=>$data[0]));
						if(isset($prod['clasif']) && isset($prod['cuenta'])){
								if(empty($err_clasif) || empty($err_cuenta) || empty($err_prod)){
									$stock=$f->datastore->lg_stocks->findOne(array('producto'=>$prod['_id'],'almacen'=> $almacenes['_id']));
									if(is_null($stock)){
										$stock=array(
											'producto'=> $prod['_id'],
											'almacen'=> $almacenes['_id'],
											'stock'=>floatval($data[9]),
									        'costo'=>$prod['precio'],
										);
										$stock=$f->datastore->lg_stocks->insert($stock);
									}
									else{
										$stock_upd=array(
											'stock'=>floatval($data[9]),
									        'costo'=>$prod['precio'],
										);
										$stock=$f->datastore->lg_stocks->update(array('_id'=>$stock['_id']),array('$set'=>$stock_upd));
									}
									$mov['stock']=$stock['_id'];
									$mov=$f->datastore->lg_movimientos->insert($mov);
								}
								else
								{
		                        	array_push($no_movi, $mov);
		                        }
						}
                        else{
                        	print_r("primero");
                        	var_dump($prod);
                        	array_push($no_movi, $mov);
                        }
                        
                        //if(empty($err_clasif) || empty($err_cuenta) || empty($err_prod))
                        {
                        	if(isset($prod['clasif']) && isset($prod['clasif'])){
                        		$f->datastore->lg_productos->update(array('_id'=>$prod['_id']),array('$set'=>array(
									'cant'=>floatval($data[9]),
									'valor_total'=>floatval($data[14]),
									'stock'=>array(
										array(
								    		'inicializado'=>true,
											'almacen'=>array(
												'_id' => $almacenes['_id'],
											    "nomb" => $almacenes['nomb'],
											),
											'actual'=>floatval($data[9]),
								            'valor_total'=>floatval($data[14])
										)
									)
								)));
								$stock=$f->datastore->lg_stocks->findOne(array('producto'=>$prod['_id'],'almacen'=> $almacenes['_id']));
								if(is_null($stock)){
									$stock=array(
										'producto'=> $prod['_id'],
										'almacen'=> $almacenes['_id'],
										'stock'=>floatval($data[9]),
								        'costo'=>$prod['precio'],
									);
									$stock=$f->datastore->lg_stocks->insert($stock);
								}
								else{
									$stock_upd=array(
										'stock'=>floatval($data[9]),
								        'costo'=>$prod['precio'],
									);
									$stock=$f->datastore->lg_stocks->update(array('_id'=>$stock['_id']),array('$set'=>$stock_upd));
								}
                        	}	
							else
								array_push($no_prod, $prod);
                        }			
						//else
						//	array_push($no_prod, $prod);
						$tmp_cod++;
						}
					}
					else{
						if(strlen($data[1])==1) $data[1] = '0'.$data[1];
							if(strlen($data[3])==1) $data[3] = '0'.$data[3];
								$mov = array(
								   'autor'=>$f->session->userDB,
								   'trabajador'=>$f->session->userDB,
								   'fecreg'=>new MongoDate(strtotime('2017-'.$data[1].'-'.$data[3])),
								   'fecmod'=>new MongoDate(),
								   'saldo'=>str_replace(',', '.',$data[9]),
								   'saldo_imp'=>str_replace(',', '.',$data[14]),
								);
						$prod = $f->datastore->lg_productos->findOne(array( 'oldid'=>$data[0]));
						/*Verificación de Producto*/
						if(is_null($prod)){
								if(array_key_exists($data[0],$producto)){
									if($producto[$data[0]][0]==strval($data[0])){
										//var_dump($data[0]);

											/*Se encontro nuevo producto*/
											$prod = array(
												'oldid'=>$producto[$data[0]][0],
												'autor'=>$f->session->userDB,
												'trabajador'=>$f->session->userDB,
												'fecreg'=>new MongoDate(),
												'fecmod'=>new MongoDate(),
												'cod'=>$producto[$data[0]][10],
												'nomb'=>utf8_encode($producto[$data[0]][1]),
												'descr'=>utf8_encode($producto[$data[0]][1]),
												'estado'=>'H',
												'precio'=>0,
												'cant'=>0,
												'valor_total'=>0,
												'precio_promedio'=>0,
												'tipo_producto'=>'P'
											);

											$unid=$f->datastore->lg_unidades->findOne(array('nomb'=>utf8_encode($producto[$data[0]][4])));
											//print_r($producto[$data[0]]);
											/*Agregar unidad si no existe*/
											if(is_null($unid)){
												array_push($err_unidad, $unid);
												$unid = array(
													'nomb'=>utf8_encode($producto[$data[0]][4]),
													'abrev'=>utf8_encode($producto[$data[0]][4]),
													'fecreg'=>new MongoDate(),
													'estado'=>'H',
													'autor'=>$f->session->userDB,
												);
												$f->datastore->lg_unidades->insert($unid);
											}
											$prod['unidad'] = array (
												'_id'=>$unid['_id'],
												'nomb'=>$unid['nomb']
											);

											//Buscar cuenta contable
											$cta = $f->datastore->ct_cuenta->findOne(array('cod'=>$producto[$data[0]][5]));
											if(is_null($cta))
													array_push($err_cuenta, $producto[$data[0]][5]);
											else{
												$prod['cuenta'] = array (
													'_id'=>$cta['_id' ],
												    'cod'=>$cta['cod'],
													'descr'=>$cta['descr']
												);
											}

											/*Verificar si hay clasificador*/
											$cla = $f->datastore->pr_clasificadores->findOne(array('cod'=>$data[16]));
											if(is_null($cla)){
												if(array_key_exists($producto[$data[0]][5],$clasif)){
													if($producto[$data[0]][5]==$clasif[$producto[$data[0]][5]][3]){
														$cla = $f->datastore->pr_clasificadores->findOne(array('cod'=>$clasif[$producto[$data[0]][5]][0]));
													}
													else
														print_r("No se encontro el clasificador de cuenta ".$producto[$data[0]][5]." ...</br>");
												}
												else
													array_push($err_clasif, $clasif[$producto[$data[0]][5]][0]);
											}
											if(is_null($cla))
												array_push($err_clasif, $clasif[$producto[$data[0]][5]][3]);
											else
												$prod[ 'clasif'] = array (
													'_id'=>$cla['_id' ],
													'cod'=>$cla['cod'],
													'descr'=>$cla['descr']
												);

											if(!is_null($cta) && !is_null($cla)){
												//if (empty($err_clasif) || empty($err_cuenta) || empty($err_prod)) 
													$prod=$f->datastore->lg_productos->insert($prod);
												//else print_r("Incongruencia ");
											}
											else
												array_push($no_prod, $data[0]);
										}
										else
											print_r("No se encontro el producto");
								}
								else
									array_push($err_prod, $data[0]);

							}
						
						if(isset($prod['cuenta']) && isset($prod['clasif'])){
							$mov['producto'] = array (
							   '_id'=>$prod['_id'],
							   'cod'=>$prod['cod'],
							   'nomb'=>$prod['nomb']
							);
							$mov['cuenta'] = array(
								'_id'=>$prod['cuenta']['_id'],
								'cod'=>$prod['cuenta']['cod'],
								'descr'=>$prod['cuenta']['descr']
							);
							$mov['clasif'] = array(
							    '_id'=>$prod['clasif']['_id'],
							    'cod'=>$prod['clasif']['cod'],
							    'descr'=>$prod['clasif']['descr']
							);
						}
							switch ($data[4]){
								case 'PEB':
								      $mov['tipo'] = 'E';
								      $mov['almacen'] = array(
								                  '_id'=>$almacenes['_id'],
								                  "nomb"=>$almacenes['nomb']
								      );
								      $mov['documento'] = array(
								           'tipo'=>'PEB',
								           'cod'=>$data[5]
								      );
								      $mov['cant'] = $data[7];
								      $mov['precio_unitario'] = str_replace(',', '.',$data[10]);
								      $mov['total'] = str_replace(',', '.',$data[12]);
								      break;
								case 'OC':
								      $mov['tipo'] = 'E';
								      $mov['almacen'] = array(
								                  '_id'=>$almacenes['_id'],
								                  "nomb"=>$almacenes['nomb']
								      );
								      $mov['documento'] = array(
								           'tipo'=>'OC',
								           'cod'=>$data[5]
								      );
								      $mov['cant'] = $data[7];
								      $mov['precio_unitario'] = str_replace(',', '.',$data[10]);
								      $mov['total'] = str_replace(',', '.',$data[12]);
								      break;
								case 'PAL':
								      $mov['tipo'] = 'E';
								      $mov['almacen'] = array(
								                  '_id'=>$almacenes['_id'],
								                  "nomb"=>$almacenes['nomb']
								      );
								      $mov['documento'] = array(
								           'tipo'=>'PAL',
								           'cod'=>$data[5]
								      );
								      $mov['cant'] = $data[7];
								      $mov['precio_unitario'] = str_replace(',', '.',$data[10]);
								      $mov['total'] = str_replace(',', '.',$data[12]);
								      break;
								case 'PAE':
								      $mov['tipo'] = 'E';
								      $mov['almacen'] = array(
								                  '_id'=>$almacenes['_id'],
								                  "nomb"=>$almacenes['nomb']
								      );
								      $mov['documento'] = array(
								           'tipo'=>'PAE',
								           'cod'=>$data[5]
								      );
								      $mov['cant'] = $data[7];
								      $mov['precio_unitario'] = str_replace(',', '.',$data[10]);
								      $mov['total'] = str_replace(',', '.',$data[12]);
								      break;
								case 'NCE':
								      $mov['tipo'] = 'E';
								      $mov['almacen'] = array(
								                  '_id'=>$almacenes['_id'],
								                  "nomb"=>$almacenes['nomb']
								      );
								      $mov['documento'] = array(
								           'tipo'=>'NCE',
								           'cod'=>$data[5]
								      );
								      $mov['cant'] = $data[7];
								      $mov['precio_unitario'] = str_replace(',', '.',$data[10]);
								      $mov['total'] = str_replace(',', '.',$data[12]);
								      break;
								case 'IFE':
								      $mov['tipo'] = 'E';
								      $mov['almacen'] = array(
								                  '_id'=>$almacenes['_id'],
								                  "nomb"=>$almacenes['nomb']
								      );
								      $mov['documento'] = array(
								           'tipo'=>'IFE',
								           'cod'=>$data[5]
								      );
								      $mov['cant'] = $data[7];
								      $mov['precio_unitario'] = str_replace(',', '.',$data[10]);
								      $mov['total'] = str_replace(',', '.',$data[12]);
								      break;
								case 'IFE':
								      $mov['tipo'] = 'E';
								      $mov['almacen'] = array(
								                  '_id'=>$almacenes['_id'],
								                  "nomb"=>$almacenes['nomb']
								      );
								      $mov['documento'] = array(
								           'tipo'=>'IFE',
								           'cod'=>$data[5]
								      );
								      $mov['cant'] = $data[7];
								      $mov['precio_unitario'] = str_replace(',', '.',$data[10]);
								      $mov['total'] = str_replace(',', '.',$data[12]);
								      break;
								case 'PSB':
								      $mov['tipo'] = 'S';
								      $mov['almacen'] = array(
								                  '_id'=>$almacenes['_id'],
								                  "nomb"=>$almacenes['nomb']
								      );
								      $mov['documento'] = array(
								           'tipo'=>'PSB',
								           'cod'=>$data[5]
								      );
								      $mov['cant'] = $data[8];
								      $mov['precio_unitario'] = str_replace(',', '.',$data[11]);
								      $mov['total'] = str_replace(',', '.',$data[13]);
								      break;
								case 'PAS':
								      $mov['tipo'] = 'S';
								      $mov['almacen'] = array(
								                  '_id'=>$almacenes['_id'],
								                  "nomb"=>$almacenes['nomb']
								      );
								      $mov['documento'] = array(
								           'tipo'=>'PAS',
								           'cod'=>$data[5]
								      );
								      $mov['cant'] = $data[8];
								      $mov['precio_unitario'] = str_replace(',', '.',$data[11]);
								      $mov['total'] = str_replace(',', '.',$data[13]);
								      break;
								case 'NCS':
								      $mov['tipo'] = 'S';
								      $mov['almacen'] = array(
								                  '_id'=>$almacenes['_id'],
								                  "nomb"=>$almacenes['nomb']
								      );
								      $mov['documento'] = array(
								           'tipo'=>'NCS',
								           'cod'=>$data[5]
								      );
								      $mov['cant'] = $data[8];
								      $mov['precio_unitario'] = str_replace(',', '.',$data[11]);
								      $mov['total'] = str_replace(',', '.',$data[13]);
								      break;
								case 'IFS':
								      $mov['tipo'] = 'S';
								      $mov['almacen'] = array(
								                  '_id'=>$almacenes['_id'],
								                  "nomb"=>$almacenes['nomb']
								      );
								      $mov['documento'] = array(
								           'tipo'=>'IFS',
								           'cod'=>$data[5]
								      );
								      $mov['cant'] = $data[8];
								      $mov['precio_unitario'] = str_replace(',', '.',$data[11]);
								      $mov['total'] = str_replace(',', '.',$data[13]);
								      break;
							}
							switch ($data[6]){
								case 'ADC':
								      $mov['organizacion'] = array(
										'_id'=>$organizaciones['_id'],
									    'nomb'=>$organizaciones['nomb'],
								      );
								      break;
								case 'CEM':
								      $mov['organizacion'] = array(
								           '_id'=>new MongoId('51a50f0f4d4a13c409000013'),
								           'nomb'=>'Unidad de Cementerio y Servicios Funerarios'
								      );
								      break;
								case 'ICH':
								      $mov['organizacion'] = array(
								           '_id'=>new MongoId('51a50dd94d4a13c40900000e'),
								           'nomb'=>'Unidad del CAR del Ni\u00f1o \"Chavez de la Rosa\"'
								      );
								      break;
								case 'CSM':
								      $mov['organizacion'] = array(
								           '_id'=>new MongoId('51a50e614d4a13c409000012'),
								           'nomb'=>'Unidad del Centro de Salud Mental "Moisés Heresi"'
								      );
								      break;
								case 'ALB':
								      $mov['organizacion'] = array(
								           '_id'=>new MongoId('51a50da64d4a13f806000014'),
								           'nomb'=>'Unidad de del CAR del Adulto Mayor "El buen Jesús"'
								      );
								      break;
								case 'AGU':
								      $mov['organizacion'] = array(
								           '_id'=>new MongoId('51a50f3d4d4a13c409000014'),
								           'nomb'=>'Balneario de Jesús'
								      );
								      break;
								case 'INM':
								      $mov['organizacion'] = array(
								           '_id'=>new MongoId('51a50edc4d4a13441100000e'),
								           'nomb'=>'Unidad de Patrimonio, Muebles e Inmuebles'
								      );
								      break;
								case 'HOG':
								      $mov['organizacion'] = array(
								           '_id'=>new MongoId('51a50d804d4a13f806000012'),
								           'nomb'=>'Unidad Casa Refugio Hogar de María y Hospedaje el Buen Samaritano'
								      );
								      break;
							}
							/*Verificacion de indentificadores para introducir movimiento*/
							$prod = $f->datastore->lg_productos->findOne(array('oldid'=>$data[0]));
							if(isset($prod['clasif']) && isset($prod['cuenta'])){
									if(empty($err_clasif) || empty($err_cuenta) || empty($err_prod)){
										$stock=$f->datastore->lg_stocks->findOne(array('producto'=>$prod['_id'],'almacen'=> $almacenes['_id']));
										if(is_null($stock)){
											$stock=array(
												'producto'=> $prod['_id'],
												'almacen'=> $almacenes['_id'],
												'stock'=>floatval($data[9]),
										        'costo'=>$prod['precio'],
											);
											$stock=$f->datastore->lg_stocks->insert($stock);
										}
										else{
											$stock_upd=array(
												'stock'=>floatval($data[9]),
										        'costo'=>$prod['precio'],
											);
											$stock=$f->datastore->lg_stocks->update(array('_id'=>$stock['_id']),array('$set'=>$stock_upd));
										}
										$mov['stock']=$stock['_id'];
										$mov=$f->datastore->lg_movimientos->insert($mov);
									}
									else
										array_push($no_movi, $mov);
							}
							else{
								print_r("Segundo");
								var_dump($prod);
								array_push($no_movi, $mov);
							}

							/*Verificacion de cuentas, clasificadores y Id's*/
							//if(empty($err_clasif) || empty($err_cuenta) || empty($err_prod)) 
							{
								if(isset($prod['clasif']) && isset($prod['clasif'])){
		                    		$f->datastore->lg_productos->update(array('_id'=>$prod['_id']),array('$set'=>array(
										'cant'=>floatval($data[9]),
										'valor_total'=>floatval($data[14]),
										'stock'=>array(
											array(
									    		'inicializado'=>true,
												'almacen'=>array(
													'_id' => $almacenes['_id'],
												    "nomb" => $almacenes['nomb'],
												),
												'actual'=>floatval($data[9]),
									            'valor_total'=>floatval($data[14])
											)
										)
									)));
									$stock=$f->datastore->lg_stocks->findOne(array('producto'=>$prod['_id'],'almacen'=> $almacenes['_id']));
									if(is_null($stock)){
										$stock=array(
											'producto'=> $prod['_id'],
											'almacen'=> $almacenes['_id'],
											'stock'=>floatval($data[9]),
									        'costo'=>doubleval($prod['precio']),
										);
										$stock=$f->datastore->lg_stocks->insert(($stock));
									}
									else{
										$stock_upd=array(
											'stock'=>floatval($data[9]),
									        'costo'=>doubleval($prod['precio']),
										);
										$stock=$f->datastore->lg_stocks->update(array('_id'=>$stock['_id']),array('$set'=>$stock_upd));
									}
                        		}
                        		else
                        			array_push($no_prod, $prod);
							}
							//else 
							//{
							//	array_push($no_prod, $prod);
							//	/*
							//	print_r($err_clasif);
							//	print_r('</br>');
							//	print_r($err_cuenta);
							//	print_r('</br>');
							//	print_r($err_prod);
							//	print_r('</br>');	
							//	*/
							//}
						$tmp_cod++;
				}
				//else{
					if($data[1]=='0')
					$noimp++;	
				//}
				
	        }
			$cod++;
		}

		if (!empty($err_clasif) || !empty($err_cuenta) || !empty($err_prod))
			print_r('quizas no se agregaron algunos productos ');
		print_r('kardex de 2 columna = 0 </br>');
		print_r($noimp);
		print_r('</br>');
		print_r('productos a agregar manualmente</br>');
		//print_r(array_unique($err_prod));
		print_r(($err_prod));
		print_r('clasificadores a agregar manualmente</br>');
		print_r(array_unique($err_clasif));
		//var_dump(($err_clasif));
		print_r('cuentas a agregar manualmente</br>');
		//print_r(array_unique($err_cuenta));
		var_dump(($err_cuenta));
		print_r('productos no agregados</br>');
		print_r($no_prod);
		//print_r(array_unique($no_prod));
		print_r('movimientos no agregados</br>');
		print_r($no_movi);
		//print_r(array_unique($no_mov));
		print_r('</pre>');
		echo "terminado</br>";
	}
}
?>
