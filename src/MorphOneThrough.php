<?php

namespace Actengage\Media;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphPivot;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

/**
 * @extends MorphToMany<Media, Model, MorphPivot, string>
 */
class MorphOneThrough extends MorphToMany
{
    /**
     * Get the results of the relationship.
     *
     * @return mixed
     */
    public function getResults()
    {
        // Always return the first item of the results instead of the entire collection.
        return $this->first();
    }

    /**
     * Find multiple related models by their primary keys.
     *
     * @param  mixed  $ids
     * @param  array<int, string>  $columns
     * @return mixed
     */
    public function findMany($ids, $columns = ['*'])
    {
        // In this relationship we need to ensure null is returned if nothing
        // is found. In this case, null is being returned instead of an new
        // empty collection instance.
        if (empty($ids)) {
            return null;
        }

        $this->query->whereIn($this->getRelated()->getQualifiedKeyName(), $ids);

        return $this->get($columns);
    }

    /**
     * Match the eagerly loaded results to their parents.
     *
     * @param  array<int, Model>  $models
     * @param  Collection<int, Media>  $results
     * @param  string  $relation
     * @return array<int, Model>
     */
    public function match(array $models, Collection $results, $relation)
    {
        $dictionary = $this->buildDictionary($results->filter());

        // Once we have an array dictionary of child objects we can easily match the
        // children back to their parent using the dictionary and the keys on the
        // the parent models. Then we will return the hydrated models back out.
        foreach ($models as $model) {
            // This is the key difference, if the model is found then set the
            // relationship, otherwise return null. This will ensure either
            // the matching object is returned, or null instead of an empty array.
            $key = $model->{$this->parentKey};

            if ((is_int($key) || is_string($key)) && isset($dictionary[$key])) {
                $model->setRelation($relation, reset($dictionary[$key]));
            } else {
                $model->setRelation($relation, null);
            }
        }

        return $models;
    }
}
