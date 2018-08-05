<?php

namespace Betta\Models\Traits;

use Illuminate\Support\Str;

trait HasMeta
{
    /**
     * Filtered metas
     *
     * @var Collection
     */
    protected $filteredMetas;

    /**
     * Model can have meta
     * Implementation is model-specific
     *
     * @return Relation
     */
    abstract public function metas();

    /**
     * Handle dynamic method calls into the method.
     *
     * @param  string  $method
     * @param  array   $parameters
     * @return mixed
     */
    public function __call($method, $parameters)
    {
        if (Str::startsWith($method, 'meta')) {
            return $this->dynamicMeta($method, $parameters);
        }

        return parent::__call($method, $parameters);
    }

    /**
     * Dynamically apply conditions to metas
     *
     * @param  string $method
     * @param  mixed $parameters
     * @return Collection
     */
    public function dynamicMeta($method, $parameters)
    {
        # remove word "meta" from method.. and we have the key!
        $metaKey = substr($method, 4);

        # get the Filtered Metas
        $this->filteredMetas = $this->metas->where('meta_key', Str::snake($metaKey));

        if(!empty($parameters)){
            return $this->filteredMetas->whereIn('meta_value', $parameters);
        }

        return $this->filteredMetas;
    }
}
