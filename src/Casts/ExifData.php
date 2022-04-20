<?php
 
namespace Actengage\Media\Casts;

class ExifData extends Json
{
    /**
     * Cast the given value.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @param  string  $key
     * @param  mixed  $value
     * @param  array  $attributes
     * @return \Actengage\Media\Support\ExifData
     */
    public function get($model, $key, $value, $attributes)
    {
        return new \Actengage\Media\Support\ExifData(
            parent::get($model, $key, $value, $attributes)
        );
    }
 
    /**
     * Prepare the given value for storage.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @param  string  $key
     * @param  \Actengage\Media\Support\ExifData  $value
     * @param  array  $attributes
     * @return string
     */
    public function set($model, $key, $value, $attributes)
    {
        return $value->toJson();
    }
}