<?php

namespace Phrenetic;


class StoreFile
{
	protected static $_instances = array();
	
	
	public function __construct($name)
	{
		$this->_rootStoreDirectory = ROOTDIR.'/store/'.$name;
		if (!file_exists($this->_rootStoreDirectory)) {
			$rc = mkdir(ROOTDIR.'/store/'.$name);
		}
		
		if (!is_dir($this->_rootStoreDirectory)) {
			throw new Exception('Store directory for '.
				$name.' does not exit');
		}
		if (!is_writeable($this->_rootStoreDirectory)) {
			throw new Exception('Store directory for '.
				$name.' is not writeable');
		}
	}
	
	public function add($fname)
	{
		if (!is_uploaded_file($fname)) {
			throw new Exception('File '
				.$fname.' is not an uploaded file');
		}
		
		$newname = md5($fname).'-'.time().'.blob';
		move_uploaded_file($fname, 
			$this->_rootStoreDirectory.'/'.$newname);
		
		return $newname;
	}
	
	public function get($name)
	{
		$name = basename($name);
		return file_get_contents($this->_rootStoreDirectory.'/'.$name);
	}
	
	public static function instance($name)
	{
		if (!isset(self::$_instances[$name])) {
			self::$_instances[$name] = new self($name);
		}
		
		return self::$_instances[$name];
	}
}