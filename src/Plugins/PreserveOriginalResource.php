<?php

namespace Actengage\Media\Plugins;

use Actengage\Media\Contracts\Resource;
use Actengage\Media\Media;
use Actengage\Media\Resources\OriginalResource;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Str;

class PreserveOriginalResource extends Plugin
{
    /**
     * The original resource stream.
     *
     * @var OriginalResource
     */
    protected OriginalResource $resource;

    /**
     * Fires after the resource has been initialized.
     *
     * @param Resource $resource
     * @return void
     */
    public function initialized(Resource $resource)
    {
        // Ignore original resource files to prevent recursion
        if($resource instanceof OriginalResource) {
            return;
        }

        $this->resource = new OriginalResource($resource->stream());
    }

    /**
     * Fires after the resource has been stored.
     *
     * @param Resource $resource
     * @return void
     */
    public function stored(Resource $resource, Media $model)
    {
        // Ignore original resource files to prevent recursion
        if($resource instanceof OriginalResource) {
            return;
        }
     
        $this->resource
            ->parent($model)
            ->context((string) $this->options->get('context', 'original'))
            ->disk((string) $this->options->get('disk', $model->disk))
            ->directory((string) $this->options->get('directory', $model->directory))
            ->filename($this->generateFilename($resource))
            ->save();
    }

    /**
     * Generate a dynamic filename.
     *
     * @param Resource $resource
     * @return string
     */
    protected function generateFilename(Resource $resource): string
    {
        $filename = $this->options->get('filename', '{{ $filename }}_{{ $hash }}.{{ $extension }}');

        return Blade::render($filename, array_merge(
            $resource->toArray(),
            pathinfo($resource->filename),
            [
                'hash' => substr(sha1(Str::random()), 0, 8)
            ]
        ));
    }
}