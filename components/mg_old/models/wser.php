<?php
class Model_mg_wser extends Model {
	//date_default_timezone_set("America/Lima");
	private $db;
	public $items;
	
	public function __construct() {
		global $f;
		$this->db = $f->datastore->mg_webservice;
	}

	protected function get_timestamp($host = 'pool.ntp.org', $timeout = 10){
		global $f;
		$socket = stream_socket_client('udp://' . $host . ':123', $errno, $errstr, (int)$timeout);
		$msg = "\010" . str_repeat("\0", 47);
		fwrite($socket, $msg);
		$response = fread($socket, 48);
		fclose($socket);
		// unpack to unsigned long
		$data = unpack('N12', $response);
		// 9 =  Receive Timestamp (rec): Time at the server when the request arrived
   		// from the client, in NTP timestamp format.
		$timestamp = sprintf('%u', $data[9]);
		// NTP = number of seconds since January 1st, 1900
		// Unix time = seconds since January 1st, 1970
		// remove 70 years in seconds to get unix timestamp from NTP time
		$timestamp -= 2208988800;
		$this->items = $timestamp;
	}
}
?>