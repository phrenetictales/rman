<?php

namespace RMAN\Models\ORM;


class Release extends Base
{
	public $timestamps = false;
	protected $guarded = array('id');
	
	protected $with = array('picture', 'tracks', 'tracks.artists');
	
	public function picture()
	{
		return $this->belongsTo('RMAN\Models\ORM\Picture');
	}
	
	public function tracks()
	{
		return $this->hasMany('RMAN\Models\ORM\Track');
	}
}
