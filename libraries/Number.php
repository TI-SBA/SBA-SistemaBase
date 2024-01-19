<?php
//defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * Librería auxiliar para manejo de números
 * @name Number
 * @version 1.2
 */
class Number {
		
	public function lit($number) {
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
			if ($i != 1) { $this->do_hundreds($i, $str); }
			$str .= " MIL";
		}

		$i = (float) substr($tmp, 3, 3 );
		if ($i != 0 ) {
			if ($i != 1) {
				$this->do_hundreds($i, $str);
			} else {
				$str .= " UN";
			}
			$str .= " MILLON" . (($i != "1") ? "ES" : "");
		}

		$i = (float) substr($tmp, 6, 3 );
		if ($i != 0 ) {
			if ($i != 1) { $this->do_hundreds($i, $str); }
			$str .= " MIL";
		}

		$i = (float) substr($tmp, -3 );
		if ($i != 0 ) {
			$this->do_hundreds($i, $str);
		}

		return $str;
	}

	private function do_hundreds($i, &$str) {
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
			$this->do_tens($i, $str);
		}

		return;
	}

	private function do_tens($i, &$str) {
		$x = $i % 100;
		switch (true) {
			case ($x >= 90 && $x <= 99): $str .= " NOVENTA"; $this->do_ones($i, $str); break;
			case ($x >= 80 && $x <= 89): $str .= " OCHENTA"; $this->do_ones($i, $str); break;
			case ($x >= 70 && $x <= 79): $str .= " SETENTA"; $this->do_ones($i, $str); break;
			case ($x >= 60 && $x <= 69): $str .= " SESENTA"; $this->do_ones($i, $str); break;
			case ($x >= 50 && $x <= 59): $str .= " CINCUENTA"; $this->do_ones($i, $str); break;
			case ($x >= 40 && $x <= 49): $str .= " CUARENTA"; $this->do_ones($i, $str); break;
			case ($x >= 30 && $x <= 39): $str .= " TREINTA"; $this->do_ones($i, $str); break;
			//case ($x >= 20 && $x <= 29): $str .= " VEINTE"; $this->do_ones($i, $str); break;
			case ($x == 29): $str .= " VEINTINUEVE"; break;
			case ($x == 28): $str .= " VEINTIOCHO"; break;
			case ($x == 27): $str .= " VEINTICIETE"; break;
			case ($x == 26): $str .= " VEINTISEIS"; break;
			case ($x == 25): $str .= " VEINTICINCO"; break;
			case ($x == 24): $str .= " VEINTICUATRO"; break;
			case ($x == 23): $str .= " VEINTITRES"; break;
			case ($x == 22): $str .= " VEINTIDOS"; break;
			case ($x == 21): $str .= " VEINTIUNO"; break;
			case ($x == 20): $str .= " VEINTE"; break;
			case ($x == 19): $str .= " DIECINUEVE"; break;
			case ($x == 18): $str .= " DIECIOCHO"; break;
			case ($x == 17): $str .= " DIECISIENTE"; break;
			case ($x == 16): $str .= " DIECISEIS"; break;
			case ($x == 15): $str .= " QUINCE"; break;
			case ($x == 14): $str .= " CATORCE"; break;
			case ($x == 13): $str .= " TRECE"; break;
			case ($x == 12): $str .= " DOCE"; break;
			case ($x == 11): $str .= " ONCE"; break;
			case ($x == 10): $str .= " DIEZ"; break;
			default: $this->do_ones($i, $str); break;
		}

		return;
	}

	private function do_ones($i, &$str) {
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