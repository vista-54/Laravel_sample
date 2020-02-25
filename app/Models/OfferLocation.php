<?php

namespace App\Models;

use App\Scopes\OrderScope;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\OfferLocation
 *
 * @property-read \App\Models\OfferCard $offerCard
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OfferLocation newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OfferLocation newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OfferLocation query()
 * @mixin \Eloquent
 * @property-read \App\Models\Offer $offer
 */
class OfferLocation extends Model
{
    protected $fillable = [
        'offer_id',
        'latitude',
        'longitude',
        'params'
    ];

    public function offer()
    {
        return $this->belongsTo(Offer::class);
    }

    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope(new OrderScope);
    }
}
