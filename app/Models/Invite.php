<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Invite
 *
 * @property-read \App\Models\AreaManager $manager
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Invite newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Invite newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Invite query()
 * @mixin \Eloquent
 * @property-read \App\Models\AreaManager $areaManager
 */
class Invite extends Model
{
    protected $fillable = ['area_manager_id', 'shop_id', 'email', 'confirmed'];

    public function areaManager()
    {
        return $this->belongsTo(AreaManager::class);
    }
}
