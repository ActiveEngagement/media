<?php

namespace Actengage\Media\Casts;

use Illuminate\Database\Eloquent\Model;

class ExifData extends Json
{
    /**
     * Cast the given value.
     *
     * @param  Model  $model
     * @param  string  $key
     * @param  mixed  $value
     * @param  array<string, mixed>  $attributes
     * @return \Actengage\Media\Support\ExifData
     */
    public function get($model, $key, $value, $attributes)
    {
        $decoded = parent::get($model, $key, $value, $attributes);

        return new \Actengage\Media\Support\ExifData(
            is_array($decoded) ? $decoded : []
        );
    }

    /**
     * Prepare the given value for storage.
     *
     * @param  Model  $model
     * @param  string  $key
     * @param  \Actengage\Media\Support\ExifData  $value
     * @param  array<string, mixed>  $attributes
     * @return string
     */
    public function set($model, $key, $value, $attributes)
    {
        return $value->toJson();
    }
}
