<?php

use Actengage\Media\Facades\Resource;
use Actengage\Media\Media;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Hash;
use Tests\User;

beforeEach(function (): void {
    $this->media = Resource::make(__DIR__.'/../src/file.txt')->save();

    $this->user = User::create([
        'name' => 'John Smith',
        'email' => 'john@example.com',
        'password' => Hash::make('password'),
    ]);
});

it('returns the first related model from the relationship', function (): void {
    $this->user->media()->attach($this->media->id);

    expect($this->user->medium()->first())->toBeInstanceOf(Media::class);
    expect($this->user->medium()->first()->id)->toBe($this->media->id);
});

it('eager loads the first medium', function (): void {
    $this->user->media()->attach($this->media->id);

    $users = User::with('medium')->get();

    expect($users->first()->medium)->toBeInstanceOf(Media::class);
    expect($users->first()->medium->id)->toBe($this->media->id);
});

it('eager loads null when there is no related medium', function (): void {
    $users = User::with('medium')->get();

    expect($users->first()->medium)->toBeNull();
});

it('returns null from findMany when no ids are given', function (): void {
    expect($this->user->medium()->findMany([]))->toBeNull();
});

it('finds many related models by their ids', function (): void {
    $this->user->media()->attach($this->media->id);

    $results = $this->user->medium()->findMany([$this->media->id]);

    expect($results)->toBeInstanceOf(Collection::class);
    expect($results->first()->id)->toBe($this->media->id);
});
