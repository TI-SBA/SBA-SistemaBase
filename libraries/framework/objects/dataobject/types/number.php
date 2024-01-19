<?php
/**
 * Librería auxiliar para manejo de números
 * @name Number
 * @version 1.2
 */
abstract class Number {
	const int = 0x1;
	const pos = 0x2;
	const neg = 0x4;
	
	public static function ceil($number) {
		return is_null($number) ? null : ceil( $number );
	}
	
	public static function floor($number) {
		return is_null($number) ? null : floor($number);
	}
	
	public static function round( $number, $precision = null ) {
		return is_null($number) ? null : round($number, $precision);
	}
	
	public static function hex() {
		return is_null($number) ? null : dechex( $number );
	}
	
	public static function oct() {
		return is_null($number) ? null : decoct( $number );
	}
	
	public static function bin() {
		return is_null($number) ? null : decbin( $number );
	}
	
	public static function ip(){
		return is_null($number) ? null : long2ip( $number );
	}
	
	public static function format($number, $decimals = 2, $decimals_sep = '.', $thousands_sep = '') {
		return is_null($number) ? null : number_format( $number, $decimals, $decimals_sep, $thousands_sep );
	}
	
	public static function mask($number, $mask) {
		
	}
	
	public static function pad($number, $length, $pad = "0") {
		return is_null($number) ? null : sprintf("%'{$pad}{$length}s", $number);
	}

	public static function rnd($min = 0, $max = RAND_MAX) {
		return mt_rand( $min, $max );
	}
	
	public static function is($number, $check) {
		$is = false;
		if ($check & self::int) { $is = is_int($number); }
		if ($check & self::pos) { $is = $number >= 0; }
		if ($check & self::neg) { $is = $number < 0; }
		return $is;
	}
	
	public static function lit($number) {
		if ( is_null($number) ) { return null; }
		$i = 0;
		$j = 0;

		$tmp = sprintf("%012d", $number);
		$str = "";

		if ( strlen($tmp) > 12) { return ""; }

		# zero is a special case.
		# you may want to change this to "no"
		# as in "no dollars and 12/100" for writing checks.
		if ( (float) $tmp == 0 ) { return "CERO"; }

		$i = (float) substr($tmp, 0, 3 );
		if ($i != 0 ) {
			if ($i != 1) { self::do_hundreds($i, $str); } else {
				$str .= " UN";
			}
			$str .= " MIL";
		}

		$i = (float) substr($tmp, 3, 3 );
		if ($i != 0 ) {
			if ($i != 1) {
				self::do_hundreds($i, $str);
			} else {
				$str .= " UN";
			}
			$str .= " MILLON" . (($i != "1") ? "ES" : "");
		}

		$i = (float) substr($tmp, 6, 3 );
		if ($i != 0 ) {
			if ($i != 1) { self::do_hundreds($i, $str); } else {
				$str .= " UN";
			}
			$str .= " MIL";
		}

		$i = (float) substr($tmp, -3 );
		if ($i != 0 ) {
			self::do_hundreds($i, $str);
		}

		return $str;
	}

	public static function do_hundreds($i, &$str) {
		if ($i > 99) {
	   		$j = $i;
	   		$i = (int) ($i / 100);

			switch (true) {
				case ($j == 100): $str .= " CIEN"; break;
				case ($j > 100 && $j <= 199): $str .= " CIENTO"; break;
				case ($j >= 200 && $j <= 299): $str .= " DOSCIENTOS"; break;
				case ($j >= 300 && $j <= 399): $str .= " TRESCIENTOS"; break;
				case ($j >= 400 && $j <= 499): $str .= " CUATROCIENTOS"; break;
				case ($j >= 500 && $j <= 599): $str .= " QUINIENTOS"; break;
				case ($j >= 600 && $j <= 699): $str .= " SEISCIENTOS"; break;
				case ($j >= 700 && $j <= 799): $str .= " SETECIENTOS"; break;
				case ($j >= 800 && $j <= 899): $str .= " OCHOCIENTOS"; break;
				case ($j >= 900 && $j <= 999): $str .= " NOVECIENTOS"; break;
			}

			$i = $j % 100;
		}

		if ($i != 0 ) {
			self::do_tens($i, $str);
		}

		return;
	}

	public static function do_tens($i, &$str) {
		$x = $i % 100;
		switch (true) {
			case ($x >= 90 && $x <= 99): $str .= " NOVENTA"; self::do_ones($i, $str); break;
			case ($x >= 80 && $x <= 89): $str .= " OCHENTA"; self::do_ones($i, $str); break;
			case ($x >= 70 && $x <= 79): $str .= " SETENTA"; self::do_ones($i, $str); break;
			case ($x >= 60 && $x <= 69): $str .= " SESENTA"; self::do_ones($i, $str); break;
			case ($x >= 50 && $x <= 59): $str .= " CINCUENTA"; self::do_ones($i, $str); break;
			case ($x >= 40 && $x <= 49): $str .= " CUARENTA"; self::do_ones($i, $str); break;
			case ($x >= 30 && $x <= 39): $str .= " TREINTA"; self::do_ones($i, $str); break;
			//case ($x >= 20 && $x <= 29): $str .= " VEINTE"; self::do_ones($i, $str); break;
			case ($x == 29): $str .= " VEINTINUEVE"; break;
			case ($x == 28): $str .= " VEINTIOCHO"; break;
			case ($x == 27): $str .= " VEINTISIETE"; break;
			case ($x == 26): $str .= " VEINTISEIS"; break;
			case ($x == 25): $str .= " VEINTICINCO"; break;
			case ($x == 24): $str .= " VEINTICUATRO"; break;
			case ($x == 23): $str .= " VEINTITRES"; break;
			case ($x == 22): $str .= " VEINTIDOS"; break;
			case ($x == 21): $str .= " VEINTIUNO"; break;
			case ($x == 20): $str .= " VEINTE"; break;
			case ($x == 19): $str .= " DIECINUEVE"; break;
			case ($x == 18): $str .= " DIECIOCHO"; break;
			case ($x == 17): $str .= " DIECISIETE"; break;
			case ($x == 16): $str .= " DIECISEIS"; break;
			case ($x == 15): $str .= " QUINCE"; break;
			case ($x == 14): $str .= " CATORCE"; break;
			case ($x == 13): $str .= " TRECE"; break;
			case ($x == 12): $str .= " DOCE"; break;
			case ($x == 11): $str .= " ONCE"; break;
			case ($x == 10): $str .= " DIEZ"; break;
			default: self::do_ones($i, $str); break;
		}

		return;
	}

	public static function do_ones($i, &$str) {
		if ($i < 10 || $i % 10 == 0) {
			$str .= " ";
		} else {
			$str .= " Y ";
		}

		$x = $i % 10;
		switch ($x) {
			case 9: $str .= "NUEVE"; break;
			case 8: $str .= "OCHO"; break;
			case 7: $str .= "SIETE"; break;
			case 6: $str .= "SEIS"; break;
			case 5: $str .= "CINCO"; break;
			case 4: $str .= "CUATRO"; break;
			case 3: $str .= "TRES"; break;
			case 2: $str .= "DOS"; break;
			case 1: $str .= "UNO"; break;
		}

		return;
	}
}

?>