<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Product
 *
 * @property-read \App\Models\ClientShop $clientShop
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Product newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Product newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Product query()
 * @mixin \Eloquent
 */
class Product extends Model
{
    protected $fillable = ['client_shop_id', 'name', 'value', 'quantity'];

    public function clientShop()
    {
        return $this->belongsTo(ClientShop::class);
    }
}
