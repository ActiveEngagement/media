<?php
 
namespace Actengage\Media\Casts;

use ColorThief\Color;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
 
class Colors implements CastsAttributes
{
    /**
     * Cast the given value.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @param  string  $key
     * @param  mixed  $value
     * @param  array  $attributes
     * @return array
     */
    public function get($model, $key, $value, $attributes)
    {
        return collect($value)->map(function($color) {
            return new Color(...$color);
        });
    }
 
    /**
     * Prepare the given value for storage.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @param  string  $key
     * @param  array  $value
     * @param  array  $attributes
     * @return array
     */
    public function set($model, $key, $value, $attributes)
    {
        return collect($value)->map->getArray()->toArray();
    }
}