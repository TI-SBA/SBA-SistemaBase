<?php
$text = array(
	'cabecera'=>'Datos de la Entidad',
	'btn_select'=>'Elegir otra entidad'
);
if(!isset($data)) $data = array();
$text = array_merge($text,$data);
?>
<div name="mini_enti" class="panel panel-info">
	<div class="panel-heading">
		<h3 class="panel-title"><?=$text['cabecera']?></h3>
	</div>
	<div class="panel-body">
		<div class="col-lg-3" align="center"> <img alt="User Pic" src="images/usuario.jpg" class="img-circle img-responsive" style="max-width:80px;"> </div>
		<div class=" col-lg-9"> 
			<table class="table table-user-information">
				<tbody>
					<tr name="nomb">
						<td>Nombre:</td>
						<td></td>
					</tr>
					<tr name="dni">
						<td>DNI:</td>
						<td></td>
					</tr>
					<tr name="ruc">
						<td>RUC:</td>
						<td></td>
					</tr>
					<tr name="email">
						<td>Email</td>
						<td></td>
					</tr>
					<tr name="url">
						<td>Sitios Web</td>
						<td></td>
					</tr>
					<tr name="telef">
						<td>Tel&eacute;fono</td>
						<td></td>
					</tr>
					<tr name="direc">
						<td>Direcci&oacute;n</td>
						<td></td>
					</tr>
				</tbody>
			</table>
			<a href="#" name="btnAct" class="btn btn-primary"><i class="fa fa-pencil"></i> Actualizar datos</a>
			<a href="#" name="btnSel" class="btn btn-info"><i class="fa fa-search"></i> <?=$text['btn_select']?></a>
			<a href="#" name="btnEli" class="btn btn-danger" style="display:none;"><i class="fa fa-trash"></i> Borrar entidad</a>
		</div>
	</div>
</div>