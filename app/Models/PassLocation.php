<?php

namespace App\Models;

use App\Scopes\OrderScope;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\PassLocation
 *
 * @property-read \App\Models\Pass $pass
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PassLocation newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PassLocation newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PassLocation query()
 * @mixin \Eloquent
 */
class PassLocation extends Model
{
    protected $fillable = [
        'pass_id',
        'latitude',
        'longitude',
        'params'
    ];

    public function pass()
    {
        return $this->belongsTo(Pass::class);
    }

    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope(new OrderScope);
    }
}
