<?php

declare(strict_types=1);

namespace Actengage\Media\Support;

class ExifCoordinates
{
    /**
     * The latitude coordinate.
     */
    public ?float $latitude;

    /**
     * The longitude coordinate.
     */
    public ?float $longitude;

    /**
     * Creates a new instance of Exif Coordinates.
     */
    public function __construct(ExifData $exif)
    {
        $latitude = $exif->get('GPSLatitude');
        $latitudeRef = $exif->get('GPSLatitudeRef');
        $longitude = $exif->get('GPSLongitude');
        $longitudeRef = $exif->get('GPSLongitudeRef');

        $this->latitude = $this->coordinate(
            is_array($latitude) ? $latitude : null,
            is_string($latitudeRef) ? $latitudeRef : null
        );

        $this->longitude = $this->coordinate(
            is_array($longitude) ? $longitude : null,
            is_string($longitudeRef) ? $longitudeRef : null
        );
    }

    /**
     * Extract the coordinate from EXIF data.
     *
     * @param  array<array-key, mixed>|null  $coord
     */
    protected function coordinate(?array $coord, ?string $ref): ?float
    {
        if (is_null($coord)) {
            return null;
        }

        $d = $this->divideString($this->toString($coord[0] ?? null));
        $m = $this->divideString($this->toString($coord[1] ?? null));
        $s = $this->divideString($this->toString($coord[2] ?? null));

        $coordinate = (
            (int) $d < 0 ? -1 : 1
        ) * (abs($d) + ($m / 60.0) + ($s / 3600.0));

        return $coordinate * ($ref == 'N' ? 1 : -1);
    }

    /**
     * Coerce a scalar EXIF value into a string.
     */
    protected function toString(mixed $value): string
    {
        return is_scalar($value) ? (string) $value : '';
    }

    /**
     * Divide the string to get the coordinate values.
     */
    protected function divideString(string $string): float
    {
        [$a, $b] = explode('/', $string);

        return $b !== '' && $b !== '0' ? (float) $a / (float) $b : (float) $a;
    }
}
