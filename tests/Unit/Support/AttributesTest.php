<?php

use Actengage\Media\Exceptions\BadAttributeException;
use Actengage\Media\Facades\Resource;

it('gets an attribute fluently', function (): void {
    $resource = Resource::path(__DIR__.'/../../src/file.txt')->disk('public');

    expect($resource->attribute('disk'))->toBe('public');
});

it('sets an attribute fluently', function (): void {
    $resource = Resource::path(__DIR__.'/../../src/file.txt');

    expect($resource->setAttribute('caption', 'A caption')->caption)->toBe('A caption');
});

it('throws when getting an unknown attribute', function (): void {
    Resource::path(__DIR__.'/../../src/file.txt')->getAttribute('doesNotExist');
})->throws(BadAttributeException::class);

it('throws when setting an unknown attribute', function (): void {
    Resource::path(__DIR__.'/../../src/file.txt')->setAttribute('doesNotExist', 'value');
})->throws(BadAttributeException::class);
