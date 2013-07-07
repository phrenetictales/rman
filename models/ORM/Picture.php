<?php

namespace RMAN\Models\ORM;


class Picture extends Base
{
	public $timestamps = false;
	protected $guarded = array('id');
	
	public function artist()
	{
		return $this->hasOne('RMAN\Models\ORM\Artist');
	}
	
	public function url()
	{
		return '/pictures/display/'.$this->storename;
	}
}
