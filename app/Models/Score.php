<?php

namespace App\Models;

use App\Scopes\OrderScope;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Score
 *
 * @property-read \App\Models\LoyaltyProgram $loyaltyProgram
 * @method static \Illuminate\Database\Eloquent\Builder|Score newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Score newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Score query()
 * @mixin \Eloquent
 */
class Score extends Model
{
    protected $fillable = [
        'loyalty_program_id',
        'set_email',
        'set_phone',
        'set_card',
        'scan_card'
    ];

    public function loyaltyProgram()
    {
        return $this->belongsTo(LoyaltyProgram::class);
    }

    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope(new OrderScope);
    }
}
