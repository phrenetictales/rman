<?php

namespace RMAN\Models\ORM;


class Track extends Base
{
	public $timestamps = false;
	protected $guarded = array('id');
	
	
	public function release()
	{
		return $this->belongsTo('RMAN\Models\ORM\Release');
	}
	
	public function artists()
	{
		return $this->belongsToMany('RMAN\Models\ORM\Artist');
	}
	
	public function fulltitle()
	{
			
		return 
			\O\c(\O\a($this->artists->toArray()))
				->map(function($artist) {
					return $artist['name'];
				})
				->implode(' & ').
			' - '.
			$this->title;
	}
}
