<?php

declare(strict_types=1);

namespace Actengage\Media\Plugins;

use Actengage\Media\Contracts\Resource;
use Actengage\Media\Resources\Image;
use Intervention\Image\Constraint;

/**
 * Enforce a maximum dimension for images.
 *
 * Available Options:
 *
 * - `width`: The maximum width of the image.
 * - `height`: The maximum height of the image.
 * - `aspectRatio`: Should resize and maintain the aspect ratio.
 * - `upsize`: Should upsize if the maximum width and height are smaller than
 *   the image.
 */
class EnforceMaximumImageDimensions extends Plugin
{
    /**
     * The resources that are ignored by the plugin.
     *
     * @var array<int, class-string<resource>>
     */
    protected static array $compatibleResources = [
        Image::class,
    ];

    /**
     * Fires after the resource has been initialized.
     *
     * @return void
     */
    public function initialized(Resource $resource)
    {
        if ($resource instanceof Image) {
            $width = $this->options->get('width');
            $height = $this->options->get('height');

            $resource->resize(
                is_numeric($width) ? (int) $width : null,
                is_numeric($height) ? (int) $height : null,
                function (Constraint $constraint) {
                    if ($this->options->get('aspectRatio', true)) {
                        $constraint->aspectRatio();
                    }

                    if ($this->options->get('upsize')) {
                        $constraint->upsize();
                    }
                }
            );
        }
    }
}
