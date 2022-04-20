<?php

namespace Actengage\Media\Support;

class ExifCoordinates {

    /**
     * The latitude coordinate.
     *
     * @var float|null
     */
    public float|null $latitude;

    /**
     * The longitude coordinate.
     *
     * @var float|null
     */
    public float|null $longitude;

    /**
     * Creates a new instance of Exif Coordinates.
     *
     * @param ExifData $exif
     */
    public function __construct(ExifData $exif)
    {
        $this->latitude = $this->coordinate(
            $exif->get('GPSLatitude'), $exif->get('GPSLatitudeRef')
        );

        $this->longitude = $this->coordinate(
            $exif->get('GPSLongitude'), $exif->get('GPSLongitudeRef')
        );
    }

    /**
     * Extract the coordinate from EXIF data.
     *
     * @param array|null $coord
     * @param string|null $ref
     * @return float|null
     */
    protected function coordinate(?array $coord, ?string $ref): ?float
    {
        if(is_null($coord)) {
            return null;
        }

        $d = $this->divideString($coord[0]);
        $m = $this->divideString($coord[1]);
        $s = $this->divideString($coord[2]);

        $coordinate = (
            (int) $d < 0 ? - 1 : 1
        ) * (abs($d) + ($m / 60.0) + ($s / 3600.0));

        return $coordinate * ($ref == 'N' ? 1 : -1);
    }

    /**
     * Divide the string to get the coordinate values.
     *
     * @param string $string
     * @return float
     */
    protected function divideString(string $string): float
    {
        [ $a, $b ] = explode('/', $string);

        return $b ? (float) $a / (float) $b : $a;
    }
}
