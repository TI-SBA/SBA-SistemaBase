<?php

class FileSystemObject {
	const exec = 1;
	const read = 2;
	const write = 4;
	
	//public $clearCache = false;
	
	/*private function clearCache() {
		clearstatcache();
	}*/
	
	public function cd($path) {
		if (! is_dir($path)) { throw new Exception(); }
		chdir($path);
	}
	
	public function open($filename) {
		switch (true) {
			case (is_file($filename)):
				return new File( $filename );
				break;
			case (is_dir($filename)):
				return new Directory( $filename );
				break;
			default:
				throw new Exception();
		}
	}
	
	public function newfile($filename) {
		if (! file_put_contents( $filename, "" )) { throw new Exception(); }
	}
	
	public function newdir($dirname) {
		if (! mkdir($dirname)) { throw new Exception(); }
	}
	
	public function copy($to) {
		if (! copy($this->_filename, $to)) { throw new Exception(); }
	}
	
	public function move($to) {
		if (! rename($this->_filename, $to)) { throw new Exception(); }
	}
	
	public function delete() {
		switch (true) {
			case (is_file($filename)):
				if (! unlink($this->_filename)) { throw new Exception(); }
				break;
			case (is_dir($filename)):
				return new Directory( realpath($filename) );
				break;
			default:
				throw new Exception();
		}
	}
	
	public function owner($newowner) {
		if (! chown($this->_filename, $newowner)) { throw new Exception(); }
	}
	
	public function mode($owner, $group, $public) {
		$newmode = '0' . $owner . $group . $public;
		if (! chmod($this->_filename, $newmode)) { throw new Exception(); }
	}
}
?>