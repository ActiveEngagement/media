<?php

namespace Actengage\Media\Support;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Intervention\Image\Image;

class ExifData implements Arrayable, Jsonable {

    /**
     * The EXIF data stored an array.
     *
     * @var Collection
     */
    protected Collection $data;

    /**
     * The coordinates in the EXIF data.
     *
     * @var ExifCoordinates
     */
    protected ExifCoordinates $coordinates;

    /**
     * Creates a new instance of Exif Data.
     *
     * @param array|Image $data
     */
    public function __construct($data)
    {
        if($data instanceof Image) {
            $data = $data->exif();
        }

        $this->data = collect($data);
    }

    /**
     * Magically get the property from the EXIF data.
     *
     * @param string $key
     * @return mixed
     */
    public function __get($key): mixed
    {
        return $this->get($key);
    }

    /**
     * Get the value from the EXIF data.
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function get($key, $default = null): mixed
    {
        return $this->data->get(Str::studly($key), $default);
    }

    /**
     * Get the EXIF coordinates.
     *
     * @return ExifCoordinates
     */
    public function coordinates(): ExifCoordinates
    {
        return $this->coordinates ?? (
            $this->coordinates = new ExifCoordinates($this)
        );
    }

    /**
     * Get the latitude.
     *
     * @return float|null
     */
    public function latitude(): ?float
    {
        return $this->coordinates()->latitude;
    }

    /**
     * Get the longitude.
     *
     * @return float|null
     */
    public function longitude(): ?float
    {
        return $this->coordinates()->longitude;
    }

    /**
     * Get the instance as an array.
     *
     * @return array
     */
    public function toArray(): array
    {
        return $this->data->toArray();
    }

    /**
     * Convert the object to its JSON representation.
     *
     * @return string
     */
    public function toJson($options = 0): string
    {
        return json_encode($this->data->toArray(), $options);
    }
}
