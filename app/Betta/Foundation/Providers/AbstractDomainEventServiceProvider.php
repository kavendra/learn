<?php

namespace Betta\Foundation\Providers;

use Illuminate\Support\Facades\Event;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

abstract class AbstractDomainEventServiceProvider extends ServiceProvider
{
    /**
     * Event Domain Namespace
     *
     * @var string
     */
    protected $namespace = '';


    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [];


    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();
    }


    /**
     * Get the events and handlers.
     *
     * @return array
     */
    public function listens()
    {
        $listens = [];

        foreach($this->listen as $event => $array){
            $listens[ $this->applyNamespace($event) ] = $array;
        }

        return $listens;
    }


    /**
     * Apply the namespace to each Event
     *
     * @param  string $string
     * @return string
     */
    protected function applyNamespace($string)
    {
        return implode('\\', [$this->namespace, $string]);
    }
}
