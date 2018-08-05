<?php

namespace Betta\Foundation\Handlers;

use Betta\Helpers\Strings;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Contracts\Support\Arrayable;

abstract class AbstractTransformer implements Arrayable
{
    /**
     * Columns
     *
     * @var Array
     */
    protected $keys = [];

    /**
     * List the Values
     *
     * @var array
     */
    protected $attributes = [];

    /**
     * Helper values that should not be visible in resulting array
     *
     * @var array
     */
    protected $hidden = [];

    /**
     * Get the instance as an array.
     *
     * @return array
     */
    public function toArray()
    {
        return Arr::except($this->attributes, $this->hidden);
    }

    /**
     * Get a base Support collection instance from this collection.
     *
     * @return \Illuminate\Support\Collection
     */
    public function toBase()
    {
        return new Collection($this->toArray());
    }

    /**
     * Dynamically retrieve attributes on the model.
     *
     * @param  string  $key
     * @return mixed
     */
    public function __get($key)
    {
        return $this->getAttribute($key);
    }

    /**
     * Determine if an attribute exists.
     *
     * @param  string  $key
     * @return bool
     */
    public function __isset($key)
    {
        return ! is_null($this->getAttribute($key));
    }

    /**
     * Handle a single record
     *
     * @return $this
     */
    public function fill()
    {
        foreach ($this->keys as $key) {
            $this->setAttribute($key, $this->getAttribute($key));
        }

        return $this;
    }

    /**
     * Get the Attributes
     *
     * @chainable
     * @param  string $key
     * @param  mixed $value
     * @return $this
     */
    protected function setAttribute($key, $value)
    {
        $this->attributes[$key] = $value;

        return $this;
    }

    /**
     * Identify the method and resolve
     *
     * @param  string $key
     * @param  mixed $default
     * @return mixed
     */
    protected function getAttribute($key, $default = null)
    {
        if (array_key_exists($key, $this->attributes)) {
            return $this->getAttributeValue($key);
        }

        return $this->mutateAttribute($key, $default);
    }

    /**
     * Get the value from Attributes
     *
     * @param  string $key
     * @return mixed
     */
    protected function getAttributeValue($key)
    {
        return Arr::get($this->attributes, $key);
    }

    /**
     * Make the attribute Method name
     *
     * @param  string $key
     * @return string
     */
    protected function getAttributeMethod($key)
    {
        return 'get'.Strings::ncSlug($key, '').'Attribute';
    }

    /**
     * Get the value of an attribute using its mutator.
     *
     * @param  string  $key
     * @param  mixed   $default
     * @param  boolean $remember
     * @return mixed
     */
    protected function mutateAttribute($key, $default, $remember = true)
    {
        $value = $this->{$this->getAttributeMethod($key)}($default);

        if ($remember) {
            $this->setAttribute($key, $value);
        }

        return $value;
    }

    /**
     * Return the keys of the transformer
     *
     * @return array
     */
    public function getKeys()
    {
        return $this->keys;
    }
}
