<?php

namespace App\Models;

use App\Scopes\OrderByCreatedScope;
use App\Scopes\OrderScope;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Transaction
 *
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Transaction newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Transaction newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Transaction query()
 * @mixin \Eloquent
 * @property-read \App\Models\Client $client
 * @property-read mixed $shop_name
 * @property-read \App\Models\Shop $shop
 */
class Transaction extends Model
{
    protected $fillable = ['client_id', 'shop_id', 'point', 'amount', 'status', 'currency', 'area_manager_id'];

    protected $appends = ['shop_name'];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function shop()
    {
        return $this->belongsTo(Shop::class);
    }

    public function getShopNameAttribute()
    {
        return $this->shop()->exists() ? $this->shop->name : 'Buy offer';
    }

    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope(new OrderByCreatedScope);
    }
}
