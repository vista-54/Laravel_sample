<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\ClientLoyaltyProgram
 *
 * @property-read \App\Models\Client $client
 * @property-read \App\Models\LoyaltyProgram $loyaltyProgram
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ClientLoyaltyProgram newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ClientLoyaltyProgram newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ClientLoyaltyProgram query()
 * @mixin \Eloquent
 */
class ClientLoyaltyProgram extends Model
{
    protected $fillable = ['loyalty_program_id', 'client_id', 'point', 'stamped_count', 'client_loyalty_id'];

    public function loyaltyProgram()
    {
        return $this->belongsTo(LoyaltyProgram::class);
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }
}
