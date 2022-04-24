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
     * User exposed observable events.
     *
     * These are extra user-defined events observers may subscribe to.
     *
     * @var array
     */
    protected static array $observables = [
        'initialized', 'creating', 'created', 'storing', 'stored'
    ];
    
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

        return static::$dispatcher->dispatch(
            static::dispatchEventName($event), [$this, ...$args]
        );
    }

    /**
     * Get the observable event names.
     *
     * @return array
     */
    public static function getObservableEvents()
    {
        return static::$observables;
    }

    /**
     * Set the observable event names.
     *
     * @param  array  $observables
     * @return void
     */
    public static function setObservableEvents(array $observables)
    {
        static::$observables = $observables;
    }

    /**
     * Add an observable event name.
     *
     * @param  array|mixed  $observables
     * @return void
     */
    public static function addObservableEvents($observables)
    {
        static::$observables = array_unique(array_merge(
            static::$observables, is_array($observables) ? $observables : func_get_args()
        ));
    }

    /**
     * Remove an observable event name.
     *
     * @param  array|mixed  $observables
     * @return void
     */
    public static function removeObservableEvents($observables)
    {
        static::$observables = array_diff(
            static::$observables, is_array($observables) ? $observables : func_get_args()
        );
    }

    /**
     * Is the event name an observable event.
     *
     * @param string $name
     * @return boolean
     */
    public static function isObservableEvent(string $name): bool
    {
        return in_array($name, static::getObservableEvents());
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
        if (isset(static::$dispatcher)) {
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

        foreach(static::getObservableEvents() as $event) {
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