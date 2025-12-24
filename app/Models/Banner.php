<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Banner extends Model
{
	use SoftDeletes;

    public $fillable = [
		'image',
		'placement_id',
		'url',
		'status'
	];

	protected $appends = [
		'image_url',
		'status_text'
	];
	public $casts = [
		'status' => 'boolean'
	];

	public function placement() {
		return $this->belongsTo(BannerPlacement::class, 'placement_id');
	}

	public function getImageUrlAttribute()
	{
		if ($this->image) {
			return asset('storage/images/banners/' . $this->image);
		}
		return null;
	}

	public function getStatusTextAttribute()
	{
		return $this->status == 1 ? 'Active' : 'Inactive';
	}
}
