<?php

use Actengage\Media\Media;
use Actengage\Media\Plugins\ExtractImageColors;
use Actengage\Media\Resources\File;
use Actengage\Media\Resources\Image;

return [

    'model' => Media::class,

    'resources' => [
        'image' => Image::class,
        'file' => File::class
    ],
    
    'plugins' => [
        'image' => [
            [ExtractImageColors::class, [
                'colorCount' => 3,
                'quality' => 10
            ]]
        ]
    ]

];