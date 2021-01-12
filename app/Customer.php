<?php

namespace App;

use App\Contract\SearchTrait;
use App\Contract\UuidGeneratorTrait;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class Customer extends Model
{
    use SoftDeletes;
    use UuidGeneratorTrait;
    use SearchTrait;

    /** Status */
    const STATUS_CONTACT = 'contact';
    const STATUS_PROSPECT = 'prospect';
    const STATUS_ACTIVE = 'active';

    protected $fillable = ['name', 'email', 'phone', 'country_id', 'status', 'seller_id'];

    protected $search_fields = [
        'name', 'email', 'phone'
    ];

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

    /**
     * Status available
     *
     * @return array
     */
    public static function statusAvailable()
    {
        return [
            self::STATUS_CONTACT => __('status.' . self::STATUS_CONTACT),
            self::STATUS_PROSPECT => __('status.' . self::STATUS_PROSPECT),
            self::STATUS_ACTIVE => __('status.' . self::STATUS_ACTIVE),
        ];
    }

    /**
     * Assigned customers
     *
     * @param Builder $query
     * @return Builder
     */
    public function scopeAssigned(Builder $query): Builder
    {
        if (! Auth::user()->isAdmin()) {
            $query->where('seller_id', Auth::user()->id);
        }

        return $query;
    }
}
