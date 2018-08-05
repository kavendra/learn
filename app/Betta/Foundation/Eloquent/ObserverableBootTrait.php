<?php
namespace Betta\Foundation\Eloquent;

trait ObserverableBootTrait
{
    /**
     * Indicate Presence of an autoloading observer
     *
     * @var boolean
     */
    protected static $selfObserved = true;

    /**
     * Add more standard observers
     *
     * @var array
     */
    protected static $observers = [];

    /**
     * Boot Model and five events
     *
     * @return Void
     */
    public static function boot()
    {
        # Boot Model
        parent::boot();
        # If we have self-observer
        if (static::$selfObserved === true){
            static::observe(app()->make(static::selfObserverName()));
        }
        # We can provide additional observers
        foreach (static::$observers as $class) {
            static::observe(app()->make($class));
        }
    }

    /**
     * Automatically resolve the observer name
     *
     * @return String
     */
    protected static function selfObserverName()
    {
        # what we need to find
        $class = class_basename(static::class);
        # Replace Value
        $observer = empty(static::$selfObserver)
                    ? "Observers\\{$class}Observer"
                    : static::$selfObserver;

        # replace the class name with Observer
        return preg_replace('/\b'.$class.'$/', $observer, static::class);
    }
}
