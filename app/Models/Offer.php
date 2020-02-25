<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Offer
 *
 * @property-read \App\Models\LoyaltyProgram $loyaltyProgram
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Offer newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Offer newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Offer query()
 * @mixin \Eloquent
 * @property-read mixed $offer_card
 * @property-read \App\Models\OfferCard $offerCard
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\OfferLocation[] $offerLocations
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\ClientOffer[] $clientOffers
 * @property-read mixed $start_date
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Pos[] $poses
 */
class Offer extends Model
{
    const ACTIVE = 1;
    const INACTIVE = 0;

    protected $fillable = [
        'loyalty_program_id',
        'name',
        'description',
        'start_date',
        'end_date',
        'points_cost',
        'customer_limit',
        'availability_count',
        'notify',
        'status',
        'margin_value',
    ];

//    protected $appends = ['offer_card'];

    public function loyaltyProgram()
    {
        return $this->belongsTo(LoyaltyProgram::class);
    }

    public function offerCard()
    {
        return $this->hasOne(OfferCard::class);
    }

    public function offerLocations()
    {
        return $this->hasMany(OfferLocation::class);
    }

    public function clientOffers()
    {
        return $this->hasMany(ClientOffer::class);
    }

    public function getOfferCardAttribute()
    {
        return $this->offerCard()->first();
    }

    public function getStartDateAttribute()
    {
        return Carbon::parse($this->attributes['start_date'])->toDateString();
    }

    public function poses()
    {
        return $this->hasMany(Pos::class);
    }

    protected static function boot()
    {
        parent::boot();

        self::created(function ($model) {
            /** @var self $model */
            $model->offerCard()->create();
        });
    }
}
