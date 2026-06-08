# Media

This package provides a unit tested API to manage different types of files which can be related to Eloquent models (many to many relationships). Each type of file can be associated to its own resource for additional processing and manipulation. For instance, images are processed with the `Image` resource and manipulated with [Intervention Image](https://image.intervention.io/). API's are provides for creating your own resources.

### Features

- `Media` Eloquent Model and Migrations.
- `Mediable` trait to relate any eloquent model to the `Media` model.
- `Resource` API to add support for additional types of media.
- `Plugin` API which adds optional features, like color extraction for images.
- Uses [File Storage](https://laravel.com/docs/filesystem) to manage files.
- Uses [Intervention Image](https://image.intervention.io/) to manage and manipulate images.
- Fully tested with [Pest](https://pestphp.com/) at 100% code coverage.

## Requirements

- PHP 8.3, 8.4, or 8.5
- Laravel 11, 12, or 13
- Intervention Image 2.x
- GD or Imagick PHP extension (plus `exif` and `fileinfo` for image metadata)

## Getting Started

*Install via Composer*

```bash
composer require actengage/media
```

The service provider, `Media` facade, and `Resource` facade are auto-discovered, and the package's migrations are loaded automatically — no extra setup is required to start storing media.

*Optionally publish the config file*

```bash
php artisan vendor:publish --tag=media-config
```

*Optionally publish the migrations* (only needed if you want to customize the schema; they are loaded automatically otherwise)

```bash
php artisan vendor:publish --tag=media-migrations
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

Sometimes you may not know what kind of resource you are creating. You can use the `is()` method to conditionally chain methods to a specific resource types. Use the key configuration in `config/media.php` for matching or fully qualified class name. 

```php
use Actengage\Media\Facades\Resource;
use Actengage\Media\Resources\Image;

$resource = Resource::request('image')
    // When the resource is an image, make it greyscale.
    ->is('image', function($resource) {
        $resource->greyscale();
    })
    // You may also use the literal class as a match...
    ->is(Image::class, function($resource) {
        $resource->greyscale();
    })
    // When the resource is a file, do something else...
    ->is('file', function($resource) {
        // Do something here...
    });
```

You may also need to check for `true` and `false` values before executing code on a resource. For these scenarios, you may use `when()` and `not()` methods to conditionally chain methods to the resource.

```php
use Actengage\Media\Facades\Resource;
use Actengage\Media\Resources\Image;

$resource = Resource::request('image')
    // When the value is `true` execute the callback.
    ->when(true, function($resource) {
        // This will only be called when `true` is passed to the first argument.
    })
    // You may also use a callback to check for a `true` value.
    ->when(function($resource) {
        return true;
    }, function($resource) {
        // This will only be called when `true` is returned from the first callback.
    })
    // When the value is `false` execute the callback.
    ->not(false, function($resource) {
        // This will only be called when `false` is passed to the first argument.
    })
    // You may also use a callback to check for a `false` value.
    ->not(function($resource) {
        return false;
    }, function($resource) {
        // This will only be called when `false` is returned from the first callback.
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

The observable events fired during a resource's lifecycle are, in order: `initialized`, `saving`, `saved`, `storing`, and `stored`.

### Global Methods

```php
use Actengage\Media\Resources\Image;

Image::saving(function($resource, $model) {
    // This method is fired for every Image resource before it has been
    // persisted, similar to the `saving` Eloquent event.
});

Image::stored(function($resource, $model) {
    // This method is fired for every Image resource after its file has
    // been written to disk.
});
```

### Instance Methods

```php
$resource = Resource::request('image')
    ->saving(function($resource, $model) {
        // This method is fired for the resource instance before it
        // has been persisted.
    })
    ->stored(function($resource, $model) {
        // This method is fired for the resource instance after its
        // file has been written to disk.
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

Plugins are used to add additional functionality to resources not provided by the core API's. This is an example of the `ExtractImageColors` plugin that extracts the common colors from an image and stores them in the model. A plugin has instance methods which apply to the specific resource that is being manipulated. Or as event handlers.

A `Plugin` class can be used in one of two ways. The first way is to bind it statically to the resource it should be used with. The second way is by defining it in the `config/media.php` file. In the config, plugins can be bound to specific resources or globally for all resources.

*Static Binding*

```php
use Actengage\Media\Resources\Image;
use Actengage\Media\Plugins\ExtractImageColors;
use Actengage\Media\Plugins\HashFilename;

// These plugins will only fire on Image resources.
Image::register([
    // This is a plugin without any options defined.
    HashFilename::class,

    // This is a plugin with options defined.
    [ExtractImageColors::class, [
        'colorCount' => 3,
        'quality' => 10
    ]],
]);
```

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

use Actengage\Media\Contracts\Resource;
use Actengage\Media\Media;
use Actengage\Media\Resources\Image;
use ColorThief\ColorThief;
use Illuminate\Support\Collection;

class ExtractImageColors extends Plugin
{
    /**
     * The resources that are compatible with the plugin.
     */
    protected static array $compatibleResources = [
        Image::class,
    ];

    /**
     * Runs after the `saving` event fires.
     */
    public function saving(Resource $resource, Media $model): void
    {
        if ($resource instanceof Image) {
            $model->colors = $resource->palette(
                (int) $this->options->get('colorCount', 10),
                (int) $this->options->get('quality', 10)
            );
        }
    }

    /**
     * Boot the plugin and register the `palette` macro on the Image resource.
     */
    public static function boot(Collection $options): void
    {
        Image::macro('palette', function (
            int $colorCount = 10,
            int $quality = 10,
            ?array $area = null,
            string $outputFormat = 'obj',
            $adapter = null
        ): Collection {
            return new Collection(ColorThief::getPalette(
                $this->image->getCore(),
                $colorCount,
                $quality,
                $area,
                $outputFormat,
                $adapter
            ));
        });
    }
}
```

## Development

This package uses [Pest](https://pestphp.com/) for testing, [Larastan](https://github.com/larastan/larastan)/[PHPStan](https://phpstan.org/) for static analysis, [Rector](https://getrector.com/) for automated refactoring, and [Pint](https://laravel.com/docs/pint) for code style. Continuous integration runs all four against a PHP 8.3, 8.4, and 8.5 matrix.

```bash
# Run the test suite with 100% coverage enforcement
composer test
vendor/bin/pest --coverage --min=100

# Static analysis (max level)
vendor/bin/phpstan analyse

# Check (and apply) automated refactors
vendor/bin/rector --dry-run
vendor/bin/rector

# Check (and fix) code style
vendor/bin/pint --test
vendor/bin/pint
```

### Releases

Versioning is managed with [changesets](https://github.com/changesets/changesets). When you make a change that should be released, add a changeset describing it:

```bash
pnpm changeset
```

Merging the resulting "Version Packages" pull request tags the release automatically.