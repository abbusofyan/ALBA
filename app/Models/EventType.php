<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class EventType extends Model
{
	use HasFactory;
	protected $hidden = ['created_at', 'updated_at'];
	protected $fillable = [
		'name',
	];

	const NEEDS_JOIN = [
		'Private Event',
		'ALBA Event'
	];
}
