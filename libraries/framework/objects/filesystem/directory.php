<?php

class Directory {
	const exec = 0x1;
	const read = 0x2;
	const write = 0x4;
	
	private $_dirname;
	
	private $properties;

	public function __construct($dirname) {
		if (is_dir($dirname)) {
			$this->_dirname = realpath( $dirname );
			if ($this->_dirname[strlen($this->_dirname)-1] != '/') { $this->_dirname .= '/'; }
		} else {
			throw new Exception();
		}
	}
	
	private function getProperty($property) {
		if (! is_array($this->properties)) { $this->properties = array(); }
		if (! isset($this->properties[$property])) {
			switch ($property) {
				case 'name':
					$this->properties['name'] = basename($this->_filename);
					break;
				case 'dir':
					$this->properties['dir'] = dirname($this->_filename);
					break;
				case 'filecount':
					$this->properties['size'] = filesize($this->_filename);
					break;
				case 'dircount':
					$this->properties['size'] = filesize($this->_filename);
					break;
				case 'owner':
					$this->properties['owner'] = fileowner($this->_filename);
					break;
				case 'mode':
					
				case 'readonly':
					$this->properties['readonly'] = ! is_writable($this->_filename);
					break;
				case 'files':
				
				case 'dirs':
					
			}
		}
		return $this->properties[$property];
	}
	
	
	
	public function newdir($name) {
		if (file_exists($this->_dirname))
	}
	
	public function newfile($name) {
		
	}
	
	public function open($name) {
		
	}
	
	public function read() {
		
		return file_get_contents($this->_filename);
	}
	
	public function copy() {}
	
	public function move() {
		clearstatcache();
	}
	
	public function delete() {
		clearstatcache();
	}
	
	public function owner() {
		
	}
	
	public function mode() {
		
	}
	
	public function __get($property) {
		switch ($property) {
			case 'name':
			case 'dir':
			case 'ext':
			case 'size':
			case 'owner':
			case 'mode':
			case 'md5':
			case 'crc32':
			case 'readonly':
				$this->getFileProperty($property);
		}
	}
	
	public function __toString() {
		return $this->_dirname;
	}
}
?>