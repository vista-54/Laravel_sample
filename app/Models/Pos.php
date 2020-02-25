<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Pos
 *
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Pos newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Pos newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Pos query()
 * @mixin \Eloquent
 */
class Pos extends Model
{
    protected $fillable = [
        'ticket_id',
        'ticket_date',
        'store_id',
        'cashier_id',
        'payment_type',
        'stock_code',
        'pack',
        'quantity',
        'unit_price',
        'discount',
        'discount_2',
        'discount_3',
        'amount',
        'coupon_id',
        'loyalty_id',
        'campaign_id',
        'offer_id'
    ];
}
