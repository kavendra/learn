<?php

namespace Betta\Foundation\Eloquent;

use Illuminate\Database\Eloquent\Model;
use App\Models\Eloquent\EloquentBuilder;
use Betta\Foundation\Eloquent\AbstractModelTrait;

abstract class AbstractModel extends Model
{
    use AbstractModelTrait,
        ObserverableBootTrait;

    /**
     * Override.
     * Create a new Eloquent query builder for the model.
     *
     * @param  \Illuminate\Database\Query\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder|static
     */
    public function newEloquentBuilder($query)
    {
        return new EloquentBuilder($query);
    }
}
