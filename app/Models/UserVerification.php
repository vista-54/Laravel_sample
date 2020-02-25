<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\UserVerification
 *
 * @property-read \Illuminate\Database\Eloquent\Model|\Eloquent $verifiable
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserVerification newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserVerification newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserVerification query()
 * @mixin \Eloquent
 */
class UserVerification extends Model
{
    protected $fillable = ['token', 'verifiable_id', 'verifiable_type'];
    public $timestamps = false;

    public function verifiable()
    {
        return $this->morphTo();
    }
}
