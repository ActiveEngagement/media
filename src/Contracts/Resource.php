<?php

declare(strict_types=1);

namespace Actengage\Media\Contracts;

use Actengage\Media\Media;
use Closure;
use Psr\Http\Message\StreamInterface;

interface Resource
{
    /**
     * Get the model attributes.
     *
     * @return array<string, mixed>
     */
    public function attributes(): array;

    /**
     * Set the `caption` property.
     */
    public function caption(string $value): self;

    /**
     * Set the `context` property.
     */
    public function context(string $value): self;

    /**
     * Set the `directory` property.
     */
    public function directory(string $value): self;

    /**
     * Set the `disk` property.
     */
    public function disk(string $value): self;

    /**
     * Set the `extension` property.
     */
    public function extension(string $value): self;

    /**
     * Set the `filename` property.
     */
    public function filename(string $value): self;

    /**
     * Set the `filesize` property.
     *
     * @param  mixed  $value
     */
    public function filesize($value): self;

    /**
     * Initialize the resource.
     *
     * @return void
     */
    public function initialize(mixed $data);

    /**
     * Add an `is` callback resolver that executes when the resource matches
     * the key(s).
     *
     * @param  array<array-key, mixed>|string  $key
     */
    public function is(array|string $key, Closure $fn): self;

    /**
     * Set the `meta` property.
     *
     * @param  array<array-key, mixed>|string  $key
     * @param  mixed  $value
     */
    public function meta(array|string $key, $value = null): self;

    /**
     * Set the `mime` property.
     */
    public function mime(string $value): self;

    /**
     * Add an `not` callback resolver that executes if the condition is
     * `false`.
     */
    public function not(Closure|bool $value, Closure $fn): self;

    /**
     * Store the resource on the disk.
     */
    public function store(Media $model): bool;

    /**
     * Get the resource data as a stream.
     */
    public function stream(): StreamInterface;

    /**
     * Associate a parent model to the resource.
     */
    public function parent(?Media $model): self;

    /**
     * Save the resource in the database.
     */
    public function save(): Media;

    /**
     * Set the `tags` property.
     *
     * @param  string|string[]  ...$values
     */
    public function tags(...$values): self;

    /**
     * Set the `title` property.
     */
    public function title(string $value): self;

    /**
     * Set the storage options passed to `Storage`.
     *
     * @param  array<array-key, mixed>  $values
     */
    public function storageOptions(array $values): self;

    /**
     * Add an `when` callback resolver that executes if the condition is
     * `true`.
     */
    public function when(Closure|bool $value, Closure $fn): self;

    /**
     * Get the instance as an array.
     *
     * @return array<string, mixed>
     */
    public function toArray();

    /**
     * Boot the resource.
     *
     * @return void
     */
    public static function boot();

    /**
     * Create a new instance of the resource.
     *
     * @param  mixed  ...$args
     */
    public static function make(...$args): self;
}
