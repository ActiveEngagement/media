<?php

namespace Tests;

use Actengage\Media\Contracts\Resource;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TestEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
 
    /**
     * The order instance.
     *
     * @var \Actengage\Media\Contracts\Resource
     */
    public $resource;
 
    /**
     * Create a new event instance.
     *
     * @param  \Actengage\Media\Contracts\Resource  $resource
     * @return void
     */
    public function __construct(Resource $resource)
    {
        $this->resource = $resource;
    }
}