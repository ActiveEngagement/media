<?php

namespace Actengage\Media;

use Actengage\Media\Casts\Collection;
use Actengage\Media\Casts\Colors;
use Actengage\Media\Casts\ExifData;
use Actengage\Media\Support\QueryScopes;
use ColorThief\Color;
use Database\Factories\MediaFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model as BaseModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Support\Facades\Storage;

/**
 * @property int $id
 * @property int|null $parent_id
 * @property string|null $disk
 * @property string|null $directory
 * @property string|null $filename
 * @property int $filesize
 * @property string|null $mime
 * @property string|null $extension
 * @property string|null $context
 * @property string|null $title
 * @property string|null $caption
 * @property bool $ready
 * @property bool $favorite
 * @property int|null $order
 * @property \Illuminate\Support\Collection<array-key, mixed> $meta
 * @property \Illuminate\Support\Collection<array-key, mixed> $tags
 * @property \Illuminate\Support\Collection<array-key, Color> $colors
 * @property Support\ExifData $exif
 * @property array<array-key, mixed>|null $filters
 * @property array<array-key, mixed>|null $conversions
 * @property-read string $relative_path
 * @property-read string $url
 * @property-read string $size
 * @property-read bool $file_exists
 */
class Media extends BaseModel
{
    /** @use HasFactory<MediaFactory> */
    use HasFactory;

    use QueryScopes;

    /**
     * Create a new factory instance for the model.
     */
    protected static function newFactory(): MediaFactory
    {
        return MediaFactory::new();
    }

    /**
     * The database table name.
     *
     * @var string
     */
    protected $table = 'media';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'ready', 'favorite', 'disk', 'context', 'title', 'caption', 'directory',
        'filesize', 'filename', 'orig_filename', 'mime', 'extension', 'disk',
        'filters', 'conversions', 'meta', 'tags', 'order', 'exif',
    ];

    /**
     * The attributes that are hidden.
     *
     * @var list<string>
     */
    protected $hidden = [
        'parent_id',
    ];

    /**
     * The attributes that are cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'ready' => 'bool',
        'filters' => 'array',
        'conversions' => 'array',
        'colors' => Colors::class,
        'exif' => ExifData::class,
        'tags' => Collection::class,
        'meta' => Collection::class,
    ];

    /**
     * The attributes that are appended.
     *
     * @var list<string>
     */
    protected $appends = [
        'relative_path',
        'url',
    ];

    /**
     * The default attributes.
     *
     * @var array<string, mixed>
     */
    protected $attributes = [
        'filesize' => 0,
    ];

    /**
     * Get the colors attribute.
     *
     * @param  mixed  $value
     * @return \Illuminate\Support\Collection<array-key, Color>
     */
    public function getColorsAttribute($value): \Illuminate\Support\Collection
    {
        $key = 'colors';

        return (new Colors)->get($this, $key, $this->meta->get($key), $this->attributes);
    }

    /**
     * Set the colors attribute.
     *
     * @param  mixed  $value
     * @return void
     */
    public function setColorsAttribute($value)
    {
        $key = 'colors';

        $this->meta->put(
            $key, (new Colors)->set($this, $key, $value, $this->attributes)
        );
    }

    /**
     * Get all of the owning mediable models.
     *
     * @param  class-string<BaseModel>  $class
     * @return MorphToMany<BaseModel, $this>
     */
    public function mediable(string $class)
    {
        return $this->morphedByMany($class, 'mediable');
    }

    /**
     * Get the parent model.
     *
     * @return BelongsTo<Media, $this>
     */
    public function parent()
    {
        return $this->belongsTo(Media::class, 'parent_id');
    }

    /**
     * Get the children models.
     *
     * @return HasMany<Media, $this>
     */
    public function children()
    {
        return $this->hasMany(Media::class, 'parent_id');
    }

    /**
     * Get the human-readable size attribute.
     */
    public function getSizeAttribute(): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];

        $bytes = (float) max($this->filesize, 0);

        $pow = (int) min(floor(
            ($bytes !== 0.0 ? log($bytes) : 0) / log(1024)
        ), count($units) - 1);

        $bytes /= (1 << (10 * $pow));

        return round($bytes, 2).' '.$units[$pow];
    }

    /**
     * Get the file_exists attribute.
     */
    public function getFileExistsAttribute(): bool
    {
        return Storage::disk($this->disk)->exists($this->relative_path);
    }

    /**
     * Get the relative path for the associated file.
     */
    public function getRelativePathAttribute(): string
    {
        return ltrim(sprintf('%s/%s', $this->directory, $this->filename), '/');
    }

    /**
     * Get the path for the associated file.
     */
    public function getUrlAttribute(): string
    {
        return Storage::disk($this->disk)->url($this->relative_path);
    }
}
