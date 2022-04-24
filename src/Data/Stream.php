<?php

namespace Actengage\Media\Data;

use Actengage\Media\Exceptions\NotReadableException;
use GuzzleHttp\Psr7\Stream as BaseStream;
use Psr\Http\Message\StreamInterface;
use SplFileInfo;

class Stream extends BaseStream
{
    /**
     * @var resource
     */
    protected $stream;

    /**
     * Create an instance of a file stream.
     *
     * @param resource $subject
     * @param array $options
     */
    public function __construct($subject, array $options = [])
    {
        parent::__construct($this->stream = $subject, $options);
    }

    /**
     * Get the extension of the resource.
     *
     * @return string
     */
    public function extension(): string
    {
        return pathinfo($this->filename(), PATHINFO_EXTENSION);
    }

    /**
     * Get the name of the resource.
     *
     * @return string
     */
    public function filename(): string
    {
        return $this->getMetadata('filename') ?? basename($this->getMetadata('uri'));
    }

    /**
     * Get the mime type of the resource.
     *
     * @return string
     */
    public function mime(): string
    {
        return mime_content_type($this->stream);
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
     * @param mixed $subject
     * @param array $options
     * @throws NotReadableException
     * @return static
     */
    public static function make($subject, array $options = []): static
    {
        if($subject instanceof StreamInterface) {
            return static::createFromStreamInterface($subject, $options);
        }

        if(is_resource($subject)) {
            return static::createFromResource($subject, $options);
        }
        
        if($subject instanceof SplFileInfo) {
            return static::createFromSplFileInfo($subject, array_merge($options, [
                'metadata' => [
                    'filename' => $subject->getBasename()
                ]
            ]));
        }
        
        if(is_string($subject) && file_exists($subject)) {
            return static::createFromPath($subject, $options);
        }
        
        if(is_string($subject)) {
            return static::createFromString($subject, $options);
        }
        
        throw new NotReadableException('Cannot create stream using invalid data.');
    }

    /**
     * Create a stream from a resource.
     *
     * @param mixed $subject
     * @param array $options
     * @return static
     */
    protected static function createFromResource($subject, array $options = []): static
    {
        return new static($subject, $options);
    }
    /**
     * Create a stream from a file path.
     *
     * @param mixed $subject
     * @param array $options
     * @return static
     */
    protected static function createFromPath(string $data, array $options = []): static
    {
        return new static(fopen($data, 'r+'), $options);
    }

    /**
     * Create a stream from a SplFileInfo object.
     *
     * @param mixed $subject
     * @param array $options
     * @return static
     */
    protected static function createFromSplFileInfo(SplFileInfo $data, array $options = []): static
    {
        return new static(fopen($data->getPathname(), 'r+'), $options);
    }

    /**
     * Create from PSR stream interface.
     *
     * @param StreamInterface $stream
     * @return static
     */
    protected static function createFromStreamInterface(StreamInterface $stream, array $options = []): static
    {
        $resource = fopen('php://memory','r+');

        $stream->rewind();

        while(!$stream->eof()) {
            fwrite($resource, $stream->read(1000000));
        }

        $stream->rewind();

        rewind($resource);
        
        return new Stream($resource, $options);


        // $data = new Stream(fopen('php://memory','r+'), $options);
        
        // $stream->rewind();

        // while(!$stream->eof()) {
        //     $data->write($stream->read(1000000));
        // }

        // $stream->rewind();
        
        // $data->rewind();

        // return $data;
    }

    /**
     * Create a stream from a string.
     *
     * @param mixed $subject
     * @param array $options
     * @return static
     */
    protected static function createFromString(string $data, array $options = []): static
    {
        $resource = fopen('php://memory','r+');

        fwrite($resource, $data);
        rewind($resource);

        return new static($resource, $options);
    }
}