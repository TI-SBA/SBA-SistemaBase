<?php global $f; ?>
<form class="form-horizontal" role="form">
	<div class="form-group">
		<label class="col-sm-4 control-label">A&ntildeo: </label>
			<div class="col-sm-8">
				<select class="form-control" name="ano" type = "text" style="width:200px" >
							<option value="2015">2015</option>
                            <option value="2016">2016</option>
                            <option value="2017" selected>2017</option>
                            <option value="2018">2018</option>
                            <option value="2019">2019</option>
                            <option value="2020">2020</option>
                            <option value="2021">2021</option>
				</select>
			</div>
	</div>
	<div class="form-group">
		<label class="col-sm-4 control-label">mes: </label>
			<div class="col-sm-8">
				<select class="form-control" name="mes" type = "text" style="width:200px" >
							<option value="01">ENERO</option>
                            <option value="02">FEBRERO</option>
                            <option value="03">MARZO</option>
                            <option value="04">ABRIL</option>
                            <option value="05">MAYO</option>
                            <option value="06">JUNIO</option>
                            <option value="07">JULIO</option>
                            <option value="08">AGOSTO</option>
                            <option value="09">SETIEMBRE</option>
                            <option value="10">OCTUBRE</option>
                            <option value="11">NOVIEMBRE</option>
                            <option value="12">DICIEMBRE</option>
				</select>
			</div>
	</div>
	<div class="form-group">
		<ddiv>
			<label class="col-sm-4 control-label">Cuenta Bancaria: </label>
				<div class="col-sm-3">
					<span class="form-control" name="banco"  style="width:200px"></span>
				</div>
				<div class="col-sm-5">
					<span class="input-group-btn">
						<button name="btnBanco" type="button" class="btn btn-info"><i class="fa fa-search"></i></button>
					</span>
				</div>
		</ddiv>
	</div>
	<div class="form-group">
		<label class="col-sm-4 control-label">Saldo Inicial</label>
		<div class="col-sm-8">
			<input type="text" class="form-control" style="width:200px" name="saldo_final" disabled="disabled">
		</div>
	</div>
	<div class="form-group">
			<div name="gridComprobantes"></div>
	</div>
</form>