<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RVMTransaction extends Model
{
	public $table = 'rvm_transactions';

    public $fillable = [
		'bin_id',
		'rvm_type',
		'qrcode',
		'data',
	];
}
