<?php
 
namespace Actengage\Media\Casts;

use ColorThief\Color;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Support\Collection;

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
        return (new Collection($value))->map(function($color) {
            list($red, $green, $blue) = sscanf($color, "#%02x%02x%02x");

            return new Color($red, $green, $blue);
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
        return (new Collection($value))->map->getHex('#')->toArray();
    }
}