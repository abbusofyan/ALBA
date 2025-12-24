<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EventWasteType extends Model
{
    public $fillable = [
		'event_id',
		'waste_type_id',
		'price'
	];

	public function wasteType() {
		return $this->belongsTo(WasteType::class);
	}

	protected $hidden = ['created_at', 'updated_at'];
	
}
