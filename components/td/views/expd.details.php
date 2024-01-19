<div class="ui-layout-west" >
	<ul class="list">
		<li class="ui-state-highlight"><a href="section1">Datos Generales</a></li>
		<li><a href="section2">Resoluciones</a></li>
		<li><a href="section3">Documentos</a></li>
		<li><a href="section4">Traslados</a></li>
	</ul>
</div>
<div class="ui-layout-center" style="overflow: auto;">
	<div name="section1" style="height:450px;overflow-y: auto;">
		<fieldset>
			<legend>Datos Generales</legend>
			<table style="width:100%" cellpadding="8">
				<tr>
					<td style="width:120px"><label>N&uacute;mero</label></td>
					<td><span name="num"></span></td>
				</tr>
				<tr>
					<td><label>Registrado</label></td>
					<td><span name="fecreg"></span></td>
				</tr>
				<tr>
					<td><label>Gestor</label></td>
					<td><span name="gestor"></span></td>
				</tr>
				<tr>
					<td><label>Asunto</label></td>
					<td><span name="concepto"></span></td>
				</tr>
				<tr>
					<td><label>Instancia / Estado</label></td>
					<td><span name="ins"></span>&nbsp;&nbsp;&nbsp;<span name="estado"></span></td>
				</tr>
				<tr>
					<td><label>Vencimiento</label></td>
					<td><span name="fecven"></span></td>
				</tr>
				<tr>
					<td><label>Ubicaci&oacute;n Actual</label></td>
					<td><span name="ubicacion"></span></td>
				</tr>
				<tr>
					<td><label>Resoluci&oacute;n</label></td>
					<td><span name="fecharpta"></span></td>
				</tr>
				<tr>
					<td><label>Observaciones del Expediente</label></td>
					<td><span name="observ_expd"></span></td>
				</tr>
				<tr style="display:none;">
					<td><label>Observaci&oacute;n de conclusi&oacute; Expediente</label></td>
					<td><span name="observ_conc"></span></td>
				</tr>
			</table>
		</fieldset>
	</div>
	<div name="section2" style="width:593px; height:450px; overflow-y: auto;">
		<fieldset style="padding: 4px;">
			<legend>Resoluciones</legend>
			<div name="inicio">
				<hr>
				<table style="width:100%">
					<tr>
						<td colspan="2" style="width:"><label><b>Inicio</b></label></td>
						<td style="width:80px"><label>Iniciado</label></td>
						<td style="width:"><span name="fecini"></span></td>
					</tr>
					<tr>
						<td style="width:80px"><label>Resoluci&oacute;n</label></td>
						<td><span name="resolucion"></span></td>
						<td><label>Concluido</label></td>
						<td><span name="fecfin"></span></td>
					</tr>
				</table>
			</div>
			<div name="reconsidera">
				<hr>
				<table style="width:100%">
					<tr>
						<td colspan="2" style="width:"><label><b>Reconsideraci&oacute;n</b></label></td>
						<td style="width:80px"><label>Iniciado</label></td>
						<td style="width:"><span name="fecini"></span></td>
					</tr>
					<tr>
						<td style="width:80px"><label>Resoluci&oacute;n</label></td>
						<td><span name="resolucion"></span></td>
						<td><label>Concluido</label></td>
						<td><span name="fecfin"></span></td>
					</tr>
				</table>
			</div>
			<div name="apela">
				<hr>
				<table style="width:100%">
					<tr>
						<td colspan="2" style="width:"><label><b>Apelaci&oacute;n</b></label></td>
						<td style="width:80px"><label>Iniciado</label></td>
						<td style="width:"><span name="fecini"></span></td>
					</tr>
					<tr>
						<td style="width:80px"><label>Resoluci&oacute;n</label></td>
						<td><span name="resolucion"></span></td>
						<td><label>Concluido</label></td>
						<td><span name="fecfin"></span></td>
					</tr>
				</table>
			</div>
			<hr>
		</fieldset>
	</div>
	<div name="section3" style="width:593px; height:450px; overflow-y: auto;">
		<fieldset style="padding: 4px;">
			<legend>Documentos</legend>
			<div name="docRef" style="display: none;">
				<hr>
				<table style="width:100%">
					<tr>
						<td colspan="2"><b><span name="tdoc"></span></b></td>
						<td><label>Fecha</label></td>
						<td><span name="fecha"></span></td>
					</tr>
					<tr>
						<td><label>Origen</label></td>
						<td><span name="origen"></span></td>
						<td><label>N&deg; de Folios</label></td>
						<td><span name="folios"></span></td>
					</tr>
					<tr>
						<td><label>Asunto</label></td>
						<td colspan="3"><span name="asunto"></span></td>
					</tr>
				</table>
			</div>
		</fieldset>
	</div>
	<div name="section4" style="width:593px; height:450px; overflow-y: auto;">
		<fieldset style="padding: 4px;">
			<legend>Traslados</legend>
			<div name="trasRef" style="display: none;">
				<hr>
				<table style="width:100%">
					<tr>
						<td colspan="2"><b><span name="origen"></span></b></td>
						<td><label>Recibido</label></td>
						<td><span name="recibido"></span></td>
					</tr>
					<tr>
						<td><label>Recibido por</label></td>
						<td><span name=entidad></span></td>
						<td><label>Enviado</label></td>
						<td><span name="enviado"></span></td>
					</tr>
					<tr>
						<td><label>Enviado a</label></td>
						<td><span name="destino"></span></td>
						<td><b><label name="estadoTras"></label></b></td>
						<td><span name="fechaTras"></span></td>
					</tr>
					<tr>
						<td><label>Copias</label></td>
						<td colspan="3"><span name="copias"></span></td>
					</tr>
				</table>
				<hr>
			</div>
		</fieldset>
	</div>
</div>