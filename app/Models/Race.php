<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Race
 *
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Race newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Race newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Race query()
 * @mixin \Eloquent
 */
class Race extends Model
{
    protected $fillable = ['name'];
}
