<?php
/**
 * @name FileStorage
 * Almacenamiento de archivos
 * Provee almacenamiento aleatorio en directorio específico
 * @author dsalas
 * @version 1.12
 */
class FileStorage {
	const FS_MIN = 0;
	const FS_MAX = 10;
	private $_BaseDir;
	
	const ERR_NOCONFIG = 'No existe el archivo de configuración';
	const ERR_CONFIG = 'El archivo de configuración no es correcto';
	const ERR_NOMAGIC = 'No existe la base de datos de MIME';
	const ERR_NODIR = 'No existe el directorio especificado';
	const ERR_NOTEMPTY = 'El directorio especificado no se encuentra vacío';
	const ERR_NOFILE = 'No se encuentra el archivo especificado';
	const ERR_COPY = 'No se pudo copiar el archivo especificado';
	const ERR_DELETE = 'No se pudo eliminar el archivo especificado';
	
	function __construct($ConfigName) {
		$ini_file = dirname(__FILE__).'/FileStorage.config';
		if (! file_exists($ini_file) ) { die(FileStorage::ERR_NOCONFIG); }
		$config = parse_ini_file($ini_file, true);
		if (!isset($config[$ConfigName]['directory'])) { die(FileStorage::ERR_CONFIG); }
		$directory = $config[$ConfigName]['directory'];
		
		if (! is_dir($directory)) { die(FileStorage::ERR_NODIR); }
		$this->_BaseDir = dir( $directory );
	}
	
	function CreateStorage($levels = 1) {
		$count = 0;
		while( ($f = $this->_BaseDir->read()) !== false) {
			if ($f != "." && $f != "..") { $count++; }
		}
		if ($count > 0) { echo FileStorage::ERR_NOTEMPTY; return false; }
		
		$this->CreateFolders( $this->_BaseDir->path );
		if ( $levels == 2 ) {
			for( $i = FileStorage::FS_MIN; $i <= FileStorage::FS_MAX; $i++ ) {
				$f = $this->_BaseDir->path . '/' . sprintf("%02X", $i);
				$this->CreateFolders( $f );
			}
		}
		return true;
	}
	
	function CopyFile($file) {
		if (! file_exists($file) ) { echo FileStorage::ERR_NOFILE; return false; }
		while ( true ) {
			$dest = $this->ChooseFolder();
			if (! file_exists($dest) ) { break; }
		}
		
		if (! copy($file, $dest) ) { echo FileStorage::ERR_COPY; return false; }
		$dest = str_replace($this->_BaseDir->path . '/', '', $dest);
		return $dest;
	}
	
	function MoveFile($file) {
		$dest = $this->CopyFile($file);
		if ($dest === false) { echo FileStorage::ERR_COPY; return false; }
		if (! unlink($file)) { echo FileStorage::ERR_DELETE; }
		return $dest;
	}
	
	function MoveUploadedFile($field, $key = null) {
		if ( $key ) {
			$temp_file = $_FILES[$field]['tmp_name'][$key];
			//$type = $_FILES[$field]['type'][$key];
			//$size = $_FILES[$field]['size'][$key];
		} else {
			$temp_file = $_FILES[$field]['tmp_name'];
			//$type = $_FILES[$field]['type'];
			//$size = $_FILES[$field]['size'];
		}
		if (! is_uploaded_file( $temp_file )) { echo FileStorage::ERR_NOFILE; return false; }
		
		$dest = $this->CopyFile($temp_file);
		if ($dest === false) { echo FileStorage::ERR_COPY; return false; }
		if (! unlink($temp_file)) { echo FileStorage::ERR_DELETE; }
		//return array('path'=> $dest, 'size'=> $size, 'type'=> $type);
		return $dest;
	}
	
	function ReadFile($path) {
		if (! $f = $this->CheckFile($path) ) { echo FileStorage::ERR_NOFILE; return false; }
		@readfile($f);
	}
	
	function GetSize($path) {
		if (! $f = $this->CheckFile($path) ) { echo FileStorage::ERR_NOFILE; return false; }
		return filesize($f);
	}
	
	function GetChecksum($path) {
		if (! $f = $this->CheckFile($path) ) { echo FileStorage::ERR_NOFILE; return false; }
		return md5_file($f);
	}
	
	function GetMimeType($path) {
		$magic_db = dirname(__FILE__).'/magic/magic';
		if (! file_exists($magic_db) ) { die(FileStorage::ERR_NOMAGIC); }
		if (! $f = $this->CheckFile($path) ) { echo FileStorage::ERR_NOFILE; return false; }
		$finfo = finfo_open(FILEINFO_MIME, $magic_db);
  		return finfo_file($finfo, $f);
	}
	
	function GetPageCount($path) {
		if (! $f = $this->CheckFile($path) ) { echo FileStorage::ERR_NOFILE; return false; }
		switch ( $this->GetMimeType($path) ) {
		case 'application/pdf': return $this->pdf_getPageCount($f); break;
		default: return 0;
		}
	}
	
	function DeleteFile($path) {
		if (! $f = $this->CheckFile($path) ) { echo FileStorage::ERR_NOFILE; return false; }
		if (! unlink($path)) {echo FileStorage::ERR_DELETE; return false; }
		return true;
	}
	
	private function CheckFile($path) {
		$file = $this->_BaseDir->path . '/' . $path;
		return file_exists($file) ? $file : false;
	}
	
	private function CreateFolders($dir) {
		for( $i = FileStorage::FS_MIN; $i <= FileStorage::FS_MAX; $i++ ) {
			mkdir($dir . '/' . sprintf("%02X", $i));
		}
		return true;
	}
	
	private function ChooseFolder() {
		$dest = $this->_BaseDir->path;
		while ( true ) {
			$f = sprintf("%02X", rand(FileStorage::FS_MIN, FileStorage::FS_MAX));
			$dest .= '/' . $f;
			if ( is_dir($dest) ) { continue; }
			break;
		}
		return $dest;
	}
	
	private function pdf_getPageCount($file) {
		// se debe comprobar previamente que archivo existe
		if($handle = @fopen($file, "rb")) {
			$count = 0;
			$i=0;
			while (!feof($handle)) {
				if($i > 0) {
					$contents .= fread($handle,8152);
				} else {
					$contents = fread($handle, 1000);
					//In some pdf files, there is an N tag containing the number of
					//of pages. This doesn't seem to be a result of the PDF version.
					//Saves reading the whole file.
					if(preg_match("/\/N\s+([0-9]+)/", $contents, $found)) {
						return $found[1];
					}
				}
				$i++;
			}
			fclose($handle);
		
			//get all the trees with 'pages' and 'count'. the biggest number
			//is the total number of pages, if we couldn't find the /N switch above.
			if (preg_match_all("/\/Type\s*\/Pages\s*.*\s*\/Count\s+([0-9]+)/", $contents, $capture, PREG_SET_ORDER)) {
				foreach($capture as $c) {
					if($c[1] > $count)
					$count = $c[1];
				}
				return $count;
			}
		}
	    return 0;
	}

}
?>