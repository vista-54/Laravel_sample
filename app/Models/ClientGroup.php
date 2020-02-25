<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\ClientGroup
 *
 * @property-read User $user
 * @method static Builder|ClientGroup newModelQuery()
 * @method static Builder|ClientGroup newQuery()
 * @method static Builder|ClientGroup query()
 * @mixin \Eloquent
 * @property-read Collection|Client[] $clients
 */
class ClientGroup extends Model
{
    protected $fillable = ['user_id', 'name'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function clients()
    {
        return $this->belongsToMany(Client::class, 'client_has_groups');
    }
}
