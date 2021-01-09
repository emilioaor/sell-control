<?php

namespace App;

use App\Contract\UuidGeneratorTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PhoneType extends Model
{
    use SoftDeletes;
    use UuidGeneratorTrait;

    protected $fillable = ['name'];

    /**
     * Wholesalers
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function wholesalers()
    {
        return $this->belongsToMany(Wholesaler::class)->withPivot(['qty']);
    }
}
