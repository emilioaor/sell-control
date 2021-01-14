<?php

namespace App;

use App\Contract\SearchTrait;
use Illuminate\Database\Eloquent\Model;

class Province extends Model
{
    use SearchTrait;

    protected $fillable = ['name', 'country_id'];

    protected $search_fields = [
        'name'
    ];

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
