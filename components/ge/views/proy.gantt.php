<link href="themes/kunanui/css/jsgantt.css" rel="stylesheet" type="text/css"/>
<script src="scripts/plugins/jsgantt.js" type="text/javascript"></script>
<form class="form-horizontal" role="form" name="general">
	<div class="form-group">
		<label class="col-sm-4 control-label">Nombre</label>
		<div class="col-sm-8 input-group">
			<span class="input-group-addon"><i class="fa fa-font fa-fw"></i></span>
			<input type="text" class="form-control" name="nomb">
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-4 control-label">Descripci&oacute;n</label>
		<div class="col-sm-8">
			<textarea cols="30" rows="2" class="form-control" name="descr"></textarea>
		</div>
	</div>
</form>
<fieldset>
	<legend>Diagrama de Gantt</legend>
	<div style="position:relative" class="gantt" id="GanttChartDIV"></div>
</fieldset>