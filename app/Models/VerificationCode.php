<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VerificationCode extends Model
{
    public $fillable = ['phone', 'code', 'expires_at'];
}
