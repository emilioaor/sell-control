<?php

namespace App;

use App\Contract\UuidGeneratorTrait;
use Illuminate\Database\Eloquent\Model;

class CustomerReminder extends Model
{
    use UuidGeneratorTrait;

    protected $fillable = ['customer_id', 'date', 'subject'];

    protected $dates = ['date'];

    /**
     * Customer
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function customer()
    {
        return $this->belongsTo(Customer::class)->withTrashed();
    }
}
