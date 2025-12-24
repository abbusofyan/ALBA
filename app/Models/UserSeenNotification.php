<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserSeenNotification extends Model
{
    public $fillable = [
		'user_id',
		'notification_id',
		'seen_at'
	];

	public function notification()
    {
        return $this->belongsTo(PushNotification::class, 'notification_id');
    }
}
