<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Campaign
 *
 * @property-read \App\Models\Shop $shop
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Shop[] $shops
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Campaign newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Campaign newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Campaign query()
 * @mixin \Eloquent
 * @property-read \App\Models\ClientGroup $clientGroup
 */
class Campaign extends Model
{
    protected $fillable = [
        'user_id',
        'campaign_name',
        'race',
        'age',
        'month',
        'customer_type',
        'type',
        'distribution',
        'campaign_start',
        'campaign_end',
        'date_time',
        'purpose',
        'text',
        'tag',
        'frequency',
        'region',
        'trans_total_value',
        'media',
        'client_group_id',
        'margin_value',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function shop()
    {
        return $this->belongsTo(Shop::class);
    }

    public function shops()
    {
        return $this->belongsToMany(Shop::class, 'campaign_shops');
    }

    public function clientGroup()
    {
        return $this->belongsTo(ClientGroup::class);
    }
}
