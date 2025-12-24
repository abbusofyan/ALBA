<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Reward extends Model
{
	use SoftDeletes;

	use HasFactory;

	CONST STATUS_ACTIVE = 1;
	CONST STATUS_INACTIVE = 0;

	public $fillable = [
		'code',
		'name',
		'price',
		'description',
		'tnc',
		'label',
		'image',
		'status',
		'event_id',
		'start_date',
		'end_date',
		'start_time',
		'end_time'
	];

	protected $casts = [
		'status' => 'boolean',
		'price' => 'integer'
	];

	protected $appends = [
		'status_text',
		'image_url',
		'remaining_vouchers',
		'is_expired'
	];

	public function getImageUrlAttribute()
	{
		if ($this->image) {
			return asset('storage/images/reward/' . $this->image);
		}
		return null;
	}

	public function getStatusTextAttribute()
	{
		return $this->status == 1 ? 'Active' : 'Inactive';
	}

	public static function generateCode()
	{
		$prefix = 'RWD';

		$latest = self::where('code', 'like', $prefix . '%')
			->orderByDesc('id')
			->first();

		$lastNumber = 0;
		if ($latest && preg_match('/^' . $prefix . '(\d{6})$/', $latest->code, $matches)) {
			$lastNumber = (int) $matches[1];
		}

		$newNumber = $lastNumber + 1;
		$suffix = str_pad($newNumber, 6, '0', STR_PAD_LEFT);

		return $prefix . $suffix;
	}

	public function vouchers()
	{
		return $this->hasMany(Voucher::class);
	}

	public function getRemainingVouchersAttribute()
	{
		return $this->vouchers()->where('status', '!=', Voucher::STATUS_REDEEMED)->count();
	}

	public function getIsExpiredAttribute()
	{
		if ($this->expired_date === null) {
			return false;
		}
		return $this->expired_date < now();
	}

	public function scopeActive($query)
	{
		return $query->where(function ($q) {
			$q->whereNull('expired_date')
				->orWhere('expired_date', '>=', now());
		});
	}

	public function scopeExpired($query)
	{
		return $query->where(function ($q) {
			$q->whereNotNull('expired_date')
				->where('expired_date', '<', now());
		});
	}

	public function event() {
		return $this->belongsTo(Event::class);
	}
}
