<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class EventRecyclingLog extends Model
{
    use HasFactory;
    public $guarded = [];
    protected $fillable = [
		'recycling_id',
        'user_id',
        'event_id',
    ];
}
