<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Voucher extends Model
{
	const STATUS_ACTIVE = 1;
	const STATUS_REDEEMED = 2;
	
	public $fillable = [
		'reward_id',
		'user_id',
		'code',
		'status'
	];

	protected $appends = [
		'status_text',
	];

	public function getStatusTextAttribute()
	{
		return $this->status == 1 ? 'Available' : 'Redeemed';
	}

	public function user()
	{
	    return $this->hasOneThrough(User::class, UserReward::class, 'voucher_id', 'id', 'id', 'user_id');
	}

	public function userReward()
	{
	    return $this->hasOne(UserReward::class);
	}

	public function reward()
	{
		return $this->belongsTo(Reward::class);
	}
}
