<?php

namespace Betta\Foundation\Eloquent;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Database\Eloquent\Collection;

trait AbstractModelScopes
{
    /**
     * Scope the Model by Primary Key
     *
     * @param  Builder $query
     * @param  \Arrayable|array|int $values
     * @param  boolean $safe
     * @return Builder
     */
    public function scopeByKey($query, $values, $safe = true)
    {
        if ($safe === true ){
            # Ensure there is at least one value
            $values = $this->forceProtectArray($values);
        }

        return $query->whereIn($this->getQualifiedKeyName(), $values);
    }

    /**
     * Scope Out Model by Primary Key
     *
     * @param  Builder $query
     * @param  \Arrayable|array|int $values
     * @return Builder
     */
    public function scopeExcludeKey($query, $values)
    {
        return $query->whereNotIn($this->getQualifiedKeyName(), $values);
    }

    /**
     * Scope the Model by Primary Key where such Primary key is MD5 string
     *
     * @param  Builder $query
     * @param  string  $md5
     * @return Builder
     */
    public function scopeByMd5($query, $md5)
    {
        # Get key
        $key = $this->getQualifiedKeyName();

        return $query->whereRaw("MD5({$key}) = '{$md5}'");
    }

    /**
     * Scope any firleswith LIKE
     *
     * @param  Buidler $query
     * @param  string  $attribute
     * @param  string  $value
     * @return Buidler
     */
    public function scopeWhereLike($query, $attribute, $value)
    {
        return $query->where($attribute, 'LIKE', $value);
    }

    /**
     * Scope any firleswith LIKE
     *
     * @param  Buidler $query
     * @param  string  $attribute
     * @param  string  $value
     * @return Buidler
     */
    public function scopeOrWhereLike($query, $attribute, $value)
    {
        # find the
        return $query->orWhere($attribute, 'LIKE', $value);
    }
}
