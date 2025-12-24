<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    public $fillable = [
		'user_max_daily_reward',
		'user_max_monthly_redemption'
	];

	public $casts = [
		'user_max_daily_reward' => 'integer',
		'user_max_monthly_redemption' => 'integer',
	];
}
