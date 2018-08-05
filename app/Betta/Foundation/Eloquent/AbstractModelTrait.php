<?php

namespace Betta\Foundation\Eloquent;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Database\Eloquent\Collection;

trait AbstractModelTrait
{
    use AbstractModelScopes;
    use AbstractModelAttributes;

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

    /**
     * Convert th ID to its MD5 hash
     *
     * @return string
     */
    public function getMd5Attribute()
    {
        return md5($this->id);
    }

    /**
     * Combine the MorphClass and key
     * as is {Class}::{id}
     *
     * @return string
     */
    public function getMorphKeyAttribute()
    {
        return $this->getMorphClass() .'::'. $this->getKey();
    }

    /**
     * Access class constants
     *
     * @param  string $name
     * @param  mixed $default
     * @return mixed
     */
    public function getConstant($name, $default = null)
    {
        # parse
        $name = "static::{$name}";
        # resolve
        return defined($name) ? constant($name) : $default;
    }

    /**
     * Return only a given number of attributes and properties
     *
     * @param  string|array $attribute
     * @return array
     */
    public function only($attribute, $default = null)
    {
        foreach ((array)$attribute as $key) {
            array_set($only, $key, object_get($this, $key, $default));
        }

        return array_collapse($only);
    }

    /**
     * Ensure there is at least once value in array
     *
     * @param  \Arrayable|array|int $values
     * @return [type]
     */
    protected function forceProtectArray($values, $impossibleId = 0)
    {
        # if we have a list of Models
        if ($values instanceof Collection) {
            $values = $values->modelKeys();
        }
        # if the values are Arrayable
        if ($values instanceof Arrayable) {
            $values = $values->toArray();
        }

        return array_prepend((array) $values, $impossibleId);
    }

    /**
     * True if the Field in question is dirty, was null and not is not null
     *
     * @param  string $field
     * @return boolean
     */
    public function isDirtyFromNull($field)
    {
        return $this->isDirty($field)
           and is_null($this->getOriginal($field))
           and !is_null($this->getAttribute($field));
    }
}
