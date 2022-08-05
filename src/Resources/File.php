<?php

namespace Actengage\Media\Resources;

use Actengage\Media\Data\Stream;
use Actengage\Media\Exceptions\InvalidResourceException;
use Actengage\Media\Exceptions\NotReadableException;
use Actengage\Media\Media;
use Illuminate\Support\Facades\Storage;
use Psr\Http\Message\StreamInterface;

class File extends Resource
{
    /**
     * The data stream.
     *
     * @var Stream
     */
    protected Stream $stream;

    /**
     * Initialize the resource.
     *
     * @return void
     */
    public function initialize(mixed $data)
    {
        try {
            $this->stream = Stream::make($data);
            $this->extension = $this->stream->extension();
            $this->filename = $this->stream->filename();
            $this->filesize = $this->stream->getSize();
            $this->mime = $this->stream->mime();
        }
        catch(NotReadableException $e) {
            throw new InvalidResourceException(
                $e->getMessage(), $e->getCode(), $e
            );
        }
    }

    /**
     * Store the resource on the disk.
     *
     * @param Media $model
     * @return boolean
     */
    public function store(Media $model): bool
    {
        return Storage::disk($model->disk)->writeStream(
            $model->relative_path, $this->stream->resource()
        );
    }

    /**
     * Get the stream instance.
     *
     * @return StreamInterface
     */
    public function stream(): StreamInterface
    {
        return $this->stream;
    }
}