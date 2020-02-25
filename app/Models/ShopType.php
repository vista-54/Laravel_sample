<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\ShopType
 *
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ShopType newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ShopType newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ShopType query()
 * @mixin \Eloquent
 */
class ShopType extends Model
{
    protected $fillable = ['name'];
}
