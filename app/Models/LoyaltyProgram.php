<?php

namespace App\Models;

use App\Scopes\OrderScope;
use Illuminate\Database\Eloquent\Model;
use App\Facades\Base64Validator;
use App\Facades\Base64;
use Illuminate\Support\Str;

/**
 * App\Models\LoyaltyProgram
 *
 * @method static \Illuminate\Database\Eloquent\Builder|LoyaltyProgram newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|LoyaltyProgram newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|LoyaltyProgram query()
 * @mixin \Eloquent
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Card $card
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Stamps[] $stamps
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Score[] $contactsTerm
 * @property-read mixed $contacts_term
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Score[] $score
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Location[] $locations
 * @property-read mixed $icon
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Offer[] $offers
 * @property-read mixed $currencies
 * @property-read \App\Models\User $user
 */
class LoyaltyProgram extends Model
{

    protected $fillable = [
        'user_id',
        'title',
        'description',
        'country',
        'language',
        'link',
        'currency',
        'currency_value',
        'start_at'
    ];

    protected $appends = ['currencies'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function card()
    {
        return $this->hasOne(Card::class);
    }

    public function stamps()
    {
        return $this->hasMany(Stamps::class);
    }

    public function contactsTerm()
    {
        return $this->hasMany(ContactsTerm::class);
    }

    public function score()
    {
        return $this->hasMany(Score::class);
    }

    public function locations()
    {
        return $this->hasMany(Location::class);
    }

    public function offers()
    {
        return $this->hasMany(Offer::class);
    }

//    public function getCardAttribute()
//    {
//        return $this->card()->get();
//    }
//
//    public function getStampsAttribute()
//    {
//        return $this->stamps()->get();
//    }
//
//    public function getScoreAttribute()
//    {
//        return $this->score()->get();
//    }
//
//    public function getContactsTermAttribute()
//    {
//        return $this->score()->get();
//    }

    public function getCurrenciesAttribute()
    {
        return Currency::all();
    }

    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope(new OrderScope);
    }
}