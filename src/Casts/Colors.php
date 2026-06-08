<?php

declare(strict_types=1);

namespace Actengage\Media\Casts;

use ColorThief\Color;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

/**
 * @implements CastsAttributes<Collection<array-key, Color>, mixed>
 */
class Colors implements CastsAttributes
{
    /**
     * Cast the given value.
     *
     * @param  Model  $model
     * @param  string  $key
     * @param  mixed  $value
     * @param  array<string, mixed>  $attributes
     * @return Collection<array-key, Color>
     */
    public function get($model, $key, $value, $attributes)
    {
        return $this->collect($value)->map(function ($color): Color {
            [$red, $green, $blue] = sscanf(is_string($color) ? $color : '', '#%02x%02x%02x') ?? [0, 0, 0];

            return new Color((int) $red, (int) $green, (int) $blue);
        });
    }

    /**
     * Prepare the given value for storage.
     *
     * @param  Model  $model
     * @param  string  $key
     * @param  mixed  $value
     * @param  array<string, mixed>  $attributes
     * @return array<array-key, string>
     */
    public function set($model, $key, $value, $attributes)
    {
        return $this->collect($value)
            ->map(fn ($color): string => $color instanceof Color
                ? $color->getHex('#')
                : (is_scalar($color) ? (string) $color : ''))
            ->all();
    }

    /**
     * Normalize a value into a collection.
     *
     * @param  mixed  $value
     * @return Collection<array-key, mixed>
     */
    protected function collect($value): Collection
    {
        if ($value instanceof Arrayable) {
            $value = $value->toArray();
        }

        return new Collection(is_array($value) ? $value : []);
    }
}
