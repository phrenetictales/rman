<?php

namespace RMAN\Models\ORM;

class Base extends \Illuminate\Database\Eloquent\Model
{
	public function getTable()
	{
		if (isset($this->table)) return $this->table;
		
		$ns = \O\c(\O\s(get_class($this)))->explode('\\');
		if ($ns->slice(0, 3)->implode('\\') == 'RMAN\Models\ORM') {
			return snake_case(str_plural((string)$ns->slice(3)
				->implode('_')));
				
		}
	}
}
