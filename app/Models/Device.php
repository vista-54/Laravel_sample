<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Device
 *
 * @property-read \Illuminate\Database\Eloquent\Model|\Eloquent $devicable
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Device newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Device newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Device query()
 * @mixin \Eloquent
 */
class Device extends Model
{
    protected $fillable = ['token', 'devicable_id', 'devicable_type'];
    public $timestamps = false;

    public function devicable()
    {
        return $this->morphTo();
    }
}
