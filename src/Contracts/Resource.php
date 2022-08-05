<?php

namespace Actengage\Media\Contracts;

use Actengage\Media\Media;
use Closure;
use Psr\Http\Message\StreamInterface;

interface Resource
{
    /**
     * Get the model attributes.
     *
     * @return array
     */
    public function attributes(): array;

    /**
     * Set the `caption` property.
     *
     * @param string $value
     * @return self
     */
    public function caption(string $value): self;

    /**
     * Set the `context` property.
     *
     * @param string $value
     * @return self
     */
    public function context(string $value): self;

    /**
     * Set the `directory` property.
     *
     * @param string $value
     * @return self
     */
    public function directory(string $value): self;

    /**
     * Set the `disk` property.
     *
     * @param string $value
     * @return self
     */
    public function disk(string $value): self;

    /**
     * Set the `extension` property.
     *
     * @param string $value
     * @return self
     */
    public function extension(string $value): self;

    /**
     * Set the `filename` property.
     *
     * @param string $value
     * @return self
     */
    public function filename(string $value): self;

    /**
     * Set the `filesize` property.
     *
     * @param mixed $value
     * @return self
     */
    public function filesize($value): self;

    /**
     * Initialize the resource.
     *
     * @param mixed $data
     * @return void
     */
    public function initialize(mixed $data);

    /**
     * Add an `is` callback resolver that executes when the resource matches
     * the key(s).
     *
     * @param array|string $key
     * @param Closure $fn
     * @return self
     */
    public function is(array|string $key, Closure $fn): self;

    /**
     * Set the `meta` property.
     *
     * @param array|string $key
     * @param mixed $value
     * @return self
     */
    public function meta(array|string $key, $value = null): self;

    /**
     * Set the `mime` property.
     *
     * @param string $value
     * @return self
     */
    public function mime(string $value): self;

    /**
     * Add an `not` callback resolver that executes if the condition is
     * `false`.
     *
     * @param Closure|boolean $value
     * @param Closure $fn
     * @return self
     */
    public function not(Closure|bool $value, Closure $fn): self;

    /**
     * Store the resource on the disk.
     *
     * @param Media $model
     * @return boolean
     */
    public function store(Media $model): bool;

    /**
     * Get the resource data as a stream.
     *
     * @return StreamInterface
     */
    public function stream(): StreamInterface;

    /**
     * Associate a parent model to the resource.
     *
     * @param Media|null $model
     * @return self
     */
    public function parent(?Media $model): self;

    /**
     * Save the resource in the database.
     *
     * @return Media
     */
    public function save(): Media|bool;

    /**
     * Set the `tags` property.
     *
     * @param string[] ...$values
     * @return self
     */
    public function tags(...$values): self;

    /**
     * Set the `title` property.
     *
     * @param string $value
     * @return self
     */
    public function title(string $value): self;

    /**
     * Set the storage options passed to `Storage`.
     */
    public function storageOptions(array $values): self;

    /**
     * Add an `when` callback resolver that executes if the condition is
     * `true`.
     *
     * @param Closure|boolean $value
     * @param Closure $fn
     * @return self
     */
    public function when(Closure|bool $value, Closure $fn): self;

    /**
     * Get the instance as an array.
     *
     * @return array<TKey, TValue>
     */
    public function toArray();

    /**
     * Boot the resource.
     *
     * @return void
     */
    public static function boot();
}