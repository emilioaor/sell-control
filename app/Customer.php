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

    public function __construct(array $attributes = [])
    {
        if (Auth::check() && Auth::user()->isSeller()) {
            $attributes['seller_id'] = Auth::user()->id;
        }

        parent::__construct($attributes);
    }

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
     * Country
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function country()
    {
        return $this->belongsTo(Country::class);
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

    /**
     * Set store
     *
     * @param array $data
     */
    public function setStore(Array $data)
    {
        if ($this->store) {
            $store = $this->store;
            $store->fill($data);
        } else {
            $store = new Store($data);
            $store->customer_id = $this->id;
        }
        $store->save();

        $store->cities()->sync([]);
        foreach ($data['locations'] as $location) {
            if (isset($location['city']['id']) && $location['city']['id']) {
                $store->cities()->attach($location['city']['id']);
            }
        }
    }

    /**
     * Set wholesaler
     *
     * @param array $data
     */
    public function setWholesaler(array $data)
    {
        if ($this->wholesaler) {
            $wholesaler = $this->wholesaler;
            $wholesaler->fill($data);
        } else {
            $wholesaler = new Wholesaler($data);
            $wholesaler->customer_id = $this->id;
        }
        $wholesaler->save();

        if ($data['allProvinces'] && isset($data['country']['id']) && $data['country']['id']) {
            $provinceIds = Province::query()->where('country_id', $data['country']['id'])->pluck('id');
        } else {
            $provinceIds = array_column($data['provinces'], 'id');
        }

        $wholesaler->provinces()->sync($provinceIds);

        $phoneTypes = [];
        foreach ($data['phone_types'] as $phoneType) {
            $phoneTypes[$phoneType['id']] = ['qty' => $phoneType['qty']];
        }

        $wholesaler->phoneTypes()->sync($phoneTypes);

        $phoneBrands = [];
        foreach ($data['phone_brands'] as $phoneBrand) {
            $phoneBrands[$phoneBrand['id']] = ['qty' => $phoneBrand['qty']];
        }

        $wholesaler->phoneBrands()->sync($phoneBrands);
    }
}
