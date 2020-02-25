<?php

namespace App\Models;


use App\Scopes\OrderScope;
use Illuminate\Database\Eloquent\Model;
use Base64;
use Illuminate\Support\Str;

/**
 * App\Models\Card
 *
 * @property-read \App\Models\LoyaltyProgram $loyaltyProgram
 * @method static \Illuminate\Database\Eloquent\Builder|Card newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Card newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Card query()
 * @mixin \Eloquent
 * @property-read \App\Models\Stamps $stamps
 * @property-read mixed $background_image
 * @property-read mixed $icon
 */
class Card extends Model
{
    protected $fillable = [
        'loyalty_program_id',
        'background_color',
        'background_main_color',
        'foreground_color',
        'label_color',
        'points_head',
        'points_value',
        'customer_head',
        'customer_value',
        'flip_head',
        'flip_value',
        'loyalty_profile',
        'loyalty_offers',
        'loyalty_contact',
        'loyalty_terms',
        'loyalty_terms_value',
        'loyalty_message',
        'icon',
        'background_image',
        'customer_id',
    ];

    protected $appends = ['stamps'];

    public function loyaltyProgram()
    {
        return $this->belongsTo(LoyaltyProgram::class);
    }

    public function stamps()
    {
        return $this->hasOne(Stamps::class);
    }

    public function getStampsAttribute()
    {
        return $this->stamps()->first();
    }

    public function getIconAttribute()
    {
        return $this->attributes['icon'] ? url('storage/' . $this->attributes['icon']) : null;
    }

    public function getBackgroundImageAttribute()
    {
        return $this->attributes['background_image'] ? url('storage/' . $this->attributes['background_image']) : null;
    }

    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope(new OrderScope);

        self::updating(function ($item) {
            $icon = \request()->input('icon');
            if (Str::startsWith($icon, 'data:image')) {
                $item->icon = Base64::save($icon, 'icon', $item->loyalty_program_id . '/loyalty_program');
            }
            if ($icon === null) {
                $item->icon = null;
            }

            $background_image = \request()->input('background_image');
            if (Str::startsWith($background_image, 'data:image')) {
                $item->background_image = Base64::save($background_image, 'background_image', $item->loyalty_program_id . '/card/background_image');
            }
            if ($background_image === null) {
                $item->background_image = null;
            }
            $item->background_color = request()->input('background_color') ?? 'rgb(255,255,255,0)';
            $item->foreground_color = request()->input('foreground_color') ?? 'rgb(255,255,255,0)';
            $item->label_color = request()->input('label_color') ?? 'rgb(255,255,255,0)';
        });
    }
}
