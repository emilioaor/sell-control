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
}
