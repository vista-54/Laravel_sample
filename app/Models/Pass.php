<?php

namespace App\Models;

use App\Scopes\OrderScope;
use Illuminate\Database\Eloquent\Model;
use Base64Validator;
use Base64;
use Illuminate\Support\Str;

/**
 * App\Models\Pass
 *
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Pass newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Pass newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Pass query()
 * @mixin \Eloquent
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\PassLocation[] $passLocations
 * @property-read \App\Models\PassTemplate $passTemplate
 * @property-read mixed $pass_template
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\ClientPass[] $clientPasses
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Pos[] $poses
 */
class Pass extends Model
{
    const ACTIVE = 1;
    const INACTIVE = 0;

    protected $fillable = [
        'user_id',
        'title',
        'description',
        'value',
        'availability_count',
        'start_date',
        'end_date',
        'status',
        'margin_value',
        'expiration_date',
    ];

    protected $appends = ['pass_template'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function passTemplate()
    {
        return $this->hasOne(PassTemplate::class);
    }

    public function passLocations()
    {
        return $this->hasMany(PassLocation::class);
    }

    public function getPassTemplateAttribute()
    {
        return $this->passTemplate()->first();
    }

    public function clientPasses()
    {
        return $this->hasMany(ClientPass::class);
    }

    public function poses()
    {
        return $this->hasMany(Pos::class, 'coupon_id');
    }

    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope(new OrderScope);

        self::created(function ($model) {
            /** @var self $model */
            $model->passTemplate()->create();
        });
    }
}
