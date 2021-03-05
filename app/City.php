<?php

namespace App;

use App\Contract\SearchTrait;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class City extends Model
{
    use SearchTrait;

    protected $fillable = ['name', 'iso', 'province_id'];

    protected $search_fields = [
        'cities.name',
        'provinces.name',
        'countries.name'
    ];

    /**
     * Province
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function province()
    {
        return $this->belongsTo(Province::class);
    }

    /**
     * Stores
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function stores()
    {
        return $this->belongsToMany(Store::class);
    }

    /**
     * Search
     *
     * @param Builder $query
     * @param null|string $search
     * @return Builder
     */
    public function scopeSearch(Builder $query, ?string $search): Builder
    {
        $query
            ->select(['cities.*'])
            ->join('provinces', 'provinces.id', '=', 'cities.province_id')
            ->join('countries', 'countries.id', '=', 'provinces.country_id');

        return $this->_search($query, $search);
    }
}
