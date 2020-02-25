<?php

namespace App\Models;

use App\Scopes\OrderScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

/**
 * App\Models\Stamps
 *
 * @method static \Illuminate\Database\Eloquent\Builder|Stamps newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Stamps newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Stamps query()
 * @mixin \Eloquent
 * @property-read \App\Models\Card $card
 * @property-read mixed $background_image
 * @property-read mixed $stamp_image
 * @property-read mixed $unstamp_image
 */
class Stamps extends Model
{
    protected $fillable = [
        'card_id',
        'stamps_number',
        'background_color',
        'background_image',
        'stamp_color',
        'unstamp_color',
        'stamp_image',
        'unstamp_image'
    ];

    public function card()
    {
        return $this->belongsTo(Card::class);
    }

    public function getBackgroundImageAttribute()
    {
        return $this->attributes['background_image'] ? url('storage/' . $this->attributes['background_image']) : null;
    }

    public function getStampImageAttribute()
    {
        return $this->attributes['stamp_image'] ? url('storage/' . $this->attributes['stamp_image']) : null;
    }

    public function getUnstampImageAttribute()
    {
        return $this->attributes['unstamp_image'] ? url('storage/' . $this->attributes['unstamp_image']) : null;
    }

    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope(new OrderScope);

        self::updating(function ($item) {
            $background_image = \request()->input('background_image');
            $stamp_image = \request()->input('stamp_image');
            $unstamp_image = \request()->input('unstamp_image');

//            if (\Base64Validator::validate($background_image)){
            if (Str::startsWith($background_image, 'data')){
                $item->background_image = \Base64::save($background_image, 'background_image', $item->card_id . '/background_image');
            }
            if ($background_image === null) {
                $item->background_image = null;
            }
//            if (\Base64Validator::validate($stamp_image)){
            if (Str::startsWith($stamp_image, 'data')){
                $item->stamp_image = \Base64::save($stamp_image, 'stamp_image', $item->card_id . '/stamp_image');
            }
            if ($stamp_image === null) {
                $item->stamp_image = null;
            }
//            if (\Base64Validator::validate($unstamp_image)){
            if (Str::startsWith($unstamp_image, 'data')){
                $item->unstamp_image = \Base64::save($unstamp_image, 'unstamp_image', $item->card_id . '/unstamp_image');
            }
            if ($unstamp_image === null) {
                $item->unstamp_image = null;
            }
            $item->background_color = request()->input('background_color') ?? 'rgb(255,255,255,0)';
            $item->unstamp_color = request()->input('unstamp_color') ?? 'rgb(255,255,255,0)';
            $item->stamp_color = request()->input('stamp_color') ?? 'rgb(255,255,255,0)';
        });
    }
}
