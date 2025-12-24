<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    protected $guarded = [];

	public function option() {
		return $this->belongsTo(ServiceOption::class, 'service_option_id');
	}
}
