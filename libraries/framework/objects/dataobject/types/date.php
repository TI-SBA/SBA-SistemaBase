<?php
/**
 * Librería auxiliar para manejo de fecha y hora
 * @name Date
 * @version 1.2
 */
abstract class Date {
	const date = 0x1;
	const time = 0x2;
	const datetime = 0x3;
	
	const leap = 0x10;

	const interval_years = 'years';
	const interval_months = 'months';
	const interval_days = 'days';
	const interval_hours = 'hours';
	const interval_minutes = 'minutes';
	const interval_seconds = 'seconds';
	
	public static function now() {
		return date("Y-m-d H:i:s");
	}

	public static function format($date, $format) {
		if ( is_null( $date) ) { return null; }
		if (! is_integer($date)) { $date = self::parse_date($date, self::datetime); }
		$fdate = date( $format, $date );
		if (substr_count($format, "F") > 0) {
			$months = array( 1 => "Enero", 2 => "Febrero", 3 => "Marzo", 4 => "Abril", 5 => "Mayo", 6 => "Junio", 7 => "Julio", 8 => "Agosto", 9 => "Septiembre", 10 => "Octubre", 11 => "Noviembre", 12 => "Diciembre");
			$fdate = str_ireplace(date("F", $date), $months[date("n", $date)], $fdate);
		}
		if (substr_count($format, "M") > 0) {
			$months = array( 1 => "Ene", 2 => "Feb", 3 => "Mar", 4 => "Abr", 5 => "May", 6 => "Jun", 7 => "Jul", 8 => "Ago", 9 => "Sep", 10 => "Oct", 11 => "Nov", 12 => "Dic");
			$fdate = str_ireplace(date("M", $date), $months[date("n", $date)], $fdate);
		}
		if (substr_count($format, "l") > 0) {
			$days = array( 0 => "Domingo", 1 => "Lunes", 2 => "Martes", 3 => "Miércoles", 4 => "Jueves", 5 => "Viernes", 6 => "Sábado");
			$fdate = str_ireplace(date("l", $date), $days[date("w", $date)], $fdate);
		}
		if (substr_count($format, "D") > 0) {
			$days = array( 0 => "Dom", 1 => "Lun", 2 => "Mar", 3 => "Mié", 4 => "Jue", 5 => "Vie", 6 => "Sáb");
			$fdate = str_ireplace(date("D", $date), $days[date("w", $date)], $fdate);
		}
		return $fdate;
	}

	public static function fdate($date) {
		if ( is_null($date) ) { return null; }
		if (! is_integer($date)) { $date = self::parse_date($date, self::datetime); }
		return date("Y-m-d", $date);
	}

	public static function ftime($date) {
		if ( is_null($date) ) { return null; }
		if (! is_integer($date)) { $date = self::parse_date($date, self::datetime); }
		return date("H:i:s", $date);
	}

	public static function ts() {
		return time();
	}

	public static function add( $date, $interval = self::interval_seconds, $value ) {
        $base_day = date("d", $date);
        $base_month = date("m", $date);
        $base_year = date("Y", $date);
        $base_hour = date("H", $date);
        $base_minute = date("i", $date);
        $base_second = date("s", $date);
        $days = 0;
        $months = 0;
        $years = 0;
        $hours = 0;
        $minutes = 0;
        $seconds = 0;
        //if ( isset( $$interval ) ) { $$interval = $value; }
        $date = mktime( $base_hour + $hours, $base_minute + $minutes, $base_second + $seconds, $base_month + $months, $base_day + $days, $base_year + $years);
        return self::str_date( $date );
	}

	public static function diff( $date1, $date2 ) {
        if ( is_string($date2) ) { $date2 = self::parse_date( $date2 ); }
        if ( is_string($date1) ) { $date1 = self::parse_date( $date1 ); }
        
        /*if ($date2 > $this->date) {
            $diff = $date2 - $this->date;
        } else {
            $diff = $this->date - $date2;
        }*/
		$diff = $date2 - $date1;

        $diffarr['years'] = self::extract_from_seconds($diff, "years");
        $diffarr['months'] = self::extract_from_seconds($diff, "months");
        $diffarr['days'] = self::extract_from_seconds($diff, "days");
        $diffarr['hours'] = self::extract_from_seconds($diff, "hours");
        $diffarr['minutes'] = self::extract_from_seconds($diff, "minutes");
        $diffarr['seconds'] = self::extract_from_seconds($diff, "seconds");

        return $diffarr;
	}
	
	public static function is( $date, $check ) {
		$is = false;
		if ($check & self::leap) {
			$year = date('Y', $date);
			if ( function_exists('mcal_is_leap_year') ) { $is = mcal_is_leap_year( $year ); }
			if ( $year % 4 == 0 ) {
				if ( $year % 100 == 0 ) {
					if ( $year % 200 == 0 ) { $is = true; }
					else { $is = false; }
				} else { $is = true; }
			} else { $is = false; }
		}
		return $is;
	}

	private function str_date( $date, $mode ) {
		if ( is_null($date) ) { return null; };
		switch ( $mode ) {
			case self::time:
				$str_date = date("H:i:s", $date);
				break;
			case self::date:
				$str_date = date("Y-m-d", $date);
				break;
			default:
				$str_date = date("Y-m-d H:i:s", $date);
		}
		return $str_date;
	}

	private function parse_date( $str, $mode=self::datetime ) {
		if ( is_null($str) ) { return null; }
		$date = array( 'year' => 0,	'month' => 0, 'day' => 0, 'hour' => 0, 'minute' => 0, 'second' => 0 );
		$str .= "";
		$str = preg_replace("#\s#", "", $str);
		$str = preg_replace("#\:#", "", $str);
		$str = preg_replace("#\.#", "", $str);
		$str = preg_replace("#\-#", "", $str);
		$str = preg_replace("#\/#", "", $str);
		if ( $mode != self::time ) {
			$date['year']   = (strlen($str) == 6) ? substr($str,0,2) : substr($str,0,4);
			$date['month']  = (strlen($str) == 6) ? substr($str,2,2) : substr($str,4,2);
			$date['day']    = (strlen($str) == 6) ? substr($str,4,2) : substr($str,6,2);
			$date['hour']   = (strlen($str) < 12) ? 0 : substr($str, 8,2);
			$date['minute'] = (strlen($str) < 12) ? 0 : substr($str, 10,2);
			$date['second'] = (strlen($str) < 14) ? 0 : substr($str, 12,2);
		} else {
			$date['second'] = (strlen($str) == 6) ? substr($str, 4,2) : 0;
			$date['hour']   = (strlen($str) >= 4) ? substr($str, 0,2) : 0;
			$date['minute'] = (strlen($str) >= 4) ? substr($str, 2,2) : 0;
		}
		$parse_date = mktime($date['hour'], $date['minute'], $date['second'], $date['month'], $date['day'], $date['year']);
		return $parse_date;
	}

	private function extract_from_seconds(&$seconds, $interval) {
	    switch ($interval) {
			case "minutes": case "minute": case "i":
			    $value   = bcdiv($seconds,60);
			    $seconds = bcmod($seconds,60);
			    break;
			case "hours": case "H": case "h": case "hour":
			    $value   = bcdiv($seconds,3600);
			    $seconds = bcmod($seconds,3600);
			    break;
			case "days": case "D": case "d": case "day":
			    $value   = bcdiv($seconds,3600*24);
			    $seconds = bcmod($seconds,3600*24);
			    break;
			case "months": case "m": case "n": case "month":
			    $value   = bcdiv($seconds,3600*24*30);
			    $seconds = bcmod($seconds,3600*24*30);
			    break;
			case "weeks": case "W": case "w": case "week":
			    $value   = bcdiv($seconds,3600*24*7);
			    $seconds = bcmod($seconds,3600*24*7);
			    break;
			case "years": case "year": case "y": case "Y":
			    $value   = bcdiv($seconds,3600*24*365);
			    $seconds = bcmod($seconds,3600*24*365);
			    break;
			default:
			    $value = $seconds;
	    }
    	return $value;
    }
}
?>
