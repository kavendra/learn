<?php

namespace Betta\Foundation\Eloquent;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Database\Eloquent\Collection;

trait AbstractModelAttributes
{
    /**
     * Convert the ID to its MD5 hash
     *
     * @return string
     */
    public function getMd5Attribute()
    {
        return md5($this->getKey());
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
     * Combine the MorphClass and key

     *
     * @return string
     */
    public function getContextRefAttribute()
    {
        return encrypt(json_encode([
            'context_id' => $this->getKey(),
            'context_type' => $this->getMorphClass(),
        ]));
    }
}
