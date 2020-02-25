<?php

namespace App\Models;

use CodeItNow\BarcodeBundle\Utils\BarcodeGenerator;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Str;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Tymon\JWTAuth\Contracts\JWTSubject;

/**
 * App\Models\User
 *
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection|\Illuminate\Notifications\DatabaseNotification[] $notifications
 * @method static \Illuminate\Database\Eloquent\Builder|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|User query()
 * @mixin \Eloquent
 * @property-read \App\Models\PasswordReset $passwordReset
 * @property-read \App\Models\LoyaltyProgram $loyaltyProgram
 * @method static bool|null forceDelete()
 * @method static \Illuminate\Database\Query\Builder|\App\Models\User onlyTrashed()
 * @method static bool|null restore()
 * @method static \Illuminate\Database\Query\Builder|\App\Models\User withTrashed()
 * @method static \Illuminate\Database\Query\Builder|\App\Models\User withoutTrashed()
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Client[] $clients
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Pass[] $passes
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Shop[] $shops
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\AreaManager[] $areaManagers
 * @property-read \App\Models\UserVerification $userVerification
 * @property-read mixed $qr
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Device[] $devices
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Log[] $logs
 * @property-read string $full_name
 * @property mixed $logo
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Campaign[] $campaigns
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\PosTerminal[] $terminals
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\ClientGroup[] $clientGroups
 */
class User extends Authenticatable implements JWTSubject
{
    use Notifiable;
    use SoftDeletes;

    const ROLE_SUPER_ADMIN = 'SUPER_ADMIN';
    const ROLE_MERCHANT = 'MERCHANT';
    const ROLE_MANAGER = 'MANAGER';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'department',
        'email',
        'password',
        'business',
        'role',
        'first_name',
        'last_name',
        'address',
        'timezone',
        'verified',
        'token',
        'logo',
        'app_url',
        'android_version',
        'ios_version',
    ];

//    protected $appends = ['qr'];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

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

    public function getQrAttribute()
    {
        return 'data:image/png;base64,' . base64_encode(QrCode::format('png')->size(300)->generate($this->id));
    }

    public function getLogoAttribute()
    {
        return $this->attributes['logo'] ? url('storage/' . $this->attributes['logo']) : null;
    }

    public function userVerification()
    {
        return $this->morphOne(UserVerification::class, 'verifiable');
    }

    public function clients()
    {
        return $this->hasMany(Client::class);
    }

    public function passwordReset()
    {
        return $this->morphOne(PasswordReset::class, 'resetable');
    }

    public function loyaltyProgram()
    {
        return $this->hasOne(LoyaltyProgram::class);
    }

    public function passes()
    {
        return $this->hasMany(Pass::class);
    }

    public function shops()
    {
        return $this->hasMany(Shop::class);
    }

    public function areaManagers()
    {
        return $this->hasMany(AreaManager::class);
    }

    public function devices()
    {
        return $this->morphMany(Device::class, 'devicable');
    }

    public function logs()
    {
        return $this->morphMany(Log::class, 'logable');
    }

    public function campaigns()
    {
        return $this->hasMany(Campaign::class);
    }

    public function terminals()
    {
        return $this->hasMany(PosTerminal::class);
    }

    public function clientGroups()
    {
        return $this->hasMany(ClientGroup::class);
    }

    /**
     * Get the user's full name.
     *
     * @return string
     */
    public function getFullNameAttribute()
    {
        return "{$this->first_name} {$this->last_name}";
    }

    public static function boot()
    {
        parent::boot();

        self::creating(function ($model) {
            /** @var $model self */
            $model->token = Str::random(32);
        });

        self::created(function ($model) {
            /** @var $model self */
            if ($model->role === User::ROLE_MERCHANT) {
                /** @var LoyaltyProgram $program */
                $program = $model->loyaltyProgram()->create([
                    'currency' => 'THB',
                    'currency_value' => 100
                ]);
                $program->contactsTerm()->create();
                $program->score()->create();
                /** @var Card $card */
                $card = $program->card()->create();
                $card->stamps()->create();
            }
        });
    }
}
