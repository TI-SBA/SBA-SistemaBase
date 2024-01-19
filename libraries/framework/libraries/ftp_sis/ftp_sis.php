<?php
define("SERVER","146.148.98.178");
define("PORT",21);
define("USER","bitnami");
define("PASSWORD","fxeutQ9M");
define("MODO",true);
class ftp_sis{
	function ConectarFTP(){
		$id_ftp=ftp_connect(SERVER,PORT);
		$login_result = ftp_login($id_ftp, USER, PASSWORD) or die("<h1>You do not have access to this ftp server!</h1>");
		ftp_pasv($id_ftp,MODO);
		if((!$id_ftp) || (!$login_result)){
			die("Falló la conexión");
		}
		return $id_ftp;
	}
	function SubirArchivo($archivo_local,$archivo_remoto){
		echo $archivo_local."<br />";
		echo $archivo_remoto."<br />";
		$id_ftp=$this->ConectarFTP();
		ftp_put($id_ftp,$archivo_remoto,$archivo_local,FTP_BINARY);
		//ftp_put($conn_id, $remote_file, $file, FTP_ASCII)
		ftp_quit($id_ftp);
	}
	function ObtenerRuta(){
		$id_ftp=$this->ConectarFTP();
		$Directorio=ftp_pwd($id_ftp);
		ftp_quit($id_ftp);
		return $Directorio;
	}
	
	public function uploadFile ($fileFrom, $dir, $fileTo){
		$id_ftp=$this->ConectarFTP();
		// *** Set the transfer mode
		$asciiArray = array('txt', 'csv');
		$value = explode('.', $fileFrom);
		$extension = end($value);
		if (in_array($extension, $asciiArray)) {
			$mode = FTP_ASCII;
		} else {
			$mode = FTP_BINARY;
		}
        $contents_on_server = ftp_nlist($id_ftp, $dir); 
		if(substr($dir, strlen($dir)-1)=='/')
			$dir = substr($dir, 0, strlen($dir)-1);
        $check_file_exist = $dir.'/'.$fileTo;
        if (in_array($check_file_exist, $contents_on_server)){
        	@ftp_delete($id_ftp,$check_file_exist);
		}
		if(@ftp_delete($id_ftp,$check_file_exist)){
			//
		}
		$fileTo = $dir.'/'.$fileTo;
		// *** Upload the file
		$upload = ftp_put($id_ftp, $fileTo, $fileFrom, $mode);
		// *** Check upload status
		if (!$upload) {
			return false;
		} else {
			return true;
		}
	}
	
	public function crearCarpeta($directory){
		// *** If creating a directory is successful...
		$id_ftp=$this->ConectarFTP();
		if (ftp_mkdir($id_ftp, $directory)) {
			return true;
		} else {
			// *** ...Else, FAIL.
			return false;
		}
	}
	
	
	
	
	
	
	
	
	public function ftpDeleteDirectory($conn_id,$directory){
	    if(empty($directory))//Validate that a directory was sent, otherwise will delete ALL files/folders
	        return false;
	    else{
	        # here we attempt to delete the file/directory
	        if( !(@ftp_rmdir($conn_id,$directory) || @ftp_delete($conn_id,$directory)) )
	        {
	            # if the attempt to delete fails, get the file listing
	            $filelist = @ftp_nlist($conn_id, $directory);
	            # loop through the file list and recursively delete the FILE in the list
	            if(isset($filelist)){
	            	if (is_array($filelist) || is_object($filelist)){
			            foreach($filelist as $file){
			            //  return json_encode($filelist);
			                //$this->ftpDeleteDirectory($conn_id,$directory.'/'.$file);/***THIS IS WHERE I MUST RESEND ABSOLUTE PATH TO FILE***/
							$this->ftpDeleteDirectory($conn_id,$file);/***THIS IS WHERE I MUST RESEND ABSOLUTE PATH TO FILE***/
			            }
			        }
		        }
	
	            #if the file list is empty, delete the DIRECTORY we passed
	            $this->ftpDeleteDirectory($conn_id,$directory);
	        }
	    }
	    return true;
	}
}
?>