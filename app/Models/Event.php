<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Carbon\Carbon;

class Event extends Model
{
	use HasFactory;

	const STATUS_ACTIVE = 1;

	protected $fillable = [
		'code',
		'secret_code',
		'event_type_id',
		'district_id',
		'user_id',
		'name',
		'address',
		'postal_code',
		'lat',
		'long',
		'date_start',
		'date_end',
		'time_start',
		'time_end',
		'description',
		'image',
		'status',
		'use_all_bins'
	];

	public $appends = [
		'status_text',
		'image_url',
		'time_start_formatted',
		'time_end_formatted',
		'distance',
		'needs_join'
	];

	protected $casts = [
		'status' => 'boolean',
		'use_all_bins' => 'boolean',
	];

	public function getNeedsJoinAttribute() {
		$eventTypeIds = EventType::whereIn('name', EventType::NEEDS_JOIN)->get()->pluck('id')->toArray();
		if (in_array($this->event_type_id, $eventTypeIds)) {
			return true;
		}
		return false;
	}

	public static function generateCode($eventTypeId)
	{
		$prefixMap = [
			1 => 'EDV',
			2 => 'CFT',
			3 => 'PVT',
			4 => 'AVT',
		];

		$prefix = $prefixMap[$eventTypeId] ?? 'EVT';

		$latestEvent = self::where('code', 'like', $prefix . '%')
			->orderByDesc('id')
			->first();

		$lastNumber = 0;
		if ($latestEvent && preg_match('/^' . $prefix . '(\d{6})$/', $latestEvent->code, $matches)) {
			$lastNumber = (int) $matches[1];
		}

		$newNumber = $lastNumber + 1;
		$suffix = str_pad($newNumber, 6, '0', STR_PAD_LEFT);

		return $prefix . $suffix;
	}

	public function getTimeStartFormattedAttribute()
	{
		return Carbon::createFromFormat('H:i:s', $this->time_start)->format('h:i A');
	}

	public function getTimeEndFormattedAttribute()
	{
		return Carbon::createFromFormat('H:i:s', $this->time_end)->format('h:i A');
	}

	public function getStatusTextAttribute()
	{
		return $this->status == 1 ? 'Active' : 'Inactive';
	}

	public function getImageUrlAttribute()
	{
		if ($this->image) {
			return asset('storage/images/event/' . $this->image);
		}
		return null;
	}

	public function bins()
	{
		return $this->belongsToMany(Bin::class, 'event_bins');
	}

	public function binTypes()
	{
		return $this->belongsToMany(BinType::class, 'event_bins');
	}

	public function wasteTypes()
	{
		return $this->belongsToMany(WasteType::class, 'event_waste_types');
	}

	public function type()
	{
		return $this->belongsTo(EventType::class, 'event_type_id');
	}

	public function eventWasteType()
	{
		return $this->hasMany(EventWasteType::class);
	}

	public function eventBins()
	{
		return $this->hasMany(EventBin::class);
	}

	public function district()
	{
		return $this->belongsTo(District::class);
	}

	public function user()
	{
		return $this->belongsTo(user::class);
	}

	public function participants()
	{
	    return $this->belongsToMany(User::class, 'user_joined_events', 'event_id', 'user_id');
	}

	public function getDistanceAttribute()
	{
		$userLatitude = request()->lat;
		$userLongitude = request()->lng;

		if (!$userLatitude || !$userLongitude || !$this->lat || !$this->long) {
			return null;
		}
		$earthRadius = 6371; // in KM, use 3959 for miles

		$latFrom = deg2rad($userLatitude);
		$lonFrom = deg2rad($userLongitude);
		$latTo = deg2rad($this->lat);
		$lonTo = deg2rad($this->long);

		$latDelta = $latTo - $latFrom;
		$lonDelta = $lonTo - $lonFrom;

		$angle = 2 * asin(sqrt(pow(sin($latDelta / 2), 2) +
			cos($latFrom) * cos($latTo) * pow(sin($lonDelta / 2), 2)));

		return round($earthRadius * $angle, 2); // in KM
	}

	public function recyclings()
    {
        return $this->belongsToMany(Recycling::class, 'event_recycling_logs', 'event_id', 'recycling_id');
    }
}
