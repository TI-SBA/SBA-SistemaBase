<div class="row">
    <div class="col-lg-12">
        <table class="table table-bordered" name="grid">
            <thead>
                <tr>
                    <th colspan="4">
                        <button title="Inmuebles" type="button" class="btn btn-primary dim btn-large-dim" name="btnAlquiler"><i class="fa fa-home"></i></button>
                        <button title="Playa de estacionamiento" type="button" name="btnPlaya" class="btn btn-success dim btn-large-dim" disabled><i class="fa fa-car"></i></button>
                        <button title="Productos farmacia" type="button" name="btnFarmacia" class="btn btn-danger dim btn-large-dim"><i class="fa fa-medkit"></i></button>
                        <button title="Hospitalizaciones" type="button" name="btnHospitalizacion" class="btn btn-warning dim btn-large-dim" disabled><i class="fa fa-hospital-o" ></i></button>
                        <button title="Productos agua chapi" type="button" name="btnAgua" class="btn btn-info dim btn-large-dim"><i class="fa fa-tint"></i></button>
                        <button title="Cuentas por cobrar" type="button" name="btnCuentaCobrar" class="btn btn-default dim btn-large-dim" disabled><i class="fa fa-book"></i></button>
                        <button title="Cuentas por cobrar" type="button" name="btnServicio" class="btn btn-default dim btn-large-dim"><i class="fa fa-file-text"></i></button>
                    </th>
                </tr>
                <tr>
                    <th colspan="4">
                        <!-- <textarea class="form-control" name="descripcion_item"></textarea> -->
                        <!-- LA SUNAT YA NO PERMITE SALTOS DE LINEA DESDE EL 21/12/2018, comunicado y subido el 27/12/2018 -->
                        <input style="width:100%" type="text" name="descripcion_item" class="input-lg form-control"></input>
                    </th>
                </tr>
                <tr>
                    <th style="width:50px;">Item</th>
                    <th style="width:450px;">Concepto/Producto</th>
                    <th style="width:100px;">Adicional</th>
                    <th style="width:100px;">Monto</th>
                </tr>
            </thead>
            <tbody>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="3">Valor unitario</td>
                    <td><input type="text" name="valor_unitario" class="form-control"></td>
                </tr>
                <tr>
                    <td colspan="3">Cantidad</td>
                    <td><input type="text" name="cantidad" class="form-control"></td>
                </tr>
                <tr>
                    <td colspan="3">Unidad</td>
                    <td><select name="unidad" class="form-control">
                        <option value="NIU">UNIDAD</option>
                        <option value="ZZ">SERVICIO</option>
                    </select></td>
                </tr>
                <tr>
                    <td colspan="3">Tipo IGV</td>
                    <td><select name="tipo_igv" class="form-control">
                        <option value="10">GRAVADO - OPERACION ONEROSA</option>
                        <option value="20">EXONERADO - OPERACION ONEROSA</option>
                        <option value="30">INAFECTO - OPERACION ONEROSA</option>
                    </select></td>
                </tr>
                <tr>
                    <td colspan="3">I.G.V.</td>
                    <td><input type="text" name="igv" class="form-control"></td>
                </tr>
                <tr>
                    <td colspan="3">Precio de venta unitario</td>
                    <td><input type="text" name="precio_venta_unitario" class="form-control"></td>
                </tr>
                <tr>
                    <td colspan="3">Precio de total</td>
                    <td><input type="text" name="precio_total" class="form-control"></td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>