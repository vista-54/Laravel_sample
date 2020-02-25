<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\ClientShop
 *
 * @property-read \App\Models\Client $client
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Product[] $products
 * @property-read \App\Models\Shop $shop
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ClientShop newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ClientShop newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ClientShop query()
 * @mixin \Eloquent
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Pos[] $poses
 */
class ClientShop extends Model
{
    const TYPE_POS = 'pos';
    const TYPE_LOYALTY = 'loyalty';
    const TYPE_OFFER = 'offer';
    const TYPE_COUPON = 'coupon';

    protected $fillable = ['client_id', 'shop_id', 'amount', 'type', 'point', 'created_by'];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function shop()
    {
        return $this->belongsTo(Shop::class);
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function poses()
    {
        return $this->hasMany(Pos::class, 'loyalty_id');
    }

    public static function boot()
    {
        parent::boot();

        self::creating(function ($model) {
            /** @var self $model */
//            $model->created_by = auth()->user()->id;
        });
    }
}
