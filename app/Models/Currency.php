<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Currency
 *
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Currency newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Currency newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Currency query()
 * @mixin \Eloquent
 */
class Currency extends Model
{
    protected $fillable = ['name'];
}
