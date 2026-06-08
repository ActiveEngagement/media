<?php

namespace Actengage\Media\Resources;

use Actengage\Media\Data\Stream;
use Actengage\Media\Exceptions\BadAttributeException;
use Actengage\Media\Exceptions\InvalidResourceException;
use Actengage\Media\Media;
use Actengage\Media\Support\ExifData;
use ColorThief\Color;
use ColorThief\ColorThief;
use ColorThief\Image\Adapter\AdapterInterface;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Exception\NotReadableException;
use Intervention\Image\ImageManagerStatic;
use Psr\Http\Message\StreamInterface;

/**
 * @method \Illuminate\Support\Collection<int, \ColorThief\Color> palette(int $colorCount = 10, int $quality = 10, array{0?: int, 1?: int, 2?: int, 3?: int}|null $area = null, string $outputFormat = 'obj', \ColorThief\Image\Adapter\AdapterInterface|string|null $adapter = null)
 * @method $this resize(int|null $width, int|null $height, ?callable $callback = null)
 * @method $this greyscale()
 * @method $this saving(\Closure|string $callback)
 * @method static null saved(\Closure|string $callback)
 * @method static string staticGreeting()
 */
class Image extends Resource
{
    /**
     * The exif data instance.
     */
    public ExifData $exif;

    /**
     * The image resource.
     */
    protected \Intervention\Image\Image $image;

    /**
     * Call methods on the image resource and return the value.
     *
     * @param  string  $name
     * @param  array<array-key, mixed>  $arguments
     * @return mixed
     */
    public function __call($name, $arguments)
    {
        try {
            return parent::__call($name, $arguments);
        } catch (BadAttributeException) {
            $this->image->{$name}(...array_values($arguments));

            $this->filesize($this->image->stream(
                $this->extension
            ));
        }

        return $this;
    }

    /**
     * Initialize the resource.
     *
     * @return void
     */
    public function initialize(mixed $data)
    {
        try {
            $this->image = ImageManagerStatic::make($data);
            $this->filename = $this->extractFilename($data);
            $this->extension = $this->extractExtension($data);
            $this->filesize = $this->image->filesize();
            $this->mime = $this->image->mime();
            $this->exif = new ExifData($this->image);
        } catch (NotReadableException $e) {
            throw new InvalidResourceException(
                $e->getMessage(), $e->getCode(), $e
            );
        }
    }

    /**
     * Get the model attributes.
     *
     * @param  array<string, mixed>  ...$overrides
     * @return array<string, mixed>
     */
    public function attributes(array ...$overrides): array
    {
        return parent::attributes([
            'exif' => $this->exif,
        ], ...$overrides);
    }

    /**
     * Returns core image resource/obj.
     */
    public function core(): mixed
    {
        return $this->image->getCore();
    }

    /**
     * Get the dominant color of the image.
     *
     * @param  array{0?: int, 1?: int, 2?: int, 3?: int}|null  $area
     * @param  AdapterInterface|string|null  $adapter
     * @return mixed
     */
    public function color(
        int $quality = 10,
        ?array $area = null,
        string $outputFormat = 'obj',
        $adapter = null
    ) {
        return ColorThief::getColor(
            $this->image->getCore(),
            $quality,
            $area,
            $outputFormat,
            $adapter
        );
    }

    /**
     * Set the exif data.
     */
    public function exif(ExifData $exif): self
    {
        $this->exif = $exif;

        return $this;
    }

    /**
     * Get the image instance.
     */
    public function image(): \Intervention\Image\Image
    {
        return $this->image;
    }

    /**
     * Get the resource data as a stream.
     */
    public function stream(): StreamInterface
    {
        return $this->image->stream();
    }

    /**
     * Store the resource on the disk.
     */
    public function store(Media $model): bool
    {
        $stream = Stream::make($this->image->stream(
            $this->extension
        ));

        return Storage::disk($model->disk)->writeStream(
            $model->relative_path, $stream->resource(), $this->getStorageOptionsArray()
        );
    }

    /**
     * Extract the extension from the data.
     */
    protected function extractExtension(mixed $data): ?string
    {
        if ($data instanceof UploadedFile) {
            return $data->getClientOriginalExtension();
        }

        if ($data instanceof Stream) {
            return $data->extension();
        }

        return $this->image->extension;
    }

    /**
     * Extract the filename from the data.
     */
    protected function extractFilename(mixed $data): ?string
    {
        if ($data instanceof UploadedFile) {
            return $data->getClientOriginalName();
        }

        if ($data instanceof Stream) {
            return $data->filename();
        }

        return basename($this->image->basePath());
    }
}
