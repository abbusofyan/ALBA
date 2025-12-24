<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PickUpOrder extends Model
{
    protected $fillable = [
		'user_id',
		'waste_type_id',
		'min_weight',
		'max_weight',
		'quantity',
		'e_waste_description',
		'photo',
		'remark',
		'pickup_date',
		'pickup_start_time',
		'pickup_end_time',
		'address'
	];

	public $appends = [
		'photo_url'
	];

	public function getPhotoUrlAttribute()
	{
		if ($this->photo) {
			return asset('storage/images/pickup_orders/' . $this->photo);
		}
		return null;
	}

	public function wasteType() {
		return $this->belongsTo(PickUpWasteType::class, 'waste_type_id');
	}
}
