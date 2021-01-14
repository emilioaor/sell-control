<?php

namespace App;

use App\Contract\UuidGeneratorTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Wholesaler extends Model
{
    use SoftDeletes;
    use UuidGeneratorTrait;

    protected $fillable = ['customer_id', 'qty', 'office_sellers', 'street_sellers', 'customers_qty'];

    /**
     * Customer
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function customer()
    {
        return $this->belongsTo(Customer::class)->withTrashed();
    }

    /**
     * Provinces
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function provinces()
    {
        return $this->belongsToMany(Province::class, 'wholesaler_province');
    }

    /**
     * Phone types
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function phoneTypes()
    {
        return $this->belongsToMany(PhoneType::class, 'wholesaler_phone_type')->withPivot(['qty']);
    }

    /**
     * Phone brands
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function phoneBrands()
    {
        return $this->belongsToMany(PhoneBrand::class, 'wholesaler_phone_brand')->withPivot(['qty']);
    }
}
