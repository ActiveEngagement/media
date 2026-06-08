<?php

use Actengage\Media\Facades\Resource;
use Actengage\Media\Media;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Tests\User;

beforeEach(function (): void {
    $this->text = Resource::make(__DIR__.'/src/file.txt')->save();
    $this->html = Resource::make(__DIR__.'/src/index.html')->save();
});

it('associates and detaches media on a mediable model', function (): void {
    $user = User::create([
        'name' => 'John Smith',
        'email' => 'john@example.com',
        'password' => Hash::make('password'),
    ]);

    $user->media()->sync([
        $this->text->id => ['favorite' => true],
        $this->html->id => ['favorite' => false],
    ]);

    expect($user->media)->toHaveCount(2);
    expect($user->media()->wherePivot('favorite', 1)->count())->toBe(1);
    expect($this->text)->toBeInstanceOf(Media::class);
    expect($user->medium->id)->toBe($this->text->id);

    $user->delete();

    expect(DB::table('mediables')->count())->toBe(0);
});

it('builds the morphed-by-many relationship for owning models', function (): void {
    expect($this->text->mediable(User::class))->toBeInstanceOf(MorphToMany::class);
});
