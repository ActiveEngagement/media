# Media

This package provides a unit tested API to manage different types of files which can be related to Eloquent models (many to many relationships). Each type of file can be associated to its own resource for additional processing and manipulation. For instance, images are processed with the `Image` resource and manipulated with [Intervention Image](https://image.intervention.io/). API's are provides for creating your own resources.

### Features

- `Media` Eloquent Model and Migrations.
- `Mediable` trait to relate any eloquent model to the `Media` model.
- `Resource` API to add support for additional types of media.
- `Plugin` API which adds optional features, like color extraction for images.
- Uses [File Storage](https://laravel.com/docs/9.x/filesystem) to manage files.
- Uses [Intervention Image](https://image.intervention.io/) to manage and manipulate images.
- Unit tested
  
## Requirements

- Laravel 9.x+
- PHP 8.x+
- Intervention Image 2.x+
- GD or Imagick

## Getting Started

*Install via composer*
 
```
composer require actengage/media
```

*Publish the config file*

```
php artisan vendor:publish --tag=config
```

*Optional, publish the migration files*

```
php artisan vendor:publish --tag=config
```

## Resource Factory

The resource factory provides a unified interface for creating a variety of resources. You can even create your own resource classes and register them in `config/media.php`. By default, there are two types of resources: `Image` and `File`. For instance, you could define an `Audio` or `Video` resource. Resources are processed in the order they are defined.

```php
use Actengage\Media\Facades\Resource;
use Illuminate\Http\UploadedFile;

// Create a resource from a path.
$resource = Resource::path('some/file/path/image.jpeg');

// Create a resource from request() instance.
$resource = Resource::request('image');

// Create a resource from an \Illuminate\Http\UploadedFile instance.
$resource = Resource::make(
    new UploadedFile('some/file/path/image.jpeg', 'image.jpeg')
);
```

## Resource Methods

Every resource share a set of standard methods. And each type of resource may define its own method. Methods are chainable.

```php
use Actengage\Media\Facades\Resource;

// Chain methods on the Resource facade to build the resource.
// This will save the resource to the `public` disk in the `images`
// directory. This resource is assumed to be an image and will be
// filtered using `greyscale()`.
$resource = Resource::request('image')
    ->disk('public')
    ->directory('images')
    ->filename('renamed.jpeg')
    ->title('Hello World!')
    ->caption('This is my first resource.')
    ->greyscale();

// Save the resource to the disk and return the `Media` model.
$media = $resource->save();
```

## Conditional Resource Methods

Sometimes you may not know what kind of resource you are creating. You can use the `when()` method to conditionally chain methods to the resource. Use the key configuration in `config/media.php` for matching. 

```php
use Actengage\Media\Facades\Resource;

$resource = Resource::request('image')
    // When the resource is an image, make it greyscale.
    ->when('image', function($resource) {
        $resource->greyscale()
    })
    // When the resource is a file, do something else...
    ->when('file', function($resource) {
        // Do something here...
    });
```

## Resource Context, Meta, Tags

Contextual data can be used to search and filter `Media` models.

```php
use Actengage\Media\Facades\Resource;

$resource = Resource::request('image');

// Context allows you to give a simple string to assign from context to 
// resources. For example, if you want to notate a resource is an image.
$resource->context('image');

// Meta data is a key/value store that is saved as JSON in the database.
// Similar to context, but this allows you to associate custom meta data
// with a resource instance.
$resource->meta([
    'some_key' => 'Some value goes here.'
]);

// Meta can be also added using individual arguments.
$resource->meta('another_key', 'Another key goes here.');

// Tags add an array of keys as context to a resource.
$resource->tags(['a', 'b', 'c']);

// Tags can be added using individual arguments or an array.
$resource->tags('d', 'e', 'f');
```

## Events

Similar to Eloquent events, `Resource` event handlers work the same way. There are two ways to bind events, globally to a `Resource` class, or on the instance of a resource. The difference is global event binding is handled for all resources, whereas the instance methods are only fired for that instance.

### Global Methods

```php
use Actengage\Resources\Image;

Image::creating(function($resource, $model) {
    // This method is fired for the every Image resource before it
    // has been saved, similar to the `creating` Eloquent event.
});

Image::created(function($resource, $model) {
    // This method is fired for the every Image resource after it
    // has been saved, similar to the `creating` Eloquent event.
});
```

### Instance Methods
```php
$resource = Resource::request('image')
    ->creating(function($resource, $model) {
        // This method is fired for the resource instance before it
        // has been saved, similar to the `creating` Eloquent event.
    })
    ->created(function($resource, $model) {
        // This method is fired for the resource instance after it
        // has been saved, similar to the `created` Eloquent event.
    });
```

## Query Scopes

The `Media` model provides some convenient scopes for searching.

```php
use Actengage\Media\Media;

// Search by one or more captions
Media::caption('Some Caption');
Media::caption('Some Caption', 'Another Caption');
Media::caption(['Some Caption', 'Another Caption']);

// Search by one or more contexts
Media::context('Some Context');
Media::context('Some Context', 'Another Context');
Media::context(['Some Context', 'Another Context']);

// Search by one or more disks
Media::disk('public');
Media::disk('public', 's3');
Media::disk(['public', 's3']);

// Search by one or more extensions
Media::extension('jpeg');
Media::extension('jpeg', 'jpg');
Media::extension(['jpeg', 'jpg']);

// Search by one or more filenames
Media::filename('a.jpeg');
Media::filename('a.jpeg', 'b.jpeg');
Media::filename(['a.jpeg', 'b.jpeg']);

// Search by one or more filesizes
Media::filesize(2500);
Media::filesize(2500, 3500);
Media::filesize([2500, 3500]);

// Search by meta key/values
Media::meta([
    'a' => 1,
    'b' => 2,
    'c' => 3
]);

// Search by one or more mime types
Media::mime('text/plain');
Media::mime('text/plain', 'text/html');
Media::mime(['text/plain', 'text/html']);

// Search by one or more tags
Media::tag('a');
Media::tag('a', 'b');
Media::tag(['a', 'b', 'c']);

// Alias to tag() is tags()
Media::tags('a', 'b');

// Search by one or more titles
Media::title('Some Title');
Media::title('Some Title', 'Another Title');
Media::title(['Some Title', 'Another Title']);

// Search records without one or more tags
Media::withoutTag('a');
Media::withoutTag('a', 'b');
Media::withoutTag(['a', 'b', 'c']);

// Alias to withoutTag() is withoutTags()
Media::withoutTags('a', 'b');
```

## Mediable Trait

The `Mediable` trait is used to associate `Media` models to your custom models. `Media` models are related using `morphToMany` relationships.

*Document.php*

```php
<?php

namespace App;

use Actengage\Media\Mediable;
use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    use Mediable;
}
```
*Basic Usage*

```php
// Create a new `Media` model using the `request()` instance.
$media = Resource::request('image')
    ->disk('public')
    ->save();

// Create a new Document
$document = new Document();
$document->content = 'Hello World!';
$document->save();

// Sync the model to the document
$document->media()->sync($media);

// Many to Many usage
dd($document->media);

// One to Many usage. This method will give the last `Media` model
// associated with the document model. This method is for convenience.
dd($document->medium);
```

## Plugins

Plugins are used to add additional functionality to resources not provided by the core API's. This is an example of the `ExtractImageColors` plugin that extracts the common colors from an image and stores them in the model. A plugin consists of a `Plugin` class and is defined in the `media.config`

*config/media.php*

```php
<?php

use Actengage\Media\Media;
use Actengage\Media\Plugins\ExtractImageColors;
use Actengage\Media\Plugins\HashDirectory;
use Actengage\Media\Plugins\HashFilename;
use Actengage\Media\Resources\File;
use Actengage\Media\Resources\Image;

return [

    // Resources are defined in key/value pairs. The key is the common name
    // and the value is the class. Plugins are matched to their common name.
    'resources' => [
        'image' => Image::class,
        'file' => File::class
    ],
    
    'plugins' => [
        // This plugins will apply to all resources and has no options.
        HashFilename::class,

        // These plugins will only apply to image resources
        'image' => [
            // This plugin has some options
            [ExtractImageColors::class, [
                'colorCount' => 3,
                'quality' => 10
            ]],
        ],

        // These plugins will only apply to file resources
        'file' => [
            HashDirectory::class
        ]
    ]

];
```

*Plugins/ExtractImageColors.php*

```php
<?php

namespace Actengage\Media\Plugins;

use Actengage\Media\Media;
use Actengage\Media\Resources\Image;
use ColorThief\ColorThief;
use Illuminate\Support\Collection;

class ExtractImageColors extends Plugin {

    /**
     * Boot the plugin.
     *
     * @param Collection $options
     * @return void
     */
    public static function boot(Collection $options): void
    {
        /**
         * Get the color palette of the image.
         *
         * @param integer $colorCount
         * @param integer $quality
         * @param array|null $area
         * @param string $outputFormat
         * @param \ColorThief\Image\Adapter\AdapterInterface|string|null $adapter 
         * @return \Illuminate\Support\Collection
         */
        Image::macro('palette', function(
            int $colorCount = 10,
            int $quality = 10,
            ?array $area = null,
            string $outputFormat = 'obj',
            $adapter = null
        ): Collection {
            return collect(ColorThief::getPalette(
                $this->image->getCore(),
                $colorCount,
                $quality,
                $area,
                $outputFormat,
                $adapter
            ));
        });
    
        Image::creating(function(Image $resource, Media $model) use ($options) {
            $model->colors = $resource->palette(
                (int) $options->get('colorCount', 10),
                (int) $options->get('quality', 10)
            );
        });
    }
}
```