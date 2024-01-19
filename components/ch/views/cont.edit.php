<div class="col-lg-4">
	<?php global $f; ?>
	<form class="form-horizontal" role="form" onsubmit="return false;">
		<div class="form-group">
			<label class="col-sm-4 control-label">C&oacute;digo Historia Cl&iacute;nica</label>
			<div class="input-group col-sm-8">
				<input type="number" class="form-control" name="cod_hist" />
				<span class="input-group-btn">
					<button name="btnHist" type="button" class="btn btn-info"><i class="fa fa-user"></i></button>
				</span>
			</div>
		</div>
	</form>
	<form class="form-horizontal" role="form" onsubmit="return false;">
		<div class="form-group">
			<label class="col-sm-4 control-label">Modulo</label>
				<div class="col-sm-8">
					<select class="form-control" name="modulo" type = "text" style="width:300px" required>
						<option value="CH">Chilpinilla</option>
					</select>
				</div>
		</div>
	</form>
	<?php $f->response->view('mg/enti.mini'); ?>
</div>
<div class="col-lg-8">
	<div name="gridMed"></div>
</div>