<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EventBin extends Model
{
    public $fillable = [
		'event_id',
		'bin_id',
		'bin_type_id',
		'point'
	];

	protected $hidden = ['created_at', 'updated_at'];

	public $casts = [
		'point' => 'integer'
	];

	public function bin()
	{
	    return $this->belongsTo(Bin::class);
	}

	public function binType()
	{
		return $this->belongsTo(BinType::class);
	}

	public function event()
	{
	    return $this->belongsTo(Event::class);
	}
}
