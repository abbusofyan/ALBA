<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class School extends Model
{
	public $fillable = [
		'name',
		'email',
		'contact',
		'address',
		'postal_code',
		'status'
	];

	protected $appends = [
		'status_text'
	];

	protected $casts = [
	    'status' => 'boolean',
	];

	public function getStatusTextAttribute() {
		return $this->status == 1 ? 'Active' : 'Inactive';
	}

}
