<?php
 
namespace Actengage\Media\Casts;

use Illuminate\Support\Collection as SupportCollection;

class Collection extends Json
{
    /**
     * Cast the given value.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @param  string  $key
     * @param  mixed  $value
     * @param  array  $attributes
     * @return \Illuminate\Support\Collection
     */
    public function get($model, $key, $value, $attributes)
    {
        return new SupportCollection(parent::get($model, $key, $value, $attributes));
    }
}