<?php

namespace App\Models;

use App\Services\Identifier;
use Illuminate\Database\Eloquent\Model;
use Base64;
use Illuminate\Support\Str;

/**
 * App\Models\OfferCard
 *
 * @property-read \App\Models\Offer $offer
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\OfferLocation[] $offerLocations
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OfferCard newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OfferCard newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OfferCard query()
 * @mixin \Eloquent
 * @property-read mixed $background_image
 * @property-read mixed $customer_id
 * @property-read mixed $customer_value
 * @property-read mixed $icon
 * @property-read mixed $limited_offer
 * @property-read mixed $loyalty_terms_value
 */
class OfferCard extends Model
{
    protected $fillable = [
        'offer_id',
        'background_color',
        'background_main_color',
        'foreground_color',
        'label_color',
        'background_image',
        'stripe_image',
        'points_head',
        'points_value',
        'offer_head',
        'offer_value',
        'customer_head',
        'customer_value',
        'loyalty_active_offer',
        'loyalty_offers',
        'loyalty_profile',
        'loyalty_contact',
        'loyalty_terms',
        'loyalty_last_message',
        'loyalty_message',
        'icon',
//        'customer_id',
    ];

    protected $appends = [
        'loyalty_terms_value', 'limited_offer',
    ];

    public function offer()
    {
        return $this->belongsTo(Offer::class);
    }

    public function getCustomerValueAttribute()
    {
        return $this->offer->description;
//        return Offer::find($this->offer_id)->description;
    }

    public function getLoyaltyTermsValueAttribute()
    {
        $lp = LoyaltyProgram::whereHas('offers', function ($item) {
            /** @var LoyaltyProgram $item */
            $item->where('id', $this->attributes['offer_id']);
        })->first();
        $terms = $lp->contactsTerm()->first();
        return $terms ? $terms->conditions : null;
    }

    public function getLimitedOfferAttribute()
    {
        return $this->offer->availability_count;
    }

    public function getIconAttribute()
    {
        return $this->attributes['icon'] ? url('storage/' . $this->attributes['icon']) : null;
    }

    public function getBackgroundImageAttribute()
    {
        return $this->attributes['background_image'] ? url('storage/' . $this->attributes['background_image']) : null;
    }

//    public function getCustomerIdAttribute()
//    {
//        return auth()->user()->role === null ? \Barcode::generate(\Identifier::generate(Identifier::OFFER, auth()->user()->id, $this->id)) : $this->attributes['customer_id'] ;
//    }

    protected static function boot()
    {
        parent::boot();

        self::updating(function ($item) {
            $icon = \request()->input('icon');
            if (Str::startsWith($icon, 'data:image')) {
                $item->icon = Base64::save($icon, 'icon', $item->offer_id . '/offer_card/icon');
            }
            if ($icon === null) {
                $item->icon = null;
            }

            $background_image = \request()->input('background_image');
            if (Str::startsWith($background_image, 'data:image')) {
                $item->background_image = Base64::save($background_image, 'background_image', $item->offer_id . '/offer_card/background_image');
            }
            if ($background_image === null) {
                $item->background_image = null;
            }

            $item->background_color = request()->input('background_color') ?? 'rgb(255,255,255,0)';
            $item->foreground_color = request()->input('foreground_color') ?? 'rgb(255,255,255,0)';
            $item->label_color = request()->input('label_color') ?? 'rgb(255,255,255,0)';

            $item->offer()->update(['description' => request()->input('customer_value')]);
        });
    }
}
