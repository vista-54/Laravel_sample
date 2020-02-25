<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Base64Validator;
use Base64;
use Illuminate\Support\Str;

/**
 * App\Models\PassTemplate
 *
 * @property-read mixed $background_image
 * @property-read \App\Models\Pass $pass
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PassTemplate newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PassTemplate newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PassTemplate query()
 * @mixin \Eloquent
 * @property-read mixed $marker
 * @property-read mixed $stripe_image
 * @property-read mixed $icon
 */
class PassTemplate extends Model
{
    protected $fillable = [
        'background_color',
        'background_main_color',
        'foreground_color',
        'label_color',
        'points_head',
        'points_value',
        'offer_head',
        'offer_value',
        'customer_head',
        'customer_value',
        'flip_head',
        'flip_value',
        'back_side_head',
        'back_side_value',
        'icon',
        'background_image',
        'stripe_image',
        'customer_id',
        'unlimited'
    ];

    protected $appends = ['marker'];

    public function pass()
    {
        return $this->belongsTo(Pass::class);
    }

    public function getIconAttribute()
    {
        return $this->attributes['icon'] ? url('storage/' . $this->attributes['icon']) : null;
    }

    public function getBackgroundImageAttribute()
    {
        return $this->attributes['background_image'] ? url('storage' . $this->attributes['background_image']) : null;
    }

    public function getStripeImageAttribute()
    {
        return $this->attributes['background_image'] ? url('storage' . $this->attributes['background_image']) : null;
    }

    public function getMarkerAttribute()
    {
        return 1;
    }

    protected static function boot()
    {
        parent::boot();

        self::updating(function ($item) {
            $icon = \request()->input('icon');
            if (Str::startsWith($icon, 'data:image')){
                $item->icon = Base64::save($icon, 'icon', $item->user_id . '/loyalty_program');
            }
            if ($icon === null) {
                $item->icon = null;
            }

            $background_image = \request()->input('background_image');
            if (Str::startsWith($background_image, 'data:image')) {
                $item->background_image = Base64::save($background_image, 'background_image', $item->card_id . '/pass_template');
            }
            if ($background_image === null) {
                $item->background_image = null;
            }

            $stripe_image = \request()->input('stripe_image');
            if (Str::startsWith($stripe_image, 'data:image')) {
                $item->stripe_image = Base64::save($stripe_image, 'stripe_image', $item->card_id . '/pass_template');
            }
            if ($stripe_image === null) {
                $item->stripe_image = null;
            }

            $item->background_color = request()->input('background_color') ?? 'rgb(255,255,255,0)';
            $item->foreground_color = request()->input('foreground_color') ?? 'rgb(255,255,255,0)';
            $item->label_color = request()->input('label_color') ?? 'rgb(255,255,255,0)';
        });
    }
}
