<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Carbon\Carbon;

class Bin extends Model
{
	use HasFactory;

	const STATUS_ACTIVE = 1;
	const STATUS_INACTIVE = 0;
	const SHOWN = 1;
	const HIDDEN = 0;

	protected $fillable = [
		'code',
		'organization_id',
		'bin_type_id',
		'e_waste_bin_type_id',
		'address',
		'postal_code',
		'lat',
		'long',
		'status',
		'visibility',
		'map_radius',
		'qrcode',
		'remark'
	];

	protected $appends = [
		'status_text',
		'google_maps_url',
		'apple_maps_url',
		'point',
		'qrcode_content'
	];

	protected $casts = [
		'status' => 'boolean',
		'visibility' => 'boolean',
	];

	public function getQrcodeContentAttribute() {
	    if ($this->qrcode) {
			return $this->qrcode;
	    }
		return $this->code;
	}

	public function getMapRadiusAttribute($value)
    {
		return (int)preg_replace('/[^0-9.]/', '', $value);
    }

	public function getPointAttribute()
	{
		$today = Carbon::today();

		$eventBin = $this->eventBins()
			->whereHas('event', function ($query) use ($today) {
				$query->whereDate('date_start', '<=', $today)
					->whereDate('date_end', '>=', $today);
			})->first();

		if ($eventBin) {
			return $eventBin->point;
		}

		$eventBinType = BinType::find($this->bin_type_id)->eventBins()
			->whereHas('event', function ($query) use ($today) {
				$query->whereDate('date_start', '<=', $today)
					->whereDate('date_end', '>=', $today);
			})->first();

		if ($eventBinType) {
			return $eventBinType->point;
		}

		return $this->type?->point;
	}

	public static function generateCode(): string
	{
	    do {
	        $code = str_pad(random_int(0, 99999999), 8, '0', STR_PAD_LEFT);
	    } while (Bin::where('code', $code)->exists());

	    return $code;
	}

	public function getGoogleMapsUrlAttribute()
	{
		return 'https://maps.google.com/?q=' . $this->lat . ',' . $this->long;
	}

	public function getAppleMapsUrlAttribute()
	{
		return 'https://maps.apple.com/directions?destination=' . $this->lat . ',' . $this->long;
	}

	// public function getCodeAttribute()
	// {
	// 	return str_pad((string) ($this->id), 8, '0', STR_PAD_LEFT);
	// }

	public function getStatusTextAttribute()
	{
		return $this->status == 1 ? 'Active' : 'Inactive';
	}

	public function type()
	{
		return $this->belongsTo(BinType::class, 'bin_type_id');
	}

	public function EWasteBinType()
	{
		return $this->belongsTo(EWasteBinType::class);
	}

	public function events()
	{
		return $this->belongsToMany(Event::class, 'event_bins');
	}

	public function eventBins()
	{
		return $this->hasMany(EventBin::class);
	}

	public function ongoingEvents()
	{
		return $this->belongsToMany(Event::class, 'event_bins', 'bin_id', 'event_id')
			->whereDate('date_start', '<=', now())
			->whereDate('date_end', '>=', now());
	}

	public function organization()
    {
        return $this->belongsTo(User::class, 'organization_id');
    }

	public function recyclings() {
		return $this->hasMany(Recycling::class);
	}
}
