<?php $baseURL = $f->config->url->base; ?>
<script type="text/javascript">
jQuery(document).ready(function(){
	jQuery('#btn_eliminar').click(function() {
		var url = "<?php echo($baseURL).$_GET['path']; ?>";
		var params = jQuery('#eliminar').serialize();
		jQuery.post(url,params,<?php echo $_GET['funcion']; ?>);
		MochaUI.closeWindow($('<?php echo $_GET['ventana']; ?>'));
		MUI.notification('Registro eliminado.');
		return false;
	});
	jQuery('#btn_cancelar').click(function() {
		MochaUI.closeWindow($('<?php echo $_GET['ventana']; ?>'));
	});
});
</script>
<div class="mochaTitleBar" style="background-color: #E5E5E5">
	<h3 class="mochaTitle">Eliminar item</h3>
</div>
<form name="eliminar" id="eliminar">
<input type="hidden" id="id" name="id" value="<? echo $_GET['id']; ?>">
<table width="370px" style="text-align:center">
	<tr>
		<td colspan="2">&iquest;Desea eliminar el siguiente item&#63;</td>
	</tr>
	<tr>
		<td colspan="2"><b><? echo htmlentities($_GET['nomb']); ?></b></td>
	</tr>
	<tr>
		<td>
			<input id="btn_cancelar" type="button" class="button" style="float: right" value="Cancelar">
		</td>
		<td width="70px">
			<input id="btn_eliminar" type="button" class="button" value="Eliminar">
		</td>
	</tr>
</table>
</form>