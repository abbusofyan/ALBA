<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BannedList extends Model
{
    public $fillable = [
		'user_id',
		'reason',
		'evidence_filename',
		'duration_days',
		'moderator'
	];

	public $appends = [
		'expired_at'
	];

	public function getExpiredAtAttribute() {
		if ($this->duration) {
			return now()->addDays($this->duration_days);
		}
		return null;
	}


}
