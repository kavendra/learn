<?php

namespace Betta\Foundation\Providers;

use Illuminate\Support\ServiceProvider;

abstract class AbstractCommandServiceProvider extends ServiceProvider
{
    /**
     * List Commands to register
     *
     * @var Array
     */
    protected $commands = [];

    /**
     * Register Commands
     *
     * @return Void
     */
    public function register(){
        $this->commands($this->commands);
    }
}
