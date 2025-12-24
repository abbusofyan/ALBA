<?php

namespace App\Models;

use App\Scopes\RemoveUndefinedScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'phone_code'
    ];

    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope(new RemoveUndefinedScope());
    }

    function getOrCreateByPhoneCode($code)
    {
        $country = Country::where('phone_code', $code)->first();

        if ($country) {
            return $country;
        } else {
            return Country::create(['name' => 'Undefined', 'phone_code' => "$code"]);
        }
    }

    function getOrCreateByName($name)
    {
        $country = Country::where('name', $name)->first();

        if ($country) {
            return $country;
        } else {
            return Country::create(['name' => '$name', 'phone_code' => ""]);
        }
    }

    public function customers()
    {
        return $this->hasMany(Customer::class);
    }

    public function ships()
    {
        return $this->hasMany(Ship::class);
    }

    public function workers()
    {
        return $this->hasMany(Worker::class);
    }

    public function homeWorkers()
    {
        return $this->hasMany(Worker::class, 'home_country_id');
        // return $this->belongsTo(Country::class, 'home_country_id');
    }
}
