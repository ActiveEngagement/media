<?php

namespace Actengage\Media\Casts;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection as SupportCollection;

class Collection extends Json
{
    /**
     * Cast the given value.
     *
     * @param  Model  $model
     * @param  string  $key
     * @param  mixed  $value
     * @param  array<string, mixed>  $attributes
     * @return SupportCollection<array-key, mixed>
     */
    public function get($model, $key, $value, $attributes)
    {
        $decoded = parent::get($model, $key, $value, $attributes);

        return new SupportCollection(is_array($decoded) ? $decoded : []);
    }
}
