<?php

namespace App\Models;

use App\Scopes\OrderScope;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Shop
 *
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Shop newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Shop newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Shop query()
 * @mixin \Eloquent
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\ClientShop[] $clientShops
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\AreaManager[] $areaManagers
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Invite[] $invites
 * @property-read mixed $shop_type
 * @property-read \App\Models\ShopType $shopType
 */
class Shop extends Model
{
    protected $fillable = [
        'user_id',
        'shop_type_id',
        'number',
        'name',
        'address',
        'cluster',
        'region',
        'area',
        'city',
    ];

    protected $appends = ['shop_type'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function areaManagers()
    {
        return $this->belongsToMany(AreaManager::class, 'area_managers_shops');
    }

    public function clientShops()
    {
        return $this->hasMany(ClientShop::class);
    }

    public function invites()
    {
        return $this->hasMany(Invite::class);
    }

    public function shopType()
    {
        return $this->belongsTo(ShopType::class);
    }

    public function getShopTypeAttribute()
    {
        if ($this->shopType()->doesntExist()) {
            $this->update(['shop_type_id' => 1]);
        }
        return $this->shopType()->first()->name;
    }

    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope(new OrderScope);

//        self::creating(function ($model) {
//            $model->user_id = auth()->user()->id;
//        });
    }
}
