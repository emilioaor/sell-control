<?php

namespace App;

use App\Contract\UuidGeneratorTrait;
use Illuminate\Database\Eloquent\Model;

class CustomerStatusHistory extends Model
{
    use UuidGeneratorTrait;

    protected $fillable = ['customer_id', 'user_id', 'status'];

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
     * User
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class)->withTrashed();
    }
}
