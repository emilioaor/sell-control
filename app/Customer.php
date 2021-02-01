<?php

namespace App;

use App\Contract\SearchTrait;
use App\Contract\UuidGeneratorTrait;
use Carbon\Carbon;
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

    protected $fillable = ['name', 'email', 'phone', 'country_id', 'status', 'seller_id', 'contact_name'];

    protected $search_fields = [
        'name', 'email', 'phone', 'status',
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
     * Customer observations
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function customerObservations()
    {
        return $this->hasMany(CustomerObservation::class)->orderBy('id', 'DESC');
    }

    /**
     * Customer status histories
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function customerStatusHistories()
    {
        return $this->hasMany(CustomerStatusHistory::class)->orderBy('id', 'DESC');
    }

    /**
     * Customer reminders
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function customerReminders()
    {
        return $this->hasMany(CustomerReminder::class)
            ->where('date', '>=', (Carbon::now()->modify('-1 day')))
            ->orderBy('date', 'DESC')
        ;
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

    /**
     * Add customer observation
     *
     * @param string $observation
     */
    public function addCustomerObservation(string $observation)
    {
        $customerObservation = new CustomerObservation();
        $customerObservation->customer_id = $this->id;
        $customerObservation->observation = $observation;
        $customerObservation->user_id = Auth::user()->id;
        $customerObservation->save();
    }

    /**
     * Save change status history
     */
    public function changeStatus()
    {
        $lastStatus = CustomerStatusHistory::query()->where('customer_id', $this->id)->orderBy('id', 'DESC')->first();

        if (! $lastStatus || $lastStatus->status !== $this->status) {
            $customerStatusHistory = new CustomerStatusHistory();
            $customerStatusHistory->customer_id = $this->id;
            $customerStatusHistory->user_id = Auth::user()->id;
            $customerStatusHistory->status = $this->status;
            $customerStatusHistory->save();
        }
    }

    /**
     * Add customer reminder
     *
     * @param array $data
     */
    public function addCustomerReminder(array $data)
    {
        $customerReminder = new CustomerReminder();
        $customerReminder->customer_id = $this->id;
        $customerReminder->subject = $data['subject'];
        $customerReminder->date = $data['date'];
        $customerReminder->save();
    }

    /**
     * Scope report
     *
     * @param Builder $query
     * @param array $data
     * @return Builder
     * @throws \Exception
     */
    public function scopeReport(Builder $query, array $data)
    {
        $start = new Carbon($data['start']);
        $end = new Carbon($data['end']);
        $sellers = $data['sellers'];

        $query
            ->select(['customers.*'])
            ->join('customer_status_histories', 'customer_status_histories.customer_id', '=', 'customers.id')
            ->whereBetween('customer_status_histories.created_at', [$start, $end])
            ->distinct()
            ->with(['customerStatusHistories.user', 'seller'])
            ->orderBy('seller_id')
            ->orderBy('customers.id')
        ;

        if ($sellers) {
            $query->whereIn('seller_id', array_column($sellers, 'id'));
        }

        return $query;
    }
}
