<?php

use Actengage\Media\Media;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Support\Facades\DB;
use Tests\User;

it('associates and detaches media on a mediable model', function (): void {
    $text = Media::factory()->createOne();
    $html = Media::factory()->createOne();
    $user = User::factory()->createOne();

    $user->media()->sync([
        $text->id => ['favorite' => true],
        $html->id => ['favorite' => false],
    ]);

    expect($user->media)->toHaveCount(2);
    expect($user->media()->wherePivot('favorite', 1)->count())->toBe(1);
    expect($user->medium?->id)->toBe($text->id);

    $user->delete();

    expect(DB::table('mediables')->count())->toBe(0);
});

it('builds the morphed-by-many relationship for owning models', function (): void {
    $media = Media::factory()->createOne();

    expect($media->mediable(User::class))->toBeInstanceOf(MorphToMany::class);
});
