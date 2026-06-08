<?php

namespace Actengage\Media\Data;

use Actengage\Media\Exceptions\NotReadableException;
use GuzzleHttp\Psr7\Stream as BaseStream;
use Psr\Http\Message\StreamInterface;
use SplFileInfo;

/**
 * @phpstan-consistent-constructor
 */
class Stream extends BaseStream
{
    /**
     * @var resource
     */
    protected $stream;

    /**
     * Create an instance of a file stream.
     *
     * @param  resource  $subject
     * @param  array{size?: int, metadata?: array<string, mixed>}  $options
     */
    public function __construct($subject, array $options = [])
    {
        parent::__construct($this->stream = $subject, $options);
    }

    /**
     * Get the extension of the resource.
     */
    public function extension(): string
    {
        return pathinfo($this->filename(), PATHINFO_EXTENSION);
    }

    /**
     * Get the name of the resource.
     */
    public function filename(): string
    {
        $filename = $this->getMetadata('filename');

        if (is_string($filename)) {
            return $filename;
        }

        $uri = $this->getMetadata('uri');

        return basename(is_string($uri) ? $uri : '');
    }

    /**
     * Get the mime type of the resource.
     */
    public function mime(): string
    {
        $mime = mime_content_type($this->stream);

        return $mime === false ? '' : $mime;
    }

    /**
     * Get the resource stream.
     *
     * @return resource
     */
    public function resource()
    {
        return $this->stream;
    }

    /**
     * Instantiate an instance of the stream.
     *
     * @param  mixed  $subject
     * @param  array{size?: int, metadata?: array<string, mixed>}  $options
     *
     * @throws NotReadableException
     */
    public static function make($subject, array $options = []): static
    {
        if ($subject instanceof StreamInterface) {
            return static::createFromStreamInterface($subject, $options);
        }

        if (is_resource($subject)) {
            return static::createFromResource($subject, $options);
        }

        if ($subject instanceof SplFileInfo) {
            return static::createFromSplFileInfo($subject, array_merge($options, [
                'metadata' => [
                    'filename' => $subject->getBasename(),
                ],
            ]));
        }

        if (is_string($subject) && file_exists($subject)) {
            return static::createFromPath($subject, $options);
        }

        if (is_string($subject)) {
            return static::createFromString($subject, $options);
        }

        throw new NotReadableException('Cannot create stream using invalid data.');
    }

    /**
     * Create a stream from a resource.
     *
     * @param  resource  $subject
     * @param  array{size?: int, metadata?: array<string, mixed>}  $options
     */
    protected static function createFromResource($subject, array $options = []): static
    {
        return new static($subject, $options);
    }

    /**
     * Create a stream from a file path.
     *
     * @param  array{size?: int, metadata?: array<string, mixed>}  $options
     *
     * @throws NotReadableException
     */
    protected static function createFromPath(string $data, array $options = []): static
    {
        return new static(static::open($data, 'r+'), $options);
    }

    /**
     * Create a stream from a SplFileInfo object.
     *
     * @param  array{size?: int, metadata?: array<string, mixed>}  $options
     *
     * @throws NotReadableException
     */
    protected static function createFromSplFileInfo(SplFileInfo $data, array $options = []): static
    {
        return new static(static::open($data->getPathname(), 'r+'), $options);
    }

    /**
     * Create from PSR stream interface.
     *
     * @param  array{size?: int, metadata?: array<string, mixed>}  $options
     *
     * @throws NotReadableException
     */
    protected static function createFromStreamInterface(StreamInterface $stream, array $options = []): static
    {
        $resource = static::open('php://memory', 'r+');

        $stream->rewind();

        while (! $stream->eof()) {
            fwrite($resource, $stream->read(1000000));
        }

        $stream->rewind();

        rewind($resource);

        return new static($resource, $options);
    }

    /**
     * Create a stream from a string.
     *
     * @param  array{size?: int, metadata?: array<string, mixed>}  $options
     *
     * @throws NotReadableException
     */
    protected static function createFromString(string $data, array $options = []): static
    {
        $resource = static::open('php://memory', 'r+');

        fwrite($resource, $data);
        rewind($resource);

        return new static($resource, $options);
    }

    /**
     * Open a file handle, ensuring a valid resource is returned.
     *
     * @return resource
     *
     * @throws NotReadableException
     */
    protected static function open(string $path, string $mode)
    {
        return fopen($path, $mode) ?: throw new NotReadableException("Unable to open stream for [{$path}].");
    }
}
