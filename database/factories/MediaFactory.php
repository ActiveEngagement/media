<?php

namespace Database\Factories;

use Actengage\Media\Media;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Media>
 */
class MediaFactory extends Factory
{
    /**
     * @var class-string<Media>
     */
    protected $model = Media::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'disk' => 'public',
            'directory' => null,
            'context' => null,
            'title' => $this->faker->sentence(),
            'caption' => $this->faker->sentence(),
            'filename' => $this->faker->unique()->slug(2).'.txt',
            'filesize' => $this->faker->numberBetween(1, 100_000),
            'mime' => 'text/plain',
            'extension' => 'txt',
        ];
    }
}
