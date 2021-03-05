<?php

namespace App;

use App\Contract\SearchTrait;
use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    use SearchTrait;

    protected $fillable = ['name', 'iso'];

    protected $search_fields = [
        'name'
    ];

    /**
     * Provinces
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function provinces()
    {
        return $this->hasMany(Province::class);
    }

    /**
     * Customers
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function customers()
    {
        return $this->hasMany(Customer::class);
    }

    /**
     * Mutator
     *
     * @param $value
     */
    public function setIsoAttribute($value)
    {
        $this->attributes['iso'] = strtoupper($value);
    }
}
