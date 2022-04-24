<?php

namespace Actengage\Media\Resources;

use Actengage\Media\Data\Stream;
use Actengage\Media\Media;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Psr\Http\Message\StreamInterface;

class OriginalResource extends File
{
    /**
     * Create an instance of the File resource.
     *
     * @param mixed $data
     * @throws InvalidResourceException
     * @return void
     */
    public function __construct(StreamInterface $data)
    {
        parent::__construct($data);
    }
}