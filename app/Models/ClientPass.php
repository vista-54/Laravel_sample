<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\ClientPass
 *
 * @property-read \App\Models\Client $client
 * @property-read \App\Models\Offer $offer
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ClientPass newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ClientPass newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ClientPass query()
 * @mixin \Eloquent
 * @property-read \App\Models\Pass $pass
 */
class ClientPass extends Model
{
    protected $table = 'client_pass';
    protected $fillable = ['client_id', 'offer_id', 'shop_id', 'created_by'];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function pass()
    {
        return $this->belongsTo(Pass::class);
    }
}
