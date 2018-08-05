<?php

namespace Betta\Foundation\Mail;

use Illuminate\Support\Collection;

trait AttachesGeneratedFiles
{
    /*
    |--------------------------------------------------------------------------
    | Generates and attaches generates files
    |--------------------------------------------------------------------------
    |
    | The Mailable is expected to have the protected generators property.
    | protected $generators = [
    |   {generator name} => [
    |       {parameter name} => {parameter value}
    |   ],
    | ];
    |
    | Parameter values would be resolved from the  class using $this, so they must exist in root
    */

    /**
     * Get Generators value
     *
     * @return array
     */
    protected function generators()
    {
        return (array)$this->generators;
    }

    /**
     * Attach items
     *
     * @return $this
     */
    protected function attaches()
    {
        foreach($this->generators() as $name => $arguments){
            # generate the files
            $files = generator($name)->handle($this->generatorArguments($arguments));
            # conver the result to collection
            $files = ($files instanceOf Collection) ? $files : collect([$files]);
            # go through montions of attaching
            $files->each(function($file){
                # Only attach the good records
                if(!empty($file->path) AND file_exists($file->path)){
                    $this->attach($file->path, ['as'=> $file->file]);
                }
            });
        }

        return $this;
    }

    /**
     * Resolve the values from the current class
     *
     * @param  $arguments
     * @param  array $values
     * @return array
     */
    protected function generatorArguments($arguments, $values = [])
    {
        foreach($arguments as $name => $value){
            $values[$name] = data_get($this, $value);
        }

        return $values;
    }
}
