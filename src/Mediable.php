<?php

namespace Actengage\Media;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

/**
 * @property-read Collection<int, Media> $media
 * @property-read Media|null $medium
 *
 * @mixin Model
 */
trait Mediable
{
    /**
     * Boot the trait.
     *
     * @return void
     */
    protected static function bootMediable()
    {
        static::deleting(function (self $model) {
            $model->media()->detach();
        });
    }

    /**
     * Get all of the associated media models.
     *
     * @return MorphToMany<Media, $this>
     */
    public function media(): MorphToMany
    {
        return $this->morphToMany(
            Media::class, 'mediable', null, 'mediable_id', 'model_id'
        );
    }

    /**
     * Get the first associated media model.
     */
    public function medium(): MorphOneThrough
    {
        return $this->morphOneThrough(
            'mediable', 'mediables', 'mediable_id', 'model_id', 'id', 'id'
        );
    }

    /**
     * Custom/hack helper function. This allows eager loading of relationships,
     * but instead of returning a collection, it returns the first result found.
     *
     * @param  string  $name
     * @param  string  $table
     * @param  string  $foreignPivotKey
     * @param  string  $relatedPivotKey
     * @param  string  $parentKey
     * @param  string  $relatedKey
     * @param  string|null  $relationName
     * @param  bool  $inverse
     */
    public function morphOneThrough($name, $table, $foreignPivotKey,
        $relatedPivotKey, $parentKey, $relatedKey,
        $relationName = null, $inverse = false): MorphOneThrough
    {
        return new MorphOneThrough(
            Media::query(), $this, $name, $table, $foreignPivotKey,
            $relatedPivotKey, $parentKey, $relatedKey, $relationName, $inverse
        );
    }
}
