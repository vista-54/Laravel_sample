<?php
/**
 * Created by PhpStorm.
 * User: Dell
 * Date: 20.06.2019
 * Time: 13:02
 */

namespace App\Services;


use App\Models\Shop;

class ShopPerformance
{

    protected $shops;

    public function __construct()
    {
        $this->shops = auth()->user()->shops;
    }

    public function result()
    {
        return $this->shops->map(function ($item) {
            /** @var Shop $item */
            $item['amount'] = $item->clientShops->sum('amount');
            $item['install'] = 'In Progress';
            $item['collected'] = $item->clientShops->sum('point');
            $item['redeem'] = 'In Progress';
            $item['sms'] = 'In Progress';
            $item['qr'] = 'In Progress';
            $item['sold'] = $item->clientShops()->withCount('products')->get()->sum('products_count');
            return $item;
        });
    }
}