<?php

namespace Actengage\Media\Support;

use Illuminate\Support\Collection;

trait QueryScopes {
    /**
     * Add a query scope for the `caption` attribute.
     *
     * @param Illuminate\Database\Eloquent\Builder $query
     * @param string|string[] ...$values
     * @return void
     */
    public function scopeCaption($query, ...$values)
    {
        $query->oneOrMore('caption', ...$values);
    }

    /**
     * Add a query scope for the `context` attribute.
     *
     * @param Illuminate\Database\Eloquent\Builder $query
     * @param string|string[] ...$values
     * @return void
     */
    public function scopeContext($query, ...$value)
    {
        $query->oneOrMore('context', ...$value);
    }

    /**
     * Add a query scope for the `disk` attribute.
     *
     * @param Illuminate\Database\Eloquent\Builder $query
     * @param string|string[] ...$values
     * @return void
     */
    public function scopeDisk($query, ...$values)
    {
        $query->oneOrMore('disk', ...$values);
    }

    /**
     * Add a query scope for the `extension` attribute.
     *
     * @param Illuminate\Database\Eloquent\Builder $query
     * @param string|string[] ...$values
     * @return void
     */
    public function scopeExtension($query, ...$values)
    {
        $query->oneOrMore('extension', ...$values);
    }

    /**
     * Add a query scope for the `filename` attribute.
     *
     * @param Illuminate\Database\Eloquent\Builder $query
     * @param string|string[] ...$values
     * @return void
     */
    public function scopeFilename($query, ...$values)
    {
        $query->oneOrMore('filename', ...$values);
    }

    /**
     * Add a query scope for the `filesize` attribute.
     *
     * @param Illuminate\Database\Eloquent\Builder $query
     * @param int|int[] ...$values
     * @return void
     */
    public function scopeFilesize($query, ...$values)
    {
        $query->oneOrMore('filesize', ...$values);
    }

    /**
     * Add a query scope for the `meta` attribute.
     *
     * @param Illuminate\Database\Eloquent\Builder $query
     * @param array $meta
     * @return void
     */
    public function scopeMeta($query, array $meta)
    {
        $query->whereRaw('JSON_CONTAINS(`meta`, '.json_encode($meta).')');
    }

    /**
     * Add a query scope for the `mime` attribute.
     *
     * @param Illuminate\Database\Eloquent\Builder $query
     * @param string|string[] ...$values
     * @return void
     */
    public function scopeMime($query, ...$values)
    {
        $query->oneOrMore('mime', ...$values);
    }

    /**
     * Make one or more values.
     *
     * @param Illuminate\Database\Eloquent\Builder $query
     * @param string $key
     * @param mixed|mixed[] ...$values
     * @return void
     */
    public function scopeOneOrMore($query, string $key, ...$values)
    {
        $values = (new Collection($values))->flatten();

        if($values->count() == 1) {
            $query->where($key, $values->first());
        }
        else {
            $query->whereIn($key, $values);
        }
    }

    /**
     * Add a query scope for the `tags` attribute.
     *
     * @param Illuminate\Database\Eloquent\Builder $query
     * @param mixed ...$tags
     * @return void
     */
    public function scopeTag($query, ...$tags)
    {
        $this->scopeTags($query, $tags);
    }

    /**
     * Add a query scope for the `tags` attribute.
     *
     * @param Illuminate\Database\Eloquent\Builder $query
     * @param array $tags
     * @return void
     */
    public function scopeTags($query, array $tags)
    {
        $query->where(function($q) use ($tags) {
            foreach($tags as $tag) {
                $q->orWhereJsonContains('tags', $tag);
            }
        });
    }

    /**
     * Add a query scope for the `title` attribute.
     *
     * @param Illuminate\Database\Eloquent\Builder $query
     * @param string|string[] ...$values
     * @return void
     */
    public function scopeTitle($query, ...$values)
    {
        $query->oneOrMore('title', ...$values);
    }

    /**
     * Add a query scope without the `tags` attribute.
     *
     * @param Illuminate\Database\Eloquent\Builder $query
     * @param mixed ...$tags
     * @return void
     */
    public function scopeWithoutTag($query, ...$tags)
    {
        $this->scopeWithoutTags($query, $tags);
    }

    /**
     * Add a query scope without the `tags` attribute.
     *
     * @param Illuminate\Database\Eloquent\Builder $query
     * @param array $tags
     * @return void
     */
    public function scopeWithoutTags($query, array $tags)
    {
        $query->where(function($q) use ($tags) {
            foreach($tags as $tag) {
                $q->orWhereJsonDoesntContain('tags', $tag);
            }
        });
    }
}
