<?php
class ConfluxException extends Exception
{
	protected $_data;
	public function __construct($message="", $code=0 , Exception $previous=NULL, $data = array())
	{
		$this->_data = $data;
		parent::__construct($message, $code, $previous);
	}
	public function getData()
	{
		return $this->_data;
	}
}
?>