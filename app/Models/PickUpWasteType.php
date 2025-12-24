<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PickUpWasteType extends Model
{
    const GENERAL_PAPER = 1;
	const CONFIDENTIAL_PAPER = 2;
	const E_WASTE = 3;
}
