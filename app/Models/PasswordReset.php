<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\PasswordReset
 *
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PasswordReset newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PasswordReset newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PasswordReset query()
 * @mixin \Eloquent
 * @property-read \Illuminate\Database\Eloquent\Model|\Eloquent $resetable
 */
class PasswordReset extends Model
{
    protected $fillable = ['code', 'resetable_id', 'resetable_type'];
    public $timestamps = false;

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function resetable()
    {
        return $this->morphTo();
    }
}
