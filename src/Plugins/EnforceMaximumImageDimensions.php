<?php

namespace Actengage\Media\Plugins;

use Actengage\Media\Contracts\Resource;
use Actengage\Media\Resources\Image;
use Illuminate\Support\Str;

/**
 * Enforce a maximum dimension for images.
 * 
 * Available Options:
 * 
 * @var int $width The maximum width of the image.
 * @var int $height The maximum height of the image.
 * @var boolean $aspectRatio Should resize and maintain the aspect ratio.
 * @var boolean $upsize Should upsize if the maximum width and height are
 *                      smaller than the image.
 */
class EnforceMaximumImageDimensions extends Plugin
{
    /**
     * The resources that are ignored by the plugin.
     *
     * @var array
     */
    protected static array $compatibleResources = [
        Image::class
    ];

    /**
     * Fires after the resource has been initialized.
     *
     * @param Resource $resource
     * @return void
     */
    public function initialized(Resource $resource)
    {
        $resource->resize(
            $this->options->get('width'),
            $this->options->get('height'),
            function ($constraint) {
                if($this->options->get('aspectRatio', true)) {
                    $constraint->aspectRatio();
                }

                if($this->options->get('upsize')) {
                    $constraint->upsize();
                }
            }
        );
    }
}