<?php

class File {
	const exec = 1;
	const read = 2;
	const write = 4;
	
	private $_filename;
	
	private $properties;

	public function __construct($filename = null) {
		if (is_file($filename)) {
			$this->_filename = realpath($filename);
		} else {
			throw new Exception('no existe el archivo especificado: ' . $filename);
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
				case 'ext':
					 $info = pathinfo($this->_filename);
					 $this->properties['ext'] = isset($info['extension']) ? $info['extension'] : '';
					break;
				case 'mime':
					if (function_exists('finfo_open')) {
						$finfo = finfo_open(FILEINFO_MIME); // return mime type mimetype extension
   						$this->properties['mime'] = finfo_file($finfo, $this->_filename);
						finfo_close($finfo);
					} elseif (function_exists('mime_content_type')) {
						$this->properties['mime'] = mime_content_type($this->_filename);
					} else {
						$this->properties['mime'] = '';
					}
					break;
				case 'size':
					$this->properties['size'] = filesize($this->_filename);
					break;
				case 'created':
					$this->properties['created'] = filesize($this->_filename);
					break;
				case 'modified':
					$this->properties['modified'] = filesize($this->_filename);
					break;
				case 'owner':
					$this->properties['owner'] = fileowner($this->_filename);
					break;
				case 'mode':
					
				case 'md5':
					$this->properties['md5'] = hash_file('MD5', $this->_filename);
					break;
				case 'crc32':
					$this->properties['crc32'] = hash_file('CRC32', $this->_filename);
					break;
				case 'readonly':
					$this->properties['readonly'] = ! is_writable($this->_filename);
					break;
			}
		}
		return $this->properties[$property];
	}
	
	public function read() {
		return file_get_contents($this->_filename);
	}
	
	public function write($content) {
		if (! file_put_contents( $this->filename, $content )) { throw new Exception('no se puede escribir el archivo'); }
		clearstatcache();
		unset($this->properties['size']);
		unset($this->properties['md5']);
		unset($this->properties['crc32']);
	}
	
	public function append($content) {
		if (! file_put_contents( $this->filename, $content, FILE_APPEND ))  { throw new Exception('no se puede agregar contenido al archivo'); }
		clearstatcache();
		unset($this->properties['size']);
		unset($this->properties['md5']);
		unset($this->properties['crc32']);
	}
	
	public function copy($to) {
		if (! copy($this->_filename, $to)) { throw new Exception('no se puede copiar el archivo'); }
	}
	
	public function move($to) {
		if (! rename($this->_filename, $to)) { throw new Exception('no se puede mover el archivo'); }
		clearstatcache();
	}
	
	public function delete() {
		if (! unlink($this->_filename)) { throw new Exception('no se puede eliminar el archivo'); }
		clearstatcache();
	}
	
	public function owner($newowner) {
		if (! chown($this->_filename, $newowner)) { throw new Exception('no se puede cambiar de dueño'); }
		clearstatcache();
		unset($this->properties['owner']);
	}
	
	public function mode($owner, $group, $public) {
		$newmode = '0' . $owner . $group . $public;
		if (! chmod($this->_filename, $newmode)) { throw new Exception('no se pueden cambiar los permisos'); }
		clearstatcache();
		unset($this->properties['mode']);
	}
	
	public function __get($property) {
		switch ($property) {
			case 'name':
			case 'dir':
			case 'ext':
			case 'mime':
			case 'size':
			case 'owner':
			case 'mode':
			case 'md5':
			case 'crc32':
			case 'readonly':
				return $this->getProperty($property);
				break;
			default:
		 		throw new Exception('no existe la propiedad ' . $property);
		}
	}
	
	public function __toString() {
		return $this->_filename;
	}
}
?>