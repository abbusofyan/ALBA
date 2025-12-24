<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BannerPlacement extends Model
{
	public $fillable = [
		'name',
		'status'
	];

	protected $appends = [
		'status_text'
	];

	protected $casts = [
		'status' => 'boolean'
	];

	public function getStatusTextAttribute()
	{
		return $this->status == 1 ? 'Active' : 'Inactive';
	}

	public function banners()
	{
	    return $this->hasMany(Banner::class, 'placement_id')->whereNull('deleted_at');
	}

	public function banner() {
		return $this->hasOne(Banner::class, 'placement_id')->whereNull('deleted_at')->latestOfMany();
	}

}
