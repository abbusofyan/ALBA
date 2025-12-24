<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    public $appends = [
		'module_name'
	];

	public function getModuleNameAttribute() {
		return ucwords(str_replace('-', ' ', $this->module));
	}
}
