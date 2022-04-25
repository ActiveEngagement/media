<?php

namespace Actengage\Media\Support;

use Closure;
use Illuminate\Contracts\Events\Dispatcher;

trait HasEvents
{
    /**
     * The event dispatcher instance.
     *
     * @var Dispatcher
     */
    protected static Dispatcher $dispatcher;

    /**
     * The event map for the model.
     *
     * Allows for object-based events for native Eloquent events.
     *
     * @var array
     */
    protected $dispatchesEvents = [];

    /**
     * User exposed observable events.
     *
     * These are extra user-defined events observers may subscribe to.
     *
     * @var array
     */
    protected array $observables = [];
    
    /**
     * Filter the event results.
     *
     * @param  mixed  $result
     * @return mixed
     */
    protected function filterEventResults($result)
    {
        if(is_array($result)) {
            $result = array_filter($result, function ($response) {
                return ! is_null($response);
            });
        }

        return $result;
    }

    /**
     * Fire a custom model event for the given event.
     *
     * @param  string  $event
     * @param  string  $method
     * @return mixed|null
     */
    protected function fireCustomEvent($event)
    {
        if(!isset($this->dispatchesEvents[$event])) {
            return;
        }
        
        $result = static::$dispatcher->dispatch(
            new $this->dispatchesEvents[$event]($this)
        );

        if(!is_null($result)) {
            return $result;
        }
    }

    /**
     * Fire the given event for the resource.
     *
     * @param  string  $event
     * @param  mixed  ...$args
     * @return mixed
     */    
    protected function fireEvent($event, ...$args)
    {
        if(!isset(static::$dispatcher)) {
            return true;
        }

        $result = $this->filterEventResults(
            $this->fireCustomEvent($event)
        );

        if($result === false) {
            return false;
        }

        return static::$dispatcher->dispatch(
            static::dispatchEventName($event), [$this, ...$args]
        );
    }

    /**
     * Get the observable event names.
     *
     * @return array
     */
    public function getObservableEvents()
    {
        return array_merge(
            [
                'initialized', 'saving', 'saved', 'storing', 'stored'
            ],
            $this->observables
        );
    }

    /**
     * Set the observable event names.
     *
     * @param  array  $observables
     * @return void
     */
    public function setObservableEvents(array $observables)
    {
        $this->observables = $observables;
    }

    /**
     * Add an observable event name.
     *
     * @param  array|mixed  $observables
     * @return void
     */
    public function addObservableEvents($observables)
    {
        $this->observables = array_unique(array_merge(
            $this->observables, is_array($observables) ? $observables : func_get_args()
        ));
    }

    /**
     * Remove an observable event name.
     *
     * @param  array|mixed  $observables
     * @return void
     */
    public function removeObservableEvents($observables)
    {
        $this->observables = array_diff(
            $this->observables, is_array($observables) ? $observables : func_get_args()
        );
    }

    /**
     * Is the event name an observable event.
     *
     * @param string $name
     * @return boolean
     */
    public function isObservableEvent(string $name): bool
    {
        return in_array($name, $this->getObservableEvents());
    }

    /**
     * Get the event dispatcher instance.
     *
     * @return Dispatcher
     */
    public static function getEventDispatcher()
    {
        return static::$dispatcher;
    }

    /**
     * Set the event dispatcher instance.
     *
     * @param  Dispatcher  $dispatcher
     * @return void
     */
    public static function setEventDispatcher(Dispatcher $dispatcher)
    {
        static::$dispatcher = $dispatcher;
    }

    /**
     * Unset the event dispatcher.
     *
     * @return void
     */
    public static function unsetEventDispatcher()
    {
        static::$dispatcher = null;
    }

    /**
     * Register an event with the dispatcher.
     *
     * @param  string  $event
     * @param  \Illuminate\Events\QueuedClosure|\Closure|string  $callback
     * @return void
     */
    protected static function registerEvent(string $event, $callback)
    {
        if(isset(static::$dispatcher)) {
            static::$dispatcher->listen(
                static::dispatchEventName($event), $callback
            );
        }
    }

    /**
     * Remove all of the event listeners for the model.
     *
     * @return void
     */
    public static function flushEventListeners()
    {
        if(!isset(static::$dispatcher)) {
            return;
        }

        $instance = new static;

        foreach($instance->getObservableEvents() as $event) {
            static::$dispatcher->forget(static::dispatchEventName($event));
        }
    }

    /**
     * Generate the proper dispatch event name.
     *
     * @param string $event
     * @param string $class
     * @return string
     */
    public static function dispatchEventName(string $event, string $class = null): string
    {
        return sprintf('%s:%s', $event, $class ?? static::class);
    }
}