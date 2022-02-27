<?php

namespace WalkerChiu\MorphImage\Models\Entities;

use WalkerChiu\Core\Models\Entities\Entity;
use WalkerChiu\Core\Models\Entities\LangTrait;

class Image extends Entity
{
    use LangTrait;



    /**
     * Create a new instance.
     *
     * @param Array  $attributes
     * @return void
     */
    public function __construct(array $attributes = [])
    {
        $this->table = config('wk-core.table.morph-image.images');

        $this->fillable = array_merge($this->fillable, [
            'host_type', 'host_id',
            'morph_type', 'morph_id',
            'filename',
            'serial',
            'identifier',
            'type', 'size',
            'data',
            'options',
            'is_visible'
        ]);

        $this->casts = array_merge($this->casts, [
            'options'    => 'json',
            'is_visible' => 'boolean'
        ]);

        parent::__construct($attributes);
    }

    /**
     * Get it's lang entity.
     *
     * @return Lang
     */
    public function lang()
    {
        if (
            config('wk-core.onoff.core-lang_core')
            || config('wk-morph-image.onoff.core-lang_core')
        ) {
            return config('wk-core.class.core.langCore');
        } else {
            return config('wk-core.class.morph-image.imageLang');
        }
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function langs()
    {
        if (
            config('wk-core.onoff.core-lang_core')
            || config('wk-morph-image.onoff.core-lang_core')
        ) {
            return $this->langsCore();
        } else {
            return $this->hasMany(config('wk-core.class.morph-image.imageLang'), 'morph_id', 'id');
        }
    }

    /**
     * @param $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOfVisible($query)
    {
        return $query->where('is_visible', 1);
    }

    /**
     * Get the owning host model.
     */
    public function host()
    {
        return $this->morphTo(null, 'host_type', 'host_id');
    }

    /**
     * Get the owning morph model.
     */
    public function morph()
    {
        return $this->morphTo(null, 'morph_type', 'morph_id');
    }

    /**
     * Get all of the comments for the image.
     *
     * @param Int  $user_id
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function comments($user_id = null)
    {
        return $this->morphMany(config('wk-core.class.morph-comment.comment'), 'morph')
                    ->when($user_id, function ($query, $user_id) {
                                return $query->where('user_id', $user_id);
                            });
    }
}
