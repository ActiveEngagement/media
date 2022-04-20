<?php

namespace Actengage\Media;

use Actengage\Media\Casts\Collection;
use Actengage\Media\Casts\Colors;
use Actengage\Media\Casts\ExifData;
use Actengage\Media\Support\QueryScopes;
use Illuminate\Database\Eloquent\Model as BaseModel;
use Illuminate\Support\Facades\Storage;

class Media extends BaseModel
{
    use QueryScopes;

    /**
     * The database table name.
     *
     * @var string
     */
    protected $table = 'media';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'ready', 'favorite', 'disk', 'context', 'title', 'caption', 'directory',
        'filesize', 'filename', 'orig_filename', 'mime', 'extension','disk',
        'filters', 'conversions', 'meta', 'tags', 'order', 'exif'
    ];

    /**
     * The attributes that are hidden.
     *
     * @var array
     */
    protected $hidden = [
        'parent_id'
    ];

    /**
     * The attributes that are cast.
     *
     * @var array
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
     * @var array
     */
    protected $appends = [
        'relative_path',
        'url'
    ];

    /**
     * The default attributes.
     *
     * @var array
     */
    protected $attributes = [
        'filesize' => 0
    ];

    /**
     * Get the colors attribute.
     *
     * @param mixed $value
     * @return \Illuminate\Support\Collection
     */
    public function getColorsAttribute($value): \Illuminate\Support\Collection
    {
        return $this->getClassCastableAttributeValue(
            $key = 'colors', $this->meta->get($key)
        );
    }

    /**
     * Set the colors attribute.
     *
     * @param mixed $value
     * @return void
     */
    public function setColorsAttribute($value)
    {
        $caster = $this->resolveCasterClass($key = 'colors');
        
        $this->meta->put(
            $key, $caster->set($this, $key, $value, $this->attributes)
        );
    }

    /**
     * Get all of the owning mediable models.
     */
    public function mediable(string $class)
    {
        return $this->morphedByMany($class, 'mediable');
    }

    /**
     * Get the parent model.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function parent()
    {
        return $this->belongsTo(static::class, 'parent_id');
    }

    /**
     * Get the children models.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function children()
    {
        return $this->hasMany(static::class, 'parent_id');
    }

    /**
     * Get the filesize attribute.
     *
     * @return float
     */
    public function getSizeAttribute()
    {
        $units = array('B', 'KB', 'MB', 'GB', 'TB');

        $bytes = max($this->filesize, 0);
        
        $pow = min(floor(
            ($bytes ? log($bytes) : 0) / log(1024)
        ), count($units) - 1);

        $bytes /= (1 << (10 * $pow));

        return round($bytes, 2) . ' ' . $units[$pow];
    }

    /**
     * Get the file_exists attribute.
     *
     * @return bool
     */
    public function getFileExistsAttribute(): bool
    {
        return Storage::disk($this->disk)->exists($this->relative_path);
    }

    /**
     * Get the relative path for the associated file.
     *
     * @param $value
     */
    public function getRelativePathAttribute()
    {
        return ltrim(sprintf('%s/%s', $this->directory, $this->filename), '/');
    }

    /**
     * Get the path for the associated file.
     *
     * @param $value
     * @return string
     */
    public function getUrlAttribute(): string
    {
        return Storage::disk($this->disk)->url($this->relative_path);
    }

}
