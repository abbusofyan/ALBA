<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WasteType extends Model
{
    protected $fillable = ['name'];

	protected $hidden = ['created_at', 'updated_at'];

	public function bins() {
		return $this->belongsToMany(Bin::class, 'bin_waste_types');
	}

	public function events()
	{
		return $this->belongsToMany(Event::class, 'event_waste_types');
	}
}
