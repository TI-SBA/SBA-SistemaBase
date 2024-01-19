<?php
class Controller_ac_perm extends Controller {
	function execute_index(){
		global $f;
		$data = array();
		//Seguridad
		//$data[] = array("id"=>"ac","descr"=>'Seguridad',"sangria"=>"0");
		$data[] = array("id"=>"ac.groups","descr"=>'El Usuario puede crear y modificar <b>Grupos</b>',"sangria"=>"1");
		$data[] = array("id"=>"ac.users","descr"=>'El Usuario puede crear y modificar <b>Usuarios</b>',"sangria"=>"1");
		$data[] = array("id"=>"ac.logs","descr"=>'El Usuario puede consultar <b>Actividad de Usuarios</b>',"sangria"=>"1");
		//Maestros Generales
		//$data[] = array("id"=>"mg","descr"=>'Maestro Generales',"sangria"=>'0');
		$data[] = array("id"=>"mg.titu","descr"=>'El Usuario puede editar la <b>Información del Titular</b>',"sangria"=>"1");
		$data[] = array("id"=>"mg.orga","descr"=>'El Usuario puede editar la <b>Estructura Organizacional</b>',"sangria"=>"1");
		$data[] = array("id"=>"mg.vars","descr"=>'El Usuario puede editar las <b>Variables Globales</b>',"sangria"=>"1");
		$data[] = array("id"=>"mg.serv","descr"=>'El Usuario puede editar los <b>Servicios</b>',"sangria"=>"1");
		//Porteria
		$data[] = array("id"=>"po.visi","descr"=>'El Usuario puede agregar y editar <b>Visitantes del registro externo</b>',"sangria"=>"1");
		//Tramite documentario
		//$data[] = array("id"=>"td","descr"=>'Tramite Documentario',"sangria"=>'0');
		$data[] = array("id"=>"td.tupa","descr"=>'El Usuario puede consultar el <b>TUPA</b>',"sangria"=>"1");
		$data[] = array("id"=>"td.tupa.ant","descr"=>'El Usuario puede consultar <b>versiones anteriores</b> del TUPA',"sangria"=>"2","padre"=>"td.tupa");
		$data[] = array("id"=>"td.tupa.edit","descr"=>'El Usuario puede crear un <b>nuevo TUPA</b> o editar los procedimientos',"sangria"=>"3","padre"=>"td.tupa");
		$data[] = array("id"=>"td.tdoc","descr"=>'El Usuario puede editar los <b>Tipos de Documento</b>',"sangria"=>"1");
		$data[] = array("id"=>"td.orga","descr"=>'El Usuario puede editar los <b>&Oacute;rganos Externos</b>',"sangria"=>"1");
		$data[] = array("id"=>"td.expd","descr"=>'El Usuario puede gestionar <b>Expedientes</b>',"sangria"=>"1");
		$data[] = array("id"=>"td.expd.area","descr"=>'El Usuario puede registrar Expedientes de su <b>respectiva Área</b>',"sangria"=>"2","padre"=>"td.expd");
		$data[] = array("id"=>"td.expd.gest.int","descr"=>'El Usuario puede registrar Expedientes de <b>Gestores Internos</b>',"sangria"=>"2","padre"=>"td.expd.area");
		$data[] = array("id"=>"td.expd.gest.ext","descr"=>'El Usuario puede registrar Expedientes de <b>Gestores Externos</b>',"sangria"=>"2","padre"=>"td.expd.area");
		$data[] = array("id"=>"td.expd.hist","descr"=>'El Usuario puede <b>consultar los Expedientes</b> de su <b>respectiva Área</b>',"sangria"=>"2","padre"=>"td.expd");
		$data[] = array("id"=>"td.expd.hist.org","descr"=>'El Usuario puede <b>consultar Expedientes</b> de <b>toda la Organización</b>',"sangria"=>"3","padre"=>"td.expd.hist");
		$data[] = array("id"=>"td.repo","descr"=>'El Usuario puede <b>visualizar Reportes</b> respectivos a todas las &aacute;reas',"sangria"=>"1");
		//Cementerio
		//$data[] = array("id"=>"cm","descr"=>'Cementerio',"sangria"=>'0');
		$data[] = array("id"=>"cm.accs","descr"=>'El Usuario puede editar los <b>Accesorios</b>',"sangria"=>"1");
		$data[] = array("id"=>"cm.espc","descr"=>'El Usuario puede editar los <b>Espacios</b>',"sangria"=>"1");
		$data[] = array("id"=>"cm.ctas","descr"=>'El Usuario puede administrar <b>Propietarios y Ocupantes</b>',"sangria"=>"1");
		$data[] = array("id"=>"cm.ctas.ant","descr"=>'El Usuario puede registrar <b>Ocupantes Anteriores</b>',"sangria"=>"2","padre"=>"cm.ctas");
		$data[] = array("id"=>"cm.oper","descr"=>'El Usuario puede gestionar <b>Operaciones</b>',"sangria"=>"1");
		$data[] = array("id"=>"cm.oper.conc","descr"=>'El Usuario puede gestionar <b>Concesiones</b>',"sangria"=>"2","padre"=>"cm.oper");
		$data[] = array("id"=>"cm.comp","descr"=>'El Usuario puede gestionar <b>Comprobantes</b>',"sangria"=>"1");
		$data[] = array("id"=>"cm.conf","descr"=>"El Usuario puede gestionar la <b>Configuraci&oacute;n</b> del m&oacute;dulo","sangria"=>"1");
		$data[] = array("id"=>"cm.reps","descr"=>"El Usuario puede consultar <b>Reportes</b>","sangria"=>"1");
		
		//Inmuebles
		$data[] = array("id"=>"in.loca","descr"=>'El Usuario puede crear y editar <b>Locales</b>',"sangria"=>"1");
		$data[] = array("id"=>"in.ctas","descr"=>'El Usuario puede administrar <b>Arrendatarios, Representantes y Avales</b>',"sangria"=>"1");
		$data[] = array("id"=>"in.arre","descr"=>'El Usuario puede gestionar <b>Arrendamientos</b>',"sangria"=>"1");
		$data[] = array("id"=>"in.rent","descr"=>'El Usuario puede gestionar <b>Rentas</b>',"sangria"=>"1");
		$data[] = array("id"=>"in.comp","descr"=>'El Usuario puede gestionar <b>Comprobantes</b>',"sangria"=>"1");
		$data[] = array("id"=>"in.reps","descr"=>"El Usuario puede consultar <b>Reportes</b>","sangria"=>"1");
		
		//Logistica
		$data[] = array("id"=>"lg.almc","descr"=>'El Usuario puede crear y editar <b>Almacenes</b>',"sangria"=>"1");
		$data[] = array("id"=>"lg.ctas","descr"=>'El Usuario puede administrar <b>Proveedores</b>',"sangria"=>"1");
		$data[] = array("id"=>"lg.umed","descr"=>'El Usuario puede crear y editar <b>Unidades de Medida</b>',"sangria"=>"1");
		$data[] = array("id"=>"lg.prod","descr"=>'El Usuario puede crear y editar <b>Productos</b>',"sangria"=>"1");
		$data[] = array("id"=>"lg.bien","descr"=>'El Usuario puede registrar <b>Bienes Auxiliares</b>',"sangria"=>"1");
		$data[] = array("id"=>"lg.bien.act","descr"=>"El Usuario puede registrar <b>Activos</b>","sangria"=>"2","padre"=>"lg.bien");
		$data[] = array("id"=>"lg.bien.nod","descr"=>"El Usuario puede registrar <b>Bienes no Depreciables</b>","sangria"=>"2","padre"=>"lg.bien");
		$data[] = array("id"=>"lg.bien.aux","descr"=>"El Usuario puede registrar <b>Bienes Auxiliares</b>","sangria"=>"2","padre"=>"lg.bien");
		$data[] = array("id"=>"lg.serv","descr"=>'El Usuario puede crear y editar <b>Servicios</b>',"sangria"=>"1");
		$data[] = array("id"=>"lg.stck","descr"=>"El Usuario puede consultar <b>Stocks</b>","sangria"=>"1");
		$data[] = array("id"=>"lg.stck.min","descr"=>"El Usuario puede actualizar el <b>Stock Mínimo y Máximo</b>","sangria"=>"2","padre"=>"lg.stock");
		$data[] = array("id"=>"lg.stck.ini","descr"=>"El Usuario puede registrar el <b>Movimiento Inicial de los Productos</b>","sangria"=>"2","padre"=>"lg.stock");
		$data[] = array("id"=>"lg.cnec","descr"=>"El Usuario puede consultar el Cuadro de Necesidades","sangria"=>"1");
		$data[] = array("id"=>"lg.cnec.dep","descr"=>"El Usuario puede crear el Cuadro de Necesidades <b>de su dependencia</b>","sangria"=>"2","padre"=>"lg.cnec");
		$data[] = array("id"=>"lg.cnec.dep.edit","descr"=>"El Usuario puede <b>crear y editar</b> el Cuadro de Necesidades","sangria"=>"3","padre"=>"lg.cnec.dep");
		$data[] = array("id"=>"lg.cnec.org","descr"=>"El Usuario puede revisar los Cuadros de Necesidades de <b>todas las dependencias</b>","sangria"=>"2","padre"=>"lg.cnec");
		$data[] = array("id"=>"lg.cnec.org.edit","descr"=>"El Usuario puede <b>aprobar y habilitar</b> el Cuadro de Necesidades","sangria"=>"3","padre"=>"lg.cnec.org");
		$data[] = array("id"=>"lg.cotz","descr"=>"El Usuario puede crear y editar <b>Cotizaciones</b>","sangria"=>"1");
		/*$data[] = array("id"=>"lg.peds","descr"=>"El Usuario puede consultar <b>Pedidos Internos</b>","sangria"=>"1");
		$data[] = array("id"=>"lg.peds.edit","descr"=>"El Usuario puede <b>crear, editar</b> Pedidos Internos","sangria"=>"2","padre"=>"lg.peds");
		$data[] = array("id"=>"lg.peds.rev","descr"=>"El Usuario puede <b>revisar</b> Pedidos Internos","sangria"=>"2","padre"=>"lg.peds");
		$data[] = array("id"=>"lg.peds.fin","descr"=>"El Usuario puede <b>finalizar</b> Pedidos Internos","sangria"=>"3","padre"=>"lg.peds.rev");*/

		$data[] = array("id"=>"lg.pedi","descr"=>"El Usuario puede consultar los <b>Requerimientos</b>","sangria"=>"1");
		$data[] = array("id"=>"lg.pedi.nuev","descr"=>"El Usuario puede consultar los <b>Requerimientos</b> de su programa asociado","sangria"=>"2","padre"=>"lg.pedi");
		$data[] = array("id"=>"lg.pedi.bien","descr"=>"El Usuario puede consultar los <b>Requerimientos (Bienes) - pendientes</b> de todos los programas de la institucion","sangria"=>"2","padre"=>"lg.pedi");
		$data[] = array("id"=>"lg.pedi.serv","descr"=>"El Usuario puede consultar los <b>Requerimientos (Servicios) - pendientes</b> de todos los programas de la institucion","sangria"=>"2","padre"=>"lg.pedi");
		$data[] = array("id"=>"lg.pedi.loca","descr"=>"El Usuario puede consultar los <b>Requerimientos (Locacion) - pendientes</b> de todos los programas de la institucion","sangria"=>"2","padre"=>"lg.pedi");
		$data[] = array("id"=>"lg.pedi.todo","descr"=>"El Usuario puede consultar todos los <b>Requerimientos</b>de todos los programas de la institucion","sangria"=>"2","padre"=>"lg.pedi");

		$data[] = array("id"=>"lg.coti.todo","descr"=>"El Usuario puede consultar todas las <b>Cotizaciones</b>","sangria"=>"1");

		$data[] = array("id"=>"lg.soli","descr"=>"Solicitudes de certificacion","sangria"=>"1");
		$data[] = array("id"=>"lg.soli.nuev","descr"=>"El Usuario puede consultar <b>Solicitudes de certificacion</b> Nuevas","sangria"=>"2","padre"=>"lg.soli");
		$data[] = array("id"=>"lg.soli.envi","descr"=>"El Usuario puede consultar <b>Solicitudes de certificacion</b> Enviadas a Presupuesto","sangria"=>"2","padre"=>"lg.soli");
		$data[] = array("id"=>"lg.soli.rece","descr"=>"El Usuario puede consultar <b>Solicitudes de certificacion</b> Recepcionadas por Presupuesto","sangria"=>"2","padre"=>"lg.soli");
		$data[] = array("id"=>"lg.soli.apro","descr"=>"El Usuario puede consultar <b>Solicitudes de certificacion</b> Aprobados por Presupuesto","sangria"=>"2","padre"=>"lg.soli");

		$data[] = array("id"=>"lg.cert","descr"=>"Certificacion presupuestal","sangria"=>"1");
		$data[] = array("id"=>"lg.cert.nuev","descr"=>"El Usuario puede consultar <b>Certificacion presupuestal</b> Nuevas","sangria"=>"2","padre"=>"lg.cert");
		$data[] = array("id"=>"lg.cert.apro","descr"=>"El Usuario puede consultar <b>Certificacion presupuestal</b> Aprobados por Presupuesto","sangria"=>"2","padre"=>"lg.cert");
		$data[] = array("id"=>"lg.cert.envi","descr"=>"El Usuario puede consultar <b>Certificacion presupuestal</b> Enviadas a Logistica","sangria"=>"2","padre"=>"lg.cert");
		$data[] = array("id"=>"lg.cert.rece","descr"=>"El Usuario puede consultar <b>Certificacion presupuestal</b> Recepcionadas por Logistica","sangria"=>"2","padre"=>"lg.cert");

		$data[] = array("id"=>"lg.orde","descr"=>"Orden de compra","sangria"=>"1");
		$data[] = array("id"=>"lg.orde.nuev","descr"=>"El Usuario puede consultar <b>Ordenes de compra</b> Nuevas","sangria"=>"2","padre"=>"lg.orde");
		$data[] = array("id"=>"lg.orde.apro","descr"=>"El Usuario puede consultar <b>Ordenes de compra</b> Aprobados","sangria"=>"2","padre"=>"lg.orde");
		$data[] = array("id"=>"lg.orde.envi","descr"=>"El Usuario puede consultar <b>Ordenes de compra</b> Enviadas a proveedor","sangria"=>"2","padre"=>"lg.orde");
		$data[] = array("id"=>"lg.orde.rece","descr"=>"El Usuario puede consultar productos/bienes de <b>Ordenes de compra</b> Recepcionadas por Logistica","sangria"=>"2","padre"=>"lg.orde");

		$data[] = array("id"=>"lg.orse","descr"=>"Orden de servicio","sangria"=>"1");
		$data[] = array("id"=>"lg.orse.nuev","descr"=>"El Usuario puede consultar <b>Ordenes de servicio</b> Nuevas","sangria"=>"2","padre"=>"lg.orde");
		$data[] = array("id"=>"lg.orse.apro","descr"=>"El Usuario puede consultar <b>Ordenes de servicio</b> Aprobados","sangria"=>"2","padre"=>"lg.orde");
		$data[] = array("id"=>"lg.orse.envi","descr"=>"El Usuario puede consultar <b>Ordenes de servicio</b> Enviadas a proveedor","sangria"=>"2","padre"=>"lg.orde");
		$data[] = array("id"=>"lg.orse.conf","descr"=>"El Usuario puede consultar productos/bienes de <b>Ordenes de servicio</b> con conformidad de ejecucion","sangria"=>"2","padre"=>"lg.orde");
		
		$data[] = array("id"=>"lg.note","descr"=>"El Usuario puede consultar <b>Notas de Entrada</b>","sangria"=>"1");
		$data[] = array("id"=>"lg.note.edit","descr"=>"El Usuario puede <b>crear, editar</b> Notas de Entrada","sangria"=>"2","padre"=>"lg.note");
		$data[] = array("id"=>"lg.note.rev","descr"=>"El Usuario puede <b>revisar Notas</b> de Entrada","sangria"=>"2","padre"=>"lg.note");
		$data[] = array("id"=>"lg.note.fin","descr"=>"El Usuario puede <b>finalizar Notas</b> de Entrada","sangria"=>"3","padre"=>"lg.note.rev");
		$data[] = array("id"=>"lg.pcsa","descr"=>"El Usuario puede consultar <b>PECOSAS</b>","sangria"=>"1");
		$data[] = array("id"=>"lg.pcsa.edit","descr"=>"El Usuario puede <b>crear, editar</b> PECOSAS","sangria"=>"2","padre"=>"lg.pcsa");
		$data[] = array("id"=>"lg.pcsa.rev","descr"=>"El Usuario puede <b>revisar</b> PECOSAS","sangria"=>"2","padre"=>"lg.pcsa");
		$data[] = array("id"=>"lg.pcsa.fin","descr"=>"El Usuario puede <b>finalizar</b> PECOSAS","sangria"=>"3","padre"=>"lg.pcsa.rev");
		$data[] = array("id"=>"lg.pcsa.ent","descr"=>"El Usuario puede <b>confirmar entrega</b> de PECOSAS","sangria"=>"2","padre"=>"lg.pcsa");
		$data[] = array("id"=>"lg.pcsa.rec","descr"=>"El Usuario puede <b>confirmar recepción</b> de PECOSAS","sangria"=>"2","padre"=>"lg.pcsa");
		$data[] = array("id"=>"lg.krdx","descr"=>"El Ususario puede consultar el <b>Kardex</b>","sangria"=>"1");
		$data[] = array("id"=>"lg.movs","descr"=>"El Ususario puede consultar <b>Movimientos</b>","sangria"=>"1");
		$data[] = array("id"=>"lg.cert","descr"=>"El Usuario puede consultar <b>Certificaciones Presupuestarias</b>","sangria"=>"1");
		$data[] = array("id"=>"lg.cert.edit","descr"=>"El Usuario puede <b>crear, editar</b> Certificaciones Presupuestarias","sangria"=>"2","padre"=>"lg.cert");
		$data[] = array("id"=>"lg.cert.rev","descr"=>"El Usuario puede <b>revisar</b> Certificaciones Presupuestarias","sangria"=>"2","padre"=>"lg.cert");
		$data[] = array("id"=>"lg.cert.fin","descr"=>"El Usuario puede <b>finalizar</b> Certificaciones Presupuestarias","sangria"=>"3","padre"=>"lg.cert.rev");
		$data[] = array("id"=>"lg.reps","descr"=>"El Usuario puede consultar <b>Reportes</b>","sangria"=>"1");
		//$data[] = array("id"=>"","descr"=>"","sangria"=>"");
		//Planificacion y presupuesto
		$data[] = array("id"=>"pr.func","descr"=>'El Usuario puede modificar la <b>Estructura Funcional</b>',"sangria"=>"1");
		$data[] = array("id"=>"pr.prog","descr"=>'El Usuario puede modificar la <b>Estructura Programática</b>',"sangria"=>"1");
		$data[] = array("id"=>"pr.actv","descr"=>'El Usuario puede modificar <b>Actividades y Componentes</b>',"sangria"=>"1");
		$data[] = array("id"=>"pr.umed","descr"=>'El Usuario puede crear y editar <b>Unidades de Medida</b>',"sangria"=>"1");
		$data[] = array("id"=>"pr.fuen","descr"=>'El Usuario puede crear y editar las <b>Fuentes de Financiamiento</b>',"sangria"=>"1");
		$data[] = array("id"=>"pr.clas","descr"=>'El Usuario puede crear y editar los <b>Clasificadores</b>',"sangria"=>"1");
		$data[] = array("id"=>"pr.pres.pia","descr"=>'El Usuario puede actualizar el <b>Presupuesto Insitucional de Apertura (PIA)</b>',"sangria"=>"1");
		$data[] = array("id"=>"pr.pres.pim","descr"=>'El Usuario puede registrar modificaciones en el <b>Presupuesto Insitucional Modificado (PIM)</b>',"sangria"=>"1");
		$data[] = array("id"=>"pr.plan.prog","descr"=>'El Usuario puede ingresar la <b>Programación del Plan Operativo</b> de su <b>respectiva Dependencia</b>',"sangria"=>"1");
		$data[] = array("id"=>"pr.plan.prog.org","descr"=>'El Usuario puede modificar la <b>Programación del Plan Operativo</b> de <b>toda la Organización</b>',"sangria"=>"2","padre"=>"pr.plan.prog");
		$data[] = array("id"=>"pr.plan.ejec","descr"=>'El Usuario puede ingresar la <b>Ejecución del Plan Operativo</b> de su <b>respectiva Dependencia</b>',"sangria"=>"1");
		$data[] = array("id"=>"pr.plan.ejec.org","descr"=>'El Usuario puede consultar la <b>Ejecución del Plan Operativo</b> de <b>toda la Organización</b>',"sangria"=>"2","padre"=>"pr.plan.ejec");
		$data[] = array("id"=>"pr.reps","descr"=>"El Usuario puede consultar <b>Reportes</b>","sangria"=>"1");
		
		//Personal
		$data[] = array("id"=>"pe.carg","descr"=>"El Usuario puede crear y editar <b>Cargos</b>","sangria"=>"1");
		$data[] = array("id"=>"pe.carc","descr"=>"El Usuario puede crear y editar <b>Cargos Clasificados</b>","sangria"=>"1");
		$data[] = array("id"=>"pe.grup","descr"=>"El Usuario puede crear y editar <b>Grupos Ocupacionales</b>","sangria"=>"1");
		$data[] = array("id"=>"pe.nivr","descr"=>"El Usuario puede crear y editar <b>Niveles Remunerativos</b>","sangria"=>"1");
		$data[] = array("id"=>"pe.tinc","descr"=>"El Usuario puede crear y editar <b>Tipos de Incidencia</b>","sangria"=>"1");
		$data[] = array("id"=>"pe.pens","descr"=>"El Usuario puede crear y editar <b>Sistemas de Pensiones</b>","sangria"=>"1");
		$data[] = array("id"=>"pe.tcon","descr"=>"El Usuario puede crear y editar <b>Tipos de Contrato</b>","sangria"=>"1");
		$data[] = array("id"=>"pe.conc","descr"=>"El Usuario puede crear y editar <b>Conceptos</b>","sangria"=>"1");
		$data[] = array("id"=>"pe.equi","descr"=>"El Usuario puede crear y editar <b>Equipos</b>","sangria"=>"1");
		$data[] = array("id"=>"pe.perm","descr"=>"El Usuario puede crear y editar <b>Permisos</b>","sangria"=>"1");
		$data[] = array("id"=>"pe.ctas.trab.276","descr"=>"El Usuario puede crear y editar <b>Trabajadores 276</b>","sangria"=>"1");
		$data[] = array("id"=>"pe.ctas.trab.cas","descr"=>"El Usuario puede crear y editar <b>Trabajadores CAS</b>","sangria"=>"1");
		$data[] = array("id"=>"pe.ctas.trab.prac","descr"=>"El Usuario puede crear y editar <b>Practicantes</b>","sangria"=>"1");
		$data[] = array("id"=>"pe.feri","descr"=>"El Usuario puede asignar los días <b>Feriados</b>","sangria"=>"1");
		$data[] = array("id"=>"pe.turn","descr"=>"El Usuario puede crear y editar <b>Turnos</b>","sangria"=>"1");
		$data[] = array("id"=>"pe.pres","descr"=>"El Usuario puede consultar el <b>Presupuesto Analítico</b>","sangria"=>"1");
		$data[] = array("id"=>"pe.pres.edit","descr"=>"El Usuario puede <b>crear</b> un nuevo PAP","sangria"=>"2","padre"=>"pe.pres");
		$data[] = array("id"=>"pe.pres.hab","descr"=>"El Usuario puede <b>aprobar y habilitar</b> el PAP","sangria"=>"3","padre"=>"pe.pres.edit");
		$data[] = array("id"=>"pe.cuas","descr"=>"El Usuario puede consultar el <b>Cuadro de Asignación</b>","sangria"=>"1");
		$data[] = array("id"=>"pe.cuas.edit","descr"=>"El Usuario puede <b>crear</b> un nuevo CAP","sangria"=>"2","padre"=>"pe.cuas");
		$data[] = array("id"=>"pe.cuas.apr","descr"=>"El Usuario puede <b>aprobar</b> el CAP","sangria"=>"3","padre"=>"pe.cuas.edit");
		$data[] = array("id"=>"pe.asis","descr"=>"El Usuario puede gestionar el <b>Control de Asistencia</b>","sangria"=>"1");
		$data[] = array("id"=>"pe.perm","descr"=>"El Usuario puede gestionar <b>Permisos</b>","sangria"=>"1");
		$data[] = array("id"=>"pe.plan","descr"=>"El Usuario puede gestionar <b>Planillas de Pago</b>","sangria"=>"1");
		$data[] = array("id"=>"pe.plan.276","descr"=>"El Usuario puede gestionar las <b>Planillas de Trabajadores 276</b>","sangria"=>"2","padre"=>"pe.plan");
		$data[] = array("id"=>"pe.plan.cas","descr"=>"El Usuario puede gestionar las <b>Planillas de Trabajadores CAS</b>","sangria"=>"2","padre"=>"pe.plan");
		$data[] = array("id"=>"pe.plan.25a","descr"=>"El Usuario puede gestionar <b>Compensaciones 25 años</b>","sangria"=>"2","padre"=>"pe.plan");
		$data[] = array("id"=>"pe.plan.30a","descr"=>"El Usuario puede gestionar <b>Compensaciones 30 años</b>","sangria"=>"2","padre"=>"pe.plan");
		$data[] = array("id"=>"pe.plan.vac","descr"=>"El Usuario puede gestionar <b>Compensaciones Vacacional</b>","sangria"=>"2","padre"=>"pe.plan");
		$data[] = array("id"=>"pe.prop","descr"=>"El Usuario puede registrar <b>Propinas de Practicantes</b>","sangria"=>"1");
		$data[] = array("id"=>"pe.reps","descr"=>"El Usuario puede consultar <b>Reportes</b>","sangria"=>"1");
		
		//Asesoria Legal
		$data[] = array("id"=>"al.dilg","descr"=>"El Usuario puede gestionar <b>Diligencias</b>","sangria"=>"1");
		$data[] = array("id"=>"al.expd","descr"=>"El Usuario puede gestionar <b>Expedientes Legales</b>","sangria"=>"1");
		$data[] = array("id"=>"al.cont","descr"=>"El Usuario puede gestionar <b>Contingencias</b>","sangria"=>"1");
		$data[] = array("id"=>"al.conv","descr"=>"El Usuario puede gestionar <b>Convenios</b>","sangria"=>"1");
		$data[] = array("id"=>"al.reps","descr"=>"El Usuario puede consultar <b>Reportes</b>","sangria"=>"1");
		
		//Contabilidad
		$data[] = array("id"=>"ct.plan","descr"=>"El Usuario puede editar el <b>Plan Contable</b>","sangria"=>"1");
		$data[] = array("id"=>"ct.tnot","descr"=>"El Usuario puede editar <b>Tipos de Nota</b>","sangria"=>"1");
		$data[] = array("id"=>"ct.plac","descr"=>"El Usuario puede editar <b>Tipos de Plan de Cuentas</b>","sangria"=>"1");
		$data[] = array("id"=>"ct.codl","descr"=>"El Usuario puede editar <b>Códigos de Libros</b>","sangria"=>"1");
		$data[] = array("id"=>"ct.comp","descr"=>"El Usuario puede editar <b>Comprobantes de Pago</b>","sangria"=>"1");
		$data[] = array("id"=>"ct.notc","descr"=>"El Usuario puede gestionar <b>Notas de Contabilidad</b>","sangria"=>"1");
		$data[] = array("id"=>"ct.notf","descr"=>"El Usuario puede gestionar <b>Notas a los Estados Financieros</b>","sangria"=>"1");
		$data[] = array("id"=>"ct.regc","descr"=>"El Usuario puede gestionar el <b>Registro de Compras</b>","sangria"=>"1");
		$data[] = array("id"=>"ct.regv","descr"=>"El Usuario puede gestionar el <b>Registro de Ventas</b>","sangria"=>"1");
		$data[] = array("id"=>"ct.conp","descr"=>"El Usuario puede gestionar <b>Control Patrimonial</b>","sangria"=>"1");
		$data[] = array("id"=>"ct.conb","descr"=>"El Usuario puede gestionar <b>Conciliaciones Bancarias</b>","sangria"=>"1");
		$data[] = array("id"=>"ct.auxe","descr"=>"El Usuario puede gestionar <b>Auxiliares Standard</b>","sangria"=>"1");
		$data[] = array("id"=>"ct.ejpr","descr"=>"El Usuario puede gestionar la <b>Ejecución Presupuestaria</b>","sangria"=>"1");
		$data[] = array("id"=>"ct.mayr","descr"=>"El Usuario puede consultar la <b>Mayorización de Cuentas</b>","sangria"=>"1");
		$data[] = array("id"=>"ct.movc","descr"=>"El Usuario puede consultar los <b>Movimientos de Cuentas</b>","sangria"=>"1");
		$data[] = array("id"=>"ct.libd","descr"=>"El Usuario puede consultar el <b>Libro Diario</b>","sangria"=>"1");
		$data[] = array("id"=>"ct.libd.nota","descr"=>"El Usuario puede <b>asignar Notas</b> al libro","sangria"=>"2","padre"=>"ct.libd");
		$data[] = array("id"=>"ct.libd.fin","descr"=>"El Usuario puede <b>cerrar el periodo</b>","sangria"=>"3","padre"=>"ct.libd.nota");
		$data[] = array("id"=>"ct.libm","descr"=>"El Usuario puede consultar el <b>Libro Mayor</b>","sangria"=>"1");
		$data[] = array("id"=>"ct.libm.nota","descr"=>"El Usuario puede <b>asignar Notas</b> al libro","sangria"=>"2","padre"=>"ct.libm");
		$data[] = array("id"=>"ct.libm.fin","descr"=>"El Usuario puede <b>cerrar el periodo</b>","sangria"=>"3","padre"=>"ct.libm.nota");
		$data[] = array("id"=>"ct.reps","descr"=>"El Usuario puede consultar <b>Reportes</b>","sangria"=>"1");
		
		//Caja
		$data[] = array("id"=>"cj.caja","descr"=>"El Usuario puede crear y editar <b>Cajas</b>","sangria"=>"1");
		$data[] = array("id"=>"cj.conc","descr"=>"El Usuario puede crear y editar <b>Conceptos</b>","sangria"=>"1");
		$data[] = array("id"=>"cj.taln","descr"=>"El Usuario puede crear y editar <b>Talonarios</b>","sangria"=>"1");
		$data[] = array("id"=>"cj.ctas","descr"=>"El Usuario puede consultar <b>Clientes y Cajeros</b>","sangria"=>"1");
		$data[] = array("id"=>"cj.ctas.caj","descr"=>"El Usuario puede crear y editar <b>Cajeros</b>","sangria"=>"2","padre"=>"cj.ctas");
		$data[] = array("id"=>"cj.ccob","descr"=>"El Usuario puede gestionar <b>Cuentas por Cobrar</b>","sangria"=>"1");
		$data[] = array("id"=>"cj.ccob.dep","descr"=>"El Usuario puede <b>crear Cuentas por Cobrar de su dependencia</b>","sangria"=>"2","padre"=>"cj.ccob");
		$data[] = array("id"=>"cj.ccob.org","descr"=>"El Usuario puede <b>consultar las Cuentas por Cobrar de todas las dependencias</b>","sangria"=>"2","padre"=>"cj.ccob");
		$data[] = array("id"=>"cj.ccob.comp","descr"=>"El Usuario puede <b>emitir Comprobantes</b>","sangria"=>"3","padre"=>"cj.ccob.org");
		$data[] = array("id"=>"cj.comp","descr"=>"El usuario puede consultar <b>Comprobantes</b>","sangria"=>"1");
		$data[] = array("id"=>"cj.ecom","descr"=>"El usuario puede crear y consultar <b>comprobantes electr&oacute;nicos</b>","sangria"=>"1");
		$data[] = array("id"=>"cj.comp.reci","descr"=>"El usuario puede generar el <b>Recibo de Ingresos</b>","sangria"=>"2","padre"=>"cj.comp");
		$data[] = array("id"=>"cj.reps","descr"=>"El Usuario puede consultar <b>Reportes</b>","sangria"=>"1");
		
		//Tesoreria
		$data[] = array("id"=>"ts.cajc","descr"=>"El Usuario puede crear y editar <b>Cajas Chicas</b>","sangria"=>"1");
		$data[] = array("id"=>"ts.conc","descr"=>"El Usuario puede crear y editar <b>Conceptos</b>","sangria"=>"1");
		$data[] = array("id"=>"ts.tmed","descr"=>"El Usuario puede crear y editar <b>Tipos de Medio de Pago</b>","sangria"=>"1");
		$data[] = array("id"=>"ts.banc","descr"=>"El Usuario puede crear y editar <b>Cuentas Bancarias</b>","sangria"=>"1");
		$data[] = array("id"=>"ts.ctap","descr"=>"El Usuario puede gestionar <b>Cuentas por Pagar</b>","sangria"=>"1");
		$data[] = array("id"=>"ts.comp","descr"=>"El Usuario puede gestionar <b>Comprobantes de Pago</b>","sangria"=>"1");
		$data[] = array("id"=>"ts.rein","descr"=>"El Usuario puede gestionar <b>Recibos de Ingresos</b>","sangria"=>"1");
		$data[] = array("id"=>"ts.movc","descr"=>"El Usuario puede gestionar <b>Movimientos de Caja Chica</b>","sangria"=>"1");
		$data[] = array("id"=>"ts.movc.dep","descr"=>"El Usuario puede gestionar Movimientos de Caja Chica <b>de su Dependencia</b>","sangria"=>"2","padre"=>"ts.movc");
		$data[] = array("id"=>"ts.movc.org","descr"=>"El Usuario puede gestionar Movimientos de Caja Chica <b>de toda la Organización</b>","sangria"=>"2","padre"=>"ts.movc");
		$data[] = array("id"=>"ts.polz","descr"=>"El Usuario puede gestionar <b>Pólizas Contables</b>","sangria"=>"1");
		$data[] = array("id"=>"ts.polz.dep","descr"=>"El Usuario puede gestionar Pólizas Contables de <b>su Dependencia</b>","sangria"=>"2","padre"=>"ts.polz");
		$data[] = array("id"=>"ts.polz.org","descr"=>"El Usuario puede gestionar Pólizas Contables <b>de toda la Organización</b>","sangria"=>"2","padre"=>"ts.polz");
		$data[] = array("id"=>"ts.reci","descr"=>"El Usuario puede gestionar <b>Recibos de Ingresos</b>","sangria"=>"1");
		$data[] = array("id"=>"ts.reci.dep","descr"=>"El Usuario puede gestionar Recibos de Ingresos <b>de su Dependencia</b>","sangria"=>"2","padre"=>"ts.reci");
		$data[] = array("id"=>"ts.reci.org","descr"=>"El Usuario puede gestionar Recibos de Ingresos <b>de toda la Organización</b>","sangria"=>"2","padre"=>"ts.reci");
		$data[] = array("id"=>"ts.sald","descr"=>"El Usuario puede consultar <b>Saldos</b>","sangria"=>"1");
		$data[] = array("id"=>"ts.sald.ini","descr"=>"El Usuario puede <b>generar Saldos Iniciales</b>","sangria"=>"2","padre"=>"ts.sald");
		$data[] = array("id"=>"ts.movs","descr"=>"El Usuario puede consultar <b>Movimientos</b>","sangria"=>"1");
		$data[] = array("id"=>"ts.cheq","descr"=>"El Usuario puede consultar y editar <b>Cheques</b>","sangria"=>"1");
		$data[] = array("id"=>"ts.rede","descr"=>"El Usuario puede consultar y editar <b>Recibos Definitvos</b>","sangria"=>"1");
		//RECIBOS
		$data[] = array("id"=>"ts.reci","descr"=>"El Usuario puede generar <b>Recibos</b> definitivos y provisionales;","sangria"=>"1");
		$data[] = array("id"=>"ts.aprob","descr"=>"El Usuario puede <b>Aprobar</b> recibos definitivos y provisionales","sangria"=>"1");
		
		//USA
		$data[] = array("id"=>"us.coci","descr"=>"El Usuario puede crear y editar <b>Cocinas</b>","sangria"=>"1");
		$data[] = array("id"=>"us.unid","descr"=>"El Usuario puede crear y editar <b>Unidades</b>","sangria"=>"1");
		$data[] = array("id"=>"us.ingr","descr"=>"El Usuario puede crear y editar <b>Ingredientes</b>","sangria"=>"1");
		$data[] = array("id"=>"us.rece","descr"=>"El Usuario puede crear y editar <b>Recetario</b>","sangria"=>"1");
		$data[] = array("id"=>"us.pedi","descr"=>"El Usuario puede realizar <b>Pedido de Raciones</b>","sangria"=>"1");
		$data[] = array("id"=>"us.rece","descr"=>"El Usuario puede totalizar la <b>Recepci&oacute;n de Pedidos</b>","sangria"=>"1");
		$data[] = array("id"=>"us.prog","descr"=>"El Usuario puede gestionar la <b>Programaci&oacute;n Semanal</b>","sangria"=>"1");
		$data[] = array("id"=>"us.cons","descr"=>"El Usuario puede visualizar el <b>Consumo de Insumos</b>","sangria"=>"1");
		$data[] = array("id"=>"us.repo","descr"=>"El Usuario puede generar <b>Reportes</b>","sangria"=>"1");
		
		//ARCHIVO DIGITAL
		$data[] = array("id"=>"dd.conf","descr"=>"El Usuario puede editar la <b>Configuraci&oacute;n</b> de Archivo Digital","sangria"=>"1");


		//HOSPITALIZACION
		$data[] = array("id"=>"ho.conf","descr"=>"El Usuario puede editar la <b>Configuraci&oacute;n</b> de Hospitalizaci&oacute;n","sangria"=>"1");
		$data[] = array("id"=>"ho.tari","descr"=>"El Usuario puede crear y editar <b>Tarifa de Hospitalizaciones</b>","sangria"=>"1");
		$data[] = array("id"=>"ho.cont","descr"=>"El Usuario puede crear y editar el <b>Control de Medicinas</b>","sangria"=>"1");
		$data[] = array("id"=>"ho.pend","descr"=>"El Usuario puede crear y editar <b>Pendientes de Cobro</b>","sangria"=>"1");
		$data[] = array("id"=>"ho.alta","descr"=>"El Usuario puede <b>Dar de Alta</b> a los pacientes","sangria"=>"1");
		$data[] = array("id"=>"ho.hosp","descr"=>"El Usuario puede crear y editar <b>Hospitalizaciones</b>","sangria"=>"1");
		$data[] = array("id"=>"ho.reci","descr"=>"El Usuario puede crear y editar <b>Recibos de Caja</b>","sangria"=>"1");
		$data[] = array("id"=>"ho.rein","descr"=>"El Usuario puede visualizar <b>Recibos de Ingresos</b>","sangria"=>"1");
		$data[] = array("id"=>"ho.repo","descr"=>"El Usuario puede generar <b>Reportes</b>","sangria"=>"1");
		//CHILPINILLA
		$data[] = array("id"=>"ch.conf","descr"=>"El Usuario puede editar la <b>Configuraci&oacute;n</b> de Hospitalizaci&oacute;n","sangria"=>"1");
		$data[] = array("id"=>"ch.tari","descr"=>"El Usuario puede crear y editar <b>Tarifa de Hospitalizaciones</b>","sangria"=>"1");
		$data[] = array("id"=>"ch.cont","descr"=>"El Usuario puede crear y editar el <b>Control de Medicinas</b>","sangria"=>"1");
		$data[] = array("id"=>"ch.pend","descr"=>"El Usuario puede crear y editar <b>Pendientes de Cobro</b>","sangria"=>"1");
		$data[] = array("id"=>"ch.alta","descr"=>"El Usuario puede <b>Dar de Alta</b> a los pacientes","sangria"=>"1");
		$data[] = array("id"=>"ch.hosp","descr"=>"El Usuario puede crear y editar <b>Hospitalizaciones</b>","sangria"=>"1");
		$data[] = array("id"=>"ch.reci","descr"=>"El Usuario puede crear y editar <b>Recibos de Caja</b>","sangria"=>"1");
		$data[] = array("id"=>"ch.rein","descr"=>"El Usuario puede visualizar <b>Recibos de Ingresos</b>","sangria"=>"1");
		$data[] = array("id"=>"ch.repo","descr"=>"El Usuario puede generar <b>Reportes</b>","sangria"=>"1");
		/*------------------------------------------------------------------------------------------------------------------------------------------*/
		
		//INFORMATICA
		$data[] = array("id"=>"ti.comp","descr"=>"El Usuario puede crear y editar <b>Computadoras</b>","sangria"=>"1");
		$data[] = array("id"=>"ti.back","descr"=>"El Usuario puede crear <b>Copias de Seguridad</b>","sangria"=>"1");
		$data[] = array("id"=>"ti.erro","descr"=>"El Usuario puede reportar solucion a <b>Errores del Sistema</b>","sangria"=>"1");
		$data[] = array("id"=>"ti.repo","descr"=>"El Usuario puede generar <b>Reportes</b>","sangria"=>"1");
		$data[] = array("id"=>"ti.edit","descr"=>"El Usuario puede editar <b>Editor de C&oacute;digo</b>","sangria"=>"1");

		//FARMACIA
		$data[] = array("id"=>"fa.conf","descr"=>"El Usuario puede editar la <b>Configuraci&oacute;n</b> de Farmacia","sangria"=>"1");
		$data[] = array("id"=>"fa.prod","descr"=>"El Usuario puede crear y editar <b>Productos</b>","sangria"=>"1");
		$data[] = array("id"=>"fa.guia","descr"=>"El Usuario puede visulizar <b>Guias de Remision</b>","sangria"=>"1");
		$data[] = array("id"=>"fa.lote","descr"=>"El Usuario puede visualizar <b>Lotes</b>","sangria"=>"1");
		$data[] = array("id"=>"fa.vent","descr"=>"El Usuario puede realizar <b>Ventas</b>","sangria"=>"1");
		$data[] = array("id"=>"fa.comp","descr"=>"El Usuario puede visualizat y editar <b>Comprobantes de Pago</b>","sangria"=>"1");
		$data[] = array("id"=>"fa.repo","descr"=>"El Usuario puede generar <b>Reportes</b>","sangria"=>"1");

		//AGUA CHAPI
		$data[] = array("id"=>"ag.conf","descr"=>"El Usuario puede editar la <b>Configuraci&oacute;n</b> de Agua Chapi","sangria"=>"1");
		$data[] = array("id"=>"ag.prod","descr"=>"El Usuario puede crear y editar <b>Productos</b>","sangria"=>"1");
		$data[] = array("id"=>"ag.guia","descr"=>"El Usuario puede visulizar <b>Guias de Remision</b>","sangria"=>"1");
		$data[] = array("id"=>"ag.lote","descr"=>"El Usuario puede visualizar <b>Lotes</b>","sangria"=>"1");
		$data[] = array("id"=>"ag.vent","descr"=>"El Usuario puede realizar <b>Ventas</b>","sangria"=>"1");
		$data[] = array("id"=>"ag.comp","descr"=>"El Usuario puede visualizat y editar <b>Comprobantes de Pago</b>","sangria"=>"1");
		$data[] = array("id"=>"ag.repo","descr"=>"El Usuario puede generar <b>Reportes</b>","sangria"=>"1");

		//RECURSOS ECONOMICOS
		$data[] = array("id"=>"re.repo","descr"=>"El Usuario puede generar <b>Reportes</b> de Recursos Econ&oacute;","sangria"=>"1");
		
		//PROYECTOS
		$data[] = array("id"=>"ge.proy","descr"=>"El Usuario puede crear y editar <b>Proyectos</b>","sangria"=>"1");
		$f->response->json( array( "items" => $data) );
	}
}