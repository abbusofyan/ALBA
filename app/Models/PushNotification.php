<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PushNotification extends Model
{
	const STATUS_PENDING = 1;
	const STATUS_SENT = 2;
	const STATUS_FAILED = 3;
	const STATUS_TEXT = [
		'1' => 'Pending',
		'2' => 'Sent',
		'3' => 'Failed'
	];

    public $fillable = [
		'title',
		'body',
		'scheduled_at',
		'status',
		'send_now'
	];

	protected $appends = [
		'status_text'
	];

	public function getStatusTextAttribute()
	{
		if ($this->status) {
			return self::STATUS_TEXT[$this->status];
		}
	}

	public function seenByUsers()
    {
        return $this->hasMany(UserSeenNotification::class, 'notification_id');
    }
}
