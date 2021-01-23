<?php

namespace App;

use App\Contract\UuidGeneratorTrait;
use Illuminate\Database\Eloquent\Model;

class CustomerObservation extends Model
{
    use UuidGeneratorTrait;

    protected $fillable = ['observation', 'customer_id'];

    /**
     * Customer
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
}
