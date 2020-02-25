<?php
/**
 * Created by PhpStorm.
 * User: Dell
 * Date: 03.06.2019
 * Time: 15:49
 */

namespace App\Scopes;

use Illuminate\Database\Eloquent\Scope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class OrderByCreatedScope implements Scope
{
    /**
     * Apply the scope to a given Eloquent query builder.
     *
     * @param Builder $builder
     * @param Model $model
     * @return void
     */
    public function apply(Builder $builder, Model $model)
    {
        $builder->orderByDesc('created_at')->where('status', 1);
    }
}
