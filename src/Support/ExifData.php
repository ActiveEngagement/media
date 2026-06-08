<?php

namespace Actengage\Media\Support;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Intervention\Image\Image;

/**
 * @property-read mixed $make
 * @property-read mixed $model
 *
 * @implements Arrayable<array-key, mixed>
 */
class ExifData implements Arrayable, Jsonable
{
    /**
     * The EXIF data stored an array.
     *
     * @var Collection<array-key, mixed>
     */
    protected Collection $data;

    /**
     * The coordinates in the EXIF data.
     */
    protected ExifCoordinates $coordinates;

    /**
     * Creates a new instance of Exif Data.
     *
     * @param  array<array-key, mixed>|Image|null  $data
     */
    public function __construct($data)
    {
        if ($data instanceof Image) {
            $data = $data->exif();
        }

        $this->data = new Collection(is_array($data) ? $data : []);
    }

    /**
     * Magically get the property from the EXIF data.
     *
     * @param  string  $key
     */
    public function __get($key): mixed
    {
        return $this->get($key);
    }

    /**
     * Get the value from the EXIF data.
     *
     * @param  string  $key
     * @param  mixed  $default
     */
    public function get($key, $default = null): mixed
    {
        return $this->data->get(Str::studly($key), $default);
    }

    /**
     * Get the EXIF coordinates.
     */
    public function coordinates(): ExifCoordinates
    {
        return $this->coordinates ?? (
            $this->coordinates = new ExifCoordinates($this)
        );
    }

    /**
     * Get the latitude.
     */
    public function latitude(): ?float
    {
        return $this->coordinates()->latitude;
    }

    /**
     * Get the longitude.
     */
    public function longitude(): ?float
    {
        return $this->coordinates()->longitude;
    }

    /**
     * Get the instance as an array.
     *
     * @return array<array-key, mixed>
     */
    public function toArray(): array
    {
        return $this->data->toArray();
    }

    /**
     * Convert the object to its JSON representation.
     */
    public function toJson($options = 0): string
    {
        $json = json_encode($this->data->toArray(), $options);

        return $json === false ? '' : $json;
    }
}
