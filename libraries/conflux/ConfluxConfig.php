<?php
class ConfluxConfig {
	public function item($var=null){
		$config = array();
        $config['conflux-ruc'] = '20120958136';
        #$config['conflux-razon_social'] = 'SOCIEDAD BENEFICENCIA PUBLICA DE AREQUIPA';
        $config['conflux-razon_social'] = 'SOCIEDAD DE BENEFICENCIA AREQUIPA';
        #$config['conflux-direccion'] = 'CALLE PIEROLA 201';
        $config['conflux-direccion'] = 'AV. GOYENECHE 341';
        $config['conflux-ubigeo'] = '040101';
        $config['conflux-nota_firma'] = 'Elaborado por Sistema de Emision Electronica Facturador SBPA (CONFLUX-SEE) 1.0.0';
        $config['conflux-sol_usuario'] = 'SIST2016';
        $config['conflux-sol_clave'] = 'sbp@2016';
        $config['conflux-pfx_clave'] = 'beneficenciamayas2012';
        $config['environment'] = 'homologation';
        $config['conflux-pagina_web'] = 'https://facturacion.sbparequipa.gob.pe/';
        $config['conflux-resolucion_intendencia'] = '0520050000188';
        if(isset($config[$var])){
            return $config[$var];
        }else{
            return $config;
        }
	}
}
?>