<?php

namespace Actengage\Media\Support;

use Closure;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Events\QueuedClosure;

trait HasEvents
{
    /**
     * The event dispatcher instance.
     */
    protected static ?Dispatcher $dispatcher = null;

    /**
     * The event map for the model.
     *
     * Allows for object-based events for native Eloquent events.
     *
     * @var array<string, class-string>
     */
    protected $dispatchesEvents = [];

    /**
     * User exposed observable events.
     *
     * These are extra user-defined events observers may subscribe to.
     *
     * @var array<int, string>
     */
    protected array $observables = [];

    /**
     * Fire a custom model event for the given event.
     *
     * @param  string  $event
     * @return mixed|null
     */
    protected function fireCustomEvent($event)
    {
        if (! isset($this->dispatchesEvents[$event]) || ! isset(static::$dispatcher)) {
            return;
        }

        $result = static::$dispatcher->dispatch(
            new $this->dispatchesEvents[$event]($this)
        );

        if (! is_null($result)) {
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
        if (! isset(static::$dispatcher)) {
            return true;
        }

        $this->fireCustomEvent($event);

        return static::$dispatcher->dispatch(
            static::dispatchEventName($event), [$this, ...$args]
        );
    }

    /**
     * Get the observable event names.
     *
     * @return array<int, string>
     */
    public function getObservableEvents()
    {
        return array_merge(
            [
                'initialized', 'saving', 'saved', 'storing', 'stored',
            ],
            $this->observables
        );
    }

    /**
     * Set the observable event names.
     *
     * @param  array<int, string>  $observables
     * @return void
     */
    public function setObservableEvents(array $observables)
    {
        $this->observables = $observables;
    }

    /**
     * Add an observable event name.
     *
     * @param  array<int, string>|string  $observables
     * @return void
     */
    public function addObservableEvents($observables)
    {
        $observables = is_array($observables) ? $observables : func_get_args();

        $this->observables = array_values(array_unique(array_merge(
            $this->observables, array_map(fn (mixed $v): string => is_scalar($v) ? (string) $v : '', $observables)
        )));
    }

    /**
     * Remove an observable event name.
     *
     * @param  array<int, string>|string  $observables
     * @return void
     */
    public function removeObservableEvents($observables)
    {
        $observables = is_array($observables) ? $observables : func_get_args();

        $this->observables = array_values(array_diff(
            $this->observables, array_map(fn (mixed $v): string => is_scalar($v) ? (string) $v : '', $observables)
        ));
    }

    /**
     * Is the event name an observable event.
     */
    public function isObservableEvent(string $name): bool
    {
        return in_array($name, $this->getObservableEvents());
    }

    /**
     * Get the event dispatcher instance.
     *
     * @return Dispatcher|null
     */
    public static function getEventDispatcher()
    {
        return static::$dispatcher;
    }

    /**
     * Set the event dispatcher instance.
     *
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
     * @param  QueuedClosure|Closure|string  $callback
     * @return void
     */
    protected static function registerEvent(string $event, $callback)
    {
        if (isset(static::$dispatcher)) {
            static::$dispatcher->listen(
                static::dispatchEventName($event),
                $callback instanceof QueuedClosure ? $callback->resolve() : $callback
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
        if (! isset(static::$dispatcher)) {
            return;
        }

        $instance = new static;

        foreach ($instance->getObservableEvents() as $event) {
            static::$dispatcher->forget(static::dispatchEventName($event));
        }
    }

    /**
     * Generate the proper dispatch event name.
     */
    public static function dispatchEventName(string $event, ?string $class = null): string
    {
        return sprintf('%s:%s', $event, $class ?? static::class);
    }
}
