<div name="espacio">
<table cellpadding="10">
	<tr>
		<td width="112">Tipo de Espacio:</td>
		<td>
			<div name="rbtn_espacios" class="buttonRow" style="text-align:center;">
				<input type="radio" value="1" name="rbtn_espa" id="rbtn_mauso" checked="checked"><label for="rbtn_mauso">Mausoleo</label>
				<input type="radio" value="2" name="rbtn_espa" id="rbtn_tumba"><label for="rbtn_tumba">Tumba</label>
			</div>
		</td>
	</tr>
	<tr>
		<td width="112"><label>Capacidad:</label></td>
		<td><input type="text" name="capa" size="37"></td>
	</tr>
</table>
<div name="mausoleo">
	<table cellpadding="10">
				<tr>
					<td width="112"><label>Lote:</label></td>
					<td colspan="4"><input type="text" name="lote" size="37"></td>
				</tr>
				<tr>
					<td width="112"><label>Denominaci&oacute;n:</label></td>
					<td colspan="4"><input type="text" name="deno" size="37"></td>
				</tr>
				<tr>
					<td width="112">Tipo:</td>
					<td colspan="4">
						<div name="rbtn_zona" class="buttonRow" style="text-align:left;">
							<input type="radio" value="N" name="rbtn_zona" id="rbtn_zona_n" checked="checked"><label for="rbtn_zona_n">Normal</label>
							<input type="radio" value="P" name="rbtn_zona" id="rbtn_zona_p"><label for="rbtn_zona_p">Preferencial</label>
						</div>
					</td>
				</tr>
				<tr>
					<td width="112">Tipo:</td>
					<td colspan="4">
						<div name="rbtn_tipos_mau" class="buttonRow" style="text-align:left;">
							<input type="radio" value="B" name="rbtn_tip_mau" id="rbtn_bove" checked="checked"><label for="rbtn_bove">B&oacute;veda</label>
							<input type="radio" value="C" name="rbtn_tip_mau" id="rbtn_capi"><label for="rbtn_capi">Capilla</label>
							<input type="radio" value="R" name="rbtn_tip_mau" id="rbtn_crip"><label for="rbtn_crip">Cripta</label>
						</div>
					</td>
				</tr>
				<tr>
					<td rowspan="2"  width="112"><label>Medidas(metros)</label></td>
					<td><label>Largo</label></td>
					<td><label>Ancho</label></td>
					<td><label>Altura 1</label></td>
					<td><label>Altura 2</label></td>
				</tr>
				<tr>
					<td><input type="text" name="largo" size="7"></td>
					<td><input type="text" name="ancho" size="7"></td>
					<td><input type="text" name="alt1" size="7"></td>
					<td><input type="text" name="alt2" size="7"></td>
				</tr>
				<tr>
					<td width="112"><label>Referencia:</label></td>
					<td colspan="4"><input type="text" name="refm" size="37"></td>
				</tr>
	</table>
	<fieldset>
		<legend>Precios</legend>
		<table>
			<tr>
				<td><label>Concesi&oacute;n Permanente</label></td>
				<td><input type="text" name="precio" size="6"></td>
			</tr>
		</table>
	</fieldset>
</div>
<div name="tumba" style="display:none">
	<table cellpadding="10">
		<tr>
			<td width="112"><label>Denominaci&oacute;n:</label></td>
			<td><input type="text" name="denom" size="37"></td>
		</tr>
		<tr>
			<td><label>Referencia:</label></td>
			<td><input type="text" name="reft" size="37"></td>
		</tr>
	</table>
</div>