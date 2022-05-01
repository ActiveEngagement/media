<?php

use Actengage\Media\Media;
use Actengage\Media\Plugins\EnforceMaximumImageDimensions;
use Actengage\Media\Plugins\ExtractImageColors;
use Actengage\Media\Plugins\ExtractImageMetaData;
use Actengage\Media\Plugins\HashDirectory;
use Actengage\Media\Plugins\HashFilename;
use Actengage\Media\Plugins\ModelDirectory;
use Actengage\Media\Plugins\PreserveOriginalResource;
use Actengage\Media\Resources\File;
use Actengage\Media\Resources\Image;

return [

    /*
    |--------------------------------------------------------------------------
    | Model Class
    |--------------------------------------------------------------------------
    |
    | Override the Media model class.
    |
    | The class should extend the Media model and override changes through
    | inheritance.
    |
    */

    'model' => Media::class,

    /*
    |--------------------------------------------------------------------------
    | Resource Classes
    |--------------------------------------------------------------------------
    |
    | The resources classes are used to process different types of content.
    | Resources are processed in the order they are defined.
    |
    */

    'resources' => [
        'image' => Image::class,
        'file' => File::class
    ],
    
    /*
    |--------------------------------------------------------------------------
    | Plugin Classes
    |--------------------------------------------------------------------------
    |
    | Plugins are defined using their class name, or as an array with the first
    | item in the array as the class name, and the second item is an array of
    | plugin options.
    |
    */

    'plugins' => [
        // [EnforceMaximumImageDimensions::class, [
        //     'width' => 1024,
        //     'height' => 768,
        //     'aspectRatio' => true,
        //     'upsize' => false
        // ]],

        // [ExtractImageColors::class, [
        //     'colorCount' => 3,
        //     'quality' => 10
        // ]],

        // ExtractImageMetaData::class,

        // [HashDirectory::class, [
        //     'length' => 8
        // ]],

        // [HashFilename::class, [
        //     'length' => 8
        // ]],

        // ModelDirectory::class,

        // PreserveOriginalResource::class,
    ]

];