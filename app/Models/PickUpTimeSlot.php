<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PickUpTimeSlot extends Model
{
    public $appends = [
		'start_time_formatted',
		'end_time_formatted'
	];

	public function getStartTimeFormattedAttribute() {
		return date("h:i A", strtotime($this->start_time));
	}

	public function getEndTimeFormattedAttribute() {
		return date("h:i A", strtotime($this->end_time));
	}
}
