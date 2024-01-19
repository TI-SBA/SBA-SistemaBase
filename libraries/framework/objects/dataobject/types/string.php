<?php
/**
 * Librería auxiliar para manejo de cadenas de texto
 * @name String
 * @version 1.2
 */
abstract class String {
	/* constantes de codificación */
	const ascii = 0x1;
	const utf8 = 0x2;
	const latin1 = 0x4;
	
	/* constantes de formato de texto. función: is() */
	const hex = 0x10;
	const date = 0x20;
	const ip = 0x40;
	const email = 0x80;
	const url = 0x100;
	
	/* constantes de nivel de aleatorio. función: rnd() */
	const numbers = 0x1;
	const uchars = 0x2;
	const lchars = 0x4;
	const symbols = 0x8;
	
	public static function len($string) {
		return strlen($string);
	}

	public static function html($string) {
		$str = htmlentities( $string, ENT_QUOTES, "UTF-8" );
		$str = nl2br( $str );
		return $str;
	}
	
	public static function encoding($string) {
    	return mb_detect_encoding($string, "ISO-8859-1, UTF-8");
	}

	public static function utf8($string) {
		//return (mb_detect_encoding($string, "ISO-8859-1, UTF-8") == "ISO-8859-1") ? iconv("ISO-8859-1", "UTF-8", $string) : $string;
		return iconv("ISO-8859-1", "UTF-8", $string);
	}
	
	public static function latin1($string) {
    	//return (mb_detect_encoding($string, "UTF-8, ISO-8859-1") == "UTF-8") ? iconv("UTF-8", "ISO-8859-1", $string) : $string;
    	return iconv("UTF-8", "ISO-8859-1", $string);
	}

	/*
	function sql() {
		global $f;
		if ( $this->length() == 0 ) { return "NULL"; }
		$str = (mb_detect_encoding($this->string, "UTF-8, ISO-8859-1") == "UTF-8") ? iconv("UTF-8", "ISO-8859-1", $this->string) : $this->string;
		$str = $f->database->get_escaped( $str );
		$str = $f->database->get_quoted( $str );
		return $str;
	}
	*/

	public static function md5($string) {
		return md5( $string );
	}
	
	public static function sha1($string) {
		return sha1( $string );
	}
	
	public static function uc($string) {
		return strtoupper( $string );
	}
	
	public static function ucf($string) {
		return ucfirst( $string );
	}
	
	public static function ucw($string) {
		return ucwords( $string );
	}
	
	public static function lc($string) {
		return strtolower( $string );
	}
	
	public static function trim($string) {
		return trim($string);
	}
	
	public static function ltrim($string) {
		return ltrim($string);
	}
	
	public static function rtrim($string) {
		return rtrim($string);
	}

	public static function rnd($length = 6, $level = 0xF) {
		$str = "";
		$levels = array();
		for($i = 1; $i <= $length; $i++) {
			if ( $level & self::numbers ) { $levels[] = self::numbers; }
			if ( $level & self::uchars ) { $levels[] = self::uchars; }
			if ( $level & self::lchars ) { $levels[] = self::lchars; }
			if ( $level & self::symbols ) { $levels[] = self::symbols; }
			$part = mt_rand(0, size($levels));
			switch ( $levels[$part] ) {
				case self::numbers: $str .= mt_rand(0, 9); break;
		    	case self::uchars: $str .= chr(mt_rand(65, 90)); break;
		    	case self::lchars: $str .= chr(mt_rand(97, 122)); break;
		    	case self::symbols:
		    		$sub_part = mt_rand(0, 3);
		    		switch ( $sub_part ) {
		    			case 0: $str .= chr(mt_rand(33, 47)); break;
		    			case 1: $str .= chr(mt_rand(58, 64)); break;
		    			case 2: $str .= chr(mt_rand(91, 96)); break;
		    			case 3: $str .= chr(mt_rand(123, 126)); break;
		    		}
			}
		}
		return $str;
	}
	
	public static function is($string, $check) {
		$is = false;
		
		return $is;
	}
}
?>