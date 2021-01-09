<?php

namespace App;

use App\Contract\UuidGeneratorTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Customer extends Model
{
    use SoftDeletes;
    use UuidGeneratorTrait;

    /** Status */
    const STATUS_CONTACT = 'contact';
    const STATUS_PROSPECT = 'prospect';
    const STATUS_ACTIVE = 'active';

    protected $fillable = ['name', 'email', 'phone', 'country_id', 'status'];

    /**
     * Store info
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function store()
    {
        return $this->hasOne(Store::class);
    }

    /**
     * Wholesaler
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function wholesaler()
    {
        return $this->hasOne(Wholesaler::class);
    }

    /**
     * Seller
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function seller()
    {
        return $this->belongsTo(User::class, 'seller_id')->withTrashed();
    }

    /**
     * Distributors
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function distributors()
    {
        return $this->hasMany(Distributor::class);
    }
}
