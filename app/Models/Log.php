<?php

namespace App\Models;

use App\Scopes\OrderScope;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Log
 *
 * @property-read \Illuminate\Database\Eloquent\Model|\Eloquent $logable
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Log newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Log newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Log query()
 * @mixin \Eloquent
 * @property-read mixed $points
 */
class Log extends Model
{
    protected $fillable = [
        'logabe_type',
        'logable_id',
        'amount',
        'point',
        'message',
        'shop_id',
        'area_manager_id'
    ];

    protected $appends = ['points'];

    public function getPointsAttribute()
    {
        return $this->attributes['point'];
    }

    public function logable()
    {
        return $this->morphTo();
    }

    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope(new OrderScope);
    }
}
