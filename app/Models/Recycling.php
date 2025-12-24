<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Carbon\Carbon;

class Recycling extends Model
{
	use HasFactory;

	public $fillable = [
		'user_id',
		'bin_id',
		'bin_type_id',
		'reward',
		'photo',
	];

	public $appends = [
		'photo_url',
		'formatted_created_at'
	];

	protected $casts = [
		'reward' => 'integer'
	];

	public function getFormattedCreatedAtAttribute()
	{
		if ($this->created_at) {
			return $this->created_at->format('d M Y h:i A');
		}
		return null;
	}

	public function getPhotoUrlAttribute()
	{
		if ($this->photo) {
			return asset('storage/photos/recycling/' . $this->photo);
		}
		return null;
	}

	public function bin()
	{
		return $this->belongsTo(Bin::class);
	}

	public function binType()
	{
		return $this->belongsTo(BinType::class);
	}

	public function user()
	{
		return $this->belongsTo(User::class);
	}

	public function events()
    {
        return $this->belongsToMany(Event::class, 'event_recycling_logs', 'recycling_id', 'event_id');
    }
}
