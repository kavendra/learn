<?php

namespace Betta\Services\Generator\Drivers;

trait ArrayableTemplate
{
    /**
     * Convert the Object to array
     *
     * @return array
     */
    public function toArray()
    {
        return [
            'path'   => $this->path,
            'file'   => $this->file,
            'stream' => $this->stream,
        ];
    }
}
