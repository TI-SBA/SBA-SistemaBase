<?php
include_once('FirePHPCore/fb.php');
				
class Trace {
	
	/**
	 * @var FirePHP
	 */
	private static $instance;
	
	/**
	 * @return Trace
	 */
	public static function getInstance() {
        if (!self::$instance) {
            self::$instance = FirePHP::getInstance(true);
            self::$instance->setOptions(array('includeLineNumbers' => false));
        }
        return self::$instance;
	}
	
	public static function disable() {
		self::$instance->enabled = false;
	}
    
	public static function log($message, $label = null) {
    	self::$instance->log(utf8_encode($message), utf8_encode($label));
    }
    
	public static function info($message, $label = null) {
    	self::$instance->info(utf8_encode($message), utf8_encode($label));
    }
    
	public static function warn($message, $label = null) {
    	self::$instance->warn(utf8_encode($message), utf8_encode($label));
    }
    
	public static function error($message, $label = null) {
    	self::$instance->error(utf8_encode($message), utf8_encode($label));
    }
    
    public static function trace2($object) {
    	self::$instance->trace($object);
    }
    
    public static function table($subject, $label = null) {
    	self::$instance->table(utf8_encode($label), $subject);
    }
}
?>