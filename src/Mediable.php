<?php

namespace Actengage\Media;

use Actengage\Media\Media;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

trait Mediable {

    /**
     * Boot the trait.
     *
     * @return void
     */
    protected static function bootMediable()
    {
        static::deleting(function ($model) {
            $model->media()->detach();
        });
    }

    /**
     * Get all of the associated media models.
     *
     * @return Illuminate\Database\Eloquent\Relations\MorphToMany
     */
    public function media(): MorphToMany
    {
        return $this->morphToMany(
            Media::class, 'mediable', null, 'mediable_id', 'model_id'
        );
    }

    /**
     * Get the first associated media model.
     *
     * @return Actengage\Media\MorphOneThrough
     */
    public function medium()
    {
        return $this->morphOneThrough(
            'mediable', 'mediables', 'mediable_id', 'model_id', 'id', 'id'
        );
    }

    /**
     * Custom/hack helper function. This allows eager loading of relationships,
     * but instead of returning a collection, it returns the first result found.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  \Illuminate\Database\Eloquent\Model  $parent
     * @param  string  $table
     * @param  string  $foreignPivotKey
     * @param  string  $relatedPivotKey
     * @param  string  $parentKey
     * @param  string  $relatedKey
     * @param  string  $relationName
     * @return void
     */
    public function morphOneThrough($name, $table, $foreignPivotKey,
                                    $relatedPivotKey, $parentKey, $relatedKey,
                                    $relationName = null, $inverse = false)
    {
        return new MorphOneThrough(
            Media::query(), $this, $name, $table, $foreignPivotKey,
            $relatedPivotKey, $parentKey, $relatedKey, $relationName, $inverse
        );
    }

}
