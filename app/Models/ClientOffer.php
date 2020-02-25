<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\ClientOffer
 *
 * @property-read \App\Models\Client $client
 * @property-read \App\Models\Offer $offer
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ClientOffer newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ClientOffer newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ClientOffer query()
 * @mixin \Eloquent
 */
class ClientOffer extends Model
{
    protected $fillable = ['client_id', 'offer_id', 'used'];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function offer()
    {
        return $this->belongsTo(Offer::class);
    }
}
