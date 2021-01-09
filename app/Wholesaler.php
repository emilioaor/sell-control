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
        return $this->belongsToMany(Province::class);
    }

    /**
     * Phone types
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function phoneTypes()
    {
        return $this->belongsToMany(PhoneType::class)->withPivot(['qty']);
    }

    /**
     * Phone brands
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function phoneBrands()
    {
        return $this->belongsToMany(PhoneBrand::class)->withPivot(['qty']);
    }
}
