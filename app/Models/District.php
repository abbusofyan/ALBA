<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class District extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'code',
        'name',
        'region',
    ];

	protected $hidden = ['code', 'created_at', 'updated_at'];

    /**
     * Scope a query to only include districts from a specific region.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  string  $region
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByRegion($query, $region)
    {
        return $query->where('region', $region);
    }

    /**
     * Scope a query to search districts by name.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  string  $name
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSearchByName($query, $name)
    {
        return $query->where('name', 'like', '%' . $name . '%');
    }

    /**
     * Scope a query to search districts by code.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  string  $code
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByCode($query, $code)
    {
        return $query->where('code', $code);
    }

    /**
     * Get all available regions.
     *
     * @return array
     */
    public static function getRegions()
    {
        return ['Central', 'East', 'North', 'North-East', 'West'];
    }

    /**
     * Get districts grouped by region.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getDistrictsByRegion()
    {
        return self::orderBy('region')->orderBy('name')->get()->groupBy('region');
    }

    /**
     * Get region statistics.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getRegionStatistics()
    {
        return self::selectRaw('
            region,
            COUNT(*) as district_count
        ')
        ->groupBy('region')
        ->orderBy('region')
        ->get();
    }

    /**
     * Get districts by region with count.
     *
     * @return array
     */
    public static function getRegionCounts()
    {
        return self::groupBy('region')
            ->selectRaw('region, count(*) as count')
            ->pluck('count', 'region')
            ->toArray();
    }

    /**
     * Find district by code.
     *
     * @param string $code
     * @return SingaporeDistrict|null
     */
    public static function findByCode($code)
    {
        return self::where('code', $code)->first();
    }

    /**
     * Get all districts in alphabetical order.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getAllAlphabetical()
    {
        return self::orderBy('name')->get();
    }

    /**
     * Get districts for a specific region in alphabetical order.
     *
     * @param string $region
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getByRegionAlphabetical($region)
    {
        return self::where('region', $region)->orderBy('name')->get();
    }
}
