<?php

namespace App;

use App\Contract\SearchTrait;
use Illuminate\Database\Eloquent\Model;

class City extends Model
{
    use SearchTrait;

    protected $fillable = ['name', 'iso', 'province_id'];

    protected $search_fields = [
        'name'
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
}
