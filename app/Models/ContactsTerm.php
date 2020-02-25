<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\ContactsTerm
 *
 * @property-read \App\Models\LoyaltyProgram $loyaltyProgram
 * @method static \Illuminate\Database\Eloquent\Builder|ContactsTerm newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ContactsTerm newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ContactsTerm query()
 * @mixin \Eloquent
 */
class ContactsTerm extends Model
{
    protected $fillable = [
        'loyalty_program_id',
        'company_name',
        'address',
        'website',
        'email',
        'phone',
        'conditions'
    ];

    public function loyaltyProgram()
    {
        return $this->belongsTo(LoyaltyProgram::class);
    }
}
