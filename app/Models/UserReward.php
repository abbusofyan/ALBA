<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserReward extends Model
{
    protected $fillable = [
        'user_id',
        'reward_id',
        'voucher_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function reward()
    {
        return $this->belongsTo(Reward::class);
    }

    public function voucher()
    {
        return $this->belongsTo(Voucher::class);
    }
}
