<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Province extends Model
{
    protected $fillable = ['name', 'country_id'];

    /**
     * Country
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    /**
     * Cities
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function cities()
    {
        return $this->hasMany(City::class);
    }

    /**
     * Wholesalers
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function wholesalers()
    {
        return $this->belongsToMany(Wholesaler::class);
    }
}
