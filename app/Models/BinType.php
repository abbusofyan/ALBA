<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class BinType extends Model
{
	use HasFactory;

	public $fillable = [
		'name',
		'fixed_qrcode',
		'image',
		'point',
		'icon'
	];

	protected $hidden = ['created_at', 'updated_at'];

	public $appends = [
		'qrcode_type',
		'image_url',
		'icon_url',
	];

	public $casts = [
		'point' => 'integer'
	];

	public function getImageUrlAttribute()
	{
		if ($this->image) {
			return asset('storage/images/bin-types/' . $this->image);
		}
		return null;
	}

	public function getIconUrlAttribute()
	{
		if ($this->icon) {
			return asset('storage/images/bin-types/icon/' . $this->icon);
		}
		return null;
	}

	public function getQrcodeTypeAttribute()
	{
		if ($this->fixed_qrcode) {
			return 'Fixed QR Code';
		}
		return 'Unique QR Code';
	}

	public function wasteTypes()
	{
		return $this->belongsToMany(WasteType::class, 'bin_type_waste');
	}

	public function bins()
	{
		return $this->hasMany(Bin::class);
	}

	public function eventBins()
	{
		return $this->hasMany(EventBin::class);
	}

	public function ongoingEvents()
	{
		return $this->belongsToMany(Event::class, 'event_bins', 'bin_type_id', 'event_id')
			->whereDate('date_start', '<=', now())
			->whereDate('date_end', '>=', now());
	}
}
