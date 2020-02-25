<?php

namespace App\Models;

use App\Http\Resources\Admin\Client\ClientLogResource;
use App\Http\Resources\Collection;
use App\Scopes\OrderScope;
use App\Services\Identifier;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Tymon\JWTAuth\Contracts\JWTSubject;

/**
 * App\Models\Client
 *
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Client newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Client newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Client query()
 * @mixin \Eloquent
 * @property-read \App\Models\PasswordReset $passwordReset
 * @property-read \App\Models\User $user
 * @property-read \App\Models\UserVerification $userVerification
 * @property-read \App\Models\ClientLoyaltyProgram $clientLoyaltyProgram
 * @property-read mixed $full_name
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\LoyaltyProgram[] $loyaltyProgram
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Offer[] $offers
 * @property-read mixed $offer_status
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Device[] $devices
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\ClientShop[] $clientShops
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Log[] $logs
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Pass[] $passes
 * @property-read mixed $races
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Transaction[] $transactions
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Invite[] $invites
 * @property-read mixed $currency
 * @property-read mixed $app_url
 * @property-read mixed $points
 * @property-read mixed $transaction
 * @property-read mixed $transaction_total
 */
class Client extends Authenticatable implements JWTSubject
{
    protected $fillable = [
        'user_id',
        'phone',
        'email',
        'password',
        'first_name',
        'last_name',
        'address',
        'timezone',
        'code',
        'social',
        'block',
        'race',
        'lifetime_value',
        'birthday',
        'country_code',
        'device_type'
    ];

    protected $appends = [
        'offer_status',
        'logs',
        'races',
        'currency',
        'app_url',
        'transaction_total',
        'points',
        'transaction'
    ];

    protected $hidden = ['password'];

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }

    public function userVerification()
    {
        return $this->morphOne(UserVerification::class, 'verifiable');
    }

    public function passwordReset()
    {
        return $this->morphOne(PasswordReset::class, 'resetable');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function loyaltyProgram()
    {
        return $this->belongsToMany(LoyaltyProgram::class, 'client_loyalty_programs');
    }

    public function clientLoyaltyProgram()
    {
        return $this->hasOne(ClientLoyaltyProgram::class);
    }

    public function offers()
    {
        return $this->belongsToMany(Offer::class, 'client_offers');
    }

    public function passes()
    {
        return $this->belongsToMany(Pass::class);
    }

    public function getFullNameAttribute()
    {
        return ucfirst($this->first_name) . ' ' . ucfirst($this->last_name);
    }

    public function getOfferStatusAttribute()
    {
        return $this->offers()->wherePivot('used', 0)->first();
    }

    public function devices()
    {
        return $this->morphMany(Device::class, 'devicable');
    }

    public function logs()
    {
        return $this->morphMany(Log::class, 'logable');
    }

    public function getLogsAttribute()
    {
        return new Collection(ClientLogResource::collection($this->logs()->get()));
    }

    public function clientShops()
    {
        return $this->hasMany(ClientShop::class);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public function getRacesAttribute()
    {
        return Race::all();
    }

    public function invites()
    {
        return $this->hasMany(Invite::class, 'email', 'email');
    }

    public function getCurrencyAttribute()
    {
        return $this->loyaltyProgram()->first()->currency;
    }

    public function getAppUrlAttribute()
    {
        return $this->user()->first()->app_url;
    }

    public function getTransactionTotalAttribute()
    {
        return $this->clientShops()->sum('amount');
    }

    public function getPointsAttribute()
    {
        return $this->clientLoyaltyProgram()->sum('point');
    }

    public function getTransactionAttribute()
    {
        return $this->clientShops()->max('created_at');
    }

    public function clientGroups()
    {
        return $this->belongsToMany(ClientGroup::class, 'client_has_groups');
    }

    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope(new OrderScope);
//        self::created(function ($model) {
//            /** @var self $model */
//            $model->loyaltyProgram()->attach([$model->user->loyaltyProgram->id],
//                ['client_loyalty_id' => \Identifier::generate(Identifier::LOUALTY, $model->id, $model->user->loyaltyProgram->id)]);
////            $model->offers()->sync($model->user->loyaltyProgram->offers()->pluck('id'));
//        });
    }
}
