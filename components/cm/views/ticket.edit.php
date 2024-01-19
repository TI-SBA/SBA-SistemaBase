<?php global $f; ?>
<form class="form-horizontal" role="form">
<div class="form-group date" data-provide="datepicker" >
		<label class="col-sm-4 control-label">Fecha de Evento: </label>
		<div class="col-sm-7.5 input-group">
			<input type="text" class="form-control"  name="fecve" disabled="disabled" style="width:300px">
		</div>
	</div>    
    <div name="cliente"><?php $f->response->view('mg/enti.mini'); ?></div>
    <div class="form-group">
		<label class="col-sm-4 control-label">Fecha de Emision</label>
		<div class="col-sm-8">
			<input type="text" class="form-control" name="fecven" style="width:300px">
		</div>
	</div>

    <div class="form-group row">
        <label class="col-sm-4 control-label">Servicio</label>
        <div class="col-md-6">
            <div class="input-group">
                <span class="form-control" name="servicio" style="width:600px"></span>
                <span class="input-group-append">
                    <button name="btnServ" type="button" class="btn btn-info"><i class="fa fa-search"></i></button>    
                </span>
            </div>
        </div>
    </div>

    <div class="form-group">
		<label class="col-sm-4 control-label">Monto</label>
		<div class="col-sm-8">
			<input type="number" class="form-control" name="monto" style="width:100px">
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-4 control-label">Cantidad</label>
		<div class="col-sm-8">
			<input type="number" class="form-control" name="cant" style="width:100px">
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-4 control-label">Total</label>
		<div class="col-sm-8">
			<input type="number" class="form-control" name="total" style="width:100px" disabled="disabled" >
		</div>
	</div>
    <div class="form-group">
		<label class="col-sm-4 control-label">Serie</label>
		<div class="col-sm-8">
        <input type="number" class="form-control" name="serie" style="width:100px" disabled="disabled">
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-4 control-label">Numero</label>
		<div class="col-sm-8">
			<input type="text" class="form-control" name="num" style="width:100px">
		</div>
	</div>
	<div name="gridTicket">
	</div>
    <!--<div class="form-group">
    <label class="col-sm-4 control-label">Listado de Tickets</label>
    <div class="col-sm-8">
        <div class="input-group">
            <input type="text" class="form-control" placeholder="Desde..." name="ini" style="width:100px">
            <input type="text" class="form-control" placeholder="Hasta..." name="fin" style="width:100px">
        </div>
    </div>
    </div>-->
    <div class="form-horizontal" role="form" >
		<div class="form-group">
			<label class="col-sm-4 control-label">Observaciones:</label>
				<div class="col-sm-8">
					<textarea cols="30" rows="3" class="form-control" name="observ"></textarea>
				</div>
		</div>
	</div>
</form>

