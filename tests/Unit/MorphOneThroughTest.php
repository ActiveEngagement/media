<?php

use Actengage\Media\Media;
use Illuminate\Database\Eloquent\Collection;
use Tests\User;

it('returns the first related model from the relationship', function (): void {
    $media = Media::factory()->createOne();
    $user = User::factory()->createOne();

    $user->media()->attach($media->id);

    expect($user->medium()->first())->toBeInstanceOf(Media::class);
    expect($user->medium()->first()?->id)->toBe($media->id);
});

it('eager loads the first medium', function (): void {
    $media = Media::factory()->createOne();
    $user = User::factory()->createOne();

    $user->media()->attach($media->id);

    $users = User::with('medium')->get();

    expect($users->first()?->medium)->toBeInstanceOf(Media::class);
    expect($users->first()?->medium?->id)->toBe($media->id);
});

it('eager loads null when there is no related medium', function (): void {
    User::factory()->createOne();

    $users = User::with('medium')->get();

    expect($users->first()?->medium)->toBeNull();
});

it('returns null from findMany when no ids are given', function (): void {
    $user = User::factory()->createOne();

    expect($user->medium()->findMany([]))->toBeNull();
});

it('finds many related models by their ids', function (): void {
    $media = Media::factory()->createOne();
    $user = User::factory()->createOne();

    $user->media()->attach($media->id);

    $results = $user->medium()->findMany([$media->id]);

    expect($results)->toBeInstanceOf(Collection::class);

    $first = $results instanceof Collection ? $results->first() : null;

    expect($first?->id)->toBe($media->id);
});
