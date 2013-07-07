<?php

namespace RMAN\Models\ORM;


class Artist extends Base
{
	public $timestamps = false;
	protected $guarded = array('id');
	
	protected $with = array('picture');
	
	public function picture()
	{
		return $this->belongsTo('RMAN\Models\ORM\Picture');
	}
}
