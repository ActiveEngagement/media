<?php

namespace Actengage\Media\Resources;

use Actengage\Media\Data\Stream;
use Actengage\Media\Exceptions\InvalidResourceException;
use Actengage\Media\Exceptions\BadAttributeException;
use Actengage\Media\Media;
use Actengage\Media\Support\ExifData;
use ColorThief\ColorThief;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Exception\NotReadableException;
use Intervention\Image\ImageManagerStatic;
use Psr\Http\Message\StreamInterface;

class Image extends Resource
{
    /**
     * The exif data instance.
     *
     * @var ExifData
     */
    public ExifData $exif;

    /**
     * The image resource.
     *
     * @var \Intervention\Image\Image
     */
    protected \Intervention\Image\Image $image;

    /**
     * Call methods on the image resource and return the value.
     *
     * @param string $name
     * @param array $arguments
     * @return mixed
     */
    public function __call($name, $arguments)
    {
        try {
            return parent::__call($name, $arguments);
        }
        catch(BadAttributeException $e) {
            call_user_func_array([$this->image, $name], $arguments);

            $this->filesize($this->image->stream(
                $this->extension
            ));
        }

        return $this;
    }

    /**
     * Initialize the resource.
     *
     * @param mixed $data
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
        } 
        catch(NotReadableException $e) {
            throw new InvalidResourceException(
                $e->getMessage(), $e->getCode(), $e
            );
        }
    }

    /**
     * Get the model attributes.
     *
     * @return array
     */
    public function attributes(): array
    {
        return parent::attributes([
            'exif' => $this->exif
        ]);
    }

    /**
     * Returns core image resource/obj.
     *
     * @return mixed
     */
    public function core(): mixed
    {
        return $this->image->getCore();
    }

    /**
     * Get the dominant color of the image.
     *
     * @param integer $quality
     * @param array|null $area
     * @param string $outputFormat
     * @param \ColorThief\Image\Adapter\AdapterInterface|string|null $adapter 
     * @return \ColorThief\Color|int|string|null
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
     *
     * @param ExifData $exif
     * @return self
     */
    public function exif(ExifData $exif): self
    {
        $this->exif = $exif;

        return $this;
    }

    /**
     * Get the image instance.
     *
     * @return \Intervention\Image\Image
     */
    public function image(): \Intervention\Image\Image
    {
        return $this->image;
    }
    
    /**
     * Get the resource data as a stream.
     *
     * @return StreamInterface
     */
    public function stream(): StreamInterface
    {
        return $this->image->stream();
    }

    /**
     * Store the resource on the disk.
     *
     * @param Media $model
     * @return boolean
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
     *
     * @param mixed $data
     * @return string|null
     */
    protected function extractExtension(mixed $data): ?string
    {
        if($data instanceof UploadedFile) {
            return $data->getClientOriginalExtension();
        }

        if($data instanceof Stream) {
            return $data->extension();
        }

        return $this->image->extension;
    }

    /**
     * Extract the filename from the data.
     *
     * @param mixed $data
     * @return string|null
     */
    protected function extractFilename(mixed $data): ?string
    {
        if($data instanceof UploadedFile) {
            return $data->getClientOriginalName();
        }

        if($data instanceof Stream) {
            return $data->filename();
        }

        return basename($this->image->basePath());
    }
}