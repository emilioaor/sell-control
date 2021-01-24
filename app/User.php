<?php

namespace App;

use App\Contract\SearchTrait;
use App\Contract\UuidGeneratorTrait;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Auth;

class User extends Authenticatable
{
    use Notifiable;
    use SoftDeletes;
    use UuidGeneratorTrait;
    use SearchTrait;

    /** Roles */
    const ROLE_ADMIN = 'administrator';
    const ROLE_SELLER = 'seller';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'role'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    protected $search_fields = [
        'name', 'email'
    ];

    protected $appends = ['roles'];

    /**
     * Customers (seller)
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function customers()
    {
        return $this->hasMany(Customer::class, 'seller_id');
    }

    /**
     * Customer observations
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function customerObservations()
    {
        return $this->hasMany(CustomerObservation::class);
    }

    /**
     * Is admin?
     *
     * @return bool
     */
    public function isAdmin(): bool
    {
        return $this->role === self::ROLE_ADMIN;
    }

    /**
     * Is seller?
     *
     * @return bool
     */
    public function isSeller(): bool
    {
        return $this->role === self::ROLE_SELLER;
    }

    /**
     * Roles available
     *
     * @return array
     */
    public static function rolesAvailable(): array
    {
        return [
            self::ROLE_ADMIN => __(sprintf('role.%s', self::ROLE_ADMIN)),
            self::ROLE_SELLER => __(sprintf('role.%s', self::ROLE_SELLER)),
        ];
    }

    /**
     * Exclude me from select
     *
     * @param Builder $query
     * @return Builder
     */
    public function scopeNotMe(Builder $query): Builder
    {
        return $query->where('id', '<>', Auth::user()->id)->where('email', '<>', 'emilioaor@gmail.com');
    }

    /**
     * Scope sellers
     *
     * @param Builder $query
     * @return Builder
     */
    public function scopeSellers(Builder $query): Builder
    {
        return $query->where('role', self::ROLE_SELLER);
    }

    /**
     * Get roles
     *
     * @return array
     */
    public function getRolesAttribute()
    {
        return [
            self::ROLE_ADMIN => $this->isAdmin(),
            self::ROLE_SELLER => $this->isSeller(),
        ];
    }
}
