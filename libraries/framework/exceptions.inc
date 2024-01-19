<?php /* Framework Exceptions

# Application
App001	0x0101	La clase solicitada '%s' no ha sido encontrada.
App002	0x0102	El archivo de configuración especificado no existe: %s.
App012	0x010C	La estructura del archivo de configuración no es correcta: %s.
App003	0x0103	La clave solicitada no se encuentra en el archivo de configuración: %s/%s.
App004	0x0104	La biblioteca solicitada '%s' no ha sido encontrada.
App005	0x0105	La propiedad especificada no existe en el objeto: %s.
App255	0x01FF	Error desconocido de la Aplicación.

# Loader
Ldr001	0x0201	<!DOCTYPE html><html><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><title>SBPA | Error 404</title><link href="http://200.10.77.59/themes/inspinia/css/bootstrap.css" rel="stylesheet"><link href="http://200.10.77.59/themes/inspinia/css/font-awesome.css" rel="stylesheet"><link href="http://200.10.77.59/themes/inspinia/css/animate.css" rel="stylesheet"><link href="http://200.10.77.59/themes/inspinia/css/style.css" rel="stylesheet"></head><body class="gray-bg row"><div class="middle-box col-sm-12 text-center animated fadeInDown"><h1>404</h1><h3 class="font-bold">P&aacute;gina no encontrada</h3><h3>(%s)</h3><div class="error-desc">Lo sentimos, pero la p&aacute;gina que est&aacute; buscando no ha sido encontrada. Intente verificar la URL por alg&uac;en error, luego presione el bot&oacute;n Refrescar en su navegador.<form class="form-inline m-t" role="form"><!--<div class="form-group"><input type="text" class="form-control" placeholder="Search for page"></div><button type="submit" class="btn btn-primary">Search</button>--><button type="button" class="btn btn-primary" onclick="window.location='http://sistemas.sbparequipa.gob.pe';">Ir al Inicio!</button></form></div></div><!-- Mainly scripts --><script src="http://200.10.77.59/themes/inspinia/js/jquery-2.js"></script><script src="http://200.10.77.59/themes/inspinia/js/bootstrap.min.js"></script></body></html>.

# Request
Req001	0x0301	La propiedad '%s' no se encuentra especificada en la solicitud.

# Response
Res001	0x0400

# Session
Ses001	0x0501	La variable de sesión solicitada no existe: %s.
Ses004	0x0504	La sesión ha expirado.

# Datastore
Dst001	0x0601	No se ha especificado un Driver de Base de Datos.
Dst002	0x0602	No se encuentra el archivo auxiliar del motor de almacenamiento.
Dst003	0x0603	No se establecieron correctamente los parámetros de conexión a la Base de Datos.
Dst004	0x0604	No se pudo establecer conexión con la Base de Datos: %s.
Dst005	0x0605	La última consulta a la Base de Datos devolvió un error: %s.
Dst006	0x0606	No se pudo obtener el número de registros de última consulta: %s.
Dst007	0x0607	No existe el método en el objeto de enlace con la Base de Datos: %s.
Dst200	0x06C8	Error de Base de Datos: Violación de Integridad Referencial.

# Controller
Ctr001	0x0701	No se encuentra la clase correspondiente al controlador: %s.
Ctr002	0x0702	La clase no es de tipo controlador: %s.
Ctr003	0x0703	%s: El método o tarea '%s' no existe.

# Model
Mod001	0x0801	No se encuentra la clase correspondiente al modelo: %s.
Mod002	0x0802	La clase cargada no es un modelo válido: %s.
Mod003	0x0803	%s: El método '%s' no existe.
Mod004	0x0804	Número de parámetros incorrecto.

# DataObject
Dob001	0x0901	No se encuentra la clase correspondiente al objeto de datos: %s.
Dob002	0x0902	La clase cargada no es un objeto de datos válido: %s.
Dob003	0x0903	El archivo cargado no contiene la clase correspondiente al objeto de datos requerido: %s.

# FileSystemObject
Fso001	0x1001

# User Defined
Usr000	0xFF00	%s

*/ ?>