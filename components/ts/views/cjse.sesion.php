<?php global $f; ?>
<form class="form-horizontal" role="form">
	
	<div class="form-group"  data-provide="datepicker">
		<label class="col-sm-4 control-label">Fecha de Cierre: </label>
		<div class="col-sm-8">
			<input type="text" class="form-control" name="feccie" style="width:200px">
		</div>
	</div>
	
	<label class="col-sm-4 control-label">Estado: </label>
			<div class="col-sm-8">
				<select class="form-control" name="estado" type = "text" disabled="disabled" style="width:200px" >
							<option value="A">Aperturado</option>
							<option value="C" selected>Cerrado</option>
							
				</select>
			</div>
	</div>
</form>