<?php

namespace App\Models;

use App\Scopes\OrderScope;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Tymon\JWTAuth\Contracts\JWTSubject;

/**
 * App\Models\AreaManager
 *
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Shop[] $shops
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\AreaManager newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\AreaManager newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\AreaManager query()
 * @mixin \Eloquent
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Log[] $logs
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Invite[] $invites
 * @property-read \App\Models\PasswordReset $passwordReset
 * @property-write mixed $password
 */
class AreaManager extends Authenticatable implements JWTSubject
{
    protected $fillable = ['user_id', 'name', 'password', 'email'];

    protected $appends = ['shops'];

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

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function shops()
    {
        return $this->belongsToMany(Shop::class, 'area_managers_shops');
    }

    public function getShopsAttribute()
    {
        return $this->shops()->get();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function logs()
    {
        return $this->morphMany(Log::class, 'logable');
    }

    public function invites()
    {
        return $this->hasMany(Invite::class);
    }

    public function passwordReset()
    {
        return $this->morphOne(PasswordReset::class, 'resetable');
    }

    public function setPasswordAttribute($password)
    {
        $this->attributes['password'] = \Hash::make($password);
    }

    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope(new OrderScope);

//        self::creating(function ($model) {
//            $model->user_id = auth()->user()->id;
//        });

        self::created(function ($model) {
            /** @var self $model */
            $model->shops()->sync(request()->input('ids'));
        });

//        self::updated(function ($model) {
//            /** @var self $model */
//            $model->shops()->sync(request()->input('ids'));
//        });
    }
}
