<?php global $f; ?>
<form class="form-horizontal" role="form">
    <div id="wrapper">
        <div class="gray-bg">
            <div class="wrapper wrapper-content">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="ibox">
                            <div class="ibox-content">
                                <h1>Digitalizacion de Recibos | Operaciones</h1>
                            </div>
                        </div>
                    </div>
                    <ddiv>
						<label class="col-sm-1 control-label">Nombre:</label>
							<div class="col-md-3">
								<span class="form-control" name="nomb"></span>
							</div>
					</ddiv>
                    <ddiv>
						<label class="col-sm-1 control-label">Sector:</label>
							<div class="col-md-3">
								<span class="form-control" name="sector"></span>
							</div>
					</ddiv>
                    <ddiv>
						<label class="col-sm-1 control-label">Capacidad</label>
							<div class="col-md-3">
								<span class="form-control" name="capa"></span>
							</div>
					</ddiv>
                    <ddiv>
						<label class="col-sm-1 control-label">Estado</label>
							<div class="col-md-3">
								<span class="form-control" name="estado"></span>
							</div>
					</ddiv>
                    <ddiv>
						<label class="col-sm-1 control-label">Zona</label>
							<div class="col-md-3">
								<span class="form-control" name="zona"></span>
							</div>
					</ddiv>
                </div>
            </div>
        </div>
    </div>
        
    <h2>Historial de Operaciones</h2>
    <div name="gridHis"></div>
</form>