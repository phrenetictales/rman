<?php

class FileStore
{
	protected $_archive = null;
	
	
	private function _makeName()
	{
		$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$randstring = '';
		
		for ($i = 0; $i < 32; $i++) 
		{
			$randstring .= $characters[rand(0, strlen($characters)-1)];
		}
		
		return $randstring . time();
	}
	
	public function __construct($zipfile)
	{
		$this->_archive = new ZipArchive;
		$this->_archive->open($zipfile, ZipArchive::CREATE);
	}
	
	public function get($name)
	{
		return $this->_archive->getFromName($name);
	}
	
	public function add($file)
	{
		$name = $this->_makeName();
		
		$this->_archive->addFile($file, $name);
		return $name;
	}
}